<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        
        .edit-date{
            cursor: pointer;
             color: #67bed9;
        }
    .edit-date:hover{
            color: #4ea8c3;
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


                        <?php
                            $message = $this->session->flashdata('message');
                            $id_pegawai = $this->session->userdata('id_pegawai');


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
                                            <li class="breadcrumb-item active">Main Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <?php

                                    $list_bulan = array_bulan();

                                    $periode_bulan = $this->session->userdata('periode_bulan');
                                    $periode_tahun = $this->session->userdata('periode_tahun');
                                    $jenis   = $this->session->userdata('jenis'); //jenis absensi IZIN/SAKIT


                                    $tgl_dari_session = '2025-11-01';
                                    $tgl_sampai_session = '2025-11-30';



                                   // print_array($pegawai);



                               ?>




                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">

                                        <h4 class="header-title">Pengajuan Dinas Luar Pegawai</h4>
                                        <br>

                                        <form action="<?php echo base_url();?>admin/presensi/filter_pengajuan_izin" method="post">
                                            <div class="row">
                                            
                                                <div class="col-md-3">
                                                    <label for="status">Status</label>
                                                    <select name="status" class="form-control" >
                                                        <option value="0">Pending</option>
                                                        <option value="1">Approved</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="status">Date From</label>
                                                    <input type="text" name="date_from" class="date_from form-control" value="<?php echo  $tgl_dari_session;?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="status">Date To</label>
                                                    <input type="text" name="date_to" class="date_to form-control" value="<?php echo  $tgl_sampai_session;?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="status"></label><br>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="table-responsive mt-3">
                                            <table class="table table-sm mb-0 table-bordered">
                                                 <thead>
                                                        <tr>
                                                            <th class="text-center" data-sort="no">No</th>
                                                            <th class="text-center" data-sort="nama_pegawai">Nama Pegawai</th>
                                                            <th class="text-center" data-sort="telat">Jenis DL</th>
                                                          
                                                            <th class="text-center" data-sort="p_awal">Tanggal</th>
                                                            <th class="text-center" data-sort="izin">Keterangan</th>
                                                            <th class="text-center" data-sort="sakit">Status</th>
                                                    
                                                            <th class="text-center" data-sort="status">Date Created</th>
                                                            <th class="text-center" data-sort="action">Action</th>
                                                        </tr>
                                                 </thead>
                                                <tbody>
                                                  <?php 

                                                
//print_array($pengajuan);
                                                    $no = 1;
                                                    foreach ($pengajuan as $rekap){

                                                            $nama = $rekap->nama;
                                                            $id = $rekap->id;
                                                            $jns_dl = $rekap->jns_dl;
                                                         
                                                             $tanggal = $rekap->tanggal;
                                                 
                                                            $keterangan = $rekap->keterangan;
                                                            $file_image = $rekap->photo;
                                                            $status = $rekap->status;
                                                            $nip = $rekap->nip;

                                                            $pin = substr($nip, -4);
                                                            $create_at = $rekap->create_at;


                                                            if($status==0){
                                                                $flag = '<span class="badge bg-warning-lighten text-warning"> Pending</span>';
                                                            }else{
                                                                $flag = '<span class="badge bg-warning-success text-success">Approved</span>';
                                                            }

                                                        

                                                        echo' <tr>
                                                                <td class="text-center">'.$no.'</td>
                                                                <td class="text-left">'.$nama.'</td>
                                                                <td class=" text-center">'.$jns_dl.'</td>
                                                                  <td class="text-center">'.format_view($tanggal).'</td>
                                                                <td class="text-left">'.word_limiter($keterangan, 5).'</td>
                                                                <td class="text-center">'.$flag.'</td>
                                                                <td class="text-center">'.format_view($create_at).'</td>
                                                                 <td class="text-center">
                                                                    <button type="button" data-id="'.$id.'" data-pin="'.$pin.'" data-bs-toggle="modal" data-bs-target="#detail-modal" class="text-primary bg-white border-0 view-detail">
                                                                         <i class="mdi mdi-eye"></i> View
                                                                    </button>
                                                                    <button type="button" data-id="'.$id.'" data-pin="'.$pin.'" class="text-warning bg-white border-0 approve">
                                                                         <i class="mdi mdi-check"></i> Approve
                                                                    </button>
                                                                    <button type="button" data-id="'.$id.'" data-pin="'.$pin.'" class="text-danger bg-white border-0 delete">
                                                                    <i class="mdi mdi-trash-can-outline"></i> Delete </button>
                                                                 </td>
                                                         

                                                                </tr>';

                                                            $no += 1;

                                                    }

                                                ?>
                                          
                                                   

                                                </tbody>
                                            </table>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->

                            </div>



                                  <div class="modal fade" id="detail-modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="scrollableModalTitle">Detail Pengajuan DL</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                                </div>
                                                <div class="modal-body" id="detail_pengajuan_dl">
                                                     <table class="table table-bordered">
                                                        <tr>
                                                            <th>Nama</th>
                                                            <td id="detail_nama"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>NIP</th>
                                                            <td id="detail_nip"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-md-5"  id="detail_tanggal"></div>
                                                                     <div class="col-md-7">
                                                                         <span class="edit-date"> <i class="mdi mdi-pencil"></i>  Edit Tanggal</span>
                                                                          <div id="form-edit-date" class="d-none formEditDate">
                                                                            <form id="editDateDl" action="<?php echo base_url();?>admin/presensi/change_date_dl" method="post">
                                                                                <input type="hidden" name="id_dl" id="dl_id" value="">
                                                                                <input type="date" id="new_date" class="form-control d-inline" name="new_date" value="" required style="width: 160px;">
                                                                                
                                                                                <button type="submit" class="btn btn-sm btn-success save-edit d-inline">Save Change</button>
                                                                                 <button type="button" class="btn btn-sm btn-danger cancel-edit  d-inline">
                                                                                    <i class="mdi mdi-close"></i>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                     </div>
                                                                </div>
                                                                
                                                              
                                                                      
                                                            </td>

                                                      

                                                        </tr>
                                                        <tr>
                                                            <th>Jenis DL</th>
                                                            <td id="detail_jns_dl"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Keterangan</th>
                                                            <td id="detail_keterangan"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Foto</th>
                                                             <td>
                                                                    
                                                                <img id="detail_foto" src="" width="250">
                                                                <br>

                                                                <a id="detail_foto_link" href="" class="btn btn-sm btn-info mt-2" target="_blank">View Image</a>
                                                                </td>

                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                  
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    


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
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- demo end -->

       
        <script>

           
            $(document).on("click", ".view-detail", function () {
                    let id = $(this).data("id");
        

                            $.ajax({
                                url: "<?php echo site_url('admin/presensi/ajax_detail_pengajuan_dl'); ?>",
                                type: "POST",
                                data: { id: id},
                                dataType: "json",
                                success: function (res) {
                                   data = res.data;
                                   let d = data[0];

                                    let baseThumb = "<?= base_url('uploads/photo_dinas_luar/thumb/') ?>";
                                    let baseFull  = "<?= base_url('uploads/photo_dinas_luar/thumb/') ?>";

                        // Tampilkan ke modal
                                    $("#detail_nama").text(d.nama);
                                    $("#detail_nip").text(d.nip);
                                    $("#detail_tanggal").text(d.tanggal);
                                    $("#detail_keterangan").text(d.keterangan);
                                    $("#detail_jns_dl").text(d.jns_dl);
                                    $("#dl_id").val(d.id);
                                   
                                    $("#detail_foto").attr("src", baseThumb + d.photo);      // thumbnail
                                    $("#detail_foto_link").attr("href", baseFull + d.photo); // link foto besar

                                },
                               
                            });
             });

           $(document).on("click", ".approve", function () {
                    let id = $(this).data("id");
                     let pin = $(this).data("pin");

                    Swal.fire({
                        title: 'Setujui Pengajuan Dinas Luar?',
                        text: "Pastikan data sudah benar.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Setujui!'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.ajax({
                                url: "<?php echo site_url('admin/presensi/ajaxSetujuiDL'); ?>",
                                type: "POST",
                                data: { id: id, pin:pin },
                                dataType: "json",
                                success: function (res) {
                                    if (res.status === true) {

                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Pengajuan izin/sakit berhasil di ACC.',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload(); // refresh tabel
                                        });

                                    } else {

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: res.message
                                        });

                                    }
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan pada server.'
                                    });
                                }
                            });

                        }
                    });
                });


                $(document).on("click", ".delete", function () {
                    let id = $(this).data("id");
                 
                    Swal.fire({
                        title: 'Hapus Pengajuan DL ini ?',
                        text: "Pastikan data sudah benar.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.ajax({
                                url: "<?php echo site_url('admin/presensi/ajaxDeleteDl'); ?>",
                                type: "POST",
                                data: { id: id},
                                dataType: "json",
                                success: function (res) {
                                    if (res.status === true) {

                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Pengajuan izin/sakit berhasil di hapus.',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload(); // refresh tabel
                                        });

                                    } else {

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: res.message
                                        });

                                    }
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan pada server.'
                                    });
                                }
                            });

                        }
                    });
                });

                $(".edit-date").click(function(){
                    $(this).hide();
                   
                    $('.formEditDate').removeClass('d-none');
                });

                 $(".cancel-edit").click(function(){
                    $(".edit-date").show();
                   
                    $('.formEditDate').addClass('d-none');
                });



                 $('#editDateDl').submit(function() {
                    
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        dataType: 'json',

                        success: function(res) {
                           if (res.status === true) {

                                let tgl_baru = res.new_date;
                                 $("#detail_tanggal").text(tgl_baru);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Tanggal Dinas luar berhasil diupdate.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    //location.reload(); // refresh tabel
                                });

                            } else {

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: res.message
                                });

                            }   
                        }
                    })
                    return false;
                });


        </script>






        <script>
              $(".date_from").flatpickr();
              $(".date_to").flatpickr();

        </script>

    </body>
</html>
