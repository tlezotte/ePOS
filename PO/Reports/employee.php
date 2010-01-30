<?php
/**
 * License Manager
 *
 * list.php displays available PO.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/License/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Reports
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('../../PO/Reports/debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Check User Access
 */
require_once('../../security/check_user.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

/* Update Summary */
Summary($dbh, 'Employee Report', $_SESSION['eid']);


/* ------------------ START DATABASE ACCESS ----------------------- */ 
/* SQL for Licenses list */
$sql = <<< SQL
	SELECT a.id, a.eid, a.department, o.name, s.version, s.license, CONCAT( e.lst, ', ', e.fst ) AS fullname, s.submitted, a.string_id
	FROM Assign a, Strings s, Standards.Employees e, Standards.Software o
	WHERE s.id = a.string_id
	  AND a.status = '1'
	  AND s.status = '1'
	  AND e.eid = a.eid
	  AND s.name = o.id
	ORDER BY e.lst, o.name
SQL;

$data_sql = $dbh->prepare($sql);

/* Loop through list of Licenses */
$data_sth = $dbh->execute($data_sql);
$num_rows = $data_sth->numRows();	

/* Get Department names from Standards database */
$DEPARTMENT = $dbh->getAssoc("SELECT *
							  FROM Standards.Department 
							  ORDER BY name"); 	
/* ------------------ END DATABASE ACCESS ----------------------- */

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
$ONLOAD_OPTIONS.="prepareForm();";
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
  <link type="text/css" href="../../default.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?> 
	<script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <!-- InstanceBeginEditable name="head" -->  <!-- InstanceEndEditable -->
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
          <td valign="top"><a href="../../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include('../../include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><?php include('../../include/menu/main_left.php'); ?><!-- InstanceEndEditable --></td>
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
                <td valign="top"><img height="20" alt="" src="../../images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="../../images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="../../images/c-ghcb.gif" width="3"></td>
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
                  <td><br>
				  <table border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                          <td height="25">&nbsp;</td>
                        </tr>
                        <tr>
                          <td class="BGAccentVeryDark"><div align="left">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Employee Software Report... </td>
                                  <td>&nbsp;</td>
                                </tr>
                              </table>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="BGAccentVeryDarkBorder">
						  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td><table width="100%"  border="0">
                                <tr>
                                  <td height="25" class="BGAccentDark">&nbsp;</td>
                                  <td class="BGAccentDark"><strong>&nbsp;Name</strong><img src="../../images/1downarrow.gif" width="16" height="16" align="absmiddle"></td>
                                  <td class="BGAccentDark"><strong>&nbsp;Department</strong></td>
                                  <td class="BGAccentDark"><strong>&nbsp;Software</strong>&nbsp;</td>
                                  <td class="BGAccentDark"><strong>&nbsp;Version</strong></td>
                                </tr>
                                <?php
									while($data_sth->fetchInto($DATA)) {
										if ($user == $DATA['eid']) {
											$DATA['fullname'] = "";
										} else {
											$user = $DATA['eid'];
										    /* Line counter for alternating line colors */
											$counter++;
											$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
										}
								?>
                                <tr <?php pointer($row_color); ?>>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?php if ($DATA['submitted'] == $_SESSION['eid']) { ?><a href="../../PO/assign.php?id=<?= $DATA[id]; ?>" <?php help('', 'Get a Detailed view'); ?>><img src="../../images/detail.gif" width="18" height="20" border="0" align="absmiddle" id="noPrint"></a>&nbsp;<a href="mailto:?subject=License String&body=<?= ucwords(strtolower($DATA['name'])); ?> <?= ucwords(strtolower($DATA['version'])); ?><br><br><?= $DATA['license']; ?>" <?php help('', 'Email a copy of this license string'); ?>><img src="../../images/email.gif" width="17" height="16" border="0" align="absmiddle" id="noPrint"></a>&nbsp;<a href="../../PO/assign.php?id=<?= $DATA['id']; ?>&license=<?= $DATA['string_id']; ?>&action=unassign" <?php help('', 'Unassign user from License'); ?>><img src="../../images/delete.gif" width="17" height="17" border="0" align="absmiddle" id="noPrint"></a><?php } ?></td>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['fullname'])); ?></td>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DEPARTMENT[$DATA['department']])); ?></td>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['name'])); ?></td>
                                  <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($DATA['version'])); ?></td>
                                </tr>
                                <?php } // End PO while ?>
								
                              </table></td>
                            </tr>
                          </table>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;<span class="GlobalButtonTextDisabled"><?= $num_rows ?> Licenses</span></td>
                        </tr>
                    </table>
					  <br>
                  </td>
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
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('../../include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><?php include('../../include/version.php'); ?><!-- InstanceEndEditable --></div></td>
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
include_once('../../PO/Reports/debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>