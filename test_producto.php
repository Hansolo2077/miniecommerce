<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Producto.php';

try {
    $conexion = obtenerConexion();
    $producto = new Producto($conexion);

    $productos = $producto->listar(1, 5);

    foreach ($productos as $item) {
        echo $item['id'] . " - ";
        echo $item['nombre'] . " - C$";
        echo $item['precio'] . " - Stock: ";
        echo $item['stock'] . PHP_EOL;
    }
} catch (Throwable $e) {
    echo $e->getMessage();
}