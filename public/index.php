<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja Sorpresa - Unboxing</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="main-header">
        <div class="logo-container">
            <span class="logo-icon">📦</span>
            <span class="logo-text">CajaSorpresa</span>
        </div>
        <nav>
            <a href="index.php" class="nav-link">Inicio</a>
            <a href="admin/" class="nav-link btn-admin-nav">🔒 Admin</a>
        </nav>
    </header>

    <main>
        <div class="app-container">
            
            <div id="pantalla-inicio" class="pantalla activa">
                <h1>🎁 Mystery Box</h1>
                <p>Elige tu destino y prueba tu suerte</p>
                
                <div class="categorias">
                    <button class="btn-cat" onclick="iniciarCaja(1)">🎁 Normal</button>
                    <button class="btn-cat geek" onclick="iniciarCaja(2)">🕷️ Geek</button>
                    <button class="btn-cat hot" onclick="iniciarCaja(3)">🔥 +18</button>
                </div>

                <div class="sobre-animado" id="sobre-icono">✉️</div>
                <p id="mensaje-carga" class="oculto">Generando tu caja...</p>
            </div>

            <div id="pantalla-juego" class="pantalla oculto">
                <h2>Objeto <span id="contador-actual">1</span> / 10</h2>
                
                <div class="escenario-carta">
                    <div class="carta" id="carta-activa" onclick="voltearCarta()">
                        <div class="cara detras"><span>?</span></div>
                        <div class="cara frente">
                            <img id="img-producto" src="" alt="Producto">
                            <h3 id="nombre-producto">Nombre</h3>
                            <p class="precio" id="precio-producto">0.00€</p>
                        </div>
                    </div>
                </div>

                <button id="btn-siguiente" class="btn-accion oculto" onclick="siguienteCarta()">Siguiente ▶</button>
            </div>

            <div id="pantalla-resumen" class="pantalla oculto">
                <h2>💰 ¡Botín Conseguido!</h2>
                <p>Valor total real: <strong id="valor-total">0.00€</strong></p>
                
                <div id="grid-productos" class="grid-final"></div>

                <button class="btn-accion" onclick="location.reload()">🔄 Abrir otra caja</button>
            </div>

        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <p>&copy; 2025 <strong>CajaSorpresa Inc.</strong> Todos los derechos reservados.</p>
            <p class="credits">Proyecto desarrollado con PHP y ❤️ por <span>Alejandro Alarcón López</span></p>
            <div class="socials">
                <a href="#">🐦</a>
                <a href="#">📷</a>
                <a href="#">💼</a>
            </div>
        </div>
    </footer>

    <script defer src="js/app.js"></script>
</body>
</html>