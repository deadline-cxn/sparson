<?php
include("./common.php");
$cronfile_l="/etc/crontab";
if(fif("sparson.php",$cronfile_l)) {
  echo "Crontab entry for ping_sparson.php FOUND! (skipping)\n";
}
else {
  $f=fopen($cronfile_l,"a");
  if($f) {
    $c="*/5 * * * * root php ";///home/sparson/sparson/ping_sparson.php\n";
    $c.=getcwd()."/ping_sparson.php\n";
    echo "WRITING [$c] to [$cronfile_l]\n";
    fwrite($f,$c);
    fclose($f);
  }
}

