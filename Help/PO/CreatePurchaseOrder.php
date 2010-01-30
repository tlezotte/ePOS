<?php
/**
 * Request System
 *
 * CreatePurchaseOrder.php tutorial on creating a Purchase Request.
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
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Flash: CreatePurchaseOrder', $_SESSION['eid']);
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>PO: Create Purchase Order</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>

<body>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="795" height="795">
  <param name="movie" value="CreatePurchaseOrder.swf">
  <param name="quality" value="high">
  <embed src="CreatePurchaseOrder.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="795" height="795"></embed>
</object>
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