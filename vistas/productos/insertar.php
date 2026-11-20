<section class="encabezado-seccion encabezado-formulario">
    <div>
        <p class="etiqueta">NUEVO REGISTRO</p>
        <h1>Registrar producto</h1>
        <p class="subtitulo">Completa la información y agrega una fotografía del producto.</p>
    </div>
    <a class="enlace-regreso" href="<?= BASE_URL ?>/index.php">← Volver al catálogo</a>
</section>

<section class="panel panel-formulario">
    <form method="POST" action="<?= BASE_URL ?>/index.php?accion=crear" enctype="multipart/form-data" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="rejilla-formulario">
            <div class="campo campo-completo">
                <label for="nombre">Nombre del producto *</label>
                <input type="text" id="nombre" name="nombre" maxlength="30" placeholder="Ej. Teclado inalámbrico" required>
                <small>Máximo 30 caracteres.</small>
            </div>
            <div class="campo">
                <label for="precio">Precio (MXN) *</label>
                <input type="number" id="precio" name="precio" min="0" step="0.01" placeholder="0.00" required>
            </div>
            <div class="campo">
                <label for="stock">Stock disponible *</label>
                <input type="number" id="stock" name="stock" min="0" step="1" placeholder="0" required>
            </div>
            <div class="campo campo-completo">
                <label for="categoria_id">Categoría *</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['descripcion'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo campo-completo">
                <label for="foto_p">Fotografía del producto *</label>
                <input type="file" id="foto_p" name="foto_p" accept="image/jpeg,image/png,image/webp" required>
                <small>Formatos permitidos: JPG, PNG o WEBP. Tamaño máximo: 3 MB.</small>
            </div>
        </div>
        <div class="pie-formulario">
            <a class="boton boton-secundario" href="<?= BASE_URL ?>/index.php">Cancelar</a>
            <button class="boton boton-principal" type="submit">Guardar producto</button>
        </div>
    </form>
</section>
