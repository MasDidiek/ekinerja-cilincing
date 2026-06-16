<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');
class Presensi_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}


    public function get_absensi_pegawai($pin, $bulan, $tahun)
    {
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $start = $tahun . '-' . $bulan . '-01';
        $end   = date('Y-m-t', strtotime($start));

		$table = ($tahun < 2026) ? 'tbl_absensi' : 'tbl_kehadiran_harian';

            return $this->db
			->select('a.*, s.nama_shift, s.jam_masuk jam_shift_masuk, s.jam_pulang as jam_shift_pulang')
			->from($table . ' as a')
			->join('mst_shift_kerja as s', 's.kode_shift = a.shift', 'left')
			->where('a.pin', $pin)
			->where('a.tanggal >=', $start)
			->where('a.tanggal <=', $end)
			->order_by('a.tanggal', 'ASC')
			->get()
                ->result();
   }
    


	
	function getDetailPegawaiByNip($nip, $jns_pegawai = 'non_pns')
	{

		$this->db->where('mst_pegawai.jns_pegawai', $jns_pegawai);
		$this->db->where('mst_pegawai.nip', $nip);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');


		$qry = $this->db->get();

		$row = $qry->row();
		return $row;
	}



	function getDetailPegawaiByPin($pin, $jns_pegawai = 'non_pns')
	{

		$this->db->where('mst_pegawai.jns_pegawai', $jns_pegawai);
		$this->db->where('mst_pegawai.pin', $pin);
		$this->db->select('mst_pegawai.*, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');


		$qry = $this->db->get();

		$row = $qry->row();
		return $row;
	}



	function update_absensi_cuti($pin, $tanggal, $alasan_cuti)
	{
		$data = array(
			'shift' => 'OFF',
			'status' => 'CUTI',
			'telat_menit' => 0,
			'p_awal_menit' => 0,
			'keterangan' => $alasan_cuti
		);


		//print_array($data);
		$this->db->where('pin', $pin);
		$this->db->where('tanggal', $tanggal);
		$this->db->update('tbl_kehadiran_harian', $data);

		return true;
	}



	
	// public function update_absensi_khusus($referensi_table = 'ts_hari_libur', $status_filter = 'HADIR', $shift_filter = 'REG', $default_telat = 0, $default_pulang_awal = 0)
	// {
	// 	// ambil semua tanggal dari tabel referensi
	// 	$this->db->select('tgl, keterangan');
	// 	$this->db->from($referensi_table);
	// 	$query = $this->db->get();
	// 	$hari_khusus = $query->result();

	// 	//print_array($hari_khusus);
	// 	if (empty($hari_khusus)) return false; // jika tidak ada data, langsung keluar

	// 	foreach ($hari_khusus as $row) {
	// 		$this->db->set('status', 'OFF');
	// 		$this->db->set('shift', 'OFF');
	// 		$this->db->set('status_detail', 'LIBUR NASIONAL');
	// 		$this->db->set('telat_menit', $default_telat);
	// 		$this->db->set('p_awal_menit', $default_pulang_awal);
	// 		$this->db->set('keterangan', $row->keterangan);
	// 		$this->db->where('tanggal', $row->tgl);
	// 		$this->db->where('status', $status_filter);
	// 		$this->db->where_in('shift', ['REG', 'REG-JUM']); // <-- update sini
	// 		$this->db->update('tbl_kehadiran_harian');
	// 	}

	// 	return true;
	// }

	
	function saveAbsensiIzinSakit($pin, $tanggal, $jenis_absen, $status_detail, $ket = '')
	{
	
		$sql = $this->db->get_where('tbl_kehadiran_harian', array('tanggal' => $tanggal, 'pin' => $pin));
		$row = $sql->row();

		// Default struktur data
		$data = [
			'tanggal'      => $tanggal,
			'pin'          => $pin,
			'telat_menit'  => 0,
			'p_awal_menit' => 0,
			'status'       => $jenis_absen,
			'status_detail' => $status_detail,
			'keterangan'   => $ket
		];

	
		// Jika belum ada → INSERT
		if (empty($row)) {

			$this->db->insert('tbl_kehadiran_harian', $data);
			return $this->db->affected_rows() > 0;
		} 
		// Jika sudah ada → UPDATE
		else {

			// Untuk update tidak perlu ubah tanggal & pin
			unset($data['tanggal'], $data['pin']);

			$this->db->where('id', $row->id);
			$this->db->update('tbl_kehadiran_harian', $data);

			return $this->db->affected_rows() >= 0;
		}
	}

	


	function saveAbsensiDL($pin, $tanggal, $jns_dl, $ket = '')
	{
		// Cek apakah data sudah ada
		//$id = $this->cekAbsenExist($tanggal, $pin);

		$sql = $this->db->get_where('tbl_kehadiran_harian', array('tanggal' => $tanggal, 'pin' => $pin));
		$row = $sql->row();

		// Default struktur data
		$data = [
			'tanggal'      => $tanggal,
			'pin'          => $pin,
			'telat_menit'  => 0,
			'p_awal_menit' => 0,
			'status'       => 'DINAS',
			'status_detail' => $jns_dl,
			'keterangan'   => $ket
		];

		// print_array($row);
		// exit;

		// Jika belum ada → INSERT
		if (empty($row)) {

			$this->db->insert('tbl_kehadiran_harian', $data);
			return $this->db->affected_rows() > 0;
		} 
		// Jika sudah ada → UPDATE
		else {

			// Untuk update tidak perlu ubah tanggal & pin
			unset($data['tanggal'], $data['pin']);

			$this->db->where('id', $row->id);
			$this->db->update('tbl_kehadiran_harian', $data);

			return $this->db->affected_rows() >= 0;
		}
	}



	function update_absensi_pegawai($pin, $bulan, $tahun)
	{
		$this->db->where('pin', $pin);
		$this->db->where('MONTH(tanggal)', $bulan);
		$this->db->where('YEAR(tanggal)', $tahun);
		$data = $this->db->get('tbl_kehadiran_harian')->result();

		if (!$data) return true;

		// Shift yang nyebrang hari
		$shift_cross = ['M','SM','PSM'];

		foreach ($data as $row) {

			// ==========================================
			// JANGAN SENTUH STATUS FINAL
			// ==========================================
			if (in_array($row->status, ['CUTI','IZIN','SAKIT','DINAS','ISOMAN'])) {
				continue;
			}

			$status = $row->status;
			$telat = 0;
			$pulang_awal = 0;

			// ==========================================
			// AMBIL DATA SHIFT
			// ==========================================
			$shift = $this->db
				->where('kode_shift', $row->shift)
				->where('publish', 1)
				->get('mst_shift_kerja')
				->row();

			if (!$shift) continue;

			$tanggal = $row->tanggal;
			$jam_masuk_shift  = $shift->jam_masuk;
			$jam_pulang_shift = $shift->jam_pulang;

			// ==========================================
			// 1️⃣ SHIFT OFF
			// ==========================================
			if ($row->shift == 'OFF') {

				$status = 'OFF';
				$telat = 0;
				$pulang_awal = 0;
			}

			// ==========================================
			// 2️⃣ SHIFT NYEBRANG HARI (M, SM, PSM)
			// ==========================================
			elseif (in_array($row->shift, $shift_cross)) {

				// HITUNG TELAT SAJA
				if (empty($row->jam_masuk)) {
					$telat = 300;
				} else {

					$shift_masuk = strtotime($tanggal . ' ' . $jam_masuk_shift);
					$absen_masuk = strtotime($tanggal . ' ' . $row->jam_masuk);

					$telat = max(0, ($absen_masuk - $shift_masuk) / 60);
				}

				$pulang_awal = 0;

				$status = ($telat > 0) ? 'TELAT' : 'HADIR';

			
			}

			// ==========================================
			// 3️⃣ SHIFT L-OFF (LANJUTAN SHIFT MALAM)
			// ==========================================
			elseif ($row->shift == 'L-OFF') {

				// HITUNG PULANG AWAL SAJA
				if (empty($row->jam_pulang)) {
					$pulang_awal = 150;
				} else {

					$shift_pulang = strtotime($tanggal . ' ' . $jam_pulang_shift);
					$absen_pulang = strtotime($tanggal . ' ' . $row->jam_pulang);

					$pulang_awal = max(0, ($shift_pulang - $absen_pulang) / 60);
				}

				$telat = 0;

				$status = ($pulang_awal > 0) ? 'TELAT' : 'HADIR';

					
			
			}

	
			// ==========================================
			// 4️⃣ SHIFT NORMAL (TIDAK NYEBRANG HARI)
			// ==========================================
			else {

				if (empty($row->jam_masuk) && empty($row->jam_pulang)) {

					$status = 'ALPHA';
					$telat = 0;
					$pulang_awal = 0;

				} else {

					// ======================
					// TELAT
					// ======================
					if (empty($row->jam_masuk)) {
						$telat = 300;
					} else {

						$shift_masuk = strtotime($tanggal . ' ' . $jam_masuk_shift);
						$absen_masuk = strtotime($tanggal . ' ' . $row->jam_masuk);

						$telat = max(0, ($absen_masuk - $shift_masuk) / 60);
					}

					// ======================
					// PULANG AWAL
					// ======================
					if (empty($row->jam_pulang)) {
						$pulang_awal = 150;
					} else {

						$shift_pulang = strtotime($tanggal . ' ' . $jam_pulang_shift);
						$absen_pulang = strtotime($tanggal . ' ' . $row->jam_pulang);

						$pulang_awal = max(0, ($shift_pulang - $absen_pulang) / 60);
					}

					if ($telat > 0 || $pulang_awal > 0) {
						$status = 'TELAT';
					} else {
						$status = 'HADIR';
					}
				}
			}


		
			// ==========================================
			// UPDATE DATABASE
			// ==========================================
			$this->db->where('id', $row->id);
			$this->db->update('tbl_kehadiran_harian', [
				'status'        => $status,
				'telat_menit'   => (int)$telat,
				'p_awal_menit'  => (int)$pulang_awal
			]);
		}

		return true;
	}

	public function generate_rekap_bulanan($pin, $id_pegawai, $bulan, $tahun)
	{
		$periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

		$this->db->where('pin', $pin);
		$this->db->where('MONTH(tanggal)', $bulan);
		$this->db->where('YEAR(tanggal)', $tahun);
		$data = $this->db->get('tbl_kehadiran_harian')->result();

		$rekap = [
			'telat' => 0,
			'pulang_awal' => 0,
			'izin' => 0,
			'sakit' => 0,
			'sakit_dgn_sk' => 0,
			'alpha' => 0,
			'cuti' => 0,
			'isoman' => 0,
			'dl_penuh' => 0,
			'dl_awal' => 0,
			'dl_akhir' => 0
		];

		foreach ($data as $row) {
	// ======================
			// TOTAL MENIT TELAT
			// ======================
			if ($row->telat_menit > 0) {
				$rekap['telat'] += $row->telat_menit;
			}

			// ======================
			// TOTAL MENIT PULANG AWAL
			// ======================
			if ($row->p_awal_menit > 0) {
				$rekap['pulang_awal'] += $row->p_awal_menit;
			}

			switch ($row->status) {

				case 'IZIN':
					$rekap['izin']++;
					break;

				case 'SAKIT':
					if ($row->status_detail == 'DENGAN DGN SURAT') {
						$rekap['sakit_dgn_sk']++;
					} else {
						$rekap['sakit']++;
					}
					break;

				case 'ALPHA':
					$rekap['alpha']++;
					break;

				case 'CUTI':
					$rekap['cuti']++;
					break;

				case 'DINAS':
					$rekap['dl_penuh']++;
					break;

				case 'ISOMAN':
					$rekap['isoman']++;
					break;
			}
		}

		// ============================
		// CEK APAKAH SUDAH ADA REKAP
		// ============================

		$cek = $this->db
			->where('id_pegawai', $id_pegawai)
			->where('periode', $periode)
			->get('ts_rekap_absensi')
			->row();

		$data_insert = array_merge($rekap, [
			'id_pegawai' => $id_pegawai,
			'periode' => $periode,
			'status' => 1,
			'date_update' => date('Y-m-d H:i:s')
		]);



		//print_array($data_insert);
		if (!$cek) {
			$this->db->insert('ts_rekap_absensi', $data_insert);
		} else {
			$this->db->where('id', $cek->id);
			$this->db->update('ts_rekap_absensi', $data_insert);
		}
		//exit;

		return true;
	}







	function getNamaPuskesmas($id_puskesmas){
		$this->db->where('id_puskesmas', $id_puskesmas);
		$qry = $this->db->get('mst_puskesmas');
		$row = $qry->result();
		$nama = $row[0]->nama;

		return $nama;
	}

	function getListPuskesmas(){

		$this->db->order_by('id_puskesmas', 'ASC');
		$qry = $this->db->get('mst_puskesmas');
		$row = $qry->result();
	    return $row;
	}




	function insertDataPegawai(){


				$new_data = array(
					'nama_pegawai' =>$this->input->post('nama'),
					'nip' => $this->input->post('nip'),
					'pin' =>$this->input->post('pin'),
					'rumpun_kerja' =>$this->input->post('rumpun_kerja'),
					'id_puskesmas' => $this->input->post('puskesmas'),
					'shift' => $this->input->post('shift'),
					'jns_pegawai' => 'reg',
					'status' => 1
				);

				#print_array($new_data );
				$this->db->insert('mst_pegawai', $new_data);

				return true;

	}


	function updateStatusAbsensiRekap($id_rekap){
		$this->db->where('id', $id_rekap);
		$this->db->set('status', 1);
		$this->db->update('ts_rekap_absensi');

		return true;
	}


	function countAbsenSesuai($periode, $status = 0){

		$qry = $this->db->get_where('ts_rekap_absensi', array('periode'=> $periode, 'status'=> $status ));
		$row = $qry->num_rows();
		return $row;
	}
	
	
	// function getDataRekapAbsensi($id_pegawai, $periode='2023-02'){
	// 	$sql = "SELECT * FROM `ts_rekap_absensi` WHERE id_pegawai = $id_pegawai AND periode = '$periode' ORDER BY periode ASC";
	// 	$qry = $this->db->query($sql);

	// 	return $qry->result();
	// }


	
	function getDataRekapAbsensi($periode, $order_by, $jns_pegawai){
		$this->db->order_by($order_by,"ASC");

		$this->db->select('mst_pegawai.nama,mst_pegawai.pin, a.*');
		$this->db->where('periode', $periode);
		$this->db->where('jns_pegawai', $jns_pegawai);
		$this->db->where('status_kerja !=', 0);
		$this->db->from('ts_rekap_absensi a');
		$this->db->join('mst_pegawai', 'a.id_pegawai = mst_pegawai.id_pegawai', 'left');
		$qry = $this->db->get();
		$row = $qry->result();

		return $row;
	}


	
    function getRekapAbsensiTahunan($id_pegawai, $tahun){

		$sql = "SELECT * FROM ts_rekap_absensi WHERE id_pegawai =  $id_pegawai AND periode LIKE '$tahun%' ORDER BY id DESC;";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;

    }

    function getRekapAbsensiPegawai($id_pegawai, $periode){

		$qry = $this->db->get_where('ts_rekap_absensi', array('id_pegawai' => $id_pegawai, 'periode'=> $periode));
		$row = $qry->result();
		return $row;

    }


	function getAbsensiByStatus($periode, $status = 0){

        $this->db->select('mst_pegawai.*');
        $this->db->where('periode', $periode);
        $this->db->where('status', $status);
        $this->db->from('ts_rekap_absensi');
    
		$this->db->join('mst_pegawai', 'ts_rekap_absensi.id_pegawai = mst_pegawai.id_pegawai', 'left');
		$qry = $this->db->get();
		$row = $qry->result();
		
	
		
		return $row;
	}




	function updateDataPegawai($id_pegawai){


			$new_data = array(
				'nama_pegawai' =>$this->input->post('nama'),
				'nip' => $this->input->post('nip'),
				'pin' =>$this->input->post('pin'),
				'rumpun_kerja' =>$this->input->post('rumpun_kerja'),
				'id_puskesmas' => $this->input->post('puskesmas'),
				'shift' => $this->input->post('shift'),
			);

			$this->db->where('id_pegawai', $id_pegawai);
			$this->db->update('mst_pegawai', $new_data);

			return true;

	}

	function getIDPegawaiByNip($nip){

		$qry = $this->db->get_where('mst_pegawai', array('nip' => $nip));
		$row = $qry->result();
		$id_pegawai = $row[0]->id_pegawai;
		return $id_pegawai;


    }



    function getListPegawai(){
		$thn_anggaran = '2024';
		$qry = $this->db->get_where('mst_pegawai', array('jns_pegawai' => 'non_pns', 'status_kerja > '=> 0, 'tahun_anggaran'=> $thn_anggaran), 25, 0);
		$row = $qry->result();
		return $row;

    }

    function getListPegawaiUKP_UKM($rumpun_kerja){
		$thn_anggaran = '2024';
		$qry = $this->db->get_where('mst_pegawai', array('jns_pegawai' => 'non_pns', 'status_kerja > '=>0, 'rumpun_kerja'=> $rumpun_kerja, 'tahun_anggaran'=> $thn_anggaran));
		$row = $qry->result();
		return $row;

    }
	function getListPegawaiKelurahan($id_puskesmas){
		$thn_anggaran = '2024';
		$this->db->order_by('nama', 'ASC');
		$qry = $this->db->get_where('mst_pegawai', array('jns_pegawai' => 'non_pns', 'status_kerja > '=>0, 'id_puskesmas'=> $id_puskesmas, 'tahun_anggaran'=> $thn_anggaran));
		$row = $qry->result();
		return $row;

    }



	function getDataCutiPegawai($id_pegawai){
		$this->db->order_by('tgl', 'DESC');
		$qry = $this->db->get_where('ts_cuti', array('id_pegawai' => $id_pegawai), 5,0);
		$row = $qry->result();
		return $row;

    }
	function deleteAbsenCuti($id_cuti){

		$this->db->where('id', $id_cuti);
		$this->db->delete('absensi_cuti');
		return true;
	}



	function cekCutiPegawai($id_pegawai, $tanggal){

		$qry = $this->db->get_where('absensi_cuti', array('id_pegawai' => $id_pegawai, 'tanggal'=>$tanggal));
		$row = $qry->result();
		return $row;

    }

	function cekHariLibur($tanggal){

		$qry = $this->db->get_where('ts_hari_libur', array('tgl'=>$tanggal));
		$row = $qry->result();
		return $row;

    }



	function insertPengajuanIzinSakit($id_pegawai, $tgl, $jns_absensi, $ket){
		


		//echo $jns_absensi;
		
		if($jns_absensi=='IZIN'){
			$jns_izin = $this->input->post('jns_izin');
		}else{
			$jns_izin=0;
		}


		$newarray = array(
			'id_pegawai' => $id_pegawai,
			'tanggal ' => $tgl,
			'jenis_absen' => $jns_absensi,
			'jns_izin'    => $jns_izin,
			'status' => 1,
			'keterangan' => $ket
		);


		$this->db->insert('pengajuan_izin_sakit', $newarray);

		// print_array($newarray);
		// exit;

		return true;
	}



	function getJnsIzin($id_pegawai, $periode){
		$sql = "SELECT jns_izin FROM pengajuan_izin_sakit  WHERE id_pegawai = $id_pegawai AND jenis_absen = 'IZIN' AND tanggal like '$periode%'";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;
	}
	
	function getDataIzinSakitPegawai($id_pegawai){
		$this->db->select('tanggal, keterangan, jenis_absen');
		$qry = $this->db->get_where('pengajuan_izin_sakit', array('id_pegawai' => $id_pegawai));
		$row = $qry->result();
		return $row;

    }


	 function cekIzinSakit($id_pegawai, $tanggal){

		$this->db->select('id, jenis_absen, jns_izin');
		$qry = $this->db->get_where('pengajuan_izin_sakit', array('id_pegawai' => $id_pegawai, 'tanggal'=>$tanggal));
		$row = $qry->result();
		return $row;




    }

	function pengajuanDinasLuarPegawai($id_validator=0, $status=''){

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
			$periode = date('Y-m', strtotime($periode));


		#	print_array($this->session->userdata);
			if($id_validator==0){
				$and = "";
			}else{
				$and = "AND b.id_validator = $id_validator";
			}

			if($status =='Pending'){
				$AND2 = "AND status = 0";
			}elseif($status=='Disetujui'){
				$AND2 = "AND status = 1";
			}elseif($status=='Ditolak'){
				$AND2 = "AND status = 2";
			}else{
					$AND2 = "";
			}
			// $sql = "SELECT a.*, b.nama
			// FROM pengajuan_dinas_luar a
			// LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai WHERE status = 0 AND tanggal >= '2024-02-01' $and ";

			$sql = "SELECT a.*, b.nama
			FROM pengajuan_dinas_luar a
			LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai WHERE  tanggal LIKE '$periode%' $and $AND2  ";
			$qry = $this->db->query($sql);

			
			$row = $qry->result();

	
			return $row;

		}



	function getPengajuanDlPending()  {
		
		$sql = "SELECT a.*, b.nama, b.id_validator, b.nip
		FROM pengajuan_dinas_luar a LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai 
		WHERE  status = 0 ORDER BY  b.nama ASC  LIMIT 300 OFFSET 0";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;
	}


	function getDataPengajuanDL($id_pegawai, $tanggal){


		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id_pegawai' => $id_pegawai, 'tanggal'=>$tanggal));
		$row = $qry->result();
		return $row;

    }


	function getDataPengajuanDLPerbulan($id_pegawai, $periode){

		$sql = "SELECT * FROM pengajuan_dinas_luar WHERE id_pegawai = $id_pegawai AND tanggal like '$periode%' order by tanggal ASC";
		$qry = $this->db->query($sql);
		$row = $qry->result();
		return $row;

    }

	function deletePengajuanIzinSakit($id_pegawai, $tgl){
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('tanggal', $tgl);
		$this->db->delete('pengajuan_izin_sakit');
		return true;
	}



	function cekDL($id_pegawai, $tanggal, $jns_dl){

		$qry = $this->db->get_where('pengajuan_dinas_luar', array('id_pegawai' => $id_pegawai, 'tanggal'=>$tanggal, 'jns_dl' => $jns_dl, 'status'=> 1));
		$row = $qry->result();
		return $row;

    }

	function cekDataShift($id_pegawai, $tanggal){

		$qry = $this->db->get_where('shift_kerja', array('id_pegawai' => $id_pegawai, 'tanggal'=>$tanggal));
		$row = $qry->result();
		return $row;

    }

	function deleteTblAbsensi($pin, $tanggal){
		$this->db->where('pin', $pin);
		$this->db->where('tanggal', $tanggal);
		$this->db->delete('tbl_absensi');
		return true;
	}

	function deleteShiftPegawai($id_pegawai, $tanggal){
		$this->db->where('id_pegawai', $id_pegawai);
		$this->db->where('tanggal', $tanggal);
		$this->db->delete('shift_kerja');
		return true;
	}


	// function insertAbsensiCuti($tanggal, $pin, $ket=''){

	// 	$cekAbsenExist = $this->cekAbsenExist($tanggal, $pin);
	// 	if($cekAbsenExist==false){
	// 		$newArray = array(
	// 			'tanggal' => $tanggal,
	// 			'pin' => $pin,
	// 			'shift' => 'REG',
	// 			'jam_masuk' => '07:30:00',
	// 			'jam_pulang' => '16:00:00',
	// 			'masuk' => 'CUTI',
	// 			'pulang' => 'CUTI',
	// 			'telat' => 0,
	// 			'p_awal' => 0,
	// 			'keterangan' =>  $ket
	// 		);

	// 		#print_array($newArray);

	// 	$this->db->insert('tbl_absensi', $newArray);

	// 	}else{

	// 		$id = $cekAbsenExist;
	// 		$newArray = array(
	// 			'masuk' => 'CUTI',
	// 			'pulang' => 'CUTI',
	// 			'telat' => 0,
	// 			'p_awal' => 0,
	// 			'keterangan' => ''
	// 		);

	// 		#print_array($newArray);
	// 		$this->db->where('id', $id);
	// 		$this->db->update('tbl_absensi', $newArray);

	// 	}

	// 	return true;


	// }

	function insertShiftPegawai($pin, $tanggal, $shift){
            $JamKerjaShift = $this->detailShiftByKode($shift);
            $jamMasuk  = $JamKerjaShift[0]->jam_masuk;
            $jamPulang  = $JamKerjaShift[0]->jam_pulang;


			$newArray = array(
				'tanggal' => $tanggal,
				'pin' => $pin,
				'shift' => $shift,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
				'masuk' => '',
				'pulang' => '',
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => ''
			);

			$this->db->insert('tbl_absensi', $newArray);

			$insert_id = $this->db->insert_id();

			return  $insert_id;
	}

	
	
	function  updateShiftPegawai($pin, $tanggal, $shift, $id){

			$JamKerjaShift = $this->detailShiftByKode($shift);
			$jamMasuk  = $JamKerjaShift[0]->jam_masuk;
			$jamPulang  = $JamKerjaShift[0]->jam_pulang;

			$newArray = array(
				'shift' => $shift,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
			);

			$this->db->where('id', $id);
			$this->db->update('tbl_absensi', $newArray);
	}



    function updateTableAbsensi($periode, $pin, $shiftKerja){

        $data_absensi = $this->getAbsensiPegawai($pin, $periode);


        foreach ($data_absensi as $list) {

            $id      = $list->id;
            $tanggal = $list->tanggal;
            $absen   = $this->getRawAbensiDB($pin, $tanggal);

//            $shift = $this->Presensi_model->generate_shift_kerja($pin, $tanggal);
//            $this->Presensi_model->updateShiftPegawai($pin, $tanggal, $shift, $id);
           // print_array($absen);
            $absen_masuk = '';
            $absen_pulang = '';
            if (!empty($absen)) {
                for ($a = 0; $a < count($absen); $a++) {
                    $tanggal = $absen[$a]->tanggal;
                    $status = $absen[$a]->status;
                    $jam = date('H:i:s', strtotime($tanggal));

                    if($shiftKerja=='non_shift'){

                        $explode = explode(":", $jam);
                        $hour    = $explode[0];

                        if($hour < 10){
                            $status=0;
                        }else{
                            $status=1;
                        }

                    }

                    if ($status == 0) {

                        $this->db->where('id', $id);
                        $this->db->set('masuk', $jam);
                        $this->db->update('tbl_absensi');

                    } else {
                        $this->db->where('id', $id);
                        $this->db->set('pulang', $jam);
                        $this->db->update('tbl_absensi');
                    }

                }//close for
            } //close if


        }//close foreach



        return true;

    }


	// function updateShiftPegawai($id, $shift){

	// 	$this->db->where('id', $id);
	// 	$this->db->set('shift', $shift);
	// 	$this->db->update('shift_kerja');
	// 	return true;
	// }


	function getRawAbensiPertanggal($pin, $tgl, $orderBy='ASC')
	{

		$this->db->where('pin', $pin);
		$this->db->order_by('tanggal', $orderBy);
		$this->db->like('tanggal', $tgl, 'after');
		$qry = $this->db->get('ts_absensi');
		$row = $qry->result();
	    return $row;
	}
	function getRawAbensi($pin, $tgl)
	{

		$this->db->where('pin', $pin);
		$this->db->order_by('tanggal', 'ASC');
		$this->db->like('tanggal', $tgl, 'after');
		$qry = $this->db->get('ts_absensi');
		$row = $qry->result();
	    return $row;
	}

	function getRawAbensiDB($pin, $tgl)
	{

		$this->db->where('pin', $pin);
		$this->db->order_by('tanggal', 'ASC');
		$this->db->like('tanggal', $tgl, 'after');
		$qry = $this->db->get('ts_import_absensi');
		$row = $qry->result();
	    return $row;
	}

	function getDataRekapAbsensiByValidator($periode='2025-02'){

		$id_validator = $this->session->userdata('id_pegawai');
 	    $id_pj_sess = $this->session->userdata('id_pj');

		if($id_pj_sess !=''){
			$id_validator = $id_pj_sess;
		}
		$sql = "
			SELECT 
				a.nama, 
				a.nip, 
				a.id_pegawai,
				a.jns_jam_kerja,
				COALESCE(b.telat, 0) AS telat,
				COALESCE(b.izin, 0) AS izin,
				COALESCE(b.sakit, 0) AS sakit,
				COALESCE(b.sakit_dgn_sk, 0) AS sakit_dgn_sk,
				COALESCE(b.alpha, 0) AS alpha,
				COALESCE(b.pulang_awal, 0) AS pulang_awal,
				COALESCE(b.cuti, 0) AS cuti,
				COALESCE(b.status, 2) AS status
			FROM mst_pegawai a
			LEFT JOIN ts_rekap_absensi b 
				ON a.id_pegawai = b.id_pegawai
				AND b.periode = '$periode'
			
			WHERE 
				a.status_kerja != 0 
				AND a.jns_pegawai = 'non_pns'
					AND a.id_validator = $id_validator
			ORDER BY a.nama ASC
		";
		$qry = $this->db->query($sql);

		$row = $qry->result();


		return $row;

	
	}


	function EditRawAbsen($id){



		$sql = $this->db->get_where('ts_absensi', array('id' => $id));
		$row = $sql->result();

		$status = $row[0]->status;
		if ($status == 0) {
			$this->db->where('id', $id);
			$this->db->set('status', 1);
			$this->db->update('ts_absensi');
			echo '<span class="badge bg-warning">KEL</span>';
		} else {
			$this->db->where('id', $id);
			$this->db->set('status', 0);
			$this->db->update('ts_absensi');
			echo '<span class="badge bg-success">MSK</span>';
		}

		return true;

	}


	function cekAbsensi($tanggal, $pin){
		$qry = $this->db->get_where('ts_absensi', array('tanggal'=> $tanggal, 'pin'=> $pin));
		$row = $qry->num_rows();
		return $row;
	}



	function deleteRawAbsensi($id)
	{

		$this->db->where('id', $id);
		$this->db->delete('ts_absensi');
		return true;
	}


	function deleteDataShift($id_pegawai, $periode){
		$sql = "DELETE FROM `shift_kerja` WHERE tanggal like '$periode%' AND id_pegawai= $id_pegawai";

		$this->db->query($sql);
		return true;

	}

	function deleteAbsenDL($id)
	{

		$this->db->where('id', $id);
		$this->db->delete('pengajuan_dinas_luar');
		return true;
	}

	function deleteAbsenIzinSakit($id)
	{

		$this->db->where('id', $id);
		$this->db->delete('absensi_izin');
		return true;
	}

	function getNamaPegawai($id_pegawai){

		$this->db->select('nama');
		$sql = $this->db->get_where('mst_pegawai', array('id_pegawai' => $id_pegawai));
		$row = $sql->result();

		$nama = $row[0]->nama;
		return $nama;
	}




	function cekDataRekapAbsensi($id_pegawai, $periode ){
		$sql = $this->db->get_where('ts_rekap_absensi', array('id_pegawai' => $id_pegawai, 'periode' => $periode));
		$row = $sql->result();

		if(empty($row)){
			return 0;
		}else{
			$id = $row[0]->id;
			return $id;
		}

	}


	function insertDataCuti($data_cuti){

		$this->db->insert_batch('absensi_cuti', $data_cuti);
		return true;
	}

	function getDataInitialShift($tgl){
		$this->db->where('tgl', $tgl);
		$qry = $this->db->get('tbl_initial_shift');
		$row = $qry->result();
		return $row;
	}


    function getShiftKerja(){
		$this->db->where('status_kerja', 'non_pns');
		$this->db->where('publish', 1);
		$this->db->order_by('urutan','ASC');
		$qry = $this->db->get('mst_shift_kerja');
		$row = $qry->result();
		return $row;

    }

	function getIpaddressByPuskesmas($id_puskesmas, $ket=''){

		$qry = $this->db->get_where('tbl_mesin_absensi', array('id_puskesmas' => $id_puskesmas, 'ket'=> $ket));
		$row = $qry->result();
		$ip  = $row[0]->ip_address;
		return $ip;
	}

    function getDetPegawai($pin){

		$qry = $this->db->get_where('mst_pegawai', array('pin' => $pin));
		$row = $qry->result();
		return $row;

    }

	function getdetPegawaiBYNIP($nip){

		$qry = $this->db->get_where('mst_pegawai', array('nip' => $nip));
		$row = $qry->result();
		return $row;

    }



	public function getShiftPegawai($id_pegawai, $tgl)
    {


        $qry = $this->db->get_where('ts_shift_kerja', array('id_pegawai' => $id_pegawai, 'tanggal' => $tgl));
        $row = $qry->result();
        if (!empty($row)) {
            $shift = $row[0]->shift;
            return $shift;
        } else {
            return '';
        }
    }


    function detailShiftByKode($kode, $result='')
    {
        $qry = $this->db->get_where('mst_shift_kerja', array('kode_shift' => $kode));
		if($result==''){
 			$row = $qry->result();
		}else{
 			$row = $qry->row();
		}
       

        return $row;
    }


    public function getDataAbsenRaw($pin, $tanggal = '2023-08')
    {

        $sql = "SELECT * FROM ts_import_absensi WHERE tanggal like '$tanggal%' AND pin = '$pin'";
        # echo $sql;
        $qry = $this->db->query($sql);
        $row = $qry->result();
		return $row;
    }

	function insertDataAbsen($pin){

		$tahun = $this->session->userdata('periode_tahun');
        $bulan = $this->session->userdata('periode_bulan');
		$periode = $tahun . '-' . $bulan;
        $periode = date('Y-m', strtotime($periode));

			$absen = $this->getDataAbsenRaw($pin,  $periode);

			for ($i=0; $i < count($absen); $i++) {
						$datetime = $absen[$i]->tanggal;
						$status = $absen[$i]->status;
						$this->insertAbsensi($datetime, $pin, $status);


			}
			return true;
	}


	function insertAbsensi($datetime, $pin, $status){
		$newArray = array(
			'tanggal' => $datetime,
			'pin' => $pin,
			'status' => $status
		);

		$this->db->insert('ts_absensi', $newArray);
		return true;
	}


	function insertAbsensiDL($pin, $tanggal, $jns_dl, $ket=''){

		$dataDefault = [
				'tanggal' => $tanggal,
				'pin' => $pin,
				'masuk' => '',
				'pulang' => '',
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => $ket
			];

			if($jns_dl == 'DLA'){
				$dataDefault['masuk'] = $jns_dl;
				$dataDefault['p_awal'] = 150;
			} elseif($jns_dl == 'DLAK'){
				$dataDefault['pulang'] = $jns_dl;
				$dataDefault['telat'] = 300;
			} else {
				$dataDefault['masuk'] = $jns_dl;
				$dataDefault['pulang'] = $jns_dl;
			}

		$this->db->insert('tbl_absensi', $dataDefault);
		return $this->db->affected_rows() > 0;

	}


	function insertAbsensiIzinSakit($pin, $tanggal, $jns_absen, $jns_izin, $ket=''){

		$cekAbsenExist = $this->cekAbsenExist($tanggal, $pin);

		if($cekAbsenExist==false){

				$newArray = array(
					'tanggal' => $tanggal,
					'pin' => $pin,
					'masuk' => $jns_absen,
					'pulang' => $jns_absen,
					'telat' => 0,
					'p_awal' => 0,
					'keterangan' => $ket
				);



			$this->db->insert('tbl_absensi', $newArray);

		}else{
			$id = $cekAbsenExist;

				if ($jns_absen=='IZIN') {
					if($jns_izin== '2') {
						//setngh hari awal
						$newArray = array(
							'masuk' => $jns_absen,
							'telat' => 0,
							'keterangan' => $ket
						);
		
					}else if($jns_izin== '3'){
						//stngh hari akhir
						$newArray = array(
							'pulang' => $jns_absen,
							'p_awal' => 0,
							'keterangan' => $ket
						);
		
					}else{
						//izin seharian

						$newArray = array(
							'masuk' => $jns_absen,
							'pulang' => $jns_absen,
							'telat' => 0,
							'p_awal' => 0,
							'keterangan' => $ket
						);
					}
				}else{

					$newArray = array(
						'masuk' => $jns_absen,
						'pulang' => $jns_absen,
						'telat' => 0,
						'p_awal' => 0,
						'keterangan' => $ket
					);
	

				}



			$this->db->where('id', $id);
			$this->db->update('tbl_absensi', $newArray );

			// print_array($newArray);
			// exit;

		}

		return true;

	}


    function getlistTblAbsensi($periode, $pin){
        $sql = "SELECT *  FROM tbl_absensi WHERE pin = '$pin' AND tanggal like '$periode%'";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }






	function insertAbsensiHarian($pin, $datetime,  $status,  $id_absen, $ket=''){

		$explode = explode(" ", $datetime);
		$tanggal = $explode[0];
		$jam     = $explode[1];


		$masuk  = '';
		$pulang = '';

		if($status==0){
			$masuk  = $jam;

			$newArray = array(
				'tanggal' => $tanggal,
				'pin' => $pin,
				'masuk' => $masuk,
				'telat' => 0,
				'keterangan' => $ket
			);


		}

		if($status==1){
			$pulang  = $jam;
			$newArray = array(
				'tanggal' => $tanggal,
				'pin' => $pin,
				'pulang' => $pulang,
				'p_awal' => 0,
				'keterangan' => $ket
			);

		}


		//khusus untuk absensi IZIN, SAKIT, DL
		if($status==2){
			$masuk  = $jam;
			$pulang  = $jam;

			$newArray = array(
				'tanggal' => $tanggal,
				'pin' => $pin,
				'masuk' => $masuk,
				'pulang' => $pulang,
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => $ket
			);

		}



		$cekAbsenExist = $this->cekAbsenExist($tanggal, $pin);
		if($cekAbsenExist==false){



			$this->db->insert('tbl_absensi', $newArray);
		}else{


			$data = array(
				'masuk'=> $masuk,
				'pulang' => $pulang,
				'keterangan'=> $ket
			);

			$this->db->where('pin', $pin);
			$this->db->where('tanggal', $tanggal);
			$this->db->update('tbl_absensi', $data );

		}

		$this->db->where('id', $id_absen);
		$this->db->set('status_update', 1);
		$this->db->update('ts_absensi');

		return true;
	}

	function getAbsensiPegawaiPerbulan($pin, $periode){
		  $kehadiran = $this->db
            ->where('pin', $pin)
            ->where('DATE_FORMAT(tanggal,"%Y-%m") =', $periode)
            ->get('tbl_kehadiran_harian')
            ->result();
			return $kehadiran;

	}

	// function getAbsensiPegawai($pin, $periode){

	// 	$sql = "SELECT * FROM tbl_kehadiran_harian WHERE pin = $pin AND tanggal like '$periode%'  ORDER BY tanggal ASC";

	// 	$qry = $this->db->query($sql);


	// 	return $qry->result();
	// }

	function getDataAbsensi($pin, $tanggal){
		$sql = $this->db->get_where('tbl_absensi', array('tanggal' => $tanggal, 'pin' => $pin));
		$row = $sql->result();

		return $row;

	}

	function getDataAbsensiHarian($tanggal, $pin){
	    
		$sql = $this->db->get_where('tbl_absensi', array('tanggal' => $tanggal, 'pin' => $pin));
		$row = $sql->result();

		return $row;

	}





	function insertAbsensiCuti($tgl_cuti, $pin, $alasan)
	{

		$cekAbsenExist = $this->Presensi_model->cekAbsenExist($tgl_cuti, $pin);


		//echo $cekAbsenExist ;
		if ($cekAbsenExist == false) {
			$newArray = array(
				'pin' => $pin,
				'tanggal' => $tgl_cuti,
				'shift' => 'OFF',
				'jam_masuk' => '00:00:00',
				'jam_pulang' => '00:00:00',
				'masuk' => 'CUTI',
				'pulang' => 'CUTI',
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => $alasan
			);

			$this->db->insert('tbl_absensi', $newArray);
		} else {

			$newArray = array(
				'shift' => 'OFF',
				'jam_masuk' => '00:00:00',
				'jam_pulang' => '00:00:00',
				'masuk' => 'CUTI',
				'pulang' => 'CUTI',
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => $alasan
			);
			

			$this->db->where('pin', $pin);
			$this->db->where('tanggal', $tgl_cuti);
			$this->db->update('tbl_absensi', $newArray);
		}
		

		// echo $pin.' -- '.$tgl_cuti;

		//  print_array($newArray );
		//  exit;

		return true;
	}


	function cekAbsenShiftKerja($pin, $periode){
		$sql = "SELECT id FROM tbl_absensi WHERE pin = '$pin' AND tanggal like '$periode%' LIMIT 10";
		#echo $sql;
		$qry = $this->db->query($sql);
		$row = $qry->result();

		if(empty($row)){
			//belum ada sama sekali
			return false;
		}else{
			return true;
		}
	}

	function getJnsCuti($id_pegawai, $tgl){
		$sql = "SELECT * FROM ts_cuti WHERE id_pegawai = $id_pegawai AND '$tgl' between tgl_dari AND tgl_sampai";

		$qry = $this->db->query($sql);
		$row = $qry->result();
		if(!empty($row)){
			$jns_cuti = $row[0]->jns_cuti;
		}else{
			$jns_cuti = '-';
		}
		

		return $jns_cuti;

	}


	

	function cekAbsenExist($tanggal, $pin, $select='id'){
		$sql = $this->db->get_where('tbl_kehadiran_harian', array('tanggal' => $tanggal, 'pin' => $pin));
		$row = $sql->result();

		if(empty($row)){
			return 0;
		}else{
			if($select=='id'){
				$id = $row[0]->id;
				return $id;
			}else{

				$shift = $row[0]->shift;
				return $shift;
				
			}
		
		}
	}

	function updateAbsensiCancelCuti($id_cuti, $pin){
		//klo cuti di cancel sedangkan cuti sudah diapprove, maka data absen harus dikembalikan

		$list_hari = $this->Cuti_model->getListHariCuti($id_cuti);
		#print_array($list_hari);
		for ($c=0; $c < count($list_hari) ; $c++) {
		   $tgl_cuti = $list_hari[$c]->tanggal;
		   # $this->Presensi_model->insertAbsensiCuti($tgl_cuti, $pin);

		   $dataAbsensi  = $this->Presensi_model->getAbsenHarian($pin, $tgl_cuti);

#print_array($dataAbsensi);
		   if(!empty($dataAbsensi)){
				$datetime       = $dataAbsensi[0]->tanggal;
				$status_absen   = $dataAbsensi[0]->status;
				$id_absen       = $dataAbsensi[0]->id;

				$explode = explode(" ", $datetime);
				$tanggal = $explode[0];
				$jam     = $explode[1];

				if($status_absen == 0){

					$newArray = array(
						'tanggal' => $tanggal,
						'pin' => $pin,
						'masuk' => $jam,
						'telat' => 0,
						'keterangan' => ''
					);

				}else {
					$newArray = array(
						'tanggal' => $tanggal,
						'pin' => $pin,
						'pulang' => $jam,
						'p_awal' => 0,
						'keterangan' => ''
					);
				}


				$this->db->where('pin', $pin);
				$this->db->where('tanggal', $tanggal);
				$this->db->update('tbl_absensi', $newArray );

			}else{
				$newArray = array(
					'masuk' => '',
					'pulang' => '',
					'telat' => 300,
					'p_awal' => 150,
					'keterangan' => ''
				);

				$this->db->where('pin', $pin);
				$this->db->where('tanggal', $tgl_cuti);
				$this->db->update('tbl_absensi', $newArray );
			}
		}


		   return true;
	}

	function getAbsenHarian($pin, $tanggal){
		$sql = "SELECT * FROM ts_import_absensi WHERE tanggal  like '$tanggal%'  AND pin = '$pin'";
		$qry = $this->db->query($sql);
        $row = $qry->result();
		return $row;
	}

	function getAbsenBulanan($pin, $periode){
		$this->db->like('tanggal', $periode, 'after'); // 'after' artinya LIKE '$periode%'
		$this->db->where('pin', $pin);
		$query = $this->db->get('ts_import_absensi');
		$row = $query->result();

		return $row;
	}


	function getDatashiftKerja($id_pegawai, $tgl, $select = '*'){
		$sql = "SELECT $select FROM ts_shift_kerja WHERE tanggal = '$tgl' AND id_pegawai = '$id_pegawai'";
		$qry = $this->db->query($sql);
        $row = $qry->result();

		//echo $sql;

		//print_array($row_data);
		if($select=='shift'){
			if(!empty($row)){
				$data = $row[0]->shift;
				//print_array($row_data);
			}else{
				$data = '-';
			}
		}else if($select=='id'){
			if(!empty($row)){
				$data = $row[0]->id;
			}else{
				$data =  0;
			}
		}else{
			$data = $row;
		}


		return $data;
		//return $row;
	}


	function rekapAbsensi($id_pegawai, $periode, $totalTelat, $totalPawal, $numIzin, $numSakit, $numDLP, $numDLA, $numDLH){

		$dataRekap = array(
			'id_pegawai' => $id_pegawai,
			'periode' => $periode,
			'telat' => $totalTelat,
			'pulang_awal' => $totalPawal,
			'izin' => $numIzin,
			'sakit' => $numSakit,
			'alpha' => 0,
			'isoman' => 0,
			'dl_penuh' => $numDLP,
			'dl_awal' => $numDLA,
			'dl_akhir' => $numDLH,
			'status' => 0
		);


		$this->db->insert('ts_rekap_absensi', $dataRekap);
		return true;
	}



	function updateRekapAbsensi($id, $totalTelat, $totalPawal, $numIzin, $numSakit, $numDLP, $numDLA, $numDLH, $sakit_dgn_surat, $numCuti){

		$dataRekap = array(
			'telat' => $totalTelat,
			'pulang_awal' => $totalPawal,
			'izin' => $numIzin,
			'izin_half' => $izin_half, // izin setengah hari (awal atau akhir)
			'sakit' => $numSakit,
			'alpha' => $aplha,
			'isoman' => 0,
			'dl_penuh' => $numDLP,
			'dl_awal' => $numDLA,
			'dl_akhir' => $numDLH,
			'cuti' => $numCuti,
			'sakit_dgn_sk' => $sakit_dgn_surat,
		);


		$this->db->where('id', $id);
		$this->db->update('ts_rekap_absensi', $dataRekap);
		return true;
	}


    public function getAbsenMasuk($pin, $tanggal)
    {
        $tgl = date('Y-m-d', strtotime($tanggal));

        $sql = "SELECT tanggal FROM ts_absensi WHERE tanggal like '$tgl%' AND pin = '$pin' AND status = 0";
        $qry = $this->db->query($sql);
        $row = $qry->result();

        if (empty($row)) {
            $jam = '-';
        } else {
            $tgl = $row[0]->tanggal;
            $jam = date('H:i:s', strtotime($tgl));

            return $jam;
        }
    }






	public function getAbsenPulang($pin, $tanggal)
    {
        $tgl = date('Y-m-d', strtotime($tanggal));

        $sql = "SELECT tanggal FROM ts_absensi WHERE tanggal like '$tgl%' AND pin = '$pin' AND status = 1";
        $qry = $this->db->query($sql);
        $row = $qry->result();

        if (empty($row)) {
            $jam = '-';
        } else {
            $tgl = $row[0]->tanggal;
            $jam = date('H:i:s', strtotime($tgl));

            return $jam;
        }
    }



	public function insertJamAbsen($tgl, $jam_absen, $pin, $status)
    {

        if (strpos($jam_absen, ':') !== false) {
            $tanggal_jam_absen = $tgl . ' ' . $jam_absen;
        } else {

            if (strpos($jam_absen, '.') !== false) {
                $full = str_replace(".", ":", $jam_absen);
            } else {
                $hour = substr($jam_absen, 0, 2);

                $min = substr($jam_absen, 2, 2);
                $sec = substr($jam_absen, 4, 2);
                $full = $hour . ':' . $min . ':' . $sec;
            }



            $tanggal_jam_absen = $tgl . ' ' . $full;
        }


        $newData = array(
            'pin' => $pin,
            'tanggal' => $tanggal_jam_absen,
            'status' => $status,
			'status_update' => 0
        );


        $this->db->insert('ts_absensi', $newData);

        return true;
    }

	public function getListMesin()
    {
        $qry = $this->db->get('tbl_mesin_absensi');
        $row = $qry->result();
        return $row;
    }

	public function detailMesin($serial_number)
    {
        $this->db->where('serial_number', $serial_number);
        $qry = $this->db->get('tbl_mesin_absensi');
        $row = $qry->result();
        return $row;
    }



	function insertUpdateMesinAbsensi(){
		$nama_mesin  = $this->input->post('nama_mesin');
        $ip_addr     = $this->input->post('ip_address');
        $sn          = $this->input->post('sn');
        $action    = $this->input->post('action');


		if($action==''){
			$new_data = array(
				'nama_mesin' => $nama_mesin,
				'serial_number' => $sn,
				'id_puskesmas' =>  $this->input->post('id_puskesmas'),
				'ip_address' => $ip_addr
			);

			$this->db->insert('tbl_mesin_absensi', $new_data);
		}else{
			$new_data = array(
				'nama_mesin' => $nama_mesin,
				'serial_number' => $sn,
				'id_puskesmas' =>  $this->input->post('id_puskesmas'),
				'ip_address' => $ip_addr
			);

			$this->db->where('serial_number', $sn);
			$this->db->update('tbl_mesin_absensi', $new_data);

		}




		return true;
	}


	function createinitialShift2($pin, $tgl, $masuk='', $pulang='')
    {



			$libur = isWeekend($tgl);

			if ($libur == true){
				$shift = 'OFF';
				$jamMasuk = '00:00:00';
				$jamPulang = '00:00:00';
			}else{
				$day = date('D', strtotime($tgl));

				if($day == 'Fri'){
					$shift = 'REG-JUM';
					$jamMasuk = '07:30:00';
					$jamPulang = '16:30:00';
				}else{
					$shift = 'REG';
					$jamMasuk = '07:30:00';
					$jamPulang = '16:00:00';
				}

			}

            $newArray = array(
				'tanggal' => $tgl,
				'pin' => $pin,
				'shift' => $shift,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
				'masuk' => $masuk,
				'pulang' => $pulang,
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => ''
			);

            $this->db->insert('tbl_absensi', $newArray);
          
        return true;
    }

	


	function getLastAbsensi($pin)  {
		$sql = "SELECT * FROM `tbl_absensi` WHERE pin = '$pin' AND masuk != '' ORDER BY tanggal DESC limit 1 OFFSET 0";
		$qry = $this->db->query($sql);
		return $qry->result();

	}

    function createinitialShift($pin, $tgl, $masuk='', $pulang='')
    {

		$lastDate = date('t', strtotime($tgl));
      //  $lastDate = getLastDateMonth($tgl);
	    $newdate = $tgl;
        for($a=0; $a < $lastDate; $a++) {

			$libur = isWeekend($newdate);

			if ($libur == true){
				$shift = 'OFF';
				$jamMasuk = '00:00:00';
				$jamPulang = '00:00:00';
			}else{
				$day = date('D', strtotime($newdate));

				if($day == 'Fri'){
					$shift = 'REG-JUM';
					$jamMasuk = '07:30:00';
					$jamPulang = '16:30:00';
				}else{
					$shift = 'REG';
					$jamMasuk = '07:30:00';
					$jamPulang = '16:00:00';
				}

			}

            $newArray = array(
				'tanggal' => $newdate,
				'pin' => $pin,
				'shift' => $shift,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
				'masuk' => $masuk,
				'pulang' => $pulang,
				'telat' => 0,
				'p_awal' => 0,
				'keterangan' => ''
			);

            $this->db->insert('tbl_absensi', $newArray);
            $newdate = addDaysToDate($newdate, 1);

			print_array($newArray );


        }//close for



        return true;
    }


	function updateTblAbsensiShift($pin, $tgl)  {
		$lastDate = date('t', strtotime($tgl));
      //  $lastDate = getLastDateMonth($tgl);
	    $newdate = $tgl;
        for($a=0; $a < $lastDate; $a++) {

			$libur = isWeekend($newdate);

			if ($libur == true){
				$shift = 'OFF';
				$jamMasuk = '00:00:00';
				$jamPulang = '00:00:00';
			}else{
				$day = date('D', strtotime($newdate));

				if($day == 'Fri'){
					$shift = 'REG-JUM';
					$jamMasuk = '07:30:00';
					$jamPulang = '16:30:00';
				}else{
					$shift = 'REG';
					$jamMasuk = '07:30:00';
					$jamPulang = '16:00:00';
				}

			}

            $newArray = array(
				'shift' => $shift,
				'jam_masuk' => $jamMasuk,
				'jam_pulang' => $jamPulang,
				
			);

			$this->db->where('pin', $pin);
			$this->db->where('tanggal', $newdate);
            $this->db->update('tbl_absensi', $newArray);
            $newdate = addDaysToDate($newdate, 1);
        }//close for

        return true;
	}



	function updateStatusMesinAbsensi($ip_address,$statusMesin ){

        $this->db->where('ip_address', $ip_address);
        $this->db->set('status', $statusMesin);
        $this->db->update('tbl_mesin_absensi');
		return true;
	}
	

	function cekExistAbsensi( $pin, $tanggal){

		$qry = $this->db->get_where('ts_absensi', array('pin'=> $pin, 'tanggal'=> $tanggal));
		$row = $qry->num_rows();
		return $row;
	}

	function getjumlahCuti($id_pegawai, $periode){
		$sql = "SELECT SUM(jml_hari) as jumlah_cuti FROM `ts_rekap_cuti` WHERE id_pegawai = $id_pegawai AND periode = '$periode'";
		$qry = $this->db->query($sql);
		$row = $qry->result();

		return $row[0]->jumlah_cuti;
	}

	function cekDataRekapCuti($id_cuti){

		$qry = $this->db->get_where('ts_rekap_cuti', array('id_cuti'=> $id_cuti));
		$row = $qry->num_rows();
		return $row;
	}



	function cekLastImportData($serial_number)
	{
		$this->db->order_by('tanggal', 'DESC');
		$this->db->where('serial_number', $serial_number, 2, 0);
		$qry = $this->db->get('ts_absensi');
		$row = $qry->result();

		return $row;
	}

	function getPengajuanIzinSakitByValidator($id_validator, $periode)
	{
		// =====================
		// QUERY DATA
		// =====================
		$this->db->select('a.*, b.nama, b.nip');
		$this->db->from('pengajuan_izin_sakit a');
		$this->db->join('mst_pegawai b', 'a.id_pegawai = b.id_pegawai', 'left');
		$this->db->where('a.status', 0);
		$this->db->where('a.tanggal >=', $periode);
		$this->db->where('b.id_validator', $id_validator);
		$this->db->order_by('b.nama', 'ASC');

		$data = $this->db->get()->result();

		// =====================
		// QUERY COUNT
		// =====================
		$this->db->from('pengajuan_izin_sakit a');
		$this->db->join('mst_pegawai b', 'a.id_pegawai = b.id_pegawai', 'left');
		$this->db->where('a.status', 0);
		$this->db->where('a.tanggal >=', $periode);
		$this->db->where('b.id_validator', $id_validator);

		$count = $this->db->count_all_results();

		return [
			'data'  => $data,
			'count' => $count
		];
	}

	function getDataIzinSakit($id_pegawai, $jenis_absen = 'IZIN', $periode = '2026-01')
	{
		// Pecah tahun dan bulan
		$tahun = substr($periode, 0, 4);
		$bulan = substr($periode, 5, 2);

		// Tanggal awal & akhir bulan
		$tanggal_awal  = date('Y-m-01', strtotime($tahun . '-' . $bulan));
		$tanggal_akhir = date('Y-m-t', strtotime($tanggal_awal));

		$this->db->select('a.*, b.nama, b.nip');
		$this->db->from('pengajuan_izin_sakit a');
		$this->db->join('mst_pegawai b', 'a.id_pegawai = b.id_pegawai', 'left');
		$this->db->where('a.id_pegawai', $id_pegawai);

		// ⬇️ Tambahan logika ALL
		if ($jenis_absen !== 'ALL') {
			$this->db->where('a.jenis_absen', $jenis_absen);
		}

		$this->db->where('a.tanggal >=', $tanggal_awal);
		$this->db->where('a.tanggal <=', $tanggal_akhir);

		$this->db->order_by('b.nama', 'ASC');

		return $this->db->get()->result();
	}


	function generate_shift_kerja($pin, $tanggal){

		$absenMasuk   = $this->Presensi_model->getAbsenMasuk($pin, $tanggal);
		$absenPulang  = $this->Presensi_model->getAbsenPulang($pin, $tanggal);
        
		$shiftKerja = '-';
		if($absenMasuk =='' && $absenPulang==''){
			$shiftKerja = 'OFF';
		}

		
		if($absenMasuk !='' && $absenPulang==''){

			$xplod = explode(":", $absenMasuk);
			$jam = $xplod[0];
			if($jam < 9){
				$shiftKerja = 'PSM';
			}else if($jam > 12 && $jam < 15){
				$shiftKerja = 'SM';
			}else if($jam >= 15 && $jam < 18){
				$shiftKerja = 'SM-RUS';
			}else{
				$shiftKerja = 'M';
			}

		}


		if($absenMasuk !='' && $absenPulang !=''){

			$xplod = explode(":", $absenMasuk);
			$jam = $xplod[0];

			$xplod2 = explode(":", $absenPulang);
			$jam2 = $xplod2[0];

			if($jam < 9){
				//P atau PS
				if($jam2 < 17){
					//pagi
					$shiftKerja = 'P';
				}else{
					$shiftKerja = 'PS';
				}

			}else if($jam > 13 && $jam < 18){
				$shiftKerja = 'S';
			}else{
				$shiftKerja = 'M';
			}


		}


		    if($absenMasuk =='' && $absenPulang !=''){
				$shiftKerja = 'L-OFF';
			}
		

		return $shiftKerja;

	}


	function modelDataAbsensi($pin, $id_pegawai, $id, $jns_jam_kerja, $hari, $absenMasuk, $absenPulang, $jamMasukKerja, $jamKeluarKerja, $formatDate, $kodeShift){
		
		$tgl_now = date('Y-m-d');
		$keterangan_absen   = '';

		if($jns_jam_kerja == 'non_shift'){

			//khusus untuk yang jam kerjanya shift
			 if($hari != 'Sabtu' && $hari != 'Minggu'){

			
				if($absenMasuk=='' && $jamMasukKerja != '' && $formatDate < $tgl_now ){
					$absenMasuk = '<button class="btn-info-absensi btn btn-xs fs-6 btn-danger" value="'.$pin.'/'.$id_pegawai.'/'.$formatDate.'"
					 data-bs-toggle="modal" data-bs-target="#modal-info-absen">ALPHA</button>';
					$telat          = 300;
				}

				
				if($absenPulang=='' && $jamKeluarKerja != ''  && $formatDate < $tgl_now ){
					$absenPulang = '<button class="btn-info-absensi btn btn-xs fs-6 btn-danger" value="'.$pin.'/'.$id_pegawai.'/'.$formatDate.'"
					 data-bs-toggle="modal" data-bs-target="#modal-info-absen">ALPHA</button>';
					$p_awal         = 150;
				}


			}

				
			$hariLibur = $this->Presensi_model->cekHariLibur($formatDate);
			$hari_libur = false;
			if(!empty($hariLibur )){
					$kodeShift         = '';
					$jamMasukKerja     = '-';
					$jamKeluarKerja    = '-';
					$bg_btn = 'btn btn-xs badge-danger-lighten';
	
					$absenMasuk         = '<span class="btn btn-xs badge-danger-lighten fs-6">LIBUR NASIONAL</span>';
					$absenPulang        =  '<span class="btn btn-xs  badge-danger-lighten fs-6">LIBUR NASIONAL</span>';
					$keterangan_absen   = $hariLibur[0]->keterangan;

					
					$telat          = 0;
					$p_awal         = 0;
					$hari_libur = true;


			}
	  }else{ // shift2an
		  if($kodeShift=='P' || $kodeShift=='S' || $kodeShift=='PS'){

				
				if($absenMasuk==''){
					$absenMasuk = '<button class="btn-info-absensi btn btn-xs btn-danger fs-6" value="'.$pin.'/'.$id_pegawai.'/'.$formatDate.'"
					data-bs-toggle="modal" data-bs-target="#modal-info-absen">ALPHA</button>';
					$telat         = 300;
				}

			  if($absenPulang==''){
					$absenPulang = '<button class="btn-info-absensi btn btn-xs btn-danger fs-6" value="'.$pin.'/'.$id_pegawai.'/'.$formatDate.'"
					data-bs-toggle="modal" data-bs-target="#modal-info-absen">ALPHA</button>';
					$p_awal         = 150;
			  }
		   
		  }

		  if($kodeShift=='L-OFF'){
			
			if($absenPulang==''){
			  $absenPulang = '<button class="btn-info-absensi btn btn-xs btn-danger fs-6" value="'.$pin.'/'.$id_pegawai.'/'.$formatDate.'"
					data-bs-toggle="modal" data-bs-target="#modal-info-absen">ALPHA</button>';
			  $p_awal         = 150;
			}
		  }
			
		  if($kodeShift=='SM' ||$kodeShift=='M' ||$kodeShift=='PSM'){
			if($absenMasuk=='' && $jamMasukKerja != '' ){
			  $absenMasuk = '<button class="btn-info-absensi  btn btn-xs btn-danger fs-6" value="'.$pin.'/'.$id_pegawai.'/'.$formatDate.'"
					data-bs-toggle="modal" data-bs-target="#modal-info-absen">ALPHA</button>';
			  $telat          = 300;
			  $bg_btn = 'btn btn-xs btn-danger fs-6';
			}

		  }



	  }//close if juam kerja == shift


	  if($kodeShift=='OFF'){
		  $jamMasukKerja  = '';
		  $jamKeluarKerja  = '';
		  $bg_btn = 'btn btn-xs badge-danger-lighten';
		
	  }else if($kodeShift=='L-OFF'){
		
		  $bg_btn = 'btn btn-xs badge-warning-lighten';
		  $jamMasukKerja  = '-';
		 

	  }else if($kodeShift=='N/A'){
		
		$bg_btn = 'px-2.5 py-0.5 inline-flex items-center text-xs font-medium rounded border bg-slate-100 border-transparent text-slate-500 dark:bg-slate-500/20 dark:border-transparent';
		$jamMasukKerja  = '-';
	   

	}else{
		
	   
		$bg_btn = 'btn btn-xs badge-info-lighten';
	  }

	  if($jamKeluarKerja=='00:00:00'){
		$jamKeluarKerja  = '-';
	  }

	  if ($absenMasuk =='CUTI') {
		  $absenMasuk         = '<button class="info-cuti btn btn-xs btn-success fs-6 status" value="'.$pin.'/'.$id_pegawai.'/'.$id.'"
			 data-modal-target="infoAbsen">CUTI</button>';
		  $absenPulang        =  '<button class="info-cuti btn btn-xs btn-success fs-6 status" value="'.$pin.'/'.$id_pegawai.'/'.$id.'"
			 data-modal-target="infoAbsen">CUTI</button>';
		}

	   if ($absenMasuk =='DLP') {
		  $absenMasuk         = '<button class=" btn btn-xs badge-primary-lighten fs-6"  data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >DLP</button>';
		  $absenPulang        =  '<button class=" btn btn-xs badge-primary-lighten fs-6" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >DLP</button>';
		}

		if ($absenMasuk =='IZIN') {
		  $absenMasuk         = '<button class=" btn btn-xs badge-warning-lighten fs-6"  value="'.format_view($formatDate).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >IZIN</button>';
		
		}

		if ($absenPulang =='IZIN') {
		
			$absenPulang        =  '<button class="btn btn-xs badge-warning-lighten fs-6"  value="'.format_view($formatDate).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >IZIN</button>';
		  }

		if ($absenPulang =='DLAK') {
		 
			$absenPulang        =  '<button class="btn btn-xs badge-primary-lighten fs-6">DLAK</button>';
		}
		if ($absenMasuk =='DLA') {
		 
			$absenMasuk        =  '<button class="btn-info-absensi btn btn-xs badge-primary-lighten fs-6">DLA</button>';
		}

		
		if($absenMasuk =='SAKIT') {
			$absenMasuk         = '<button class="btn-info-absensi btn btn-xs badge-primary-lighten fs-6"  value="'.format_view($formatDate).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >SAKIT</button>';
			$absenPulang        =  '<button class="btn-info-absensi btn btn-xs badge-primary-lighten fs-6"  value="'.format_view($formatDate).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >SAKIT</button>';
		}


		return array($bg_btn, $absenMasuk, $absenPulang);

	}


	// function generate_shift_kerja($pin){

	// 	$periode_bulan = $this->session->userdata('periode_bulan');
	// 	$periode_tahun = $this->session->userdata('periode_tahun');

	// 	if($periode_bulan=='') {
	// 	  $bulan = date('m');
	// 	  $tahun = date('Y');

	// 	}else{
	// 	  $bulan = $periode_bulan;
	// 	  $tahun = $periode_tahun;
	// 	}


	// 	$periode = $tahun . '-' . $bulan;
    //     $periode = date('Y-m', strtotime($periode));


	// 	$lastDate = date('t', strtotime($periode)) + 1;

	// 	for ($t = 1; $t < $lastDate; $t++) {
	// 		$tanggal = $periode . '-' . $t;
	// 		$formatDate = date('Y-m-d', strtotime($tanggal));
	// 		$day = date('D', strtotime($tanggal));



	// 		$absenMasuk = $this->Presensi_model->getAbsenMasuk($pin, $tanggal);
	// 		$absenPulang = $this->Presensi_model->getAbsenPulang($pin, $tanggal);

	// 		if($absenMasuk =='' && $absenPulang==''){
	// 			$shiftKerja = 'OFF';
	// 		}

	// 		if($absenMasuk !='' && $absenPulang==''){

	// 			$xplod = explode(":", $absenMasuk);
	// 			$jam = $xplod[0];
	// 			if($jam < 9){
	// 				$shiftKerja = 'PSM';
	// 			}else if($jam > 12 && $jam < 15){
	// 				$shiftKerja = 'SM';
	// 			}else if($jam >= 15 && $jam < 18){
	// 				$shiftKerja = 'SM-RUS';
	// 			}else{
	// 				$shiftKerja = 'M';
	// 			}

	// 		}


	// 		if($absenMasuk !='' && $absenPulang !=''){

	// 			$xplod = explode(":", $absenMasuk);
	// 			$jam = $xplod[0];

	// 			$xplod2 = explode(":", $absenPulang);
	// 			$jam2 = $xplod2[0];

	// 			if($jam < 9){
	// 				//P atau PS
	// 				if($jam2 < 17){
	// 					//pagi
	// 					$shiftKerja = 'P';
	// 				}else{
	// 					$shiftKerja = 'PS';
	// 				}

	// 			}else if($jam > 13 && $jam < 18){
	// 				$shiftKerja = 'S';
	// 			}else{
	// 				$shiftKerja = 'M';
	// 			}


	// 		}

	// 		if($absenMasuk =='' && $absenPulang !=''){
	// 			$shiftKerja = 'L-OFF';
	// 		}

	// 		#$this->Presensi_model->deleteShiftPegawai($id_pegawai, $tanggal);
	// 		$insert = $this->Presensi_model->insertShiftPegawai($pin, $formatDate, $shiftKerja);
	// 	}

	// 	return true;
	// }







}
