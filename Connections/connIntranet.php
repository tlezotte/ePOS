<?php
require_once 'DB.php';
$dsn_int = 'mysql://phpnuke:nuke123@11.1.1.17/phpnuke';
$dbh_int = DB::connect($dsn_int);
$dbh_int->setFetchMode(DB_FETCHMODE_ASSOC);
if (DB::isError($dbh_int)) { die ($dbh_int->getMessage()); } 
?>