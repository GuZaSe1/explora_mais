<?php
require 'db.php';

$response = ['success' => false, 'message' => 'Requisição inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM roteiros WHERE usuario_id = ?");
            $stmt_check->execute([$id]);
            $count = $stmt_check->fetchColumn();

            if ($count > 0) {
                $response['message'] = 'O usuário não pode ser excluído, pois possui roteiro(s) associado(s).';
            } else {
                $stmt_delete = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt_delete->execute([$id]);
                $response['success'] = true;
                $response['message'] = 'Usuário excluído com sucesso!';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Erro no banco de dados ao tentar excluir o usuário.';
        }
    } else {
        $response['message'] = 'Nenhum usuário foi informado para exclusão.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
