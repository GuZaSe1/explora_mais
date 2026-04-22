<?php
require 'db.php';
session_start();

$response = ['success' => false, 'message' => 'Requisição inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roteiro_id = $_POST['roteiro_id'] ?? null;
    $id = $_POST['id'] ?? null;

    if ($roteiro_id && $id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM roteiro_itens WHERE roteiro_id = ? AND id = ?");
            $stmt->execute([$roteiro_id, $id]);
            $response['success'] = true;
            $response['message'] = 'Item do roteiro excluído com sucesso.';
        } catch (Exception $e) {
            $response['message'] = 'Erro ao excluir o item do roteiro: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Dados insuficientes para a exclusão.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
