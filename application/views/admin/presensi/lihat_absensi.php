<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
<?php  $this->load->view('master/meta');?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/style.css">
  
  
<style>
   table{
                width: 100% ;
            }

            .table-absensi th{
                border: 1px solid #EEE;
                padding: 10px;
                text-align: center;
                font-size: 15px;
            }
            .table-absensi td{
                border: 1px solid #EEE;
                padding: 8px;
                text-align: center;
                font-size: 14px;
                color:#555

            }
            .btn-hover-show{
              border:none;
              color:#FFF;
              background:#FFF;
            }

            .btn-hover-show:hover{
              border:none;
              color:blue;
              background:#F8F8F8;
            }

            .hover-show{
              color:#FFF;
            }

            .hover-show:hover{
              color:#F00;
            }

            .popup-click{
              width:500px;
              height:auto;
              position: fixed;
              background:#FFF;
              z-index:999;
              margin-left:15%;
              margin-top:10%;
              padding:20px;
              display: none;
              box-shadow: -2px 8px 23px 5px rgba(204,204,204,0.75);
              -webkit-box-shadow: -2px 8px 23px 5px rgba(204,204,204,0.75);
              -moz-box-shadow: -2px 8px 23px 5px rgba(204,204,204,0.75);

            }
            .btn-close-shift{
              position: absolute;
              right: 10px;
              top:10px;
              cursor: pointer;
            }

            .btn-close-shift:hover{
              color:#900;
            }
           .btn-shft {
              width:80px;
              padding:5px;
              display:inline-block;
              text-align:center;
              font-size:12px;
              color:#FFF;
             -webkit-transition: all 0.3s;
              -moz-transition: all 0.3s;
              transition: all 0.3s;
              border:none;
           }

           .btn-info{
            background:#2eafeb
           }
           .btn-info:hover{
            background:#19cd7c;
           }

           .btn-warning{
            background:#f3bb14
           }
           .btn-warning:hover{
            background:#19cd7c;
           }
           
           .btn-danger{
            background:#f98282;
           }
           .btn-danger:hover{
            background:#19cd7c;
           }
           

          
          
</style>
</head>

<body>
 <!--  Body Wrapper -->
 <div id="main-wrapper">
    <!-- Sidebar Start -->
    <aside class="left-sidebar with-vertical">
      <div><!-- ---------------------------------- -->
      <!-- Start Vertical Layout Sidebar -->
      <!-- ---------------------------------- -->

      <?php $this->load->view('layout/section/sidebar');?>

    </aside>

    <!--  Sidebar End -->
    <div class="page-wrapper">
      <!--  Header Start -->
      <?php $this->load->view('layout/section/header');?>

  
      <?php

            #print_array($this->session->userdata);
            // $bulan = $this->session->userdata('periode_bulan');
            // $tahun = $this->session->userdata('periode_tahun');

            $periode_bulan = $this->session->userdata('periode_bulan'); 
            $periode_tahun = $this->session->userdata('periode_tahun'); 
            $id_pkm_sess   = $this->session->userdata('id_pkm');

          
            if($periode_bulan=='') {
                $bulan = date('m');
                $tahun = date('Y');
  
              }else{
                $bulan = $periode_bulan;
                $tahun = $periode_tahun;
              }

              
 
            $nm_bulan = getBulan($bulan);
            $periode = $tahun.'-'.$bulan;
            $periode = date('Y-m', strtotime($periode));


            $lastDate = date('t', strtotime($periode)) + 1;

            $id_pegawai = $this->uri->segment(4);

            $nip = $data_pegawai[0]->nip;
            $shift = $jns_jam_kerja= $data_pegawai[0]->jns_jam_kerja;
            $nama_pegawai = $data_pegawai[0]->nama;
            $id_puskesmas = $data_pegawai[0]->id_puskesmas;
         
            $puskesmas = $this->Presensi_model->getNamaPuskesmas($id_puskesmas);
            $message = $this->session->flashdata('message');


            $pin = substr($nip, -4);

         



            ?>

      <div class="body-wrapper">
         <div class="container-fluid">
             <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
                <div class="card-body px-4 py-3">
                  <div class="row align-items-center">
                    <div class="col-9">
                      <h4 class="fw-semibold mb-8">Absensi Pegawai</h4>
                      <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="<?php echo base_url();?>admin/dashboard/index" >Home</a>
                          </li>
                         
                           <li> &nbsp; / &nbsp; </li>
                          
                          <li class="breadcrumb-acive">Absensi  Pegawai</li>
                        
                        </ol>
                      </nav>
                    </div>
                  
                  </div>

                  
                </div>
                


              <div class="popup-click">
                  <form action="<?php echo base_url();?>admin/presensi/update_shift_harian/<?php echo $pin.'/'.$id_pegawai;?>" method="post">
                    <h4>Edit Shift</h4>
                    <span class="btn-close-shift"> <i class="ti ti-x me-1 fs-4"></i></span></span>
                      <h6>Tanggal : <span class="tanggal-shift"> </h6>
                      <br>
                      <input type="hidden" name="tgl_shift" value="" id="tgl_shift">
                        <?php                                                           
                          for ($s=0; $s < count($data_shift_kerja) ; $s++) { 
                              if($data_shift_kerja[$s]->kode_shift=='OFF'){
                                $btn_shift = ' btn-danger';
                              }else if($data_shift_kerja[$s]->kode_shift=='L-OFF'){
                                $btn_shift = ' btn-warning';
                              }else{
                                $btn_shift = ' btn-info';
                              }
                              echo '<button type="submit" name="shift" value="'.$data_shift_kerja[$s]->kode_shift.'" class="btn-shft '.$btn_shift.' m-1">'.$data_shift_kerja[$s]->kode_shift.'</button>';
                          }

                      ?>
                      </form>
              </div>


              </div>
                         <div class="dropdown float-end">
                            <a class="text-decoration-none" href="javascript:void(0)" id="balance-dd" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots fs-4"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="balance-dd" style="">
                                <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"  data-bs-target="#modal-loading">
                                    <i class="ti ti-share me-1 fs-4"></i>  Data Cuti </a>
                                </li>
                                <li>
                                <a class="dropdown-item" href="<?php echo base_url();?>admin/presensi/sinkron_absensi/<?php echo $pin.'/'.$id_pegawai;?>" >
                                    <i class="ti ti-download me-1 fs-4"></i> Tarik Data Absensi  </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo base_url();?>admin/presensi/update_data_rekap/<?php echo $pin.'/'.$id_pegawai;?>">
                                    <i class="ti ti-info-circle me-1 fs-4"></i>Update Rekap </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo base_url();?>admin/presensi/print_absensi/<?php echo $id_pegawai;?>">
                                    <i class="ti ti-printer me-1 fs-4"></i>Print Absensi </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="<?php echo base_url();?>admin/presensi/purgeDataAbsensi/<?php echo $id_pegawai.'/'.$pin;?>">
                                    <i class="ti ti-refresh me-1 fs-4"></i>Purge Absensi </a>
                                </li>
                            </ul>
                        </div>

                        <div class="clearfix"></div>

                 <div class="row">
                   <div class="col-lg-12 d-flex align-items-stretch">
                      <div class="card w-100">
                        <div class="card-body p-4">
                         <div class="col-md-12  my-4">
                            <h4>
                              <?php echo $nama_pegawai ;?> 
                              <a href="<?php echo base_url();?>admin/presensi/edit_data_pegawai/<?php echo $pin.'/'.$id_pegawai;?>" class="text-primary" title="edit data pegawai" >
                                <i class="fas fa-edit"></i>
                              </a>
                              <br>
                              <span class="text-muted fs-3"><?php echo $nip ;?></span>

                            </h4>

                            <small><?php echo $puskesmas;?></small>
                            <?php

                            if($shift ==1){
                                  echo '<span class="badge bg-info-subtle text-info">Shift</span>';
                            }
                            ?>
                            <br>
                            Periode : <strong> <?php echo date('F Y', strtotime($periode));?> </strong>


                            <div class="p-2 mt-2">
                             <h5>Data Rekap Absensi</h5>


                             <?php
                                if (!empty($dataRekap)) {

                                  $id_rekap = $dataRekap[0]->id;
                                  $status = $dataRekap[0]->status;

                                  if($status==0){
                                    $status_absen = '<span class="badge bg-warning-subtle text-warning">  <i class="ti ti-times"></i> Absensi Belum sesuai</span> 
                                    <a href="'.base_url().'admin/presensi/check_ok/'.$pin.'/'.$id_pegawai.'/'.$id_rekap.'" class="btn btn-light mt-4">
                                    <i class="ti ti-check"></i> Check Sesuai</a>';
                                  }else{
                                    $status_absen = '<span class="badge bg-success-subtle  text-success"> <i class="ti ti-check"></i> Absensi Sudah  sesuai</span>';
                                  }

                                  echo ' <table class="table table-bordered">
                                            <tr>
                                              <td>Telat 
                                                <br> <span class="text-danger">'.$dataRekap[0]->telat.' menit</span>
                                              </td>
                                              <td>Pulang Awal  <br> <span class="text-danger">'.$dataRekap[0]->pulang_awal.' menit</span></td>
                                              <td>Sakit  <br> <span class="text-danger">'.$dataRekap[0]->sakit.' hari</span></td>
                                              <td>Izin<br> <span class="text-danger">'.$dataRekap[0]->izin.' hari</span></td>
                                              <td>Cuti <br> <span class="text-danger">'.$dataRekap[0]->cuti.' hari</span></td>
                                              <td class="text-end">  '. $status_absen.' </td>
                                            </tr>
                                            
              
                                          </table>';
                                }else{
                                  echo '<div class="alert alert-warning text-warning">Warning! Data Absensi belum direkap</div>';
                                }
                             ?>
                            
                            </div>
                          

                         </div>
                                                         
                            <a href="<?php echo base_url();?>admin/presensi/update_data_rekap/<?php echo $pin.'/'.$id_pegawai;?>" class="btn btn-outline-success   mb-3">
                            <i class="fas fa-refresh"></i>
                            Update Rekap
                            </a>
                           
                            <a class="btn btn-success  me-2 float-end  mb-3"href="<?php echo base_url();?>admin/presensi/sinkron_absensi/<?php echo $pin.'/'.$id_pegawai;?>" >
                              <i class="ti ti-download me-1 fs-4"></i> Sinkron Data Absensi 
                            </a>
                            <a href="<?php echo base_url();?>admin/presensi/absensi_raw/<?php echo $pin.'/'.$id_pegawai;?>" class="btn btn-warning float-end mb-3 me-2">
                                <i class="fas fa-file"></i>  Absensi RAW
                            </a>
                            <a href="<?php echo base_url();?>admin/presensi/edit_shift/<?php echo $pin.'/'.$id_pegawai;?>" class="btn btn-info float-end mb-3 me-2">
                                <i class="fas fa-pencil"></i>  Edit Shift
                            </a>                            
                              <form action="<?php echo base_url();?>admin/presensi/generate_shift" class="d-inline-block" method="post">
                                  <input type="hidden" name="data_post" value="<?php echo $pin.'/'.$id_pegawai;?>">

                                      <button type="submit" class="btn btn-outline-primary  mb-3">
                                      <i class="fas fa-refresh"></i> Generate Shift
                                  </button> 
                              </form>
                            <div class="clearfix"></div>
                             <div class="table-responsive mt-4">
                                 <table class="table-absensi table-sm">
                                    <thead>
                                        <tr>
                                            <th width="50" rowspan="2">Tanggal</th>
                                            <th width="100" rowspan="2">Hari</th>
                                            <th width="100" rowspan="2">Shift</th>
                                            <th width="160" colspan="2">Jam Kerja</th>
                                            <th width="160" colspan="2">Jam Absen</th>
                                
                                            <th width="100"  rowspan="2">Telat</th>
                                            <th width="100"  rowspan="2">P Awal</th>
                                            <th  rowspan="2">Keterangan</th>
                                     
                                        
                                        </tr>

                                        <tr>
                                            <th width="100">  Masuk</th>
                                            <th width="100"> Pulang</th>
                                            <th width="100"> Masuk</th>
                                            <th width="100"> Pulang</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                          <?php

                                                  $tgl_now = date('Y-m-d');
                                            
                                                      $totalTelat = 0;
                                                      $totalPawal = 0;  

                                                      $totalIzin = 0;
                                                      $totalSakit = 0;
                                                      $totalCuti = 0;


                                                  for ($t = 1; $t < $lastDate; $t++) {
                                                      $tanggal = $periode . '-' . $t;
                                                      $formatDate = date('Y-m-d', strtotime($tanggal));
                                                      $day = date('l', strtotime($tanggal));
                                                      $hari = getNamahari($tanggal);

                                                      $absensiHarian  = $this->Presensi_model->getDataAbsensi($pin, $formatDate);
                                                      if(!empty($absensiHarian)){
                                                          $kodeShift         = $absensiHarian[0]->shift;
                                                          $jamMasukKerja     = $absensiHarian[0]->jam_masuk;
                                                          $jamKeluarKerja    = $absensiHarian[0]->jam_pulang;
                                          
                                                          $absenMasuk         = $absensiHarian[0]->masuk;
                                                          $absenPulang        = $absensiHarian[0]->pulang;
                                                          $keterangan_absen   = $absensiHarian[0]->keterangan;

                                                          $telat         = $absensiHarian[0]->telat;
                                                          $p_awal         = $absensiHarian[0]->p_awal;
                                                      }else{
                                                          $kodeShift         = '';
                                                          $jamMasukKerja      = '';
                                                          $jamKeluarKerja    = '';
                                          
                                                          $absenMasuk        = '';
                                                          $absenPulang        = '';
                                                          $keterangan_absen   = '';

                                                          $telat         = 0;
                                                          $p_awal         = 0;

                                                      }




                                                      if($jns_jam_kerja == 'non_shift'){

                                                        //khusus untuk yang jam kerjanya shift
                                                        if($hari != 'Sabtu' && $hari != 'Minggu'){

                                                          
                                                          if($absenMasuk=='' && $jamMasukKerja != '' && $formatDate < $tgl_now ){
                                                            $absenMasuk = '<button class="text-danger btn bg-danger-subtle btn-action" value="'.format_view($tanggal).'"
                                                            data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg" >Alpha</button>';
                                                            $telat          = 300;
                                                          }

                                                              
                                                          if($absenPulang=='' && $jamKeluarKerja != ''  && $formatDate < $tgl_now ){
                                                            $absenPulang = '<button class="text-danger btn bg-danger-subtle btn-action" value="'.format_view($tanggal).'"
                                                            data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg">Alpha</button>';
                                                            $p_awal         = 150;
                                                          }


                                                        }

                                                              
                                                          $hariLibur = $this->Presensi_model->cekHariLibur($tanggal);
                                                          $hari_libur = false;
                                                          if(!empty($hariLibur )){
                                                            $kodeShift         = '';
                                                            $jamMasukKerja     = '-';
                                                            $jamKeluarKerja    = '-';
                                            
                                                            $absenMasuk         = '<span class="text-danger btn bg-danger-subtle fs-2">LIBUR NASIONAL</span>';
                                                            $absenPulang        =  '<span class="text-danger btn bg-danger-subtle fs-2">LIBUR NASIONAL</span>';
                                                            $keterangan_absen   = $hariLibur[0]->keterangan;

                                                            
                                                            $telat          = 0;
                                                            $p_awal         = 0;
                                                            $hari_libur = true;


                                                          }
                                                      }else{
                                                          if($kodeShift=='P' || $kodeShift=='S' || $kodeShift=='PS'){

                                                            
                                                            if($absenMasuk==''){
                                                              $absenMasuk = '<button class="text-danger btn bg-danger-subtle btn-action" value="'.format_view($tanggal).'"
                                                              data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg">Alpha</button>';
                                                              $telat         = 300;
                                                            }

                                                              if($absenPulang==''){
                                                                $absenPulang = '<button class="text-danger btn bg-danger-subtle btn-action" value="'.format_view($tanggal).'"
                                                                data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg">Alpha</button>';
                                                                $p_awal         = 150;
                                                              }
                                                           
                                                          }

                                                          if($kodeShift=='L-OFF'){
                                                            
                                                            if($absenPulang==''){
                                                              $absenPulang = '<button class="text-danger btn bg-danger-subtle btn-action" value="'.format_view($tanggal).'"
                                                              data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg">Alpha</button>';
                                                              $p_awal         = 150;
                                                            }
                                                          }
                                                            
                                                          if($kodeShift=='SM' ||$kodeShift=='M' ||$kodeShift=='PSM'){
                                                            if($absenMasuk=='' && $jamMasukKerja != '' ){
                                                              $absenMasuk = '<button class="text-danger btn bg-danger-subtle btn-action" value="'.format_view($tanggal).'"
                                                              data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg" >Alpha</button>';
                                                              $telat          = 300;
                                                            }

                                                          }



                                                      }


                                                      if($kodeShift=='OFF'){
                                                          $jamMasukKerja  = '';
                                                          $jamKeluarKerja  = '';
                                                          $bg_btn = 'btn-danger';
                                                        
                                                      }else if($kodeShift=='L-OFF'){
                                                        
                                                          $bg_btn = 'btn-warning';
                                                          $jamMasukKerja  = '-';
                                                         
                                                      }else{
                                                        $bg_btn = 'btn-info';
                                                      }

                                                      if($jamKeluarKerja=='00:00:00'){
                                                        $jamKeluarKerja  = '-';
                                                      }

                                                      if ($absenMasuk =='CUTI') {
                                                          $absenMasuk         = '<button class="text-success btn bg-success-subtle">CUTI</button>';
                                                          $absenPulang        =  '<button class="text-success btn bg-success-subtle">CUTI</button>';
                                                        }

                                                       if ($absenMasuk =='DLP') {
                                                          $absenMasuk         = '<button class="text-success btn bg-success-subtle  btn-delete-dl"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >DLP</button>';
                                                          $absenPulang        =  '<button class="text-success btn bg-success-subtle  btn-delete-dl"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >DLP</button>';
                                                        }

                                                        if ($absenMasuk =='IZIN') {
                                                          $absenMasuk         = '<button class="text-warning btn bg-warning-subtle  btn-delete-izin"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >IZIN</button>';
                                                          $absenPulang        =  '<button class="text-warning btn bg-warning-subtle  btn-delete-izin"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >IZIN</button>';
                                                        }

                                                        if ($absenPulang =='DLAK') {
                                                         
                                                            $absenPulang        =  '<button class="text-info btn bg-info-subtle  btn-delete-dl"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >DLAK</button>';
                                                        }

                                                        if ($absenMasuk =='SAKIT') {
                                                          $absenMasuk         = '<button class="text-warning btn bg-warning-subtle  btn-delete-sakit"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >SAKIT</button>';
                                                          $absenPulang        =  '<button class="text-warning btn bg-warning-subtle  btn-delete-sakit"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm" >SAKIT</button>';
                                                        }



                                                      $totalTelat = $totalTelat+$telat;
                                                      $totalPawal = $totalPawal+$p_awal;
              

                                                          echo '  <tr>
                                                                          <td class="text-center">' . $t . '</td>
                                                                          <td class="text-center">' . $hari . '</td>
                                                                          <td id="tr_'.$t.'" class="text-center">
                                                                          <button type="button" class="btn-shift text-white  fs-2 '.$bg_btn.' change-shift" value="'.format_view($tanggal).'">'. $kodeShift.'</button> </td>
                                                                         

                                                                          <td class="text-info text-center">'.$jamMasukKerja  .'</td>
                                                                          <td class="text-danger  text-center">'.$jamKeluarKerja.'</td>
                                                                          <td class="text-center">'.$absenMasuk;
                                                                          if($absenMasuk == ''){
                                                                            echo '<button class="btn-hover-show"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg"><i class="fa-solid fa-pencil"></i></button>';
                                                                          }else{
                                                                            echo '<a href="'.base_url().'admin/presensi/delete_absensi/'.$tanggal.'/'.$pin.'/'.$id_pegawai.'/0" class="float-end hover-show"><i class="fa-solid fa-trash"></i></a>';
                                                                          }

                                                                          echo '</td>
                                                                          <td class="text-center" class="td-jam-absen">'.$absenPulang;

                                                                          if($absenPulang != ''){
                                                                            echo '<a href="'.base_url().'admin/presensi/delete_absensi/'.$tanggal.'/'.$pin.'/'.$id_pegawai.'/1" class="float-end hover-show"><i class="fa-solid fa-trash"></i>  </a>';
                                                                          }else{
                                                                            echo '<button class="btn-hover-show"  value="'.format_view($tanggal).'" data-bs-toggle="modal" data-bs-target="#bs-example-modal-xlg"><i class="fa-solid fa-pencil"></i></button>';
                                                                          }

                                                                        
                                                                          echo '</td>
                                                                          <td class="text-center">'.$telat.'</td>
                                                                          <td class="text-center">'. $p_awal.'</td>
                                                                          <td style="text-align:left">'. $keterangan_absen.'</td>
                                                                         
                                                                          
                                                              </tr>';
                  
                                                              

                                                              

                                                  }


                                                  ?>
                                                <tr>
                                                    <td colspan="7"></td>
                                                    <td><?php echo $totalTelat ;?></td>
                                                    <td><?php echo $totalPawal ;?></td>
                                                    <td></td>
                                                    <td></td>
                                                
                                                </tr>
                                       </tbody>
                                    </table>
                                  </div>
                          </div>
                       </div>
                 </div>
              </div><!--row-->


              <?php

             

                  $cars = array (
                    array("success",'dlp','DL-PENUH'),
                    array("info",'dla','DL-AWAL'),
                    array("info",'dlh','DL-AKHIR'),
                    array("warning",'izin','IZIN'),
                    array("warning",'sakit','SAKIT'),
                    array("warning",'sakit2','SAKIT DGN SURAT')
                  );

                 # print_array($cars);

                  
              ?>
               <div class="modal fade" id="bs-example-modal-xlg" tabindex="-1"
                        aria-labelledby="bs-example-modal-lg" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header d-flex align-items-center">
                              <h4 class="modal-title" id="myLargeModalLabel">
                                Edit Data Absensi
                              </h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                          
                                <div class="tabs">
                                    <input type="radio" id="tab1" name="tab-control" checked>
                                    <input type="radio" id="tab2" name="tab-control">
                                    <input type="radio" id="tab3" name="tab-control">  
                                    <input type="radio" id="tab4" name="tab-control">
                                    <ul>
                                        <li title="Features"><label for="tab1" role="button"><span>Input Absen</span></label></li>
                                        <li title="Delivery Contents"><label for="tab2" role="button"><span>Input Jam Absen</span></label></li>
                                        <li title="Shipping"><label for="tab3" role="button"><span>Pengajuan Cuti</span></label></li>
                                        <li title="Returns"><label for="tab4" role="button"><span>Pengajuan Dinas Luar</span></label></li>
                                    </ul>
                                    <div class="slider">
                                        <div class="indicator"></div>
                                    </div>
                                    <div class="content">
                                        <section>
                                          <h2>Input Absen Ketidakhadiran</h2>
                                            <form method="post" action="<?php echo base_url().'admin/presensi/insert_absen_ketidakhadiran/'.$pin.'/'.$id_pegawai;?>">
                                           
                                            <label for="tgl_absensi">Tanggal</label> &nbsp;&nbsp;: &nbsp;&nbsp;
                                             <input type="text" name="tgl_absensi" class="form-control" readonly id="tgl_absensi" value="" style="width:150px">
                                            <br>
                                                <?php
      
                                                  for ($row = 0; $row < 6; $row++) {
                                                  
                                                        echo '  <div class="form-check mb-2">
                                                                  <input class="form-check-input '.$cars[$row][0].'" type="radio" required value="'.$cars[$row][2].'" name="jns_absensi" id="'.$cars[$row][1].'" value="option1">
                                                                  <label class="form-check-label" for="'.$cars[$row][1].'">'.$cars[$row][2].'</label>
                                                                </div>';
                                                    
                                                  
                                                  }
                                                ?>
                                                    <br>
                                                <textarea name="keterangan" id="keterangan" class="form-control mt-2" rows="2"></textarea>
                                                <br>
                                                <button type="submit" class="btn btn-info mt-2 float-end">Simpan</button>


                                          </form>
                                        </section>
                                        <section>
                                          <h2>Input Jam Absen</h2>
                                            <form method="post" action="<?php echo base_url().'admin/presensi/insert_absen_manual/'.$pin.'/'.$tanggal;?>">
                                            <input type="hidden" name="id_pegawai" value="<?php echo $id_pegawai;?>">
                                             <input type="hidden" name="tgl_absensi_edit" value="" id="tgl_absensi_edit">
                                                  <div class="row alert-info">
                                                        <div class="col-md-6">
                                                                Jam Masuk :  <br>
                                                            <input type="text" name="jam_masuk"  class="form-control">
                                                        </div>
                                                        <div class="col-md-6">
                                                            Jam Pulang : <br>
                                                            <input type="text" name="jam_pulang" class="form-control">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <button type="submit" class="btn btn-info float-end">Simpan</button>
                                            </form>
                                        </section>
                                        <section>
                                          <h2>Pengajuan Cuti</h2>
                                          <?php     
                                          for ($i=0; $i < count($dataCuti) ; $i++) { 

                                           echo '<table class="table table-borderless">
                                           <tr>
                                             <td>
                                               <label for="tgl_pengajuan">Tgl Pengajuan</label> <br> 
                                               <span class="text-danger">'.format_slash($dataCuti[$i]->tgl).'</span>
                                             </td>
                                             <td> Status Pengajuan Cuti <br>
                                              '.$dataCuti[$i]->status.'
                                               
                                           </td>
                                           </tr>
                                           <tr>
                                               <td>
                                                 <label for="tgl_pengajuan">Tgl Mulai Cuti  </label><br>
                                                 <span class="text-danger">'.format_slash($dataCuti[$i]->tgl_dari).'</span>
                                               </td>
                                               
                                               <td>
                                                 <label for="tgl_pengajuan">Tgl Akhir Cuti  </label><br>
                                                 <span class="text-danger">'.format_slash($dataCuti[$i]->tgl_sampai).'</span>
                                               </td>
                                           </tr>
                                           <tr>
                                             <td><label for="tgl_pengajuan">Jumlah Hari</label> <br>   <span class="text-danger">'.$dataCuti[$i]->hari_cuti.' hari </span> </td>
                                             <td><label for="tgl_pengajuan">Alasan</label> <br> '.$dataCuti[$i]->alasan_cuti.'</td>
                                           </tr>

                                         </table>';
                                          }
                                          ?>
                                          
                                        </section>
                                        <section>
                                          <h2>Pengajuan Dinas Luar</h2>
                                          <p id="data_pengajuan_dl"></p>
                                        </section>
                                    </div>
                                  </div>
                            </div>
                                
                             
                          
                            <div class="modal-footer">
                              <button type="button"
                                class="btn bg-danger-subtle text-danger font-medium waves-effect text-start"
                                data-bs-dismiss="modal">
                                Close
                              </button>
                            </div>
                          </div>
                          <!-- /.modal-content -->
                        </div>
                        
                        <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->
                    </div>
                   <div>



                   <div class="modal fade" id="bs-example-modal-sm" tabindex="-1" aria-labelledby="mySmallModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                          <div class="modal-content">
                            <div class="modal-header d-flex align-items-center">
                              <h4 class="modal-title" id="myModalLabel">
                                Delete Absensi
                              </h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <h4>
                                Apakah anda ingin menghapus absensi ini?
                              </h4>

                              <div  id="footer-button"></div>
                             
                             
                            </div>
                            <div class="modal-footer">

                              <button type="button" class="btn bg-danger-subtle text-danger font-medium waves-effect"
                                data-bs-dismiss="modal">
                                Close
                              </button>
                            </div>
                          </div>
                          <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                      </div>
                      <!-- /.modal -->
                    </div>
                  </div>
                </div>
              </div>
        
                  <script>
                    function handleColorTheme(e) {
                      $("html").attr("data-color-theme", e);
                      $(e).prop("checked", !0);
                    }
                  </script>

        <?php $this->load->view('layout/section/theme-setting.php');?>

        <?php $this->load->view('master/request-cuti.php');?>

  </div>
  <div class="dark-transparent sidebartoggler"></div>
  <!-- Import Js Files -->

    <script src="<?php echo LIBS_JS_PATH;?>jquery/dist/jquery.min.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>app.min.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>simplebar/dist/simplebar.min.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>sidebarmenu.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>theme.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>init.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>toastr-init.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>jquery.blockUI.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>block-ui.js"></script>


  </body>


<script>
  
  var pesan = '<?php echo $message;?>';
  if (pesan != '') {
    toastr.success(pesan, "Success!");
  }
   


    $(".btn-action").click(function() {
         var tanggal = $(this).val();
         var id_pegawai = '<?php echo $id_pegawai;?>';
         var pin = '<?php echo $pin;?>';
         $("#tgl_absensi").val(tanggal); 
         $("#tgl_absensi_edit").val(tanggal); 

            $.ajax({
                        
                        type:"POST",
                        dataType:"html",
                        url:"<?php echo base_url();?>admin/presensi/getDataPengajuanDL",
                        data:"tanggal="+tanggal+"&id_pegawai="+id_pegawai+"&pin="+pin,
                        success:function(msg){
                           
                            $("#data_pengajuan_dl").html(msg);
                            //console.log(msg);
                        }
                    
                    });
    });



    $(".btn-hover-show").click(function() {
         var tanggal = $(this).val();
         var id_pegawai = '<?php echo $id_pegawai;?>';
         var pin = '<?php echo $pin;?>';
         $("#tgl_absensi").val(tanggal); 
         $("#tgl_absensi_edit").val(tanggal); 

            $.ajax({
                        
                        type:"POST",
                        dataType:"html",
                        url:"<?php echo base_url();?>admin/presensi/getDataPengajuanDL",
                        data:"tanggal="+tanggal+"&id_pegawai="+id_pegawai+"&pin="+pin,
                        success:function(msg){
                           
                            $("#data_pengajuan_dl").html(msg);
                            //console.log(msg);
                        }
                    
                    });
    });


    
    $(".change-shift").click(function() {
         var tanggal = $(this).val();
        $(".popup-click").show();
        $(".tanggal-shift").html(tanggal);
        $("#tgl_shift").val(tanggal);
        
        
    });
 
    $(".btn-close-shift").click(function() {
        $(".popup-click").hide();
       
    });

  


    $(".btn-delete-dl").click(function() {
         var tanggal = $(this).val();

         $("#footer-button").html('<br><a href="<?php echo base_url();?>admin/presensi/delete_absensi_dl/<?php echo $pin.'/'.$id_pegawai;?>/'+tanggal+'" class="btn btn-success">Iya Hapus</a>');   
    });


</script>
  
</html>