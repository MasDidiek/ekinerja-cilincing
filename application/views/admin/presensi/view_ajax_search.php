                                
<?php

    $bulan = $this->session->userdata('periode_bulan') ?? date('m');
    $tahun = $this->session->userdata('periode_tahun') ?? date('Y');
    $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

?>
            <table class="table table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Employee Name</th>
                            <?php

                            $lastDateMonth = date('t', strtotime($periode));
                            for ($i = 1; $i < ($lastDateMonth + 1); $i++) {
                                $date =  $periode . '-' . $i;

                                $tanggal = format_db($date);
                                $day = date('l', strtotime($tanggal));
                                if ($day == 'Sunday') {
                                    $hari = 'Mg';
                                } else if ($day == 'Monday') {
                                    $hari = 'Sn';
                                } else if ($day == 'Tuesday') {
                                    $hari = 'Sl';
                                } else if ($day == 'Wednesday') {
                                    $hari = 'Rb';
                                } else if ($day == 'Thursday') {
                                    $hari = 'Km';
                                } else if ($day == 'Friday') {
                                    $hari = 'Jm';
                                } else {
                                    $hari = 'Sb';
                                }

                                echo ' <th class="text-center">' . $i . ' <br>
                          <small>' . $hari . '</small></th>';
                            }
                            ?>


                        </tr>
                    </thead>
                    <tbody>

                        <?php


                        $no = 1;



                        foreach ($pegawai as $peg) {

                            //print_array($peg);

                            $id_pegawai = $peg->id_pegawai;
                            $nip = $peg->nip;
                            $nama = $peg->nama;
                            $jns_jam_kerja = $peg->jns_jam_kerja;
                            $jns_pegawai = $peg->jns_pegawai;
                            //$id_pj = $peg->id_validator;

                            $pin =  $peg->pin;

                            $dataRekap = $this->Presensi_model->getRekapAbsensiPegawai($id_pegawai, $periode);
                            if (!empty($dataRekap)) {

                                $status = $dataRekap[0]->status;
                                if ($status == 1) {
                                    $flag_rekap = '<i class="uil-shield-check text-success"></i>';
                                } else {
                                    $flag_rekap = '<i class="uil-shield-question  text-warning"></i>';
                                }
                            } else {
                                $flag_rekap = '<i class="uil-exclamation-circle  text-danger"></i>';
                            }



                            $dataAbsensi  = $this->Presensi_model->getAbsensiPegawaiPerbulan($pin, $periode);

                            ?>


                             <tr>
                                <td style="font-size:20px"><?=  $flag_rekap  ?></td>
                                <td>  
                                    <a href="<?=  base_url() . 'admin/absensi/view_absensi_pegawai/' . $pin . '/' . $bulan . '/' . $tahun . '/' . $jns_pegawai  ?>" 
                                    class="btn btn-sm text-start"><?= word_limiter($nama, 2)  ?></a>
                                </td>

                                    <?php
                          
                                    for ($i = 0; $i < count($dataAbsensi); $i++) {
                                        $absn_msk = $dataAbsensi[$i]->jam_masuk;
                                        $absn_plg = $dataAbsensi[$i]->jam_pulang;
                                        $tanggal = $dataAbsensi[$i]->tanggal;
                                        $shift   = $dataAbsensi[$i]->shift;
                                        $status_detail   = $dataAbsensi[$i]->status_detail;
                                        $status   = trim($dataAbsensi[$i]->status);


                                        $status_absen = '<i class="uil-check"></i>';
                                        $flag = 'badge-info-lighten';

                                        if ($shift === 'OFF') {

                                            if($status =='CUTI'){
                                                $status ='CT';
                                                $flag = 'bg-success text-white';
                                            }else if($status =='IZIN'){
                                                $status ='IZ';
                                                $flag = 'bg-warning text-white';
                                            }else{
                                                $flag = 'badge-light-lighten text-muted';
                                            }

                                         
                                            
                                        } else {
                                            
                                            if (empty($absn_msk) && empty($absn_plg)) {
                                                $status_absen = 'A';
                                                $flag = 'badge-danger-lighten';
                                            }elseif (empty($absn_msk) || empty($absn_plg)) {
                                                $flag = 'badge-warning-lighten';
                                            }


                                            if ($shift == 'SM') {
                                                $status_absen = $shift;
                                                if ($absn_msk == '') {
                                                    $flag = 'badge-danger-lighten';
                                                } else {
                                                    $flag = 'badge-info-lighten';
                                                }
                                            }  else if ($shift == 'M') {
                                               $status_absen = $shift;
                                                if ($absn_msk == '') {
                                                    $flag = 'badge-danger-lighten';
                                                } else {
                                                    $flag = 'badge-info-lighten';
                                                }
                                            } else if ($shift == 'L-OFF') {
                                                $status_absen = '<span style="font-size:10px">LO</span>';
                                                if ($absn_plg == '') {
                                                    $flag = 'badge-danger-lighten';
                                                } else {
                                                    $flag = 'badge-info-lighten';
                                                }
                                            } else if ($shift == 'P') {
                                                $status_absen = $shift;
                                                if ($absn_msk == '') {

                                                    if ($absn_plg == '') {
                                                        $flag = 'badge-danger-lighten';
                                                    } else {
                                                        $flag = 'badge-warning-lighten';
                                                    }
                                                } else {
                                                    if ($absn_plg == '') {
                                                        $flag = 'badge-danger-lighten';
                                                    } else {
                                                        $flag = 'badge-info-lighten';
                                                    }
                                                }
                                            } else if ($shift == 'PSM') {
                                                 $status_absen = $shift;
                                                if ($absn_msk == '') {
                                                    $flag = 'badge-danger-lighten';
                                                } else {
                                                    $flag = 'badge-info-lighten';
                                                }
                                            } else if ($shift == 'OFF') {
                                                $flag = 'badge-danger-lighten';
                                            }



                                            if($status=='HADIR'){
                                                $status = '<i class="uil-check"></i>';
                                            }else if($status =='DINAS'){
                                                $status ='DL';
                                                $flag = 'bg-info text-white';
                                            }else if($status =='ALPHA'){
                                                $status ='<i class="uil-exclamation-circle"></i>';
                                                $flag = 'bg-danger-lighten text-danger';
                                            }

                                            
                                        }



                                          

                                        ?>


                                        <td>
                                            <button type="button" data-modal-target="extraLargeModal" 
                                            class="btn btn-xs <?= $flag;?> btn-info-absensi" value="<?= $pin . '/' . $id_pegawai . '/' . $tanggal ;?>">
                                            <?= $status ;?>
                                            </button>
                                        </td>
                                <?php } ?>

                            </tr>

                       <?php  }?>


                    </tbody>
                  </table>