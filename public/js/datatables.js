// public/js/datatables.js
// Configuração global para inicializar DataTables em português

$(document).ready(function() {
    // Verifica se existe alguma tabela com a classe 'dataTable' ou ID 'dataTable'
    if ($('#dataTable').length > 0 || $('.dataTable').length > 0) {
        
        // Inicializa o plugin DataTable
        $('#dataTable, .dataTable').DataTable({
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            // Ordenação padrão: primeira coluna (ID), decrescente (mais recente primeiro)
            "order": [[ 0, "desc" ]],
            // Responsividade
            "responsive": true
        });
    }
});