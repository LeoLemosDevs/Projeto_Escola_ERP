<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_role('aluno');

$pdo = get_db_connection();
$alunoId = $_SESSION['profile_id'] ?? 1;

// Processamento de pagamento via AJAX / POST (Simulador PagSeguro)
if (isset($_GET['action']) && $_GET['action'] === 'pay' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $billId = (int)($_GET['id'] ?? 0);
    $method = sanitize_input($_GET['method'] ?? 'pix');
    $codPs  = 'PAGSEGURO-' . strtoupper(substr(md5(uniqid()), 0, 10));

    try {
        $stmtPay = $pdo->prepare("
            UPDATE mensalidades 
            SET status = 'pago', 
                metodo_pagamento = ?, 
                codigo_pagseguro = ?, 
                data_pagamento = NOW() 
            WHERE id = ? AND aluno_id = ?
        ");
        $stmtPay->execute([$method, $codPs, $billId, $alunoId]);

        log_system_action($pdo, $_SESSION['user_id'], "Pagamento mensalidade ID $billId via PagSeguro ($method)");

        echo json_encode(['success' => true, 'codigo_ps' => $codPs]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$pageTitle = 'Pagamento de Mensalidades — ' . APP_NAME;
$pageHeaderTitle = 'Financeiro & PagSeguro';
$activeModule = 'financeiro';

$stmtList = $pdo->prepare("SELECT * FROM mensalidades WHERE aluno_id = ? ORDER BY vencimento ASC");
$stmtList->execute([$alunoId]);
$mensalidades = $stmtList->fetchAll();
?>
<?php include __DIR__ . '/../includes/dashboard_header.php'; ?>

<script src="<?= base_url('assets/js/payment.js') ?>" defer></script>

<div class="glass-card" style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 1.4rem; color: #60a5fa;">Mensalidades Escolares</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Ambiente seguro integrado ao <strong>PagSeguro Checkout</strong> (PIX, Boleto e Cartão de Crédito).
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <span class="badge badge-success">Ambiente Protegido SSL</span>
            <span class="badge badge-primary">PagSeguro API <?= PAGSEGURO_ENV ?></span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mês de Referência</th>
                    <th>Vencimento</th>
                    <th>Valor do Título</th>
                    <th>Forma Pagto.</th>
                    <th>Status</th>
                    <th style="text-align: right;">Ação de Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mensalidades)): ?>
                    <tr><td colspan="6" style="text-align: center;">Nenhuma cobrança registrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($mensalidades as $m): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($m['mes_referencia']) ?></strong></td>
                            <td><?= format_date($m['vencimento']) ?></td>
                            <td><strong style="color: #60a5fa;"><?= format_currency($m['valor']) ?></strong></td>
                            <td><?= !empty($m['metodo_pagamento']) ? strtoupper($m['metodo_pagamento']) : '-' ?></td>
                            <td>
                                <?php if ($m['status'] === 'pago'): ?>
                                    <span class="badge badge-success">✔ PAGO</span>
                                <?php elseif ($m['status'] === 'atrasado'): ?>
                                    <span class="badge badge-danger">✖ ATRASADO</span>
                                <?php else: ?>
                                    <span class="badge badge-accent">⌛ PENDENTE</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($m['status'] === 'pago'): ?>
                                    <button type="button" onclick="showToast('Comprovante de Pagamento: Cód. <?= htmlspecialchars($m['codigo_pagseguro']) ?> — Pago em <?= format_date($m['data_pagamento'], 'd/m/Y H:i') ?>', 'success')" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">
                                        📄 Ver Comprovante
                                    </button>
                                <?php else: ?>
                                    <button type="button" onclick="openPagSeguroModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['mes_referencia'])) ?>', '<?= $m['valor'] ?>')" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;">
                                        ⚡ Pagar com PagSeguro
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL INTERATIVO DO PAGSEGURO CHECKOUT -->
<div class="modal-overlay" id="pagseguroModal">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 6px;">PagSeguro Checkout</span>
                <h3 id="psModalTitle" style="font-size: 1.3rem;">Mensalidade</h3>
                <span id="psModalValue" style="color: #34d399; font-weight: 700; font-size: 1.1rem;">R$ 0,00</span>
            </div>
            <button type="button" class="modal-close" onclick="closePagSeguroModal()">✕</button>
        </div>

        <div class="pagseguro-tabs">
            <button type="button" class="ps-tab-btn active" data-tab="pix" onclick="switchPaymentTab('pix')">💠 PIX Instantâneo</button>
            <button type="button" class="ps-tab-btn" data-tab="boleto" onclick="switchPaymentTab('boleto')">📄 Boleto Bancário</button>
            <button type="button" class="ps-tab-btn" data-tab="credito" onclick="switchPaymentTab('credito')">💳 Cartão de Crédito</button>
        </div>

        <!-- ABA PIX -->
        <div class="ps-tab-content" id="tab-pix">
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px;">
                    Abra o app do seu banco e escaneie o QR Code ou copie o código abaixo:
                </p>
                <div class="qr-placeholder">
                    <span>💠 QR CODE PAGSEGURO<br>PIX DINÂMICO</span>
                </div>
                <div style="display: flex; gap: 8px; margin-bottom: 15px;">
                    <input type="text" id="pixCopiaCola" readonly value="00020126360014br.gov.bcb.pix0114masterschool" class="form-input" style="font-size: 0.75rem; text-align: center;">
                    <button type="button" onclick="copyPixCode()" class="btn btn-outline" style="padding: 8px 14px;">Copiar</button>
                </div>
                <div style="font-size: 0.8rem; color: #fbbf24;">
                    ⌛ O código expira em 15 minutos. Aprovação em tempo real.
                </div>
            </div>
        </div>

        <!-- ABA BOLETO -->
        <div class="ps-tab-content" id="tab-boleto" style="display: none;">
            <div style="margin-bottom: 20px;">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px;">
                    O boleto pode ser pago em qualquer agência, lotérica ou aplicativo bancário até o vencimento.
                </p>
                <div style="background: rgba(15, 23, 42, 0.6); padding: 16px; border-radius: 12px; border: 1px solid var(--glass-border); margin-bottom: 15px;">
                    <span style="font-size: 0.8rem; color: #94a3b8; display: block; margin-bottom: 4px;">Linha Digitável:</span>
                    <strong style="color: #60a5fa; font-size: 0.95rem; word-break: break-all;">
                        34191.79001 01043.510047 91020.150008 2 9680000245000
                    </strong>
                </div>
                <button type="button" onclick="showToast('Visualizando PDF do Boleto PagSeguro...', 'info')" class="btn btn-outline" style="width: 100%;">
                    📥 Baixar Boleto em PDF
                </button>
            </div>
        </div>

        <!-- ABA CARTÃO DE CRÉDITO -->
        <div class="ps-tab-content" id="tab-credito" style="display: none;">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Número do Cartão</label>
                    <input type="text" id="cardNumberInput" class="form-input" placeholder="0000 0000 0000 0000" maxlength="19">
                </div>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Validade (MM/AA)</label>
                        <input type="text" class="form-input" placeholder="12/28">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-input" placeholder="123" maxlength="4">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Nome do Titular do Cartão</label>
                    <input type="text" class="form-input" placeholder="COMO IMPRESSO NO CARTÃO">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Parcelamento sem juros</label>
                    <select class="form-select">
                        <option value="1">1x à vista — R$ Sem juros</option>
                        <option value="2">2x — Sem juros</option>
                        <option value="3">3x — Sem juros</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- BOTÃO DE SIMULAÇÃO DE CONFIRMAÇÃO DO PAGSEGURO -->
        <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px; margin-top: 20px;">
            <button type="button" id="psProcessBtn" onclick="processPagSeguroPayment()" class="btn btn-primary" style="width: 100%; padding: 14px;">
                Confirmar & Processar Pagamento (PagSeguro Sandbox) &rarr;
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/dashboard_footer.php'; ?>
