<?php
namespace App;

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level; // <--- ESTO ES NUEVO EN MONOLOG 3

class LoggerService {
    private $logger;

    public function __construct($canal = 'caja_sorpresa') {
        // 1. Definimos la ruta del archivo log
        $logFile = dirname(__DIR__) . '/logs/app.log';

        // 2. Creamos el canal
        $this->logger = new Logger($canal);
        
        // 3. Configuramos el manejador
        // IMPORTANTE: Usamos Level::Debug para registrar TODO (Info, Errores, Warnings...)
        // Si usaras Logger::INFO aquí, te daría error en la versión nueva.
        $this->logger->pushHandler(new StreamHandler($logFile, Level::Debug));
    }

    // Métodos ayudantes para no tener que llamar a getLogger() todo el rato
    public function info($mensaje) {
        $this->logger->info($mensaje);
    }

    public function error($mensaje) {
        $this->logger->error($mensaje);
    }
    
    public function warning($mensaje) {
        $this->logger->warning($mensaje);
    }
    
    // Si alguna vez necesitas el objeto logger original de Monolog
    public function getMonologInstance() {
        return $this->logger;
    }
}