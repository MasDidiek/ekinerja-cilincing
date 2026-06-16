<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

    <?php $this->load->view('layout/section/header');?>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <style>
      #calendar {
        max-width: 90%;
        margin: 10px auto;
      }

      .fc-col-header-cell-cushion{
          color: #333;
          font-weight: bold;
      }

      #ajaxlist_aktifitas, #list_keterangan{
          max-height:200px;
          overflow-y: auto;

          border:1px solid #EEE;

      }

      .list-aktifitas, .list-keterangan{
          cursor:pointer;
             padding:5px 10px;
      }


      .list-aktifitas:hover{
          background:#EEE;
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

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                            <li class="breadcrumb-item active">Input Kinerja Harian</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title"> Kinerja </h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <?php
                                   $message = $this->session->flashdata('message');

                                   $id_pegawai = $this->session->userdata('id_pegawai');
                                   $nip  =$this->session->userdata('nip');


                                    $periode_bulan = $this->session->userdata('periode_bulan');
                                    $periode_tahun = $this->session->userdata('periode_tahun');


                                    if($periode_bulan=='') {
                                        $bulan = date('m');
                                        $tahun = date('Y');

                                    }else{
                                        $bulan = $periode_bulan;
                                        $tahun = $periode_tahun;
                                    }

                                    $bulanLalu = $bulan-1;


                                    $nm_bulan = getBulan($bulan);
                                    $periode = $tahun.'-'.$bulan;
                                    $periode = date('Y-m', strtotime($periode));

                                    $jumlahHariKerja = $this->Master_model->getMenitEfektifBulan($bulan, $tahun);


                                    // echo  'jumlh hari'.$jumlahHariKerja;
                                     //exit;
                                     $menitEfektifBulanan  = $jumlahHariKerja*300;


                                     $totalInput = $this->Kinerja_model->getJumlahInputPerBulan($id_pegawai, $periode);
                                     if($totalInput==0){
                                         $totalInput = 1;
                                     }

                                     $persenInput = ($totalInput/$menitEfektifBulanan)*100;
                                     if ($persenInput > 100) {
                                         $persenInput = 100;
                                     }




                                     $totalApprove  = $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);
                                     if($totalApprove==''){
                                         $totalApprove  = 0;
                                     }
                                     $persenApprove = ceil(($totalApprove/$totalInput)*100);


                                     $totalReject = $this->Kinerja_model->getAktifitasByStatus($id_pegawai, $periode, 2);
                                     if($totalReject==''){
                                         $totalReject  = 0;
                                     }

                                     $persenReject = ($totalReject/$totalInput)*100;



                                     $lastDate = date('t', strtotime($periode))+1;
                                     $hariAwal = date('N', strtotime($periode.'-01'));

                                     $prevPeriode = $tahun.'-'.$bulanLalu;

                                     $lastDateBlnLalu = date('t', strtotime($prevPeriode))+1;



                                     $tglTerakhirBulanLalu = $lastDateBlnLalu-$hariAwal;


                            ?>


                      <div class="toast-container position-fixed top-0 end-0 p-3">
                        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                          <div class="d-flex">
                            <div class="toast-body">
                              Kegiatan berhasil disimpan 🎉
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                          </div>
                        </div>
                      </div>

                           <!-- Toast Container -->
                    <div class="toast-container position-fixed top-0 end-0 p-3">
                      <div id="testToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                          <div class="toast-body">
                            Halaman berhasil dimuat 🎉
                          </div>
                          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                      </div>
                    </div>

                          
                            <div class="row">

                                <div class="col-xxl-4 col-lg-12">
                                      <div class="card widget-flat">
                                          <div class="card-body text-left">
                                              <h4>Capaian Kinerja</h4>
                                              <br>


                                                  <span class="float-right">   Periode :  <strong> <?php echo $nm_bulan;?> <?php echo $tahun;?> </strong> </span>

                                                  <div class="row">

                                                      <div class="col-md-4 col-xxl-12">

                                                          <h4><?php echo number_format($menitEfektifBulanan);?> menit</h4>
                                                          <h5 class="text-muted fw-normal mt-0" title="Revenue">Waktu Efektif</h5>
                                                      </div>
                                                      <hr>


                                                      <div class="col-md-4 col-xxl-12">

                                                            <h4 ><?php echo number_format($totalInput) ;?> Menit</h4>
                                                              <h5 class="text-muted fw-normal mt-0" title="Revenue">Total Input Aktifitas</h5>
                                                              <div class="progress mb-2">
                                                                  <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $persenInput;?>%" aria-valuenow="<?php echo $persenInput;?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                              </div>

                                                      </div>

                                                      <div class="col-md-4 col-xxl-12">

                                                              <h4><?php echo number_format($totalApprove) ;?> Menit</h4>
                                                              <h5 class="text-muted fw-normal mt-0" title="Revenue">Aktiftias Disetujui</h5>
                                                              <div class="progress">
                                                                  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $persenApprove;?>%" aria-valuenow="<?php echo $persenApprove;?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                              </div>

                                                      </div>

                                                  </div>




                                          </div>
                                      </div>
                                </div>
                                <div class="col-xl-8 col-lg-12">
                                    <div class="card">
                                        <div class="card-body">

                                              <h2 class="text-center mt-4">Kalender Kinerja Pegawai</h2>
                                              <div id="calendar"></div>


                                            <!-- Modal Form -->
                                            <div class="modal fade" id="kinerjaModal" tabindex="-1" aria-labelledby="kinerjaModalLabel" aria-hidden="true">
                                              <div class="modal-dialog">
                                                <form id="formKinerja">
                                                  <div class="modal-content">
                                                    <div class="modal-header">
                                                      <h5 class="modal-title">Input Kegiatan Harian</h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                          <div id="info-insert"></div>

                                                              <div class="col-12 mb-2 text-center">

                                                                <h4><span class="badge badge-info-lighten" id="title_date">Rabu, 23 Feb 2025</span></h4>

                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                        Absen Masuk : <br> <span class="text-success fw-bold" id="absen_masuk">00 </span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            Absen Keluar :  <br>  <span class="text-danger fw-bold"  id="absen_pulang">00 </span>
                                                                        </div>

                                                                    </div>


                                                              </div>
                                                              <div class="col-6 mb-2">
                                                                  <input type="hidden"  name="tanggal" id="tanggalInput" value="" class="form-control">
                                                              </div>


                                                            <div class="col-12">
                                                                <div class="mb-3">
                                                                    <label class="control-label form-label">Nama Aktifitas</label>
                                                                    <input class="form-control" placeholder="cari aktifitas" type="text" name="aktifitas" id="aktifitas" required="">
                                                                      <div id="ajaxlist_aktifitas"></div>
                                                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                                                </div>
                                                            </div>


                                                            <div class="col-6 mb-2">
                                                                <label for="jam_mulai" class="inline-block mb-2 text-base font-medium">Jam Mulai </label>
                                                                <input type="text"  name="jam_mulai" id="jam_mulai" value="06:00" class="time jam_mulai form-control">
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label for="jam_selesai" class="inline-block mb-2 text-base font-medium">Jam Selesai</label>
                                                                <input type="text" name="jam_selesai" id="jam_selesai" value="07:00" class="time durationNegativeMinMax form-input-kinerja form-control">
                                                            </div>


                                                            <div class="col-6 mb-2">
                                                                <label for="waktu_efektif" class="inline-block mb-2 text-base font-medium"> Waktu Efektif  </label> <span class="text-danger">*</span></label>
                                                                <input  type="text" id="waktu_efektif" name="waktu_efektif" value=""  class="form-control">
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label for="volume" class="inline-block mb-2 text-base font-medium">Volume</label><span class="text-danger">*</span></label>
                                                                <input  type="text" id="volume" name="volume" required  autocomplete="off"  value="" class="form-control">
                                                            </div>


                                                            <div class="col-12 mb-2">

                                                                <label for="keterangan" class="inline-block mb-2 text-base font-medium">Keterangan</label>
                                                                <textarea name="keterangan" id="keterangan" class="form-control"  rows="2" cols="10" wrap="soft"></textarea>
                                                                  <div id="list_keterangan"></div>
                                                            </div>
                                                        </div><!--row-->
                                                    </div>
                                                    <div class="modal-footer">
                                                      <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                  </div>
                                                </form>
                                              </div>
                                            </div>




                                            <!-- Modal List Kegiatan -->
                                            <div class="modal fade" id="modalKegiatanList" tabindex="-1">
                                              <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <h5 class="modal-title">Daftar Kegiatan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                  </div>
                                                  <div class="modal-body" id="listKegiatan">
                                                <!-- Diisi lewat AJAX -->

                                              </div>
                                            </div>
                                          </div>
                                        </div>



                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
            <!--  Header Start -->

       

    <!-- END wrapper -->
        <?php $this->load->view("layout/section/theme-setting"); ?>

        <!-- bundle -->
        <script src="<?php echo base_url(); ?>assets/new/js/vendor.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/new/js/app.min.js"></script>
        <!-- Todo js -->
        <script src="<?php echo base_url(); ?>assets/new/js/ui/component.todo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-clock-timepicker.js"></script>
         <script src="<?php echo base_url(); ?>assets/new/js/pages/demo.toastr.js"></script>
           
               <script>

                          let calendar;
                          document.addEventListener('DOMContentLoaded', function () {
                            var calendarEl = document.getElementById('calendar');

                             calendar = new FullCalendar.Calendar(calendarEl, {
                              initialView: 'dayGridMonth',

                                  events: {
                                    url: '<?= site_url("kinerja/load_kegiatan") ?>',
                                    failure: function() {
                                      alert('Gagal memuat data kegiatan');
                                    }
                                  },

                                      dateClick: function(info) {
                                          const clickedDate = new Date(info.dateStr);
                                          const today = new Date();

                                          // Validasi tanggal future
                                          today.setHours(0,0,0,0);
                                          clickedDate.setHours(0,0,0,0);
                                          if (clickedDate > today) {
                                            alert("Anda belum diizinkan untuk input aktivitas pada tanggal tersebut.");
                                            return;
                                          }

                                          // Validasi bulan sebelumnya hanya sampai tgl 6
                                          const currentMonth = today.getMonth();
                                          const clickedMonth = clickedDate.getMonth();
                                          const currentYear = today.getFullYear();
                                          const clickedYear = clickedDate.getFullYear();
                                          if (clickedYear < currentYear || (clickedYear === currentYear && clickedMonth < currentMonth)) {
                                            if (today.getDate() > 5) {
                                              alert("Input aktivitas untuk bulan sebelumnya sudah ditutup sejak tanggal 6.");
                                              return;
                                            }
                                          }

                                          // Panggil fungsi buka form
                                          bukaFormKinerja(info.dateStr);

                                   
                                  },
                                 eventClick: function(info) {
                                      var tanggal = info.event.startStr;
                                      var tanggalIndo = formatTanggalIndoFull(tanggal); // contoh pakai format full



                                      $.ajax({
                                        url: '<?= site_url("kinerja/get_kegiatan_by_tanggal") ?>',
                                        type: 'GET',
                                        data: { tgl: tanggal },
                                        success: function(res) {
                                          var data = JSON.parse(res);
                                          var html = '';

                                          if (data.length > 0) {
                                            html += `
                                               <div class="d-flex justify-content-between align-items-center mb-3">
                                                  <h5 class="mb-0">Tanggal : ${tanggalIndo}</h5>
                                                   <button class="btn btn-sm btn-primary" onclick="bukaFormKinerja('${tanggal}')">
                                                    <i class="bi bi-plus-circle"></i> Input Kinerja
                                                  </button>
                                                </div>

                                              <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle">
                                                  <thead class="table-light">
                                                    <tr>
                                                      <th>No</th>
                                                      <th>Jam</th>
                                                      <th>Nama Kegiatan</th>
                                                      <th>Action</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody>
                                            `;

                                            data.forEach(function(item, index) {
                                              html += `
                                                <tr>
                                                  <td>${index + 1}</td>
                                              
                                                  <td>${item.jam_mulai} - ${item.jam_selesai} (${item.total} menit)</td>
                                                  <td> <strong> ${item.nama_kegiatan} </strong> <br> ${item.ket ?? '-'}</td>
                                                 
                                                  <td>
                                                    <button class="btn btn-sm btn-info me-1" onclick="editKegiatan(${item.id})">
                                                      <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="hapusKegiatan(${item.id})">
                                                      <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                  </td>
                                                </tr>
                                              `;
                                            });

                                            html += `
                                                  </tbody>
                                                </table>
                                              </div>
                                            `;
                                          } else {
                                            html = '<div class="alert alert-info">Tidak ada kegiatan.</div>';
                                          }

                                          $('#listKegiatan').html(html);
                                          $('#modalKegiatanList').modal('show');
                                        }
                                      });
                                    }

                                });

                            calendar.render();

                          });

                          function bukaFormKinerja(tanggal) {
                              // Reset form biar bersih
                              $('#formKinerja')[0].reset();
                              $('#formKinerja input[name="id"]').remove();
                               $('#modalKegiatanList').modal('hide');

                              // Format tanggal Indonesia
                              const formattedDate = new Date(tanggal).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                              });

                              // Isi judul modal & input hidden tanggal
                              $("#title_date").html(formattedDate);
                              $("#tanggalInput").val(tanggal);

                              // Tampilkan modal
                              $('#kinerjaModal').modal('show');

                              // Ambil jam absen sesuai tanggal
                              $.ajax({
                                type: "POST",
                                dataType: "html",
                                url: "<?= base_url('kinerja/ajaxGetJamAbsen') ?>",
                                data: { tanggal: tanggal },
                                success: function(msg) {
                                  var jam_absen = msg.split("/");
                                  $("#absen_masuk").html(jam_absen[0]);
                                  $("#absen_pulang").html(jam_absen[1]);
                                }
                              });

                            }
                            

                            
                          // Form submit ke controller CI
                          $('#formKinerja').on('submit', function(e) {
                              e.preventDefault();
                              $.ajax({
                                url: '<?= site_url("kinerja/simpan") ?>',
                                method: 'POST',
                                data: $(this).serialize(),
                                success: function(res) {
                                  $('#kinerjaModal').modal('hide');
                                  //alert('Kegiatan berhasil disimpan');
                                  $("#info-insert").html("Kegiatan berhasil disimpan");

                                    calendar.refetchEvents(); // ⬅⬅ ini bagian pentingnya!
                                    $.NotificationApp.send("Success","Data kegiatan berhasil disimpan","top-center","#FFF","success");
                                    // reset form setelah submit
                                    $('#formKinerja')[0].reset();
                                    $('#formKinerja input[name="id"]').remove(); // hapus hidden id supaya balik ke mode insert
                                    
                                },
                                error: function() {
                                  $.NotificationApp.send("Error", "Gagal menyimpan data!", "top-right", "#bf441d", "error");
                                }
                              });
                          });



                          // Reset form setiap kali modal ditutup
                          $('#kinerjaModal').on('hidden.bs.modal', function () {
                              let tanggal = $('#tanggalInput').val(); // simpan tanggal
                              $('#formKinerja')[0].reset();           // reset semua input
                              $('#tanggalInput').val(tanggal);        // balikin tanggal
                          });



                            function editKegiatan(id) {
                              // Load data kegiatan by ID lalu isi form + tampilkan modal form
                              $.ajax({
                                url: '<?= site_url("kinerja/get_kegiatan_by_id") ?>',
                                data: { id: id },
                                success: function(res) {
                                  var data = JSON.parse(res);
                                  $('#title_date').html(formatTanggalIndoFull(data.tgl));
                                  $('#tanggalInput').val(data.tgl);
                                  $('[name="aktifitas"]').val(data.nama_kegiatan);
                                  $('[name="jam_mulai"]').val(data.jam_mulai);
                                  $('[name="jam_selesai"]').val(data.jam_selesai);
                                  $('[name="volume"]').val(data.volume);
                                  $('[name="waktu_efektif"]').val(data.waktu_efektif);
                                   $('[name="keterangan"]').val(data.ket);
                                  // Tambahkan hidden input id
                                  if (!$('#formKinerja input[name="id"]').length) {
                                    $('#formKinerja').append('<input type="hidden" name="id">');
                                  }
                                  $('[name="id"]').val(data.id);
                                  $('#modalKegiatanList').modal('hide');
                                  $('#kinerjaModal').modal('show');
                                }
                              });
                            }

                            function hapusKegiatan(id) {
                              if (confirm("Yakin ingin menghapus kegiatan ini?")) {
                                $.ajax({
                                  url: '<?= site_url("kinerja/hapus") ?>',
                                  method: 'POST',
                                  data: { id: id },
                                  success: function(res) {
                                      $.NotificationApp.send("Success","Data kegiatan berhasil dihapus","top-center","#FFF","success");
                                    $('#modalKegiatanList').modal('hide');
                                    location.reload(); // Atau bisa refresh kalender
                                  }
                                });
                              }
                            }


                            $("#aktifitas").click(function(){

                              var keyword = $(this).val();
                              $("#ajaxlist_aktifitas").show();
                                  $.ajax({
                                      type:"POST",
                                      dataType:"html",
                                      url:"<?php echo base_url();?>kinerja/ajaxGetFrequentAktifitas",
                                      data:"keyword="+keyword,
                                      success:function(msg){
                                          $("#ajaxlist_aktifitas").html(msg);
                                      }

                                  });
                              });

                              $("#aktifitas").keyup(function(){
                                    var keyword = $(this).val();
                                    $("#ajaxlist_aktifitas").show();
                                        $.ajax({
                                            type:"POST",
                                            dataType:"html",
                                            url:"<?php echo base_url();?>kinerja/ajaxSearchAktifitas",
                                            data:"keyword="+keyword,
                                            success:function(msg){
                                                $("#ajaxlist_aktifitas").html(msg);
                                            }

                                        });


                                });

                                  $("#keterangan").keyup(function(){
                                    var keyword = $(this).val();
                                    $("#list_keterangan").show();
                                        $.ajax({
                                            type:"POST",
                                            dataType:"html",
                                            url:"<?php echo base_url();?>kinerja/ajaxSearchKeterangan",
                                            data:"keyword="+keyword,
                                            success:function(msg){
                                                $("#list_keterangan").html(msg);
                                            }

                                        });


                                });


                            $('.jam_mulai').clockTimePicker({
                                precision: 5
                                });

                            $('.durationNegativeMinMax').clockTimePicker({
                                duration: true,
                                precision: 5
                                });


                                



                                $("#jam_mulai").change(function() {
                                        var jam_mulai = $(this).val();

                                        $("#jam_selesai").val(jam_mulai);
                                        $('.durationNegativeMinMax').clockTimePicker({
                                            duration: true,
                                            minimum: jam_mulai,
                                            maximum: '23:59',
                                            precision: 5
                                        });

                                      $("#jam_selesai").focus();
                                });


                                $("#jam_selesai").change(function() {
                                        waktu_efektif = $("#waktu_efektif").val();
                                        var jam_mulai = $("#jam_mulai").val();
                                        var jam_selesai = $(this).val();
                                        $(".loader").show();
                                        $.ajax({
                                            type: "POST",
                                            dataType: "html",
                                            url: "<?php echo base_url();?>kinerja/hitung_volume",
                                            data: "waktu_efektif=" + waktu_efektif + "&jam_mulai=" + jam_mulai + "&jam_selesai=" + jam_selesai,
                                            success: function(msg) {
                                            $("#volume").val(msg);
                                            $(".loader").fadeOut();
                                            }
                                        });
                                });


                                function formatTanggalIndoFull(tgl) {
                                  let d = new Date(tgl);
                                  let bulan = d.toLocaleString('id-ID', { month: 'long' });
                                  return `${d.getDate()} ${bulan} ${d.getFullYear()}`;
                                }


                        </script>


    </body>
</html>
