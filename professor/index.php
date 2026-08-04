<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('professor');

$pageTitle = 'Painel Docente — ' . APP_NAME;
$pageHeaderTitle = 'Visão Geral do Professor';
$activeModule = 'home';

$pdo = get_db_connection();
$professorId = $_SESSION['profile_id'] ?? 1;

// Busca disciplinas atribuídas ao professor
$stmtDisc = $pdo->prepare("
    SELECT d.*, t.nome as turma_nome, t.turno, t.sala 
    FROM disciplinas d
    LEFT JOIN turmas t ON d.turma_id = t.id
    WHERE d.professor_id = ?
");
$stmtDisc->execute([$professorId]);
$minhasDisciplinas = $stmtDisc->fetchAll();

// Total de alunos ensinados pelo professor (através das turmas)
$stmtAlunos = $pdo->prepare("
    SELECT COUNT(DISTINCT a.id) as total_alunos
    FROM alunos a
    JOIN disciplinas d ON a.turma_id = d.turma_id
    WHERE d.professor_id = ?
");
$stmtAlunos->execute([$professorId]);
$totalAlunos = (int)($stmtAlunos->fetch()['total_alunos'] ?? 0);
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<!-- RESUMO RÁPIDO -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <h4>Minhas Disciplinas</h4>
            <div class="value" style="color: #60a5fa;"><?= count($minhasDisciplinas) ?></div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Atribuídas em 2026</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">📚</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Total de Alunos</h4>
            <div class="value" style="color: #fbbf24;"><?= $totalAlunos ?></div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Estudantes nas turmas</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">🎓</div>
    </div>

    <div class="stat-card">
        <div>
            <h4>Período Letivo</h4>
            <div class="value" style="font-size: 1.4rem; color: #34d399;">1º Bimestre</div>
            <span style="font-size: 0.8rem; color: #94a3b8;">Aberto para Notas</span>
        </div>
        <div style="font-size: 2.2rem; opacity: 0.8;">🗓️</div>
    </div>
</div>

<div class="glass-card" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 1.4rem; color: #60a5fa;">Minhas Turmas & Disciplinas</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Selecione uma ação rápida para gerenciar a frequência diária ou avaliações.
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Disciplina</th>
                    <th>Turma</th>
                    <th>Turno / Sala</th>
                    <th style="text-align: right;">Ações Acadêmicas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($minhasDisciplinas)): ?>
                    <tr><td colspan="5" style="text-align: center;">Nenhuma disciplina vinculada ao seu perfil.</td></tr>
                <?php else: ?>
                    <?php foreach ($minhasDisciplinas as $d): ?>
                        <tr>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($d['codigo']) ?></span></td>
                            <td><strong><?= htmlspecialchars($d['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($d['turma_nome'] ?? 'Turma Geral') ?></td>
                            <td><?= htmlspecialchars($d['turno'] ?? '-') ?> — <?= htmlspecialchars($d['sala'] ?? '-') ?></td>
                            <td style="text-align: right;">
                                <a href="chamada.php?disc=<?= $d['id'] ?>" class="btn btn-primary" style="padding: 6px 14px; font-size: 0.82rem; margin-right: 6px;">
                                    📋 Chamada
                                </a>
                                <a href="notas.php?disc=<?= $d['id'] ?>" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.82rem;">
                                    📝 Lançar Notas
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
