<?php
/**
 * - Load Common Email Functions
 */
include_once('/var/www/Common/PHP/functionsEmail.php');	

/**
 * -------- Basic Message ---------------------------------------------------------------------------
 */	
function message2($BODY, $URL, $COMMENTS) {
	global $default;
	global $style;

$htmlBody = <<< END_OF_HTML
$style
</head>

<body>
<table width="640" border="0" align="center" cellspacing="0" cellpadding="0" >
  <tr>
    <td><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <br>
	  <blockquote>$BODY</blockquote>
	  <br>
	  <br>		
    </td>   
  <tr>
	<td class="header" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$default[title1] Link</b></td>
  </tr>
  <tr>		
    <td class="message">
	  <br>
	  <blockquote><a href="$URL">$URL</a></blockquote>
	  <br>
    </td>
  </tr>
  <tr>
    <td class="header"><span class="header">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Comments</strong></span></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <blockquote>$COMMENTS</blockquote>
	  <br>
    </td>
  </tr>
</table>
</body>
</html>
END_OF_HTML;

	return $htmlBody;
}

function message3($BODY, $URL, $URL2, $COMMENTS) {
	global $default;
	global $style;

$htmlBody = <<< END_OF_HTML
$style
</head>

<body>
<table width="640" border="0" align="center" cellspacing="0" cellpadding="0" >
  <tr>
    <td><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <br>
	  <blockquote>$BODY</blockquote>
	  <br>
	  <br>		
    </td>   
  <tr>
	<td class="header" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$default[title1] Link</b></td>
  </tr>
  <tr>		
    <td class="message">
	  <br>
	  <blockquote>
	    <b>Details:</b> <a href="$URL">$URL</a><br>
		<b>Queue:</b> <a href="$URL2">$URL2</a>
	  </blockquote>
	  <br>
    </td>
  </tr>
  <tr>
    <td class="header"><span class="header">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Comments</strong></span></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <blockquote>$COMMENTS</blockquote>
	  <br>
    </td>
  </tr>
</table>
</body>
</html>
END_OF_HTML;

	return $htmlBody;
}

function message4($BODY, $URL, $URL2) {
	global $default;
	global $style;

$htmlBody = <<< END_OF_HTML
$style
</head>

<body>
<table width="640" border="0" align="center" cellspacing="0" cellpadding="0" >
  <tr>
    <td><img src="$default[URL_HOME]/images/email_header.gif" width="646" height="74"></td>
  </tr>
  <tr>
    <td class="message">
	  <br>
	  <br>
	  <blockquote>$BODY</blockquote>
	  <br>
	  <br>		
    </td>   
  <tr>
	<td class="header" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$default[title1] Link</b></td>
  </tr>
  <tr>		
    <td class="message">
	  <br>
	  <blockquote>
	    <b>Details:</b> <a href="$URL">$URL</a><br>
		<b>Queue:</b> <a href="$URL2">$URL2</a>
	  </blockquote>
	  <br>
    </td>
  </tr>
</table>
</body>
</html>
END_OF_HTML;

	return $htmlBody;
}
/* ------------------ START EMAIL FUNCTIONS ----------------------- */


/**
 * -------- Send out email for approval -----------------------------------------------
 */		
function sendMail($sendTo,$PO_Level,$PO_ID,$purpose) {
	global $default;
	global $COMMENTS;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];	
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "Requisition ".$PO_ID.": ".$purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
You have a new Purchase Requisition to review.<br>
The purpose for this Requisition is: <b>$purpose</b><br>
END_OF_HTML;

/* Request URL */
//$url = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID."&approval=".$PO_Level;
$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID."/".$PO_Level;

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
END_OF_HTML;

	$htmlBody = message2($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	//End sendMail


/**
 * -------- Send out email to Purchasing -----------------------------------------------
 */		
function sendPurchasing($PO_ID,$purpose) {
	global $default;
	global $COMMENTS;
	global $dbh;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];
					  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";

	/* Get Purchasing Group */
	$sth = $dbh->query("SELECT e.email, CONCAT(e.fst , ' ' , e.lst) AS fullname
						FROM Users u
						  INNER JOIN Standards.Employees e ON e.eid=u.eid
						WHERE u.status = '0' 
						  AND e.status = '0'
						  AND u.eid <> '08745'
						  AND u.role = 'purchasing'");	
	
	/* Email each Purchaser */					  	
	while ($data = $sth->fetchRow()) {
		$mail->AddAddress($data['email'], $data['fullname']);
		$mail->Subject = "Requisition " . $PO_ID . ": " . $purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
A newly approved Purchase Requisition has been added to the queue.<br>
The purpose for Requisition <b>$PO_ID</b> is: <b>$purpose</b><br>
END_OF_HTML;

/* Request URL */
//$url  = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID;
$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;
$url2 = $default['URL_HOME']."/PO/list.php";

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
END_OF_HTML;

		$htmlBody = message3($message_body, $url, $url2, $comments);	
	
		$mail->Body = $htmlBody;
		$mail->isHTML(true);
		
		if(!$mail->Send())
		{
			echo "Failed to send email to: " . $data['email'];
		}
		
		// Clear all addresses and attachments for next loop
		$mail->ClearAddresses();
		$mail->ClearAttachments();
	}
}	//End sendPurchasing


/**
 * -------- Send out email to Purchasing -----------------------------------------------
 */		
function sendPurchasingHOT($PO_ID,$purpose) {
	global $default;
	global $dbh;
					  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->Priority = 1;

	$sth = $dbh->query("SELECT e.email, CONCAT(e.fst , ' ' , e.lst) AS fullname
						FROM Users u
						  INNER JOIN Standards.Employees e ON e.eid=u.eid
						WHERE u.status = '0' 
						  AND e.status = '0'
						  AND u.eid <> '08745'
						  AND u.role = 'purchasing'");	
						  	
	while ($data = $sth->fetchRow()) {
		$mail->AddAddress($data['email'], $data['fullname']);
		$mail->Subject = "HOT Requisition " . $PO_ID . ": " . $purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
A new Purchase Requisition tagged HOT has been submitted.<br>
The purpose for Requisition <b>$PO_ID</b> is: <b>$purpose</b><br>
END_OF_HTML;

/* Request URL */
//$url  = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID;
$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;
$url2 = $default['URL_HOME']."/PO/list.php";

		$htmlBody = message4($message_body, $url, $url2);	
	
		$mail->Body = $htmlBody;
		$mail->isHTML(true);
		
		if(!$mail->Send())
		{
			echo "Failed to send email to: " . $data['email'] . "<br>";
		}
		
		// Clear all addresses and attachments for next loop
		$mail->ClearAddresses();
		$mail->ClearAttachments();
	}
}	//End sendPurchasingHOT


/**
 * -------- Send denied email ----------------------------------------------- 
 */	
function sendDeny($sendTo,$PO_ID,$purpose) {
	global $default;
	global $COMMENTS;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];	
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "PO DENIED: ".$purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
Purchase Requisition <b>$PO_ID</b>has been <i>DENIED</i>.<br>
The purpose for this Requisition is: <b>$purpose</b><br>
END_OF_HTML;

/* Request URL */
//$url = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID;
$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
END_OF_HTML;

	$htmlBody = message2($message_body, $url, $comments);		

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	//End sendDeny


/** 
 * -------- Send approved email -----------------------------------------------
 */	
function sendApproved($sendTo,$PO_ID,$purpose,$PONum) {
	global $default;
	global $COMMENTS;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];	
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "PO ".$PONum.": ".$purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
Purchase Requisition <b>$PO_ID</b> has been approved.<br>
The purpose for this Requisition is: <b>$purpose</b><br>
END_OF_HTML;

/* Request URL */
//$url = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID;
$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
END_OF_HTML;

	$htmlBody = message2($message_body, $url, $comments);		

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	//End sendApproved

/** 
 * -------- Send approved email -----------------------------------------------
 */	
function sendApproved2($sendTo,$PurchaserName,$PurchaserEmail,$PO_ID,$purpose,$PONum) {
	global $default;
	global $COMMENTS;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];	
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "PO ".$PONum.": ".$purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
Purchase Requisition <b>$PO_ID</b> has been approved.<br>
The purpose for this Requisition is: <b>$purpose</b><br>
<br>
<a href="mailto:$PurchaserEmail">$PurchaserName</a>, from the Purchasing Department, has been assigned this Request.<br>
END_OF_HTML;

/* Request URL */
//$url = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID;
$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
END_OF_HTML;

	$htmlBody = message2($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	//End sendApproved

/** 
 * -------- Send Completed (Vendor Kickoff) email -----------------------------------------------
 */	
function sendCompleted($requisitioner,$PO_ID,$purpose,$PONum,$purchaser) {
	global $default;

	/* Get/Set Requisitioner information */
	$RequisitionerName=getEmployee($requisitioner);
	$RequisitionerFullname=caps($RequisitionerName['fst'] . ' ' . $RequisitionerName['lst']);
	$sendTo=$RequisitionerName['email'];

	/* Get/Set Purchaser information */
	$PurchaserName=getEmployee($requisitioner);
	$PurchaserFullname=caps($PurchaserName['fst'] . ' ' . $PurchaserName['lst']);
		
/* Email message */			
$message_body = <<< END_OF_HTML
Purchase Requisition Number <b>$PO_ID</b> has been completed by <a href="$default[URL_HOME]/PO/comments.php?action=comment&eid=$purchaser&request_id=$PO_ID&type=private">$PurchaserFullname</a>.<br>
The Purchase Order Number for this Requisition is <b>$PONum</b> and has been sent to the vendor.<br>
<br>
The purpose for this Requisition is: <b>$purpose</b><br>
END_OF_HTML;

	/* Request URL */
	//$url = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID;
	$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo, $RequisitionerFullname);
	$mail->Subject = "Vendor Kickoff for Requisition ".$PO_ID.": ".$purpose;

	$htmlBody = message1($message_body, $url);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	if(!$mail->Send())
	{
		echo "Failed to send email to: " . $sendTo . "<br>";
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}	//End sendCompleted


/** 
 * -------- Send approved email -----------------------------------------------
 */	
function sendNotify($sendTo, $message) {
	global $default;
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = $default['title1'] . " Notification";

	$message_body=$message;
	
	$url = $default['URL_HOME'];
	
	$htmlBody = message1($message_body, $url);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		echo "Failed to send email to: " . $sendTo . "<br>";
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}


/** 
 * -------- Resend approved email -----------------------------------------------
 */	
function sendResend($sendTo,$PO_Level,$PO_ID,$purpose) {
	global $default;
	global $COMMENTS;
	
	$app1 = $COMMENTS[app1][0];
	$app1Com = $COMMENTS[app1][1];
	$app2 = $COMMENTS[app2][0];
	$app2Com = $COMMENTS[app2][1];
	$app3 = $COMMENTS[app3][0];
	$app3Com = $COMMENTS[app3][1];
	$app4 = $COMMENTS[app4][0];
	$app4Com = $COMMENTS[app4][1];	
				  
	// ---------- Start Email Comment
	require_once("phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	
	$mail->From     = $default['email_from'];
	$mail->FromName = $default['title1'];
	$mail->Host     = $default['smtp'];
	$mail->Mailer   = "smtp";
	$mail->AddAddress($sendTo);
	$mail->Subject = "Requisition ".$PO_ID.": ".$purpose;

/* Email message */			
$message_body = <<< END_OF_HTML
You have a new Purchase Requisition to review.<br>
The purpose for this Requisition is: <b>$purpose</b><br>
END_OF_HTML;

	/* Request URL */
	//$url = $default['URL_HOME']."/PO/detail.php?id=".$PO_ID."&approval=".$PO_Level;
	$url = $default['URL_HOME']."/u.php?q=1/".$PO_ID."/".$PO_Level;

/* Request comments */
$comments = <<< END_OF_HTML
$app1 &quot;$app1Com&quot;<br>
$app2 &quot;$app2Com&quot;<br>
$app3 &quot;$app3Com&quot;<br>
$app4 &quot;$app4Com&quot;<br>
END_OF_HTML;

	$htmlBody = message2($message_body, $url, $comments);	

	$mail->Body = $htmlBody;
	$mail->isHTML(true);
	
	if(!$mail->Send())
	{
		$_SESSION['error'] = "There is a problem with the email server.  Your<br>information was saved but no emails where sent out.<br>Pick the &quot;Return Home&quot; button";
		header("Location: ../error.php");
		exit();
	}
	
	// Clear all addresses and attachments for next loop
	$mail->ClearAddresses();
	$mail->ClearAttachments();
}
/* ------------------ END FUNCTIONS ----------------------- */
?>