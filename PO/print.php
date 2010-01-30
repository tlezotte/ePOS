<?php
/**
 * Request System
 *
 * PO.php generate a xdp file for PDF.
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
 * PDF Toolkit
 * @link http://www.accesspdf.com/
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

/* -------------------------------------------------------------				
 * ------------- START DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */
/* ------------- Getting PO information ------------- */
$PO = $dbh->getRow("SELECT *, p.id AS _id, DATE_FORMAT(p.reqDate,'%M %e, %Y') AS _reqDate
				    FROM PO p
					  INNER JOIN Standards.Vendor v ON v.BTVEND=p.sup
					  INNER JOIN Standards.VendorTerms t ON t.terms_id=p.terms
					  INNER JOIN Standards.Plants l ON l.id=p.ship
					  INNER JOIN Authorization a ON a.type_id=p.id
				    WHERE p.id = ?",array($_GET['id']));
/* ------------- Get Employee names from Standards database ------------- */
$EMPLOYEES = $dbh->getAssoc("SELECT eid, CONCAT(fst,' ',lst) AS name
							 FROM Standards.Employees");
/* ------------- Get items related to this Request -------------*/
$items_sql = "SELECT * FROM Items WHERE type_id = " . $PO['_id'];
$items_query = $dbh->prepare($items_sql);						   
$items_sth = $dbh->execute($items_query);
$items_count = $items_sth->numRows();					 																	 				  	
/* -------------------------------------------------------------				
 * ------------- END DATABASE CONNECTIONS -------------------
 * -------------------------------------------------------------
 */


$row=0;
$total=0;
$phone_char=array('(', ')', ' ', '-', '.');
$format_phone="(000)000-0000";
$TEMPLETE = ($PO['hot'] == 'yes') ? $default['po_templete_hot'] : $default['po_templete'];

if ($_GET['output'] == 'pdf') {
	header('Content-type: application/vnd.adobe.xdp+xml');
	header('Pragma: public');        
	header('Cache-control: private');
	header('Expires: -1');
}

$output  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$output .= "<?xfa generator=\"XFA2_4\" APIVersion=\"2.6.7116.0\"?>\n";
$output .= "<xdp:xdp xmlns:xdp=\"http://ns.adobe.com/xdp/\">\n";
$output .= "<xfa:datasets xmlns:xfa=\"http://www.xfa.org/schema/xfa-data/1.0/\">\n";
$output .= "  <xfa:data>\n";
$output .= "	<topmostSubform>\n";
$output .= "		<id>" . $PO['_id'] . "</id>\n";
$output .= "		<po>" . $PO['po'] . "</po>\n";
$output .= "		<orderDate>" . $PO['issuerDate'] . "</orderDate>\n";
$output .= "		<dueDate>" . $PO['dueDate'] . "</dueDate>\n";
$output .= "		<vendor><![CDATA[" . caps($PO['BTNAME']) . "]]></vendor>\n";
$output .= "		<vendorAddress1>" . caps($PO['BTADR1']) . "</vendorAddress1>\n";
$output .= "		<vendorAddress2>" . caps($PO['BTADR2']) . "</vendorAddress2>\n";
$output .= "		<vendorCity>" . caps($PO['BTADR3']) . "</vendorCity>\n";
$output .= "		<vendorState>" . strtoupper($PO['BTPRCD']) . "</vendorState>\n";
$output .= "		<vendorZIP>" . $PO['BTPOST'] . "</vendorZIP>\n";
$output .= "		<vendorFOB></vendorFOB>\n";
$output .= "		<vendorTerms>" . caps($PO['terms_name']) . "</vendorTerms>\n";
$output .= "		<vendorShipVia>" . $PO['via'] . "</vendorShipVia>\n";
$output .= "		<vendorPhone>" . str_format_number(str_replace($phone_char, '', $PO['BTTEL#']), $format_phone) . "</vendorPhone>\n";
$output .= "		<vendorFax>" . str_format_number(str_replace($phone_char, '', $PO['BTFAX#']), $format_phone) . "</vendorFax>\n";
$output .= "		<vendorContact>" . caps($PO['BTCONT']) . "</vendorContact>\n";
$output .= "		<requisitioner>" . caps($EMPLOYEES[$PO['req']]) . "</requisitioner>\n";
$output .= "		<address1>" . caps($PO['address']) . "</address1>\n";
$output .= "		<city>" . caps($PO['city']) . "</city>\n";
$output .= "		<state>" . strtoupper($PO['state']) . "</state>\n";
$output .= "		<ZIP>" . $PO['zip5'] . "</ZIP>\n";
$output .= "		<FOB>" . $PO['fob'] . "</FOB>\n";
$output .= "		<terms></terms>\n";
$output .= "		<shipVia>" . $PO['via'] . "</shipVia>\n";
$output .= "		<phone>" . str_format_number(str_replace($phone_char, '', $PO['phone']), $format_phone) . "</phone>\n";
$output .= "		<fax>" . str_format_number(str_replace($phone_char, '', $PO['fax']), $format_phone) . "</fax>\n";
$output .= "		<buyer>" . caps($EMPLOYEES[$PO['issuer']]) . "</buyer>\n";

while($items_sth->fetchInto($ITEMS)) {
	$row++;
	$d=(substr($ITEMS['price'], -2, 2) == '00') ? 2 : 4;
	$itemTotal=$ITEMS['qty'] * $ITEMS['price'];

	$output .= "		<qty" . $row . ">" . $ITEMS['qty'] . "</qty" . $row . ">\n";
	$output .= "		<unit" . $row . ">" . $ITEMS['unit'] . "</unit" . $row . ">\n";
	$output .= "		<Company" . $row . ">" . $ITEMS['part'] . "</Company" . $row . ">\n";
	$output .= "		<mfg" . $row . ">" . $ITEMS['manuf'] . "</mfg" . $row . ">\n";
	$output .= "		<item" . $row . ">" . caps($ITEMS['descr']) . "</item" . $row . ">\n";
	$output .= "		<price" . $row . ">$" . number_format($ITEMS['price'],$d) . "</price" . $row . ">\n";
	$output .= "		<ext" . $row . ">$" .  number_format($itemTotal, 2) . "</ext" . $row . ">\n";

	$total += $itemTotal;	
	$itemTotal=0;
}

$output .= "		<total>$" . number_format($total, 2) . "</total>\n";
$output .= "	</topmostSubform>\n";
$output .= "  </xfa:data>\n";
$output .= "</xfa:datasets>\n";
$output .= "<pdf href=\"" . $TEMPLETE . "\" xmlns=\"http://ns.adobe.com/xdp/pdf/\"/>\n";
$output .= "</xdp:xdp>\n";


if ($_GET['output'] != 'pdf') {
	generate_xdp_file($PO['_id'], $output);
} else {
	print $output;
}


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>