<?php
/**
 * Request System
 *
 * list.php displays CERs.
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

/* ------------------ START DATABASE PROCESSING ----------------------- */ 
if ($_POST['stage'] == "three") {
	/* Set form variables as session variables */
	$_SESSION['summary'] = $_POST['summary'];
	$_SESSION['file']  = $_POST['file'];
	
	/* Just to the next page in wizard */
	header("Location: finished.php"); 
}
/* ------------------ END DATABASE PROCESSING ----------------------- */ 

 
/* ------------------ START DATABASE ACCESS ----------------------- */ 
/* SQL for different views of CER list */
if ($_GET['action'] == "my" and $_GET['view'] == "all") {
	$where_clause = "WHERE req like '".$_SESSION['eid']."'";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my");
	$view_gif = '../images/button.php?i=b90.png&l=View Open';
	$view_help = 'View all of My Open Requests';
} elseif ($_GET['view'] == "all") {
	$where_clause = "";
	$view_all = $_SERVER['PHP_SELF'];
	$view_gif = '../images/button.php?i=b90.png&l=View Open';
	$view_help = 'View all Open Requests';
} elseif ($_GET['action'] == "my") {
	$where_clause = "WHERE req like '".$_SESSION['eid']."' AND cer IS NULL";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&view=all");
	$view_gif = '../images/button.php?i=b90.png&l=View All';
	$view_help = 'View all of My Requests';
} else {
	$where_clause = "WHERE cer IS NULL";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?view=all");
	$view_gif = '../images/button.php?i=b90.png&l=View All';
	$view_help = 'View all Requests';
}

/* SQL for CER list */
$cer_sql = $dbh->prepare("SELECT id, cer, purpose, reqDate, req, date1, date2, location, totalCost ".
						"FROM CER ".
						"$where_clause ORDER BY reqDate DESC");
$cer_sth = $dbh->execute($cer_sql);
$num_rows = $cer_sth->numRows();

/* Get Plants and Employees from Stanards database */
$PLANTS = $dbh->getAssoc("SELECT id, name FROM Standards.Plants");
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");								
/* ------------------ END DATABASE ACCESS ----------------------- */


/* ------------------ START FUNCTIONS ----------------------- */
function email($id) {
	echo "mailto:?subject=$default[title1]".
	     "&body=".ucwords(strtolower($EMPLOYEES[$_SESSION[eid]]))." has email you this Capital Expense Request<br><br>".
		 "http://$_SERVER[HTTP_HOST]$default[url_home]/CER/detail.php?id=$id";
}
/* ------------------ END FUNCTIONS ----------------------- */

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
				    <?php
						/* Dont display column headers and totals if no requests */
						if ($num_rows == 0) {
					  ?>
							<div align="center" class="DarkHeaderSubSub">No Requests Found<br>
							  <br>
						    Click <a href="<?= $view_all; ?>"><img src="<?= $view_gif; ?>" border="0" align="absmiddle"></a></div>
					<?php } else { ?>					
					<table border="0" align="center" cellpadding="0" cellspacing="0">
					  <tr><td height="30"><div align="right"><a href="<?= $view_all; ?>" <?php help($view_help, 'default'); ?>><img src="<?= $view_gif; ?>" border="0"></a>&nbsp;&nbsp;</div></td>
					  </tr>
                      <tr>
                        <td class="BGAccentVeryDark"><div align="left">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<?php if ($_GET['action'] == "my") { echo "My "; } ?>Capital Expenditure Requests... </td>
                                <td>&nbsp;</td>
                              </tr>
                          </table>
                        </div></td>
                      </tr>
                      <tr>
                        <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td valign="top" class="BGAccentDarkBorder"><table width="100%"  border="0">
                                <tr>
                                  <td height="25" class="BGAccentDark">&nbsp;</td>
								  <?php if ($_GET['view'] == "all") { ?>
								    <td class="BGAccentDark"><strong>CER</strong></td>
								  <?php } ?>									  
                                  <td class="BGAccentDark"><strong>&nbsp;Purpose</strong></td>
								  <?php if ($_GET['action'] != "my") { ?>
								    <td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
								  <?php } ?>									  
                                  <td class="BGAccentDark"><strong>&nbsp;Requested<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle">&nbsp;</strong></td>
                                  <td class="BGAccentDark"><strong>&nbsp;Location</strong></td>
                                <td class="BGAccentDark">&nbsp;<strong>Total</strong></td>
                                </tr>
							    <?php
								  /* Loop through list of CERs */
								  while($cer_sth->fetchInto($CER)) {
								    /* Line counter for alternating line colors */
								  	$counter++;
		    						$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
								  ?>
                                <tr <?php pointer($row_color); ?>>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $CER[id]; ?>" <?php help('', 'Get a detailed view', 'default'); ?>><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a><!--&nbsp;<a href="<?php email($CER[id]); ?>" <?php help('', 'Email this CER to an employee', 'default'); ?>><img src="../images/email.gif" width="17" height="16" border="0" align="absmiddle"></a>-->&nbsp;<a href="print.php?id=<?= $CER[id]; ?>" <?php help('', 'Print a hard copy', 'default'); ?>><img src="../images/printer.gif" width="15" height="20" border="0" align="absmiddle"></a></td>
								  <?php if ($_GET['view'] == "all") { ?>
								  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $CER[cer]; ?></td>
								  <?php } ?>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower(substr(stripslashes($CER[purpose]), 0, 40))); ?>
                                      <?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?></td>
								  <?php if ($_GET['action'] != "my") { ?>
								    <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$CER[req]])); ?></td>
								  <?php } ?>										  
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="javascript:void(0);" class="black" <?php help("Project Start:&nbsp;$CER[date1]<br>Project End:&nbsp;&nbsp;&nbsp;$CER[date2]", 'default'); ?>><?= $CER[reqDate]; ?></a></td>
                                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?php if ($CER[location] == "100") { echo "All Plants"; } else { echo $PLANTS[$CER[location]]; } ?></td>
                                <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="2%">$</td>
                                        <td width="98%"><div align="right"><?= number_format($CER[totalCost], 2, '.', ','); ?></div></td>
                                      </tr>
                                  </table></td>
                                </tr>
								<?php $itemsTotal += $CER[totalCost]; ?>
								<?php } ?>
								  </table></td>
                            </tr>
                            <tr>
                              <td class="BGAccentDark"><table  border="0" align="right" cellpadding="0" cellspacing="0">
                               <tr>
                                  <td class="padding"><strong>Total:</strong></td>
                                  <td class="padding">&nbsp;$ <?= number_format($itemsTotal, 2, '.', ','); ?></td>
                                </tr>
							  </table></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                        <tr>
                          <td>&nbsp;<span class="GlobalButtonTextDisabled"><?= $num_rows ?> Requests</span></td>
                      </tr>
                    </table>
					  <?php } // End num_row if ?>
                  <br>                  </td></tr>
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