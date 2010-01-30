<?php
/**
 * Request System
 *
 * detail.php displays detailed information on PO.
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
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../../include/BlackBerry.php');
 
/**
 * - Start Page Loading Timer
 */
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');


/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../../security/check_user.php');


//$GROUPS = $dbh->getAll("SELECT role FROM Users GROUP BY role");

/* SQL statement to get controllers */
$sql = "SELECT u.eid, CONCAT(e.fst,' ',e.lst) AS fullname, u.role
		   FROM Users u
		     INNER JOIN Standards.Employees e ON e.eid=u.eid
		   WHERE role<>''
		   ORDER BY e.lst, u.role";
$query = $dbh->prepare($sql);

/* This weeks execution */
$sth = $dbh->execute($query);
$count = $sth->numRows();	

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $default['title1']; ?>
    </title>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/noPrint.css" rel="stylesheet">
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
  <link type="text/css" href="../../epos.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['pageloading'] == 'on') { ?>
  <script type="text/javascript" src="/Common/js/pageloading.js"></script>
  <?php } ?>
  <script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <script type="text/javascript" src="/Common/js/overlibmws/overlibmws_iframe.js"></script>
  <script type="text/javascript" SRC="/Common/js/googleAutoFillKill.js"></script>
  <script type="text/javascript" src="/Common/js/disableEnterKey.js"></script>
  
	<script type="text/javascript" src="/Common/js/pointers.js"></script>
	
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	<script type="text/javascript" src="../../js/dynamicInputItems.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= $default['URL_HOME']; ?>/style/dd_tabs.css" />	
    <style type="text/css">
	.sectionTitle {
		height:30px; 
		font-weight:bold; 
		padding-top:5px;
	}
    </style>
  
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body>
  <div id="Acc1" class="Accordion" tabindex="0" style="width:600px;margin:0px auto;">
    <div class="AccordionPanel">
      <div class="BGAccentVeryDark" onClick="Effect.toggle('role', 'blind');">
        <div class="sectionTitle"></div>
      </div>
      <div id="role" style="display:display">
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%" border="0">
                <tr>
                  <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                  <td width="175" align="center" class="BGAccentDark">Username</td>
                  <td width="250" align="center" nowrap class="BGAccentDark">Role</td>
                </tr>
                <?php
				$counter=0;		// Reset counter
				
				while($sth->fetchInto($DATA)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
				?>
                <tr <?php pointer($row_color); ?>>
                  <td class="padding" bgcolor="#<?= $row_color; ?>">&nbsp;</td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DATA['fullname']); ?></td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DATA['role']); ?></td>
                </tr>
                <?php } // End PO while ?>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentDark">&nbsp;</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  </body>
</html>

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