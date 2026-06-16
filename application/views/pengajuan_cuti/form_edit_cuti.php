<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <style>

:root {
  --teal-050: #effcf6;
  --teal-100: #c6f7e2;
  --teal-200: #8eedc7;
  --teal-300: #65d6ad;
  --teal-400: #3ebd93;
  --teal-500: #27ab83;
  --teal-600: #199473;
  --teal-700: #147d64;
  --teal-800: #0c6b58;
  --teal-900: #014d40;

  --blue-grey-050: #f0f4f8;
  --blue-grey-100: #d9e2ec;
  --blue-grey-200: #bcccdc;
  --blue-grey-300: #9fb3c8;
  --blue-grey-400: #829ab1;
  --blue-grey-500: #627d98;
  --blue-grey-600: #486581;
  --blue-grey-700: #334e68;
  --blue-grey-800: #243b53;
  --blue-grey-900: #102a43;
}



main {
  max-width: 100%;
  background-color: #fff;
  border-radius: 8px;
}


.month-indicator {
  color: var(--blue-grey-700);
  text-align: center;
  font-weight: bold;
  font-size:25px;
}


.day-of-week,
.date-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
}


.day-of-week {
  margin-top: 1.25em;
}

.day-of-week > * {
  font-size: 1.0em;
  color: #666;
  font-weight:bold;
  letter-spacing: 0.1em;
  text-align: center;
  height:40px;
  padding:5px;
  border:1px solid #EEE;
  
}

/* Dates */
.date-grid {
  border-bottom:1px solid #DDD;
}

/* Positioning the first day */
.date-grid button:first-child {
  grid-column: 1;

}

.info-inputan{
    background:#66DF80;
    color:#FFF;
    border-radius:3px;
    padding:2px;
    font-size:12px;
    text-align:center;
}
.pending{
    background:#FFDE59;
}

.approve{
    background:#66DF80;
}

.reject{
    background:#F34F50;
}

.date-grid span {
  text-align: right;
  position: relative;
  border: 0;
  width: 100%;
  min-height: 80px;
  background-color: #FFF;
  border-top:1px solid #EEE;
  border-right:1px solid #EEE;
  padding:3px;
  color:#CCC;
}

.date-grid button {
  text-align: right;
  position: relative;
  border: 0;
  width: 100%;
  min-height: 80px;
  background-color: transparent;
  border-top:1px solid #EEE;
  border-right:1px solid #EEE;
  color: var(--blue-grey-600);
}

.date-grid button:hover,
.date-grid button:focus {
  outline: none;
  background-color: var(--blue-grey-050);
  color: var(--blue-grey-700);
}

.date-grid button:active,
.date-grid button.is-selected {
  background-color: var(--teal-100);
  color: var(--teal-900);
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
                                            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url('cuti') ?>">Cuti</a></li>
                                            <li class="breadcrumb-item active">Edit Cuti</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Cuti</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                            <?php
                                    $message = $this->session->flashdata('message');
                                
                                    $id_pegawai = $this->uri->segment(4);
                                    $nip  =$this->session->userdata('nip');
                                      $sisaTahun2025 = $cuti['2025']['sisa'];
                                    $sisaTahun2026 = $cuti['2026']['sisa'];

                                    $sisa2025 = (int) $sisaTahun2025;
                                    $sisa2026 = (int) $sisaTahun2026;

                                    // default
                                    $checked2025 = $detail_cuti->tahun_hak_cuti == '2025' ? 'selected' : '';
                                    $checked2026 = $detail_cuti->tahun_hak_cuti == '2026' ? 'selected' : '';
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

                                    $tgl_mulai = date('d-m-Y', strtotime($detail_cuti->tgl_mulai));
                                    $tgl_selesai = date('d-m-Y', strtotime($detail_cuti->tgl_selesai));

                                    
                            ?>
                     
                        <div class="row">
                          <div class="col-xxl-12 col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                        <h4>Edit Cuti</h4>
                                        <br>

                                      
                                            <form method="post" action="<?php echo base_url();?>cuti/update_pengajuan_cuti/<?= $detail_cuti->id; ?>" enctype="multipart/form-data">

                                               
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

                                                                    $selectJnsCuti = $id==$detail_cuti->jenis_cuti?'selected':'';

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
                                                            <label for="tgl_selesai" class="form-label">Tanggal Akhir Cuti</label>
                                                            <div class="input-group">
                                                                <input type="text" name="tgl_selesai" class="form-control bg-white"
                                                                    required id="tgl_selesai" placeholder="Pilih tanggal cuti"  value="<?= $tgl_selesai; ?>">
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
                                                                <input type="text" name="jumlah_hari_cuti" id="jumlah_hari_cuti" value="<?= $detail_cuti->lama_cuti; ?>"
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

                                                                    $selected = ($id_pegawai==$detail_cuti->id_pengganti) ? 'selected' : '';

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
                                                                value="<?php echo $detail_cuti->no_telp; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <!-- Alasan -->
                                                        <div class="mb-3">
                                                            <label for="alasan_cuti" class="form-label">Alasan Cuti</label>
                                                            <textarea name="alasan_cuti" class="form-control" required id="alasan_cuti" rows="3"><?= $detail_cuti->alasan_cuti; ?></textarea>
                                                        </div>

                                                    </div>
                                                        
                                                    <div class="col-md-4 col-12">
                                                        <!-- Alasan -->
                                                        <div class="mb-3">
                                                            <label for="alamat" class="form-label">Alamat Selama Cuti</label>
                                                            <textarea name="alamat" class="form-control" required  id="alamat" rows="3"><?= $detail_cuti->alamat_cuti; ?></textarea>
                                                        </div>

                                                    </div>
                                                     <div class="col-md-4 col-12">
                                                        <!-- Alasan -->
                                                        <div class="mb-3">
                                                            <label for="delgeasi tugas" class="form-label">Delegasi Tugas</label>
                                                            
                                                            <textarea name="delegasi_tugas" class="form-control" required min="50" id="delegasi_tugas" rows="3"><?= $detail_cuti->delegasi_tugas; ?></textarea>
                                                            <span class="text-muted">Tuliskan pekerjaan-pekerjaan yang akan didelegasikan ke pengganti cuti minimal 3 tugas, beri tanda koma(,) sebagai pemisah</span>
                                                        </div>

                                                    </div>
                                                        
                                                    <div class="col-12">

                                                        <button type="submit" class="btn btn-success">
                                                           Simpan Perubahan
                                                        </button>
                                                    </div>

                                                </div>
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
          <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>




        <?php
    if($message !=''){
    ?>
    <script>$.NotificationApp.send("Berhasil","<?php echo $message;?>","top-right","rgba(0,0,0,0.2)","success")</script>

    <?php } ?>


        <script>
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
              yesterday.setDate(yesterday.getDate() - 45);


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
    </body>
</html>
