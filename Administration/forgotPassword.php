<?php 
/**
 * Request System
 *
 * forgotPassword.php email a user their password.
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
 * PHP Mailer
 * @link http://phpmailer.sourceforge.net/ 
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


if ($_POST['action'] == 'process' OR $_GET['action'] == 'process') {
	$EID = (array_key_exists('eid', $_GET)) ? $_GET['eid'] : $_POST['eid'];
	/* Get requested users information */
	$USER = $dbh->getRow("SELECT fst, lst, email, username, password ".
						 "FROM Standards.Employees ".
						 "WHERE eid = ?",array($EID));

		/* ----- Start Send Email ----- */
		$sendTo = $USER['email'];
		
		$subject = $default['title1'] . " Access Notification";
				
$message_body = <<< END_OF_HTML
I forgot my username and password.<br>
<br>
<b>Username:</b> $USER[username]<br>
<b>Password:</b> $USER[password]<br>
END_OF_HTML;

		$url=$default['URL_HOME'];

		sendGeneric($sendTo, $subject, $message_body, $url);
		/* ----- End Send Email ----- */
	
	$message="Your username and password has been emailed.<br><br>Please click outside this window to continue.";
	$forward = (array_key_exists('eid', $_GET)) ? 'users.php' : "../Common/blank.php?message=$message";
	header('Location: '.$forward);
	exit();
}

/* Get Purchase Request users */
$employees_sql = "SELECT U.eid, E.fst, E.lst, E.email 
				  FROM Users U, Standards.Employees E
				  WHERE U.eid = E.eid and U.status = '0' and E.status = '0'
				  ORDER BY E.lst ASC";
$Dbg->addDebug($employees_sql,DBGLINE_QUERY,__FILE__,__LINE__);	//Debug SQL
$Dbg->DebugPerf(DBGLINE_QUERY);									//Start debug timer  
$employees_query = $dbh->prepare($employees_sql);
$employees_sth = $dbh->execute($employees_query);
$Dbg->DebugPerf(DBGLINE_QUERY);									//Stop debug timer

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
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/newCompany.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../epos.css" type="text/css" charset="UTF-8" rel="stylesheet">
  </head>
  <body>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon">
    <br>
    <table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="GlobalButtonTextSelected">Select your name from the list below and click <strong>Send</strong>. In a couple of minutes you will receive an email with your username and password. </td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
      </tr>
      <tr>
        <td class="BGAccentVeryDarkBorder"><br>
            <table  border="0" align="center">
              <tr>
                <td nowrap><label for="username"><vllabel form="Form" validators="eid" class="valRequired2" errclass="valError">Your Name:</vllabel> </label></td>
                <td><select name="eid" id="eid">
                    <option value="0">Select One</option>
                    <?php
					$employees_sth = $dbh->execute($employees_sql);
					while($employees_sth->fetchInto($EMPOLYEES)) {
						print "<option value=\"".$EMPOLYEES[eid]."\" ".$selected.">".ucwords(strtolower($EMPOLYEES[lst].", ".$EMPOLYEES[fst]))."</option>";
					}
					?>
					</select></td>
              </tr>
            </table>
          <vlvalidator name="eid" type="compare" control="eid" validtype="string" comparevalue="0" comparecontrol="eid" operator="ne">
            <br></td>
      </tr>
      <tr>
        <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
      </tr>
      <tr>
        <td><div align="right">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
                <td><div align="right">
                    <input name="action" type="hidden" id="action" value="process">
                    <input name="login" type="image" id="login" src="../images/button.php?i=b70.png&l=Send" border="0">
                  &nbsp;&nbsp; </div></td>
              </tr>
            </table>
        </div></td>
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
