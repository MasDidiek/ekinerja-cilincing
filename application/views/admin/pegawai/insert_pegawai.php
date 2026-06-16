<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />


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

                       

                            $arrayJnsPegawai = array('non_pns', 'pns', 'pppk', 'pjlp');

                            $arrayUsergroup = arrayUsergroup();

                            $message = $this->session->flashdata('message');
                            $rumpun_kerja = 'UKP';
                            

                           
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
                                    <h4 class="page-title">Add New Pegawai </h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->



                        <div class="row">
                             <div class="col-sm-12">
                                 <!-- Profile -->
                                 <div class="card">
                                     <div class="card-body profile-user-box">
                                        <h5>Add New Pegawai</h5>
                                        <br>

                                        <?php

                                                if ( $message != '') {
                                                    echo '<div class="alert alert-success">'.$message.'</div>';
                                                }
                                        ?>


                                                
                                        <form action="<?php echo base_url();?>admin/pegawai/insert_pegawai" method="post" id="update_myprofile">
                                          <div class="row">
                                            <div class="col-md-4">
                                                
                                                
                                                   <div class="row mb-2">
                                                        <div class="col-md-12">

                                                    
                                                            <label for="nama" class="inline-block">Nama Lengkap</label>
                                                            <input type="text" id="nama" name="nama" required class=" form-control">
                                                        </div><!--end col-->
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="mb-2">
                                                                <label for="nip" class="inline-block">NIP</label>
                                                                <input type="text" id="nip" name="nip" required class=" form-control">
                                                            </div><!--end col-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-2">
                                                                <label for="nrk" class="inline-block">NRK</label>
                                                                <input type="text" id="nrk" name="nrk" required class="form-control">
                                                            </div><!--end col-->
                                                        
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-md-4">
                                                            <div>
                                                                <label for="nrk" class="inline-block">TMT</label>
                                                                <input type="text" id="tmt" name="tmt" required class=" form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div>
                                                                <label for="inputValue" class="inline-block">Jenis Pegawai</label>
                                                                <select class="form-select" id="jns_pegawai" name="jns_pegawai">
                                                                        <?php
                                                                            for ($j=0; $j < 4; $j++){
                                                                                                        
                                                                                $jns_peg = $arrayJnsPegawai[$j];

                                                                                $namaJnsPegawai = strtoupper($jns_peg);
                                                                                $namaJnsPegawai = str_replace("_"," ", $namaJnsPegawai);
                                                                              
                                                                                 echo ' <option value="'. $jns_peg .'">'.$namaJnsPegawai .'</option>';
                                                                              
                                                                            
                                                                            }

                                                                        ?>
                                                                
                                                                </select>
                                                                
                                                                
                                                            </div><!--end col-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div>
                                                              <label for="inputValue" class="inline-block">Rumpun Kerja</label>
                                                                 <input type="text" class="form-control" required id="rumpun_kerja" value="UKP" name="rumpun_kerja">
                                                            </div><!--end col-->
                                                        </div>
                                                    </div>
                                                  
                                                    
                                                    <div class="mb-2">
                                                        <label for="id_jabatan" class="inline-block">Jabatan</label>
                                                        <select class="form-select" id="id_jabatan" name="id_jabatan" >
                                                            <?php
                                                                foreach ($list_jabatan as $jbt){
                                                                                            
                                                                    $id_jab = $jbt->id;
                                                                    $nama_jabatan = $jbt->nama;

                                                                    echo ' <option value="'. $id_jab .'">'.$nama_jabatan .'</option>';
                                                                  
                                                                
                                                                    
                                                                }

                                                            ?>
                                                    
                                                        </select>
                                                        
                                                    
                                                    </div><!--end col-->

                                                      
                                                    <div class="mb-2">
                                                        <label for="id_pendidikan" class="inline-block">Pendidikan</label>
                                                        <select class="form-select" id="id_pendidikan" name="id_pendidikan" >
                                                            <?php
                                                                foreach ($list_pendidikan as $pend){
                                                                                            
                                                                    $id_pend = $pend->id;
                                                                    $pendidikan = $pend->pendidikan;

                                                                    echo ' <option value="'. $id_pend .'">'.$pendidikan .'</option>';
                                                                   
                                                                    
                                                                }

                                                            ?>
                                                    
                                                        </select>
                                                        
                                                    
                                                    </div><!--end col-->

                                                    
                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Keterangan Jabatan</label>
                                                        <input type="text" id="inputValue" name="ket_jab"  class="form-control">
                                                    </div><!--end col-->
                                                    
                                                    
                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Poli/Bagian</label>
                                                        <select class="form-select" id="poliInput" name="id_poli">
                                                                <?php
                                                                    foreach ($list_poli as $poli){
                                                                                                
                                                                        $id_poli = $poli->id;
                                                                        $nama_poli = $poli->nama_poli;

                                                                        echo ' <option value="'. $id_poli .'">'.$nama_poli .'</option>';
                                                                        
                                                                    
                                                                    }

                                                                ?>
                                                        
                                                        </select>
                                                    </div><!--end col-->
                                                    
                                                    
                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Jenis Jam Kerja</label>
                                                        
                                                    <div class="flex flex-wrap gap-4">
                                                            <div class="flex items-center gap-2">
                                                                <input id="radioInline4" name="jam_kerja"  value="non_shift" checked type="radio">
                                                                <label for="radioInline4" class="align-middle">
                                                                    REGULER
                                                                </label>
                                                            </div>
                                                
                                                    
                                                
                                                            <div class="flex items-center gap-2">
                                                                <input id="radioInline5" name="jam_kerja"  value="shift"  type="radio" value="">
                                                                <label for="radioInline5" class="align-middle">
                                                                    SHIFT
                                                                </label>
                                                            </div>
                                                
                                                        
                                                        </div>
                                                        
                                                        
                                                    
                                                    </div><!--end col-->
                                                
                                                    
                                                    <div class=" mb-2 ">
                                                        <label for="inputValue" class="inline-block">Puskesmas</label>
                                                        <select id="puskesmasInput" class="form-control " name="id_puskesmas" required aria-label="Default select example">
                                                        
                                                                <?php
                                                                    foreach ($list_puskesmas as $puskesmas){
                                                                                                
                                                                            $id_puskesmas = $puskesmas->id_puskesmas;
                                                                            $nama_puskesmas = $puskesmas->nama;
                                                                            
                                                                            echo ' <option value="'. $id_puskesmas .'">'.$nama_puskesmas .'</option>';
                                                                                                                        
                                                                    

                                                                        }


                                                                ?>
                                                            </select>
                                                    </div><!--end-->
                                                    
                                                    
                                                    <div class="mb-4">
                                                        <label for="inputValue" class="inline-block">Atasan Langsung</label>
                                                        <input type="text" id="id_validator" readonly value="1058"  name="validator"  class="form-control" placeholder="No Rekening Bank DKI">
                                                    </div>
                                                   
                                               
                                            
                                            </div><!--col-md-4-->
                                            <div class="col-md-4">

                                                  <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="mb-2 ">
                                                                <label for="inputValue" class="inline-block">No Telp/HP</label>
                                                                <input type="text" id="inputValue" name="no_tlp" class="form-control" placeholder="081222...">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="mb-2">
                                                                <label for="inputValue" class="inline-block">Email Address</label>
                                                                <input type="email" id="inputValue" name="email" class=" form-control" placeholder="Enter your email address">
                                                            </div><!--end col-->
                                                         </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="mb-2">
                                                                <label for="tmptLahir" class="inline-block">Tempat Lahir</label>
                                                                <input type="text" id="tmptLahir" name="tempat_lahir" class=" form-control" placeholder="cth: Jakarta, Semarang" >
                                                            </div><!--end col-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-2">
                                                                <label for="joiningDateInput" class="inline-block">Tanggal Lahir</label>
                                                                <input type="text" id="joiningDateInput"  name="tgl_lahir"  class="f form-control" placeholder="Select date" data-provider="flatpickr" data-date-format="d M, Y" >
                                                            </div><!--end col-->
                                                        </div>
                                                    </div>
                                                  
                                              

                                                     <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">No KTP</label>
                                                        <input type="text" id="ktp"   name="no_ktp"  class="form-control" placeholder="No KTP">
                                                    </div><!--end col-->
                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">No NPWP</label>
                                                        <input type="text" id="npwp"   name="npwp"  class="form-control" placeholder="No NPWP">
                                                    </div><!--end col-->
                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">No Rekening</label>
                                                        <input type="text" id="no_rekening"  name="no_rekening"  class="form-control" placeholder="No Rekening Bank DKI">
                                                    </div><!--end col-->
                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Usergroup</label>
                                                        <select class="form-control" id="usergroup" name="usergroup"  data-choices=""  aria-label="Default select example">
                                                            <?php
                                                                for ($u= 0; $u < count($arrayUsergroup);  $u++){                                                                                           
                                                                
                                                                    $nama_ug = $arrayUsergroup[$u];
                                                                    echo ' <option value="'. $u .'">'.$nama_ug .'</option>';
                                                                  
                                                                }

                                                            ?>
                                                    
                                                        </select>
                                                        
                                                    </div><!--end col-->
                                                    <div class="mb-2">
                                                        <label for="inputValue">Alamat KTP</label>
                                                        <textarea name="alamat_ktp" class="form-control" id="exampleFormControlTextarea2" placeholder="Alamat sesuai KTP" rows="3"></textarea>
                                                        
                                                    </div><!--end col-->
                                                    <div class="mb2">
                                                        <label for="inputValue" class="block mb-2">Alamat Domisili</label>
                                                        <textarea name="alamat_domisili" class="form-control" id="exampleFormControlTextarea1" placeholder="Alamat domisili" rows="3"></textarea>
                                                        
                                                    </div><!--end col-->

                                                    <div class="mt-4">
                                                        <a href="<?php echo base_url();?>admin/pegawai/list_pegawai/non_pns" class="btn btn-light">Back To List</a>
                                                        <button type="submit" class="btn btn-success float-end">Simpan</button>
                                                    </div>

                                             </div>
                                         </div>
                                         
                                            
                                        </form><!--end form-->

                                     </div> <!-- end card-body/ profile-user-box-->
                                 </div><!--end profile/ card -->
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
            
        $("#puskesmasInput").change(function(){
            var id_puskesmas = $(this).val();
            var rumpun = '<?php echo $rumpun_kerja;?>';
    
                   $.ajax({
        
                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>profile/getValidator",
                            data:"id_puskesmas="+id_puskesmas+"&rumpun_kerja="+rumpun,
                            success:function(msg){
                                //window.location.reload();
                                $("#id_validator").val(msg);
                                //console.log(msg);
                            }
    
                    });
    
            });
        
        
                 
        </script>



    </body>
</html>
