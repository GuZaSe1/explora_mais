<?php
require 'db.php';
session_start();

$categoria = $_GET['categoria'] ?? null;

if ($categoria) {
    $sql = "SELECT id, nome, descricao, preco, imagem, cidade, categoria FROM pontos_turisticos WHERE categoria = ? ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoria]);
} else {
    $sql = "SELECT id, nome, descricao, preco, imagem, cidade, categoria FROM pontos_turisticos ORDER BY nome";
    $stmt = $pdo->query($sql);
}

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
