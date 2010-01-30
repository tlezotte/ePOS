<?php 
/**
 * Request System
 *
 * accessRequest.php allows users to request access to system.
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
if ($debug_page) { $request_email = "tlezotte@Company.com"; }

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php'); 
/**
 * - Database Connection
 */
require_once('../Connections/connStandards.php'); 
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');
/**
 * -- Email 
 */
require("phpmailer/class.phpmailer.php");


/**
 * ---------------- $_POST REQUEST -----------------
 */
switch ($_POST['action']) {
	/** 
	 * -------------------- REQUEST ---------------------- 
	 */
	case 'request':  
		$email = $_POST['email']."@".$default['email_domain'];
		$name = ucwords(strtolower($_POST[fst]." ".$_POST[lst]));

		/* ----- Start Send out email message ----- */
		$sendTo = $request_email;
		$subject = $default['title1'] . " - Access Request Notification";

$message_body = <<< END_OF_BODY
The following user is requesting access to $default[title1]<br>
<br>
<b><a href="mailto:$email">$name</a></b><br>
<a href="$default[URL_HOME]/Administration/accessRequest.php?action=yes&eid=$_POST[eid]&fst=$_POST[fst]&mdl=$_POST[mdl]&lst=$_POST[lst]&email=$_POST[email]&phn=$_POST[phn]&dept=$_POST[dept]" style="color: #000000; text-decoration: none;"><img src="$default[URL_HOME]/images/approved.gif" width="18" height="18" align="texttop" border="0"> YES</a><br>
<a href="$default[URL_HOME]/Administration/accessRequest.php?action=no&eid=$_POST[eid]&fst=$_POST[fst]&lst=$_POST[lst]&email=$_POST[email]" style="color: #000000; text-decoration: none;"><img src="$default[URL_HOME]/images/notapproved.gif" width="18" height="18" align="texttop" border="0"> NO</a><br>
END_OF_BODY;

		$url = "https://".$default['server'].$default['url_home'];
		
		sendGeneric($sendTo, $subject, $message_body, $url);
		/* ----- End Send out email message ----- */

		/* Record transaction for history */
		History(NULL, $_POST['action'], $_SERVER['PHP_SELF'], $name);
				
		$message="Your access request was sent to $request_name.<br><br>Please click outside this window to continue.";
		$forward = "../Common/blank.php?message=".$message;
		header('Location: '.$forward);
		exit();
}

/**
 * ---------------- $_GET REQUEST -----------------
 */
switch ($_GET['action']) {		
	/** 
	 * -------------------- YES --------------------- 
	 */	
	case 'yes':
		$users = $dbh->getRow("SELECT * FROM Users WHERE eid=".$_GET['eid']);						//Get user data
		$standards = $dbh->getRow("SELECT * FROM Standards.Employees WHERE eid=".$_GET['eid']);		//Get user data
		
		/* ----- Check to see if user is already a user ----- */
		if (isset($users)) {
			$get_email = $_GET['email']."@".$default['email_domain'];							//Generate email address
			
			/* --- Check current email address --- */
			if ($standards['email'] != $get_email) {
				$email = $get_email;
				$dbh_standards->query("UPDATE Employees SET email='".$get_email."' WHERE eid=".$_GET['eid']);		//Update email address in Standards
			} else {
				$email = $standards['email'];
			}
			$username = $standards['username'];
			$password = $standards['password'];
		}
		
		/* ----- Check to see if user is already in Standards ----- */
		if (isset($standards)) {
			$sql="INSERT INTO Users (eid) VALUES ('".$_GET['eid']."')";
			$dbh->query($sql);												//Add user to system
			History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));				// Record transaction for history
			
			$username = $standards['username'];
			$password = $standards['password'];
			$email = $standards['email'];			
		} else {
//			$fst = strtolower($_GET['fst']);							
//			$lst = strtolower($_GET['lst']);
			
//			$username = $fst{0} . substr($lst, 0, 7);						//Generate username
//			$password = $fst{0} . $lst{0} . $_GET['eid'];					//Generate password
//			$email = $_GET['email']."@".$default['email_domain'];			//Generate email address
						
//			$sql_standards="INSERT INTO Employees (dept, phn, lst, fst, mdl, eid, email, username, password) 
//										   VALUES ('".$_GET['dept']."', '".$_GET['phn']."', '".$_GET['lst']."', '".$_GET['fst']."', '".$_GET['mdl']."', '".$_GET['eid']."', '".$email."', '".$username."', '".$password."')";
//			$dbh_standards->query($sql_standards);							//Add user to Standards												  
//			History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql_standards)));		// Record transaction for history
															  
//			$sql="INSERT INTO Users (eid) VALUES (".$_GET['eid'].")";
//			$dbh->query($sql);												//Add user to system
//			History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));				// Record transaction for history	

			echo caps($_GET['fst'] . " " . $_GET['lst']) . " is not listed in the Employees database and needs an EID generated for them.<br>Please contact Leanne Czuchaj<br>";
		}
	
		/* ----- Start Send Email ----- */
		$sendTo = $_GET[email]."@".$default['email_domain'];
		
		$subject = $default['title1'] . " - Access Request Notification";
				
$message_body = <<< END_OF_HTML
You now have access to $default[title1]<br>
<br>
Username: $username<br>
Password: $password<br>
END_OF_HTML;

		$url=$default['URL_HOME'];

		sendGeneric($sendTo, $subject, $message_body, $url);
		/* ----- End Send Email ----- */
				
		header('Location: ../index.php');
		exit();	
	break;
		
	/**
	 * -------------------- NO ---------------------- 
	 */	
	case 'no';
		/* ----- Start Send Email ----- */
		$sendTo = $_GET[email]."@".$default['email_domain'];
		
		$subject = $default['title1'] . " - Access Request Notification";
				
$message_body = <<< END_OF_HTML
Your request for access to $default[title1] has been declined. If you have<br>
any questions contact <a href="mailto:$request_email">$request_name.</a><br>
END_OF_HTML;

		$url=$default['URL_HOME'];

		sendGeneric($sendTo, $subject, $message_body, $url);
		/* ----- End Send Email ----- */
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], $name);
				
		header('Location: ../index.php');
		exit();	
	break;		
}

/* ------------- START DATABASE CONNECTIONS --------------------- */
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status='0'
						   ORDER BY name");						    
/* ------------- END DATABASE CONNECTIONS --------------------- */

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
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?>
  <script src="/Common/js/pointers.js" type="text/javascript"></script>
  <script src="/Common/js/gen_validatorv3.js" type="text/javascript"></script>
  <SCRIPT SRC="/Common/js/overlibmws.js"></SCRIPT>
  <SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
  <SCRIPT SRC="/Common/js/googleAutoFillKill.js"></SCRIPT>
  </head>

  <body>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon">
    <br>
    <table  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                  <tr>
                    <td width="110"><vllabel form="Form" validators="fst" class="valRequired2" errclass="valError">First:</vllabel></td>
                    <td><input name="fst" type="text" id="fst" size="20" maxlength="20">
                        <vlvalidator name="fst" type="required" control="fst"></td>
                  </tr>
                  <tr>
                    <td class="valNone">Middle:</td>
                    <td><input name="mdl" type="text" id="mdl" size="5" maxlength="1"></td>
                  </tr>
                  <tr>
                    <td><vllabel form="Form" validators="lst" class="valRequired2" errclass="valError">Last:</vllabel></td>
                    <td><input name="lst" type="text" id="lst" size="30" maxlength="30">
                        <vlvalidator name="lst" type="required" control="lst"></td>
                  </tr>
                  <tr>
                    <td><vllabel form="Form" validators="email" class="valRequired2" errclass="valError">Email Address:</vllabel></td>
                    <td><input name="email" type="text" id="email" size="10" maxlength="15">
                      @
                        <?= $default['email_domain']; ?>
                        <vlvalidator name="email" type="required" control="email"></td>
                  </tr>
                  <tr>
                    <td><vllabel form="Form" validators="eid" class="valRequired2" errclass="valError">Employee ID:</vllabel></td>
                    <td><input name="eid" type="text" id="eid" size="5" maxlength="5">
                        <vlvalidator name="eid" type="required" control="eid" errmsg="Employee ID requires 5 digits" minlength="5" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td class="valNone">Department:</td>
                    <td><select name="dept" id="dept">
                        <option value="0" selected>Select One</option>
                        <?php
						  $dept_sth = $dbh->execute($dept_sql);
						  while($dept_sth->fetchInto($DEPT)) {
							print "<option value=\"".$DEPT[id]."\">(".$DEPT[id].")".ucwords(strtolower($DEPT[name]))."</option>";
						  }
						?>
                    </select></td>
                  </tr>
                  <tr>
                    <td class="valNone">Phone:</td>
                    <td><input name="phn" type="text" id="phn" size="15" maxlength="15"></td>
                  </tr>
              </table></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="bottom"><img src="../images/spacer.gif" width="15" height="5" border="0"></td>
      </tr>
      <tr>
        <td valign="bottom"><div align="right">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><span class="Copyright"><strong>NOTE:</strong> Your Access Request is being sent<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to
                  <?= $request_name; ?>
                  for approval. </span></td>
                <td align="right"><input name="action" type="hidden" id="action" value="request">
                    <input name="imageField" type="image" src="../images/button.php?i=b130.png&l=Send Request" border="0">
                  &nbsp;&nbsp; </td>
              </tr>
            </table>
        </div></td>
      </tr>
      <tr>
        <td valign="bottom"><br>
            <vlsummary form="Form" class="valErrorListSmall" headertext="Form Errors:" displaymode="bulletlist" showsummary="true" messagebox="false"></td>
      </tr>
      <tr>
        <td height="50" valign="bottom"><!-- #BeginLibraryItem "/Library/history.lbi" -->
<script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php if ($_SESSION['request_access'] == 3) { ?>
<table width="190"  border="0" cellspacing="0" cellpadding="0">
	<tr>
	  <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
			<td align="center"><span class="ColorHeaderSubSub">Administration</span> </td>
			<td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
		  </tr>
	  </table></td>
	</tr>
	<tr>
	  <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td><a href="javascript:void(0);" class="dark" onClick="MM_openBrWindow('history.php?page=<?= $_SERVER[PHP_SELF]; ?>','history','scrollbars=yes,resizable=yes,width=875,height=800')" <?php help('', 'Get the history of this page', 'default'); ?>><strong> History </strong></a></td>
		  </tr>
	  </table></td>
	</tr>
	<tr>
	  <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
			<td><img src="../images/spacer.gif" width="10" height="10"></td>
			<td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
		  </tr>
	  </table></td>
	</tr>
</table>
<?php } ?>
<!-- #EndLibraryItem --></td>
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
