<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('aluno');

$pageTitle = 'Boletim de Notas & Frequência — ' . APP_NAME;
$pageHeaderTitle = 'Boletim Acadêmico do Aluno';
$activeModule = 'boletim';

$pdo = get_db_connection();
$alunoId = $_SESSION['profile_id'] ?? 1;

// Busca disciplinas, notas dos 4 bimestres e observações
$stmtNotas = $pdo->prepare("
    SELECT 
        d.id as disciplina_id,
        d.nome as disciplina_nome,
        d.codigo,
        p.especialidade as prof_especialidade,
        u.nome as prof_nome,
        MAX(CASE WHEN n.bimestre = 1 THEN n.nota END) as b1,
        MAX(CASE WHEN n.bimestre = 2 THEN n.nota END) as b2,
        MAX(CASE WHEN n.bimestre = 3 THEN n.nota END) as b3,
        MAX(CASE WHEN n.bimestre = 4 THEN n.nota END) as b4,
        MAX(n.observacao_professor) as observacao
    FROM disciplinas d
    LEFT JOIN professores p ON d.professor_id = p.id
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN notas n ON n.disciplina_id = d.id AND n.aluno_id = ?
    GROUP BY d.id, d.nome, d.codigo, p.especialidade, u.nome
    ORDER BY d.nome ASC
");
$stmtNotas->execute([$alunoId]);
$boletim = $stmtNotas->fetchAll();

// Busca Histórico Anual Completo de Frequência (Chamada do Ano Inteiro)
$stmtFreq = $pdo->prepare("
    SELECT 
        f.data_aula,
        f.presente,
        f.observacao,
        d.nome as disciplina_nome
    FROM frequencias f
    JOIN disciplinas d ON f.disciplina_id = d.id
    WHERE f.aluno_id = ?
    ORDER BY f.data_aula DESC
");
$stmtFreq->execute([$alunoId]);
$historicoFrequencia = $stmtFreq->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<!-- 1. ABA DE NOTAS E OBSERVAÇÕES DO PROFESSOR -->
<div class="glass-card" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 1.4rem; color: #60a5fa;">Boletim Bimestral de Notas</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Notas oficiais de 1º ao 4º Bimestre e parecer acadêmico.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">
            🖨️ Imprimir Boletim PDF
        </button>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Disciplina / Código</th>
                    <th>Professor(a)</th>
                    <th>1º Bim</th>
                    <th>2º Bim</th>
                    <th>3º Bim</th>
                    <th>4º Bim</th>
                    <th>Média</th>
                    <th>Feedback / Observação do Professor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($boletim)): ?>
                    <tr><td colspan="8" style="text-align: center;">Nenhuma nota lançada ainda no período letivo.</td></tr>
                <?php else: ?>
                    <?php foreach ($boletim as $row): ?>
                        <?php
                            $notasValidas = array_filter([$row['b1'], $row['b2'], $row['b3'], $row['b4']], function($val) { return $val !== null; });
                            $media = count($notasValidas) > 0 ? array_sum($notasValidas) / count($notasValidas) : 0;
                            $mediaFormatada = count($notasValidas) > 0 ? number_format($media, 1, ',', '.') : '-';
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['disciplina_nome']) ?></strong><br>
                                <span style="font-size: 0.78rem; color: #94a3b8;"><?= htmlspecialchars($row['codigo']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['prof_nome'] ?? 'Corpo Docente') ?></td>
                            <td><?= $row['b1'] !== null ? number_format($row['b1'], 1, ',', '.') : '-' ?></td>
                            <td><?= $row['b2'] !== null ? number_format($row['b2'], 1, ',', '.') : '-' ?></td>
                            <td><?= $row['b3'] !== null ? number_format($row['b3'], 1, ',', '.') : '-' ?></td>
                            <td><?= $row['b4'] !== null ? number_format($row['b4'], 1, ',', '.') : '-' ?></td>
                            <td><strong style="color: #60a5fa;"><?= $mediaFormatada ?></strong></td>
                            <td style="max-width: 280px; font-size: 0.85rem; color: #cbd5e1; font-style: italic;">
                                "<?= htmlspecialchars($row['observacao'] ?? 'Desempenho acompanhado.') ?>"
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 2. ABA DE FREQUÊNCIA E CHAMADA DO ANO INTEIRO -->
<div class="glass-card">
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 1.4rem; color: #fbbf24;">Histórico de Presenças e Chamada Anual</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Registros de chamada lançados diariamente pelos professores nas turmas.</p>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data da Aula</th>
                    <th>Disciplina</th>
                    <th>Status de Chamada</th>
                    <th>Observação / Justificativa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historicoFrequencia)): ?>
                    <tr><td colspan="4" style="text-align: center;">Nenhum registro de chamada encontrado para este aluno.</td></tr>
                <?php else: ?>
                    <?php foreach ($historicoFrequencia as $f): ?>
                        <tr>
                            <td><strong><?= format_date($f['data_aula']) ?></strong></td>
                            <td><?= htmlspecialchars($f['disciplina_nome']) ?></td>
                            <td>
                                <?php if ($f['presente']): ?>
                                    <span class="badge badge-success">✔ Presente</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">✖ Ausente / Falta</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($f['observacao'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
