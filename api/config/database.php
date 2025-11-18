<?php
/**
 * Configuración de conexión a la base de datos MySQL para XAMPP
 *
 * Asegúrate de:
 * 1. Tener XAMPP instalado y Apache + MySQL corriendo
 * 2. Crear la base de datos 'proyecto_justin' en phpMyAdmin
 * 3. Ejecutar el script database.sql
 */

class Database {
    // Configuración de conexión
    private $host = "localhost";
    private $db_name = "proyecto_justin";
    private $username = "root";
    private $password = ""; // Por defecto XAMPP no tiene contraseña
    private $conn;

    /**
     * Obtiene la conexión a la base de datos
     * @return PDO|null Conexión PDO o null si falla
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // Configurar el charset UTF-8
            $this->conn->exec("set names utf8");

            // Configurar modo de error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
