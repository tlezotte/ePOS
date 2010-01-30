<?php
/**
 * Request System
 *
 * router.php sends out emails.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package CER
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
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
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting CER information */
$CER = $dbh->getRow("SELECT * FROM CER WHERE id = ?",array($_GET['type_id']));
/* Getting Authoriztions for above CER */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'CER'",array($_GET['type_id']));								   										   		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

if ($debug) {
	echo "<BR>SESSION: <BR>";
	print_r($_SESSION);
	echo "<BR>GET: <BR>";
	print_r($_GET);
	echo "<BR>POST: <BR>";
	print_r($_POST);
}
		
/* ------------------ START FUNCTIONS ----------------------- */
/* -------- Send out email for approval ---------- */		
function sendMail($sendTo,$CER_Level,$CER_ID,$purpose) {
	global $default;
	
	include_once("Mail.php");
	
	$url = "http://".$_SERVER["HTTP_HOST"]."/go/Request/CER/detail.php?id=".$CER_ID."&approval=".$CER_Level;
	$recipients = $sendTo;
	
	$headers["From"]    = $default['email_from'];
	$headers["To"]      = $sendTo;
	$headers["Subject"] = "CER: ".$purpose;
	
	$body = "\n-------------------------------------------------------\n".
			"Welcome to the Your Company Purchase Request System\n".
			"-------------------------------------------------------\n\n".
			"You have a new Capital Expenditure Request to review.\n".
			"The purpose for this CER is: ".$purpose."\n\n".
			"URL: ".$url;
	
	$params["host"] = $default['smtp'];
	$params["port"] = $default['smtp_port'];
	
	// Create the mail object using the Mail::factory method
	$mail_object =& Mail::factory("smtp", $params);
	
	$mail_object->send($recipients, $headers, $body);
}	//End sendMail


/* -------- Send denied email ---------- */	
function sendDeny($sendTo,$CER_ID,$purpose) {
	global $default;
	
	include_once("Mail.php");
	
	$recipients = $sendTo;
	
	$headers["From"]    = $default['email_from'];
	$headers["To"]      = $sendTo;
	$headers["Subject"] = "CER DENIED: ".$purpose;
	
	$body = "\n-------------------------------------------------------\n".
			"Welcome to the Your Company Purchase Request System\n".
			"-------------------------------------------------------\n\n".
			"You Capital Expenditure Request has been denied.\n".
			"The purpose for this CER was: ".$purpose."\n\n".
			"URL: http://".$_SERVER["HTTP_HOST"]."/go/Request/CER/detail.php?id=".$CER_ID;
	
	$params["host"] = $default['smtp'];
	$params["port"] = $default['smtp_port'];
	
	// Create the mail object using the Mail::factory method
	$mail_object =& Mail::factory("smtp", $params);
	
	$mail_object->send($recipients, $headers, $body);
}	//End sendDeny


/* -------- Send approved email ---------- */	
function sendApproved($sendTo,$CER_ID,$purpose,$cer) {
	global $default;
	
	include_once("Mail.php");
	
	$recipients = $sendTo;
	
	$headers["From"]    = $default['email_from'];
	$headers["To"]      = $sendTo;
	$headers["Subject"] = "CER ".$cer.": ".$purpose;
	
	$body = "\n-------------------------------------------------------\n".
			"Welcome to the Your Company Purchase Request System\n".
			"-------------------------------------------------------\n\n".
			"Capital Expenditure Request ".$cer." has been approved.\n".
			"The purpose for this CER was: ".$purpose."\n\n".
			"URL: http://".$_SERVER["HTTP_HOST"]."/go/Request/CER/detail.php?id=".$CER_ID;
	
	$params["host"] = $default['smtp'];
	$params["port"] = $default['smtp_port'];
	
	// Create the mail object using the Mail::factory method
	$mail_object =& Mail::factory("smtp", $params);
	
	$mail_object->send($recipients, $headers, $body);
}	//End sendApproved
/* ------------------ END FUNCTIONS ----------------------- */



/* ------------------ START PROCESSING ----------------------- */

/* ---------- Issuer assigned CER number ---------- */
if ($_GET['approval'] == 'issuer' ) {
	$data = $dbh->getRow("SELECT CONCAT(fst,' ',lst) AS name, email ".
						 "FROM Standards.Employees ".
						 "WHERE eid = ?", array("$CER[req]"));
						 
	sendApproved($data['email'], $_GET['type_id'], $CER['purpose'], $_GET['req']);

	/* Create RSS file or continue to list.php */
	if ($default['rss'] == 'on') {
		$forward = "rss.php";
	} else {
		$forward = "list.php?action=my";
	}
	
	if ($debug) {
		echo "<BR>RSS: ".$default['rss']."<br>";
		echo "FORWARD: ".$forward."<br>";
		echo "APPROVAL: ".$_GET['approval']."<br>";
		echo "ISSUER";
		exit;
	}
	
	header("Location: ".$forward);
	exit;
}

/* ---------- Check CER yn ---------- */
if ($_GET['yn'] == 'no') {
	$data = $dbh->getRow("SELECT CONCAT(fst,' ',lst) AS name, email ".
						 "FROM Standards.Employees ".
						 "WHERE eid = ?", array("$CER[req]"));
						 
	sendDeny($data['email'], $_GET['type_id'], $CER['purpose']);
	
	header("Location: list.php?action=my");
	exit;
}

/* ---------- Check which CER level needs to be approved next ---------- */
$curApprover = substr($_GET['approval'],3);		//Extract CER number from previous CER

if ($debug) {
	echo "<BR>APPROVER: ".$curApprover."<BR>";
}

for ($key = ++$curApprover; $key <= 11; $key++) {
	$nextCER = 'app'.$key;	//Set CER name
	/* Check which CER level for approver */
	if (isset($AUTH[$nextCER]) and $AUTH[$nextCER] != 0) {
		$data = $dbh->getRow("SELECT CONCAT(fst,' ',lst) AS name, email ".
							 "FROM Standards.Employees ".
							 "WHERE eid = ?", array("$AUTH[$nextCER]"));
							   
		sendMail($data['email'], $nextCER, $_GET['type_id'], $CER['purpose']);
		
		/* Create RSS file or continue to list.php */
		if ($default['rss'] == 'on' and $_GET['approval'] == 'app0') {
			$forward = "rss.php";
		} else {
			$forward = "list.php?action=my";
		}
		
		if ($debug) {
			echo "<br>RSS: ".$default['rss']."<br>";
			echo "FORWARD: ".$forward."<br>";
			echo "CER: ".$curApprover."<br>";
			exit;
		}
	
		header("Location: ".$forward);
		exit;
	} 
}

/* ---------- Send approval to Issuer when there is no more CER's ---------- */
$data = $dbh->getRow("SELECT CONCAT(fst,' ',lst) AS name, email ".
					 "FROM Standards.Employees ".
					 "WHERE eid = ?", array("$AUTH[issuer]"));
   
sendMail($data['email'], 'issuer', $_GET['type_id'], $CER['purpose']);
header("Location: list.php?action=my");
exit;
/* ------------------ END PROCESSING ----------------------- */

/**
 * - Display Debug Information
 */
include_once('debug/footer.php');

/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>