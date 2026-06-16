<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <style>
                /* Wrapper tab */
            .custom-tabs {
                border-bottom: 1px solid #dee2e6;
            }

            /* Semua tab */
            .custom-tabs .nav-link {
                border: 1px solid #dee2e6;
                border-bottom: none;
                margin-right: 3px;
                color: #6c757d;
                background: #FFF;
                border-radius: 3px 3px 0 0;
                transition: all .2s ease-in-out;
            }

            /* Hover */
            .custom-tabs .nav-link:hover {
                background: #ffffff;
                color: #0d6efd;
            }

            /* TAB AKTIF */
            .custom-tabs .nav-link.active {
                background: #ffffff;
                color: #0d6efd;
                font-weight: 600;

                border: 2px solid #0d6efd;   /* border tebal */
                border-bottom: 1px solid #ffffff; /* nyatu ke konten */
                position: relative;
                top: 1px;
            }

</style>
    <body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": false}'>
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
            <?php $this->load->view('layout/section/sidebar');?>
            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
                    <?php $this->load->view('layout/section/topbar');?>
                    <?php
                        // print_array($pegawai);

                            $id_pegawai     = $pegawai->id_pegawai;
                            $tgl_masuk      = $pegawai->tgl_masuk;
                            $nip            = $pegawai->nip;
                            $nama_pegawai   = $pegawai->nama;
                            $tmt            = $pegawai->tmt;
                            $id_pendidikan = $pegawai->id_pendidikan;
                            $jns_jam_kerja  = $pegawai->jns_jam_kerja;
                            $photo          = $this->Pegawai_model->getPhotoPegawai($nip);

                            $keterangan_jabatan = $pegawai->keterangan_jabatan;
                         
                             $jns_pegawai = $pegawai->jns_pegawai;
                             $jns_jam_kerja = $pegawai->jns_jam_kerja;

                         

                            if($photo==''){
                                $photo = 'avatar.png';
                            }




                            $pendidikan = $this->Master_model->getNamaPendidikan($id_pendidikan);

                            $tahun = date('Y');
                            $today = date('Y-m-d');

                            $masa_kerja = hitungMasaKerja($tmt, $today);
           // calculateMasakerja

                            $detailPegawai = $this->Pegawai_model->getDataDetailPegawai($nip);
                            $jns_kelamin  = $detailPegawai->jns_kelamin;
                            $agama  = $detailPegawai->agama;
                            $status_perkawinan  = $detailPegawai->status_perkawinan;
                            $tab = $this->uri->segment(2); // misal 'my_profile', 'data_keluarga', dll

                            $arrayGolonganDarah = ['A', 'B', 'AB', 'O', 'Tidak tahu'];
                            $arrayPendidikan = ['SD', 'SMP', 'SMA/SLTA', 'D I', 'D III', 'D IV', 'S1', 'S2'];
                            $selectedPendidikan = $detailPegawai->pendidikan ?? '';

                            $arrayStatusPernikahan = [
                                'Belum Kawin',
                                'Kawin',
                                'Cerai Hidup',
                                'Cerai Mati'
                            ];

                            $selectedStatus = $detailPegawai->status_perkawinan ?? '';

                            $arrayAgama = [
                                'Islam',
                                'Kristen',
                                'Katolik',
                                'Protestan',
                                'Hindu',
                                'Buddha',
                                'Konghucu'
                            ];

                            $selectedAgama = $detailPegawai->agama ?? '';
                             //print_array($detailPegawai);
                     ?>


                    <!-- Start Content-->
                  <div class="container-fluid mt-3 bg-white">
                    <a href="<?= base_url('profile/edit_profile/'.$id_pegawai) ?>" class="btn mt-2 btn-info float-end"> <i class="mdi mdi-account-edit-outline me-1"></i>  Edit Profile</a>

                    <div class="clearfix"></div>
                        <!-- Tabs -->
                      <ul class="nav nav-tabs  mb-3 custom-tabs" role="tablist">

                            <li class="nav-item">
                                <a class="nav-link <?= ($tab=='my_profile')?'active':'' ?>"
                                href="<?= base_url('profile/my_profile/'.$id_pegawai) ?>">
                                 <i class="mdi mdi-account me-1"></i>     Profile
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= ($tab=='data_keluarga')?'active':'' ?>"
                                href="<?= base_url('profile/data_keluarga/'.$id_pegawai) ?>">
                                   <i class="mdi mdi-account-multiple me-1"></i>  Data Keluarga
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= ($tab=='riwayat_pendidikan')?'active':'' ?>"
                                href="<?= base_url('profile/riwayat_pendidikan/'.$id_pegawai) ?>">
                                    <i class="mdi mdi-school me-1"></i>  Pendidikan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= ($tab=='dokumen')?'active':'' ?>"
                                href="<?= base_url('profile/dokumen/'.$id_pegawai) ?>">
                                    <i class="mdi mdi-file me-1"></i>  Dokumen
                                </a>
                            </li>

                        </ul>

                        <div class="modal fade" id="changePhotoModal" tabindex="-1" aria-labelledby="changePhotoModal" aria-hidden="true">
                            <div class="modal-dialog">
                            <div class="modal-content">
                            <form id="formUploadDokumen" enctype="multipart/form-data" method="POST" action="<?= base_url('profile/upload_profile_picture') ?>">

                                <div class="modal-header">
                                    <h5 class="modal-title">Ubah Photo Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">

                                        <div class="mb-3">
                                            <label for="file_dokumen" class="form-label">Pilih File (JPG/PNG)</label>
                                            <input type="file" class="form-control" name="imageupload" id="file_dokumen" accept="jpg,.jpeg,.png" required>
                                        </div>


                                    </div>
                                    <div class="modal-footer">
                                        <div class="loader d-none text-success">Menyimpan...</div>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                        <div class="row">

                           
                            <!-- CENTER : BIODATA -->
                            <div class="col-md-9">
                                <div class="">
                                   <div class="card-header bg-primary text-white">
                                            <h5>Edit Biodata Pegawai</h5>
                                        </div>
                                    <div>
                                      <form action="<?= base_url('profile/update_profle'); ?>" method="POST">
                                                <div class="card shadow-0">
                                                  
                                                    <div class="card-body">

                                                        <!-- NIP -->
                                                        <div class="mb-3">
                                                            <label class="form-label">NIP</label>
                                                            <input type="text" name="nip" class="form-control" 
                                                                value="<?= $pegawai->nip; ?>" readonly>
                                                        </div>

                                                      
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                  <!-- Nama -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Lengkap</label>
                                                                        <input type="text" name="nama" class="form-control" 
                                                                            value="<?= $pegawai->nama; ?>" required>
                                                                    </div>

                                                            </div>
                                                            <div class="col-md-6">
                                                                 <!-- Jenis Kelamin -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Jenis Kelamin</label>
                                                                    <select name="jenis_kelamin" class="form-select" required>
                                                                        <option value="L" <?= ($jns_kelamin=='L')?'selected':''; ?>>Laki-laki</option>
                                                                        <option value="P" <?= ($jns_kelamin=='P')?'selected':''; ?>>Perempuan</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                 <!-- Jenis Kelamin -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Golongan Darah</label>
                                                                   <select name="golongan_darah" class="form-select" required>
                                                                        <option value="">-- Pilih Golongan Darah --</option>
                                                                        <?php foreach ($arrayGolonganDarah as $gd) : ?>
                                                                            <option value="<?= $gd; ?>" 
                                                                                <?= ($detailPegawai->golongan_darah == $gd) ? 'selected' : ''; ?>>
                                                                                <?= $gd; ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <!-- Tempat Lahir -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Tempat Lahir</label>
                                                                    <input type="text" name="tempat_lahir" class="form-control" 
                                                                        value="<?= $detailPegawai->tempat_lahir; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <!-- Tanggal Lahir -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Tanggal Lahir</label>
                                                                    <input type="date" name="tgl_lahir" class="form-control" 
                                                                        value="<?= $detailPegawai->tgl_lahir; ?>">
                                                                </div>
                                                            </div>

                                                         

                                                             <div class="col-md-6">
 <!-- No Telp -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label">No. Telepon</label>
                                                                        <input type="text" name="no_telp" class="form-control" 
                                                                            value="<?= $detailPegawai->no_tlp; ?>">
                                                                    </div>
                                                             </div>


                                                             <div class="col-md-6">

                                                                <!-- Email -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Email</label>
                                                                    <input type="email" name="email" class="form-control" 
                                                                        value="<?= $detailPegawai->email; ?>">
                                                                </div>

                                                             </div>

                                                                 <div class="col-md-6">
                                                                 <!-- Pendidikan -->
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Pendidikan Terakhir</label>
                                                                        <select name="pendidikan" class="form-select" required>
                                                                            <option value="">-- Pilih Pendidikan Terakhir --</option>
                                                                            <?php foreach ($arrayPendidikan as $pendidikan) : ?>
                                                                                <option value="<?= $pendidikan; ?>"
                                                                                    <?= ($selectedPendidikan === $pendidikan) ? 'selected' : ''; ?>>
                                                                                    <?= $pendidikan; ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                              </div>
                                                                <div class="col-md-6">
                                                                 <!-- Pendidikan -->
                                                                    <div class="mb-3">
                                                                         <label class="form-label">Status Pernikahan</label>
                                                                         <select name="status_pernikahan" class="form-select" required>
                                                                            <option value="">-- Pilih Status Pernikahan --</option>
                                                                            <?php foreach ($arrayStatusPernikahan as $status) : ?>
                                                                                <option value="<?= $status; ?>"
                                                                                    <?= ($selectedStatus === $status) ? 'selected' : ''; ?>>
                                                                                    <?= $status; ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                 <div class="col-md-6">
                                                                 <!-- Pendidikan -->
                                                                    <div class="mb-3">
                                                                         <label class="form-label">Agama</label>
                                                                        <select name="agama" class="form-select" required>
                                                                            <option value="">-- Pilih Agama --</option>
                                                                            <?php foreach ($arrayAgama as $agama) : ?>
                                                                                <option value="<?= $agama; ?>"
                                                                                    <?= ($selectedAgama === $agama) ? 'selected' : ''; ?>>
                                                                                    <?= $agama; ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                        </div>
                                                       
                                                       

                                                        <!-- Alamat -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Alamat KTP</label>
                                                            <textarea name="alamat" class="form-control" rows="3"><?= $detailPegawai->alamat_ktp; ?></textarea>
                                                        </div>

                                                         <!-- Alamat -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Alamat Domisili</label>
                                                            <textarea name="alamat_domisili" class="form-control" rows="3"><?= $detailPegawai->alamat_domisili; ?></textarea>
                                                        </div>

                                                        
                                                        <!-- No KTP -->
                                                        <div class="mb-3">
                                                            <label class="form-label">No KTP</label>
                                                            <input type="text" name="no_ktp" class="form-control" 
                                                                value="<?= $detailPegawai->no_ktp; ?>">
                                                        </div>

                                                        <!-- NPWP -->
                                                        <div class="mb-3">
                                                            <label class="form-label">NPWP</label>
                                                            <input type="text" name="npwp" class="form-control" 
                                                                value="<?= $detailPegawai->npwp; ?>">
                                                        </div>

                                                        <!-- No Rekening -->
                                                        <div class="mb-3">
                                                            <label class="form-label">No Rekening</label>
                                                            <input type="text" name="no_rekening" class="form-control" 
                                                                value="<?= $detailPegawai->no_rekening; ?>">
                                                        </div>

                                                    </div>

                                                    <div class="card-footer text-end">
                                                        <button type="submit" class="btn btn-success">Update</button>
                                                        <a href="<?= base_url('profile/my_profile');?>" class="btn btn-secondary">Batal</a>
                                                    </div>
                                                </div>
                                            </form>

                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT : MENU KEPEGAWAIAN -->
                            <div class="col-md-3">
                                <div class="list-group shadow-sm">
                                
                                    <a href="#" class="list-group-item list-group-item-action">
                                        💰  Gaji
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        ⭐ Jabatan
                                    </a>
                                   
                                    <a href="#" class="list-group-item list-group-item-action">
                                        ⚖️ Hukuman Disiplin
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        🎓 Diklat
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        🏅 Penghargaan
                                    </a>
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


          <script>
            /*$(document).ready(function() {
                $('#formEditData').submit(function() {
                    $(".loader").removeClass("d-none");
                     $.ajax({


                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        success: function(msg) {
                            // Ganti isi loader dengan pesan sukses
                             $(".loader").html(msg);

                                // Tunggu 5 detik (5000 ms), lalu reload halaman
                                setTimeout(function() {
                                window.location.reload();
                                }, 5000);
                            }
                        });
                           return false;
                    });


                 });*/
            </script>

    </body>
</html>
