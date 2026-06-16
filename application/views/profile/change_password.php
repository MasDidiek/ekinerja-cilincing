<!DOCTYPE html>
<html lang="en" class="light scroll-smooth group" data-layout="vertical" data-sidebar="light" data-sidebar-size="lg" data-mode="light" data-topbar="light" data-skin="default" data-navbar="sticky" data-content="fluid" dir="ltr">
<head>
   <?php $this->load->view('layout/header');?>
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


   <style>


      .wrapper .content {
        margin: 20px 0 10px;
      }
      .content p {
        color: #333;
        font-size: 1.3rem;
      }
      .content .requirement-list {
        margin-top: 20px;
        color: #900;
      }
      .requirement-list li {
        font-size: 0.8rem;
        list-style: none;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
      
      }
      .requirement-list li i {
        width: 20px;
        color: #aaa;
        font-size: 0.6rem;
      }
      .requirement-list li.valid i {
        font-size: 1.2rem;
        color: green;
      }
      .requirement-list li span {
        margin-left: 12px;
        color: #333;
      }
      .requirement-list li.valid span {
        color:#229f48;
      }

      #form_change_password p{
        color: #f06b59;
      }
   </style>
</head>

<body class="text-base bg-body-bg text-body font-public dark:text-zink-100 dark:bg-zink-800 group-data-[skin=bordered]:bg-body-bordered group-data-[skin=bordered]:dark:bg-zink-700">
<div class="group-data-[sidebar-size=sm]:min-h-sm group-data-[sidebar-size=sm]:relative">

    <?php 
       $this->load->view('layout/sidebar');
       $this->load->view('layout/topheader');

       $message_status = $this->session->flashdata('message_status');

          
       $list_bulan = array_bulan();
        

        $periode_bulan = $this->session->userdata('periode_bulan');
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

        $nip = $pegawai[0]->nip;


     ?>
   
    <div class="relative min-h-screen group-data-[sidebar-size=sm]:min-h-sm">



        <div class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

              <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                  <div class="grow">
                      <h5 class="text-16">Pegawai</h5>
                  </div>
                  <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                      <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                          <a href="#!" class="text-slate-400 dark:text-zink-200">Pegawai</a>
                      </li>
                      <li class="text-slate-700 dark:text-zink-100">
                      Detail Pegawai
                      </li>
                  </ul>
              </div>

        <?php


            //print_array($rekap_capaian_kinerja);

          $id_pegawai = $pegawai[0]->id_pegawai;
          $tgl_masuk = $pegawai[0]->tgl_masuk;
          $nip = $pegawai[0]->nip;
          $nama_pegawai = $pegawai[0]->nama;
          $photo = $this->Pegawai_model->getPhotoPegawai($nip);

          if($photo==''){
            $photo = 'avatar.png';
          }

            $status_kawin  = $pegawai[0]->status_kawin;
            $status_pajak  = $pegawai[0]->status_pajak;
            $id_pendidikan = $pegawai[0]->id_pendidikan;
            
          
            $gaji_pokok = $pegawai[0]->gaji_pokok;
            $pengkalian = $pegawai[0]->pengkalian;
            $status_kerja= $pegawai[0]->status_kerja;
            
           

            $tkd_pokok  = $gaji_pokok*$pengkalian;


            $tmt = $pegawai[0]->tmt;
            $today = date('Y-m-d');

            $masa_kerja = hitungMasaKerja($tmt, $today);


        ?>
              <div class="mt-1 -ml-3 -mr-3 rounded-none card">
                    <div class="card-body !px-2.5">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-12 2xl:grid-cols-12">
                            <div class="lg:col-span-2 2xl:col-span-1">
                                <div class="relative inline-block rounded-full shadow-md size-20 bg-slate-100 profile-user xl:size-28">
                                    <img src="<?php echo base_url().'uploads/photo_profile/'.$photo;?>" alt="" class="object-cover border-0 rounded-full img-thumbnail user-profile-image">
                                    <div class="absolute bottom-0 flex items-center justify-center rounded-full size-8 ltr:right-0 rtl:left-0 profile-photo-edit">
                                        <input id="profile-img-file-input" type="file" class="hidden profile-img-file-input">
                                        <label for="profile-img-file-input" class="flex items-center justify-center bg-white rounded-full shadow-lg cursor-pointer size-8 dark:bg-zink-600 profile-photo-edit">
                                            <i data-lucide="image-plus" class="size-4 text-slate-500 dark:text-zink-200 fill-slate-100 dark:fill-zink-500"></i>
                                        </label>
                                    </div>
                                </div>
                            </div><!--end col-->
                            <div class="lg:col-span-4 2xl:col-span-5">
                                <h5 class="mb-1"><?php echo $nama_pegawai;?> <i data-lucide="badge-check" class="inline-block size-4 text-sky-500 fill-sky-100 dark:fill-custom-500/20"></i></h5>
                                <div class="flex gap-3 mb-4">
                                    <p class="text-slate-500 dark:text-zink-200"><i data-lucide="user-circle" class="inline-block size-4 ltr:mr-1 rtl:ml-1 text-slate-500 dark:text-zink-200 fill-slate-100 dark:fill-zink-500"></i> <?php echo $pegawai[0]->jabatan;?></p>
                                    <p class="text-slate-500 dark:text-zink-200"><i data-lucide="map-pin" class="inline-block size-4 ltr:mr-1 rtl:ml-1 text-slate-500 dark:text-zink-200 fill-slate-100 dark:fill-zink-500"></i> <?php echo $pegawai[0]->puskesmas;?></p>
                                </div>
                                <ul class="flex flex-wrap gap-3 mt-4 text-center divide-x divide-slate-200 dark:divide-zink-500 rtl:divide-x-reverse">
                                    <li class="px-5">
                                        <h5><?php echo format_semi($tgl_masuk);?></h5>
                                        <p class="text-slate-500 dark:text-zink-200">TMT</p>
                                    </li>
                                    <li class="px-5">
                                        <h5><?php echo $masa_kerja['years'].' Tahun '.$masa_kerja['months'].' bulan';?></h5>
                                        <p class="text-slate-500 dark:text-zink-200">Masa Kerja</p>
                                    </li>
                                   
                                </ul>
                               
                               <br><br>
                             

                            </div>
                            
                            
                            <div class="lg:col-span-6 2xl:col-span-6">
                              <div class="grid grid-cols-12 2xl:grid-cols-12 gap-x-2 ">
                                <div class="col-span-12  md:col-span-4 2xl:col-span-3">
                                    <div class="card-body">
                                       
                                        <p class="mb-3 text-slate-500 dark:text-zink-200">Gaji Pokok</p>
                                        <h5 class="mb-4">Rp. <span class="counter-value" data-target="<?php echo $gaji_pokok;?>">0</span> </h5>

                                    </div>
                                </div><!--end col-->
                                <div class="col-span-12  md:col-span-3 2xl:col-span-3">
                                    <div class="card-body">
                                      
                                        <p class="mb-3 text-slate-500 dark:text-zink-200">Pengali</p>
                                        <h5 class="mb-4"><span><?php echo $pengkalian;?></span> x </h5>

                                    </div>
                                </div><!--end col-->
                                <div class="col-span-12  md:col-span-4 2xl:col-span-3">
                                    <div class="card-body">
                                       
                                        <p class="mb-3 text-slate-500 dark:text-zink-200"> TKD Pokok</p>
                                        <h5 class="mb-4">Rp.  <span class="counter-value" data-target="<?php echo $tkd_pokok?>">0</span> </h5>

                                    </div>
                                </div><!--end col-->
                                
                            </div>
                            </div>
                        </div><!--end grid-->
                        <br>
                    </div>
                    <div class="card-body !px-2.5 !py-0">
                        <ul class="flex flex-wrap w-full text-sm font-medium text-center nav-tabs">
                            <li class="group active">
                                <a href="javascript:void(0);" data-tab-toggle="" data-target="overviewTabs" class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border-b border-transparent group-[.active]:text-custom-500 dark:group-[.active]:text-custom-500 group-[.active]:border-b-custom-500 dark:group-[.active]:border-b-custom-500 hover:text-custom-500 dark:hover:text-custom-500 active:text-custom-500 dark:active:text-custom-500 -mb-[1px]">Overview</a>
                            </li>
                        
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                <?php echo form_error('field name', '<div class="error">', '</div>'); ?>
                
                    <div class="tab-pane" id="personalTabs">
                         <div class="card">
                            <div class="card-body">
                                <h6 class="mb-1 text-15">Change Password</h6>
                                <p class="mb-4 text-slate-500 dark:text-zink-200">Update your photo and personal details here easily.</p>
                                <form action="<?php echo base_url();?>profile/change_password_process" method="post" id="form_change_password">
                                    <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
                                        <div class="xl:col-span-4">
                                            <label for="inputValue" class="inline-block mb-2 text-base font-medium">Old Password *</label>
                                            <div class="relative">
                                              
                                                <input type="password" name="old_password" id="old_password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"  placeholder="Enter current password">
                                                <button class="absolute top-2 ltr:right-4 rtl:left-4 " id="eye1" onclick="toggle()" type="button"><i class="align-middle ri-eye-fill text-slate-500 dark:text-zink-200"></i></button>
                                            </div>

                                            <?php echo form_error('old_password'); ?>
                                        </div><!--end col-->
                                        <div class="xl:col-span-4 pass-field">
                                            <label for="new_password" class="inline-block mb-2 text-base font-medium">New Password *</label>
                                            <div class="relative">
                                           
                                                <input type="password" autocomplete="off" value="<?php echo set_value('new_password'); ?>" name="new_password" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" id="new_password" placeholder="Enter new password">
                                                <button class="absolute top-2 ltr:right-4 rtl:left-4 " id="eye2" onclick="toggle2()" type="button">
                                                  <i class="align-middle ri-eye-fill text-slate-500 dark:text-zink-200"></i>
                                                </button>

                                            </div>
                                            <?php echo form_error('new_password'); ?>

                                            <div class="content mt-4">
                                                <strong>Password harus berisi</strong>
                                                <ul class="requirement-list">
                                                  <li>
                                                    <i data-lucide="dot" class="inline-block w-5 h-5"></i>
                                                    <span>Minimal panjang  8 karakter</span>
                                                  </li>
                                                  <li>
                                                  <i data-lucide="dot" class="inline-block w-5 h-5"></i>
                                                    <span>Minimal 1 karakter (0...9)</span>
                                                  </li>
                                                  <li>
                                                  <i data-lucide="dot" class="inline-block w-5 h-5"></i>
                                                    <span>Minimal 1 karakter huruf kecil (a...z)</span>
                                                  </li>
                                                  <li>
                                                  <i data-lucide="dot" class="inline-block w-5 h-5"></i>
                                                    <span>Minimal 1 karakter spesial karakter (!...$)</span>
                                                  </li>
                                                  <li>
                                                  <i data-lucide="dot" class="inline-block w-5 h-5"></i>
                                                    <span>Minimal 1 karakter huruf besar (A...Z)</span>
                                                  </li>
                                                </ul>

                                            </div>

                                            
                                              
                                        </div><!--end col-->
                                        <div class="xl:col-span-4">
                                            <label for="conf_password" class="inline-block mb-2 text-base font-medium">Confirm Password *</label>
                                            <div class="relative">
                                                <input type="password"  name="conf_password"  value="<?php echo set_value('conf_password'); ?>" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" id="conf_password" placeholder="Confirm password">
                                                <button class="absolute top-2 ltr:right-4 rtl:left-4 " id="eye3" onclick="toggle3()" type="button"><i class="align-middle ri-eye-fill text-slate-500 dark:text-zink-200"></i></button>
                                            </div>
                                            <?php echo form_error('conf_password'); ?>
                                            
                                        </div><!--end col-->
                                        <div class="flex items-center xl:col-span-6">
                                            <a href="javascript:void(0);" class="underline text-custom-500 text-13">Forgot Password ?</a>
                                        </div>

                                        
                                        <div class="flex justify-end xl:col-span-6">
                                            <button type="submit" class="text-white bg-green-500 border-green-500 btn hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/10">Change Password</button>
                                        </div>


                                        
                                    </div><!--end grid-->
                                </form>
                            </div>
                        </div>
                    </div><!--end tab pane-->
                </div><!--end tab content-->


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



<!-- apexcharts js -->
<script src="<?php echo base_url();?>assets/premium/libs/apexcharts/apexcharts.min.js"></script>
<script src="<?php echo base_url();?>assets/premium/libs/dropzone/dropzone-min.js"></script>
<script src="<?php echo base_url();?>assets/premium/js/pages/pages-account.init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>

<script>
    var state= false;
    var state2= false;
    var state3= false;
        function toggle(){
            if(state){
              document.getElementById("old_password").setAttribute("type","password");
              document.getElementById("eye1").style.color='#7a797e';
              state = false;
            } else{
              document.getElementById("old_password").setAttribute("type","text");
              document.getElementById("eye1").style.color='#5887ef';
              state = true;
            }
        }

        function toggle2(){
            if(state2){
              document.getElementById("new_password").setAttribute("type","password");
              document.getElementById("eye2").style.color='#7a797e';
              state2 = false;
            } else{
              document.getElementById("new_password").setAttribute("type","text");
              document.getElementById("eye2").style.color='#5887ef';
              state2 = true;
            }
        }

        function toggle3(){
            if(state3){
              document.getElementById("conf_password").setAttribute("type","password");
              document.getElementById("eye3").style.color='#7a797e';
              state3 = false;
            } else{
              document.getElementById("conf_password").setAttribute("type","text");
              document.getElementById("eye3").style.color='#5887ef';
              state3 = true;
            }
        }


        //validate password
        const passwordInput = document.querySelector(".pass-field input");
        const eyeIcon = document.querySelector(".pass-field i");
        const requirementList = document.querySelectorAll(".requirement-list li");
        // An array of password requirements with corresponding 
        // regular expressions and index of the requirement list item
        const requirements = [
            { regex: /.{8,}/, index: 0 }, // Minimum of 8 characters
            { regex: /[0-9]/, index: 1 }, // At least one number
            { regex: /[a-z]/, index: 2 }, // At least one lowercase letter
            { regex: /[^A-Za-z0-9]/, index: 3 }, // At least one special character
            { regex: /[A-Z]/, index: 4 }, // At least one uppercase letter
        ]
        passwordInput.addEventListener("keyup", (e) => {
            requirements.forEach(item => {
                // Check if the password matches the requirement regex
                const isValid = item.regex.test(e.target.value);
                const requirementItem = requirementList[item.index];
                // Updating class and icon of requirement item if requirement matched or not

                if (isValid) {
                    requirementItem.classList.add("valid");
                    requirementItem.firstElementChild.className = "check";
                } else {
                    requirementItem.classList.remove("valid");
                    requirementItem.firstElementChild.className = "dot";
                }
            });
        });
        // eyeIcon.addEventListener("click", () => {
        //     // Toggle the password input type between "password" and "text"
        //     passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        //     // Update the eye icon class based on the password input type
        //     eyeIcon.className = `fa-solid fa-eye${passwordInput.type === "password" ? "" : "-slash"}`;
        // });

        var status_update = '<?php echo $message_status;?>';
        if(status_update !=''){
          Toastify({
                    text: 'Success! Password berhasil diubah',
                    duration: 3000,
                    close: true,
                    className: "info",
                    gravity: "top", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    style: {
                        background: "#2f8f4c",
                        color: "#FFF"
                    },
                    onClick: function(){} // Callback after click
                }).showToast();
        }
          
        
        

</script>

</html>