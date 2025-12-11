<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    /**
     * Exibe o formulário de login
     */
    public function loginForm()
    {
        // Se o usuário já estiver logado, redireciona para o painel
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/home');
            return;
        }
        
        // Renderiza a view de login passando dados e estilos específicos
        $this->view('pages/login', [
            'error' => $_SESSION['login_error'] ?? null,
            
            // AQUI ESTÁ A CONFIGURAÇÃO DO CSS ESPECÍFICO
            // O layout vai ler isso e carregar 'public/css/login-custom.css'
            'pageStyles' => [
                'css/login-custom.css'
            ]
        ]);

        // Limpa a mensagem de erro da sessão após exibir
        unset($_SESSION['login_error']);
    }

    /**
     * Processa os dados do formulário (POST)
     */
    public function login()
    {
        // Filtra os dados de entrada
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['senha'] ?? '';

        // Validação básica
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = "Preencha todos os campos.";
            $this->redirect('/login');
            return;
        }

        // Instancia o Modelo para verificar no banco
        $userModel = new UserModel();
        $user = $userModel->authenticate($email, $password);

        if ($user) {
            // Login Sucesso: Salva dados na sessão
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_nivel'] = $user['nivel_acesso']; // Ex: 1 (Admin), 2 (Médico)
            
            // Redireciona para o painel
            $this->redirect('/home');
        } else {
            // Login Falha
            $_SESSION['login_error'] = "E-mail ou senha inválidos.";
            $this->redirect('/login');
        }
    }

    /**
     * Encerra a sessão
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}