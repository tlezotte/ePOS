<?php 
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
 * - Check User Access
 */
require_once('../security/check_access1.php');

/**
 * - Database Connection
 */
require_once('../Connections/connStandards.php');  

/**
 * - Config Information
 */
require_once('../include/config.php'); 


/* ----- START DATABASE ACCESS ----- */ 
$employees_sql = "SELECT eid, email 
				  FROM Employees";
$Dbg->addDebug($employees_sql,DBGLINE_QUERY,__FILE__,__LINE__);		//Debug SQL
$Dbg->DebugPerf(DBGLINE_QUERY);									//Start debug timer  						  
$employees_query = $dbh->prepare($employees_sql);							
$Dbg->DebugPerf(DBGLINE_QUERY);									//Stop debug timer    
/* ----- END DATABASE ACCESS ----- */
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>

<body>
<?php
$employees_sth = $dbh->execute($employees_query);
while($employees_sth->fetchInto($EMPOLYEES)) {
	$email = split('@', $EMPOLYEES['email']);	
	$new_email = $email[0].'@'.$default['email_domain'];
	$sql = "UPDATE Employees SET email='$new_email' WHERE eid=".$EMPOLYEES['eid'];
	print $sql."<br>";
//	$dbh->query($sql);
}
?>
</body>
</html>
