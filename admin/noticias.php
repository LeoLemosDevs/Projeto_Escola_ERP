<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Mural de Notícias & Eventos — ' . APP_NAME;
$pageHeaderTitle = 'Gestão do Mural Escolar';
$activeModule = 'noticias';

$pdo = get_db_connection();
$message = '';
$success = false;

// 1. PUBLICAR NOVO COMUNICADO / NOTÍCIA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_noticia'])) {
    $titulo = sanitize_input($_POST['titulo'] ?? '');
    $resumo = sanitize_input($_POST['resumo'] ?? '');
    $categoria = sanitize_input($_POST['categoria'] ?? 'evento');
    $destaque = isset($_POST['destaque']) ? 1 : 0;

    if (!empty($titulo) && !empty($resumo)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO noticias (titulo, resumo, conteudo, categoria, destaque, publicada) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$titulo, $resumo, $resumo, $categoria, $destaque]);
            log_system_action($pdo, $_SESSION['user_id'], "Publicou notícia no mural: $titulo");
            $success = true;
            $message = "Comunicado publicado com sucesso na Página Principal!";
        } catch (Exception $e) {
            $message = "Erro ao publicar: " . $e->getMessage();
        }
    }
}

// 2. EXCLUIR NOTÍCIA
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([$delId]);
        log_system_action($pdo, $_SESSION['user_id'], "Excluiu notícia ID $delId");
        $success = true;
        $message = "Notícia removida do mural público com sucesso.";
    } catch (Exception $e) {
        $message = "Erro ao excluir notícia.";
    }
}

$stmtList = $pdo->query("SELECT * FROM noticias ORDER BY id DESC");
$noticias = $stmtList->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<?php if ($message): ?>
    <div style="background: <?= $success ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= $success ? '#10b981' : '#ef4444' ?>; color: <?= $success ? '#34d399' : '#f87171' ?>; padding: 14px; border-radius: 12px; margin-bottom: 24px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="glass-card" style="margin-bottom: 30px;">
    <h3 style="font-size: 1.35rem; margin-bottom: 16px; color: #60a5fa;">📰 Publicar Novo Aviso ou Evento Escolar</h3>
    <form method="POST">
        <input type="hidden" name="create_noticia" value="1">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Título da Chamada</label>
                <input type="text" name="titulo" class="form-input" placeholder="Ex: Feira de Ciências 2026 — Inscrições Abertas" required>
            </div>
            <div class="form-group">
                <label class="form-label">Categoria</label>
                <select name="categoria" class="form-select">
                    <option value="evento">Evento Escolar</option>
                    <option value="ferias">Férias & Recessos</option>
                    <option value="aviso">Aviso Acadêmico</option>
                    <option value="conquista">Alunos Destaques</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Resumo do Comunicado (Exibido na Home)</label>
            <textarea name="resumo" rows="3" class="form-textarea" placeholder="Breve descrição da notícia para a comunidade escolar..." required></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; color: #fbbf24; font-weight: 600;">
                <input type="checkbox" name="destaque" value="1">
                ⭐ Destacar no Mural Principal da Master School
            </label>
            <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">
                📢 Publicar no Portal Principal
            </button>
        </div>
    </form>
</div>

<div class="glass-card">
    <h3 style="font-size: 1.35rem; margin-bottom: 16px;">Notícias & Comunicados Publicados (<?= count($noticias) ?>)</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Título & Resumo</th>
                    <th>Destaque</th>
                    <th>Data</th>
                    <th style="text-align: right;">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($noticias)): ?>
                    <tr><td colspan="5" style="text-align: center;">Nenhuma notícia cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($noticias as $n): ?>
                        <tr>
                            <td>
                                <span class="badge <?= $n['categoria'] === 'ferias' ? 'badge-danger' : ($n['categoria'] === 'evento' ? 'badge-primary' : 'badge-accent') ?>">
                                    <?= strtoupper($n['categoria']) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($n['titulo']) ?></strong><br>
                                <span style="font-size: 0.85rem; color: #94a3b8;"><?= htmlspecialchars($n['resumo']) ?></span>
                            </td>
                            <td>
                                <?= $n['destaque'] ? '<span style="color: #fbbf24;">⭐ SIM</span>' : '<span style="color: #64748b;">NÃO</span>' ?>
                            </td>
                            <td><?= format_date($n['criado_em'], 'd/m/Y') ?></td>
                            <td style="text-align: right;">
                                <a href="noticias.php?delete=<?= $n['id'] ?>" onclick="return confirm('Excluir este comunicado do mural?')" class="btn btn-outline" style="color: #f87171; border-color: #ef4444; padding: 4px 10px; font-size: 0.78rem;">
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
