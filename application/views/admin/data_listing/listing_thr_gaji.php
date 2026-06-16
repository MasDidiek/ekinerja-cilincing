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
        .active{
            font-weight: bold;
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
                                    <h4 class="page-title">Listing THR Gaji</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                     

                            <div class="col-xxl-12col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                      
                                   
                                      
                                      <table id="data-gaji" class="table table-sm dt-responsive mt-2 nowrap w-100">
                                                <thead class="bg-light">
                                                      <tr>

                                                          <th>No</th>
                                                          <th>Nama Pegawai</th>

                                                          <th>NIP</th>
                                                          <th>Gaji Pokok</th>
                                                          <th>Tunj. Suami</th>
                                                          <th>Tunj. Anak 1</th>
                                                          <th>Tunj. Anak 2</th>
                                                          <th>Pajak</th>
                                                           <th>THR TKD</th>
                                                            <th>THR GAJI</th>

                                                          <th>Total</th>
                                                          <th>No Rekening</th>
                                                          <th>TTD SPJ</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody class="list form-check-all">
                                                  <?php

                                                         $total_bruto = 0;
                                                         $total_pajak = 0;

                                                         $total_thp = 0;

                                                          $no = 1;
                                                          foreach ($datalist as $peg){

                                                          $nama = $peg->nama;

                                                          $jabatan = $peg->jabatan;



                                                          $ttd_spj = $peg->ttd_spj;

                                                          if($ttd_spj  == ''){
                                                             $flag_status = '<span class="badge badge-warning-lighten">Belum </span>';
                                                             $btn_check = '<a href="'.base_url().'admin/listing_tkd/cekSesuai/'.$peg->id.'" class="text-info float-end"> <i class="mdi mdi-account-edit me-1"></i></a>';

                                                          }else{
                                                              $flag_status = '<span class="badge badge-success-lighten">Sudah</span>';
                                                               $btn_check = '';
                                                          }

                                                             $total_bruto = $total_bruto+0;
                                                             $total_pajak = $total_pajak+0;

                                                             $total_thp = 0;

                                                              echo' <tr>
                                                                      <td class="text-center ">'.$no.'</td>
                                                                      <td class="text-left  nama_pegawai">   '.$peg->nama.'
                                                                      </td>
                                                                      <td class="text-center">'.$peg->nip.'</td>
                                                                      <td class="text-center  tkd_pokok">'.rupiah($peg->gaji_pokok).'</td>
                                                                      <td class="text-end">
                                                                       <strong>'.rupiah($peg->tunj_suami).'</strong>  </td>
                                                                      <td class="text-end">'.rupiah($peg->tunj_anak1).'</td>
                                                                      <td class="text-end">'.rupiah($peg->tunj_anak2).'</td>
                                                                      <td class="text-end">0</td>
                                                                      <td class="text-end"> <strong>'.rupiah($peg->thr_tkd).'</strong></td>
                                                                      <td class="text-end"> <strong>'.rupiah($peg->thr_gaji).'</strong></td>
                                                                      <td class="text-end"> <strong>'.rupiah($peg->total).'</strong></td>
                                                                      <td class="text-center">'.$peg->no_rekening.'</td>
                                                                      <td class="text-center">'.$flag_status.'</td>

                                                                      </tr>';

                                                                  $no += 1;

                                                          }

                                                       ?>



                                                    <tr>
                                                        <th>Total</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th><?php echo rupiah($total_bruto);?></th>
                                                        <th><?php echo rupiah($total_pajak);?></th>

                                                        <th><?php echo rupiah($total_thp);?></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>


                                                  </tbody>

                                              </table>

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

                     <!-- Datatables js -->
        <script src="<?php echo base_url();?>assets/new/js/vendor/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.bootstrap5.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/responsive.bootstrap5.min.js"></script>

            <script>
            $(document).ready(function() {
                $('#data-gaji').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "lengthMenu": [10, 25, 50, 100],
                    "ordering": true,
                    "searching": true,
                    "language": {
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "paginate": {
                            "first": "Pertama",
                            "last": "Terakhir",
                            "next": "Next",
                            "previous": "Prev"
                        },
                        "zeroRecords": "Data tidak ditemukan"
                    }
                });
            });
            </script>

    </body>
</html>
