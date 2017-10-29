<?php
////////////////////////////////////////////////////////////////////////////
// SPARSON.COM 
include("config.php");
include("common.php");

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
    echo "<table border=0>";
    echo "<tr>";
    //echo "<th>id</th>";
    echo "<th>hostname</th>";
    echo "<th>os</th>";
    echo "<th>ip_address</th>";
    
    echo "<th>ping</th>";
    
    echo "<th>timestamp</th>";
    echo "</tr>";
    $r=lib_mysql_query('select * from hosts');
    while($row=$r->fetch_object()) {
        echo "<tr>";
        
        //echo "<td>$row->id</td>";
        echo "<td>$row->hostname</td>";
        
        echo "<td>$row->os</td>";
        
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
        

        // imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
    
        $out="?a=pingline&w=100&h=50";
        for($i=8;$i>0;$i--) {
            //echo $_SESSION[$row->hostname]['pingtimes'][$i]."<br>";
            $out.="&pl$i=".$_SESSION[$row->hostname]['pingtimes'][$i];
        }
        
        //echo "<BR>
        
        echo "<img src=\"/genimg.php$out\"> ";
        //<BR>";

        
        unset($ping_a);
        unset($ping_result);
        
        /*echo "<br>";
        echo time()."<br>";
        echo date("Y-d-m H:i:s ",time())."<br>";
        echo date("U",time())."<br>";
        
        echo date("Y-d-m H:i:s",strtotime($row->timestamp))."<br>";
        echo date("U",strtotime($row->timestamp))."<br>";
        */
        $current_time=time();
        $last_update_time=strtotime($row->timestamp);
        $time_transpired=$current_time-$last_update_time;
        
        // echo "ct[$current_time]<br>lu[$last_update_time]<br>tt[$time_transpired]<br>";
        if($time_transpired>$GLOBALS['expired_host_time']) {
            $query="delete from `hosts` where `hostname` = '$row->hostname' limit 1";
            lib_mysql_query($query);
        }
        

        echo "</td></tr></table>";
        
        
        
        echo "</td>";
        
        echo "<td>$row->timestamp</td>";

        echo "</tr>";
    }
    echo "</table>";
    
    echo "<meta http-equiv=\"refresh\" content=\"1\">";
    
}

function update_host() {
    $hostname=$_REQUEST['hostname'];
    $REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
    $datetime=date("Y-m-d H:i:s");
    if(isset($_REQUEST['os'])) $os=$_REQUEST['os'];
    if(!isset($os)) $os="UNKNOWN OS";
    
    $r=lib_mysql_query("select * from hosts where hostname='$hostname';");
    if($r->num_rows==0){
        $id = guid(1);        
        $q = "insert into `hosts` ( `id`, `hostname`,  `ip_address`,`timestamp`,`os`)
                           values ('$id','$hostname','$REMOTE_ADDR','$datetime','$os');";
        //echo "<BR>$q<BR>";
        lib_mysql_query($q);
        $q = "select * from `hosts` where `hostname`='$hostname';";
        // echo "<BR>$q<BR>";
        $r=lib_mysql_query($q);
    }
    
    $row=$r->fetch_assoc();
    
    
    $q="update `hosts` set `ip_address` = '$REMOTE_ADDR' where `hostname`='$hostname'";
    lib_mysql_query($q);
    $q="update `hosts` set `os` = '$os' where `hostname`='$hostname'";
    lib_mysql_query($q);
    $q="update `hosts` set `timestamp` = '$datetime' where `hostname` = '$hostname'";
    lib_mysql_query($q);
    // echo "<BR>$q<BR>";

}
