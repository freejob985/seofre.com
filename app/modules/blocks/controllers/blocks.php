<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class blocks extends MX_Controller {
	

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');

		// call all services: 
		$this->load->model('services/services_model','services_md');
		// call all category: 
		$this->load->model('category/category_model','category_model');

		//Config Module
		$this->tb_tickets    		= TICKETS;
		$this->tb_users    		    = USERS;
		$this->tb_ticket_message    = TICKET_MESSAGES;

	}

	public function set_language(){
		set_language(post("id"));

		ms(array("status" => "success"));
	}

	public function header(){
		$data = array();
		$this->load->view('header', $data);
	}

	public function sidebar(){
		$data = array();
		$this->load->view('sidebar', $data);
	}	
	
	public function header_vertical(){
		$data = array(
        	'lang_current' => get_lang_code_defaut(),
        	'languages'    => $this->model->fetch('*', LANGUAGE_LIST,'status = 1'),
        );
		$this->load->view('header_vertical', $data);
	}	

	public function footer(){
		$data = array(
        	'lang_current' => get_lang_code_defaut(),
        	'languages'    => $this->model->fetch('*', LANGUAGE_LIST,'status = 1'),
        );
		$this->load->view('footer', $data);
	}	

	public function back_to_admin(){
		$user = $this->model->get("id, ids", $this->tb_users, ['id' => session('uid')]);
		if (empty($user)) {
			ms(array(
				'status'  => 'error',
				'message' => lang("There_was_an_error_processing_your_request_Please_try_again_later"),
			));
		}
		unset_session("uid_tmp");
		unset_session("user_current_info");
		if (!session('uid_tmp')) {
			ms(array(
				'status'  => 'success',
				'message' => lang("processing_"),
			));
		}
	}

	public function user_header(){
		$categories = $this->category_model->get_category_lists(true);
		$data = array(
			'all_items' => $categories
		);
		$this->load->view("user/header", $data);
	}

	public function user_header_top($link = ''){

		$first_link = (object)array(
			'link'  => cn(),
			'name'  => 'Home'
		);
		
		$data = array(
			'first_link'  => $first_link,
			'second_link' => $link,
		);
		$this->load->view("user/header_top", $data);
	}

	public function empty_data(){
		$data = array();
		$this->load->view('empty_data', $data);
	}

	public function search_box(){

		if (in_array(segment(1), ['services'])) {
			$requests = [
				'method' => 'POST',
				'action' => cn(segment(1)."/ajax_search"),
				'class'  => 'ajaxSearchItemsKeyUp',
			];
		}else{
			$requests = [
				'method' => 'GET',
				'action' => cn(segment(1)."/search"),
				'class'  => '',
			];
		}

		$data_search = '';
		/*----------  Order and Dripfeed  ----------*/
		if (segment(1) == 'order' || (segment(1) == 'order' && segment(2) == 'search') || segment(1) == 'dripfeed') {
			$data_search = [
				1 => 'Order ID',
				2 => 'API Order ID',
				3 => 'Order Link',
				4 => 'User Email',
			];
		}
		
		/*----------  Transactions ----------*/
		if (segment(1) == 'transactions') {
			$data_search = [
				2 => 'Transaction ID',
				1 => 'User Email',
			];
		}
		/*----------  Tickets ----------*/
		if (segment(1) == 'tickets') {
			$data_search = [
				3 => 'Subject',
				2 => 'Client Email',
				1 => 'Last IP Address',
			];
		}
		$this->load->view('search_box', ['data_search' => $data_search, 'requests' => $requests]);
	}
}