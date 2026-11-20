<section class="encabezado-seccion">
    <div>
        <p class="etiqueta">ADMINISTRACIÓN</p>
        <h1>Catálogo de productos</h1>
        <p class="subtitulo">Consulta, registra y administra los productos almacenados.</p>
    </div>
    <a class="boton boton-principal" href="<?= BASE_URL ?>/index.php?accion=nuevo">+ Registrar producto</a>
</section>

<section class="panel">
    <div class="panel-cabecera">
        <div>
            <h2>Productos registrados</h2>
            <p><?= count($productos) ?> producto(s) en el catálogo</p>
        </div>
    </div>

    <?php if (empty($productos)): ?>
        <div class="estado-vacio">
            <div class="estado-icono">▧</div>
            <h3>Aún no hay productos</h3>
            <p>Registra el primer producto para que aparezca en este catálogo.</p>
            <a class="boton boton-principal" href="<?= BASE_URL ?>/index.php?accion=nuevo">Registrar primer producto</a>
        </div>
    <?php else: ?>
        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Fotografía</th>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>
                                <img class="foto-producto" src="<?= BASE_URL . '/' . htmlspecialchars($producto['foto_p'], ENT_QUOTES, 'UTF-8') ?>" alt="Fotografía de <?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                            </td>
                            <td>#<?= (int) $producto['id_producto'] ?></td>
                            <td class="nombre-producto"><?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="etiqueta-categoria"><?= htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>$<?= number_format((float) $producto['precio'], 2) ?></td>
                            <td><?= (int) $producto['stock'] ?></td>
                            <td>
                                <div class="acciones">
                                    <a class="boton boton-secundario boton-pequeno" href="<?= BASE_URL ?>/index.php?accion=editar&id=<?= (int) $producto['id_producto'] ?>">Editar</a>
                                    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=eliminar" onsubmit="return confirm('¿Deseas borrar este producto? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="id_producto" value="<?= (int) $producto['id_producto'] ?>">
                                        <button class="boton boton-peligro boton-pequeno" type="submit">Borrar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
