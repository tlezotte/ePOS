<?php
/**
 * Request System
 *
 * index.php allows enduser to select supplier for PO.
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
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');


/* ----- CLEAR ERRORS ----- */
unset($_SESSION['error']);
unset($_SESSION['redirect']);

/* ------------- START SESSION VARIABLES --------------------- */
if ($_POST['stage'] == "one") {
	/* Set form variables as session variables */
	$_SESSION['supplier']  = htmlentities($_POST['supplier'], ENT_QUOTES, 'UTF-8');

	/* Forward user to next page */
	header("Location: information_beta.php"); 
}

/* Unset all Session variables, then resets username and access */
if ($_GET['stage'] == "new") {
	clearSession();
}
/* ------------- END SESSION VARIABLES --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */
/* Get My Suppliers */
$mySuppliers_sql = $dbh->prepare("SELECT DISTINCT s.BTVEND AS id, BTNAME AS name, BTVEND AS vid, s.BTADR1 AS address, s.BTADR3 AS city, s.BTPRCD AS state, s.BTPOST AS zip5, BTWPAG AS web
		     		   			  FROM Standards.Vendor s
								    INNER JOIN PO p ON p.sup=s.BTVEND
		 	 		   			  WHERE p.req = ".$_SESSION['eid']." AND s.BTSTAT = 'A'
		 	 		   			  ORDER BY s.BTNAME");
$suppliers_sth = $dbh->execute($mySuppliers_sql);
$num_rows = $suppliers_sth->numRows();
/* ------------- END DATABASE CONNECTIONS --------------------- */

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS="init();";
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
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT type="text/javascript" SRC="/Common/js/overlibmws/calendarmws.js"></SCRIPT>
	
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css">	
	
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">
	
	<script type="text/javascript">
		function switchVendor(mode) {
			if (mode=="search") {
				Effect.BlindUp('myVendSection');
				Effect.BlindDown('vendSearchSection', {delay: 1.1});
				document.getElementById("switchVendor").innerHTML = "View My Vendors";
				document.getElementById("switchVendor").href = "javascript:switchVendor('my')";
			} else {
				Effect.BlindUp('vendSearchSection');			
				Effect.BlindDown('myVendSection', {delay: 1.1});
				document.getElementById("switchVendor").innerHTML = "Search Vendors";
				document.getElementById("switchVendor").href = "javascript:switchVendor('search')";
			}
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
    <!-- InstanceBeginEditable name="main" -->    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><div id="noPrint">
				    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="200" valign="top"><?php include('../include/menu/vendor.php'); ?></td>
                        <td><br>
                          <table  border="0" cellpadding="0" cellspacing="0">
                          <tr>
						    <td rowspan="2"><img src="../images/spacer.gif" width="110" height="5"></td>
                            <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                            <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                            <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                            <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                            <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                            <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                            <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                            <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                            <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                          </tr>
                          <tr>
						    <td colspan="9"><table width="100%"  border="0">
                                <tr>
								  
                                  <td width="15%" class="wizardCurrent">Vendor</td>
                                  <td width="25%" class="wizardFuture"><div align="center">Information</div></td>
                                  <td width="25%" class="wizardFuture"><div align="center">Items</div></td>
                                  <td width="25%" class="wizardFuture"><div align="center">Authorization</div></td>
                                  <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                                </tr>
                            </table></td>
                          </tr>
                        </table></td></tr>
                    </table>
				  </div>
                    <br>
                    <br>
                          <table  border="0" align="center" cellpadding="0" cellspacing="0">
                            
                            <tr>
                              <td><?php if ($num_rows == 0) { ?>
							    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td height="40" align="center" valign="top" class="DarkHeader">No Vendors Found</td>
                                  </tr>
                                </table>
								<?php require('../include/searchVendor.php'); ?>
							  <?php } else { ?>
							  <div id="myVendSection">
							  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td valign="bottom">&nbsp;</td>
                                  <td align="right"><table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= $_SERVER['PHP_SELF']."?stage=new"; ?>" <?php help('', 'Remove current Request from memory and start a new Request', 'default'); ?>><img src="../images/button.php?i=b110.png&l=Clear Request" border="0"></a>&nbsp;&nbsp; </td>
                                      </tr>
                                  </table></td>
                                </tr>
                              </table>
							  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="Form" id="Form" runat="vdaemon">
                                <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="BGAccentVeryDark"><div align="left">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                          <tr>
                                            <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Select My Vendor...</td>
                                            <td width="50%" valign="middle"><div align="right">&nbsp;&nbsp;</div>											</td>
                                          </tr>
                                        </table>
                                    </div></td>
                                  </tr>
                                  <tr>
                                    <td class="BGAccentVeryDarkBorder"><table width="885" border="0">
                                        <tr>
                                          <td width="20" height="25" class="BGAccentDark"><div align="center">
                                            <vllabel form="Form" validators="supplier" class="valRequired" errclass="valError2"></vllabel>
                                            <vlvalidator name="supplier" type="required" control="supplier">
                                          </div></td>
                                          <td width="300" class="BGAccentDark"><strong>&nbsp;<?= $language['label']['vendName']; ?><img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
                                          <td width="350" class="BGAccentDark"><strong>&nbsp;<?= $language['label']['vendAddress']; ?></strong></td>
                                          <td width="200" class="BGAccentDark"><strong>&nbsp;<?= $language['label']['vendCity']; ?></strong></td>
                                          <td width="25" class="BGAccentDark"><strong>&nbsp;<?= $language['label']['vendState']; ?>&nbsp;</strong></td>
                                          <td width="35" class="BGAccentDark"><strong>&nbsp;<?= $language['label']['vendZip']; ?></strong></td>
                                        </tr>									
										<?php
											/* Loop through list of POs */	
											while($suppliers_sth->fetchInto($SUPPLIER)) {
											/* Line counter for alternating line colors */
											$counter++;
											$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
										?>
                                        <tr <?php pointer($row_color); ?>>
                                          <td class="padding" bgcolor="#<?= $row_color; ?>"><input  name="supplier" type="radio" value="<?= $SUPPLIER['id']; ?>" <?php if ($_SESSION['supplier'] == $SUPPLIER['id']) { echo "checked"; } ?>></td>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><a href="../Administration/vendor_details.php?id=<?= $SUPPLIER[id]; ?>" title="<?= caps($SUPPLIER['name']); ?>&#39;s Details" <?php help('', 'Click here to view information for '.ucwords(strtolower($SUPPLIER[name])).'', 'default'); ?> onclick="return GB_show(this.title, this.href, 511, 400)"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>
										  <?php if (!empty($SUPPLIER['web'])) { ?>
										  <a href="http://<?= $SUPPLIER['web']; ?>" title="<?= caps($SUPPLIER['name']); ?>'s website" <?php help('', 'Click here to view '.caps($SUPPLIER[name]).' website.', 'default'); ?> rel="gb_page_fs[]"><img src="/Common/images/globe.gif" width="18" height="18" border="0" align="absmiddle"></a>
										  <?php } ?>
										  <?= ucwords(strtolower($SUPPLIER['name'])) . " (".strtoupper($SUPPLIER['vid']).")"; ?></td>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="padding">
										  <?php if (strlen($SUPPLIER['address']) > 0) { ?>
										  <a href="http://maps.google.com/maps?q=<?= $SUPPLIER['address']; ?> <?= $SUPPLIER['city']; ?>, <?= $SUPPLIER['state']; ?> <?= $SUPPLIER['zip5']; ?>&om=1" title="<?= caps($SUPPLIER['name']); ?>&#39;s location" rel="gb_page_fs[]"><img src="/Common/images/map.gif" width="20" height="20" border="0" align="absmiddle" <?php help('', "Get a map showing ".caps($SUPPLIER['name'])."\'s location", 'default'); ?>></a>
										  <?php } ?>
										  <?= ucwords(strtolower($SUPPLIER['address'])); ?></td>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= ucwords(strtolower($SUPPLIER['city'])); ?></td>
                                          <td class="padding" bgcolor="#<?= $row_color; ?>"><?= strtoupper($SUPPLIER['state']); ?></td>
                                          <td nowrap bgcolor="#<?= $row_color; ?>" class="padding"><?= $SUPPLIER['zip5']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                                  </tr>
                                  <tr>
                                    <td>&nbsp;
                                      <input name="next" type="image" id="next" src="../images/button.php?i=b70.png&l=Next" border="0">
                                    <input name="stage" type="hidden" id="stage" value="one"></td></tr>
                              </table></form></div>
								<div id="vendSearchSection" style="display:none"><?php require('../include/searchVendor.php'); ?></div>							  
							  <?php } ?></td>
                            </tr>
                          </table>
                        <br>
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