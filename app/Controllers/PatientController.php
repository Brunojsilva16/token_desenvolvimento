<?php
// app/Controllers/PatientController.php

namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\ProfessionalModel;

class PatientController extends BaseController
{
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $profModel = new ProfessionalModel();
        $profissionais = $profModel->getAll();

        $this->view('pages/cadastro_paciente', [
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

        $nome = $_POST['nome'] ?? '';
        $cpf = $_POST['cpf'] ?? '';
        $idProf = $_POST['id_prof'] ?? null;
        
        // NOVOS CAMPOS
        $nomeResponsavel = $_POST['nome_responsavel'] ?? null;
        $respFinanceiro = $_POST['responsavel_financeiro'] ?? null;

        if (empty($nome)) {
            $_SESSION['flash_error'] = "O nome é obrigatório.";
            $this->redirect('/pacientes/cadastrar');
            return;
        }

        $patientModel = new PatientModel();
        
        // Envia array completo para o Model
        $dados = [
            'nome' => $nome, 
            'cpf' => $cpf, 
            'id_prof' => $idProf,
            'nome_responsavel' => $nomeResponsavel,
            'responsavel_financeiro' => $respFinanceiro
        ];

        if ($patientModel->create($dados)) {
            $_SESSION['flash_success'] = "Paciente <strong>$nome</strong> cadastrado com sucesso!";
        } else {
            $_SESSION['flash_error'] = "Erro ao cadastrar paciente.";
        }

        $this->redirect('/pacientes/cadastrar');
    }

    public function searchApi()
    {
        header('Content-Type: application/json');
        $term = $_GET['term'] ?? '';
        
        if (strlen($term) < 2) {
            echo json_encode([]);
            return;
        }

        $patientModel = new PatientModel();
        $results = $patientModel->search($term);

        echo json_encode($results);
        exit;
    }
}