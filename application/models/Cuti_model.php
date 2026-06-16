<?php
defined("BASEPATH") or exit("No direct script access allowed");
class Cuti_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    public function updateStatusCuti($id, $status)
    {
        if($status=='1'){
            $new_status = 'PEND1';
        }else{
             $new_status = 'REJECT';
        }

            $data = [
                "cek_pengganti" => 1,
                "tgl_cek" => date("Y-m-d"),
                "status" => $new_status,
            ];

            $this->db->where("id", $id);
            $this->db->update("ts_cuti", $data);

            return true;
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

        $start = new DateTime($start);
        $end = new DateTime($end);

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


    function getDetailPengajuanCuti($id_pengajuan, $role_approval = "pengganti")
    {
        return $this->db
            ->select(
                'c.*,
                p.nama,
                mc.jenis_cuti,
                a.status as status_approval,
                a.id_pegawai_approval,
                a.role_approval'
            )
            ->from("ts_pengajuan_cuti_approval a")
            ->join("ts_pengajuan_cuti c", "c.id = a.id_pengajuan_cuti")
            ->join("mst_pegawai p", "p.id_pegawai = c.id_pegawai")
            ->join("mst_cuti mc", "mc.id = c.jenis_cuti")
            ->where("a.id_pengajuan_cuti", $id_pengajuan)
            ->where("a.role_approval", $role_approval)
            ->order_by("c.created_at", "ASC")
            ->get()
            ->row();
    }












    function countSearchCutiPegawai($nama)
    {
        $this->db->like("nama", $nama);
        $this->db->or_like("nip", $nama);
        $this->db->from("mst_pegawai");
        $this->db->join(
            "ts_cuti",
            "ts_cuti.id_pegawai = mst_pegawai.id_pegawai",
            "left"
        );
        return $this->db->count_all_results();
    }
    function searchCutiPegawaiByNama($nama, $limit, $page)
    {
        $this->db->order_by("tgl", "DESC");
        $this->db->limit($limit, $page);
        $this->db->select(
            "mst_pegawai.id_pegawai, nama, nip, id_jabatan, id_validator, status_kerja,ts_cuti.*"
        );
        $this->db->like("nama", $nama);
        $this->db->or_like("nip", $nama);
        $this->db->from("mst_pegawai");
        $this->db->join(
            "ts_cuti",
            "ts_cuti.id_pegawai = mst_pegawai.id_pegawai",
            "left"
        );
        $qry = $this->db->get();
        $row = $qry->result();

        return $row;
    }

    public function count_pengajuan_cuti()
    {
        $and = "";
        $status_session = $this->session->userdata("status_cuti");
        $tgl = $this->session->userdata("tanggal_cuti");
        $id_validator_cuti = $this->session->userdata("id_validator_cuti");

        if ($status_session == "PENDING-0") {
            $and = 'AND status="PEND0"';
        } elseif ($status_session == "PENDING-1") {
            $and = 'AND status="PEND1"';
        } elseif ($status_session == "PENDING-2") {
            $and = 'AND status="PEND2"';
        } elseif ($status_session == "PENDING-3") {
            $and = 'AND status="PEND3"';
        } elseif ($status_session == "APPROVE") {
            $and = 'AND status="APPROVE"';
        } elseif ($status_session == "CANCEL") {
            $and = 'AND status="CANCEL"';
        }

        $and2 = " ";
        if ($id_validator_cuti == "") {
            $and2 = " ";
        } elseif ($id_validator_cuti > 0) {
            $and2 = "AND b.id_validator = $id_validator_cuti";
        }

        $and3 = " ";

        if ($tgl == "") {
            $and3 = " ";
        } elseif ($tgl != "") {
            if ($tgl == "--SEMUA--") {
                $and3 = " ";
            } else {
                $tgl_cuti = str_replace("/", "-", $tgl);
                $tgl_cuti = format_db($tgl_cuti);
                $and3 = "AND ('$tgl_cuti' BETWEEN tgl_dari AND tgl_sampai)";
            }
        }

        $slect =
            "a.tgl, a.jns_cuti, alasan_cuti, a.id, tgl_dari, tgl_sampai, hari_cuti, nama, a.status";
        $sql = "SELECT $slect FROM ts_cuti a LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		WHERE tgl_dari >= '2025-01-01' $and $and2 $and3  ORDER BY tgl DESC ";

        $qry = $this->db->query($sql);
        $row = $qry->num_rows();
        return $row;
    }
    public function getListCutiPage($limit, $offset)
    {
        $and = "";
        $status_session = $this->session->userdata("status_cuti");
        $tgl = $this->session->userdata("tanggal_cuti");
        $id_validator_cuti = $this->session->userdata("id_validator_cuti");

        if ($status_session == "PENDING-0") {
            $and = 'AND status="PEND0"';
        } elseif ($status_session == "PENDING-1") {
            $and = 'AND status="PEND1"';
        } elseif ($status_session == "PENDING-2") {
            $and = 'AND status="PEND2"';
        } elseif ($status_session == "PENDING-3") {
            $and = 'AND status="PEND3"';
        } elseif ($status_session == "APPROVE") {
            $and = 'AND status="APPROVE"';
        } elseif ($status_session == "CANCEL") {
            $and = 'AND status="CANCEL"';
        }

        $and2 = " ";
        if ($id_validator_cuti == "") {
            $and2 = " ";
        } elseif ($id_validator_cuti > 0) {
            $and2 = "AND b.id_validator = $id_validator_cuti";
        }

        $and3 = " ";

        if ($tgl == "") {
            $and3 = " ";
        } elseif ($tgl != "") {
            if ($tgl == "--SEMUA--") {
                $and3 = " ";
            } else {
                $tgl_cuti = str_replace("/", "-", $tgl);
                $tgl_cuti = format_db($tgl_cuti);
                $and3 = "AND ('$tgl_cuti' BETWEEN tgl_dari AND tgl_sampai)";
            }
        }

        $slect =
            "a.tgl, a.jns_cuti, alasan_cuti, a.id, tgl_dari, tgl_sampai, hari_cuti, nama, a.status";
        $sql = "SELECT $slect FROM ts_cuti a LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		WHERE tgl_dari >= '2025-01-01' $and $and2 $and3  ORDER BY tgl DESC LIMIT $limit OFFSET $offset";

        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function pagination($limit, $num_rows, $url)
    {
        $config["per_page"] = $limit;

        // Tentukan jumlah total data
        $config["total_rows"] = $num_rows;

        // Tentukan base_url untuk pagination
        $config["base_url"] = base_url($url);

        // Tentukan konfigurasi untuk tampilan pagination
        $config["full_tag_open"] =
            '<ul class="pagination justify-content-end">';
        $config["full_tag_close"] = "</ul>";
        $config["first_tag_open"] = '<li class="page-item"> ';
        $config["first_tag_close"] = "</li>";
        $config["last_tag_open"] = '<li class="page-item">';
        $config["last_tag_close"] = "</li>";
        $config["next_tag_open"] = '<li class="page-item">';
        $config["next_tag_close"] = "</li>";
        $config["prev_tag_open"] = '<li class="page-item">';
        $config["prev_tag_close"] = "</li>";
        $config["cur_tag_open"] =
            '<li class="page-item  active" aria-current="page"><a href="#" class="page-link">';
        $config["cur_tag_close"] = "</a></li>";
        $config["num_tag_open"] = '<li class="page-item">';
        $config["num_tag_close"] = "</li>";

        // Inisialisasi pagination
        $this->pagination->initialize($config);
        $page = $this->uri->segment(4, 0);
        return $page;
    }


    

    function getDetailCuti($id_cuti)
    {
        $this->db->select('p.nama, p.nip, c.*');
        $this->db->where('id', $id_cuti);
        $this->db->from("ts_pengajuan_cuti c");
        $this->db->join("mst_pegawai p",   "p.id_pegawai = c.id_pegawai",  "left"   );
        $qry = $this->db->get();
        $row = $qry->row();
        return $row;
    }

    public function getDataCutiByStatus($id_pegawai, $status, $periode)
    {
        $startDate = $periode . "-01";
        $endDate = $periode . "-31";

        $slect = "alasan_cuti, tgl_dari, tgl_sampai, hari_cuti";
        $sql = "SELECT $slect FROM `ts_cuti` WHERE id_pegawai = $id_pegawai AND jns_cuti = $status AND (tgl_dari >= '$startDate' AND tgl_sampai <='$endDate') AND status = 'APPROVE';";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    public function cekCutiBersalin($id_pegawai, $tanggal)
    {
        //$startDate = $periode.'-01';
        $slect = "alasan_cuti, tgl_dari, tgl_sampai, hari_cuti";
        $sql = "SELECT $slect FROM `ts_cuti` WHERE id_pegawai = $id_pegawai AND '$tanggal' BETWEEN tgl_dari  and tgl_sampai AND jns_cuti = 2";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        if (!empty($row)) {
            return 1;
        } else {
            return 0;
        }
    }

    function searchCutiPegawai($status, $id_validator)
    {
        if ($status == "" || $status == "Semua") {
            $and = "";
        } elseif ($status == "approve") {
            $and = "AND status = 'APPROVE'";
        } elseif ($status == "reject") {
            $and = "AND status = 'REJECT'";
        } elseif ($status == "cancel") {
            $and = "AND status = 'CANCEL'";
        } elseif ($status == "Ditangguhkan") {
            $and = "AND status = 'HOLD'";
        } else {
            $and =
                "AND (status = 'PEND0' OR status = 'PEND1' OR status = 'PEND2' OR status = 'PEND3')";
        }

        $tanggal_cuti = $this->session->userdata("tanggal_cuti");

        if ($id_validator > 0) {
            $and .= "AND b.id_validator = $id_validator";
        }

        $explod = explode("to", $tanggal_cuti);
        $from = format_db($explod[0]);
        $to = format_db(@$explod[1]);
        if ($to == "") {
            $to = $from;
        }

        $sql = "SELECT a.*, b.nama, b.id_validator FROM ts_cuti a LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
				WHERE (a.tgl_dari >= '$from' AND a.tgl_sampai <='$to') $and  ORDER BY tgl_dari DESC  LIMIT 300 OFFSET 0";

        $qry = $this->db->query($sql);
        $row = $qry->result();

        //exit;
        return $row;
    }

    function getDataCutiPegawai($status, $id_validator)
    {
        if ($status == "" || $status == "Semua") {
            $where = "";
        } elseif ($status == "Approve") {
            $where = "WHERE status = 'APPROVE'";
        } elseif ($status == "Tolak") {
            $where = "WHERE status = 'REJECT'";
        } elseif ($status == "Batal") {
            $where = "WHERE status = 'CANCEL'";
        } elseif ($status == "Ditangguhkan") {
            $where = "WHERE status = 'HOLD'";
        } else {
            $where =
                "WHERE (status = 'PEND0' OR status = 'PEND1' OR status = 'PEND2' OR status = 'PEND3')";
        }

        if ($id_validator > 0) {
            if ($where != "") {
                $where .= "AND b.id_validator = $id_validator";
            } else {
                $where = "WHERE  b.id_validator = $id_validator";
            }
        }

        $sql = "SELECT a.*, b.nama, b.id_validator
		FROM ts_cuti a
		LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai $where ORDER BY tgl_dari DESC  LIMIT 300 OFFSET 0";

        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function getListPegawaiPenggantiCuti($id_pegawai, $id_jabatan)
    {
        //khusus utk psikolgi
        if ($id_jabatan == 33) {
            $id_jabatan = 1;
        }
        $sql = "SELECT id_pegawai, nama FROM mst_pegawai
        WHERE tahun_anggaran = '2024' AND id_jabatan =  $id_jabatan   AND status_kerja = 1 AND id_pegawai != $id_pegawai";
        $qry = $this->db->query($sql);

        return $qry->result();
    }

    function getCutiByJnsCuti($jns_cuti)
    {
        if ($jns_cuti == "" || $jns_cuti == 0) {
            $where = "";
        } else {
            $where = "WHERE jns_cuti = $jns_cuti";
        }

        $bulan = $this->input->post("bulan");

        if ($bulan == "" || $bulan == 0) {
            $AND = "";
        } else {
            $AND = "(month(tgl_dari) = '$bulan' OR month(tgl_sampai) = '$bulan')";

            if ($where == "") {
                $where = " WHERE  " . $AND;
            } else {
                $where = $where . " AND  $AND";
            }
        }

        $jns_pegawai = $this->input->post("jns_pegawai");

        if ($jns_pegawai == "pns") {
            $whereJnsPegawai = "AND jns_pegawai = '$jns_pegawai'";
        } elseif ($jns_pegawai == "non_pns") {
            $whereJnsPegawai = "AND jns_pegawai = '$jns_pegawai'";
        } else {
            $whereJnsPegawai = "";
        }

        $sql = "SELECT a.*, b.nama, b.id_validator, c.nama AS jabatan, d.photo
		FROM ts_cuti a
		LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		LEFT JOIN mst_jabatan c ON b.id_jabatan = c.id
		LEFT JOIN detail_pegawai d ON b.nip = d.nip $where $whereJnsPegawai";

        $qry = $this->db->query($sql);
        $row = $qry->result();

        return $row;
    }
    function getRekapCutiByJnsCuti($id_pegawai, $periode, $jns_cuti)
    {
        $this->db->select("SUM(jml_hari) AS jumlah");
        $qry = $this->db->get_where("ts_rekap_cuti", [
            "id_pegawai" => $id_pegawai,
            "periode" => $periode,
            "jns_cuti" => $jns_cuti,
        ]);
        $row = $qry->result();

        return $row;
    }

    function getHariCutiPegawai($id_pegawai, $bulan, $tahun)
    {
        $sql = "SELECT SUM(hari_cuti) AS hari_cuti FROM `ts_cuti` WHERE id_pegawai = $id_pegawai AND (MONTH(tgl_dari) = '$bulan' OR MONTH(tgl_sampai) = '$tahun') AND YEAR(tgl_dari) = '$tahun'";
        #echo $sql;
        $qry = $this->db->query($sql);
        $row = $qry->result();

        if (!empty($row)) {
            $num = $row[0]->hari_cuti;
        } else {
            $num = 0;
        }

        return $num;
    }

    function getDataCutiPegawaiKasatpel($id_validator, $status = "PEND1")
    {
        $tahun = $this->session->userdata("periode_tahun");
        $bulan = $this->session->userdata("periode_bulan");
        $periode = $tahun . "-" . $bulan;
        $periode = date("Y-m", strtotime($periode));

        if ($status == "Pending") {
            $AND = "AND (status = 'PEND0' OR status = 'PEND1') ";
        } elseif ($status == "Disetujui") {
            $AND = "AND status = 'APPROVE' ";
        } elseif ($status == "Ditolak") {
            $AND = "AND status = 'REJECT' ";
        } else {
            $AND = "";
        }

        //print_array($this->session->userdata);
        $sql = "SELECT a.*, b.nama, c.nama AS jabatan, d.photo
		FROM ts_cuti a
		LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		LEFT JOIN mst_jabatan c ON b.id_jabatan = c.id
		LEFT JOIN detail_pegawai d ON b.nip = d.nip WHERE (a.tgl_dari like '$periode%' OR a.tgl_sampai like '$periode%')  AND b.id_validator = $id_validator $AND ";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function getDataCutiPegawaiKTU($status)
    {
        $tahun = $this->session->userdata("periode_tahun");
        $bulan = $this->session->userdata("periode_bulan");
        $periode = $tahun . "-" . $bulan;
        $periode = date("Y-m", strtotime($periode));

        if ($status == "Pending") {
            $AND = "AND status = 'PEND2' ";
        } elseif ($status == "Disetujui") {
            $AND = "AND status = 'APPROVE' ";
        } elseif ($status == "Ditolak") {
            $AND = "AND status = 'REJECT' ";
        } else {
            $AND = "";
        }

        //print_array($this->session->userdata);
        $sql = "SELECT a.*, b.nama, c.nama AS jabatan, d.photo
		FROM ts_cuti a
		LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		LEFT JOIN mst_jabatan c ON b.id_jabatan = c.id
		LEFT JOIN detail_pegawai d ON b.nip = d.nip WHERE (a.tgl_dari like '$periode%' OR a.tgl_sampai like '$periode%') $AND ";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function getNumCutiPending($id_validator)
    {
        $this->db->select("id");
        $this->db->where("status", "PEND1");
        $this->db->where("tgl_dari >=", "2025-01-01");
        $this->db->where("id_validator", $id_validator);
        $this->db->from("ts_cuti");
        $this->db->join(
            "mst_pegawai",
            "mst_pegawai.id_pegawai = ts_cuti.id_pegawai",
            "left"
        );
        $qry = $this->db->get();
        $row = $qry->num_rows();
        return $row;
    }


    function getDataCutiPending(){
        $this->db->select('a.*, b.nama, b.id_validator');
        $this->db->from('ts_cuti a');
        $this->db->join('mst_pegawai b', 'a.id_pegawai = b.id_pegawai', 'left');
        $this->db->where_in('status', ['PEND1', 'PEND2', 'PEND3']);
        $this->db->order_by('tgl_dari', 'DESC');
        $this->db->limit(300, 0); // limit 300 offset 0

        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }



    public function getPengajuanCutiPegawai()
    {
            $status = $this->input->get('status');

            $this->db->select('ts_cuti.*, mst_pegawai.nama, mst_pegawai.id_puskesmas');
            $this->db->from('ts_cuti');
            $this->db->join('mst_pegawai', 'ts_cuti.id_pegawai = mst_pegawai.id_pegawai');

            // Filter status
            if ($status == 'Pending') {
                $this->db->where_in('ts_cuti.status', ['PEND0', 'PEND1', 'PEND2']);
            } elseif ($status == 'Disetujui') {
                $this->db->where('ts_cuti.status', 'APPROVE');
            }
            // jika "semua" → tidak difilter

            // Filter tanggal jika ada
            $tgl_mulai = $this->input->get('tgl_mulai');
            $tgl_akhir = $this->input->get('tgl_akhir');


            $tgl_mulai = format_db($tgl_mulai);
            $tgl_akhir = format_db($tgl_akhir);

            if ($tgl_mulai && $tgl_akhir) {
                $this->db->where('ts_cuti.tgl_dari >=', $tgl_mulai);
                $this->db->where('ts_cuti.tgl_sampai <=', $tgl_akhir);
            }

            $this->db->order_by('ts_cuti.tgl_dari', 'DESC');
            $this->db->limit(300);

          //  echo $this->db->last_query();
            return $this->db->get()->result();
    }


    public function search_pengajuan_cuti($keyword)
    {
        $this->db->select('ts_cuti.*, mst_pegawai.nama');
        $this->db->from('ts_cuti');
        $this->db->join('mst_pegawai', 'ts_cuti.id_pegawai = mst_pegawai.id_pegawai');
        $this->db->like('mst_pegawai.nama', $keyword);
        $this->db->where_in('ts_cuti.status', ['PEND0', 'PEND1', 'PEND2']);
        $this->db->order_by('ts_cuti.tgl_dari', 'DESC');
        $this->db->limit(300);

        return $this->db->get()->result();
    }


    function getCutiPending($id_validator)
    {
        $this->db->select("*");
        $this->db->where("status", "PEND1");
        $this->db->where("tgl_dari >=", "2025-01-01");
        $this->db->where("id_validator", $id_validator);
        $this->db->from("ts_cuti");
        $this->db->join(
            "mst_pegawai",
            "mst_pegawai.id_pegawai = ts_cuti.id_pegawai",
            "left"
        );
        $qry = $this->db->get();
        $row = $qry->result();
        return $row;
    }


    // function getDetailPengajuanCuti($id_pengajuan)
    // {
    //     return $this->db
    //         ->select('
       
    //             a.*,
    //             p.nama,
    //             mc.jenis_cuti
    //         ')
    //         ->from('ts_cuti a')
    //         ->join('mst_pegawai p', 'p.id_pegawai = a.id_pegawai')
    //         ->join('mst_cuti mc', 'mc.id = a.jns_cuti')
    //         ->where('a.id', $id_pengajuan)
    //         ->get()
    //         ->result();
    // }


	function getInfoPenggantiCuti($id_pengganti){


		$this->db->where('mst_pegawai.id_pegawai', $id_pengganti);
		$this->db->select('mst_pegawai.nama, mst_pegawai.nip, mst_jabatan.nama AS jabatan, mst_puskesmas.nama AS puskesmas');
		$this->db->from('mst_pegawai');
		$this->db->join('mst_jabatan', 'mst_pegawai.id_jabatan = mst_jabatan.id');
		$this->db->join('mst_puskesmas', 'mst_pegawai.id_puskesmas = mst_puskesmas.id_puskesmas');

		$qry = $this->db->get();

		$row = $qry->result();
		return $row;
	}

    


    function numDataCutiByStatus($status)
    {
        if ($status == "pending1") {
            $where_status = "PEND1";
        } elseif ($status == "pending2") {
            $where_status = "PEND2";
        } elseif ($status == "pending3") {
            $where_status = "PEND3";
        } elseif ($status == "approve") {
            $where_status = "APPROVE"; //sudah diacc kapuskec, menunggu acc kasudin
        } elseif ($status == "pending4") {
            $where_status = "APPROVE2"; //sudah diacc kasudin
        } else {
            $where_status = "REJECT";
        }

        $sql = "SELECT id FROM ts_cuti  WHERE status = '$where_status'";
        $qry = $this->db->query($sql);
        $row = $qry->num_rows();
        return $row;
    }

    function getDataCutiPegawaiByStatus($status = "")
    {
        // if ($status == "pending1") {
        //     $where_status = "PEND1";
        // } elseif ($status == "pending2") {
        //     $where_status = "PEND2";
        // } elseif ($status == "pending3") {
        //     $where_status = "PEND3";
        // } elseif ($status == "approve") {
        //     $where_status = "APPROVE"; //sudah diacc kapuskec, menunggu acc kasudin
        // } elseif ($status == "pending4") {
        //     $where_status = "APPROVE2"; //sudah diacc kasudin
        // } else {
        //     $where_status = "REJECT";
        // }

        $sql = "SELECT a.*, b.nama, c.nama AS jabatan, d.photo
		FROM ts_cuti a
		LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		LEFT JOIN mst_jabatan c ON b.id_jabatan = c.id
		LEFT JOIN detail_pegawai d ON b.nip = d.nip WHERE status = '$status' ORDER BY tgl_dari DESC";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    public function getByMonthRange($bulan, $tahun, $id_validator = null)
    {
        $firstDay = $tahun . '-' . str_pad($bulan, 2, "0", STR_PAD_LEFT) . '-01';
        $lastDay  = date("Y-m-t", strtotime($firstDay)); // hari terakhir bulan itu

        $this->db->select('a.*, b.nama, b.nip, b.id_validator');
        $this->db->from('ts_cuti a');
        $this->db->join('mst_pegawai b', 'a.id_pegawai = b.id_pegawai', 'left');

        // filter per bulan (cek rentang cuti)
        $this->db->where("(
            (MONTH(a.tgl_dari) = $bulan AND YEAR(a.tgl_dari) = $tahun)
            OR
            (MONTH(a.tgl_sampai) = $bulan AND YEAR(a.tgl_sampai) = $tahun)
            OR
            (a.tgl_dari <= '$lastDay' AND a.tgl_sampai >= '$firstDay')
        )", null, false);

        // filter berdasarkan validator (jika ada)
        if ($id_validator) {
            $this->db->where('b.id_validator', $id_validator);
        }

        return $this->db->get()->result();
    }

    function getDataCutiPegawaiPending($status, $id_validator = 0)
    {
        if ($id_validator == 0) {
            $and = "";
        } else {
            $and = "AND b.id_validator = $id_validator ";
        }

        $sql = "SELECT a.*, b.nama, c.nama AS jabatan, d.photo
		FROM ts_cuti a
		LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
		LEFT JOIN mst_jabatan c ON b.id_jabatan = c.id
		LEFT JOIN detail_pegawai d ON b.nip = d.nip WHERE status = '$status'  $and";

        #echo $sql;
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }


    function hitungHariCuti($tglMulai, $tglSelesai, $tipePegawai = 'non_shift', $hariLibur = [])
    {
         // fallback kalau hariLibur null atau bukan array
        if (!is_array($hariLibur)) {
            $hariLibur = [];
        }
        
        $mulai    = new DateTime($tglMulai);
        $selesai  = new DateTime($tglSelesai);

        // supaya tanggal selesai ikut dihitung
        $selesai->modify('+1 day');

        $interval = new DateInterval('P1D'); // interval 1 hari
        $periode = new DatePeriod($mulai, $interval, $selesai);

        $jumlahHari = 0;
        $listTanggal = [];


        foreach ($periode as $tanggal) {
            $hari = $tanggal->format('N'); // 1 = Senin, 7 = Minggu
            $tglStr = $tanggal->format('Y-m-d');

            if ($tipePegawai === 'non_shift') {
                // skip Sabtu(6), Minggu(7), dan hari libur
                if ($hari >= 6 || in_array($tglStr, $hariLibur)) {
                    continue;
                }
            }

          $jumlahHari++;
          $listTanggal[] = $tglStr;
        }

       return [
            'jumlah_hari' => $jumlahHari,
            'hari_cuti'   => implode(', ', $listTanggal)
        ];
    }

    function updateDataCuti($id_cuti) {
        $id_pegawai     = $this->input->post("id_pegawai");
        $tgl_dari       = $this->input->post("tgl_dari");
        $tgl_sampai     = $this->input->post("tgl_sampai");
        $id_pengganti   = $this->input->post("id_pengganti");
        $jns_cuti       = $this->input->post("jns_cuti");

        $tgl_dari       = format_db($tgl_dari);
        $tgl_sampai     = format_db($tgl_sampai);

        $pegawai        = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $jns_jam_kerja  = $pegawai[0]->jns_jam_kerja;

        $getHariLibur = $this->Master_model->getHariLibur();

        $hariLibur = array_map(function($row) {
            return $row->tgl;
        }, $getHariLibur);

        $hitungCuti    = $this->hitungHariCuti($tgl_dari, $tgl_sampai, $jns_jam_kerja, $hariLibur);
        $jumlah_hari   = $hitungCuti['jumlah_hari'];
        $listHariCuti  = $hitungCuti['hari_cuti'];


        if($jumlah_hari==1){
            $tgl_cuti = $tgl_dari;
        }else{
            $tgl_cuti = $tgl_dari.'s/d'.$tgl_sampai;
        }
     

     
         $newArray = [
            "id_pengganti" => $id_pengganti,
            "tgl_dari"     => $tgl_dari,
            "tgl_sampai"   => $tgl_sampai,
            "hari_cuti"    => $jumlah_hari,
            "jns_cuti"     => $jns_cuti
        ];

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $newArray);

        //ambil data log terakhir sebelum log yang paling baru  utk memastikan sisa cuti 
        $this->db->order_by('id', 'DESC');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('id_cuti !=', $id_cuti);
        $qry = $this->db->get('log_cuti', 1,0);
        $row = $qry->result();

        if(count($row)==0){
            $sisa_akhir  = 12;
        }else{
            $sisa_akhir  = $row[0]->sisa_akhir;
        }


        $updateLog = array(
            'keterangan' => 'Pemakaian hak cuti',
            'jumlah_hari' => $jumlah_hari,
            'sisa_akhir'  => $sisa_akhir -  $jumlah_hari,
            'date_create' => date('Y-m-d H:i:s')
        );

        $this->db->where('id_cuti', $id_cuti);
        $this->db->update('log_cuti', $updateLog);


        $updateTblDetailCuti = array(
            'lama_cuti'  => $jumlah_hari,
            'sisa_cuti_awal' => $sisa_akhir,
            'sisa_akhir'  => $sisa_akhir -  $jumlah_hari,
            'tgl_cuti' => $tgl_cuti,
            'list_tgl_cuti' => $listHariCuti,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('id_cuti', $id_cuti);
        $this->db->update('tbl_detail_cuti', $updateTblDetailCuti);


        return true;
    }

    function getLogCuti($id_pegawai)
    {
        $sql = "SELECT a.*, b.jns_cuti, b.alasan_cuti, b.tgl_dari, b.tgl_sampai, b.hari_cuti
		FROM log_cuti a
		LEFT JOIN ts_cuti b ON a.id_cuti = b.id WHERE a.id_pegawai = '$id_pegawai'  ORDER BY a.id DESC";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function insertLogCuti(  $id_pegawai, $jns_hak, $jns_cuti,  $id_cuti,  $jumlah_hari,$sisa_awal, $sisa_akhir, $ket ) 
    {
        $newData = [
            "id_pegawai" => $id_pegawai,
            "jns_hak_cuti" => $jns_hak,
            "jns_cuti" => $jns_cuti, //karena ini bukan permtongan cuti dari pegawai
            "id_cuti" => $id_cuti,
            "jumlah_hari" => $jumlah_hari,
            "sisa_awal" => $sisa_awal,
            "sisa_akhir" => $sisa_akhir,
            "keterangan" => $ket,
        ];

        //print_array($newData);

        $this->db->insert("ts_log_cuti", $newData);
        return true;
    }

    function getSisaCuti($id_pegawai, $jns_hak_cuti)
    {
        $this->db->order_by("id", "DESC");
        $this->db->select("sisa_akhir");
        $qry = $this->db->get_where("log_cuti", [
            "id_pegawai" => $id_pegawai,
            "jns_hak_cuti" => $jns_hak_cuti,
        ]);
        $row = $qry->result();

        if (empty($row)) {
            //klo belum pernah ngajuin cuti sama sekali
            //ambil dari tabel cuti pegawai
            $new_row = $this->Pegawai_model->getHakCutiPegawai(
                $id_pegawai,
                $jns_hak_cuti
            );

            if (!empty($new_row)) {
                $sisa_akhir = $new_row[0]->jumlah;
            } else {
                $sisa_akhir = 0;
            }
        } else {
            $sisa_akhir = $row[0]->sisa_akhir;
        }

        return $sisa_akhir;
    }

    public function getHariCuti($datediff, $start_date)
    {
        $hariCuti = 0;
        $arrayHariCuti = [];
        $newDate = $start_date;
        $loop = $datediff;

        for ($a = 0; $a < $loop; $a++) {
            $cekhariLibur = $this->cekHariLiburNasional($newDate);
            $hari_ke = formatDayOfWeek($newDate);

            if ($hari_ke < 6) {
                if ($cekhariLibur == false) {
                    $hariCuti = $hariCuti + 1;
                    $arrayHariCuti[] = $newDate;
                }
            }

            $newDate = addDaysToDate($newDate, 1);
        }

        #countDatecuti dimulai sehari dari tanggal cuti dari, jadi harus tambah dengan tanggal awal cuti agar tgl awal cuti juga dihitung sebagai hari cuti
        #$jumlah_hari_cuti = $countDateCuti+1;

        return [$hariCuti, $arrayHariCuti];
    }

    function cekTgCutiBersama($datediff, $start_date)
    {
        $newDate = $start_date;
        $loop = $datediff;

        $allowd_cuti = true;

        $tgl_cuber = ["2025-05-14", "2025-05-28", "2025-06-01"];

        for ($a = 0; $a < $loop; $a++) {
            if (in_array($newDate, $tgl_cuber)) {
                $allowd_cuti = false;
            }

            $newDate = addDaysToDate($newDate, 1);
        }

        return true;
        // return $allowd_cuti;
    }

    function updateDataDetailCuti($id_cuti)
    {
        $id_pegawai_pengganti = $this->input->post("id_pegawai_pengganti");
        $alasan_cuti = $this->input->post("alasan_cuti");
        $alamat_cuti = $this->input->post("alamat");
        $no_tlp = $this->input->post("tlp");

        $newData = [
            "alasan_cuti" => $alasan_cuti,
            "alamat_cuti" => $alamat_cuti,
            "no_tlp" => $no_tlp,
            "id_pengganti" => $id_pegawai_pengganti,
        ];

        $this->db->where("id", $id_cuti);
        $this->db->update("ts_cuti", $newData);

        return true;
    }

    function insertDataCuti($jml_hari_cuti,  $jns_hak_cuti)
    {

        //print_array($this->session->userdata);
        $id_pegawai = $this->session->userdata("id_pegawai");

        $tgl_dari           = $this->session->userdata('tgl_mulai');
        $tgl_akhir          = $this->session->userdata('tgl_akhir');
            // Ambil semua input tugas
        $tugas = [
            $this->input->post("tugas1"),
            $this->input->post("tugas2"),
            $this->input->post("tugas3"),
            $this->input->post("tugas4"),
        ];

        // Gabungkan dengan delimiter "+"
        $delegasi_tugas = implode("+", array_filter($tugas));


        $newData = [
            "tgl" => date("Y-m-d"),
            "id_pegawai" => $id_pegawai,
            "jns_cuti" => $this->session->userdata("jns_cuti"),
            "jns_hak_cuti" => $jns_hak_cuti,
            "alasan_cuti" =>  $this->input->post('alasan_cuti'),
            "tgl_dari" => format_db($tgl_dari),
            "tgl_sampai" => format_db($tgl_akhir),
            "alamat_cuti" => $this->input->post('alamat'),
            "no_tlp" => $this->input->post('tlp'),
            "hari_cuti" => $jml_hari_cuti,
            "catatan" => "",
            "status" => "PEND0",
            "id_pengganti" =>  $this->input->post('id_pengganti'),
            "delegasi_tugas" => $delegasi_tugas,
            "file_image" => ''
        ];



        $this->db->insert("ts_cuti", $newData);
        $idcuti = $this->db->insert_id();

        $sisa_cuti = $this->Cuti_model->getSisaCuti($id_pegawai, $jns_hak_cuti);
        $sisa_akhir = $sisa_cuti - $jml_hari_cuti;

        $jns_cuti = $this->session->userdata("jns_cuti");
        if ($jns_cuti == 1) {
            $this->Cuti_model->insertLogCuti(
                $id_pegawai,
                $jns_hak_cuti,
                $jns_cuti,
                $idcuti,
                $jml_hari_cuti,
                $sisa_akhir,
                $this->input->post('alasan_cuti')
            );
        }

        return $idcuti;
    }


    function getListTanggalCuti($id_cuti)
    {
        $qry = $this->db->get_where("tbl_detail_cuti", [
            "id_cuti" => $id_cuti,
        ]);
        $row = $qry->result();

        if (count($row) > 0) {
            $list_tgl_cuti = explode(',', $row[0]->list_tgl_cuti);
        } else {
            $list_tgl_cuti = [];
        }

        return $list_tgl_cuti;
           
    }


    function insertDataCutiDetail($id_cuti,   $sisa_cuti)
    {
        $nip            = $this->session->userdata("nip");
        $nama           = $this->session->userdata("nama");

        $id_pengganti   = $this->input->post('id_pengganti', true);
        $nama_pengganti = $this->Pegawai_model->getNamaPegawaiByID($id_pengganti);
        $jns_cuti       = $this->session->userdata("jns_cuti");
        $jenis_cuti     = $this->Master_model->getJenisCuti($jns_cuti);

        
        $tgl_mulai      =  $this->session->userdata('tgl_mulai');
        $tgl_akhir      =  $this->session->userdata('tgl_akhir');

        $tgl_cuti       = $tgl_mulai .'s/d'.$tgl_akhir;



        $list_tgl_cuti  = $this->session->userdata("list_tgl_cuti");
        $lama_cuti      = count($list_tgl_cuti);
        $list_hari_cuti = implode(',', $list_tgl_cuti);
        $sisa_akhir     = $sisa_cuti - $lama_cuti;

        if ($jns_cuti > 1) {

            //jika jenis cuti selain cuti tahunan, maka sisa cuti akhir sama dengan sisa cuti awal
            $sisa_akhir     = $sisa_cuti;
        }


       $data = [
                'id_cuti'         => $id_cuti,
                'nip'             => $nip,
                'nama'            => $nama,
                'nama_pengganti'  => $nama_pengganti,
                'tgl_cuti'        => $tgl_cuti,
                'jns_cuti'        => $jenis_cuti,
                'lama_cuti'       => $lama_cuti,
                'alasan_cuti'     => $this->input->post('alasan_cuti', true),
                'file_pendukung'  => '', // hasil upload file, bisa simpan path atau nama file saja
                'sisa_cuti_awal'  => $sisa_cuti,
                'sisa_akhir'      => $sisa_akhir,
                'list_tgl_cuti'   => $list_hari_cuti, // bisa JSON/implode array
                'acc_pengganti'   => null,
                'acc_atasan'      => null,
                'acc_ktu'         => null,
                'status'          => 'Pengajuan',
                'keterangan_status'  => 'Menunggu acc pengganti', // default status pengajuan
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];


        $this->db->insert('tbl_detail_cuti', $data);
        return true;
    }


    function getDataDetailCuti($id_cuti)
    {
        $qry = $this->db->get_where("tbl_detail_cuti", [
            "id_cuti" => $id_cuti,
        ]);
        $row = $qry->result();


        return $row;
    }


    function getFileCuti($id_cuti){

         $qry = $this->db->get_where("ts_file_cuti", [
            "id_cuti" => $id_cuti,
        ]);

        $row = $qry->result();


        return $row;

    }


    function getListCuti($periode)
    {
        $sql = "SELECT id, jns_cuti, tgl_dari, tgl_sampai, status, b.nama, b.id_pegawai, b.id_puskesmas, b.jns_pegawai
                FROM ts_cuti a LEFT JOIN mst_pegawai b ON a.id_pegawai = b.id_pegawai
                WHERE status != 'CANCEL' AND (tgl_dari like '$periode%' OR tgl_sampai like '$periode%')
                         ORDER BY id DESC";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }
    function getCutiPegawai($id_pegawai, $periode)
    {
        $sql = "SELECT * FROM ts_cuti WHERE id_pegawai = $id_pegawai AND (tgl_dari like '$periode%' OR tgl_sampai like '$periode%') AND status !='CANCEL' ORDER BY id DESC";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function getLogCutiPegawai($id_pegawai, $tahun='2024'){
        $this->db->order_by('id', 'DESC');
        $qry = $this->db->get_where('ts_log_cuti',['id_pegawai'=> $id_pegawai, 'hak_cuti_tahun'=> $tahun]);
        $row = $qry->result();
        return $row;
    }



    function getHistoryCutiPegawai($id_pegawai, $tahun = 2024)
    {
        $tgl_dari = $tahun . "-01-01";
        $sql = "SELECT * FROM ts_cuti WHERE id_pegawai = $id_pegawai AND tgl_dari >='$tgl_dari' ORDER BY id DESC";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

    function cekLogCuti($id_cuti){
        $qry = $this->db->get_where('ts_log_cuti',['id_cuti'=> $id_cuti]);
        $row = $qry->result();

        if(!empty($row)){
           return true;
        }else{
          return false;
        }

    }

    function getPenggunaanCuti($id_pegawai, $tahun){
        $sql = "SELECT SUM(jumlah) as total FROM `ts_log_cuti` WHERE `id_pegawai` = $id_pegawai AND jns_transaksi = 'PEMAKAIAN' AND hak_cuti_tahun='$tahun' ";
        $qry = $this->db->query($sql);
        $row = $qry->result();

        if(!empty($row)){
            $total = $row[0]->total;
        }else{
            $total = 0;
        }

        return $total;
    }


    function getSisaCutiTahun($id_pegawai, $tahun){
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $qry = $this->db->get_where('ts_log_cuti',['id_pegawai'=> $id_pegawai, 'hak_cuti_tahun'=> $tahun, 'jns_cuti'=> 'TAHUNAN']);
        $row = $qry->result();

        if(!empty($row)){
            $jumlah = $row[0]->sisa_akhir;
        }else{
            $jumlah = 0;
        }


      //  echo $jumlah;
        return $jumlah;
    }


    function getHakCutiTahun($id_pegawai, $tahun){
        $this->db->order_by('hak_cuti_tahun', 'ASC');
        $this->db->limit(1);
        $qry = $this->db->get_where('ts_log_cuti',['id_pegawai'=> $id_pegawai, 'hak_cuti_tahun'=> $tahun]);
        $row = $qry->result();

        if(!empty($row)){
            $jumlah = $row[0]->jumlah;
        }else{
            $jumlah = 0;
        }

        return $jumlah;
    }


    public function getHistoryCuti($id_pegawai, $tahun) {
          
        if ($tahun > 2025) {
            $this->db
                    ->select('a.*, mc.jenis_cuti')
                    ->from("ts_pengajuan_cuti a")
                    ->join("mst_cuti mc", "mc.id = a.jenis_cuti")
                    ->where("a.id_pegawai", $id_pegawai)
                    ->like("a.tgl_mulai", $tahun, 'after');
        }else{
             $this->db
                    ->select('a.*, mc.jenis_cuti')
                    ->from("ts_cuti a")
                    ->join("mst_cuti mc", "mc.id = a.jns_cuti")
                    ->where("a.id_pegawai", $id_pegawai)
                    ->like("a.tgl_dari", $tahun, 'after');
        }

            return $this->db
                ->order_by("a.id", "DESC")
                ->get()
                ->result();
    }


    // function getMyHistoryCuti()
    // {
    //     $tahun = 2025;
    //     $id_pegawai = $this->session->userdata("id_pegawai");
    //     $sql = "SELECT * FROM ts_cuti WHERE id_pegawai = $id_pegawai AND tgl_dari like '$tahun%' ORDER BY id DESC";
    //     $qry = $this->db->query($sql);
    //     $row = $qry->result();
    //     return $row;
    // }

    function getPermohonanPengganti($id_pegawai)
    {
        $this->db->select("id, id_pegawai, alasan_cuti, tgl_dari, tgl_sampai");
        $qry = $this->db->get_where("ts_cuti", [
            "id_pengganti" => $id_pegawai,
            "status" => "PEND0",
        ]);
        $row = $qry->result();
        return $row;
    }

    function insertDataDetailCuti($id_cuti, $id_pegawai, $tanggal)
    {
        $newData = [
            "id_cuti" => $id_cuti,
            "id_pegawai" => $id_pegawai,
            "tanggal" => $tanggal,
            "status" => 1,
        ];

        $this->db->insert("ts_cuti_detail", $newData);
        return true;
    }
    function cekCutiPegawai($tanggal, $id_pegawai)
    {
        $sql = "SELECT * FROM `ts_cuti` WHERE id_pegawai = $id_pegawai AND '$tanggal' BETWEEN tgl_dari and tgl_sampai AND status != 'CANCEL'";
        $qry = $this->db->query($sql);
        $row = $qry->result();
        return $row;
    }

   	function uploadDokumenCuti(){

		   $path = "./uploads/lain2/";

					// print_array($_FILES);
					// exit;
           $fileName = basename($_FILES["file_upload"]["name"]);

           $reversedString = strrev($fileName);
           $explodeString  = explode(".", $reversedString);
           $rs = $explodeString[0];
           $fileType   = strrev($rs);
           $files = $_FILES;
           $file_name = date('His').'_temp';

           $config['file_name']     = $file_name;
           $config['upload_path']    = $path;
           $config['allowed_types']  = 'gif|jpg|png|jpeg|jfif|bmp|tiff|webp';
           $config['max_size']       = '5000';
           $config['max_width']      = '5000';
           $config['max_height']     = '5000';
           $this->upload->initialize($config);

           if (!$this->upload->do_upload('file_upload')) {
               $data = array('error' => $this->upload->display_errors('', ''));
               $error = $data['error'];

			echo $error;
			$this->session->set_flashdata('message', $error);
			return false;




           } else {
               return $file_name;

           }


	}


    function cekIzinSakitPegawai($tanggal, $id_pegawai)
    {
        $qry = $this->db->get_where("pengajuan_izin_sakit", [
            "id_pegawai" => $id_pegawai,
            "tanggal" => $tanggal,
        ]);
        $row = $qry->result();
        return $row;
    }

    function getlisthariCutiPegawai($id_pegawai)
    {
        ///$this->db->order_by('id', 'DESC');

        $qry = $this->db->get_where("ts_cuti_detail", [
            "id_pegawai" => $id_pegawai,
        ]);
        $row = $qry->result();
        return $row;
    }
    function getListHariCuti($id_cuti)
    {
        $qry = $this->db->get_where("ts_cuti_detail", ["id_cuti" => $id_cuti]);
        $row = $qry->result();
        return $row;
    }

    function getLastIDCuti()
    {
        $this->db->select("id");
        $this->db->order_by("id", "DESC");
        $qry = $this->db->get("ts_cuti", 1, 0);
        $row = $qry->result();

        $id = $row[0]->id;
        return $id;
    }
    public function cekHariLiburNasional($tgl)
    {
        $qry = $this->db->get_where("ts_hari_libur", ["tgl" => $tgl]);
        $num = $qry->num_rows();

        if ($num == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function get_cuti_3_bulan_kebelakang() {
        $tanggal_sekarang = date('Y-m-d');
        $tanggal_batas = date('Y-m-d', strtotime('-3 months'));

        $this->db->select('mst_pegawai.id_pegawai, mst_pegawai.nama, ts_cuti.tgl_dari, ts_cuti.tgl_sampai, ts_cuti.alasan_cuti');
        $this->db->from('ts_cuti');
        $this->db->join('mst_pegawai', 'ts_cuti.id_pegawai = mst_pegawai.id_pegawai');
        $this->db->where('ts_cuti.tgl_dari >=', $tanggal_batas);
        $this->db->where('ts_cuti.tgl_dari <=', $tanggal_sekarang);
        $this->db->where('ts_cuti.jns_cuti', 2); //jenis cuti bersalin

        $query = $this->db->get();
        return $query->result();
    }

}
