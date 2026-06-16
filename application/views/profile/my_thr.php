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
                                            <li class="breadcrumb-item active">Main Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                      
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                       
                                        <h4 class="header-title">Detail Informasi Tunjangan Hari Raya</h4><br><br>


                                        <table class="table">
                                            <tr>
                                                <th>THR TKD</th>
                                                <th>THR Gaji</th>
                                                <th>THR Total</th>
                                            </tr>

                                            <?php
                                                //print_array($thr_gaji);

                                                $tahun = date('Y'); 
                                                if($thr_tkd){
                                                    $jumlah_thr_tkd = $thr_tkd->total;
                                                    $jumlah_thr_gaji = $thr_gaji->thr_gaji;
                                                    $total = $jumlah_thr_tkd+$jumlah_thr_gaji;

                                                    $ttd_thr_tkd   = $thr_tkd->ttd_spj;
                                                    $date_ttd_tkd  = $thr_tkd->date_ttd;



                                                    $imageTTD = '<img src="'.base_url().'uploads/ttd_spj/'.$ttd_thr_tkd.'" width="200">  <br> <br>
                                                            <p>Sign On : <em> '. format_view($date_ttd_tkd) .'  &nbsp; '. date('H:i', strtotime($date_ttd_tkd)) .'</em></p>';


                                                }else{
                                                    $jumlah_thr_tkd =  0;
                                                    $jumlah_thr_gaji = 0;
                                                    $total = 0;

                                                     $ttd_thr_tkd   =  '';
                                                       $imageTTD = '';

                                                       echo '<div class="alert alert-danger">Listing Tunjangan Hari Raya (TKD) belum ada</div>';
                                                }
                                               



                                                if($thr_gaji){

                                                    $ttd_thr_gaji  = $thr_gaji->ttd_spj;
                                                    $date_ttd_gaji = $thr_gaji->date_ttd;


                                                    $imageTTDGaji = '<img src="'.base_url().'uploads/ttd_spj/'.$ttd_thr_gaji.'" width="200">  <br> <br>
                                                            <p>Sign On : <em> '. format_view($date_ttd_gaji) .'  &nbsp; '. date('H:i', strtotime($date_ttd_gaji)) .'</em></p>';
                                                            $btn_ttd_spj_gaji = ' <a href="'.base_url().'profile/detail_thr_gaji/'.$thr_gaji->id.'" class="btn btn-light"> <i class="mdi mdi-eye"></i> Detail </a>';
                                                }else{
                                                    $imageTTDGaji = '';
                                                    $btn_ttd_spj_gaji = ' ';

                                                    echo '<div class="alert alert-danger">Listing Tunjangan Hari Raya (Gaji) belum ada</div>';
                                                }


                                                

                                            ?>
                                            <tr>
                                                <td>
                                                        <h4>Rp. <?= rupiah($jumlah_thr_tkd); ?></h4>  <br>

                                                        <?php 
                                                        if( $thr_tkd && $ttd_thr_tkd==''){
                                                            
                                                            echo '<button type="button" class="btn btn-info btn-sm ttd_spj"  data-bs-toggle="modal" data-bs-target="#modal-ttd-spj" > <i class="mdi mdi-pencil"></i> Tandatangani SPJ THR TKD</button>';
                                                        }else{

                                                             echo $imageTTD;
                                                        }
                                                       ?>
                                                        
                                                       
                                                </td>
                                                <td>
                                                    <h4>Rp.  <?= rupiah($jumlah_thr_gaji); ?></h4> <br>
                                                       
                                                        <?php 
                                                        if($thr_gaji && $ttd_thr_gaji==''){
                                                            echo '<div class="alert alert-danger">SPJ Belum ditandatangani</div>';
                                                        }else{

                                                             echo $imageTTDGaji;
                                                        }

                                                        echo $btn_ttd_spj_gaji;
                                                       ?>
                                                      
                                                </td>
                                                <td>
                                                    <h4>Rp.  <?= rupiah($total); ?></h4> 
                                                </td>
                                            </tr>
                                        </table>
                                      
                                         
                                    </div>
                                </div>
                            </div>



                            <div class="modal fade" id="modal-ttd-spj" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="mySmallModalLabel">Tanda tangan SPJ THR TKD</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                        </div>
                                        <div class="modal-body">
                                             <form method="post" action="<?php echo base_url();?>profile/save_ttd_thr_tkd" enctype="multipart/form-data">
                                               
                                                    No HP :  <input type="text" name="no_hp" required class="form-control" placeholder="masukkan no telepon / hanphonde">
                                                <br><br>


                                                    <div class="flex flex-wrap gap-2">

                                                            <div id="signature-pad">
                                                                <div style="border:solid 1px teal; width:360px;height:110px;padding:3px;position:relative;">
                                                                    <div id="note" onmouseover="my_function();">Tanda tangan harus di dalam kotak</div>
                                                                    <canvas id="the_canvas" width="350px" height="100px"></canvas>
                                                                </div>

                                                                <div style="margin:10px;">
                                                                    <input type="hidden" id="id_spj" name="id_spj" value="<?php echo $thr_tkd->id;?>">
                                                                    <input type="hidden" id="tahun" name="tahun" value="<?php echo $tahun;?>">
                                                                    
                                                                    <input type="hidden" id="signature" name="signature">
                                                                    <button type="button" id="clear_btn" class="btn btn-danger" data-action="clear"><span class="glyphicon glyphicon-remove"></span> Clear</button>
                                                                    <button type="submit" id="save_btn" class="btn btn-primary" data-action="save-png"><span class="glyphicon glyphicon-ok"></span> Submit</button>
                                                                </div>
                                                            </div>
                                                       

                                                    </div>

                                                <br>
                                                 <form>

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
