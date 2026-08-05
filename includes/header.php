<?php
require_once __DIR__ . '/../config/config.php';
$pageTitle = $pageTitle ?? APP_NAME . ' — ' . APP_SUBTITLE;
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/light-school.css') ?>">
    <script src="<?= base_url('assets/js/main.js') ?>" defer></script>
</head>
<body class="light-body">

<!-- BARRA DE DESTAQUES SUPERIOR (ESTILO COLÉGIO MASTER - LINHA ÚNICA E LIMPA) -->
<div class="top-announcement-bar">
    <div class="container top-bar-container">
        <span>📍 VEM AÍ O CONEXÃO MASTER 2027</span>
        <a href="contato.php" class="top-pill-btn">PODCAST</a>
    </div>
</div>

<header class="navbar light-theme">
    <div class="container nav-container">
        <!-- LOGOMARCA MASTER SCHOOL (GEOMÉTRICA INSPIRADA NO COLÉGIO MASTER) -->
        <a href="<?= base_url('index.php') ?>" class="master-logo">
            <div class="master-logo-text">
                <span class="master-logo-subtitle">COLÉGIO & GRUPO</span>
                <span class="master-logo-title"><span class="text-master">master</span> <span class="text-school">school</span></span>
            </div>
            <div class="master-logo-shapes">
                <div class="shape-square" title="Educação Infantil"></div>
                <div class="shape-arch" title="Ensino Fundamental"></div>
                <div class="shape-circle" title="Ensino Médio"></div>
                <div class="shape-star" title="Excelência & Conquista"></div>
            </div>
        </a>

        <ul class="nav-menu">
            <li><a href="<?= base_url('index.php') ?>" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Home</a></li>
            <li><a href="<?= base_url('quem-somos.php') ?>" class="nav-link <?= $activePage === 'quem-somos' ? 'active' : '' ?>">Quem Somos</a></li>
            <li><a href="<?= base_url('unidades.php') ?>" class="nav-link <?= $activePage === 'unidades' ? 'active' : '' ?>">Unidades</a></li>
            <li><a href="<?= base_url('index.php#niveis') ?>" class="nav-link">Níveis de Ensino</a></li>
            <li><a href="<?= base_url('professores.php') ?>" class="nav-link <?= $activePage === 'professores' ? 'active' : '' ?>">Professores</a></li>
            <li><a href="<?= base_url('contato.php') ?>" class="nav-link <?= $activePage === 'contato' ? 'active' : '' ?>">Contato</a></li>
            
            <?php if (is_logged_in()): ?>
                <?php
                    $role = get_user_role();
                    $dashUrl = 'aluno/index.php';
                    if ($role === 'professor') $dashUrl = 'professor/index.php';
                    if ($role === 'admin') $dashUrl = 'admin/index.php';
                ?>
                <li><a href="<?= base_url($dashUrl) ?>" class="btn nav-login-btn" style="background: var(--master-navy);">Meu Painel (<?= ucfirst($role) ?>)</a></li>
                <li><a href="<?= base_url('logout.php') ?>" class="nav-link" style="color: #dc2626 !important;">Sair</a></li>
            <?php else: ?>
                <li><a href="<?= base_url('login.php') ?>" class="btn nav-login-btn">Portal EDU (Login)</a></li>
            <?php endif; ?>
        </ul>

        <button class="mobile-toggle" aria-label="Abrir Menu">☰</button>
    </div>
</header>
