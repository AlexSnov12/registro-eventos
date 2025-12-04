<?php
// Habilitar errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 0); // En producción, los errores van a logs

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir la conexión a la base de datos
require_once __DIR__ . '/database.php';

// Respuesta por defecto
$response = [
    'success' => false,
    'message' => 'Acción no especificada',
    'data' => null
];

try {
    // Obtener la acción
    $input = $_POST;
    if (empty($input) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Para pruebas GET
        $input = $_GET;
    }
    
    $action = $input['action'] ?? '';
    
    // Crear instancia de base de datos
    $db = new Database();
    $conn = $db->getConnection();
    
    switch ($action) {
        case 'create':
            $nombre = trim($input['nombre'] ?? '');
            $telefono = trim($input['telefono'] ?? '');
            
            if (empty($nombre) || empty($telefono)) {
                $response['message'] = 'Nombre y teléfono son requeridos';
                break;
            }
            
            // Validar teléfono (10 dígitos)
            if (!preg_match('/^\d{10}$/', $telefono)) {
                $response['message'] = 'El teléfono debe tener 10 dígitos';
                break;
            }
            
            // Generar folio único
            $folio = 'EVT-' . strtoupper(substr(md5(uniqid()), 0, 6));
            
            $stmt = $conn->prepare("INSERT INTO registros (folio, nombre, telefono) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$folio, $nombre, $telefono])) {
                $response['success'] = true;
                $response['message'] = 'Registro creado exitosamente';
                $response['folio'] = $folio;
                $response['data'] = [
                    'folio' => $folio,
                    'nombre' => $nombre,
                    'telefono' => $telefono
                ];
            } else {
                $response['message'] = 'Error al crear el registro';
            }
            break;
            
        case 'read':
            $folio = trim($input['folio'] ?? '');
            
            if (empty($folio)) {
                $response['message'] = 'Folio requerido';
                break;
            }
            
            $stmt = $conn->prepare("SELECT * FROM registros WHERE folio = ?");
            $stmt->execute([$folio]);
            $registro = $stmt->fetch();
            
            if ($registro) {
                $response['success'] = true;
                $response['message'] = 'Registro encontrado';
                $response['data'] = $registro;
            } else {
                $response['message'] = 'Registro no encontrado';
            }
            break;
            
        case 'list':
            $stmt = $conn->query("SELECT * FROM registros ORDER BY fecha_registro DESC LIMIT 50");
            $registros = $stmt->fetchAll();
            
            $response['success'] = true;
            $response['message'] = 'OK';
            $response['data'] = $registros;
            break;
            
        case 'delete':
            $folio = trim($input['folio'] ?? '');
            
            if (empty($folio)) {
                $response['message'] = 'Folio requerido';
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM registros WHERE folio = ?");
            
            if ($stmt->execute([$folio])) {
                if ($stmt->rowCount() > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Registro eliminado';
                } else {
                    $response['message'] = 'Registro no encontrado';
                }
            } else {
                $response['message'] = 'Error al eliminar';
            }
            break;
            
        default:
            $response['message'] = 'Acción no válida. Acciones: create, read, list, delete';
            break;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
    error_log("CRUD Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
}

// Enviar respuesta
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>