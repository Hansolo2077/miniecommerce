<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar perfil</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<main class="contenedor">
    <h1>Mini eCommerce Aula</h1>
    <nav aria-label="Navegación principal">
        <a href="index.php">Registro</a> · <a href="catalogo.php">Catálogo</a> ·
        <a href="carrito.php">Carrito</a> · <a href="perfil.php">Perfil</a>
    </nav>
    <h2>Actualizar perfil</h2>

    <?php if ($mensaje !== ''): ?>
        <div class="mensaje exito">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="mensaje error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($datosUsuario): ?>
        <form method="POST">
            <label for="nombre">Nombre</label>
            <input
                type="text"
                id="nombre"
                name="nombre" maxlength="100"
                value="<?= htmlspecialchars($datosUsuario['nombre']) ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email" maxlength="255"
                value="<?= htmlspecialchars($datosUsuario['email']) ?>"
                required
            >

            <label for="password">
                Nueva contraseña
            </label>

            <input
                type="password"
                id="password"
                name="password" minlength="8" maxlength="72"
                placeholder="Dejar vacío para conservar la actual"
            >

            <button type="submit">
                Actualizar perfil
            </button>
        </form>
    <?php endif; ?>
</main>

</body>
</html>