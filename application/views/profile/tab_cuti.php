<?php


$tgl_masuk = $pegawai[0]->tgl_masuk;
$nip = $pegawai[0]->nip;
$nama_pegawai = $pegawai[0]->nama;
$id_pegawai = $pegawai[0]->id_pegawai;


$sisaTahunLalu = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 1, 'DESC');
$sisaTahunIni = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 2, 'DESC');
$sisaCuber = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 3, 'DESC');

$sisaCutiAll = $sisaTahunLalu+$sisaTahunIni+$sisaCuber;

$function = $this->uri->segment(2);
if($function == 'my_profile'){
    $tab_active = '';
}else{
    $tab_active = 'show active';
}

?>
     <div class="tab-pane fade <?php echo  $tab_active ;?>" id="pills-cuti" role="tabpanel" aria-labelledby="pills-security-tab"  tabindex="0">
         <div class="row">
                <!-- Column -->
                <div class="col-md-12">
                     <button type="button" class="btn btn-info  btn-circle float-end btn-xl"   data-bs-toggle="modal" data-bs-target="#formcuti-modal" data-bs-whatever="@mdo">
                        <i class="fa-solid fa-pencil"></i>&nbsp; Buat Pengajuan Cuti
                    </button>

                </div>
                <div class="col-lg-3 col-md-6">

                  <div class="card">
                    <div class="card-body">

                      <div class="d-flex flex-row align-items-center">

                        <div class="round-40 text-white d-flex align-items-center justify-content-center text-bg-warning">
                          <i class="ti ti-credit-card fs-6"></i>
                        </div>
                        <div class="ms-3 align-self-center">

                            <h3 class="mb-0 fs-6">
                             <strong><?php echo $sisaTahunLalu;?></strong>
                              <small>hari</small>
                            </h3>
                          <span class="text-muted">Sisa Cuti Tahun lalu</span>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- Column -->
                <!-- Column -->
                <div class="col-lg-3 col-md-6">
                  <div class="card">
                    <div class="card-body">


                      <div class="d-flex flex-row align-items-center">
                        <div class="round-40  text-white d-flex align-items-center justify-content-center text-bg-info">
                          <i class="ti ti-users fs-6"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                          <h3 class="mb-0 fs-6">

                              <strong><?php echo $sisaTahunIni;?></strong>
                              <small>hari</small>
                            </h3>
                          <span class="text-muted">Sisa Cuti Tahun 2024</span>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- Column -->
                <!-- Column -->
                <div class="col-lg-3 col-md-6">
                  <div class="card">
                    <div class="card-body">

                      <div class="d-flex flex-row align-items-center">
                        <div class="round-40  text-white d-flex align-items-center justify-content-center text-bg-danger">
                          <i class="ti ti-calendar fs-6"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                             <h3 class="mb-0 fs-6">

                              <strong><?php echo $sisaCuber;?></strong>
                              <small>hari</small>
                            </h3>
                          <span class="text-muted">Hak Cuti Bersama</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Column -->
                <!-- Column -->
                <div class="col-lg-3 col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex flex-row align-items-center">
                        <div class="round-40  text-white d-flex align-items-center justify-content-center text-bg-success">
                          <i class="ti ti-settings fs-6"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                          <h3 class="mb-0 fs-6"><?php echo $sisaCutiAll;?>  &nbsp;  <small>hari</small></h3>
                          <span class="text-muted">Total hak Cuti</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Column -->
                </div>






            <br><br>
                <h5>Riwayat Pengajuan Cuti</h5>

                  <div class=" py-3">

                      <?php
      #print_array($history);

                          for ($i=0; $i < count($history) ; $i++) {
                            $tgl_dari = $history[$i]->tgl_dari;
                            $tgl_sampai = $history[$i]->tgl_sampai;
                            $hari_cuti = $history[$i]->hari_cuti;
                            $id = $history[$i]->id;
                            $status = $history[$i]->status;
                            $alasan_cuti = $history[$i]->alasan_cuti;

                            $tgl = $history[$i]->tgl;

                            $flagStatus = getStatusCuti($status);

                            if ($hari_cuti==1) {
                              $tgl_cuti = format_view($tgl_dari);
                            }else{
                              $tgl_cuti = format_view($tgl_dari).' s/d '. format_view($tgl_sampai);
                            }

                            echo ' <div class="row border text-dark p-2">
                                      <div class="col-md-3 mb-3">
                                       Tanggal Pengajuan : <br><strong>'.format_view($tgl).'</strong>
                                      </div>
                                      <div class="col-md-3  mb-3">
                                        Tanggal Mulai Cuti :<br> <strong>'.format_view($tgl_dari).'</strong>
                                      </div>

                                      <div class="col-md-3  mb-3">
                                         Tanggal Akhir Cuti :<br> <strong>'.format_view($tgl_sampai).'</strong>
                                      </div>

                                      <div class="col-md-3  mb-3">
                                        Lama Cuti :<br> <strong>'.$hari_cuti.' hari</strong>
                                      </div>

                                      <div class="col-md-6  mb-3">
                                      <p class="mb-0">Alasan Cuti : </p>
                                        <strong>'. $alasan_cuti .'</strong>
                                    </div>


                                      <div class="col-md-6  mb-3">
                                        '. $flagStatus;

                                        if($status=='PEND0' || $status=='PEND1' || $status=='PEND2'){
                                          echo ' <button class="btn bg-danger-subtle text-danger ms-2 float-end cancel_cuti" value="'.$id.'" data-bs-toggle="modal" data-bs-target="#al-info-alert">Batalkan</button>
                                          <a href="'.base_url().'cuti/edit_cuti/'.$id.'" class="btn bg-primary-subtle text-primary ms-2 float-end">Ubah</a>';
                                        }

                                        echo '  <a href="'.base_url().'cuti/detail_cuti/'.$id.'" class="btn btn-primary float-end">Lihat Detail</a>';

                                     echo '</div>

                                  </div>


                              </div>';
                          }
                      ?>




                <?php
                    $id_pegawai = $this->session->userdata('id_pegawai');
                    $hakCutiThnLalu = $this->Cuti_model->getSisaCuti($id_pegawai, 1);
                    $hakCutiThnIni  = $this->Cuti_model->getSisaCuti($id_pegawai, 2);
                    $hakCutiBersama = $this->Cuti_model->getSisaCuti($id_pegawai, 3);
                    $arrayHakCuti = array('Sisa Cuti tahun lalu', 'Hak Cuti tahun ini', 'Hak Cuti Bersama');
                    $arraySisaCuti = array($hakCutiThnLalu, $hakCutiThnIni, $hakCutiBersama );
                ?>




             <div class="modal fade" id="formcuti-modal" tabindex="-1" aria-labelledby="exampleModalLabel1">
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

                                            <?php

                                                for ($c=0; $c < count($master_cuti); $c++) {
                                                    $id = $master_cuti[$c]->id;
                                                    $jenis_cuti = $master_cuti[$c]->jenis_cuti;

                                                    echo ' <option value="'. $id .'">'.$jenis_cuti .'</option>';

                                                }
                                              ?>


                                      </select>

                                  </div>
                                  <div class="col-md-6 mt-3">
                                  Hak  Cuti yang digunakan:
                                      <select name="jns_hak_cuti" id="jns_cuti"  class="form-control">
                                      <?php
                                          for ($i=0; $i < count($arrayHakCuti) ; $i++) {
                                              $idjnsHak = $i+1;
                                              $nama_hak_cuti = $arrayHakCuti[$i];

                                            echo '<option value="'.$idjnsHak.'">'.$nama_hak_cuti.' ('.$arraySisaCuti[$i].')</option>';


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



               <div class="modal fade" id="al-info-alert" tabindex="-1" aria-labelledby="vertical-center-modal"
                      aria-hidden="true">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content modal-filled">
                          <div class="modal-body p-4">
                            <div class="text-center text-danger">
                              <i class="ti ti-info-circle fs-7"></i>
                              <h4 class="mt-2">Pembatalan Cuti</h4>
                              <p class="mt-3">
                                Apakah anda yakin ingin membatalkan  cuti ini?
                              </p>

                              <form method="post" action="<?php echo base_url();?>cuti/cancel_cuti_pegawai">


                                <input type="hidden" name="id_cuti" id="id_cuti_cancel" value="">


                                <button type="submit" class="btn btn-success my-2"> Iya, Batalkan </button>
                                <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">
                                Batal
                            </button>

                            </form>

                            </div>
                          </div>
                        </div>
                        <!-- /.modal-content -->
                      </div>
                    </div>
                    </div>
