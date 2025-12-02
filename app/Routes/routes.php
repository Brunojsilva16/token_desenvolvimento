<?php
// app/Routes/routes.php

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\TokenController;
use App\Controllers\PatientController; // Novo
use App\Controllers\PanelController;
use App\Controllers\TvController;
use App\Controllers\TestController;

// --- Rotas de Teste ---
$router->get('/teste-conexao', TestController::class, 'testConnection');

// --- Auth ---
$router->get('/login', AuthController::class, 'loginForm');
$router->post('/login', AuthController::class, 'login');
$router->get('/logout', AuthController::class, 'logout');

// --- Painel ---
$router->get('/', HomeController::class, 'index');
$router->get('/home', HomeController::class, 'index');

// --- Tokens ---
$router->get('/gerar-token', TokenController::class, 'create');
$router->post('/gerar-token/salvar', TokenController::class, 'store');
$router->get('/meus-tokens', TokenController::class, 'history');
$router->get('/token/imprimir', TokenController::class, 'print');
$router->post('/token/baixar', TokenController::class, 'settle');

// --- Pacientes (NOVO) ---
$router->get('/pacientes/cadastrar', PatientController::class, 'create');
$router->post('/pacientes/salvar', PatientController::class, 'store');
// API JSON para autocomplete
$router->get('/api/pacientes/busca', PatientController::class, 'searchApi');

// --- Painel Médico ---
$router->get('/painel-atendimento', PanelController::class, 'index');
$router->get('/atendimento/chamar', PanelController::class, 'call');
$router->get('/atendimento/iniciar', PanelController::class, 'start');
$router->get('/atendimento/finalizar', PanelController::class, 'finish');
$router->get('/atendimento/cancelar', PanelController::class, 'cancel');

// --- TV ---
$router->get('/painel-tv', TvController::class, 'index');
$router->get('/tv/buscar-ultimo', TvController::class, 'buscarUltimo');