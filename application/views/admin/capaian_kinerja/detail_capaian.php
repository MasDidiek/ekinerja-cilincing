
<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">


  <head>
  <?php $this->load->view('master/meta'); ?>
<style>

.c-dashboardInfo {
  margin-bottom: 15px;
}
.c-dashboardInfo .wrap {
  background: #ffffff;
  border: 1px solid #EEE;
  border-radius: 3px;
  text-align: center;
  position: relative;
  overflow: hidden;
  padding: 20px 25px 20px;
  height: 100%;
}
.c-dashboardInfo__title,
.c-dashboardInfo__subInfo {
  color: #6c6c6c;
  font-size: 1.18em;
}
.c-dashboardInfo span {
  display: block;
}
.c-dashboardInfo__count {
  font-weight: 600;
  font-size: 2.5em;
  line-height: 64px;
  color: #323c43;
}
.c-dashboardInfo .wrap:after {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 10px;
  content: "";
}

.c-dashboardInfo:nth-child(1) .wrap:after {
  background: linear-gradient(82.59deg, #00c48c 0%, #00a173 100%);
}
.c-dashboardInfo:nth-child(2) .wrap:after {
  background: linear-gradient(82.59deg, #00c48c 0%, #00a173 100%);
}
.c-dashboardInfo:nth-child(3) .wrap:after {
  background: linear-gradient(82.59deg, #00c48c 0%, #00a173 100%);
}
.c-dashboardInfo:nth-child(4) .wrap:after {
  background: linear-gradient(82.59deg, #00c48c 0%, #00a173 100%);
}
.c-dashboardInfo__title svg {
  color: #d7d7d7;
  margin-left: 5px;
}
.MuiSvgIcon-root-19 {
  fill: currentColor;
  width: 1em;
  height: 1em;
  display: inline-block;
  font-size: 24px;
  transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
  user-select: none;
  flex-shrink: 0;
}



  .progress-circle {
   font-size: 20px;
   margin: 20px;
   position: relative; /* so that children can be absolutely positioned */
   padding: 0;
   width: 5em;
   height: 5em;
   background-color: #EEE; 
   border-radius: 50%;
   line-height: 5em;
}

.progress-circle:after{
    border: none;
    position: absolute;
    top: 0.35em;
    left: 0.35em;
    text-align: center;
    display: block;
    border-radius: 50%;
    width: 4.3em;
    height: 4.3em;
    background-color: white;
    content: " ";
}
/* Text inside the control */
.progress-circle span {
    position: absolute;
    line-height: 5em;
    width: 5em;
    text-align: center;
    display: block;
    color: #53777A;
    z-index: 2;
}
.left-half-clipper { 
   /* a round circle */
   border-radius: 50%;
   width: 5em;
   height: 5em;
   position: absolute; /* needed for clipping */
   clip: rect(0, 5em, 5em, 2.5em); /* clips the whole left half*/ 
}
/* when p>50, don't clip left half*/
.progress-circle.over50 .left-half-clipper {
   clip: rect(auto,auto,auto,auto);
}
.value-bar {
   /*This is an overlayed square, that is made round with the border radius,
   then it is cut to display only the left half, then rotated clockwise
   to escape the outer clipping path.*/ 
   position: absolute; /*needed for clipping*/
   clip: rect(0, 2.5em, 5em, 0);
   width: 5em;
   height: 5em;
   border-radius: 50%;
   border: 0.45em solid #2edb9a; /*The border is 0.35 but making it larger removes visual artifacts */
   /*background-color: #4D642D;*/ /* for debug */
   box-sizing: border-box;
  
}
/* Progress bar filling the whole right half for values above 50% */
.progress-circle.over50 .first50-bar {
   /*Progress bar for the first 50%, filling the whole right half*/
   position: absolute; /*needed for clipping*/
   clip: rect(0, 5em, 5em, 2.5em);
   background-color: #2edb9a;
   border-radius: 50%;
   width: 5em;
   height: 5em;
}
.progress-circle:not(.over50) .first50-bar{ display: none; }


/* Progress bar rotation position */
.progress-circle.p0 .value-bar { display: none; }
.progress-circle.p1 .value-bar { transform: rotate(4deg); }
.progress-circle.p2 .value-bar { transform: rotate(7deg); }
.progress-circle.p3 .value-bar { transform: rotate(11deg); }
.progress-circle.p4 .value-bar { transform: rotate(14deg); }
.progress-circle.p5 .value-bar { transform: rotate(18deg); }
.progress-circle.p6 .value-bar { transform: rotate(22deg); }
.progress-circle.p7 .value-bar { transform: rotate(25deg); }
.progress-circle.p8 .value-bar { transform: rotate(29deg); }
.progress-circle.p9 .value-bar { transform: rotate(32deg); }
.progress-circle.p10 .value-bar { transform: rotate(36deg); }
.progress-circle.p11 .value-bar { transform: rotate(40deg); }
.progress-circle.p12 .value-bar { transform: rotate(43deg); }
.progress-circle.p13 .value-bar { transform: rotate(47deg); }
.progress-circle.p14 .value-bar { transform: rotate(50deg); }
.progress-circle.p15 .value-bar { transform: rotate(54deg); }
.progress-circle.p16 .value-bar { transform: rotate(58deg); }
.progress-circle.p17 .value-bar { transform: rotate(61deg); }
.progress-circle.p18 .value-bar { transform: rotate(65deg); }
.progress-circle.p19 .value-bar { transform: rotate(68deg); }
.progress-circle.p20 .value-bar { transform: rotate(72deg); }
.progress-circle.p21 .value-bar { transform: rotate(76deg); }
.progress-circle.p22 .value-bar { transform: rotate(79deg); }
.progress-circle.p23 .value-bar { transform: rotate(83deg); }
.progress-circle.p24 .value-bar { transform: rotate(86deg); }
.progress-circle.p25 .value-bar { transform: rotate(90deg); }
.progress-circle.p26 .value-bar { transform: rotate(94deg); }
.progress-circle.p27 .value-bar { transform: rotate(97deg); }
.progress-circle.p28 .value-bar { transform: rotate(101deg); }
.progress-circle.p29 .value-bar { transform: rotate(104deg); }
.progress-circle.p30 .value-bar { transform: rotate(108deg); }
.progress-circle.p31 .value-bar { transform: rotate(112deg); }
.progress-circle.p32 .value-bar { transform: rotate(115deg); }
.progress-circle.p33 .value-bar { transform: rotate(119deg); }
.progress-circle.p34 .value-bar { transform: rotate(122deg); }
.progress-circle.p35 .value-bar { transform: rotate(126deg); }
.progress-circle.p36 .value-bar { transform: rotate(130deg); }
.progress-circle.p37 .value-bar { transform: rotate(133deg); }
.progress-circle.p38 .value-bar { transform: rotate(137deg); }
.progress-circle.p39 .value-bar { transform: rotate(140deg); }
.progress-circle.p40 .value-bar { transform: rotate(144deg); }
.progress-circle.p41 .value-bar { transform: rotate(148deg); }
.progress-circle.p42 .value-bar { transform: rotate(151deg); }
.progress-circle.p43 .value-bar { transform: rotate(155deg); }
.progress-circle.p44 .value-bar { transform: rotate(158deg); }
.progress-circle.p45 .value-bar { transform: rotate(162deg); }
.progress-circle.p46 .value-bar { transform: rotate(166deg); }
.progress-circle.p47 .value-bar { transform: rotate(169deg); }
.progress-circle.p48 .value-bar { transform: rotate(173deg); }
.progress-circle.p49 .value-bar { transform: rotate(176deg); }
.progress-circle.p50 .value-bar { transform: rotate(180deg); }
.progress-circle.p51 .value-bar { transform: rotate(184deg); }
.progress-circle.p52 .value-bar { transform: rotate(187deg); }
.progress-circle.p53 .value-bar { transform: rotate(191deg); }
.progress-circle.p54 .value-bar { transform: rotate(194deg); }
.progress-circle.p55 .value-bar { transform: rotate(198deg); }
.progress-circle.p56 .value-bar { transform: rotate(202deg); }
.progress-circle.p57 .value-bar { transform: rotate(205deg); }
.progress-circle.p58 .value-bar { transform: rotate(209deg); }
.progress-circle.p59 .value-bar { transform: rotate(212deg); }
.progress-circle.p60 .value-bar { transform: rotate(216deg); }
.progress-circle.p61 .value-bar { transform: rotate(220deg); }
.progress-circle.p62 .value-bar { transform: rotate(223deg); }
.progress-circle.p63 .value-bar { transform: rotate(227deg); }
.progress-circle.p64 .value-bar { transform: rotate(230deg); }
.progress-circle.p65 .value-bar { transform: rotate(234deg); }
.progress-circle.p66 .value-bar { transform: rotate(238deg); }
.progress-circle.p67 .value-bar { transform: rotate(241deg); }
.progress-circle.p68 .value-bar { transform: rotate(245deg); }
.progress-circle.p69 .value-bar { transform: rotate(248deg); }
.progress-circle.p70 .value-bar { transform: rotate(252deg); }
.progress-circle.p71 .value-bar { transform: rotate(256deg); }
.progress-circle.p72 .value-bar { transform: rotate(259deg); }
.progress-circle.p73 .value-bar { transform: rotate(263deg); }
.progress-circle.p74 .value-bar { transform: rotate(266deg); }
.progress-circle.p75 .value-bar { transform: rotate(270deg); }
.progress-circle.p76 .value-bar { transform: rotate(274deg); }
.progress-circle.p77 .value-bar { transform: rotate(277deg); }
.progress-circle.p78 .value-bar { transform: rotate(281deg); }
.progress-circle.p79 .value-bar { transform: rotate(284deg); }
.progress-circle.p80 .value-bar { transform: rotate(288deg); }
.progress-circle.p81 .value-bar { transform: rotate(292deg); }
.progress-circle.p82 .value-bar { transform: rotate(295deg); }
.progress-circle.p83 .value-bar { transform: rotate(299deg); }
.progress-circle.p84 .value-bar { transform: rotate(302deg); }
.progress-circle.p85 .value-bar { transform: rotate(306deg); }
.progress-circle.p86 .value-bar { transform: rotate(310deg); }
.progress-circle.p87 .value-bar { transform: rotate(313deg); }
.progress-circle.p88 .value-bar { transform: rotate(317deg); }
.progress-circle.p89 .value-bar { transform: rotate(320deg); }
.progress-circle.p90 .value-bar { transform: rotate(324deg); }
.progress-circle.p91 .value-bar { transform: rotate(328deg); }
.progress-circle.p92 .value-bar { transform: rotate(331deg); }
.progress-circle.p93 .value-bar { transform: rotate(335deg); }
.progress-circle.p94 .value-bar { transform: rotate(338deg); }
.progress-circle.p95 .value-bar { transform: rotate(342deg); }
.progress-circle.p96 .value-bar { transform: rotate(346deg); }
.progress-circle.p97 .value-bar { transform: rotate(349deg); }
.progress-circle.p98 .value-bar { transform: rotate(353deg); }
.progress-circle.p99 .value-bar { transform: rotate(356deg); }
.progress-circle.p100 .value-bar { transform: rotate(360deg); }


</style>
           

  </head>

  <body>
    <div id="main-wrapper">
        <!-- Sidebar Start -->
        <aside class="left-sidebar with-vertical">
           <div><!-- ---------------------------------- -->
             <?php $this->load->view('layout/section/sidebar'); ?>

         </aside>

        <!--  Sidebar End -->
            <div class="page-wrapper">
            <!--  Header Start -->
            <?php $this->load->view('layout/section/header'); ?>

            <?php


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

              if($bulan > 1){
                $bulanSeblmnya = $bulan-1;

                $periodeSblmnya = $tahun.'-'.$bulanSeblmnya;

              }else{
                $bulanSeblmnya = 12;
                $tahunSblmnya  = $tahun-1;
                $periodeSblmnya = $tahunSblmnya.'-'.$bulanSeblmnya;
              }

              $periodeSblmnya = date('Y-m', strtotime($periodeSblmnya));

            
            


             

              $periode = $tahun.'-'.$bulan;
              $periode = date('Y-m', strtotime($periode));
              $id_pegawai = $detail_pegawai[0]->id_pegawai;
              $nip = $detail_pegawai[0]->nip;
              $pin = substr($nip, -4);
              $jns_jam_kerja= $detail_pegawai[0]->jns_jam_kerja;
              $nama_pegawai = $detail_pegawai[0]->nama;
              $puskesmas = $detail_pegawai[0]->puskesmas;
              #print_array($detail_pegawai);

              $gaji_pokok= $detail_pegawai[0]->gaji_pokok;
              $pengkalian = $detail_pegawai[0]->pengkalian;
              $pph21 = $detail_pegawai[0]->pph21;
              $bpjs_kes= $detail_pegawai[0]->bpjs_kes;
              $bpjs_tk = $detail_pegawai[0]->bpjs_tk;
              $status_kerja = $detail_pegawai[0]->status_kerja;

              $jumlahHariKerja = $this->Master_model->getMenitEfektifBulan($bulan, $tahun);
              $waktu_efektif  = $jumlahHariKerja*300;



              $tkdBulanLalu   = $this->Laporan_model->getRekapTKDPegawai($nip, $periodeSblmnya);
            
              $capaianBlnLalu = $tkdBulanLalu[0]->capaian;
          

              $tkd_pokok = ceil($gaji_pokok*$pengkalian);

              $pengurang = $pph21+$bpjs_kes+$bpjs_tk;


              $totalInputAktifitas   =  $this->Kinerja_model->getJumlahInputanAktifitas($id_pegawai, $periode);

              $poinPerilaku     =  $this->Kinerja_model->getPoinPerilaku($id_pegawai, $periode_bulan, $periode_tahun);
              $totalAktifitas   =  $this->Kinerja_model->getAktifitasApprove($id_pegawai, $periode);
              $rekap_absensi    =  $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
              $jmlh_cuti        =  $this->Presensi_model->getjumlahCuti($id_pegawai, $periode);


              $nama_bulan = getBulan($bulan);



              #print_array($rekap_absensi);
              if(empty($rekap_absensi)){
                $telat = 0;
                $pulang_awal = 0;
                $izin = 0;
                $sakit = 0;

                $totalPengurang = 6000;

              }else{
                $telat = $rekap_absensi[0]->telat;
                $pulang_awal = $rekap_absensi[0]->pulang_awal;
                $izin = $rekap_absensi[0]->izin;
                $sakit = $rekap_absensi[0]->sakit;
                $sakit_dgn_sk = $rekap_absensi[0]->sakit_dgn_sk;

                $menit_izin = $izin*300;
                $menit_sakit = $sakit*300;
                $menit_sakit_dgn_surat = $sakit_dgn_sk*150;


                $totalPengurang = $telat+$pulang_awal+$menit_izin+$menit_sakit+$menit_sakit_dgn_surat;
              }
              
              $serapan = SERAPAN;
              #print_array($detail_pegawai);




                if($jmlh_cuti==''){
                  $jmlh_cuti = 0;
                }

                $menitPenambah       = $jmlh_cuti*300;
                $nilaiTotalAktifitas = $totalAktifitas+$menitPenambah;


                $totalWaktuEfektif = $waktu_efektif-$totalPengurang; //total waktu efektif setelah dikurangi menit pengurangik


                #echo $totalWaktuEfektif;
                $nilaiLebihKecil  =  $totalWaktuEfektif;



                if ($totalWaktuEfektif > $nilaiTotalAktifitas) {
                  $nilaiLebihKecil  =  $nilaiTotalAktifitas;
                }


                $bobotAktifitas = ($nilaiLebihKecil/$waktu_efektif)*100;

                $bobotTotal     = round($bobotAktifitas*0.7, 2);
                $totalCapaian =  number_format($bobotTotal+$poinPerilaku+$serapan,2);
                // echo $totalCapaian;


              // $bruto = round(($tkd_pokok*$totalCapaian)/100);


              #print_array($rekapTKD);

              if(!empty($rekapTKD)){
                $tkd_pokok = $rekapTKD[0]->tkd_pokok;
                $capaian = $rekapTKD[0]->capaian;
                $bruto  = $rekapTKD[0]->bruto;
                $pph21 = $rekapTKD[0]->pph21;
                $bpjs = $rekapTKD[0]->bpjs;
                $bpjs_tk = $rekapTKD[0]->bpjs_tk;
                $thp = $rekapTKD[0]->thp;

              }else{
                $tkd_pokok = 0;
                $capaian =  0;
                $bruto  = 0;
                $pph21 =  0;
                $bpjs =  0;
                $bpjs_tk =  0;
                $thp =  0;

              }


              $selisihCapaian = $capaian-$capaianBlnLalu;
              

              ?>
          <div class="body-wrapper">
            <div class="container-fluid">
        <!--  Row 1 -->
              <div class="row">
                <div class="col-lg-8 d-flex align-items-strech">
                  <div class="card w-100">
                    <div class="card-body p-0">
                      <div class="d-sm-flex d-block align-items-center  p-3 justify-content-between mb-9">
                        <div class="mb-3 mb-sm-0">
                          <h5 class="card-title fw-semibold">Capian Kinerja</h5>

                          <h6 class="text-dark">   <?php echo $nama_pegawai.'<br>'.$nip;?></h6>
                         
                        </div>

                        <div class="alert">
                            Periode  : <?php echo getBulan($bulan);?> 2024
                        </div>
                      </div>
                      
                      <hr>
                    
                      <div class="row">
                       
                        <div class="col-md-12  p-4">
                       
                          <?php
                            if(!empty($dataRekap)){
                              $telat = $dataRekap[0]->telat;
                              $pulang_awal = $dataRekap[0]->pulang_awal;
                              $izin = $dataRekap[0]->izin;
                              $sakit = $dataRekap[0]->sakit;
                              $sakit_dgn_sk = $dataRekap[0]->sakit_dgn_sk;
                            }else{
                              $telat = '-';
                              $pulang_awal = '-';
                              $izin = '-';
                              $sakit = '-';
                              $sakit_dgn_sk = '-';
                            }
                      ?>

                          <div id="root">
                            <div class="container">
                              <div class="row align-items-stretch">
                                <div class="c-dashboardInfo col-lg-3 col-md-6">
                                  <div class="wrap">
                                    <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Bobot Aktifitas<svg
                                        class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path
                                          d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                        </path>
                                      </svg></h4>
                                      <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $bobotTotal;?>%</span>
                                  </div>
                                </div>
                                <div class="c-dashboardInfo col-lg-3 col-md-6">
                                  <div class="wrap">
                                    <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Perilaku<svg
                                        class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path
                                          d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                        </path>
                                      </svg></h4>
                                      <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $poinPerilaku;?>%</span>
                                  </div>
                                </div>
                                <div class="c-dashboardInfo col-lg-3 col-md-6">
                                  <div class="wrap">
                                    <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Serapan<svg
                                        class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path
                                          d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                        </path>
                                      </svg></h4>
                                      <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $serapan;?>%</span>
                                  </div>
                                </div>
                                <div class="c-dashboardInfo col-lg-3 col-md-6">
                                  <div class="wrap">
                                    <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Total Capaian<svg
                                        class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path
                                          d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                        </path>
                                      </svg></h4>
                                      <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $capaian;?>%</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <table class="table table-sm table-borderless">
                              <tr>
                                <td width="200">Telat</td>
                                <td width="20">:</td>
                                <td> <?php echo $telat;?> menit </td>
                              </tr>
                              <tr>
                                <td>Pulang Awal</td>
                                <td>:</td>
                                <th> <?php echo $pulang_awal;?> menit</th>
                              </tr>
                              <tr height="100">
                                <td>Izin</td>
                                <td>:</td>
                                <th>   <?php echo $izin;?> hari</th>
                              </tr>
                              <tr>
                                <td>Sakit</td>
                                <td>:</td>
                                <th>  <?php echo $sakit;?> hari</th>
                              </tr>
                              <tr>
                                <td>Sakit dengan surat</td>
                                <td>:</td>
                                <th> <?php echo $sakit_dgn_sk;?> hari </th>
                              </tr>
                            </table>

                              <br>
                            <table class="table table-sm table-borderless">
                            <tr>
                                <th>Jenis Cuti</th>
                                <th>Jumlah</th>

                            </tr>
                            <?php


                                for ($i=0; $i < count($master_cuti); $i++) {
                                    $jns_cuti = $master_cuti[$i]->id;

                                    $rekap_cuti = $this->Cuti_model->getRekapCutiByJnsCuti($id_pegawai, $periode, $jns_cuti);
                                    $jumlah_cuti = $rekap_cuti[0]->jumlah;
                                    if($jumlah_cuti==''){
                                      $jumlah_cuti = 0;
                                    }
                                    #print_array($rekap_cuti);
                                  echo ' <tr>
                                                <td width="220">'.$master_cuti[$i]->jenis_cuti.'</td>
                                                <td>'.$jumlah_cuti.' &nbsp; hari</td>
                                            </tr>';
                                }
                            ?>


                  </table>

                  <a href="<?php echo base_url();?>admin/presensi/lihat_absensi_pegawai/<?php echo $id_pegawai.'/'.$pin;?>" class="btn btn-primary btn-sm" target="_blank">
                 Lihat  Detail Absensi <i class="ti ti-arrow-up-right"></i></a>
                  </div>

                  <div class="col-md-6 p-4">
                          <h5>Perhitungan Kinerja</h5><br>
                      
                          <table class="table table-sm table-borderless">
                            <tr>
                              <td width="200">Input aktifitas</td>
                              <td width="20">:</td>
                              <td>  <?php echo rupiah($totalInputAktifitas);?> menit </td>
                            </tr>
                            <tr>
                              <td>Disetujui</td>
                              <td>:</td>
                              <th> <?php echo rupiah($totalAktifitas);?> menit</th>
                            </tr>
                            <tr>
                              <td>Menit Penambah (cuti)</td>
                              <td>:</td>
                              <th> <?php echo rupiah($menitPenambah);?> menit</th>
                            </tr>

                            <tr height="50">
                              <td style="font-weight: bold; font-size:16px">Total Menit Aktifitas</td>
                              <td>:</td>
                              <th style="font-weight: bold; font-size:16px"> <?php echo rupiah($nilaiTotalAktifitas);?> menit</th>
                            </tr>
                      
                            <tr  height="50">
                              <td>Waktu Efektif Bulan</td>
                              <td>:</td>
                              <th> <?php echo rupiah($waktu_efektif);?> menit</th>
                            </tr>
                            <tr>
                              <td>Nilai Aktifitas</td>
                              <td>:</td>
                              <th> <?php echo round($bobotAktifitas, 2);?></th>
                            </tr>
                            <tr style="font-size:20px">
                              <td>Bobot Aktifitas</td>
                              <td>:</td>
                              <th> <?php echo round($bobotTotal, 2);?>%</th>
                            </tr>
                            
                            <table class="table table-sm table-borderless">
                          
                            <tr>
                              <td  width="200">Perilaku</td>
                              <td width="20"> :</td>
                              <th> <?php echo $poinPerilaku ;?>% </th>
                            </tr>
                            <tr>
                              <td>Serapan</td>
                              <td>:</td>
                              <th> <?php echo $serapan;?>% </th>
                            </tr>
                            <tr style="font-size:20px">
                              <td>Total</td>
                              <td>:</td>
                              <th> <?php echo  $capaian;?>% </th>
                            </tr>
                          </table>


                          <br>

                          <a href="<?php echo base_url();?>admin/capaian_kinerja/updateCapaianKinerjaPegawai/<?php echo $id_pegawai.'/'.$nip.'/'.$totalCapaian.'/'.$periode;?>" class="btn btn-info">
                            Update
                          </a>
                          
                          </table>
                        </div>
                </div>

                  

                  



              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="row">
              <div class="col-lg-12">
                <!-- Yearly Breakup -->
                <div class="card overflow-hidden">
                  <div class="card-body p-4">
                    <h5 class="card-title mb-9 fw-semibold">Capaian Kinerja</h5>
                    <div class="row align-items-center">
                      <div class="col-8">
                       
                        <div class="d-flex align-items-center mb-3">
                                                      
                            <div class="progress-circle over50 p<?php echo round($capaian,0);?>">
                              <span><?php echo $capaian;?>%</span>
                              <div class="left-half-clipper">
                                  <div class="first50-bar"></div>
                                  <div class="value-bar"></div>
                              </div>
                            </div>



                          <span
                            class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                            <i class="ti ti-arrow-up-left text-success"></i>
                          </span>
                          <p class="text-dark me-1 fs-3 mb-0"><?php echo round($selisihCapaian,2);?>%</p>
                          <p class="fs-3 mb-0">last month</p>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="me-4">
                            <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                            <span class="fs-2"><?php echo getBulan($bulan);?></span>
                          </div>
                          <div>
                            <span class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                            <span class="fs-2"><?php echo getBulan($bulanSeblmnya);?> </span> &nbsp; : &nbsp; <strong> <?php echo $capaianBlnLalu ;?> %</strong> 
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
              <div class="col-lg-12">
                <!-- Monthly Earnings -->
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <h5 class="card-title mb-9 fw-semibold"> Penerimaan TKD </h5>
                        <h4 class="fw-semibold mb-3">Rp. <?php echo rupiah($thp);?></h4>
                        <div class="align-items-center pb-1">
                          <span
                            class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                            <i class="ti ti-arrow-down-right text-danger"></i>
                          </span>
                          <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                          <p class="fs-3 mb-0">last year</p>
                        </div>

                        <br>
                        <table class="table table-sm table-borderless">
                           <tr>
                              <th colspan="3" class=" text-primary fs-4"><strong>Pokok</strong></th>
                            </tr>
                          <tr>
                            <td width="250">Gaji Pokok</td>
                            <td width="20">:</td>
                            <th>Rp. <?php echo rupiah($gaji_pokok);?> </th>
                           
                          </tr>
                          <tr>
                            <td>Pengkalian</td>
                            <td>:</td>
                            <th><?php echo $pengkalian;?> x</th>
                           
                          </tr>

                          <tr>
                            <td>TKD Pokok</td>
                            <td>:</td>
                            <th>Rp. <?php echo rupiah($tkd_pokok);?></th>
                           
                          </tr>

                         
                           <tr>
                             <th colspan="3" class=" text-warning fs-4"><strong>Pengurang</strong></th>
                           </tr>


                           <tr>
                            <td>Pajak (PPh21)</td>
                            <td>:</td>
                            <th>Rp. <?php echo rupiah($pph21);?></th>
                           
                          </tr>
                          <tr>
                            <td>BPJS Kesehatan</td>
                            <td>:</td>
                            <th>Rp. <?php echo rupiah($bpjs_kes);?></th>
                           
                          </tr>
                          <tr>
                            <td>BPJS Ketenagakerjaan</td>
                            <td>:</td>
                            <th>Rp. <?php echo rupiah($bpjs_tk);?></th>
                           
                          </tr>
                          <tr>
                            <td>Total Pengurang</td>
                            <td>:</td>
                            <th>Rp. <?php echo rupiah($pengurang);?></th>
                           
                          </tr>




                            <tr>
                                <td colspan="3"><strong class=" fs-4 text-success">Penerimaan</strong><br>
                                <label for="" class="text-muted">TKD Bruto - Pengurang</label>
                              </td>
                            </tr>
                            <tr>


                                <td>
                                <label for="" class="text-muted">Total THP</label>
                                  <h5 class="fw-semibold mb-3 text-success">Rp. <?php echo rupiah($thp);?></h5>
                                </td>
                            </tr>


                      </table>

                      </div>
                      
                      <div class="col-4">
                        <div class="d-flex justify-content-end">
                          <div
                            class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                            <i class="ti ti-currency-dollar fs-6"></i>
                          </div>
                        </div>
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

            </div>



        </div>

        </div>
<!-- /.container -->


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

                <script src="<?php echo LIBS_JS_PATH; ?>jquery/dist/jquery.min.js"></script>
                <script src="<?php echo NEW_JS_PATH; ?>app.min.js"></script>

                <script src="<?php echo LIBS_JS_PATH; ?>bootstrap/dist/js/bootstrap.bundle.min.js"></script>
                <script src="<?php echo LIBS_JS_PATH; ?>simplebar/dist/simplebar.min.js"></script>

                <script src="<?php echo NEW_JS_PATH; ?>sidebarmenu.js"></script>
                <script src="<?php echo NEW_JS_PATH; ?>theme.js"></script>
                <script src="<?php echo NEW_JS_PATH; ?>init.js"></script>

                <script src="<?php echo NEW_JS_PATH; ?>jquery.blockUI.js"></script>
                <script src="<?php echo NEW_JS_PATH; ?>block-ui.js"></script>
                <script src="<?php echo NEW_JS_PATH; ?>toastr-init.js"></script>


                <script src="<?php echo NEW_JS_PATH; ?>prettify.js"></script>
                <script src="<?php echo NEW_JS_PATH; ?>jquery.js"></script>





</body>


</html>

