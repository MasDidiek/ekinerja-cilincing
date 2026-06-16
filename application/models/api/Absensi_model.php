<?php


class Absensi_model extends CI_Model
{
    public function get_all()
    {
        return $this->db
            ->select('absensi.*, pegawai.nama')
            ->from('absensi')
            ->join('pegawai', 'pegawai.id = absensi.pegawai_id')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->get_where('absensi', ['id' => $id])
            ->row_array();
    }
}


?>