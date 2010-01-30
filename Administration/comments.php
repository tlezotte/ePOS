<?php
/**
 * Request System
 *
 * summary.php lists the usage of some sections.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
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
/**
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 

/* Detailed Module Information */
$comment_sql = "SELECT *
				FROM Comments
				ORDER BY submitted DESC";	   
$comment_query = $dbh->prepare($comment_sql); 
$comment_sth = $dbh->execute($comment_query);
$num_rows = $comment_sth->numRows();

/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name
							 FROM Users u, Standards.Employees e
							 WHERE e.eid = u.eid");
/* Get Employee email address from Standards database */
$EMAIL = $dbh->getAssoc("SELECT e.eid, email 
						 FROM Users u, Standards.Employees e 
						 WHERE e.eid = u.eid");							
/* ------------- END DATABASE CONNECTIONS --------------------- */

$TYPE = array("0" => "Select One",
			  "1" => "Comment",
			  "2" => "New Feature",
			  "3" => "Improvement");

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
  <link type="text/css" href="../default.css" charset="UTF-8" rel="stylesheet">
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
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include('../include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['request_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="/Common/images/spacer.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<?php } else { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tr>
	<td>&nbsp;</td>
	<td><table cellspacing="0" cellpadding="0" summary="" border="0">
		<tr>
		  <td nowrap><a href="users.php" class="off"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="settings.php" class="off"> Settings </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>		  
		  <td nowrap><a href="db/index.php" class="off"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="utilities.php" class="off"> Utilities </a></td>			  			  
		  <td nowrap>&nbsp;</td>
		</tr>
	</table></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
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
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td width="200" valign="top"><!-- #BeginLibraryItem "/Library/utilities.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="notify.php" class="dark">Notify Users by Email</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="notify_web.php" class="dark">Notify Users by Webs</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="testemail.php" class="dark">Send Test Email </a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="summary.php" class="dark">Usage Summary</a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="comments.php" class="dark">Comments</a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="reminder_past.php" class="dark">Send Past Reminders </a></td>
                      </tr>
                    </table>
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                      <tr>
                        <td class="mainsection"><a href="updateRSS.php" class="dark">Update  RSS </a></td>
                      </tr>
                    </table>
					<!--
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="javascript:void(0);" class="dark">ePOS</a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection">&nbsp;&nbsp;&nbsp;<a href="../Administration/migrate.php" class="dark">Migrate Data </a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection">&nbsp;&nbsp;&nbsp;<a href="../Administration/epos_status.php" class="dark">Status</a></td>
            </tr>
          </table>--></td>
      <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --></td>
                  <td><br>
				  	<br>
					  <?php
						/* Dont display column headers and totals if no requests */
						if ($num_rows == 0) {
					  ?>
							<div align="center" class="DarkHeaderSubSub">No Comments Found</div>
					  <?php } else { ?>
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
                      <table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                          <td class="BGAccentVeryDark"><div align="left">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Comments... </td>
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
                                      <td class="BGAccentDark"><strong>&nbsp;<strong>Date<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></strong></td>
                                      <td class="BGAccentDark"><strong>&nbsp;Employee</strong></td>
                                      <td class="BGAccentDark"><strong>&nbsp;Type</strong></td>
                                      <td class="BGAccentDark"><strong>&nbsp;Comment</strong></td>
                                    </tr>
                                    <?php
									/* Reset items total variable */
									$itemsTotal = 0;
									
									while($comment_sth->fetchInto($COMMENT)) {
										/* Line counter for alternating line colors */
										$counter++;
										$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
										
										$BODY = $COMMENT['comment']."<br><br>".$default['URL_HOME'];	//Body text for email
									?>
                                    <tr <?php pointer($row_color); ?>>
                                      <td valign="top" nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?php $access = explode(" ", $COMMENT[submitted]); echo $access[0]; ?></td>
                                      <td valign="top" nowrap bgcolor="#<?= $row_color; ?>" class="padding"><a href="mailto:<?= strtolower($EMAIL[$COMMENT[eid]]); ?>?subject=Request System Comment&body=<?= $BODY; ?>" class="dark" <?php help('', 'Send Reply Email', 'default'); ?>><?= ucwords(strtolower($EMPLOYEES[$COMMENT[eid]])); ?></a></td>
                                      <td valign="top" nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $TYPE[$COMMENT['type']]; ?></td>
                                      <td valign="top" bgcolor="#<?= $row_color; ?>" class="padding"><?= $COMMENT['comment']; ?></td>
                                    </tr>
                                    <?php } // End SUMMARY while ?>
                                </table></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td valign="top">&nbsp;<span class="GlobalButtonTextDisabled">
                                  <?= $num_rows ?>
            Comments</span> </td>
                                <td valign="bottom"><div align="right"> </div></td>
                              </tr>
                          </table></td>
                        </tr>
                      </table>
                      <?php } // End num_row if ?>
                        <br>
                  </form>
                  </td></tr>
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
