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
include_once('debug/header.php');

/**
 * - ODBC Database Connection
 */
require('../Connections/ODBCos400.php');
/**
 * - Database Connection
 */
require('../Connections/connStandards.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


$count = 0;																// Reset record count
$tableDestination = "COA";										// Destination (remote) table
$tableSource = "MAST";												// Source (Local) table

if ($_GET['html'] == 'on') {
	echo "Server: " . $default['server'] . "<br>";
	echo "Database: " . $default['database'] . "<br>";
	echo "Table: " . $tableDestination . "<br><br>";
}


/* ---- FULL BACKUP AND INSTALL ---- */
if ($_GET['b'] != 'off') {
	$full_path = $default['UPLOAD'].'/'.$default['database'].'_'.$tableDestination.'_'.date("Ymd").'.sql';
	/* ----- Dump Current state of database ----- */
	system('/usr/bin/mysqldump --databases '.$default['database'].' --tables '.$tableDestination.' -l -h '.$default['server'].' -u '.$default['username'].' -p'.$default['password'].' > '.$full_path);
	system('/bin/gzip ' . $full_path);
}

/**
 * - Clean out current local Vendor DB
 */	
$res = $dbh_standards->query("TRUNCATE TABLE " . $tableDestination);
if (PEAR::isError($res)) { echo "Empty Table: " . $res->getMessage() . "<br>"; }
			
/**
 * - Getting Data from AS/400
 */							
//$sql="SELECT * FROM company.VEND FETCH FIRST 10 ROWS ONLY";	
$sql="SELECT * FROM company." . $tableSource;							// Get all rows from VEND
$rs=odbc_exec($conn,$sql);
if (!$rs) { exit(0); }														// Connection Failed 
   
/**
 * - Process each record
 */
while(odbc_fetch_row($rs)) {
	$count++;		
	$vendor_data = "";														// Reset variable
	for($i=2;$i<=6;$i++) { 
	   $field = odbc_result($rs,$i); 										// Getting each FIELD from ROW
	   switch ($i) {
	   	case '2':
			$plant = '0' . substr($field, 0, 1);							// Generate plant code
			$department = substr($field, 1, 2);								// Generate department code
			$vendor_data .= "'".$plant."', '".$department."', ";			// Prepping SQL data
		break;
		case '3':
			$cat = '0' . substr($field, 0, 3);								// Generate category code
			$sub = substr($field, 3, 2);									// Generate sub-category code
			$vendor_data .= "'".$cat."', '".$sub."', ";						// Prepping SQL data		
		break;
		case '5':
			$vendor_data .= "'".$field."', ";								// Prepping SQL data
		break;
		case '6':
			$vendor_data .= "'".$field."'";									// Prepping SQL data
		break;
	   }
	 }

	$vendor_sql = "INSERT INTO ".$tableDestination." (coa_plant, coa_department, coa_account, coa_suffix, coa_description, coa_type) VALUES (" . $vendor_data . ")";
	$res = $dbh_standards->query($vendor_sql);
	if (PEAR::isError($res)) { echo "Insert Record: " . $res->getMessage() . "<br>"; }
	
	if ($debug_page) { echo $vendor_sql . "<br>"; }
}


if ($_GET['html'] == 'on') {
	echo "Records: " . $count . "<br>";
}

/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh_standards->disconnect();
/**
 * - Disconnect from ODBC database
 */
odbc_close($conn);
odbc_close_all();


/**
 * - Redirect
 */
if ($_GET['html'] == 'on') {
	header("Location: " . $_SERVER['HTTP_REFERER']);
	exit();
}
?>
