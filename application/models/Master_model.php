<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Master_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


	function getlistPuskesmas(){
		$this->db->order_by('id_puskesmas', 'ASC');
		$qry = $this->db->get('mst_puskesmas');
		$row = $qry->result();
		return $row;
	}

	function getListStatusAbsensi(){
		$qry = $this->db->get('tbl_status_absensi');
		$row = $qry->result();
		return $row;
	}

	function insertStatusAbsensi(){
		$data = array(
			'status_absensi' => $this->input->post('nama_status'),
			'mnt_penambah' => $this->input->post('menit_penambah'),
			'mnt_pengurang' => $this->input->post('menit_pengurang')
		);
		$this->db->insert('tbl_status_absensi', $data);
		return $this->db->insert_id();
	}

	function getlistJabatan(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_jabatan');
		$row = $qry->result();
		return $row;
	}
	function getlistPendidikan(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_pendidikan');
		$row = $qry->result();
		return $row;
	}
	function getlistPoli(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_poli');
		$row = $qry->result();
		return $row;
	}
	function getlistStatus(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_status');
		$row = $qry->result();
		return $row;
	}


	public function detailMesin($serial_number){
        $this->db->where('serial_number', $serial_number);
        $query = $this->db->get('tbl_mesin_absensi');
        return $query->result();
    }





	function getIpAddress($id_puskesmas){
		$this->db->where('id_puskesmas', $id_puskesmas);
		$qry = $this->db->get('tbl_mesin_absensi');
		$row = $qry->result();
		$ip_address = $row[0]->ip_address;
		return  $ip_address;
	}

	function getlistAktifitas(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_kegiatan');
		$row = $qry->result();
		return $row;
	}

	function getDetailAktifitas($id){
		$this->db->where('id', $id);
		$qry = $this->db->get('mst_kegiatan');
		$row = $qry->result();
		return $row;
	}


	function getJenisCuti($id_cuti){
		$this->db->where('id', $id_cuti);
		$qry = $this->db->get('mst_cuti');
		$row = $qry->result();

		$jenis_cuti = $row[0]->jenis_cuti;
		return $jenis_cuti;
	
	}

	function getlistCuti(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_cuti');
		$row = $qry->result();
		return $row;
	}

	function getlistMasaKerja(){
		$this->db->order_by('id', 'ASC');
		$qry = $this->db->get('mst_masa_kerja');
		$row = $qry->result();
		return $row;
	}


	

	function getlistMesin(){
		$this->db->order_by('id_puskesmas', 'ASC');
		$qry = $this->db->get('tbl_mesin_absensi');
		$row = $qry->result();
		return $row;
	}



	function getListMenu($menu_type='P', $parent_id=0)
	{

		$this->db->order_by('sort', 'ASC');
		if($menu_type=='P'){
			$qry = $this->db->get_where('mst_menu', array('menu_level'=> 'P'));
		}else{
			$qry = $this->db->get_where('mst_menu', array('menu_level'=> 'C', 'parent_id'=> $parent_id));
		}
		$row = $qry->result();
		return $row;
	}

	function getNamaMenu($id_menu){
		$qry = $this->db->get_where('mst_menu', array('id_menu'=> $id_menu));
		$row = $qry->result();
		$menuName = $row[0]->menu_name;
		return $menuName;
	}

	function getNamaJabatan($id_jabatan){
		$qry = $this->db->get_where('mst_jabatan', array('id'=> $id_jabatan));
		$row = $qry->result();
		$jabatan = $row[0]->nama;
		return $jabatan;
	}


	function getNamaPendidikan($id_pendidikan){
		$qry = $this->db->get_where('mst_pendidikan', array('id'=> $id_pendidikan));
		$row = $qry->row();
		$pendidikan = $row->pendidikan;
		return $pendidikan;
	}

	function getIdPuskesmas($nama){
		$qry = $this->db->get_where('mst_puskesmas', array('nama'=> $nama));
		$row = $qry->result();
		$id_puskesmas = $row[0]->id_puskesmas;
		return $id_puskesmas;
	}



	function getMstGaji($id_masa_kerja, $id_status, $id_pend)
	{

		$sql = "SELECT jumlah FROM mst_gaji WHERE id_masa_kerja = $id_masa_kerja AND id_status = $id_status AND id_pendidikan = $id_pend";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		if (!empty($row)) {
			$jumlah = $row[0]->jumlah;
		} else {
			$jumlah = 0;
		}


		return $jumlah;
	}

	function getKapuskec(){
		$this->db->where('usergroup', 2);
		$this->db->select('nama, nip, id_pegawai');

		$qry = $this->db->get('mst_pegawai');
		$row = $qry->result();
		return $row;

	}

	function getKasubbagTU(){
		$this->db->where('usergroup', 1);
		$this->db->select('nama, nip, id_pegawai');

		$qry = $this->db->get('mst_pegawai');
		$row = $qry->result();
		return $row;

	}


	function getNamaPuskesmas($id_puskesmas){
		$qry = $this->db->get_where('mst_puskesmas', array('id_puskesmas'=> $id_puskesmas));
		$row = $qry->result();
		$puskesmas = $row[0]->nama;
		return $puskesmas;
	}

	function getMasaKerja($id){
		$qry = $this->db->get_where('mst_masa_kerja', array('id'=> $id));
		$row = $qry->result();
		#$masa_kerja = $row[0]->masa_kerja;
		return $row;
	}

	function cekJabatan($nama_jabatan){
		$qry = $this->db->get_where('mst_jabatan', array('nama'=> $nama_jabatan));
		$row = $qry->result();

		if(empty($row)){

			$data = array('nama' => $nama_jabatan);
			$this->db->insert('mst_jabatan', $data);

			$id_jabatan = $this->cekJabatan($nama_jabatan);
			return $id_jabatan;

		}else{
			$id_jabatan = $row[0]->id;
			return $id_jabatan;
		}

	}

	function getIdMasaKerja($tahun){
		if ($tahun % 2 == 0) {
			$sql = "SELECT * FROM mst_masa_kerja WHERE start =  $tahun ORDER BY id ASC LIMIT 1 OFFSET 0";

		}else{
			//angka ganjil
			$sql = "SELECT * FROM mst_masa_kerja WHERE start < $tahun ORDER BY id DESC LIMIT 1 OFFSET 0";

		}

		$qry = $this->db->query($sql);
		$row = $qry->result();
		$id_masa_kerja = $row[0]->id;
		return $id_masa_kerja;

	}

	function getGajiPokok($id_masa_kerja, $id_pendidikan){
		$qry = $this->db->get_where('mst_gaji', array('id_masa_kerja'=> $id_masa_kerja, 'id_pendidikan'=> $id_pendidikan, 'id_status'=> 4));
		$row = $qry->result();
		$gaji_pokok = $row[0]->jumlah;
		return $gaji_pokok;
	}

	function insertJabatan(){
		$data = array(
			'nama' => $this->input->post('nama_jabatan')
		);

		$this->db->insert('mst_jabatan', $data);
		return true;
	}


	function insertMenu($menu_type, $parent_id){
		$data = array(
			'menu_level' => $menu_type,
			'parent_id'=> $parent_id,
			'menu_name' => $this->input->post('menu_name'),
			'controller' => $this->input->post('controller'),
			'link' => $this->input->post('url'),
			'icon' => $this->input->post('icon'),
			'sort' => $this->getLastSort($menu_type, $parent_id)
		);

		$this->db->insert('mst_menu', $data);
		return true;
	}


	function getLastSort($menu_type='P', $parent_id=0){

		$this->db->order_by('sort', 'DESC');
		$this->db->select('sort');

		if($menu_type=='P'){
			$qry = $this->db->get_where('mst_menu', array('menu_level'=> 'P'));
		}else{
			$qry = $this->db->get_where('mst_menu', array('menu_level'=> 'C', 'parent_id'=> $parent_id));
		}

		$row = $qry->result();

		if(empty($row)){
			$new_sort = 1;
		}else{
			$sort = $row[0]->sort;
			$new_sort = $sort+1;
		}

		return $new_sort;


	}


	function tarikData($ip_address, $bulan, $pin){

        $new_array = array();
		$num = 0;



	    if($pin == '0569'){
            $pin_mesin = '10569';
        }else{
            $pin_mesin = $pin;
        }

		$buffer = $this->Sinkron_model->getDataPresensi($ip_address, $pin_mesin);


		for($a=0; $a < count($buffer);$a++){
			$data=$this->Sinkron_model->Parse_Data($buffer[$a],"<Row>","</Row>");
			$PIN_absen = $this->Sinkron_model->Parse_Data($data,"<PIN>","</PIN>");
			$DateTime  = $this->Sinkron_model->Parse_Data($data,"<DateTime>","</DateTime>");
			$Verified  = $this->Sinkron_model->Parse_Data($data,"<Verified>","</Verified>");
			$Status    = $this->Sinkron_model->Parse_Data($data,"<Status>","</Status>");

			$bulan_absen = date('m', strtotime($DateTime));


			if($bulan_absen == $bulan){


				array_push($new_array, $DateTime.'/'.$Status);

			}


        }


        return $new_array;
    }


	public function get_by_id($id)
	{
		return $this->db->get_where('mst_shift_kerja', ['id' => $id])->row_array();
	}


	function getJamKerjaShift($shift){
		$this->db->where('kode_shift', $shift);
		$qry = $this->db->get('mst_shift_kerja');
		$row = $qry->result();

		$jam_masuk = $row[0]->jam_masuk;
		$jam_pulang = $row[0]->jam_pulang;

		return array($jam_masuk, $jam_pulang);
	}


	function shiftKerjaUGD(){
		$this->db->order_by('urutan', 'ASC');
		$qry = $this->db->get('mst_shift_kerja', 8, 0);
		$row = $qry->result();

		return $row;
	}

	function getShiftKerja($publish=''){
		$this->db->order_by('urutan', 'ASC');
		if($publish != ''){
			$this->db->where('publish', $publish);
		}
		$qry = $this->db->get('mst_shift_kerja');
		$row = $qry->result();
		return $row;
	}


	function getMenitEfektifBulan($bulan, $tahun){

		//jika bulan dibawah bulan april, maka ambil dari ts)_hari kerja, tp klo bulan 5  keatas dan tahun 2026 maka ambil dari table  	tbl_shift_template_detail

		if($tahun >= 2026 && $bulan >= 5){
			$table = 'tbl_shift_template_detail';
			$sql = $this->db->where('tanggal >=', $tahun.'-'.$bulan.'-01')
						->where('tanggal <=', $tahun.'-'.$bulan.'-31')
						->where('shift_id >', 1)
						->get($table);
			$row = $sql->num_rows();
			$jumlah_hari = $row;

		
		}else{
			$qry = $this->db->get_where('ts_hari_kerja', array('bulan'=> $bulan, 'tahun'=> $tahun));
			$row = $qry->result();
			if(!empty($row)){
				$jumlah_hari = $row[0]->jumlah_hari ;
			}else{
				$jumlah_hari = 0;
			}
		}

		
		return $jumlah_hari;

	}

	function getHariKerja($tahun){

		$this->db->where('tahun', $tahun);
		$this->db->order_by('bulan', 'DESC');
		$qry = $this->db->get('ts_hari_kerja');
		$row = $qry->result();
		return $row;
	}

	function getBagianShift(){

		$this->db->order_by('id_bagian', 'ASC');
		$qry = $this->db->get('mst_bagian');
		$row = $qry->result();
		return $row;
	}


	function getHariLibur(){

		$this->db->order_by('tgl', 'DESC');
		$qry = $this->db->get('ts_hari_libur');
		$row = $qry->result();
		return $row;
	}

	function getDetMesinAbsensi($serial_number){
		$this->db->where('serial_number', $serial_number);
		$qry = $this->db->get('tbl_mesin_absensi');
		$row = $qry->result();
		return $row;
	}
	function getMesinAbsensi(){

		$this->db->order_by('id_puskesmas', 'DESC');
		$qry = $this->db->get('tbl_mesin_absensi');
		$row = $qry->result();
		return $row;
	}


	function insertHariKerja(){
		$data = array(
			'bulan' => $this->input->post('bulan'),
			'tahun' => $this->input->post('tahun'),
			'jumlah_hari' => $this->input->post('jumlah_hari')
		);

		$this->db->insert('ts_hari_kerja', $data);
		return true;
	}

	function updatetHariKerja($id){
		$data = array(
			'bulan' => $this->input->post('bulan'),
			'tahun' => $this->input->post('tahun'),
			'jumlah_hari' => $this->input->post('jumlah_hari')
		);
		$this->db->where('id', $id);
		$this->db->update('ts_hari_kerja', $data);
		return true;
	}


	function getEditHariKerja($id){
		$qry = $this->db->get_where('ts_hari_kerja', array('id'=> $id));
		$row = $qry->result();
		return $row;
	}


	function insertHariLibur(){
		$data = array(
			'tgl' => $this->input->post('tgl'),
			'keterangan' => $this->input->post('keterangan')
		);

		$this->db->insert('ts_hari_libur', $data);
		return true;
	}

	function cekHakAkses($ug_id, $id_menu){

		$qry = $this->db->get_where('tbl_hak_akses', array('usergroup_id'=> $ug_id, 'id_menu'=> $id_menu));
		return  $qry->result();
	}

	function uploadFilePDF($path, $file_name, $max_size){


		$config = array(
            'upload_path'   => './uploads/'.$path.'/',
            'allowed_types' => 'pdf',
            'overwrite'     => 1,
            'max_size'     => $max_size,
            'file_name'    => $file_name
        );

        $this->load->library('upload');
        $this->upload->initialize($config);

		if ($this->upload->do_upload('filedocs')) {
            $this->upload->data();

			return true;
        } else {

            $error = array('error' => $this->upload->display_errors());
			$err_msg = $error['error'];


			if($err_msg=='<p>The file you are attempting to upload is larger than the permitted size.</p>'){
				$error_message = 'Ukuran file terlalu besar! Ukuran yang diizinkan = 1 MB';
			}else if($err_msg=='<p>The filetype you are attempting to upload is not allowed.</p>'){
				$error_message = 'Format file tidak dizinkan! Hanya format file PDF yang diizinkan';
			}else if($err_msg=='<p>The uploaded file exceeds the maximum allowed size in your PHP configuration file.</p>'){
				$error_message = 'Ukuran file terlalu besar! Ukuran yang diizinkan = 1 MB';
			}else{
				$error_message = $err_msg;
			}
			// $pesan =  createMessageInfo($error_message, 'danger');


			$this->session->set_flashdata('message_status', 250);
			$this->session->set_flashdata('message', 'Gagal !! '.$error_message);
			$this->session->set_userdata($this->input->post());

			return false;


        }

	}



	// Fungsi untuk melakukan proses upload file
	public function upload_file($filename, $path){
		$this->load->library('upload'); // Load librari upload

		$config['upload_path'] = './uploads/'.$path.'/';
		$config['allowed_types'] = 'xlsx';
		$config['max_size']	= '2048';
		$config['overwrite'] = true;
		$config['file_name'] = $filename;


		//print_array($_FILES);



		$this->upload->initialize($config); // Load konfigurasi uploadnya
		if($this->upload->do_upload('file')){ // Lakukan upload dan Cek jika proses upload berhasil
		// Jika berhasil :
		$return = array('result' => 'success', 'file' => $this->upload->data(), 'error' => '');
		return $return;
		}else{
		// Jika gagal :
		$return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
		return $return;
		}

	}

	function config_global(){

		$qry = $this->db->get('global_config');
		$row = $qry->result();
		return $row;
	}


	public function getRows($id = ''){
        $this->db->select('id,file_name,uploaded_on');
        $this->db->from('files');
        if($id){
            $this->db->where('id',$id);
            $query = $this->db->get();
            $result = $query->row_array();
        }else{
            $this->db->order_by('uploaded_on','desc');
            $query = $this->db->get();
            $result = $query->result_array();
        }
        return !empty($result)?$result:false;
    }

}
