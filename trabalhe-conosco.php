<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Trabalhe Conosco — ' . APP_NAME;
$activePage = 'trabalhe-conosco';
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 60px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
            <span class="badge badge-accent" style="margin-bottom: 15px;">Carreira Docente & Administrativa</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Trabalhe Conosco</h1>
            <p style="color: var(--text-muted); font-size: 1.15rem;">
                Buscamos educadores apaixonados por inovação, pesquisadores, coordenadores bilíngues e especialistas em tecnologia educacional.
            </p>
        </div>

        <div class="grid-2" style="align-items: flex-start;">
            <!-- VAGAS EM DESTAQUE -->
            <div class="glass-card">
                <h3 style="font-size: 1.6rem; margin-bottom: 24px; color: #60a5fa;">Vagas Abertas (2026/2027)</h3>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
                        <span class="badge badge-primary">Docente — IB</span>
                        <h4 style="font-size: 1.2rem; margin: 8px 0 4px;">Professor(a) de Física (Inglês/Português)</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Campus Paulista — Regime Integral — Requer fluência em Inglês e mestrado/doutorado na área.</p>
                    </div>

                    <div style="border-bottom: 1px solid var(--glass-border); padding-bottom: 16px;">
                        <span class="badge badge-accent">Tecnologia</span>
                        <h4 style="font-size: 1.2rem; margin: 8px 0 4px;">Analista de Suporte ERP & EdTech</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Campus Morumbi — Híbrido — Conhecimento em PHP, MySQL (XAMPP) e ferramentas educacionais.</p>
                    </div>

                    <div>
                        <span class="badge badge-primary">Coordenação</span>
                        <h4 style="font-size: 1.2rem; margin: 8px 0 4px;">Coordenador(a) Pedagógico Bilíngue</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Campus Alphaville — Regime Integral — Experiência em currículo IB e liderança educacional.</p>
                    </div>
                </div>
            </div>

            <!-- FORMULÁRIO DE CANDIDATURA -->
            <div class="glass-card">
                <h3 style="font-size: 1.6rem; margin-bottom: 16px;">Envie seu Currículo</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">
                    Preencha o formulário abaixo e faremos contato caso seu perfil se alinhe com nosso plano de carreira.
                </p>

                <form onsubmit="event.preventDefault(); showToast('Currículo recebido com sucesso! Entraremos em contato.', 'success'); this.reset();">
                    <div class="form-group">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" class="form-input" placeholder="Seu nome completo..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-mail Profissional</label>
                        <input type="email" class="form-input" placeholder="seu.email@exemplo.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Área de Interesse</label>
                        <select class="form-select" required>
                            <option value="">Selecione uma área...</option>
                            <option value="docencia">Docência / Professor(a)</option>
                            <option value="coordenacao">Coordenação Pedagógica</option>
                            <option value="ti">Tecnologia (ERP / EdTech)</option>
                            <option value="admin">Administrativo / Secretaria</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Link para Currículo Lattes ou LinkedIn</label>
                        <input type="url" class="form-input" placeholder="https://linkedin.com/in/..." required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Enviar Candidatura ➤</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
