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


/* Update Summary */
Summary($dbh, 'Users - BlackBerry', $_SESSION['eid']);

  

if ($_POST['action'] == 'console') {
	switch ($_POST['task']) {
		case 'switchUser':
			list($eid, $fullname, $username, $group) = split(':', $_POST['switchData']);
			$_SESSION['eid'] = $eid;
			$_SESSION['fullname'] = $fullname;
			$_SESSION['username'] = $username;
			$_SESSION['request_role'] = $group;
		break;
	}
	echo "<meta http-equiv=\"refresh\" content=\"0\">";
}

/* Get list of users */
$employees_sql = "SELECT E.fst, E.lst, U.eid, U.role, E.username
				   FROM  Users U
				    INNER JOIN Standards.Employees E ON U.eid=E.eid
					WHERE E.status='0' OR U.status='0'
				   ORDER BY E.lst"; 						  
$employees_query = $dbh->prepare($employees_sql);
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?= $default['title1']; ?></title>
<meta name="author" content="Thomas LeZotte" />
<meta name="copyright" content="2005 Your Company" />
<link href="../handheld.css" rel="stylesheet" type="text/css" media="handheld">
</head>

<body>
<table width="240" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="center"><a href="../home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0"></a></td>
  </tr>
  <tr>
    <td><div align="center">
      <?= $default['title1']; ?>    
    </div></td>
  </tr>
  <tr>
    <td><div align="center"><strong> Administration </strong></div></td>
  </tr>
  <tr>
    <td height="10" class="center"><img src="../../images/spacer.gif" width="10" height="10"></td>
  </tr>
</table>
<table width="240" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="15" nowrap><img src="/Common/images/Company_Bullet.gif" width="11" height="15"></td>
    <td><form name="form1" method="post" action="edit_users.php">
      <input name="letter" type="text" id="letter" size="7" maxlength="10">
      <input name="edit" type="submit" value="Edit User" class="button">
    </form></td>
  </tr>
  <tr>
    <td width="15"><img src="/Common/images/Company_Bullet.gif" width="11" height="15"></td>
    <td><form action="add_users.php" method="post" name="form2" id="form2">
      <input name="letter" type="text" id="letter" size="7" maxlength="10">
      <input name="add" type="submit" value="Add User" class="button">
    </form></td>
  </tr>
  <tr>
    <td><img src="/Common/images/Company_Bullet.gif" width="11" height="15"></td>
    <td height="10"><form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchUserForm" id="SwitchUserForm">
      <select name="switchData" id="switchData">
        <option value="0">Select One</option>
        <?php
			$employees_sth = $dbh->execute($employees_query);
			while($employees_sth->fetchInto($EMPOLYEES)) {
				print "<option value=\"".$EMPOLYEES['eid'].":".caps($EMPOLYEES['fst']." ".$EMPOLYEES['lst']).":".$EMPOLYEES['username'].":".$EMPOLYEES['role']."\" ".$selected.">".caps($EMPOLYEES['lst'].", ".$EMPOLYEES['fst'])."</option>";
			}
		?>
      </select>
      <input name="action" type="hidden" id="action" value="console">
      <input name="task" type="hidden" id="task" value="switchUser">
      <input name="switch" type="submit" value="Switch" class="button" id="switch">
    </form></td>
  </tr>  
  <tr>
    <td>&nbsp;</td>
    <td height="10"><img src="../../images/spacer.gif" width="10" height="10"></td>
  </tr>
  <tr>
    <td><img src="/Common/images/Company_Bullet.gif" width="11" height="15"></td>
    <td height="10"><form action="edit_detail.php" method="GET" name="form3" id="form3">
      <input name="id" type="text" id="id" size="7" maxlength="10">
      <input name="add" type="submit" value="Edit Req" class="button">
    </form></td>
  </tr>
</table>
</body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
?>