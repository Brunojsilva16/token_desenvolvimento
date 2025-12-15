<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\TokenModel;

class HomeController extends BaseController
{
    public function index()
    {
        // 1. VERIFICAÇÃO DE SEGURANÇA (CRUCIAL)
        // Se não estiver logado, redireciona para login e encerra a execução deste método.
        // Adicionei este método estático 'protect' no arquivo Auth.php para facilitar.
        Auth::protect();

        // --- Código do Dashboard abaixo só roda se estiver logado ---

        $tokenModel = new TokenModel();

        // Busca dados
        $todosTokens = $tokenModel->getAllWithDetails(1000);

        $hoje = date('Y-m-d');
        $mesAtual = date('Y-m');

        $atendimentosHoje = 0;
        $faturamentoHoje = 0.00;
        $faturamentoMes = 0.00;
        $totalTokens = count($todosTokens);

        foreach ($todosTokens as $t) {
            $dataReg = substr($t['data_registro'], 0, 10);
            $mesReg = substr($t['data_registro'], 0, 7);
            $valor = $t['valor'] ?? 0;

            if ($dataReg == $hoje) {
                $atendimentosHoje++;
                $faturamentoHoje += $valor;
            }

            if ($mesReg == $mesAtual) {
                $faturamentoMes += $valor;
            }
        }

        // Pega os 5 mais recentes
        $recentes = array_slice($todosTokens, 0, 5);

        // Prepara dados para a View
        $data = [
            'view' => 'home',
            'atendimentos_hoje' => $atendimentosHoje,
            'faturamento_hoje' => $faturamentoHoje,
            'faturamento_mes' => $faturamentoMes,
            'total_tokens' => $totalTokens,
            'atendimentos' => $recentes, // Dados para a tabela da Home
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
            ],

            // Dados extras do usuário logado para exibir no topo/navbar
            'usuario_nome' => Auth::name(),
            'usuario_email' => Auth::email()


        ];

        $this->view('pages/home', $data);
    }
}
