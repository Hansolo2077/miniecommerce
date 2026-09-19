<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Producto.php';
require_once __DIR__ . '/../src/Orden.php';

$total = null;
$error = '';

try {
    $conexion = obtenerConexion();

    $producto = new Producto($conexion);
    $orden = new Orden($conexion);

    $productos = $producto->listar(1, 50);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $seleccion = [];

        $cantidades = $_POST['cantidad'] ?? [];
        if (!is_array($cantidades)) {
            throw new InvalidArgumentException('La selección de productos no es válida.');
        }
        foreach ($cantidades as $productoId => $cantidad) {
            $cantidad = filter_var($cantidad, FILTER_VALIDATE_INT);
            $productoId = filter_var($productoId, FILTER_VALIDATE_INT);
            if ($cantidad === false || $cantidad < 0 || $productoId === false || $productoId < 1) {
                throw new InvalidArgumentException('Las cantidades deben ser enteros no negativos y los productos válidos.');
            }

            if ($cantidad > 0) {
                $seleccion[] = [
                    'producto_id' => (int) $productoId,
                    'cantidad' => $cantidad
                ];
            }
        }

        if (empty($seleccion)) {
            throw new InvalidArgumentException(
                "Debe seleccionar al menos un producto."
            );
        }

        // Calcula el total de la selección
        $total = $orden->calcularTotal($seleccion);
    }
} catch (Throwable $e) {
    $productos = $productos ?? [];
    $error = ($e instanceof PDOException || $e instanceof TypeError) ? 'No fue posible procesar la solicitud. Revise los datos e intente nuevamente.' : $e->getMessage();
}

require_once __DIR__ . '/../views/carrito.php';
