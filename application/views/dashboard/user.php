<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
<?php  $this->load->view('master/meta');?>
<style>
                .datepicker{
                z-index: 1999;
            }
            .button {
              width:100%;
              display: inline-block;
              height: 50px;
              line-height: 50px;
              padding-right: 30px;
              padding-left: 70px;
              position: relative;
              background-color:rgb(41,127,184);
              color:rgb(255,255,255);
              text-decoration: none;
              text-transform: uppercase;
              letter-spacing: 1px;
              margin-bottom: 15px;
              border-radius: 5px;
              -moz-border-radius: 5px;
              -webkit-border-radius: 5px;
              text-shadow:0px 1px 0px rgba(0,0,0,0.5);
            -ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ff123852,Positive=true)";zoom:1;
            filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ff123852,Positive=true);

              -moz-box-shadow:0px 2px 2px rgba(0,0,0,0.2);
              -webkit-box-shadow:0px 2px 2px rgba(0,0,0,0.2);
              box-shadow:0px 2px 2px rgba(0,0,0,0.2);
              -ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=2,Color=#33000000,Positive=true)";
            filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=2,Color=#33000000,Positive=true);
            }

        .button span {
            position: absolute;
            left: 0;
            width: 50px;
            background-color:rgba(0,0,0,0.5);

            -webkit-border-top-left-radius: 5px;
            -webkit-border-bottom-left-radius: 5px;
            -moz-border-radius-topleft: 5px;
            -moz-border-radius-bottomleft: 5px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
            border-right: 1px solid  rgba(0,0,0,0.15);
        }

              .button:hover span, .button.active span {
                background-color:rgb(0,102,26);
                border-right: 1px solid  rgba(0,0,0,0.3);
              }

              .button:active {
                margin-top: 2px;
                margin-bottom: 13px;

                -moz-box-shadow:0px 1px 0px rgba(255,255,255,0.5);
              -webkit-box-shadow:0px 1px 0px rgba(255,255,255,0.5);
              box-shadow:0px 1px 0px rgba(255,255,255,0.5);
              -ms-filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ccffffff,Positive=true)";
              filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=0,OffY=1,Color=#ccffffff,Positive=true);
              }

          .button.orange {
            background: #FF7F00;
          }

          .button.purple {
            background: #8e44ad;
          }

          .button.turquoise {
            background: #1abc9c;
          }


            .loading-image{
              background: rgba(255,255,255,0.8) ;
              width: 100%;
              height: 100%;
              position: absolute;
              z-index: 888;
              text-align: center;
              display: none;
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
        
        
              <?php 
                
                $nama_user =  $this->session->userdata('nama');
                $nip_user =  $this->session->userdata('nip');
                $id_pegawai =  $this->session->userdata('id_pegawai');
                $usergroup =  $this->session->userdata('usergroup');
                $photo = $this->Pegawai_model->getPhotoPegawai($nip_user);
                $message = $this->session->flashdata('message'); 
                
                $periode_bulan = $this->session->userdata('periode_bulan'); 
                $periode_tahun = $this->session->userdata('periode_tahun'); 
                if($periode_bulan=='') {
                  $bulan = date('m');
                  $tahun = date('Y');
    
                }else{
                  $bulan = $periode_bulan;
                  $tahun = $periode_tahun;
                }

               $jumlah_cuti =  $this->Cuti_model->getHariCutiPegawai($id_pegawai, $bulan, $tahun);
               #print_array($this->session->userdata);

                if($photo==''){
                  $photo = 'avatar.png';
                }

                
                if(!empty($rekapTKD)){
                  $tkd_pokok = $rekapTKD[0]->tkd_pokok;
                  $capaian = $rekapTKD[0]->capaian;
                  $bruto  = $rekapTKD[0]->bruto;
                  $pph21 = $rekapTKD[0]->pph21;
                  $bpjs = $rekapTKD[0]->bpjs;
                  $bpjs_tk = $rekapTKD[0]->bpjs_tk;
                  $thp = $rekapTKD[0]->thp;
                  $masa_kerja = $rekapTKD[0]->masa_kerja;
                     
                }else{
                  $tkd_pokok = 0;
                  $capaian =  0;
                  $bruto  = 0;
                  $pph21 =  0;
                  $bpjs =  0;
                  $bpjs_tk =  0;
                  $thp =  0;
                  $masa_kerja = '';
                }



                if(!empty($dataRekap)){
                  $telat = $dataRekap[0]->telat;
                  $pulang_awal = $dataRekap[0]->pulang_awal;
                  $izin = $dataRekap[0]->izin;
                  $sakit = $dataRekap[0]->sakit;
                }else{
                  $telat = 0;
                  $pulang_awal =0;
                  $izin = 0;
                  $sakit =0;
                }
             

                $nama_bulan = getBulan($bulan);
                #print_array($dataRekap);
                
            ?>

          <div class="row">

               <div class="col-md-12">
                  <div class="d-flex align-items-center gap-4 mb-4">
                      <div class="position-relative">
                        <div class="border border-2 border-primary rounded-circle">
                          <img src="<?php echo base_url();?>uploads/photo_profile/<?php echo $photo ;?>" class="rounded-circle m-1" alt="user1" width="60">
                        </div>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-primary"> 3
                          <span class="visually-hidden">unread messages</span>
                        </span>
                      </div>
                      <div>
                        <h3 class="fw-semibold">Hi, <span class="text-dark">   <?php echo $nama_user;?></span>
                        </h3>
                        <span>Cheers, and happy activities - <?php echo date('d');?> <?php echo date('F');?> 2024</span>
                      </div>
                    </div>

                    
           </div>
           
             <!--  Row 1 -->
             <div class="row">
              <div class="col-lg-8 d-flex align-items-strech">
                <div class="card w-100">
                  <div class="card-body">
                    <div
                      class="d-sm-flex d-block align-items-center justify-content-between mb-9"
                    >
                      <div class="mb-3 mb-sm-0">
                        <h5 class="card-title fw-semibold">Rekap Absensi</h5>
                        <p class="card-subtitle mb-0">Data rekap absensi bulan </p>

                      </div>
                      <select class="form-select w-auto" id="change_periode">
                        <?php
                          $listBulan = array_bulan();


                          for ($i=1; $i < count($listBulan) ; $i++) { 

                            if($i==$bulan){
                              echo '<option value="'.$i.'/'.$tahun.'" selected>'.$listBulan[$i].' '.$tahun.'</option>';
                            }else{
                              echo '<option value="'.$i.'/'.$tahun.'">'.$listBulan[$i].'  '.$tahun.'</option>';
                            }
                             
                          }
                        ?>
                     
                      </select>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-md-8">

                        <div class="loading-image">
                          <img src="<?php echo base_url();?>assets/images/loading_baru.gif" width="200">
                        </div>

                        <table class="table table-borderless">
                          <tr>
                            <th> Terlambat </th>
                            <th>:</th>
                            <th><?php echo $telat;?> menit</th>
                          </tr>
                          <tr>
                            <th> Pulang Cepat </th>
                            <th>:</th>
                            <th><?php echo $pulang_awal;?> menit</th>
                          </tr>
                          <tr>
                            <th> Sakit </th>
                            <th>:</th>
                            <th><?php echo $sakit;?> hari</th>
                          </tr>
                          <tr>
                            <th> Izin </th>
                            <th>:</th>
                            <th><?php echo $izin;?> hari</th>
                          </tr>
                          <tr>
                            <th> Cuti </th>
                            <th>:</th>
                            <th><?php echo $jumlah_cuti ;?> hari</th>
                          </tr>
                        </table>
                           

                      </div>
                      <div class="col-md-4">
                        <div class="hstack mb-4 pb-1">
                          <div
                            class="p-8 bg-primary-subtle rounded-1 me-3 d-flex align-items-center justify-content-center"
                          >
                            <i class="ti ti-grid-dots text-primary fs-6"></i>
                          </div>
                          <div>
                            <h4 class="mb-0 fs-7 fw-semibold">Rp. <?php echo rupiah($thp);?></h4>
                            <p class="fs-3 mb-0">Total THP TKD</p>
                          </div>
                        </div>
                        <div>
                          <div class="d-flex align-items-baseline mb-4">
                            <span
                              class="round-8 text-bg-primary rounded-circle me-6"
                            ></span>
                            <div>
                              <p class="fs-3 mb-1">PPh21 </p>
                              <h6 class="fs-5 fw-semibold mb-0">Rp. <?php echo rupiah($pph21);?></h6>
                            </div>
                          </div>
                          <div class="d-flex align-items-baseline mb-4 pb-1">
                            <span
                              class="round-8 text-bg-secondary rounded-circle me-6"
                            ></span>
                            <div>
                              <p class="fs-3 mb-1">Lainnya</p>
                              <h6 class="fs-5 fw-semibold mb-0">-</h6>
                            </div>
                          </div>
                          <div>
                            <a href="<?php echo base_url();?>kinerja/capaian" class="btn btn-primary w-100">
                              Lihat Detail
                          </a>


                          <?php
                            if($usergroup==0)
                            {

                              echo '<a href="'.base_url().'dashboard/tarikDataAbsensiMesin" class="btn btn-success w-100 mt-4">
                              <i class="fa-solid fa-download"></i> &nbsp;  Tarik Data Absensi
                              </a>';

                              
                              echo '<a href="'.base_url().'dashboard/list_pengajuan_izin" class="btn btn-warning w-100 mt-4">
                              <i class="fa-solid fa-file"></i> &nbsp;  Pengajuan Izin / Sakit
                              </a>';
                            }
                          ?>

                         
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="row">
                  <div class="col-lg-12 col-md-6">
                    <!-- Yearly Breakup -->
                    <div class="card">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-8">
                            <h5 class="card-title mb-9 fw-semibold">
                              Capaian Kinerja
                            </h5>
                            <div class="loading-image">
                          <img src="<?php echo base_url();?>assets/images/loading_baru.gif" width="200">
                        </div>
                            <h4 class="fw-semibold mb-3"> <?php echo  $capaian;?> %</h4>
                            
                            <div class="d-flex align-items-center mb-3">
                              <span
                                class="me-1 rounded-circle bg-success-subtle round-20 d-flex align-items-center justify-content-center"
                              >
                                <i class="ti ti-arrow-up-left text-success"></i>
                              </span>
                              <p class="text-dark me-1 fs-3 mb-0"></p>
                              <p class="fs-3 mb-0"><?php echo $nama_bulan;?></p>
                            </div>
                            <div class="d-flex align-items-center">
                              <div class="me-4">
                                <span
                                  class="round-8 text-bg-primary rounded-circle me-2 d-inline-block"
                                ></span>
                                <span class="fs-2"><?php echo $tahun;?></span>
                              </div>
                              <div>
                                <span
                                  class="round-8 bg-primary-subtle rounded-circle me-2 d-inline-block"
                                ></span>
                                <span class="fs-2">2024</span>
                              </div>
                            </div>
                          </div>
                          <div class="col-4">
                            <div class="d-flex justify-content-center">
                              <div id="breakup"></div>

                              
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12 col-md-6">
                    <!-- Monthly Earnings -->
                    <div class="card">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-12">
                            <h5 class="card-title mb-9 fw-semibold">
                             Shortcut
                            </h5>
                            <div class="col-12 text-center"> 
                                   
                                <a href="<?php echo base_url();?>kinerja/index" class="button"><span><i class="fa-solid fa-book "></i></span>Input Kinerja </a>
                                <a href="<?php echo base_url();?>absensi/dinas_luar" class="button turquoise"><span><i class="fa-solid fa-building-circle-exclamation"></i> </span> Dinas Luar</a>
                                <a href="#" class="button purple"  data-bs-toggle="modal" data-bs-target="#samedata-modal" data-bs-whatever="@mdo">
                                        <span><i class="fa-solid fa-calendar"></i></span>Pengajuan Cuti</a>
                                <a href="<?php echo base_url();?>absensi/izin_sakit" class="button orange"><span><i class="fa-solid fa-file-medical"></i></span>Sakit/Izin</a>
                              </div>
                           
                          </div>
                          <div class="col-4">
                           
                          </div>
                        </div>
                      </div>
                      <div id="earning"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
       
        
            
            
         
        </div>
      </div>


      
<?php
    $id_pegawai = $this->session->userdata('id_pegawai');
    $hakCutiThnLalu = $this->Cuti_model->getSisaCuti($id_pegawai, 1);
    $hakCutiThnIni  = $this->Cuti_model->getSisaCuti($id_pegawai, 2);
    $hakCutiBersama = $this->Cuti_model->getSisaCuti($id_pegawai, 3);
     $arrayHakCuti = array('Sisa Cuti tahun lalu', 'Hak Cuti tahun ini', 'Hak Cuti Bersama');
     $arraySisaCuti = array($hakCutiThnLalu, $hakCutiThnIni, $hakCutiBersama );
?>


         <div class="modal fade" id="samedata-modal" tabindex="-1" aria-labelledby="exampleModalLabel1">
             <div class="modal-dialog" role="document">
                 <form method="post" action="<?php echo base_url();?>cuti/check_date" enctype="multipart/form-data">
                      <div class="modal-content">
                          <div class="modal-header d-flex align-items-center">
                              <h4 class="modal-title" id="exampleModalLabel1">
                                  Pengajuan Cuti
                              </h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                        
                              <div class="row">
                                  <div class="col-md-6 col-sm-6 col-6 mt-3">
                                      <label for="">Tanggal Mulai: </label>
                                      <input type="text" required name="date_from" autocomplete="off" class="form-control" value="<?php echo date('d-m-Y');?>" id="dpd1" ></div>
                                  <div class="col-md-6 col-sm-6 col-6 mt-3">
                                      <label for=""> Tanggal Akhir: </label> 
                                      <input type="text" required  name="date_to"  autocomplete="off"   class="form-control"value="<?php echo date('d-m-Y');?>" id="dpd2" ></div>
                                  <div class="col-md-6 mt-3">
                                    Jenis Cuti: 
                                      <select name="jns_cuti" id="jns_cuti"  class="form-control">
                                          <option value="1">Cuti Tahunan</option>
                                          <option value="2">Cuti Bersalin</option>
                                          <option value="3">Cuti Alasan Penting</option>
                                          <option value="4">Cuti Sakit</option>
                                          <option value="5">Cuti Besar</option>

                                      </select>

                                  </div>
                                  <div class="col-md-6 mt-3">
                                  Hak  Cuti yang digunakan: 
                                      <select name="jns_hak_cuti" id="jns_cuti"  class="form-control">
                                      <?php
                                          for ($i=0; $i < count($arrayHakCuti) ; $i++) { 
                                              $idjnsHak = $i+1;
                                              $nama_hak_cuti = $arrayHakCuti[$i];

                                              if($jns_hak_cuti==$idjnsHak){
                                                  echo '<option value="'.$idjnsHak.'" selected>'.$nama_hak_cuti.' ('.$arraySisaCuti[$i].')</option>';
                                              }else{
                                                  echo '<option value="'.$idjnsHak.'">'.$nama_hak_cuti.' ('.$arraySisaCuti[$i].')</option>';
                                              }
                                              
                                          }
                                      ?>

                                        
                                      </select>
                                  </div>
                              </div>
                           
                       
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn bg-danger-subtle text-danger font-medium"
                                  data-bs-dismiss="modal">
                                  Close
                              </button>
                              <button type="submit" class="btn btn-primary">
                                Selanjutnya
                              </button>
                          </div>
                      </div>

                      </form>
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
    <script src="../assets/js/app.init.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>simplebar/dist/simplebar.min.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>sidebarmenu.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>theme.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>init.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>jquery.blockUI.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>block-ui.js"></script>

    <script src="<?php echo LIBS_JS_PATH;?>apexcharts/dist/apexcharts.min.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>prettify.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>jquery.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>bootstrap-datepicker.js"></script>
</body>


<script>
      $("#button_upload").click(function(){
            $(".form-upload").removeClass('d-none');


        });


      $(".approve").click(function() {
          var id_cuti = $(this).val();
          $("#id_cuti_approve").val(id_cuti);

      });

      
      $("#change_periode").change(function(){
              var periode = $(this).val();
              var explod = periode.split("/");
              var bulan  = explod[0];
              var tahun = explod[1];
              $(".loading-image").show();
             
              // alert(tahun);
              // return false;

              $.ajax({
                          
                          type:"POST",
                          dataType:"html",
                          url:"<?php echo base_url();?>dashboard/set_session_periode",
                          data:"bulan="+bulan+"&tahun="+tahun,
                          success:function(msg){
                            //return false;
                            window.location.reload();
                            //$("#modal-form").html(msg);
                            //console.log(msg);
                          }
                      
                    });

            });




        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        //1700931600000
        //1703264400000

        var checkin = $('#dpd1').datepicker({
            
            onRender: function(date) {

                //alert(date.valueOf());
                //return date.valueOf() < now.valueOf() ? 'disabled' : '';
                return '';
            }
        }).on('changeDate', function(ev) {
        if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.setValue(newDate);
            }

       
        checkin.hide();
        $('#dpd2')[0].focus();

        }).data('datepicker');
            var checkout = $('#dpd2').datepicker({
            onRender: function(date) {
           // return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
           return '';
        }
        }).on('changeDate', function(ev) {
          checkout.hide();
        }).data('datepicker');


</script>
</html>