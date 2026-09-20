# Diseño vs. Código

Taller elegido: **A. Registrar usuario**. Los cuatro casos de uso del escenario se implementan.
Los diagramas describen los casos de uso, las clases y las relaciones de la base de datos del módulo.

| Caso / artefacto | UI y controlador (URL relativa a localhost:8000) | Archivo, clase y método | ER / SQL | Prueba |
|---|---|---|---|---|
| UC1 Registrar usuario | `views/registro.php` → `public/index.php` (`/index.php`) | `src/Usuario.php`: `Usuario::registrar(nombre,email,password)` | `usuarios`: INSERT, UNIQUE email, hash | P1: alta correcta; P4: duplicado |
| UC2 Consultar catálogo | `views/catalogo.php` → `public/catalogo.php` (`/catalogo.php?pagina=1`) | `src/Producto.php`: `Producto::listar(pagina,porPagina)` | `productos`: SELECT con OFFSET/FETCH | Verificación automática: listado/paginación |
| UC3 Calcular total | `views/carrito.php` → `public/carrito.php` (`/carrito.php`) | `src/Orden.php`: `Orden::calcularTotal(seleccion)` → `src/DetalleOrden.php`: `DetalleOrden::calcularSubtotal()` | `productos`: precio y stock reales; suma cantidad × precio | P2: C$470.00; automático: stock y cantidades inválidas |
| UC4 Actualizar perfil | `views/perfil.php` → `public/perfil.php` (`/perfil.php?id=1`) | `src/Usuario.php`: `obtenerPorId(id)`, `actualizarPerfil(id,nombre,email,password)` | `usuarios`: UPDATE, email único, hash opcional | P3: actualización; P4: email duplicado |
| Persistencia del modelo de orden (sin UI; taller A) | No aplica | `src/Orden.php`: `crearOrden(usuarioId,seleccion)`; getters de `DetalleOrden` | `ordenes` y `detalle_orden`: transacción, PK/FK y CHECK | `test_orden.php` (manual; crea una orden) |
| Conexión | Utilizada por los cuatro controladores | `config/database.php`: `obtenerConexion()` | PDO_SQLSRV, SQL Server / MiniEcommerce | `test_connection.php` |

- Casos de uso: [diagrama_casos_uso.puml](diagrama_casos_uso.puml).
- Clases y firmas reales: [diagrama_clases.puml](diagrama_clases.puml).
- Tablas, relaciones y restricciones: [modelo_er.puml](modelo_er.puml) → [DDL y semillas](../scripts/ddl_&_seed.sql).
- Evidencias P1–P4: [notas](evidencias_pruebas/pruebas.md).
- Pruebas automatizadas: [tests/verificar.php](../tests/verificar.php).
