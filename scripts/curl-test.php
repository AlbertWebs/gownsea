<?php

$ch = curl_init('https://gownsea.com/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Mozilla/5.0',
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
]);
$out = curl_exec($ch);
echo 'err='.curl_error($ch).PHP_EOL;
echo 'code='.curl_getinfo($ch, CURLINFO_HTTP_CODE).PHP_EOL;
echo 'len='.strlen((string) $out).PHP_EOL;
curl_close($ch);
