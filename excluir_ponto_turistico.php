<?php
require 'db.php';
session_start();

$response = ['success' => false, 'message' => 'Requisição inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM roteiro_itens WHERE ponto_turistico_id = ?");
            $stmt_check->execute([$id]);
            $count = $stmt_check->fetchColumn();

            if ($count > 0) {
                $response['message'] = 'O ponto turístico não pode ser excluído, pois está associado a roteiro(s).';
            } else {
                $stmt_delete = $pdo->prepare("DELETE FROM pontos_turisticos WHERE id = ?");
                $stmt_delete->execute([$id]);
                $response['success'] = true;
                $response['message'] = 'Ponto turístico excluído com sucesso!';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro ao excluir ponto turístico.';
        }
    } else {
        $response['message'] = 'Nenhum ponto turístico foi informado para exclusão.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
