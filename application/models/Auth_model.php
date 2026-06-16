<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Auth_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

    function cekDataPegawai($nip){
        //apakah nip tersebut ada didatabase
        $nip_leght = strlen($nip);
        if($nip_leght < 10){
            //menggunakan NRK
            $qry = $this->db->get_where('mst_pegawai', array('nrk'=> $nip));
        }else{
            //pake NIP 
            
            $thn_angg = '2024';
            #echo 'pake nip';
            $this->db->limit(3,0);
            #$this->db->order_by('tahun_anggaran','DESC');
            $qry = $this->db->get_where('mst_pegawai', array('nip'=> $nip, 'tahun_anggaran'=> $thn_angg));
        }
		
		$row = $qry->result();

    
		return $row;
    }

	function cekUserAuth($nip, $pass)
	{
	
        $nip_leght = strlen($nip);
        if($nip_leght < 10){
            //menggunakan NRK
            $qry = $this->db->get_where('mst_pegawai', array('nrk'=> $nip, 'password'=> $pass));
        }else{
            //pake NIP 
            
            $thn_angg = '2024';
            #echo 'pake nip';
            $this->db->limit(3,0);
            #$this->db->order_by('tahun_anggaran','DESC');
            $qry = $this->db->get_where('mst_pegawai', array('nip'=> $nip, 'password'=> $pass, 'tahun_anggaran'=> $thn_angg));
        }
		
		$row = $qry->result();

    
		return $row;
	}

    function cekAuthMenu($usergroup_id, $menu_id){
        $qry = $this->db->get_where('tbl_hak_akses', array('usergroup_id'=> $usergroup_id, 'id_menu'=> $menu_id));
        $row = $qry->num_rows();

        if($row==0){
            return false;
        }else{
            return true;
        }

    }

    function cekSession(){

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
	
		if($periode_bulan=='' || $periode_tahun=='') {
			$bulan = date('m');
			$tahun = date('Y');

			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);


		  }

          return true;
    }


    function getLastSort(){
        $this->db->limit(1);
        $this->db->order_by('sort_order', 'DESC');
        $this->db->from('mst_menu');
        $qry = $this->db->get();
        $row = $qry->result();
        $sort_order = $row[0]->sort_order;
        return $sort_order;

    }

    function get_menu(){
        $this->db->order_by('menu_level', 'ASC');
        $this->db->order_by('sort_order', 'ASC');
        $this->db->from('mst_menu');
        $qry = $this->db->get();
        $row = $qry->result();

        return $row;
       

    }

    function get_usergroup(){
        $this->db->from('usergroup');
        $qry = $this->db->get();
        $row = $qry->result();

        return $row;
       

    }

    

    function getMenuUsergroup($id_usergroup){

	    $this->db->where('id', $id_usergroup);
        $this->db->from('usergroup');
        $qry = $this->db->get();
        $row = $qry->result();

        return $row;
    }

    function getUserinGroup($id_usergroup){
            $this->db->order_by('mst_pegawai.id_jabatan', 'ASC');
			$this->db->where('status_kerja >', 0);
			$this->db->where('usergroup', $id_usergroup);
			$this->db->select('mst_pegawai.*,mst_jabatan.nama AS jabatan');
			$this->db->from('mst_pegawai');
			$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
			$qry = $this->db->get();

			//echo $this->db->last_query();
			$row = $qry->result();
			return $row;

    
    }

    function getMenuChild($id_menu){
        $this->db->where('parent_id', $id_menu);
        $this->db->from('mst_menu');
        $qry = $this->db->get();
        $row = $qry->result();

        return $row;
    }


    function getMenuName($id_menu){
        $this->db->where('id_menu', $id_menu);
        $this->db->from('mst_menu');
        $qry = $this->db->get();
        $row = $qry->result();

        if(!empty($row)){
            $menuName = $row[0]->menu_name;
        }else{
             $menuName = 'N/A';
        }

        return $menuName;
    }
    

    function  cekAuthLogin(){
        //cek apakah sudah habis sessionya
        $id_pegawai = $this->session->userdata('id_pegawai');

       

        if($id_pegawai==''){
            $getUri = $this->uri->uri_string();

            $this->session->set_userdata('uri_login', $getUri);
            redirect('login/index');
            
        }
    }

    function updatePassword($new_pass){
        $id_pegawai = $this->session->userdata('id_pegawai');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->set('password', $new_pass);
        $this->db->update('mst_pegawai');
        return true;

    }

}