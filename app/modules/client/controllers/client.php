<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class client extends MX_Controller {
	public $tb_users;
	public $tb_order;
	public $tb_categories;
	public $tb_services;
	public $module_name;
	public $module_icon;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');

		//Config Module
		$this->tb_users               = USERS;
		$this->tb_order               = ORDER;
		$this->tb_categories          = CATEGORIES;
		$this->tb_services            = SERVICES;
		$this->module_name            = 'Order';
		$this->module_icon            = "fa ft-users";
	}


	/**
	 *
	 * Form get client id
	 *
	 */
	public function index(){
		$k = get('query');
		$k = $email = htmlspecialchars(trim($k));
		$error = false;
		if ($email == "") {
			$error 		= true;
			$error_ms 	= lang('please_enter_a_valid_email_address');
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error 		= true;
			$error_ms 	= lang('please_enter_a_valid_email_address');
	    }
		
		if($error){
			$data = array(
				"module"   	=> get_class($this),
				"error"   	=> $error,
				"error_ms"  => $error_ms,
			);
			$this->template->set_layout('user');
			$this->template->build("index", $data);
		}else{
			$page        = (int)get("p");
			$page        = ($page > 0) ? ($page - 1) : 0;
			$limit_per_page = get_option("default_limit_per_page", 10);
			$query = array('query' => $k);
			$query_string = "";
			if(!empty($query)){
				$query_string = "?".http_build_query($query);
			}
			$config = array(
				'base_url'           => cn(get_class($this).'/orders/'.$query_string),
				'total_rows'         => $this->model->get_count_orders_per_client($email),
				'per_page'           => $limit_per_page,
				'use_page_numbers'   => true,
				'prev_link'          => '<i class="fe fe-chevron-left"></i>',
				'first_link'         => '<i class="fe fe-chevrons-left"></i>',
				'next_link'          => '<i class="fe fe-chevron-right"></i>',
				'last_link'          => '<i class="fe fe-chevrons-right"></i>',
			);
			$this->pagination->initialize($config);
			$links = $this->pagination->create_links();
			$orders = $this->model->get_client_orders_by($email, $limit_per_page, $page * $limit_per_page);
			$data = array(
				"module"     => get_class($this),
				"orders"     => $orders,
				"pagination" => $links,
			);
			$this->template->set_layout('user');
			$this->template->build('orders_log', $data);
		}

	}

	public function terms(){
		$data = array();
		$this->template->set_layout('user');
		$this->template->build("terms/index", $data);
	}

	public function faq(){
		$this->load->model('faqs/faqs_model', 'faqs_model');
		$faqs = $this->faqs_model->get_faqs();
		$data = array(
			"module"     => get_class($this),
			"faqs"       => $faqs,
		);
		$this->template->set_layout('user');
		$this->template->build("faq/index", $data);
	}
	
}