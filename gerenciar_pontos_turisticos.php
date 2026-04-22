<?php
require 'templates/header.php';
require 'templates/navbar.php';
ini_set('max_file_uploads', '100');
session_start();
navbar('home');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pontos Turísticos</title>
</head>

<body>
    <div class="main-container">
        <div class="easyui-panel" title="Gerenciamento de Pontos Turísticos" style="padding:10px;">
            <div style="margin-bottom:10px;">
                <a onclick="abrirDialogInclusaoPonto()" class="easyui-linkbutton" data-options="iconCls:'icon-add'">Incluir Ponto Turístico</a>
                <a href="gerenciar_roteiros.php" class="easyui-linkbutton" data-options="iconCls:'icon-undo'">Voltar para Roteiros</a>
            </div>

            <table id="dg_pontos" class="easyui-datagrid" style="width:100%; height:450px"
                data-options="url:'listar_pontos_turisticos_json.php', method:'post', pagination:true, fitColumns:true, singleSelect:true, onLoadSuccess: function() { $('.easyui-linkbutton').linkbutton(); }">
                <thead>
                    <tr>
                        <th data-options="field:'id',width:80">Código</th>
                        <th data-options="field:'nome',width:220">Nome</th>
                        <th data-options="field:'categoria',width:120">Categoria</th>
                        <th data-options="field:'cidade',width:120">Cidade</th>
                        <th data-options="field:'preco',width:120,align:'right',formatter:formatCurrency">Preço</th>
                        <th data-options="field:'action',width:150,align:'center',formatter:formatActionPonto">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="dlg-ponto" class="easyui-dialog" style="width:650px; padding: 10px 20px;" closed="true" modal="true" buttons="#dlg-ponto-buttons"></div>
    <div id="dlg-ponto-buttons">
        <a class="easyui-linkbutton c6" iconCls="icon-ok" onclick="salvarPonto()" style="width:90px">Salvar</a>
        <a class="easyui-linkbutton" iconCls="icon-cancel" onclick="$('#dlg-ponto').dialog('close')" style="width:90px">Cancelar</a>
    </div>
</body>

</html>

<script>
    function abrirDialogInclusaoPonto() {
        $('#dlg-ponto').dialog('open').dialog('setTitle', 'Incluir Novo Ponto Turístico');
        $('#dlg-ponto').load('controlar_ponto_turistico.php?form_only=1', function() {
            $.parser.parse('#dlg-ponto');
        });
    }

    function abrirDialogModificacaoPonto(id) {
        $('#dlg-ponto').dialog('open').dialog('setTitle', 'Modificar Ponto Turístico');
        $('#dlg-ponto').load('controlar_ponto_turistico.php?form_only=1&id=' + id, function() {
            $.parser.parse('#dlg-ponto');
        });
    }

    function salvarPonto() {
        var form = $('#fm-ponto');
        if (!form.form('validate')) return;

        var formData = new FormData(form[0]);

        $.ajax({
            url: 'controlar_ponto_turistico.php',
            type: 'post',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(result) {
                if (result.success) {
                    $('#dlg-ponto').dialog('close');
                    $('#dg_pontos').datagrid('reload');
                    $.messager.show({
                        title: 'Sucesso',
                        msg: 'Ponto turístico salvo com sucesso.'
                    });
                } else {
                    $.messager.alert('Erro', result.message, 'error');
                }
            },
            error: function() {
                $.messager.alert('Erro Crítico', 'Não foi possível contatar o servidor.', 'error');
            }
        });
    }

    function excluirPonto(id) {
        $.messager.confirm('Confirmar Exclusão', 'Tem certeza que deseja excluir este ponto turístico?', function(r) {
            if (r) {
                $.ajax({
                    url: 'excluir_ponto_turistico.php',
                    type: 'post',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $('#dg_pontos').datagrid('reload');
                            $.messager.show({
                                title: 'Sucesso',
                                msg: result.message
                            });
                        } else {
                            $.messager.alert('Erro na Exclusão', result.message, 'error');
                        }
                    },
                    error: function() {
                        $.messager.alert('Erro Crítico', 'Não foi possível contatar o servidor para exclusão.', 'error');
                    }
                });
            }
        });
    }

    function formatActionPonto(value, row) {
        var btnModificar = '<a class="easyui-linkbutton" data-options="iconCls:\'icon-edit\',plain:true" onclick="abrirDialogModificacaoPonto(' + row.id + ')">Modificar</a>';
        var btnExcluir = '<a class="easyui-linkbutton" data-options="iconCls:\'icon-remove\',plain:true" onclick="excluirPonto(' + row.id + ')">Excluir</a>';
        return btnModificar + ' ' + btnExcluir;
    }

    function formatCurrency(value) {
        if (value === null || value === undefined || value === '') return '';
        var val = parseFloat(value);
        if (isNaN(val)) return '';
        return val.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }
</script>

<?php require 'templates/footer.php'; ?>