<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

 <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">


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


                        <?php
                            $message = $this->session->flashdata('message');
                            $id_pegawai = $this->session->userdata('id_pegawai');
                            $usergroup = $this->session->userdata('usergroup');
                            
                           // print_array($this->session->userdata);

//print_array($cuti_pegawai);
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
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Cuti Pegawai</a></li>
                                            <li class="breadcrumb-item active">Pengajuan Cuti</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Pengajuan Cuti</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                      

                             <div class="row">
                                    <div class="col-md-8">
                                        
                                        <div class="card">
                                            <div class="card-body">

                                            <a href="<?php echo base_url();?>admin/cuti/table_pengajuan_cuti" class="btn btn-info" target="_blank">Lihat table cuti</a>
                                            <div id="calendar"></div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                            <div class="card">
                                            <div class="card-body">
                                            <h5>Detail Cuti </h5>
                                            <div id="detailCuti">
                                                <p class="text-muted">Klik tanggal di kalender</p>
                                            </div>
                                            </div>
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


        <script src="<?php echo base_url();?>assets/new/js/vendor.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/app.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
         <script>
            document.addEventListener('DOMContentLoaded', function () {

                const calendarEl = document.getElementById('calendar');
                let selectedDate = null;

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    selectable: true,

                    // 🔥 ambil data REAL + range aktif kalender
                    events: function(info, successCallback, failureCallback) {

                    $.ajax({
                        url: '<?= base_url("admin/cuti/calendar_cuti") ?>',
                        dataType: 'json',
                        data: {
                            start: info.startStr,
                            end: info.endStr
                        },
                            success: function(res) {
                            successCallback(res);
                        },
                            error: function() {
                            failureCallback();
                        }
                    });

                    },

                    // klik tanggal (bukan event)
                    dateClick: function(info) {
                    selectedDate = info.dateStr;
                    loadDetailCuti(selectedDate);
                    highlightSelectedDate();
                    },

                    // klik bar event
                    eventClick: function(info) {
                    selectedDate = info.event.startStr;
                    loadDetailCuti(selectedDate);
                    highlightSelectedDate();
                    }

                });

                calendar.render();

                // =========================
                // Helper functions
                // =========================

                function loadDetailCuti(tanggal) {

                    $('#detailCuti').html('<p class="text-muted">Loading...</p>');

                    $.ajax({
                    url: '<?= base_url("admin/cuti/get_cuti_by_tanggal") ?>',
                    data: { tanggal: tanggal },
                    success: function(html) {
                        $('#detailCuti').html(html);
                    }
                    });
                }

                function highlightSelectedDate() {

                    $('.fc-daygrid-day').removeClass('fc-selected-day');

                    if (!selectedDate) return;

                    $('.fc-daygrid-day[data-date="' + selectedDate + '"]')
                    .addClass('fc-selected-day');
                }

                });

                    

            </script>
                            
               <script>
                        function approveCutiAjax(id, role) {
                        Swal.fire({
                            title: 'Setujui Pengajuan Cuti?',
                            text: 'Pastikan data cuti sudah benar.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Setujui',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {

                            if (!result.isConfirmed) return;

                            fetch("<?= base_url('admin/cuti/ajax_setujui_cuti') ?>", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id_pengajuan: id,
                                role_approval: role
                            })
                            })
                            .then(res => res.json())
                            .then(res => {

                            if (res.status) {
                                Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                showConfirmButton: false,
                                timer: 2500
                                });

                                // 🔥 refresh panel kanan & kalender (tanpa reload page)
                                if (typeof selectedDate !== 'undefined' && selectedDate) {
                                loadDetailCuti(selectedDate);
                                }

                                if (typeof calendar !== 'undefined') {
                                calendar.refetchEvents();
                                }

                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }

                            })
                            .catch(() => {
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                            });

                        });
                        }
                        </script>


    </body>
</html>
