<?php
/**
 * Request System
 *
 * packingSlip.php emails an electronic packing slip.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
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
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');


/* Update Summary */
Summary($dbh, 'Packing Slip', $_SESSION['eid']);

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");			
							
if ($_POST['send'] == 'yes') {
	include("Mail.php");
	
	/* Getting Issuers information */
	$ISSUER = $dbh->getRow("SELECT * FROM Standards.Employees WHERE eid = ?",array($_POST['issuer']));				
	
	$url = htmlentities($default['URL_HOME']."/PO/detail.php?id=".$_POST['id'], ENT_QUOTES, 'UTF-8');
	$recipients = $ISSUER['email'];
	
	$headers["From"]    = $default['email_from'];
	$headers["To"]      = $ISSUER['email'];
	$headers["Subject"] = "PO Packing Slip: #".$_POST['po'];
	
	$body = "\n-------------------------------------------------------\n".
			"Welcome to the Your Company Purchase Request System\n".
			"---------------------------------------------------------\n\n".
			ucwords(strtolower($EMPLOYEES[$_POST['req']]))." has sent you an electronic\n".
			"Packing Slip for Purchase Order #".$_POST['po']."\n".
			"All items for this Purchase Order have been received.\n\n".$url;
	
	$params["host"] = $default['smtp'];
	$params["port"] = $default['smtp_port'];
	
	// Create the mail object using the Mail::factory method
	$mail_object =& Mail::factory("smtp", $params);
	$mail_object->send($recipients, $headers, $body);
	
    header('Content-Type: text/html; charset=utf-8');
    echo "<html><head><meta http-equiv=\"refresh\" content=\"0;URL=javascript:window.close()\"></head><body></body></html>";
    exit;
}							

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $default['title1']; ?>
    </title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/noPrint.css" rel="stylesheet">
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
  <link type="text/css" href="../epos.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?>
  <?php if ($default['pageloading'] == 'on') { ?>
  <script type="text/javascript" src="/Common/js/pageloading.js"></script>
  <?php } ?>
  <script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <script type="text/javascript" src="/Common/js/overlibmws/overlibmws_iframe.js"></script>
  <script type="text/javascript" SRC="/Common/js/googleAutoFillKill.js"></script>
  <script type="text/javascript" src="/Common/js/disableEnterKey.js"></script>  
  </head>

  <body <?= $ONLOAD; ?>>
  <br>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td> An electronic Packing Slip is sent when you don't receive a packing slip for Items in the Request. (ex. Training or Annual Support)<br>
            <br>
            <blockquote>Click <strong>Send</strong> to send
              <?= ucwords(strtolower($EMPLOYEES[$_GET[issuer]])); ?>
              an electronic Packing Slip for  Purchase Order  #
              <?= $_GET['po']; ?>
              .</blockquote></td>
      </tr>
      <tr>
        <td height="30"><input name="id" type="hidden" id="id" value="<?= $_GET[id]; ?>">
            <input name="req" type="hidden" id="req" value="<?= $_GET[req]; ?>">
            <input name="issuer" type="hidden" id="issuer" value="<?= $_GET[issuer]; ?>">
            <input name="po" type="hidden" id="po" value="<?= $_GET[po]; ?>">
            <input name="send" type="hidden" id="send" value="yes"></td>
      </tr>
      <tr>
        <td><div align="right"><a href="javascript:parent.parent.GB_hide();"><img src="../images/button.php?i=b70.png&l=Cancel" border="0"></a>&nbsp;
                <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Send" border="0">
          &nbsp;&nbsp;</div></td>
      </tr>
    </table>
  </form>
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