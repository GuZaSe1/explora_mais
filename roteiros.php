<?php
require 'db.php';
session_start();

$sql = "
    SELECT
        r.id,
        r.nome,
        r.descricao,
        r.codigo_compartilhamento,
        r.criado_em,
        u.nome AS nome_usuario,
        COUNT(ri.id) AS qtd_pontos
    FROM roteiros r
    INNER JOIN usuarios u
            ON u.id = r.usuario_id
    LEFT JOIN roteiro_itens ri
           ON ri.roteiro_id = r.id
    GROUP BY
        r.id,
        r.nome,
        r.descricao,
        r.codigo_compartilhamento,
        r.criado_em,
        u.nome
    ORDER BY r.id DESC
";

$stmt = $pdo->query($sql);
$roteiros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explora+ | Todos os Roteiros</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primaria: #4F46E5;
            --primaria-hover: #4338CA;
            --fundo: #F3F4F6;
            --branco: #ffffff;
            --texto-forte: #111827;
            --texto: #374151;
            --texto-suave: #6B7280;
            --borda: #E5E7EB;
            --container: 1280px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--fundo);
            color: var(--texto);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: min(100% - 40px, var(--container));
            margin: 0 auto;
        }

        .topo {
            background: var(--branco);
            border-bottom: 1px solid var(--borda);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .topo .container {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--primaria);
        }

        .menu {
            display: flex;
            gap: 24px;
            align-items: center;
            flex-wrap: wrap;
        }

        .menu a {
            font-weight: 500;
            color: var(--texto-suave);
        }

        .menu a:hover {
            color: var(--primaria);
        }

        .hero {
            background: linear-gradient(135deg, #1E1B4B 0%, #4F46E5 100%);
            color: white;
            padding: 70px 0;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.4rem);
            margin-bottom: 16px;
            line-height: 1.1;
        }

        .hero p {
            color: #C7D2FE;
            max-width: 760px;
            font-size: 1.08rem;
        }

        .secao {
            padding: 50px 0 70px;
        }

        .grid-roteiros {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .card {
            background: var(--branco);
            border: 1px solid var(--borda);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .tag {
            display: inline-block;
            background: #EEF2FF;
            color: var(--primaria);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            width: fit-content;
        }

        .card h3 {
            font-size: 1.2rem;
            color: var(--texto-forte);
            line-height: 1.3;
        }

        .descricao {
            color: var(--texto-suave);
            font-size: 0.95rem;
            line-height: 1.7;
            min-height: 72px;
        }

        .infos {
            border-top: 1px solid var(--borda);
            padding-top: 14px;
            display: grid;
            gap: 8px;
        }

        .infos p {
            font-size: 0.92rem;
            color: var(--texto);
        }

        .infos strong {
            color: var(--texto-forte);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 10px;
            background: var(--primaria);
            color: white;
            font-weight: 600;
            margin-top: auto;
        }

        .btn:hover {
            background: var(--primaria-hover);
        }

        .vazio {
            background: var(--branco);
            border: 1px dashed var(--borda);
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            color: var(--texto-suave);
        }

        .rodape {
            background: #111827;
            color: #9CA3AF;
            padding: 40px 0;
            margin-top: 20px;
        }

        .rodape h3 {
            color: white;
            margin-bottom: 8px;
        }

        @media (max-width: 1024px) {
            .grid-roteiros {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .topo .container {
                height: auto;
                padding: 16px 0;
                flex-direction: column;
            }

            .menu {
                justify-content: center;
            }

            .grid-roteiros {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="topo">
        <div class="container">
            <a href="index.php" class="logo">Explora+</a>

            <nav class="menu">
                <a href="index.php">Início</a>
                <a href="catalogo.php">Pontos turísticos</a>
                <a href="roteiros.php">Roteiros</a>
                <a href="index.php#informacoes">Informações úteis</a>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="gerenciar_roteiros.php">Meus roteiros</a>
                    <a href="logout.php">Sair</a>
                <?php else: ?>
                    <a href="login.php">Entrar</a>
                    <a href="cadastro.php">Criar conta</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h1>Todos os roteiros cadastrados</h1>
            <p>
                Explore roteiros criados pelos usuários do sistema e descubra combinações de passeios,
                praias, mirantes, centros culturais e muito mais.
            </p>
        </div>
    </section>

    <section class="secao">
        <div class="container">
            <?php if (!empty($roteiros)): ?>
                <div class="grid-roteiros">
                    <?php foreach ($roteiros as $roteiro): ?>
                        <article class="card">
                            <span class="tag">Roteiro</span>

                            <h3><?php echo htmlspecialchars($roteiro['nome']); ?></h3>

                            <p class="descricao">
                                <?php echo htmlspecialchars($roteiro['descricao'] ?: 'Sem descrição cadastrada.'); ?>
                            </p>

                            <div class="infos">
                                <p><strong>Criado por:</strong> <?php echo htmlspecialchars($roteiro['nome_usuario']); ?></p>
                                <p><strong>Qtd. de pontos:</strong> <?php echo (int) $roteiro['qtd_pontos']; ?></p>
                                <p><strong>Código:</strong> <?php echo htmlspecialchars($roteiro['codigo_compartilhamento']); ?></p>
                                <p><strong>Criado em:</strong> <?php echo htmlspecialchars($roteiro['criado_em']); ?></p>
                            </div>

                            <a href="detalhes_roteiro.php?id=<?php echo $roteiro['id']; ?>" class="btn">Ver detalhes</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="vazio">
                    <h3>Nenhum roteiro cadastrado ainda.</h3>
                    <p style="margin-top:10px;">Assim que os usuários criarem roteiros, eles aparecerão aqui.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="rodape">
        <div class="container">
            <h3>Explora+</h3>
            <p>Plataforma para descoberta de destinos, roteiros e informações úteis da cidade.</p>
        </div>
    </footer>
</body>

</html>