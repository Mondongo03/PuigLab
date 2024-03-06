<?php 
// DB credentials.
//define('DB_HOST','fdb15.eohost.com');
define('DB_HOST','127.0.0.1');
define('DB_USER','initbox');
define('DB_PASS','12345678');
define('DB_NAME','armentum');
// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}
?>
