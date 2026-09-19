<?php

try {
    $conexion = new PDO(
        "sqlsrv:Server=localhost;Database=MiniEcommerce;TrustServerCertificate=1",
        null,
        null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo "Conexion exitosa con SQL Server.";
} catch (PDOException $e) {
    echo "Error de conexion: " . $e->getMessage();
}