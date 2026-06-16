<?php ob_start();
defined('BASEPATH') OR exit('No direct script access allowed');
class Shift_model extends CI_Model
{
	function __construct()
	{
        parent::__construct();
		
    }
	
	function getDataMasterShift()
	{
		
		$this->db->select('*');
		$this->db->from('mst_shift_kerja');
		$this->db->where('flag', 1);
		$this->db->order_by('shift', 'ASC');		
		$query = $this->db->get();
		$row = $query->result();
		
		return $row;
		
	}
	function getDataShiftPegawai($bulan_tahun)
	{
		$this->db->select('*');
		$this->db->from('ts_shift_kerja');
		$this->db->where('bulan_tahun', $bulan_tahun);
		$this->db->group_by('id_pegawai');		
		$query = $this->db->get();
		$row = $query->result();
		
		return $row;
		
	}
	
	function getDetailShift($id_shift)
	{
		$sql = "SELECT a.id, tanggal, a.bulan_tahun, b.name AS shift 
		FROM ts_shift_kerja a LEFT JOIN mst_shift_kerja b ON a.shift=b.shift
		WHERE a.id = $id_shift";
		$qry =$this->db->query($sql);
		$row = $qry->result();
		
		return $row;
	}
	
	function getDataShiftPerPegawai($id_pegawai, $tanggal)
	{
		
		$this->db->select('a.id, a.tanggal, b.nama_shift AS shift, b.jam_masuk, b.jam_pulang');
		$this->db->from('ts_shift_kerja a');
		$this->db->join('mst_shift_kerja b', 'a.shift = b.nama_shift', 'left');
		$this->db->where('a.id_pegawai', $id_pegawai);
		$this->db->where('a.tanggal', $tanggal);

		$qry = $this->db->get();
		$row = $qry->result();

		
		return $row;
		
	}


	function getShiftPerbulanById($id_pegawai, $periode){
		
		$this->db->like('tanggal', $periode, 'after');     // Produces: WHERE `title` LIKE 'match%' ESCAPE '!'
		$this->db->where('id_pegawai', $id_pegawai);
		$qry = $this->db->get('ts_shift_kerja');
		$row = $qry->result();
		return $row;
		
	}


	function getShiftPerbulan($pin, $periode){
		
		$this->db->like('tanggal', $periode, 'after');     // Produces: WHERE `title` LIKE 'match%' ESCAPE '!'
		$this->db->where('pin', $pin);
		$qry = $this->db->get('ts_shift_kerja');
		$row = $qry->result();
		return $row;
		
	}

	
	function getShiftPerhari($pin, $tgl){
		
		$qry = $this->db->get_where('ts_shift_kerja', array('pin'=> $pin, 'tanggal'=> $tgl));
		$row = $qry->result();


		if(!empty($row)){
			$shift = $row[0]->shift;
		}else{
			$shift = 'OFF';
		}
		
		return $shift;
		
	}
	
	
	function insertDataShift()
	{
		
		$data = array(
			'shift' => $this->input->post('shift_id'),
			'name' => $this->input->post('shift_name'),
			'jam_masuk' => $this->input->post('jam_masuk'),
			'jam_pulang' => $this->input->post('jam_pulang'),
			'keterangan' => $this->input->post('keterangan')
		);
		
		$this->db->insert('mst_shift_kerja', $data);
		
		return true;
		
	}
	 
	  function deleteData($tgl, $id_pegawai)
	 {
			$sql = "DELETE FROM jadwal_shift WHERE tgl ='$tgl' AND id_pegawai = $id_pegawai";			
			$qry = $this->db->query($sql);
			
			return true;
	 }
	 
	 
	 function insertData($tgl, $id_pegawai, $jns, $shift, $shift_name)
	 {
			
			$array = array(
					'id_pegawai'=>$id_pegawai,
					'jns' =>$jns,
					'tgl' => $tgl,
					'shift' =>$shift,
					'shift_name' =>$shift_name
					
			);
			
			$this->db->insert('jadwal_shift', $array);
			
			return true;
	 }
	 
	 function getDataShift($tgl, $jns)
	 {
		 
		 $sql  = "SELECT * FROM jadwal_shift WHERE tgl like '$tgl%' AND jns = $jns GROUP BY id_pegawai";
		 $qry = $this->db->query($sql);
		 $row = $qry->result();
		 
		 return $row;
		 
		 
	 }
	 
	 
	 
	 
}
?>