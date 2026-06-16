<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/css/snackbar.css" rel="stylesheet" type="text/css" />




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
                   

                        $tgl_mulai = date('d-m-Y', strtotime($detail_cuti->tgl_mulai));
                        $tgl_selesai = date('d-m-Y', strtotime($detail_cuti->tgl_selesai));

                        $id_pegawai = $detail_cuti->id_pegawai;
                        $tahun_list                  = [2025, 2026];
                        $rekap_hak_cuti    = $this->acm->get_rekap_cuti_pegawai_by_id($id_pegawai, $tahun_list);

                                        
                        $pegawai = $this->Pegawai_model->getDataEditPegawai($id_pegawai);
                        $id_jabatan = $pegawai->id_jabatan;
                         $nama = $pegawai->nama;


                        $listPegawaiPengganti = $this->Cuti_model->getListPegawaiPenggantiCuti( $id_pegawai, $id_jabatan );
                        

                        $message = $this->session->flashdata('success');

                        // default

                        $sisaTahun2025 = $rekap_hak_cuti['2025']['sisa'];
                        $sisaTahun2026 = $rekap_hak_cuti['2026']['sisa'];

                        $sisa2025 = (int) $sisaTahun2025;
                        $sisa2026 = (int) $sisaTahun2026;

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
                     ?>


                    <!-- Start Content-->
                    <div class="container-fluid">

                    
                        <div class="row">

                            <!-- ================= HEADER ================= -->
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">Edit Pengajuan Cuti</h4>
                                      
                                    </div>
                                    <a href="<?= base_url('dashboard/pengajuan_cuti_pending') ?>" class="btn btn-light">
                                        ← Kembali
                                    </a>
                                </div>
                            </div>

                            <!-- ================= KONTEN KIRI ================= -->
                            <div class="col-sm-7">

                                 <form method="post" action="<?php echo base_url();?>admin/cuti/update_cuti/<?= $detail_cuti->id; ?>" enctype="multipart/form-data">

                                        <!-- 📅 INFORMASI CUTI -->
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="text-uppercase text-secondary mb-3">📅 Informasi Cuti</h6>

                                                 <strong>Nama: </strong> <?=$nama;?>
                                                
                                                 <br><br>
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

                                                    <div class="row mt-3">
                                                        <div class="col-md-6 col-6">
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

                                                        <div class="col-md-6 col-6">
                                                            <!-- Tanggal Akhir -->
                                                            <div class="mb-3">
                                                                <label for="tgl_selesai" class="form-label">Tanggal Akhir Cuti</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="tgl_selesai" class="form-control bg-white"
                                                                        required id="tgl_akhir_cuti" placeholder="Pilih tanggal cuti"  value="<?= $tgl_selesai; ?>">
                                                                    <span class="input-group-text">
                                                                        <i class="uil-calendar-alt"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="col-md-6 col-6">
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

                                                        <div class="col-md-6 col-12">

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

                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label for="alasan_cuti" class="form-label">Alasan Cuti</label>
                                                                    <textarea name="alasan_cuti" id="alasan_cuti" class="form-control" rows="3" required><?= $detail_cuti->alasan_cuti; ?></textarea>
                                                                </div>
                                                            </div>
                                                              <div class="col-md-12">
                                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                              </div>



                                                        
                                                    </div>

                                            </div>

                                    </form>
                                </div>

                               

                            </div>

                            <!-- ================= KONTEN KANAN ================= -->
                            <div class="col-sm-5">
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
