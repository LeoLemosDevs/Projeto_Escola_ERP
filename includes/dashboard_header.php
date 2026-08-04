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
    <!-- Chart.js para gráficos de desempenho -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>" defer></script>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- BARRA LATERAL (SIDEBAR) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= base_url('index.php') ?>" style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: white;">
                    MS
                </div>
                <div>
                    <strong style="font-family: 'Outfit'; font-size: 1.1rem; color: #f8fafc; display: block;">Master School</strong>
                    <span style="font-size: 0.72rem; color: #60a5fa; text-transform: uppercase;">ERP <?= ucfirst($currentRole) ?></span>
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
