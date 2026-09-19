<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Producto.php';

try {
    $conexion = obtenerConexion();
    $producto = new Producto($conexion);

    $pagina = max(1, min(1000000, (int) ($_GET['pagina'] ?? 1)));
    $productos = $producto->listar($pagina, 5);
} catch (Throwable $e) {
    $productos = [];
    $error = ($e instanceof PDOException || $e instanceof TypeError) ? 'No fue posible procesar la solicitud. Revise los datos e intente nuevamente.' : $e->getMessage();
}

require_once __DIR__ . '/../views/catalogo.php';
