<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Painel Financeiro Geral — ' . APP_NAME;
$pageHeaderTitle = 'Receitas & Inadimplência';
$activeModule = 'financeiro';

$pdo = get_db_connection();

// Resumo financeiro consolidado
$stmtResumo = $pdo->query("
    SELECT 
        COUNT(*) as total_titulos,
        COALESCE(SUM(valor), 0) as total_geral,
        COALESCE(SUM(CASE WHEN status = 'pago' THEN valor END), 0) as pago,
        COALESCE(SUM(CASE WHEN status = 'pendente' THEN valor END), 0) as pendente,
        COALESCE(SUM(CASE WHEN status = 'atrasado' THEN valor END), 0) as atrasado
    FROM mensalidades
");
$resumo = $stmtResumo->fetch();

// Lista todas as mensalidades
$stmtList = $pdo->query("
    SELECT m.*, u.nome as aluno_nome, a.matricula 
    FROM mensalidades m
    JOIN alunos a ON m.aluno_id = a.id
    JOIN usuarios u ON a.usuario_id = u.id
    ORDER BY m.vencimento ASC
");
$mensalidades = $stmtList->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div>
            <h4>Arrecadação Liquidada</h4>
            <div class="value" style="color: #34d399; font-size: 1.6rem;"><?= format_currency($resumo['pago']) ?></div>
            <span style="font-size: 0.8rem; color: #34d399;">✔ Mensalidades pagas via PagSeguro</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">💸</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>A Receber (Pendente)</h4>
            <div class="value" style="color: #fbbf24; font-size: 1.6rem;"><?= format_currency($resumo['pendente']) ?></div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Títulos em dia a vencer</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">⌛</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Inadimplência (Atrasado)</h4>
            <div class="value" style="color: #ef4444; font-size: 1.6rem;"><?= format_currency($resumo['atrasado']) ?></div>
            <span style="font-size: 0.8rem; color: #f87171;">Títulos vencidos sem baixa</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">⚠️</div>
    </div>
</div>

<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 1.35rem;">Listagem Geral de Mensalidades (<?= count($mensalidades) ?>)</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Monitoramento de transações, métodos (PIX, Boleto, Cartão) e códigos PagSeguro.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
            🖨️ Exportar Relatório Financeiro
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Estudante</th>
                    <th>Referência</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Método</th>
                    <th>Cód. PagSeguro</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mensalidades as $m): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($m['matricula']) ?></strong></td>
                        <td><?= htmlspecialchars($m['aluno_nome']) ?></td>
                        <td><?= htmlspecialchars($m['mes_referencia']) ?></td>
                        <td><?= format_date($m['vencimento']) ?></td>
                        <td><strong style="color: #60a5fa;"><?= format_currency($m['valor']) ?></strong></td>
                        <td><?= !empty($m['metodo_pagamento']) ? strtoupper($m['metodo_pagamento']) : '-' ?></td>
                        <td><code style="font-size: 0.75rem;"><?= htmlspecialchars($m['codigo_pagseguro'] ?? '-') ?></code></td>
                        <td>
                            <?php if ($m['status'] === 'pago'): ?>
                                <span class="badge badge-success">✔ PAGO</span>
                            <?php elseif ($m['status'] === 'atrasado'): ?>
                                <span class="badge badge-danger">✖ ATRASADO</span>
                            <?php else: ?>
                                <span class="badge badge-accent">⌛ PENDENTE</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
