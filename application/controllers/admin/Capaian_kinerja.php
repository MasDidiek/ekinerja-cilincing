<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Capaian_kinerja  extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('Laporan_model');
		$this->load->helper('text');
		$this->Auth_model->cekAuthLogin();
	}


	public function index()
	{

        $periode_bulan = $this->session->userdata('periode_bulan'); 
		$periode_tahun = $this->session->userdata('periode_tahun'); 
		$id_validator = $this->session->userdata('id_pegawai');
        $id_pj_sess = $this->session->userdata('id_pj');
        $thn_anggaran = date('Y');

        if($id_pj_sess != ''){
            $id_validator = $id_pj_sess;
        }
		
		if($periode_bulan=='') {
			$bulan = date('m');
			$tahun = date('Y');

			$this->session->set_userdata('periode_bulan', $bulan);
			$this->session->set_userdata('periode_tahun', $tahun);


		  }else{
			$bulan = $periode_bulan;
			$tahun = $periode_tahun;

		  }
          $thn_anggrn  = 2024;
		
          #$data['pegawai'] = $this->Pegawai_model->getListPegawai('non_pns', $thn_anggrn );
		  $data['pegawai'] =  $this->Pegawai_model->getListPegawaiByValidator($id_validator, $thn_anggrn);

          $this->load->view('admin/capaian_kinerja/main', $data);
    }

	function inputan_kinerja(){

		$jns_pegawai = 'non_pns';
		$thn_anggrn = date('Y');
		$data['pegawai'] = $this->Pegawai_model->getListPegawai($jns_pegawai, $thn_anggrn );
	
		$this->load->view('admin/capaian_kinerja/inputan_kinerja', $data);
	}


	function detail_capaian($id_pegawai, $nip){
		$periode_bulan = $this->session->userdata('periode_bulan'); 
		$periode_tahun = $this->session->userdata('periode_tahun'); 
		$periode = $periode_tahun.'-'.$periode_bulan;
        $periode = date('Y-m', strtotime($periode));

		
	
		$data['detail_pegawai']   = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$data['dataRekap'] = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
		$data['rekapTKD'] = $this->Laporan_model->getRekapTKDPegawai($nip, $periode);
		$data['master_cuti'] = $this->Master_model->getlistCuti();
		$this->load->view('admin/capaian_kinerja/detail_capaian', $data);
	}

	function updateCapaianKinerjaPegawai($id_pegawai, $nip,  $totalCapaian, $periode){


		$peg = $this->Pegawai_model->getDetailPegawai($id_pegawai);
		$gaji_pokok = $peg[0]->gaji_pokok;
		$pengkalian = $peg[0]->pengkalian;
		$pph21 = $peg[0]->pph21;
		$bpjs_kes = $peg[0]->bpjs_kes;
		$bpjs_tk = $peg[0]->bpjs_tk;
		$bpjs_kes = $peg[0]->bpjs_kes;

		$tkd_pokok = ceil($gaji_pokok*$pengkalian);
		$bruto = round(($tkd_pokok*$totalCapaian)/100);
		$pengurang = $pph21+$bpjs_kes+$bpjs_tk;

		$thp = $bruto-$pengurang ;

		$newRekap = array(
			'tkd_pokok' => $tkd_pokok,
			'capaian'=> $totalCapaian,
			'bruto'=> $bruto,
			'pph21' => $pph21,
			'bpjs'=> $bpjs_kes,
			'bpjs_tk' => $bpjs_tk,
			'thp'=> $thp,
			'update_on'=> date('Y-m-d H:i:s')
		);

		$this->db->where('periode', $periode);
		$this->db->where('nip', $nip);
		$this->db->update('ts_rekap_tkd', $newRekap);
        $this->session->set_flashdata('message',' Data rekap TKD berhasil diupdate');
        redirect('admin/capaian_kinerja/detail_capaian/'.$id_pegawai.'/'.$nip);

	}

}