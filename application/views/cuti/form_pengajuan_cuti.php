<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <script type="text/javascript" src="<?php echo base_url();?>assets/js/signature.js"></script>

    <style>
            .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            }
            .photo-box {
            position: relative;
            }
            .photo-box img {
            max-width: 120px;
            border: 1px solid #ccc;
            padding: 4px;
            }
            .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            width: 20px;
            height: 20px;
            font-size: 12px;
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

                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Pengajuan Cuti</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Pengajuan Cuti</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php

                                    $list_bulan = array_bulan();


                                   // print_array($this->session->userdata);
                                    $periode_bulan = $this->session->userdata('periode_bulan');
                                    $periode_tahun = $this->session->userdata('periode_tahun');

                                    $status_pengajuan   = $this->session->flashdata('status');
                                    $message            = $this->session->flashdata('error');
                                    
                                    $jns_cuti      =  $this->session->userdata('jns_cuti');
                                    $tgl_mulai     =  $this->session->userdata('tgl_mulai');
                                    $tgl_akhir     =  $this->session->userdata('tgl_akhir');
                                    $id_pengganti  =  $this->session->userdata('id_pengganti');
                                    $alasan_cuti   =  $this->session->userdata('alasan_cuti');
                                    $tlp           =  $this->session->userdata('no_tlp');
                                    $alamat        =  $this->session->userdata('alamat');
                                    $delegasi_tugas        =  $this->session->userdata('delegasi_tugas');
                                     $lama_cuti        =  $this->session->userdata('jumlah_hari_cuti');

                                    // print_array($this->session->userdata());

                                     //$lama_cuti        =   0;


                                    $bulan = $periode_bulan;
                                    $tahun = $periode_tahun;



                                   $id_pegawai = $this->session->userdata('id_pegawai');

                                   $nama_user =  $this->session->userdata('nama');
                                   $nip_user =  $this->session->userdata('nip');
                                   $id_pegawai =  $this->session->userdata('id_pegawai');
                                   $photo = $this->Pegawai_model->getPhotoPegawai($nip_user);


                                    $pin 	= substr($nip_user, -4);

                                    $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);

                                    $jabatan =  $detail_pegawai->jabatan;
                                    $puskesmas =  $detail_pegawai->puskesmas;

                                    $id_jabatan =  $detail_pegawai->id_jabatan;


                                    $listPegawaiPengganti = $this->Cuti_model->getListPegawaiPenggantiCuti( $id_pegawai, $id_jabatan);
                                    
                                    $sisaTahun2025 = $cuti['2025']['sisa'];
                                    $sisaTahun2026 = $cuti['2026']['sisa'];

                                    $sisa2025 = (int) $sisaTahun2025;
                                    $sisa2026 = (int) $sisaTahun2026;

                                    // default
                                    $checked2025 = '';
                                    $checked2026 = '';
                                    $disabled2025 = '';
                                    $disabled2026 = '';

                                    // logic disabled
                                    if ($sisa2025 <= 0) {
                                        $disabled2025 = 'disabled';
                                    }
                                    if ($sisa2026 <= 0) {
                                        $disabled2026 = 'disabled';
                                    }

                                    // logic auto checked
                                    if ($sisa2025 > 0 && $sisa2026 <= 0) {
                                        $checked2025 = 'selected';
                                    }
                                    elseif ($sisa2026 > 0 && $sisa2025 <= 0) {
                                        $checked2026 = 'selected';
                                    }


                               ?>



                        <?php 
                          if($message !=''){
                            echo '<div class="alert alert-danger">'.$message.'</div>';
                          }?>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">

                                        <h4 class="header-title">Input Pengajuan Cuti</h4>
                                        <br>


                                             <form method="post" action="<?php echo base_url();?>cuti/simpan_pengajuan_cuti" enctype="multipart/form-data">

                                               
                                                <div class="row">
                                      
                                                    <div class="col-md-3 col-12">
                                                        <!-- Jenis Cuti -->
                                                        <div class="mb-3">
                                                            <label for="jns_cuti" class="form-label">Jenis Cuti</label>
                                                            
                                                            <select name="jns_cuti" id="jns_cuti" class="form-control" required>
                                                                <option value="">-- Pilih Jenis cuti --</option>
                                                                <?php
                                                                for ($c=0; $c < count($master_cuti); $c++) {
                                                                    $id = $master_cuti[$c]->id;
                                                                    $jenis_cuti = $master_cuti[$c]->jenis_cuti;
                                                                    $selectJnsCuti = $id==$jns_cuti?'selected':'';

                                                                    echo '<option value="'.$id.'"  '.$selectJnsCuti .'>'.$jenis_cuti.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-6">
                                                        <div class="mb-3">
                                                            <label for="tgl_mulai_cuti" class="form-label">Tanggal Mulai Cuti</label>
                                                            <div class="input-group" >
                                                                <input type="text" name="tgl_mulai" class="form-control bg-white"
                                                                    required id="tgl_mulai_cuti" placeholder="Pilih tanggal cuti" value="<?= $tgl_mulai; ?>">
                                                                <span class="input-group-text">
                                                                    <i class="uil-calendar-alt"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 col-6">
                                                        <!-- Tanggal Akhir -->
                                                        <div class="mb-3">
                                                            <label for="tgl_akhir_cuti" class="form-label">Tanggal Akhir Cuti</label>
                                                            <div class="input-group">
                                                                <input type="text" name="tgl_akhir" class="form-control bg-white"
                                                                    required id="tgl_akhir_cuti" placeholder="Pilih tanggal cuti"  value="<?= $tgl_akhir; ?>">
                                                                <span class="input-group-text">
                                                                    <i class="uil-calendar-alt"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-6">

                                                        <!-- Jumlah Hari -->
                                                        <div class="mb-3">
                                                            <label for="jumlah_hari_cuti" class="form-label">Jumlah Hari Cuti</label>
                                                            <div class="input-group" style="max-width:200px;">
                                                                <input type="text" name="jumlah_hari_cuti" id="jumlah_hari_cuti" value="<?= $lama_cuti; ?>"
                                                                    class="form-control bg-white" readonly>
                                                                <span class="input-group-text titel_hari">hari</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 col-6">
                                                            <!-- Hak Cuti -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Hak Cuti yang Digunakan</label>
                                                                <select name="hak_cuti" id="hak_cuti_digunakan" class="form-control">
                                                                    <option value="">-- Pilih Hak Cuti --</option>
                                                                    <option value="2025" <?= $checked2025; ?> <?= $disabled2025; ?>>Cuti Tahun 2025 (Sisa: <?= $sisa2025; ?> hari)</option>
                                                                    <option value="2026" <?= $checked2026; ?> <?= $disabled2026; ?>>Cuti Tahun 2026 (Sisa: <?= $sisa2026; ?> hari)</option>
                                                                    <option value="lainnya">Lainnya</option>
                                                                </select>
                                                            </div>

                                                    </div>
                                                    <div class="col-md-4 col-12">

                                                        <!-- Pengganti Cuti -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Pengganti Cuti</label>
                                                            <select class="form-control select2" name="id_pengganti" required>
                                                                <option value="">-- Pilih pengganti cuti --</option>
                                                                <?php
                                                                for ($p=0; $p < count($listPegawaiPengganti); $p++) {
                                                                    $id_pegawai = $listPegawaiPengganti[$p]->id_pegawai;
                                                                    $nama_pegawai = $listPegawaiPengganti[$p]->nama;
                                                                    $selected = ($id_pegawai==$id_pengganti) ? 'selected' : '';
                                                                    echo '<option value="'.$id_pegawai.'" '.$selected.'>'.$nama_pegawai.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-3 col-12">
                                                        <div class="mb-4">
                                                            <label for="tlp" class="form-label">No Telepon / HP</label>
                                                            <input type="text" id="tlp" name="no_tlp"
                                                                class="form-control" placeholder="08xxxx"
                                                                value="<?php echo $tlp;?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <!-- Alasan -->
                                                        <div class="mb-3">
                                                            <label for="alasan_cuti" class="form-label">Alasan Cuti</label>
                                                            <textarea name="alasan_cuti" class="form-control" required id="alasan_cuti" rows="3"><?= $alasan_cuti; ?></textarea>
                                                        </div>

                                                    </div>
                                                        
                                                    <div class="col-md-4 col-12">
                                                        <!-- Alasan -->
                                                        <div class="mb-3">
                                                            <label for="alamat" class="form-label">Alamat Selama Cuti</label>
                                                            <textarea name="alamat" class="form-control" required  id="alamat" rows="3"><?= $alamat; ?></textarea>
                                                        </div>

                                                    </div>
                                                     <div class="col-md-4 col-12">
                                                        <!-- Alasan -->
                                                        <div class="mb-3">
                                                            <label for="delgeasi tugas" class="form-label">Delegasi Tugas</label>
                                                            
                                                            <textarea name="delegasi_tugas" class="form-control" required min="50" id="delegasi_tugas" rows="3"><?= $delegasi_tugas;?></textarea>
                                                            <span class="text-muted">Tuliskan pekerjaan-pekerjaan yang akan didelegasikan ke pengganti cuti minimal 3 tugas, beri tanda koma(,) sebagai pemisah</span>
                                                        </div>

                                                    </div>
                                                        
                                                    <div class="col-12">

                                                        <button type="submit" class="btn btn-success">
                                                            Kirim Pengajuan Cuti
                                                        </button>
                                                    </div>

                                                </div>
                                          </form>

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
        <!-- demo end -->

        <script>
            // var message = '<?php echo $message;?>';

            // if(message != ''){
            //     $.NotificationApp.send("Berhasil",message,"top-right","rgba(0,0,0,0.2)","success")
            // }
            
            let jenisCuti = null;

            // tangkap jenis cuti
            $("#jns_cuti").on("change", function () {
                jenisCuti = $(this).val();
             if (jenisCuti == 2) {
                    $(".titel_hari").html("bulan");
                } else {
                    $(".titel_hari").html("hari");
                }
            });

              const yesterday = new Date();
              yesterday.setDate(yesterday.getDate() - 1);


                // Inisialisasi tanggal mulai
                const tglMulai = flatpickr("#tgl_mulai_cuti", {
                  minDate: yesterday,              // Mulai dari hari ini
                  maxDate: new Date().fp_incr(90), // Maksimal 90 hari ke depan (~3 bulan)
                  dateFormat: "d-m-Y",
                  allowInput: true,
                  altInput: false,
                  onChange: function(selectedDates, dateStr, instance) {
                      // Otomatis isi tanggal akhir sama dengan tanggal mulai
                      if (selectedDates.length > 0) {
                           const startDate = selectedDates[0];

                           // Set tanggal akhir sama dengan tanggal mulai
                           tglAkhir.setDate(startDate);
                           tglAkhir.set('minDate', startDate);

                           // Fokus ke input akhir
                           document.getElementById("tgl_akhir_cuti").focus();
                         }
                    }
                });

                // Inisialisasi tanggal akhir
                const tglAkhir = flatpickr("#tgl_akhir_cuti", {
                  minDate: yesterday,
                  allowInput: true,
                    altInput: false,         // Ini akan di-update saat pilih tgl mulai
                  maxDate: new Date().fp_incr(100),
                  dateFormat: "d-m-Y"
                });


                function isValidDate(dateString) {
                  // Cek format YYYY-MM-DD pakai regex sederhana
                  return /^\d{2}-\d{2}-\d{4}$/.test(dateString);
                }

                $("#tgl_mulai_cuti, #tgl_akhir_cuti").on("change", function() {
                    const tglMulai = $("#tgl_mulai_cuti").val();
                    const tglAkhir = $("#tgl_akhir_cuti").val();

                    if (!tglMulai || !tglAkhir) {
                        $("#jumlah_hari_cuti").val("-");
                        return;
                    }

                    if (!isValidDate(tglMulai) || !isValidDate(tglAkhir)) {
                        alert("Format tanggal harus DD-MM-YYYY");
                        $("#jumlah_hari_cuti").val("-");
                        return;
                    }

                    // 👉 CUTI BERSALIN
                    if (jenisCuti == 2) {
                        $("#jumlah_hari_cuti").val("3");
                        return;
                    }

                    // 👉 CUTI BIASA
                    hitungHariCuti(tglMulai, tglAkhir);

                });

                function hitungHariCuti(tglMulai, tglAkhir) {
                  const jenis = $("#jenis_jam_kerja").val();
                  $.ajax({
                    url: "<?php echo base_url("cuti/hitung"); ?>",
                    type: "POST",
                    data: { tgl_mulai: tglMulai, tgl_akhir: tglAkhir, jenis_jam_kerja: jenis },
                    dataType: "json",
                    success: function(response) {
                      if (response.error) {
                        alert(response.error);
                        $("#jumlah_hari_cuti").val(0);
                      } else {
                        $("#jumlah_hari_cuti").val(response.jumlah_hari );
                      }
                    },
                    error: function() {
                      alert("Terjadi kesalahan saat menghitung cuti.");
                      $("#jumlah_hari_cuti").val(0);
                    }
                  });
                }




        </script>



        <script>
            document.getElementById('file_upload').addEventListener('change', function(e) {
                const files = e.target.files;
                const previewContainer = document.getElementById('previewContainer');

                // Clear previous previews
                previewContainer.innerHTML = '';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const photoBox = document.createElement('div');
                            photoBox.className = 'photo-box';

                            photoBox.innerHTML = `
                                <img src="${e.target.result}" alt="Preview">
                                <button type="button" class="remove-btn" onclick="removePhoto(this)">×</button>
                            `;

                            previewContainer.appendChild(photoBox);
                        };

                        reader.readAsDataURL(file);
                    }
                }
            });

            function removePhoto(button) {
                button.parentElement.remove();
            }

            // document.getElementById('uploadBtn').addEventListener('click', function() {
            //     const fileInput = document.getElementById('file_upload');
            //     const files = fileInput.files;

            //     if (files.length === 0) {
            //         alert('Silakan pilih file gambar terlebih dahulu');
            //         return;
            //     }


            //     return true;
            //     // Proses upload di sini
            //     //alert('Fitur upload akan diimplementasikan');
            // });
        </script>

    </body>
</html>