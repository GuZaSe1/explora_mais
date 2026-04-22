<?php
require 'db.php';
require 'proteger.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => true, 'message' => ''];

    $id = $_POST['id'] ?: null;
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome === '') {
        $response['success'] = false;
        $response['message'] = 'Informe o nome do roteiro.';
    } else {
        $usuario_id = $_SESSION['usuario_id'];

        if ($id) {
            if ($_SESSION['usuario_tipo'] !== 'admin') {
                $stmt = $pdo->prepare("SELECT id FROM roteiros WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$id, $_SESSION['usuario_id']]);

                if (!$stmt->fetch()) {
                    $response['success'] = false;
                    $response['message'] = 'Você não tem permissão para editar este roteiro.';
                }
            }

            if ($response['success']) {
                $stmt = $pdo->prepare("
                    UPDATE roteiros
                       SET nome = ?, descricao = ?
                     WHERE id = ?
                ");
                $stmt->execute([$nome, $descricao, $id]);
            }
        } else {
            $codigo = uniqid('ROT-');

            $stmt = $pdo->prepare("
                INSERT INTO roteiros (usuario_id, nome, descricao, codigo_compartilhamento)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$usuario_id, $nome, $descricao, $codigo]);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

/*
|--------------------------------------------------------------------------
| A PARTIR DAQUI É O FORMULÁRIO DO DIALOG
|--------------------------------------------------------------------------
*/

$is_form_only = isset($_GET['form_only']);
$id = $_GET['id'] ?? null;

$nome = '';
$descricao = '';
$is_edit = $id !== null;

if ($is_edit) {
    if ($_SESSION['usuario_tipo'] === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM roteiros WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM roteiros WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $_SESSION['usuario_id']]);
    }

    $roteiro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$roteiro) {
        die('Roteiro não encontrado ou sem permissão.');
    }

    $nome = $roteiro['nome'];
    $descricao = $roteiro['descricao'];
}

if (!$is_form_only) {
    $pageTitle = $is_edit ? 'Modificar Roteiro' : 'Incluir Novo Roteiro';
    require 'templates/header.php';
}
?>

<form id="fm-roteiro" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">

    <div style="margin-bottom:20px; padding-top:10px;">
        <input class="easyui-textbox"
               name="nome"
               style="width:100%"
               data-options="
                    label:'Nome do Roteiro:',
                    labelWidth:130,
                    required:true,
                    prompt:'Digite o nome do roteiro...'
               "
               value="<?php echo htmlspecialchars($nome); ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox"
               name="descricao"
               style="width:100%; height:100px"
               data-options="
                    label:'Descrição:',
                    labelWidth:130,
                    multiline:true,
                    prompt:'Descreva o roteiro...'
               "
               value="<?php echo htmlspecialchars($descricao); ?>">
    </div>
</form>

<?php
if (!$is_form_only) {
    require 'templates/footer.php';
}
?>