<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('admin');

$pageTitle = 'Trilha de Auditoria & Logs — ' . APP_NAME;
$pageHeaderTitle = 'Trilha de Segurança e Monitoramento';
$activeModule = 'logs';

$pdo = get_db_connection();

$stmtLogs = $pdo->query("
    SELECT l.*, u.nome as usuario_nome, u.role, u.email
    FROM system_logs l
    LEFT JOIN usuarios u ON l.usuario_id = u.id
    ORDER BY l.data_hora DESC
    LIMIT 100
");
$logs = $stmtLogs->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 1.4rem; color: #60a5fa;">Logs de Sistema (Últimos 100 Registros)</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Todos os eventos relevantes de login, cadastro, alteração de notas e pagamentos são gravados para conformidade.
            </p>
        </div>
        <button onclick="window.print()" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 14px;">
            🖨️ Exportar Trilha de Auditoria
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Log</th>
                    <th>Data & Hora</th>
                    <th>Usuário / E-mail</th>
                    <th>Tipo (Role)</th>
                    <th>Ação Executada no ERP</th>
                    <th>Endereço IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" style="text-align: center;">Nenhuma ação de auditoria registrada ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><code>#<?= $log['id'] ?></code></td>
                            <td><?= format_date($log['criado_em'], 'd/m/Y H:i:s') ?></td>
                            <td>
                                <strong><?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema / Convidado') ?></strong><br>
                                <span style="font-size: 0.78rem; color: #94a3b8;"><?= htmlspecialchars($log['email'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="badge <?= ($log['role'] === 'admin') ? 'badge-primary' : (($log['role'] === 'professor') ? 'badge-accent' : 'badge-success') ?>">
                                    <?= strtoupper($log['role'] ?? 'SYS') ?>
                                </span>
                            </td>
                            <td style="color: #f8fafc; font-weight: 500;"><?= htmlspecialchars($log['acao']) ?></td>
                            <td><code style="background: rgba(0,0,0,0.3); padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;"><?= htmlspecialchars($log['ip_address']) ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
