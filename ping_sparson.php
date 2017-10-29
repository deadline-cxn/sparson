<?php
include("common.php");
// CRONTAB ENTRY: */5 * * * * root php /home/sparson/sparson/ping_sparson.php

$hostname = urlencode(gethostname());
$uname	  = urlencode(php_uname());
$os       = urlencode(PHP_OS);


$url="http://sparson.com?a=p";
$url.="&hostname=$hostname";
$url.="&os=$os";

$x=file_get_contents($url);


