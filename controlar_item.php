<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Valores enviados pelo formulário
    $cod_item      = $_POST['cod_item'] ?: null;
    $den_item      = trim($_POST['den_item'] ?? '');
    $qtd_estoque   = $_POST['qtd_estoque'] ?? 0;
    $preco_item    = $_POST['preco_item'] ?? 0;
    $cod_categoria = $_POST['cod_categoria'] ?? null;

    $imagem_item = null;
    $response = ['success' => true, 'message' => ''];

    if (!empty($_FILES['imagem_item']['name'])) {

        // Cria a pasta se ela não existir
        if (!is_dir('imagens')) {
            mkdir('imagens', 0777, true);
        }

        $arquivo = $_FILES['imagem_item'];
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        // Valida extensão
        if (!in_array($ext, $ext_permitidas)) {
            $response = ['success' => false, 'message' => 'Use imagens JPG, JPEG, PNG ou GIF.'];
        } else {
            $nome_imagem = uniqid('img_') . '.' . $ext;
            $destino = "imagens/" . $nome_imagem;

            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                $imagem_item = $destino;
            } else {
                $response = ['success' => false, 'message' => 'Erro ao salvar a imagem.'];
            }
        }
    }

    if ($den_item === '') {
        $response = ['success' => false, 'message' => 'Informe o nome/descrição do item.'];
    }

    if (!$cod_categoria) {
        $response = ['success' => false, 'message' => 'Selecione uma categoria.'];
    }

    // Se deu erro acima, retorna agora
    if (!$response['success']) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if ($cod_item) {

        // Atualizar item existente
        $sql = "UPDATE item
                SET den_item     = ?,
                    qtd_estoque  = ?,
                    preco_item   = ?,
                    cod_categoria = ?";

        $params = [$den_item, $qtd_estoque, $preco_item, $cod_categoria];

        // Só atualiza imagem se foi enviada
        if ($imagem_item) {
            $sql .= ", imagem_item = ?";
            $params[] = $imagem_item;
        }

        $sql .= " WHERE cod_item = ?";
        $params[] = $cod_item;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {

        // INSERIR NOVO ITEM — cod_item agora é AUTO_INCREMENT
        $sql = "INSERT INTO item
            (den_item, qtd_estoque, preco_item, imagem_item, cod_categoria)
            VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $den_item,
            $qtd_estoque,
            $preco_item,
            $imagem_item,
            $cod_categoria
        ]);
    }

    // Retorna resultado para AJAX
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$is_form_only = isset($_GET['form_only']);
$cod_item = $_GET['cod_item'] ?? null;

$den_item = '';
$qtd_estoque = 0;
$preco_item = 0;
$imagem_item = '';
$cod_categoria = '';

if ($cod_item) {
    $stmt = $pdo->prepare("SELECT * FROM item WHERE cod_item = ?");
    $stmt->execute([$cod_item]);
    $item = $stmt->fetch();

    if ($item) {
        $den_item      = $item['den_item'];
        $qtd_estoque   = $item['qtd_estoque'];
        $preco_item    = $item['preco_item'];
        $imagem_item   = $item['imagem_item'];
        $cod_categoria = $item['cod_categoria'];
    } else {
        die("Item não encontrado.");
    }
}

$categorias = $pdo->query("SELECT cod_categoria, nome_categoria FROM categoria_item ORDER BY nome_categoria")->fetchAll();

if (!$is_form_only) {
    $pageTitle = $cod_item ? 'Editar Item' : 'Novo Item';
    require 'templates/header.php';
}
?>

<form id="fm-item" method="post" enctype="multipart/form-data">

    <input type="hidden" name="cod_item" value="<?= $cod_item ?>">

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="den_item" style="width:100%"
            data-options="label:'Descrição:',labelWidth:140,required:true"
            value="<?= htmlspecialchars($den_item) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-numberbox" name="qtd_estoque" style="width:100%"
            data-options="label:'Estoque:',labelWidth:140,min:0"
            value="<?= $qtd_estoque ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-numberbox" name="preco_item" style="width:100%"
            data-options="label:'Preço (R$):',labelWidth:140,precision:2"
            value="<?= $preco_item ?>">
    </div>

    <div style="margin-bottom:20px;">
        <select class="easyui-combobox" name="cod_categoria" style="width:100%" data-options="label:'Categoria:',labelWidth:140,required:true">
            <option value="">Selecione...</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['cod_categoria'] ?>"
                    <?= $cod_categoria == $c['cod_categoria'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome_categoria']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:20px;">
        <input type="file" name="imagem_item" accept="image/*">
    </div>

    <?php if ($imagem_item): ?>
        <img src="<?= $imagem_item ?>" style="max-width:150px;max-height:150px;">
    <?php endif; ?>
</form>

<?php
if (!$is_form_only) {
    require 'templates/footer.php';
}
?>