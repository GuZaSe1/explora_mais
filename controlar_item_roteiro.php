<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => true, 'message' => ''];

    $ponto_turistico_id = $_POST['ponto_turistico_id'] ?? '';
    $ordem_visita = $_POST['ordem_visita'] ?? '';
    $horario_visita = $_POST['horario_visita'] ?? null;
    $roteiro_id_post = $_POST['roteiro_id'] ?? null;
    $id_post = $_POST['id'] ?: null;

    if (empty($ponto_turistico_id) || empty($ordem_visita) || empty($roteiro_id_post)) {
        $response['success'] = false;
        $response['message'] = 'Todos os campos obrigatórios devem ser preenchidos.';
    } else {

        $query = "SELECT 1 
                    FROM pontos_turisticos 
                   WHERE id = :ponto_turistico_id";

        $exe = $pdo->prepare($query, [':ponto_turistico_id' => $ponto_turistico_id]);
        $row = $exe->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $response['success'] = false;
            $response['message'] = 'O ponto turístico informado não existe.';
        } else {
            if ($id_post) {

                $query_update = "UPDATE roteiro_itens 
                                    SET ponto_turistico_id = :ponto_turistico_id, 
                                        ordem_visita = :ordem_visita, 
                                        horario_visita = :horario_visita
                                  WHERE id = :id AND roteiro_id = :roteiro_id";

                $params_update = [
                    ':ponto_turistico_id' => $ponto_turistico_id,
                    ':ordem_visita' => $ordem_visita,
                    ':horario_visita' => $horario_visita,
                    ':id' => $id_post,
                    ':roteiro_id' => $roteiro_id_post
                ];

                $exe = $pdo->prepare($query_update, $params_update);
                $row = $exe->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt_insert = $pdo->prepare("INSERT INTO roteiro_itens (roteiro_id, ponto_turistico_id, ordem_visita, horario_visita) VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([$roteiro_id_post, $ponto_turistico_id, $ordem_visita, $horario_visita]);
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$is_form_only = isset($_GET['form_only']);
$roteiro_id = $_GET['roteiro_id'] ?? null;
$id = $_GET['id'] ?? null;
$is_edit = $id !== null;

if ($roteiro_id === null) die('Roteiro é obrigatório.');

$stmt_pontos = $pdo->query("SELECT id, nome FROM pontos_turisticos ORDER BY nome");
$pontos = $stmt_pontos->fetchAll(PDO::FETCH_ASSOC);
$json_pontos = json_encode($pontos);

$ponto_turistico_id = '';
$ordem_visita = '';
$horario_visita = '';

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM roteiro_itens WHERE roteiro_id = ? AND id = ?");
    $stmt->execute([$roteiro_id, $id]);
    $item_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item_data) {
        $ponto_turistico_id = $item_data['ponto_turistico_id'];
        $ordem_visita = $item_data['ordem_visita'];
        $horario_visita = $item_data['horario_visita'];
    } else {
        die('Item do roteiro não encontrado.');
    }
}

if (!$is_form_only) {
    $pageTitle = $is_edit ? 'Modificar Item do Roteiro' : 'Incluir Item no Roteiro';
    require 'templates/header.php';
}
?>

<form id="fm-item-roteiro" method="post">
    <input type="hidden" name="roteiro_id" value="<?= htmlspecialchars($roteiro_id) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? '') ?>">

    <div style="margin-bottom:20px; padding-top:10px;">
        <input class="easyui-combobox" name="ponto_turistico_id" style="width:100%" data-options="
            label: 'Ponto Turístico:', labelWidth: 140, required: true,
            data: <?= htmlspecialchars($json_pontos, ENT_QUOTES, 'UTF-8') ?>,
            valueField: 'id', textField: 'nome',
            prompt: 'Digite ou selecione um ponto turístico...'
        " value="<?= htmlspecialchars($ponto_turistico_id) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-numberbox" name="ordem_visita" style="width:100%" data-options="label:'Ordem da Visita:',labelWidth:140,required:true,min:1" value="<?= htmlspecialchars($ordem_visita) ?>">
    </div>

    <div style="margin-bottom:20px;">
        <input class="easyui-timespinner" name="horario_visita" style="width:100%" data-options="label:'Horário:',labelWidth:140,showSeconds:false" value="<?= htmlspecialchars($horario_visita) ?>">
    </div>
</form>

<?php if (!$is_form_only) require 'templates/footer.php'; ?>