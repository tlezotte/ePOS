<?php 
/**
 * Request System
 *
 * bookmark.php emails link and display instructions.
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


/* ----- START $_POST VARIABLES ----- */
switch ($_POST['action']) {
	/* ---------- EMAIL ACCESS REQUEST FORM ---------- */
	case "requestlink":
			$url = "http://".$_SERVER['SERVER_NAME'].$default['url_home']."/BlackBerry/index.php";
			$data = $dbh->getRow("SELECT * FROM Standards.Employees WHERE eid=".$_SESSION['eid']);
			
			require("phpmailer/class.phpmailer.php");
		
			$mail = new PHPMailer();
			
			$mail->From     = $default['email_from'];
			$mail->FromName = $default['title1'];
			$mail->Host     = $default['smtp'];
			$mail->Mailer   = "smtp";
			$mail->AddAddress($data['email']);
			$mail->Subject = $default['title1'].": Blackberry Access";

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<br>
Select the link listed below to display the BlackBerry version of the $default[title1].<br>
Remember to bookmark this page in your BlackBerry after it loads.<br>
<br>
URL: <a href="$url">$url</a><br>
</body>
</html>
END_OF_HTML;
/* HTML message */

			$mail->Body = $htmlBody;
			$mail->isHTML(true);
			
			if(!$mail->Send())
			{
			   echo "Message was not sent";
			   echo "Mailer Error: " . $mail->ErrorInfo;
			}
			
			// Clear all addresses and attachments for next loop
			$mail->ClearAddresses();
			$mail->ClearAttachments();	

			/* Update Summary */
			Summary($dbh, 'BlackBerry Access', $_SESSION['eid']);
	break;
}
/* ----- END $_POST VARIABLES ----- */

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
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td class="DarkHeader">&nbsp;<img src="/Common/images/bb_bullet.gif" width="20" height="20" align="texttop"> <strong>Features</strong></td>
      <td rowspan="11"><img src="../images/spacer.gif" width="20" height="10"></td>
      <td rowspan="11" bgcolor="#001762"><img src="../images/spacer.gif" width="5" height="10"></td>
      <td rowspan="11"><img src="../images/spacer.gif" width="20" height="10"></td>
      <td><img src="/Common/images/bb_logo.gif" width="135" height="40" border="0"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td nowrap><span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>View Purchase Requests </td>
      <td height="20"><form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
          <img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Press the
        <input name="send" type="image" id="send" src="../images/button.php?i=b150.png&l=Send Email Link" align="bottom" border="0">
        Button.
        <input name="action" type="hidden" id="action" value="requestlink">
      </form></td>
    </tr>
    <tr>
      <td nowrap><span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>Approve Purchase Requests </td>
      <td height="20" nowrap><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> You will receive an email on your BlackBerry with the Link.</td>
    </tr>
    <tr>
      <td><span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>Track Shippments </td>
      <td height="20"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Select the <strong>Link</strong> from the email. </td>
    </tr>
    <tr>
      <td><?php if ($_SESSION['request_access'] >= 1) { ?>
          <span style="margin: 0"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> </span>User Administration
        <?php } ?></td>
      <td height="20"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> After the Login Page loads on your BlackBerry, bookmark it. </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="20"><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Push the <strong>Click Wheel</strong> in. </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Select <strong>Add Bookmark</strong> from menu. </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><img src="/Common/images/bb_bullet.gif" width="16" height="16" align="absmiddle"> Click <strong>Add</strong> from the Bookmark popup.</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td class="MessageBoxLink"><div align="center">Leave this page open until you have completed the instructions.</div></td>
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