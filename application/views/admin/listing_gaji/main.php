<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                            <!-- Datatables css -->
    <link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/new/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css" />
                                                    
    <style>
        .button{
            border: 1px solid #315ed8;
            font-size: 17px;
            padding: 10px 25px;
            border-radius: 3px;
        }
        .non_pns{
            border: 1px solid  #e78a4c;
            color: #e78a4c;
        }

         .pppk{
            border: 1px solid  #30be54;
            color: #30be54;
        }

        .btn-active{
            background-color: #4acf9c;
            color: #FFF;
        }
        .btn-white{
            background-color: #FFF;
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
                        $info = $this->session->flashdata('message');


                        $list_bulan = array_bulan();


                        $periode_bulan = $this->session->userdata('periode_bulan');
                        $periode_tahun = $this->session->userdata('periode_tahun');
                        $id_pkm_sess   = $this->session->userdata('id_pkm');
                        $jenis_pegawai = $this->uri->segment(3);
                        $id_user_validator   = $this->session->userdata('id_pegawai');

                        if($periode_bulan=='') {
                        $bulan = date('m');
                        $tahun = date('Y');

                        }else{
                            $bulan = $periode_bulan;
                            $tahun = $periode_tahun;
                        }

                        $periode = $tahun.'-'.$bulan;
                        $periode = date('Y-m', strtotime($periode));

                        $nm_bulan = getBulan($bulan);

        
                        ?>
                        
                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Listing Gaji</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Listing Gaji</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                     

                            <div class="col-xxl-12col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                        <h4>Data Listing Gaji</h4>
                                        <br>

                                        <a href="<?php echo base_url();?>admin/listing_gaji/datalisting/non_pns" class="button non_pns"> <i class="uil-user-circle"></i> NON PNS</a>
                                        <a href="<?php echo base_url();?>admin/listing_gaji/datalisting/pppk_pw" class="button pppk"> <i class="uil-user-square"></i>  PPPK PW</a>

                                    

                                    </div>
                                </div>
                            </div> <!-- end col-->
                        </div>
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


    </body>
</html>
