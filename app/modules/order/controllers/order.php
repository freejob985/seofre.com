<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class order extends MX_Controller {
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

		$this->columns = array(
			"order_id"                  => lang("order_id"),
			"order_basic_details"       => lang("order_basic_details"),
			"created"                   => lang("Created"),
			"status"                    => lang("Status"),
		);
		
		if (get_role("admin") || get_role("supporter")) {
			$this->columns = array(
				"order_id"                  => lang("order_id"),
				"api_order_id"              => lang("api_orderid"),
				"Customer"                  => 'Customer',
				"order_basic_details"       => lang("order_basic_details"),
				"created"                   => lang("Created"),
				"status"                    => lang("Status"),
				"response"                  => lang("API_Response"),
				"action"                    => lang("Action"),
			);
		}
	}

	// LOGS
	public function index($order_status = ""){
		if ($order_status == "") {
			$order_status = "all";
		}
		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		$query = array();
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}
		$config = array(
			'base_url'           => cn(get_class($this)."/".$order_status.$query_string),
			'total_rows'         => $this->model->get_order_logs_list(true, $order_status),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();

		$order_logs = $this->model->get_order_logs_list(false, $order_status, $limit_per_page, $page * $limit_per_page);
		$data = array(
			"module"                => get_class($this),
			"columns"               => $this->columns,
			"order_logs"            => $order_logs,
			"order_status"          => $order_status,
			"pagination"            => $links,
			"number_error_orders"   => $this->model->get_count_orders('error'),
		);
		$this->template->build('logs/logs', $data);
	}

	public function log_update($ids = ""){
		$order    = $this->model->get("*", $this->tb_order, "ids = '{$ids}'");
		$data = array(
			"module"   		=> get_class($this),
			"order" 	    => $order,
		);
		$this->load->view('logs/update', $data);
	}

	public function ajax_logs_update($ids = ""){
		$link 			= post("link");
		$start_counter  = post("start_counter");
		$remains 		= post("remains");
		$status 		= post("status");

		if($link == ""){
			ms(array(
				"status"  => "error",
				"message" => lang("link_is_required")
			));
		}


		if(!is_numeric($start_counter) && $start_counter != ""){
			ms(array(
				"status"  => "error",
				"message" => lang("start_counter_is_a_number_format")
			));
		}

		if(!is_numeric($remains) && $remains != ""){
			ms(array(
				"status"  => "error",
				"message" => lang("start_counter_is_a_number_format")
			));
		}

		$data = array(
			"link" 	    	=> $link,
			"status"    	=> $status,
			"start_counter" => $start_counter,
			"remains"    	=> $remains,
			"changed" 		=> NOW,
		);

		$check_item = $this->model->get("ids, charge, uid, quantity, status", $this->tb_order, "ids = '{$ids}'");
		if(!empty($check_item)){
			$this->db->update($this->tb_order, $data, array("ids" => $check_item->ids));
			
			ms(array(
				"status"  => "success",
				"message" => lang("Update_successfully")
			));
		}else{
			ms(array(
				"status"  => "error",
				"message" => lang("There_was_an_error_processing_your_request_Please_try_again_later")
			));
		}
	}
	
	// function Search Data
	public function search(){
		$k           = get('query');
		$k           = trim($k);
		$k           = strip_tags($k);
		$k           = htmlspecialchars($k);
		if(!$k){
			redirect(cn('order'));
		}
		$search_type = (int)get('search_type');
		$data_search = ['k' => $k, 'type' => $search_type];
		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		if(in_array($k, ['1', '2'])){
			$k = orders_id_filter($k);
		}
		$query = ['query' => $k, 'search_type' => $search_type];
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}
		$config = array(
			'base_url'           => cn(get_class($this)."/search".$query_string),
			'total_rows'         => $this->model->get_count_orders_by_search($data_search),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();

		$order_logs = $this->model->search_logs_by_get_method($data_search, $limit_per_page, $page * $limit_per_page);

		$data = array(
			"module"       			=> get_class($this),
			"columns"      			=> $this->columns,
			"order_logs"   			=> $order_logs,
			"order_status"			=> '',
			"pagination"            => $links,
			"number_error_orders"   => $this->model->get_count_orders('error'),
		);
		$this->template->build('logs/logs', $data);
		
	}

	/*----------  Change Order status  ----------*/
	public function change_status($status = "", $ids = "" , $is_redirect = true){
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		if (!is_string($ids)) {
			redirect(cn('order/log'));
		}
		$data = [];
		$check_item = $this->model->get("ids, service_id, quantity, charge", $this->tb_order, ['ids' => $ids]);
		
		if ($check_item ) {
			switch ($status) {
				case 'resend_order':
					$related_service = $this->model->get('id, cate_id, api_provider_id, api_service_id, original_price', $this->tb_services, ['id' => $check_item->service_id]);
					if (!empty($related_service)) {
						$data['cate_id']              = $related_service->cate_id;
						$data['service_id']           = $related_service->id;
						$data['api_provider_id']      = $related_service->api_provider_id;
						$data['api_service_id']       = $related_service->api_service_id;
						$data['formal_charge']        = ($check_item->quantity * $related_service->original_price)/1000;
						$data['profit']               = $check_item->charge - $data['formal_charge'];
					}

					$data['status']         = 'pending';
					$data['note']           = 'Resent';
					$data['changed']        = NOW;
					$data['api_order_id']   = -1;
					$this->db->update($this->tb_order, $data, array("ids" => $check_item->ids));
					
					break;
			}
			
		}
		// Get redirect URL
		if ($is_redirect) {
			$redirect_url = get('r_url');
			if (!$redirect_url) {
				$redirect_url = cn('order/log');
			}
			redirect($redirect_url);
		}else{
			return false;
		}
	}

	public function ajax_order_by($status = ""){
		if (!empty($status) && $status !="" ) {
			$order_logs = $this->model->get_order_logs_list(false, $status);
			$data = array(
				"module"     => get_class($this),
				"columns"    => $this->columns,
				"order_logs" => $order_logs,
			);
			$this->load->view("logs/ajax_search", $data);
		}
	}

	public function ajax_log_delete_item($ids = ""){
		$this->model->delete($this->tb_order, $ids, false);
	}
}