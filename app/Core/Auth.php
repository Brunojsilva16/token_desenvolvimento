<?php
// app/Core/Auth.php

namespace App\Core;

class Auth
{
    /**
     * Garante que a sessão foi iniciada
     */
    private static function init()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Realiza o login do usuário na sessão
     * @param array|object $user Dados do usuário vindo do banco
     */
    public static function login($user)
    {
        self::init();

        // Converte para array se for objeto, para facilitar acesso
        $userData = (array) $user;

        // Mapeia os campos comuns (ajuste conforme suas colunas no banco)
        // Tenta 'id_user' (padrão do seu banco) ou 'id'
        $_SESSION['user_id']    = $userData['id_user'] ?? $userData['id'] ?? null;
        $_SESSION['user_name']  = $userData['nome'] ?? null;
        $_SESSION['user_email'] = $userData['email'] ?? null;
        $_SESSION['user_level'] = $userData['nivel'] ?? 1;
        
        // Marca explicitamente como logado
        $_SESSION['app_logged_in'] = true;
    }

    /**
     * Verifica se está logado
     * @return bool
     */
    public static function isLogged()
    {
        self::init();
        
        // Verifica se existe ID e a flag de logado
        return isset($_SESSION['user_id']) && 
               !empty($_SESSION['user_id']) && 
               isset($_SESSION['app_logged_in']) && 
               $_SESSION['app_logged_in'] === true;
    }

    /**
     * Retorna o ID do usuário logado
     */
    public static function id()
    {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Retorna objeto com dados básicos do usuário
     */
    public static function user()
    {
        self::init();
        if (!self::isLogged()) {
            return null;
        }

        return (object) [
            'id'    => $_SESSION['user_id'],
            'nome'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'nivel' => $_SESSION['user_level']
        ];
    }

    /**
     * Desloga o usuário
     */
    public static function logout()
    {
        self::init();
        session_unset();
        session_destroy();
    }

    /**
     * Força o login (Middleware simples)
     */
    public static function requireLogin()
    {
        if (!self::isLogged()) {
            // Redireciona para login
            header('Location: ' . URL_BASE . '/login');
            exit;
        }
    }
}