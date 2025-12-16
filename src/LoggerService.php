<?php

namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;

class LoggerService {
    private static $logger;

    public static function getLogger(): Logger {
        if (self::$logger === null) {
            // Cargar configuración si es necesario (o usar ruta fija)
            $logFile = dirname(__DIR__) . '/logs/app.log';
            
            // Crear canal de log
            self::$logger = new Logger('CajaSorpresa');
            // Le decimos que escriba en logs/app.log
            self::$logger->pushHandler(new StreamHandler($logFile, Logger::INFO));
        }
        return self::$logger;
    }
}