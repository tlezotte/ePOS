<?php
/**
 * Request System
 *
 * search.php search available PO.
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
$Dbg->DatabaseName="Request";

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
//require_once('../security/check_user.php');


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name ".
							"FROM Users u, Standards.Employees e ".
							"WHERE e.eid = u.eid");
/* Getting suppliers from Standards */						 
$SUPPLIERS = $dbh->getAssoc("SELECT BTVEND AS id, BTNAME AS name
						     FROM Standards.Vendor
						     ORDER BY name");															
/* Getting plant locations from Standards.Plants */								
$plant_sql = $dbh->prepare("SELECT id, name FROM Standards.Plants ORDER BY name ASC");
/* Getting plant locations from Standards.Department */	
$dept_sql  = $dbh->prepare("SELECT * FROM Standards.Department ORDER BY name ASC");
/* Getting plant locations from Standards.Category */	
$category_sql = $dbh->prepare("SELECT * FROM Standards.Category ORDER BY name ASC");
/* Getting units from Standards.Units */	
$units_sql = $dbh->prepare("SELECT * FROM Standards.Units ORDER BY name ASC");
/* Getting companies from Standards.Companies */								
$company_sql = $dbh->prepare("SELECT id, name ".
						 "FROM Standards.Companies ".
						 "WHERE id > 0 ".
						 "ORDER BY name");
/* Getting suppliers from Suppliers */						 
$supplier_sql = $dbh->prepare("SELECT BTVEND AS id, CONCAT(BTNAME, ' ( ', BTVEND, ' )') AS name, BTSTAT AS status
							    FROM Standards.Vendor
							    WHERE BTVEND NOT LIKE '%R'
							    ORDER BY name");						 
/* Getting issuers from Users */			 
$issuer_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
						 "FROM Users U, Standards.Employees E ".
						 "WHERE U.eid = E.eid and U.issuer = '1' and U.status = '0' and E.status = '0' ".
						 "ORDER BY E.lst ASC"); 
/* Project Originator from Users.Requesters */
$req_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
						 "FROM Users U, Standards.Employees E ".
						 "WHERE U.eid = E.eid and U.requester = '1' ".
						 "ORDER BY E.lst ASC");
/* Getting approver 1's from Users */									 
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
					  "FROM Users U, Standards.Employees E ".
					  "WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0' ".
					  "ORDER BY E.lst ASC"); 
/* Getting approver 2's from Users */						  
$app2_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst ".
					   "FROM Users U, Standards.Employees E ".
					   "WHERE U.eid = E.eid and U.two = '1' and U.status = '0' and E.status = '0' ".
					   "ORDER BY E.lst ASC"); 
					   
/* 
 * Getting PO numbers from PO 
 */	
$PO = $dbh->getAll("SELECT DISTINCT(po) FROM PO ORDER BY po ASC");			
/* Generate $POARRAY for Autocomplete Javascript */
foreach ($PO as $key => $value) {
	$POARRAY .= "'$value[po]',";
}
$POARRAY = substr($POARRAY, 0, -1);		//Remove the last coma in $POARRAY
		   
/* 
 * Getting JOB numbers from PO 
 */	
$JOB = $dbh->getAll("SELECT DISTINCT(job) FROM PO ORDER BY job ASC");
/* Generate $JOBARRAY for Autocomplete Javascript */
foreach ($JOB as $key => $value) {
	$JOBARRAY .= "'$value[job]',";
}
$JOBARRAY = substr($JOBARRAY, 0, -1);		//Remove the last coma in $JOBARRAY

/* 
 * Getting Part numbers from PO 
 */	
$PART = $dbh->getAll("SELECT DISTINCT(part) FROM Items ORDER BY part ASC");
/* Generate $PARTARRAY for Autocomplete Javascript */
foreach ($PART as $key => $value) {
	$PARTARRAY .= "'$value[part]',";
}
$PARTARRAY = substr($PARTARRAY, 0, -1);		//Remove the last coma in $PARTARRAY

/* 
 * Getting VT numbers from PO 
 */	
$VT = $dbh->getAll("SELECT DISTINCT(vt) FROM Items ORDER BY vt ASC");
/* Generate $VTARRAY for Autocomplete Javascript */
foreach ($VT as $key => $value) {
	$VTARRAY .= "'$value[vt]',";
}
$VTARRAY = substr($VTARRAY, 0, -1);		//Remove the last coma in $VTARRAY

/* Getting CER numbers from CER */							 						 
$cer_sql = $dbh->prepare("SELECT id, cer 
                          FROM CER 
					      WHERE cer IS NOT NULL 
					      GROUP BY cer+0");
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
  <!-- InstanceBeginEditable name="head" -->
    <script type="text/javascript" src="/Common/js/pointers.js"></script>
	
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	<script type="text/javascript" src="/Common/js/greybox5/options1.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/AJS.js"></script>
	<script type="text/javascript" src="/Common/js/greybox5/AJS_fx.js"></script>
    <script type="text/javascript" src="/Common/js/greybox5/gb_scripts.js"></script>
	<link type="text/css" href="/Common/js/greybox5/gb_styles.css" rel="stylesheet" media="all">
		
	<script type="text/javascript" src="/Common/js/autoassist/autoassist.js"></script>
	<link href="/Common/js/autoassist/autoassist.css" rel="stylesheet" type="text/css">		 
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_Main.lbi" --><?php
$menu1 = $default['url_home'] . "/PO/index.php";
$menu2 = $default['url_home'] . "/PO/list.php?action=my&access=0";
$menu3 = $default['url_home'] . "/PO/list.php";
$menu4 = $default['url_home'] . "/PO/search.php";
$menu5 = $default['url_home'] . "/PO/track.php";
$menu6 = $default['url_home'] . "/PO/prefered.php";
$menu7 = $default['url_home'] . "/PO/Reports/index.php";
?>
<table cellspacing="0" cellpadding="0" summary="" border="0">
	<tr>
	  <td>&nbsp;</td>
	  <td>
		<table cellspacing="0" cellpadding="0" summary="" border="0">
			<tr>
		  	  <td nowrap>&nbsp;<a href="<?= $menu1; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu1) ? on : off; ?>" onmouseover="return overlib('Start a new Purchase Request',  TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">NEW</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>			
			  <td nowrap>&nbsp;<a href="<?= $menu2; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu2) ? on : off; ?>" onmouseover="return overlib('List of your Purchase Requests', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">My Requisitions</a>&nbsp;</td>
			  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu3; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu3) ? on : off; ?>" onmouseover="return overlib('List all Purchase Requests', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">All Requisitions</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu4; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu4) ? on : off; ?>" onmouseover="return overlib('Search all Purchase Request', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Search</a>&nbsp;</td>
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu5; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu5) ? on : off; ?>" onmouseover="return overlib('Track Shipments or Deliveries from FedEx, UPS, USPS and DHL', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Tracking</a>&nbsp;</td>			  
		  	  <td width="20" valign="middle" nowrap><div align="center"><img src="../images/dot.gif" width="10" height="10"></div></td>
			  <td nowrap>&nbsp;<a href="<?= $menu7; ?>" class="<?= ($_SERVER['REQUEST_URI'] == $menu7) ? on : off; ?>" onmouseover="return overlib('Reports on spending habits', TEXTPADDING, 5, WRAPMAX, 250, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');" onmouseout="nd();">Reports</a>&nbsp;</td>
			  </tr>
		</table>
	  </td>
	  <td>&nbsp;</td>
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
    <!-- InstanceBeginEditable name="main" --><br>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="GET" name="Form" id="Form">
      <table width="925"  border="0" align="center" cellpadding="0" cellspacing="0">
        
        <tr>
          <td>
		  <div style="display: display;" id="Req">
		    <script type="text/javascript">Req.style.display='';</script>
		  <table  border="0" cellpadding="0" cellspacing="0">
            <tr class="BGAccentVeryDark">
              <td height="30">&nbsp;&nbsp;<span class="DarkHeaderSubSub">Search...</span> </td>
            </tr>
            <tr>
              <td class="BGAccentVeryDarkBorder"><table width="100%"  border="0">
                  <tr>
                    <td width="100">Company:</td>
                    <td><select name="company" id="company">
                        <option value="">Select One</option>
                        <?php
						  $company_sth = $dbh->execute($company_sql);
						  while($company_sth->fetchInto($COMPANY)) {
							$selected = ($_GET['company'] == $COMPANY[id]) ? selected : $blank;
							print "<option value=\"".$COMPANY[id]."\" ".$selected.">".$COMPANY[name]."</option>\n";
						  }
						?>
						</select></td>
                    <td width="125">PO Number:</td>
                    <td><input name="po" type="text" id="po" size="11" autocomplete="off"></td>
                    <td width="125">Requester:</td>
                    <td><input id="reqFullname" name="reqFullname" type="text" size="30" />
                      <script type="text/javascript">
						Event.observe(window, "load", function() {
							var aa = new AutoAssist("reqFullname", function() {
								return "../Common/employees.php?l=on&q=" + this.txtBox.value;
							});
						});
					  </script>
                      <input name="req" type="hidden" id="ajaxEID"></td>
                  </tr>
                  <tr>
                    <td nowrap>Department:</td>
                    <td><select name="department" id="department">
                        <option value="">Select One</option>
                        <?php
						  $dept_sth = $dbh->execute($dept_sql);
						  while($dept_sth->fetchInto($DEPT)) {
							$selected = ($_GET['department'] == $DEPT[id]) ? selected : $blank;
							print "<option value=\"".$DEPT[id]."\" ".$selected." ".$readonly.">(".$DEPT[id].") ".ucwords(strtolower($DEPT[name]))."</option>\n";
						  }
						  ?>
						</select></td>
                    <td>Part Number: </td>
                    <td><input name="part" type="text" id="part" value="<?= $_GET['part']; ?>" autocomplete="off"></td>
                    <td>Approver 1: </td>
                    <td><select name="app1" id="app1" disabled>
                        <option value="">Select One</option>
                        <?php
						  $app1_sth = $dbh->execute($app1_sql);
						  while($app1_sth->fetchInto($APP1)) {
							$selected = ($_GET['app1'] == $APP1[eid]) ? selected : $blank;
							print "<option value=\"".$APP1[eid]."\" ".$selected.">".ucwords(strtolower($APP1[lst].", ".$APP1[fst]))."</option>";
						  }
						 ?>
						</select></td>
                  </tr>
                  <tr>
                    <td>Bill to Plant:</td>
                    <td><select name="plant">
                        <option value="">Select One</option>
                        <?php
						  $plant_sth = $dbh->execute($plant_sql);
						  while($plant_sth->fetchInto($PLANT)) {
							$selected = ($_GET['plant'] == $PLANT[id]) ? selected : $blank;
							print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
						  }
						  ?>
						</select></td>
                    <td>Job Number: </td>
                    <td><input name="job" type="text" id="job" value="<?= $_GET['job']; ?>" autocomplete="off"></td>
                    <td>Approver 2: </td>
                    <td><select name="app2" id="app2" disabled>
                        <option value="">Select One</option>
                        <?php
						  $app2_sth = $dbh->execute($app2_sql);
						  while($app2_sth->fetchInto($APP2)) {
							$selected = ($_GET['app2'] == $APP2[eid]) ? selected : $blank;
							print "<option value=\"".$APP2[eid]."\" ".$selected.">".ucwords(strtolower($APP2[lst].", ".$APP2[fst]))."</option>";
						  }
						  ?>
						</select></td>
                  </tr>
                  <tr>
                    <td>Ship To Plant:</td>
                    <td><select name="ship">
                        <option value="">Select One</option>
                        <?php
					  $plant_sql_sth = $dbh->execute($plant_sql);
					  while($plant_sql_sth->fetchInto($PLANT)) {
					    $selected = ($_GET['ship'] == $PLANT[id]) ? selected : $blank;
						print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>VT Number: </td>
                    <td><input name="vt" type="text" id="vt" value="<?= $_GET['vt']; ?>" autocomplete="off"></td>
                    <td>Issuer:</td>
                    <td><select name="issuer" id="issuer" disabled>
                        <option value="">Select One</option>
                        <?php
				  $issuer_sth = $dbh->execute($issuer_sql);
				  while($issuer_sth->fetchInto($ISSUER)) {
				    $selected = ($_GET['issuer'] == $ISSUER[eid]) ? selected : $blank;
					print "<option value=\"".$ISSUER[eid]."\" ".$selected.">".ucwords(strtolower($ISSUER[lst].", ".$ISSUER[fst]))."</option>";
				  }
				  ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Vendor:</td>
                    <td><input id="vendSearch" name="vendSearch" type="text" size="40" />
                      <script type="text/javascript">
								Event.observe(window, "load", function() {
									var aa = new AutoAssist("vendSearch", function() {
										return "../Common/vendor.php?q=" + this.txtBox.value;
									});
								});
						</script>
                      <input name="sup" type="hidden" id="supplierNew"></td>
                    <td>Purpose:</td>
                    <td><input type="text" name="purpose" value="<?= $_GET['purpose']; ?>" autocomplete="off"></td>
                    <td nowrap>Request Status: </td>
                    <td><select name="status" id="status">
                        <option value="">Select One</option>
                        <option value="N">New</option>
                        <option value="A">Approved</option>
                        <option value="O">Ordered</option>
                        <option value="R">Received</option>
                        <option value="X">Not Approved</option>
                        <option value="C">Canceled</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td nowrap>CER Number: </td>
                    <td><select name="cer">
                      <option value="0">Select One</option>
                      <?php
						  $cer_sth = $dbh->execute($cer_sql);
						  while($cer_sth->fetchInto($CER)) {
							$selected = ($_GET['cer'] == $CER[id]) ? selected : $blank; 
							print "<option value=\"".$CER[id]."\" ".$selected.">".$CER[cer]."</option>\n";
						  }
						?>
                    </select></td>
                    <td nowrap>Item Description:</td>
                    <td><input type="text" name="descr"  value="<?= $_GET['descr']; ?>"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
            <tr>
              <td height="30"><table width="100%"  border="0">
                  <tr>
                    <td valign="top" nowrap><span class="GlobalButtonTextDisabled">NOTE: Use a percent sign (%), for a wild card search </span></td>
                    <td valign="bottom"><div align="right">
                      <input name="action" type="hidden" id="action" value="search">
                        <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Search" border="0">&nbsp;
					</div></td>
                  </tr>
              </table></td>
            </tr>
          </table></div></td>
        </tr>
      </table>
    </form>
	  <?php 
	  /* Process search criteria */
	  if ($_GET['action'] == 'search') {
	  
		$S_PO = (!empty($_GET['po'])) ? "p.po LIKE '".$_GET['po']."' AND " : '';
		$S_REQ = (!empty($_GET['req'])) ? "p.req='".$_GET['req']."' AND " : '';
		$S_COM = (!empty($_GET['company'])) ? "p.company='".$_GET['company']."' AND " : '';
		$S_SUP = (!empty($_GET['sup'])) ? "p.sup='".$_GET['sup']."' AND " : '';
		$S_PLA = (!empty($_GET['plant'])) ? "p.plant='".$_GET['plant']."' AND " : '';		
		$S_SHI = (!empty($_GET['ship'])) ? "p.ship='".$_GET['ship']."' AND " : '';
		$S_DEP = (!empty($_GET['department'])) ? "p.department='".$_GET['department']."' AND " : '';
		$S_JOB = (!empty($_GET['job'])) ? "p.job LIKE '".$_GET['job']."' AND " : '';
		$S_PUR = (!empty($_GET['purpose'])) ? "p.purpose LIKE '".$_GET['purpose']."' AND " : '';
		$S_STA = (!empty($_GET['status'])) ? "p.status='".$_GET['status']."' AND " : '';
		$S_CER = (!empty($_GET['cer'])) ? "p.cer='".$_GET['cer']."' AND " : '';
		
		$S_DES = (!empty($_GET['descr'])) ? "i.descr LIKE '".$_GET['descr']."' AND " : '';
		//$S_PLA = (!empty($_GET['plant'])) ? "i.plant='".$_GET['plant']."' AND " : '';
		$S_PAR = (!empty($_GET['part'])) ? "i.part LIKE '".$_GET['part']."' AND " : '';
		$S_JOB = (!empty($_GET['job'])) ? "i.job LIKE '".$_GET['job']."' AND " : '';
		$S_VT = (!empty($_GET['vt'])) ? "i.vt LIKE '".$_GET['vt']."' AND " : '';

		$WHERE = "WHERE p.id = i.type_id AND
						$S_PO $S_REQ $S_COM $S_SUP $S_SHI $S_DEP $S_JOB $S_PUR $S_STA $S_CER 
						$S_DES $S_PLA $S_PAR $S_JOB $S_VT";
		$SEARCH = preg_replace("/AND\s+$/", "", $WHERE);
		
		if ($debug) {
			echo "WHERE: ".$WHERE."<BR>";
			echo "SEARCH: ".$SEARCH."<BR>";
		}
		
		/* SQL for PO list */
		$po_sql = "SELECT DISTINCT(p.id), p.po, p.purpose, p.reqDate, p.req, p.sup, p.total 
				   FROM PO p, Items i
				   $SEARCH 
				   ORDER BY p.reqDate DESC";
		$Dbg->addDebug($po_sql,DBGLINE_QUERY,__FILE__,__LINE__);
		$Dbg->DebugPerf(DBGLINE_QUERY);		   
		$po_query =& $dbh->prepare($po_sql);
		$po_sth = $dbh->execute($po_query);
		$num_rows = $po_sth->numRows();	
		$Dbg->DebugPerf(DBGLINE_QUERY);	
		
		/* Dont display column headers and totals if no requests */
		if ($num_rows == 0) {				
	  ?>
		<div align="center" class="DarkHeaderSubSub">No Requests Found</div>
		<?php } else { ?>	
		<!--
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="errorFirefox">Multiple Entries shown in Search Results is a display bug</td>
          </tr>
        </table>
		-->
    <br>
    <table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="BGAccentVeryDark"><div align="left">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" class="DarkHeaderSubSub">&nbsp;&nbsp;Search Results... </td>
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
                  <td class="BGAccentDark">Num</td>
                  <td class="BGAccentDark"><strong>&nbsp;Purpose</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Requested<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
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
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="../PO/detail.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Get a Detailed view', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $PO['po']; ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail_preview.php?id=<?= $PO['id']; ?>" title="Requisition Preview" rel="gb_page_fs[]" class="black">
                  <?= $PO['id']; ?></a></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= caps(substr(stripslashes($PO['purpose']), 0, 40)); ?><?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= caps($EMPLOYEES[$PO['req']]); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO['reqDate']); echo date("M-d-Y", strtotime($reqDate[0])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= caps($SUPPLIERS[$PO['sup']]); ?></td>
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
                  <td class="padding"><strong>Total:</strong></td>
                  <td class="padding">&nbsp;$<?= number_format($itemsTotal, 2, '.', ','); ?></td>
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