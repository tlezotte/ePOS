<?php
/**
 * Request System
 *
 * rss.php generates RSS feed.
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


	
$rss_type = 'PO';		//Type of RSS feed
switch ($rss_type) {
case 'CER':
   $DATABASE = 'CER';
   $LABEL = 'Capital Acquisitions';
   break;
case 'PO':
   $DATABASE = 'PO';
   $LABEL = 'Purchase Requests';
   break;
}

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
$rss_items = $default['rss_items'] / 2;

/* Getting Submitted PO information */
$submitted_query = <<< SQL
	SELECT id, purpose, reqDate, req, company
	FROM $DATABASE
	ORDER BY reqDate DESC
	LIMIT $rss_items
SQL;
$submitted_sql = $dbh->prepare($submitted_query);

/* Getting Approved PO information */
$approved_query = <<< SQL
	SELECT p.id, p.purpose, p.req, p.company, a.issuer, a.issuerDate
	FROM $DATABASE p, Authorization a
	WHERE p.id = a.type_id AND a.type = '$DATABASE' AND a.issuerDate IS NOT NULL
	ORDER BY a.issuerDate DESC
	LIMIT $rss_items
SQL;
$approved_sql = $dbh->prepare($approved_query);

/* Getting Denied PO information */
$denied_query = <<< SQL
	SELECT p.id, p.purpose, p.req, p.company, a.app1Date
	FROM $DATABASE p, Authorization a
	WHERE p.id = a.type_id AND a.type = '$DATABASE' AND (a.app1yn = 'no' OR a.app2yn = 'no')
	ORDER BY a.app1Date DESC
	LIMIT $rss_items
SQL;
$denied_sql = $dbh->prepare($denied_query);

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");
/* Get Companies names from Standards database */
$COMPANY = $dbh->getAssoc("SELECT id, name FROM Standards.Companies WHERE id > 0");		
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

/* ------------------ START VARIABLES ----------------------- */
/* Generate at RFC 2822 formatted date */
$pubDate = date("r");
$filename = $default['rss_file'];
/* ------------------ END VARIABLES ----------------------- */



/* ------------------------------------------ CREATE RSS 2.0 FILE ----------------------------------------- */

//header('Content-Type: text/xml');

$rss  = "<?xml version=\"1.0\"?>\n";
$rss .= "<rss version=\"2.0\">\n";
$rss .= "	<channel>\n";
$rss .= "		<title>$LABEL</title>\n"; 
$rss .= "		<link>".$default['URL_HOME']."/index.php</link>\n";
$rss .= "		<description>List of $LABEL transactions using the $default[title1]</description>\n";
$rss .= "		<pubDate>$pubDate</pubDate>\n";
$rss .= "		<copyright>2004 Your Company</copyright>\n";
$rss .= "		<webMaster>webmaster@".$default['email_domain']."</webMaster>\n";
$rss .= "		<category>$default[title1]</category>\n";
$rss .= "		<image>\n";
$rss .= "			<title>Your Company</title>\n";
$rss .= "			<url>$default[rss_image]</url>\n";
$rss .= "			<width>150</width>\n";
$rss .= "			<height>50</height>\n";
$rss .= "			<link>http://intranet.Company.com/</link>\n";
$rss .= "		</image>\n";

$submitted_sth = $dbh->execute($submitted_sql);
while($submitted_sth->fetchInto($SUBMITTED)) {
	$title = $SUBMITTED['purpose'];
	$company = caps$COMPANY[$SUBMITTED['company']]);
	$author = caps($EMPLOYEES[$SUBMITTED['req']]);
	
	$rss .= "		<item>\n";
	$rss .= "			<title>".str_replace("&", "and", $title)."</title>\n";
	$rss .= "			<link>".$default['URL_HOME']."/$DATABASE/detail.php?id=$SUBMITTED[id]</link>\n";
	$rss .= "			<author>$author</author>\n";
	$rss .= "			<description>".str_replace("&", "and", $title)."</description>\n";
	$rss .= "			<category>Submitted</category>\n";
//	$rss .= "			<category>$company</category>\n";
	$rss .= "			<pubDate>$SUBMITTED[reqDate]</pubDate>\n";
	$rss .= "		</item>\n";
}

$approved_sth = $dbh->execute($approved_sql);
while($approved_sth->fetchInto($APPROVED)) {
	$title = $APPROVED['purpose'];
	$company = caps($COMPANY[$APPROVED[company]]);
	$author = caps($EMPLOYEES[$APPROVED[req]]);
	
	$rss .= "		<item>\n";
	$rss .= "			<title>".str_replace("&", "and", $title)."</title>\n";
	$rss .= "			<link>".$default['URL_HOME']."/$DATABASE/detail.php?id=$APPROVED[id]</link>\n";
	$rss .= "			<author>$author</author>\n";
	$rss .= "			<description>".str_replace("&", "and", $title)."</description>\n";
	$rss .= "			<category>Approved</category>\n";
//	$rss .= "			<category>$company</category>\n";
	$rss .= "			<pubDate>$APPROVED[reqDate]</pubDate>\n";
	$rss .= "		</item>\n";
}

$denied_sth = $dbh->execute($denied_sql);
while($denied_sth->fetchInto($DENIED)) {
	$title = $DENIED['purpose'];
	$company = caps($COMPANY[$DENIED[company]]);
	$author = caps($EMPLOYEES[$DENIED[req]]);
	
	$rss .= "		<item>\n";
	$rss .= "			<title>".str_replace("&", "and", $title)."</title>\n";
	$rss .= "			<link>".$default['URL_HOME']."/$DATABASE/detail.php?id=$DENIED[id]</link>\n";
	$rss .= "			<author>$author</author>\n";
	$rss .= "			<description>".str_replace("&", "and", $title)."</description>\n";
	$rss .= "			<category>Denied</category>\n";
//	$rss .= "			<category>$company</category>\n";
	$rss .= "			<pubDate>$DENIED[app1Date]</pubDate>\n";
	$rss .= "		</item>\n";
}

$rss .= "	</channel>\n";
$rss .= "</rss>\n";
/* ------------------------------------------ CREATE RSS 2.0 FILE ----------------------------------------- */

if ($debug) {
	echo "RSS_ITEMS: ".$rss_items."<br>";
	echo "DEFAULT: ".$default['rss_items']."<br>";
	echo "QUERY: <br>".$submitted_query."<br>";
	echo "FILENAME: ".$filename."<br>";
	echo "RSS: <BR>".$rss;
	exit;
}

/* ------------------ START RSS.XML FILE ----------------------- */
// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {
	// Open $filename for writing
   if (!$handle = fopen($filename, 'w')) {
		$_SESSION['error'] = "Cannot open file ($filename)";
		
		header("Location: ../error.php");
		exit;
   }
   // Write $rss to our opened file.
   if (fwrite($handle, $rss) === FALSE) {
		$_SESSION['error'] = "Cannot write to file ($filename)";
		
		header("Location: ../error.php");   
		exit;
   }
   //echo "Success, wrote ($somecontent) to file ($filename)";
   fclose($handle);
} else {
	$_SESSION['error'] = "The file $filename is not writable";
	
	header("Location: ../error.php");   
	exit;
}
/* ------------------ END RSS.XML FILE ----------------------- */

/* Forward user to list.php after RSS file is created */
header("Location: list.php?action=my&access=0");


/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>