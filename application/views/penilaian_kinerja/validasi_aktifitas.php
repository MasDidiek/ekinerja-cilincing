<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('layout/section/header');?>
                                                        <!-- Datatables css -->
<link href="<?php echo base_url();?>assets/new/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/new/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />

    <style>

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

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="<?php echo base_url();?>dashboard/my_dashboard">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="<?php echo base_url();?>admin/penilaian_kinerja/validasi_aktifitas">Penilaian Kinerja</a></li>
                                            <li class="breadcrumb-item active">Penilaian Aktifitas</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Penilaian Aktifitas</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->



                            <div class="col-xxl-12col-lg-12">
                                <div class="card widget-flat">
                                    <div class="card-body text-left">
                                        <h4>Data Validasi Aktifitas</h4>
                                        <br>

                                        <?php
                                                    $info = $this->session->flashdata('message');


                                                    $list_bulan = array_bulan();

                                                    //print_array($this->session->userdata);

                                                    $periode_bulan = $this->session->userdata('periode_bulan');
                                                    //$periode_bulan = '04';
                                                    $periode_tahun = $this->session->userdata('periode_tahun');
                                                    $id_pkm_sess   = $this->session->userdata('id_pkm');
                                                    $id_pj_sess = $this->session->userdata('id_pj');
                                                    $id_user_validator   = $this->session->userdata('id_pegawai');

                                                    if($periode_bulan=='') {
                                                    $bulan = date('m');
                                                    $tahun = date('Y');

                                                    }else{
                                                        $bulan = $periode_bulan;
                                                        $tahun = $periode_tahun;
                                                    }

                                                    $periode = $tahun.'-'.$bulan;
                                                    $periode = date('Y-m', strtotime($periode));

                                                    $nm_bulan = getBulan($bulan);

                                                    //print_array($detail_validator);
                                                    $id_puskesmas = $detail_validator->id_validator;
                                                    $usergroup = $detail_validator->usergroup;

                                                    //print_array($detail_validator);


                                        ?>

                                        <div class="row">
                                            <div class="col-md-2">
                                                    <select  name="bulan" id="bulan" class="form-control">
                                                    <option value="">Bulan</option>
                                                    <?php
                                                        for ($b=0; $b < count($list_bulan) ; $b++) {

                                                            $no_bulan = $b+1;

                                                            if($bulan == $b){
                                                                $selc = 'selected';
                                                            }else{
                                                                $selc = '';
                                                            }


                                                            echo '<option value="'.$b.'" '.$selc.'>'.$list_bulan[$b].'</option>';
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <select  name="tahun" id="tahun" class="form-control">
                                                    <option value="">Tahun</option>
                                                    <?php
                                                        for ($b=2023; $b < 2030 ; $b++) {



                                                            if($periode_tahun == $b){
                                                                $selc2 = 'selected';
                                                            }else{
                                                                $selc2 = '';
                                                            }

                                                            echo '<option value="'.$b.'" '.$selc2.'> '.$b.'</option>';
                                                        }
                                                        ?>
                                                </select>

                                            </div>
                                            <?php if($usergroup <= 2 ){?>
                                                    <div class="col-md-3">
                                                        <select name="id_validator" id="validator" class="form-control">

                                                            <?php
                                                                foreach ($validator as $pj) {

                                                                $id_pj = $pj->id_pegawai;
                                                                $nama_pj   = $pj->nama;

                                                                if($id_pj_sess==$id_pj){
                                                                    echo '<option value="'. $id_pj.'" selected>'.$nama_pj.'</option>';
                                                                }else{
                                                                    echo '<option value="'. $id_pj.'">'.$nama_pj.'</option>';
                                                                }
                                                                }
                                                            ?>

                                                            </select>

                                                    </div>
                                            <?php } ?>
                                        </div>
                                        <br>

                                        <table id="basic-datatable" class="table dt-responsive nowrap w-100 mt-3">
                                                <thead class="bg-light">
                                                    <tr class="fs-5">

                                                        <th class="text-center"> No</th>
                                                        <th class="text-center">Nama Pegawai</th>
                                                        <th class="text-center">Jabatan</th>
                                                        <th class="text-center">Jumlah Input</th>
                                                        <th class="text-center">Divalidasi</th>
                                                        <th class="text-center">Belum Divalidasi</th>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php


                                                    $no = 1;

                                                    //print_array($data_pegawai);
                                                    foreach ($data_pegawai as $pegawai){

                                                            $nama  = $pegawai->nama;
                                                            $nip   = $pegawai->nip;
                                                            $id_pegawai   = $pegawai->id_pegawai;
                                                            $jabatan   = $pegawai->jabatan;



                                                            $jmlhInput       = $this->Kinerja_model->getJumlahInputPerBulan($id_pegawai, $periode);
                                                            if($jmlhInput ==''){
                                                                $jmlhInput = 0;
                                                            }
                                                            $belumDivalidasi = $this->Kinerja_model->getAktifitasByStatus($id_pegawai, $periode, 0);
                                                            if($belumDivalidasi==''){
                                                                $belumDivalidasi = 0;
                                                            }

                                                            $sudahDivalidasi =  $jmlhInput-$belumDivalidasi;


                                                        echo' <tr class="fs-5">
                                                                <td class="text-center">'.$no.'</td>
                                                                <td class="text-left">
                                                                    <a href="'.base_url().'admin/penilaian_kinerja/list_aktifitas/'.$id_pegawai.'" class="text-dark"> '.$nama.'</a>
                                                                 </td>

                                                                <td class=" text-left">'.$jabatan.'</td>
                                                                <td class="text-end ">  <a href="'.base_url().'admin/penilaian_kinerja/list_aktifitas/'.$id_pegawai.'" class="badge badge-info-lighten fs-5">'.rupiah($jmlhInput).'</a> </td>
                                                                <td class="text-end">   <a href="'.base_url().'admin/penilaian_kinerja/list_aktifitas/'.$id_pegawai.'" class="badge bg-success fs-5">'.rupiah($sudahDivalidasi).'</a> </td>
                                                                <td class="text-end"> <a href="'.base_url().'admin/penilaian_kinerja/list_aktifitas/'.$id_pegawai.'" class="badge badge-warning-lighten fs-5">'.rupiah($belumDivalidasi).'</a></td>

                                                                </tr>';

                                                            $no += 1;

                                                    }


                                                    ?>




                                                 </tbody>
                                        </table>



                                    </div>
                                </div>
                            </div> <!-- end col-->
                        </div>
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

        <!-- Todo js -->
        <script src="<?php echo base_url();?>assets/new/js/ui/component.todo.js"></script>


        <script src="<?php echo base_url();?>assets/new/js/pages/demo.toastr.js"></script>
        <!-- demo end -->

                <!-- Datatables js -->
        <script src="<?php echo base_url();?>assets/new/js/vendor/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.bootstrap5.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url();?>assets/new/js/vendor/responsive.bootstrap5.min.js"></script>

        <!-- Datatable Init js -->
        <script src="<?php echo base_url();?>assets/new/js/pages/demo.datatable-init.js"></script>





        <script>


            $("#bulan").change(function(){
                var bulan = $(this).val();
                var tahun = $("#tahun").val();

                $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>admin/penilaian_kinerja/set_session_periode",
                            data:"bulan="+bulan+"&tahun="+tahun,
                            success:function(msg){
                            window.location.reload();
                            //$("#modal-form").html(msg);
                            //console.log(msg);
                            }

                    });

            });


            $("#tahun").change(function(){
                var tahun = $(this).val();
                var bulan = $("#bulan").val();
                $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>admin/penilaian_kinerja/set_session_periode",
                            data:"bulan="+bulan+"&tahun="+tahun,
                            success:function(msg){
                            window.location.reload();
                            //$("#modal-form").html(msg);
                            //console.log(msg);
                            }

                    });

            });

            $("#validator").change(function(){
                var id_pj = $(this).val();

                $.ajax({

                            type:"POST",
                            dataType:"html",
                            url:"<?php echo base_url();?>admin/presensi/set_session_validator",
                            data:"id_pj="+id_pj,
                            success:function(msg){
                            window.location.reload();
                            //$("#modal-form").html(msg);
                            //console.log(msg);
                            }

                    });

            });




            </script>


    </body>
</html>
