<?php

namespace App;

class BoxGenerator {
    
    private $repo;

    public function __construct() {
        $this->repo = new ProductRepository();
    }

    public function crearCaja(int $categoriaId): array {
        // 1. Obtenemos TODOS los productos de esa categoría
        $todos = $this->repo->obtenerPorCategoria($categoriaId);
        
        // 2. Barajamos el mazo (Shuffle)
        shuffle($todos);

        // 3. Cortamos las 10 primeras cartas
        // Si hay menos de 10, cogerá las que haya.
        $seleccionados = array_slice($todos, 0, 10);

        // 4. Calculamos el valor real (solo por curiosidad)
        $totalValor = 0;
        foreach ($seleccionados as $p) {
            $totalValor += (float) $p['precio'];
        }

        return [
            'exito'     => count($seleccionados) > 0,
            'cantidad'  => count($seleccionados),
            'valor_real'=> round($totalValor, 2),
            'productos' => $seleccionados
        ];
    }
}