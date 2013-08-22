#!/usr/bin/php
<?php
error_reporting(0);

$api_login      = $argv[1] ? $argv[1] : "api_user";
$api_key        = $argv[2] ? $argv[2] : "api_key";

include_once ("./php-cloudfiles/cloudfiles.php");

$auth = new CF_Authentication($api_login, $api_key);
$auth->authenticate();
$conn = new CF_Connection($auth);

if($argv[3]){
    print 'use container: ' . $argv[3];
    $a = array((object) array('name' => $argv[3]));
}
else
{
    print "get containers\t";
    if(($a = $conn->get_containers())) print "ok"; else print "fail";
}

foreach($a as $n)
{
    print "\nentering in container {$n->name}\t";
    if(($g = $conn->get_container($n->name)))
    {
        print "ok\nget objects\t";
	if(($e = $g->get_objects()))
        {
            $c = count($e);
            $i = 1;
            foreach($e as $l)
	    {
                print "\n" . $i . ' of ' . $c . ' ..  ' . $l->name . "\t";
		$o = $g->get_object($l->name);

		$d = explode('/', $l->name); array_pop($d);
                if($d)
                {
                    mkdir($argv[4] . '/' . implode('/', $d), 0755, true);
                }
                print $o->save_to_filename($argv[4] . '/' . $l->name) ? 'ok' : 'fail';
                $i++;
	    }
	}
        else print "fail";
    }
    else print "fail";
    print "\n";
}
