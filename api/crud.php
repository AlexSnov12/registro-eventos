<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Habilitar errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $action = $_POST['action'] ?? '';
    
    $response = ['success' => false, 'message' => 'Acción no válida'];
    
    switch ($action) {
        case 'create':
            $nombre = trim($_POST['nombre'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            
            if (empty($nombre) || empty($telefono)) {
                $response['message'] = 'Nombre y teléfono son requeridos';
                break;
            }
            
            if (!preg_match('/^\d{10}$/', $telefono)) {
                $response['message'] = 'Teléfono debe tener 10 dígitos';
                break;
            }
            
            $folio = $db->generateFolio();
            
            $stmt = $conn->prepare("INSERT INTO registros (folio, nombre, telefono) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$folio, $nombre, $telefono])) {
                $response['success'] = true;
                $response['message'] = 'Registro creado';
                $response['folio'] = $folio;
                $response['data'] = [
                    'folio' => $folio,
                    'nombre' => $nombre,
                    'telefono' => $telefono
                ];
            } else {
                $response['message'] = 'Error al crear';
            }
            break;
            
        case 'read':
            $folio = trim($_POST['folio'] ?? '');
            
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
            $stmt = $conn->query("SELECT * FROM registros ORDER BY fecha_registro DESC LIMIT 20");
            $registros = $stmt->fetchAll();
            
            $response['success'] = true;
            $response['message'] = 'OK';
            $response['data'] = $registros;
            break;
            
        case 'delete':
            $folio = trim($_POST['folio'] ?? '');
            
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
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
    error_log("CRUD Error: " . $e->getMessage());
}

echo json_encode($response);
?>