<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_YBDB = "localhost";
$database_YBDB = "ybdb";
$username_YBDB = "root";
$password_YBDB = "mysql";
$YBDB = mysql_pconnect($hostname_YBDB, $username_YBDB, $password_YBDB) or trigger_error(mysql_error(),E_USER_ERROR); 
?>