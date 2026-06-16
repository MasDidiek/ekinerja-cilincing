<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Pengajuan_cuti extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		 $this->load->model('Profile_model');
		 $this->load->helper('text');
         $this->Auth_model->cekAuthLogin();
	}

	function index($status='Semua'){
		$id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');

		$status_session = $this->session->userdata('status');
        $tanggal_cuti = $this->session->userdata('tanggal_cuti');
		$usergroup = $this->session->userdata('usergroup');

		//$status_session = 'Batal';

		if($usergroup==3){
			$id_validator = $id_pegawai;
		}else{
			$id_validator = 0;
		}

		$jns_pegawai = 'non_pns';
		//print_array($this->session->userdata);

		if($tanggal_cuti ==''){
			$data['list_cuti'] = $this->Cuti_model->getDataCutiPegawai($status_session, $id_validator);
		}else{

			$data['list_cuti'] = $this->Cuti_model->searchCutiPegawai($status_session, $id_validator, $jns_pegawai );
		}


		//$this->load->view('pengajuan_cuti/index', $data);

		redirect('admin/pengajuan_cuti/lastest');
	}

	function lastest(){
		//pengajuan cuti terbaru
		$limit    = 25;
		$url      = 'admin/pengajuan_cuti/lastest/';

		//print_array($this->session->userdata());
		$usergroup = $this->session->userdata('usergroup');
		$id_validator =  $this->session->userdata('id_pegawai');

		if($usergroup==3 || $usergroup== 4){
			$id_validator_cuti = $this->session->userdata('id_validator_cuti');
			if($id_validator_cuti==''){
				$this->session->set_userdata('id_validator_cuti', $id_validator);

			}
		}

		$num_rows = $this->Cuti_model->count_pengajuan_cuti();
		$page     = $this->Cuti_model->pagination($limit,  $num_rows, $url);
		$data['cutiPegawai'] = $this->Cuti_model->getListCutiPage($limit, $page);
		$data['pagination'] = $this->pagination->create_links();
		$data['num_row'] = $num_rows;
		$data['validator'] = $this->Pegawai_model->getValidator();
		$this->load->view('pengajuan_cuti/pengajuan_cuti_terbaru', $data);
	}

	function search_pegawai() {

		$nama_pegawai = $this->input->post('nama_pegawai');

		$this->session->set_userdata('search_pegawai', $nama_pegawai);
		$limit    = 25;
		$url      = 'admin/pengajuan_cuti/lastest/';
		$num_rows = $this->Cuti_model->countSearchCutiPegawai($nama_pegawai);
		$page     = $this->Cuti_model->pagination($limit,  $num_rows, $url);
		$data['cutiPegawai'] = $this->Cuti_model->searchCutiPegawaiByNama($nama_pegawai, $limit, $page);
		$data['pagination'] = $this->pagination->create_links();
		$data['num_row'] = $num_rows;
		$data['validator'] = $this->Pegawai_model->getValidator();


		$this->load->view('pengajuan_cuti/pengajuan_cuti_terbaru', $data);

	}


	function set_session_bulan(){

		$periode_bulan = $this->input->post('periode_bulan');
		$periode_tahun = $this->input->post('periode_tahun');
		$this->session->set_userdata('periode_bulan', $periode_bulan);
		$this->session->set_userdata('periode_tahun', $periode_tahun);

		redirect('admin/pengajuan_cuti/index');
	}

	function set_session_tanggal(){

		$tanggal_cuti = $this->input->post('tgl');
		$this->session->set_userdata('tanggal_cuti', $tanggal_cuti);

		//redirect('admin/pengajuan_cuti/index');
	}
	function set_session($status){

		//$status = $this->input->post('status');
		$this->session->set_userdata('status_cuti', $status);

		redirect('admin/pengajuan_cuti/lastest');
	}

	function set_session_validator(){

		$id_validator = $this->input->post('id_validator');
		$this->session->set_userdata('id_validator_cuti', $id_validator);

		return true;
	}


	function index_v2($status='Semua'){
        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');

		$status_session = $this->session->userdata('status');
        $tanggal_cuti = $this->session->userdata('tanggal_cuti');
		$usergroup = $this->session->userdata('usergroup');

		if($usergroup==3){
			$id_validator = $id_pegawai;
		}else{
			$id_validator = 0;
		}

		$jns_pegawai = 'non_pns';
		//print_array($this->session->userdata);

		if($tanggal_cuti ==''){
			$data['list_cuti'] = $this->Cuti_model->getDataCutiPegawai($status_session, $id_validator);
		}else{

			$data['list_cuti'] = $this->Cuti_model->searchCutiPegawai($status_session, $id_validator, $jns_pegawai );
		}


		$this->load->view('pengajuan_cuti/index_v2', $data);
	}


	function search_pegawai_cuti(){
		$keyword = $this->input->post('keyword');

		$sql = "SELECT id_pegawai, nama FROM mst_pegawai where nama like '%$keyword%' AND tahun_anggaran = 2024";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		//print_array($row);
		$dataCuti = array();

		if(!empty($row)){


			for ($i=0; $i < count($row) ; $i++) {
					$id_pegawai = $row[$i]->id_pegawai;
					$nama = $row[$i]->nama;
					$this->db->order_by('id', 'DESC');
					$qry2 = $this->db->get_where('ts_cuti', array('id_pegawai'=> $id_pegawai));
					$row2 = $qry2->result();


					for ($c=0; $c < count($row2) ; $c++) {
						$dataCuti[] = (object)  array(
							'id' => $row2[$c]->id,
							'jns_hak_cuti'  => $row2[$c]->jns_hak_cuti,
							'tgl' => $row2[$c]->tgl,
							'id_pegawai' => $row2[$c]->id_pegawai,
							'jns_cuti' => $row2[$c]->jns_cuti,
							'alasan_cuti' => $row2[$c]->alasan_cuti,
							'tgl_dari' => $row2[$c]->tgl_dari,
							'tgl_sampai' => $row2[$c]->tgl_sampai,
							'hari_cuti' => $row2[$c]->hari_cuti,
							'status' => $row2[$c]->status,
							'nama' => $nama,
						);

					}


					//print_array($row2);
			}
		}

		$data['list_cuti'] = $dataCuti;
		$this->load->view('pengajuan_cuti/index', $data);

	}

	function search_cuti()  {
		//print_array($this->input->post());

		$from = $this->input->post('from');
		$to   = $this->input->post('to');


		$tgl_dari = format_db($from);
		$tgl_sampai = format_db($to);
		$tanggal_cuti = $tgl_dari.'to'.$tgl_sampai;


		// $status = $this->input->post('status');
        // $tanggal_cuti = $this->input->post('tanggal_cuti');

		$this->session->set_userdata('tanggal_cuti', $tanggal_cuti);
		redirect('admin/pengajuan_cuti/index');

	}

	function list_pegawai_cuti_pending(){
		$id_validator = $this->session->userdata('id_pegawai');
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');


		$bulan = $periode_bulan;
		$tahun = $periode_tahun;

		$thn_anggaran = $tahun;

		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));

		$tgl = $periode.'-01';


		$id_validator    = $this->session->userdata('id_pegawai');

		$data['dinas_luar']        = $this->Absensi_model->getPengajuanDLbyValidator($id_validator, $tgl);
		$data['list_pegawai_cuti'] = $this->Cuti_model->getCutiPending($id_validator);

		$this->load->view('dashboard/pengajuan_cuti', $data);

	}


    function dashboard_cuti(){
        	$this->load->view('pengajuan_cuti/dashboard_cuti');
    }

    function filter_data(){


        $jns_cuti = $this->input->post('jns_cuti');
        $jns_pegawai = $this->input->post('jns_pegawai');


        $this->session->set_userdata($this->input->post());


        $data['list_cuti'] = $this->Cuti_model->getCutiByJnsCuti($jns_cuti);
		$this->load->view('pengajuan_cuti/filter_data', $data);
    }

	function ajaxDetailCuti() {
		$id_cuti = $this->input->post('id_cuti');

		$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
		$data['list_hari_cuti'] = $this->Cuti_model->getListHariCuti($id_cuti);
		$this->load->view('pengajuan_cuti/view_detail_cuti', $data);
	}

    function detail_cuti($id_cuti){
        $data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
		$data['list_hari_cuti'] = $this->Cuti_model->getListHariCuti($id_cuti);
		$data['master_cuti'] = $this->Master_model->getlistCuti();
		$this->load->view('pengajuan_cuti/detail_pengajuan_cuti', $data);
    }

	function print_cuti($id_cuti){
		$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
		$this->load->view('pengajuan_cuti/print_cuti', $data);
	}


	function list_pegawai_cuti_all($status='PEND1') {
		$id_puskesmas  = $this->session->userdata('id_puskesmas');
		$usergroup     = $this->session->userdata('usergroup');
		$id_pegawai    = $this->session->userdata('id_pegawai');
		$usergroup     = $this->session->userdata('usergroup');


		if($usergroup==3 || $usergroup==4){

			$this->session->set_userdata('status', 'pending');
			redirect('admin/pengajuan_cuti/index_v2');

			//$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiKasatpel($id_pegawai, $status);
		}else if($usergroup==1){ //Kapuskec
			$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiPending('PEND3');
		}else{
			$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiKTU($status);
		}
	  	$this->load->view('pengajuan_cuti/pengajuan_cuti_pending', $data);

	}
	function pengajuan_cuti_pegawai($status='PEND1'){
		$id_puskesmas  = $this->session->userdata('id_puskesmas');
		$usergroup     = $this->session->userdata('usergroup');
		$id_pegawai    = $this->session->userdata('id_pegawai');
		$usergroup     = $this->session->userdata('usergroup');


		$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiPending($status);
		$this->load->view('pengajuan_cuti/pengajuan_cuti_pending', $data);
		// if($usergroup==3 || $usergroup==4){

		// 	$this->session->set_userdata('status', 'pending');
		// 	redirect('admin/pengajuan_cuti/index_v2');

		// 	//$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiKasatpel($id_pegawai, $status);
		// }else if($usergroup==1){ //Kapuskec
		// 	$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiPending('PEND3');
		// }else{
		// 	$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiKTU($status);
		// }
	 //  	$this->load->view('pengajuan_cuti/pengajuan_cuti_pending', $data);

	}



		function pengajuan_cuti_admen(){
			$id_puskesmas  = $this->session->userdata('id_puskesmas');
			$usergroup     = $this->session->userdata('usergroup');
			$id_pegawai    = $this->session->userdata('id_pegawai');
	        $usergroup     = $this->session->userdata('usergroup');

			$data['cutiPegawai'] = $this->Cuti_model->getDataCutiPegawaiPending('PEND1', $id_pegawai);

			$this->load->view('pengajuan_cuti/pengajuan_cuti_pending', $data);

		}

    function approve_pengajuan_cuti($id_cuti, $status){
			//$status  = $this->input->post('status');

			// $detail_cuti  = $this->Cuti_model->getDetailCuti($id_cuti);
			// $list_hari = $this->Cuti_model->getListHariCuti($id_cuti);

			// $id_pegawai = $detail_cuti[0]->id_pegawai;
			// $nip          = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
			// $pin          = substr($nip, -4);
			// $alasan_cuti  = $detail_cuti[0]->alasan_cuti;

			// //print_array($list_hari);
			// for ($c=0; $c < count($list_hari) ; $c++) {
			// 		$tgl_cuti = $list_hari[$c]->tanggal;
			// 		$this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti );
			// }

			// exit;
			if($status=='PEND0'){
				$data = array(
						'cek_pengganti' => 1,
						'tgl_cek' => date('Y-m-d'),
						'status' => 'PEND1'
				);

                $this->db->where('id', $id_cuti);
                $this->db->update('ts_cuti', $data);

			}elseif($status=='PEND1'){

		    	$detail_cuti  = $this->Cuti_model->getDetailCuti($id_cuti);
				$id_pegawai   = $detail_cuti[0]->id_pegawai;

				$detail_pegawai   = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
				$rumpun_kerja   = $detail_pegawai[0]->rumpun_kerja;



				$data = array(
						'check_kapuskel' => 1,
						'tgl_check' => date('Y-m-d'),
						'status' => 'PEND2'
				);

                $this->db->where('id', $id_cuti);
                $this->db->update('ts_cuti', $data);

				if($rumpun_kerja=='admen'){
				    //jika pegawai lansung ke atasan KTU maka status langung pending ke 3
				    	$data = array(
							'check_ktu' => 1,
							'tgl_check_ktu' => date('Y-m-d'),
							'check_kapuskec' => 1,
							'tgl_check2' => date('Y-m-d'),
							'status' => 'APPROVE'
				 	);

				 	$this->db->where('id', $id_cuti);
                    $this->db->update('ts_cuti', $data);
				}





			}elseif($status=='PEND2' ||  $status=='PEND3'){

					$data = array(
							'check_ktu' => 1,
							'tgl_check_ktu' => date('Y-m-d'),
							'status' => 'PEND3'
					);

				    $this->db->where('id', $id_cuti);
                    $this->db->update('ts_cuti', $data);

					$detail_cuti  = $this->Cuti_model->getDetailCuti($id_cuti);
					$id_pegawai   = $detail_cuti[0]->id_pegawai;
					$jns_cuti     = $detail_cuti[0]->jns_cuti;
					$tgl_dari     = $detail_cuti[0]->tgl_dari;
					$jns_hak_cuti = $detail_cuti[0]->jns_hak_cuti;
					$hari_cuti    = $detail_cuti[0]->hari_cuti;
					$alasan_cuti  = $detail_cuti[0]->alasan_cuti;

					$nip          = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
					$pin          = substr($nip, -4);


					$list_hari = $this->Cuti_model->getListHariCuti($id_cuti);
					for ($c=0; $c < count($list_hari) ; $c++) {
							$tgl_cuti = $list_hari[$c]->tanggal;
							$this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti );
					}


					$data = array(
							'check_kapuskec' => 1,
							'tgl_check2' => date('Y-m-d'),
							'status' => 'APPROVE'
					);
					//print_array($data);

			      $this->db->where('id', $id_cuti);
                  $this->db->update('ts_cuti', $data);

			}



        $this->session->set_flashdata('message', 'Pengajuan cuti telah disetujui');

        redirect('admin/pengajuan_cuti/detail_cuti/'.$id_cuti);
    }

		function tolak_cuti($id_cuti){

					$status        = $this->input->post('status');
					$alasan_tolak  = $this->input->post('alasan_tolak');

						  if($status=='PEND1'){
								$data = array(
										'check_kapuskel' => 2,
										'tgl_check' => date('Y-m-d'),
										'status' => 'REJECT',
										'alasan_tolak' => $alasan_tolak
								);

							}else if($status=='PEND2'){
								$data = array(
										'check_ktu' => 2,
										'tgl_check_ktu' => date('Y-m-d'),
										'status' => 'REJECT',
										'alasan_tolak' => $alasan_tolak
								);

							}else{
								$data = array(
										'check_kapuskec' => 2,
										'tgl_check2' => date('Y-m-d'),
										'status' => 'REJECT',
										'alasan_tolak' => $alasan_tolak
								);
							}


		        $this->db->where('id', $id_cuti);
		        $this->db->update('ts_cuti', $data);
		        $this->session->set_flashdata('message', 'Pengajuan cuti telah ditolak');

		        redirect('admin/pengajuan_cuti/detail/'.$id_cuti);


		}

    function cancel_cuti($id_cuti, $status, $pin){




		$detail_cuti   = $this->Cuti_model->getDetailCuti($id_cuti);
		$id_pegawai    = $detail_cuti[0]->id_pegawai;
		$jns_hak_cuti  = $detail_cuti[0]->jns_hak_cuti;
		$hari_cuti     = $detail_cuti[0]->hari_cuti;
		$status        = $detail_cuti[0]->status;
		$jns_cuti      = $detail_cuti[0]->jns_cuti;

		///$nip = $getNipPegawaiByID($id_pegawai)

		//klo sudah di acc sama kapus kecamatan, harus kembalikan cutinya
		$sisa_cuti  = $this->Cuti_model->getSisaCuti($id_pegawai, $jns_hak_cuti);
		$sisa_akhir = $sisa_cuti+$hari_cuti;
		$ket = 'Sisa  cuti dikembalikan, pembatalan cuti';
		$this->Cuti_model->insertLogCuti( $id_pegawai, $jns_hak_cuti, $jns_cuti, $id_cuti, $hari_cuti, $sisa_akhir, $ket);

		$this->Presensi_model->updateAbsensiCancelCuti($id_cuti, $pin);


        $this->db->where('id', $id_cuti);
        $this->db->set('status', 'CANCEL');
        $this->db->update('ts_cuti');

        $this->session->set_flashdata('message', 'Cuti telah dibatalkan');
        redirect('admin/pengajuan_cuti/detail_cuti/'.$id_cuti);
    }

	function accCutiKapuskel($id_cuti){
		//$id_cuti     = $this->input->post('id_cuti');

		$data = array(
			'check_kapuskel' => 1,
			'tgl_check' => date('Y-m-d'),
			'status' => 'PEND2'
		);


       $this->db->where('id', $id_cuti);
       $this->db->update('ts_cuti', $data);


	   $message =  '<div class="alert alert-success">Cuti berhasil disetujui</div> ';
	   $this->session->set_flashdata('message', $message);

	   redirect('cuti/detail_pengajuan_cuti/'.$id_cuti);


	}

	function setujui_cuti($id_cuti, $status){



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
        $this->db->update('ts_cuti', $data);
        $this->session->set_flashdata('message', 'Cuti telah disetujui');

		$message =  '<div class="alert alert-success">Cuti berhasil disetujui</div> ';
		$this->session->set_flashdata('message', $message);

		redirect('cuti/detail_pengajuan_cuti/'.$id_cuti);
    }


	// function edit_cuti($id_cuti){
	// 	$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
	// 	$this->load->view('pengajuan_cuti/edit_pengajuan_cuti', $data);
	// }

	function update_tanggal_cuti($id_cuti) {

		$tgl_cuti  =  $this->input->post('tgl_cuti');

        $explode = explode("-", $tgl_cuti);
        $dateFrom = $explode[0];
        $dateTo   = @$explode[1];
         //klo cuti cuma sehari
        if($dateTo==''){
            $dateTo = $dateFrom;
        }


		$dateFrom = str_replace("/", "-", $dateFrom);
		$dateTo = str_replace("/", "-", $dateTo);


		$start_date = format_db($dateFrom);
        $end_date   = format_db($dateTo);

		//echo $start_date.' sampai '.$end_date;

		$detail_cuti = $this->Cuti_model->getDetailCuti($id_cuti);
		$jns_cuti    = $detail_cuti[0]->jns_cuti;
		$jns_hak_cuti= $detail_cuti[0]->jns_hak_cuti;
		$id_pegawai  = $detail_cuti[0]->id_pegawai;
		$hari_cuti   = $detail_cuti[0]->hari_cuti;
		$status      = $detail_cuti[0]->status;


		$jamKerja     = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);

		if($start_date == $end_date){
			//cuma sehari
			echo 'cuti  sehari';
		}else{
			//lebih dari sehari
			echo 'cuti lebih dari sehari';
		}



		print_array($detail_cuti);


	}

	function update_data_cuti($id_cuti, $id_pegawai){

		$tgl_cuti  =  $this->input->post('tgl_cuti');

        $explode = explode("to", $tgl_cuti);
        $dateFrom = $explode[0];
        $dateTo   = @$explode[1];
         //klo cuti cuma sehari
        if($dateTo==''){
            $dateTo = $dateFrom;
        }


        $start_date = format_db($dateFrom);
        $end_date   = format_db($dateTo);

		$jns_cuti             =  $this->input->post('jns_cuti');

		$id_pegawai_pengganti = $this->input->post('id_pengganti');
		$jns_jam_kerja        = $this->input->post('jns_jam_kerja');
		$selisihhari          =  datediff('d', $start_date, $end_date)+1;




		if($jns_cuti==1){
		    //cuti tahunan
			if($jns_jam_kerja =='non_shift'){

				if($selisihhari > 1){
					//klo cuti lebih dari 1 hari
					$dataCuti     = $this->Cuti_model->getHariCuti($selisihhari, $start_date);
					$hariCuti     = $dataCuti[0];
					$listhariCuti = $dataCuti[1];


				}else{
					$hariCuti = 1;
					$listhariCuti = array($start_date);
				}




				$this->db->where('id_cuti', $id_cuti);
				$this->db->delete('ts_cuti_detail');


				for ($i=0; $i < count($listhariCuti) ; $i++) {
					$tanggal = $listhariCuti[$i];
					$this->Cuti_model->insertDataDetailCuti($id_cuti, $id_pegawai, $tanggal);
				}


			}else{

				$perhitunganCuti = hitungHariCuti($start_date, $end_date);
				$hariCuti     = $perhitunganCuti[0];
				$listhariCuti = $perhitunganCuti[1];

			}


		}else if($jns_cuti==2){
			//cuti melahirkan
			$perhitunganCuti = hitungHariCuti($start_date, $end_date);
			$hariCuti     = $perhitunganCuti[0];
			$listhariCuti = $perhitunganCuti[1];


		}else{
			//cuti alasan penting, cuti besar, dll
		}//close jenis cuti


			$newData = array(
				'jns_cuti' => $jns_cuti,
				'tgl_dari' => $start_date,
				'tgl_sampai' => $end_date,
				'hari_cuti' => $hariCuti,
			);

			$this->db->where('id', $id_cuti);
			$this->db->update('ts_cuti', $newData);


			$this->session->set_flashdata('message', 'Cuti telah berhasil diubah');

			redirect('admin/pengajuan_cuti/detail_cuti/'.$id_cuti);




	}



}
