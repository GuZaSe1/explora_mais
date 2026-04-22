<?php
require 'db.php';
session_start();

header('Content-Type: application/json');
if (!isset($_GET['roteiro_id'])) {
    echo json_encode(['total' => 0, 'rows' => [], 'error' => 'Roteiro não informado.']);
    exit;
}

$roteiro_id = intval($_GET['roteiro_id']);

$sql = "
    SELECT
        ri.id,
        ri.roteiro_id,
        ri.ordem_visita,
        ri.horario_visita,
        pt.nome AS nome_ponto,
        pt.categoria,
        pt.cidade
    FROM roteiro_itens ri
    JOIN pontos_turisticos pt ON ri.ponto_turistico_id = pt.id
    WHERE ri.roteiro_id = :roteiro_id
    ORDER BY ri.ordem_visita
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':roteiro_id', $roteiro_id, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['total' => count($items), 'rows' => $items]);
