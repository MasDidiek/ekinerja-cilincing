<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Absensi extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->model('Old_model');
         $this->load->model('Admin_cuti_model', 'acm');
        $this->load->model('Laporan_model');
        $this->load->helper('text');
        $this->Auth_model->cekAuthLogin();
         $this->load->model('Template_shift_model');
         $this->load->model('Shift_model');
        date_default_timezone_set('Asia/Jakarta');

    }


    function index()
    {
         $tahun = date('Y');
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['detail_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $data['dataRekap']    = $this->Pegawai_model->getRekapAbsensiPegawai($id_pegawai, $tahun);
        $this->load->view('my_absensi/index', $data);
    }


     function view_absensi(){

    // print_array($this->session->userdata);

        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        if (!$bulan) {
             $bulan = date('m');
        }

        if (!$tahun) {
              $tahun = date('Y');
        }

        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;


        $nip = $this->session->userdata('nip');
        $jns_pegawai = $this->session->userdata('jns_pegawai');
        //$pin = substr($nip, -4);

        //print_array($this->session->userdata);
        
        $pegawai = $this->Presensi_model->getDetailPegawaiByNip($nip, $jns_pegawai);
 //print_array($pegawai);

         $id_pegawai = $pegawai->id_pegawai;
         $pin = $pegawai->pin;
        

        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['cuti_pegawai'] = $this->acm->get_cuti_bulanan_absensi($id_pegawai, $bulan, $tahun);
        $data['absensi']      = $this->Presensi_model->get_absensi_pegawai($pin, $bulan, $tahun);
        $data['dataRekap']    = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
        $data['DinasLuar']    =  $this->Presensi_model->getDataPengajuanDLPerbulan($id_pegawai, $periode);

        $data['IzinSakit']    =  $this->Presensi_model->getDataIzinSakit($id_pegawai, 'ALL', $periode);

        $data['detailPegawai'] =  $pegawai;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['pin'] = $pin;
        $this->load->view('my_absensi/view_absensi', $data);
    }

    function view_raw_absensi(){

        $pin        = $this->input->get('pin');
        $bulan      = $this->input->get('bulan');
        $tahun      = $this->input->get('tahun');
        $id_puskesmas= $this->input->get('id_puskesmas');
        $id_pegawai      = $this->input->get('id_pegawai');
        
        $ip_address = $this->Master_model->getIpAddress($id_puskesmas);

        $get_absensi_raw    = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);

        $absensi_raw = [];

        foreach ($get_absensi_raw as $row) {

            $datetime = $row['DateTime'];

            $tgl = date('Y-m-d', strtotime($datetime));
            $bln = date('m', strtotime($datetime));
            $thn = date('Y', strtotime($datetime));
            $jam = date('H:i:s', strtotime($datetime));

            // filter bulan & tahun
            if ($bln != $bulan || $thn != $tahun) {
                continue;
            }

            // status
            $status = ($row['Status'] == 0) ? 'masuk' : 'pulang';

            $absensi_raw[$tgl][] = [
                'jam' => $jam,
                'status' => $status
            ];
        }

    

        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));

        
        $data['absensi']      = $this->Presensi_model->get_absensi_pegawai($pin, $bulan, $tahun);
        $data['dataRekap']    = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
        $data['detailPegawai'] =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['absensi_raw'] = $absensi_raw;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
         $data['pin'] = $pin;


        


         $this->load->view('my_absensi/view_raw_absensi', $data);
    }

    function update_data_absensi($pin, $ip_address, $bulan, $tahun)
    {
       $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        // =========================
        // 1. Ambil data dari mesin
        // =========================
        $absensi_raw = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);

        $qry = $this->db
                ->select('id_pegawai, jns_jam_kerja')
                ->get_where('mst_pegawai', ['pin' => $pin]);

            $row = $qry->row();

            if (!$row) {
                return; // pegawai tidak ditemukan
            }

            $id_pegawai = $row->id_pegawai;
            $jns_jam_kerja = $row->jns_jam_kerja;


            if($jns_jam_kerja=='non_shift'){

                 $qry      = $this->db->get_where('tbl_shift_template', ['bulan' => $bulan, 'tahun' => $tahun]);
                 $template = $qry->row();

                    if (!$template) {
                        return; // pegawai tidak ditemukan
                    }
                    $id_template = $template->id;

                  $this->update_shift($id_template, $pin, $bulan, $tahun, 'non_shift');
            }else{
                   $this->update_shift($id_pegawai, $pin, $bulan, $tahun, 'shift');
            }

    

        if (empty($absensi_raw)) {
            return;
        }

        foreach ($absensi_raw as $row) {

            $tanggal = date('Y-m-d', strtotime($row['DateTime']));
            $jam     = date('H:i:s', strtotime($row['DateTime']));

            // hanya proses bulan yg dipilih
            if (date('Y-m', strtotime($tanggal)) != $periode) {
                continue;
            }


            $cekData = $this->db
                ->where('pin', $pin)
                ->where('tanggal', $tanggal)
                ->where('jam', $jam)
                ->get('tbl_log_absensi')
                ->row();

            if(empty($cekData)){
                // ==================================
                // 2. Simpan ke table log (RAW ONLY)
                // ==================================
                $this->db->insert('tbl_log_absensi', [
                    'pin'       => $pin,
                    'tanggal'   => $tanggal,
                    'jam'       => $jam,
                    'datetime_log' => $row['DateTime'],
                    'source_ip' => $ip_address
                ]);
            }

            $kehadiran = $this->db
                ->where('pin', $pin)
                ->where('tanggal', $tanggal)
                ->get('tbl_kehadiran_harian')
                ->row();

            if (!$kehadiran) {
                continue; // kalau belum generate shift, skip
            }


            $kode_shift = $kehadiran->shift;
            $shift = $this->db
                    ->where('kode_shift', $kode_shift)
                    ->get('mst_shift_kerja')
                    ->row();

                if (!$shift) {
                    continue;
                }

            $jam_masuk_shift  = $shift->jam_masuk;
            $jam_pulang_shift = $shift->jam_pulang;


            $jam_absen = $this->tentukan_jam_absen($pin, $tanggal, $jam_masuk_shift, $jam_pulang_shift);
            $jam_masuk  = $jam_absen[0]; // log pertama
            $jam_pulang = $jam_absen[1];  // log terakhir

            if($jam_masuk_shift=='00:00:00'){
                $jam_masuk = null;
            }


            if($jam_pulang_shift=='00:00:00'){
                $jam_pulang = null;
            }

           

            if ($kode_shift == 'OFF') {
                continue;
            }

            $this->db->where('id', $kehadiran->id);
            $this->db->update('tbl_kehadiran_harian', [
                'jam_masuk'  => $jam_masuk,
                'jam_pulang' => $jam_pulang,
               
            ]);


        }

      
        $this->session->set_flashdata('message', createMessageInfo('Absensi berhasil diupdate', 'success'));
        redirect('absensi/view_absensi?bulan='.$bulan.'&tahun='.$tahun);
    }

    private function tentukan_jam_absen($pin, $tanggal, $jam_masuk_shift, $jam_pulang_shift)
    {
        $logs = $this->db
            ->where('pin', $pin)
            ->where('tanggal', $tanggal)
            ->order_by('jam', 'ASC')
            ->get('tbl_log_absensi')
            ->result();

        if (empty($logs)) {
            return [null, null];
        }

        $target_masuk  = strtotime($tanggal . ' ' . $jam_masuk_shift);
        $target_pulang = strtotime($tanggal . ' ' . $jam_pulang_shift);

        $closest_masuk  = null;
        $closest_pulang = null;

        $min_diff_masuk  = PHP_INT_MAX;
        $min_diff_pulang = PHP_INT_MAX;

        foreach ($logs as $log) {

            $log_time = strtotime($tanggal . ' ' . $log->jam);

            // cari paling dekat ke jam masuk
            $diff_masuk = abs($log_time - $target_masuk);
            if ($diff_masuk < $min_diff_masuk) {
                $min_diff_masuk  = $diff_masuk;
                $closest_masuk   = $log->jam;
            }

            // cari paling dekat ke jam pulang
            $diff_pulang = abs($log_time - $target_pulang);
            if ($diff_pulang < $min_diff_pulang) {
                $min_diff_pulang = $diff_pulang;
                $closest_pulang  = $log->jam;
            }
        }

        return [$closest_masuk, $closest_pulang];
    }


    public function repair_periode_reguler($pin, $bulan, $tahun)
    {
        $this->Absensi_model->updateShiftKerja($pin, $bulan, $tahun);

        redirect('absensi/view_absensi?bulan='.$bulan.'&tahun='.$tahun);
    }


    function change_bulan($bulan, $tahun=2026)
    {
        $this->session->set_userdata('periode_bulan', $bulan);
        $this->session->set_userdata('periode_tahun', $tahun);
     
        redirect('absensi/view_absensi');
    }



    function updateShiftReguler($pin, $data_shift)
    {

        $dateNow = date('Y-m-d');

        //print_array($data_shift);
        for ($i = 0; $i < count($data_shift); $i++) {

            $tanggal = $data_shift[$i]->tanggal;

            //echo $tanggal;

            $libur = $this->db
                ->where('tgl', $tanggal)
                ->get('ts_hari_libur')
                ->row();

            $status_detail = null;
            $keterangan = null;


            //print_array($libur);
            if ($libur) {
                $shift = 'OFF';
                $status_detail = 'LIBUR NASIONAL';
                $keterangan = $libur->keterangan;
            } else {


                $shiftDetail = $this->db->where('id', $data_shift[$i]->shift_id)->get('mst_shift_kerja')->row();
                //print_array($shiftDetail);
                $shift = $shiftDetail->kode_shift;
            }

            // ============================
            // Cek apakah data sudah ada
            // ============================
            // $cek = $this->db
            //     ->where('pin', $pin)
            //     ->where('tanggal', $tanggal)
            //     ->get('tbl_kehadiran_harian')
            //     ->row();

            $sql = $this->db->get_where('tbl_kehadiran_harian', array('tanggal' => $tanggal, 'pin' => $pin));
            $kehadiran = $sql->row();
           // print_array($row);


            $tanggal2  = strtotime($tanggal);
            $dateNow  = strtotime(date('Y-m-d'));
            if($tanggal2 > $dateNow ){
                $status = '-';
            }else{

               if ($kehadiran->shift != 'OFF') {

                    if (empty($kehadiran->jam_masuk) && empty($kehadiran->jam_pulang)) {
                        $status = 'ALPHA';
                    } else {
                        $status = 'HADIR';
                    }

                } else {
                    $status = 'OFF';
                }

            
            }


            if (!$kehadiran) {


                // INSERT jika belum ada
                $this->db->insert('tbl_kehadiran_harian', [
                    'pin' => $pin,
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'status' => $status,
                    'status_detail' => $status_detail,
                    'keterangan' => $keterangan,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'telat_menit' => 0,
                    'p_awal_menit' => 0
                ]);
            } else {



                // UPDATE shift & detail saja
                $this->db->where('id', $kehadiran->id);
                $this->db->update('tbl_kehadiran_harian', [
                    'shift' => $shift,
                    'status' => $status,
                    'status_detail' => $status_detail,
                    'keterangan' => $keterangan
                ]);
            }
        }


        return true;
    }



    function update_shift($id_template, $pin, $bulan, $tahun, $jns_shift = 'non_shift')
    {

        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));


        if ($jns_shift == 'non_shift') {
            $detail   = $this->Template_shift_model->get_detail($id_template);

            $this->updateShiftReguler($pin, $detail);
        } else {
            //jam kerja khusus pegawai yg  shift2an

            //jika jam kerja shift, maka id_template adalah id_pegawai untuk mengambil data shift perbulan dari tabel shift_kerja
            $id_pegawai = $id_template;
            $dataShift = $this->Shift_model->getShiftPerbulanById($id_pegawai, $periode);

            //print_array($dataShift);


            foreach ($dataShift as $shift_kerja) {
                $shift   = $shift_kerja->shift;
                $tanggal = $shift_kerja->tanggal;

                //$cekAbsen = $this->Presensi_model->cekAbsenExist($tanggal, $pin, 'id');
                $sql = $this->db->get_where('tbl_kehadiran_harian', array('tanggal' => $tanggal, 'pin' => $pin));
                $row = $sql->row();
                //print_array($row);

                if (empty($row)) {
                    //insert shift
                    $this->db->insert('tbl_kehadiran_harian', [
                        'pin' => $pin,
                        'tanggal' => $tanggal,
                        'shift' => $shift,
                        'status' => ($shift == 'OFF') ? 'OFF' : 'ALPHA',
                        'status_detail' => null,
                        'keterangan' => null,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'telat_menit' => 0,
                        'p_awal_menit' => 0
                    ]);
                } else {
                    //update shift
                    $id = $row->id;
                    $this->db->where('id', $id);
                    $this->db->set('shift', $shift);
                    $this->db->update('tbl_kehadiran_harian');
                }
            }
            //exit;
        }

        return true;
    }

   

    function create_session()  {
        $tahun = date('Y');
        $bulan = date('m');
        $this->session->set_userdata('periode_tahun', $tahun);
        $this->session->set_userdata('periode_bulan', $bulan);


        redirect('absensi/view_absensi');
    }


    function lihat_absensi($bulan, $tahun)
    {
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['detail_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $this->load->view('my_absensi/lihat_absensi', $data);
    }


    function absensi_pegawai(){
       
		$id_user   = $this->session->userdata('id_user');
		$usergroup = $this->session->userdata('usergroup');
		$jabatan      = $this->session->userdata('jabatan');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		$id_pkm        = $this->session->userdata('id_pkm');
		
		
		//	print_array($this->session->userdata );

		if($periode_bulan=='') {
			$bulan = date('m');
			$tahun = date('Y');


			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);
			$this->session->set_userdata('id_pkm', 1);

		  }else{
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;

		  }


	      $periode       = $tahun . '-' . $bulan;
	      $periode       = date('Y-m', strtotime($periode));

		  $id_validator = $this->session->userdata('id_pegawai');
		  $id_pj_sess = $this->session->userdata('id_pj');
		  //$thn_anggaran = date('Y');
		  $thn_anggaran = 2024;

		  if($id_pj_sess != ''){
			  $id_validator = $id_pj_sess;
		  }

		  //print_array($this->session->userdata);

		$data['puskesmas']        = $this->Presensi_model->getListPuskesmas();
		$data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
		//$data['pegawai']  = $this->Pegawai_model->getListPegawaiByValidator($id_validator, $thn_anggaran);
		$data['validator'] = $this->Pegawai_model->getValidator();
		
		
		
		$config = array();
        $config['base_url'] = base_url().'admin/presensi/index_v2/';
        $total_rows =  $this->Pegawai_model->get_total_pegawai($id_validator, $thn_anggaran);
        $config['total_rows'] = $total_rows ;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        
        $config['full_tag_open'] = '<ul class="flex flex-wrap items-center gap-2">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a href="#" class="inline-flex items-center justify-center bg-white dark:bg-zink-700 size-8 transition-all duration-150 ease-linear border border-slate-200 dark:border-zink-500 rounded text-slate-500 dark:text-zink-200 hover:text-custom-500 dark:hover:text-custom-500 hover:bg-custom-50 dark:hover:bg-custom-500/10 focus:bg-custom-50 dark:focus:bg-custom-500/10 focus:text-custom-500 dark:focus:text-custom-500 [&amp;.active]:text-custom-50 dark:[&amp;.active]:text-custom-50 [&amp;.active]:bg-custom-500 dark:[&amp;.active]:bg-custom-500 [&amp;.active]:border-custom-500 dark:[&amp;.active]:border-custom-500 [&amp;.disabled]:text-slate-400 dark:[&amp;.disabled]:text-zink-300 [&amp;.disabled]:cursor-auto active">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li> ';
        $config['num_tag_close'] = '</li>';


        $this->pagination->initialize($config);

        // Fetch data
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

    	$data['total_rows']  = $total_rows ;
		$data['pegawai']  = $this->Pegawai_model->getListPegawaiByValidator_page($id_validator, $thn_anggaran, $config['per_page'], $page);
		
	//	print_array($data['pegawai'] );
		$data['pagination'] = $this->pagination->create_links();
		
		$data['numAllPegawai']    = $this->Pegawai_model->countPegawaiAktif($thn_anggaran, 'non_pns');
		$data['absensiSesuai']    = $this->Presensi_model->countAbsenSesuai($periode, 1);
		$data['absensiBlmSesuai'] = $this->Presensi_model->countAbsenSesuai($periode);
		
	
        $this->load->view('absensi/absensi_pegawai', $data);
    }

    function rekap_absensi(){

		$id_pegawai = $this->session->userdata('id_pegawai');
		$nip  = $this->session->userdata('nip');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}


		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));;

		$data['detail_pegawai']   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiTahunan($id_pegawai, $tahun);
		$data['nip'] = $nip;

		$data['master_cuti'] = $this->Master_model->getlistCuti();
		$this->load->view('my_absensi/rekap_absensi', $data);

    
	}





    function update_absensi($bulan, $tahun, $pin, $id_puskesmas)
    {
        $id_pegawai = $this->session->userdata('id_pegawai');
        $this->db->where('id_puskesmas', $id_puskesmas);
        $qry = $this->db->get('tbl_mesin_absensi');
        $row = $qry->result();

        $ip_address = $row[0]->ip_address;

        $dataPresensi = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);

        #echo $ip_address;

        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));


        for ($i = 0; $i < count($dataPresensi); $i++) {

            $DateTime = $dataPresensi[$i]['DateTime'];
            $pinMesin      = $dataPresensi[$i]['pin'];
            $Status   = $dataPresensi[$i]['Status'];

            $periode_db = date('Y-m', strtotime($DateTime));

            $thn = date('Y', strtotime($DateTime));

            $cekAbsen  = $this->Presensi_model->cekExistAbsensi($pinMesin, $DateTime);

            if ($cekAbsen == 0 && ($thn == $tahun)) {
                $this->Presensi_model->insertAbsensi($DateTime, $pinMesin, $Status);
            }
        }

        $dataAbsensi  = $this->Presensi_model->getAbsenBulanan($pin, $periode);
        //  print_array($dataAbsensi);

        //  exit;

        for ($a = 0; $a < count($dataAbsensi); $a++) {


            $datetime       = $dataAbsensi[$a]->tanggal;
            $status_absen  = $dataAbsensi[$a]->status;
            $id_absen  = $dataAbsensi[$a]->id;

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

            


            $cekAbsenExist = $this->Presensi_model->cekAbsenExist($tanggal, $pin);
            if ($cekAbsenExist == false) {

                $this->db->insert('tbl_absensi', $newArray);
            } else {


                $this->db->where('pin', $pin);
                $this->db->where('tanggal', $tanggal);
                $this->db->update('tbl_absensi', $newArray);
            }
        }


        $pesan =  createMessageInfo('Absensi berhasil diupdate', 'success');
        $this->session->set_flashdata('message', $pesan);
        redirect('absensi/lihat_absensi/' . $bulan . '/' . $tahun);


        #print_array($dataPresensi);
    }

    function pengajuan_dinas_luar()
    {

        $periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}


		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));;

        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['pengajuan_dinas_luar'] = $this->Absensi_model->getListPengajuanDL($id_pegawai, $tahun);
        $this->load->view('my_absensi/dinas_luar', $data);
    }

    function delete_pengajuan_dl($id)
    {

        $path_surtug = 'uploads/surat_tugas/';
        $path_photo  = 'uploads/photo_dinas_luar/thumb/';

        $pengajuan_dinas_luar = $this->Absensi_model->getDetailPengajuanDL($id);
        $surtug = $pengajuan_dinas_luar[0]->surtug;
        $photo  = $pengajuan_dinas_luar[0]->photo;


        $fileImage     = $path_photo . $photo;
        if (file_exists($fileImage)) {
            unlink($fileImage);
        }

        $fileImageSurtug     = $path_surtug . $surtug;
        if (file_exists($fileImageSurtug)) {
            unlink($fileImageSurtug);
        }

        $this->db->where('id', $id);
        $this->db->delete('pengajuan_dinas_luar');


        $pesan =  createMessageInfo('Pengajuan dinas luar berhasil dihapus', 'success');
        $this->session->set_flashdata('message', $pesan);
        redirect('absensi/pengajuan_dinas_luar');
    }

    function detail_pengajuan_dl($id)
    {


        $data['pengajuan_dinas_luar'] = $this->Absensi_model->getDetailPengajuanDL($id);
        $this->load->view('my_absensi/detail_dinas_luar', $data);
    }

    function insertPengajuanDinasLuar()
    {

       // print_array($this->input->post());
      

        $now = date('Y-m-d H:i:s');
        

        $tgl_dl = $this->input->post('tgl_dl');
        $jns_dl = $this->input->post('jns_dl');
        $keterangan = $this->input->post('keterangan');
        $cameraInput = $this->input->post('cameraInput');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $start_date = format_db($tgl_dl);


        $uploadPhoto = $this->Absensi_model->uploadPhotoDL();

        if($uploadPhoto == false){
             //klo  gagal
            $this->session->set_flashdata('status', 'gagal');
            exit;
           // redirect('dashboard/my_dashboard');
        }else{

                $file_name = $uploadPhoto;
                //klo berhasil
                $fileName = basename($_FILES["cameraInput"]["name"]);

                $reversedString = strrev($fileName);
                $explodeString  = explode(".", $reversedString);
                $rs = $explodeString[0];
                $fileType   = strrev($rs);


                $nama_user   =  $this->session->userdata('nama');
                $id_pegawai  =  $this->session->userdata('id_pegawai');


                $namaPeg = strtolower($nama_user);
                $namaPeg = url_title($namaPeg);
                $namaPeg = str_replace("-", "_", $namaPeg);


                $date = date('YmdHi');

                $target_dir = "./uploads/photo_dinas_luar/";

                $nameSmallImage = $namaPeg . '_' .  $date . '.' . $fileType;
                $resized_file = $target_dir . 'thumb/' . $nameSmallImage;
                $fileUploaded = $target_dir.$file_name.'.'.$fileType;

                resizeImage($fileUploaded, 800, 600, $resized_file);

                #delete image exis
                $fileImageName     = $target_dir . $file_name.'.'.$fileType;

                if (file_exists($fileImageName)) {
                    unlink($fileImageName);
                }


                $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : 'Unknown';
                $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : 'Unknown';



                $namaFileDB = $nameSmallImage;

                $id = $this->Absensi_model->insertPengajuanDL($id_pegawai, $start_date, $namaFileDB, $latitude, $longitude);
              
                $this->session->set_flashdata('status', 'success');
                $this->session->set_flashdata('message', 'Pengajuan Dinas Luar berhasil dikirim');


                redirect('absensi/detail_pengajuan_dl/'.$id);


        }



    }

    function upload_surat_tugas($id, $tgl_dl){
        $nama_user   =  $this->session->userdata('nama');
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        //$tanggal     =  $this->input->post('tanggal');

        $xplod       = explode(' ', $nama_user);
        $first_name  = $xplod[0];
        $tanggal_dl  = date('Ymd', strtotime($tgl_dl));


        $file_title  = 'surtug_'.$first_name.'_'.$tanggal_dl;
        $nama_file   =  $file_title;
        $nama_file  .= $_FILES['filedocs']['name'] ;

        $file_name       = $file_title.'_'. $jns_dl;
        $file_name       = url_title($file_name);

        $namaFileDB      =  $file_name.'.pdf';
        $numchar         = strlen($namaFileDB);

        $path = 'surat_tugas';
        $uploadFile = $this->Master_model->uploadFilePDF($path, $file_name, '5000');


        if ($uploadFile) {

            $pesan =  createMessageInfo('Surat Tugas berhasil diupload', 'success');
            $this->session->set_flashdata('message', $pesan);
            $this->db->where('id', $id);
            $this->db->set('surtug', $namaFileDB);
            $this->db->update('pengajuan_dinas_luar');

        }


        redirect('absensi/detail_pengajuan_dl/'.$id );


    }

    function pengajuan_izin_sakit() {
        $tahun = $this->uri->segment(3)??2026;
       // $tahun = date('Y');
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['pengajuan_izin'] = $this->Absensi_model->getListPengajuanIzinSakit($id_pegawai, $tahun, 'IZIN');
        $data['pengajuan_sakit'] = $this->Absensi_model->getListPengajuanIzinSakit($id_pegawai, $tahun,'SAKIT');
        $this->load->view('my_absensi/izin_sakit', $data);

    }


    function pengajuan_izin()  {
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['pengajuan_izin_sakit'] = $this->Absensi_model->getListPengajuanIzinSakit($id_pegawai);
        $this->load->view('my_absensi/pengajuan_izin', $data);
    }


    function pengajuan_sakit()  {
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['pengajuan_izin_sakit'] = $this->Absensi_model->getListPengajuanIzinSakit($id_pegawai);
        $this->load->view('my_absensi/izin_sakit', $data);
    }

    function izin_sakit()
    {
        $tahun = $this->uri->segment(3)??2026;
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['pengajuan_izin'] = $this->Absensi_model->getListPengajuanIzinSakit($id_pegawai, $tahun, 'IZIN');
        $data['pengajuan_sakit'] = $this->Absensi_model->getListPengajuanIzinSakit($id_pegawai, $tahun,'SAKIT');
        $this->load->view('my_absensi/izin_sakit', $data);
    }


    function simpan_pengajuan_izin_sakit()
    {
        $id_pegawai = $this->session->userdata('id_pegawai');
        $jenis_absen = $this->input->post('jenis_absen'); // IZIN atau SAKIT
        $tanggal = $this->input->post('tanggal');
        $keterangan = $this->input->post('keterangan');
        $file_image = '';

        if($jenis_absen=='IZIN'){
            $jns_izin  = $this->input->post('jns_izin');
            $jns_sakit = 0;
        }else{
            $jns_sakit = $this->input->post('jenis_sakit');
            $jns_izin  = 0;
        }

        if (!empty($_FILES['file_image']['name'])) {
            $config['upload_path'] = './uploads/surat_izin/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 2000;
            $config['file_name'] = 'izin_' . $id_pegawai . '_' . date('YmdHis');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('file_image')) {
                $upload_data = $this->upload->data();
                $file_image = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('status', 'gagal');
                $this->session->set_flashdata('message', $this->upload->display_errors());
                redirect('absensi/izin_sakit');
                return;
            }
        }

        $data = [
            'id_pegawai' => $id_pegawai,
            'jenis_absen' => $jenis_absen,
            'jns_izin' => $jns_izin,
            'jns_sakit' => $jns_sakit,
            'tanggal' => $tanggal,
            'keterangan' => $keterangan,
            'file_image' => $file_image,
            'create_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('pengajuan_izin_sakit', $data);

        $this->session->set_flashdata('status', 'success');
        $this->session->set_flashdata('message', 'Pengajuan izin/sakit berhasil disimpan.');
        redirect('absensi/izin_sakit');
    }




    function lihat_file_upload(){

        $file_image       =  $this->input->post('file_image');

        echo '<img src="'.base_url().'uploads/surat_izin/'. $file_image.'" style="width:400px;">'; 
    }

    function detail_pengajuan_sakit($id_pengajuan)  {


        $data['detail'] = $this->Absensi_model->getDetailPengajuanIzinSakit($id_pengajuan);

        $this->load->view('absensi/detail_pengajuan_sakit', $data);


    }



    function delete_pengajuan_izin_sakit($id, $file_name='')
    {

        $path = 'uploads/surat_izin/';


        if (file_exists($path . $file_name)) {
            unlink($path . $file_name);
        }

        $this->db->where('id', $id);
        $this->db->delete('pengajuan_izin_sakit');

        $this->session->set_flashdata('status',  200);
        $this->session->set_flashdata('message', 'Pengajuan izin /  sakit berhasil dihapus. ' );
        redirect('absensi/izin_sakit');
    }
}
