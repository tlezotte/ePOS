<?php
/**
 * Request System
 *
 * reminder_past.php sends out a reminder to requests older than >60.
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


$po_query = "SELECT p.id, p.purpose, p.req, p.reqDate, a.app1, a.app1Date, a.app2, a.app2Date, a.app3, a.app3Date, a.issuer, a.issuerDate,
			 TO_DAYS(NOW()) - TO_DAYS(p.reqDate) AS curReq,
			 TO_DAYS(NOW()) - TO_DAYS(a.app1Date) AS curApp1,
			 TO_DAYS(NOW()) - TO_DAYS(a.app1Date) AS curApp1Issuer,
			 TO_DAYS(NOW()) - TO_DAYS(a.app2Date) AS curApp2Issuer,
			 TO_DAYS(NOW()) - TO_DAYS(a.app3Date) AS curApp3Issuer
			FROM Authorization a, PO p
			WHERE p.id = a.type_id and p.reqDate < DATE_SUB(NOW(), INTERVAL 60 DAY) and p.po IS NULL and p.status <> 'C'
			ORDER BY p.reqDate DESC"; 
$po_sql = $dbh->prepare($po_query); 						
$po_sth = $dbh->execute($po_sql);
$num_rows = $po_sth->numRows();

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");
/* ------------- END DATABASE CONNECTIONS --------------------- */

/* ------------------ START FUNCTIONS ----------------------- */
function sendMail($sendTo,$PO_Level,$PO_ID,$PURPOSE,$DAYS,$REQUESTER) {
	global $default;
	
	$URL = "http://www.yourdomain.com/go/Request/PO/detail.php?id=".$PO_ID."&approval=".$PO_Level;
	$URL2 = "http://www.yourdomain.com/go/Request/PO/cancelSlip.php?id=".$PO_ID;
	$recipients = "tlezotte@Company.com";
	//$recipients = $sendTo;

	// ---------- Start Email Comment
	require("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($recipients);
	$mail->Subject = "PO Reminder: " . ucwords(strtolower($PURPOSE));
	$mail->Priority  =  1;		//High Priority

/* HTML message */				
$htmlBody = <<< END_OF_HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>$default[title1]</title>
</head>
<body>
<p><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></p>
<br>
This email is an automatic reminder that a<br>
Purchase Order Request needs review.<br>
The Request was submitted to you $DAYS days ago.<br>
The purpose for this PO is: $PURPOSE<br><br>
URL: $URL<br><br>
NOTE: If the Request is not valid anymore, contact<br>
$REQUESTER to cancel the Request.<br><br>
URL: $URL2;
</body>
</html>
END_OF_HTML;

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
	   echo "Message was not sent<br>";
	   echo "Mailer Error: " . $mail->ErrorInfo . "<br><br>";
	   echo "<a href=\"". $default['URL_HOME']. "\" class=\"dark\">" . $default['title1'] . "</a>";
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}
/* ------------------ END FUNCTIONS ----------------------- */

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $default['title1']; ?></title>
  <!-- InstanceEndEditable -->
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="../default.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?> 
	<script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <!-- InstanceBeginEditable name="head" -->  <!-- InstanceEndEditable -->
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body class="yui-skin-sam">  
    <img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- #BeginLibraryItem "/Library/help.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
<tr>
  <td width="30"><a href="../Common/calculator.php" onClick="window.open(this.href,this.target,'width=281,height=270'); return false;" <?php help('', 'Calculator', 'default'); ?>><img src="../images/xcalc.png" width="16" height="14" border="0"></a></td>
  <td><a href="../Help/index.php" rel="gb_page_fs[]"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
  <td class="DarkHeaderSubSub">&nbsp;<a href="../Help/index.php" rel="gb_page_fs[]" class="dark">Help</a></td>
</tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include('../include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['request_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="/Common/images/spacer.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<?php } else { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tr>
	<td>&nbsp;</td>
	<td><table cellspacing="0" cellpadding="0" summary="" border="0">
		<tr>
		  <td nowrap><a href="users.php" class="off"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="settings.php" class="off"> Settings </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>		  
		  <td nowrap><a href="db/index.php" class="off"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="utilities.php" class="off"> Utilities </a></td>			  			  
		  <td nowrap>&nbsp;</td>
		</tr>
	</table></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGGrayDark" rowspan="2"></td>
                <td class="BGGrayMedium" rowspan="2"></td>
                <td class="BGGrayLight" rowspan="2"></td>
              </tr>
              <tr>
                <td class="BGColorDark" width="100%"><?php 
				  	if (isset($_SESSION['username'])) {
				  ?>
                    <div align="right" class="FieldNumberDisabled">&nbsp;</div>
                  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                </td>
              </tr>
              <tr>
                <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3"></td>
                <td colspan="7"></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" -->    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td width="200" valign="top"><!-- #BeginLibraryItem "/Library/utilities.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="notify.php" class="dark">Notify Users by Email</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="notify_web.php" class="dark">Notify Users by Webs</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="testemail.php" class="dark">Send Test Email </a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="summary.php" class="dark">Usage Summary</a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="comments.php" class="dark">Comments</a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="reminder_past.php" class="dark">Send Past Reminders </a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="updateRSS.php" class="dark">Update  RSS </a></td>
                      </tr>
                    </table>
					<!--
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="javascript:void(0);" class="dark">ePOS</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection">&nbsp;&nbsp;&nbsp;<a href="../Administration/migrate.php" class="dark">Migrate Data </a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection">&nbsp;&nbsp;&nbsp;<a href="../Administration/epos_status.php" class="dark">Status</a></td>
            </tr>
          </table>--></td>
      <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --></td>
                  <td><br>
				  	<br>
					  <?php
						/* Dont display column headers and totals if no requests */
						if ($num_rows == 0) {
					  ?>
							<div align="center" class="DarkHeaderSubSub">No Requests Found</div>
					  <?php } else { ?>
                      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="BGAccentVeryDark"><div align="left">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Purchase Requests... </td>
                                    <td>&nbsp;</td>
                                  </tr>
                                </table>
                            </div></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder">
                              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td><table width="100%"  border="0">
                                      <tr>
                                        <td class="BGAccentDark"><strong>&nbsp;ID</strong></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Requested<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Approver 1 </strong></td>
                                        <td class="BGAccentDark"><div align="center"><strong>&nbsp;Approver 2 </strong></div></td>
                                        <td class="BGAccentDark"><div align="center"><strong>&nbsp;Approver 3 </strong></div></td>
                                        <td class="BGAccentDark"><strong>&nbsp;Issuer&nbsp;</strong></td>
                                      </tr>
                                      <?php
									/* Reset items total variable */
									$itemsTotal = 0;
									
									while($po_sth->fetchInto($PO)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									?>
                                      <tr <?php pointer($row_color); ?>>
                                        <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><a href="../PO/detail.php?id=<?= $PO[id]; ?>"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<?= $PO[id]; ?></td>
                                        <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($EMPLOYEES[$PO[req]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo $reqDate[0]; ?></td>
                                        <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($EMPLOYEES[$PO[app1]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[app2]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[app3]])); ?></td>
                                        <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[issuer]])); ?></td>
                                      </tr>
                                      <?php $itemsTotal += $PO[total]; ?>
                                      <?php } // End PO while ?>
                                  </table></td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td height="30" valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td valign="top">&nbsp;<span class="GlobalButtonTextDisabled"><?= $num_rows ?> Requests</span> </td>
                                  <td valign="bottom"><div align="right">
                                  <input name="stage" type="hidden" id="stage" value="process">
                                  <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Send" border="0">
                                  &nbsp;</div></td>
                                </tr>
                            </table></td>
                          </tr>
                        </table>
                      </form>
                      <?php } // End num_row if ?>
                      <br>
                  </td></tr>
              </tbody>
          </table></td>
        </tr>
      </tbody>
      </table>
    <!-- InstanceEndEditable --><br>
    <br>
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('../include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><?php include('../include/version.php'); ?><!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><?php if ($_SESSION['request_role'] == 'purchasing') { ?><a href="<?= $default['URL_HOME']; ?>/Help/chat.php" target="chat" onclick="window.open(this.href,this.target,'width=250,height=400'); return false;" id="meebo"><img src="/Common/images/meebo.gif" width="18" height="20" border="0" align="absmiddle">Company Chat</a><?php } ?><!-- InstanceEndEditable --></div>
			<div class="TrainVisited" id="noPrint"><?= onlineCount(); ?></div>
    	</td>
        </tr>
      </tbody>
  </table>
   <br>
  </body>
  <script>var request_id='<?= $_GET['id']; ?>';</script>
  <script type="text/javascript" src="/Common/js/scriptaculous/prototype-min.js"></script>
  <script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=builder,effects"></script>
  <script type="text/javascript" src="/Common/js/ps/tooltips.js"></script>  
  <!-- InstanceBeginEditable name="js" --><!-- InstanceEndEditable -->   
<!-- InstanceEnd --></html>
<?php 
/* ------------------ START DATA PROCESS ----------------------- */
if ($_POST['stage'] == 'process') {
	while($po_sth->fetchInto($PO)) {
		/* Calculate the remander */
		if (is_null($PO['app1Date'])) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[app1]"));
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));
			sendMail($APP['email'],'app1',$PO['id'],$PO['purpose'],$PO['curReq'],$REQ['name']);
		} else if (isset($PO['app2']) and is_null($PO['app2Date']) and $PO['curApp1'] >= 2 and $PO['curApp1'] % 2 == 0) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[app2]"));
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));	 						 
			sendMail($APP['email'],'app2',$PO['id'],$PO['purpose'],$PO['curApp1'],$REQ['name']);
		} else if (isset($PO['app2']) and is_null($PO['issuerDate']) and $PO['curApp2Issuer'] >= 2 and $PO['curApp2Issuer'] % 2 == 0) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[issuer]"));	
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));			 			 
			sendMail($APP['email'],'issuer',$PO['id'],$PO['purpose'],$PO['curApp2Issuer'],$REQ['name']);
		} else if ($PO['curApp1Issuer'] >= 2 and $PO[curApp1Issuer] % 2 == 0) {
			$APP = $dbh->getRow("SELECT eid, email
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[issuer]"));
			$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
								 FROM Standards.Employees
								 WHERE eid = ?", array("$PO[req]"));			 							  
			sendMail($APP['email'],'issuer',$PO['id'],$PO['purpose'],$PO['curApp1Issuer'],$REQ['name']);	
		}
	}
}
/* ------------------ END DATA PROCESS ----------------------- */

/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>
