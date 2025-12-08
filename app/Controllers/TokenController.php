<?php
// app/Controllers/TokenController.php

namespace App\Controllers;

use App\Models\ProfessionalModel;
use App\Models\TokenModel;

class TokenController extends BaseController
{
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $profModel = new ProfessionalModel();
        $profissionais = $profModel->getAll();

        $this->view('pages/gerar_token', [
            'profissionais' => $profissionais,
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
            'pageStyles' => [
                'css/gerartoken.css'
            ]
        ]);

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        // Dados Básicos
        $paciente = $_POST['nome_paciente'] ?? '';
        $profissionalId = $_POST['profissional_id'] ?? '';

        // Dados Pessoais Extras
        $cpf = $_POST['cpf_paciente'] ?? null;
        $nomeResp = $_POST['nome_responsavel'] ?? null;
        $respFin = $_POST['responsavel_financeiro'] ?? null;

        // Dados Financeiros
        $valorRaw = $_POST['valor'] ?? '0';
        $valor = str_replace(['R$', '.', ','], ['', '', '.'], $valorRaw);

        $formapag = $_POST['formapagamento'] ?? null;
        $nomeBanco = $_POST['nome_banco'] ?? null; // NOVO CAMPO
        $modalidadep = $_POST['modalidade'] ?? null;
        $vencimento = $_POST['vencimento'] ?? date('Y-m-d');

        // Agendamento
        $sessoes = $_POST['sessoes'] ?? [];

        if (empty($paciente) || empty($profissionalId)) {
            $_SESSION['flash_error'] = "Preencha todos os campos obrigatórios!";
            $this->redirect('/gerar-token');
            return;
        }

        $tokenModel = new TokenModel();
        $codigo = $tokenModel->generateUniqueCode();

        $dados = [
            'token' => $codigo,
            'id_user' => $_SESSION['user_id'],
            'id_prof' => $profissionalId,
            'paciente' => $paciente,
            'cpf' => $cpf,
            'nome_Resp' => $nomeResp,
            'responsavel_financeiro' => $respFin,
            // Financeiro
            'nome_banco' => $nomeBanco, // Passando para o model
            'valor' => $valor,
            'formapagamento' => $formapag,
            'modalidade' => $modalidadep,
            'vencimento' => $vencimento,
            // Sessões
            'sessoes' => $sessoes
        ];

        if ($tokenModel->create($dados)) {
            $_SESSION['flash_success'] = "Token <strong>$codigo</strong> gerado e sessões agendadas!";
        } else {
            $_SESSION['flash_error'] = "Erro ao gerar token.";
        }

        $this->redirect('/gerar-token');
    }

    public function history()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $tokenModel = new TokenModel();
        $meusTokens = $tokenModel->getByUserId($_SESSION['user_id']);
        $this->view('pages/meus_tokens', ['tokens' => $meusTokens]);
    }

    public function print()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $id = $_GET['id'] ?? null;
        $tokenModel = new TokenModel();
        $token = $tokenModel->getById($id);
        if ($token) {
            $viewPath = __DIR__ . "/../Views/pages/print_token.phtml";
            if (file_exists($viewPath)) require $viewPath;
        } else {
            echo "Token não encontrado";
        }
    }

    /**
     * Exibe o formulário de edição
     */
    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/home');
            return;
        }

        $tokenModel = new TokenModel();
        $token = $tokenModel->getById($id);

        $profModel = new ProfessionalModel();

        $this->view('pages/editar_token', [
            'token' => $token,
            'profissionais' => $profModel->getAll()
        ]);
    }

    /**
     * Processa a atualização dos dados
     */
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $id = $_POST['id_token'] ?? null;

        if (!$id) {
            $this->redirect('/home');
            return;
        }

        // Coleta dados do formulário
        $paciente = $_POST['nome_paciente'] ?? '';
        $profissionalId = $_POST['profissional_id'] ?? '';

        $dados = [
            'id_prof' => $profissionalId,
            'paciente' => $paciente,
            'cpf' => $_POST['cpf_paciente'] ?? null,
            'nomeresp' => $_POST['nome_responsavel'] ?? null,
            'responsavel_f' => $_POST['responsavel_financeiro'] ?? null,
            'nome_banco' => $_POST['nome_banco'] ?? null,
            'formapag' => $_POST['formapag'] ?? null,
            'modalidade' => $_POST['modalidade'] ?? null,
            'vencimento' => $_POST['vencimento'] ?? null
        ];

        // Tratamento do Valor (R$)
        $valorRaw = $_POST['valor'] ?? '0';
        $dados['valor'] = str_replace(['R$', '.', ','], ['', '', '.'], $valorRaw);

        $tokenModel = new TokenModel();

        if ($tokenModel->update($id, $dados)) {
            $_SESSION['flash_success'] = "Atendimento atualizado com sucesso!";
        } else {
            $_SESSION['flash_error'] = "Erro ao atualizar atendimento.";
        }

        $this->redirect('/home'); // Volta para a tabela de controle
    }
}
