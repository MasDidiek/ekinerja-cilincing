<?php ob_start();

define('KATEGORI', 'ms_category');
define('STOCK', 'ts_stock');
define('TBL_USER', 'mst_user');
define('NEW_CSS_PATH', base_url() . 'assets/css/material/');
define('NEW_JS_PATH',  base_url() . 'assets/js/');
define('LIBS_JS_PATH',  base_url() . 'assets/libs/');
define('PROFILE_PAGE', 'https://ekinerja-puskesmascilincing.jakarta.go.id/profile/');
define('IMAGES_PATH', base_url() . 'assets/images/');
define('ABSENSI', base_url() . 'absensi/');
define('PATH_IMAGE', 'https://puskesmascilincing.jakarta.go.id/asset/material/images/');

define('CSS_PATH', base_url() . 'assets/tabler/dist/css/');
define('DIST_PATH', base_url() . 'assets/tabler/dist/');
define('KAPUS','Raden Achmad Sigit Mustika Adi / 196801242007011020');
define('KTU','Muklatul Ainiah / 197003071990022001');




$SERVER_NAME =  $_SERVER['SERVER_NAME'];

if (strpos($SERVER_NAME, '10.15.39.96') !== false) {
	//link local

	define('MAIN_URL', 'http://' . $SERVER_NAME . '/ekinerja-cilincing/');
} else {
	//link production
	define('MAIN_URL', 'https://' . $SERVER_NAME . '/');
}



// Fungsi untuk mengubah byte menjadi format MB/KB
function formatSizeUnits($bytes) {
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}



function tahunText($tanggal){
	$tahun = date('Y', strtotime($tanggal));

	$thn =  substr($tahun,2);

	$txtThn = tanggalText($thn);

	$textTahun = 'Dua Ribu '.$txtThn;
	return $textTahun;
}


function tanggalText($tgl){
	

	if($tgl < 10){
		$tgl = createTextTgl($tgl);

	}else if($tgl==10){
		$tgl = 'Sepuluh';
	}else if($tgl==11){
		$tgl = 'Sebelas';
	}else if($tgl > 11 && $tgl < 20){
	
		$d =  substr($tgl,1);
		$tgl_awal = createTextTgl($d);
		$tgl = $tgl_awal.' Belas';
	}else if($tgl==20){
		$tgl = 'Dua Puluh';
	}else if($tgl > 20 && $tgl < 30){
		$d =  substr($tgl,1);

		$tgl_akhir = createTextTgl($d);
		$tgl = 'Dua Puluh '.$tgl_akhir;
	}else if($tgl == 30){
		$tgl = 'Tiga Puluh';
	}else{
		$tgl = 'Tiga Puluh Satu';
	}


	return $tgl;
	
}

function createTextTgl($tgl){
	switch ($tgl) {
		case 1:
			$tgl_text = 'Satu';
			break;
		case 2:
			$tgl_text = 'Dua';
			break;
		case 3:
			$tgl_text = 'Tiga';
			break;
		case 4:
			$tgl_text = 'Empat';
			break;
		case 5:
			$tgl_text = 'Lima';
			break;
		case 6:
			$tgl_text = 'Enam';
			break;
		case 7:
			$tgl_text = 'Tujuh';
			break;
		case 8:
			$tgl_text = 'Delapan';
			break;
		case 9:
			default:
			$tgl_text = 'Sepuluh';
			break;

		}

		return $tgl_text;
}



function formatTanggalIndo($date){
	$tgl = date('d', strtotime($date));
	$bln = date('m', strtotime($date));
	$thn = date('Y', strtotime($date));

	$bulan  = getBulan($bln);
	$new_date =  $tgl.' '.$bulan.' '.$thn;
	return $new_date;
}

function getFileType($fileUploadName)
{

	$format = substr($_FILES[$fileUploadName]['name'], strrpos($_FILES[$fileUploadName]['name'], '.') + 1);
	return $format;
}


function getNamahari($tgl)
{
	$day = date('D', strtotime($tgl));
	switch ($day) {
		case "Mon":
			$hari = "Senin";
			break;
		case "Tue":
			$hari = "Selasa";
			break;
		case "Wed":
			$hari = "Rabu";
			break;
		case "Thu":
			$hari = "Kamis";
			break;
		case "Fri":
			$hari = "Jumat";
			break;
		case "Sat":
			$hari = "Sabtu";
			break;
		case "Sun":
			$hari = "Minggu";
			break;
		default:
			$hari = "No information available for that day.";
			break;
	}

	return $hari;
}


function getStartDate($bulan, $tahun)
{
	$periode = $tahun . '-' . $bulan;
	$from_date = $periode . '-01';
	$start_date = format_db($from_date);

	return $start_date;
}


function getEndDate($bulan, $tahun)
{

	$start_date = getStartDate($bulan, $tahun);
	$last_date = date('t', strtotime($start_date));
	$periode = $tahun . '-' . $bulan;
	$end_date = $periode . '-' . $last_date;

	return $end_date;
}



function lowerCase($string)
{
	$new_string = strtolower($string);
	return $new_string;
}


function upperCase($string)
{
	$new_string = strtolower($string);
	$new_string = strtoupper($new_string);

	return $new_string;
}




function jenis_pegawai()
{
	$arrayJnsPegawai = array('PNS', 'NON PNS', 'PPPK', 'PPPK PW', 'ISHIP', 'PJLP',  'Lainnya');
	return $arrayJnsPegawai;
}


function format_jam($jam)
{
	$newJam = substr($jam, 0, 5);
	return $newJam;
}


function calculateMinutesDifference($jam_mulai, $jam_selesai) {
    // Membuat objek DateTime dari string

	//tanggal dummy utk menghitung seilih waktu dalam waktu 1 hari
	$tgl = '2024-01-01';
	$start_date = $tgl.' '.$jam_mulai;
	$end_date = $tgl.' '.$jam_selesai;


    $start = new DateTime($start_date);
    $end = new DateTime($end_date);

    // Menghitung selisih antara dua tanggal
    $interval = $start->diff($end);

    // Mengonversi selisih waktu menjadi menit
    $total_minutes = ($interval->y * 525600) + // 1 tahun = 525600 menit
                     ($interval->m * 43800) +  // 1 bulan = 43800 menit (rata-rata)
                     ($interval->d * 1440) +   // 1 hari = 1440 menit
                     ($interval->h * 60) +     // 1 jam = 60 menit
                     $interval->i;             // menit

    return $total_minutes;
}



function get_array($arr)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}
function clear_tags($val)
{
	$new_val = str_replace(',', "", $val);
	$new_val = str_replace('.', "", $new_val);
	return $new_val;
}
function format_full_day($date)
{
	$new_date = date('l, d M Y', strtotime($date));
	return $new_date;
}
function format_db($date)
{
	$new_date = date('Y-m-d', strtotime($date));
	return $new_date;
}
function format_view($date)
{
	$new_date = date('d-m-Y', strtotime($date));
	return $new_date;
}
function format_slash($date)
{
	$new_date = date('d/m/Y', strtotime($date));
	return $new_date;
}
function format_semi($date)
{
	$new_date = date('d M Y', strtotime($date));
	return $new_date;
}

function format_full($date)
{
	$new_date = date('d M Y', strtotime($date));

	$tanggal = str_replace("Jan", "Januari", $new_date);
	$tanggal = str_replace("Feb", "Februari", $tanggal);
	$tanggal = str_replace("Mar", "Maret", $tanggal);
	$tanggal = str_replace("Apr", "April", $tanggal);
	$tanggal = str_replace("May", "Mei", $tanggal);
	$tanggal = str_replace("Jun", "Juni", $tanggal);
	$tanggal = str_replace("Jul", "Juli", $tanggal);
	$tanggal = str_replace("Aug", "Agustus", $tanggal);
	$tanggal = str_replace("Sep", "September", $tanggal);
	$tanggal = str_replace("Oct", "Oktober", $tanggal);
	$tanggal = str_replace("Nov", "November", $tanggal);
	$tanggal = str_replace("Dec", "Desember", $tanggal);


	return $tanggal;
}


if (!function_exists('getIDBulan')) {
	function getIDBulan($bln)
	{

		switch ($bln) {
			case 'Januari':
				$id = 1;
				break;
			case 'Februari':
				$id = 2;
				break;
			case 'Maret':
				$id = 3;
				break;
			case 'April':
			$id = 4;
				break;
			case 'Mei':
				$id = 5;
				break;
			case 'Juni':
			$id = 6;
				break;
			case 'Juli':
			$id = 7;
				break;
			case 'Agustus':
				$id = 8;
				break;
			case 'September':
				$id = 9;
				break;
			case 'Oktober':
				$id = 10;
				break;

			case 'November':
				$id = 11;
				break;

			default:
			$id = 12;
		}

		return $id;
	}
}



function format_hari($date)
{
	$hari = date('l', strtotime($date));
	if ($hari == 'Sunday') {
		$day = 'Minggu';
	} elseif ($hari == 'Monday') {
		$day = 'Senin';
	} elseif ($hari == 'Tuesday') {
		$day = 'Selasa';
	} elseif ($hari == 'Wednesday') {
		$day = 'Rabu';
	} elseif ($hari == 'Thursday') {
		$day = 'Kamis';
	} elseif ($hari == 'Friday') {
		$day = 'Jumat';
	} elseif ($hari == 'Saturday') {
		$day = 'Sabtu';
	}
	return $day;
}


if (!function_exists('add_date')) {
	function add_date($date, $days)
	{
		$plus2 = strtotime('+' . $days . ' day', strtotime($date)); //tambah 1 hari
		$newDate  = date('Y-m-d', $plus2);
		return $newDate;
	}
}

if (!function_exists('getTglTugas')) {
	function getTglTugas($tanggal_dari, $tanggal_sampai)
	{
	
			$m1 = date('m', strtotime($tanggal_dari));
			$m2 = date('m', strtotime($tanggal_sampai));

			$d1 = date('d', strtotime($tanggal_dari));
			$d2 = date('d', strtotime($tanggal_sampai));

			$y1 = date('Y', strtotime($tanggal_dari));

			$bulan  = getBulan($m1);


			if($m1==$m2){
			  $tanggal_tugas = $d1.' - '.$d2.' '.$bulan.' '.$y1;
			}else{
			  $tanggal_tugas = format_view($tanggal_dari).' s/d '.format_view($tanggal_sampai);
			}


		return $tanggal_tugas;
	}
}


if (!function_exists('getHariTugas')) {
	function getHariTugas($tanggal_dari, $tanggal_sampai)
	{
	
		$hari1 = format_hari($tanggal_dari);
		$hari2 = format_hari($tanggal_sampai);

		$hari_tugas = $hari1.' - '.$hari2;


		return $hari_tugas;
	}
}






if (!function_exists('reduce_date')) {
	function reduce_date($date, $days)
	{
		$plus2 = strtotime('-' . $days . ' day', strtotime($date)); //tambah 1 hari
		$newDate  = date('Y-m-d', $plus2);
		return $newDate;
	}
}

if (!function_exists('changeFormatDate')) {

	function changeFormatDate($date)
	{
		//Sat Nov 27 2021

		$newDate  = explode(" ", $date);
		$bulan  = $newDate[1];
		$tgl = $newDate[2];
		$tahun  = $newDate[3];

		$newDate = $tahun . '-' . $bulan . '-' . $tgl;
		$newFormat = format_db($newDate);

		return $newFormat;
	}
}


function hitungMasaKerja($date1, $date2)
{
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);

    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->d;

    return array('years' => $years, 'months' => $months, 'days' => $days);
}



function arrayKategori()
{
	$kategori = array('Alkes', 'Obat', 'Vaksin', 'Cetakan', 'Kebersihan', 'Alat Rumah Tangga', 'ATK', 'Alat Elektronik', 'Lain-lain');
	return $kategori;
}

function arrayBagian()
{
	$bagian  = array('Mutu', 'TB-MH', 'Farmasi', 'Alkes', 'UKP', 'UKM','Admen','Admin Pustu', 'Gizi', 'Lain');
	return $bagian;
}

function arrayUsergroup(){
	$arrayUG = array('Superadmin', 'Kapuskec', 'Kasubbag TU', 'Kapuskel', 'Kasatpel', ' Admin', 'User', 'Penanggung Jawab');
	return $arrayUG;

}

function masterDisposisi(){
	$arrayUG = array('Pj.Barang', 'Pj.Alkes', 'Pj.TB', 'Pj.ART', 'Pj.HIV','Pj.Kesling', 'Pj.BMHP', 'Pj.Mutu', 'Pj.ATK', 'Pj.KIA', 'Pj.Farmasi', ' Pj.Alkes', 'Pj.BMHP', 'Pj.Diklat', 'Pj.Vaksin',  'Pj.IT','Kepegawaian', 'Keuangan', 'UKP', 'PPK', 'UKM', 'Bendahara', 'Perencanaan', 'Ka.Sub.bag.TU');
	return $arrayUG;

}


function createMessageInfo($msg, $alert_type='success'){


	if($alert_type=='success'){
		$mssage = '<div class="alert bg-success-subtle text-success alert-dismissible bg-success border-0 fade show" role="alert">
			<button type="button" class="btn-close btn-close-success" data-bs-dismiss="alert" aria-label="Close"></button>
			<strong>Success! </strong> '.$msg.'
		</div>';
	}else{
		$mssage = '<div class="alert bg-'.$alert_type.'-subtle text-'.$alert_type.' alert-dismissible  border-0 fade show" role="alert">
					<button type="button" class="btn-close btn-close-danger" data-bs-dismiss="alert" aria-label="Close"></button>
					<strong>Gagal! </strong> '.$msg.'
				</div>';

	}
		

	  return $mssage;

}

if (!function_exists('ugName')) {
	function getugName($id)
	{

		switch ($id) {
			case 0:
				$groupName = 'Superadmin';
				break;
			case 1:
				$groupName = 'Kapuskec';
				break;
			case 2:
				$groupName = 'KA Subbag TU';
				break;
			case 3:
				$groupName = 'Kapuskel';
				break;
			case 4:
				$groupName = 'Kasatpel';
				break;
			case 5:
				$groupName = 'Admin';
				break;
				
			case 6:
				$groupName = 'User';
				break;
			default:
				$groupName = 'Penanggung Jawab ';
		}

		return $groupName;
	}
}


function addDaysToDate($date, $days)
{
    $datetime = new DateTime($date);
    $datetime->add(new DateInterval('P'.$days.'D'));
    return $datetime->format('Y-m-d');
}


function formatDayOfWeek($date)
{
    $datetime = new DateTime($date);
    return $datetime->format('N'); // Returns the day of the week as a numeric value
}



function dateDifference($date1, $date2)
{

    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
     return $interval->format('%a'); // Returns the difference in days
}



if (!function_exists('getPendidikan')) {
	function getPendidikan($id)
	{

		switch ($id) {
			case 0:
				$pendidikan = 'Belum/Tidak Bersekolah';
				break;
			case 1:
				$pendidikan = 'SD';
				break;
			case 2:
				$pendidikan = 'SMP';
				break;
			case 3:
				$pendidikan = 'SLTA';
				break;
			case 4:
				$pendidikan = 'D III/D IV';
				break;
			case 5:
				$pendidikan = 'S1';
				break;
			case 6:
				$pendidikan = 'S2/dr/drg/Apoteker/Ners';
				break;
				
			default:
				$pendidikan = 'not found';
		}

		return $pendidikan;
	}
}




if (!function_exists('getNamaBulan')) {
	function getNamaBulan($bln)
	{

		switch ($bln) {
			case 1:
				$namaBulan = 'Jan';
				break;
			case 2:
				$namaBulan = 'Feb';
				break;
			case 3:
				$namaBulan = 'Mar';
				break;
			case 4:
				$namaBulan = 'Apr';
				break;
			case 5:
				$namaBulan = 'Mei';
				break;
			case 6:
				$namaBulan = 'Juni';
				break;
			case 7:
				$namaBulan = 'Juli';
				break;
			case 8:
				$namaBulan = 'Agust';
				break;
			case 9:
				$namaBulan = 'Sep';
				break;
			case 10:
				$namaBulan = 'Okt';
				break;

			case 11:
				$namaBulan = 'Nov';
				break;

			default:
				$namaBulan = 'Des';
		}

		return $namaBulan;
	}
}


function getHariTerakhir($bulan)
{
	if ($bulan == 2) {
		$numDays = 28;
	} elseif ($bulan == 4 || $bulan == 6 || $bulan == 9 || $bulan == 11) {
		$numDays = 30;
	} else {
		$numDays = 31;
	}

	return $numDays;
}
function getWaktu()
{

	$waktu = array('07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30');



	return $waktu;
}

function format_db2($date)
{
	$expl = explode("/", $date);
	//$impl = implode("-", $expl);
	$ar0 = $expl[0]; //bulan
	$ar1 = $expl[1]; //tanggal
	$ar2 = $expl[2]; //tahun
	//$tgl = str_replace("/", "-", $date);
	$new_date = $ar2 . '-' . $ar0 . '-' . $ar1;
	return $new_date;
}
function format_db3($date)
{
	$expl = explode("/", $date);
	//$impl = implode("-", $expl);
	$ar0 = $expl[0]; //tanggal
	$ar1 = $expl[1]; //bulan
	$ar2 = $expl[2]; //tahun
	//$tgl = str_replace("/", "-", $date);
	$new_date = $ar2 . '-' . $ar1 . '-' . $ar0;
	return $new_date;
}
function rupiah($val)
{
	$return = str_replace(',', '.', number_format($val));
	return $return;
}

function noCommas($val)
{
	#$return = str_replace(',', '.', number_format($val));
	return $val;
}


if (!function_exists('datePlus')) {
	function datePlus($tgl, $num)
	{
		$newDate = strtotime('+' . $num . ' day', strtotime($tgl)); //tambah 1 hari
		$newDate = date('Y-m-d', $newDate);
		return $newDate;
	}
}
if (!function_exists('dateMinus')) {
	function dateMinus($tgl, $num)
	{
		$newDate = strtotime('-' . $num . ' day', strtotime($tgl)); //tambah 1 hari
		$newDate = date('Y-m-d', $newDate);
		return $newDate;
	}
}

function getHourDifference($startHour, $endHour, $case='telat') {
    $format = 'H:i:s'; // Format for representing hours and minutes


	 //case digunakan untuk menghitung telat atau menghitung pulang cepat

    // Create DateTime objects for the start and end hours
    $startDateTime = DateTime::createFromFormat($format, $startHour);
    $endDateTime = DateTime::createFromFormat($format, $endHour);

    // Calculate the difference between the two DateTime objects
    $interval = $startDateTime->diff($endDateTime);

    // Extract the difference in hours and minutes
    $hours = $interval->h;
    $minutes = $interval->i;
	$invert = $interval->invert;


	if($case=='telat'){
		if($invert==1){
				//hasil hitungan sudah min (tidak terlambat)
				$totalMinutes = 0;
			}else{
				// Convert the difference to minutes
				$totalMinutes = $hours * 60 + $minutes;
			}
	}else{
		//hitung pulang
		if($invert==0){
			//hasil hitungan sudah min (tidak terlambat)
			$totalMinutes = 0;
		}else{
			// Convert the difference to minutes
			$totalMinutes = $hours * 60 + $minutes;
		}
	}

	

 
    return $totalMinutes;
}


function word_format($string)
{
	$newFormat = strtolower($string);
	$newFormat = ucwords($newFormat);
	return $newFormat;
}

if (!function_exists('umurbulan')) {
	function umurbulan($date2, $date1)
	{
		$diff = abs(strtotime($date2) - strtotime($date1));
		$years = floor($diff / (365 * 60 * 60 * 24));
		$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
		$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
		$bulan = ($years * 12) + $months;
		return $bulan;
	}
}
function getBulan($bulan)
{
	$arrayBulan = array('--Bulan--', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	for ($b = 0; $b < 13; $b++) {
		if ($bulan == $b) {
			$newMonth = $arrayBulan[$b];
		}
	}
	return $newMonth;
}

function array_bulan()
{
	$bulan = array('--Bulan--', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	return $bulan;
}
function array_bulan2()
{
	$bulan = array('--Semua--', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	return $bulan;
}


function print_array($var)
{
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}


function calculateMasakerja($tgl_masuk) {

	#$bulanTahun = '2023-06-30';
	#$today = new DateTime($bulanTahun);
	$today = new DateTime();

	#print_array($today);
	$diff = $today->diff(new DateTime($tgl_masuk));
	return $diff->y.'-'.$diff->m.'-'.$diff->d;
  }


function dateIndo()
{
	// Ambil waktu server terkini, ambil tanggal dan jam untuk zona indonesia
	$dat_server = mktime(date("G"), date("i"), date("s"), date("n"), date("j"), date("Y"));
	//echo 'Waktu server: '.date("H:i:s", $dat_server);
	// Ambil perbedaan waktu server dengan GMT
	$diff_gmt = substr(date("O", $dat_server), 1, 2);
	// karena perbedaan waktu adalah dalam jam, maka kita jadikan detik
	$datdif_gmt = 60 * 60 * $diff_gmt;
	// Hitung perbedaannya
	if (substr(date("O", $dat_server), 0, 1) == '+') {
		$dat_gmt = $dat_server - $datdif_gmt;
	} else {
		$dat_gmt = $dat_server + $datdif_gmt;
	}
	// Hitung perbedaan GMT dengan waktu Indonesia (GMT+7)
	// karena perbedaan waktu adalah dalam jam, maka kita jadikan detik
	$datdif_id = 60 * 60 * 7;
	$dat_id = $dat_gmt + $datdif_id;
	//echo 'Waktu Indonesia: '.date("H:i:s", $dat_id);
	$date = date("Y-m-d H:i:s", $dat_id);
	return $date;
}
if (!function_exists('gotoEncript')) {
	function gotoEncript($str)
	{
		return str_replace('=', 'equal', str_replace('+', 'plus', base64_encode($str)));
	}
}
if (!function_exists('gotoDecript')) {
	function gotoDecript($str)
	{
		$decript = $str;
		$decript = str_replace('plus', '+', $decript);
		$decript = str_replace('equal', '=', $decript);
		$decript = base64_decode($decript);
		return $decript;
	}
}
function isWeekend($date)
{
	return (date('N', strtotime($date)) >= 6);
}
function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
{
	/*$interval can be:
	yyyy - Number of full years
	q - Number of full quarters
	m - Number of full months
	y - Difference between day numbers
	(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
	d - Number of full days
	w - Number of full weekdays
	ww - Number of full weeks
	h - Number of full hours
	n - Number of full minutes
					s - Number of full seconds (default)
					*/
	if (!$using_timestamps) {
		$datefrom = strtotime($datefrom, 0);
		$dateto = strtotime($dateto, 0);
	}
	$difference = $dateto - $datefrom; // Difference in seconds
	switch ($interval) {
		case 'yyyy': // Number of full years
			$years_difference = floor($difference / 31536000);
			if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
				$years_difference--;
			}
			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
				$years_difference++;
			}
			$datediff = $years_difference;
			break;
		case "q": // Number of full quarters
			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$quarters_difference--;
			$datediff = $quarters_difference;
			break;
		case "m": // Number of full months
			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$months_difference--;
			$datediff = $months_difference;
			break;
		case 'y': // Difference between day numbers
			$datediff = date("z", $dateto) - date("z", $datefrom);
			break;
		case "d": // Number of full days
			$datediff = floor($difference / 86400);
			break;
		case "w": // Number of full weekdays
			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$datediff = ($weeks_difference * 5) + $days_remainder;
			break;
		case "ww": // Number of full weeks
			$datediff = floor($difference / 604800);
			break;
		case "h": // Number of full hours
			$datediff = floor($difference / 3600);
			break;
		case "n": // Number of full minutes
			$datediff = floor($difference / 60);
			break;
		default: // Number of full seconds (default)
			$datediff = $difference;
			break;
	}
	return $datediff;
}
