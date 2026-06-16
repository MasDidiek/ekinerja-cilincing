<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Admin_jadwal_shift extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();
        $this->load->library('cart');
        $this->Auth_model->cekAuthLogin();
        $this->load->model('Shift_model');
        $this->load->model('Admin_cuti_model', 'acm');
    }



    function index()
    {
       /// print_array($this->session->userdata);
        $nip = $this->session->userdata('nip');
        $usergroup    = $this->session->userdata('usergroup');
        $id_puskesmas = $this->session->userdata('id_puskesmas');

        if($usergroup==7){
            //penanggung jawab
            $this->db->select('id_poli');
            $qry = $this->db->get_where('mst_pegawai', array( 'nip' => $nip));
            $row = $qry->result();
            if(count($row) > 0) {
                $id_poli = $row[0]->id_poli;
            } else {
                $id_poli = null;
            }

            redirect('admin_jadwal_shift/list_pegawai?bagian=rb&id_poli='.$id_poli.'&id_puskesmas='.$id_puskesmas);
        }


        $bagian = $this->input->get('bagian');
        if($bagian=='rb') {
            $qry = $this->db->get_where('mst_puskesmas', array('rb' => 1));
            $row = $qry->result();
        }else if($bagian=='ugd') {
            $qry = $this->db->get_where('mst_poli', array('ugd' => 1));
            $row = $qry->result();
        }else if($bagian=='lab') {
            redirect('admin_jadwal_shift/list_pegawai?bagian=lab');
        }else if($bagian=='driver') {
            redirect('admin_jadwal_shift/list_pegawai?bagian=driver');
        }else{

             $qry = $this->db->get_where('mst_puskesmas', array('rb' => 1));
             $row = $qry->result();
        }

        $data['row'] = $row;
        $this->load->view('admin_jadwal_shift/index', $data);


    }


    function list_pegawai()
    {
        $bagian = $this->input->get('bagian');

        if($bagian=='rb') {
            $id_puskesmas = $this->input->get('id_puskesmas');
            $id_poli = 14; //poli RB
            $row = $this->Pegawai_model->getPegawaiRB($id_puskesmas, $id_poli);
        }else if($bagian=='ugd') {
            $id_poli = $this->input->get('id_poli');
            $row = $this->Pegawai_model->getPegawaiByPoli($id_poli);
        }else if($bagian=='lab') {
            $id_poli = 7;
            $row = $this->Pegawai_model->getPegawaiByPoli($id_poli);
        }else if($bagian=='driver') {
            $id_poli = 27;
            $row = $this->Pegawai_model->getPegawaiByPoli($id_poli);
        }else{
            $id_poli = 1;
            $row = $this->Pegawai_model->getPegawaiByPoli($id_poli);
        }

        $data['list_pegawai'] = $row;
        $data['shift_kerja'] = $this->Master_model->shiftKerjaUGD();
        $this->load->view('admin_jadwal_shift/list_pegawai', $data);
    }

    function no_access_page()
    {
        $this->load->view('admin_jadwal_shift/no_access_page');
    }
    function ajax_get_pegawai_shift()
    {

        $bagian = $this->input->post('id_bagian');
        $explod = explode("/", $bagian);

        $id_bagian    = $explod[0];
        $nama_bagian  = $explod[1];
        $list_pegawai = $this->Pegawai_model->getPegawaiPerbagian($id_bagian);

        $indexpegawai = 0;

        echo '
         <h5 class="mb-3 text-16">' . $nama_bagian . ' </h5>

         <table class="table table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
        $no = 1;

        for ($i = 0; $i < count($list_pegawai); $i++) {
            $namaPegawai = $list_pegawai[$i]->nama;
            $id_pegawai   = $list_pegawai[$i]->id_pegawai;

            echo '<tr>
                                      <td>' . $no . '</td>
                                      <td>' . $namaPegawai . '</td>
                                      <td>
                                      <a href="' . base_url() . 'admin_jadwal_shift/delete_from_list/' . $id_pegawai . '" class="btn btn-sm btn-danger delete" onClick="return confirm(\'Apakah anda ingin Menghapus data ini ?\')">
                                        Hapus </a></td>
                                 </tr>';




            $no += 1;
        }


        echo '</table>';
    }


    function create_initial_shift($id_bagian, $id_pegawai, $pin, $periode)
    {
        $shift = 'OFF';
        $lastDate = date('t', strtotime($periode));
        $batch = array();

        for ($t = 1; $t <= $lastDate; $t++) {

            $tanggal = $periode . '-' . str_pad($t, 2, '0', STR_PAD_LEFT);
            $formatDate = date('Y-m-d', strtotime($tanggal));

            $batch[] = array(
                'tanggal' => $formatDate,
                'id_pegawai' => $id_pegawai,
                'pin' => $pin,
                'shift' => $shift
            );
        }

        // Insert ignore manual
        foreach ($batch as $row) {
            $this->db->query("
                INSERT IGNORE INTO ts_shift_kerja (tanggal, id_pegawai, pin, shift)
                VALUES (?, ?, ?, ?)
            ", $row);
        }

        redirect('admin_jadwal_shift/shift_kerja/' . $id_bagian);
    }


    function craeteShiftBulanan()
    {


        $id_pegawai = 734;
        $pin = '2227';
        $periode = '2025-11';
        $shift = 'OFF';


        $lastDate = date('t', strtotime($periode));
        for ($t = 0; $t < $lastDate; $t++) {
            $date = $t + 1;
            $tanggal = $periode . '-' . $date;
            $formatDate = date('Y-m-d', strtotime($tanggal));

            if (date('N', strtotime($formatDate)) == 6 || date('N', strtotime($formatDate)) == 7) {
                $shift = 'OFF';
            } else {
                $shift = 'REG';
            }

            $data = array(
                'tanggal' => $formatDate,
                'id_pegawai' => $id_pegawai,
                'pin' => $pin,
                'shift' => $shift
            );

            $this->db->insert('ts_shift_kerja', $data);
        }


        echo 'Berhasil Membuat Shift Bulanan untuk Bagian UMUM';
    }




    function create_bagian()
    {
        $nama_bagian = $this->input->post('nama_bagian');

        $data = array(
            'nama_bagian' => $nama_bagian,
            'nama_pj_bagian' => $this->input->post('nama_pj'),
            'id_pj' => $this->input->post('id_pj')
        );
        $this->session->set_flashdata('success', 'Data bagian telah berhasil ditambahkan');
        $this->db->insert('mst_bagian', $data);
    }

    function delete($id_bagian)
    {

        $this->db->where('id_bagian', $id_bagian);
        $this->db->delete('mst_bagian');

        $this->session->set_flashdata('success', 'Data bagian telah berhasil dihapus');
        redirect('admin_jadwal_shift/index');
    }

    public function search_pegawai()
    {
        $keyword = $this->input->post('keyword');

        echo '<script>


                $(".choose_pegawai").click(function() {
                    var data = $(this).attr("id");
                    var pecah = data.split("/");
                    var id_pegawai = pecah[0];
                    var nama_pegawai = pecah[1];

                    $("#search_pegawai").val(nama_pegawai);
                    $("#id_pj_choose").val(id_pegawai);
                    $("#list_pegawai").hide();
                });
                </script>';



        $row = $this->Pegawai_model->search_pegawai($keyword);

        for ($i = 0; $i < count($row); $i++) {
            $id_pegawai = $row[$i]->id_pegawai;
            $nama = $row[$i]->nama;

            echo '<div class="choose_pegawai" id="' . $id_pegawai . '/' . $nama . '">' . $nama . '</div>';
        }
    }

    function edit()
    {
        $id_bagian = $this->input->post('id_bagian');

        $qry = $this->db->get_where('mst_bagian', array('id_bagian' => $id_bagian));
        $data['data_edit']  = $qry->result();

        $this->load->view('admin_jadwal_shift/edit', $data);
        // print_array($row);
    }

    function update($id_bagian)
    {
        $nama_bagian = $this->input->post('nama_bagian');

        $data = array(
            'nama_bagian' => $nama_bagian,
            'nama_pj_bagian' => $this->input->post('nama_pj'),
            'id_pj' => $this->input->post('id_pj')
        );

        $this->db->where('id_bagian', $id_bagian);
        $this->db->update('mst_bagian', $data);

        $this->session->set_flashdata('success', 'Data bagian telah berhasil disimpan');
        redirect('admin_jadwal_shift/index');
    }


    function shift_kerja($id_bagian)
    {

        $data['list_pegawai']  = $this->Pegawai_model->getPegawaiPerbagian($id_bagian);
        $data['shift_kerja']  =  $this->Master_model->shiftKerjaUGD();// ambil shift kerja yg biasa diguakan utk petugas UGD
        $data['all_pegawai']  = $this->Pegawai_model->getListPegawai('non_pns', 2024);

        $this->load->view('admin_jadwal_shift/view_jadwal', $data);
       // $this->load->view('admin_jadwal_shift/list_pegawai', $data);
    }


    function summary($id_bagian)
    {
        $data['list_pegawai']  = $this->Pegawai_model->getPegawaiPerbagian($id_bagian);
        $data['shift_kerja']  =  $this->Master_model->getShiftKerja(1);
        $this->load->view('admin_jadwal_shift/summary', $data);
    }

    function simpan_shift_kerja()  {

        $shift = $this->input->post('shift');
        $id_pegawai = $this->input->post('id_pegawai');
        $pin = $this->input->post('pin');

        foreach ($shift as $tanggal => $kode_shift) {
              $detailShift        = $this->Master_model->getJamKerjaShift($kode_shift);
              $data = array(
                'tanggal' => $tanggal,
                'id_pegawai' =>  $this->input->post('id_pegawai'),
                'pin' =>  $this->input->post('pin'),
                'shift' => $kode_shift,
                'jam_masuk' => $detailShift[0],
                'jam_keluar' => $detailShift[1],

            );

            $this->db->insert('ts_shift_kerja', $data);

            //$this->Absensi_model->insertAbsensiKehadiranHarian($id_pegawai, $pin,$tanggal, $kode_shift,null,null);
        }


         redirect('admin_jadwal_shift/shift_kerja/2');
    }


    function insertShiftKerja()
    {
        $data_post = $this->input->post('data_post');
        $kode_shift = $this->input->post('kode_shift');


        $expld = explode("_", $data_post);
        $id_pegawai = $expld[0];
        $tanggal    = $expld[1];

        $tgl = format_db($tanggal);

        $nip        = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
        $cekIdShift =  $this->Presensi_model->getDatashiftKerja($id_pegawai, $tgl, 'id');

        $detailShift        = $this->Master_model->getJamKerjaShift($kode_shift);

        $pin = substr($nip, -4);
        if ($cekIdShift != 0) {
            $id = $cekIdShift;
            $this->db->where('id', $id);
            $this->db->set('shift', $kode_shift);
            $this->db->set('jam_masuk', $detailShift[0]);
            $this->db->set('jam_keluar', $detailShift[1]);
            $this->db->update('ts_shift_kerja');


        } else {
            $data = array(
                'tanggal' => $tgl,
                'id_pegawai' => $id_pegawai,
                'pin' => $pin,
                'shift' => $kode_shift,
                'jam_masuk' => $detailShift[0],
                'jam_pulang' => $detailShift[1],

            );

            $this->db->insert('ts_shift_kerja', $data);
        }


        $sql = $this->db->get_where('tbl_kehadiran_harian', array('tanggal' => $tgl, 'pin' => $pin));
		$row = $sql->row();

        if (!empty($row)) {
            $id_absen = $row->id;
            $this->db->where('id', $id_absen);
            $this->db->set('shift', $kode_shift);
            $this->db->update('tbl_kehadiran_harian');
        }
        redirect('admin_jadwal_shift/index');
    }

    function update_absensi_pegawai($id_pegawai, $pin)
    {
        $periode_bulan = $this->session->userdata('periode_bulan');
        $periode_tahun = $this->session->userdata('periode_tahun');

        if ($periode_bulan == '') {
            $bulan = date('m');
            $tahun = date('Y');
        } else {
            $bulan = $periode_bulan;
            $tahun = $periode_tahun;
        }


        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));

        $lastDate = date('t', strtotime($periode)) + 1;
        for ($t = 1; $t < $lastDate; $t++) {
            $tanggal = $periode . '-' . $t;
            $formatDate = date('Y-m-d', strtotime($tanggal));

            $shift = $this->Presensi_model->getDatashiftKerja($id_pegawai, $formatDate, 'shift');

            if ($shift == '-') {
                //echo 'Jangan Update';

                // $id = $this->Presensi_model->cekAbsenExist($formatDate, $pin);

            } else {
                $id = $this->Presensi_model->cekAbsenExist($formatDate, $pin);

                if ($id == 0) {
                    $this->Presensi_model->insertShiftPegawai($pin, $formatDate, $shift);
                } else {

                    $this->Presensi_model->updateShiftPegawai($pin, $formatDate, $shift, $id);
                }
            }
        }
        redirect('admin_jadwal_shift/shift_kerja/7');
    }



    function shift_regular()
    {
        //
        $data['shift_kerja']  =  $this->Master_model->getShiftKerja(1);
        $this->load->view('admin_jadwal_shift/shift_regular', $data);
    }

    function getInfo()
    {
        $data_post = $this->input->post('data_post');

        $expld = explode("_", $data_post);
        $id_pegawai = $expld[0];
        $tanggal    = $expld[1];

        $formatDate = format_full($tanggal);
        $hari = getNamahari($tanggal);


        if ($id_pegawai > 0) {
            $nama = $this->Pegawai_model->getNamaPegawaiByID($id_pegawai);
        } else {
            $nama = '';
        }


        echo  '<h5>' . $nama . '</h5>Tanggal : <strong>'.$hari.', ' . $formatDate . '</strong>';
    }

    public function search_pegawai_edit()
    {
        $keyword = $this->input->post('keyword');

        echo '<script>


                $(".choose_pegawai").click(function() {
                    var data = $(this).attr("id");
                    var pecah = data.split("/");
                    var id_pegawai = pecah[0];
                    var nama_pegawai = pecah[1];

                    $("#search_pegawai_edit").val(nama_pegawai);
                    $("#id_pj_choose_edit").val(id_pegawai);
                    $("#list_pegawai_edit").hide();
                });
                </script>';



        $row = $this->Pegawai_model->search_pegawai($keyword);

        for ($i = 0; $i < count($row); $i++) {
            $id_pegawai = $row[$i]->id_pegawai;
            $nama = $row[$i]->nama;

            echo '<div class="choose_pegawai" id="' . $id_pegawai . '/' . $nama . '">' . $nama . '</div>';
        }
    }


    public function search_pegawai_cart()
    {
        $keyword = $this->input->post('keyword');

        echo '<script>


                $(".choose_pegawai").click(function() {
                    var id_pegawai = $(this).attr("id");


                        $.ajax({
                            type: "POST",
                            url: "' . base_url() . 'admin_jadwal_shift/add_cart",
                            data: "id_pegawai=" + id_pegawai,
                            success: function(return_data) {
                                $("#search_pegawai_cart").val("");
                                $("#list_pegawai_cart").hide();
                                $("#cart_pegawai").html(return_data);
                            }
                        });


                });
                </script>';



        $row = $this->Pegawai_model->search_pegawai($keyword);

        for ($i = 0; $i < count($row); $i++) {
            $id_pegawai = $row[$i]->id_pegawai;
            $nama       = $row[$i]->nama;

            echo '<div class="choose_pegawai" id="' . $id_pegawai . '">' . $nama . '</div>';
        }
    }

    public function add_cart()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);

        $data = array(
            'id'      => $id_pegawai,
            'qty'     => 1,
            'price'   => 1000,
            'name'    => $pegawai->nama,
            'desc'  =>  $pegawai->nip,
        );

        $this->cart->insert($data);
        $cart_contents = $this->cart->contents();

        echo '  <table class="table mt-4">
                    <thead class="ltr:text-left rtl:text-right bg-slate-100 dark:bg-zink-600">
                        <tr>
                            <th>No.</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody> ';
        //echo 'berhasil';
        $no = 1;
        foreach ($cart_contents as $item) :


            echo '<tr id="tr_' . $item['rowid'] . '">
									<td>' . $no . '</td>
									<td>' . $item['desc'] . ' </td>
									<td> ' . $item['name'] . '  </td>
									<td>
									<button type="button" value="' . $item['rowid'] . '" class="remove-cart">Remove</button> </td>
								</tr>';
            $no += 1;
        endforeach;


        echo '</tbody>
						</table>';


        echo '<script>

			$(".remove-cart").click(function(){
					var rowid = $(this).val();


						$.ajax({

							type:"POST",
							dataType:"html",
							url:"' . base_url() . 'kinerja/remove_cart",
							data:"rowid="+rowid,
							success:function(msg){
								$("#tr_"+rowid).hide();
								$("#snackbar").html(\'Kegiatan berhasil dihapus dari aktiftias utama\');
								var x = document.getElementById("snackbar");
								x.className = "show";
								setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
							}

						});
				});

				</script>';
    }

    function upload_data_shift()
    {


        $id_bagian  = $this->input->get('id_bagian');
        $pin        = $this->input->get('pin');
        $bulan      = $this->input->get('bulan');
        $tahun      = $this->input->get('tahun');
        $jns_pegawai= $this->input->get('jns_pegawai');
        $id_pegawai      = $this->input->get('id_pegawai');
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);


        $dataShift = $this->Shift_model->getShiftPerbulanById($id_pegawai, $periode);


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

        $this->session->set_flashdata('success', 'Data shift  berhasil diupload ke absensi pegawai');

        redirect('admin_jadwal_shift/view_absensi?id_pegawai=' . $id_pegawai . '&bulan=' . $bulan . '&tahun=' . $tahun . '&jns_pegawai=' . $jns_pegawai);

        // print_array($dataShift);
    }
        public function sinkron_absensi()
        {
            $id_bagian  = $this->input->get('id_bagian');
            $pin        = $this->input->get('pin');
            $bulan      = $this->input->get('bulan');
            $tahun      = $this->input->get('tahun');
            $jns_pegawai= $this->input->get('jns_pegawai');
            $id_puskesmas      = $this->input->get('id_puskesmas');
            $id_pegawai      = $this->input->get('id_pegawai');
            $ip_address = $this->Master_model->getIpAddress($id_puskesmas);

            $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

            $jns_jam_kerja = 'shift'; // perbaikan dari $$ (typo)
        // =========================
        // 1. Ambil data dari mesin
        // =========================
        $absensi_raw = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);

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
            if ($kode_shift == 'OFF') {
                continue;
            }

            $this->db->where('id', $kehadiran->id);
            $this->db->update('tbl_kehadiran_harian', [
                'jam_masuk'  => $jam_masuk,
                'jam_pulang' => $jam_pulang,

            ]);
        }

        redirect('admin_jadwal_shift/view_absensi?id_pegawai=' . $id_pegawai . '&bulan=' . $bulan . '&tahun=' . $tahun . '&jns_pegawai=' . $jns_pegawai);

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


    function view_absensi()
    {

        $id_pegawai = $this->input->get('id_pegawai');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        $jns_pegawai = $this->input->get('jns_pegawai');
        if ($jns_pegawai=='') {
            $jns_pegawai = 'non_pns';
        }

        $this->session->set_userdata('status_pegawai',  $jns_pegawai);

        $pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $pin = $pegawai->pin;



        $periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));

        $data['cuti_pegawai'] = $this->acm->get_cuti_bulanan_absensi($id_pegawai, $bulan, $tahun);

        $data['absensi']      = $this->Presensi_model->get_absensi_pegawai($pin, $bulan, $tahun);
        $data['dataRekap']    = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
        $data['DinasLuar']    =  $this->Presensi_model->getDataPengajuanDLPerbulan($id_pegawai, $periode);

        $data['IzinSakit']    = $this->Presensi_model->getDataIzinSakit($id_pegawai, 'ALL', $periode);


        $data['detailPegawai'] =  $pegawai;
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $this->load->view('admin_jadwal_shift/view_absensi_pegawai', $data);
    }


    public function update_rekap_absensi($id_bagian, $id_pegawai, $pin, $bulan, $tahun)
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

            if ($shift == 'OFF') {
                $this->update_menit($row->id, 0, 0);
                $this->update_status($row->id, 'OFF', null);
                continue;
            }

            if ($shift == 'L-OFF') {
                if (empty($jam_pulang)) {
                    $pulang_awal = 150;
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

                    // jika tidak ada absen sama sekali
                    $status = 'HADIR';
                    if (empty($jam_masuk) && empty($jam_pulang)) {
                        $status = 'ALPHA';
                    }

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
        redirect('admin_jadwal_shift/view_absensi/' . $id_bagian . '/' . $pin . '/' . $bulan . '/' . $tahun . '/non_pns');
    }


    private function update_status($id, $status, $status_detail)
    {
        $this->db->where('id', $id);
        $this->db->set('status', $status);
        $this->db->set('status_detail', $status_detail);
        $this->db->update('tbl_kehadiran_harian');
        return true;
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

    private function updateStatusIzinSakit($row, $id_pegawai, $tanggal)
    {
        $sakit = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('tanggal', $tanggal)
            ->where('status', 1)
            ->where('jenis_absen', 'SAKIT')
            ->get('pengajuan_izin_sakit')
            ->row();

        if ($sakit) {

            $status = 'SAKIT';

            $status_detail = ($sakit->jns_sakit == 2)
                ? 'SAKIT_SK'
                : 'SAKIT';

            $this->update_status($row->id, $status, $status_detail);
            $this->update_menit($row->id, 0, 0);

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
                $this->update_status($row->id, 'IZIN', 'IZIN FULL');
            }

            if ($izin->jns_izin == 2) {
                $update = $this->update_status($row->id, 'IZIN', 'IZIN AWAL');
            }

            if ($izin->jns_izin == 3) {
                $this->update_status($row->id, 'IZIN', 'IZIN AKHIR');
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

                if ($row->status_detail == 'IZIN_FULL') {
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

    function simpan($id_bagian)
    {
        $cart_contents = $this->cart->contents();

        foreach ($cart_contents as $item) :

            $nip = $item['desc'];
            $this->db->where('nip', $nip);
            $this->db->set('bagian_shift', $id_bagian);
            $this->db->update('mst_pegawai');

        endforeach;


        $this->session->set_flashdata('success', 'Data bagian telah berhasil disimpan');
        redirect('admin_jadwal_shift/shift_kerja/' . $id_bagian);
    }
    function delete_from_list($id_pegawai, $id_bagian)
    {
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->set('bagian_shift', 0);
        $this->db->update('mst_pegawai');
        $this->session->set_flashdata('success', 'Data bagian telah berhasil disimpan');
        redirect('admin_jadwal_shift/shift_kerja/' . $id_bagian);
    }
}
