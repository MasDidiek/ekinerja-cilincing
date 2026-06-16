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

                      
                   // print_array($cuti);
                    // exit;
                        $id_pegawai           = $cuti->id_pegawai;
                        $id_pengganti         = $cuti->id_pengganti;
                        $id_cuti         = $cuti->id;
                        $jenis_cuti         = $cuti->jenis_cuti;
                        $alasan_cuti         = $cuti->alasan_cuti;
                        $created_at         = $cuti->created_at;
                        $status_akhir         = $cuti->status_akhir;

                        $status_approval         = $cuti->status_approval;
                        $id_pegawai_approval     = $cuti->id_pegawai_approval;
                        $role_approval         = $cuti->role_approval;
                        $id_pegawai_validator = $this->session->userdata("id_pegawai");
                        $message            = $this->session->flashdata('success');
                        


                        
                        $approval         = $this->acm->getApprovalCuti($id_cuti);

                        $pengaju         = $this->acm->getInfoPenggantiCuti($id_pegawai);
                        $infoPengganti         = $this->acm->getInfoPenggantiCuti($id_pengganti);
                       // print_array($cuti );

                        

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
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Cuti</a></li>
                                            <li class="breadcrumb-item active">Detail Pengajuan Cuti</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Detail Pengajuan Cuti </h4>
                                     <?php 
                                    if($message !=''){
                                        echo '<div class="alert alert-success">'.$message.'</div>';
                                    }?>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->



                            <div class="row">
                             

                                      <div class="col-sm-8">
                                        <div class="card border-bawah-danger">
                                                <div class="card-body ">

                                                        <h5>Informasi Pengajuan Cuti</h5>

                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td width="250" class="fw-bold">Tanggal Pengajuan</td>
                                                                <td width="20">:</td>
                                                                <td><?php echo format_full($cuti->tgl_pengajuan);?></td>
                                                            </tr>

                                                            <tr>
                                                                <td class="fw-bold">Jenis Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti->jenis_cuti;?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Alasan Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti->alasan_cuti;?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Tanggal Mulai</td>
                                                                <td>:</td>
                                                                <td><?php echo format_full($cuti->tgl_mulai);?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Tanggal Selesai</td>
                                                                <td>:</td>
                                                                <td><?php echo format_full($cuti->tgl_selesai);?></td>
                                                            </tr>

                                                            
                                                              <tr>
                                                                <td class="fw-bold">Lama Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti->lama_cuti;?> hari</td>
                                                            </tr>

                                                            <tr>
                                                                <td class="fw-bold">Alamat Selama Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti->alamat_cuti;?></td>
                                                            </tr>
                                                          
                                                          
                                                              <tr>
                                                                <td class="fw-bold">Delegasi Tugas</td>
                                                                <td>:</td>
                                                                <td>- <?php echo str_replace(",", "<br>- ",$cuti->delegasi_tugas);?></td>
                                                            </tr>


                                                          
                                                        </table>
                                                       
                                                            <h5 class="text-warning">Pengganti Cuti</h5>
                                                            <table class="table table-sm table-borderless">
                                                                <tr>
                                                                    <th width="250" >Nama</th>
                                                                    <td width="20">:</td>
                                                                    <td> <?= $infoPengganti->nama ? : '-' ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Jabatan</th>
                                                                    <td>:</td>
                                                                    <td> <?= $infoPengganti->jabatan ? : '-' ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Unit Kerja</th>
                                                                    <td>:</td>
                                                                    <td><?= $infoPengganti->puskesmas ? : '-' ?></td>
                                                                </tr>
                                                            </table>
   
                                                        <hr>
                                                     <?php if ($status_approval == 'pending' && $id_pegawai_validator == $id_pegawai_approval) : ?>
                                                    <center>
                                                        <button id="btn-approve" class="btn btn-success" onclick="approveCutiAjax(<?= $id_cuti ?>, '<?= $role_approval ?>')">
                                                            <i class="fa-solid fa-circle-check"></i> Setujui
                                                        </button>

                                                        <button id="btn-reject" class="btn btn-danger" onclick="rejectCutiAjax(<?= $id_cuti ?>, '<?= $role_approval ?>')">
                                                            <i class="fa-solid fa-circle-xmark"></i> Tolak
                                                        </button>

                                                    </center>
                                                <?php endif; ?>




                                                </div>
                                            <!-- end card-body -->
                                        </div>
                                         
                                    </div> <!-- end col-->

                                     <div class="col-sm-4">
                                        <div class="card border-bawah-danger">
                                                <div class="card-body ">

                                                          <h4 class="header-title mb-2">Recent Activity</h4>

                                                            <div data-simplebar style="max-height: 419px;"> 
                                                                <div class="timeline-alt pb-0">

                                                                    <!-- Step 0 : Pengajuan -->
                                                                    <div class="timeline-item">
                                                                        <i class="mdi mdi-upload bg-info-lighten text-info timeline-icon"></i>
                                                                        <div class="timeline-item-info">
                                                                            <a href="#" class="text-info fw-bold mb-1 d-block">Pengajuan Cuti</a>
                                                                            <small><?= $jenis_cuti ?> “<?= $alasan_cuti ?>”</small>
                                                                            <p class="mb-0 pb-2">
                                                                                <small class="text-muted"><?= timeAgo($created_at) ?></small>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Step approval -->
                                                                    <?php foreach($approval as $row): 
                                                                        $style = statusStyle($row['status']);
                                                                    ?>
                                                                        <div class="timeline-item">
                                                                            <i class="mdi <?= $style['icon'] ?> <?= $style['bg'] ?> <?= $style['text'] ?> timeline-icon"></i>
                                                                            <div class="timeline-item-info">
                                                                                <a href="#" class="fw-bold mb-1 d-block <?= $style['text'] ?>">
                                                                                    Persetujuan <?= ucfirst($row['role_approval']) ?>
                                                                                </a>
                                                                                <small><?= $row['nama'] ?: '-' ?></small>

                                                                                <p class="mb-0 pb-2">
                                                                                    <span class="<?= $style['text'] ?>">
                                                                                        <?= $style['label'] ?>
                                                                                    </span>
                                                                                    <?php if($row['approved_at']): ?>
                                                                                        <br><small class="text-muted"><?= timeAgo($row['approved_at']) ?></small>
                                                                                    <?php endif; ?>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>

                                                                </div>
                                                             </div>


                                                                
                                                 
                                                </div>
                                            <!-- end card-body -->
                                        </div>
                                         
                                    </div> <!-- end col-->

                                    
                                 
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

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                function lockButtons() {
                    document.getElementById('btn-approve')?.setAttribute('disabled', true);
                    document.getElementById('btn-reject')?.setAttribute('disabled', true);
                }

                function approveCutiAjax(id, role) {
                    Swal.fire({
                        title: 'Setujui sebagai pengganti Pengajuan Cuti?',
                        text: 'Pastikan data sudah benar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            lockButtons();

                            fetch("<?= base_url('cuti/ajax_setujui') ?>", {
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
                                            timer: 3000
                                        });

                                        setTimeout(() => location.reload(), 1000);
                                    } else {
                                        Swal.fire('Gagal', res.message, 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                                });
                        }
                    });
                }


                /*----------tolak cuti------------*/
                function rejectCutiAjax(id, role) {
                    Swal.fire({
                        title: 'Tolak Pengajuan Cuti',
                        input: 'textarea',
                        inputLabel: 'Alasan Penolakan',
                        inputPlaceholder: 'Masukkan alasan penolakan cuti...',
                        inputAttributes: {
                            'aria-label': 'Alasan penolakan'
                        },
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Alasan penolakan wajib diisi';
                            }
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Tolak',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            lockButtons();

                            fetch("<?= base_url('admin/cuti/tolak_ajax') ?>", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        id_pengajuan: id,
                                        role_approval: role,
                                        alasan: result.value
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
                                            timer: 3000
                                        });

                                        setTimeout(() => location.reload(), 1000);
                                    } else {
                                        Swal.fire('Gagal', res.message, 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                                });
                        }
                    });
                }
            </script>



    </body>
</html>
