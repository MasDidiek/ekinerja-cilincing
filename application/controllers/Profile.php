<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Profile extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();

		 $this->load->model('Profile_model');
         $this->load->model('Listing_model');
         $this->load->model('Laporan_model');
           $this->load->model('Admin_cuti_model', 'acm');
         $this->load->helper('text');
         $this->Auth_model->cekAuthLogin();
	}



	function my_profile(){

        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');
        $this->Pegawai_model->cekDataDetailPegawai($nip);
        
        $tahun = date('Y');
		$data['list_jabatan'] 	   = $this->Master_model->getlistJabatan();
		$data['list_pendidikan']   = $this->Master_model->getlistPendidikan();
		$data['list_poli'] 		   = $this->Master_model->getlistPoli();
		$data['list_puskesmas']    = $this->Master_model->getlistPuskesmas();
		$data['list_Status']       = $this->Master_model->getlistStatus();
		$data['list_validator']    = $this->Pegawai_model->getValidator();

		$data['rekap_absensi']    = $this->Pegawai_model->getRekapAbsensiPegawai($id_pegawai, $tahun);
		$data['rekap_capaian_kinerja'] = $this->Kinerja_model->getListCapaian($nip);
		$data['pegawai'] 		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);

		$data['cutiPegawai']    = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
        
        $data['detailPegawai'] = $this->Pegawai_model->getDataDetailPegawai($nip);
        
        $data['data_diklat'] = $this->Profile_model->getDataDiklat($nip);



		$this->load->view('profile/data_diri/my_profile', $data);

		//$this->load->view('profile/my_profile', $data);
	}

    function edit_profile(){
        
        $id_pegawai = $this->session->userdata('id_pegawai');

        $data['pegawai'] 		   =  $this->Pegawai_model->getDataEditPegawai($id_pegawai);
        $this->load->view('profile/edit_profile', $data);
    }

    public function update_profle()
    {
        $nip = $this->input->post('nip', true);

        if (!$nip) {
            show_error('Data tidak valid');
        }

    

        $dataDetail = [
            'golongan_darah'     => $this->input->post('golongan_darah', true),
            'jns_kelamin'       => $this->input->post('jenis_kelamin', true),
            'tempat_lahir'       => $this->input->post('tempat_lahir', true),
            'tgl_lahir'          => $this->input->post('tgl_lahir', true),
            'no_tlp'             => $this->input->post('no_telp', true),
            'email'              => $this->input->post('email', true),
            'pendidikan'         => $this->input->post('pendidikan', true),
            'status_perkawinan'  => $this->input->post('status_pernikahan', true),
            'agama'              => $this->input->post('agama', true),
            'alamat_ktp'         => $this->input->post('alamat', true),
            'alamat_domisili'    => $this->input->post('alamat_domisili', true),
            'no_ktp'             => $this->input->post('no_ktp', true),
            'npwp'               => $this->input->post('npwp', true),
            'no_rekening'        => $this->input->post('no_rekening', true),
        ];

        $this->db->trans_start();

       // $this->db->where('nip', $nip)->update('mst_pegawai', $dataPegawai);
        $this->db->where('nip', $nip)->update('detail_pegawai', $dataDetail);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
        } else {
            $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
        }

        redirect('profile/my_profile');
    }

    function dokumen(){
        $nip    = $this->session->userdata('nip');
        $id_pegawai   = $this->session->userdata('id_pegawai');
        $data['pegawai'] 		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);   
        $data['detailPegawai'] = $this->Pegawai_model->getDataDetailPegawai($nip);
        $this->load->view('profile/data_diri/dokumen', $data);
    }




    function my_gaji()  {
        $nip_user = $this->session->userdata('nip');
        $nama     = $this->session->userdata('nama');
        $nik     = $this->Pegawai_model->getNIKbyNIP($nip_user);

        $jns_pegawai   = $this->session->userdata('jns_pegawai');
//print_array($this->session->userdata);
        $nama = ucwords($nama);


        $tahun  = date('Y');

        if($jns_pegawai=='non_pns')
        {
              $data['datalist'] = $this->Laporan_model->getGajiPegawaipertahun($nik, $tahun);
               $this->load->view('profile/my_gaji', $data);
        }else{
            //ppppk paruh waktu
             $data['datalist'] = $this->Laporan_model->getGajiPegawaipertahunP3K($nip_user, $tahun);
              $this->load->view('profile/my_gaji_pw', $data); //p3k paruh waktu
        }
    

    }

    

    function detail_gaji($id){


        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');


        $data['detail_pegawai']		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['detail_gaji']		   =  $this->Laporan_model->getDetailRekapGaji($id);

        $this->load->view('profile/detail_gaji', $data);

    }

    

    function detail_gaji_p3kpw($id){


        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');


        $data['detail_pegawai']		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['detail_gaji']		   =  $this->Laporan_model->getDetailRekapGajiP3k($id);

        $this->load->view('profile/detail_gaji_p3kpw', $data);

    }

    function my_tkd()  {
       $nip_user    =  $this->session->userdata('nip');
       $jns_pegawai =  $this->session->userdata('jns_pegawai');
       $tahun = $this->input->get('tahun')?? date('Y');

       if($jns_pegawai=='non_pns'){
            $data['datalist'] = $this->Laporan_model->getRekapTKDPegawaipertahun($nip_user, $tahun);
       }else{

        $qry = $this->db->get_where('detail_pegawai',['nip' => $nip_user]);
        $row = $qry->row();

        if($row){
             $no_rekening = $row->no_rekening;
        }else{
             $no_rekening = 0;
        }



        if($tahun < 2026){
            //status masih non_pns
            $this->db->select('*');
            $this->db->from('ts_rekap_tkd');
            $this->db->where('no_rekening', $no_rekening);
            $this->db->like('periode', $tahun,'after');
            $query = $this->db->get();

           
        }else{
            //status ppp3_pw
            $this->db->select('*');
            $this->db->from('ts_rekap_tkd_pppk');
            $this->db->where('nip', $nip_user);
            $this->db->like('periode', $tahun,'after');
            $query = $this->db->get();

        }
         

           $data['datalist'] = $query->result();

       }
               
        $this->load->view('profile/my_tkd', $data);


    }


    function reset_password()  {
        
        $id_pegawai = $this->session->userdata('id_pegawai');
        $data['pegawai'] 		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $this->load->view('profile/reset_password', $data);
    }

    public function update_password()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules(
            'current_password',
            'Current Password',
            'required'
        );

        $this->form_validation->set_rules(
            'new_password',
            'New Password',
            'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.#_-])[A-Za-z\d@$!%*?&.#_-]{8,}$/]'
        );

        $this->form_validation->set_rules(
            'confirm_new_password',
            'Confirm Password',
            'required|matches[new_password]'
        );

        $this->form_validation->set_message(
            'regex_match',
            'Password minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka dan simbol.'
        );

        $this->form_validation->set_message(
            'matches',
            'Confirm password tidak sama.'
        );
        $this->form_validation->set_message(
            'required',
            '{field} wajib diisi.'
        );

        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profile/reset_password');
        }

        $current_password = $this->input->post('current_password');
        $new_password     = $this->input->post('new_password');

        // ambil user login
        $user_id = $this->session->userdata('id_pegawai');
        $user = $this->db->get_where('mst_pegawai', ['id_pegawai' => $user_id])->row();

        // cek password lama
        if (!password_verify($current_password, $user->password)) {
            $this->session->set_flashdata('error', 'Current password salah');
            redirect('profile/reset_password');
        }

        // update password
        $data = [
            'password' => password_hash($new_password, PASSWORD_DEFAULT)
        ];

        $this->db->where('id_pegawai', $user_id);
        $this->db->update('mst_pegawai', $data);

        $this->session->set_flashdata('success', 'Password berhasil diubah');
        redirect('profile/reset_password');
    }


    function my_thr() {
        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip_user = $this->session->userdata('nip');
        $nama     = $this->session->userdata('nama');
        //$npwp     = $this->Laporan_model->getNpwpByNip($nip_user);

        $data['detail_pegawai']		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);

        $thr_tkd  = $this->Listing_model->getListingTHR($nip_user, 'tkd');
        $thr_gaji = $this->Listing_model->getListingTHR($nip_user, 'gaji');

        $data['thr_gaji'] = $thr_gaji;
        $data['thr_tkd'] = $thr_tkd;
        $this->load->view('profile/my_thr', $data);
    }

    function gaji13(){
        $nip_user = $this->session->userdata('nip');
        $id_pegawai = $this->session->userdata('id_pegawai');
        $nik = $this->Pegawai_model->getNIKbyNIP($nip_user);
        

        $qry = $this->db->get_where('ts_listing_gaji13',['nik' => $nik]);
        $data['datalist'] = $qry->result();

        $this->load->view('profile/list_gaji13', $data);
        
    }

    function detail_gaji13($id){

        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');


        $data['detail_pegawai']		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['detail_gaji']		   =  $this->Laporan_model->getDetailRekapGaji13($id);

        $this->load->view('profile/detail_gaji13', $data);
    }


    function ttd_spj($periode) {
        $this->load->view('dashboard/ttd_spj');
    }

    function save_ttd_thr_tkd() {
        $id_spj = $this->input->post('id_spj');
        $tahun  = $this->input->post('tahun');
        $no_hp  = $this->input->post('no_hp');

        $nip = $this->session->userdata('nip');

		$sig_string=$this->input->post('signature');
		$nama_file="signature_".$nip.date("his").".png";

       
		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		 
        // $jns_pegawai =  $this->session->userdata('jns_pegawai');
        // $table = ($jns_pegawai == 'non_pns' || $tahun <= 2025) 
        // ? 'ts_rekap_tkd' 
        // : 'ts_rekap_tkd_pppk';

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'no_hp' => $no_hp,
            'ttd_spj' => $nama_file,
            'date_ttd' => $dateNow,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('ts_listing_thr_tkd', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/my_thr' );

	}


    function save_ttd_thr_gaji() {
        $id_spj = $this->input->post('id_spj');
        $tahun  = $this->input->post('tahun');
        $no_hp  = $this->input->post('no_hp');

        $nip = $this->session->userdata('nip');

		$sig_string=$this->input->post('signature');
		$nama_file="signature_".$nip.date("his").".png";

       
		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		 
        // $jns_pegawai =  $this->session->userdata('jns_pegawai');
        // $table = ($jns_pegawai == 'non_pns' || $tahun <= 2025) 
        // ? 'ts_rekap_tkd' 
        // : 'ts_rekap_tkd_pppk';

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'no_hp' => $no_hp,
            'ttd_spj' => $nama_file,
            'date_ttd' => $dateNow,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('ts_listing_thr', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/my_thr' );

	}


    function detail_thr_gaji($id){
        $nip = $this->session->userdata('nip');
        $dataTKD = $this->db->where('id', $id)->get('ts_listing_thr')->row();

        $qry = $this->db->get_where('mst_pegawai', array('nip' => $nip));
        $data['detail_pegawai']= $qry->row();

        $data['detail_thr'] = $dataTKD;

      $this->load->view('profile/detail_thr_gaji', $data);

    }

    
    


    function insert_ttd_spj_tkd() {
        $id_spj = $this->input->post('id_spj');
        $tahun  = $this->input->post('tahun');
        $no_hp  = $this->input->post('no_hp');
        $nip = $this->session->userdata('nip');
        $nama = $this->session->userdata('nama');

        $kata = explode(" ", strtolower(trim($nama)));

        $sign_name = $kata[0];
        if (isset($kata[1])) {
         $sign_name .= "_" . $kata[1];
        }

        
		$sig_string=$this->input->post('signature');
		$nama_file="sign_".$sign_name.'_'.date("dmY").".png";

		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		 
        $jns_pegawai =  $this->session->userdata('jns_pegawai');
        $table = ($jns_pegawai == 'non_pns' || $tahun <= 2025) 
        ? 'ts_rekap_tkd' 
        : 'ts_rekap_tkd_pppk';

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'no_hp' => $no_hp,
            'ttd_spj' => $nama_file,
            'ttd_on' => $dateNow,
        );

        $this->db->where('id', $id_spj);
        $this->db->update($table, $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/detail_tkd/'.$id_spj.'/'.$tahun );

	}

    function insert_tdd_spj_gaji() {
		$sig_string=$this->input->post('signature');
		$nama_file="signature_".date("his").".png";

		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		$id_spj = $this->input->post('id_spj');


        $no_hp=$this->input->post('no_hp');

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'ttd_spj' => $nama_file,
            'date_ttd' => $dateNow,
            'no_hp' => $no_hp,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('ts_listing_gaji', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/detail_gaji/'.$id_spj);

	}

    function insert_tdd_spj_gaji13() {
		$sig_string=$this->input->post('signature');
		$nama_file="signature_".date("his").".png";

		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		$id_spj = $this->input->post('id_spj');


        $no_hp=$this->input->post('no_hp');

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'ttd_spj' => $nama_file,
            'date_ttd' => $dateNow,
            'no_hp' => $no_hp,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('ts_listing_gaji13', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/detail_gaji13/'.$id_spj);

	}


     function insert_tdd_spj_gaji_p3k() {
		$sig_string=$this->input->post('signature');
		$nama_file="signature_".date("his").".png";

		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		$id_spj = $this->input->post('id_spj');


        $no_hp=$this->input->post('no_hp');

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'ttd_pegawai'=>1,
            'ttd_oleh' => $nama_file,
            'tgl_ttd' => date('Y-m-d'),
            'no_hp' => $no_hp,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('tbl_gaji_p3k_pw', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/detail_gaji_p3kpw/'.$id_spj);

	}



    function insert_tdd_spj_thr() {
		$sig_string=$this->input->post('signature');
		$nama_file="signature_".date("his").".png";

		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		$id_spj = $this->input->post('id_spj');


        $no_hp=$this->input->post('no_hp');

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'no_hp' => $no_hp,
            'ttd_spj' => $nama_file,
            'date_ttd' => $dateNow,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('ts_listing_thr', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/my_thr');

	}


    function insert_tdd_spj_thr_gaji() {

         $nip = $this->session->userdata('nip');

		$sig_string=$this->input->post('signature');
		$nama_file="signature_".$nip.date("his").".png";


		$path = './uploads/ttd_spj/';
		$tte  = $path.'/'.$nama_file;

		file_put_contents($tte, file_get_contents($sig_string));

		$id_spj = $this->input->post('id_spj');


        $no_hp=$this->input->post('no_hp');

        $dateNow = date('Y-m-d H:i:s');
        $new_array = array(
            'no_hp' => $no_hp,
            'ttd_spj' => $nama_file,
            'date_ttd' => $dateNow,
        );

        $this->db->where('id', $id_spj);
        $this->db->update('ts_listing_thr', $new_array);

        $this->session->set_flashdata('message','<strong>Success!!! </strong> SPJ berhasil ditandatangani');


        redirect('profile/my_thr');

	}

    


    function detail_tkd($id_rekap_tkd, $tahun=2025){

        $id_pegawai = $this->session->userdata('id_pegawai');
        $nip = $this->session->userdata('nip');

        $this->Pegawai_model->cekDataDetailPegawai($nip);
        $jns_pegawai =  $this->session->userdata('jns_pegawai');

        $table = ($jns_pegawai == 'non_pns' || $tahun <= 2025) 
        ? 'ts_rekap_tkd' 
        : 'ts_rekap_tkd_pppk';

         $data['detail_tkd']		   =  $this->Laporan_model->getDetailRekapTKD($id_rekap_tkd, $table );
        //$data['detail_pegawai']		   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
    

        $transaksi = $this->db
                            ->where('id_rekap_tkd', $id_rekap_tkd)
                            ->get('ts_transaksi_tkd')
                            ->result();


                            
        $data['transaksi'] = $transaksi;

        $this->load->view('profile/detail_tkd', $data);

    }


    function getValidator()  {

        $klaster = $this->input->post('klaster');
        $id_puskesmas = $this->input->post('id_puskesmas');

        if($id_puskesmas >1){
             $validator = $this->db->where('id_puskesmas', $id_puskesmas)->get('mst_validator')->row();    
             $nama = $validator->nama;
        }else{

            $qry = $this->db->get_where('mst_validator', ['id_puskesmas'=> $id_puskesmas, 'klaster'=> $klaster]);
            $row = $qry->row();
            $nama = $row->nama;
        }
	

        echo $nama;

    }
    function change_password(){
        $id_pegawai = $this->session->userdata('id_pegawai');
        #print_array($this->input->post());
        $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $this->load->view('profile/change_password', $data);
    }

    function change_password_process(){

        #print_array($this->input->post());
        $id_pegawai    = $this->session->userdata('id_pegawai');
        $curr_password = $this->input->post('curr_password');
        $new_password  = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');




        $this->load->library('form_validation');
        $config = array(
            array(
                  'field'   => 'old_password',
                  'label'   => 'Password lama',
                  'rules'   => 'trim|required|callback_oldpassword_check'
               ),
            array(
                  'field'   => 'new_password',
                  'label'   => 'New Password',
                  'rules'   => 'trim|required|min_length[8]|max_length[25]|callback_password_check'
               ),
            array(
                  'field'   => 'conf_password',
                  'label'   => 'Confirm Password',
                  'rules'   => 'trim|required|matches[new_password]'
               ));


         $this->form_validation->set_rules($config );
        if ($this->form_validation->run() == FALSE)
        {

            #print_array($this->input->post());
            $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
            $this->load->view('profile/change_password', $data);
                //$this->load->view('myform');
        } else {

            $new_pass = md5($new_password);
            $this->Auth_model->updatePassword($new_pass);

            $this->session->set_flashdata('message_status', 200);


            redirect('profile/change_password');

        }




    }


    public function password_check($str)
    {

        if (preg_match('~[0-9]+~', $str)) {
            if(preg_match('/[A-Z]/', $str)){


                    if(preg_match('/[^a-zA-Z0-9]/', $str) > 0)
                    {
                        return true;
                    }else{
                        $this->form_validation->set_message('password_check', ' {field} harus berisi minimal 1 special karakter [@...&]');
                        return FALSE;
                    }


            }else{
                $this->form_validation->set_message('password_check', ' {field} harus berisi minimal 1 huruf besar [A-Z]');
                return FALSE;
            }

        }else{
            $this->form_validation->set_message('password_check', ' {field} harus berisi minimal 1 angka [0-9]');
            return FALSE;
        }


    }

    function oldpassword_check($password) {



        if($password ==''){
            $this->form_validation->set_message('oldpassword_check', ' {field} harus diisi ');
            return FALSE;
        }else{
            $nip        = $this->session->userdata('nip');
            $password   = md5($password);
            $cekAuth    = $this->Auth_model->cekUserAuth($nip, $password);

            if(empty($cekAuth)){
                $this->form_validation->set_message('oldpassword_check', ' {field} salah ');
                return FALSE;
            }else{
                return TRUE;
            }
        }



    }


    public function is_password_strong($password)
    {
    if (preg_match('#[0-9]#', $password) && preg_match('#[a-zA-Z]#', $password)) {
        return TRUE;
    }
    return FALSE;
    }

    function edit_sipstr(){
        $id = $this->input->post('id');


		$qry = $this->db->get_where('tbl_sip_str', array('id'=> $id));
		$row = $qry->result();


        $tgl_terbit = $row[0]->tgl_terbit;
        $tgl_kadaluarsa = $row[0]->tgl_kadaluarsa;
        $no_sip_str = $row[0]->no_sip_str;


        echo '
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="recipient-name" class="control-label">Tanggal Terbit:</label>
                            <input type="text" required name="tanggal_terbit" value="'.format_view($tgl_terbit).'" autocomplete="off" class="form-control" id="dpd3" >
                        </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                            <label for="recipient-name" class="control-label">Tanggal Expired:</label>
                            <input type="text" name="tanggal_expired" autocomplete="off"  value="'.format_view($tgl_kadaluarsa).'"  class="form-control" id="dpd4" >
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id" id="id" value="'.$id.'">


                <div class="mb-3">
                    <label for="message-text" class="control-label">No <span id="title_no">SIP</span>:</label>
                    <input type="text" name="no_sip_str" required autocomplete="off" class="form-control" value="'.$no_sip_str.'" >
                </div>




            <script>

                var nowTemp = new Date();
                var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

                var checkin = $("#dpd3").datepicker({
                        onRender: function(date) {
                            //  return date.valueOf() < now.valueOf() ? "disabled" : "";
                            }
                }).on("changeDate", function(ev) {
                if (ev.date.valueOf() > checkout.date.valueOf()) {
                    var newDate = new Date(ev.date)
                    newDate.setDate(newDate.getDate() + 1);
                    checkout.setValue(newDate);
                }
                    checkin.hide();
                $("#dpd4")[0].focus();
                }).data("datepicker");
                    var checkout = $("#dpd4").datepicker({
                    onRender: function(date) {
                    return date.valueOf() <= checkin.date.valueOf() ? "disabled" : "";
                }
                }).on("changeDate", function(ev) {
                checkout.hide();
                }).data("datepicker");

            </script>
            ';



    }


    function edit_pelatihan(){
        $id = $this->input->post('id');
        $arayJenis = getListJnsDiklat();

		$qry = $this->db->get_where('tbl_diklat', array('id'=> $id));
		$row = $qry->result();


        $tgl_terbit = $row[0]->tgl_mulai;
        $tgl_kadaluarsa = $row[0]->tgl_selesai;
        $judul_pelatihan = $row[0]->judul_pelatihan;
        $lokasi_diklat = $row[0]->lokasi_diklat;


        echo '
                <div class="row">

                        <div class="mb-3">
                                <label for="message-text" class="control-label">Judul / Nama Pelatihan:</label>
                                <input type="text" name="judul" required autocomplete="off" class="form-control" value="'.$judul_pelatihan.'" >
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recipient-name" class="control-label">Tanggal Mulai:</label>
                                    <input type="text" required name="tanggal_mulai" value="'.format_view($tgl_terbit).'" autocomplete="off" class="form-control" id="dpd3" >
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="mb-3">
                                    <label for="recipient-name" class="control-label">Tanggal Selesai:</label>
                                    <input type="text" name="tanggal_selesai" autocomplete="off"  value="'.format_view($tgl_kadaluarsa).'"  class="form-control" id="dpd4" >
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id" id="id" value="'.$id.'">


                        <div class="mb-3">
                        <label for="recipient-name" class="control-label">Jenis Diklat:</label>
                        <select name="jns_diklat" id="jns_diklat"  class="form-control">';

                                for ($d=0; $d < count($arayJenis) ; $d++) {
                                    $jns_diklat = trim($arayJenis[$d]);

                                    if($judul_pelatihan == $jns_diklat){
                                        echo '<option value="'.$jns_diklat.'" selected>'.$jns_diklat .'</option>';
                                    }else{
                                        echo '<option value="'.$jns_diklat.'">'.$jns_diklat .'</option>';
                                    }




                                }
                        echo '
                        </select>
                    </div>


                        <div class="mb-3">
                            <label for="message-text" class="control-label">Lokasi/Tempat Pelatihan:</label>
                            <input type="text" name="lokasi" required autocomplete="off" class="form-control" value="'.$lokasi_diklat.'" >
                        </div>

                        <h6>Ubah file Sertifikat/Surat Tugas ?</h6>
                        <input type="radio" name="change_file" class="ganti_file" value="0" checked> Tidak &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="change_file" class="ganti_file" value="1"> Iya





                        <div class="mb-3 edit-image d-none">
                                <div class="card w-100 bg-info-subtle overflow-hidden p-2 shadow-none">
                                    <h6>Dokumen Sertifikat/Surat Tugas:</h6>
                                    <p class="text-danger">
                                        Jenis file yang diizinkan : <strong>PDF </strong> <br>
                                        Ukuran Maksimum File      : <strong>1 MB </strong>
                                    </p>


                                    <br>
                                    <br>
                                        <input type="file" name="filedocs" id="file-dokumen" class="d-none" multiple />
                                             <label for="file-input">
                                                <div class="btn btn-primary">
                                                    <i class="fa fa-folder-open"></i>
                                                    &nbsp; Choose Files To Upload
                                                </div>
                                            </label>

                                        <div id="num-of-files">No Files Choosen</div>
                                        <ul id="files-list"></ul>
                                        <div id="inforequired"></div>
                                    </div>
                        </div>





            <script>


            $(".ganti_file").click(function(){
                var ganti = $(this).val();

                if(ganti==1){
                    $(".edit-image").removeClass("d-none");
                    $("#file-dokumen").prop("required",true);

                }else{
                    $(".edit-image").addClass("d-none");
                    $("#file-dokumen").prop("required",false);

                }


            });


                var nowTemp = new Date();
                var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

                var checkin = $("#dpd3").datepicker({
                        onRender: function(date) {
                            //  return date.valueOf() < now.valueOf() ? "disabled" : "";
                            }
                }).on("changeDate", function(ev) {
                if (ev.date.valueOf() > checkout.date.valueOf()) {
                    var newDate = new Date(ev.date)
                    newDate.setDate(newDate.getDate() + 1);
                    checkout.setValue(newDate);
                }
                    checkin.hide();
                $("#dpd4")[0].focus();
                }).data("datepicker");
                    var checkout = $("#dpd4").datepicker({
                    onRender: function(date) {
                    return date.valueOf() <= checkin.date.valueOf() ? "disabled" : "";
                }
                }).on("changeDate", function(ev) {
                checkout.hide();
                }).data("datepicker");

            </script>
            ';



    }






    function update_sip_str(){
        $tanggal_terbit     =  $this->input->post('tanggal_terbit');
        $tanggal_expired     =  $this->input->post('tanggal_expired');
        $id     =  $this->input->post('id');


        $tgl_terbit     = format_db($tanggal_terbit);
        if($tanggal_expired==''){
			$tgl_kadaluarsa     = '';
		}else{
			$tgl_kadaluarsa     = format_db($tanggal_expired);
		}


        $newarray = array(
            'tgl_terbit' => $tgl_terbit,
            'tgl_kadaluarsa' => $tgl_kadaluarsa,
            'no_sip_str' => $this->input->post('no_sip_str')
        );

        #print_array($newarray);
        $this->db->where('id', $id);
        $this->db->update('tbl_sip_str', $newarray);

        $this->session->set_flashdata('message_status', 200);
        $pesan =  createMessageInfo('Data SIP / STR berhasil diupdate', 'success');
        $this->session->set_flashdata('message_update', $pesan);

        redirect('profile/my_profile');

    }

    function data_keluarga(){
         $nip    = $this->session->userdata('nip');
        $id_pegawai   = $this->session->userdata('id_pegawai');

        $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['anggota_keluarga'] =    $this->db->get_where('anggota_keluarga', ['id_pegawai' => $id_pegawai])->result();


        $this->load->view('profile/data_keluarga', $data);
    }


    public function simpan_data_keluarga()
    {
        $data = [
            'id_pegawai'         => $this->input->post('id_pegawai'),
            'status_anggota'  => $this->input->post('status_anggota'),
            'nama'               => $this->input->post('nama'),
            'tgl_lahir'          => $this->input->post('tgl_lahir'),
            'jns_kelamin'                => $this->input->post('jns'),
            'pekerjaan'          => $this->input->post('pekerjaan'),
            'pendidikan'         => $this->input->post('pendidikan'),
            'alamat'             => $this->input->post('alamat')
        ];

        $this->db->insert('anggota_keluarga', $data);
        echo json_encode(['status' => 'ok']);
    }

    public function get_keluarga_by_pegawai($id_pegawai)
    {
        $data = $this->db->get_where('anggota_keluarga', ['id_pegawai' => $id_pegawai])->result();
        echo json_encode($data);
    }


    public function get_keluarga_by_id($id)
    {
        $data = $this->db->get_where('anggota_keluarga', ['id' => $id])->row();
        echo json_encode($data);
    }

    public function update_keluarga()
    {
        $id = $this->input->post('id');
        $data = $this->input->post();
        unset($data['id']); // hapus id agar tidak ikut terupdate

        $this->db->where('id', $id)->update('anggota_keluarga', $data);
        echo json_encode(['status' => 'ok']);
    }

    public function delete_keluarga()
    {
        $id = $this->input->post('id');
        $this->db->delete('anggota_keluarga', ['id' => $id]);
        echo json_encode(['status' => 'deleted']);
    }

    function riwayat_pendidikan(){

        $nip    = $this->session->userdata('nip');
        $id_pegawai   = $this->session->userdata('id_pegawai');

        $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['riwayat_pendidikan']   =  $this->Pegawai_model->getRiawayatPendidikan($nip);


        $this->load->view('profile/riwayat_pendidikan', $data);
    }

    function update_data_pendidikan(){
        $nip    = $this->session->userdata('nip');

        $jenjang = $this->input->post('jenjang');
        $tahun_lulus = $this->input->post('tahun_lulus');
        $jurusan = $this->input->post('jurusan');
        $nama_sekolah = $this->input->post('nama_sekolah');
        //print_array($this->input->post());
        for ($i=0; $i < count($jenjang) ; $i++) {

            $pend = $this->Pegawai_model->getRiwayatPendidikanByJenjang($nip, $jenjang[$i]);

            if(!empty($pend)){

                    $newData = array(
                        'jenjang' => $jenjang[$i],
                        'nama_sekolah' => $nama_sekolah[$i],
                        'jurusan' => $jurusan[$i],
                        'tahun_lulus' => $tahun_lulus[$i],
                    );

                    $this->db->where('nip', $nip);
                    $this->db->update('tbl_riwayat_pendidikan', $newData);
            }else{

                $newData = array(
                    'nip' => $nip,
                    'jenjang' => $jenjang[$i],
                    'nama_sekolah' => $nama_sekolah[$i],
                    'jurusan' => $jurusan[$i],
                    'tahun_lulus' => $tahun_lulus[$i],
                );



                $this->db->insert('tbl_riwayat_pendidikan', $newData);
            }


        }

        #// hapus data yang kosong

        $sql = "DELETE FROM tbl_riwayat_pendidikan WHERE nip = '$nip' AND nama_sekolah = '' ";
        $this->db->query($sql);


        $this->session->set_flashdata('message_status', 200);
        $pesan =  createMessageInfo('Data riwayat pendidikan berhasil disimpan', 'success');
        $this->session->set_flashdata('message_update', $pesan);

        redirect('profile/riwayat_pendidikan');

        //print_array($this->input->post());
    }



    public function upload_dokumen()
    {

        $nip    = $this->session->userdata('nip');

        $nama          = $this->session->userdata('nama');
        $jenis_dokumen = $this->input->post('jenis_dokumen');
        $keterangan    = $this->input->post('keterangan');
        $now = date('His');


        $jns_dok = str_replace(' ', '_', $jenis_dokumen);

        $first_name = strtok($nama, " "); // Test
        $img_name   = $now.'_'.$jns_dok.'_'.$first_name;
		$ImageName_temp = createTempImageName($img_name, 'imageupload');
		$ImageName_db   = createImageName($img_name, 'imageupload');
		$upload    	   = $this->Profile_model->upload_dokumen($ImageName_temp);

		if ($upload == '') {
		    $this->Profile_model->upload_dokumen($ImageName_db);
			#delete image temp
			$fileImageName 	= './uploads/dokumen/' . $ImageName_temp;
			unlink($fileImageName);
			#upload new image
                $this->db->insert('dokumen_pegawai', [
                    'nip' => $nip,
                    'jenis_dokumen' => $jenis_dokumen,
                    'nama_file' => $ImageName_db,
                    'keterangan' => $keterangan
                ]);
            $pesan =  createMessageInfo('Dokumen berhasil disimpan', 'success');
            $this->session->set_flashdata('message_update', $pesan);
            redirect('profile/dokumen');

		} else {
			// echo '<script language="javascript">alert("' . $upload . '!");window.history.go(-1);</script>';
			// exit();
            $pesan = $upload;

            $this->session->set_flashdata('message_status', 250);
            $this->session->set_flashdata('message', $pesan);


            redirect('profile/dokumen');
		}



    }

    public function hapus_dokumen()
    {
        $id = $this->input->post('id');
        $file = $this->input->post('file');

        // Lokasi folder upload
        $path = FCPATH . 'uploads/dokumen/' . $file;

        // Hapus file
        if (file_exists($path)) {
            unlink($path);
        }

        // Hapus data dari database
        $this->db->where('id', $id);
        $this->db->delete('dokumen_pegawai');

        echo json_encode(['status' => 'ok']);
    }


    function uraian_tugas(){

        $nip    = $this->session->userdata('nip');
        $id_pegawai   = $this->session->userdata('id_pegawai');

        $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['uraian_tugas']   =  $this->Pegawai_model->getUraianTugas($nip);


        $this->load->view('profile/uraian_tugas', $data);
    }

    function insert_uraian_tugas(){

        $nip    = $this->session->userdata('nip');

        $newarray = array(
            'nip' => $nip,
            'tugas_pokok' => $this->input->post('tugas_pokok'),
            'tugas_integrasi' => $this->input->post('tugas_integrasi'),
            'wewenang' => $this->input->post('wewenang'),
            'tanggung_jawab' => $this->input->post('tanggung_jawab'),
            'created_at' => date('Y-m-d H:i:s')

        );


        $this->db->insert('uraian_tugas', $newarray);

        $this->session->set_flashdata('message_status', 200);
        $pesan =  createMessageInfo('Data uraian tugas berhasil disimpan', 'success');
        $this->session->set_flashdata('message_update', $pesan);

        redirect('profile/uraian_tugas');
    }

    function ajaxEditUraianTugas(){
        $nip   = $this->session->userdata('nip');

        $uraian_tugas   =  $this->Pegawai_model->getUraianTugas($nip);

        echo json_encode($uraian_tugas);

    }

    function update_uraian_tugas(){

        $nip    = $this->session->userdata('nip');

        $newarray = array(

            'tugas_pokok' => $this->input->post('tugas_pokok'),
            'tugas_integrasi' => $this->input->post('tugas_integrasi'),
            'wewenang' => $this->input->post('wewenang'),
            'tanggung_jawab' => $this->input->post('tanggung_jawab'),
            'created_at' => date('Y-m-d H:i:s')

        );

        $this->db->where('nip', $nip);
        $this->db->update('uraian_tugas', $newarray);

        $this->session->set_flashdata('message_status', 200);
        $pesan =  createMessageInfo('Data uraian tugas berhasil disimpan', 'success');
        $this->session->set_flashdata('message_update', $pesan);

        redirect('profile/uraian_tugas');
    }

    function print_uraian_tugas(){
        $nip   = $this->session->userdata('nip');
        $id_pegawai   = $this->session->userdata('id_pegawai');
        $data['uraian_tugas']   =  $this->Pegawai_model->getUraianTugas($nip);


        $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $this->load->view('profile/print_uraian_tugas', $data);
    }

    function update_myprofile(){
         #print_array($this->input->post());

         $this->Profile_model->updateProfileDetail();


        // echo '<span class="text-success"> <i class="uil-check"></i> Data profile pegawai  berhasil diupdate</span>';
                $this->session->set_flashdata('message_status', 200);
        $pesan =  createMessageInfo('Data uraian tugas berhasil disimpan', 'success');
         redirect('profile/my_profile');
    }


    function upload_profile_picture() {



        $nip    = $this->session->userdata('nip');
        $nama   = $this->session->userdata('nama');


        $first_name = strtok($nama, " "); // Test
        $img_name   = $nip.'_'.$first_name;

		$ImageName_temp = createTempImageName($img_name, 'imageupload');
		$ImageName_db   = createImageName($img_name, 'imageupload');

		$upload    	   = $this->Profile_model->do_upload($ImageName_temp);



		if ($upload == '') {
			#delete image exist
			$fileImageName 	= './uploads/photo_profile/' . $ImageName_db;
			if (file_exists($fileImageName) && $nip != '') {
				unlink($fileImageName);
			}
			#delete image temp
			$fileImageName 	= './uploads/photo_profile/' . $ImageName_temp;
			unlink($fileImageName);
			#upload new image
			$this->Profile_model->do_upload($img_name);


            $this->db->where('nip', $nip);
            $this->db->set('photo', $ImageName_db);
            $this->db->update('detail_pegawai');

            $pesan =  createMessageInfo('Profile picture berhasil diubah', 'success');
            $this->session->set_flashdata('message_update', $pesan);

            redirect('profile/my_profile');

		} else {
			// echo '<script language="javascript">alert("' . $upload . '!");window.history.go(-1);</script>';
			// exit();
            $pesan = $upload;

            $this->session->set_flashdata('message_status', 250);
            $this->session->set_flashdata('message', $pesan);


            redirect('profile/my_profile');
		}


    }

    function pelatihan(){
        $nip    = $this->session->userdata('nip');
        $id_pegawai   = $this->session->userdata('id_pegawai');

        $data['pegawai']   =  $this->Pegawai_model->getDetailPegawai($id_pegawai);
        $data['pelatihan']   =  $this->Pegawai_model->getDataPelatihan($nip);


        $this->load->view('profile/pelatihan', $data);
    }


    function upload_sip_str(){
        $nama_user   =  $this->session->userdata('nama');
        $id_pegawai  =  $this->session->userdata('id_pegawai');
        $nip         =  $this->session->userdata('nip');
        $tanggal     =  $this->input->post('tanggal');

        $xplod       = explode(' ', $nama_user);
        $first_name  = $xplod[0];
        $tanggal_dl  = date('Ymd', strtotime($tanggal));

        $jns_dokumen = $this->input->post('jns_dokumen');

        $file_title  = $jns_dokumen.'_'.$first_name.'_'.$tanggal_dl;
        $nama_file   =  $file_title;
        $nama_file  .= $_FILES['filedocs']['name'] ;

        $namaFileCaption = substr($nama_file, 0, -4);
        $namaFileCaption = url_title($namaFileCaption);
        $file_name       = strtolower($namaFileCaption);

        $namaFileDB      =  $file_name.'.pdf';
        $numchar         = strlen($namaFileDB);



        if($numchar > 99){
            $pesan =  createMessageInfo('Upload SIP/STR Gagal, nama file terlalu panjang. Ubah nama file kurang dari 80 karakter', 'danger');
            $this->session->set_flashdata('message', $pesan);
            $this->session->set_userdata($this->input->post());
            redirect('profile/my_profile' );
        }


        $path = 'sip_str';
        $uploadFile = $this->Master_model->uploadFilePDF($path, $file_name, '1000');


        if ($uploadFile) {

            $this->Profile_model->insertSIPSTR($nip, $namaFileDB);
            $pesan =  createMessageInfo(' SIP/STR berhasil disimpan', 'success');
            $this->session->set_flashdata('message', $pesan);

            $this->session->unset_userdata('tanggal');
            $this->session->unset_userdata('keterangan');
            $this->session->unset_userdata('jns_dl');

           redirect('profile/my_profile' );
        } else {

            redirect('profile/my_profile' );

        }


    }

    function delete_sip_str($id, $file_name){
        $path = 'uploads/sip_str/';


        if (file_exists($path.$file_name)) {
            unlink($path.$file_name);

        }

        $this->db->where('id', $id);
        $this->db->delete('tbl_sip_str');


        $pesan =  createMessageInfo('Data SIP/STR telah dihapus', 'success');
        $this->session->set_flashdata('message', $pesan);
        redirect('profile/my_profile' );
    }

    function input_diklat(){

        $nama_user   =  $this->session->userdata('nama');
        $nip         =  $this->session->userdata('nip');
        $tanggal     =  $this->input->post('tanggal');

        $xplod       = explode(' ', $nama_user);
        $first_name  = $xplod[0];

        $jns_dokumen = 'diklat';

        $file_title  = $jns_dokumen.'_'.$first_name;
        $nama_file   =  $file_title;
        $nama_file  .= $_FILES['filedocs']['name'] ;

        $namaFileCaption = substr($nama_file, 0, -4);
        $namaFileCaption = url_title($namaFileCaption);
        $file_name       = strtolower($namaFileCaption);

        $namaFileDB      =  $file_name.'.pdf';
        $numchar         = strlen($namaFileDB);



        if($numchar > 99){
            $pesan          =  'Input pelatihan gagal,  nama file terlalu panjang. Ubah nama file kurang dari 80 karakter';
            $this->session->set_flashdata('message', $pesan);
            $this->session->set_flashdata('message_status', 250);
            $this->session->set_userdata($this->input->post());
            #redirect('profile/my_profile' );
        }


        $path = 'diklat';
        $uploadFile = $this->Master_model->uploadFilePDF($path, $file_name, '1000');


        if ($uploadFile) {

            $this->Profile_model->insertDiklat($nip, $namaFileDB);
            $pesan ='Input pelatihan berhasil disimpan';
            $this->session->set_flashdata('message', $pesan);
            $this->session->set_flashdata('message_status', 200);
            $this->session->unset_userdata('tanggal');
            $this->session->unset_userdata('keterangan');
            $this->session->unset_userdata('jns_dl');

           redirect('profile/pelatihan' );
        } else {

            redirect('profile/pelatihan' );

        }
    }


    function update_pelatihan(){
        $nama_user   =  $this->session->userdata('nama');
        $nip         =  $this->session->userdata('nip');
        $change_file     =  $this->input->post('change_file');
        $id     =  $this->input->post('id');


        $this->Profile_model->updateDataDiklat($nip, $id);

        if($change_file==1){


            $xplod       = explode(' ', $nama_user);
            $first_name  = $xplod[0];

            $jns_dokumen = 'diklat';

            $file_title  = $jns_dokumen.'_'.$first_name;
            $nama_file   =  $file_title;
            $nama_file  .= $_FILES['filedocs']['name'] ;

            $namaFileCaption = substr($nama_file, 0, -4);
            $namaFileCaption = url_title($namaFileCaption);
            $file_name       = strtolower($namaFileCaption);

            $namaFileDB      =  $file_name.'.pdf';
            $numchar         = strlen($namaFileDB);



            if($numchar > 99){
                $pesan =  createMessageInfo('Input pelatihan gagal,  nama file terlalu panjang. Ubah nama file kurang dari 80 karakter', 'danger');
                $this->session->set_flashdata('message', $pesan);
                $this->session->set_userdata($this->input->post());
                #redirect('profile/my_profile' );
            }


            $path = 'diklat';
            $uploadFile = $this->Master_model->uploadFilePDF($path, $file_name, '1000');


                if ($uploadFile) {

                    $this->Profile_model->updateFileDiklat($id, $namaFileDB);
                    $pesan = createMessageInfo(' Input pelatihan berhasil disimpan', 'success');
                    $this->session->set_flashdata('message', $pesan);

                    $this->session->unset_userdata('tanggal');
                    $this->session->unset_userdata('keterangan');
                    $this->session->unset_userdata('jns_dl');

                    redirect('profile/my_profile' );
                } else {

                    redirect('profile/my_profile' );


                }


        }
    }


    function hapus_diklat()
    {
        $id = $this->input->post('id');
        $file = $this->input->post('file');

        // Lokasi folder upload
        $path = FCPATH . 'uploads/diklat/' . $file;

        // Hapus file
        if (file_exists($path)) {
            unlink($path);
        }

        // Hapus data dari database
        $this->db->where('id', $id);
        $this->db->delete('tbl_diklat');

        echo json_encode(['status' => 'ok']);
    }




}
