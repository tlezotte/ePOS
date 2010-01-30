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



switch ($_GET['db']) {
	case 'cer':
		/* ------------------ CER database ----------------------- */
		$query = "SELECT id, cer, purpose
				  FROM CER
				  WHERE cer IS NOT NULL
				  ORDER BY cer DESC";
		$major = 'ResultSet';
		$minor = 'Result';
	break;
	case 'items':
		/* ------------------ Items database ----------------------- */
		$query = "SELECT id, qty, descr AS description, price, unit, part AS Company, manuf AS manufacture
				  FROM Items
				  WHERE type_id=$_GET[id]
				  ORDER BY id DESC";
		$major = 'items';
		$minor = 'item';				  
	break;
	case 'files':
		/* ------------------ FileFolder database ----------------------- */
		$query = "SELECT *, DATE_FORMAT(file_date,'%b %d, %Y') AS format_file_date
				  FROM FileFolder
				  WHERE type_id=$_GET[id]
				  ORDER BY file_id DESC";
		$major = 'files';
		$minor = 'file';				  
	break;
	case 'history':
		/* ------------------ History database ----------------------- */
		$query = "SELECT *, CONCAT(e.lst, ', ', e.fst) AS fullname
				  FROM History h
				    INNER JOIN Standards.Employees e ON e.eid=h.eid
				  WHERE type_id=$_GET[id]
				  ORDER BY ts DESC";
		$major = 'history';
		$minor = 'sql';				  
	break;
	case 'tracking':
		/* ------------------ Tracking database ----------------------- */
		$query = "SELECT *
				  FROM TrackShipments
				  WHERE type_id=$_GET[id]
				  ORDER BY track_id ASC";
		$major = 'tracking';
		$minor = 'shipment';				  
	break;			
	default:
		$data = array('error' => 'Improper URL - no database found.');
	break;	
}	
$data = $dbh->getAll($query);

if ($_GET['output'] == 'json') {
	require_once 'json/JSON.php';
	
	$json = new Services_JSON();
	$output = '{"' . $major . '":{"' . $minor . '":' . $json->encode($data) . '}}';
} else {
	require_once('minixml/minixml.inc.php');
	
	$xmlDoc = new MiniXMLDoc();
	$xmlArray = array();
	
	$xmlArray[$major][$minor] = array();
	array_push($xmlArray[$major][$minor], $data);

	$xmlDoc->fromArray($xmlArray);
	$output = $xmlDoc->toString();
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