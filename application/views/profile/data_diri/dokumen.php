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
                            $jabatan = $pegawai->jabatan;
                            $puskesmas = $pegawai->puskesmas;
                           // $gaji_pokok = $pegawai->gaji_pokok;
                             $masa_kerja = $pegawai->masa_kerja;
                             $jns_pegawai = $pegawai->jns_pegawai;
                             $jns_jam_kerja = $pegawai->jns_jam_kerja;

                         
                        
                            if($photo==''){
                                $photo = 'avatar.png';
                            }

                            $pendidikan = $this->Master_model->getNamaPendidikan($id_pendidikan);

                            $tahun = date('Y');
                            $today = date('Y-m-d');

                            $masa_kerja = hitungMasaKerja($tmt, $today);
                           


                            $jns_kelamin  = $detailPegawai->jns_kelamin;
                            $agama  = $detailPegawai->agama;
                            $status_perkawinan  = $detailPegawai->status_perkawinan;
                            $tab = $this->uri->segment(2); // misal 'my_profile', 'data_keluarga', dll

                             $dokumen_rules = [
                            [
                                'jns_dokumen' => 'KTP',
                                'format' => ['jpg', 'png'],
                                'keterangan' => '<span class="text-danger">wajib Upload</span>'
                            ],
                            [
                                'jns_dokumen' => 'Kartu Keluarga',
                                'format' => ['jpg', 'png'],
                                'keterangan' => '<span class="text-danger">wajib Upload</span>'
                            ],
                            [
                                'jns_dokumen' => 'Ijazah',
                                'format' => ['pdf', 'jpg', 'png'],
                                'keterangan' => '<span class="text-danger">wajib Upload</span>'
                            ],

                            [
                                'jns_dokumen' => 'Surat Nikah',
                                'format' => ['pdf'],
                                'keterangan' => '<span class="text-primary">Jika sudah menikah</span>'
                            ],
                            [
                                'jns_dokumen' => 'Surat Cerai',
                                'format' => ['pdf'],
                                'keterangan' => '<span class="text-primary">Jika sudah Cerai</span>'
                            ],[
                                'jns_dokumen' => 'Akta Lahir Anak',
                                'format' => ['pdf'],
                                'keterangan' => '<span class="text-primary">Jika sudah punya anak</span>'
                            ],[
                                'jns_dokumen' => 'SIP',
                                'format' => ['pdf'],
                                'keterangan' => '<span class="text-warning">Nakes wajib upload</span>'
                            ],
                            [
                                'jns_dokumen' => 'STR',
                                'format' => ['pdf'],
                                'keterangan' => '<span class="text-warning">Nakes wajib upload</span>'
                            ]

                        ];      
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

                            <!-- LEFT : FOTO -->
                            <div class="col-md-3">
                                <div class=" text-center">
                                    <div class="card-body">
                                        <img src="<?php echo base_url().'uploads/photo_profile/'. $photo ;?>" 
                                            class=" rounded mb-3" 
                                            alt="Foto Pegawai" width="160">
                                <br>
                                            <button class="text btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#changePhotoModal">
                                                <i class="mdi mdi-account-edit-outline me-1"></i> Change Photo</button>

                                        <div class="fs-5  text-dark px-3 py-2">
                                            TMT : <?php echo format_view($tmt); ?>
                                        
                                        </div>
                                        Masa Kerja :   <?= $masa_kerja['years']; ?> tahun <?= $masa_kerja['months']; ?> bulan
                                        
                                    </div>
                                </div>
                            </div>

                            <!-- CENTER : BIODATA -->
                            <div class="col-md-6">
                                <div class="">
                                    
                                    <div class="card-header  fw-bold">
                                        # Dokumen Pegawai
                                    </div>
                                    <div class="card-body p-2">
                                        <center>
                                       
                                          <div class="text-dark fw-bold fs-5 "> <?php echo $nama_pegawai ;?></div>
                                    <?= $jabatan.' - '.$keterangan_jabatan ?>
                                        </center>
                                      

                                
                                        <hr>

                                        <table class="table table-sm table-borderless  mb-0">
                                           <tr>
                                            <th>Nama Dokumen</th>
                                            <th>Keterangan</th>
                                            <th>Status
                                                

                                            </th>
                                           </tr>

                                           <?php

                                                        foreach ($dokumen_rules as $rule) {
                                                            $jns_dokumen = $rule['jns_dokumen'];



                                                             $dokumen_pegawai  =  $this->Pegawai_model->getDokumenPegawai($nip, $jns_dokumen);

                                                             for ($i=0; $i < count($dokumen_pegawai) ; $i++) {
                                                               echo '<tr>
                                                                        <td>'. $jns_dokumen.'</td>
                                                                        <td>'.$dokumen_pegawai[$i]->nama_file.'</td>
                                                                        <td>
                                                                            <img src="'.base_url().'uploads/dokumen/'.$dokumen_pegawai[$i]->nama_file.'" alt="Dokumen" class="img-thumbnail">
                                                                        </td>
                                                                        <td>

                                                                            <a href="'.base_url().'uploads/dokumen/'.$dokumen_pegawai[$i]->nama_file.'" class="btn btn-sm btn-info" target="_blank">Lihat</a>
                                                                            <button class="btn btn-sm btn-danger btnHapusDokumen" data-id="'.$dokumen_pegawai[$i]->id.'" data-file="'.$dokumen_pegawai[$i]->nama_file.'">
                                                                                Hapus
                                                                            </button>
                                                                        </td>


                                                               </tr>';
                                                             }

                                                        }






                                                        ?>
                                            
                                        </table>

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
