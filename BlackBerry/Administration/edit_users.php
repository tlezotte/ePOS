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


switch ($_GET['action']) {
	/* ---------- EDIT USER ---------- */
	case "edit":
		//$sql="UPDATE Users (eid) VALUES('".$_POST['addUser']."')";
		$dbh->query($sql);
		
		/* Record transaction for history */
		//History($_SESSION['eid'], $_POST['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));
	break;
	case "requester":
	case "one":
	case "two":
	case "three":
	case "four":
	case "issuer":
	case "status":
		$sql="UPDATE Users
			  SET $_GET[action]='".$_GET[value]."'
			  WHERE eid='".$_GET['eid']."'";
		$dbh->query($sql);
	
		/* Record transaction for history */
		History($_SESSION['eid'], $_GET['action'], $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));
			
		header("Location: ".$_SERVER['PHP_SELF']."?eid=".$_GET['eid']."&action=details");
		exit();
	break;
}

if (array_key_exists('letter', $_POST)) {
	/* ----- START DATABASE ACCESS ----- */
	$users_sql = "SELECT E.eid, E.fst, E.lst 
				  FROM Users U
				    INNER JOIN Standards.Employees E ON U.eid = E.eid
				  WHERE E.status = '0'
					AND E.lst LIKE '$_POST[letter]%'
				  ORDER BY E.lst ASC";		 
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
    <td nowrap><div align="center"><strong>Edit User</strong></div></td>
  </tr>
</table>
<?php 
if ($_GET['action'] == 'details') { 
	$USERS = $dbh->getRow("SELECT E.eid, E.fst, E.lst, E.username, E.email, E.password, U.access, U.requester, U.one, U.two, U.three, U.four, U.issuer, U.status 
							 FROM Users U, Standards.Employees E 
							 WHERE U.eid = E.eid
							   AND U.eid=".$_GET['eid']);
$email = split('@', $USERS['email']);

/* ----------------- REQUESTER --------------------- */
switch ($USERS['requester']) {
	case '0':
		$requester_url = $_SERVER['PHP_SELF']."?action=requester&value=1&eid=".$USERS['eid'];
		$requester_class = "no";
		$requester_message = "No";								
	break;
	case '1':
		$requester_url = $_SERVER['PHP_SELF']."?action=requester&value=0&eid=".$USERS['eid'];
		$requester_class = "yes";
		$requester_message = "Yes";	
	break;
}

/* ----------------- APPROVER 1 --------------------- */						
switch ($USERS['one']) {
	case '0':
		$one_url = $_SERVER['PHP_SELF']."?action=one&value=1&eid=".$USERS['eid'];
		$one_class = "no";
		$one_message = "No";							
	break;
	case '1':
		$one_url = $_SERVER['PHP_SELF']."?action=one&value=0&eid=".$USERS['eid'];
		$one_class = "yes";
		$one_message = "Yes";	
	break;
}

/* ----------------- APPROVER 2 --------------------- */
switch ($USERS['two']) {
	case '0':
		$two_url = $_SERVER['PHP_SELF']."?action=two&value=1&eid=".$USERS['eid'];
		$two_class = "no";
		$two_message = "No";							
	break;
	case '1':
		$two_url = $_SERVER['PHP_SELF']."?action=two&value=0&eid=".$USERS['eid'];
		$two_class = "yes";
		$two_message = "Yes";	
	break;
}

/* ----------------- APPROVER 3 --------------------- */
switch ($USERS['three']) {
	case '0':
		$three_url = $_SERVER['PHP_SELF']."?action=three&value=1&eid=".$USERS['eid'];
		$three_class = "no";
		$three_message = "No";							
	break;
	case '1':
		$three_url = $_SERVER['PHP_SELF']."?action=three&value=0&eid=".$USERS['eid'];
		$three_class = "yes";
		$three_message = "Yes";	
	break;
}

/* ----------------- APPROVER 4 --------------------- */
switch ($USERS['four']) {
	case '0':
		$four_url = $_SERVER['PHP_SELF']."?action=four&value=1&eid=".$USERS['eid'];
		$four_class = "no";
		$four_message = "No";							
	break;
	case '1':
		$four_url = $_SERVER['PHP_SELF']."?action=four&value=0&eid=".$USERS['eid'];
		$four_class = "yes";
		$four_message = "Yes";	
	break;
}

/* ----------------- ISSUER --------------------- */
switch ($USERS['issuer']) {
	case '0':
		$issuer_url = $_SERVER['PHP_SELF']."?action=issuer&value=1&eid=".$USERS['eid'];
		$issuer_class = "no";
		$issuer_message = "No";							
	break;
	case '1':
		$issuer_url = $_SERVER['PHP_SELF']."?action=issuer&value=0&eid=".$USERS['eid'];
		$issuer_class = "yes";
		$issuer_message = "Yes";	
	break;
}
																																							
/* -- Setup and Calculate the Administration access -- */
switch ($USERS['access']) {
	case '1':
		$level_message="Level 1";
		break;
	case '2':	
		$level_message="Level 2";									
		break;
	case '3':
		$level_message="Level 3";												
		break;
	default:
		$level_message="None";														
		break;							
}

/* ----------------- ACCESS STATUS --------------------- */						
switch ($USERS['status']) {
	case '0':
		if ($USERS['eid'] == '08745') {
			$status_url = "javascript:void(0);";		
		} else {
			$status_url = $_SERVER['PHP_SELF']."?action=status&value=1&eid=".$USERS['eid'];
		}
		$status_class = "yes";
		$status_message = "ACTIVE";	
	break;
	case '1':
		$status_url = $_SERVER['PHP_SELF']."?action=status&value=0&eid=".$USERS['eid'];
		$status_class = "no";
		$status_message = "DISABLE";
	break;
}
?>
<table width="240"  border="0">
  <tr class="BGAccentDark">
    <td height="25"><strong>&nbsp;User Information </strong></td>
  </tr>
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;<?= ucwords(strtolower($USERS['fst']." ".$USERS['lst'])); ?></td>
  </tr>
  <tr bgcolor="DFDFBF">
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;<?= $USERS['username']; ?></td>
  </tr>
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/key7-16-bw.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;<?= ($USERS['eid'] == '08745') ? '*******' : $USERS['password']; ?></td>
  </tr>
  <tr bgcolor="DFDFBF">
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/email.gif" width="17" height="16" border="0" align="absmiddle">&nbsp;<a href="mailto:<?= $USERS['email']; ?>" class="dark"><?= $email[0]; ?></a></td>
  </tr>
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><img src="../../images/wait.gif" width="18" height="20" border="0" align="absmiddle">&nbsp;<a href="<?= $requester_url; ?>" class="<?= $requester_class; ?>"><?= $requester_message; ?></a></td>
  </tr>
  <tr bgcolor="DFDFBF">
    <td bgcolor="#<?= $row_color; ?>"><img src="../../images/wait1.gif" width="18" height="20" border="0" align="absmiddle">&nbsp;<a href="<?= $one_url; ?>" class="<?= $one_class; ?>"><?= $one_message; ?></a></td>
  </tr>
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><img src="../../images/wait2.gif" width="18" height="20" border="0" align="absmiddle">&nbsp;<a href="<?= $two_url; ?>" class="<?= $two_class; ?>"><?= $two_message; ?></a></td>
  </tr>
  <tr bgcolor="DFDFBF">
    <td bgcolor="#<?= $row_color; ?>"><img src="../../images/wait3.gif" width="18" height="20" border="0" align="absmiddle">&nbsp;<a href="<?= $three_url; ?>" class="<?= $three_class; ?>"><?= $three_message; ?></a></td>
  </tr>
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><img src="../../images/wait4.gif" width="18" height="20" border="0" align="absmiddle">&nbsp;<a href="<?= $four_url; ?>" class="<?= $four_class; ?>"><?= $four_message; ?></a></td>
  </tr>
  <tr bgcolor="DFDFBF">
    <td bgcolor="#<?= $row_color; ?>"><img src="../../images/wait0.gif" width="18" height="20" border="0" align="absmiddle">&nbsp;<a href="<?= $issuer_url; ?>" class="<?= $issuer_class; ?>"><?= $issuer_message; ?></a></td>
  </tr>
  <tr bgcolor="FFFFFF">
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/groupinfo.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;<?= $level_message; ?></td>
  </tr>
  <tr bgcolor="DFDFBF">
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/required.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;<a href="<?= $status_url; ?>" class="<?= $status_class; ?>"><?= $status_message; ?></a></td>
  </tr>
</table>
<?php } else { ?>
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
    <td bgcolor="#<?= $row_color; ?>"><img src="/Common/images/userinfo.gif" width="16" height="16" border="0" align="absmiddle"><a href="../../Administration/forgotPassword.php?action=process&eid=<?= $USERS['eid']; ?>"><img src="../../images/moving_email.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?= $_SERVER['PHP_SELF']; ?>?eid=<?= $USERS['eid']; ?>&action=details" class="dark"><?= ucwords(strtolower($USERS['lst'].", ".$USERS['fst'])); ?>
    </a></td>
  </tr>
  <?php } ?>
</table>
<?php } ?>
</body>
</html>


<?php
/**
 * - Display Debug Information
 */
include_once('debug/footer.php');
?>