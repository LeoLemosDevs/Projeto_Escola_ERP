<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('professor');

$pageTitle = 'Lançamento de Notas — ' . APP_NAME;
$pageHeaderTitle = 'Diário de Notas & Avaliações';
$activeModule = 'notas';

$pdo = get_db_connection();
$professorId = $_SESSION['profile_id'] ?? 1;
$userId = $_SESSION['user_id'];

// Disciplinas do professor
$stmtDisc = $pdo->prepare("SELECT id, nome, codigo, turma_id FROM disciplinas WHERE professor_id = ? ORDER BY nome ASC");
$stmtDisc->execute([$professorId]);
$disciplinas = $stmtDisc->fetchAll();

$selectedDiscId = (int)($_GET['disc'] ?? ($disciplinas[0]['id'] ?? 0));
$selectedBim = (int)($_GET['bim'] ?? 1);
if ($selectedBim < 1 || $selectedBim > 4) $selectedBim = 1;

$message = '';
$success = false;

// SALVA OU ATUALIZA NOTAS DO BIMESTRE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grades'])) {
    $discId = (int)($_POST['disciplina_id'] ?? 0);
    $bimestre = (int)($_POST['bimestre'] ?? 1);
    $notas = $_POST['nota'] ?? [];
    $observacoes = $_POST['obs'] ?? [];

    if ($discId > 0 && $bimestre >= 1 && $bimestre <= 4) {
        try {
            $pdo->beginTransaction();
            $stmtUpsert = $pdo->prepare("
                INSERT INTO notas (aluno_id, disciplina_id, bimestre, nota, observacao_professor) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nota = VALUES(nota), observacao_professor = VALUES(observacao_professor)
            ");

            foreach ($notas as $alunoId => $notaVal) {
                // Trata vírgula como ponto
                $notaLimpa = str_replace(',', '.', trim($notaVal));
                $notaNum = is_numeric($notaLimpa) ? min(10.0, max(0.0, (float)$notaLimpa)) : null;
                $obsText = sanitize_input($observacoes[$alunoId] ?? '');

                if ($notaNum !== null) {
                    $stmtUpsert->execute([$alunoId, $discId, $bimestre, $notaNum, $obsText]);
                }
            }

            $pdo->commit();
            log_system_action($pdo, $userId, "Lançou/atualizou notas {$bimestre}º Bimestre disciplina ID $discId");
            $success = true;
            $message = "Notas do {$bimestre}º Bimestre salvas e consolidadas no boletim dos estudantes!";
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = "Erro ao gravar notas: " . $e->getMessage();
        }
    }
}

// Busca alunos da turma + notas existentes para o bimestre selecionado
$alunos = [];
if ($selectedDiscId > 0) {
    $stmtAlunos = $pdo->prepare("
        SELECT 
            a.id as aluno_id,
            a.matricula,
            u.nome as aluno_nome,
            n.nota,
            n.observacao_professor
        FROM disciplinas d
        JOIN alunos a ON d.turma_id = a.turma_id
        JOIN usuarios u ON a.usuario_id = u.id
        LEFT JOIN notas n ON n.aluno_id = a.id AND n.disciplina_id = d.id AND n.bimestre = ?
        WHERE d.id = ?
        ORDER BY u.nome ASC
    ");
    $stmtAlunos->execute([$selectedBim, $selectedDiscId]);
    $alunos = $stmtAlunos->fetchAll();
}
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div class="glass-card" style="margin-bottom: 24px;">
    <form method="GET" action="notas.php" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 250px;">
            <label class="form-label">Disciplina / Turma</label>
            <select name="disc" class="form-select" onchange="this.form.submit()">
                <?php foreach ($disciplinas as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $d['id'] == $selectedDiscId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['nome']) ?> (<?= htmlspecialchars($d['codigo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
            <label class="form-label">Período Avaliativo</label>
            <select name="bim" class="form-select" onchange="this.form.submit()">
                <option value="1" <?= $selectedBim == 1 ? 'selected' : '' ?>>1º Bimestre</option>
                <option value="2" <?= $selectedBim == 2 ? 'selected' : '' ?>>2º Bimestre</option>
                <option value="3" <?= $selectedBim == 3 ? 'selected' : '' ?>>3º Bimestre</option>
                <option value="4" <?= $selectedBim == 4 ? 'selected' : '' ?>>4º Bimestre</option>
            </select>
        </div>

        <button type="submit" class="btn btn-outline" style="padding: 12px 20px;">
            🔍 Filtrar Alunos
        </button>
    </form>
</div>

<?php if ($message): ?>
    <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 1.35rem;">Lançamento de Notas — <?= $selectedBim ?>º Bimestre</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Insira notas de <strong>0,0 a 10,0</strong>. O feedback qualitativo é exibido no boletim do estudante.
            </p>
        </div>
    </div>

    <form method="POST" action="notas.php?disc=<?= $selectedDiscId ?>&bim=<?= $selectedBim ?>">
        <input type="hidden" name="save_grades" value="1">
        <input type="hidden" name="disciplina_id" value="<?= $selectedDiscId ?>">
        <input type="hidden" name="bimestre" value="<?= $selectedBim ?>">

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Matrícula</th>
                        <th>Nome do Estudante</th>
                        <th style="width: 160px;">Nota (0 a 10)</th>
                        <th>Feedback / Observação Qualitativa (Opcional)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alunos)): ?>
                        <tr><td colspan="4" style="text-align: center;">Nenhum aluno cadastrado para esta disciplina.</td></tr>
                    <?php else: ?>
                        <?php foreach ($alunos as $a): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($a['matricula']) ?></strong></td>
                                <td><?= htmlspecialchars($a['aluno_nome']) ?></td>
                                <td>
                                    <input type="number" step="0.1" min="0" max="10" name="nota[<?= $a['aluno_id'] ?>]" class="form-input" style="padding: 8px 12px; font-weight: 700; color: #60a5fa; text-align: center;" placeholder="Ex: 8.5" value="<?= $a['nota'] !== null ? htmlspecialchars($a['nota']) : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="obs[<?= $a['aluno_id'] ?>]" class="form-input" style="padding: 8px 12px; font-size: 0.88rem;" placeholder="Ex: Excelente desempenho na prova e trabalhos." value="<?= htmlspecialchars($a['observacao_professor'] ?? '') ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                💾 Consolidar & Gravar Notas do Bimestre
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
