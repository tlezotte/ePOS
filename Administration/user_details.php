<?php 
/**
 * Request System
 *
 * users.php list all users.
 *
 * @version 1.5
 * @link https://hr.Company.com/go/HCR/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */

/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../include/BlackBerry.php');
 
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
require_once('../security/check_access1.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 



/* ---------------------------------------------------
 * -------------- START PROCESSING DATA -------------- 
 * --------------------------------------------------- 
 */
switch ($_POST['action']) {
	case 'update':
		$USER=$dbh->getRow("SELECT * FROM Users WHERE eid='" . $_SESSION['eid'] . "'");
		
		/* ----- Update User information ----- */	
		$update_sql="UPDATE Users SET access='".$_POST['access']."',
									  requester='".$_POST['requester']."',
									  one='".$_POST['one']."',
									  two='".$_POST['two']."',
									  three='".$_POST['three']."',
									  four='".$_POST['four']."',
									  vacation='".$_POST['vacation']."',
									  role='".$_POST['role']."',
									  status='".$_POST['status']."'
								WHERE eid=".$_POST['eid'];
		$dbh->query($update_sql);	

		/* ----- Convert current Requisitions to DofA user ----- */
		if (strlen($USER['vacation']) == 5 AND $_POST['vacation'] == '0') {
			stopDelegate($USER['vacation'], $_SESSION['eid']);									// Turn off vacation
		} elseif (strlen($USER['vacation']) == '0' AND strlen($_POST['vacation']) == 5) {
			startDelegate($_SESSION['eid'], $_POST['vacation']);								// Turn on vacation
		}
				
		$forward="../Common/blank.php?gb=close&message=User Informatin was updated";					
		header("Location: ".$forward);
		exit();
	break;
}
/* ---------------------------------------------------
 * -------------- END PROCESSING DATA -------------- 
 * --------------------------------------------------- 
 */


/* ---------------------------------------------------
 * -------------- START DATABASE ACCESS -------------- 
 * --------------------------------------------------- 
 */
/* ---------- Get employees permissions ---------- */
$user_sql = "SELECT * 
			  FROM Users
			  WHERE eid=".$_GET['eid'];	  
$USER = $dbh->getRow($user_sql);	
/* ---------- Get Current Users ---------- */
$emp_sql = $dbh->prepare("SELECT E.eid, E.fst, E.lst
					   	 FROM Users U
						  INNER JOIN Standards.Employees E ON U.eid = E.eid
						 WHERE E.status = '0' AND U.status = '0'
					   	 ORDER BY E.lst");				  
/* ---------------------------------------------------
 * -------------- END DATABASE ACCESS -------------- 
 * --------------------------------------------------- 
 */


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="init();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $language['label']['title1']; ?>
    </title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
  <link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
  <link href="/Common/newCompany.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../default.css" type="text/css" charset="UTF-8" rel="stylesheet">
  <script src="/Common/js/pointers.js" type="text/javascript"></script>
  
  <SCRIPT SRC="/Common/js/overlibmws.js"></SCRIPT>
  <SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
  
  <SCRIPT SRC="/Common/js/googleAutoFillKill.js"></SCRIPT>
  
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>  
  </head>

  <body>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
	<br>
    <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
      <table  border="0" align="center" cellpadding="0" cellspacing="0">
        
        <tr>
          <td nowrap class="BGAccentVeryDarkBorder"><table  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="24" nowrap class="padding">Requester:</td>
                <td><select name="requester" id="requester">
                    <option value="1" <?= ($USER['requester'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['requester'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td width="200" height="24" nowrap class="padding"><?= $language['label']['app1']; ?>:</td>
                <td width="150"><select name="one" id="one">
                    <option value="1" <?= ($USER['one'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['one'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app2']; ?>:</td>
                <td><select name="two" id="two">
                    <option value="1" <?= ($USER['two'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['two'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app3']; ?>:</td>
                <td><select name="three" id="three">
                    <option value="1" <?= ($USER['three'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['three'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding"><?= $language['label']['app4']; ?>:</td>
                <td><select name="four" id="four">
                    <option value="1" <?= ($USER['four'] == '1') ? selected : $blank; ?>>Yes</option>
                    <option value="0" <?= ($USER['four'] == '0') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
              
              <tr>
                <td height="5" colspan="2" nowrap class="padding"><img src="../images/spacer.gif" width="10" height="5"></td>
              </tr>		
              			  	  
              <tr>
                <td height="24" nowrap class="padding">Group Access:</td>
                <td><select name="role" id="role">
                    <option>None</option>
                    <option value="purchasing" <?= ($USER['role'] == 'purchasing') ? selected : $blank; ?>>Purchasing</option>
                    <option value="executive" <?= ($USER['role'] == 'executive') ? selected : $blank; ?>>Executive</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding">Administration Access:</td>
                <td><select name="access" id="access">
                    <option value="0" <?= ($USER['access'] == '0') ? selected : $blank; ?>>None</option>
                    <option value="1" <?= ($USER['access'] == '1') ? selected : $blank; ?>>Level 1</option>
                    <option value="2" <?= ($USER['access'] == '2') ? selected : $blank; ?>>Level 2</option>
                    <option value="3" <?= ($USER['access'] == '3') ? selected : $blank; ?>>Level 3</option>
                </select></td>
              </tr>
              <tr>
                <td height="24" nowrap class="padding">Application Access:</td>
                <td><select name="status" id="status">
                    <option value="0" <?= ($USER['status'] == '0') ? selected : $blank; ?>>Yes</option>
                    <option value="1" <?= ($USER['status'] == '1') ? selected : $blank; ?>>No</option>
                </select></td>
              </tr>
			<tr>
			  <td height="5" nowrap><img src="../images/spacer.gif" width="10" height="5"></td>
			</tr>			  
			<tr>
			  <td nowrap>Delegation of Authority / Vacation:</td>
			  <td><select name="vacation" id="vacation">
				<option value="0">Off</option>
				<?php
				  $emp_sth = $dbh->execute($emp_sql);
				  while($emp_sth->fetchInto($EMPLOYEE)) {
					$selected = ($USER['vacation'] == $EMPLOYEE['eid']) ? selected : $blank;
					print "<option value=\"".$EMPLOYEE[eid]."\" ".$selected.">".caps($EMPLOYEE[lst].", ".$EMPLOYEE[fst])."</option>";
				  }
				?>
				</select></td>
			</tr>			  
          </table></td>
        </tr>
        <tr>
          <td height="5" nowrap><img src="../images/spacer.gif" width="10" height="5"></td>
        </tr>
        <tr>
          <td nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
                <td align="right"><input name="eid" type="hidden" id="eid" value="<?= $_GET['eid']; ?>">
                    <input name="action" type="hidden" id="action" value="update">
                    <input name="Done" type="image" class="button" id="Done" src="../images/button.php?i=b70.png&l=<?= $language['label']['update']; ?>" alt="<?= $language['label']['update']; ?>" border="0">
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