<?php
require 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;

if (!file_exists('debug_omega.html')) {
    die("❌ Error: No encuentro debug_omega.html");
}

$html = file_get_contents('debug_omega.html');
$crawler = new Crawler($html);

echo "<h1>🕵️ Informe del Detective (Intento 2)</h1>";

// 1. Buscamos el precio
$precio = $crawler->filter('.price, .current-price, .product-price, .amount')->first();

if ($precio->count() > 0) {
    echo "<h3>✅ He encontrado un precio: " . $precio->text() . "</h3>";
    echo "<p>Analizando jerarquía superior (Padres):</p>";
    echo "<ul>";
    
    $nodoActual = $precio;
    
    // 2. Subimos manualmente 8 niveles
    for ($i = 0; $i < 8; $i++) {
        // Sacamos el elemento nativo de PHP (DOMElement)
        $domElement = $nodoActual->getNode(0);
        
        // Si no hay padre, paramos (hemos llegado al final)
        if (!$domElement || !$domElement->parentNode) break;
        
        $padreDom = $domElement->parentNode;
        $nodoActual = new Crawler($padreDom); // Lo volvemos a convertir a Crawler
        
        $tag = $padreDom->nodeName;
        // Sacamos la clase si existe
        $clase = $padreDom->hasAttribute('class') ? $padreDom->getAttribute('class') : '---';
        
        echo "<li>Nivel $i: Etiqueta <strong>&lt;$tag&gt;</strong> | Clase: <span style='color:blue'>'$clase'</span></li>";
        
        // Si vemos algo que huele a producto, lo marcamos
        if (strpos($clase, 'product') !== false || strpos($clase, 'item') !== false || strpos($clase, 'col-') !== false) {
             echo " 👉 <strong>¡POSIBLE CONTENEDOR!</strong><br>";
        }
    }
    echo "</ul>";
} else {
    echo "<h3>❌ Sigo sin encontrar el precio. Revisa que el HTML no esté vacío.</h3>";
}