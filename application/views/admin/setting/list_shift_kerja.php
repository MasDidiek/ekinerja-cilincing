            <?php
            $msg_delete = $this->session->flashdata('msg_delete');
            $msg_insert = $this->session->flashdata('msg_insert');

            ?>


                    <div class="table-responsive p-4">
                       
                        <?php echo $msg_insert;?>

                        <a href="<?php echo base_url();?>mesin_absensi/index" class="btn btn-danger">Back</a>
                     
                        <form action="<?php echo base_url();?>admin/setting/update_all" method="post">

                        <button type="submit" class="btn btn-success float-end ms-2">  <i class="fa-solid fa-refresh"></i>&nbsp; Update Data</button>

                           <a href="#" class="btn btn-info mb-4 float-end ms-2 " data-bs-toggle="modal" data-bs-target="#modal-report">
                                <i class="fa-solid fa-plus"></i>&nbsp;   Tambah Shift Kerja </a>

                                <a href="<?php echo base_url();?>admin/setting/create_initial_shift" class="btn btn-light mb-4 float-end">
                                <i class="fa-solid fa-calendar"></i>&nbsp;   Buat Inisial Shift </a>
                               

                                <div class="clearfix"></div>
                 

                                        <table class="table "  id="data-table">
                                            <thead style="text-align: center">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode Shift</th>
                                                    <th>Nama Shift</th>
                                                    <th>Jam Masuk </th>
                                                    <th>Jam Keluar</th>
                                                    <th>Sort</th>
                                                    <th>Publish</th>
                                                    <th>Action</th>

                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                        $no = 0;
                                                        for ($i=0; $i < count($shift_kerja) ; $i++) { 
                                                            $id  = $shift_kerja[$i]->id;
                                                            $nama_shift = $shift_kerja[$i]->nama_shift;
                                                            $kode_shift = $shift_kerja[$i]->kode_shift;
                                                            $jam_masuk = $shift_kerja[$i]->jam_masuk;
                                                            $jam_pulang = $shift_kerja[$i]->jam_pulang;
                                             
                                                            $publish = $shift_kerja[$i]->publish;
                                                            $urutan = $shift_kerja[$i]->urutan;

                                                            if($publish==1){
                                                                $check ='checked';
                                                            }else{
                                                                $check ='';
                                                            }

                                                            $no = $no+1;
                                                                echo '
                                                                <tr>
                                                                  <input type="hidden" name="id_shift[]" value="'.$id.'">
                                                                    <td style="text-align:center">'.$no.'</td>
                                                                    <td style="text-align:center">'.$kode_shift.'</td>
                                                                    <td>'.$nama_shift.'</td>
                                                                    <td style="text-align: center" class="text-success">'.$jam_masuk.'</td>
                                                                    <td style="text-align: center" class="text-danger">'.$jam_pulang.'</td>
                                                                    <td  style="text-align:center">
                                                                        <input type="text" name="sort[]" value="'.$urutan.'" class="form-table" style="width:50px">
                                                                    </td>
                                                                    <td >
                                                                         <div class="form-check text-center">
                                                                            <input type="checkbox" name="publish[]" value="1" class="form-check-input" '.$check.' id="customCheck1">
                                                                            
                                                                        </div>
                                                                    </td>
                                                                    <td  style="text-align:center">
                                                                        <a href="'.base_url().'admin/setting/edit_shift_kerja/'.$kode_shift.'" class="btn btn-sm btn-success">Ubah</a>
                                                                    </td>
                                                                
                                                                </tr>';
                                                            }
                                                        
                                                ?>
                                            </tbody>

                                        </table>

                               </form>
                         </div>


                  <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content" id="modal-form">
                      <form method="post" action="<?php echo base_url(); ?>admin/setting/insert_user/<?php echo $ip_address.'/'.$serial_number;?>">
      
                          <div class="modal-header">
                              <h5 class="modal-title">Add Mesin Absensi</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
      
                        
                            <div class="modal-body">
                             
                                    <label for="nama shift">PIN &nbsp;: </label> &nbsp;
                                    <input type="text" name="pin" class="form-control" style="width: 150px;" required><br>

                                    <br>
                                    <label for="nama shift">Nama &nbsp;: </label> &nbsp;
                                    <input type="text" name="nama" class="form-control" required><br><br>
            
                            </div>
      
      
                          <div class="modal-footer">
                              <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                  Cancel
                              </a>
                              <button type="submit" class="btn btn-primary ms-auto" >
                              <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                              Save
                              </button>
                          </div>
      
                  </form>
      
              </div>
            </div>
          </div>