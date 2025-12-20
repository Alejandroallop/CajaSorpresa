<?php

require '../vendor/autoload.php';

use App\GeekScraper;
use App\NormalScraper;
use App\AdultScraper;
use App\ProductRepository;
use App\LoggerService;

// Configuración: Tiempo límite alto para que no se corte
set_time_limit(300); 

$logger = LoggerService::getLogger();
$repo = new ProductRepository();

echo "<h1>🚀 Iniciando Scraping Masivo...</h1><hr>";

// --- 1. GEEK (Omega Center) ---
try {
    echo "<h3>🕷️ 1. Procesando Geek (Omega Center)...</h3>";
    $geekScraper = new GeekScraper();
    $datosGeek = $geekScraper->scrape("https://www.omegacenter.es/1146-figuras");
    $guardados = $repo->guardar($datosGeek);
    echo "<p style='color:green'>✅ Geek: " . count($datosGeek) . " encontrados / <strong>$guardados nuevos guardados</strong>.</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error en Geek: " . $e->getMessage() . "</p>";
}

// --- 2. NORMAL (Curiosite) ---
try {
    echo "<h3>🎁 2. Procesando Normal (Curiosite)...</h3>";
    $normalScraper = new NormalScraper();
    // Usamos la URL de la categoría general que no da error 404
    $datosNormal = $normalScraper->scrape("https://www.curiosite.es/regalos/originales/");
    $guardados = $repo->guardar($datosNormal);
    echo "<p style='color:green'>✅ Normal: " . count($datosNormal) . " encontrados / <strong>$guardados nuevos guardados</strong>.</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error en Normal: " . $e->getMessage() . "</p>";
}

// --- 3. ADULTOS (Platanomelon) ---
try {
    echo "<h3>🔥 3. Procesando Adultos (Platanomelon)...</h3>";
    $adultScraper = new AdultScraper();
    $datosAdult = $adultScraper->scrape("https://www.platanomelon.com/collections/juguetes");
    
    if (count($datosAdult) > 0) {
        $guardados = $repo->guardar($datosAdult);
        echo "<p style='color:green'>✅ Adultos: " . count($datosAdult) . " encontrados / <strong>$guardados nuevos guardados</strong>.</p>";
    } else {
        echo "<p style='color:orange'>⚠️ Adultos: No se descargaron productos (¿Posible bloqueo de Proxy?). Prueba en casa.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:orange'>⚠️ Error en Adultos (Normal si estás en clase): " . $e->getMessage() . "</p>";
}

echo "<hr><h1>✨ ¡Proceso Terminado!</h1>";
echo "<p>Revisa la tabla 'productos' en phpMyAdmin para ver los resultados.</p>";