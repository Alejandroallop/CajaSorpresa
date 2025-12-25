<?php
use PHPUnit\Framework\TestCase;

// Clase de prueba
class BoxTest extends TestCase {

    // TEST 1: Comprobar que una caja vacía vale 0 euros
    public function testCajaVaciaTienePrecioCero() {
        // Simulamos una caja vacía (array vacío)
        $productos = [];
        
        // Calculamos el total
        $total = 0;
        foreach($productos as $p) {
            $total += $p['precio'];
        }

        // El ROBOT comprueba: "Espero que $total sea igual a 0"
        $this->assertEquals(0, $total);
    }

    // TEST 2: Comprobar que suma bien los productos
    public function testSumaDePreciosEsCorrecta() {
        // Datos de prueba (falsos)
        $productos = [
            ['nombre' => 'Funko', 'precio' => 10],
            ['nombre' => 'Camiseta', 'precio' => 20]
        ];

        // Lógica a probar
        $total = 0;
        foreach($productos as $p) {
            $total += $p['precio'];
        }

        // El ROBOT comprueba: "Espero que 10+20 sea 30"
        $this->assertEquals(30, $total);
    }

    // TEST 3: Comprobar que detecta productos caros
    public function testDetectaProductoCaro() {
        $producto = ['nombre' => 'Figura Lujo', 'precio' => 50];
        
        $esCaro = $producto['precio'] > 40;

        // El ROBOT comprueba: "Espero que esto sea VERDADERO"
        $this->assertTrue($esCaro);
    }
}