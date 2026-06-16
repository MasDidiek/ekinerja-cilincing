<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <style>
        .btn-xs{
            padding:3px 6px !important;
        }

        .modal-dialog{
            z-index:999;
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

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="<?php echo base_url();?>dashboard/my_dashboard">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="<?php echo base_url();?>absensi/my_absensi">My Absensi</a></li>
                                            <li class="breadcrumb-item active">Pengajuan Izin / Sakit</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Pengajuan Izin / Sakit</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php
                                       $message = $info= $this->session->flashdata('message');
                                
                                        $periode_bulan = $this->session->userdata('periode_bulan'); 
                                        $periode_tahun = $this->session->userdata('periode_tahun'); 
                                        
                            
                                        if($periode_bulan=='') {
                                            $bulan = date('m');
                                            $tahun = date('Y');
                            
                                        }else{
                                            $bulan = $periode_bulan;
                                            $tahun = $periode_tahun;
                                        }
                            
                                   
                                        
                                ?>


                        <div class="d-flex align-items-center bg-warning text-white p-2 mb-3 d-none loading">
                            <strong>Loading...</strong>
                            <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
                        </div>


                        <div class="row">

                            <div class="col-xxl-8  col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                        <h4>Riwayat Pengajuan Izin / Sakit</h4>
                                        <br>

                                            <h5>Pengajuan Izin</h5>
                                            <table class="table table-bordered table-sm">
                                                <thead class="bg-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Hari</th>
                                                            <th class="text-center">Tanggal</th>
                                                        
                                                            <th class="text-center">Jenis Izin</th>
                                                            <th>Alasan</th>
                                        
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list form-check-all">
                                                        


                                                            <?php
                                                                $no = 1;
                                                                foreach ($pengajuan_izin as $key => $value) {
                                                                    $tanggal_absen = $value->tanggal;
                                                                    $jenis_absen = $value->jenis_absen;
                                                                    $jns_izin = $value->jns_izin;
                                                                    $keterangan = $value->keterangan;
                                                                    $file_image = $value->file_image;
                                                                    $status = $value->status;
                                                                    $create_at = $value->create_at;
                                                                    $id = $value->id;

                                                       
                                                                    if($jns_izin == 1){
                                                                        $izinJns = '<span class="badge bg-info-lighten text-info">IZIN PENUH</span>';
                                                                    }else if($jns_izin == 2){
                                                                        $izinJns = '<span class="badge bg-warning-lighten text-danger">IZIN AWAL</span>';
                                                                    }else{
                                                                            $izinJns = '<span class="badge bg-warning-lighten text-danger">IZIN AKHIR</span>';
                                                                    }
                                                                    

                                                                    if($status==0){
                                                                        $status_absen = '<span class="badge bg-warning-lighten text-danger">Belum diperiksa</span>';
                                                                    }else{
                                                                        $status_absen = '<span class="badge bg-success-lighten text-success">Sudah diperiksa</span>';
                                                                    }

                                                                    
                                                                    echo '<tr>

                                                                            <td class="text-center">'.$no .'</td>
                                                                            <td class="text-center">  '.format_hari($tanggal_absen).'</td>
                                                                            <td class="text-center">'.format_slash($tanggal_absen).' </td>
                                                                    
                                                                            <td class="text-center"> '. $izinJns.'  </td>
                                                                            <td>  '. $keterangan.'  </td>
                                                                        
                                                                            <td class="text-center">  '. $status_absen.'  </td>
                                                                            <td class="text-center">';
                                                                            
                                                                                if($status==0){
                                                                                        echo ' <a href="'.base_url().'absensi/delete_pengajuan_izin_sakit/'.$id.'/'.$file_image.'" onClick="return confirm(\'Apakah anda yakin membatalkan pengajuan izin/sakit ini dan menghapusnya dari database?\')" class="text-danger" title="Hapus Pengajuan Izin/Sakit">
                                                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                                                        </a>';
                                                                                    }
                                                                        echo ' </td>

                                                                    </tr>';

                                                                    $no +=1;

                                                                }
                                                            ?>

                                            
                                            </tbody>
                                        </table>

                                         <h5>Pengajuan Sakit</h5>
                                            <table class="table table-bordered table-sm">
                                                <thead class="bg-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Hari</th>
                                                            <th class="text-center">Tanggal</th>
                                                            <th class="text-center">Jenis Pengajuan Sakit</th>
                                                            <th>Alasan</th>
                                        
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list form-check-all">
                                                        


                                                            <?php
                                                                $no = 1;
                                                                foreach ($pengajuan_sakit as $key => $value) {
                                                                    $tanggal_absen = $value->tanggal;
                                                                    $jenis_absen = $value->jenis_absen;
                                                                    $jns_sakit = $value->jns_sakit;
                                                                    $keterangan = $value->keterangan;
                                                                    $file_image = $value->file_image;
                                                                    $status = $value->status;
                                                                    $create_at = $value->create_at;
                                                                    $id = $value->id;

                                                             
                                                                   if($jns_sakit == 2){
                                                                        $jns_pengajuan_sakit = '<span class="badge bg-success">Dengan Surat Keterangan</span>';
                                                                    }else{
                                                                            $jns_pengajuan_sakit = '<span class="badge bg-warning">Tanpa Surat Keterangan</span>';
                                                                    }

                                                                    if($status==0){
                                                                        $status_absen = '<span class="badge bg-warning-lighten text-danger">Belum diperiksa</span>';
                                                                    }else{
                                                                        $status_absen = '<span class="badge bg-success-lighten text-success">Sudah diperiksa</span>';
                                                                    }

                                                                    
                                                                    echo '<tr>

                                                                            <td class="text-center">'.$no .'</td>
                                                                            <td class="text-center">  '.format_hari($tanggal_absen).'</td>
                                                                            <td class="text-center">'.format_slash($tanggal_absen).' </td>
                                                                    
                                                                            <td class="text-center"> '. $jns_pengajuan_sakit.'  </td>
                                                                            <td>  '. $keterangan.'  </td>
                                                                        
                                                                            <td class="text-center">  '. $status_absen.'  </td>
                                                                            <td class="text-center">';
                                                                            
                                                                                   if($file_image != ''){
                                                                                    echo '<button class="btn btn-sm btn-info" 
                                                                                                data-bs-toggle="modal" data-bs-target="#lihat-surat-modal"
                                                                                                data-img="'.base_url('uploads/surat_izin/'.$file_image).'">
                                                                                            Lihat Surat
                                                                                        </button> ';
                                                                                    }
                                                                                    

                                                                                    
                                                                                if($status==0){
                                                                                        echo ' <a href="'.base_url().'absensi/delete_pengajuan_izin_sakit/'.$id.'/'.$file_image.'" onClick="return confirm(\'Apakah anda yakin membatalkan pengajuan izin/sakit ini dan menghapusnya dari database?\')" class="btn btn-xs btn-danger " title="Hapus Pengajuan Izin/Sakit">
                                                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                                                        </a>';
                                                                                    }
                                                                        echo ' </td>

                                                                    </tr>';

                                                                    $no +=1;

                                                                }
                                                            ?>

                                            
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div> <!-- end col-->

                              <!-- Modal Lihat Surat -->
                                <div class="modal fade" id="lihat-surat-modal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Surat Keterangan Dokter</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                         
                                            </button>
                                        </div>
                                        <div class="modal-body text-center">
                                              <!-- Tombol fullscreen -->
                                                <button type="button" id="btnFullscreen" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-expand"></i> Fullscreen
                                                </button>
                                            <img id="suratImage" src="" class="img-fluid rounded shadow" alt="Surat Keterangan">
                                        </div>
                                        </div>
                                    </div>
                                </div>


                               <div class="col-xxl-4  col-lg-12" id="form_izin_sakit">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                         <h5> Pengajuan Izin / Sakit</h5>

                                         <ul class="nav nav-tabs mb-3">
                                            <li class="nav-item">
                                                <a href="#izin" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                                    <i class="mdi mdi-home-variant d-md-none d-block"></i>
                                                    <span class="d-none d-md-block">Izin</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#sakit" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                    <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                                    <span class="d-none d-md-block">Sakit</span>
                                                </a>
                                            </li>
                                           
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane show active" id="izin">
                                                 <h5>Form Pengajuan Izin</h5>
                                               

                                                <form action="<?php echo base_url();?>absensi/simpan_pengajuan_izin_sakit" method="post" enctype="multipart/form-data">
                                                    <div class="mb-3">
                                                        <label for="tanggal" class="form-label">Tanggal Izin</label>
                                                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="jns_izin" class="form-label">Jenis Izin</label>
                                                        <select class="form-select" id="jns_izin" name="jns_izin" required>
                                                            <option value="">-- Pilih Jenis Izin --</option>
                                                            <option value="1">Izin Penuh (Seharian)</option>
                                                            <option value="2">Izin Awal (Setengah Hari - Pagi)</option>
                                                            <option value="3">Izin Akhir (Setengah Hari - Sore)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="keterangan" class="form-label">Alasan / Keterangan</label>
                                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="file_image" class="form-label">Upload File Pendukung (Opsional)</label>
                                                        <input type="file" class="form-control" id="file_image" name="file_image" accept=".jpg,.jpeg,.png,.pdf">
                                                        <small class="form-text text-muted">Format yang diterima: .jpg, .jpeg, .png, .pdf. Ukuran maksimal 2MB.</small>
                                                    </div>

                                                    <input type="hidden" name="jenis_absen" value="IZIN">

                                                    <button type="submit" class="btn btn-primary">Submit</button>

                                                </form>
                                            </div>
                                        
                                            <div class="tab-pane" id="sakit">
                                                <h5>Form Pengajuan Sakit</h5>
                                                <form action="<?php echo base_url();?>absensi/simpan_pengajuan_izin_sakit" method="post" enctype="multipart/form-data">
                                                    <div class="mb-3">
                                                        <label for="tanggal" class="form-label">Tanggal Sakit</label>
                                                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis Pengajuan Sakit</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="jenis_sakit" id="tanpa_surat" value="1" required>
                                                            <label class="form-check-label" for="tanpa_surat">
                                                                Tanpa Surat Keterangan Dokter
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="jenis_sakit" id="dengan_surat" value="2" required>
                                                            <label class="form-check-label" for="dengan_surat">
                                                                Dengan Surat Keterangan Dokter
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="keterangan" class="form-label">Alasan / Keterangan</label>
                                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="file_image" class="form-label">Upload File Pendukung (Opsional)</label>
                                                        <input type="file" class="form-control" id="file_image" name="file_image" accept=".jpg,.jpeg,.png,.pdf">
                                                        <small class="form-text text-muted">Format yang diterima: .jpg, .jpeg, .png, .pdf. Ukuran maksimal 2MB.</small>
                                                    </div>

                                                    <input type="hidden" name="jenis_absen" value="SAKIT">

                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </form>
                                            </div>
                                        </div>

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

        <?php
    if($message !=''){
    ?>
    <script>$.NotificationApp.send("Berhasil","<?php echo $message;?>","top-right","rgba(0,0,0,0.2)","success")</script>

    <?php } ?>

                
           
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form Sakit
        var sakitForm = document.querySelector('#sakit form');
        if (sakitForm) {
            sakitForm.addEventListener('submit', function(e) {
                var denganSurat = sakitForm.querySelector('input[name="jenis_sakit"][value="dengan_surat"]');
                var fileInput = sakitForm.querySelector('input[name="file_image"]');
                if (denganSurat && denganSurat.checked) {
                    if (!fileInput.value) {
                        alert('Anda harus mengupload file pendukung jika memilih "Dengan Surat Keterangan Dokter".');
                        fileInput.focus();
                        e.preventDefault();
                    }
                }
            });
        }
    });

  $('#lihat-surat-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // tombol yang diklik
        var imgSrc = button.data('img')     // ambil url dari data-img
        var modal = $(this)
        modal.find('#suratImage').attr('src', imgSrc)
    })

 
        // tombol fullscreen
    document.getElementById('btnFullscreen').addEventListener('click', function () {
        var img = document.getElementById('suratImage');
        if (img.requestFullscreen) {
        img.requestFullscreen();
        } else if (img.webkitRequestFullscreen) { // Safari
        img.webkitRequestFullscreen();
        } else if (img.msRequestFullscreen) { // IE11
        img.msRequestFullscreen();
        }
    });


        
    </script>



    </body>
</html>
