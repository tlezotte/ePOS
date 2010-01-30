<?php 
/**
 * Request System
 *
 * print.php prints a hardcopy of CER.
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

/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/**
 * - Database Connection
 */
require_once('../Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('../include/config.php'); 
$CER = $dbh->getRow("SELECT * FROM CER WHERE id = ?",array($_GET['id']));
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'CER'",array($CER['id']));

$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name FROM Users u, Standards.Employees e WHERE e.eid = u.eid");
$SETTINGS = $dbh->getAssoc("SELECT variable, value FROM Settings WHERE company = ".$CER['company']."");			
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

require_once('attachment.php');

/* ---- Set Project Class description ---- */
switch ($CER['projClass']) {
case 1:
   $projClass = "Expansion / New Product Projects";
   break;
case 2:
   $projClass = "Cost Reduction Projects";
   break;
case 3:
   $projClass = "Replacement / Profit Maintaining Projects";
   break;
case 4:
   $projClass = "OSHA / Environmental / Safety Projects";
   break;   
case 5:
   $projClass = "Other Projects";
   break;   
}

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="copyright" content="2004 Your Company" />
	<link href="/Common/noPrint.css" rel="stylesheet" type="text/css">
	<link href="/Common/Print.css" rel="stylesheet" type="text/css" media="print">
	<link href="/Common/newCompany.css" rel="stylesheet" type="text/css" media="screen">
	<link href="../epos.css" type="text/css" charset="UTF-8" rel="stylesheet">
  
    <style type="text/css">
<!--
.style1 {font-size: 14}
-->
    </style>
  </head>

  <?php if ($debug) { ?>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">	
  <?php } else { ?>
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" onLoad="javascript:window.print() ;javascript:history.back()">
  <?php } ?>
  <table width="750" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td><div align="left">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="187" height="106" valign="top"><img src="<?= $SETTINGS['logo']; ?>" alt="" width="187" height="105" /></td>
                  <td><div align="center">Capital Expenditure Request (CER)<br>
        Project Approval Sheet <br>
                  </div></td>
                </tr>
              </table></td>
            </tr>
        </table>
      </div></td>
    </tr>
    <tr>
      <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                <tr>
                  <td height="25" colspan="4" class="BGAccentDark"><strong>&nbsp;&nbsp;Project Information </strong></td>
                </tr>
                <tr>
                  <td>Project Title :</td>
                  <td colspan="3"><strong><?= stripslashes($CER['purpose']); ?></strong></td>
                </tr>
                <tr>
                  <td>Project Originator :</td>
                  <td><strong><?= ucwords(strtolower($EMPLOYEES[$CER['req']])); ?></strong></td>
                  <td>Project Start Date:&nbsp;</td>
                  <td><strong><?= $CER['date1']; ?></strong></td>
                </tr>
                <tr>
                  <td>Company Location:</td>
                  <td><strong>
                    <?php 
					  if ($CER['location'] == '100') {
						echo "All Plants";
					  } else {
						$location = $dbh->getRow("SELECT name FROM Standards.Plants WHERE id = ?", array($CER['location']));
						echo ucwords(strtolower($location[name]));
					  }
					  ?>
                  </strong></td>
                  <td>Project Completion Date:</td>
                  <td><strong><?= $CER['date2']; ?></strong></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                      <tr>
                        <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Project Classification</strong></td>
                      </tr>
                      <tr>
                        <td valign="top"><blockquote><?= $projClass; ?></blockquote></td>
                      </tr>
                  </table></td>
                  <td width="15" valign="top">&nbsp;</td>
                  <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                      <tr>
                        <td height="25" colspan="2" class="BGAccentDark"><strong>&nbsp;&nbsp;Budget Status </strong></td>
                      </tr>
                      <tr>
                        <td>Capital Budget:</td>
                        <td><strong><?= $CER['capBudget']; ?></strong></td>
                      </tr>
                      <tr>
                        <td>Amount Budgeted: </td>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="12">$</td>
                            <td class="right"><strong><?= number_format($CER['amtBudget'],2); ?></strong></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td>Budget Transfer: </td>
                        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="12">$</td>
                            <td class="right"><strong><?= number_format($CER['budgetTrans'],2); ?></strong></td>
                          </tr>
                        </table></td>
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
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                      <tr>
                        <td width="65%" height="25" class="BGAccentDark" nowrap><strong>&nbsp;&nbsp;Summary of Project Description and Justifications</strong></td>
                      </tr>
                      <tr>
                        <td height="100" valign="top"><blockquote><?= $CER['summary']; ?></blockquote></td>
                      </tr>
                      <tr>
                        <td valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="110" height="25" class="BGAccentDark">&nbsp;&nbsp;<strong>Attachment:</strong></td>
                              <td class="BGAccentDarkBorder">&nbsp;<?= $Attachment; ?> </td>
                            </tr>
                          </table></td>
                      </tr>
                  </table></td>
                </tr>
            </table></td>
          </tr>
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
                                    <td width="150">Asset Cost:</td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['assetCost'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td>Accessories Cost: </td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['accCost'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td>Frt &amp; Installation: </td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['frtInstall'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td>Other:</td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['otherCost'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td>Total:</td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['totalCost'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
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
                                    <td width="175">Net Present Value:</td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['netValue'],2); ?></strong></td>
                                      </tr>
                                    </table> </td>
                                  </tr>
                                  <tr>
                                    <td>Internal Rate of Return:</td>
                                    <td class="right">&nbsp;<strong>&nbsp;<?= number_format($CER['rateOfReturn'],2); ?></strong>% </td>
                                  </tr>
                                  <tr>
                                    <td>Return on Net Assets:</td>
                                    <td class="right">&nbsp;&nbsp;<strong><?= number_format($CER['netAsset'],2); ?></strong>% </td>
                                  </tr>
                                  <tr>
                                    <td>Payback (Yrs): </td>
                                    <td class="right">&nbsp;&nbsp;<strong><?= number_format($CER['payback'],1); ?></strong></td>
                                  </tr>
                                  <tr>
                                    <td>Asset Life:</td>
                                    <td class="right">&nbsp;&nbsp;<strong><?= number_format($CER['assetLife'],1); ?></strong></td>
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
                                    <td width="100"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="55">1st Qtr</td>
                                          <td><?= $CER['firstYr']; ?></td>
                                        </tr>
                                    </table></td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['firstExp'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="55">2nd Qtr</td>
                                          <td><?= $CER['secYr']; ?></td>
                                        </tr>
                                    </table></td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['secExp'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="55">3rd Qtr</td>
                                          <td><?= $CER['thirdYr']; ?></td>
                                        </tr>
                                    </table></td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['thirdExp'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td width="55">4th Qtr</td>
                                          <td><?= $CER['forthYr']; ?></td>
                                        </tr>
                                    </table></td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['forthExp'],2); ?></strong></td>
                                      </tr>
                                    </table></td>
                                  </tr>
                                  <tr>
                                    <td>Total:</td>
                                    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="12">$</td>
                                        <td class="right"><strong><?= number_format($CER['totalExp'],2); ?></strong></td>
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
            <td valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                <tr>
                  <td height="25" colspan="6" class="BGAccentDark"><strong>&nbsp;&nbsp;Approvals</strong></td>
                </tr>
                <tr>
                  <td width="140" nowrap>Project Originator :</td>
                  <td width="160" nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app1']])); ?></strong></td>
                  <td width="70" nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app1Date']); ?></td>
                  <td width="150" nowrap>Corporate Controller: </td>
                  <td width="160" nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app7']])); ?></strong></td>
                  <td width="70" nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app7Date']); ?></td>
                </tr>
                <tr>
                  <td nowrap>Plant Manager: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app2']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app2Date']); ?></td>
                  <td nowrap>Chief Operating Officer: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app8']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app8Date']); ?></td>
                </tr>
                <tr>
                  <td nowrap>Plant Controller : </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app3']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app3Date']); ?></td>
                  <td nowrap>Vice President Officer: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app9']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app9Date']); ?></td>
                </tr>
                <tr>
                  <td nowrap>Plant Engineer: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app4']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app4Date']); ?></td>
                  <td nowrap>Chief Financial Officer: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app10']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app10Date']); ?></td>
                </tr>
                <tr>
                  <td nowrap>Other:</td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app5']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app5Date']); ?></td>
                  <td nowrap>Chief Executive Officer: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app11']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app11Date']); ?></td>
                </tr>
                <tr>
                  <td nowrap>Other: </td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app6']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app6Date']); ?></td>
                  <td nowrap>Chairman of the Board:</td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['app12']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['app12Date']); ?></td>
                </tr>
                <tr>
                  <td nowrap>&nbsp;</td>
                  <td nowrap>&nbsp;</td>
                  <td nowrap class="InlineInfoText">&nbsp;</td>
                  <td nowrap>CER Issuer:</td>
                  <td nowrap><strong><?= ucwords(strtolower($EMPLOYEES[$AUTH['issuer']])); ?></strong></td>
                  <td nowrap class="InlineInfoText"><?= preg_replace("/ /", "<br>", $AUTH['issuerDate']); ?></td>
                </tr>
            </table></td>
          </tr>
      </table></td>
    </tr>
</table>
    <br>

    <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td colspan="2">
          </td>
        </tr>

        <tr>
          <td colspan="2">
          </td>
        </tr>
        <tr>
          <td width="100%" height="20" colspan="2"><?php include('../include/copyright.php'); ?></td>
        </tr>

        <tr>
          <td colspan="2">
          </td>
        </tr>
      </tbody>
  </table>
    <br>
</body>
</html>
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
