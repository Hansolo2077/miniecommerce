<?php

require_once __DIR__ . '/config/database.php';

try {
    $conexion = obtenerConexion();

    $consulta = $conexion->query("SELECT DB_NAME() AS base_actual");
    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

    echo "Conexion correcta: " . $resultado['base_actual'];
} catch (Throwable $e) {
    echo $e->getMessage();
}