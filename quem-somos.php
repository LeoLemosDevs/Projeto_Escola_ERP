<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Quem Somos — ' . APP_NAME;
$activePage = 'quem-somos';
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 60px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
            <span class="badge badge-primary" style="margin-bottom: 15px;">Institucional</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Quem Somos</h1>
            <p style="color: var(--text-muted); font-size: 1.15rem; line-height: 1.8;">
                A <strong>Master School</strong> é uma instituição educacional brasileira de vocação global. Nosso nome em inglês reflete o compromisso autêntico com a cidadania mundial e fluência idiomática sem perder o DNA criativo do Brasil.
            </p>
        </div>

        <div class="grid-2" style="align-items: center; margin-bottom: 80px;">
            <div class="glass-card">
                <h3 style="font-size: 1.8rem; margin-bottom: 16px; color: #60a5fa;">Nossa História & Propósito</h3>
                <p style="color: var(--text-muted); margin-bottom: 16px; line-height: 1.7;">
                    Fundada em São Paulo, a escola surgiu da necessidade de um ensino que não impusesse a escolha entre passar nos vestibulares nacionais (USP, UNICAMP, ENEM) e ser admitido em grandes universidades internacionais (Ivy League, Oxford, Cambridge).
                </p>
                <p style="color: var(--text-muted); line-height: 1.7;">
                    Com uma metodologia inovadora que combina experimentação STEAM (Ciência, Tecnologia, Engenharia, Artes e Matemática) com humanismo literário, formamos alunos seguros, éticos e preparados para o futuro.
                </p>
            </div>

            <div class="glass-card" style="background: rgba(30, 58, 138, 0.25); border-color: rgba(96, 165, 250, 0.3);">
                <h3 style="font-size: 1.8rem; margin-bottom: 20px; color: #fbbf24;">Os Pilares Master School</h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 16px;">
                    <li style="display: flex; gap: 12px;">
                        <span style="color: #60a5fa; font-size: 1.2rem;">★</span>
                        <div>
                            <strong>Bilinguismo de Imersão:</strong>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Aulas em inglês e português com professores nativos ou com mestrado no exterior.</p>
                        </div>
                    </li>
                    <li style="display: flex; gap: 12px;">
                        <span style="color: #60a5fa; font-size: 1.2rem;">★</span>
                        <div>
                            <strong>Tecnologia Integrada:</strong>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Uso diário do ERP Master School para boletins digitais, frequência inteligente e financeiro ágil.</p>
                        </div>
                    </li>
                    <li style="display: flex; gap: 12px;">
                        <span style="color: #60a5fa; font-size: 1.2rem;">★</span>
                        <div>
                            <strong>Identidade e Cidadania Brasileira:</strong>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Valorização de nossa história, ecossistemas e diversidade cultural.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
