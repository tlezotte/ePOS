<?php
/**
 * Request System
 *
 * ListPurchaseOrders.php tutorial on listing purchase orders.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../../Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('../../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Flash: Details V4', $_SESSION['eid']);
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Help: detail V4 Features</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
#swf {
	width: 1024px;
	margin-top: auto; 
	margin-bottom: auto;	
	margin-left: auto; 
	margin-right: auto;
}
body {
	background-color: #F3F3F3;
}
-->
</style>
<script src="../../../Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
</head>

<body>
<div id="swf">
<script type="text/javascript">
AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0','width','1024','height','768','src','detail','quality','high','pluginspage','http://www.macromedia.com/go/getflashplayer','movie','detail' ); //end AC code
</script><noscript><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="1024" height="768">
  <param name="movie" value="detail.swf">
  <param name="quality" value="high">
  <embed src="detail.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="1024" height="768"></embed>
</object></noscript>
</div>
</body>
</html>
<?php 
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>