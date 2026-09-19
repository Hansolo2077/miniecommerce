<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Usuario.php';
require_once __DIR__ . '/../src/Producto.php';
require_once __DIR__ . '/../src/Orden.php';

function comprobar(bool $condicion, string $nota): void
{
    if (!$condicion) {
        throw new RuntimeException('FALLO: ' . $nota);
    }
    echo 'OK: ' . $nota . PHP_EOL;
}

function rechazar(callable $accion, string $mensaje): void
{
    try {
        $accion();
    } catch (InvalidArgumentException | RuntimeException $e) {
        comprobar(str_contains($e->getMessage(), $mensaje), 'Rechazo esperado: ' . $mensaje);
        return;
    }
    throw new RuntimeException('No se rechazó: ' . $mensaje);
}

$conexion = obtenerConexion();
$conexion->beginTransaction();
try {
    $usuario = new Usuario($conexion);
    $producto = new Producto($conexion);
    $orden = new Orden($conexion);
    $email = 'revision.' . bin2hex(random_bytes(6)) . '@example.test';
    comprobar($usuario->registrar('Prueba Aula', $email, 'Prueba123'), 'P1 registro');
    $consulta = $conexion->prepare('SELECT id, password_hash FROM usuarios WHERE email = ?');
    $consulta->execute([$email]);
    $fila = $consulta->fetch(PDO::FETCH_ASSOC);
    $id = (int) $fila['id'];
    $hash = $fila['password_hash'];
    comprobar($hash !== 'Prueba123' && password_verify('Prueba123', $hash), 'Contraseña hasheada y verificable');
    $pagina1 = $producto->listar(1, 2);
    $pagina2 = $producto->listar(2, 2);
    comprobar(count($pagina1) === 2 && count($pagina2) === 2 && $pagina1[0]['id'] !== $pagina2[0]['id'], 'Catálogo paginado');
    $seleccion = [['producto_id' => 1, 'cantidad' => 2], ['producto_id' => 2, 'cantidad' => 1]];
    comprobar(abs($orden->calcularTotal($seleccion) - 470) < 0.001, 'P2 total C$470.00');
    $emailNuevo = 'nuevo.' . $email;
    comprobar($usuario->actualizarPerfil($id, 'Perfil actualizado', $emailNuevo), 'P3 actualización de perfil');
    $actualizado = $usuario->obtenerPorId($id);
    comprobar($actualizado['nombre'] === 'Perfil actualizado' && $actualizado['email'] === $emailNuevo, 'Nombre y email persistidos');
    $consulta->execute([$emailNuevo]);
    comprobar($consulta->fetch(PDO::FETCH_ASSOC)['password_hash'] === $hash, 'Contraseña vacía conserva el hash');
    rechazar(fn() => $usuario->registrar('Duplicado', $emailNuevo, 'Prueba123'), 'El email ya esta registrado.');
    $otroEmail = 'otro.' . $email;
    $usuario->registrar('Otro usuario', $otroEmail, 'Prueba123');
    rechazar(fn() => $usuario->actualizarPerfil($id, 'No guardar', $otroEmail), 'El email ya esta registrado.');
    comprobar($usuario->obtenerPorId($id)['nombre'] === 'Perfil actualizado', 'P4 duplicado no modifica perfil');
    $usuario->actualizarPerfil($id, 'Perfil actualizado', $emailNuevo, 'Nueva123');
    $consulta->execute([$emailNuevo]);
    comprobar(password_verify('Nueva123', $consulta->fetch(PDO::FETCH_ASSOC)['password_hash']), 'Cambio opcional de contraseña');
    rechazar(fn() => $usuario->registrar('Prueba', 'no-es-email', 'Prueba123'), 'email no es valido');
    rechazar(fn() => $usuario->actualizarPerfil(-1, 'Prueba', $email), 'Usuario no encontrado');
    rechazar(fn() => $orden->calcularTotal([['producto_id' => 1, 'cantidad' => 1.5]]), 'cantidades enteras positivas');
    rechazar(fn() => $orden->calcularTotal([['producto_id' => 1, 'cantidad' => 999999]]), 'Stock insuficiente');
    rechazar(fn() => $orden->calcularTotal([]), 'Debe seleccionar');
    echo 'RESULTADO: todas las verificaciones pasaron.' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} finally {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
}
