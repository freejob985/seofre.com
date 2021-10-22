<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class checkout_model extends MY_Model {
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;
	public $tb_order;
	public $tb_social_network;

	public function __construct(){
		$this->tb_users          = USERS;
		$this->tb_categories     = CATEGORIES;
		$this->tb_services       = SERVICES;
		$this->tb_api_providers  = API_PROVIDERS;
		$this->tb_order          = ORDER;
		$this->tb_social_network = SOCIAL_NETWORK_CATEGORIES;
		parent::__construct();
	}

	public function get_order_by_id($id){
		$this->db->select("o.id, o.quantity, o.link, s.name as service_name");
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_services." s", "o.service_id = s.id", 'left');
		$this->db->where('o.id', $id);
		$query  = $this->db->get();
		$result = $query->row();
		return $result;
	}

	public function get_service_detail_by($id){
		$this->db->select('s.*, cate.required_field, cate.features');
		$this->db->from($this->tb_services." s");
		$this->db->join($this->tb_categories." cate", "s.cate_id = cate.id", 'left');
		$this->db->where("s.id", $id);
		$this->db->where("s.status", 1);
		$query = $this->db->get();
		$result = $query->row();
		return $result;
	}

	/*----------  Get Order details  ----------*/
	public function get_order_detail($order_id){
		$this->db->select('o.id, o.quantity, u.email as user_email, s.name as service_name');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->where('o.id', $order_id);
		$query = $this->db->get();
		if (!empty($query->row())) {
			$result = $query->row();
		}else{
			$result = "";
		}
		return $result;
	}

	// get the free package per each user
	function get_count_free_order($params = []){
		$where_like = "";
		switch ($params['type']) {
			case 'email':
				$where_like = "`u`.`email` LIKE '%".$params['email']."%' ESCAPE '!'";
				break;
			case 'ip_address':
				$where_like = "`o`.`ip_address` LIKE '%".$params['ip_address']."%' ESCAPE '!'";
				break;
		}
		$this->db->select('o.id');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->where('`o`.`charge` <=', 0);
		if ($where_like) $this->db->where($where_like);
		$this->db->where_in('`o`.`status`', ['pending', 'inprogress','processing']);
		$query = $this->db->get();
		$number_row = $query->num_rows();
		return $number_row;
	}
		
}
