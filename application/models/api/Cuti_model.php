<?php


class Cuti_model extends CI_Model
{
    public function get_all()
    {
        return $this->db
            ->select('ts_pengajuan_cuti.*, mst_pegawai.nama')
            ->from('ts_pengajuan_cuti')
            ->join('mst_pegawai', 'mst_pegawai.id_pegawai = ts_pengajuan_cuti.id_pegawai', 'left')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->get_where('ts_pengajuan_cuti', ['id' => $id])
            ->row_array();
    }
}


?>