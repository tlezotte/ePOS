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
					      GROUP BY cer");
/* ------------------ END DATABASE CONNECTIONS ----------------------- */

/* Setup onLoad javascript program */
if ($default['pageloading'] == 'on') {
  $ONLOAD_OPTIONS="pageloading();";
}
//$ONLOAD_OPTIONS.="prepareForm();";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
  <head>
  
    <title><?= $default['title1']; ?></title>	
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="copyright" content="2004 Your Company" />
  <meta name="author" content="Thomas LeZotte" />
  <link type="text/css" href="/Common/noPrint.css" rel="stylesheet">
  <link type="text/css" href="/Common/Print.css" rel="stylesheet" media="print">
  <link type="text/css" href="/Common/newCompany.css" rel="stylesheet" media="screen">
  <link type="text/css" href="../epos.css" charset="UTF-8" rel="stylesheet">
  
  <script type="text/javascript" src="/Common/js/overlibmws.js"></script>
  <script type="text/javascript" src="/Common/js/overlibmws/overlibmws_iframe.js"></script>
  <script type="text/javascript" SRC="/Common/js/googleAutoFillKill.js"></script>
  <script type="text/javascript" src="/Common/js/disableEnterKey.js"></script>  
  
    <script type="text/javascript" src="/Common/js/pointers.js"></script>
	<script type="text/javascript" src="/Common/js/prototype/prototype.js"></script>
	<script type="text/javascript" src="/Common/js/scriptaculous/scriptaculous.js?load=effects"></script>
	</head>

  <body <?= $ONLOAD; ?>>
  <br>
  <form action="<?= $_SERVER['PHP_SELF']; ?>" method="GET" name="Form" id="Form">
    <table width="925"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><div style="display: display;" id="Req">
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
							$selected = ($_GET['company'] == $COMPANY[id]) ? selected : $blank;
							print "<option value=\"".$COMPANY[id]."\" ".$selected.">".$COMPANY[name]."</option>\n";
						  }
						?>
                      </select></td>
                      <td width="125">PO Number:</td>
                      <td><input name="po" type="text" id="po" size="11" autocomplete="off"></td>
                      <td width="125">Requester:</td>
                      <td><select name="req">
                          <option value="">Select One</option>
                          <?php
						  $req_sth = $dbh->execute($req_sql);
						  while($req_sth->fetchInto($REQ)) {
							$selected = ($_GET['req'] == $REQ[eid]) ? selected : $blank;
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
                      <td>Plant:</td>
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
                      <td>Ship To: </td>
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
                      <td><select name="sup" id="sup">
                          <option value="">Select One</option>
                          <?php
					  $supplier_sth = $dbh->execute($supplier_sql);
					  while($supplier_sth->fetchInto($SUPPLIER)) {
					    $selected = ($_GET['sup'] == $SUPPLIER[id]) ? selected : $blank;
						print "<option value=\"".$SUPPLIER[id]."\" ".$selected.">".ucwords(strtolower($SUPPLIER[name]))."</option>\n";
					  }
					?>
                      </select></td>
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
                          <input name="imageField" type="image" src="../images/button.php?i=b70.png&l=Search" border="0">
                        &nbsp; </div></td>
                    </tr>
                </table></td>
              </tr>
            </table>
        </div></td>
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
		//$S_PLA = (!empty($_GET['plant'])) ? "plant='".$_GET['plant']."' AND " : '';		
		$S_SHI = (!empty($_GET['ship'])) ? "p.ship='".$_GET['ship']."' AND " : '';
		$S_DEP = (!empty($_GET['department'])) ? "p.department='".$_GET['department']."' AND " : '';
		$S_JOB = (!empty($_GET['job'])) ? "p.job LIKE '".$_GET['job']."' AND " : '';
		$S_PUR = (!empty($_GET['purpose'])) ? "p.purpose LIKE '".$_GET['purpose']."' AND " : '';
		$S_STA = (!empty($_GET['status'])) ? "p.status='".$_GET['status']."' AND " : '';
		$S_CER = (!empty($_GET['cer'])) ? "p.cer='".$_GET['cer']."' AND " : '';
		
		$S_DES = (!empty($_GET['descr'])) ? "i.descr LIKE '".$_GET['descr']."' AND " : '';
		$S_PLA = (!empty($_GET['plant'])) ? "i.plant='".$_GET['plant']."' AND " : '';
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
				   ORDER BY p.id DESC";
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
						$row_color = ($PO['hot'] == 'yes') ? FF0000 : $row_color;		// Override row color is marked HOT

						/* Celculate the Requests approval position */
						if ($PO['status'] == 'X') {
							$position = 'waitX.gif';
							$wait_for = 'Not Approved';
						} else if (!isset($PO['app1Date'])) {
							$position = 'wait1.gif';
							$wait_for = ucwords(strtolower($EMPLOYEES[$PO['app1']])).' to approve this Request';
						} else if ((isset($PO['app2']) AND $PO['app2'] != '') AND !isset($PO['app2Date'])) {
							$position = 'wait2.gif';
							$wait_for = ucwords(strtolower($EMPLOYEES[$PO['app2']])).' to approve this Request';
						} else if ((isset($PO['app3']) AND $PO['app3'] != '') AND !isset($PO['app3Date'])) {
							$position = 'wait3.gif';
							$wait_for = ucwords(strtolower($EMPLOYEES[$PO['app3']])).' to approve this Request';
						} else if ((isset($PO['app4']) AND $PO['app4'] != '') AND !isset($PO['app4Date'])) {
							$position = 'wait4.gif';
							$wait_for = ucwords(strtolower($EMPLOYEES[$PO['app4']])).' to approve this Request';											
						} else if (!isset($PO['issuerDate'])) {
							$position = 'wait0.gif';
							$wait_for = 'Purchasing to issue a Purchase Oorder number';
						}						
					?>
                <tr <?php pointer($row_color); ?>>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><a href="detail.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Requisition Preview', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="../PO/print.php?id=<?= $PO[id]; ?>" onMouseover="return overlib('Click here to print this Purchase Order Request<br><br><b>Printer Setup:</b><br>Margins: .25<br>Orientation: Landscape', CAPTION, 'Message');" onMouseout="return nd();"><img src="../images/printer.gif" width="15" height="20" border="0" align="absmiddle"></a></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $PO['po']; ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= $PO['id']; ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= caps(substr($PO[purpose], 0, 40)); ?>
                      <?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= caps($EMPLOYEES[$PO['req']]); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo date("M-d-Y", strtotime($reqDate[0])); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><?= caps($SUPPLIERS[$PO['sup']]); ?></td>
                  <td class="padding" bgcolor="#<?= $row_color; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="2%">$</td>
                        <td width="98%"><div align="right">
                          <?= number_format($PO['total'], 2, '.', ','); ?>
                        </div></td>
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
                  <td class="padding">&nbsp;$
                      <?= number_format($itemsTotal, 2, '.', ','); ?></td>
                </tr>
            </table></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;<span class="GlobalButtonTextDisabled">
        <?= $num_rows ?>
        Requests</span></td>
    </tr>
  </table>
  <?php } ?>
  <?php } ?>
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