<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        // Load model 'My_model'
         $this->load->model('Master_model');
         $this->load->model('Pegawai_model');
         $this->load->model('Auth_model');
		
    }

	public function index()
	{

		$data['pegawai'] = $this->Master_model->get_data_pegawai();
        $data['mst_puskesmas'] = $this->Master_model->getMasterDataList('mst_puskesmas');
		//$this->load->view('home', $data);
        $data['title'] = 'Dashboard';
        $data['content'] = $this->load->view('home', [], true);
        $this->load->view('layout/main', $data);
	}
}
