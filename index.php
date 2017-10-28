<?php
////////////////////////////////////////////////////////////////////////////
// SPARSON.COM 
include("config.php");
include("common.php");

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
    echo "<table border=0>";
    echo "<tr>";
    echo "<th>id</th>";
    echo "<th>hostname</th>";
    echo "<th>ip_address</th>";
    
    echo "<th>ping</th>";
    
    echo "<th>timestamp</th>";
    echo "</tr>";
    $r=lib_mysql_query('select * from hosts');
    while($row=$r->fetch_object()) {
        echo "<tr>";
        
        echo "<td>$row->id</td>";
        echo "<td>$row->hostname</td>";
        echo "<td>$row->ip_address</td>";
        
        /*
        $port = 80; 
        $waitTimeoutInSeconds = 1; 
        if($fp = fsockopen($row->ip_address,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
        // It worked 
            $ping_result = "up";
        } else {
        // It didn't work 
            $ping_result = "down";
        } 
        fclose($fp);
        */
        
        echo "<td>";
        
        $c="ping $row->ip_address -c 1";
        exec($c,$ping_result);
        $ping_a=explode("=",$ping_result[1]);
        echo $ping_a[3];
        unset($ping_a);
        unset($ping_result);
        
        //ping($row->ip_address);        
        //echo "($c)<br>";
        //foreach($ping_result as $k => $v) {
            //echo "$k [$v]<br>";
        //}
        
        
        
        echo "</td>";
        
        echo "<td>$row->timestamp</td>";

        echo "</tr>";
    }
    echo "</table>";
    
    echo "<meta http-equiv=\"refresh\" content=\"5\">";
    
}

function update_host() {
    $hostname=$_REQUEST['hostname'];
    $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
    $datetime=date("Y-m-d H:i:s");
    
    $r=lib_mysql_query("select * from hosts where hostname='$hostname';");
    if($r->num_rows==0){
        $id = guid(1);        
        $q = "insert into `hosts` (`id`,`hostname`,`ip_address`,`timestamp`) values ('$id','$hostname','$REMOTE_ADDR','$datetime');";
        //echo "<BR>$q<BR>";
        lib_mysql_query($q);
        $q = "select * from `hosts` where `hostname`='$hostname';";
        // echo "<BR>$q<BR>";
        $r=lib_mysql_query($q);
    }
    
    $row=$r->fetch_assoc();
    
    
    $q="update `hosts` set `ip_address` = '$REMOTE_ADDR' where `hostname`='$hostname'";
    lib_mysql_query($q);
    $q="update `hosts` set `timestamp` = '$datetime' where `hostname` = '$hostname'";
    lib_mysql_query($q);
    // echo "<BR>$q<BR>";

}
