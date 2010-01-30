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
/**
 * - Check User Access
 */
require_once('../security/check_user.php');


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting PO information */
$PO = $dbh->getRow("SELECT * FROM PO WHERE id = ?",array($_GET['type_id']));
/* Getting Authoriztions for above PO */
$EMPLOYEE = $dbh->getRow("SELECT eid, email FROM Standards.Employees WHERE eid = ?",array($_GET['eid']));						   		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */


/* ------------------ START FUNCTIONS ----------------------- */
function debug_var($section) {
	global $PO;
	global $AUTH;
	global $data;
	global $EMPLOYEE;
	global $default;
	
	echo $section;
	echo "From     = ".$default['email_from']."<br>";
	echo "FromName = ".$default['title1']."<br>";
	echo "Host     = ".$default['smtp']."<br>";
	echo "AddAddress= ".$EMPLOYEE['email']."<br>";
	echo '== $_SESSION<br>';	
	print_r($_SESSION);
	echo '<br>== $_POST<br>';
	print_r($_POST);
	echo '<br>== $_GET<br>';
	print_r($_GET);
	echo '<br>== $data<br>';
	print_r($data);		
	echo '<br>== $PO<br>';
	print_r($PO);	
	echo '<br>== $EMPLOYEE<br>';
	print_r($EMPLOYEE);		
	echo '<br>== $AUTH<br>';
	print_r($AUTH);	
	echo '<br>== $default<br>';
	print_r($default);	
}

sendResend($EMPLOYEE['email'], $_GET['approval'], $_GET['type_id'], $PO['purpose']);

if ($debug_page and $_SESSION['eid'] == '08745') {
	debug_var("RESEND SECTION<br><br>");
	exit();
} else {
	header('Location: detail.php?id='.$_GET['type_id']);
	exit();
}
			
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