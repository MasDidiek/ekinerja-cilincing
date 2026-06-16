<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
<?php  $this->load->view('master/meta');?>
<style>
             .datepicker{
                z-index: 1999;
            }

            .input-number{
              background:#FFF;
              border-top:2px solid #EEE;
              padding-top:20px;
            
            }


            .mybtn{
              border:none;
              padding:6px 6px;
              display:inline-block;
              color:#FFF;
              border-radius:4px;
            }
            .btn-save{
              background:#3de78c;
            }

            .btn-save:hover{
              background:#36d17e;
            }
            .btn-cancel{
              background:#fa896b;
            }
            .btn-cancel:hover{
              background:#e96d4c;
            }

            .fake-input{
              border:none;
              width:30px;
              text-align:center;
              font-size:22px;
            }

            small{
              color:#666;
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
                      <h4 class="fw-semibold mb-8">Data Pegawai</h4>
                      <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="../main/index.html" >Home</a>
                          </li>
                         
                           <li> &nbsp; / &nbsp; </li>
                          
                          <li class="breadcrumb-acive">Data Pegawai</li>
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
                    
                    $id_pegawai = $pegawai[0]->id_pegawai;
                    $tgl_masuk = $pegawai[0]->tgl_masuk;
                    $nip = $pegawai[0]->nip;
                    $nama_pegawai = $pegawai[0]->nama;
                    $photo = $this->Pegawai_model->getPhotoPegawai($nip);
      
                    if($photo==''){
                      $photo = 'avatar.png';
                    }
      
      
                    $arrayStatusPajak = array('TK', 'K0', 'K1', 'K2');
                    $array_group = arrayUsergroup(); 
      
                
                    $message = $this->session->flashdata('message'); 
      
                    echo $message;
                
               ?>

                   
              <div class="row">
                <div class="col-lg-12 d-flex align-items-stretch">
                  <div class="card w-100">
                    <div class="card-body p-4">
                        <ul class="nav nav-pills user-profile-tab" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button
                                  class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-4"
                                  id="pills-account-tab" data-bs-toggle="pill" data-bs-target="#pills-account" type="button" role="tab"
                                  aria-controls="pills-account" aria-selected="true">
                                  <i class="ti ti-user-circle me-2 fs-6"></i>
                                  <span class="d-none d-md-block">Account</span>
                              </button>
                            </li>

                         

                            <li class="nav-item" role="presentation">
                              <button
                                class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-4"
                                id="pills-bills-tab" data-bs-toggle="pill" data-bs-target="#pills-str-sip" type="button" role="tab"
                                aria-controls="pills-bills" aria-selected="false">
                                <i class="ti ti-files me-2 fs-6"></i>
                                <span class="d-none d-md-block">SIP/STR</span>
                              </button>
                            </li>


                            <li class="nav-item" role="presentation">
                              <button
                                class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-4"
                                id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-cuti" type="button"
                                role="tab" aria-controls="pills-security" aria-selected="false">
                                <i class="ti ti-bookmark me-2 fs-6"></i>
                                <span class="d-none d-md-block">Data Cuti</span>
                              </button>
                            </li>
                          
                            <li class="nav-item" role="presentation">
                              <button
                                class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-4"
                                id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-pelatihan" type="button"
                                role="tab" aria-controls="pills-security" aria-selected="false">
                                <i class="ti ti-badge me-2 fs-6"></i>
                                <span class="d-none d-md-block">Pelatihan</span>
                              </button>
                            </li>
                        </ul>




                          <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab" tabindex="0">

                                <?php $this->load->view('admin/pegawai/tab_account');?>
                            </div>

                            <div class="tab-pane fade" id="pills-str-sip" role="tabpanel" aria-labelledby="pills-bills-tab"
                                tabindex="0">
                                <div class="row">
                                      <h3>SIP / STR</h3>
                                </div><!--row-->
                            </div><!--tab-pane fade-->

                            
                            <div class="tab-pane fade" id="pills-cuti" role="tabpanel" aria-labelledby="pills-bills-tab"
                                tabindex="0">
                                <?php $this->load->view('admin/pegawai/tab_cuti');?>
                            </div><!--tab-pane fade-->

                            
                            <div class="tab-pane fade" id="pills-pelatihan" role="tabpanel" aria-labelledby="pills-bills-tab"
                                tabindex="0">
                                <div class="row">
                                      <h3>Pelatihan</h3>
                                </div><!--row-->
                            </div><!--tab-pane fade-->


                          </div><!--tab-content"-->

                            
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
    <script src="../assets/js/app.init.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo LIBS_JS_PATH;?>simplebar/dist/simplebar.min.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>sidebarmenu.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>theme.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>init.js"></script>

    <script src="<?php echo NEW_JS_PATH;?>jquery.blockUI.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>block-ui.js"></script>


    <script src="<?php echo NEW_JS_PATH;?>prettify.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>jquery.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>bootstrap-datepicker.js"></script>

    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>

</body>


<?php
$sisaTahunLalu = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 1, 'DESC');
$sisaTahunIni = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 2, 'DESC');
$sisaCuber = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 3, 'DESC');

?>


<script>
 
 $('#data-table').dataTable( {
                lengthMenu: [
                    [  20, -1 ],
                    ['20', '50', '100', 'Show all' ]
                ]
            } );


            $("#btn-edit1").click(function(){
                $(".input-cuti1").removeClass("d-none");
            });

           
		

            $("#btn-edit2").click(function(){
                $(".input-cuti2").removeClass("d-none");
            });

            
            $("#btn-edit3").click(function(){
                $(".input-cuti3").removeClass("d-none");
            });



            $(".btn-cancel").click(function(){

              $(".input-number").addClass("d-none");
          });
           
          $("#input_form1").change(function(){
            var jumlah = $(this).val();
            var jmlhSebelumnya = <?php echo $sisaTahunLalu;?>;
            var total = parseInt(jumlah)+parseInt(jmlhSebelumnya);
            $("#jumlah_cuti1").val(total);
            $("#sisa_akhir1").val(total);
          
          });

             
          $("#input_form2").change(function(){
            var jumlah = $(this).val();
            var jmlhSebelumnya = <?php echo $sisaTahunIni;?>;
            var total = parseInt(jumlah)+parseInt(jmlhSebelumnya);
            $("#jumlah_cuti2").val(total);
            $("#sisa_akhir2").val(total);
          
          });

             
          $("#input_form3").change(function(){
            var jumlah = $(this).val();
            var jmlhSebelumnya = <?php echo $sisaCuber;?>;
            var total = parseInt(jumlah)+parseInt(jmlhSebelumnya);
            $("#jumlah_cuti3").val(total);
            $("#sisa_akhir3").val(total);
          
          });

          
		

</script>
</html>