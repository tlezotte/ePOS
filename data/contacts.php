<?php
/**
 * Request System
 *
 * list.php displays available PO.
 *
 * @version 1.5
 * @link https://hr.Company.com/go/HCR/
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



/* ------------------ START DATABASE CONNECTIONS ----------------------- */
$sql = "SELECT * 
		FROM Contacts 
		WHERE request_id=" . $_GET['id'] . " 
		ORDER BY name DESC";
$query = $dbh->prepare($sql);
$sth = $dbh->execute($query);

$vendor_sql = "SELECT id, BTCONT AS name, `BTTEL#` AS phone, `BTFAX#` AS fax, BTEMAL AS email
			   FROM PO p 
			   INNER JOIN Standards.Vendor v ON v.BTVEND=p.sup
			   WHERE p.id=" . $_GET['id'];
$VENDOR = $dbh->getRow($vendor_sql);

if ($debug_page) { echo $sql . "<br>" . $vendor_sql; }					 		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */


/* ------------------ START VARIABLES ----------------------- */
$phone_char=array('(', ')', ' ', '-', '.');
$format_phone="(000)000-0000";
/* ------------------ END VARIABLES ----------------------- */


if ($_GET['output'] == 'json') {


} else {

	header('Content-type: text/xml');
	header('Pragma: public');     
	header('Cache-control: private');
	header('Expires: -1');
	
	$vendorExt = (empty($VENDOR['ext'])) ? "" : "x" . $VENDOR['ext'];
	
	$output .= "<contacts>\n";
	$output .= "    <contact>\n";
	$output .= "        <id>" . $VENDOR['id'] . "</id>\n";		
	$output .= "        <name>" . caps($VENDOR['name']) . "</name>\n";	
	$output .= "        <phone>" . str_format_number(str_replace($phone_char, '', $VENDOR['phone']), $format_phone) . "</phone>\n";
	$output .= "        <ext>" . $vendorExt . "</ext>\n";
	$output .= "        <fax>" . str_format_number(str_replace($phone_char, '', $VENDOR['fax']), $format_phone) . "</fax>\n";
	$output .= "        <email><![CDATA[" . $VENDOR['email'] . "]]></email>\n";
	$output .= "        <status>0</status>\n";
	$output .= "        <source>cms</source>\n";	
	$output .= "    </contact>\n";
			
	while($sth->fetchInto($DATA)) {
		$ext = (empty($DATA['ext'])) ? "" : "x" . $DATA['ext'];
	
		$output .= "    <contact>\n";
		$output .= "        <id>" . $DATA['id'] . "</id>\n";		
		$output .= "        <name>" . caps($DATA['name']) . "</name>\n";	
		$output .= "        <phone>" . str_format_number(str_replace($phone_char, '', $DATA['phone']), $format_phone) . "</phone>\n";
		$output .= "        <ext>" . $ext . "</ext>\n";
		$output .= "        <fax>" . str_format_number(str_replace($phone_char, '', $DATA['fax']), $format_phone) . "</fax>\n";
		$output .= "        <email><![CDATA[" . $DATA['email'] . "]]></email>\n";
		$output .= "        <status>" . $DATA['status'] . "</status>\n";
		$output .= "        <source>local</source>\n";	
		$output .= "    </contact>\n";
	}
	
	$output .= "</contacts>\n";
	
}
        
print $output;
?>


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