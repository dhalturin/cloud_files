#!/usr/bin/php
<?php
error_reporting(0);

$api_login      = "айдишник_клодовки";
$api_key        = "ключ_от_клодовки";

include_once ("./php-cloudfiles/cloudfiles.php");

$auth = new CF_Authentication($api_login, $api_key);
$auth->authenticate();
$conn = new CF_Connection($auth);

$a = $conn->get_container('public')->get_objects();

foreach($a as $l)
{

    $c = curl_init();
    $r = curl_setopt($c, CURLOPT_URL,            'http://' . str_replace('_', '-', $api_login) . '.cs.clodoserver.ru/' . $l->name);
    $r = curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_setopt($c, CURLOPT_TIMEOUT,        30);
    $r = curl_exec($c);
    $i = curl_getinfo($c);
    curl_close($c);

    if($i['http_code'] > 350)
    {
        print $i['http_code'] . ' ';
        print $l->name . "\n";
    }
}

