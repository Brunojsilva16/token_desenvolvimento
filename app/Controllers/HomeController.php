<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\TokenModel;

class HomeController extends BaseController
{
    public function index()
    {
        Auth::isLogged();
        
        $tokenModel = new TokenModel();
        
        // Busca os últimos 1000 registros para garantir histórico suficiente para as estatísticas e tabela
        $todosTokens = $tokenModel->getAllWithDetails(1000); 
        
        $hoje = date('Y-m-d');
        $mesAtual = date('Y-m');
        
        $atendimentosHoje = 0;
        $faturamentoHoje = 0.00;
        $faturamentoMes = 0.00;
        $totalTokens = count($todosTokens);
        
        foreach ($todosTokens as $t) {
            // Data do registro (assumindo formato Y-m-d H:i:s)
            $dataReg = substr($t['data_registro'], 0, 10); // Y-m-d
            $mesReg = substr($t['data_registro'], 0, 7);   // Y-m
            
            // Valor (limpar formatação se vier como string R$)
            $valor = $t['valor'] ?? 0;
            
            if ($dataReg == $hoje) {
                $atendimentosHoje++;
                $faturamentoHoje += $valor;
            }
            
            if ($mesReg == $mesAtual) {
                $faturamentoMes += $valor;
            }
        }
        
        // ALTERAÇÃO AQUI: 
        // Em vez de cortar apenas os 5 primeiros (array_slice), passamos a lista inteira ($todosTokens).
        // O DataTables no front-end fará a paginação de 10 em 10.
        $recentes = $todosTokens;

        $data = [
            'view' => 'home', 
            'atendimentos_hoje' => $atendimentosHoje,
            'faturamento_hoje' => $faturamentoHoje,
            'faturamento_mes' => $faturamentoMes,
            'total_tokens' => $totalTokens,
            'tokens_recentes' => $recentes,
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
        ];

        $this->view('pages/home', $data);
    }
}

