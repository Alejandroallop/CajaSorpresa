<?php

namespace App;

interface ScraperInterface {
    // Todo scraper debe tener un método que reciba una URL y devuelva una lista de productos
    public function scrape(string $url): array;
}