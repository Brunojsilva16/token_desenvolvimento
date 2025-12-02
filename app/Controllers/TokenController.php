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
            'error' => $_SESSION['flash_error'] ?? null
        ]);

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $paciente = $_POST['nome_paciente'] ?? '';
        $profissionalId = $_POST['profissional_id'] ?? '';
        
        // NOVOS CAMPOS
        $nomeResp = $_POST['nome_responsavel'] ?? null;
        $respFin = $_POST['responsavel_financeiro'] ?? null;

        if (empty($paciente) || empty($profissionalId)) {
            $_SESSION['flash_error'] = "Preencha todos os campos obrigatórios!";
            $this->redirect('/gerar-token');
            return;
        }

        $tokenModel = new TokenModel();
        $codigo = $tokenModel->generateUniqueCode();

        $dados = [
            'token' => $codigo,
            'user_id' => $_SESSION['user_id'],
            'profissional_id' => $profissionalId,
            'nome_paciente' => $paciente,
            'nomeresp' => $nomeResp,       // Passando para o Model
            'responsavel_f' => $respFin    // Passando para o Model
        ];

        if ($tokenModel->create($dados)) {
            $_SESSION['flash_success'] = "Token <strong>$codigo</strong> gerado com sucesso!";
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
        if (!isset($_SESSION['user_id'])) { $this->redirect('/login'); return; }
        $id = $_GET['id'] ?? null;
        $tokenModel = new TokenModel();
        $token = $tokenModel->getById($id);
        if ($token) {
            $viewPath = __DIR__ . "/../Views/pages/print_token.phtml";
            if (file_exists($viewPath)) require $viewPath;
        } else { echo "Token não encontrado"; }
    }

    public function settle()
    {
        if (!isset($_SESSION['user_id'])) { $this->redirect('/login'); return; }
        $id = $_POST['id_token'] ?? null;
        $valor = $_POST['valor'] ?? 0;
        $formaPgto = $_POST['forma_pgto'] ?? 'DINHEIRO';

        if ($id) {
            $tokenModel = new TokenModel();
            if ($tokenModel->confirmPayment($id, $valor, $formaPgto)) {
                $_SESSION['flash_success'] = "Pagamento confirmado!";
            } else {
                $_SESSION['flash_error'] = "Erro ao registrar pagamento.";
            }
        }
        $this->redirect('/home');
    }
}