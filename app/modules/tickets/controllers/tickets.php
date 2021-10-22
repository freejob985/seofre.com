<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class tickets extends MX_Controller {
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_orders;
	public $tb_tickets;
	public $module;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		//Config Module
		$this->tb_users      = USERS;
		$this->tb_categories = CATEGORIES;
		$this->tb_services   = SERVICES;
		$this->tb_orders     = ORDER;
		$this->tb_tickets    = TICKETS;
		$this->module        = get_class($this);

		$this->columns = [
			"From"            => 'From',
			"Subsject"        => 'Subsject',
			"message"         => 'Message',
			"id"       		  => 'Last IP Address',
			"status"       	  => 'Status',
			"created"         => 'Created',
			"action"       	  => 'Action',
		];

	}

	public function index(){
		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		$query = array();
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}
		$config = array(
			'base_url'           => cn(get_class($this).$query_string),
			'total_rows'         => $this->model->get_tickets(true),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();
		$tickets = $this->model->get_tickets(false, "all", $limit_per_page, $page * $limit_per_page);
		$data = array(
			"module"     => get_class($this),
			"tickets"    => $tickets,
			"pagination" => $links,
			"columns"    => $this->columns,
		);
		$this->template->build('index', $data);
		
	}

	public function view($ids = ""){
		$ticket    = $this->model->get("*", $this->tb_tickets, ['ids' => $ids]);
		$data = array(
			"module"   		=> get_class($this),
			"ticket" 	    => $ticket,
		);
		$this->load->view('view', $data);
	}

	public function reply($ids = ""){
		$ticket    = $this->model->get("*", $this->tb_tickets, ['ids' => $ids]);
		$data = array(
			"module"   		=> get_class($this),
			"ticket" 	    => $ticket,
		);
		$this->load->view('reply', $data);
	}

	public function ajax_send_email(){
		$user_email       = post("email_to");
		$subject          = post("subject");
		$email_content    = $this->input->post("email_content", false);

		if($subject == ''){
			ms(array(
				'status'  => 'error',
				'message' => lang("subject_is_required"),
			));
		}

		if($email_content == ''){
			ms(array(
				'status'  => 'error',
				'message' => lang("message_is_required"),
			));
		}

		$subject = "[{{website_name}}" ."] ".$subject;
		$template = [ 'subject' => $subject, 'message' => $email_content, 'type' => 'default'];

		$check_email_issue = $this->model->send_mail_template($template, $user_email );
		if ($check_email_issue) {
			ms(array(
				"status"  => "error",
				"message" => $check_email_issue,
			));
		}

		ms(array(
			"status"  => "success",
			"message" => lang("your_email_has_been_successfully_sent_to_user"),
		));
		
	}

	//Search
	public function search(){
		$k           = get('query');
		$k           = htmlspecialchars(trim($k));
		$search_type = (int)get('search_type');
		$data_search = ['k' => $k, 'type' => $search_type];
		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		$query = ['query' => $k, 'search_type' => $search_type];
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}
		$config = array(
			'base_url'           => cn(get_class($this)."/search".$query_string),
			'total_rows'         => $this->model->get_count_tickets_by_search($data_search),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();

		$tickets = $this->model->search_logs_by_get_method($data_search, $limit_per_page, $page * $limit_per_page);
		$data = array(
			"module"     => get_class($this),
			"tickets"    => $tickets,
			"pagination" => $links,
			"columns"    => $this->columns,
		);

		$this->template->build('index', $data);
	}

	public function ajax_delete_item($ids = ""){
		$this->model->delete($this->tb_tickets, $ids, false);
	}

	/*----------  Actions Option  ----------*/
	public function ajax_actions_option(){
		$type = post("type");
		$idss = post("ids");
		if ($type == '') {
			ms(array(
				"status"  => "error",
				"message" => lang('There_was_an_error_processing_your_request_Please_try_again_later')
			));
		}

		if (!$idss && !in_array($type, ['clear_all'])) {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_at_least_one_item")
			));
		}
		switch ($type) {
			case 'clear_all':
				$this->db->empty_table($this->tb_tickets);
				break;

			case 'closed':
				$data['status'] = 'closed';
				$this->db->where_in('ids', $idss);
                $this->db->update($this->tb_tickets, $data);
				break;
		}

		ms(array(
			"status"  => "success",
			"message" => lang("Updated_successfully")
		));

	}

}

