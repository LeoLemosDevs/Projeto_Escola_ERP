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
    <link rel="stylesheet" href="assets/css/design-system.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body>

<header class="navbar">
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <div class="logo-badge">MS</div>
            <span>Master School</span>
        </a>

        <ul class="nav-menu">
            <li><a href="quem-somos.php" class="nav-link <?= $activePage === 'quem-somos' ? 'active' : '' ?>">Quem Somos</a></li>
            <li><a href="missao-visao-valores.php" class="nav-link <?= $activePage === 'mvv' ? 'active' : '' ?>">Missão, Visão & Valores</a></li>
            <li><a href="unidades.php" class="nav-link <?= $activePage === 'unidades' ? 'active' : '' ?>">Unidades</a></li>
            <li><a href="professores.php" class="nav-link <?= $activePage === 'professores' ? 'active' : '' ?>">Professores</a></li>
            <li><a href="trabalhe-conosco.php" class="nav-link <?= $activePage === 'trabalhe-conosco' ? 'active' : '' ?>">Trabalhe Conosco</a></li>
            <li><a href="contato.php" class="nav-link <?= $activePage === 'contato' ? 'active' : '' ?>">Contato</a></li>
            
            <?php if (is_logged_in()): ?>
                <?php
                    $role = get_user_role();
                    $dashUrl = 'aluno/index.php';
                    if ($role === 'professor') $dashUrl = 'professor/index.php';
                    if ($role === 'admin') $dashUrl = 'admin/index.php';
                ?>
                <li><a href="<?= $dashUrl ?>" class="btn nav-login-btn">Meu Painel (<?= ucfirst($role) ?>)</a></li>
                <li><a href="logout.php" class="nav-link" style="color: var(--danger);">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn nav-login-btn">Login de Entrada</a></li>
            <?php endif; ?>
        </ul>

        <button class="mobile-toggle" aria-label="Abrir Menu">☰</button>
    </div>
</header>
