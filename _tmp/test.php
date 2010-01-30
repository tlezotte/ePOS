<?php

/**
 * - Database Connection
 */
require_once('../Connections/connDB.php'); 
/**
 * - Config Information
 */
require_once('../include/config.php'); 

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
//$query = $dbh->prepare("SELECT DISTINCT controller FROM Standards.Controller");
//$sth = $dbh->execute($query);
//while($sth->fetchInto($DATA)) {
//	echo "UPDATE Users SET role='controller' WHERE eid=" . $DATA['controller'] . "<br>";
//	$dbh->query("UPDATE Users SET role='controller' WHERE eid=" . $DATA['controller']);
//}
$plant = '9';
$department = '73';

$CONTROLLER = getController($plant, $department);

print_r($CONTROLLER);
?>
</body>
</html>
