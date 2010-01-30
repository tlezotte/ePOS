<?php
/**
 * - Database Connection
 */
require_once('../Connections/connDB.php');
/**
 * - Config Information
 */
require_once('../include/config.php'); 



/* ------------------ START DATABASE CONNECTIONS ----------------------- */
$value = $dbh->getAll("SELECT id, cer, purpose
                          FROM CER
					      WHERE cer IS NOT NULL
		                  ORDER BY cer DESC");
						  
    error_reporting(E_ALL);

    require_once 'json/JSON.php';

// create a new instance of Services_JSON
     $json = new Services_JSON();
     
     // convert a complexe value to JSON notation, and send it to the browser
     //$value = array('foo', 'bar', array(1, 2, 'baz'), array(3, array(4)));
     $output = $json->encode($value);
     
     print($output);
      // prints: ["foo","bar",[1,2,"baz"],[3,[4]]]
    
     // accept incoming POST data, assumed to be in JSON notation
     $input = file_get_contents('php://input', 1000000);
     $value = $json->decode($input);
	 
/**
 * - Disconnect from database
 */
$dbh->disconnect();
?>
