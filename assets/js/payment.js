/**
 * Master School ERP - Módulo Financeiro & Checkout PagSeguro (Sandbox ES6+)
 * Controla modal interativo de PIX, Boleto e Cartão de Crédito
 */

let currentBillId = null;
let currentMethod = 'pix';

function openPagSeguroModal(billId, refMonth, valueAmount) {
  currentBillId = billId;
  const modal = document.getElementById('pagseguroModal');
  const titleEl = document.getElementById('psModalTitle');
  const valueEl = document.getElementById('psModalValue');

  if (titleEl) titleEl.innerText = `Mensalidade de ${refMonth}`;
  if (valueEl) valueEl.innerText = `R$ ${parseFloat(valueAmount).toFixed(2).replace('.', ',')}`;

  if (modal) {
    modal.classList.add('active');
    switchPaymentTab('pix');
  }
}

function closePagSeguroModal() {
  const modal = document.getElementById('pagseguroModal');
  if (modal) modal.classList.remove('active');
}

function switchPaymentTab(method) {
  currentMethod = method;
  document.querySelectorAll('.ps-tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.ps-tab-content').forEach(content => content.style.display = 'none');

  const activeBtn = document.querySelector(`.ps-tab-btn[data-tab="${method}"]`);
  const activeContent = document.getElementById(`tab-${method}`);

  if (activeBtn) activeBtn.classList.add('active');
  if (activeContent) activeContent.style.display = 'block';
}

function copyPixCode() {
  const codeInput = document.getElementById('pixCopiaCola');
  if (codeInput) {
    navigator.clipboard.writeText(codeInput.value);
    showToast('Código PIX Copia-e-Cola copiado com sucesso!', 'success');
  }
}

/**
 * Simula o processamento do PagSeguro (Sandbox)
 */
async function processPagSeguroPayment() {
  if (!currentBillId) return;

  const btn = document.getElementById('psProcessBtn');
  const originalText = btn.innerHTML;
  btn.innerHTML = '⌛ Processando no PagSeguro...';
  btn.disabled = true;

  try {
    const response = await fetch(`financeiro.php?action=pay&id=${currentBillId}&method=${currentMethod}`, {
      method: 'POST'
    });
    const result = await response.json();

    if (result.success) {
      showToast(`✔ Pagamento ${currentMethod.toUpperCase()} aprovado com sucesso! Cód: ${result.codigo_ps}`, 'success');
      closePagSeguroModal();
      setTimeout(() => window.location.reload(), 1500);
    } else {
      showToast(result.message || 'Erro no processamento do pagamento.', 'error');
      btn.innerHTML = originalText;
      btn.disabled = false;
    }
  } catch (err) {
    showToast('Erro ao comunicar com o servidor do ERP.', 'error');
    btn.innerHTML = originalText;
    btn.disabled = false;
  }
}

// Formatação visual do Cartão de Crédito no formulário
document.addEventListener('DOMContentLoaded', () => {
  const cardInput = document.getElementById('cardNumberInput');
  if (cardInput) {
    cardInput.addEventListener('input', (e) => {
      let val = e.target.value.replace(/\D/g, '');
      val = val.replace(/(.{4})/g, '$1 ').trim();
      e.target.value = val.substring(0, 19);
    });
  }
});
