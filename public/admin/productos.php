<?php
require '../../vendor/autoload.php';
use App\Database;

session_start();
if (!isset($_SESSION['usuario_logueado'])) { header("Location: login.php"); exit; }

$db = Database::getInstance();
$conn = $db->getConnection();

// --- 1. LÓGICA DE BORRADO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrar_id'])) {
    $id_borrar = $_POST['borrar_id'];
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id_borrar]);
    
    // Mantenemos el filtro y la página al recargar
    $filtro_actual = $_GET['cat'] ?? '';
    $pag_actual = $_GET['pag'] ?? 1;
    header("Location: productos.php?pag=$pag_actual&cat=$filtro_actual");
    exit;
}

// --- 2. PREPARAR FILTROS ---
// Recogemos el filtro de la URL (si existe)
$filtro_cat = isset($_GET['cat']) && $_GET['cat'] !== '' ? (int)$_GET['cat'] : null;

// --- 3. PAGINACIÓN CON FILTRO ---
$por_pagina = 10;
$pagina = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $por_pagina;

// Contar productos (¿Todos o solo los filtrados?)
if ($filtro_cat) {
    $stmtCount = $conn->prepare("SELECT count(*) FROM productos WHERE categoria_id = ?");
    $stmtCount->execute([$filtro_cat]);
} else {
    $stmtCount = $conn->query("SELECT count(*) FROM productos");
}
$total_prod = $stmtCount->fetchColumn();
$total_paginas = ceil($total_prod / $por_pagina);

// Obtener productos (¿Todos o solo los filtrados?)
$sql = "SELECT * FROM productos";
if ($filtro_cat) {
    $sql .= " WHERE categoria_id = :cat";
}
$sql .= " ORDER BY id DESC LIMIT :inicio, :cantidad";

$stmt = $conn->prepare($sql);
if ($filtro_cat) {
    $stmt->bindValue(':cat', $filtro_cat, PDO::PARAM_INT);
}
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':cantidad', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        /* Estilos Tabla y Botones */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(0,0,0,0.3); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { background: #16213e; color: #4ecca3; }
        tr:hover { background: rgba(255,255,255,0.05); }
        .img-mini { width: 40px; height: 40px; object-fit: contain; background: white; border-radius: 4px; }
        
        .paginacion { margin-top: 20px; text-align: center; }
        .paginacion a { display: inline-block; padding: 8px 12px; margin: 0 5px; background: #16213e; color: white; text-decoration: none; border-radius: 5px; }
        .paginacion a.activo { background: #e94560; font-weight: bold; }
        
        .btn-borrar { background: #ff2e63; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        
        /* ESTILO DEL FILTRO */
        .barra-filtro { background: #16213e; padding: 15px; border-radius: 8px; display: flex; gap: 10px; align-items: center; margin-bottom: 20px; }
        select { padding: 8px; border-radius: 4px; border: none; }
        .btn-filtrar { background: #4ecca3; color: #16213e; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="app-container" style="max-width: 1000px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h1>📦 Inventario (<?= $total_prod ?>)</h1>
            <a href="index.php" class="btn-cat" style="text-decoration:none;">← Volver</a>
        </div>

        <form method="GET" class="barra-filtro">
            <label>Filtrar por:</label>
            <select name="cat">
                <option value="">-- Ver Todos --</option>
                <option value="1" <?= $filtro_cat == 1 ? 'selected' : '' ?>>🎁 Normal</option>
                <option value="2" <?= $filtro_cat == 2 ? 'selected' : '' ?>>🕷️ Geek</option>
                <option value="3" <?= $filtro_cat == 3 ? 'selected' : '' ?>>🔥 Adultos</option>
            </select>
            <button type="submit" class="btn-filtrar">Aplicar Filtro</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cat</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($productos) > 0): ?>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>#<?= $p['id'] ?></td>
                        <td><img src="<?= $p['imagen_url'] ?>" class="img-mini"></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= $p['precio'] ?>€</td>
                        <td>
                            <?php 
                                if($p['categoria_id'] == 1) echo "🎁 Normal";
                                elseif($p['categoria_id'] == 2) echo "🕷️ Geek";
                                else echo "🔥 Adultos";
                            ?>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('¿Borrar este producto?');">
                                <input type="hidden" name="borrar_id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn-borrar">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 20px;">No hay productos con este filtro.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="paginacion">
            <?php for($i=1; $i<=$total_paginas; $i++): ?>
                <a href="?pag=<?= $i ?>&cat=<?= $filtro_cat ?>" class="<?= $i == $pagina ? 'activo' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>