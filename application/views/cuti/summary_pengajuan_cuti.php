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

        .dokumen-cuti img{
           max-width: 50%;
           padding: 10px;
           border: 1px solid #EEE;
           margin-bottom: 10px;
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

                      
                        $id_pegawai         = $cuti[0]->id_pegawai;
                        $id_pengganti         = $cuti[0]->id_pengganti;
                        $id_cuti         = $cuti[0]->id;
                        $jenis_cuti         = $cuti[0]->jenis_cuti;
                        $alasan_cuti         = $cuti[0]->alasan_cuti;
                        $status_akhir         = $cuti[0]->status_akhir;
                        $created_at         = $cuti[0]->created_at;
                        


                        $tahun_list         = [2025, 2026];
                        $rekap_hak_cuti         = $this->acm->get_rekap_cuti_pegawai_by_id($id_pegawai, $tahun_list);
                        $approval         = $this->acm->getApprovalCuti($id_cuti);

                        $pengaju         = $this->acm->getInfoPenggantiCuti($id_pegawai);
                        $pengganti         = $this->acm->getInfoPenggantiCuti($id_pengganti);

                        $photoCuti = $this->acm->getDokumentCuti($id_cuti);
                       // print_array($cuti );

                         $message            = $this->session->flashdata('success');
                        

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
                                            <li class="breadcrumb-item active">Summary Pengajuan Cuti</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Summary Pengajuan Cuti </h4>
                                     <?php 
                                    if($message !=''){
                                        echo '<div class="alert alert-success">'.$message.'</div>';
                                    }?>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->



                            <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card border-bawah-danger">
                                                <div class="card-body ">

                                                    <div class="d-flex align-items-center">
                                                        <div class="w-100 overflow-hidden">
                                                            <h5 class="mt-0"><?php echo $pengaju[0]->nama;?></h5>
                                                            <h5 class="m-0 fw-normal cta-box-title"><?php echo $pengaju[0]->jabatan;?>   -
                                                            <b><?php echo $pengaju[0]->puskesmas;?></b> </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- end card-body -->
                                        </div>
                                            <!-- end card-->
                                    </div> <!-- end col-->

                                      <div class="col-sm-8">
                                        <div class="card border-bawah-danger">
                                                <div class="card-body ">

                                                          <?php
                                                            if($status_akhir =='proses' || $status_akhir =='draft' ){
                                                            
                                                          ?>
                                                                <a href="<?php echo base_url();?>cuti/cancel/<?= $id_cuti ?>" class="btn btn-danger  float-end ms-2"  onclick="return confirm('Apakah anda ingin membatalkan cuti ini?');">Batal</a>
                                               
                                                                <a href="<?php echo base_url();?>cuti/edit/<?= $id_cuti; ?>" class="btn btn-info float-end">Ubah</a>

                                                                <?php } ?>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td width="250" class="fw-bold">Tanggal Pengajuan</td>
                                                                <td width="20">:</td>
                                                                <td><?php echo format_full($cuti[0]->tgl_pengajuan);?></td>
                                                            </tr>

                                                            <tr>
                                                                <td class="fw-bold">Jenis Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti[0]->jenis_cuti;?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Alasan Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti[0]->alasan_cuti;?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Tanggal Mulai</td>
                                                                <td>:</td>
                                                                <td><?php echo format_full($cuti[0]->tgl_mulai);?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold">Tanggal Selesai</td>
                                                                <td>:</td>
                                                                <td><?php echo format_full($cuti[0]->tgl_selesai);?></td>
                                                            </tr>

                                                            
                                                              <tr>
                                                                <td class="fw-bold">Lama Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti[0]->lama_cuti;?> hari</td>
                                                            </tr>

                                                            <tr>
                                                                <td class="fw-bold">Alamat Selama Cuti</td>
                                                                <td>:</td>
                                                                <td><?php echo $cuti[0]->alamat_cuti;?></td>
                                                            </tr>
                                                          
                                                            <tr>
                                                                <td class="fw-bold">Pegawai Pengganti</td>
                                                                <td>:</td>
                                                                <td> <?= $pengganti[0]->nama; ?> - <?= $pengganti[0]->nip; ?>
                                                                    
                                                                </td>
                                                            </tr>

                                                              <tr>
                                                                <td class="fw-bold">Delegasi Tugas</td>
                                                                <td>:</td>
                                                                <td>- <?php echo str_replace(",", "<br>- ",$cuti[0]->delegasi_tugas);?></td>
                                                            </tr>


                                                          
                                                        </table>

                                                        <h5>Sisa Cuti</h5>
                                                        <table class="table table-bordered text-center">
                                                            <tr class="bg-info-lighten">
                                                                
                                                                <th>Hak Cuti Tahun</th>
                                                                <th class="text-dark">Jumlah</th>
                                                                <th class="text-danger">Digunakan</th>
                                                                <th class="text-warning">Pending</th>
                                                                <th class="text-success">Sisa Akhir</th>
                                                            </tr>
                                                            <?php foreach($rekap_hak_cuti as $tahun => $cuti): ?>
                                                                <tr>
                                                                    <td class="text-center"><?= $tahun ?></td>
                                                                    <td class="text-center"><?= $cuti['hak'] ?></td>
                                                                    <td class="text-center"><?= $cuti['terpakai'] ?></td>
                                                                    <td class="text-center"><?= $cuti['reserved'] ?></td>
                                                                    <td class="text-center fw-bold"><?= $cuti['sisa'] ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </table>
                                                        
                                                        <strong>ket :</strong> <br>
                                                        - <span class="text-danger">Digunakan : </span> Cuti yang sudah disetujui oleh Kasubbag TU / Kepala Puskesmas Cilincing <br>
                                                         - <span class="text-warning">Pending : </span> Cuti yang masih dalam proses pengajuan (belum disetujui) <br>
                                                         - <span class="text-success">Sisa Akhir : </span> Sisa Cuti yang dapat digunakan
                                                        <br><br>
                                                         <p>Sisa cuti akan tetap memotong hak cuti meskipun pengajuan masih dalam status <strong>pending</strong> <br>
                                                         Hak cuti akan dikembalikan jika  cuti dibatalkan atau pengajuan ditolak
                                                        </p>

                                                </div>
                                            <!-- end card-body -->
                                        </div>
                                         
                                    </div> <!-- end col-->

                                     <div class="col-sm-4">
                                        <div class="card border-bawah-danger">
                                                <div class="card-body ">

                                                          <h4 class="header-title mb-2">Recent Activity</h4>

                                                            <div data-simplebar style="max-height: 500px;"> 
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

                                                          
                                                             <div class="mt-4 dokumen-cuti">
                                                                <h4>Dokumen Pendukung Cuti</h4>

                                                                
                                                                <?php

                                                                        if(empty($photoCuti)){
                                                                                echo 'Tidak ada dokumen pendukung cuti';
                                                                        }else{

                                                                            foreach ($photoCuti as $photo) {
                                                                               
                                                                                echo '
                                                                                <a href="'.base_url().$photo->file_path.'" target="_blank"><img src="'.base_url().$photo->file_path.'" class="photo-cuti"></a>';
                                                                            }
                                                                        }
                                                                    ?>

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

        <!-- Todo js -->
        <script src="<?php echo base_url();?>assets/new/js/ui/component.todo.js"></script>


        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->


<!-- 
        <script>
            $(document).ready(function() {
                $('#uploadForm').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);

                    // Menampilkan status upload
                    $('#status').html('Meng-upload file...');

                    // Mengirim file menggunakan AJAX
                    $.ajax({
                        url: '<?php echo base_url();?>cuti/upload_dokumen_cuti', // Ganti dengan URL endpoint server untuk menerima file
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#status').html('File berhasil diupload.');
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('#status').html('Terjadi kesalahan saat mengupload file.');
                        }
                    });
                });
            });
            </script> -->

    </body>
</html>