<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/circle.css">
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/signature.js"></script>

    <style>
        .my-table{
            width: 100%;
        }
        .my-table  td{
            padding:5px;
            font-size: 16px;

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
                                            <li class="breadcrumb-item active">Main Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php

                                    $list_bulan = array_bulan();

                                    $periode_bulan = $this->session->userdata('periode_bulan');
                                    $periode_tahun = $this->session->userdata('periode_tahun');
                                    $id_pkm_sess   = $this->session->userdata('id_pkm');
                                     $nip = $this->session->userdata('nip');


                                    $bulan = $periode_bulan;
                                    $tahun = $this->uri->segment(4);


                                   //$pin 	= substr($nip_user, -4);

                                   $id_listing_tkd = $detail_thr->id;
                                   $periode = $detail_thr->periode;

                                   $nama_pegawai = $detail_thr->nama;
                                  
                                   
                                    $status_kawin = $detail_thr->status_kawin;
                                    $npwp = $detail_thr->npwp;
                                    $gaji_pokok = $detail_thr->gaji_pokok;
                                    $no_rekening = $detail_thr->no_rekening;
                                    $tunj_suami = $detail_thr->tunj_suami;
                                    $tunj_anak1 = $detail_thr->tunj_anak1;
                                    $tunj_anak2 = $detail_thr->tunj_anak2;
                                    $thr_gaji = $detail_thr->thr_gaji;
                                  
                            
                                   $ttd_spj = $detail_thr->ttd_spj;
                                   $ttd_on = $detail_thr->ttd_spj;
                                   $periode = $detail_thr->periode;

                                 
                                  // $detail_pegawai = $this->Pegawai_model->getPegawaiByNama($nama_pegawai);

                                   $nip =  $detail_pegawai->nip;
                                   $nama =  $detail_pegawai->nama;
                                   $id_pegawai =  $detail_pegawai->id_pegawai;


                                   $id_pendidikan =  $detail_pegawai->id_pendidikan;

                                   $tmt =  $detail_pegawai->tmt;

                                 
                                   //print_array($detail_thr);


                                   $datePeriode = $periode.'-01';
                                   $lastDate    = date('t', strtotime($datePeriode));
                                   $endPeriode  = $periode.'-'.$lastDate;


                                   $pendidikan = $this->Master_model->getNamaPendidikan($id_pendidikan);
                                   $masa_kerja =  hitungMasaKerja($tmt, $endPeriode);
                                   $masa_kerja_tahun = $masa_kerja['years'];
                                   $masa_kerja_bulan = $masa_kerja['months'];

                                   //print_array($masa_kerja);
                                

                                    if($ttd_spj==''){
                                        
                                            $btn_ttd = '<button type="button" class="btn btn-info btn-sm ttd_spj"  data-bs-toggle="modal" data-bs-target="#modal-ttd-spj" > Tandatangani SPJ TKD</button>';
                                        
                                    }else{
                                        $btn_ttd = '<img src="'.base_url().'uploads/ttd_spj/'.$ttd_spj.'" width="200">  <br> <br>
                                        <p>Sign On : <em> '. format_view($ttd_on) .'  &nbsp; '. date('H:i', strtotime($ttd_on)) .'</em></p>';
                                    }




                                   $today = new DateTime('2025-04-30');

                                    $format_periode = date('F Y', strtotime($periode));
                                    $status = $detail_thr->status_kawin;

                                   
                               
                               ?>




                        <div class="row">

                            <div class="col-xl-7 col-lg-7">
                                <div class="card">
                                    <div class="card-body">
                                         <h3>Profile Pegawai</h3>

                                         <table class="my-table">
                                            <tr>
                                                <td width="150">Nama</td>
                                                <td><strong><?php echo  $nama_pegawai ;?> </strong> </td>
                                            </tr>
                                            <tr>
                                                <td>Jabatan</td>
                                                <td><strong><?php echo $detail_thr->jabatan;?> </strong> </td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal Masuk</td>
                                                <td><strong><?php echo format_full($tmt);?></strong> </td>
                                            </tr>
                                            <tr>
                                                <td>Masa Kerja</td>
                                                <td><strong><?php echo $masa_kerja_tahun.' tahun '.$masa_kerja_bulan.' bulan' ;?></strong>
                                                </td>
                                            </tr>

                                            <tr>
                                            <td width="150">Pendidikan</td>
                                            <td><strong><?php echo $pendidikan;?></strong> </td>
                                            </tr>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5 col-lg-5">
                                <div class="card">
                                <h4 class="header-title p-2">Tunjangan Hari Raya (Gaji)</h4>

                                    <div class="card-body text-center fw-bold">

                                         
                                            <h3 class="fw-bold mt-3">
                                                <span class="text-success">Rp. <?php echo rupiah($thr_gaji);?></span>
                                            </h3>

                                            <div class=" fs-4 fw-bold">Total Penerimaan</div>

                                    </div>

                                         <div class="card-body border-top p-2">
                                           <h5>Informasi Detail Tunjangan Hari Raya (Gaji)</h5>
                                             <br>
                                             <i class="mdi mdi-arrow-right"> </i> 
                                            <ul>
                                                <li> Status Kawin :  <span class="float-end fw-bold"><?= $status; ?></span></li>
                                               
                                            </ul>
                                           <br>

                                          <i class="mdi mdi-arrow-right"> </i> Perhitungan THR
                                          <ul>
                                                <li>Gaji Pokok  <span class="float-end fw-bold">Rp. <?php echo rupiah($gaji_pokok);?></span></li>
                                                <li>Tunjangan Suami / Istri  <span class="float-end fw-bold">Rp. <?php echo rupiah($tunj_suami);?></span></li>
                                                <li>Tunjangan Anak 1  <span class="float-end fw-bold">Rp. <?php echo rupiah($tunj_anak1);?></span></li>
                                                <li>Tunjangan Anak 2  <span class="float-end fw-bold">Rp. <?php echo rupiah($tunj_anak2);?></span></li>
                                          </ul>


                                         
                                          <hr>

                                          <div class=" fs-5 fw-bold">Total Penerimaan</div>
                                            <span class="fs-4 float-end fw-bold">Rp. <?php echo rupiah($thr_gaji);?></span>
                                        </div>


                                </div>
                            </div>



                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">



                                            <div class="row text-center mt-2 p-4" >
                                                <div class="col-md-4 mb-3">
                                                    NIK
                                                    <br>
                                                    <strong><?php echo $npwp;?></strong>


                                                </div>
                                                <div class="col-md-4 mb-3">
                                                No Rekening
                                                                <br>
                                                                <strong><?php echo $no_rekening;?></strong>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                Tanda Tangan  <br>
                                                            <br>
                                                            <?php echo $btn_ttd;?>
                                        </div>
                                            </div>


                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->

                            </div>



                            <div class="modal fade" id="modal-ttd-spj" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="mySmallModalLabel">Tanda tangan SPJ THR (Gaji)</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                        </div>
                                        <div class="modal-body">
                                             <form method="post" action="<?php echo base_url();?>profile/insert_tdd_spj_thr_gaji" enctype="multipart/form-data">
                                                Periode TKD :  <input type="text" name="periode" readonly class="form-control"  value="<?php echo $format_periode;?>" id="periode">
                                                <br>
                                                    No HP :  <input type="text" name="no_hp" required class="form-control" placeholder="masukkan no telepon / hanphonde">
                                                <br><br>


                                                    <div class="flex flex-wrap gap-2">

                                                            <div id="signature-pad">
                                                                <div style="border:solid 1px teal; width:360px;height:110px;padding:3px;position:relative;">
                                                                    <div id="note" onmouseover="my_function();">Tanda tangan harus di dalam kotak</div>
                                                                    <canvas id="the_canvas" width="350px" height="100px"></canvas>
                                                                </div>

                                                                <div style="margin:10px;">
                                                                    <input type="hidden" id="id_spj" name="id_spj" value="<?php echo $detail_thr->id;?>">
                                                                    <input type="hidden" id="tahun" name="tahun" value="<?php echo $tahun;?>">
                                                                    <input type="hidden" id="signature" name="signature">
                                                                    <button type="button" id="clear_btn" class="btn btn-danger" data-action="clear"><span class="glyphicon glyphicon-remove"></span> Clear</button>
                                                                    <button type="submit" id="save_btn" class="btn btn-primary" data-action="save-png"><span class="glyphicon glyphicon-ok"></span> Submit</button>
                                                                </div>
                                                            </div>
                                                        <form>

                                                    </div>

                                                <br>

                                        </div>
                                    </div><!-- /.modal-content -->
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
        <!-- demo end -->



        <script>
        var message = '<?php echo $message;?>';

        if(message != ''){
            $.NotificationApp.send("Berhasil",message,"top-right","rgba(0,0,0,0.2)","success")
        }



var wrapper = document.getElementById("signature-pad");
                var clearButton = wrapper.querySelector("[data-action=clear]");
                var savePNGButton = wrapper.querySelector("[data-action=save-png]");
                var canvas = wrapper.querySelector("canvas");
                var el_note = document.getElementById("note");
                var signaturePad;
                signaturePad = new SignaturePad(canvas);
                clearButton.addEventListener("click", function (event) {
                document.getElementById("note").innerHTML="The signature should be inside box";
                signaturePad.clear();
                });
                savePNGButton.addEventListener("click", function (event){
                if (signaturePad.isEmpty()){
                    alert("Please provide signature first.");
                    event.preventDefault();
                }else{
                    var canvas  = document.getElementById("the_canvas");
                    var dataUrl = canvas.toDataURL();
                    document.getElementById("signature").value = dataUrl;
                }
                });
                function my_function(){
                document.getElementById("note").innerHTML="";
                }

            </script>

    </body>
</html>
