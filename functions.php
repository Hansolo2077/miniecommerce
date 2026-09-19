<?php
require_once __DIR__ . '/db.php';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function get_categories() {
    $stmt = db()->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
    return $stmt->fetchAll();
}

function get_category_by_name($nombre) {
    $stmt = db()->prepare("SELECT id FROM categorias WHERE nombre = ?");
    $stmt->execute([$nombre]);
    return $stmt->fetch();
}

function add_category($nombre) {
    $nombre = trim($nombre);
    if ($nombre === '') return null;
    $existing = get_category_by_name($nombre);
    if ($existing) return (int)$existing['id'];
    $stmt = db()->prepare("INSERT INTO categorias (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
    return (int)db()->lastInsertId();
}

function ensure_categories(array $nombres) {
    $ids = [];
    foreach ($nombres as $n) {
        $n = trim($n);
        if ($n === '') continue;
        $ids[] = add_category($n);
    }
    return array_values(array_unique(array_filter($ids, fn($x) => !is_null($x))));
}

function create_book($data) {
    $stmt = db()->prepare("INSERT INTO libros (titulo, autor, edicion, anio, isbn) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['titulo'] ?? '',
        $data['autor'] ?? '',
        $data['edicion'] !== '' ? $data['edicion'] : null,
        $data['anio'] !== '' ? $data['anio'] : null,
        $data['isbn'] !== '' ? $data['isbn'] : null,
    ]);
    $book_id = (int)db()->lastInsertId();
    // categorías
    $cat_ids = $data['categorias_ids'] ?? [];
    db()->prepare("DELETE FROM libro_categoria WHERE libro_id = ?")->execute([$book_id]);
    $ins = db()->prepare("INSERT IGNORE INTO libro_categoria (libro_id, categoria_id) VALUES (?, ?)");
    foreach ($cat_ids as $cid) $ins->execute([$book_id, $cid]);
    return $book_id;
}

function update_book($id, $data) {
    $stmt = db()->prepare("UPDATE libros SET titulo=?, autor=?, edicion=?, anio=?, isbn=? WHERE id=?");
    $stmt->execute([
        $data['titulo'] ?? '',
        $data['autor'] ?? '',
        $data['edicion'] !== '' ? $data['edicion'] : null,
        $data['anio'] !== '' ? $data['anio'] : null,
        $data['isbn'] !== '' ? $data['isbn'] : null,
        $id,
    ]);
    // categorías
    $cat_ids = $data['categorias_ids'] ?? [];
    db()->prepare("DELETE FROM libro_categoria WHERE libro_id = ?")->execute([$id]);
    $ins = db()->prepare("INSERT IGNORE INTO libro_categoria (libro_id, categoria_id) VALUES (?, ?)");
    foreach ($cat_ids as $cid) $ins->execute([$id, $cid]);
}

function delete_book($id) {
    db()->prepare("DELETE FROM libro_categoria WHERE libro_id = ?")->execute([$id]);
    db()->prepare("DELETE FROM libros WHERE id = ?")->execute([$id]);
}

function get_book($id) {
    $stmt = db()->prepare("SELECT * FROM libros WHERE id = ?");
    $stmt->execute([$id]);
    $b = $stmt->fetch();
    if (!$b) return null;
    $b['categorias'] = get_book_categories($id);
    return $b;
}

function get_book_categories($id) {
    $stmt = db()->prepare("
        SELECT c.id, c.nombre 
        FROM categorias c
        JOIN libro_categoria lc ON lc.categoria_id = c.id
        WHERE lc.libro_id = ?
        ORDER BY c.nombre
    ");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

function query_books($search = '', $cat_filter = null, $sort = 'titulo', $dir = 'asc') {
    $validSort = ['titulo','autor','edicion','anio','isbn','creado_en','actualizado_en'];
    $sort = in_array($sort, $validSort) ? $sort : 'titulo';
    $dir = strtolower($dir) === 'desc' ? 'DESC' : 'ASC';

    $params = [];
    $where = [];
    if ($search !== '') {
        $where[] = "(l.titulo LIKE ? OR l.autor LIKE ? OR l.isbn LIKE ?)";
        $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
    }
    if ($cat_filter) {
        $where[] = "EXISTS (SELECT 1 FROM libro_categoria lc2 WHERE lc2.libro_id = l.id AND lc2.categoria_id = ?)";
        $params[] = $cat_filter;
    }
    $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "
        SELECT 
            l.*,
            GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre SEPARATOR ' | ') AS categorias_str
        FROM libros l
        LEFT JOIN libro_categoria lc ON lc.libro_id = l.id
        LEFT JOIN categorias c ON c.id = lc.categoria_id
        $whereSql
        GROUP BY l.id
        ORDER BY $sort $dir, l.id DESC
        LIMIT 500
    ";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>