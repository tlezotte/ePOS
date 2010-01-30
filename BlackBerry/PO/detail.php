<?php 
/**
 * Request System
 *
 * home.php is the default page after a seccessful login.
 *
 * @version 1.5
 * @link http://www.yourdomain.com/go/Request/
 * @author	Thomas LeZotte (tom@lezotte.net)
 *
 * @filesource
 *
 * PHP Debug
 * @link http://phpdebug.sourceforge.net/
 * Pear HTML_QuickForm
 * @link http://pear.php.net/package/HTML_QuickForm
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
 * - Check User Access
 */
require_once('../../security/check_user.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 


/* ------------------ START $_POST ----------------------- */
switch ($_POST['action']) {
	case 'approve':
		/* Update the approvals for the PO */
		$dbh->query("UPDATE Authorization 
					 SET ".$_POST['auth']."yn='".$_POST['yn']."', 
					       ".$_POST['auth']."Date=NOW(), 
						   ".$_POST['auth']."Com='".htmlentities($_POST['Com'], ENT_QUOTES, 'UTF-8')."'
					 WHERE id = ".$_POST['auth_id']." ");
		
		header("Location: ../../PO/router.php?type_id=".$_POST['type_id']."&approval=".$_POST['auth']."&yn=".$_POST['yn']."");
		exit();
	break;
}
/* ------------------ START $_POST ----------------------- */


/* ------------------ START DATABASE CONNECTIONS ----------------------- */
/* Getting PO information */
$PO = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate
				    FROM PO
				    WHERE id = ?",array($_GET['id']));
$items_sql = $dbh->query("SELECT * FROM Items WHERE type_id = ".$PO['id']."");
/* Getting Authoriztions for above PO */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
/* Get Employee names from Standards database */
$EMPLOYEES = $dbh->getAssoc("SELECT e.eid, CONCAT(e.fst,' ',e.lst) AS name
							 FROM Users u, Standards.Employees e
							 WHERE e.eid = u.eid");					
/* Getting plant locations from Standards.Department */	
$DEPARTMENT  = $dbh->getAssoc("SELECT id, name FROM Standards.Department ORDER BY name ASC");
/* Getting units from Standards.Units */	
$UNITS = $dbh->getAssoc("SELECT id, name FROM Standards.Units ORDER BY name ASC");
/* Getting suppliers from Suppliers */						 
$SUPPLIER = $dbh->getAssoc("SELECT BTVEND AS id, BTNAME AS name FROM Standards.Vendor");						
/* Getting CER numbers from CER */							 						 
$CER = $dbh->getAssoc("SELECT id, cer
                          FROM CER
					      WHERE cer IS NOT NULL
					      ORDER BY cer");	
/* ------------------ END DATABASE CONNECTIONS ----------------------- */					  

/**
 * -  Display attachments from V3
 */
include_once('attachment.php');	


$vendor = (strlen($PO['sup2']) == 6) ? $PO['sup2'] : $PO['sup'];			// Set vendor to sup or sup2
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="../handheld.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td nowrap><div align="center"><a href="../home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0"></a></div></td>
  </tr>
  <tr>
    <td nowrap><div align="center">
      <?= $default['title1']; ?>
    </div></td>
  </tr>
  <tr>
    <td nowrap><div align="center"><strong> Detail View </strong></div></td>
  </tr>
</table>
<div class="transform_rule" rule="retaintable" devices="palm,rim" >
<table width="240" class="transform_rule" border="0">
  <tr>
    <td height="25" class="BGAccentVeryDarkBorder"><table width="240" class="transform_rule" border="0">
      <tr>
        <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;<a name="top"></a>Information</strong></td>
      </tr>
      <tr>
        <td>Req#:<strong>
          <?= $PO['id']; ?>
        </strong></td>
      </tr>
      <tr>
        <td>PO: <strong>
          <?= $PO['po']; ?>
        </strong></td>
      </tr>
      <tr>
        <td>CER: <strong>
          <?= $CER[$PO['cer']]; ?>
        </strong></td>
      </tr>
      <tr>
        <td>Req: <strong>
          <?= ucwords(strtolower($EMPLOYEES[$PO['req']])); ?>
        </strong></td>
      </tr>
      <tr>
        <td>Date: <strong>
          <?= $PO['_reqDate']; ?>
        </strong></td>
      </tr>
      <tr>
        <td>Vendor: <strong>
          <?= caps($SUPPLIER[$vendor]); ?>
        </strong></td>
      </tr>
      <tr>
        <td>Depart: <strong>
          <?= ucwords(strtolower($DEPARTMENT[$PO['department']])); ?>
        </strong></td>
      </tr>
      <tr>
        <td>Purpose: <strong>
          <?= ucwords(strtolower($PO['purpose'])); ?>
        </strong></td>
      </tr>
    </table></td>
  </tr>
</table>
<br>
<table width="240" class="transform_rule" border="0">
  <tr>
    <td height="25" class="BGAccentVeryDarkBorder"><table width="240" class="transform_rule" border="0">
      <tr>
        <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Attachments</strong></td>
      </tr>
      <tr>
        <td><?= $Attachment; ?></td>
      </tr>
    </table></td>
  </tr>
</table>
<br>
<table width="240" class="transform_rule" border="0">
  <tr>
    <td height="25" class="BGAccentVeryDarkBorder"><table width="240" class="transform_rule" border="0">
      <tr>
        <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Items</strong></td>
      </tr>
      <?php while($items_sql->fetchInto($ITEMS)) { ?>
      <tr>
        <td><hr noshade color="#999966"></td>
      </tr>
      <tr>
        <td>Qty: <strong>
          <?= $ITEMS['qty']." ".ucwords(strtolower($UNITS[$ITEMS['unit']])); ?>
        </strong></td>
      </tr>
      <tr>
        <td>&nbsp;<strong>
          <?= $ITEMS['descr']; ?>
        </strong></td>
      </tr>
      <tr>
        <td>Price: <strong>$
              <?= number_format($ITEMS['price'], 2, '.', ','); ?>
        </strong></td>
      </tr>
      <?php } ?>
      <tr>
        <td><hr noshade color="#999966"></td>
      </tr>
      <tr>
        <td class="DarkHeaderSubSub">Total: $
            <?= number_format($PO['total'], 2, '.', ','); ?></td>
      </tr>
    </table></td>
  </tr>
  <?php while($items_sql->fetchInto($ITEMS)) { ?>
  
  <?php } ?>
</table>
<br>
<table width="240" class="transform_rule" border="0">
  <tr>
    <td height="25" class="BGAccentVeryDarkBorder"><table width="240" class="transform_rule" border="0">
      <tr>
        <td height="25" class="BGAccentDark"><strong>&nbsp;&nbsp;Approvals</strong></td>
      </tr>
      <?php if ($AUTH['controller'] != "") { ?>
      <tr>
        <td><img src="/Common/images/waitController.gif" width="19" height="16" align="absmiddle">
            <?php CheckAuth($AUTH['controller'], $AUTH['controlleryn'], $AUTH['controllerCom'], $AUTH['controllerDate']); ?>
          <strong>&nbsp;
            <?= caps($EMPLOYEES[$AUTH['controller']]); ?>
          </strong></td>
      </tr>
      <?php } ?>	  
      <tr>
        <td><img src="/Common/images/wait1.gif" width="19" height="16" align="absmiddle">
            <?php CheckAuth($AUTH['app1'], $AUTH['app1yn'], $AUTH['app1Com'], $AUTH['app1Date']); ?>
          <strong>&nbsp;
            <?= caps($EMPLOYEES[$AUTH['app1']]); ?>
          </strong></td>
      </tr>
      <?php if ($AUTH['app2'] != "") { ?>
      <tr>
        <td><img src="/Common/images/wait2.gif" width="19" height="16" align="absmiddle">
            <?php CheckAuth($AUTH['app2'], $AUTH['app2yn'], $AUTH['app2Com'], $AUTH['app2Date']); ?>
          <strong>&nbsp;
            <?= caps($EMPLOYEES[$AUTH['app2']]); ?>
          </strong></td>
      </tr>
      <?php } ?>
      <?php if ($AUTH['app3'] != "") { ?>
      <tr>
        <td><img src="/Common/images/wait3.gif" width="19" height="16" align="absmiddle">
            <?php CheckAuth($AUTH['app3'], $AUTH['app3yn'], $AUTH['app3Com'], $AUTH['app3Date']); ?>
          <strong>&nbsp;
            <?= caps($EMPLOYEES[$AUTH['app3']]); ?>
          </strong></td>
      </tr>
      <?php } ?>
      <?php if ($AUTH['app4'] != "") { ?>
      <tr>
        <td><img src="/Common/images/wait4.gif" width="19" height="16" align="absmiddle">
            <?php CheckAuth($AUTH['app4'], $AUTH['app4yn'], $AUTH['app4Com'], $AUTH['app4Date']); ?>
          <strong>&nbsp;
            <?= caps($EMPLOYEES[$AUTH['app4']]); ?>
          </strong></td>
      </tr>
      <?php } ?>
      <tr>
        <td><img src="/Common/images/wait0.gif" width="19" height="16" align="absmiddle"><img src="../../images/spacer.gif" width="18" height="18" align="absmiddle"><strong>&nbsp;
              <?= caps($EMPLOYEES[$AUTH['issuer']]); ?>
        </strong></td>
      </tr>
    </table></td>
  </tr>
  
  <?php if ($AUTH['app2'] != "") { ?>
  
  <?php } ?>
  <?php if ($AUTH['app3'] != "") { ?>
  
  <?php } ?>
  <?php if ($AUTH['app4'] != "") { ?>
  
  <?php } ?>
</table>
</div>
<?php if (($_SESSION['eid'] == $AUTH[$_GET['approval']] OR $_SESSION['eid'] == $AUTH['issuer'])) { ?>
<?php
 if (isset($_GET['approval'])) {
	/* Set auth level to GET[approval] */
	$auth_value = $_GET['approval'];
 } elseif ($_SESSION['eid'] == $AUTH['issuer']) {
	/* Allow update if GET[approval] was sent and Requester is viewing */
	$auth_value = "issuer";
 }
?>
<form name="Form" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
  <table width="240"  border="0">
    <tr>
      <td height="25" class="blueNoteAreaBorder"><table width="240"  border="0">
        <tr>
          <td height="25" class="blueNoteArea"><strong>&nbsp;&nbsp;Approved</strong></td>
        </tr>
        <tr>
          <td><table  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><table  border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="20">Approved: </td>
                      <td width="20"><input name="yn" type="radio" value="yes"></td>
                      <td width="50">Yes</td>
                      <td width="20"><input name="yn" type="radio" value="no"></td>
                      <td>No</td>
                    </tr>
                </table></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td>Comments:
            <input name="Com" type="text" id="Com" size="10" maxlength="100"></td>
        </tr>
      </table></td>
    </tr>
  </table>
  <br>
  <table width="240" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><div align="right">
          <input name="auth" type="hidden" id="auth" value="<?= $auth_value; ?>">
          <input name="auth_id" type="hidden" id="auth_id" value="<?= $AUTH['id']; ?>">
          <input name="type_id" type="hidden" id="type_id" value="<?= $_GET['id']; ?>">
          <input name="action" type="hidden" id="action" value="approve">
          <input name="update" type="submit" value="Update" class="button">
        &nbsp;</div></td>
    </tr>
  </table>
</form>
<?php } ?><a href="#top"><img src="/Common/images/top_v1.gif" width="40" height="20" border="0"></a>
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