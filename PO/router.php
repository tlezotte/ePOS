<?php
/**
 * Request System
 *
 * router.php sends required emails.
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
 * PHP Mailer
 * @link http://phpmailer.sourceforge.net/
 */
 

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
	


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting PO information */
$PO = $dbh->getRow("SELECT * FROM PO WHERE id = ?",array($_GET['type_id']));
if ($debug_page) { print_r($PO); echo "<br>"; }
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
if ($debug_page) { print_r($AUTH); echo "<br>"; }
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name
							 FROM Users u, Standards.Employees e
							 WHERE e.eid = u.eid");						 								   		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */


/* ------------------ START VARIABLE DATA ----------------------- */
$COMMENTS = array('app1' => array( caps($EMPLOYEES[$AUTH[app1]]), caps($AUTH['app1Com']) ),
				  'app2' => array( caps($EMPLOYEES[$AUTH[app2]]), caps($AUTH['app2Com']) ),
				  'app3' => array( caps($EMPLOYEES[$AUTH[app3]]), caps($AUTH['app3Com']) ),
				  'app4' => array( caps($EMPLOYEES[$AUTH[app4]]), caps($AUTH['app4Com']) )
				 );
/* ------------------ END VARIABLE DATA ----------------------- */


/* ------------------ START PROCESSING ---------------------------------------------------------------------- */
/* ---------- Check PO yn ---------- */
if ($_GET['yn'] == 'no') {
	$data = getEmployee($PO['req']);										// Get Requester's email
						 
	sendDeny($data['email'], $PO['id'], caps($PO['purpose']));				// Send deny email

	/* Create RSS file or continue to list.php */
	if ($default['rss'] == 'on') {
		$forward = "rss.php";												// Run RSS feed
	} else {
		$forward = "list.php?action=my&access=0";							// Forward to My Requisitions
	}

	if ($debug_page and $_SESSION['eid'] == '08745') {
		echo "email: ".$data['email']."<br>";
		echo "id: ".$PO['id']."<br>";	
		echo "Y AND N SECTION<br><br>";
		exit();
	} else {
		header("Location: ".$forward);
		exit();
	}
}


/* ---------- Controller Area ---------- */
if ($_GET['approval'] == 'controller') {
	$CONTROLLER = getController($_GET['plant'], $_GET['department']);						// Get Controller information
	
	setController($PO['id'], $CONTROLLER['eid']);											// Set Controller
	setAuthLevel($PO['id'], 'controller');													// Set Authorization level to controller
	
	sendMail($CONTROLLER['email'], 'controller', $PO['id'], caps($PO['purpose']));			// Send email to Controller
	
	//$forward = "list.php?action=my&access=0";												// Forward to My Requisitions
	$forward = "list.php";		
	header("Location: ".$forward);
	exit();
}


/* ---------- Check which PO level needs to be approved next ---------- */
$poApprover = substr($_GET['approval'],3);		//Extract PO approval from previous PO
if ($debug_page) { echo "poApprover: " . $poApprover . "<br>"; }
if ($debug_page) { echo "approverLevels: " . $default['approverLevels'] . "<br>"; }

for ($key = ++$poApprover; $key <= $default['approverLevels']; $key++) {
	$nextPO = 'app'.$key;															// Set PO name
	if ($debug_page) { echo "nextPO: " . $nextPO . "<br>"; }

	/* Check which PO level for approver */
	if (isset($AUTH[$nextPO]) AND strlen($AUTH[$nextPO]) == 5) {
		if ($debug_page) { echo "AUTH[nextPO]: " . $AUTH[$nextPO] . "<br>"; }
		
		$data = getEmployee($AUTH[$nextPO]);										// Get Approver's email
		
		setAuthLevel($PO['id'], $nextPO);											// Update Auth Level
			   
		sendMail($data['email'], $nextPO, $PO['id'], caps($PO['purpose']));			// Send email to next Approver
			
		/* Create RSS file or continue to list.php */
		if ($default['rss'] == 'on' and $_GET['approval'] == 'app0') {
			$forward = "rss.php";													// Run RSS feed
		} else {
			//$forward = "list.php?action=my&access=0";								// Forward to My Requisitions
			$forward = "list.php";
		}		

		if ($debug_page AND $_SESSION['eid'] == '08745') {
			echo "email: ".$data['email']."<br>";
			echo "id: ".$PO['id']."<br>";			
			echo "APPROVAL SECTION<br><br>";
			exit();
		} else {		
			header("Location: ".$forward);
			exit();
		}
	} 
}


/* ---------- Send approval to Purchasing when there is no more APP's ---------- */
setRequestStatus($PO['id'], 'A');									// Update PO status
setAuthLevel($PO['id'], 'A');										// Update Auth Level
sendPurchasing($PO['id'], caps($PO['purpose']));					// Email Approved Request to Purchasing
//recordAS400($PO['id']);											// Send Request data to AS400

if ($debug_page and $_SESSION['eid'] == '08745') {
	echo "email: ".$data['email']."<br>";
	echo "id: ".$PO['id']."<br>";
	echo "NO MORE APPS SECTION<br><br>";
	exit();
} else {
	//$forward = "list.php?action=my&access=0";						// Forward to My Requisitions
	$forward = "list.php";
	header("Location: ".$forward);
	exit();
}
/* ------------------ END PROCESSING --------------------------------------------------------------------------- */


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>