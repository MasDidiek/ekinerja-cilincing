<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
<?php  $this->load->view('master/meta');?>
<style>
             .datepicker{
                z-index: 1999;
            }

            .input-number{
              background:#FFF;
              border-top:2px solid #EEE;
              padding-top:20px;
            
            }


            .mybtn{
              border:none;
              padding:6px 6px;
              display:inline-block;
              color:#FFF;
              border-radius:4px;
            }
            .btn-save{
              background:#3de78c;
            }

            .btn-save:hover{
              background:#36d17e;
            }
            .btn-cancel{
              background:#fa896b;
            }
            .btn-cancel:hover{
              background:#e96d4c;
            }

            .fake-input{
              border:none;
              width:30px;
              text-align:center;
              font-size:22px;
            }

            small{
              color:#666;
            }
         </style>
</head>

<body>
  <!-- <div class="toast toast-onload align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body hstack align-items-start gap-6">
      <i class="ti ti-alert-circle fs-6"></i>
      <div>
        <h5 class="text-white fs-3 mb-1">Welcome to Modernize</h5>
        <h6 class="text-white fs-2 mb-0">Easy to costomize the Template!!!</h6>
      </div>
      <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div> -->
  <!-- Preloader -->

  <div id="main-wrapper">
    <!-- Sidebar Start -->
    <aside class="left-sidebar with-vertical">
      <div><!-- ---------------------------------- -->
      <!-- Start Vertical Layout Sidebar -->
      <!-- ---------------------------------- -->
      

      <?php $this->load->view('layout/section/sidebar');?>

    </aside>

    <!--  Sidebar End -->
    <div class="page-wrapper">
      <!--  Header Start -->
      <?php $this->load->view('layout/section/header');?>
      <!--  Header End -->


      <div class="body-wrapper">
        <div class="container-fluid">
          <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
                <div class="card-body px-4 py-3">
                  <div class="row align-items-center">
                    <div class="col-9">
                      <h4 class="fw-semibold mb-8">Detail Cuti</h4>
                      <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="../main/index.html" >Home</a>
                          </li>
                         
                           <li> &nbsp; / &nbsp; </li>
                          
                          <li class="breadcrumb-acive">Detail Cuti</li>
                        </ol>
                      </nav>
                    </div>
                    <div class="col-3">
                      <div class="text-center mb-n5">

                        
                      </div>
                    </div>
                  </div>

                  
                </div>
              </div>
              <?php 
                    
                
                #echo $usergroup;
                $tgl_dari   =  $detail_cuti[0]->tgl_dari;
                $tgl_sampai =  $detail_cuti[0]->tgl_sampai;
                $hari_cuti  =  $detail_cuti[0]->hari_cuti;
                $status  =  $detail_cuti[0]->status;
                $id_cuti  =  $detail_cuti[0]->id;
                $id_pegawai =  $detail_cuti[0]->id_pegawai;

                $tgl_pengajuan  =  $detail_cuti[0]->tgl;
                $id_pengganti  =  $detail_cuti[0]->id_pengganti;
                $delegasi_tugas  =  $detail_cuti[0]->delegasi_tugas;

                $tgl_check2  =  $detail_cuti[0]->tgl_check2;

                if($tgl_check2 != null){
                  $approve_date = format_full($tgl_check2);
                }else{
                  $approve_date = '';
                }

                $jns_cuti  =  $detail_cuti[0]->jns_cuti;
                if($jns_cuti==1){
                  $jenis_cuti = 'Tahunan';
                }else if($jns_cuti==2){
                  $jenis_cuti = 'Cuti Bersalin';
                }else if($jns_cuti==3){
                  $jenis_cuti = 'Cuti Alasan Penting';
                }else if($jns_cuti==4){
                  $jenis_cuti = 'Cuti Sakit';
                }else if($jns_cuti==5){
                  $jenis_cuti = 'Cuti Besar';
                }else{
                  $jenis_cuti = 'Cuti Bersalin Anak 3';
                }
              
                $message = $this->session->flashdata('message'); 

               
                  
                  if($hari_cuti==1){
                      $tgl_cuti = '<span class="text-dark">'.format_full($tgl_dari).'</span>';
                  }else{
                      $tgl_cuti = '<span class="text-dark">'.format_full($tgl_dari).' </span> &nbsp;&nbsp;&nbsp; s/d &nbsp;&nbsp;&nbsp;<span class="text-dark">'.format_full($tgl_sampai).'</span>';
                  }

                  
                  $flagStatus = getStatusCuti($status);


                  $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);
                  $id_pj = $detail_pegawai[0]->id_validator;
                  $nama = $detail_pegawai[0]->nama;
                  $jns_pegawai = $detail_pegawai[0]->jns_pegawai;
                  $jabatan = $detail_pegawai[0]->jabatan;
                  $puskesmas = $detail_pegawai[0]->puskesmas;
                  $jns_pegawai = $detail_pegawai[0]->jns_pegawai;
                  
                  

                  $detail_pegawai_pengganti = $this->Pegawai_model->getDetailPegawai($id_pengganti);
                  $jabatan_pengganti = $detail_pegawai_pengganti[0]->jabatan;
                  $puskesmas_pengganti = $detail_pegawai_pengganti[0]->puskesmas;
                 #print_array($detail_pegawai);


                  $delegasi = explode("+", $delegasi_tugas);
      
                    echo $message;
                
               ?>

                   
              <div class="row">
                <div class="col-lg-4 d-flex align-items-stretch">
                  <div class="card w-100">
                    <div class="card-body p-4">
                       
                        <h5>Cuti pegawai</h5>


                        <div class="mt-4">
                         <label for="">Tanggal Pengajuan</label>
                         <h6><?php echo format_full($tgl_pengajuan) ;?></h6>
                      </div>
                      
                      <div class="mt-4">
                         <label for="">Tanggal Cuti</label>
                         <h6><?= $tgl_cuti;?></h6>
                      </div>

                      
                      <div class="mt-4">
                         <label for="">Hari Cuti</label>
                         <h6> <?php echo  $detail_cuti[0]->hari_cuti ;?> Hari</h6>
                      </div>

                      
                      <div class="mt-4">
                         <label for="">Jenis Cuti</label>
                         <h6> <?php echo $jenis_cuti;?></h6>
                      </div>

                      
                      <div class="mt-4">
                         <label for="">Alasan Cuti</label>
                         <h6><?php echo $detail_cuti[0]->alasan_cuti ;?></h6>
                      </div>

                      
                      <div class="mt-4">
                         <label for="">Alamat Selama Cuti</label>
                         <h6><?php echo $detail_cuti[0]->alamat_cuti ;?></h6>
                      </div>

                      <div class="mt-4">
                         <label for="">Status Cuti</label><br><br>
                        <?php
                          if($status=='PEND0'){
                            echo '<span class="bg-warning-subtle p-2 text-warning">PENDING - menunggu persetujuan  pegawai pengganti</span>
                            <br><br>';
                          }else if($status=='PEND1'){
                            if($jns_pegawai=='pns' || $id_validator== $detail_pegawai[0]->id_validator){
                              echo '<span class="bg-warning-subtle p-2 text-warning">PENDING - menunggu persetujuan Kasubbag TU</span>
                              <br><br>';
                            }else{
                              echo '<span class="bg-warning-subtle p-2 text-warning">PENDING - menunggu persetujuan  Ka. Pustu/Kasatpel UKM-UKP</span>
                              <br><br>';
                            }
                           
                          }else if($status=='PEND2'){
                            echo '<span class="bg-info-subtle p-2 text-info">PENDING - menunggu persetujuan Kasubbag TU</span>
                            <br><br>';
                          }else if($status=='PEND3'){
                            echo '<span class="bg-info-subtle p-2 text-info">PENDING - menunggu persetujuan Ka. Puskesmas</span>
                            <br><br>';
                          }else if($status=='APPROVE'){
                            echo '<span class="bg-success-subtle p-2 text-success">APPROVE - Cuti telah disetujui</span>
                            <br><br>';
                          }else if($status=='CANCEL'){
                            echo '<span class="bg-danger text-white p-2">CANCELED - Cuti telah dibatalkan</span>
                            <br><br>';
                          }
                          ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-8 d-flex align-items-stretch">
                  <div class="card w-100">
                    <div class="card-body">
                       
                        <div class=" text-info  fs-6">Pegawai Pemohon Cuti</div>
                    
                        <h5> <?php echo $nama;?></h5>
                        <strong><?php echo  $detail_pegawai[0]->nip;?></strong><br>
                        <?php echo  $jabatan ;?> @ <?php echo $puskesmas;?>


                        <div class="text-warning mt-4 fs-6">Pegawai Pengganti Cuti</div>
                        
                        <h5><?php echo $detail_pegawai_pengganti[0]->nama ;?></h5>
                        <strong><?php echo  $detail_pegawai_pengganti[0]->nip;?></strong><br>
                        <?php echo  $jabatan_pengganti ;?> @ <?php echo $puskesmas_pengganti;?>


                        <div class="text-primary mt-4 fs-6">Pendelegasian Tugas</div>
                        <p>
                          <ul>
                            <?php
                            for ($i=0; $i < count($delegasi) ; $i++) { 
                            echo ' <li> &nbsp;'.($i+1).'.&nbsp;  '.$delegasi[$i].'</li>
                                ';
                            }
                          ?>
                          </ul>
                        </p>


                        <br><br>

                        <?php if($status != 'CANCEL'){ ?>
                          <a href="<?php echo base_url();?>admin/pegawai/cancel_cuti/<?php echo $id_cuti;?>" class="btn btn-danger" onClick="return confirm('Batalkan cuti ini?');">Batalkan Cuti</a>

                       <?php  } ?>
                       
                          
                    </div>
                  </div>
                </div>
              </div>
           

      <script>
          function handleColorTheme(e) {
            $("html").attr("data-color-theme", e);
            $(e).prop("checked", !0);
          }
        </script>

        <?php $this->load->view('layout/section/theme-setting.php');?>

        <?php $this->load->view('master/request-cuti.php');?>

  </div>
  <div class="dark-transparent sidebartoggler"></div>
  <!-- Import Js Files -->

    <script src="<?php echo LIBS_JS_PATH;?>jquery/dist/jquery.min.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>app.min.js"></script>
    <script src="../assets/js/app.init.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>simplebar/dist/simplebar.min.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>sidebarmenu.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>theme.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>init.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>jquery.blockUI.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>block-ui.js"></script>


    <script src="<?php echo NEW_JS_PATH;?>prettify.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>jquery.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>bootstrap-datepicker.js"></script>

</body>




<script>
 

</script>
</html>