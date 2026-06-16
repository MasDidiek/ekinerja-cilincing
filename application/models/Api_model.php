<?php ob_start();
defined('BASEPATH') or exit('No direct script access allowed');
class Api_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		//$this->db = $this->load->database('ekin', true);

	}



        function getDetailCuti($id){
            $this->db->select('ts_pengajuan_cuti.*, mst_pegawai.nama, mst_pegawai.pin');
            $this->db->from('ts_pengajuan_cuti');
            $this->db->join('mst_pegawai', 'mst_pegawai.id_pegawai = ts_pengajuan_cuti.id_pegawai', 'left');


            $this->db->where('ts_pengajuan_cuti.id', $id);
            $this->db->order_by('ts_pengajuan_cuti.tgl_pengajuan', 'DESC');

            $qry = $this->db->get();
            $row = $qry->row();
            return $row;

        }

        function getApproval($id_pengajuan){
            $qry = $this->db->get_where('ts_pengajuan_cuti_approval', ['id_pengajuan_cuti' =>$id_pengajuan]);
            $approval = $qry->result();

            return $approval;
        }

        function getHakCutiPegawai($id_pegawai){
            $qry = $this->db->get_where('ts_hak_cuti_pegawai', ['id_pegawai'=> $id_pegawai]);
            $hak_cuti = $qry->result();
            return $hak_cuti;
        }

         function getRiwayatCutiPegawai($id_pegawai){
            $this->db->order_by('id', 'DESC');
            $qry = $this->db->get_where('ts_pengajuan_cuti', ['id_pegawai'=> $id_pegawai]);
            $cuti = $qry->result();
            return $cuti;
        }


    function getRekapAbsensiPerbulan($jns_pegawai, $periode){

       
            $this->db->from('ts_rekap_absensi');      
            $qry = $this->db->get();
            $rekap = $qry->result();
            return $rekap;
    }
        // function getRekapAbsensiPerbulan($jns_pegawai, $periode){
        //     $this->db->order_by('ts_rekap_absensi.telat', 'DESC');
        //     $this->db->select('mst_pegawai.nama, mst_pegawai.pin, mst_pegawai.id_pegawai, ts_rekap_absensi.*');
        //     $this->db->from('ts_rekap_absensi');
        //     $this->db->join('mst_pegawai', 'mst_pegawai.id_pegawai = ts_rekap_absensi.id_pegawai', 'left');
        //     $this->db->where("periode", $periode);
        //     $this->db->where("mst_pegawai.jns_pegawai", $jns_pegawai);
        //     $qry = $this->db->get();
        //     $rekap = $qry->result();
        //     return $rekap;

        

        // }


        
        public function getLogAbsensi($pin, $periode)
        {
            // contoh format periode: 2026-04

            // tanggal awal bulan
            $tanggal_awal = date('Y-m-01', strtotime($periode));

            // tanggal akhir bulan
            $tanggal_akhir = date('Y-m-t', strtotime($periode));

            return $this->db
                ->where('pin', $pin)
                ->where('tanggal >=', $tanggal_awal)
                ->where('tanggal <=', $tanggal_akhir)
                ->order_by('tanggal', 'ASC')
                ->get('tbl_log_absensi')
                ->result();
        }

        
        function getListHariCuti($id_pengajuan_cuti){
            $qry = $this->db->get_where('ts_pengajuan_cuti_detail', ['id_pengajuan_cuti' => $id_pengajuan_cuti]);
            $list_hari = $qry->result();
            return $list_hari;
        }

        function getInfoPegawaiByPin($pin, $jns_pegawai = 'non_pns'){
             $this->db->select('
                mst_pegawai.*,
                mst_puskesmas.nama AS puskesmas,
                mst_jabatan.nama AS jabatan
            ');

            $this->db->from('mst_pegawai');
            $this->db->join(
                'mst_puskesmas',
                'mst_puskesmas.id_puskesmas = mst_pegawai.id_puskesmas',
                'left'
            );
            $this->db->join(
                'mst_jabatan',
                'mst_jabatan.id = mst_pegawai.id_jabatan',
                'left'
            );

            $this->db->where('mst_pegawai.pin', $pin);
            $this->db->where('mst_pegawai.jns_pegawai', $jns_pegawai);
            $qry = $this->db->get();

            $row = $qry->row();

            return $row;
        }



        public function sync_kehadiran_harian($logs)
        {
            // STEP 1: group by pin + tanggal
            $grouped = [];

            //print_array($logs); // cek data log absensi yang diterima
           
            foreach ($logs as $log) {

                $key = $log->pin . '_' . $log->tanggal;

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'pin' => $log->pin,
                        'tanggal' => $log->tanggal,
                        'jam_list' => []
                    ];
                }

                $grouped[$key]['jam_list'][] = $log->jam;
            }

            // STEP 2: proses tiap hari
            foreach ($grouped as $data) {

                // sort jam
                sort($data['jam_list']);

                $jam_masuk  = $data['jam_list'][0]; // paling awal
                $jam_pulang = end($data['jam_list']); // paling akhir

                $pin     = $data['pin'];
                $tanggal = $data['tanggal'];

                // STEP 3: cek existing
                $this->db->where('pin', $pin);
                $this->db->where('tanggal', $tanggal);
                $exist = $this->db->get('tbl_kehadiran_harian')->row();

                $payload = [
                    'jam_masuk'  => $jam_masuk,
                    'jam_pulang' => $jam_pulang
                ];

                if ($exist) {

                    // UPDATE
                    $this->db->where('pin', $pin);
                    $this->db->where('tanggal', $tanggal);
                    $this->db->update('tbl_kehadiran_harian', $payload);

                } else {

                    // INSERT
                    $payload['pin'] = $pin;
                    $payload['tanggal'] = $tanggal;

                    $this->db->insert('tbl_kehadiran_harian', $payload);
                }
            }

            return true;
        }

}
