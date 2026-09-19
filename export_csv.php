<?php
require_once __DIR__ . '/functions.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="libros.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['titulo','autor','edicion','anio','isbn','categorias']);

$rows = query_books('', null, 'titulo', 'asc');
foreach ($rows as $r) {
    $cats = $r['categorias_str'] ?? '';
    fputcsv($out, [$r['titulo'],$r['autor'],$r['edicion'],$r['anio'],$r['isbn'],$cats]);
}
fclose($out);
exit;
?>