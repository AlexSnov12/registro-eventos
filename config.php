<?php
// config.php - Configuración para Railway y desarrollo local

// 1. PRIMERO: Intentar obtener las variables de Railway
// Railway proporciona automáticamente estas variables cuando conectas una base de datos MySQL.
$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT') ?: '3306'; // Puerto por defecto
$dbname = getenv('MYSQLDATABASE');
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');

// 2. SEGUNDO: Si las variables de Railway NO están configuradas, usar configuración local
// Esto te permite seguir probando la app en XAMPP/MAMP.
if (empty($host) || empty($dbname)) {
    $host = 'localhost';
    $port = '3306';
    $dbname = 'evento_db'; // Asegúrate de que esta base de datos exista en tu local
    $username = 'root';
    $password = ''; // Contraseña común en XAMPP
}

// 3. Definir las constantes para usar en database.php
define('DB_HOST', $host);
define('DB_PORT', $port);
define('DB_NAME', $dbname);
define('DB_USER', $username);
define('DB_PASSWORD', $password);

// Configuración de la aplicación
define('SITE_NAME', 'Registro de Evento');
define('FOLIO_PREFIX', 'EVT-');

// Para depuración (solo en entornos de desarrollo)
// error_log("Usando DB: " . DB_HOST . " | " . DB_NAME);
?>