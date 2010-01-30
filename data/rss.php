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



if (array_key_exists('p', $_GET) OR array_key_exists('d', $_GET) OR array_key_exists('e', $_GET)) {
	/* ========== Custom RSS feed ========== */
	
	// Example: http://www.yourdomain.com/go/Request/PO/rss.php?e=MDEzNTI%3D
	
	/* --- Custom RSS feed by employee --- */
	if (array_key_exists('a', $_GET) AND array_key_exists('e', $_GET)) {
		$search_query = "a." . $_GET['a'] . "='" . base64_decode(urldecode($_GET['e'])) . "'";
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
echo $sql;
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

} else {
	/* ========== Standard RSS feed ========== */
	
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
		$company = caps($COMPANY[$SUBMITTED['company']]);
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