<?php
require 'proteger.php';
require 'db.php';
require 'templates/navbar.php';
session_start();

navbar('home');

$stmt = $pdo->prepare("SELECT nome, email, tipo, idioma, criado_em FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Pega a primeira letra do nome para o Avatar
$primeira_letra = !empty($usuario['nome']) ? mb_strtoupper(mb_substr($usuario['nome'], 0, 1, 'UTF-8')) : '?';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta | Explora+</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            line-height: 1.6;
        }

        .container {
            max-width: 750px;
            margin: 50px auto;
            padding: 0 20px;
        }

        /* Botão de Debug Oculto (Sanfona) */
        .debug-section {
            margin-bottom: 20px;
            background: #111827;
            border-radius: 12px;
            overflow: hidden;
        }

        .debug-section summary {
            padding: 12px 20px;
            color: #10B981;
            font-family: monospace;
            cursor: pointer;
            outline: none;
            list-style: none;
            font-weight: bold;
        }

        .debug-section summary::-webkit-details-marker {
            display: none;
        }

        .debug-content {
            padding: 0 20px 20px;
            color: #A7F3D0;
            font-family: monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }

        /* Cartão de Perfil */
        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #E5E7EB;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #1E1B4B 0%, #4F46E5 100%);
            padding: 40px 30px;
            color: white;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 700;
            border: 2px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(5px);
        }

        .header-info h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .header-info p {
            color: #C7D2FE;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 40px 30px;
        }

        .section-title {
            font-size: 1.2rem;
            color: #111827;
            margin-bottom: 20px;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #F3F4F6;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            background: #F9FAFB;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            transition: all 0.2s ease;
        }

        .info-item:hover {
            border-color: #D1D5DB;
            background: #F3F4F6;
        }

        .info-label {
            display: block;
            font-size: 0.8rem;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 1.05rem;
            color: #111827;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 600;
            background: #EEF2FF;
            color: #4F46E5;
            text-transform: capitalize;
        }

        @media (max-width: 600px) {
            .card-header {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <main class="container">

        <div class="card">
            <header class="card-header">
                <div class="avatar">
                    <?php echo $primeira_letra; ?>
                </div>
                <div class="header-info">
                    <h1><?php echo htmlspecialchars($usuario['nome'] ?? 'Usuário'); ?></h1>
                    <p>Membro do Explora+</p>
                </div>
            </header>

            <div class="card-body">
                <h2 class="section-title">Informações Pessoais</h2>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nome Completo</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Endereço de E-mail</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Nível de Acesso</span>
                        <span class="info-value">
                            <span class="badge"><?php echo htmlspecialchars($usuario['tipo']); ?></span>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Idioma de Preferência</span>
                        <span class="info-value"><?php echo htmlspecialchars($usuario['idioma'] ?: 'Não definido'); ?></span>
                    </div>

                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">Data de Criação da Conta</span>
                        <span class="info-value">
                            <?php
                            // Formata a data se existir, senão mostra original
                            if (!empty($usuario['criado_em'])) {
                                echo date('d/m/Y \à\s H:i', strtotime($usuario['criado_em']));
                            } else {
                                echo 'Data não disponível';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </main>
</body>

</html>