<?php
/**
 * Request System
 *
 * information.php enduser enters information about PO.
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
if ($_POST['stage'] == 'two') {
	/* Set form variables as session variables */
	foreach ($_POST as $key => $value) {
		$_SESSION[$key]  = htmlentities($value, ENT_QUOTES);
	}

	/* Forward user to next page */
	header("Location: items_beta.php"); 
}
/* ------------- END SESSION VARIABLES --------------------- */


/* ------------- START DATABASE CONNECTIONS --------------------- */

$company_sql = $dbh->prepare("SELECT id, name 
						      FROM Standards.Companies 
						      WHERE id > 0 
							    AND status <> '1'
						      ORDER BY name");
$plants_sql = $dbh->prepare("SELECT id, name
						     FROM Standards.Plants
						     WHERE status = '0'
						     ORDER BY name");
$dept_sql = $dbh->prepare("SELECT id, name 
						   FROM Standards.Department 
						   WHERE status = '0' 
						   ORDER BY name");
$cer_sql = $dbh->prepare("SELECT id, cer 
                          FROM CER 
					      WHERE cer IS NOT NULL 
					      ORDER BY cer");			  													 				    
/* ------------- END DATABASE CONNECTIONS --------------------- */


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
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/calendarmws.js"></SCRIPT>
	
	<SCRIPT SRC="/Common/js/submitOnce.js"></SCRIPT>
	
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css">	
		
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">	
    <script type="text/JavaScript">
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><?php include('../include/menu/main_left.php'); ?><!-- InstanceEndEditable --></td>
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
    <!-- InstanceBeginEditable name="main" --><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
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
                        <td><a href="index_beta.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
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
                              <td width="25%"><div align="center" class="wizardCurrent">Information</div></td>
                              <td width="25%"><div align="center" class="wizardFuture">Items</div></td>
                              <td width="25%"><div align="center" class="wizardFuture">Authorization</div></td>
                              <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                            </tr>
                        </table></td>
                      </tr>
                    </table>
				  </div>
                    <br>
					<br>
                    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form" runat="vdaemon">
                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="BGAccentVeryDark"><div align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<strong><img src="../images/info.png" width="16" height="16" align="texttop"></strong>&nbsp;Information...</td>
                                        <td width="50%"><div align="left"> </div></td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td colspan="2" class="hotMessage"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="150" class="valNone">HOT Requisition:</td>
                                          <td><select name="hot" id="hot">
                                            <option value="no" selected>No</option>
                                            <option value="yes">Yes</option>
                                          </select></td>
                                        </tr>
                                      </table></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">Private Requisition: </td>
                                      <td><select name="select">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                      </select></td>
                                    </tr>
                                    <tr>
                                      <td height="5" colspan="2"><img src="/Common/images/spacer.gif" width="5" height="5"></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">On Behalf Of: </td>
                                      <td><input id="ajaxName" name="ajaxName" type="text" size="40" />
											<script type="text/javascript">
												Event.observe(window, "load", function() {
													var aa = new AutoAssist("ajaxName", function() {
														return "../Common/employees.php?q=" + this.txtBox.value;
													});
												});
											</script>
                                      <input name="incareof" type="hidden" id="ajaxEID"></td>
                                    </tr>
                                    <tr>
                                      <td height="5" colspan="2"><img src="/Common/images/spacer.gif" width="5" height="5"></td>
                                    </tr>
									<tr>
                                      <td><vllabel form="Form" validators="plant" class="valRequired2" errclass="valError">Bill To Plant:</vllabel></td>
                                      <td><span class="error">
										<select name="plant" id="plant">
											  <option value="0">Select One</option>
											  <?php
											  $ship_sth = $dbh->execute($plants_sql);
											  while($ship_sth->fetchInto($PLANT)) {
												$selected = ($_SESSION['ship'] == $PLANT[id]) ? selected : $blank;
												print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
											  }
											  ?>
										  </select>
                                        <vlvalidator name="plant" type="compare" control="plant" validtype="string" comparevalue="0" comparecontrol="plant" operator="ne">
                                      </span></td>
                                    </tr>									
                                    <tr>
                                      <td><vllabel form="Form" validators="ship" class="valRequired2" errclass="valError">Deliver To Plant:</vllabel></td>
                                      <td><span class="error">
										<select name="ship" id="ship">
											  <option value="0">Select One</option>
											  <?php
											  $ship_sth = $dbh->execute($plants_sql);
											  while($ship_sth->fetchInto($PLANT)) {
												$selected = ($_SESSION['ship'] == $PLANT[id]) ? selected : $blank;
												print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
											  }
											  ?>
										  </select>
                                        <vlvalidator name="ship" type="compare" control="ship" validtype="string" comparevalue="0" comparecontrol="ship" operator="ne">
                                      </span></td>
                                    </tr>									
                                    <tr>
                                      <td class="valNone">Job Number:&nbsp;</td>
                                      <td><input name="job" type="text" id="job" size="15" maxlength="15" value="<?= $_SESSION['job']; ?>"></td>
                                    </tr>
                                    <tr>
                                      <td><vllabel form="Form" validators="department" class="valRequired2" errclass="valError">Department:</vllabel></td>
                                      <td><span class="error">
									<select name="department" id="department">
                                          <option value="0">Select One</option>
                                          <?php
										  $dept_sth = $dbh->execute($dept_sql);
										  while($dept_sth->fetchInto($DEPT)) {
											$selected = ($_SESSION['department'] == $DEPT[id]) ? selected : $blank;
											print "<option value=\"".$DEPT[id]."\" ".$selected.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
										  }
										  ?>
                                      </select>
                                        <vlvalidator name="department" type="compare" control="department" validtype="string" comparevalue="0" comparecontrol="department" operator="ne">
                                      </span></td>
                                    </tr>
                                    <tr>
                                      <td height="5" colspan="2"><img src="/Common/images/spacer.gif" width="5" height="5"></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">Due Date:</td>
                                      <td><input name="date1" type="text" id="date1" size="10" maxlength="10" readonly="on" value="<?= $_SESSION['date1']; ?>">
                                        &nbsp;<a href="javascript:show_calendar('Form.date1')" <?php help('', 'Click to select a date from a calendar popup', 'default') ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                    </tr>
                                    <tr>
                                      <td nowrap><vllabel form="Form" validators="purpose" class="valRequired2" errclass="valError">Purpose / Usage:</vllabel></td>
                                      <td><input name="purpose" type="text" id="purpose" value="<?= $_SESSION['purpose']; ?>" size="75" maxlength="100">
                                      <vlvalidator name="purpose" type="required" control="purpose" minlength="10" maxlength="100"></td>
                                    </tr>
                                    
                                    <tr>
                                      <td height="5" colspan="2"><img src="/Common/images/spacer.gif" width="5" height="5"></td>
                                    </tr>
                                    <tr>
                                      <td class="valNone">Capital Acuisition:&nbsp;</td>
                                      <td><select name="cer">
                                          <option value="0">Select One</option>
                                          <?php
										  $cer_sth = $dbh->execute($cer_sql);
										  while($cer_sth->fetchInto($CER)) {
											if (isset($_SESSION['cer'])) {
											  $selected = ($_SESSION['cer'] == $CER['id']) ? selected : $blank;
											}
											print "<option value=\"".$CER['id']."\" ".$selected.">".ucwords(strtolower($CER['cer']))."</option>\n";
										  }
										  ?>
                                        </select>
                                      <a href="cer_list.php" title="Capital Acuisition List" onClick="return GB_showFullScreen(this.title, this.href)" <?php help('', 'Click here to get a list of approved Capital Acuisition Requests', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                                    </tr>
                                </table></td>
                              </tr>
                              <tr>
                                <td height="5"><img src="../images/spacer.gif" width="5" height="5"></td>
                              </tr>
                              <tr>
                                <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><a href="index_beta.php">&nbsp;<img src="../images/button.php?i=b70.png&l=Back" border="0"></a></td>
                                    <td><div align="right">
                                      <input name="stage" type="hidden" id="stage" value="two">
                                      <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Next" border="0">
                                      &nbsp;</div></td>
                                  </tr>
                                </table></td>
                              </tr>
                            </table>
                    </form>
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