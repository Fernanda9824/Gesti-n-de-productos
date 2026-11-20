<?php
/**
 * Conexión a MySQL mediante MySQLi.
 * En una instalación XAMPP local, el usuario predeterminado suele ser root
 * y la contraseña queda vacía.
 */
$servidor = 'localhost';
$usuario = 'root';
$contrasena = '';
$baseDatos = 'AVS_03_04_01';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($servidor, $usuario, $contrasena, $baseDatos);
    $conexion->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('No fue posible conectar con MySQL. Verifica que Apache y MySQL estén activos y que hayas importado la base de datos AVS_03_04_01.');
}
