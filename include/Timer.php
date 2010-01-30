<?php

function StartLoadTimer() {
 $time = explode(" ",microtime());   
 $time = $time[0] + $time[1];   
 return $time; 
}

function StopLoadTimer() {
 global $starttime;
 
 $round = 3; 
 $time = explode(" ",microtime());   
 $time = $time[0] + $time[1];   
 $endtime = $time;   
 $totaltime = ($endtime - $starttime);
 echo "<img src=\"/Common/images/clock.gif\" width=\"16\" height=\"16\" align=\"absmiddle\">&nbsp;This Page Loaded In: " . round($totaltime,$round) . " seconds";
}
?>