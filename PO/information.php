<?php
/**
 * Request System
 *
 * information.php enduser enters information about PO.
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
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');


/* ---------------------------------------------------------------------
 * ------------------ START PAGE PROCESSING ----------------------- 
 * ---------------------------------------------------------------------
 */
if ($_POST['stage'] == "two") {
	
	/*  START FILE UPLOAD (FIRST HALF)  */
	$exp_file = explode(".",$_FILES['file']['name']);
	$file_ext = end($exp_file);
	/*  END FILE UPLOAD (FIRST HALF)  */

	/* Getting Vendor terms from Standards */
	$terms_sql = "SELECT terms_id AS id, terms_name AS name 
				  FROM Standards.Vendor v
				  	INNER JOIN Standards.VendorTerms t ON t.terms_id=v.BTTRMC
				  WHERE BTVEND='" . $_SESSION['supplier'] . "'";
	$TERMS = $dbh->getRow($terms_sql);	
	
	/* ---------------------------------------------------------------------
	 * ------------------ START DATABASE CONNECTIONS ----------------------- 
	 * ---------------------------------------------------------------------
	 */
	 $incareof = (strlen($_POST['ajaxName']) > 0) ? $_POST['incareof'] : '';			// Check to see if ajaxName was removed
	 
	/* ---------- Commiting data into PO database ---------- */
	$po_values = "'".mysql_real_escape_string($_SESSION['eid'])."',
				NOW(),			
				'".mysql_real_escape_string($incareof)."',
				'".mysql_real_escape_string($_POST['plant'])."',
				'".mysql_real_escape_string($_POST['ship'])."',
				'".mysql_real_escape_string($_SESSION['supplier'])."',
				'".mysql_real_escape_string($TERMS['id'])."',
				'".mysql_real_escape_string($_POST['job'])."',
				'".mysql_real_escape_string($_POST['company'])."',
				'".mysql_real_escape_string($_POST['department'])."',
				'".mysql_real_escape_string($_POST['purpose'])."',
				'".mysql_real_escape_string(htmlentities($_FILES['file']['name'], ENT_QUOTES, 'UTF-8'))."',
				'".mysql_real_escape_string(htmlentities($_FILES['file']['type'], ENT_QUOTES, 'UTF-8'))."',
				'".mysql_real_escape_string($file_ext)."',
				'".mysql_real_escape_string($_FILES['file']['size'])."',				
				'".mysql_real_escape_string($_SESSION['total'])."',
				'".mysql_real_escape_string($_POST['cer'])."',
				'".mysql_real_escape_string($_POST['hot'])."',
				'N',
				'".mysql_real_escape_string($_POST['date1'])."'
				";
	$po_sql = "INSERT into PO (req, reqDate, incareof, plant, ship, sup, terms, job, company, department, purpose, file_name, file_type, file_ext, file_size, total, cer, hot, status, dueDate) VALUES ($po_values)";
	$dbh->query($po_sql);
	
	/* ---------- Get PO auto_increment ID ---------- */							
	$PO_ID = $dbh->getOne("select max(id) from PO");
		
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($po_sql)));		// Record transaction for history
	}

	/* ---------- Record DofA transacation ---------- */
	if (strlen($_SESSION['app1']) == 10) {
		$fromEID=substr($_SESSION['app1'], 0, 5);
		$toEID=substr($_SESSION['app1'], 5, 5);
		$_SESSION['app1'] = $toEID;
		
		$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $PO_ID . "', 'app1', '" . $fromEID . "', '" . $toEID . "')";
		$dbh->query($sql);
		
		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
		}		
	}
	if (strlen($_SESSION['app2']) == 10) {
		$fromEID=substr($_SESSION['app2'], 0, 5);
		$toEID=substr($_SESSION['app2'], 5, 5);
		$_SESSION['app2'] = $toEID;
		
		$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $PO_ID . "', 'app2', '" . $fromEID . "', '" . $toEID . "')";
		$dbh->query($sql);
		
		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
		}		
	}
	if (strlen($_SESSION['app3']) == 10) {
		$fromEID=substr($_SESSION['app3'], 0, 5);
		$toEID=substr($_SESSION['app3'], 5, 5);
		$_SESSION['app3'] = $toEID;
		
		$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $PO_ID . "', 'app3', '" . $fromEID . "', '" . $toEID . "')";
		$dbh->query($sql);
		
		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
		}		
	}
	if (strlen($_SESSION['app4']) == 10) {
		$fromEID=substr($_SESSION['app4'], 0, 5);
		$toEID=substr($_SESSION['app4'], 5, 5);
		$_SESSION['app4'] = $toEID;
		
		$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $PO_ID . "', 'app4', '" . $fromEID . "', '" . $toEID . "')";
		$dbh->query($sql);
		
		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
		}		
	}
	
	/* ---------- Commiting data into Authorization database ---------- */
	$auth_fields = "NULL,
					'PO',
					'".mysql_real_escape_string($PO_ID)."',
					'".mysql_real_escape_string($_SESSION['app1'])."',
					'".mysql_real_escape_string($_SESSION['app2'])."',
					'".mysql_real_escape_string($_SESSION['app3'])."',
					'".mysql_real_escape_string($_SESSION['app4'])."'
					";													
 	$auth_sql = "INSERT into Authorization (id, type, type_id, app1, app2, app3, app4) VALUES ($auth_fields)";
	$dbh->query($auth_sql);
	
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($auth_sql)));		// Record transaction for history
	}
	
	/* ---------- Commiting data into Items database ---------- */
 	for ($i = 1; $i <= $_SESSION['total_items']; $i++) {
		$qty = 'qty'.$i;
		$unit = 'unit'.$i;
		$part = 'part'.$i;
		$manuf = 'manuf'.$i;
		$descr = 'descr'.$i;
		$price = 'price'.$i;
		$cat = 'cat'.$i;
		$vt = 'vt'.$i;
		$plant = 'plant'.$i;
		$items_fields = "NULL,
						'".mysql_real_escape_string($PO_ID)."',
						'".mysql_real_escape_string($_SESSION[$qty])."',
						'".mysql_real_escape_string($_SESSION[$descr])."',
						'".mysql_real_escape_string($_SESSION[$price])."',
						'".mysql_real_escape_string($_SESSION[$cat])."',
						'".mysql_real_escape_string($_SESSION[$unit])."',
						'".mysql_real_escape_string($_SESSION[$part])."',
						'".mysql_real_escape_string($_SESSION[$manuf])."',
						'".mysql_real_escape_string($_SESSION[$vt])."',
						'".mysql_real_escape_string($_SESSION[$plant])."',
						'N'
						";
		/* ---------- Only recording lines containing information ---------- */
		if (!empty($_SESSION[$descr])) {
			$items_sql = "INSERT into Items (id, type_id, qty, descr, price, cat, unit, part, manuf, vt, plant, rec) VALUES ($items_fields)";
			$dbh->query($items_sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($items_sql)));		// Record transaction for history
			}			
		}
	}
	
	/* ---------- Commiting data into Postings database ---------- */
	if (strlen($_POST['comment']) > 0) {
		$post_fields = "NULL,
						'".mysql_real_escape_string($PO_ID)."',
						'".mysql_real_escape_string($_SESSION['eid'])."',
						NOW(),
						'".mysql_real_escape_string($_POST['comment'])."'
						";													
		$post_sql = "INSERT into Postings (id, request_id, eid, posted, comment) VALUES ($post_fields)";
		$dbh->query($post_sql);
		
		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $PO_ID, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($post_sql)));		// Record transaction for history
		}		
	}
	/* ---------------------------------------------------------------------
	 * ------------------ END DATABASE CONNECTIONS ----------------------- 
	 * ---------------------------------------------------------------------
	 */
	
	
	/* ------------------ START FILE UPLOAD (SECOND HALF) ----------------------- */
	$store = $default['files_store'];								//Store uploaded files to this directory
	$dest = $store."/".$PO_ID.".".$file_ext;
	$source = $_FILES['file']['tmp_name'];
	if (file_exists($source)) {
		if (is_writable($default['PO_UPLOAD'])) {
			copy($source, $dest);							//Copy temp upload to $store
		} else {
			$_SESSION['error'] = "Cannot upload file (".$_FILES['file']['name'].")";
			$_SESSION['redirect'] = "http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];
			
			header("Location: ../error.php");
		}
	}
	/* ------------------ END FILE UPLOAD (SECOND HALF) ----------------------- */	
	clearSession();			// Reset Session

	
	/* ---------- Also send email to Purchasing Queue ---------- */
	if ($_POST['hot'] == 'yes') {
		sendPurchasingHOT($PO_ID, caps($_POST['purpose']));
	}	
	
	
	/* ---------- Send email to On Behalf ---------- */
	if ($_POST['onBehalfEmail'] == 'yes') {
		$sendTo = getEmployee($_POST['incareof']);
		$subject = "Requisition ".$PO_ID.": ".$purpose;
		$message_body  = "This new Purchase Requisition was submitted on your behalf.<br>";
		$message_body .= "The purpose for this Requisition is: <b>" . caps($_POST['purpose']) . "</b><br>";
		$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;		
		
		sendGeneric($sendTo['email'], $subject, $message_body, $url);
	}	
	
	
	/* ---------- Forward to router ---------- */
	/* -- Dont send to controllers for HQ, ITC and Processing Center -- */
	if ($_POST['plant'] == '9' OR $_POST['plant'] == '27' OR $_POST['plant'] == '32') {
		/* -- Dont send to controller if from HQ -- */
		$forward = "router.php?type_id=" . $PO_ID . "&approval=app0";
	} else {
		$forward = "router.php?type_id=" . $PO_ID . "&approval=controller&plant=" . $_POST['plant'] . "&department=" . $_POST['department'];
	}
	header("Location: " . $forward);
}
/* ---------------------------------------------------------------------
 * ------------------ END PAGE PROCESSING ----------------------- 
 * ---------------------------------------------------------------------
 */



/* ---------------------------------------------------------------------
 * ------------------ START DATABASE CONNECTIONS ----------------------- 
 * ---------------------------------------------------------------------
 */
$company_sql = $dbh->prepare("SELECT id, name 
						      FROM Standards.Companies 
						      WHERE id > 0 
							    AND status <> '1'
						      ORDER BY name");
$plants_sql = $dbh->prepare("SELECT id, name
						     FROM Standards.Plants
						     WHERE status = '0'
						     ORDER BY name");
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status = '0' 
						   ORDER BY name");
$cer_sql = $dbh->prepare("SELECT id, cer 
                          FROM CER 
					      WHERE cer IS NOT NULL 
					      ORDER BY (cer+0)");				
$comm_sql = $dbh->prepare("SELECT id, comment FROM Comment");	
$polist_sql = $dbh->prepare("SELECT DISTINCT(conbr) AS id, podb AS name 
							 FROM Standards.Plants 
							 WHERE status = '0'
							 ORDER BY name");	
$employees_sql = $dbh->prepare("SELECT eid, fst, lst 
								FROM Standards.Employees 
								WHERE status = '0'
								ORDER BY lst");						  													 				    
/* ---------------------------------------------------------------------
 * ------------------ END DATABASE CONNECTIONS ----------------------- 
 * ---------------------------------------------------------------------
 */


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS2.="init();";
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
  <script type="text/javascript" src="/Common/js/jquery/jquery-min.js"></script>
  <!-- InstanceBeginEditable name="head" -->
  <script type="text/javascript" src="/Common/js/jquery/ui/ui.datepicker-min.js"></script>  
  
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css">	  
			
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">
  
    <script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
    </script>
  <!-- InstanceEndEditable -->
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><?php include('../include/menu/main_left.php'); ?><!-- InstanceEndEditable --></td>
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
    <!-- InstanceBeginEditable name="main" --><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><br>
				  <div id="noPrint">
                    <table  border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                        <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                        <td><a href="items.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                        <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                        <td><a href="authorization.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                        <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                        <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                        <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                      </tr>
                      <tr>
                        <td colspan="9"><table width="100%"  border="0">
                            <tr>
                              <td width="15%" class="wizardPast">Vendor</td>
                              <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Items</div></td>
                              <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Authorization</div></td>
                              <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Information</div></td>
                              <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                            </tr>
                        </table></td>
                      </tr>
                    </table>
				  </div>
                    <br>
					<br>
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="Form" id="Form" onSubmit="submitonce(this)" runat="vdaemon">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="BGAccentVeryDark"><div align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Information...</td>
                                        <td width="50%"><div align="left"> </div></td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td colspan="2"><div id="hotMessage">
										  Tag Requisition as HOT:
										  <select name="hot" id="hot" onChange="$('hotMessageWarning').toggle();">
                                            <option value="no">No</option>
                                            <option value="yes">Yes</option>
                                          </select>
										  <div id="hotMessageWarning" style="display:none">
											    This option should only be used on very importent<br> 
											    Requisitions that need to be looked at ASAP.<br>
											    <br>
												This option will email the first Approver and Purchasing.<br>
												<br>
												Not for  general Requisitions!
									    </div>
                                      </div></td>
                                    </tr>
                                    <tr>
                                      <td><vllabel form="Form" validators="company" class="valRequired2" errclass="valError">Company:</vllabel></td>
                                      <td><span class="error">
									  <select name="company">
                                          <option value="0">Select One</option>
                                          <?php
										  $company_sth = $dbh->execute($company_sql);
										  while($company_sth->fetchInto($COMPANY)) {
											if (isset($_SESSION['company'])) {
											  $selected = ($_SESSION['company'] == $COMPANY[id]) ? selected : $blank;
											} else {
											  $selected = ($COMPANY['id'] == '4') ? selected : $blank;
											}
											print "<option value=\"".$COMPANY[id]."\" ".$selected.">".ucwords(strtolower($COMPANY[name]))."</option>\n";
										  }
										  unset($selected);
										  ?>
                                      </select>
                                      <vlvalidator name="company" type="compare" control="company" validtype="string" comparevalue="0" comparecontrol="company" operator="ne">
                                      </span></td>
                                    </tr>
                                    <tr>
                                      <td><vllabel form="Form" validators="plant" class="valRequired2" errclass="valError">Bill To Plant:</vllabel></td>
                                      <td><span class="error">
										<select name="plant" id="plant">
											  <option value="0">Select One</option>
											  <?php
											  $ship_sth = $dbh->execute($plants_sql);
											  while($ship_sth->fetchInto($PLANT)) {
												$selected = ($_SESSION['ship'] == $PLANT[id]) ? selected : $blank;
												print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
											  }
											  ?>
										  </select>
                                        <vlvalidator name="plant" type="compare" control="plant" validtype="string" comparevalue="0" comparecontrol="plant" operator="ne">
                                      </span></td>
                                    </tr>									
                                    <tr>
                                      <td><vllabel form="Form" validators="ship" class="valRequired2" errclass="valError">Deliver To Plant:</vllabel></td>
                                      <td><span class="error">
										<select name="ship" id="ship">
											  <option value="0">Select One</option>
											  <?php
											  $ship_sth = $dbh->execute($plants_sql);
											  while($ship_sth->fetchInto($PLANT)) {
												$selected = ($_SESSION['ship'] == $PLANT[id]) ? selected : $blank;
												print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
											  }
											  ?>
										  </select>
                                        <vlvalidator name="ship" type="compare" control="ship" validtype="string" comparevalue="0" comparecontrol="ship" operator="ne">
                                      </span></td>
                                    </tr>
                                    
                                    <tr>
                                      <td class="valNone">On Behalf Of: </td>
                                      <td><input id="ajaxName" name="ajaxName" type="text" size="40" />
											<script type="text/javascript">
												Event.observe(window, "load", function() {
													var aa = new AutoAssist("ajaxName", function() {
														return "../Common/employees.php?q=" + this.txtBox.value;
													});
												});
											</script>
                                      <input name="incareof" type="hidden" id="ajaxEID">
                                      <input name="onBehalfEmail" type="checkbox" id="onBehalfEmail" value="yes">
                                      <label id="onBehalfEmail" <?php help('', 'Select checkbox to inform the <b>On Behalf Of</b> employee', 'default') ?> class="DataText">Inform by Email</label></td>
                                    </tr>
                                    
                                    <tr>
                                      <td class="valNone">Job Number:&nbsp;</td>
                                      <td><input name="job" type="text" id="job" size="15" maxlength="15" value="<?= $_SESSION['job']; ?>"></td>
                                    </tr>
                                    <tr>
                                      <td><vllabel form="Form" validators="department" class="valRequired2" errclass="valError">Department:</vllabel></td>
                                      <td><span class="error">
									<select name="department" id="department">
                                          <option value="0">Select One</option>
                                          <?php
										  $dept_sth = $dbh->execute($dept_sql);
										  while($dept_sth->fetchInto($DEPT)) {
											if (isset($_SESSION['dept'])) {
											  $selected = ($_SESSION['dept'] == $DEPT[id]) ? selected : $blank;
											}
											print "<option value=\"".$DEPT[id]."\" ".$selected.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
										  }
										  ?>
                                      </select>
                                        <vlvalidator name="department" type="compare" control="department" validtype="string" comparevalue="0" comparecontrol="department" operator="ne">
                                      </span></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">Due Date:</td>
                                      <td><input name="date1" type="text" id="date1" size="10" maxlength="10" class="popupcalendar" value="<?= $_SESSION['date1']; ?>"></td>
                                    </tr>
                                    <tr>
                                      <td nowrap><vllabel form="Form" validators="purpose" class="valRequired2" errclass="valError">Purpose / Usage:</vllabel></td>
                                      <td><input name="purpose" type="text" id="purpose" value="<?= $_SESSION['purpose']; ?>" size="75" maxlength="100">
                                      <vlvalidator name="purpose" type="required" control="purpose" minlength="10" maxlength="100"></td>
                                    </tr>
                                    <tr>
                                      <td valign="top" nowrap class="valNone">Detailed Comments: </td>
                                      <td><textarea name="comment" cols="50" rows="10" id="comment"></textarea></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">Quote:</td>
                                      <td><input name="file" type="file" size="38"></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">Capital Acquisition:&nbsp;</td>
                                      <td><select name="cer">
                                          <option value="0">Select One</option>
                                          <?php
										  $cer_sth = $dbh->execute($cer_sql);
										  while($cer_sth->fetchInto($CER)) {
											if (isset($_SESSION['cer'])) {
											  $selected = ($_SESSION['cer'] == $CER['id']) ? selected : $blank;
											}
											print "<option value=\"".$CER['id']."\" ".$selected.">".ucwords(strtolower($CER['cer']))."</option>\n";
										  }
										  ?>
                                        </select>
                                      <a href="cer_list.php" <?php help('', 'Click here to get a list of approved Capital Expenditure Requests', 'default'); ?> rel="gb_page_fs[]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                    </tr>
                                </table></td>
                              </tr>
                              <tr>
                                <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                              </tr>
                              <tr>
                                <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><a href="authorization.php">&nbsp;<img src="../images/button.php?i=b70.png&l=Back" border="0"></a></td>
                                    <td><div align="right">
                                      <input name="stage" type="hidden" id="stage" value="two">
                                      <input name="done" id="done" type="image" src="../images/button.php?i=b70.png&l=Done" border="0">&nbsp;</div></td>
                                  </tr>
                                </table></td>
                              </tr>
                            </table>
                    </form>
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
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><?php if ($_SESSION['request_role'] == 'purchasing') { ?><a href="<?= $default['URL_HOME']; ?>/Help/chat.php" target="chat" onClick="window.open(this.href,this.target,'width=250,height=400'); return false;" id="meebo"><img src="/Common/images/meebo.gif" width="18" height="20" border="0" align="absmiddle">Company Chat</a><?php } ?>
<script>
jQuery(document).ready(function(){
	/* ===== jQuery UI Calendar ===== */
	jQuery.datepicker.setDefaults({showOn: 'both', buttonImageOnly: true, dateFormat: 'YMD-', buttonImage: '/Common/images/calendar.gif'});
	jQuery('input.popupcalendar').datepicker();
});						
</script>		  
		  
		  <!-- InstanceEndEditable --></div>
			<div class="TrainVisited" id="noPrint"><?= onlineCount(); ?></div>
    	</td>
        </tr>
      </tbody>
  </table>
   <br>
  </body>
  <script>var request_id='<?= $_GET['id']; ?>';</script>
  <!-- InstanceBeginEditable name="js" --><!-- InstanceEndEditable -->   
<!-- InstanceEnd --></html>


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