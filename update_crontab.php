<?php
$cronfile_l="/etc/crontab";
if(fif("sparson.php",$cronfile_l)) {
  echo "Crontab entry for ping_sparson.php FOUND! (skipping)\n";
}
else {
  $f=fopen($cronfile_l,"a");
  if($f) {
    fwrite($f,"*/5 * * * * root php /home/sparson/sparson/ping_sparson.php\n");
    fclose($f);
  }
}

function fif($t,$f) {
  $d=file_get_contents($f);
  if(stristr($d,$t)) return true;
  return false;
}
