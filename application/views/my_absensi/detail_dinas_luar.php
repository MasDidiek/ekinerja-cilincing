<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD4ULw0TXRCvk5YaDkVBUdUwDZXLMs8opc&callback=initMap"
          type="text/javascript"></script>


   <style>
      #map {
            height: 400px;
            width: 100%;
            margin: 0 auto;
    
          }
   </style>

<script>
     
     function initMap() {
        var cilincing = {lat: <?php echo $pengajuan_dinas_luar[0]->lat; ?>, lng:<?php echo $pengajuan_dinas_luar[0]->lon; ?>};
         var map = new google.maps.Map(document.getElementById('map'), {
         center: cilincing,
         zoom: 15
       });

   
                     
       var marker0 = new google.maps.Marker({
       position: {lat: <?php echo $pengajuan_dinas_luar[0]->lat; ?>, lng:<?php echo $pengajuan_dinas_luar[0]->lon; ?>},
       map: map,
       title: 'Lokasi'
         });

     
       marker0.addListener('click', function() {
         infowindow0.open(map, marker0);
       });
       
       
         var contentString0 = '';

             var infowindow0 = new google.maps.InfoWindow({
           content: contentString0  
           });
       
     }
</script>


<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
            <?php $this->load->view('layout/section/sidebar');?>
            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
                    <?php $this->load->view('layout/section/topbar');?>

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Main Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php
                                       $message = $info= $this->session->flashdata('message');
                                 
                                              
                                      $id_pegawai = $this->session->userdata('id_pegawai'); 
                                  
                                      $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);
                                      $id_pj       = $detail_pegawai->id_validator;
                                      $nama        = $detail_pegawai->nama;
                                      $jns_pegawai = $detail_pegawai->jns_pegawai;
                                      $jabatan     = $detail_pegawai->jabatan;
                                      $puskesmas   = $detail_pegawai->puskesmas;
                                      $jns_pegawai = $detail_pegawai->jns_pegawai;
                        

                                      $id = $pengajuan_dinas_luar[0]->id;
                                      $id_pegawai = $pengajuan_dinas_luar[0]->id_pegawai;
                                      $tgl = $pengajuan_dinas_luar[0]->tanggal;
                                      $create_at = $pengajuan_dinas_luar[0]->create_at;
                                      $jns_dl = $pengajuan_dinas_luar[0]->jns_dl;
                                      $photo = $pengajuan_dinas_luar[0]->photo;
                                      $keterangan = $pengajuan_dinas_luar[0]->keterangan;
                                      $latitude = $pengajuan_dinas_luar[0]->lat;
                                      $longitude = $pengajuan_dinas_luar[0]->lon;
                                      $status = $pengajuan_dinas_luar[0]->status;
                                      $surtug = $pengajuan_dinas_luar[0]->surtug;
                      
                      
                                      if ($jns_dl == 'DLP') {
                                          $dl_name = '<span class="badge  bg-primary-subtle text-primary">DL - PENUH</span>';
                                      } else if ($jns_dl == 'DLA') {
                                          $dl_name = '<span class="badge  bg-warning-subtle text-warning">DL - AWAL</span>';
                                      } else {
                                          $dl_name = '<span class="badge  bg-success-subtle text-success">DL -  AKHIR</span>';
                                      }
                      
                                      if($status==0){
                                        $classBg =  createClassBadge('warning');
                                        $status_dl = ' Pending';
                                      }else if($status==1){
                                        $classBg =  createClassBadge('success');
                                        $status_dl = '  Disetujui';
                                      }else{
                                        $classBg =  createClassBadge('danger');
                                        $status_dl = 'Ditolak';

                                      }
                      
                                      $path   = 'uploads/surat_tugas/';
                            
                                        
                                ?>



<div class="row">
                            <div class="col-xxl-8 col-lg-6">
                                <!-- project card -->
                                <div class="card d-block">
                                    <div class="card-body">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="dripicons-dots-3"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Edit</a>
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Delete</a>
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-email-outline me-1"></i>Invite</a>
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-exit-to-app me-1"></i>Leave</a>
                                            </div>
                                        </div>
                                        <!-- project title-->
                                        <h3 class="mt-0">
                                        <?php echo $keterangan ;?>
                                        </h3>
                                        <div class="badge <?php echo $classBg;?>  mb-3"><?php echo $status_dl ;?></div>

                                        </p>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <h5>Waktu Pengajuan</h5>
                                                    <p><?php echo format_full($create_at);?> 8 <small class="text-muted"> <?php echo date('H:i:s', strtotime($create_at));?></small></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <h5>Tanggal Dinas Luar</h5>
                                                    <p><?php echo format_hari($tgl);?>, <?php echo format_full($tgl);?></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <h5>Jenis DL</h5>
                                                    <p><?php echo $dl_name ;?></p>
                                                </div>
                                            </div>
                                        </div>


                                    </div> <!-- end card-body-->
                                    
                                </div> <!-- end card-->

                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="mt-0 mb-3">Action</h4>

                                     
                                        <div class="text-center mt-2">
                                        <a href="<?php echo  base_url().'absensi/delete_pengajuan_dl/' . $id . '/' . $surtug ;?>" class="btn btn-danger btn-sm" title="Batalkan dinas luar " onClick="return confirm('Apakah anda yakin untuk membatalkan dinas luar ini?');"> 
                                          <i class="uil-trash-alt"></i> Batalkan</a> 
                                        </div>
                                    </div> <!-- end card-body-->
                                </div>
                                <!-- end card-->
                            </div> <!-- end col -->

                            <div class="col-lg-6 col-xxl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Photo</h5>
                                        <div dir="ltr">
                                        <img src="<?php echo base_url();?>uploads/photo_dinas_luar/thumb/<?php echo $photo;?>" width="95%">
                                        </div>
                                    </div>
                                </div>
                                <!-- end card-->

                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Lokasi</h5>

                                        <div id="map" class="border p-2 mt-4" style="width: 100%;"></div>



                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                       
                     </div>
                  </div> <!-- container -->
                </div> <!-- content -->

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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   
            

    </body>
</html>
