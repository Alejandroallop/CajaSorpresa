<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class GeekScraper implements ScraperInterface {
    
    private $client;

    public function __construct() {
        $this->client = new Client([
            'timeout'  => 30.0,
            'verify'   => false, // Ignorar SSL en local
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ]
        ]);
    }

    public function scrape(string $url): array {
        $productos = [];

        try {
            // 1. Descargar HTML
            $response = $this->client->request('GET', $url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // 2. Buscamos el contenedor
            $nodos = $crawler->filter('.js-product-miniature');

            echo "\n";

            if ($nodos->count() === 0) {
                echo ""; // Aquí podrías poner un log de error si quisieras
            }

            $nodos->each(function (Crawler $node, $i) use (&$productos) {
                try {
                    // --- TÍTULO Y ENLACE ---
                    // Buscamos específicamente la etiqueta <a> dentro del título
                    $nodoTitulo = $node->filter('.product-title a, h3.h3 a, .product-name a');
                    
                    $nombre = $nodoTitulo->count() > 0 ? trim($nodoTitulo->text()) : null;
                    
                    // Extraemos el enlace (href) para que sea único
                    $urlProducto = $nodoTitulo->count() > 0 ? $nodoTitulo->attr('href') : 'https://www.omegacenter.es';

                    // --- PRECIO ---
                    $nodoPrecio = $node->filter('.product-price-and-shipping, .price');
                    $precioTexto = $nodoPrecio->count() > 0 ? $nodoPrecio->text() : '0';

                    // --- IMAGEN ---
                    $nodoImagen = $node->filter('img');
                    $imagen = $nodoImagen->count() > 0 ? $nodoImagen->attr('src') : null;
                    if ($nodoImagen->count() > 0 && $nodoImagen->attr('data-src')) {
                        $imagen = $nodoImagen->attr('data-src');
                    }

                    // --- LIMPIEZA ---
                    $precioLimpio = preg_replace('/[a-zA-Z€\s]/u', '', $precioTexto); 
                    $precioLimpio = str_replace(',', '.', $precioLimpio); 
                    
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
                            'url_origen'   => $urlProducto, // Usamos la URL única
                            'categoria_id' => 2, 
                            'franquicia'   => 'General'
                        ];
                    }

                } catch (\Exception $e) {
                    // Error silencioso en producto individual
                }
            });

        } catch (\Exception $e) {
            echo "Error conexión: " . $e->getMessage();
        }

        return $productos;
    }
}