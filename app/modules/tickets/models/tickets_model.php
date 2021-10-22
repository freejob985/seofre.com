<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class tickets_model extends MY_Model {
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_orders;
	public $tb_tickets;
	public $tb_ticket_message;

	public function __construct(){
		parent::__construct();
		//Config Module
		$this->tb_users      = USERS;
		$this->tb_categories = CATEGORIES;
		$this->tb_services   = SERVICES;
		$this->tb_orders     = ORDER;
		$this->tb_tickets    = TICKETS;
		$this->tb_ticket_message    = TICKET_MESSAGES;

	}

	function get_tickets($total_rows = false, $status = "", $limit = "", $start = ""){
		$this->db->select('*');
		$this->db->from($this->tb_tickets);
		$this->db->order_by('changed', 'DESC');
		$this->db->limit($limit, $start);
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

	function get_ticket_detail($id){
		if (get_role("user")) {
			$this->db->where("tk.uid", session("uid"));
		}
		$this->db->select('tk.*, u.email as user_email, u.first_name, u.last_name,u.role');
		$this->db->from($this->tb_tickets." tk");
		$this->db->join($this->tb_users." u", "u.id = tk.uid", 'left');
		$this->db->where("tk.id", $id);
		$this->db->order_by('tk.changed', 'DESC');
		$query = $this->db->get();
		if($query->row()){
			return $data = $query->row();
		}else{
			return false;
		}
	}

	function get_ticket_content($id){
		if (get_role("admin")) {
			$this->db->order_by('tk_m.created', 'DESC');
		}else{
			$this->db->order_by('tk_m.created', 'ASC');
		}
		$this->db->select('tk_m.*, u.email as user_email, u.first_name, u.last_name,u.role');
		$this->db->from($this->tb_ticket_message." tk_m");
		$this->db->join($this->tb_users." u", "u.id = tk_m.uid", 'left');
		$this->db->where("tk_m.ticket_id", $id);
		$query = $this->db->get();
		if($query->result()){
			return $data = $query->result();
		}else{
			return false;
		}
	}


	// Get Count of orders by Search query
	public function get_count_tickets_by_search($search = []){
		$k = trim($search['k']);
		$where_like = "";
		switch ($search['type']) {
			case 1:
				#Ticket ip
				$where_like = "`tk`.`ip` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				# User Email
				$where_like = "`tk`.`from` LIKE '%".$k."%' ESCAPE '!'";
				break;

			case 3:
				# Subjects
				$where_like = "`tk`.`subject` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}
		$this->db->select('tk.id');
		$this->db->from($this->tb_tickets." tk");
		if ($where_like) $this->db->where($where_like);
		$query = $this->db->get();
		$number_row = $query->num_rows();
		return $number_row;
	}

	// Search Logs by keywork and search type
	public function search_logs_by_get_method($search, $limit = "", $start = ""){
		$k = trim($search['k']);
		$where_like = "";
		switch ($search['type']) {
			case 1:
				#Ticket ip
				$where_like = "`tk`.`ip` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				# User Email
				$where_like = "`tk`.`from` LIKE '%".$k."%' ESCAPE '!'";
				break;

			case 3:
				# Subjects
				$where_like = "`tk`.`subject` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select('tk.*');
		$this->db->from($this->tb_tickets." tk");
		if ($where_like) $this->db->where($where_like);
		$this->db->order_by("FIELD ( tk.status, 'pending', 'answered', 'closed')");
		$this->db->order_by('tk.changed', 'DESC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
}
