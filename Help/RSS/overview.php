<?php
/**
 * Request System
 *
 * overview.php explains features of RSS.
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

/* Update Summary */
Summary($dbh, 'RSS: Overview', $_SESSION['eid']);

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
	<SCRIPT LANGUAGE="JavaScript">

	function ClipBoard()
	{
		holdtext.innerText = copytext.innerText;
		Copied = holdtext.createTextRange();
		Copied.execCommand("Copy");
	}
	
	</SCRIPT>
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
  <!-- InstanceBeginEditable name="head" --> 
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_help.lbi" -->
                    <table cellspacing="0" cellpadding="0" summary="" border="0">
                      <tr>
                        <td>&nbsp;</td>
                        <td><table cellspacing="0" cellpadding="0" summary="" border="0">
                            <tr>
                              <td nowrap><a href="../index.php" class="off">Home</a></td>
                              <td width="30" valign="middle" nowrap><div align="center"><img src="../../images/dot.gif" width="10" height="10"></div></td>	
							  <!--						
                              <td nowrap><a href="../Help/CER/index.php" class="off">Capital Expense</a></td>
                              <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
							  -->
                              <td nowrap><a href="../PO/index.php" class="off">Purchase Order</a></td>
                              <!--
							  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
                              <td nowrap><a href="../Help/Administration/index.php" class="off">Administraiton</a></td>	
                              -->
							  <td width="30" valign="middle" nowrap><div align="center"><img src="../../images/dot.gif" width="10" height="10"></div></td>
                              <td nowrap><a href="index.php" class="off">RSS</a></td>							  					  
							  <td width="30" valign="middle" nowrap><div align="center"><img src="../../images/dot.gif" width="10" height="10"></div></td>
                              <td nowrap><a href="../References/index.php" class="off">References</a></td>							
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
    <!-- InstanceBeginEditable name="main" -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="200" valign="top"><table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
          <tbody>
            <tr>
              <td valign="top" width="13" background="../../images/asyltlb.gif"><img height="20" alt="" src="../../images/t.gif" width="13" border="0"></td>
              <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../../images/asybase.gif" width="145" border="0"> <br>
                  <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="overview.php" class="dark">Overview</a></td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="SharpReader.php" class="dark">Install SharpReader </a></td>
                    </tr>
                  </table>
                  <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="Thunderbird.php" class="dark">Install Thunderbird </a></td>
                    </tr>
                  </table>
                  <!--
                  <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="CreatePurchaseOrder.swf" class="dark">Create a Purchase Order </a></td>
                    </tr>
                  </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="#feeds" class="dark">Edit a Purchase Order</a> </td>
                    </tr>
                  </table>--></td>
              <td valign="top" width="22" background="../../images/asyltrb.gif"><img height="20" alt="" src="../../images/t.gif" width="22" border="0"></td>
            </tr>
            <tr>
              <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../../images/asyltb.gif" width="200" border="0"></td>
            </tr>
          </tbody>
        </table>
        </td>
        <td><table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center"><p><span class="NavBarInactiveLink"><span class="DarkHeaderSubSub"><?= $default['title0']; ?></span><br>
                      <span class="DarkHeader"><?= $default['title1']; ?>'s</span><br>
                      <span class="DarkHeader">RSS 2.0 Feed</span><br>
            </span></p></td>
          </tr>
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="229"><img src="../../images/r_1.gif" width="229" height="88"></td>
                  <td background="../../images/r_2.gif">&nbsp;</td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center" bgcolor="#f2f2f2"><p><span class="g4"><b>R</b>eally <b>S</b>imple <b>S</b>yndication (RSS)</span> can be understood as a web syndication protocol that is primarily used by news websites and weblogs. RSS allows a web developer to publish content on their website in a format that a computer program can easily understand and digest. This allows users to easily repackage the content on their own websites or blogs, or privately on their own computers.</p>
                <p>RSS simply repackages the content as a list of data items, such as the date of a news story, a summary of the story and a link to it. A program known as an RSS aggregator or feed reader can then check RSS-enabled webpages for the user, and display any updated articles that it finds. This is more convenient than having the user repeatedly visit their favorite news websites, because it makes sure that the reader only sees material that they haven't seen before. Web-based RSS aggregators are also available, offering the user an alternative to using dedicated software, and making the user's feeds available on any computer with Web access.</p>
                <p>Below, Company <?= $default['title1']; ?> offers several RSS feeds with headlines, descriptions and links back to the Capital Expenses and Purchase Orders for the full story.<br>
                    <br>
              </p></td>
          </tr>
          <tr>
            <td height="30"><a name="feeds"></a></td>
          </tr>
          <tr>
            <td height="30" class="BGAccentDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="25" colspan="2" class="BGAccentDark">&nbsp;&nbsp;<strong>RSS Feeds </strong></td>
                </tr>
                <tr>
                  <td width="30" height="25"><div align="center"><a href="<?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?>" <?php help('', 'Copy RSS URL to Clipboard for Mozilla based browsers (Firefox) Right Click on the icon and select <b>Copy Link Location</b>', 'default'); ?>><img src="../../images/livemarks16.gif" width="16" height="16" border="0" align="absmiddle"></a></div></td>
                  <td nowrap><a href="javascript:void(0);" <?php help('', "Click text to copy RSS URL to Clipboard for Microsoft Internet Explorer only", 'default'); ?>><BUTTON class="rss" onClick="ClipBoard();">Purchase Orders</BUTTON></a>
                  <span id="copytext" style="visibility:hidden;"><?= $default['URL_HOME']; ?>/PO/<?= $default['rss_file']; ?></span><TEXTAREA ID="holdtext" STYLE="display:none;"></TEXTAREA></td>
                </tr>
                <tr>
                  <td height="25">&nbsp;</td>
                  <td><b>Description:</b> A feed of the last <?= $default['rss_items']; ?> submitted and approved Purchase Order Requests. This feed is updated when ever a transaction takes place.</td>
                </tr>
                <tr>
                  <td width="30" height="25"><div align="center"><a href="<?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?>" <?php help('', 'Copy RSS URL to Clipboard for Mozilla based browsers (Firefox) Right Click on the icon and select <b>Copy Link Location</b>', 'default'); ?>><img src="../../images/livemarks16.gif" width="16" height="16" border="0" align="absmiddle"></a></div></td>
                  <td nowrap><a href="javascript:void(0);" <?php help('', "Click text to copy RSS URL to Clipboard for Microsoft Internet Explorer only", 'default'); ?>><BUTTON class="rss" onClick="ClipBoard();">Capital Expenses</BUTTON></a>
                  <span id="copytext" style="visibility:hidden;"><?= $default['URL_HOME']; ?>/CER/<?= $default['rss_file']; ?></span><TEXTAREA ID="holdtext" STYLE="display:none;"></TEXTAREA></td>
                </tr>
                <tr>
                  <td height="25">&nbsp;</td>
                  <td><b>Description:</b> A feed of the last <?= $default['rss_items']; ?> submitted and approved Capital Expense Requests. This feed is updated when ever a transaction takes place.</td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="30"><a name="software"></a></td>
          </tr>
          <tr>
            <td height="30" class="BGAccentDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="25" class="BGAccentDark">&nbsp;<strong>&nbsp;RSS Software</strong> </td>
                </tr>
                <tr>
                  <td height="25" class="padding"><a href="http://intranet.Company.com/modules.php?name=Downloads&d_op=getit&lid=26" class="dark">Sharp Reader</a> - SharpReader is an RSS/Atom Aggregator for Windows</td>
                </tr>
                <tr>
                  <td height="25" class="padding"><a href="http://intranet.Company.com/modules.php?name=Downloads&d_op=getit&lid=30" class="dark">RSS Bandit</a> - RSS Bandit is an RSS/Atom Aggregator for Windows</td>
                </tr>
				<!--
                <tr>
                  <td height="25" class="padding"><a href="http://intranet.Company.com/modules.php?name=Downloads&d_op=getit&lid=27" class="dark">Blog Navigator</a> - Blog Navigator is a program that makes it easy to read RSS feed from the Internet.</td>
                </tr>-->
                <tr>
                  <td height="25" class="padding"><a href="http://intranet.Company.com/modules.php?name=Downloads&d_op=getit&lid=20" class="dark">Thunderbird</a> - is Mozilla's next generation e-mail client with RSS Integration.</td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="20">&nbsp;</td>
          </tr>
          <tr>
            <td align="center" bgcolor="#CC9966" class="padding">Prior to running SharpReader or RSS Bandit, you will need to install the .NET Framework, version 1.1. If you do not currently have the .NET Framework installed, you can get it at <a href="http://windowsupdate.microsoft.com" target="_blank" class="black"><strong>Windows Update</strong></a>.</td>
          </tr>
        </table></td>
      </tr>
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