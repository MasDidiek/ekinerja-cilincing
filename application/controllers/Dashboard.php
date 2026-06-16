<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();
		$this->Auth_model->cekAuthLogin();
		$this->load->model('Laporan_model');
		  $this->load->model('Admin_cuti_model', 'acm');
		$this->load->helper('text');
	}

	function index()
	{


		$id_puskesmas = $this->session->userdata('id_puskesmas');
		$usergroup    = $this->session->userdata('usergroup');
		$id_pegawai    = $this->session->userdata('id_pegawai');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		if($periode_bulan=='') {
		  $bulan = date('m')-1;
		  $tahun = date('Y');

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}


		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));

		$nip  = $this->session->userdata('nip');

		#$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiKasatpel($id_pegawai);
		$data['pengajuanDL'] = $this->Presensi_model->pengajuanDinasLuarPegawai($id_pegawai);
		$data['pengajuanIzinSakit'] = $this->Presensi_model->getPengajuanIzinSakitByValidator($id_pegawai, $periode);

					#print_array($pengajuanDL);
		if($usergroup==3 || $usergroup==4 ){
			//$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiPending('PEND1', $id_pegawai);
			$data['numCutiPending'] = $this->acm->getNumCutiPending($id_pegawai);
			$data['pengajuanDL'] = $this->Presensi_model->pengajuanDinasLuarPegawai($id_pegawai, 'Pending');
			//$data['pengajuanIzinSakit'] = $this->Presensi_model->getDataPengajuanIzinSakitByValidator($id_pegawai, $periode);
			$this->load->view('dashboard/kapuskel_dashboard', $data);
		}else if($usergroup==1){ //Kapuskec
			$data['numCutiPending'] = $this->acm->getNumCutiPending($id_pegawai);
			$this->load->view('dashboard/kapuskec_dashboard', $data);
		}else if($usergroup==2){
			//ktu
			$data['numCutiPending'] = $this->acm->getNumCutiPending($id_pegawai);
			//$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiPending($id_pegawai);
			//$data['cutiPegawaiAdmen'] = $this->Cuti_model->getDataCutiPegawaiPending('PEND1', $id_pegawai);
			
			$data['pengajuanDL'] = $this->Presensi_model->pengajuanDinasLuarPegawai(0, 0);
			$this->load->view('dashboard/ktu_dashboard', $data);
		}else{
			$data['totalAktifitas']   = $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);
			$data['dataRekap']        = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
			$data['rekapTKD']         = $this->Laporan_model->getRekapTKDPegawai($nip, $periode);
			
			redirect('dashboard/my_dashboard');

			//$this->load->view('dashboard/user', $data);
		}


    }



	function pengajuan_cuti_pending($role_approval='kapustu'){

		$id_validator_session = $this->session->userdata("id_pegawai");


		if($role_approval=='kapus'){
			//kepala puskesmas induk
			$data["cuti_pegawai"] = $this->acm->getPengajuanCutiPegawai($id_validator_session, 'kapus');// pengajuan cuti menunggu acc kapus kec
		}else{
			$data["cuti_pegawai"] = $this->acm->getPengajuanCutiPegawai($id_validator_session, $role_approval);// pengajuan cuti menunggu acc kapustu
			$data["cuti_pegawai_ktu"] = $this->acm->getPengajuanCutiPegawai($id_validator_session, 'ktu');// pengajuan cuti menunggu acc ktu

		}


		$this->load->view('dashboard/dashboard_pengajuan_cuti', $data);
	}


	public function calendar_cuti()
	{
		$start = date('Y-01-01');
		$end   = date('Y-12-31');

		$id_validator = $this->session->userdata('id_pegawai');

		$data = $this->Cuti_model->getCutiForCalendar(
			$start,
			$end,
			$id_validator
		);

		$events = [];

		foreach ($data as $row) {

			$warna = ($row->status_akhir == 'disetujui')
				? '#28a745'
				: '#17a2b8';

			$events[] = [
				'id'    => $row->id,
				'title' => $row->nama,
				'start' => $row->tgl_mulai,
				'end'   => date('Y-m-d', strtotime($row->tgl_selesai . ' +1 day')),
				'backgroundColor' => $warna,
				'borderColor'     => $warna
			];
		}

		echo json_encode($events);
	}
	
	function pengajuan_izin_pending($usergroup='ktu'){
		$id_pegawai = $this->session->userdata('id_pegawai');
		$bulan = $this->session->userdata('periode_bulan');
		$tahun = $this->session->userdata('periode_tahun');

		if (empty($bulan) || empty($tahun)) {

			$tanggal = new DateTime();

			// Kalau tanggal <= 10, mundur 1 bulan
			if ((int)$tanggal->format('d') <= 10) {
				$tanggal->modify('-1 month');
			}

			$bulan = $tanggal->format('m');
			$tahun = $tanggal->format('Y');

			$this->session->set_userdata([
				'periode_bulan' => $bulan,
				'periode_tahun' => $tahun
			]);
		}

		$periode = $tahun . '-' . $bulan; // format Y-m




		$data['pengajuanIzinSakit'] = $this->Presensi_model->getPengajuanIzinSakitByValidator($id_pegawai, $periode);

		$this->load->view('dashboard/dashboard_pengajuan_izin', $data);
	}


	function dashboard_pengajuan_cuti(){

		$data['pengajuanCuti']  = $this->Cuti_model->getDataCutiPending();
		$data['master_cuti']    = $this->Master_model->getlistCuti();
		$data['pengajuanDL']        = $this->Presensi_model->getPengajuanDlPending();
		$data['pengajuanIzin']        = $this->Presensi_model->getDataIzinSakit(0, 'IZIN');
		$data['pengajuanSakit']        = $this->Presensi_model->getDataIzinSakit(0, 'SAKIT');
		$this->load->view('dashboard/dashboard_pengajuan_cuti', $data);

	}

	
	function dashboard_pengajuan_izin(){

		$data['pengajuanCuti']  = $this->Cuti_model->getDataCutiPending();
		$data['master_cuti']    = $this->Master_model->getlistCuti();
		$data['pengajuanDL']        = $this->Presensi_model->getPengajuanDlPending();
		$data['pengajuanIzin']        = $this->Presensi_model->getDataIzinSakit(0, 'IZIN');
		$data['pengajuanSakit']        = $this->Presensi_model->getDataIzinSakit(0, 'SAKIT');
		$this->load->view('dashboard/dashboard_pengajuan_izin', $data);

	}

	

	function search_pengajuan_cuti(){
		$keyword = $this->input->post('keyword');

		$result = $this->Cuti_model->search_pengajuan_cuti($keyword);
		$master_cuti    = $this->Master_model->getlistCuti();

		$this->load->view('dashboard/partial/list_pengajuan_cuti', ['pengajuanCuti' => $result, 'master_cuti'=>$master_cuti]);
		
	}

	function ajaxDetailCuti() {
		$id_cuti = $this->input->post('id_cuti');

		$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
		$data['list_hari_cuti'] = $this->Cuti_model->getListHariCuti($id_cuti);
		$this->load->view('pengajuan_cuti/view_detail_cuti', $data);
	}


	function ajaxEditDataCuti(){
		$id_cuti = $this->input->post('id_cuti');
		$cuti = $this->Cuti_model->getDetailCuti($id_cuti);


		echo json_encode($cuti);
	}



	function dashboard_pengajuan_dl(){
		$data['pengajuanDL']        = $this->Presensi_model->getPengajuanDlPending();
		$data['pengajuanCuti']  = $this->Cuti_model->getDataCutiPending();
		$data['pengajuanIzin']        = $this->Presensi_model->getDataIzinSakit(0, 'IZIN');
		$data['pengajuanSakit']        = $this->Presensi_model->getDataIzinSakit(0, 'SAKIT');
		$this->load->view('dashboard/dashboard_pengajuan_dl', $data);
	}


	function kapuskel_dashboard(){
		$periode_bulan = $this->session->userdata('periode_bulan'); 
		$periode_tahun = $this->session->userdata('periode_tahun'); 
	
		if($periode_bulan=='') {
			$day = date('d');
			$tahun = date('Y');
			if($day > 10){
				$bulan = date('m');
				
			}else{
				$bulan = date('m')-1;
			}

			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);

			redirect('dashboard/my_dashboard');
		

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}

		$thn_anggaran = $tahun;

		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));

		$tgl = $periode.'-01';

		$id_validator    = $this->session->userdata('id_pegawai');
		
		$data['pegawai'] = $this->Pegawai_model->getAllPegawaiByValidator($id_validator, $thn_anggaran);
		$data['cuti_pending'] = $this->Cuti_model->getDataCutiPegawai('PEND1', $id_validator);
		$data['dinas_luar'] = $this->Absensi_model->getPengajuanDLbyValidator($id_validator, $tgl);

		$this->load->view('dashboard/kapuskel_dashboard', $data);
	}



    function user_dashboard(){
        $id_pegawai    = $this->session->userdata('id_pegawai');
        $data['penggantian_cuti'] = $this->Cuti_model->getPermohonanPengganti($id_pegawai);

		$this->load->view('dashboard/user_dashboard', $data);
	}
	function my_dashboard(){
		//print_array($this->session->userdata);

	
		$periode_bulan = $this->session->userdata('periode_bulan'); 
		$periode_tahun = $this->session->userdata('periode_tahun'); 
		$id_pegawai    = $this->session->userdata('id_pegawai');
		$nip  		   = $this->session->userdata('nip');
		$usergroup     = $this->session->userdata('usergroup');

		$pin           = substr($nip, -4);


		if($usergroup==3 || $usergroup==4 ){
			redirect('dashboard/index');
		}

		
		if($periode_bulan=='') {
			$day = date('d');
			$tahun = date('Y');
			if($day > 10){
				$bulan = date('m');
				
			}else{
				
				$bulan = date('m');

				if($bulan==1){
					$bulan = 12;
					$tahun = date('Y')-1;
				}else{
					$bulan = $bulan-1;
				}

			}

			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);

			//redirect('dashboard/my_dashboard');
		

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}

		
		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));;

      
		if($usergroup==3 || $usergroup==4 ){
			redirect('dashboard/kapuskel_dashboard');
		}
		 $jns_pegawai   = $this->session->userdata('jns_pegawai');

        $data['penggantian_cuti'] = $this->Cuti_model->getPermohonanPengganti($id_pegawai);
		$data['rekapAbsensi'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$data['getLastAbsensi'] = $this->Presensi_model->getLastAbsensi($pin);
		$data['rekap_capaian_kinerja']  = $this->Laporan_model->getRekapTKDPegawaipertahun($nip, $tahun);
		$data['pengajuan_dinas_luar'] = $this->Absensi_model->getListPengajuanDL($id_pegawai, $periode);
		$data['dataCuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);
		$data['dataIzinSakit'] = $this->Presensi_model->getDataIzinSakitPegawai($id_pegawai);
		//$data['rekapTKD']  = $this->Laporan_model->getRekapTKDPegawai($nip, $periode);

		$data['last_tkd']  = $this->Laporan_model->getLastTKD($nip, $jns_pegawai);
		$data['totalAktifitas'] =  $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);

		$data['poinPerilaku']     =  $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);
		$data['master_cuti'] = $this->Master_model->getlistCuti();

		$pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $id_jabatan = $pegawai->id_jabatan;
		$data['listPegawaiPengganti']   = $this->Cuti_model->getListPegawaiPenggantiCuti( $id_pegawai, $id_jabatan) ;

		$this->load->view('dashboard/my_dashboard', $data);
	}

	function acc_pengajuan_cuti(){
		$id_cuti		= $this->input->post('id');
		$status = $this->input->post('status');

        if($status =='PEND1'){
             //kondisi menunggu validasi kapustu
            $data = array(
                'check_kapuskel' => 1,
                'tgl_check' => date('Y-m-d'),
                'status' => 'PEND2'
            );
        }else if($status =='PEND2'){
              //kondisi sudah divalidasi oleh kapustu/kasatpel, menunggu approve kasubbag
              $data = array(
                'check_ktu' => 1,
                'tgl_check_ktu' => date('Y-m-d'),
				'check_kapuskec' => 1,
				'tgl_check2' => date('Y-m-d'),
                'status' => 'APPROVE'
            );

			$detail_cuti  = $this->Cuti_model->getDetailCuti($id_cuti);
			$id_pegawai   = $detail_cuti[0]->id_pegawai;
			$jns_cuti     = $detail_cuti[0]->jns_cuti;
			$tgl_dari     = $detail_cuti[0]->tgl_dari;
			$end_date     = $detail_cuti[0]->tgl_sampai;
			$jns_hak_cuti = $detail_cuti[0]->jns_hak_cuti;
			$hari_cuti    = $detail_cuti[0]->hari_cuti;
			$alasan_cuti     = $detail_cuti[0]->alasan_cuti;
			$nip          = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
			$pin          = substr($nip, -4);

            if($jns_cuti==1){
                //cuti tahunan

                if($hari_cuti==1){
                    $this->Presensi_model->insertAbsensiCuti($tgl_dari, $pin, $alasan_cuti);
                }else{
                    $list_hari = $this->Cuti_model->getListHariCuti($id_cuti);
                    for ($c=0; $c < count($list_hari) ; $c++) {
                       $tgl_cuti = $list_hari[$c]->tanggal;
                       $this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti );
                    }
                }

            }else{


                $selisihhari =  datediff('d', $tgl_dari, $end_date);
                $hari_cuti = $selisihhari+1;
                $tgl_cuti = $tgl_dari;
                for ($c=0; $c < $hari_cuti; $c++) {

                    $this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti );
                     $tgl_cuti = add_date($tgl_cuti, 1);
                 }


            }

        }



        $this->db->where('id', $id_cuti);
		$updated = $this->db->update('ts_cuti', $data);

		if ($updated) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}



	}


	// function acc_dinas_luar(){
	// 	$id = $this->input->post('id');
	// 	$status_acc = $this->input->post('status');

	// 	$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
	// 	$dinasLuar = $qry->result();

	// 	$jns_dl = $dinasLuar[0]->jns_dl;
	// 	$keterangan = $dinasLuar[0]->keterangan;
	// 	$tanggal = $dinasLuar[0]->tanggal;
	// 	$id_pegawai = $dinasLuar[0]->id_pegawai;

	// 	$nip = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
	// 	$pin = substr($nip, -4);

		
	// 	if($status_acc=='acc'){
	// 		$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

	// 		$this->db->where('id', $id);
	// 		$this->db->set('status', 1);
	// 		$this->db->update('pengajuan_dinas_luar');
	// 		//echo 'Pengajuan dinas luar telah disetujui';
			
	// 	}else{
			
	// 		$this->db->where('id', $id);
	// 		$this->db->set('status', 2);
	// 		$this->db->update('pengajuan_dinas_luar');
	// 		//echo 'Pengajuan dinas luar tidak disetujui';
			
	// 	}

		
	
	// }

	function set_session_periode($prev_next){

		// $bulan = $this->input->post('bulan');
		// $tahun = $this->input->post('tahun');

		  
        $periode_bulan = $this->session->userdata('periode_bulan'); 
        $periode_tahun = $this->session->userdata('periode_tahun'); 
        
        if($periode_bulan==''){
            $periode_bulan = date('m');
           $periode_tahun = date('Y');
        }
        
    
		if($prev_next=='next'){
			$next_month = $periode_bulan+1;
			if($next_month==13){
				$bulan = 1;
				$tahun = $periode_tahun+1;
			}else{
				$bulan = $next_month;
				$tahun = $periode_tahun;
			}
			
			
		}else{
			$prev_month = $periode_bulan-1;
			

			if($prev_month == 0){
				$bulan = 12;
				$tahun = $periode_tahun-1;
			}else{
				$bulan = $prev_month;
				$tahun = $periode_tahun;
			}
		}

		$this->session->set_userdata('periode_bulan', $bulan);
		$this->session->set_userdata('periode_tahun', $tahun);

		redirect('dashboard/index');

	

		#$this->session->set_userdata($this->input->post());
		#redirect('admin/presensi/index');
	}


	function pengajuan_dinas_luar($status=''){
        $id_pegawai    = $this->session->userdata('id_pegawai');
        $usergroup    = $this->session->userdata('usergroup');
		$data['pengajuanDL']        = $this->Presensi_model->getPengajuanDlPending();
		$this->load->view('admin/presensi/pengajuan_dinas_luar_pegawai', $data);
    }


	function getDetailDL(){
		$id = $this->input->post('id');
		$dinas_luar =$this->Absensi_model->getDetailPengajuanDL($id);

		echo json_encode($dinas_luar);
	}

	function update_data_pengajuan_cuti(){

		$id_cuti     = $this->input->post("id_cuti");
	    $this->Cuti_model->updateDataCuti($id_cuti);

		$this->session->set_flashdata('message', [
			'type' => 'success', // bisa: success, error, info, warning
			'text' => 'Data Pengajuan cuti berhasil diubah!'
		]);

		redirect('dashboard/dashboard_pengajuan_cuti');
	}



	function update_pengajuan_dl(){

		$id = $this->input->post('id');
		$data = array(
			'jns_dl' => $this->input->post('jns_dl'),
			'tanggal' => $this->input->post('tgl'),
			'keterangan' => $this->input->post('keterangan')
		);

		$this->db->where('id', $id);
		$this->db->update('pengajuan_dinas_luar', $data);

		$this->session->set_flashdata('message', [
		'type' => 'success', // bisa: success, error, info, warning
		'text' => 'Pengajuan dinas luar berhasil diubah!'
		]);
		redirect('dashboard/dashboard_pengajuan_dl');
	}


	public function delete_dl()
	{
		$id = $this->input->post('id');

		$this->db->where('id', $id);
		$deleted = $this->db->delete('pengajuan_dinas_luar');

		if ($deleted) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}
	}


	function detail_pengajuan_dl($id,  $id_pegawai){

        $data['pengajuan_dinas_luar'] = $this->Absensi_model->getDetailPengajuanDL($id);
		$data['detail_pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $this->load->view('admin/presensi/detail_dinas_luar', $data);
	}


	function acc_dl(){

		$id = $this->input->post('id');

		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
		$dinasLuar = $qry->result();

		$jns_dl     = $dinasLuar[0]->jns_dl;
		$keterangan = $dinasLuar[0]->keterangan;
		$tanggal    = $dinasLuar[0]->tanggal;
		$id_pegawai = $dinasLuar[0]->id_pegawai;

		$nip = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
		$pin = substr($nip, -4);



		$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

		$this->db->where('id', $id);
		$this->db->set('status', 1);
		$updated = $this->db->update('pengajuan_dinas_luar');

		if ($updated) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}
		#redirect('admin/presensi/index');
	}

	
	function delete_pengajuan_dl($id){

		$this->db->where('id', $id);
		$this->db->delete('pengajuan_dinas_luar');
		redirect('dashboard/pengajuan_dinas_luar');
		
	}

	function acc_pengajuan_izin_sakit(){
   
	    $id_izin    = $this->input->post('id');
	   
	    $status_acc = 1;

		$qry = $this->db->get_where('pengajuan_izin_sakit', array('id' => $id_izin));
		$izinSakit = $qry->result();

		$tanggal 	 = $izinSakit[0]->tanggal;
		$jenis_absen = $izinSakit[0]->jenis_absen;
		$id_pegawai  = $izinSakit[0]->id_pegawai;
		$ket         = $izinSakit[0]->keterangan;

		$nip         = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
		$pin = substr($nip, -4);


		if($status_acc==1){
			//pengajuan di ACC 
			$this->Presensi_model->insertAbsensiIzinSakit($pin, $tanggal, $jenis_absen, $ket);
			$this->db->where('id', $id_izin);
			$this->db->set('status', 1);
			$updated = $this->db->update('pengajuan_izin_sakit');
			//echo 'Pengajuan izin/sakit telah disetujui';
		}else{
			$this->db->where('id', $id_izin);
			$this->db->set('status', 0);
			$updated = $this->db->update('pengajuan_izin_sakit');
			///echo 'Pengajuan izin/sakit  tidak disetujui';
		}

		if ($updated) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error']);
		}

		//$this->session->set_flashdata('message', $alert);

		//redirect('dashboard/ktu_dashboard');
	}




	// function accPengajuanDL(){
	  
		   
	//     $id         = $this->input->post('id');
	//     $pin        = $this->input->post('pin');
	//     $status_acc = 1;
	    
	// 	$qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
	// 	$dinasLuar = $qry->result();

	// 	$jns_dl = $dinasLuar[0]->jns_dl;
	// 	$keterangan = $dinasLuar[0]->keterangan;
	// 	$tanggal = $dinasLuar[0]->tanggal;

		
	// 	if($status_acc==1){
	// 		$this->Presensi_model->insertAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

	// 		$this->db->where('id', $id);
	// 		$this->db->set('status', 1);
	// 	}else{
	// 		$this->db->where('id', $id);
	// 		$this->db->set('status', 2);
	// 	}

	// 	$this->db->update('pengajuan_dinas_luar');
		
	// 		$alert =  '  <div class="alert alert-success">
    //                 <span class="font-bold">Success! </span> Dinas Luar telah disetujui
    //             </div>
    //             ';

	// 	$this->session->set_flashdata('message', $alert);

	// 	redirect('dashboard/ktu_dashboard');

	// }

	
	
    function ajax_detail_pengajuan_dl()
    {
		$id = $this->input->post('id');

        $data['pengajuan_dinas_luar'] = $this->Absensi_model->getDetailPengajuanDL($id);
        $this->load->view('dashboard/detail_dinas_luar', $data);
    }

	

	function change_theme(){
		$theme = $this->input->post('theme');

		$this->session->set_userdata('theme', $theme);
		return true;
	}

	function tarikDataAbsensiMesin(){

		$data['mesin_absensi'] = $this->Master_model->getlistMesin();
		$this->load->view('dashboard/tarik_data_absensi', $data);
	}

	function list_user($id_puskesmas, $serial_number){
		$this->db->select('*');
		$qry = $this->db->get_where('mst_pegawai', array('id_puskesmas'=> $id_puskesmas,'tahun_anggaran'=> '2024','jns_pegawai'=> 'non_pns'));
		$row = $qry->result();



		$data['pegawai'] = $row;
		$data['mesin_absensi'] = $this->Master_model->getDetMesinAbsensi($serial_number);
		$this->load->view('dashboard/list_user', $data);
	}

	function change_date($tgl) {

		$this->session->set_userdata('tgl_hari_ini', $tgl);
		redirect('dashboard/my_dashboard');

	}

	function sinkron_absensi() {
		

		$id_pegawai    = $this->session->userdata('id_pegawai');
		$id_puskesmas  = $this->session->userdata('id_puskesmas');
		$nip  		   = $this->session->userdata('nip');
		$tgl           = $this->input->post('tgl');
		$pin 		   = $this->Pegawai_model->getPinPegawai($nip);
		//$ip_address    = '10.20.170.102';
		
		$sinkron = $this->Sinkron_model->sinkronAbsensiHarian($id_pegawai, $pin, $tgl);
		$masuk = $sinkron[0];
		$pulang = $sinkron[1];
	
		echo $masuk.'/'.$pulang;


	}

	function ajaxTarikDataAbsensi(){

		$serial_number = $this->input->post('serial_number');
		$detail_mesin = $this->Presensi_model->detailMesin($serial_number);
        $ip_address   = $detail_mesin[0]->ip_address;

		$dataPresensi = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin='');
		#print_array($dataPresensi);

		$last_update   = $detail_mesin[0]->last_update;

		for ($i=0; $i < count($dataPresensi) ; $i++) {

			$DateTime = $dataPresensi[$i]['DateTime'];
			$pin      = $dataPresensi[$i]['pin'];
			$Status   = $dataPresensi[$i]['Status'];

			//$periode_db = date('Y-m', strtotime($DateTime));

			#echo 'Periode DB '.$periode_db;

			#print_array($dataPresensi);

			if($DateTime >= $last_update){
				$this->Presensi_model->insertAbsensi($DateTime, $pin, $Status);
			}


		}


		// echo $DateTime;

		// echo $serial_number.' -------'.$DateTime;
		$this->db->where('serial_number', $serial_number);
		$this->db->set('last_update', $DateTime);
		$this->db->update('tbl_mesin_absensi');

		echo '<h4>
		        <span class="text-success"><i class="fas fa-check-circle"></i></span> Data absensi berhasil ditarik
				</h4>';
		//redirect('dashboard/list_user/'.$serial_number);

	}


	function list_pengajuan_izin(){
		$jns_absensi = $this->session->set_userdata('jenis_absensi');
		$status = $this->session->set_userdata('status');
		$data['izin_sakit'] = $this->Absensi_model->getDataPengjuanIzinSakit($jns_absensi, $status);
		$this->load->view('dashboard/list_pengajuan_izin', $data);
	}

	function filter_jns_absensi(){
		$jns_absensi = $this->input->post('jenis_absensi');
		$status = $this->input->post('status');

		$this->session->set_userdata($this->input->post());
		$izin_sakit = $this->Absensi_model->getDataPengjuanIzinSakit($jns_absensi, $status);

		for ($i=0; $i < count($izin_sakit); $i++) {
			$id = $izin_sakit[$i]->id;
			$id_pegawai = $izin_sakit[$i]->id_pegawai;
			$tanggal = $izin_sakit[$i]->tanggal;
			$jenis_absen = $izin_sakit[$i]->jenis_absen;
			$keterangan = $izin_sakit[$i]->keterangan;
			$nama = $this->Pegawai_model->getNamaPegawaiByID($id_pegawai);
			$status = $izin_sakit[$i]->status;
			$file_image = $izin_sakit[$i]->file_image;
			if($status==0){
			  $status_flag = '<span class="badge bg-warning-subtle text-warning">Belum diperiksa</span>';
			}else{
			  $status_flag = '<span class="badge bg-success-subtle text-success">Sudah diperiksa</span>';
			}

			echo '
			 <tr>
			  <td>'.($i+1).'</td>

			  <td>'.$jenis_absen.'</td>
			  <td> <a href="'.base_url().'uploads/surat_izin/'.$file_image.'" target="_blank">'.$nama.'</a></td>
			  <td>'.format_view($tanggal).'</td>
			  <td>'.$keterangan.'</td>
			  <td>'.$status_flag .'</td>

			  <td> <a href="'.base_url().'uploads/dashboard/change_status/'.$id.'"> Acc</a></td>
			 </tr>
			';
			# code...
		}
	}

	
    function view_daftar_ttd($case='tkd') {
        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');


        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));
       // $periode = '2025-02';
        if($case=='tkd'){
            $table = 'ts_rekap_tkd';
        }else{
             $table = 'ts_listing_thr';
        }
        $data['data_tkd'] = $this->Laporan_model->getDataListing($periode, $table);

        $this->load->view('view_daftar_ttd', $data);
    }
}