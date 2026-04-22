<?php
require 'db.php';

// ID do usuário a ser atualizado
$usuario_id = 3;

// Nova senha em texto simples
$nova_senha = '123456';

// Gerar o hash da nova senha
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

try {
    // Atualizar a senha no banco de dados
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->execute([$senha_hash, $usuario_id]);

    echo "Senha atualizada com sucesso para o usuário ID: $usuario_id";
} catch (PDOException $e) {
    echo "Erro ao atualizar a senha: " . $e->getMessage();
}
