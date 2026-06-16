<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Kinerja extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();
		$this->Auth_model->cekAuthLogin();
		$this->load->model('Laporan_model');
		 $this->load->model('Admin_cuti_model', 'acm');
		$this->load->helper('form');
		$this->load->helper('text');
		$this->load->library('cart');
	}

	function index()
	{
		$id_pegawai = $this->session->userdata('id_pegawai');


		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');


		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');
		  $this->session->set_userdata('periode_bulan', $bulan);
		  $this->session->set_userdata('periode_tahun', $tahun);

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}



		// redirect('kinerja/input_kinerja_v2');
		$data['dataAktifitasPegawai'] = $this->Kinerja_model->getAktifitasPegawai($id_pegawai);
        $this->load->view('kinerja/main', $data);
    }


    public function load_kegiatan()
    {
        // Misalnya kamu hanya ambil kinerja milik pegawai tertentu (contoh: id_pegawai = 1)
        $id_pegawai = $this->session->userdata('id_pegawai'); // Bisa kamu ganti nanti sesuai session
        //$tahun = $this->input->get('tahun') ?? date('Y');

		$tahun =  '2025';
		$tgl   =  '2025-06-01';
        // Ambil total menit dan status gabungan per tanggal
        $this->db->select('tgl, SUM(total) as total_menit, GROUP_CONCAT(status) as status_list');
        $this->db->from('ts_kinerja');
        $this->db->where('id_pegawai', $id_pegawai);
        //$this->db->where('YEAR(tgl)', $tahun);
		$this->db->where('tgl >=', $tgl);
        $this->db->group_by('tgl');
        $result = $this->db->get()->result();

        $events = [];

        foreach ($result as $row) {
            // Hitung status agregat
            $statuses = explode(',', $row->status_list);
            $has_unvalidated = in_array("0", $statuses);
            $all_validated = (count(array_unique($statuses)) === 1 && $statuses[0] == "1");

            // Default warna: abu
            $color = '#6c757d'; // gray
            if ($has_unvalidated) {
                $color = '#ffde59'; // orange
            }
            if ($all_validated) {
                $color = '#5cb85c'; // green
            }

            $events[] = [
                'title'   => $row->total_menit . ' menit',
                'start'   => $row->tgl,
                'allDay'  => true,
                'color'   => $color
            ];
        }



		// Tambahkan event manual, misalnya: izin
		

		$cutiPegawai        = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
		$izinSakit          = $this->Presensi_model->getDataIzinSakitPegawai($id_pegawai);

		for ($i=0; $i < count($cutiPegawai) ; $i++) { 
			 $tgl_dari = $cutiPegawai[$i]->tgl_dari;
             $tgl_sampai = $cutiPegawai[$i]->tgl_sampai;


			 $events[] = [
				'title'  => 'Cuti',
				'start'  =>  $tgl_dari,
				'allDay' => true,
				'color'  => '#78bbf5'
			];
					

		}
		//print_array($cutiPegawai);

        echo json_encode($events);
    }

    public function get_kegiatan_by_tanggal()
    {
          $id_pegawai = $this->session->userdata('id_pegawai'); // Ganti sesuai session
        $tgl = $this->input->get('tgl');

        $data = $this->db->get_where('ts_kinerja', [
            'id_pegawai' => $id_pegawai,
            'tgl' => $tgl
        ])->result();

        echo json_encode($data);
    }


    public function get_kegiatan_by_id()
    {
        $id = $this->input->get('id');
        $data = $this->db->get_where('ts_kinerja', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        $this->db->delete('ts_kinerja', ['id' => $id]);
        echo json_encode(['status' => 'success']);
    }


	function index2()
	{
		$id_pegawai = $this->session->userdata('id_pegawai');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');


		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');
		  $this->session->set_userdata('periode_bulan', $bulan);
		  $this->session->set_userdata('periode_tahun', $tahun);

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}


		$data['dataAktifitasPegawai'] = $this->Kinerja_model->getAktifitasPegawai($id_pegawai);
        $this->load->view('kinerja/input_kinerja', $data);
    }


	function detail_capaian($periode) {

		$id_pegawai = $this->session->userdata('id_pegawai');
		$nip  = $this->session->userdata('nip');



		//print_array($this->session->userdata);


		$data['detail_pegawai']   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		//$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiTahunan($id_pegawai, $tahun);
		$data['dataRekap']  = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);


		$data['dataCapaian'] = $this->Kinerja_model->getDataCapaianPegawai($nip);
		$this->load->view('kinerja/detail_capaian', $data);
	}

	// function input_kinerja_v2() {

	// 	$id_pegawai = $this->session->userdata('id_pegawai');

	// 	$periode_bulan = $this->session->userdata('periode_bulan');
	// 	$periode_tahun = $this->session->userdata('periode_tahun');


	// 	if($periode_bulan=='') {
	// 	  $bulan = date('m');
	// 	  $tahun = date('Y');
	// 	  $this->session->set_userdata('periode_bulan', $bulan);
	// 	  $this->session->set_userdata('periode_tahun', $tahun);

	// 	}else{
	// 	  $bulan = $periode_bulan;
	// 	  $tahun = $periode_tahun;
	// 	}
	// 	$data['dataAktifitasPegawai'] = $this->Kinerja_model->getAktifitasPegawai($id_pegawai);

	// 	$data['cutiPegawai']          = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
	// 	$data['izinSakit']          = $this->Presensi_model->getDataIzinSakitPegawai($id_pegawai);
	// 	$data['freqAktifitas'] = $this->Kinerja_model->GetFrequentAktifitas($id_pegawai);
    //     $this->load->view('kinerja/input_kinerja_v2', $data);

	// }

	function ajaxSetSessionPeriode(){

		$prev_next     = $this->input->post('prev_next');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');

		if($periode_bulan=='') {
		  $curr_month = date('m');
		  $tahun = date('Y');

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;


			if($prev_next=='prev'){
				if($bulan==1){
					$curr_month = 12;
					$tahun = $tahun-1;
				}else{
					$curr_month = $bulan-1;
				}
			}else{
				if($bulan==12){
					$curr_month = 1;
					$tahun = $tahun+1;
				}else{
					$curr_month = $bulan+1;
				}

			}


		}


		$this->session->set_userdata('periode_bulan', $curr_month);
		$this->session->set_userdata('periode_tahun', $tahun);

		return true;

	}

	function list_kinerja()  {
		$id_pegawai = $this->session->userdata('id_pegawai');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');


		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');
		  $this->session->set_userdata('periode_bulan', $bulan);
		  $this->session->set_userdata('periode_tahun', $tahun);

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}
		$data['dataAktifitasPegawai'] = $this->Kinerja_model->getAktifitasPegawai($id_pegawai);

		$data['cutiPegawai']          = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
		$data['izinSakit']          = $this->Presensi_model->getDataIzinSakitPegawai($id_pegawai);
		$data['freqAktifitas'] = $this->Kinerja_model->GetFrequentAktifitas($id_pegawai);
        $this->load->view('kinerja/list_kinerja', $data);
	}



	function ajaxGetJamAbsen(){
		$tanggal       = $this->input->post('tanggal');
		$nip  		   = $this->session->userdata('nip');
		$id_pegawai    = $this->session->userdata('id_pegawai');
		$pin 		   = substr($nip, -4);
		$sinkron       = $this->Sinkron_model->sinkronAbsensiHarian($id_pegawai, $pin, $tanggal);




		echo $sinkron[0].'/'.$sinkron[1];
		//print_array($sinkron);
	}

	function ajaxGetAktifitasHarian() {

		$tanggal       = $this->input->post('tanggal');
		$id_pegawai    = $this->session->userdata('id_pegawai');

		$id_puskesmas  = $this->session->userdata('id_puskesmas');
		$nip  		   = $this->session->userdata('nip');

		$tanggal       = format_db($tanggal);

		$qry = $this->db->get_where('ts_kinerja', array('id_pegawai'=> $id_pegawai, 'tgl'=> $tanggal ));

		$pin 		   = substr($nip, -4);
		//$ip_address    = '10.20.170.102';
		$sinkron       = $this->Sinkron_model->sinkronAbsensiHarian($id_pegawai, $pin, $tanggal);


		$data['aktifitas']          = $qry->result();
		$data['tanggal_aktifitas']	= $tanggal ;
		$data['absensi']	= $sinkron ;

		$this->load->view('kinerja/view_aktifitas_harian', $data);
	}

	function ajaxGetEditAktifitas() {

		$id  = $this->input->post('id');
		$qry = $this->db->get_where('ts_kinerja', array('id'=> $id));
		$data['dataAktifitas'] = $qry->result();
		$this->load->view('kinerja/view_edit_aktifitas', $data);

	}

	function ajaxGetDataEdit()  {
		$tanggal     = $this->input->post('tanggal');
		$jam         = $this->input->post('jam');
		$explod      = explode(":", $jam);
		$hours       = $explod[0];
		$minutes     = $explod[1];

		if($hours<10){
			$hours = '0'.$hours;
		}

		if($minutes==0){
			$minutes = '00';
		}

		$tgl = format_db($tanggal);
		$jam_mulai   = $hours.':'.$minutes;
		$id_pegawai  = $this->session->userdata('id_pegawai');


		$qry = $this->db->get_where('ts_kinerja', array('id_pegawai'=> $id_pegawai, 'tgl'=> $tgl, 'jam_mulai'=> $jam_mulai));
		$row = $qry->result();

		echo json_encode($row);

	}

		//mencari data aktifitas dari bank aktifitas
	public function autocomplete()
	{
		$term = $this->input->get('term');
		
	
		$results = $this->Kinerja_model->search_aktivitas($term);

		$data = array();
		foreach ($results as $row) {
			$data[] = array(
				'label' => $row['nama_kegiatan'].'. ( '.$row['waktu'].' menit )',
				'value' => $row['id']    // value yang disimpan (jika perlu)
			);
		}

		echo json_encode($data);
	}
	function pengaturan_aktifitas(){
		$nip = $this->session->userdata('nip');
		$data['aktifitas_utama'] = $this->Kinerja_model->getAktifitasUtamaPegawai($nip);
		$data['kegiatan'] = $this->Kinerja_model->getMasterKegiatan();
		$this->load->view('kinerja/pengaturan_aktifitas', $data);
	}


	function ajaxRefreshList(){
		$nip = $this->session->userdata('nip');
		$aktifitas_utama = $this->Kinerja_model->getAktifitasUtamaPegawai($nip);
		
		 $no = 1;
			foreach ($aktifitas_utama as $aktifitas){

				$id             = $aktifitas->id;
				$nama_kegiatan  = $aktifitas->nama_kegiatan;
				$satuan         = $aktifitas->satuan;
				$waktu          = $aktifitas->waktu;

			

						echo' <tr>
									<td class="text-center">'.$no.' </td>

									<td> '.$nama_kegiatan.'</td>
									<td>'.$satuan.' </td>
									<td>'.$waktu.' menit </td>
									<td> 
									<a href="'.base_url().'kinerja/delete_aktifitas_utama/'.$id.'" class="btn btn-danger btn-sm" title="Delete from list">
										Hapus
									</a>
									</td>
									
								</tr>';

								$no += 1;
							

			}

		
	}


	public function simpan_aktifitas_pegawai()
	{
		$id_aktivitas = $this->input->post('aktivitas_id');
		$nama = $this->input->post('nama_aktivitas');
		$nip = $this->session->userdata('nip');
		// Simpan ke tabel aktivitas_pegawai misalnya
		$newArray = array(
			'nip' => $nip,
			'id_aktifitas' => $id_aktivitas
		);

		$this->db->insert('aktifitas_pegawai', $newArray);

		echo json_encode(['status' => 'success']);
	}

	function save_aktifitas(){
		$nip = $this->session->userdata('nip');
		$cart_contents = $this->cart->contents();

		foreach ($cart_contents as $item) :

			$newArray = array(
				'nip' => $nip,
				'id_aktifitas' =>$item['id']
			);

			$this->db->insert('aktifitas_pegawai', $newArray);


		endforeach;
		$this->cart->destroy();
		redirect('kinerja/pengaturan_aktifitas');
	}


	function add_aktifitas_utama() {
		$nip = $this->session->userdata('nip');
		$id  = $this->input->post('id');

		$newArray = array(
			'nip' => $nip,
			'id_aktifitas' => $id
		);

		$this->db->insert('aktifitas_pegawai', $newArray);

		$aktifitas_utama = $this->Kinerja_model->getAktifitasUtamaPegawai($nip);
		//redirect('kinerja/pengaturan_aktifitas')

			$no = 1;
			foreach ($aktifitas_utama as $aktifitas){

				$id             = $aktifitas->id;
				$nama_kegiatan  = $aktifitas->nama_kegiatan;
				$satuan         = $aktifitas->satuan;
				$waktu          = $aktifitas->waktu;



				echo' <tr>
							<td>'.$no.' </td>
							<td>'.$nama_kegiatan.' </td>
							<td class="text-center">'.$satuan.'</td>
							<td class="text-center">'.$waktu.' menit </td>
							<td>  <button type="button" value="'.$id.'" class="btn btn-sm btn-danger" title="Delete from list">
								<i class="ti ti-trash fs-3"></i>
									</button></td>

						</tr>';

						$no += 1;


			}

			echo '<script>

			$(".btn-danger").click(function(){
					var id = $(this).val();


						$.ajax({

							type:"POST",
							dataType:"html",
							url:"'.base_url().'kinerja/delete_aktifitas_utama",
							data:"id="+id,
							success:function(msg){
							$("#data_aktifitas_utama").html(msg);
							$("#snackbar").html(\'Kegiatan berhasil dihapus dari aktiftias utama\');
							var x = document.getElementById("snackbar");
							x.className = "show";
							setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
							}

						});
				});

				</script>';

	}

	function delete_aktifitas_utama($id) {
		$nip = $this->session->userdata('nip');


		$this->db->where('id', $id);
		$this->db->delete('aktifitas_pegawai');

		redirect('kinerja/pengaturan_aktifitas');

	}


	function insert_new_aktifitas() {
		$nama        = $this->input->post('nama');
		$satuan        = $this->input->post('satuan');
		$waktu        = $this->input->post('waktu');
		$id        = $this->input->post('id_kegiatan');

		$new_data = array(
			'id_jabatan' => 1,
			'nama_kegiatan' => $nama,
			'satuan' => $satuan,
			'waktu' => $waktu,
		);

		if($id==0){
			$this->db->insert('mst_kegiatan', $new_data);
		}else{
			$this->db->where('id', $id);
			$this->db->update('mst_kegiatan', $new_data);
		}




		redirect('kinerja/pengaturan_aktifitas');

	}

	function getInputanAktifitas(){
		$id_pegawai = $this->session->userdata('id_pegawai');
		$tgl        = $this->input->post('tanggal');

		$detail_pegawai   = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
		$nip = $detail_pegawai[0]->nip;
		$pin = substr($nip, -4);


		$tanggal = format_db($tgl);
		$data['dataAktifitas'] = $this->Kinerja_model->getDataInputAktifitas($id_pegawai, $tanggal);
		$data['absensiHarian'] = $this->Presensi_model->getDataAbsensi($pin, $tanggal);
		$data['tanggal'] = $tanggal;

		$this->load->view('kinerja/view_inputan_aktifitas', $data);
	}

	function getDataEditAktifitas(){

		$id = $this->input->post('id');

		$data['dataAktifitas'] = $this->Kinerja_model->getDataEditAktifitas($id);
		$this->load->view('kinerja/view_edit_aktifitas', $data);
	}

	function input_aktifitas() {
		$tanggal = $this->input->post('tanggal');
		$data['tgl'] = $tanggal;
		$this->load->view('kinerja/view_input_aktifitas', $data);
	}

	function cekTanggal(){
		//cari data ditanggal tsb, apakah cuti, izin, sakit, atau tanggal sudah lewat
		$tanggal        = $this->input->post('tanggal');
		$id_pegawai     = $this->session->userdata('id_pegawai');

		$bulanNow       = date('m');
        $bulanAktifitas = date('m', strtotime($tanggal));

		$formatDate     = format_db($tanggal);

		$cekCuti        = $this->Cuti_model->cekCutiPegawai($tanggal, $id_pegawai);
		$cekIzinSakit   = $this->Cuti_model->cekIzinSakitPegawai($tanggal, $id_pegawai);
		$tgl_now = date('d');

		$bulanInput = date('m', strtotime($formatDate));


		// if($bulanInput < 11){

		// 	echo ' <div class="px-4 py-3 mb-4 text-sm text-red-500 border border-transparent rounded-md bg-red-50 dark:bg-red-400/20">
		// 			<span class="font-bold">Warning !!!</span> Input kinerja /  aktifitas tidak diizinkan, Tanggal input melewati batas akhir input kinerja
		// 	</div>

		// 	';
		// }

		if($bulanInput < $bulanNow ){

			//$allowInput = 'error1';
$allowInput = true;
		}else{
			$allowInput = true;
		}

		if($bulanAktifitas < $bulanNow){
			if($tgl_now < 6){
				echo 'restricted2';
			}else{
				echo 'allowed';
			}

		}else{


			if(strtotime($tanggal) > strtotime($tglSekarang))
			{
				echo 'restricted2';
			}else{
				echo 'allowed';
			}

			//echo 'allowed';
		}




		$nip            = $this->Pegawai_model->getNipPegawaiByID($id_pegawai);
		$pin            = substr($nip, 19,4);

		$dataAbsensi    = $this->Presensi_model->getDataAbsensi($pin, $formatDate);





		if(!empty($dataAbsensi)){
			$masuk   = $dataAbsensi[0]->masuk;
			$pulang  = $dataAbsensi[0]->pulang;

			echo ' <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
						<div class="xl:col-span-6">
						<label class="inline-block mb-2 text-base font-medium">Absen Masuk : &nbsp; &nbsp;</label>
						<strong class="transition-all w-[100%] text-custom-500 !bg-custom-100 dark:!bg-custom-500/20 border-none rounded-md py-1.5 px-3" class="external-event fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event text-custom-500 btn bg-custom-100 hover:text-white hover:bg-custom-600 focus:text-white focus:bg-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:ring active:ring-custom-100 dark:bg-custom-500/20 dark:text-custom-500 dark:hover:bg-custom-500 dark:hover:text-white dark:focus:bg-custom-500 dark:focus:text-white dark:active:bg-custom-500 dark:active:text-white dark:ring-custom-400/20">'.$masuk.'</strong>

					</div>
					<div class="xl:col-span-6">
						<label  class="inline-block mb-2 text-base font-medium">Absen Pulang  : &nbsp; &nbsp;</label>
							<strong class="transition-all w-[100%] text-custom-500 !bg-custom-100 dark:!bg-custom-500/20 border-none rounded-md py-1.5 px-3" class="external-event fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event text-custom-500 btn bg-custom-100 hover:text-white hover:bg-custom-600 focus:text-white focus:bg-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:ring active:ring-custom-100 dark:bg-custom-500/20 dark:text-custom-500 dark:hover:bg-custom-500 dark:hover:text-white dark:focus:bg-custom-500 dark:focus:text-white dark:active:bg-custom-500 dark:active:text-white dark:ring-custom-400/20">'.$pulang.'</strong>
					</div>
				</div>';


			if($masuk=='' && $pulang==''){


				echo '<div class="px-4 py-3 my-4 text-sm text-red-500 border border-transparent rounded-md bg-red-50 dark:bg-red-400/20">
                         <span class="font-bold">Warning !!!</span> Input kinerja /  aktifitas tidak diizinkan, anda tidak absen pada hari ini
						  <br>
						  <a href="'.base_url().'admin/presensi/tarik_absensi/'.$pin.'/'.$id_pegawai.'" target="_blank" class="text-white btn bg-yellow-500 border-yellow-500 hover:text-white hover:bg-yellow-600 hover:border-yellow-600 focus:text-white focus:bg-yellow-600 focus:border-yellow-600 focus:ring focus:ring-yellow-100 active:text-white active:bg-yellow-600 active:border-yellow-600 active:ring active:ring-slate-100 dark:ring-slate-400/20">Tarik Absensi</a>

					</div>

					';
			}else{
				if($masuk=='SAKIT'){

				echo ' <div class="px-4 py-3 my-4 text-sm text-red-500 border border-transparent rounded-md bg-red-50 dark:bg-red-400/20">
							<span class="font-bold">Warning !!!</span> Input kinerja /  aktifitas tidak diizinkan, anda tidak absen pada hari ini
					</div>';
				}

			}




		}else{
			// echo '<div class="px-4 py-3 my-4 text-sm text-red-500 border border-transparent rounded-md bg-red-50 dark:bg-red-400/20">
            //              <span class="font-bold">Warning !!!</span> Input kinerja /  aktifitas tidak diizinkan, tidak ada data absensi
			// 			 <br>
			// 			  <a href="'.base_url().'admin/presensi/tarik_absensi/'.$pin.'/'.$id_pegawai.'" target="_blank" class="text-white btn bg-yellow-500 border-yellow-500 hover:text-white hover:bg-yellow-600 hover:border-yellow-600 focus:text-white focus:bg-yellow-600 focus:border-yellow-600 focus:ring focus:ring-yellow-100 active:text-white active:bg-yellow-600 active:border-yellow-600 active:ring active:ring-slate-100 dark:ring-slate-400/20">Tarik Absensi</a>
			// 		</div>

			// ';
		}
		//print_array($dataAbsensi);
		//getDataAbsensi($pin, $tanggal)
		//echo $formatDate;

	}

	function cekTanggalAktifitas(){
		$tanggal        = $this->input->post('tanggal');
		//echo 'allowed';
		$bulanNow       = date('m');
        $bulanAktifitas = date('m', strtotime($tanggal));
		//echo 'allowed';

		//print_array($this->session->userdata());

		$id_pegawai = $this->session->userdata('id_pegawai');

		$tanggal       = format_db($tanggal);
		$cekCuti       = $this->Cuti_model->cekCutiPegawai($tanggal, $id_pegawai);

		$cekIzinSakit  = $this->Cuti_model->cekIzinSakitPegawai($tanggal, $id_pegawai);

		$tglSekarang = date('d-m-Y');


		if(count($cekCuti) > 0){
			echo 'restricted3';
			exit;
		}

		if(count($cekIzinSakit) > 0){
			echo 'restricted4';
			exit;
		}

		$tgl_now = date('d');

		if($bulanAktifitas < $bulanNow){
			if($tgl_now < 6){
				echo 'restricted2';
			}else{
				echo 'allowed';
			}

		}else{


			if(strtotime($tanggal) > strtotime($tglSekarang))
			{
				echo 'restricted2';
			}else{
				echo 'allowed';
			}

			//echo 'allowed';
		}



	}



	function ajaxSearchIndikator(){
		$keyword = $this->input->post('keyword');
		$sql = "Select kode_subkegiatan, nama_kegiatan, satuan FROM mst_indikator_kegiatan WHERE nama_kegiatan like '%$keyword%' AND kode_subkegiatan != 0";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		for ($i=0; $i < count($row) ; $i++) {
			$nama_kegiatan = $row[$i]->nama_kegiatan;
			$satuan = $row[$i]->satuan;
			$kode_subkegiatan = $row[$i]->kode_subkegiatan;
			echo '<div class="list-indikator" id="'.$kode_subkegiatan.'-+-'.$nama_kegiatan .'"> '.$nama_kegiatan .' <br>  Satuan : <strong>'.$satuan.'</strong> </div>';
		}

		echo '<script>

				$(".list-indikator").click(function(){
					$("#ajaxlist_indikator").hide();
					var data_indikator = $(this).attr("id");
					var pecah = data_indikator.split("-+-");
					var kode = pecah[0];
					var nama_indikator = pecah[1];
					$("#indikator").val(nama_indikator);
				});


		  </script>';


	}

	//SELECT ket FROM `ts_kinerja` WHERE id_pegawai = 761 AND ket like '%melakukan%' GROUP by ket
	function ajaxSearchKeterangan() {
		$keyword    = $this->input->post('keyword');

		$id_pegawai = $this->session->userdata('id_pegawai');
		$sql = "Select ket FROM ts_kinerja WHERE id_pegawai = '$id_pegawai' AND ket like '%$keyword%' GROUP by ket";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		for ($i=0; $i < count($row) ; $i++) {
			$ket = $row[$i]->ket;

			echo '<div class="list-keterangan" id="'.$ket.'"> '.$ket .'</div>';

		}

		echo '<script>

				$(".list-keterangan").click(function(){
					$("#list_keterangan").hide();
					var keterangan = $(this).attr("id");

					$("#keterangan").val(keterangan);


				});


		  </script>';
	}

	function ajaxGetKeteranganAktifitas(){
		$id_pegawai = $this->session->userdata('id_pegawai');
		$sql = "Select ket FROM ts_kinerja WHERE id_pegawai = '$id_pegawai' GROUP BY ket";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		for ($i=0; $i < count($row) ; $i++) {
			$ket = $row[$i]->ket;

			echo '<div class="list-keterangan" id="'.$ket.'"> '.$ket .'</div>';

		}

		echo '<script>

				$(".list-keterangan").click(function(){
					$("#list_keterangan").hide();
					var keterangan = $(this).attr("id");

					$("#keterangan").val(keterangan);


				});


		  </script>';
	}

	function ajaxGetFrequentAktifitas(){
		$id_pegawai = $this->session->userdata('id_pegawai');
		$sql = "Select nama_kegiatan, waktu_efektif FROM ts_kinerja WHERE id_pegawai = '$id_pegawai' ORDER BY tgl DESC LIMIT 10";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		for ($i=0; $i < count($row) ; $i++) {
			$nama_kegiatan = $row[$i]->nama_kegiatan;
			$waktu = $row[$i]->waktu_efektif;

			echo '<div class="list-aktifitas" id="'.$nama_kegiatan.'--+--'.$waktu .'"> '.$nama_kegiatan .' <br>  waktu : <strong>'.$waktu.'</strong> menit</div>';

		}

		echo '<script>

				$(".list-aktifitas").click(function(){
					$("#ajaxlist_aktifitas").hide();
					var data_aktifitas = $(this).attr("id");
					var pecah = data_aktifitas.split("--+--");
					var nama_aktifitas = pecah[0];
					var waktu_efektif = pecah[1];
					$("#aktifitas").val(nama_aktifitas);
					$("#waktu_efektif").val(waktu_efektif);

					$("#jam_mulai").focus();

				});


		  </script>';

	}

	function ajaxSearchAktifitas(){
		$nip = $this->session->userdata('nip');
		$keyword = $this->input->post('keyword');

		$row = $this->Kinerja_model->searchAktifitasUtamaPegawai($nip, $keyword);
		// $sql = "Select nama_kegiatan, waktu FROM mst_kegiatan WHERE nama_kegiatan like '%$keyword%'";
		// $qry = $this->db->query($sql);
		// $row = $qry->result();

		for ($i=0; $i < count($row) ; $i++) {
			$nama_kegiatan = $row[$i]->nama_kegiatan;
			$waktu = $row[$i]->waktu;

			echo '<div class="list-aktifitas" id="'.$nama_kegiatan.'--+--'.$waktu .'"> '.$nama_kegiatan .' <br>  waktu : <strong>'.$waktu.'</strong> menit</div>';

		}

		echo '<script>

				$(".list-aktifitas").click(function(){
					$("#ajaxlist_aktifitas").hide();
					var data_aktifitas = $(this).attr("id");
					var pecah = data_aktifitas.split("--+--");
					var nama_aktifitas = pecah[0];
					var waktu_efektif = pecah[1];
					$("#aktifitas").val(nama_aktifitas);
					$("#waktu_efektif").val(waktu_efektif);

					$("#jam_mulai").focus();

				});


		  </script>';


	}

	function simpan()  {

		   $tgl = $this->input->post('tanggal');
		   $tgl = format_db($tgl);
		   $id 	= $this->input->post('id');
		   $we  = $this->input->post('waktu_efektif');
		   $vol = $this->input->post('volume');
		   $total = $vol*$we;

		    if($we==0){
				echo json_encode(['status' => 'error1']);
			}

			if($vol==0){
				echo json_encode(['status' => 'error2']);
			}
			

			$data = [
				'id_pegawai'     => $this->session->userdata('id_pegawai'),
				'tgl'            => $tgl,
				'nama_kegiatan'  => $this->input->post('aktifitas'),
				'jam_mulai'      => $this->input->post('jam_mulai'),
				'jam_selesai'    => $this->input->post('jam_selesai'),
				'volume'         => $this->input->post('volume'),
				'waktu_efektif'  => $this->input->post('waktu_efektif'),
				'ket'     => $this->input->post('keterangan'),
				'total'          => $total
			];



			if ($id) {
				// update
				$this->db->where('id', $id);
				$this->db->update('ts_kinerja', $data);
			} else {
				// insert baru
				$this->db->insert('ts_kinerja', $data);
			}


			

		 echo json_encode(['status' => 'sukses']);

	}


	function insert_aktifitas_v2()  {


		$tgl = $this->input->post('tanggal');
		$vol = $this->input->post('vol');
		$waktu_efektif = $this->input->post('waktu_efektif');
		$total = $vol*$waktu_efektif;

		if($waktu_efektif==0){
			echo 'failed1';
			exit;
		}

		if($vol==0){
			echo 'failed2';
			exit;
		}

			$new_data = array(
				'id_pegawai' => $this->session->userdata('id_pegawai'),
				'tgl' => format_db($tgl),
				'jns_kegiatan' => 1,
				'id_indikator' => 0,
				'nama_kegiatan' => $this->input->post('aktifitas'),
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'volume' => $vol,
				'waktu_efektif' => $waktu_efektif,
				'total' =>  $total,
				'ket' =>  $this->input->post('keterangan')
			);

		$this->db->insert('ts_kinerja', $new_data);

		echo 'success';



	}

	function update_aktifitas_v2()  {
		$id = $this->input->post('id');
		$this->Kinerja_model->updateAktifitas($id);
		echo 'Data aktifitas berhasil diubah';


	}






	function insert_aktifitas(){
		$action = $this->input->post('action');
		$id_aktifitas = $this->input->post('id_aktifitas');


		if($id_aktifitas==''){
			$this->Kinerja_model->insertAktifitas();
			echo 'aktifitas berhasil disimpan';
		}else{
			$this->Kinerja_model->updateAktifitas($id_aktifitas);
			echo 'aktifitas berhasil diubah';
		}

	}


	function ajaxDeleteAktifitas(){
		$id_aktifitas = $this->input->post('id');

		$this->db->where('id', $id_aktifitas);
		$this->db->delete('ts_kinerja');
		echo 'Aktifitas telah berhasil dihapus.';
	}


	function deleteAktifitas($id){

		$this->db->where('id', $id);
		$this->db->delete('ts_kinerja');
		redirect('kinerja/list_kinerja');
	}

	function refreshInputanKinerja(){

		$id_pegawai = $this->session->userdata('id_pegawai');
		$periode    = $this->input->post('periode');

		$explod     = explode("-",$periode);
		$bulan = $explod[1];
		$tahun = $explod[0];

        $jumlahHariKerja = $this->Master_model->getMenitEfektifBulan($bulan, $tahun);
        $menitEfektifBulanan  = $jumlahHariKerja*300;



        $totalInput = $this->Kinerja_model->getJumlahInputPerBulan($id_pegawai, $periode);
        if($totalInput==0){
            $totalInput = 1;
        }

        $persenInput = ($totalInput/$menitEfektifBulanan)*100;
        if ($persenInput > 100) {
            $persenInput = 100;
        }


        $totalApprove  = $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);
        if($totalApprove==''){
            $totalApprove  = 0;
        }
        $persenApprove = ($totalApprove/$totalInput)*100;


        $totalReject = $this->Kinerja_model->getAktifitasByStatus($id_pegawai, $periode, 2);
        if($totalReject==''){
            $totalReject  = 0;
        }
        $persenReject = ($totalReject/$totalInput)*100;


		echo '
		  <div>
			<div class="flex items-center justify-between gap-4 mb-2">
				<h6>Total Input Aktifitas</h6>
				<span class="text-slate-500 dark:text-zink-200">'.$totalInput.'</span>
			</div>
			<div class="w-full h-3.5 rounded bg-slate-200 dark:bg-zink-600">
				<div class="h-3.5 rounded bg-custom-500" style="width:  '. $persenInput.'%"></div>
			</div>
		</div>

		<div>
			<div class="flex items-center justify-between gap-4 mb-2">
				<h6>Disetujui</h6>
				<span class="text-slate-500 dark:text-zink-200">'.$totalApprove.'</span>
			</div>
			<div class="w-full h-3.5 rounded bg-slate-200 dark:bg-zink-600">
				<div class="h-3.5 rounded bg-green-500" style="width: '.$persenApprove.'%"></div>
			</div>
		</div>
		<div>
			<div class="flex items-center justify-between gap-4 mb-2">
				<h6>Ditolak</h6>
				<span class="text-slate-500 dark:text-zink-200">'.$totalReject.'</span>
			</div>
			<div class="w-full h-3.5 rounded bg-slate-200 dark:bg-zink-600">
				<div class="h-3.5 rounded bg-red-500 dark:text-red-500" style="width: '.$persenReject.'%"></div>
			</div>
		</div>';

	}

	function hitung_volume()
	{
		$waktu_efektif = $this->input->post('waktu_efektif');
		$jam_mulai = $this->input->post('jam_mulai');
		$jam_selesai = $this->input->post('jam_selesai');

		$to_time   = strtotime($jam_mulai);
		$from_time = strtotime($jam_selesai);
		$diff      = round(abs($to_time - $from_time) / 60, 2);


		$wktu = number_format($diff / $waktu_efektif, 2);

		$expl = explode(".", $wktu);
		$ex1  = $expl[0];
		$ex2  = $expl[1];

		if ($ex2 < 60) {
			$volume = $ex1;
		} else {

			$volume = $ex1 + 1;
		}

		echo $volume;


	}


	function tarik_data_aktifitas(){
		$nip = $this->session->userdata('nip');
		$id_pegawai = $this->session->userdata('id_pegawai');

		$data_ekin = $this->Kinerja_model->getIDPegawaiekin($nip);

        $id_pegawai_ekin = $data_ekin[0]->id_pegawai;


		$getInputan = $this->Kinerja_model->getInputan($id_pegawai_ekin);


		if(count($getInputan) > 0){
			foreach ($getInputan as $akt) {

				$tgl =$akt->tgl;
				$jns_kegiatan =$akt->jns_kegiatan;
				$id_kegiatan =$akt->id_kegiatan;
				$jam_mulai =$akt->jam_mulai;
				$jam_selesai =$akt->jam_selesai;
				$volume =$akt->volume;
				$ket  =$akt->ket;
				$nama_kegiatan  =$akt->nama_kegiatan;
				$waktu  =$akt->waktu;

				$total = $waktu*$volume;


				$new_data = array(
					'id_pegawai' => $id_pegawai,
					'tgl' => $tgl,
					'jns_kegiatan' => $jns_kegiatan,
					'id_indikator' => 0,
					'nama_kegiatan' => $nama_kegiatan,
					'jam_mulai' => $jam_mulai,
					'jam_selesai' => $jam_selesai,
					'volume' => $volume,
					'waktu_efektif' => $waktu,
					'total' =>  $total,
					'ket' =>  $ket
				);

				$this->db->insert('ts_kinerja', $new_data);

			}



			echo '
			<h3>Success tarik data kinerja</h3>
			<a href="'.base_url().'kinerja/index">Kembali</a>';
		}else{
			echo '
			<h3>Gagal Tarik data</h3>
			<a href="'.base_url().'kinerja/index">Kembali</a>';
		}





	}



	function delete_aktifitas($id_pegawai){
		$sql = "SELECT id FROM ts_kinerja where id_pegawai = $id_pegawai AND tgl like '2024-01%'";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		for ($i=0; $i < count($row) ; $i++) {
			$this->db->where('id', $row[$i]->id);
			$this->db->delete('ts_kinerja');
		}
		#print_array($row);


		$this->session->set_flashdata('message','<strong>Success!!! </strong> Data aktifitas  telah dihapus');
		redirect('admin/pegawai/data_pegawai/non_pns');
	}


	function capaian_kinerja_v2(){

		$id_pegawai = $this->session->userdata('id_pegawai');
		$nip  = $this->session->userdata('nip');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}


		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));;



		$data['detail_pegawai']   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		//$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiTahunan($id_pegawai, $tahun);
		$data['dataRekap']  = $this->Laporan_model->getRekapTKDPegawaipertahun($nip, $tahun);


		$data['dataCapaian'] = $this->Kinerja_model->getDataCapaianPegawai($nip);
		$this->load->view('kinerja/capaian_kinerja', $data);
	}


	function capaian(){

		$id_pegawai = $this->session->userdata('id_pegawai');
		$nip  = $this->session->userdata('nip');

		$periode_bulan = $this->session->userdata('periode_bulan');
		$periode_tahun = $this->session->userdata('periode_tahun');
		if($periode_bulan=='') {
		  $bulan = date('m');
		  $tahun = date('Y');

		}else{
		  $bulan = $periode_bulan;
		  $tahun = $periode_tahun;
		}


		$periode = $tahun.'-'.$bulan;
		$periode = date('Y-m', strtotime($periode));;



		$data['detail_pegawai']   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$data['rekapTKD'] = $this->Laporan_model->getRekapTKDPegawai($nip, $periode);

		$data['master_cuti'] = $this->Master_model->getlistCuti();
		$this->load->view('kinerja/capaian_kinerja', $data);
	}

	function renkin(){

		$id_pegawai = $this->session->userdata('id_pegawai');
		$nip  = $this->session->userdata('nip');

		$data['renkin'] = $this->Kinerja_model->getDataRenkinPegawai($nip);
        $this->load->view('kinerja/renkin', $data);
    }



}
