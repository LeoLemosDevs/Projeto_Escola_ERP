<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Meu Perfil Administrativo — ' . APP_NAME;
$pageHeaderTitle = 'Dados de Administração Geral';
$activeModule = 'perfil';

$pdo = get_db_connection();
$userId = $_SESSION['user_id'];
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $nome = sanitize_input($_POST['nome'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $novaSenha = $_POST['nova_senha'] ?? '';

    if (!empty($nome) && !empty($email)) {
        try {
            if (!empty($novaSenha)) {
                $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha_hash = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $hash, $userId]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $userId]);
            }

            $_SESSION['user_name'] = $nome;
            $_SESSION['user_email'] = $email;

            log_system_action($pdo, $userId, "Atualizou dados do perfil admin");
            $success = true;
            $message = "Dados administrativos atualizados com sucesso!";
        } catch (Exception $e) {
            $message = "Erro ao atualizar dados administrativos.";
        }
    }
}

$stmtU = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
$stmtU->execute([$userId]);
$admin = $stmtU->fetch();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="glass-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #a855f7, #6b21a8); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 16px;">
                ⚙️
            </div>
            <h3 style="font-size: 1.5rem;"><?= htmlspecialchars($admin['nome'] ?? 'Administrador') ?></h3>
            <span class="badge badge-primary">Diretor(a) / Gestão TI do ERP</span>
        </div>

        <?php if ($message): ?>
            <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px; text-align: center;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="update_admin" value="1">

            <div class="form-group">
                <label class="form-label">Nome de Gestão</label>
                <input type="text" name="nome" class="form-input" value="<?= htmlspecialchars($admin['nome'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail Administrativo</label>
                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nova Senha (Deixe em branco para não alterar)</label>
                <input type="password" name="nova_senha" class="form-input" placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                Salvar Alterações de Perfil &rarr;
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
