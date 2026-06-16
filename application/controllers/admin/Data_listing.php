<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Data_listing  extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_model');
        $this->load->model('Admin_cuti_model', 'acm');
        $this->load->helper('text');
        $this->Auth_model->cekAuthLogin();
    }


    public function thr($case='tkd')
    {
        if($case=='tkd'){
            $tahun  = date('Y');
            $dataTKD = $this->db->where('tahun', $tahun)->get('ts_listing_thr_tkd')->result();
            $data['listing_tkd'] = $dataTKD;

            //$this->load->view('admin/listing_tkd/main', $data);
            $this->load->view('admin/data_listing/listing_thr_tkd', $data);
         }else{
            $periode  = date('Y-m');
            $dataTKD = $this->db->where('periode', $periode)->get('ts_listing_thr')->result();
            $data['datalist'] = $dataTKD;

            //$this->load->view('admin/listing_tkd/main', $data);
            $this->load->view('admin/data_listing/listing_thr_gaji', $data);
        }
       


    }


}