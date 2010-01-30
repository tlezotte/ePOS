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



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?= $default['title1']; ?></title>
    <meta http-equiv="imagetoolbar" content="no">
    <meta name="copyright" content="2004 Your Company" />
    <meta name="author" content="Thomas LeZotte" />
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/reset-fonts-grids/reset-fonts-grids-min.css" />   <!-- CSS Grid -->
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/fonts/fonts-min.css" />							<!-- Datatable, TabView -->
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/assets/skins/sam/datatable.css" />				<!-- Datatable -->
    <link type="text/css" rel="stylesheet" href="/Common/js/yahoo/assets/skins/custom/menu.css">  					<!-- Menu -->  
    <link type="text/css" rel="stylesheet" href="/Common/Print.css" media="print" />
    <link type="text/css" rel="stylesheet" href="default_yui.css" />
    <link type="text/css" rel="alternate stylesheet" title="seasonal" href="/Common/themes/winter/default.css" />
    <!--[if IE]><link type="text/css" rel="alternate stylesheet" title="autumn" href="/Common/themes/autumn/default_ie.css" /><![endif]-->
    <link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default.css" />
    <!--[if IE]><link type="text/css" rel="alternate stylesheet" title="night" href="/Common/themes/night/default_ie.css" /><![endif]-->      
    <?php if ($default['rss'] == 'on') { ?>
    <link rel="alternate" type="application/rss+xml" title="<?= $default['title1']; ?> Announcements" href="<?= $default['URL_HOME']; ?>/data/<?= $default['rss_file']; ?>">
    <link rel="alternate" type="application/rss+xml" title="Capital Acquisition Announcements" href="<?= $default['URL_HOME']; ?>/data/<?= $default['rss_file']; ?>">
    <?php } ?>
    <link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all"> 
        
	<script type="text/javascript" src="/Common/js/styleswitcher.js"></script>
    
    <script type="text/javascript" src="/Common/js/jquery/jquery-min.js"></script>
</head>
<body class="yui-skin-sam">
  <div id="doc3" class="yui-t7">
    <div id="hd">
      <div class="yui-gb">
          <div class="yui-u first">
            <img src="/Common/images/CompanyPrint.gif" name="Print" width="437" height="61" id="Print" />
            <a href="../home.php" title="<?= $default['title1']; ?>|Home Page"><img src="/Common/images/Company.gif" width="300" height="50" border="0"></a> 
          </div>
          <div class="yui-u"><!-- Center Title Area -->&nbsp;</div>
          <div class="yui-u">
              <div id="applicationTitle" style="font-weight:bold;font-size:115%;text-align:right"><?= $language['label']['title1']; ?>&nbsp;</div>
              <div id="loggedInUser" class="loggedInUser" style="text-align:right"><strong><a href="Administration/user_information.php" class="loggedInUser" title="User Task|Edit your user information"><?= caps($_SESSION['fullname']); ?></a></strong>&nbsp;<a href="logout.php" class="loggedInUser" title="User Task|Selecting [logout] will Log you out of the <?= $default[title1]; ?> and stop automatic cookie login">[logout]</a>&nbsp;</div>
            <div id="styleSwitcher" style="text-align:right">Themes: <span id="defaultStyle" class="style" title="Style Switcher|Default Colors"><a href="#" onclick="setActiveStyleSheet('default'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="seasonalStyle" class="style" title="Style Switcher|Winter Wonderland"><a href="#" onclick="setActiveStyleSheet('seasonal'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span><span id="nightStyle" class="style" title="Style Switcher|Night Time Colors"><a href="#" onclick="setActiveStyleSheet('night'); return false;"><img src="/Common/images/spacer.gif" width="14" height="10" border="0" /></a></span>&nbsp;</div>
          </div>
      </div>		      
    </div>
   <div id="bd">
	<div class="yui-g" id="mainMenu"><?php include('include/main_menu.php'); ?></div>
	<div class="yui-g">
	  <table width="99%"  border="0" align="center" cellpadding="0" cellspacing="0" id="HomePage">
        <tr>
          <td width="300" align="left" valign="top">
            <div id="searchPanel" style="border: thin solid #7F7F7F; background-color:#EDF5FF;padding:5px">
              <form name="form2" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
                <table  border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td><strong>Search:<input name="action" type="hidden" id="action" value="search"></strong></td>
                    <td><input name="number" type="text" id="number" size="8" maxlength="10" title="Enter a Request or PO number, then select the appropriate button."></td>
                    <td align="center">
                      <input name="po_id" type="image" id="po_id" title="Search by Request Number" src="images/button.php?i=w70.png&l=Num" border="0">
                      <input name="po" type="image" id="po" title="Search by PO Number" src="images/button.php?i=w70.png&l=PO" border="0">
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <div id="myRequestsTable" class="infoPanel"></div>
            <div id="currencyTable" class="infoPanel"></div>
            <div id="marketTable" class="infoPanel"></div></td>
          <td align="center" valign="top">
            <div style="padding-top:50px; padding-bottom:50px"> 
              <span class="DarkHeaderSubSub"><?= $language['label']['title0']; ?></span><br>
              <span class="DarkHeader"><?= $language['label']['title1']; ?></span><br>
              <span class="DarkHeaderSubSub"><?= $language['label']['title2']; ?></span>
            </div>
          </td>
          <td width="300" align="right" valign="top">
            <div id="ChangeLogTable" class="infoPanel"></div>
            <div id="WeatherTable" class="infoPanel"></div>
          </td>
        </tr>
      </table>
    <div id="ChangeLog" style="display:none">
      <img src="images/next_button.gif" width="19" height="19" border="0" align="absmiddle"> Return to <?= $default['title1']; ?> Home Page</a><br>
      <br>
      <iframe frameborder="0" height="800" width="98%" name="ChangeLog"></iframe>
    </div>      
   <div id="ft" style="padding-top:50px">
	 <div class="yui-gb">
        <div class="yui-u first"><?php include('include/copyright.php'); ?></div>
        <div class="yui-u"><!-- FOOTER CENTER AREA -->&nbsp;</div>
        <div class="yui-u" style="text-align:right"><!-- FOOTER RIGHT AREA -->&nbsp;</div>
	    </div>
     </div>  
   </div>
</div>
<script type="text/javascript" src="/Common/js/yahoo/yahoo-dom-event/yahoo-dom-event.js" ></script>		<!-- Menu, TabView, Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/container/container-min.js"></script> 				<!-- Menu -->
<script type="text/javascript" src="/Common/js/yahoo/menu/menu-min.js"></script> 						<!-- Menu -->
<script type="text/javascript" src="/Common/js/yahoo/utilities/utilities.js"></script>					<!-- Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/datasource/datasource-beta-min.js"></script>		<!-- Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/datatable/datatable-beta-min.js"></script>			<!-- Datatable -->
<script type="text/javascript" src="/Common/js/yahoo/connection/connection-min.js" ></script>			<!-- Datatable -->
<script type="text/javascript" src="js/YUIhome.js"></script>

<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
<script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>

<script type="text/javascript" src="js/jQdefault.js"></script>
<script type="text/javascript" src="/Common/js/jquery/cluetip/jquery.dimensions.js"></script>
<script type="text/javascript" src="/Common/js/jquery/cluetip/jquery.cluetip.js"></script>

<script type="text/javascript">
	/* ========== YUI Main Menu ========== */
	YAHOO.util.Event.onContentReady("productsandservices", function () {
		var oMenuBar = new YAHOO.widget.MenuBar("productsandservices", { autosubmenudisplay: true, hidedelay: 750, lazyload: true });
		oMenuBar.render();
	});
</script> 

<?php if (!$debug_page) { ?>
<!--<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "<?= $default['google_analytics']; ?>";
urchinTracker();
</script>-->
<?php } ?>
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