<?php
// public/index.php

session_start();

// Carrega o autoloader do Composer
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use Dotenv\Dotenv;

// echo $_SESSION['user_nome'];

// print_r($_SESSION);
// Tenta carregar as variáveis de ambiente com tratamento de erro
try {
    // Verifica se o arquivo .env existe antes de tentar carregar
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
    }
} catch (\Exception $e) {
    // Silencia erros do Dotenv em produção ou se o arquivo faltar
}

// Definição da URL Base (APP_URL)
// Usa $_ENV, $_SERVER ou um valor padrão (fallback) para evitar o erro "Undefined array key"
$urlBase = $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'http://localhost/app_mestre/public';
define('URL_BASE', rtrim($urlBase, '/'));

// Carregar Rotas
// CORREÇÃO CRÍTICA DE CAMINHO:
// __DIR__ aponta para '.../public'. Usamos '/../' para subir um nível e acessar 'app'.
$routesPath = __DIR__ . '/../app/Routes/routes.php';

if (file_exists($routesPath)) {
    $router = new Router();
    require $routesPath;
    $router->dispatch();
} else {
    die("Erro Crítico: Arquivo de rotas não encontrado em: " . $routesPath);
}