<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Se já estiver logado, redireciona ao painel correspondente
if (is_logged_in()) {
    $role = get_user_role();
    if ($role === 'aluno') redirect('aluno/index.php');
    if ($role === 'professor') redirect('professor/index.php');
    if ($role === 'admin') redirect('admin/index.php');
}

$error = '';
$selectedRole = $_GET['role'] ?? 'aluno';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['senha'] ?? '';
    $roleTab = $_POST['role'] ?? 'aluno';

    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos de login.';
    } else {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND role = ? LIMIT 1");
            $stmt->execute([$email, $roleTab]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['senha_hash'])) {
                // Sessão Global do Usuário
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'];

                // Identifica o ID do perfil específico
                if ($user['role'] === 'aluno') {
                    $sAlu = $pdo->prepare("SELECT id FROM alunos WHERE usuario_id = ? LIMIT 1");
                    $sAlu->execute([$user['id']]);
                    $rowAlu = $sAlu->fetch();
                    $_SESSION['profile_id'] = $rowAlu['id'] ?? 1;
                } elseif ($user['role'] === 'professor') {
                    $sProf = $pdo->prepare("SELECT id FROM professores WHERE usuario_id = ? LIMIT 1");
                    $sProf->execute([$user['id']]);
                    $rowProf = $sProf->fetch();
                    $_SESSION['profile_id'] = $rowProf['id'] ?? 1;
                } else {
                    $_SESSION['profile_id'] = $user['id'];
                }

                // Log de auditoria
                log_system_action($pdo, $user['id'], "Login bem-sucedido no ERP ({$user['role']})");

                // Redirecionamento de acordo com a role
                if ($user['role'] === 'aluno') redirect('aluno/index.php');
                if ($user['role'] === 'professor') redirect('professor/index.php');
                if ($user['role'] === 'admin') redirect('admin/index.php');
            } else {
                $error = 'E-mail ou senha inválidos para o perfil selecionado.';
            }
        } catch (Exception $e) {
            $error = 'Erro na conexão com o banco de dados. Execute o instalador se ainda não configurou.';
        }
    }
}

$pageTitle = 'Login de Entrada — ' . APP_NAME;
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 40px 0;">
    <div class="container" style="max-width: 520px;">
        <div class="glass-card" style="padding: 40px 36px; position: relative;">
            
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; font-weight: 800; margin: 0 auto 16px;">
                    MS
                </div>
                <h2 style="font-size: 1.8rem; margin-bottom: 6px;">Portal Educacional ERP</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Selecione o seu tipo de perfil e acesse sua conta.
                </p>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #f87171; padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 0.9rem; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- ABAS DE PERFIL -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; background: rgba(15, 23, 42, 0.7); padding: 6px; border-radius: 14px; margin-bottom: 28px; border: 1px solid var(--glass-border);">
                <button type="button" class="btn-role <?= $selectedRole === 'aluno' ? 'active' : '' ?>" onclick="switchRole('aluno')">
                    🎓 Aluno
                </button>
                <button type="button" class="btn-role <?= $selectedRole === 'professor' ? 'active' : '' ?>" onclick="switchRole('professor')">
                    👨‍🏫 Professor
                </button>
                <button type="button" class="btn-role <?= $selectedRole === 'admin' ? 'active' : '' ?>" onclick="switchRole('admin')">
                    ⚙️ Admin
                </button>
            </div>

            <!-- FORMULÁRIO DE LOGIN -->
            <form method="POST" action="login.php" id="loginForm">
                <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($selectedRole) ?>">

                <div class="form-group">
                    <label class="form-label" for="emailInput">E-mail Cadastrado</label>
                    <input type="email" name="email" id="emailInput" class="form-input" placeholder="ex: aluno@masterschool.edu.br" required autocomplete="email">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label class="form-label" for="senhaInput" style="margin: 0;">Senha</label>
                        <a href="esqueci-senha.php" style="color: #60a5fa; font-size: 0.82rem; text-decoration: underline;">Esqueceu a senha?</a>
                    </div>
                    <div style="position: relative;">
                        <input type="password" name="senha" id="senhaInput" class="form-input" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" style="position: absolute; right: 14px; top: 14px; background: none; border: none; color: #94a3b8; cursor: pointer;">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px; padding: 14px;">
                    Entrar no ERP &rarr;
                </button>
            </form>

            <!-- ATALHO PARA TESTE (DEMO) -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--glass-border); text-align: center;">
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">
                    ⚡ Modo de Teste Rápido (XAMPP Demo):
                </p>
                <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                    <button type="button" onclick="fillDemo('aluno')" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.75rem;">
                        Preencher Aluno
                    </button>
                    <button type="button" onclick="fillDemo('professor')" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.75rem;">
                        Preencher Professor
                    </button>
                    <button type="button" onclick="fillDemo('admin')" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.75rem;">
                        Preencher Admin
                    </button>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
.btn-role {
    background: transparent;
    border: none;
    color: var(--text-muted);
    font-family: var(--font-heading);
    font-weight: 600;
    font-size: 0.88rem;
    padding: 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-role.active {
    background: #3b82f6;
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}
.btn-role:hover:not(.active) {
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-main);
}
</style>

<script>
function switchRole(role) {
    document.getElementById('roleInput').value = role;
    document.querySelectorAll('.btn-role').forEach(btn => {
        btn.classList.remove('active');
        if (btn.innerText.toLowerCase().includes(role)) {
            btn.classList.add('active');
        }
    });
    
    // Atualiza placeholders visuais
    const emailInput = document.getElementById('emailInput');
    if (role === 'aluno') emailInput.placeholder = 'lucas.mendes@aluno.masterschool.edu.br';
    if (role === 'professor') emailInput.placeholder = 'carlos.silva@masterschool.edu.br';
    if (role === 'admin') emailInput.placeholder = 'admin@masterschool.edu.br';
}

function fillDemo(role) {
    switchRole(role);
    if (role === 'aluno') {
        document.getElementById('emailInput').value = 'lucas.mendes@aluno.masterschool.edu.br';
        document.getElementById('senhaInput').value = 'aluno123';
    } else if (role === 'professor') {
        document.getElementById('emailInput').value = 'carlos.silva@masterschool.edu.br';
        document.getElementById('senhaInput').value = 'prof123';
    } else {
        document.getElementById('emailInput').value = 'admin@masterschool.edu.br';
        document.getElementById('senhaInput').value = 'admin123';
    }
    showToast('Credenciais de demonstração (' + role.toUpperCase() + ') preenchidas!', 'info');
}

function togglePassword() {
    const field = document.getElementById('senhaInput');
    field.type = (field.type === 'password') ? 'text' : 'password';
}

// Inicializa no role padrão
document.addEventListener('DOMContentLoaded', () => {
    switchRole('<?= htmlspecialchars($selectedRole) ?>');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
