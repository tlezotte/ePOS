<?php
require_once 'DB.php';
$dsn_epos = 'mysql://amgadmin:amg01@192.168.71.14/ePOS';
$dbh_epos = DB::connect($dsn_epos);
$dbh_epos->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh_epos)) { die ($dbh_epos->getMessage()); } 
?>