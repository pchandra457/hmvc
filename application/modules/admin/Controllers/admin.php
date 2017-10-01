<?php

class Admin extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->module("home");
		
		if(!$this->home->_is_admin()){
			redirect('index.php');
			//show_404();
		}
	}
	
	function index(){
		$data['main_content'] = 'dashboard';
		$data['currentuser'] = @$this->home->userdata();
		$this->load->view('page', $data);
	}
		
}

?>