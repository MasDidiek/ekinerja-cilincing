<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view("layout/section/header"); ?>

<style>
.shift-wrapper {
    overflow: auto;
    max-height: 75vh;
    border: 1px solid #ddd;
}

.card-shift{
    display: grid;
    grid-template-columns: 170px  repeat(3, 170px);
    margin:5px;
}

.mini-card{
    background-color: #F8F8F8; /* pale blue */
    padding: 10px;
    border-radius: 8px;
    margin:5px;
    cursor:pointer;
    border:1px solid #EEE;
}
.mini-card:hover{
    background-color: #E6F8E7; /* pale blue */
    color:#239529;
     border:1px solid #93F197;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: 265px  repeat(31, 120px);
}

/* CELL */
.cell {
    border: 1px solid #eee;
    padding: 4px;
    background: #fff;
     white-space: nowrap;
}

/* HEADER */
.header .cell {
    background: #f1f5f9;
    font-weight: 600;
    text-align: center;
    margin-left:-12px;
}

/* NAMA */
.nama {
    font-weight: bold;
    color: #0F47FF;
}

/* STICKY */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 2;
}

.sticky-left {
    position: sticky;
    left: 0;
    z-index: 10;
    background: #fff;
    min-width: 260px;
    max-width: 260px;
    padding:15px
}

.sticky-left.sticky-top {
    z-index: 5;
    background: #e2e8f0;
}

/* SHIFT CARD */
.shift-card {
    border-radius: 6px;
    padding: 10px;
    font-size: 11px;
    line-height: 1.2;
    cursor: pointer;
    left:50px
}

/* VARIASI SHIFT */
.shift-card.pagi {
    background: #dbeafe;
    color: #1d4ed8;
}

.shift-card.sore {
    background: #fef3c7;
    color: #92400e;
}

.shift-card.malam {
    background: #e9d5ff;
    color: #6b21a8;
}

.shift-card.libur {
    background: #fee2e2;
    color: #991b1b;
}

.shift-card.lepas-off {
    background: #fffdf3;
    color: #ad6c0b;
}

.sticky-left.sticky-top {
    z-index: 20;
    background: #e2e8f0;
}
/* TEXT */
.shift-nama {
    font-weight: bold;
}

.shift-jam {
    font-size: 10px;
    opacity: 0.8;
}

.empty-shift {
      grid-column: span 30;
    display: flex;
    justify-content: left;
    align-items: left;
    font-size: 14px;
    color: red;
    background-color:#fee2e2;
}

.jadwal-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr); /* 7 kolom = 1 minggu */
  gap: 10px;

}


/* Hari biasa */
.hari-kerja {
  background-color: #e3f2fd; /* pale blue */
  padding: 10px;
  border-radius: 8px;
}

/* Sabtu & Minggu */
.hari-libur {
  background-color: #fdecea; /* pale red */
  padding: 10px;
  border-radius: 8px;
}

/* isi */
.hari {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.hari label {
  font-weight: bold;
}

.hari small {
  font-size: 12px;
  margin-bottom: 5px;
}

.hari select {
  width: 100%;
}

.hari input {
  width: 50px;
  text-align: center;
  padding: 5px;
}

.hari small {
  font-size: 12px;
  color: #666;
}

.header-hari {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  font-weight: bold;
  margin-bottom: 10px;
}

.kosong {
  visibility: hidden;
}

    </style>

    <body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":true, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
            <?php $this->load->view("layout/section/sidebar"); ?>
            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
                    <?php $this->load->view("layout/section/topbar"); ?>

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
                        $info = $this->session->flashdata("message");
                        $id_pegawai = $this->session->userdata("id_pegawai");
                        // $periode = date('Y-m');
                        $periode_bulan = $this->session->userdata( "periode_bulan" );
                        $periode_tahun = $this->session->userdata( "periode_tahun" );

                        //print_array($this->session->all_userdata());

                        if ($periode_bulan == "") {
                            $bulan = date("m");
                            $tahun = date("Y");
                        } else {
                            $bulan = $periode_bulan;
                            $tahun = $periode_tahun;
                        }


                        $nm_bulan = getBulan($bulan);

                        $periode = $tahun . "-" . $bulan;
                        $periode = date("Y-m", strtotime($periode));

                        $listBulan = array_bulan();

                        $lastDateMonth = date("t", strtotime($periode));
                        $cart_contents = $this->cart->contents();


                        ?>

                    <div class="col-xxl-12col-lg-12">
                        <div class="card widget-flat">
                            <div class="card-body text-left">
                                <h4>Jadwal Dinas Pegawai</h4>
                                <br>
                                Periode :  <strong><?php echo date( "F Y", strtotime($periode)); ?></strong>

                                <div class="row mb-2">
                                    <div class="col-md-2">

                                        <select  name="bulan" id="bulan" class="form-control">
                                                <option value="">Bulan</option>
                                                <?php for (  $b = 0; $b <  count($listBulan); $b++ ) {
                                                    $no_bulan = $b + 1;

                                                    if ($bulan == $b) {
                                                        $selc ="selected";
                                                    } else {
                                                        $selc = "";
                                                    }

                                                    echo '<option value="' . $b . '" ' . $selc .   ">" .  $listBulan[$b] .  "</option>";
                                                } ?>
                                            </select>
                                    </div>
                                    <div class="col-md-2">

                                            <select  name="tahun" id="tahun"  class="form-control">
                                                <option value="">Tahun</option>
                                                <?php for (
                                                    $b = 2023;
                                                    $b < 2030;
                                                    $b++
                                                ) {
                                                    if ( $periode_tahun ==  $b  ) {
                                                        $selc2 = "selected";
                                                    } else {
                                                        $selc2 = "";
                                                    }

                                                    echo '<option value="' . $b .  '" ' . $selc2 ."> " . $b ."</option>";
                                                } ?>
                                            </select>
                                    </div>
                                    <div class="col-md-4">


                                    </div>
                                </div>

                                <div class="progress d-none">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                                </div>

                                <button type="button" class="btn btn-primary float-end ms-1" data-bs-toggle="modal"  data-bs-target="#tambahpegawai">
                                    Tambah Pegawai
                                    </button>
                                    <a href="<?php echo base_url(); ?>admin_jadwal_shift/summary/<?php echo $this->uri->segment( 3); ?>" class="btn btn-info float-end">Lihat Jam Kerja</a>
                                <div class="clearfix"></div>

                                        <div class="shift-wrapper  mt-2">

                                            <!-- HEADER -->
                                            <div class="grid header">
                                                <div class="cell sticky-left sticky-top header-nama">Pegawai</div>


                                                    <?php for (   $i = 1;   $i <$lastDateMonth + 1; $i++ ) {
                                                            $date = $periode .   "-" .  $i;

                                                            $tanggal = format_db( $date );
                                                            $day = date(  "l",  strtotime(  $tanggal ));
                                                            if ( $day == "Sunday"  ) {
                                                                $hari =  "Mg";
                                                            } elseif ( $day =="Monday" ) {
                                                                $hari = "Sn";
                                                            } elseif (  $day ==  "Tuesday" ) {
                                                                $hari =  "Sl";
                                                            } elseif ( $day == "Wednesday"  ) {
                                                                $hari = "Rb";
                                                            } elseif (  $day == "Thursday" ) {
                                                                $hari = "Km";
                                                            } elseif (  $day ==  "Friday"  ) {
                                                                $hari = "Jm";
                                                            } else {
                                                                $hari = "Sb";
                                                            }

                                                            echo '   <div class="cell sticky-top header-tanggal">
                                                                    '. $i .' <br><small>'. $hari .'</small>
                                                                </div>';
                                                        } ?>

                                                <!-- loop tanggal -->
                                                <!-- <div class="cell sticky-top header-tanggal">
                                                    1 <br><small>Sn</small>
                                                </div>
                                                <div class="cell sticky-top header-tanggal">
                                                    2 <br><small>Sl</small>
                                                </div> -->
                                            </div>

                                            <!-- ROW PEGAWAI -->
                                                <?php
                                                    $id_bagian = $this->uri->segment(3);
                                                    for ( $i = 0;  $i < count($list_pegawai); $i++ ) {
                                                        $id_pegawai = $list_pegawai[$i] ->id_pegawai;
                                                        $nip = $list_pegawai[$i]->nip;
                                                        $pin = $list_pegawai[$i]->pin;
                                                        $jns_pegawai = $list_pegawai[$i]->jns_pegawai;
                                                        $nama =  $list_pegawai[$i]->nama;

                                                        $shiftKerja = $this->Shift_model->getShiftPerbulan( $pin,$periode);

                                                    //print_array($shiftKerja);


                                                    echo' <div class="grid row">
                                                            <div class="cell sticky-left nama">
                                                                <a href="'.base_url().'admin_jadwal_shift/view_absensi/'.$id_bagian.'/'.$pin.'/'.$bulan.'/'.$tahun.'/'.$jns_pegawai.'">'.$nama.'</a> </div>';?>

                                                            <?php

                                                                if(count($shiftKerja) == 0){

                                                                            echo '<div class="cell empty-shift">
                                                                                <div class="shift-card fs-5"> Belum ada jadwal untuk bulan ini </div>
                                                                                <button class="btn btn-sm btn-primary create-jadwal mt-2"
                                                                                data-id="'.$id_pegawai.'"
                                                                                data-pin="'.$pin.'"
                                                                                data-name="'.$nama.'"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalBuatJadwal">Buat Jadwal</button>
                                                                            </div>';

                                                                }else{
                                                                        for ($a = 0; $a < count($shiftKerja); $a++) :


                                                                            $t = $a + 1;
                                                                            $tanggal = $periode . "-" . $t;
                                                                            $matrikId = $id_pegawai . "_" . $tanggal;
                                                                            $tgl = format_db($tanggal);

                                                                            $shift = $shiftKerja[$a]->shift;
                                                                            $jam_masuk = $shiftKerja[$a]->jam_masuk;
                                                                            $jam_keluar = $shiftKerja[$a]->jam_keluar;


                                                                            $jam_masuk = date("H:i", strtotime($jam_masuk));
                                                                            $jam_keluar = date("H:i", strtotime($jam_keluar));


                                                                            switch ($shift) {
                                                                                case 'OFF':
                                                                                    $shift_class = "libur";
                                                                                    $jam_masuk = "";
                                                                                    $jam_keluar = "";
                                                                                    break;
                                                                                case 'L-OFF':
                                                                                    $shift_class = "lepas-off";
                                                                                    break;
                                                                                case 'SM':
                                                                                    $shift_class = "sore";
                                                                                    break;
                                                                                case 'M':
                                                                                    $shift_class = "malam";
                                                                                    break;

                                                                                default:
                                                                                    $shift_class = "pagi";
                                                                                    break;
                                                                            }
                                                                        ?>

                                                                        <div class="cell edit-shift" data-id_pegawai="<?=$id_pegawai;?>"
                                                                        data-id="<?= $shiftKerja[$a]->id;?>" data-bs-toggle="modal"
                                                                        data-bs-target="#modalEditShift">
                                                                            <div class="shift-card <?php echo $shift_class; ?> btn-change-shift" id="<?php echo $matrikId; ?>" value="<?php echo $id_pegawai; ?>">
                                                                                <div class="shift-nama"><?php echo $shift; ?></div>
                                                                                <div class="shift-jam"><?php echo $jam_masuk; ?> - <?php echo $jam_keluar; ?></div>
                                                                            </div>
                                                                        </div>

                                                                    <?php endfor;

                                                                } ?>

                                                        </div>
                                                     <?php  } ?>

                                        </div>
                                    </div>

                                </div>
                            </div> <!-- end col-->
                        </div>
                     </div>
                  </div> <!-- container -->
                </div> <!-- content -->



                 <div class="modal fade" id="modalBuatJadwal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myLargeModalLabel">Input Jadwal shift  Pegawai </h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body">
                               <form action="<?php echo base_url();?>admin_jadwal_shift/simpan_shift_kerja" method="post">
                                <button type="submit" class="btn btn-success float-end">Simpan</button>
                                        <table>
                                            <tr>
                                                <td width="150">Nama </td>
                                                <td id="data_info_nama" class="fw-bold">  &nbsp; --nama pegawai--</td>
                                                <input type="hidden" name="id_pegawai" id="id_pegawai" value="">
                                                <input type="hidden" name="pin" id="pin_pegawai" value="">

                                            </tr>
                                            <tr>
                                                <td> Periode   </td>
                                                <td id="data_periode"  class="fw-bold">: &nbsp; <?php echo getNamaBulan($periode_bulan) . ' ' . $tahun; ?></td>
                                            </tr>
                                        </table>
                                        <div class="header-hari">
                                                <div class="bg-light p-2">Min</div>
                                                <div  class="bg-light p-2">Sen</div>
                                                <div class="bg-light p-2">Sel</div>
                                                <div class="bg-light p-2">Rab</div>
                                                <div class="bg-light p-2">Kam</div>
                                                <div class="bg-light p-2">Jum</div>
                                                <div class="bg-light p-2">Sab</div>
                                            </div>

                                        <form>
                                                <div class="jadwal-grid">
                                                    <!-- Generate 31 hari -->
                                                    <?php
                                                        $tanggal_awal = date('Y-m-01', strtotime("$tahun-$bulan-01"));
                                                        $hari_pertama = date('w', strtotime($tanggal_awal));
                                                        $jumlah_hari  = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

                                                        $nama_hari = [
                                                            'Sun' => 'Min',
                                                            'Mon' => 'Sen',
                                                            'Tue' => 'Sel',
                                                            'Wed' => 'Rab',
                                                            'Thu' => 'Kam',
                                                            'Fri' => 'Jum',
                                                            'Sat' => 'Sab'
                                                        ];
                                                        // slot kosong sebelum tanggal 1
                                                        for ($i = 0; $i < $hari_pertama; $i++) {
                                                            echo '<div class="kosong"></div>';
                                                        }
                                                        for ($i = 1; $i <= $jumlah_hari; $i++) {

                                                            $tanggal = date('Y-m-d', strtotime("$tahun-$bulan-$i"));
                                                            $hari_en = date('w', strtotime($tanggal)); // ganti pakai angka biar gampang

                                                            // mapping hari
                                                            $nama_hari = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
                                                            $hari_id = $nama_hari[$hari_en];

                                                            // warna
                                                            if ($hari_en == 0 || $hari_en == 6) {
                                                                $class = 'hari-libur';
                                                            } else {
                                                                $class = 'hari-kerja';
                                                            }
                                                            echo '<div class="hari '.$class.'">
                                                                    <label>'. $i .'</label>
                                                                    <small>'. $hari_id .'</small>
                                                                    <select class="form-control form-control-sm" name="shift['.$tanggal.']" id="shift_'. $i .'">
                                                                      ';

                                                                        foreach ($shift_kerja as $sk) {
                                                                            echo '<option value="'. $sk->kode_shift .'">'. $sk->kode_shift .'</option>';
                                                                        }

                                                            echo   '</select>
                                                                </div>';
                                                        }
                                                        ?>
                                                </div>
                                        </form>

                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <div class="modal fade" id="modalEditShift" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-lg">
                       <div class="modal-content">
                           <div class="modal-header">
                               <h4 class="modal-title" id="myLargeModalLabel">Ubah shift  Pegawai </h4>
                               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                           </div>
                           <div class="modal-body">
                                <div id="info_date">Senin, 19 April 2026</div>
                              
                                <div class="card-shift">
                                        <?php
                                            foreach ($shift_kerja as $sk) {
                                                  echo ' <button type="button" class="mini-card pilih-shift" value="'. $sk->kode_shift .'">
                                                            <div class="fw-bold"> '. $sk->kode_shift .'</div> '. $sk->nama_shift .'
                                                        </button>';
                                        }

                                        ?>
                                </div>

                           </div>
                       </div><!-- /.modal-content -->
                   </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->

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
        <?php $this->load->view("layout/section/theme-setting"); ?>


        <!-- bundle -->
        <script src="<?php echo base_url(); ?>assets/new/js/vendor.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/new/js/app.min.js"></script>

        <!-- Todo js -->
        <script src="<?php echo base_url(); ?>assets/new/js/ui/component.todo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script type="text/javascript">


            $(".create-jadwal").click(function(e) {
                let id_pegawai = $(this).data("id");
                let nama = $(this).data("name");
                let pin = $(this).data("pin");
                //alert(id_pegawai);

                $("#data_info_nama").html(": &nbsp;"+nama+" - "+pin);
                $("#id_pegawai").val(id_pegawai);
                $("#pin_pegawai").val(pin);

            });

            $("#nama_pegawai").change(function(){
                var id_pegawai = $(this).val();
                //alert(nama_pegawai);

                $.ajax({
                            type: "POST",
                            url: "<?php echo base_url(); ?>admin_jadwal_shift/add_cart",
                            data: "id_pegawai=" + id_pegawai,
                            success: function(return_data) {
                                $("#textAreaPegawai").html(return_data);

                            }
                        });




            });

              matrikId = '';
              id_pegawai = '';


              $(".btn-change-shift").click(function(){

                matrikId   = $(this).attr("id");
                id_pegawai = $(this).val();
                $(".box-shift").show();

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>admin_jadwal_shift/getInfo",
                        data: "data_post=" + matrikId,
                        success: function(return_data) {
                            //alert(return_data);
                            $("#info_date").html(return_data);

                        }
                    });
              });


              $(".close_modal").click(function(){
                $(".box-shift").hide();
              });


              $(".pilih-shift").click(function(){
                var kode_shift = $(this).val();

                if(kode_shift=='L-OFF'){
                  var shift_choose = '<span class="bg-warning-lighten">L-OFF</span>';
                }else if(kode_shift=='OFF'){
                  var shift_choose = '<span class="bg-danger-lighten">OFF</span>';

                }else{
                    var shift_choose = '<span class="bg-success-lighten">'+kode_shift+'</span';
                }

                $("#"+matrikId).html(shift_choose);



                $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>admin_jadwal_shift/insertShiftKerja",
                        data: "data_post=" + matrikId+"&kode_shift="+kode_shift,
                        success: function(return_data) {

                            $(".btn-close").trigger("click");
                            //$("#data_info").html(return_data);

                              Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Data shift kerja berhasil disimpan',
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                        }
                    });

                   //$("#div_change_shift").hide();
              });


              $(".btn-close").click(function(){
                $("#div_change_shift").hide();
              });



              $("#bulan").change(function(){
                var bulan = $(this).val();
                var tahun = $("#tahun").val();
                $(".progress").removeClass("d-none");

                $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url(); ?>admin/presensi/set_session_periode",
                            data:"bulan="+bulan+"&tahun="+tahun,
                            success:function(msg){
                             window.location.reload();
                              //$("#modal-form").html(msg);
                              //console.log(msg);
                            }

                      });

              });


              $("#tahun").change(function(){
                var bulan = $("#bulan").val();
                var tahun = $(this).val();
                $(".progress").removeClass("d-none");

                $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url(); ?>admin/presensi/set_session_periode",
                            data:"bulan="+bulan+"&tahun="+tahun,
                            success:function(msg){
                             window.location.reload();
                              //$("#modal-form").html(msg);
                              //console.log(msg);
                            }

                      });

              });


              $("#periode").click(function(){
                $(".form-periode").show();
              });



        </script>




    </body>
</html>
