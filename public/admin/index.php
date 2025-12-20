<?php
require '../../vendor/autoload.php';
use App\Database;

session_start();
// 🔒 SEGURIDAD
if (!isset($_SESSION['usuario_logueado'])) { header("Location: login.php"); exit; }

$db = Database::getInstance();
$conn = $db->getConnection();

// 1. OBTENER TOTALES
$stmt = $conn->query("SELECT count(*) FROM productos");
$total = $stmt->fetchColumn();

// 2. OBTENER DATOS PARA LA GRÁFICA (Agrupados por categoría)
// Asumimos: 1=Normal, 2=Geek, 3=Adultos
$sql_stats = "SELECT categoria_id, COUNT(*) as cantidad FROM productos GROUP BY categoria_id";
$stmt = $conn->query($sql_stats);
$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Devuelve array [id => cantidad]

// Preparamos los datos para JS (si no hay datos pone 0)
$cant_normal = $stats[1] ?? 0;
$cant_geek   = $stats[2] ?? 0;
$cant_adulto = $stats[3] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .dashboard { max-width: 900px; text-align: left; }
        .header-admin { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        .grid-panel { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .card-stat { background: #16213e; padding: 20px; border-radius: 10px; border-left: 5px solid #4ecca3; }
        .card-grafico { background: #16213e; padding: 20px; border-radius: 10px; text-align: center; }
        
        .btn-logout { background: #333; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .btn-tool { 
            display: block; width: 100%; text-align: center; padding: 15px; margin-bottom: 10px;
            text-decoration: none; color: white; border-radius: 5px; font-weight: bold; 
            transition: transform 0.2s;
        }
        .btn-tool:hover { transform: scale(1.02); }
    </style>
</head>
<body>

    <div class="app-container dashboard">
        <div class="header-admin">
            <h1>👮‍♂️ Panel de Mando</h1>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>

        <div class="grid-panel">
            
            <div>
                <div class="card-stat">
                    <h3>📊 Resumen del Almacén</h3>
                    <p style="font-size: 2rem; margin: 10px 0;"><?= $total ?> <span style="font-size:1rem">productos</span></p>
                    <small>Listo para servir cajas.</small>
                </div>

                <div class="card-stat" style="margin-top: 20px; border-left-color: #e94560;">
                    <h3>🛠️ Acciones Rápidas</h3>
                    <div style="margin-top: 15px;">
                        <a href="productos.php" class="btn-tool" style="background: #e94560;">📦 Gestionar Inventario</a>
                        <a href="ver_logs.php" class="btn-tool" style="background: #0f3460;">📠 Ver Logs del Sistema</a>
                        
                        <a href="../scrape_all.php" target="_blank" class="btn-tool" style="background: #f1c40f; color: #333;">
                            🔄 Actualizar Catálogo (Scraper)
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-grafico">
                <h3>Distribución por Categoría</h3>
                <canvas id="miGrafica"></canvas>
            </div>

        </div>
    </div>

    <script>
        const ctx = document.getElementById('miGrafica').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut', // Tipo "Donut"
            data: {
                labels: ['Normal 🎁', 'Geek 🕷️', 'Adultos 🔥'],
                datasets: [{
                    data: [<?= $cant_normal ?>, <?= $cant_geek ?>, <?= $cant_adulto ?>],
                    backgroundColor: [
                        '#ff2e63', // Rosa (Normal)
                        '#4ecca3', // Verde (Geek) - Ajustado a tu tema
                        '#ff9f43'  // Naranja (Adultos)
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { color: 'white' } }
                }
            }
        });
    </script>

</body>
</html>