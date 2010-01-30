<?php
/**
 * Request System
 *
 * suppliers.php list all suppliers.
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
include_once('../../include/Timer.php');
$starttime = StartLoadTimer();
/**
 * - Set debug mode
 */
$debug_page = false;
include_once('../debug/header.php');

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php');
/**
 * - Check User Access
 */
require_once('../../security/check_user.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 


/* ------------------ START VARIABLES ----------------------- */
/* --- Pagination Variables --- */
$page_order = (array_key_exists('o', $_GET)) ? $_GET['o'] : "name";									// Order By field
$page_direction = (array_key_exists('d', $_GET)) ? $_GET['d'] : "ASC";								// Order By field direction
$page_rows = $dbh->getRow("SELECT COUNT(BTVEND) AS total 
						   FROM Standards.Vendor 
						   WHERE BTSTAT = 'A'
						     AND BTVEND NOT LIKE '%R'
							 AND BTCLAS NOT LIKE 'I'");			// Get total number of active Projects
$page_start = (array_key_exists('s', $_GET)) ? $_GET['s'] : "0";									// Page start row
$viewable_rows = ($viewable_rows > $page_rows['total']) ? $page_rows['total'] : $viewable_rows;		// Checks rows with default viewable_rows
$page_next = $page_start + $viewable_rows;															// Set next page
$page_previous = $page_start - $viewable_rows;														// Set previous page
$page_last = $page_rows['total'] - $viewable_rows;													// Set last page
$letter = (array_key_exists('letter', $_GET)) ? $_GET['letter'].'%' : '%';
$limit = (!array_key_exists('display', $_GET)) ? "LIMIT $page_start, $viewable_rows" : $blank;
/* ------------------ END VARIABLES ----------------------- */

$suppliers_query = "SELECT BTVEND AS id, BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, BTCNTC AS country
					FROM Standards.Vendor
					WHERE BTSTAT='A'
					  AND BTNAME LIKE '$letter'
					  AND BTVEND NOT LIKE '%R'
					  AND BTCLAS NOT LIKE 'I'
					ORDER BY $page_order $page_direction
					$limit";
$suppliers_sql = $dbh->prepare($suppliers_query);								
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
  <link type="text/css" href="../../default.css" charset="UTF-8" rel="stylesheet">
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
    <script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
    </script>
  <!-- InstanceEndEditable -->
  <?php if ($ONLOAD_OPTIONS) { ?>
  <script language="javascript">
	AJS.AEV(window, "load", <?= $ONLOAD_OPTIONS; ?>);
  </script>
  <?php } ?>
  </head>

  <body class="yui-skin-sam" onLoad="MM_preloadImages('../../images/previous_button_on.gif','../../images/next_button_on.gif')">  
    <img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" />
	<div id="noPrint">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
      <tbody>
        <tr>
          <td valign="top"><a href="../../home.php" title="<?= $default['title1']; ?> Home"><img name="Company" src="/Common/images/Company.gif" width="300" height="50" border="0"></a></td>
          <td align="right" valign="top">
          <!-- InstanceBeginEditable name="topRightMenu" --><!-- #BeginLibraryItem "/Library/help.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
<tr>
  <td width="30"><a href="../../Common/calculator.php" onClick="window.open(this.href,this.target,'width=281,height=270'); return false;" <?php help('', 'Calculator', 'default'); ?>><img src="../../images/xcalc.png" width="16" height="14" border="0"></a></td>
  <td><a href="../../Help/index.php" rel="gb_page_fs[]"><img src="../../images/help.gif" width="18" height="18" border="0" align="absmiddle"></a></td>
  <td class="DarkHeaderSubSub">&nbsp;<a href="../../Help/index.php" rel="gb_page_fs[]" class="dark">Help</a></td>
</tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>
        </tr>

        <tr>
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><?php include('../../include/menu/main_right.php'); ?><!-- InstanceEndEditable --></td>

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
		  <td nowrap><a href="../users.php" class="off"> Users </a></td>
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="../settings.php" class="off"> Settings </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>		  
		  <td nowrap><a href="index.php" class="off"> Databases </a></td>			  					  
		  <td width="20" valign="middle" nowrap><div align="center"><img src="/Common/images/dot.gif" width="10" height="10"></div></td>
		  <td nowrap><a href="../utilities.php" class="off"> Utilities </a></td>			  			  
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
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="200" align="center" valign="top"><table width="190"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="top"><img src="../../images/menu_top_left.gif" width="10" height="10"></td>
                  <td align="center"><span class="ColorHeaderSubSub">Display Vendors </span> </td>
                  <td width="10" height="10" valign="top"><img src="../../images/menu_top_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><table  border="0" align="center" cellpadding="5" cellspacing="0">
                      <tr>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=A" class="dark">A</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=B" class="dark">B</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=C" class="dark">C</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=D" class="dark">D</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=E" class="dark">E</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=F" class="dark">F</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=G" class="dark">G</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=H" class="dark">H</a></td>
                      </tr>
                      <tr>
                        <td align="center"><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=I" class="dark">I</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=J" class="dark">J</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=K" class="dark">K</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=L" class="dark">L</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=M" class="dark">M</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=N" class="dark">N</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=O" class="dark">O</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=P" class="dark">P</a></td>
                      </tr>
                      <tr>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=Q" class="dark">Q</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=R" class="dark">R</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=S" class="dark">S</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=T" class="dark">T</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=U" class="dark">U</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=V" class="dark">V</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=W" class="dark">W</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=X" class="dark">X</a></td>
                      </tr>
                      <tr>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=Y" class="dark">Y</a></td>
                        <td><a href="<?= $_SERVER['../PHP_SELF']; ?>?letter=Z" class="dark">Z</a></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2"><div align="center"><a href="<?= $_SERVER['../PHP_SELF']; ?>?display=all" class="dark"><strong>All</strong></a></div></td>
                      </tr>
                  </table></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td height="10" class="accentVerydark"><table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10" height="10" valign="bottom"><img src="../../images/menu_bottom_left.gif" width="10" height="10"></td>
                  <td><img src="../../images/spacer.gif" width="10" height="10"></td>
                  <td width="10" height="10" valign="bottom"><img src="../../images/menu_bottom_right.gif" width="10" height="10"></td>
                </tr>
            </table></td>
          </tr>
        </table></td>
        <td valign="top"><table  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="25" valign="top"><div align="right">&nbsp;&nbsp;</div></td>
          </tr>
          <tr>
            <td height="30" colspan="2" class="BGAccentVeryDark"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Vendors List...</td>
                  <td width="50%">&nbsp;</td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2" class="BGAccentVeryDarkBorder"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="25" class="BGAccentDark">&nbsp;<strong>Name<img src="../../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
                  <td height="25" class="BGAccentDark">&nbsp;<strong>Address</strong></td>
                  <td valign="middle" class="BGAccentDark"><strong>&nbsp;City</strong></td>
                  <td valign="middle" class="BGAccentDark">&nbsp;<strong>State</strong></td>
                  <td align="center" valign="middle" class="BGAccentDark">&nbsp;<strong>Zip</strong></td>
                  <td valign="middle" class="BGAccentDark"><strong>&nbsp;Country</strong></td>
                </tr>
                <?php 
			$suppliers_sth = $dbh->execute($suppliers_sql);
			$num_rows = $suppliers_sth->numRows();
			while($suppliers_sth->fetchInto($SUPPLIERS)) {
				/* Line counter for alternating line colors */
				$counter++;
				$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
			?>
                <tr <?php pointer($row_color); ?>>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="javascript:void(0);" <?php help('', 'Click here to view information for '.ucwords(strtolower($SUPPLIERS[name])).'', 'default'); ?>><img src="../../images/detail.gif" width="18" height="20" border="0" align="absmiddle" onClick="MM_openBrWindow('../vendor_details.php?id=<?= $SUPPLIERS[id]; ?>','details','resizable=yes,width=350,height=500')"></a>&nbsp;<a href="javascript:void(0);" <?php help('', 'Click here to get a map of '.ucwords(strtolower($SUPPLIERS[name].' location.')).'', 'default'); ?>><img src="../../images/map.gif" width="13" height="20" border="0" align="absmiddle" <?php help("Get a map showing ".ucwords(strtolower($SUPPLIERS['name']))."\'s location", 'default'); ?> onClick="MM_openBrWindow('http://maps.google.com/maps?q=<?= $SUPPLIERS['address'] . "+" . $SUPPLIERS['city'] . "+" . $SUPPLIERS['state'] . "+" . $SUPPLIERS['zip5']; ?>','edit','scrollbars=yes,resizable=yes,width=800,height=800')"></a>&nbsp;<?= ucwords(strtolower($SUPPLIERS['name'])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($SUPPLIERS['address'])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= ucwords(strtolower($SUPPLIERS['city'])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= strtoupper($SUPPLIERS['state']); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $SUPPLIERS['zip5']; ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= strtoupper($SUPPLIERS['country']); ?></td>
                </tr>
                <?php } ?>
            </table></td>
          </tr>
          <tr>
            <td><?php if ($_GET['display'] != 'all') { 
			  		if ($num_rows >= $viewable_rows) {
			  ?><!-- #BeginLibraryItem "/Library/supplier_pagination.lbi" -->
<script type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%" height="25">&nbsp;<span class="GlobalButtonTextDisabled">
      <?= ($page_start+1)."-".($page_next)." out of ".$page_rows['total']; ?>
      Vendors</span></td>
    <td width="50%" align="right" valign="bottom"><table border="0" cellpadding="0" cellspacing="0">
      <tr>
        <?php if ($page_previous > 0) { ?>
        <td width="22"><a href="<?= $_SERVER['../../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=1"; ?>" <?php help('', 'Return the the beginning', '#336699'); ?>><img src="../../images/previous_button.gif" name="beginning" width="19" height="19" border="0" id="beginning" onMouseOver="MM_swapImage('beginning','','../../images/previous_button_on.gif',1)" onMouseOut="MM_swapImgRestore()"></a></td>
        <td width="100"><a href="<?= $_SERVER['../../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_previous; ?>" class="pagination" onMouseOver="MM_swapImage('previous','','../../images/previous_button_on.gif',1)" onMouseOut="MM_swapImgRestore()" <?php help('', 'Jump to the previous page', '#336699'); ?>><img src="../../images/previous_button.gif" name="previous" width="19" height="19" border="0" align="top" id="previous">PREVIOUS</a></td>
        <?php } ?>
        <td width="100">&nbsp;</td>
        <?php if ($page_rows['total'] > $page_next) { ?>
        <td width="65" align="right"><a href="<?= $_SERVER['../../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_next; ?>" class="pagination" onMouseOver="MM_swapImage('Image1','','../../images/next_button_on.gif',1)" onMouseOut="MM_swapImgRestore()" <?php help('', 'Jump to the next page', '#336699'); ?>>NEXT<img src="../../images/next_button.gif" name="next" width="19" height="19" border="0" align="top" id="Image1"></a></td>
        <td width="22" align="right"><a href="<?= $_SERVER['../../Library/PHP_SELF']."?o=".$page_order."&d=".$page_direction."&s=".$page_last; ?>" <?php help('', 'Jump to the last page', '#336699'); ?>><img src="../../images/next_button.gif" name="end" width="19" height="19" border="0" id="end" onMouseOver="MM_swapImage('end','','../../images/next_button_on.gif',1)" onMouseOut="MM_swapImgRestore()"></a></td>
        <?php } ?>
      </tr>
    </table></td>
  </tr>
</table>
<!-- #EndLibraryItem --><?php } else {?>
				  <span class="GlobalButtonTextDisabled"> <?= $num_rows ?> Vendors</span>
              <?php } ?>
              <?php } else { ?>
				  <span class="GlobalButtonTextDisabled"> <?= $num_rows ?> Vendors</span>
              <?php } ?></td>
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
                <td width="50%"><span class="Copyright"><!-- InstanceBeginEditable name="copyright" --><?php include('../../include/copyright.php'); ?><!-- InstanceEndEditable --></span></td>
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
      <td class="DarkHeaderSubSub">&nbsp;<a href="javascript:void(0);" onClick="MM_openBrWindow('../../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')" class="dark">v1.0</a></td>
      <td width="20" class="DarkHeaderSubSub"><div align="right"><a href="javascript:void(0);" onClick="MM_openBrWindow('../../Help/releasenotes.php','help','scrollbars=yes,resizable=yes,width=800,height=800')"><img src="../../images/notes.gif" alt="Release Notes" width="12" height="15" border="0" align="absmiddle"></a></div></td>
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
include_once('../debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>