<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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

        .btn-acc-cuti{
            padding:5px 10px;
            background-color: #3ED890;
            color:white;
            border-radius:5px;
            text-decoration:none;
            border: none;
        }

        .btn-acc-cuti:hover{
            padding:5px 10px;
            background-color: #27B372;
            color:white;
            border-radius:5px;
            text-decoration:none;
            border: none;
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
                            $id_pegawai = $this->session->userdata('id_pegawai');


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
                                            <li class="breadcrumb-item active">Main Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php

                                    $list_bulan = array_bulan();

                                    $periode_bulan = $this->session->userdata('periode_bulan');
                                    $periode_tahun = $this->session->userdata('periode_tahun');
                                    $id_pkm_sess   = $this->session->userdata('id_pkm');


                                    $tgl_dari_session = '2025-11-01';
                                    $tgl_sampai_session = '2025-11-30';



                                   // print_array($pegawai);
                               ?>




                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">

                                        <h4 class="header-title">Data Absensi Pegawai</h4>
                                        <br>

                                        <div class="table-responsive mt-3">
                                            <table class="table table-sm mb-0 table-bordered">
                                                 <thead>
                                                        <tr>
                                                            <th class="text-center" data-sort="no">No</th>
                                                            <th class="text-center" data-sort="nama_pegawai">Nama Pegawai</th>
                                                            <th class="text-center" data-sort="telat">Telat</th>
                                                            <th class="text-center" data-sort="p_awal">P. Awal</th>
                                                            <th class="text-center" data-sort="izin">Izin</th>
                                                            <th class="text-center" data-sort="sakit">Sakit</th>
                                                            <th class="text-center" data-sort="sakit_dgn_surat">Sakit Dgn Surat</th>
                                                            <th class="text-center" data-sort="cuti">Cuti</th>
                                                            <th class="text-center" data-sort="status">Status</th>
                                                        </tr>
                                                 </thead>
                                                <tbody>
                                                  <?php 

                                                
//print_array($pegawai);
                                                    $no = 1;
                                                    foreach ($pegawai as $rekap){

                                                            $nama = $rekap->nama;
                                                            $telat = $rekap->telat;
                                                            $pulang_awal = $rekap->pulang_awal;
                                                            $sakit = $rekap->sakit;
                                                            $izin = $rekap->izin;
                                                            $cuti = $rekap->cuti;
                                                            $sakit_dgn_sk = $rekap->sakit_dgn_sk;
                                                            $status = $rekap->status;


                                                            $pin = '2227';

                                                            
                                                            if($status==2){
                                                                $flag_status = '<span class="badge bg-danger">Belum Direkap</span>';
                                                            }else if($status==0){
                                                                $flag_status = '<span class="badge bg-warning">Belum Sesuai</span>';
                                                            }else if($status==1){
                                                                $flag_status = '<span class="badge bg-success">Sesuai</span>';
                                                            }
                                                            
                                                        

                                                        echo' <tr>
                                                                <td class="text-center">'.$no.'</td>
                                                                <td class="text-left"><a href="'.base_url('admin/presensi/view_absensi/'.$rekap->id_pegawai.'/'.$pin).'">'.$nama.'</a></td>
                                                                <td class=" text-center">'.$telat.'</td>
                                                                <td class="text-center">'.$pulang_awal.'</td>
                                                                <td class="text-center">'.$izin.'</td>
                                                                <td class="text-center">'.$sakit.'</td>
                                                                <td class="text-center">'.$sakit_dgn_sk.'</td>
                                                                <td class="text-center">'.$cuti.'</td>
                                                                <td class="text-center">'.$flag_status.'</td>


                                                                </tr>';

                                                            $no += 1;

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


        <script src="<?php echo base_url();?>assets/new/js/vendor.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/app.min.js"></script>
         <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
         <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- demo end -->

        <script>

           
           $(document).on("click", ".approve", function () {
                    let id = $(this).data("id");

                    Swal.fire({
                        title: 'Setujui Pengajuan Cuti?',
                        text: "Pastikan data sudah benar.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Setujui!'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.ajax({
                                url: "<?php echo site_url('admin/cuti/approve'); ?>",
                                type: "POST",
                                data: { id: id },
                                dataType: "json",
                                success: function (res) {
                                    if (res.status === true) {

                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Pengajuan cuti berhasil di ACC.',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload(); // refresh tabel
                                        });

                                    } else {

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: res.message
                                        });

                                    }
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan pada server.'
                                    });
                                }
                            });

                        }
                    });
                });


        </script>




    </body>
</html>
