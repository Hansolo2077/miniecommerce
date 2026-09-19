<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<main class="contenedor">
    <h1>Mini eCommerce Aula</h1>
    <nav aria-label="Navegación principal">
        <a href="index.php">Registro</a> · <a href="catalogo.php">Catálogo</a> ·
        <a href="carrito.php">Carrito</a> · <a href="perfil.php">Perfil</a>
    </nav>
    <h2>Carrito de compra</h2>

    <?php if ($error !== ''): ?>
        <div class="mensaje error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($total !== null): ?>
        <div class="mensaje exito">
            Total de la compra:
            C$<?= number_format($total, 2) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Cantidad</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($productos as $item): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($item['nombre']) ?>
                        </td>

                        <td>
                            C$<?= number_format((float) $item['precio'], 2) ?>
                        </td>

                        <td>
                            <?= (int) $item['stock'] ?>
                        </td>

                        <td>
                            <input
                                type="number"
                                name="cantidad[<?= (int) $item['id'] ?>]"
                                min="0"
                                max="<?= (int) $item['stock'] ?>"
                                value="0"
                                oninvalid="this.setCustomValidity('La cantidad no puede superar el stock disponible.')"
                                oninput="this.setCustomValidity('')"
                            >
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit">
            Calcular total
        </button>
    </form>
</main>

</body>
</html>