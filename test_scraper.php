<?php

require 'vendor/autoload.php';

use App\GeekScraper;

// Instanciamos tu scraper
$scraper = new GeekScraper();
$urlObjetivo = "https://www.omegacenter.es/1146-figuras";

echo "<h1>🕷️ Iniciando Scraping de Omega Center...</h1>";

// Ejecutamos la magia
$datos = $scraper->scrape($urlObjetivo);

// Mostramos resultados
if (count($datos) > 0) {
    echo "<h2>¡Éxito! Se han encontrado " . count($datos) . " productos.</h2>";
    echo "<pre>";
    print_r($datos); // Muestra el array en bruto
    echo "</pre>";
} else {
    echo "<h2>❌ No se encontraron productos. Revisa los selectores.</h2>";
}