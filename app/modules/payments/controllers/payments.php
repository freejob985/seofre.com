<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class payments extends MX_Controller {
	public $tb_payments;
	public $columns;
	public $module;
	public $module_name;
	public $module_icon;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		$this->tb_payments       = PAYMENTS_METHOD;
		$this->module            = get_class($this);
		$this->columns = array(
			"method"           => lang("method"),
			"name"             => lang("Name"),
			"status"           => lang("Status"),
			"sort"             => 'Sort',
		);
	}

	public function index(){
		$payments = $this->model->get_payment_lists();
		$data = array(
			"module"       => get_class($this),
			"columns"      => $this->columns,
			"payments"    => $payments,
		);
		$this->template->build('index', $data);
	}

	public function update($id = ""){
		$payment    = $this->model->get("*", $this->tb_payments, ['id' => $id]);
		$data = array(
			"module"   		=> get_class($this),
			"payment" 	    => $payment,
		);
		$this->load->view('integrations/'.$payment->type, $data);
	}

	public function ajax_update($id = ""){
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		$payment        = $this->model->get("*", $this->tb_payments, ['id' => $id]);
		$payment_params = post('payment_params');
		$sort 			= (int)$payment_params['sort'];
		if (!$payment) {
			_validation('error', "There was an error processing your request. Please try again later");
		}

		if ($payment->type != $payment_params['type']) {
			_validation('error', "There was an error processing your request. Please try again later");
		}
		
		if(!$sort || $sort <= 0){
			_validation('error', lang("sort_number_must_to_be_greater_than_zero"));
		}

		$data_payment = array(
			"name"         	  => $payment_params['name'],
			"sort"         	  => $sort,
			"status"          => (int)$payment_params['status'],
			"params"          => json_encode($payment_params),
		);
		$this->db->update($this->tb_payments, $data_payment, ['id' => $id]);
		_validation('success', lang("Update_successfully"));
	}
	
	public function ajax_toggle_item_status($id = ""){
		if (!get_role('admin')) _validation('error', "Permission Denied!");
		_is_ajax($this->module);
		$status  = post('status');
		$item  = $this->model->get("id", $this->tb_payments, ['id' => $id]);
		if ($item ) {
			$this->db->update($this->tb_payments, ['status' => (int)$status], ['id' => $id]);
			_validation('success', lang("Update_successfully"));
		}
	}

	public function ajax_delete_item($ids = ""){
		$this->model->delete($this->tb_payments, $ids, true);
	}

}