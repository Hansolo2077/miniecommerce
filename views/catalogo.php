<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<main class="contenedor">
    <h1>Mini eCommerce Aula</h1>
    <nav aria-label="Navegación principal">
        <a href="index.php">Registro</a> · <a href="catalogo.php">Catálogo</a> ·
        <a href="carrito.php">Carrito</a> · <a href="perfil.php">Perfil</a>
    </nav>
    <h2>Catálogo de productos</h2>

    <?php if (!empty($error)): ?>
        <div class="mensaje error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <p>No hay productos disponibles.</p>
    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td>C$<?= number_format((float) $item['precio'], 2) ?></td>
                        <td><?= (int) $item['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="paginacion">
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?= $pagina - 1 ?>">Anterior</a>
            <?php endif; ?>

            <span>Página <?= $pagina ?></span>

            <?php if (count($productos) === 5): ?>
                <a href="?pagina=<?= $pagina + 1 ?>">Siguiente</a>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</main>

</body>
</html>