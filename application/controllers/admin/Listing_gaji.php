<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Listing_gaji  extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
        $this->load->model('Laporan_model');
         $this->load->model('Admin_cuti_model', 'acm');
		$this->load->helper('text');
		$this->Auth_model->cekAuthLogin();
	}


	public function index()
	{

        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['listing_tkd'] = $this->Laporan_model->getListingTKD($periode);

        //$this->load->view('admin/listing_tkd/main', $data);
        $this->load->view('admin/listing_gaji/main', $data);
    }

    function datalisting($status_pegawai='non_pns') {
        

        $bulan = $this->input->get('bulan')??date('m');
        $tahun = $this->input->get('tahun')??date('Y');

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        $table_gaji = $status_pegawai=='non_pns' ? 'ts_listing_gaji': 'tbl_gaji_p3k_pw';

		$this->db->order_by('nama', 'ASC');
		$this->db->select('*');
		$this->db->from($table_gaji);
		$this->db->where('periode', $periode);

		$query = $this->db->get();
		$data['datalist'] =  $query->result();
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['status_pegawai'] = $status_pegawai;

        if($status_pegawai=='non_pns'){
            $this->load->view('admin/listing_gaji/listing_gaji_nonpns', $data);
        }else{
            $this->load->view('admin/listing_gaji/listing_gaji_pppk', $data);
        }
       
    }


    
    //import file gaji non_pns
    function preview_import()  {
        $date_now = date("Ymd_Hi");
        $file_name = $date_now;

       
        $bulan = $this->input->post('bulan')??date('m');
        $tahun = $this->input->post('tahun')??date('Y');

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        //---delete data gaji periode tersebut sebelum import ulang----

        $this->db->where("periode", $periode);
        $this->db->delete("ts_listing_gaji");

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load( "uploads/" . $path . "/" . $file_name . ".xlsx" ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;
            $newData = [];
            $dataInsert = [];
           // print_array($sheet);

                foreach ($sheet as $row) {

                      $rekening    = trim($row["T"]);
                      if($rekening!=''){
                     
                  
                        $nama        = trim($row["B"]);
                        $jabatan     = trim($row["C"]);

                        $tanggal     = $row["D"];
                        $bulan_tmt       = bulanAngka($row["E"]);
                        $tahun_tmt       = $row["F"];

                        $tmt = date('Y-m-d', strtotime("$tahun_tmt-$bulan_tmt-$tanggal"));

                        $status      = trim($row["G"]);
                        $nik         = trim($row["H"]);

                      
                        $gaji_pokok  = normalizeNumber($row["I"]);
                        $tunj_suami  = clear_tags($row["J"]);
                        $tunj_anak1  = clear_tags($row["K"]);
                        $tunj_anak2  = clear_tags($row["L"]);

                        $bruto       = normalizeNumber($row["O"]);
                        $ptkp        = trim($row["P"]);

                        $tarif       = str_replace(',', '.', $row["Q"]);
                        $pajak       = normalizeNumber($row["R"]);
                        $netto       = normalizeNumber($row["S"]);

                       
                        $dataInsert[] = [
                            'periode'       => $periode,
                            'nama'          => $nama,
                            'jabatan'       => $jabatan,
                            'tmt'           => $tmt,
                            'status_kawin'  => $status,
                            'nik'           => $nik,
                            'gaji_pokok'    => $gaji_pokok,
                            'tunj_suami'    => $tunj_suami,
                            'tunj_anak1'    => $tunj_anak1,
                            'tunj_anak2'    => $tunj_anak2,
                            'bruto'         => $bruto,
                            'ptkp'          => $ptkp,
                            'tarif_ter'     => $tarif,
                            'pajak'         => $pajak,
                            'netto'         => $netto,
                            'no_rekening'   => $rekening,
                            'ttd_spj'       => '',
                            'no_hp'         => '',
                            'ket'           => null
                        ];

                      }

                }
               
                // print_array($dataInsert);
                // exit;
               // insert batch
                if (!empty($dataInsert)) {
                    $this->db->insert_batch('ts_listing_gaji', $dataInsert);
                }
        }

        $this->session->set_flashdata(
            "message",
            '<div class="alert alert-success" role="alert">Data berhasil diimport!</div>'
        );



        redirect("admin/listing_gaji/datalisting/non_pns?bulan=$bulan&tahun=$tahun");
        
    }


    function view_ttd($status_pegawai='non_pns') {
        $bulan = $this->input->get('bulan')??date('m');
        $tahun = $this->input->get('tahun')??date('Y');

         $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        $table_gaji = $status_pegawai=='non_pns' ? 'ts_listing_gaji': 'tbl_gaji_p3k_pw';

		$this->db->order_by('nama', 'ASC');
		$this->db->select('*');
		$this->db->from($table_gaji);
		$this->db->where('periode', $periode);

		$query = $this->db->get();
		$data['data_gaji'] =  $query->result();
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['status_pegawai'] = $status_pegawai;

        if($status_pegawai=='non_pns'){
            $this->load->view('admin/listing_gaji/view_daftar_ttd_gaji', $data);
        }else{
            $this->load->view('admin/listing_gaji/view_daftar_ttd_gaji_pppk', $data);
        }


    }

     function print($status_pegawai='non_pns') {
        $bulan = $this->input->get('bulan')??date('m');
        $tahun = $this->input->get('tahun')??date('Y');

         $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        $table_gaji = $status_pegawai=='non_pns' ? 'ts_listing_gaji': 'tbl_gaji_p3k_pw';

		$this->db->order_by('nama', 'ASC');
		$this->db->select('*');
		$this->db->from($table_gaji);
		$this->db->where('periode', $periode);

		$query = $this->db->get();
		$data['data_gaji'] =  $query->result();
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['status_pegawai'] = $status_pegawai;

        if($status_pegawai=='non_pns'){
            $this->load->view('admin/listing_gaji/print', $data);
        }else{
            $this->load->view('admin/listing_gaji/print_pppk', $data);
        }


    }

    function import_gaji_pppk()  {
         $date_now = date("Ymd_Hi");
        $file_name = $date_now;

       
        $bulan = $this->input->post('bulan')??date('m');
        $tahun = $this->input->post('tahun')??date('Y');

        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        //---delete data gaji periode tersebut sebelum import ulang----

        $this->db->where("periode", $periode);
        $this->db->delete("tbl_gaji_p3k_pw");

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load( "uploads/" . $path . "/" . $file_name . ".xlsx" ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;
            $newData = [];
            $dataInsert = [];
           // print_array($sheet);

                foreach ($sheet as $row) {
                    if ($numrow > 1) {

                        $nama_nip               = trim($row["B"]);
                

                        if (strpos($nama_nip, "/") !== false) {
                            list($nama, $nipppk_pw) = array_map('trim', explode("/", $nama_nip));
                        } else {
                            $nama = $nama_nip;
                            $nipppk_pw = '';
                        }


                        $jabatan            = trim($row["C"]);
                        $jumlah_gaji        = clear_tags($row["D"]);
                        $gaji_hasil_kerja   = clear_tags($row["E"]);
                        $pot_absen          = clear_tags($row["F"]);
                        $pot_bpjs_1         = clear_tags($row["G"]);
                        $pot_pph            = clear_tags($row["H"]);
                        $pot_tht_325        = clear_tags($row["I"]);
                        $bruto              = clear_tags($row["J"]);
                        $jumlah_diterima    = clear_tags($row["K"]);
                        $tunjangan_bpjs_4   = clear_tags($row["L"]);

                        $pegawai = $this->db
                            ->where('nip', $nipppk_pw)
                            ->get('mst_pegawai')
                            ->row();

                        $id_pegawai = $pegawai ? $pegawai->id_pegawai : 0;


                        // contoh ambil bulan & tahun dari input/form
                        $bulan = $this->input->post('bulan');
                        $tahun = $this->input->post('tahun');

                        // format periode (YYYY-MM)
                        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

                        $dataInsert[] = [
                            'id_pegawai'  => $id_pegawai,
                            'nama'                      => $nama,
                            'nipppk_pw'                 => $nipppk_pw, // isi jika ada
                            'jabatan'                   => $jabatan,
                            'bulan'                     => $bulan,
                            'tahun'                     => $tahun,
                            'periode'                   => $periode,
                            'jumlah_gaji'               => $jumlah_gaji,
                            'gaji_hasil_kerja'          => $gaji_hasil_kerja,
                            'pot_absen'                 => $pot_absen,
                            'pot_bpjs_1'                => $pot_bpjs_1,
                            'pot_pph'                   => $pot_pph,
                            'pot_tht_325'               => $pot_tht_325,
                            'gaji_setelah_pot_absen'    => $bruto,
                            'jumlah_diterima'           => $jumlah_diterima,
                            'tunjangan_bpjs_4'          => $tunjangan_bpjs_4,
                            'bruto'                     => $bruto,
                            'no_rekening'               => NULL,
                            'ttd_pegawai'               => 0,
                            'no_hp'                     => NULL,
                            'ttd_oleh'                  => NULL,
                            'tgl_ttd'                   => NULL
                        ];
                    }

                    $numrow++;
                }


                // print_array($dataInsert);
                // exit;
                // insert batch
                if (!empty($dataInsert)) {
                    $this->db->insert_batch('tbl_gaji_p3k_pw', $dataInsert);
                }
        }

        $this->session->set_flashdata(
            "message",
            '<div class="alert alert-success" role="alert">Data berhasil diimport!</div>'
        );



        redirect("admin/listing_gaji/datalisting/pppk_pw?bulan=$bulan&tahun=$tahun");
        
    }

    function fixedDataGaji(){
        
        $qry = $this->db->get_where('ts_listing_gaji13',['periode' => '2025-06']);
        $datalist = $qry->result();

        // foreach ($datalist as $list) {
        //     $total = $list->total;
        //     $id = $list->id;
        //     $this->db->where('id', $id);
        //     $this->db->update('ts_listing_gaji13', array('netto' => $total));
        // }

        print_array($datalist);
    }

}