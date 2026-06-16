<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Setting extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();
		$this->load->model('Admin_cuti_model', 'acm');
		$this->Auth_model->cekAuthLogin();
	}

	function index()
	{

		$this->load->view('dashboard');
	}

	function menu()
	{
		$data['title'] = 'Menu Setting';
		$data['menu'] = $this->Master_model->getListMenu($menu_type = 'P', $parent_id = 0);
		$this->load->view('admin/setting/main', $data);
	}


	function insert_menu()
	{
		$this->Master_model->insertMenu('P', 0);

		$this->session->set_flashdata('message', 'Data berhasil disimpan');
		redirect('admin/setting/menu');
	}

	function status_absensi()
	{
		$data['title'] = 'Status Absensi';
		$data['status_absensi'] = $this->Master_model->getListStatusAbsensi();
		$this->load->view('admin/setting/status_absensi', $data);
	}

	function insert_status_absensi()
	{
		$this->Master_model->insertStatusAbsensi();

		$this->session->set_flashdata('message', 'Data berhasil disimpan');
		redirect('admin/setting/status_absensi');
	}

	function update_sort()
	{
		$id_menu = $this->input->post('id_menu');
		$sort    = $this->input->post('sort');

		for ($i = 0; $i < count($id_menu); $i++) {

			$this->db->where('id_menu', $id_menu[$i]);
			$this->db->set('sort', $sort[$i]);
			$this->db->update('mst_menu');
		}

		$this->session->set_flashdata('message', 'Data berhasil diupdate');
		redirect('admin/setting/menu');
	}



	function menu_level_2($id_menu)
	{
		$data['title'] = 'SubMenu Setting';
		$data['menu'] = $this->Master_model->getListMenu($menu_type = 'C', $id_menu);
		$this->load->view('admin/setting/main', $data);
	}


	function importDataAbsensi()
	{

		$this->load->view('admin/setting/import_data');
	}

	public function import_process()
	{
		$data_import   = $this->input->post('data_import');
		$dataParse     = explode("\n", $data_import);

		#print_array($dataParse);

		#$id_puskesmas = $this->session->userdata('id_puskesmas');
		$id_puskesmas = 1;

		for ($i = 0; $i < count($dataParse); $i++) {
			$dataRow = $dataParse[$i];
			$dataRaw = preg_replace('/\s/', '', $dataRow);
			$revString = strrev($dataRaw);

			#echo $revString;
			$revtime    = substr($revString, 3, 8);  // returns "1111"
			$revdate    = substr($revString, 11, 10);  // returns "1111"
			$revPin    = substr($revString, 21, 5);
			$revStatus    = substr($revString, 1, 1);

			$pin  =  strrev($revPin);
			$date =  strrev($revdate);
			$time =  strrev($revtime);
			$status =  strrev($revStatus);

			$dateTime = $date . ' ' . $time;

			$this->Presensi_model->insertDataAbsensiImport($dateTime, $pin, $status, $id_puskesmas);
		}

		echo '<span class="text-success"> <i class="fa fa-check "></i> &nbsp; ' . $i . ' Rows </strong> has been inserted</span>';

		echo '<br><br>
				<a href="' . base_url() . 'admin/setting/importDataAbsensi">Back to home</a>';
	}




	function insert_submenu($parent_id)
	{
		$this->Master_model->insertMenu('C', $parent_id);

		$this->session->set_flashdata('message', 'Data berhasil disimpan');
		redirect('admin/setting/menu_level_2/' . $parent_id);
	}

	function hari_kerja()
	{

		//$periode_tahun = $this->session->userdata('periode_tahun');
		$periode_tahun = date('Y');
		$data['title'] = 'Hari Kerja';

		$data['hari_kerja'] = $this->Master_model->getHariKerja($periode_tahun);

		//print_array($data['hari_kerja']);
		$this->load->view('admin/setting/hari_kerja', $data);
	}

	function change_periode()
	{
		$this->session->set_userdata($this->input->post());
		redirect('admin/setting/create_initial_shift');
	}

	function create_initial_shift()
	{
		$data['shift_kerja'] = $this->Master_model->getShiftKerja();
		$data['title'] = 'Buat Inisial Shift';
		$this->load->view('admin/setting/main', $data);
	}

	function update_initial_shift()
	{
		// print_array( $this->input->post());
		// exit;

		$shift = $this->input->post('shift');
		$tgl = $this->input->post('tgl');



		for ($i = 0; $i < count($tgl); $i++) {

			$this->db->where('tgl', $tgl[$i]);
			$this->db->delete('tbl_initial_shift');

			$array = array(
				'publish' => 1,
				'tgl' => $tgl[$i],
				'shift' => $shift[$i],
			);
			$this->db->insert('tbl_initial_shift', $array);
		}

		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil disimpan</div>');
		redirect('admin/setting/create_initial_shift');
	}

	function shift_kerja()
	{
		$data['title'] = 'Shift Kerja Pegawai';
		$data['shift_kerja'] = $this->Master_model->getShiftKerja();
		$this->load->view('admin/setting/shift_kerja', $data);
	}

	function insert_shift_kerja()
	{
		$jam_masuk = $this->input->post('jam_masuk');
		$jam_pulang = $this->input->post('jam_pulang');
		$array = array(
			'nama_shift' => $this->input->post('nama_shift'),
			'kode_shift' => $this->input->post('kode_shift'),
			'jam_masuk' => $jam_masuk,
			'jam_pulang' => $jam_pulang,
			'jumlah_jam_kerja' => 0,
			'urutan' => 1,
			'status_kerja' => $this->input->post('jns_pegawai'),
			'publish' => 1,

		);
		$this->db->insert('mst_shift_kerja', $array);
		redirect('admin/setting/shift_kerja');
	}

	function ajaxChangePublish()
	{
		$id = $this->input->post('id');
		$publish = $this->input->post('publish');

		// Validasi data jika perlu
		if ($id !== null) {
			// Update ke database (ganti 'your_model' dan nama tabel/field sesuai kebutuhan)

			$this->db->where('id', $id);
			$this->db->update('mst_shift_kerja', ['publish' => $publish]);

			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
		}
	}

	public function get_shift_by_id()
	{
		$id = $this->input->post('id');
		$shift = $this->Master_model->get_by_id($id);

		echo json_encode($shift);
	}


	function update_shift_kerja()
	{

		$id = $this->input->post('id_shift');
		$data = array(
			'kode_shift' => $this->input->post('kode_shift'),
			'nama_shift' => $this->input->post('nama_shift'),
			'jam_masuk' => $this->input->post('jam_masuk'),
			'jam_pulang' => $this->input->post('jam_pulang'),
			'status_kerja' => $this->input->post('user_pengguna')
		);

		$this->db->where('id', $id);
		$this->db->update('mst_shift_kerja', $data);

		$this->session->set_flashdata('message', [
			'type' => 'success', // bisa: success, error, info, warning
			'text' => 'Shift Kerja berhasil diubah!'
		]);
		redirect('admin/setting/shift_kerja');
	}

	function delete_shift($id)
	{

		$this->db->where('id', $id);
		$this->db->delete('mst_shift_kerja');

		$this->session->set_flashdata('message', [
			'type' => 'success', // bisa: success, error, info, warning
			'text' => 'Shift Kerja berhasil dihapus!'
		]);
		redirect('admin/setting/shift_kerja');
	}




	function insert_hari_kerja()
	{
		$this->Master_model->insertHariKerja();
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil disimpan</div>');
		redirect('admin/setting/hari_kerja');
	}

	function get_hari_kerja($id)
	{

		$editData = $this->Master_model->getEditHariKerja($id);
		echo json_encode($editData);
	}


	function update_hari_kerja($id)
	{
		$this->Master_model->updatetHariKerja($id);
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil disimpan</div>');
		redirect('admin/setting/hari_kerja');
	}

	function delete_hari_kerja($id)
	{

		$this->db->where('id', $id);
		$this->db->delete('ts_hari_kerja');
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil dihapus</div>');
		redirect('admin/setting/hari_kerja');
	}


	function hari_libur()
	{
		$data['hari_libur'] = $this->Master_model->getHariLibur();
		$data['title'] = 'Hari Libur';
		$this->load->view('admin/setting/hari_libur', $data);
	}


	function insert_hari_libur()
	{
		$this->Master_model->insertHariLibur();
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil disimpan</div>');
		redirect('admin/setting/hari_libur');
	}


	function delete_hari_libur($id)
	{

		$this->db->where('id', $id);
		$this->db->delete('ts_hari_libur');
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil dihapus</div>');
		redirect('admin/setting/hari_libur');
	}



	function mesin_absensi()
	{
		$data['mesin_absensi'] = $this->Master_model->getlistMesin();
		$data['list_puskesmas'] = $this->Master_model->getlistPuskesmas();
		$data['title'] = 'Mesin Absensi';
		$this->load->view('admin/setting/mesin_absensi', $data);
	}

	function insert_mesin_absensi()
	{
		$nama_mesin  = $this->input->post('nama_mesin');
		$ip_addr     = $this->input->post('ip_address');
		$sn          = $this->input->post('sn');

		$new_data = array(
			'nama_mesin' => $nama_mesin,
			'serial_number' => $sn,
			'id_puskesmas' =>  $this->input->post('id_puskesmas'),
			'ip_address' => $ip_addr
		);

		$this->db->insert('tbl_mesin_absensi', $new_data);

		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil disimpan</div>');
		redirect('admin/setting/mesin_absensi');
	}


	function getDetMesinAbsensi($sn)
	{
		$editData = $this->Master_model->getDetMesinAbsensi($sn);
		echo json_encode($editData);
	}

	function update_mesin_absensi($sn)
	{
		$nama_mesin  = $this->input->post('nama_mesin');
		$ip_addr     = $this->input->post('ip_address');

		$new_data = array(
			'nama_mesin' => $nama_mesin,
			'serial_number' => $this->input->post('sn'),
			'id_puskesmas' =>  $this->input->post('id_puskesmas'),
			'ip_address' => $ip_addr
		);

		$this->db->where('serial_number', $sn);
		$this->db->update('tbl_mesin_absensi', $new_data);

		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil diupdate</div>');
		redirect('admin/setting/mesin_absensi');
	}



	function usergroup()
	{
		$data['title'] = 'Usergroup';
		$this->load->view('admin/setting/main', $data);
	}



	function usergroup_hak_akses($id)
	{
		$data['menu'] = $this->Master_model->getListMenu($menu_type = 'P', $parent_id = 0);
		$data['title'] = 'Hak Akses Usergroup';
		$this->load->view('admin/setting/main', $data);
	}


	function update_hak_akses($usergroup_id)
	{

		$id_menu = $this->input->post('id_menu');

		$this->db->where('usergroup_id', $usergroup_id);
		$this->db->delete('tbl_hak_akses');


		for ($i = 0; $i < count($id_menu); $i++) {
			$menuID = $id_menu[$i];

			$newData = array(
				'usergroup_id' => $usergroup_id,
				'id_menu' => $menuID,
				'akses' => 1
			);


			$this->db->insert('tbl_hak_akses', $newData);
		}


		$this->session->set_flashdata('message', 'Data berhasil diupdate');
		redirect('admin/setting/usergroup_hak_akses/' . $usergroup_id);
	}

	function jabatan()
	{
		$data['list_jabatan'] = $this->Master_model->getlistJabatan();
		$data['title'] = 'Master Jabatan';
		$this->load->view('admin/setting/main', $data);
	}

	function insert_jabatan()
	{
		$this->Master_model->insertJabatan();
		$pesan =  createMessageInfo('Data Jabatan berhasil ditambahkan');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/setting/jabatan');
	}


	function edit_jabatan()
	{
		$id = $this->input->post('id');

		$nama_jabatan   = $this->Master_model->getNamaJabatan($id);

		echo '
		<div class="modal-header">
			<h5 class="modal-title">Edit  Jabatan</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>

		<form action="' . base_url() . 'admin/setting/updateJabatan/' . $id . '" method="post">
			  <div class="modal-body">

						<div class="mb-3">
							<label class="form-label">Nama Jabatan</label>
							<input type="text" class="form-control" name="nama_jabatan" value="' . $nama_jabatan . '" required >

						</div>

					<div class="modal-footer">
						<a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
							Cancel
						</a>
						<button type="submit" class="btn btn-primary ms-auto" >
						<i class="fas fa-check"></i>
						Save Changes
						</button>
					</div>
				</form>';
	}

	function updateJabatan($id)
	{

		$nama = $this->input->post('nama_jabatan');
		$this->db->where('id', $id);
		$this->db->set('nama', $nama);
		$this->db->update('mst_jabatan');
		$pesan =  createMessageInfo('Data Jabatan berhasil diubah');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/setting/jabatan');
	}


	function  refresh_mesin($ip_address)
	{
		$cekStatusOnlineMesin = $this->Sinkron_model->cekKoneksi($ip_address);

		if ($cekStatusOnlineMesin) {
			$statusMesin = 1;
		} else {
			$statusMesin = 0;
		}


		$this->Presensi_model->updateStatusMesinAbsensi($ip_address, $statusMesin);


		redirect('admin/setting/mesin_absensi');
	}






	function delete_mesin_absensi($serial_number)
	{

		$this->db->where('serial_number', $serial_number);
		$this->db->delete('tbl_mesin_absensi');

		$pesan =  createMessageInfo('Data  Mesin absensi  berhasil dihapus');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/setting/mesin_absensi');
	}



	public function list_user($serial_number)
	{
		$data['title'] = 'Hari Kerja';
		$detail_mesin = $this->Presensi_model->detailMesin($serial_number);
		$ip_address   = $detail_mesin[0]->ip_address;
		$data['list_user']    = $this->Sinkron_model->getListUserMesin($ip_address);
		$data['detail_mesin'] = $detail_mesin;
		$data['mesin_absensi'] = $this->Master_model->getlistMesin();


		$this->load->view('mesin/list_user', $data);
	}

	function tarik_data($serial_number)
	{

		$tarikdata = $this->Sinkron_model->download_log_mesin($serial_number);

		echo $tarikdata;

		//redirect('dashboard/list_user/'.$serial_number);

	}



	function data_absensi($pin, $ip_address, $serial_number = '')
	{
		$data['absensi'] = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);
		$data['detail_mesin'] = $this->Presensi_model->detailMesin($serial_number);
		$data['title'] = 'Data Absensi';
		$this->load->view('admin/setting/data_absensi_user', $data);
	}



	function detail_user($pin, $ip_address)
	{
		$data['absensi'] = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);
		$data['title'] = 'Data Absensi';
		$this->load->view('admin/setting/data_absensi_user', $data);
	}




	public function insert_user($ip_address, $serial_number)
	{
		$pin  = $this->input->post('pin');
		$nama = $this->input->post('nama');

		$insert_user = $this->Sinkron_model->insertUser($ip_address, $pin, $nama);

		$pesan =  createMessageInfo('Data  user   berhasil disimpan');
		$this->session->set_flashdata('message', $pesan);
		redirect('admin/setting/list_user/' . $serial_number);
	}

	function benerinUser()
	{
		$sql = $this->db->get('mst_pegawai');
		$row = $sql->result();

		for ($i = 1; $i < count($row); $i++) {
			$nama = $row[$i]->nama;

			$id = $i + 1;
			$this->db->where('nama',  $nama);
			$this->db->set('id_pegawai', $id);
			$this->db->update('mst_pegawai');
		}


		echo 'selesau';
		#print_array($row);
	}
	public function delete_user($pin, $serial_number, $ip_address)
	{
		$delete_user = $this->Sinkron_model->deleteUser($pin, $ip_address);

		$pesan =  createMessageInfo('Data  user   berhasil dihapus');
		$this->session->set_flashdata('message', $pesan);
		//redirect('admin/setting/list_user/'.$serial_number);

		echo 'berhasil';
	}

	function master_gaji($id_masa_kerja = 1)
	{
		$data['title'] = 'Master Gaji';
		$data['mst_pendidikan'] = $this->Master_model->getlistPendidikan();
		$data['mst_status'] = $this->Master_model->getlistStatus();
		$data['masa_kerja'] = $this->Master_model->getMasaKerja($id_masa_kerja);
		$data['datalist_masa_kerja'] = $this->Master_model->getlistMasaKerja();
		$this->load->view('admin/setting/main', $data);
	}


	function importFileAbsensi()
	{

		$this->load->view('admin/setting/import_data');
	}


	function import_file()
	{
		$sn = $this->input->post('sn');
		$file = $_FILES['file_1']['name'];


		$this->load->library('upload');
		$file = $_FILES['file_1']['name'];
		$file_loc = $_FILES['file_1']['tmp_name'];
		$folder = "./uploads/absensi/";



		$periode = '2024-03-01';

		$lastImport = $this->Presensi_model->cekLastImportData($sn);
		if (!empty($lastImport)) {
			$tanggal = $lastImport[0]->tanggal;
			$tgl_import = format_db($tanggal);
		} else {

			$tgl_import = $periode;
		}


		$this->upload->do_upload("file_1");
		if (move_uploaded_file($file_loc, $folder . $file)) {
			$handle = fopen("./uploads/absensi/$file", "r");

			if ($handle) {
				$dataInsert = array();
				$totalRow = 0;
				while (($buffer = fgets($handle, 4096)) !== false) {
					//echo $buffer;
					//$dataParse       = explode(" ", $buffer);

					$explodData = preg_split("/[\s+ ]/u", trim($buffer));
					$pin        =   $explodData[0];
					$date       =   $explodData[1];;
					$time       =   $explodData[2];
					$status     =   $explodData[4];
					$dateTime   =   $date . ' ' . $time;

					//$dataRow		 = $dataParse[0];

					#print_array($explodData);
					// $explodData      = preg_split('/\s+/', $dataRow);
					// $pin      =   $explodData[0];
					// $date     =   $explodData[1];;
					// $time     =   $explodData[2];
					// $status   =   $explodData[4];
					// $dateTime =   $date . ' ' . $time;

					if ($date >= $tgl_import) {
						$totalRow = $totalRow + 1;
						$dataInsert[] = array(
							'tanggal' => $dateTime,
							'pin' => $pin,
							'serial_number' => $sn,
							'status' => $status,
							'method_absen' => 1
						);
					}
				}


				// $dataParse       = explode("\n", $buffer);
				// 	$dataRow		 = $dataParse[0];

				// 	print_array($dataParse);
				// 	$explodData      = preg_split('/\s+/', $dataRow);
				// 	$pin      =   $explodData[0];
				// 	$date     =   $explodData[1];;
				// 	$time     =   $explodData[2];
				// 	$status   =   $explodData[4];
				// 	$dateTime =   $date . ' ' . $time;

				// 	if ($date >= $tgl_import) {
				// 		$totalRow = $totalRow + 1;
				// 		$dataInsert[] = array(
				// 			'tanggal' => $dateTime,
				// 			'pin' => $pin,
				// 			'serial_number' => $sn,
				// 			'status' => $status,
				// 			'method_absen' => 1
				// 		);
				// 	}
				// }

				fclose($handle);
			}


			print_array($dataInsert);
			exit;
			$this->db->insert_batch('ts_absensi', $dataInsert);
			$pesan =  createMessageInfo('Data  absensi    berhasil diimport');
			$this->session->set_flashdata('message', $pesan);
			redirect('admin/setting/mesin_absensi');
			#echo $totalRow;
		} else {
			echo 'upload failed';
		}
	}



	function import_pegawai()
	{
		echo form_open_multipart(base_url() . 'admin/setting/import_process_pegawai');
		echo form_fieldset('IMPORT FILE');
		echo '
			<label>Upload file (*.xls) : </label>
			<input name="file" type="file"><br>
			<br>
			<button type="submit" class="btn btn-sm btn-info" id="clickme">
				<i class="fa fa-external-link-square"></i> &nbsp; Import
			</button>';
		echo form_fieldset_close();
		echo form_close();
	}


	public function import_process_pegawai()
	{
		$data = array(); // Buat variabel $data sebagai array

		$date_now  = date('Ymd_Hi');
		$file_name = $date_now;

		$upload = $this->Master_model->upload_file($file_name);

		if ($upload['result'] == "success") { // Jika proses upload sukses
			// Load plugin PHPExcel nya
			include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

			$excelreader = new PHPExcel_Reader_Excel2007();
			$loadexcel = $excelreader->load('uploads/' . $file_name . '.xlsx'); // Load file yang tadi diupload ke folder excel
			$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

			// Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
			$data = array();
			$numrow = 1;
			$num = 0;
			foreach ($sheet as $row) {


				if ($numrow > 2) {
					// Kita push (add) array data ke variabel data


					#print_array($row);
					$nama      = $row['B'];
					$nip      = $row['C'];
					$jabatan      = $row['G'];
					$pend     = $row['I'];
					$puskes      = $row['J'];
					$status_kawin      = $row['K'];
					$status_pajak      = $row['L'];
					$gaji_pokok     = $row['M'];
					$pengkalian      = $row['N'];
					$pph      = $row['P'];
					$bpjs      = $row['Q'];
					$bpjs_tk      = $row['R'];
					$ptkp      = $row['S'];

					if ($pend == 'D3') {
						$id_pendidikan = 4;
					} else if ($pend == 'SMA') {
						$id_pendidikan = 3;
					} else if ($pend == 'S1') {
						$id_pendidikan = 5;
					} else if ($pend == 'Profesi') {
						$id_pendidikan = 6;
					} else {
						$id_pendidikan = 1;
					}


					$nip = str_replace("oo", "", $nip);

					#$id_puskesmas = $this->Master_model->getIdPuskesmas($puskes);

					if ($nama != '') {


						// $dataInsert = array(
						// 	'nama' => $nama,
						// 	'nip'=> $nip,
						// 	'nrk' => 0,
						// 	'golongan'=> '',
						// 	'id_puskesmas' => $id_puskesmas,
						// 	'rumpun_kerja' => 'ukp',
						// 	'id_jabatan' => $this->Master_model->cekJabatan($jabatan),
						// 	'id_poli' => 1,
						// 	'tgl_masuk' => '2024-01-01',
						// 	'tmt' => '2024-01-01',
						// 	'jns_pegawai'=>'non_pns',
						// 	'jns_jam_kerja' => 'non_shift',
						// 	'password' => md5(123456),
						// 	'status_kawin' => 2,
						// 	'status_pajak' => 'K0',
						// 	'id_pendidikan' => $id_pendidikan,
						// 	'status_kerja' => 1,
						// 	'id_validator' => 1,
						// 	'kategori_masa_kerja' => 2,
						// 	'masa_kerja' => '1-1-1',
						// 	'tahun_anggaran' => 2024,
						// 	'usergroup'=> 7

						//);


						$id_pegawai = $this->Pegawai_model->cekData($nip);


						if ($pengkalian == '') {
							$pengkalian = 0;
						}

						if ($bpjs_tk == '') {
							$bpjs_tk = 0;
						}
						$dataGaji = array(
							'id_pegawai' => $id_pegawai,
							'gaji_pokok' => clear_tags($gaji_pokok),
							'pengkalian' => $pengkalian,
							'pph21' => clear_tags($pph),
							'bpjs_kes' => clear_tags($bpjs),
							'bpjs_tk' => clear_tags($bpjs_tk),
							'ptkp' => clear_tags($ptkp)
						);

						$this->db->insert('gaji_pegawai', $dataGaji);
						//print_array($dataGaji);
					}



					$numrow++; // Tambah 1 setiap kali looping



				}

				$numrow++; // Tambah 1 setiap kali looping
			}

			echo 'berhasil';
		} else { // Jika proses upload gagal

			echo  $upload['error'];
			#$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
			#redirect('admin/swab/error_import');
		}

		exit;
	}
}
