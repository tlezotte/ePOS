<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_test = "www.yourdomain.com";
$database_test = "Request";
$username_test = "req";
$password_test = "req123";
$test = mysql_pconnect($hostname_test, $username_test, $password_test) or trigger_error(mysql_error(),E_USER_ERROR); 
?>