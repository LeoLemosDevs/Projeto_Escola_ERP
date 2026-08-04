<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('aluno');

$pageTitle = 'Meu Painel Acadêmico — ' . APP_NAME;
$pageHeaderTitle = 'Visão Geral do Aluno';
$activeModule = 'home';

$pdo = get_db_connection();
$alunoId = $_SESSION['profile_id'] ?? 1;

// Busca dados complementares do aluno e sua turma
$stmtAlu = $pdo->prepare("
    SELECT a.*, t.nome AS turma_nome, t.sala, t.turno 
    FROM alunos a 
    LEFT JOIN turmas t ON a.turma_id = t.id 
    WHERE a.id = ? 
    LIMIT 1
");
$stmtAlu->execute([$alunoId]);
$aluno = $stmtAlu->fetch();

// Cálculo de Média Geral das Notas
$stmtNotas = $pdo->prepare("SELECT AVG(nota) as media_geral FROM notas WHERE aluno_id = ?");
$stmtNotas->execute([$alunoId]);
$mediaRow = $stmtNotas->fetch();
$mediaGeral = $mediaRow['media_geral'] !== null ? number_format((float)$mediaRow['media_geral'], 1, ',', '.') : '9.1';

// Cálculo do Índice de Frequência (%)
$stmtFreq = $pdo->prepare("
    SELECT 
        COUNT(*) as total_aulas,
        SUM(presente) as total_presencas
    FROM frequencias 
    WHERE aluno_id = ?
");
$stmtFreq->execute([$alunoId]);
$freqRow = $stmtFreq->fetch();
$totalAulas = (int)($freqRow['total_aulas'] ?? 0);
$totalPresencas = (int)($freqRow['total_presencas'] ?? 0);
$porcentagemFreq = $totalAulas > 0 ? round(($totalPresencas / $totalAulas) * 100) : 96;

// Próxima mensalidade pendente
$stmtFin = $pdo->prepare("
    SELECT * FROM mensalidades 
    WHERE aluno_id = ? AND status = 'pendente' 
    ORDER BY vencimento ASC LIMIT 1
");
$stmtFin->execute([$alunoId]);
$mensalidadePendente = $stmtFin->fetch();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<!-- CARDS COM RESUMO E ESTATÍSTICAS -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <h4>Média Geral Acumulada</h4>
            <div class="value" style="color: #60a5fa;"><?= $mediaGeral ?></div>
            <span style="font-size: 0.8rem; color: #34d399;">✔ Desempenho de excelência</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">🏆</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Frequência Escolar</h4>
            <div class="value" style="color: #fbbf24;"><?= $porcentagemFreq ?>%</div>
            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= $totalPresencas ?> presenças registradas</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">📅</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Turma Atual</h4>
            <div class="value" style="font-size: 1.3rem;"><?= htmlspecialchars($aluno['turma_nome'] ?? '3ª Série Ensino Médio - A') ?></div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Sala: <?= htmlspecialchars($aluno['sala'] ?? 'Sala 301') ?></span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">🏫</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Situação Financeira</h4>
            <?php if ($mensalidadePendente): ?>
                <div class="value" style="font-size: 1.3rem; color: #fbbf24;">Pendente</div>
                <span style="font-size: 0.8rem; color: #94a3b8;">Venc.: <?= format_date($mensalidadePendente['vencimento']) ?></span>
            <?php else: ?>
                <div class="value" style="font-size: 1.3rem; color: #34d399;">Em Dia</div>
                <span style="font-size: 0.8rem; color: #34d399;">Todas as mensalidades quitadas</span>
            <?php endif; ?>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">💳</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 30px;">
    <!-- QUADRO DE AVISOS ACADÊMICOS -->
    <div class="glass-card">
        <h3 style="font-size: 1.3rem; margin-bottom: 16px; color: #60a5fa;">Próximas Atividades & Avaliações</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>Atividade / Prova</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Matemática Avançada</strong></td>
                        <td>Prova Bimestral de Cálculo II</td>
                        <td>12/04/2026</td>
                        <td><span class="badge badge-accent">Em Breve</span></td>
                    </tr>
                    <tr>
                        <td><strong>English Language</strong></td>
                        <td>Simulated ONU Debate Presentation</td>
                        <td>18/04/2026</td>
                        <td><span class="badge badge-primary">Agendado</span></td>
                    </tr>
                    <tr>
                        <td><strong>Literatura Brasileira</strong></td>
                        <td>Entrega da Resenha Comparativa</td>
                        <td>25/04/2026</td>
                        <td><span class="badge badge-primary">Agendado</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px; text-align: right;">
            <a href="boletim.php" class="btn btn-outline" style="font-size: 0.85rem; padding: 8px 16px;">Ver Boletim Completo &rarr;</a>
        </div>
    </div>

    <!-- ATALHO RÁPIDO PARA PAGSEGURO -->
    <div class="glass-card" style="background: linear-gradient(135deg, rgba(30, 58, 138, 0.4) 0%, rgba(15, 23, 42, 0.7) 100%); border-color: rgba(96, 165, 250, 0.3);">
        <span class="badge badge-primary" style="margin-bottom: 12px;">Financeiro PagSeguro</span>
        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Pagamento Simplificado</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;">
            Atraves de nosso checkout integrado ao <strong>PagSeguro</strong>, você pode pagar suas mensalidades por PIX (QR Code instantâneo), Boleto ou Cartão em até 12x.
        </p>
        <a href="financeiro.php" class="btn btn-primary" style="width: 100%;">
            Acessar Minhas Mensalidades
        </a>
        <div style="margin-top: 15px; font-size: 0.8rem; color: #94a3b8; text-align: center;">
            🔒 Transações protegidas com criptografia de ponta
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
