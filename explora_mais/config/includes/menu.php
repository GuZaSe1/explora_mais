<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav style="padding:15px; background:#ffffff; border-bottom:1px solid #ddd; display:flex; gap:15px; flex-wrap:wrap;">
    <a href="/explora_mais/index.php">Início</a>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="/explora_mais/explora/publico/minha_conta.php">Minha conta</a>

        <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
            <a href="/explora_mais/explora/admin/gerenciar_usuarios.php">Usuários</a>
            <a href="/explora_mais/explora/admin/gerenciar_pontos_turisticos.php">Pontos turísticos</a>
            <a href="/explora_mais/explora/admin/gerenciar_roteiros.php">Roteiros</a>
        <?php else: ?>
            <a href="/explora_mais/explora/admin/gerenciar_roteiros.php">Meus roteiros</a>
        <?php endif; ?>

        <a href="/explora_mais/autenticacao/logout.php">Sair</a>
    <?php else: ?>
        <a href="/explora_mais/autenticacao/login.php">Entrar</a>
        <a href="/explora_mais/autenticacao/cadastro.php">Criar conta</a>
    <?php endif; ?>
</nav>