<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/paths.php';

$cat = $_GET['categoria'] ?? null;

$sql = "SELECT den_item, preco_item, imagem_item 
          FROM item 
         WHERE cod_categoria = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$cat]);

header('Content-Type: application/json');
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($items as &$item) {
    $item['imagem_item'] = imagem_url($item['imagem_item'] ?? '');
}

echo json_encode($items);
