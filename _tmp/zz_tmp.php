<?php
/**
 * Request System
 *
 * vendor.php get vendor data from AS/400.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
 * @filesource
 */
 

/**
 * - Set debug mode
 */
$debug_page = false;
include_once('../debug/header.php');

/**
 * - ODBC Database Connection
 */
require('../Connections/ODBCos400.php');
/**
 * - Database Connection
 */
require('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


$currentDate = date("Y-m-d");


/**
 * - $_Get Information
 */		
$result = odbc_exec($conn, "INSERT INTO ZZ_TEST.PORQI (JIREQ#, JIQDAT, JIQTYO, JIOUNT, JIPT#, JIUPRC, JIPUNT, JIREQR, JIAPRV, JIVND#, JIVNAM, JIVPT#, JISTS, JIITM#, JIUSER, JIISTR, JIAPBY, JICRCM, JITAXG, JITAXR, JIBUYR, JIPLNT) 
											   VALUES (311, '".$currentDate."', 1.00, 'EA', 'XXX', 5.00, 'EA', 'EXT', 'Y', 'AC0018', 'Ace Tex Corporation', 'XY123', 'N', 1, 'LEZOTTET', 'STX', 'SHOEJ', '1', 'EXP', '0', 'AS', 'DFT')");
									   
if ($result == FALSE) {
	echo odbc_errormsg();
}

$result2 = odbc_exec($conn, "INSERT INTO ZZ_TEST.PORQD VALUES (311, 1, 'This is for notepad')");		
if ($result2 == FALSE) {
	echo odbc_errormsg();
}


/**
 * - Display Debug Information
 */
include_once('../debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
/**
 * - Disconnect from ODBC database
 */
odbc_close($conn);


/**
 * - Disconnect from ODBC database
 */
if ($_GET['html'] == 'on') {
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit();
}
?>
