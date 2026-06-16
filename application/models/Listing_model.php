<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Listing_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}



    function getCapaianTerkecil($periode)
    {
        $sql = "SELECT id, nama, capaian FROM ts_rekap_tkd WHERE periode = '$periode' AND capaian != 50 ORDER BY capaian ASC LIMIT 10 OFFSET 0";
        $qry = $this->db->query($sql);
        return $qry->result();
    }



    function getListingTHR($nip, $jns_thr='tkd') {

        if($jns_thr=='tkd'){

            $this->db->order_by('tahun', 'DESC');
            $this->db->where('nip', $nip);
            $qry = $this->db->get('ts_listing_thr_tkd', 1,0);
            
        }else{

            $this->db->order_by('periode', 'DESC');
            $this->db->where('nip', $nip);
            $qry = $this->db->get('ts_listing_thr', 1,0);
            
        }

        return $qry->row();

    }



}


