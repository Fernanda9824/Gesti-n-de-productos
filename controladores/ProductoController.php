<?php
/**
 * Controlador Producto.
 * Recibe las acciones del usuario, valida los datos y coordina el modelo y las vistas.
 */
class ProductoController
{
    private Producto $modelo;
    private string $directorioFotos;

    public function __construct(Producto $modelo)
    {
        $this->modelo = $modelo;
        $this->directorioFotos = dirname(__DIR__) . '/uploads/productos/';
    }

    /** Decide qué operación ejecutar según la acción solicitada. */
    public function manejar(string $accion): void
    {
        try {
            switch ($accion) {
                case 'nuevo':
                    $this->mostrarFormularioNuevo();
                    break;
                case 'crear':
                    $this->crear();
                    break;
                case 'editar':
                    $this->mostrarFormularioEditar();
                    break;
                case 'actualizar':
                    $this->actualizar();
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                case 'listar':
                default:
                    $this->listar();
                    break;
            }
        } catch (InvalidArgumentException $e) {
            // Los errores de validación se muestran de forma clara para que el usuario pueda corregirlos.
            $this->redirigir('listar', $e->getMessage(), 'error');
        } catch (Throwable $e) {
            // El detalle técnico se registra en el log del servidor, no se expone al visitante.
            error_log($e->getMessage());
            $this->redirigir('listar', 'Ocurrió un error al procesar la solicitud. Revisa los datos e inténtalo nuevamente.', 'error');
        }
    }

    private function listar(): void
    {
        $productos = $this->modelo->obtenerTodos();
        $this->renderizar('productos/lista.php', [
            'titulo' => 'Catálogo de productos',
            'productos' => $productos
        ]);
    }

    private function mostrarFormularioNuevo(): void
    {
        $categorias = $this->modelo->obtenerCategorias();
        $this->renderizar('productos/insertar.php', [
            'titulo' => 'Registrar producto',
            'categorias' => $categorias
        ]);
    }

    private function crear(): void
    {
        $this->validarSolicitudPost();
        $datos = $this->validarDatosFormulario();

        // Para un producto nuevo la fotografía es obligatoria.
        $foto = $this->guardarFoto(true);
        $datos['foto_p'] = $foto;

        try {
            $this->modelo->insertar($datos);
        } catch (Throwable $e) {
            $this->eliminarArchivoFoto($foto);
            throw $e;
        }

        $this->redirigir('listar', 'El producto se insertó correctamente.', 'exito');
    }

    private function mostrarFormularioEditar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->redirigir('listar', 'Selecciona un producto válido para actualizar.', 'error');
        }

        $producto = $this->modelo->obtenerPorId((int) $id);
        if (!$producto) {
            $this->redirigir('listar', 'El producto solicitado no existe.', 'error');
        }

        $this->renderizar('productos/editar.php', [
            'titulo' => 'Actualizar producto',
            'producto' => $producto,
            'categorias' => $this->modelo->obtenerCategorias()
        ]);
    }

    private function actualizar(): void
    {
        $this->validarSolicitudPost();

        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->redirigir('listar', 'El identificador del producto no es válido.', 'error');
        }

        $productoActual = $this->modelo->obtenerPorId((int) $id);
        if (!$productoActual) {
            $this->redirigir('listar', 'No se encontró el producto que intentas actualizar.', 'error');
        }

        $datos = $this->validarDatosFormulario();
        $fotoNueva = null;
        $datos['foto_p'] = $productoActual['foto_p'];

        // La fotografía es opcional al actualizar: si se elige otra, se reemplaza.
        if (isset($_FILES['foto_p']) && $_FILES['foto_p']['error'] !== UPLOAD_ERR_NO_FILE) {
            $fotoNueva = $this->guardarFoto(false);
            $datos['foto_p'] = $fotoNueva;
        }

        try {
            $this->modelo->actualizar((int) $id, $datos);
        } catch (Throwable $e) {
            if ($fotoNueva !== null) {
                $this->eliminarArchivoFoto($fotoNueva);
            }
            throw $e;
        }

        if ($fotoNueva !== null) {
            $this->eliminarArchivoFoto($productoActual['foto_p']);
        }

        $this->redirigir('listar', 'El producto se actualizó correctamente.', 'exito');
    }

    private function eliminar(): void
    {
        $this->validarSolicitudPost();

        $id = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $this->redirigir('listar', 'Selecciona un producto válido para borrar.', 'error');
        }

        $producto = $this->modelo->obtenerPorId((int) $id);
        if (!$producto) {
            $this->redirigir('listar', 'El producto que intentas borrar no existe.', 'error');
        }

        $this->modelo->eliminar((int) $id);
        $this->eliminarArchivoFoto($producto['foto_p']);

        $this->redirigir('listar', 'El producto se borró correctamente.', 'exito');
    }

    /** Verifica que los formularios se envíen por POST y tengan token CSRF válido. */
    private function validarSolicitudPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('listar', 'La operación solicitada no es válida.', 'error');
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->redirigir('listar', 'La sesión del formulario expiró. Vuelve a intentarlo.', 'error');
        }
    }

    /** Valida y normaliza los campos escritos en el formulario. */
    private function validarDatosFormulario(): array
    {
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $precioTexto = trim((string) ($_POST['precio'] ?? ''));
        $stockTexto = trim((string) ($_POST['stock'] ?? ''));
        $categoriaTexto = trim((string) ($_POST['categoria_id'] ?? ''));

        if ($nombre === '' || mb_strlen($nombre) > 30) {
            throw new InvalidArgumentException('El nombre es obligatorio y debe tener como máximo 30 caracteres.');
        }
        if ($precioTexto === '' || !is_numeric($precioTexto) || (float) $precioTexto < 0) {
            throw new InvalidArgumentException('El precio debe ser un número igual o mayor que cero.');
        }
        if ($stockTexto === '' || filter_var($stockTexto, FILTER_VALIDATE_INT) === false || (int) $stockTexto < 0) {
            throw new InvalidArgumentException('El stock debe ser un número entero igual o mayor que cero.');
        }
        if ($categoriaTexto === '' || filter_var($categoriaTexto, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('Selecciona una categoría válida.');
        }

        $categoriaId = (int) $categoriaTexto;
        $categoriasValidas = array_column($this->modelo->obtenerCategorias(), 'id_categoria');
        if (!in_array($categoriaId, array_map('intval', $categoriasValidas), true)) {
            throw new InvalidArgumentException('La categoría seleccionada no está registrada.');
        }

        return [
            'nombre' => $nombre,
            'precio' => (float) $precioTexto,
            'stock' => (int) $stockTexto,
            'categoria_id' => $categoriaId
        ];
    }

    /** Valida y guarda una imagen. Devuelve su ruta relativa para almacenarla en la BD. */
    private function guardarFoto(bool $obligatoria): string
    {
        if (!isset($_FILES['foto_p']) || $_FILES['foto_p']['error'] === UPLOAD_ERR_NO_FILE) {
            if ($obligatoria) {
                throw new InvalidArgumentException('Debes seleccionar una fotografía para el producto.');
            }
            return '';
        }

        $archivo = $_FILES['foto_p'];
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('No se pudo cargar la fotografía. Comprueba el archivo e inténtalo nuevamente.');
        }
        if ($archivo['size'] > 3 * 1024 * 1024) {
            throw new InvalidArgumentException('La fotografía no debe superar los 3 MB.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($archivo['tmp_name']);
        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];
        if (!isset($tiposPermitidos[$mime])) {
            throw new InvalidArgumentException('Formato no permitido. Utiliza una imagen JPG, PNG o WEBP.');
        }

        if (!is_dir($this->directorioFotos) && !mkdir($this->directorioFotos, 0755, true) && !is_dir($this->directorioFotos)) {
            throw new RuntimeException('No se pudo preparar la carpeta para las fotografías.');
        }

        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$mime];
        $destino = $this->directorioFotos . $nombreArchivo;
        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            throw new RuntimeException('No se pudo guardar la fotografía en el servidor.');
        }

        return 'uploads/productos/' . $nombreArchivo;
    }

    /** Elimina una foto anterior o una carga que no pudo asociarse a un registro. */
    private function eliminarArchivoFoto(string $rutaRelativa): void
    {
        $nombre = basename($rutaRelativa);
        if ($nombre === '' || $nombre === '.' || $nombre === '..') {
            return;
        }

        $rutaCompleta = $this->directorioFotos . $nombre;
        if (is_file($rutaCompleta)) {
            unlink($rutaCompleta);
        }
    }

    /** Carga una vista dentro de la plantilla común. */
    private function renderizar(string $vista, array $datos = []): void
    {
        extract($datos, EXTR_SKIP);
        $mensaje = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $csrfToken = $_SESSION['csrf_token'];
        require dirname(__DIR__) . '/vistas/partials/header.php';
        require dirname(__DIR__) . '/vistas/' . $vista;
        require dirname(__DIR__) . '/vistas/partials/footer.php';
    }

    /** Guarda un mensaje y redirige para evitar reenviar formularios al actualizar el navegador. */
    private function redirigir(string $accion, string $texto, string $tipo): never
    {
        $_SESSION['flash'] = ['texto' => $texto, 'tipo' => $tipo];
        header('Location: ' . BASE_URL . '/index.php?accion=' . urlencode($accion));
        exit;
    }
}
