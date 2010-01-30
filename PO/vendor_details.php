<?php 
/**
 * Request System
 *
 * supplier_edit.php allows users to edit supplier information.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
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

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ------------- START DATABASE CONNECTIONS --------------------- */
$vendor_sql = "SELECT * 
			   FROM Standards.Vendor 
			   WHERE BTVEND='" . $_GET['id'] . "'";
$VENDOR = $dbh->getRow($vendor_sql);		 
/* ------------- END DATABASE CONNECTIONS --------------------- */  
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title><?= $default['title1']; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/newCompany.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../epos.css" type="text/css" charset="UTF-8" rel="stylesheet">
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
	  <td width="125" class="xpHeaderLeft">ID</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTVEND']; ?></td>
	</tr>
	<tr>
	  <td width="100" class="xpHeaderLeft">Database</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTADR7']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">&nbsp;</td>		
	  <td bgcolor="#dfdfbf">&nbsp;</td>
	</tr>		
	<tr>
	  <td class="xpHeaderLeft">Name</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTNAME']; ?></td>
	</tr>			
	<tr>
	  <td class="xpHeaderLeft">Address1</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTADR1']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Address2</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTADR2']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Address4</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTADR4']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Address5</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTADR5']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Address6</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTADR6']; ?></td>
	</tr>											
	<tr>
	  <td class="xpHeaderLeft">City</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTADR3']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">State</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTPRCD']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">ZIP</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTPOST']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Country</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTCNTC']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">&nbsp;</td>		
	  <td bgcolor="#ffffff">&nbsp;</td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Contact</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTCONT']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Phone</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTTEL#']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Fax</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTFAX#']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Email</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= $VENDOR['BTEMAL']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Web</td>
	  <td bgcolor="#dfdfbf" class="padding" nowrap><?= $VENDOR['BTWPAG']; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">Status</td>
	  <td bgcolor="#ffffff" class="padding" nowrap><?= ($VENDOR['BTSTAT'] == 'A') ? Active : Inactive; ?></td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">&nbsp;</td>		
	  <td bgcolor="#dfdfbf" class="padding" nowrap>&nbsp;</td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">&nbsp;</td>		
	  <td bgcolor="#ffffff" class="padding" nowrap>&nbsp;</td>
	</tr>
	<tr>
	  <td class="xpHeaderLeft">&nbsp;</td>		
	  <td bgcolor="#dfdfbf" class="padding" nowrap>&nbsp;</td>
	</tr>			
 </table>
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