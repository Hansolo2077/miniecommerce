<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<main class="contenedor">
    <h1>Mini eCommerce Aula</h1>
    <nav aria-label="Navegación principal">
        <a href="index.php">Registro</a> · <a href="catalogo.php">Catálogo</a> ·
        <a href="carrito.php">Carrito</a> · <a href="perfil.php">Perfil</a>
    </nav>
    <h2>Registrar usuario</h2>

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

    <form method="POST">
        <label for="nombre">Nombre</label>
        <input
            type="text"
            id="nombre"
            name="nombre" maxlength="100"
            required
        >

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email" maxlength="255"
            required
        >

        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password" minlength="8" maxlength="72"
            required
        >

        <button type="submit">Registrar usuario</button>
    </form>
</main>

</body>
</html>