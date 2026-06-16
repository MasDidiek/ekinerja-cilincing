<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
<?php  $this->load->view('master/meta');?>
<style>
           #snackbar {
  visibility: hidden;
  min-width: 250px;
  margin-left: -125px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 1;
  left: 50%;
  bottom: 30px;
  font-size: 17px;
}

#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;} 
  to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
  from {bottom: 30px; opacity: 1;} 
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
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
                      <h4 class="fw-semibold mb-8">Detail  Pegawai</h4>
                      <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="../main/index.html" >Home</a>
                          </li>
                         
                           <li> &nbsp; / &nbsp; </li>
                          
                          <li class="breadcrumb-item"> <a class="text-muted text-decoration-none" href="../main/index.html" >Data Pegawai PJLP</a></li>
                          <li> &nbsp; / &nbsp; </li>
                          <li class="breadcrumb-acive">Detail Pegawai</li>
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
                    
                    $message = $this->session->flashdata('message'); 
                    echo $message
                ?>

                   
              <div class="row">
                
                  <div class="col-lg-6">
                      <div class="card w-100">
                             <div class="card-body p-4">

                             <form method="post" id="update_pegawai" action="<?php echo base_url();?>admin/pegawai/update_pegawai/<?php echo $pegawai[0]->id;?>">
                               <table class="table table-data">
                                    <tr>
                                        <td>ID PJLP</td>
                                        <td>:</td>
                                        <td><?php echo $pegawai[0]->id_pjlp;?></td>
                                    </tr>
                                    <tr>
                                        <td>ID Mesin</td>
                                        <td>:</td>
                                        <td><?php echo $pegawai[0]->id_mesin;?></td>
                                    </tr>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td><?php echo $pegawai[0]->nama;?></td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td>Petugas <?php echo $pegawai[0]->jabatan;?></td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi Kerja</td>
                                        <td>:</td>
                                        <td><?php echo $pegawai[0]->lokasi_kerja;?></td>
                                    </tr>

                                    <tr>
                                        <td colspan="3">
                                           <button type="button" class="btn btn-danger">Hapus</button>
                                           <button type="button" class="btn btn-info float-end">Ubah</button>
                                        </td>
                                    </tr>
                                 </table>

                                 <table class="table table-edit d-none" id="table_edit">
                                    <tr>
                                        <td colspan="3"><h3>Edit Data Pegawai PJLP</h3></td>
                                    </tr>
                                    <tr>
                                        <td>ID PJLP</td>
                                        <td>:</td>
                                        <td>   <input type="text" name="id_pjlp" class="form-control" value="<?php echo $pegawai[0]->id_pjlp;?>" ></td>
                                    </tr>
                                    <tr>
                                        <td>ID Mesin</td>
                                        <td>:</td>
                                        <td><input type="text" name="id_mesin" class="form-control" value="<?php echo $pegawai[0]->id_mesin;?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama</td>
                                        <td>:</td>
                                        <td> <input type="text" name="nama" class="form-control" value="<?php echo $pegawai[0]->nama;?>" ></td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td>
                                            <select class="form-select" name="jabatan" aria-label="Default select example">
                                                <option value="keamanan"> Petugas <?php echo $pegawai[0]->jabatan;?>  (Security) </option>
                                                <option value="keamanan">Petugas Keamanan (Security)</option>
                                                <option value="kebersihan">Petugas Kebersihan (Cleaning Service) </option>
                                            </select>
                                          </td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi Kerja</td>
                                        <td>:</td>
                                        <td>
                                            
                                            <select class="form-select" name="lokasi_kerja" aria-label="Default select example">
                                                <?php
                                                    $lokasi_kerja = $pegawai[0]->lokasi_kerja;
                                                    foreach ($list_puskesmas as $puskesmas){
                                                                                
                                                    
                                                    $nama_puskesmas = $puskesmas->nama;

                                                    if($lokasi_kerja==$nama_puskesmas){
                                                        echo ' <option value="'. $nama_puskesmas .'" selected>'.$nama_puskesmas .'</option>';
                                                    }else{
                                                        echo ' <option value="'. $nama_puskesmas .'">'.$nama_puskesmas .'</option>';
                                                    }
                                                    

                                                    }

                                                ?>
                                        
                                            </select>
                                         </td>
                                    </tr>

                                    <tr>
                                        <td colspan="3">
                                           <a href="<?php echo base_url();?>admin/pegawai/detail_pegawai_pjlp/<?php echo $pegawai[0]->id;?>" class="btn btn-light text-danger">Kembali</a>
                                           <button type="submit" class="btn btn-success float-end">Simpan</button>
                                        </td>
                                    </tr>
                                 </table>
                            
                                </form>
                              </div>
                        </div>
                  </div>
            </div>
           

                <button onclick="myFunction()" style="display:none">Show Snackbar</button>

                <div id="snackbar">Data pegawai berhasil diupdate</div>

                <div class="modal fade" id="samedata-modal" tabindex="-1" aria-labelledby="exampleModalLabel1">
                      <div class="modal-dialog " role="document">
                          <div class="modal-content">
                            <div class="modal-header d-flex align-items-center">
                              <h4 class="modal-title" id="exampleModalLabel1">
                                Pengajuan Cuti
                              </h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <form action="https://ekinerja-puskesmascilincing.jakarta.go.id/admin/pengajuan_cuti/approve_pengajuan_cuti/4513" method="post">
                                    <div class="mb-3">
                                         <h6>Apakah anda yakin untuk menyetujui  pengajuan cuti ini ?</h6>
                                    </div>
                                      <center>
                                        <button type="submit" class="btn btn-success" name="status" value="PEND2">
                                          Iya, Setujui
                                        </button>
                                      </center>

                               </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn bg-danger-subtle text-danger font-medium" data-bs-dismiss="modal">
                                Close
                              </button>
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

    <script src="<?php echo NEW_JS_PATH;?>jquery.blockUI.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>block-ui.js"></script>


    <script src="<?php echo NEW_JS_PATH;?>prettify.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>jquery.js"></script>


</body>




<script>

        $('.btn-info').click(function(){
            $(".table-data").addClass('d-none');
            $(".table-edit").removeClass('d-none');

        });

        $('#update_pegawai').submit(function() {
				
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(data) {
						//window.location.reload();
                        open_notification();
                    }
                })
                return false;
            });



            

        function open_notification() {
            var x = document.getElementById("snackbar");
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
            }

  </script>
</html>