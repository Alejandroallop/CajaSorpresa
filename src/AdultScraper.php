<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class AdultScraper implements ScraperInterface {
    
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

            // Shopify suele usar estas clases
            $nodos = $crawler->filter('.product-item, .product-card, .grid__item');
            
            echo "\n";

            $nodos->each(function (Crawler $node) use (&$productos) {
                try {
                    // --- TÍTULO ---
                    $nodoTitulo = $node->filter('.product-item__title, .product-card__title, h3 a');
                    $nombre = $nodoTitulo->count() > 0 ? trim($nodoTitulo->text()) : null;
                    
                    // --- ENLACE ---
                    // A veces el enlace está en el título o rodeando toda la tarjeta
                    $urlRelativa = $nodoTitulo->attr('href');
                    if (!$urlRelativa) {
                         $urlRelativa = $node->filter('a')->attr('href');
                    }
                    // Shopify da URLs relativas (/products/...), hay que poner el dominio
                    $urlProducto = 'https://www.platanomelon.com' . $urlRelativa;

                    // --- PRECIO ---
                    $nodoPrecio = $node->filter('.price, .product-item__price, .money');
                    $precioTexto = $nodoPrecio->count() > 0 ? $nodoPrecio->text() : '0';

                    // --- IMAGEN ---
                    $nodoImagen = $node->filter('img');
                    $imagen = $nodoImagen->count() > 0 ? $nodoImagen->attr('src') : null;
                    // Fix para imágenes que empiezan por //
                    if ($imagen && strpos($imagen, '//') === 0) {
                        $imagen = 'https:' . $imagen;
                    }

                    // --- LIMPIEZA ---
                    $precioLimpio = preg_replace('/[a-zA-Z€\s]/u', '', $precioTexto);
                    $precioLimpio = str_replace(',', '.', $precioLimpio);
                    // Quedarse con el primer precio si hay rangos
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
                            'categoria_id' => 3, // ID 3 = Adultos
                            'puntuacion'   => 5.0 // Valor por defecto
                        ];
                    }

                } catch (\Exception $e) {
                    // Ignorar fallos puntuales
                }
            });

        } catch (\Exception $e) {
            echo "Error conexión Platanomelon: " . $e->getMessage();
        }

        return $productos;
    }
}