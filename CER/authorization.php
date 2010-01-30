<?php
/**
 * Request System
 *
 * authorization.php who will approve CER.
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
 * - Check User Access
 */
require_once('../security/check_user.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 

/* ------------------ START PROCESSING DATA ----------------------- */
if ($_POST['stage'] == "two") {
	/* Set form variables as session variables */
	$_SESSION['issuer'] = $_POST['issuer'];
	$_SESSION['app1'] = $_POST['app1'];
	$_SESSION['app2'] = $_POST['app2'];
	$_SESSION['app3'] = $_POST['app3'];
	$_SESSION['app4'] = $_POST['app4'];
	$_SESSION['app5'] = $_POST['app5'];
	$_SESSION['app6'] = $_POST['app6'];
	$_SESSION['app7'] = $_POST['app7'];
	$_SESSION['app8'] = $_POST['app8'];
	$_SESSION['app9'] = $_POST['app9'];
	$_SESSION['app10'] = $_POST['app10'];
	$_SESSION['app11']  = $_POST['app11'];
	
	header("Location: justification.php"); 
}
/* ------------------ END PROCESSING DATA ----------------------- */

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
$issuer_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
							   "FROM Users U, Standards.Employees E ".
							   "WHERE U.eid = E.eid and U.issuer = '1' and U.status = '0' and E.status = '0' ".
							   "ORDER BY E.lst ASC");
$app_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
							"FROM Users U, Standards.Employees E ".
							"WHERE U.eid = E.eid and U.cer = ? and U.status = '0' and E.status = '0' ".
							"ORDER BY E.lst ASC");				
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

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
				  	 <div id="noPrint">                    
				  	   <table  border="0" align="center" cellpadding="0" cellspacing="0">
                         <tr>
                           <td width="15">&nbsp;</td>
                           <td valign="bottom"><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                           <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                           <td valign="bottom"><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                           <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                           <td valign="bottom"><img src="../images/vnFuture.gif" width="36" height="36"></td>
                           <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                           <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                           <td width="15">&nbsp;</td>
                         </tr>
                         <tr>
                           <td colspan="9"><table width="100%"  border="0">
                               <tr>
                                 <td width="21%" class="wizardPast"><div align="left">&nbsp;&nbsp;Information</div></td>
                                 <td width="27%" class="wizardCurrent"><div align="center">&nbsp;Authorization</div></td>
                                 <td width="36%" class="wizardFuture"><div align="center">Justification&nbsp;&nbsp;&nbsp;</div></td>
                                 <td width="16%" class="wizardFuture"><div align="center">&nbsp;&nbsp;&nbsp;Finished</div></td>
                               </tr>
                           </table></td>
                         </tr>
                       </table>
				  	 </div>
                      <br>
                      <br>
                      <form name="Form" method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="BGAccentVeryDark"><div align="left">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td width="50%" height="30" class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;Authorization...</td>
                                    <td width="50%"><div align="right"> </div></td>
                                  </tr>
                                </table>
                            </div></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">				
                                <tr>
                                  <td>Plant Manager: </td>
                                  <td width="20"><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app1" id="app1">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('1'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app1'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                              </tr>
                                <tr class="BGAccentLight">
                                  <td>Plant Controller: </td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app2" id="app2">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('2'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app2'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                                </tr>
                                <tr>
                                  <td>Plant Engineer: </td>
                                  <td>&nbsp;</td>
                                  <td><select name="app3" id="app3">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('3'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app3'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                              </tr>
                                <tr class="BGAccentLight">
                                  <td>Vice Presendent - Purchasing: </td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app4" id="app4">
                                    <option value="0">Select One</option>
                                    <?php
								  $app_sth = $dbh->execute($app_sql,array('4'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app4'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
                                  </select></td>
                                </tr>
                                <tr>
                                  <td>Controller  Operations: </td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app5" id="app5">
                                    <option value="0">Select One</option>
                                    <?php
								  $app_sth = $dbh->execute($app_sql,array('5'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app5'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
                                  </select></td>
                                </tr>
                                <tr class="BGAccentLight">
                                  <td>Corporate Controller: </td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app6" id="app6">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('6'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app6'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                                </tr>
                                <tr>
                                  <td>Chief Operating Officer: </td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app7" id="app7">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('7'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app7'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                              </tr>
                                <tr class="BGAccentLight">
                                  <td>Vice President - Finance:</td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app8" id="app8">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('8'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app8'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                                </tr>
                                <tr>
                                  <td>Chief Financial Officer: </td>
                                  <td><?php if ($_SESSION['totalCost'] >= 5000) { echo $WARNING; } ?></td>
                                  <td><select name="app9" id="app9">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('9'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app9'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                              </tr>
                                <tr class="BGAccentLight">
                                  <td>Chief Executive Officer:</td>
                                  <td><?php if ($_SESSION['totalCost'] >= 100000) { echo $WARNING; } ?></td>
                                  <td><select name="app10" id="app10">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('10'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app10'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                                </tr>
                                <tr>
                                  <td>Chairman of the Board:</td>
                                  <td><?php if ($_SESSION['totalCost'] >= 500000) { echo $WARNING; } ?></td>
                                  <td><select name="app11" id="app11">
								      <option value="0">Select One</option>
								  <?php
								  $app_sth = $dbh->execute($app_sql,array('11'));
								  while($app_sth->fetchInto($APP)) {
								    $selected = ($_SESSION['app11'] == $APP[eid]) ? selected : $blank;
								    print "<option value=\"".$APP[eid]."\" ".$selected.">".ucwords(strtolower($APP[lst].", ".$APP[fst]))."</option>";
								  }
								  ?>
								  </select></td>
                              </tr>
                                <tr>
                                  <td>CER Issuer:</td>
                                  <td><?= $WARNING; ?></td>
                                  <td><select name="issuer">
								      <option value="0">Select One</option>
								  <?php
								  
								  $issuer_sth = $dbh->execute($issuer_sql);
								  while($issuer_sth->fetchInto($ISSUER)) {
								    $selected = ($_SESSION['issuer'] == $ISSUER[eid]) ? selected : $blank;
								    print "<option value=\"".$ISSUER[eid]."\" ".$selected.">".ucwords(strtolower($ISSUER[lst].", ".$ISSUER[fst]))."</option>";
								  }
								  
								  ?>
								  </select></td>
                                </tr>										  
                            </table></td>
                          </tr>
                          <tr>
                            <td height="5"><img src="../images/spacer.gif" width="15" height="5">
                            </td>
                          </tr>
                          <tr>
                            <td>
                                <div align="right">
                                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td>&nbsp;&nbsp;<a href="index.php"><img src="../images/button.php?i=b70.png&l=Back" border="0"></a></td>
                                      <td><div align="right">
                                        <input name="stage" type="hidden" id="stage" value="two">
                                        <input name="next" type="image" id="next" src="../images/button.php?i=b70.png&l=Next" class="button" border="0">
&nbsp;&nbsp;</div></td>
                                    </tr>
                                  </table>
                              </div></td>
                          </tr>
                        </table>
                    </form>
<script type="text/javascript">
	var frmvalidator = new Validator("Form");
	frmvalidator.addValidation("issuer","dontselect=0");
	<?php if ($_SESSION['totalCost'] >= 5000) { ?>
<!--	frmvalidator.addValidation("app1","dontselect=0"); -->
<!--	frmvalidator.addValidation("app2","dontselect=0"); -->
<!--	frmvalidator.addValidation("app4","dontselect=0"); -->
<!--	frmvalidator.addValidation("app5","dontselect=0"); -->
<!--	frmvalidator.addValidation("app6","dontselect=0"); -->
<!--	frmvalidator.addValidation("app7","dontselect=0"); -->
<!--	frmvalidator.addValidation("app8","dontselect=0"); -->
<!--	frmvalidator.addValidation("app9","dontselect=0");  -->
	<?php } ?>
	<?php if ($_SESSION['totalCost'] >= 100000) { ?>
<!--	frmvalidator.addValidation("app10","dontselect=0"); -->
	<?php } ?>
	<?php if ($_SESSION['totalCost'] >= 500000) { ?>
<!--	frmvalidator.addValidation("app11","dontselect=0"); -->
	<?php } ?>
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
