<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/css/snackbar.css" rel="stylesheet" type="text/css" />


<style>
       .card-body img{
        max-height:100px;
        width: 100px;
       }

       .border-bawah-success{
          border:1px solid #EEE;
       }

       .border-bawah-danger{
          border:1px solid #EEE;
       }

       .rounded-10{
        border-radius:10px;
        font-size:12px
       }

</style>


    <body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
            <?php $this->load->view('layout/section/sidebar');?>
            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
                    <?php $this->load->view('layout/section/topbar');?>
                    <?php
                    //print_array($rekap_capaian_kinerja);
       
                    $id_pegawai_session = $this->session->userdata('id_pegawai'); 
                    $usergroup = $this->session->userdata('usergroup'); 
                  
                  //  $sisaCuber = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 3, 'DESC');
             
                   //  $sisaCutiAll = $sisaTahunLalu+$sisaTahunIni+$sisaCuber;
                   //  $arrayHakCuti = array('Sisa Cuti tahun lalu', 'Hak Cuti tahun ini', 'Hak Cuti Bersama');
                   //  $arraySisaCuti = array($sisaTahunLalu, $sisaTahunIni, $sisaCuber );
             
             
                       
             
                         //print_array($detail_cuti);
                            $id   =  $detail_cuti[0]->id;
                             $tgl_dari   =  $detail_cuti[0]->tgl_dari;
                             $tgl_sampai =  $detail_cuti[0]->tgl_sampai;
                             $hari_cuti  =  $detail_cuti[0]->hari_cuti;
                             $status  =  $detail_cuti[0]->status;
                             $id_cuti  =  $detail_cuti[0]->id;
                             $id_pegawai =  $detail_cuti[0]->id_pegawai;
             
                             $tgl_pengajuan  =  $detail_cuti[0]->tgl;
                             $id_pengganti  =  $detail_cuti[0]->id_pengganti;
                             $delegasi_tugas  =  $detail_cuti[0]->delegasi_tugas;
             
                             $file_image =  $detail_cuti[0]->file_image;
             
                             $tgl_check  =  $detail_cuti[0]->tgl_check;
                             $tgl_check2  =  $detail_cuti[0]->tgl_check_ktu;

                             $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);
                              //print_array($detail_pegawai);
                              $id_pj       = $detail_pegawai[0]->id_validator;
                              $nama        = $detail_pegawai[0]->nama;
                              $nip        = $detail_pegawai[0]->nip;
                              $jns_pegawai = $detail_pegawai[0]->jns_pegawai;
                              $id_jabatan = $detail_pegawai[0]->id_jabatan;
                              $jabatan     = $detail_pegawai[0]->jabatan;
                              $puskesmas   = $detail_pegawai[0]->puskesmas;
                              $jns_pegawai = $detail_pegawai[0]->jns_pegawai;
                              $jns_jam_kerja = $detail_pegawai[0]->jns_jam_kerja;
                              $rumpun_kerja = strtolower($detail_pegawai[0]->rumpun_kerja);
                              $keterangan_jabatan = $detail_pegawai[0]->keterangan_jabatan;
             
                             //$status_cuti =  getStatusCuti($status);
                              $btn_acc = '';
                              $acc_pengganti = false;
                              $acc_kapustu = false;
                              $acc_ktu = false;
                              $ket_acc = 'Menunggu persetujuan pengganti cuti';
                              $canceled = false;
             
                              $approve_date_pengganti = '';
                              $status_cuti = '<span class="font-medium p-2 rounded border bg-orange-100 border-orange-100 text-orange-500 dark:bg-slate-400/20 dark:border-transparent">
                              PENDING
                              </span>';
             
                             
                             if($status=='PEND1'){
                                $acc_pengganti = true;
                                $approve_date_pengganti = date('d, M Y', strtotime($tgl_check));
                                $approve_date = '';
                                $approve_date_ktu = '';
                                $ket_acc = 'Menunggu persetujuan Kapustu/Kasatpel';
                                $status_cuti = '<span class="text-warning">
                                PENDING
                                </span>';

                                

                                if($rumpun_kerja == 'admen'){
                                    $btn_acc = '<button class="btn btn-danger float-end ms-1"   value="'.$id.'" data-bs-toggle="modal" data-bs-target="#reject-modal"><i class="uil-info-circle"></i> Tolak</button>
                                                <button class="btn btn-success float-end btn-acc"  value="'.$id.'" data-bs-toggle="modal" data-bs-target="#acc-modal"><i class="uil-check-circle"></i> Setujui</button>';
                                }
                                
             
                             }else if($status == 'PEND2'){
                               $acc_kapustu = true;
                               $acc_pengganti = true;
                               $approve_date = date('d, M Y', strtotime($tgl_check));
                               $approve_date_pengganti = date('d, M Y', strtotime($tgl_check));
                               $approve_date_ktu = '';
                               $ket_acc = 'Menunggu persetujuan Ka.Subbag TU';
                               $status_cuti = '<span class="text-warning">
                               PENDING
                               </span>';

                               $btn_acc = '<button class="btn btn-danger float-end ms-1"  value="'.$id.'" data-bs-toggle="modal" data-bs-target="#reject-modal"><i class="uil-info-circle"></i> Tolak</button>
                               <button class="btn btn-success float-end btn-acc"  value="'.$id.'" data-bs-toggle="modal" data-bs-target="#acc-modal"><i class="uil-check-circle"></i> Setujui</button>';
             
                             }elseif($status == 'PEND3' || $status == 'APPROVE'){
                               $acc_ktu = true;
                               $acc_kapustu = true;
                               $acc_pengganti = true;
                               $approve_date = date('d, M Y', strtotime($tgl_check));
                               $approve_date_ktu = date('d, M Y', strtotime($tgl_check2));
                               $ket_acc = 'Cuti telah disetujui';
                               $status_cuti = '<span class="text-success">APPROVED </span>';
             
                             }elseif($status == 'CANCEL'){
                               $acc_ktu = true;
                               $ket_acc = 'Cuti telah dibatalkan';
                               $approve_date = '';
                               $approve_date_ktu = '';
                               $status_cuti = '<span class="text-danger">CANCELED </span>';
                               $canceled = true;
             
                             }else{
                               $approve_date = '';
                               $approve_date_ktu = '';
                              // $ket_acc = 'Cuti dibatalkan';
                             }
             
                           
             
             
             
                             $tgl_pengajuan  =  $detail_cuti[0]->tgl;
                             $tgl_check_pengganti  =  $detail_cuti[0]->tgl_cek;
             
                                           
                             $sisaTahunLalu = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 2, 'DESC');
                             $sisaTahunIni = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 4, 'DESC');
             
                             $jns_hak_cuti  =  $detail_cuti[0]->jns_hak_cuti;
             
                             if($jns_hak_cuti==2){
                                 $hakCuti = 'Sisa Cuti tahun 2024';
                                 $sisaCuti =  $sisaTahunLalu;
                             }else{
                                 $hakCuti = 'Sisa Cuti tahun 2025';
                                 $sisaCuti = $sisaTahunIni ;
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
             


                             
             
                             $pin          = substr($nip, -4);
             
                             $detail_pegawai_pengganti = $this->Pegawai_model->getDetailPegawai($id_pengganti);
                             $jabatan_pengganti        = $detail_pegawai_pengganti[0]->jabatan;
                             $puskesmas_pengganti      = $detail_pegawai_pengganti[0]->puskesmas;
                             #print_array($detail_pegawai);
                             $delegasi = explode("+", $delegasi_tugas);
                             
                             if($file_image !=''){
                                 $filePenunjang = explode(",", $file_image);
                             }else{
                                   $filePenunjang = array('');
                             }
             
                             $photoPengaju   = $this->Pegawai_model->getPhotoPegawai($detail_pegawai[0]->nip);
                             $photoPengganti = $this->Pegawai_model->getPhotoPegawai($detail_pegawai_pengganti[0]->nip);
             
                             // echo $photoPengaju;
             
                             $listBulan = array_bulan();
             
             
                             $div_upload_file = 'd-none';
             
                            
             
                             $cutiPegawai   = $this->Cuti_model->getHistoryCutiPegawai($id_pegawai);
                             $listHariPegawai   = $this->Cuti_model->getlisthariCutiPegawai($id_pegawai);
                       
             
                             $message = $this->session->flashdata('message'); 

                          


                            
                     ?>


                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Pegawai</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Detail Pegawai </h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="toastBox "></div>

                        <div id="acc-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="standard-modalLabel">Persetujuan Cuti</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                    </div>
                                    <div class="modal-body fs-4" id="confirm-title">
                                  
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="id_cuti" value="" class="btn btn-success confirm-acc-cuti ">Iya, Setujui</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                         <div class="row">


                                <div class="col-sm-8">

                                   <div class="row">
                            
                                        <div class="col-xl-6">
                                            <div class="card border-bawah-danger">
                                                <div class="card-body ">
                                                <h4 class="text-danger">Pegawai yang mengajukan cuti</h4>
                                                    <div class="d-flex align-items-center">
                                                        <div class="w-100 overflow-hidden">
                                                        <h3 class="mt-0"><?php echo $nama;?></h3>
                                                            <h5 class="m-0 fw-normal cta-box-title"><?php echo $jabatan;?>    - 
                                                            <b><?php echo $keterangan_jabatan;?></b> <i class="mdi mdi-arrow-right-bold-outline"></i></h5>
                                                        </div>
                                                        
                                                        <img src="<?php echo base_url().'uploads/photo_profile/'. $photoPengaju ;?>" alt="" class="rounded-circle img-thumbnail">
                                                    
                                                    </div>
                                                </div>
                                                <!-- end card-body -->
                                            </div>
                                            <!-- end card-->
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="card border-bawah-success">
                                                <div class="card-body">
                                                <h4 class="text-success"> Pengganti Cuti</h4>
                                                    <div class="d-flex align-items-center">
                                                        <div class="w-100 overflow-hidden">
                                                            <h3 class="mt-0"><?php echo $detail_pegawai_pengganti[0]->nama;?></h3>
                                                            <h5 class="m-0 fw-normal cta-box-title"><?php echo $jabatan_pengganti;?> <b><?php echo $detail_pegawai_pengganti[0]->keterangan_jabatan;?></b>  <i class="mdi mdi-arrow-right-bold-outline"></i></h5>
                                                        </div>
                                                        <img src="<?php echo base_url().'uploads/photo_profile/'. $photoPengganti ;?>" alt="" class="rounded-circle img-thumbnail">
                                                    
                                                    </div>
                                                </div>
                                                <!-- end card-body -->
                                            </div>

                                        </div> <!-- end col-->


                                        <div class="col-xl-12">
                                            <div class="card">
                                                <div class="card-body">
                                                 
                                                            <div class="mb-4">
                                                                <h4 class="text-15 grow">Detail Cuti</h4>
                                                             
                                                            </div>
                                                                <div class="row">
                                                                     <div class="col-md-6">
                                                                        <table class="table table-sm">
                                                                       
                                                                            <tr>
                                                                                <td>Jenis Cuti</td>
                                                                                <td class="text-end fw-bold"><?php echo $jenis_cuti;?> </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>  Hak Cuti yang digunakan  </td>
                                                                                <td  class="text-end fw-bold"><?php echo str_replace("Sisa", "",$hakCuti);?> </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td> <?php echo $hakCuti;?>   </td>
                                                                                <td  class="text-end fw-bold"><?php echo $sisaCuti;?></strong>  hari</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        
                                                                   
                                                                            <table class="table table-sm">
                                                                                <tbody>
                                                                              
                                                                                
                                                                                    <tr>
                                                                                        <td>
                                                                                        Tanggal Pengajuan
                                                                                        </td>
                                                                                        <td class="text-end fw-bold"><?php echo format_full($tgl_pengajuan);?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                        Tanggal Mulai
                                                                                        </td>
                                                                                        <td  class="text-end fw-bold"><?php  echo getNamahari($tgl_dari).', '.format_full($tgl_dari); ?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                        Tanggal Selesai
                                                                                        </td>
                                                                                        <td  class="text-end fw-bold"><?php  echo getNamahari($tgl_sampai).', '.format_full($tgl_sampai);?></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                        Jumlah Hari Cuti
                                                                                        </td>
                                                                                        <td  class="text-end fw-bold"><?php echo $hari_cuti;?> hari</td>
                                                                                    </tr>
                                                                                    <tr class="font-semibold">
                                                                                        <td>
                                                                                        Alasan Cuti
                                                                                        </td>
                                                                                        <td  class="text-end fw-bold"><?php echo $detail_cuti[0]->alasan_cuti;?></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>

                                                                            <button  type="button" class="btn btn-light float-end" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm">Ubah Tanggal Cuti</button>
                                                                    </div>
                                                                    <div class="col-md-12 text-center">
                                                                      <a href="<?php echo base_url();?>admin/pengajuan_cuti/cancel_cuti/<?php echo  $detail_cuti[0]->id.'/'. $detail_cuti[0]->status.'/'.$pin;?>" onClick="return confirm ('Apakah anda ingin membatalkan cuti ini?')" class="btn btn-light text-danger float-start"><i class="uil-trash"></i> Batalkan Cuti</a>
                                                                      
                                                                       <?php echo $btn_acc;?>
                                                                      

                                                                        
                                                                        <div class="clearfix"></div>
                                                                    </div>

                                                                   
                                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div> <!-- end col-->

                                <!-- Small modal -->
                                   
                                    <div class="modal fade" id="bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="mySmallModalLabel">Ubah Tanggal Cuti</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Date Range -->
                                                     <form action="<?php echo base_url();?>admin/pengajuan_cuti/update_tanggal_cuti/<?php echo $id_cuti;?>" method="post">
                                                        <div class="mb-3">
                                                            <label class="form-label">Pilih Tanggal</label>
                                                            <input type="text" class="form-control date" name="tgl_cuti" value="<?php echo format_slash($tgl_dari).' - '.format_slash($tgl_sampai);?>" id="singledaterange" data-toggle="date-picker" data-cancel-class="btn-warning">
                                                        </div>

                                                        <button type="submit" class="btn btn-success float-end">Simpan Perubahan</button>

                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->


                            <div class="col-xl-4">
                                 <div class="card">
                                    <div class="card-body">
                                    <a href="" class="btn btn-light float-end"><i class="uil-print"></i> Print Surat Cuti</a>
                                        <h4>Status Cuti</h4>

                                        <div class="float-start"><?php echo $status_cuti;?></div><br>
                                        <small><?php echo $ket_acc ;?></small>
                                        <div class="clearfix"></div>

                                        <hr>

                                        <div data-simplebar="" style="max-height: 419px;"> 
                                            <div class="timeline-alt pb-0">
                                                <div class="timeline-item">
                                                    <i class="mdi mdi-upload bg-info-lighten text-info timeline-icon"></i>
                                                    <div class="timeline-item-info">
                                                        <a href="#" class="text-info fw-bold mb-1 d-block">Pengajuan Cuti</a>
                                                        <small>Cuti diajukan oleh pegawai.</small>
                                                        <p class="mb-0 pb-2">
                                                            <small class="text-muted"><?php echo date('d, M Y', strtotime($tgl_pengajuan));?></small>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="timeline-item">
                                                    <i class="mdi mdi-airplane bg-primary-lighten text-primary timeline-icon"></i>
                                                    <div class="timeline-item-info">
                                                        <a href="#" class="text-primary fw-bold mb-1 d-block">Acc Pengganti</a>
                                                        <small>Cuti disetujui oleh pengganti
                                                            <span class="fw-bold"><?php echo $detail_pegawai_pengganti[0]->nama;?></span>
                                                        </small>
                                                        <p class="mb-0 pb-2">
                                                            <small class="text-muted"><?php echo $approve_date_pengganti;?></small>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="timeline-item">
                                                    <i class="mdi mdi-check bg-info-lighten text-info timeline-icon"></i>
                                                    <div class="timeline-item-info">
                                                        <a href="#" class="<?php echo  ($acc_ktu) ? "text-success" : "text-secondary"; ?> fw-bold mb-1 d-block">Acc Kapustu/Kasatpel</a>
                                                        <small>Cuti disetujui oleh Kapustu/Kasatpel </small>
                                                        <p class="mb-0 pb-2">
                                                            <small class="text-muted"><?php echo $approve_date;?></small>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="timeline-item">
                                                    <i class="mdi mdi-check bg-primary-lighten text-primary timeline-icon"></i>
                                                    <div class="timeline-item-info">
                                                        <a href="#" class="<?php echo  ($acc_ktu) ? "text-success" : "text-secondary"; ?> fw-bold mb-1 d-block">Acc  Ka. Subbag TU</a>
                                                        <small>
                                                            <span class="fw-bold">Cuti disetujui oleh Kasubag TU</span>
                                                        </small>
                                                        <p class="mb-0 pb-2">
                                                            <small class="text-muted"><?php echo $approve_date_ktu;?></small>
                                                        </p>
                                                    </div>
                                                </div>

                                             
                                            </div>
                                            <!-- end timeline -->
                                        </div> <!-- end slimscroll -->

                                        
                                              <hr>
                                                <div class="mb-2">
                                                    <h5 class="text-15 grow">Delegasi Tugas</h5>
                                                    
                                                </div>
                                                <div class="flex gap-4">
                                                    <div class="shrink-0">
                                                        <img src="assets/images/delivery-1.png" alt="" class="h-10">
                                                    </div>
                                                    <div class="grow">
                                                    <ol>
                                                            <?php
                                                            for ($i=0; $i < count($delegasi) ; $i++) {
                                                                echo '<li>  -  &nbsp;  '.$delegasi[$i].'</li>
                                                                ';
                                                            }
                                                        ?>
                                                    </ol>
                                                    </div>
                                                </div>
                                        </div>

                                     </div>
                                    
                                 </div>

                            </div>


                         </div>
                         <!-- end row -->


                         <div class="row">
                             <div class="col-xl-6 col-xxl-4">
                                 <!-- Personal-Information -->


                             </div> <!-- end col-->
                         </div>
                         <!-- end row -->



                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <script>document.write(new Date().getFullYear())</script> © Hyper - Coderthemes.com
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-end footer-links d-none d-md-block">
                                    <a href="javascript: void(0);">About</a>
                                    <a href="javascript: void(0);">Support</a>
                                    <a href="javascript: void(0);">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>


        </div>
        <!-- END wrapper -->
        <?php $this->load->view('layout/section/theme-setting');?>


        <!-- bundle -->
        <script src="<?php echo base_url();?>assets/new/js/vendor.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/app.min.js"></script>

        <!-- Todo js -->
        <script src="<?php echo base_url();?>assets/new/js/ui/component.todo.js"></script>


        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->



            <script>
                    const toastBox = document.querySelector('.toastBox');
                    const successMsg =
                    '<i class="uil-check"></i> <strong> Success!!  </strong> &nbsp; Pengajuan cuti berhasil disetujui ';
                    const errorMsg =
                    '<i class="fas fa-times-circle"></i> Please fix the error ! ';
                   
                    function showToast(message, type) {
                            const toast = document.createElement('div');
                            toast.classList.add('toast1', type);
                            toast.innerHTML =
                                '<button class="close-btn">X</button>'
                                                            + message;
                            toastBox.appendChild(toast);

                            const closeButton =
                                    toast.querySelector('.close-btn');
                                    closeButton.addEventListener('click', () => {
                                        toast.remove();
                            });

                            setTimeout(() => {
                                toast.remove();
                            }, 5000);
                    }




                


                    $(".btn-acc").click(function(){
                        var id = $(this).val();

                        $("#modal_heading").html("Persetujuan Cuti");
                        $("#confirm-title").html("Apakah anda ingin menyetujui Cuti ini?");
                        $("#id_cuti").val(id);
                    

                    });

            
                    var status = '<?php echo $status;?>';

                    $(".confirm-acc-cuti").click(function(){
                        var id = $(this).val();
                        $(this).hide();
                        $(".cancel-btn").hide();
                        $(".loading-btn").show();

                        $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>admin/pengajuan_cuti/setujui_cuti",
                            data:"id_cuti="+id+"&status="+status,
                                success:function(msg){
                                   
                                    showToast(msg, 'success');
                                    setTimeout(function(){
                                        window.location.reload();
                                        }, 2000);
                                }

                            });

                    });
                
            </script>

    </body>
</html>
