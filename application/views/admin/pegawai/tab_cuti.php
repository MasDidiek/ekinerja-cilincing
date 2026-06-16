<?php


$tgl_masuk = $pegawai[0]->tgl_masuk;
$nip = $pegawai[0]->nip;
$nama_pegawai = $pegawai[0]->nama;
$id_pegawai = $pegawai[0]->id_pegawai;


$sisaTahunLalu = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 1, 'DESC');
$sisaTahunIni = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 2, 'DESC');
$sisaCuber = $this->Pegawai_model->getHakCutiPegawai($id_pegawai, 3, 'DESC');

$sisaCutiAll = $sisaTahunLalu+$sisaTahunIni+$sisaCuber;

?>

       <div class="row">
           <div class="col-lg-3 col-md-6">
                    
                  <div class="card">
                    <div class="card-body">
                      <button type="button" class="text-info bg-info-subtle btn float-end" id="btn-edit1"  title="edit cuti"> <i class="ti ti-pencil fs-6"></i></button>
                        <div class="d-flex flex-row align-items-center">
                          <div class="round-40 text-white d-flex align-items-center justify-content-center text-bg-warning">
                            <i class="ti ti-credit-card fs-6"></i>
                          </div>
                          <div class="ms-3 align-self-center">
                              <h3 class="mb-0 fs-6">
                                <input type="text" name="jumlah_cuti" value="<?php echo $sisaTahunLalu;?>" id="jumlah_cuti1" class="fake-input">  
                                <small>hari</small>
                              </h3>
                            <span class="text-muted">Sisa Cuti Tahun lalu</span>
                          </div>
                        </div>

                        <form method="post" action="<?php echo base_url();?>admin/pegawai/insert_sisa_cuti/<?php echo $id_pegawai;?>" id="input_cuti_tahun_lalu">
                              <div class="input-number input-cuti1 row mt-4 d-none">
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" name="qty_input" value="0" id="input_form1">
                                        <input type="hidden" name="jns_hak" value="1">
                                        <input type="hidden" name="sisa_akhir" id="sisa_akhir1" value="<?php echo $sisaTahunLalu;?>">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="mybtn btn-save"> <i class="ti ti-check fs-6"></i></button>
                                
                                        <button type="button" class="mybtn btn-cancel"> <i class="ti ti-x fs-6"></i></button>
                                    </div>
                              </div>

                         </form>
                    </div>
                  </div>
                </div>
                <!-- Column -->
                <!-- Column -->
                <div class="col-lg-3 col-md-6">
                  <div class="card">
                    <div class="card-body">
                    <button type="button" class="text-info bg-info-subtle btn float-end" id="btn-edit2"  title="edit cuti"> <i class="ti ti-pencil fs-6"></i></button>

                      <div class="d-flex flex-row align-items-center">
                        <div class="round-40  text-white d-flex align-items-center justify-content-center text-bg-info">
                          <i class="ti ti-users fs-6"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                          <h3 class="mb-0 fs-6">
                              <input type="text" name="jumlah_cuti" value="<?php echo $sisaTahunIni;?>" id="jumlah_cuti2" class="fake-input">  
                              <small>hari</small>
                            </h3>
                          <span class="text-muted">Sisa Cuti Tahun 2024</span>
                        </div>
                      </div>

                         <form method="post" action="<?php echo base_url();?>admin/pegawai/insert_sisa_cuti/<?php echo $id_pegawai;?>" id="input_cuti_tahun_ini">

                                <div class="input-number  input-cuti2 row mt-4 d-none">

                                   <div class="col-md-8">
                                        <input type="number" class="form-control" name="qty_input" value="0" id="input_form2">
                                        <input type="hidden" name="sisa_akhir" id="sisa_akhir2" value="<?php echo $sisaTahunIni;?>">
                                        <input type="hidden" name="jns_hak" value="2">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="mybtn btn-save"> <i class="ti ti-check fs-6"></i></button>
                                        <button type="button" class="mybtn btn-cancel"> <i class="ti ti-x fs-6"></i></button>
                                    </div>
                                </div>

                        </form>

                    </div>
                  </div>
                </div>
                <!-- Column -->
                <!-- Column -->
                <div class="col-lg-3 col-md-6">
                  <div class="card">
                    <div class="card-body">
                    <button type="button" class="text-info bg-info-subtle btn float-end" id="btn-edit3" title="edit cuti"> <i class="ti ti-pencil fs-6"></i></button>

                      <div class="d-flex flex-row align-items-center">
                        <div class="round-40  text-white d-flex align-items-center justify-content-center text-bg-danger">
                          <i class="ti ti-calendar fs-6"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                             <h3 class="mb-0 fs-6">
                              <input type="text" name="jumlah_cuti" value="<?php echo $sisaCuber;?>" id="jumlah_cuti3" class="fake-input">  
                              <small>hari</small>
                            </h3>
                          <span class="text-muted">Hak Cuti Bersama</span>
                        </div>
                      </div>

                       <form method="post" action="<?php echo base_url();?>admin/pegawai/insert_sisa_cuti/<?php echo $id_pegawai;?>" id="input_cuti_bersama">
                            <div class="input-number  input-cuti3 row mt-4 d-none">
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" name="qty_input" value="0" id="input_form3">
                                        <input type="hidden" name="sisa_akhir" id="sisa_akhir3" value="<?php echo $sisaCuber;?>">
                                        <input type="hidden" name="jns_hak" value="3">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="mybtn btn-save"> <i class="ti ti-check fs-6"></i></button>
                                
                                        <button type="button" class="mybtn btn-cancel"> <i class="ti ti-x fs-6"></i></button>
                                    </div>

                            </div>

                        </form>

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
          

              
                <table class="table align-middle text-center table-bordered text-nowrap mb-0"   id="data-table">
                                    <thead>
                                        <tr class="text-muted fw-semibold">
                                            <th>No</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th scope="col">Tanggal Mulai</th>
                                            <th scope="col">Tanggal Akhir</th>
                                            <th scope="col">Lama Cuti</th>
                                            <th class="text-start" scope="col">Alasan</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top">

                                    <?php
                                        for ($i=0; $i < count($cutiPegawai) ; $i++) { 
                                          $tgl = $cutiPegawai[$i]->tgl;
                                            $tgl_dari = $cutiPegawai[$i]->tgl_dari;
                                            $tgl_sampai = $cutiPegawai[$i]->tgl_sampai;
                                            $hari_cuti = $cutiPegawai[$i]->hari_cuti;
                                            $status = $cutiPegawai[$i]->status;
                                        
                                            $flagStatus = getStatusCuti($status);

                                            echo '
                                              <tr>
                                                <td>'.($i+1).'</td>
                                                <td>'.format_semi($tgl).'</td>
                                                <td>'.format_semi($tgl_dari).'</td>
                                                <td> '.format_semi($tgl_sampai).'</td>
                                                <td>'. $hari_cuti.' hari</td>
                                                <td class="text-start">'. $cutiPegawai[$i]->alasan_cuti.' </td>
                                                <td>'. $flagStatus.'</td>
                                                <td> <a href="'.base_url().'admin/pegawai/detail_cuti/'.$cutiPegawai[$i]->id.'" class="btn  btn-sm btn-info">Lihat Detail</a> </td>
                                              </tr>
                                            ';


                                        }

                                        ?>




                                    </tbody>
                                </table>
  </div>


