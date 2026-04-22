<?php
require 'db.php';

$cat = $_GET['categoria'] ?? null;

$sql = "SELECT den_item, preco_item, imagem_item 
          FROM item 
         WHERE cod_categoria = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$cat]);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));