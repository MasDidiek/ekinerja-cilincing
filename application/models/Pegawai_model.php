<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Pegawai_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


	protected $table = 'mst_pegawai';

	function getPegawaiRB($id_puskesmas, $id_poli){

        $this->db->select('id_pegawai, nama, pin, jns_pegawai');
        $this->db->from('mst_pegawai');
        $this->db->where('id_poli', $id_poli);
        $this->db->where('id_puskesmas', $id_puskesmas);

        $this->db->group_start();
        $this->db->where('status_kerja !=', 0);
        $this->db->where('jns_pegawai', 'non_pns');
        $this->db->or_where('jns_pegawai', 'pppk_pw');
        $this->db->group_end();

        $qry = $this->db->get();
        $row = $qry->result();
        return $row;
	}

	function getPegawaiByPoli($id_poli)
	{
        $this->db->select('id_pegawai, nama, pin, jns_pegawai');
        $qry = $this->db->get_where('mst_pegawai', array('id_poli' => $id_poli, 'jns_jam_kerja' => 'shift', 'status_kerja !=' => 0));
        $row = $qry->result();
		return $row;
	}

	function getPegawaiByIDPuskesmas($id_puskesmas, $jns_pegawai, $select = '*')
	{

		// $this->db->select($select);
		// $qry =  $this->db->get_where('mst_pegawai', array('status_kerja !=' => 0, 'id_puskesmas' => $id_puskesmas, 'jns_pegawai' => $jns_pegawai));
		// $row = $qry->result();


		$this->db->where('p.id_puskesmas', $id_puskesmas);
		$this->db->where('p.jns_pegawai', $jns_pegawai);
		$this->db->where('p.status_kerja !=', 0);

		$this->db->select('p.*, j.nama AS jabatan');
		$this->db->from('mst_pegawai p');
		$this->db->join('mst_jabatan j', 'p.id_jabatan = j.id');
		$query = $this->db->get();
		$row   = $query->result();

		//echo $this->db->last_query();
		return $row;
	}
	function getPegawaiByKlaster($id_puskesmas, $klaster, $jns_pegawai, $select = '*')
	{

		// $this->db->select($select);
		// $qry =  $this->db->get_where('mst_pegawai', array('status_kerja !=' => 0, 'id_puskesmas' => $id_puskesmas, 'klaster' => $klaster, 'jns_pegawai' => $jns_pegawai));
		// $row = $qry->result();

		// //echo $this->db->last_query();
		// return $row;



		$this->db->where('p.id_puskesmas', $id_puskesmas);
		$this->db->where('p.klaster', $klaster);
		$this->db->where('p.jns_pegawai', $jns_pegawai);
		$this->db->where('p.status_kerja !=', 0);

		$this->db->select('p.*, j.nama AS jabatan');
		$this->db->from('mst_pegawai p');
		$this->db->join('mst_jabatan j', 'p.id_jabatan = j.id');
		$query = $this->db->get();
		$row   = $query->result();

		//echo $this->db->last_query();
		return $row;
	}



	function getDataPegawaiAktif($jns_pegawai)
	{
		$this->db->select('*');
		$qry =  $this->db->get_where('mst_pegawai', array('tahun_anggaran' => 2024, 'status_kerja !=' => 0, 'jns_pegawai' => $jns_pegawai));
		$row = $qry->result();
		return $row;
	}


	function countPegawaiAktif($thn, $jns_pegawai)
	{
		$this->db->select('id_pegawai');
		$qry =  $this->db->get_where('mst_pegawai', array('tahun_anggaran' => $thn, 'status_kerja !=' => 0, 'jns_pegawai' => $jns_pegawai));
		$num = $qry->num_rows();
		return $num;
	}


	function cekDataGajipegawai($nip, $tahun)
	{
		$this->db->select('id');
		$qry =  $this->db->get_where('tbl_riwayat_gaji', array('nip' => $nip, 'tahun' => $tahun));
		$row = $qry->result();

		if (!empty($row)) {
			$id = $row[0]->id;
		} else {
			$id = 0;
		}

		return $id;
	}

	function getAtasanPegawai($id_pegawai)
	{
		$this->db->select('id_validator');
		$qry =  $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $qry->result();
		$id_validator = $row[0]->id_validator;
		return $id_validator;
	}

	function getTotalPegawaiByJabatan($jabatan = 1)
	{
		$this->db->where('tahun_anggaran', '2024');
		$this->db->where('id_jabatan', $jabatan);
		$query = $this->db->get('mst_pegawai');
		return $query->num_rows();
	}


	function getTotalPegawai($jns = 'PNS')
	{
		$this->db->where('jns_pegawai', $jns);
		$this->db->where('tahun_anggaran', '2024');
		$query = $this->db->get('mst_pegawai');
		return $query->num_rows();
	}


	function getPegawaiByNIK($nik)
	{
		$this->db->where('no_ktp', $nik);
		$this->db->select('b.nip, a.no_rekening, b.nama, b.id_pegawai');
		$this->db->from('detail_pegawai a');
		$this->db->join('mst_pegawai b', 'a.nip = b.nip');

		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}

	function getPegawaiforListingTKD($thn_anggaran, $limit = 500)
	{


		$this->db->where('jns_pegawai', 'non_pns');
		$this->db->where('status_kerja >', 0);
		$this->db->select('mst_pegawai.*,mst_jabatan.nama AS jabatan, detail_pegawai.npwp, detail_pegawai.no_rekening, d.*');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
		$this->db->join('detail_pegawai', 'mst_pegawai.nip = detail_pegawai.nip', 'left');
		$this->db->join('tbl_riwayat_gaji d', 'mst_pegawai.nip = d.nip', 'left');
		$qry = $this->db->get();

		//echo $this->db->last_query();
		$row = $qry->result();
		return $row;
	}



	// function getPegawaiforListingTKD($thn_anggaran, $limit = 500){
	// 	   $this->db->limit($limit);
	// 	    $this->db->order_by('mst_pegawai.id_jabatan', 'ASC');
	// 		$this->db->order_by('mst_pegawai.tgl_masuk', 'ASC');
	// 		$this->db->where('jns_pegawai', 'non_pns');
	// 		$this->db->where('status_kerja >', 0);
	// 		$this->db->where('tahun_anggaran', $thn_anggaran);
	// 		$this->db->select('mst_pegawai.*,mst_jabatan.nama AS jabatan, detail_pegawai.npwp, detail_pegawai.no_rekening, gaji_pegawai.*');
	// 		$this->db->from('mst_pegawai');
	// 		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
	// 		$this->db->join('detail_pegawai', 'mst_pegawai.nip = detail_pegawai.nip', 'left');
	// 		$this->db->join('gaji_pegawai', 'mst_pegawai.id_pegawai = gaji_pegawai.id_pegawai');

	// 		$qry = $this->db->get();

	// 		//echo $this->db->last_query();
	// 		$row = $qry->result();
	// 		return $row;
	// }

	function countTotalPegawai()
	{
		$jns_pegawai = 'non_pns';
		$thn_anggaran = 2024;
	}


	function getListGajiPegawai()
	{

		$this->db->order_by('masa_kerja_tahun', 'DESC');
		$this->db->order_by('masa_kerja_bulan', 'DESC');
		$this->db->select('a.*,  b.nama, b.id_jabatan, b.status_kawin');
		$this->db->from('tbl_riwayat_gaji a');
		$this->db->join('mst_pegawai b', 'a.nip = b.nip', 'left');
		$qry = $this->db->get();

		$row = $qry->result();

		//print_array($row);
		return $row;
	}



	function getListPegawai($jns_pegawai, $thn_anggaran)
	{

		$this->db->order_by('nama', 'ASC');

		#$qry = $this->db->get_where('mst_pegawai', array('jns_pegawai'=> $jns_pegawai,'tahun_anggaran'=> $thn_anggaran, 'status_kerja !='=> 0));
		if ($jns_pegawai == 'non_pns') {
			$this->db->where('jns_pegawai', $jns_pegawai);
			$this->db->where('tahun_anggaran', $thn_anggaran);
			$this->db->where('status_kerja >', 0);
			$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas, gaji_pegawai.gaji_pokok');
			$this->db->from('mst_pegawai');
			$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
			$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas', 'left');
			$this->db->join('gaji_pegawai', 'mst_pegawai.id_pegawai = gaji_pegawai.id_pegawai', 'left');
		} else {
			$this->db->where('jns_pegawai', $jns_pegawai);
			$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
			$this->db->from('mst_pegawai');
			$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
			$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas', 'left');
		}

		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}

	public function getPegawaiByBulanMasuk($bulan)
	{
		return $this->db
			->where('MONTH(tgl_masuk)', $bulan)
			->where('jns_pegawai', 'non_pns')
			->where('status_kerja !=', 0)
			->get('mst_pegawai')
			->result();
	}



	function getDetailPegawai($id_pegawai)
	{


		$this->db->where('mst_pegawai.id_pegawai', $id_pegawai);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');

		$qry = $this->db->get();

		$row = $qry->row();
		return $row;
	}

	function getMasaKerja($id_pegawai)
	{

		$tanggalMasuk = $this->getTglMasuk($id_pegawai);

		$masa_kerja  = $this->hitungMasaKerja($tanggalMasuk);
		return $masa_kerja;
	}

	function getTglMasuk($id_pegawai)
	{

		$this->db->select('tgl_masuk');
		$qry = $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
		$num = $qry->num_rows();
		$row = $qry->result();

		if ($num > 0) {
			$tgl_masuk = $row[0]->tgl_masuk;
		} else {
			$tgl_masuk = 0;
		}

		return $tgl_masuk;
	}



	function hitungMasaKerja($tanggalMasuk, $tanggalSekarang = null)
	{
		// Jika tanggal sekarang tidak diberikan, gunakan tanggal hari ini
		// $tanggalSekarang = $tanggalSekarang ?? date('Y-m-d');

		// // Konversi string ke objek DateTime
		// $lahir = new DateTime($tanggalMasuk);
		// $sekarang = new DateTime($tanggalSekarang);

		// // Hitung selisih
		// $selisih = $lahir->diff($sekarang);

		// // Total bulan = tahun * 12 + bulan
		// $umurBulan = ($selisih->y * 12) + $selisih->m;

		// return $umurBulan;
	}


	function cekData($nip)
	{
		#$this->db->order_by('tahun_anggaran','2024');
		$this->db->where('tahun_anggaran', '2024');
		$qry = $this->db->get_where('mst_pegawai', array('nip' => $nip));
		$num = $qry->num_rows();
		$row = $qry->result();
		if ($num > 0) {
			$id_pegawai = $row[0]->id_pegawai;
		} else {
			$id_pegawai = 0;
		}

		return $id_pegawai;
	}

	function getRekapAbsensiPegawai($id_pegawai, $tahun)
	{

		$this->db->order_by('periode', 'ASC');
		$this->db->like('periode', $tahun, 'after');
		$this->db->where('id_pegawai', $id_pegawai);    // Produces: WHERE `title` LIKE 'match%' ESCAPE '!'

		$qry = $this->db->get('ts_rekap_absensi');
		$row = $qry->result();
		return $row;
	}


	function getAllPegawaiByValidator($id_validator, $thn_anggaran)
	{
		$this->db->order_by('nama', 'ASC');
		$this->db->where('id_validator', $id_validator);
		$this->db->where('tahun_anggaran', $thn_anggaran);
		$this->db->where('status_kerja >', 0);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');

		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}


	function getListPegawaiPustu($id_puskesmas)
	{
		$this->db->order_by('nama', 'ASC');
		$this->db->where('mst_pegawai.id_puskesmas', $id_puskesmas);
		$this->db->where('jns_pegawai', 'non_pns');
		$this->db->where('status_kerja >', 0);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');
		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}






	function getListPegawaiByValidator($id_validator, $thn_anggaran)
	{

		$this->db->order_by('nama', 'ASC');
		$this->db->where('id_validator', $id_validator);
		$this->db->where('jns_pegawai', 'non_pns');
		$this->db->where('tahun_anggaran', $thn_anggaran);
		$this->db->where('status_kerja >', 0);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');



		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}


	function getListPegawaiByValidator_page($id_validator, $thn_anggaran, $limit = 10, $offset = 0)
	{


		$this->db->limit($limit, $offset);
		$this->db->order_by('nama', 'ASC');
		$this->db->where('id_validator', $id_validator);
		$this->db->where('jns_pegawai', 'non_pns');
		$this->db->where('tahun_anggaran', $thn_anggaran);
		$this->db->where('status_kerja != ', 0);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');

		$qry = $this->db->get();

		$row = $qry->result();

		//echo $this->db->last_query();

		//	print_array($row);
		return $row;
	}


	public function get_total_pegawai($id_validator, $thn_anggaran)
	{
		$this->db->where('id_validator', $id_validator);
		$this->db->where('jns_pegawai', 'non_pns');
		$this->db->where('tahun_anggaran', $thn_anggaran);
		$this->db->where('status_kerja >', 0);
		$this->db->from('mst_pegawai');
		$qry = $this->db->get();

		return $qry->num_rows(); // Replace 'items' with your table name
	}


	function getpegawaiCuti()
	{
		$qry = $this->db->get_where('mst_pegawai_cuti', array('jns_pegawai' => 'pns'));
		$row = $qry->result();
		return $row;
	}

	function getIpAddresPegawai($id_pegawai)
	{

		$this->db->select('jns_jam_kerja, id_puskesmas');
		$this->db->where('id_pegawai', $id_pegawai);

		$qry = $this->db->get('mst_pegawai');
		$row = $qry->result();

		$id_puskesmas = $row[0]->id_puskesmas;
		$jns_jam_kerja = $row[0]->jns_jam_kerja;

		if ($id_puskesmas == 6) {
			if ($jns_jam_kerja == 'shift') {
				$id_puskesmas = 12;
			} else {
				$id_puskesmas = 6;
			}
		}


		$ip_address = $this->Master_model->getIpAddress($id_puskesmas);

		return $ip_address;
	}

	function getPinPegawai($nip)
	{
		$this->db->where('nip', $nip);
		$this->db->select('pin');
		$qry = $this->db->get('mst_pegawai');
		$row = $qry->row();

		if ($row) {
			$pin = $row->pin;
		} else {
			$pin =  0;
		}
		return $pin;
	}

	function getDataPegawai($id_pegawai, $select)
	{
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->select($select);

		$qry = $this->db->get('mst_pegawai');
		$row = $qry->row();
		return $row;
	}

	function getDataEditPegawai($id_pegawai)
	{

		$qry = $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $qry->row();
		return $row;
	}


	function getPegawaiPerbagian($bagian)
	{
		$this->db->select('nama, id_pegawai, nip, pin, jns_pegawai');
		$qry = $this->db->get_where('mst_pegawai', array('bagian_shift' => $bagian, 'tahun_anggaran' => 2024));
		$row = $qry->result();
		return $row;
	}
	function getDataGajiPegawai($nip, $tahun)
	{
		$this->db->order_by('tmt', 'DESC');
		$qry = $this->db->get_where('tbl_riwayat_gaji_baru', array('nip' => $nip), 1, 0);
		$row = $qry->result();
		return $row;
	}


	public function get_by_nip($nip)
	{
		return $this->db->get_where('mst_pegawai', ['nip' => $nip, 'tahun_anggaran' => 2024])->row();
	}

	function getGajiPokok($id_pegawai)
	{
		$this->db->select('gaji_pokok');
		$qry = $this->db->get_where('gaji_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $qry->result();
		$gaji_pokok = $row[0]->gaji_pokok;
		return $gaji_pokok;
	}


	function getTKDPokok($id_pegawai)
	{
		$this->db->select('gaji_pokok, pengkalian');
		$qry = $this->db->get_where('gaji_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $qry->result();
		$gaji_pokok = $row[0]->gaji_pokok;
		$pengkalian = $row[0]->pengkalian;

		$tkd_pokok = ceil($gaji_pokok * $pengkalian);
		return $tkd_pokok;
	}



	function checkJenisJamKerja($id_pegawai)
	{
		$this->db->select('jns_jam_kerja');
		$qry = $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $qry->result();
		$jns_jam_kerja = $row[0]->jns_jam_kerja;
		return $jns_jam_kerja;
	}



	function getNamaPegawaiByID($id_pegawai)
	{
		$this->db->select('nama');
		$this->db->where('id_pegawai', $id_pegawai);
		$qry =  $this->db->get('mst_pegawai');
		$row = $qry->row();

		$nama = $row->nama;
		return $nama;
	}

	function getPegawaiByNama($nama)
	{
		//$tahun = date('Y');
		$tahun =  2024;
		$this->db->where('nama', $nama);
		$this->db->where('tahun_anggaran', $tahun);
		$qry =  $this->db->get('mst_pegawai');
		$row = $qry->result();


		return $row;
	}

	function cekUsergroupName($id_pegawai)
	{
		$this->db->select('nama, id_pegawai, userlevel_name');
		$this->db->where('id_pegawai', $id_pegawai);
		$qry =  $this->db->get('mst_pegawai');
		$row = $qry->result();


		//print_array($row);
		$userlevel_name = $row[0]->userlevel_name;
		return $userlevel_name;
	}
	function getKapuskec()
	{
		$this->db->select('nama, nip');
		$this->db->where('usergroup', 1);
		$qry =  $this->db->get('mst_pegawai');
		$row = $qry->result();


		return $row;
	}

	public function cari_pegawai_pengganti_cuti($id_jabatan, $id_pegawai)
	{
		$this->db->order_by('nama', 'ASC');
		$this->db->where('status_kerja', 1);
		$this->db->where('tahun_anggaran', '2024');
		$this->db->where('id_jabatan', $id_jabatan);
		$this->db->where('id_pegawai !=', $id_pegawai);
		$this->db->select('id_pegawai, nama');
		$query = $this->db->get('mst_pegawai');
		return $query->result(); // bentuk: [ ['id'=>1, 'nama'=>'Joko'], ... ]
	}

	function numSearchPegawai($keyword)
	{

		$sql = "SELECT id_pegawai FROM mst_pegawai WHERE nama like '%$keyword%' AND tahun_anggaran = '2024' AND status_kerja > 0 AND jns_pegawai = 'non_pns'";
		$qry = $this->db->query($sql);
		$row = $qry->num_rows();

		return $row;
	}
	public function search_pegawai($keyword, $limit = 300, $offset = 0)
	{

		$sql = "SELECT *  FROM mst_pegawai WHERE nama like '%$keyword%' AND tahun_anggaran = '2024'  AND status_kerja > 0 AND jns_pegawai = 'non_pns' LIMIT $limit OFFSET $offset ";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		return $row;
	}


	function getJabatanByNIP($nip)
	{
		$this->db->select('mst_jabatan.nama AS jabatan');
		$this->db->where('nip', $nip);
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id', 'left');
		$qry = $this->db->get();
		$row = $qry->result();
		$jabatan = $row[0]->jabatan;
		return $jabatan;
	}

	function getDataDetailPegawai($nip)
	{
		$qry = $this->db->get_where('detail_pegawai', array('nip' => $nip));
		$row = $qry->row();

		if (!empty($row)) {
			return $row;
		} else {
			$this->insertDataDetail($nip, 0, 0, 0, '', '01-01-2000', '', '', '0', '', '');
			$this->getDataDetailPegawai($nip);
		}
	}



	function getHakCutiPegawai($id_pegawai, $jns_hak_cuti, $order = 'ASC')
	{

		#hak cuti 1 = sisa cuti tahun lalu, 2 = hak cuti tahun ini, 3 = hak cuti bersama
		$this->db->order_by('id', $order);
		$qry = $this->db->get_where('log_cuti', array('id_pegawai' => $id_pegawai, 'jns_hak_cuti' => $jns_hak_cuti));
		$row = $qry->result();

		if (count($row) == 0) {
			return 0;
		} else {
			$sisa_akhir = $row[0]->sisa_akhir;
			return $sisa_akhir;
		}
	}


	/*

	function getHakCutiPegawai($id_pegawai, $jns_hak_cuti, $order='ASC'){

		#hak cuti 1 = sisa cuti tahun lalu, 2 = hak cuti tahun ini, 3 = hak cuti bersama
		$this->db->order_by('id', $order);
		$qry = $this->db->get_where('cuti_pegawai', array('id_pegawai'=> $id_pegawai, 'jns_hak_cuti'=> $jns_hak_cuti));
		$row = $qry->result();
		return $row;
	}
*/

	function getDetValidator($nip)
	{

		$this->db->where('a.nip', $nip);
		$this->db->select('a.*, b.id_jabatan');
		$this->db->from('mst_validator a');
		$this->db->join('mst_pegawai b', 'ON a.nip = b. nip', 'left');
		$qry = $this->db->get();
		$row = $qry->row();

		return $row;
	}



	function getValidator()
	{
		$this->db->order_by('a.id_puskesmas', 'ASC');
		$this->db->select('a.*, b.nama as puskesmas, c.nama as jabatan');
		$this->db->from('mst_pegawai a');

		// PJ = usergroup 2,3,4
		$this->db->where_in('a.usergroup', [2, 3, 4]);

		$this->db->join('mst_puskesmas b', 'a.id_puskesmas = b.id_puskesmas', 'left');
		$this->db->join('mst_jabatan c', 'a.id_jabatan = c.id', 'left');

		$qry = $this->db->get();
		return $qry->result();
	}

	// function getValidator(){
	// 	$this->db->order_by('usergroup', 'ASC');
	// 	$this->db->where('usergroup < ', 6);
	// 	$this->db->where('usergroup > ', 1);
	// 	$qry = $this->db->get('mst_pegawai');
	// 	$row = $qry->result();
	// 	return $row;
	// }


	// function getPJ_PJLP(){
	// 	$this->db->order_by('usergroup', 'ASC');
	// 	$this->db->where('usergroup', 8);
	// 	$qry = $this->db->get('mst_pegawai');
	// 	$row = $qry->result();
	// 	return $row;
	// }

	function getPegawaiPJLP()
	{
		$this->db->order_by('nama', 'ASC');
		$qry = $this->db->get('tbl_pegawai_pjlp');
		$row = $qry->result();
		return $row;
	}

	function getDataEditPegawaiPJLP($id_pjlp)
	{


		$qry = $this->db->get_where('tbl_pegawai_pjlp', array('id_pjlp' => $id_pjlp));
		$row = $qry->result();
		return $row;
	}

	function insertNewPegawaiPJLP()
	{

		$data = array(
			'nama' => $this->input->post('nama'),
			'id_pjlp' => $this->input->post('id_pjlp'),
			'id_mesin' =>  $this->input->post('id_mesin'),
			'jabatan' =>  $this->input->post('jabatan'),
			'lokasi_kerja' => $this->input->post('lokasi_kerja'),
		);

		$this->db->insert('tbl_pegawai_pjlp', $data);

		return true;
	}

	function getNoKTP($nip)
	{
		$this->db->select('no_ktp');
		$qry = $this->db->get_where('detail_pegawai', array('nip' => $nip));
		$row = $qry->result();
		$no_ktp = $row[0]->no_ktp;
		return $no_ktp;
	}


	public function getPhotoPegawai($nip)
	{
		$this->db->select('photo');
		$qry = $this->db->get_where('detail_pegawai', array('nip' => $nip));
		$row = $qry->result();

		if (!empty($row)) {

			$photo = $row[0]->photo;

			if ($photo == '') {
				$photo = 'avatar.png';
			}
		} else {
			$photo = 'avatar.png';
		}

		return $photo;
	}


	function getIDpegawaiByName($nama)
	{
		$this->db->select('id_pegawai');
		$qry = $this->db->get_where('mst_pegawai', array('nama' => $nama, 'tahun_anggaran' => '2024'));
		$row = $qry->result();

		if (!empty($row)) {
			$id_pegawai = $row[0]->id_pegawai;
		} else {
			$id_pegawai = 0;
		}
		return $id_pegawai;
	}

	function getNIPByName($nama)
	{
		$this->db->select('nip');
		$qry = $this->db->get_where('mst_pegawai', array('nama' => $nama, 'tahun_anggaran' => '2024'));
		$row = $qry->result();

		if (!empty($row)) {
			$nip = $row[0]->nip;
		} else {
			$nip = 0;
		}
		return $nip;
	}

	function getNIKbyNIP($nip)
	{
		$this->db->select('no_ktp');
		$qry = $this->db->get_where('detail_pegawai', array('nip' => $nip));
		$row = $qry->result();

		if (!empty($row)) {
			$nik = $row[0]->no_ktp;
		} else {
			$nik = 0;
		}
		return $nik;
	}

	function cekNoKTP($nik)
	{
		$this->db->select('nip');
		$qry = $this->db->get_where('detail_pegawai', array('no_ktp' => $nik));
		$row = $qry->result();

		if (!empty($row)) {
			$nip = $row[0]->nip;
		} else {
			$nip = 0;
		}
		return $nip;
	}

	function getNamaPuskesmasByNama($nama)
	{
		$sql = "SELECT b.nama FROM `mst_pegawai` a LEFT JOIN mst_puskesmas b ON a.id_puskesmas = b.id_puskesmas WHERE a.nama = '$nama' AND tahun_anggaran = '2024' ";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		if (!empty($row)) {
			$id_puskesmas = $row[0]->nama;
		} else {
			$id_puskesmas = '-';
		}
		return $id_puskesmas;
	}

	function getHubdisPegawai($nip)
	{
		$qry = $this->db->get_where('tbl_hubdis', array('nip' => $nip));
		$row = $qry->result();
		return $row;
	}

	function getDetailHubdis($id)
	{
		$qry = $this->db->get_where('tbl_hubdis', array('id' => $id));
		$row = $qry->result();
		return $row;
	}




	function getNamaPuskesmasByNip($nip)
	{
		$sql = "SELECT b.nama FROM `mst_pegawai` a LEFT JOIN mst_puskesmas b ON a.id_puskesmas = b.id_puskesmas WHERE nip = '$nip' AND tahun_anggaran = '2024' ";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		if (!empty($row)) {
			$id_puskesmas = $row[0]->nama;
		} else {
			$id_puskesmas = '-';
		}
		return $id_puskesmas;
	}

	function getNipPegawaiByID($id_pegawai)
	{
		$this->db->select('nip');
		$qry = $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $qry->result();

		if (!empty($row)) {
			$nip = $row[0]->nip;
		} else {
			$nip = 0;
		}
		return $nip;
	}



	function insertDataGaji($id_pegawai, $gaji_pokok, $pengkalian, $pph21, $bpjs_kes, $bpjs_tk, $ptkp)
	{
		$data = array(
			'id_pegawai' => $id_pegawai,
			'gaji_pokok' => $gaji_pokok,
			'pengkalian' => $pengkalian,
			'pph21' =>  $pph21,
			'bpjs_kes' => $bpjs_kes,
			'bpjs_tk' => $bpjs_tk,
			'ptkp' => $ptkp
		);

		#print_array($data);
		$this->db->insert('gaji_pegawai', $data);
		return true;
	}


	function cekDataDetailPegawai($nip)
	{
		$this->db->select('nip');
		$qry = $this->db->get_where('mst_pegawai', array('nip' => $nip));
		$row = $qry->row();
		if (!$row) {
			$this->insertDataDetail($nip, 0, 0, 0, '', '1970-01-01', '', '', '', '', '');
		}
	}

	function insertDataDetail($nip, $npwp, $no_rekening, $no_ktp, $tempat_lahir, $tgl_lahir, $alamat_ktp, $alamat_domisili, $no_tlp, $email, $photo)
	{
		$data = array(
			'nip' => $nip,
			'npwp' => $npwp,
			'no_rekening' => $no_rekening,
			'no_ktp' =>  $no_ktp,
			'tempat_lahir' => $tempat_lahir,
			'tgl_lahir' => $tgl_lahir,
			'alamat_ktp' => $alamat_ktp,
			'alamat_domisili' => $alamat_domisili,
			'no_tlp' => $no_tlp,
			'email ' => $email,
			'photo' => $photo,
		);

		#print_array($data);
		$this->db->insert('detail_pegawai', $data);
		return true;
	}


	function insertNewPegawai()
	{
		$tgl_masuk = $this->input->post('tmt');
		$nip = $this->input->post('nip');

		$password = 'Cilincing#779988';
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$data = array(
			'nama' => $this->input->post('nama'),
			'nip' => $nip,
			'nrk' =>  $this->input->post('nrk'),
			'golongan' =>  $this->input->post('golongan'),
			'id_puskesmas' => $this->input->post('id_puskesmas'),
			'rumpun_kerja' => $this->input->post('rumpun_kerja'),
			'id_jabatan' => $this->input->post('id_jabatan'),
			'id_poli' => $this->input->post('id_poli'),
			'tgl_masuk' => format_db($tgl_masuk),
			'tmt' => format_db($tgl_masuk),
			'jns_pegawai' => $this->input->post('jns_pegawai'),
			'jns_jam_kerja' => $this->input->post('jam_kerja'),
			'status_kawin' => 1,
			'status_pajak' => 'TK',
			'id_pendidikan' =>  $this->input->post('id_pendidikan'),
			'id_validator' => 1058,
			'usergroup' => $this->input->post('usergroup'),
			'password' => $hashed_password,
			'kategori_masa_kerja' => 1,
			'masa_kerja' => '0-0-1',
			'tahun_anggaran' => '2024'

		);


		$this->db->insert('mst_pegawai', $data);

		$this->insertDataDetail($nip, 0, 0, 0, '', '0000-00-00', '', '', '08', '@gmail.com', 'dummy.png');

		$id_pegawai = $this->getlastIDPegawai();

		$this->insertDataGaji($id_pegawai, 0, 0, 0, 0, 0, 0);

		return true;
	}


	function getPegawaiNaikGaji($bulan, $tahun)
	{
		//tahun = ganjil genap

		$sql = "SELECT * FROM mst_pegawai WHERE MONTH(tgl_masuk) = $bulan AND tahun_anggaran = '2024' AND status_kerja != 0 ORDER BY tgl_masuk ASC";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;
	}
	function getlastIDPegawai()
	{
		$this->db->select('id_pegawai');
		$this->db->order_by('id_pegawai', 'DESC');
		$qry = $this->db->get('mst_pegawai', 1, 0);
		$row = $qry->result();
		$id_pegawai = $row[0]->id_pegawai;
		return $id_pegawai;
	}



	function getRiwayatPendidikanByJenjang($nip, $jenjang)
	{
		$this->db->where('nip', $nip);
		$this->db->where('jenjang', $jenjang);
		$qry = $this->db->get('tbl_riwayat_pendidikan');
		$row = $qry->result();

		return $row;
	}

	function getDataPelatihan($nip)
	{
		$this->db->order_by('id', 'DESC');
		$this->db->where('nip', $nip);
		$qry = $this->db->get('tbl_diklat');
		$row = $qry->result();

		return $row;
	}

	function getDokumenPegawai($nip, $jns_dokumen)
	{
		$this->db->order_by('id', 'DESC');
		$this->db->where('nip', $nip);
		$this->db->where('jenis_dokumen', $jns_dokumen);
		$qry = $this->db->get('dokumen_pegawai');
		$row = $qry->result();

		return $row;
	}


	function getPendidikanTerakhir($nip)
	{
		$this->db->order_by('id', 'DESC');
		$this->db->where('nip', $nip);
		$qry = $this->db->get('tbl_riwayat_pendidikan');
		$row = $qry->result();

		return $row;
	}

	function getRiawayatPendidikan($nip)
	{

		$this->db->where('nip', $nip);
		$qry = $this->db->get('tbl_riwayat_pendidikan');
		$row = $qry->result();

		return $row;
	}

	function getUraianTugas($nip)
	{

		$this->db->where('nip', $nip);
		$qry = $this->db->get('uraian_tugas');
		$row = $qry->result();

		return $row;
	}

	function updateTKDPokokPegawai($periode)
	{

		$this->db->select('a.id_pegawai, a.nip, a.nama, a.pengali, b.gaji_pokok');
		$this->db->from('mst_pegawai a');
		$this->db->join('ts_gaji_pegawai b', 'a.nip = b.nip');

		$qry = $this->db->get();
		$row = $qry->result();


		foreach ($row as $pegawai) {
			$nip  = $pegawai->nip;
			$pengali  = $pegawai->pengali;
			$gaji_pokok  = $pegawai->gaji_pokok;

			$tkd_pokok = round($gaji_pokok * $pengali);

			$this->db->where('nip', $nip);
			$this->db->where('periode', $periode);
			$this->db->set('tkd_pokok', $tkd_pokok);
			$this->db->update('ts_rekap_tkd');
		}

		return true;
	}

	function updateDataPegawai($id_pegawai)
	{
		$tgl_masuk = $this->input->post('tmt');



		$tgl_masuk = date('Y-m-d', strtotime($tgl_masuk));

		$klaster = $this->input->post('klaster');
		$id_puskesmas = $this->input->post('id_puskesmas');

		if ($id_puskesmas > 1) {
			$validator = $this->db->where('id_puskesmas', $id_puskesmas)->get('mst_validator')->row();
			$id_validator = $validator->id_pegawai;
		} else {

			$qry = $this->db->get_where('mst_validator', ['id_puskesmas' => $id_puskesmas, 'klaster' => $klaster]);
			$row = $qry->row();
			$id_validator = $row->id_pegawai;
		}

		$jns_pegawai = $this->input->post('jns_pegawai');

		$data = array(
			'nama' => $this->input->post('nama'),
			'nip' => $this->input->post('nip'),
			'nrk' =>  $this->input->post('nrk'),
			'golongan' =>  $this->input->post('golongan'),
			'id_puskesmas' => $this->input->post('id_puskesmas'),
			'id_pendidikan' => $this->input->post('id_pendidikan'),
			'id_jabatan' => $this->input->post('id_jabatan'),
			'keterangan_jabatan' => $this->input->post('ket_jab'),
			'id_poli' => $this->input->post('id_poli'),
			'klaster' => $this->input->post('klaster'),
			'pin' => $this->input->post('pin'),
			'tgl_masuk' => format_db($tgl_masuk),
			'tmt' => format_db($tgl_masuk),
			'jns_pegawai' => $jns_pegawai,
			'jns_jam_kerja' => $this->input->post('jam_kerja'),
			'id_validator' => $id_validator,
			'usergroup' => $this->input->post('usergroup'),

		);



		// $data = array(
		// 	'nama' => $this->input->post('nama'),
		// 	'nip' => $this->input->post('nip'),
		// 	'nrk' =>  $this->input->post('nrk'),
		// 	'golongan' =>  $this->input->post('golongan'),
		// 	'id_puskesmas' => $this->input->post('id_puskesmas'),
		// 	'rumpun_kerja' => $rumpun_kerja,
		// 	'id_jabatan' => $this->input->post('id_jabatan'),
		// 	'keterangan_jabatan' => $this->input->post('ket_jab'),
		// 	'id_poli' => $this->input->post('id_poli'),
		// 	'tgl_masuk' => format_db($tgl_masuk),
		// 	'tmt' => format_db($tgl_masuk),
		// 	'jns_pegawai' => $jns_pegawai,
		// 	'jns_jam_kerja' => $this->input->post('jam_kerja'),
		// 	'status_kawin' => $this->input->post('status_kawin'),
		// 	'status_pajak' => $this->input->post('status_pajak'),
		// 	'id_pendidikan' =>  $this->input->post('id_pendidikan'),
		// 	'id_validator' => $id_validator,
		// 	'usergroup' => $this->input->post('usergroup'),

		// );


		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->update('mst_pegawai', $data);
		return true;
	}


	function updateDataDetailPegawai($nip)
	{
		$tgl_lahir = $this->input->post('tgl_lahir');
		$nik = $this->input->post('nik');
		$strlen = strlen($nik);

		$data = array(
			'npwp' => $this->input->post('npwp'),
			'no_rekening' => $this->input->post('no_rekening'),
			'no_ktp' =>  $this->input->post('no_ktp'),
			'tempat_lahir' => $this->input->post('tempat_lahir'),
			'tgl_lahir' => format_db($tgl_lahir),
			'no_tlp' => $this->input->post('no_tlp'),
			'email' => $this->input->post('email'),
			'alamat_ktp' => $this->input->post('alamat_ktp'),
			'alamat_domisili' => $this->input->post('alamat_domisili'),

		);

		#print_array($data);
		$this->db->where('nip', $nip);
		$this->db->update('detail_pegawai', $data);
		return true;
	}

	function updateDataGaji($id_pegawai)
	{

		$data = array(
			'gaji_pokok' => $this->input->post('gaji_pokok'),
			'pengkalian' => $this->input->post('pengkalian'),
			'pph21' =>  $this->input->post('pph21'),
			'bpjs_kes' =>  $this->input->post('bpjs_kes'),
			'bpjs_tk' => $this->input->post('bpjs_tk'),
			'ptkp' => $this->input->post('ptkp'),


		);

		// print_array($data);
		// exit;
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->update('gaji_pegawai', $data);
		return true;
	}


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

		return true;
		//redirect('admin/gaji/index');

	}



	public function getPegawaiForRiwayatGaji($tahun_anggaran)
	{
		return $this->db
			->select('
					id_pegawai,
					nip,
					nama,
					tgl_masuk,
					id_pendidikan,
					pengali,
					jns_pegawai,
					status_kerja,
					tahun_anggaran
				')
			->from($this->table)
			->where('tahun_anggaran', $tahun_anggaran)
			->where('jns_pegawai', 'non_pns')
			->where('status_kerja !=', 0)
			->order_by('tgl_masuk', 'ASC')
			->get()
			->result();
	}
}
