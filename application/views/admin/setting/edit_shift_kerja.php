                <div class="card-body">
                      
                  <form action="<?php echo base_url();?>admin/setting/update_shift_kerja/<?php echo $dataEdit[0]->id;?>" method="post">

                      <div class="row pt-3">
                        <div class="col-md-3">
                          <div class="mb-3">
                            <label class="form-label">Kode Shift</label>
                            <input type="text" name="kode_shift" id="firstName" class="form-control" value="<?php echo $dataEdit[0]->kode_shift;?>">
                            
                          </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-3">
                          <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text"  name="nama_shift"  class="form-control"  value="<?php echo $dataEdit[0]->nama_shift;?>">
                          </div>
                        </div>
                        <!--/span-->
                      </div>
                      <!--/row-->
                      <div class="row">
                      <div class="col-md-3">
                          <div class="mb-3">
                            <label class="form-label">Jam Masuk</label>
                            <input type="text" name="jam_masuk" id="jam_masuk" class="form-control" value="<?php echo $dataEdit[0]->jam_masuk;?>">
                          
                          </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-3">
                          <div class="mb-3">
                            <label class="form-label">Jam Keluar</label>
                            <input type="text"  name="jam_keluar"  class="form-control"  value="<?php echo $dataEdit[0]->jam_pulang;?>">
                          </div>
                        </div>
                        <!--/span-->
                      </div>
                      <!--/row-->
                      <div class="row">
                        
                      <?php 
                         $status_kerja =  $dataEdit[0]->status_kerja;
                         $checkUser1 = '';
                         $checkUser2 = '';

                         if($status_kerja =='non_pns'){
                                $checkUser1 = 'checked';
                         }else{
                               $checkUser2 = 'checked';
                         }
                      ?>
                        <!--/span-->
                        <div class="col-md-6">
                          <div class="mb-3">
                            <label class="form-label">Penggunaan untuk pegawai</label>
                            <div class="form-check py-1">
                              <input type="radio" id="customRadio11" name="user_pengguna" value="non_pns" class="form-check-input" <?php echo $checkUser1;?>>
                              <label class="form-check-label" for="customRadio11">Non PNS</label>
                            </div>
                            <div class="form-check py-1">
                              <input type="radio" id="customRadio22" name="user_pengguna"   value="pjlp" class="form-check-input"  <?php echo $checkUser2;?>>
                              <label class="form-check-label" for="customRadio22">PJLP</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                            

                        </div>
                        <!--/span-->
                      </div>


                      <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                      </form>

                    </div>
