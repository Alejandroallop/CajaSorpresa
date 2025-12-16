<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

// 1. Descargamos la web real
$client = new Client(['verify' => false]);
$url = "https://www.curiosite.es/regalos/originales/";
echo "<h1>🕵️ Analizando Curiosite...</h1>";

try {
    $html = $client->request('GET', $url)->getBody()->getContents();
    $crawler = new Crawler($html);

    // 2. Buscamos un precio que sabemos que existe (ej: 10,95€ de los calcetines)
    // O buscamos un elemento común de precio
    $precio = $crawler->filter('.price, .product-price, span[itemprop="price"]')->first();

    if ($precio->count() > 0) {
        echo "<h3>✅ He encontrado un precio: " . $precio->text() . "</h3>";
        echo "<p>Voy a subir hacia arriba para encontrar la caja del producto:</p><ul>";
        
        $nodo = $precio;
        for ($i = 0; $i < 8; $i++) {
            $domElement = $nodo->getNode(0);
            if (!$domElement || !$domElement->parentNode) break;
            
            $padre = $domElement->parentNode;
            $nodo = new Crawler($padre);
            
            $tag = $padre->nodeName;
            $clase = $padre->hasAttribute('class') ? $padre->getAttribute('class') : '---';
            
            echo "<li>Nivel $i: &lt;$tag&gt; | Clase: <span style='color:blue'>'$clase'</span></li>";
        }
        echo "</ul>";
    } else {
        echo "<h3>❌ No encuentro precios. Curiosite usa clases muy raras.</h3>";
        // Intento de rescate: imprimir todas las clases del body para pistas
        echo "<textarea style='width:100%; height:200px'>" . htmlspecialchars($html) . "</textarea>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}