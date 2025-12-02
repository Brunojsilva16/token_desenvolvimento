<?php
// app/Core/Router.php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function add($method, $route, $controller, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($route, $controller, $action)
    {
        $this->add('GET', $route, $controller, $action);
    }

    public function post($route, $controller, $action)
    {
        $this->add('POST', $route, $controller, $action);
    }

    public function dispatch()
    {
        $url = $_GET['url'] ?? '/';
        $url = '/' . trim($url, '/');
        if ($url === '/') $url = '/';

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['route'] === $url && $route['method'] === $method) {
                
                $controllerClass = $route['controller'];

                // 1. Tenta encontrar a classe exatamente como foi passada
                // (Isso funciona quando usamos 'use App\Controllers\Home;' e 'Home::class')
                if (class_exists($controllerClass)) {
                    // Classe encontrada, segue o fluxo
                } 
                // 2. Se não achou, verifica se adicionando o namespace padrão resolve
                // (Isso funciona se passar apenas a string 'HomeController')
                elseif (class_exists("App\\Controllers\\" . $controllerClass)) {
                    $controllerClass = "App\\Controllers\\" . $controllerClass;
                } 
                // 3. Se ainda não achou, é porque o Autoload não mapeou ou o arquivo não existe
                else {
                    echo "Erro Crítico: A classe controller <strong>'$controllerClass'</strong> não foi encontrada.<br>";
                    echo "Dica: Verifique se o arquivo existe em <em>app/Controllers/</em> e rode o comando <code>composer dump-autoload</code> no terminal.";
                    return;
                }

                // Instancia o controller
                $controller = new $controllerClass();
                    
                if (method_exists($controller, $route['action'])) {
                    $controller->{$route['action']}();
                } else {
                    echo "Erro: Método '{$route['action']}' não encontrado no controller '$controllerClass'.";
                }
                return;
            }
        }

        echo "404 - Página não encontrada";
    }
}