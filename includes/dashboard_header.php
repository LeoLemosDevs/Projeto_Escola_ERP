<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

// Garante que o usuário está autenticado
if (!is_logged_in()) {
    redirect(base_url('login.php'));
}

$currentRole = get_user_role();
$pageTitle = $pageTitle ?? APP_NAME . ' — ERP Dashboard (' . ucfirst($currentRole) . ')';
$activeModule = $activeModule ?? 'home';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/light-school.css') ?>">
    <!-- Chart.js para gráficos de desempenho -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>" defer></script>
</head>
<body class="light-body dashboard-light-theme">

<div class="dashboard-wrapper">
    <!-- BARRA LATERAL (SIDEBAR) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header" style="height: 86px; padding: 0 16px;">
            <a href="<?= base_url('index.php') ?>" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                <div class="master-logo" style="transform: scale(0.65); transform-origin: left center; margin-right: -42px; cursor: pointer;">
                    <div class="master-logo-text">
                        <span class="master-logo-subtitle" style="color: #64748b;">COLÉGIO & GRUPO</span>
                        <span class="master-logo-title" style="font-size: 1.85rem;"><span class="text-master">master</span> <span class="text-school">school</span></span>
                    </div>
                    <div class="master-logo-shapes">
                        <div class="shape-square"></div>
                        <div class="shape-arch"></div>
                        <div class="shape-circle"></div>
                        <div class="shape-star"></div>
                    </div>
                </div>
                <div style="padding-top: 4px;">
                    <span class="badge" style="background: rgba(249, 115, 22, 0.15); color: #ea580c; font-size: 0.68rem; font-weight: 800; border: 1px solid rgba(249, 115, 22, 0.3); padding: 4px 8px; border-radius: 6px;">ERP <?= strtoupper($currentRole) ?></span>
                </div>
            </a>
        </div>

        <ul class="sidebar-nav">
            <?php if ($currentRole === 'aluno'): ?>
                <li><a href="<?= base_url('aluno/index.php') ?>" class="sidebar-link <?= $activeModule === 'home' ? 'active' : '' ?>">📊 Meu Painel</a></li>
                <li><a href="<?= base_url('aluno/boletim.php') ?>" class="sidebar-link <?= $activeModule === 'boletim' ? 'active' : '' ?>">📑 Boletim & Frequência</a></li>
                <li><a href="<?= base_url('aluno/financeiro.php') ?>" class="sidebar-link <?= $activeModule === 'financeiro' ? 'active' : '' ?>">💳 Mensalidades (PagSeguro)</a></li>
                <li><a href="<?= base_url('aluno/perfil.php') ?>" class="sidebar-link <?= $activeModule === 'perfil' ? 'active' : '' ?>">👤 Meu Perfil</a></li>

            <?php elseif ($currentRole === 'professor'): ?>
                <li><a href="<?= base_url('professor/index.php') ?>" class="sidebar-link <?= $activeModule === 'home' ? 'active' : '' ?>">📊 Minhas Turmas</a></li>
                <li><a href="<?= base_url('professor/chamada.php') ?>" class="sidebar-link <?= $activeModule === 'chamada' ? 'active' : '' ?>">📋 Chamada de Turma</a></li>
                <li><a href="<?= base_url('professor/notas.php') ?>" class="sidebar-link <?= $activeModule === 'notas' ? 'active' : '' ?>">📝 Lançamento de Notas</a></li>
                <li><a href="<?= base_url('professor/horarios.php') ?>" class="sidebar-link <?= $activeModule === 'horarios' ? 'active' : '' ?>">🕒 Grade e Horários</a></li>

            <?php elseif ($currentRole === 'admin'): ?>
                <li><a href="<?= base_url('admin/index.php') ?>" class="sidebar-link <?= $activeModule === 'home' ? 'active' : '' ?>">📈 Executive Analytics</a></li>
                <li><a href="<?= base_url('admin/professores.php') ?>" class="sidebar-link <?= $activeModule === 'professores' ? 'active' : '' ?>">👨‍🏫 Corpo Docente</a></li>
                <li><a href="<?= base_url('admin/alunos.php') ?>" class="sidebar-link <?= $activeModule === 'alunos' ? 'active' : '' ?>">🎓 Alunos & Matrículas</a></li>
                <li><a href="<?= base_url('admin/turmas.php') ?>" class="sidebar-link <?= $activeModule === 'turmas' ? 'active' : '' ?>">🏫 Turmas & Disciplinas</a></li>
                <li><a href="<?= base_url('admin/financeiro.php') ?>" class="sidebar-link <?= $activeModule === 'financeiro' ? 'active' : '' ?>">💰 Painel Financeiro</a></li>
                <li><a href="<?= base_url('admin/noticias.php') ?>" class="sidebar-link <?= $activeModule === 'noticias' ? 'active' : '' ?>">📰 Mural & Eventos</a></li>
                <li><a href="<?= base_url('admin/logs.php') ?>" class="sidebar-link <?= $activeModule === 'logs' ? 'active' : '' ?>">🛡️ Logs de Segurança</a></li>
                <li><a href="<?= base_url('admin/perfil.php') ?>" class="sidebar-link <?= $activeModule === 'perfil' ? 'active' : '' ?>">⚙️ Meus Dados</a></li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <a href="<?= base_url('index.php') ?>" style="display: block; color: #94a3b8; margin-bottom: 8px;">&larr; Voltar ao Portal Escola</a>
            <a href="<?= base_url('logout.php') ?>" style="display: block; color: #ef4444; font-weight: 600;">Encerrar Sessão ✕</a>
        </div>
    </aside>

    <!-- CONTAINER PRINCIPAL -->
    <div class="main-container">
        <!-- BARRA SUPERIOR (TOPBAR) -->
        <header class="topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button type="button" class="btn btn-outline" style="padding: 6px 12px; display: none;" id="mobileSidebarToggle" onclick="document.getElementById('sidebar').classList.toggle('active')">
                    ☰
                </button>
                <h2 style="font-size: 1.25rem; font-weight: 700;"><?= htmlspecialchars($pageHeaderTitle ?? 'Visão Geral do Módulo') ?></h2>
            </div>

            <div class="topbar-user">
                <span class="badge badge-primary"><?= strtoupper($currentRole) ?></span>
                <div class="user-avatar">
                    <?= mb_substr(htmlspecialchars($_SESSION['user_name'] ?? 'U'), 0, 1) ?>
                </div>
                <div>
                    <strong style="display: block; font-size: 0.92rem;"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?></strong>
                    <span style="font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
                </div>
            </div>
        </header>

        <main class="main-content">
