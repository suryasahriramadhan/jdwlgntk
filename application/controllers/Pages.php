<?php
class Pages extends CI_Controller
{
	function __construct(){
	parent::__construct();	
	}
	public function home(){
	$this->load->view('template/header');
	$this->load->view('pages/hompage');
	$this->load->view('template/footer');
	}

}

