<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Laporan extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();
        $this->load->model('Laporan_model');
		//$this->Auth_model->cekAuthLogin();
         $this->load->model('Admin_cuti_model', 'acm');
		$this->load->helper('text');


	}


    function index(){
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $data['pegawai'] = $this->Laporan_model->getListPegawai();

        $this->load->view('admin/laporan/index', $data);
       // $this->load->view('admin/laporan/dashboard_laporan', $data);
    }

    function create_session_absensi($id_pegawai, $pin, $periode){

        $xplod = explode('-', $periode);
        $tahun = $xplod[0];
        $bulan = $xplod[1];

       $this->session->set_userdata('periode_tahun', $tahun);
       $this->session->set_userdata('periode_bulan', $bulan);

       redirect('admin/presensi/lihat_absensi_pegawai/'.$id_pegawai.'/'.$pin);

    }

    function filter_data(){

        $bulan_start = $this->input->post('bulan_start');
        $bulan_end = $this->input->post('bulan_end');
        $tahun = $this->input->post('tahun');
        $jenis = $this->input->post('jenis');

        $new_session = array(
            'bulan_start' => $bulan_start,
            'bulan_end' => $bulan_end,
            'tahun' => $tahun,
            'jns_absensi' => $jenis
        );

        #print_array($new_session);
        $this->session->set_userdata($new_session);



    }



    function update_tkd_pegawai($nip, $tkd_id, $periode) {

        $getDataCapaian   = $this->Kinerja_model->getDataCapaian($nip, $periode);
        if (!empty($getDataCapaian)) {
         $totalCapaian = $getDataCapaian[0]->total_capaian;
        }else{
         $totalCapaian = 0;
        }



        $detailTKD = $this->Laporan_model->getDetailRekapTKD($tkd_id);
        $tkd_pokok    = $detailTKD[0]->tkd_pokok;
        $pph21    = $detailTKD[0]->pph21;
        $bpjs_kes = $detailTKD[0]->bpjs;
        $bpjs_tk  = $detailTKD[0]->bpjs_tk;

        $bruto       = ($totalCapaian*$tkd_pokok)/100;
       // $bruto       = $bruto*0.75;


        $potongan = $pph21+$bpjs_kes+$bpjs_tk;
        $thp = $bruto-$potongan;



        $newData = array(

            'capaian' => $totalCapaian,
            'bruto' =>$bruto,
            'thp' => $thp,
        );

        $this->db->where('id', $tkd_id);
        $this->db->update('ts_rekap_tkd', $newData);

        redirect('admin/listing_tkd/index');

    }



    function update_listing_tkd(){


        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $listing_tkd  = $this->Laporan_model->getListingTKD($periode);

       // print_array($listing_tkd);

        foreach ($listing_tkd as $tkd) {
                $id = $tkd->id;
                $bruto = $tkd->bruto;
                $pph21 = $tkd->pph21;
                $bpjs_kes = $tkd->bpjs;
                $bpjs_tk = $tkd->bpjs_tk;

                $potongan = $pph21+$bpjs_kes+$bpjs_tk;
                $thp = $bruto-$potongan;

                $this->db->where('id', $id);
                $this->db->set('thp', $thp);
                $this->db->update('ts_rekap_tkd');




        }

        redirect('admin/listing_tkd/index');
    }



    function laporan_pegawai(){

        $this->db->order_by('tgl_masuk', 'ASC');
       // $qry = $this->db->get_where('mst_pegawai', array('jns_pegawai'=>'non_pns', 'tgl_masuk <=' => '2025-01-01'));
        $this->db->order_by('mst_pegawai.id_jabatan', 'ASC');
        $this->db->order_by('mst_pegawai.tgl_masuk', 'ASC');
        $this->db->where('jns_pegawai', 'non_pns');
       // $this->db->where('tgl_masuk <=', '2025-01-01');
        $this->db->where('tahun_anggaran', '2024');
        $this->db->select('mst_pegawai.*,mst_jabatan.nama AS jabatan, detail_pegawai.tempat_lahir, detail_pegawai.tgl_lahir, gaji_pegawai.*,ts_listing_thr.total AS total_thr, ts_listing_gaji13.total, mst_pendidikan.pendidikan, tbl_riwayat_gaji.gaji_pokok, tbl_riwayat_gaji.pengali');
        $this->db->from('mst_pegawai');
        $this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
        $this->db->join('detail_pegawai', 'mst_pegawai.nip = detail_pegawai.nip', 'left');
        $this->db->join('gaji_pegawai', 'mst_pegawai.id_pegawai = gaji_pegawai.id_pegawai');
        $this->db->join('mst_pendidikan', 'mst_pegawai.id_pendidikan = mst_pendidikan.id', 'left');
         $this->db->join('tbl_riwayat_gaji', 'mst_pegawai.nip = tbl_riwayat_gaji.nip', 'left');
        $this->db->join('ts_listing_gaji13', 'detail_pegawai.no_rekening = ts_listing_gaji13.no_rekening', 'left');
        $this->db->join('ts_listing_thr', 'detail_pegawai.no_rekening = ts_listing_thr.no_rekening', 'left');



        $qry = $this->db->get();

        //echo $this->db->last_query();
        
        $data['datalist'] = $qry->result(); 
        $this->load->view('admin/laporan/laporan_pegawai', $data);
    }

    function update_rekap_tkd( $periode ){

        //$periode          = $this->input->post('periode');

        $explod           = explode('-',$periode);
        $tahun            = $explod[0];
        $bulan            = $explod[1];
        $thn_anggrn       = 2024;
        $jumlahHariKerja  = $this->Master_model->getMenitEfektifBulan($bulan, $tahun);
        $waktu_efektif    = $jumlahHariKerja*300;


        $getListData      = $this->Laporan_model->getListingTKD($periode);
        if (!empty($getListData)) {
            //klo udah ada datanya
            foreach ($getListData as $tkd) {
                        $id       = $tkd->id;
                        $bruto    = $tkd->bruto;
                        $nama    = $tkd->nama;
                        $nip      = $tkd->nip;
                        $pph21    = $tkd->pph21;
                        $bpjs_kes = $tkd->bpjs;
                        $bpjs_tk  = $tkd->bpjs_tk;

                        $id_pegawai        = $this->Pegawai_model->getIDpegawaiByName($nama);

                       // $tkd_pokok        = $this->Pegawai_model->getTKDPokok($id_pegawai);

                        // $getDataCapaian   = $this->Kinerja_model->getDataCapaian($nip, $periode);
                        // if (!empty($getDataCapaian)) {
                        //     $totalCapaian = $getDataCapaian[0]->total_capaian;
                        // }else{
                        //     $totalCapaian = 0;
                        // }

                        //$bruto = ($tkd_pokok*$totalCapaian)/100;
                        $potongan = $pph21+$bpjs_kes+$bpjs_tk;
                        $thp = $bruto-$potongan;

                        // $updateData = array(
                        //     'tkd_pokok' => $tkd_pokok,
                        //     'capaian' => $totalCapaian,
                        //     'bruto' => $bruto,
                        //     'thp' => $thp,
                        //     'update_on' => date('Y-m-d H:i:s')
                        // );

                        $updateData = array(
                            'thp' => $thp,
                            'update_on' => date('Y-m-d H:i:s')
                        );

                        $this->db->where('id', $id);
                        $this->db->update('ts_rekap_tkd', $updateData);

                       //print_array($updateData);

                }
        }else{
            //klo periode itu masih kosong datanya
        }
        //print_array($getListData);

        redirect('admin/listing_tkd/index');
    }




    function detailCapaianPegawai(){
        $nip = $this->input->post('nip');

        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $ta = $this->session->userdata('periode_tahun');

        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $id_pegawai               =   $this->Pegawai_model->getIDpegawaiByNIP($nip, $ta);
        $data['detail_pegawai']   = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $data['dataRekap']        = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
        $data['dataTKD']          = $this->Laporan_model->getRekapTKDPegawai($nip, $periode);

        $this->load->view('admin/listing_tkd/detail_capaian_pegawai', $data);
    }


	public function ajaxDetailCapaian()
	{
			$dataPrse  = $this->input->post('data');
			$expld = explode("/", $dataPrse);
			$id_pegawai = $expld[0];
			$nip    = $expld[1];
			$periode    = $expld[2];

			$data['pegawai']   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
			$data['dataRekap']        = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
			$data['dataTKD']          = $this->Laporan_model->getRekapTKDPegawai($nip, $periode);
			$data['dataCapaian']          = $this->Kinerja_model->getDataCapaian($nip, $periode);
			$this->load->view('admin/listing_tkd/detail_capaian_pegawai', $data);
			//print_array($detail_pegawai);
	}


	function detail_capaian($id_pegawai){
		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		$periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

		$data['detail_pegawai']   = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$data['master_cuti'] = $this->Master_model->getlistCuti();
		$this->load->view('admin/capaian_kinerja/detail_capaian', $data);
	}

    function updateEditListingGaji()  {
        $no_ktp = $this->input->post('nik');


        $dataPegawai = $this->Pegawai_model->getPegawaiByNIK($no_ktp);


        // ///echo $npwp;
        if(!empty($dataPegawai)){

            $nip = $dataPegawai[0]->nip;
            $no_rekening = $dataPegawai[0]->no_rekening;
            $nama = $dataPegawai[0]->nama;

            $this->db->where('npwp', $no_ktp);
            $this->db->set('no_rekening', $no_rekening);
            $this->db->set('nama', $nama);
            $this->db->update('ts_listing_gaji');
            echo 'berhasil!! data berhasil diupdate';
        }else{
            echo 'Gagal!! data tidak ditemukan';
        }


    }


    function import_gaji()  {
        echo form_open_multipart(base_url() . 'admin/listing_tkd/import_gaji_process');

        echo ' <strong> file (*.xls) : </strong>
                                        <input name="file" type="file"><br>
                                      <br>
                                       <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Import File</button>';


        echo form_close();


    }
    function import_gaji_process()  {


        $date_now  = date('Ymd_Hi');
        $file_name = $date_now;

        $jns_file = $this->input->post('jns_file');
        $path = 'pajak';

        $upload = $this->Master_model->upload_file($file_name, $path);




        if($upload['result'] == "success"){ // Jika proses upload sukses
          // Load plugin PHPExcel nya
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';

          $excelreader = new PHPExcel_Reader_Excel2007();
          $loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
          $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

          $periode = '2025-05';
          // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
          $data = array();
          $numrow = 1;
          $num = 0;
          foreach($sheet as $row){


            //print_array($row);
              if($numrow > 1){

                      $nama         = $row['B'];
                      $jabatan      = $row['C'];

                      $tgl_tmt         = $row['D'];
                      $bln_tmt      =  getNoBulan($row['E']);
                      $thn_tmt         = $row['F'];

                      $status_kawin = $row['G'];

                      $npwp         = $row['H'];
                      $gp      = $row['I'];

                      $tunj_suami         = $row['J'];
                      $tunj_anak1      = $row['K'];
                      $tunj_anak2      = $row['L'];

                      $bruto  = $row['N'];
                      $ptkp  = $row['P'];
                      $tarif_ter  = $row['Q'];
                      $pph  = $row['R'];
                      $netto  = $row['S'];
                      $no_rekening  = $row['U'];
                    //   $jumlah      = str_replace("Rp","", $jumlah);
                    //   $jumlah      = str_replace(",","", $jumlah);

                        $tmt = $tgl_tmt.'-'.$bln_tmt.'-'.$thn_tmt;
                        $tmt = format_db($tmt);

                        $pajak = clear_tags($pph);
                        $pajak      = str_replace("Rp","", $pajak);

                        $npwp = str_replace(",","", $npwp);

                      if($nama != ''){
                          $data[] = array(
                              'periode' => $periode,
                              'nama' => $nama,
                              'jabatan'=> $jabatan,
                              'tmt' => $tmt,
                              'status_kawin' => $status_kawin,
                              'npwp' => $npwp,
                              'gaji_pokok'=> clear_tags($gp),
                              'tunj_suami' => clear_tags($tunj_suami),
                              'tunj_anak1' => clear_tags($tunj_anak1),
                              'tunj_anak2' => clear_tags($tunj_anak2),
                              'bruto'=> clear_tags($bruto),
                              'ptkp' => $ptkp,
                              'tarif_ter' => $tarif_ter,
                              'pajak' => $pajak,
                              'netto'=> clear_tags($netto),
                              'no_rekening' => $no_rekening,
                              'ttd_spj' => '',


                          );

                      }



                  $numrow++; // Tambah 1 setiap kali looping


              }



              $numrow++; // Tambah 1 setiap kali looping
          }


         // print_array($data);
       $this->db->insert_batch('ts_listing_gaji', $data);

       echo 'berhasil';
      // redirect('admin/swab/error_import');

        }else{ // Jika proses upload gagal

          echo  $upload['error'];
          #$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
          #redirect('admin/swab/error_import');
        }



  }




    function importAll()  {
        echo form_open_multipart(base_url() . 'admin/listing_tkd/import_thr');

        echo ' <strong> file (*.xls) : </strong>
                                        <input name="file" type="file"><br>
                                      <br>
                                       <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Import File</button>';


        echo form_close();


    }

 function import_thr()  {
        $date_now  = date('Ymd_Hi');
        $file_name = $date_now;

        $jns_file = $this->input->post('jns_file');
        $path = 'pajak';

        $upload = $this->Master_model->upload_file($file_name, $path);

        $periode = '2025-01';


        if($upload['result'] == "success"){ // Jika proses upload sukses
          // Load plugin PHPExcel nya
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';

          $excelreader = new PHPExcel_Reader_Excel2007();
          $loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
          $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);


          // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
          $data = array();
          $numrow = 1;
          $num = 0;
          foreach($sheet as $row){



                    if($numrow > 0){
                            $nama =  $row['B'];
                            $jabatan =  $row['C'];
                            $tmt_tgl =  $row['D'];
                            $tmt_bln =  $row['E'];
                            $tmt_thn  =  $row['F'];
                            $status_kawin    =  $row['G'];
                            $npwp    =  $row['H'];
                            $gp     =  $row['I'];
                            $tunj_suami  =  $row['J'];
                            $tunj_anak1    =  $row['K'];
                            $tunj_anak2    =  $row['L'];
                            $thr_gaji    =  $row['M'];
                            $thr_tkd    =  $row['N'];
                            $total    =  $row['O'];
                            $no_rekening    =  $row['Q'];


                            $tmt = $tmt_tgl.' '.$tmt_bln.' '.$tmt_thn;


                            $newData[] = array(
                                'periode' => '2025-02',
                                'nama' => $nama,
                                'jabatan' => $jabatan,
                                'tmt' => $tmt,
                                'status_kawin' => $status_kawin,
                                'npwp' => clear_tags($npwp),
                                'gaji_pokok' => clear_tags($gp),
                                'tunj_suami' => clear_tags($tunj_suami),
                                'tunj_anak1' =>clear_tags($tunj_anak1),
                                'tunj_anak2' =>clear_tags($tunj_anak2),
                                'thr_gaji' =>clear_tags($thr_gaji),
                                'thr_tkd' => clear_tags($thr_tkd),
                                'total' => clear_tags($total),
                                'no_rekening' => $no_rekening,
                                'ttd_spj' => '',
                            );


                        }
              } //close loop

              //print_array($newData);

              $this->db->insert_batch('ts_listing_thr', $newData);

          }


        echo 'berhasil';
    }


    function import_tkd_full()  {
        $date_now  = date('Ymd_Hi');
        $file_name = $date_now;

        $jns_file = $this->input->post('jns_file');
        $path = 'pajak';

        $upload = $this->Master_model->upload_file($file_name, $path);

        $periode = '2025-03';


        if($upload['result'] == "success"){ // Jika proses upload sukses
          // Load plugin PHPExcel nya
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';

          $excelreader = new PHPExcel_Reader_Excel2007();
          $loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
          $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);


          // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
          $data = array();
          $numrow = 1;
          $num = 0;
          foreach($sheet as $row){


            //print_array($row);
              if($numrow > 0){
                        $nama =  $row['B'];
                        $tkd_pokok =  $row['E'];
                        $capaian  =  $row['F'];
                        $bruto    =  $row['G'];
                        $pph21    =  $row['H'];
                        $bpjs     =  $row['I'];
                        $bpjs_tk  =  $row['J'];
                        $total    =  $row['K'];


                        $tkd_pokok = clear_tags($tkd_pokok);
                        $bruto = clear_tags($bruto);
                        $thp = clear_tags($total);
                        $pph21 = clear_tags($pph21);
                        $bpjs = clear_tags($bpjs);
                        $bpjs_tk = clear_tags($bpjs_tk);

                        $capaian = str_replace(",",".", $capaian);
                        //$potongan = $pph21+$bpjs+$bpjs_tk;
                      // $thp = $bruto-$potongan;



                        $cekTKD = $this->Laporan_model->cekDatalistingTKD($nama, $periode);
                        if(!empty($cekTKD) ){
                            $id = $cekTKD[0]->id;

                            echo $id;

                            $data = array(
                                'tkd_pokok' => $tkd_pokok,
                                'bruto'=> $bruto,
                                'capaian'=> $capaian,
                                'pph21'=> $pph21,
                                'bpjs'=> $bpjs,
                                'thp'=> $thp

                            );

                        //    $this->db->where('id', $id);
                        //    $this->db->update('ts_rekap_tkd', $data);

                            print_array($data);


                        }



              }



          }

        }

        echo 'Selesai';
    }




    function import_data()  {


		  $date_now  = date('Ymd_Hi');
		  $file_name = $date_now;

          $jns_file = $this->input->post('jns_file');
          $path = 'pajak';

		  $upload = $this->Master_model->upload_file($file_name, $path);


          $sql = 'DELETE FROM temp_import';
          $this->db->query($sql);


		  if($upload['result'] == "success"){ // Jika proses upload sukses
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';

			$excelreader = new PHPExcel_Reader_Excel2007();
			$loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
			$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);


			// Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
			$data = array();
			$numrow = 1;
            $num = 0;
			foreach($sheet as $row){



                if($numrow > 0){




                    if($jns_file=='pph21'){


                        $nama       = $row['A'];
                        $jumlah      = $row['B'];

                        $jumlah      = str_replace("Rp","", $jumlah);
                        $jumlah      = str_replace(",","", $jumlah);

                        $nip = $this->Pegawai_model->getNIPByName($nama);

                        $data[] = array(
                            'nik' =>  $nip,
                            'nama' => $nama,
                            'jumlah'=> $jumlah,
                            'import_file' => $jns_file

                        );
                    }else{

                        $nik       = $row['A'];
                        $nama       = $row['B'];
                        $jumlah      = $row['C'];

                        $jumlah      = str_replace("Rp","", $jumlah);
                        $jumlah      = str_replace(",","", $jumlah);

                        if($nik != ''){
                            $data[] = array(
                                'nik' => $nik,
                                'nama' => $nama,
                                'jumlah'=> $jumlah,
                                'import_file' => $jns_file

                            );

                        }


                    }




                    $numrow++; // Tambah 1 setiap kali looping


                }

                $numrow++; // Tambah 1 setiap kali looping
            }

            $this->db->insert_batch('temp_import', $data);

             redirect('admin/listing_tkd/list_import');
           // print_array($data);


             // echo 'Data pajak berhasil diimport.';

		  }else{ // Jika proses upload gagal

            echo  $upload['error'];
			#$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
			#redirect('admin/swab/error_import');
		  }



    }



    function submit_import(){

          $qry         = $this->db->get('temp_import');
          $import_data = $qry->result();
          //$periode     = '2024-10';

          $periode_bulan = $this->session->userdata('periode_bulan');
          $periode_tahun = $this->session->userdata('periode_tahun');
          $periode = $periode_tahun.'-'.$periode_bulan;
          $periode = date('Y-m', strtotime($periode));

         // $periode = '2024-10';


          foreach ($import_data as $import){
                    $nik    = $import->nik;
                    $nama    = $import->nama;
                    $jumlah  = $import->jumlah;
                    $import_file  = $import->import_file;


                    $this->db->where('nama', $nama);
                    $this->db->where('periode', $periode);
                    $this->db->set($import_file, $jumlah);
                   $update =  $this->db->update('ts_rekap_tkd');

                   if($update){
                       echo $nama .' Update Data Pajak berhasil';
                   }else{
                       echo $nama .' Update Data Pajak Gagal';
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




    function import_pajak(){
          $data = array(); // Buat variabel $data sebagai array


          $periode = '2024-08';

		  $date_now  = date('Ymd_Hi');
		  $file_name = $date_now;
          $path = 'pajak';

		  $upload = $this->Master_model->upload_file($file_name, $path);

		  if($upload['result'] == "success"){ // Jika proses upload sukses
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';

			$excelreader = new PHPExcel_Reader_Excel2007();
			$loadexcel = $excelreader->load('uploads/'. $path.'/'.$file_name.'.xlsx'); // Load file yang tadi diupload ke folder excel
			$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

			// Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
			$data = array();
			$numrow = 1;
            $num = 0;


			foreach($sheet as $row){
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
              <a href="'.base_url().'admin/listing_tkd/update_listing_tkd">Update data</a>';

		  }else{ // Jika proses upload gagal

            echo  $upload['error'];
			#$this->session->set_userdata('error_msg', $upload['error']); // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
			#redirect('admin/swab/error_import');
		  }

          exit;
    }


    function list_import(){

         $qry = $this->db->get('temp_import');

         $data['import_data'] = $qry->result();
         $this->load->view('admin/listing_tkd/import_data', $data);
    }



    function reset_tdd($id)  {

        $this->db->where('id', $id);
        $this->db->set('ttd_spj', '');
        $this->db->update('ts_rekap_tkd');

        $this->session->set_flashdata('message','<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset');
        redirect('admin/listing_tkd/view_daftar_ttd');

    }

    function reset_tdd_gaji($id)  {

        $this->db->where('id', $id);
        $this->db->set('ttd_spj', '');
        $this->db->update('ts_listing_gaji');

        $this->session->set_flashdata('message','<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset');
        redirect('admin/listing_tkd/view_daftar_ttd_gaji');

    }

    function cekSesuai($id) {
        $this->db->where('id', $id);
        $this->db->set('status', 1);
        $this->db->update('ts_rekap_tkd');

        $this->session->set_flashdata('message','<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset');
        redirect('admin/listing_tkd/index');
    }



    function update_npwp(){

        $new_npwp = $this->input->post('npwp_edit');
        $id_spj = $this->input->post('id_spj');

        $new_npwp = trim($new_npwp);

        $this->db->where('id', $id_spj);
        $this->db->set('npwp', $new_npwp);
        $this->db->update('ts_listing_gaji');


        $qry2 = $this->db->get_where('ts_listing_gaji', array('id'=>$id_spj));
        $row2 = $qry2->result();
        $nama = $row2[0]->nama;

        $this->db->where('nama', $nama);
        $this->db->set('npwp', $new_npwp);
        $this->db->update('ts_rekap_tkd');



        $this->session->set_flashdata('message','<strong>Success!!! </strong> Tanda tangan SPJ berhasil direset');
        redirect('admin/listing_tkd/view_daftar_ttd_gaji');
    }


    function view_daftar_ttd($case='tkd') {
        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');


        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));
       // $periode = '2025-02';
        if($case=='tkd'){
            $table = 'ts_rekap_tkd';
        }else{
             $table = 'ts_listing_thr';
        }
        $data['data_tkd'] = $this->Laporan_model->getDataListing($periode, $table);

        $this->load->view('admin/listing_tkd/view_daftar_ttd', $data);
    }



    public function listing_gaji()
	{

        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['data_gaji'] = $this->Laporan_model->getListingGaji($periode);

        $this->load->view('admin/listing_tkd/view_listing_gaji', $data);
    }

    public function listing_gaji13()
	{

        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $filter = 'all';

        $data['data_gaji'] = $this->Laporan_model->getListingGaji13($periode, $filter);

        $this->load->view('admin/listing_tkd/view_listing_gaji13', $data);
    }

    public function view_daftar_ttd_gaji13($status='all'){
        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

       
        $data['data_gaji'] = $this->Laporan_model->getListingGaji13($periode, $status);

        $this->load->view('admin/listing_tkd/view_daftar_ttd_gaji13', $data);
    }

    
    public function print_daftar_ttd_gaji13($status='all'){
        // $periode_bulan = $this->session->userdata('periode_bulan');
        // $periode_tahun = $this->session->userdata('periode_tahun');
        // $periode = $periode_tahun.'-'.$periode_bulan;
        // $periode = date('Y-m', strtotime($periode));

        $periode = '2025-06';
        $data['data_gaji'] = $this->Laporan_model->getListingGaji13($periode, $status);

        $this->load->view('admin/laporan/print_ttd_spj_gaji13', $data);
    }


    public function updateNama()
    {
        $id   = $this->input->post('id');
        $nama = $this->input->post('nama');

        $this->db->where('id', $id);
        $this->db->set('nama', $nama);
        $updated = $this->db->update('ts_listing_gaji13');

        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Nama berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'fail', 'message' => 'Gagal update.']);
        }
    }


    function dataraw_spj_ttd_gaji($status ='all') {
        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['data_gaji'] = $this->Laporan_model->getListingGaji($periode, $status);

        $this->load->view('admin/listing_tkd/dataraw_spj_ttd_gaji', $data);
    }


    function view_daftar_ttd_gaji() {

        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['data_gaji'] = $this->Laporan_model->getListingGaji($periode);

        $this->load->view('admin/listing_tkd/view_daftar_ttd_gaji', $data);
     }


     function dataraw_spj_ttd_tkd($case='tkd') {
         $periode_bulan = $this->session->userdata('periode_bulan');
         $periode_tahun = $this->session->userdata('periode_tahun');
         $periode = $periode_tahun.'-'.$periode_bulan;
         $periode = date('Y-m', strtotime($periode));
         if($case=='tkd'){
             $table = 'ts_rekap_tkd';
         }else{
              $table = 'ts_listing_thr';
         }
         $data['data_tkd'] = $this->Laporan_model->getDataListing($periode, $table);

         $this->load->view('admin/listing_tkd/dataraw_spj_ttd_tkd', $data);
     }

     function export_spj_ttd_gaji() {
        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');
        $periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

         $data['data_tkd'] = $this->Laporan_model->getListingGaji($periode);

         $this->load->view('admin/listing_tkd/export_spj_ttd_gaji', $data);
     }



    function export_spj_ttd($case='tkd') {
         $periode_bulan = $this->session->userdata('periode_bulan');
         $periode_tahun = $this->session->userdata('periode_tahun');


         $periode = $periode_tahun.'-'.$periode_bulan;
         $periode = date('Y-m', strtotime($periode));


        if($case=='tkd'){
            $table = 'ts_rekap_tkd';
        }else{
            $table = 'ts_listing_thr';
        }
       //  $data['data_tkd'] = $this->Laporan_model->getListingTKD($periode);

         $data['data_tkd'] = $this->Laporan_model->getDataListing($periode, $table);

         $this->load->view('admin/listing_tkd/export_spj_ttd', $data);
     }


     function updateNIP(){
        $table = 'ts_listing_thr';
        $periode = '2025-02';
        $data_tkd = $this->Laporan_model->getDataListing($periode, $table);

        foreach ($data_tkd as $peg){

            $nama = $peg->nama;
            //$nip = $peg->nip;


            $nip        = $this->Pegawai_model->getNIPByName($nama);

            //echo $nama.'-'.$nip.'<br>';

            $this->db->where('nama', $nama);
            $this->db->set('nip', $nip);
            $this->db->update($table);

        }
        echo 'SELESAI';
    }




}

?>
