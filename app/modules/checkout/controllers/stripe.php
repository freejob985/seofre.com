<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class stripe extends MX_Controller {
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
		$this->public_key          		= get_value($option, 'public_key');
		$this->secret_key       		= get_value($option, 'secret_key');
		$this->pm_details       		= get_value($option, 'pm_details');

		$this->load->library("stripeapi");
		$this->payment_lib = new stripeapi( $this->secret_key, $this->public_key );
	}

	public function index(){
		redirect(cn("checkout"));
	}

	public function create_payment($order_details = ""){
		_is_ajax('checkout');
		if ($order_details) {
			$order_details['stripe_public_key'] = $this->public_key;
			$this->load->view($this->payment_type.'/index', $order_details);
		}else{
			redirect(cn());
		}
	}

	/*----------  complete the payment  ----------*/
	public function create_payment_step2(){
		$order_details = post('order');
		if ($order_details['item_ids'] == "" || $order_details['email'] == "" || $order_details['link'] == ""|| $order_details['price'] == "") {
			redirect(cn());
		}
		$item_ids      = $order_details['item_ids'];
		$email         = $order_details['email'];
		$item 		   = $this->model->get('id, price, name, quantity', $this->tb_services, ['ids' => $item_ids, 'status' => 1]);

		if (!$item) _validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));

		$itemName = $this->pm_details .' ('.$item->quantity.' - '.$email.')';
		$amount = (double)$item->price;
		$token  = post("stripeToken");
		if(!$token){
			_validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
		}
		// Card info
		$card_num       = post('card_num');
		$card_cvv       = post('cvv');
		$card_exp_month = post('exp_month');
		$card_exp_year  = post('exp_year');
		
		// Buyer info
		$data_buyer_info = array(
			"source" 	  => $token,
			"email" 	  => $email ,
		);

		//add customer to stripe
		$customer = $this->payment_lib->customer_create($data_buyer_info);
		// Item info
		$itemNumber = $item->name;
		$orderID    = 'ODRS'.strtotime(NOW);//charge a credit or a debit card.

		if (strtolower($this->currency_code) == 'jpy') {
			$charge = $amount;
		}else{
			$charge = $amount * 100;
		}

		$data_charge = array(
			'customer'     => $customer->id,
	        'amount'       => $charge,
	        'currency'     => strtolower($this->currency_code),
	        'description'  => $itemName,
	        'metadata'     => array(
	            'order_id' => $orderID
	        )
		);
		//charge a credit or a debit card
	    $result = $this->payment_lib->create_payment($data_charge);
		if (!empty($result) && $result->status == 'success') {
			/*----------  Insert to Transaction table  ----------*/
			$response = $result->response;
			$data_order = (object)array(
				'payment_type'          => $this->payment_type,
				'amount'                => $amount,
				'txt_id'                => $response->id,
				'order_details'         => $order_details,
				'transaction_fee'       => "",
				'send_notice_email'     => true,
			);

			//Save order
			$save_order = $this->checkout->save_order($data_order);
			if(isset($save_order->status) && $save_order->status == 'success' && $save_order->transaction_id){
				redirect(cn("checkout/success/".$save_order->transaction_id));
			}else{
				redirect(cn("checkout/unsuccess"));
			}
		}else{
			if (!empty($result) && $result->status == 'error') {
				_validation('error', $result->message );
			}else{
				redirect(cn("checkout/unsuccess"));
			}
		}
	
		
	}
}