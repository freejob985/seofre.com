<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class category_model extends MY_Model {
	public $tb_users;
	public $tb_categories;
	public $tb_social_network;
	public $tb_services;

	public function __construct(){
		$this->tb_categories     = CATEGORIES;
		$this->tb_social_network = SOCIAL_NETWORK_CATEGORIES;
		parent::__construct();
	}

	function get_category_lists($require_data = false){
		$this->db->select('c.id, c.ids, c.name, c.url_slug, c.sort, c.status, sn.name as social_network_name, sn.id as main_sn_id');
		$this->db->from($this->tb_categories." c");
		$this->db->join($this->tb_social_network." sn", "sn.id = c.sncate_id", 'left');
		$this->db->order_by("sn.sort", 'ASC');
		$this->db->order_by("c.sort", 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		$sn_group = array();
		if ($result) {
			foreach ($query->result_array() as $row) {
               $sn_group[$row['social_network_name']][] = (object)$row;
         	}
		}
		return $sn_group;
	}

	function get_category_lists_by_search($k){
		$k = trim(htmlspecialchars($k));
		$this->db->select('c.*, sn.name as social_network_name');
		$this->db->from($this->tb_categories." c");
		$this->db->join($this->tb_social_network." sn", "sn.id = c.sncate_id", 'left');

		if ($k != "" && strlen($k) >= 2) {
			$this->db->where("(`c`.`name` LIKE '%".$k."%' ESCAPE '!' OR  `sn`.`name` LIKE '%".$k."%' ESCAPE '!')");
		}
		$this->db->where('c.lang_code', "en");
		$this->db->order_by("c.sort", 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
	
	function get_categories_by_social_network_id($id){
		$this->db->select('c.*, sn.name as social_network_name');
		$this->db->from($this->tb_categories." c");
		$this->db->join($this->tb_social_network." sn", "sn.id = c.sncate_id", 'left');

		$this->db->where("c.sncate_id", $id);
		$this->db->where('c.lang_code', "en");
		$this->db->order_by("c.sort", 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
}
