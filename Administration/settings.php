<?php
/**
 * Request System
 *
 * settings.php display, add and edit system wide variables.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @package Administration
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
require_once('../security/check_access.php'); 

/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ----- START ADD VARIABLE ----- */
if ($_POST['action'] == "add") {
	$dbh->query("INSERT into Settings VALUES(NULL, '".$_POST['company']."','".$_POST['variable']."','".$_POST['value']."', description='".$_POST['description']."')");
}
/* ----- END ADD VARIABLE ----- */

/* ----- START UPDATE VARIABLE ----- */
if ($_POST['action'] == "update") {
	if ($_POST['delete'] == "yes") {
		$dbh->query("DELETE from Settings WHERE id=".$_POST['id']."");
	} else {
		$dbh->query("UPDATE Settings
					 SET company='".$_POST['company']."', variable='".$_POST['variable']."', value='".$_POST['value']."', description='".$_POST['description']."'
					 WHERE id=".$_POST['id']."");
	}
}
/* ----- END UPDATE VARIABLE ----- */

$COMPANY = $dbh->getAssoc("SELECT id, name 
						   FROM Standards.Companies 
						   WHERE status <> '1'
						   ORDER BY id");
$settings_sq1 = $dbh->prepare("SELECT * FROM Settings WHERE company = ? ORDER BY variable");
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
    <style type="text/css">
<!--
.mainsection {	font-size: 9pt;
	font-weight: bold;
	padding-left: 10px;
}
.subsection { 	font-size: 9pt; 
 	 font-weight: normal;
 		margin-left: 15px;
}
-->
    </style>
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_admin.lbi" --><?php if ($_SESSION['request_access'] == 0) { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td><img src="/Common/images/spacer.gif" width="200" height="5" border="0"></td>
    </tr>
</table>
<?php } else { ?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tr>
	<td>&nbsp;</td>
	<td><table cellspacing="0" cellpadding="0" summary="" border="0">
		<tr>
		  <td nowrap><a href="users.php" class="off"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="settings.php" class="off"> Settings </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>		  
		  <td nowrap><a href="db/index.php" class="off"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="utilities.php" class="off"> Utilities </a></td>			  			  
		  <td nowrap>&nbsp;</td>
		</tr>
	</table></td>
	<td>&nbsp;</td>
  </tr>
</table>
<?php } ?>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
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
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="200" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table cellspacing="0" cellpadding="0" width="200" align="left"
summary="" border="0">
              <tbody>
                <tr>
                  <td valign="top" width="13" background="../images/asyltlb.gif"><img height="20" alt="" src="../images/t.gif" width="13" border=
	  "0"></td>
                  <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../images/asybase.gif" width="145"
		border="0"> <br>
                      <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                        <tr>
                          <td class="mainsection"><a href="#add" class="dark">Add New Variable </a></td>
                        </tr>
                    </table></td>
                  <td valign="top" width="22" background="../images/asyltrb.gif"><img height="20" alt="" src="../images/t.gif" width="22" border="0"></td>
                </tr>
                <tr>
                  <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../images/asyltb.gif" width="200" border="0"></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><table width="190"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                      <td align="center"><span class="ColorHeaderSubSub">Administration</span> </td>
                      <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td class="BGAccentVeryDarkBorder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><a href="<?= $_SERVER['PHP_SELF']; ?>" class="dark">Update Configuration</a></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                      <td><img src="../images/spacer.gif" width="10" height="10"></td>
                      <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                    </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
        <td valign="top"><br>
          <table  border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><?php 
					for ($i = 0; $i <= count($COMPANY) - 1; $i++) {
				  ?>
                  <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td height="30" colspan="2" class="BGAccentVeryDark">&nbsp;<b><?= $COMPANY[$i]; ?> </b></td>
                    </tr>
                    <tr>
                      <td colspan="2" class="BGAccentVeryDarkBorder">
					    <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
						    <tr>
							  <td class="xpHeaderTop" width="20">&nbsp;</td>
							  <td class="xpHeaderTopActive">Variable</td>
							  <td class="xpHeaderTop">Value</td>
							  <td class="xpHeaderTop">Description</td>
							  <td class="xpHeaderTop">&nbsp;</td>
							</tr>						
                          <?php
							$settings_sth = $dbh->execute($settings_sq1, array($i));
							while($settings_sth->fetchInto($SETTINGS)) {
						  ?>
						  <form name="Form<?= $SETTINGS[id]; ?>" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                            <tr>
                              <td height="28"><div align="center"><a href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $SETTINGS['id']; ?>&delete=yes" <?php help('', 'Delete this Variable','default'); ?>><img src="/go/Request/images/nochange2.png" width="17" height="17" border="0"></a></div></td>
                              <td><input name="variable" type="text" value="<?= $SETTINGS['variable']; ?>" size="20" maxlength="50"></td>
                              <td>&nbsp;
                              <input name="value" type="text" value="<?= $SETTINGS['value']; ?>" size="50" maxlength="100"></td>
                              <td>&nbsp;
                                <input name="description" type="text" id="description" value="<?= $SETTINGS['description']; ?>" size="50" maxlength="100">
                                  &nbsp;
                                  <input type="hidden" name="id" value="<?= $SETTINGS['id']; ?>">
                                  <input type="hidden" name="company" value="<?= $i; ?>">
                              <input type="hidden" name="action" value="update"></td>
                              <td valign="middle"><input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Update" border="0"></td>
							</tr>
						  </form>
                          <?php } ?>
                      </table>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <?php } ?>
              </td>
            </tr>
          </table>
          <br>
          <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="formAdd" id="formAdd">
            <a name="add"></a>
            <table width="190"  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
                      <td align="center"><span class="ColorHeaderSubSub">Add New Variable </span> </td>
                      <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25"><strong>Company:&nbsp;</strong></td>
                    <td><select name="select" id="select">
                        <?php 
							for ($i = 0; $i <= count($COMPANY) - 1; $i++) {
						  ?>
                        <option value="<?= $i; ?>">
                          <?= $COMPANY[$i]; ?>
                      </option>
                        <?php } ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="25"><strong>Variable:</strong></td>
                    <td><input name="variable2" type="text" id="variable2" maxlength="50"></td>
                  </tr>
                  <tr>
                    <td height="25"><strong>Value:</strong></td>
                    <td><input name="value2" type="text" id="value2" size="50" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="25"><strong>Descrption:</strong></td>
                    <td><input name="description2" type="text" id="description2" size="50" maxlength="150"></td>
                  </tr>
                  <tr>
                    <td height="25">&nbsp;</td>
                    <td>
                      <input name="action2" type="hidden" id="action2" value="add">
                    <input name="imageField2" type="image" src="../images/button.php?i=b70.png&l=Add" border="0"></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_left.gif" width="10" height="10"></td>
                      <td><img src="../images/spacer.gif" width="10" height="10"></td>
                      <td width="10" height="10" valign="bottom"><img src="../images/menu_bottom_right.gif" width="10" height="10"></td>
                    </tr>
                </table></td>
              </tr>
            </table>
          </form></td>
      </tr>
    </table>
	<br>
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
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- #BeginLibraryItem "/Library/versionadmin.lbi" --><script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<table cellspacing="0" cellpadding="0" summary="" border="0">
  <tbody>
    <tr>
      <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">v1.0</a></td>
      <td width="20" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../images/notes.gif" alt="Release Notes" width="12" height="15" border="0" align="absmiddle"></a></div></td>
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