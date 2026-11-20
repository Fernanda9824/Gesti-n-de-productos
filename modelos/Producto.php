<?php
/**
 * Modelo Producto.
 * Contiene exclusivamente operaciones de acceso a datos (consultas SQL).
 */
class Producto
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Obtiene todos los productos junto con la descripción de su categoría. */
    public function obtenerTodos(): array
    {
        $sql = 'SELECT p.id_producto, p.nombre, p.precio, p.stock, p.foto_p,
                       p.categoria_id, c.descripcion AS categoria
                FROM productos AS p
                INNER JOIN categorias AS c ON p.categoria_id = c.id_categoria
                ORDER BY p.id_producto DESC';

        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /** Busca un producto por su identificador. */
    public function obtenerPorId(int $id): ?array
    {
        $sql = 'SELECT p.id_producto, p.nombre, p.precio, p.stock, p.foto_p,
                       p.categoria_id, c.descripcion AS categoria
                FROM productos AS p
                INNER JOIN categorias AS c ON p.categoria_id = c.id_categoria
                WHERE p.id_producto = ?';

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $producto ?: null;
    }

    /** Obtiene las categorías disponibles para los formularios. */
    public function obtenerCategorias(): array
    {
        $resultado = $this->conexion->query(
            'SELECT id_categoria, descripcion FROM categorias ORDER BY descripcion'
        );
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /** Inserta un producto nuevo. */
    public function insertar(array $datos): bool
    {
        $sql = 'INSERT INTO productos (nombre, precio, stock, foto_p, categoria_id)
                VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            'sdisi',
            $datos['nombre'],
            $datos['precio'],
            $datos['stock'],
            $datos['foto_p'],
            $datos['categoria_id']
        );
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    /** Actualiza los datos de un producto existente. */
    public function actualizar(int $id, array $datos): bool
    {
        $sql = 'UPDATE productos
                SET nombre = ?, precio = ?, stock = ?, foto_p = ?, categoria_id = ?
                WHERE id_producto = ?';
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param(
            'sdisii',
            $datos['nombre'],
            $datos['precio'],
            $datos['stock'],
            $datos['foto_p'],
            $datos['categoria_id'],
            $id
        );
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    /** Elimina un producto por su identificador. */
    public function eliminar(int $id): bool
    {
        $stmt = $this->conexion->prepare(
            'DELETE FROM productos WHERE id_producto = ?'
        );
        $stmt->bind_param('i', $id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }
}
