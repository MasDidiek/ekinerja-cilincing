<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Sinkron_model extends CI_Model
{


    public function deleteRawAbsensi($pin, $periode)
	{
		$periode = date('Y-m', strtotime($periode));

		$sql = "DELETE FROM absensi where pin = '$pin' AND tanggal like '$periode%'";
		$this->db->query($sql);

		return true;
	}

    public function getDataAbsenMesin($ip_address, $pin)
	{
        $buffer      = $this->getDataPresensi($ip_address,  $pin);
        if($buffer=='failed'){
            return 'failed';
        }else{
            $new_array   = array();

            for($a=0; $a < count($buffer);$a++){
                    $data=$this->Parse_Data($buffer[$a],"<Row>","</Row>");
                    $PIN_absen=$this->Parse_Data($data,"<PIN>","</PIN>");
                    $DateTime = $this->Parse_Data($data,"<DateTime>","</DateTime>");
                    $Verified = $this->Parse_Data($data,"<Verified>","</Verified>");
                    $Status   = $this->Parse_Data($data,"<Status>","</Status>");
 
                        if($PIN_absen != ''){
                            $new_array[] = array(
                                'pin' => $PIN_absen,
                                'DateTime' => $DateTime,
                                'Status'  => $Status,
                                'Verified'  => $Verified
                            );
                        }
                   
                
    
            }
    
            return $new_array;
        }
      

       
	}

    function sinkronAbsensiHarian($id_pegawai,  $pin, $tgl){

        $ip_address    = $this->Pegawai_model->getIpAddresPegawai($id_pegawai);

		$dataPresensi  = $this->Sinkron_model->getDataAbsenMesin($ip_address, $pin);

		//print_array($dataPresensi);
		$absensiexits = false;
		$masuk = '00:00:00';
		$pulang = '00:00:00';
		for ($i=0; $i < count($dataPresensi) ; $i++) { 

			$DateTime  = $dataPresensi[$i]['DateTime'];
			$Status    = $dataPresensi[$i]['Status'];
			$dateAbsen = format_db($DateTime);
			$jamAbsen  = date('H:i:s',strtotime($DateTime));

			if($dateAbsen==$tgl){
				
				$absensiexits = true;
				
				$cekAbsen = $this->Presensi_model->cekAbsenExist($dateAbsen, $pin);
				if($cekAbsen==0){

					$id = $this->Presensi_model->insertShiftPegawai($pin, $tgl, 'REG');
	
					if($Status==0){
						$masuk = $jamAbsen;
						$pulang = '';
					
			
					}else{
						$masuk = '';
						$pulang =$jamAbsen;
					

					}

					$newArray = array(
						'jam_masuk' => $masuk,
						'jam_pulang' => $pulang,
						'telat_menit' => 0,
						'p_awal_menit' => 0,
						'keterangan' => ''
					);
					$this->db->insert('tbl_kehadiran_harian', $newArray);
	
				}else{

					if($Status==0){
						$masuk = $jamAbsen;
					
						$this->db->where('id', $cekAbsen);
						$this->db->set('jam_masuk', $masuk);
						$this->db->update('tbl_kehadiran_harian');
					}else{
						
						$pulang =$jamAbsen;
						$this->db->where('id', $cekAbsen);
						$this->db->set('jam_pulang', $pulang);
						$this->db->update('tbl_kehadiran_harian');

					}//close if status

				}//close if cekAbsen

			} //close if $dateAbsen==$tgl

		}// close loop

        return array($masuk, $pulang);

    }

    // public function getDataAbsenMesin($ip_address, $pin)
	// {

    //     $buffer      = $this->getDataPresensi($ip_address,  $pin );
      
    //     $new_array   = array();

    //     #print_array($buffer);

    //     for($a=0; $a < count($buffer);$a++){
    //             $data=$this->Parse_Data($buffer[$a],"<Row>","</Row>");
    //             $PIN_absen=$this->Parse_Data($data,"<PIN>","</PIN>");
    //             $DateTime = $this->Parse_Data($data,"<DateTime>","</DateTime>");
    //             $Verified = $this->Parse_Data($data,"<Verified>","</Verified>");
    //             $Status   = $this->Parse_Data($data,"<Status>","</Status>");

    //             $new_array[] = array(
    //                 'pin' => $PIN_absen,
    //                 'DateTime' => $DateTime,
    //                 'Status'  => $Status,
    //                 'Verified'  => $Verified
    //             );
            

    //     }

    //     #print_array($new_array);

    //     return $new_array;
	// }

    

    public function insertUser($ip_address, $pin, $nama)
    {
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
        if($Connect){
        
            
            $soap_request="<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">0</ArgComKey>
            <Arg><PIN>".$pin."</PIN><Name>".$nama."</Name></Arg>
            </SetUserInfo>";
            $newLine="\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
            fputs($Connect, "Content-Type: text/xml".$newLine);
            fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
            fputs($Connect, $soap_request.$newLine);
            $buffer="";
            while($Response=fgets($Connect, 1024)){
                $buffer=$buffer.$Response;
            }
            $buffer=$this->Parse_Data($buffer,"<Information>","</Information>");
            return $buffer;
        }else{
            return false;
        } 
          
           
    }

    public function cekKoneksi($ip_address){
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
      
        return $Connect;
    }

    public function getListUserMesin($ip_address)
    {
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);

        if($Connect){
            //$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN>  </Arg></GetAttLog>";
            $soap_request="
                <GetUserInfo>
                    <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
                    <Arg>
                        <PIN xsi:type=\"xsd:integer\">all</PIN>  

                    </Arg>

                </GetUserInfo>";
            $newLine="\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
            fputs($Connect, "Content-Type: text/xml".$newLine);
            fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
            fputs($Connect, $soap_request.$newLine);
            $buffer="";
            while($Response=fgets($Connect, 1024)){
                $buffer=$buffer.$Response;
            }
        }else{
            echo "Koneksi Gagal";
        }

        $buffer= $this->Parse_Data($buffer,"<GetUserInfoResponse>","</GetUserInfoResponse>");
        $buffer=explode("\r\n",$buffer);
        sort($buffer);


        for($a=0;$a<count($buffer);$a++){
            $data = $this->Parse_Data($buffer[$a],"<Row>","</Row>");
            $PIN  = $this->Parse_Data($data,"<PIN2>","</PIN2>");
            $nama = $this->Parse_Data($data,"<Name>","</Name>");


            $new_array[] = array(
                'pin' => $PIN,
                'nama' => $nama,
            );

        }
       

        return $new_array;
    }

    
	function getUserinfo($pin, $ip_address)  {
		$Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);

		if($Connect){
			//$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN>  </Arg></GetAttLog>";
			$soap_request="
                <GetUserInfo>
                    <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
                    <Arg>
                        <PIN xsi:type=\"xsd:integer\">$pin</PIN>  

                    </Arg>

                </GetUserInfo>";
			$newLine="\r\n";
			fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
			fputs($Connect, "Content-Type: text/xml".$newLine);
			fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
			fputs($Connect, $soap_request.$newLine);
			$buffer="";
			while($Response=fgets($Connect, 1024)){
				$buffer=$buffer.$Response;
			}
		}else{
			echo "Koneksi Gagal";
		}

		$buffer= $this->Parse_Data($buffer,"<GetUserInfoResponse>","</GetUserInfoResponse>");
		$buffer=explode("\r\n",$buffer);
		sort($buffer);


		for($a=0;$a<count($buffer);$a++){
			$data = $this->Parse_Data($buffer[$a],"<Row>","</Row>");
			$PIN  = $this->Parse_Data($data,"<PIN2>","</PIN2>");
			$nama = $this->Parse_Data($data,"<Name>","</Name>");


			if($PIN !=''){
				$new_array[] = array(
					'pin' => $PIN,
					'nama' => $nama,
				);
			}
			

		}


		return $new_array;

	}


    public function download_log_mesin($serial_number)
    {
        // 1️⃣ Ambil data mesin
        $mesin = $this->db
            ->where('serial_number', $serial_number)
            ->get('tbl_mesin_absensi')
            ->row();

        if (!$mesin) {
            return 'Mesin tidak ditemukan';
        }

        $ip_address = $mesin->ip_address;

        // 2️⃣ Tentukan last download
        $last_download = $mesin->last_download;


      //  echo $last_download;
        //exit;
        if (!$last_download) {
            // kalau belum pernah download
            $last_download = date('Y-01-01 00:00:00'); 
        }

        // format SOAP datetime
        $soap_datetime = date('Y-m-d\TH:i:s', strtotime($last_download));

         $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
                if($Connect){
                
                    $soap_request="
                    <GetAttLog>
                        <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
                        <Arg>
                            <PIN xsi:type=\"xsd:integer\">2227</PIN>
                            <DateTime xsi:type=\"xsd:dateTime\">
                                $soap_datetime
                            </DateTime>
                        </Arg>

                    </GetAttLog>";
                    $newLine="\r\n";
                    fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
                    fputs($Connect, "Content-Type: text/xml".$newLine);
                    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
                    fputs($Connect, $soap_request.$newLine);
                    $buffer="";
                    while($Response=fgets($Connect, 1024)){
                        $buffer=$buffer.$Response;
                    }
                }else{
                    return  'failed';
                }



        $buffer= $this->Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
        $buffer=explode("\r\n",$buffer);

        if($buffer=='failed'){
            return 'failed';
        }else{
            $new_array   = array();

            for($a=0; $a < count($buffer);$a++){
                    $data=$this->Parse_Data($buffer[$a],"<Row>","</Row>");
                    $pin =$this->Parse_Data($data,"<PIN>","</PIN>");
                    $DateTime = $this->Parse_Data($data,"<DateTime>","</DateTime>");
                    $Verified = $this->Parse_Data($data,"<Verified>","</Verified>");
                    $Status   = $this->Parse_Data($data,"<Status>","</Status>");
 
                                
                if($pin != ''){

                    $sql = "
                        INSERT IGNORE INTO tbl_log_mesin_raw
                        (serial_number, pin, datetime_log, status_log)
                        VALUES (?, ?, ?, ?)
                    ";

                    $this->db->query($sql, [
                        $ip_address,
                        $pin,
                        $DateTime,
                        $Status
                    ]);
                }

    
            }
    
          
        }
      

        // 6️⃣ Update last_download
        $this->db->where('serial_number', $serial_number);
        $this->db->update('tbl_mesin_absensi', [
            'last_download' => $last_download
        ]);

        return 'Sukses download ' . count($data) . ' data';
    }




     private function parse_attlog($response)
    {
        $result = [];

        preg_match_all('/<Row>(.*?)<\/Row>/s', $response, $rows);

        foreach ($rows[1] as $row) {

            preg_match('/<PIN>(.*?)<\/PIN>/', $row, $pin);
            preg_match('/<DateTime>(.*?)<\/DateTime>/', $row, $datetime);
            preg_match('/<Status>(.*?)<\/Status>/', $row, $status);

            if (!empty($pin[1]) && !empty($datetime[1])) {

                $result[] = [
                    'pin'      => trim($pin[1]),
                    'datetime' => date('Y-m-d H:i:s', strtotime($datetime[1])),
                    'status'   => isset($status[1]) ? trim($status[1]) : 0
                ];
            }
        }

        return $result;
    }


    
    function getDataPresensi($ip_address,  $PIN){
        $PIN = trim($PIN);
        $last_sync_datetime = '2026-02-01 16:10:22';
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
                if($Connect){
                
                    $soap_request="
                    <GetAttLog>
                        <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
                        <Arg>
                            <PIN xsi:type=\"xsd:integer\">$PIN</PIN>
                           <DateTime xsi:type=\"xsd:dateTime\">
                                $last_sync_datetime
                            </DateTime>
                        </Arg>

                    </GetAttLog>";
                    $newLine="\r\n";
                    fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
                    fputs($Connect, "Content-Type: text/xml".$newLine);
                    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
                    fputs($Connect, $soap_request.$newLine);
                    $buffer="";
                    while($Response=fgets($Connect, 1024)){
                        $buffer=$buffer.$Response;
                    }
                }else{
                   return  'failed';
                }

    

        $buffer= $this->Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
        $buffer=explode("\r\n",$buffer);

        return $buffer;
    }

    // function getDataPresensi($ip_address,  $PIN ){
    //     $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
    //             if($Connect){
                
    //                 $soap_request="
    //                 <GetAttLog>
    //                     <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
    //                     <Arg>
    //                         <PIN xsi:type=\"xsd:integer\">$PIN</PIN>
    //                         <DateTime xsi:type=\"xsd:date\">
    //                                 2019-05-01
    //                         </DateTime>
    //                     </Arg>

    //                 </GetAttLog>";
    //                 $newLine="\r\n";
    //                 fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
    //                 fputs($Connect, "Content-Type: text/xml".$newLine);
    //                 fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
    //                 fputs($Connect, $soap_request.$newLine);
    //                 $buffer="";
    //                 while($Response=fgets($Connect, 1024)){
    //                     $buffer=$buffer.$Response;
    //                 }
    //             }else{
    //                 echo "<div class='alert alert-danger'>Koneksi Gagal</div>";
    //             }

    

    //     $buffer= $this->Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
    //     $buffer=explode("\r\n",$buffer);

    //     return $buffer;
    // }


    public function deleteDataAbsensi($pin, $ip_address)
    {
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
            if($Connect){
                $soap_request="<ClearData><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">".$pin."</Value></Arg></ClearData>";
                $newLine="\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
                fputs($Connect, "Content-Type: text/xml".$newLine);
                fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
                fputs($Connect, $soap_request.$newLine);
                $buffer="";
                while($Response=fgets($Connect, 1024)){
                    $buffer=$buffer.$Response;
                }
            }else{
                echo "Koneksi Gagal";
                include("parse.php");
                //echo $buffer;
                $buffer=$this->Parse_Data($buffer,"<ClearDataResponse>","</ClearDataResponse>");
                $buffer=$this->Parse_Data($buffer,"<Information>","</Information>");
                
            }




            if(strpos($buffer, 'Successfully!') !== false){
                return '<div class="alert alert-success">Data absensi Berhasil dihapus</div>';
            } else{
                return "Error ! ".$buffer;
            }
                
    }



    public function deleteUser($pin, $ip_address)
    {
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
            if($Connect){
                $soap_request="<DeleteUser><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">".$pin."</PIN></Arg></DeleteUser>";
                $newLine="\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
                fputs($Connect, "Content-Type: text/xml".$newLine);
                fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
                fputs($Connect, $soap_request.$newLine);
                $buffer="";
                while($Response=fgets($Connect, 1024)){
                    $buffer=$buffer.$Response;
                }
            }else{
                echo "Koneksi Gagal";
                include("parse.php");
                //echo $buffer;
                $buffer=$this->Parse_Data($buffer,"<DeleteUserResponse>","</DeleteUserResponse>");
                $buffer=$this->Parse_Data($buffer,"<Information>","</Information>");
                
            }




            if(strpos($buffer, 'Successfully!') !== false){
                return '<div class="alert alert-success">Data Berhasil dihapus</div>';
            } else{
                return "Error ! ".$buffer;
            }
                
    }

    function getSidikJari($ip_address, $pin){
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);

        if($Connect){
            $soap_request="<GetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
            <Arg><PIN xsi:type=\"xsd:integer\">".$pin."</PIN>
            </Arg></GetUserTemplate>";
            $newLine="\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
            fputs($Connect, "Content-Type: text/xml".$newLine);
            fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
            fputs($Connect, $soap_request.$newLine);
            $buffer="";
            while($Response=fgets($Connect, 1024)){
                $buffer=$buffer.$Response;
            }
        }else{
            echo "Koneksi Gagal";
        }
        
        
        $buffer=$this->Parse_Data($buffer,"<GetUserTemplateResponse>","</GetUserTemplateResponse>");
        $buffer=explode("\r\n",$buffer);

        $bufferIn0 = $buffer[0];
        $new_array = array();
        for($a=0;$a<count($buffer);$a++){
            $data=$this->Parse_Data($buffer[$a],"<Row>","</Row>");
            $PIN=$this->Parse_Data($data,"<PIN>","</PIN>");
            $FingerID=$this->Parse_Data($data,"<FingerID>","</FingerID>");
            $Size=$this->Parse_Data($data,"<Size>","</Size>");
            $Valid=$this->Parse_Data($data,"<Valid>","</Valid>");
            $Template=$this->Parse_Data($data,"<Template>","</Template>");

            if($PIN <> ''){

                $new_array[] = array(
                    'pin' => $PIN,
                    'FingerID' => $FingerID,
                    'Template'  => $Template,
                    'Size'  => $Size
                );
            }

        }

        return $new_array;
    }


    public function insertSidikJari($ip_address, $pin, $template, $fn)
    {
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
        // echo $ip_address.'<Br>';
        // echo $template.'<Br>';
        // echo $pin.'<Br>';
        // echo $fn;
        // exit;
            if($Connect){
            
                $soap_request="<SetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">".$pin."</PIN><FingerID xsi:type=\"xsd:integer\">".$fn."</FingerID><Size>".strlen($template)."</Size><Valid>1</Valid><Template>".$template."</Template></Arg></SetUserTemplate>";
                $newLine="\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
                fputs($Connect, "Content-Type: text/xml".$newLine);
                fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
                fputs($Connect, $soap_request.$newLine);
                $buffer="";
                while($Response=fgets($Connect, 1024)){
                    $buffer=$buffer.$Response;
                }

                $buffer=$this->Parse_Data($buffer,"<SetUserTemplateResponse>","</SetUserTemplateResponse>");
                $buffer=$this->Parse_Data($buffer,"<Information>","</Information>");

                return true;
                #return $buffer;
            }else{
                return false;
               # return "Koneksi Gagal";	
            }

        

            exit;




    }


    public function tarikDataAll($ip_address){
        $Connect = fsockopen($ip_address, "80", $errno, $errstr, 1);
        $new_array   = array();


        if($Connect){
            //$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN>  </Arg></GetAttLog>";
            $soap_request="
                <GetAttLog>
                    <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
                    <Arg>
                        <PIN xsi:type=\"xsd:integer\">ALL</PIN>  
                    </Arg>
                    
                </GetAttLog>";
            $newLine="\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
            fputs($Connect, "Content-Type: text/xml".$newLine);
            fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
            fputs($Connect, $soap_request.$newLine);
            $buffer="";
            while($Response=fgets($Connect, 1024)){
                $buffer=$buffer.$Response;
            }

            $buffer= $this->Parse_Data($buffer,"<GetUserInfoResponse>","</GetUserInfoResponse>");
            $buffer=explode("\r\n",$buffer);
            sort($buffer);

            print_array($buffer);


            for($a=0;$a<count($buffer);$a++){
                $data = $this->Parse_Data($buffer[$a],"<Row>","</Row>");
                $PIN  = $this->Parse_Data($data,"<PIN>","</PIN>");
                $DateTime = $this->Parse_Data($data,"<DateTime>","</DateTime>");
                $Verified = $this->Parse_Data($data,"<Verified>","</Verified>");
                $Status = $this->Parse_Data($data,"<Status>","</Status>");

                $new_array[] = array(
                    'pin' => $PIN,
                    'DateTime' => $DateTime,
                    'Verified' => $Verified,
                    'Status' => $Status,
                );

            }
        

            print_array($new_array);
            return $new_array;


    }else{
         echo "Koneksi Gagal";
    }


   }


    function Parse_Data($data,$p1,$p2){
        $data=" ".$data;
        $hasil="";
        $awal=strpos($data,$p1);
        if($awal!=""){
            $akhir=strpos(strstr($data,$p1),$p2);
            if($akhir!=""){
                $hasil=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
            }
        }
        return $hasil;	
    }



}