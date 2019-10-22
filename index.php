<?php

$public_key_file = 'public.pem';
$private_key_file = 'private.pem';

// При первом запуске создадим ключи
if(!file_exists('private.pem'))
      createCert('priv');


// Работаем с сообщением
if(isset($_GET['crypttext']) && !empty($_GET['crypttext'])){

   $crypttext = $_GET['crypttext'];

   // Извлекаем
   $pk  = openssl_get_privatekey( is_file($private_key_file) ? file_get_contents($private_key_file) : createCert('priv') ) ;
   openssl_private_decrypt( $crypttext, $out, $pk);

   echo "\r\n----------------";
   echo $out;
   echo "---------------------\r\n";

}



if(isset($_GET['message']) && !empty($_GET['message'])){
   // Добавляем

   //$plain = "PHP is my secret love.";	
   $plain = $_GET['message'];   

   $publickey = openssl_get_publickey( is_file($public_key_file) ? file_get_contents($public_key_file) : createCert('public') );
     	

   // Encrypt
   openssl_public_encrypt($plain, $crypttext, $publickey);
   // Криптованный текст
   echo chunk_split(base64_encode($crypttext));
   // освобождаем ресурсы ключей
   openssl_free_key($publickey);
}     
   	  
?>

<form action="" method="GET"> 
   <p><input type="text" name="crypttext"> crypttext</p>
   <p><input type="text" name="message"> message</p>
</form>

<?php

//echo chunk_split( file_get_contents( $public_key_file) );
echo  file_get_contents( $public_key_file) ;









function createCert($type=""){
	
	if($type == 'priv'){
	 	// Если приватного ключа нет, то генерим пару заново 
		exec('openssl genrsa -out private.pem 2048');
		exec('openssl rsa -in private.pem -out public.pem -outform PEM -pubout ');
		Echo 'Сгенерилась новая пара ключей, запускай заново';
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




class OpenSSL {
  
   public $privatekey;
   public $publickey;
   public $csr;
   public $crypttext;
   public $ekey;
   
   public function encrypt($plain) {
     
      // Turn public key into resource
      $publickey = openssl_get_publickey(is_file(OPEN_SSL_PUBKEY_PATH)? file_get_contents(OPEN_SSL_PUBKEY_PATH) : OPEN_SSL_PUBKEY_PATH);
     
      // Encrypt
      openssl_seal($plain, $crypttext, $ekey, array($publickey));
      openssl_free_key($publickey);
     
      // Set values
      $this->crypttext = $crypttext;
      $this->ekey = $ekey[0];
   }

   public function decrypt($crypt, $privatekey, $ekey="") {
  
      // Turn private key into resource
     	 $privatekey = openssl_get_privatekey((is_file($privatekey)? file_get_contents($privatekey) : $privatekey), OPEN_SSL_PASSPHRASE);
     
      // Decrypt
      openssl_open($crypt, $plaintext, $ekey, $privatekey);
      openssl_free_key($privatekey);
     
      // Return value
      return $plaintext;
   }

   public function do_csr(
   $countryName = "UK",
   $stateOrProvinceName = "London",
   $localityName = "Blah",
   $organizationName = "Blah1",
   $organizationalUnitName = "Blah2",
   $commonName = "Joe Bloggs",
   $emailAddress = "openssl@domain.com"
   ) {        
      $dn = array(
         "countryName" => $countryName,
         "stateOrProvinceName" => $stateOrProvinceName,
         "localityName" => $localityName,
         "organizationName" => $organizationName,
         "organizationalUnitName" => $organizationalUnitName,
         "commonName" => $commonName,
         "emailAddress" => $emailAddress
         );
      $config = array(
         "config" => OPEN_SSL_CONF_PATH
         );
      $privkey = openssl_pkey_new();
      $csr = openssl_csr_new($dn, $privkey, $config);
      $sscert = openssl_csr_sign($csr, null, $privkey, OPEN_SSL_CERT_DAYS_VALID, $config);
      openssl_x509_export($sscert, $this->publickey);
      openssl_pkey_export($privkey, $this->privatekey, OPEN_SSL_PASSPHRASE, $config);
      openssl_csr_export($csr, $this->csr);
   }
  
}

?>