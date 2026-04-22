<?php
require 'db.php';

// Busca todos os pontos turísticos (sem LIMIT)
$sql_pontos = "SELECT id, nome, descricao, categoria, endereco, cidade, horario_funcionamento, preco, acessibilidade, imagem
                 FROM pontos_turisticos
             ORDER BY nome ASC"; // Ordenado por nome para facilitar no catálogo
$stmt_pontos = $pdo->query($sql_pontos);
$pontos_turisticos = $stmt_pontos->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Catálogo Completo | Explora+</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* O CSS principal se mantém para garantir a identidade visual */
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
            --container: 1300px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--fundo);
            color: var(--texto);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: 0.2s;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(100% - 40px, var(--container));
            margin: 0 auto;
        }

        /* TOPO */
        .topo {
            background: var(--branco);
            border-bottom: 1px solid var(--borda);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topo .container {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--primaria);
            letter-spacing: -0.5px;
        }

        .menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .menu a {
            font-weight: 500;
            color: var(--texto-suave);
            font-size: 0.95rem;
        }

        .menu a:hover,
        .menu a.ativo {
            color: var(--primaria);
        }

        /* HEADER INTERNO */
        .header-interno {
            background: var(--branco);
            padding: 40px 0;
            border-bottom: 1px solid var(--borda);
            margin-bottom: 40px;
        }

        .header-interno h1 {
            font-size: 2.5rem;
            color: var(--texto-forte);
        }

        .header-interno p {
            color: var(--texto-suave);
            font-size: 1.1rem;
            margin-top: 8px;
        }

        /* CARDS HORIZONTAIS */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 60px;
        }

        .card {
            display: flex;
            background: var(--branco);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--borda);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .card-img-wrapper {
            width: 35%;
            min-height: 250px;
            position: relative;
        }

        .card-img-wrapper img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-conteudo {
            width: 65%;
            padding: 24px;
            display: flex;
            flex-direction: column;
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .card h3 {
            font-size: 1.3rem;
            color: var(--texto-forte);
            line-height: 1.3;
        }

        .preco {
            background: #ECFDF5;
            color: var(--secundaria);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .categoria-chip {
            display: inline-block;
            color: var(--primaria);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .descricao {
            font-size: 0.95rem;
            color: var(--texto-suave);
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .card-footer {
            display: flex;
            gap: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--borda);
            font-size: 0.85rem;
        }

        .card-footer div {
            display: flex;
            flex-direction: column;
        }

        .card-footer span {
            color: var(--texto-suave);
            font-weight: 500;
        }

        .card-footer strong {
            color: var(--texto-forte);
        }

        /* FOOTER */
        .rodape {
            background: var(--texto-forte);
            color: #9CA3AF;
            padding: 60px 0;
            margin-top: auto;
        }

        .rodape-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .rodape h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }

        @media (max-width: 1024px) {
            .grid-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .topo .container {
                flex-direction: column;
                height: auto;
                padding: 15px 0;
                gap: 15px;
            }

            .card {
                flex-direction: column;
            }

            .card-img-wrapper {
                width: 100%;
                height: 220px;
                min-height: auto;
            }

            .card-conteudo {
                width: 100%;
            }

            .rodape-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
    </style>
</head>

<body style="display: flex; flex-direction: column; min-height: 100vh;">
    <header class="topo">
        <div class="container">
            <a href="index.php" class="logo">Explora+</a>
            <nav class="menu">
                <a href="index.php">Início</a>
                <a href="catalogo.php" class="ativo">Catálogo de Pontos</a>
            </nav>
        </div>
    </header>

    <div class="header-interno">
        <div class="container">
            <h1>Catálogo Completo</h1>
            <p>Explore todos os <?php echo count($pontos_turisticos); ?> destinos turísticos cadastrados na nossa base de dados.</p>
        </div>
    </div>

    <main class="container">
        <?php if (!empty($pontos_turisticos)): ?>
            <div class="grid-cards">
                <?php foreach ($pontos_turisticos as $ponto): ?>
                    <article class="card">
                        <div class="card-img-wrapper">
                            <?php if (!empty($ponto['imagem'])): ?>
                                <img src="<?php echo htmlspecialchars($ponto['imagem']); ?>" alt="<?php echo htmlspecialchars($ponto['nome']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/800x600?text=Sem+Foto" alt="Sem imagem">
                            <?php endif; ?>
                        </div>

                        <div class="card-conteudo">
                            <span class="categoria-chip"><?php echo htmlspecialchars($ponto['categoria']); ?></span>

                            <div class="card-header-flex">
                                <h3><?php echo htmlspecialchars($ponto['nome']); ?></h3>
                                <span class="preco"><?php echo formatarPreco($ponto['preco']); ?></span>
                            </div>

                            <p class="descricao"><?php echo htmlspecialchars($ponto['descricao']); ?></p>

                            <div class="card-footer">
                                <div>
                                    <span>Localização</span>
                                    <strong><?php echo htmlspecialchars($ponto['cidade']); ?> - <?php echo htmlspecialchars($ponto['endereco']); ?></strong>
                                </div>
                                <div>
                                    <span>Acessibilidade</span>
                                    <strong><?php echo htmlspecialchars($ponto['acessibilidade'] ?: 'Não informada'); ?></strong>
                                </div>
                                <div>
                                    <span>Horário</span>
                                    <strong><?php echo htmlspecialchars($ponto['horario_funcionamento'] ?: 'Sob consulta'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 16px; border: 1px dashed var(--borda); margin-bottom: 60px;">
                <h3>Nenhum ponto registrado.</h3>
                <p>Volte para o painel inicial e adicione novos destinos.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="rodape">
        <div class="container rodape-grid">
            <div>
                <h3>Explora+</h3>
                <p>Sistema inteligente de gestão turística.</p>
            </div>
            <div style="text-align: right;">
                <p>&copy; <?php echo date('Y'); ?> Explora+. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>

</html>