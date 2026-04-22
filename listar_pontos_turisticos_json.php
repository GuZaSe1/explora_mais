<?php
require 'db.php';
session_start();

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page - 1) * $rows;

$stmt_total = $pdo->query("SELECT COUNT(*) FROM pontos_turisticos");
$total = $stmt_total->fetchColumn();

$stmt_rows = $pdo->prepare("SELECT id, nome, categoria, cidade, preco FROM pontos_turisticos ORDER BY nome LIMIT :rows OFFSET :offset");
$stmt_rows->bindParam(':rows', $rows, PDO::PARAM_INT);
$stmt_rows->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_rows->execute();
$items = $stmt_rows->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(["total" => $total, "rows" => $items]);
