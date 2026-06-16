<?php
defined("BASEPATH") or exit("No direct script access allowed");
class Listing_tkd extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Laporan_model");
        $this->load->model("Admin_cuti_model", "acm");
        $this->load->helper("text");
        $this->Auth_model->cekAuthLogin();
    }

    public function index()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["listing_tkd"] = $this->Laporan_model->getListingTKD($periode);

        //$this->load->view('admin/listing_tkd/main', $data);
        $this->load->view("admin/listing_tkd/main", $data);
    }

    public function p3k_pw()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["listing_tkd"] = $this->Laporan_model->getListingTKDP3k($periode);

        //$this->load->view('admin/listing_tkd/main', $data);
        $this->load->view("admin/listing_tkd/pppk_pw/index", $data);
    }

    public function view_listing()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["listing_tkd"] = $this->Laporan_model->getListingTKD($periode);

        //$this->load->view('admin/listing_tkd/main', $data);
        $this->load->view("admin/listing_tkd/view_listing", $data);
    }

    public function create_listing()
    {
        //membuat row listing TKD
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $date_periode = $periode . "-01";

        $this->db->select('
			p.id_pegawai,
            p.tgl_masuk,
			p.nama,
			p.nip,
			j.nama as jabatan,
            dp.npwp,
            dp.no_rekening,
			c.periode,
			c.bobot_aktifitas,
			c.perilaku,
			c.serapan,
			c.total_capaian
		');

        $this->db->from("mst_pegawai p");
        // Join capaian
        $this->db->join(
            "tbl_capaian c",
            "p.nip = c.nip AND c.periode = " . $this->db->escape($periode),
            "left"
        );

        // Join jabatan
        $this->db->join("mst_jabatan j", "p.id_jabatan = j.id", "left");
        $this->db->join("detail_pegawai dp", "p.nip = dp.nip", "left");

        $this->db->where("p.jns_pegawai", "non_pns");
        $this->db->where("p.status_kerja !=", 0); // optional kalau hanya pegawai aktif

        $this->db->order_by("p.nama", "ASC");

        $row = $this->db->get()->result();

        $no = 1;

        print_array($row);
        exit;

        foreach ($row as $list) {
            $gp = $list->gaji_pokok;
            $pengkalian = $list->pengali;
            $tkd_pokok = round($gp * $pengkalian);
            $tgl_masuk = $list->tgl_masuk;
            $capaian = $list->total_capaian;
            $masa_kerja_bulan = $list->masa_kerja_bulan;
            $masa_kerja_tahun = $list->masa_kerja_tahun;
            $nip = $list->nip;

            $pph21 = 0;
            $bpjs_kes = 0;
            $bpjs_tk = 0;

            $bruto = ($capaian * $tkd_pokok) / 100;
            $pengurang = $pph21 + $bpjs_tk + $bpjs_kes;
            $thp = $bruto - $pengurang;

            //if (strtotime($tgl_masuk) <= strtotime($date_periode)) {

            $newData = [
                "periode" => $periode,
                "nama" => ucwords($list->nama),
                "jabatan" => $list->jabatan,
                "nip" => $list->nip,
                "npwp" => clear_tags($list->npwp),
                "tkd_pokok" => 0,
                "capaian" => $capaian,
                "bruto" => 0,
                "pph21" => 0,
                "bpjs" => 0,
                "bpjs_tk" => 0,
                "thp" => 0,
                "no_rekening" => $list->no_rekening,
                "masa_kerja" =>
                    $masa_kerja_tahun . "tahun " . $masa_kerja_bulan . " bulan",
                "urutan" => $no,
                "update_on" => date("Y-m-d H:i:s"),
            ];
            //     }

            $no += 1;

            $this->db->select("nip");
            $this->db->from("ts_rekap_tkd");
            $this->db->where("periode", $periode);
            $this->db->where("nip", $list->nip);
            $existing = $this->db->get()->row();

            if (!$existing) {
                $this->db->insert("ts_rekap_tkd", $newData);
            }
        }

        redirect("admin/listing_tkd/index");
    }

    function update_capaian()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $this->db->select('
			p.id_pegawai,
            p.tgl_masuk,
			p.nama,
			p.nip,
			c.total_capaian,
		');

        $this->db->from("mst_pegawai p");
        // Join capaian
        $this->db->join(
            "tbl_capaian c",
            "p.nip = c.nip AND c.periode = " . $this->db->escape($periode),
            "left"
        );

        $this->db->where("p.jns_pegawai", "non_pns");
        $this->db->where("p.status_kerja !=", 0); // optional kalau hanya pegawai aktif

        $this->db->order_by("p.nama", "ASC");

        $row = $this->db->get()->result();
        $no = 1;

        foreach ($row as $list) {
            $capaian = $list->total_capaian;
            $nip = $list->nip;

            $newData = [
                "capaian" => $capaian,
                "update_on" => date("Y-m-d H:i:s"),
            ];

            $this->db->where("periode", $periode);
            $this->db->where("nip", $nip);
            $this->db->update("ts_rekap_tkd", $newData);
            $no += 1;
        }

        redirect("admin/listing_tkd/index");
    }

    function delete_form_list($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("ts_rekap_tkd");
        redirect("admin/listing_tkd/index");
    }

    function update_tkd_pegawai($nip, $tkd_id, $periode)
    {
        $getDataCapaian = $this->Kinerja_model->getDataCapaian($nip, $periode);
        if (!empty($getDataCapaian)) {
            $totalCapaian = $getDataCapaian[0]->total_capaian;
        } else {
            $totalCapaian = 0;
        }

        $detailTKD = $this->Laporan_model->getDetailRekapTKD($tkd_id);
        $tkd_pokok = $detailTKD[0]->tkd_pokok;
        $pph21 = $detailTKD[0]->pph21;
        $bpjs_kes = $detailTKD[0]->bpjs;
        $bpjs_tk = $detailTKD[0]->bpjs_tk;

        $bruto = ($totalCapaian * $tkd_pokok) / 100;
        // $bruto       = $bruto*0.75;

        $potongan = $pph21 + $bpjs_kes + $bpjs_tk;
        $thp = $bruto - $potongan;

        $newData = [
            "capaian" => $totalCapaian,
            "bruto" => $bruto,
            "thp" => $thp,
        ];

        $this->db->where("id", $tkd_id);
        $this->db->update("ts_rekap_tkd", $newData);

        redirect("admin/listing_tkd/index");
    }

    function update_listing_tkd()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $last_date = getEndDate($periode_bulan, $periode_tahun);
        $date1 = format_db($last_date);

        $listing_tkd = $this->Laporan_model->getListingTKD($periode);
        $tahun = date("Y");

        foreach ($listing_tkd as $tkd) {
            $id = $tkd->id;
            $pph21 = $tkd->pph21;
            $bpjs_kes = $tkd->bpjs;
            $bpjs_tk = $tkd->bpjs_tk;
            $nip = $tkd->nip;
            $capaian = $tkd->capaian;

            $dataGajiPegawai = $this->Pegawai_model->getDataGajiPegawai(
                $nip,
                $tahun
            );
            $detailPegawai = $this->Pegawai_model->getDataDetailPegawai($nip);
            //print_array($dataGajiPegawai);
            if (!empty($dataGajiPegawai)) {
                $tmt = $dataGajiPegawai[0]->tmt;
                $gaji_pokok = $dataGajiPegawai[0]->gaji_pokok;
                $pengkalian = $dataGajiPegawai[0]->pengali;
                $tkd_pokok = $gaji_pokok * $pengkalian;
                $masa_kerja_tahun = $dataGajiPegawai[0]->masa_kerja_tahun;
                $masa_kerja_bulan = $dataGajiPegawai[0]->masa_kerja_bulan;

                $hitungMasaKerja = hitungMasaKerja($date1, $tmt);
                $masa_kerja_tahun =
                    $masa_kerja_tahun + $hitungMasaKerja["years"];
                $masa_kerja_bulan =
                    $masa_kerja_bulan + $hitungMasaKerja["months"];
                $masa_kerja_days = $masa_kerja_bulan + $hitungMasaKerja["days"];

                //print_array($hitungMasaKerja);

                // echo $date1;

                $masa_kerja =
                    $masa_kerja_tahun .
                    " tahun " .
                    $masa_kerja_bulan .
                    " bulan";
            } else {
                $gaji_pokok = 0;
                $pengkalian = 0;
                $tkd_pokok = 0;
                $masa_kerja = "0";
            }

            $bruto = round(($capaian * $tkd_pokok) / 100);

            if ($masa_kerja_tahun == 0 && $masa_kerja_bulan < 3) {
                $bruto = round(($bruto * 75) / 100);
            }

            //echo $masa_kerja_bulan.' - '.$masa_kerja_days;
            // if ($masa_kerja_tahun == 0 && $masa_kerja_bulan < 4 && $masa_kerja_days < 10) {
            //     $bruto = round(($bruto * 75) / 100);
            // }

            $potongan = $pph21 + $bpjs_kes + $bpjs_tk;
            $thp = $bruto - $potongan;

            $jabatan = $this->Pegawai_model->getJabatanByNIP($nip);

            $data_update = [
                "jabatan" => $jabatan,
                "npwp" => $detailPegawai->npwp,
                "tkd_pokok" => $tkd_pokok,
                "bruto" => $bruto,
                "thp" => $thp,
                "no_rekening" => $detailPegawai->no_rekening,
                "masa_kerja" => $masa_kerja
            ];

            $this->db->where("id", $id);
            $this->db->update("ts_rekap_tkd", $data_update);

            // print_array($data_update);
        }

        redirect("admin/listing_tkd/index");
    }

    function ins()
    {
        $row = $this->db
            ->where("nip", "10202719990206202512597")
            ->where("periode", "2026-01")
            ->get("ts_rekap_tkd")
            ->row();

        if ($row) {
            $data = (array) $row; // ubah object ke array

            unset($data["id"]); // hapus id agar auto increment
            $data["periode"] = "2026-02"; // ubah periode
            $data["update_on"] = date("Y-m-d H:i:s"); // update waktu jika perlu

            $this->db->insert("ts_rekap_tkd", $data);
        }

        echo "selesai";
    }

    function update_rekap_tkd($periode)
    {
        //$periode          = $this->input->post('periode');

        $explod = explode("-", $periode);
        $tahun = $explod[0];
        $bulan = $explod[1];
        $thn_anggrn = 2024;
        $jumlahHariKerja = $this->Master_model->getMenitEfektifBulan(
            $bulan,
            $tahun
        );
        $waktu_efektif = $jumlahHariKerja * 300;

        $getListData = $this->Laporan_model->getListingTKD($periode);
        if (!empty($getListData)) {
            //klo udah ada datanya
            foreach ($getListData as $tkd) {
                $id = $tkd->id;
                $bruto = $tkd->bruto;
                $nama = $tkd->nama;
                $nip = $tkd->nip;
                $pph21 = $tkd->pph21;
                $bpjs_kes = $tkd->bpjs;
                $bpjs_tk = $tkd->bpjs_tk;

                $id_pegawai = $this->Pegawai_model->getIDpegawaiByName($nama);

                // $tkd_pokok        = $this->Pegawai_model->getTKDPokok($id_pegawai);

                // $getDataCapaian   = $this->Kinerja_model->getDataCapaian($nip, $periode);
                // if (!empty($getDataCapaian)) {
                //     $totalCapaian = $getDataCapaian[0]->total_capaian;
                // }else{
                //     $totalCapaian = 0;
                // }

                //$bruto = ($tkd_pokok*$totalCapaian)/100;
                $potongan = $pph21 + $bpjs_kes + $bpjs_tk;
                $thp = $bruto - $potongan;

                // $updateData = array(
                //     'tkd_pokok' => $tkd_pokok,
                //     'capaian' => $totalCapaian,
                //     'bruto' => $bruto,
                //     'thp' => $thp,
                //     'update_on' => date('Y-m-d H:i:s')
                // );

                $updateData = [
                    "thp" => $thp,
                    "update_on" => date("Y-m-d H:i:s")
                ];

                $this->db->where("id", $id);
                $this->db->update("ts_rekap_tkd", $updateData);

                //print_array($updateData);
            }
        } else {
            //klo periode itu masih kosong datanya
        }
        //print_array($getListData);

        redirect("admin/listing_tkd/index");
    }

    function detailCapaianPegawai()
    {
        $nip = $this->input->post("nip");

        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $ta = $this->session->userdata("periode_tahun");

        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $id_pegawai = $this->Pegawai_model->getIDpegawaiByNIP($nip, $ta);
        $data["detail_pegawai"] = $this->Pegawai_model->getDataEditPegawai(
            $id_pegawai
        );
        $data["dataRekap"] = $this->Presensi_model->getRekapAbsensiPegawai(
            $id_pegawai,
            $periode
        );
        $data["dataTKD"] = $this->Laporan_model->getRekapTKDPegawai(
            $nip,
            $periode
        );

        $this->load->view("admin/listing_tkd/detail_capaian_pegawai", $data);
    }

    public function ajaxDetailCapaian()
    {
        $dataPrse = $this->input->post("data");
        $expld = explode("/", $dataPrse);
        $id_pegawai = $expld[0];
        $nip = $expld[1];
        $periode = $expld[2];

        $data["pegawai"] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data["dataRekap"] = $this->Presensi_model->getRekapAbsensiPegawai(
            $id_pegawai,
            $periode
        );
        $data["dataTKD"] = $this->Laporan_model->getRekapTKDPegawai(
            $nip,
            $periode
        );
        $data["dataCapaian"] = $this->Kinerja_model->getDataCapaian(
            $nip,
            $periode
        );
        $this->load->view("admin/listing_tkd/detail_capaian_pegawai", $data);
        //print_array($detail_pegawai);
    }

    function detail_capaian($id_pegawai)
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["detail_pegawai"] = $this->Pegawai_model->getDataEditPegawai(
            $id_pegawai
        );
        $data["dataRekap"] = $this->Presensi_model->getRekapAbsensiPegawai(
            $id_pegawai,
            $periode
        );
        $data["master_cuti"] = $this->Master_model->getlistCuti();
        $this->load->view("admin/capaian_kinerja/detail_capaian", $data);
    }

    function detailTKD($jns_pegawai='non_pns', $id)
    {
        //$data['detail_pegawai']		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $table = $jns_pegawai == 'p3k_pw' ? 'ts_rekap_tkd_pppk' : 'ts_rekap_tkd';
        $data["detail_tkd"] = $this->Laporan_model->getDetailRekapTKD($id, $table);
     
        $transaksi = $this->db
            ->where("id_rekap_tkd", $id)
            ->get("ts_transaksi_tkd")
            ->result();
        $data["transaksi"] = $transaksi;

        if($jns_pegawai=='non_pns'){
            $this->load->view("admin/listing_tkd/detail_tkd_pegawai", $data);
        }else{
            $this->load->view("admin/listing_tkd/pppk_pw/detail_tkd", $data);
        }
      
    }

    function delete_ttd($id_rekap_tkd)
    {
        $this->db->where("id", $id_rekap_tkd);
        $this->db->set("ttd_spj", "");
        $this->db->update("ts_rekap_tkd");

        $this->session->set_flashdata(
            "success",
            "Data Signature TKD berhasil dihapus"
        );
        redirect("admin/listing_tkd/detailTKD/" . $id_rekap_tkd);
    }

    public function insert_transaksi()
    {
        $data = [
            "id_rekap_tkd" => $this->input->post("id_rekap_tkd"),
            "jenis_transaksi" => $this->input->post("jenis_transaksi"),
            "jumlah" => $this->input->post("jumlah"),
            "keterangan" => $this->input->post("keterangan"),
            "created_at" => date("Y-m-d H:i:s"),
        ];

        $this->db->insert("ts_transaksi_tkd", $data);

        redirect("admin/listing_tkd/detailTKD/" . $data["id_rekap_tkd"]);
    }

    // function insert_potongan(){
    //     $id_rekap_tkd = $this->input->post('id_rekap_tkd', TRUE);
    //     $jumlah = $this->input->post('jumlah', TRUE);
    //     $keterangan = $this->input->post('keterangan', TRUE);

    //     if (empty($id_rekap_tkd) || empty($jumlah)) {
    //         $this->session->set_flashdata('error', 'ID Rekap TKD dan Jumlah wajib diisi!');
    //         redirect('admin/listing_tkd/detailTKD/'.$id_rekap_tkd);
    //         return;
    //     }

    //     $periode_bulan = $this->session->userdata('periode_bulan');
    // 	$periode_tahun = $this->session->userdata('periode_tahun');
    // 	$periode = sprintf("%04d-%02d", $periode_tahun, $periode_bulan);

    //     $newData = [
    //         'id_rekap_tkd' => $id_rekap_tkd,
    //         'periode'=> $periode,
    //         'jumlah' => $jumlah,
    //         'keterangan'=> $keterangan
    //     ];

    //     $this->db->insert('ts_potongan_tkd', $newData);

    //     $this->session->set_flashdata('success', 'Data pemotongan TKD berhasil disimpan');
    //     redirect('admin/listing_tkd/detailTKD/'.$id_rekap_tkd);

    // }

    function update_tkd($id_rekap_tkd)
    {
        $tkd = $this->Laporan_model->getDetailRekapTKD($id_rekap_tkd);

        print_array($tkd);
    }

    function update_data_tkd()
    {
        $jns_pegawai = $this->input->get("jns_pegawai");
        if(empty($jns_pegawai)){
            $jns_pegawai = 'non_pns';
        }
        $id_rekap = $this->input->post("id_rekap_tkd");
        $tkd_pokok = (float) $this->input->post("tkd_pokok");
      
        $pph21 = (float) $this->input->post("pajak");
        $bpjs = (float) $this->input->post("bpjs");
      

        if($jns_pegawai == 'p3k_pw'){
            $bruto = (float) $this->input->post("bruto");
            $pot_absen = (float) $this->input->post("pot_absen");
            $tht = (float) $this->input->post("tht");
            $thp = (float) $this->input->post("thp");

            $data_update = [
                "tkd_pokok" => $tkd_pokok,
                "bruto" => $bruto,
                "pph21" => $pph21,
                "bpjs" => $bpjs,
                "pot_absensi" => $pot_absen,
                "tht"      => $tht,
                "thp" => $thp,
                "no_rekening" => $this->input->post("no_rekening"),
            ];
            $table = 'ts_rekap_tkd_pppk';

            // print_array($data_update);
            // exit;
        }else{
            $capaian = (float) $this->input->post("capaian");
            $bpjs_tk = (float) $this->input->post("bpjs_tk");

                // hitung nilai turunan
            $bruto = $tkd_pokok * ($capaian / 100);
            $thp = $bruto - ($pph21 + $bpjs + $bpjs_tk);
    
            $data_update = [
                "tkd_pokok" => $tkd_pokok,
                "capaian" => $capaian,
                "bruto" => $bruto,
                "pph21" => $pph21,
                "bpjs" => $bpjs,
                "bpjs_tk" => $bpjs_tk,
                "thp" => $thp
            ];
            $table = 'ts_rekap_tkd';
        }
        

        $this->db->where("id", $id_rekap);
        $this->db->update($table, $data_update);

        $this->session->set_flashdata("success", "Data  TKD berhasil disimpan");
        redirect("admin/listing_tkd/detailTKD/" .$jns_pegawai . "/" . $id_rekap);
    }

    function listing_thr()
    {
        $periode = "2025-02";
        $table = "ts_listing_thr";

        $data["data_tkd"] = $this->Laporan_model->getDataListing(
            $periode,
            $table
        );

        $this->load->view("admin/listing_tkd/listing_thr", $data);
    }

    function import_gaji()
    {
        echo form_open_multipart(
            base_url() . "admin/listing_tkd/import_gaji_process"
        );

        echo ' <strong> file (*.xls) : </strong>
                                        <input name="file" type="file"><br>
                                      <br>
                                       <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Import File</button>';

        echo form_close();
    }
    function import_gaji_process()
    {
        $date_now = date("Ymd_Hi");
        $file_name = $date_now;

        $jns_file = $this->input->post("jns_file");
        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load(
                "uploads/" . $path . "/" . $file_name . ".xlsx"
            ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            $periode = "2025-03";
            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;
            foreach ($sheet as $row) {
                //print_array($row);
                if ($numrow > 1) {
                    $nama = $row["B"];
                    $jabatan = $row["C"];

                    $tgl_tmt = $row["D"];
                    $bln_tmt = getNoBulan($row["E"]);
                    $thn_tmt = $row["F"];

                    $status_kawin = $row["G"];

                    $npwp = $row["H"];
                    $gp = $row["I"];

                    $tunj_suami = $row["J"];
                    $tunj_anak1 = $row["K"];
                    $tunj_anak2 = $row["L"];

                    $bruto = $row["N"];
                    $ptkp = $row["P"];
                    $tarif_ter = $row["Q"];
                    $pph = $row["R"];
                    $netto = $row["S"];
                    $no_rekening = $row["U"];
                    //   $jumlah      = str_replace("Rp","", $jumlah);
                    //   $jumlah      = str_replace(",","", $jumlah);

                    $tmt = $tgl_tmt . "-" . $bln_tmt . "-" . $thn_tmt;
                    $tmt = format_db($tmt);

                    $pajak = clear_tags($pph);
                    $pajak = str_replace("Rp", "", $pajak);

                    $npwp = str_replace(",", "", $npwp);

                    if ($nama != "") {
                        $data[] = [
                            "periode" => $periode,
                            "nama" => $nama,
                            "jabatan" => $jabatan,
                            "tmt" => $tmt,
                            "status_kawin" => $status_kawin,
                            "npwp" => $npwp,
                            "gaji_pokok" => clear_tags($gp),
                            "tunj_suami" => clear_tags($tunj_suami),
                            "tunj_anak1" => clear_tags($tunj_anak1),
                            "tunj_anak2" => clear_tags($tunj_anak2),
                            "bruto" => clear_tags($bruto),
                            "ptkp" => $ptkp,
                            "tarif_ter" => $tarif_ter,
                            "pajak" => $pajak,
                            "netto" => clear_tags($netto),
                            "no_rekening" => $no_rekening,
                            "ttd_spj" => ""
                        ];
                    }

                    $numrow++; // Tambah 1 setiap kali looping
                }

                $numrow++; // Tambah 1 setiap kali looping
            }

            // print_array($data);
            $this->db->insert_batch("ts_listing_gaji", $data);

            echo "berhasil";
            // redirect('admin/swab/error_import');
        } else {
            // Jika proses upload gagal

            echo $upload["error"];
            #$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
            #redirect('admin/swab/error_import');
        }
    }

    function importAll()
    {
        echo form_open_multipart(base_url() . "admin/listing_tkd/import_thr");

        echo ' <strong> file (*.xls) : </strong>
                                        <input name="file" type="file"><br>
                                      <br>
                                       <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Import File</button>';

        echo form_close();
    }

    function process_import_thr_tkd()
    {
        $date_now = date("Ymd_Hi");
        $file_name = $date_now;
        $path = "pajak";
        $upload = $this->Master_model->upload_file($file_name, $path);
        $tahun = "2026";

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load(
                "uploads/" . $path . "/" . $file_name . ".xlsx"
            ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $new_array = [];
            $numrow = 1;
            $num = 0;
            foreach ($sheet as $row) {
                if ($numrow > 0) {
                    $nama = $row["B"];
                    $jabatan = $row["C"];
                    $nik = $row["D"];
                    $tkd_pokok = $row["E"];
                    $total = $row["F"];
                    $no_rekening = $row["G"];

                    $dataTKD = $this->db
                        ->where("no_rekening", $no_rekening)
                        ->get("ts_rekap_tkd")
                        ->row();

                    // print_array($dataTKD);

                    if ($dataTKD) {
                        $nip = $dataTKD->nip;

                        $new_array[] = [
                            "tahun" => date("Y"),
                            "nama" => $nama,
                            "jabatan" => $jabatan,
                            "nip" => $nip,
                            "tkd_pokok" => clear_tags($tkd_pokok),
                            "total" => $total,
                            "no_rekening" => $no_rekening
                        ];

                        $num++;
                    }
                }
            }

            // $this->db->insert_batch('ts_listing_thr_tkd', $new_array);

            echo $num . " rows inserted";

            //print_array($new_array);
        }
    }

    //khusu import THR Gaji
    function import_thr()
    {
        $date_now = date("Ymd_Hi");
        $file_name = $date_now;

        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        $periode = "2026-03";

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load(
                "uploads/" . $path . "/" . $file_name . ".xlsx"
            ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;
            foreach ($sheet as $row) {
                if ($numrow > 0) {
                    $nama = $row["B"];
                    $jabatan = $row["C"];
                    $tmt_tgl = $row["D"];
                    $tmt_bln = $row["E"];
                    $tmt_thn = $row["F"];
                    $status_kawin = $row["G"];
                    $npwp = $row["H"];
                    $gp = $row["I"];
                    $tunj_suami = $row["J"];
                    $tunj_anak1 = $row["K"];
                    $tunj_anak2 = $row["L"];
                    $thr_gaji = $row["O"];

                    $thr_tkd = 0;
                    $total = 0;
                    $no_rekening = $row["P"];

                    $tmt = $tmt_tgl . " " . $tmt_bln . " " . $tmt_thn;

                    $dataTKD = $this->db
                        ->where("nama", $nama)
                        ->get("ts_listing_thr_tkd")
                        ->row();

                    if ($dataTKD) {
                        $jumlahThrTkd = $dataTKD->total;
                        $nip = $dataTKD->nip;
                        $total = $thr_gaji + $jumlahThrTkd;
                    } else {
                        $nip = 0;
                        $jumlahThrTkd = 0;
                    }
                    //print_array($dataTKD);

                    $newData[] = [
                        "periode" => $periode,
                        "nip" => $nip,
                        "nama" => $nama,
                        "jabatan" => $jabatan,
                        "tmt" => $tmt,
                        "status_kawin" => $status_kawin,
                        "npwp" => clear_tags($npwp),
                        "gaji_pokok" => clear_tags($gp),
                        "tunj_suami" => clear_tags($tunj_suami),
                        "tunj_anak1" => clear_tags($tunj_anak1),
                        "tunj_anak2" => clear_tags($tunj_anak2),
                        "thr_gaji" => clear_tags($thr_gaji),
                        "thr_tkd" => clear_tags($jumlahThrTkd),
                        "total" => clear_tags($total),
                        "no_rekening" => $no_rekening,
                        "ttd_spj" => ""
                    ];
                }

                $num++;
            } //close loop

            //print_array($newData);

            $this->db->insert_batch("ts_listing_thr", $newData);
        }

        echo $num . " rows inserted";

        echo "berhasil";
    }

    function import_tkd_full()
    {
        $date_now = date("Ymd_Hi");
        $file_name = $date_now;

        $jns_file = $this->input->post("jns_file");
        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        $periode = "2025-03";

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load(
                "uploads/" . $path . "/" . $file_name . ".xlsx"
            ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;
            foreach ($sheet as $row) {
                //print_array($row);
                if ($numrow > 0) {
                    $nama = $row["B"];
                    $tkd_pokok = $row["E"];
                    $capaian = $row["F"];
                    $bruto = $row["G"];
                    $pph21 = $row["H"];
                    $bpjs = $row["I"];
                    $bpjs_tk = $row["J"];
                    $total = $row["K"];

                    $tkd_pokok = clear_tags($tkd_pokok);
                    $bruto = clear_tags($bruto);
                    $thp = clear_tags($total);
                    $pph21 = clear_tags($pph21);
                    $bpjs = clear_tags($bpjs);
                    $bpjs_tk = clear_tags($bpjs_tk);

                    $capaian = str_replace(",", ".", $capaian);
                    //$potongan = $pph21+$bpjs+$bpjs_tk;
                    // $thp = $bruto-$potongan;

                    $cekTKD = $this->Laporan_model->cekDatalistingTKD(
                        $nama,
                        $periode
                    );
                    if (!empty($cekTKD)) {
                        $id = $cekTKD[0]->id;

                    

                        $data = [
                            "tkd_pokok" => $tkd_pokok,
                            "bruto" => $bruto,
                            "capaian" => $capaian,
                            "pph21" => $pph21,
                            "bpjs" => $bpjs,
                            "thp" => $thp,
                        ];

                        //    $this->db->where('id', $id);
                        //    $this->db->update('ts_rekap_tkd', $data);

                        print_array($data);
                    }
                }
            }
        }

        echo "Selesai";
    }

    function import_data()
    {
        $date_now = date("Ymd_Hi");
        $file_name = $date_now;

        $jns_file = $this->input->post("jns_file");
        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        $sql = "DELETE FROM temp_import";
        $this->db->query($sql);

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load(
                "uploads/" . $path . "/" . $file_name . ".xlsx"
            ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;
            foreach ($sheet as $row) {
                if ($numrow > 0) {
                    if ($jns_file == "pph21") {
                        $nama = $row["A"];
                        $jumlah = $row["B"];

                        $jumlah = str_replace("Rp", "", $jumlah);
                        $jumlah = str_replace(",", "", $jumlah);

                        $nip = $this->Pegawai_model->getNIPByName($nama);

                        $data[] = [
                            "nik" => $nip,
                            "nama" => $nama,
                            "jumlah" => $jumlah,
                            "import_file" => $jns_file,
                        ];
                    } else {
                        $nik = $row["A"];
                        $nama = $row["B"];
                        $jumlah = $row["C"];

                        $jumlah = str_replace("Rp", "", $jumlah);
                        $jumlah = str_replace(",", "", $jumlah);

                        if ($nik != "") {
                            $data[] = [
                                "nik" => $nik,
                                "nama" => $nama,
                                "jumlah" => $jumlah,
                                "import_file" => $jns_file,
                            ];
                        }
                    }

                    $numrow++; // Tambah 1 setiap kali looping
                }

                $numrow++; // Tambah 1 setiap kali looping
            }

            $this->db->insert_batch("temp_import", $data);

            redirect("admin/listing_tkd/list_import");
            // print_array($data);

            // echo 'Data pajak berhasil diimport.';
        } else {
            // Jika proses upload gagal

            echo $upload["error"];
            #$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
            #redirect('admin/swab/error_import');
        }
    }

    function submit_import()
    {
        $qry = $this->db->get("temp_import");
        $import_data = $qry->result();
        //$periode     = '2024-10';

        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        // $periode = '2024-10';

        foreach ($import_data as $import) {
            $nik = $import->nik;
            $nama = $import->nama;
            $jumlah = $import->jumlah;
            $import_file = $import->import_file;

            $this->db->where("nama", $nama);
            $this->db->where("periode", $periode);
            $this->db->set($import_file, $jumlah);
            $update = $this->db->update("ts_rekap_tkd");

            if ($update) {
                echo $nama . " Update Data Pajak berhasil";
            } else {
                echo $nama . " Update Data Pajak Gagal";
            }

            // $pegawai = $this->Pegawai_model->getPegawaiByNIK($nik);

            // if(!empty($pegawai)){
            //     $id_pegawai  = $pegawai[0]->id_pegawai;
            //     $nm  = $pegawai[0]->nama;
            //     $nip = $pegawai[0]->nip;

            //     $this->db->where('nip', $nip);
            //     $this->db->where('periode', $periode);
            //     $this->db->set($import_file, $jumlah);
            //     $this->db->update('ts_rekap_tkd');

            //     // if($import_file=='bpjs'){
            //     //     $colmn = 'bpjs_kes';
            //     // }else{
            //     //      $colmn = $import_file;
            //     // }

            //     // $this->db->where('id_pegawai', $id_pegawai);
            //     // $this->db->set($colmn, $jumlah);
            //     // $this->db->update('gaji_pegawai');

            // }
        }

        //  echo 'Data berhasil diupdate';

        //$this->load->view('admin/listing_tkd/success_import');
    }

    function updateEditListingGaji()
    {
        $no_ktp = $this->input->post("nik");

        $dataPegawai = $this->Pegawai_model->getPegawaiByNIK($no_ktp);

        // ///echo $npwp;
        if (!empty($dataPegawai)) {
            $nip = $dataPegawai[0]->nip;
            $no_rekening = $dataPegawai[0]->no_rekening;
            $nama = $dataPegawai[0]->nama;

            $this->db->where("nik", $no_ktp);
            $this->db->set("no_rekening", $no_rekening);
            $this->db->set("nama", $nama);
            $this->db->update("ts_listing_gaji");
            echo "berhasil!! data berhasil diupdate";
        } else {
            echo "Gagal!! data tidak ditemukan";
        }
    }

    function import_pajak()
    {
        $data = []; // Buat variabel $data sebagai array

        $periode = "2024-08";

        $date_now = date("Ymd_Hi");
        $file_name = $date_now;
        $path = "pajak";

        $upload = $this->Master_model->upload_file($file_name, $path);

        if ($upload["result"] == "success") {
            // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . "third_party/PHPExcel/PHPExcel.php";

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load(
                "uploads/" . $path . "/" . $file_name . ".xlsx"
            ); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel
                ->getActiveSheet()
                ->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = [];
            $numrow = 1;
            $num = 0;

            foreach ($sheet as $row) {
                print_array($row);

                // if($numrow > 0){
                //         $nama       = $row['A'];
                //         $pajak      = $row['G'];

                //         $pph21      = str_replace("Rp","", $pajak);
                //         $pph21      = str_replace(",","", $pph21);
                //         $id_pegawai = $this->Pegawai_model->getIDpegawaiByName($nama);

                //      // echo 'Nama :'.$nama.'  - NIP : '.$NIP.' <br> Jumlah :'.$pph21.'<br>';

                //         // $this->db->where('id_pegawai', $id_pegawai);
                //         // $this->db->set('pph21', $pph21);
                //         // $this->db->update('gaji_pegawai');

                //     $numrow++; // Tambah 1 setiap kali looping

                // }

                // $numrow++; // Tambah 1 setiap kali looping
            }

            echo 'Data pajak berhasil diimport.
              <a href="' .
                base_url() .
                'admin/listing_tkd/update_listing_tkd">Update data</a>';
        } else {
            // Jika proses upload gagal

            echo $upload["error"];
            #$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
            #redirect('admin/swab/error_import');
        }

        exit();
    }

    function list_import()
    {
        $qry = $this->db->get("temp_import");

        $data["import_data"] = $qry->result();
        $this->load->view("admin/listing_tkd/import_data", $data);
    }

    function reset_tdd($id)
    {
        $this->db->where("id", $id);
        $this->db->set("ttd_spj", "");
        $this->db->update("ts_rekap_tkd");

        $this->session->set_flashdata(
            "message",
            "<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset"
        );
        redirect("admin/listing_tkd/view_daftar_ttd");
    }

    function reset_tdd_gaji($id)
    {
        $this->db->where("id", $id);
        $this->db->set("ttd_spj", "");
        $this->db->update("ts_listing_gaji");

        $this->session->set_flashdata(
            "message",
            "<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset"
        );
        redirect("admin/listing_tkd/view_daftar_ttd_gaji");
    }

    function cekSesuai($id)
    {
        $this->db->where("id", $id);
        $this->db->set("status", 1);
        $this->db->update("ts_rekap_tkd");

        $this->session->set_flashdata(
            "message",
            "<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset"
        );
        redirect("admin/listing_tkd/index");
    }

    function update_npwp()
    {
        $new_npwp = $this->input->post("npwp_edit");
        $id_spj = $this->input->post("id_spj");

        $new_npwp = trim($new_npwp);

        $this->db->where("id", $id_spj);
        $this->db->set("npwp", $new_npwp);
        $this->db->update("ts_listing_gaji");

        $qry2 = $this->db->get_where("ts_listing_gaji", ["id" => $id_spj]);
        $row2 = $qry2->result();
        $nama = $row2[0]->nama;

        $this->db->where("nama", $nama);
        $this->db->set("npwp", $new_npwp);
        $this->db->update("ts_rekap_tkd");

        $this->session->set_flashdata(
            "message",
            "<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset"
        );
        redirect("admin/listing_tkd/view_daftar_ttd_gaji");
    }

    function view_daftar_ttd($case = "tkd")
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");

        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));
        // $periode = '2025-02';
        if ($case == "tkd") {
            $table = "ts_rekap_tkd";
        } else {
            $table = "ts_listing_thr";
        }
        $data["data_tkd"] = $this->Laporan_model->getDataListing(
            $periode,
            $table
        );

        $this->load->view("admin/listing_tkd/view_daftar_ttd", $data);
    }

    public function listing_gaji()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["data_gaji"] = $this->Laporan_model->getListingGaji($periode);

        $this->load->view("admin/listing_tkd/view_listing_gaji", $data);
    }

    function view_daftar_ttd_gaji()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["data_gaji"] = $this->Laporan_model->getListingGaji($periode);

        $this->load->view("admin/listing_tkd/view_daftar_ttd_gaji", $data);
    }

    function export_spj_ttd_gaji()
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");
        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        $data["data_tkd"] = $this->Laporan_model->getListingGaji($periode);

        $this->load->view("admin/listing_tkd/export_spj_ttd_gaji", $data);
    }

    function export_spj_ttd($case = "tkd")
    {
        $periode_bulan = $this->session->userdata("periode_bulan");
        $periode_tahun = $this->session->userdata("periode_tahun");

        $periode = $periode_tahun . "-" . $periode_bulan;
        $periode = date("Y-m", strtotime($periode));

        if ($case == "tkd") {
            $table = "ts_rekap_tkd";
        } else {
            $table = "ts_listing_thr";
        }
        //  $data['data_tkd'] = $this->Laporan_model->getListingTKD($periode);

        $data["data_tkd"] = $this->Laporan_model->getDataListing(
            $periode,
            $table
        );

        $this->load->view("admin/listing_tkd/export_spj_ttd", $data);
    }

    function updateNIP()
    {
        $table = "ts_listing_thr";
        $periode = "2025-02";
        $data_tkd = $this->Laporan_model->getDataListing($periode, $table);

        foreach ($data_tkd as $peg) {
            $nama = $peg->nama;
            //$nip = $peg->nip;

            $nip = $this->Pegawai_model->getNIPByName($nama);

            //echo $nama.'-'.$nip.'<br>';

            $this->db->where("nama", $nama);
            $this->db->set("nip", $nip);
            $this->db->update($table);
        }
        echo "SELESAI";
    }

    function getNilaiBPJSBulanLalu()
    {
        $sql =
            "SELECT periode, nip, nama, bpjs FROM ts_rekap_tkd WHERE periode = '2025-11'";
        $qry = $this->db->query($sql);
        $getListData = $qry->result();

        foreach ($getListData as $key => $value) {
            $nip = $value->nip;
            $bpjs = $value->bpjs;

            $sql2 = "UPDATE ts_rekap_tkd SET bpjs = $bpjs WHERE nip = $nip AND periode = '2025-12'";
            $this->db->query($sql2);
        }

        // print_array($getListData);

        echo "Selesai";
    }
}
