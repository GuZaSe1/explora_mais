<?php
require 'db.php';
session_start();

$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
$usuario_tipo = $_SESSION['usuario_tipo'] ?? '';

$stmt = $pdo->query("SELECT COUNT(*) FROM pontos_turisticos");
$total_pontos = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM roteiros");   
$total_roteiros = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
$total_usuarios = (int) $stmt->fetchColumn();

$sql_pontos = "SELECT id, nome, descricao, categoria, endereco, cidade, horario_funcionamento, preco, acessibilidade, imagem
                 FROM pontos_turisticos
             ORDER BY id DESC
                LIMIT 4";
$stmt_pontos = $pdo->query($sql_pontos);
$pontos_turisticos = $stmt_pontos->fetchAll(PDO::FETCH_ASSOC);

$sql_info = "SELECT id, tipo, nome, descricao, endereco, cidade, telefone, imagem
               FROM informacoes_uteis
           ORDER BY id DESC
              LIMIT 4";
$stmt_info = $pdo->query($sql_info);
$informacoes_uteis = $stmt_info->fetchAll(PDO::FETCH_ASSOC);

function formatarPreco($valor)
{
    if ($valor === null || $valor === '' || (float)$valor <= 0) {
        return 'Gratuito';
    }

    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

function formatarTipoInfo($tipo)
{
    $mapa = [
        'hospital' => 'Hospital',
        'farmacia_24h' => 'Farmácia 24h',
        'transporte' => 'Transporte',
        'seguranca' => 'Segurança'
    ];

    return isset($mapa[$tipo]) ? $mapa[$tipo] : ucfirst(str_replace('_', ' ', $tipo));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explora+ | Guia Turístico</title>
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
            gap: 20px;
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
            flex-wrap: wrap;
        }

        .menu a,
        .menu span {
            font-weight: 500;
            color: var(--texto-suave);
            font-size: 0.95rem;
        }

        .menu a:hover,
        .menu a.ativo {
            color: var(--primaria);
        }

        .usuario-badge {
            background: #EEF2FF;
            color: var(--primaria);
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primario {
            background: var(--primaria);
            color: white;
        }

        .btn-primario:hover {
            background: var(--primaria-hover);
            transform: translateY(-2px);
        }

        .btn-contorno {
            border: 2px solid var(--primaria);
            color: var(--primaria);
            background: transparent;
        }

        .btn-contorno:hover {
            background: var(--primaria);
            color: white;
        }

        .hero {
            background: linear-gradient(135deg, #1E1B4B 0%, #4F46E5 100%);
            padding: 100px 0 140px;
            color: white;
            text-align: center;
        }

        .hero h1 {
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            max-width: 900px;
            margin-inline: auto;
        }

        .hero p {
            font-size: 1.2rem;
            color: #C7D2FE;
            max-width: 700px;
            margin-inline: auto;
            margin-bottom: 40px;
        }

        .stats-container {
            margin-top: -70px;
            position: relative;
            z-index: 10;
            margin-bottom: 80px;
        }

        .hero-stats {
            display: flex;
            justify-content: space-between;
            background: var(--branco);
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat {
            flex: 1;
            text-align: center;
            min-width: 150px;
            border-right: 1px solid var(--borda);
        }

        .stat:last-child {
            border-right: none;
        }

        .stat strong {
            display: block;
            font-size: 2.5rem;
            color: var(--texto-forte);
            line-height: 1;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .stat span {
            color: var(--texto-suave);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        section {
            padding: 40px 0 60px;
        }

        .secao-topo {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
            border-bottom: 2px solid var(--borda);
            padding-bottom: 20px;
        }

        .secao-topo h2 {
            font-size: 2rem;
            color: var(--texto-forte);
            margin-bottom: 8px;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
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
            gap: 12px;
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
            white-space: nowrap;
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
            flex-wrap: wrap;
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

        .grid-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .info-card {
            background: var(--branco);
            padding: 30px 24px;
            border-radius: 12px;
            border-top: 4px solid var(--primaria);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .tipo-info {
            display: inline-block;
            background: #EEF2FF;
            color: var(--primaria);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .info-card h3 {
            font-size: 1.1rem;
            color: var(--texto-forte);
            margin-bottom: 12px;
        }

        .info-card p {
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: var(--texto-suave);
        }

        .info-card strong {
            color: var(--texto-forte);
        }

        .rodape {
            background: var(--texto-forte);
            color: #9CA3AF;
            padding: 60px 0;
            margin-top: 60px;
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

            .grid-info {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-stats {
                padding: 20px;
            }

            .stat {
                min-width: calc(50% - 20px);
                border-right: none;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            .topo .container {
                flex-direction: column;
                height: auto;
                padding: 15px 0;
                gap: 15px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero {
                padding: 60px 0 100px;
            }

            .secao-topo {
                flex-direction: column;
                align-items: flex-start;
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

            .grid-info {
                grid-template-columns: 1fr;
            }

            .rodape-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header class="topo">
        <div class="container">
            <a href="index.php" class="logo">Explora+</a>

            <nav class="menu">
                <a href="catalogo.php">Catálogo de Pontos</a>
                <a href="#informacoes">Informações úteis</a>
                <a href="roteiros.php">Roteiros</a>

                <?php if ($usuario_logado): ?>
                    <span class="usuario-badge">
                        Olá, <?php echo htmlspecialchars($usuario_nome); ?>
                    </span>

                    <?php if ($usuario_tipo === 'admin'): ?>
                        <a href="gerenciar_pontos_turisticos.php">Gerenciar pontos</a>
                        <a href="gerenciar_roteiros.php">Gerenciar roteiros</a>
                        <a href="gerenciar_usuarios.php">Usuários</a>
                    <?php else: ?>
                        <a href="gerenciar_roteiros.php">Meus roteiros</a>
                        <a href="minha_conta.php">Minha conta</a>
                    <?php endif; ?>

                    <a href="logout.php">Sair</a>
                <?php else: ?>
                    <a href="login.php">Entrar</a>
                    <a href="cadastro.php">Criar conta</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <?php if ($usuario_logado && $usuario_tipo === 'admin'): ?>
                    <h1>Gerencie e descubra o melhor da cidade.</h1>
                    <p>O painel administrativo do Explora+ permite o controle total sobre pontos turísticos, roteiros e informações úteis para os visitantes.</p>
                    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                        <a href="gerenciar_pontos_turisticos.php" class="btn btn-primario">Adicionar Novo Ponto</a>
                        <a href="catalogo.php" class="btn" style="background: rgba(255,255,255,0.1); color: white;">Ver Catálogo Completo</a>
                    </div>
                <?php elseif ($usuario_logado): ?>
                    <h1>Planeje seus roteiros e descubra novos destinos.</h1>
                    <p>Com sua conta no Explora+, você pode visualizar pontos turísticos e organizar roteiros personalizados para a sua viagem.</p>
                    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                        <a href="gerenciar_roteiros.php" class="btn btn-primario">Meus roteiros</a>
                        <a href="catalogo.php" class="btn" style="background: rgba(255,255,255,0.1); color: white;">Explorar destinos</a>
                    </div>
                <?php else: ?>
                    <h1>Descubra os melhores destinos e monte seu roteiro.</h1>
                    <p>Explore pontos turísticos, encontre informações úteis e crie uma conta para organizar seus próprios roteiros de viagem.</p>
                    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                        <a href="cadastro.php" class="btn btn-primario">Criar conta</a>
                        <a href="login.php" class="btn" style="background: rgba(255,255,255,0.1); color: white;">Entrar</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <div class="container stats-container">
            <div class="hero-stats">
                <div class="stat">
                    <strong><?php echo $total_pontos; ?></strong>
                    <span>Locais Cadastrados</span>
                </div>
                <div class="stat">
                    <strong><?php echo $total_roteiros; ?></strong>
                    <span>Roteiros Criados</span>
                </div>
                <div class="stat">
                    <strong><?php echo count($informacoes_uteis); ?></strong>
                    <span>Serviços Úteis</span>
                </div>
                <div class="stat">
                    <strong><?php echo $total_usuarios; ?></strong>
                    <span>Usuários Ativos</span>
                </div>
            </div>
        </div>

        <section id="pontos">
            <div class="container">
                <div class="secao-topo">
                    <div>
                        <h2>Últimos Destinos Adicionados</h2>
                        <p style="color: var(--texto-suave);">Confira os 4 cadastros mais recentes do sistema.</p>
                    </div>
                    <a href="catalogo.php" class="btn btn-contorno">Ver todo o catálogo</a>
                </div>

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

                                    <p class="descricao"><?php echo htmlspecialchars(mb_strimwidth($ponto['descricao'], 0, 140, '...')); ?></p>

                                    <div class="card-footer">
                                        <div>
                                            <span>Localização</span>
                                            <strong><?php echo htmlspecialchars($ponto['cidade']); ?></strong>
                                        </div>
                                        <div>
                                            <span>Acessibilidade</span>
                                            <strong><?php echo htmlspecialchars($ponto['acessibilidade'] ?: 'N/A'); ?></strong>
                                        </div>
                                        <div>
                                            <span>Horário</span>
                                            <strong><?php echo htmlspecialchars($ponto['horario_funcionamento'] ?: 'N/A'); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px; background: white; border-radius: 16px; border: 1px dashed var(--borda);">
                        <h3>Nenhum ponto registrado.</h3>
                        <p>Utilize o gerenciador para cadastrar os primeiros destinos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="informacoes" style="background: white; border-top: 1px solid var(--borda);">
            <div class="container">
                <div class="secao-topo" style="border-bottom: none;">
                    <div>
                        <h2>Rede de Apoio</h2>
                        <p style="color: var(--texto-suave);">Contatos e endereços de emergência e conveniência.</p>
                    </div>
                </div>

                <?php if (!empty($informacoes_uteis)): ?>
                    <div class="grid-info">
                        <?php foreach ($informacoes_uteis as $info): ?>
                            <article class="info-card">
                                <span class="tipo-info"><?php echo htmlspecialchars(formatarTipoInfo($info['tipo'])); ?></span>
                                <h3><?php echo htmlspecialchars($info['nome']); ?></h3>
                                <p style="height: 45px; overflow: hidden;"><?php echo htmlspecialchars(mb_strimwidth($info['descricao'], 0, 70, '...')); ?></p>
                                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--borda);">
                                    <p><strong>Tel:</strong> <?php echo htmlspecialchars($info['telefone'] ?: 'Não informado'); ?></p>
                                    <p><strong>End:</strong> <?php echo htmlspecialchars($info['endereco']); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: var(--texto-suave);">
                        <p>Ainda não há informações úteis cadastradas.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="rodape">
        <div class="container rodape-grid">
            <div>
                <h3>Explora+</h3>
                <p>Sistema inteligente de gestão turística.<br>Centralizando destinos, roteiros e facilidades.</p>
            </div>
            <div style="text-align: right;">
                <p>&copy; <?php echo date('Y'); ?> Explora+. Todos os direitos reservados.</p>
                <p style="font-size: 0.85rem; margin-top: 8px;">Painel Administrativo v2.0</p>
            </div>
        </div>
    </footer>
</body>

</html>