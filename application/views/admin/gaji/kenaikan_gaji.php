<!DOCTYPE html>
<html lang="en">
<?php $this->load->view('layout/section/header'); ?>
<!-- Datatables css -->
<link href="<?php echo base_url(); ?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />

<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": false}'>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        <?php $this->load->view('layout/section/sidebar'); ?>
        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                <?php $this->load->view('layout/section/topbar'); ?>

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

                    $bulan_filter    = $this->session->userdata('bulan_tmt');
                    $tanggal_recount = $this->session->userdata('tanggal_recount');


                    ?>


                    <div class="col-xxl-12 col-lg-12">
                        <div class="card widget-flat">
                            <div class="card-body text-left">
                                <h4>Datalist Riwayat Gaji</h4>
                                <br>





                                <table id="datatable-buttons" class="table dt-responsive nowrap mt-3 w-100">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">TMT</th>
                                            <th class="text-center">TMT Terakhir</th>
                                            <th class="text-center">Masa Kerja</th>
                                            <th class="text-center">Gaji Pokok</th>
                                            <th class="text-center">Pengali </th>
                                            <th class="text-center">TKD Pokok </th>

                                            <th class="text-center">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php

                                        $no = 1;

                                        $tahunNow = date('Y');
                                        $bulanNow = date('m');
                                        $bulanLalu = $bulanNow - 1;

                                        $dateCount = '2026-02-28';

                                        foreach ($pegawai as $row) {
                                            $tgl_masuk = $row->tgl_masuk;
                                            $tahunMasuk = date('Y', strtotime($tgl_masuk));
                                            if ($tahunMasuk % 2 == 0) {
                                                //genap
                                                $class = 'text-warning fw-bold';
                                            } else {
                                                //ganjil
                                                $class = 'text-muted';
                                            }

                                            $masa_kerja =  hitungMasaKerja($tgl_masuk,  $dateCount);
                                            $gajiTerakhir = $this->RiwayatGaji_model->getLastRiwayatGaji($row->id_pegawai);
                                            //print_array($gajiTerakhir);
                                            if ($gajiTerakhir) {
                                                $tmt_terakhir = $gajiTerakhir->tmt;
                                                $gaji_pokok = $gajiTerakhir->gaji_pokok;
                                                $pengali = $gajiTerakhir->pengali;
                                                $besaran_tkd_pokok = $gajiTerakhir->total_gaji;
                                            }
                                            echo '
                                            <tr>
                                              <td>' . $no++ . '</td>
                                              <td>' . $row->nama . '</td>
                                              <td class="' . $class . '">' . format_view($row->tgl_masuk) . '</td>
                                              <td>' . format_view($tmt_terakhir) . '</td>
                                              <td class="text-end">' . $masa_kerja['years'] . ' tahun ' . $masa_kerja['months'] . ' bulan</td>
                                              
                                              <td class="text-end">' . rupiah($gaji_pokok) . '</td>
                                              <td class="text-center">' . $pengali . '</td>
                                              <td class="text-end">' . rupiah($besaran_tkd_pokok) . '</td>
                                            
                                              <td></td>
                                            </tr>
                                           ';
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
                    <script>
                        document.write(new Date().getFullYear())
                    </script> © Hyper - Coderthemes.com
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
    <?php $this->load->view('layout/section/theme-setting'); ?>


    <!-- bundle -->
    <script src="<?php echo base_url(); ?>assets/new/js/vendor.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/new/js/app.min.js"></script>

    <!-- Todo js -->
    <script src="<?php echo base_url(); ?>assets/new/js/ui/component.todo.js"></script>


    <script src="<?php echo base_url(); ?>assets/new/js/pages/demo.toastr.js"></script>
    <!-- demo end -->




</body>

</html>