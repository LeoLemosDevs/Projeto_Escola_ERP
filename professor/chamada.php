<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('professor');

$pageTitle = 'Chamada de Turma — ' . APP_NAME;
$pageHeaderTitle = 'Diário de Classe & Presença';
$activeModule = 'chamada';

$pdo = get_db_connection();
$professorId = $_SESSION['profile_id'] ?? 1;
$userId = $_SESSION['user_id'];

// Busca disciplinas do professor para o seletor
$stmtDisc = $pdo->prepare("SELECT id, nome, codigo, turma_id FROM disciplinas WHERE professor_id = ? ORDER BY nome ASC");
$stmtDisc->execute([$professorId]);
$disciplinas = $stmtDisc->fetchAll();

$selectedDiscId = (int)($_GET['disc'] ?? ($disciplinas[0]['id'] ?? 0));
$selectedDate = sanitize_input($_GET['data'] ?? date('Y-m-d'));

$message = '';
$success = false;

// SALVA OU ATUALIZA CHAMADA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    $discId = (int)($_POST['disciplina_id'] ?? 0);
    $dataAula = sanitize_input($_POST['data_aula'] ?? date('Y-m-d'));
    $presencas = $_POST['presente'] ?? [];
    $observacoes = $_POST['obs'] ?? [];

    if ($discId > 0 && !empty($dataAula)) {
        try {
            $pdo->beginTransaction();
            $stmtUpsert = $pdo->prepare("
                INSERT INTO frequencias (aluno_id, disciplina_id, data_aula, presente, observacao) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE presente = VALUES(presente), observacao = VALUES(observacao)
            ");

            foreach ($presencas as $alunoId => $statusPresenca) {
                $statusInt = (int)$statusPresenca;
                $obsText = sanitize_input($observacoes[$alunoId] ?? '');
                $stmtUpsert->execute([$alunoId, $discId, $dataAula, $statusInt, $obsText]);
            }

            $pdo->commit();
            log_system_action($pdo, $userId, "Lançou/atualizou chamada disciplina ID $discId em $dataAula");
            $success = true;
            $message = "Chamada diária salva e registrada com sucesso para " . format_date($dataAula) . "!";
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = "Erro ao gravar chamada: " . $e->getMessage();
        }
    }
}

// Busca alunos da turma correspondente e se já há presença registrada na data
$alunos = [];
if ($selectedDiscId > 0) {
    $stmtAlunos = $pdo->prepare("
        SELECT 
            a.id as aluno_id,
            a.matricula,
            u.nome as aluno_nome,
            f.presente,
            f.observacao
        FROM disciplinas d
        JOIN alunos a ON d.turma_id = a.turma_id
        JOIN usuarios u ON a.usuario_id = u.id
        LEFT JOIN frequencias f ON f.aluno_id = a.id AND f.disciplina_id = d.id AND f.data_aula = ?
        WHERE d.id = ?
        ORDER BY u.nome ASC
    ");
    $stmtAlunos->execute([$selectedDate, $selectedDiscId]);
    $alunos = $stmtAlunos->fetchAll();
}
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div class="glass-card" style="margin-bottom: 24px;">
    <form method="GET" action="chamada.php" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
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

        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 180px;">
            <label class="form-label">Data da Aula</label>
            <input type="date" name="data" class="form-input" value="<?= htmlspecialchars($selectedDate) ?>" onchange="this.form.submit()">
        </div>

        <button type="submit" class="btn btn-outline" style="padding: 12px 20px;">
            🔍 Filtrar Lista
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
            <h3 style="font-size: 1.35rem;">Lista de Frequência — <?= format_date($selectedDate) ?></h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Marque os alunos presentes ou ausentes na aula.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="marcarTodos(1)" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">
                ✔ Marcar Todos Presentes
            </button>
            <button type="button" onclick="marcarTodos(0)" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;">
                ✖ Todos Falta
            </button>
        </div>
    </div>

    <form method="POST" action="chamada.php?disc=<?= $selectedDiscId ?>&data=<?= $selectedDate ?>">
        <input type="hidden" name="save_attendance" value="1">
        <input type="hidden" name="disciplina_id" value="<?= $selectedDiscId ?>">
        <input type="hidden" name="data_aula" value="<?= htmlspecialchars($selectedDate) ?>">

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nome do Estudante</th>
                        <th>Presença na Aula</th>
                        <th>Observação / Justificativa (Opcional)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alunos)): ?>
                        <tr><td colspan="4" style="text-align: center;">Nenhum aluno matriculado nesta turma ou selecione uma disciplina válida.</td></tr>
                    <?php else: ?>
                        <?php foreach ($alunos as $a): ?>
                            <?php $pVal = $a['presente'] !== null ? (int)$a['presente'] : 1; // padrão presente ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($a['matricula']) ?></strong></td>
                                <td><?= htmlspecialchars($a['aluno_nome']) ?></td>
                                <td>
                                    <div style="display: flex; gap: 16px;">
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 6px; color: #34d399; font-weight: 600;">
                                            <input type="radio" name="presente[<?= $a['aluno_id'] ?>]" value="1" class="radio-presente" <?= $pVal === 1 ? 'checked' : '' ?>>
                                            ✔ Presente
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 6px; color: #f87171; font-weight: 600;">
                                            <input type="radio" name="presente[<?= $a['aluno_id'] ?>]" value="0" class="radio-presente" <?= $pVal === 0 ? 'checked' : '' ?>>
                                            ✖ Falta
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="obs[<?= $a['aluno_id'] ?>]" class="form-input" style="padding: 6px 12px; font-size: 0.85rem;" placeholder="Ex: Justificada / Atestado" value="<?= htmlspecialchars($a['observacao'] ?? '') ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                💾 Gravar Diário de Frequência
            </button>
        </div>
    </form>
</div>

<script>
function marcarTodos(status) {
    const radios = document.querySelectorAll('.radio-presente');
    radios.forEach(radio => {
        if (parseInt(radio.value) === status) {
            radio.checked = true;
        }
    });
    showToast(status === 1 ? 'Todos marcados como Presente!' : 'Todos marcados como Falta!', 'info');
}
</script>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
