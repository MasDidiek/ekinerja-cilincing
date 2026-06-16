<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/css/snackbar.css" rel="stylesheet" type="text/css" />


<style>
  
     .image-preview {
            display: inline-block;
            margin: 10px;
            position: relative;
        }
        .image-preview img {
            width: 150px;
            height: auto;
            border: 1px solid #ccc;
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
                    //print_array($rekap_capaian_kinerja);

                      
                     //print_array($cuti);
                    // exit;
                        $id_pegawai           = $cuti->id_pegawai;
                        $id_pengganti         = $cuti->id_pengganti;
                        $id_cuti         = $cuti->id;
                        $jenis_cuti         = $cuti->jenis_cuti;
                        $alasan_cuti         = $cuti->alasan_cuti;
                        $created_at         = $cuti->created_at;
                        $status_approval         = $cuti->status_approval;
                        $id_pegawai_approval         = $cuti->id_pegawai_approval;
                        $role_approval         = $cuti->role_approval;

                        $status_akhir = $cuti->status_akhir;

                        $id_pegawai_validator = $this->session->userdata("id_pegawai");
                        $message            = $this->session->flashdata('success');
                        $usergroup = $this->session->userdata('usergroup');
                        if($usergroup==3 || $usergroup==4 ){

                         $group = 'kapustu';
                        }else if($usergroup==1){ //Kapuskec
                         $group = 'kapus';
                        }else if($usergroup==2){
                            //ktu
                        $group = 'ktu';
                        }
                        
                    // print_array($this->session->userdata);

                        
                        $approval         = $this->acm->getApprovalCuti($id_cuti);

                        //print_array($approval );

                        $pengaju         = $this->acm->getInfoPenggantiCuti($id_pegawai);
                        $pengganti         = $this->acm->getInfoPenggantiCuti($id_pengganti);
                       //print_array($cuti );

                        
                       $currentApproval = null;

                        foreach ($approval as $row) {
                            if ($row['status'] == 'pending') {
                                $currentApproval = $row;
                                break; // karena sudah urut level, langsung ambil yg pertama
                            }
                        }

                        if ($currentApproval) {
                            $id_pending = $currentApproval['id_pegawai_approval'];
                        }


                        $id_login = $this->session->userdata('id_pegawai');
                        if ($currentApproval && $id_login == $currentApproval['id_pegawai_approval']) {
                            $boleh_approve = true;
                        } else {
                            $boleh_approve = false;
                        }


                     ?>


                    <!-- Start Content-->
                    <div class="container-fluid">

                    
                        <div class="row">

                            <!-- ================= HEADER ================= -->
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">Detail Pengajuan Cuti</h4>
                                        <small class="text-muted">
                                            <?= $cuti->nama ?> • <?= $cuti->jenis_cuti ?>
                                        </small>
                                    </div>
                                    <a href="<?= base_url('dashboard/pengajuan_cuti_pending') ?>" class="btn btn-light">
                                        ← Kembali
                                    </a>
                                </div>
                            </div>

                            <!-- ================= KONTEN KIRI ================= -->
                            <div class="col-sm-8">

                                <!-- 👤 PEMOHON -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-secondary mb-3">👤 Pemohon Cuti</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr><td width="180" class="fw-semibold">Nama</td><td width="30">:</td><td><?= $cuti->nama ?></td></tr>
                                            <tr><td class="fw-semibold">Jabatan</td><td>:</td><td><?= $pengaju->jabatan ?></td></tr>
                                            <tr><td class="fw-semibold">Tanggal Pengajuan</td><td>:</td><td><?= format_full($cuti->tgl_pengajuan) ?></td></tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- 📅 INFORMASI CUTI -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-secondary mb-3">📅 Informasi Cuti</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td width="180" class="fw-semibold">Jenis Cuti</td><td width="30">:</td>
                                                <td><span class="badge bg-info"><?= $cuti->jenis_cuti ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Periode</td><td>:</td>
                                                <td><?= format_full($cuti->tgl_mulai) ?> – <?= format_full($cuti->tgl_selesai) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Lama Cuti</td><td>:</td>
                                                <td><span class="badge bg-success"><?= $cuti->lama_cuti ?> hari</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Alasan</td><td>:</td>
                                                <td><?= ucfirst(strtolower($cuti->alasan_cuti)) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Alamat Selama Cuti</td><td>:</td>
                                                <td><?= $cuti->alamat_cuti ?></td>
                                            </tr>
                                        </table>
                                       

                                    </div>
                                </div>

                                <!-- 🔁 PENGGANTI & DELEGASI -->
                                <?php
                                    $delegasi = preg_split("/[\r\n,]+/", $cuti->delegasi_tugas);
                                    $delegasi = array_filter(array_map('trim', $delegasi));
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-secondary mb-3">🔁 Pengganti & Delegasi</h6>

                                        <div class="mb-3">
                                            <strong>Pegawai Pengganti</strong><br>
                                            <?= $pengganti->nama ?><br>
                                            <small class="text-muted"><?= $pengganti->jabatan ?></small>
                                        </div>

                                        <strong>Delegasi Tugas</strong>
                                        <ul class="ps-3 mb-0">
                                            <?php foreach($delegasi as $d): ?>
                                                <li><?= htmlspecialchars($d) ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                </div>

                                
                                <!-- ✅ TINDAKAN APPROVAL -->
                                <?php if($boleh_approve): ?>
                                <div class="card mb-3 border-success">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase text-secondary mb-3">Tindakan Persetujuan</h6>

                                        

                                         <?php if($status_akhir=='proses'){?>
                                            <button
                                            class="btn text-danger"
                                            onclick="cancelCutiAjax(<?= $cuti->id ?>)">
                                           <i class="uil-cancel"></i>  Batalkan
                                        </button>

                                        <a href="<?= base_url('admin/cuti/edit_cuti/'.$cuti->id) ?>" class="btn btn-info mx-2">
                                            <i class="mdi mdi-pencil-outline"></i> Edit
                                        </a>

                                        <?php } ?>

                                        
                                        <button
                                            class="btn btn-warning"
                                            data-id="<?= $cuti->id ?>">
                                           <i class="uil-exclamation-circle"></i>   Tolak
                                        </button>

                                        <button
                                            class="btn btn-success"
                                            onclick="approveCutiAjax(<?= $cuti->id ?>, '<?= $group ?>')">
                                           <i class="mdi mdi-check-circle-outline"></i>    Setujui
                                        </button>


                                    </div>
                                    
                                </div>
                                <?php endif; ?>

                                

                                <!-- 📊 SISA CUTI -->
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-secondary mb-3">📊 Rekap Sisa Cuti</h6>

                                        <table class="table table-bordered text-center">
                                            <tr class="bg-info-lighten">
                                                <th>Tahun</th>
                                                <th>Hak</th>
                                                <th class="text-danger">Digunakan</th>
                                                <th class="text-warning">Pending</th>
                                                <th class="text-success">Sisa Akhir</th>
                                            </tr>
                                            <?php foreach($rekap_hak_cuti as $tahun => $cuti): ?>
                                            <tr>
                                                <td><?= $tahun ?></td>
                                                <td><?= $cuti['hak'] ?></td>
                                                <td><?= $cuti['terpakai'] ?></td>
                                                <td><?= $cuti['reserved'] ?></td>
                                                <td class="fw-bold"><?= $cuti['sisa'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </table>

                                        <small class="text-muted">
                                            <strong>Keterangan:</strong><br>
                                            <span class="text-danger">Digunakan</span>: sudah disetujui<br>
                                            <span class="text-warning">Pending</span>: masih proses<br>
                                            <span class="text-success">Sisa Akhir</span>: sisa yang dapat digunakan
                                        </small>
                                    </div>
                                </div>

                            </div>

                            <!-- ================= KONTEN KANAN ================= -->
                            <div class="col-sm-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-secondary mb-3">🕒 Riwayat Persetujuan</h6>

                                        <?php if($status_akhir =='dibatalkan'){?>
                                            
                                                <div class="alert alert-danger">
                                                    Cuti ini telah dibatalkan
                                                </div>
                                        <?php } ?>


                                        <div data-simplebar style="max-height: 500px;">
                                            <div class="timeline-alt pb-0">

                                                <!-- Pengajuan -->
                                                <div class="timeline-item">
                                                    <i class="mdi mdi-upload bg-info-lighten text-info timeline-icon"></i>
                                                    <div class="timeline-item-info">
                                                        <strong class="text-info d-block">Pengajuan Cuti</strong>
                                                        <small><?= $jenis_cuti ?> – “<?= $alasan_cuti ?>”</small>
                                                        <p class="mb-0">
                                                            <small class="text-muted"><?= timeAgo($created_at) ?></small>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Approval -->
                                                <?php foreach($approval as $row):
                                                    $style = statusStyle($row['status']);
                                                ?>
                                                <div class="timeline-item">
                                                    <i class="mdi <?= $style['icon'] ?> <?= $style['bg'] ?> <?= $style['text'] ?> timeline-icon"></i>
                                                    <div class="timeline-item-info">
                                                        <strong class="<?= $style['text'] ?>">
                                                            Persetujuan <?= ucfirst($row['role_approval']) ?>
                                                        </strong>
                                                        <br>
                                                        <small><?= $row['nama'] ?: '-' ?></small>
                                                        <p class="mb-0">
                                                            <span class="<?= $style['text'] ?>"><?= $style['label'] ?></span><br>
                                                            <?php if($row['approved_at']): ?>
                                                                <small class="text-muted"><?= timeAgo($row['approved_at']) ?></small>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>






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

         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
         const IS_DETAIL_CUTI = true;
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
                timer: 2000
            });

            // ======================
            // KONTEN HALAMAN DETAIL
            // ======================
            if (typeof IS_DETAIL_CUTI !== 'undefined' && IS_DETAIL_CUTI) {
                setTimeout(() => {
                    location.reload(); // 🔥 reload supaya tombol approval hilang
                }, 1200);
                return;
            }

            // ======================
            // KONTEN HALAMAN CALENDAR
            // ======================
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

     function cancelCutiAjax(id) {
        Swal.fire({
            title: 'Batalkan Pengajuan Cuti?',
            text: 'Apakah anda yakin untuk membatalkan cuti ini?.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, batalkan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {

            if (!result.isConfirmed) return;

                fetch("<?= base_url('admin/cuti/ajax_cancel_cuti') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_pengajuan: id
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
                timer: 2000
            });

            // ======================
            // KONTEN HALAMAN DETAIL
            // ======================
            if (typeof IS_DETAIL_CUTI !== 'undefined' && IS_DETAIL_CUTI) {
                setTimeout(() => {
                    location.reload(); // 🔥 reload supaya tombol approval hilang
                }, 1200);
                return;
            }

            // ======================
            // KONTEN HALAMAN CALENDAR
            // ======================
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
