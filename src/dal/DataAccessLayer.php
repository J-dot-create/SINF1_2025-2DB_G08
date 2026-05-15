<?php
require_once __DIR__ . '/../../config/db_config.php';

/**
 * Data Access Layer (DAL)
 * Gere todas as interações diretas com a base de dados MySQL utilizando mysqli.
 */
class DataAccessLayer {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Definir o charset para utf8mb4 para garantir a codificação correta dos caracteres (ex: acentos, emojis)
        $this->conn->set_charset("utf8mb4");
    }

    /**
     * Executa uma query SELECT e devolve todos os resultados como um array associativo.
     */
    public function executeSelect($query, $params = [], $types = "") {
        $stmt = $this->conn->prepare($query);
        
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $data;
    }

    /**
     * Executa uma query INSERT, UPDATE ou DELETE.
     * Devolve o número de linhas afetadas.
     */
    public function executeNonQuery($query, $params = [], $types = "") {
        $stmt = $this->conn->prepare($query);
        
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
