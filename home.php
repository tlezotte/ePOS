<?php 
/**
 * Request System
 *
 * home.php is the default page after a seccessful login.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
 * Pear XML/RSS
 * @link http://pear.php.net/package/XML_RSS
 */


/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('include/BlackBerry.php');

/**
 * - Start Page Loading Timer
 */
include_once('include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('Connections/connDB.php'); 
/**
 * - Check User Access
 */
require_once('security/check_user.php');
/**
 * - Config Information
 */
require_once('include/config.php'); 



/**
 * - Check to see if a web notice needs to be displayed 
 */
if ($default['notify_web'] == 'on' and !isset($_COOKIE['notify_web'])) {
	header("Location: notice.php");
	exit;
}

if ($_POST['action'] == 'search') {
	/* ----- ID SEARCH -----*/
	if (array_key_exists('po_id_x', $_POST)) {
		$DATA = $dbh->getRow("SELECT id FROM PO WHERE id = '".$_POST['number']."'");
		
		if (isset($DATA)) {
			$GoTo = "PO/detail.php?id=".$DATA['id'];
		} else {
			$_SESSION['error'] = "Purchase Order ".$_POST['number']." was not found";
			$GoTo = "error.php";
		}
	}
	/* ----- PO SEARCH -----*/
	if (array_key_exists('po_x', $_POST)) {
		$DATA = $dbh->getRow("SELECT id FROM PO WHERE po = '".$_POST['number']."'");
		
		if (isset($DATA)) {
			$GoTo = "PO/detail.php?id=".$DATA['id'];
		} else {
			$_SESSION['error'] = "Purchase Order ".$_POST['number']." was not found";
			$GoTo = "error.php";
		}
	}
	/* ----- ID SEARCH -----*/
	if (array_key_exists('cer_id_x', $_POST)) {
		$DATA = $dbh->getRow("SELECT id FROM CER WHERE cer = '".$_POST['number']."'");
		
		if (isset($DATA)) {
			$GoTo = "CER/detail.php?id=".$DATA['id'];
		} else {
			$_SESSION['error'] = "Purchase Order ".$_POST['number']." was not found";
			$GoTo = "error.php";
		}
	}	
	/* ----- CER SEARCH -----*/
	if (array_key_exists('cer_x', $_POST)) {
		$DATA = $dbh->getRow("SELECT id FROM CER WHERE cer = '".$_POST['number']."'");
		
		if (isset($DATA)) {
			$GoTo = "CER/detail.php?id=".$DATA['id'];
		} else {
			$_SESSION['error'] = "Capital Acquisition ".$_POST['number']." was not found";
			$GoTo = "error.php";
		}
	}
	header("Location: ".$GoTo);
	exit();
}


/* ------------- START DATABASE CONNECTIONS --------------------- */
$requests_sql = "SELECT id, hot, purpose
				 FROM PO 
				 WHERE req like '" . $_SESSION['eid'] . "' AND status = 'N'
				 ORDER BY id DESC";						
$requests_query = $dbh->prepare($requests_sql);
$requests_sth = $dbh->execute($requests_query);
$num_rows = $requests_sth->numRows();
/* ------------- END DATABASE CONNECTIONS --------------------- */
			

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}

if ($_GET['v'] == 'on') {
	$ONLOAD_OPTIONS.="function() { GB_showCenter('Delegation of Authority / Vacation', '/go/Request/Administration/vacation.php', 200, 400); }";
}
//$ONLOAD_OPTIONS.="function() { new Effect.Pulsate('releaseNotes'); }";
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
  <link type="text/css" href="default.css" charset="UTF-8" rel="stylesheet">
  <?php if ($default['rss'] == 'on') { ?>
  <link rel="alternate" type="application/rss+xml" title="Purchase Requisition Announcements" href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>">
  <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>">
  <?php } ?> 
	<script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <!-- InstanceBeginEditable name="head" -->
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	  
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all"> 

	<?php if ($_SESSION['request_access'] == '3') { ?>
	<script type="text/javascript" src="/Common/js/slideHelp.js"></script>
	<?php } ?>	
		
	<script>
	function displayChangeLog(mode) {
				if (mode == 'off') {
					$('ChangeLog').hide();
					$('HomePage').show();
				} else {
					$('HomePage').hide();
					Effect.SlideDown('ChangeLog', {duration:3});
				}		
			}
	function MM_openBrWindow(theURL,winName,features) { //v2.0
	  window.open(theURL,winName,features);
	}
	</script>
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
          <td valign="top"><a href="home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- #BeginLibraryItem "/Library/help.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
<tr>
  <td width="30"><a href="Common/calculator.php" onClick="window.open(this.href,this.target,'width=281,height=270'); return false;" <?php help('', 'Calculator', 'default'); ?>><img src="images/xcalc.png" width="16" height="14" border="0"></a></td>
  <td><a href="Help/index.php" rel="gb_page_fs[]"><img src="images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
  <td class="DarkHeaderSubSub">&nbsp;<a href="Help/index.php" rel="gb_page_fs[]" class="dark">Help</a></td>
</tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" -->
            <?php include('include/menu/main_right.php'); ?>
          <!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="images/spacer.gif" width="300" height="5" border="0"></td>
    </tr>
</table><!-- InstanceEndEditable --></td>
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
                <td valign="top"><img height="20" alt="" src="images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="images/c-ghcb.gif" width="3"></td>
                <td colspan="7"></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" -->
    <div align="center">	
      <table width="100%"  border="0" cellspacing="0" cellpadding="0" id="HomePage">
        <tr>
          <td width="300" valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
            
            <tr>
              <td class="BGAccentVeryDarkBorder">
			  <?php 
			    if ($num_rows == 0) {
				  echo "Currently you don&rsquo;t have any open requisitions";
				} else {
			      while($requests_sth->fetchInto($REQ)) { 
			  ?>
                <div id="<?= $REQ['id']; ?>"><a href="PO/detail.php?id=<?= $REQ['id']; ?>" class="black"><strong><?= $REQ['id']; ?></strong></a> <a href="PO/detail.php?id=<?= $REQ['id']; ?>" class="dark"><?= caps(substr(stripslashes($REQ['purpose']), 0, 25)); ?></a></div>
			  <?php 
			     }
				} 
			  ?></td>
            </tr>
            <tr>
             <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="10" height="10" valign="bottom"><img src="images/menu_bottom_left.gif" width="10" height="10"></td>
				  <td align="center" class="ColorHeaderSubSub">My Requested Requisitions </td>
				  <td width="10" height="10" valign="bottom"><img src="images/menu_bottom_right.gif" width="10" height="10"></td>
				</tr>
			  </table></td>
            </tr>
          </table>
			<?php 
			  if ($_SESSION['request_access'] == '3') {
				echo "&nbsp;<img src=\"/Common/images/adminAction.gif\" onClick=\"new Effect.toggle('adminPanel', 'slide')\">";
				
				include('Administration/include/detail.php');
			  } 
			?>			  		  
		  </td>
          <td valign="top"><div align="center"><br>
            <br>
            <br>
            <br>
                <span class="DarkHeaderSubSub">
                <?= $default['title0']; ?>
                </span><br>
                <span class="DarkHeader">
                <?= $default['title1']; ?>
                </span><br>
                <span class="DarkHeaderSubSub">
                <?= $default['title2']; ?>
                </span><br>
            <br>
            <br>
            <br>
            <br>
                <a href="images/HowDoesIt.jpg" title="Show me the Supply Chain workflow?" class="play" rel="gb_image[]"><!--Show me the Supply Chain workflow?--></a></div></td>
          <td width="300" valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" id="releaseNotes">
            <tr>
              <td height="25" valign="middle" class="BGAccentVeryDark">&nbsp;&nbsp;<strong>Changes Log  </strong> </td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder">
				<?php
//				require_once "XML/RSS.php";
//				
//				$rss =& new XML_RSS("http://intranet.Company.com/?q=taxonomy/term/27/0/feed");
//				$rss->parse();
//				
//				echo "<table border=\"0\">\n";
//				
//				foreach ($rss->getItems() as $item) {
//					list($dow, $day, $month, $year) = split(" ", $item['pubdate']);
//					echo "<tr><td valign=\"top\" nowrap><strong>$month-$day</strong></td><td><a href=\"" . $item['link'] . "\" title=\"" . $default['title1'] . "\" onClick=\"displayChangeLog('on');\" class=\"dark\" target=\"ChangeLog\">" . $item['title'] . "</a> <a href=\"" . $item['link'] . "\" title=\"Open in new window or tab\" target=\"log\"><img src=\"/Common/images/offsite.gif\" border=\"0\"></a></td></tr>\n";
//				}
//				
//				echo "</table>\n";
				?>			  </td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top"><img src="images/spacer.gif" width="10" height="50"></td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td align="center" valign="top" class="BGAccentVeryDarkBorder"><form name="form2" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
            <table  border="0" cellspacing="2" cellpadding="0">
              
              <tr>
                <td><strong>Quick Search:
                    <input name="action" type="hidden" id="action" value="search">
                </strong></td>
                <td><input name="number" type="text" id="number" size="10" maxlength="10">
                  &nbsp;</td>
                <td align="center"><input name="po_id" type="image" id="po_id" src="images/button.php?i=b70.png&l=Num" border="0">
				<input name="po" type="image" id="po" src="images/button.php?i=b70.png&l=PO" border="0"></td>
              </tr>
            </table>
</form>          </td>
          <td align="center" valign="bottom"><a href="BlackBerry/bookmark.php" title="View and approve Requisition with a Blackberry" <?php help('', 'View and approve Requisition with a Blackberry','default'); ?> rel="gb_page_center[725,275]"><img src="/Common/images/bb_logo.gif" width="135" height="40" border="0"></a></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
          <td align="center" valign="top"><a href="BlackBerry/bookmark.php" class="TrainUnvisited">Select to get more information </a> </td>
        </tr>
      </table>
	  <div id="ChangeLog" style="display:none">
	  <a href="javascript:displayChangeLog('off')" class="CalendarTitle"><img src="images/next_button.gif" width="19" height="19" border="0" align="absmiddle"> Return to <?= $default['title1']; ?> Home Page</a><br>
	  <br>
	<iframe frameborder="0" height="800" width="98%" name="ChangeLog"></iframe>
	</div>
    </div>
	<?php include('Administration/include/detail.php'); ?>
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
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><?php include('include/version.php'); ?><!-- InstanceEndEditable --></div></td>
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
