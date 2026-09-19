IF DB_ID(N'MiniEcommerce') IS NULL
    EXEC(N'CREATE DATABASE MiniEcommerce');
GO

USE MiniEcommerce;
GO

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(100) NOT NULL,
    email NVARCHAR(255) NOT NULL UNIQUE,
    password_hash NVARCHAR(255) NOT NULL,
    creado_en DATETIME2 NOT NULL DEFAULT SYSDATETIME()
);
GO

-- Tabla de productos
CREATE TABLE productos (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(150) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    CONSTRAINT CK_productos_precio CHECK (precio >= 0),
    CONSTRAINT CK_productos_stock CHECK (stock >= 0)
);
GO

-- Tabla de ordenes
CREATE TABLE ordenes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATETIME2 NOT NULL DEFAULT SYSDATETIME(),
    CONSTRAINT FK_ordenes_usuarios
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
GO

-- Tabla de detalle de orden
CREATE TABLE detalle_orden (
    orden_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    CONSTRAINT PK_detalle_orden
        PRIMARY KEY (orden_id, producto_id),
    CONSTRAINT FK_detalle_orden_ordenes
        FOREIGN KEY (orden_id) REFERENCES ordenes(id),
    CONSTRAINT FK_detalle_orden_productos
        FOREIGN KEY (producto_id) REFERENCES productos(id),
    CONSTRAINT CK_detalle_orden_cantidad CHECK (cantidad > 0),
    CONSTRAINT CK_detalle_orden_precio CHECK (precio_unitario >= 0)
);
GO

-- Usuarios de prueba
INSERT INTO usuarios (nombre, email, password_hash)
VALUES
(
    'Ana Lopez',
    'ana@ucn.edu.ni',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'
),
(
    'Carlos Perez',
    'carlos@ucn.edu.ni',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'
);
GO

-- Productos de prueba
INSERT INTO productos (nombre, precio, stock)
VALUES
('Cuaderno universitario', 75.00, 30),
('Memoria USB 32 GB', 320.00, 15),
('Camiseta UCN', 250.00, 20),
('Lapicero azul', 20.00, 50),
('Mochila estudiantil', 650.00, 10);
GO

-- Verifica los datos creados
SELECT * FROM usuarios;
SELECT * FROM productos;
GO
