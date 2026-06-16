<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />


    <body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": false}'>
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

                          $id_pegawai = $pegawai->id_pegawai;
                          $tgl_masuk = $pegawai->tgl_masuk;
                          $nip = $pegawai->nip;
                          $nama_pegawai = $pegawai->nama;



                            $status_kawin  = $pegawai->status_kawin;
                            $status_pajak  = $pegawai->status_pajak;
                            $id_pendidikan = $pegawai->id_pendidikan;

                            $pendidikan = $this->Master_model->getNamaPendidikan($id_pendidikan);

                            if($status_kawin==1){
                                $status_nikah = 'K3 (Menikah + 2 anak)';
                            }else if($status_kawin==2){
                                $status_nikah = 'K2 (Menikah + 1 anak)';
                            }else if($status_kawin==3){
                                $status_nikah = 'K1 (Menikah + 0 anak)';
                            }else{
                                $status_nikah = 'K0 (Belum Menikah)';
                            }


                            if(!empty($pegawai_gaji)){
                                $gaji_pokok = $pegawai_gaji[0]->gaji_pokok;
                                $pengkalian = $pegawai_gaji[0]->pengkalian;
                                $status_kerja= $pegawai->status_kerja;

                            }else{
                                $gaji_pokok = 0;
                                $pengkalian = 0;
                                $status_kerja= 1;

                            }


                             $checkAktif = '';
                             $checkCuti = '';
                             $checkNonAktif = '';

                            if($status_kerja==1){
                                $checkAktif = 'checked';
                            }

                            if($status_kerja==2){
                                $checkCuti = 'checked';
                            }

                            if($status_kerja==0){
                                $checkNonAktif = 'checked';
                            }


                            $tkd_pokok  = $gaji_pokok*$pengkalian;

                            //print_array($pegawai);

                            $tmt = $pegawai->tmt;
                            $today = date('Y-m-d');

                            $id_poli = $pegawai->id_poli;
                            $jns_pegawai = $pegawai->jns_pegawai;

                            $jns_jam_kerja = $pegawai->jns_jam_kerja;
                            $rumpun_kerja  = $pegawai->rumpun_kerja;
                            $klaster  = $pegawai->klaster;
                            $id_validator  = $pegawai->id_validator;
                            $id_pendidikan = $pegawai->id_pendidikan;
                            $bagian_shift = $pegawai->bagian_shift;

                            $namaValidator = $this->Pegawai_model->getNamaPegawaiByID($id_validator);
                            $arrayJnsPegawai = array('non_pns', 'pns', 'pppk','pppk_pw', 'pjlp');
                            $arrayUsergroup = arrayUsergroup();

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



                        <div class="row">
                             <div class="col-sm-12">
                                 <!-- Profile -->
                                 <div class="card">
                                     <div class="card-body profile-user-box">
                                        <h5>Edit Data Pegawai</h5>
                                        <br>

                                         <a href="<?php echo base_url();?>admin/pegawai/list_pegawai/pppk_pw" class="btn btn-light">Back To List</a>
                                        <?php

                                                if ( $message != '') {
                                                    echo '<div class="alert alert-success">'.$message.'</div>';
                                                }
                                        ?>



                                        <form action="<?php echo base_url();?>admin/pegawai/update_profile_pegawai/<?php echo $id_pegawai.'/'.$nip;?>" method="post" id="update_myprofile">
                                          <div class="row">
                                            <div class="col-md-6">


                                                   <div class="row mb-2">
                                                        <div class="col-md-12">


                                                            <label for="nama" class="inline-block">Nama Lengkap</label>
                                                            <input type="text" id="nama" name="nama" value="<?php echo $pegawai->nama;?>" class=" form-control">
                                                        </div><!--end col-->
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="mb-2">
                                                                <label for="nip" class="inline-block">NIP</label>
                                                                <input type="text" id="nip" name="nip" value="<?php echo $pegawai->nip;?>" class=" form-control">
                                                            </div><!--end col-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-2">
                                                                <label for="nrk" class="inline-block">NRK</label>
                                                                <input type="text" id="nrk" name="nrk" value="<?php echo $pegawai->nrk;?>" class="form-control">
                                                            </div><!--end col-->

                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-md-4">
                                                            <div>
                                                                <label for="nrk" class="inline-block">TMT</label>
                                                                <input type="text" id="tmt" name="tmt" value="<?php echo $pegawai->tmt;?>" class=" form-control">
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

                                                                                if($jns_peg == $jns_pegawai){

                                                                                        echo ' <option value="'. $jns_peg .'" selected>'.$namaJnsPegawai .'</option>';
                                                                                }else{
                                                                                        echo ' <option value="'. $jns_peg .'">'.$namaJnsPegawai .'</option>';
                                                                                }


                                                                            }

                                                                        ?>

                                                                </select>


                                                            </div><!--end col-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div>
                                                              <label for="inputValue" class="inline-block">Rumpun Kerja</label>
                                                                 <input type="text" class="form-control" value="<?php echo strtoupper($rumpun_kerja);?>" id="rumpun_kerja" name="rumpun_kerja">
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


                                                                    if($id_jab== $pegawai->id_jabatan){

                                                                            echo ' <option value="'. $id_jab .'" selected>'.$nama_jabatan .'</option>';
                                                                    }else{
                                                                            echo ' <option value="'. $id_jab .'">'.$nama_jabatan .'</option>';
                                                                    }


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


                                                                    if($id_pend== $id_pendidikan){

                                                                            echo ' <option value="'. $id_pend .'" selected>'.$pendidikan .'</option>';
                                                                    }else{
                                                                            echo ' <option value="'. $id_pend .'">'.$pendidikan .'</option>';
                                                                    }


                                                                }

                                                            ?>

                                                        </select>


                                                    </div><!--end col-->


                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Keterangan Jabatan</label>
                                                        <input type="text" id="inputValue" name="ket_jab" value="<?php echo $pegawai->keterangan_jabatan;?>" class="form-control">
                                                    </div><!--end col-->


                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Poli/Bagian</label>
                                                        <select class="form-select" id="poliInput" name="id_poli">
                                                                <?php
                                                                    foreach ($list_poli as $poli){

                                                                        $id_poli = $poli->id;
                                                                        $nama_poli = $poli->nama_poli;


                                                                            if($id_poli== $pegawai->id_poli){

                                                                                echo ' <option value="'. $id_poli .'" selected>'.$nama_poli .'</option>';
                                                                        }else{
                                                                                echo ' <option value="'. $id_poli .'">'.$nama_poli .'</option>';
                                                                        }


                                                                    }

                                                                ?>

                                                        </select>
                                                    </div><!--end col-->


                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Jenis Jam Kerja</label>

                                                    <div class="flex flex-wrap gap-4">
                                                            <div class="flex items-center gap-2">
                                                                <input id="radioInline4" name="jam_kerja"  value="non_shift" <?php echo ($jns_jam_kerja=='non_shift') ? "checked" : "";?> class="border rounded-full appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500" type="radio" value="" checked="">
                                                                <label for="radioInline4" class="align-middle">
                                                                    REGULER
                                                                </label>
                                                            </div>



                                                            <div class="flex items-center gap-2">
                                                                <input id="radioInline5" name="jam_kerja"  value="shift" <?php echo ($jns_jam_kerja=='shift') ? "checked" : "";?>  class="border rounded-full appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-orange-500 checked:border-orange-500 dark:checked:bg-orange-500 dark:checked:border-orange-500" type="radio" value="">
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

                                                                            if($id_puskesmas== $pegawai->id_puskesmas){

                                                                                echo ' <option value="'. $id_puskesmas .'" selected>'.$nama_puskesmas .'</option>';
                                                                            }else{
                                                                                echo ' <option value="'. $id_puskesmas .'">'.$nama_puskesmas .'</option>';
                                                                            }




                                                                        }


                                                                ?>
                                                            </select>
                                                    </div><!--end-->


                                                    <div class="mb-4">
                                                        <label for="inputValue" class="inline-block">Atasan Langsung</label>
                                                        <input type="text" id="id_validator" readonly value="<?php echo $namaValidator;?>"  name="validator"  class="form-control" placeholder="No Rekening Bank DKI">
                                                    </div>



                                            </div><!--col-md-4-->
                                            <div class="col-md-6">

                                                  <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="mb-2 ">
                                                                <label for="inputValue" class="inline-block">No Telp/HP</label>
                                                                <input type="text" id="inputValue" name="no_tlp" value="<?php echo $pegawai_detail->no_tlp;?>" class="form-control" placeholder="081222...">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="mb-2">
                                                                <label for="inputValue" class="inline-block">Email Address</label>
                                                                <input type="email" id="inputValue" name="email" value="<?php echo $pegawai_detail->email;?>" class=" form-control" placeholder="Enter your email address">
                                                            </div><!--end col-->
                                                         </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="mb-2">
                                                                <label for="tmptLahir" class="inline-block">Tempat Lahir</label>
                                                                <input type="text" id="tmptLahir" name="tempat_lahir" value="<?php echo $pegawai_detail->tempat_lahir;?>" class=" form-control" placeholder="cth: Jakarta, Semarang" >
                                                            </div><!--end col-->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-2">
                                                                <label for="joiningDateInput" class="inline-block">Tanggal Lahir</label>
                                                                <input type="text" id="joiningDateInput" value="<?php echo $pegawai_detail->tgl_lahir;?>" data-default-date="<?php echo date('d M, Y', strtotime($pegawai_detail->tgl_lahir));?>" name="tgl_lahir"  class="f form-control" placeholder="Select date" data-provider="flatpickr" data-date-format="d M, Y" >
                                                            </div><!--end col-->
                                                        </div>
                                                    </div>



                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="ktp" class="inline-block">No KTP</label>
                                                            <input type="text"
                                                                id="ktp"
                                                                name="no_ktp"
                                                                value="<?php echo $pegawai_detail->no_ktp;?>"
                                                                class="form-control"
                                                                placeholder="No KTP">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="npwp" class="inline-block">No NPWP</label>
                                                            <input type="text"
                                                                id="npwp"
                                                                name="npwp"
                                                                value="<?php echo $pegawai_detail->npwp;?>"
                                                                class="form-control"
                                                                placeholder="No NPWP">
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="no_rekening" class="inline-block">No Rekening</label>
                                                            <input type="text"
                                                                id="no_rekening"
                                                                name="no_rekening"
                                                                value="<?php echo $pegawai_detail->no_rekening;?>"
                                                                class="form-control"
                                                                placeholder="No Rekening Bank DKI">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="inputValue" class="inline-block">Usergroup</label>
                                                            <select class="form-control" id="usergroup" name="usergroup"  data-choices=""  aria-label="Default select example">
                                                                <?php
                                                                    for ($u= 0; $u < count($arrayUsergroup);  $u++){


                                                                        $nama_ug = $arrayUsergroup[$u];


                                                                        if($u== $pegawai->usergroup){

                                                                                echo ' <option value="'. $u .'" selected>'.$nama_ug .'</option>';
                                                                        }else{
                                                                                echo ' <option value="'. $u .'">'.$nama_ug .'</option>';
                                                                        }

                                                                    }

                                                                ?>

                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="no_rekening" class="inline-block">Klater</label>
                                                            <input type="number"
                                                                id="klaster"
                                                                name="klaster"
                                                                value="<?php echo $pegawai->klaster;?>"
                                                                class="form-control" min="1"
                                                                max="5">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="no_rekening" class="inline-block">ID Mesin</label>
                                                            <input type="text"
                                                                id="pin"
                                                                name="pin"
                                                                value="<?php echo $pegawai->pin;?>"
                                                                class="form-control">
                                                        </div>
                                                    </div>


                                                    <div class="mb-2">
                                                        <label for="inputValue" class="inline-block">Shift Bagian</label>
                                                        <select class="form-control" id="shift_bagian" name="shift_bagian">
                                                            <?php
                                                                for ($bg= 0; $bg < count($bagianShift);  $bg++){
                                                                    $nama_bagian_shift = $bagianShift[$bg]->nama_bagian;
                                                                     $id_bagian = $bagianShift[$bg]->id_bagian;

                                                                     if($bagian_shift== $id_bagian){
                                                                            echo ' <option value="'. $id_bagian.'" selected>'.$nama_bagian_shift .'</option>';
                                                                         }else{
                                                                            echo ' <option value="'. $id_bagian.'">'.$nama_bagian_shift .'</option>';
                                                                     }




                                                                }

                                                            ?>

                                                        </select>



                                                    </div><!--end col-->

                                                    <div class="mb-2">
                                                        <label for="inputValue">Alamat KTP</label>
                                                        <textarea name="alamat_ktp" class="form-control" id="exampleFormControlTextarea2" placeholder="Alamat sesuai KTP" rows="3"><?php echo $pegawai_detail->alamat_ktp;?></textarea>

                                                    </div><!--end col-->
                                                    <div class="mb2">
                                                        <label for="inputValue" class="block mb-2">Alamat Domisili</label>
                                                        <textarea name="alamat_domisili" class="form-control" id="exampleFormControlTextarea1" placeholder="Alamat domisili" rows="3"><?php echo $pegawai_detail->alamat_domisili;?></textarea>

                                                    </div><!--end col-->

                                                    <div class="mt-4">
                                                        <a href="<?php echo base_url();?>admin/pegawai/list_pegawai/pppk_pw" class="btn btn-light">Back To List</a>
                                                        <a href="<?php echo base_url();?>admin/pegawai/detail_pegawai/<?php echo $id_pegawai;?>" class="btn btn-light">Back</a>
                                                        <button type="submit" class="btn btn-success float-end">Updates</button>
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
            var klaster = $("#klaster").val();

                   $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>profile/getValidator",
                            data:"id_puskesmas="+id_puskesmas+"&klaster="+klaster,
                            success:function(msg){
                                //window.location.reload();
                                $("#id_validator").val(msg);
                                //console.log(msg);
                            }

                    });

            });


          $("#usergroup").change(function(){
            var usergroup = $(this).val();

                if(usergroup==5){
                    $("#shift_bagian").slideDown();
                }else{
                      $("#shift_bagian").slideUp();
                }

            });






        </script>



    </body>
</html>
