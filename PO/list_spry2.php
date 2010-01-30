<?php
/**
 * Request System
 *
 * list.php displays available PO.
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
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../include/BlackBerry.php');

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


if ($_SESSION['request_role'] == 'purchasing') {
	if (!array_key_exists('status', $_GET)) {
		$_GET['status'] = 'A';
	}
}

/* SQL for different views of PO list */
if ($_GET['action'] == "my" AND $_GET['view'] == "all") {
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&access=".$_GET['access']);
	$view_gif = '../images/button.php?i=b90.png&l=View Open';
	$view_help = 'View all of My Open Requests';
} elseif ($_GET['view'] == "all") {
	$view_all = $_SERVER['PHP_SELF'];
	$view_gif = '../images/button.php?i=b90.png&l=View Open';
	$view_help = 'View all Open Requests';
} elseif ($_GET['action'] == "my") {
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&view=all&access=".$_GET['access']);
	$view_gif = '../images/button.php?i=b90.png&l=View All';
	$view_help = 'View all of My Requests';
} else {
	$view_all = htmlentities($_SERVER['PHP_SELF']."?view=all");
	$view_gif = '../images/button.php?i=b90.png&l=View All';
	$view_help = 'View all Requests';
}

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
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
<!--	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	--> 
    
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">
    
    <script src="/Common/js/Spry/includes/xpath.js" type="text/javascript"></script>
    <script src="/Common/js/Spry/includes/SpryData.js" type="text/javascript"></script>
<!--    <script src="/Common/js/Spry/includes/SpryEffects.js" type="text/javascript"></script>-->
<!--    <script src="/Common/js/Spry/widgets/accordion/SpryAccordion.js" type="text/javascript"></script>-->
	<script type="application/javascript">
		//var searchQuery="?<?= 'action=' . $_GET[action] . '&view=' . $_GET[view] . '&status=' . $_GET[status]; ?>";
		var dsRequisition = new Spry.Data.XMLDataSet("requisitions_xml.php?<?= $_SERVER['QUERY_STRING']; ?>", "requisitions/requisition", {sortOnLoad:"id",sortOrderOnLoad:"ascending"});
		dsRequisition.setColumnType("hire", "date");
		dsRequisition.setColumnType("requester/@date", "date");
		dsRequisition.setColumnType("duedate", "date");
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" -->
                  <?php include('../include/menu/main_left.php'); ?>
                <!-- InstanceEndEditable --></td>
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
	<?php 
	  if ($_SESSION['request_access'] == '3') {
		echo "&nbsp;<img src=\"/Common/images/adminAction.gif\" onClick=\"new Effect.toggle('adminPanel', 'slide')\">";
		
		include('../Administration/include/detail.php');
	  } 
	?>	
	  <table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
      <tbody>
        <tr>
          <td height="2"></td>
        </tr>
        <tr>
          <td><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
              <tbody>
                <tr>
                  <td>			  
				  <br>
                    <?php
					/* Dont display column headers and totals if no requests */
					if ($num_rows == 0) {
					?>
				    <table border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="40" align="center" valign="top" class="DarkHeader">No Requisitions Found</td>
                      </tr>
                      <tr>
                        <td height="30" align="center" class="DarkHeaderSub">Click <a href="<?= $view_all; ?>"><img src="<?= $view_gif; ?>" border="0" align="absmiddle"></a></td>
                      </tr>
                      <tr>
                        <td height="30" align="center" valign="middle" class="DarkHeaderSubSub">or change the</td>
                      </tr>
                      <tr>
                        <td height="30" align="center" nowrap class="DarkHeaderSub"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" name="Form" id="Form" style="margin: 0">
                          Access View:
                          <input name="action" type="hidden" id="action" value="<?= $_GET['action']; ?>">
						  <input name="view" type="hidden" id="view" value="<?= $_GET['view']; ?>">
						  <select name="access" id="access" onChange="this.form.submit();">
							<option value="0" <?php if ($_GET['access'] == '0') { echo "selected"; } ?>>Requester</option>
                            <option value="5" <?php if ($_GET['access'] == '5') { echo "selected"; } ?>>Controller</option>
							<option value="1" <?php if ($_GET['access'] == '1') { echo "selected"; } ?>>Approver 1</option>
							<option value="2" <?php if ($_GET['access'] == '2') { echo "selected"; } ?>>Approver 2</option>
							<option value="3" <?php if ($_GET['access'] == '3') { echo "selected"; } ?>>Approver 3</option>
							<option value="4" <?php if ($_GET['access'] == '4') { echo "selected"; } ?>>Approver 4</option>
						  </select>
                        </form></td>
                      </tr>
                      <tr>
                        <td height="30" align="center" nowrap class="DarkHeaderSub"><form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" name="Form2" id="Form2" style="margin: 0">
                          <div align="right">Status View:
                            <input name="action" type="hidden" id="action" value="<?= $_GET['action']; ?>">
                              <input name="view" type="hidden" id="view" value="<?= $_GET['view']; ?>">
                              <select name="status" id="status" onChange="this.form.submit();">
                                <option value="All" <?php if ($_GET['status'] == 'All') { echo "selected"; } ?>>All</option>
                                <option value="N" <?php if ($_GET['status'] == 'N') { echo "selected"; } ?>>New</option>
                                <option value="A" <?php if ($_GET['status'] == 'A') { echo "selected"; } ?>>Approved</option>
                                <option value="O" <?php if ($_GET['status'] == 'O') { echo "selected"; } ?>>Vendor Kickoff</option>
                                <option value="R" <?php if ($_GET['status'] == 'R') { echo "selected"; } ?>>Received</option>
                                <option value="X" <?php if ($_GET['status'] == 'X') { echo "selected"; } ?>>Not Approved</option>
                                <option value="C" <?php if ($_GET['status'] == 'C') { echo "selected"; } ?>>Canceled</option>
                              </select>
                          </div>
                        </form></td>
                      </tr>
                    </table>
				    <?php } else { ?>
					 <table border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                          <td height="25">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">

                              <tr>
                                <td valign="bottom">
									<?php
									  if ($_SESSION['request_role'] == 'purchasing') {
										$date1=(array_key_exists('date1', $_POST)) ? $_POST['date1'] : date("Y-m-d");
								
										$submitted = $dbh->getRow("SELECT COUNT(id) as today FROM PO WHERE reqDate='".$date1."'");
										$issued = $dbh->getRow("SELECT COUNT(id) as today FROM Authorization WHERE issuerDate>'".$date1." 00:00:00' AND issuerDate<'".$date1." 23:59:59'");
									?>
									  <form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>" style="margin: 0">
										<table width="190"  border="0" cellpadding="0" cellspacing="0">
										  <tr>
										    <td rowspan="2">&nbsp;</td>
											<td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
											  <tr>
												<td width="10" height="10" valign="top"><img src="../images/menu_top_left.gif" width="10" height="10"></td>
												  <td align="center" onClick="$('stats').toggle();" class="ColorHeaderSubSub">Purchasing Stats</td>
												  <td width="10" height="10" valign="top"><img src="../images/menu_top_right.gif" width="10" height="10"></td>
											  </tr>
											  </table></td>
										  </tr>
										  <tr id="stats" style="display:none">
										    <td class="BGAccentVeryDarkBorder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
											  <tr>
												<td width="75%">Requisitions:</td>
												<td><strong><?= $submitted['today']; ?></strong></td>
											  </tr>
											  
											  <tr>
												<td>Vendor Kickoff:</td>
												<td><strong><?= $issued['today']; ?></strong></td>
											  </tr>
											  <?php if (!array_key_exists('date1', $_POST)) { ?>
											  
											  <?php } ?>
											  </table></td>
										  </tr>
										</table>
									  </form>
									  <?php } ?>
								</td>
                                <td valign="bottom" class="padding">
								  <?php if ($_GET['action'] == 'my') { ?>
                                  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" name="Form" id="Form" style="margin: 0">
                                    <div align="right"> Access View:
								    <input name="action" type="hidden" id="action" value="<?= $_GET['action']; ?>">
								    <input name="view" type="hidden" id="view" value="<?= $_GET['view']; ?>">									
                                      <select name="access" id="access" onChange="this.form.submit();">
                                        <option value="0" <?php if ($_GET['access'] == '0') { echo "selected"; } ?>>Requester</option>
                                        <option value="5" <?php if ($_GET['access'] == '5') { echo "selected"; } ?>>Controller</option>
                                        <option value="1" <?php if ($_GET['access'] == '1') { echo "selected"; } ?>>Approver 1</option>
                                        <option value="2" <?php if ($_GET['access'] == '2') { echo "selected"; } ?>>Approver 2</option>
                                        <option value="3" <?php if ($_GET['access'] == '3') { echo "selected"; } ?>>Approver 3</option>
                                        <option value="4" <?php if ($_GET['access'] == '4') { echo "selected"; } ?>>Approver 4</option>
                                      </select>
                                    </div>
                                  </form>
                                  <?php } ?>
								<?php if ($_GET['action'] != 'my') { ?>
								<form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" name="Form" id="Form" style="margin: 0">
								  <div align="right">Status View:								    
								    <input name="action" type="hidden" id="action" value="<?= $_GET['action']; ?>">
								    <input name="view" type="hidden" id="view" value="<?= $_GET['view']; ?>">
								    <select name="status" id="status" onChange="this.form.submit();">
                                      <option value="All" <?php if ($_GET['status'] == 'All') { echo "selected"; } ?>>All</option>
                                      <option value="N" <?php if ($_GET['status'] == 'N') { echo "selected"; } ?>>New</option>
                                      <option value="A" <?php if ($_GET['status'] == 'A') { echo "selected"; } ?>>Approved</option>
                                      <option value="O" <?php if ($_GET['status'] == 'O') { echo "selected"; } ?>>Vendor Kickoff</option>
                                      <option value="R" <?php if ($_GET['status'] == 'R') { echo "selected"; } ?>>Received</option>
                                      <option value="X" <?php if ($_GET['status'] == 'X') { echo "selected"; } ?>>Not Approved</option>
                                      <option value="C" <?php if ($_GET['status'] == 'C') { echo "selected"; } ?>>Canceled</option>
                                    </select>
							      </div>
								</form>
								<?php } ?></td>
                                <td width="110" align="right" valign="bottom" class="padding"><a href="<?= $view_all; ?>" <?php help('', $view_help, 'default'); ?>><img src="<?= $view_gif; ?>" border="0"></a></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td class="BGAccentVeryDark"><div align="left">
                              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<?php if ($_GET['action'] == "my") { echo "My "; } ?>
                                  Purchase Requisitions... </td>
                                  <td>&nbsp;</td>
                                </tr>
                              </table>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="BGAccentVeryDarkBorder">
                          <div spry:region="dsRequisition" class="SpryHiddenRegion">
						  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>
                              <table border="0" cellpadding="0" cellspacing="0" class="tableBorder" id="employees">
                                <tr id="columnHeadings">
                                    <th class="cellLeftHeadings" scope="col" spry:sort="eid" spry:choose="spry:choose">
                                        <span spry:when="'{ds_SortColumn}' == 'eid' && '{ds_SortOrder}' == 'ascending'">EID<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
                                        <span spry:when="'{ds_SortColumn}' == 'eid' && '{ds_SortOrder}' == 'descending'">EID<img src="/Common/images/descending.gif" align="absmiddle" /></span>
                                        <span spry:default="spry:default">EID</span>
                                    </th>
                                    <th class="cellLeftHeadings" scope="col" spry:sort="fst" spry:choose="spry:choose">
                                        <span spry:when="'{ds_SortColumn}' == 'fst' && '{ds_SortOrder}' == 'ascending'">First Name<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
                                        <span spry:when="'{ds_SortColumn}' == 'fst' && '{ds_SortOrder}' == 'descending'">First Name<img src="/Common/images/descending.gif" align="absmiddle" /></span>
                                        <span spry:default="spry:default">First Name</span>
                                    </th>
                                    <th class="cellLeftHeadings" scope="col" spry:sort="lst" spry:choose="spry:choose">
                                        <span spry:when="'{ds_SortColumn}' == 'lst' && '{ds_SortOrder}' == 'ascending'">Last Name<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
                                        <span spry:when="'{ds_SortColumn}' == 'lst' && '{ds_SortOrder}' == 'descending'">Last Name<img src="/Common/images/descending.gif" align="absmiddle" /></span>
                                        <span spry:default="spry:default">Last Name</span>
                                    </th>
                                    <th class="cellLeftHeadings" scope="col" spry:sort="dept" spry:choose="spry:choose">
                                        <span spry:when="'{ds_SortColumn}' == 'dept' && '{ds_SortOrder}' == 'ascending'">Department<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
                                        <span spry:when="'{ds_SortColumn}' == 'dept' && '{ds_SortOrder}' == 'descending'">Department<img src="/Common/images/descending.gif" align="absmiddle" /></span>
                                        <span spry:default="spry:default">Department</span>
                                    </th>
                                    <th class="cellLeftHeadings" scope="col" spry:sort="location" spry:choose="spry:choose">
                                        <span spry:when="'{ds_SortColumn}' == 'location' && '{ds_SortOrder}' == 'ascending'">Location<img src="/Common/images/ascending.gif" align="absmiddle" /></span>
                                        <span spry:when="'{ds_SortColumn}' == 'location' && '{ds_SortOrder}' == 'descending'">Location<img src="/Common/images/descending.gif" align="absmiddle" /></span>
                                        <span spry:default="spry:default">Location</span>
                                    </th>
                                </tr>
                                <tr>
                                  <td colspan="5"><div class="spryReady" spry:state="ready" spry:if="{ds_RowCount} == 0">No requisitions found.</div>
                                      <div class="spryLoading" spry:state="loading">Loading requisitions...</div>
                                    <div class="spryFailed" spry:state="error">Failed to load requisitions...</div></td>
                                </tr>
                                <tr  spry:repeat="dsRequisition" spry:even="evenRow" spry:odd="oddRow" spry:setrow="dsRequisition" spry:hover="rowHover" spry:select="rowSelected" spry:selected="selected">
                                  <td spry:choose="spry:choose"><div spry:when="'{@status}' == 'Current'">{@id} </div>
                                      <div spry:when="'{@status}' == 'Inactive'" style="color:#FF0000">{@id} </div></td>
                                  <td class="cellLeft">{fst}</td>
                                  <td class="cellLeft">{lst}</td>
                                  <td class="cellLeft" spry:choose="spry:choose"><div spry:when="'{dept}'.length != 0">({dept/@id}) {dept}</div></td>
                                  <td class="cellLeft" spry:choose="spry:choose"><div spry:when="'{location}'.length != 0">({location/@conbr}) {location}</div></td>
                                </tr>
                              </table></td>
                            </tr>
                            <tr>
                              <td class="BGAccentDark"><table  border="0" align="right" cellpadding="0" cellspacing="0">
                               <tr>
                                  <td class="padding"><strong>Total:</strong></td>
                                  <td class="padding">&nbsp;$<?= number_format($itemsTotal, 2, '.', ','); ?></td>
                                </tr>
							  </table></td>
                            </tr>
                          </table>
                          </div>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;<span class="GlobalButtonTextDisabled">{ds_RowCount} Requisitions</span></td>
                        </tr>
                    </table>
					  <?php } // End num_row if ?>
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
		  <div align="center"><!-- InstanceBeginEditable name="footer" --><?php if ($_SESSION['request_role'] == 'purchasing') { ?><a href="<?= $default['URL_HOME']; ?>/Help/chat.php" target="chat" onClick="window.open(this.href,this.target,'width=250,height=400'); return false;" id="meebo"><img src="/Common/images/meebo.gif" width="18" height="20" border="0" align="absmiddle">Company Chat</a><?php } ?><!-- InstanceEndEditable --></div>
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