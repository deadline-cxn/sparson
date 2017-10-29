<?php
// CRONTAB ENTRY: */5 * * * * root php /home/sparson/sparson/ping_sparson.php

$hostname=gethostname();
$os=php_uname();

$url="http://sparson.com?a=p";
$url.="&hostname=$hostname";
$url.="&os=$os";

$x=file_get_contents($url);
