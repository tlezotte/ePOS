<?php
/**
 * Request System
 *
 * index.php main page for references.
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
                              <td nowrap><a href="../RSS/index.php" class="off">RSS</a></td>							  					  
							  <td width="30" valign="middle" nowrap><div align="center"><img src="../../images/dot.gif" width="10" height="10"></div></td>
                              <td nowrap><a href="index.php" class="off">References</a></td>							
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
                      <td class="mainsection"><a href="Company.php" class="dark" <?php help('', 'View References', 'default'); ?>>Your Company  </a>&nbsp;&nbsp;&nbsp;<a href="Company.php?output=save" <?php help('', 'Save a Copy', 'default'); ?>><img src="../../images/filesaveas.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
                    </tr>
                </table>
				  <!--
		          <table width="100%" border="0" cellspacing="0" cellpadding="1" rules="rows">
                    <tr>
                      <td class="mainsection"><a href="../../preview/index.php" class="dark">Modify a Purchase Order </a></td>
                    </tr>
                  </table>
				  -->
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
        </table></td>
        <td><table  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center"><br>
                <br>
                <br>
                <br>
                <span class="DarkHeaderSubSub"><?= $default['title0']; ?></span><br>
                <span class="DarkHeader"><?= $default['title1']; ?>'s<br>
      References Section</span><br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <span class="NavBarInactiveLink">Choose from the menu on the left side of the screen <br>
    </span></td>
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