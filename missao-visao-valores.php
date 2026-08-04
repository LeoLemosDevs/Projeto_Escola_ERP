<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Missão, Visão e Valores — ' . APP_NAME;
$activePage = 'mvv';
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 60px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
            <span class="badge badge-accent" style="margin-bottom: 15px;">Filosofia Acadêmica</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Missão, Visão e Valores</h1>
            <p style="color: var(--text-muted); font-size: 1.15rem;">
                Norteamos cada aula, evento e projeto escolar por compromissos éticos inegociáveis.
            </p>
        </div>

        <div class="grid-3" style="margin-bottom: 60px;">
            <!-- CARD 1: MISSÃO -->
            <div class="glass-card" style="text-align: center; border-color: rgba(96, 165, 250, 0.4);">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                    🎯
                </div>
                <h3 style="font-size: 1.6rem; margin-bottom: 16px;">Nossa Missão</h3>
                <p style="color: var(--text-muted); line-height: 1.7;">
                    Proporcionar educação de excelência com padrão internacional, capacitando jovens brasileiros com pensamento crítico, criatividade bilíngue e sensibilidade humana para impactar positivamente a sociedade global.
                </p>
            </div>

            <!-- CARD 2: VISÃO -->
            <div class="glass-card" style="text-align: center; border-color: rgba(245, 158, 11, 0.4);">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(245, 158, 11, 0.2); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                    👁️
                </div>
                <h3 style="font-size: 1.6rem; margin-bottom: 16px;">Nossa Visão</h3>
                <p style="color: var(--text-muted); line-height: 1.7;">
                    Ser reconhecida como a instituição educacional mais inovadora do Brasil, referência na aprovação em universidades de elite nacionais e estrangeiras e no desenvolvimento integral do estudante.
                </p>
            </div>

            <!-- CARD 3: VALORES -->
            <div class="glass-card" style="text-align: center; border-color: rgba(16, 185, 129, 0.4);">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); color: #34d399; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                    💎
                </div>
                <h3 style="font-size: 1.6rem; margin-bottom: 16px;">Nossos Valores</h3>
                <ul style="text-align: left; list-style: none; display: flex; flex-direction: column; gap: 8px; color: var(--text-muted); font-size: 0.95rem;">
                    <li>✔ <strong>Ética e Integridade:</strong> Transparência total com famílias.</li>
                    <li>✔ <strong>Inovação Contínua:</strong> Uso do ERP digital e métodos ativos.</li>
                    <li>✔ <strong>Empatia e Diversidade:</strong> Respeito e inclusão em cada atitude.</li>
                    <li>✔ <strong>Excelência Acadêmica:</strong> Superação e mérito intelectual.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
