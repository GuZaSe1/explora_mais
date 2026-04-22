<?php
require 'templates/header.php';
require 'templates/navbar.php';
require 'proteger.php';
navbar('home');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Roteiros</title>
</head>

<body>
    <div class="main-container" style="max-width: 1200px; margin: auto;">
        <div class="easyui-panel" title="Gerenciamento de Roteiros" style="padding:10px;">
            <div style="margin-bottom:10px;">
                <a onclick="abrirDialogInclusaoRoteiro()" class="easyui-linkbutton" data-options="iconCls:'icon-add'">Incluir Roteiro</a>
                <a href="gerenciar_usuarios.php" class="easyui-linkbutton" data-options="iconCls:'icon-man'">Gerenciar Usuários</a>
                <a href="gerenciar_pontos_turisticos.php" class="easyui-linkbutton" data-options="iconCls:'icon-tip'">Gerenciar Pontos Turísticos</a>
            </div>

            <table id="dg_roteiros" style="width:100%; height:500px"></table>
        </div>
    </div>

    <div id="dlg-roteiro" class="easyui-dialog" style="width:650px; padding: 10px 20px;" closed="true" modal="true" buttons="#dlg-roteiro-buttons"></div>
    <div id="dlg-roteiro-buttons">
        <a class="easyui-linkbutton" iconCls="icon-save" onclick="salvarRoteiro()" style="width:90px">Salvar</a>
        <a class="easyui-linkbutton" iconCls="icon-cancel" onclick="$('#dlg-roteiro').dialog('close')" style="width:90px">Cancelar</a>
    </div>

    <div id="dlg-item-roteiro" class="easyui-dialog" style="width:650px; padding: 10px 20px;" closed="true" modal="true" buttons="#dlg-item-roteiro-buttons"></div>
    <div id="dlg-item-roteiro-buttons">
        <a class="easyui-linkbutton" iconCls="icon-save" onclick="salvarItemRoteiro()" style="width:90px">Salvar</a>
        <a class="easyui-linkbutton" iconCls="icon-cancel" onclick="$('#dlg-item-roteiro').dialog('close')" style="width:90px">Cancelar</a>
    </div>
</body>

</html>

<script type="text/javascript" src="https://www.jeasyui.com/easyui/datagrid-detailview.js"></script>
<script>
    $(document).ready(function() {
        $('#dg_roteiros').datagrid({
            url: 'listar_roteiros_json.php',
            method: 'post',
            pagination: true,
            fitColumns: true,
            singleSelect: true,
            rownumbers: true,
            columns: [
                [{
                        field: 'id',
                        title: 'Código',
                        width: 80,
                        align: 'center'
                    },
                    {
                        field: 'nome',
                        title: 'Roteiro',
                        width: 220
                    },
                    {
                        field: 'nome_usuario',
                        title: 'Usuário',
                        width: 220
                    },
                    {
                        field: 'qtd_pontos',
                        title: 'Qtd. Pontos',
                        width: 100,
                        align: 'center'
                    },
                    {
                        field: 'action',
                        title: 'Ações',
                        width: 150,
                        align: 'center',
                        formatter: formatActionRoteiro
                    }
                ]
            ],
            view: detailview,
            detailFormatter: function(index, row) {
                return '<div style="padding:2px"><table class="ddv"></table></div>';
            },
            onExpandRow: function(index, rowRoteiro) {
                var ddv = $(this).datagrid('getRowDetail', index).find('table.ddv');
                ddv.datagrid({
                    url: 'listar_itens_roteiro_json.php?roteiro_id=' + rowRoteiro.id,
                    toolbar: [{
                        text: 'Adicionar Ponto Turístico',
                        iconCls: 'icon-add',
                        plain: true,
                        handler: function() {
                            abrirDialogInclusaoItem(rowRoteiro.id);
                        }
                    }],
                    fitColumns: true,
                    singleSelect: true,
                    striped: true,
                    rownumbers: true,
                    columns: [
                        [{
                                field: 'ordem_visita',
                                title: 'Ordem',
                                width: 60,
                                align: 'center'
                            },
                            {
                                field: 'nome_ponto',
                                title: 'Ponto Turístico',
                                width: 220
                            },
                            {
                                field: 'categoria',
                                title: 'Categoria',
                                width: 120
                            },
                            {
                                field: 'cidade',
                                title: 'Cidade',
                                width: 120
                            },
                            {
                                field: 'horario_visita',
                                title: 'Horário',
                                width: 90,
                                align: 'center'
                            },
                            {
                                field: 'action',
                                title: 'Ações',
                                width: 120,
                                align: 'center',
                                formatter: formatActionItemRoteiro
                            }
                        ]
                    ],
                    onResize: function() {
                        $('#dg_roteiros').datagrid('fixDetailRowHeight', index);
                    },
                    onLoadSuccess: function() {
                        setTimeout(function() {
                            $('#dg_roteiros').datagrid('fixDetailRowHeight', index);
                            ddv.datagrid('getPanel').find('.easyui-linkbutton').linkbutton();
                        }, 0);
                    }
                });
                $('#dg_roteiros').datagrid('fixDetailRowHeight', index);
            },
            onLoadSuccess: function() {
                $('#dg_roteiros').datagrid('getPanel').find('.easyui-linkbutton').linkbutton();
            }
        });
    });

    function formatActionRoteiro(value, row) {
        var btnModificar = `<a class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true" onclick="abrirDialogModificacaoRoteiro(${row.id})">Modificar</a>`;
        var btnExcluir = `<a class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="excluirRoteiro(${row.id})">Excluir</a>`;
        return btnModificar + ' ' + btnExcluir;
    }

    function formatActionItemRoteiro(value, row) {
        var btnModificar = `<a class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true" onclick="abrirDialogModificacaoItem(${row.roteiro_id}, ${row.id})"></a>`;
        var btnExcluir = `<a class="easyui-linkbutton" data-options="iconCls:'icon-cancel',plain:true" onclick="excluirItemDoRoteiro(${row.roteiro_id}, ${row.id})"></a>`;
        return btnModificar + ' ' + btnExcluir;
    }

    function abrirDialogInclusaoRoteiro() {
        $('#dlg-roteiro').dialog('open').dialog('setTitle', 'Incluir Novo Roteiro');
        $('#dlg-roteiro').load('controlar_roteiro.php?form_only=1', () => $.parser.parse('#dlg-roteiro'));
    }

    function abrirDialogModificacaoRoteiro(id) {
        $('#dlg-roteiro').dialog('open').dialog('setTitle', 'Modificar Roteiro');
        $('#dlg-roteiro').load(`controlar_roteiro.php?form_only=1&id=${id}`, () => $.parser.parse('#dlg-roteiro'));
    }

    function salvarRoteiro() {
        $('#fm-roteiro').form('submit', {
            url: 'controlar_roteiro.php',
            onSubmit: function() {
                return $(this).form('validate');
            },
            success: function(result) {
                var res = JSON.parse(result);
                if (res.success) {
                    $('#dlg-roteiro').dialog('close');
                    $('#dg_roteiros').datagrid('reload');
                    $.messager.show({
                        title: 'Sucesso',
                        msg: 'Roteiro salvo com sucesso.'
                    });
                } else {
                    $.messager.alert('Erro', res.message || 'Ocorreu um erro ao salvar.', 'error');
                }
            }
        });
    }

    function excluirRoteiro(id) {
        $.messager.confirm('Confirmar Exclusão', 'Tem certeza que deseja excluir este roteiro e todos os seus itens?', function(r) {
            if (r) {
                $.post('excluir_roteiro.php', {
                    id: id
                }, function(result) {
                    if (result.success) {
                        $('#dg_roteiros').datagrid('reload');
                        $.messager.show({
                            title: 'Sucesso',
                            msg: 'Roteiro excluído.'
                        });
                    } else {
                        $.messager.alert('Erro', result.message, 'error');
                    }
                }, 'json');
            }
        });
    }

    function abrirDialogInclusaoItem(roteiro_id) {
        if (!roteiro_id) {
            $.messager.alert('Erro', 'Roteiro inválido.', 'error');
            return;
        }
        $('#dlg-item-roteiro').dialog('open').dialog('setTitle', 'Adicionar Novo Ponto ao Roteiro');
        $('#dlg-item-roteiro').load(`controlar_item_roteiro.php?form_only=1&roteiro_id=${roteiro_id}`, () => $.parser.parse('#dlg-item-roteiro'));
    }

    function abrirDialogModificacaoItem(roteiro_id, id) {
        $('#dlg-item-roteiro').dialog('open').dialog('setTitle', 'Modificar Item do Roteiro');
        $('#dlg-item-roteiro').load(`controlar_item_roteiro.php?form_only=1&roteiro_id=${roteiro_id}&id=${id}`, () => $.parser.parse('#dlg-item-roteiro'));
    }

    function salvarItemRoteiro() {
        $('#fm-item-roteiro').form('submit', {
            url: 'controlar_item_roteiro.php',
            onSubmit: function() {
                return $(this).form('validate');
            },
            success: function(result) {
                var res = JSON.parse(result);
                if (res.success) {
                    $('#dlg-item-roteiro').dialog('close');
                    $('#dg_roteiros').datagrid('reload');
                } else {
                    $.messager.alert('Erro', res.message, 'error');
                }
            }
        });
    }

    function excluirItemDoRoteiro(roteiro_id, id) {
        $.messager.confirm('Confirmar Exclusão', 'Tem certeza que deseja excluir este item do roteiro?', function(r) {
            if (r) {
                $.post('excluir_item_roteiro.php', {
                    roteiro_id: roteiro_id,
                    id: id
                }, function(result) {
                    if (result.success) {
                        $('#dg_roteiros').datagrid('reload');
                    } else {
                        $.messager.alert('Erro', result.message, 'error');
                    }
                }, 'json');
            }
        });
    }
</script>

<?php require 'templates/footer.php'; ?>