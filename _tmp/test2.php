<?php
/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');

function startDelegate($fromEID, $toEID) {
	global $dbh;
	
	$app_sql = "SELECT controller, one, two, three, four 
				FROM Users 
				WHERE eid='" . $fromEID . "'";
	echo $app_sql . "<br>";
	$APP = $dbh->getRow($app_sql);
	print_r($APP);
echo "<br>-------------------------------- APP1 ---------------------------------<br>";
	if ($APP['one'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE app1='" . $fromEID . "' AND level IN ('controller', 'app1')";
		echo $request_sql . "<br>";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
//			print_r($REQUEST); echo "<br>";
			//$dbh->query("UPDATE Authorization SET app1='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			echo "UPDATE Authorization SET app1='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id'] . "<br>";
			$dbh->query("INSERT INTO Delegate (type_id, level, from_eid, to_eid) VALUES ('" . $REQUEST['type_id'] . "', 'app1', '" . $fromEID . "', '" . $toEID . "')");
			echo "INSERT INTO Delegate (type_id, level, from_eid, to_eid) VALUES ('" . $REQUEST['type_id'] . "', 'app1', '" . $fromEID . "', '" . $toEID . "')<br>";
		}
	}
echo "-------------------------------- APP2 ---------------------------------<br>";
	if ($APP['two'] == '1') {
		$request_sql = "SELECT DISTINCT(type_id) FROM Authorization WHERE app2='" . $fromEID . "' AND level IN ('controller', 'app1', 'app2')";
		echo $request_sql . "<br>";
		$request_query = $dbh->prepare($request_sql);
		$request_sth = $dbh->execute($request_query);

		while($request_sth->fetchInto($REQUEST)) {
//			print_r($REQUEST); echo "<br>";
			//$dbh->query("UPDATE Authorization SET app2='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id']);
			echo "UPDATE Authorization SET app2='" . $toEID . "' WHERE type_id=" . $REQUEST['type_id'] . "<br>";
			$dbh->query("INSERT INTO Delegate (type_id, level, from_eid, to_eid) VALUES ('" . $REQUEST['type_id'] . "', 'app2', '" . $fromEID . "', '" . $toEID . "')");
			echo "INSERT INTO Delegate (type_id, level, from_eid, to_eid) VALUES ('" . $REQUEST['type_id'] . "', 'app2', '" . $fromEID . "', '" . $toEID . "')<br>";
		}
	}
}

?>

<html>
<head>
      <title>Ajax Example</title>    
</head>
<body>
<?php startDelegate('99998', '08745'); ?>
</body>
</html>