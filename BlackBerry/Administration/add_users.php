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


/* ---------- START $_GET ACTIONS ---------- */
switch ($_GET['action']) {
	/* ---------- ADD USER ---------- */
	case "add":
		$sql="INSERT into Users (eid) VALUES('".$_GET['eid']."')";
		$dbh->query($sql);
		
		/* Record transaction for history */
		History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));
		
		header("Location: edit_users.php?eid=".$_GET['eid']."&action=details");
		exit();
	break;
}
/* ---------- END $_GET ACTIONS ---------- */

if (array_key_exists('letter', $_POST)) {
	/* ----- START DATABASE ACCESS ----- */
	$users_sql = "SELECT eid, fst, lst 
				  FROM Standards.Employees 
				  WHERE status = '0'
					AND lst LIKE '$_POST[letter]%'
				  ORDER BY lst ASC";		 
	$users_query = $dbh->prepare($users_sql);		 
	$users_sth = $dbh->execute($users_query);
	$num_rows = $users_sth->numRows();
	/* ----- END DATABASE ACCESS ----- */
}

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
<meta name="cache-control" content="no-cache" />
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="../handheld.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td nowrap class="center"><div align="center"><a href="../home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0"></a></div></td>
  </tr>
  <tr>
    <td nowrap class="center"><?= $default['title1']; ?></td>
  </tr>
  <tr>
    <td nowrap class="center"><strong> Add User</strong></td>
  </tr>
</table>
<table width="240"  border="0">
  <tr class="BGAccentDark">
    <td height="25"><strong>&nbsp;User Name</strong></td>
  </tr>
  <?php 
	  while($users_sth->fetchInto($USERS)) {
		/* Line counter for alternating line colors */
		$counter++;
		$row_color = ($counter % 2) ? FFFFFF : DFDFBF;
  ?>
  <tr <?php pointer($row_color); ?>>
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;<a href="<?= $_SERVER['PHP_SELF']; ?>?action=add&eid=<?= $USERS['eid']; ?>" class="dark">
      <?= ucwords(strtolower($USERS['lst'].", ".$USERS['fst'])); ?>
    </a></td>
  </tr>
  <?php } ?>
</table>
</body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
?>