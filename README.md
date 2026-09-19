# Mini eCommerce Aula

Módulo académico PHP + SQL Server. Alcance: registro seguro, catálogo paginado,
cálculo de carrito y actualización de nombre/email con contraseña opcional.
El taller obligatorio elegido es **A. Registrar usuario**: formulario → INSERT → confirmación.

## Ejecución local

1. Tener PHP con `pdo_sqlsrv`, SQL Server y Microsoft ODBC Driver para SQL Server instalados.
   Verificar con `php -m` que aparece `pdo_sqlsrv`.
2. Abrir `scripts/ddl_&_seed.sql` en SQL Server Management Studio y ejecutarlo **una vez**
   en una instalación nueva. Crea `MiniEcommerce`, cuatro tablas, cinco productos y dos usuarios.
   El script no elimina ni reinicializa una base existente; si ya tiene las tablas, no repetirlo.
3. Revisar `config/database.php`: instancia `localhost`, base `MiniEcommerce`, autenticación
   integrada de Windows. La cuenta que ejecuta PHP debe tener acceso a la base.
   `TrustServerCertificate=1` se usa para esta demostración local.
4. En una terminal situada en la raíz del proyecto:

   ```powershell
   php test_connection.php
   php -S localhost:8000 -t public
   ```

5. Abrir http://localhost:8000/index.php y usar los enlaces de navegación:

   | Función | Ruta |
   |---|---|
   | Registro (taller A) | `/index.php` |
   | Catálogo, cinco productos por página | `/catalogo.php?pagina=1` |
   | Carrito: 2 cuadernos + 1 USB = C$470.00 | `/carrito.php` |
   | Perfil de demostración | `/perfil.php?id=1` |

El perfil usa un ID explícito (por defecto 1) para la simulación de aula; no incluye login.
La contraseña requiere entre 8 y 72 bytes. En perfil, dejarla vacía conserva su hash.
El carrito calcula con precios consultados en la base. El taller B no fue elegido:
no se requiere guardar la compra desde la UI. `Orden::crearOrden()` conserva la operación
por capas ya implementada para `ordenes` y `detalle_orden`.

## Entregables y pruebas

- `docs/tabla_diseno_codigo.md`: trazabilidad de casos de uso, UI, diagramas, código y pruebas.
- `docs/diagrama_casos_uso.puml`, `docs/diagrama_clases.puml`, `docs/modelo_er.puml`: fuentes PlantUML.
- `scripts/ddl_&_seed.sql`: DDL y semillas para SQL Server (único motor utilizado).
- `config/`, `src/`, `views/`, `public/`: conexión, modelos, plantillas y controladores.
- `docs/evidencias_pruebas/`: capturas, notas y verificación automática.
- Ejecutar `php tests/verificar.php` para verificar los modelos con SQL Server real.
  Sus datos temporales se revierten mediante una transacción.

Para entregar, incluir las carpetas anteriores, `tests/`, este README, `index.php` y
`test_connection.php`. La carpeta `php/` contiene un instalador local y no forma parte del código entregable.