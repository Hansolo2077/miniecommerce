<?php
require_once __DIR__ . '/functions.php';

// Manejo de acciones
$msg = $_GET['msg'] ?? '';
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create' || $action === 'update') {
        $titulo = trim($_POST['titulo'] ?? '');
        $autor  = trim($_POST['autor'] ?? '');
        $edicion= trim($_POST['edicion'] ?? '');
        $anio   = trim($_POST['anio'] ?? '');
        $isbn   = trim($_POST['isbn'] ?? '');

        // categorías seleccionadas existentes
        $cats = isset($_POST['categorias']) ? (array)$_POST['categorias'] : [];
        $cats = array_map('intval', $cats);

        // nuevas categorías (separadas por coma)
        $nueva_cat = trim($_POST['nueva_categoria'] ?? '');
        $nuevas = array_filter(array_map('trim', explode(',', $nueva_cat)), fn($s)=>$s!=='');
        $nuevas_ids = ensure_categories($nuevas);

        $categorias_ids = array_values(array_unique(array_filter(array_merge($cats, $nuevas_ids))));

        $payload = [
            'titulo' => $titulo,
            'autor' => $autor,
            'edicion' => $edicion,
            'anio' => $anio,
            'isbn' => $isbn,
            'categorias_ids' => $categorias_ids
        ];

        if ($titulo === '' || $autor === '') {
            $msg = 'Título y Autor son obligatorios.';
        } else {
            if ($action === 'create') {
                $newId = create_book($payload);
                header('Location: index.php?msg='.urlencode('Libro agregado').'#lista');
                exit;
            } else {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    update_book($id, $payload);
                    header('Location: index.php?msg='.urlencode('Libro actualizado').'&edit='.$id);
                    exit;
                }
            }
        }
    }
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) delete_book($id);
        header('Location: index.php?msg='.urlencode('Libro eliminado').'#lista');
        exit;
    }
}

// Datos para UI
$all_cats = get_categories();
$search = trim($_GET['q'] ?? '');
$filtro_categoria = isset($_GET['cat']) && $_GET['cat'] !== '' ? (int)$_GET['cat'] : null;
$sort = $_GET['sort'] ?? 'titulo';
$dir = $_GET['dir'] ?? 'asc';
$rows = query_books($search, $filtro_categoria, $sort, $dir);

$edit_book = null;
if ($edit_id) $edit_book = get_book($edit_id);

?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo h(APP_TITLE); ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="header">
    <h1>📚 <?php echo h(APP_TITLE); ?></h1>
    <div style="margin-left:auto;display:flex;gap:8px">
      <a class="btn" href="export_csv.php">⬇️ Exportar CSV</a>
      <form action="import_csv.php" method="post" enctype="multipart/form-data" style="display:inline-flex;gap:8px;align-items:center">
        <input type="file" name="csv" accept=".csv" required>
        <button class="btn">⬆️ Importar CSV</button>
      </form>
    </div>
  </div>

  <div class="container">
    <?php if ($msg): ?>
      <div class="card" style="border-color:#86efac;background:#f0fdf4;color:#14532d;margin-bottom:12px">
        ✅ <?php echo h($msg); ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <h2 style="margin:4px 0 12px 0">Agregar / Editar libro</h2>
      <form method="post" class="grid">
        <input type="hidden" name="action" value="<?php echo $edit_book ? 'update' : 'create'; ?>">
        <?php if ($edit_book): ?><input type="hidden" name="id" value="<?php echo (int)$edit_book['id']; ?>"><?php endif; ?>

        <div class="col-6">
          <label>Título *</label>
          <input name="titulo" value="<?php echo h($edit_book['titulo'] ?? ''); ?>" placeholder="Ej. El principito">
        </div>
        <div class="col-6">
          <label>Autor *</label>
          <input name="autor" value="<?php echo h($edit_book['autor'] ?? ''); ?>" placeholder="Ej. Antoine de Saint-Exupéry">
        </div>
        <div class="col-6">
          <label>Edición</label>
          <input type="number" name="edicion" value="<?php echo h($edit_book['edicion'] ?? ''); ?>" placeholder="Ej. 3">
        </div>
        <div class="col-6">
          <label>Año</label>
          <input type="number" name="anio" value="<?php echo h($edit_book['anio'] ?? ''); ?>" placeholder="Ej. 2020">
        </div>
        <div class="col-12">
          <label>ISBN</label>
          <input name="isbn" value="<?php echo h($edit_book['isbn'] ?? ''); ?>" placeholder="Opcional">
        </div>
        <div class="col-12">
          <label>Categorías (selecciona varias con Ctrl/Cmd)</label>
          <select name="categorias[]" multiple size="6">
            <?php
            $selected_ids = array_map(fn($c)=>$c['id'], $edit_book['categorias'] ?? []);
            foreach ($all_cats as $c):
            ?>
              <option value="<?php echo (int)$c['id']; ?>" <?php echo in_array($c['id'],$selected_ids) ? 'selected' : ''; ?>>
                <?php echo h($c['nombre']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="preview-chips">
            <?php foreach ($edit_book['categorias'] ?? [] as $c): ?>
              <span class="badge"><?php echo h($c['nombre']); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="col-12">
          <label>Añadir nuevas categorías (separadas por coma)</label>
          <input name="nueva_categoria" placeholder="Ej. Poesía, Juvenil">
        </div>

        <div class="col-12" style="display:flex;gap:10px;margin-top:6px">
          <button class="btn primary"><?php echo $edit_book ? '💾 Guardar cambios' : '➕ Agregar libro'; ?></button>
          <?php if ($edit_book): ?>
            <a class="btn" href="index.php">✖️ Cancelar</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <div class="tools" id="lista">
      <div class="left">
        <form method="get" style="display:flex;gap:8px;align-items:center;flex:1">
          <input type="text" name="q" placeholder="Buscar por título, autor o ISBN" value="<?php echo h($search); ?>" style="flex:1">
          <select name="cat">
            <option value="">Todas las categorías</option>
            <?php foreach ($all_cats as $c): ?>
              <option value="<?php echo (int)$c['id']; ?>" <?php echo ($filtro_categoria===$c['id'])?'selected':''; ?>>
                <?php echo h($c['nombre']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select name="sort">
            <?php
              $opts = ['titulo'=>'Título','autor'=>'Autor','edicion'=>'Edición','anio'=>'Año','isbn'=>'ISBN','creado_en'=>'Creado','actualizado_en'=>'Actualizado'];
              foreach ($opts as $k=>$label):
            ?>
              <option value="<?php echo h($k); ?>" <?php echo $sort===$k?'selected':''; ?>>Ordenar: <?php echo h($label); ?></option>
            <?php endforeach; ?>
          </select>
          <select name="dir">
            <option value="asc" <?php echo $dir==='asc'?'selected':''; ?>>Asc</option>
            <option value="desc" <?php echo $dir==='desc'?'selected':''; ?>>Desc</option>
          </select>
          <button class="btn">🔍 Filtrar</button>
          <?php if ($search!=='' || $filtro_categoria || $sort!=='titulo' || $dir!=='asc'): ?>
            <a class="btn" href="index.php">♻️ Limpiar</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <div class="card">
      <h2 style="margin:4px 0 12px 0">Libros agregados (máx 500)</h2>
      <div style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>Título</th>
              <th>Autor</th>
              <th>Edición</th>
              <th>Año</th>
              <th>ISBN</th>
              <th>Categorías</th>
              <th style="text-align:right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($rows)===0): ?>
              <tr><td colspan="7">No hay registros.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr>
                <td><?php echo h($r['titulo']); ?></td>
                <td><?php echo h($r['autor']); ?></td>
                <td><?php echo h($r['edicion'] ?? ''); ?></td>
                <td><?php echo h($r['anio'] ?? ''); ?></td>
                <td><?php echo h($r['isbn'] ?? ''); ?></td>
                <td><?php echo h($r['categorias_str'] ?? ''); ?></td>
                <td style="text-align:right">
                  <a class="btn" href="index.php?edit=<?php echo (int)$r['id']; ?>">✏️ Editar</a>
                  <form method="post" style="display:inline" onsubmit="return confirm('¿Eliminar este libro?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                    <button class="btn danger">🗑 Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
      <div class="footer">Consejo: Exporta en CSV como respaldo antes de grandes cambios.</div>
    </div>

  </div>
</body>
</html>