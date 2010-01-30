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
$Dbg->DatabaseName = "Request";

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



/* -------------------------------------------------------------				
 * ------------- START FINANCE PROCESSING -------------------
 * -------------------------------------------------------------
 */
if ($_POST['action'] == 'finance_update') {
	$changePlant = false;
	$changeDepartment = false;
	
	/* ----- Controller changes Plant ----- */
	if ($_POST['currentPlant'] != $_POST['plant']) {
		$plant_sql = "UPDATE PO SET plant='" . $_POST['plant'] . "' WHERE id=" . $_POST['type_id'];					// Update Bill to Plant
		$dbh->query($plant_sql);

		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($plant_sql)));		// Record transaction for history
		}
		
		$changePlant = true;
	}
	
	/* ----- Controller changes Department ----- */
	if ($_POST['currentDepartment'] != $_POST['department']) {
		$dept_sql = "UPDATE PO SET department='" . $_POST['department'] . "' WHERE id=" . $_POST['type_id'];			// Update Department
		$dbh->query($dept_sql);

		if ($default['debug_capture'] == 'on') {
			debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($dept_sql)));		// Record transaction for history
		}
		
		$changeDepartment = false;	// Turned off because we are not checking for departments now (HQ)
	}														

	/* ----- Forward to new Controller ----- */	
	if ($changePlant OR $changeDepartment) {
		$forward = "router.php?type_id=" . $_POST['type_id'] . "&approval=controller&plant=" . $_POST['plant'] . "&department=" . $_POST['department'];		// Check Controler
		header("Location: ".$forward);
		exit();
	}
	
	/* ----- Cycle through items ----- */
	for ($x=1; $x <= $_POST['items_count']; $x++) {
		$item = "item" . $x;					// Item ID
		$item_COA = $item . "_newCOA";			// COA value 
		$item_COAid = $item_COA . "id";			// COA value ID
		
		if (strlen($_POST[$item_COAid]) > 0) {
			$item_sql = "UPDATE Items SET cat='" . $_POST[$item_COAid] . "' WHERE id=" . $_POST[$item];
			$dbh->query($item_sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($item_sql)));		// Record transaction for history
			}			
		}
	}
	
	/* ----- Update CER numbers and Credit Card ----- */
	$po_sql = "UPDATE PO SET cer='" . $_POST['cer'] . "', 
							 creditcard='" . $_POST['creditcard'] . "' 
				 WHERE id=" . $_POST['type_id'];
	$dbh->query($po_sql);					 
				 
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($po_sql)));		// Record transaction for history
	}			 	
}
/* -------------------------------------------------------------				
 * ------------- END FINANCE PROCESSING -------------------
 * -------------------------------------------------------------
 */
 
 
 
/* -------------------------------------------------------------
 * ---------- START DENIAL PROCESSING -------------------------- 
 * -------------------------------------------------------------
 */
if (substr($_POST['auth'],0,3) == 'app' OR $_POST['auth'] == 'controller') {
	if (array_key_exists('yes_x', $_POST)) { $yn = "yes"; }					// Set approval button pressed
	if (array_key_exists('no_x', $_POST)) { $yn = "no"; }					// Set approval button pressed

	/* ---------------- Check to see if a Comment was provided ---------------- */
	if (empty($_POST['Com']) AND $yn == 'no') {
		$_SESSION['error'] = "A denied Requisition requires you to enter a Comment.";
		$_SESSION['redirect'] = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		
		header("location: ../error.php");
		exit();		
	}
		
	/* ---------------- Change status for non approved request ---------------- */
	if ($yn == 'no') {
	  setRequestStatus($_POST['type_id'], 'X');		// Update PO status
	}

	/* ---------------- Update the approvals for the PO ---------------- */
	$auth_sql = "UPDATE Authorization 
				 SET ".$_POST['auth']."yn='" . $yn . "', 
					 ".$_POST['auth']."Date=NOW(), 
					 ".$_POST['auth']."Com='" . htmlentities($_POST['Com'], ENT_QUOTES, 'UTF-8') . "'
				 WHERE id = ".$_POST['auth_id'];
	$dbh->query($auth_sql);
				 
	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($auth_sql)));		// Record transaction for history
	}	


	if ($_POST['auth'] == 'controller') {
		$forward = "router.php?type_id=" . $_POST['type_id'] . "&approval=app0";									// Forward to Approver 1
	} else {
		$forward = "router.php?type_id=" . $_POST['type_id'] . "&approval=" . $_POST['auth'] . "&yn=" . $yn;		// Record Controllers Approval
	}
	
	header("Location: ".$forward);
	exit();
}
/* -------------------------------------------------------------
 * ---------- END DENIAL PROCESSING ----------------------------
 * -------------------------------------------------------------
 */

 

/* -------------------------------------------------------------				
 * ------------- START UPDATE PROCESSING -----------------------
 * -------------------------------------------------------------
 */
if ($_POST['stage'] == "update") {
	/* -------------------------------------------------------------
	 * ---------- START REQUESTER PROCESSING ----------------------- 
	 * -------------------------------------------------------------
	 */
	if ($_POST['auth'] == "req") {
		/* ---------------- START CANCEL PURCHASE ORDER -------------- */ 
		if ($_POST['cancel'] == 'yes') {
			setRequestStatus($_POST['type_id'], 'C');		// Update PO status
			
			header("location: list.php?action=my&access=0");
			exit();
		}
		/* --------- END CANCEL PURCHASE ORDER ----------------------- */

		/* ---------------- START COPY PURCHASE ORDER ---------------- */ 		
//		if (array_key_exists('copyrequest_x',$_POST)) {
//			/* Getting PO information */
//			$PO = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate
//								FROM PO
//								WHERE id = ?",array($_POST['type_id']));	
//
//			/* -- Read $PO into $_SESSION -- */
//			foreach ($PO as $key => $value) {
//				$_SESSION[$key] = $value;
//			}
//			
//			header("location: information.php");
//			exit();	
//		}
		/* --------- END COPY PURCHASE ORDER ----------------------- */
		
		/* ---------------- START RESEND PURCHASE ORDER ---------------- */ 		
		if (array_key_exists('restorerequest_x',$_POST)) {
			setRequestStatus($_POST['type_id'], 'N');											// Update PO status
					
			header("location: router.php?type_id=" . $_POST['type_id'] . "&approval=app0");		// Forward to router as new Request
			exit();	
		}
		/* --------- END RESEND PURCHASE ORDER ----------------------- */		
	}
    /* -------------------------------------------------------------
	 * ---------- END REQUESTER PROCESSING ----------------------- 
	 * -------------------------------------------------------------
	 */
}
/* -------------------------------------------------------------				
 * ------------- END UPDATE PROCESSING -----------------------
 * -------------------------------------------------------------
 */



/* -------------------------------------------------------------				
 * ------------- START PURCHASING PROCESSING -------------------
 * -------------------------------------------------------------
 */
if ($_POST['action'] == 'purchasing_update') {
	/* ------------- Adding supplier contact information ------------- */
	if (array_key_exists('sendPO_x',$_POST)) {
		print_r($_POST);
		exit();	
	}
	
	/* ------------- Adding supplier contact information ------------- */
	if (array_key_exists('addContact_x',$_POST)) {
		$status = ($_POST['cc_save'] == 'no') ? '1' : '0';
		
		$sql = "INSERT INTO Contacts (id, request_id, eid, recorded, name, phone, ext, fax, email, status)
							  VALUES (NULL, 
									  '" . $_POST['id'] . "',
									  '" . $_SESSION['eid'] . "',
									  NOW(),
									  '" . $_POST['cc_name'] . "',
									  '" . $_POST['cc_phone'] . "',
									  '" . $_POST['cc_ext'] . "',
									  '" . $_POST['cc_fax'] . "',
									  '" . $_POST['cc_email'] . "',
									  '" . $status . "'
									  )";
		$dbh->query($sql);										   

		header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
		exit();	
	}
	
	/* ------------- Adding payment information ------------- */
	if (array_key_exists('addPayment_x',$_POST)) {
		$sql = "INSERT INTO Payments (pay_id, request_id, pay_eid, pay_recorded, pay_amount, pay_date, pay_status)
							  VALUES (NULL, 
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
	
	/* ------------- Update payment information ------------- */
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
		
	/* ------------- Purchaser marks Request as Complete ------------- */
	if (array_key_exists('completed_x',$_POST)) {
		/* Record Purchasing agent */
		$auth_sql = "UPDATE Authorization SET issuer='" . $_SESSION['eid'] . "', 
							  			 	  issuerDate=NOW(),
											  level='O'
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
	
	/* ------------- Add Vendor and change Terms when Purchasing changes Vendor ------------- */
	if (strlen($_POST['vendSearch']) > 0) {
		$supplier2  = "sup2='" . $_POST['supplierNew'] . "',";
		$supplier2 .= "terms='" . $_POST['supplierNewTerms'] . "',";
	} else {
		$supplier2  = "terms='".$_POST['terms']."',";
	}
	
	/* ------------- Creating update fields ------------- */
	$PO = "po='".$_POST['po']."',
		   fob='".$_POST['fob']."',
		   via='".$_POST['via']."',
		   purchaseUpdate=NOW(),
		   reqType='".$_POST['reqtype']."',
		   $supplier2
		   private='".$_POST['private']."',
		   hot='".$_POST['hot']."'
		   ";												
	$sql = "UPDATE PO SET $PO WHERE id = ".$_POST['id'];
	$dbh->query($sql);

	History($_SESSION['eid'], 'update', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));				// Record transaction for history

	header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['id']);
	exit();	
}
/* -------------------------------------------------------------				
 * ------------- END PURCHASING PROCESSING -------------------
 * -------------------------------------------------------------
 */


/* -------------------------------------------------------------				
 * ------------- START DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */
/* ------------- Getting PO information ------------- */
$PO = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate
				    FROM PO
				    WHERE id = ?",array($_GET['id']));
/* ------------- Getting Authoriztions for above PO ------------- */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
/* ------------- Get Employee names from Standards database ------------- */
$EMPLOYEES = $dbh->getAssoc("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees");
/* ------------- Getting Vendor information from Standards ------------- */							  
$VENDOR = $dbh->getAssoc("SELECT BTVEND, BTNAME FROM Standards.Vendor");
/* ------------- Getting Company information from Standards ------------- */	
//$COMPANY = $dbh->getAssoc("SELECT id, name FROM Standards.Companies");
/* ------------- Getting Plant information from Standards ------------- */	
$plant_sql = "SELECT id, name FROM Standards.Plants WHERE status = '0'";
$Dbg->addDebug($plant_sql,DBGLINE_QUERY,__FILE__,__LINE__);		//Debug SQL	
$PLANTS = $dbh->getAssoc($plant_sql);
$plant_query = $dbh->prepare($plant_sql);
$PLANT = $dbh->getRow("SELECT * FROM Standards.Plants WHERE id=" . $PO['plant']);
/* ------------- Getting Department information from Standards ------------- */	
$dept_sql = "SELECT id, CONCAT('(',id,') ',name) AS fullname FROM Standards.Department WHERE status='0' ORDER BY name";
$DEPARTMENT = $dbh->getAssoc($dept_sql);
$dept_query = $dbh->prepare($dept_sql);	
/* ------------- Getting Category information from Standards ------------- */	
$COA = $dbh->getAssoc("SELECT CONCAT(coa_account,'-',coa_suffix) AS id, coa_description AS name 
						FROM Standards.COA 
						WHERE coa_plant=" . $PLANT['conbr']);
/* ------------- Getting CER numbers from CER ------------- */							 						 
$cer_sql = "SELECT id, cer FROM CER WHERE cer IS NOT NULL ORDER BY cer+0";	
$CER = $dbh->getAssoc($cer_sql);	
$cer_query = $dbh->prepare($cer_sql);	
/* ------------- Getting Vendor terms from Standards ------------- */
$terms_sql = "SELECT terms_id AS id, terms_name AS name FROM Standards.VendorTerms ORDER BY name";
$TERMS = $dbh->getAssoc($terms_sql);
$terms_query = $dbh->prepare($terms_sql);
/* ------------- Get items related to this Request -------------*/
$items_sql = "SELECT * FROM Items WHERE type_id = ".$PO['id'];
$items_query = $dbh->prepare($items_sql);						   
$items_sth = $dbh->execute($items_query);
$items_count = $items_sth->numRows();	
/* ------------- Get Purchase Request users ------------- */
$purchaser_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst, E.email 
								 FROM Users U
								   INNER JOIN Standards.Employees E ON U.eid = E.eid
								 WHERE  U.role = 'purchasing' 
								   AND E.status = '0'
								   AND U.eid <> '08745'
								 ORDER BY E.lst ASC");
/* ------------- Get Purchase Request users ------------- */
$financer_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst, E.email 
								 FROM Users U
								   INNER JOIN Standards.Employees E ON U.eid = E.eid
								 WHERE  U.role = 'financing' 
								   AND E.status = '0'
								   AND U.eid <> '08745'
								 ORDER BY E.lst ASC");								 
/* ------------- Get Vendor Payments ------------- */
$payments = $dbh->query("SELECT * FROM Payments WHERE request_id=" . $PO['id'] . " AND pay_status='0' ORDER BY pay_date ASC");
$payments_count = $payments->numRows();
/* ------------- Getting Comments Information ------------- */
$post_sql = "SELECT * FROM Postings 
			 WHERE request_id = ".$_GET['id']." 
			   AND type = 'global'
			 ORDER BY posted DESC";
$LAST_POST = $dbh->getRow($post_sql);		// Get the last posted comment
$post_query = $dbh->prepare($post_sql);						   
$post_sth = $dbh->execute($post_query);
$post_count = $post_sth->numRows();							 																	 				  	
/* -------------------------------------------------------------				
 * ------------- END DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */


/* -------------------------------------------------------------				
 * ------------- START VARIABLES -------------------------------
 * -------------------------------------------------------------
 */
/* ------------- Check current level and current user ------------- */
if (array_key_exists('approval', $_GET)) {
	if 	($PO['status'] != 'N') {
		switch ($PO['status']) {
			case 'C': $message="This Requisition has been marked Canceled."; break;
			case 'X': $message="This Requisition has been marked Not Approved."; break;
			case 'A': $message="This Requisition has been already Approved."; break;
			case 'O': $message="This Requisition has been Kicked off to the Vendor."; break;
		}
		unset($_GET['approval']);
	} elseif ($_GET['approval'] != $AUTH['level']) {
		$message="This Requisition is currently not at your approval level.";
		unset($_GET['approval']);
	}  elseif ($_GET['approval'] == $AUTH['level'] AND $AUTH[$_GET['approval']] == $_SESSION['eid']) {
		$_GET['approval'] = $_GET['approval'];	
	}
}

/* ------------- Set message for Message Center ------------- */
switch ($_GET['approval']) {
//	case 'controller': $message="Finance can edit the Finance area and fields highlighted in <span style=\"color:#009900;\">GREEN</span>."; break;
}

$highlight='class="highlight"';						// Highlighted style sheet class

/* ------------- Set items display mode ------------- */
if ($_GET['approval'] == 'controller') {
	$displayItem = "display";						// Display Items detail
} else {
	$displayItem = "none";							// Don't display Items detail
}
/* -------------------------------------------------------------				
 * ------------- END VARIABLES -------------------------------
 * -------------------------------------------------------------
 */

/**
 * -  Display attachments from V3
 */
include_once('attachment.php');	


$phone_char=array('(', ')', ' ', '-', '.');
$format_phone="(000)000-0000";

/* ------------- Setup onLoad javascript program ------------- */
//$ONLOAD_OPTIONS.="function() { 
//								new Effect.Pulsate('hotMessage', {delay:2, duration:5});
//								new Effect.Pulsate('messageCenter', {delay:2, duration:5});
//							}";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?= $default['title1']; ?></title>
    <meta http-equiv="imagetoolbar" content="no">
    <meta name="copyright" content="2004 Your Company" />
    <meta name="author" content="Thomas LeZotte" />
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/reset-fonts-grids/reset-fonts-grids.css" />   
	<link type="text/css" rel="stylesheet" href="/Common/js/yahoo/container/assets/container.css" />  
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/fonts/fonts-min.css" />
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/assets/skins/sam/datatable.css" />
	<link type="text/css" rel="stylesheet" href="/Common/js/yahoo/assets/skins/sam/tabview.css" /> 
    <link type="text/css" rel="stylesheet" href="/Common/Javascript/yahoo/assets/skins/custom/menu.css">    
    <link type="text/css" rel="stylesheet" href="/Common/Print.css" media="print" />
    <link type="text/css" rel="stylesheet" href="../default_yui.css" />
    <?php if ($default['rss'] == 'on') { ?>
    <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
    <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
    <?php } ?>
	<script type="text/javascript" src="/Common/js/scriptaculous/prototype-min.js"></script>
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css" />
        
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all" />
</head>
<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <a href="../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u" align="right">
              <div style="font-weight:bold;font-size:115%"><?= $language['label']['title1']; ?>&nbsp;</div>
              <div class="FieldNumberDisabled"><strong><a href="../Administration/user_information.php" class="FieldNumberDisabled" title="Edit your user information"><?= ucwords(strtolower($_SESSION['fullname'])); ?></a></strong>&nbsp;<a href="../logout.php" class="FieldNumberDisabled" title="Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;</div>
          </div>
      </div>		      
    </div>
   <div id="bd">
	<div class="yui-g" id="noPrint"><?php include($default['FS_HOME'].'/include/main_menu_html.php'); ?></div>
	<div class="yui-g">
	  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon">
        <table border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td></td>
                  <td height="26" valign="bottom"><table border="0" align="right" cellpadding="0" cellspacing="0">
                      <tr>
                        <?php if ($_SESSION['eid'] == $PO['req'] and ! empty($PO['po'])) { ?>
                        <td><div id="ddcolortabs">
                            <ul>
                              <li id="inactive"><a href="checkInformation.php?id=<?= $PO['id']; ?>" title="Request Purchasing to send Vendor a check" rel="gb_page_center[750, 450]"><span>Check&nbsp;Request</span></a></li>
                            </ul>
                        </div></td>
                        <td><div id="ddcolortabs">
                            <ul>
                              <li id="inactive"><a href="packingSlip.php?id=<?= $_GET['id']; ?>&po=<?= $PO['po']; ?>&req=<?= $PO['req']; ?>&issuer=<?= $AUTH['issuer']; ?>" title="Send electronic packing slip to Puchasing" rel="gb_page_center[500, 300]"><span>Packing&nbsp;Slip</span></a></li>
                            </ul>
                        </div></td>
                        <?php } ?>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                      </tr>
                  </table></td>
                </tr>
                <tr class="BGAccentVeryDark">
                  <td width="50%" height="30" nowrap class="DarkHeaderSubSub">&nbsp;&nbsp;Purchase Order Requisition...</td>
                  <td width="50%"></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                      <tr>
                        <td height="25" colspan="4" class="BGAccentDark"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><img src="../images/info.png" width="16" height="16" align="texttop"><strong>&nbsp;Information</strong></td>
                              <td><div align="right" class="mainsection">Status: <strong>
                                <?= reqStatus($PO['status']); ?>
                                </strong>
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
                        <td width="45%" class="label"><?= ($PO['creditcard'] == 'yes') ? "Credit Card" : $PO['po']; ?></td>
                        <td width="13%" nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td nowrap>CER Number:</td>
                              <td width="35" align="right"><a href="cer_list.php" rel="gb_page_center[700, 600]" title="Click here to get a list of approved Capital Acquisition Requests"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                            </tr>
                        </table></td>
                        <td width="26%"><table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td class="label"><a href="../CER/detail.php?id=<?= $PO['cer']; ?>" class="dark" rel="gb_page_fs[]">
                                <?= $CER[$PO['cer']]; ?>
                              </a></td>
                              <td><?php if (!empty($PO['cer'])) { ?>
                                  <a href="<?= $default['URL_HOME']; ?>/CER/print.php?id=<?= $PO['cer']; ?>" title="Click here to print this Capital Acquisition Request"><img src="../images/printer.gif" border="0" align="absmiddle"></a>
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
                      <?php if (strlen($PO['sup2']) == 6) { ?>
                      <tr>
                        <td nowrap>Final Vendor: </td>
                        <td nowrap class="label"><?= caps($VENDOR[$PO['sup2']]) . " (" . strtoupper($PO['sup2']) . ")"; ?>
                            <?php
										  /* Getting suppliers from Suppliers */						 
										  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web
																    FROM Standards.Vendor
																    WHERE BTVEND = '".$PO['sup2']."'");		
							   			  ?>
                          <span class="padding"><a href="../Common/vendor_map.php?id=<?= strtoupper($SUPPLIER['id']); ?>&name=<?= caps($SUPPLIER['name']); ?>&address=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s Location" rel="gb_page_center[602, 602]"><img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle"></a>
                            <?php if (!empty($SUPPLIER['web'])) { ?>
                            &nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a>
                            <?php } ?>
                            <a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle"></a></span></td>
                        <td>Kickoff Date:</td>
                        <td class="label"><?= ($AUTH['issuerDate'] == '0000-00-00 00:00:00' OR is_null($AUTH['issuerDate'])) ? $blank : date("F j, Y", strtotime($AUTH['issuerDate'])); ?></td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td nowrap><?= (empty($PO['po'])) ? Recommended : Final; ?>
                          Vendor:</td>
                        <td nowrap class="label"><?= caps($VENDOR[$PO['sup']]) . " (" . strtoupper($PO['sup']) . ")"; ?>
                            <?php
										  /* Getting suppliers from Suppliers */						 
										  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web
																    FROM Standards.Vendor
																    WHERE BTVEND = '".$PO['sup']."'");		
							   			  ?>
                          <span class="padding"><a href="../Common/vendor_map.php?id=<?= strtoupper($SUPPLIER['id']); ?>&name=<?= caps($SUPPLIER['name']); ?>&address=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s Location" rel="gb_page_center[602, 602]"><img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle"></a>
                            <?php if (!empty($SUPPLIER['web'])) { ?>
                            &nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a>
                            <?php } ?>
                            <a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle"></a></span></td>
                        <?php if (strlen($PO['sup2']) != 6 AND !empty($PO['po'])) { ?>
                        <td>Kickoff Date:</td>
                        <td class="label"><?= ($AUTH['issuerDate'] == '0000-00-00 00:00:00' OR is_null($AUTH['issuerDate'])) ? $blank : date("F j, Y", strtotime($AUTH['issuerDate'])); ?></td>
                        <?php } else { ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php } ?>
                      </tr>
                      <!--                                        <tr>
                                          <td>Company:</td>
                                          <td class="label"><?= caps($COMPANY[$PO[company]]); ?></td>
                                          <td nowrap>&nbsp;</td>
                                          <td>&nbsp;</td>
                                        </tr>-->
                      <tr>
                        <td>Bill to Plant: </td>
                        <td class="label"><?= caps($PLANTS[$PO['plant']]); ?>
                            <input type="hidden" name="currentPlant" id="currentPlant" value="<?= $PO['plant']; ?>"></td>
                        <td>Deliver to Plant: </td>
                        <td class="label"><?= caps($PLANTS[$PO['ship']]); ?></td>
                      </tr>
                      <tr>
                        <td>Department:</td>
                        <td class="label"><?= caps($DEPARTMENT[$PO['department']]); ?>
                            <input type="hidden" name="currentDepartment" id="currentDepartment" value="<?= $PO['department']; ?>"></td>
                        <td>Job Number: </td>
                        <td class="label"><?= $PO['job']; ?></td>
                      </tr>
                      <tr>
                        <td height="5" colspan="4"><img src="../images/spacer.gif" width="5" height="5"></td>
                      </tr>
                      <tr>
                        <td valign="top" nowrap>Purpose / Usage:</td>
                        <td colspan="3" class="label"><?= caps(stripslashes($PO['purpose'])); ?></td>
                      </tr>
                  </table></td>
                </tr>
                <!--
                                  <tr>
                                    <td>&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td class="BGAccentDarkBorder"><table width="100%" border="0">
                                      <tr>
                                        <td width="100%" height="25" colspan="6" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td>&nbsp;<a href="javascript:switchTracking();" class="black" title="Show or Hide the Track Shipments"><strong><img src="../images/package.gif" width="16" height="16" border="0" align="texttop">&nbsp;Track Shipments </strong></a></td>
                                              <td width="120">&nbsp;</td>
                                            </tr>
                                        </table></td>
                                      </tr>
									  <td>&nbsp;</td>
                                    </table>
                                    </td>
                                  </tr>
								  -->
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                      <tr>
                        <td height="25" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>&nbsp;<a href="javascript:void(0);" onClick="new Effect.toggle('itemsContainer','blind')" class="black" title="Show or Hide the Item Information"><strong><img src="../images/text.gif" width="16" height="16" border="0" align="texttop">&nbsp;Item Information</strong></a></td>
                              <td width="120"><a href="javascript:toggleItemDisplay('itemsContainer');" class="viewcomments">View All Details </a></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><div id="itemsContainer">
                            <table width="100%"  border="0">
                              <tr>
                                <td width="35" class="HeaderAccentDark">&nbsp;</td>
                                <td width="35" class="HeaderAccentDark">Unit</td>
                                <td width="80" nowrap class="HeaderAccentDark">Company#&nbsp;</td>
                                <td width="60" nowrap class="HeaderAccentDark">Manuf#&nbsp;</td>
                                <td class="HeaderAccentDark">Item Description</td>
                                <td width="80" nowrap class="HeaderAccentDark">Price</td>
                              </tr>
                              <?php
								while($items_sth->fetchInto($ITEMS)) {
									$count_items++;
									$row_color = ($count_items % 2) ? FFFFFF : DFDFBF;
							  ?>
                              <!-- Start of Item<?= $count_items; ?> -->
                              <tr>
                                <td width="50" bgcolor="#<?= $row_color; ?>" class="label"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><a href="javascript:switchItems('itemDetails<?= $count_items; ?>', 'collapsed');" id="itemDetails<?= $count_items; ?>A" title="View more details on this item"><img src="../images/1rightarrow.gif" name="itemDetails<?= $count_items; ?>I" width="16" height="16" border="0" id="itemDetails<?= $count_items; ?>I">
                                            <input name="item<?= $count_items; ?>" type="hidden" id="item<?= $count_items; ?>" value="<?= $ITEMS['id']; ?>">
                                      </a></td>
                                      <td><strong>
                                        <?= $ITEMS['qty']; ?>
                                      </strong></td>
                                    </tr>
                                </table></td>
                                <td class="label" bgcolor="#<?= $row_color; ?>"><?= strtoupper($ITEMS['unit']); ?></td>
                                <td class="label" bgcolor="#<?= $row_color; ?>"><?= strtoupper(stripslashes($ITEMS['part'])); ?></td>
                                <td class="label" bgcolor="#<?= $row_color; ?>"><?= strtoupper(stripslashes($ITEMS['manuf'])); ?></td>
                                <td nowrap bgcolor="#<?= $row_color; ?>" class="label"><?php
										  	if (strlen($ITEMS['descr']) > 50) {
												echo caps(substr(stripslashes($ITEMS['descr']), 0, 50));
												echo "...<a href=\"javascript:void(0);\" title=\"" . caps(stripslashes($ITEMS['descr'])) . "\"><img src=\"../images/bubble.gif\" width=14 height=17 border=0 align=absmiddle></a>";
											} else {
												echo caps(stripslashes($ITEMS['descr']));
											}
											?></td>
                                <td bgcolor="#<?= $row_color; ?>" class="label"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><strong>$</strong></td>
                                      <td align="right"><strong>
                                        <?php  $d=(substr($ITEMS['price'], -2, 2) == '00') ? 2 : 4; echo number_format($ITEMS['price'],$d); ?>
                                        <?= ($d == 2) ? "<img src=\"/Common/images/spacer.gif\" width=\"18\" height=\"5\">" : ''; ?>
                                      </strong></td>
                                    </tr>
                                </table></td>
                              </tr>
                              <tr id="itemDetails<?= $count_items; ?>" style="display:<?= $displayItem; ?>">
                                <td colspan="6" bgcolor="#<?= $row_color; ?>" class="label"><table border="0">
                                    <tr>
                                      <td width="20">&nbsp;</td>
                                      <td width="200">&nbsp;</td>
                                      <td width="24">&nbsp;</td>
                                      <td width="100" class="gHeader">GL Code </td>
                                      <td width="250" class="gHeader">Name</td>
                                    </tr>
                                    <tr>
                                      <td>&nbsp;</td>
                                      <td><?= ($_GET['approval'] == 'controller') ? Recommended : Item; ?>
                                        Category: </td>
                                      <td class="label"><?= checkCategory($count_items, $PLANT['conbr'], $PO['department'], $ITEMS['cat']); ?></td>
                                      <td class="label"><?= $PLANT['conbr'] . "-" . $PO['department'] . "-" . $ITEMS['cat']; ?></td>
                                      <td class="label"><?= caps($COA[$ITEMS['cat']]); ?></td>
                                    </tr>
                                    <?php if ($_GET['approval'] == 'controllerOFF') { ?>
                                    <tr>
                                      <td>&nbsp;</td>
                                      <td>Change Category: </td>
                                      <td class="label">&nbsp;</td>
                                      <td class="label"><div id="item<?= $count_items; ?>_newGLnumber"></div></td>
                                      <td class="label"><input id="item<?= $count_items; ?>_newCOA" name="item<?= $count_items; ?>_newCOA" type="text" size="50" class="editContent" />
                                          <script type="text/javascript">
													Event.observe(window, "load", function() {
														var aa = new AutoAssist("item<?= $count_items; ?>_newCOA", function() {
															return "../Common/COA.php?p=<?= $PLANT['conbr']; ?>&d=<?= $PO['department']; ?>&i=<?= $count_items; ?>&q=" + this.txtBox.value;
														});
													});
											    </script>
                                          <input name="item<?= $count_items; ?>_newCOAid" type="hidden" id="item<?= $count_items; ?>_newCOAid"></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if ($_GET['approval'] == 'controller') { ?>
                                    <tr>
                                      <td>&nbsp;</td>
                                      <td>Change Category: </td>
                                      <td class="label">&nbsp;</td>
                                      <td nowrap class="label"><?= $PLANT['conbr']; ?>
                                        -
                                          <?= $PO['department']; ?>
                                        -
                                        <input name="item<?= $count_items; ?>_newCOAid" type="text" class="BGAccentDarkBlueBorder" id="item<?= $count_items; ?>_newCOAid" size="7" maxlength="7" /></td>
                                      <td><img src="/Common/images/left-collapsed.gif" width="10" height="10" align="absmiddle"> Enter GL-Number here (XXXX-XX)</td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                      <td>&nbsp;</td>
                                      <td>Company Tool Number: </td>
                                      <td class="label">&nbsp;</td>
                                      <td class="label"><?= $ITEMS['vt']; ?></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                </table></td>
                              </tr>
                              <!-- End of Item<?= $count_items; ?> -->
                              <?php } ?>
                              <tr>
                                <td colspan="5" align="right" class="xpHeaderBottomActive"><input name="items_count" type="hidden" id="items_count" value="<?= $count_items; ?>">
                                  Total: </td>
                                <td class="xpHeaderBottomActive"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td style="font-weight:bold">$</td>
                                      <td style="font-weight:bold" align="right"><?= number_format($PO['total'],2); ?>
                                          <img src="/Common/images/spacer.gif" width="18" height="5"></td>
                                    </tr>
                                </table></td>
                              </tr>
                            </table>
                        </div></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                      <tr>
                        <td height="25" class="BGAccentDark"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>&nbsp;<a href="javascript:void(0);" onClick="new Effect.toggle('attachments','blind')" class="black" title="Show or Hide the Attachments"><strong><img src="../images/paperclip.gif" width="17" height="17" border="0" align="texttop">&nbsp;Attachments</strong></a></td>
                              <td width="160"><!--<a href="../Uploads/index.php?request_id=<?= $_GET['id']; ?>&type_id=PO#upload" target="attachments" class="viewcomments">Upload File</a>--></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><div id="attachments">
                            <!--<iframe id="attachments" name="attachments" frameborder="0" width="100%" height="150" src="../Uploads/index.php?request_id=<?= $_GET['id']; ?>&type_id=PO"></iframe>-->
                            <table width="100%"  border="0">
                              <tr>
                                <td width="12%">Quote:</td>
                                <td nowrap><?= $Attachment; ?></td>
                                <?php if ($_SESSION['eid'] == $PO['req'] AND $_SESSION['eid'] != $AUTH['issuer']) { ?>
                                <td width="50%">&nbsp;
                                    <?= $CHANGE; ?>
                                  &nbsp;
                                  <input name="file" type="file" id="file" size="50"></td>
                                <?php } ?>
                              </tr>
                              <tr>
                                <td nowrap>File Cabinet:</td>
                                <td nowrap><?= $Attachment2; ?></td>
                                <?php if ($_SESSION['eid'] == $PO['req']) { ?>
                                <td>&nbsp;
                                    <?= $ADDITION; ?>
                                  &nbsp;
                                  <input name="file2" type="file" id="file2" size="50"></td>
                                <?php } ?>
                              </tr>
                            </table>
                        </div></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlueBorder : BGAccentDarkBorder; ?>"><table width="100%" border="0">
                      <tr>
                        <td width="100%" height="25" colspan="6" class="<?= (array_key_exists('approval', $_GET)) ? BGAccentDarkBlue : BGAccentDark; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>&nbsp;<a href="javascript:switchComments();" class="<?= (array_key_exists('approval', $_GET)) ? white : black; ?>" title="Show or Hide the Comments"><strong><img src="../images/comments.gif" width="19" height="16" border="0" align="texttop">&nbsp;Comments</strong></a></td>
                              <td width="120"><a href="comments.php?request_id=<?= $_GET['id']; ?>&eid=<?= $_SESSION['eid']; ?>" title="Post a new comment" rel="gb_page_center[675,325]" class="<?= (array_key_exists('approval', $_GET)) ? addWhite : addBlack; ?>">NEW COMMENT</a></td>
                            </tr>
                        </table></td>
                      </tr>
                    <td><?php if ($post_count > 0) { ?>
                        <div id="commentsHeader" onClick="switchComments();">There
                          <?= ($post_count > 1) ? are : is; ?>
                          currently <strong>
                            <?= $post_count; ?>
                            </strong> comment
                          <?= ($post_count > 1) ? s : ''; ?>
                          . The last comment was posted on <strong>
                            <?= date('F d, Y \a\t H:i A', strtotime($LAST_POST['posted'])); ?>
                            </strong>.<br>
                          <br>
                          <div class="clickToView">Click to view all Comments.</div>
                        </div>
                      <?php } else { ?>
                        <div id="commentsHeader">There are currently <strong>NO</strong> comments.</div>
                      <?php } ?>
                        <div width="95%" border="0" align="center" id="commentsArea" style="display:none" onClick="switchComments();"> <br>
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
                                  <td class="commentbody"><?= caps(stripslashes($POST['comment'])); ?></td>
                                </tr>
                                <tr>
                                  <td class="comment_date"><?= date("h:i A", strtotime($POST['posted'])); ?></td>
                                </tr>
                              </table>
                            </div>
                          <br>
                            <?php } ?>
                        </div></td>
                          </table></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <?php if ($_SESSION['request_role'] == 'controller' OR $_SESSION['request_role'] == 'executive') { ?>
                <tr>
                  <td class="BGAccentDarkBlueBorder"><table width="100%" border="0">
                      <tr>
                        <td height="25" class="BGAccentDarkBlue"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                              <td><img src="../images/team.gif" width="16" height="18" align="texttop"> <a href="javascript:void(0);" class="white"><strong>Finance Department</strong></a></td>
                              <td align="right"><a href="javascript:void(0);"  title="Last Updated', <?= date('\D\a\y\:  F d, Y \<\b\r\>\T\i\m\e\: H:i:s', strtotime($PO['update_status'])) ?>" class="LightHeaderSubSub"></a></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><fieldset class="collapsible" id="financeInfoF">
                          <legend><a href="javascript:switchFieldset('financeInfo', 'collapsible');" id="financeInfoA">Billing</a></legend>
                          <table width="100%" border="0" cellspacing="2" cellpadding="0" id="financeInfo">
                            <tr>
                              <td width="194" nowrap>CER Number:</td>
                              <td width="150" class="label"><a href="../CER/detail.php?id=<?= $PO['cer']; ?>" class="dark" rel="gb_page_fs[]">
                                <?= $CER[$PO['cer']]; ?>
                              </a></td>
                              <td nowrap><!--Credit Card Purchase:--></td>
                              <td width="187"><!--<select name="creditcard" id="creditcard" >
                                            <option value="no" <?= ($PO['creditcard'] == 'no') ? selected : $blank; ?>>No</option>
                                            <option value="yes" <?= ($PO['creditcard'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                          </select>--></td>
                            </tr>
                            <tr>
                              <td nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td nowrap>Change CER Number:</td>
                                    <td width="35" align="right"><a href="cer_list.php" rel="gb_page_center[700, 600]" title="Click here to get a list of approved Capital Acquisition Requests"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                  </tr>
                              </table></td>
                              <td><select name="cer" id="cer">
                                  <option value="0">Select One</option>
                                  <?php
									$cer_sth = $dbh->execute($cer_query);	
									while($cer_sth->fetchInto($DATA)) {
										echo "  <option value=\"".$DATA['id']."\">".caps($DATA['cer'])."</option>\n";
									}
									?>
                              </select></td>
                              <td nowrap><!--In Budget:--></td>
                              <td><!--<select name="inBudget" id="inBudget">
                                            <option value="no" <?= ($PO['inBudget'] == 'no') ? selected : $blank; ?>>No</option>
                                            <option value="yes" <?= ($PO['inBudget'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                          </select>--></td>
                            </tr>
                            <tr>
                              <td height="5" colspan="4" nowrap><img src="../images/spacer.gif" width="5" height="5"></td>
                            </tr>
                            <tr>
                              <td nowrap>Bill to Plant: </td>
                              <td class="label"><?= caps($PLANTS[$PO['plant']]); ?></td>
                              <td nowrap>Department:</td>
                              <td class="label"><?= caps($DEPARTMENT[$PO['department']]); ?></td>
                            </tr>
                            <tr>
                              <td nowrap>Change Bill to Plant: </td>
                              <td><select name="plant" id="plant">
                                  <option value="0">Select One</option>
                                  <?php
												$plant_sth = $dbh->execute($plant_query);	
												while($plant_sth->fetchInto($DATA)) {
													$selected = ($PO['plant'] == $DATA['id']) ? selected : $blank;
													echo "  <option value=\"".$DATA['id']."\" ".$selected.">".caps($DATA['name'])."</option>\n";
												}
												?>
                              </select></td>
                              <td nowrap>Change Department:</td>
                              <td><select name="department" id="department">
                                  <option value="0">Select One</option>
                                  <?php
											$dept_sth = $dbh->execute($dept_query);	
											while($dept_sth->fetchInto($DEPT)) {
												$selected = ($PO['department'] == $DEPT['id']) ? selected : $blank;
												echo "  <option value=\"".$DEPT['id']."\" ".$selected.">".caps($DEPT['fullname'])."</option>\n";
											}
											?>
                              </select></td>
                            </tr>
                            <tr>
                              <td height="5" colspan="4" nowrap><img src="../images/spacer.gif" width="5" height="5"></td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                              <td><!--<input name="updaterequest" type="image" src="../images/button.php?i=w130.png&l=Update Request" alt="Update Request" border="0">-->
                                  <input name="action" type="hidden" id="action" value="finance_update">
                                &nbsp;</td>
                            </tr>
                          </table>
                        </fieldset></td>
                      </tr>
                  </table></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <?php } ?>
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
                              <td width="20" align="center" nowrap class="HeaderAccentDark"><a href="javascript:void(0);" title="Days between Approvals"><img src="/Common/images/clock.gif" width="16" height="16" border="0"></a></td>
                              <td width="30" align="center" nowrap class="HeaderAccentDark"><a href="javascript:void(0);" title="Number of comments submitted by each Approver"><img src="../images/comments.gif" width="19" height="16" border="0"></a></td>
                              <td width="400" nowrap class="HeaderAccentDark">Comments</td>
                              <td nowrap class="HeaderAccentDark"><?= (array_key_exists('approval', $_GET)) ? 'Approval' : $blank; ?></td>
                            </tr>
                            <tr>
                              <td nowrap>Requester:</td>
                              <td align="center" nowrap><?= showCommentIcon($PO['req'], ucwords(strtolower($EMPLOYEES[$PO['req']])), $PO['id']); ?></td>
                              <td nowrap class="label"><?= ucwords(strtolower($EMPLOYEES[$PO['req']])); ?></td>
                              <td nowrap class="label"><?= $PO['_reqDate']; ?></td>
                              <td nowrap class="TrainActive">-</td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $PO['req']); ?>
                              </a></td>
                              <td nowrap>&nbsp;</td>
                              <td nowrap>&nbsp;</td>
                            </tr>
                            <!-- END REQUESTER -->
                            <?php if (strlen($AUTH['controller']) == 5) { ?>
                            <!-- START CONTROLLER -->
                            <tr <?= ($_GET['approval'] == 'controller') ? $highlight : $blank; ?>>
                              <td nowrap>Controller:</td>
                              <td align="center" nowrap><?php 
											  if (is_null($AUTH['controllerDate'])) {
												echo showMailIcon('controller', $AUTH['controller'], caps($EMPLOYEES[$AUTH['controller']]), $PO['id']);
											  } else { 
												echo showCommentIcon($AUTH['controller'], caps($EMPLOYEES[$AUTH['controller']]), $PO['id']);
											  }
											  ?></td>
                              <td nowrap class="label"><?= caps($EMPLOYEES[$AUTH['controller']]); ?></td>
                              <td nowrap class="label"><?php if (isset($AUTH['controllerDate'])) { echo date("F d, Y", strtotime($AUTH['controllerDate'])); } ?></td>
                              <td nowrap class="TrainActive">&nbsp;</td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $AUTH['controller']); ?>
                              </a></td>
                              <td nowrap class="label"><?= displayAppComment('controller', $_GET['approval'], $AUTH['controller'], $AUTH['controllerCom'], $AUTH['controllerDate']); ?></td>
                              <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'controller', $AUTH['controller'], $AUTH['controllerDate']); ?></td>
                            </tr>
                            <?php } ?>
                            <!-- END CONTROLLER -->
                            <!-- START APPROVER 1 -->
                            <tr <?= ($_GET['approval'] == 'app1') ? $highlight : $blank; ?>>
                              <td nowrap><?= $language['label']['app1']; ?>
                                :</td>
                              <td align="center" nowrap><?php 
													  if (is_null($AUTH['app1Date'])) {
                                                    	echo showMailIcon('app1', $AUTH['app1'], caps($EMPLOYEES[$AUTH['app1']]), $PO['id']);
                                                      } else { 
													    echo showCommentIcon($AUTH['app1'], caps($EMPLOYEES[$AUTH['app1']]), $PO['id']);
													  }
													  ?></td>
                              <td nowrap class="label"><?= displayApprover($_GET['id'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
                              <td nowrap class="label"><?php if (isset($AUTH['app1Date'])) { echo date("F d, Y", strtotime($AUTH['app1Date'])); } ?></td>
                              <td nowrap class="TrainActive"><?php if (isset($AUTH['app1Date'])) { echo abs(ceil((strtotime($PO['reqDate']) - strtotime($AUTH['app1Date'])) / (60 * 60 * 24))); } ?></td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $AUTH['app1']); ?>
                              </a></td>
                              <td nowrap class="label"><?= displayAppComment('app1', $_GET['approval'], $AUTH['app1'], $AUTH['app1Com'], $AUTH['app1Date']); ?></td>
                              <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
                            </tr>
                            <!-- END APPROVER 1 -->
                            <?php if (strlen($AUTH['app2']) == 5 OR $PO['status'] == 'N') { ?>
                            <!-- START APPROVER 2 -->
                            <tr <?= ($_GET['approval'] == 'app2') ? $highlight : $blank; ?>>
                              <td nowrap><?= $language['label']['app2']; ?>
                                :</td>
                              <td align="center" nowrap><?php if (is_null($AUTH['app2Date']) AND !is_null($AUTH['app1Date']) AND $AUTH['app2'] != '0') {
                                                    	echo showMailIcon('app2', $AUTH['app2'], caps($EMPLOYEES[$AUTH['app2']]), $PO['id']);
                                                      } else if (!is_null($AUTH['app2Date'])) { 
													    echo showCommentIcon($AUTH['app2'], caps($EMPLOYEES[$AUTH['app2']]), $PO['id']);
													  }
													  ?></td>
                              <td nowrap class="label"><?= displayApprover($_GET['id'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
                              <td nowrap class="label"><?php if (isset($AUTH['app2Date'])) { echo date("F d, Y", strtotime($AUTH['app2Date'])); } ?></td>
                              <td nowrap class="TrainActive"><?php if (isset($AUTH['app2Date'])) { echo abs(ceil((strtotime($AUTH['app1Date']) - strtotime($AUTH['app2Date'])) / (60 * 60 * 24))); } ?></td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $AUTH['app2']); ?>
                              </a></td>
                              <td nowrap class="label"><?= displayAppComment('app2', $_GET['approval'], $AUTH['app2'], $AUTH['app2Com'], $AUTH['app2Date']); ?></td>
                              <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
                            </tr>
                            <!-- END APPROVER 2 -->
                            <?php } ?>
                            <?php if (strlen($AUTH['app3']) == 5 OR $PO['status'] == 'N') { ?>
                            <!-- END APPROVER 3 -->
                            <tr <?= ($_GET['approval'] == 'app3') ? $highlight : $blank; ?>>
                              <td nowrap><?= $language['label']['app3']; ?>
                                :</td>
                              <td align="center" nowrap><?php if (is_null($AUTH['app3Date']) AND !is_null($AUTH['app2Date']) AND $AUTH['app3'] != '0') {
                                                    	echo showMailIcon('app3', $AUTH['app3'], caps($EMPLOYEES[$AUTH['app3']]), $PO['id']);
                                                      } else if (!is_null($AUTH['app3Date'])) { 
													    echo showCommentIcon($AUTH['app3'], caps($EMPLOYEES[$AUTH['app3']]), $PO['id']);
													  }
													  ?></td>
                              <td nowrap class="label"><?= displayApprover($_GET['id'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
                              <td nowrap class="label"><?php if (isset($AUTH['app3Date'])) { echo date("F d, Y", strtotime($AUTH['app3Date'])); } ?></td>
                              <td nowrap class="TrainActive"><?php if (isset($AUTH['app3Date'])) { echo abs(ceil((strtotime($AUTH['app2Date']) - strtotime($AUTH['app3Date'])) / (60 * 60 * 24))); } ?></td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $AUTH['app3']); ?>
                              </a></td>
                              <td nowrap class="label"><?= displayAppComment('app3', $_GET['approval'], $AUTH['app3'], $AUTH['app3Com'], $AUTH['app3Date']); ?></td>
                              <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
                            </tr>
                            <!-- END APPROVER 3 -->
                            <?php } ?>
                            <?php if (strlen($AUTH['app4']) == 5 OR $PO['status'] == 'N') { ?>
                            <!-- START APPROVER 4 -->
                            <tr <?= ($_GET['approval'] == 'app4') ? $highlight : $blank; ?>>
                              <td nowrap><?= $language['label']['app4']; ?>
                                :</td>
                              <td align="center" nowrap><?php if (is_null($AUTH['app4Date']) AND !is_null($AUTH['app2Date']) AND $AUTH['app4'] != '0') {
                                                    	echo showMailIcon('app4', $AUTH['app4'], caps($EMPLOYEES[$AUTH['app4']]), $PO['id']);
                                                      } else if (!is_null($AUTH['app4Date'])) { 
													    echo showCommentIcon($AUTH['app4'], caps($EMPLOYEES[$AUTH['app4']]), $PO['id']);
													  }
													  ?></td>
                              <td nowrap class="label"><?= displayApprover($_GET['id'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
                              <td nowrap class="label"><?php if (isset($AUTH['app4Date'])) { echo date("F d, Y", strtotime($AUTH['app4Date'])); } ?></td>
                              <td nowrap class="TrainActive"><?php if (isset($AUTH['app4Date'])) { echo abs(ceil((strtotime($AUTH['app3Date']) - strtotime($AUTH['app4Date'])) / (60 * 60 * 24))); } ?></td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $AUTH['app4']); ?>
                              </a></td>
                              <td nowrap class="label"><?= displayAppComment('app4', $_GET['approval'], $AUTH['app4'], $AUTH['app4Com'], $AUTH['app4Date']); ?></td>
                              <td nowrap><?= displayAppButtons($_GET['id'], $_GET['approval'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
                            </tr>
                            <!-- END APPROVER 4 -->
                            <?php } ?>
                            <?php if (!is_null($PO['po'])) { ?>
                            <!-- START PURCHASER -->
                            <tr <?= ($_GET['approval'] == 'purchaser') ? $highlight : $blank; ?>>
                              <td nowrap>Purchaser: </td>
                              <td align="center" nowrap><?= showCommentIcon($AUTH['issuer'], caps($EMPLOYEES[$AUTH['issuer']]), $PO['id']); ?></td>
                              <td nowrap class="label"><?= caps($EMPLOYEES[$AUTH['issuer']]); ?></td>
                              <td nowrap class="label"><?= date("F j, Y", strtotime($AUTH['issuerDate'])); ?></td>
                              <td nowrap class="TrainActive"><?php if (isset($AUTH['issuerDate'])) { echo abs(ceil((strtotime($AUTH['issuerDate']) - strtotime($AUTH['issuerDate'])) / (60 * 60 * 24))); } ?></td>
                              <td nowrap class="TrainActive"><a href="javascript:void(0);" onClick="switchComments();" class="black">
                                <?= checkComments($_GET['id'], $AUTH['issuer']); ?>
                              </a></td>
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
                              <td nowrap class="TrainActive"><?= abs(ceil((strtotime($REQUEST['reqDate']) - strtotime($AUTH['issuerDate'])) / (60 * 60 * 24))); ?></td>
                              <td nowrap class="TrainActive">&nbsp;</td>
                              <td nowrap class="TipLabel">Days</td>
                              <td nowrap>&nbsp;</td>
                            </tr>
                            <!-- END TOTAL -->
                        </table></td>
                      </tr>
                  </table></td>
                </tr>
                <?php if ($_SESSION['request_role'] == 'purchasing' OR $_SESSION['request_role'] == 'executive') { ?>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td valign="top" class="BGAccentDarkBlueBorder"><table width="100%" border="0">
                      <tr>
                        <td height="25" class="BGAccentDarkBlue"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="color:#FFFFFF; font-weight:bold;"><img src="../images/team.gif" width="16" height="18" align="texttop"> Purchasing Department</td>
                              <td align="right"><a href="javascript:void(0);" title="Last Updated', <?= date('\D\a\y\:  F d, Y \<\b\r\>\T\i\m\e\: H:i:s', strtotime($PO['update_status'])) ?>" class="LightHeaderSubSub">
                                <?php if (!empty($PO['purchaseUpdate'])) { ?>
                                Last Updated:
                                <?= date('F d, Y', strtotime($PO['update_status'])); ?>
                                </a>
                                  <?php } ?></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><table width="100%" border="0" cellspacing="2" cellpadding="0">
                            <tr>
                              <td height="5" colspan="4"><?php if (is_null($PO['po']) AND $PO['status'] == 'A') { ?>
                                  <div id="approvalTime"> Time elapsed since Requisition approval: <strong>
                                    <?= elapsedApprovalTime($_GET['id']); ?>
                                    hours</strong> </div>
                                <?php } ?>
                                  <div id="purchasingTabMenu" class="yui-navset">
                                    <ul class="yui-nav">
                                      <li class="selected"><a href="#Information"><em>Information</em></a></li>
                                      <li><a href="#VendorPayments"><em>Vendor Payments</em></a></li>
                                      <li><a href="#Contacts"><em>Contacts Information</em></a></li>
                                      <?php if (!empty($PO['po']) AND $PO['reqType'] == 'blanket' OR true) { ?>
                                      <li><a href="#SendPO"><em>Send Purchase Order</em></a></li>
                                      <?php } ?>
                                    </ul>
                                    <div class="yui-content">
                                      <div id="Information">
                                        <!-- ===================== START INFORMATION TAB ===================== -->
                                        <table width="100%" border="0" cellspacing="5" cellpadding="0" id="purchasingInfo">
                                          <tr>
                                            <td colspan="4" class="blueNoteArea"> Purchase Order Number:
                                              <input name="po" type="text" id="po" value="<?= $PO['po']; ?>" size="10" maxlength="7">
                                                <?php if (empty($PO['po'])) { ?>
                                              &nbsp;
                                              <input name="completed" type="image" src="../images/button.php?i=w130.png&l=Vendor Kickoff" alt="Supplier Kickoff" border="0">
                                              <?php } ?>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td nowrap="nowrap">Purchaser:</td>
                                            <td class="label"><?= caps($EMPLOYEES[$AUTH['issuer']]); ?></td>
                                            <td nowrap="nowrap">Vendor Kickoff:</td>
                                            <td class="label"><?= ($AUTH['issuerDate'] == '0000-00-00 00:00:00' OR is_null($AUTH['issuerDate'])) ? $blank : date("F j, Y h:iA", strtotime($AUTH['issuerDate'])); ?></td>
                                          </tr>
                                          <?php } ?>
                                          <tr>
                                            <td colspan="4" nowrap="nowrap">&nbsp;</td>
                                          </tr>
                                          <tr>
                                            <td nowrap="nowrap">Private Requisition:</td>
                                            <td><select name="private" id="private" disabled="disabled">
                                                <option value="no" <?= ($PO['private'] == 'no') ? selected : $blank; ?>>No</option>
                                                <option value="yes" <?= ($PO['private'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                            </select></td>
                                            <td nowrap="nowrap">HOT Requisition:</td>
                                            <td><select name="hot" id="hot">
                                                <option value="no" <?= ($PO['hot'] == 'no') ? selected : $blank; ?>>No</option>
                                                <option value="yes" <?= ($PO['hot'] == 'yes') ? selected : $blank; ?>>Yes</option>
                                            </select></td>
                                          </tr>
                                          <tr>
                                            <td width="194" nowrap="nowrap">FOB:</td>
                                            <td width="150"><input name="fob" type="text" id="fob" value="<?= $PO['fob']; ?>" size="15" maxlength="15" /></td>
                                            <td width="142" nowrap="nowrap">Requisition Type: </td>
                                            <td width="187"><select name="reqType" id="reqType">
                                                <option value="0">Select One</option>
                                                <option value="blanket" <?= ($PO['reqType'] == 'blanket') ? selected : $blank; ?>>Blanket Order</option>
                                                <option value="capex" <?= ($PO['reqType'] == 'capex') ? selected : $blank; ?>>Capital Expense</option>
                                                <option value="mro" <?= ($PO['reqType'] == 'mro') ? selected : $blank; ?>>MRO</option>
                                                <option value="tooling" <?= ($PO['reqType'] == 'tooling') ? selected : $blank; ?>>Tooling</option>
                                            </select></td>
                                          </tr>
                                          <tr>
                                            <td>Ship Via:</td>
                                            <td><input name="via" type="text" id="via" value="<?= $PO['via']; ?>" size="15" maxlength="15" /></td>
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
                                          </tr>
                                          <tr>
                                            <td>&nbsp;</td>
                                            <td colspan="2" class="dHeader">Name</td>
                                            <td class="dHeader">Terms</td>
                                          </tr>
                                          <tr>
                                            <td>Recommended Vendor:</td>
                                            <td colspan="2" nowrap="nowrap" class="label"><?= caps($VENDOR[$PO['sup']]) . " (" . strtoupper($PO['sup']) . ")"; ?>
                                                <?php
												  /* Getting suppliers from Suppliers */						 
												  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web, BTTRMC AS terms
																	FROM Standards.Vendor
																	WHERE BTVEND = '" . $PO['sup'] . "'");		
												  ?>
                                                <span class="padding"><a href="../Common/vendor_map.php?id=<?= strtoupper($SUPPLIER['id']); ?>&amp;name=<?= caps($SUPPLIER['name']); ?>&amp;address=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s Location" rel="gb_page_center[602, 602]"><img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle" /></a>
                                                <?php if (!empty($SUPPLIER['web'])) { ?>
                                                  &nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle" /></a>
                                                  <?php } ?>
                                                  <a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle" /></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&amp;company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle" />&nbsp;&nbsp;</a></span></td>
                                            <td class="label"><?= $TERMS[$SUPPLIER['terms']]; ?></td>
                                          </tr>
                                          <?php if (strlen($PO['sup2'])== 6) { ?>
                                          <tr>
                                            <td>Final Vendor:</td>
                                            <td colspan="2" nowrap="nowrap" class="label"><?= caps($VENDOR[$PO['sup2']]) . " (" . strtoupper($PO['sup2']) . ")"; ?>
                                                <?php
												  /* Getting suppliers from Suppliers */						 
												  $SUPPLIER = $dbh->getRow("SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTWPAG AS web, BTTRMC AS terms
																	FROM Standards.Vendor
																	WHERE BTVEND = '" . $PO['sup2'] . "'");		
												?>
                                                <span class="padding"><a href="../Common/vendor_map.php?id=<?= strtoupper($SUPPLIER['id']); ?>&amp;name=<?= caps($SUPPLIER['name']); ?>&amp;address=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s Location" rel="gb_page_center[602, 602]"><img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle" /></a>
                                                <?php if (!empty($SUPPLIER['web'])) { ?>
                                                  &nbsp;<a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website." rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle" /></a>
                                                  <?php } ?>
                                                  <a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?> information" rel="gb_page[400, 511]"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle" /></a>&nbsp;<a href="fax.php?id=<?= $PO['sup']; ?>&amp;company=<?= $PO['company']; ?>" title="Generate a fax cover sheet" target="_blank"><img src="../images/printer.gif" border="0" align="absmiddle" />&nbsp;&nbsp;</a></span></td>
                                            <td class="label"><?= $TERMS[$SUPPLIER['terms']]; ?></td>
                                          </tr>
                                          <?php } ?>
                                          <tr>
                                            <td>Change Vendor:</td>
                                            <td colspan="3" class="label"><input id="vendSearch" name="vendSearch" type="text" size="50" />
                                                <script type="text/javascript">
																	Event.observe(window, "load", function() {
																		var aa = new AutoAssist("vendSearch", function() {
																			return "../Common/vendor.php?q=" + this.txtBox.value;
																		});
																	});
                                                                </script>
                                                <input name="supplierNew" type="hidden" id="supplierNew" />
                                                <input name="supplierNewTerms" type="hidden" id="supplierNewTerms" /></td>
                                          </tr>
                                          <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td><input name="updaterequest" type="image" src="../images/button.php?i=w130.png&amp;l=Update Request" alt="Update Request" border="0" />
                                                <input name="action" type="hidden" id="action" value="purchasing_update" />
                                              &nbsp;</td>
                                          </tr>
                                        </table>
                                        <!-- ===================== END INFORMATION TAB ===================== -->
                                      </div>
                                      <div id="VendorPayments">
                                        <!-- ===================== START VENDOR PAYMENTS TAB ===================== -->
                                        <div class="blueNoteArea" style="text-align:right" id="outstanding"></div>
                                        <br>
                                        <table border="0" align="center" cellpadding="0" cellspacing="5" id="vendorPayments">
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
                                                        <td class="label">$
                                                          <input name="pay_amount" type="text" class="BGAccentDarkBlueBorder" id="pay_amount" size="15" /></td>
                                                        <td class="label"><input name="paymentDate" type="text" id="paymentDate" size="10" maxlength="10" class="BGAccentDarkBlueBorder" value="<?= $PAYMENTS['0']['pay_date']; ?>" readonly="readonly" />
                                                          <a href="javascript:show_calendar('Form.paymentDate')" title="Click here to choose a date"><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle" /></a></td>
                                                        <td class="label"><input name="addPayment" type="image" id="addPayment" src="../images/add.gif" /></td>
                                                      </tr>
                                                  </table></td>
                                                </tr>
                                            </table></td>
                                            <td rowspan="2" align="right" valign="top"><img src="../images/spacer.gif" width="50" height="10" /></td>
                                            <td align="right" valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td class="blueNoteAreaBorder"><?php if ($payments_count > 0) { ?>
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
                                                          <td class="label"><?= $count; ?>
                                                              <input type="hidden" name="pay_id<?= $count; ?>" id="pay_id<?= $count; ?>" value="<?= $PAYMENTS['pay_id']; ?>" /></td>
                                                          <td class="label">$
                                                            <input name="pay_amount<?= $count; ?>" type="text" class="BGAccentDarkBlueBorder" id="pay_amount<?= $count; ?>" value="<?= $PAYMENTS['pay_amount']; ?>" size="15" /></td>
                                                          <td class="label"><input name="pay_date<?= $count; ?>" type="text" id="pay_date<?= $count; ?>" size="10" maxlength="10" class="BGAccentDarkBlueBorder" value="<?= $PAYMENTS['pay_date']; ?>" readonly="readonly" />
                                                            <a href="javascript:show_calendar('Form.pay_date<?= $count; ?>')" title="Click here to choose a date"><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle" /></a></td>
                                                          <td align="center" class="label"><input type="checkbox" name="pay_remove<?= $count; ?>" id="pay_remove<?= $count; ?>" value="yes" /></td>
                                                        </tr>
                                                        <?php } ?>
                                                      </table>
                                                    <input name="payments_count" id="payments_count" type="hidden" value="<?= $payments_count; ?>" />
                                                      <script>$('outstanding').update('Oustanding: $<?= number_format($total,2); ?>');</script>
                                                      <?php } else { ?>
                                                      <script>$('outstanding').update('Oustanding: $<?= number_format($PO['total'],2); ?>');</script>
                                                      <?php } ?></td>
                                                </tr>
                                            </table></td>
                                          </tr>
                                          <?php if ($total == $PO['total']) { ?>
                                          <tr>
                                            <td>&nbsp;</td>
                                            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                  <td class="label"><?= number_format($total,2); ?></td>
                                                  <td align="right"><input name="updatePayments" id="updatePayments" type="image" src="../images/button.php?i=w70.png&amp;l=Update" alt="Update Payments" border="0" />
                                                    &nbsp;</td>
                                                </tr>
                                            </table></td>
                                          </tr>
                                          <?php } ?>
                                        </table>
                                        <!-- ===================== END VENDOR PAYMENTS TAB ===================== -->
                                      </div>
                                      <div id="Contacts">
                                        <!-- ===================== START CONTACTS TAB ===================== -->
                                        <center>
                                          <div id="contactsXML"></div>
                                        </center>
                                        <!-- ===================== END CONTACTS TAB ===================== -->
                                      </div>
                                      <div id="SendPO">
                                        <!-- ===================== START SEND PURCHASE ORDER TAB ===================== -->
                                        <center>
                                          <div id="sendPoXML"></div>
                                        </center>
                                        <!-- ===================== END SEND PURCHASE ORDER TAB ===================== -->
                                      </div>
                                    </div>
                                  </div></td>
                            </tr>
                        </table></td>
                      </tr>
                  </table></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="5" valign="bottom"><img src="../images/spacer.gif" width="5" height="5"></td>
          </tr>
          <tr>
            <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%"><?php if ($_SESSION['eid'] == $PO['req'] AND $PO['status'] != 'X' AND $PO['status'] != 'C' AND empty($_GET['approval'])) { ?>
                      <table  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="20" valign="middle"><input name="cancel" type="checkbox" id="cancel" value="yes"></td>
                          <td><input name="cancelrequest" type="image" src="../images/button.php?i=w130.png&l=Cancel Request" alt="Cancel Request" border="0"></td>
                          <!--<td>&nbsp;</td>
                                           <td><input name="copyrequest" type="image" src="../images/button.php?i=w130.png&l=Copy Request" alt="Copy Request" border="0"></td>-->
                        </tr>
                      </table>
                    <?php } ?>
                      <?php if ($_SESSION['eid'] == $PO['req'] AND ($PO['status'] == 'X' OR $PO['status'] == 'C')) { ?>
                      <table  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="20" valign="middle"><input name="restore" type="checkbox" id="restore" value="yes"></td>
                          <td><input name="restorerequest" type="image" src="../images/button.php?i=w130.png&l=Restore Request" alt="Restore Request"></td>
                        </tr>
                      </table>
                    <?php } ?>
                    &nbsp;</td>
                  <td width="50%" align="right"><input name="id" type="hidden" id="id" value="<?= $PO['id']; ?>">
                      <input name="type_id" type="hidden" id="type_id" value="<?= $PO['id']; ?>">
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
                      <input name="stage" type="hidden" id="stage" value="<?= $update_stage ?>">
                      <!--<input name="imageField" type="image" src="../images/button.php?i=b150.png&l=Update Request" alt="Update Request">-->
                      <?php } ?></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </form>
	  <!-- YOUR DATA GOES HERE -->
    </div>
	</div>
   <div id="ft">
	 <div class="yui-gb">
        <div class="yui-u first"><?php include('../include/copyright.php'); ?></div>
        <div class="yui-u"><!-- FOOTER CENTER AREA -->&nbsp;</div>
        <div class="yui-u" id="noPrint" align="right" style="margin-top: 5px;"><!-- FOOTER RIGHT AREA -->&nbsp;</div>
	    </div>
     </div>  
   </div>
</div>
<script>var request_id='<?= $_GET['id']; ?>';</script>
<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=builder,effects"></script>
<script type="text/javascript" src="/Common/js/ps/tooltips.js"></script>
<script type="text/javascript" src="/Common/js/ps/treasure.js"></script>
  
<script type="text/javascript" src="/Common/js/yahoo/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="/Common/js/yahoo/event/event-min.js" ></script>
<script type="text/javascript" src="/Common/js/yahoo/dom/dom-min.js" ></script>
<script type="text/javascript" src="/Common/js/yahoo/animation/animation-min.js" ></script>
<script type="text/javascript" src="/Common/js/yahoo/connection/connection-min.js" ></script>
<script type="text/javascript" src="/Common/js/yahoo/container/container-min.js"></script> 
<script type="text/javascript" src="/Common/js/yahoo/utilities/utilities.js"></script>
<script type="text/javascript" src="/Common/js/yahoo/datasource/datasource-beta-min.js"></script>
<script type="text/javascript" src="/Common/js/yahoo/datatable/datatable-beta-min.js"></script>
<script type="text/javascript" src="/Common/js/yahoo/element/element-beta-min.js"></script>
<script type="text/javascript" src="/Common/js/yahoo/tabview/tabview-min.js"></script>
<script>
	(function() {
		var tabView = new YAHOO.widget.TabView('purchasingTabMenu');
	})();
</script>
<?php include($default['FS_HOME'].'/include/main_menu_js.php'); ?> 
<?php if ($_SESSION['request_role'] == 'purchasing' OR $_SESSION['request_role'] == 'executive') { ?>
<script type="text/javascript" src="../js/purchasingDepartment.js"></script>
<?php } ?>
<?php if ($_SESSION['request_access'] == '3') { ?>
<script type="text/javascript" src="../js/adminDepartment.js"></script>
<?php } ?>

<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>

<script language="javascript">
	function toggleItemDisplay(id) {
		$R(1, <?= $items_count+1; ?>, true).each(function(s) {
			$('itemDetails' + s).toggle();
		});	
	}
</script>

<script type="text/javascript" charset="utf-8">
	$$("a").each( function(link) {
		new Tooltip(link, {backgroundColor: "#FF8C00", borderColor: "#000", textColor: "#000", textShadowColor: "#FFF", opacity:1});
	});
	$$("input").each( function(input) {
		new Tooltip(input, {backgroundColor: "#E6AC00", borderColor: "#000", textColor: "#000", textShadowColor: "#FFF", opacity:1});
	});		
</script> 
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