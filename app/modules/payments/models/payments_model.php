<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class payments_model extends MY_Model {
	public $tb_payments;
	
	public function __construct(){
		$this->tb_payments       = PAYMENTS_METHOD;
		parent::__construct();
	}

	public function get_payment_lists(){
		$this->db->select("*");
		$this->db->from($this->tb_payments);
		$this->db->where('type !=', 'free');
		$this->db->order_by("sort", 'ASC');
		$this->db->order_by("id", 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
}
