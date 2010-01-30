<?php
/**
 * Request System
 *
 * authorization.php setup automatic printing to issuer.
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


/* ------------------ START PROCESSING DATA ----------------------- */
if ($_POST['stage'] == "three") {
	/* Set form variables as session variables */
	$_SESSION['app1'] = htmlentities($_POST['app1'], ENT_QUOTES, 'UTF-8');
	$_SESSION['app2'] = htmlentities($_POST['app2'], ENT_QUOTES, 'UTF-8');
	$_SESSION['app3'] = htmlentities($_POST['app3'], ENT_QUOTES, 'UTF-8');
	$_SESSION['app4'] = htmlentities($_POST['app4'], ENT_QUOTES, 'UTF-8');

	header("Location: information.php"); 
}
/* ------------------ END PROCESSING DATA ----------------------- */

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst, U.vacation
					       FROM Users U
						     INNER JOIN Standards.Employees E ON E.eid=U.eid
					       WHERE U.one = '1' AND U.status = '0' AND E.status = '0'
					       ORDER BY E.lst ASC");
$app2_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst, U.vacation
					       FROM Users U
						     INNER JOIN Standards.Employees E ON E.eid=U.eid
						   WHERE U.two = '1' AND U.status = '0' AND E.status = '0'
					       ORDER BY E.lst ASC");
$app3_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst, U.vacation
					       FROM Users U
						     INNER JOIN Standards.Employees E ON E.eid=U.eid
					       WHERE U.three = '1' AND U.status = '0' AND E.status = '0' 
					       ORDER BY E.lst ASC");
$app4_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst, U.vacation
					       FROM Users U
						     INNER JOIN Standards.Employees E ON E.eid=U.eid
					       WHERE U.four = '1' AND U.status = '0' AND E.status = '0' 
					       ORDER BY E.lst ASC");
$EMPLOYEE = $dbh->getAssoc("SELECT eid, CONCAT(lst,', ',fst) AS name FROM Standards.Employees");
/* ------------------ END DATABASE CONNECTIONS ----------------------- */



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
                          <td><a href="index.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
                          <td valign="bottom"><img src="../images/vnPastLine.gif" width="108" height="18"></td>
                          <td><a href="items.php"><img src="../images/vnPast.gif" width="36" height="36" border="0"></a></td>
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
                                <td width="25%" class="wizardFuture"><div align="center" class="wizardPast">Items</div></td>
                                <td width="25%" class="wizardFuture"><div align="center" class="wizardCurrent">Authorization</div></td>
                                <td width="25%" class="wizardFuture"><div align="center">Information</div></td>
                                <td width="13%" class="wizardFuture"><div align="right">Finished</div></td>
                              </tr>
                          </table></td>
                        </tr>
                      </table>
                    </div>
                      <br>
                      <br>
                      <form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" runat="vdaemon">
                        <table border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <td><table border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td class="BGAccentVeryDark"><div align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Authorization...</td>
                                        <td width="50%"><div align="right"> </div></td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="3" errmsg="Approver 3 is required for requests above $<?= $default['app3_min']; ?> and $<?= $default['app3_max']; ?>.">
                                    <tr>
                                      <td><vllabel form="form1" validators="app1" class="valRequired2" errclass="valError">Approver 1:</vllabel> </td>
                                      <td nowrap><select name="app1" id="app1">
                                          <option value="0">Select One</option>
                                          <?php
										  $app1_sth = $dbh->execute($app1_sql);
										  while($app1_sth->fetchInto($APP1)) {
											if (strlen($APP1['vacation']) == 5) {
												$vacation = " => ".caps($EMPLOYEE[$APP1['vacation']]);
												$eid = $APP1['eid'] . $APP1['vacation'];
											} else {
												unset($vacation);
												$eid = $APP1['eid'];
											}
											$selected = ($_SESSION['app1'] == $APP1[eid]) ? selected : $blank;
											print "<option value=\"".$eid."\" ".$selected.">".caps($APP1['lst'].", ".$APP1['fst']).$vacation."</option>";
										  }
										  ?>
                                        </select>
                                          <vlvalidator name="app1" type="compare" control="app1" errmsg="Approver 1 is required for all requests." validtype="string" comparevalue="0" comparecontrol="app1" operator="ne"></td>
                                    </tr>
                                    <tr>
									  <?php if ($_SESSION['total'] > $default['app2_min']) { ?>
									  <td><vllabel form="form1" validators="app2" class="valRequired2" errclass="valError">Approver 2:</vllabel></td> 
									  <?php } else { ?>
									  <td class="valNone">Approver 2:</td>
									  <?php } ?>
                                      <td><select name="app2" id="app2">
                                          <option value="0">Select One</option>
                                          <?php
										  $app2_sth = $dbh->execute($app2_sql);
										  while($app2_sth->fetchInto($APP2)) {
											if (strlen($APP2['vacation']) == 5) {
												$vacation = " => ".caps($EMPLOYEE[$APP2['vacation']]);
												$eid = $APP2['eid'] . $APP2['vacation'];
											} else {
												unset($vacation);
												$eid = $APP2['eid'];
											}
											$selected = ($_SESSION['app2'] == $APP2[eid]) ? selected : $blank;
											print "<option value=\"".$eid."\" ".$selected.">".caps($APP2['lst'].", ".$APP2['fst']).$vacation."</option>";
										  }
										  ?>
                                        </select>
                                          <?php if ($_SESSION['total'] > $default['app2_min']) { ?>
										  <vlvalidator name="app2" type="compare" control="app2" errmsg="Approver 2 is required for requests between $<?= $default['app2_min']; ?> and $<?= $default['app2_max']; ?>." validtype="string" comparevalue="0" comparecontrol="app2" operator="ne">
									  <?php } ?></td>
                                    </tr>
                                    <tr>
									  <?php if ($_SESSION['total'] > $default['app3_min']) { ?>
									  <td><vllabel form="form1" validators="app3" class="valRequired2" errclass="valError">Approver 3:</vllabel></td> 
									  <?php } else { ?>
									  <td class="valNone">Approver 3:</td>
									  <?php } ?>									  
                                      <td><select name="app3" id="app3">
                                          <option value="0">Select One</option>
                                          <?php
										  $app3_sth = $dbh->execute($app3_sql);
										  while($app3_sth->fetchInto($APP3)) {
											if (strlen($APP3['vacation']) == 5) {
												$vacation = " => ".caps($EMPLOYEE[$APP3['vacation']]);
												$eid = $APP3['eid'] . $APP3['vacation'];
											} else {
												unset($vacation);
												$eid = $APP3['eid'];
											}
											$selected = ($_SESSION['app3'] == $APP3[eid]) ? selected : $blank;
											print "<option value=\"".$eid."\" ".$selected.">".caps($APP3['lst'].", ".$APP3['fst']).$vacation."</option>";
										  }
										  ?>
                                        </select>
										  <?php if ($_SESSION['total'] > $default['app3_min']) { ?>
                                          <vlvalidator name="app3" type="compare" control="app3" errmsg="Approver 3 is required for requests between $<?= $default['app3_min']; ?> and $<?= $default['app3_max']; ?>." validtype="string" comparevalue="0" comparecontrol="app3" operator="ne">
									  <?php } ?></td>
                                    </tr>
                                    <tr>
									  <?php if ($_SESSION['total'] > $default['app4_min']) { ?>
									  <td><vllabel form="form1" validators="app4" class="valRequired2" errclass="valError">Approver 4:</vllabel></td> 
									  <?php } else { ?>
									  <td class="valNone">Approver 4:</td>
									  <?php } ?>									  
                                      <td><select name="app4" id="app4">
                                          <option value="0">Select One</option>
                                          <?php
										  $app4_sth = $dbh->execute($app4_sql);
										  while($app4_sth->fetchInto($APP4)) {
											if (strlen($APP4['vacation']) == 5) {
												$vacation = " => ".caps($EMPLOYEE[$APP4['vacation']]);
												$APP4['eid'] . $eid = $APP4['vacation'];
											} else {
												unset($vacation);
												$eid = $APP4['eid'];
											}
											$selected = ($_SESSION['app4'] == $APP4[eid]) ? selected : $blank;
											print "<option value=\"".$eid."\" ".$selected.">".caps($APP4['lst'].", ".$APP4['fst']).$vacation."</option>";
										  }
										  ?>
                                        </select>
										  <?php if ($_SESSION['total'] > $default['app4_min']) { ?>
                                          <vlvalidator name="app4" type="compare" control="app4" errmsg="Approver 4 is required for requests above $<?= $default['app4_min']; ?>." validtype="string" comparevalue="0" comparecontrol="app4" operator="ne">
									  <?php } ?></td>
                                    </tr>

                                </table></td>
                              </tr>
                              <tr>
                                <td height="5"><img src="../images/spacer.gif" width="5" height="5"> </td>
                              </tr>
                              <tr>
                                <td><div align="right">
                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td>&nbsp;<a href="items.php"><img src="../images/button.php?i=b70.png&l=Back" border="0"></a></td>
                                        <td><div align="right">
                                            <input name="stage" type="hidden" id="stage" value="three">
                                            <input name="next" type="image" id="next" src="../images/button.php?i=b70.png&l=Next" class="button" border="0">
                                          &nbsp;</div></td>
                                      </tr>
                                    </table>
                                </div></td>
                              </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td><vlsummary form="form1" class="valErrorList" headertext="This Request totals $<?= $_SESSION['total']; ?> and requires particular Authorization:" displaymode="bulletlist" showsummary="true" messagebox="false"></td>
                          </tr>
                          <tr>
                            <td><img src="../images/spacer.gif" width="10" height="50"></td>
                          </tr>
                          <tr>
                            <td class="BGAccentDarkBorder" style="padding:10px">
							  This Requisition may also need approval by a plant Controller.<br>
							  Approver 1 is required for all requisitions.<br>
                              Approver 2 is required for requisitions above $<?= number_format($default['app2_min'],2); ?>.<br>
                              Approver 3 is required for requisitions above $<?= number_format($default['app3_min'],2); ?>.<br>
                              Approver 4 is required for requisitions above $<?= number_format($default['app4_min'],2); ?>. </td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
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