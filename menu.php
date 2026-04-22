<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav style="padding:15px; background:#ffffff; border-bottom:1px solid #ddd; display:flex; gap:15px; flex-wrap:wrap;">
    <a href="index.php">Início</a>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="minha_conta.php">Minha conta</a>

        <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
            <a href="gerenciar_usuarios.php">Usuários</a>
            <a href="gerenciar_pontos_turisticos.php">Pontos turísticos</a>
            <a href="gerenciar_roteiros.php">Roteiros</a>
        <?php else: ?>
            <a href="gerenciar_roteiros.php">Meus roteiros</a>
        <?php endif; ?>

        <a href="logout.php">Sair</a>
    <?php else: ?>
        <a href="login.php">Entrar</a>
        <a href="cadastro.php">Criar conta</a>
    <?php endif; ?>
</nav>