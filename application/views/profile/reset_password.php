<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>

<style>


    </style>

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
                        // print_array($pegawai);

                        $upload_status  = $this->session->flashdata('message_status');
                        $upload_message = $this->session->flashdata('message');
                       


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
                                    <h4 class="page-title">Reset Password </h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body"> 
                                        <?php if($this->session->flashdata('error')): ?>
                                            <div class="alert alert-danger">
                                                <?php echo $this->session->flashdata('error'); ?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if($this->session->flashdata('success')): ?>
                                            <div class="alert alert-success">
                                                <?php echo $this->session->flashdata('success'); ?>
                                            </div>
                                            <?php endif; ?>

                                        <form action="<?php echo base_url();?>profile/update_password" method="POST">

                                            <div class="mb-3">
                                                <label class="form-label">Current Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="current_password" id="current_password" required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password">
                                                        👁
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">New Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="new_password" id="new_password" required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password">
                                                        👁
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Confirm New Password</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password" required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_new_password">
                                                        👁
                                                    </button>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                            </form>
                                    </div>
                                </div>
                            </div>

                        </div>




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
                document.querySelectorAll('.toggle-password').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const target = document.getElementById(this.dataset.target);

                        if (target.type === "password") {
                            target.type = "text";
                            this.innerHTML = "🙈";
                        } else {
                            target.type = "password";
                            this.innerHTML = "👁";
                        }
                    });
                });
                </script>


    </body>
</html>
