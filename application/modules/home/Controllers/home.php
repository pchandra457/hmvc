<?php

class Home extends MY_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('home_model');
		$this->load->library('form_validation');
	}
	
	function index(){
		$data['main_content'] = 'index';
		$this->load->view('login', $data);
	}
	
	function signin(){
		//Redirect
		if($this->_is_logged_in()){
			redirect('');
		}
		
		if($_POST){
			//Data
			$user_email = $this->input->post('user_email', true);
			$password 	= $this->input->post('password', true);

			$userdata 	= $this->home_model->validate($user_email, md5($password));
			//Validation
			if($userdata){
				if($userdata->status == 0){
					$data['error'] = "Not validated!";
					$data['main_content'] = 'index';
					$this->load->view('login', $data);
				}else{
					$data['userid'] = $userdata->id;
					$data['logged_in'] = true;
					$this->session->set_userdata($data);
					redirect('admin');
				}				
			}else{
				$data['error'] = "Your email / password not correct!";
				$data['main_content'] = 'index';
				$this->load->view('login', $data);
			}

			return;
		}
		$data['main_content'] = 'index';
		$this->load->view('login', $data);
	}
	
	function signup(){
		if($_POST){
		
			$config = array(
				array(
					'field' => 'fullname',
					'label' => 'Full name',
					'rules' => 'trim|required',
				),
				array(
					'field' => 'username',
					'label' => 'User name',
					'rules' => 'trim|is_unique[users.user_nicename]',
				),
				array(
					'field' => 'email',
					'label' => 'E-mail',
					'rules' => 'trim|required|valid_email|is_unique[users.user_email]',
				),
				array(
					'field' => 'password',
					'label' => 'Password',
					'rules' => 'trim|required',
				)
			);
			
			$this->form_validation->set_rules($config);
			
			if($this->form_validation->run() === false){
				$data['error'] = validation_errors();
				$data['main_content'] = 'signup';
				$this->load->view('page', $data);
			}else{
				$data['user_login']		= $this->input->post('fullname',true);
				$data['user_pass']		= md5($this->input->post('password',true));
				$data['user_nicename']	= $this->input->post('username',true); 
				$data['user_email']		= $this->input->post('email',true);
				$data['activation_key']	= md5(rand(0,1000).'uniquefrasehere');
				
				$create = $this->home_model->create($data);
				
				if($create){
					
					//Send validation mail 
					
					$this->load->library('email');

					$this->email->from('noreply@yoursite.com', 'Site Name');
					$this->email->to($data['user_email']); 
					
					$this->email->subject('Confirmation');
					$this->email->message("Confirm your subscription <a href=''>Confirmar</a>".$data['activation_key']);	
					$this->email->send();
					
					
					$data['main_content'] = 'signup-success';
					$this->load->view('page', $data);
					
					
				
				}else{
					error_log("Un usuario no se pudo registrar");
				}
			}
			
			
			return;
		}
		$data['main_content'] = 'signup';
		$this->load->view('page', $data);
	}
	
	function logout(){
		$this->session->sess_destroy();
		redirect('index.php');
	}
	
	function _is_logged_in(){
		if($this->session->userdata('logged_in')){
			return true;
		}else{
			return false;
		}
	}
	
	function userdata(){
		if($this->_is_logged_in()){
			return $this->home_model->user_by_id($this->session->userdata('userid'));
		}else{
			return false;
		}
	}
	
	function _is_admin(){
		if(@$this->home->userdata()->role === 1){
			return true;
		}else{
			return false;
		}
	}
		
}

?>