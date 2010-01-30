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
		$_SESSION[$price]  = htmlentities($_POST[$price], ENT_QUOTES, 'UTF-8');
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
/* SQL for PO list */
$po_sql = "SELECT *
			 FROM Items i
			   INNER JOIN PO p ON p.id=i.type_id
			 WHERE p.sup='$_SESSION[supplier]' 
			   AND p.status <> 'C'
			   AND i.price <> '0.00'
			 ORDER BY i.id DESC";
$po_query = $dbh->prepare($po_sql);
$po_sth = $dbh->execute($po_query);								 
/* ------------- END DATABASE CONNECTIONS --------------------- */

/* -- Set Purchase History status from users settings -- */
$_SESSION['purchaseHistory'] = 'off';
if ($_SESSION['purchaseHistory'] == 'off') {
	$phItemsMenuStatus = 'display';
	$vendorHistoryStatus = 'none';
} else {
	$phItemsMenuStatus = 'none';
	$vendorHistoryStatus = 'display';
}


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
  <!-- InstanceBeginEditable name="head" -->
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/effects.js"></script> 
	<script type="text/javascript">
		function switchHistory(mode) {
			if (mode=="off") {
				Effect.DropOut('vendorHistory');
				Effect.BlindDown('phItemsMenu', {delay:1.0});
			} else {
				Effect.BlindDown('vendorHistory');	
				Effect.BlindUp('phItemsMenu', {delay:1.0});
			}
		}
	</script>

    <link href="/Common/js/greybox/greybox.css" rel="stylesheet" type="text/css" media="all">
	<script type="text/javascript" src="/Common/js/greybox/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox/AmiJS.js"></script>
    <script type="text/javascript" src="/Common/js/greybox/greybox.js"></script>	
	
	<link rel="stylesheet" type="text/css" href="/Common/js/yahoo/tabview/assets/tabview.css">
	<link rel="stylesheet" type="text/css" href="/Common/js/yahoo/css/module_tabs.css">
	<script type="text/javascript" src="/Common/js/yahoo/yahoo/yahoo.js"></script>
	<script type="text/javascript" src="/Common/js/yahoo/event/event.js"></script>
	<script type="text/javascript" src="/Common/js/yahoo/dom/dom.js"></script>
	<script type="text/javascript" src="/Common/js/yahoo/tabview/tabview.js"></script>
	<script type="text/javascript">
		YAHOO.example.init = function() {
			var tabView = new YAHOO.widget.TabView();
			
			YAHOO.util.Event.onContentReady('top-stories', function() {
				var modules = YAHOO.util.Dom.getElementsByClassName('mod', 'div', this);
				
				YAHOO.util.Dom.batch(modules, function(module) {
					tabView.addTab( new YAHOO.widget.Tab({
						label: module.getElementsByTagName('h3')[0].innerHTML,
						contentEl: YAHOO.util.Dom.getElementsByClassName('bd', 
								'div', module)[0]
					}));
					YAHOO.util.Dom.setStyle(module, 'display', 'none'); /* hide modules */
				});
		
				tabView.set('activeIndex', 0); // make first tab active
				tabView.appendTo(this); // append to "top-stories"
			});
		};
		
		YAHOO.example.init();
	</script>
		 
	<script language="javascript">
		Effect.CashRegister = Class.create();
		Object.extend(Object.extend(Effect.CashRegister.prototype, Effect.Base.prototype), {
		initialize: function(element, price) {
		  var options = arguments[2] || {};
		  this.element = $(element);
		  this.startPrice = parseFloat(this.element.innerHTML.substring(1));
		  this.finishPrice = price;
		  this.delta = (this.finishPrice-this.startPrice);
		  this.start(options);
		},
		update: function(position) {
		  var value = (this.startPrice + (this.delta*position)).toString().split('.');
		  var cent  = value.length==1 ? '00' : (
			value[1].length == 1 ? value[1]+"0" : value[1].substring(0,2));
		  Element.update(this.element, '$' + value[0] + '.' + cent);
		}
		});	
		
		function cent(amount) {
		// returns the amount in the .99 format 
			amount -= 0;
			amount = (Math.round(amount*100))/100;
			return (amount == Math.floor(amount)) ? amount + '.00' : (  (amount*10 == Math.floor(amount*10)) ? amount + '0' : amount);
		}
		
		function Calculate()
		{
		  total = $('qty1').value * $('price1').value;
		  //$('totalAmount').innerHTML = cent(total);
		  new Effect.CashRegister('totalAmount', total, { afterFinish:function(effect){ new Effect.Highlight(effect.element) }});
		}
	</script>
	
	<script type="text/javascript" src="../js/dynamicInputItems.js"></script>	
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
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="200" valign="top"><div id="itemsMenu" style="display:<?= $itemsMenuStatus; ?>"><?php include('../include/menu/items.php'); ?>,</div></td><td><br>
						  <table  border="0" cellpadding="0" cellspacing="0">
                            <tr>
                              <td rowspan="2"><img src="../images/spacer.gif" width="105" height="5"></td>
                              <td><a href="index_beta.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                              <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                              <td><a href="information_beta.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                              <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                              <td><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                              <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                              <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                              <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                              <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                            </tr>
                            <tr>
                              <td colspan="9"><table width="100%"  border="0">
                                  <tr>
                                    <td width="15%" class="wizardPast">Vendor</td>
                                    <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Information</div></td>
                                    <td width="25%" align="center" class="wizardCurrent">Items</td>
                                    <td width="25%" class="wizardFuture"><div align="center">Authorization</div></td>
                                    <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                                  </tr>
                              </table></td>
                            </tr>
                      </table></td>
                    </tr>
                  </table>
                    <br>                       
                          <table  border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td height="30" align="right">&nbsp;</td>
                            </tr>
                            <tr>
                              <td><form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
                                <table border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="BGAccentVeryDark"><div align="left">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                          <tr>
                                            <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<a href="javascript:void();" onClick="new Effect.toggle('items','blind')" class="black" <?php help('', 'Show or Hide the Item Information', 'default'); ?>><strong><img src="../images/text.gif" width="16" height="16" border="0" align="texttop"></strong></a>&nbsp;Item Information...</td>
                                            <td width="50%" class="DarkHeaderSubSub">&nbsp;</td>
                                          </tr>
                                        </table>
                                    </div></td>
                                  </tr>
                                  <tr>
                                    <td class="BGAccentVeryDarkBorder">
									<div id="doc">
									<div id="top-stories">
									<div id="mod"><h3>ITEM 1</h3></div>
									<div id="bd">
									<table width="100%" border="0" class="BGAccentVeryDarkBorder">
                                      <tr>
                                        <td colspan="2" class="CalendarHeader">ITEM 1 </td>
                                      </tr>
                                      <tr>
                                        <td height="5" colspan="2"><img src="../images/spacer.gif" width="5" height="5"></td>
                                      </tr>
                                      <tr>
                                        <td>Quanty</td>
                                        <td><input name="qty1" type="text" id="qty1" size="8" maxlength="8" value="<?= $qty; ?>" onBlur="Calculate();"></td>
                                      </tr>
                                      <tr>
                                        <td>Units:</td>
                                        <td><select name="unit1" id="unit1">
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
                                      </tr>
                                      <tr>
                                        <td>Company Part Number: </td>
                                        <td><input name="part1" type="text" id="part1" size="25" maxlength="25" value="<?= $part; ?>"></td>
                                      </tr>
                                      <tr>
                                        <td>Manufactures Part Number: </td>
                                        <td><input name="manuf1" type="text" id="manuf1" size="25" maxlength="25" value="<?= stripslashes($manuf); ?>"></td>
                                      </tr>
                                      <tr>
                                        <td>Item Description</td>
                                        <td><input name="descr1" type="text" id="descr1" size="100" maxlength="100" value="<?= stripslashes($descr); ?>"></td>
                                      </tr>
                                      <tr>
                                        <td>Price:</td>
                                        <td><input name="price1" type="text" id="price1" size="15" maxlength="15" value="<?= $price; ?>" onBlur="Calculate();"></td>
                                      </tr>
                                      <tr>
                                        <td>Category:</td>
                                        <td><select id="cat1" name="cat1">
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
                                      </tr>
                                      <tr>
                                        <td>Company Tool Number: </td>
                                        <td><input name="vt1" type="text" id="vt1" size="10" maxlength="10" value="<?= $vt; ?>"></td>
                                      </tr>
                                      <tr>
                                        <td>&nbsp;</td>
                                        <td><table border="0" align="right">
                                          <tr>
                                            <td width="20"><a href="javascript:onClick=showBlock('item');"><img src="/Common/images/addition.gif" width="16" height="16" border="0"></a></td>
                                            <td width="20">&nbsp;</td>
                                          </tr>
                                        </table></td>
                                      </tr>
                                    </table>
									</div>
									<br><?php for ($i=2; $i <= 50; $i++) { ?><div id="item<?= $i; ?>"></div> <?php } ?>
									</div></div>
									</td>
                                  </tr>
                                  <tr>
                                    <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                          <td width="33%" height="26" valign="top"><a href="information_beta.php">&nbsp;<img src="../images/button.php?i=b70.png&l=Back" border="0">
                                            <input name="item_count" type="hidden" id="item_count" value="1">
                                          </a></td>
                                          <td width="34%" align="center">Total: <span style="font-weight:bold; font-size:20px" id="totalAmount">$0.00
                                          </span></td>
                                          <td width="33%" valign="top"><div align="right">
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
                          <br>
						  <div id="vendorHistory" style="display:<?= $vendorHistoryStatus; ?>">
						  <form name="Form2" id="Form2" method="post" action="<?= $_SESSION['PHP_SELF']; ?>">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td><table width="750" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td height="30" class="BGAccentVeryDark">&nbsp;&nbsp;<a href="javascript:switchHistory('off');" class="black" <?php help('', 'Hide Purchase History', 'default'); ?>><img src="/Common/images/history.gif" width="18" height="18" border="0" align="texttop">&nbsp;<strong>Purchase History...</strong></a></td>
                                </tr>
                                <tr>
                                  <td class="BGAccentVeryDarkBorder">
                                        <table width="100%"  border="0">
                                          <tr class="BGAccentDark">
                                            <td width="25"></td>
                                            <td width="50"></td>
                                            <td width="100" align="center"><strong>&nbsp;Company#&nbsp;</strong></td>
                                            <td width="100" align="center"><strong>&nbsp;Manuf#</strong></td>
                                            <td><strong>&nbsp;Item Description</strong></td>
                                            <td width="75"><strong>&nbsp;Price&nbsp;</strong></td>
                                          </tr>
                                          <?php	while ($po_sth->fetchInto($ITEMS)) { ?>
                                          <tr class="BGAccentLight">
                                            <td align="center"><input type="checkbox" name="checkbox" value="<?= $ITEMS['id']; ?>"></td>
                                            <td align="right" nowrap><?= $ITEMS['qty']; ?>
                                                <?= strtoupper($ITEMS[unit]); ?></td>
                                            <td align="center" nowrap><?= $ITEMS['part']; ?></td>
                                            <td align="center" nowrap><?= $ITEMS['manuf']; ?></td>
                                            <td nowrap><?= $ITEMS['descr']; ?></td>
                                            <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td width="15" align="center">$</td>
                                                  <td align="right" nowrap><?= $ITEMS['price']; ?></td>
                                                </tr>
                                            </table></td>
                                          </tr>
                                          <?php } ?>
                                        </table>
                                  </td>
                                </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                            </tr>
                            <tr>
                              <td>
								  <input name="action" type="hidden" id="action" value="copy">
								  <input name="imageField" type="image" id="imageField" src="../images/button.php?i=b70.png&l=Copy" alt="Copy History Item" border="0">
							  </td>
                            </tr>
                          </table>
					  </form>
					  </div>
                  </td>
                </tr>
              </tbody>
          </table>
		  </td>
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