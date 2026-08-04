<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Contato & Ouvidoria — ' . APP_NAME;
$activePage = 'contato';
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 60px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
            <span class="badge badge-primary" style="margin-bottom: 15px;">Fale Conosco</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Atendimento e Matrículas</h1>
            <p style="color: var(--text-muted); font-size: 1.15rem;">
                Nossa equipe de secretaria, coordenação pedagógica e suporte técnico está pronta para tirar suas dúvidas.
            </p>
        </div>

        <div class="grid-2" style="align-items: flex-start; margin-bottom: 80px;">
            <!-- CANAIS DE ATENDIMENTO -->
            <div>
                <div class="glass-card" style="margin-bottom: 30px;">
                    <h3 style="font-size: 1.6rem; margin-bottom: 20px; color: #60a5fa;">Canais Diretos</h3>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 16px; color: var(--text-muted);">
                        <li>
                            <strong style="color: var(--text-main);">📞 Central de Matrículas:</strong><br>
                            (11) 3333-0000 | Segunda a Sexta, 08h às 18h
                        </li>
                        <li>
                            <strong style="color: var(--text-main);">💬 WhatsApp Oficial:</strong><br>
                            (11) 98888-0000
                        </li>
                        <li>
                            <strong style="color: var(--text-main);">✉️ Secretaria Acadêmica:</strong><br>
                            secretaria@masterschool.edu.br
                        </li>
                        <li>
                            <strong style="color: var(--text-main);">✉️ Suporte ERP & Login:</strong><br>
                            suporte.ti@masterschool.edu.br
                        </li>
                    </ul>
                </div>

                <div class="glass-card" style="background: rgba(30, 58, 138, 0.25);">
                    <h4 style="font-size: 1.2rem; margin-bottom: 12px; color: #fbbf24;">📍 Principal Campus Paulista</h4>
                    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                        Av. Paulista, 1500 — Bela Vista — São Paulo, SP<br>
                        Estacionamento próprio com acesso pela Rua São Carlos do Pinhal.
                    </p>
                </div>
            </div>

            <!-- FORMULÁRIO DE CONTATO -->
            <div class="glass-card">
                <h3 style="font-size: 1.6rem; margin-bottom: 16px;">Envie uma Mensagem</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">
                    Retornaremos em até 24 horas úteis.
                </p>

                <form onsubmit="event.preventDefault(); showToast('Mensagem enviada com sucesso! Protocolo: MS-' + Math.floor(1000 + Math.random()*9000), 'success'); this.reset();">
                    <div class="form-group">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" class="form-input" placeholder="Seu nome..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-mail para Contato</label>
                        <input type="email" class="form-input" placeholder="email@exemplo.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="tel" class="form-input" placeholder="(11) 99999-9999" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Assunto</label>
                        <select class="form-select" required>
                            <option value="">Selecione um assunto...</option>
                            <option value="matricula">Novas Matrículas & Bolsas</option>
                            <option value="duvidas">Dúvidas Acadêmicas</option>
                            <option value="financeiro">Financeiro / PagSeguro</option>
                            <option value="suporte">Ajuda com Login / Senha do ERP</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sua Mensagem</label>
                        <textarea class="form-textarea" rows="4" placeholder="Escreva aqui..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Enviar Mensagem ➤</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
