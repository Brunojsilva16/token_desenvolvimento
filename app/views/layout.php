<?php // app/Views/layout.php ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Controle Token' ?></title>
    
    <!-- Fontes e Ícones -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- CSS Global -->
    
    <!-- 1. BOOTSTRAP 5 VIA CDN (Correção do erro visual) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 2. Seu CSS Personalizado -->
    <link href="<?= URL_BASE ?>/css/style.css" rel="stylesheet">

    <!-- CSS DINÂMICO DA PÁGINA (Login, etc) -->
    <?php if (isset($pageStyles) && is_array($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <?php $href = (strpos($style, 'http') === 0) ? $style : URL_BASE . '/' . $style; ?>
            <link rel="stylesheet" href="<?= $href ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Scripts de Cabeçalho -->
    <?php if (isset($headerScripts) && is_array($headerScripts)): ?>
        <?php foreach ($headerScripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="<?= isset($_SESSION['user_id']) ? 'body-admin' : 'body-login' ?>">

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php require __DIR__ . '/partials/navbar.phtml'; ?>
        <div class="d-flex" id="wrapper">
            <?php require __DIR__ . '/partials/sidebar.phtml'; ?>
            <main class="w-100 p-4 main-content">
                <?= $content ?> 
            </main>
        </div>
    <?php else: ?>
        <!-- Wrapper de Login -->
        <div class="login-wrapper">
            <?= $content ?>
        </div>
    <?php endif; ?>

    <!-- Scripts Globais -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="<?= URL_BASE ?>/js/function.js"></script> -->

    <!-- SCRIPTS DINÂMICOS (Rodapé) -->
    <?php if (isset($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <?php $src = (strpos($script, 'http') === 0) ? $script : URL_BASE . '/' . $script; ?>
            <script src="<?= $src ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>