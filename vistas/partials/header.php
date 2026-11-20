<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Gestión de productos', ENT_QUOTES, 'UTF-8') ?> | AVS.03.04.01</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
</head>
<body>
<header class="encabezado">
    <div class="contenedor encabezado-interior">
        <a class="marca" href="<?= BASE_URL ?>/index.php">
            <span class="marca-icono">P</span>
            <span>Gestión de productos<small>AVS.03.04.01</small></span>
        </a>
        <nav aria-label="Navegación principal">
            <a href="<?= BASE_URL ?>/index.php">Catálogo</a>
            <a class="boton boton-claro boton-pequeno" href="<?= BASE_URL ?>/index.php?accion=nuevo">+ Nuevo producto</a>
        </nav>
    </div>
</header>
<main class="contenedor contenido">
    <?php if (!empty($mensaje)): ?>
        <div class="alerta alerta-<?= htmlspecialchars($mensaje['tipo'], ENT_QUOTES, 'UTF-8') ?>" role="status">
            <?= htmlspecialchars($mensaje['texto'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
