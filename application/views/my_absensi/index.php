<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
<?php  $this->load->view('master/meta');?>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<style>
           .bg-grey{
            background-color: #F8F8F8 !important;
           }

           .table-sm td{
            padding:10px !important;
            font-size: 13px;
           }
         </style>
</head>

<body>
  <!-- <div class="toast toast-onload align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body hstack align-items-start gap-6">
      <i class="ti ti-alert-circle fs-6"></i>
      <div>
        <h5 class="text-white fs-3 mb-1">Welcome to Modernize</h5>
        <h6 class="text-white fs-2 mb-0">Easy to costomize the Template!!!</h6>
      </div>
      <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div> -->
  <!-- Preloader -->

  <div id="main-wrapper">
    <!-- Sidebar Start -->
    <aside class="left-sidebar with-vertical">
      <div><!-- ---------------------------------- -->
      <!-- Start Vertical Layout Sidebar -->
      <!-- ---------------------------------- -->


      <?php $this->load->view('layout/section/sidebar');?>

<!--
            <div  class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
              <div class="hstack gap-3">
                <div class="john-img">
                  <img
                    src="../assets/images/profile/user-1.jpg"
                    class="rounded-circle"
                    width="40"
                    height="40"
                    alt=""
                  />
                </div>
                <div class="john-title">
                  <h6 class="mb-0 fs-4 fw-semibold">Mathew</h6>
                  <span class="fs-2">Designer</span>
                </div>
                <button
                  class="border-0 bg-transparent text-primary ms-auto"
                  tabindex="0"
                  type="button"
                  aria-label="logout"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  data-bs-title="logout"
                >
                  <i class="ti ti-power fs-6"></i>
                </button>
              </div>
            </div>

            <!-- ---------------------------------- -->
            <!-- Start Vertical Layout Sidebar -->
            <!-- ---------------------------------- -
            </div> -->
    </aside>

    <!--  Sidebar End -->
    <div class="page-wrapper">
      <!--  Header Start -->
      <?php $this->load->view('layout/section/header');?>
      <!--  Header End -->


      <div class="body-wrapper">
        <div class="container-fluid">
          <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
                <div class="card-body px-4 py-3">
                  <div class="row align-items-center">
                    <div class="col-9">
                      <h4 class="fw-semibold mb-8">My Absensi</h4>
                      <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="../main/index.html" >Home</a>
                          </li>

                           <li> &nbsp; / &nbsp; </li>

                          <li class="breadcrumb-acive">My Absensi</li>
                        </ol>
                      </nav>
                    </div>
                    <div class="col-3">
                      <div class="text-center mb-n5">

                      </div>
                    </div>
                  </div>


                </div>
              </div>
              <?php

                $nama_user =  $this->session->userdata('nama');
                $nip_user =  $this->session->userdata('nip');
                $id_pegawai =  $this->session->userdata('id_pegawai');
                $photo = $this->Pegawai_model->getPhotoPegawai($nip_user);
                $message = $this->session->flashdata('message');


            ?>
            <div class="row">
                <!-- Column -->
                <?php


                $ls_bulan = array_bulan();

                //print_array($ls_bulan);
                $tahun = date('Y');
                // $rekapTerlmbt = array();
                // $rekapPulangAwal = array();
                // $rekapIzin = array();
                // $rekapSakit = array();
                // $rekapCuti = array();

                //   for ($i=1; $i < 13 ; $i++) {

                //     $periode = $tahun.'-'.$i;
                //     $periode = date('Y-m', strtotime($periode));
                //   // $dataRekap = $this->Presensi_model->getDataRekapAbsensiPegawai($id_pegawai, $periode);

                //     if(!empty($dataRekap)){
                //       $telat = $dataRekap[0]->telat;
                //       $pulang_awal = $dataRekap[0]->pulang_awal;
                //       $izin = $dataRekap[0]->izin;
                //       $sakit = $dataRekap[0]->sakit;
                //       $sakit_dgn_sk = $dataRekap[0]->sakit_dgn_sk;
                //       $cuti = $dataRekap[0]->cuti;
                //     }else{
                //       $telat = 0;
                //       $pulang_awal = 0;
                //       $izin =  0;
                //       $sakit =  0;
                //       $sakit_dgn_sk =  0;
                //       $cuti = 0;
                //     }

                //     array_push($rekapTerlmbt, $telat);
                //     array_push($rekapPulangAwal, $pulang_awal);
                //     array_push($rekapIzin, $izin);
                //     array_push($rekapSakit, $sakit);
                //     array_push($rekapCuti, $cuti);

                //   }

                  $arrayBulan = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des');



            ?>
                <div class="col-lg-12">
                     <h4>Rekapitulasi Absensi</h4>
                      <table class="table table-bordered table-sm">
                        <tr>
                           <td class="bg-grey">Absensi</td>
                          <?php
                            for ($i=0; $i < 12 ; $i++) { 
                              echo ' <th class="text-center bg-grey"> '.$arrayBulan[$i].' <br>
                                <a href="'.base_url().'absensi/lihat_absensi/'.($i+1).'/'.$tahun.'" class="text-primary fs-2" style="font-weight:400"> Lihat
                                </a>
                              </th>';
                            }
                          ?>

                          <th  class="bg-grey">Total</th>
                         
                        </tr>
                        <tr>
                          <td>Terlambat</td>
                          <?php
                          $totalTerlmbat = 0;
                            for ($i=0; $i < 12 ; $i++) { 

                              $terlambat = @$dataRekap[$i]->telat;
                              if($terlambat==''){
                                  $terlambat = 0;
                              }
                              $totalTerlmbat = $totalTerlmbat+$terlambat;

                              echo ' <td class="text-end">'.$terlambat.'</td>';
                            }
                          ?>
                          <td class="text-end bg-grey"> <strong><?php echo $totalTerlmbat ;?> </strong> </td>
                        </tr>
                        <tr>
                          <td>Pulang Awal</td>
                          <?php
                              $totalPA = 0;
                            for ($i=0; $i < 12 ; $i++) { 

                                 $pulang_awal = @$dataRekap[$i]->pulang_awal;
                                  if($pulang_awal==''){
                                     $pulang_awal = 0;
                                  }
                              
                                $totalPA = $totalPA+$pulang_awal;
                              echo '  <td class="text-end">'.$pulang_awal.'</td>';
                            }
                          ?>
                           <td class="text-end bg-grey"> <strong><?php echo $totalPA ;?> </strong> </td>
                        </tr>
                        <tr>
                          <td>Izin</td>
                          <?php
                            $totalIzin = 0;
                            for ($i=0; $i < 12 ; $i++) { 
                                
                                 $izinAbsen = @$dataRekap[$i]->izin;
                                  if($izinAbsen==''){
                                     $izinAbsen = 0;
                                  }
                         

                              $totalIzin = $totalIzin+$izinAbsen;
                              echo ' <td class="text-end">'.$izinAbsen.'</td>';
                            }
                          ?>
                           <td class="text-end bg-grey"> <strong><?php echo $totalIzin ;?> </strong> </td>
                        </tr>
                        <tr>
                          <td>Sakit</td>
                          <?php

                            $totalSakit = 0;
                            for ($i=0; $i < 12 ; $i++) { 
                            
                                $sakitAbsen = @$dataRekap[$i]->sakit;
                                  if($sakitAbsen==''){
                                     $sakitAbsen = 0;
                                  }
                         
                         

                              $totalSakit = $totalSakit+$sakitAbsen;
                              echo ' <td class="text-end">'.$sakitAbsen.'</td>';
                            }
                          ?>
                            <td class="text-end bg-grey"> <strong><?php echo $totalSakit ;?> </strong> </td>
                        </tr>

                        <tr>
                          <td>Sakit Dgn Surat</td>
                          <?php
                          
                           $totalSakit2 = 0;
                            for ($i=0; $i < 12 ; $i++) { 
                                
                                 $sakit_dgn_sk = @$dataRekap[$i]->sakit_dgn_sk;
                                  if($sakit_dgn_sk==''){
                                     $sakit_dgn_sk = 0;
                                  }
                                  
                                  
                                   $totalSakit2 =  $totalSakit2+$sakit_dgn_sk;
                              echo ' <td class="text-end">'.$sakit_dgn_sk.'</td>';
                            }
                          ?>
                          <td class="text-end bg-grey"> <strong>0</strong> </td>
                        </tr>
                        <tr>
                          <td>Cuti</td>
                          <?php
                           $totalCuti = 0;
                            for ($i=0; $i < 12 ; $i++) { 
                                
                              $cutiAbsen = @$dataRekap[$i]->cuti;
                                  if($cutiAbsen==''){
                                     $cutiAbsen = 0;
                                  }
                                  
                                  

                              $totalCuti = $totalCuti+$cutiAbsen;
                              echo ' <td class="text-end">'.$cutiAbsen .'</td>';
                            }
                          ?>
                            <td class="text-end bg-grey"> <strong><?php echo $totalCuti ;?> </strong> </td>
                        </tr>

                     </table>
                  

                </div>

               

                <!-- Column -->
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
    <script src="../assets/js/app.init.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>simplebar/dist/simplebar.min.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>sidebarmenu.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>theme.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>init.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>jquery.blockUI.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>block-ui.js"></script>

</body>


<script>
const xArray = ["Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agust", "Sept", "Okt","Nov", "Des"];
const yArray = [<?php echo '"'.implode('","', $rekapTerlmbt).'"' ?>];

// Define Data
const data = [{
  x: xArray,
  y: yArray,
  mode:"lines"
}];

// Define Layout
const layout = {
  xaxis: {xArray, title: "Bulan"},
  yaxis: {range: [0, 500], title: "Menit"},
  title: "Grafik Keterlambatan"
};

// Display using Plotly
Plotly.newPlot("myPlot", data, layout);
</script>
</html>
