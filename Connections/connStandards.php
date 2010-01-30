<?php
/* Database Settings */ 
$default['database'] = "Standards";
$default['server'] = "www.yourdomain.com";
$default['username'] = "standard2";
//$default['password'] = "def765";
$default['password'] = "rp4std";

/* -- PEAR DB connection -- */
require_once('DB.php');

$dsn_standards = "mysql://".$default['username'].":".$default['password']."@".$default['server']."/".$default['database'];
$dbh_standards = DB::connect($dsn_standards);
$dbh_standards->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh_standards)) { die ($dbh_standards->getMessage()); } 
?>