<section class="encabezado-seccion encabezado-formulario">
    <div>
        <p class="etiqueta">EDICIÓN DE REGISTRO</p>
        <h1>Actualizar producto</h1>
        <p class="subtitulo">Modifica los datos del producto seleccionado.</p>
    </div>
    <a class="enlace-regreso" href="<?= BASE_URL ?>/index.php">← Volver al catálogo</a>
</section>

<section class="panel panel-formulario">
    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=actualizar" enctype="multipart/form-data" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id_producto" value="<?= (int) $producto['id_producto'] ?>">
        <div class="producto-editando">
            <img class="foto-producto foto-grande" src="<?= BASE_URL . '/' . htmlspecialchars($producto['foto_p'], ENT_QUOTES, 'UTF-8') ?>" alt="Fotografía actual de <?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>">
            <div><strong>Producto #<?= (int) $producto['id_producto'] ?></strong><span>Fotografía actual</span></div>
        </div>
        <div class="rejilla-formulario">
            <div class="campo campo-completo">
                <label for="nombre">Nombre del producto *</label>
                <input type="text" id="nombre" name="nombre" maxlength="30" value="<?= htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="campo">
                <label for="precio">Precio (MXN) *</label>
                <input type="number" id="precio" name="precio" min="0" step="0.01" value="<?= htmlspecialchars((string) $producto['precio'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="campo">
                <label for="stock">Stock disponible *</label>
                <input type="number" id="stock" name="stock" min="0" step="1" value="<?= (int) $producto['stock'] ?>" required>
            </div>
            <div class="campo campo-completo">
                <label for="categoria_id">Categoría *</label>
                <select id="categoria_id" name="categoria_id" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>" <?= (int) $categoria['id_categoria'] === (int) $producto['categoria_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['descripcion'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo campo-completo">
                <label for="foto_p">Cambiar fotografía (opcional)</label>
                <input type="file" id="foto_p" name="foto_p" accept="image/jpeg,image/png,image/webp">
                <small>Si no seleccionas otra imagen, se conservará la fotografía actual. Máximo 3 MB.</small>
            </div>
        </div>
        <div class="pie-formulario">
            <a class="boton boton-secundario" href="<?= BASE_URL ?>/index.php">Cancelar</a>
            <button class="boton boton-principal" type="submit">Guardar cambios</button>
        </div>
    </form>
</section>
