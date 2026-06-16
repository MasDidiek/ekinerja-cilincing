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
                        $info = $this->session->flashdata('message');


                        $list_bulan = array_bulan();


                      
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

                     
                           <form action="<?php echo base_url();?>admin/listing_gaji/datalisting/<?= $status_pegawai; ?>" method="get">
                                <div class="col-xxl-12col-lg-12">
                                    <div class="card widget-flat">
                                        <div class="card-body text-left">
                                            <h4>Data Listing Gaji</h4>
                                            <br>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <a href="<?php echo base_url();?>admin/listing_gaji/datalisting/non_pns" class="button non_pns active"> <i class="uil-user-circle"></i> NON PNS</a>
                                                    <a href="<?php echo base_url();?>admin/listing_gaji/datalisting/pppk_pw?bulan=<?= $bulan; ?>&tahun=<?= $tahun; ?>" class="button pppk"> <i class="uil-user-square"></i>  PPPK PW</a>
                                                </div>
                                                <div class="col-md-2">
                                                        <select  name="bulan" id="bulan"  class="form-control"  data-choices="" >
                                                            <option value="">Bulan</option>
                                                            <?php
                                                                for ($b=0; $b < count($list_bulan) ; $b++) {

                                                                    $no_bulan = $b+1;

                                                                    if($bulan == $b){
                                                                        $selc = 'selected';
                                                                    }else{
                                                                        $selc = '';
                                                                    }


                                                                    echo '<option value="'.$b.'" '.$selc.'>'.$b.' - '.$list_bulan[$b].'</option>';
                                                                }
                                                                ?>
                                                        </select>

                                                        </div>
                                                        <div class="col-md-2">
                                                            <select  name="tahun" id="tahun" class="form-control"  data-choices="" >
                                                                <option value="">Tahun</option>
                                                                    <?php
                                                                        for ($b=2023; $b < 2030 ; $b++) {



                                                                            if($tahun == $b){
                                                                                $selc2 = 'selected';
                                                                            }else{
                                                                                $selc2 = '';
                                                                            }

                                                                            echo '<option value="'.$b.'" '.$selc2.'> '.$b.'</option>';
                                                                        }
                                                                        ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                                <button class="btn btn-info filter_periode">Submit</button>
                                                                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#import-modal"><i class="uil-download-alt"></i> Import File</button>
                                                        </div>
                                            
                                             </div>

                                        

                                        </div>
                                    </div>
                                </div> <!-- end col-->

                            </form>

                <div id="import-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                      <div class="modal-content">
                      <?php
                           // echo form_open_multipart(base_url() . 'admin/import_data/import_file');
                            echo form_open_multipart(base_url() . 'admin/listing_gaji/preview_import');?>


                          <div class="modal-header">
                              <h4 class="modal-title" id="standard-modalLabel">Import Data </h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                          </div>
                          
                          
                          <div class="modal-body">

                            <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="bulan">Bulan</label>
                                        <input type="number" class="form-control" name="bulan" value="<?= date('m'); ?>" max="12">
                                    </div>
                                    <div class="col-md-5">
                                        <label for="bulan">Tahun</label>
                                        <input type="number" class="form-control"  name="tahun" value="<?= date('Y'); ?>" max="2030">
                                    </div>
                                </div>
                            </div>
                                <strong> file (*.xlxs) : </strong>   

                                <div class="col-sm-12">
                                    <label class="form-label">File input</label>
                                    <input  name="file"  type="file" class="form-control" id="inputGroupFile04">
                                </div>                                
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Import File</button>
                          </div>

                          <?php  echo form_close();
                                ?>
                      </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->


                            <div class="col-xxl-12col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                      
                                    <h4>Periode :  <strong><?php echo $nm_bulan ;?> <?php echo $tahun ;?></strong> </h4>

                                    <a href="<?= base_url() ?>admin/listing_gaji/view_ttd/non_pns?bulan=<?= $bulan; ?>&tahun=<?= $tahun ?>" class="btn btn-primary float-end mb-2" target="_blank">Lihat TTD</a>
                                    <div class="clearfix"></div>
                                      
                                      <table id="data-gaji" class="table table-sm dt-responsive mt-2 nowrap w-100">
                                                <thead class="bg-light">
                                                      <tr>

                                                          <th>No</th>
                                                          <th>Nama Pegawai</th>

                                                          <th>NIK</th>
                                                          <th>Gaji Pokok</th>
                                                          <th>Tunj. Suami</th>
                                                          <th>Tunj. Anak 1</th>
                                                          <th>Tunj. Anak 2</th>
                                                          <th>Pajak</th>

                                                          <th>Netto</th>
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

                                                                $total_bruto = $total_bruto+$peg->bruto;
                                                                $total_pajak = $total_pajak+$peg->pajak;

                                                                $total_thp = $total_thp+$peg->netto;

                                                              echo' <tr>
                                                                      <td class="text-center ">'.$no.'</td>
                                                                      <td class="text-left">   '.$peg->nama.'
                                                                      </td>
                                                                      <td class="text-center">'.$peg->nik.'</td>
                                                                      <td class="text-center  tkd_pokok">'.rupiah($peg->gaji_pokok).'</td>
                                                                      <td class="text-end">
                                                                       <strong>'.rupiah($peg->tunj_suami).'</strong>  </td>
                                                                      <td class="text-end">'.rupiah($peg->tunj_anak1).'</td>
                                                                      <td class="text-end">'.rupiah($peg->tunj_anak2).'</td>
                                                                      <td class="text-end">'.rupiah($peg->pajak).'</td>
                                                                      <td class="text-end"> <strong>'.rupiah($peg->netto).'</strong></td>
                                                                      <td class="text-center">'.$peg->no_rekening.'</td>
                                                                      <td class="text-center">'.$flag_status.'</td>

                                                                      </tr>';

                                                                  $no ++;

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
