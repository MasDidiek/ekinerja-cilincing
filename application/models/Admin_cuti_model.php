<?php
defined("BASEPATH") or exit("No direct script access allowed");
class Admin_cuti_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }




    public function getDatalistCuti($start, $end, $id_validator = '')
    {
        return $this->db
            ->select('
                c.id,
                c.id_pegawai,
                mc.jenis_cuti AS nama_jenis_cuti,
                p.nama,
                c.jenis_cuti,
                c.lama_cuti,
                c.tgl_mulai,
                c.tgl_selesai,
                c.status_akhir,
                ap.role_approval,
                ap.id_pegawai_approval,
                c.alasan_cuti
            ')
            ->from('ts_pengajuan_cuti c')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->join('mst_cuti mc', 'mc.id = c.jenis_cuti')
            ->join(
                'ts_pengajuan_cuti_approval ap',
                'ap.id_pengajuan_cuti = c.id 
                AND ap.status = "pending"',
                'left'
            )
            ->where('c.tgl_mulai <=', $end)
            ->where('c.tgl_selesai >=', $start)
            ->get()
            ->result();
    }


    public function getCutiForCalendar($start, $end, $id_validator)
    {
        return $this->db
            ->select('
                c.id,
                c.id_pegawai,
                p.nama,
                c.jenis_cuti,
                c.tgl_mulai,
                c.tgl_selesai,
                c.status_akhir,
                ap.role_approval,
                ap.level_approval
            ')
            ->from('ts_pengajuan_cuti c')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->join(
                'ts_pengajuan_cuti_approval ap',
                'ap.id_pengajuan_cuti = c.id 
                AND ap.status = "pending"',
                'left'
            )
            ->where_in('c.status_akhir', ['proses', 'disetujui'])
            ->where('p.id_validator', $id_validator)
            ->where('c.tgl_mulai <=', $end)
            ->where('c.tgl_selesai >=', $start)
            ->get()
            ->result();
    }

    public function getCutiForCalendarByPuskesmas($start, $end, $id_puskesmas)
    {
        return $this->db
            ->select('
                c.id,
                c.id_pegawai,
                p.nama,
                c.jenis_cuti,
                c.tgl_mulai,
                c.tgl_selesai,
                c.status_akhir,
                ap.role_approval,
                ap.level_approval
            ')
            ->from('ts_pengajuan_cuti c')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->join(
                'ts_pengajuan_cuti_approval ap',
                'ap.id_pengajuan_cuti = c.id 
                AND ap.status = "pending"',
                'left'
            )
            ->where_in('c.status_akhir', ['proses', 'disetujui'])
            ->where('p.id_puskesmas ', $id_puskesmas)
            ->where('c.tgl_mulai <=', $end)
            ->where('c.tgl_selesai >=', $start)
            ->get()
            ->result();
    }


    public function getCutiByTanggal($tanggal, $id_puskesmas)
    {
        return $this->db
            ->select('
                c.id,
                p.nama,
                mc.jenis_cuti,
                c.tgl_mulai,
                c.tgl_selesai,
                c.lama_cuti,
                c.status_akhir,
                c.alasan_cuti,
                ap.role_approval,
                ap.level_approval
            ')
            ->from('ts_pengajuan_cuti c')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->join('mst_cuti mc', 'mc.id = c.jenis_cuti')
            ->join(
                'ts_pengajuan_cuti_approval ap',
                'ap.id_pengajuan_cuti = c.id 
                AND ap.status = "pending"',
                'left'
            )
            ->where_in('c.status_akhir', ['proses', 'disetujui'])
            ->where('p.id_puskesmas', $id_puskesmas)
            ->where('c.tgl_mulai <=', $tanggal)
            ->where('c.tgl_selesai >=', $tanggal)
            ->order_by('p.nama', 'ASC')
            ->get()
            ->result();
    }


    function getNumCutiPending($id_validator = 0)
    {

        $approval = $this->db
            ->select('id')
            ->from('ts_pengajuan_cuti_approval')
            ->where('id_pegawai_approval', $id_validator)
            ->where('status', 'pending')
            ->order_by('level_approval', 'ASC')
            ->get()
            ->num_rows();

        return $approval;
    }


    // function getDataCutiPegawaiPending($id_validator = 0)
    // {

    //         $approval = $this->db
    //             ->select('*')
    //             ->from('ts_pengajuan_cuti_approval')
    //             ->where('id_pegawai_approval', $id_validator)
    //             ->where('status', 'pending')
    //             ->order_by('level_approval','ASC')
    //             ->get()
    //             ->result_array();

    //         return $approval;

    // }

    function getLogCutiPegawai($id_pegawai, $tahun = 2025)
    {

        return $this->db
            ->select(' 
                b.*,
                a.jns_cuti,
                a.jns_hak_cuti,
                a.tgl_dari,
                a.tgl_sampai, 
                a.alasan_cuti,
                a.status,
                p.nama
            ')
            ->from('ts_cuti a')
            ->join('ts_log_cuti b', 'a.id = b.id_cuti', 'left')
            ->join('mst_pegawai p', 'p.id_pegawai = a.id_pegawai')
            ->where('a.id_pegawai', $id_pegawai)
            ->order_by('a.tgl_dari', 'ASC')
            ->get()
            ->result();
    }

    function getCutiBersalin($id_pegawai)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->where('id_pegawai', $id_pegawai);
        $qry = $this->db->get('ts_pengajuan_cuti', 1, 0);
        $row = $qry->row();

        return $row;
    }


    public function getPengajuanCutiPegawaiAll()
    {

        $status     = $this->input->get('status');
        $tgl_mulai  = $this->input->get('tgl_mulai');
        $tgl_akhir  = $this->input->get('tgl_akhir');

        $tgl_mulai = format_db($tgl_mulai);
        $tgl_akhir = format_db($tgl_akhir);

        $this->db->select('
            a.*,
            p.nama,
            mc.jenis_cuti
        ');
        $this->db->from('ts_pengajuan_cuti a');
        $this->db->join('mst_pegawai p', 'p.id_pegawai = a.id_pegawai');
        $this->db->join('mst_cuti mc', 'mc.id = a.jenis_cuti');

        /* FILTER STATUS */
        if (!empty($status) && $status !== 'semua') {
            $this->db->where('a.status_akhir', $status);
        }

        /* FILTER TANGGAL */
        if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
            $this->db->where('a.tgl_mulai >=', $tgl_mulai);
            $this->db->where('a.tgl_selesai <=', $tgl_akhir);
        }

        /* ORDER */
        $this->db->order_by('a.created_at', 'ASC');
        //         $query = $this->db->get();

        //   echo $this->db->last_query();
        //   exit;

        return $this->db->get()->result();
    }

    public function get_cuti_bulanan_absensi($id_pegawai, $bulan, $tahun)
    {
        $bulan  = str_pad($bulan, 2, '0', STR_PAD_LEFT);

        $tgl_awal  = $tahun . '-' . $bulan . '-01';
        $tgl_akhir = date('Y-m-t', strtotime($tgl_awal)); // otomatis last day of month

        $this->db->select('
            c.id,
            c.tgl_mulai,
            c.tgl_selesai,
            c.status_akhir,
            c.lama_cuti,
            mc.jenis_cuti AS nama_jenis_cuti
        ');

        $this->db->from('ts_pengajuan_cuti c');

        // join jenis cuti
        $this->db->join('mst_cuti mc', 'mc.id = c.jenis_cuti', 'left');

        $this->db->where('c.id_pegawai', $id_pegawai);

        // logika overlap periode bulan
        $this->db->where('c.tgl_mulai <=', $tgl_akhir);
        $this->db->where('c.tgl_selesai >=', $tgl_awal);

        // jangan ambil yang dibatalkan
        $this->db->where('c.status_akhir !=', 'dibatalkan');

        return $this->db->get()->result();
    }



    public function get_cuti_by_pegawai($id_pegawai, $bulan, $tahun = 2026)
    {
        $tgl_awal  = $tahun . '-' . $bulan . '-01';
        $tgl_akhir = $tahun . '-' . $bulan . '-31';

        $this->db->select("
            c.id,
            c.tgl_mulai,
            c.tgl_selesai,
            c.lama_cuti,
            c.status_akhir,
            mc.jenis_cuti AS nama_jenis_cuti,
            a.role_approval,
            CASE
                WHEN c.status_akhir = 'proses'
                    THEN CONCAT('Proses - Menunggu ACC ', UPPER(a.role_approval))
                WHEN c.status_akhir = 'disetujui'
                    THEN 'Selesai - Disetujui'
                WHEN c.status_akhir = 'ditolak'
                    THEN 'Selesai - Ditolak'
                WHEN c.status_akhir = 'ditangguhkan'
                    THEN 'Selesai - Ditangguhkan'
                WHEN c.status_akhir = 'dibatalkan'
                    THEN 'Selesai - Dibatalkan'
                ELSE c.status_akhir
            END AS status_tampilan
        ");
        $this->db->from('ts_pengajuan_cuti c');

        // jenis cuti
        $this->db->join('mst_cuti mc', 'mc.id = c.jenis_cuti', 'left');

        // approval pending level terendah
        $this->db->join(
            '(SELECT x.*
              FROM ts_pengajuan_cuti_approval x
              WHERE x.status = "pending"
              ORDER BY x.level_approval ASC
            ) a',
            'a.id_pengajuan_cuti = c.id',
            'left'
        );

        $this->db->where('c.id_pegawai', $id_pegawai);
        $this->db->where('c.tgl_mulai <=', $tgl_akhir);
        $this->db->where('c.tgl_selesai >=', $tgl_awal);

        return $this->db->get()->result();
    }
    public function getPengajuanCutiPegawai($id_pegawai, $role_approval = 'kapustu')
    {
        return $this->db
            ->select('
                 c.tgl_pengajuan,
                c.lama_cuti,
                c.alamat_cuti,
                c.delegasi_tugas,
                c.id,
                c.id_pengganti,
                c.id_pegawai,
                p.nama,
                mc.jenis_cuti,
                c.tgl_mulai,
                c.tgl_selesai,
                c.alasan_cuti,
                c.created_at,
                a.status as status_approval
            ')
            ->from('ts_pengajuan_cuti_approval a')
            ->join('ts_pengajuan_cuti c', 'c.id = a.id_pengajuan_cuti')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->join('mst_cuti mc', 'mc.id = c.jenis_cuti')
            ->where('a.id_pegawai_approval', $id_pegawai)
            ->where('a.role_approval', $role_approval)
            ->where('a.status', 'pending')
            ->order_by('c.created_at', 'ASC')
            ->get()
            ->result();


        //   echo $this->db->last_query();
    }

    function updateNextRoleApproval($id_pengajuan, $next_approval)
    {
        $this->db->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('role_approval', $next_approval)
            ->update('ts_pengajuan_cuti_approval', [
                'status' => 'pending'
            ]);
        return true;
    }

    public function getSisaCutiTahunan($id_pegawai, $tahun)
    {
        $row = $this->db
            ->where('id_pegawai', $id_pegawai)
            ->where('tahun', $tahun)
            ->get('ts_hak_cuti_pegawai')
            ->row();

        if (!$row) return 0;

        return $row->hak_total - ($row->hak_terpakai + $row->hak_reserved);
    }

    public function get_rekap_cuti_pegawai_by_id($id_pegawai, $tahunList = [2025, 2026])
    {
        $data = [];

     

        foreach ($tahunList as $th) {

            $row = $this->db
                ->where('id_pegawai', $id_pegawai)
                ->where('tahun', $th)
                ->get('ts_hak_cuti_pegawai')
                ->row();

            if (!$row) {
                $data[$th] = [
                    'hak'      => 0,
                    'terpakai' => 0,
                    'reserved' => 0,
                    'sisa'     => 0
                ];
                continue;
            }

            $hak_total   = (int) $row->hak_total;
            $terpakai    = (int) $row->hak_terpakai;
            $reserved    = (int) $row->hak_reserved;
            $sisa        = $hak_total - ($terpakai + $reserved);

            $data[$th] = [
                'hak'      => $hak_total,
                'terpakai' => $terpakai,
                'reserved' => $reserved,
                'sisa'     => $sisa
            ];
        }

        return $data;
    }


    public function releaseHakCuti($id_pengajuan, $id_pegawai)
    {
        $logs = $this->db
            ->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('tipe', 'reserve')
            ->get('ts_log_mutasi_cuti')
            ->result();

        foreach ($logs as $log) {

            // Ambil saldo terakhir
            $row = $this->db
                ->where('id_pegawai', $id_pegawai)
                ->where('tahun', $log->tahun)
                ->get('ts_hak_cuti_pegawai')
                ->row();

            $sebelum = $row->hak_total - ($row->hak_terpakai + $row->hak_reserved);
            $sesudah = $sebelum + $log->jumlah;

            // Log release
            $this->db->insert('ts_log_mutasi_cuti', [
                'id_pegawai' => $id_pegawai,
                'tahun' => $log->tahun,
                'id_pengajuan_cuti' => $id_pengajuan,
                'tipe' => 'release',
                'jumlah' => $log->jumlah,
                'saldo_sebelum' => $sebelum,
                'saldo_sesudah' => $sesudah,
                'keterangan' => 'Pengajuan ditolak oleh pengganti'
            ]);

            // Update hak_reserved
            $this->db->where('id', $row->id)
                ->update('ts_hak_cuti_pegawai', [
                    'hak_reserved' => $row->hak_reserved - $log->jumlah
                ]);
        }
    }



    public function get_rekap_cuti_pegawai($tahunList = [2025, 2026])
    {
        // Ambil semua pegawai
        $pegawai = $this->db->get('mst_pegawai')->result_array();

        $result = [];

        foreach ($pegawai as $p) {

            $row = [
                'id_pegawai' => $p['id_pegawai'],
                'nama'       => $p['nama'],
                'jabatan'    => $p['jabatan'],
                'cuti'       => []
            ];

            foreach ($tahunList as $th) {

                // 1. Ambil hak cuti
                $hak = $this->db->select('hak_cuti')
                    ->where('id_pegawai', $p['id_pegawai'])
                    ->where('tahun', $th)
                    ->get('ts_master_cuti')
                    ->row();

                $hak_cuti = $hak ? (int)$hak->hak_cuti : 0;

                // 2. Ambil mutasi
                $mutasi = $this->db->select("
                            SUM(CASE WHEN tipe = 'USED' THEN ABS(jumlah) ELSE 0 END) as used,
                            SUM(CASE WHEN tipe = 'HOLD' THEN ABS(jumlah) ELSE 0 END) as hold
                        ")
                    ->where('id_pegawai', $p['id_pegawai'])
                    ->where('tahun', $th)
                    ->get('ts_log_mutasi_cuti')
                    ->row();

                $used = (int) $mutasi->used;
                $hold = (int) $mutasi->hold;

                $sisa = $hak_cuti - ($used + $hold);

                $row['cuti'][$th] = [
                    'hak'      => $hak_cuti,
                    'terpakai' => $used,
                    'hold'     => $hold,
                    'sisa'     => $sisa
                ];
            }

            $result[] = $row;
        }

        return $result;
    }


    function getApprovalCuti($id_pengajuan)
    {
        $approval = $this->db
            ->select('a.*, p.nama')
            ->from('ts_pengajuan_cuti_approval a')
            ->join('mst_pegawai p', 'p.id_pegawai = a.id_pegawai_approval', 'left')
            ->where('a.id_pengajuan_cuti', $id_pengajuan)
            ->order_by('a.level_approval', 'ASC')
            ->get()
            ->result_array();

        return $approval;
    }

    function getPermohonanPengganti($id_pegawai)
    {
        return $this->db
            ->select('
                c.id,
                c.id_pegawai,
                p.nama,
                c.jenis_cuti,
                c.tgl_mulai,
                c.tgl_selesai,
                c.alasan_cuti,
                c.created_at,
                a.status as status_approval
            ')
            ->from('ts_pengajuan_cuti_approval a')
            ->join('ts_pengajuan_cuti c', 'c.id = a.id_pengajuan_cuti')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->where('a.id_pegawai_approval', $id_pegawai)
            ->where('a.role_approval', 'pengganti')
            ->where('a.status', 'pending')
            ->order_by('c.created_at', 'ASC')
            ->get()
            ->result();
    }


    function getlistHariCuti($id_cuti)
    {
        $qry = $this->db->get_where('ts_pengajuan_cuti_detail', ['id_pengajuan_cuti' => $id_cuti]);
        return $qry->result();
    }


    function get_last_status_approval_cuti($id_cuti)
    {
        $qry = $this->db->get_where('ts_pengajuan_cuti_approval', ['id_pengajuan_cuti' => $id_cuti, 'status' => 'pending']);
        $row =  $qry->row();
        if (!empty($row)) {
            $role_approval = $row->role_approval;
        } else {
            $role_approval = '-';
        }
        return $role_approval;
    }


    function getDetailPengajuanCuti($id_pengajuan, $role_approval = 'pengganti')
    {
        return $this->db
            ->select('
       
                c.*,
                p.nama,
                mc.jenis_cuti,
                a.status as status_approval,
                a.id_pegawai_approval,
                a.role_approval
            ')
            ->from('ts_pengajuan_cuti_approval a')
            ->join('ts_pengajuan_cuti c', 'c.id = a.id_pengajuan_cuti')
            ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
            ->join('mst_cuti mc', 'mc.id = c.jenis_cuti')
            ->where('a.id_pengajuan_cuti', $id_pengajuan)
            ->order_by('c.created_at', 'ASC')
            ->get()
            ->row();

        //  ->from('ts_pengajuan_cuti_approval a')
        // ->join('ts_pengajuan_cuti c', 'c.id = a.id_pengajuan_cuti')
        // ->join('mst_pegawai p', 'p.id_pegawai = c.id_pegawai')
        // ->join('mst_cuti mc', 'mc.id = c.jenis_cuti')
        // ->where('a.id_pengajuan_cuti', $id_pengajuan)
        // ->where('a.role_approval', $role_approval)
        // ->order_by('c.created_at', 'ASC')
        // ->get()
        // ->row();
    }



    function getListTanggalCuti($tgl_cuti_dari, $tgl_cuti_sampai, $jns_cuti, $jamKerja, $hariLibur)
    {
        $listTanggal = [];

        $start = new DateTime($tgl_cuti_dari);
        $end   = new DateTime($tgl_cuti_sampai);
        $end->modify('+1 day'); // supaya tanggal akhir ikut

        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        foreach ($period as $dt) {

            $tgl = $dt->format('Y-m-d');
            $hari = $dt->format('N'); // 6 = sabtu, 7 = minggu

            // default: hari dihitung
            $hitung = true;

            // jika BUKAN bersalin
            if ($jns_cuti != 2) {

                // jika pegawai reguler
                if ($jamKerja == 'non_shift') {

                    // skip sabtu & minggu
                    if ($hari >= 6) {
                        $hitung = false;
                    }

                    // skip hari libur nasional
                    if (in_array($tgl, $hariLibur)) {
                        $hitung = false;
                    }
                }
            }

            if ($hitung) {
                $listTanggal[] = $tgl;
            }
        }

        return  $listTanggal;
    }


    public function approveKapusInduk($id_pengajuan)
    {

        // Ambil approval Kapus Induk yang masih pending
        $approval = $this->db
            ->where('id_pengajuan_cuti', $id_pengajuan)
            ->where('role_approval', 'kapus')
            ->where_in('status', ['pending', 'approved'])
            ->get('ts_pengajuan_cuti_approval')
            ->row();



        if (!$approval) {
            return false; // atau bisa lempar error
        }

        // Ambil data cuti
        $cuti = $this->db
            ->where('id', $id_pengajuan)
            ->get('ts_pengajuan_cuti')
            ->row();

        $id_pegawai_cuti = $cuti->id_pegawai;
        $hak_tahun      = $cuti->tahun_hak_cuti;
        $lama_cuti      = $cuti->lama_cuti;

        $this->db->trans_start();

        // 1. Approve Kapus Induk
        $this->db->where('id', $approval->id)
            ->update('ts_pengajuan_cuti_approval', [
                'status' => 'approved',
                'approved_at' => date('Y-m-d H:i:s')
            ]);

        // 2. Update status pengajuan
        $this->db->where('id', $id_pengajuan)
            ->update('ts_pengajuan_cuti', [
                'status_akhir' => 'disetujui',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // 3. Ambil saldo sebelum
        $rowHak = $this->db
            ->where('id_pegawai', $id_pegawai_cuti)
            ->where('tahun', $hak_tahun)
            ->get('ts_hak_cuti_pegawai')
            ->row();

        $saldo_sebelum = $rowHak->hak_total - ($rowHak->hak_terpakai + $rowHak->hak_reserved);

        // 4. Pindahkan reserve → terpakai
        // $this->db->set('hak_reserved', 'hak_reserved - ' . $lama_cuti, false);
        // $this->db->set('hak_terpakai', 'hak_terpakai + ' . $lama_cuti, false);
        // $this->db->where('id_pegawai', $id_pegawai_cuti);
        // $this->db->where('tahun', $hak_tahun);
        // $this->db->update('ts_hak_cuti_pegawai');

        // 5. Hitung saldo sesudah
        $saldo_sesudah = $saldo_sebelum - $lama_cuti;

        // 6. Insert log FINAL (JANGAN UPDATE LOG LAMA)
        $this->db->insert('ts_log_mutasi_cuti', [
            'id_pegawai' => $id_pegawai_cuti,
            'tahun' => $hak_tahun,
            'id_pengajuan_cuti' => $id_pengajuan,
            'tipe' => 'final',
            'jumlah' => -$lama_cuti,
            'saldo_sebelum' => $saldo_sebelum,
            'saldo_sesudah' => $saldo_sesudah,
            'keterangan' => 'Final approval Kapus Induk',
            'created_by' => $approval->id_pegawai_approval
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false; // atau bisa lempar error
        }

        return true;
    }




    function getHariLibur($skipHariLibur, $tgl_cuti_dari, $tgl_cuti_sampai)
    {
        $hariLibur = [];

        if ($skipHariLibur) {
            $libur = $this->db
                ->select('tgl')
                ->from('ts_hari_libur')
                ->where('tgl >=', $tgl_cuti_dari)
                ->where('tgl <=', $tgl_cuti_sampai)
                ->get()
                ->result_array();

            $hariLibur = array_column($libur, 'tgl');
        }

        return $hariLibur;
    }


    public function getApproverCuti($id_pegawai, $id_pengganti)
    {

        $unit_kerja_pegawai =  $this->db->select('id_puskesmas, klaster')
            ->from('mst_pegawai')
            ->where('id_pegawai', $id_pegawai)
            ->get()
            ->row();

        $id_puskesmas = $unit_kerja_pegawai->id_puskesmas;
        $klaster = $unit_kerja_pegawai->klaster; //(1,2,3,4, 5)



        // kapustu
        if ($id_puskesmas == 1) {
            //klo pegawai pkc, maka cek dia masuk cluster berapa
            $pegawai = $this->db
                ->select('id_pegawai')
                ->from('mst_validator')
                ->where('id_puskesmas', $id_puskesmas)
                ->where('klaster', $klaster)
                ->get()
                ->row();
        } else {
            $pegawai = $this->db
                ->select('id_pegawai')
                ->from('mst_validator')
                ->where('id_puskesmas', $id_puskesmas)
                ->get()
                ->row();
        }


        // ktu
        $ktu = $this->db
            ->select('id_pegawai')
            ->from('mst_pegawai')
            ->where('usergroup', 2)
            ->limit(1)
            ->get()
            ->row();

        // kapus
        $kapus = $this->db
            ->select('id_pegawai')
            ->from('mst_pegawai')
            ->where('usergroup', 1)
            ->limit(1)
            ->get()
            ->row();

        return [
            'pengganti' => $id_pengganti,
            'kapustu'   => $pegawai->id_pegawai,
            'ktu'       => $ktu->id_pegawai,
            'kapus'     => $kapus->id_pegawai
        ];
    }


    function hitungHariInklusif($start, $end, $format = 'Y-m-d')
    {
        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);

        // supaya tanggal akhir ikut dihitung
        $endPlus = (clone $endDate)->modify('+1 day');

        $period = new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endPlus
        );

        $listTgl = [];
        foreach ($period as $date) {
            $listTgl[] = $date->format($format);
        }

        $jumlah = count($listTgl);

        return array($jumlah, $listTgl);
    }


    public function hitungHariKerja($start, $end, $jenis_jam_kerja)
    {
        $jumlah = 0;

        // $start = new DateTime($start);
        // $end = new DateTime($end);

        $interval  = new DateInterval("P1D");
        $daterange = new DatePeriod($start, $interval, $end->modify("+1 day"));

        $arrayTglCuti = array();

        foreach ($daterange as $date) {
            $tgl = $date->format("Y-m-d");
            $dayOfWeek = (int) $date->format("N");

            //N = Non Shift (reguler)
            if ($jenis_jam_kerja == "N") {
                if ($dayOfWeek >= 6) {
                    continue;
                } // Sabtu/Minggu
                if ($this->isLibur($tgl)) {
                    continue;
                } // Hari libur nasional

                array_push($arrayTglCuti, $tgl);
                $jumlah++;
            } elseif ($jenis_jam_kerja === "S") {
                $jumlah++;
                array_push($arrayTglCuti, $tgl);
            }
        }


        //print_array($arrayTglCuti);
        //  exit;
        $this->session->set_userdata('list_tgl_cuti', $arrayTglCuti);
        return $jumlah;
    }


    public function isLibur($tanggal)
    {
        $this->db->where("tgl", $tanggal);
        $query = $this->db->get("ts_hari_libur");
        return $query->num_rows() > 0;
    }


    function getInfoPenggantiCuti($id_pengganti)
    {


        $this->db->where('mst_pegawai.id_pegawai', $id_pengganti);
        $this->db->select('mst_pegawai.nama, mst_pegawai.nip, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
        $this->db->from('mst_pegawai');
        $this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
        $this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');

        $qry = $this->db->get()->row();

        return $qry;
    }


    function getDokumentCuti($id_cuti)
    {
        $qry = $this->db->get_where('ts_cuti_bukti', ['id_pengajuan' => $id_cuti]);
        return $qry->result();
    }

    function cekCutiPerhari($id_pegawai, $tanggal){
        $this->db->from('ts_pengajuan_cuti');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tgl_mulai <=', $tanggal);
        $this->db->where('tgl_selesai >=', $tanggal);

        $cuti = $this->db->get()->row();

        if ($cuti) {
            return true;
        } else {
            return false;
        }
    }
}
