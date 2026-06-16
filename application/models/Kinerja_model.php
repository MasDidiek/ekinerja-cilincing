<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');
class Kinerja_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		//$this->db2 = $this->load->database('ekin1', true);

	}




	function getIDPegawaiekin($nip)
	{

		$this->db2->where('nip', $nip);

		$qry = $this->db2->get('mst_pegawai');
		$row = $qry->result();
		return $row;
	}

	function getInputan($id_pegawai)
	{
		$tgl = '2024-01-01';
		$this->db2->where('id_pegawai', $id_pegawai);
		$this->db2->where('tgl >=', $tgl);

		$this->db2->select('ts_kinerja.*, mst_kegiatan.nama_kegiatan, mst_kegiatan.waktu');
		$this->db2->from('ts_kinerja');
		$this->db2->join('mst_kegiatan', 'ts_kinerja.id_kegiatan = mst_kegiatan.id');

		$qry = $this->db2->get();
		$row = $qry->result();

		return $row;
	}


	function getDataPerilkuEkin1($id_pegawai)
	{
		$query = $this->db2->get_where('penilaian_perilaku', array('id_pegawai' => $id_pegawai, 'periode_bulan' => 1, 'periode_tahun' => 2024));
		$row = $query->result();
		return $row;
	}
	//mengecek data aktifitas 
	function cekDataAktifitasPending($id_pegawai, $tanggal)
	{
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('tgl', $tanggal);
		$this->db->where('status', 0);
		$this->db->select('status');
		$qry = $this->db->get('ts_kinerja');
		$row = $qry->result();
		return $row;
	}


	function getJumlahInputanAktifitas($id_pegawai, $periode)
	{
		$sql = "SELECT sum(total) as total_aktifitas FROM ts_kinerja WHERE id_pegawai = $id_pegawai AND tgl like '$periode%' ";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		$aktiftias = $row[0]->total_aktifitas;



		return $aktiftias;
	}


	function getAktifitasApprove($id_pegawai, $periode)
	{
		$sql = "SELECT sum(total) as total_aktifitas FROM ts_kinerja WHERE id_pegawai = $id_pegawai AND tgl like '$periode%' AND status = 1 ";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		$aktiftias = $row[0]->total_aktifitas;



		return $aktiftias;
	}


	function delete_inputan($id_pegawai)
	{

		$sql = "DELETE FROM ts_kinerja WHERE id_pegawai = $id_pegawai AND tgl < '2024-02-01' ";
		$this->db->query($sql);
		return true;
	}

	function getDataInputAktifitas($id_pegawai, $tanggal)
	{
		$this->db->order_by('jam_mulai', 'ASC');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('tgl', $tanggal);
		$this->db->select('ts_kinerja.*, mst_indikator_kegiatan.nama_kegiatan AS indikator, mst_indikator_kegiatan.indikator_kegiatan');
		$this->db->from('ts_kinerja');
		$this->db->join('mst_indikator_kegiatan', 'ts_kinerja.id_indikator = mst_indikator_kegiatan.id', 'LEFT');

		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}



	function getAktifitasPegawaiPerBulan($id_pegawai, $periode)
	{
		
		$start_date  = $periode . '-01';
		$end_date  = $periode . '-31';
		$date_in  = '2026-04-05';
		

		$this->db->order_by('tgl', 'ASC');


		
		// $start_date  = $periode . '-01';
		// $end_date  = $periode . '-31';
		// $qry = $this->db->get_where('ts_kinerja', array('id_pegawai' => $id_pegawai, 'date >=' => $start_date, 'tgl <=' => $end_date));
		// return $qry->result();

		$qry = $this->db->get_where('ts_kinerja', array('id_pegawai' => $id_pegawai, 'date_in <=' => $date_in, 'tgl >=' => $start_date, 'tgl <=' => $end_date));
		return $qry->result();

		
	}


	function getAktifitasPegawai($id_pegawai)
	{
		$this->db->order_by('tgl', 'ASC');
		$this->db->select('*');
		$qry = $this->db->get_where('ts_kinerja', array('id_pegawai' => $id_pegawai));
		return $qry->result();
	}

	function getJumlahInputPerBulan($id_pegawai, $periode)
	{

		$sql = "SELECT SUM(total) as jmlh_input FROM ts_kinerja WHERE id_pegawai = $id_pegawai AND tgl like '$periode%'";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		$total = $row[0]->jmlh_input;
		return $total;
	}

	function generateNilaiPerilaku($id_pegawai, $bulan, $tahun) 
	{
		
		$perilakuTerakhir = $this->copyPenilaianTerakhir($id_pegawai, $bulan, $tahun);
		if($perilakuTerakhir){
			$this->session->set_flashdata('message', 'Nilai perilaku berhasil digenerate ulang');
			$this->session->set_flashdata('type', 'success');	
		}else{
			$this->session->set_flashdata('message', 'Gagal generate nilai perilaku');
			$this->session->set_flashdata('type', 'error');	
		}

		return true;
		
	}

	function getInputanPerhari($id_pegawai, $tanggal)
	{

		$this->db->select('id, status');
		$qry = $this->db->get_where('ts_kinerja', array('id_pegawai' => $id_pegawai, 'tgl' => $tanggal));
		return $qry->result();
	}

	function getJumlahInputAktifitas($id_pegawai, $periode)
	{

		$sql = "SELECT id FROM ts_kinerja WHERE id_pegawai = $id_pegawai AND tgl like '$periode%'";
		$qry = $this->db->query($sql);
		$row = $qry->num_rows();


		return $row;
	}




	function getAktifitasByStatus($id_pegawai, $periode, $status = 0)
	{
		$sql = "SELECT SUM(total) as jmlh_input FROM ts_kinerja WHERE id_pegawai = $id_pegawai AND tgl like '$periode%' AND status = $status";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		$total = $row[0]->jmlh_input;
		return $total;
	}



	function getDataEditAktifitas($id)
	{
		$this->db->where('ts_kinerja.id', $id);
		$this->db->select('ts_kinerja.*, mst_indikator_kegiatan.nama_kegiatan AS indikator, mst_indikator_kegiatan.indikator_kegiatan');
		$this->db->from('ts_kinerja');
		$this->db->join('mst_indikator_kegiatan', 'ts_kinerja.id_indikator = mst_indikator_kegiatan.id', 'LEFT');

		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}


	function GetFrequentAktifitas($id_pegawai)
	{

		$sql = "Select nama_kegiatan, waktu_efektif FROM ts_kinerja WHERE id_pegawai = '$id_pegawai' LIMIT 10";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;
	}

	function insertAktifitas()
	{
		$tgl           =  $this->input->post('tanggal');
		$indikator     =  $this->input->post('indikator');
		$vol           =  $this->input->post('vol');
		$waktu_efektif =  $this->input->post('waktu_efektif');

		$total = $waktu_efektif * $vol;
		// 'id_indikator' => $this->getKodeIndikator($indikator),

		$new_data = array(
			'id_pegawai' => $this->session->userdata('id_pegawai'),
			'tgl' => format_db($tgl),
			'jns_kegiatan' => $this->input->post('jns_kegiatan'),
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

		return true;
	}


	function updateAktifitas($id)
	{
		$indikator     =  $this->input->post('indikator');
		$vol           =  $this->input->post('vol');
		$waktu_efektif =  $this->input->post('waktu_efektif');

		$total = $waktu_efektif * $vol;

		//'id_indikator' => $this->getKodeIndikator($indikator),
		//	$this->input->post('jns_kegiatan')
		$new_data = array(
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

		$this->db->where('id', $id);
		$this->db->update('ts_kinerja', $new_data);

		return true;
	}


	function getKodeIndikator($indikator)
	{
		$this->db->where('nama_kegiatan', $indikator);
		$qry = $this->db->get('mst_indikator_kegiatan');
		$row = $qry->result();

		return $row[0]->id;
	}


	function getPoinPerilaku($id_pegawai, $bulan, $tahun)
	{
		$this->db->select('SUM(poin) as total');
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('periode_bulan', $bulan);
		$this->db->where('periode_tahun', $tahun);

		$row = $this->db->get('tbl_penilaian_perilaku')->row();

		$total = ($row->total / 250) * 10;

		return round($total, 2);
	}



	public function copyPenilaianTerakhir($id_pegawai, $bulanBaru, $tahunBaru)
	{

		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('periode_tahun', 2026);
		$this->db->where('periode_bulan', 1);

		$data = $this->db->get('tbl_penilaian_perilaku')->result_array();
		if (!$data) {
			return;
		}

		foreach ($data as $row) {

			$insert = [
				'id_pegawai' => $row['id_pegawai'],
				'periode_bulan' => $bulanBaru,
				'periode_tahun' => $tahunBaru,
				'tgl_input' => date('Y-m-d'),
				'id_pertanyaan' => $row['id_pertanyaan'],
				'jns_item' => $row['jns_item'],
				'jawaban' => $row['jawaban'],
				'poin' => $row['poin']
			];

	

			$this->db->insert('tbl_penilaian_perilaku', $insert);
		}

		return true;
	
	}


	function searchAktifitasUtamaPegawai($nip, $keyword)
	{
		$this->db->order_by('b.nama_kegiatan', 'ASC');
		$this->db->select('a.*, b.nama_kegiatan, b.waktu, b.satuan');
		$this->db->where('nip', $nip);
		$this->db->like('b.nama_kegiatan', $keyword);
		$this->db->from('aktifitas_pegawai a');
		$this->db->join('mst_kegiatan b', 'a.id_aktifitas = b.id');
		$qry = $this->db->get();
		$row = $qry->result();
		return $row;
	}


	function getAktifitasUtamaPegawai($nip)
	{

		$this->db->order_by('b.nama_kegiatan', 'ASC');
		$this->db->select('a.*, b.nama_kegiatan, b.waktu, b.satuan');
		$this->db->where('nip', $nip);
		$this->db->from('aktifitas_pegawai a');
		$this->db->join('mst_kegiatan b', 'a.id_aktifitas = b.id');
		$qry = $this->db->get();
		$row = $qry->result();
		return $row;
	}


	function cekDataCapaian($nip, $periode)
	{
		$this->db->select('id');
		$qry = $this->db->get_where('tbl_capaian', array('nip' => $nip, 'periode' => $periode));
		$row = $qry->result();

		if (count($row) == 0) {
			return 0;
		} else {
			return $row[0]->id;
		}
	}

	function getMenitPenambahPengurang($id_pegawai, $periode)
	{
		$qry = $this->db->get_where("ts_rekap_absensi", [
			"id_pegawai" => $id_pegawai,
			"periode" => $periode,
		]);
		$row = $qry->row();

		//print_array($row);
		if ($row) {

			$telat = $row->telat;
			$pulang_awal = $row->pulang_awal;
			$izin = $row->izin * 300;
			$izin_half = $row->izin_half * 150;
			$sakit = $row->sakit * 300;
			$sakit_dgn_sk = $row->sakit_dgn_sk * 240;
			$alpha = $row->alpha * 450;
			$totalPengurang = $telat + $pulang_awal + $izin + $izin_half + $sakit + $sakit_dgn_sk + $alpha;

			$cuti = $row->cuti + 300;
			$totalPenambah = $cuti;
		} else {
			$totalPengurang = 0;
			$totalPenambah  = 0;
		}



		return array($totalPenambah, $totalPengurang);
	}



	public function get_capaian_by_puskesmas($id_puskesmas, $periode, $klaster = 5)
	{
		$this->db->select('
			p.id_pegawai,
			p.nama,
			p.pin,
			p.nip,
			j.nama as jabatan,

			c.periode,
			c.bobot_aktifitas,
			c.perilaku,
			c.serapan,
			c.total_capaian,

			a.telat,
			a.pulang_awal,
			a.izin,
			a.sakit,
			a.sakit_dgn_sk,
			a.alpha,
			a.isoman,
			a.dl_penuh,
			a.dl_awal,
			a.dl_akhir,
			a.cuti
		');

		$this->db->from('mst_pegawai p');

		// Join capaian
		$this->db->join(
			'tbl_capaian c',
			'p.nip = c.nip AND c.periode = ' . $this->db->escape($periode),
			'left'
		);

		// Join jabatan
		$this->db->join(
			'mst_jabatan j',
			'p.id_jabatan = j.id',
			'left'
		);

		// Join rekap absensi
		$this->db->join(
			'ts_rekap_absensi a',
			'p.id_pegawai = a.id_pegawai AND a.periode = ' . $this->db->escape($periode),
			'left'
		);

		$this->db->where('p.jns_pegawai', 'non_pns');
		$this->db->where('p.id_puskesmas', $id_puskesmas);
		if ($id_puskesmas == 1) {
			$this->db->where('p.klaster', $klaster);
		}
		$this->db->where('p.status_kerja !=', 0); // optional kalau hanya pegawai aktif

		$this->db->order_by('p.nama', 'ASC');

		return $this->db->get()->result();
	}

	public function get_capaian_by_validator($id_validator, $periode)
	{
		$this->db->select('
			p.id_pegawai,
			p.nama,
			p.nip,
			j.nama as jabatan,

			c.periode,
			c.bobot_aktifitas,
			c.perilaku,
			c.serapan,
			c.total_capaian,

			a.telat,
			a.pulang_awal,
			a.izin,
			a.sakit,
			a.sakit_dgn_sk,
			a.alpha,
			a.isoman,
			a.dl_penuh,
			a.dl_awal,
			a.dl_akhir,
			a.cuti
		');

		$this->db->from('mst_pegawai p');

		// Join capaian
		$this->db->join(
			'tbl_capaian c',
			'p.nip = c.nip AND c.periode = ' . $this->db->escape($periode),
			'left'
		);

		// Join jabatan
		$this->db->join(
			'mst_jabatan j',
			'p.id_jabatan = j.id',
			'left'
		);

		// Join rekap absensi
		$this->db->join(
			'ts_rekap_absensi a',
			'p.id_pegawai = a.id_pegawai AND a.periode = ' . $this->db->escape($periode),
			'left'
		);

		$this->db->where('p.jns_pegawai', 'non_pns');
		$this->db->where('p.id_validator', $id_validator);
		$this->db->where('p.status_kerja !=', 0); // optional kalau hanya pegawai aktif

		$this->db->order_by('p.nama', 'ASC');

		return $this->db->get()->result();
	}



	function getDataCapaianPegawai($nip)
	{
		$this->db->order_by('periode', 'DESC');
		$qry = $this->db->get_where('tbl_capaian', array('nip' => $nip));
		$row = $qry->result();
		return $row;
	}

	function getDataCapaian($nip, $periode)
	{
		$this->db->order_by('total_capaian', 'ASC');
		$qry = $this->db->get_where('tbl_capaian', array('nip' => $nip, 'periode' => $periode));
		$row = $qry->result();
		return $row;
	}

	function getListCapaian($nip)
	{
		$this->db->order_by('periode', 'ASC');
		$this->db->where('nip', $nip);
		$qry = $this->db->get('ts_rekap_tkd');
		$row = $qry->result();
		return $row;
	}

	function getMasterKegiatan()
	{
		$this->db->order_by('id', 'DESC');
		$qry = $this->db->get('mst_kegiatan');
		$row = $qry->result();
		return $row;
	}


	function getListKategoriPenilaianPerilaku()
	{
		$qry = $this->db->get('mst_kategori_penilaian');
		$row = $qry->result();

		return $row;
	}

	function getDaftarPertanyaan($id_kat)
	{
		$this->db->where('id_kategori', $id_kat);
		$qry = $this->db->get('daftar_pertanyaan');
		$row = $qry->result();
		return $row;
	}




	function getJawaban($id_pegawai, $id, $bulan, $tahun)
	{
		$query = $this->db->get_where('tbl_penilaian_perilaku', array('id_pegawai' => $id_pegawai, 'id_pertanyaan' => $id, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun), 1, 0);
		$row = $query->result();
		return $row;
	}


	function cekPenilaianPegawai($id_pegawai, $bulan, $tahun)
	{
		$query = $this->db->get_where('tbl_penilaian_perilaku', array('id_pegawai' => $id_pegawai, 'periode_bulan' => $bulan, 'periode_tahun' => $tahun), 1, 0);
		$row = $query->result();
		if (!empty($row)) {
			$jawaban = $row[0]->jawaban;
			return true;
		} else {
			return false;
		}
	}


	function insertInitialPerilaku($id_pegawai, $bulan, $tahun)
	{
		$datalist = $this->getListKategoriPenilaianPerilaku();
		$tgl = date('Y-m-d');

		for ($a = 0; $a < count($datalist); $a++) {
			$id_kat = $datalist[$a]->id;
			$daftar = $this->getDaftarPertanyaan($id_kat);

			for ($i = 0; $i < count($daftar); $i++) {
				$id         = $daftar[$i]->id;
				$jns_item   = $daftar[$i]->jns_item;

				$this->insertPenilaianPerilaku($id_pegawai, $bulan, $tahun, $tgl, $id, $jns_item, 0, 0);
			}
		}

		return true;
	}

	function insertPenilaianPerilaku($id_pegawai, $bulan, $tahun, $tgl, $id_item, $jns_item, $jawaban, $poin)
	{
		$data = array(
			'id_pegawai' => $id_pegawai,
			'periode_bulan' => $bulan,
			'periode_tahun' => $tahun,
			'tgl_input' => $tgl,
			'id_pertanyaan' => $id_item,
			'jns_item' => $jns_item,
			'jawaban' => $jawaban,
			'poin' => $poin,

		);

		$this->db->insert('tbl_penilaian_perilaku', $data);
	}

	public function search_aktivitas($term)
	{
		$this->db->like('nama_kegiatan', $term);
		$query = $this->db->get('mst_kegiatan'); // nama tabel kamu
		return $query->result_array();
	}

	function getDataRenkinPegawai($nip)
	{
		// $this->db->where('user_id', $nip);
		// $this->db->select('*');
		// $qry = $this->db->get('ts_renkin');
		// //$this->db->join('mst_indikator_kegiatan', 'ts_renkin.id_indikator = mst_indikator_kegiatan.id');
		// $row = $qry->result();


		$sql = "SELECT a.*, b.*
		FROM ts_renkin a LEFT JOIN mst_indikator_kegiatan b ON a.id_indikator = b. id
		WHERE user_id = '$nip'";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;
	}
}
