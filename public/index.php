<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Usuario.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion = obtenerConexion();
        $usuario = new Usuario($conexion);

        $usuario->registrar(
            $_POST['nombre'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );

        $mensaje = 'Usuario registrado correctamente.';
    } catch (Throwable $e) {
        $error = ($e instanceof PDOException || $e instanceof TypeError) ? 'No fue posible procesar la solicitud. Revise los datos e intente nuevamente.' : $e->getMessage();
    }
}

require_once __DIR__ . '/../views/registro.php';