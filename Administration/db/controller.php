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

/* SQL statement to get controllers */
$no_sql = "SELECT c.id, p.name AS plant, d.name AS department, CONCAT(e.fst, ' ', e.lst) AS fullname
		   FROM Standards.Controller c
		     INNER JOIN Standards.Plants p ON p.id=c.plant
		     INNER JOIN Standards.Department d ON d.id=c.department
		     INNER JOIN Standards.Employees e ON e.eid=c.controller
		   WHERE department='00'
		   ORDER BY plant";
$no_query = $dbh->prepare($no_sql);

/* This weeks execution */
$no_sth = $dbh->execute($no_query);
$no_count = $no_sth->numRows();	

/* SQL statement to get controllers */
$dept_sql = "SELECT c.id, p.name AS plant, c.department AS department_id, d.name AS department, CONCAT(e.fst, ' ', e.lst) AS fullname
		   FROM Standards.Controller c
		     INNER JOIN Standards.Plants p ON p.id=c.plant
		     INNER JOIN Standards.Department d ON d.id=c.department
		     INNER JOIN Standards.Employees e ON e.eid=c.controller
		   WHERE department<>'00'
		   ORDER BY c.department";
$dept_query = $dbh->prepare($dept_sql);

/* This weeks execution */
$dept_sth = $dbh->execute($dept_query);
$dept_count = $dept_sth->numRows();	

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
  <div id="Acc1" class="Accordion" tabindex="0" style="width:700px;margin:0px auto;">
    <div class="AccordionPanel">
      <div class="BGAccentVeryDark" onClick="Effect.toggle('plantLevel', 'blind');">
        <div class="sectionTitle"> Plant Level Controllers (
            <?= $no_count; ?>
          ) </div>
      </div>
      <div id="plantLevel" style="display:display">
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%" border="0">
                <tr>
                  <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                  <td width="175" align="center" class="BGAccentDark">Plant</td>
                  <td width="250" align="center" nowrap class="BGAccentDark">Department</td>
                  <td class="BGAccentDark">Controller</td>
                </tr>
                <?php
				$counter=0;		// Reset counter
				
				while($no_sth->fetchInto($DATA)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
				?>
                <tr <?php pointer($row_color); ?>>
                  <td class="padding" bgcolor="#<?= $row_color; ?>">&nbsp;</td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DATA['plant']); ?></td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DATA['department']); ?></td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DATA['fullname']); ?></td>
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
    <div class="AccordionPanel">
      <div class="BGAccentVeryDark" onClick="Effect.toggle('deptLevel', 'blind');">
        <div class="sectionTitle"> Department Level Controllers (
            <?= $dept_count; ?>
          ) </div>
      </div>
      <div id="deptLevel" style="display:display">
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%" border="0">
                <tr>
                  <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                  <td width="175" align="center" class="BGAccentDark">Plant</td>
                  <td width="250" align="center" nowrap class="BGAccentDark">Department</td>
                  <td class="BGAccentDark">Controller</td>
                </tr>
                <?php
				$counter=0;		// Reset counter
				
				while($dept_sth->fetchInto($DEPT)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
				?>
                <tr <?php pointer($row_color); ?>>
                  <td class="padding" bgcolor="#<?= $row_color; ?>">&nbsp;</td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DEPT['plant']); ?></td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= '(' . $DEPT['department_id'] . ') ' . caps($DEPT['department']); ?></td>
                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($DEPT['fullname']); ?></td>
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