<!DOCTYPE html>
<html>
<head>
	<title></title>
<style type="text/css">
	.return {
		width:50%; border: 1px dashed; padding: 1em;
	}
  .red{
    color: red
  }
</style>
</head>
<body>
<?php


// ENCRYPT
if(isset($_POST['message']) && isset($_POST['key']) && !empty($_POST['message'])){
  // Добавляем
	$plain = $_POST['message'];
  if ( !openssl_get_publickey(  $_POST['key']  )   )
      echo '<strong class="red">НЕВАЛИДНЫЙ СЕРТИФИКАТ</strong>'; 	
  else{

      $publickey = openssl_get_publickey(  $_POST['key']  );


   // Encrypt
   	openssl_public_encrypt($plain, $crypttext, $publickey);
    // echo '--'.$crypttext.'--';
   // Криптованный текст
     echo 'Зашифрованное публичным ключом <div class="return">'. chunk_split( base64_encode($crypttext), 64, '<br>' ).'</div>' ;
   // освобождаем ресурсы ключей
   openssl_free_key($publickey);
  }
}     


   	  
?>

<h1>Запечатать публичным ключом</h1>

<h3>Сообщение</h3>
<form action="" method="POST"> 
    <p><input type="text" name="message" value="Типография"> message</p>
    <p><textarea rows="10" cols="45"  name="key"></textarea> Публичный ключ</p>
   <input type="submit" name="" value="Запечатать">
</form>

<hr>

</body>
</html>