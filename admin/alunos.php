<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Gestão de Alunos — ' . APP_NAME;
$pageHeaderTitle = 'Alunos & Matrículas';
$activeModule = 'alunos';

$pdo = get_db_connection();
$message = '';
$success = false;

// 1. ADICIONAR NOVO ALUNO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_aluno'])) {
    $nome = sanitize_input($_POST['nome'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $matricula = sanitize_input($_POST['matricula'] ?? '');
    $cpf = sanitize_input($_POST['cpf'] ?? '');
    $turmaId = (int)($_POST['turma_id'] ?? 1);
    $dataNasc = sanitize_input($_POST['data_nascimento'] ?? '2008-01-01');

    if (!empty($nome) && !empty($email) && !empty($matricula)) {
        try {
            $pdo->beginTransaction();
            // Senha padrão aluno123
            $hash = password_hash('aluno123', PASSWORD_BCRYPT);

            $stmtU = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, 'aluno')");
            $stmtU->execute([$nome, $email, $hash]);
            $newUserId = $pdo->lastInsertId();

            $stmtA = $pdo->prepare("INSERT INTO alunos (usuario_id, matricula, cpf, data_nascimento, turma_id) VALUES (?, ?, ?, ?, ?)");
            $stmtA->execute([$newUserId, $matricula, $cpf, $dataNasc, $turmaId]);

            $pdo->commit();
            log_system_action($pdo, $_SESSION['user_id'], "Cadastrou novo aluno: $nome ($matricula)");
            $success = true;
            $message = "Aluno(a) cadastrado(a) com sucesso! A senha inicial é aluno123.";
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = "Erro ao cadastrar aluno: E-mail ou matrícula possivelmente em uso.";
        }
    } else {
        $message = "Preencha os campos obrigatórios (Nome, E-mail e Matrícula).";
    }
}

// 2. EXCLUIR ALUNO (GET)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    try {
        $stmtFind = $pdo->prepare("SELECT usuario_id FROM alunos WHERE id = ? LIMIT 1");
        $stmtFind->execute([$delId]);
        $row = $stmtFind->fetch();
        if ($row) {
            $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$row['usuario_id']]);
            log_system_action($pdo, $_SESSION['user_id'], "Removeu aluno ID $delId");
            $success = true;
            $message = "Estudante excluído do sistema ERP com sucesso.";
        }
    } catch (Exception $e) {
        $message = "Erro ao excluir: Aluno possui registros acadêmicos ou financeiros associados.";
    }
}

// Busca turmas para o select
$turmas = $pdo->query("SELECT id, nome FROM turmas ORDER BY nome ASC")->fetchAll();

// Busca e Filtra Alunos
$searchQuery = sanitize_input($_GET['q'] ?? '');
$sql = "
    SELECT a.*, u.nome, u.email, t.nome as turma_nome 
    FROM alunos a 
    JOIN usuarios u ON a.usuario_id = u.id 
    LEFT JOIN turmas t ON a.turma_id = t.id 
";
$params = [];
if ($searchQuery !== '') {
    $sql .= " WHERE u.nome LIKE ? OR a.matricula LIKE ? OR u.email LIKE ?";
    $params = ["%$searchQuery%", "%$searchQuery%", "%$searchQuery%"];
}
$sql .= " ORDER BY u.nome ASC";
$stmtList = $pdo->prepare($sql);
$stmtList->execute($params);
$alunos = $stmtList->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<?php if ($message): ?>
    <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- ACORDEÃO OU CARD DE ADIÇÃO RÁPIDA -->
<div class="glass-card" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 1.3rem; color: #60a5fa;">➕ Matricular Novo Aluno no ERP</h3>
        <button type="button" onclick="document.getElementById('formNovoAluno').style.display = (document.getElementById('formNovoAluno').style.display === 'none') ? 'block' : 'none'" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
            Exibir / Ocultar Formulário
        </button>
    </div>

    <form method="POST" id="formNovoAluno" style="display: none; border-top: 1px solid var(--glass-border); padding-top: 20px;">
        <input type="hidden" name="create_aluno" value="1">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" name="nome" class="form-input" placeholder="Ex: Ana Clara Silva" required>
            </div>
            <div class="form-group">
                <label class="form-label">Matrícula</label>
                <input type="text" name="matricula" class="form-input" placeholder="MS2026-99" required>
            </div>
            <div class="form-group">
                <label class="form-label">Turma</label>
                <select name="turma_id" class="form-select">
                    <?php foreach ($turmas as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">E-mail Institucional</label>
                <input type="email" name="email" class="form-input" placeholder="ana.clara@aluno.masterschool.edu.br" required>
            </div>
            <div class="form-group">
                <label class="form-label">CPF do Aluno</label>
                <input type="text" name="cpf" class="form-input" placeholder="000.000.000-00">
            </div>
            <div class="form-group">
                <label class="form-label">Data de Nascimento</label>
                <input type="date" name="data_nascimento" class="form-input" value="2008-05-10">
            </div>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">
                💾 Confirmar Matrícula e Criar Conta &rarr;
            </button>
        </div>
    </form>
</div>

<!-- LISTAGEM E FILTRO -->
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h3 style="font-size: 1.35rem;">Alunos Matriculados (<?= count($alunos) ?>)</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Gerencie perfis, matrículas e turmas dos estudantes.</p>
        </div>
        <form method="GET" action="alunos.php" style="display: flex; gap: 8px;">
            <input type="text" name="q" class="form-input" placeholder="Buscar nome ou matrícula..." value="<?= htmlspecialchars($searchQuery) ?>" style="width: 220px;">
            <button type="submit" class="btn btn-outline" style="padding: 8px 14px;">🔍</button>
            <?php if ($searchQuery !== ''): ?>
                <a href="alunos.php" class="btn btn-outline" style="padding: 8px 14px;">✕</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nome do Estudante</th>
                    <th>E-mail</th>
                    <th>Turma</th>
                    <th style="text-align: right;">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alunos)): ?>
                    <tr><td colspan="5" style="text-align: center;">Nenhum aluno cadastrado ou localizado com o filtro.</td></tr>
                <?php else: ?>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($aluno['matricula']) ?></span></td>
                            <td><strong><?= htmlspecialchars($aluno['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($aluno['email']) ?></td>
                            <td><?= htmlspecialchars($aluno['turma_nome'] ?? 'Sem Turma') ?></td>
                            <td style="text-align: right;">
                                <a href="alunos.php?delete=<?= $aluno['id'] ?>" onclick="return confirm('Tem certeza que deseja remover este aluno?')" class="btn btn-outline" style="color: #f87171; border-color: #ef4444; padding: 4px 10px; font-size: 0.78rem;">
                                    🗑️ Excluir
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
