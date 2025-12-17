<?php

namespace App;

use PDO;

class ProductRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function guardar(array $productos): int {
        $guardados = 0;
        
        // 1. Preparamos una sentencia SQL que acepte TODOS los campos posibles
        // Usamos parámetros con nombre (:param)
        $sql = "INSERT INTO productos (nombre, precio, imagen_url, url_origen, categoria_id, franquicia, condicion, puntuacion) 
                VALUES (:nombre, :precio, :imagen, :url, :cat, :fran, :cond, :punt)";
        
        $stmt = $this->pdo->prepare($sql);

        foreach ($productos as $producto) {
            // Evitar duplicados
            if ($this->existe($producto['url_origen'])) {
                continue;
            }

            try {
                // 2. Ejecutamos usando '?? null'
                // Esto significa: "Si existe la clave, úsala. Si no, pon NULL".
                $stmt->execute([
                    ':nombre' => $producto['nombre'],
                    ':precio' => $producto['precio'],
                    ':imagen' => $producto['imagen_url'],
                    ':url'    => $producto['url_origen'],
                    ':cat'    => $producto['categoria_id'],
                    
                    // AQUÍ ESTÁ EL ARREGLO MÁGICO 👇
                    ':fran'   => $producto['franquicia'] ?? null, 
                    ':cond'   => $producto['condicion'] ?? null,
                    ':punt'   => $producto['puntuacion'] ?? null
                ]);
                $guardados++;
            } catch (\Exception $e) {
                // Si falla una inserción, la saltamos y seguimos
            }
        }
        
        return $guardados;
    }

    private function existe(string $url): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE url_origen = :url");
        $stmt->execute([':url' => $url]);
        return $stmt->fetchColumn() > 0;
    }
    public function obtenerPorCategoria(int $categoriaId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE categoria_id = :cat");
        $stmt->execute([':cat' => $categoriaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}