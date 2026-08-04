<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Gestão de Professores — ' . APP_NAME;
$pageHeaderTitle = 'Corpo Docente & Especialidades';
$activeModule = 'professores';

$pdo = get_db_connection();
$message = '';
$success = false;

// 1. CADASTRAR NOVO PROFESSOR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_prof'])) {
    $nome = sanitize_input($_POST['nome'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $especialidade = sanitize_input($_POST['especialidade'] ?? 'Ensino Geral');
    $titulacao = sanitize_input($_POST['titulacao'] ?? 'Especialista');

    if (!empty($nome) && !empty($email)) {
        try {
            $pdo->beginTransaction();
            $hash = password_hash('prof123', PASSWORD_BCRYPT);
            $stmtU = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, 'professor')");
            $stmtU->execute([$nome, $email, $hash]);
            $newId = $pdo->lastInsertId();

            $stmtP = $pdo->prepare("INSERT INTO professores (usuario_id, especialidade, titulacao) VALUES (?, ?, ?)");
            $stmtP->execute([$newId, $especialidade, $titulacao]);

            $pdo->commit();
            log_system_action($pdo, $_SESSION['user_id'], "Cadastrou novo professor: $nome ($especialidade)");
            $success = true;
            $message = "Professor cadastrado com sucesso! A senha inicial é prof123.";
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $message = "Erro ao cadastrar professor. E-mail já utilizado.";
        }
    }
}

// 2. EXCLUIR PROFESSOR
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    try {
        $stmtFind = $pdo->prepare("SELECT usuario_id FROM professores WHERE id = ? LIMIT 1");
        $stmtFind->execute([$delId]);
        $row = $stmtFind->fetch();
        if ($row) {
            $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$row['usuario_id']]);
            log_system_action($pdo, $_SESSION['user_id'], "Removeu professor ID $delId");
            $success = true;
            $message = "Professor removido do quadro docente com sucesso.";
        }
    } catch (Exception $e) {
        $message = "Erro ao remover professor: possui disciplinas ou avaliações vinculadas.";
    }
}

// Busca Professores
$stmtList = $pdo->query("
    SELECT p.*, u.nome, u.email 
    FROM professores p 
    JOIN usuarios u ON p.usuario_id = u.id 
    ORDER BY u.nome ASC
");
$professores = $stmtList->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<?php if ($message): ?>
    <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="glass-card" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 1.3rem; color: #60a5fa;">👨‍🏫 Contratar / Cadastrar Novo Professor</h3>
        <button type="button" onclick="document.getElementById('formNovoProf').style.display = (document.getElementById('formNovoProf').style.display === 'none') ? 'block' : 'none'" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
            Exibir / Ocultar Formulário
        </button>
    </div>

    <form method="POST" id="formNovoProf" style="display: none; border-top: 1px solid var(--glass-border); padding-top: 20px;">
        <input type="hidden" name="create_prof" value="1">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" name="nome" class="form-input" placeholder="Ex: Prof. Roberto Mendes" required>
            </div>
            <div class="form-group">
                <label class="form-label">E-mail Institucional</label>
                <input type="email" name="email" class="form-input" placeholder="roberto@masterschool.edu.br" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Especialidade / Disciplina</label>
                <input type="text" name="especialidade" class="form-input" placeholder="Ex: Física e Mecânica" required>
            </div>
            <div class="form-group">
                <label class="form-label">Titulação Acadêmica</label>
                <select name="titulacao" class="form-select">
                    <option value="Especialista">Especialista</option>
                    <option value="Mestre">Mestre</option>
                    <option value="Doutor">Doutor / Pós-Doc</option>
                </select>
            </div>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">
                💾 Confirmar Cadastro de Docente &rarr;
            </button>
        </div>
    </form>
</div>

<div class="glass-card">
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 1.35rem;">Professores Ativos (<?= count($professores) ?>)</h3>
        <p style="color: var(--text-muted); font-size: 0.88rem;">Listagem completa do corpo docente da Master School.</p>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome do Docente</th>
                    <th>E-mail</th>
                    <th>Especialidade</th>
                    <th>Titulação</th>
                    <th style="text-align: right;">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($professores)): ?>
                    <tr><td colspan="5" style="text-align: center;">Nenhum professor cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($professores as $prof): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($prof['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($prof['email']) ?></td>
                            <td><?= htmlspecialchars($prof['especialidade']) ?></td>
                            <td><span class="badge badge-accent"><?= htmlspecialchars($prof['titulacao']) ?></span></td>
                            <td style="text-align: right;">
                                <a href="professores.php?delete=<?= $prof['id'] ?>" onclick="return confirm('Tem certeza que deseja remover este docente?')" class="btn btn-outline" style="color: #f87171; border-color: #ef4444; padding: 4px 10px; font-size: 0.78rem;">
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
