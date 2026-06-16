<!DOCTYPE html>
<html lang="en">
<?php $this->load->view('layout/section/header'); ?>
<!-- Datatables css -->
<link href="<?php echo base_url(); ?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/new/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css" />



<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":true, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": false}'>
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
                    $info = $this->session->flashdata('message');


                    $list_bulan = array_bulan();


                    $periode_bulan = $this->session->userdata('periode_bulan');
                    $periode_tahun = $this->session->userdata('periode_tahun');
                    $id_pkm_sess   = $this->session->userdata('id_pkm');
                    $id_pj_sess = $this->session->userdata('id_pj');
                    $id_user_validator   = $this->session->userdata('id_pegawai');

                    if ($periode_bulan == '') {
                        $bulan = date('m');
                        $tahun = date('Y');
                    } else {
                        $bulan = $periode_bulan;
                        $tahun = $periode_tahun;
                    }

                    $periode = $tahun . '-' . $bulan;
                    $periode = date('Y-m', strtotime($periode));

                    $nm_bulan = getBulan($bulan);

                    //print_array($detail_validator);
                    $id_puskesmas = $detail_validator->id_validator;
                    $usergroup = $detail_validator->usergroup;


                    ?>

                    <div class="col-xxl-12col-lg-12">
                        <div class="card widget-flat">
                            <div class="card-body text-left">
                                <h4>Capaian Kinerja Pegawai</h4>
                                <br>


                                <div class="row mt-2 mb-3">
                                    <?php if ($usergroup <= 2) { ?>
                                        <div class="col-md-3">
                                            <select name="id_validator" id="validator" class="form-control">

                                                <?php
                                                foreach ($validator as $pj) {

                                                    $id_pj = $pj->id_pegawai;
                                                    $nama_pj   = $pj->nama;


                                                    if ($id_pj_sess == $id_pj) {
                                                        echo '<option value="' . $id_pj . '" selected>' . $nama_pj . '</option>';
                                                    } else {
                                                        echo '<option value="' . $id_pj . '">' . $nama_pj . '</option>';
                                                    }
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    <?php } ?>
                                    <div class="col-md-2">
                                        <select name="bulan" id="bulan" class="form-control" data-choices="">
                                            <option value="">Bulan</option>
                                            <?php
                                            for ($b = 0; $b < count($list_bulan); $b++) {

                                                $no_bulan = $b + 1;

                                                if ($bulan == $b) {
                                                    $selc = 'selected';
                                                } else {
                                                    $selc = '';
                                                }


                                                echo '<option value="' . $b . '" ' . $selc . '>' . $b . ' - ' . $list_bulan[$b] . '</option>';
                                            }
                                            ?>
                                        </select>

                                    </div>
                                    <div class="col-md-2">
                                        <select name="tahun" id="tahun" class="form-control" data-choices="">
                                            <option value="">Tahun</option>
                                            <?php
                                            for ($b = 2023; $b < 2030; $b++) {



                                                if ($periode_tahun == $b) {
                                                    $selc2 = 'selected';
                                                } else {
                                                    $selc2 = '';
                                                }

                                                echo '<option value="' . $b . '" ' . $selc2 . '> ' . $b . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>





                                </div><!--end grid-->

                                <form action="<?php echo base_url(); ?>admin/penilaian_kinerja/update_capaian" method="post">
                                    <input type="hidden" name="tahun" value="<?= $periode_tahun ?>">
                                    <input type="hidden" name="bulan" value="<?= $periode_bulan ?>">

                                    <button type="" submit" class="btn btn-success mb-2 float-end"><i class="uil-refresh"></i> Update Data</button>
                                    <div class="clearfix"></div>

                                    <table id="datatable-buttons" class="table table-sm table-bordered dt-responsive nowrap w-100">
                                        <thead class="bg-light">
                                            <tr>

                                                <th class="text-center ">No</th>
                                                <th>Nama Pegawai</th>
                                                <th>Jabatan</th>
                                                <th class="text-center ">Telat</th>
                                                <th class="text-center ">P Awal</th>
                                                <th class="text-center ">Izin</th>
                                                <th class="text-center ">Sakit</th>
                                                <th class="text-center ">Sakit dgn Surat</th>
                                                <th class="text-center ">Alpha</th>
                                                <th class="text-center ">Bobot Aktifitas</th>
                                                <th class="text-center ">Perilaku</th>
                                                <th class="text-center ">Serapan</th>
                                                <th class="text-center ">Total</th>
                                                <th class="text-center ">Check
                                                    <input type="checkbox" name="checkAll" checked id="checkAll">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;

                                            if (!empty($capaian)) :
                                                foreach ($capaian as $row) :

                                                    // Biar NULL jadi 0
                                                    $telat           = $row->telat ?? 0;
                                                    $pulang_awal     = $row->pulang_awal ?? 0;
                                                    $izin            = $row->izin ?? 0;
                                                    $sakit           = $row->sakit ?? 0;
                                                    $sakit_dgn_sk    = $row->sakit_dgn_sk ?? 0;
                                                    $alpha           = $row->alpha ?? 0;

                                                    $bobot_aktifitas = $row->bobot_aktifitas ?? 0;
                                                    $perilaku        = $row->perilaku ?? 0;
                                                    $serapan         = $row->serapan ?? 0;
                                                    $total           = $total_capaian = $row->total_capaian ?? 0;


                                                    if ($total_capaian > 97) {
                                                        $classBadge = "text-success";
                                                    } elseif ($total_capaian < 97 && $total_capaian  > 90) {
                                                        $classBadge = "text-info";
                                                    } elseif ($total_capaian < 91 && $total_capaian  > 80) {
                                                        $classBadge = "text-warning";
                                                    } else {
                                                        $classBadge = "text-danger";
                                                    }



                                            ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++; ?></td>
                                                        <td> <a href="<?= base_url() . 'admin/presensi/view_absensi/' . $row->id_pegawai . '/' . $row->pin; ?>"> <?= $row->nama; ?></a></td>
                                                        <td><?= $row->jabatan; ?></td>

                                                        <td class="text-center"><?= $telat; ?></td>
                                                        <td class="text-center"><?= $pulang_awal; ?></td>
                                                        <td class="text-center"><?= $izin; ?></td>
                                                        <td class="text-center"><?= $sakit; ?></td>
                                                        <td class="text-center"><?= $sakit_dgn_sk; ?></td>
                                                        <td class="text-center"><?= $alpha; ?></td>

                                                        <td class="text-center"><?= number_format($bobot_aktifitas, 2); ?></td>
                                                        <td class="text-center"><?= number_format($perilaku, 2); ?></td>
                                                        <td class="text-center"><?= number_format($serapan, 2); ?></td>
                                                        <td class="text-center fw-bold <?= $classBadge; ?>"><?= number_format($total, 2); ?></td>

                                                        <td class="text-center">
                                                            <input type="checkbox" class="form-check-input check-item" name="check_id[]" checked value="<?= $row->id_pegawai ?>">
                                                        </td>
                                                    </tr>
                                                <?php
                                                endforeach;
                                            else : ?>
                                                <tr>
                                                    <td colspan="14" class="text-center text-muted">
                                                        Data tidak ditemukan
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>


                                    </table>

                                </form>



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

    <script>
        // Ambil checkbox utama dan semua checkbox item
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.check-item');

        // Saat checkbox utama diklik
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Saat salah satu checkbox item diubah
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                // Jika semua checkbox item tercentang, centang juga "checkAll"
                checkAll.checked = document.querySelectorAll('.check-item:checked').length === checkboxes.length;
            });
        });



        $("#bulan").change(function() {
            var bulan = $(this).val();
            var tahun = $("#tahun").val();

            $.ajax({

                type: "POST",
                dataType: "html",
                url: "<?php echo base_url(); ?>admin/penilaian_kinerja/set_session_periode",
                data: "bulan=" + bulan + "&tahun=" + tahun,
                success: function(msg) {
                    window.location.reload();
                    //$("#modal-form").html(msg);
                    //console.log(msg);
                }

            });

        });


        $("#tahun").change(function() {
            var tahun = $(this).val();
            var bulan = $("#bulan").val();
            $.ajax({

                type: "POST",
                dataType: "html",
                url: "<?php echo base_url(); ?>admin/penilaian_kinerja/set_session_periode",
                data: "bulan=" + bulan + "&tahun=" + tahun,
                success: function(msg) {
                    window.location.reload();
                    //$("#modal-form").html(msg);
                    //console.log(msg);
                }

            });

        });

        $("#validator").change(function() {
            var id_pj = $(this).val();

            $.ajax({

                type: "POST",
                dataType: "html",
                url: "<?php echo base_url(); ?>admin/presensi/set_session_validator",
                data: "id_pj=" + id_pj,
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