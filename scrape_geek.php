<?php

require 'vendor/autoload.php';

use App\GeekScraper;
use App\ProductRepository;
use App\LoggerService;

// 1. Iniciamos el Logger
$logger = LoggerService::getLogger();
$logger->info("----- INICIO DEL PROCESO DE SCRAPING (GEEK) -----");

try {
    // 2. Ejecutamos el Scraper
    $scraper = new GeekScraper();
    $url = "https://www.omegacenter.es/1146-figuras";
    
    echo "Descargando datos de $url...\n";
    $productos = $scraper->scrape($url);
    
    $totalEncontrados = count($productos);
    $logger->info("Se han descargado $totalEncontrados productos de la web.");
    echo "Encontrados: $totalEncontrados productos.\n";

    // 3. Guardamos en Base de Datos
    if ($totalEncontrados > 0) {
        $repo = new ProductRepository();
        $guardados = $repo->guardar($productos);
        
        $logger->info("Se han guardado $guardados productos nuevos en la base de datos.");
        echo "¡Éxito! Se han guardado $guardados productos nuevos en la BBDD.\n";
    } else {
        $logger->warning("No se encontraron productos para guardar.");
    }

} catch (Exception $e) {
    $logger->error("Error fatal en el script: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}

$logger->info("----- FIN DEL PROCESO -----");