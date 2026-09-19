<?php

class Producto
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(int $pagina = 1, int $porPagina = 5): array
    {
        if ($pagina < 1) {
            $pagina = 1;
        }

        if ($porPagina < 1) {
            $porPagina = 5;
        }

        $offset = ($pagina - 1) * $porPagina;

        // Lista productos con paginación
        $consulta = $this->conexion->prepare(
            "SELECT id, nombre, precio, stock
             FROM productos
             ORDER BY id
             OFFSET ? ROWS
             FETCH NEXT ? ROWS ONLY"
        );

        $consulta->bindValue(1, $offset, PDO::PARAM_INT);
        $consulta->bindValue(2, $porPagina, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        // Busca un producto específico
        $consulta = $this->conexion->prepare(
            "SELECT id, nombre, precio, stock
             FROM productos
             WHERE id = ?"
        );

        $consulta->execute([$id]);

        $producto = $consulta->fetch(PDO::FETCH_ASSOC);

        return $producto ?: null;
    }
}