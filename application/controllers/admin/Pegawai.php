<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Pegawai extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();
		$this->load->model('Old_model');
		$this->load->model('Profile_model');
		$this->load->model('Laporan_model');
		  $this->load->model('Admin_cuti_model', 'acm');
		$this->load->helper('text');
		$this->Auth_model->cekAuthLogin();

	}

    function list_pegawai($jns_pegawai='non_pns'){
       $thn_anggrn  = 2024;
		$data['title'] = 'Data Pegawai';
		if($jns_pegawai=='pjlp'){
			$data['pegawai'] = $this->Pegawai_model->getPegawaiPJLP();
			$this->load->view('admin/pegawai/pegawai_pjlp', $data);
		}else{

			$data['pegawai'] = $this->Pegawai_model->getListPegawai($jns_pegawai, $thn_anggrn );
			$this->load->view('admin/pegawai/pegawai_non_pns', $data);
		}
    }

	function update_pin($jns_pegawai='non_pns'){

		$pegawai = $this->Pegawai_model->getListPegawai($jns_pegawai, 2024);

    	foreach ($pegawai as $peg) {
           $id_pegawai = $peg->id_pegawai;
           $nama = $peg->nama;
           $pin = $this->getNIP($nama);

		   $this->db->where('id_pegawai', $id_pegawai);
		   $this->db->set('pin', $pin);
		   $this->db->update('mst_pegawai');

        }

		echo 'selesai';

    }


	function getNIP($nama){

		$qry = $this->db->get_where('ts_rekap_tkd', ['nama'=> $nama], 1,0);
		$row = $qry->row();

		$nip = $row->nip;
		$pin = substr($nip, -4);

		return $pin;



		//print_array($row);
	}


	function data_pegawai($jns_pegawai='non_pns')
	{

        $thn_anggrn  = 2024;
		$data['title'] = 'Data Pegawai';
			if($jns_pegawai=='pjlp'){
				$data['pegawai'] = $this->Pegawai_model->getPegawaiPJLP();
				$this->load->view('admin/pegawai/pegawai_pjlp', $data);
			}else{

				$data['pegawai'] = $this->Pegawai_model->getListPegawai($jns_pegawai, $thn_anggrn );
				$this->load->view('admin/pegawai/main', $data);
			}

    }



	function detail_pegawai($id_pegawai){
		$tahun = date('Y');
			$detail_pegawai   = $this->Pegawai_model->getDetailPegawai($id_pegawai);


			$data['list_jabatan'] 	   = $this->Master_model->getlistJabatan();
			$data['list_pendidikan']   = $this->Master_model->getlistPendidikan();
			$data['list_poli'] 		   = $this->Master_model->getlistPoli();
			$data['list_puskesmas']    = $this->Master_model->getlistPuskesmas();
			$data['list_Status']       = $this->Master_model->getlistStatus();
			$data['list_validator']    = $this->Pegawai_model->getValidator();


			$nip = $detail_pegawai->nip;
			$data['rekap_absensi']    = $this->Pegawai_model->getRekapAbsensiPegawai($id_pegawai, $tahun);
			$data['rekap_capaian_kinerja'] = $this->Kinerja_model->getListCapaian($nip);
			$data['data_diklat'] = $this->Profile_model->getDataDiklat($nip);
			$data['pegawai'] 		   = $detail_pegawai;

			$data['cutiPegawai']     = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);

			$data['data_tkd']        = $this->Laporan_model->getRekapTKDPegawaipertahun($nip, 2025);
			$data['hubdis']        = $this->Pegawai_model->getHubdisPegawai($nip);


			$jns_pegawai = $detail_pegawai->jns_pegawai;


			$this->load->view('admin/pegawai/detail_pegawai2', $data);


	}

	function view_profile_pegawai($id_pegawai, $nip){


		$data['pegawai'] 		   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$data['data_diklat'] = $this->Profile_model->getDataDiklat($nip);
		$data['data_tkd']        = $this->Laporan_model->getRekapTKDPegawaipertahun($nip, 2025);
		$data['hubdis']        = $this->Pegawai_model->getHubdisPegawai($nip);

		$this->load->view('admin/pegawai/view_profile_pegawai', $data);
	}


	function ajaxDetailHubdis() {

		$id = $this->input->post('id');
		$data['hd']    = $this->Pegawai_model->getDetailHubdis($id);

		$this->load->view('admin/pegawai/detail_hubdis', $data);
	}

	function upload_file_hubdis($nip, $id) {


		$this->db->select('nama, id_pegawai');
		$qry = $this->db->get_where('mst_pegawai', array('nip'=> $nip));
		$row = $qry->result();

		$id_pegawai = $row[0]->id_pegawai;

		$nama = $row[0]->nama;
		$url_title = url_title($nama);
		$fileName = 'SK_hubdis_'.strtolower($url_title);

		$path 		 = 'hubdis';
		$namaFileDB  = $fileName.'.pdf';


        $uploadFile = $this->Master_model->uploadFilePDF($path, $fileName, '1000');
		$fileNameDB = $fileName.'.pdf';

		$this->db->where('id', $id);
		$this->db->set('file_hubdis', $fileNameDB);
		$this->db->update('tbl_hubdis');


		redirect('admin/pegawai/detail_pegawai/'.$id_pegawai );

	}

	function delete_hubdis($id_pegawai) {

		$id_hubdis = $this->input->post('id_hubdis');

		$this->db->where('id', $id_hubdis);
		$this->db->delete('tbl_hubdis');
		redirect('admin/pegawai/detail_pegawai/'.$id_pegawai );

	}

	function detail_pegawai2($id_pegawai){
		$tahun = date('Y');
		$detail_pegawai   = $this->Pegawai_model->getDetailPegawai($id_pegawai);


		$data['list_jabatan'] 	   = $this->Master_model->getlistJabatan();
		$data['list_pendidikan']   = $this->Master_model->getlistPendidikan();
		$data['list_poli'] 		   = $this->Master_model->getlistPoli();
		$data['list_puskesmas']    = $this->Master_model->getlistPuskesmas();
		$data['list_Status']       = $this->Master_model->getlistStatus();
		$data['list_validator']    = $this->Pegawai_model->getValidator();


		$nip = $detail_pegawai->nip;
		$data['rekap_absensi']    = $this->Pegawai_model->getRekapAbsensiPegawai($id_pegawai, $tahun);
		$data['rekap_capaian_kinerja'] = $this->Kinerja_model->getListCapaian($nip);
		$data['data_diklat'] = $this->Profile_model->getDataDiklat($nip);
		$data['pegawai'] 		   = $detail_pegawai;

		$data['cutiPegawai']    = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
		$data['pegawai_gaji']    = $this->Pegawai_model->getDataGajiPegawai($id_pegawai);


		$jns_pegawai = $detail_pegawai->jns_pegawai;


		$this->load->view('admin/pegawai/detail_pegawai', $data);


		// if($jns_pegawai=='pns'){
		// 	$this->load->view('admin/pegawai/detail_pegawai_pns', $data);
		// }else{
		// 	$this->load->view('admin/pegawai/detail_pegawai', $data);
		// }


	}

	function view_log_cuti($id_pegawai){

		$data['logCuti']    = $this->Cuti_model->getLogCuti($id_pegawai);
		$this->load->view('admin/pegawai/view_log_cuti', $data);
	}

	function edit_pegawai($id_pegawai, $nip) {


			$data['list_jabatan'] 	   = $this->Master_model->getlistJabatan();
			$data['list_pendidikan']   = $this->Master_model->getlistPendidikan();
			$data['list_poli'] 		   = $this->Master_model->getlistPoli();
			$data['list_puskesmas']    = $this->Master_model->getlistPuskesmas();
			$data['list_Status']       = $this->Master_model->getlistStatus();
			$data['list_validator']    = $this->Pegawai_model->getValidator();
			$data['bagianShift']       = $this->Master_model->getBagianShift();


			$data['pegawai']  		   = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
			$data['pegawai_detail']    = $this->Pegawai_model->getDataDetailPegawai($nip);


			$this->load->view('admin/pegawai/edit_pegawai', $data);

	}


	function detail_pegawai_pjlp($id){
		$data['list_puskesmas']    = $this->Master_model->getlistPuskesmas();
		$data['pegawai'] 		   =$this->Pegawai_model->getDataEditPegawaiPJLP($id);
		$this->load->view('admin/pegawai/detail_pegawai_pjlp', $data);

	}


	function update_pegawai($id){
		$data = array(
			'nama' => $this->input->post('nama'),
			'id_pjlp' => $this->input->post('id_pjlp'),
			'id_mesin' =>  $this->input->post('id_mesin'),
			'jabatan' =>  $this->input->post('jabatan'),
			'lokasi_kerja' => $this->input->post('lokasi_kerja'),
		);

		$this->db->where('id', $id);
		$this->db->update('tbl_pegawai_pjlp', $data);

	}

	function update_gaji($id_pegawai, $id_gaji) {
		$gaji_pokok = $this->input->post('gaji_pokok');
		$pengkalian = $this->input->post('pengali');
		$gaji_pokok = str_replace(".","", $gaji_pokok);

		if($id_gaji != 0){
			$data = array(
				'gaji_pokok' => $gaji_pokok,
				'pengali' => $pengkalian,
			);

			$this->db->where('id', $id_gaji);
			$this->db->update('tbl_riwayat_gaji', $data);


			$this->db->where('id_pegawai', $id_pegawai);
			$this->db->set('pengali', $pengkalian);
			$this->db->update('mst_pegawai');

		}else{

			$this->Pegawai_model->insertDataGaji($id_pegawai, $gaji_pokok, $pengkalian, 0, 0, 0, 4500000);
		}

		$pesan =  createMessageInfo('Data  pegawai  berhasil disimpan');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/detail_pegawai/'.$id_pegawai );
	}

	function add_pegawai($jns_pegawai=''){
		$data['list_jabatan'] = $this->Master_model->getlistJabatan();
		$data['list_pendidikan'] = $this->Master_model->getlistPendidikan();
		$data['list_poli'] = $this->Master_model->getlistPoli();
		$data['list_puskesmas'] = $this->Master_model->getlistPuskesmas();
		$data['list_Status'] = $this->Master_model->getlistStatus();
		$data['list_validator'] = $this->Pegawai_model->getValidator();
	//	$data['list_validator_pjlp'] = $this->Pegawai_model->getPJ_PJLP();

			if($jns_pegawai=='pjlp'){
				$this->load->view('admin/pegawai/insert_pegawai_pjlp', $data);
			}else{
				$this->load->view('admin/pegawai/insert_pegawai', $data);
			}
	}


	function insert_new_pegawai() {
			$tgl_masuk = $this->input->post('tmt');
			$nip = $this->input->post('nip');

			// ambil 4 digit terakhir dari NIP untuk pin
			$pin = substr($nip, -4);

			// buat password hash dari default password 123456
			$default_password = '123456';
			$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

			$data = array(
				'nama' => $this->input->post('nama'),
				'nip' => $nip,
				'nrk' => '',
				'golongan' => '',
				'id_puskesmas' => $this->input->post('id_puskesmas'),
				'rumpun_kerja' => $this->input->post('rumpun'),
				'id_jabatan' => $this->input->post('jabatan'),
				'id_poli' => $this->input->post('poli'),
				'tgl_masuk' => format_db($tgl_masuk),
				'tmt' => format_db($tgl_masuk),
				'jns_pegawai' => 'non_pns',
				'jns_jam_kerja' => 'non_shift',
				'status_kawin' => 4,
				'status_pajak' => 'TK',
				'id_pendidikan' => $this->input->post('pendidikan'),
				'id_validator' => 1,
				'usergroup' => 7,
				'password' => $hashed_password,
				'kategori_masa_kerja' => 1,
				'masa_kerja' => '0-0-1',
				'pin' => $pin,
				'tahun_anggaran' => date('Y')
			);

			$this->db->insert('mst_pegawai', $data);


		$this->Pegawai_model->insertDataDetail($nip, 0, 0, 0, '', '0000-00-00', '', '', '08', '@gmail.com', 'dummy.png');

		$id_pegawai = $this->Pegawai_model->getlastIDPegawai();

		$gaji_pokok =  $this->input->post('gaji_pokok');
		$pengali =  $this->input->post('pengali');


		$this->Pegawai_model->insertDataGaji($id_pegawai,$gaji_pokok , $pengali, 0, 0, 0, 0);



	}

	function insert_pegawai($jns_pegawai='non_pns'){
		if($jns_pegawai=='pjlp'){
			$this->Pegawai_model->insertNewPegawaiPJLP();
		}else{
			$this->Pegawai_model->insertNewPegawai();
		}

		$pesan =  createMessageInfo('Data  pegawai  berhasil disimpan');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/list_pegawai/'.$jns_pegawai );
	}

	function update_profile_pegawai($id_pegawai, $nip){

		$this->Pegawai_model->updateDataPegawai($id_pegawai);
		$this->Pegawai_model->updateDataDetailPegawai($nip);

		$usergroup = $this->input->post('usergroup');
		$shift_bagian = $this->input->post('shift_bagian');
		$nama = $this->input->post('nama');


		$pesan = '<strong>Success!</strong> Data profile pegawai berhasil diupdate.';
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/edit_pegawai/'.$id_pegawai.'/'.$nip);

	}



	function delete_pegawai(){
        $id_pegawai = $this->input->post('id_pegawai');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->delete('mst_pegawai');

		echo   createMessageInfo('Data  pegawai  berhasil dihapus');
	//	$this->session->set_flashdata('message', $pesan);
	//	redirect('admin/pegawai/data_pegawai/non_pns');
	}

// 	function delete_pegawai($id_pegawai){

// 		$this->db->where('id_pegawai', $id_pegawai);
// 		$this->db->delete('mst_pegawai');

// 		$pesan =  createMessageInfo('Data  pegawai  berhasil dihapus');
// 		$this->session->set_flashdata('message', $pesan);
// 		redirect('admin/pegawai/data_pegawai/non_pns');
// 	}


	function cuti_pegawai()  {

		$jns_pegawai = 'non_pns';
		$thn_anggrn = 2024;

		$data['pegawai'] = $this->Pegawai_model->getListPegawai($jns_pegawai, $thn_anggrn );
		$this->load->view('admin/pegawai/cuti_pegawai_non_pns', $data);
	}



	function detail_cuti($id_cuti=0){
			$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
			$this->load->view('admin/pegawai/detail_cuti', $data);
	}

	function cancel_cuti($id_cuti=0){
		$detail_cuti   = $this->Cuti_model->getDetailCuti($id_cuti);
        $id_pegawai    = $detail_cuti[0]->id_pegawai;
        $jns_hak_cuti  = $detail_cuti[0]->jns_hak_cuti;
        $hari_cuti     = $detail_cuti[0]->hari_cuti;
        $status        = $detail_cuti[0]->status;
        $jns_cuti      = $detail_cuti[0]->jns_cuti;

		if($status == 'APPROVE'){
            //klo sudah di acc sama kapus kecamatan, harus kembalikan cutinya
            $sisa_cuti  = $this->Cuti_model->getSisaCuti($id_pegawai, $jns_hak_cuti);
            $sisa_akhir = $sisa_cuti+$hari_cuti;
            $ket = 'Sisa  cuti dikembalikan, pembatalan cuti';
            $this->Cuti_model->insertLogCuti( $id_pegawai, $jns_hak_cuti, $jns_cuti, $id_cuti, $hari_cuti, $sisa_akhir, $ket);

           // $this->Presensi_model->updateAbsensiCancelCuti($id_cuti, $pin);
        }

        $this->db->where('id', $id_cuti);
        $this->db->set('status', 'CANCEL');
        $this->db->update('ts_cuti');

        $this->session->set_flashdata('message', 'Cuti telah dibatalkan');
        redirect('admin/pegawai/detail_cuti/'.$id_cuti);

	}

	function validator(){
		$data['validator']= $this->Pegawai_model->getValidator();
		$data['list_puskesmas']    = $this->Master_model->getlistPuskesmas();
		//print_array($row);
		$this->load->view('admin/pegawai/validator', $data);

	}
	public function add_validator()
	{
		$pegawai = $this->input->post('id_pegawai');
		$explode = explode('-', $pegawai);
		$nama = trim($explode[0]);
		$nip = trim($explode[1]);

		$data = [
			'id_puskesmas' => $this->input->post('id_puskesmas'),
			'klaster'      => $this->input->post('klaster'),
			'usergroup'    => $this->input->post('usergroup')
		];

		$this->db->where('nip', $nip);
		$this->db->update('mst_pegawai', $data);

		$pesan =  createMessageInfo('Data validator  berhasil ditambahkan');

		redirect('admin/pegawai/validator');
	}


	function update_validator(){


		$id_pegawai = $this->input->post('id_pegawai');
		$id_puskesmas = $this->input->post('id_puskesmas');
		$klaster = $this->input->post('klaster');

		$update = [
			'id_puskesmas' => $id_puskesmas,
			'klaster' => $klaster,

		];

		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->set($update);
		$this->db->update('mst_pegawai');

		$pesan =  createMessageInfo('Data validator  berhasil diupdate');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/validator');

	}

	function delete_validator($id_pegawai=0){
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->set('usergroup', 7); //ubah ke user biasa
		$this->db->update('mst_pegawai');

		$pesan =  createMessageInfo('Data validator  berhasil dihapus');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/validator');

	}


	//untuk mncari pegawai validtor
	public function search_pegawai_validator()
	{
		$keyword = $this->input->post('keyword');

		$this->db->like('nama', $keyword);
		$this->db->or_like('nip', $keyword);
		$this->db->limit(10);
		$query = $this->db->get('mst_pegawai')->result();

		$data = [];

		foreach ($query as $row) {
			$data[] = [
				'label' => $row->nama . ' - ' . $row->nip,
				'value' => $row->id_pegawai
			];
		}

		echo json_encode($data);
	}

	public function search_pegawai()
	{
		 $keyword = $this->input->post('keyword', TRUE);

		$this->db->select('
			mst_pegawai.id_pegawai,
			mst_pegawai.nama,
			mst_pegawai.nip,
			mst_pegawai.nrk,
			mst_jabatan.nama as nama_jabatan
		');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_jabatan.id = mst_pegawai.id_jabatan', 'left');
		$this->db->like('mst_pegawai.nama', $keyword);
		$this->db->limit(10);

		$query = $this->db->get()->result();

		$data = [];

		foreach ($query as $row) {
			$data[] = [
				'label'   => $row->nama . ' - ' . $row->nip,
				'nama'    => $row->nama,
				'nip'     => $row->nip,
				'nrk'     => $row->nrk,
				'id_pegawai'     => $row->id_pegawai,
				'jabatan' => $row->nama_jabatan
			];
		}

		echo json_encode($data);
	}

	function change_status_kerja($status, $id_pegawai){
		///$status = $this->input->post('status');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->set('status_kerja', $status);
		$this->db->update('mst_pegawai');

		$pesan =  createMessageInfo('Status kerja pegawai telah diubah');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/detail_pegawai/'.$id_pegawai);
	}

	function reset_password($id_pegawai){

		$pass = 123456;
		$new_pass = md5($pass);
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->set('password', $new_pass);
		$this->db->update('mst_pegawai');
		$pesan =  createMessageInfo('Password berhasil direset');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/pegawai/detail_pegawai/'.$id_pegawai);
	}


	function insert_sisa_cuti($id_pegawai){

		$jns_hak = $this->input->post('jns_hak');
		$jumlah  = $this->input->post('qty_input');
		$sisa_akhir  = $this->input->post('sisa_akhir');



		$newData = array(
			'id_pegawai' => $id_pegawai,
			'jns_hak_cuti' => $jns_hak,
			'jns_cuti' => 0, //karena ini bukan permtongan cuti dari pegawai
			'id_cuti' => 0,
			'jumlah_hari' => $jumlah,
			'sisa_akhir' => $sisa_akhir,
			'keterangan' => 'Penambahan/pengurangan cuti'
		);



		$this->db->insert('log_cuti', $newData);


		$pesan =  createMessageInfo('Sisa Cuti  pegawai telah tambahkan');
		 $this->session->set_flashdata('message', $pesan);
		 redirect('admin/pegawai/detail_pegawai/'.$id_pegawai);




	}

	function insert_hubdis($id_pegawai, $nip){



        $tanggal    	  =  $this->input->post('tgl_hubdis');
		$jns_hukuman      =  $this->input->post('jns_hukuman');
		$kategori_hukuman =  $this->input->post('kategori_hukuman');
		$no_sk     		  =  $this->input->post('no_sk');
		$pejabat_ttd      =  $this->input->post('pejabat_ttd');
		$tmt_awal_tkd     =  $this->input->post('tmt_awal_tkd');
		$tmt_akhir_tkd     =  $this->input->post('tmt_akhir_tkd');
		$catatan     	  =  $this->input->post('catatan');


		$newData = array(
			'nip'=> $nip,
			'tgl_hubdis' => format_db($tanggal),
			'jns_hukuman' => $jns_hukuman,
			'kategori' => $kategori_hukuman,
			'no_sk'=> $no_sk,
			'pejabat_ttd' => $pejabat_ttd,
			'tgl_mulai' => format_db($tmt_awal_tkd),
			'tgl_akhir' => format_db($tmt_akhir_tkd),
			'catatan' => $catatan,
			'file_hubdis' => ''
		);

		$this->db->insert('tbl_hubdis', $newData);

		redirect('admin/pegawai/detail_pegawai/'.$id_pegawai );

        // $path 			  = $jns_dokumen = 'hubdis';

		// $dateFile 		  = date('Ymd', strtotime($tanggal));
        // $file_name  	  = $jns_dokumen.'_'.$nama_pegawai.'_'.$dateFile;
		// $namaFileDB      =  $file_name.'.pdf';


        // $uploadFile = $this->Master_model->uploadFilePDF($path, $file_name, '1000');

        // if ($uploadFile) {

        //     //$this->Pegawai_model->insertDiklat($nip, $namaFileDB);


        //     $pesan ='Data Hukuman disiplin berhasil disimpan';
        //     $this->session->set_flashdata('message', $pesan);
        //     $this->session->set_flashdata('message_status', 200);
        //     $this->session->unset_userdata('tanggal');
        //     $this->session->unset_userdata('keterangan');
        //     $this->session->unset_userdata('jns_dl');

        //    redirect('profile/my_profile' );
        // } else {

        //     redirect('profile/my_profile' );

        // }
    }




    function import_pajak(){
        $data = array(); // Buat variabel $data sebagai array

		  $date_now  = date('Ymd_Hi');
		  $file_name = $date_now;
          $path = 'pajak';

		  $upload = $this->Master_model->upload_file($file_name, $path);

		  if($upload['result'] == "success"){ // Jika proses upload sukses
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';

			$excelreader = new PHPExcel_Reader_Excel2007();
			$loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
			$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

			// Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
			$data   = array();
			$numrow = 1;
            $num    = 0;
			foreach($sheet as $row){

				if($numrow > 0){
							$nama      = $row['A'];
							$pajak      = $row['B'];
							$bpjs_tk      = $row['C'];
                            $pph21 = str_replace(",","", $pajak);
                            $tk = str_replace(",","", $bpjs_tk);
                            $id_pegawai = $this->Pegawai_model->getIDpegawaiByName($nama);
                            $updatePajak = array(

                                'id_pegawai' => $id_pegawai,
                                'pph21' => $pph21,
                                'bpjs_tk' => $tk,
                            );


							//print_array( $updatePajak);
                            if($id_pegawai  > 0){
                                $this->db->where('id_pegawai', $id_pegawai);
                              $this->db->update('gaji_pegawai', $updatePajak);

                            }


                        $numrow++; // Tambah 1 setiap kali looping



				}

				$numrow++; // Tambah 1 setiap kali looping
			}

			echo 'berhasil';

		  }else{ // Jika proses upload gagal

            echo  $upload['error'];
			#$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
			#redirect('admin/swab/error_import');
		  }

          exit;



	}
	function importPegawaiPJLP(){



			$data = array(); // Buat variabel $data sebagai array

			  $date_now  = date('Ymd_Hi');
			  $file_name = $date_now;
			  $path = 'pajak';

			  $ta = 2024;
			  $upload = $this->Master_model->upload_file($file_name, $path);

			  if($upload['result'] == "success"){ // Jika proses upload sukses
				// Load plugin PHPExcel nya
				include APPPATH.'third_party/PHPExcel/PHPExcel.php';

				$excelreader = new PHPExcel_Reader_Excel2007();
				$loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
				$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

				// Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
				$data = array();
				$numrow = 1;
				$num = 0;

				  foreach($sheet as $row){

						if($numrow > 1){

								$nama      = $row['B'];
								$jabatan      = $row['D'];
								$ID_PJLP      = $row['C'];
								$lokasi_kerja      = $row['E'];

								$data[] = array(
									'id_pjlp' => $ID_PJLP,
									'nama' => $nama,
									'jabatan' => $jabatan,
									'lokasi_kerja' => $lokasi_kerja
								);

						}

					$numrow++; // Tambah 1 setiap kali looping
				}


				#print_array($data);

				$this->db->insert_batch('tbl_pegawai_pjlp', $data);

				echo '<h3>Data BPJS TK berhasil diupdate</h3>';

			  }else{ // Jika proses upload gagal

				echo  $upload['error'];
				#$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
				#redirect('admin/swab/error_import');
			  }

			  exit;

	}
}
