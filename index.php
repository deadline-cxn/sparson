<?php

////////////////////////////////////////////////////////////////////////////
// SPARSON.COM 
include("config.php");
include("common.php");
$GLOBALS['expired_host_time'] = 310;

session_start();
// echo $UPDATE_INTERVAL;

if(isset($_REQUEST['a'])) $a=$_REQUEST['a'];

if(isset($a)) {
    if($a=="time")  echo date("H:i:s");
    if($a=="p")     update_host();
    exit();
}

show_header();
show_hosts();

function show_header() {
    echo "<html>";
    echo "<head>";
    echo "<title>SPARSON</title>";
    $x=guid(1);
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"sparson.css?v=$x\"/>";
    echo "</head>";
}

function show_hosts() {
    $td[0]="#000";
    $td[1]="#030"; 
    $tdc=0;
    echo "<table border=0>";
    echo "<tr>";
    echo "<th>Host</th>";
    echo "<th>Network</th>";
    echo "<th>Last Ping</th>";
    echo "</tr>";
    $r=lib_mysql_query('select * from hosts order by hostname');
    while($row=$r->fetch_object()) {
        $tdc=$tdc+1; if($tdc>1) $tdc=0;

        echo "<tr>";
        
        echo "<td style='background-color: ".$td[$tdc]."' >";
        
        echo "<table border=0><tr><td>";
        $dir=scandir("images");
        foreach($dir as $k => $v) {
            $name=explode(".",$v);
            $name=strtolower($name[0]);
            if(stristr(strtolower($row->distro),$name)) {
                $rut=strtolower($row->distro);
               echo "<img src=\"images/$v\" width=80>";
               break;
            }
        }
        echo "</td><td>";
        
        echo "$row->hostname";
        echo "<hr>";
        echo "$row->os ";
        echo "$row->distro ";
        echo "$row->distroversion ";
        echo "$row->distrocodename<br>";
        echo "<hr>";
        
        echo nl2br($row->drives);
        
        echo "</td></tr></table>";
        
        
        
        echo "</td>";
        
        echo "<td style='background-color: ".$td[$tdc]."' >";
        echo "$row->ip_address";
        echo "<table border=0><tr>";
        echo "<td>";
        
        $c="ping $row->ip_address -c 1";
        exec($c,$ping_result);
        $ping_a=explode("=",$ping_result[1]);
        $ping_a=str_replace(" ms","",$ping_a[3]);
        echo "<table border=0><tr><td>";
        echo $ping_a."";
        echo "</td><td>";
        
        if(!isset($_SESSION[$row->hostname])) {
            $_SESSION[$row->hostname]=array();//"what";
            $_SESSION[$row->hostname]['pingtimes']=array();
            $_SESSION[$row->hostname]['pingtimes'][0]="0.1";
            $_SESSION[$row->hostname]['pingtimes'][1]="0.2";
            $_SESSION[$row->hostname]['pingtimes'][2]=0;
            $_SESSION[$row->hostname]['pingtimes'][3]=0;
            $_SESSION[$row->hostname]['pingtimes'][4]=0;
            $_SESSION[$row->hostname]['pingtimes'][5]=0;
            $_SESSION[$row->hostname]['pingtimes'][6]=0;
            $_SESSION[$row->hostname]['pingtimes'][7]=0;
            $_SESSION[$row->hostname]['pingtimes'][8]=0;
            $_SESSION[$row->hostname]['pingtimes'][9]=0;
        }
        
        for($i=0;$i<9;$i++) {
            $_SESSION[$row->hostname]['pingtimes'][$i] = 
            $_SESSION[$row->hostname]['pingtimes'][$i+1];
        }
        $_SESSION[$row->hostname]['pingtimes'][8]=$ping_a;
  
        $out="?a=pingline&w=90&h=50";
        for($i=8;$i>0;$i--) {
            $out.="&pl$i=".$_SESSION[$row->hostname]['pingtimes'][$i];
        }
        
        echo "<img src=\"/genimg.php$out\"> ";
        
        unset($ping_a);
        unset($ping_result);
        
        $current_time=time();
        $last_update_time=strtotime($row->timestamp);
        $time_transpired=$current_time-$last_update_time;

        if(!$row->do_not_delete) {
            if($time_transpired>$GLOBALS['expired_host_time']) {
                $query="delete from `hosts` where `hostname` = '$row->hostname' limit 1";
                lib_mysql_query($query);
            }
        }
        

        echo "</td></tr></table>";
        
        
        
        echo "</td>";
        echo "</td></tr></table>";
        
        echo "<td style='background-color: ".$td[$tdc]."' >";
        echo "$row->timestamp";
        echo "</td>";
        
        //echo "<td style='background-color: ".$td[$tdc]."' >";
        //echo "</td>";

        echo "</tr>";
    }
    echo "</table>";
    
    echo "<meta http-equiv=\"refresh\" content=\"5\">";
    
}

function update_host() {
    $hostname=$_REQUEST['hostname'];
    $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
    $datetime=date("Y-m-d H:i:s");
    $os=$_REQUEST['os'];
    $distro=$_REQUEST['distro'];
    $distroversion=$_REQUEST['distroversion'];
    $distrocodename=$_REQUEST['distrocodename'];
    $drives=$_REQUEST['drives'];
    
    $r=lib_mysql_query("select * from hosts where hostname='$hostname';");
    if($r->num_rows==0){
        $id = guid(1);        
        $q = "insert into `hosts` ( `id`, `hostname`,  `ip_address`,`timestamp`, `os`,  `distro`,  `distroversion`,  `distrocodename`, `drives` )
                           values ('$id','$hostname','$REMOTE_ADDR','$datetime','$os', '$distro', '$distroversion', '$distrocodename', '$drives' );";
        lib_mysql_query($q);
        $q = "select * from `hosts` where `hostname`='$hostname';";
        $r=lib_mysql_query($q);
    }
    $row=$r->fetch_assoc();
    $q="update `hosts` set `ip_address` = '$REMOTE_ADDR' where `hostname`='$hostname'";         lib_mysql_query($q);
    $q="update `hosts` set `os` = '$os' where `hostname`='$hostname'";                          lib_mysql_query($q);
    $q="update `hosts` set `distro` = '$distro' where `hostname`='$hostname'";                  lib_mysql_query($q);
    $q="update `hosts` set `distroversion` = '$distroversion' where `hostname`='$hostname'";    lib_mysql_query($q);
    $q="update `hosts` set `distrocodename` = '$distrocodename' where `hostname`='$hostname'";  lib_mysql_query($q);
    $q="update `hosts` set `timestamp` = '$datetime' where `hostname` = '$hostname'";           lib_mysql_query($q);
    $q="update `hosts` set `drives` = '$drives' where `hostname` = '$hostname'";                lib_mysql_query($q);


}
