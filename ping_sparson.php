<?php
include("common.php");
// CRONTAB ENTRY: */5 * * * * root php /home/sparson/sparson/ping_sparson.php

$hostname = urlencode(gethostname());
$uname	  = urlencode(php_uname());
$os       = urlencode(PHP_OS);
$distro_i       = get_distro();
$distro         = urlencode($distro_i["DISTRIB_ID"]);
$distroversion  = urlencode($distro_i["DISTRIB_RELEASE"]);
$distrocodename = urlencode($distro_i["DISTRIB_CODENAME"]);

$url="http://sparson.com?a=p";
$url.="&hostname=$hostname";
$url.="&os=$os";
$url.="&distro=$distro";
$url.="&distroversion=$distroversion";
$url.="&distrocodename=$distrocodename";

$x=file_get_contents($url);


