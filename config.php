<?php
// CONFIGURACIÓN PARA RAILWAY - CORREGIDA

// Railway NO está configurando MYSQLHOST, MYSQLPORT, etc.
// En su lugar, Railway usa diferentes nombres de variables o no las está configurando

// PRIMERO: Verificar TODAS las variables posibles
echo "<!-- Debug: Checking all possible MySQL vars -->";

// Railway a veces usa estos nombres:
$possible_hosts = [
    getenv('MYSQLHOST'),
    getenv('MYSQL_HOST'),
    getenv('DATABASE_HOST'),
    getenv('DB_HOST'),
    getenv('host')
];

$possible_dbs = [
    getenv('MYSQLDATABASE'),
    getenv('MYSQL_DATABASE'),
    getenv('DATABASE_NAME'),
    getenv('DB_DATABASE'),
    getenv('database')
];

$possible_users = [
    getenv('MYSQLUSER'),
    getenv('MYSQL_USER'),
    getenv('DATABASE_USER'),
    getenv('DB_USERNAME'),
    getenv('user')
];

$possible_passwords = [
    getenv('MYSQLPASSWORD'),
    getenv('MYSQL_PASSWORD'),
    getenv('DATABASE_PASSWORD'),
    getenv('DB_PASSWORD'),
    getenv('password')
];

// Tomar el primer valor no nulo
$host = array_values(array_filter($possible_hosts))[0] ?? null;
$dbname = array_values(array_filter($possible_dbs))[0] ?? null;
$username = array_values(array_filter($possible_users))[0] ?? null;
$password = array_values(array_filter($possible_passwords))[0] ?? null;

// Si NO hay variables, usar configuración por defecto de Railway
if (!$host || !$dbname) {
    // Railway tiene una base de datos integrada
    // Para desarrollo, usar SQLite temporal o mostrar error claro
    
    // Opción 1: Usar SQLite temporal (para que al menos funcione)
    define('USE_SQLITE', true);
    
    // Opción 2: Mostrar error claro
    die(json_encode([
        'error' => true,
        'message' => 'Configuración de base de datos faltante en Railway',
        'instructions' => 'Por favor, crea una base de datos MySQL en Railway Dashboard y reconecta'
    ]));
}

// Si tenemos credenciales, usarlas
define('DB_HOST', $host);
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_NAME', $dbname);
define('DB_USER', $username ?: 'root');
define('DB_PASSWORD', $password ?: '');

// Para debugging
error_log("Railway DB Config - Host: " . DB_HOST . ", DB: " . DB_NAME);
?>