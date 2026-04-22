<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => true, 'message' => ''];

    $id_post = $_POST['id'] ?: null;
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tipo = $_POST['tipo'] ?? 'turista';

    if ($nome === '') {
        $response['success'] = false;
        $response['message'] = 'O nome do usuário não pode estar vazio.';
    } elseif ($email === '') {
        $response['success'] = false;
        $response['message'] = 'O e-mail do usuário não pode estar vazio.';
    } else {
        $sql_check = "SELECT id FROM usuarios WHERE email = ?";
        $params_check = [$email];

        if ($id_post) {
            $sql_check .= " AND id <> ?";
            $params_check[] = $id_post;
        }

        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute($params_check);

        if ($stmt_check->fetch()) {
            $response['success'] = false;
            $response['message'] = 'Já existe um usuário cadastrado com esse e-mail.';
        } else {
            if ($id_post) {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $tipo, $id_post]);
            } else {
                $senha_padrao = password_hash('123456', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $senha_padrao, $tipo]);
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$is_form_only = isset($_GET['form_only']);
$id_get = $_GET['id'] ?? null;
$is_edit = $id_get !== null;

$nome = '';
$email = '';
$tipo = 'turista';

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT nome, email, tipo FROM usuarios WHERE id = ?");
    $stmt->execute([$id_get]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nome = $usuario['nome'];
        $email = $usuario['email'];
        $tipo = $usuario['tipo'];
    } else {
        die('Erro: Usuário não encontrado.');
    }
}

if (!$is_form_only) {
    $pageTitle = $is_edit ? 'Modificar Usuário' : 'Incluir Novo Usuário';
    require 'templates/header.php';
}
?>

<form id="fm-usuario" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id_get ?? '') ?>">

    <div style="margin-bottom:20px; padding-top: 10px;">
        <input class="easyui-textbox" name="nome" style="width:100%" data-options="
            label: 'Nome:',
            labelWidth: 120,
            required: true,
            prompt: 'Digite o nome do usuário...'
        " value="<?= htmlspecialchars($nome) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-textbox" name="email" style="width:100%" data-options="
            label: 'E-mail:',
            labelWidth: 120,
            required: true,
            validType: 'email',
            prompt: 'Digite o e-mail...'
        " value="<?= htmlspecialchars($email) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <select class="easyui-combobox" name="tipo" style="width:100%" data-options="label:'Tipo:',labelWidth:120,required:true">
            <option value="turista" <?= $tipo === 'turista' ? 'selected' : '' ?>>Turista</option>
            <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </div>
</form>

<?php
if (!$is_form_only) {
    require 'templates/footer.php';
}
?>
