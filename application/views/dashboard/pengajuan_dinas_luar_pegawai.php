<!DOCTYPE html>
<html lang="en" class="light scroll-smooth group" data-layout="vertical" data-sidebar="light" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
<head>
   <?php $this->load->view('layout/header');?>
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

   <style>
    .bg-success{
        background: #30cb8c;
    }
    .bg-success:hover{
        background: #1ca86f;
    }

    .file-upload-izin{
        position: relative;
        display: inline-block;
        overflow: hidden;
        border: 1px solid #DDD;
        border-radius: 5px;
        padding: 10px;
        width: 100%;
    }
    
    
    .file-upload {
      position: relative;
      display: inline-block;
      overflow: hidden;
      border: 1px solid #DDD;
      border-radius: 5px;
      padding: 10px;
    }

    .file-upload input[type='file'] {
      position: absolute;
      font-size: 100px;
      opacity: 0;
      right: 0;
      top: 0;
    }

    .file-upload-izin input[type='file'] {
      position: absolute;
      font-size: 100px;
      opacity: 0;
      right: 0;
      top: 0;
    }

    .file-upload-button{
        cursor: pointer;
    }

    .file-upload-name {
      margin-left: 10px;
      font-family: Arial, sans-serif;
    }


   </style>
</head>

<body class="text-base bg-body-bg text-body font-public dark:text-zink-100 dark:bg-zink-800 group-data-[skin=bordered]:bg-body-bordered group-data-[skin=bordered]:dark:bg-zink-700">
<div class="group-data-[sidebar-size=sm]:min-h-sm group-data-[sidebar-size=sm]:relative">

    <?php 
       $this->load->view('layout/sidebar');
       $this->load->view('layout/topheader');

       $status_pengajuan_dl = $this->session->flashdata('status');
       $message = $this->session->flashdata('message');
       $id_pegawai = $this->session->userdata('id_pegawai'); 


    

     ?>

    <div class="relative min-h-screen group-data-[sidebar-size=sm]:min-h-sm">



        <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Dinas Luar </h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Absensi</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                    Dinas Luar
                    </li>
                </ul>
            </div>

         
                    <div id="smallModal" modal-center="" class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
                        <div class="w-screen md:w-[25rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full">
                            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                                <h5 class="text-16" id="modal_heading">Dinas Luar</h5>
                                <button data-modal-close="smallModal" class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i data-lucide="x" class="size-5"></i></button>
                            </div>
                            <form action="<?php echo base_url();?>absensi/insertPengajuanDinasLuar" method="post" enctype="multipart/form-data">
                              <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
                                 <div class="mb-3">
                                    <label>Tanggal DL</label>
                                    <input type="text" name="tgl_dl" required class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" data-provider="flatpickr" data-date-format="d M Y" data-range-date="true" placeholder="Select Date">
                                </div>

                                <div class="mb-3">
                                        <div class="xl:col-span-12">

                                            <label for="keterangan" class="inline-block mb-2 text-base font-medium">Jenis Dinas Luar</label>
                                            <div class="flex items-center gap-2">
                                                <input id="radioInline1" required value="DLP" name="jns_dl" class="border rounded-full appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500" type="radio">
                                                <label for="radioInline1" class="align-middle">
                                                    DL-PENUH
                                                </label>
                                                    &nbsp;&nbsp;&nbsp;
                                                <input id="radioInline2" required value="DLA" name="jns_dl" class="border rounded-full appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500" type="radio">
                                                <label for="radioInline2" class="align-middle">
                                                DL-AWAL
                                                </label>
                                                &nbsp;&nbsp;&nbsp;
                                                <input id="radioInline3" required value="DLAK" name="jns_dl" class="border rounded-full appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500" type="radio">
                                                <label for="radioInline3" class="align-middle">
                                                    DL-AKHIR
                                                </label>

                                            </div>
                                    </div>
                                  
                                </div>
                                
                                        <div class="xl:col-span-12 mt-3">
                                
                                            <label for="keterangan" class="inline-block mb-2 text-base font-medium">Keterangan</label>
                                            <textarea name="keterangan" required id="keterangan" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"  rows="2" cols="10" wrap="soft"></textarea>
                                        </div>
                                        <div class="xl:col-span-12 mt-3">
                                                <div class="file-upload">
                                                    <button type="button" class="file-upload-button text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                    <i class="align-baseline ltr:pr-1 rtl:pl-1 ri-download-2-line"></i> 
                                                    Open Camera</button>
                                                    <input type="file" accept="image/*" capture="camera" id="cameraInput" name="cameraInput">
                                                    <span class="file-upload-name">No file chosen</span>
                                                </div>

                                                <img id="preview" src="#" alt="Image preview" style="display: none; max-width: 300px;">
                                                <br><br>
                                                <input type="hidden" id="latitude" name="latitude">
                                                <input type="hidden" id="longitude" name="longitude">
                                        </div>

                                  
                                       
                           

                                  <br>

                                  <button type="submit"  class="float-right ml-2 text-white btn bg-success border-success ">
                                     Kirim Pengajuan
                                </button>
                                  <button type="button" data-modal-close="smallModal"  class="float-right  text-red-500 bg-red-100 btn hover:text-white hover:bg-red-600 focus:text-white focus:bg-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:ring active:ring-red-100 dark:bg-red-500/20 dark:text-red-500 dark:hover:bg-red-500 dark:hover:text-white dark:focus:bg-red-500 dark:focus:text-white dark:active:bg-red-500 dark:active:text-white dark:ring-red-400/20"> <i class="align-baseline ltr:pl-1 rtl:pr-1 ri-close-line"></i> Cancel</button>
                                  </form>
                            </div>
                       </div>
                    </div><!--close modal form cuti-->


                    <div class="grid grid-cols-1 gap-x-5 2xl:grid-cols-12">
                            <div class="2xl:col-span-12">
                               
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-3 text-15">Pengajuan Dinas Luar</h6>
                                        
                                        <hr>
                                          <br>
                                        <div class="overflow-x-auto">

                                      <?php    
                                            if($status_pengajuan_dl=='gagal'){
                                                echo '    <div class="px-4 py-3 text-sm text-red-500 border border-transparent rounded-md bg-red-50 dark:bg-red-400/20">
                                                    <span class="font-bold">Gagal</span>!!  '.$message.'
                                                </div>';
                                            }else if($status_pengajuan_dl=='success'){
                                                echo '    <div class="px-4 py-3 text-sm text-green-500 border border-transparent rounded-md bg-green-50 dark:bg-green-400/20">
                                                <span class="font-bold">Berhasil </span>!!  '.$message.'
                                            </div>';
                                            }
                                            
                                        ?>


                                     

                                      <div id="largeModal" modal-center="" class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
                                          <div class="w-screen md:w-[40rem] bg-white shadow rounded-md dark:bg-zink-600 flex flex-col h-full" id="info-dl">
                                              <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                                                  <h5 class="text-16">Modal Heading</h5>
                                                  <button data-modal-close="largeModal" class="transition-all duration-200 ease-linear text-slate-500 hover:text-red-500 dark:text-zink-200 dark:hover:text-red-500"><i data-lucide="x" class="size-5"></i></button>
                                              </div>
                                              <div class="max-h-[calc(theme('height.screen')_-_180px)] p-4 overflow-y-auto">
                                                  <h5 class="mb-3 text-16">Modal Content</h5>
                                                  <p class="text-slate-500 dark:text-zink-200">They all have something to say beyond the words on the page. They can come across as casual or neutral, exotic or graphic.</p>
                                              </div>
                                              <div class="flex items-center justify-between p-4 mt-auto border-t border-slate-200 dark:border-zink-500">
                                                  <h5 class="text-16">Modal Footer</h5>
                                              </div>
                                          </div>
                                      </div>

                                         <table class="w-full whitespace-nowrap mt-4">
                                            <thead class="ltr:text-left rtl:text-right bg-slate-100 text-slate-500 dark:text-zink-200 dark:bg-zink-600">
                                                <tr>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">No</th>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">Status</th>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">Tgl Pengajuan</th>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">Nama Pegawai</th>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">Jenis DL</th>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">Keterangan</th>
                                                 
                                                </tr>
                                            </thead>

                                          
                                               <tbody>
                                         
                                               <?php 

                                                  $path        = 'uploads/surat_tugas/';
                                                  $no = 1;
                                                  foreach ($pengajuan_dinas_luar as $dl){

                                                      $id_pegawai = $dl->id_pegawai;
                                                      $id = $dl->id;
                                                      $jns_dl = $dl->jns_dl;
                                                      $tanggal = $dl->tanggal;
                                                      $keterangan = $dl->keterangan;
                                                      $status = $dl->status;
                                                      $surtug = $dl->surtug;

                                                      $nama = $this->Pegawai_model->getNamaPegawaiByID($id_pegawai);

                                                      if ($jns_dl=='DLP') {
                                                          $dl_name = '<span class="bg-white px-2.5 py-0.5  text-custom-500 btn border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:bg-zink-700 dark:hover:bg-custom-500 dark:ring-custom-400/20 dark:focus:bg-custom-500">DL - PENUH</span>';
                                                      }else if($jns_dl=='DLA'){
                                                          $dl_name = '<span class="px-2.5 py-0.5 text-xs text-orange-500 bg-white border-orange-500 btn hover:text-white hover:bg-orange-600 hover:border-orange-600 focus:text-white focus:bg-orange-600 focus:border-orange-600 focus:ring focus:ring-orange-100 active:text-white active:bg-orange-600 active:border-orange-600 active:ring active:ring-orange-100 dark:bg-zink-700 dark:hover:bg-orange-500 dark:ring-orange-400/20 dark:focus:bg-orange-500">DL - AWAL</span>';
                                                      }else{
                                                          $dl_name = '<span class="px-2.5 py-0.5 text-purple-500 bg-white border-purple-500 btn hover:text-white hover:bg-purple-600 hover:border-purple-600 focus:text-white focus:bg-purple-600 focus:border-purple-600 focus:ring focus:ring-purple-100 active:text-white active:bg-purple-600 active:border-purple-600 active:ring active:ring-purple-100 dark:bg-zink-700 dark:hover:bg-purple-500 dark:ring-purple-400/20 dark:focus:bg-purple-500">DL -  AKHIR</span>';
                                                      }

                                                      if($status==0){
                                                          $flag = '<span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded-full border bg-orange-100 border-transparent text-orange-500 dark:bg-orange-500/20 dark:border-transparent">Belum diperiksa</span>';
                                                      }else if($status==1){
                                                          $flag = '<span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded-full border bg-green-100 border-transparent text-green-500 dark:bg-green-500/20 dark:border-transparent">Disetujui</span>';
                                                      }else{
                                                          $flag = '<span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded-full border bg-custom-100 border-transparent text-custom-500 dark:bg-custom-500/20 dark:border-transparent">Tidak Disetujui</span>';
                                                      }

                                                      


                                                      echo' <tr>
                                                              <td class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">'.$no.' </td>
                                                              <td class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">'.$flag.' </td>
                                                            
                                                            
                                                              <td class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500"> '.format_semi($tanggal).'</td>
                                                              <td class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500"> '.$nama.'</td>
                                                              <td class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">
                                                               <button type="button" class="view-detail" data-modal-target="largeModal" value="'.$id.'">  '.$dl_name.'</button></td>
                                                              <td class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500">'.word_limiter($keterangan,5).' </td>
                                                        
                                                          
                                                              
                                                          </tr>';

                                                              $no += 1;



                                                  }

                                                  ?>
                                                  
                                                </tbody>
                                            </table>
                                        </div>
                                        

                                    </div>
                                </div>
                            </div><!--end col-->
                          
                        </div><!--end grid-->

              </div>
              <!-- container-fluid -->
          </div>
          <!-- End Page-content -->



      <?php $this->load->view('layout/footer');?>


    </div>

</div>

<div class="fixed items-center hidden bottom-6 right-12 h-header group-data-[navbar=hidden]:flex">
    <button data-drawer-target="customizerButton" type="button" class="inline-flex items-center justify-center w-12 h-12 p-0 transition-all duration-200 ease-linear rounded-md shadow-lg text-sky-50 bg-sky-500">
        <i data-lucide="settings" class="inline-block w-5 h-5"></i>
    </button>
</div>


<?php $this->load->view('layout/theme_config');?>
<?php $this->load->view('layout/mainjs');?>

<!-- listjs init -->
<script src="<?php echo base_url();?>assets/premium/js/pages/listcuti.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
Usage
</body>

<script>




$(".view-detail").click(function(){
    var id = $(this).val();

        $.ajax({

                type:"POST",
                dataType:"html",
                url:"<?php echo base_url();?>admin/presensi/ajax_detail_dl",
                data:"id="+id,
                success:function(msg){
                    
                    $("#info-dl").html(msg);
                    //console.log(msg);
                }

            });


});



</script>

</html>