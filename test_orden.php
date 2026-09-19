<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Orden.php';

try {
    $conexion = obtenerConexion();
    $orden = new Orden($conexion);

    $seleccion = [
        [
            'producto_id' => 1,
            'cantidad' => 2
        ],
        [
            'producto_id' => 2,
            'cantidad' => 1
        ]
    ];

    $total = $orden->calcularTotal($seleccion);

    echo "Total: C$" . number_format($total, 2) . PHP_EOL;

    $ordenId = $orden->crearOrden(1, $seleccion);

    echo "Orden creada con ID: " . $ordenId;
} catch (Throwable $e) {
    echo $e->getMessage();
}