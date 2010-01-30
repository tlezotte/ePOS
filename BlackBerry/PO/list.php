<?php
/**
 * Request System
 *
 * track.php track shipments.
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


/* SQL for access view */
if ($_GET['action'] == "my") {
	switch ($_GET['access']) {
		case '0':
			$access = "p.req";
		break;
		case '1':
			$access = "a.app1";
		break;
		case '2':
			$access = "a.app2";	
		break;
		case '3':
			$access = "a.app3";		
		break;
		case '4':
			$access = "a.app4";		
		break;
		case '5':
			$access = "a.issuer";	
		break;
	}
}

/* SQL for different views of PO list */
if ($_GET['action'] == "my" and $_GET['view'] == "all") {
	$where_clause = $access." like '".$_SESSION['eid']."'";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&access=".$_GET['access']);
	$view_gif = 'VIEW OPEN';
} elseif ($_GET['view'] == "all") {
	$where_clause = "p.status <> 'C'";
	$view_all = $_SERVER['PHP_SELF'];
	$view_gif = 'VIEW OPEN';
} elseif ($_GET['action'] == "my") {
	//$where_clause = $access." like '".$_SESSION['eid']."' AND p.po IS NULL";
	if ($access == "p.req") {
		$where_clause = $access." like '".$_SESSION['eid']."' AND p.po IS NULL";
	} else {
		$where_clause = $access." like '".$_SESSION['eid']."' AND ".$access."Date IS NULL";	
	}
	$view_all = htmlentities($_SERVER['PHP_SELF']."?action=my&view=all&access=".$_GET['access']);
	$view_gif = 'VIEW ALL';
} else {
	$where_clause = "p.po IS NULL";
	$view_all = htmlentities($_SERVER['PHP_SELF']."?view=all");
	$view_gif = 'VIEW ALL';
}

/* Setting up Status view */
switch ($_GET['status']) {
case N:
   $where_status = "AND p.status = 'N'";
   break;
case A:
   $where_status = "AND p.status = 'A'";
   break;
case O:
   $where_status = "AND p.status = 'O'";
   break;
case R:
   $where_status = "AND p.status = 'R'";
   break;
case X:
   $where_status = "AND p.status = 'X'";
   break;
case C:
   $where_status = "AND p.status = 'C'";
   break;  
default:
   $where_status = "AND p.status <> 'C'";
   break;           
}

/* SQL for PO list */
$po_sql = "SELECT p.id, p.po, p.purpose, p.reqDate, p.req, p.sup, p.total, a.app1, a.app1Date, a.app2, a.app2Date, a.app3, a.app3Date, a.app4, a.app4Date, a.issuer, a.issuerDate, p.status
			 FROM PO p, Authorization a
			 WHERE p.id = a.type_id AND $where_clause $where_status
			 ORDER BY p.reqDate DESC";
			// echo $po_sql."<br>";
$po_query = $dbh->prepare($po_sql);
$po_sth = $dbh->execute($po_query);
$num_rows = $po_sth->numRows();

/* Get Plants and Employees from Stanards database */
$SUPPLIER = $dbh->getAssoc("SELECT id, name FROM Supplier");
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name 
							 FROM Users u, Standards.Employees e 
							 WHERE e.eid = u.eid");					
/* ------------------ END DATABASE ACCESS ----------------------- */
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>
<?= $default['title1']; ?>
</title>
<meta name="cache-control" content="no-cache" />
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="../handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td nowrap class="center"><div align="center"><a href="../home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0"></a></div></td>
  </tr>
  <tr>
    <td nowrap><div align="center">
      <?= $default['title1']; ?>
    </div></td>
  </tr>
  <tr>
    <td nowrap><div align="center"><strong>List Requests</strong></div></td>
  </tr>
</table>
<?php
	/* Dont display column headers and totals if no requests */
	if ($num_rows == 0) {
?>
<table width="240" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top" class="DarkHeader">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="top" class="DarkHeaderSubSub">No Requests Found</td>
  </tr>
  <tr>
    <td height="30" align="center">Click <a href="<?= $view_all; ?>"><?= $view_gif; ?></a>&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="top">or change the</td>
  </tr>
  <tr>
    <td height="30">
		<form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" name="Form" id="Form" style="margin: 0">
		<div align="center">
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>View:
              <input name="action2" type="hidden" id="action2" value="<?= $_GET['action']; ?>"></td>
              <td><select name="select" id="select" onChange="this.form.submit();">
                <option value="0" <?php if ($_GET['access'] == '0') { echo "selected"; } ?>>Requester</option>
                <option value="1" <?php if ($_GET['access'] == '1') { echo "selected"; } ?>>Approver 1</option>
                <option value="2" <?php if ($_GET['access'] == '2') { echo "selected"; } ?>>Approver 2</option>
                <option value="3" <?php if ($_GET['access'] == '3') { echo "selected"; } ?>>Approver 3</option>
                <option value="4" <?php if ($_GET['access'] == '4') { echo "selected"; } ?>>Approver 4</option>
                <option value="5" <?php if ($_GET['access'] == '5') { echo "selected"; } ?>>Issuer</option>
              </select>
              <input name="view2" type="hidden" id="view2" value="<?= $_GET['view']; ?>"></td>
            </tr>
          </table>
		  </div>
        </form></td>
  </tr>
</table>
<?php } else { ?>
<a name="top"></a>
<div class="transform_rule" rule="retaintable" devices="palm,rim" >
<table width="240" class="transform_rule" border="0">
  <tr>
    <td height="25"><div align="right">
	<table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
		<form action="<?= $_SERVER['PHP_SELF']; ?>" method="get" name="Form" id="Form" style="margin: 0">
          <input name="action" type="hidden" id="action" value="<?= $_GET['action']; ?>">
		  <select name="access" id="access" onchange="this.form.submit();">
			<option value="0" <?php if ($_GET['access'] == '0') { echo "selected"; } ?>>Requester</option>
			<option value="1" <?php if ($_GET['access'] == '1') { echo "selected"; } ?>>Approver 1</option>
			<option value="2" <?php if ($_GET['access'] == '2') { echo "selected"; } ?>>Approver 2</option>
			<option value="3" <?php if ($_GET['access'] == '3') { echo "selected"; } ?>>Approver 3</option>
			<option value="4" <?php if ($_GET['access'] == '4') { echo "selected"; } ?>>Approver 4</option>
			<option value="5" <?php if ($_GET['access'] == '5') { echo "selected"; } ?>>Issuer</option>
		  </select>
		  <input name="view" type="hidden" id="view" value="<?= $_GET['view']; ?>">
		</form></td>
        <td><a href="<?= $view_all; ?>"><?= $view_gif; ?></a></td>
      </tr>
    </table></div></td>
  </tr>
  <tr class="BGAccentDark">
    <td height="25">&nbsp;<span class="DarkHeaderSubSub">
      <strong><?php if ($_GET['action'] == "my") { echo "My "; } ?> Requests</strong></span>
	</td>
  </tr>
<?php
/* Reset items total variable */
$itemsTotal = 0;

while($po_sth->fetchInto($PO)) {
	/* Line counter for alternating line colors */
	$counter++;
	$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
	
	/* Celculate the Requests approval position */
	if ($PO['status'] == 'X') {
		$position = 'waitX.gif';
	} else if (!isset($PO['app1Date'])) {
		$position = 'wait1.gif';
	} else if ((isset($PO['app2']) AND $PO['app2'] != '') AND !isset($PO['app2Date'])) {
		$position = 'wait2.gif';
	} else if ((isset($PO['app3']) AND $PO['app3'] != '') AND !isset($PO['app3Date'])) {
		$position = 'wait3.gif';
	} else if ((isset($PO['app4']) AND $PO['app4'] != '') AND !isset($PO['app4Date'])) {
		$position = 'wait4.gif';											
	} else if (!isset($PO['issuerDate'])) {
		$position = 'wait0.gif';
	} else {
		$position = 'wait.gif';
	}
	
	/* Set access control for access view selected */
	if ($_GET['action'] == "my" AND $_GET['access'] == '1' AND !isset($PO['app1Date'])) {
		$approval_option = "&approval=app1";
	} else if ($_GET['action'] == "my" AND $_GET['access'] == '2' AND !isset($PO['app2Date'])) {
		$approval_option = "&approval=app2";
	} else if ($_GET['action'] == "my" AND $_GET['access'] == '3' AND !isset($PO['app3Date'])) {
		$approval_option = "&approval=app3";
	} else if ($_GET['action'] == "my" AND $_GET['access'] == '4' AND !isset($PO['app4Date'])) {
		$approval_option = "&approval=app4";											
	} else if ($_GET['action'] == "my" AND $_GET['access'] == '5' AND !isset($PO['issuerDate'])) {
		$approval_option = "&approval=issuer";
	} else {
		$approval_option = "";
	}										
?>  
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="18"><a href="detail.php?id=<?= $PO[id]; ?><?= $approval_option; ?>"><img src="../../images/detail.gif" width="18" height="20" border="0" align="absmiddle"></a></td>
        <td width="18"><img src="../../images/<?= $position; ?>" border="0" align="absmiddle"></td>
        <td nowrap><?= caps($PO[purpose]); ?></td>
      </tr>
    </table>      
    </td>
  </tr>
<?php } ?>
</table>
</div>
<a href="#top"><img src="/Common/images/top_v1.gif" width="40" height="20" border="0"></a>
<?php } // End num_row if ?>
</body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
?>