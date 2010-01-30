<?php
if ($_SESSION['request_access'] >= 2) {  

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
			/* ------------- Getting Authoriztions for above PO ------------- */
			$AUTH = $dbh->getRow("SELECT * FROM Authorization WHERE type_id = ? and type = 'PO'",array($_POST['id']));
		
			if ($_POST['controller'] != $AUTH['controller']) { 
				$app_sql="UPDATE Authorization SET controller='" . $_POST['controller'] . "' WHERE type_id=" . $_POST['id'];
				$dbh->query($app_sql);
				
				if ($default['debug_capture'] == 'on') {
					debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($app_sql)));	// Record transaction for history
				}					 
			}
			if ($_POST['app1'] != $AUTH['app1']) {
				$app_sql="UPDATE Authorization SET app1='" . $_POST['app1'] . "' WHERE type_id=" . $_POST['id']; 
				$dbh->query($app_sql);
				
				if ($default['debug_capture'] == 'on') {
					debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($app_sql)));	// Record transaction for history
				}				
			}
			if ($_POST['app2'] != $AUTH['app2']) { 
				$app_sql="UPDATE Authorization SET app2='" . $_POST['app2'] . "' WHERE type_id=" . $_POST['id'];
				$dbh->query($app_sql);
				
				if ($default['debug_capture'] == 'on') {
					debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($app_sql)));	// Record transaction for history
				}				
			}
			if ($_POST['app3'] != $AUTH['app3']) { 
				$app_sql="UPDATE Authorization SET app3='" . $_POST['app3'] . "' WHERE type_id=" . $_POST['id'];
				$dbh->query($app_sql);
				
				if ($default['debug_capture'] == 'on') {
					debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($app_sql)));	// Record transaction for history
				}	
			}
			if ($_POST['app4'] != $AUTH['app4']) { 
				$app_sql="UPDATE Authorization SET app4='" . $_POST['app4'] . "' WHERE type_id=" . $_POST['id'];
				$dbh->query($app_sql);
				
				if ($default['debug_capture'] == 'on') {
					debug_capture($_SESSION['eid'], $_POST['id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($app_sql)));	// Record transaction for history
				}				
			}			
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
<div id="noPrint">
<div id="adminPanel" style="display:none;" class="BGAccentDarkBorder">
	<div id="leftPanelContent">
	  <div>
	  <table border="0" align="center" cellpadding="0" cellspacing="10">
        <tr>
	    <?php if (array_key_exists('id', $_GET)) { ?>		
          <td valign="top"><form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchStatusForm" id="SwitchStatusForm">
            <div class="mod">
			<div class="hd"><a class="mod-title" href="javascript:void(0);">Change Status</a></div>
			<table border="0" align="center">
              <tr>
                <td><select name="status" id="status">
                    <option value="">Select One</option>
                    <option value="N" <?= ($PO['status'] == 'N') ? selected : ''; ?>>New</option>
                    <option value="A" <?= ($PO['status'] == 'A') ? selected : ''; ?>>Approve</option>
                    <option value="O" <?= ($PO['status'] == 'O') ? selected : ''; ?>>Kickoff</option>
                    <option value="C" <?= ($PO['status'] == 'C') ? selected : ''; ?>>Cancel</option>
                  </select></td>
              </tr>
            </table>
			<div class="t-shadow"></div></div>			  
            <input name="action" type="hidden" id="action" value="console">
			<input name="task" type="hidden" id="task" value="switchStatus">
			<input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
			<input name="submit22" type="image" id="submit22" src="../images/button.php?i=w70.png&l=Change" border="0">
          </form></td>
          <td valign="top"><form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchLevelForm" id="SwitchLevelForm">
            <div class="mod">
			<div class="hd"><a class="mod-title" href="javascript:void(0);">Approval Level</a></div>		  
            <table border="0" align="center">
              <tr>
                <td><select name="level" id="level">
                    <option value="">Select One</option>
                    <option value="controller" <?= ($AUTH['level'] == 'controller') ? selected : ''; ?>>Controller</option>
                    <option value="app1" <?= ($AUTH['level'] == 'app1') ? selected : ''; ?>>Approver 1</option>
                    <option value="app2" <?= ($AUTH['level'] == 'app2') ? selected : ''; ?>>Approver 2</option>
                    <option value="app3" <?= ($AUTH['level'] == 'app3') ? selected : ''; ?>>Approver 3</option>
                    <option value="app4" <?= ($AUTH['level'] == 'app4') ? selected : ''; ?>>Approver 4</option>
                  </select></td>
              </tr>
            </table>
			<div class="t-shadow"></div></div>			  
            <input name="action" type="hidden" id="action" value="console">
			<input name="task" type="hidden" id="task" value="switchLevel">
			<input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
			<input name="submit" type="image" id="submit" src="../images/button.php?i=w70.png&l=Change" border="0">
          </form></td>
          <td valign="top"><form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchAppForm" id="SwitchAppForm">
            <div class="mod">
			<div class="hd"><a class="mod-title" href="javascript:void(0);">Change Approvers</a></div>		  
            <table border="0" align="center">
              <tr>
                <td><?= displayApprover($_GET['id'], 'controller', $AUTH['controller'], $AUTH['controllerDate']); ?></td>
              </tr>
              <tr>
                <td><?= displayApprover($_GET['id'], 'app1', $AUTH['app1'], $AUTH['app1Date']); ?></td>
              </tr>
              <tr>
                <td><?= displayApprover($_GET['id'], 'app2', $AUTH['app2'], $AUTH['app2Date']); ?></td>
              </tr>
              <tr>
                <td><?= displayApprover($_GET['id'], 'app3', $AUTH['app3'], $AUTH['app3Date']); ?></td>
              </tr>
              <tr>
                <td><?= displayApprover($_GET['id'], 'app4', $AUTH['app4'], $AUTH['app4Date']); ?></td>
              </tr>
            </table>
			<div class="t-shadow"></div></div>			  
            <input name="action" type="hidden" id="action" value="console">
			<input name="task" type="hidden" id="task" value="switchApprover">
			<input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
			<input name="submit" type="image" id="submit" src="../images/button.php?i=w70.png&l=Change" border="0">
          </form></td>
		  <?php } ?>
          <td valign="top"><form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" name="SwitchUserForm" id="SwitchUserForm">
            <div class="mod">
			<div class="hd"><a class="mod-title" href="javascript:void(0);">Switch User</a></div>		  
            <table border="0" align="center">
              <tr>
                <td nowrap><select name="switchData" id="switchData">
                    <option value="0">Select One</option>
                    <?php
				$employees_sth = $dbh->execute($employees_query);
				while($employees_sth->fetchInto($EMPOLYEES)) {
					print "<option value=\"".$EMPOLYEES['eid'].":".caps($EMPOLYEES['fst']." ".$EMPOLYEES['lst']).":".$EMPOLYEES['username'].":".$EMPOLYEES['role']."\" ".$selected.">".caps($EMPOLYEES['lst'].", ".$EMPOLYEES['fst'])."</option>";
				}
				?>
                </select></td>
              </tr>
            </table>
			<div class="t-shadow"></div></div>				  
			<input name="action" type="hidden" id="action" value="console">
			<input name="task" type="hidden" id="task" value="switchUser">
			<input name="id" type="hidden" id="id" value="<?= $_GET['id']; ?>">
			<input name="submit" type="image" id="submit" src="../images/button.php?i=w70.png&l=Switch" border="0">
          </form></td>
        </tr>
      </table>
	  </div>
	</div>
</div>
</div>
<?php } ?>