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
 * PHP Mailer
 * @link http://phpmailer.sourceforge.net/ 
 */
 

/**
 * - Set debug mode
 */
$debug_page = false;

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ------------- START DATABASE CONNECTIONS --------------------- */
$sql = $dbh->prepare("SELECT p.id, p.purpose, p.req, p.reqDate, a.app1, a.app1Date, a.app2, a.app2Date, a.app3, a.app3Date, a.app4, a.app4Date, a.issuer, a.issuerDate,
					   TO_DAYS(NOW()) - TO_DAYS(p.reqDate) AS curReq,
					   TO_DAYS(NOW()) - TO_DAYS(a.app1Date) AS curApp1,
					   TO_DAYS(NOW()) - TO_DAYS(a.app1Date) AS curApp1Issuer,
					   TO_DAYS(NOW()) - TO_DAYS(a.app2Date) AS curApp2Issuer,
					   TO_DAYS(NOW()) - TO_DAYS(a.app3Date) AS curApp3Issuer,
					   TO_DAYS(NOW()) - TO_DAYS(a.app4Date) AS curApp4Issuer
					  FROM Authorization a, PO p
					  WHERE p.id = a.type_id and p.reqDate > DATE_SUB(NOW(), INTERVAL 60 DAY) and p.po IS NULL and p.status <> 'C'");					 
$sth = $dbh->execute($sql);

/* ------------- END DATABASE CONNECTIONS --------------------- */

/* ------------------ START FUNCTIONS ----------------------- */	
function sendMail($sendTo,$PO_Level,$PO_ID,$PURPOSE,$DAYS,$REQUESTER) {
	global $default;
	
	/* Set Variables */
	$PURPOSE = ucwords(strtolower($PURPOSE));
	$DAYS = ucwords(strtolower($DAYS));
	$REQUESTER = ucwords(strtolower($REQUESTER));
	$URL = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID."&approval=".$PO_Level;
	$URL2 = $default['URL_HOME']."/PO/cancelSlip.php?id=".$PO_ID;

	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	if ($debug_page) {
		$mail->AddAddress($default['debug_email']);
	} else {
		$mail->AddAddress($sendTo);
	}
	$mail->Subject = "PO REMINDER: ".$PURPOSE;
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
The Request was submitted to you $DAYS days ago<br>
The purpose for this PO is: $PURPOSE<br>
<br>
URL: <a href="$URL">$URL</a><br>
<br>
<br>
NOTE: If the Request is not valid anymore, contact<br>
$REQUESTER to cancel the Request.<br>
<br>
URL: <a href="$URL2">$URL2</a>
</body>
</html>
END_OF_HTML;

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
	   echo "Message was not sent";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}
/* ------------------ END FUNCTIONS ----------------------- */	



while($sth->fetchInto($PO)) {
  	/* Calculate the remander */
	if (is_null($PO['app1Date']) and $PO['curReq'] >= 2 and $PO['curReq'] % 2 == 0) {
 		$APP = $dbh->getRow("SELECT eid, email
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[app1]"));
		$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[req]"));					 
		sendMail($APP['email'],'app1',$PO['id'],$PO['purpose'],$PO['curReq'],$REQ['name']);
		if ($debug_page) {
			echo "EMAIL: ".$APP['email']."\n APP1\n ID: ".$PO['id']."\n PURPOSE: ".$PO['purpose']."\n REQ: ".$PO['curReq']."\n NAME: ".$REQ['name']."<br><br>";
		}
	} else if ((!is_null($PO['app2']) or $PO['app2'] != '0') and is_null($PO['app2Date']) and $PO['curApp1'] >= 2 and $PO['curApp1'] % 2 == 0) {
 		$APP = $dbh->getRow("SELECT eid, email
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[app2]"));
		$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[req]"));									 
		sendMail($APP['email'],'app2',$PO['id'],$PO['purpose'],$PO['curApp1'],$REQ['name']);
                if ($debug_page) {
                        echo "EMAIL: ".$APP['email']."\n APP2\n ID: ".$PO['id']."\n PURPOSE: ".$PO['purpose']."\n REQ: ".$PO['curReq']."\n NAME: ".$REQ['name']."<br><br>";
                }
	} else if (isset($PO['app2']) and $PO['app2'] != '0' and is_null($PO['issuerDate']) and $PO['curApp2Issuer'] >= 2 and $PO['curApp2Issuer'] % 2 == 0) {
 		$APP = $dbh->getRow("SELECT eid, email
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[issuer]"));	
		$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[req]"));						 
 		sendMail($APP['email'],'issuer',$PO['id'],$PO['purpose'],$PO['curApp2Issuer'],$REQ['name']);
                if ($debug_page) {
                        echo "EMAIL: ".$APP['email']."\n ISSUER\n ID: ".$PO['id']."\n PURPOSE: ".$PO['purpose']."\n REQ: ".$PO['curReq']."\n NAME: ".$REQ['name']."<br><br>";
                }
	} else if ($PO['curApp1Issuer'] >= 2 and $PO[curApp1Issuer] % 2 == 0) {
 		$APP = $dbh->getRow("SELECT eid, email
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[issuer]"));
		$REQ = $dbh->getRow("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees
							 WHERE eid = ?", array("$PO[req]"));									  
		sendMail($APP['email'],'issuer',$PO['id'],$PO['purpose'],$PO['curApp1Issuer'],$REQ['name']);	
                if ($debug_page) {
                        echo "EMAIL: ".$APP['email']."\n ISSUER\n ID: ".$PO['id']."\n PURPOSE: ".$PO['purpose']."\n REQ: ".$PO['curReq']."\n NAME: ".$REQ['name']."<br><br>";
                }
	}
}
?>
