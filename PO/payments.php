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
require_once('../include/BlackBerry.php');
 
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
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../security/check_user.php');


$today = date('Y-m-d');
$year = date('Y');
$current_month = date('m');
$day = date('d');

/* Current week settings */
$current_day_number = date("w", mktime(0, 0, 0, $current_month, $day, $year));
$last_day_number = 6 - $current_day_number;
$current_week_first = $year . '-' . $current_month . '-' . $day;
$current_week_last = $year . '-' . $current_month . '-' . date("d", mktime(0, 0, 0, $current_month, $day + $last_day_number, $year));
/* Current month settings */
$current_month_first = $year . '-' . $current_month . '-01';
$current_month_last = $year . '-' . $current_month . '-' . date("t", mktime(0, 0, 0, $current_month, 1, $year));
/* Next month settings */
$next_month = date('m', mktime(0, 0, 0, $current_month + 1, '01', $year));
$next_month_first = $year . '-' . $next_month . '-01';
$next_month_last = $year . '-' . $next_month . '-' . date("t", mktime(0, 0, 0, $next_month, 1, $year));
/* Last month settings */
$last_month = date('m', mktime(0, 0, 0, $current_month + 2, '01', $year));
$last_month_first = $year . '-' . $last_month . '-01';
$last_month_last = $year . '-' . $last_month . '-' . date("t", mktime(0, 0, 0, $last_month, 1, $year));


/* SQL statement to get payments */
$sql = "SELECT pay_id, request_id, pay_eid, pay_amount, pay_date, p.po, p.purpose, CONCAT(e.fst, ' ', e.lst) AS fullname
		FROM Payments
		  INNER JOIN PO p ON p.id=request_id
		  INNER JOIN Standards.Employees e ON e.eid=pay_eid
		WHERE pay_status='0' AND (pay_date >= ? and pay_date <= ?)";
$query = $dbh->prepare($sql);

/* This weeks execution */
$week_sth = $dbh->execute($query, array($current_week_first,$current_week_last));
$week_count = $week_sth->numRows();	
/* Current month execution */				
$current_month_sth = $dbh->execute($query, array($current_month_first,$current_month_last));
$current_month_count = $current_month_sth->numRows();
/* Next month execution */
$next_month_sth = $dbh->execute($query, array($next_month_first,$next_month_last));
$next_month_count = $next_month_sth->numRows();	
/* Last month execution */
$last_month_sth = $dbh->execute($query, array($last_month_first,$last_month_last));
$last_month_count = $last_month_sth->numRows();	


/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html><!-- InstanceBegin template="/Templates/vnmain.dwt.php" codeOutsideHTMLIsLocked="false" -->
  <head>
  <!-- InstanceBeginEditable name="doctitle" -->
    <title><?= $default['title1']; ?></title>
  <!-- InstanceEndEditable -->
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="../default.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?> 
	<script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <!-- InstanceBeginEditable name="head" -->
	<script type="text/javascript" src="/Common/js/pointers.js"></script>
	
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">

    <script type="text/javascript" src="../js/dynamicInputItems.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= $default['URL_HOME']; ?>/style/dd_tabs.css" />	
    <style type="text/css">
	.sectionTitle {
		height:30px; 
		font-weight:bold; 
		padding-top:5px;
	}
    </style>
  <!-- InstanceEndEditable -->
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body class="yui-skin-sam">  
    <img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- #BeginLibraryItem "/Library/help.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
<tr>
  <td width="30"><a href="../Common/calculator.php" onClick="window.open(this.href,this.target,'width=281,height=270'); return false;" <?php help('', 'Calculator', 'default'); ?>><img src="../images/xcalc.png" width="16" height="14" border="0"></a></td>
  <td><a href="../Help/index.php" rel="gb_page_fs[]"><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
  <td class="DarkHeaderSubSub">&nbsp;<a href="../Help/index.php" rel="gb_page_fs[]" class="dark">Help</a></td>
</tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" -->
            <?php include('../include/menu/main_right.php'); ?>
          <!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><?php include('../include/menu/main_left.php'); ?><!-- InstanceEndEditable --></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGColorDark" rowspan="2"></td>
                <td class="BGGrayDark" rowspan="2"></td>
                <td class="BGGrayMedium" rowspan="2"></td>
                <td class="BGGrayLight" rowspan="2"></td>
              </tr>
              <tr>
                <td class="BGColorDark" width="100%"><?php 
				  	if (isset($_SESSION['username'])) {
				  ?>
                    <div align="right" class="FieldNumberDisabled">&nbsp;</div>
                  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
                </td>
              </tr>
              <tr>
                <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3"></td>
                <td colspan="7"></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" -->    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td align="center">
				  <br>
				  <br>				  
				  <img src="../images/calendar.gif" width="17" height="18" align="absmiddle"> <span class="DarkHeaderSub">Vendor Payments - Calendar View</span><br>
				  <br>
                  <br>
                  <div id="Acc1" class="Accordion" tabindex="0" style="width:800px;">
                      <div class="AccordionPanel">
                        <div class="BGAccentVeryDark" onClick="Effect.toggle('week', 'blind');">
                          <div class="sectionTitle"> This Week (<?= $week_count; ?>) </div>
                        </div>
                        <div id="week" style="display:<?= ($week_count == 0) ? none : display; ?>">
                          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><table width="100%" border="0">
                                  <tr>
                                    <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                                    <td width="50" align="center" class="BGAccentDark">Num</td>
                                    <td width="50" align="center" class="BGAccentDark">PO</td>
                                    <td width="125" class="BGAccentDark">Purchasing</td>
                                    <td width="425" class="BGAccentDark">&nbsp;Purpose </td>
                                    <td width="75" class="BGAccentDark">&nbsp;Date<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</td>
                                    <td width="75" class="BGAccentDark">&nbsp;Amount&nbsp;</td>
                                  </tr>
                                    <?php
									$counter=0;		// Reset counter
									
									while($week_sth->fetchInto($WEEK)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
									?>
                                  <tr <?php pointer($row_color); ?>>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $WEEK['request_id']; ?>" title="Detialed View" rel="gb_page_fs[]" <?php help('', 'Get a Detailed view', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $WEEK['request_id']; ?></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $WEEK['po']; ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($WEEK['fullname']); ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps(substr(stripslashes($WEEK['purpose']), 0, 40)); ?><?php if (strlen($WEEK['purpose']) >= 40) { echo "..."; } ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $WEEK['pay_date']; ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="10">$</td>
                                        <td align="right"><?= number_format($WEEK['pay_amount'],2); ?></td>
                                      </tr>
                                    </table>                                    </td>
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
                        <div class="BGAccentVeryDark" onClick="Effect.toggle('currentMonth', 'blind');">
                          <div class="sectionTitle"><?= date('F'); ?> (<?= $current_month_count; ?>)</div>
                        </div>
                        <div id="currentMonth" style="display:<?= ($current_month_count == 0) ? none : display; ?>">
                          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><table width="100%" border="0">
                                  <tr>
                                    <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                                    <td width="50" align="center" class="BGAccentDark">Num</td>
                                    <td width="50" align="center" class="BGAccentDark">PO</td>
                                    <td width="125" class="BGAccentDark">Purchasing</td>
                                    <td width="425" class="BGAccentDark">&nbsp;Purpose </td>
                                    <td width="75" class="BGAccentDark">&nbsp;Date<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</td>
                                    <td width="75" class="BGAccentDark">&nbsp;Amount&nbsp;</td>
                                  </tr>
                                  <?php
									$counter=0;		// Reset counter
																	  
									while($current_month_sth->fetchInto($CURRENT)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
									?>
                                  <tr <?php pointer($row_color); ?>>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $CURRENT['request_id']; ?>" title="Detialed View" rel="gb_page_fs[]" <?php help('', 'Get a Detailed view', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $CURRENT['request_id']; ?></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $CURRENT['po']; ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($CURRENT['fullname']); ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps(substr(stripslashes($CURRENT['purpose']), 0, 40)); ?><?php if (strlen($CURRENT['purpose']) >= 40) { echo "..."; } ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $CURRENT['pay_date']; ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="10">$</td>
                                          <td align="right"><?= number_format($CURRENT['pay_amount'],2); ?></td>
                                        </tr>
                                      </table></td>
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
                      <div id="AccordionPanel">
                        <div class="BGAccentVeryDark" onClick="Effect.toggle('nextMonth', 'blind');">
                          <div class="sectionTitle"><?= date('F', mktime(0, 0, 0, $current_month + 1, '01', $year)); ?> (<?= $next_month_count; ?>) </div>
                        </div>
                        <div id="nextMonth" style="display:<?= ($next_month_count == 0) ? none : display; ?>">
                          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><table width="100%" border="0">
                                  <tr>
                                    <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                                    <td width="50" align="center" class="BGAccentDark">Num</td>
                                    <td width="50" align="center" class="BGAccentDark">PO</td>
                                    <td width="125" class="BGAccentDark">Purchasing</td>
                                    <td width="425" class="BGAccentDark">&nbsp;Purpose </td>
                                    <td width="75" class="BGAccentDark">&nbsp;Date<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</td>
                                    <td width="75" class="BGAccentDark">&nbsp;Amount&nbsp;</td>
                                  </tr>
                                  <?php
									$counter=0;		// Reset counter
																	  
									while($next_month_sth->fetchInto($NEXT)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
									?>
                                  <tr <?php pointer($row_color); ?>>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $NEXT['request_id']; ?>" title="Detialed View" rel="gb_page_fs[]" <?php help('', 'Get a Detailed view', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $NEXT['request_id']; ?></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $NEXT['po']; ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($NEXT['fullname']); ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps(substr(stripslashes($NEXT['purpose']), 0, 40)); ?>
                                        <?php if (strlen($NEXT['purpose']) >= 40) { echo "..."; } ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $NEXT['pay_date']; ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="10">$</td>
                                          <td align="right"><?= number_format($NEXT['pay_amount'],2); ?></td>
                                        </tr>
                                      </table></td>
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
                      <div id="AccordionPanel">
                        <div class="BGAccentVeryDark" onClick="Effect.toggle('lastMonth', 'blind');">
                          <div class="sectionTitle"><?= date('F', mktime(0, 0, 0, $current_month + 2, '01', $year)); ?> (<?= $last_month_count; ?>) </div>
                        </div>
                        <div id="lastMonth" style="display:<?= ($last_month_count == 0) ? none : display; ?>">
                          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><table width="100%" border="0">
                                  <tr>
                                    <td width="25" height="25" class="BGAccentDark">&nbsp;</td>
                                    <td width="50" align="center" class="BGAccentDark">Num</td>
                                    <td width="50" align="center" class="BGAccentDark">PO</td>
                                    <td width="125" class="BGAccentDark">Purchasing</td>
                                    <td width="425" class="BGAccentDark">&nbsp;Purpose </td>
                                    <td width="75" class="BGAccentDark">&nbsp;Date<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</td>
                                    <td width="75" class="BGAccentDark">&nbsp;Amount&nbsp;</td>
                                  </tr>
                                  <?php
									$counter=0;		// Reset counter
																	  
									while($last_month_sth->fetchInto($LAST)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;										
									?>
                                  <tr <?php pointer($row_color); ?>>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $LAST['request_id']; ?>" title="Detialed View" rel="gb_page_fs[]" <?php help('', 'Get a Detailed view', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $LAST['request_id']; ?></td>
                                    <td align="center" bgcolor="#<?= $row_color; ?>" class="padding"><?= $LAST['po']; ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps($LAST['fullname']); ?></td>
                                    <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= caps(substr(stripslashes($LAST['purpose']), 0, 40)); ?>
                                        <?php if (strlen($LAST['purpose']) >= 40) { echo "..."; } ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $LAST['pay_date']; ?></td>
                                    <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="10">$</td>
                                          <td align="right"><?= number_format($LAST['pay_amount'],2); ?></td>
                                        </tr>
                                      </table></td>
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
                    <br>
                  <br></td>
                </tr>
              </tbody>
          </table></td>
        </tr>
      </tbody>
      </table>
  <!-- InstanceEndEditable --><br>
    <br>
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100%" height="20" class="BGAccentDark">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('../include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><?php include('../include/version.php'); ?><!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><?php if ($_SESSION['request_role'] == 'purchasing') { ?><a href="<?= $default['URL_HOME']; ?>/Help/chat.php" target="chat" onclick="window.open(this.href,this.target,'width=250,height=400'); return false;" id="meebo"><img src="/Common/images/meebo.gif" width="18" height="20" border="0" align="absmiddle">Company Chat</a><?php } ?><!-- InstanceEndEditable --></div>
			<div class="TrainVisited" id="noPrint"><?= onlineCount(); ?></div>
    	</td>
        </tr>
      </tbody>
  </table>
   <br>
  </body>
  <script>var request_id='<?= $_GET['id']; ?>';</script>
  <script type="text/javascript" src="/Common/js/scriptaculous/prototype-min.js"></script>
  <script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=builder,effects"></script>
  <script type="text/javascript" src="/Common/js/ps/tooltips.js"></script>  
  <!-- InstanceBeginEditable name="js" --><!-- InstanceEndEditable -->   
<!-- InstanceEnd --></html>

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