<?php


class Api_cuti_model extends CI_Model
{

    public function get_all($params = [])
    {
        $this->db
            ->select('ts_pengajuan_cuti.*, mst_pegawai.nama')
            ->from('ts_pengajuan_cuti')
            ->join(
                'mst_pegawai',
                'mst_pegawai.id_pegawai = ts_pengajuan_cuti.id_pegawai',
                'left'
            );

        // filter jenis cuti
        if (!empty($params['jenis_cuti'])) {
            $this->db->where(
                'ts_pengajuan_cuti.jenis_cuti',
                $params['jenis_cuti']
            );
        }

        // filter status akhir
        if (!empty($params['status_akhir'])) {
            $this->db->where(
                'ts_pengajuan_cuti.status_akhir',
                $params['status_akhir']
            );
        }

        // filter bulan
        if (!empty($params['bulan'])) {

            $bulan = (int)$params['bulan'];
            $this->db->group_start();
            $this->db->where('MONTH(ts_pengajuan_cuti.tgl_mulai)', $bulan);
            $this->db->or_where('MONTH(ts_pengajuan_cuti.tgl_selesai)', $bulan);
            $this->db->group_end();
        }

        // filter tahun
        if (!empty($params['tahun'])) {

            $tahun = (int)$params['tahun'];

            $this->db->group_start();
            $this->db->where('YEAR(ts_pengajuan_cuti.tgl_mulai)', $tahun);
            $this->db->or_where('YEAR(ts_pengajuan_cuti.tgl_selesai)', $tahun);
            $this->db->group_end();
        }

        return $this->db
            ->order_by('ts_pengajuan_cuti.tgl_pengajuan', 'DESC')
            ->get()
            ->result();
    }

   public function get_by_pegawai_periode($id_pegawai, $bulan, $tahun)
    {
        // awal dan akhir bulan
        $tanggal_awal  = date('Y-m-01', strtotime($tahun . '-' . $bulan . '-01'));
        $tanggal_akhir = date('Y-m-t', strtotime($tanggal_awal));

        $this->db
            ->select('ts_pengajuan_cuti.*, mst_pegawai.nama')
            ->from('ts_pengajuan_cuti')
            ->join(
                'mst_pegawai',
                'mst_pegawai.id_pegawai = ts_pengajuan_cuti.id_pegawai',
                'left'
            )
            ->where('ts_pengajuan_cuti.id_pegawai', $id_pegawai);

        // cuti yang overlap dengan periode bulan tersebut
        $this->db->where('ts_pengajuan_cuti.tgl_mulai <=', $tanggal_akhir);
        $this->db->where('ts_pengajuan_cuti.tgl_selesai >=', $tanggal_awal);

        return $this->db
            ->order_by('ts_pengajuan_cuti.tgl_mulai', 'ASC')
            ->get()
            ->result();
    }

    public function update_cuti($id_cuti, $data)
    {
        $this->db->where('id', $id_cuti);
        return $this->db->update('ts_pengajuan_cuti', $data);
    }

    function updateHakCuti($id_pegawai, $tahun_hak_cuti, $hak_terpakai,  $hari_cuti, $sisa_hak_reserved){
        //pindahkan ke hak terpakai
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $tahun_hak_cuti);
        $this->db->set('hak_terpakai', $hak_terpakai + $hari_cuti);
        $this->db->set('hak_reserved', $sisa_hak_reserved);
        $this->db->update('ts_hak_cuti_pegawai');

        return true;
    }

    function updateLogMutasiCuti($id_pegawai, $id_cuti, $tahun_hak_cuti, $hari_cuti, $sisa_hak_reserved, $sisa_akhir){
         $this->db->insert('ts_log_mutasi_cuti', [
            'id_pengajuan_cuti' => $id_cuti,
            'id_pegawai' => $id_pegawai,
            'tahun' => $tahun_hak_cuti,
            'tipe' => 'final',
            'jumlah' => $hari_cuti,
            'saldo_sebelum' => $sisa_hak_reserved,
            'saldo_sesudah' => $sisa_akhir,
            'keterangan' => 'Pengajuan cuti disetujui, pemotongan hak cuti tahunan'
        ]);

        return true;
    }


    public function get_by_id($id)
    {
        return $this->db
            ->select('ts_pengajuan_cuti.*, mst_pegawai.nama, mst_pegawai.pin')
            ->from('ts_pengajuan_cuti')
            ->join(
                'mst_pegawai',
                'mst_pegawai.id_pegawai = ts_pengajuan_cuti.id_pegawai',
                'left'
            )
            ->where('ts_pengajuan_cuti.id', $id)
            ->get()
            ->row();
    }

    function get_data_approval($id_pengajuan)
    {
        return $this->db
            ->select('ts_pengajuan_cuti_approval.*, mst_pegawai.nama')
            ->from('ts_pengajuan_cuti_approval')
            ->join(
                'mst_pegawai',
                'mst_pegawai.id_pegawai = ts_pengajuan_cuti_approval.id_pegawai_approval',
                'left'
            )
            ->where('ts_pengajuan_cuti_approval.id_pengajuan_cuti', $id_pengajuan)
            ->get()
            ->result();
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

    
        function getListHariCuti($id_pengajuan_cuti){
            $qry = $this->db->get_where('ts_pengajuan_cuti_detail', ['id_pengajuan_cuti' => $id_pengajuan_cuti]);
            $list_hari = $qry->result();
            return $list_hari;
        }


    
}


?>