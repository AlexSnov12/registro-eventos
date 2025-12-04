<?php
require_once __DIR__ . '/config.php';

class Database {
    private $connection;
    
    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, DB_USER, DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            $this->createTable();
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS registros (
            id INT AUTO_INCREMENT PRIMARY KEY,
            folio VARCHAR(20) UNIQUE NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(15) NOT NULL,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_folio (folio)
        )";
        
        $this->connection->exec($sql);
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function generateFolio() {
        $prefix = FOLIO_PREFIX;
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        $folio = $prefix . $random;
        
        // Verificar que no exista
        while ($this->folioExists($folio)) {
            $random = strtoupper(substr(md5(uniqid()), 0, 6));
            $folio = $prefix . $random;
        }
        
        return $folio;
    }
    
    private function folioExists($folio) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM registros WHERE folio = ?");
        $stmt->execute([$folio]);
        return $stmt->fetchColumn() > 0;
    }
}
?>