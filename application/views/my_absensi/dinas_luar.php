<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
   

    <style>
        .inputfile {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .inputfile + label {
                font-weight: 700;
                color: #fff;
                background-color: #3095b2;
                border-color: #2e8ca7;
                border-radius:5px;
                display: inline-block;
                cursor: pointer; 
                padding: .45rem .9rem;
                font-size: .9rem;
                transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;
            }

            .inputfile:focus + label,
            .inputfile + label:hover {
                background-color:rgb(21, 124, 152);
            }

            #preview{
                width: 100%;
                height:auto;
            }

            #preview img{
                width: 100%;
                height:auto;
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

                    <!-- Start Content-->
                    <div class="container-fluid">

                       
                        <?php
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
                                            <li class="breadcrumb-item active">Dinas Luar</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dinas Luar</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php

                                    $list_bulan = array_bulan();

                                    $periode_bulan = $this->session->userdata('periode_bulan'); 
                                    $periode_tahun = $this->session->userdata('periode_tahun'); 
                                    $id_pkm_sess   = $this->session->userdata('id_pkm');

                                  
                                    $bulan = $periode_bulan;
                                    $tahun = $periode_tahun;
                                

                                  
                                   $id_pegawai = $this->session->userdata('id_pegawai');
                                   
                                   $nama_user =  $this->session->userdata('nama');
                                   $nip_user =  $this->session->userdata('nip');
                                   $id_pegawai =  $this->session->userdata('id_pegawai');
                                   $photo = $this->Pegawai_model->getPhotoPegawai($nip_user);


                                   //$pin 	= substr($nip_user, -4);

                                   $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);


                                   //print_array($detail_pegawai);

                                   $jabatan =  $detail_pegawai->jabatan;
                                   $puskesmas =  $detail_pegawai->puskesmas;
                                    $jabatan =  $detail_pegawai->pin;


                               ?>



                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body">
                                        <span class="float-start m-2 me-4">
                                            <img src="<?php echo base_url();?>uploads/photo_profile/<?php echo $photo ;?>" style="height: 100px; Width:100px" alt="" class="rounded-circle img-thumbnail">
                                        </span>
                                        <div class="">
                                            <h4 class="mt-1 mb-1"><?php echo  $nama_user ;?></h4>
                                              <p class="font-13"> <?php echo $nip_user;?></p>

                                            <strong class="font-13"> <?php echo $puskesmas;?></strong>
                                              <p class="font-13"> <?php echo $jabatan;?></p>
                                        </div>

                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" class="dropdown-item">Refresh Report</a>
                                                <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                                            </div>
                                        </div>
                                        <h4 class="header-title">Dinas Luar</h4>
                                        <br><br>

                                        <button type="button" class="btn btn-primary float-end"  data-bs-toggle="modal" data-bs-target="#scrollable-modal">
                                            Input Dinas Luar
                                            </button>
                                            <div class="clearfix"></div>
                                      
                                        <div class="table-responsive mt-3">
                                            <table class="table table-sm mb-0 table-bordered">
                                                <thead>
                                                    <tr>
                                                        
                                                            <th class="text-center">No.</th>
                                                            <th>Tanggal Pengajuan</th>
                                                            <th class="text-center">Jenis DL</th>
                                                            <th class="text-center">Tanggal DL</th>
                                                            <th class="text-start">Keterangan</th>
                                                            <th class="text-center">Status</th>
                                                                                                      
                                                            <th class="text-center">Action</th>
                                                        
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                     
                                                    $path   = 'uploads/surat_tugas/';
                                                    $no = 1;

                                                    //print_array($pengajuan_dinas_luar);
                                                    foreach ($pengajuan_dinas_luar as $dl) {

                                                                $id_pegawai = $dl->id_pegawai;
                                                                $id = $dl->id;
                                                                $jns_dl = $dl->jns_dl;
                                                                $tanggal = $dl->tanggal;
                                                                $keterangan = $dl->keterangan;
                                                                $status = $dl->status;
                                                                $surtug = $dl->surtug;
                                                                $photo = $dl->photo;
                                                                $create_at = $dl->create_at;
                                                                

                                                                if ($jns_dl == 'DLP') {
                                                                    $dl_name = '<span class="badge  bg-primary-lighten text-primary">DL - PENUH</span>';
                                                                } else if ($jns_dl == 'DLA') {
                                                                    $dl_name = '<span class="badge  bg-warning-lighten text-warning">DL - AWAL</span>';
                                                                } else {
                                                                    $dl_name = '<span class="badge  bg-success-lighten text-success">DL -  AKHIR</span>';
                                                                }

                                                            
                                                                
                                                                if ($status == 0) {
                                                                    $flag = createClassBadge('yellow');
                                                                    $status_flag = '<span class="badge  bg-warning text-white">Pending</span>';
                                                                } else if ($status == 1) {
                                                                    $flag = createClassBadge('success');
                                                                    $status_flag = '<span class="badge  bg-success text-white">Disetujui</span>';
                                                                } else {
                                                                    $flag = createClassBadge('danger');
                                                                    $status_flag = '<span class="badge  bg-danger text-white">Tidak Valid</span>';
                                                                }

                                                                echo ' <tr>
                                                                <td class="text-center"> 
                                                                    '.$no.'
                                                                </td>
                                                                <td class="text-center fw-bold">' . format_semi($create_at) . '</td>
                                                                <td class="text-center">'.$dl_name.'</td>
                                                                <td class="text-center fw-bold">'.format_semi($tanggal).'</td>
                                                                <td class="text-start">'.$keterangan.'</td>
                                                                <td class="text-center">'.$status_flag.'</td>
                                                               
                                                                
                                                                <td  class="text-center">
                                                                 <a href="'.base_url().'absensi/detail_pengajuan_dl/'.$id.'" class="btn btn-info btn-sm" title="Lihat detail "> 
                                                                  <i class="uil-window-maximize"></i></a>

                                                                  <a href="'.base_url().'absensi/delete_pengajuan_dl/' . $id . '/' . $surtug . '" class="btn btn-danger btn-sm" title="Batalkan dinas luar "> 
                                                                  <i class="uil-trash-alt"></i></a>
                                                                </td>
                                                            </tr>';

                                                            $no += 1;
                                                        }
                                                          
                                                      
                                                    ?>
                                                   
                                                   
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->

                            </div>

                          

                           
                        <div class="modal fade" id="scrollable-modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-scrollable" role="document">
                          <form action="<?php echo base_url(); ?>absensi/insertPengajuanDinasLuar" method="post" enctype="multipart/form-data" id="pengajuan_dl">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="scrollableModalTitle">Pengajuan Dinas Luar</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                  </div>
                                  <div class="modal-body">
                                       <table class="table table-borderless table-sm">
                                          <tr>
                                            <th>Tanggal</th>
                                            <td>   <input type="text" name="tgl_dl"  class="form-control"  required value="<?php echo date('d-m-Y');?>" id="tgl_absen_dl" value="" data-provider="flatpickr" data-date-format="d M Y" placeholder="Select Date"></td>
                                          </tr>
                                          <tr>
                                            <th>Jenis DL</th>
                                            <td> 
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="DLP"  required name="jns_dl" value="DLP"  class="form-check-input">
                                                    <label class="form-check-label" for="DLP">DL-PENUH</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="DLA" required value="DLA" name="jns_dl"  class="form-check-input">
                                                    <label class="form-check-label" for="DLA"> DL-AWAL</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="radio" id="DLAK"  required value="DLAK" name="jns_dl" class="form-check-input">
                                                    <label class="form-check-label" for="DLAK"> DL-AKHIR</label>
                                                </div>

                                            </div>
                                            </td>
                                          </tr>

                                          <tr>
                                            <th> <label for="keterangan" class="inline-block mb-2 text-base font-medium">Keterangan</label></th>
                                            <td> <textarea name="keterangan" required id="keterangan" class="form-control"  rows="2" cols="10" wrap="soft"></textarea></td>
                                        </tr>
                                      
                                       </table>      
                                       
                                         
                                       <div class="xl:col-span-12 ">
                                                <!-- <div class="file-upload">
                                                    <button type="button" class="file-upload-button btn btn-primary mr-4">
                                                    <i class="mdi mdi-camera"></i>
                                                    Open Camera</button>
                                                    <input type="file" accept="image/*" capture="camera" id="cameraInput" name="cameraInput">
                                                    <span class="file-upload-name">No file chosen</span>
                                                </div> -->

                                                    <center>
                                                            <input type="file" name="cameraInput" id="cameraInput" class="inputfile" />
                                                        <label for="cameraInput">Choose a file</label>

                                                    </center>
                                             
                                                    <br><br>

                                                  <img id="preview" src="#" alt="Image preview" style="display: none;">

                                                <br><br>
                                                <input type="hidden" id="latitude" name="latitude">
                                                <input type="hidden" id="longitude" name="longitude">
                                        </div>
                                   
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                      <button type="submit" class="btn btn-success kirim" id="submit_btn">Kirim</button>
                                  </div>
                              </div><!-- /.modal-content -->

                              </form>
                          </div><!-- /.modal-dialog -->
                      </div><!-- /.modal -->



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
         <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
         <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <!-- demo end -->

        <script>
            var message = '<?php echo $message;?>';

            if(message != ''){
                $.NotificationApp.send("Berhasil",message,"top-right","rgba(0,0,0,0.2)","success")
            }
          

            
            $(document).ready(function () {

                flatpickr("#tgl_absen_dl", {});


                    document.getElementById('cameraInput').addEventListener('change', function(event) {
                        var file = event.target.files[0];
                        if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('preview').src = e.target.result;
                            document.getElementById('preview').style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                        }
                    });

                });


               // Mendapatkan lokasi pengguna
               if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    }, function(error) {
                    console.error('Error getting location:', error);
                    });
                } else {
                    console.error('Geolocation is not supported by this browser.');
                }



                $("#pengajuan_dl").submit(function(){
                   // alert("sagfasw");
                    $(".modal-footer").html('<div class="d-flex align-items-center"><strong>Loading...</strong><div class="spinner-border text-success ms-auto" role="status" aria-hidden="true"></div></div>');
                });


        </script>




    </body>
</html>
