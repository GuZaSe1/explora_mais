<?php
require 'templates/header.php';
require 'templates/navbar.php';
session_start();
navbar('home');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
</head>

<body>
    <div class="main-container">
        <div class="easyui-panel" title="Gerenciamento de Usuários" style="padding:10px;">
            <div style="margin-bottom:10px;">
                <a onclick="abrirDialogInclusao()" class="easyui-linkbutton" data-options="iconCls:'icon-add'">Incluir Usuário</a>
                <a href="gerenciar_roteiros.php" class="easyui-linkbutton" data-options="iconCls:'icon-undo'">Voltar para Roteiros</a>
            </div>

            <table id="dg_usuarios" style="width:100%; height:400px"></table>
        </div>
    </div>

    <div id="dlg" class="easyui-dialog" style="width:550px; padding: 10px 20px;" closed="true" modal="true" buttons="#dlg-buttons"></div>
    <div id="dlg-buttons">
        <a class="easyui-linkbutton" iconCls="icon-ok" onclick="salvarUsuario()" style="width:90px">Salvar</a>
        <a class="easyui-linkbutton" iconCls="icon-cancel" onclick="$('#dlg').dialog('close')" style="width:90px">Cancelar</a>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        $('#dg_usuarios').datagrid({
            url: 'listar_usuarios_json.php',
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
                        title: 'Nome',
                        width: 220
                    },
                    {
                        field: 'email',
                        title: 'E-mail',
                        width: 240
                    },
                    {
                        field: 'tipo',
                        title: 'Tipo',
                        width: 120,
                        align: 'center'
                    },
                    {
                        field: 'action',
                        title: 'Ações',
                        width: 150,
                        align: 'center',
                        formatter: formatActionUsuario
                    }
                ]
            ],
            onLoadSuccess: function() {
                $('#dg_usuarios').datagrid('getPanel').find('.easyui-linkbutton').linkbutton();
            }
        });
    });

    function abrirDialogInclusao() {
        $('#dlg').dialog('open').dialog('setTitle', 'Incluir Novo Usuário');
        $('#dlg').load('controlar_usuario.php?form_only=1', function() {
            $.parser.parse('#dlg');
        });
    }

    function abrirDialogModificacao(id) {
        $('#dlg').dialog('open').dialog('setTitle', 'Modificar Usuário');
        $('#dlg').load('controlar_usuario.php?form_only=1&id=' + id, function() {
            $.parser.parse('#dlg');
        });
    }

    function salvarUsuario() {
        var form = $('#fm-usuario');
        if (!form.form('validate')) return;

        $.ajax({
            url: 'controlar_usuario.php',
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    $('#dlg').dialog('close');
                    $('#dg_usuarios').datagrid('reload');
                    $.messager.show({
                        title: 'Sucesso',
                        msg: 'Usuário salvo com sucesso.'
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

    function excluirUsuario(id) {
        $.messager.confirm('Confirmar Exclusão', 'Tem certeza que deseja excluir este usuário?', function(r) {
            if (r) {
                $.ajax({
                    url: 'excluir_usuario.php',
                    type: 'post',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $('#dg_usuarios').datagrid('reload');
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

    function formatActionUsuario(value, row) {
        var btnModificar = '<a class="easyui-linkbutton" data-options="iconCls:\'icon-edit\',plain:true" onclick="abrirDialogModificacao(' + row.id + ')">Modificar</a>';
        var btnExcluir = '<a class="easyui-linkbutton" data-options="iconCls:\'icon-remove\',plain:true" onclick="excluirUsuario(' + row.id + ')">Excluir</a>';
        return btnModificar + ' ' + btnExcluir;
    }
</script>

<?php require 'templates/footer.php'; ?>