<?php
require 'db.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    die('Roteiro não informado.');
}

$sql_roteiro = "
    SELECT
        r.id,
        r.nome,
        r.descricao,
        r.codigo_compartilhamento,
        r.criado_em,
        u.nome AS nome_usuario
    FROM roteiros r
    INNER JOIN usuarios u
            ON u.id = r.usuario_id
    WHERE r.id = ?
";

$stmt_roteiro = $pdo->prepare($sql_roteiro);
$stmt_roteiro->execute([$id]);
$roteiro = $stmt_roteiro->fetch(PDO::FETCH_ASSOC);

if (!$roteiro) {
    die('Roteiro não encontrado.');
}

$sql_itens = "
    SELECT
        ri.id,
        ri.ordem_visita,
        ri.horario_visita,
        pt.nome AS nome_ponto,
        pt.descricao,
        pt.categoria,
        pt.endereco,
        pt.cidade,
        pt.horario_funcionamento,
        pt.preco,
        pt.acessibilidade,
        pt.imagem
    FROM roteiro_itens ri
    INNER JOIN pontos_turisticos pt
            ON pt.id = ri.ponto_turistico_id
    WHERE ri.roteiro_id = ?
    ORDER BY ri.ordem_visita ASC
";

$stmt_itens = $pdo->prepare($sql_itens);
$stmt_itens->execute([$id]);
$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

function formatarPreco($valor)
{
    if ($valor === null || $valor === '' || (float)$valor <= 0) {
        return 'Gratuito';
    }

    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explora+ | Detalhes do Roteiro</title>
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
            --secundaria: #10B981;
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
            max-width: 900px;
            font-size: 1.05rem;
        }

        .meta-roteiro {
            margin-top: 22px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .meta-roteiro span {
            background: rgba(255, 255, 255, 0.12);
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 0.9rem;
        }

        .secao {
            padding: 50px 0 70px;
        }

        .titulo-secao {
            margin-bottom: 24px;
        }

        .titulo-secao h2 {
            font-size: 2rem;
            color: var(--texto-forte);
            margin-bottom: 8px;
        }

        .titulo-secao p {
            color: var(--texto-suave);
        }

        .grid-itens {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .item-card {
            background: var(--branco);
            border: 1px solid var(--borda);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            display: grid;
            grid-template-columns: 320px 1fr;
        }

        .item-img {
            width: 100%;
            height: 100%;
            min-height: 260px;
            object-fit: cover;
        }

        .item-conteudo {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .topo-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .ordem {
            background: #EEF2FF;
            color: var(--primaria);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .categoria {
            color: var(--primaria);
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .item-conteudo h3 {
            font-size: 1.35rem;
            color: var(--texto-forte);
        }

        .descricao {
            color: var(--texto-suave);
            line-height: 1.7;
        }

        .infos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 20px;
            padding-top: 14px;
            border-top: 1px solid var(--borda);
        }

        .infos div {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .infos span {
            color: var(--texto-suave);
            font-size: 0.85rem;
        }

        .infos strong {
            color: var(--texto-forte);
        }

        .vazio {
            background: var(--branco);
            border: 1px dashed var(--borda);
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            color: var(--texto-suave);
        }

        .acoes {
            margin-top: 24px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
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
        }

        .btn:hover {
            background: var(--primaria-hover);
        }

        .btn-secundario {
            background: white;
            color: var(--primaria);
            border: 1px solid var(--borda);
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

        @media (max-width: 900px) {
            .item-card {
                grid-template-columns: 1fr;
            }

            .item-img {
                min-height: 220px;
            }

            .infos {
                grid-template-columns: 1fr;
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
            <h1><?php echo htmlspecialchars($roteiro['nome']); ?></h1>
            <p><?php echo htmlspecialchars($roteiro['descricao'] ?: 'Sem descrição cadastrada.'); ?></p>

            <div class="meta-roteiro">
                <span><strong>Criado por:</strong> <?php echo htmlspecialchars($roteiro['nome_usuario']); ?></span>
                <span><strong>Código:</strong> <?php echo htmlspecialchars($roteiro['codigo_compartilhamento']); ?></span>
                <span><strong>Criado em:</strong> <?php echo htmlspecialchars($roteiro['criado_em']); ?></span>
                <span><strong>Total de pontos:</strong> <?php echo count($itens); ?></span>
            </div>

            <div class="acoes">
                <a href="roteiros.php" class="btn btn-secundario">Voltar para roteiros</a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="gerenciar_roteiros.php" class="btn">Abrir meus roteiros</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="secao">
        <div class="container">
            <div class="titulo-secao">
                <h2>Pontos turísticos do roteiro</h2>
                <p>Veja a ordem de visita e os detalhes de cada local incluído neste roteiro.</p>
            </div>

            <?php if (!empty($itens)): ?>
                <div class="grid-itens">
                    <?php foreach ($itens as $item): ?>
                        <article class="item-card">
                            <?php if (!empty($item['imagem'])): ?>
                                <img class="item-img" src="<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome_ponto']); ?>">
                            <?php else: ?>
                                <img class="item-img" src="https://via.placeholder.com/800x500?text=Sem+Imagem" alt="Sem imagem">
                            <?php endif; ?>

                            <div class="item-conteudo">
                                <div class="topo-item">
                                    <div>
                                        <span class="categoria"><?php echo htmlspecialchars($item['categoria']); ?></span>
                                        <h3><?php echo htmlspecialchars($item['nome_ponto']); ?></h3>
                                    </div>
                                    <span class="ordem">Parada <?php echo (int) $item['ordem_visita']; ?></span>
                                </div>

                                <p class="descricao">
                                    <?php echo htmlspecialchars($item['descricao'] ?: 'Sem descrição cadastrada.'); ?>
                                </p>

                                <div class="infos">
                                    <div>
                                        <span>Horário da visita</span>
                                        <strong><?php echo htmlspecialchars($item['horario_visita'] ?: 'Não definido'); ?></strong>
                                    </div>

                                    <div>
                                        <span>Preço</span>
                                        <strong><?php echo formatarPreco($item['preco']); ?></strong>
                                    </div>

                                    <div>
                                        <span>Cidade</span>
                                        <strong><?php echo htmlspecialchars($item['cidade']); ?></strong>
                                    </div>

                                    <div>
                                        <span>Acessibilidade</span>
                                        <strong><?php echo htmlspecialchars($item['acessibilidade'] ?: 'Não informado'); ?></strong>
                                    </div>

                                    <div>
                                        <span>Endereço</span>
                                        <strong><?php echo htmlspecialchars($item['endereco']); ?></strong>
                                    </div>

                                    <div>
                                        <span>Horário de funcionamento</span>
                                        <strong><?php echo htmlspecialchars($item['horario_funcionamento'] ?: 'Não informado'); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="vazio">
                    <h3>Este roteiro ainda não possui pontos turísticos cadastrados.</h3>
                    <p style="margin-top:10px;">Adicione itens ao roteiro no gerenciador para que eles apareçam aqui.</p>
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