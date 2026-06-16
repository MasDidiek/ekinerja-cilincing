<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Renkin extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();
	
		$this->Auth_model->cekAuthLogin();

	}

	function index()
	{

		
		//$data['renkin'] = $this->Pegawai_model->detailPegawaiPJLP($id_pjlp);
		$this->load->view('admin/renkin/index');
		
       
    }

    function renkin_saya(){
        $this->load->view('kinerja/renkin');
    }

}