<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/signature.js"></script>

    <style>
        .my-table{
            width: 100%;
        }
        .my-table  td{
            padding:2px;
           
        }

        .btn-list {
            padding:10px 15px;
            text-align:center;
            border-bottom:1px solid #EEE;
            color:#666;
            margin-right:2px;
        }

        .active-btn{
            border-bottom:1px solid #66bad9;
            color:orange;
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

                                

                                  
                                   $id_pegawai = $this->session->userdata('id_pegawai');
                                   
                                   $nama_user =  $this->session->userdata('nama');
                                   $nip_user =  $this->session->userdata('nip');
                                   $id_pegawai =  $this->session->userdata('id_pegawai');
                                   $photo = $this->Pegawai_model->getPhotoPegawai($nip_user);


                                   $pin 	= substr($nip_user, -4);

                                   $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);

                                   $jabatan =  $detail_pegawai->jabatan;
                                   $puskesmas =  $detail_pegawai->puskesmas;


                               ?>



                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body">
                                        <span class="float-start m-2 me-4">
                                            <img src="<?php echo base_url();?>uploads/photo_profile/<?php echo $photo ;?>" style="height: 100px; Width:100px" alt="" class="rounded-circle img-thumbnail">
                                        </span>
                                        <div class="">
                                            <h4 class="mt-1 mb-1"><?php echo  $nama_user ;?></h4>
                                              <p class="font-13"> <?php echo $nip_user;?></p>

                                            <strong class="font-13"> <?php echo $puskesmas;?></strong>
                                              <p class="font-13"> <?php echo $jabatan;?></p>
                                        </div>

                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" class="dropdown-item">Refresh Report</a>
                                                <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                                            </div>
                                        </div>
                                        <h4 class="header-title">Listing Gaji Ke 13</h4>
                                        <br><br>


                                        <div class="table-responsive mt-3">
                                            <table id="datatable-buttons" class="table w-100">
                                                <thead class="bg-light">
                                                      <tr>

                                                          <th>No</th>
                                                          <th>Tahun</th>
                                                         
                                                          <th>Gaji Pokok</th>
                                                          <th>Tunj. Suami</th>
                                                          <th>Tunj. Anak 1</th>
                                                          <th>Tunj. Anak 2</th>
                                                          <th class="text-end">TKD</th>
                                                        
                                                          <th class="text-end">Gaji</th>
                                                          <th class="text-end">Total</th>
                                                          <th class="text-end">Pajak(PPH21)</th>
                                                          <th class="text-end">THP</th>
                                                          <th class="text-center">TTD SPJ</th>
                                                          <th class="text-center">Action</th>

                                                      </tr>
                                                  </thead>
                                                  <tbody class="list form-check-all">
                                                  <?php

                                                         $totalGaji13 = 0;
                                                         $total_pajak = 0;
                                                    
                                                         $total_thp = 0;

                                                          $no = 1;
                                                          foreach ($datalist as $peg){

                                                          $nama = $peg->nama;
                                                       
                                                          $jabatan = $peg->jabatan;

                                                            

                                                          $ttd_spj = $peg->ttd_spj;

                                                          if($ttd_spj  == ''){
                                                             $flag_status = '<span class="badge badge-warning-lighten">Belum </span>';
                                                             $btn_check = '<a href="'.base_url().'admin/listing_tkd/cekSesuai/'.$peg->id.'" class="text-info float-end"> <i class="mdi mdi-account-edit me-1"></i></a>';

                                                          }else{
                                                              $flag_status = '<span class="badge badge-success-lighten">Sudah</span>';
                                                               $btn_check = '';
                                                          }

                                                          
                                                          $total = $peg->total;

                                                          $totalGaji13 = $totalGaji13+$total;
                                                          $periode = $peg->periode;
                                                          $explod = explode("-", $periode);

                                                          $periode_tahun = $explod[0];
                                                          $periode_bulan = $explod[1];

                                                          

                                                              echo' <tr>
                                                                      <td class="text-center ">'.$no.'</td>
                                                                      <td class="text-center fw-bold">   '.$periode_tahun.' </td>
                                                                     
                                                                      <td class="text-center  tkd_pokok">'.rupiah($peg->gaji_pokok).'</td>
                                                                      <td class="text-end">
                                                                       <strong>'.rupiah($peg->tunj_suami).'</strong>  </td>
                                                                      <td class="text-end">'.rupiah($peg->tunj_anak1).'</td>
                                                                      <td class="text-end">'.rupiah($peg->tunj_anak2).'</td>
                                                                      <td class="text-end">'.rupiah($peg->thr_gaji).'</td>
                                                                      <td class="text-end"> <strong>'.rupiah($peg->tkd13).'</strong></td>
                                                                      <td class="text-end"> <strong>'.rupiah($peg->total).'</strong></td>
                                                                      <td class="text-end text-danger"> <strong>'.rupiah($peg->pajak).'</strong></td>
                                                                      <td class="text-center text-primary"><strong>'.rupiah($peg->netto).'</strong></td>
                                                                      <td class="text-center">'.$flag_status.'</td>
                                                                      <td class="text-center ">
                                                                          <a href="'.base_url().'profile/detail_gaji13/'.$peg->id.'">
                                                                           <i class="uil-eye me-1"></i> View Detail</a>
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
         <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->

        <script>
            var message = '<?php echo $message;?>';

            if(message != ''){
                $.NotificationApp.send("Berhasil",message,"top-right","rgba(0,0,0,0.2)","success")
            }
          
        </script>



        <script>

                $(".ttd_spj").click(function(){
                    var data = $(this).val();
                    var expl  = data.split('/');
                    var id = expl[0];
                    var periode = expl[1];
                    
                    $("#periode").val(periode);
                    $("#id_spj").val(id);

                });



                
                var wrapper = document.getElementById("signature-pad");
                var clearButton = wrapper.querySelector("[data-action=clear]");
                var savePNGButton = wrapper.querySelector("[data-action=save-png]");
                var canvas = wrapper.querySelector("canvas");
                var el_note = document.getElementById("note");
                var signaturePad;
                signaturePad = new SignaturePad(canvas);
                clearButton.addEventListener("click", function (event) {
                document.getElementById("note").innerHTML="The signature should be inside box";
                signaturePad.clear();
                });
                savePNGButton.addEventListener("click", function (event){
                if (signaturePad.isEmpty()){
                    alert("Please provide signature first.");
                    event.preventDefault();
                }else{
                    var canvas  = document.getElementById("the_canvas");
                    var dataUrl = canvas.toDataURL();
                    document.getElementById("signature").value = dataUrl;
                }
                });
                function my_function(){
                document.getElementById("note").innerHTML="";
                }



                </script>

    </body>
</html>
