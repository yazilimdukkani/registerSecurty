<?php 
session_start();
ob_start();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <title>Kayıt Ol</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" type="" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
    .kapsayici{
      margin: 200px auto;
        min-height: 400px;
        width: 450px;
        background: rgba(110, 110, 110, 0);
        border-radius: 5px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5.8px);
        -webkit-backdrop-filter: blur(5.8px);
    }

.form{

  padding:40px 40px 0px 40px;


}


label{

color: black;

}

body{

    background:#d4d4cd;

}

a{
margin:10px;
color: black;
float: right;
}

    </style>
</head>
<body>
    
<div class="col-md-4 offset-md-4 kapsayici">
    
<div class="form">
    

<form action="" method="post">

<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Kullancı Adı</label>
<input class="form-control" type="text" placeholder="" aria-label="default input example" name="kadi">
</div>

<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">E-Posta</label>
<input class="form-control" type="text" placeholder="" aria-label="default input example" name="eposta">
</div>

<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Şifre</label>
<input class="form-control" type="password" placeholder="" aria-label="default input example" name="password">

</div>

<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">TcNo</label>
<input class="form-control" type="text" placeholder="" aria-label="default input example" name="tc">

</div>
<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Ad</label>
<input class="form-control" type="text" placeholder="" aria-label="default input example" name="ad">

</div>
<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Soyad</label>
<input class="form-control" type="text" placeholder="" aria-label="default input example" name="soyad">

</div>

<div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Doğum yılı</label>
<input class="form-control" type="text" placeholder="" aria-label="default input example" name="yil">

</div>
<button type="submit" class="btn btn-warning col-12">Kayıt Ol</button>

<a href="login">Zaten bir hesabın var mı? </a>

</form>
<?php 


  include 'admin/class/class-1.php';
  
#burasi register

$insert= new Klasbuke();



if (!empty($_POST['kadi']) && !empty($_POST['eposta']) && !empty($_POST['password']) && !empty($_POST['tc'])
&& !empty($_POST['ad']) && !empty($_POST['soyad']) && !empty($_POST['yil'])) {
  $insert->EmailControl($_POST['eposta'],$_POST['tc']);

  $kontrol=$insert->EmailRow();
if ($kontrol) {
# email kontrol


include("registerHata.php");


}
else{

  #soap tc kimlik

  function tcno_dogrula($bilgiler){
    $gonder = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
    <TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
    <TCKimlikNo>'.$bilgiler["tcno"].'</TCKimlikNo>
    <Ad>'.$bilgiler["isim"].'</Ad>
    <Soyad>'.$bilgiler["soyisim"].'</Soyad>
    <DogumYili>'.$bilgiler["dogumyili"].'</DogumYili>
    </TCKimlikNoDogrula>
    </soap:Body>
    </soap:Envelope>';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_POST,           true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS,    $gonder);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
    'POST /Service/KPSPublic.asmx HTTP/1.1',
    'Host: tckimlik.nvi.gov.tr',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
    'Content-Length: '.strlen($gonder)
    ));
    $gelen = curl_exec($ch);
    curl_close($ch);
      return strip_tags($gelen);
  }

$bilgiler = array(
"isim"      => $_POST['ad'], // Isım büyük harflerle yazılmak zorunda
"soyisim"   => $_POST['soyad'], // Soyisim Buyuk harflerle yazılmak zorunda
"dogumyili" => $_POST['yil'],
"tcno"      => $_POST['tc']
);
$sonuc = tcno_dogrula($bilgiler);

if($sonuc=="true"){

  //echo "Girmiş olduğunuz kimlik bilgileri doğrudur.";

  $user="user";

#xss guvenlik kontrol
$kullanici=@$_POST['kadi'];
$posta=@$_POST['eposta'];
$diziKullanici= array('/(<script>)/','/(<\/script>)/','/(,)/','/(;)/','/(")/','/(alert)/',"/(')/",'/(<)/','/(\$)/','/(\?)/','/(>)/','/( )/'
,'/(\{)/','/(\})/','/(\()/','/(\))/');

$diziPosta=array('/(<script>)/','/(<\/script>)/','/(,)/','/(;)/','/(")/','/(alert)/',"/(')/",'/(<)/','/(\$)/','/(\?)/','/(>)/','/(\{)/','/(\})/'
,'/(\()/','/(\))/');

$kadi= preg_replace($diziKullanici, "b", $kullanici);



$eposta= preg_replace($diziPosta, "c", $posta);
$aranacak=".@gmail.com";
$aranacakHot=".@hotmail.com";
$et= ".@";



$uzantiGmail=strstr($eposta,$aranacak);
$ilkAranacak=strpos($uzantiGmail,'.');



$uzantiHotmail=strstr($eposta,$aranacakHot);
$hot=strpos($uzantiHotmail,'.');

$uzantiEt=strstr($eposta,$et);
$etim= strpos($uzantiEt,'.');

#son nokta karakteri olmucak
$son=".com.";


$aranacakSon=strstr($eposta,$son);
$sonKarakter=substr($aranacakSon,-1);
#son nokta karakteri olmucak


#ilk nokta karakteri olmucak


$ilkKarakter= substr($eposta,0,1);
//var_dump($ilkKarakter);
#ilk nokta karakteri olmucak

#nokta birtane olabilir


$birNokta="..";
$noktaKarakter=strstr($eposta,$birNokta);


#nokta birtane olabilir

# @gmail.com kullanılcak
$kutu="@gmail.com";

$gmail=strstr($eposta,$kutu);

# @gmail.com kullanılcak


if ($ilkAranacak === FALSE  && $hot === FALSE && $etim === FALSE && !$sonKarakter && $ilkKarakter != "."  && !$noktaKarakter && $gmail) {
  # code...

$eski=array('ç','ğ','ı','ö','ş','ü','Ç','Ğ','I','Ö','Ş','Ü','.','_','-','*','/','=','#','!','^','%','&');
$yeni=array('c','g','i','o','s','u','C','G','İ','O','S','U','N','N','N','N','N','N','N','N','N','N','N');



$eskiposta=array('ç','ğ','ı','ö','ş','ü','Ç','Ğ','I','Ö','Ş','Ü',' ','-','_','*','/','=','#','!','^','%','&');
$yeniposta=array('c','g','i','o','s','u','C','G','İ','O','S','U','b','b','b','N','N','N','N','N','N','N','N');


  $cevirKadi=str_replace($eski,$yeni,$kadi);
  
  
  $cevirEposta=str_replace($eskiposta,$yeniposta,$eposta);
#xss guvenlik kontrol

 


  $insert->Register($cevirKadi,md5($_POST['password']),$cevirEposta,$user,$_POST['tc'],$_POST['ad'],$_POST['soyad'],$_POST['yil']);
  
  $ip=$_SERVER['REMOTE_ADDR'];
  $tarih=date("H:i:s Y-m-d ");
  $insert->RegisterLog($cevirKadi,$cevirEposta,$ip,$tarih);
  include("admin/success.php");
  
  header("Refresh:2; url=register");



}

else{

echo "böle bir mail adresi yok";

}


}else{
echo "Doğrulama başarısız";
}

  #soap tc kimlik

      

    }
  
  

  





}


?>

</div>
</div>




<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>

</body>
</html>