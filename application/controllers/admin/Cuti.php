<?php
defined("BASEPATH") or exit("No direct script access allowed");
class Cuti extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Profile_model");
        $this->load->model('Admin_cuti_model', 'acm');
        $this->Auth_model->cekAuthLogin();
    }

    function index()
    {
        $data["history"] = $this->Cuti_model->getMyHistoryCuti();

        $id_pegawai = $this->session->userdata("id_pegawai");
        $nip = $this->session->userdata("nip");
        $data["data_detail"] = $this->Pegawai_model->getDataDetailPegawai($nip);
        $data["pegawai"] = $this->Pegawai_model->getDataEditPegawai( $id_pegawai);

        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $this->load->view("cuti/my_cuti", $data);
    }


    function filter_date(){
        $this->session->set_userdata($this->input->post());
        redirect('admin/cuti/pengajuan_cuti');
    }

    function sisa_cuti()
    {
        $data['pegawai'] =  $this->Pegawai_model->getPegawaiforListingTKD('2024',500);
        $this->load->view("admin/cuti/sisa_cuti", $data);
    }


    function detail_pengajuan_cuti($id_cuti, $tahun_cuti=2026)  {

        $usergroup = $this->session->userdata('usergroup');
        if($usergroup==1){
            $role = 'kapus';
        }else if($usergroup==2){
            $role = 'ktu';
        }else{
            $role = 'kapustu';
        }

        if($tahun_cuti <= 2025){
            $cuti = $this->Cuti_model->getDetailPengajuanCuti($id_cuti);
            $data["cuti"]                = $cuti;
             $this->load->view("admin/cuti/detail_pengajuan_cuti2025", $data);
        }else{
            $cuti = $this->acm->getDetailPengajuanCuti($id_cuti, $role);
               
        
        
            $id_pegawai = $cuti->id_pegawai;

            $data['role']                = $role;
            //$data["cuti"] = $this->Cuti_model->getDetailPengajuanCuti($id_cuti, $role);
            $data["cuti"]                = $cuti;
            $tahun_list                  = [2025, 2026];
            $data['rekap_hak_cuti']      = $this->acm->get_rekap_cuti_pegawai_by_id($id_pegawai, $tahun_list);

        
            $this->load->view("admin/cuti/detail_pengajuan_cuti", $data);
        }



    
    }


     function sisa_cuti_fixed()
    {
        $tahunList = [2025, 2026];

        $this->db->select('
            p.id_pegawai,
            p.nama,
            j.nama as nama_jabatan,
            hc.tahun,
            hc.hak_total,
            hc.hak_terpakai,
            hc.hak_reserved
        ');
        $this->db->where('jns_pegawai','non_pns');
        $this->db->where('status_kerja >',0);
        $this->db->from('mst_pegawai p');
        $this->db->join('mst_jabatan j', 'j.id = p.id_jabatan', 'left');
        $this->db->join(
            'ts_hak_cuti_pegawai hc',
            'hc.id_pegawai = p.id_pegawai AND hc.tahun IN (' . implode(',', $tahunList) . ')',
            'left'
        );
        $this->db->order_by('p.nama', 'ASC');

        $query = $this->db->get()->result();

        $dataPegawai = [];

            foreach ($query as $row) {
                $id = $row->id_pegawai;

                if (!isset($dataPegawai[$id])) {
                    $dataPegawai[$id] = [
                        'id_pegawai' => $row->id_pegawai,
                        'nama'       => $row->nama,
                        'jabatan'    => $row->nama_jabatan,
                        'cuti'       => []
                    ];
                }

                if ($row->tahun) {
                    $sisa = $row->hak_total - $row->hak_terpakai - $row->hak_reserved;

                    $dataPegawai[$id]['cuti'][$row->tahun] = [
                        'hak'       => (int) $row->hak_total,
                        'terpakai'  => (int) $row->hak_terpakai,
                        'sisa'      => max(0, $sisa)
                    ];
                }
            }

        $data['list'] = $dataPegawai;
        $data['tahun'] = $tahunList;


        $this->load->view("admin/cuti/sisa_cuti_fixed", $data);
    }



    function insert_hak_cuti($tahun){
      ///  print_array($this->input->post());
         $id_pegawai = $this->input->post('id_pegawai');
         $jumlah = $this->input->post('hari');

   
         $newData = [
            'id_pegawai'=> $id_pegawai,
            'hak_cuti_tahun'=> $tahun,
            'jns_cuti' => 'TAHUNAN',
            'jns_transaksi' => 'OPENING',
            'keterangan' => 'input sisa cuti tahun '.$tahun,
            'sisa_awal' =>0,
            'jumlah' => $jumlah,
            'sisa_akhir' => $jumlah,
            'created_at' => date('Y-m-d H:i:s')
         ];


         $this->db->insert('ts_log_cuti', $newData);
         redirect('admin/cuti/sisa_cuti');
    }



    public function simpanSisaCuti2025()
    {
        $idPegawai = $this->input->post('id_pegawai');
        $sisa      = (int) $this->input->post('sisa_cuti');

        // cek sudah ada atau belum
        $exist = $this->db->get_where('ts_hak_cuti_pegawai', [
            'id_pegawai' => $idPegawai,
            'tahun' => 2025
        ])->row();

        if ($exist) {
            // update
            $this->db->where('id', $exist->id)->update('ts_hak_cuti_pegawai', [
                'hak_total'    => $sisa,
                'hak_terpakai' => 0,
                'hak_reserved' => 0,
                'updated_at'   => date('Y-m-d H:i:s')
            ]);
        } else {
            // insert
            $this->db->insert('ts_hak_cuti_pegawai', [
                'id_pegawai'   => $idPegawai,
                'tahun'        => 2025,
                'hak_total'    => $sisa,
                'hak_terpakai' => 0,
                'hak_reserved' => 0
            ]);
        }

        // LOG (WAJIB)
        $this->db->insert('ts_log_mutasi_cuti', [
            'id_pegawai'   => $idPegawai,
            'tahun'        => 2025,
            'tipe'         => 'manual',
            'jumlah'       => $sisa,
            'saldo_sebelum'=> 0,
            'saldo_sesudah'=> $sisa,
            'keterangan'   => 'Adjustment awal sisa cuti 2025',
            'created_by'   => $this->session->userdata('id_user')
        ]);

        $this->session->set_flashdata('success', 'Sisa cuti 2025 berhasil disimpan');
        redirect('admin/cuti/sisa_cuti_fixed');
    }

    public function generateCuti2026()
    {
        $pegawai = $this->db->get('mst_pegawai')->result();

        foreach ($pegawai as $p) {

            $cek = $this->db->get_where('ts_hak_cuti_pegawai', [
                'id_pegawai' => $p->id_pegawai,
                'tahun' => 2026
            ])->row();

            if (!$cek) {
                $this->db->insert('ts_hak_cuti_pegawai', [
                    'id_pegawai' => $p->id_pegawai,
                    'tahun' => 2026,
                    'hak_total' => 12,
                    'hak_terpakai' => 0,
                    'hak_reserved' => 0
                ]);

                $this->db->insert('ts_log_mutasi_cuti', [
                    'id_pegawai' => $p->id_pegawai,
                    'tahun' => 2026,
                    'tipe' => 'manual',
                    'jumlah' => 12,
                    'saldo_sebelum' => 0,
                    'saldo_sesudah' => 12,
                    'keterangan' => 'Generate hak cuti awal tahun 2026',
                    'created_by' => $this->session->userdata('id_user')
                ]);
            }
        }

        $this->session->set_flashdata('success', 'Hak cuti 2026 berhasil digenerate');
        redirect('admin/cuti/adjustment');
    }

    
	public function calendar_cuti()
	{
		$start = date('Y-01-01');
		$end   = date('Y-12-31');

		$id_validator = $this->session->userdata('id_pegawai');
        $nrk = $this->session->userdata('nrk');
        $qry = $this->db->get_where('mst_validator', ['nrk'=> $nrk]);
        $row = $qry->row();

        if($row)
        {
            $id_puskesmas = $row->id_puskesmas;
        }
       

		$data = $this->acm->getCutiForCalendarByPuskesmas(
			$start,
			$end,
			$id_puskesmas
		);

		$events = [];

		foreach ($data as $row) {

                // default abu
                $warna = '#adb5bd';

                if ($row->status_akhir === 'disetujui') {
                    // final
                    $warna = '#31db78';
                } else {
                    // status berjalan berdasarkan approval
                    switch ($row->role_approval) {
                        case 'pengganti':
                            $warna = '#fff6db';
                            break;
                        case 'kapustu':
                            $warna = '#ffc107';
                            break;
                        case 'ktu':
                            $warna = '#8c58ee';
                            break;
                        case 'kapus':
                            $warna = '#36c7eb';
                            break;
                    }
                }

                $events[] = [
                    'id'    => $row->id,
                    'title' => $row->nama,
                    'start' => $row->tgl_mulai,
                    'end'   => date('Y-m-d', strtotime($row->tgl_selesai . ' +1 day')),
                    'backgroundColor' => $warna,
                    'borderColor'     => $warna,

                    // 🔥 penting buat panel kanan / debug
                    'extendedProps' => [
                        'role_approval'  => $row->role_approval,
                        'level_approval' => $row->level_approval,
                        'status_akhir'   => $row->status_akhir
                    ]
                ];
            }

		echo json_encode($events);
	}
	

    public function get_cuti_by_tanggal()
    {
        $tanggal = $this->input->get('tanggal');


        //print_array($this->session->userdata());
        $id_validator = $this->session->userdata('id_pegawai');
        $nrk = $this->session->userdata('nrk');
        $qry = $this->db->get_where('mst_validator', ['nrk'=> $nrk]);
        $row = $qry->row();

        if($row)
        {
            $id_puskesmas = $row->id_puskesmas;
        }

        if (!$tanggal) {
            echo '<p class="text-muted">Tanggal tidak valid</p>';
            return;
        }

        $data['cuti'] = $this->acm->getCutiByTanggal(
            $tanggal,
            $id_puskesmas
        );

        $this->load->view('admin/cuti/list_cuti_tanggal', $data);
    }


   

    function pengajuan_cuti()
    {
         $id_validator = $this->session->userdata("id_pegawai");
         $this->load->view("admin/cuti/pengajuan_cuti");

    }


    function table_pengajuan_cuti(){
        $start = '2026-01-01';
        $end = '2026-04-01';
        
        $data['cuti'] = $this->acm->getDatalistCuti($start, $end);
        $this->load->view("admin/cuti/table_pengajuan_cuti", $data);
}

    public function ajax_setujui_cuti()
    {
       
       
        $input = json_decode(file_get_contents("php://input"), true);

        $id_pengajuan = $input['id_pengajuan'] ?: null; 
        $role_approval = $input['role_approval'] ?: 'pengganti';
        $id_pegawai = $this->session->userdata("id_pegawai");

        $usergroup = $this->session->userdata('usergroup');


        if($usergroup==2){
                // Ambil row approval kapustu
            $approval = $this->db
                ->where('id_pengajuan_cuti', $id_pengajuan)
                ->where('id_pegawai_approval', $id_pegawai)
                ->where('status', 'pending')
                ->get('ts_pengajuan_cuti_approval')
                ->row();
        }else{
              // Ambil row approval kapustu
            $approval = $this->db
                ->where('id_pengajuan_cuti', $id_pengajuan)
                ->where('role_approval', $role_approval)
                ->where('id_pegawai_approval', $id_pegawai)
                ->where('status', 'pending')
                ->get('ts_pengajuan_cuti_approval')
                ->row();
        }
                      


      

        if (!$approval) {
            show_error("Anda tidak berhak atau approval sudah diproses");
        }

        $this->db->trans_start();

        // 1. Approve pengganti
        $this->db->where('id', $approval->id)->update('ts_pengajuan_cuti_approval', [ 'status' => 'approved','approved_at' => date('Y-m-d H:i:s')]);

        // 2. Aktifkan ka tu / kapuscam

        if($role_approval=='kapustu'){
            $next_approval = 'ktu'; 
            $this->acm->updateNextRoleApproval($id_pengajuan, $next_approval);

        }else if($role_approval=='ktu'){
            $next_approval = 'kapus'; 
            $this->acm->updateNextRoleApproval($id_pengajuan, $next_approval);
        }else{
            //
            $this->approveKapusInduk($id_pengajuan);
           // $this->Presensi_model->update_absensi_cuti($pin, $tanggal, $alasan_cuti);
        }
        
        $this->db->trans_complete();

           echo json_encode([
            'status' => true,
            'message' => 'Permohonan pengganti cuti berhasil disetujui'
        ]);
        //redirect('admin/cuti/detail_pengajuan_cuti/'.$id_pengajuan);
    }


    public function approveKapusInduk($id_pengajuan)
    {
        $id_kapus = $this->session->userdata('id_pegawai');

     
        // Ambil approval Kapus Induk yang masih pending
        $approval = $this->db
            ->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('role_approval', 'kapus')
            ->where('id_pegawai_approval', $id_kapus)
            ->get('ts_pengajuan_cuti_approval')
            ->row();



        if (!$approval) {
            show_error("Anda tidak berhak atau approval sudah diproses");
        }

        // Ambil data cuti
        $cuti = $this->db
            ->where('id', $id_pengajuan)
            ->get('ts_pengajuan_cuti')
            ->row();

        $id_pegawai_cuti = $cuti->id_pegawai;
        $hak_tahun      = $cuti->tahun_hak_cuti;
        $lama_cuti      = $cuti->lama_cuti;

        $this->db->trans_start();

        // 1. Approve Kapus Induk
        $this->db->where('id', $approval->id)
            ->update('ts_pengajuan_cuti_approval', [
                'status' => 'approved',
                'approved_at' => date('Y-m-d H:i:s')
            ]);

        // 2. Update status pengajuan
        $this->db->where('id', $id_pengajuan)
            ->update('ts_pengajuan_cuti', [
                'status_akhir' => 'disetujui',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // 3. Ambil saldo sebelum
        $rowHak = $this->db
            ->where('id_pegawai', $id_pegawai_cuti)
            ->where('tahun', $hak_tahun)
            ->get('ts_hak_cuti_pegawai')
            ->row();

        $saldo_sebelum = $rowHak->hak_total - ($rowHak->hak_terpakai + $rowHak->hak_reserved);

        // 4. Pindahkan reserve → terpakai
        $this->db->set('hak_reserved', 'hak_reserved - '.$lama_cuti, false);
        $this->db->set('hak_terpakai', 'hak_terpakai + '.$lama_cuti, false);
        $this->db->where('id_pegawai', $id_pegawai_cuti);
        $this->db->where('tahun', $hak_tahun);
        $this->db->update('ts_hak_cuti_pegawai');

        // 5. Hitung saldo sesudah
        $saldo_sesudah = $saldo_sebelum - $lama_cuti;

        // 6. Insert log FINAL (JANGAN UPDATE LOG LAMA)
        $this->db->insert('ts_log_mutasi_cuti', [
            'id_pegawai' => $id_pegawai_cuti,
            'tahun' => $hak_tahun,
            'id_pengajuan_cuti' => $id_pengajuan,
            'tipe' => 'final',
            'jumlah' => -$lama_cuti,
            'saldo_sebelum' => $saldo_sebelum,
            'saldo_sesudah' => $saldo_sesudah,
            'keterangan' => 'Final approval Kapus Induk',
            'created_by' => $id_kapus
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            show_error("Gagal memproses persetujuan cuti");
        }


       
    }



    
    public function ajax_cancel_cuti()
    {
        //$id_pegawai = $this->session->userdata('id_pegawai');
        $input = json_decode(file_get_contents("php://input"), true);

        $id_pengajuan  = $input['id_pengajuan'] ? : null;
        // Ambil pengajuan
        $cuti = $this->db
            ->where('id', $id_pengajuan)
            ->get('ts_pengajuan_cuti')
            ->row();

        if (!$cuti) {
            show_error("Data cuti tidak ditemukan");
        }

        if ($cuti->status_akhir == 'disetujui') {
            show_error("Cuti sudah final, tidak bisa dibatalkan");
        }


        $id_pegawai = $cuti->id_pegawai;
        $lama_cuti = $cuti->lama_cuti;
        $hak_tahun = $cuti->tahun_hak_cuti;

        $this->db->trans_start();

        // 1. Update status pengajuan
        $this->db->where('id', $id_pengajuan)
            ->update('ts_pengajuan_cuti', [
                'status_akhir' => 'dibatalkan',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // 2. Matikan semua approval
        $this->db->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('status', 'pending')
            ->update('ts_pengajuan_cuti_approval', [
                'status' => 'cancelled'
            ]);

        // 3. Ambil saldo sebelum
        $rowHak = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('tahun', $hak_tahun  )
            ->get('ts_hak_cuti_pegawai')
            ->row();

        $saldo_sebelum = $rowHak->hak_total - ($rowHak->hak_terpakai + $rowHak->hak_reserved);

        // 4. Lepaskan reserve
        $this->db->set('hak_reserved', 'hak_reserved - ' . $lama_cuti, false);
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $hak_tahun);
        $this->db->update('ts_hak_cuti_pegawai');

        $saldo_sesudah = $saldo_sebelum + $lama_cuti;

        // 5. Log RELEASE
        $this->db->insert('ts_log_mutasi_cuti', [
            'id_pegawai' => $id_pegawai,
            'tahun' => $hak_tahun,
            'id_pengajuan_cuti' => $id_pengajuan,
            'tipe' => 'release',
            'jumlah' => $lama_cuti,
            'saldo_sebelum' => $saldo_sebelum,
            'saldo_sesudah' => $saldo_sesudah,
            'keterangan' => 'Pengajuan dibatalkan oleh admin',
            'created_by' => $id_pegawai
        ]);

        $this->db->trans_complete();

        
        echo json_encode([
            'status' => true,
            'message' => 'Pengajuan cuti berhasil dibatalkan'
        ]);
    }



    function approve($id_cuti)
    {
        //$id_cuti = $this->input->post("id");
        $id_validator_session = $this->session->userdata("id_pegawai");

        $row = $this->db->get_where('tbl_detail_cuti', ['id_cuti' => $id_cuti])->row();
        if (!$row) {
            echo json_encode(['status' => false, 'message' => 'Data cuti tidak ditemukan.']);
            return;
        }   

     

        $alasan_cuti    = $row->alasan_cuti;
        $nip            = $row->nip;
        $lama_cuti      = $row->lama_cuti;
        $list_tgl_cuti  = $row->list_tgl_cuti;
        $pin            = substr($nip, -4);

        if($lama_cuti == 1){
            $tgl_cuti = $list_tgl_cuti;
            $this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti );
        } else{
            $explode     = explode(',', $list_tgl_cuti);
            foreach ($explode as $tgl_cuti) {
                // proses insert absensi cuti
               $this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin, $alasan_cuti );
            }
        }


        $this->db->select('id_validator');
        $pegawai      = $this->db->get_where('mst_pegawai', ['nip' => $nip])->row();
        $id_validator = $pegawai->id_validator;


        //1058 = id pegawai bu muklah
        if($id_validator == 1058){
        
               $data = array(
                        'check_ktu' => 1,
                        'tgl_check_ktu' => date('Y-m-d'),
                        'check_kapuskec' => 1,
                        'tgl_check2' => date('Y-m-d'),
                        'status' => 'APPROVE'
                );

                //langsung approve tanpa harus di approve ktu
                
        }else{

                
            $data = array(
                    'check_kapuskec' => 1,
                    'tgl_check2' => date('Y-m-d'),
                    'status' => 'APPROVE'
            );
            

        }

      
        
            $this->db->where('id', $id_cuti);
            $update = $this->db->update('ts_cuti', $data);


        if ($update) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal menyetujui pengajuan.']);
        }
    }


    // function cancel_cuti()
    // {
    //     $id_cuti = $this->input->post("id");
    //     $id_validator_session = $this->session->userdata("id_pegawai");

    //     $row = $this->db->get_where('ts_log_cuti', ['id_cuti' => $id_cuti])->row();
    //     if (!$row) {
    //         echo json_encode(['status' => false, 'message' => 'Data cuti tidak ditemukan.']);
    //         return;
    //     }   

        
    //     $id_pegawai  = $row->id_pegawai;
    //     $jumlah      = $row->jumlah;

    //     $sql = "SELECT sisa_akhir FROM ts_log_cuti WHERE id_pegawai = $id_pegawai ORDER BY id DESC LIMIT 1";
    //     $query = $this->db->query($sql);
    //     $row = $query->row();
    //     $sisa_cuti = $row ? $row->sisa_akhir : 0;

    //     $sisa_akhir     = $sisa_cuti + $jumlah;

    //      $newData = [
    //         "id_pegawai" => $id_pegawai,
    //         "hak_cuti_tahun" => '2025',
    //         "jns_cuti" => '-',
    //         "jns_transaksi" => 'PEMBATALAN', //karena ini bukan permtongan cuti dari pegawai
    //         "id_cuti" =>  $id_cuti,
    //         "sisa_awal" => $sisa_cuti,
    //         "jumlah" => $jumlah,
    //         "sisa_akhir" => $sisa_akhir,

    //     ];

    //     // //print_array($newData);

    //      $this->db->insert("ts_log_cuti", $newData);



    //     $this->db->where('id', $id_cuti);
    //     $this->db->set('status', 'CANCEL');
    //     $update = $this->db->update('ts_cuti');

    //     $this->db->where('id_cuti', $id_cuti);
    //     $this->db->set('status', 'Batal');
    //     $update = $this->db->update('tbl_detail_cuti');



    //     if ($update) {
    //         echo json_encode(['status' => true]);
    //     } else {
    //         echo json_encode(['status' => false, 'message' => 'Gagal menyetujui pengajuan.']);
    //     }
    // }

    function edit_cuti($id_cuti){
        $data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);  
        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $this->load->view('admin/cuti/form_edit_cuti', $data);
    }

    function ajaxDetailCuti() {
		$id_cuti = $this->input->post('id_cuti');
		$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
		
		$this->load->view('admin/cuti/view_detail_cuti', $data);
	}

    function ajaxEditCuti() {
		$id_cuti = $this->input->post('id_cuti');
		$data['detail_cuti'] = $this->Cuti_model->getDetailCuti($id_cuti);
		$data["master_cuti"] = $this->Master_model->getlistCuti();
         
		$this->load->view('admin/cuti/form_edit_cuti', $data);
	}

    


    function update_cuti($id_cuti ){
        $detail_cuti = $this->Cuti_model->getDetailCuti($id_cuti);

        if(!$detail_cuti){
            show_error('Data cuti tidak ditemukan');
            exit;
        }


        $alasan_cuti  = $this->input->post('alasan_cuti');
        $jns_cuti     = $this->input->post('jns_cuti');
        $tgl_mulai    = $this->input->post('tgl_mulai');
        $tgl_akhir    = $this->input->post('tgl_selesai');
        $hak_cuti = $this->input->post('hak_cuti');  // hak cuti tahun berapa yang dipakai, contoh: 2025
        $id_pengganti = $this->input->post('id_pengganti');
        $id_pegawai = $detail_cuti->id_pegawai;
     
        $detail_pegawai_pengganti  = $this->Pegawai_model->getDetailPegawai($id_pengganti);

        $jamKerja = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);
    

        $tgl_mulai = date('Y-m-d', strtotime($tgl_mulai));
        $tgl_akhir = date('Y-m-d', strtotime($tgl_akhir));

          
        $skipHariLibur = false;

        if ($jns_cuti == 2) {
            // Cuti Bersalin → semua hari dihitung
            $skipHariLibur = false;
        } else {
            // Tahunan, sakit, alasan penting
            if ($jamKerja == 'non_shift') {
                $skipHariLibur = true;   // lewati sabtu, minggu, libur
            } else {
                $skipHariLibur = false;  // shift → semua hari dihitung
            }
        }
    echo $tgl_mulai.' - '.$tgl_akhir.' skip libur? '.($skipHariLibur ? 'ya' : 'tidak');
      
        $hariLibur   = $this->acm->getHariLibur($skipHariLibur, $tgl_mulai, $tgl_akhir);

        $getLamaCuti = hitungHariCuti(
            $tgl_mulai,
            $tgl_akhir,
            $skipHariLibur,
            $hariLibur
        );

        $lamaCuti = $getLamaCuti[0];
        $list_hari_cuti = $getLamaCuti[1];


         $this->session->set_userdata('lama_cuti',$lamaCuti);
    
        if ($lamaCuti <= 0) {
            $this->session->set_flashdata('error', 'Tanggal yang dipilih tidak menghasilkan hari cuti');
            redirect('cuti/buat_pengajuan_cuti');
        }

        $sisaCuti = $this->acm->getSisaCutiTahunan($id_pegawai, $hak_cuti);

       // echo ' sisa cuti '.$sisaCuti;

        if($jns_cuti == 1){
            if ($lamaCuti > $sisaCuti) {
                $this->session->set_flashdata(
                    'error',
                    "Sisa cuti tahun $hak_cuti tidak mencukupi. Sisa: $sisaCuti hari"
                );
               // redirect('cuti/buat_pengajuan_cuti');
            }
        }
    
    //

        $dataPengajuan = [
                'jenis_cuti'      => $jns_cuti,
                'tahun_hak_cuti'  => $hak_cuti,
                'lama_cuti'       => $lamaCuti,
                'tgl_mulai'       => $tgl_mulai,
                'tgl_selesai'     => $tgl_akhir,
                'id_pengganti'    => $id_pengganti,
                'alasan_cuti'     => $alasan_cuti,
            ];

        $this->db->where('id', $id_cuti);
        $this->db->update('ts_pengajuan_cuti', $dataPengajuan);


        // cek table ts_log_mutasi_cuti yg id_pengajuan_cuti = $id_cuti, jika ada update, jika tidak ada insert
        $logMutasi = $this->db->get_where('ts_log_mutasi_cuti', ['id_pengajuan_cuti' => $id_cuti])->row();  

    
        if($jns_cuti == 1){
                    
                if($logMutasi){
                        // update
                        $this->db->where('id', $logMutasi->id)->update('ts_log_mutasi_cuti', [
                            'tahun' => $hak_cuti,
                            'jumlah' => -$lamaCuti,
                            'saldo_sebelum'     => $sisaCuti,
                            'saldo_sesudah'     => $sisaCuti - $lamaCuti,
                            'keterangan' => 'Update pengajuan cuti, lama cuti '.$lamaCuti.' hari'
                        ]);
                    }else{
                        // insert
                        $this->db->insert('ts_log_mutasi_cuti', [
                            'id_pegawai' => $id_pegawai,
                            'tahun' => $hak_cuti,
                            'id_pengajuan_cuti' => $id_cuti,
                            'tipe' => 'update',
                            'jumlah' => $lamaCuti,
                            'saldo_sebelum'     => $sisaCuti,
                            'saldo_sesudah'     => $sisaCuti - $lamaCuti,
                            'keterangan' => 'Update pengajuan cuti, lama cuti '.$lamaCuti.' hari',
                        ]);

                    }     

                    $this->db->set('hak_reserved', 'hak_reserved+'.$lamaCuti, false);
                    $this->db->where('id_pegawai',$id_pegawai);
                    $this->db->where('tahun',$HakCutiTahun);
                    $this->db->update('ts_hak_cuti_pegawai');
        }// close if jenis cuti tahunan


            $this->session->set_flashdata('success', 'Pengajuan cuti berhasil diperbarui');

            redirect('admin/cuti/edit_cuti/'.$id_cuti);
    }








    function sinkron($id_cuti, $id_pegawai){
        $tahun = 2025;



        $sisa_cuti = $this->Cuti_model->getSisaCutiTahun($id_pegawai, $tahun);

        $qry  = $this->db->get_where('tbl_detail_cuti', ['id_cuti' => $id_cuti], 1, 0);
        $log_cuti = $qry->row();
       

        $qry = $this->db->get_where('ts_cuti', ['id_pegawai' => $id_pegawai, 'tgl_dari LIKE' => "$tahun%", 'status !=' => 'CANCEL']);
        $cuti = $qry->result();


        foreach($cuti as $row){
            $id_cuti = $row->id;
            $jns_cuti = $row->jns_cuti;
            $jns_hak_cuti = $row->jns_hak_cuti;
            $hari_cuti = $row->hari_cuti;
            $alasan_cuti = $row->alasan_cuti;

             $cekLogCuti = $this->Cuti_model->cekLogCuti($id_cuti);

            $sisa_awal = 0;

           
                if($jns_hak_cuti==2){
                    $cuti_tahun = 2024;
                }else{
                    $cuti_tahun = 2025;
                }


                 $sisa_awal = $this->Cuti_model->getSisaCutiTahun($id_pegawai, $cuti_tahun);
                 $sisa_akhir = $sisa_awal - $hari_cuti;

                if($jns_cuti==1){
                    $jenis_cuti = 'TAHUNAN';

                  
                }else if($jns_cuti==2){
                    $jenis_cuti = 'CB';
                     $sisa_awal = 0;
                     $sisa_akhir = 0;
                }else if($jns_cuti==3){
                     $jenis_cuti = 'CAP';
                    $sisa_awal = 0;
                     $sisa_akhir = 0;
                }else{
                    $jenis_cuti = 'SAKIT';
                    $sisa_awal = 0;
                     $sisa_akhir = 0;
                }

                if($cekLogCuti){
                    continue;
                }

                
                $newData = [
                    'id_pegawai'=> $id_pegawai,
                    'hak_cuti_tahun'=> $cuti_tahun,
                    'jns_cuti' => $jenis_cuti,
                    'jns_transaksi' => 'PEMAKAIAN',
                    'id_cuti' =>  $id_cuti,
                    'keterangan' => $alasan_cuti,
                    'sisa_awal' => $sisa_awal,
                    'jumlah' => $hari_cuti,
                    'sisa_akhir' => $sisa_akhir,
                    'created_at' => date('Y-m-d H:i:s')
                ];


                //$this->db->insert('ts_log_cuti', $newData);

               print_array($newData);
          
       }

       exit;
       redirect('admin/cuti/pengajuan_cuti');
    }


    function log_cuti_pegawai($id_pegawai){

        

        $data['log_cuti'] = $this->acm->getLogCutiPegawai($id_pegawai);
        $this->load->view('admin/cuti/log_cuti_2025', $data);
    }



    function update_riwayat_cuti(){
        $id_pegawai = $this->input->post('id_pegawai');
        $tahun = 2024;
        $tahun2 = 2025;
        $sisa_tahun_2024 = 0;
        $sisa_tahun_2025 = 12;



        $sql = "SELECT * FROM ts_cuti WHERE id_pegawai = $id_pegawai AND (tgl_dari >= '2025-01-01' AND tgl_sampai <= '2025-12-31') ORDER BY tgl_dari ASC";
        $qry = $this->db->query($sql);
        $row = $qry->result();

    

            $saldo = [];

            foreach ($row as $list) {

                $id_cuti = $list->id;
                $id_pegawai = $list->id_pegawai;
                $jns_cuti = $list->jns_cuti;
                $jns_hak_cuti = $list->jns_hak_cuti;
                $hari_cuti = $list->hari_cuti;
                $status = $list->status;
                $alasan_cuti = $list->alasan_cuti;

                // Skip jika sudah pernah masuk log
                $cek = $this->db->get_where('ts_log_cuti', [
                    'id_cuti' => $id_cuti,
                    'jns_transaksi' => 'CUTI'
                ])->row();
                if ($cek) {
                    continue;
                }

                if ($jns_hak_cuti == 2) {
                    $hak_cuti_tahun = 2024;
                    $saldo_awal_tahun = $sisa_tahun_2024;
                } else {
                    $hak_cuti_tahun = 2025;
                    $saldo_awal_tahun = $sisa_tahun_2025;
                }

                $key = $id_pegawai . '-' . $hak_cuti_tahun;

                if (!isset($saldo[$key])) {
                    $last = $this->db->order_by('id','desc')
                        ->get_where('ts_log_cuti', [
                            'id_pegawai'=>$id_pegawai,
                            'hak_cuti_tahun'=>$hak_cuti_tahun
                        ])->row();

                    $saldo[$key] = $last ? $last->sisa_akhir : $saldo_awal_tahun;
                }

                if ($status == 'APPROVE' && $jns_cuti == 1) {

                    $sisa_awal = $saldo[$key];
                    $sisa_akhir = $sisa_awal - $hari_cuti;

                    $this->db->insert("ts_log_cuti", [
                        "id_pegawai"     => $id_pegawai,
                        "hak_cuti_tahun" => $hak_cuti_tahun,
                        "jns_cuti"       => $jns_cuti,
                        "jns_transaksi"  => "CUTI",
                        "id_cuti"       => $id_cuti,
                        "jumlah"        => $hari_cuti,
                        "sisa_awal"     => $sisa_awal,
                        "sisa_akhir"    => $sisa_akhir,
                        "keterangan"    => $alasan_cuti,
                        "created_at"    => date('Y-m-d H:i:s')
                    ]);

                    $saldo[$key] = $sisa_akhir;
                }

            }


           


        redirect('admin/cuti/log_cuti_pegawai/'.$id_pegawai);

    }


    function create_session_pengajuan_cuti()
    {
        
        $this->session->set_userdata([
            "tgl_mulai" => $this->input->post("tgl_mulai"),
            "tgl_akhir" => $this->input->post("tgl_akhir"),
            'jns_cuti' => $this->input->post("jns_cuti")
        ]);

        $this->load->view('pengajuan_cuti/form_delegasi_tugas');
        redirect('cuti/form_detail_pengajuan_cuti');
    }

    // function edit_cuti($id_pegawai)
    // {
    //     $data["history"] = $this->Cuti_model->getMyHistoryCuti();

    //     $id_pegawai = $this->session->userdata("id_pegawai");
    //     $nip = $this->session->userdata("nip");
    //     $data["data_detail"] = $this->Pegawai_model->getDataDetailPegawai($nip);
    //     $data["pegawai"] = $this->Pegawai_model->getDataEditPegawai( $id_pegawai);

    //     $data["master_cuti"] = $this->Master_model->getlistCuti();
    //     $this->load->view("cuti/my_cuti", $data);
    // }


}