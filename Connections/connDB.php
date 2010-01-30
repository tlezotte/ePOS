<?php
/* Database Settings */ 
$default['server'] = "www.yourdomain.com";
$default['username'] = "req";
$default['password'] = "req123";
$default['database'] = "Request";

/* -- PEAR DB connection -- */
require 'DB.php';
$dsn = "mysql://".$default['username'].":".$default['password']."@".$default['server']."/".$default['database'];
$dbh = DB::connect($dsn);
$dbh->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh)) { die ($dbh->getMessage()); }  

/* -- Default PHP database connection -- */
mysql_connect( $default['server'], $default['username'], $default['password']);
mysql_select_db($default['database']);
?>