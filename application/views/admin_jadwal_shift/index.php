<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css" />


    <body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": false}'>
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
                            $info   = $this->session->flashdata('message');
                            $bagian = $this->input->get('bagian')? $this->input->get('bagian') : 'rb';


                            if($bagian == 'rb') {
                                $header_table = ' <tr>
                                                    <th>Puskesmas</th>
                                                    <th>Penanggung Jawab</th>
                                                    <th>Action</th>
                                                </tr>';
                            }else{
                                $header_table = ' <tr>
                                                    <th>Poli</th>
                                                    <th>Penanggung Jawab</th>
                                                    <th>Action</th>
                                                </tr>';
                            }
                         ?>

                            <div class="col-xxl-12col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                        <h4>Jadwal Dinas Pegawai</h4>
                                        <br>


                                        <a href="<?php echo base_url().'admin_jadwal_shift/index';?>?bagian=rb" class="btn btn-sm <?php echo $bagian == 'rb' ? 'btn-primary' : 'btn-outline-light text-dark'; ?>">RB</a>
                                        <a href="<?php echo base_url().'admin_jadwal_shift/index';?>?bagian=ugd" class="btn btn-sm <?php echo $bagian == 'ugd' ? 'btn-primary' : 'btn-outline-light text-dark'; ?>">UGD 24 Jam</a>
                                        <a href="<?php echo base_url().'admin_jadwal_shift/index';?>?bagian=lab" class="btn btn-sm <?php echo $bagian == 'lab' ? 'btn-primary' : 'btn-outline-light text-dark'; ?>">Laboratorium</a>
                                        <a href="<?php echo base_url().'admin_jadwal_shift/index';?>?bagian=driver" class="btn btn-sm <?php echo $bagian == 'driver' ? 'btn-primary' : 'btn-outline-light text-dark'; ?>">Driver</a>



                                        <table class="table  table-hover mt-3">
                                            <thead class="table-light">
                                                <?php echo $header_table; ?>
                                            </thead>
                                            <tbody>

                                                <?php
                                               if($bagian=='rb'){
                                                    foreach ($row as $puskesmas): ?>
                                                    <tr>
                                                        <td><?php echo $puskesmas->nama; ?></td>
                                                        <td><?php echo $puskesmas->pj_rb; ?></td>
                                                        <td>
                                                            <a href="<?php echo site_url('admin_jadwal_shift/list_pegawai') . '?bagian=rb&id_puskesmas=' . $puskesmas->id_puskesmas; ?>" class="btn btn-sm btn-primary">View List Pegawai</a>
                                                        </td>
                                                    </tr>

                                                    <?php endforeach;
                                                }else{
                                                    foreach ($row as $poli): ?>
                                                    <tr>
                                                        <td><?php echo $poli->nama_poli; ?></td>
                                                        <td><?php echo $poli->pj_poli; ?></td>
                                                        <td>
                                                            <a href="<?php echo site_url('admin_jadwal_shift/list_pegawai') . '?bagian=' . $bagian . '&id_poli=' . $poli->id; ?>" class="btn btn-sm btn-primary">View List Pegawai</a>
                                                        </td>
                                                    </tr>

                                                    <?php endforeach;
                                                }
                                                ?>


                                            </tbody>
                                        </table>


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


        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->

                <!-- Datatables js -->
        <script src="<?php echo base_url();?>assets/new/js/vendor/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.bootstrap5.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/responsive.bootstrap5.min.js"></script>


        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.buttons.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/buttons.bootstrap5.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/buttons.html5.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/buttons.flash.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/buttons.print.min.js"></script>




        <!-- Datatable Init js -->
        <script src="<?php echo base_url();?>assets/new/js/pages/demo.datatable-init.js"></script>


        <script type="text/javascript">

                $(".lihat_pegawai").click(function(){

                    var id_bagian = $(this).val();
                    $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>admin_jadwal_shift/ajax_get_pegawai_shift",
                            data:"id_bagian="+id_bagian,
                            success:function(msg){
                                $("#list_pegawai").html(msg);
                            }

                        });

                    });


        </script>




    </body>
</html>
