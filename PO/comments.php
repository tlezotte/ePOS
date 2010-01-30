<?php
/**
 * Request System
 *
 * detail.php displays detailed information on PO.
 *
 * @version 1.5
 * @link https://hr.Company.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * PDF Toolkit
 * @link http://www.accesspdf.com/
 */



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



/* -------------------- Start Send eail for private message ---------------------- */
if ($_POST['type'] == 'private') {
	$employee = getEmployee($_POST['eid']);

	/* Send out email message */
	$sendTo = $employee['email'];
	$subject = $default['title1'] . " - Private Message";

$message_body = <<< END_OF_BODY
The following message was sent by $_SESSION[fullname]. To reply, select the below message in this email.<br>
<br>
<a href="$default[URL_HOME]/PO/comments.php?action=comment&eid=$_SESSION[eid]&request_id=$_POST[request_id]&type=private">$_POST[comment]</a><br>
<br>
END_OF_BODY;

	$url = $default['URL_HOME']."/PO/detail.php?id=".$_POST['request_id'];
	
	sendGeneric($sendTo, $subject, $message_body, $url);
}
/* -------------------- End Send eail for private message ---------------------- */

/* ------------------ START DATA PROCESSING ----------------------- */
if ($_POST['action'] == 'add') {
	$type = ($_POST['type'] == 'private') ? 'private' : 'global';							// Set comment type
	$gbAction = ($_POST['type'] == 'private') ? 'close' : 'reload';							// Set GreyBox action
	$message_type = ($_POST['type'] == 'private') ? 'emailed' : 'saved';
	$sendto = ($_POST['type'] == 'private') ? $_POST['eid'] : '';							// Set send to 
	/* Enter comment into Posting database */
	$post_sql="INSERT INTO Postings (id, request_id, eid, posted, comment, type, sendto) VALUES 
									 (NULL, 
									  '".mysql_real_escape_string($_POST['request_id'])."', 
									  '".mysql_real_escape_string($_SESSION['eid'])."',
									  NOW(),
									  '".mysql_real_escape_string($_POST['comment'])."',
									  '".mysql_real_escape_string($type)."',
									  '".mysql_real_escape_string($sendto)."')";
	$dbh->query($post_sql);	
			
	$message="Your comment has been " . $message_type  . ".";
	$forward = "../Common/blank.php?gb=" . $gbAction . "&message=".$message;
	header('Location: '.$forward);
	exit();		
}

/* Getting Employee Information */
$post_sql = $dbh->prepare("SELECT * FROM Postings 
						   WHERE request_id = ".$_GET['request_id']." 
							 AND type = 'global'
						   ORDER BY posted DESC");
$post_sth = $dbh->execute($post_sql);
$num_rows = $post_sth->numRows();								 	
/* ------------------ END DATA PROCESSING ----------------------- */
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?= $language['label']['title1']; ?></title>
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">  
  <script src="/Common/js/tiny_mce/tiny_mce_gzip.php"></script>
  <script type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "simple"
	});
  </script>
</head>

<body style="background-color:#E6E6E6;">
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<form method="post" name="Form" id="Form" action="<?= $_SERVER['PHP_SELF']; ?>">
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td><table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><textarea name="comment" cols="75" rows="12" id="comment"></textarea></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="5"><img src="../images/spacer.gif" width="10" height="5"></td>
    </tr>
    <tr>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td align="right"><input name="eid" type="hidden" id="eid" value="<?= $_GET['eid']; ?>">
                <input name="type" type="hidden" id="type" value="<?= $_GET['type']; ?>">
                <input name="request_id" type="hidden" id="request_id" value="<?= $_GET['request_id']; ?>">
                <input name="action" type="hidden" id="action" value="add">
                <?php $commentButton=($_GET['type'] == 'private') ? "Send Message" : "Post Comment"; ?>
                <input name="imageField" type="image" src="../images/button.php?i=w150.png&l=<?= $commentButton; ?>" alt="<?= $commentButton; ?>" border="0">
            &nbsp;</td>
        </tr>
      </table></td>
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
