<?php
require 'db.php';
require 'proteger.php';
session_start();
header('Content-Type: application/json');

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
$offset = ($page - 1) * $rows;

if ($_SESSION['usuario_tipo'] === 'admin') {

    $stmt_total = $pdo->query("SELECT COUNT(*) FROM roteiros");
    $total = (int) $stmt_total->fetchColumn();

    $sql = "
        SELECT
            r.id,
            r.nome,
            u.nome AS nome_usuario,
            COUNT(ri.id) AS qtd_pontos
        FROM roteiros r
        INNER JOIN usuarios u
                ON u.id = r.usuario_id
        LEFT JOIN roteiro_itens ri
               ON ri.roteiro_id = r.id
        GROUP BY r.id, r.nome, u.nome
        ORDER BY r.id DESC
        LIMIT :rows OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rows', $rows, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {

    $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM roteiros WHERE usuario_id = ?");
    $stmt_total->execute([$_SESSION['usuario_id']]);
    $total = (int) $stmt_total->fetchColumn();

    $sql = "
        SELECT
            r.id,
            r.nome,
            u.nome AS nome_usuario,
            COUNT(ri.id) AS qtd_pontos
        FROM roteiros r
        INNER JOIN usuarios u
                ON u.id = r.usuario_id
        LEFT JOIN roteiro_itens ri
               ON ri.roteiro_id = r.id
        WHERE r.usuario_id = :usuario_id
        GROUP BY r.id, r.nome, u.nome
        ORDER BY r.id DESC
        LIMIT :rows OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->bindParam(':rows', $rows, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$roteiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total' => $total,
    'rows' => $roteiros
]);
exit;
