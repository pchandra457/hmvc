<?php

class Home_model extends CI_Model{
	
	var $table = "users";
	
	function __construct(){
		parent::__construct();
	}
	
	function create($data){
		$str = $this->db->insert_string($this->table, $data);
		
		$query = $this->db->query($str);
		
		if($query){
			return true;
		}else{
			return false;
		}
		
	}
	
	function read(){
		$query = $this->db->query("SELECT * FROM $this->table");
		return $query->result();
	}
	
	function user_by_id($id){
		$query = $this->db->query("
			SELECT * 
			FROM $this->table
			WHERE id = $id
		");
		
		$query->row()->role = $this->get_role($id);
		$query->row()->role_name = $this->get_role_name($query->row()->role);
		
		if($query->num_rows > 0){
			return $query->row();
		}else{
			return false;
		}
	}
	
	function get_role($user_id){
		$query = $this->db->query("SELECT role_id FROM users_roles WHERE user_id = $user_id");
		if($query->num_rows > 0){
			return (int)$query->row()->role_id;
		}else{
			return 0;
		}
	}
	function get_role_name($role_id){
		$query = $this->db->query("SELECT name FROM roles WHERE id = $role_id");
		if($query->num_rows > 0){
			return $query->row()->name;
		}else{
			return false;
		}
	}
	
	function validate($user_email, $password){
		$query = $this->db->query("SELECT * FROM $this->table WHERE user_email = '$user_email' AND user_pass = '$password'");
		if($query->num_rows === 1){
			return $query->row();
		}else{
			return false;
		}
	}
	
		
}

?>