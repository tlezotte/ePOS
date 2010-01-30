<?php
/**
 * Request System
 *
 * releasenotes.php list of features added.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
  * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 */
 
/**
 * - Start Page Loading Timer
 */
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('../../include/config.php'); 

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
  <link type="text/css" href="../../default.css" charset="UTF-8" rel="stylesheet">
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
          <td valign="top"><a href="../../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" -->
            <table border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="17">&nbsp;</td>
              </tr>
            </table>
          <!-- InstanceEndEditable --></td>

          <td>
          </td>
        </tr>

        <tr>
          <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
            <tbody>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghtl.gif" width="4"></td>
                <td colspan="4"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td class="BGColorDark" valign="top" rowspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ght.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghtr.gif" width="4"></td>
              </tr>
              <tr>
                <td class="BGGrayLight" rowspan="3"></td>
                <td class="BGGrayMedium" rowspan="3"></td>
                <td class="BGGrayDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"></td>
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_release.lbi" -->
                    <table cellspacing="0" cellpadding="0" summary="" border="0">
                      <tr>
                        <td>&nbsp;</td>
                        <td><table cellspacing="0" cellpadding="0" summary="" border="0">
                            <tr>
                              <td nowrap><a href="../releasenotes.php" class="off">Home</a></td>
                              <td width="30" valign="middle" nowrap><div align="center"><img src="../../images/dot.gif" width="10" height="10"></div></td>	
							  <!--						
                              <td nowrap><a href="../Help/CER/releasenotes.php" class="off">Capital Expense</a></td>
                              <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
							  -->
                              <td nowrap><a href="releasenotes.php" class="off">Purchase Order</a></td>
                              <!--
							  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
                              <td nowrap><a href="../Help/Administration/releasenotes.php" class="off">Administraiton</a></td>
							  -->				
                              <td width="30" valign="middle" nowrap><div align="center"><img src="../../images/dot.gif" width="10" height="10"></div></td>
                              <td nowrap><a href="../futureReleases.php" class="off">Future Releases</a></td>  
                            </tr>
                        </table></td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>
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
                <td valign="top"><img height="20" alt="" src="../../images/c-ghct.gif" width="25"></td>
                <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td height="4"></td>
                      </tr>
                    </tbody>
                </table></td>
                <td valign="top" colspan="4"><img height="20" alt="" src="../../images/c-ghbr.gif" width="4"></td>
              </tr>
              <tr>
                <td width="4" colspan="4" height="4"><img height="4" alt="" src="../../images/c-ghbl.gif" width="4"></td>
                <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../../images/c-ghb.gif" border="0">
                    <tbody>
                      <tr>
                        <td></td>
                      </tr>
                    </tbody>
                </table></td>
                <td><img height="4" alt="" src="../../images/c-ghcb.gif" width="3"></td>
                <td colspan="7"></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
  </table>
  </div>
    <!-- InstanceBeginEditable name="main" --><br>
    <br>
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="BGAccentDarkBorder"><table width="100%"  border="0" cellspacing="1">
            <tr>
              <td height="30" class="BGAccentVeryDark">&nbsp;&nbsp;V3.5</td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Graphs and Reports (beta) </strong></td>
                  <td><div align="right">Mar 20, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> Generates Graphs and Reports for Company and Supplier.</blockquote></td>
            </tr>
            <tr>
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Check Request </strong></td>
                  <td><div align="right">Mar 09, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><blockquote> Generates a check request form.</blockquote></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Reference Sheet</strong></td>
                  <td><div align="right">Mar 09, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> PDF file listing all New Company Holdings references.</blockquote></td>
            </tr>
            <tr>
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Status Icon </strong></td>
                  <td><div align="right">Mar 09, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><blockquote> Displays an icon, in the List View, of where the Request currently is located.</blockquote></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Auto-Print for Issuer </strong></td>
                  <td><div align="right">Feb 12, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> Allows Issuer to automatically print a hard copy of the Request when entering the PO number.</blockquote></td>
            </tr>
            <tr>
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Track Shipments</strong></td>
                  <td><div align="right">Feb 10, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><blockquote> A quick access point for tracking shipments and deliveries from FedEX, UPS, USPS and DHL.</blockquote></td>
            </tr>			
            <tr>
              <td height="30" class="BGAccentVeryDark">&nbsp;&nbsp;V3.1</td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Edit Request</strong></td>
                  <td><div align="right">Feb 10, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> The Purchase Requester can edit PO number, CER number, Due date, Order date and Received date.</blockquote></td>
            </tr>
            <tr>
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>Cancel Request </strong></td>
                  <td><div align="right">Feb 10, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td><blockquote> The Purchase Requester can Cancel a Request after PO number was issued.</blockquote></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td><strong>List Status </strong></td>
                  <td><div align="right">Feb 10, 2005</div></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> All users can list Requests based on their current status.</blockquote></td>
            </tr>
            <tr>
              <td height="30" class="BGAccentVeryDark">&nbsp;&nbsp;V3.0</td>
            </tr>
            <tr bgcolor="DFDFBF">
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td><strong>Interface</strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr bgcolor="DFDFBF">
              <td bgcolor="DFDFBF"><blockquote> The Purchase Request System has a new cleaner and easier to use interface.</blockquote></td>
            </tr>
            <tr class="odd">
              <td>
                <table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>Create Wizard </strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr class="odd">
              <td><blockquote> The Purchase Request System will now set you through creating a Purchase Order Request.</blockquote></td>
            </tr>
            <tr bgcolor="DFDFBF">
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>Store Request</strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr bgcolor="DFDFBF">
              <td><blockquote> The Purchase Request System will store your Request until you complete the Request or exit your web browser. This will allow you to gather more information and complete the Request later. </blockquote></td>
            </tr>
            <tr>
              <td><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>Edit Request </strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><blockquote> The Purchase Request System now lets the Requester edit any area of the Request, even the quote attachment. </blockquote></td>
            </tr>
            <tr bgcolor="DFDFBF">
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>Increased Items</strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr bgcolor="DFDFBF">
              <td><blockquote> The Purchase Request System allows up to 30 Items per Request. When printing a hard copy multiple Purchase Orders will be printed with the same Purchase Order number. </blockquote></td>
            </tr>
            <tr>
              <td>
                <table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>My List</strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><blockquote> The My List option displays your open Requests or all your Requests. </blockquote></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>My Suppliers</strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> When creating a new Request only  Supplier you have used in the past will display. This will help  you to create your Request faster. </blockquote></td>
            </tr>
            <tr>
              <td>
                <table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>Video Help</strong></td>
                    <td><div align="right">Dec 14, 2004</div></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><blockquote> The Purchase Request System has video help to show you step by step on how to use the system. </blockquote></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF">
                <table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><strong>RSS Feed</strong></td>
                    <td><div align="right">Dec 14, 2004 </div></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td bgcolor="DFDFBF"><blockquote> The Purchase Request System offers a technology called RSS. This feature will allow you to see all transaction on the Request System. <a href="../rss/overview.php">[more]</a></blockquote></td>
            </tr>
        </table></td>
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
                <td width="50%"><div id="noPrint" align="right"><!-- InstanceBeginEditable name="version" --><!-- InstanceEndEditable --></div></td>
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