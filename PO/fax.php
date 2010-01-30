<?php
/**
 * - PDF FDF processor
 */
require_once('pdf/forge_fdf.php');
/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* Update Summary */
Summary($dbh, 'Fax Cover Sheet', $_SESSION['eid']);


/* Getting suppliers from Suppliers */						 
$SUPPLIER = $dbh->getRow("SELECT  BTNAME AS name, BTCONT AS contact, `BTFAX#` AS fax
						  FROM Standards.Vendor 
						  WHERE BTVEND='" . $_GET['id'] . "'");

/* Getting Employee information */						 
$EMPLOYEE = $dbh->getRow("SELECT *
						  FROM Standards.Employees
						  WHERE eid = ".$_SESSION['eid']);	
  									   

$pdf_form_url=$default['URL_HOME'] . "/Common/FaxCoverSheet.pdf";		// PDF Template
 
$fdf_data_strings=array('date' => date("F j, Y"), 
						'from' => ucwords(strtolower($EMPLOYEE['fst'])).' '.ucwords(strtolower($EMPLOYEE['lst'])),
						'to' => ucwords(strtolower($SUPPLIER['contact'])),
						'company' => ucwords(strtolower($SUPPLIER['name'])),
						'fax' => $SUPPLIER['fax'],
						'pages' => '',
						'message' => '');
$fdf_data_names=array();

$fields_hidden=array();
$fields_readonly=array('date','from','to','company','fax','pages','message');

header( 'content-type: application/vnd.fdf' );

echo forge_fdf( $pdf_form_url,
		$fdf_data_strings, 
		$fdf_data_names,
		$fields_hidden,
		$fields_readonly );
?>
