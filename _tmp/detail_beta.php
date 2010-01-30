<?php
/**
 * Request System
 *
 * detail.php displays detailed information on PO.
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
 * PDF Toolkit
 * @link http://www.accesspdf.com/
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
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Form Validation
 */
//include('vdaemon/vdaemon.php');


/* --------- START PURCHASING AREA ----------------------- */
if ($_POST['action'] == 'purchasing_update') {
	/* --- Adding supplier contact information --- */
	if (array_key_exists('addContact_x',$_POST)) {
		$sql = "INSERT INTO Contacts VALUES (NULL, 
											  '" . $_POST['id'] . "',
											  '" . $_SESSION['eid'] . "',
											  NOW(),
											  '" . $_POST['cc_name'] . "',
											  '" . $_POST['cc_phone'] . "',
											  '" . $_POST['cc_ext'] . "',
											  '" . $_POST['cc_email'] . "'
											  )";
		$dbh->query($sql);										   

		header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
		exit();	
	}
	
	/* --- Adding payment information --- */
	if (array_key_exists('addPayment_x',$_POST)) {
		$sql = "INSERT INTO Payments VALUES (NULL, 
											   '" . $_POST['id'] . "', 
											   '" . $_SESSION['eid'] . "',
											   NOW(),
											   '" . mysql_real_escape_string($_POST['pay_amount']) . "',
											   '" . mysql_real_escape_string($_POST['paymentDate']) . "',
											   '0')";	
		$dbh->query($sql);										   

		header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
		exit();	
	}
	
	/* --- Update payment information --- */
	if (array_key_exists('updatePayments_x',$_POST)) {
		for ($i=1; $i <= $_POST['payments_count']; $i++) {
			$pay_id = "pay_id" . $i;
			$pay_amount = "pay_amount" . $i;
			$pay_date = "pay_date" . $i;
			$pay_remove = "pay_remove" . $i;
			$pay_status = ($_POST[$pay_remove] == 'yes') ? 1 : 0;
			
			$sql = "UPDATE Payments SET pay_eid = '" . $_SESSION['eid'] . "',
										pay_recorded = NOW(),
										pay_amount = '" . mysql_real_escape_string($_POST[$pay_amount]) . "',
										pay_date = '" . mysql_real_escape_string($_POST[$pay_date]) . "',
										pay_status = '" . $pay_status . "'
									WHERE pay_id = " . $_POST[$pay_id];	
			$dbh->query($sql);
		}										   
		
		header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
		exit();	
	}	
		
	/* --- Purchaser marks Request as Complete --- */
	if (array_key_exists('completed_x',$_POST)) {
		/* Record Purchasing agent */
		$auth_sql = "UPDATE Authorization SET issuer='" . $_SESSION['eid'] . "', 
							  			 	  issuerDate=NOW()
						  			 	  WHERE id = " . $_POST['auth_id'];
		$dbh->query($auth_sql);

		/* Update Request Status */
		$po_sql = "UPDATE PO SET po='" . $_POST['po'] . "',
								 status='O' 
							 WHERE id = " . $_POST['id'];
		$dbh->query($po_sql);		
		
		sendCompleted($_POST['req'],$_POST['id'],htmlspecialchars($_POST['purpose']),$_POST['po'], $_SESSION['eid']);		// Send email to Requester
		//sendVendor($_POST['id']);
	
		header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
		exit();
	}
	
	/* Add Vendor and change Terms when Purchasing changes Vendor */
	if (strlen($_POST['vendSearch']) > 0) {
		$supplier2  = "sup2='" . $_POST['supplierNew'] . "',";
		$supplier2 .= "terms='" . $_POST['supplierNewTerms'] . "',";
	} else {
		$supplier2  = "terms='".$_POST['terms']."',";
	}
	
	/* Creating update fields */
	$PO = "fob='".$_POST['fob']."',
		   via='".$_POST['via']."',
		   update_status=NOW(),
		   $supplier2
		   purchaserUpdate='" . $_POST['purchaserUpdate'] . "'
		   ";												
	$sql = "UPDATE PO SET $PO WHERE id = ".$_POST['id'];
	$dbh->query($sql);

	History($_SESSION['eid'], 'update', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));				// Record transaction for history

	header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
	exit();	
}
/* --------- END PURCHASING AREA ----------------------- */

						
/* ------------- START APPROVAL PROCESSING --------------------- */
if ($_POST['stage'] == "update") {
	if ($_POST['auth'] == "req") {

		/* -------------------------------------------------------------
		 * ---------------- START CANCEL PURCHASE ORDER ---------------- 
		 * -------------------------------------------------------------
		 */	
		if ($_POST['cancel'] == 'yes') {
			setRequestStatus($_POST['type_id'], 'C');		// Update PO status
			
			header("location: list.php?action=my&access=0");
			exit();
		}
		/* --------- END CANCEL PURCHASE ORDER ----------------------- */

		/* -------------------------------------------------------------
		 * ---------------- START COPY PURCHASE ORDER ---------------- 
		 * -------------------------------------------------------------
		 */			
		if (array_key_exists('copyrequest_x',$_POST)) {
			/* Getting PO information */
			$PO = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate
								FROM PO
								WHERE id = ?",array($_POST['type_id']));	

			/* -- Read $PO into $_SESSION -- */
			foreach ($PO as $key => $value) {
				$_SESSION[$key] = $value;
			}
			
			header("location: information.php");
			exit();	
		}
		/* --------- END COPY PURCHASE ORDER ----------------------- */
	}

	/* -------------------------------------------------------------
	 * ---------- START APP1 and APP2 APPROVAL PROCESSING ---------- 
	 * -------------------------------------------------------------
	 */
	if (substr($_POST['auth'],0,3) == 'app') {
		$yn = (array_key_exists('yes_x', $_POST)) ? yes : no;			// Set approval button pressed

		/* Check to see if a Comment was provided */
		if (empty($_POST['Com']) AND $yn == 'no') {
			$_SESSION['error'] = "A denied Requisition requires you to enter a Comment.";
			$_SESSION['redirect'] = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			
			header("location: ../error.php");
			exit();				
		}
			
		/* Change status for non approved request */
		if ($yn == 'no') {
		  setRequestStatus($_POST['type_id'], 'X');		// Update PO status
		}
	
		/* Update the approvals for the PO */
		$dbh->query("UPDATE Authorization 
					 SET ".$_POST['auth']."yn='" . $yn . "', 
					       ".$_POST['auth']."Date=NOW(), 
						   ".$_POST['auth']."Com='" . htmlentities($_POST['Com'], ENT_QUOTES, 'UTF-8') . "', 
						   app2='".$_POST['app2']."',
						   app3='".$_POST['app3']."',
						   app4='".$_POST['app4']."'
					 WHERE id = ".$_POST['auth_id']);
		
		header("location: router.php?type_id=" . $_POST['type_id'] . "&approval=" . $_POST['auth'] . "&yn=" . $yn);
		exit();
	}
	/* ------------- END APP1 and APP2 APPROVAL PROCESSING -------------- */
}
/* ------------------ END APPROVAL STAGE ----------------------- */


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting PO information */
$PO = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate
				    FROM PO
				    WHERE id = ?",array($_GET['id']));
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees");
/* Getting Vendor information from Standards */							  
$VENDOR = $dbh->getAssoc("SELECT BTVEND, BTNAME FROM Standards.Vendor");
/* Getting Company information from Standards */	
//$COMPANY = $dbh->getAssoc("SELECT id, name FROM Standards.Companies");
/* Getting Department information from Standards */	
$DEPARTMENT = $dbh->getAssoc("SELECT id, name FROM Standards.Department");	
/* Getting Category information from Standards */	
//$COA = $dbh->getAssoc("SELECT coa_id AS id, coa_description AS name FROM Standards.COA WHERE coa_plant='" . $PO['plant'] . "'");
/* Getting CER numbers from CER */							 						 
$CER = $dbh->getAssoc("SELECT id, cer FROM CER WHERE cer IS NOT NULL ORDER BY cer");
/* Getting Category information from Standards */	
$CAT = $dbh->getAssoc("SELECT id, name FROM Standards.Category WHERE status = '0'");
/* Getting Plant information from Standards */	
$PLANT = $dbh->getAssoc("SELECT id, name FROM Standards.Plants WHERE status = '0'");
/* Getting Vendor terms from Standards */
$terms_sql = "SELECT terms_id AS id, terms_name AS name FROM Standards.VendorTerms ORDER BY name";
$TERMS = $dbh->getAssoc($terms_sql);
$terms_query = $dbh->prepare($terms_sql);
				  
/* Get items related to this Request */
$items_sql = $dbh->query("SELECT * FROM Items WHERE type_id = ".$PO['id']."");
/* Get Purchase Request users */
$purchaser_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst, E.email 
								 FROM Users U
								   INNER JOIN Standards.Employees E ON U.eid = E.eid
								 WHERE  U.group = 'purchasing' 
								   AND E.status = '0'
								   AND U.eid <> '08745'
								 ORDER BY E.lst ASC");
/* Get Vendor Payments */
$payments = $dbh->query("SELECT * FROM Payments WHERE request_id=" . $PO['id'] . " AND pay_status='0' ORDER BY pay_date ASC");
$payments_count = $payments->numRows();
/* Get Contact Information */
$contacts = $dbh->query("SELECT * FROM Contacts WHERE request_id=" . $PO['id'] . " ORDER BY id DESC");
$contacts_count = $contacts->numRows();
/* Getting Comments Information */

$post_sql = "SELECT * FROM Postings 
			 WHERE request_id = ".$_GET['id']." 
			   AND type = 'global'
			 ORDER BY posted DESC";
$LAST_POST = $dbh->getRow($post_sql);		// Get the last posted comment
$post_query = $dbh->prepare($post_sql);						   
$post_sth = $dbh->execute($post_query);
$post_count = $post_sth->numRows();	
							 																	 				  	
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

$highlight='class="highlight"';								// Highlighted style sheet class

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
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
  <!-- InstanceBeginEditable name="head" -->
	<script type="text/javascript" src="/Common/js/pointers.js"></script>
  
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/calendarmws.js"></SCRIPT>
	
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css">	
    
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">

	<script language="javascript">
		AJS.AEV(window, "load", function() { 
			new Effect.Pulsate('hotMessage', {delay:2, duration:5});
			new Effect.Shake('messageCenter', {delay:8});
		});
	</script>
    <script type="text/javascript" src="../js/dynamicInputItems.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= $default['URL_HOME']; ?>/style/dd_tabs.css" />
    <style type="text/css">
	form.inplaceeditor-form a {
		color:#FFFFFF;
	}
    </style>
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
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" -->
            <?php include('../include/menu/main_right.php'); ?>
          <!-- InstanceEndEditable --></td>

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
    <!-- InstanceBeginEditable name="main" -->    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td>
				  <div id="hotMessage" align="center" style="width: 700px;display:<?= ($PO['hot'] == 'yes') ? display : none; ?>" onMouseOver="new Effect.Pulsate(this);">This Requisition has been tagged HOT!!</div>	
				  <div id="messageCenter" align="center" style="width: 700px;display:<?= ($PO['message'] == 'yes') ? display : none; ?>"><?= $message; ?></div>	
                    <br>
                        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td>
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td></td>
                                      <td height="26" valign="bottom">
                                        <table border="0" align="right" cellpadding="0" cellspacing="0">
                                          <tr>
										  <?php if ($_SESSION['eid'] == $PO['req'] and ! empty($PO['po'])) { ?>
                                            <td><div id="ddcolortabs">
                                              <ul>
                                                <li id="inactive"><a href="checkInformation.php?id=<?= $PO['id']; ?>" <?php help('', 'Request Purchasing to send Vendor a check.', 'default'); ?> rel="gb_page_center[750, 450]"><span>Check&nbsp;Request</span></a></li>
                                              </ul>
                                            </div></td>
                                            <td>
                                                <div id="ddcolortabs">
                                                  <ul>
                                                    <li id="inactive"><a href="packingSlip.php?id=<?= $_GET['id']; ?>&po=<?= $PO['po']; ?>&req=<?= $PO['req']; ?>&issuer=<?= $AUTH['issuer']; ?>" <?php help('', 'Send electronic packing slip to Puchasing', 'default'); ?> rel="gb_page_center[500, 300]"><span>Packing&nbsp;Slip</span></a></li>
                                                  </ul>
                                                </div>
                                            </td>
											<?php } ?>
                                            <td><div id="ddcolortabs">
                                                <ul>
                                                  <li id="inactive"><a href="javascript: window.print();" <?php help('', 'Click here to print this Request', 'default'); ?>><span>Print&nbsp;Requisition</span></a></li>
                                                </ul>
                                            </div></td>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                    <tr class="BGAccentVeryDark">
                                      <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;Purchase Order Requisition...</td>
                                      <td width="50%"></td>
                                    </tr>
                                  </table>
                              </td>
                            </tr>
                            <tr>
                              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                        <tr>
                                          <td height="25" colspan="4" class="BGAccentDark"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                <td><img src="../images/info.png" width="16" height="16" align="texttop"><strong>&nbsp;Information</strong></td>
                                                <td><div align="right" class="mainsection">Status: <?= reqStatus($PO['status']); ?>
                                                  <input type="hidden" name="status" value="<?= $PO['status']; ?>">
                                                &nbsp;&nbsp;</div></td>
                                              </tr>
                                            </table></td>
                                        </tr>
                                        <tr>
                                          <td nowrap>Requisition Number:</td>
                                          <td class="label"><?= $_GET['id']; ?></td>
                                          <td nowrap>&nbsp;</td>
                                          <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                          <td width="12%" nowrap>Purchase Order  Number:</td>
                                          <td width="45%" class="label"><?= $PO['po']; ?></td>
                                          <td width="13%" nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td nowrap>CER Number:</td>
                                              <td width="35" align="right"><a href="cer_list.php" title="Capital Acquisition List" rel="gb_page_center[700, 600]" <?php help('', 'Click here to get a list of approved Capital Expenditure Requests', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                            </tr>
                                          </table>                                          </td>
                                          <td width="26%"><table border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                <td class="label"><?= $CER[$PO['cer']]; ?></td>
                                              <td><?php if (!empty($PO['cer'])) { ?>
												<a href="<?= $default['URL_HOME']; ?>/CER/print.php?id=<?= $PO['cer']; ?>" <?php help('', 'Click here to print this Capital Expenditure Request', 'default'); ?>><img src="../images/printer.gif" border="0" align="absmiddle"></a>
												<?php } ?></td>
                                              </tr>
                                            </table></td>
                                        </tr>
                                        <tr>
                                          <td>Requisitioner:</td>
                                          <td class="label"><?= caps($EMPLOYEES[$PO['req']]); ?></td>
                                          <td nowrap>Requisition Date:</td>
                                          <td class="label"><?= $PO['_reqDate']; ?></td>
                                        </tr>
										<?php if (!empty($PO['incareof']) AND $PO['req'] != $PO['incareof']) { ?>
                                        <tr>
                                          <td><img src="/Common/images/menupointer2.gif" width="4" height="7" align="absmiddle"> In Care Of:</td>
                                          <td class="label"><?= caps($EMPLOYEES[$PO['incareof']]); ?></td>
                                          <td>&nbsp;</td>
                                          <td>&nbsp;</td>
                                        </tr>
										<?php } ?>
                                        <tr>
                                          <td height="5" colspan="4"><img src="../images/spacer.gif" width="5" height="5"></td>
                                        </tr>
										<?php if (!is_null($PO['sup2'])) { ?>
                                        <tr>
                                          <td nowrap>Final Vendor: </td>
                                          <td class="label"><?= caps($VENDOR[$PO['sup2']]) . " (" . strtoupper($PO['sup2']) . ")"; ?><?php
										  /* Getting suppliers from Suppliers */						 
										  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web
																    FROM Standards.Vendor
																    WHERE BTVEND = '".$PO['sup2']."'");		
							   			  ?><span class="padding"><a href="http://maps.google.com/maps?q=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>&om=1" title="Map <?= caps($SUPPLIER['name']); ?>'s location" rel="gb_page_fs[]">&nbsp;<img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle"></a><?php if (!empty($SUPPLIER['web'])) { ?>&nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a><?php } ?>&nbsp;<a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle"></a></span></td>
                                          <td>Kickoff Date:</td>
                                          <td class="label"><?= ($AUTH['issuerDate'] == '0000-00-00 00:00:00' OR is_null($AUTH['issuerDate'])) ? $blank : date("F j, Y", strtotime($AUTH['issuerDate'])); ?></td>
                                        </tr>
										<?php } ?>										
                                        <tr>
                                          <td nowrap><?= (is_null($PO['sup2']) AND empty($PO['po'])) ? Recommended : Final; ?> Vendor:</td>
                                          <td class="label"><?= caps($VENDOR[$PO['sup']]) . " (" . strtoupper($PO['sup']) . ")"; ?><?php
										  /* Getting suppliers from Suppliers */						 
										  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web
																    FROM Standards.Vendor
																    WHERE BTVEND = '".$PO['sup']."'");		
							   			  ?><span class="padding"><a href="http://maps.google.com/maps?q=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>&om=1" title="Map <?= caps($SUPPLIER['name']); ?>'s location" rel="gb_page_fs[]">&nbsp;<img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle"></a><?php if (!empty($SUPPLIER['web'])) { ?>&nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a><?php } ?>&nbsp;<a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle"></a></span></td>
										  <?php if (is_null($PO['sup2']) AND !empty($PO['po'])) { ?>
                                          <td>Kickoff Date:</td>
                                          <td class="label"><?= ($AUTH['issuerDate'] == '0000-00-00 00:00:00' OR is_null($AUTH['issuerDate'])) ? $blank : date("F j, Y", strtotime($AUTH['issuerDate'])); ?></td>
										  <?php } else { ?>
										  <td>&nbsp;</td>
										  <td>&nbsp;</td>
										  <?php } ?>
                                        </tr>
                                        <tr>
                                          <td>Company:</td>
                                          <td class="label"><?= caps($COMPANY[$PO[company]]); ?></td>
                                          <td nowrap>&nbsp;</td>
                                          <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                          <td>Bill to Plant: </td>
                                          <td class="label"><?= caps($PLANT[$PO['plant']]); ?></td>
                                          <td>Deliver to Plant: </td>
                                          <td class="label"><?= caps($PLANT[$PO['ship']]); ?></td>
                                        </tr>
                                        <tr>
                                          <td>Department:</td>
                                          <td class="label"><?= '(' . $PO['department'] . ') ' . caps($DEPARTMENT[$PO['department']]); ?></td>
                                          <td>Job Number: </td>
                                          <td><span class="label">
                                            <?= $PO['job']; ?>
                                          </span></td>
                                        </tr>
                                        
                                        <tr>
                                          <td height="5" colspan="4"><img src="../images/spacer.gif" width="5" height="5"></td>
                                        </tr>
										<tr>
                                          <td valign="top" nowrap>Purpose / Usage:</td>
                                          <td colspan="3" class="label"><?= stripslashes($PO['purpose']); ?></td>
                                        </tr>
                                    </table></td>
                                  </tr>
								  
                                  <tr>
                                    <td>&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td class="BGAccentDarkBorder"><table width="100%" border="0">
                                      <tr>
                                        <td width="100%" height="25" colspan="6" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td>&nbsp;<a href="javascript:switchTracking();" class="black" <?php help('', 'Show or Hide the Track Shipments', 'default'); ?>><strong><img src="../images/package.gif" width="16" height="16" border="0" align="texttop">&nbsp;Track Shipments </strong></a></td>
                                              <td width="120">&nbsp;</td>
                                            </tr>
                                        </table></td>
                                      </tr>
									  <td>&nbsp;</td>
                                    </table>
                                    </td>
                                  </tr>
								  
                                  <tr>
                                    <td>&nbsp;</td>
                                  </tr>
								  <tr><td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td height="25" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td>&nbsp;<a href="javascript:void(0);" onClick="new Effect.toggle('items','blind')" class="black" <?php help('', 'Show or Hide the Item Information', 'default'); ?>><strong><img src="../images/text.gif" width="16" height="16" border="0" align="texttop">&nbsp;Item Information</strong></a></td>
                                            <td width="160"><!--<a href="javascript:void(0);" class="viewcomments">View All Details </a>--></td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                    <tr>
                                      <td>
									  <div id="items">
									  <table width="100%"  border="0">
                                        <tr>
                                          <td width="35" class="HeaderAccentDark">&nbsp;</td>
                                          <td width="35" class="HeaderAccentDark">Unit</td>
                                          <td width="80" nowrap class="HeaderAccentDark">Company#&nbsp;</td>
                                          <td width="60" nowrap class="HeaderAccentDark">Manuf#&nbsp;</td>
                                          <td class="HeaderAccentDark">Item Description</td>
                                          <td width="50" nowrap class="HeaderAccentDark">Price</td>
                                        </tr>
										<?php
										while($items_sql->fetchInto($ITEMS)) {
											$count_items++;
											$row_color = ($count_items % 2) ? FFFFFF : DFDFBF;
										?>
<!-- Start of Item<?= $count_items; ?> -->
                                        <tr <?php pointer($row_color); ?>>
                                          <td class="label" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td><a href="javascript:switchItems('itemDetails<?= $count_items; ?>', 'collapsed');" id="itemDetails<?= $count_items; ?>A" <?php help('', 'View more details on this item', 'default'); ?>><img src="../images/1rightarrow.gif" name="itemDetails<?= $count_items; ?>I" width="16" height="16" border="0" id="itemDetails<?= $count_items; ?>I">
                                                <input name="item<?= $count_items; ?>" type="hidden" id="item<?= $count_items; ?>" value="<?= $ITEMS['id']; ?>">
                                              </a></td>
                                              <td><strong><?= $ITEMS['qty']; ?></strong></td>
                                            </tr>
                                          </table></td>
                                          <td class="label" bgcolor="#<?= $row_color; ?>"><?= strtoupper($ITEMS['unit']); ?></td>
                                          <td class="label" bgcolor="#<?= $row_color; ?>"><?= strtoupper(stripslashes($ITEMS['part'])); ?></td>
                                          <td class="label" bgcolor="#<?= $row_color; ?>"><?= strtoupper(stripslashes($ITEMS['manuf'])); ?></td>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="label">
										  <?php
										  	if (strlen($ITEMS['descr']) > 50) {
												echo caps(substr(htmlspecialchars(stripslashes($ITEMS['descr'])), 0, 50));
												echo "...<a href=\"javascript:void(0);\" class=black onmouseover=\"return overlib('" . caps(htmlspecialchars(stripslashes($ITEMS['descr']))) . "', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#000000', CGCOLOR, '#E68B2C', FGCOLOR, '#B0D585');\" onmouseout=\"nd();\"><img src=\"../images/bubble.gif\" width=14 height=17 border=0 align=absmiddle></a>";
											} else {
												echo caps(stripslashes($ITEMS['descr']));
											}
											?></td>
                                          <td class="label" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td><strong>$</strong></td>
                                              <td align="right"><strong><?= number_format($ITEMS['price'],2); ?></strong></td>
                                            </tr>
                                          </table></td>
                                        </tr>
                                        <tr <?php pointer($row_color); ?> id="itemDetails<?= $count_items; ?>" style="display:none">
                                          <td colspan="6" bgcolor="#<?= $row_color; ?>" class="label"><table width="100%" border="0">
										  <!--
                                            <tr>
                                              <td width="200">Supplier Delevery Date: </td>
                                              <td>&nbsp;</td>
                                              <td width="200">&nbsp;</td>
                                              <td>&nbsp;</td>
                                              </tr>
                                            <tr>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                            </tr>
											-->
                                            <tr>
                                              <td width="200">Category Name: </td>
                                              <td class="label"><?= caps($CAT[$ITEMS['cat']]); ?></td>
                                              <td width="200">Category Number: </td>
                                              <td class="label"><?= $ITEMS['cat']; ?></td>
                                            </tr>
                                            <tr>
                                              <td>Company Tool Number: </td>
                                              <td class="label"><?= $ITEMS['vt']; ?></td>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                            </tr>
                                          </table></td>
                                        </tr>
<!-- End of Item<?= $count_items; ?> -->
                                        <?php } ?>
									    <tr><td colspan="5" align="right" class="xpHeaderBottomActive">Total: </td>
									    <td class="xpHeaderBottomActive"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td style="font-weight:bold">$</td>
                                            <td style="font-weight:bold" align="right"><?= number_format($PO['total'],2); ?></td>
                                          </tr>
                                        </table></td>
										</tr>
                                    </table>
									</div>									</td>
                                    </tr>
                                  </table></td></tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								  <tr>
								    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                      <tr>
                                        <td height="25" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td>&nbsp;<a href="javascript:void(0);" onClick="new Effect.toggle('attachments','blind')" class="black" <?php help('', 'Show or Hide the Attachments', 'default'); ?>><strong><img src="../images/paperclip.gif" width="17" height="17" border="0" align="texttop">&nbsp;Attachments</strong></a></td>
                                              <td width="160"><!--<a href="../Uploads/index.php?request_id=<?= $_GET['id']; ?>&type_id=PO#upload" target="attachments" class="viewcomments">Upload File</a>--></td>
                                            </tr>
                                          </table></td>
                                      </tr>
                                      <tr>
                                        <td>
										<div id="attachments">
										  <iframe id="attachments" name="attachments" frameborder="0" width="100%" height="150" src="../Uploads/index.php?request_id=<?= $_GET['id']; ?>&type_id=PO"></iframe>
										</div></td>
                                      </tr>
                                    </table></td>
							    </tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>
								  <tr>
								    <td class="BGAccentDarkBorder"><table width="100%" border="0">
                                      <tr>
                                        <td width="100%" height="25" colspan="6" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td>&nbsp;<a href="javascript:switchComments();" class="black" <?php help('', 'Show or Hide the Comments', 'default'); ?>><strong><img src="../images/comments.gif" width="19" height="16" border="0" align="texttop">&nbsp;Comments</strong></a></td>
                                              <td width="120"><a href="comments.php?request_id=<?= $_GET['id']; ?>&eid=<?= $_SESSION['eid']; ?>" title="Post a new comment" rel="gb_page_center[675,325]" class="add">NEW COMMENT</a></td>
                                            </tr>
                                        </table></td>
                                      </tr>
								        <td>
											<?php if ($post_count > 0) { ?>
											<div id="commentsHeader" onClick="switchComments();">There are currently <strong><?= $post_count; ?></strong> comments. The last comment was posted on <strong><?= date('F d, Y \a\t H:i A', strtotime($LAST_POST['posted'])); ?></strong>.<br>
							                <br><div class="clickToView">Click to view all Comments.</div></div>
											<?php } else { ?>
											<div id="commentsHeader">There are currently <strong>NO</strong> comments.</div>
											<?php } ?>
								            <div width="95%" border="0" align="center" id="comments_area" style="display:none" onClick="switchComments();">
											<br>
												<?php
													$count=0;
													while($post_sth->fetchInto($POST)) {
														$count++;
												  ?>
                                                    <div class="comment">
                                                      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                          <td width="55" rowspan="3" valign="top" class="comment_datenum"><div class="comment_month">
                                                              <?= date("M", strtotime($POST['posted'])); ?>
                                                            </div>
                                                              <div class="comment_day">
                                                                <?= date("d", strtotime($POST['posted'])); ?>
                                                              </div>
                                                            <div class="comment_year">
                                                                <?= date("y", strtotime($POST['posted'])); ?>
                                                            </div></td>
                                                          <td class="comment_wrote"><?= ucwords(strtolower($EMPLOYEES[$POST[eid]])); ?>
                                                            wrote... </td>
                                                        </tr>
                                                        <tr>
                                                          <td class="commentbody"><?= $POST['comment']; ?></td>
                                                        </tr>
                                                        <tr>
                                                          <td class="comment_date"><?= date("h:i A", strtotime($POST['posted'])); ?></td>
                                                        </tr>
                                                      </table>
                                                    </div>
                                                  <br>
                                            <?php } ?></div></td>
								      </table></td>
							    </tr>
								  <tr>
								    <td>&nbsp;</td>
							    </tr>								
								  <tr>
								    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                      <tr>
                                        <td width="405" height="25" colspan="6" class="BGAccentDark"><strong><a name="approvals"></a><img src="../images/checkmark.gif" width="16" height="16" align="texttop"></strong>&nbsp;Approvals</td>
                                      </tr>
									  <!-- START REQUESTER -->
                                      <tr>
                                        <td height="25" colspan="6"><table border="0">
                                          <tr class="BGAccentDark">
                                            <td height="25" nowrap>&nbsp;</td>
                                            <td width="30" nowrap>&nbsp;</td>
                                            <td nowrap class="HeaderAccentDark">&nbsp;</td>
                                            <td nowrap class="HeaderAccentDark">Date</td>
                                            <td width="20" align="center" nowrap class="HeaderAccentDark"><img src="/Common/images/clock.gif" width="16" height="16"></td>
                                            <td width="400" nowrap class="HeaderAccentDark">Comments</td>
                                            <td nowrap class="HeaderAccentDark"><?= (array_key_exists('approval', $_GET)) ? 'Approval' : $blank; ?></td>
                                          </tr>
                                          <tr>
                                            <td nowrap>Requester:</td>
                                            <td align="center" nowrap><?= showCommentIcon($PO['req'], ucwords(strtolower($EMPLOYEES[$PO['req']])), $PO['id']); ?></td>
                                            <td nowrap class="label"><?= ucwords(strtolower($EMPLOYEES[$PO['req']])); ?></td>
                                            <td nowrap class="label"><?= $PO['_reqDate']; ?></td>
                                            <td nowrap class="TrainActive">-</td>
                                            <td nowrap>&nbsp;</td>
                                            <td nowrap>&nbsp;</td>
                                          </tr>
										  <!-- END REQUESTER -->
                                          <!-- START APPROVER 1 -->
                                          <tr <?= ($_GET['approval'] == 'app1') ? $highlight : $blank; ?>>
                                            <td nowrap><?= $language['label']['app1']; ?>:</td>
                                            <td align="center" nowrap><?php 
													  if (is_null($AUTH['app1Date'])) {
                                                    	echo showMailIcon('app1', $AUTH['app1'], ucwords(strtolower($EMPLOYEES[$AUTH['app1']])), $PO['id']);
                                                      } else { 
													    echo showCommentIcon($AUTH['app1'], ucwords(strtolower($EMPLOYEES[$AUTH['app1']])), $PO['id']);
													  }
													  ?></td>
                                            <td nowrap class="label"><?= displayApprover($_GET['id'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
                                            <td nowrap class="label"><?php if (isset($AUTH['app1Date'])) { echo date("F d, Y", strtotime($AUTH['app1Date'])); } ?></td>
                                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app1Date'])) { echo abs(ceil((strtotime($REQUEST['reqDate']) - strtotime($AUTH['app1Date'])) / (60 * 60 * 24))); } ?></td>
                                            <td nowrap class="label"><?= displayAppComment('app1', $_GET['approval'], $AUTH['app1'], $AUTH['app1Com'], $AUTH['app1Date']); ?></td>
                                            <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
                                          </tr>
                                          <!-- END APPROVER 1 -->
										  <?php if (strlen($AUTH['app2']) == 5 OR $PO['status'] == 'N') { ?>
                                          <!-- START APPROVER 2 -->
                                          <tr <?= ($_GET['approval'] == 'app2') ? $highlight : $blank; ?>>
                                            <td nowrap><?= $language['label']['app2']; ?>:</td>
                                            <td align="center" nowrap><?php if (is_null($AUTH['app2Date']) AND !is_null($AUTH['app1Date']) AND $AUTH['app2'] != '0') {
                                                    	echo showMailIcon('app2', $AUTH['app2'], ucwords(strtolower($EMPLOYEES[$AUTH['app2']])), $PO['id']);
                                                      } else if (!is_null($AUTH['app2Date'])) { 
													    echo showCommentIcon($AUTH['app2'], ucwords(strtolower($EMPLOYEES[$AUTH['app2']])), $PO['id']);
													  }
													  ?></td>
                                            <td nowrap class="label"><?= displayApprover($_GET['id'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
                                            <td nowrap class="label"><?php if (isset($AUTH['app2Date'])) { echo date("F d, Y", strtotime($AUTH['app2Date'])); } ?></td>
                                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app2Date'])) { echo abs(ceil((strtotime($AUTH['app1Date']) - strtotime($AUTH['app2Date'])) / (60 * 60 * 24))); } ?></td>
                                            <td nowrap class="label"><?= displayAppComment('app2', $_GET['approval'], $AUTH['app2'], $AUTH['app2Com'], $AUTH['app2Date']); ?></td>
                                            <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
                                          </tr>
                                          <!-- END APPROVER 2 -->
										  <?php } ?>
										  <?php if (strlen($AUTH['app3']) == 5 OR $PO['status'] == 'N') { ?>
                                          <!-- END APPROVER 3 -->
                                          <tr <?= ($_GET['approval'] == 'app3') ? $highlight : $blank; ?>>
                                            <td nowrap><?= $language['label']['app3']; ?>:</td>
                                            <td align="center" nowrap><?php if (is_null($AUTH['app3Date']) AND !is_null($AUTH['app2Date']) AND $AUTH['app3'] != '0') {
                                                    	echo showMailIcon('app3', $AUTH['app3'], ucwords(strtolower($EMPLOYEES[$AUTH['app3']])), $PO['id']);
                                                      } else if (!is_null($AUTH['app3Date'])) { 
													    echo showCommentIcon($AUTH['app3'], ucwords(strtolower($EMPLOYEES[$AUTH['app3']])), $PO['id']);
													  }
													  ?></td>
                                            <td nowrap class="label"><?= displayApprover($_GET['id'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
                                            <td nowrap class="label"><?php if (isset($AUTH['app3Date'])) { echo date("F d, Y", strtotime($AUTH['app3Date'])); } ?></td>
                                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app3Date'])) { echo abs(ceil((strtotime($AUTH['app2Date']) - strtotime($AUTH['app3Date'])) / (60 * 60 * 24))); } ?></td>
                                            <td nowrap class="label"><?= displayAppComment('app3', $_GET['approval'], $AUTH['app3'], $AUTH['app3Com'], $AUTH['app3Date']); ?></td>
                                            <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
                                          </tr>
                                          <!-- END APPROVER 3 -->
										  <?php } ?>
										  <?php if (strlen($AUTH['app4']) == 5 OR $PO['status'] == 'N') { ?>
                                          <!-- START APPROVER 4 -->
                                          <tr <?= ($_GET['approval'] == 'app4') ? $highlight : $blank; ?>>
                                            <td nowrap><?= $language['label']['app4']; ?>:</td>
                                            <td align="center" nowrap><?php if (is_null($AUTH['app4Date']) AND !is_null($AUTH['app2Date']) AND $AUTH['app4'] != '0') {
                                                    	echo showMailIcon('app4', $AUTH['app4'], ucwords(strtolower($EMPLOYEES[$AUTH['app4']])), $PO['id']);
                                                      } else if (!is_null($AUTH['app4Date'])) { 
													    echo showCommentIcon($AUTH['app4'], ucwords(strtolower($EMPLOYEES[$AUTH['app4']])), $PO['id']);
													  }
													  ?></td>
                                            <td nowrap class="label"><?= displayApprover($_GET['id'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
                                            <td nowrap class="label"><?php if (isset($AUTH['app4Date'])) { echo date("F d, Y", strtotime($AUTH['app4Date'])); } ?></td>
                                            <td nowrap class="TrainActive"><?php if (isset($AUTH['app4Date'])) { echo abs(ceil((strtotime($AUTH['app3Date']) - strtotime($AUTH['app4Date'])) / (60 * 60 * 24))); } ?></td>
                                            <td nowrap class="label"><?= displayAppComment('app4', $_GET['approval'], $AUTH['app4'], $AUTH['app4Com'], $AUTH['app4Date']); ?></td>
                                            <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
                                          </tr>
                                          <!-- END APPROVER 4 -->
										  <?php } ?>
										  <?php if (!is_null($PO['po'])) { ?>
                                          <!-- START PURCHASER -->
                                          <tr <?= ($_GET['approval'] == 'purchaser') ? $highlight : $blank; ?>>
                                            <td nowrap>Purchaser: </td>
                                            <td align="center" nowrap><?= showCommentIcon($AUTH['issuer'], ucwords(strtolower($EMPLOYEES[$AUTH['issuer']])), $PO['id']); ?></td>
                                            <td nowrap class="label"><?= ucwords(strtolower($EMPLOYEES[$AUTH['issuer']])); ?></td>
                                            <td nowrap class="label"><?= date("F j, Y", strtotime($AUTH['issuerDate'])); ?></td>
                                            <td nowrap class="TrainActive"><?php if (isset($AUTH['generatorDate'])) { echo abs(ceil((strtotime($AUTH['coordinatorDate']) - strtotime($AUTH['generatorDate'])) / (60 * 60 * 24))); } ?></td>
                                            <td nowrap>&nbsp;</td>
                                            <td nowrap>&nbsp;</td>
                                          </tr>
										  <!-- END PURCHASER -->
										  <?php } ?>
										  <!-- START TOTAL -->
                                          <tr class="xpHeaderTotal">
                                            <td height="25" nowrap>Totals:</td>
                                            <td nowrap>&nbsp;</td>
                                            <td nowrap>&nbsp;</td>
                                            <td nowrap>&nbsp;</td>
                                            <td nowrap class="TrainActive"><?= abs(ceil((strtotime($REQUEST['reqDate']) - strtotime($AUTH['generatorDate'])) / (60 * 60 * 24))); ?></td>
                                            <td nowrap class="TipLabel">Days</td>
                                            <td nowrap>&nbsp;</td>
                                          </tr>
										  <!-- END TOTAL -->
                                        </table></td>
                                      </tr>
                                    </table></td>
							    </tr>
								<?php if ($_SESSION['request_role'] == 'purchasing') { ?>
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td valign="top" class="BGAccentDarkBlueBorder"><table width="100%" border="0">
                                    <tr>
                                      <td height="25" class="BGAccentDarkBlue"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                          <td style="color:#FFFFFF; font-weight:bold;"><img src="../images/team.gif" width="16" height="18" align="texttop"> Purchasing Department</td>
                                          <td align="right"><a href="javascript:void(0);"  <?php help('Last Updated', date('\D\a\y\:  F d, Y \<\b\r\>\T\i\m\e\: H:i:s', strtotime($PO['update_status'])), 'default'); ?> class="LightHeaderSubSub"><?php if (!empty($PO['purchaserUpdate'])) { ?>Last Updated: <?= date('F d, Y', strtotime($PO['purchaserUpdate'])); ?></a><?php } ?></td>
                                        </tr>
                                      </table></td>
                                    </tr>
                                    <tr>
                                      <td><table width="100%" border="0" cellspacing="2" cellpadding="0">
										<?php if (is_null($PO['po'])) { ?>
                                          <tr>										
                                            <td colspan="4" class="blankNoteArea">Purchaser scheduled to Complete Requisition:
                                            <select name="purchaserHold" id="purchaserHold" onchange="this.form.submit();">
                                            <option value="0">Select One</option>
                                            <?php
												  $purchaser_sth = $dbh->execute($purchaser_sql);
												  while($purchaser_sth->fetchInto($PURCHASER)) {
													$selected = ($AUTH['issuer'] == $PURCHASER['eid']) ? selected : $blank;
													print "<option value=\"".$PURCHASER['eid']."\" ".$selected.">".ucwords(strtolower($PURCHASER['lst'].", ".$PURCHASER['fst']))."</option>";
												  }
											  ?>
                                            </select></td>
										  </tr>
										<?php } else { ?>
										  <tr>
											<td width="100">Purchaser:</td>
											<td class="label"><?= caps($EMPLOYEES[$AUTH['issuer']]); ?></td>
											<td width="125">Supplier Kickoff:</td>
											<td nowrap class="label"><?= ($AUTH['issuerDate'] == '0000-00-00 00:00:00' OR is_null($AUTH['issuerDate'])) ? $blank : date("F j, Y h:iA", strtotime($AUTH['issuerDate'])); ?></td>									
										  </tr>
										<?php } ?>
                                        <tr>
                                          <td height="5" colspan="4"><fieldset class="collapsible" id="purchasingInfoF">
										  <legend><a href="javascript:switchFieldset('purchasingInfo', 'collapsible');" id="purchasingInfoA">Information</a></legend>
										  <table width="100%" border="0" cellspacing="2" cellpadding="0" id="purchasingInfo">                                            
                                            <tr>
                                              <td width="194" nowrap>FOB:</td>
                                              <td width="150"><input name="fob" type="text" id="fob" value="<?= $PO['fob']; ?>" size="15" maxlength="15"></td>
                                              <td width="142" nowrap>Requisition Type: </td>
                                              <td width="187"><select name="reqType" id="reqType">
												  <option value="0">Select One</option>
												  <option value="blanket">Blanket Order</option>
												  <option value="capex">Capital Expense</option>
												  <option value="mro">MRO</option>
												  <option value="tooling">Tooling</option>
											  </select></td>
                                            </tr>
                                            <tr>
                                              <td>Ship Via:</td>
                                              <td><input name="via" type="text" id="via" value="<?= $PO['via']; ?>" size="15" maxlength="15"></td>
                                              <td width="142">Final Terms:</td>
                                              <td><select name="terms" id="terms">
													<option value="0">Select One</option>
													<?php
													  $terms_sth = $dbh->execute($terms_query);
													  while($terms_sth->fetchInto($TERMS2)) {
														$selected = ($PO['terms'] == $TERMS2['id']) ? selected : $blank;
														print "<option value=\"".$TERMS2['id']."\" ".$selected.">".$TERMS2['name']."</option>";
													  }
													  ?>
													</select></td>
                                            </tr>
                                            <tr>
                                              <td colspan="4">&nbsp;</td>
                                            <tr>
                                              <td>&nbsp;</td>
                                              <td colspan="2" class="dHeader">Name</td>
                                              <td class="dHeader">Terms</td>
                                            </tr>
                                            <tr>
                                              <td>Recommended Vendor:</td>
                                              <td colspan="2" nowrap><?= caps($VENDOR[$PO['sup']]) . " (" . strtoupper($PO['sup']) . ")"; ?>
                                                  <?php
												  /* Getting suppliers from Suppliers */						 
												  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web, BTTRMC AS terms
																    FROM Standards.Vendor
																    WHERE BTVEND = '" . $PO['sup'] . "'");		
												  ?><span class="padding"><a href="http://maps.google.com/maps?q=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>&om=1" title="Map <?= caps($SUPPLIER['name']); ?>'s location" rel="gb_page_fs[]">&nbsp;<img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle"></a><?php if (!empty($SUPPLIER['web'])) { ?>&nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a><?php } ?>&nbsp;<a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle"></a></span></td>
                                              <td><?= $TERMS[$SUPPLIER['terms']]; ?></td>
                                            </tr>
                                            <?php if (!is_null($PO['sup2'])) { ?>
                                            <tr>
                                              <td>Final Vendor:</td>
                                              <td colspan="2" nowrap><?= caps($VENDOR[$PO['sup2']]) . " (" . strtoupper($PO['sup2']) . ")"; ?>
                                                  <?php
												  /* Getting suppliers from Suppliers */						 
												  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web, BTTRMC AS terms
																    FROM Standards.Vendor
																    WHERE BTVEND = '" . $PO['sup2'] . "'");		
												  ?><span class="padding"><a href="http://maps.google.com/maps?q=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>&om=1" title="Map <?= caps($SUPPLIER['name']); ?>'s location" rel="gb_page_fs[]">&nbsp;<img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle"></a><?php if (!empty($SUPPLIER['web'])) { ?>&nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a><?php } ?>&nbsp;<a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle"></a></span></td>
                                              <td><?= $TERMS[$SUPPLIER['terms']]; ?></td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                              <td>Change Vendor:</td>
                                              <td colspan="3"><input id="vendSearch" name="vendSearch" type="text" size="50" />
                                                  <script type="text/javascript">
													Event.observe(window, "load", function() {
														var aa = new AutoAssist("vendSearch", function() {
															return "../Common/vendor.php?q=" + this.txtBox.value;
														});
													});
											</script>
                                                <input name="supplierNew" type="hidden" id="supplierNew"><input name="supplierNewTerms" type="hidden" id="supplierNewTerms"></td>
                                            </tr>
                                            <tr>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                              <td>&nbsp;</td>
                                              <td><input name="updaterequest" type="image" src="../images/button.php?i=w130.png&l=Update Request" alt="Update Request" border="0">
                                                  <input name="action" type="hidden" id="action" value="purchasing_update">
                                                &nbsp;</td>
                                            </tr>
                                          </table>
                                          </fieldset>
										  <fieldset class="collapsed" id="vendorPaymentsF">
                                            <legend><a href="javascript:switchFieldset('vendorPayments', 'collapsed');" id="vendorPaymentsA">Vendor Payments (<?= $payments_count; ?>)</a></legend>
                                            <table border="0" align="center" cellpadding="0" cellspacing="5" id="vendorPayments" style="display:none">
                                              <tr>
                                                <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                                                  <tr>
                                                    <td class="blueNoteAreaBorder"><table border="0">
                                                        <tr class="blueNoteArea">
                                                          <td class="label">Amount</td>
                                                          <td class="label">Date</td>
                                                          <td class="label">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                          <td class="label">$<input name="pay_amount" type="text" class="BGAccentDarkBlueBorder" id="pay_amount" size="15"></td>
                                                          <td class="label"><input name="paymentDate" type="text" id="paymentDate" size="10" maxlength="10" class="BGAccentDarkBlueBorder" value="<?= $PAYMENTS['0']['pay_date']; ?>" readonly>
                                                            <a href="javascript:show_calendar('Form.paymentDate')" <?php help('', 'Click here to choose a date', 'default'); ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                                          <td class="label"><input name="addPayment" type="image" id="addPayment" src="../images/add.gif"></td>
                                                        </tr>
                                                    </table></td>
                                                  </tr>
                                                </table></td>
                                                <td rowspan="2" align="right" valign="top"><img src="../images/spacer.gif" width="50" height="10"></td>
                                                <td align="right" valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                                                  <tr>
                                                    <td class="blueNoteAreaBorder">
													<?php if ($payments_count > 0) { ?>
													<table border="0">
                                                        <tr class="blueNoteArea">
                                                          <td class="label">&nbsp;</td>
                                                          <td class="label">Amount</td>
                                                          <td class="label">Date</td>
                                                          <td class="label">Delete</td>
                                                        </tr>
                                                        <?php
														$count=0;
														$total=$PO['total'];
														
														while ($payments->fetchInto($PAYMENTS)) {
															$count++;
															$total -= $PAYMENTS['pay_amount'];
														?>
                                                        <tr>
                                                          <td class="label"><?= $count; ?><input type="hidden" name="pay_id<?= $count; ?>" id="pay_id<?= $count; ?>" value="<?= $PAYMENTS['pay_id']; ?>"></td>
                                                          <td class="label">$<input name="pay_amount<?= $count; ?>" type="text" class="BGAccentDarkBlueBorder" id="pay_amount<?= $count; ?>" value="<?= $PAYMENTS['pay_amount']; ?>" size="15"></td>
                                                          <td class="label"><input name="pay_date<?= $count; ?>" type="text" id="pay_date<?= $count; ?>" size="10" maxlength="10" class="BGAccentDarkBlueBorder" value="<?= $PAYMENTS['pay_date']; ?>" readonly>
                                                            <a href="javascript:show_calendar('Form.pay_date<?= $count; ?>')" <?php help('', 'Click here to choose a date', 'default'); ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                                          <td align="center" class="label"><input type="checkbox" name="pay_remove<?= $count; ?>" id="pay_remove<?= $count; ?>" value="yes"></td>
                                                        </tr>
                                                        <?php } ?>														
                                                    </table>
													<input name="payments_count" id="payments_count" type="hidden" value="<?= $payments_count; ?>">
													<?php } ?></td>
                                                  </tr>
                                                </table></td>
                                              </tr>
                                              <tr>
                                                <td>&nbsp;</td>
                                                <td>
												<?php if ($payments_count > 0) { ?>
												<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                  <tr>
                                                    <td class="label">Outstanding: $<?= number_format($total,2); ?></td>
                                                    <td align="right"><input name="updatePayments" id="updatePayments" type="image" src="../images/button.php?i=w70.png&l=Update" alt="Update Payments" border="0">&nbsp;</td>
                                                  </tr>
                                                </table>
												<?php } ?>
												</td>
                                              </tr>
                                            </table>
                                          </fieldset>
										  <fieldset class="collapsed" id="currentContactsF">
										  <legend><a href="javascript:switchFieldset('currentContacts', 'collapsed');" id="currentContactsA">Contact Information (<?= $contacts_count; ?>)</a></legend>
										  <table border="0" align="center" cellpadding="0" cellspacing="0" id="currentContacts" style="display:none">
                                            <tr>
                                              <td class="blueNoteAreaBorder"><table border="0">
                                                <tr class="blueNoteArea">
                                                  <td class="label">Name</td>
                                                  <td class="label">Phone</td>
												  <td class="label">Ext</td>
                                                  <td class="label">Email</td>
                                                </tr>
                                                <tr>
                                                  <td class="label"><input name="cc_name" type="text" id="cc_name" size="30" maxlength="30" class="BGAccentDarkBlueBorder"></td>
                                                  <td class="label"><input name="cc_phone" type="text" id="cc_phone" size="15" maxlength="15" class="BGAccentDarkBlueBorder"></td>
												  <td class="label"><input name="cc_ext" type="text" id="cc_ext" size="10" maxlength="10" class="BGAccentDarkBlueBorder"></td>
                                                  <td class="label"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td><input name="cc_email" type="text" id="cc_email" size="50" maxlength="50" class="BGAccentDarkBlueBorder"></td>
                                                        <td width="35" align="center"><input name="addContact" type="image" id="addContact" src="../images/add.gif"></td>
                                                      </tr>
                                                  </table></td>
                                                </tr>
                                                <?php
												while($contacts->fetchInto($CONTACTS)) {
												?>
                                                <tr>
                                                  <td class="label"><?= $CONTACTS['name']; ?></td>
                                                  <td class="label"><?= $CONTACTS['phone']; ?></td>
												  <td class="label"><?= $CONTACTS['ext']; ?></td>
                                                  <td class="label"><a href="mailto:<?= $CONTACTS['email']; ?>" class="emailLink"><?= $CONTACTS['email']; ?></a></td>
                                                </tr>
                                                <?php } ?>
                                              </table></td>
                                            </tr>
                                          </table>
										  </fieldset></td>
                                        </tr>
                                          <tr>
                                            <td height="5" colspan="4"><img src="../images/spacer.gif" width="5" height="10"></td>
                                          </tr>
                                          <tr>
                                            <td colspan="4" class="blueNoteArea">
											<?php if (empty($PO['po'])) { ?>
											<table border="0" align="center" cellpadding="0" cellspacing="0">
                                              <tr>
											    <td style="color:#FFFFFF; font-weight:bold">Purchase Order Number:</td>
                                                <td width="85" align="center"><input name="po" type="text" id="po" size="15"></td>
                                                <td><a href="vendorKickoff.php?id=<?= $_GET['id']; ?>" rel="gb_page_center[550,275]"><img src="../images/button.php?i=w130.png&l=Vendor Kickoff" alt="Supplier Kickoff" border="0"></a></td>
                                              </tr>
                                            </table>
											<?php } else { ?>
											<span class="editable">Purchase Order Number: <span id="poNumber"><?= $PO['po']; ?></span></span>
											<script type="text/javascript">new Ajax.InPlaceEditor('poNumber', '../Common/detail_updates.php?a=po&id=$PO[id]', {highlightcolor:"#D8D8EB", highlightendcolor:"#014B94"});</script>											
											<?php } ?></td>
                                          </tr>
                                      </table></td>
                                    </tr>
                                  </table></td>
								</tr>
								  <?php } ?>
                              </table></td>
                            </tr>
                            <tr>
                              <td height="5" valign="bottom"><img src="../images/spacer.gif" width="5" height="5"></td>
                            </tr>
                            <tr>
                              <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td width="50%"><?php if ($_SESSION['eid'] == $PO['req'] AND $PO['status'] != 'X' AND empty($_GET['approval'])) { ?>
                                    <table  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="20" valign="middle"><input name="cancel" type="checkbox" id="cancel" value="yes"></td>
                                        <td><input name="cancelrequest" type="image" src="../images/button.php?i=w130.png&l=Cancel Request" alt="Cancel Request" border="0"></td>
                                        <!--<td>&nbsp;</td>
                                           <td><input name="copyrequest" type="image" src="../images/button.php?i=w130.png&l=Copy Request" alt="Copy Request" border="0"></td>-->
                                      </tr>
                                    </table>
                                    <?php } ?>
                                    <?php if ($_SESSION['eid'] == $PO['req'] AND $PO['status'] == 'X') { ?>
                                    <table  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="20" valign="middle"><input name="restore" type="checkbox" id="restore" value="yes"></td>
                                        <td><input name="restorerequest" type="image" src="../images/button.php?i=w130.png&l=Restore Request" alt="Restore Request"></td>
                                      </tr>
                                    </table>
                                  <?php } ?>&nbsp;</td>
								  <td width="50%" align="right">
                                    <input name="id" type="hidden" id="id" value="<?= $PO['id']; ?>">
									<input name="req" type="hidden" id="req" value="<?= $PO['req']; ?>">
									<input name="purpose" type="hidden" id="purpose" value="<?= $PO['purpose']; ?>">
									<input name="auth_id" type="hidden" id="auth_id" value="<?= $AUTH['id']; ?>">							  
								    <?php 
										if (($_SESSION['eid'] == $AUTH[$_GET['approval']] OR
									 								   $_SESSION['eid'] == $PO['req'] OR
																	   $_SESSION['eid'] == $AUTH['issuer'])) { ?>
                                    <?php
										 if (isset($_GET['approval'])) {
											/* Set auth level to GET[approval] */
											$auth_value = $_GET['approval'];
										 } elseif ($_SESSION['eid'] == $PO['req']) {
											/* Allow update if GET[approval] was sent and Requester is viewing */
											$auth_value = "req";
										 } elseif ($_SESSION['eid'] == $AUTH['issuer']) {
											/* Allow update if GET[approval] was sent and Requester is viewing */
											$auth_value = "issuer";
										 }
										 
										 /* Set type of update before or after PO was issued */
										 $update_stage = (empty($PO[po])) ? "update" : "post_update";
									   ?>
                                    <input name="auth" type="hidden" id="auth" value="<?= $auth_value; ?>">
                                    <input name="type_id" type="hidden" id="type_id" value="<?= $_GET['id']; ?>">
                                    <input name="stage" type="hidden" id="stage" value="<?= $update_stage ?>">
                                    <!--<input name="imageField" type="image" src="../images/button.php?i=b150.png&l=Update Request" alt="Update Request">-->
                                  <?php } ?></td>								  
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
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>