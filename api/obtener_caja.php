<?php
// Permitir que cualquiera se conecte (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require '../vendor/autoload.php';

use App\BoxGenerator;

// 1. Miramos si nos han pasado la categoría (ej: ?cat=1)
$categoriaId = isset($_GET['cat']) ? (int)$_GET['cat'] : 1;

try {
    // 2. Generamos la caja
    $generador = new BoxGenerator();
    $resultado = $generador->crearCaja($categoriaId);

    // 3. Enviamos la respuesta en JSON
    echo json_encode($resultado);

} catch (Exception $e) {
    // Si algo falla, enviamos error
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}