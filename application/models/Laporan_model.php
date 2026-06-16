<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Laporan_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


	function getListPegawai(){
		$thn_anggaran = '2024';
		$this->db->order_by('nama', 'ASC');
		$this->db->select('nama, id_pegawai, nip');
		$this->db->from('mst_pegawai');
		$this->db->where('tahun_anggaran', $thn_anggaran);
		$this->db->where('jns_pegawai', 'non_pns');

		$query = $this->db->get();
		return $query->result();
	}

    function getCapaianTerkecil($periode)
    {
        $sql = "SELECT id, nama, capaian FROM ts_rekap_tkd WHERE periode = '$periode' AND capaian != 50 ORDER BY capaian ASC LIMIT 10 OFFSET 0";
        $qry = $this->db->query($sql);
        return $qry->result();
    }
	function getRekapAbsensiCuti($periode){

		$sql = "SELECT count(cuti) as cuti FROM ts_rekap_absensi  WHERE periode = '$periode' AND cuti != 0";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		$numCuti = $row[0]->cuti;
		return $numCuti;
	}

	function getRekapAbsensiIzin($periode){

		$sql = "SELECT count(izin) as izin FROM ts_rekap_absensi  WHERE periode = '$periode' AND izin != 0";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		$numCuti = $row[0]->izin;
		return $numCuti;
	}


	function getRekapAbsensiSakit($periode){

		$sql = "SELECT count(sakit) as sakit FROM ts_rekap_absensi  WHERE periode = '$periode' AND sakit != 0";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		$numCuti = $row[0]->sakit;
		return $numCuti;
	}




	function getRekapTelat($jns_absensi_filter, $periode, $id_pegawai = 0){


		$this->db->select($jns_absensi_filter);
		$this->db->from('ts_rekap_absensi');
		$this->db->where('periode', $periode);
		$this->db->where('id_pegawai', $id_pegawai);
		$query = $this->db->get();
		$row =  $query->result();

		if(!empty($row)){

			if($jns_absensi_filter=='telat'){
				$telat = $row[0]->telat;
			}else if($jns_absensi_filter=='izin'){
				$telat = $row[0]->izin;

			}else if($jns_absensi_filter=='sakit'){
				$telat = $row[0]->sakit;

			}else{
				$telat = $row[0]->cuti;
			}

		}else{
			$telat = 0;
		}

		return $telat;
	}



    function getBelumTTDSPJGaji(){
        return array();
    }


    function getBelumTTDSPJTKD(){
        return array();
    }
	function getDetailRekapGaji($id){
		$this->db->from('ts_listing_gaji');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->result();
	}


    function getDetailPegawaiByNIP($nip){
		$this->db->from('detail_pegawai');
		$this->db->where('nip', $nip);
		$query = $this->db->get();
		return $query->row();
	}



    function getDetailRekapGajiP3k($id){
		$this->db->from('tbl_gaji_p3k_pw');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}



	function getDetailRekapGaji13($id){
		$this->db->from('ts_listing_gaji13');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->result();
	}

	function getDetailRekapTKD($id, $table){
		$this->db->from($table);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	// function getDataDetailPegawai($id_pegawai){
	// 	$this->db->from('detail_pegawai');
	// 	$this->db->where('id_pegawai2', $id_pegawai);
	// 	$query = $this->db->get();
	// 	return $query->result();
	// }
	

	function getListingGaji($periode, $status='all'){

		$this->db->order_by('nama', 'ASC');
		$this->db->select('*');
		$this->db->from('ts_listing_gaji');
		$this->db->where('periode', $periode);
		if($status=='belum'){
			$this->db->where('ttd_spj', '');
		}else if($status=='sudah'){
			$this->db->where('ttd_spj !=', '');
		}



		$query = $this->db->get();
		return $query->result();
	}

	function getListingGaji13($periode, $status='all'){

		$this->db->order_by('nama', 'ASC');
		$this->db->select('*');
		$this->db->from('ts_listing_gaji13');
		$this->db->where('periode', $periode);
		if($status=='belum'){
			$this->db->where('ttd_spj', '');
		}else if($status=='sudah'){
			$this->db->where('ttd_spj !=', '');
		}



		$query = $this->db->get();
		return $query->result();

	}


	function getListingTKD($periode){

		$this->db->order_by('id', 'ASC');
		$this->db->select('*');
		$this->db->from('ts_rekap_tkd');
		$this->db->where('periode', $periode);
		$query = $this->db->get();
		return $query->result();
	}

	function getListingTKDP3k($periode){

		$this->db->order_by('id', 'ASC');
		$this->db->select('*');
		$this->db->from('ts_rekap_tkd_pppk');
		$this->db->where('periode', $periode);
		$query = $this->db->get();
		return $query->result();
	}
	

	function getDataListing($periode, $table){

		$this->db->order_by('id', 'ASC');
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where('periode', $periode);
		$query = $this->db->get();
		return $query->result();
	}

	function cekNamaPegawai($nama){


		$this->db->select('id_pegawai, nama');
		$this->db->from('mst_pegawai');
		$this->db->where('nama', $nama);
		$this->db->where('tahun_anggaran', 2024);
		$query = $this->db->get();
		return $query->result();
	}


	function getlastGaji($nik){
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1, 0);
		$this->db->select('*');
		$this->db->from('ts_listing_gaji');
		$this->db->where('nik', $nik);
		$query = $this->db->get();

		return $query->result();
	}


	function getLastTKD($nip, $jns_pegawai='non_pns'){

		$table = $jns_pegawai=='non_pns'?'ts_rekap_tkd':'ts_rekap_tkd_pppk';
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1, 0);
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where('nip', $nip);
		$query = $this->db->get();

		return $query->row();

	}

	// function getlastTKD($nama){
	// 	$this->db->order_by('id', 'DESC');
	// 	$this->db->limit(1, 0);
	// 	$this->db->select('*');
	// 	$this->db->from('ts_rekap_tkd');
	// 	$this->db->where('nama', $nama);
	// 	$query = $this->db->get();

	// 	return $query->result();
	// }

	function getlastTHR($nama){
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1, 0);
		$this->db->select('*');
		$this->db->from('ts_listing_thr');
		$this->db->where('nama', $nama);
		$query = $this->db->get();

		return $query->result();
	}

	function getlastGaji13($nik){
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1, 0);
		$this->db->select('*');
		$this->db->from('ts_listing_gaji13');
		$this->db->where('nik', $nik);
		$query = $this->db->get();

		return $query->result();
	}


	function getDataTHR($nama){


		//echo $nama;
		$this->db->order_by('id', 'ASC');
		$this->db->select('*');
		$this->db->from('ts_listing_thr');
		$this->db->where('nama', $nama);
		$query = $this->db->get();

		//echo $this->db->last_query();
		return $query->result();
	}



	function getNpwpByNip($nip){

		$this->db->select('npwp');
		$this->db->order_by('periode', 'DESC');
		$query = $this->db->get_where('ts_rekap_tkd', array('nip'=> $nip), 1,0);

		$row = $query->result();

	//	print_array($row);

		if(!empty($row)){
			$npwp = $row[0]->npwp;
		}else{
			$npwp = 0;
		}
		return $npwp;
	}

	function getNoHP($nip){

		$this->db->select('no_hp');
		$this->db->order_by('periode', 'DESC');
		$query = $this->db->get_where('ts_rekap_tkd', array('nip'=> $nip), 1,0);

		$row = $query->result();

	//	print_array($row);

		if(!empty($row)){
			$no_hp = $row[0]->no_hp;
		}else{
			$no_hp = 0;
		}
		return $no_hp;
	}


	function getRekapTKDPegawaipertahun($nip, $tahun){

	
		//echo $nip;
		$this->db->select('*');
		$this->db->from('ts_rekap_tkd');
		$this->db->where('nip', $nip);
		$this->db->like('periode', $tahun,'after');
		$query = $this->db->get();

		$row = $query->result();

		//echo $this->db->last_query();
		return $row;
	}

	function getGajiPegawaipertahun($nik, $tahun){

		$this->db->select('*');
		$this->db->from('ts_listing_gaji');
		$this->db->where('nik', $nik);
		$this->db->like('periode', $tahun,'after');
		$query = $this->db->get();

	
		return $query->result();
	}
    function getGajiPegawaipertahunP3K($nip, $tahun){

		$this->db->select('*');
		$this->db->from('tbl_gaji_p3k_pw');
		$this->db->where('nipppk_pw', $nip);
        $this->db->where('tahun', $tahun);
		$query = $this->db->get();

	
		return $query->result();
	}


	function getRekapTKDPegawai($nip, $periode){

		$this->db->select('*');
		$this->db->from('ts_rekap_tkd');
		$this->db->where('nip', $nip);
		$this->db->where('periode', $periode);
		$query = $this->db->get();
		return $query->result();
	}


	function potongan_thr()
	{

		$this->db->select('*');
		$this->db->from('pot_thr');
		$query = $this->db->get();
		$row = $query->result();

		return $row;
	}

	function getPotonganTHR($id_pegawai)
	{

		$this->db->select('*');
		$this->db->from('pot_thr');
		$this->db->where('id_pegawai', $id_pegawai);
		$query = $this->db->get();
		$row = $query->result();

		return $row;
	}



	function getDataPegawai_detail()
	{

		$this->db->select('*');
		$this->db->from('mst_pegawai_detail');
		$this->db->limit(400, 0);
		$query = $this->db->get();
		$row = $query->result();

		return $row;
	}

	function getDataTblDetail($id_pegawai)
	{

		$this->db->select('*');
		$this->db->from('mst_pegawai_detail');
		$this->db->where('id_pegawai2', $id_pegawai);
		$query = $this->db->get();
		$row = $query->result();

		return $row;
	}

	function getDataPajak($nama)
	{

		$this->db->select('*');
		$this->db->from('tbl_import_pajak');
		$this->db->where('nama', $nama);
		$query = $this->db->get();
		$row = $query->result();


		return $row;
	}



	function getDendaSTR($id_pegawai, $bulan, $tahun)
	{
		$bulanTahun = $bulan . '-' . $tahun;
		$this->db->select('*');
		$this->db->from('ts_penambahan');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('debet_kredit', 2);
		$this->db->where('bulan_tahun', $bulanTahun);
		//$this->db->where('Denda 15% STR/SIP Kadaluarsa/tidak ada');
		$query = $this->db->get();
		$row = $query->result();


		return $row;
	}



	function cekPenambahPengurang($id_pegawai, $bulan, $tahun)
	{

		$bulanTahun = $bulan . '-' . $tahun;

		$this->db->select('*');
		$this->db->from('ts_penambahan');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('bulan_tahun', $bulanTahun);
		$query = $this->db->get();
		$row = $query->result();


		return $row;
	}

	function createDataDetailPegawai($id_pegawai, $nama, $nik, $jabatan, $tgl_masuk, $status, $Pendidikan)
	{

		$array = array(
			'id_pegawai' => $id_pegawai,
			'id_pegawai2' => $id_pegawai,
			'nama' => $nama,
			'nik' => $nik,
			'jabatan' => $jabatan,
			'pendidikan' => $Pendidikan,
			'tgl_masuk' => $tgl_masuk,
			'status' => $status
		);


		$this->db->insert('mst_pegawai_detail', $array);
		return true;
	}


	function createDataPerhitunganTKD($id_pegawai)
	{

		$array = array(
			'id_pegawai' => $id_pegawai,
			'gaji_pokok' => 0,
			'pengali' => 0,
			'total' => 0,
			'bpjs_kes' => 0,
			'bpjs_kerja' => 0
		);


		$this->db->insert('perhitungan_tkd', $array);
		return true;
	}



	function cekDataTempTkd($id_pegawai, $bulan, $tahun)
	{

		$this->db->select('id');
		$this->db->from('ts_temp_tkd');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('bulan', $bulan);
		$this->db->where('tahun', $tahun);
		$query = $this->db->get();
		$row = $query->result();


		return $row;
	}

	function updateTableRekapTKD($id_pegawai)
	{

		$tahun 		 = $this->session->userdata('periode_tahun');
		$bulan 		 = $this->session->userdata('periode_bulan');
		$totalJam    = $this->master_model->getTotalJamkerjaPerbulan($bulan, $tahun);
		$rekap_tkd   = $this->master_model->getDataRekapTKD($id_pegawai, $bulan, $tahun);
		$perilaku   = $this->master_model->getPenilaianPerilaku($id_pegawai, $bulan, $tahun);


		$id 		 = $rekap_tkd[0]->id;
		$cuti 		 = $rekap_tkd[0]->cuti;
		$izin 		 = $rekap_tkd[0]->izin;
		$sakit 		 = $rekap_tkd[0]->sakit;
		$telat 		 = $rekap_tkd[0]->telat;
		$total_menit = $rekap_tkd[0]->total_menit;
		$pdp 		 = $rekap_tkd[0]->pdp;

		$serapan 	 = SERAPAN;

		$totalMenitIzin  = $izin * 300;
		$totalMenitSakit = $sakit * 300;
		$totalMenitCuti  = $cuti * 300;
		$totalMenitPDP  = $pdp * 300;

		$waktuEfektif  = $totalJam - $total_menit;

		$tgl_dari        = $tahun . '-' . $bulan . '-01';
		$LastDate        = date('t', strtotime($tgl_dari));
		$tgl_sampai      = $tahun . '-' . $bulan . '-' . $LastDate;
		$input_accept    = $this->master_model->countTotalInputKinerja($id_pegawai, $tgl_dari, $tgl_sampai, 1);

		$jumlahAktifitas = $input_accept + $totalMenitCuti + $totalMenitPDP;

		$min = $waktuEfektif;
		if ($waktuEfektif > $jumlahAktifitas) {
			$min = $input_accept;
		}


		$nilaiAktifitas  = round(($min / $totalJam) * 100, 2);
		$AktifitasBobot  = round(($nilaiAktifitas * 70) / 100, 2);

		$totalCapaian = $AktifitasBobot + $serapan + $perilaku;


		$newArray =  array(
			'perilaku'  => $perilaku,
			'bobot_aktifitas'  => $AktifitasBobot,
			'serapan'  => $serapan,
			'total'  => $totalCapaian,
		);
		$this->db->where('id', $id);
		$this->db->update('ts_rekap_tkd', $newArray);

		return true;
	}

	function updateDataLaporanPerPegawai($id_pegawai)
	{
		$tahun = $this->session->userdata('periode_tahun');
		$bulan = $this->session->userdata('periode_bulan');
		$bpjs_kes_val      = 0;
		$bpjs_kerja_val    = 0;
		$today 			   = date('Y-m-d');
		$pph21      	   = 0.05; //pph21
		$bpjskes    	   = 0; //bpjskes
		$jamsostek  	   = 0; //jamsostek

		$totalMenit 	   = 0;

		$totalPPH 		   = 0;
		$totalBPJSKES      = 0;
		$totalTKDALL       = 0;
		$totalTKDPOKOK     = 0;
		$date_update       = date('Y-m-d H:i:s');
		$totalJam          = $this->master_model->getTotalJamkerjaPerbulan($bulan, $tahun);
		$data_pegawai      = $this->master_model->detailPegawai($id_pegawai);

		$nama 		       = $data_pegawai[0]->nama;
		$jabatan 		   = $data_pegawai[0]->jabatan;
		$status_kerja 	   = $data_pegawai[0]->status_kerja;

		$gp 	           = $data_pegawai[0]->gaji_pokok;
		$pengali           = $data_pegawai[0]->pengali;

		$total_gp          = $gp * $pengali;



		$data_pegawai2     = $this->Laporan_model->getDataDetailPegawai($id_pegawai);
		$tgl_masuk 		   = $data_pegawai2[0]->tgl_masuk;

		$masaKerja         = datediff('m', $tgl_masuk, $today); //dalam hari

		$getRakapTKD = $this->master_model->getDataRekapTKD($id_pegawai, $bulan, $tahun);
		if (!empty($getRakapTKD)) {
			$nilaiAktifitas  = $getRakapTKD[0]->nilai_aktifitas;
			$bobot_aktifitas = $getRakapTKD[0]->bobot_aktifitas;
			$perilaku 		 = $getRakapTKD[0]->perilaku;
			$serapan 		 = $getRakapTKD[0]->serapan;
			$cuti		     = $getRakapTKD[0]->cuti;
			$izin 			 = $getRakapTKD[0]->izin;
			$sakit 		 	 = $getRakapTKD[0]->sakit;
			$telat 		 	 = $getRakapTKD[0]->telat;
			$totalTelat 	 = $getRakapTKD[0]->total_menit;
		} else {
			$nilaiAktifitas  = 0;
			$bobot_aktifitas = 0;
			$perilaku 		 = 0;
			$serapan 		 = 0;
			$cuti		     = 0;
			$izin 			 = 0;
			$sakit 		 	 = 0;
			$telat 		 	 = 0;
			$totalTelat 	 = 0;
		}


		if ($status_kerja == 1) {
			$total   = $bobot_aktifitas + $perilaku + $serapan; //total capaian

			if ($id_pegawai == 391) {
				$total = 0;
			}
		} else if ($status_kerja == 2) {
			$total   = 0; //pegawai yang tidak aktif/keluar
		} else {
			$total   = 50; //pegawai yang hamil dapet 50%
		}


		$getDataTKD = $this->master_model->getDataTKD($id_pegawai);


		if (!empty($getDataTKD)) {
			//$gp         = $getDataTKD[0]->gaji_pokok;
			//$total_gp   = $getDataTKD[0]->total;// total stelah hitung pengali
			$bpjs_kerja = $getDataTKD[0]->bpjs_kerja;
			$bpjs_kes   = $getDataTKD[0]->bpjs_kes;


			if ($masaKerja < 3) {

				$totalHitung = $total_gp * 0.75;
				//total TKD
				$tkd_bruto     = ($totalHitung * $total) / 100; //total sebelum pemotongan
			} else {
				//total TKD
				$tkd_bruto     = ($total_gp * $total) / 100; //total sebelum pemotongan
			}

			$totalTKDPOKOK = $totalTKDPOKOK + $tkd_bruto;

			//5% dari total pengali TKD
			if ($id_pegawai == 249) {
				$pajak_pph21      = 0; //karna gaji nya di bawah PTKP
			} else {
				$pajak_pph21      = round($tkd_bruto * $pph21);
			}


			if ($bpjs_kes == 1) {
				//2% dari gaji pokok
				$bpjskes = round($gp * 0.02);
			}

			if ($bpjs_kerja == 1) {
				//3% dari gaji pokok
				$jamsostek = round($gp * 0.03);
			}

			$total_potongan = $pajak_pph21 + $bpjskes + $jamsostek;

			//penerimaan = total tkd-total potongan
			$totalPenerimaan = $tkd_bruto - $total_potongan;
		} else {
			$total_gp = 0;
			$total1   = 0;
			$pajak    = 0;
			$total_potongan = $pajak;
			$totalPenerimaan = $total_gp - $pajak;
		}




		$data = array(
			'id_pegawai' => $id_pegawai,
			'nama' => strtoupper($nama),
			'Jabatan' => $jabatan,
			'Cuti' => $cuti,
			'izin' => $izin,
			'sakit' => $sakit,
			'telat' => $totalTelat,
			'total' => $totalMenit,
			'tkd_pokok' => $total_gp,
			'kinerja' => $bobot_aktifitas,
			'perilaku' => $perilaku,
			'serapan' => $serapan,
			'total_kinerja' => $total,
			'total_tkd' => $tkd_bruto,
			'pph' => $pajak_pph21,
			'bpjs_kes' => $bpjskes,
			'jamsostek' => $jamsostek,
			'thp' => $totalPenerimaan,
			'masa_kerja' => $masaKerja,
			'bulan' => $bulan,
			'tahun' => $tahun,
			'date_update' => $date_update
		);


		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('bulan', $bulan);
		$this->db->where('tahun', $tahun);
		$this->db->update('ts_temp_tkd', $data);


		//print_array($data);
		return true;
	}

	function getDataLaporan($bulan, $tahun, $id_pegawai)
	{

		$this->db->select('*');
		$this->db->from('ts_temp_tkd');
		$this->db->where('bulan', $bulan);
		$this->db->where('tahun', $tahun);
		$this->db->where('id_pegawai', $id_pegawai);
		$query = $this->db->get();
		$row = $query->result();
		return $row;
	}

	function getNorekPegawai($id_pegawai)
	{

		$this->db->select('no_rekening, npwp');
		$this->db->from('mst_pegawai');
		$this->db->where('id_pegawai', $id_pegawai);
		$query = $this->db->get();
		$row = $query->result();
		return $row;
	}

	function getCapaianTerendah($id_validator, $bulan, $tahun, $offset = 0)
	{
		if ($id_validator == 355) {
			$AND_validator = "";
		} else {
			$AND_validator = " AND  a.id_validator = '$id_validator'";
		}
		$sql = "SELECT a.id_pegawai, a.nama, b.total, b.tahun, b.bulan
		FROM mst_pegawai a
		LEFT JOIN ts_rekap_tkd b ON a.id_pegawai=b.id_pegawai
		WHERE  b.tahun = $tahun AND bulan = $bulan $AND_validator
		ORDER BY total ASC LIMIT 10 OFFSET $offset";

		$query = $this->db->query($sql);
		$row = $query->result();
		return $row;
	}



	function getTelatTertinggi($id_validator, $bulan, $tahun, $offset = 0, $case = 'terlambat')
	{
		if ($id_validator == 355) {
			$AND_validator = "";
		} else {
			$AND_validator = " AND  a.id_validator = '$id_validator'";
		}
		$sql = "SELECT a.id_pegawai, a.nama, b.terlambat, b.pc, b.sakit, b.izin
		FROM mst_pegawai a
		LEFT JOIN ts_rekap_absensi b ON a.id_pegawai=b.id_pegawai
		WHERE  b.tahun = $tahun AND bulan = $bulan $AND_validator
		ORDER BY $case DESC LIMIT 10 OFFSET $offset";

		$query = $this->db->query($sql);
		$row = $query->result();
		return $row;
	}




	// function getLaporanCapaian($id_validator, $bulan, $tahun)
	// {

	// 	if ($id_validator == 355) //272 id pegawai (bu Nur)
	// 	{
	// 		$andWhere = '';
	// 	} else {
	// 		$andWhere = "AND a.id_validator = $id_validator";
	// 	}
	// 	//$andWhere = '';

	// 	$sql = "SELECT a.nama, a.id_pegawai, a.nip, a.status_kerja, b.*
	// 	FROM mst_pegawai a
	// 	LEFT JOIN ts_rekap_tkd b ON a.id_pegawai = b.id_pegawai
	// 	WHERE  (b.bulan='$bulan' AND b.tahun = '$tahun') $andWhere";


	// 	$query = $this->db->query($sql);
	// 	$row = $query->result();
	// 	return $row;
	// }


	function getLaporanCapaian($id_validator, $bulan, $tahun)
	{



		if ($id_validator == 355) //272 id pegawai (bu Nur)
		{
			$andWhere = '';
		} else {
			$andWhere = "AND a.id_validator = $id_validator";
		}


		$sql = "SELECT a.nama, a.id_pegawai, a.nip, a.status_kerja
		FROM mst_pegawai a WHERE  status_kerja <> 2 $andWhere  AND status_pegawai = 2 ORDER BY nama ASC";

		$query = $this->db->query($sql);
		$row = $query->result();
		return $row;
	}


	function getTotalCapaian($id_pegawai, $bulan, $tahun)
	{

		$this->db->select('*');
		$this->db->from('ts_rekap_tkd');
		$this->db->where('bulan', $bulan);
		$this->db->where('tahun', $tahun);
		$this->db->where('id_pegawai', $id_pegawai);
		$query = $this->db->get();
		$row = $query->result();

		#print_array($row);

		if (!empty($row)) {
			$totalCapaian = $row[0]->total;
		} else {
			$totalCapaian = 0;
		}

		return $totalCapaian;
	}

	function cekDatalistingTKD($nama, $periode)
	{
		$qry = $this->db->get_where('ts_rekap_tkd', array('nama' => $nama, 'periode' => $periode));
		$row = $qry->result();
		return $row;
	}

	function save_import_gaji($sheet, $periode){
		$num = 0;
		$numrow = 1;
		$data = [];
		$newData = [];

		 foreach ($sheet as $row) {
                if ($numrow > 0) {
                    $nama = $row["B"];
                    $jabatan = $row["C"];
                    $tmt_tgl = $row["D"];
                    $tmt_bln = $row["E"];
                    $tmt_thn = $row["F"];
                    $status_kawin = $row["G"];
                    $nik = $row["H"];
                    $gp = $row["I"]; //gaji pokok
                    $tunj_suami = $row["J"];
                    $tunj_anak1 = $row["K"];
                    $tunj_anak2 = $row["L"];
                    $jumlah_gaji = $row["M"]; //bruto
                    $ptkp = $row["N"];
                    $tarif_ter = $row["O"];
                    $pajak = $row["P"];
                    $netto = $row["Q"];
                    $no_rekening = $row["R"];

                    $no_bulan = getNomorBulan($tmt_bln);

                    $tmt = $tmt_tgl . "-" . $no_bulan . "-" . $tmt_thn;
                    $tmt_format = format_db($tmt);

                    $pajak = str_replace("Rp ", "", $pajak);

                    if ($tmt_format != "1970-01-01") {
                        $newData[] = [
                            "periode" => $periode,
                            "nama" => $nama,
                            "jabatan" => $jabatan,
                            "tmt" => $tmt_format,
                            "status_kawin" => $status_kawin,
                            "nik" => clear_tags($nik),
                            "gaji_pokok" => clear_tags($gp),
                            "tunj_suami" => clear_tags($tunj_suami),
                            "tunj_anak1" => clear_tags($tunj_anak1),
                            "tunj_anak2" => clear_tags($tunj_anak2),
                            "bruto" => clear_tags($jumlah_gaji),
                            "ptkp" => clear_tags($ptkp),
                            "tarif_ter" => $tarif_ter,
                            "pajak" => clear_tags($pajak),
                            "netto" => clear_tags($netto),
                            "no_rekening" => $no_rekening,
                            "ttd_spj" => "",
                        ];
                    }
                }
            } //close loop

			$insert = $this->db->insert_batch("ts_listing_gaji", $newData);

			return $insert;

	}

	function save_import_gaji13($sheet, $periode){


		$num = 0;
		$numrow = 1;
		$data = [];
		$newData = [];

		 foreach ($sheet as $row) {
                if ($numrow > 0) {
                    $nama = $row["B"];
                    $jabatan = $row["C"];
                    $tmt_tgl = $row["D"];
                    $tmt_bln = $row["E"];
                    $tmt_thn = $row["F"];
                    $status_kawin = $row["G"];
                    $nik = $row["H"];
                    $gp = $row["I"]; //gaji pokok
                    $tunj_suami = $row["J"];
                    $tunj_anak1 = $row["K"];
                    $tunj_anak2 = $row["L"];
                    //$jumlah_gaji = $row["M"]; //bruto
                    $thr_gaji = $row["N"];
                    $tkd = $row["O"];
                    $total = $row["P"]; //total gaji & tkd
                    $pajak = $row["Q"];
                    $netto = $row["R"];  //jumlah diterima setelah dipotong pajak
					$no_rekening = $row["T"]; 

                    $no_bulan = getNomorBulan($tmt_bln);

                    $tmt = $tmt_tgl . "-" . $no_bulan . "-" . $tmt_thn;
                    $tmt_format = format_db($tmt);

                    $pajak = str_replace("Rp ", "", $pajak);

                    if ($tmt_format != "1970-01-01") {
                        $newData[] = [
                            "periode" => $periode,
                            "nama" => $nama,
                            "jabatan" => $jabatan,
                            "tmt" => $tmt_format,
                            "status_kawin" => $status_kawin,
                            "nik" => clear_tags($nik),
                            "gaji_pokok" => clear_tags($gp),
                            "tunj_suami" => clear_tags($tunj_suami),
                            "tunj_anak1" => clear_tags($tunj_anak1),
                            "tunj_anak2" => clear_tags($tunj_anak2),
                            "thr_gaji" => round($thr_gaji),
                            "tkd13" => round($tkd),
							"total" => round($total),
                            "pajak" => round($pajak),
                            "netto" => round($netto),
                            "no_rekening" => $no_rekening,
                            "ttd_spj" => "",
                        ];
                    }
                }
            } //close loop


			// print_array($newData);
			// exit;
			$insert = $this->db->insert_batch("ts_listing_gaji13", $newData);

			//exit;
			return $insert;

	}



	function getListingTKDByStatus($periode, $status) {
		$periode = date('Y-m', strtotime($periode));
		$this->db->select('id');
		$query = $this->db->get_where('ts_rekap_tkd', array('periode'=> $periode, 'status'=>$status));
		$num   = $query->num_rows();
		return $num;
	}


	function cekDataTKDPegawai($id_pegawai, $bulan, $tahun)
	{
		$sql = "SELECT id FROM ts_temp_tkd WHERE id_pegawai = $id_pegawai AND bulan = '$bulan' AND tahun = '$tahun' ";
		$query = $this->db->query($sql);
		$row = $query->num_rows();

		if ($row == 0) {
			return false;
		} else {
			return true;
		}
	}


	function getDataTKDPegawai($id_pegawai, $bulan, $tahun)
	{
		$sql = "SELECT a.*, b.id AS id_jabatan, c.npwp, c.no_rekening
		FROM ts_temp_tkd a
		LEFT JOIN mst_jabatan b ON a.Jabatan = b.nama
		LEFT JOIN mst_pegawai c ON a.id_pegawai = c.id_pegawai
		WHERE a.id_pegawai = $id_pegawai AND bulan = '$bulan' AND tahun = '$tahun' ";

		$query = $this->db->query($sql);
		$row = $query->result();

		return $row;
	}

	function getListTKD($bulan, $tahun, $orderBy = 'total', $sortBy = 'DESC')
	{
		$id_user   = $this->session->userdata('id_user');
		if ($id_user == 272) //272 id pegawai (bu Nur)
		{
			$andWhere = '';
		} else {
			$andWhere = "AND c.id_validator = $id_user";
		}

		$sql = "SELECT a.*, b.id AS id_jabatan, c.npwp, c.no_rekening, c.gaji_pokok, c.pengali, c.tgl_masuk
		FROM ts_temp_tkd a
		LEFT JOIN mst_jabatan b ON a.Jabatan = b.nama
		LEFT JOIN mst_pegawai c ON a.id_pegawai = c.id_pegawai
		WHERE bulan = '$bulan' AND tahun = '$tahun' $andWhere
		GROUP BY a.nama ORDER BY $orderBy $sortBy, tgl_masuk ASC ";

		$query = $this->db->query($sql);
		$row = $query->result();
		return $row;
	}


	function getListPegawaiByValidator($id_validator)
	{
		$sql = "SELECT a.nama, b.nama AS jabatan, a.status_kerja, a.id_pegawai, a.nip, a.tgl_masuk, a.id_puskesmas as puskesmas
		FROM mst_pegawai a
		LEFt JOIN mst_jabatan b ON a.jabatan = b.id
		WHERE id_validator = $id_validator  AND status_pegawai = 2 AND status_kerja != 2 ORDER BY a.jabatan ASC";

		$query = $this->db->query($sql);
		$row = $query->result();
		return $row;
	}



	function getListTKDValid($bulan, $tahun, $orderBy = 'total', $sortBy = 'DESC')
	{
		$sql = "SELECT a.*, b.id AS id_jabatan
		FROM ts_temp_tkd a
		LEFT JOIN mst_jabatan b ON a.Jabatan = b.nama
		LEFT JOIN mst_pegawai c ON a.id_pegawai = c.id_pegawai
		WHERE bulan = '$bulan' AND tahun = '$tahun'
		GROUP BY a.nama ORDER BY $orderBy $sortBy, masa_kerja DESC ";


		$query = $this->db->query($sql);
		$row = $query->result();
		return $row;
	}


	/*function getListTKD($bulan, $tahun)
	{

		$this->db->select('*');
		$this->db->from('ts_temp_tkd');
		$this->db->where('bulan', $bulan);
		$this->db->where('tahun', $tahun);
		$this->db->order_by('Jabatan ASC, masa_kerja DESC');
		$query = $this->db->get();
		$row = $query->result();
		return $row;
	}
	*/


	function getDataDetailPegawai($nip)
	{

		$sql = "SELECT * FROM detail_pegawai WHERE nip = '$nip'";
		$qry = $this->db->query($sql);
		$row = $qry->row();
		return $row;
	}


	function getDataCapaian($periode){
		
       	$this->db->select('a.id, a.periode, a.nip, a.tkd_pokok, b.total_capaian');
        $this->db->from('ts_rekap_tkd a');
        $this->db->where('a.periode', $periode);
        $this->db->join(
        'tbl_capaian b',
        'a.periode = b.periode AND a.nip = b.nip', 'left'
        );
        $query = $this->db->get();

        $result = $query->result();

	
        return $result;
	}


	// //ambil data dari aplikasi penggajian
	// function getDataPerhitunganTKDPegawai($nip, $bulan, $tahun){
	//     	ini_set("allow_url_fopen", 1);
	// 		$url = 'http://puskesmascilincing.id/e-penggajian/cek_gaji/getDataPerhitunganTKD/'.$nip.'/'.$bulan.'/'.$tahun;
	// 		$ch = curl_init();
	// 		// IMPORTANT: the below line is a security risk, read https://paragonie.com/blog/2017/10/certainty-automated-cacert-pem-management-for-php-software
	// 		// in most cases, you should set it to true
	// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 		curl_setopt($ch, CURLOPT_URL, $url);
	// 		$result = curl_exec($ch);
	// 		curl_close($ch);


	// 		$obj = json_decode($result);

	//     	return $obj;

	// }


}
