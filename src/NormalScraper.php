<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class NormalScraper implements ScraperInterface {
    
    private $client;

    public function __construct() {
        $this->client = new Client([
            'timeout'  => 30.0,
            'verify'   => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ]
        ]);
    }

    public function scrape(string $url): array {
        $productos = [];

        try {
            $response = $this->client->request('GET', $url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // 1. Selector corregido gracias a tu detective (Nivel 2)
            $nodos = $crawler->filter('.item');
            
            echo "\n";

            $nodos->each(function (Crawler $node) use (&$productos) {
                try {
                    // --- TÍTULO ---
                    // En Curiosite suele estar en una clase .name o .title
                    $nodoTitulo = $node->filter('.name, .title, h5');
                    $nombre = $nodoTitulo->count() > 0 ? trim($nodoTitulo->text()) : null;

                    // --- ENLACE ---
                    // Nivel 1 dijo que hay un <a> directo
                    $nodoLink = $node->filter('a');
                    $urlProducto = $nodoLink->count() > 0 ? $nodoLink->attr('href') : null;

                    // --- PRECIO ---
                    // Usamos la clase que encontró el detective
                    $nodoPrecio = $node->filter('.price, .price-rating-container');
                    $precioTexto = $nodoPrecio->count() > 0 ? $nodoPrecio->text() : '0';

                    // --- IMAGEN ---
                    $nodoImagen = $node->filter('img');
                    $imagen = $nodoImagen->count() > 0 ? $nodoImagen->attr('src') : null;
                    // A veces usan data-src para lazy loading
                    if ($nodoImagen->count() > 0 && $nodoImagen->attr('data-src')) {
                         $imagen = $nodoImagen->attr('data-src');
                    }

                    // --- LIMPIEZA ---
                    $precioLimpio = preg_replace('/[a-zA-Z€\s]/u', '', $precioTexto);
                    $precioLimpio = str_replace(',', '.', $precioLimpio);
                    // Quedarse solo con números y puntos (ej: "69.99")
                    if (preg_match('/(\d+\.?\d+)/', $precioLimpio, $matches)) {
                        $precioVal = floatval($matches[0]);
                    } else {
                        $precioVal = 0.0;
                    }

                    // --- GUARDADO ---
                    if ($nombre && $precioVal > 0) {
                        $productos[] = [
                            'nombre'       => $nombre,
                            'precio'       => $precioVal,
                            'imagen_url'   => $imagen,
                            'url_origen'   => $urlProducto,
                            'categoria_id' => 1, // 1 = Normal
                            'condicion'    => 'Nuevo'
                        ];
                    }
                } catch (\Exception $e) {
                    // Ignorar errores puntuales
                }
            });

        } catch (\Exception $e) {
            echo "Error Curiosite: " . $e->getMessage();
        }

        return $productos;
    }
}