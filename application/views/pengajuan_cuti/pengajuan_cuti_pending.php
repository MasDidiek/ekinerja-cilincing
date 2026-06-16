<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view("layout/section/header"); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">



    <body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
            <?php $this->load->view("layout/section/sidebar"); ?>
            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
                    <?php $this->load->view("layout/section/topbar"); ?>

                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">User Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Pengajuan Cuti Pending</h4>
                                </div>



                            </div>
                        </div>
                        <!-- end page title -->

                        <?php
                                $list_bulan = array_bulan();

                                $periode_bulan = $this->session->userdata(
                                    "periode_bulan"
                                );
                                $periode_tahun = $this->session->userdata(
                                    "periode_tahun"
                                );
                                $id_pkm_sess = $this->session->userdata("id_pkm");

                                $bulan = $periode_bulan;
                                $tahun = $periode_tahun;

                                // print_array($this->session->userdata);

                                $id_pegawai = $this->session->userdata("id_pegawai");
                                $nm_bulan = getBulan($bulan);
                                $periode = $tahun . "-" . $bulan;
                                $periode = date("Y-m", strtotime($periode));



                        ?>



                        <div class="row">
                            <div class="col-xl-12 col-xxl-12  col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-header">
                                        <h4 class="card-title">Menunggu Persetujuan Kapustu/Kasatpel</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <?php
                                            // print_array($cutiPegawai);
                                                    $offset = $this->uri->segment(4);
                                                    if($offset==''){
                                                        $offset = 0;
                                                    }

                                                    $no = 0;
                                                    for ($i=0; $i < count($cutiPegawai) ; $i++) {
                                                        $tgl_pengajuan = $cutiPegawai[$i]->tgl;
                                                        $nama_pegawai = $cutiPegawai[$i]->nama;
                                                        $tgl_dari = $cutiPegawai[$i]->tgl_dari;
                                                        $tgl_sampai = $cutiPegawai[$i]->tgl_sampai;
                                                        $hari_cuti = $cutiPegawai[$i]->hari_cuti;
                                                        $status = $cutiPegawai[$i]->status;
                                                        $alasan_cuti = $cutiPegawai[$i]->alasan_cuti;
                                                        $jns_cuti = $cutiPegawai[$i]->jns_cuti;
                                                        $id_cuti = $cutiPegawai[$i]->id;

                                                        $no = $i ;

                                                        if($status=='PEND0'){
                                                            $flag_status = '<span class="text-warning" title="pending menunggu acc pengganti"><i class="uil-info-circle"></i> Pending</span>';
                                                        }else if($status=='PEND1'){
                                                            $flag_status = '<span class="text-primary" title="pending menunggu acc Kapustu/Kasatpel"><i class="uil-info-circle"></i> Pending </span>';
                                                        }else if($status=='PEND2'){
                                                            $flag_status = '<span class="text-info" title="pending menunggu acc Ka. TU"><i class="uil-info-circle"></i> Pending</span>';
                                                        }else if($status=='PEND3'){
                                                            $flag_status = '<span class="text-success" title="pending menunggu acc Ka. TU"><i class="uil-info-circle"></i>  Pending</span>';
                                                        }else if($status=='APPROVE'){
                                                            $flag_status = '<span class="text-success" title="disetujui"><i class="uil-check"></i> Disetujui</span>';
                                                        }else if($status=='CANCEL'){
                                                            $flag_status = '<span class="text-danger" title="dibatalkan"><i class="uil-times-circle"></i> Dibatalkan</span>';
                                                        }else if($status=='REJECT'){
                                                            $flag_status = '<span class="text-danger" title="tidak disetujui"><i class="uil-exclamation-triangle"></i> </span>';
                                                        }

                                                        $span_class="text-info";
                                                        if ($jns_cuti==1){
                                                            $jenis_cuti = 'Cuti Tahunan';

                                                        }else if($jns_cuti==2){
                                                            $jenis_cuti = 'Cuti Melahirkan';
                                                            $span_class="text-success";
                                                        }else if($jns_cuti==3){
                                                            $jenis_cuti = 'Cuti Alasan Penting';
                                                            $span_class="text-warning";
                                                        }else if($jns_cuti==4){
                                                            $jenis_cuti = 'Cuti Sakit';
                                                            $span_class="text-danger";
                                                        }else if($jns_cuti==5){
                                                            $jenis_cuti = 'Cuti Besar';
                                                            $span_class="text-primary";
                                                        }else{
                                                            $jenis_cuti = 'Cuti Lainnya';
                                                            $span_class="text-secondary";
                                                        }

                                                                echo '
                                                                <div class="col-md-4">
                                                                    <div class="card p-2 border">
                                                                        <div class="widget-content">
                                                                          <span class="'.$span_class.' mb-0 float-start">'. $jenis_cuti .'</span>
                                                                          <br>
                                                                            <div class="widget-detail">
                                                                                <h4 class="mb-0">'. $nama_pegawai .'</h4>

                                                                            </div>


                                                                            <div class="widget-detail">
                                                                                <strong class="mb-0">'.format_full($tgl_dari).' s.d '.format_full($tgl_sampai).'</strong> ('.$hari_cuti.' Hari)
                                                                                <br><br>
                                                                                <p class="text-muted mb-0">'. $alasan_cuti .'</p>
                                                                            </div>


                                                                             <a href="'.base_url().'cuti/detail_pengajuan_cuti/'.$id_cuti.'" class="btn btn-primary btn-sm mt-2"> Detail</a>

                                                                        </div>
                                                                    </div>
                                                                  </div>';

                                                            }


                                                    ?>


                                        </div>

                                    </div>

                                </div>
                            </div> <!-- end col -->



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
        <?php $this->load->view("layout/section/theme-setting"); ?>

        <!-- bundle -->
        <script src="<?php echo base_url(); ?>assets/new/js/vendor.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/new/js/app.min.js"></script>

        <!-- Todo js -->
        <script src="<?php echo base_url(); ?>assets/new/js/ui/component.todo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

         <script src="<?php echo base_url(); ?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->





    </body>
</html>
