<?php
/**
 * Request System
 *
 * index.php is default CER page.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package CER
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

/* ----- CLEAR ERRORS ----- */
unset($_SESSION['error']);
unset($_SESSION['redirect']);

/* ------------- START SESSION VARIABLES --------------------- */
if ($_POST['stage'] == "one") {
	/* Set form variables as session variables */
	$_SESSION['purpose'] = htmlentities($_POST['purpose'], ENT_QUOTES, 'UTF-8');
	$_SESSION['req']  = $_POST['req'];
	$_SESSION['date1'] = $_POST['date1'];
	$_SESSION['date2'] = $_POST['date2'];
	$_SESSION['req'] = $_POST['req'];
	$_SESSION['location'] = $_POST['location'];
	$_SESSION['company'] = $_POST['company'];
	$_SESSION['projClass'] = $_POST['projClass'];
	$_SESSION['capBudget'] = $_POST['capBudget'];
	$_SESSION['amtBudget'] = $_POST['amtBudget'];
	$_SESSION['budgetTrans'] = $_POST['budgetTrans'];
	$_SESSION['assetCost'] = $_POST['assetCost'];
	$_SESSION['accCost'] = $_POST['accCost'];
	$_SESSION['frtInstall'] = $_POST['frtInstall'];
	$_SESSION['otherCost'] = $_POST['otherCost'];
	$_SESSION['totalCost'] = $_POST['totalCost'];
	$_SESSION['netValue'] = $_POST['netValue'];
	$_SESSION['rateOfReturn'] = $_POST['rateOfReturn'];
	$_SESSION['netAsset'] = $_POST['netAsset'];
	$_SESSION['payback'] = $_POST['payback'];
	$_SESSION['assetLife'] = $_POST['assetLife'];
	$_SESSION['firstYr'] = $_POST['firstYr'];
	$_SESSION['secYr'] = $_POST['secYr'];
	$_SESSION['thirdYr'] = $_POST['thirdYr'];
	$_SESSION['forthYr'] = $_POST['forthYr'];
	$_SESSION['totalExp'] = $_POST['totalExp'];
	$_SESSION['firstExp'] = $_POST['firstExp'];
	$_SESSION['secExp'] = $_POST['secExp'];
	$_SESSION['thirdExp'] = $_POST['thirdExp'];
	$_SESSION['forthExp'] = $_POST['forthExp'];

	/* Forward user to next page */
	header("Location: authorization.php"); 
}

/* Unset all Session variables, then resets username and access */
if ($_GET['stage'] == "new") {
	clearSession();
}
/* ------------- END SESSION VARIABLES --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */
/* Project Originator from Users.Requesters */
$req_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst
						 FROM Users U, Standards.Employees E
						 WHERE U.eid = E.eid and U.requester = '1' and U.status = '0' ORDER BY E.lst ASC");
/* Getting plant locations from Standards.Plants */								
$plants_sql = $dbh->prepare("select id, name from Standards.Plants order by name");
/* Getting Your Company Companies from Standards.Companies */								
$company_sql = $dbh->prepare("SELECT id, name
						     FROM Standards.Companies
						     WHERE id > 0
							    AND status <> '1'							 
						     ORDER BY name");					 
/* ------------- END DATABASE CONNECTIONS --------------------- */

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
	<script type="text/javascript">function sf(){ document.Form.purpose.focus(); }</script>
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
	<script language="javascript">
	function Calculate()
	{
	  Form.totalCost.value = parseFloat(Form.assetCost.value) + parseFloat(Form.accCost.value) + parseFloat(Form.frtInstall.value) + parseFloat(Form.otherCost.value);
	  Form.totalExp.value = parseFloat(Form.firstExp.value) + parseFloat(Form.secExp.value) + parseFloat(Form.thirdExp.value) + parseFloat(Form.forthExp.value);
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_cer.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td>&nbsp;
	  
	  </td>
	  <td>
		<table cellspacing="0" cellpadding="0" summary="" border="0">
			<tr>
			  <td nowrap>&nbsp;<a href="index.php" class="off">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="list.php?action=my" class="off">My Requests </a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="list.php" class="off">All Requests</a>&nbsp;</td>
		  	  <!--<td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="../CER/search.php" class="off">Search</a>&nbsp;</td>-->
			</tr>
		</table>
	  </td>
	  <td>&nbsp;
	  
	  </td>
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
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td><br>
				  <div id="noPrint">
                    <table  border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="15">&nbsp;</td>
                      <td valign="bottom"><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                      <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                      <td valign="bottom"><img src="../images/vnFuture.gif" width="36" height="36"></td>
                      <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                      <td valign="bottom"><img src="../images/vnFuture.gif" width="36" height="36"></td>
                      <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                      <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                      <td width="15">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="9"><table width="100%"  border="0">
                          <tr>
                            <td width="21%" class="wizardCurrent"><div align="left">Information</div></td><td width="27%" class="wizardFuture"><div align="center">&nbsp;Authorization</div></td><td width="36%" class="wizardFuture"><div align="center">Justification&nbsp;&nbsp;&nbsp;</div></td>
                            <td width="16%" class="wizardFuture"><div align="center">&nbsp;&nbsp;&nbsp;Finished</div></td>
                          </tr>
                      </table></td>
                    </tr>
                  </table>
				  </div>
                    <br>
                        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" name="Form" id="Form">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
						  	<tr><td height="30" valign="top"><div align="right"><a href="<?= $_SERVER['PHP_SELF']."?stage=new"; ?>" <?php help('', 'Start a new Capital Expense Request', 'default'); ?>><img src="../images/button.php?i=b70.png&l=New" border="0" class="button"></a>&nbsp;&nbsp;</div></td>
						  	</tr>
                            <tr>
                              <td class="BGAccentVeryDark"><div align="left">
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Capital Expenditure Request...</td>
                                      <td width="50%">&nbsp;</td>
                                    </tr>
                                  </table>
                              </div></td>
                            </tr>
                            <tr>
                              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                        <tr>
                                          <td height="25" colspan="6" class="BGAccentDark"><strong>&nbsp;&nbsp;Project Information </strong></td>
                                        </tr>
                                        <tr>
                                          <td><label for="purpose">Project Title:</label></td>
                                          <td width="20"><?= $WARNING; ?></td>
                                          <td colspan="4"><input name="purpose" type="text" id="purpose" value="<?= stripslashes($_SESSION['purpose']); ?>" size="75" maxlength="100"></td>
                                        </tr>
                                        <tr>
                                          <td>Company Origination:</td>
                                          <td><?= $WARNING; ?></td>
                                          <td><span class="error">
                                            <select name="company" id="company">
                                              <option value="">Select One</option>
                                              <?php
										  $company_sth = $dbh->execute($company_sql);
										  while($company_sth->fetchInto($COMPANY)) {
										    $selected = ($_SESSION['company'] == $COMPANY[id]) ? selected : $blank;
											print "<option value=\"".$COMPANY[id]."\" ".$selected.">".$COMPANY[name]."</option>\n";
										  }
										  ?>
                                            </select>
                                          </span></td>
                                          <td>Project Start Date:</td>
                                          <td width="20"><?= $WARNING; ?></td>
                                          <td><input name="date1" type="text" id="date1" value="<?= $_SESSION['date1']; ?>" size="10" maxlength="10" readonly>
											&nbsp;<a href="javascript:show_calendar('Form.date1')" <?php help('', 'Select a start date', 'default'); ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                        </tr>
                                        <tr>
                                          <td>Company Location:</td>
                                          <td><?= $WARNING; ?></td>
                                          <td><span class="error">
                                            <select name="location">
                                              <option value="0">Select One</option>
                                              <option value="100">** All Plants **</option>
                                              <?php
										  $plants_sth = $dbh->execute($plants_sql);
										  while($plants_sth->fetchInto($PLANTS)) {
										    $selected = ($_SESSION['location'] == $PLANTS[id]) ? selected : $blank;
											print "<option value=\"".$PLANTS[id]."\" ".$selected.">".$PLANTS[name]."</option>\n";
										  }
										  ?>
                                            </select>
                                            </span></td>
                                          <td>Project Completion Date:</td>
                                          <td><?= $WARNING; ?></td>
                                          <td><input name="date2" type="text" id="date2" value="<?= $_SESSION['date2']; ?>" size="10" maxlength="10" readonly>
											&nbsp;<a href="javascript:show_calendar('Form.date2')" <?php help('', 'Select a completion date', 'default'); ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                        </tr>
                                    </table></td>
                                  </tr>
								  <tr><td>&nbsp;</td></tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                            <tr>
                                              <td height="25" colspan="2" class="BGAccentDark"><strong>&nbsp;&nbsp;Project Classification&nbsp;<?= $WARNING; ?></strong></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass1" type="radio" value="1" <?php if ($_SESSION['projClass'] == "1") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass1">Expansion / New Product Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass2" type="radio" value="2" <?php if ($_SESSION['projClass'] == '2') { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass2">Cost Reduction Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass3" type="radio" value="3" <?php if ($_SESSION['projClass'] == "3") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass3">Replacement / Profit Maintaining Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass4" type="radio" value="4" <?php if ($_SESSION['projClass'] == "4") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass4">OSHA / Environmental / Safety Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass5" type="radio" value="5" <?php if ($_SESSION['projClass'] == "5") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass5">Other Projects</label></td>
                                            </tr>
                                          </table></td>
                                        <td width="15" valign="top">&nbsp;</td>
                                          <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                            <tr>
                                              <td height="25" colspan="2" class="BGAccentDark"><strong>&nbsp;&nbsp;Budget Status </strong></td>
                                            </tr>
                                            <tr>
                                              <td>Capital Budget:</td>
                                              <td>
                                                <select name="capBudget" id="capBudget">
                                                  <option value="yes" <?php if ($_SESSION['capBudget'] == 'yes') { echo "selected"; } ?>>Yes</option>
                                                  <option value="no" <?php if ($_SESSION['capBudget'] == 'no') { echo "selected"; } ?>>No</option>
                                                </select></td>
                                            </tr>
                                            <tr>
                                              <td><label for="amtBudget">Amount Budgeted:</label></td>
                                              <td>$
                                                  <input name="amtBudget" type="text" id="amtBudget" value="<?= $_SESSION['amtBudget']; ?>" size="15" maxlength="15" onFocus="this.select()"></td>
                                            </tr>
                                            <tr>
                                              <td><label for="budgetTrans">Budget Transfer:</label></td>
                                              <td>$
                                                  <input name="budgetTrans" type="text" id="budgetTrans" value="<?= $_SESSION['budgetTrans']; ?>" size="15" maxlength="15" onFocus="this.select()"></td>
                                            </tr>
                                          </table></td>
                                        </tr>
                                        <tr>
                                          <td valign="top">&nbsp;</td>
                                        <td valign="top">&nbsp;</td>
                                          <td valign="top">&nbsp;</td>
                                        </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                        <tr>
                                          <td height="25" class="BGAccentDark" valign="top"><div align="center"><strong>Summary of Project Amounts</strong></div></td>
                                        </tr>
                                        <tr>
                                          <td valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                              <td valign="top" class="BGAccentMediumBorder"><table width="100%"  border="0">
                                                <tr>
                                                  <td height="25" class="BGAccentMedium"><strong>&nbsp;Project Classification</strong></td>
                                                </tr>
                                                <tr>
                                                  <td valign="top"><table width="100%"  border="0" cellspacing="0">
                                                      <tr>
                                                        <td><label for="assetCost">Asset Cost:</label></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="assetCost" type="text" id="assetCost" value="<?= (isset($_SESSION['assetCost'])) ? $_SESSION['assetCost'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="accCost">Accessories Cost:</label></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="accCost" type="text" id="accCost" value="<?= (isset($_SESSION['accCost'])) ? $_SESSION['accCost'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="frtInstall">Frt &amp; Installation:</label></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="frtInstall" type="text" id="frtInstall" value="<?= (isset($_SESSION['frtInstall'])) ? $_SESSION['frtInstall'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="otherCost">Other:</label></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="otherCost" type="text" id="otherCost" value="<?= (isset($_SESSION['otherCost'])) ? $_SESSION['otherCost'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Total:</td>
                                                        <td>$
                                                            <input name="totalCost" type="text" id="totalCost" value="<?= (isset($_SESSION['totalCost'])) ? $_SESSION['totalCost'] : 0; ?>" size="12" maxlength="15" readonly></td>
                                                      </tr>
                                                  </table></td>
                                                </tr>
                                              </table></td>
                                              <td width="5">&nbsp;</td>
                                              <td valign="top" class="BGAccentMediumBorder"><table width="100%"  border="0">
                                                <tr>
                                                  <td height="25" class="BGAccentMedium"><strong>&nbsp;Investment Performance</strong></td>
                                                </tr>
                                                <tr>
                                                  <td valign="top"><table width="100%"  border="0">
                                                      <tr>
                                                        <td><label for="netValue">Net Present Value:</label></td>
                                                        <td>$
                                                            <input name="netValue" type="text" id="netValue" value="<?= $_SESSION['netValue']; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="rateOfReturn">Internal Rate of Return:</label></td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="rateOfReturn" type="text" id="rateOfReturn" value="<?= $_SESSION['rateOfReturn']; ?>" size="12" maxlength="15" onFocus="this.select()">
              % </td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="netAsset">Return on Net Assets:</label></td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="netAsset" type="text" id="netAsset" value="<?= $_SESSION['netAsset']; ?>" size="12" maxlength="15" onFocus="this.select()">
              % </td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="payback">Payback (Yrs):</label></td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="payback" type="text" id="payback" value="<?= $_SESSION['payback']; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><label for="assetLife">Asset Life:</label></td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="assetLife" type="text" id="assetLife" value="<?= $_SESSION['assetLife']; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                  </table></td>
                                                </tr>
                                              </table></td>
                                              <td width="5">&nbsp;</td>
                                              <td valign="top" class="BGAccentMediumBorder"><table width="100%"  border="0">
                                                <tr>
                                                  <td height="25" class="BGAccentMedium"><strong>&nbsp;Timing/Expenditure</strong></td>
                                                </tr>
                                                <tr>
                                                  <td valign="top"><table width="100%"  border="0">
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55"><label for="firstExp">1st Qtr</label></td>
                                                              <td><select name="firstYr" id="firstYr">
                                                                  <option value="2004">2004</option>
                                                                  <option value="2005" selected>2005</option>
                                                                  <option value="2006">2006</option>
                                                                  <option value="2007">2007</option>
                                                                  <option value="2008">2008</option>
                                                                  <option value="2009">2009</option>
                                                                  <option value="2010">2010</option>
                                                                  <option value="2011">2011</option>
                                                                  <option value="2012">2012</option>
                                                                  <option value="2013">2013</option>
                                                                  <option value="2014">2014</option>
                                                                  <option value="2015">2015</option>
                                                                  <option value="2016">2016</option>
                                                                  <option value="2017">2017</option>
                                                                  <option value="2018">2018</option>
                                                                  <option value="2019">2019</option>
                                                                  <option value="2020">2020</option>
                                                              </select></td>
                                                            </tr>
                                                        </table></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="firstExp" type="text" id="firstExp" value="<?= (isset($_SESSION['firstExp'])) ? $_SESSION['firstExp'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55"><label for="secExp">2nd Qtr</label></td>
                                                              <td><select name="secYr" id="secYr">
                                                                  <option value="2004">2004</option>
                                                                  <option value="2005" selected>2005</option>
                                                                  <option value="2006">2006</option>
                                                                  <option value="2007">2007</option>
                                                                  <option value="2008">2008</option>
                                                                  <option value="2009">2009</option>
                                                                  <option value="2010">2010</option>
                                                                  <option value="2011">2011</option>
                                                                  <option value="2012">2012</option>
                                                                  <option value="2013">2013</option>
                                                                  <option value="2014">2014</option>
                                                                  <option value="2015">2015</option>
                                                                  <option value="2016">2016</option>
                                                                  <option value="2017">2017</option>
                                                                  <option value="2018">2018</option>
                                                                  <option value="2019">2019</option>
                                                                  <option value="2020">2020</option>
                                                              </select></td>
                                                            </tr>
                                                        </table></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="secExp" type="text" id="secExp" value="<?= (isset($_SESSION['secExp'])) ? $_SESSION['secExp'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55"><label for="thirdExp">3rd Qtr</label></td>
                                                              <td><select name="thirdYr" id="thirdYr">
                                                                  <option value="2004">2004</option>
                                                                  <option value="2005" selected>2005</option>
                                                                  <option value="2006">2006</option>
                                                                  <option value="2007">2007</option>
                                                                  <option value="2008">2008</option>
                                                                  <option value="2009">2009</option>
                                                                  <option value="2010">2010</option>
                                                                  <option value="2011">2011</option>
                                                                  <option value="2012">2012</option>
                                                                  <option value="2013">2013</option>
                                                                  <option value="2014">2014</option>
                                                                  <option value="2015">2015</option>
                                                                  <option value="2016">2016</option>
                                                                  <option value="2017">2017</option>
                                                                  <option value="2018">2018</option>
                                                                  <option value="2019">2019</option>
                                                                  <option value="2020">2020</option>
                                                              </select></td>
                                                            </tr>
                                                        </table></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="thirdExp" type="text" id="thirdExp" value="<?= (isset($_SESSION['thirdExp'])) ? $_SESSION['thirdExp'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55"><label for="forthExp">4th Qtr</label></td>
                                                              <td><select name="forthYr" id="forthYr">
                                                                  <option value="2004">2004</option>
                                                                  <option value="2005" selected>2005</option>
                                                                  <option value="2006">2006</option>
                                                                  <option value="2007">2007</option>
                                                                  <option value="2008">2008</option>
                                                                  <option value="2009">2009</option>
                                                                  <option value="2010">2010</option>
                                                                  <option value="2011">2011</option>
                                                                  <option value="2012">2012</option>
                                                                  <option value="2013">2013</option>
                                                                  <option value="2014">2014</option>
                                                                  <option value="2015">2015</option>
                                                                  <option value="2016">2016</option>
                                                                  <option value="2017">2017</option>
                                                                  <option value="2018">2018</option>
                                                                  <option value="2019">2019</option>
                                                                  <option value="2020">2020</option>
                                                              </select></td>
                                                            </tr>
                                                        </table></td>
                                                        <td>$
                                                            <input onBlur="Calculate();" name="forthExp" type="text" id="forthExp" value="<?= (isset($_SESSION['forthExp'])) ? $_SESSION['forthExp'] : 0; ?>" size="12" maxlength="15" onFocus="this.select()"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Total:</td>
                                                        <td>$
                                                            <input name="totalExp" type="text" id="totalExp" value="<?= (isset($_SESSION['totalExp'])) ? $_SESSION['totalExp'] : 0; ?>" size="12" maxlength="15" readonly></td>
                                                      </tr>
                                                  </table></td>
                                                </tr>
                                              </table></td>
                                            </tr>
                                          </table></td>
                                        </tr>
                                    </table></td>
                                  </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td height="5" class="GlobalButtonTextDisabled"><img src="../images/spacer.gif" width="15" height="5"></td>
                            </tr>
                            <tr>
                              <td>
                                <div align="right">
                                  <input name="stage" type="hidden" id="stage" value="one">
                                  <input name="next" type="image" id="next" class="button" src="../images/button.php?i=b70.png&l=Next" border="0">
&nbsp;&nbsp; </div></td>
                            </tr>
                          </table>
                    </form>
<script type="text/javascript">
	var frmvalidator = new Validator("Form");
	frmvalidator.addValidation("purpose","req","Please enter the purpose");
	frmvalidator.addValidation("date1","req","Please enter the Start Date");
	frmvalidator.addValidation("date2","req","Please enter the End Date");
	frmvalidator.addValidation("projClass","selone","Please enter the Project Class");
	
	frmvalidator.addValidation("location","dontselect=0");
	frmvalidator.addValidation("req","dontselect=0");
	frmvalidator.addValidation("company","dontselect=0");
</script>

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
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- #BeginLibraryItem "/Library/versioncer.lbi" --><script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tbody>
    <tr>
      <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">v0.9</a></td>
      <td width="20" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" <?php help('', 'Release Notes', 'default'); ?>><img src="../images/notes.gif" alt="Release Notes" width="12" height="15" border="0" align="absmiddle"></a></div></td>
	  <?php if ($default['rss'] == 'on') { ?>
	  <td width="25" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/RSS/overview.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" <?php help('', 'Really Simple Syndication (RSS)', 'default'); ?>><img src="../images/livemarks16.gif" width="16" height="16" border="0"></a></div></td>
	  <?php } ?>
   </tr>
  </tbody>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></div></td>
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