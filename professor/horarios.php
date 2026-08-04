<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('professor');

$pageTitle = 'Minha Grade de Horários — ' . APP_NAME;
$pageHeaderTitle = 'Horários & Cronograma Semanal';
$activeModule = 'horarios';

$pdo = get_db_connection();
$professorId = $_SESSION['profile_id'] ?? 1;

// Disciplinas ensinadas
$stmtDisc = $pdo->prepare("
    SELECT d.*, t.nome as turma_nome, t.sala, t.turno 
    FROM disciplinas d
    LEFT JOIN turmas t ON d.turma_id = t.id
    WHERE d.professor_id = ?
");
$stmtDisc->execute([$professorId]);
$disciplinas = $stmtDisc->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<div class="glass-card" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 1.4rem; color: #60a5fa;">Grade de Aulas e Turnos — Ano Letivo 2026</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Aulas regulares organizadas por dias da semana e alocação de salas do Campus Paulista.
            </p>
        </div>
        <button onclick="window.print()" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">
            🖨️ Imprimir Horário
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table" style="text-align: center;">
            <thead>
                <tr>
                    <th>Horário</th>
                    <th>Segunda-feira</th>
                    <th>Terça-feira</th>
                    <th>Quarta-feira</th>
                    <th>Quinta-feira</th>
                    <th>Sexta-feira</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>07:30 - 08:20</strong></td>
                    <td>
                        <span class="badge badge-primary">MAT-301</span><br>
                        <small style="color: #94a3b8;">3ª Série — Sala 301</small>
                    </td>
                    <td>—</td>
                    <td>
                        <span class="badge badge-primary">MAT-301</span><br>
                        <small style="color: #94a3b8;">3ª Série — Sala 301</small>
                    </td>
                    <td>—</td>
                    <td>
                        <span class="badge badge-primary">MAT-301</span><br>
                        <small style="color: #94a3b8;">3ª Série — Sala 301</small>
                    </td>
                </tr>
                <tr>
                    <td><strong>08:20 - 09:10</strong></td>
                    <td>
                        <span class="badge badge-primary">MAT-301</span><br>
                        <small style="color: #94a3b8;">3ª Série — Sala 301</small>
                    </td>
                    <td>
                        <span class="badge badge-accent">MAT-201</span><br>
                        <small style="color: #94a3b8;">2ª Série — Sala 204</small>
                    </td>
                    <td>
                        <span class="badge badge-primary">MAT-301</span><br>
                        <small style="color: #94a3b8;">3ª Série — Sala 301</small>
                    </td>
                    <td>
                        <span class="badge badge-accent">MAT-201</span><br>
                        <small style="color: #94a3b8;">2ª Série — Sala 204</small>
                    </td>
                    <td>—</td>
                </tr>
                <tr style="background: rgba(15, 23, 42, 0.4);">
                    <td><strong>09:10 - 09:30</strong></td>
                    <td colspan="5" style="color: #fbbf24; font-weight: 600;">☕ INTERVALO / RECREIO ACADÊMICO</td>
                </tr>
                <tr>
                    <td><strong>09:30 - 10:20</strong></td>
                    <td>—</td>
                    <td>
                        <span class="badge badge-accent">MAT-201</span><br>
                        <small style="color: #94a3b8;">2ª Série — Sala 204</small>
                    </td>
                    <td>—</td>
                    <td>
                        <span class="badge badge-accent">MAT-201</span><br>
                        <small style="color: #94a3b8;">2ª Série — Sala 204</small>
                    </td>
                    <td>
                        <span class="badge badge-primary">MAT-301</span><br>
                        <small style="color: #94a3b8;">3ª Série — Sala 301</small>
                    </td>
                </tr>
                <tr>
                    <td><strong>10:20 - 11:10</strong></td>
                    <td>
                        <span class="badge badge-success">PLANTÃO</span><br>
                        <small style="color: #94a3b8;">Atendimento Dúvidas</small>
                    </td>
                    <td>—</td>
                    <td>
                        <span class="badge badge-success">PLANTÃO</span><br>
                        <small style="color: #94a3b8;">Atendimento Dúvidas</small>
                    </td>
                    <td>—</td>
                    <td>—</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
