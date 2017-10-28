<?php
// CRONTAB ENTRY: */5 * * * * root php /home/sparson/sparson/ping_sparson.php
$url="http://sparson.com?a=p";
$url.="&hostname=".gethostname();
$x=file_get_contents($url);
