<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class services_model extends MY_Model {
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;
	public $tb_social_network;

	public function __construct(){
		$this->tb_categories     = CATEGORIES;
		$this->tb_services       = SERVICES;
		$this->tb_api_providers  = API_PROVIDERS;
		$this->tb_social_network = SOCIAL_NETWORK_CATEGORIES;
		parent::__construct();
	}


	function get_services_list($service_data = false){
		$this->db->select('s.*, api.name as api_name, c.name as category_name, c.id as main_cate_id');
		$this->db->from($this->tb_services." s");
		$this->db->join($this->tb_categories." c", "c.id = s.cate_id", 'left');
		$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
		$this->db->order_by("c.sort", 'ASC');
		$this->db->order_by("s.price", 'ASC');
		$this->db->order_by("s.name", 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		$category = array();
		if ($result) {
			foreach ($query->result_array() as $row) {
               $category[$row['category_name']][] = (object)$row;
         	}
		}
		return $category;
	}

	
	function get_services_by_search($k){
		$k = trim(htmlspecialchars($k));

		if (get_role("user")) {
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');

			if ($k != "" && strlen($k) >= 2) {
				$this->db->where("(`s`.`id` LIKE '%".$k."%' ESCAPE '!' OR `s`.`api_service_id` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!')");
			}

			$this->db->where("s.status", 1);
			$this->db->order_by("s.id", 'DESC');
			$query = $this->db->get();
			$result = $query->result();

		}else{
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');

			if ($k != "" && strlen($k) >= 2) {
				$this->db->where("(`s`.`id` LIKE '%".$k."%' ESCAPE '!' OR `s`.`api_service_id` LIKE '%".$k."%' ESCAPE '!' OR  `s`.`name` LIKE '%".$k."%' ESCAPE '!' OR  `api`.`name` LIKE '%".$k."%' ESCAPE '!')");
			}
			
			$this->db->order_by("s.id", 'ASC');
			$query = $this->db->get();
			$result = $query->result();
		}
		return $result;
	}

	function get_categories_list_by_social_network(){
		$data  = array();
		$this->db->select("id, ids, name");
		$this->db->from($this->tb_social_network);
		$this->db->order_by("sort", 'cate_id');
		$query = $this->db->get();
		$social_networks = $query->result();
		if(!empty($social_networks)){
			$i = 0;
			foreach ($social_networks as $key => $row) {
				$i++;
				if ($i > 0) {
					$categories = $this->model->fetch("id, ids, sncate_id, name", $this->tb_categories, ['sncate_id' => $row->id], 'id', 'ASC');
					if(!empty($categories)){
						$social_networks[$key]->categories = $categories;
					}else{
						unset($social_networks[$key]);	
					}
				}else{
					break;
				}
			}
		}
		return array_values($social_networks);
	}

	function get_services_by_cate_id($id){
		if (get_role("user")) {
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');

			$this->db->where("s.cate_id", $id);
			$this->db->where("s.status", 1);
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			$result = $query->result();

		}else{
			$this->db->select('s.*, api.name as api_name');
			$this->db->from($this->tb_services." s");
			$this->db->join($this->tb_api_providers." api", "s.api_provider_id = api.id", 'left');
			
			$this->db->where("s.cate_id", $id);
			$this->db->order_by("s.price", 'ASC');
			$query = $this->db->get();
			$result = $query->result();
		}
		return $result;
	}

}
