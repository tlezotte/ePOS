<?php
/**
 * Request System
 *
 * detail.php detailed view of CER
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

/* ------------------ START UPDATE STAGE ----------------------- */
if ($_POST['stage'] == "update") {
	if ($_POST['auth'] == "issuer") {
		/* Update the CER */
		$dbh->query("UPDATE CER
					 set cer='".$_POST['cer']."', 
					      gl='".$_POST['gl']."'
					 where id = ".$_POST['type_id']." ");
		$dbh->query("UPDATE Authorization
					 set issuerDate=NOW()
					 where type_id = ".$_POST['type_id']." ");
					
		header("Location: router.php?type_id=".$_POST['type_id']."&approval=".$_POST['auth']."&cer=".$_POST['cer']."");
	} else {
		/* Update the approvals for the CER */
		$dbh->query("UPDATE Authorization ".
					"set ".$_POST['auth']."yn='".$_POST['yn']."', ".$_POST['auth']."Date=NOW(), ".$_POST['auth']."Com='".$_POST['Com']."'".
					"where id = ".$_POST['auth_id']." ");
		
		header("Location: router.php?type_id=".$_POST['type_id']."&approval=".$_POST['auth']."&yn=".$_POST['yn']."");
	}
}
/* ------------------ END UPDATE STAGE ----------------------- */


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting CER information */
$CER = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate 
					 FROM CER 
					 WHERE id = ?",array($_GET['id']));
/* Getting Authoriztions for above CER */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'CER'",array($CER['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							FROM Users u, Standards.Employees e 
							WHERE e.eid = u.eid");										
/* Project Originator from Users.Requesters */
$req_query = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
							FROM Users U, Standards.Employees E 
							WHERE U.eid = E.eid and U.requester = '1' and U.status = '0' 
							ORDER BY E.lst ASC");
/* Getting plant locations from Standards.Plants */								
$location_guery = $dbh->prepare("select id, name from Standards.Plants order by name");

$issuer_query  = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
							    FROM Users U, Standards.Employees E 
							    WHERE U.eid = E.eid and U.issuer = '1' and U.status = '0' 
							    ORDER BY E.lst ASC");
$app_query  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
							"FROM Users U, Standards.Employees E ".
							"WHERE U.eid = E.eid and U.cer = ? and U.status = '0' ".
							"ORDER BY E.lst ASC");
/* Getting Your Company Companies from Standards.Companies */								
$companies_guery = $dbh->prepare("SELECT id, name 
								  FROM Standards.Companies 
								  WHERE id > 0
								  ORDER BY name");			
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

require_once('attachment.php'); 		// Display attachment icon

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
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_exclusive.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_iframe.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/overlibmws_draggable.js"></SCRIPT>
	<SCRIPT SRC="/Common/js/overlibmws/calendarmws.js"></SCRIPT>
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
                    <br>
                        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" name="Form" id="Form">
                          <table border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td>
                                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
								    <tr>
                                      <td>&nbsp;</td>
                                      <td height="26" valign="top"><div align="right"><a href="<?= $default['URL_HOME']; ?>/CER/print.php?id=<?= $CER['id']; ?>" <?php help('', 'Click here to print this Capital Expense Request', 'default'); ?>><img src="../images/button.php?i=b70.png&l=Print" name="noPrint" border="0" align="absmiddle" id="noPrint"></a>&nbsp;&nbsp;</div></td>
                                    </tr>
                                    <tr class="BGAccentVeryDark">
                                      <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Capital Expenditure Request...</td>
                                      <td width="50%">&nbsp;</td>
                                    </tr>
                                  </table>
                              </td>
                            </tr>
                            <tr>
                              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                        <tr>
                                          <td height="25" colspan="6" class="BGAccentDark"><strong>&nbsp;&nbsp;Project Information </strong></td>
                                        </tr>
                                        <tr>
                                          <td width="150">CER Number: </td>
                                          <td width="20"><?php if ($_GET['approval'] == "issuer") { echo $WARNING; } ?></td>
                                          <td><input name="cer" type="text" id="cer" size="10" maxlength="10" value="<?= $CER['cer']; ?>"></td>
                                          <td>General Ledger Number: </td>
                                          <td width="20"><?php if ($_GET['approval'] == "issuer") { echo $WARNING; } ?></td>
                                          <td><input name="gl" type="text" id="gl" size="20" maxlength="20" value="<?= $CER['gl']; ?>"></td>
                                        </tr>
                                        <tr>
                                          <td>Project Originator:</td>
                                          <td>&nbsp;</td>
                                          <td><?= ucwords(strtolower($EMPLOYEES[$CER['req']])); ?>                                          </td>
                                          <td width="175">Project Submit Date:</td>
                                          <td>&nbsp;</td>
                                          <td><?= $CER['_reqDate']; ?></td>
                                        </tr>
                                        <tr>
                                          <td>Company Origination:</td>
                                          <td>&nbsp;</td>
                                          <td><select name="company" id="company">
                                            <option value="0">Select One</option>
                                            <?php
										  $sth = $dbh->execute($companies_guery);
										  while($sth->fetchInto($row)) {
										    $selected = ($CER['company'] == $row[id]) ? selected : $blank;
											print "<option value=\"".$row[id]."\" ".$selected.">".$row[name]."</option>\n";
										  }
										  ?>
                                          </select></td>
                                          <td>Project Start Date:</td>
                                          <td>&nbsp;</td>
                                          <td><input name="date1" type="text" id="date1" value="<?= $CER['date1']; ?>" size="10" maxlength="10" readonly>
&nbsp;<a href="javascript:show_calendar('Form.date1')" <?php help('', 'Click to select a date from a calendar popup', 'default') ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                        </tr>
                                        <tr>
                                          <td>Company Location:</td>
                                          <td>&nbsp;</td>
                                          <td><select name="location">
                                            <option value="0">Select One</option>
                                            <option value="100" <?php if ($CER['location'] == 100) { echo "selected"; } ?>>** All Plants **</option>
                                            <?php
										  $sth = $dbh->execute($location_guery);
										  while($sth->fetchInto($row)) {
										    $selected = ($CER['location'] == $row[id]) ? selected : $blank;
											$disabled = ($row[id] == '2') ? disabled : $blank;			//Disable selection for Deluxe Engineering
											print "<option value=\"".$row[id]."\" ".$selected." ".$disabled.">".$row[name]."</option>\n";
										  }
										  ?>
                                          </select></td>
                                          <td>Project Completion Date:</td>
                                          <td>&nbsp;</td>
                                          <td><input name="date2" type="text" id="date2" value="<?= $CER['date2']; ?>" size="10" maxlength="10" readonly>
&nbsp;<a href="javascript:show_calendar('Form.date2')" <?php help('', 'Click to select a date from a calendar popup', 'default') ?>><img src="../images/calendar.gif" width="17" height="18" border="0" align="absmiddle"></a></td>
                                        </tr>
                                        <tr>
                                          <td>Project Title:</td>
                                          <td>&nbsp;</td>
                                          <td colspan="4"><input name="purpose" type="text" id="purpose" size="100" maxlength="100" value="<?= stripslashes($CER['purpose']); ?>"></td>
                                        </tr>
                                    </table></td>
                                  </tr>
								  <tr><td>&nbsp;</td></tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                            <tr>
                                              <td height="25" colspan="2" class="BGAccentDark"><strong>&nbsp;&nbsp;Project Classification</strong></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass1" type="radio" value="1" <?php if ($CER['projClass'] == "1") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass1">Expansion / New Product Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass2" type="radio" value="2" <?php if ($CER['projClass'] == '2') { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass2">Cost Reduction Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass3" type="radio" value="3" <?php if ($CER['projClass'] == "3") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass3">Replacement / Profit Maintaining Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass4" type="radio" value="4" <?php if ($CER['projClass'] == "4") { echo "checked"; } ?>>
                                              </div></td>
                                              <td><label for="projClass4">OSHA / Environmental / Safety Projects</label></td>
                                            </tr>
                                            <tr>
                                              <td><div align="center">
                                                  <input name="projClass" id="projClass5" type="radio" value="5" <?php if ($CER['projClass'] == "5") { echo "checked"; } ?>>
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
                                              <td>&nbsp;&nbsp;
                                                  <select name="capBudget" id="capBudget">
                                                    <option value="yes" <?php if ($CER['capBudget'] == 'yes') { echo "selected"; } ?>>Yes</option>
                                                    <option value="no" <?php if ($CER['capBudget'] == 'no') { echo "selected"; } ?>>No</option>
                                                </select></td>
                                            </tr>
                                            <tr>
                                              <td>Amount Budgeted: </td>
                                              <td>$
                                                  <input name="amtBudget" type="text" id="amtBudget" value="<?= $CER['amtBudget']; ?>" size="15" maxlength="15"></td>
                                            </tr>
                                            <tr>
                                              <td>Budget Transfer: </td>
                                              <td>$
                                                  <input name="budgetTrans" type="text" id="budgetTrans" value="<?= $CER['budgetTrans']; ?>" size="15" maxlength="15"></td>
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
								  <tr><td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Summary of Project Description and Justifications</strong></td>
                                    </tr>
                                    <tr>
                                      <td><textarea name="summary" cols="100" rows="10" id="summary"><?= $CER['summary']; ?></textarea>
                                        <div align="center"></div></td>
                                    </tr>
                                    <tr>
                                      <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="110" height="25" class="BGAccentDark">&nbsp;&nbsp;<strong>Attachment:</strong></td>
                                          <td class="BGAccentDarkBorder"><strong><?= $Attachment; ?></strong></td>
                                        </tr>
                                      </table></td>
                                    </tr>
                                  </table></td></tr>
								  <tr>
								    <td>&nbsp;</td>
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
                                                        <td>Asset Cost:</td>
                                                        <td>$
                                                            <input name="assetCost" type="text" id="assetCost" value="<?= $CER['assetCost']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Accessories Cost: </td>
                                                        <td>$
                                                            <input name="accCost" type="text" id="accCost" value="<?= $CER['accCost']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Frt &amp; Installation: </td>
                                                        <td>$
                                                            <input name="frtInstall" type="text" id="frtInstall" value="<?= $CER['frtInstall']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Other:</td>
                                                        <td>$
                                                            <input name="otherCost" type="text" id="otherCost" value="<?= $CER['otherCost']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Total:</td>
                                                        <td>$
                                                            <input name="totalCost" type="text" id="totalCost" value="<?= $CER['totalCost']; ?>" size="12" maxlength="15" readonly></td>
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
                                                        <td>Net Present Value:</td>
                                                        <td>$
                                                            <input name="netValue" type="text" id="netValue" value="<?= $CER['netValue']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Internal Rate of Return:</td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="rateOfReturn" type="text" id="rateOfReturn" value="<?= $CER['rateOfReturn']; ?>" size="12" maxlength="15">
              % </td>
                                                      </tr>
                                                      <tr>
                                                        <td>Return on Net Assets:</td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="netAsset" type="text" id="netAsset" value="<?= $CER['netAsset']; ?>" size="12" maxlength="15">
              % </td>
                                                      </tr>
                                                      <tr>
                                                        <td>Payback (Yrs): </td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="payback" type="text" id="payback" value="<?= $CER['payback']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Asset Life:</td>
                                                        <td>&nbsp;&nbsp;
                                                            <input name="assetLife" type="text" id="assetLife" value="<?= $CER['assetLife']; ?>" size="12" maxlength="15"></td>
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
                                                              <td width="55">1st Qtr</td>
                                                              <td><select name="firstYr" id="firstYr">
                                                                  <option value="2005">2005</option>
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
                                                            <input name="firstExp" type="text" id="firstExp" value="<?= $CER['firstExp']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55">2nd Qtr</td>
                                                              <td><select name="secYr" id="secYr">
                                                                  <option value="2005">2005</option>
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
                                                            <input name="secExp" type="text" id="secExp" value="<?= $CER['secExp']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55">3rd Qtr</td>
                                                              <td><select name="thirdYr" id="thirdYr">
                                                                  <option value="2005">2005</option>
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
                                                            <input name="thirdExp" type="text" id="thirdExp" value="<?= $CER['thirdExp']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                              <td width="55">4th Qtr</td>
                                                              <td><select name="forthYr" id="forthYr">
                                                                  <option value="2005">2005</option>
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
                                                            <input name="forthExp" type="text" id="forthExp" value="<?= $CER['forthExp']; ?>" size="12" maxlength="15"></td>
                                                      </tr>
                                                      <tr>
                                                        <td>Total:</td>
                                                        <td>$
                                                            <input name="totalExp" type="text" id="totalExp" value="<?= $CER['totalExp']; ?>" size="12" maxlength="15" readonly></td>
                                                      </tr>
                                                  </table></td>
                                                </tr>
                                              </table></td>
                                            </tr>
                                          </table></td>
                                        </tr>
                                    </table></td>
                                  </tr>
								  <tr><td>&nbsp;</td></tr>
								  <tr>
								    <td class="BGAccentDarkBorder"><table width="100%"  border="0">
                                      <tr>
                                        <td height="25" colspan="6" class="BGAccentDark"><strong>&nbsp;&nbsp;Approvals</strong></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>Plant Manager: </td>
                                        <td width="45" nowrap><strong>
                                          <?php CheckAuth($AUTH['app1'], $AUTH['app1yn'], $AUTH['app1Com'], $AUTH['app1Date']); ?>
                                        </strong></td>
                                        <td><select <?php CheckAuthLevel(1); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('1'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app1'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                        <td nowrap>Chief Operating Officer: </td>
                                        <td width="45" nowrap><strong>
                                          <?php CheckAuth($AUTH['app7'], $AUTH['app7yn'], $AUTH['app7Com'], $AUTH['app7Date']); ?>
                                        </strong></td>
                                        <td><select name="app7" id="app7" <?php CheckAuthLevel(7); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('7'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app7'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>Plant Controller : </td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app2'], $AUTH['app2yn'], $AUTH['app2Com'], $AUTH['app2Date']); ?>
                                        </strong></td>
                                        <td><select name="app2" <?php CheckAuthLevel(2); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('2'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app2'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                        <td nowrap>Vice President Finance:</td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app8'], $AUTH['app8yn'], $AUTH['app8Com'], $AUTH['app8Date']); ?>
                                        </strong></td>
                                        <td><select name="app8" id="app8" <?php CheckAuthLevel(8); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('8'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app8'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>Plant Engineer: </td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app3'], $AUTH['app3yn'], $AUTH['app3Com'], $AUTH['app3Date']); ?>
                                        </strong></td>
                                        <td><select name="app3" <?php CheckAuthLevel(3); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('3'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app3'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                        <td nowrap>Chief Financial Officer: </td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app9'], $AUTH['app9yn'], $AUTH['app9Com'], $AUTH['app9Date']); ?>
                                        </strong></td>
                                        <td><select name="app9" id="app9" <?php CheckAuthLevel(9); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('9'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app9'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>Other:</td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app4'], $AUTH['app4yn'], $AUTH['app4Com'], $AUTH['app4Date']); ?>
                                        </strong></td>
                                        <td><select name="app4" <?php CheckAuthLevel(4); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('4'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app4'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                        <td nowrap>Chief Executive Officer:</td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app10'], $AUTH['app10yn'], $AUTH['app10Com'], $AUTH['app10Date']); ?>
                                        </strong></td>
                                        <td><select name="app10" id="app10" <?php CheckAuthLevel(10); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('10'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app10'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>Other: </td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app5'], $AUTH['app5yn'], $AUTH['app5Com'], $AUTH['app5Date']); ?>
                                        </strong></td>
                                        <td><select name="app5" id="app5" <?php CheckAuthLevel(5); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('5'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app5'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                        <td nowrap>Chairman of the Board:</td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app11'], $AUTH['app11yn'], $AUTH['app11Com'], $AUTH['app11Date']); ?>
                                        </strong></td>
                                        <td><select name="app11" id="app11" <?php CheckAuthLevel(11); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('11'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app11'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                      </tr>
                                      <tr>
                                        <td nowrap>Corporate Controller: </td>
                                        <td nowrap><strong>
                                          <?php CheckAuth($AUTH['app6'], $AUTH['app6yn'], $AUTH['app6Com'], $AUTH['app6Date']); ?>
                                        </strong></td>
                                        <td><select name="app6" id="app6" <?php CheckAuthLevel(6); ?>>
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($app_query,array('6'));
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['app6'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                        <td nowrap>CER Issuer: </td>
                                        <td nowrap><strong>
                                          <?php CheckAuth(NULL, NULL, NULL, $AUTH['issuerDate']); ?>
                                        </strong></td>
                                        <td><select name="issuer">
                                          <option value="0">Select One</option>
                                          <?php
										  $sth = $dbh->execute($issuer_query);
										  while($sth->fetchInto($row)) {
											$selected = ($AUTH['issuer'] == $row[eid]) ? selected : $blank;
											print "<option value=\"".$row[eid]."\" ".$selected.">".ucwords(strtolower($row[lst].", ".$row[fst]))."</option>";
										  }
										  ?>
                                        </select></td>
                                      </tr>
                                    </table></td>
							    </tr>
								<?php if ($_SESSION['eid'] == $AUTH[$_GET['approval']] and $_GET['approval'] != "issuer") { ?>
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                    <tr>
                                      <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Approved</strong></td>
                                    </tr>
                                    <tr>
                                      <td><table  border="0" cellpadding="0" cellspacing="0">
                                          <tr>
                                            <td width="100">Approved:</td>
                                            <td width="20"><?= $WARNING; ?></td>
                                            <td width="20"><input name="yn" type="radio" value="yes"></td>
                                            <td width="50">Yes</td>
                                            <td width="20"><input name="yn" type="radio" value="no"></td>
                                            <td>No</td>
                                          </tr>
                                        </table></td>
                                    </tr>
                                    <tr>
                                      <td><table  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="100">Comments:</td>
                                          <td width="20"><?= $WARNING; ?></td>
                                          <td><input name="Com" type="text" id="Com" size="100" maxlength="100"></td>
                                        </tr>
                                      </table></td>
                                    </tr>
                                  </table></td></tr>
								  <?php } ?>
                              </table></td>
                            </tr>
                            <tr>
                              <td height="5" class="GlobalButtonTextDisabled"><img src="../images/spacer.gif" width="15" height="5"></td>
                            </tr>
                            <tr>
                              <td>
                                <div align="right">
                                 <?php if ($_SESSION['eid'] == $AUTH[$_GET['approval']]) { ?>
                                  <input name="auth" type="hidden" id="auth" value="<?= $_GET['approval']; ?>">
                                  <input name="auth_id" type="hidden" id="auth_id" value="<?= $AUTH['id']; ?>">
                                  <input name="type_id" type="hidden" id="type_id" value="<?= $AUTH['type_id']; ?>">
                                  <input name="stage" type="hidden" id="stage" value="update">
                                  <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Update" border="0">
								 <?php } ?>                             
                                &nbsp;&nbsp; </div></td>
                            </tr>
                          </table>
                    </form>
<script type="text/javascript">
	var frmvalidator = new Validator("Form");
  <?php if ($_GET['approval'] == 'issuer') { ?>
	frmvalidator.addValidation("cer","req","Please enter a CER number");
  <?php } else { ?>
	frmvalidator.addValidation("yn","selone","Please select Yes or No");
	frmvalidator.addValidation("Com","req","Please enter a comment");
  <?php } ?>  
</script>

<?php 
if (!empty($CER['cer'])) {
	/* Getting suppliers from Standards */						 
	$SUPPLIERS = $dbh->getAssoc("SELECT BTVEND AS id, BTNAME AS name
							    FROM Standards.Vendor
							    ORDER BY name");
						   
	/* SQL for PO list */
	$po_sql =& $dbh->prepare("SELECT id, po, purpose, reqDate, req, sup, total
							  FROM PO 
							  WHERE cer = '".$CER['id']."'
							  ORDER BY reqDate ASC");
			
	$po_sth = $dbh->execute($po_sql);
	$num_rows = $po_sth->numRows();	
	
	/* Dont display column headers and totals if no requests */
	if ($num_rows == 0) {
?>
<div align="center" class="DarkHeaderSubSub">No Purchase Orders Found</div>
<?php } else { ?>
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
	<td class="BGAccentVeryDark"><div align="left">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Associated Purchase Orders... </td>
			<td>&nbsp;</td>
		  </tr>
		</table>
	</div></td>
  </tr>
  <tr>
	<td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td><table width="100%"  border="0">
			  <tr>
				<td height="25" class="BGAccentDark">&nbsp;</td>
				<td class="BGAccentDark"><strong>&nbsp;PO</strong></td>
				<td class="BGAccentDark"><strong>&nbsp;Purpose</strong></td>
				<td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
				<td class="BGAccentDark"><strong>&nbsp;Submitted</strong></td>
				<td class="BGAccentDark"><strong>&nbsp;Vendor</strong></td>
				<td class="BGAccentDark"><div align="center"><strong>&nbsp;Total</strong></div></td>
			  </tr>
			  <?php
				/* Reset items total variable */
				$itemsTotal = 0;
				$span = 4;
				
				/* Loop through list of POs */
				while($po_sth->fetchInto($PO)) {
				/* Line counter for alternating line colors */
				$counter++;
				$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
			  ?>
			  <tr <?php pointer($row_color); ?>>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><a href="../PO/detail.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Get a Detailed view', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="../PO/print.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Print a hardcopy', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/printer.gif" width="15" height="20" border="0" align="absmiddle"></a></td>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><?= $PO[po]; ?></td>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower(substr(stripslashes($PO[purpose]), 0, 40))); ?>
					<?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?></td>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[req]])); ?></td>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo $reqDate[0]; ?></td>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($SUPPLIERS[$PO[sup]])); ?></td>
				<td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					  <td width="2%">$</td>
					  <td width="98%"><div align="right"><?= number_format($PO['total'], 2, '.', ','); ?></div></td>
					</tr>
				</table></td>
			  </tr>
			  <?php $itemsTotal += $PO[total]; ?>
			  <?php } ?>
		  </table></td>
		</tr>
		<tr>
		  <td class="BGAccentDark"><table  border="0" align="right" cellpadding="0" cellspacing="0">
			  <tr>
				<td class="padding"><strong>Total Spent:</strong></td>
				<td width="100" align="right" class="padding" <?php if ($itemsTotal > $CER['totalCost']) { echo "style=\"color: red\""; } ?>>&nbsp;$<?= number_format($itemsTotal, 2, '.', ','); ?></td>
			  </tr>
		  </table></td>
		</tr>		
		<tr>
		  <td class="BGAccentDark"><table  border="0" align="right" cellpadding="0" cellspacing="0">
            <tr>
              <td class="padding"><strong>Total Remaining:</strong></td>
              <td width="100" align="right" class="padding" <?php if ($itemsTotal > $CER['totalCost']) { echo "style=\"color: red\""; } ?>>&nbsp;$<?= number_format($CER['totalCost'] - $itemsTotal, 2, '.', ','); ?></td>
            </tr>
          </table></td>
		  </tr>
	</table></td>
  </tr>
  <tr>
	<td>&nbsp;<span class="GlobalButtonTextDisabled"><?= $num_rows ?> Requests</span></td>
  </tr>
</table>
<?php } ?>
<?php } ?>
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