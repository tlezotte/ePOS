<?php
/**
 * Request System
 *
 * search.php searches current CER.
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

/* ???? Start Debug ???? */
if ($debug) {
	echo "<BR><--- POST: ---><BR>";
	print_r($_POST);
	echo "<BR><--- GET: ---><BR>";
	print_r($_GET);
	echo "<BR><--- SESSION: ---><BR>";
	print_r($_SESSION);
	//exit;
}
/* ???? End Debug ???? */



/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u, Standards.Employees e 
							 WHERE e.eid = u.eid");
/* Getting suppliers from Standards */						 
$SUPPLIERS = $dbh->getAssoc("SELECT id, name 
						     FROM Supplier 
						     WHERE status = '0' 
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
$company_sql = $dbh->prepare("SELECT id, name 
						      FROM Standards.Companies 
						      WHERE id > 0 
						      ORDER BY name");
/* Getting suppliers from Suppliers */						 
$supplier_sql = $dbh->prepare("SELECT id, name 
							   FROM Supplier 
							   WHERE status = '0' 
							   ORDER BY name");						 
/* Getting issuers from Users */			 
$issuer_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
						      FROM Users U, Standards.Employees E 
						      WHERE U.eid = E.eid and U.issuer = '1' and U.status = '0' and E.status = '0' 
						      ORDER BY E.lst ASC");
/* Project Originator from Users.Requesters */
$req_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
						  FROM Users U, Standards.Employees E 
						  WHERE U.eid = E.eid and U.requester = '1' 
						  ORDER BY E.lst ASC");
/* Getting approver 1's from Users */									 
$app1_sql = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
					       FROM Users U, Standards.Employees E 
					       WHERE U.eid = E.eid and U.one = '1' and U.status = '0' and E.status = '0' 
					       ORDER BY E.lst ASC");
/* Getting approver 2's from Users */						  
$app2_sql  = $dbh->prepare("SELECT U.eid, E.fst, E.lst 
					        FROM Users U, Standards.Employees E 
					        WHERE U.eid = E.eid and U.two = '1' and U.status = '0' and E.status = '0' 
					        ORDER BY E.lst ASC");
/* Getting PO numbers from PO */	
$po_sql = $dbh->prepare("SELECT DISTINCT(po) FROM PO ORDER BY po ASC");					   
/* Getting JOB numbers from PO */	
$job_sql = $dbh->prepare("SELECT DISTINCT(job) FROM PO ORDER BY job ASC");
/* Getting Part numbers from PO */	
$part_sql = $dbh->prepare("SELECT DISTINCT(part) FROM Items ORDER BY part ASC");
/* Getting VT numbers from PO */	
$vt_sql = $dbh->prepare("SELECT DISTINCT(vt) FROM Items ORDER BY vt ASC");
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

/* ------------------ START FUNCTIONS ----------------------- */
function pointer($row_color) {
	/* Javascript code for mouseover color change in list */
	echo "onmouseover=\"setPointer(this, 1, 'over', '#".$row_color."', '#999966', '#".$row_color."');\" ".
  		 "onmouseout=\"setPointer(this, 1, 'out', '#".$row_color."', '#999966', '#".$row_color."');\"";
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
	<script type="text/javascript">function sf(){ document.form.field.focus(); }</script>
	<script language="JavaScript">
		function clikker(a,b,c)
		{
			if (a.style.display =="")
			{
				a.style.display = "none";
				b.src="<?= $default['url_home']; ?>/images/button.php?i=b70.png&l=Show";
				c.value = 0
			}
			else
			{
				a.style.display="";
				b.src="<?= $default['url_home']; ?>/images/button.php?i=b70.png&l=Hide";
				c.value = 1
			}
		}
	</script>
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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><!-- #BeginLibraryItem "/Library/lm_CER.lbi" --><table cellspacing="0" cellpadding="0" summary="" border="0">
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
    <!-- InstanceBeginEditable name="main" --><br>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" name="Form" id="Form">
      <table width="925"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="25" valign="top"><div align="right"><a href="javascript:void(0);"  <?php help("Show/Hide Search Criteria"); ?>><img src="../images/button.php?i=b70.png&l=Hide" border="0" id="ReqIcon" onClick="clikker(Req,ReqIcon,ReqForm);"></a>&nbsp;&nbsp;</div></td>
        </tr>
        <tr>
          <td>
		  <div style="display: display;" id="Req">
		  <input id="ReqForm" value="0" name="ReqForm" type="hidden">
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
					    $selected = ($_POST['company'] == $COMPANY[id]) ? selected : $blank;
						print "<option value=\"".$COMPANY[id]."\" ".$selected.">".$COMPANY[name]."</option>\n";
					  }
					?>
                    </select></td>
                    <td width="125">CER Number:</td>
                    <td><input name="cer" type="text" id="cer" size="11"></td>
                    <td width="125">Requester:</td>
                    <td><select name="req">
                        <option value="">Select One</option>
                        <?php
				  $req_sth = $dbh->execute($req_sql);
				  while($req_sth->fetchInto($REQ)) {
				    $selected = ($_POST['req'] == $REQ[eid]) ? selected : $blank;
					print "<option value=\"".$REQ[eid]."\" ".$selected.">".ucwords(strtolower($REQ[lst])).", ".ucwords(strtolower($REQ[fst]))."</option>\n";
				  }
				  ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td nowrap>Department:</td>
                    <td><select name="department" id="department">
                        <option value="">Select One</option>
                        <?php
				  $dept_sth = $dbh->execute($dept_sql);
				  while($dept_sth->fetchInto($DEPT)) {
				    $selected = ($_POST['department'] == $DEPT[id]) ? selected : $blank;
					print "<option value=\"".$DEPT[id]."\" ".$selected.">".$DEPT[name]."</option>\n";
				  }
				  ?>
                    </select></td>
                    <td>Part Number: </td>
                    <td><select name="part" id="part" disabled>
                        <option value="">Select One</option>
                        <?php
					  $part_sth = $dbh->execute($part_sql);
					  while($part_sth->fetchInto($ITEMS)) {
					    $selected = ($_POST['part'] == $ITEMS[part]) ? selected : $blank;
						print "<option value=\"".$ITEMS[part]."\" ".$selected.">".ucwords(strtolower($ITEMS[part]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>Approver 1: </td>
                    <td><select name="app1" id="app1" disabled>
                        <option value="">Select One</option>
                        <?php
				  $app1_sth = $dbh->execute($app1_sql);
				  while($app1_sth->fetchInto($APP1)) {
				    $selected = ($_POST['app1'] == $APP1[eid]) ? selected : $blank;
					print "<option value=\"".$APP1[eid]."\" ".$selected.">".ucwords(strtolower($APP1[lst].", ".$APP1[fst]))."</option>";
				  }
				 ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Plant:</td>
                    <td><select name="plant" disabled>
                        <option value="">Select One</option>
                        <?php
				  $plant_sth = $dbh->execute($plant_sql);
				  while($plant_sth->fetchInto($PLANT)) {
				    $selected = ($_POST['plant'] == $PLANT[id]) ? selected : $blank;
					print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
				  }
				  ?>
                    </select></td>
                    <td>Job Number: </td>
                    <td><select name="job" id="job" disabled>
                        <option value="">Select One</option>
                        <?php
					  $job_sth = $dbh->execute($job_sql);
					  while($job_sth->fetchInto($PO)) {
					    $selected = ($_POST['job'] == $PO[job]) ? selected : $blank;
						print "<option value=\"".$PO[job]."\" ".$selected.">".ucwords(strtolower($PO[job]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>Approver 2: </td>
                    <td><select name="app2" id="app2" disabled>
                        <option value="">Select One</option>
                        <?php
				  $app2_sth = $dbh->execute($app2_sql);
				  while($app2_sth->fetchInto($APP2)) {
				    $selected = ($_POST['app2'] == $APP2[eid]) ? selected : $blank;
					print "<option value=\"".$APP2[eid]."\" ".$selected.">".ucwords(strtolower($APP2[lst].", ".$APP2[fst]))."</option>";
				  }
				  ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Ship To: </td>
                    <td><select name="ship">
                        <option value="">Select One</option>
                        <?php
					  $plant_sql_sth = $dbh->execute($plant_sql);
					  while($plant_sql_sth->fetchInto($PLANT)) {
					    $selected = ($_POST['ship'] == $PLANT[id]) ? selected : $blank;
						print "<option value=\"".$PLANT[id]."\" ".$selected.">".ucwords(strtolower($PLANT[name]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>VT Number: </td>
                    <td><select name="vt" id="vt" disabled>
                        <option value="">Select One</option>
                        <?php
					  $vt_sth = $dbh->execute($vt_sql);
					  while($vt_sth->fetchInto($ITEMS)) {
					    $selected = ($_POST['vt'] == $ITEMS[vt]) ? selected : $blank;
						print "<option value=\"".$ITEMS[vt]."\" ".$selected.">".ucwords(strtolower($ITEMS[vt]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>Issuer:</td>
                    <td><select name="issuer" id="issuer" disabled>
                        <option value="">Select One</option>
                        <?php
				  $issuer_sth = $dbh->execute($issuer_sql);
				  while($issuer_sth->fetchInto($ISSUER)) {
				    $selected = ($_POST['issuer'] == $ISSUER[eid]) ? selected : $blank;
					print "<option value=\"".$ISSUER[eid]."\" ".$selected.">".ucwords(strtolower($ISSUER[lst].", ".$ISSUER[fst]))."</option>";
				  }
				  ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td>Supplier:</td>
                    <td><select name="sup" id="sup">
                        <option value="">Select One</option>
                        <?php
					  $supplier_sth = $dbh->execute($supplier_sql);
					  while($supplier_sth->fetchInto($SUPPLIER)) {
					    $selected = ($_POST['sup'] == $SUPPLIER[id]) ? selected : $blank;
						print "<option value=\"".$SUPPLIER[id]."\" ".$selected.">".ucwords(strtolower($SUPPLIER[name]))."</option>\n";
					  }
					?>
                    </select></td>
                    <td>Purpose:</td>
                    <td><input type="text" name="purpose" value="<?= $_POST['purpose']; ?>"></td>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td nowrap>Item Description:</td>
                    <td><input type="text" name="descr"  value="<?= $_POST['descr']; ?>" disabled></td>
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
                      <input name="stage" type="hidden" id="stage" value="search">
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
	  if ($_POST['stage'] == 'search') {
	  
		$S_PO = (!empty($_POST['po'])) ? "po LIKE '".$_POST['po']."' and " : '';
		$S_REQ = (!empty($_POST['req'])) ? "req='".$_POST['req']."' and " : '';
		$S_COM = (!empty($_POST['company'])) ? "company='".$_POST['company']."' and " : '';
		$S_SUP = (!empty($_POST['sup'])) ? "sup='".$_POST['sup']."' and " : '';
		//$S_PLA = (!empty($_POST['plant'])) ? "plant='".$_POST['plant']."' and " : '';		
		$S_SHI = (!empty($_POST['ship'])) ? "ship='".$_POST['ship']."' and " : '';
		$S_DEP = (!empty($_POST['department'])) ? "department='".$_POST['department']."' and " : '';
		$S_JOB = (!empty($_POST['job'])) ? "job LIKE '".$_POST['job']."' and " : '';
		$S_PUR = (!empty($_POST['purpose'])) ? "purpose LIKE '".$_POST['purpose']."' and " : '';
		$S_STA = (!empty($_POST['status'])) ? "status='".$_POST['status']."' and " : '';

		//if ($_POST['app1']) { $SEARCH .="app1=$app1 and ");
		//if ($_POST['app2']) { $SEARCH .="app2=$app2 and ");
		//if ($_POST['issuer']) { $SEARCH .="POI=$POI and ");
		
		//if ($_POST['plant']) { $SEARCH .="plant=$plant or plant1 like \"%plant%\" or plant2 like \"%plant%\" or plant3 like \"%plant%\" or plant4 like \"%plant%\" or plant5 like \"%plant%\" or plant6 like \"%plant%\" or plant7 like \"%plant%\" or plant8 like \"%plant%\" or plant9 like \"%plant%\" or plant10 like \"%plant%\" and ");
		//if ($_POST['descr']) { $SEARCH .="desc1 like \"%$desc%\" or desc2 like \"%$desc%\" or desc3 like \"%$desc%\" or desc4 like \"%$desc%\" or desc5 like \"%$desc%\" or desc6 like \"%$desc%\" or desc7 like \"%$desc%\" or desc8 like \"%$desc%\" or desc9 like \"%$desc%\" or desc10 like \"%$desc%\" and ");
		//if ($_POST['part']) { $SEARCH .="part1 like \"%$part%\" or part2 like \"%$part%\" or part3 like \"%$part%\" or part4 like \"%$part%\" or part5 like \"%$part%\" or part6 like \"%$part%\" or part7 like \"%$part%\" or part8 like \"%$part%\" or part9 like \"%$part%\" or part10 like \"%$part%\" and ");
		//if ($_POST['vt']) { $SEARCH .="vt1 like \"%$vt%\" or vt2 like \"%$vt%\" or vt3 like \"%$vt%\" or vt4 like \"%$vt%\" or vt5 like \"%$vt%\" or vt6 like \"%$vt%\" or vt7 like \"%$vt%\" or vt8 like \"%$vt%\" or vt9 like \"%$vt%\" or vt10 like \"%$vt%\" and ");
		//if (isset($SEARCH)) {$SEARCH = "where $SEARCH";     //$SEARCH =~ s/\sand\s$//;}        ##Strip last " and "
		$WHERE = "WHERE $S_PO $S_REQ $S_COM $S_SUP $S_SHI $S_DEP $S_JOB $S_PUR $S_STA";
		$SEARCH = preg_replace("/and\s+$/", "", $WHERE);
		
		if ($debug) {
			echo "WHERE: ".$WHERE."<BR>";
			echo "SEARCH: ".$SEARCH."<BR>";
		}
		
		/* SQL for PO list */
		$po_sql =& $dbh->prepare("SELECT id, po, purpose, reqDate, req, sup, total ".
								"FROM PO ".
								"$SEARCH ".
								"ORDER BY reqDate ASC");
								
		$po_sth = $dbh->execute($po_sql);
		$num_rows = $po_sth->numRows();	
		
		/* Dont display column headers and totals if no requests */
		if ($num_rows == 0) {				
	  ?>
		<div align="center" class="DarkHeaderSubSub">No Requests Found</div>
		<?php } else { ?>	
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
                  <td class="BGAccentDark"><strong>&nbsp;CER</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Purpose</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Requester</strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Requested<img src="../images/1downarrow.gif" width="16" height="16" align="absmiddle"></strong></td>
                  <td class="BGAccentDark"><strong>&nbsp;Supplier</strong></td>
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