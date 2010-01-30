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


$count = 0;															// Reset record count
$tableDestination = "Units";										// Destination (remote) table
$tableSource = "CODELF5";												// Source (Local) table

if ($_GET['html'] == 'on') {
	echo "Server: " . $default['server'] . "<br>";
	echo "Database: " . $default['database'] . "<br>";
	echo "Table: " . $tableDestination . "<br><br>";
}


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
$sql="SELECT * FROM company." . $tableSource;										// Get all rows from VEND
$rs=odbc_exec($conn,$sql);
if (!$rs) { exit(0); }																	// Connection Failed 
   
/**
 * - Process each record
 */
while(odbc_fetch_row($rs)) {
	$count++;		
	$vendor_data = "";																					// Reset variable
	for($i=3;$i<=odbc_num_fields($rs);$i++) { 
	   $field = odbc_result($rs,$i);  																	// Getting each FIELD from ROW
	   $vendor_data .= "'".htmlentities(trim($field), ENT_QUOTES, 'UTF-8')."',";								// Prepping SQL data
	 }
	$vendor_data2 = preg_replace("/\,$/", "", $vendor_data);											// Remove last comma from SQL data
	$vendor_sql = "INSERT INTO ".$tableDestination." (name, id) VALUES (" . $vendor_data2 . ")";
	
	if ($debug_page) { 
		echo $vendor_sql . "<br>"; 
	} else {
		$res = $dbh_standards->query($vendor_sql);
		if (PEAR::isError($res)) { echo "Insert Record (Full): " . $res->getMessage() . "<br>"; }	
	}
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
