<?php
/**
 * Request System
 *
 * track.php track shipments.
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
 * - PEAR QuickForm
 */
require_once ('HTML/QuickForm.php');

/* Update Summary */
Summary($dbh, 'Track Shipment', $_SESSION['eid']);

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
	<script language="JavaScript">
		function clikker(a,b,c)
		{
			if (a.style.display =="")
			{
				a.style.display = "none";
				b.src="<?= $default['url_home']; ?>/images/button.php?i=b70.png&l=Show";
				c.value = 0
			}
			else
			{
				a.style.display="";
				b.src="<?= $default['url_home']; ?>/images/button.php?i=b70.png&l=Hide";
				c.value = 1
			}
		}
	</script>
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
  <!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_Main.lbi" --><?php
$menu1 = $default['url_home'] . "/PO/index.php";
$menu2 = $default['url_home'] . "/PO/list.php?action=my&access=0";
$menu3 = $default['url_home'] . "/PO/list.php";
$menu4 = $default['url_home'] . "/PO/search.php";
$menu5 = $default['url_home'] . "/PO/track.php";
$menu6 = $default['url_home'] . "/PO/prefered.php";
$menu7 = $default['url_home'] . "/PO/Reports/index.php";
?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td>&nbsp;</td>
	  <td>
		<table cellspacing="0" cellpadding="0" summary="" border="0">
			<tr>
		  	  <td nowrap>&nbsp;<a href="<?= $menu1; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu1) ? on : off; ?>" onmouseover="return overlib('Start a new Purchase Request',  TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="<?= $menu2; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu2) ? on : off; ?>" onmouseover="return overlib('List of your Purchase Requests', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">My Requisitions</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu3; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu3) ? on : off; ?>" onmouseover="return overlib('List all Purchase Requests', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">All Requisitions</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu4; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu4) ? on : off; ?>" onmouseover="return overlib('Search all Purchase Request', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Search</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu5; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu5) ? on : off; ?>" onmouseover="return overlib('Track Shipments or Deliveries from FedEx, UPS, USPS and DHL', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Tracking</a>&nbsp;</td>			  
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu7; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu7) ? on : off; ?>" onmouseover="return overlib('Reports on spending habits', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Reports</a>&nbsp;</td>
			  </tr>
		</table>
	  </td>
	  <td>&nbsp;</td>
	</tr>
</table><!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
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
    <!-- InstanceBeginEditable name="main" --><br>
      <table width="800"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="25" valign="top"><div align="right"><a href="javascript:void(0);"  <?php help("Show/Hide Search Criteria", 'default'); ?>><img src="../images/button.php?i=b70.png&l=Hide" border="0" id="ReqIcon" onClick="clikker(Req,ReqIcon,ReqForm);"></a>&nbsp;&nbsp;</div></td>
        </tr>
        <tr>
          <td>
		  <div style="display: display;" id="Req">
		  <input id="ReqForm" value="0" name="ReqForm" type="hidden">
		  <script type="text/javascript">Req.style.display='';</script>
		  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr class="BGAccentVeryDark">
              <td height="30">&nbsp;&nbsp;<span class="DarkHeaderSubSub">Track Shipments and Deliveries...</span> </td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="25%"><?php
						// -------- Start Generate Form ------------------
						$fedex =& new HTML_QuickForm('tracking', 'post','http://www.fedex.com/Tracking','track');
									  
						//$fedex->addElement('header', '', 'Feedback');
						$fedex->addElement('text', 'tracknumbers');
						//$fedex->addElement('hidden', 'action', 'track');
						$fedex->addElement('hidden', 'language', 'english');
						$fedex->addElement('hidden', 'cntry_code', 'us');
						//$fedex->addElement('hidden', 'mps', 'y');
						$fedex->addElement('hidden', 'ascend_header', '1');
						$fedex->addElement('hidden', 'clienttype', 'pluginff');
						$fedex->addElement('hidden', 'template_type', 'plugin');
						$fedex->addElement('image', 'submit', '../images/button.php?i=b70.png&l=FedEx');
						
						$fedex->addRule('tracknumbers', 'Please enter a FedEx tracking number', 'required', null, 'client');

						$fedex->display();		// Display Form
						?></td>
                  <td width="25%"><?php
						// -------- Start Generate Form ------------------
						$ups =& new HTML_QuickForm('trkinput', 'get','http://wwwapps.ups.com/WebTracking/processInputRequest','track');
									  
						//$ups->addElement('header', '', 'Feedback');
						$ups->addElement('text', 'tracknum');
						$ups->addElement('hidden', 'AgreeToTermsAndConditions', 'yes');
						$ups->addElement('hidden', 'HTMLVersion', '5.0');
						$ups->addElement('hidden', 'loc', 'en_US');
						$ups->addElement('hidden', 'Requester', 'UPSHome');
						$ups->addElement('image', 'submit', '../images/button.php?i=b70.png&l=UPS');
						
						$ups->addRule('tracknum', 'Please enter a UPS tracking number', 'required', null, 'client');

						$ups->display();		// Display Form
						?></td>
                  <td width="25%"><?php
						// -------- Start Generate Form ------------------
						$usps =& new HTML_QuickForm('getTrackNum', 'post','http://trkcnfrm1.smi.usps.com/netdata-cgi/db2www/cbd_243.d2w/output','track');
									  
						//$usps->addElement('header', '', 'Feedback');
						$usps->addElement('text', 'strOrigTrackNum');
						$usps->addElement('hidden', 'CAMEFROM', 'OK');
						$usps->addElement('image', 'submit', '../images/button.php?i=b70.png&l=USPS');
						
						$usps->addRule('strOrigTrackNum', 'Please enter a USPS tracking number', 'required', null, 'client');

						$usps->display();		// Display Form
						?></td>
                  <td width="25%"><?php
						// -------- Start Generate Form ------------------
						$dhl =& new HTML_QuickForm('frmTrackByNbr', 'post','http://track.dhl-usa.com/TrackByNbr.asp','track');
									  
						//$dhl->addElement('header', '', 'Feedback');
						$dhl->addElement('text', 'txtTrackNbrs');
						$dhl->addElement('image', 'submit', '../images/button.php?i=b70.png&l=DHL');
						
						$dhl->addRule('txtTrackNbrs', 'Please enter a DHL tracking number', 'required', null, 'client');

						$dhl->display();		// Display Form
						?></td>
                </tr>
              </table></td>
            </tr>
          </table>
		  </div></td>
        </tr>
      </table>
	  <br>
      <div align="center">
          <iframe height="100%" width="800" name="track" frameborder="0" scrolling="auto"></iframe>
      </div>
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
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- InstanceEndEditable --></div></td>
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
?>