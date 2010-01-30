<?php
/**
 * Request System
 *
 * items.php allows enduser to enter items details.
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
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 
/**
 * - Form Validation
 */
include('vdaemon/vdaemon.php');


/* ------------- START SESSION VARIABLES --------------------- */
if ($_POST['stage'] == "four") {
	/* Set form variables as session variables */
	$_SESSION['total_items'] = htmlentities($_POST['total_items'], ENT_QUOTES, 'UTF-8');
	$_SESSION['total'] = htmlentities($_POST['total'], ENT_QUOTES, 'UTF-8');
	
	for ($i = 1; $i <= $_POST['total_items']; $i++) {
	    $qty = 'qty'.$i;
		$unit = 'unit'.$i;
		$part = 'part'.$i;
		$manuf = 'manuf'.$i;
		$descr = 'descr'.$i;
		$price = 'price'.$i;
		$cat = 'cat'.$i;
		$vt = 'vt'.$i;
		$plant = 'plant'.$i;
		$_SESSION[$qty]  = htmlentities($_POST[$qty], ENT_QUOTES, 'UTF-8');
		$_SESSION[$unit]  = htmlentities($_POST[$unit], ENT_QUOTES, 'UTF-8');
		$_SESSION[$part]  = htmlentities($_POST[$part], ENT_QUOTES, 'UTF-8');
		$_SESSION[$manuf]  = htmlentities($_POST[$manuf], ENT_QUOTES, 'UTF-8');
		$_SESSION[$descr]  = htmlentities($_POST[$descr], ENT_QUOTES, 'UTF-8');
		$_SESSION[$price]  = htmlentities(str_replace(',', '', $_POST[$price]), ENT_QUOTES, 'UTF-8');
		$_SESSION[$cat]  = htmlentities($_POST[$cat], ENT_QUOTES, 'UTF-8');
		$_SESSION[$vt]  = htmlentities($_POST[$vt], ENT_QUOTES, 'UTF-8');
		$_SESSION[$plant]  = htmlentities($_POST[$plant], ENT_QUOTES, 'UTF-8');
		
		/* Check that Item is completely filled out */				
		if (! empty($_POST[$descr])) {
			if (empty($_POST[$manuf])) { 
			  $COLUMNS = "<li>Manufacturer Number<BR>";
			}		
			if (empty($_POST[$price])) { 
			  $COLUMNS = "<li>Price<BR>";
			}
			if (empty($_POST[$cat])) { 
			  $COLUMNS .= "<li>Category<BR>";
			}	
			// Turned off Oct 29, 2005, Scott Warren says only an over all plant is required
			//if (empty($_POST[$plant])) {
			//  $COLUMNS .= "<li>Plant";   
			//}
		}
	}
	
	/* Display errors if there is one or more */
	if (isset($COLUMNS)) {
		$_SESSION['error'] = "Please complete the following information for <strong>Item $i</strong><br>".
							 "<blockquote>$COLUMNS</blockquote><br><br>".
							 "<img src=\"/Common/images/required.gif\" border=\"0\" align=\"absmiddle\"> Denotes a Required Field<br>";
		
		header("Location: ../error.php");
		exit();
	}
	print_r($_SESSION);
	exit();
	/* Forward user to next page */
	header("Location: authorization.php");
}

/* ------------- END SESSION VARIABLES --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */
$cat_sql = $dbh->prepare("SELECT id, name 
						  FROM Standards.Category 
						  WHERE status = '0'
						  ORDER BY name");
$units_sql = $dbh->prepare("SELECT id, name FROM Standards.Units ORDER BY name");
$plants_sql = $dbh->prepare("SELECT id, name
							 FROM Standards.Plants
							 WHERE status = '0'
							 ORDER BY name");	 
/* ------------- END DATABASE CONNECTIONS --------------------- */

$items_help="<b>Have you already entered items but need to increase the count?</b><br><br>Press the <b>Next</b> button to store your currently entered items. Then press <b>Back</b> on the next page to return. Now that your items are stored increase the item count.";


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
	<?php
	/* Create Calculate javascript for totaling items */
	$lines = isset($_POST['total_items']) ? $_POST['total_items'] : 10;
	for ($cnt = 2; $cnt <= $lines; $cnt++) {
	  $clear_price .= "var price$cnt = Form.price$cnt.value.replace(/\,/g, '');\n";
	  $items_total .= " + (parseFloat(Form.qty$cnt.value) * parseFloat(price$cnt))";
	  //$items_total .= " + (parseFloat(Form.qty$cnt.value) * parseFloat(Form.price$cnt.value))";
	}
	?>
	<script language="javascript">
	function cent(amount) {
	// returns the amount in the .99 format 
		amount -= 0;
		amount = (Math.round(amount*100))/100;
		return (amount == Math.floor(amount)) ? amount + '.00' : (  (amount*10 == Math.floor(amount*10)) ? amount + '0' : amount);
	}
	
	function Calculate()
	{
	  var price1 = Form.price1.value.replace(/\,/g, '');
	  <?= $clear_price; ?>
	  
	  var total = (parseFloat(Form.qty1.value) * parseFloat(price1)) <?= $items_total; ?>;
	  Form.total.value = cent(total);
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
    <!-- InstanceBeginEditable name="main" -->
    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><br>
                        <table  border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                            <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                            <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
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
                                  <td width="15%" class="wizardPast">Vendor</td>
                                  <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Items</div></td>
                                  <td width="25%" class="wizardFuture"><div align="center" class="wizardFuture">Authorization</div></td>
                                  <td width="25%" class="wizardFuture"><div align="center" class="wizardFuture">Information</div></td>
                                  <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                                </tr>
                            </table></td>
                          </tr>
                        </table>
                        <br>
                        <br>                       
                          <table  border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td height="30"><form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0" runat="vdaemon">
						      <div align="right">&nbsp;Number of Items
                                <select name="total_items" id="total_items" onchange="this.form.submit();">
                                  <option value="10" <?php if ($_POST['total_items'] == 10) { echo "selected"; } ?>>10</option>
								  <option value="15" <?php if ($_POST['total_items'] == 15) { echo "selected"; } ?>>15</option>
                                  <option value="20" <?php if ($_POST['total_items'] == 20) { echo "selected"; } ?>>20</option>
								  <option value="25" <?php if ($_POST['total_items'] == 25) { echo "selected"; } ?>>25</option>
                                  <option value="30" <?php if ($_POST['total_items'] == 30) { echo "selected"; } ?>>30</option>
								  <option value="35" <?php if ($_POST['total_items'] == 35) { echo "selected"; } ?>>35</option>
                                  <option value="40" <?php if ($_POST['total_items'] == 40) { echo "selected"; } ?>>40</option>
								  <option value="45" <?php if ($_POST['total_items'] == 45) { echo "selected"; } ?>>45</option>
                                  <option value="50" <?php if ($_POST['total_items'] == 50) { echo "selected"; } ?>>50</option>								  
                                </select>
                                <a href="javascript:void();" <?php help('', $items_help, 'default'); ?>><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a>							  &nbsp;&nbsp;</div>
						    </form></td>
                            </tr>
                            <tr>
                              <td><form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
                                <table border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="BGAccentVeryDark"><div align="left">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                          <tr>
                                            <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Item Information...</td>
                                            <td width="50%" class="DarkHeaderSubSub">&nbsp;</td>
                                          </tr>
                                        </table>
                                    </div></td>
                                  </tr>
                                  <tr>
                                    <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                                        <tr class="BGAccentDark">
                                          <td><div align="center"><?= $WARNING; ?></div></td>
                                          <td><strong>&nbsp;Unit&nbsp;<?= $WARNING; ?></strong></td>
                                          <td><strong>&nbsp;Company#&nbsp;</strong></td>
                                          <td><strong>&nbsp;Manuf#&nbsp;<?= $WARNING; ?></strong></td>
                                          <td><strong>&nbsp;Item Description&nbsp;<?= $WARNING; ?></strong></td>
                                          <td><strong>&nbsp;Price&nbsp;<?= $WARNING; ?></strong></td>
                                          <td><strong>&nbsp;Category&nbsp;<?= $WARNING; ?></strong></td>
                                          <td><strong>&nbsp;CT</strong></td>
                                          <td><strong>&nbsp;Plant</strong></td>
                                        </tr>
										<?php
										/* Get total number of items */
										if (isset($_POST['total_items'])) {
											$total_items = $_POST['total_items'];
										} elseif (isset($_SESSION['total_items'])) {
											$total_items = $_SESSION['total_items'];
										} else {
											$total_items = 10;
										}
										
										/* Get Session variables for items */
										for ($items = 1; $items <= $total_items; $items++) {
											$qty = 'qty'.$items;
											$unit = 'unit'.$items;
											$part = 'part'.$items;
											$manuf = 'manuf'.$items;
											$descr = 'descr'.$items;
											$price = 'price'.$items;
											$cat = 'cat'.$items;
											$vt = 'vt'.$items;
											$plant = 'plant'.$items;
											
											$qty = (isset($_SESSION[$qty])) ? $_SESSION[$qty] : 1;
											$unit = (isset($_SESSION[$unit])) ? $_SESSION[$unit] : EA;
											$part = (isset($_SESSION[$part])) ? $_SESSION[$part] : $blank;
											$manuf = (isset($_SESSION[$manuf])) ? $_SESSION[$manuf] : $blank;
											$descr = (isset($_SESSION[$descr])) ? $_SESSION[$descr] : $blank;
											$price = (isset($_SESSION[$price])) ? $_SESSION[$price] : 0;
											$cat = (isset($_SESSION[$cat])) ? $_SESSION[$cat] : $blank;
											$vt = (isset($_SESSION[$vt])) ? $_SESSION[$vt] : $blank;
											$plant = (isset($_SESSION[$plant])) ? $_SESSION[$plant] : $blank;
										?>
                                        <tr class="BGAccentLight">
                                          <td><input name="qty<?= $items; ?>" type="text" id="qty<?= $items; ?>" size="8" maxlength="8" value="<?= $qty; ?>" onBlur="Calculate();"></td>
                                          <td><select name="unit<?= $items; ?>" id="unit<?= $items; ?>">
                                            <option value="0">Select One</option>
                                            <?php
											  $unit_sth = $dbh->execute($units_sql);
											  while($unit_sth->fetchInto($UNITS)) {
												if (isset($unit)) {
												  $selected = ($unit == $UNITS[id]) ? selected : $blank;
												}
												print "<option value=\"".$UNITS[id]."\" ".$selected.">".ucwords(strtolower($UNITS[name]))."</option>\n";
											  }
											?>
                                          </select></td>
                                          <td align="center"><input name="part<?= $items; ?>" type="text" id="part<?= $items; ?>" size="12" maxlength="25" value="<?= $part; ?>"></td>
                                          <td align="center"><input name="manuf<?= $items; ?>" type="text" id="manuf<?= $items; ?>" size="12" maxlength="25" value="<?= stripslashes($manuf); ?>"></td>
                                          <td><input name="descr<?= $items; ?>" type="text" id="descr<?= $items; ?>" size="40" maxlength="100" value="<?= stripslashes($descr); ?>"></td>
                                          <td><input name="price<?= $items; ?>" type="text" id="price<?= $items; ?>" size="10" maxlength="15" value="<?= $price; ?>" onBlur="Calculate();"></td>
                                          <td><select name="cat<?= $items; ?>">
                                            <option value="0">Select One</option>
                                            <?php
											  $cat_sth = $dbh->execute($cat_sql);
											  while($cat_sth->fetchInto($CAT)) {
												if (isset($cat)) {
												  $selected = ($cat == $CAT[id]) ? selected : $blank;
												}
												print "<option value=\"".$CAT[id]."\" ".$selected.">".ucwords(strtolower($CAT[name]))."</option>\n";
											  }
											 ?>
                                          </select></td>
                                          <td><input name="vt<?= $items; ?>" type="text" id="vt<?= $items; ?>" size="5" maxlength="10" value="<?= $vt; ?>"></td>
                                          <td><select name="plant<?= $items; ?>">
                                            <option value="0">Select One</option>
                                            <?php
											  $plant_sth = $dbh->execute($plants_sql);
											  while($plant_sth->fetchInto($PLANTS)) {
												if (isset($plant)) {
												  $selected = ($plant == $PLANTS[id]) ? selected : $blank;
												}
												print "<option value=\"".$PLANTS[id]."\" ".$selected.">".ucwords(strtolower($PLANTS[name]))."</option>\n";
											  }
											?>
                                          </select></td>
                                        </tr>
                                        <?php } ?>
									    <tr><td colspan="5" align="right"><strong>Total: </strong></td>
									    <td colspan="4"><input name="total" type="text" id="total" size="10" maxlength="15" value="<?= $_SESSION['total']; ?>" readonly>
									      <vlvalidator name="total" type="compare" control="total" validtype="string" comparevalue="NaN" comparecontrol="total" operator="ne">
									      <vlvalidator name="total2" type="compare" control="total" validtype="string" comparevalue="0.00" comparecontrol="total" operator="ne">
									      <vlvalidator name="total3" type="required" control="total">
									      <a href="javascript:void();" <?php help('', $items_help, 'default'); ?>><img src="../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a>&nbsp;<vllabel form="Form" errtext="There is a number missing from the Price column." validators="total" errclass="valError"></vllabel><vllabel form="Form" errtext="Items listed have no total." validators="total3" errclass="valError"></vllabel><vllabel form="Form" errtext="Items listed have a $0.00 total." validators="total2" errclass="valError"></vllabel></td>
									    </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                          <td width="50%" height="26" valign="bottom"><a href="index.php">&nbsp;<img src="../images/button.php?i=b70.png&l=Back" border="0"></a></td>
                                          <td width="50%" valign="bottom"><div align="right">
                                              <input name="total_items" type="hidden" id="total_items" value="<?= $total_items; ?>">
                                              <input name="stage" type="hidden" id="stage" value="four">
                                              <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Next" border="0">
										&nbsp;</div></td>
                                        </tr>
                                    </table></td>
                                  </tr>
                                </table>
                              </form></td>
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