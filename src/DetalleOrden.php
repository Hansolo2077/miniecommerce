<?php

class DetalleOrden
{
    private int $productoId;
    private int $cantidad;
    private float $precioUnitario;

    public function __construct(
        int $productoId,
        int $cantidad,
        float $precioUnitario
    ) {
        $this->productoId = $productoId;
        $this->cantidad = $cantidad;
        $this->precioUnitario = $precioUnitario;
    }

    // Calcula el subtotal del producto
    public function calcularSubtotal(): float
    {
        return $this->cantidad * $this->precioUnitario;
    }

    public function obtenerProductoId(): int
    {
        return $this->productoId;
    }

    public function obtenerCantidad(): int
    {
        return $this->cantidad;
    }

    public function obtenerPrecioUnitario(): float
    {
        return $this->precioUnitario;
    }
}