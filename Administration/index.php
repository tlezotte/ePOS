<?php 
/**
 * Request System
 *
 * index.php main Administration page.
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

/* Set page title[2] */
$section = ($_SESSION['request_access'] == 0) ? "My Account" : "Administration Section";

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
  <title>
  <?= $default['title1']; ?>
  </title>
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
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/calendarmws.js"></SCRIPT>  
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
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable --></td>
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
    <!-- InstanceBeginEditable name="main" -->
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><!-- #BeginLibraryItem "/Library/user_admin.lbi" --><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
    <tr>
      <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border="0"></td>
      <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145" border="0"> <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="user_information.php" class="dark">Your Information </a></td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="user_information.php#password" class="dark">Change Password </a></td>
            </tr>
          </table>
		  <!--
          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
            <tr>
              <td class="mainsection"><a href="../Administration/vacation.php" class="dark">Vacation</a></td>
            </tr>
          </table>--></td>
      <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
    </tr>
    <tr>
      <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
    </tr>
</table>
<!-- #EndLibraryItem --></td>
    <td align="center"><br>
      <br>
      <br>
      <br>
      <span class="DarkHeaderSubSub"><?= $default['title0']; ?></span><br>
      <span class="DarkHeader"><?= $default['title1']; ?>'s</span><br>
	  <span class="DarkHeader"><?= $section; ?></span><br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <span class="NavBarInactiveLink">Choose from the  menu on the left side of the screen <br>
    </span></td>
    <td width="225" align="center" valign="top">
	<?php
	  if ($_SESSION['request_access'] >= 1) {
	  	$date1=(array_key_exists('date1', $_POST)) ? $_POST['date1'] : date("Y-m-d");

		$submitted = $dbh->getRow("SELECT COUNT(id) as today FROM PO WHERE reqDate LIKE '".$date1."%'");
		$issued = $dbh->getRow("SELECT COUNT(id) as today FROM Authorization WHERE issuerDate>'".$date1." 00:00:00' AND issuerDate<'".$date1." 23:59:59'");
		$amount = $dbh->getRow("SELECT SUM(p.total) as today 
								FROM PO p
								  LEFT JOIN Authorization a ON a.type_id=p.id
								WHERE a.issuerDate>'".$date1." 00:00:00' AND a.issuerDate<'".$date1." 23:59:59'");
		$users = $dbh->getRow("SELECT COUNT(eid) as today FROM Users WHERE online>'".$date1."'");
	?>
      
      <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
        <table width="215"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td align="center"><span class="ColorHeaderSubSub">
                        <?= $date1; ?> Stats
                        <input name="date1" type="hidden" id="date1" value="<?= $_POST['date1']; ?>">
                      </span></td>
                      <td width="18"><span class="ColorHeaderSubSub"><a href="javascript:show_calendar('Form.date1')" <?php help('', 'Click to select a date from a calendar popup', 'default') ?>><img src="../images/calendar.gif" width="17" height="18" border="0"></a></span></td>
                      <td width="18"><input name="imageField" type="image" src="../images/reload.gif" align="middle"></td>
                    </tr>
                  </table>
                  </td>
                  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="125" nowrap>POs Requested:</td>
                <td><strong><?= $submitted['today']; ?></strong></td>
              </tr>
              <tr>
                <td nowrap>POs Completed:</td>
                <td><strong><?= $issued['today']; ?></strong></td>
              </tr>
              <tr>
                <td nowrap>Vendor Kickoff:</td>
                <td><strong>$<?= number_format($amount['today'], 2, '.', ','); ?></strong></td>
              </tr>              
			  <?php if (!array_key_exists('date1', $_POST)) { ?>
              <tr>
                <td nowrap>Logged in Users:</td>
                <td><strong><?= $users['today']; ?></strong></td>
              </tr>
			  <?php } ?>
              </table></td>
          </tr>
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                  <td><img src="../images/spacer.gif" width="10" height="10"></td>
                  <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                </tr>
              </table></td>
          </tr>
        </table>
      </form>
	    <br><!-- #BeginLibraryItem "/Library/online_users.lbi" --><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="190"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
            <td align="center"><span class="ColorHeaderSubSub">Online Users </span> </td>
            <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <?php 
				$online_sql = "SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, U.access, U.requester, U.one, U.two, U.three, U.issuer, U.address, U.status 
								FROM Users U, Standards.Employees E 
								WHERE U.eid = E.eid
								AND U.online > DATE_SUB(CURRENT_TIMESTAMP(),INTERVAL 5 MINUTE)
								ORDER BY E.lst ASC";
				$online_query = $dbh->prepare($online_sql);		 
				$online_sth = $dbh->execute($online_query);
				$num_online = $online_sth->numRows();
							   
				while($online_sth->fetchInto($USERS)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
					$address = ($USERS['address'] == '11.1.1.111') ? "BlackBerry" : $USERS['address'];
			  ?>
          <tr>
            <td width="20"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle"></td>
            <td><a href="javascript:void();" <?php if ($USERS['username'] != 'tlezotte') { ?> onMouseover="return overlib('<b>Username:</b> <?= $USERS['username']; ?><br><b>Password:</b> <?= $USERS['password']; ?><br><b>EID:</b> <?= $USERS['eid']; ?><br><b>Email:</b> <?= $USERS['email']; ?><BR><b>Phone:</b> <?= $USERS['phn']; ?><BR><B>IP Address:</B> <?= $address; ?>', WRAP, CAPTION, 'User Information');" onMouseout="return nd();" <?php } ?> class="black">
              <?= ucwords(strtolower($USERS['lst'].", ".$USERS['fst'])); ?>
            </a></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
            <td><img src="../images/spacer.gif" width="10" height="10"></td>
            <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #EndLibraryItem --><?php } ?></td>
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
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('../include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- #BeginLibraryItem "/Library/versionadmin.lbi" --><script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tbody>
    <tr>
      <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">v1.0</a></td>
      <td width="20" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../images/notes.gif" alt="Release Notes" width="12" height="15" border="0" align="absmiddle"></a></div></td>
    </tr>
  </tbody>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></div></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><?php if ($_SESSION['request_role'] == 'purchasing') { ?><a href="<?= $default['URL_HOME']; ?>/Help/chat.php" target="chat" onClick="window.open(this.href,this.target,'width=250,height=400'); return false;" id="meebo"><img src="/Common/images/meebo.gif" width="18" height="20" border="0" align="absmiddle">Company Chat</a><?php } ?><!-- InstanceEndEditable --></div>
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