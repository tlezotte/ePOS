<?php
/**
 * Employee List
 *
 * index.php is the search page for the Employee List.
 *
 * @version 0.1
 * @link http://www.yourdomain.com/go/Employees/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @global mixed $default[]
 * @filesource
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
require_once('../Connections/connStandards.php'); 
/**
 * --- CHECK USER ACCESS --- 
 */
require_once('../security/check_user.php');
/**
 * - Access to Request
 */
//require_once('../security/group_access.php');
/**
 * - Common Information
 */
require_once('../include/config.php'); 



/* SQL for access view */
if ($_GET['action'] == "my") {
	switch ($_GET['access']) {
		case '0':
			$access = "p.req";
		break;
		case '1':
			$access = "a.app1";
		break;
		case '2':
			$access = "a.app2";	
		break;
		case '3':
			$access = "a.app3";		
		break;
		case '4':
			$access = "a.app4";		
		break;
		case '5':
			$access = "a.controller";	
		break;
	}
}

/* SQL for different views of PO list */
if ($_GET['action'] == "my" AND $_GET['view'] == "all") {
	$where_clause = $access." like '".$_SESSION['eid']."'";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&access=".$_GET['access']);
	$view_gif = '../images/button.php?i=b90.png&l=View Open';
	$view_help = 'View all of My Open Requests';
} elseif ($_GET['view'] == "all") {
	$where_clause = "p.status <> 'C'";
	$view_all = $_SERVER['PHP_SELF'];
	$view_gif = '../images/button.php?i=b90.png&l=View Open';
	$view_help = 'View all Open Requests';
} elseif ($_GET['action'] == "my") {
	if ($access == "p.req") {
		$where_clause = $access." like '".$_SESSION['eid']."' AND p.po IS NULL";
	} else {
		$where_clause = $access." like '".$_SESSION['eid']."' AND ".$access."Date IS NULL";	
	}
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&view=all&access=".$_GET['access']);
	$view_gif = '../images/button.php?i=b90.png&l=View All';
	$view_help = 'View all of My Requests';
} else {
	$where_clause = "p.po IS NULL";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?view=all");
	$view_gif = '../images/button.php?i=b90.png&l=View All';
	$view_help = 'View all Requests';
}

/* Setting up Status view */
switch ($_GET['status']) {
case N:
   $where_status = "AND p.status = 'N'";
   break;
case A:
   $where_status = "AND p.status = 'A'";
   break;
case O:
   $where_clause = "p.po IS NOT NULL";
   $where_status = "AND p.status = 'O'";
   break;
case R:
   $where_status = "AND p.status = 'R'";
   break;
case X:
   $where_status = "AND p.status = 'X'";
   break;
case C:
   $where_status = "AND p.status = 'C'";
   break;  
default:
   $where_status = "AND p.status NOT IN ('X', 'C')";
   break;           
}

/* SQL for PO list */
$po_sql = "SELECT *, p.id AS _ID, DATE_FORMAT(p.reqDate, '%b %d, %Y') AS _DATE, DATE_FORMAT(p.reqDate, '%H:%i') AS _TIME
		   FROM PO p
			   INNER JOIN Authorization a ON p.id=a.type_id
		   WHERE $where_clause $where_status AND p.private = 'no'
		   ORDER BY p.id DESC";
$po_query = $dbh->prepare($po_sql);
$po_sth = $dbh->execute($po_query);
$num_rows = $po_sth->numRows();

$TERMS = $dbh->getAssoc("SELECT terms_id AS id, terms_name AS name FROM Standards.VendorTerms");
$DEPT = $dbh->getAssoc("SELECT id, name FROM Standards.Department");

/* Get Plants and Employees from Stanards database */
$SUPPLIER = $dbh->getAssoc("SELECT BTVEND AS id, BTNAME AS name FROM Standards.Vendor");
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u
							   INNER JOIN Standards.Employees e ON e.eid = u.eid");	
$PLANT = $dbh->getAssoc("SELECT id, name FROM Standards.Plants");
$CER = $dbh->getAssoc("SELECT id, cer FROM CER");						 		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

function shortPurpose($purpose) {
	$output = htmlspecialchars(caps(substr(stripslashes($purpose), 0, 40)), ENT_QUOTES, 'UTF-8');
	if (strlen($purpose) >= 40) { 
		$output .= "..."; 
	}

	return $output;
}

$format_phone="(000)000-0000";

//echo $po_sql;

header('Content-type: text/xml');
header('Pragma: public');     
header('Cache-control: private');
header('Expires: -1');
//header('Expires: ' . date(DATE_RFC822, strtotime('+5 miutes')) . '');

$output .= "<requisitions>\n";

while($po_sth->fetchInto($DATA)) {
	$output .= "    <requisition id=\"" . $DATA['_ID'] . "\" po=\"" . $DATA['po'] . "\">\n";
	$output .= "        <id>" . $DATA['_ID'] . "</id>\n";	
	$output .= "        <po>" . $DATA['po'] . "</po>\n";	
	$output .= "        <requester eid=\"" . $DATA['req'] . "\" date=\"" . $DATA['_DATE'] . "\" time=\"" . $DATA['_TIME'] . "\">" . caps($EMPLOYEES[$DATA['req']]) . "</requester>\n";
	$output .= "        <incareof eid=\"" . $DATA['incareof'] . "\">" . caps($EMPLOYEES[$DATA['incareof']]) . "</incareof>\n";
	$output .= "        <billtoplant id=\"" . $DATA['plant'] . "\">" . caps($PLANT[$DATA['plant']]) . "</billtoplant>\n";
	$output .= "        <shiptoplant id=\"" . $DATA['ship'] . "\">" . caps($PLANT[$DATA['ship']]) . "</shiptoplant>\n";
	$output .= "        <department id=\"" . $DATA['department'] . "\">" . caps($DEPT[$DATA['department']]) . "</department>\n";
	$output .= "        <vendor id=\"" . $DATA['sup'] . "\"><![CDATA[" . caps($SUPPLIER[$DATA['sup']]) .  "]]></vendor>\n";
	$output .= "        <vendor2 id=\"" . $DATA['sup2'] . "\"><![CDATA[" . caps($SUPPLIER[$DATA['sup2']]) .  "]]></vendor2>\n";
	$output .= "        <fob><![CDATA[" . $DATA['fob'] . "]]></fob>\n";
	$output .= "        <terms><![CDATA[" . $TERMS[$DATA['terms']] . "]]></terms>\n";
	$output .= "        <job><![CDATA[" . $DATA['job'] . "]]></job>\n";
	$output .= "        <via><![CDATA[" . $DATA['via'] . "]]></via>\n";
	$output .= "        <duedate>" . $DATA['dueDate'] . "</duedate>\n";	
	$output .= "        <purpose short=\"" . shortPurpose($DATA['purpose']) . "\"><![CDATA[" . caps(stripslashes($DATA['purpose'])) . "]]></purpose>\n";
//	$output .= "        <items>\n";
//	$output .= "        	<item></item>\n";
//	$output .= "        </items>\n";
	$output .= "        <total>" . number_format($DATA['total'],2) . "</total>\n";
	$output .= "        <cer id=\"" . $DATA['cer'] . "\">" . $CER[$DATA['cer']] .  "</cer>\n";
	$output .= "        <creditcard>" . $DATA['creditcard'] . "</creditcard>\n";
	$output .= "        <private>" . $DATA['private'] . "</private>\n";
	$output .= "        <hot>" . $DATA['hot'] . "</hot>\n";
	$output .= "        <level>" . $DATA['level'] . "</level>\n";	
	$output .= "        <status>" . reqStatus($DATA['status']) . "</status>\n";
	$output .= "        <authorization level=\"" . $DATA['level'] . "\">\n";
	$output .= "        	<controller eid=\"" . $DATA['controller'] . "\" yn=\"" . $DATA['controlleryn'] . "\" date=\"" . $DATA['controllerDate'] . "\">" . caps($EMPLOYEES[$DATA['controller']]) . "</controller>\n";
	$output .= "        	<approver1 eid=\"" . $DATA['app1'] . "\" yn=\"" . $DATA['app1yn'] . "\" date=\"" . $DATA['app1Date'] . "\">" . caps($EMPLOYEES[$DATA['app1']]) . "</approver1>\n";
	$output .= "        	<approver2 eid=\"" . $DATA['app2'] . "\" yn=\"" . $DATA['app2yn'] . "\" date=\"" . $DATA['app2Date'] . "\">" . caps($EMPLOYEES[$DATA['app2']]) . "</approver2>\n";
	$output .= "        	<approver3 eid=\"" . $DATA['app3'] . "\" yn=\"" . $DATA['app3yn'] . "\" date=\"" . $DATA['app3Date'] . "\">" . caps($EMPLOYEES[$DATA['app3']]) . "</approver3>\n";
	$output .= "        	<approver4 eid=\"" . $DATA['app4'] . "\" yn=\"" . $DATA['app4yn'] . "\" date=\"" . $DATA['app4Date'] . "\">" . caps($EMPLOYEES[$DATA['app4']]) . "</approver4>\n";				
	$output .= "        </authorization>\n";	
	$output .= "    </requisition>\n";
}

$output .= "</requisitions>\n";
        
print $output;
?>


<?php 
/**
 * - Display debug information 
 */
include_once('debug/footer.php');
/* 
 * - Disconnect from database 
 */
$dbh->disconnect();
$dbh_standards->disconnect();
?>