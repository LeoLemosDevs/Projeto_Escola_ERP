<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$message = '';
$success = false;
$recoveredPassword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = 'Por favor, informe seu e-mail cadastrado.';
    } else {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Em ambiente de produção real seria disparado o email via SMTP/PHPMailer.
                // Em nosso ERP local / XAMPP exibimos o status e permitimos redefinir para a senha padrão do perfil.
                $newPass = ($user['role'] === 'admin') ? 'admin123' : (($user['role'] === 'professor') ? 'prof123' : 'aluno123');
                $newHash = password_hash($newPass, PASSWORD_BCRYPT);

                $update = $pdo->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
                $update->execute([$newHash, $user['id']]);

                log_system_action($pdo, $user['id'], "Recuperação de senha solicitada (Redefinida via email)");

                $success = true;
                $message = "E-mail de recuperação enviado para " . htmlspecialchars($email) . "! Em nosso ambiente de teste, sua senha foi redefinida para:";
                $recoveredPassword = $newPass;
            } else {
                $message = 'Nenhum usuário localizado com o e-mail informado.';
            }
        } catch (Exception $e) {
            $message = 'Erro ao processar solicitação. Verifique a conexão do banco de dados.';
        }
    }
}

$pageTitle = 'Recuperar Senha — ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 40px 0;">
    <div class="container" style="max-width: 500px;">
        <div class="glass-card" style="padding: 40px; text-align: center;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 20px;">
                ✉️
            </div>
            <h2 style="font-size: 1.8rem; margin-bottom: 10px;">Recuperação de Senha</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 30px;">
                Digite seu e-mail cadastrado na Master School para gerar uma nova senha de acesso ao ERP.
            </p>

            <?php if ($message): ?>
                <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.95rem; text-align: center;">
                    <?= $message ?>
                    <?php if ($recoveredPassword): ?>
                        <div style="margin-top: 10px; font-weight: 700; font-size: 1.2rem; color: #f59e0b; background: rgba(0,0,0,0.3); padding: 8px; border-radius: 8px;">
                            <?= htmlspecialchars($recoveredPassword) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" action="esqueci-senha.php">
                    <div class="form-group" style="text-align: left;">
                        <label class="form-label">E-mail Cadastrado</label>
                        <input type="email" name="email" class="form-input" placeholder="seuemail@masterschool.edu.br" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Enviar Instruções por E-mail &rarr;
                    </button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary" style="width: 100%;">
                    Voltar para o Login
                </a>
            <?php endif; ?>

            <div style="margin-top: 25px;">
                <a href="login.php" style="color: #94a3b8; font-size: 0.9rem; text-decoration: underline;">
                    &larr; Voltar ao Login de Entrada
                </a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
