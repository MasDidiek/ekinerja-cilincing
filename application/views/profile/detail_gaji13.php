<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/signature.js"></script>

    <style>
        .my-table{
            width: 100%;
        }
        .my-table  td{
            padding:2px;
           
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
                                            <li class="breadcrumb-item active">Gaji ke 13</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Gaji</h4>
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


                                   $pin 	= substr($nip_user, -4);

                              
                                   $jabatan =  $detail_pegawai->jabatan;
                                   $puskesmas =  $detail_pegawai->puskesmas;
                                   $id_pendidikan =  $detail_pegawai->id_pendidikan;
                                   $tmt =  $detail_pegawai->tmt;
                                 

                                   $pendidikan = $this->Master_model->getNamaPendidikan($id_pendidikan);

                                   $no_hp     = $this->Laporan_model->getNoHP($nip_user);

                                    $periode = $detail_gaji[0]->periode;
                                    $pajak = $detail_gaji[0]->pajak;
                                    $netto = $detail_gaji[0]->netto;

                                    // Hitung Masa Kerja berdasarkan TMT dan Periode Gaji
                                    $tmt_date = new DateTime($tmt);
                                    $periode_date = new DateTime($periode . '-01');
                                    $diff_mk = $tmt_date->diff($periode_date);
                                    $masa_kerja = $diff_mk->y . " tahun, " . $diff_mk->m . " bulan";
                                   
                                    $nama_pegawai = $detail_gaji[0]->nama;
                                    $nik    = $detail_gaji[0]->nik;
                                    $jabatan = $detail_gaji[0]->jabatan;
                                    $gaji_pokok = $detail_gaji[0]->gaji_pokok;
                                    $total = $detail_gaji[0]->total;
                                    $tkd13 = $detail_gaji[0]->tkd13;
                                    $thr_gaji = $detail_gaji[0]->thr_gaji;
                                   // $tmt = $detail_gaji[0]->tmt;
                                    $status_kawin = $detail_gaji[0]->status_kawin;
                                    $tunj_suami = $detail_gaji[0]->tunj_suami;
                                    $tunj_anak1 = $detail_gaji[0]->tunj_anak1;
                                    $tunj_anak2 = $detail_gaji[0]->tunj_anak2;
                                    $no_rekening = $detail_gaji[0]->no_rekening;

                                    $ttd_spj = $detail_gaji[0]->ttd_spj;
                                    $ttd_on = $detail_gaji[0]->date_ttd;

                                    $format_periode = date('F Y', strtotime($periode));
                                  

                                    if($ttd_spj==''){
                                        $btn_ttd = '<button type="button" class="btn btn-info btn-sm ttd_spj"  data-bs-toggle="modal" data-bs-target="#modal-ttd-spj" > Tandatangani SPJ Gaji Ke 13</button>';
                                    }else{
                                        $btn_ttd = '<img src="'.base_url().'uploads/ttd_spj/'.$ttd_spj.'" width="200">  <br> <br>
                                        <p>Sign On : <em> '. format_view($ttd_on) .'  &nbsp; '. date('H:i', strtotime($ttd_on)) .'</em></p>';
                                    }
                                   
                                    
                               ?>



                      
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                       
                                        <h4 class="header-title mb-4">Detail Informasi Gaji Ke 13 - <?= $format_periode; ?></h4>

                                        <div class="row">
                                                <div class="col-md-7">
                                                    <h5 class="text-primary mb-3">Informasi Pegawai</h5>
                                                    <table class="table table-sm table-borderless fs-5">
                                                    <tr>
                                                        <td width="180">Nama</td>
                                                        <td><strong><?php echo  $nama_pegawai ;?> </strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jabatan</td>
                                                        <td class="text-wrap"><strong><?php echo $jabatan;?> </strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Masuk</td>
                                                        <td><strong><?php echo format_full($tmt);?></strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Masa Kerja Gaji</td>
                                                        <td><strong><?php echo $masa_kerja;?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pendidikan</td>
                                                        <td><strong><?php echo $pendidikan;?></strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Status Pajak</td>
                                                        <td><strong><?php echo $status_kawin;?></strong> </td>
                                                    </tr>
                                                </table>
                                                </div>

                                                <div class="col-md-5">
                                                    <h5 class="text-primary mb-3">Ringkasan Pembayaran</h5>
                                                    <table class="table table-sm table-bordered">
                                                    <tr class="table-light">
                                                        <td>Gaji Pokok (100%)</td>
                                                        <td class="text-end"><strong>Rp. <?php echo rupiah($gaji_pokok);?> </strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Diterima Bersih (Netto)</td>
                                                        <td class="text-end text-success fs-4"><strong>Rp. <?php echo rupiah($netto);?></strong></td>
                                                    </tr>
                                                </table>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <h5 class="mb-3 text-info">Rincian Komponen Gaji</h5>
                                                    <table class="table table-sm table-borderless fs-5">
                                                        <tr><td>Gaji Pokok</td><td class="text-end">Rp. <?= rupiah($gaji_pokok); ?></td></tr>
                                                        <tr><td>Tunjangan Suami/Istri</td><td class="text-end">Rp. <?= rupiah($tunj_suami); ?></td></tr>
                                                        <tr><td>Tunjangan Anak 1</td><td class="text-end">Rp. <?= rupiah($tunj_anak1); ?></td></tr>
                                                        <tr><td>Tunjangan Anak 2</td><td class="text-end">Rp. <?= rupiah($tunj_anak2); ?></td></tr>
                                                        <tr class="border-top">
                                                            <td><strong>Total Komponen Gaji (A)</strong></td>
                                                            <td class="text-end"><strong>Rp. <?= rupiah($thr_gaji); ?></strong></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="mb-3 text-info">Tunjangan Kinerja (TKD)</h5>
                                                    <table class="table table-sm table-borderless fs-5">
                                                        <tr><td>TKD Ke 13</td><td class="text-end">Rp. <?= rupiah($tkd13); ?></td></tr>
                                                        <tr class="border-top">
                                                            <td><strong>Total Komponen TKD (B)</strong></td>
                                                            <td class="text-end"><strong>Rp. <?= rupiah($tkd13); ?></strong></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <table class="table table-sm table-bordered fs-5">
                                                        <tr class="table-info">
                                                            <td><strong>Penghasilan Bruto (A + B)</strong></td>
                                                            <td class="text-end"><strong>Rp. <?= rupiah($total); ?></strong></td>
                                                        </tr>
                                                        <tr class="table-warning">
                                                            <td>Potongan Pajak (PPh 21)</td>
                                                            <td class="text-end text-danger"><strong>- Rp. <?= rupiah($pajak); ?></strong></td>
                                                        </tr>
                                                        <tr class="table-success">
                                                            <td class="fs-4"><strong>Total Diterima (Netto)</strong></td>
                                                            <td class="text-end fs-4"><strong>Rp. <?= rupiah($netto); ?></strong></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row text-center mt-2 p-4" >
                                                <div class="col-md-4 mb-3">
                                                            NIK
                                                    <br>
                                                    <strong><?php echo $nik;?></strong>
                                                                

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
                                            <h4 class="modal-title" id="mySmallModalLabel">Tanda tangan SPJ Gaji ke 13</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                        </div>
                                        <div class="modal-body">
                                             <form method="post" action="<?php echo base_url();?>profile/insert_tdd_spj_gaji13" enctype="multipart/form-data">
                                                Periode Gaji :  <input type="text" name="periode" readonly class="form-control"  value="<?php echo $format_periode;?>" id="periode">
                                                <br>
                                                    No HP :  <input type="text" name="no_hp" value="<?php echo $no_hp;?>" required class="form-control" placeholder="masukkan no telepon / hanphonde">
                                                <br><br>


                                                    <div class="flex flex-wrap gap-2">
                                                        
                                                            <div id="signature-pad">
                                                                <div style="border:solid 1px teal; width:360px;height:110px;padding:3px;position:relative;">
                                                                    <div id="note" onmouseover="my_function();">Tanda tangan harus di dalam kotak</div>
                                                                    <canvas id="the_canvas" width="350px" height="100px"></canvas>
                                                                </div>

                                                                <div style="margin:10px;">
                                                                    <input type="hidden" id="id_spj" name="id_spj" value="<?php echo $detail_gaji[0]->id;?>">
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
