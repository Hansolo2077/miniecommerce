<?php

require_once __DIR__ . '/DetalleOrden.php';

class Orden
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function calcularTotal(array $seleccion): float
    {
        $this->validarSeleccion($seleccion);
        $total = 0;

        foreach ($seleccion as $item) {
            $productoId = (int) $item['producto_id'];
            $cantidad = (int) $item['cantidad'];

            if ($cantidad <= 0) {
                continue;
            }

            // Obtiene el precio actual del producto
            $consulta = $this->conexion->prepare(
                "SELECT precio, stock
                 FROM productos
                 WHERE id = ?"
            );

            $consulta->execute([$productoId]);
            $producto = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                throw new RuntimeException("Producto no encontrado.");
            }

            if ($cantidad > (int) $producto['stock']) {
                throw new RuntimeException("Stock insuficiente.");
            }

            $detalle = new DetalleOrden(
                $productoId,
                $cantidad,
                (float) $producto['precio']
            );

            $total += $detalle->calcularSubtotal();
        }

        return $total;
    }

    public function crearOrden(int $usuarioId, array $seleccion): int
    {
        $this->validarSeleccion($seleccion);

        $this->conexion->beginTransaction();

        try {
            // Crea la orden principal
            $consulta = $this->conexion->prepare(
                "INSERT INTO ordenes (usuario_id)
                 OUTPUT INSERTED.id
                 VALUES (?)"
            );

            $consulta->execute([$usuarioId]);
            $ordenId = (int) $consulta->fetchColumn();

            foreach ($seleccion as $item) {
                $productoId = (int) $item['producto_id'];
                $cantidad = (int) $item['cantidad'];

                if ($cantidad <= 0) {
                    continue;
                }

                $consulta = $this->conexion->prepare(
                    "SELECT precio, stock
                     FROM productos
                     WHERE id = ?"
                );

                $consulta->execute([$productoId]);
                $producto = $consulta->fetch(PDO::FETCH_ASSOC);

                if (!$producto) {
                    throw new RuntimeException("Producto no encontrado.");
                }

                if ($cantidad > (int) $producto['stock']) {
                    throw new RuntimeException("Stock insuficiente.");
                }

                $detalle = new DetalleOrden(
                    $productoId,
                    $cantidad,
                    (float) $producto['precio']
                );

                // Guarda el detalle de la orden
                $consulta = $this->conexion->prepare(
                    "INSERT INTO detalle_orden
                     (orden_id, producto_id, cantidad, precio_unitario)
                     VALUES (?, ?, ?, ?)"
                );

                $consulta->execute([
                    $ordenId,
                    $detalle->obtenerProductoId(),
                    $detalle->obtenerCantidad(),
                    $detalle->obtenerPrecioUnitario()
                ]);
            }

            $this->conexion->commit();

            return $ordenId;
        } catch (Throwable $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    private function validarSeleccion(array $seleccion): void
    {
        if (!$seleccion) {
            throw new InvalidArgumentException('Debe seleccionar al menos un producto.');
        }
        $ids = [];
        foreach ($seleccion as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Selección no válida.');
            }
            $id = filter_var($item['producto_id'] ?? null, FILTER_VALIDATE_INT);
            $cantidad = filter_var($item['cantidad'] ?? null, FILTER_VALIDATE_INT);
            if ($id === false || $id < 1 || $cantidad === false || $cantidad < 1 || isset($ids[$id])) {
                throw new InvalidArgumentException('Seleccione productos sin repetir y cantidades enteras positivas.');
            }
            $ids[$id] = true;
        }
    }
}
