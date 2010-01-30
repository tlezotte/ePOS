<?php
/**
 * @link http://www.Company.com/
 * @author	Thomas LeZotte (tom@lezotte.net)
 */


//include('include/functions.php');
/**
 * - Forward BlackBerry users to BlackBerry version
 */
require_once('../include/BlackBerry.php');

if ($_GET['beta'] == 'off') {
	unset($_SESSION['beta']);
	header("Location: list.php?".$_SERVER['QUERY_STRING']);
	exit();
}
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
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<title>Your Company</title>
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">
    
    <link type="text/css" href="/Common/noPrint.css" rel="stylesheet">
    <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
    <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
    <link type="text/css" href="../epos.css" charset="UTF-8" rel="stylesheet">
    <!--<link type="text/css" href="/Common/js/Spry/widgets/tabbedpanels/SpryTabbedPanels.css" charset="UTF-8" rel="stylesheet">-->

<script src="/Common/js/jquery/jquery-min.js" type="text/javascript"></script>
    <script src="/Common/js/jquery/jquery.catfish.js" type="text/javascript"></script>
    
	<script src="/Common/js/Spry/includes/xpath.js" type="text/javascript"></script>
    <script src="/Common/js/Spry/includes/SpryData.js" type="text/javascript"></script>
	<!--<script src="/Common/js/Spry/includes/SpryPagedView.js" type="text/javascript"></script>
    <script src="/Common/js/Spry/widgets/tabbedpanels/SpryTabbedPanels.js" type="text/javascript"></script>-->
    <?php if ($debug_page) { ?>
    <script src="/Common/js/Spry/includes/SpryDebug.js" type="text/javascript"></script>
    <?php } ?>
  
    <script language="javascript">
		/* --- Get news from RSS feed --- */
		var dsRequisitions = new Spry.Data.XMLDataSet("requisitions_xml.php?<?= $_SERVER['QUERY_STRING']; ?>", "requisitions/requisition", {sortOnLoad:"id",sortOrderOnLoad:"descending", subPaths: [ "authorization" ]});
		dsRequisitions.setColumnType("id", "number");
		dsRequisitions.setColumnType("hire", "date");
		dsRequisitions.setColumnType("requester/@date", "date");
		dsRequisitions.setColumnType("duedate", "date");
		dsRequisitions.setColumnType("total", "currency");
		
		//var pvRSS = new Spry.Data.PagedView( dsRequisitions ,{ pageSize: 20 });
//    	var pvRSSInfo = pvRSS.getPagingInfo();
		
		<!--var quickview_toggle = new Spry.Effect.Slide('quickview', {duration: 2000, from: '0px', to: '200px', toggle:true});-->
    </script>
    
    <style type="text/css">
<!--
#total, #totalH {
	text-align: right;
}
.cellLeftHeadings {
	padding-top: 2px;
	padding-right: 5px;
	padding-bottom: 2px;
	padding-left: 5px;
	cursor: pointer;
	background-image: url(../images/accentDark.gif);
	background-repeat: repeat-x;
	BACKGROUND-COLOR: #cccc99;
}
#id, #po, #billtoplant, #purpose, #total {
	padding-top: 2px;
	padding-right: 5px;
	padding-bottom: 2px;
	padding-left: 5px;
}
.spryHover {
	background-color: #999966;
	color: #000000;
}
.spryEven {
	background: #F7F7E6;
}
.spryOdd {
	background: #FFFFFF;
}
#pageDisplay {
	font-size: 75%;
	text-align: right;
}
.movePage {
	font-size: 12px;
	text-transform: uppercase;
	color: #336699;
	vertical-align: middle;
	font-weight: bold;
}
.otherPages {
	color: #336699;
	text-decoration: none;
}
.currentPage {
	font-weight: bold;
	color: #336699;
	text-decoration: underline;
}
.pageCount {
	font-size: 12px;
	color: #336699;
	text-align: right;
}
#pageSizeTF {
	float: right;
}
#pageSize {
	color: #000000;
	background-color: #D9D8B5;
	border: thin solid #336699;
}
.TabbedPanels {
	width: 500px;
}
.TabbedPanelsTab {
	font-family: sans-serif;
	font-size: 12px;
	font-weight: bold;
}
hr {
	clear: left;
}
.hot-yes, .approved-no {
	background-color:#FF0000;
}
/* catfish and 'position:fixed' emulation */
#catfish {
	position: fixed;
	bottom: 0;
	z-index: 100;
	width:100%;
	vertical-align:top;
	overflow: auto;
	padding: 2px;
	height: 125px;
	width:100%;
	margin-bottom: 0pt;
	color:#FFFFFF;
	background-color:#336699; /*#F7F7E6;*/
	border-top-width: 2px;
	border-top-style: solid;
	border-top-color: #000000; /*#999966;*/
}
#catfish h6#goaway {
float:right;
text-align:right
}
.zip {
height: 100%;
overflow: auto;
position: relative;
z-index: 2;
}
body.zipped, html.zipped
{
margin: 0;
padding: 0 0 0 0;
height: 100%;
overflow: hidden;
}
body {
	margin-left: 0px;
	margin-right: 0px;
}
.red {
	color:#990000;
	font-weight:bold;
}
-->
    </style>
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
  <tbody>
    <tr>
      <td valign="top"><a href="../home.php"><img src="/Common/images/Company.gif" alt="<?= $default['title1']; ?> Home" name="Company" width="300" height="50" border="0" id="Company" /></a></td>
      <td align="right" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom" align="right" colspan="2"><?php include('../include/menu/main_right.php'); ?></td>
      <td></td>
    </tr>
    <tr>
      <td width="100%" colspan="3"><table cellspacing="0" cellpadding="0" width="100%" summary="" border="0">
        <tbody>
          <tr>
            <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtl.gif" width="4" /></td>
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
            <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghtr.gif" width="4" /></td>
          </tr>
          <tr>
            <td class="BGGrayLight" rowspan="3"></td>
            <td class="BGGrayMedium" rowspan="3"></td>
            <td class="BGGrayDark" rowspan="3"></td>
            <td class="BGColorDark" rowspan="3"></td>
            <td class="BGColorDark" rowspan="3"><?php include('../include/menu/main_left.php'); ?></td>
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
                  <div align="right" class="FieldNumberDisabled"><strong> Welcome: <a href="../Administration/user_information.php" class="FieldNumberDisabled">
                    <?= caps($_SESSION['fullname']); ?>
                  </a> </strong>&nbsp;&nbsp;<a href="../logout.php" class="FieldNumberDisabled">[logout]</a>&nbsp;</div>
              	  <?php
				    } else {
					  echo "&nbsp;";
					}
				  ?>
            </td>
          </tr>
          <tr>
            <td valign="top"><img height="20" alt="" src="../images/c-ghct.gif" width="25" /></td>
            <td valign="top" colspan="2"><table cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
              <tbody>
                <tr>
                  <td height="4"></td>
                </tr>
              </tbody>
            </table></td>
            <td valign="top" colspan="4"><img height="20" alt="" src="../images/c-ghbr.gif" width="4" /></td>
          </tr>
          <tr>
            <td width="4" colspan="4" height="4"><img height="4" alt="" src="../images/c-ghbl.gif" width="4" /></td>
            <td><table height="4" cellspacing="0" cellpadding="0" width="100%" summary="" background="../images/c-ghb.gif" border="0">
              <tbody>
                <tr>
                  <td></td>
                </tr>
              </tbody>
            </table></td>
            <td><img height="4" alt="" src="../images/c-ghcb.gif" width="3" /></td>
            <td colspan="7"></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<table border="0" align="center">
      <tr>
        <td colspan="2"><div align="right"><a href="<?= $_SERVER['PHP_SELF']; ?>?beta=off" style="color:#336699;font-size:10px">Turn beta off</a></div><br />
          <table border="0" cellpadding="0" cellspacing="0" >
            <tr class="BGAccentVeryDark">
              <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;<?php if ($_GET['action'] == "my") { echo "My "; } ?>Purchase Requisitions... </td>
              <td style="text-align:right">&nbsp;</td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder" colspan="2">
            <div spry:region="dsRequisitions" class="SpryHiddenRegion">
            <table border="0" >
            <tr height="25">
              <th class="cellLeftHeadings" nowrap="nowrap" scope="col" spry:sort="id" spry:choose="spry:choose">&nbsp;</th>
              <th class="cellLeftHeadings" nowrap="nowrap" scope="col" spry:sort="id" spry:choose="spry:choose"> 
                <span spry:when="'{ds_SortColumn}' == 'id' && '{ds_SortOrder}' == 'ascending'">NUM <img src="/Common/images/ascending.gif" align="absmiddle" /></span> 
                <span spry:when="'{ds_SortColumn}' == 'id' && '{ds_SortOrder}' == 'descending'">NUM <img src="/Common/images/descending.gif" align="absmiddle" /></span> 
                <span spry:default="spry:default">NUM</span></th>
              <th class="cellLeftHeadings" nowrap="nowrap">Purpose</th>
              <th class="cellLeftHeadings" nowrap="nowrap" scope="col" spry:sort="requester/@date" spry:choose="spry:choose"> 
                <span spry:when="'{ds_SortColumn}' == 'requester/@date' && '{ds_SortOrder}' == 'ascending'">Requested <img src="/Common/images/ascending.gif" align="absmiddle" /></span> 
                <span spry:when="'{ds_SortColumn}' == 'requester/@date' && '{ds_SortOrder}' == 'descending'">Requested <img src="/Common/images/descending.gif" align="absmiddle" /></span> 
                <span spry:default="spry:default">Requested</span></th>
              <th class="cellLeftHeadings" nowrap="nowrap" scope="col" spry:sort="billtoplant" spry:choose="spry:choose"> 
                <span spry:when="'{ds_SortColumn}' == 'billtoplant' && '{ds_SortOrder}' == 'ascending'">Location <img src="/Common/images/ascending.gif" align="absmiddle" /></span> 
                <span spry:when="'{ds_SortColumn}' == 'billtoplant' && '{ds_SortOrder}' == 'descending'">Location <img src="/Common/images/descending.gif" align="absmiddle" /></span> 
                <span spry:default="spry:default">Location</span></th>                  
              <th class="cellLeftHeadings" nowrap="nowrap" scope="col" spry:sort="vendor" spry:choose="spry:choose"> 
                <span spry:when="'{ds_SortColumn}' == 'vendor' && '{ds_SortOrder}' == 'ascending'">Vendor <img src="/Common/images/ascending.gif" align="absmiddle" /></span>
                <span spry:when="'{ds_SortColumn}' == 'vendor' && '{ds_SortOrder}' == 'descending'">Vendor <img src="/Common/images/descending.gif" align="absmiddle" /></span> 
                <span spry:default="spry:default">Vendor</span></th>
              <th class="cellLeftHeadings" id="totalH" nowrap="nowrap" scope="col" spry:sort="total" spry:choose="spry:choose"> 
                <span spry:when="'{ds_SortColumn}' == 'total' && '{ds_SortOrder}' == 'ascending'">Total <img src="/Common/images/ascending.gif" align="absmiddle" /></span> 
                <span spry:when="'{ds_SortColumn}' == 'total' && '{ds_SortOrder}' == 'descending'">Total <img src="/Common/images/descending.gif" align="absmiddle" /></span> 
                <span spry:default="spry:default">Total</span></th>
            </tr>
            <tr>
              <td colspan="7">
                <div class="spryReady" spry:state="ready" spry:if="{ds_RowCount} == 0">No requisitions found.</div>
                <div class="spryLoading" spry:state="loading">Loading requisitions...</div>
                <div class="spryFailed" spry:state="error">Failed to load requisitions...</div></td>
            </tr>
            <tr spry:repeat="dsRequisitions" spry:even="spryEven" spry:odd="spryOdd" spry:hover="spryHover" spry:select="spryHover" spry:setrow="dsRequisitions" class="hot-{hot}">
              <td nowrap="nowrap" id="id"><a href="detail.php?id={id}"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle" /></a> <img src="/Common/images/{authorization/@level}.gif" width="19" height="16" align="absmiddle" /></td>
              <td nowrap="nowrap" id="id"> {id} </td>
              <td nowrap="nowrap" id="purpose"> {purpose/@short} </td>
              <td nowrap="nowrap" id="requester/@date"> {requester/@date} </td>
              <td nowrap="nowrap" id="billtoplant"> {billtoplant} </td>
              <td nowrap="nowrap" id="vendor"> {vendor} </td>
              <td nowrap="nowrap" id="total"> ${total} </td>
            </tr>
            <tr>
                <td height="25" colspan="7" style="background-color:#C6C58D">&nbsp;&nbsp;{ds_RowCount} Requisitions</td>
            </tr>            
          </table>
          </div></td>
          </tr>
          </table></td>
  </tr>
<!--     <tr>
     	<td><span id="nextButton" spry:if="{ds_RowNumber} != 1" onclick="pvRSS.previousPage();"><img src="../images/previous_button.gif" align="absmiddle" /><span class="movePage">Previous</span> | </span>Pages: <span spry:region="pvRSSInfo" spry:repeatchildren="pvRSSInfo" class="SpryHiddenRegion"> <a spry:if="{ds_CurrentRowNumber} != {ds_RowNumber}" href="#" onclick="pvRSS.goToPage('{ds_PageNumber}'); return false;" class="otherPages">{ds_PageNumber}</a> <span spry:if="{ds_CurrentRowNumber} == {ds_RowNumber}" class="currentPage">{ds_PageNumber}</span> </span><span id="nextButton" spry:if="{ds_RowNumber} != {ds_RowCount}" onclick="pvRSS.nextPage();"> | <span class="movePage">Next</span><img src="../images/next_button.gif" align="absmiddle" /></span> </td>
        <td align="right"><input type="text" id="pageSizeTF2" size="5" value="20" />
&nbsp;
<input type="button" id="pageSize2" value="Set Page Size" onclick="pvRSS.setPageSize(parseInt(document.getElementById('pageSizeTF').value));" /></td>
     </tr>-->
</table>
  <div style="padding-bottom:125px;">&nbsp;</div>
<div spry:detailregion="dsRequisitions" id="catfish">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td width="125">Requisition:</td>
      <td class="label">{id}</td>
      <td width="125">Current Status:</td>
      <td class="label" spry:choose="spry:choose"><div spry:when="'{hot}' == 'yes'" class="red">{status} (HOT)</div>
          <div spry:default="spry:default">{status}</div></td>
      <td width="125">Next Approver:</td>
      <td class="label"><div spry:if="'{authorization/@level}' == 'controller'">{authorization/controller} (CONT)</div>
        <div spry:if="'{authorization/@level}' == 'app1'">{authorization/approver1} (APP1)</div>
        <div spry:if="'{authorization/@level}' == 'app2'">{authorization/approver2} (APP2)</div>
        <div spry:if="'{authorization/@level}' == 'app3'">{authorization/approver3} (APP3)</div>
        <div spry:if="'{authorization/@level}' == 'app4'">{authorization/approver4} (APP4)</div>
        <div spry:if="'{authorization/@level}' == 'A'">Purchasing</div></td>
    </tr>
    <tr>
      <td>Purchase Order:</td>
      <td class="label">{po}</td>
      <td>Requisitioner:</td>
      <td class="label">{requester}</td>
      <td>Vendor:</td>
      <td class="label">{vendor} ({vendor/@id})</td>
    </tr>
    <tr>
      <td>CER:</td>
      <td class="label">{cer}</td>
      <td>Request Date:</td>
      <td class="label">{requester/@date}</td>
      <td>Terms:</td>
      <td class="label">{terms}</td>
    </tr>
    <tr>
      <td>Bill to Plant:</td>
      <td class="label">{billtoplant}</td>
      <td>&nbsp;</td>
      <td class="label">&nbsp;</td>
      <td>Requisition Total:</td>
      <td><span class="label">${total}</span></td>
    </tr>
    <tr>
      <td>Department:</td>
      <td class="label">{department} ({department/@id})</td>
      <td>Purpose / Usage:</td>
      <td colspan="3"><span class="label">{purpose}</span></td>
    </tr>
  </table>
</div>  
</body>
</html>
