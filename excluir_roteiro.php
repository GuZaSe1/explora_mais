<?php
require 'db.php';
require 'proteger.php';
session_start();

$response = ['success' => false, 'message' => 'Requisição inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        if ($_SESSION['usuario_tipo'] === 'admin') {
            $stmt = $pdo->prepare("DELETE FROM roteiros WHERE id = ?");
            $stmt->execute([$id]);
            $response['success'] = true;
            $response['message'] = 'Roteiro excluído com sucesso.';
        } else {
            $stmt = $pdo->prepare("DELETE FROM roteiros WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $_SESSION['usuario_id']]);

            if ($stmt->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = 'Roteiro excluído com sucesso.';
            } else {
                $response['message'] = 'Você não pode excluir este roteiro.';
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
