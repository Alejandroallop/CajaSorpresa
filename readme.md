# 🎁 Proyecto Caja Sorpresa

Aplicación web profesional para la gestión y venta de cajas sorpresa temáticas (Geek, Normal y Adultos). Desarrollada con **PHP 8 nativo**, **MySQL** y arquitectura **MVC** estricta.

## 🚀 Requisitos del Servidor
* PHP 8.1 o superior
* MySQL / MariaDB
* Composer (Gestor de dependencias)

## ⚠️ Nota sobre Redes Restringidas (Instituto/Oficina)
Si ejecutas el scraper desde una red educativa o corporativa (como el Wi-Fi del instituto), es probable que la **Categoría Adultos (Platanomelon)** falle o no descargue productos.
* **Causa:** El firewall del centro bloquea el acceso a webs para adultos.
* **Solución:** Probar el scraping desde una red doméstica o usar datos compartidos (móvil).

## 📦 Instalación y Despliegue

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/Alejandroallop/CajaSorpresa.git](https://github.com/Alejandroallop/CajaSorpresa.git)
    cd CajaSorpresa
    ```

2.  **Instalar dependencias:**
    ```bash
    composer install
    ```

3.  **Configurar Base de Datos:**
    * Crear una BBDD llamada `cajas_sorpresa_db`.
    * Importar el archivo `database.sql`.
    * Copiar el archivo `.env.example` a `.env` y configurar credenciales:
        ```ini
        DB_HOST=localhost
        DB_NAME=cajas_sorpresa_db
        DB_USER=root
        DB_PASS=
        DB_CHARSET=utf8mb4
        ```

4.  **Acceso Web:**
    * El punto de entrada público es la carpeta `/public`.
    * Acceder vía navegador: `http://localhost/CajaSorpresa/public/`

5.  **Panel de Administración:**
    * Ruta: `/public/admin/`
    * Credenciales por defecto: `admin` / `1234`
    * Incluye: Gráficas (Chart.js), CRUD de productos y visor de Logs.

## 🧪 Calidad de Código y Testing (Fase 5)
El proyecto implementa estándares profesionales de calidad de software:

### 1. Ejecución de Tests Unitarios (PHPUnit)
Para verificar la lógica de negocio (cálculo de precios, validaciones, etc.), ejecutar:
```bash
./vendor/bin/phpunit