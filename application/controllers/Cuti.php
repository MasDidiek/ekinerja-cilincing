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
    
        $tahun = $this->uri->segment(3) ?: date('Y');
        $id_pegawai = $this->session->userdata("id_pegawai");
        $data["history"] = $this->Cuti_model->getHistoryCuti($id_pegawai, $tahun);

        $nip = $this->session->userdata("nip");
        $data["data_detail"] = $this->Pegawai_model->getDataDetailPegawai($nip);
        $data["pegawai"] = $this->Pegawai_model->getDataEditPegawai( $id_pegawai);

        $data['tahun']          = [2025, 2026];
        $data['rekap_hak_cuti']           = $this->acm->get_rekap_cuti_pegawai_by_id($id_pegawai, $data['tahun']);

        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $this->load->view("cuti/my_cuti", $data);
    }


    //detail cuti pada menu my cuti
    function detail_mycuti(){
        $id_cuti = $this->uri->segment(3);
        $tahun   = $this->uri->segment(4);

        $id_pegawai = $this->session->userdata("id_pegawai");
        $data["cuti"] = $this->Cuti_model->getDetailPengajuanCuti($id_cuti);
        $this->load->view("cuti/detail_mycuti", $data);

       
    }

    //detail pengajuan cuti pada  penganti cuti
    function detail_pengajuan_cuti($id_cuti)
    {
        //$data["detail_cuti"] = $this->Cuti_model->getDetailPengajuanCuti($id_cuti);
        $data["cuti"] = $this->Cuti_model->getDetailPengajuanCuti($id_cuti);
        
        $this->load->view("cuti/detail_pengajuan_cuti", $data);
    }


    public function hitung()
    {
        $start = $this->input->post("tgl_mulai");
        $end = $this->input->post("tgl_akhir");
        $jenis_jam_kerja = $this->input->post("jenis_jam_kerja");

        // Validasi input kosong
        if (empty($start) || empty($end)) {
            echo json_encode([
                "error" => "Tanggal mulai dan akhir harus diisi",
            ]);
            return;
        }


        // Convert ke DateTime
        // try {
        //     $start = new DateTime($start);
        //     $end = new DateTime($end);
        // } catch (Exception $e) {
        //     echo json_encode(["error" => "Format tanggal tidak valid"]);
        //     return;
        // }

        // Cek logika tanggal: akhir >= mulai
        if ($end < $start) {
            echo json_encode([
                "error" => "Tanggal akhir tidak boleh sebelum tanggal mulai",
            ]);
            return;
        }

        // Optional: validasi rentang tanggal (misal max 90 hari dari hari ini)
        $today = new DateTime();
        $maxDate = (clone $today)->modify("+120 days");
        // if ($start < $today->modify("-14 day") || $end > $maxDate) {
        //     echo json_encode([
        //         "error" =>
        //             "Tanggal cuti harus antara kemarin sampai 120 hari ke depan",
        //     ]);
        //     return;
        // }


        $id_pegawai = $this->session->userdata("id_pegawai");
        $detail_pegawai    = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $jns_jam_kerja     = $detail_pegawai->jns_jam_kerja;
        if($jns_jam_kerja=='non_shift'){
           $jenis_jam_kerja = 'N';
        }
        else{
            $jenis_jam_kerja = 'S';
        }

      

     //echo 'jns_jam_kerja '.$jns_jam_kerja;
        // Kalau semua valid, lanjut hitung hari kerja...
        $jumlah = $this->Cuti_model->hitungHariKerja( $start,  $end,  $jenis_jam_kerja );

        echo json_encode(["jumlah_hari" => $jumlah]);
    }

    // Fungsi bantu cek format tanggal
    private function isValidDate($date)
    {
        $d = DateTime::createFromFormat("Y-m-d", $date);
        return $d && $d->format("Y-m-d") === $date;
    }

    // function create_session_pengajuan_cuti()
    // {
        
    //     $this->session->set_userdata([
    //         "tgl_mulai" => $this->input->post("tgl_mulai"),
    //         "tgl_akhir" => $this->input->post("tgl_akhir"),
    //         'jns_cuti' => $this->input->post("jns_cuti")
    //     ]);

    //     $this->load->view('pengajuan_cuti/form_delegasi_tugas');
    //     redirect('cuti/form_detail_pengajuan_cuti');
    // }


    // function form_detail_pengajuan_cuti()
    // {

    //     $id_pegawai                = $this->session->userdata("id_pegawai");
    //     $pegawai                   = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
    //     $id_jabatan                = $pegawai[0]->id_jabatan;

    //     $data['pegawai_pengganti'] = $this->Pegawai_model->cari_pegawai_pengganti_cuti($id_jabatan, $id_pegawai);


    //     $this->load->view('cuti/form_detail_pengajuan_cuti', $data);

    // }


   function simpan_pengajuan_cuti()
    {
        $id_pegawai   = $this->session->userdata("id_pegawai");
      
        $jns_cuti        = $this->input->post("jns_cuti");
        $hak_cuti        = $HakCutiTahun = $this->input->post("hak_cuti");
        $tgl_cuti_dari   = $this->input->post("tgl_mulai");
        $tgl_cuti_sampai = $this->input->post("tgl_akhir");
        $id_pengganti    = $this->input->post("id_pengganti");
        $no_tlp          = $this->input->post("no_tlp");
        $alasan_cuti     = $this->input->post("alasan_cuti");
        $alamat          = $this->input->post("alamat");
        $delegasi_tugas          = $this->input->post("delegasi_tugas");

        //($this->input->post());

        $this->session->set_userdata($this->input->post());

        // $explodeHakCuti = explode("-", $hak_cuti);
        // $HakCutiTahun = $explodeHakCuti[0];
        // $sisaCuti     = $explodeHakCuti[1];

        $tgl_cuti_dari = format_db($tgl_cuti_dari);
        $tgl_cuti_sampai = format_db($tgl_cuti_sampai);

       
        $jamKerja = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);
       
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

      
        $hariLibur   = $this->acm->getHariLibur($skipHariLibur, $tgl_cuti_dari,$tgl_cuti_sampai);

        $getLamaCuti = hitungHariCuti(
            $tgl_cuti_dari,
            $tgl_cuti_sampai,
            $skipHariLibur,
            $hariLibur
        );

        $lamaCuti = $getLamaCuti[0];
        $list_hari_cuti = $getLamaCuti[1];
        //print_array($lamaCuti);

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


            // cek tgl mulai cuti, ga boleh kurang dari hari ini
        }
    
     
        //1. insert ke table ts_pengajuan_cuti
            $dataPengajuan = [
                    'id_pegawai'      => $id_pegawai,
                    'jenis_cuti'      => $jns_cuti,
                    'tahun_hak_cuti'  => $HakCutiTahun,
                    'lama_cuti'       => $lamaCuti,
                    'tgl_pengajuan'   => date('Y-m-d'),
                    'tgl_mulai'       => $tgl_cuti_dari,
                    'tgl_selesai'     => $tgl_cuti_sampai,
                    'id_pengganti'    => $id_pengganti,
                    'alamat_cuti'     => $alamat,
                    'no_telp'         => $no_tlp,
                    'alasan_cuti'     => $alasan_cuti,
                    'delegasi_tugas'  => $delegasi_tugas,
                    'status_akhir'         => 'draft'
                ];

            $this->db->insert('ts_pengajuan_cuti', $dataPengajuan);
            $id_pengajuan = $this->db->insert_id();

          
        //2. insert ke table ts_pengajuan_cuti_detail

            
            $listTanggal = $this->acm->getListTanggalCuti($tgl_cuti_dari, $tgl_cuti_sampai, $jns_cuti, $jamKerja, $hariLibur);

            if ($jns_cuti != 2) {
                foreach($listTanggal as $tgl){
                    $this->db->insert('ts_pengajuan_cuti_detail', [
                        'id_pengajuan_cuti' => $id_pengajuan,
                        'tgl_cuti' => $tgl,
                        'tahun_hak_cuti' => $HakCutiTahun
                    ]);
                }
            }
        //3. insert ts_pengajuan_cuti_detail

         $approver = $this->acm->getApproverCuti($id_pegawai, $id_pengganti);

        //  print_array($approver);
        //  exit;
         $workflow = [
                ['level'=>1,'role'=>'pengganti','id'=>$approver['pengganti']],
                ['level'=>2,'role'=>'kapustu','id'=>$approver['kapustu']],
                ['level'=>3,'role'=>'ktu','id'=>$approver['ktu']],
                ['level'=>4,'role'=>'kapus','id'=>$approver['kapus']],
            ];

            foreach($workflow as $w){
                $this->db->insert('ts_pengajuan_cuti_approval',[
                    'id_pengajuan_cuti' => $id_pengajuan,
                    'level_approval'            => $w['level'],
                    'role_approval'             => $w['role'],
                    'id_pegawai_approval'      => $w['id'],
                    'status'           => ($w['level']==1 ? 'pending' : 'waiting')
                ]);
            }
        //4. insert ke table ts_log_mutasi_cuti

        if($jns_cuti == 1){
            $this->db->insert('ts_log_mutasi_cuti', [
                    'id_pegawai'         => $id_pegawai,
                    'tahun'             => $HakCutiTahun,
                    'id_pengajuan_cuti' => $id_pengajuan,
                    'tipe'              => 'reserve',
                    'jumlah'            => $lamaCuti,
                    'saldo_sebelum'     => $sisaCuti,
                    'saldo_sesudah'     => $sisaCuti - $lamaCuti,
                    'keterangan'        => 'Reserve pengajuan cuti'
                ]);
            //5. insert ke table ts_hak_cuti_pegawai
            
                $this->db->set('hak_reserved', 'hak_reserved+'.$lamaCuti, false);
                $this->db->where('id_pegawai',$id_pegawai);
                $this->db->where('tahun',$HakCutiTahun);
                $this->db->update('ts_hak_cuti_pegawai');
        }   

         $this->session->set_flashdata('success', 'Pengajuan cuti berhasil disimpan');

         if($jns_cuti > 1){
            redirect('cuti/upload_file_pendukung/'.$id_pengajuan);
         }

         redirect('cuti/detail_mycuti/'.$id_pengajuan);

    }



    //setujui cuti diakses oleh pengganti cuti
    public function ajax_setujui()
    {

        $input = json_decode(file_get_contents("php://input"), true);

        $id_pengajuan = $input['id_pengajuan'] ?: null;
        $role_approval = $input['role_approval'] ?: 'pengganti';
        $id_pegawai = $this->session->userdata("id_pegawai");

        if (!$id_pengajuan) {
            echo json_encode([
                'status' => false,
                'message' => 'ID pengajuan tidak valid'
            ]);
            return;
        }

        // Ambil row approval pengganti
        $approval = $this->db
            ->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('role_approval', 'pengganti')
            ->where('id_pegawai_approval', $id_pegawai)
            ->where('status', 'pending')
            ->get('ts_pengajuan_cuti_approval')
            ->row();

        if (!$approval) {
            show_error("Anda tidak berhak atau approval sudah diproses");
        }

        $this->db->trans_start();

        // 1. Approve pengganti
        $this->db->where('id', $approval->id)
            ->update('ts_pengajuan_cuti_approval', [
                'status' => 'approved',
                'approved_at' => date('Y-m-d H:i:s')
            ]);

        // 2. Aktifkan Kapustu
        $this->db->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('role_approval', 'kapustu')
            ->update('ts_pengajuan_cuti_approval', [
                'status' => 'pending'
            ]);

        // 3. Update status di pengajuan
        $this->db->where('id', $id_pengajuan)
            ->update('ts_pengajuan_cuti', [
                'status_akhir' => 'proses'
            ]);

        $this->db->trans_complete();

        echo json_encode([
            'status' => true,
            'message' => 'Permohonan pengganti cuti berhasil disetujui'
        ]);
    }


    function upload_file_pendukung($id_pengajuan=0){
        
        $data["cuti"] = $this->acm->getDetailPengajuanCuti($id_pengajuan);
        $this->load->view('cuti/upload_file_pendukung', $data);
    }


    function summary_pengajuan_cuti($id_cuti){

        
        //  $data["data_cuti"] = $this->Cuti_model->getDetailCuti($id_cuti);
        //  $data_detail_cuti = $this->Cuti_model->getDataDetailCuti($id_cuti);

        //  if(count($data_detail_cuti)==0){
           
        //     redirect('cuti/insertDataDetailCuti/'.$id_cuti);
         
        //  }


        // $data["data_detail_cuti"] = $data_detail_cuti;
          $data["cuti"] = $this->acm->getDetailPengajuanCuti($id_cuti);

        $this->load->view('cuti/summary_pengajuan_cuti', $data);
    }



    public function ajax_upload() {
        $config['upload_path'] = './uploads/dokumen_cuti/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);

        $id_cuti = $this->input->post('id_pengajuan');

        if (!$this->upload->do_upload('file')) {
            echo json_encode([
                'status' => 'error',
                'message' => strip_tags($this->upload->display_errors())
            ]);
        } else {
            $data = $this->upload->data();
            $filename = $data['file_name'];
            $file = $this->upload->data();
            // Simpan ke tabel ts_file_cuti
           
            $this->db->insert('ts_cuti_bukti', [
                'id_pengajuan' => $id_cuti,
                'file_name' => $file['file_name'],
                'file_path' => 'uploads/dokumen_cuti/'.$file['file_name'],
                'file_type' => $file['file_type']
            ]);

            $bukti_id = $this->db->insert_id();

            // return HTML preview
            echo '
            <div class="col-md-3" id="bukti_'.$bukti_id.'">
                <div class="card">
                    <img src="'.base_url('uploads/dokumen_cuti/'.$file['file_name']).'" class="img-fluid">
                    <div class="card-body text-center">
                        <button onclick="hapusBukti('.$bukti_id.')" class="btn btn-sm btn-danger">Hapus</button>
                    </div>
                </div>
            </div>';
        }
    }




    public function upload_ajax()
    {
        $id = $this->input->post('id_pengajuan');

        $config['upload_path']   = './uploads/dokumen_cuti/';
        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        $config['max_size'] = 2048;


//         if (!is_dir(FCPATH.'uploads/cuti')) {
//     echo "Folder tidak ada";
//     exit;
// }

// if (!is_writable(FCPATH.'uploads/cuti')) {
//     echo "Folder tidak bisa ditulis";
//     exit;
// }



        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            echo '<div class="text-danger">'.$this->upload->display_errors().'</div>';
            return;
        }

        $file = $this->upload->data();

        $this->db->insert('ts_cuti_bukti', [
            'id_pengajuan' => $id,
            'file_name' => $file['file_name'],
            'file_path' => 'uploads/dokumen_cuti/'.$file['file_name'],
            'file_type' => $file['file_type']
        ]);

        $bukti_id = $this->db->insert_id();

        // return HTML preview
        echo '
        <div class="col-md-3" id="bukti_'.$bukti_id.'">
            <div class="card">
                <img src="'.base_url('uploads/dokumen_cuti/'.$file['file_name']).'" class="img-fluid">
                <div class="card-body text-center">
                    <button onclick="hapusBukti('.$bukti_id.')" class="btn btn-sm btn-danger">Hapus</button>
                </div>
            </div>
        </div>';
    }

   
    public function delete_bukti()
    {
        $id = $this->input->post('id');

        $bukti = $this->db->where('id',$id)->get('ts_cuti_bukti')->row();

        if ($bukti) {
            unlink('./'.$bukti->file_path);
            $this->db->where('id',$id)->delete('ts_cuti_bukti');
        }
    }

    public function cancel()
    {
        $id_pegawai = $this->session->userdata('id_pegawai');
        $input = json_decode(file_get_contents("php://input"), true);

        $id_pengajuan  = $input['id_pengajuan'] ? : null;
        // Ambil pengajuan
        $cuti = $this->db
            ->where('id', $id_pengajuan)
            ->where('id_pegawai', $id_pegawai)
            ->get('ts_pengajuan_cuti')
            ->row();

        if (!$cuti) {
            show_error("Data cuti tidak ditemukan");
        }

        if ($cuti->status_akhir == 'disetujui') {
            show_error("Cuti sudah final, tidak bisa dibatalkan");
        }

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
            ->where('tahun', $hak_tahun)
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
            'keterangan' => 'Pengajuan dibatalkan oleh pegawai',
            'created_by' => $id_pegawai
        ]);

        $this->db->trans_complete();

        
        echo json_encode([
            'status' => true,
            'message' => 'Pengajuan cuti berhasil dibatalkan'
        ]);
    }

    
    function my_cuti()
    {
        $id_pegawai = $this->session->userdata("id_pegawai");
        $nip = $this->session->userdata("nip");
        $data["cutiPegawai"] = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $data["penggantian_cuti"] = $this->Cuti_model->getPermohonanPengganti($id_pegawai);

        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $id_jabatan = $pegawai[0]->id_jabatan;

        $data[ "listPegawaiPengganti" ] = $this->Cuti_model->getListPegawaiPenggantiCuti( $id_pegawai, $id_jabatan );

        $this->load->view("cuti/my_cuti", $data);
    }

    function pengajuan_cuti()
    {
        $id_validator = $this->session->userdata("id_pegawai");
        $thn_anggaran = 2024;
        $data["list_pegawai"] = $this->Pegawai_model->getListPegawaiByValidator(
            $id_validator,
            $thn_anggaran
        );

        $this->load->view("cuti/pengajuan_cuti", $data);
    }




    function acc_pengganti_cuti(){

        $id_cuti		= $this->input->post('id');
    
        $data = array(
            'cek_pengganti' => 1, // bernilai true bearati sudah di setujui
            'tgl_cek' => date('Y-m-d'), //tanggal pengganti acc cuti sebagai pengganti
            'status' => 'PEND1' // pindah ke satatus menungga acca kapustu
        );

        $this->db->where('id', $id_cuti);
        $updated = $this->db->update('ts_cuti', $data);


        if ($updated) {

            $data_update = array(
                'acc_pengganti' => date('Y-m-d H:i:s'),
                'status' =>  'Proses',
                'keterangan_status' =>  'Menunggu acc Kapustu/Kasatpel'
            );

            $this->db->where("id_cuti", $id_cuti);
            $this->db->update("tbl_detail_cuti", $data_update);
            
           echo json_encode(['status' => 'success']);
        } else {
           echo json_encode(['status' => 'error']);
        }

    }
    
public function ajax_delete() {
    $filename = $this->input->post('filename');
    $id_cuti = $this->input->post('id_cuti'); // jika mau lebih spesifik

    $file_path = FCPATH . 'uploads/dokumen_cuti/' . $filename;

    // Cek apakah file ada
    if (file_exists($file_path)) {
        // Hapus file dari server
        unlink($file_path);

        // Hapus dari database
        $this->db->where('file_name', $filename);
        if ($id_cuti) {
            $this->db->where('id_cuti', $id_cuti);
        }
        $this->db->delete('ts_file_cuti');

        echo json_encode([
            'status' => 'success',
            'message' => 'File berhasil dihapus.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'File tidak ditemukan di server.'
        ]);
    }
}



    function delete_pengajuan_cuti($id_cuti)
    {
        $this->db->where("id", $id_cuti);
        $this->db->delete("ts_cuti");

        $this->session->set_flashdata("message", "Cuti berhasil dihapus");
        redirect("cuti/my_cuti");
    }






    function edit_cuti($id_cuti)
    {
        $id_pegawai = $this->session->userdata("id_pegawai");
        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $id_jabatan = $pegawai->id_jabatan;
    
        $data[
        "listPegawaiPengganti"
        ] = $this->Cuti_model->getListPegawaiPenggantiCuti(
        $id_pegawai,
        $id_jabatan
        );

        $data['tahun']          = [2025, 2026];
        $data['cuti']           = $this->acm->get_rekap_cuti_pegawai_by_id($id_pegawai, $data['tahun']);
        $data["detail_cuti"] = $this->Cuti_model->getDetailCuti($id_cuti);
        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $this->load->view("pengajuan_cuti/form_edit_cuti", $data);
    }

    function update_pengajuan_cuti($id_cuti) {
        $tgl_cuti_dari      = format_db($this->input->post('tgl_mulai'));
        $tgl_cuti_sampai     = format_db($this->input->post('tgl_selesai'));
        $alasan_cuti    = $this->input->post('alasan_cuti');
        $id_pengganti   = $this->input->post('id_pengganti');
        $jns_cuti       = $this->input->post('jns_cuti');
        $hak_cuti       = $this->input->post('hak_cuti');
        $no_tlp         = $this->input->post('no_tlp');

        //print_array($this->input->post());


        // echo $tgl_cuti_dari.' s/d '.$tgl_cuti_sampai;
        //  echo '<br>';
        //  echo 'jenis cuti '.$jns_cuti;
        //  echo '<br>';
        //  echo 'hak cuti '.$hak_cuti;
        //  echo '<br>';

        $detail_cuti = $this->Cuti_model->getDetailCuti($id_cuti);
        $id_pegawai = $detail_cuti->id_pegawai;


        $jamKerja = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);


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


        $hariLibur   = $this->acm->getHariLibur($skipHariLibur, $tgl_cuti_dari,$tgl_cuti_sampai);
        $getLamaCuti = hitungHariCuti(  $tgl_cuti_dari, $tgl_cuti_sampai, $skipHariLibur, $hariLibur );
        //setelah cek hari libur, cek hari sabtu minggu, baru dapat lama cuti yang sebenarnya
        $lamaCuti       = $getLamaCuti[0];
        $list_hari_cuti = $getLamaCuti[1];

        $listTglCuti = [];
        $hari_cuti   = 0;
        if($skipHariLibur){
            for ($i=0; $i < count($list_hari_cuti); $i++) { 
                $tgl = $list_hari_cuti[$i];
                $dayName = date('D', strtotime($tgl));
                if ($dayName !== 'Sat' && $dayName !== 'Sun') {
                    // Jika hari libur jatuh pada hari Sabtu atau Minggu, tambahkan 1 hari ke lama cuti
                    $hari_cuti += 1;
                    $listTglCuti[] = date('Y-m-d', strtotime($tgl . ' +1 day'));
                }
            }
        }else{
            $hari_cuti = $lamaCuti;
            $listTglCuti = $list_hari_cuti;
        }


        $log_mutasi = $this->db
            ->where('id_pengajuan_cuti', $id_cuti)
            ->get('ts_log_mutasi_cuti')
            ->row();

            $id_log          = $log_mutasi->id;
            $saldo_sebelum   = $log_mutasi->saldo_sebelum;


            $dataLog = [
                'jumlah' => $hari_cuti,
                'saldo_sebelum' => $saldo_sebelum , // kembalikan saldo sebelum dengan menambahkan jumlah log sebelumnya
                'saldo_sesudah' => $saldo_sebelum - $hari_cuti, // hitung ulang saldo sesudah dengan mengurangi jumlah 
                ];

              
            $this->db->where('id', $id_log);
            $this->db->update('ts_log_mutasi_cuti', $dataLog);

            //hapus dulu, baru buat lagi
           
            $this->db->where('id_pengajuan_cuti', $id_cuti);
            $this->db->delete('ts_pengajuan_cuti_detail');

             if ($jns_cuti != 2) {

                foreach($listTglCuti as $tgl){
                    $this->db->insert('ts_pengajuan_cuti_detail', [
                        'id_pengajuan_cuti' => $id_cuti,
                        'tgl_cuti' => $tgl,
                        'tahun_hak_cuti' => $hak_cuti
                    ]);
                }
            }


        $data = [
            'tgl_mulai' => $tgl_cuti_dari,
            'tgl_selesai' => $tgl_cuti_sampai,
            'alasan_cuti' => $alasan_cuti,
            'alamat_cuti' => $this->input->post('alamat'),
            'id_pengganti' => $id_pengganti,
            'jenis_cuti' => $jns_cuti,
            'tahun_hak_cuti' => $hak_cuti,
            'no_telp' => $no_tlp,
            'lama_cuti' => $hari_cuti,
            'status_akhir' => 'draft'
        ];


        $this->db->where('id', $id_cuti);
        $this->db->update('ts_pengajuan_cuti', $data);

        $this->session->set_flashdata('success', 'Pengajuan cuti berhasil diubah');
        redirect('cuti/edit_cuti/'.$id_cuti);
    }

    function edit_tanggal_cuti($id_cuti)
    {
        $id_pegawai = $this->session->userdata("id_pegawai");
        $now = date("Y-m-d");
        $date_from = $this->input->post("date_from");
        $date_to = $this->input->post("date_to");

        $start_date = format_db($date_from);
        $end_date = format_db($date_to);

        $this->session->set_userdata($this->input->post());

        $selisihhari = datediff("d", $start_date, $end_date);
        //selesih hari jika cuti yang  hari dianggap 0 hari, maka dari itu harus ditambah 1 hari
        $selisihhari = $selisihhari + 1;

        if ($selisihhari < 1) {
            //salah memasukkan tanggal (tanggal akhir lebih kecil dari pada tanggal dari)
            $pesan = createMessageInfo(
                "Tanggal tidak valid, Periksa kembali tanggal cuti ",
                "danger"
            );
            $this->session->set_flashdata("message", $pesan);

            redirect("cuti/edit_cuti/" . $id_cuti);
        }

        $jns_cuti = $this->input->post("jns_cuti");
        $jns_hak_cuti = $this->input->post("jns_hak_cuti");

        $jamKerja = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);

        if ($jns_cuti == 1) {
            //klo cuti tahunan
            /* proses cek hari cuti yang diajukan..   */

            if ($start_date < $now) {
                $diff_date = dateDifference($start_date, $now);
                //klo tanggal cuti lebih kecil dari tanggal hari ini//cek apakah sudah melebihi 14 hari
                if ($diff_date > 30) {
                    //klo mengajukan cuti tanggal sudah terlewat, maksimal 14 hari dari hari ini
                    $pesan = createMessageInfo(
                        "Pengajuan cuti untuk tanggal yang sudah lewat, <strong>maksimal 14 hari</strong> dari hari ini!",
                        "danger"
                    );
                    $this->session->set_flashdata("message", $pesan);

                    redirect("cuti/edit_cuti/" . $id_cuti);
                }
            }

            $cekSisaCuti = $this->Cuti_model->getSisaCuti(
                $id_pegawai,
                $jns_hak_cuti
            );
            if ($cekSisaCuti == 0) {
                $pesan = createMessageInfo(
                    "Sisa cuti tidak mencukupi",
                    "danger"
                );
                $this->session->set_flashdata("message", $pesan);

                redirect("cuti/edit_cuti/" . $id_cuti);
            }

            if ($jamKerja == "non_shift") {
                if ($selisihhari > 1) {
                    //klo cuti lebih dari 1 hari
                    $dataCuti = $this->Cuti_model->getHariCuti(
                        $selisihhari,
                        $start_date
                    );
                    $hariCuti = $dataCuti[0];
                    $listhariCuti = $dataCuti[1];
                } else {
                    $hariCuti = 1;
                    $listhariCuti = [$start_date];
                }

                if ($cekSisaCuti < $hariCuti) {
                    $pesan = createMessageInfo(
                        "Sisa cuti tidak mencukupi",
                        "danger"
                    );
                    $this->session->set_flashdata("message", $pesan);

                    redirect("cuti/edit_cuti/" . $id_cuti);
                } else {
                    $pesan = createMessageInfo(
                        "Cuti berhasil diupdate",
                        "success"
                    );
                    $this->session->set_flashdata("message", $pesan);

                    $this->Cuti_model->updateDataCuti(
                        $id_cuti,
                        $jns_cuti,
                        $jns_hak_cuti,
                        $start_date,
                        $end_date,
                        $hariCuti
                    );

                    $this->db->where("id_cuti", $id_cuti);
                    $this->db->delete("ts_cuti_detail");

                    for ($i = 0; $i < count($listhariCuti); $i++) {
                        $tanggal = $listhariCuti[$i];
                        $this->Cuti_model->insertDataDetailCuti(
                            $id_cuti,
                            $id_pegawai,
                            $tanggal
                        );
                    }

                    redirect("cuti/edit_cuti/" . $id_cuti);
                }
            } else {
                #$hariCuti =  datediff('d', $start_date, $end_date);

                $arrayHariCuti = [];
                $datetime1 = date_create($start_date);
                $datetime2 = date_create($end_date);
                // Calculates the difference between DateTime objects
                $interval = date_diff($datetime1, $datetime2);
                $hariCuti = $interval->format("%a") + 1;
                $newDate = $start_date;
                for ($a = 0; $a < $hariCuti; $a++) {
                    $arrayHariCuti[] = $newDate;
                    $newDate = addDaysToDate($newDate, 1);
                }

                $pesan = createMessageInfo("Cuti berhasil diupdate", "success");
                $this->session->set_flashdata("message", $pesan);
                $this->Cuti_model->updateDataCuti(
                    $id_cuti,
                    $jns_cuti,
                    $jns_hak_cuti,
                    $start_date,
                    $end_date,
                    $hariCuti
                );

                $this->db->where("id_cuti", $id_cuti);
                $this->db->delete("ts_cuti_detail");

                for ($i = 0; $i < count($arrayHariCuti); $i++) {
                    $tanggal = $arrayHariCuti[$i];
                    $this->Cuti_model->insertDataDetailCuti(
                        $id_cuti,
                        $id_pegawai,
                        $tanggal
                    );
                }

                redirect("cuti/edit_cuti/" . $id_cuti);
            }
        } else {
            //jenis cuti yang lain, cuti bersalin, cuti alasan penting tidak perlu cek sisa cuti
            $hariCuti = datediff("d", $start_date, $end_date);
            $pesan = createMessageInfo("Cuti berhasil diupdate", "success");
            $this->session->set_flashdata("message", $pesan);

            $this->Cuti_model->updateDataCuti(
                $id_cuti,
                $jns_cuti,
                $jns_hak_cuti,
                $start_date,
                $end_date,
                $hariCuti
            );
            redirect("cuti/edit_cuti/" . $id_cuti);
        }
    }


    function check_date()
    {
        $id_pegawai = $this->session->userdata("id_pegawai");
        $tgl_cuti = $this->input->post("tgl_cuti");
        $jns_cuti = $this->input->post("jns_cuti");
        $jns_hak_cuti = $this->input->post("jns_hak_cuti");
        $id_pengganti = $this->input->post("id_pengganti");

        $tgl_cuti_dari = $this->input->post("tgl_cuti_dari");
        $tgl_cuti_sampai = $this->input->post("tgl_cuti_sampai");

        $this->session->set_userdata($this->input->post());

        if ($tgl_cuti != "") {
            $explode = explode("-", $tgl_cuti);
            $dateFrom = $explode[0];
            $dateTo = @$explode[1];
            //klo cuti cuma sehari
            if ($dateTo == "") {
                $dateTo = $dateFrom;
            }
        } else {
            $dateFrom = $tgl_cuti_dari;
            $dateTo = $tgl_cuti_sampai;

            $tgl_cuti = $dateFrom . " - " . $dateTo;

            $this->session->set_userdata("tgl_cuti", $tgl_cuti);
        }

        $jamKerja = $this->Pegawai_model->checkJenisJamKerja($id_pegawai);
        $dateFrom = str_replace("/", "-", $dateFrom);
        $dateTo = str_replace("/", "-", $dateTo);

        $start_date = format_db($dateFrom);
        $end_date = format_db($dateTo);
        $today = date("Y-m-d");

        $selisihhari = datediff("d", $start_date, $today);
        $this->session->set_userdata("jns_hak_cuti", 4);

        $datetime = new DateTime($today);
        $datetime->sub(new DateInterval("P14D"));
        $maksCutiTerakhir = $datetime->format("d F Y");

        if ($id_pengganti == "") {
            $this->session->set_flashdata("status", "error");
            $this->session->set_flashdata(
                "message",
                '<div class="alert alert-danger">Gagal! Pengganti cuti belum dipilih</div>'
            );
            redirect("cuti/buat_pengajuan_cuti");
        } else {
            if ($selisihhari > 15) {
                $this->session->set_flashdata(
                    "message",
                    '<div class="alert alert-danger">Gagal! maksimal 14 hari sebelum tanggal hari ini, tanggal terakhir yang diizinkan adalah tanggal <strong>' .
                        $maksCutiTerakhir .
                        " </strong></div>"
                );
                redirect("cuti/buat_pengajuan_cuti");
            } else {
                if ($jns_cuti == 2 || $jns_cuti == 6) {
                    //cuti bersalin
                    $arrayHariCuti = [];
                    $datetime1 = date_create($start_date);
                    $datetime2 = date_create($end_date);
                    // Calculates the difference between DateTime objects
                    $interval = date_diff($datetime1, $datetime2);
                    $hariCuti = $interval->format("%a") + 1;
                    $newDate = $start_date;

                    for ($a = 0; $a < $hariCuti; $a++) {
                        $arrayHariCuti[] = $newDate;
                        $newDate = addDaysToDate($newDate, 1);
                    }

                    $this->session->set_userdata("jml_hari_cuti", $hariCuti);
                    $this->session->set_userdata(
                        "list_hari_cuti",
                        $arrayHariCuti
                    );
                } else {
                    //tahunan, CAP, sakit
                    $sisaCuti = $this->Cuti_model->getSisaCuti($id_pegawai, 2);
                    $diff = datediff("d", $start_date, $end_date) + 1;

                    if ($jamKerja == "non_shift") {
                        $dataCuti = $this->Cuti_model->getHariCuti(
                            $diff,
                            $start_date
                        );
                        $hariCuti = $dataCuti[0]; //jumlah hari cuti
                        $arrayHariCuti = $dataCuti[1]; //list hari cuti

                        $this->session->set_userdata(
                            "jml_hari_cuti",
                            $hariCuti
                        );
                        $this->session->set_userdata(
                            "list_hari_cuti",
                            $arrayHariCuti
                        );

                        if ($jns_cuti == 1) {
                            //cek tanggal cuti yang tidak boleh
                            $cekAllowedCuti = $this->Cuti_model->cekTgCutiBersama(
                                $diff,
                                $start_date
                            );

                            if ($cekAllowedCuti == false) {
                                $this->session->set_flashdata(
                                    "message",
                                    '<div class="alert alert-danger"><span class="font-bold">Gagal !! </span>
                                            Tidak diizinkan untuk menggunakan cuti ditanggal sebelum dan setelah cuti bersama (Jumat 09 Mei 2025, Rabu 14 Mei 2025, Rabu 28 Mei 2025, Senin 02 Juni 2025)</div>'
                                );
                                redirect("cuti/buat_pengajuan_cuti");
                            }

                            //cuti tahunan, harus cek sisa cuti
                            if ($sisaCuti >= $hariCuti) {
                                //klo sisa cuti tahun lalu masih mencukupi
                                $this->session->set_userdata("jns_hak_cuti", 2);
                                redirect("cuti/form_delegasi_tugas");
                            } else {
                                //klo sisa cuti tahun lalu tidak mencukupi, cek ke hak cuti tahun sekarang
                                $sisaCuti = $this->Cuti_model->getSisaCuti(
                                    $id_pegawai,
                                    4
                                );

                                if ($sisaCuti < $hariCuti) {
                                    //klo sisa cuti tahun ini tidak mencukup juga, maka kembalikan ke halaman pengajuan cuti
                                    $this->session->set_flashdata(
                                        "message",
                                        '<div class="alert alert-danger">Gagal! Sisa cuti tidak mencukupi</div>'
                                    );
                                    redirect("cuti/buat_pengajuan_cuti");
                                } else {
                                    $this->session->set_userdata(
                                        "jns_hak_cuti",
                                        4
                                    );
                                    // sisa cuti mencukupi
                                    redirect("cuti/form_delegasi_tugas");
                                }
                            }
                        } else {
                            ///selain itu ga perlu karna ga motong cuti tahunan
                            $this->session->set_userdata("jns_hak_cuti", 0);
                            // sisa cuti mencukupi
                            redirect("cuti/form_delegasi_tugas");
                        }
                    } else {
                        //untuk yang shift kerjanya shift

                        $arrayHariCuti = [];
                        $datetime1 = date_create($start_date);
                        $datetime2 = date_create($end_date);
                        // Calculates the difference between DateTime objects
                        $interval = date_diff($datetime1, $datetime2);
                        $hariCuti = $interval->format("%a") + 1;
                        $newDate = $start_date;

                        for ($a = 0; $a < $hariCuti; $a++) {
                            $arrayHariCuti[] = $newDate;
                            $newDate = addDaysToDate($newDate, 1);
                        }
                        $this->session->set_userdata(
                            "jml_hari_cuti",
                            $hariCuti
                        );
                        $this->session->set_userdata(
                            "list_hari_cuti",
                            $arrayHariCuti
                        );

                        if ($jns_cuti == 1) {
                            //cuti tahunan, harus cek sisa cuti
                            if ($sisaCuti >= $hariCuti) {
                                //klo sisa cuti tahun lalu masih mencukupi
                                redirect("cuti/form_delegasi_tugas");
                            } else {
                                //klo sisa cuti tahun lalu tidak mencukupi, cek ke hak cuti tahun sekarang
                                $sisaCuti = $this->Cuti_model->getSisaCuti(
                                    $id_pegawai,
                                    4
                                );

                                if ($sisaCuti < $hariCuti) {
                                    //klo sisa cuti tahun ini tidak mencukup juga, maka kembalikan ke halaman pengajuan cuti
                                    $this->session->set_flashdata(
                                        "message",
                                        '<div class="alert alert-danger">Gagal! Sisa cuti tidak mencukupi</div>'
                                    );
                                    redirect("cuti/buat_pengajuan_cuti");
                                } else {
                                    // sisa cuti mencukupi
                                    redirect("cuti/form_delegasi_tugas");
                                }
                            }
                        } ///selain itu ga perlu karna ga motong cuti tahunan
                    }
                } //close jenis cuti
            } //close if selisihhari
        } //close pengganti cuti
    }

    function approve_pengganti_cuti($status, $id_cuti)
    {
        $data = [
            "cek_pengganti" => 1,
            "tgl_cek" => date("Y-m-d"),
            "status" => $status,
        ];

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $data);
        $this->session->set_flashdata(
            "message",
            "Anda telah menyetujui sebagai pengganti cuti"
        );

        redirect("cuti/my_cuti");
    }

    function approve_cuti_kapus($status, $id_cuti)
    {
        $data = [
            "check_kapuskel" => 1,
            "tgl_check" => date("Y-m-d"),
            "status" => $status,
        ];

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $data);
        $this->session->set_flashdata(
            "message",
            "Pengajuan cuti telah disetuju"
        );

        redirect("cuti/detail_cuti/" . $id_cuti);
    }

    function approve_cuti_kasubbag_tu($status, $id_cuti)
    {
        $data = [
            "check_ktu" => 1,
            "tgl_check_ktu" => date("Y-m-d"),
            "status" => $status,
        ];

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $data);
        $this->session->set_flashdata(
            "message",
            "Pengajuan cuti telah disetuju"
        );

        redirect("cuti/detail_cuti/" . $id_cuti);
    }

    function approve_cuti_kapuskec($status, $id_cuti)
    {
        $detail_cuti = $this->Cuti_model->getDetailCuti($id_cuti);
        $id_pegawai = $detail_cuti[0]->id_pegawai;
        $jns_cuti = $detail_cuti[0]->jns_cuti;
        $tgl_dari = $detail_cuti[0]->tgl_dari;
        $jns_hak_cuti = $detail_cuti[0]->jns_hak_cuti;
        $hari_cuti = $detail_cuti[0]->hari_cuti;
        $alasan_cuti = $detail_cuti[0]->alasan_cuti;
        $nip = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
        $pin = substr($nip, -4);

        if ($jns_cuti == 1) {
            //cuti tahunan
            $sisa_cuti = $this->Cuti_model->getSisaCuti(
                $id_pegawai,
                $jns_hak_cuti
            );
            $sisa_akhir = $sisa_cuti - $hari_cuti;
            $ket = "Penggunaan cuti";
            $this->Cuti_model->insertLogCuti(
                $id_pegawai,
                $jns_hak_cuti,
                $jns_cuti,
                $id_cuti,
                $hari_cuti,
                $sisa_akhir,
                $ket
            );

            if ($hari_cuti == 1) {
                $this->Presensi_model->insertAbsensiCuti($tgl_dari, $pin);
            } else {
                $list_hari = $this->Cuti_model->getListHariCuti($id_cuti);
                for ($c = 0; $c < count($list_hari); $c++) {
                    $tgl_cuti = $list_hari[$c]->tanggal;
                    $this->Presensi_model->insertAbsensiCuti(
                        $tgl_cuti,
                        $pin,
                        $alasan_cuti
                    );
                }
            }
            // kondisi sudah divalidasi oleh kasubbag TU
            $data = [
                "check_kapuskec" => 1,
                "tgl_check2" => date("Y-m-d"),
                "status" => "APPROVE",
            ];
        } else {
            $detail_cuti = $this->Cuti_model->getDetailCuti($id_cuti);
            $id_pegawai = $detail_cuti[0]->id_pegawai;
            $jns_cuti = $detail_cuti[0]->jns_cuti;
            $tgl_dari = $detail_cuti[0]->tgl_dari;
            $end_date = $detail_cuti[0]->tgl_sampai;
            $alasan_cuti = $detail_cuti[0]->alasan_cuti;

            $selisihhari = datediff("d", $tgl_dari, $end_date);
            $hari_cuti = $selisihhari + 1;
            $tgl_cuti = $tgl_dari;

            for ($c = 0; $c < $hari_cuti; $c++) {
                $this->Presensi_model->insertAbsensiCuti(
                    $tgl_cuti,
                    $pin,
                    $alasan_cuti
                );
                $tgl_cuti = add_date($tgl_cuti, 1);
            }

            $data = [
                "check_kapuskec" => 1,
                "tgl_check2" => date("Y-m-d"),
                "status" => "APPROVE",
            ];
        }

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $data);
        $this->session->set_flashdata(
            "message",
            "Pengajuan cuti telah disetuju"
        );

        redirect("cuti/detail_cuti/" . $id_cuti);
    }





    function cancel_cuti_pegawai($id_cuti)
    {
        //function untuk pembatalan cuti oleh pegawai yang bersangkutan (self cancel)

        $detail_cuti = $this->Cuti_model->getDetailCuti($id_cuti);
        $id_pegawai = $detail_cuti[0]->id_pegawai;
        $jns_hak_cuti = $detail_cuti[0]->jns_hak_cuti;
        $hari_cuti = $detail_cuti[0]->hari_cuti;
        $status = $detail_cuti[0]->status;
        $jns_cuti = $detail_cuti[0]->jns_cuti;

        $sisa_cuti = $this->Cuti_model->getSisaCuti($id_pegawai, $jns_hak_cuti);
        $sisa_akhir = $sisa_cuti + $hari_cuti;
        $ket = "Sisa  cuti dikembalikan, pembatalan cuti";
        $this->Cuti_model->insertLogCuti(
            $id_pegawai,
            $jns_hak_cuti,
            $jns_cuti,
            $id_cuti,
            $hari_cuti,
            $sisa_akhir,
            $ket
        );

        //$this->Presensi_model->updateAbsensiCancelCuti($id_cuti, $pin);

        $this->db->where("id", $id_cuti);
        $this->db->set("status", "CANCEL");
        $this->db->update("ts_cuti");

        $this->db->where("id_cuti", $id_cuti);
        $this->db->delete("tbl_detail_cuti");

        $this->session->set_flashdata("message", "Cuti telah dibatalkan");
        redirect("cuti/my_cuti");
    }

    // function detail_mycuti($id_cuti)
    // {
    //     $id_pegawai = $this->session->userdata("id_pegawai");
    //     $data["detail_cuti"] = $this->Cuti_model->getDetailCuti($id_cuti);
    //     $data["list_hari_cuti"] = $this->Cuti_model->getListHariCuti($id_cuti);
    //     $data["cutiPegawai"] = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
    //     $data['detail_pegawai'] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
    //     $this->load->view("cuti/detail_mycuti", $data);
    // }

    function acc_cuti_kapuskel()
    {
        $id = $this->input->post("id");

        //echo $id;
    }

    function ajaxGetCuti(){
        $status = $this->input->post("status");
        $pengajuan_cuti_pending =  $this->Cuti_model->getDataCutiPegawaiByStatus($status);

        echo ' <table class="table table-centered table-nowrap table-hover mb-0 ">
                                                <tbody>';

        foreach ($pengajuan_cuti_pending as $cuti) {
            $status = $cuti->status;
            if ($status=='PEND0') {
                $flag_status = '<span class="badge badge-warning-lighten">ACC Pengganti</span>';
            }else if($status=='PEND1'){
                $flag_status = '<span class="badge badge-info-lighten">ACC Kapustu</span>';
            }else if($status=='PEND2'){
                $flag_status = '<span class="badge badge-success-lighten">ACC KTU</span>';
            }else{
                $flag_status = '-';
            }

            echo ' <tr>
                        <td>
                            <h5 class="font-14 my-1"><a href="javascript:void(0);" class="text-body">'.$cuti->nama.'</a></h5>
                            <span class="text-muted font-13">'.$cuti->alasan_cuti.'</span>
                        </td>
                        <td>
                            <span class="text-muted font-13">Menunggu</span> <br>
                            '.$flag_status.'
                        </td>
                        <td>
                            <span class="text-muted font-13">Tanggal Cuti</span>
                            <h5 class="font-14 mt-1 fw-normal">'.format_hari($cuti->tgl_dari).', '.format_view($cuti->tgl_dari).'</h5>
                        </td>
                        <td>
                            <span class="text-muted font-13">Lama Cuti</span>
                            <h5 class="font-14 mt-1 fw-normal">'.$cuti->hari_cuti.' hari</h5>
                        </td>
                        <td class="table-action" style="width: 90px;">
                            <a href="'.base_url().'cuti/detail_pengajuan_cuti/'.$cuti->id.'" class="btn btn-sm btn-info"> View Detail</a>

                        </td>
                    </tr>';
        }

        echo '      </tbody>
             </table>';
    }

    function pengajuan_cuti_pegawai()
    {
        $id_validator = $this->session->userdata("id_pegawai");
        $data["cutiPegawai"] = $this->Cuti_model->getDataCutiPegawai(
            "Semua",
            $id_validator
        );
        $this->load->view("cuti/pengajuan_cuti_pegawai", $data);
    }

    function detail_cuti($id_cuti)
    {
        $data["detail_cuti"] = $this->Cuti_model->getDetailCuti($id_cuti);
        $data["list_hari_cuti"] = $this->Cuti_model->getListHariCuti($id_cuti);
        $this->load->view("cuti/detail_cuti", $data);
    }

    function update_detail_cuti($id_cuti)
    {
        $this->Cuti_model->updateDataDetailCuti($id_cuti);

        $pesan = createMessageInfo("Cuti berhasil diupdate", "success");
        $this->session->set_flashdata("message", $pesan);
        redirect("cuti/edit_cuti/" . $id_cuti);
    }

    function generateRandomNumber()
    {
        $min = pow(10, 9); // Minimum 10-digit number (1000000000)
        $max = pow(10, 10) - 1; // Maximum 10-digit number (9999999999)

        return strval(mt_rand($min, $max)); // Generate random number and convert to string
    }

  

     function ajax_detail_pengajuan_cuti($id_cuti)
    {
        $data["detail_cuti"] = $this->Cuti_model->getDetailCuti($id_cuti);

        $this->load->view("cuti/ajax_detail_pengajuan_cuti", $data);
    }


    public function ajax_detail_pengganti_cuti()
    {
        $id_pegawai = $this->session->userdata('id_pegawai');
        $data['cuti'] = $this->Cuti_model->getPermohonanPengganti($id_pegawai);
        $this->load->view('cuti/ajax_detail_pengganti_cuti', $data);
    }

    public function search_pegawai()
    {
        $id_pegawai = $this->session->userdata("id_pegawai");
        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $id_jabatan = $pegawai[0]->id_jabatan;

        $keyword = $this->input->post("keyword");

        echo '<script>


                $(".choose_pegawai").click(function() {
                    var data = $(this).attr("id");
                    var pecah = data.split("/");
                    var id_pegawai = pecah[0];
                    var nama_pegawai = pecah[1];

                    $("#search_pegawai").val(nama_pegawai);
                    $("#id_pegawai_choose").val(id_pegawai);
                    $("#list_pegawai").hide();
                    $(".btn-success").removeClass("d-none");
                });
                </script>';

        $sql = "SELECT id_pegawai, nama FROM mst_pegawai
        WHERE nama like '%$keyword%'
        AND tahun_anggaran = '2024'
        AND id_jabatan =  $id_jabatan
        AND status_kerja = 1 AND id_pegawai != $id_pegawai";
        $qry = $this->db->query($sql);
        $row = $qry->result();

        //	$row = $this->Pegawai_model->search_pegawai($keyword);

        for ($i = 0; $i < count($row); $i++) {
            $id_pegawai = $row[$i]->id_pegawai;
            $nama = $row[$i]->nama;

            echo '<div class="choose_pegawai" id="' .
                $id_pegawai .
                "/" .
                $nama .
                '">' .
                $nama .
                "</div>";
        }
    }


    // function setujui_pengganti_cuti($id_cuti, $from_page='my_dashboard')
    // {
    //     $this->db->where("id", $id_cuti);
    //     $this->db->set("cek_pengganti", 1);
    //     $this->db->set("tgl_cek", date("Y-m-d"));
    //     $this->db->set("status", "PEND1");
    //     $this->db->update("ts_cuti");

    //     $pesan = createMessageInfo("Pengajuan cuti telah disetujui", "success");
    //     $this->session->set_flashdata("message", $pesan);

    //     if($from_page == 'my_dashboard')
    //         redirect("dashboard/my_dashboard");
    //       else
    //         redirect("dashboard");
        
      
    // }


    function setujui_cuti($id)
    {
        if ($this->input->method() === 'post') {
                $status = $this->input->post('status');

                // Validasi status: 1 = setujui, 2 = tolak
                if (!in_array($status, ['1', '2'])) {
                    echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
                    return;
                }

                // Update status cuti di database
                $result = $this->Cuti_model->updateStatusCuti($id, $status);

                if ($result) {
                    $msg = ($status == '1') ? 'Cuti disetujui.' : 'Cuti ditolak.';
                    echo json_encode(['success' => true, 'message' => $msg]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status.']);
                }
            } else {
                show_404();
            }
    }

    function setujui_cuti_kapuskel($id_cuti)
    {
        //$id_cuti = $this->input->post("id_cuti");

        $data = [
            "check_kapuskel" => 1,
            "tgl_check" => date("Y-m-d"),
            "status" => "PEND2",
        ];

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $data);


         $dataTblDetailCuti = [
            "acc_atasan" =>  date("Y-m-d H:i:s"),
            "status" => "Proses",
            'keterangan_status' => 'Menunggu acc KTU',
        ];

        $this->db->where('id_cuti', $id_cuti);
        $this->db->update("tbl_detail_cuti", $dataTblDetailCuti);
         echo json_encode(['status' => 'success']);
    }


    function pengajuan_cuti_all()
    {

        $id_validator = $this->session->userdata("id_pegawai");

        $tahun = 2025;
        $data['cuti_perbulan'] = [];

        for ($i = 1; $i <= 12; $i++) {
        $cuti = $this->Cuti_model->getByMonthRange($i, $tahun, $id_validator);
        $data['cuti_perbulan'][$i] = $cuti;
        }

        $data['tahun'] = $tahun;


        $this->load->view("cuti/pengajuan_cuti_all", $data);
    }
    
    function buat_pengajuan_cuti($jns_hak_cuti = 1)
    {
        $id_pegawai = $this->session->userdata("id_pegawai");
        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $id_jabatan = $pegawai->id_jabatan;
        $nip = $this->session->userdata("nip");
        $data[
            "listPegawaiPengganti"
        ] = $this->Cuti_model->getListPegawaiPenggantiCuti(
            $id_pegawai,
            $id_jabatan
        );

        $data['tahun']          = [2025, 2026];
        $data['cuti']           = $this->acm->get_rekap_cuti_pegawai_by_id($id_pegawai, $data['tahun']);
        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $this->load->view("cuti/form_pengajuan_cuti", $data);
    }

    // function buat_pengajuan_cuti2($jns_hak_cuti = 1)
    // {
    //     $id_pegawai = $this->session->userdata("id_pegawai");
    //     $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
    //     $id_jabatan = $pegawai[0]->id_jabatan;
    //     $nip = $this->session->userdata("nip");
    //     $data[
    //         "listPegawaiPengganti"
    //     ] = $this->Cuti_model->getListPegawaiPenggantiCuti(
    //         $id_pegawai,
    //         $id_jabatan
    //     );
    //     $data["master_cuti"] = $this->Master_model->getlistCuti();
    //     $this->load->view("cuti/form_pengajuan_cuti2", $data);
    // }

    function update_cuti($id_cuti)
    {
        $this->Cuti_model->updateDataCuti($id_cuti);

        $this->session->set_flashdata(
            "message",
            '<div class="alert alert-success">Pengajuan cuti berhasil diupdate</div>'
        );
        redirect("cuti/detail_pengajuan_cuti/" . $id_cuti);
    }

    

    function finish_pengajuan_cuti(){
        $this->session->set_userdata($this->input->post());
        redirect('cuti/summary_pengajuan_cuti');
    }
  


    public function search_pengganti() {
        $keyword = $this->input->get('q');

        $id_pegawai = $this->session->userdata("id_pegawai");
        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $id_jabatan = $pegawai[0]->id_jabatan;

        $result = $this->Pegawai_model->cari_pegawai_pengganti_cuti($keyword, $id_jabatan, $id_pegawai);

        echo json_encode($result); // hasil harus array of ['id' => ..., 'nama' => ...]
    }


    function upload_file(){


      $uploadPhoto = $this->Cuti_model->uploadDokumenCuti();

      if($uploadPhoto == false){
           //klo  gagal
          echo 'gagal';
         // redirect('dashboard/my_dashboard');
      }else{
          echo 'berhasil';
      }
    }


    public function auto_upload() {
      if (!isset($_FILES['photo'])) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada file dikirim.']);
        return;
      }

      $uploadPath = './uploads/lain2/';
      if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
      }

      $file_name = date('His').'_temp';
      $this->load->library('upload');
      $config['upload_path'] = $uploadPath;
      $config['allowed_types'] = 'jpg|jpeg|png|gif';
      $config['file_name'] = $file_name;


      $this->upload->initialize($config);

      // if (!$this->upload->do_upload('photo')) {
      //     echo $this->upload->display_errors('<p>', '</p>');
      //     echo '<pre>'; print_r($_FILES); echo '</pre>';
      // }


      if ($this->upload->do_upload('photo')) {
        $data = $this->upload->data();
        $fileName = $data['file_name'];
        $session_id = session_id();

        $this->db->insert('cuti_foto_temp', [
          'session_id' => $session_id,
          'file_name' => $fileName
        ]);

        $photo_id = $this->db->insert_id();

        echo json_encode([
          'status' => 'success',
          'message' => 'Upload berhasil.',
          'url' => base_url('uploads/' . $fileName),
          'photo_id' => $photo_id
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => strip_tags($this->upload->display_errors())
        ]);
      }
    }

    public function delete_temp_photo($id) {
      $photo = $this->db->get_where('cuti_foto_temp', ['id' => $id])->row();

      if ($photo) {
        $filePath = './uploads/' . $photo->file_name;
        if (file_exists($filePath)) {
          unlink($filePath);
        }

        $this->db->delete('cuti_foto_temp', ['id' => $id]);

        echo json_encode(['status' => 'success']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Foto tidak ditemukan.']);
      }
    }

    public function submit_form() {
      // Simpan form cuti
      $this->db->insert('pengajuan_cuti', [
        'nama' => $this->input->post('nama'),
        'tanggal_mulai' => $this->input->post('tanggal_mulai'),
        'tanggal_selesai' => $this->input->post('tanggal_selesai'),
        // dst...
      ]);
      $cuti_id = $this->db->insert_id();

      // Pindahkan foto dari temp ke tabel cuti_foto
      $session_id = session_id();
      $fotos = $this->db->get_where('cuti_foto_temp', ['session_id' => $session_id])->result();

      foreach ($fotos as $foto) {
        $this->db->insert('cuti_foto', [
          'pengajuan_id' => $cuti_id,
          'file_name' => $foto->file_name
        ]);
      }

      // Bersihkan foto temp
      $this->db->delete('cuti_foto_temp', ['session_id' => $session_id]);

      echo "Pengajuan cuti dan foto berhasil disimpan.";
    }


    // function cancel_cuti(){
    //     $id_cuti = $this->input->post('id_cuti');

    //     $data = array(
    //         'status' => 'CANCEL'
    //     );

    //     $this->db->where('id', $id_cuti);
    //     $this->db->update('ts_cuti', $data);

    //     $pesan =  createMessageInfo('Cuti telah dibatalkan', 'success');
    //     $this->session->set_flashdata('message', $pesan);

    //     redirect('cuti/index');

    // }
}
