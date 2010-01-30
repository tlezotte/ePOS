<?php 
/**
 * License Manager
 *
 * software.php generates graphs for software.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/License/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * ChartDirector
 * @link http://www.advsofteng.com/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
 */
 
/**
 * - Set debug mode
 */
$debug_page = false;

/**
 * - Start Page Loading Timer
 */
include_once('../include/Timer.php');
$starttime = StartLoadTimer();
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
Summary($dbh, 'Software Report', $_SESSION['eid']);

/* --- PEAR QuickForm --- */
require_once ('HTML/QuickForm.php');


/* Set number of software products to display */
if (! array_key_exists('limit', $_GET)) {
	$_GET['limit'] = 10;
}

/* ------------------ START DATABASE ACCESS ----------------------- */ 
/* SQL for Licenses list */
$sql = <<< SQL
	SELECT s.name, l.version, sum( l.qty ) AS total
	FROM Strings l, Standards.Software s
	WHERE l.name = s.id
	  AND l.status = '1'
	GROUP BY s.name, l.version
	ORDER BY ? ?
SQL;

$data_sql = $dbh->prepare($sql);
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
    <!-- InstanceBeginEditable name="main" -->
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><div id="noPrint">
        <table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
          <tbody>
            <tr>
              <td valign="top" width="13" background="../../images/asyltlb.gif"><img height="20" alt="" src="../../images/t.gif" width="13" border="0"></td>
              <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../../images/asybase.gif" width="145" border="0">
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td valign="top" height="10" >&nbsp;</td>
                    </tr>
                    <tr>
                      <td><?php
				$year = date("Y");

				$form1 =& new HTML_QuickForm('Form1', 'get');
				$form1->setDefaults(array(
					'limit' => '10'
				));
								
				$form1->addElement('header', '', 'Chart Options');
				$form1->addElement('text', 'limit', 'Count:', array('size' => '5', 'maxlength' => '5'));
				
				$form1->addElement('image', 'submit', '../../images/button.php?i=b70.png&l=Submit');
				$form1->display();
				?></td>
                    </tr>
                </table></td>
              <td valign="top" width="22" background="../../images/asyltrb.gif"><img height="20" alt="" src="../../images/t.gif" width="22" border="0"></td>
            </tr>
            <tr>
              <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../../images/asyltb.gif" width="200" border="0"></td>
            </tr>
            <tr>
              <td valign="top" colspan="3">&nbsp;</td>
            </tr>
            <tr>
              <td valign="top" colspan="3">&nbsp;</td>
            </tr>
          </tbody>
        </table>
    </div></td>
    <td align="center"><div align="center"><br>
            <br>
            <img src="software_bar.php?limit=<?= $_GET['limit']; ?>"><br>
            <table  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="25">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDark"><div align="left">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Software Report... </td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                    </div></td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><table width="100%"  border="0">
                              <tr>
                                <td height="25" class="BGAccentDark"><strong>&nbsp;Software<img src="../../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Version</strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Amount</strong></td>
                              </tr>
                              <?php
								$data_sth = $dbh->execute($data_sql, array('s.name','ASC'));
								$num_rows = $data_sth->numRows();	
								
								while($data_sth->fetchInto($DATA)) {
									/* Line counter for alternating line colors */
									$counter++;
									$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									
									/* Display Action options only if owned by user */
									if ($user == $DATA['eid']) {
										$DATA['fullname'] = "";
									} else {
										$user = $DATA['eid'];
									}
								?>
                              <tr <?php pointer($row_color); ?>>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['name'])); ?></td>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['version'])); ?></td>
                                <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $DATA['total']; ?></td>
                              </tr>
                              <?php } // End PO while ?>
                          </table></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td>&nbsp;<span class="GlobalButtonTextDisabled">
                      <?= $num_rows ?> Licenses</span></td>
                  </tr>
                </table></td>
                <td width="50">&nbsp;</td>
                <td valign="top"><table border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="25">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDark"><div align="left">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Software Report... </td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                    </div></td>
                  </tr>
                  <tr>
                    <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><table width="100%"  border="0">
                              <tr>
                                <td height="25" class="BGAccentDark"><strong>&nbsp;Software</strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Version</strong>&nbsp;</td>
                                <td class="BGAccentDark"><strong>&nbsp;Amount<strong><img src="../../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></strong></td>
                              </tr>
                              <?php
								$data_sth = $dbh->execute($data_sql, array('total','DESC'));
								$num_rows = $data_sth->numRows();	
								
								while($data_sth->fetchInto($DATA)) {
									/* Line counter for alternating line colors */
									$counter++;
									$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
									
									/* Display Action options only if owned by user */
									if ($user == $DATA['eid']) {
										$DATA['fullname'] = "";
									} else {
										$user = $DATA['eid'];
									}
								?>
                              <tr <?php pointer($row_color); ?>>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['name'])); ?></td>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($DATA['version'])); ?></td>
                                <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $DATA['total']; ?></td>
                              </tr>
                              <?php } // End PO while ?>
                          </table></td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td>&nbsp;<span class="GlobalButtonTextDisabled">
                      <?= $num_rows ?> Licenses</span></td>
                  </tr>
                </table></td>
              </tr>
            </table>
</div>
        </td>
  </tr>
</table>
<br>
	<br>
    <br>
    <br>
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
/* Disconnect from database */
$dbh->disconnect();
?>