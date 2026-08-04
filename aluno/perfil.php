<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('aluno');

$pageTitle = 'Meu Perfil Completo — ' . APP_NAME;
$pageHeaderTitle = 'Cadastro & Dados Pessoais';
$activeModule = 'perfil';

$pdo = get_db_connection();
$alunoId = $_SESSION['profile_id'] ?? 1;
$userId = $_SESSION['user_id'];
$message = '';
$success = false;

// Atualização dos dados de contato do aluno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $telefone = sanitize_input($_POST['telefone'] ?? '');
    $endereco = sanitize_input($_POST['endereco'] ?? '');
    
    try {
        $stmtUp = $pdo->prepare("UPDATE alunos SET telefone = ?, endereco = ? WHERE id = ?");
        $stmtUp->execute([$telefone, $endereco, $alunoId]);
        
        log_system_action($pdo, $userId, "Aluno atualizou dados de contato no perfil");
        $success = true;
        $message = "Dados de contato atualizados com sucesso!";
    } catch (Exception $e) {
        $message = "Erro ao atualizar dados: " . $e->getMessage();
    }
}

// Busca dados completos unindo usuarios + alunos + turmas
$stmtAlu = $pdo->prepare("
    SELECT a.*, u.nome, u.email, u.avatar, t.nome AS turma_nome, t.sala, t.turno 
    FROM alunos a 
    JOIN usuarios u ON a.usuario_id = u.id 
    LEFT JOIN turmas t ON a.turma_id = t.id 
    WHERE a.id = ? LIMIT 1
");
$stmtAlu->execute([$alunoId]);
$aluno = $stmtAlu->fetch();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: flex-start;">
    <!-- CARD RESUMO AVATAR & MATRÍCULA -->
    <div class="glass-card" style="text-align: center;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1e3a8a); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; font-weight: 800; margin: 0 auto 20px; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);">
            <?= mb_substr(htmlspecialchars($aluno['nome'] ?? 'A'), 0, 1) ?>
        </div>
        <h3 style="font-size: 1.5rem; margin-bottom: 8px;"><?= htmlspecialchars($aluno['nome'] ?? 'Estudante Master') ?></h3>
        <span class="badge badge-primary" style="margin-bottom: 20px;">Matrícula: <?= htmlspecialchars($aluno['matricula'] ?? 'MS000') ?></span>

        <div style="text-align: left; border-top: 1px solid var(--glass-border); padding-top: 20px; font-size: 0.9rem; display: flex; flex-direction: column; gap: 10px;">
            <div>🏫 <strong>Turma:</strong> <?= htmlspecialchars($aluno['turma_nome'] ?? '3ª Série') ?></div>
            <div>⏰ <strong>Turno:</strong> <?= htmlspecialchars($aluno['turno'] ?? 'Matutino') ?></div>
            <div>📍 <strong>Sala:</strong> <?= htmlspecialchars($aluno['sala'] ?? 'Sala 301') ?></div>
            <div>✉️ <strong>E-mail Institucional:</strong> <?= htmlspecialchars($aluno['email'] ?? '') ?></div>
        </div>
    </div>

    <!-- FORMULÁRIO COMPLETO DE CADASTRO (LEITURA E EDIÇÃO) -->
    <div class="glass-card">
        <h3 style="font-size: 1.4rem; margin-bottom: 20px; color: #60a5fa;">Ficha Cadastral Acadêmica</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 24px;">
            Os dados documentais (CPF, Matrícula, Data de Nascimento) são validados pela Secretaria Acadêmica. Você pode editar seu endereço e telefone de contato.
        </p>

        <?php if ($message): ?>
            <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="update_profile" value="1">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Número de Matrícula (Bloqueado)</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($aluno['matricula'] ?? '') ?>" disabled style="opacity: 0.7;">
                </div>

                <div class="form-group">
                    <label class="form-label">CPF do Estudante (Bloqueado)</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($aluno['cpf'] ?? '') ?>" disabled style="opacity: 0.7;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Data de Nascimento (Bloqueado)</label>
                    <input type="text" class="form-input" value="<?= format_date($aluno['data_nascimento'] ?? '') ?>" disabled style="opacity: 0.7;">
                </div>

                <div class="form-group">
                    <label class="form-label">Telefone / Celular (Editável)</label>
                    <input type="text" name="telefone" class="form-input" value="<?= htmlspecialchars($aluno['telefone'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Endereço Residencial Completo (Editável)</label>
                <input type="text" name="endereco" class="form-input" value="<?= htmlspecialchars($aluno['endereco'] ?? '') ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Salvar Alterações de Contato
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
