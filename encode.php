<?php
$pub = <<<SOMEDATA777
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBALqbHeRLCyOdykC5SDLqI49ArYGYG1mq
aH9/GnWjGavZM02fos4lc2w6tCchcUBNtJvGqKwhC5JEnx3RYoSX2ucCAwEAAQ==
-----END PUBLIC KEY-----
SOMEDATA777;
$data = "PHP is my secret love.";
$pk  = openssl_get_publickey($pub);

openssl_public_encrypt($data, $encrypted, $pk);
echo chunk_split(base64_encode($encrypted));

?>