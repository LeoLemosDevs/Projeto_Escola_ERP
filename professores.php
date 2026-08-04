<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Nossos Professores — ' . APP_NAME;
$activePage = 'professores';

// Busca professores no banco de dados
$professores = [];
try {
    $pdo = get_db_connection();
    $stmt = $pdo->query("
        SELECT p.*, u.nome, u.email, u.avatar 
        FROM professores p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY u.nome ASC
    ");
    $professores = $stmt->fetchAll();
} catch (Exception $e) {
    // Modo offline / antes da instalação
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="section" style="padding-top: 60px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
            <span class="badge badge-primary" style="margin-bottom: 15px;">Corpo Docente</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Professores & Pesquisadores</h1>
            <p style="color: var(--text-muted); font-size: 1.15rem;">
                Mestres, Doutores e educadores internacionais dedicados ao avanço científico e literário dos nossos alunos.
            </p>
        </div>

        <?php if (empty($professores)): ?>
            <div class="glass-card" style="text-align: center; padding: 40px;">
                <p>Nenhum professor cadastrado no momento. Acesse o <a href="install.php" style="color: #60a5fa;">Instalador do Banco de Dados</a> para carregar os dados de demonstração da Master School.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($professores as $prof): ?>
                    <div class="glass-card professor-card">
                        <div class="prof-header">
                            <div class="prof-avatar">
                                <?= mb_substr(htmlspecialchars($prof['nome']), 0, 1) ?>
                            </div>
                            <div class="prof-info">
                                <h4><?= htmlspecialchars($prof['nome']) ?></h4>
                                <span class="badge badge-accent" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($prof['especialidade']) ?>
                                </span>
                            </div>
                        </div>

                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 10px 0;">
                            <?= htmlspecialchars($prof['bio'] ?? 'Professor titular da comunidade acadêmica Master School.') ?>
                        </p>

                        <div style="border-top: 1px solid var(--glass-border); padding-top: 15px; font-size: 0.85rem; color: #94a3b8; display: flex; flex-direction: column; gap: 6px;">
                            <div>🕒 <strong>Horário:</strong> <?= htmlspecialchars($prof['dias_horarios_trabalho'] ?? 'Sob consulta') ?></div>
                            <div>✉️ <strong>E-mail:</strong> <?= htmlspecialchars($prof['email']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
