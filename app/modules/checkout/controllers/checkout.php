<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class checkout extends MX_Controller {
	public $tb_users;
	public $tb_order;
	public $tb_categories;
	public $tb_services;
	public $tb_payments;
	public $tb_api_providers;
	public $tb_social_network;
	public $module_name;
	public $module_icon;
	public $ip_address;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		//Config Module
		$this->tb_users       	   = USERS;
		$this->tb_categories       = CATEGORIES;
		$this->tb_services         = SERVICES;
		$this->tb_order            = ORDER;
		$this->tb_api_providers    = API_PROVIDERS;
		$this->tb_social_network   = SOCIAL_NETWORK_CATEGORIES;
		$this->tb_transaction_logs = TRANSACTION_LOGS;
		$this->tb_payments         = PAYMENTS_METHOD;
		$this->module_name         = 'Services';
		$this->module_icon         = "fa ft-users";
		$this->ip_address          = get_client_ip();

	}

	public function index(){
		$id 				= post('item_id');
		$item 				= $this->model->get_service_detail_by($id);
		$payment_methods    = $this->model->fetch('id, name, type, sort', $this->tb_payments, ['status' => 1, 'type !=' => 'free'], 'sort', 'ASC');
		if (empty($item)) {
			redirect(cn());
		}
		$data = array(
			"module"     			=> get_class($this),
			"item"		 			=> $item,
			"payment_methods"		=> $payment_methods,
		);
		
		$this->template->set_layout('user');
		$this->template->build("index", $data);
	}

	public function process(){
		$link   = post("link");
		$email  = post("email");
		$agree  = post("agree");
		$ids    = post('item_ids');

		$payment_id = (int)post('payment_id');

		if ($link  == "" || $email == "") {
			_validation('error', lang('please_fill_in_the_required_fields') );
		}

		$link = strip_tags($link);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			_validation('error', lang('invalid_email_format') );
	    }

		if (!$agree) {
			_validation('error', lang('please_agree_to_our_terms_of_services_before_placing_an_order') );
		}

		$item = $this->model->get('id, ids, price, is_free', $this->tb_services, ['ids' => $ids, 'status' => 1]);
		if (empty($item)) {
			_validation('error', lang('services_does_not_exists') );
		}

		$data_order = array(
			"module"             => get_class($this),
			'link'               => $link,
			'email'              => $email,
			'item_ids'           => $item->ids,
			'price'              => $item->price,
		);

		//Save order for free service
		if($item->is_free){

			$limit_free_order = get_option("default_limit_trial_package_per_client", 3);
			$count_free_order_by_email = $this->model->get_count_free_order(['type' => 'email', 'email' => $email]);
			
			if( $count_free_order_by_email >= $limit_free_order){
				_validation('error', lang("you_got_free_trial_limitations_please_open_a_support_ticket_for_more_details"));
			}
			
			$count_free_order_by_ip = $this->model->get_count_free_order(['type' => 'ip_address', 'ip_address' => $this->ip_address ]);
			if( $count_free_order_by_ip >= $limit_free_order ){
				_validation('error', lang("you_got_free_trial_limitations_please_open_a_support_ticket_for_more_details"));
			}

			$data_order['payment_method'] = 'free';
			$data_new_order = (object)array(
                'payment_type'          => 'free',
                'amount'                => (double)$item->price,
                'txt_id'                => 'tnx_free_'.strtotime(NOW),
                'order_details'         => $data_order,
                'transaction_fee'       => "",
                'order_status'          => 1,
                'transaction_status'    => 1,
                'order_note'            => '',
                'send_notice_email'     => true,
            );

			$save_order = $this->save_order($data_new_order);
			if(isset($save_order->status) && $save_order->status == 'success') {
				$redirect_url = cn("checkout/success/".$save_order->transaction_id);
				echo '<div class="checkout-left-content">
						<div class="dimmer active">  
							<div class="loader"></div>
							<div class="dimmer-content">
								<script type="text/javascript">
								window.location.assign("'. $redirect_url .'");
								</script>
								</div>
							</div>
					</div>';
				exit();
			}else{
				_validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
			}
		}

		/*----------  Check payment method  ----------*/
		$payment = $this->model->get('id, type, name, params', $this->tb_payments, ['id' => $payment_id]);
		if (!$payment) {
			_validation('error', lang('There_was_an_error_processing_your_request_Please_try_again_later'));
		}
		$data_order['payment_method'] = $payment->type;

		require_once $payment->type.'.php';
		$payment_module = new $payment->type($payment);
		$payment_module->create_payment($data_order);
	}

	public function success($id = ""){
		$transaction = $this->model->get("id, ids, uid,amount, order_id, transaction_id, transaction_fee, status", $this->tb_transaction_logs, ['id' => $id]);
		if (!empty($transaction)) {
			$order_detail = $this->model->get_order_by_id($transaction->order_id);
			if (!empty($order_detail)) {
				$order_detail = $order_detail->id.' - '.$order_detail->quantity.' '.$order_detail->service_name;
			}else{
				$order_detail = 'Empty';
			}
			$data = array(
				"module"        => get_class($this),
				"transaction"   => $transaction,
				"order_detail"  => $order_detail,
			);
			unset_session("transaction_log_id");
			$this->template->set_layout('user');
			$this->template->build("payment_successfully", $data);
		}else{
			redirect(cn());
		}
	}

	public function unsuccess(){
		$data = array(
			"module"        => get_class($this),
		);
		$this->template->set_layout('user');
		$this->template->build("payment_unsuccessfully", $data);
	}

	public function save_order($data_order = ""){
		if (!is_object($data_order) && $data_order->amount < 0) {
			$response = [
				'status' 			=> 'error',
				'transaction_id' 	=> '',
				'user_id' 	        => '',
			];

			return (object)$response;
		}
		$order_detail       = $data_order->order_details;
		$amount             = $data_order->amount;
		$txt_id             = $data_order->txt_id;
		$transaction_fee    = $data_order->transaction_fee;
		$payment_type       = $data_order->payment_type;
		$order_status       = (isset($data_order->order_status) && $data_order->order_status == 0) ? 'awaiting' : 'pending';
		$order_note         = (isset($data_order->order_note)) ? $data_order->order_note : '';
		$transaction_status = (isset($data_order->transaction_status) && $data_order->transaction_status == 0) ? 0 : 1;

		//update customer informations
		$user = $this->model->get('id, total_orders, total_spent', $this->tb_users, ['email' => $order_detail['email']]);
		if (empty($user)) {
			$data_user = array(
				"ids" 	        	  => ids(),
				'email'               => $order_detail['email'],
				'total_orders'        => 1,
				'total_spent'         => $amount,
				'history_ip'          => $this->ip_address,
				'changed'             => NOW,
				'created'             => NOW,
			);
			$this->db->insert($this->tb_users, $data_user);
			$user_id = $this->db->insert_id();
		}else{
			$data_user = array(
				'total_orders'   => $user->total_orders + 1,
				'total_spent'    => $user->total_spent + $amount,
				'history_ip'     => $this->ip_address,
				'changed'        => NOW,
			);
			$this->db->update($this->tb_users, $data_user,  ['id' => $user->id]);
			$user_id = $user->id;
		}

		// insert new order
		$service = $this->model->get('*', $this->tb_services, ['ids' => $order_detail['item_ids'], 'status' => 1]);
		$data_insert_order = array(
			"ids" 	        	=> ids(),
			"uid" 	        	=> $user_id,
			"cate_id" 	    	=> $service->cate_id,
			"service_id" 		=> $service->id,
			"service_type" 		=> $service->type,
			"link" 	        	=> $order_detail['link'],
			"quantity" 	    	=> $service->quantity,
			"charge" 	    	=> $service->price,
			"api_provider_id"  	=> $service->api_provider_id,
			"api_service_id"  	=> $service->api_service_id,
			"api_order_id"  	=> -1,
			"note"  	        => $order_note,
			"status"			=> $order_status,
			"ip_address"	    => $this->ip_address,
			"changed" 	    	=> NOW,
			"created" 	    	=> NOW,
		);

		/*----------  Get Formal Charge and profit  ----------*/
		$data_insert_order['formal_charge'] = ($service->original_price * $service->quantity) / 1000;
		$data_insert_order['profit']        = $service->price - $data_insert_order['formal_charge'];


		$this->db->insert($this->tb_order, $data_insert_order);
		$order_id = $this->db->insert_id();

		/*----------  Insert to Transaction table  ----------*/
		$data_transaction = array(
			"ids" 				=> ids(),
			"uid" 				=> $user_id,
			"order_id" 		    => $order_id,
			"type" 				=> $payment_type,
			"transaction_id" 	=> $txt_id,
			"transaction_fee" 	=> $transaction_fee,
			"amount" 	        => $amount,
			"status" 	        => $transaction_status,
			"created" 			=> NOW,
		);

		$this->db->insert($this->tb_transaction_logs, $data_transaction);
		$transaction_log_id = $this->db->insert_id();

		// send email to user and admin enable_new_order_notification_send_to_customer
		if (isset($data_order->send_notice_email) && $data_order->send_notice_email ) {
			$data_send_email = array(
				'user_id'             => $user_id,
				'customer_email'      => $order_detail['email'],
				'order_id'            => $order_id,
				'amount'              => $amount,
				'package_name'        => $service->quantity .' '. $service->name,
				'manage_orders_link'  => cn('client'),

			);
			$this->send_notice_email($data_send_email);
		}

		$response = [
			'status' 			=> 'success',
			'transaction_id' 	=> $transaction_log_id,
			'user_id' 	        => $user_id,
		];
		return (object)$response;
	}

	// Send Email
	public function send_notice_email($data_send_email = array('')){
		if (get_option("enable_new_order_notification_send_to_customer")) {
			$subject = get_option('new_order_notification_send_to_customer_subject');
			$message = get_option('new_order_notification_send_to_customer_content');
			$merge_fields = array(
				"{{customer_email}}"      => $data_send_email['customer_email'],
	            "{{order_id}}"            => $data_send_email['order_id'],
	            "{{amount}}"              => $data_send_email['amount'],
	            "{{package_name}}"        => $data_send_email['package_name'],
	            "{{manage_orders_link}}"  => $data_send_email['manage_orders_link'],
			);
			$template = [ 'subject' => $subject, 'message' => $message, 'merge_fields' => $merge_fields];
			$check_send_email_issue = $this->model->send_mail_template($template, $data_send_email['user_id']);
		}		

		if (get_option("enable_new_order_notification_send_to_admin")) {
			$subject = get_option('new_order_notification_send_to_admin_subject');
			$message = get_option('new_order_notification_send_to_admin_content');
			$template = [ 'subject' => $subject, 'message' => $message, 'merge_fields' => $merge_fields];
			$admin_id = $this->model->get("id", $this->tb_users, ['role' => 'admin'])->id;
			if ($admin_id) {
				$check_send_email_issue = $this->model->send_mail_template($template, $admin_id);
			}
		}
	}

}