<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

use App\Models\TokenModel;

class HomeController extends BaseController
{
    public function index()
    {
        // Verifica Login
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $tokenModel = new TokenModel();

        // Busca os últimos 100 atendimentos para a tabela
        // Você pode aumentar esse número ou implementar paginação depois
        $atendimentos = $tokenModel->getAllWithDetails(400);

        // Renderiza a Home com a tabela
        $this->view('pages/home', [
            'nome' => $_SESSION['user_nome'],
            'atendimentos' => $atendimentos,

            // Injetamos um CSS específico para tabelas, se quiser customizar
            'pageStyles' => [],

            // Injetamos DataTables (plugin JS) para deixar a tabela pesquisável e ordenável
            'headerScripts' => [
                'https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css'
            ],
            'pageScripts' => [
                'https://code.jquery.com/jquery-3.5.1.js',
                'https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js',
                'https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js',
                // Script para ativar o DataTable na tabela #tabelaPagamentos
                'js/datatables.js'
            ]
        ]);
    }
}
