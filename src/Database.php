<?php

namespace App;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    // Patrón Singleton: Guardaremos la única instancia de la conexión aquí
    private static $instance = null;
    private $connection;

    // El constructor es privado para que nadie pueda hacer "new Database()" desde fuera
    private function __construct() {
        // 1. Cargar las variables del archivo .env
        // __DIR__ es "src", así que subimos un nivel (dirname) para ir a la raíz
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $db   = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'];

        try {
            // 2. Crear la conexión PDO
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar errores si algo falla
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas reales (Seguridad)
            ];
            
            $this->connection = new PDO($dsn, $user, $pass, $options);
            
        } catch (PDOException $e) {
            // Si falla, matamos el proceso y mostramos el error (solo para debug)
            die("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }

    // Método estático para obtener la instancia única
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Método para obtener el objeto PDO real y hacer consultas
    public function getConnection() {
        return $this->connection;
    }
}