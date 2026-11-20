<?php
// Punto de entrada de la aplicación: inicia sesión, carga dependencias y llama al controlador.
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// La URL base se calcula automáticamente a partir de la carpeta del proyecto en htdocs.
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/modelos/Producto.php';
require_once __DIR__ . '/controladores/ProductoController.php';

$modelo = new Producto($conexion);
$controlador = new ProductoController($modelo);
$accion = $_GET['accion'] ?? 'listar';
$controlador->manejar((string) $accion);
