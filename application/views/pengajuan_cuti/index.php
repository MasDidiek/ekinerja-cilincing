<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/signature.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>

    <style>
        .my-table{
            width: 100%;
        }
        .my-table  td{
            padding:2px;
           
        }
        .btn-list {
            padding:10px 15px;
            text-align:center;
            border-bottom:1px solid #EEE;
            color:#666;
            margin-right:2px;
        }

        .active-btn{
            border-bottom:1px solid #66bad9;
            color:orange;
        }

        .cth{
          background-color:#63b3be;
        }

        .cb{
          background-color:#33e697;
        }

        .cap{
          background-color:#e65361;
        }
        .cs{
          background-color:#e6b433;
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
                                            <li class="breadcrumb-item active">Pengajuan Cuti</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Pengajuan Cuti</h4>
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

                                    $tanggal_cuti = $this->session->userdata('tanggal_cuti');
                                     $status_session = $this->session->userdata('status');
                                

                                                                
                                    $explod = explode("to", $tanggal_cuti);
                                    $from = format_view($explod[0]);
                                    $to   = format_view(@$explod[1]);

                               ?>



                       
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" class="dropdown-item">Refresh Report</a>
                                                <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                                            </div>
                                        </div>
                                        <h4 class="header-title">Pengajuan Cuti</h4>
                                        <br><br>

                                        <form action="<?php echo base_url();?>admin/pengajuan_cuti/search_cuti" method="post">
                                          <label for="from">Filter Pencarian Tanggal</label>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    
                                                    <input type="text" id="from" class="form-control" name="from" value="<?php echo $from;?>" placeholder="tanggal dari" autocomplete="off">
                                                
                                                </div>
                                                <div class="col-md-2">
                                            
                                                    <input type="text" id="to" name="to" class="form-control"  value="<?php echo $to;?>" placeholder="tanggal sampai"  autocomplete="off">
                                                </div>

                                                <div class="col-md-2">
                                                <button type="submit" id="search" class="btn btn-info"><i class="align-bottom ri-search-line me-1"></i> Search</button>
                                                </div>
                                        
                                            </div>
                                        </form>

                                        <div class="table-responsive mt-3">
                                            <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                                               <thead class="bg-light">
                                                     <tr>
                                                        <th>No.</th>
                                                         <th>Nama</th>
                                                         <th>Jenis Cuti</th>
                                                         <th>Tanggal Cuti</th>
                                                         <th>Hari Cuti</th>
                                                         <th>Alasan Cuti</th>
                                                         <th>Status Cuti</th>
                                                     </tr>
                                                 </thead>
                                                 <tbody>
                                                        <?php
                                                            for ($a=0; $a < count($list_cuti) ; $a++) { 
                                                                  $id = $list_cuti[$a]->id;
                                                                  $nama_pegawai = $list_cuti[$a]->nama;
                                                                  $hari_cuti = $list_cuti[$a]->hari_cuti;
                                                                  $tgl_dari = $list_cuti[$a]->tgl_dari;
                                                                  $tgl_sampai = $list_cuti[$a]->tgl_sampai;
                                                                  $alasan_cuti = $list_cuti[$a]->alasan_cuti;
                                                                  $jns_cuti = $list_cuti[$a]->jns_cuti;
                                                                  $status = $list_cuti[$a]->status;
                                                                  $jns_hak_cuti = $list_cuti[$a]->jns_hak_cuti;
                                                                  
                                                                  $no = $a+1;

                                                                      if ($jns_cuti==1) {
                                                                          $jenis_cuti = '<span class="badge cth">TAHUNAN</span>';
                                                                      }else if($jns_cuti==2){
                                                                          $jenis_cuti = '<span class="badge cb">BERSALIN</span>';
                                                                      }else if($jns_cuti==3){
                                                                          $jenis_cuti = '<span class="badge cap">ALASAN PENTING</span>';
                                                                      }else if($jns_cuti==4){
                                                                          $jenis_cuti = '<span class="badge cs">SAKIT</span>';
                                                                      }else if($jns_cuti==5){
                                                                          $jenis_cuti = '<span class="badge cbes">BESAR</span>';
                                                                      }else{
                                                                          $jenis_cuti = '<span class="badge cbs3">BERSALIN ANAK KE 3</span>';
                                                                      }


                                                                      if($jns_hak_cuti==2){
                                                                          $hak_cuti = '2024';
                                                                      }else{
                                                                          $hak_cuti =  '2025';
                                                                      }

                                                                

                                                                      if($tgl_dari != $tgl_sampai){
                                                                          $tgl_cuti =  '<strong>'.format_view($tgl_dari).'</strong>&nbsp; s/d &nbsp;<strong>'. format_view($tgl_sampai).'<strong>' ;
                                                                      }else{
                                                                          $tgl_cuti =   '<strong>'.format_view($tgl_dari).'</strong>';
                                                                      }
                                                                    

                                                                      $tahunCuti = date('Y', strtotime($tgl_dari));

                                                                      if($status=='APPROVE'){
                                                                          $flag_status = '<span class="text-success">APPROVE</span>';
                                                                      }else if($status=='CANCEL'){
                                                                          $flag_status = '<span class="btext-danger">CANCELED</span>';
                                                                      }else{
                                                                          $flag_status = '<span class="text-warning">'.$status.'</span>';
                                                                      }

                                                                 echo ' <tr>
                                                                            <td class="text-center">'.$no .'</td>
                                                                            <td>
                                                                            <a href="'.base_url().'admin/pengajuan_cuti/detail_cuti/'.$id.'">'.$nama_pegawai .'</a></td>
                                                                            <td class="text-center">'.$jenis_cuti .'</td>

                                                                            <td>'.$tgl_cuti.'</td>
                                                                            <td class="text-center">'.$hari_cuti.'</td>
                                                                            <td>'.word_limiter($alasan_cuti,5).'</td>
                                                                            <td class="text-center">'.$flag_status.'</td>
                                                                      </tr>';

                                                                }



                                                        ?>
                                                   
                                                 </tbody>
                                             </table>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->

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
        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->
 
        <!-- Datatables js -->
        <script src="<?php echo base_url();?>assets/new/js/vendor/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.bootstrap5.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/responsive.bootstrap5.min.js"></script>

        <!-- Datatable Init js -->
        <script src="<?php echo base_url();?>assets/new/js/pages/demo.datatable-init.js"></script>


        <script>
            $( function() {
                var dateFormat = "dd-mm-yy",
                from = $( "#from" )
                    .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3,
                    dateFormat:"dd-mm-yy",
                    })
                    .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                    }),
                to = $( "#to" ).datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3,
                    dateFormat:"dd-mm-yy",
                })
                .on( "change", function() {
                    from.datepicker( "option", "maxDate", getDate( this ) );
                });
            
                function getDate( element ) {
                var date;
                try {
                    date = $.datepicker.parseDate( dateFormat, element.value );
                } catch( error ) {
                    date = null;
                }
            
                return date;
                }
            } );
            </script>
            
    </body>
</html>
