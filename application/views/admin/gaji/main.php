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


                    if ($bulan_filter == '') {
                        $bulan = date('m');
                    } else {
                        $bulan = $bulan_filter;
                    }


                    if ($tanggal_recount == '') {
                        $tanggal_recount = date('d-m-Y');
                    }


                    $info = $this->session->flashdata('message');
                    $list_bulan = array_bulan();
                    ?>


                    <div class="col-xxl-12 col-lg-12">
                        <div class="card widget-flat">
                            <div class="card-body text-left">
                                <h4>Data Gaji Pegawai Non PNS</h4>
                                <br>



                                <div class="mb-2">

                                    <form action="<?= base_url('admin/gaji/generate_bulanan'); ?>" method="post">
                                        <div class="row align-items-end">
                                            <div class="col-md-2">
                                                <label class="form-label">Tanggal Proses</label>
                                                <input type="date" name="tanggal_proses" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary">
                                                    Generate
                                                </button>

                                                <a href="<?= base_url(); ?>admin/gaji/kenaikan_gaji?bulan=03" class="btn btn-success">Lihat Kenaikan Gaji</a>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="clearfix"></div>



                                <table id="datatable-buttons" class="table dt-responsive nowrap w-100">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="w-1">No.</th>

                                            <th>TMT</th>
                                            <th>Masa Kerja</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Status</th>
                                            <th>Gaji Pokok</th>
                                            <th>Pengali </th>
                                            <th>TKD Pokok </th>


                                            <th>Update On</th>

                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php

                                        $no = 1;

                                        $tahunNow = date('Y');
                                        $bulanNow = date('m');
                                        $bulanLalu = $bulanNow - 1;

                                        foreach ($pegawai as $peg) {


                                            $nip = $peg->nip;
                                            $tmt = $peg->tmt;
                                            $nama = $peg->nama_pegawai;
                                            $masa_kerja_tahun = $peg->masa_kerja_tahun;
                                            $masa_kerja_bulan = $peg->masa_kerja_bulan;
                                            // $kategori_masa_kerja = $peg->kategori_masa_kerja; 
                                            $gaji_pokok = $peg->gaji_pokok;
                                            $pengkalian = $peg->pengali;
                                            $tkd_pokok = $peg->total_gaji; //total gaji adalah tkd pokok
                                            $update_on = $peg->created_at;



                                            $jabatan = $peg->jabatan;



                                            $bulanTMT = date('m', strtotime($tmt));
                                            $tahunTMT = date('Y', strtotime($tmt));


                                            if ($bulanLalu == $bulanTMT) {
                                                $class = ' fw-bold text-warning';
                                            } else {
                                                $class = '';
                                            }

                                            $status_k = '-';
                                            // switch ($status_kawin) {
                                            //     case 4:
                                            //         $status_k = 'K0';
                                            //         break;

                                            //     case 3:
                                            //         $status_k = 'K1';
                                            //         break;
                                            //     case 2:
                                            //         $status_k = 'K2';
                                            //         break;
                                            //     case 1:
                                            //         $status_k = 'K3';
                                            //         break;
                                            //     default:
                                            //         $status_k = '-';
                                            //         break;
                                            // }

                                            //if($tahunTMT %2==1){
                                            echo ' <tr>
                                                                                    <td class="">' . $no . ' </td>
                                                                                    
                                                                                    <td class="text-center ' . $class . '">' . format_semi($tmt) . '</td>
                                                                                    <td class="text-center">' . $masa_kerja_tahun . ' tahun ' . $masa_kerja_bulan . ' bulan</td>
                                                                                    <td> 
                                                                                    <a href="' . base_url() . 'admin/gaji/riwayat_gaji/' . $peg->id_pegawai . '">' . $nama . '</a></td>
                                                                                     <td> ' . $jabatan . '</td>
                                                                                     <td> ' . $status_k . '</td>
                                                                                    <td class="text-end"> ' . rupiah($gaji_pokok) . '</td>
                                                                                    <td class="text-center"> <strong>' . $pengkalian . '</strong></td>
                                                                                    <td class="text-center">' . rupiah($tkd_pokok) . '</td>
                                                                                   
                                                                                    <td class="text-center">' . $update_on . '</td>
                                                                                 
                                                                                </tr>';

                                            $no += 1;
                                            // }



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

    <!-- Datatables js -->
    <script src="<?php echo base_url(); ?>assets/new/js/vendor/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/new/js/vendor/dataTables.bootstrap5.js"></script>
    <script src="<?php echo base_url(); ?>assets/new/js/vendor/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/new/js/vendor/responsive.bootstrap5.min.js"></script>

    <!-- Datatable Init js -->
    <script src="<?php echo base_url(); ?>assets/new/js/pages/demo.datatable-init.js"></script>



    <script>
        $("#bulan").change(function() {
            var bulan = $(this).val();

            $.ajax({

                type: "POST",
                dataType: "html",
                url: "<?php echo base_url(); ?>admin/gaji/set_session_bulan",
                data: "bulan=" + bulan,
                success: function(msg) {
                    window.location.reload();
                    //$("#modal-form").html(msg);
                    //console.log(msg);
                }

            });

        });
    </script>



</body>

</html>