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
  <link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all"> 
  
  <link type="text/css" href="/Common/js/yahoo/fonts/fonts-min.css" rel="stylesheet">
  <link type="text/css" href="/Common/js/yahoo/datatable/assets/skins/sam/datatable.css" rel="stylesheet">      
  <script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
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
          <!-- InstanceBeginEditable name="topRightMenu" -->
              <div align="right" style="font-weight:bold;font-size:115%">
                <?= $language['label']['title1']; ?>
                &nbsp;</div>
              <div align="right" class="FieldNumberDisabled"><strong><a href="../Administration/user_information.php" class="FieldNumberDisabled" title="Edit your user information">
                <?= ucwords(strtolower($_SESSION['fullname'])); ?>
              </a></strong>&nbsp;<a href="../logout.php" class="FieldNumberDisabled" title="Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;
              </div>
			<!-- InstanceEndEditable --></td>
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
      <table width="99%"  border="0" align="center" cellpadding="0" cellspacing="0" id="HomePage">
        <tr>
          <td width="300" align="left" valign="top"><div id="searchPanel" style="border: thin solid #7F7F7F; background-color:#EDF5FF; width:300px; padding:5px">
            <form name="form2" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
              <table  border="0" cellspacing="2" cellpadding="0">
                <tr>
                  <td><strong>Search:
                      <input name="action" type="hidden" id="action" value="search">
                  </strong></td>
                  <td><input name="number" type="text" id="number" size="10" maxlength="10" title="Enter a Request or PO number, then select the appropriate button.">
                    &nbsp;</td>
                  <td align="center"><input name="po_id" type="image" id="po_id" title="Search by Request Number" src="images/button.php?i=w70.png&l=Num" border="0">
                      <input name="po" type="image" id="po" title="Search by PO Number" src="images/button.php?i=w70.png&l=PO" border="0"></td>
                </tr>
              </table>
            </form>
          </div>
          <br/>
          <div id="myRequestsTable"></div>
          <br/>
          <div id="currencyTable"></div>
          <br/>
          <div id="marketTable"></div>
          </td>
          <td align="center" valign="top">
           <div style="padding-top:50px; padding-bottom:50px">
            <span class="DarkHeaderSubSub"><?= $language['label']['title0']; ?></span><br>
            <span class="DarkHeader"><?= $language['label']['title1']; ?></span><br>
            <span class="DarkHeaderSubSub"><?= $language['label']['title2']; ?></span>
           </div>
          </td>
          <td width="300" align="right" valign="top">
          	<div id="ChangeLogTable"></div>
              <br/>
            <div id="WeatherTable"></div>
          </td>
        </tr>
      </table>
      <div id="localView" style="display:none">
          <a href="javascript:displayLocalView('off')" class="CalendarTitle"><img src="images/next_button.gif" width="19" height="19" border="0" align="absmiddle"> Return to <?= $default['title1']; ?> Home Page</a><br>
          <br>
          <iframe frameborder="0" height="800" width="98%" name="local"></iframe>
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
  <!-- InstanceBeginEditable name="js" -->
	<!--<script type="text/javascript" src="/Common/js/ps/treasure.js"></script>-->
 
 	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
       
	<script type="text/javascript" src="/Common/js/yahoo/utilities/utilities.js"></script>
    <script type="text/javascript" src="/Common/js/yahoo/datasource/datasource-beta-min.js"></script>
	<script type="text/javascript" src="/Common/js/yahoo/datatable/datatable-beta-min.js"></script>
    
	<script src="js/YUIhome.js" type="text/javascript"></script>
    
	<script type="text/javascript" charset="utf-8">
		$$("a").each( function(link) {
			new Tooltip(link, {backgroundColor: "#FF8C00", borderColor: "#000", textColor: "#000", textShadowColor: "#FFF", opacity:.9});
		});
		$$("input").each( function(input) {
			new Tooltip(input, {backgroundColor: "#E6AC00", borderColor: "#000", textColor: "#000", textShadowColor: "#FFF", opacity:.9});
		});		
	</script> 
<!-- InstanceEndEditable -->   
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
