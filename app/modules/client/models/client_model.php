<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class client_model extends MY_Model {
	public $tb_users;
	public $tb_order;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;

	public function __construct(){
		$this->tb_categories        = CATEGORIES;
		$this->tb_order             = ORDER;
		$this->tb_users             = USERS;
		$this->tb_services          = SERVICES;
		$this->tb_api_providers   	= API_PROVIDERS;
		parent::__construct();
	}

	function get_client_orders_by($email, $limit = '', $start = ''){
		if ($email) {
			$this->db->select('u.email, o.id, o.quantity, o.created, o.status, o.charge, s.name as service_name');
			$this->db->from($this->tb_order." o");
			$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
			$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
			$this->db->where('u.email', $email);
			$this->db->where_in('o.status', ['completed','processing','inprogress','pending']);
			$this->db->order_by("o.id", 'DESC');
			$this->db->limit($limit, $start);
			$query = $this->db->get();
			$result = $query->result();
			if($result){
				return $result;
			} else {
				return false;
			}

		}else{
			return false;
		}
	}

	function get_count_orders_per_client($email){
		$this->db->select('u.email, o.quantity, o.created, o.status');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->where('u.email', $email);
		$this->db->where_in('o.status', ['completed','processing','inprogress','pending']);
		$query = $this->db->get();
		$number_row = $query->num_rows();
		return $number_row;
	}

}

