<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Gestão de Turmas & Disciplinas — ' . APP_NAME;
$pageHeaderTitle = 'Turmas, Grades & Disciplinas';
$activeModule = 'turmas';

$pdo = get_db_connection();
$message = '';
$success = false;

// 1. ADICIONAR TURMA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_turma'])) {
    $nome = sanitize_input($_POST['nome'] ?? '');
    $ano = (int)($_POST['ano'] ?? 2026);
    $turno = sanitize_input($_POST['turno'] ?? 'Matutino');
    $sala = sanitize_input($_POST['sala'] ?? 'Sala 101');

    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO turmas (nome, ano_letivo, turno, sala) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $ano, $turno, $sala]);
            log_system_action($pdo, $_SESSION['user_id'], "Criou turma: $nome ($sala)");
            $success = true;
            $message = "Turma criada com sucesso!";
        } catch (Exception $e) {
            $message = "Erro ao criar turma: " . $e->getMessage();
        }
    }
}

// 2. ADICIONAR DISCIPLINA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_disciplina'])) {
    $codigo = sanitize_input($_POST['codigo'] ?? '');
    $nomeDisc = sanitize_input($_POST['nome_disciplina'] ?? '');
    $turmaId = (int)($_POST['turma_id'] ?? 1);
    $profId = (int)($_POST['prof_id'] ?? 1);

    if (!empty($codigo) && !empty($nomeDisc)) {
        try {
            $stmtD = $pdo->prepare("INSERT INTO disciplinas (codigo, nome, turma_id, professor_id) VALUES (?, ?, ?, ?)");
            $stmtD->execute([$codigo, $nomeDisc, $turmaId, $profId]);
            log_system_action($pdo, $_SESSION['user_id'], "Criou disciplina: $codigo - $nomeDisc");
            $success = true;
            $message = "Disciplina adicionada e vinculada à turma e professor!";
        } catch (Exception $e) {
            $message = "Erro ao criar disciplina: Código possivelmente já existe.";
        }
    }
}

// Busca turmas e disciplinas
$turmas = $pdo->query("SELECT * FROM turmas ORDER BY ano_letivo DESC, nome ASC")->fetchAll();

$stmtDisc = $pdo->query("
    SELECT d.*, t.nome as turma_nome, u.nome as prof_nome
    FROM disciplinas d
    LEFT JOIN turmas t ON d.turma_id = t.id
    LEFT JOIN professores p ON d.professor_id = p.id
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY d.nome ASC
");
$disciplinas = $stmtDisc->fetchAll();

$professores = $pdo->query("
    SELECT p.id, u.nome 
    FROM professores p 
    JOIN usuarios u ON p.usuario_id = u.id 
    ORDER BY u.nome ASC
")->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<?php if ($message): ?>
    <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 35px;">
    <!-- FORMULÁRIO NOVA TURMA -->
    <div class="glass-card" style="border-top: 3px solid #3b82f6; padding: 32px 28px;">
        <h3 style="font-size: 1.35rem; margin-bottom: 6px; color: #60a5fa; display: flex; align-items: center; gap: 10px;">
            🏫 Cadastrar Nova Turma
        </h3>
        <p style="color: #94a3b8; font-size: 0.88rem; margin-bottom: 24px;">
            Defina uma nova turma especificando a identificação, ano letivo, turno e sala de aula física.
        </p>
        <form method="POST">
            <input type="hidden" name="create_turma" value="1">
            <div class="form-group">
                <label class="form-label">🏷️ Nome da Turma</label>
                <input type="text" name="nome" class="form-input" placeholder="Ex: 1ª Série Ensino Médio — Turma A" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">📅 Ano Letivo</label>
                    <input type="number" name="ano" class="form-input" value="2026" required>
                </div>
                <div class="form-group">
                    <label class="form-label">🕒 Turno</label>
                    <select name="turno" class="form-select">
                        <option value="Matutino">Matutino</option>
                        <option value="Vespertino">Vespertino</option>
                        <option value="Integral">Integral</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">🚪 Sala / Ambiente</label>
                    <input type="text" name="sala" class="form-input" placeholder="Sala 105 - Ala Sul" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); margin-top: 8px;">
                ✨ Criar Nova Turma &rarr;
            </button>
        </form>
    </div>

    <!-- FORMULÁRIO NOVA DISCIPLINA -->
    <div class="glass-card" style="border-top: 3px solid #fbbf24; padding: 32px 28px;">
        <h3 style="font-size: 1.35rem; margin-bottom: 6px; color: #fbbf24; display: flex; align-items: center; gap: 10px;">
            📚 Cadastrar & Vincular Disciplina
        </h3>
        <p style="color: #94a3b8; font-size: 0.88rem; margin-bottom: 24px;">
            Cadastre uma disciplina com seu código oficial e vincule ao professor responsável na grade curricular.
        </p>
        <form method="POST">
            <input type="hidden" name="create_disciplina" value="1">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">🔢 Código</label>
                    <input type="text" name="codigo" class="form-input" placeholder="Ex: BIO-101" required>
                </div>
                <div class="form-group">
                    <label class="form-label">📖 Nome da Disciplina</label>
                    <input type="text" name="nome_disciplina" class="form-input" placeholder="Ex: Biologia Molecular e Genética" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">🏫 Turma Vinculada</label>
                    <select name="turma_id" class="form-select">
                        <?php foreach ($turmas as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?> (<?= htmlspecialchars($t['ano_letivo']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">👨‍🏫 Docente Responsável</label>
                    <select name="prof_id" class="form-select">
                        <?php foreach ($professores as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); margin-top: 8px;">
                🔗 Vincular Disciplina &rarr;
            </button>
        </form>
    </div>
</div>

<div class="glass-card" style="margin-bottom: 30px;">
    <h3 style="font-size: 1.35rem; margin-bottom: 16px; color: #60a5fa;">🏫 Turmas Ativas na Instituição (<?= count($turmas) ?>)</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome da Turma</th>
                    <th>Ano Letivo</th>
                    <th>Turno</th>
                    <th>Sala / Ambiente</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($turmas)): ?>
                    <tr><td colspan="5" style="text-align: center;">Nenhuma turma cadastrada no sistema.</td></tr>
                <?php else: ?>
                    <?php foreach ($turmas as $t): ?>
                        <tr>
                            <td><span class="badge badge-accent">#<?= $t['id'] ?></span></td>
                            <td><strong><?= htmlspecialchars($t['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($t['ano_letivo']) ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($t['turno']) ?></span></td>
                            <td><?= htmlspecialchars($t['sala']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="glass-card">
    <h3 style="font-size: 1.35rem; margin-bottom: 16px; color: #fbbf24;">📚 Disciplinas & Grades Ativas (<?= count($disciplinas) ?>)</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Disciplina</th>
                    <th>Turma</th>
                    <th>Professor Responsável</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($disciplinas as $d): ?>
                    <tr>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($d['codigo']) ?></span></td>
                        <td><strong><?= htmlspecialchars($d['nome']) ?></strong></td>
                        <td><?= htmlspecialchars($d['turma_nome'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($d['prof_nome'] ?? 'A Definir') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
