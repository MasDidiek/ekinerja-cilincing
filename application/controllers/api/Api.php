<?php
/*defined('BASEPATH') or exit('No direct script access allowed');
class Api extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=utf-8');

        $this->load->model('Api_model');
    }

    function index()
    {
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['detail_pegawai'] = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $this->load->view('my_absensi/index', $data);
    }

    function getDataPegawai($jns_pegawai = 'non_pns')
    {
            $this->db->order_by('mst_pegawai.nama', 'ASC');
            $this->db->select('
                mst_pegawai.*,
                mst_puskesmas.nama AS puskesmas,
                mst_jabatan.nama AS jabatan
            ');

            $this->db->from('mst_pegawai');

            $this->db->join(
                'mst_puskesmas',
                'mst_puskesmas.id_puskesmas = mst_pegawai.id_puskesmas',
                'left'
            );

            $this->db->join(
                'mst_jabatan',
                'mst_jabatan.id = mst_pegawai.id_jabatan',
                'left'
            );

            $this->db->where('mst_pegawai.jns_pegawai', $jns_pegawai);
            $this->db->where('mst_pegawai.status_kerja >', 0);

            $qry = $this->db->get();

            $row = $qry->result();

        echo json_encode([
            'status' => true,
            'data' => $row
        ]);
    }

    function getRekapAbsensi($jns_pegawai, $periode=null)
    {
        $periode = $periode ?? date('Y-m');
        $rekap_absensi = $this->Api_model->getRekapAbsensiPerbulan($jns_pegawai, $periode);
        echo json_encode([
            'status' => true,
            'data' => $rekap_absensi
        ]);
    }

    function getDataCuti()
    {
        $this->db->select('ts_pengajuan_cuti.*, mst_pegawai.nama');
        $this->db->from('ts_pengajuan_cuti');
        $this->db->join('mst_pegawai', 'mst_pegawai.id_pegawai = ts_pengajuan_cuti.id_pegawai', 'left');
        $this->db->where_in('status_akhir', ['proses', 'draft', 'disetujui']);
        $this->db->where('ts_pengajuan_cuti.tgl_mulai >', '2025-12-31');
        $this->db->order_by('ts_pengajuan_cuti.tgl_pengajuan', 'DESC');

        $qry = $this->db->get('', 500, 0);
        $row = $qry->result();

        echo json_encode([
            'status' => true,
            'data' => $row
        ]);
    }



    function getDataEditCuti($id)
    {
         $detail_cuti =  $this->Api_model->getDetailCuti($id);

        echo json_encode([
            'status' => true,
            'data' => $detail_cuti
        ]);
    }   


    function getDataCutiById($id)
    {

        $detail_cuti =  $this->Api_model->getDetailCuti($id);
        $id_pegawai  = $detail_cuti->id_pegawai;


        $approval     = $this->Api_model->getApproval($id);
        $hak_cuti     = $this->Api_model->getHakCutiPegawai($id_pegawai);
        $riwayat_cuti = $this->Api_model->getRiwayatCutiPegawai($id_pegawai);
        $list_hari_cuti = $this->Api_model->getListHariCuti($id);



        echo json_encode([
            'status' => true,
            'data' => [$detail_cuti, $approval, $hak_cuti, $riwayat_cuti, $list_hari_cuti]
        ]);
    }

    function accCuti()
    {
        $id_cuti = $this->input->post('id_cuti');
        $role_approval = $this->input->post('role_approval');

    
    // mapping next approval
        $next_role = [
            'kapustu' => 'ktu',
            'ktu'     => 'kapus'
        ];

         
        // jika masih ada next role → set pending
        if (isset($next_role[$role_approval])) {

            $this->db->where('id_pengajuan_cuti', $id_cuti);
            $this->db->where('role_approval', $next_role[$role_approval]);
            $this->db->update('ts_pengajuan_cuti_approval', [
                'status' => 'pending'
            ]);

        }else{

            // $this->db->where('id', $id_cuti);
            // $cuti = $this->db->get('ts_pengajuan_cuti')->row();


            $cuti =  $this->Api_model->getDetailCuti($id_cuti);

            $hari_cuti = $cuti->lama_cuti;
            $id_pegawai = $cuti->id_pegawai;
            $tahun_hak_cuti = $cuti->tahun_hak_cuti;

            $tgL_mulai = $cuti->tgl_mulai;
            $tgl_selesai = $cuti->tgl_selesai;

            if ($tahun_hak_cuti != 0) {
                //cuti tahunan
                $this->db->where('id_pegawai', $id_pegawai);
                $this->db->where('tahun', $tahun_hak_cuti);
                $hak_cuti_pegawai = $this->db->get('ts_hak_cuti_pegawai')->row();
                $hak_total = $hak_cuti_pegawai->hak_total;
                $hak_reserved = $hak_cuti_pegawai->hak_reserved;
                $hak_terpakai = $hak_cuti_pegawai->hak_terpakai;
                //ambil nilai hak reserved saat ini, lalu kurangi dengan lama cuti yang diambil
                $sisa_hak_reserved = $hak_reserved - $hari_cuti;
                //pindahkan ke hak terpakai
                $this->db->where('id_pegawai', $id_pegawai);
                $this->db->where('tahun', $tahun_hak_cuti);
                $this->db->set('hak_terpakai', $hak_terpakai + $hari_cuti);
                $this->db->set('hak_reserved', $sisa_hak_reserved);
                $this->db->update('ts_hak_cuti_pegawai');

                $sisa_akhir = $hak_total - ($sisa_hak_reserved + $hak_terpakai);

                //update log mutasi cuti
                $this->db->insert('ts_log_mutasi_cuti', [
                    'id_pengajuan_cuti' => $id_cuti,
                    'id_pegawai' => $id_pegawai,
                    'tahun' => $tahun_hak_cuti,
                    'tipe' => 'final',
                    'jumlah' => $hari_cuti,
                    'saldo_sebelum' => $sisa_hak_reserved,
                    'saldo_sesudah' => $sisa_akhir,
                    'keterangan' => 'Pengajuan cuti disetujui, pemotongan hak cuti tahunan'
                ]);
            } //close if cuti tahunan


            $this->db->where('id', $id_cuti);
            $this->db->update('ts_pengajuan_cuti', [
                'status_akhir' => 'disetujui'
            ]);

               
            $this->db->where('id_pengajuan_cuti', $id_cuti);
            $this->db->update('ts_pengajuan_cuti_approval', [
                'status' => 'approved'
            ]);


            //ubah status absensi di _tbl_kehadiran_harian, cari data by pin dan tanggal, ubah status jadi cuti

            //ambil data dari table ts_pengajuan_cuti_detail, by id_pengajuan_cuti, looping data tsb, lalu update di table _tbl_kehadiran_harian sesuai dengan tanggal yang ada di ts_pengajuan_cuti_detail

            $this->db->where('id_pengajuan_cuti', $id_cuti);
            $detail_cuti = $this->db->get('ts_pengajuan_cuti_detail')->result();

            foreach ($detail_cuti as $detail) {
                $tgl_cuti = $detail->tgl_cuti;

                $this->db->where('pin', $cuti->pin);
                $this->db->where('tanggal', $tgl_cuti);
                $this->db->update('tbl_kehadiran_harian', [
                    'status' => 'cuti',
                    'keterangan' => $cuti->alasan_cuti,
                    'telat_menit' => 0,
                    'p_awal_menit'  => 0,
                    'shift' => 'OFF'
                ]);
             }
             


        
        }//close if kapus



        echo json_encode([
            'status' => 'success'
        ]);
    }
    function tolakCuti()
    {
        $id_cuti = $this->input->post('id_cuti');

        $this->db->where('id', $id_cuti);
        $update = $this->db->update('ts_pengajuan_cuti', [
            'status_akhir' => 'ditolak'
        ]);

        echo json_encode([
            'status' => $update
        ]);
    }


    public function batalkanCuti()
    {
        $id_cuti = $this->input->post('id_cuti');

        $this->db->where('id', $id_cuti);
        $update = $this->db->update('ts_pengajuan_cuti', [
            'status_akhir' => 'dibatalkan'
        ]);

        echo json_encode([
            'status' => $update
        ]);
    }


    function updateCuti(){
        $id_cuti     = $this->input->post('id_cuti');
        $jns_cuti    = $this->input->post('jns_cuti');
        $tahun_hak_cuti = $this->input->post('tahun_hak_cuti');
        $tgl_mulai = $this->input->post('tgl_mulai');
        $tgl_selesai = $this->input->post('tgl_selesai');
        $alasan_cuti = $this->input->post('alasan_cuti');
        $alamat_cuti = $this->input->post('alamat_cuti');

        $start_date = new DateTime($tgl_mulai);
        $end_date = new DateTime($tgl_selesai);


        //pertama cari tau dulu, pegawai ini jenis jam kerjanya shift atau reguler
        $cuti = $this->db->get_where('ts_pengajuan_cuti', ['id' => $id_cuti])->row();
        $id_pegawai   = $cuti->id_pegawai;
        $lama_cuti_db = $cuti->lama_cuti;

        $pegawai       = $this->db->get_where('mst_pegawai', ['id_pegawai' => $id_pegawai])->row();
        $jns_jam_kerja = $pegawai->jns_jam_kerja;



        if ($jns_jam_kerja == 'non_shift') {

        
            //update data di table pengajuan_cuti detail
            //pertama hapus data lama di table ts_pengajuan_cuti_detail, lalu insert data baru sesuai dengan tanggal yang baru
            $this->db->where('id_pengajuan_cuti', $id_cuti);
            $this->db->delete('ts_pengajuan_cuti_detail');

            //hitung hari kerja
            //jika start date dan end date sama, maka lama cuti 1 hari
            if ($start_date->format('Y-m-d') == $end_date->format('Y-m-d')) {
                $lama_cuti = 1;

                 $this->db->insert('ts_pengajuan_cuti_detail', [
                        'id_pengajuan_cuti' => $id_cuti,
                        'tgl_cuti' => $end_date->format('Y-m-d')
                    ]);

            } else {
                $lama_cuti = 0;
                $current_date = $start_date;

                while ($current_date <= $end_date) {
                    //cek jika current date adalah hari sabtu atau minggu, maka tidak dihitung
                    if ($current_date->format('N') < 6) {
                        $lama_cuti++;
                    }

                    // cek ke table tbl_shift_template_detail, ambil data di table tsb, by tanggal, jika ada row dan di kolom keterangan tidak kosong maka itu adahlah hari libur, dan tidak dihitung dalam lama cuti
                    $shift_detail = $this->db->get_where('tbl_shift_template_detail', ['tanggal' => $current_date->format('Y-m-d')])->row();
                    if ($shift_detail) {
                        if ($shift_detail->keterangan !== '') {
                            $lama_cuti--;
                        }
                    }

                    $this->db->insert('ts_pengajuan_cuti_detail', [
                        'id_pengajuan_cuti' => $id_cuti,
                        'tgl_cuti' => $current_date->format('Y-m-d')
                    ]);


                    $current_date->modify('+1 day');
                }
            }


        } else {
            //hitung hari kalender
            $interval = $start_date->diff($end_date);
            $lama_cuti = $interval->days + 1;

        
            //update data di table pengajuan_cuti detail
            //pertama hapus data lama di table ts_pengajuan_cuti_detail, lalu insert data baru sesuai dengan tanggal yang baru
            $this->db->where('id_pengajuan_cuti', $id_cuti);
            $this->db->delete('ts_pengajuan_cuti_detail');

            //input baru
            $current_date = $start_date;
            while ($current_date <= $end_date) {
                $this->db->insert('ts_pengajuan_cuti_detail', [
                    'id_pengajuan_cuti' => $id_cuti,
                    'tgl_cuti' => $current_date->format('Y-m-d')
                ]);

                $current_date->modify('+1 day');    
            }
        }


        //kalo lama cuti berubah, update juga di hak cuti pegawai, dengan cara ambil dulu hak reserved yang lama, lalu kurangi dengan lama cuti yang lama, lalu tambahkan dengan lama cuti yang baru
        if ($lama_cuti != $lama_cuti_db) {
            $hak_cuti = $this->db->get_where('ts_hak_cuti_pegawai', ['id_pegawai' => $id_pegawai, 'tahun' => $tahun_hak_cuti])->row();
            $hak_reserved_lama = $hak_cuti->hak_reserved;
            $hak_reserved_baru = $hak_reserved_lama - $lama_cuti_db + $lama_cuti;

            $this->db->where('id_pegawai', $id_pegawai);
            $this->db->where('tahun', $tahun_hak_cuti);
            $this->db->update('ts_hak_cuti_pegawai', ['hak_reserved' => $hak_reserved_baru]);

             //update log mutasi cuti
             $this->db->insert('ts_log_mutasi_cuti', [
                'id_pengajuan_cuti' => $id_cuti,
                'id_pegawai' => $id_pegawai,
                'tahun' => $tahun_hak_cuti,
                'tipe' => 'update',
                'jumlah' => $lama_cuti - $lama_cuti_db,
                'saldo_sebelum' => $hak_reserved_lama,
                'saldo_sesudah' => $hak_reserved_baru,
                'keterangan' => 'Update pengajuan cuti, perubahan lama cuti dari '.$lama_cuti_db.' hari menjadi '.$lama_cuti.' hari'
            ]);

           
        }



          //update di table ts_pengajuan_cuti
             $this->db->where('id', $id_cuti);
             $this->db->update('ts_pengajuan_cuti', [
                'lama_cuti' => $lama_cuti,
                'tgl_mulai' => $start_date->format('Y-m-d'),
                'tgl_selesai' => $end_date->format('Y-m-d'),
                'alasan_cuti' => $alasan_cuti,
                'alamat_cuti' => $alamat_cuti
             ]);

          echo json_encode([
            'status' => 'success'
        ]);
    }

    function hapusCuti()
    {
        $id_cuti = $this->input->post('id_cuti');

        $this->db->where('id_pengajuan_cuti', $id_cuti);
        $this->db->delete('ts_log_mutasi_cuti');

        $this->db->where('id_pengajuan_cuti', $id_cuti);
        $this->db->delete('ts_pengajuan_cuti_approval');

        $this->db->where('id_pengajuan_cuti', $id_cuti);
        $this->db->delete('ts_pengajuan_cuti_detail');



        $this->db->where('id', $id_cuti);
        $delete = $this->db->delete('ts_pengajuan_cuti');

        echo json_encode([
            'status' => $delete
        ]);
    }

    function getDataAbsensi($pin, $periode)
    {
        // $pin = '2227';
        // $periode = '2026-04';
        $absensi     = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);
        $jns_pegawai = 'non_pns';
        $pegawai     = $this->Api_model->getInfoPegawaiByPin($pin, $jns_pegawai);


        echo json_encode([
            'status' => true,
            'data' => [
                'absensi' => $absensi,
                'pegawai' => $pegawai
            ]
        ]);
    }


    function updateJamAbsen()
    {

       $payload = json_decode(file_get_contents('php://input'), true);
        $id = $payload['id'];
        $jns_absen = $payload['jns_absen'];
        $jam = $payload['jam'];

        // $id = $this->input->post('id');
        // $jns_absen = $this->input->post('jns_absen');
        // $jam = $this->input->post('jam');

        $this->db->where('id', $id);

        if($jam=='00:00:00'){
           $this->db->set($jns_absen,  null);
        } else{
            $this->db->set($jns_absen, $jam);
        }

        $update = $this->db->update('tbl_kehadiran_harian');

        echo json_encode([
            'status' => $update
        ]);
    }

    function getLogAbsensi($pin, $periode)
    {   

         $log_absensi = $this->Api_model->getLogAbsensi($pin, $periode);
        echo json_encode([
            'status' => true,
            'data' => $log_absensi
        ]);
    }


    function updateDataAbsensi()
     {

        $payload = json_decode(file_get_contents('php://input'), true);


        $data = $payload['data'];
        $pin = $payload['pin'];
        $id_pegawai = $payload['id_pegawai'];
        $periode = $payload['periode'];


         $total_telat = 0;
         $total_p_awal = 0;
         $total_cuti = 0;
         $alpha = 0;

         for ($i=0; $i < count($data); $i++) { 
            $id = $data[$i]["id"];
            $shift = $data[$i]["shift"];
            $telat_menit = $data[$i]["telat_menit"];
            $p_awal_menit = $data[$i]["p_awal_menit"];
            $status = $data[$i]["status"];

             $total_telat += $absen->telat_menit;
             $total_p_awal += $absen->p_awal_menit;

             if ($status == 'CUTI') {
                $total_cuti++;
             }

            if ($status == 'ALPHA') {
                $alpha++;
            }

            $this->db->where('id', $id);
            $update = $this->db->update('tbl_kehadiran_harian', [
                'telat_menit' => $telat_menit,
                'p_awal_menit' => $p_awal_menit,
                'shift' => $shift,
                'status' => $status
            ]);
         }//close looping


            $this->db->where('id_pegawai', $id_pegawai);
            $this->db->where('periode', $periode);
            $rekap_absensi = $this->db->get('ts_rekap_absensi')->row();
        
            if ($rekap_absensi) {
                //update
                $this->db->where('id_pegawai', $id_pegawai);
                $this->db->where('periode', $periode);
                $update = $this->db->update('ts_rekap_absensi', [
                    'telat' => $total_telat,
                    'pulang_awal' => $total_p_awal,
                    'cuti' => $total_cuti,
                    'alpha' => $alpha
                ]);
            }

            echo json_encode([
                'status' => true
            ]);
     }





    // function updateDataAbsensi()
    // {

    // //function ini untuk update telat dan pulang cepat di table tbl_kehadiran_harian, berdasarkan data log absensi dari mesin absensi, yang dikirim dari aplikasi mobile, dengan parameter pin, tanggal, jam masuk, jam keluar
    //     $id = $this->input->post('id');
    //     $telat_menit = $this->input->post('telat_menit');
    //     $p_awal_menit = $this->input->post('p_awal_menit');
    //     $periode = $this->input->post('periode');
    //     $pin = $this->input->post('pin');

    //     $this->db->where('id', $id);
    //     $update = $this->db->update('tbl_kehadiran_harian', [
    //         'telat_menit' => $telat_menit,
    //         'p_awal_menit' => $p_awal_menit,
    //     ]);


    //     $jns_pegawai = 'non_pns';
    //     $pegawai     = $this->Api_model->getInfoPegawaiByPin($pin, $jns_pegawai);
    //     $id_pegawai = $pegawai->id_pegawai;


    //      $absensi     =  $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);
    //      $total_telat = 0;
    //      $total_p_awal = 0;
    //      foreach ($absensi as $absen) {

    //             $total_telat += $absen->telat_menit;
    //             $total_p_awal += $absen->p_awal_menit;
    //      }
               
         
    //      $this->db->where('id_pegawai', $id_pegawai);
    //      $this->db->where('periode', $periode);
    //      $rekap_absensi = $this->db->get('ts_rekap_absensi')->row();
    
    //             if ($rekap_absensi) {
    //                 //update
    //                 $this->db->where('id_pegawai', $id_pegawai);
    //                 $this->db->where('periode', $periode);
    //                 $update = $this->db->update('ts_rekap_absensi', [
    //                     'telat' => $total_telat,
    //                     'pulang_awal' => $total_p_awal
    //                 ]);
    //             } else {
    //                 //insert
    //                 $this->db->insert('ts_rekap_absensi', [
    //                     'id_pegawai' => $id_pegawai,
    //                     'periode' => $periode,
    //                     'telat' => $total_telat,
    //                     'pulang_awal' => $total_p_awal
    //                 ]);
    //             }
    
             

    //     echo json_encode([
    //         'status' => $update
    //     ]);
    // }


    function reSyncAbsensi()
    {

 
         $pin = $this->input->post('pin');
         $periode = $this->input->post('periode');
        $logs = $this->Api_model->getLogAbsensi($pin, $periode);
        $update = $this->Api_model->sync_kehadiran_harian($logs);

        echo json_encode([
            'status' => $update
        ]);
    }


    
    function updateHakCuti()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $tahun = $this->input->post('hak_cuti_tahun');
        $hak_total = $this->input->post('total_hak');
        $hak_reserved = $this->input->post('hak_reserved');
        $hak_terpakai = $this->input->post('hak_terpakai');

        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $tahun);
        $update = $this->db->update('ts_hak_cuti_pegawai', [

            'hak_total' => $hak_total,
            'hak_reserved' => $hak_reserved,
            'hak_terpakai' => $hak_terpakai
        ]);

        echo json_encode([
            'status' => $update
        ]);
    }


    function getDataDinasLuar()
    {
        $this->db->select('pengajuan_dinas_luar.*, mst_pegawai.nama');
        $this->db->from('pengajuan_dinas_luar');
        $this->db->join('mst_pegawai', 'mst_pegawai.id_pegawai = pengajuan_dinas_luar.id_pegawai', 'left');
        $this->db->where('pengajuan_dinas_luar.tanggal >', '2025-03-30');
        $this->db->order_by('pengajuan_dinas_luar.tanggal', 'DESC');

        $qry = $this->db->get('', 500, 0);
        $row = $qry->result();

        echo json_encode([
            'status' => true,
            'data' => $row
        ]);
    }



    function getDetailDinasLuar($id)
    {
        $this->db->select('pengajuan_dinas_luar.*, mst_pegawai.nama');
        $this->db->from('pengajuan_dinas_luar');
        $this->db->join('mst_pegawai', 'mst_pegawai.id_pegawai = pengajuan_dinas_luar.id_pegawai', 'left');
        $this->db->where('pengajuan_dinas_luar.id', $id);

        $qry = $this->db->get();
        $row = $qry->row();

        echo json_encode([
            'status' => true,
            'data' => $row
        ]);
    }


    function getPengajuanIzinSakit($jns_absen=null)
    {

        $this->db->select('pengajuan_izin_sakit.*, mst_pegawai.nama');
        $this->db->from('pengajuan_izin_sakit');
        $this->db->join('mst_pegawai', 'mst_pegawai.id_pegawai = pengajuan_izin_sakit.id_pegawai', 'left');
        if ($jns_absen) {
            $this->db->where('pengajuan_izin_sakit.jenis_absen', $jns_absen);
        }
        $this->db->where('pengajuan_izin_sakit.tanggal >', '2026-01-01');
        $this->db->order_by('pengajuan_izin_sakit.tanggal', 'DESC');

        $qry = $this->db->get('', 500, 0);
        $row = $qry->result();

        echo json_encode([
            'status' => true,
            'data' => $row
        ]);
    }

    function accDinasLuar()
    {
    }

    function tolakDinasLuar()
    {
    }

    function batalkanDinasLuar()
    {
    }

    function hapusDinasLuar()
    {
    }


    function updateAbsensiIzinSakit($id)
    {
            $izin_sakit = $this->db
                ->get_where('pengajuan_izin_sakit', ['id' => $id])
                ->row();

            $id_pegawai = $izin_sakit->id_pegawai;

            $pegawai = $this->db
                ->select('pin')
                ->get_where('mst_pegawai', ['id_pegawai' => $id_pegawai])
                ->row();

            $pin = $pegawai->pin;

            // default
            $status = 'OFF';
            $status_detail = '';
            $telat_menit = 0;
            $p_awal_menit = 0;

            if ($izin_sakit->jenis_absen == 'IZIN') {

                switch ($izin_sakit->jns_izin) {

                    case 1:
                        $status = 'IZIN';
                        $status_detail = 'IZIN PENUH';
                        break;

                    case 2:
                        $status = 'HADIR';
                        $status_detail = 'IZIN AWAL';
                        break;

                    case 3:
                        $status = 'HADIR';
                        $status_detail = 'IZIN AKHIR';
                        break;

                    default:
                        $status = 'IZIN';
                        $status_detail = 'IZIN';
                        break;
                }

            } else {

                $status = 'SAKIT';

                $status_detail = ($izin_sakit->jns_sakit == 1)
                    ? 'SAKIT TANPA SK'
                    : 'SAKIT _SK';
            }

            $update_data = [
                'status'         => $status,
                'status_detail'  => $status_detail,
                'keterangan'     => $izin_sakit->keterangan,
                'telat_menit'    => $telat_menit,
                'p_awal_menit'   => $p_awal_menit,
        
            ];

            $this->db->where('pin', $pin);
            $this->db->where('tanggal', $izin_sakit->tanggal);

            $update = $this->db->update('tbl_kehadiran_harian', $update_data);

                
              $this->db->where('id', $izin_sakit->id);
               $this->db->set('status', 1);
               $this->db->update('pengajuan_izin_sakit');

                echo json_encode([
                    'status' => $update
                ]);

    }

        function insertIzinSakit()
        {
            $nama = $this->input->post('nama');
            $tanggal = $this->input->post('tanggal');
            $jenis_absen = $this->input->post('jenis_absen');
            $keterangan = $this->input->post('keterangan');
            $jenis_sakit = $this->input->post('jenis_sakit');
            $jenis_izin = $this->input->post('jenis_izin');

            //cari id pegawai berdasarkan nama
            $pegawai = $this->db->get_where('mst_pegawai', ['nama' => $nama])->row();

            if ($pegawai) {
                $id_pegawai = $pegawai->id_pegawai;

                $insert = $this->db->insert('pengajuan_izin_sakit', [
                    'id_pegawai' => $id_pegawai,
                    'tanggal' => $tanggal,
                    'jenis_absen' => $jenis_absen,
                    'keterangan' => $keterangan,
                    'jns_sakit' => $jenis_sakit,
                    'jns_izin' => $jenis_izin,
                    'status' => 0
                ]);

                echo json_encode([
                    'status' => $insert
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ]);
            }
        }
}

/.

*/


