<?php
/**
 * Job Folders
 *
 * home.php is the default page after a seccessful login.
 *
 * @version 0.1
 * @link http://www.yourcompany.com/go/JobFolders/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @global mixed $default[]
 * @filesource
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');
$Dbg->DatabaseName = $default['database'];


/**
 * - Database Connection
 */
require_once('../Connections/connDB.php'); 
/**
 * - Check User Access 
 */
require_once('../security/check_user.php');
/**
 * - Common Information
 */
require_once('../include/config.php'); 


$sql = "SELECT h.ts, h.action, h.sql, CONCAT(e.lst, ', ', e.fst) AS name
		FROM History h, Standards.Employees e
		WHERE h.eid = e.eid
		  	AND h.page LIKE '".$_GET['page']."'
		ORDER BY h.ts DESC";
$query = $dbh->prepare($sql);

$replace = array("\n", "\r", "\t");
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Company Industries" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/newCompany.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../epos.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <script src="/Common/js/pointers.js" type="text/javascript"></script>
  <SCRIPT SRC="/Common/js/overlibmws.js"></SCRIPT>
  <SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT> 
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
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="30%" height="30" class="xpHeaderTopActive"><strong>&nbsp;Date</strong></td>
    <td width="30%" class="xpHeaderTop"><strong>&nbsp;User</strong></td>
    <td width="40%" class="xpHeaderTop"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong>&nbsp;Action</strong> </td>
          <td width="60" nowrap><a href="javascript:window.close();" class="ErrorNameText">[ close ]</a></td>
        </tr>
    </table></td>
  </tr>
  <?php
	/* Loop through list of POs */
	$sth = $dbh->execute($query);
	$num_rows = $sth->numRows();	
	while($sth->fetchInto($HISTORY)) {
	/* Line counter for alternating line colors */
	$counter++;
	$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
  ?>
  <tr <?php pointer($row_color); ?>>
    <td bgcolor="#<?= $row_color; ?>" class="padding">&nbsp;<?= datetime($HISTORY[ts]); ?></td>
    <td class="padding" bgcolor="#<?= $row_color; ?>">&nbsp;<?= ucwords(strtolower($HISTORY[name])); ?></td>
    <td class="padding" bgcolor="#<?= $row_color; ?>">&nbsp;<?php if ($_SESSION['request_access'] == 3) { ?><a href="javascript:void(0);" <?php showSQL(str_replace($replace, "", addslashes($HISTORY[sql]))); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a><?php } ?><?= $HISTORY[action]; ?></td>
  </tr>
  <?php } ?>
</table>
</body>
</html>



<?php 
/**
 * - Display debug information 
 */
include_once('debug/footer.php');
/* 
 * - Disconnect from database 
 */
$dbh->disconnect();
?>