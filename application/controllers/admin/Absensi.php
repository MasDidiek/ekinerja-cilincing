<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Absensi  extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('text');
        $this->Auth_model->cekAuthLogin();
        $this->load->model('Admin_cuti_model', 'acm');
        $this->load->model('Shift_model');
        $this->load->model('Template_shift_model');
    }

    function rekap_absensi()
    {


        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $jns_pegawai = $this->session->userdata('status_pegawai');

        $id_pj = $this->session->userdata('id_pj');
        if ($id_pj == '') {
            $this->session->set_userdata('id_pj', $this->session->userdata('id_pegawai'));
        }


        $periode       = $periode_tahun . '-' . $periode_bulan;
        $periode       = date('Y-m', strtotime($periode));

        $qry = $this->db->get_where('mst_pegawai', ['id_pegawai' => $id_pj]);
        $detValidator =  $qry->row();
        $id_puskesmas = $detValidator->id_puskesmas;
        $id_klaster = $detValidator->klaster;


        $data['bulan'] = $periode_bulan;
        $data['tahun'] = $periode_tahun;
        $data['validator'] = $this->Pegawai_model->getValidator();



        $data['data_rekap']     = $this->Presensi_model->getDataRekapAbsensi($periode, 'mst_pegawai.nama', $jns_pegawai, $id_puskesmas, $id_klaster);
        $this->load->view('admin/absensi/rekap_absensi', $data);
    }

    function view_absensi_pegawai($pin, $bulan, $tahun, $jns_pegawai = '')
    {

        if ($jns_pegawai == '') {

            $jns_pegawai =  $this->session->userdata('status_pegawai');
            if ($jns_pegawai == '') {
                $jns_pegawai = 'non_pns';
            }
        }
        $this->session->set_userdata('status_pegawai',  $jns_pegawai);

        $pegawai = $this->Presensi_model->getDetailPegawaiByPin($pin, $jns_pegawai);
        $id_pegawai = $pegawai->id_pegawai;


        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['cuti_pegawai'] = $this->acm->get_cuti_bulanan_absensi($id_pegawai, $bulan, $tahun);

        $data['absensi']      = $this->Presensi_model->get_absensi_pegawai($pin, $bulan, $tahun);
        $data['dataRekap']    = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
        $data['DinasLuar']    =  $this->Presensi_model->getDataPengajuanDLPerbulan($id_pegawai, $periode);

        $data['IzinSakit']    = $this->Presensi_model->getDataIzinSakit($id_pegawai, 'ALL', $periode);
        $data['template']     = $this->Template_shift_model->get_all();

        $data['detailPegawai'] =  $pegawai;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $this->load->view('admin/absensi/view_absensi_pegawai', $data);
    }

    function update_absensi_cuti($pin, $id_cuti, $bulan, $tahun)
    {
        $list_hari = $this->acm->getlistHariCuti($id_cuti);

        //print_array($list_hari);

        $approveCuti = $this->acm->approveKapusInduk($id_cuti);
        if (!$approveCuti) {
            show_error("Gagal memproses persetujuan cuti");
            return;
        }

        $qry = $this->db->get_where('ts_pengajuan_cuti', ['id' => $id_cuti]);
        $cuti =  $qry->row();


        $jenis_cuti = $cuti->jenis_cuti;

        if ($jenis_cuti == 2) {
            //cuti bersalin

            $tgl_mulai   = new DateTime($cuti->tgl_mulai);
            $tgl_selesai = new DateTime($cuti->tgl_selesai);

            // Supaya tanggal akhir ikut terproses
            $tgl_selesai->modify('+1 day');

            $interval = new DateInterval('P1D');
            $periode  = new DatePeriod($tgl_mulai, $interval, $tgl_selesai);

            foreach ($periode as $tgl) {

                $hari = $tgl->format('N');
                // 6 = Sabtu, 7 = Minggu

                if ($hari != 6 && $hari != 7) {

                    $tanggal_loop = $tgl->format('Y-m-d');

                    $this->Presensi_model->update_absensi_cuti(
                        $pin,
                        $tanggal_loop,
                        $cuti->alasan_cuti
                    );
                }
            }
        } else {
            //cuti tahunan, sakit, alasan penting
            foreach ($list_hari as $ls) {
                $this->Presensi_model->update_absensi_cuti($pin, $ls->tgl_cuti, $cuti->alasan_cuti);
            }
        }


        // print_array($cuti);
        // exit;


        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }

    function create_session_periode()
    {
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $id_validator = $this->input->post('id_validator');
        $status_pegawai = $this->input->post('status_pegawai');

        //$tahun = 2025;
        $this->session->set_userdata('periode_tahun', $tahun);
        $this->session->set_userdata('periode_bulan', $bulan);

        $this->session->set_userdata('id_pj', $id_validator);
        $this->session->set_userdata('status_pegawai', $status_pegawai);


        redirect('admin/absensi/rekap_absensi');
    }

    function view_raw_absensi($pin, $ip_address, $bulan, $tahun, $jns_pegawai='')
    {


        $data['absensi_raw']    = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['detailPegawai'] = $this->Presensi_model->getDetailPegawaiByPin($pin, $jns_pegawai);
        $this->load->view('admin/absensi/view_raw_absensi', $data);
    }

    function insertAbsenPengajuanDL($id, $pin, $bulan, $tahun)
    {
        $qry = $this->db->get_where('pengajuan_dinas_luar', array('id' => $id));
        $dinasLuar = $qry->result();

        $jns_dl = $dinasLuar[0]->jns_dl;
        $keterangan = $dinasLuar[0]->keterangan;
        $tanggal = $dinasLuar[0]->tanggal;


        $this->Presensi_model->saveAbsensiDL($pin, $tanggal, $jns_dl, $keterangan);

        $this->db->where('id', $id);
        $this->db->set('status', 1);

        $this->db->update('pengajuan_dinas_luar');

        $this->session->set_flashdata('message', ' Data shift berhasil disimpan');
        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }
    function insertAbsenPengajuanIzinSakit($id, $pin, $bulan, $tahun)
    {
        $qry = $this->db->get_where('pengajuan_izin_sakit', array('id' => $id));
        $is = $qry->row();

        $tanggal = $is->tanggal;
        $jenis_absen = $is->jenis_absen;
        $jns_izin = $is->jns_izin;
        $jns_sakit = $is->jns_sakit;
        $ket  = $is->keterangan;

        if ($jenis_absen == 'IZIN') {
            if ($jns_izin == 1) {
                $status_detail = 'FULL';
            } else if ($jns_izin == 2) {
                $status_detail = 'AWAL';
            } else {
                $status_detail = 'AKHIR';
            }
        } else {

            if ($jns_sakit == 1) {
                $status_detail = 'SAKIT TANPA SK';
            } else {
                $status_detail = 'SAKIT DGN SK';
            }
        }

        $this->Presensi_model->saveAbsensiIzinSakit($pin, $tanggal, $jenis_absen, $status_detail, $ket);

        $this->db->where('id', $id);
        $this->db->set('status', 1);

        $this->db->update('pengajuan_izin_sakit');

        $this->session->set_flashdata('message', ' Data  berhasil disimpan');
        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }



    function updateHariLibur()
    {
        $this->Presensi_model->update_absensi_khusus('ts_hari_libur');
    }

    public function update_shift_pegawai($pin, $bulan, $tahun)
    {
        // $this->db->where('pin', $pin);
        // $this->db->where('MONTH(tanggal)', $bulan);
        // $this->db->where('YEAR(tanggal)', $tahun);
        // $data = $this->db->get('tbl_kehadiran_harian')->result();

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $data = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);

        foreach ($data as $row) {

            $tanggal = $row->tanggal;
            $hari = date('N', strtotime($tanggal));
            // 6 = Sabtu, 7 = Minggu

            $shift_baru = $row->shift;

            // =========================
            // 1. Jika Libur Nasional
            // =========================
            if ($row->status_detail == 'LIBUR NASIONAL') {
                $shift_baru = 'OFF';
            }

            // =========================
            // 2. Jika Sabtu/Minggu
            // =========================
            elseif ($hari == 6 || $hari == 7) {
                $shift_baru = 'OFF';
            }

            $this->db->where('id', $row->id);
            $this->db->update('tbl_kehadiran_harian', [
                'shift' => $shift_baru
            ]);
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

            // print_array($dataShift);


            foreach ($dataShift as $shift_kerja) {
                $shift   = $shift_kerja->shift;
                $tanggal = $shift_kerja->tanggal;

                $cekAbsen = $this->Presensi_model->cekAbsenExist($tanggal, $pin, 'id');

                if ($cekAbsen == 0) {
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
                    $this->db->where('id', $cekAbsen);
                    $this->db->set('shift', $shift);
                    $this->db->update('tbl_kehadiran_harian');
                }
            }
            //exit;
        }


        // exit;

        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }

     function update_absensi(){
        $pin = $this->input->get('pin');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        $periode = $tahun.'-'.$bulan;
        $periode = date('Y-m', strtotime($periode));



        $this->db->where('pin', $pin);
        $this->db->where('tanggal >=', '2026-05-01');

       $qry = $this->db->get('ts_import_absensi');
       $absensi_raw = $qry->result();
     //  print_array($absensi_raw);

       
        foreach ($absensi_raw as $row) {

            $tanggal = date('Y-m-d', strtotime($row->tanggal));
            $jam     = date('H:i:s', strtotime($row->tanggal));

            $status = $row->status;

           // echo $tanggal.' - '.$jam.' - '.$status.'<br>';
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


                //echo 'Tanggal : '.$tanggal.' - Jam : '.$jam.' - Status : '.$status.'<br>';

                print_array($cekData);

            if (empty($cekData)) {
                // ==================================
                // 2. Simpan ke table log (RAW ONLY)
                // ==================================
                $this->db->insert('tbl_log_absensi', [
                    'pin'       => $pin,
                    'tanggal'   => $tanggal,
                    'jam'       => $jam,
                    'datetime_log' => $row->tanggal,
                    'source_ip' => 0
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


           // print_array($kehadiran);

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

           // print_array($jam_absen);


            if ($jam_masuk_shift == '00:00:00') {
                $jam_masuk = null;
            }


            if ($jam_pulang_shift == '00:00:00') {
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

         redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }


    public function sinkron_absensi()
    {
        $id_pegawai = $this->input->get('id_pegawai');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        $select = 'id_pegawai, pin, jns_jam_kerja, id_puskesmas';
        $pegawai = $this->Pegawai_model->getDataPegawai($id_pegawai, $select);

        $pin           = $pegawai->pin;
        $jns_jam_kerja = $pegawai->jns_jam_kerja;
        $id_puskesmas   = $pegawai->id_puskesmas;

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        // =========================
        // 1. Ambil data dari mesin
        // =========================

        //jika pin diawali angka 0, maka harus ditambahkan angka 1 didepannya, contoh: 0124 -> 10124
        $pin_mesin = convertPinMesin($pin);


        
        $ip_address = $this->Master_model->getIpAddress($id_puskesmas);
       // echo $ip_address.' - '.$pin_mesin.'<br>';


        $absensi_raw = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);
        if(empty($absensi_raw)){
            //klo proses tarik data dari mesin menggunakan pin dengan format lama (tanpa tambahan angka 1 didepan), maka coba tarik data menggunakan pin asli tanpa tambahan angka 1
           $absensi_raw = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin_mesin);
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

            if (empty($cekData)) {
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

            if ($jam_masuk_shift == '00:00:00') {
                $jam_masuk = null;
            }


            if ($jam_pulang_shift == '00:00:00') {
                $jam_pulang = null;
            }

            // echo $jam_masuk_shift.' s/d '. $jam_pulang_shift;

            // echo ' &nbsp;&nbsp;&nbsp;&nbsp;'.$jam_masuk.' -- '.$jam_pulang.' &nbsp;&nbsp;&nbsp;&nbsp;->&nbsp;&nbsp;&nbsp;';


            // $jam_masuk = null;
            // $jam_pulang = null;
            // echo $tanggal.' -'.$kode_shift.'<Br>';

            if ($kode_shift == 'OFF') {
                continue;
            }

            $this->db->where('id', $kehadiran->id);
            $this->db->update('tbl_kehadiran_harian', [
                'jam_masuk'  => $jam_masuk,
                'jam_pulang' => $jam_pulang,

            ]);
        }


        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
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

    public function update_rekap_absensi($id_pegawai, $pin, $bulan, $tahun)
    {
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $kehadiran  = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);

        $no = 1;

        foreach ($kehadiran as $row) {

            $shift       = $row->shift;
            $status      = $row->status;
            $tanggal     = $row->tanggal;
            $jam_masuk   = $row->jam_masuk;
            $jam_pulang  = $row->jam_pulang;

            $telat = 0;
            $pulang_awal = 0;

            if ($this->updateStatusIzinSakit($row, $id_pegawai, $tanggal)) {
                continue;
            }

            if ($this->updateStatusDL($row, $id_pegawai, $tanggal)) {
                continue;
            }

            if($shift == 'OFF' && $status != 'CUTI'){
                $this->update_status($row->id, 'OFF', null);
                $this->update_menit($row->id, 0, 0);
                continue;
            }

            if ($shift == 'OFF' && $status == 'OFF') {
                $this->update_menit($row->id, 0, 0);
                $this->update_status($row->id, 'OFF', null);
                continue;
            }

            if ($shift == 'L-OFF') {
                if (empty($jam_pulang)) {
                    $pulang_awal = 150;
                     $this->update_status($row->id, 'ALPHA', null);
                }else{
                    $this->update_status($row->id, 'HADIR', null);
                }
                $this->update_menit($row->id, 0, $pulang_awal);
                continue;
            }

            $detailShift = $this->Presensi_model->detailShiftByKode($shift, 'row');

            if ($detailShift) {

                $jam_masuk_shift  = $detailShift->jam_masuk;
                $jam_pulang_shift = $detailShift->jam_pulang;



                // SHIFT NORMAL (masuk dan pulang di hari yang sama)
                if ($jam_masuk_shift != '00:00:00' && $jam_pulang_shift != '00:00:00') {

            
                    // hitung telat
                    if (!empty($jam_masuk)) {
                        $telat = max(0, (strtotime($jam_masuk) - strtotime($jam_masuk_shift)) / 60);
                    } else {
                        // tidak absen masuk
                        $telat = 300;
                    }

                    // hitung pulang awal
                    if (!empty($jam_pulang)) {
                        $pulang_awal = max(0, (strtotime($jam_pulang_shift) - strtotime($jam_pulang)) / 60);
                    } else {
                        // tidak absen pulang
                        $pulang_awal = 150;
                    }
                    $status = 'HADIR';
                    if (empty($jam_masuk) && empty($jam_pulang)) {
                       

                        $cekCuti = $this->acm->cekCutiPerhari($id_pegawai, $tanggal);   
                        if($cekCuti){
                            $status = 'CUTI';
                            $pulang_awal = 0;
                            $telat = 0;
                        }else{
                            $status = 'ALPHA';
                        }
                    }

                }

                // SHIFT YANG HANYA MASUK (SM, M, PSM)
                else if ($jam_masuk_shift != '00:00:00' && $jam_pulang_shift == '00:00:00') {

                    // echo $tanggal.' - '.$jam_masuk.'<br>';

                    if (!empty($jam_masuk)) {
                        $telat = max(0, (strtotime($jam_masuk) - strtotime($jam_masuk_shift)) / 60);
                        $status = 'HADIR';
                    } else {
                        $telat = 300;
                    }

                    $pulang_awal = 0;
                }
            }

            /**
             * PERBAIKAN UNTUK ALPHA
             * jika alpha maka telat dan pulang awal = 0
             */
            if ($status == 'ALPHA') {
                $telat = 0;
                $pulang_awal = 0;
            }


            $this->update_status($row->id, $status, null);
            $this->update_menit($row->id, $telat, $pulang_awal);



            $no++;
        }


      
        $this->rekapDataAbsensi($id_pegawai, $pin, $bulan, $tahun);
        //exit;
        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }


    private function updateStatusIzinSakit($row, $id_pegawai, $tanggal)
    {

        //  echo $id_pegawai . 'tanggal :' . $tanggal;
        $sakit = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('tanggal', $tanggal)
            ->where('status', 1)
            ->where('jenis_absen', 'SAKIT')
            ->get('pengajuan_izin_sakit')
            ->row();

        // print_array($sakit);

        if ($sakit) {

            $status = 'SAKIT';

            $status_detail = ($sakit->jns_sakit == 2)
                ? 'SAKIT_SK'
                : 'SAKIT';

            $this->update_status($row->id, $status, $status_detail);
            $this->update_menit($row->id, 0, 0);

            //print_array($sakit);
            return true;
        }

        // =========================
        // 3?? CEK IZIN
        // =========================
        $izin = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('tanggal', $tanggal)
            ->where('status', 1)
            ->where('jenis_absen', 'IZIN')
            ->get('pengajuan_izin_sakit')
            ->row();


        //print_array($izin);
        if ($izin) {


            if ($izin->jns_izin == 1) {
                $this->update_status($row->id, 'IZIN', 'FULL');
            }

            if ($izin->jns_izin == 2) {
                $update = $this->update_status($row->id, 'IZIN', 'AWAL');
            }

            if ($izin->jns_izin == 3) {
                $this->update_status($row->id, 'IZIN', 'AKHIR');
                 $this->db->where('id', $row->id);
                    $this->db->update('tbl_kehadiran_harian', [
                        'p_awal_menit' => 0
                    ]);

            }

            return true;
        }

        return false;
    }

    private function updateStatusDL($row, $id_pegawai, $tanggal)
    {
        $dl = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('tanggal', $tanggal)
            ->where('status', 1)
            ->get('pengajuan_dinas_luar')
            ->row();

        if ($dl) {

            $status = 'DINAS';

            if ($dl->jns_dl == 'DLP') {
                $status_detail = 'DLP';
            } else if ($dl->jns_dl == 'DLA') {
                $status_detail = 'DLA';
            } else if ($dl->jns_dl == 'DLAK') {
                $status_detail = 'DLAK';
            }

            $this->update_status($row->id, $status, $status_detail);
            $this->update_menit($row->id, 0, 0);

            return true;
        }

        return false;
    }
    private function rekapDataAbsensi($id_pegawai, $pin, $bulan, $tahun)
    {
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $data  = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);

        $totalTelat   = 0;
        $totalPawal   = 0;
        $numIzin      = 0;
        $izin_half    = 0;
        $numSakit     = 0;
        $sakit_dgn_sk = 0;
        $alpha        = 0;
        $numDLP       = 0;
        $numDLA       = 0;
        $numDLAK      = 0;
        $numCuti      = 0;

        foreach ($data as $row) {

            // ====================
            // TOTAL MENIT
            // ====================
            $totalTelat += (int)$row->telat_menit;
            $totalPawal += (int)$row->p_awal_menit;

            // ====================
            // HITUNG STATUS
            // ====================
            if ($row->status == 'ALPHA') {
                $alpha++;
            }

            if ($row->status == 'SAKIT') {

                if ($row->status_detail == 'SAKIT_SK') {
                    $sakit_dgn_sk++;
                } else {
                    $numSakit++;
                }
            }

            if ($row->status == 'IZIN') {

                if ($row->status_detail == 'FULL') {
                    $numIzin++;
                } else {
                    $izin_half++;
                }
            }

            if ($row->status == 'DINAS') {

                if ($row->status_detail == 'DLP') {
                    $numDLP++;
                }

                if ($row->status_detail == 'DL') {
                    $numDLA++;
                }

                if ($row->status_detail == 'DLAK') {
                    $numDLAK++;
                }
            }

            if ($row->status == 'CUTI') {
                $numCuti++;
            }
        }



        if ($totalTelat < 300 && $totalPawal < 150) {
            if ($alpha == 0) {
                $status = 1;
            } else {
                $status = 0;
            }
        } else {
            $status = 0;
        }



        $rekap = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('periode', $periode)
            ->get('ts_rekap_absensi')
            ->row();



        if ($rekap) {
            $dataRekap = array(
                'telat' => $totalTelat,
                'pulang_awal' => $totalPawal,
                'izin' => $numIzin,
                'izin_half' => $izin_half,
                'sakit' => $numSakit,
                'sakit_dgn_sk' => $sakit_dgn_sk,
                'alpha' => $alpha,
                'dl_penuh' => $numDLP,
                'dl_awal' => $numDLA,
                'dl_akhir' => $numDLAK,
                'cuti' => $numCuti,
                'isoman' => 0,
                'status' => $status
            );

            echo 'update';

            $this->db->where('id', $rekap->id);
            $this->db->update('ts_rekap_absensi', $dataRekap);
        } else {
            $dataRekap = array(
                'id_pegawai' => $id_pegawai,
                'periode' => $periode,
                'telat' => $totalTelat,
                'pulang_awal' => $totalPawal,
                'izin' => $numIzin,
                'izin_half' => $izin_half,
                'sakit' => $numSakit,
                'sakit_dgn_sk' => $sakit_dgn_sk,
                'alpha' => $alpha,
                'dl_penuh' => $numDLP,
                'dl_awal' => $numDLA,
                'dl_akhir' => $numDLAK,
                'cuti' => $numCuti,
                'isoman' => 0,
                'status' => $status
            );


            $this->db->insert('ts_rekap_absensi', $dataRekap);
        }



        return true;
    }

    private function update_status($id, $status, $status_detail)
    {
        $this->db->where('id', $id);
        $this->db->set('status', $status);
        $this->db->set('status_detail', $status_detail);
        $this->db->update('tbl_kehadiran_harian');
        return true;
    }


    private function hitung_keterlambatan($id_pegawai, $pin, $bulan, $tahun)
    {
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $data = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);

        foreach ($data as $row) {

            // ========================
            // SKIP OFF
            // ========================
            if ($row->shift == 'OFF') {
                continue;
            }

            // ========================
            // SKIP FULL OVERRIDE
            // ========================
            if (in_array($row->status_detail, ['DLP', 'FULL']) || $row->status == 'SAKIT') {

                $this->update_menit($row->id, 0, 0);
                continue;
            }

            $detailShift = $this->Presensi_model->detailShiftByKode($row->shift, 'row');

            $telat = 0;
            $pulang_awal = 0;

            // ========================
            // HITUNG TELAT
            // ========================
            if ($row->jam_masuk && !in_array($row->status_detail, ['DLA', 'IZIN_AWAL'])) {

                if ($row->jam_masuk > $detailShift->jam_masuk) {


                    $telat = $this->selisih_menit(
                        $detailShift->jam_masuk,
                        $row->jam_masuk
                    );

                    // echo $row->tanggal.' - '.$row->shift.' - '. $detailShift->jam_masuk.' - '. $row->jam_masuk.' Telat : '.$telat .'<br>';
                }
            } else {
                $telat = $row->shift == 'L-OFF' ? 0 : 300;
            }

            // ========================
            // HITUNG PULANG AWAL
            // ========================
            if ($row->jam_pulang && !in_array($row->status_detail, ['DLAK', 'AKHIR'])) {

                if ($row->jam_pulang < $detailShift->jam_pulang) {

                    $pulang_awal = $this->selisih_menit(
                        $row->jam_pulang,
                        $detailShift->jam_pulang
                    );
                }
            } else {

                // echo  $row->tanggal.' - '.$row->status_detail.'<br>';
                if (!in_array($row->status_detail, ['DLAK', 'AKHIR'])) {
                    if (in_array($row->shift, ['SM', 'PSM', 'M'])) {
                        $pulang_awal = 0;
                    } else {
                        $pulang_awal = 150;
                    }
                } else {
                    $pulang_awal = 0;
                }
            }

            $this->update_menit($row->id, $telat, $pulang_awal);
        }


        //exit;

        return true;
    }

    private function selisih_menit($jam_awal, $jam_akhir)
    {
        $start = strtotime($jam_awal);
        $end   = strtotime($jam_akhir);

        return ($end - $start) / 60;
    }

    private function update_menit($id, $telat, $pulang_awal)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_kehadiran_harian', [
            'telat_menit' => $telat,
            'p_awal_menit' => $pulang_awal
        ]);

        return true;
    }


   

    public function update_absensi_pegawai($id_pegawai, $pin, $bulan, $tahun)
    {

        $this->Presensi_model->generate_rekap_bulanan($pin, $id_pegawai, $bulan, $tahun);

        //print_array($absensi_raw);
        //exit;
        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }


    function refresh_data()
    {



        $bulan = $this->session->userdata('periode_bulan') ?? date('m');
        $tahun = $this->session->userdata('periode_tahun') ?? date('Y');
        $periode = date('Y-m', strtotime("$tahun-$bulan"));
        $id_pj = $this->session->userdata('id_pj');

        if ($id_pj == '') {
            $id_pj = $this->session->userdata('id_pegawai');
            $this->session->set_userdata('id_pj', $id_pj);
        }




        $qry = $this->db->get_where('mst_pegawai', ['id_pegawai' => $id_pj]);
        $detValidator =  $qry->row();
        $id_puskesmas = $detValidator->id_puskesmas;
        $id_klaster = $detValidator->klaster;

        $jns_pegawai = $this->session->userdata('status_pegawai') ?? 'non_pns';

        $select = 'id_pegawai,pin, nama';
        if ($id_puskesmas != 1) {
            //selaian puskesmas induk
            $pegawai = $this->Pegawai_model->getPegawaiByIDPuskesmas($id_puskesmas, $jns_pegawai, $select);
        } else {

            $pegawai = $this->Pegawai_model->getPegawaiByKlaster($id_puskesmas, $id_klaster, $jns_pegawai, $select);
        }

        //print_array($pegawai);exit;

        foreach ($pegawai as $peg) {
            $id_pegawai = $peg->id_pegawai;
            $pin = $peg->pin;
            $this->Presensi_model->generate_rekap_bulanan($pin, $id_pegawai, $bulan, $tahun);
        }

        redirect('admin/absensi/rekap_absensi');

        //print_array($pegawai);
    }


    function deleteAbsenPengajuanDL($id_dl, $pin, $bulan, $tahun)
    {

        $this->db->where('id', $id_dl);
        $this->db->delete('pengajuan_dinas_luar');

        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }

    public function update_jam_manual()
    {
        $id    = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        // validasi field
        if (!in_array($field, ['jam_masuk', 'jam_pulang'])) {
            echo json_encode(['status' => false]);
            return;
        }

        $this->db->where('id', $id);
        $this->db->update('tbl_kehadiran_harian', [
            $field => $value
        ]);

        echo json_encode(['status' => true]);
    }



    public function repair_shift_over_night($periode)
    {
        // 1?? Ambil semua shift lintas hari
        $overnight = $this->db->query("
            SELECT kode_shift 
            FROM mst_shift_kerja
            WHERE jam_pulang <= jam_masuk
            AND kode_shift != 'OFF'
        ")->result();

        foreach ($overnight as $s) {

            // Hari pertama: hapus jam_pulang
            $this->db->query("
                UPDATE tbl_kehadiran_harian
                SET jam_pulang = NULL
                WHERE shift = ?
                AND DATE_FORMAT(tanggal, '%Y-%m') = ?
            ", [$s->kode_shift, $periode]);
        }

        // 2?? Khusus L-OFF (hari penyelesaian)
        $this->db->query("
            UPDATE tbl_kehadiran_harian
            SET 
                jam_masuk = NULL,
                telat_menit = 0
            WHERE shift = 'L-OFF'
            AND DATE_FORMAT(tanggal, '%Y-%m') = ?
        ", [$periode]);


        echo 'berhasil';
        //redirect($_SERVER['HTTP_REFERER']);
    }



    function insert_absen_ketidakhadiran()
    {
        $pin   = $this->input->post('pin');

        $tanggal    = $this->input->post('tanggal'); // YYYY-MM-DD
        $jamMasuk   = $this->input->post('jam_masuk');
        $jamPulang  = $this->input->post('jam_pulang');
        $status     = $this->input->post('status');
        $keterangan = $this->input->post('keterangan');

        $explode = explode("-", $tanggal);

        $tahun = $explode[0];
        $bulan = $explode[1];


        // print_array($this->input->post());
        //  exit;
        // Ambil data awal (optional, kalau ingin log/cek dulu)
        $absen = $this->db->get_where('tbl_kehadiran_harian', [
            'tanggal' => $tanggal,
            'pin'     => $pin
        ])->row();

        if (!$absen) {
            // Jika tidak ada data, buat baru atau return error
            show_error('Data absensi tidak ditemukan');
            return;
        }

        $dataUpdate = [];

        /* ==============================
			PRIORITAS 1 : JAM MANUAL
			============================== */
        if (!empty($jamMasuk) || !empty($jamPulang)) {

            if (!empty($jamMasuk)) {
                $jamMasuk = str_replace(".", ":", $jamMasuk);
                $dataUpdate['jam_masuk'] = $jamMasuk;
            }

            if (!empty($jamPulang)) {
                $jamPulang = str_replace(".", ":", $jamPulang);
                $dataUpdate['jam_pulang'] = $jamPulang;
            }

            // Optional: bisa set telat / p_awal kalau mau
        } elseif (!empty($status)) {

            switch ($status) {

                case 'DL-AWAL': // Dinas Luar Awal
                    $dataUpdate['status'] = 'DINAS';
                    $dataUpdate['status_detail'] = 'DLA';
                    // optional: update telat = 0
                    break;

                case 'DL-AKHIR': // Dinas Luar Akhir
                    $dataUpdate['status'] = 'DINAS';
                    $dataUpdate['status_detail'] = 'DLAK';
                    // optional: update p_awal = 0
                    break;

                case 'DL-PENUH': // Dinas Luar Penuh
                    $dataUpdate['status'] = 'DINAS';
                    $dataUpdate['status_detail']  = 'DLP';

                    // optional: telat = 0, p_awal = 0
                    break;

                case 'HADIR': // Dinas Luar Penuh
                    $dataUpdate['status'] = 'HADIR';
                    $dataUpdate['status_detail']  = '';

                    // optional: telat = 0, p_awal = 0
                    break;
                    case 'HADIR': // Dinas Luar Penuh
                    $dataUpdate['status'] = 'OFF';
                    $dataUpdate['status_detail']  = '';

                    // optional: telat = 0, p_awal = 0
                    break;

                default: // Izin / Sakit / Alpha
                    $dataUpdate['status'] = $status;
                    $dataUpdate['status_detail']  = $status;

                    break;
            }

            // Tambahkan keterangan jika ada
            if (!empty($keterangan)) {
                $dataUpdate['keterangan'] = $keterangan;
            }
        }



        // Jika ada data yang akan diupdate


        if (!empty($dataUpdate)) {
            $this->db->where('tanggal', $tanggal);
            $this->db->where('pin', $pin);
            $this->db->update('tbl_kehadiran_harian', $dataUpdate);
        }


        if ($status != '') {
            $qry = $this->db
                ->select('id_pegawai')
                ->get_where('mst_pegawai', ['pin' => $pin, 'jns_pegawai' => 'non_pns']);

            $row = $qry->row();

            if (!$row) {
                return; // pegawai tidak ditemukan
            }

            // print_array($row);

            $id_pegawai = $row->id_pegawai;

            // ================= IZIN / SAKIT =================
            if (in_array($status, ['IZIN', 'SAKIT'])) {

                $jns_izin  = 0;
                $jns_sakit = 0;

                // if ($status == 'SAKIT') {
                //     $jns_sakit = $this->input->post('jns_sakit');
                // }else if ($status == 'SAKIT DGN SURAT') {
                //     $jns_sakit = $this->input->post('jns_sakit');
                // }


                if ($status == 'SAKIT') {
                    $jns_sakit = $this->input->post('jns_sakit');
                } else if ($status == 'IZIN') {
                    $jns_izin = $this->input->post('jns_izin');
                }

                $data = [
                    'id_pegawai'  => $id_pegawai,
                    'tanggal'     => $tanggal,
                    'jenis_absen' => $status,
                    'jns_izin'    => $jns_izin,
                    'jns_sakit'   => $jns_sakit,
                    'file_image'  => '',
                    'status' => 1,
                    'keterangan'  => $this->input->post('keterangan')
                ];

                $this->db->insert('pengajuan_izin_sakit', $data);
            }
            // ================= DINAS LUAR =================
            else {

                if ($status != 'HADIR') {
                    $map_dl = [
                        'DL-PENUH' => 'DLP',
                        'DL-AWAL'  => 'DLA',
                        'DL-AKHIR' => 'DLAK'
                    ];

                    $jns_dl = isset($map_dl[$status]) ? $map_dl[$status] : 'DLAK';

                    $data = [
                        'id_pegawai' => $id_pegawai,
                        'tanggal'    => $tanggal,
                        'jns_dl'     => $jns_dl,
                        'photo'      => '',
                        'surtug'     => '',
                        'lat'        => 0,
                        'lon'        => 0,
                        'status'     => 1,
                        'created_by'  => 'admin',
                        'create_at'  => date('Y-m-d H:i:s'),
                        'keterangan' => $this->input->post('keterangan')
                    ];

                    $this->db->insert('pengajuan_dinas_luar', $data);
                }
            }
        }



        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
        // Redirect kembali ke halaman sebelumnya
        //redirect($_SERVER['HTTP_REFERER']);
    }


    function updateShiftReguler($pin, $data_shift)
    {


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
            $cek = $this->db
                ->where('pin', $pin)
                ->where('tanggal', $tanggal)
                ->get('tbl_kehadiran_harian')
                ->row();

            if (!$cek) {

                // INSERT jika belum ada
                $this->db->insert('tbl_kehadiran_harian', [
                    'pin' => $pin,
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'status' => ($shift == 'OFF') ? 'OFF' : 'ALPHA',
                    'status_detail' => $status_detail,
                    'keterangan' => $keterangan,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'telat_menit' => 0,
                    'p_awal_menit' => 0
                ]);
            } else {

                // UPDATE shift & detail saja
                $this->db->where('id', $cek->id);
                $this->db->update('tbl_kehadiran_harian', [
                    'shift' => $shift,
                    'status_detail' => $status_detail,
                    'keterangan' => $keterangan
                ]);
            }
        }


        return true;
    }

    public function repair_periode_reguler($pin, $bulan, $tahun)
    {
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($i = 1; $i <= $jumlah_hari; $i++) {

            $tanggal = date('Y-m-d', strtotime("$tahun-$bulan-$i"));
            $hari_ke = date('N', strtotime($tanggal)); // 1=Senin ... 7=Minggu

            // ============================
            // Cek Hari Libur Nasional
            // ============================
            $libur = $this->db
                ->where('tgl', $tanggal)
                ->get('ts_hari_libur')
                ->row();

            $status_detail = null;
            $keterangan = null;

            if ($libur) {
                $shift = 'OFF';
                $status_detail = 'LIBUR NASIONAL';
                $keterangan = $libur->keterangan;
            } else {

                // ============================
                // Tentukan Shift Reguler
                // ============================
                if ($hari_ke == 6 || $hari_ke == 7) {
                    $shift = 'OFF';
                } elseif ($hari_ke == 5) {
                    $shift = 'REG-JUM';
                } else {
                    $shift = 'REG';
                }
            }

            // ============================
            // Cek apakah data sudah ada
            // ============================
            $cek = $this->db
                ->where('pin', $pin)
                ->where('tanggal', $tanggal)
                ->get('tbl_kehadiran_harian')
                ->row();

            if (!$cek) {

                // INSERT jika belum ada
                $this->db->insert('tbl_kehadiran_harian', [
                    'pin' => $pin,
                    'tanggal' => $tanggal,
                    'shift' => $shift,
                    'status' => ($shift == 'OFF') ? 'OFF' : 'ALPHA',
                    'status_detail' => $status_detail,
                    'keterangan' => $keterangan,
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'telat_menit' => 0,
                    'p_awal_menit' => 0
                ]);
            } else {

                // UPDATE shift & detail saja
                $this->db->where('id', $cek->id);
                $this->db->update('tbl_kehadiran_harian', [
                    'shift' => $shift,
                    'status_detail' => $status_detail,
                    'keterangan' => $keterangan
                ]);
            }
        }

        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }

    function reset_absensi($id_pegawai, $pin, $bulan, $tahun)
    {
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $data  = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);
        foreach ($data as $row) {
            $id = $row->id;

            $newUpdate = [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'telat_menit' => 0,
                'p_awal_menit' => 0

            ];
            $this->db->where('id', $id);
            $this->db->update('tbl_kehadiran_harian', $newUpdate);
        }

        redirect('admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun);
    }

    function get_detail_dl()
    {
        $id = $this->input->post('id_dl');
        $qry = $this->db->get_where('pengajuan_dinas_luar', ['id' => $id]);
        $dl = $qry->row();

        //print_array($dl);   
        echo json_encode($dl);
    }
}
