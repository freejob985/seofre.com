<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class paypal extends MX_Controller {
	public $mode;
	public $checkout;
	public $payment_lib;
	public $tb_users;
	public $tb_transaction_logs;
	public $paypal;
	public $payment_type;
	public $currency_code;
	public $tb_services;
	public $tb_order;
	public $client_id;
	public $secret_key;
	public $pm_details;

	public function __construct($payment = ""){
		parent::__construct();
		$this->load->model('checkout_model', 'model');
		$this->tb_users            = USERS;
		$this->tb_transaction_logs = TRANSACTION_LOGS;
		$this->tb_services         = SERVICES;
		$this->tb_payments         = PAYMENTS_METHOD;
		$this->tb_order            = ORDER;
		$this->payment_type		   = get_class($this);
		require_once 'checkout.php';
		$this->checkout = new checkout();

		$this->currency_code       = get_option("currency_code", "USD");
		if ($this->currency_code == "") {
			$this->currency_code = 'USD';
		}

		if (!$payment) {
			$payment = $this->model->get('id, type, name, params', $this->tb_payments, ['type' => $this->payment_type]);
		}

		$this->payment_id = $payment->id;
		$params  = $payment->params;
		$option                			= get_value($params, 'option');
		$this->mode            			= get_value($option, 'environment');
		// options
		$this->client_id          		= get_value($option, 'client_id');
		$this->secret_key       		= get_value($option, 'secret_key');
		$this->pm_details       		= get_value($option, 'pm_details');
		$this->load->library("paypalapi");
		$this->payment_lib = new paypalapi( $this->client_id, $this->secret_key );
	}

	public function index(){
		redirect(cn("checkout"));
	}


	/*----------  Create payment  ----------*/
	public function create_payment($order_details = ""){
		_is_ajax('checkout');
		$item_ids      = $order_details['item_ids'];
		$email         = $order_details['email'];
		$item = $this->model->get('id, price, name, quantity', $this->tb_services, ['ids' => $item_ids, 'status' => 1]);

		if (!$this->client_id || !$this->secret_key) {
			_validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
		}
		
		if (!$item) {
			_validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
		}
		
		$description = $this->pm_details .' ('.$item->quantity.' - '.$email.')';
		$amount = (double)$item->price;
		$data = (object)array(
			"amount"       => $amount,
			"currency"     => $this->currency_code,
			"description"  => $description,
			"redirectUrls" => cn("checkout/paypal/complete"),
			"cancelUrl"    => cn("checkout/unsuccess"),
		);
		$response = $this->payment_lib->create_payment($data, $this->mode);
		if (isset($response->status) && $response->status == 'success') {
            // Insert order
			$data_order = (object)array(
                'payment_type'          => $this->payment_type,
                'amount'                => $amount,
                'txt_id'                => $response->data->id,
                'order_details'         => $order_details,
                'transaction_fee'       => "",
                'order_status'          => 0,
                'transaction_status'    => 0,
                'order_note'            => lang("waiting_for_buyer_funds"),
                'send_notice_email'     => false,
            );
            
            //Save order
			$save_order = $this->checkout->save_order($data_order);
			if(isset($save_order->status) && $save_order->status == 'success'){
				$this->load->view('redirect', ['redirect_url' => $response->approvalUrl]);
			}else{
				_validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
			}
		}else{
			_validation('error', $response->message );
		}

	}

	/*----------  Check payments ----------*/
	public function complete(){
		if ( !isset($_GET["paymentId"]) ) {
			redirect(cn("checkout/unsuccess"));
		}
		$result = $this->payment_lib->execute_payment($_GET["paymentId"], $_GET["PayerID"], $this->mode);
		// get Transaction Id
		$transactions        = $result->getTransactions();
		$related_resources   = $transactions[0]->getRelatedResources();
		$sale                = $related_resources[0]->getSale();
		$get_transaction_fee = $sale->getTransactionFee();
		$sale_id             = $sale->getId();
		$txt_status          = $sale->getState();//completed
		$payer_info          = $result->getPayer();//Get Payer Infor


		$txt_id        = $_GET["paymentId"];
		$transaction   = $this->model->get('*', $this->tb_transaction_logs, ['transaction_id' => $txt_id, 'type' => $this->payment_type]);
		if (empty($transaction)) {
			redirect(cn("checkout/unsuccess"));
		}

		if ($result->state == 'approved') {
			/*----------  Insert to Transaction table  ----------*/
			$transaction_fee  = $get_transaction_fee->getValue();
			$amount           = $result->transactions[0]->amount;
			$data_tnx_log = array(
				"transaction_id" 	=> $sale_id,
				"amount" 	        => $amount->total,
				'transaction_fee'   => $transaction_fee,
				"status" 			=> ($txt_status == 'completed') ? 1: 0,
			);
			$this->db->update($this->tb_transaction_logs, $data_tnx_log, ['id' => $transaction->id]);

			// Send email
			if ( $txt_status == 'completed' ) {
				// Update order id
				$this->db->update($this->tb_order, ['status' => 'pending', 'note' => ''],  ['id' => $transaction->order_id]);
				// send email
				$order_detail = $this->model->get_order_detail($transaction->order_id);
				if (!empty($order_detail)) {
		            $data_send_email = array(
						'user_id'             => $transaction->uid,
						'customer_email'      => $order_detail->user_email,
						'order_id'            => $transaction->order_id,
						'amount'              => $transaction->amount,
						'package_name'        => $order_detail->quantity .' '. $order_detail->service_name,
						'manage_orders_link'  => cn('client'),
					);
		            $this->checkout->send_notice_email($data_send_email);
				}
            	
	            redirect(cn("checkout/success/".$transaction->id));
			}
        } else {
            redirect(cn("checkout/unsuccess"));
        }
	}

}