<?php

function obtenerConexion(): PDO
{
    try {
        $conexion = new PDO(
            "sqlsrv:Server=localhost;Database=MiniEcommerce;TrustServerCertificate=1",
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );

        return $conexion;
    } catch (PDOException $e) {
        throw new RuntimeException("No fue posible conectar con la base de datos.");
    }
}