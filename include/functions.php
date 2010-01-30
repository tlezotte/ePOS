<?php
/**
 * - Load Common Functions
 */
include_once('/var/www/Common/PHP/functions.php');


/* ------------------ START FUNCTIONS ----------------------- */
/**
 * - Update the Checklist Database
 */
function checklist($task) {
	global $dbh;
	
	$sql = "UPDATE Checklist 
			SET $task='1'
			WHERE id=".$_SESSION['vt_id'];
	$dbh->query($sql);
	$_SESSION[$task] = 1;
}

/**
 * - Check number of online users
 */
function enterPONumber($PO, $POLIST, $TYPE_ID, $PURCHASER) {
	global $dbh;
	
	/* Update the PO */
	$dbh->query("UPDATE PO 
				 SET po='".$PO."', status='A', polist='".$POLIST."', purchaser='".$PURCHASER."'
				 WHERE id = ".$TYPE_ID);
	$dbh->query("UPDATE Authorization 
				 SET issuerDate=NOW() 
				 WHERE type_id = ".$TYPE_ID);
}	


/**
 * - Check Authorization
 */
function CheckAuth($auth_eid, $auth_yn, $auth_com, $auth_date) {
	if (isset($auth_date)) {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib(' ".date("F d, Y - g:i:s A", strtotime($auth_date))."', CAPTION, 'Submission Date', BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C');\" onMouseout=\"return nd();\">".
	       "<img src=\"/Common/images/datetime.gif\" border=\"0\" align=\"absmiddle\"></a>";
	}
	if ($auth_yn == 'yes') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Approved Comments', BGCOLOR, '#006600', CGCOLOR, '#006600');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/approved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} elseif ($auth_yn == 'no') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Non-Approved Comments', BGCOLOR, '#FF0000', CGCOLOR, '#FF0000');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/notapproved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} 
/* 	if (isset($auth_eid)) {
	  echo "<img src=\"../images/waiting.gif\" width=\"18\" height=\"18\" alt=\"Waiting...\">";
	} */
}


/**
 * - Check Resend
 */
function CheckResend($auth_eid, $auth_yn, $auth_com, $auth_date) {
	if (isset($auth_date)) {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib(' ".$auth_date."', CAPTION, 'Submission Date', BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C');\" onMouseout=\"return nd();\">".
	       "<img src=\"../images/calendar.gif\" border=\"0\" align=\"absmiddle\"></a>";
	}
	if ($auth_yn == 'yes') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Approved Comments', BGCOLOR, '#006600', CGCOLOR, '#006600');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/approved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} elseif ($auth_yn == 'no') {
	  echo "<a href=\"javascript:void(0);\" onMouseover=\"return overlib('".$auth_com."', CAPTION, 'Non-Approved Comments', BGCOLOR, '#FF0000', CGCOLOR, '#FF0000');\" onMouseout=\"return nd();\">".	
	       "<img src=\"../images/notapproved.gif\" border=\"0\" align=\"absmiddle\"></a>";
	} 
}


/**
 * - Check Authorization Level
 */
function CheckAuthLevel($auth_level) {
	echo ($auth_level < 0) ? disabled : $blank;
}


/**
 * - Reset Session
 */
function clearSession() {
	/* 
	Set Session variables to regular variables
	Session variables will be unset at end of page
	but "username", "eid" and "access" will be reset
	*/
	$get_fullname = $_SESSION['fullname'];
	$get_username = $_SESSION['username'];
	$get_access = $_SESSION['request_access'];
	$get_eid = $_SESSION['eid'];
	//$get_vacation = $_SESSION['request_vacation'];
	$get_group = $_SESSION['request_role'];
		
	/* Unsets current session variables */
	session_unset();
	
	/* Reset username and access so the user does not need to relogin */
	$_SESSION['fullname'] = $get_fullname;
	$_SESSION['username'] = $get_username;
	$_SESSION['request_access'] = $get_access;
	$_SESSION['eid'] = $get_eid;
	//$_SESSION['request_vacation'] = $get_vacation;
	$_SESSION['request_role'] = $get_group;	
}	


/**
 * - Add approved Item information to AS400
 */
function recordAS400($ID) {
	global $dbh;		// Connection to MySQL
	global $conn;		// ODBC connection to AS400

				
	$VENDOR = $dbh->getRow("SELECT v.BTVEND AS id, v.BTNAME AS name
							FROM PO p, Standards.Vendor v
							WHERE p.sup=v.BTVEND
							  AND p.id=".$ID);
	$AUTH = $dbh->getRow("SELECT app1, app2, app3, app4
						  FROM Authorization
						  WHERE type_id=".$ID);
						  
	
	/* Connect to AS400 with ODBC drivers */					  
	$dsn = "DRIVER=".$default['odbc_driver'].";SYSTEM=".$default['odbc_system'];
	$conn=odbc_connect($dsn, $default['odbc_username'], $default['odbc_password']);
	
	/* Process each item for approved Request */					  
	$line_item = 0;
	while($items_sql->fetchInto($ITEM)) {
		$line_item++;																// Line item count
		$lineItem = (strlen($line_item) == 1) ? "0".$line_item : $line_item;		// Line item count plus zero if one digit
		$itemID = $ID . $lineItem;													// Request ID plus line item count
		
		/* Insert item into AS/400 */
		$results_sql = "INSERT INTO ZZ_TEST.PORQI (JIREQ#, 
												   JIQDAT, 
												   JIQTYO, 
												   JIOUNT, 
												   JIPT#, 
												   JIUPRC, 
												   JIPUNT, 
												   JIREQR, 
												   JIAPRV, 
												   JIVND#, 
												   JIVNAM, 
												   JIVPT#, 
												   JISTS, 
												   JIITM#, 
												   JIUSER, 
												   JIISTR, 
												   JIAPBY,
												   JICRCM, 
												   JITAXG, 
												   JITAXR, 
												   JIPLNT) 
										   VALUES (".$itemID.", 
												   CURDATE(), 
												   ".$ITEM['qty'].", 
												   '".$ITEM['unit']."', 
												   '".$ITEM['part']."', 
												   ".$ITEM['price'].", 
												   '".$ITEM['unit']."', 
												   'EXT', 
												   'Y', 
												   '".$VENDOR['id']."', 
												   '".strtoupper($VENDOR['name'])."', 
												   '".$ITEM['manuf']."', 
												   'N', 
												   ".$line_item.", 
												   'LEZOTTET', 
												   'STX', 
												   '".$lastAuthorize."', 
												   '1', 
												   'EXP', 
												   '0', 
												   'DFT')";
		$result = odbc_exec($conn, $results_sql);
	}
	
	odbc_close($conn);
	odbc_close_all();			
}
 
function employeeInfo($field, $eid) {
	global $dbh;
	
	$employee = $dbh->getRow("SELECT $field AS info FROM Standards.Employees WHERE eid=".$eid);
								 
	return $employee['info'];
}


/**
 * - Display required icons for Approvals area
 */
function showMailIcon($approver, $approver_eid, $approver_name, $type_id) {
	$html="<a href=\"resend.php?approval=$approver&eid=$approver_eid&type_id=$type_id\" onmouseover=\"return overlib('Resend request to $approver_name', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');\" onmouseout=\"nd();\"><img src=\"../images/resend_email.gif\" width=\"19\" height=\"16\" border=\"0\" align=\"absmiddle\"></a>";
	
	return $html;
}


/**
 * - Display comments icons for Approvals area
 */
function showCommentIcon($approver, $approver_name, $request_id) {
	$html="<a href=\"comments.php?eid=$approver&request_id=$request_id&type=private\" title=\"Send private message to $approver_name\" onmouseover=\"return overlib('Send private message to $approver_name', TEXTPADDING, 10, WIDTH, 300, WRAPMAX, 300, AUTOSTATUS, BGCOLOR, '#E68B2C', CGCOLOR, '#E68B2C', FGCOLOR, '#FFFF99');\" onmouseout=\"nd();\" rel=\"gb_page_center[675,325]\"><img src=\"../images/comments.gif\" width=\"19\" height=\"16\" border=\"0\" align=\"absmiddle\"></a>";
	
	return $html;
}


/**
 * - Display Approver comments for Approvals area
 */
function displayAppComment($level, $action_level, $auth, $current_comment, $date) {
	$char_length = '40';
	$display = is_null($data) ? display : none;			// Check to see if approver, approved Request
	$appCom = $action_level . "Com";					// Set Comment variable
	
	if ($level == $action_level AND $_SESSION['eid'] == $auth AND $display == 'display') {
		$comment = "<input name=\"Com\" id=\"Com\" type=\"text\" size=\"75\" maxlength=\"75\">";
	} else {
		if (strlen($current_comment) > $char_length) {
				$comment  = caps(substr(stripslashes($current_comment), 0, $char_length));
				$comment .= "...<img src=\"../images/bubble.gif\" width=14 height=17 border=0 align=absmiddle title='Item Description|" . caps(htmlspecialchars(stripslashes($current_comment))) . "'>";
		} else {
				$comment = caps(stripslashes($current_comment));
		}
	}
	
	return $comment;												  
} 


/**
 * - Display Yes/No buttons for Approvals area
 */
function displayAppButtons($id, $level, $action_level, $auth, $date) {
	$display = is_null($data) ? display : none;			// Check to see if approver, approved Request

	// Check Approver to Logged in user
	if ($level == $action_level AND $_SESSION['eid'] == $auth) {
$output = <<< END_OF_HTML
	<div id="cmdSubmit" style="display:$display">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	  <td><input name="yes" type="image" src="/Common/images/gtk-yes.gif" border="0" title="Approve this Request"></td>
	  <td><input name="no" type="image" src="/Common/images/gtk-no.gif" border="0" title="Deny this Request"></td>
	  <!--<td><input name="hold" type="image" src="/Common/images/gtk-pause.gif" border="0" title="Put a Hold on this Request"></td>-->
	</tr>
	</table>
<!--	<input name="request_id" type="hidden" value="$id">
	<input name="auth" type="hidden" value="$level">
	<input name="stage" type="hidden" value="update">-->
	</div>
END_OF_HTML;
	} else {
		$output = "&nbsp;";
	}
	
	return $output;
}



/**
 * - Check GL code to see if it's valid for each Item
 */
function checkCategory($item, $conbr, $department, $cat) {
	global $dbh;
	
	/* --- Split category into account and suffix --- */
	list($account, $suffix) = split("-", $cat);						

	/* --- Check Standards.COA database for GL Code --- */
	$DATA = $dbh->getRow("SELECT coa_account 
						  FROM Standards.COA 
						  WHERE coa_plant='" . $conbr . "' 
							AND coa_department='" . $department . "' 
							AND coa_account='" . $account . "' 
							AND coa_suffix='" . $suffix . "'");
	
	/* --- Display red X is GL code is invalid --- */	
	if ($DATA['coa_account'] != $account) {
		$output  = "<img src=\"/Common/images/gtk-no.gif\" title=\"Invalid General Ledger Code\">";
		$output .= "<input name=\"item" . $item . "_no\" type=\"hidden\" value=\"item" . $item . "_no\"";
		
		return $output;
	}
}



/**
 * - Display approvers name for Approvals area
 */
function displayApprover($id, $stage, $approver, $date) {
	global $dbh;
	
	/* Set SQL level from $approver */
	switch ($stage) {
		case 'app1': $level='one'; break;
		case 'app2': $level='two'; break;
		case 'app3': $level='three'; break;
		case 'app4': $level='four'; break;
		case 'app5': $level='five'; break;
		case 'app6': $level='six'; break;
		case 'app7': $level='seven'; break;
		case 'app8': $level='eight'; break;
		case 'controller': $level='controller'; break;
	}
	
	/* Getting approver from Users */
	if ($level != 'controller') {							 
		$query = $dbh->prepare("SELECT U.eid, E.fst, E.lst
								FROM Users U
								 INNER JOIN Standards.Employees E ON U.eid = E.eid
								WHERE U.$level = '1' AND U.status = '0' AND E.status = '0'
								ORDER BY E.lst ASC");
	} else {
		$query = $dbh->prepare("SELECT distinct E.eid, E.fst, E.lst
								FROM Standards.Controller c
								 INNER JOIN Standards.Employees E ON E.eid=c.controller
								WHERE E.status = '0'
								ORDER BY E.lst ASC");
	}
	
	/* Generate HTML output */
	if (is_null($date)) {
		$output  = "<select name=\"$stage\" id=\"$stage\">";
		$output .= "	<option value=\"0\">Select One</option>";
		
		$sth = $dbh->execute($query);
		while($sth->fetchInto($DATA)) {
		$selected = ($approver == $DATA['eid']) ? selected : $blank;
			$output .= "	<option value=\"" . $DATA['eid'] . "\" " . $selected . ">" . caps($DATA['lst'] . ", " . $DATA['fst']) . "</option>";
		}
		
		$output .= "</select>";
	} else {
		$sql = "SELECT e.eid, CONCAT(e.fst, ' ', e.lst) AS fullname
				FROM Authorization a
					INNER JOIN Standards.Employees e on a.$stage=e.eid
				WHERE a.type_id=" . $id; 
		$DATA = $dbh->getRow($sql); 

		$output  = caps($DATA['fullname']);
		$output .= "<input name=\"" . $stage . "\" type=\"hidden\" value=\"" . $DATA['eid'] . "\" />";	
	}

	return $output;	
}


/**
 * - Convert Request Status
 */
function reqStatus($status) {

	switch ($status) {
		case N: $output = "New"; break;
		case A: $output = "Approved"; break;
		case O: $output = "Vendor Kickoff"; break;
		case R: $output = "Received"; break;
		case X: $output = "Not Approved"; break;
		case C: $output = "Canceled"; break;         
	}
	
	return $output;
}



/**
 * - Display number of comments made by each Approver 
 */
function checkComments($type_id, $eid) {
	global $dbh;
	
	/* Getting Comments Information */
	$POST = $dbh->getRow("SELECT count(id) AS count
						   FROM Postings 
						   WHERE request_id = ".$type_id." 
							   AND type = 'global'
							   AND eid = " . $eid);
	
	return $POST['count'];
}



/**
 * - Display Hours sence Approval
 */
function elapsedApprovalTime($id) {
	global $dbh;
	
	$AUTH = $dbh->getRow("SELECT app1, app1Date, TIMEDIFF(NOW(), app1Date) AS app1Diff,
								 app2, app2Date, TIMEDIFF(NOW(), app2Date) AS app2Diff,
								 app3, app3Date, TIMEDIFF(NOW(), app3Date) AS app3Diff,
								 app4, app4Date, TIMEDIFF(NOW(), app4Date) AS app4Diff
						  FROM Authorization WHERE type_id=$id");
	
	if (strlen($AUTH['app4']) == 5) {
		$time = $AUTH['app4Diff'];
	} elseif (strlen($AUTH['app3']) == 5) {
		$time = $AUTH['app3Diff'];
	} elseif (strlen($AUTH['app2']) == 5) {
		$time = $AUTH['app2Diff'];
	} elseif (strlen($AUTH['app1']) == 5) {
		$time = $AUTH['app1Diff'];
	}
	
	$output = round($time, 1);									// Round time to hours only
	
	if ($output > '24' AND $output < '168') {
		$format = "<strong>" . $output . "</strong>";			// Format output if greater than 24 hours
		$output = $format;
	} elseif ($output > '168') {
		$format = "<strong class=\"red\">" . $output . "</strong>";			// Format output if greater than 24 hours
		$output = $format;	
	}
	
	return $output;
}



/**
 * - Set the Authorization level for Approvals
 */
function setAuthLevel($id, $level) {
	global $dbh;
	
	$auth_sql="UPDATE Authorization SET level='" . $level . "' WHERE type_id=" . $id;
	
	$dbh->query($auth_sql);

	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $id, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($auth_sql)));		// Record transaction for history
	}		
}



/**
 * - Set the Request status
 */
function setRequestStatus($id, $status) {
	global $dbh;
	
	$status_sql="UPDATE PO SET status='". $status ."' WHERE id=" . $id;
	
	$dbh->query($status_sql);

	if ($default['debug_capture'] == 'on') {
		debug_capture($_SESSION['eid'], $id, 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($status_sql)));		// Record transaction for history
	}		
}



/**
 * - Set Controller
 */
function setController($id, $controller) {
	global $dbh;
	
	$dbh->query("UPDATE Authorization SET controller='" . $controller . "' WHERE type_id=" . $id);
}



/**
 * - Display Hours sence Approval
 */
function getController($plant, $department) {
	global $dbh;
	
	/* ---------- Check for Plant and Departmant Controller ---------- */
	$CHECK1=$dbh->getRow("SELECT c.department, c.plant, e.eid, CONCAT(e.fst,' ',e.lst) AS fullname, e.email
						  FROM Standards.Controller c
						    INNER JOIN Standards.Employees e ON e.eid=c.controller
						  WHERE c.plant='" . $plant . "' AND c.department='" . $department . "'");
	
	/* ---------- Check for Plant Controller ---------- */					 
	if ($CHECK1['plant'] != $plant) {
		$CHECK2=$dbh->getRow("SELECT c.department, c.plant, e.eid, CONCAT(e.fst,' ',e.lst) AS fullname, e.email
							  FROM Standards.Controller c
								INNER JOIN Standards.Employees e ON e.eid=c.controller
							  WHERE c.plant='" . $plant . "' AND c.department='00'");
		
		$output = $CHECK2;			// Return Plant Controller
	} else {
		$output = $CHECK1;			// Return Plant/Department Controller
	}	
	
	return $output;
}



/**
 * - Switch delegation of authority - ON
 */
function startDelegate($fromEID, $toEID) {
	global $dbh;
	global $default;
	
	/* ---------- GET USERS APPROVAL LEVELS ---------- */
	$app_sql = "SELECT controller, one, two, three, four FROM Users WHERE eid='" . $fromEID . "'";
	$APP = $dbh->getRow($app_sql);

	/* ---------- CONTROLLER APPROVER ---------- */
	if ($APP['controller'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE controller='" . $fromEID . "' AND level IN ('controller')";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
			$dbh->query("UPDATE Authorization SET controller='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $REQUEST['type_id'] . "', 'controller', '" . $fromEID . "', '" . $toEID . "')";
			$dbh->query($sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $REQUEST['type_id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
			}			
		}
	}
	/* ---------- FIRST APPROVER ---------- */
	if ($APP['one'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE app1='" . $fromEID . "' AND level IN ('controller', 'app1')";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
			$dbh->query("UPDATE Authorization SET app1='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $REQUEST['type_id'] . "', 'app1', '" . $fromEID . "', '" . $toEID . "')";
			$dbh->query($sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $REQUEST['type_id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
			}			
		}
	}
	/* ---------- SECOND APPROVER ---------- */
	if ($APP['two'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE app2='" . $fromEID . "' AND level IN ('controller', 'app1', 'app2')";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
			$dbh->query("UPDATE Authorization SET app2='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $REQUEST['type_id'] . "', 'app2', '" . $fromEID . "', '" . $toEID . "')";
			$dbh->query($sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $REQUEST['type_id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
			}			
		}
	}
	/* ---------- THIRD APPROVER ---------- */
	if ($APP['three'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE app3='" . $fromEID . "' AND level IN ('controller', 'app1', 'app2', 'app3')";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
			$dbh->query("UPDATE Authorization SET app3='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $REQUEST['type_id'] . "', 'app3', '" . $fromEID . "', '" . $toEID . "')";
			$dbh->query($sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $REQUEST['type_id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
			}			
		}
	}
	/* ---------- FOURTH APPROVER ---------- */
	if ($APP['four'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE app4='" . $fromEID . "' AND level IN ('controller', 'app1', 'app2', 'app3', 'app4')";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
			$dbh->query("UPDATE Authorization SET app4='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			$sql = "INSERT INTO Delegate (recorded, type_id, level, from_eid, to_eid) VALUES (NOW(), '" . $REQUEST['type_id'] . "', 'app4', '" . $fromEID . "', '" . $toEID . "')";
			$dbh->query($sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $REQUEST['type_id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
			}			
		}
	}	
}



/**
 * - Switch delegation of authority - OFF
 */
function stopDelegate($fromEID, $toEID) {
	global $dbh;
	global $default;

	$delegate_sql = "SELECT d.type_id, d.level, d.from_eid, d.to_eid, a.level AS current, p.status
					 FROM Delegate d
					   INNER JOIN Authorization a ON a.type_id=d.type_id
					   INNER JOIN PO p ON p.id=d.type_id
					 WHERE d.from_eid='" . $toEID . "'";
	$delegate_query = $dbh->prepare($delegate_sql);
	$delegate_sth = $dbh->execute($delegate_query);
	
	while($delegate_sth->fetchInto($DATA)) {
//	print_r($DATA); echo "<br><br>";
		$level = ($DATA['level'] != 'controller') ? substr($DATA['level'],3) : 0;
		$current = ($DATA['current'] != 'controller') ? substr($DATA['current'],3) : 0;
//		echo "level: " . $level . "<br>";
//		echo "current: " . $current . "<br>";
		
		if ($current <= $level AND $DATA['status'] == 'N') {
			$sql = "UPDATE Authorization SET " . $DATA['level'] . "='" . $toEID . "' WHERE type_id=" . $DATA['type_id'];
			$dbh->query($sql);
			
			if ($default['debug_capture'] == 'on') {
				debug_capture($_SESSION['eid'], $DATA['type_id'], 'debug', $_SERVER['PHP_SELF'], addslashes(htmlentities($sql)));		// Record transaction for history
			}			
		}
	}
}


function generate_xdp_file($id, $output) {
	global $default;
	
	$filename = $default['XDP_UPLOAD'] . "/" . $id. ".xdp";
	
	if (is_writable($default['XDP_UPLOAD'])) {
		if (!$handle = fopen($filename, 'w')) {
		echo "generate";
			$_SESSION['error'] = "Cannot open the file (".$id.".xdp)";
		}
	
		if (fwrite($handle, $output) === FALSE) {
		echo "write";
			$_SESSION['error'] = "Cannot generate the file (".$id.".xdp)";
		}
	
		fclose($handle);
	} else {
		$_SESSION['error'] = "Cannot write the file (".$id.".xdp)";	
	}
	
	if (array_key_exists('error', $_SESSION)) {
		header("Location: ../error.php");	
	}
}


/**
 * - Switch delegation of authority - OFF
 */
function getTrackingInformation($id) {
	global $dbh;
	global $default;

	$TRACKING = $dbh->getAll("SELECT * FROM TrackShipments WHERE type_id=" . $id . " AND track_status='0'");
	
	if (count($TRACKING) != 0) {
		for ($t=0; $t < count($TRACKING); $t++) {
			$track_number = $TRACKING[$t][track_number];		// Tracking number
			$track_latest = $TRACKING[$t][track_latest];		// Tracking status
	
			if (!preg_match("/DELIVERED/i", $track_latest)) {
				$trackingRSSName=$track_number . ".xml";										// RSS file name
				$trackingRSSLocation=$default['FS_HOME'] . "/proxy/" . $trackingRSSName;		// Filesystem location for RSS file
				$trackingURL=$default['track_shipment'] . $track_number;						// URL location for remotre RSS file
				
				getProxyDATA($trackingURL, $trackingRSSName);									// Save RSS file locally
				
				require_once('minixml/minixml.inc.php');										// XML parsing library
				$parsedDoc = new MiniXMLDoc();
				$parsedDoc->fromFile($trackingRSSLocation);										// Get local RSS file from HD
				$description =& $parsedDoc->getElementByPath('rss/channel/item/description');	// Get description field
				$link =& $parsedDoc->getElementByPath('rss/channel/item/link');					// Get link field
	
				$splitDescription = explode(": ", $description->toString());					// Split date from status
				$splitDate = preg_replace("/\bPackage update on\b/", "", $splitDescription[0]);	// Remove Package update on
				
				// Save tracking information
				$sql = "UPDATE TrackShipments 
						SET track_date='" . mysql_real_escape_string(trim(strip_tags($splitDate))) . "',
							track_latest='" . mysql_real_escape_string(trim(strip_tags($splitDescription[1]))) . "',
							track_url='" . mysql_real_escape_string(trim(strip_tags($link->toString()))) . "'
						WHERE track_id=" . $TRACKING[$t][track_id];
				$dbh->query($sql);
			}
		}
		
		// Get latest tracking information
		$TRACKING = $dbh->getAll("SELECT * FROM TrackShipments WHERE type_id=" . $id . " AND track_status='0' ORDER BY track_id DESC");
		
		return $TRACKING;
	}
}
/* ------------------ END FUNCTIONS ----------------------- */	


/**
 * - Load Email Functions
 */
include_once('functionsEmail.php');	
?>