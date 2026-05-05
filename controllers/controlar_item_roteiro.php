<?php
require __DIR__ . '/../config/db.php';
session_start();

$ponto_turistico_id = $_POST['ponto_turistico_id'] ?? '';
$ordem_visita = $_POST['ordem_visita'] ?? '';
$horario_visita = $_POST['horario_visita'] ?? null;
$roteiro_id_post = $_POST['roteiro_id'] ?? null;
$id_post = $_POST['id'] ?: null;

$query = "SELECT 1 
            FROM pontos_turisticos 
           WHERE id = :ponto_turistico_id";

$exe = $db->prepare($query, [':ponto_turistico_id' => $ponto_turistico_id]);
$row = $exe->fetch(PDO::FETCH_ASSOC);

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

    $exe = $db->prepare($query_update, $params_update);
    $row = $exe->fetch(PDO::FETCH_ASSOC);
} else {

    $query_insert = "INSERT INTO roteiro_itens (
                             roteiro_id,
                             ponto_turistico_id,
                             ordem_visita,
                             horario_visita) 
                      VALUES (
                             :roteiro_id,
                             :ponto_turistico_id,
                             :ordem_visita,
                             :horario_visita)";

    $params_insert = [
        'roteiro_id' => $roteiro_id_post,
        'ponto_turistico_id' => $ponto_turistico_id,
        'ordem_visita' => $ordem_visita,
        'horario_visita' => $horario_visita
    ];

    $db->prepare($query_insert, $params_insert);
}

$is_form_only = isset($_GET['form_only']);
$roteiro_id = $_GET['roteiro_id'] ?? null;
$id = $_GET['id'] ?? null;
$is_edit = $id !== null;

if ($roteiro_id === null) die('Roteiro é obrigatório.');

$stmt_pontos = $db->query("SELECT id, nome FROM pontos_turisticos ORDER BY nome");
$pontos = $stmt_pontos->fetchAll(PDO::FETCH_ASSOC);
$json_pontos = json_encode($pontos);

$ponto_turistico_id = '';
$ordem_visita = '';
$horario_visita = '';

if ($is_edit) {

    $query = "SELECT *
                FROM roteiro_itens 
               WHERE roteiro_id = :roteiro_id
                 AND id = :id";

    $params = ['roteiro_id' => $roteiro_id, 'id' => $id];

    $exe_query = $db->prepare($query, $params) or die(print_r($db->errorInfo(), true));
    $item_data = $exe_query->fetch(PDO::FETCH_ASSOC);

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
    require __DIR__ . '/../config/includes/header.php';
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

<?php if (!$is_form_only) require __DIR__ . '/../config/includes/footer.php'; ?>