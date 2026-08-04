<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Executive Analytics — ' . APP_NAME;
$pageHeaderTitle = 'Centro de Comando e Indicadores ERP';
$activeModule = 'home';

$pdo = get_db_connection();

// Estatísticas Gerais do ERP
$stmtStats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM alunos) as total_alunos,
        (SELECT COUNT(*) FROM professores) as total_professores,
        (SELECT COUNT(*) FROM turmas) as total_turmas,
        (SELECT COALESCE(SUM(valor), 0) FROM mensalidades WHERE status = 'pago') as receita_paga,
        (SELECT COALESCE(SUM(valor), 0) FROM mensalidades WHERE status = 'pendente') as receita_pendente
");
$stats = $stmtStats->fetch();

// Dados para Gráficos Chart.js (Status Financeiro)
$stmtChartFin = $pdo->query("
    SELECT status, COUNT(*) as total_titulos, COALESCE(SUM(valor),0) as soma_valor 
    FROM mensalidades GROUP BY status
");
$chartFin = $stmtChartFin->fetchAll();
$finLabels = [];
$finValues = [];
foreach ($chartFin as $cf) {
    $finLabels[] = ucfirst($cf['status']);
    $finValues[] = (float)$cf['soma_valor'];
}

// Dados para Gráficos Chart.js (Desempenho por Disciplina)
$stmtChartDisc = $pdo->query("
    SELECT d.nome, AVG(n.nota) as media_nota
    FROM disciplinas d
    LEFT JOIN notas n ON n.disciplina_id = d.id
    GROUP BY d.id, d.nome
    ORDER BY d.nome ASC
");
$chartDisc = $stmtChartDisc->fetchAll();
$discLabels = [];
$discValues = [];
foreach ($chartDisc as $cd) {
    $discLabels[] = $cd['nome'];
    $discValues[] = $cd['media_nota'] !== null ? round((float)$cd['media_nota'], 2) : 8.5;
}

// Últimas auditorias (System Logs)
$stmtLogs = $pdo->query("
    SELECT l.*, u.nome as usuario_nome, u.role
    FROM system_logs l
    LEFT JOIN usuarios u ON l.usuario_id = u.id
    ORDER BY l.criado_em DESC
    LIMIT 6
");
$ultimosLogs = $stmtLogs->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<!-- CARDS DE MÉTRICAS EXECUTIVAS -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <h4>Alunos Matriculados</h4>
            <div class="value" style="color: #60a5fa;"><?= (int)$stats['total_alunos'] ?></div>
            <span style="font-size: 0.8rem; color: #34d399;">✔ Matrículas ativas</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">🎓</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Corpo Docente</h4>
            <div class="value" style="color: #fbbf24;"><?= (int)$stats['total_professores'] ?></div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Professores contratados</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">👨‍🏫</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Turmas Abertas</h4>
            <div class="value" style="color: #a855f7;"><?= (int)$stats['total_turmas'] ?></div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Ensino Médio — A / B</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">🏫</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Receita Arrecadada</h4>
            <div class="value" style="color: #34d399; font-size: 1.5rem;"><?= format_currency($stats['receita_paga']) ?></div>
            <span style="font-size: 0.8rem; color: #fbbf24;">+<?= format_currency($stats['receita_pendente']) ?> a receber</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">💰</div>
    </div>
</div>

<!-- SEÇÃO DE GRÁFICOS ANALÍTICOS COM CHART.JS -->
<div style="display: grid; grid-template-columns: 3fr 2fr; gap: 30px; margin-bottom: 30px;">
    <!-- GRÁFICO DE BARRAS: DESEMPENHO ACADÊMICO POR DISCIPLINA -->
    <div class="glass-card">
        <h3 style="font-size: 1.3rem; margin-bottom: 6px; color: #60a5fa;">Desempenho Geral por Disciplina</h3>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px;">
            Média cumulativa das notas bimestrais lançadas pelos docentes no ERP.
        </p>
        <div style="height: 280px; position: relative;">
            <canvas id="discChart"></canvas>
        </div>
    </div>

    <!-- GRÁFICO DE ROSCA: STATUS FINANCEIRO -->
    <div class="glass-card">
        <h3 style="font-size: 1.3rem; margin-bottom: 6px; color: #fbbf24;">Distribuição da Arrecadação</h3>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 20px;">
            Montante financeiro de mensalidades (Pago vs. Pendente vs. Atrasado).
        </p>
        <div style="height: 280px; position: relative; display: flex; justify-content: center;">
            <canvas id="finChart"></canvas>
        </div>
    </div>
</div>

<!-- AUDITORIA DO SISTEMA (SYSTEM LOGS) -->
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 1.3rem;">Últimas Atividades no Sistema (Trilha de Auditoria)</h3>
        <a href="logs.php" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">Ver Auditoria Completa &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data / Hora</th>
                    <th>Usuário</th>
                    <th>Função (Role)</th>
                    <th>Ação Executada</th>
                    <th>IP de Acesso</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ultimosLogs)): ?>
                    <tr><td colspan="5" style="text-align: center;">Nenhum registro de auditoria disponível.</td></tr>
                <?php else: ?>
                    <?php foreach ($ultimosLogs as $log): ?>
                        <tr>
                            <td><?= format_date($log['criado_em'], 'd/m/Y H:i') ?></td>
                            <td><strong><?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?></strong></td>
                            <td><span class="badge badge-primary"><?= strtoupper($log['role'] ?? 'SYS') ?></span></td>
                            <td><?= htmlspecialchars($log['acao']) ?></td>
                            <td><code style="background: rgba(0,0,0,0.3); padding: 3px 6px; border-radius: 4px; font-size: 0.8rem;"><?= htmlspecialchars($log['ip_address']) ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Barras: Desempenho por Disciplina
    const ctxDisc = document.getElementById('discChart').getContext('2d');
    new Chart(ctxDisc, {
        type: 'bar',
        data: {
            labels: <?= json_encode($discLabels) ?>,
            datasets: [{
                label: 'Média de Notas (0 a 10)',
                data: <?= json_encode($discValues) ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                borderColor: '#3b82f6',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10,
                    grid: { color: 'rgba(255, 255, 255, 0.06)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.06)' },
                    ticks: { color: '#94a3b8' }
                }
            },
            plugins: {
                legend: { labels: { color: '#f8fafc' } }
            }
        }
    });

    // 2. Gráfico de Rosca: Status Financeiro
    const ctxFin = document.getElementById('finChart').getContext('2d');
    new Chart(ctxFin, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($finLabels) ?>,
            datasets: [{
                data: <?= json_encode($finValues) ?>,
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)', // Pago
                    'rgba(251, 191, 36, 0.8)', // Pendente
                    'rgba(239, 68, 68, 0.8)'   // Atrasado
                ],
                borderColor: '#1e293b',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#f8fafc' } }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
