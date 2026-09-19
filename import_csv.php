<?php
require_once __DIR__ . '/functions.php';

if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.php?msg='.urlencode('No se subió archivo CSV'));
    exit;
}

$handle = fopen($_FILES['csv']['tmp_name'], 'r');
if (!$handle) {
    header('Location: index.php?msg='.urlencode('No se pudo leer el CSV'));
    exit;
}
$header = fgetcsv($handle);
$map = [];
foreach ($header as $i => $h) {
    $map[strtolower(trim($h))] = $i;
}
$idx = fn($k) => $map[$k] ?? -1;

$count = 0;
while (($row = fgetcsv($handle)) !== false) {
    $titulo = $row[$idx('titulo')] ?? '';
    $autor  = $row[$idx('autor')] ?? '';
    $edicion = $row[$idx('edicion')] ?? '';
    $anio = $row[$idx('anio')] ?? '';
    $isbn = $row[$idx('isbn')] ?? '';
    $cat = $row[$idx('categorias')] ?? '';
    $cat_names = array_values(array_filter(array_map('trim', preg_split('/[|,]/', (string)$cat))));

    $cat_ids = ensure_categories($cat_names);

    create_book([
        'titulo' => $titulo,
        'autor' => $autor,
        'edicion' => $edicion,
        'anio' => $anio,
        'isbn' => $isbn,
        'categorias_ids' => $cat_ids
    ]);
    $count++;
}
fclose($handle);

header('Location: index.php?msg='.urlencode("Importados $count registro(s)."));
exit;
?>