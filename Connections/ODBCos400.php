<?php
/* Database Settings */ 
$default['odbc_driver'] = "iSeries Access ODBC Driver";
$default['odbc_system'] = "os400";
$default['odbc_username'] = "USERNAME";
$default['odbc_password'] = "PASS";

/* DSN Settings */
$dsn = "DRIVER=".$default['odbc_driver'].";SYSTEM=".$default['odbc_system'];
//$dsn = "DRIVER=".$default['odbc_driver'].";SYSTEM=".$default['odbc_system'].";DEFAULTLIBRARIES=".$default['odbc_library'];

/* Connect to Database */
$conn=odbc_connect($dsn, $default['odbc_username'], $default['odbc_password']);
if (!$conn) { exit("Connection Failed: " . $conn); }
?>