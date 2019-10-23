<!DOCTYPE html>
<html>
<head>
	<title></title>
<style type="text/css">
	.return {
		width:50%; border: 1px dashed; padding: 1em;
	}
</style>
</head>
<body>
<?php

$public_key_file = 'public.pem';
$private_key_file = 'private.pem';

// При первом запуске создадим ключи
if(!file_exists('private.pem'))
      createCert('priv');





// DECRIPT -  Работаем с сообщением - расшифровываем
if(isset($_GET['crypttext']) && !empty($_GET['crypttext'])){

   $crypttext = $_GET['crypttext'] ;
   
	// Ставим права для чтения ключа (лучше создавать на уровень выше )   
   exec("chmod 400 private.pem");
   // Извлекаем
   $pk  = openssl_get_privatekey(  file_get_contents($private_key_file) ) ;
   exec( "chmod 000 private.pem");

   
   if(openssl_private_decrypt( base64_decode( $crypttext) , $out, $pk) ){
   echo 'Расшифровано приватным ключом<div class="return">';
   echo $out;
   echo '</div>';
   }
   else
      echo "Не удалось расшифровать";

   openssl_free_key($pk);
}






// ENCRYPT
if(isset($_GET['message']) && !empty($_GET['message'])){
     // Добавляем
	 $plain = $_GET['message'];   
     $publickey = openssl_get_publickey(  file_get_contents($public_key_file)  );
     	

   // Encrypt
   	openssl_public_encrypt($plain, $crypttext, $publickey);
   // Криптованный текст
     echo 'Зашифрованное публичным ключом <div class="return">'. chunk_split( base64_encode($crypttext), 64 ).'</div>' ;
   // освобождаем ресурсы ключей
   openssl_free_key($publickey);
}     


// Создаем ключи
function createCert($type=""){
	
	if($type == 'priv'){
	 	// Если приватного ключа нет, то генерим пару заново 
		exec('openssl genrsa -out private.pem 2048');
		exec('openssl rsa -in private.pem -out public.pem -outform PEM -pubout ');
		Echo 'Сгенерилась новая пара ключей, запускай заново';
      exec( "chmod 000 private.pem");

		return file_get_contents('private.pem');
	}
	elseif($type == 'public'){
		// Усли публичного нет - генерим только его
		exec('openssl rsa -in private.pem -out public.pem -outform PEM -pubout ');
		return file_get_contents('public.pem');
	}
	else{
		echo "Укажи, какого ключа нет";
		return false;
	}
}

   	  
?>

<h1><a href=".">очистить</a> | <a href="sealed.php"  target="_blank">Запечатать своим ключом</a></h1>

<h3>Запечатать</h3>
<form action="" method="GET"> 
    <p><input type="text" name="message"> message</p>
   <input type="submit" name="" value="Запечатать">
</form>

<hr>

<h3>Распечатать</h3>
<form action="" method="GET"> 
   <p><input type="text" name="crypttext"> crypttext</p>
   <input type="submit" name="" value="Распечатать">
</form>
<hr>
<a href="public.pem">Публичный ключ</a>
<hr>
<div class="return">
<?php 
// Выводим пуьличный ключ в удобоваримом виде
$key = file_get_contents( $public_key_file );
$key = str_replace([
    '-----BEGIN PUBLIC KEY-----',
    '----END PUBLIC KEY-----',
    "\r\n",
    "\n",
], [
    '',
    '',
    "\n",
    ''
], $key);


echo $key = "-----BEGIN PUBLIC KEY-----<br>" . wordwrap($key, 64, "<br>", true) . "<br>-----END PUBLIC KEY-----";
//echo $key = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($key, 64, "\n", true) . "\n-----END PUBLIC KEY-----";

?></div>


</body>
</html>