<?php
/**
 * Request System
 *
 * index.php is Login page.
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


if ($_POST['stage'] == "three") { 
	/* ------------------ START FILE UPLOAD (FIRST HALF) ----------------------- */
	$exp_file = explode(".",$_FILES['file']['name']);
	$file_ext = end($exp_file);
	/* ------------------ END FILE UPLOAD (FIRST HALF) ----------------------- */	
	
	/* ------------------ START DATABASE CONNECTIONS ----------------------- */
	/* Commiting data into CER database */
	$cer_sql = "INSERT into CER values ( NULL,'".
										addslashes($_SESSION['purpose'])."',
										NULL,
										'',
										CURDATE(),'".
										$_SESSION['date1']."','".
										$_SESSION['date2']."','".
										$_SESSION['eid']."','".
										$_SESSION['location']."','".
										$_SESSION['company']."','".
										$_SESSION['projClass']."','".
										$_SESSION['capBudget']."','".
										$_SESSION['amtBudget']."','".
										$_SESSION['budgetTrans']."','".
										htmlentities($_POST['summary'], ENT_QUOTES, 'UTF-8')."','".
										htmlentities($_FILES['file']['name'], ENT_QUOTES, 'UTF-8')."','".
										htmlentities($_FILES['file']['type'], ENT_QUOTES, 'UTF-8')."','".
										$file_ext."','".
										$_FILES['file']['size']."','".
										$_SESSION['assetCost']."','".
										$_SESSION['accCost']."','".
										$_SESSION['frtInstall']."','".
										$_SESSION['otherCost']."','".
										$_SESSION['totalCost']."','".
										$_SESSION['netValue']."','".
										$_SESSION['rateOfReturn']."','".
										$_SESSION['netAsset']."','".
										$_SESSION['payback']."','".
										$_SESSION['assetLife']."','".
										$_SESSION['firstYr']."','".
										$_SESSION['secYr']."','".
										$_SESSION['thirdYr']."','".
										$_SESSION['forthYr']."','".
										$_SESSION['totalExp']."','".
										$_SESSION['firstExp']."','".
										$_SESSION['secExp']."','".
										$_SESSION['thirdExp']."','".
										$_SESSION['forthExp']."',
										'N'
										)";
	if ($debug_page) {
		echo "CER: " . $cer_sql . "<br><br>";
	} else {
		$dbh->query($cer_sql);
	}									
	
	/* Get CER auto_increment ID */							
	$CER_ID = $dbh->getOne("select max(id) from CER");
	
	/* Commiting data into Authorization database */
	$auth_sql = "INSERT into Authorization (id,type,type_id,issuer,app1,app2,app3,app4,app5,app6,app7,app8,app9,app10,app11) VALUES ( NULL,'CER','".
										$CER_ID."','".
										$_SESSION['issuer']."','".
										$_SESSION['app1']."','".
										$_SESSION['app2']."','".
										$_SESSION['app3']."','".
										$_SESSION['app4']."','".
										$_SESSION['app5']."','".
										$_SESSION['app6']."','".
										$_SESSION['app7']."','".
										$_SESSION['app8']."','".
										$_SESSION['app9']."','".
										$_SESSION['app10']."','".
										$_SESSION['app11'].									
										"')";	
	if ($debug_page) {
		echo "AUTH: " . $auth_sql . "<br><br>";
	} else {
		$dbh->query($auth_sql);	
	}																															
	/* ------------------ END DATABASE CONNECTIONS ----------------------- */
	
	
	/* ------------------ START FILE UPLOAD (SECOND HALF) ----------------------- */
	$store = $default['files_store'];								//Store uploaded files to this directory
	$dest = $store."/".$CER_ID.".".$file_ext;
	$source = $_FILES['file']['tmp_name'];
	if (file_exists($source)) {
		if (is_writable($default['CER_UPLOAD'])) {
			copy($source, $dest);									//Copy temp upload to $store
		} else {
			$_SESSION['error'] = "Cannot upload file (".$_FILES['file']['name'].")";
			$_SESSION['redirect'] = "http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI'];
			
			header("Location: ../error.php");
		}
	}
	/* ------------------ END FILE UPLOAD (SECOND HALF) ----------------------- */		

	clearSession();			// Reset Session
	
	/* ----- Forward to router ----- */
	if (!$debug_page) {
		header("Location: router.php?type_id=".$CER_ID."&approval=app0");
	}
	exit();
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
	<script type="text/javascript">function sf(){ document.Form.summary.focus(); }</script>
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
  <!-- InstanceBeginEditable name="head" -->    <!-- InstanceEndEditable -->
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
                          <td valign="bottom"><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                          <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                          <td valign="bottom"><a href="authorization.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                          <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                          <td valign="bottom"><img src="../images/vnCurrent.gif" width="36" height="36"></td>
                          <td valign="bottom"><img src="../images/vnFutureLine.gif" width="108" height="18"></td>
                          <td><img src="../images/vnFuture.gif" width="36" height="36"></td>
                          <td width="15">&nbsp;</td>
                        </tr>
                        <tr>
                          <td colspan="9"><table width="100%"  border="0">
                              <tr>
                                <td width="21%" class="wizardPast"><div align="left">&nbsp;&nbsp;Information</div></td><td width="27%" class="wizardPast"><div align="center">&nbsp;Authorization</div></td><td width="36%" class="wizardCurrent"><div align="center">Justification&nbsp;&nbsp;&nbsp;</div></td><td width="16%" class="wizardFuture"><div align="center">&nbsp;&nbsp;&nbsp;Finished</div></td>
                              </tr>
                          </table></td>
                        </tr>
                      </table>
				    </div>
                      <br>
                      <br>
                      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="Form" id="Form">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td class="BGAccentVeryDark"><div align="left">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td width="69%" height="30" class=
                                  "DarkHeaderSubSub">&nbsp;&nbsp;Capital Expenditure Request... </td>
                                    <td width="31%"><div align="left"></div></td>
                                  </tr>
                              </table>
                            </div></td>
                          </tr>
                          <tr>
                            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Summary of Project Description and Justifications&nbsp;<?= $WARNING; ?></strong></td>
                                    </tr>
                                    <tr>
                                      <td><textarea name="summary" cols="100" rows="10" id="summary" onFocus="this.select()"></textarea>
                                      <div align="center"></div></td>
                                    </tr>
                                  </table></td>
                                </tr>
                            </table>
                              <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td>&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>Attach File:
                                  <input name="file" type="file" size="50"></td>
                                </tr>
                                <tr>
                                  <td height="5">&nbsp;</td>
                                </tr>
                              </table></td>
                          </tr>
                          <tr>
                            <td height="5"><img src="../images/spacer.gif" width="15" height="5"></td>
                          </tr>
                          <tr>
                            <td>
                              <div align="right">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td>&nbsp;&nbsp;<a href="#"><img src="../images/button.php?i=b70.png&l=Back" border="0" onClick="history.back()"></a></td>
                                    <td><div align="right">
                                      <input name="stage" type="hidden" id="stage" value="three">
                                      <input name="next" type="image" id="next" src="../images/button.php?i=b70.png&l=Done" border="0" class="button">
&nbsp;&nbsp;</div></td>
                                  </tr>
                                </table>
                               </div></td>
                          </tr>
                        </table>
                    </form>
<script type="text/javascript">
	var frmvalidator = new Validator("Form");
	frmvalidator.addValidation("summary","req","Please enter a summary");
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