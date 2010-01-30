<?php
/**
 * Purachase Request System
 *
 * config.php all the commen PHP, Javscript and HTML.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/* Debug settings */
$default['debug'] = "off";
$default['debug_ip'] = "172.16.81.228";																//Only system to view debug information
$default['debug_email'] = "tlezotte@Company.com";
$default['debug_capture'] = 'on';																	// Record all sql transactions
/* Title Display */
$default['title0'] = "Welcome to the";
$default['title1'] = "Purchase Requisition System";
$default['title2'] = "";
/* Set Application Location */
$default['fs_home'] = "/http/a2/Company/com/80";											//Website Home directory
$default['url_home'] = "/go/Request";																//Application Home
$default['FS_HOME'] = $default['fs_home'].$default['url_home'];										//Filesystem Location
$default['URL_HOME'] = "http://".$_SERVER['HTTP_HOST'].$default['url_home'];						//Web Location
$default['files_store'] = "files";																	//Upload directory
$default['xdp_store'] = "xdp";																		//XDP Upload directory
$default['cer_upload'] = "/CER/".$default['files_store'];											//CER Upload directory
$default['po_upload'] = "/PO/".$default['files_store'];												//PO Upload directory
$default['xdp_upload'] = "/PO/".$default['xdp_store'];												//XDP Upload directory
$default['CER_UPLOAD'] = $default['FS_HOME'].$default['cer_upload'];								//CER Upload file system
$default['PO_UPLOAD'] = $default['FS_HOME'].$default['po_upload'];									//PO Upload file system
$default['XDP_UPLOAD'] = $default['FS_HOME'].$default['xdp_upload'];								//XDP Upload file system
$default['upload'] = "/Administration/store";														//Upload file system
$default['UPLOAD'] = $default['FS_HOME'] . "/" . $default['upload'];								//Upload file system
$default['URL_UPLOAD'] = $default['URL_HOME'] . $default['upload'];
$default['pdf_logo'] = "http://www.yourdomain.com/Common/images/Company.jpg";
$default['approverLevels'] = 4;																		// Number of Approver levels
$default['creditLimit'] = "25000";																	// Credit Card Limit
$viewable_rows = "25";																				// Pagination Value

/* Maintenance Mode */
$default['maintenance'] = "off";
$default['bb_maintenance'] = "off";																	//Maintencance status for BlackBerry
$default['maintenance_time'] = "4:30pm";															//Return Time

/* Web Notify Mode */
$default['notify_web'] = "off";
$default['email_domain'] = "Company.com";													//Email Domain
$default['email_from'] = "request@".$default['email_domain'];										//Email from address

/* RSS Feed */
$default['rss'] = "off";
$default['rss_file'] = "rss.xml";																	//RSS file name
$default['rss_items'] = "40";																		//Number of RSS items
$default['rss_image'] = "http://www.yourdomain.com/Common/images/CompanyRSS.gif";				//RSS image

/* SMTP server */
$default['smtp'] = "mail.Company.com";													//SMTP Mail server
$default['smtp_port'] = "25";																		//SMTP port

/* Approver's Signatures */
$default['signatures'] = "/images/Signatures";

/* Request access variables */
$request_email = "tjackson@Company.com";
$request_name = "Tawana Jackson";

/* Turn on or off Page Loading */
//$default['pageloading'] = "off";

/* Google keys */
$default['google_api'] = "ABQIAAAAgpDFt5t8rxR6NYa7Bd2toBSeTMhUYkYTlR5c06N4CzKSSYOEJxRy3AuheY0YgAoviJbOxqXDltjqMQ";		// Key for www.yourdomain.com
$default['google_analytics'] = "UA-2838165-4";														// Google Analytics

/* URL to track shipments */
$default['track_shipment'] = "http://www.packagemapping.com/track/tracking.php?action=track&rss=1&tracknum=";

/* Approval Limits */
$default['app1_min'] = ".01";
$default['app1_max'] = "500.00";
$default['app2_min'] = "1000.01";
$default['app2_max'] = "99999999.99";
$default['app3_min'] = "1000.01";
$default['app3_max'] = "99999999.99";
$default['app4_min'] = "5000.01";
$default['app4_max'] = "99999999.99";

$default['po_templete_hot'] = "http://www.Company.com/go/Request/Common/Company_PO.pdf";
$default['po_templete'] = "http://www.Company.com/go/Request/Common/CompanyPO.pdf";


/* ------------------ START VARIABLES ----------------------- */
$blank = "";	/* Place holder */
$CHANGE = "<a href=\"javascript:void(0);\" ".
		  "onmouseover=\"return overlib('CHANGES TO THIS FIELD WILL BE SAVED', BORDER, 2, FGCOLOR, '#FDF500', BGCOLOR, '#000000', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
          "<img src=\"/Common/images/form-update.gif\" border=\"0\" align=\"absmiddle\"></a>";
$NOCHANGE = "<a href=\"javascript:void(0);\" ".
		    "onmouseover=\"return overlib('CHANGES TO THIS FIELD WILL <b>NOT</b> BE SAVED', BORDER, 2, FGCOLOR, '#FF6600', BGCOLOR, '#FF0000', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
            "<img src=\"/Common/images/red-no.gif\" border=\"0\" align=\"absmiddle\"></a>";		  
$WARNING = "<a href=\"javascript:void(0);\" ".
		   "onmouseover=\"return overlib('REQUIRED FIELD', BORDER, 2, FGCOLOR, '#DAAA17', BGCOLOR, '#AC120F', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
           "<img src=\"/Common/images/required.gif\" border=\"0\" align=\"absmiddle\"></a>";
$ADDITION = "<a href=\"javascript:void(0);\" ".
		   "onmouseover=\"return overlib('ADD PDF FILES TO CABINET', BORDER, 2, FGCOLOR, '#89C08F', BGCOLOR, '#3B8C4C', TEXTPADDING, 5, WRAP, AUTOSTATUS);\" onmouseout=\"nd();\">".
           "<img src=\"/Common/images/folder-plus.gif\" border=\"0\" align=\"absmiddle\"></a>";		   
/* ------------------ END VARIABLES ----------------------- */	


/**
 * - Load Functions
 */
include_once('functions.php');


/**
 * - Load Language
 */
switch ($_COOKIE['language']) {
	case 'fr':
		include_once($default['FS_HOME'] . '/Language/fr/labels.php');	
	break;
	case 'en':
	default:
		include_once($default['FS_HOME'] . '/Language/en/labels.php');	
	break;
}	
?>