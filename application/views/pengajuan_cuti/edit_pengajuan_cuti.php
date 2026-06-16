<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <?php $this->load->view('master/meta'); ?>
    <style>

    #list_pegawai{
        max-height:200px;
        overflow: auto;
    }
    .choose_pegawai{
        padding:5px;
        cursor: pointer;
    }
    .choose_pegawai:hover{
        color:darkorange;


    }
    </style>


</head>

<body>


    <div id="main-wrapper">
        <!-- Sidebar Start -->
        <aside class="left-sidebar with-vertical">
            <div><!-- ---------------------------------- -->
                <!-- Start Vertical Layout Sidebar -->
                <!-- ---------------------------------- -->

                <?php $this->load->view('layout/section/sidebar'); ?>

        </aside>

        <!--  Sidebar End -->
        <div class="page-wrapper">
            <!--  Header Start -->
            <?php $this->load->view('layout/section/header'); ?>
            <!--  Header End -->

            <?php

            //print_array($this->session->userdata);
                $id_validator    = $this->session->userdata('id_pegawai');
                $usergroup    = $this->session->userdata('usergroup');
                $my_nip       = $this->session->userdata('nip');

                #echo $usergroup;
                $id   =  $detail_cuti[0]->id;
                $tgl_dari   =  $detail_cuti[0]->tgl_dari;
                $tgl_sampai =  $detail_cuti[0]->tgl_sampai;
                $hari_cuti  =  $detail_cuti[0]->hari_cuti;
                $status  =  $detail_cuti[0]->status;
                $id_cuti  =  $detail_cuti[0]->id;
                $id_pegawai =  $detail_cuti[0]->id_pegawai;

                $tgl_pengajuan  =  $detail_cuti[0]->tgl;
                $id_pengganti  =  $detail_cuti[0]->id_pengganti;
                $delegasi_tugas  =  $detail_cuti[0]->delegasi_tugas;

                $tgl_check2  =  $detail_cuti[0]->tgl_check2;

                if($tgl_check2 != null){
                  $approve_date = format_full($tgl_check2);
                }else{
                  $approve_date = '';
                }

              

                $message = $this->session->flashdata('message');



               if($hari_cuti==1){
                   $tgl_cuti = '<span class="text-dark">'.format_full($tgl_dari).'</span>';
               }else{
                   $tgl_cuti = '<span class="text-dark">'.format_full($tgl_dari).' </span> &nbsp;&nbsp;&nbsp; s/d &nbsp;&nbsp;&nbsp;<span class="text-dark">'.format_full($tgl_sampai).'</span>';
               }


               $flagStatus = getStatusCuti($status);


               $detail_pegawai = $this->Pegawai_model->getDetailPegawai($id_pegawai);
               $id_pj       = $detail_pegawai[0]->id_validator;
               $nama        = $detail_pegawai[0]->nama;
               $jns_pegawai = $detail_pegawai[0]->jns_pegawai;
               $jabatan     = $detail_pegawai[0]->jabatan;
               $puskesmas   = $detail_pegawai[0]->puskesmas;
               $jns_pegawai = $detail_pegawai[0]->jns_pegawai;



               $detail_pegawai_pengganti = $this->Pegawai_model->getDetailPegawai($id_pengganti);
               $nama_pengganti           = $detail_pegawai_pengganti[0]->nama;
               $jabatan_pengganti        = $detail_pegawai_pengganti[0]->jabatan;
               $puskesmas_pengganti      = $detail_pegawai_pengganti[0]->puskesmas;
              #print_array($detail_pegawai);
               $delegasi = explode("+", $delegasi_tugas);

                 
               $arrayHakCuti = array('Sisa Cuti tahun lalu', 'Hak Cuti tahun ini', 'Hak Cuti Bersama');
            

               $arrayJnsCuti = array('Tahunan', 'Bersalin', 'Alasan Penting', 'Sakit', 'Besar');

            ?>

            <div class="body-wrapper">
                <div class="container-fluid">

                    <div class="row p-2">
                        <div class="col-md-12 mb-4">
                         
                        </div>

                          <div class="col-md-8">
                            <h5 class="fw-semibold">Edit Pengajuan Cuti</h5>

                          </div>

                         <div class="col-md-4">

                        
                          </div>
                     </div>

                    


                        <div class="row">
                    
                             <div class="col-md-8 mb-4">
                                <div class="border mt-4">
                                  <div class="card-body">

                                    <form action="<?php echo base_url();?>admin/pengajuan_cuti/update_data_cuti/<?php echo $id;?>" method="post">
                                        <div class="p-2">
                                        <a href="<?php echo base_url();?>admin/pengajuan_cuti/pengajuan_cuti_pegawai" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                                            <button type="submit" class="btn float-end btn-success">Simpan Perubahan</button>
                                        
                                        </div>
                                        <hr>
                                      
                                         <div class="row p-3">
                                                <div class="col-md-3 col-sm-6 col-6 mt-3">
                                                    <label for="">Tanggal Mulai: </label>
                                                    <input type="text" required name="date_from" autocomplete="off" class="form-control" value="<?php echo format_view($tgl_dari)  ;?>" id="dpd1" ></div>
                                                <div class="col-md-3 col-sm-6 col-6 mt-3">
                                                    <label for=""> Tanggal Akhir: </label> 
                                                    <input type="text" required  name="date_to"  autocomplete="off"   class="form-control" value="<?php echo format_view($tgl_sampai)  ;?>" id="dpd2" ></div>
                                                <div class="col-md-3 mt-3">
                                                Jenis Cuti: 
                                                    <select name="jns_cuti" id="jns_cuti"  class="form-control">
                                                    <?php
                                                        for ($i=0; $i < count($arrayJnsCuti) ; $i++) { 
                                                            $idjns= $i+1;
                                                            $jenis_cuti = $arrayJnsCuti[$i];

                                                            if($jns_cuti==$idjns){
                                                                echo '<option value="'.$idjns.'" selected>Cuti '.$jenis_cuti.'</option>';
                                                            }else{
                                                                echo '<option value="'.$idjns.'">Cuti '.$jenis_cuti.'</option>';
                                                            }
                                                            
                                                        }
                                                    ?>

                                                    

                                        </select>

                                    </div>

                                             <div class="col-md-6 mb-3 mt-3">
                                                <span class="text-dark">Nama Pengganti: </span> <br>
                                                <input type="text" id="search_pegawai" name="nama_pengganti" value="<?php echo  $nama_pengganti ;?>" placeholder="cari nama pegawai" class="form-control" required autocomplete="off">
                                                <div id="list_pegawai"></div>

                                                <br>

                                                <span class="text-dark">NIP : </span> <br>
                                                <strong><?php echo $detail_pegawai_pengganti[0]->nip;?></strong>
                                                

                                            </div>

                                            <div class="col-md-6 mb-3">
                                            
                                            </div>

                                            <div class="col-md-6">
                                              <span class="text-dark">Jabatan : </span> <br>
                                              <strong><?php echo $detail_pegawai_pengganti[0]->jabatan;?></strong>

                                              <Br> <Br>
                                              <span class="text-dark">Tempat Tugas : </span> <br>
                                              <strong><?php echo $detail_pegawai_pengganti[0]->puskesmas;?></strong>
                                            </div>


                                             
                                         

                                  </div>

                                  </form>
                              </div>
                              </div>

                          


                          </div>
                          <div class="col-md-4">
                             <h5>Pegawai Pengaju Cuti</h5> <br>
                             <table class="table">
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><?php echo $nama;?></td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>:</td>
                                    <td><?php echo $my_nip;?></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td><?php echo $jabatan;?></td>
                                </tr>
                                <tr>
                                    <td>Unit Kerja</td>
                                    <td>:</td>
                                    <td><?php echo $puskesmas;?></td>
                                </tr>
                             </table>
                          </div>

                        </div>
                      </div>


                    <script>
                        function handleColorTheme(e) {
                            $("html").attr("data-color-theme", e);
                            $(e).prop("checked", !0);
                        }
                    </script>

                    <?php $this->load->view('layout/section/theme-setting.php'); ?>

                    <?php $this->load->view('master/request-cuti.php'); ?>

                </div>
                <div class="dark-transparent sidebartoggler"></div>
                <!-- Import Js Files -->

                <script src="<?php echo LIBS_JS_PATH;?>jquery/dist/jquery.min.js"></script>
  <script src="<?php echo LIBS_JS_PATH;?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo NEW_JS_PATH;?>sidebarmenu.js"></script>
  <script src="<?php echo NEW_JS_PATH;?>app.min.js"></script>
  <script src="<?php echo LIBS_JS_PATH;?>simplebar/dist/simplebar.js"></script>
  <script src="<?php echo NEW_JS_PATH;?>dashboard.js"></script>


    <script src="<?php echo NEW_JS_PATH;?>prettify.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>jquery.js"></script>
    <script src="<?php echo NEW_JS_PATH;?>bootstrap-datepicker.js"></script>



</body>


<script>
 

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
        

        $("#jns_cuti").change(function(){

            var jns_cuti = $(this).val();
            if(jns_cuti==1){
                $("#jenis_hak_cuti").show();
            }else{
                $("#jenis_hak_cuti").hide();
            }


        });


        $("#search_pegawai").keydown(function() {
            var keyword = $(this).val();
            $("#list_pegawai").show();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url();?>cuti/search_pegawai",
                data: "keyword=" + keyword,
                success: function(return_data) {
                    $("#list_pegawai").html(return_data);
                }
            });
        });
</script>

</html>
