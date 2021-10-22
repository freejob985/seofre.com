<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class order_model extends MY_Model {
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

	function get_categories_list(){
		$data  = array();
		$this->db->select("*");
		$this->db->from($this->tb_categories);
		$this->db->where("status", "1");
		$this->db->order_by("sort", 'ASC');
		$query = $this->db->get();

		$categories = $query->result();
		if(!empty($categories)){
			return $categories;
		}
		return false;
	}

	function get_services_list_by_cate($id = ""){
		$data  = array();
		if (!get_role("admin")) {
			$this->db->where("status", "1");
		}
		$this->db->select("*");
		$this->db->from($this->tb_services);
		$this->db->where("cate_id", $id);
		$this->db->order_by("price", "ASC");
		$query = $this->db->get();
		$services = $query->result();
		if(!empty($services)){
			return $services;
		}
		return false;
	}

	function get_service_item($id = ""){
		$data  = array();

		$this->db->select("*");
		$this->db->from($this->tb_services);
		$this->db->where("id", $id);
		$this->db->where("status", "1");
		$query = $this->db->get();

		$service = $query->row();
		if(!empty($service)){
			return $service;
		}
		return false;
	}

	function get_services_by_cate($id = ""){
		$data  = array();
		$this->db->select("*");
		$this->db->from($this->tb_services);
		$this->db->where("cate_id", $id);
		$this->db->where("status", "1");
		$query = $this->db->get();

		$services = $query->result();
		if(!empty($services)){
			return $services;
		}

		return false;
	}

	function get_order_logs_list($total_rows = false, $status = "", $limit = "", $start = ""){
		$data  = array();
		if (get_role("user")) {
			$this->db->where("o.uid", session("uid"));
		}
		if ($limit != "" && $start >= 0) {
			$this->db->limit($limit, $start);
		}
		$this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
		$this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
		if($status != "all" && !empty($status)){
			$this->db->where("o.status", $status);
		}
		$this->db->order_by("o.id", 'DESC');

		$query = $this->db->get();
		if ($total_rows) {
			$result = $query->num_rows();
			return $result;
		}else{
			$result = $query->result();
			return $result;
		}
		return false;
	}

	// Get Count of orders by status
	function get_count_orders($status = ""){
		$this->db->select("id");
		$this->db->from($this->tb_order);
		if($status != "all" && !empty($status)){
			$this->db->where("status", $status);
		}
		$query = $this->db->get();
		return $query->num_rows();
	}

	// Get Count of orders by Search query
	public function get_count_orders_by_search($search = []){
		$k = htmlspecialchars($search['k']);
		$where_like = "";
		switch ($search['type']) {
			case 1:
				#order id
				
				$where_like = "`o`.`id` IN (".$k.")";
				break;
			case 2:
				# API order id
				$where_like = "`o`.`api_order_id` IN (".$k.")";
				break;

			case 3:
				# Link
				$where_like = "`o`.`link` LIKE '%".$k."%' ESCAPE '!'";
				break;

			case 4:
				# User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}
		$this->db->select('o.id, u.email as user_email');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');

		if ($where_like) $this->db->where($where_like);
		$query = $this->db->get();
		$number_row = $query->num_rows();
		
		return $number_row;
	}
	// Search Logs by keywork and search type
	public function search_logs_by_get_method($search, $limit = "", $start = ""){
		$k = htmlspecialchars($search['k']);
		$where_like = "";
		
		switch ($search['type']) {
			case 1:
				#order id
				$where_like = "`o`.`id` IN (".$k.")";
				break;
			case 2:
				# API order id
				$where_like = "`o`.`api_order_id` IN (".$k.")";
				break;

			case 3:
				# Link
				$where_like = "`o`.`link` LIKE '%".$k."%' ESCAPE '!'";
				break;

			case 4:
				# User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select('o.*, u.email as user_email, s.name as service_name, api.name as api_name');
		$this->db->from($this->tb_order." o");
		$this->db->join($this->tb_users." u", "u.id = o.uid", 'left');
		$this->db->join($this->tb_services." s", "s.id = o.service_id", 'left');
		$this->db->join($this->tb_api_providers." api", "api.id = o.api_provider_id", 'left');
		if ($where_like) $this->db->where($where_like);
		$this->db->order_by("o.id", 'DESC');
		$this->db->limit($limit, $start);

		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

}

