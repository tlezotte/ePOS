<?php 
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
 * - Check User Access
 */
//require_once('../../security/check_user.php');
/**
 * - Common Information
 */
require_once('../../include/config.php'); 

/* --- PEAR QuickForm --- */
require_once ('HTML/QuickForm.php');


/* --- Process Form1 Data --- */
if (isset($_GET['quarter']) and isset($_GET['year'])) {
	/* Set Quarterly start and end dates */
	switch ($_GET['quarter']) {
		case 'Q1':
					$start = $_GET['year']['y']."-01-01";
					$end = $_GET['year']['y']."-03-31";
					break;
		case 'Q2':
					$start = $_GET['year']['y']."-04-01";
					$end = $_GET['year']['y']."-06-30";
					break;		
		case 'Q3':
					$start = $_GET['year']['y']."-07-01";
					$end = $_GET['year']['y']."-09-30";
					break;	
		case 'Q4':
					$start = $_GET['year']['y']."-10-01";
					$end = $_GET['year']['y']."-12-31";
					break;		
	}
}

/* --- Process Form2 Data --- */
if (isset($_GET['sDate']) AND isset($_GET['eDate'])) {
	$sDate = $_GET['sDate'];
	$eDate = $_GET['eDate'];

	$start = $sDate['y']."-".$sDate['M']."-".$sDate['d'];
	$end = $eDate['y']."-".$eDate['M']."-".$eDate['d'];
}

/* Get Department information a purchases */
if (isset($_GET['quarter']) OR isset($_GET['sDate'])) {
$sql = <<< SQL
	SELECT s.name, sum( p.total ) AS total
	FROM PO p, Items i, Standards.Plants s
	WHERE p.id = i.type_id
		AND s.id = i.plant
		AND p.reqDate >= '$start'
		AND p.reqDate <= '$end'
		AND p.status IN ('A', 'O', 'R')
	GROUP BY i.plant
	ORDER BY total DESC 
SQL;

$data_sql = $dbh->prepare($sql);
}


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
  <!-- InstanceBeginEditable name="head" -->  <!-- InstanceEndEditable -->
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
          <td valign="bottom" align="right" colspan="2"><!-- InstanceBeginEditable name="rightMenu" --><!-- #BeginLibraryItem "/Library/rightMenu_PO.lbi" -->
<table  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>
	<?php if ($_SESSION['request_access'] >= 1) { ?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../../images/rM1off.gif" width="12" height="17"></td>
        <td class="rightMenuOFF"><a href="../../Administration/index.php" class="off">Administration</a> </td>
        <td><img src="../../images/rM2off.gif" width="12" height="17"></td>
      </tr>
    </table>
	<?php } else { ?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../../images/rM1off.gif" width="12" height="17"></td>
        <td class="rightMenuOFF"><a href="../../Administration/index.php" class="off">My Account</a> </td>
        <td><img src="../../images/rM2off.gif" width="12" height="17"></td>
      </tr>
    </table>
	<?php } ?>	</td>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../../images/rM1off.gif" width="12" height="17"></td>
        <td class="rightMenuOFF"><a href="../../CER/index.php" class="off">Capital Acquisition</a> </td>
        <td><img src="../../images/rM2off.gif" width="12" height="17"></td>
      </tr>
    </table></td>	
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="../../images/rM1on.gif" width="12" height="17"></td>
        <td class="rightMenuON"><a href="../index.php" class="on">Requisition Request </a> </td>
        <td><img src="../../images/rM2on.gif" width="12" height="17"></td>
      </tr>
    </table></td>
    <td width="5"><img src="../../images/spacer.gif" width="5" height="10"></td>
  </tr>
</table>
<!-- #EndLibraryItem --><!-- InstanceEndEditable --></td>

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
                <td class="BGColorDark" rowspan="3"><!-- InstanceBeginEditable name="leftMenu" --><?php include('../../include/menu/main_left.php'); ?><!-- InstanceEndEditable --></td>
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
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top"><div id="noPrint">
        <table cellspacing="0" cellpadding="0" width="200" align="left" summary="" border="0">
          <tbody>
            <tr>
              <td valign="top" width="13" background="../../images/asyltlb.gif"><img height="20" alt="" src="../../images/t.gif" width="13" border="0"></td>
              <td valign="top" width="165" bgcolor="#cccc99"><img height="1" alt="" src="../../images/asybase.gif" width="145" border="0">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td valign="top" height="10" >&nbsp;</td>
                </tr>
                <tr>
                  <td>
				<?php
				$year = date("Y");

				$form1 =& new HTML_QuickForm('Form1', 'get');
				$form1->setDefaults(array(
					'year' => array('y' => $year)
				));
								
				$form1->addElement('header', '', 'Quaterly Report');
				$form1->addElement('select', 'quarter', 'Quarter:', array('Q1' => 'Q1', 'Q2' => 'Q2', 'Q3' => 'Q3', 'Q4' => 'Q4'));
				$form1->addElement('date', 'year', 'Year:', array('format'=>'y', 'minYear'=>2001, 'maxYear'=>$year));
				//$form1->addElement('hidden', 'stage', 'process');
				
				$form1->addElement('submit', 'submit', 'Submit');
				$form1->display();
				?></td>
                </tr>
                <tr>
                  <td height="10"><img src="../../images/spacer.gif" width="10" height="10"></td>
                </tr>
                <tr>
                  <td>
				<?php
				$month = date("m");
				$year = date("Y");
				
				$form2 =& new HTML_QuickForm('Form2', 'get');
				$form2->setDefaults(array(
					'sDate' => mktime(0, 0, 0, $month, 01, $year)
				));
				
				$form2->setConstants(array(
					'eDate' => time()
				));
				$form2->addElement('header', '', 'Custom Report');
				$form2->addElement('date', 'sDate', '', array('format'=>'M d y', 'minYear'=>2001, 'maxYear'=>$year));
				$form2->addElement('date', 'eDate', '', array('format'=>'M d y', 'minYear'=>2001, 'maxYear'=>$year));
				//$form2->addElement('hidden', 'stage', 'process');
				
				$form2->addElement('submit', 'submit', 'Submit');
				$form2->display();
				?></td>
                </tr>
              </table></td>
              <td valign="top" width="22" background="../../images/asyltrb.gif"><img height="20" alt="" src="../../images/t.gif" width="22" border="0"></td>
            </tr>
            <tr>
              <td valign="top" width="22" colspan="3"><img height="37" alt="" src="../../images/asyltb.gif" width="200" border="0"></td>
            </tr>
            <tr>
              <td valign="top" colspan="3">&nbsp;</td>
            </tr>
            <tr>
              <td valign="top" colspan="3">&nbsp;</td>
            </tr>
          </tbody>
        </table>
    </div></td>
    <td><div align="center">
		<br>		
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="BGAccentDarkBorder"><table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25" align="center" class="BGAccentDark"><strong>Plant Purchases</strong> </td>
                  </tr>
                  <tr>
                    <td valign="middle" class="xpHeaderTop"><div class="ApplicationSwitcherText">Top 10 between
                      <?= date("M d, Y",strtotime($start)); ?>
                      and
                      <?= date("M d, Y",strtotime($end)) ?>
                    </div></td>
                  </tr>
                  <tr>
                    <td><img src="department_bar.php?start=<?=$start?>&end=<?=$end?>"></td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </p><br>
			<br>
			<div style="page-break-before:always">
			<div align="left"><img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" /></div>
			<br>
			<span class="DarkHeader">Plant Purchases</span><br>
			<span class="DarkHeaderSubSub">
			<?= date("M d, Y",strtotime($start)); ?> 
			to 
			<?= date("M d, Y",strtotime($end)) ?>
			</span><br>
			<br>	
			<table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="25" class="BGAccentDark">&nbsp;</td>
              </tr>
              <tr>
                <td><table border="0" cellpadding="0" cellspacing="0" class="xpHeaderBorder">
                  <tr>
                    <td width="50" class="xpHeaderLeft">&nbsp;</td>
                    <td class="xpHeaderTop">Name</td>
                    <td class="xpHeaderTopActive">Amount</td>
                  </tr>
                  <?php
			      $data_sth = $dbh->execute($data_sql);
				  while($data_sth->fetchInto($DATA)) {
					/* Line counter for alternating line colors */
					$counter++;
					$row_color = ($counter % 2) ? 'xpHeaderOdd' : 'xpHeaderEven';
				    $total_amount = $total_amount + $DATA['total'];
			      ?>
                  <tr>
                    <td nowrap class="xpHeaderLeft"><?= $counter; ?></td>
                    <td nowrap class="<?= $row_color; ?>"><a href="<?= $_SERVER['PHP_SELF']; ?>?action=search&plant=<?= $DATA['id']; ?>&<?= $_SERVER['QUERY_STRING']; ?>" class="black"><?= ucwords(strtolower($DATA['name'])); ?></a></td>
                    <td nowrap class="<?= $row_color; ?>"><div align="right">$
                          <?= number_format($DATA['total'], 2, '.', ','); ?>
                    </div></td>
                  </tr>
                  <?php } ?>
                  <tr>
                    <td class="xpHeaderLeft">&nbsp;</td>
                    <td class="xpHeaderTotal">Total:</td>
                    <td class="xpHeaderTotal"><div align="right">$ <?= number_format($total_amount, 2, '.', ','); ?>&nbsp;&nbsp;</div></td>
                  </tr>
                </table></td>
              </tr>
            </table>
			<br>
			</div>
			<?php 
			if ($_GET['action'] == 'search') { 
				$po_sql = "SELECT DISTINCT(id), po, purpose, reqDate, req, sup, total 
						   FROM PO
						   WHERE plant=$_GET[plant]  AND reqDate >= '$start' AND reqDate <= '$end' AND status IN ('A','O','R')
						   ORDER BY reqDate DESC";
				$Dbg->addDebug($po_sql,DBGLINE_QUERY,__FILE__,__LINE__);
				$Dbg->DebugPerf(DBGLINE_QUERY);		   
				$po_query =& $dbh->prepare($po_sql);
				$po_sth = $dbh->execute($po_query);
				$num_rows = $po_sth->numRows();	
				$Dbg->DebugPerf(DBGLINE_QUERY);	
			?>
            <div style="page-break-before:always">
              <div align="left"><img src="/Common/images/CompanyPrint.gif" alt="Your Company" width="437" height="61" id="Print" /></div>
              <br>
              <br>
              <span class="DarkHeader">Plant Purchases</span><br>
              <span class="DarkHeaderSubSub"><?= ucwords(strtolower($DEPARTMENT[$_GET[department]])); ?>
              <br>
              <?= date("M d, Y",strtotime($start)); ?> to <?= date("M d, Y",strtotime($end)) ?>
              </span><br>
              <br>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="25" class="BGAccentDark">&nbsp;</td>
                </tr>
                <tr>
                  <td><table border="0" cellpadding="0" cellspacing="0" class="xpHeaderBorder">
                      <tr>
                        <td width="50" class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderTop">PO</td>
                        <td class="xpHeaderTop">Purpose</td>
                        <td class="xpHeaderTop">Requester</td>
                        <td class="xpHeaderTopActive">Requested</td>
                        <td class="xpHeaderTop">Supplier</td>
                        <td class="xpHeaderTop">Total</td>
                      </tr>
                      <?php
					  $total_amount = 0;
					  $po_sth = $dbh->execute($po_query);
					  while($po_sth->fetchInto($PO)) {
						/* Line counter for alternating line colors */
						$counter++;
						$row_color = ($counter % 2) ? 'xpHeaderOdd' : 'xpHeaderEven';
						$total_amount = $total_amount + $PO['total'];
					  ?>
                      <tr>
                        <td nowrap class="<?= $row_color; ?>"><a href="../../PO/detail.php?id=<?= $PO[id]; ?><?= $approval_option; ?>" <?php help('', 'Get a Detailed view', 'default'); ?>><img src="../../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a>&nbsp;<a href="../../PO/print.php?id=<?= $PO[id]; ?>" <?php help('', 'Print a hardcopy', 'default'); ?>><img src="../../images/printer.gif" width="15" height="20" border="0" align="absmiddle"></a></td>
                        <td nowrap class="<?= $row_color; ?>"><a href="<?= $_SERVER['PHP_SELF']; ?>?action=search&id=<?= $DATA['id']; ?>&start=<?= $start; ?>&end=<?= $end; ?>" class="black">
                          <?= $PO['po']; ?>
                        </a></td>
                        <td nowrap class="<?= $row_color; ?>">
                          <?= ucwords(strtolower(substr($PO[purpose], 0, 40))); ?>
                        <?php if (strlen($PO[purpose]) >= 40) { echo "..."; } ?></td>
                        <td nowrap class="<?= $row_color; ?>"><?= ucwords(strtolower($EMPLOYEES[$PO[req]])); ?></td>
                        <td nowrap class="<?= $row_color; ?>"><?php $reqDate = explode(" ", $PO[reqDate]); echo date("M-d-Y", strtotime($reqDate[0])); ?></td>
                        <td nowrap class="<?= $row_color; ?>"><?= ucwords(strtolower($SUPPLIER[$PO[sup]])); ?></td>
                        <td nowrap class="<?= $row_color; ?>"><div align="right"> $
                          <?= number_format($PO['total'], 2, '.', ','); ?>
                        </div></td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderLeft">&nbsp;</td>
                        <td class="xpHeaderTotal">Total:</td>
                        <td class="xpHeaderTotal"><div align="right">$
                          <?= number_format($total_amount, 2, '.', ','); ?>
  &nbsp;&nbsp;</div></td>
                      </tr>
                  </table></td>
                </tr>
              </table>
              <br>
            </div>
			<? } ?>
    </div>
        <span class="NavBarInactiveLink"><br>
      </span></td>
  </tr>
</table>
<br>
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
include_once('debug/footer.php');
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>