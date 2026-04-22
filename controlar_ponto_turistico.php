<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?: null;
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $horario_funcionamento = trim($_POST['horario_funcionamento'] ?? '');
    $preco = $_POST['preco'] ?? 0;
    $acessibilidade = trim($_POST['acessibilidade'] ?? '');
    $latitude = $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
    $longitude = $_POST['longitude'] !== '' ? $_POST['longitude'] : null;

    $imagem = null;
    $response = ['success' => true, 'message' => ''];

    if (!empty($_FILES['imagem']['name'])) {
        if (!is_dir('imagens')) {
            mkdir('imagens', 0777, true);
        }

        $arquivo = $_FILES['imagem'];
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $ext_permitidas)) {
            $response = ['success' => false, 'message' => 'Use imagens JPG, JPEG, PNG ou GIF.'];
        } else {
            $nome_imagem = uniqid('ponto_') . '.' . $ext;
            $destino = 'imagens/' . $nome_imagem;

            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                $imagem = $destino;
            } else {
                $response = ['success' => false, 'message' => 'Erro ao salvar a imagem.'];
            }
        }
    }

    if ($nome === '' || $descricao === '' || $categoria === '' || $endereco === '' || $cidade === '') {
        $response = ['success' => false, 'message' => 'Preencha os campos obrigatórios do ponto turístico.'];
    }

    if (!$response['success']) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if ($id) {
        $sql = "UPDATE pontos_turisticos SET nome = ?, descricao = ?, categoria = ?, endereco = ?, cidade = ?, horario_funcionamento = ?, preco = ?, acessibilidade = ?, latitude = ?, longitude = ?";
        $params = [$nome, $descricao, $categoria, $endereco, $cidade, $horario_funcionamento, $preco, $acessibilidade, $latitude, $longitude];

        if ($imagem) {
            $sql .= ", imagem = ?";
            $params[] = $imagem;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        $stmt = $pdo->prepare("INSERT INTO pontos_turisticos (nome, descricao, categoria, endereco, cidade, horario_funcionamento, preco, acessibilidade, imagem, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $categoria, $endereco, $cidade, $horario_funcionamento, $preco, $acessibilidade, $imagem, $latitude, $longitude]);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$is_form_only = isset($_GET['form_only']);
$id = $_GET['id'] ?? null;

$nome = '';
$descricao = '';
$categoria = '';
$endereco = '';
$cidade = '';
$horario_funcionamento = '';
$preco = 0;
$acessibilidade = '';
$imagem = '';
$latitude = '';
$longitude = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM pontos_turisticos WHERE id = ?");
    $stmt->execute([$id]);
    $ponto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ponto) {
        $nome = $ponto['nome'];
        $descricao = $ponto['descricao'];
        $categoria = $ponto['categoria'];
        $endereco = $ponto['endereco'];
        $cidade = $ponto['cidade'];
        $horario_funcionamento = $ponto['horario_funcionamento'];
        $preco = $ponto['preco'];
        $acessibilidade = $ponto['acessibilidade'];
        $imagem = $ponto['imagem'];
        $latitude = $ponto['latitude'];
        $longitude = $ponto['longitude'];
    } else {
        die('Ponto turístico não encontrado.');
    }
}

if (!$is_form_only) {
    $pageTitle = $id ? 'Editar Ponto Turístico' : 'Novo Ponto Turístico';
    require 'templates/header.php';
}
?>

<form id="fm-ponto" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="nome" style="width:100%" data-options="label:'Nome:',labelWidth:160,required:true" value="<?= htmlspecialchars($nome) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="descricao" style="width:100%;height:90px" data-options="label:'Descrição:',labelWidth:160,multiline:true,required:true" value="<?= htmlspecialchars($descricao) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="categoria" style="width:100%" data-options="label:'Categoria:',labelWidth:160,required:true" value="<?= htmlspecialchars($categoria) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="endereco" style="width:100%" data-options="label:'Endereço:',labelWidth:160,required:true" value="<?= htmlspecialchars($endereco) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="cidade" style="width:100%" data-options="label:'Cidade:',labelWidth:160,required:true" value="<?= htmlspecialchars($cidade) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="horario_funcionamento" style="width:100%" data-options="label:'Horário:',labelWidth:160" value="<?= htmlspecialchars($horario_funcionamento) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-numberbox" name="preco" style="width:100%" data-options="label:'Preço (R$):',labelWidth:160,precision:2,min:0" value="<?= htmlspecialchars($preco) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <select class="easyui-combobox" name="acessibilidade" style="width:100%" data-options="label:'Acessibilidade:',labelWidth:160,required:true">
            <option value="">Selecione...</option>
            <option value="Sim" <?= $acessibilidade === 'Sim' ? 'selected' : '' ?>>Sim</option>
            <option value="Parcial" <?= $acessibilidade === 'Parcial' ? 'selected' : '' ?>>Parcial</option>
            <option value="Não" <?= $acessibilidade === 'Não' ? 'selected' : '' ?>>Não</option>
        </select>
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-numberbox" name="latitude" style="width:100%" data-options="label:'Latitude:',labelWidth:160,precision:7" value="<?= htmlspecialchars($latitude) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-numberbox" name="longitude" style="width:100%" data-options="label:'Longitude:',labelWidth:160,precision:7" value="<?= htmlspecialchars($longitude) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input type="file" name="imagem" accept="image/*">
    </div>

    <?php if ($imagem): ?>
        <img src="<?= htmlspecialchars($imagem) ?>" style="max-width:150px;max-height:150px;">
    <?php endif; ?>
</form>

<?php if (!$is_form_only) require 'templates/footer.php'; ?>