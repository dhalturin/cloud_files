#!/usr/bin/php
<?php
error_reporting(0);

$api_login      = "api_user";
$api_key        = "api_key";

include_once ("./php-cloudfiles/cloudfiles.php");

$auth = new CF_Authentication($api_login, $api_key);
$auth->authenticate();
$conn = new CF_Connection($auth);

print "connection to storage\t";
if(($a = $conn->get_containers())) print "ok"; else print "fail";

foreach($a as $n)
{
#    if($n->name == 'private') continue;

    print "\nentering in container {$n->name}\t";
    #if(($e = $conn->get_container('public')))
    if(($e = $conn->get_container($n->name)))
    {
        print "ok\nget objects\t";
	if(($e = $e->get_objects()))
        {

            foreach($e as $l)
	    {
                print "\n" . $l->name . "\t";
                $u = 'http://' . str_replace('_', '-', $api_login) . '.cs.clodoserver.ru/' . $n->name . '/' . $l->name;
                $c = curl_init();
                $r = curl_setopt($c, CURLOPT_URL,            $u);
                $r = curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                $r = curl_setopt($c, CURLOPT_TIMEOUT,        30);
                $r = curl_exec($c);
                $i = curl_getinfo($c);
                curl_close($c);

                if($i['http_code'] > 350)
                {
                    print 'error - ' . $i['http_code'] . ' ';
                    print $u;
		}
                else
		{
                    print "ok";
                }
	    }
	}
        else print "fail";
    }
    else print "fail";
    print "\n";
}
