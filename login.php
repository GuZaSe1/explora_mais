<?php
require 'db.php';
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $mensagem = 'Informe e-mail e senha.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            header('Location: index.php');
            exit;
        } else {
            $mensagem = 'E-mail ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Explora+</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #F3F4F6;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .box {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
        }

        .brand {
            text-align: center;
            margin-bottom: 8px;
            font-size: 1.8rem;
            font-weight: 800;
            color: #4F46E5;
            letter-spacing: -0.5px;
        }

        h1 {
            font-size: 1.2rem;
            font-weight: 500;
            color: #6B7280;
            margin-bottom: 30px;
            text-align: center;
        }

        .input-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 6px;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            outline: none;
            background: #F9FAFB;
        }

        input:focus {
            border-color: #4F46E5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        input::placeholder {
            color: #9CA3AF;
        }

        button {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 8px;
            background: #4F46E5;
            color: white;
            font-family: inherit;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .erro {
            background: #FEE2E2;
            color: #EF4444;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            border: 1px solid #FECACA;
        }

        .link-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 0.9rem;
            color: #6B7280;
        }

        .link-footer a {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .link-footer a:hover {
            color: #4338CA;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="brand">Explora+</div>
        <h1>Acesse sua conta</h1>

        <?php if ($mensagem != ''): ?>
            <div class="erro"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com">
            </div>

            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Sua senha">
            </div>

            <button type="submit">Entrar</button>
        </form>

        <div class="link-footer">
            Não tem conta? <a href="cadastro.php">Criar conta</a>
        </div>
    </div>
</body>

</html>