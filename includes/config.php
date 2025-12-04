<?php
// Configuración para Railway MySQL
$host = getenv('MYSQLHOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: '3306';
$dbname = getenv('MYSQLDATABASE') ?: 'railway';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';

// Para desarrollo local
if (empty($host) || $host === 'localhost') {
    $host = 'localhost';
    $port = '3306';
    $username = 'root';
    $password = '';
    $dbname = 'evento_db';
}

define('DB_HOST', $host);
define('DB_PORT', $port);
define('DB_NAME', $dbname);
define('DB_USER', $username);
define('DB_PASSWORD', $password);
define('FOLIO_PREFIX', 'EVT-');
?>