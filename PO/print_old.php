<?php 
/**
 * Request System
 *
 * print.php prints a hardcopy of PO.
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


/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ------------------ START DATABASE QUERY ----------------------- */
$po_sql = "SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate, DATE_FORMAT(dueDate,'%M %e, %Y') AS _dueDate 
		   FROM PO 
		   WHERE id = ".$_GET['id'];
$PO = $dbh->getRow($po_sql);

$AUTH = $dbh->getRow("SELECT *, DATE_FORMAT(app1Date,'%M %e, %Y') AS _app1Date
					  FROM Authorization
					  WHERE type_id = ? and type = 'PO'",array($PO['id']));	  
$SUPPLIER = $dbh->getRow("SELECT  BTNAME AS name, BTADR1 AS address, BTADR3 AS city, BTPRCD AS state, BTPOST AS zip5, `BTTEL#` AS phone
						  FROM Standards.Vendor 
						  WHERE BTVEND='" . $PO['sup'] . "'");
$PLANT = $dbh->getRow("SELECT * FROM Standards.Plants WHERE id=".$PO['ship']);
$CER = $dbh->getRow("SELECT * , p.name AS location_name
					 FROM CER c
					   INNER JOIN Standards.Plants p ON p.id = c.location
					 WHERE c.id =".$PO['cer']);
$SETTINGS = $dbh->getAssoc("SELECT variable, value FROM Settings WHERE company=".$PO['company']);
//$COMMENTS = $dbh->getAssoc("SELECT * FROM Comment");
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name
							 FROM Users u, Standards.Employees e 
							 WHERE e.eid = u.eid");								 		
/* ------------------ END DATABASE QUERY ----------------------- */

$signature = "";	//Blank signature field
/* Only display signature when PO is Approved, Ordered or Received */
if ($PO['status'] == 'A' or $PO['status'] == 'O' or $PO['status'] == 'R') {
	$signature_image = $default['FS_HOME'].$default['signatures']."/".$AUTH['app1'].".jpg";		// Set images filesystem location
	$signature_web = $default['URL_HOME'].$default['signatures']."/".$AUTH['app1'].".jpg";		// Set images URL location 

	/* Determin if a signature image exists */
	if (file_exists($signature_image)) {
		$signature = "<img src=\"$signature_web\">";				//Display signature image
	} else {
		$signature = ucwords(strtolower($EMPLOYEES[$AUTH['app1']]));	//Display text signature
	}
}

/* Get all items */
$items_query = $dbh->prepare("SELECT * FROM Items WHERE type_id = ".$PO['id']." LIMIT ?, ?");
$sth = $dbh->execute($items_query, array(0, 50));
$numItems = $sth->numRows();
$PAGES = ceil($numItems/10);								/* Calculate number of pages needed */

/* If there are no items forward to error page */
if ($PAGES == '0') {
	$_SESSION['error'] = "No Items to print";
	header("Location: ../error.php");
}

/* ---- Seperate Items ten to a page upto thirty items ---- */
for ($p = 1; $p <= $PAGES; $p++) {
	switch ($p) {
		case 1:
		  $LIMIT = array(0, 10);
		  break;
		case 2:
		  $LIMIT = array(10, 20);
		  break;
		case 3:
		  $LIMIT = array(20, 30);
		  break;
		case 4:
		  $LIMIT = array(30, 40);
		  break;
		case 5:
		  $LIMIT = array(40, 50);
		  break;
		  
	}
	$sth = $dbh->execute($items_query, $LIMIT);
	$pageItems = $sth->numRows();						/* Number of items per page */
	

$ONLOAD_OPTIONS="";
if (isset($ONLOAD_OPTIONS)) { $ONLOAD="onLoad=\"$ONLOAD_OPTIONS\""; }
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css" media="all">
	<!--
		body        { font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular; }
	-->
    </style>
  </head>
    
	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" <?php if (!$debug_page) { ?>onLoad="javascript:window.print() ;javascript:history.back()"<?php } ?>>
		<table border="0" cellpadding="2" cellspacing="0" width="1000">
			<tr height="140">
				<td width="249" height="140"><img src="<?= $SETTINGS[logo]; ?>" width=249 height=140 border=0></td>
				<td valign="top" height="140" width="489">
					<table border="0" cellpadding="0" cellspacing="2" width="100%">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
								<div align="center">
									<b><!--REQUISTION-->NOT A LEGAL DOCUMENT</b></div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
								<div align="center">
									<font size="-2"><b><!--BILLING ADDRESS:--></b></font></div>
							</td>
						</tr>
						<tr>
							<td>
								<div align="center">
									<font size="-1"><!--P.O. BOX <?= $SETTINGS['pobox']; ?>--></font></div>
							</td>
						</tr>
						<tr>
							<td>
								<div align="center">
									<font size="-1"><!--FRASER, MI 48026--></font></div>
							</td>
						</tr>
						<tr>
						  <td><div align="center"><font size="-2"><!--ATTENTION: <?= ucwords(strtolower($SETTINGS['attention'])); ?>--></font></div></td>
					  </tr>
					</table>
				</td>
				<td align="right" width="250" height="140">
					<table border="1" cellpadding="0" cellspacing="0" borderColor="#000000" width="258">
						<tr height="20">
							<td colspan="2" height="20">
								<div align="center">
									<b>
									REQUISITION</b></div></td>
						</tr>
						<tr height="15">
							<td width="125" height="15">
								<div align="center">
									<font size="-1">NUMBER</font></div></td>
							<td width="125" height="15">
								<div align="center">
									<font size="-1">ORDER DATE</font></div></td>
						</tr>
						<tr height="25">
							<td width="125" height="25">
								<div align="center">
									&nbsp;<font size="-1"><?= $PO['po']; ?></font></div></td>
							<td width="125" height="25">
								<div align="center">
									&nbsp;<font size="-1"><?= $PO['_reqDate']; ?></font></div></td>
						</tr>
						<?php if (isset($CER)) { ?>
						<tr>
						  <td colspan="2"><div align="center"> <b>CAPITAL ACQUISITION</b></div></td>
						</tr>
						<tr>
						  <td><div align="center"> <font size="-1">NUMBER</font></div></td>
					      <td><div align="center"> <font size="-1">PLANT</font></div></td>
					  </tr>
						<tr>
						  <td><div align="center"><?= $CER['cer']; ?>
						  </div></td>
					      <td nowrap><div align="center"><font size="-1">
					        <?= $CER['location_name']; ?>
				          </font></div></td>
					  </tr>
					  <?php } ?>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table border="0" cellpadding="0" cellspacing="0" width="996" height="150">
						<tr>
							<td width="498" valign="top">
								<table border="1" cellpadding="0" cellspacing="0" width="100%" borderColor="#000000" height="162">
									<tr height="35">
										<td rowspan="4" valign="top" width="25"><b><font size="-1">TO:</font></b></td>
										<td colspan="2" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">SUPPLIER NAME</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($SUPPLIER['name'])); ?></font></td>
												</tr>
											</table>
										</td>
										<td width="125" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">PHONE NO.</font></td>
												</tr>
											<tr>
												<td><font size="-1">&nbsp;<?= $SUPPLIER['phone']; ?></font></td>
											</tr>
										</table>
									  </td>
									</tr>
									<tr height="35">
										<td colspan="2" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">ADDRESS</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($SUPPLIER['address'])); ?></font></td>
												</tr>
											</table>
										</td>
										<td width="125" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">SUPPLIER CODE</font></td>
												</tr>
												<tr>
													<td></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="35">
										<td colspan="2" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">CITY</font></td>
													<td width="50"><font size="-2">STATE</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($SUPPLIER['city'])); ?></font></td>
													<td width="50"><font size="-1">&nbsp;<?= strtoupper($SUPPLIER['state']); ?></font></td>
												</tr>
											</table>
										</td>
										<td width="125" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">ZIP CODE</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= $SUPPLIER['zip5']; ?></font></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="35">
										<td width="125" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">F.O.B.</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($PO['fob'])); ?></font></td>
												</tr>
											</table>
										</td>
										<td height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">TERMS</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($PO['terms'])); ?></font></td>
												</tr>
											</table>
										</td>
										<td width="125" height="35" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td><font size="-2">SHIP VIA</font></td>
												</tr>
												<tr>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($PO['via'])); ?></font></td>
												</tr>
											</table>
										</td>
									</tr>
							  </table>
							</td>
							<td valign="top" width="498">
								<table border="1" cellpadding="0" cellspacing="0" width="100%" borderColor="#000000">
									<tr height="25">
										<td width="35" rowspan="2" valign="top" align="center"><img src="../images/ShipLeft.gif" width="35" height="158" border="0"></td>
										<td width="463" height="25" valign="middle">
											<div align="center">
											<img src="../images/ShipTop.gif" width="463" height="23" border="0"></div>
										</td>
									</tr>
									<tr height="135">
										<td width="463" height="135">
											<table border="0" cellpadding="0" cellspacing="2">
												<tr>
													<td width="25"></td>
													<td>&nbsp;<font size="-1"><?= ucwords(strtolower($PLANT['name'])); ?></font></td>
												</tr>
												<tr>
													<td width="25"></td>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($PLANT['address'])); ?></font></td>
												</tr>
												<tr>
													<td width="25"></td>
													<td><font size="-1">&nbsp;<?= ucwords(strtolower($PLANT['city'])); ?>, <?= strtoupper($PLANT['state']); ?> <?= $PLANT['zip5']; ?>-<?= $PLANT['zip4']; ?></font></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table border="1" cellpadding="0" cellspacing="0" width="100%" height="100%" borderColor="#000000">
						<tr height="35">
							<td colspan="3" height="35" valign="top">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><font size="-2">REQUISITIONER</font></td>
									</tr>
									<tr>
										<td><font size="-1">&nbsp;<?= ucwords(strtolower($EMPLOYEES[$PO['req']])); ?></font></td>
									</tr>
								</table></td>
							<td height="35" valign="top" width="480">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="330"><font size="-2">PURPOSE FOR USE:</font></td>
										<td width="150"><font size="-2">JOB NO.</font></td>
									</tr>
									<tr>
										<td width="330"><font size="-1">&nbsp;<?= substr($PO['purpose'], 0, 40); ?><?php if (strlen($PO['purpose']) >= 40) { echo "..."; } ?></font></td>
										<td width="150"><font size="-1">&nbsp;<?= $PO['job']; ?></font></td>
									</tr>
								</table></td>
							<td colspan="2" height="35" valign="top">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
						  <tr> 
							<td><font size="-2">DUE DATE:</font></td>
						  </tr>
						  <tr>
							<td><font size="-1">&nbsp;<?= $PO['_dueDate']; ?></font></td>
						  </tr>
						  <tr> 
							<td></td>
						  </tr>
						</table></td>
						</tr>
						<tr height="30">
							<td width="90" height="30">
								<div align="center">
									<font size="-2">ACCOUNT NO.</font></div>
							</td>
							<td width="90" height="30">
								<div align="center">
									<font size="-2">QUANITY ORDERED</font></div>
							</td>
							<td width="90" height="30">
								<div align="center">
									<font size="-2">PART NO.</font></div>
							</td>
							<td height="30" width="480">
								<div align="center">
									<font size="-2">SUPPLIER PART/DESCRIPTION</font></div>
							</td>
							<td width="115" height="30">
								<div align="center">
									<font size="-2">UNIT PRICE</font></div>
							</td>
							<td width="115" height="30">
								<div align="center">
									<font size="-2">TOTAL</font></div>
							</td>
						</tr>
						<?php 
						while($sth->fetchInto($row)) {
							$ACCOUNT=(isset($CER)) ? "10-0158-00" : "$PO[department]-$row[cat]";		// Set Account number by CER
						?>
						<tr height="20">
							<td width="90" height="20"><font size="-1">&nbsp;<?= $ACCOUNT; ?></font></td>
							<td width="90" height="20"><div align="center"><font size="-1"><?= $row['qty']; ?> <?= $row['unit']; ?></font></div></td>
							<td width="90" height="20"><font size="-1">&nbsp;<?= strtoupper($row['manuf']); ?></font></td>
							<td width="480" height="20"><font size="-1">&nbsp;<?= $row['vt']; ?>&nbsp;<?= ucwords(strtolower($row['descr'])); ?></font></td>
							<td width="115" height="20"><div align="right"><font size="-1">$<?= number_format($row['price'], 2, '.', ','); ?>&nbsp;</font></div></td>
							<td width="115" height="20"><div align="right"><font size="-1">$<?= number_format($row['qty'] * $row['price'], 2, '.', ','); ?>&nbsp;</font></div></td>
						</tr>
						<?php } ?>
						<?php 
						for ($i = $pageItems + 1; $i <= 10; $i++) {
						?>
						<tr height="20">
							<td width="90" height="20">&nbsp;</td>
							<td width="90" height="20">&nbsp;</td>
							<td width="90" height="20">&nbsp;</td>
							<td width="480" height="20">&nbsp;</td>
							<td width="115" height="20">&nbsp;</td>
							<td width="115" height="20">&nbsp;</td>
						</tr>
						<?php } ?>						
						<tr height="20">
							<td width="90" height="20">&nbsp;</td>
							<td width="90" height="20">&nbsp;</td>
							<td width="90" height="20">&nbsp;</td>
							<td height="20" width="480">
								<div align="right">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								  <tr>
								    <td nowrap>&nbsp;</td>
								    <td>&nbsp;</td>
								    <td width="75"><div align="right"><font size="-1">TOTAL&nbsp;</font></div></td>
							      </tr>
								  </table>
								</div>
							</td>
							<td width="115" height="20"><div align="center"><font size="-2">
						    <?php if ($PAGES > 1) { echo "Page: ".$p." of ".$PAGES; } ?>
						    </font></div></td>
							<td width="115" height="20"><div align="right"><font size="-1"><?= ($PAGES > 1 and $p < $PAGES) ? "----------" : "$".number_format($PO['total'], 2, '.', ','); ?></font>&nbsp;</div></td>
						</tr>
						<tr height="33">
							<td valign="top" colspan="3" height="33">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><font size="-2">CONFIRMED TO</font></td>
									</tr>
									<tr>
										<td></td>
									</tr>
								</table>
							</td>
							<td width="480" height="33" valign="top">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="150"><font size="-2">DATE</font></td>
										<td width="330"><font size="-2">APPROVED BY</font></td>
									</tr>
									<tr>
										<td width="150"><font size="-1">&nbsp;<!--<?= $AUTH['_app1Date']; ?>--></font></td>
										<td width="330"><font size="-1">&nbsp;<!--<?= $signature; ?>--></font></td>
									</tr>
								</table></td>
							<td width="115" valign="top" rowspan="2">
								<div align="center"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><div align="center"><font size="-2">TAXABLE</font></div></td>
									</tr>
									<tr height="25">
										<td valign="middle" align="center" height="25">&nbsp;</td>
									</tr>
								</table></div>
							</td>
							<td width="115" valign="top" rowspan="2">
								<div align="center"><table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><div align="center"><font size="-2">NON-TAXABLE</font></div></td>
									</tr>
									<tr>
										<td></td>
									</tr>
								</table></div>
							</td>
						</tr>
						<tr height="20">
							<td height="20" valign="middle" colspan="4"><div align="center"><font size="-1">CONFIRMING ORDER-DO NOT DUPLICATE</font></div></td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="2" width="1004">
						<tr height="10">
							<td colspan="2" height="10" align="center">
								<table border="0" cellpadding="0" cellspacing="0" width="620">
									<tr>
										<td width="45"><font size="-2">Contact:</font></td>
										<td width="200"><font size="-2">&nbsp;<?= ucwords(strtolower($SUPPLIER['contact'])); ?></font></td>
										<td width="45"><font size="-2">Phone:</font></td>
										<td width="150"><font size="-2">&nbsp;<?= $SUPPLIER['phone']; ?>x<?= $SUPPLIER['ext']; ?></font></td>
										<td width="30"><font size="-2">Fax:</font></td>
										<td width="150"><font size="-2">&nbsp;<?= $SUPPLIER['fax']; ?></font></td>
								    </tr>
								</table></td>
						</tr>
						<!--
						<tr>
							<td width="768">
								<div align="right">
									<b><font size="-2">BUYER</font></b></div></td>
							<td width="230">____________________________</td>
						</tr>
						-->
					</table>
				</td>
			</tr>
		</table>
		<?php
			 if ($PAGES != $p) { echo "<p style = \"page-break-after:always \">"; }
		 }  // End for loop
		 ?>
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