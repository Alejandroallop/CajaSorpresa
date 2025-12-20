<?php
/**
 * CLIENTE DE PRUEBA API (Requisito Fase 4)
 * Este script simula ser una aplicación externa consumiendo nuestra API.
 */

require '../vendor/autoload.php';

use GuzzleHttp\Client;

// Configuración: ¿Dónde está nuestra propia API?
// NOTA: Si usas otro puerto (ej: 8080), cámbialo aquí.
$apiUrl = 'http://localhost/CajaSorpresa/public/api/obtener_caja.php';

echo "<h1>📡 Probando API REST...</h1>";

try {
    // 1. Iniciamos Guzzle (el cliente HTTP)
    $client = new Client();

    // 2. Hacemos la petición GET simulando pedir una caja GEEK
    echo "<p>📞 Llamando a: <strong>$apiUrl?tipo=geek</strong> ...</p>";
    
    $response = $client->request('GET', $apiUrl, [
        'query' => ['tipo' => 'geek']
    ]);

    // 3. Verificamos el código de estado (200 = OK)
    $statusCode = $response->getStatusCode();
    echo "<p>✅ Estado HTTP: <span style='color:green; font-weight:bold'>$statusCode</span></p>";

    // 4. Leemos el JSON que nos ha devuelto
    $body = $response->getBody();
    $data = json_decode($body, true);

    // 5. Mostramos el resultado bonito
    echo "<h3>🎁 Respuesta del Servidor (JSON Decodificado):</h3>";
    echo "<div style='background:#f4f4f4; padding:15px; border:1px solid #ccc; font-family:monospace;'>";
    
    if (isset($data['contenido'])) {
        echo "<strong>Caja Generada:</strong> " . $data['contenido'] . "<br>";
        echo "<strong>Total Precio:</strong> " . $data['total_precio'] . "€<br>";
        echo "<hr><strong>Productos:</strong><br>";
        foreach ($data['productos'] as $prod) {
            echo "- " . $prod['nombre'] . " (" . $prod['precio'] . "€)<br>";
        }
    } else {
        // Por si da error la API
        print_r($data);
    }
    
    echo "</div>";

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error al conectar con la API: " . $e->getMessage() . "</p>";
    echo "<small>Consejo: Verifica que la URL de \$apiUrl sea correcta.</small>";
}
?>