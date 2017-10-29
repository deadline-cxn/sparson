<?php
function lib_mysql_open_database($address,$user,$pass,$dbname) {
	$mysqli=new mysqli($address,$user,$pass,$dbname);
	if($mysqli->connect_errno) { echo "MySQL failed to connect (".$mysqli->connect_errno.") ".$mysqli->connect_error."<br>";	}
	return $mysqli;
}
function lib_mysql_query($query) {
	//if(stristr($query,"`users`")) $mysqli=lib_mysql_open_database($GLOBALS['userdbaddress'],$GLOBALS['userdbuser'],$GLOBALS['userdbpass'],$GLOBALS['userdbname']);
	//else 
	$mysqli=lib_mysql_open_database($GLOBALS['authdbaddress'],$GLOBALS['authdbuser'],$GLOBALS['authdbpass'],$GLOBALS['authdbname']);
	if(mysqli_connect_errno()) { echo "WARNING 38J4"; return; }
	$x=$mysqli->query($query);
    
	if(!$x) {
		if(!stristr($mysqli->error,"duplicate")) {
			$query=str_replace("<","&lt;",$query);
			// d_echo("MYSQL ERROR: $mysqli->error");
			// d_echo("MYSQL QUERY: $query");
		}
	}
    global $mysql_id;
    $mysql_id=$mysqli->insert_id;
    // if(stristr($query,"insert")) printf ("New Record has id %d.\n", $mysqli_id );   
	return ($x);
}
function fif($t,$f) {
  $d=file_get_contents($f);
  if(stristr($d,$t)) return true;
  return false;
}
function guid($trim = true){
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace.
              substr($charid,  0,  8).$hyphen.
              substr($charid,  8,  4).$hyphen.
              substr($charid, 12,  4).$hyphen.
              substr($charid, 16,  4).$hyphen.
              substr($charid, 20, 12).
              $rbrace;
    return $guidv4;
}
function ping($host, $timeout = 1) {
    /* ICMP ping packet with a pre-calculated checksum */
    $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
    $socket  = socket_create(AF_INET, SOCK_RAW, 1);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
    socket_connect($socket, $host, null);
    $ts = microtime(true);
    socket_send($socket, $package, strLen($package), 0);
    if (socket_read($socket, 255)) {
        $result = microtime(true) - $ts;
    } else {
        $result = false;
    }
    socket_close($socket);
    return $result;
}
