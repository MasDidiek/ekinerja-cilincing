<?php
class Pegawai_model extends CI_Model
{
    public function get_all()
    {
        return $this->db
            ->select('pegawai.*')
            ->from('pegawai')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->get_where('pegawai', ['id' => $id])
            ->row_array();
    }
}
?>  
           