<?php
// Configuración automática para Railway o local

// Railway proporciona estas variables automáticamente
$railway_host = getenv('MYSQLHOST');
$railway_port = getenv('MYSQLPORT');
$railway_db   = getenv('MYSQLDATABASE');
$railway_user = getenv('MYSQLUSER');
$railway_pass = getenv('MYSQLPASSWORD');

if ($railway_host && $railway_db) {
    // Usar configuración de Railway
    $host = $railway_host;
    $port = $railway_port ?: '3306';
    $dbname = $railway_db;
    $username = $railway_user ?: 'root';
    $password = $railway_pass ?: '';
} else {
    // Configuración local (desarrollo)
    $host = 'localhost';
    $port = '3306';
    $dbname = 'evento_db';
    $username = 'root';
    $password = '';
}

// Definir constantes
define('DB_HOST', $host);
define('DB_PORT', $port);
define('DB_NAME', $dbname);
define('DB_USER', $username);
define('DB_PASSWORD', $password);

// Para debugging (solo en desarrollo)
if (php_sapi_name() === 'cli-server') { // Servidor PHP integrado
    error_log("Database Config - Host: " . DB_HOST . ", DB: " . DB_NAME);
}
?>