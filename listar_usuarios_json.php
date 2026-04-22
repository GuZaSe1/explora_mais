<?php
require 'db.php';
require 'proteger.php';
session_start();
header('Content-Type: application/json');

$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$rows = isset($_POST['rows']) ? (int) $_POST['rows'] : 10;
$offset = ($page - 1) * $rows;

$stmt_total = $pdo->query("SELECT COUNT(*) FROM usuarios");
$total = (int) $stmt_total->fetchColumn();

$sql = "
    SELECT
        id,
        nome,
        email,
        tipo,
        idioma,
        criado_em
    FROM usuarios
    ORDER BY id DESC
    LIMIT :rows OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':rows', $rows, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total' => $total,
    'rows' => $usuarios
]);
exit;
