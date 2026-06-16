<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Gaji  extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_model');
        $this->load->helper('text');
        $this->Auth_model->cekAuthLogin();
        $this->load->model('Admin_cuti_model', 'acm');
        $this->load->model('RiwayatGaji_model');
    }


    public function index()
    {



        //  print_array($data);
        // $thn_aggrn = 2024;
        // $bulan_tmt = $this->session->userdata('bulan_tmt');

        // if($bulan_tmt==''){
        //     $bulan = '';
        // }else{
        //     $bulan = $bulan_tmt;
        // }

        // $data['pegawai'] = $this->Pegawai_model->getListGajiPegawai();

        $data['pegawai'] = $this->RiwayatGaji_model->getLatestPerPegawai();
        $this->load->view('admin/gaji/main', $data);
    }


    function riwayat_gaji($id_pegawai)
    {
        $data['riwayat_gaji'] = $this->RiwayatGaji_model->getDataGajiPegawai($id_pegawai);
        $data["pegawai"] = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $this->load->view('admin/gaji/riwayat_gaji', $data);
    }

    function kenaikan_gaji()
    {
        $bulan = $this->input->get('bulan');

        $this->db->select('id_pegawai, nama, tgl_masuk');
        $this->db->where('MONTH(tgl_masuk)', $bulan);
        $this->db->where('status_kerja >', 0);
        $this->db->where('jns_pegawai', 'non_pns');
        $qry = $this->db->get('mst_pegawai');
        $row = $qry->result();

        $data['pegawai'] = $row;
        $this->load->view('admin/gaji/kenaikan_gaji', $data);
    }


    public function update_riwayat_gaji()
    {
        $id = $this->input->post('id');

        $data = [
            'masa_kerja_tahun' => $this->input->post('masa_kerja_tahun'),
            'masa_kerja_bulan' => $this->input->post('masa_kerja_bulan'),
            'gaji_pokok'       => $this->input->post('gaji_pokok'),
            'pengali'          => $this->input->post('pengali'),
            'is_proyeksi'          => $this->input->post('is_proyeksi'),
            'total_gaji'       => $this->input->post('gaji_pokok') * $this->input->post('pengali'),

        ];

        $this->db->where('id', $id)
            ->update('tbl_riwayat_gaji_baru', $data);

        redirect($_SERVER['HTTP_REFERER']);
    }



    public function generate_bulanan()
    {
        $tanggalProses = $this->input->post('tanggal_proses');
        $today         = date('Y-m-d');

        if (!$tanggalProses) {
            show_error('Tanggal proses wajib diisi');
        }

        $bulanNow = date('m', strtotime($tanggalProses));
        $tahunNow = date('Y', strtotime($tanggalProses));

        // FLAG PROYEKSI
        $isProyeksi = ($tanggalProses > $today) ? 1 : 0;

        $pegawaiList = $this->Pegawai_model
            ->getPegawaiByBulanMasuk($bulanNow);

        foreach ($pegawaiList as $p) {

            $tahunMasuk = date('Y', strtotime($p->tgl_masuk));
            $bulanMasuk = date('m', strtotime($p->tgl_masuk));

            if ($bulanMasuk != $bulanNow) {
                continue;
            }

            $selisihTahun = $tahunNow - $tahunMasuk;

            // ❌ belum waktunya naik
            if ($selisihTahun < 0 || $selisihTahun % 2 != 0) {
                continue;
            }

            $tmt = $tahunNow . '-' . $bulanMasuk . '-01';

            // ❌ sudah ada
            if ($this->RiwayatGaji_model->exists($p->id_pegawai, $tmt)) {
                continue;
            }

            $masaKerjaTahun = $selisihTahun;
            $masaKerjaBulan = 0;

            $kategori = $this->RiwayatGaji_model
                ->getKategori($masaKerjaTahun);

            if (!$kategori) {
                continue;
            }

            $gajiPokok = $this->RiwayatGaji_model
                ->getGajiPokok(
                    $kategori->id,
                    $p->id_pendidikan,
                    4
                );

            if (!$gajiPokok) {
                continue;
            }

            $data = [
                'id_pegawai'         => $p->id_pegawai,
                'nip'                => $p->nip,
                'tmt'                => $tmt,
                'masa_kerja_tahun'   => $masaKerjaTahun,
                'masa_kerja_bulan'   => 0,
                'id_masa_kerja' => $kategori->masa_kerja,
                'gaji_pokok'         => $gajiPokok->jumlah,
                'pengali'            => $p->pengali,
                'total_gaji'         => $gajiPokok->jumlah * $p->pengali,
                'is_proyeksi'        => $isProyeksi,
                'created_at'         => date('Y-m-d H:i:s')
            ];

            $this->RiwayatGaji_model->insert($data);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }





    function createHistoryGaji()
    {

        $tahun_anggaran = 2024;

        $pegawai = $this->Pegawai_model->getPegawaiForRiwayatGaji($tahun_anggaran);

        echo '<pre>';
        print_r($pegawai);
        echo '</pre>';
        exit;
    }




    function set_session_bulan()
    {
        $bulan_tmt = $this->input->post('bulan');

        $this->session->set_userdata('bulan_tmt', $bulan_tmt);
        return true;
    }

    // function update_gaji_pegawai($id_pegawai, $masa_tahun, $id_pendidikan) {



    //     $id_masa_kerja  = $this->Master_model->getIdMasaKerja($masa_tahun);
    //     $gaji_pokok_mst = $this->Master_model->getGajiPokok($id_masa_kerja, $id_pendidikan);


    //     $this->db->where('id_pegawai', $id_pegawai);
    //     $this->db->set('gaji_pokok', $gaji_pokok_mst);
    //     $this->db->set('last_date_recount', $date1);
    //     $this->db->update('gaji_pegawai');

    //     $this->session->set_flashdata('message',' Data gaji berhasil diupdate');
    //     redirect('admin/gaji/index');
    // }

    function insert_to_gaji($nip)
    {

        $query      = $this->db->select('*')->from('mst_pegawai')->where('nip', $nip)->get();
        $tgl_update = '2025-05-31';
        $thn_update = '2025';

        if ($query->num_rows() > 0) {
            // print_array($query->row());

            $tgl_masuk     =  $query->row()->tgl_masuk;
            $id_pendidikan =  $query->row()->id_pendidikan;
            // $pengali       =  $query->row()->pengali;


            $masa_kerja = hitungMasaKerja($tgl_masuk, $tgl_update);
            $masa_kerja_tahun = $masa_kerja['years'];
            $masa_kerja_bulan = $masa_kerja['months'];


            $kelompok_masa_kerja = kelompok_masa_kerja($tgl_masuk, $tgl_update);
            $id_masa_kerja       = $this->Master_model->getIdMasaKerja($masa_kerja_tahun);

            $gaji_pokok = $this->Master_model->getGajiPokok($id_masa_kerja, $id_pendidikan);
            // echo $gaji_pokok;


            $query2      = $this->db->select('id')->from('ts_gaji_pegawai')->where('nip', $nip)->where('tahun', $thn_update)->get();
            if ($query2->num_rows() > 0) {
                //sudah ada datanya, tinggal update

                $updateData = array(
                    'tgl_masuk' =>  $tgl_masuk,
                    'tgl_update' =>  $tgl_update,
                    'gaji_pokok' => $gaji_pokok,
                    'masa_kerja' => $masa_kerja_tahun . ' thn ' . $masa_kerja_bulan . ' bln',
                    'kelompok_masa_kerja' => $kelompok_masa_kerja,
                );

                $id = $query2->row()->id;


                $this->db->where('id', $id);
                $this->db->update('ts_gaji_pegawai', $updateData);
            } else {
                $newData = array(
                    'nip' => $nip,
                    'tgl_masuk' =>  $tgl_masuk,
                    'tgl_update' =>  $tgl_update,
                    'gaji_pokok' => $gaji_pokok,
                    'masa_kerja' => $masa_kerja_tahun . ' thn ' . $masa_kerja_bulan . ' bln',
                    'kelompok_masa_kerja' => $kelompok_masa_kerja,
                    'tahun' => $thn_update
                );
                $this->db->insert('ts_gaji_pegawai', $newData);
            }



            //print_array($newData);
        }


        redirect('admin/gaji/index');
    }


    function updateTableTsGaji()
    {
        $pegawai = $this->Pegawai_model->getListGajiPegawai();
        foreach ($pegawai as $peg) {

            $id_pegawai = $peg->id_pegawai;
            $nip = $peg->nip;

            $this->Pegawai_model->insert_to_gaji($nip);
        }
    }



    function recount_data_gaji()
    {
        $pegawai        = $this->Pegawai_model->getListPegawai('non_pns', '2024');
        $tgl_recount    = $this->input->post('tgl_recount');
        $date_now       = date('Y-m-d');

        if ($tgl_recount == '') {
            $tgl_recount = $date_now;
        }


        $tgl_recount = format_db($tgl_recount);


        for ($i = 0; $i < count($pegawai); $i++) {
            $nip = $pegawai[$i]->nip;
            $tgl_masuk      = $pegawai[$i]->tgl_masuk;
            $pengali        = $pegawai[$i]->pengali;
            $id_pendidikan  = $pegawai[$i]->id_pendidikan;


            $hitung_masa_kerja  = hitungMasaKerja($tgl_masuk, $tgl_recount);
            $tahun_masa_kerja   = $hitung_masa_kerja['years'];
            $bulan_masa_kerja   = $hitung_masa_kerja['months'];

            $kategori_masa_kerja = kelompok_masa_kerja($tgl_masuk, $tgl_recount);

            $id_masa_kerja = get_id_by_masakerja($kategori_masa_kerja);
            $gaji_pokok    = $this->Master_model->getGajiPokok($id_masa_kerja, $id_pendidikan);
            $tahun         = date('Y');

            $tkd_pokok = $gaji_pokok * $pengali;

            $dataGaji = array(
                'nip' => $nip,
                'tmt' => $tgl_masuk,
                'masa_kerja_tahun' => $tahun_masa_kerja,
                'masa_kerja_bulan' => $bulan_masa_kerja,
                'kategori_masa_kerja' => $kategori_masa_kerja,
                'gaji_pokok' => $gaji_pokok,
                'pengali' => $pengali,
                'tkd_pokok' => $tkd_pokok,
                'tahun' =>  $tahun,
                'update_on' => $tgl_recount
            );

            $cekDataGaji = $this->Pegawai_model->cekDataGajipegawai($nip, $tahun);
            if ($cekDataGaji == 0) {
                $this->db->insert('tbl_riwayat_gaji', $dataGaji);
            } else {
                $id = $cekDataGaji;
                $this->db->where('id', $id);
                $this->db->update('tbl_riwayat_gaji', $dataGaji);
            }
        }

        redirect('admin/gaji/index');
        // print_array($pegawai);

    }



    function recount()
    {
        $tanggal    = $this->input->post('tgl_recount');
    }




    function all_data()
    {
        $thn_aggrn = date('Y');

        $data['pegawai'] = $this->Pegawai_model->getListPegawai('non_pns', $thn_aggrn);

        $this->load->view('admin/gaji/all_data', $data);
    }


    function filter_tmt()
    {

        $bulan_tmt = $this->input->post('bulan_tmt');
        $ganjil_genap = $this->input->post('ganjil_genap');

        if ($ganjil_genap == 1) {
            $gg = 1;
        } else {
            $gg = 0;
        }

        $this->session->set_userdata('bulan_tmt', $bulan_tmt);
        $this->session->set_userdata('ganjil_genap', $gg);
        redirect('admin/gaji/index');
    }




    function data_gaji()
    {
        $thn_anggrn  = 2024;

        $data['pegawai'] = $this->Pegawai_model->getListPegawai('non_pns', $thn_anggrn);
        $this->load->view('admin/gaji/update_gaji', $data);
    }

    function update_gaji_all()
    {
        #print_array($this->input->post());

        $id_pegawai = $this->input->post('id_pegawai');
        $gaji_pokok = $this->input->post('gaji_pokok');
        $pengkalian = $this->input->post('pengkalian');
        $bpjs = $this->input->post('bpjs');
        $bpjs_tk = $this->input->post('bpjs_tk');
        $pph21 = $this->input->post('pph21');


        // $numRow = count($id_pegawai);

        // echo $numRow;
        for ($i = 0; $i < count($id_pegawai); $i++) {


            $data = array(
                'gaji_pokok' => $gaji_pokok[$i],
                'pengkalian' =>  $pengkalian[$i],

            );

            $this->db->where('id_pegawai', $id_pegawai[$i]);
            $this->db->update('gaji_pegawai', $data);
        }

        //exit;
        $pesan =  createMessageInfo('Data gaji berhasil diupdate');
        $this->session->set_flashdata('message', $pesan);
        redirect('admin/gaji/data_gaji');
    }




    function updatePengali()
    {
        $pegawai    = $this->Pegawai_model->getListPegawai('non_pns', '2024');

        foreach ($pegawai as $peg) {
            $id_pegawai = $peg->id_pegawai;
            $query      = $this->db->select('pengkalian')->from('gaji_pegawai')->where('id_pegawai', $id_pegawai)->get();

            if ($query->num_rows() > 0) {
                $pengkalian =  $query->row()->pengkalian;
                //echo $pengkalian.'<br>';
                $this->db->where('id_pegawai', $id_pegawai);
                $this->db->set('pengali', $pengkalian);
                $this->db->update('mst_pegawai');
            }
        }


        echo 'berhasil';
        exit;
    }

    function recount_process()
    {
        $tgl_recount = $this->session->userdata('tgl_recount');
        $date1 = format_db($tgl_recount);

        $thn_aggrn  = date('Y');
        $pegawai    = $this->Pegawai_model->getListPegawai('non_pns', $thn_aggrn);


        $no = 1;
        foreach ($pegawai as $peg) {
            $id_pegawai = $peg->id_pegawai;
            $id_jabatan = $peg->id_jabatan;
            $tmt = $peg->tgl_masuk;
            $id_pendidikan = $peg->id_pendidikan;
            $pengkalian = $peg->pengkalian;
            $gaji_pokok = $peg->gaji_pokok;

            $masa_kerja = hitungMasaKerja($date1, $tmt);
            #print_r($masa_kerja);
            $masa_tahun = $masa_kerja['years'];
            $masa_bulan = $masa_kerja['months'];
            $masa_hari  = $masa_kerja['days'];


            $id_masa_kerja  = $this->Master_model->getIdMasaKerja($masa_tahun);
            $gaji_pokok_mst = $this->Master_model->getGajiPokok($id_masa_kerja, $id_pendidikan);


            #echo $tmt.'--'.$id_masa_kerja.'<br>';
            $this->db->where('id_pegawai', $id_pegawai);
            $this->db->set('gaji_pokok', $gaji_pokok_mst);
            $this->db->set('last_date_recount', $date1);
            $this->db->update('gaji_pegawai');


            $updatePegawai  = array(
                'kategori_masa_kerja' => $id_masa_kerja,
                'masa_kerja' => $masa_tahun . '-' . $masa_bulan . '-' . $masa_hari
            );

            $this->db->where('id_pegawai', $id_pegawai);
            $this->db->update('mst_pegawai', $updatePegawai);

            $nama = $peg->nama;


            // echo '<tr>
            //             <td>'.$nama.'</td>
            //             <td>'.$tmt.'</td>
            //             <td>'.$masa_tahun.'-'.$masa_bulan.'-'.$masa_hari.'</td>
            //             <td>'.$gaji_pokok_mst.'</td>
            //       </tr>';


            $no += 1;
        }




        $pesan =  createMessageInfo('Data gaji berhasil direcount');
        $this->session->set_flashdata('message', $pesan);
        redirect('admin/gaji/all_data');
    }

    function data_bpjs_pajak()
    {
        $thn_anggrn  = 2024;

        $this->db->limit(50, 0);
        $this->db->where('jns_pegawai', 'non_pns');
        $this->db->where('tahun_anggaran', $thn_anggrn);
        $this->db->where('status_kerja >', 0);
        $this->db->select('mst_pegawai.*, gaji_pegawai.*');
        $this->db->from('mst_pegawai');
        $this->db->join('gaji_pegawai', 'mst_pegawai.id_pegawai = gaji_pegawai.id_pegawai');
        $qry = $this->db->get();

        $data['pegawai'] = $qry->result();
        //$data['pegawai'] = $this->Pegawai_model->getListPegawai('non_pns', $thn_anggrn);


        $this->load->view('admin/gaji/update_bpjs', $data);
    }

    function update_data_bpjs_tk()
    {

        // print_array($this->input->post());
        $id_pegawai = $this->input->post('id_pegawai');
        $bpjs_tk = $this->input->post('bpjs_tk');

        for ($i = 0; $i < count($id_pegawai); $i++) {

            $this->db->where('id_pegawai', $id_pegawai[$i]);
            $this->db->set('bpjs_tk', $bpjs_tk[$i]);
            $this->db->update('gaji_pegawai');

            //print_array($data);
        }


        //exit;
        $pesan =  createMessageInfo('Data BPJS dan Pajak berhasil diupdate');
        $this->session->set_flashdata('message', $pesan);
        redirect('admin/gaji/data_bpjs_pajak');
    }

    function update_bpjs_pajak()
    {
        //print_array($this->input->post());

        $id_pegawai = $this->input->post('id_pegawai');
        $bpjs_kes = $this->input->post('bpjs_kes');
        $bpjs_tk = $this->input->post('bpjs_tk');
        $pph21 = $this->input->post('pph21');


        // $numRow = count($id_pegawai);

        // echo $numRow;
        for ($i = 0; $i < count($id_pegawai); $i++) {


            $data = array(
                'bpjs_kes' =>  $bpjs_kes[$i],
                'bpjs_tk' =>  $bpjs_tk[$i],
                'pph21' =>  $pph21[$i],

            );

            $this->db->where('id_pegawai', $id_pegawai[$i]);
            $this->db->update('gaji_pegawai', $data);

            //print_array($data);
        }


        //exit;
        $pesan =  createMessageInfo('Data BPJS dan Pajak berhasil diupdate');
        $this->session->set_flashdata('message', $pesan);
        redirect('admin/gaji/update_bpjs_pajak');
    }



    function import_bpjs()
    {
        $data = array(); // Buat variabel $data sebagai array

        $date_now  = date('Ymd_Hi');
        $file_name = $date_now;
        $path = 'bpjs_tk';
        $jns = $this->input->post('jns');

        $sql = "DELETE FROM temp_import ";
        $this->db->query($sql);

        $ta = 2024;
        $upload = $this->Master_model->upload_file($file_name, $path);

        if ($upload['result'] == "success") { // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load('uploads/' . $path . '/' . $file_name . '.xlsx'); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data_insert = array();
            $numrow = 1;
            $num = 0;


            foreach ($sheet as $row) {

                if ($numrow > 1) {
                    // Kita push (add) array data ke variabel data
                    #print_array($row);
                    $no_ktp      = $row['A'];
                    $nama      = $row['B'];
                    $jumlah      = $row['C'];

                    echo $nama;

                    $pegawaiIDetail          = $this->Pegawai_model->getPegawaiByNama($nama);
                    // $nip          = $this->Pegawai_model->cekNoKTP($no_ktp);


                    //print_array($pegawaiIDetail);
                    if (empty($pegawaiIDetail)) {
                        $statusData = 'Gagal';
                        $keterangan = 'NIP tidak ditemukan';
                        $jumlah_bpjs = 0;
                        $jumlah_import = 0;
                        $updateColomn = '';
                    } else {
                        $jumlah_import =  str_replace(",", "", $jumlah);
                        $jumlah_import =  str_replace("Rp", "", $jumlah_import);
                        $id_pegawai = $pegawaiIDetail[0]->id_pegawai;
                        $nip = $pegawaiIDetail[0]->nip;

                        //$id_pegawai = $this->Pegawai_model->getIDpegawaiByNIP($nip, $ta);

                        $dataGaji   = $this->Pegawai_model->getDataGajiPegawai($id_pegawai);

                        $bpjs_kes = $dataGaji[0]->bpjs_kes;
                        $bpjs_tk = $dataGaji[0]->bpjs_tk;
                        $pph21 = $dataGaji[0]->pph21;


                        if ($jns == 1) {
                            //bpjs kesehatan
                            if ($jumlah_import == $bpjs_kes) {
                                $status_jumlah = 'Jumlah sama';
                            } else {
                                $status_jumlah = 'Jumlah Tidak  sama';
                            }

                            $updateColomn = 'bpjs_kes';
                            $jumlah_bpjs =  $bpjs_kes;
                        } else if ($jns == 2) {
                            //bpjs ketenagakerjaan
                            if ($jumlah_import == $bpjs_tk) {
                                $status_jumlah = 'Jumlah sama';
                            } else {
                                $status_jumlah = 'Jumlah Tidak  sama';
                            }

                            $updateColomn = 'bpjs_tk';
                            $jumlah_bpjs =  $bpjs_tk;
                        } else {
                            //PAJAK
                            if ($jumlah_import == $pph21) {
                                $status_jumlah = 'Jumlah sama';
                            } else {
                                $status_jumlah = 'Jumlah Tidak  sama';
                            }

                            $updateColomn = 'pph21';
                            $jumlah_bpjs =  $pph21;
                        }

                        $statusData = 'Berhasil';
                        $keterangan = $status_jumlah;

                        //echo $bpjs_tk;
                    }

                    $data_insert[] = array(
                        'nik' => $nip,
                        'nama' => $nama,
                        'status' =>  $statusData,
                        'keterangan' => $keterangan,
                        'nilai_lama' => $jumlah_bpjs,
                        'nilai_baru' => $jumlah_import,
                        'import_file' => $updateColomn
                    );

                    $numrow++; // Tambah 1 setiap kali looping



                }

                $numrow++; // Tambah 1 setiap kali looping
            }

            //	print_array($data_insert);

            $this->db->insert_batch('temp_import', $data_insert);
            //	exit;
            redirect('admin/gaji/preview_import');
        } else { // Jika proses upload gagal

            echo  $upload['error'];
            #$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
            #redirect('admin/swab/error_import');
        }

        exit;
    }

    function preview_import()
    {

        $qry = $this->db->get('temp_import');


        $data['import'] = $qry->result();
        $this->load->view('admin/gaji/summary_import', $data);
    }


    function submit_import()
    {

        $qry = $this->db->get('temp_import');
        $import = $qry->result();

        for ($i = 0; $i < count($import); $i++) {
            $nip = $import[$i]->nik;

            //$nip = $this->Pegawai_model->cekNoKTP($nik);


            $import_file = $import[$i]->import_file;
            $nilai_baru = $import[$i]->nilai_baru;


            $id_pegawai = $this->Pegawai_model->getIDpegawaiByNIP($nip, '2024');


            $this->db->where('id_pegawai', $id_pegawai);
            $this->db->set($import_file, $nilai_baru);
            $this->db->update('gaji_pegawai');
        }


        echo 'Success! Data berhasil diimport';
    }

    function import_pajak()
    {
        $data = array(); // Buat variabel $data sebagai array

        $date_now  = date('Ymd_Hi');
        $file_name = $date_now;
        $path = 'pajak';

        $ta = 2024;
        $upload = $this->Master_model->upload_file($file_name, $path);

        if ($upload['result'] == "success") { // Jika proses upload sukses
            // Load plugin PHPExcel nya
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load('uploads/' . $path . '/' . $file_name . '.xlsx'); // Load file yang tadi diupload ke folder excel
            $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = array();
            $numrow = 1;
            $num = 0;



            echo '<table border="1" width="50%">
                    <tr>
                     
                      <th rowspan="2">Nama</th>
                      <th rowspan="2">Status Update</th>
                      <th rowspan="2">Keterangan</th>
                      <th colspan="2">Jumlah Potongan BPJS TK</th>
                    </tr>
                    <tr>
                      <th>Lama</th>
                      <th>Baru</th>
                    </tr>';

            foreach ($sheet as $row) {

                if ($numrow > 1) {
                    // Kita push (add) array data ke variabel data


                    #print_array($row);

                    $nama      = $row['A'];
                    $jumlah      = $row['B'];

                    $id_pegawai  = $this->Pegawai_model->getIDpegawaiByName($nama);
                    if ($id_pegawai == 0) {
                        echo '<tr>
                                            <td>' . $nama . ' </td>
                                            <td>Gagal</td> 
                                            <td></td>
                                            <td></td>
                                            <td>NIP tidak ditemukan</td>
                                      </tr>';
                    } else {
                        $pph21 =  str_replace(",", "", $jumlah);

                        $dataGaji   = $this->Pegawai_model->getDataGajiPegawai($id_pegawai);
                        $pph21_db   = trim($dataGaji[0]->pph21);

                        if ($pph21 == $pph21_db) {
                            $status_jumlah2 = 'Jumlah sama';
                        } else {
                            $status_jumlah2 = 'Jumlah Tidak  sama';
                        }
                        echo '<tr>
                                            <td>' . $nama . ' </td>
                                            <td>Berhasil</td>      
                                            <td>' . $pph21_db . '</td>
                                            <td>' . $pph21 . '</td>
                                            <td>' . $status_jumlah2 . '</td>
                                    </tr>';


                        $this->db->where('id_pegawai', $id_pegawai);
                        $this->db->set('pph21', $pph21);
                        $this->db->update('gaji_pegawai');
                        //echo $bpjs_tk;
                    }



                    $numrow++; // Tambah 1 setiap kali looping

                }

                $numrow++; // Tambah 1 setiap kali looping
            }

            echo '</table>';


            echo '<h3>Data BPJS TK berhasil diupdate</h3>';
        } else { // Jika proses upload gagal

            echo  $upload['error'];
            #$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
            #redirect('admin/swab/error_import');
        }

        exit;
    }


    function update_gaji_pokok_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $gaji = $this->input->post('gaji');

        print_array($this->input->post('id_pegawai'));
        for ($i = 0; $i < count($id_pegawai); $i++) {

            $this->db->where('id_pegawai', $id_pegawai[$i]);
            $this->db->set('gaji_pokok', $gaji[$i]);
            $this->db->update('gaji_pegawai');
        }


        redirect('dashboard/admin_dashboard');
    }

    function test()
    {

        $qry = $this->db->get('tbl_import');
        $row = $qry->result();

        $data['import'] = $row;
        $this->load->view('admin/gaji/test', $data);
    }

    function update_bpjstk_perpegawai($id_pegawai, $jumlah)
    {
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->set('bpjs_tk', $jumlah);
        $this->db->update('gaji_pegawai');

        redirect('admin/gaji/test');
    }


    function update_all_bpjstk()
    {
        $qry = $this->db->get('tbl_import');
        $row = $qry->result();


        foreach ($row as $data) {

            $nik = $data->nik;
            $nama = $data->nama;
            $jumlah = $data->jumlah;

            $id_pegawai = $this->Pegawai_model->getIDpegawaiByName($nama);
            $data_gaji = $this->Pegawai_model->getDataGajiPegawai($id_pegawai);


            $bpjs_tk = $data_gaji[0]->bpjs_tk;

            if ($bpjs_tk != $jumlah) {
                $this->db->where('id_pegawai', $id_pegawai);
                $this->db->set('bpjs_tk', $jumlah);
                $this->db->update('gaji_pegawai');
            }
        }

        redirect('admin/gaji/test');
    }
}
