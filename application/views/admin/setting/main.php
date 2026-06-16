<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>

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
                                    $message = $this->session->flashdata('message');
                                    $function = $this->uri->segment(3);
                                ?>

            
                  <div class="row">
                    <div class="col-lg-12 d-flex align-items-stretch">
                      <div class="card w-100">
                        <div class="card-body p-4">
                      
                        <?php    echo $message;?>
                          <?php
                          if($function=='menu'){
                              $this->load->view('admin/setting/content_menu');
                            }else if($function=='menu_level_2'){
                                $this->load->view('admin/setting/content_submenu');
                            }else if($function=='hari_kerja'){
                              $this->load->view('admin/setting/content_hari_kerja');
                            }else if($function=='hari_libur'){
                              $this->load->view('admin/setting/content_hari_libur');
                            }else if($function=='usergroup'){
                              $this->load->view('admin/setting/content_usergroup');
                            }else if($function=='usergroup_hak_akses'){
                              $this->load->view('admin/setting/usergroup_hak_akses');
                            }else if($function=='mesin_absensi'){
                              $this->load->view('admin/setting/content_mesin_absensi');
                            }else if($function=='list_user'){
                              $this->load->view('admin/setting/list_user_mesin');
                            }else if($function=='shift_kerja'){
                              $this->load->view('admin/setting/list_shift_kerja');
                            }else if($function=='edit_shift_kerja'){
                              $this->load->view('admin/setting/edit_shift_kerja');
                            }else if($function=='create_initial_shift'){
                              $this->load->view('admin/setting/create_initial_shift');
                            }else if($function=='jabatan'){
                              $this->load->view('admin/setting/list_jabatan');
                            }else if($function=='master_gaji'){
                              $this->load->view('admin/setting/master_gaji');
                            }
                          ?>

                        </div>
                      </div>
                    </div>
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
     

        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->

                <!-- Datatables js -->
        <script src="<?php echo base_url();?>assets/new/js/vendor/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.bootstrap5.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/responsive.bootstrap5.min.js"></script>

        <!-- Datatable Init js -->
        <script src="<?php echo base_url();?>assets/new/js/pages/demo.datatable-init.js"></script>


        <?php
    if($message !=''){
    ?>
    <script>$.NotificationApp.send("Berhasil","<?php echo $message;?>","top-right","rgba(0,0,0,0.2)","success")</script>

    <?php } ?>
    </body>
</html>
