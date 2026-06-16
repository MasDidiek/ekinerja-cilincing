<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Penilaian_kinerja extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->model('Profile_model');
        $this->load->model('Admin_cuti_model', 'acm');
        $this->Auth_model->cekAuthLogin();
        $this->Auth_model->cekSession();
    }


    function index()
    {

        $this->session->set_userdata('periode_tahun', '2024');
        $id_validator = $this->session->userdata('id_pegawai');
        $id_pj_sess = $this->session->userdata('id_pj');
        $thn_anggaran = date('Y');

        if ($id_pj_sess != '') {
            $id_validator = $id_pj_sess;
        }

        $data['data_pegawai'] =  $this->Pegawai_model->getListPegawaiByValidator($id_validator, $thn_anggaran);

        $data['validator'] = $this->Pegawai_model->getValidator();
        $this->load->view('penilaian_kinerja/index', $data);
    }

    function validasi_aktifitas()
    {

        $id_pegawai   = $this->session->userdata('id_pegawai');
        $usergroup    = $this->session->userdata('usergroup');
        $id_pj_sess   = $this->session->userdata('id_pj');

        if ($id_pj_sess != '') {
            $id_validator = $id_pj_sess;
        } else {
            $id_validator = $id_pegawai;
        }

        $jns_pegawai = 'non_pns';

        $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_validator);
        $id_puskesmas = $detail_pegawai->id_puskesmas;
        $klaster      = $detail_pegawai->klaster;



        if ($id_puskesmas == 1) {
            //puskesmas cilincing
            $pegawai = $this->Pegawai_model->getPegawaiByKlaster($id_puskesmas, $klaster, $jns_pegawai, '*');
        } else {
            //pustu
            $pegawai = $this->Pegawai_model->getPegawaiByIDPuskesmas($id_puskesmas, $jns_pegawai, '*');
        }

        $data['data_pegawai'] =  $pegawai;
        $data['detail_validator'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['validator'] = $this->Pegawai_model->getValidator();
        $this->load->view('penilaian_kinerja/validasi_aktifitas', $data);
    }



    function perilaku()
    {

        $id_pegawai   = $this->session->userdata('id_pegawai');
        $usergroup    = $this->session->userdata('usergroup');
        $id_pj_sess   = $this->session->userdata('id_pj');

        if ($id_pj_sess != '') {
            $id_validator = $id_pj_sess;
        } else {
            $id_validator = $id_pegawai;
        }

        $jns_pegawai = 'non_pns';

        $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_validator);
        $id_puskesmas = $detail_pegawai->id_puskesmas;
        $klaster      = $detail_pegawai->klaster;



        if ($id_puskesmas == 1) {
            //puskesmas cilincing
            $pegawai = $this->Pegawai_model->getPegawaiByKlaster($id_puskesmas, $klaster, $jns_pegawai, '*');
        } else {
            //pustu
            $pegawai = $this->Pegawai_model->getPegawaiByIDPuskesmas($id_puskesmas, $jns_pegawai, '*');
        }

        //print_array($this->session->userdata);
        $data['data_pegawai'] =  $pegawai;
        $data['detail_validator'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['validator'] = $this->Pegawai_model->getValidator();

        $this->load->view('penilaian_kinerja/penilaian_perilaku', $data);
    }

    function nilai_perilaku($id_pegawai)
    {

        $bulan = $this->session->userdata('periode_bulan');
        $tahun = $this->session->userdata('periode_tahun');
        $cekPenilaian = $this->Kinerja_model->cekPenilaianPegawai($id_pegawai, $bulan, $tahun);

        if ($cekPenilaian == false) {
            //klo belum pernah dinliai sama sekali
            $this->Kinerja_model->insertInitialPerilaku($id_pegawai, $bulan, $tahun);
        }


        $data['totalPoin']  = $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);
        $data['pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['datalist'] = $this->Kinerja_model->getListKategoriPenilaianPerilaku();


        $this->load->view('penilaian_kinerja/nilai_perilaku', $data);
    }


    function generate_nilai_perilaku($id_pegawai){
        $bulan = $this->session->userdata('periode_bulan');
        $tahun = $this->session->userdata('periode_tahun');

        //$this->Kinerja_model->generateNilaiPerilaku($id_pegawai, $bulan, $tahun);

        $perilakuTerakhir = $this->Kinerja_model->copyPenilaianTerakhir($id_pegawai, $bulan, $tahun);

       redirect('admin/penilaian_kinerja/perilaku/'.$id_pegawai);
        
    }

    function set_session_periode()
    {

        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');
        $this->session->set_userdata('periode_tahun', $tahun);
        $this->session->set_userdata('periode_bulan', $bulan);

        return true;
    }

    function inject_nilai_perilaku()
    {

        $this->session->set_userdata('periode_bulan', '01');
        $this->session->set_userdata('periode_tahun', '2026');
        $id_validator = $this->session->userdata('id_pegawai');
        $id_pj_sess = $this->session->userdata('id_pj');
        $thn_anggaran = 2024;

        if ($id_pj_sess != '') {
            $id_validator = $id_pj_sess;
        }



        $data_pegawai =  $this->Pegawai_model->getListPegawaiByValidator($id_validator, $thn_anggaran);
        $bulan = 12;
        $tahun = 2025;
        foreach ($data_pegawai as $list) {
            $id_pegawai = $list->id_pegawai;

            $query = $this->db->get_where('tbl_penilaian_perilaku', array('id_pegawai' => $id_pegawai, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun));
            $row = $query->result();


            foreach ($row as $nilai) {
                $newPerilaku = [
                    'id_pegawai' => $id_pegawai,
                    'periode_bulan' => 1,
                    'periode_tahun' => 2026,
                    'tgl_input' => date('Y-m-d'),
                    'id_pertanyaan' => $nilai->id_pertanyaan,
                    'jns_item' => $nilai->jns_item,
                    'jawaban' => $nilai->jawaban,
                    'poin' => $nilai->poin
                ];

                // print_array($newPerilaku);
                //$this->db->insert('tbl_penilaian_perilaku', $newPerilaku);
            }

            echo $list->nama . ' insert<br>';
            //print_array($row);
        }
    }

    function list_aktifitas($id_pegawai)
    {

        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $nip             = $this->session->userdata('nip');

        if ($periode_bulan == '') {
            $day = date('d');
            $tahun = date('Y');
            if ($day > 10) {
                $bulan = date('m');
            } else {
                $bulan = date('m') - 1;
            }

            $this->session->set_userdata('periode_bulan', $bulan);
            $this->session->set_userdata('periode_tahun', $tahun);
        } else {
            $bulan = $periode_bulan;
            $tahun = $periode_tahun;
        }

        $periode = $periode_tahun . '-' . $periode_bulan;
        $periode = date('Y-m', strtotime($periode));;

        $data['pengajuan_dinas_luar'] = $this->Absensi_model->getListPengajuanDL($id_pegawai, $periode);
        $data['dataCuti'] = $this->Cuti_model->getCutiPegawai($id_pegawai, $periode);
        $data['dataIzinSakit'] = $this->Presensi_model->getDataIzinSakitPegawai($id_pegawai);
        $data['totalAktifitas'] =  $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);

        $data['detail_pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
       // $data['dataAktifitasPegawai'] = $this->Kinerja_model->getAktifitasPegawaiPerBulan($id_pegawai, $periode);

        //print_array($data['dataAktifitasPegawai']);
        $this->load->view('admin/penilaian_kinerja/validasi_aktifitas', $data);
    }

    function ajaxGetAktifitasHarian()
    {

        $tanggal     = $this->input->post('tanggal');
        $id_pegawai  = $this->input->post('id_pegawai');

        $qry = $this->db->get_where('ts_kinerja', array('id_pegawai' => $id_pegawai, 'tgl' => $tanggal));
        $data['aktifitas'] = $qry->result();
        $data['tanggal_aktifitas']    = $tanggal;
        $data['id_pegawai'] = $id_pegawai;

        $this->load->view('penilaian_kinerja/view_aktifitas_harian', $data);
    }


    function refreshInputanKinerja()
    {

        $id_pegawai = $this->input->post('id_pegawai');
        $periode    = $this->input->post('periode');

        $explod     = explode("-", $periode);
        $bulan = $explod[1];
        $tahun = $explod[0];

        $jumlahHariKerja = $this->Master_model->getMenitEfektifBulan($bulan, $tahun);
        $menitEfektifBulanan  = $jumlahHariKerja * 300;



        $totalInput = $this->Kinerja_model->getJumlahInputPerBulan($id_pegawai, $periode);
        if ($totalInput == 0) {
            $totalInput = 1;
        }

        $persenInput = ($totalInput / $menitEfektifBulanan) * 100;
        if ($persenInput > 100) {
            $persenInput = 100;
        }


        $totalApprove  = $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);
        if ($totalApprove == '') {
            $totalApprove  = 0;
        }
        $persenApprove = ($totalApprove / $totalInput) * 100;


        $totalReject = $this->Kinerja_model->getAktifitasByStatus($id_pegawai, $periode, 2);
        if ($totalReject == '') {
            $totalReject  = 0;
        }
        $persenReject = ($totalReject / $totalInput) * 100;


        echo '
		  <div>
			<div class="flex items-center justify-between gap-4 mb-2">
				<h6>Total Input Aktifitas</h6>
				<span class="text-slate-500 dark:text-zink-200">' . $totalInput . '</span>
			</div>
			<div class="w-full h-3.5 rounded bg-slate-200 dark:bg-zink-600">
				<div class="h-3.5 rounded bg-custom-500" style="width:  ' . $persenInput . '%"></div>
			</div>
		</div>
		
		<div>
			<div class="flex items-center justify-between gap-4 mb-2">
				<h6>Disetujui</h6>
				<span class="text-slate-500 dark:text-zink-200">' . $totalApprove . '</span>
			</div>
			<div class="w-full h-3.5 rounded bg-slate-200 dark:bg-zink-600">
				<div class="h-3.5 rounded bg-green-500" style="width: ' . $persenApprove . '%"></div>
			</div>
		</div>
		<div>
			<div class="flex items-center justify-between gap-4 mb-2">
				<h6>Ditolak</h6>
				<span class="text-slate-500 dark:text-zink-200">' . $totalReject . '</span>
			</div>
			<div class="w-full h-3.5 rounded bg-slate-200 dark:bg-zink-600">
				<div class="h-3.5 rounded bg-red-500 dark:text-red-500" style="width: ' . $persenReject . '%"></div>
			</div>
		</div>';
    }



    function aktivitas($id_pegawai, $bulan, $tahun)
    {
        $data['pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['dataAktifitasPegawai'] = $this->Kinerja_model->getAktifitasPegawai($id_pegawai);
        $this->load->view('penilaian_kinerja/penilaian_aktivitas', $data);
    }


    // function perilaku($id_pegawai, $bulan, $tahun){
    //     $cekPenilaian = $this->Kinerja_model->cekPenilaianPegawai($id_pegawai, $bulan, $tahun);

    //     if ($cekPenilaian == false) {
    //         //klo belum pernah dinliai sama sekali
    //         $this->Kinerja_model->insertInitialPerilaku($id_pegawai, $bulan, $tahun);
    //     }


    //     $data['totalPoin']  = $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);
    //     $data['pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
    //     $data['datalist'] = $this->Kinerja_model->getListKategoriPenilaianPerilaku();
    //     $this->load->view('penilaian_kinerja/penilaian_perilaku', $data);
    // }


    function getInputanAktifitasPegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $tgl        = $this->input->post('tanggal');

        $tanggal = format_db($tgl);
        $data['dataAktifitas'] = $this->Kinerja_model->getDataInputAktifitas($id_pegawai, $tanggal);
        $data['tanggal'] = $tanggal;
        $data['id_pegawai'] = $id_pegawai;

        $cekData = $this->Kinerja_model->cekDataAktifitasPending($id_pegawai, $tanggal);
        if (count($cekData) == 0) {
            //udah ga ada yang pending
            $data['approved_all'] = true;
        } else {
            $data['approved_all'] = false;
        }


        $this->load->view('penilaian_kinerja/view_inputan_aktifitas', $data);
    }

    function approveChecklist($id_pegawai)
    {
        $id_validator = $this->session->userdata('id_pegawai');
        $chk_child        = $this->input->post('chk_child');
        $status           = $this->input->post('status');
        $tgl_validasi     = date('Y-m-d H:i:s');
        //print_array($this->session->userdata);

       for ($i = 0; $i < count($chk_child); $i++) {
            $id = $chk_child[$i];

            if ($status == 'approve') {
                $newStatus = 1;
            } else {
                $newStatus = 2;
            }

            $this->db->where('id', $id);
            $qry = $this->db->get('ts_kinerja');
            $row = $qry->row();

            // =========================
            // VALIDASI BATAS INPUT
            // =========================
            $tgl_kinerja = $row->tgl;
            $date_in = $row->date_in;

            $bulan = date('m', strtotime($tgl_kinerja));
            $tahun = date('Y', strtotime($tgl_kinerja));

            $bulan++;
            if ($bulan > 12) {
                $bulan = 1;
                $tahun++;
            }

            $batas = strtotime($tahun . '-' . $bulan . '-05 23:59:59');

            if (strtotime($date_in) > $batas) {
                // ❌ Lewat batas → skip update
                $this->session->set_flashdata('message', 'Ada data yang melewati batas input (maks tgl 5 bulan berikutnya)');
                $this->session->set_flashdata('type', 'error');
                continue; // lompat ke data berikutnya
            }

            // =========================
            // UPDATE JIKA VALID
            // =========================
            $this->db->where('id', $id);
            $this->db->set('status', $newStatus);
            $this->db->set('tgl_validasi', $tgl_validasi);
            $this->db->set('id_validator', $id_validator);
            $this->db->update('ts_kinerja');
        }

       redirect('admin/penilaian_kinerja/list_aktifitas/' . $id_pegawai);
    }

    function approve_aktifitas()
    {
        $id_validator = $this->session->userdata('id_pegawai');
        $id           = $this->input->post('id');
        $status       = $this->input->post('status');
        $tgl_validasi = date('Y-m-d H:i:s');


        $this->db->where('id', $id);
        $this->db->set('status', $status);
        $this->db->set('tgl_validasi', $tgl_validasi);
        $this->db->set('id_validator', $id_validator);
        $this->db->update('ts_kinerja');


        if ($status == 1) {
            echo 'Aktifitas  telah disetujui';
        } else {
            echo 'Aktifitas  telah ditolak';
        }
    }

    function reject_aktifitas()
    {
        $id_validator = $this->session->userdata('id_pegawai');
        $id           = $this->input->post('id');
        $status       = $this->input->post('status');
        $tgl_validasi = date('Y-m-d H:i:s');


        $this->db->where('id', $id);
        $this->db->set('status', $status);
        $this->db->set('tgl_validasi', $tgl_validasi);
        $this->db->set('id_validator', $id_validator);
        $this->db->update('ts_kinerja');


        if ($status == 1) {
            echo 'Aktifitas  telah disetujui';
        } else {
            echo 'Aktifitas  telah ditolak';
        }
    }



    function cancel_acc_aktifitas()
    {
        $id_validator = $this->session->userdata('id_pegawai');
        $id           = $this->input->post('id');
        $status       = $this->input->post('status');
        $tgl_validasi = date('Y-m-d H:i:s');


        $this->db->where('id', $id);
        $this->db->set('status', 0);
        $this->db->update('ts_kinerja');

        echo 'Aktifitas batal disetujui';
    }

    function approve_all_aktifitas($id_pegawai)
    {
        $id_validator = $this->session->userdata('id_pegawai');
        $tgl_validasi = date('Y-m-d H:i:s');
        $bulan = $this->session->userdata('periode_bulan'); 
        $tahun = $this->session->userdata('periode_tahun'); 
        $periode = $tahun.'-'.$bulan;
        $periode = date('Y-m', strtotime($periode));

        $start = $periode . '-01';
        $end   = date("Y-m-t", strtotime($start));

        $this->db->select('id');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('status', 0);
        $this->db->where('tgl >=', $start);
        $this->db->where('tgl <=', $end);

        $qry = $this->db->get('ts_kinerja');
        $row = $qry->result();

        foreach ($row as $r) {
           $id = $r->id;

            $this->db->where('id', $id);
            $this->db->set('status', 1);
            $this->db->set('tgl_validasi', $tgl_validasi);
            $this->db->set('id_validator', $id_validator);
            $this->db->update('ts_kinerja');

        }

        redirect('admin/penilaian_kinerja/list_aktifitas/'. $id_pegawai);

       

       // echo 'Aktifitas tanggal ' . format_semi($tanggal) . ' telah disetujui';
    }

    function ajaxGetPoinPerilaku()
    {
        $data_value = $this->input->post('value');
        $id_pegawai = $this->input->post('id_pegawai');
        $bulan = $this->session->userdata('periode_bulan');
        $tahun = $this->session->userdata('periode_tahun');

        $xpl             = explode("_", $data_value);
        $jawaban         = $xpl[0];
        $id_jawaban   = $xpl[1];
        $jns_item   = $xpl[2];


        if ($jns_item == 2) {
            //jenis penilaian semakin kecil semakin baik
            $poin = getPoinPerilaku($jawaban);
        } else {
            //jenis penilaian semakin besar semakin baik
            $poin = $jawaban;
        }


        $this->db->where('id', $id_jawaban);
        $this->db->set('jawaban', $jawaban);
        $this->db->set('poin', $poin);
        $this->db->update('tbl_penilaian_perilaku');


        $totalPoin          = $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);


        echo $totalPoin;
    }

    function set_session_validator()
    {
        $id_pj = $this->input->post('id_pj');

        $this->session->set_userdata('id_pj', $id_pj);
        return true;
    }


    function tarik_data($nip, $id_pegawai_baru)
    {
        $data_ekin = $this->Kinerja_model->getIDPegawaiekin($nip);
        $id_pegawai_ekin = $data_ekin[0]->id_pegawai;
        $nama = $data_ekin[0]->nama;


        // print_array($data_ekin );
        // exit;

        if (!empty($data_ekin)) {
            redirect('admin/penilaian_kinerja/proses_tarik_data/' . $id_pegawai_ekin . '/' . $id_pegawai_baru);
        } else {
            $pesan =  createMessageInfo('data pegawai tidak ditemukan');
            $this->session->set_flashdata('message', $pesan);
            redirect('admin/penilaian_kinerja/index');
        }
    }


    function capaian_kinerja()
    {


        $id_pegawai    = $this->session->userdata('id_pegawai');
        $usergroup     = $this->session->userdata('usergroup');
        $id_pj_sess    = $this->session->userdata('id_pj');
        $bulan         = $this->session->userdata('periode_bulan');
        $tahun         = $this->session->userdata('periode_tahun');

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        if ($id_pj_sess != '') {
            $id_validator = $id_pj_sess;
        } else {
            $id_validator = $id_pegawai;
        }

        $jns_pegawai = 'non_pns';

        $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_validator);
        $id_puskesmas = $detail_pegawai->id_puskesmas;
        $klaster      = $detail_pegawai->klaster;



        if ($id_puskesmas == 1) {
            //puskesmas cilincing
            $pegawai = $this->Pegawai_model->getPegawaiByKlaster($id_puskesmas, $klaster, $jns_pegawai, '*');
        } else {
            //pustu
            $pegawai = $this->Pegawai_model->getPegawaiByIDPuskesmas($id_puskesmas, $jns_pegawai, '*');
        }

        $data['puskesmas']        = $this->Presensi_model->getListPuskesmas();
        $data['data_shift_kerja'] = $this->Presensi_model->getShiftKerja();
        $data['validator'] = $this->Pegawai_model->getValidator();
        $data['capaian'] = $this->Kinerja_model->get_capaian_by_puskesmas($id_puskesmas, $periode, $klaster);

        $data['detail_validator'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);

        $this->load->view('admin/penilaian_kinerja/capaian_kinerja', $data);
    }


    function update_capaian()
    {
        $id_validator = $this->session->userdata('id_pegawai');
        $id_pj_sess   = $this->session->userdata('id_pj');

        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        // $id_pegawai = 891;
        // $this->Kinerja_model->copyPenilaianTerakhir($id_pegawai, $bulan, $tahun);
        // exit;

        //get capaian maksimal pada periode tsb
        // $this->db->select('total_capaian');
        // $qry = $this->db->get_where('tbl_capaian', array('periode' => $periode), 1, 0);
        // $row = $qry->row();

        // echo $periode;
        // print_array($row);
        // exit;
        // $capaian_max = $row->total_capaian;




        $jumlahHariKerja = $this->Master_model->getMenitEfektifBulan($bulan, $tahun);
        $waktu_efektif   = $jumlahHariKerja * 300;

        $serapan         = SERAPAN;
        $id_pegawai_post = $this->input->post('check_id');

        //print_array($id_pegawai_post);
        for ($i = 0; $i < count($id_pegawai_post); $i++) {
            $id_pegawai = $id_pegawai_post[$i];
            $nip = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);

            $totalAktifitas     =  $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);
            $poinPerilaku       =  $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);

            $poinPerilaku = $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);

            // if ($poinPerilaku == 0) {

            //     // copy penilaian bulan terakhir
            //     $this->Kinerja_model->copyPenilaianTerakhir($id_pegawai, $bulan, $tahun);

            //     // hitung ulang poin perilaku
            //     $poinPerilaku = $this->Kinerja_model->getPoinPerilaku($id_pegawai, $bulan, $tahun);
            // }

            $returnPerhitungan  =  $this->Kinerja_model->getMenitPenambahPengurang($id_pegawai, $periode);

            //echo print_array($returnPerhitungan);
            $menitPenambah   = $returnPerhitungan[0];
            $menitPengurang  = $returnPerhitungan[1];


            $nilaiTotalAktifitas = $totalAktifitas + $menitPenambah;
            $totalWaktuEfektif   = $waktu_efektif - $menitPengurang;

            $nilaiLebihKecil     = min($totalWaktuEfektif, $nilaiTotalAktifitas);
            $bobotAktifitas      = ($nilaiLebihKecil / $waktu_efektif) * 100;
            $bobotTotal          = round($bobotAktifitas * 0.7, 2);


            $cuti = $this->acm->getCutiBersalin($id_pegawai);
            $persenCuti = 0;

            if ($cuti) {
                $persenCuti = persen_cuti_bulanan(
                    $cuti->tgl_mulai,
                    $cuti->tgl_selesai,
                    $bulan,
                    $tahun
                );
            }

            if ($persenCuti >= 50) {
                $totalCapaian = 50;
            } else {
                $totalCapaian = number_format($bobotTotal + $poinPerilaku + $serapan, 2);
            }

           
            $id_row = $this->Kinerja_model->cekDataCapaian($nip, $periode);

            $this->db->select('tgl_masuk');
            $qry = $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
            $row = $qry->row();
            $tgl_masuk = $row->tgl_masuk;

            $result = getHariKerjaPegawai($tgl_masuk, $bulan, $tahun);
            $isPegawaiBaru = $result['pegawai_baru'];
            $hari_kerja = $result['hari_kerja']; // jumlah hari kerja pegawai baru
            $hari_kerja_full = $result['hari_kerja_full']; // jumlah hari kerja perbulan dikurang sabtu-minggu
            if ($isPegawaiBaru) //jika memang itu pegawai baru, maka hitung prorate
            {
                $totalCapaian = ($capaian_max / $hari_kerja_full) * $hari_kerja;
            }



            if ($id_row == 0) {
                $newData = array(
                    'periode' => $periode,
                    'nip' => $nip,
                    'bobot_aktifitas' => $bobotTotal,
                    'perilaku' => $poinPerilaku,
                    'serapan' => $serapan,
                    'total_capaian' => $totalCapaian,
                    'crated_at' => date('Y-m-d H:i:s')
                );
                $this->db->insert('tbl_capaian', $newData);
            } else {
                $newData = array(
                    'bobot_aktifitas' => $bobotTotal,
                    'perilaku' => $poinPerilaku,
                    'serapan' => $serapan,
                    'total_capaian' => $totalCapaian,
                    'crated_at' => date('Y-m-d H:i:s')
                );
                $this->db->where('id', $id_row);
                $this->db->update('tbl_capaian', $newData);
            }
        }




        
        redirect('admin/penilaian_kinerja/capaian_kinerja');
    }


    function proses_tarik_data($id_pegawai, $id_ekin_v2)
    {


        $getInputan = $this->Kinerja_model->getInputan($id_pegawai);

        $cekData = $this->Kinerja_model->delete_inputan($id_ekin_v2);

        $dataPerilaku = $this->Kinerja_model->getDataPerilkuEkin1($id_pegawai);

        foreach ($getInputan as $akt) {

            $tgl = $akt->tgl;
            $jns_kegiatan = $akt->jns_kegiatan;
            $id_kegiatan = $akt->id_kegiatan;
            $jam_mulai = $akt->jam_mulai;
            $jam_selesai = $akt->jam_selesai;
            $volume = $akt->volume;
            $ket  = $akt->ket;
            $status  = $akt->status;
            $nama_kegiatan  = $akt->nama_kegiatan;
            $waktu  = $akt->waktu;

            $total = $waktu * $volume;


            // if(!empty($cekData)){
            //     $id =$cekData[0]->id;
            //      $this->db->where('id', $id);
            //      $this->db->delete('ts_kinerja');
            // }


            $new_data = array(
                'id_pegawai' => $id_ekin_v2,
                'tgl' => $tgl,
                'jns_kegiatan' => $jns_kegiatan,
                'id_indikator' => 0,
                'nama_kegiatan' => $nama_kegiatan,
                'jam_mulai' => $jam_mulai,
                'jam_selesai' => $jam_selesai,
                'volume' => $volume,
                'waktu_efektif' => $waktu,
                'total' =>  $total,
                'status' =>  $status,
                'ket' =>  $ket
            );

            $this->db->insert('ts_kinerja', $new_data);
        }


        // $sql ="DELETE FROM tbl_penilaian_perilaku WHERE id_pegawai = $id_ekin_v2 AND periode_bulan = '01' AND periode_tahun = '2024'";
        // $this->db->query($sql);


        for ($i = 0; $i < count($dataPerilaku); $i++) {


            $tgl_input = $dataPerilaku[$i]->tgl_input;
            $poin = $dataPerilaku[$i]->poin;
            $jawaban = $dataPerilaku[$i]->jawaban;
            $jns_item = $dataPerilaku[$i]->jns_item;
            $id_item = $dataPerilaku[$i]->id_pertanyaan;


            $this->Kinerja_model->insertPenilaianPerilaku($id_ekin_v2, 1, '2024', $tgl_input, $id_item, $jns_item, $jawaban, $poin);
        }

        $pesan =  createMessageInfo('data kinerja berhasil ditransfer');
        $this->session->set_flashdata('message', $pesan);
        redirect('admin/penilaian_kinerja/index');


        #print_array($new_data);
    }

    function resetPenilainPerilaku($id_pegawai, $bulan, $tahun)
    {

        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('periode_bulan', $bulan);
        $this->db->where('periode_tahun', $tahun);
        $this->db->delete('tbl_penilaian_perilaku');

        $pesan =  createMessageInfo('Data penilaian perilaku berhasil direset');
        $this->session->set_flashdata('message', $pesan);
        redirect('admin/penilaian_kinerja/nilai_perilaku/' . $id_pegawai);
    }

    function import_kinerja($id_pegawai)
    {

        $this->db->order_by('tgl', 'ASC');
        $this->db->select('*');
        $qry = $this->db->get_where('ts_kinerja_backup', array('id_pegawai' => $id_pegawai));


        $dataAktifitasPegawai = $qry->result();



        foreach ($dataAktifitasPegawai as $aktifitas) {
            $tgl = $aktifitas->tgl;
            $jns_kegiatan = $aktifitas->jns_kegiatan;
            $id_indikator = $aktifitas->id_indikator;
            $nama_kegiatan = $aktifitas->nama_kegiatan;
            $jam_mulai = $aktifitas->jam_mulai;
            $jam_selesai = $aktifitas->jam_selesai;
            $volume = $aktifitas->volume;
            $waktu_efektif = $aktifitas->waktu_efektif;
            $ket = $aktifitas->ket;

            $total = $aktifitas->total;
            $status = $aktifitas->status;

            $tgl_validasi = $aktifitas->tgl_validasi;
            $id_validator = $aktifitas->id_validator;
            $note_validasi = $aktifitas->note_validasi;


            if ($tgl >= '2024-08-01') {
                $new_array = array(
                    'id_pegawai' => $id_pegawai,
                    'tgl' => $tgl,
                    'jns_kegiatan' => $jns_kegiatan,
                    'id_indikator' => $id_indikator,
                    'nama_kegiatan' => $nama_kegiatan,
                    'jam_mulai' => $jam_mulai,
                    'jam_selesai' => $jam_selesai,
                    'volume' => $volume,
                    'waktu_efektif' => $waktu_efektif,
                    'ket' => $ket,
                    'total' => $total,
                    'status' => $status,

                );

                $this->db->insert('ts_kinerja', $new_array);

                // print_array($new_array);

            }
        }

        echo 'data berhasil diimport';
        //print_array($dataAktifitasPegawai);
    }


    function editKinerja($id_pegawai)
    {

        $qry = $this->db->get_where('ts_kinerja', array('id_pegawai' => $id_pegawai, 'tgl >=' => '2025-01-01'));
        $data['dataAktifitasPegawai'] = $qry->result();

        $this->load->view('penilaian_kinerja/edit_kinerja', $data);
    }

    function update_aktifitas($id_pegawai)
    {

        //print_array($this->input->post());

        $id = $this->input->post('id');
        $waktu_efektif = $this->input->post('waktu_efektif');
        $volume = $this->input->post('volume');


        for ($i = 0; $i < count($id); $i++) {


            $sub_volume = $volume[$i];

            $total = $waktu_efektif[$i] * $sub_volume;

            $this->db->where('id', $id[$i]);
            $this->db->set('total', $total);
            $this->db->set('volume', $sub_volume);
            $this->db->set('status', 1);

            $this->db->update(('ts_kinerja'));
        }


        redirect('admin/penilaian_kinerja/editKinerja/' . $id_pegawai);
    }
}
