<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Presensi  extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('text');
		$this->Auth_model->cekAuthLogin();
		$this->load->model('Admin_cuti_model', 'acm');
		$this->load->model('Shift_model');
	}

	function dashboard_absensi()
	{

		$data['validator'] = $this->Pegawai_model->getValidator();


		$this->load->view('admin/presensi/dashboard_absensi', $data);
	}


	public function index()
	{
		$jns_pegawai = $this->uri->segment(4) ?? 'non_pns';

		$bulan = $this->session->userdata('periode_bulan');
		$tahun = $this->session->userdata('periode_tahun');
		$id_pj = $this->session->userdata('id_pj');

		//print_array($this->session->userdata);
		// Jika kosong → set default
		if (empty($bulan) || empty($tahun)) {
			$bulan = date('m');
			$tahun = date('Y');

			$this->session->set_userdata([
				'periode_bulan' => $bulan,
				'periode_tahun' => $tahun,
				'id_pkm'        => 1
			]);
		}
		$periode = date('Y-m', strtotime("$tahun-$bulan"));

		if ($id_pj == '') {
			$this->session->set_userdata('id_pj', $this->session->userdata('id_pegawai'));
		}


		$qry = $this->db->get_where('mst_pegawai', ['id_pegawai' => $id_pj]);
		$detValidator =  $qry->row();

		//print_array($detValidator);
		if (!empty($detValidator)) {
			$id_puskesmas = $detValidator->id_puskesmas;
			$id_klaster = $detValidator->klaster;
		} else {
			$id_puskesmas = 1;
			$id_klaster = 3;
		}



		$select = 'id_pegawai, nip, pin, nama, jns_jam_kerja, jns_pegawai';
		if ($id_puskesmas != 1) {
			//selaian puskesmas induk
			$data['pegawai'] = $this->Pegawai_model->getPegawaiByIDPuskesmas($id_puskesmas, $jns_pegawai, $select);
		} else {

			$data['pegawai'] = $this->Pegawai_model->getPegawaiByKlaster($id_puskesmas, $id_klaster, $jns_pegawai, $select);
		}

		$data['validator'] = $this->Pegawai_model->getValidator();
		$data['numAllPegawai']    = $this->Pegawai_model->countPegawaiAktif($tahun, $jns_pegawai);
		$data['absensiSesuai']    = $this->Presensi_model->countAbsenSesuai($periode, 1);
		$data['absensiBlmSesuai'] = $this->Presensi_model->countAbsenSesuai($periode);
		$this->load->view('admin/presensi/index', $data);
	}



	function ajax_search_pegawai(){
		
		$pegawai = $this->input->post('data_pegawai');
		$explode = explode("-", $pegawai);
		$nama    = $explode[0];
		$nip     = trim($explode[1]);

		$this->db->select('id_pegawai, nip, pin, nama, jns_jam_kerja, jns_pegawai');
		$qry = $this->db->get_where('mst_pegawai', ['nip'=> $nip]);
		$result = $qry->result();

		$data['pegawai'] = $result;

		$this->load->view('admin/presensi/view_ajax_search', $data);
	}


	function data_absensi()
	{

		// Ambil session
		$bulan = $this->session->userdata('periode_bulan');
		$tahun = $this->session->userdata('periode_tahun');
		$id_pkm = $this->session->userdata('id_pkm');


		$this->session->set_userdata('periode_bulan', 1);
		//$bulan =  1;

		// Jika kosong → set default
		if (empty($bulan) || empty($tahun)) {
			$bulan = date('m');
			$tahun = date('Y');

			$this->session->set_userdata([
				'periode_bulan' => $bulan,
				'periode_tahun' => $tahun,
				'id_pkm'        => 1
			]);
		}

		// Format periode
		$periode = date('Y-m', strtotime("$tahun-$bulan"));

		$id_validator = $this->session->userdata('id_pegawai');
		$thn_anggaran = 2024;
		$data['puskesmas']        = $this->Presensi_model->getListPuskesmas();
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['pegawai']  = $this->Presensi_model->getDataRekapAbsensiByValidator($periode);


		$data['validator'] = $this->Pegawai_model->getValidator();
		$data['numAllPegawai']    = $this->Pegawai_model->countPegawaiAktif($thn_anggaran, 'non_pns');
		$data['absensiSesuai']    = $this->Presensi_model->countAbsenSesuai($periode, 1);
		$data['absensiBlmSesuai'] = $this->Presensi_model->countAbsenSesuai($periode);


		$this->load->view('admin/presensi/index_v3', $data);
		//$this->load->view('admin/presensi/main', $data);
	}



	function view_absensi($id_pegawai, $pin)
	{

		//


		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');

		redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));


		$data['detail_pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		//$data['dataCuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);

		$data['cuti_pegawai'] = $this->acm->get_cuti_by_pegawai($id_pegawai, $bulan, $tahun);

		//$data['numCuti'] = $this->$this->Presensi_model->getjumlahCuti($id_pegawai, $periode);

		$this->load->view('admin/presensi/view_absensi_v3', $data);
	}




	// function view_absensi($id_pegawai, $pin){
	// 	redirect('admin/presensi/view_absensi/'.$id_pegawai.'/'.$pin);
	// 	exit;
	//     $tahun = $this->session->userdata('periode_tahun');
	//     $bulan = $this->session->userdata('periode_bulan');

	// 	$periode = $tahun . '-' . $bulan;
	//     $periode = date('Y-m', strtotime($periode));

	// 	$data['detail_pegawai']= $this->Pegawai_model->getDetailPegawai($id_pegawai);
	// 	$data['data_shift_kerja']= $this->Presensi_model->getShiftKerja();
	// 	$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
	// 	$data['dataCuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);
	// 	//$data['numCuti'] = $this->Presensi_model->getjumlahCuti($id_pegawai, $periode);

	// 	$this->load->view('admin/presensi/view_absensi', $data);
	// }




	function update_absensi_pegawai($id_pegawai, $pin)
	{
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		$periode       = $periode_tahun . '-' . $periode_bulan;
		$periode       = date('Y-m', strtotime($periode));
		$tgl           = $periode . '-01';


		$lastDate = date('t', strtotime($periode));

		for ($a = 0; $a < $lastDate; $a++) {

			$date = $a + 1;

			$tanggal = $periode . '-' . $date;
			$fullDate = format_db($tanggal);
			$dataAbsenHarian = $this->Presensi_model->cekAbsenExist($fullDate, $pin);

			if ($dataAbsenHarian == 0) {
				$this->Presensi_model->createinitialShift($pin, $fullDate);
			}
			//print_array($dataAbsenHarian);
		}



		redirect('admin/presensi/index');
	}

	function rekap_absensi()
	{
		// $periode_bulan = $this->session->userdata('periode_bulan');
		// $periode_tahun = $this->session->userdata('periode_tahun');
		// $periode       = $periode_tahun . '-' . $periode_bulan;
		// $periode       = date('Y-m', strtotime($periode));

		// $data['data_rekap']     = $this->Presensi_model->getDataRekapAbsensi($periode, 'mst_pegawai.nama');

		// $this->load->view('admin/presensi/rekap_absensi', $data);

		redirect('admin/absensi/rekap_absensi');
	}


	function set_session_validator()
	{
		$id_pj = $this->input->post('id_pj');

		$this->session->set_userdata('id_pj', $id_pj);
		return true;
	}

	function reupdate_absensi_cuti($id_cuti, $pin, $id_pegawai)
	{

		$jamKerja     = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);
		$data_cuti = 	$this->Cuti_model->getDetailCuti($id_cuti);

		$jns_cuti    = $data_cuti[0]->jns_cuti;
		$alasan_cuti = $data_cuti[0]->alasan_cuti;

		$tgl_dari     = $data_cuti[0]->tgl_dari;
		$tgl_sampai     = $data_cuti[0]->tgl_sampai;
		$hari_cuti    = $data_cuti[0]->hari_cuti;

		#print_array($data_cuti);
		if ($hari_cuti == 1) {
			$this->Presensi_model->insertAbsensiCuti($tgl_dari, $pin);
		} else {
			$list_hari = $this->Cuti_model->getListHariCuti($id_cuti);


			$selisihhari =  datediff('d', $tgl_dari, $tgl_sampai);
			//selesih hari jika cuti yang  hari dianggap 0 hari, maka dari itu harus ditambah 1 hari
			$selisihhari = $selisihhari + 1;


			if ($jamKerja == 'non_shift') {
				$dataCuti = $this->Cuti_model->getHariCuti($selisihhari, $tgl_dari);
				$hariCuti = $dataCuti[0];
				$list_hari = $dataCuti[1];
			} else {
				$list_hari = array();
				$datetime1 = date_create($tgl_dari);
				$datetime2 = date_create($tgl_sampai);
				// Calculates the difference between DateTime objects
				$interval = date_diff($datetime1, $datetime2);
				$hariCuti =  $interval->format('%a') + 1;
				$newDate      = $tgl_dari;
				for ($a = 0; $a < $hariCuti; $a++) {


					$list_hari[] = $newDate;
					$newDate = addDaysToDate($newDate, 1);
				}
			}


			// print_array($list_hari);
			// exit;
			for ($c = 0; $c < count($list_hari); $c++) {
				$tgl_cuti = $list_hari[$c];
				$this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti);
			}
		}


		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data cuti telah ditambahkan ke absensi');
		redirect('admin/presensi/absensi_raw/' . $pin . '/' . $id_pegawai);
	}

	function tarik_data_absensi()
	{
		//tarik data dari mesin absensi
		$id_pkm        = $this->session->userdata('id_pkm');
		$this->db->where('id_puskesmas', $id_pkm);
		$qry = $this->db->get('tbl_mesin_absensi');
		$row = $qry->result();

		// print_array( $row);
		// print_array( $this->session->userdata);
		// exit;
		$ip_address = $row[0]->ip_address;
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		if ($periode_bulan == '') {
			$bulan = date('m');
			$tahun = date('Y');
			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);
			$this->session->set_userdata('id_pkm', 1);
		} else {
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;
		}


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$dataPresensi = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin = '');



		for ($i = 0; $i < count($dataPresensi); $i++) {

			$DateTime = $dataPresensi[$i]['DateTime'];
			$pin      = $dataPresensi[$i]['pin'];
			$Status   = $dataPresensi[$i]['Status'];

			$periode_db = date('Y-m', strtotime($DateTime));

			#echo 'Periode DB '.$periode_db;
			if ($periode == $periode_db) {
				$this->Presensi_model->insertAbsensi($DateTime, $pin, $Status);
			}
		}
		echo 'selesai';
		#print_array($dataPresensi);
	}


	function sinkron_absensi($pin, $id_pegawai = 0)
	{

		$id_pkm        = $this->session->userdata('id_pkm');
		//$pegawai      = $this->Presensi_model->getListPegawaiKelurahan($id_pkm);
		$data_pegawai  = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		$jns_jam_kerja = $data_pegawai[0]->jns_jam_kerja;

		if ($periode_bulan == '') {
			$bulan = date('m');
			$tahun = date('Y');


			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);
			$this->session->set_userdata('id_pkm', 1);
		} else {
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;
		}


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$dataAbsensi  = $this->Presensi_model->getAbsenBulanan($pin, $periode);


		for ($a = 0; $a < count($dataAbsensi); $a++) {


			$datetime       = $dataAbsensi[$a]->tanggal;
			$status_absen   = $dataAbsensi[$a]->status;
			$id_absen       = $dataAbsensi[$a]->id;

			$explode = explode(" ", $datetime);
			$tanggal = $explode[0];
			$jam     = $explode[1];

			if ($status_absen == 0) {

				$newArray = array(
					'tanggal' => $tanggal,
					'pin' => $pin,
					'masuk' => $jam,
					'telat' => 0,
					'keterangan' => ''
				);
			} else {
				$newArray = array(
					'tanggal' => $tanggal,
					'pin' => $pin,
					'pulang' => $jam,
					'p_awal' => 0,
					'keterangan' => ''
				);
			}



			#print_array($newArray);

			$cekAbsenExist = $this->Presensi_model->cekAbsenExist($tanggal, $pin);

			#echo $tanggal.' --- '.$cekAbsenExist.'<br>';


			if ($cekAbsenExist == false) {

				$this->db->insert('tbl_absensi', $newArray);
			} else {

				// $this->db->where('id', $cekAbsenExist);
				// $this->db->set('masuk', '');
				// $this->db->set('pulang', '');
				// $this->db->update('tbl_absensi' );

				$this->db->where('pin', $pin);
				$this->db->where('tanggal', $tanggal);
				$this->db->update('tbl_absensi', $newArray);
			}
		}



		if ($id_pegawai == 0) {
			redirect('admin/presensi/index');
		} else {
			redirect('admin/presensi/lihat_absensi_pegawai/' . $id_pegawai . '/' . $pin);
		}
	}



	function set_session_periode()
	{

		$bulan = $this->input->post('bulan');
		$tahun = $this->input->post('tahun');
		//$tahun = 2025;
		$this->session->set_userdata('periode_tahun', $tahun);
		$this->session->set_userdata('periode_bulan', $bulan);

		return true;
	}



	function set_session_tahun()
	{

		$tahun = $this->input->post('tahun');
		$this->session->set_userdata('periode_tahun', $tahun);

		return true;
	}


	function set_session_puskesmas()
	{
		$id_pkm = $this->input->post('id_pkm');

		$this->session->set_userdata('id_pkm', $id_pkm);


		return true;
	}

	function hitung_telat($id_pegawai, $pin, $tanggal)
	{

		$absensiHarian  = $this->Presensi_model->getDataAbsensi($pin, $tanggal);
		if (!empty($absensiHarian)) {
			$id         = $absensiHarian[0]->id;
			$jamMasukKerja     = $absensiHarian[0]->jam_masuk;
			$jamKeluarKerja    = $absensiHarian[0]->jam_pulang;

			$absenMasuk         = $absensiHarian[0]->masuk;
			$absenPulang        = $absensiHarian[0]->pulang;
			$keterangan_absen   = $absensiHarian[0]->keterangan;

			$telat  = getHourDifference($jamMasukKerja, $absenMasuk);
			$p_awal = getHourDifference($jamKeluarKerja, $absenPulang, 'p.awal');


			$newArray = array(
				'telat' => $telat,
				'p_awal' => $p_awal,
			);

			$this->db->where('id', $id);
			$this->db->update('tbl_absensi', $newArray);
		}


		redirect('admin/presensi/lihat_absensi_pegawai/' . $id_pegawai . '/' . $pin);
	}

	function edit_id_pin($id_pegawai, $pin)
	{

		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');
		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));
		$id_mesin = $this->input->post('id_mesin');


		#$absensi= $this->Presensi_model->getRawAbensi($id_mesin, $periode);

		#print_array($absensi);


		$this->db->where('pin', $id_mesin);
		$this->db->set('pin', $pin);
		$this->db->update('ts_absensi');


		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data status absensi berhasil diupdate');
		redirect('admin/presensi/absensi_raw/' . $pin . '/' . $id_pegawai);
	}


	function print_absensi($id_pegawai)
	{

		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');

		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$data['data_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$data['dataCuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);
		//$data['numCuti'] = $this->Presensi_model->getjumlahCuti($id_pegawai, $periode);

		$this->load->view('admin/presensi/print_absensi', $data);
	}


	function ajax_get_data_rekap()
	{

		$id_pegawai = $this->input->post('id_pegawai');

		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');
		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$data['data_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$this->load->view('admin/presensi/view_ajax_rekap', $data);
	}





	function lihat_absensi_pegawai($id_pegawai, $pin)
	{

		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');

		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$data['data_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$data['dataCuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);
		//$data['numCuti'] = $this->Presensi_model->getjumlahCuti($id_pegawai, $periode);

		$this->load->view('admin/presensi/lihat_absensi', $data);
	}


	function update_shift_harian_v2($pin, $id_pegawai)
	{
		#print_array($this->input->post());
		$tgl = $this->input->post('tgl_shift');
		$shift = $this->input->post('shift');

		$tgl = format_db($tgl);

		$id_absen = $this->Presensi_model->cekAbsenExist($tgl, $pin);


		$JamKerjaShift = $this->Presensi_model->detailShiftByKode($shift);
		$jamMasuk      = $JamKerjaShift[0]->jam_masuk;
		$jamPulang     = $JamKerjaShift[0]->jam_pulang;

		$newArray = array(
			'shift' => $shift,
			'jam_masuk' => $jamMasuk,
			'jam_pulang' => $jamPulang,

		);

		#print_array($newArray);
		$this->db->where('id', $id_absen);
		$this->db->update('tbl_absensi', $newArray);



		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data shift absensi berhasil diupdate');
		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}


	function getDataPengajuanDL()
	{
		$id_pegawai   = $this->input->post('id_pegawai');
		$pin      = $this->input->post('pin');
		$tanggal      = $this->input->post('tanggal');

		$tanggal = format_db($tanggal);
		$dataDl = $this->Presensi_model->getDataPengajuanDL($id_pegawai, $tanggal);

		echo '
		<table class="table">
			<tr>
			  <th>Tanggal</th>
			  <th>Jenis DL</th>
			  <th>Keterangan</th>
			  <th>Status</th>
			  <th>Surat Tugas</th>
			  <th>Action</th>
			</tr>';

		for ($i = 0; $i < count($dataDl); $i++) {
			$id_dl = $dataDl[$i]->id;
			$tanggal = $dataDl[$i]->tanggal;
			$jns_dl = $dataDl[$i]->jns_dl;
			$keterangan = $dataDl[$i]->keterangan;
			$status = $dataDl[$i]->status;
			$surtug = $dataDl[$i]->surtug;

			if ($status == 0) {
				$flag = '<span class="badge bg-warning-subtle text-warning">Pending</span>';
			} else {
				$flag = '<span class="badge bg-success-subtle text-success">Valid</span>';
			}

			echo '	<tr>
							<td>' . format_slash($tanggal) . '</td>
							<td>' . $jns_dl . '</td>
							<td>' . $keterangan . '</td>
							<td>' . $flag . '</td>
							<td><a href="' . base_url() . 'uploads/surat_tugas/' . $surtug . '" target="_blank" class="btn-link">Lihat</a></td>
							<td><a href="' . base_url() . 'admin/presensi/setujui_pengajuan_dl/' . $id_dl . '/1/' . $pin . '/' . $id_pegawai . '" class="btn btn-sm btn-info">Setujui</a></td>
						</tr>';
		}


		echo '</table>';
	}



	function edit_shift($pin, $id_pegawai)
	{
		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));


		$data['data_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$this->load->view('admin/presensi/edit_shift', $data);
	}

	// function insert_shift_kerja(){
	// 	$id_pegawai   = $this->input->post('id_pegawai');
	// 	$tanggal   = $this->input->post('tanggal');
	// 	$kode_shift   = $this->input->post('id_shift');


	// 	$cekShift = $this->Presensi_model->cekDataShift($id_pegawai, $tanggal);
	// 	if(!empty($cekShift)){
	// 		//update
	// 		$id = $cekShift[0]->id;
	// 		$this->Presensi_model->updateShiftPegawai($id, $kode_shift);

	// 	}else{
	// 		//insert baru
	// 		$this->Presensi_model->insertShiftPegawai($id_pegawai, $tanggal, $kode_shift);

	// 	}

	// }

	function laporan_absensi()
	{
		$this->load->view('admin/presensi/laporan_absensi');
	}


	function check_ok($pin, $id_pegawai, $id_rekap)
	{
		$this->Presensi_model->updateStatusAbsensiRekap($id_rekap);

		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data status absensi berhasil diupdate');
		redirect('admin/presensi/lihat_absensi_pegawai/' . $id_pegawai . '/' . $pin);
	}

	function check_ok2()
	{
		$id_rekap = $this->input->post('id_rekap');
		$this->Presensi_model->updateStatusAbsensiRekap($id_rekap);

		echo '<strong>Success!!! </strong> Data status absensi berhasil diupdate';
	}


	function insert_user()
	{

		$this->Presensi_model->insertDataPegawai();


		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data user berhasil ditambahkan');
		redirect('admin/presensi/index');
	}

	function ajax_detail_cuti()
	{
		$id_pengajuan    = $this->input->post('id');

		$cuti =  $this->db
			->select('
       
                c.*,
                p.nama,
                mc.jenis_cuti,
                a.status as status_approval,
                a.id_pegawai_approval,
                a.role_approval
            ')
			->from('ts_pengajuan_cuti_approval a')
			->join('ts_pengajuan_cuti c', 'c.id = a.id_pengajuan_cuti')
			->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
			->join('mst_cuti mc', 'mc.id = c.jenis_cuti')
			->where('a.  ', $id_pengajuan)
			->get()
			->row();

		print_array($cuti);
	}

	function ajax_detail_absensi()
	{
		$datapost    = $this->input->post('data_post');
		$explo       = explode("/", $datapost);
		$pin         = $explo[0];
		$id_pegawai  = $explo[1];
		$tanggal     = $explo[2];

		$data['tanggal'] = $tanggal;

		$periode = date('Y-m', strtotime($tanggal));

		//$data['absensi_raw']= $this->Presensi_model->getRawAbensiPertanggal($pin, $tanggal);
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['shift_kerja'] = $this->Presensi_model->getShiftPegawai($id_pegawai, $tanggal);
		$data['detail_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['absensiHarian'] = $this->Presensi_model->getDataAbsensi($pin, $tanggal);
		$data['dinasLuar'] = $this->Presensi_model->getDataPengajuanDL($id_pegawai, $tanggal);
		$data['izinSakit'] = $this->Presensi_model->cekIzinSakit($id_pegawai, $tanggal);


		//$data['cuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);

		$data['cuti'] = $this->Cuti_model->cekCutiPegawai($tanggal, $id_pegawai);

		$this->load->view('admin/presensi/ajax_detail_absensi', $data);
	}

	function detail_absensi_harian()
	{
		$datapost    = $this->input->post('data_post');
		$explo       = explode("/", $datapost);
		$pin         = $explo[0];
		$id_pegawai  = $explo[1];
		$tanggal     = $explo[2];

		$data['tanggal'] = $tanggal;

		$periode = date('Y-m', strtotime($tanggal));

		$data['absensi_raw'] = $this->Presensi_model->getRawAbensiPertanggal($pin, $tanggal);
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['shift_kerja'] = $this->Presensi_model->getShiftPegawai($id_pegawai, $tanggal);
		$data['detail_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['absensiHarian'] = $this->Presensi_model->getDataAbsensi($pin, $tanggal);
		$data['dinasLuar'] = $this->Presensi_model->getDataPengajuanDL($id_pegawai, $tanggal);
		$data['izinSakit'] = $this->Presensi_model->cekIzinSakit($id_pegawai, $tanggal);
		$data['cuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);

		$this->load->view('admin/presensi/view_ajax_detail_absensi', $data);
	}

	function edit_data_pegawai($pin, $id_pegawai)
	{
		$data['data_pegawai'] = $this->Presensi_model->getDetPegawai($pin);
		$data['puskesmas'] = $this->Presensi_model->getListPuskesmas();

		$this->load->view('admin/presensi/edit_data_pegawai', $data);
	}

	function absensi_by_status($status = 0)
	{

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $thn_anggaran = $this->session->userdata('periode_tahun');

		$periode       = $periode_tahun . '-' . $periode_bulan;
		$periode       = date('Y-m', strtotime($periode));


		$data['pegawai']  = $this->Presensi_model->getAbsensiByStatus($periode, $status);
		$data['numAllPegawai']    = $this->Pegawai_model->countPegawaiAktif($thn_anggaran, 'non_pns');
		$data['absensiSesuai']    = $this->Presensi_model->countAbsenSesuai($periode, 1);
		$data['absensiBlmSesuai'] = $this->Presensi_model->countAbsenSesuai($periode);

		$data['puskesmas']        = $this->Presensi_model->getListPuskesmas();
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		$data['validator'] = $this->Pegawai_model->getValidator();

		$this->load->view('admin/presensi/absensi_by_status', $data);
	}

	function insert_absen_cuti($id_cuti, $id_pegawai)
	{
		$periode_bulan = $this->session->userdata('periode_bulan');
		$detail_cuti  = $this->Cuti_model->getDetailCuti($id_cuti);
		$hari_cuti    = $detail_cuti[0]->hari_cuti;
		$tgl_dari     = $detail_cuti[0]->tgl_dari;
		$alasan_cuti  = $detail_cuti[0]->alasan_cuti;
		$jns_cuti     = $detail_cuti[0]->jns_cuti;



		$nip          = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
		$pin          = substr($nip, -4);

		if ($jns_cuti == 2) {
			//cuti melahirkan
			$thn_cuti = date('Y', strtotime($tgl_dari));
			$bln_cuti = date('m', strtotime($tgl_dari));
			$tgl = date('d', strtotime($tgl_dari));
			$lastDate = date('t', strtotime($tgl_dari));
			$periode = $thn_cuti . '-' . $bln_cuti;


			if ($periode_bulan == $bln_cuti) {

				for ($i = $tgl; $i <=  $lastDate; $i++) {

					$tanggal = $periode . '-' . $i;
					$tgl_cuti = format_db($tanggal);

					$this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti);
				}
			}
		} else if ($jns_cuti == 3) {
			$qry = $this->db->get_where('tbl_detail_cuti', array('id_cuti' => $id_cuti));
			$list_hari = $qry->result();

			if (!empty($list_hari)) {
				$list_tgl_cuti = $list_hari[0]->list_tgl_cuti;
				$explodDate = explode(",", $list_tgl_cuti);

				for ($c = 0; $c < count($explodDate); $c++) {
					$tgl_cuti = $explodDate[$c];


					//echo $tgl_cuti;

					$this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti);
				}
			}
		}





		if ($hari_cuti == 1) {
			$this->Presensi_model->insertAbsensiCuti($tgl_dari, $pin, $alasan_cuti);
		} else {

			$qry = $this->db->get_where('tbl_detail_cuti', array('id_cuti' => $id_cuti));
			$list_hari = $qry->result();

			if (!empty($list_hari)) {
				$list_tgl_cuti = $list_hari[0]->list_tgl_cuti;
				$explodDate = explode(",", $list_tgl_cuti);

				for ($c = 0; $c < count($explodDate); $c++) {
					$tgl_cuti = $explodDate[$c];


					$this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti);
				}
			}
		}

		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}

	function ajax_rekap_absensi()
	{

		$datapost    = $this->input->post('data_post');
		$explo       = explode("/", $datapost);
		$pin         = $explo[0];
		$id_pegawai  = $explo[1];


		$data_pegawai  = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		$jns_jam_kerja = $data_pegawai[0]->jns_jam_kerja;

		if ($periode_bulan == '') {
			$bulan = date('m');
			$tahun = date('Y');


			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);
			$this->session->set_userdata('id_pkm', 1);
		} else {
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;
		}


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));
		$last_date = date('t', strtotime($periode));


		$totalTelat = 0;
		$totalPawal = 0;

		$numSakit = 0;
		$numIzin = 0;
		$numCuti = 0;
		$numAlpha = 0;
		$numDLP = 0;
		$numDLA = 0;
		$numDLH = 0;
		$numSakitDgnSK = 0;  // sakit dengan surat keterangan
		$tgl_now = date('Y-m-d');

		for ($d = 0; $d < $last_date; $d++) {

			$tgl = $d + 1;
			$date = $periode . '-' . $tgl;
			$fulldate = format_db($date);

			//$day = date('l', strtotime($fulldate));
			$hari = getNamahari($fulldate);

			$data_absensi = $this->Presensi_model->getDataAbsensi($pin, $fulldate);
			//print_array($data_absensi);

			if (!empty($data_absensi)) {
				$id = $data_absensi[0]->id;
				$shift = $data_absensi[0]->shift;
				$absenMasuk = $data_absensi[0]->masuk;
				$absenPulang = $data_absensi[0]->pulang;

				$jamMasukKerja     = $data_absensi[0]->jam_masuk;
				$jamKeluarKerja    = $data_absensi[0]->jam_pulang;

				$telat  = $data_absensi[0]->telat;
				$p_awal = $data_absensi[0]->p_awal;

				$hari_libur = false;
				$hariLibur  = $this->Presensi_model->cekHariLibur($fulldate);

				#print_array($hariLibur );

				if (!empty($hariLibur)) {
					$hari_libur = true;
					$telat = 0;
					$p_awal = 0;
				} else {

					if ($absenMasuk == '' && $shift != 'OFF') {
						if ($fulldate < $tgl_now) {
							$telat          = 300;
						} else {
							$telat          = 0;
						}
					} else {
						#$telat          = hitungTelat($jamMasukKerja, $absenMasuk);
						if (strpos($absenMasuk, ':') !== false) {
							$telat          = hitungTelat($jamMasukKerja, $absenMasuk);
						} else {
							$telat          = 0;
						}
					}

					if ($absenPulang == '' && $shift != 'OFF') {

						if ($fulldate < $tgl_now) {
							$p_awal         = 150;
						} else {
							$p_awal         = 0;
						}
					} else {
						if (strpos($absenPulang, ':') !== false) {
							$p_awal          = hitungPulangCepat($jamKeluarKerja, $absenPulang);
						} else {
							$p_awal          = 0;
						}
					}

					$this->db->where('id', $id);
					$this->db->set('telat', $telat);
					$this->db->set('p_awal', $p_awal);
					$this->db->update('tbl_absensi');
				}

				if ($absenMasuk == 'SAKIT') {
					$numSakit = $numSakit + 1;
				}

				if ($absenMasuk == 'IZIN') {
					$numIzin = $numIzin + 1;
				}

				if ($absenMasuk == 'DLP') {
					$numDLP = $numDLP + 1;
				}

				if ($absenMasuk == 'DLA') {
					$numDLA = $numDLA + 1;
				}

				if ($absenPulang == 'DLAK') {
					$numDLH = $numDLH + 1;
				}

				if ($absenMasuk == 'SAKIT DGN SURAT') {
					$numSakitDgnSK = $numSakitDgnSK + 1;
				}

				if ($absenMasuk == 'CUTI') {
					$numCuti = $numCuti + 1;
				}


				$numAlpha = 0;

				if ($jns_jam_kerja == 'shift') {
					$off_shift = strpos($shift, 'L-OFF');
					$sm_shift  = strpos($shift, 'SM');
					$m_shift   = strpos($shift, 'M');

					if ($off_shift !== false) {
						$telat = 0;
					}

					if ($sm_shift !== false) {
						$p_awal = 0;
					}


					if ($m_shift !== false) {
						$p_awal = 0;
					}

					$this->db->where('id', $id);
					$this->db->set('telat', $telat);
					$this->db->set('p_awal', $p_awal);
					$this->db->update('tbl_absensi');
				}

				$totalTelat = $totalTelat + $telat;
				$totalPawal = $totalPawal + $p_awal;
			}
		}

		// echo $totalPawal;

		$id_rekap = $this->Presensi_model->cekDataRekapAbsensi($id_pegawai, $periode);

		if ($id_rekap == 0) {
			$this->Presensi_model->rekapAbsensi($id_pegawai, $periode, $totalTelat, $totalPawal, $numIzin, $numSakit, $numDLP, $numDLA, $numDLH);
		} else {


			$this->Presensi_model->updateRekapAbsensi($id_rekap, $totalTelat, $totalPawal, $numIzin, $numSakit, $numDLP, $numDLA, $numDLH, $numSakitDgnSK, $numCuti);
		}


		$cuti = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);


		for ($i = 0; $i < count($cuti); $i++) {
			$hari_cuti = $cuti[$i]->hari_cuti;
			$status = $cuti[$i]->status;
			$jns_cuti = $cuti[$i]->jns_cuti;
			$id_cuti = $cuti[$i]->id;


			if ($status == 'APPROVE') {
				$cekData = $this->Presensi_model->cekDataRekapCuti($id_cuti);
				if ($cekData == 0) {
					$newArray = array(
						'id_pegawai' => $id_pegawai,
						'periode' => $periode,
						'id_cuti' => $id_cuti,
						'jns_cuti' => $jns_cuti,
						'jml_hari' => $hari_cuti
					);

					$this->db->insert('ts_rekap_cuti', $newArray);
				}
			}
		}


		// echo '<div class="flex gap-1 my-3 px-4 py-3 text-sm text-green-500 border border-green-200 rounded-md md:items-center bg-green-50 dark:bg-green-400/20 dark:border-green-500/50">
		//                <i data-lucide="check-check" class="size-3"></i> <span class="font-bold">Good!</span>  Rekap data berhasil
		//             </div>';

		echo '<div class="flex items-center gap-4 card-body">

				<ul class="flex flex-col gap-5">
					<li class="flex items-center gap-3">
						<div class="flex items-center justify-center text-red-500 bg-red-100 rounded-md size-8 dark:bg-red-500/20 shrink-0">
							<i data-lucide="clock" class="size-4"></i>
						</div>
						<h6 class="grow">Telat</h6>
						<p class="text-slate-500 dark:text-zink-200"> <i data-lucide="arrow-right" class="inline-block size-4"></i> <span class="align-middle"> </p>
							<div class="w-12 text-red-500 ltr:text-right rtl:text-left">
							' . $totalTelat . '
						</div>

					</li>
					<li class="flex items-center gap-3">
						<div class="flex items-center justify-center rounded-md size-8 text-red-500 bg-red-100 dark:bg-red-500/20 shrink-0">
							<i data-lucide="log-out" class="size-4"></i>
						</div>
						<h6 class="grow">Pulang Awal</h6>
						<p class="text-slate-500 dark:text-zink-200"> <i data-lucide="arrow-right" class="inline-block size-4"></i> <span class="align-middle"> </p>
							<div class="w-12 text-red-500 ltr:text-right rtl:text-left">
							' . $p_awal . '
						</div>


					</li>
					<li class="flex items-center gap-3">
						<div class="flex items-center justify-center text-orange-500 bg-orange-100 rounded-md size-8 dark:bg-orange-500/20 shrink-0">
							<i data-lucide="user-x-2" class="size-4"></i>
						</div>
						<h6 class="grow">Sakit</h6>
							<p class="text-slate-500 dark:text-zink-200"> <i data-lucide="arrow-right" class="inline-block size-4"></i> <span class="align-middle"> </p>

							<div class="w-12 text-orange-500 ltr:text-right rtl:text-left">
							' . $numSakit . '
						</div>

					</li>
						<li class="flex items-center gap-3">
						<div class="flex items-center justify-center rounded-md size-8 text-orange-500 bg-orange-100 dark:bg-zink-600 dark:text-zink-200 shrink-0">
							<i data-lucide="file-input" class="size-4"></i>
						</div>
						<h6 class="grow">Sakit Dengan Surat</h6>
							<p class="text-orange-500 dark:text-orange-200"> <i data-lucide="arrow-right" class="inline-block size-4"></i> <span class="align-middle"> </p>

							<div class="w-12 text-orange-500 ltr:text-right rtl:text-left">
							' . $numSakitDgnSK . '
						</div>

					</li>
					<li class="flex items-center gap-3">
						<div class="flex items-center justify-center text-custom-500 bg-custom-100 rounded-md size-8 dark:bg-custom-500/20 shrink-0">

								<i data-lucide="info" class="inline-block size-4"></i> <span class="align-middle"> </p>

						</div>
						<h6 class="grow">Izin</h6>
							<p class="text-slate-500 dark:text-zink-200"> <i data-lucide="arrow-right" class="inline-block size-4"></i> <span class="align-middle"> </p>

							<div class="w-12 text-orange-500 ltr:text-right rtl:text-left">
							' . $numIzin . '
						</div>

					</li>
					<li class="flex items-center gap-3">
						<div class="flex items-center justify-center rounded-md size-8 text-green-500 bg-green-100 dark:bg-zink-600 dark:text-zink-200 shrink-0">
							<i data-lucide="calendar-off" class="size-4"></i>
						</div>
						<h6 class="grow">Cuti</h6>
							<p class="text-slate-500 dark:text-zink-200"> <i data-lucide="arrow-right" class="inline-block size-4"></i> <span class="align-middle"> </p>

							<div class="w-12 text-green-500 ltr:text-right rtl:text-left">
							' . $numCuti . '
						</div>

					</li>

				</ul>

			</div>';
	}

	function update_data_rekap($pin, $id_pegawai, $userlevel = 'admin')
	{

		$data_pegawai  = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		$jns_jam_kerja = $data_pegawai[0]->jns_jam_kerja;

		if ($periode_bulan == '') {
			$bulan = date('m');
			$tahun = date('Y');


			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);
			$this->session->set_userdata('id_pkm', 1);
		} else {
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;
		}


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));
		$last_date = date('t', strtotime($periode));


		$totalTelat = 0;
		$totalPawal = 0;

		$numSakit = 0;
		$numIzin = 0;
		$numCuti = 0;
		$numAlpha = 0;
		$numDLP = 0;
		$numDLA = 0;
		$numDLH = 0;
		$numSakitDgnSK = 0;  // sakit dengan surat keterangan
		$tgl_now = date('Y-m-d');

		for ($d = 0; $d < $last_date; $d++) {

			$tgl = $d + 1;
			$date = $periode . '-' . $tgl;
			$fulldate = format_db($date);

			//$day = date('l', strtotime($fulldate));
			$hari = getNamahari($fulldate);

			$data_absensi = $this->Presensi_model->getDataAbsensi($pin, $fulldate);

			//print_array($data_absensi );

			if (!empty($data_absensi)) {
				$id = $data_absensi[0]->id;
				$shift = $data_absensi[0]->shift;
				$absenMasuk = $data_absensi[0]->masuk;
				$absenPulang = $data_absensi[0]->pulang;

				$jamMasukKerja     = $data_absensi[0]->jam_masuk;
				$jamKeluarKerja    = $data_absensi[0]->jam_pulang;

				$telat  = $data_absensi[0]->telat;
				$p_awal = $data_absensi[0]->p_awal;

				$hari_libur = false;
				$hariLibur  = $this->Presensi_model->cekHariLibur($fulldate);

				#print_array($hariLibur );

				if (!empty($hariLibur)) {
					$hari_libur = true;
					$telat = 0;
					$p_awal = 0;
				} else {

					if ($absenMasuk == '' && $shift != 'OFF') {
						if ($fulldate < $tgl_now) {
							$telat          = 300;
						} else {
							$telat          = 0;
						}
					} else {
						#$telat          = hitungTelat($jamMasukKerja, $absenMasuk);
						if (strpos($absenMasuk, ':') !== false) {
							$telat          = hitungTelat($jamMasukKerja, $absenMasuk);
						} else {
							$telat          = 0;
						}


						//echo $fulldate.' - '.$telat .'<br>';

					}

					if ($absenPulang == '' && $shift != 'OFF') {

						if ($fulldate < $tgl_now) {
							$p_awal         = 150;
						} else {
							$p_awal         = 0;
						}
					} else {
						if (strpos($absenPulang, ':') !== false) {
							$p_awal          = hitungPulangCepat($jamKeluarKerja, $absenPulang);
						} else {
							$p_awal          = 0;
						}
					}

					$this->db->where('id', $id);
					$this->db->set('telat', $telat);
					$this->db->set('p_awal', $p_awal);
					$this->db->update('tbl_absensi');
				}

				if ($absenMasuk == 'SAKIT') {
					$numSakit = $numSakit + 1;
				}

				if ($absenMasuk == 'IZIN') {
					$numIzin = $numIzin + 1;
				}

				if ($absenMasuk == 'DLP') {
					$numDLP = $numDLP + 1;
				}

				if ($absenMasuk == 'DLA') {
					$numDLA = $numDLA + 1;
				}

				if ($absenPulang == 'DLAK') {
					$numDLH = $numDLH + 1;
				}

				if ($absenMasuk == 'SAKIT  DGN SURAT') {
					$numSakitDgnSK = $numSakitDgnSK + 1;
				}

				if ($absenMasuk == 'CUTI') {
					$numCuti = $numCuti + 1;
				}


				$numAlpha = 0;

				if ($jns_jam_kerja == 'shift') {
					$off_shift = strpos($shift, 'L-OFF');
					$sm_shift  = strpos($shift, 'SM');
					$m_shift   = strpos($shift, 'M');

					if ($off_shift !== false) {
						$telat = 0;
					}

					if ($sm_shift !== false) {
						$p_awal = 0;
					}


					if ($m_shift !== false) {
						$p_awal = 0;
					}

					$this->db->where('id', $id);
					$this->db->set('telat', $telat);
					$this->db->set('p_awal', $p_awal);
					$this->db->update('tbl_absensi');
				}

				$totalTelat = $totalTelat + $telat;
				$totalPawal = $totalPawal + $p_awal;
			}
		}



		$id_rekap = $this->Presensi_model->cekDataRekapAbsensi($id_pegawai, $periode);

		if ($id_rekap == 0) {
			$this->Presensi_model->rekapAbsensi($id_pegawai, $periode, $totalTelat, $totalPawal, $numIzin, $numSakit, $numDLP, $numDLA, $numDLH);
		} else {


			$this->Presensi_model->updateRekapAbsensi($id_rekap, $totalTelat, $totalPawal, $numIzin, $numSakit, $numDLP, $numDLA, $numDLH, $numSakitDgnSK, $numCuti);
		}



		$cuti = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);


		for ($i = 0; $i < count($cuti); $i++) {
			$hari_cuti = $cuti[$i]->hari_cuti;
			$status = $cuti[$i]->status;
			$jns_cuti = $cuti[$i]->jns_cuti;
			$id_cuti = $cuti[$i]->id;


			if ($status == 'APPROVE') {
				$cekData = $this->Presensi_model->cekDataRekapCuti($id_cuti);
				if ($cekData == 0) {
					$newArray = array(
						'id_pegawai' => $id_pegawai,
						'periode' => $periode,
						'id_cuti' => $id_cuti,
						'jns_cuti' => $jns_cuti,
						'jml_hari' => $hari_cuti
					);

					$this->db->insert('ts_rekap_cuti', $newArray);
				}
			}
		}


		$this->session->set_flashdata('message', ' Data berhasil direkap');
		//redirect('admin/presensi/view_absensi/'.$id_pegawai.'/'.$pin);


		if ($userlevel == 'user') {
			redirect('absensi/view_absensi');
		} else {
			redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
		}
	}



	function update_data_pegawai($pin, $id_pegawai)
	{
		$this->Presensi_model->updateDataPegawai($id_pegawai);
		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data berhasil update');
		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}


	function insert_absen_manual($pin, $tgl_absensi_edit)
	{

		$jam_masuk     = $this->input->post('jam_masuk');
		$jam_pulang    = $this->input->post('jam_pulang');
		$id_pegawai    = $this->input->post('id_pegawai');
		//$tgl_absensi_edit    = $this->input->post('tgl_absensi_edit');

		$jam_masuk = str_replace(".", ":", $jam_masuk);
		$jam_pulang = str_replace(".", ":", $jam_pulang);

		if ($tgl_absensi_edit == '') {
			$this->session->set_flashdata('message', 'gagal');
			redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
		} else {
			$tanggal = format_db($tgl_absensi_edit);
			$cekAbsenExist = $this->Presensi_model->cekAbsenExist($tanggal, $pin);
			if ($cekAbsenExist == false) {

				$newArray = array(
					'tanggal' => $tanggal,
					'pin' => $pin,
					'masuk' => $jam_masuk,
					'pulang' => $jam_pulang,
					'telat' => 0,
					'p_awal' => 0,
					'keterangan' => ''
				);

				$this->db->insert('tbl_absensi', $newArray);
			} else {
				if ($jam_masuk != '') {

					if ($jam_pulang != '') {
						//isi jam pulang dan jam masuk
						$newArray = array(
							'tanggal' => $tanggal,
							'pin' => $pin,
							'masuk' => $jam_masuk,
							'pulang' => $jam_pulang,
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					} else {
						//isi jam masuk aja
						$newArray = array(
							'tanggal' => $tanggal,
							'pin' => $pin,
							'masuk' => $jam_masuk,
							'telat' => 0,
							'keterangan' => ''
						);
					}
				} else {

					$newArray = array(
						'tanggal' => $tanggal,
						'pin' => $pin,
						'pulang' => $jam_pulang,
						'p_awal' => 0,
						'keterangan' => ''
					);
				}

				$this->db->where('pin', $pin);
				$this->db->where('tanggal', $tanggal);
				$this->db->update('tbl_absensi', $newArray);
			}
		}


		$this->session->set_flashdata('message', 'success');
		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
		//redirect('admin/presensi/index_v2');

	}


	function upload_izin_sakit($id, $pin, $tgl, $jns_absensi)
	{

		$ket = '';
		$this->Presensi_model->insertAbsensiIzinSakit($pin, $tgl, $jns_absensi, $ket);

		redirect('admin/presensi/index_v2');
	}

	function insert_absen_ketidakhadiran_v2()
	{
		//print_array($this->input->post());

		$pin                = $this->input->post('pin');
		$id_pegawai        = $this->input->post('id_pegawai');

		$tanggal        = $this->input->post('tgl_absensi');
		$jns_absensi    = $this->input->post('jns_absensi');
		$jns_izin    = $this->input->post('jns_izin');
		$ket            = $this->input->post('keterangan');

		$absenDL = strpos($jns_absensi, 'DL');
		$tgl     = format_db($tanggal);


		if ($absenDL === false) {

			//insert data ke table tbl_absensi
			$this->Presensi_model->deletePengajuanIzinSakit($id_pegawai, $tgl);
			$this->Presensi_model->insertAbsensiIzinSakit($pin, $tgl, $jns_absensi, $jns_izin, $ket);

			//insert data ke table pengajuan_izin_sakit
			$this->Presensi_model->insertPengajuanIzinSakit($id_pegawai, $tgl, $jns_absensi, $ket);
		} else {

			if ($jns_absensi == 'DL-AKHIR') {
				$jns = 'DLAK';
			} else if ($jns_absensi == 'DL-AWAL') {
				$jns = 'DLA';
			} else {
				$jns = 'DLP';
			}



			#echo $jns;
			$this->Presensi_model->insertAbsensiDL($pin, $tgl, $jns, $ket);
			#exit;

			$this->db->where('id_pegawai', $id_pegawai);
			$this->db->where('tanggal', $tanggal);
			$this->db->delete('pengajuan_dinas_luar');

			$newarray = array(
				'id_pegawai' => $id_pegawai,
				'tanggal ' => $tgl,
				'jns_dl' => $jns,
				'surtug' => '',
				'status' => 1,
				'keterangan' => $this->input->post('keterangan')
			);


			$this->db->insert('pengajuan_dinas_luar', $newarray);
		}

		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
		// 		echo '  <div class="px-4 py-3 text-sm text-green-500 border border-transparent rounded-md bg-green-50 dark:bg-green-400/20">
		//                     <span class="font-bold">Success! </span> Data berhasil disimpan
		//                 </div>
		//                 ';


	}


	function insert_absen_ketidakhadiran($pin, $id_pegawai)
	{
		#print_array($this->input->post());


		$tanggal        = $this->input->post('tgl_absensi');
		$jns_absensi    = $this->input->post('jns_absensi');
		$ket            = $this->input->post('keterangan');


		$absenDL = strpos($jns_absensi, 'DL');
		$tgl     = format_db($tanggal);

		if ($absenDL === false) {

			$this->Presensi_model->insertAbsensiIzinSakit($pin, $tgl, $jns_absensi, $ket);

			$this->Presensi_model->deletePengajuanIzinSakit($id_pegawai, $tgl);
			$this->Presensi_model->insertPengajuanIzinSakit($id_pegawai, $tgl, $jns_absensi, $ket);
		} else {

			if ($jns_absensi == 'DL-AKHIR') {
				$jns = 'DLAK';
			} else if ($jns_absensi == 'DL-AWAL') {
				$jns = 'DLA';
			} else {
				$jns = 'DLP';
			}



			#echo $jns;
			$this->Presensi_model->insertAbsensiDL($pin, $tgl, $jns, $ket);
			#exit;

			$this->db->where('id_pegawai', $id_pegawai);
			$this->db->where('tanggal', $tanggal);
			$this->db->delete('pengajuan_dinas_luar');

			$newarray = array(
				'id_pegawai' => $id_pegawai,
				'tanggal ' => $tgl,
				'jns_dl' => $jns,
				'surtug' => '',
				'status' => 1,
				'keterangan' => $this->input->post('keterangan')
			);


			$this->db->insert('pengajuan_dinas_luar', $newarray);
		}



		$this->session->set_flashdata('message', 'Data Berhasil disimpan');
		return redirect('admin/presensi/lihat_absensi_pegawai/' . $id_pegawai . '/' . $pin);
	}


	function ajaxSetujuiIzin()
	{
		$id_izin = $this->input->post('id');
		$pin = $this->input->post('pin');

		$status_acc = 1;

		$qry = $this->db->get_where('pengajuan_izin_sakit', array('id' => $id_izin));
		$izinSakit = $qry->result();


		if (!$izinSakit) {
			echo json_encode(['status' => false, 'message' => 'Data pengajuan izin/sakit tidak ditemukan.']);
			return;
		}


		$tanggal 	 = $izinSakit[0]->tanggal;
		$jenis_absen = $izinSakit[0]->jenis_absen;
		$id_pegawai  = $izinSakit[0]->id_pegawai;
		$ket         = $izinSakit[0]->keterangan;


		if ($status_acc == 1) {
			//pengajuan di ACC
			$this->Presensi_model->insertAbsensiIzinSakit($pin, $tanggal, $jenis_absen, $ket);
			$this->db->where('id', $id_izin);
			$this->db->set('status', 1);
			$this->db->update('pengajuan_izin_sakit');
			echo json_encode(['status' => true]);
		} else {
			$this->db->where('id', $id_izin);
			$this->db->set('status', 0);
			$this->db->update('pengajuan_izin_sakit');
			echo json_encode(['status' => false, 'message' => 'Gagal menyetujui pengajuan.']);
		}
	}


	function ajaxDeleteIzin()
	{
		$id_izin = $this->input->post('id');
		$this->db->where('id', $id_izin);
		$this->db->delete('pengajuan_izin_sakit');
		echo json_encode(['status' => true]);
	}

	function ajaxDeleteDl()
	{
		$id_dl = $this->input->post('id');
		$this->db->where('id', $id_dl);
		$this->db->delete('pengajuan_dinas_luar');
		echo json_encode(['status' => true]);
	}


	function ajaxSetujuiDL()
	{
		$id_dl = $this->input->post('id');
		$pin = $this->input->post('pin');

		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id_dl));
		$dinasLuar = $qry->result();

		$jns_dl = $dinasLuar[0]->jns_dl;
		$keterangan = $dinasLuar[0]->keterangan;
		$tanggal = $dinasLuar[0]->tanggal;


		$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

		$this->db->where('id', $id_dl);
		$this->db->set('status', 1);
		$this->db->update('pengajuan_dinas_luar');
		echo json_encode(['status' => true]);
	}




	function insertAbsenPengajuanDL($id, $pin, $id_pegawai)
	{
		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
		$dinasLuar = $qry->result();

		$jns_dl = $dinasLuar[0]->jns_dl;
		$keterangan = $dinasLuar[0]->keterangan;
		$tanggal = $dinasLuar[0]->tanggal;


		$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

		$this->db->where('id', $id);
		$this->db->set('status', 1);

		$this->db->update('pengajuan_dinas_luar');

		$this->session->set_flashdata('message', ' Data shift berhasil disimpan');
		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}



	function ajaxAccDL()
	{

		$id         = $this->input->post('id');
		$pin        = $this->input->post('pin');
		$status_acc = 1;

		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
		$dinasLuar = $qry->result();

		$jns_dl = $dinasLuar[0]->jns_dl;
		$keterangan = $dinasLuar[0]->keterangan;
		$tanggal = $dinasLuar[0]->tanggal;


		if ($status_acc == 1) {
			$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

			$this->db->where('id', $id);
			$this->db->set('status', 1);
		} else {
			$this->db->where('id', $id);
			$this->db->set('status', 2);
		}

		$this->db->update('pengajuan_dinas_luar');

		echo '  <div class="alert alert-success">
                    <span class="font-bold">Success! </span> Dinas Luar telah disetujui
                </div>
                ';
	}

	function setujui_pengajuan_dl2($id, $status_acc,  $pin)
	{
		//acc pengajuan DL dari halaman index



		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
		$dinasLuar = $qry->result();

		$jns_dl = $dinasLuar[0]->jns_dl;
		$keterangan = $dinasLuar[0]->keterangan;
		$tanggal = $dinasLuar[0]->tanggal;


		if ($status_acc == 1) {
			$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

			$this->db->where('id', $id);
			$this->db->set('status', 1);
		} else {
			$this->db->where('id', $id);
			$this->db->set('status', 2);
		}

		$this->db->update('pengajuan_dinas_luar');
		redirect('admin/presensi/index');
		#redirect('admin/presensi/index');
	}


	function setujui_pengajuan_dl($id, $status_acc,  $pin, $id_pegawai)
	{


		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
		$dinasLuar = $qry->result();

		$jns_dl = $dinasLuar[0]->jns_dl;
		$keterangan = $dinasLuar[0]->keterangan;
		$tanggal = $dinasLuar[0]->tanggal;

		if ($status_acc == 1) {
			$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

			$this->db->where('id', $id);
			$this->db->set('status', 1);
		} else {
			$this->db->where('id', $id);
			$this->db->set('status', 2);
		}

		$this->db->update('pengajuan_dinas_luar');
		redirect('admin/presensi/absensi_raw/' . $pin . '/' . $id_pegawai);
		#redirect('admin/presensi/index');
	}


	function absensi_raw($pin, $id_pegawai)
	{

		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$data['data_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['absensi'] = $this->Presensi_model->getRawAbensi($pin, $periode);
		$data['pengajuan_dinas_luar'] = $this->Presensi_model->getDataPengajuanDLPerbulan($id_pegawai, $periode);
		$data['data_cuti'] = $this->Presensi_model->getDataCutiPegawai($id_pegawai);
		$this->load->view('admin/presensi/view_absensi_raw', $data);
	}

	// function sinkron_data($pin, $id_pegawai){

	//     $this->Presensi_model->insertDataAbsen($pin);
	// 	$this->session->set_flashdata('message','<strong>Success!!! </strong> Data berhasil disinkron');
	// 	redirect('admin/presensi/absensi_raw/'.$pin.'/'.$id_pegawai);

	// }


	function getDataCutiPegawai()
	{

		$pin = $this->input->post('pin');
		$id_pegawai_absen = $this->input->post('id_pegawai');


		$id_pegawai = $this->Cuti_model->getIdPegawaiByPIN($pin);


		$data['data_cuti'] = $this->Cuti_model->cekCutiPegawai($id_pegawai);
		$data['pin'] = $pin;
		$data['id_pegawai'] = $id_pegawai_absen;

		$this->load->view('admin/presensi/data_cuti_pegawai', $data);
	}



	function ajaxGetCutiPertanggal()
	{
		$tanggal      = $this->input->post('tanggal');
		$id_puskesmas = $this->session->userdata('id_puskesmas');

		$numPegawaiNonPNS = $this->Pegawai_model->numPegawaiPerPuskesmas($id_puskesmas);
		$jumlahNonPNS = count($numPegawaiNonPNS);

		$PegawaiPNS = $this->Cuti_model->getDataPegawaiPNSPerpuskesmas($id_puskesmas);
		$jumlahPNS = count($PegawaiPNS);

		$totalPegawai = $jumlahNonPNS + $jumlahPNS;


		$data_cuti = $getCuti = $this->Cuti_model->getCutiPertanggal($tanggal, $id_puskesmas);
		$numCuti = count($data_cuti);

		$persen = round(($numCuti / $totalPegawai) * 100);

		$hari = getNamahari($tanggal);
		echo '  <h2> ' . $hari . ', ' . format_full($tanggal) . '</h2>


		<table class="table table-bordered text-center">
			<tr>
			  <th>Jumlah Pegawai</th>
			  <th>Pegawai Cuti</th>
			  <th>Persentase</th>
			</tr>
			<tr>
		     	 <td><strong>' . $totalPegawai . '</strong></td>
				 <td><strong>' . count($data_cuti) . '</strong></td>
				 <td><strong>' . $persen . ' %</strong>  </td>
			</tr>

		</table>';

		echo '<table class="table table-hover">';
		for ($i = 0; $i < count($data_cuti); $i++) {
			$status = $data_cuti[$i]->status;
			if ($status == 1) {
				$flag_status = '<div class="badge bg-success">Disetujui</div>';
			} else {
				$flag_status = '<div class="badge bg-warning">Pending</div>';
			}

			echo '
				<tr>
				<td>' . ($i + 1) . '</td>
				<td>
				<a href="' . base_url() . 'admin/cuti/detail_cuti/' . $data_cuti[$i]->id_cuti . '" class="text-dark">
					<strong>' . $data_cuti[$i]->nama . '</strong> <br> ' . $data_cuti[$i]->alasan_cuti . ' <br>
					<small>' . format_view($data_cuti[$i]->tgl_dari) . ' &nbsp;  s/d &nbsp; ' . format_view($data_cuti[$i]->tgl_sampai) . '</small> <br>
					' . $flag_status . '
					</a>
				</td>
				<td><strong>' . $data_cuti[$i]->hari_cuti . ' Hari</strong> </td>
			</tr>

			';
		}
		echo '</table>';
	}


	function send_data_cuti($pin, $id_pegawai, $id_cuti)
	{
		$data_cuti = 	$this->Cuti_model->getDataDetailCuti($id_cuti);
		$list_hari = 	$this->Cuti_model->getListHariCuti($id_cuti);

		$jns_cuti    = $data_cuti[0]->jns_cuti;
		$alasan_cuti = $data_cuti[0]->alasan_cuti;


		for ($i = 0; $i < count($list_hari); $i++) {

			$newArray[] = array(
				'id_pegawai' => $id_pegawai,
				'tanggal' => $list_hari[$i]->tanggal,
				'jns_cuti' => $jns_cuti,
				'keterangan' => $alasan_cuti
			);
		}




		$this->Presensi_model->insertDataCuti($newArray);
		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data berhasil disimpan');
		redirect('admin/presensi/lihat_absensi/' . $pin . '/' . $id_pegawai);
	}


	function data_rekap($pin, $id_pegawai)
	{

		$data['data_pegawai'] = $this->Presensi_model->getDetPegawai($pin);
		$data['rekap_absensi'] = $this->Presensi_model->getDataRekapAbsensiPegawai($id_pegawai);
		$this->load->view('admin/presensi/data_rekap', $data);
	}
	function ubah_status_absen()
	{
		$id = $this->input->post('id');
		$this->Presensi_model->EditRawAbsen($id);
	}

	function delete_cuti($pin, $id_pegawai, $id_cuti)
	{
		$this->Presensi_model->deleteAbsenCuti($id_cuti);
		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data cuti berhasil dihapus');
		redirect('admin/presensi/absensi_raw/' . $pin . '/' . $id_pegawai);
	}

	function delete_absensi_cuti($pin, $id_pegawai, $id)
	{

		$newArray = array(
			'masuk' => "",
			'pulang' => "",
			'telat' => 300,
			'p_awal' => 150
		);

		$this->db->where('id', $id);
		$this->db->update('tbl_absensi', $newArray);


		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}

	function delete_absensi()
	{

		$id = $this->input->post('id_absen');
		$status = $this->input->post('jenis_absen');


		if ($status == 'in') {
			//absen masuk

			$this->db->where('id', $id);
			$this->db->set('jam_masuk', null);
			$this->db->update('tbl_kehadiran_harian');
		} else {
			//absen pulang
			$this->db->where('id', $id);
			$this->db->set('jam_pulang', null);
			$this->db->update('tbl_kehadiran_harian');
		}

		echo 'Data absensi berhasil dihapus';
	}

	function delete_absensi_harian($id, $pin, $id_pegawai)
	{

		$this->db->where('id', $id);
		$this->db->delete('tbl_absensi');

		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data absensi berhasil dihapus');
		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}

	

	function delete_absensi_raw()
	{
		$id = $this->input->post('id');

		$this->db->where('id', $id);
		$qry = $this->db->get('ts_absensi');
		$row = $qry->result();

		$pin = $row[0]->pin;

		$tanggal = $row[0]->tanggal;
		$status  = $row[0]->status;


		$tgl = format_db($tanggal);
		if ($status == 0) {
			//absen masuk

			$this->db->where('pin', $pin);
			$this->db->where('tanggal', $tgl);
			$this->db->set('masuk', "");
			$this->db->update('tbl_absensi');
		} else {
			//absen pulang
			$this->db->where('pin', $pin);
			$this->db->where('tanggal', $tgl);
			$this->db->set('pulang', "");
			$this->db->update('tbl_absensi');
		}

		$this->Presensi_model->deleteRawAbsensi($id);
		echo '<span class="badge bg-light text-muted">DELETED</span>';
	}

	function generate_shift()
	{
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');

		if ($periode_bulan == '') {
			$bulan = date('m');
			$tahun = date('Y');
		} else {
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;
		}


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));


		$datapost    = $this->input->post('data_post');
		$explo       = explode("/", $datapost);
		$pin         = $explo[0];
		$id_pegawai  = $explo[1];


		$detailPegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);

		$shift = $detailPegawai[0]->jns_jam_kerja;
		$lastDate = date('t', strtotime($periode)) + 1;
		if ($shift == 'non_shift') {


			for ($t = 1; $t < $lastDate; $t++) {
				$tanggal = $periode . '-' . $t;
				$formatDate = date('Y-m-d', strtotime($tanggal));
				$day = date('D', strtotime($tanggal));

				$init_shift = $this->Presensi_model->getDataInitialShift($t);
				if (!empty($init_shift)) {
					$shift = $init_shift[0]->shift;
				} else {
					$shift = 'OFF';
				}

				// if($day=='Sat' || $day=='Sun'){
				// 	$shift = 'OFF';
				// }else if($day=='Fri'){
				// 	$shift = 'REG-JUM';
				// }else{
				// 	$shift = 'REG';
				// }


				#$this->Presensi_model->deleteShiftPegawai($id_pegawai, $tanggal);
				$insert = $this->Presensi_model->insertShiftPegawai($pin, $formatDate, $shift);
			}
		} else {

			for ($t = 1; $t < $lastDate; $t++) {
				$tanggal = $periode . '-' . $t;
				$formatDate = date('Y-m-d', strtotime($tanggal));
				$day = date('D', strtotime($tanggal));



				$absenMasuk = $this->Presensi_model->getAbsenMasuk($pin, $tanggal);
				$absenPulang = $this->Presensi_model->getAbsenPulang($pin, $tanggal);

				if ($absenMasuk == '' && $absenPulang == '') {
					$shiftKerja = 'OFF';
				}

				if ($absenMasuk != '' && $absenPulang == '') {

					$xplod = explode(":", $absenMasuk);
					$jam = $xplod[0];
					if ($jam < 9) {
						$shiftKerja = 'PSM';
					} else if ($jam > 12 && $jam < 15) {
						$shiftKerja = 'SM';
					} else if ($jam >= 15 && $jam < 18) {
						$shiftKerja = 'SM-RUS';
					} else {
						$shiftKerja = 'M';
					}
				}


				if ($absenMasuk != '' && $absenPulang != '') {

					$xplod = explode(":", $absenMasuk);
					$jam = $xplod[0];

					$xplod2 = explode(":", $absenPulang);
					$jam2 = $xplod2[0];

					if ($jam < 9) {
						//P atau PS
						if ($jam2 < 17) {
							//pagi
							$shiftKerja = 'P';
						} else {
							$shiftKerja = 'PS';
						}
					} else if ($jam > 13 && $jam < 18) {
						$shiftKerja = 'S';
					} else {
						$shiftKerja = 'M';
					}
				}

				if ($absenMasuk == '' && $absenPulang != '') {
					$shiftKerja = 'L-OFF';
				}

				#$this->Presensi_model->deleteShiftPegawai($id_pegawai, $tanggal);
				$insert = $this->Presensi_model->insertShiftPegawai($pin, $formatDate, $shiftKerja);
			}
		}

		$pesan =  createMessageInfo('Data shift  berhasil digenerate');
		#$this->session->set_flashdata('message', $pesan);

		#echo $pesan;

		redirect('admin/presensi/lihat_absensi_pegawai/' . $id_pegawai . '/' . $pin);
	}

	function delete_absensi_detail()
	{
		$id = $this->input->post('id_absen');

		$this->db->where('id', $id);
		$this->db->delete('tbl_absensi');


		echo '<div class="relative p-3 pr-12 text-sm text-green-500 border border-transparent rounded-md bg-green-50 dark:bg-green-400/20">
                <button class="absolute top-0 bottom-0 right-0 p-3 text-green-200 transition hover:text-green-500 dark:text-green-400/50 dark:hover:text-green-500"  onclick="this.parentElement.style.display=\'none\';">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x" class="lucide lucide-x h-5"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button>
                <span class="font-bold">Success</span>  Data pegawai telah berhasil dihapus.
            </div>';
	}

	function list_absensi($pin, $id_pegawai)
	{

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');

		if ($periode_bulan == '') {
			$bulan = date('m');
			$tahun = date('Y');
		} else {
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;
		}


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));


		$data['detail_pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$data['datalist'] = $this->Presensi_model->getlistTblAbsensi($periode, $pin);
		$data['pegawai'] = $this->Pegawai_model->getListPegawai('non_pns', '2024');
		$this->load->view('admin/presensi/datalist_absensi_detail', $data);
	}

	function edit_shift_kerja()
	{
		$tanggal      = $this->input->post('tanggal');
		$list_id    = $this->input->post('list_id');
		$id_pegawai   = $this->input->post('id_pegawai');
		$kode_shift   = $this->input->post('kode_shift');


		$data['tanggal'] = $tanggal;
		$data['id_pegawai'] = $id_pegawai;
		$data['list_id'] = $list_id;
		$data['kode_shift'] = $kode_shift;
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();

		$this->load->view('admin/presensi/list_edit_shift', $data);
	}


	function update_shift_pegawai()
	{

		$pin         = $this->input->post('pin');
		$tanggal     = $this->input->post('tanggal');
		$shift_kerja = $this->input->post('shift');

		$tanggal     = format_db($tanggal);
		$dataAbsensi = $this->Presensi_model->getDataAbsensiHarian($tanggal, $pin);


		$JamKerjaShift = $this->Presensi_model->detailShiftByKode($shift_kerja);
		$jamMasuk      = $JamKerjaShift[0]->jam_masuk;
		$jamPulang     = $JamKerjaShift[0]->jam_pulang;

		if (!empty($dataAbsensi)) {

			$id_absen = $dataAbsensi[0]->id;

			$dataUpdate = array(
				'shift' => $shift_kerja,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang
			);


			$this->db->where('id', $id_absen);
			$this->db->update('tbl_absensi', $dataUpdate);
		} else {
			$newArray = array(
				'pin' => $pin,
				'tanggal' => $tanggal,
				'shift' => $shift_kerja,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
				'masuk' => '',
				'pulang' => '',
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => ''
			);


			$this->db->insert('tbl_absensi', $newArray);
		}
	}



	function update_shift_v2($pin, $id_pegawai)
	{
		$tanggal   = $this->input->post('tanggal');
		$shift_kerja = $this->input->post('shift');

		$tanggal = format_db($tanggal);
		$dataAbsensi = $this->Presensi_model->getDataAbsensiHarian($tanggal, $pin);


		$JamKerjaShift = $this->Presensi_model->detailShiftByKode($shift_kerja);
		$jamMasuk      = $JamKerjaShift[0]->jam_masuk;
		$jamPulang     = $JamKerjaShift[0]->jam_pulang;

		if (!empty($dataAbsensi)) {

			$id_absen = $dataAbsensi[0]->id;

			$dataUpdate = array(
				'shift' => $shift_kerja,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang
			);


			$this->db->where('id', $id_absen);
			$this->db->update('tbl_absensi', $dataUpdate);
		} else {
			$newArray = array(
				'pin' => $pin,
				'tanggal' => $tanggal,
				'shift' => $shift_kerja,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
				'masuk' => '',
				'pulang' => '',
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => ''
			);


			$this->db->insert('tbl_absensi', $newArray);
		}


		$this->session->set_flashdata('message', ' Data shift berhasil disimpan');
		redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
	}

	function updateShiftBulanan($id_pegawai, $pin, $periode,  $shift_kerja = 'non_shift', $userlevel = 'admin')
	{



		$lastDate = date('t', strtotime($periode));
		for ($t = 0; $t < $lastDate; $t++) {
			$date = $t + 1;
			$tanggal = $periode . '-' . $date;
			$formatDate = date('Y-m-d', strtotime($tanggal));

			if ($shift_kerja == 'non_shift') {
				//$shift = $this->Shift_model->getShiftPerhari('2227', $formatDate);

				if (date('N', strtotime($formatDate)) == 6 || date('N', strtotime($formatDate)) == 7) {
					$shift = 'OFF';
				} else {
					if (date('N', strtotime($formatDate)) == 5) {
						$shift = 'REG-JUM';
					} else {
						$shift = 'REG';
					}
				}
			} else {
				$shift = $this->Shift_model->getShiftPerhari($pin, $formatDate);
			}






			$id_absen = $this->Presensi_model->cekAbsenExist($formatDate, $pin, 'id');

			if ($id_absen == 0) {
				//belum ada

				$this->Presensi_model->insertShiftPegawai($pin, $formatDate, $shift);
			} else {
				//sudah ada
				$this->Presensi_model->updateShiftPegawai($pin, $formatDate, $shift, $id_absen);
			}

			//print_array($datashift);




		}


		//redirect('admin/presensi/view_absensi/'.$id_pegawai.'/'.$pin);

		if ($userlevel == 'user') {
			redirect('absensi/view_absensi');
		} else {
			redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
		}
	}

	// function update_shift_pegawai($pin, $id_pegawai) {
	// 	$tgl         = $this->input->post('tanggal');
	// 	$shift_kerja = $this->input->post('shift');

	// 	for($i=0; $i < count($tgl) ; $i++) {
	// 		$tanggal = $tgl[$i];
	// 		$shift   = $shift_kerja[$i];

	// 		$dataAbsensi = $this->Presensi_model->getDataAbsensiHarian($tanggal, $pin);

	// 		if(!empty($dataAbsensi)) {

	// 			$id_absen = $dataAbsensi[0]->id;
	// 			$shift_db = $dataAbsensi[0]->shift;

	// 			if($shift_db != $shift){
	// 				//klo shift yang sudah ada ga sama editan

	// 				$JamKerjaShift = $this->Presensi_model->detailShiftByKode($shift);
	// 				$jamMasuk      = $JamKerjaShift[0]->jam_masuk;
	// 				$jamPulang     = $JamKerjaShift[0]->jam_pulang;


	// 				$dataUpdate = array(
	// 					'shift'=> $shift,
	// 					'jam_masuk' => $jamMasuk,
	// 					'jam_pulang'=> $jamPulang
	// 				);

	// 				$this->db->where('id', $id_absen);
	// 				$this->db->update('tbl_absensi', $dataUpdate);


	// 			}
	// 		}


	// 	}



	// 	$this->session->set_flashdata('message',' Data shift berhasil disimpan');
	// 	redirect('admin/presensi/lihat_absensi_pegawai/'.$id_pegawai.'/'.$pin);

	// }


	function delete_shift($pin, $id_pegawai)
	{
		//hapus semua shift dalam 1 bulan


		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');


		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$this->Presensi_model->deleteDataShift($id_pegawai, $periode);

		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data shift telah dihapus');
		redirect('admin/presensi/lihat_absensi/' . $pin . '/' . $id_pegawai);
	}


	function update_rekap_absensi()
	{
		$id_pegawai   = $this->input->post('id_pegawai');
		$data_post   = $this->input->post('data_post');

		$tahun      = $this->session->userdata('periode_tahun');
		$bulan      = $this->session->userdata('periode_bulan');
		$periode 	= $tahun . '-' . $bulan;
		$periode    = date('Y-m', strtotime($periode));

		$cekRekap = $this->Presensi_model->cekDataRekapAbsensi($id_pegawai, $periode);
		if ($cekRekap == 0) {
			//belum ada
			$this->Presensi_model->insertDataRekapAbsensi($id_pegawai, $periode, $data_post);
		} else {
			//udah ada
			$this->Presensi_model->updateDataRekapAbsensi($cekRekap, $data_post);
		}

		return true;
	}

	function delete_absensi_izin_sakit($id, $pin, $id_pegawai)
	{

		$this->Presensi_model->deleteAbsenIzinSakit($id);
		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data berhasil dihapus');
		redirect('admin/presensi/lihat_absensi/' . $pin . '/' . $id_pegawai);
	}



	function delete_absensi_dl($pin, $id_pegawai, $tgl)
	{

		$tanggal = format_db($tgl);

		$dataDl = $this->Presensi_model->getDataPengajuanDL($id_pegawai, $tanggal);
		$id = $dataDl[0]->id;
		$this->Presensi_model->deleteAbsenDL($id);

		$cekAbsenExist = $this->Presensi_model->cekAbsenExist($tanggal, $pin);
		$id_absen = $cekAbsenExist;
		$newArray = array(
			'masuk' => '',
			'pulang' => '',
			'telat' => 300,
			'p_awal' => 150,
			'keterangan' => ''
		);

		$this->db->where('id', $id_absen);
		$this->db->update('tbl_absensi', $newArray);

		$this->session->set_flashdata('message', '<strong>Success!!! </strong> Data berhasil dihapus');
		redirect('admin/presensi/lihat_absensi_pegawai/' . $id_pegawai . '/' . $pin);
	}


	function DataRekapAbsensi($order_by = 'telat')
	{


		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');

		$periode 	= $periode_tahun . '-' . $periode_bulan;
		$periode    = date('Y-m', strtotime($periode));

		$thn_anggaran = 2024;


		$id_validator = $this->session->userdata('id_pegawai');
		$id_pj_sess = $this->session->userdata('id_pj');


		if ($id_pj_sess != '') {
			$id_validator = $id_pj_sess;
		}


		$data['validator'] = $this->Pegawai_model->getValidator();
		$data['absensiSesuai'] = $this->Presensi_model->countAbsenSesuai($periode, 1);
		$data['absensiBlmSesuai'] = $this->Presensi_model->countAbsenSesuai($periode);
		//$data['pegawai']  = $this->Pegawai_model->getListPegawaiByValidator($id_validator, $thn_anggaran);

		$data['pegawai']  = $this->Pegawai_model->getPegawaiforListingTKD($thn_anggaran);
		$this->load->view('admin/presensi/data_rekap_absensi', $data);
	}

	function lihat_data_rekapan_absensi()
	{
		$thn_anggaran = 2024;
		//$data['pegawai']  = $this->Pegawai_model->getPegawaiforListingTKD($thn_anggaran, 15);
		$id_validator = $this->session->userdata('id_pegawai');
		$id_pj_sess = $this->session->userdata('id_pj');


		if ($id_pj_sess != '') {
			$id_validator = $id_pj_sess;
		}

		$data['pegawai']  = $this->Pegawai_model->getListPegawaiByValidator($id_validator, $thn_anggaran);
		$this->load->view('admin/presensi/lihat_data_rekapan_absensi', $data);
	}

	function importDataAbsensi()
	{
		$this->load->view('admin/presensi/import_absensi');
	}




	function lihat_data_absensi_mesin()
	{

		$pin   = $this->input->post('pin');
		$id_pegawai   = $this->input->post('id_pegawai');
		$tahun     = $this->session->userdata('periode_tahun');
		$bulan     = $this->session->userdata('periode_bulan');

		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$detailPegawai = $this->Presensi_model->getDetPegawai($pin);
		$id_puskesmas  = $detailPegawai[0]->id_puskesmas;
		$shift  = $detailPegawai[0]->shift;


		if ($id_puskesmas == 6) {
			//puskesmas kel Kalibaru
			if ($shift == 1) {
				//klo dia shift berarti bidan RB
				$ket = 'RB';
			} else {
				// pegawai reguler di puskesmas
				$ket = '';
			}
		} else {
			$ket = '';
		}


		$ip_address = $this->Presensi_model->getIpaddressByPuskesmas($id_puskesmas, $ket);
		$getDataAbsensi = $this->Sinkron_model->getDataPresensi($ip_address,  $pin);



		echo '  <table  class="table table-sm table-center text-nowrap table-bordered">

				<tr>
					<th>Tanggal</th>
					<th>Status</th>
				</tr>';



		for ($i = 0; $i < count($getDataAbsensi); $i++) {
			$stringAbsen  = strip_tags($getDataAbsensi[$i]);

			$tgl = substr($stringAbsen, 4, 10);
			$thn_bulan = date('Y-m', strtotime($tgl));

			if ($thn_bulan == $periode) {

				$jamAbsen = substr($stringAbsen, 15, 8);
				$status = substr($stringAbsen, 24, 1);
				$datetime = $tgl . ' &nbsp;&nbsp;&nbsp;&nbsp;' . $jamAbsen;

				$date = date('d', strtotime($tgl));
				if ($date % 2 == 0) {
					$class = "class='bg-info'";
				} else {
					$class = "";
				}


				echo '<tr ' . $class . '>

					   <td>' . $datetime . '</td>
					   <td>' . $status . '</td>
					</tr>';
			}
		}


		echo '</table>';
	}


	function pengajuan_dl()
	{

		$data['pengajuan'] = $this->Absensi_model->getDataPengajuanDinasLuar();

		$this->load->view('admin/presensi/pengajuan_dinas_luar_pegawai', $data);
	}

	function ajax_detail_pengajuan_dl()
	{
		$id = $this->input->post('id');
		$pengajuan = $this->Absensi_model->getDetailPengajuanDL($id);
		echo json_encode(['status' => true, 'data' => $pengajuan]);
	}

	function pengajuan_izin()
	{
		$jenis = $this->session->userdata('jenis');
		$jns_absen = $jenis ?? 'IZIN';
		$data['pengajuan'] = $this->Absensi_model->getDataPengjuanIzinSakit($jns_absen);

		$this->load->view('admin/presensi/pengajuan_izin', $data);
	}


	function filter_pengajuan_izin()
	{
		$this->session->set_userdata($this->input->post());
		redirect('admin/presensi/pengajuan_izin');
	}

	function change_date_dl()
	{
		//print_array($this->input->post());

		$id = $this->input->post('id_dl');
		$new_date = $this->input->post('new_date');

		$this->db->where('id', $id);
		$this->db->set('tanggal', $new_date);
		$this->db->update('pengajuan_dinas_luar');
		$tanggal = format_view($new_date);


		echo json_encode(['status' => true, 'new_date' => $tanggal]);
	}




	function ajax_tarik_absensi()
	{


		$nip          =  $this->session->userdata('nip');
		$id_pegawai   =  $this->session->userdata('id_pegawai');
		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');

		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$dataPegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);

		$id_puskesmas  = $dataPegawai[0]->id_puskesmas;
		$jns_jam_kerja = $dataPegawai[0]->jns_jam_kerja;

		if ($id_puskesmas == 6) {
			if ($jns_jam_kerja == 'shift') {
				//ambil data dari mesin sbasensi RB kalibaru
				$id_puskesmas  = 12;
			}
		}



		$pin    = substr($nip, -4);
		$ip_address = $this->Master_model->getIpAddress($id_puskesmas);
		$dataAbsen = $this->Master_model->tarikData($ip_address, $bulan, $pin);


		print_array($dataAbsen);
		exit;
		if ($jns_jam_kerja == 'shift') {
			for ($i = 0; $i < count($dataAbsen); $i++) {
				$dabsen =  $dataAbsen[$i];
				$exlodeData = explode("/", $dabsen);

				$dateTime = $exlodeData[0];
				$status   = $exlodeData[1];

				$date = format_db($dateTime);
				$jam  = date('H:i:s', strtotime($dateTime));

				$cekAbsensiExist = $this->Presensi_model->cekAbsenExist($date, $pin);

				//echo $dateTime;
				$explode = explode(":", $jam);
				$hour    = $explode[0];


				if ($cekAbsensiExist > 0) {
					//udah ada row

					if ($status == 0) {
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('masuk', $jam);
						$this->db->update('tbl_absensi');
					} else {
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('pulang', $jam);
						$this->db->update('tbl_absensi');
					}
				} else {
					//belum ada
					if ($status == 0) {
						//absen masuk

						if ($hour < 9) {
							$shiftKerja = 'P';
							$jamMasuk = '07:30';
						} else if ($hour > 12 && $hour < 15) {
							$shiftKerja = 'SM';
							$jamMasuk = '14:00';
						} else {
							$shiftKerja = 'M';
							$jamMasuk = '21:00';
						}



						$newArray = array(
							'tanggal' => $date,
							'pin' => $pin,
							'shift' => $shiftKerja,
							'jam_masuk' => $jamMasuk,
							'jam_pulang' => '00:00:00',
							'masuk' => $jam,
							'pulang' => '',
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					} else {

						$shiftKerja = 'L-OFF';
						$jamPulang  = '07:30';

						$newArray = array(
							'tanggal' => $date,
							'pin' => $pin,
							'shift' => $shiftKerja,
							'jam_masuk' => '00:00:00',
							'jam_pulang' => $jamPulang,
							'masuk' => '',
							'pulang' => $jam,
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					}

					$this->db->insert('tbl_absensi', $newArray);
				}
			}
		} else {

			for ($i = 0; $i < count($dataAbsen); $i++) {
				$dabsen =  $dataAbsen[$i];
				$exlodeData = explode("/", $dabsen);

				$dateTime = $exlodeData[0];
				$status   = $exlodeData[1];

				$date = format_db($dateTime);
				$jam  = date('H:i:s', strtotime($dateTime));

				$explode = explode(":", $jam);
				$hour    = $explode[0];


				$cekAbsensiExist = $this->Presensi_model->cekAbsenExist($date, $pin);

				if ($hour > 5 && $hour < 10) {
					//$status = 'Masuk';

					if ($cekAbsensiExist > 0) {
						//udah ada row
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('masuk', $jam);
						$this->db->update('tbl_absensi');
					} else {
						//belum ada
						$this->Presensi_model->createinitialShift2($pin, $date, $jam, '');
					}
				} else {
					//$status = 'Pulang';

					if ($cekAbsensiExist > 0) {
						//udah ada row
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('pulang', $jam);
						$this->db->update('tbl_absensi');
					} else {
						//belum ada
						$this->Presensi_model->createinitialShift($pin, $date, '', $jam);
					}
				}
			}
		}



		echo 'Data absensi berhasil disinkron';
	}
	function ajax_get_raw_absensi()
	{
		$pin = $this->input->post('pin');
		$ip_address = $this->input->post('ip_address');

		$absensi    = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);


		echo '<table class="table table-sm table-striped">
					<tr>
						<th>PIN</th>
						<th>DateTime</th>
						<th>status</th>
					</tr>';

		rsort($absensi);
		for ($a = 0; $a < count($absensi); $a++) {
			echo ' <tr>
									<td>' . $absensi[$a]['pin'] . '</td>
									<td>' . $absensi[$a]['DateTime'] . '</td>
									<td>' . $absensi[$a]['Status'] . '</td>
								</tr>';
		}


		echo '</table>';
	}


	function sinkron_absen_import($id_pegawai, $pin)
	{
		$dataAbsenImport = $this->Presensi_model->getAbsenBulanan($pin, '2025-10');


		foreach ($dataAbsenImport as $abs) {

			$dateTime = $abs->tanggal;
			$status   = $abs->status;

			$date = format_db($dateTime);
			$jam  = date('H:i:s', strtotime($dateTime));

			$explode = explode(":", $jam);
			$hour    = $explode[0];


			if ($status == 0) {
				//$this->db->where('id', $cekAbsensiExist);

				$this->db->where('tanggal', $date);
				$this->db->where('pin', $pin);
				$this->db->set('masuk', $jam);
				$this->db->update('tbl_absensi');
			} else {
				//$this->db->where('id', $cekAbsensiExist);

				$this->db->where('tanggal', $date);
				$this->db->where('pin', $pin);
				$this->db->set('pulang', $jam);
				$this->db->update('tbl_absensi');
			}




			echo 'Berhasil disinkron untuk tanggal ' . $date . '<br/>';
		}
	}


	function tarik_absensi($pin, $id_pegawai, $userlevel = 'admin')
	{

		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');

		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));

		$dataPegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);

		$id_puskesmas  = $dataPegawai[0]->id_puskesmas;
		$jns_jam_kerja = $dataPegawai[0]->jns_jam_kerja;

		if ($id_puskesmas == 6) {
			if ($jns_jam_kerja == 'shift') {
				//ambil data dari mesin sbasensi RB kalibaru
				$id_puskesmas  = 12;
			}
		}


		$pinBaru = $pin;

		if (substr($pin, 0, 1) === "0") {
			$pinBaru = "1" . $pin;
		}

		if ($id_pegawai == '1169') {
			$pinBaru = '12100';
		}

		$ip_address = $this->Master_model->getIpAddress($id_puskesmas);
		$dataAbsen = $this->Master_model->tarikData($ip_address, $bulan, $pinBaru);


		//		print_array($dataAbsen);exit;




		if ($jns_jam_kerja == 'shift') {

			//print_array($dataAbsen);


			for ($i = 0; $i < count($dataAbsen); $i++) {
				$dabsen =  $dataAbsen[$i];
				$exlodeData = explode("/", $dabsen);

				$dateTime = $exlodeData[0];
				$status   = $exlodeData[1];

				$date = format_db($dateTime);
				$jam  = date('H:i:s', strtotime($dateTime));

				$cekAbsensiExist = $this->Presensi_model->cekAbsenExist($date, $pin);


				//echo $cekAbsensiExist.'<br>';
				//echo $dateTime;
				$explode = explode(":", $jam);
				$hour    = $explode[0];


				if ($cekAbsensiExist > 0) {
					//udah ada row

					if ($status == 0) {
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('masuk', $jam);
						$this->db->update('tbl_absensi');
					} else {
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('pulang', $jam);
						$this->db->update('tbl_absensi');
					}
				} else {
					//belum ada
					if ($status == 0) {
						//absen masuk

						if ($hour < 9) {
							$shiftKerja = 'P';
							$jamMasuk = '07:30';
						} else if ($hour > 12 && $hour < 15) {
							$shiftKerja = 'SM';
							$jamMasuk = '14:00';
						} else {
							$shiftKerja = 'M';
							$jamMasuk = '21:00';
						}



						$newArray = array(
							'tanggal' => $date,
							'pin' => $pin,
							'shift' => $shiftKerja,
							'jam_masuk' => $jamMasuk,
							'jam_pulang' => '00:00:00',
							'masuk' => $jam,
							'pulang' => '',
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					} else {

						$shiftKerja = 'L-OFF';
						$jamPulang  = '07:30';

						$newArray = array(
							'tanggal' => $date,
							'pin' => $pin,
							'shift' => $shiftKerja,
							'jam_masuk' => '00:00:00',
							'jam_pulang' => $jamPulang,
							'masuk' => '',
							'pulang' => $jam,
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					}

					$this->db->insert('tbl_absensi', $newArray);
				}
			} //close for

			$dataAbsenImport = $this->Presensi_model->getAbsenBulanan($pin, $periode);


			foreach ($dataAbsenImport as $abs) {

				$dateTime = $abs->tanggal;
				$status   = $abs->status;

				$date = format_db($dateTime);
				$jam  = date('H:i:s', strtotime($dateTime));

				$explode = explode(":", $jam);
				$hour    = $explode[0];

				$cekAbsensiExist = $this->Presensi_model->cekAbsenExist($date, $pin, 'id');


				if ($cekAbsensiExist > 0) {
					//udah ada row

					if ($status == 0) {
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('masuk', $jam);
						$this->db->update('tbl_absensi');
					} else {
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('pulang', $jam);
						$this->db->update('tbl_absensi');
					}
				} else {
					//belum ada
					if ($status == 0) {
						//absen masuk

						if ($hour < 9) {
							$shiftKerja = 'P';
							$jamMasuk = '07:30';
						} else if ($hour > 12 && $hour < 15) {
							$shiftKerja = 'SM';
							$jamMasuk = '14:00';
						} else {
							$shiftKerja = 'M';
							$jamMasuk = '21:00';
						}



						$newArray = array(
							'tanggal' => $date,
							'pin' => $pin,
							'shift' => $shiftKerja,
							'jam_masuk' => $jamMasuk,
							'jam_pulang' => '00:00:00',
							'masuk' => $jam,
							'pulang' => '',
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					} else {

						$shiftKerja = 'L-OFF';
						$jamPulang  = '07:30';

						$newArray = array(
							'tanggal' => $date,
							'pin' => $pin,
							'shift' => $shiftKerja,
							'jam_masuk' => '00:00:00',
							'jam_pulang' => $jamPulang,
							'masuk' => '',
							'pulang' => $jam,
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => ''
						);
					}

					$this->db->insert('tbl_absensi', $newArray);
				}
			}
		} else {


			$dataAbsenImport = $this->Presensi_model->getAbsenBulanan($pin, $periode);



			foreach ($dataAbsenImport as $abs) {

				$dateTime = $abs->tanggal;
				$status   = $abs->status;

				$date = format_db($dateTime);
				$jam  = date('H:i:s', strtotime($dateTime));

				$explode = explode(":", $jam);
				$hour    = $explode[0];

				$cekAbsensiExist = $this->Presensi_model->cekAbsenExist($date, $pin, 'id');

				if ($hour > 5 && $hour < 10) {

					if ($cekAbsensiExist > 0) {
						//udah ada row
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('masuk', $jam);
						$this->db->update('tbl_absensi');
					} else {
						//belum ada
						$this->Presensi_model->createinitialShift2($pin, $date, $jam, '');
					}
				} else {
					//$status = 'Pulang';


					if ($cekAbsensiExist > 0) {
						//udah ada row
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('pulang', $jam);
						$this->db->update('tbl_absensi');
					} else {
						//belum ada
						$this->Presensi_model->createinitialShift($pin, $date, '', $jam);
					}
				}
			}

			//untuk yang jam kerja non SHIFT
			for ($i = 0; $i < count($dataAbsen); $i++) {
				$dabsen =  $dataAbsen[$i];
				$exlodeData = explode("/", $dabsen);

				$dateTime = $exlodeData[0];
				$status   = $exlodeData[1];

				$date = format_db($dateTime);
				$jam  = date('H:i:s', strtotime($dateTime));

				$explode = explode(":", $jam);
				$hour    = $explode[0];


				$cekAbsensiExist = $this->Presensi_model->cekAbsenExist($date, $pin, 'id');

				if ($hour > 5 && $hour < 10) {
					//$status = 'Masuk';

					//$shift_kerja = $this->Presensi_model->cekAbsenExist($date, $pin, 'shift');

					if ($cekAbsensiExist > 0) {
						//udah ada row
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('masuk', $jam);
						$this->db->update('tbl_absensi');
					} else {
						//belum ada
						$this->Presensi_model->createinitialShift2($pin, $date, $jam, '');
					}
				} else {
					//$status = 'Pulang';


					if ($cekAbsensiExist > 0) {
						//udah ada row
						$this->db->where('id', $cekAbsensiExist);
						$this->db->set('pulang', $jam);
						$this->db->update('tbl_absensi');
					} else {
						//belum ada
						$this->Presensi_model->createinitialShift($pin, $date, '', $jam);
					}
				}
			}
		}


		$this->session->set_flashdata('message', 'Data absensi berhasil disinkron');
		// $usergroup = $this->session->userdata('usergroup');
		// if($usergroup < 3){

		// 	redirect('admin/presensi/view_absensi/'. $id_pegawai.'/'.$pin);
		// }else{
		// 	redirect('absensi/view_absensi');

		// }




		if ($userlevel == 'user') {
			redirect('absensi/view_absensi');
		} else {
			redirect('admin/presensi/view_absensi/' . $id_pegawai . '/' . $pin);
		}
	}



	function sendJsonData($url, $data)
	{
		// Inisialisasi cURL
		$ch = curl_init($url);

		// Konversi data PHP array menjadi format JSON
		$jsonData = json_encode($data);

		// Set opsi untuk cURL
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Ambil hasil sebagai string
		curl_setopt($ch, CURLOPT_POST, true); // Metode POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Data JSON yang dikirim
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json', // Tipe konten JSON
			'Content-Length: ' . strlen($jsonData) // Panjang konten
		));

		// Eksekusi permintaan dan ambil hasilnya
		$response = curl_exec($ch);

		// Periksa jika terjadi kesalahan
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}

		// Tutup koneksi cURL
		curl_close($ch);

		// Kembalikan respons dari API
		return $response;
	}


	// function update_absensi(){


	// 	$tahun     = $this->session->userdata('periode_tahun');
	// 	$bulan     = $this->session->userdata('periode_bulan');

	// 	$periode = $tahun.'-'.$bulan;
	// 	$periode = date('Y-m', strtotime($periode));

	// 	$pin   = $this->input->post('pin');
	// 	$id_pegawai   = $this->input->post('id_pegawai');

	// 	$detailPegawai = $this->Presensi_model->getDetPegawai($pin);
	// 	$id_puskesmas  = $detailPegawai[0]->id_puskesmas;
	// 	$shift  = $detailPegawai[0]->shift;


	// 	if($id_puskesmas==6){
	// 		//puskesmas kel Kalibaru
	// 		if($shift==1){
	// 			//klo dia shift berarti bidan RB
	// 			$ket = 'RB';
	// 		}else{
	// 			// pegawai reguler di puskesmas
	// 			$ket = '';
	// 		}
	// 	}else{
	// 		$ket = '';
	// 	}


	// 	$ip_address = $this->Presensi_model->getIpaddressByPuskesmas($id_puskesmas, $ket);

	// 	$getDataAbsensi = $this->Sinkron_model->getDataPresensi($ip_address,  $pin);
	// 	#print_array($getDataAbsensi);

	// 	for ($i=0; $i < count($getDataAbsensi); $i++) {
	// 		$stringAbsen  = strip_tags($getDataAbsensi[$i]);

	// 		$tgl = substr($stringAbsen,4, 10);
	// 		$thn_bulan = date('Y-m', strtotime($tgl));

	// 		if($thn_bulan== $periode){

	// 			$jamAbsen = substr($stringAbsen,15, 8);
	// 			$status = substr($stringAbsen,24, 1);
	// 			$datetime = $tgl.' '.$jamAbsen;

	// 			$this->Presensi_model->insertAbsensi($datetime, $pin, $status);
	// 		}


	// 	}

	// 	#echo $ip_address ;
	// 	echo '<div class="alert alert-success">Data Absensi berhasil ditarik dari mesin absensi</div> <br>

	// 	<a href="'.base_url().'admin/presensi/lihat_absensi/'.$pin.'/'.$id_pegawai.'" class="btn btn-info">Close</a>';

	// 	#$this->session->set_flashdata('message','<strong>Success!!! </strong> Data berhasil dihapus');
	// 	#redirect('admin/presensi/lihat_absensi/'.$pin.'/'.$id_pegawai);

	// }



	function clear_duplicate($tanggal, $pin, $id_pegawai)
	{
		$tanggal  = format_db($tanggal);
		$sql = "SELECT * FROM ts_absensi WHERE tanggal like '$tanggal%' AND pin = '$pin' ORDER BY tanggal ASC";
		$qry = $this->db->query($sql);
		$row = $qry->result();


		for ($i = 0; $i < count($row); $i++) {
			$tgl_absen = $row[$i]->tanggal;
			$id = $row[$i]->id;


			$checkData = $this->Presensi_model->cekAbsensi($tgl_absen, $pin);


			if ($checkData > 1) {
				$this->Presensi_model->deleteRawAbsensi($id);
			}
			echo $checkData . '<br>';
		}

		redirect('admin/presensi/absensi_raw/' . $pin . '/' . $id_pegawai);
	}

	function pengajuan_dinas_luar_pegawai()
	{

		$id_pegawai    = $this->session->userdata('id_pegawai');
		$data['pengajuan_dinas_luar'] = $this->Presensi_model->pengajuanDinasLuarPegawai($id_pegawai);
		$this->load->view('dashboard/pengajuan_dinas_luar_pegawai', $data);
		#print_array($data['pengajuanDL'] );
	}

	function ajax_detail_dl()
	{
		$id   =  $this->input->post('id');

		$sql = "SELECT a.*, b.nama, b.id_validator, b.nip
		FROM pengajuan_dinas_luar a LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		WHERE id = $id";
		$qry = $this->db->query($sql);

		$data['pengajuan_dinas_luar'] = $qry->result();

		$this->load->view('dashboard/detail_dinas_luar', $data);
	}



	public function import_absensi()
	{
		$this->load->view('admin/presensi/import_absensi');
	}

	public function import_absensi_process()
	{
		$date_now = date("Ymd_Hi");
		$file_name = $date_now;

		$path = "absensi";

		$this->load->library("upload"); // Load librari upload

		$config["upload_path"] = "./uploads/" . $path . "/";
		$config["allowed_types"] = "txt";
		$config["max_size"] = "2048";
		$config["overwrite"] = true;
		$config["file_name"] = $file_name;

		$this->upload->initialize($config); // Load konfigurasi uploadnya
		if ($this->upload->do_upload("absensi_file")) {
			// Lakukan upload dan Cek jika proses upload berhasil
			$upload = true;
		} else {
			$upload = false;
		}

		if (!$upload) {
			echo $this->upload->display_errors();
		} else {
			$upload_data = $this->upload->data();
			$file_path = $upload_data["full_path"];
			$insert_data = [];
			$handle = fopen($file_path, "r");
			if ($handle) {
				while (($line = fgets($handle)) !== false) {
					$data = explode("\t", trim($line));

					$user_id = $data[0];
					$datetime = $data[1];
					$status = $data[2];


					$tanggal = date("Y-m-d", strtotime($datetime));
					$jam = date("H:i:s", strtotime($datetime));

					$insert_data[] = [
						"pin" => $user_id,
						"tanggal" => $datetime,
						"status" => $status,
					];

					// $this->db->insert('ts_import_absensi', $insert_data);
				}
				fclose($handle);


				// print_array($insert_data);
				$this->db->insert_batch("ts_import_absensi", $insert_data);
				//print_array($insert_data);
				echo "Import berhasil.";
			} else {
				echo "Gagal membaca file.";
			}
		}
	}


	function purgeDataAbsensi($id_pegawai, $pin = 0)
	{


		$tahun     = $this->session->userdata('periode_tahun');
		$bulan     = $this->session->userdata('periode_bulan');

		$periode = $tahun . '-' . $bulan;
		$periode = date('Y-m', strtotime($periode));



		$sql = "DELETE FROM tbl_absensi WHERE tanggal like '$periode%' AND pin = $pin";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		//	print_array($row);

		// 		$sql = "SELECT * FROM tbl_absensi WHERE tanggal like '$tanggal%' AND pin = $pin ORDER BY tanggal ASC";
		// 		$qry = $this->db->query($sql);
		// 		$row = $qry->result();


		// 		$tglAwal = $row[0]->tanggal;

		// 	//	echo 'ini tanggal awal = ' . $tglAwal;
		// 		for ($i = 0; $i < count($row); $i++) {

		// 			$tanggalAbsen = $row[$i]->tanggal;
		// 			$id = $row[$i]->id;


		// 		//	echo '<br>ini tanggal selanjutnya = ' . $tanggalAbsen;

		// 			if ($tglAwal == $tanggalAbsen) {
		// 				//hapus

		// 				echo 'ini hapus';
		// 				$this->db->where('id', $id);
		// 				$this->db->delete('tbl_absensi');
		// 			}
		// 			$tglAwal = $tanggalAbsen;
		// 		}
		// 		print_array($row);

		exit;
		redirect('admin/presensi/update_absensi_pegawai/' . $id_pegawai . '/' . $pin);
	}
}
