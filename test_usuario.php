<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Usuario.php';

try {
    $conexion = obtenerConexion();
    $usuario = new Usuario($conexion);

    $resultado = $usuario->registrar(
        "Usuario Prueba",
        "prueba@ucn.edu.ni",
        "Prueba123"
    );

    if ($resultado) {
        echo "Usuario registrado correctamente.";
    }
} catch (Throwable $e) {
    echo $e->getMessage();
}