<?php
/**
 * CLIENTE DE PRUEBA API (Requisito Fase 4)
 * Simula una app externa consumiendo nuestra API.
 */

// Cargamos el autoload (ajustar ruta si es necesario)
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use App\LoggerService; // <--- IMPORTANTE: Usamos tu nuevo servicio

// --- 1. REGISTRO LOG CON MONOLOG (Fase 5) ---
// Esto crea una entrada en logs/app.log usando la librería profesional
$logger = new LoggerService('test_client');
$logger->info("📡 INICIO: Se ha ejecutado el test de API desde el navegador.");

// Configuración de la URL de tu API
$apiUrl = 'http://localhost/CajaSorpresa/public/api/obtener_caja.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test API + Logs</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f9; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin-top: 20px; }
        code { background: #333; color: #0f0; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>

    <h1>📡 Probando API REST y Monolog</h1>
    <p>Si esto funciona, se habrá escrito una línea en <code>logs/app.log</code>.</p>

    <?php
    try {
        // --- 2. PETICIÓN HTTP (Guzzle) ---
        $client = new Client();
        echo "<p>📞 Llamando a: <strong>$apiUrl?tipo=geek</strong> ...</p>";
        
        $response = $client->request('GET', $apiUrl, [
            'query' => ['tipo' => 'geek']
        ]);

        $statusCode = $response->getStatusCode();
        echo "<p class='success'>✅ Conexión Exitosa (HTTP $statusCode)</p>";

        $body = $response->getBody();
        $data = json_decode($body, true);

        // --- 3. MOSTRAR RESULTADOS ---
        if (isset($data['exito']) && $data['exito'] == 1) {
            echo "<div class='box'>";
            echo "<h2>📦 Caja Recibida</h2>";
            echo "<p>Precio Total: <strong>" . $data['valor_real'] . "€</strong></p>";
            echo "<ul>";
            foreach ($data['productos'] as $prod) {
                echo "<li>" . $prod['nombre'] . " (" . $prod['precio'] . "€)</li>";
            }
            echo "</ul></div>";
            
            // Log de éxito
            $logger->info("✅ FIN: Caja recibida correctamente con " . count($data['productos']) . " productos.");
            
        } else {
            echo "<p class='error'>⚠️ La API respondió pero sin éxito.</p>";
            $logger->warning("⚠️ La API respondió false en 'exito'.");
        }

    } catch (Exception $e) {
        // Log de error real
        $logger->error("❌ ERROR CRÍTICO en test_api: " . $e->getMessage());
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    ?>

</body>
</html>