# Evidencias de pruebas

## Revisión del 19/09/2026

Se ejecutó `php tests/verificar.php` contra SQL Server real. Resultado: **16 comprobaciones
correctas**, incluyendo tres flujos exitosos y el rechazo esperado de email duplicado.
Salida completa: [verificacion_modelos.txt](verificacion_modelos.txt).
Los datos de esta ejecución se revirtieron al terminar; los IDs de SQL Server pueden avanzar.

Las cuatro capturas PNG se inspeccionaron durante la revisión. Las de P1, P3 y P4
corresponden a la interfaz anterior a los ajustes de navegación. La captura de P2 fue
renovada por el usuario y verificada visualmente: muestra el total esperado de C$470.00.

## P1 — Registro exitoso (taller obligatorio A)

- Caso de uso: UC1 → `public/index.php` → `Usuario::registrar()` → `usuarios`.
- Datos de la prueba automatizada: nombre `Prueba Aula`, email único `revision.<aleatorio>@example.test`, contraseña `Prueba123`.
- Esperado: insertar el usuario con hash y confirmar el registro.
- Obtenido: INSERT correcto; `password_verify()` confirmó el hash almacenado y que no era texto plano.
- Captura original: [01_registro_exitoso.png](01_registro_exitoso.png), con el mensaje «Usuario registrado correctamente.».
- La captura prueba el mensaje; el archivo de verificación prueba el hash en SQL Server.

## P2 — Total de compra exitoso

- Caso de uso: UC3 → `public/carrito.php` → `Orden::calcularTotal()` → `DetalleOrden::calcularSubtotal()`.
- Datos: 2 cuadernos de C$75 y 1 USB de C$320.
- Esperado: 2 × 75 + 1 × 320 = **C$470.00**.
- Obtenido: C$470.00 en prueba del modelo y en POST HTTP a `http://localhost:8000/carrito.php`.
- Respuesta HTTP real guardada: [02_carrito_total_http.html](02_carrito_total_http.html).
- Captura renovada y verificada: [02_carrito_total_exitoso.png](02_carrito_total_exitoso.png), con el mensaje «Total de la compra: C$470.00».
- Los campos de cantidad vuelven a cero después del envío porque la vista reinicia el formulario. La captura evidencia el resultado; los datos de entrada y el cálculo se documentan arriba y se verifican en la prueba automatizada.

## P3 — Actualización de perfil exitosa

- Caso de uso: UC4 → `public/perfil.php` → `Usuario::actualizarPerfil()` → `usuarios`.
- Datos automatizados: usuario temporal de P1, nombre `Perfil actualizado`, nuevo email único y contraseña vacía.
- Esperado: guardar nombre/email y conservar el hash.
- Obtenido: nombre/email persistidos y mismo hash. También se comprobó por separado el cambio opcional de contraseña con `password_verify()`.
- Captura original: [03_actualizacion_perfil_exitosa.png](03_actualizacion_perfil_exitosa.png), perfil 1 con nombre `Ana Lopez Mendez`, email `ana@ucn.edu.ni` y confirmación de actualización.

## P4 — Operación fallida esperada: email duplicado

- Casos de uso: UC1 y UC4 → `Usuario::registrar()` / `actualizarPerfil()`.
- Datos automatizados: intentar registrar de nuevo el email de P1 y actualizar su perfil con el email de otro usuario temporal.
- Esperado: rechazar ambas operaciones con «El email ya esta registrado.» sin sobrescribir el perfil.
- Obtenido: ambos rechazos correctos y perfil intacto. Es una prueba exitosa del manejo de una operación fallida.
- Captura original: [04_email_duplicado_error.png](04_email_duplicado_error.png), rechazo en `/perfil.php?id=1`. La imagen no permite identificar el email que se envió originalmente.

## Comprobaciones adicionales ejecutadas

- Catálogo: paginación real en SQL Server, sin repetir el primer producto de páginas consecutivas.
- Validación del servidor: email inválido, usuario inexistente, cantidades fraccionarias, selección vacía y stock insuficiente rechazados.
- HTTP: carrito C$470.00; cantidad `1.5` rechazada; perfil `id=-1` rechazado sin falsa confirmación; catálogo `pagina=0` normalizado.
- Todos los archivos PHP de `config/`, `src/`, `public/`, `views/` y `tests/` pasaron `php -l`.
