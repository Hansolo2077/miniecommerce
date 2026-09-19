<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Usuario.php';

$mensaje = '';
$error = '';
$datosUsuario = null;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 1;

try {
    $conexion = obtenerConexion();
    $usuario = new Usuario($conexion);

    $datosUsuario = $usuario->obtenerPorId($id);
    if (!$datosUsuario) {
        throw new RuntimeException("Usuario no encontrado.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario->actualizarPerfil(
            $id,
            $_POST['nombre'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );

        $mensaje = 'Perfil actualizado correctamente.';
    }

    $datosUsuario = $usuario->obtenerPorId($id);

    if (!$datosUsuario) {
        throw new RuntimeException("Usuario no encontrado.");
    }
} catch (Throwable $e) {
    $error = ($e instanceof PDOException || $e instanceof TypeError) ? 'No fue posible procesar la solicitud. Revise los datos e intente nuevamente.' : $e->getMessage();
}

require_once __DIR__ . '/../views/perfil.php';
