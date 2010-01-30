<?php 
if ($_SESSION['request_access'] == '3') {  

/**
 * - Database Connection
 */
require_once('../../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../../include/config.php'); 
/**
 * - Check User Access
 */
require_once('../../security/check_user.php');



if ($_POST['action'] == 'console') {
	switch ($_POST['task']) {
		case 'switchStatus':
			if ($_POST['status'] != '0') { 					
				setRequestStatus($_GET['id'], $_POST['status']);		// Update PO status
				setAuthLevel($_GET['id'], $_POST['status']);			// Update Auth Level
			}
		break;
		case 'switchLevel':
			if ($_POST['level'] != '0') { 					
				setAuthLevel($_GET['id'], $_POST['level']);				// Update Auth Level
			}
		break;		
		case 'switchUser':
			list($eid, $fullname, $username, $group) = split(':', $_POST['switchData']);
			$_SESSION['eid'] = $eid;
			$_SESSION['fullname'] = $fullname;
			$_SESSION['username'] = $username;
			$_SESSION['request_role'] = $group;
		break;
		case 'switchApprover':
			if (array_key_exists('app1', $_POST)) { $dbh->query("UPDATE Authorization SET app1='" . $_POST['app1'] . "' WHERE type_id=" . $_POST['id']); }
			if (array_key_exists('app2', $_POST)) { $dbh->query("UPDATE Authorization SET app2='" . $_POST['app2'] . "' WHERE type_id=" . $_POST['id']); }
			if (array_key_exists('app3', $_POST)) { $dbh->query("UPDATE Authorization SET app3='" . $_POST['app3'] . "' WHERE type_id=" . $_POST['id']); }
			if (array_key_exists('app4', $_POST)) { $dbh->query("UPDATE Authorization SET app4='" . $_POST['app4'] . "' WHERE type_id=" . $_POST['id']); }			
		break;
	}
	//echo "<meta http-equiv=\"refresh\" content=\"0\">";
}

/* ------------- Getting PO information ------------- */
$PO = $dbh->getRow("SELECT *, DATE_FORMAT(reqDate,'%M %e, %Y') AS _reqDate
				    FROM PO
				    WHERE id = ?",array($_GET['id']));						
/* ------------- Getting Authoriztions for above PO ------------- */
$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($PO['id']));
?>

<table width="240"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td nowrap="nowrap" class="center"><div align="center"><a href="../home.php"><img src="/Common/images/Company200.gif" alt="Your Company" name="Company" width="200" height="50" border="0" id="Company" /></a></div></td>
  </tr>
  <tr>
    <td nowrap="nowrap"><div align="center">
      <?= $default['title1']; ?>
    </div></td>
  </tr>
  <tr>
    <td nowrap="nowrap"><div align="center"><strong>Edit Requisition</strong></div></td>
  </tr>
</table>
<?php if (array_key_exists('id', $_GET)) { ?>
<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchStatusForm" id="SwitchStatusForm">
  <table border="0">
    <tr>
      <td width="20" align="center"><img src="/Common/images/Company_Bullet.gif" width="11" height="15" /></td>
      <td><span style="font-weight:bold">Requisition Status</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><select name="status" id="status">
          <option value="">Select One</option>
          <option value="N" <?= ($PO['status'] == 'N') ? selected : ''; ?>>New</option>
          <option value="A" <?= ($PO['status'] == 'A') ? selected : ''; ?>>Approve</option>
          <option value="O" <?= ($PO['status'] == 'O') ? selected : ''; ?>>Kickoff</option>
          <option value="C" <?= ($PO['status'] == 'C') ? selected : ''; ?>>Cancel</option>
      </select></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="action" type="hidden" id="action" value="console">
        <input name="task" type="hidden" id="task" value="switchStatus">
        <input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
        <input name="change" type="submit" value="Change" class="button" id="change" /></td>
    </tr>
  </table>
</form>
<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchLevelForm" id="SwitchLevelForm">
  <table border="0">
    <tr>
      <td width="20" align="center"><img src="/Common/images/Company_Bullet.gif" width="11" height="15" /></td>
      <td><span style="font-weight:bold">Approval Level</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><select name="level" id="level">
          <option value="">Select One</option>
          <option value="controller" <?= ($AUTH['level'] == 'controller') ? selected : ''; ?>>Controller</option>
          <option value="app1" <?= ($AUTH['level'] == 'app1') ? selected : ''; ?>>Approver 1</option>
          <option value="app2" <?= ($AUTH['level'] == 'app2') ? selected : ''; ?>>Approver 2</option>
          <option value="app3" <?= ($AUTH['level'] == 'app3') ? selected : ''; ?>>Approver 3</option>
          <option value="app4" <?= ($AUTH['level'] == 'app4') ? selected : ''; ?>>Approver 4</option>
      </select></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="action" type="hidden" id="action" value="console">
        <input name="task" type="hidden" id="task" value="switchLevel">
        <input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
        <input name="change2" type="submit" value="Change" class="button" id="change2" /></td>
    </tr>
  </table>
</form>
<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchAppForm" id="SwitchAppForm">
  <table border="0">
    <tr>
      <td width="20" align="center"><img src="/Common/images/Company_Bullet.gif" width="11" height="15" /></td>
      <td><span style="font-weight:bold">Approvers</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><?= displayApprover($_GET['id'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><?= displayApprover($_GET['id'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><?= displayApprover($_GET['id'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><?= displayApprover($_GET['id'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="action" type="hidden" id="action" value="console">
        <input name="task" type="hidden" id="task" value="switchApprover">
        <input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
        <input name="change3" type="submit" value="Change" class="button" id="change3" /></td>
    </tr>
  </table>
</form>
<?php } ?>
<?php } ?>
