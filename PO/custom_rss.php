<?php
/**
 * Company.com
 *
 * rss.php generates RSS feed.
 *
 * @version 1.5
 * @link http://www.Company.com
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package PO
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */


/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
require_once('../Connections/connDB.php');

//http://www.yourdomain.com/go/Request/PO/custom_rss.php?e=MDEzNTI%3D
/* --- Custom RSS feed by employee --- */
if (array_key_exists('a', $_GET) AND array_key_exists('e', $_GET)) {
	$search_query = "p." . $_GET['a'] . "='" . base64_decode(urldecode($_GET['e'])) . "'";
} else if (array_key_exists('e', $_GET)) {
	$search_query = "p.req='" . base64_decode(urldecode($_GET['e'])) . "'";
}
/* --- Custom RSS feed by plant --- */
if (array_key_exists('p', $_GET)) {
	$search_query = "p.plant='" . $_GET['p'] . "'";
}
/* --- Custom RSS feed by department --- */
if (array_key_exists('d', $_GET)) {
	$search_query = "p.department='" . $_GET['d'] . "'";
}


/* ----- Getting career postings from Intranet ----- */
$sql = <<< SQL
	SELECT p.id AS _id, p.purpose, p.hot, e.fst, e.lst, e.email, l.name AS _plant, d.name AS _dept, v.BTNAME AS _vendor, DATE_FORMAT(FROM_UNIXTIME( p.reqDate),'%a, %d %b %Y %T') AS postdate
	FROM PO p
	  LEFT JOIN Standards.Employees e ON e.eid=p.req
	  LEFT JOIN Standards.Plants l ON l.id=p.plant
	  LEFT JOIN Standards.Department d ON d.id=p.department
	  LEFT JOIN Standards.Vendor v ON v.BTVEND=p.sup
	  LEFT JOIN Authorization a ON a.type_id=p.id
	WHERE $search_query AND p.status='N'
	ORDER BY p.id DESC
SQL;
$query = $dbh->prepare($sql);
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

/* ------------------ START VARIABLES ----------------------- */
/* Generate at RFC 2822 formatted date */
$pubDate = date("r");
/* ------------------ END VARIABLES ----------------------- */


/* ------------------------------------------ CREATE RSS 2.0 FILE ----------------------------------------- */

header('Content-Type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');

$rss  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$rss .= "<rss version=\"2.0\">\n";
$rss .= "	<channel>\n";
$rss .= "		<title>Purchase Requisition System - Custom</title>\n"; 
$rss .= "		<link>http://www.yourdomain.com/go/Request/index.php</link>\n";
$rss .= "		<description>Custom Purchase Requisition System RSS Feed</description>\n";
$rss .= "		<pubDate>$pubDate</pubDate>\n";
$rss .= "		<copyright>2007 Your Company, LLC.</copyright>\n";
$rss .= "		<webMaster>tlezotte@Company.com</webMaster>\n";
$rss .= "		<image>\n";
$rss .= "			<title>Your Company, LLC.</title>\n";
$rss .= "			<url>http://www.Company.com/images/CompanyRSS.gif</url>\n";
$rss .= "			<width>144</width>\n";
$rss .= "			<height>48</height>\n";
$rss .= "			<link>http://www.Company.com</link>\n";
$rss .= "		</image>\n";

$sth = $dbh->execute($query);
while($sth->fetchInto($POST)) {
	$hot = ($POST['hot'] == 'yes') ? Hot : Normal;

	$rss .= "		<item>\n";
	$rss .= "			<title><![CDATA[(" . $POST['_id'] . ") " . html_entity_decode($POST['purpose']) . "]]></title>\n";
	$rss .= "			<link><![CDATA[http://www.yourdomain.com/go/Request/PO/detail.php?id=" . $POST['_id'] . "]]></link>\n";
	$rss .= "			<author>" . $POST['email'] . " (" . ucwords(strtolower($POST['fst'])) . " " . ucwords(strtolower($POST['lst'])) .")</author>\n";
	$rss .= "			<description><![CDATA[" . html_entity_decode($POST['purpose']) . "]]></description>\n";
	$rss .= "			<category>" . ucwords(strtolower($POST['_plant'])) . "</category>\n";	
	$rss .= "			<category>" . ucwords(strtolower($POST['_dept'])) . "</category>\n";
	$rss .= "			<category><![CDATA[" . html_entity_decode(ucwords(strtolower($POST['_vendor']))) . "]]></category>\n";
	$rss .= "			<category>" . $hot . "</category>\n";
	$rss .= "			<pubDate>" . $POST['postdate'] . " -0400</pubDate>\n";
	$rss .= "		</item>\n";
}

$rss .= "	</channel>\n";
$rss .= "</rss>\n";
/* ------------------------------------------ CREATE RSS 2.0 FILE ----------------------------------------- */

print $rss;


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh_int->disconnect();
?>
