<?php 
/**
 * Request System
 *
 * vaction.php turn on or off Vaction mode.
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


/* ----- Get current user information ----- */
$USER = $dbh->getRow("SELECT U.vacation, CONCAT(E.fst, ' ', E.lst) AS vacation_name
				      FROM Users U
					    INNER JOIN Standards.Employees E ON E.eid=U.vacation
				      WHERE U.eid = ?",array($_SESSION['eid']));	
	
/* ---------- Turn off vacation mode ---------- */
if ($_POST['stage'] == 'change') {
	$dbh->query("UPDATE Users SET vacation='0' WHERE eid = ".$_SESSION['eid']);			// Turn off vacation in database

	setcookie(request_vacation, $_SESSION['vacation'], time() - 3600);					// Turn off vacation cookie
	
	stopDelegate($USER['vacation'], $_SESSION['eid']);									// Move open requisition back to user
	
//	header("Location: ../Common/blank.php?gb=forward&url=/go/Request/PO/Reports/delegation.php&message=Delegation of Authority has been turned OFF");
	header("Location: ../Common/blank.php?gb=close&message=Delegation of Authority has been turned OFF");
	exit;
}


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
    <title><?= $default['title1']; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
  <link type="text/css" href="../epos.css" charset="UTF-8" rel="stylesheet">
  
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
    <div align="center"><br>
      <br>
      Delegation of Authority is currently <strong>ON</strong><br>
      and being sent to <strong><?= $USER['vacation_name']; ?></strong><br>
      <br>
      <br>
      <input name="stage" type="hidden" id="stage" value="change">
      <input type="image" src="../images/button.php?i=b110.png&l=Turn Off" border="0"> <img src="../images/button.php?i=b110.png&l=Leave On" border="0" onClick="javascript:parent.GB.hide();"><br> 
    </div>
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