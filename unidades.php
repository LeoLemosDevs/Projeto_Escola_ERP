<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Unidades & Campi — ' . APP_NAME;
$activePage = 'unidades';
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top: 60px;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
            <span class="badge badge-primary" style="margin-bottom: 15px;">Infraestrutura Premium</span>
            <h1 style="font-size: 3rem; margin-bottom: 20px;">Nossas Unidades</h1>
            <p style="color: var(--text-muted); font-size: 1.15rem;">
                Espaços projetados com laboratórios STEAM, centros esportivos e auditórios multimodais em locais estratégicos.
            </p>
        </div>

        <div class="grid-3">
            <!-- UNIDADE 1 -->
            <div class="glass-card">
                <span class="badge badge-primary" style="margin-bottom: 12px;">São Paulo — Centro</span>
                <h3 style="font-size: 1.6rem; margin-bottom: 10px;">Campus Paulista (Ala Norte)</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 16px;">
                    Ensino Fundamental II e Ensino Médio Internacional (IB World School).
                </p>
                <div style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;">
                    📍 Av. Paulista, 1500 — Bela Vista, São Paulo/SP<br>
                    📞 (11) 3333-1000<br>
                    🕒 Seg a Sex — 07h00 às 19h00
                </div>
                <a href="contato.php" class="btn btn-outline" style="width: 100%;">Agendar Visita Guiada</a>
            </div>

            <!-- UNIDADE 2 -->
            <div class="glass-card">
                <span class="badge badge-accent" style="margin-bottom: 12px;">São Paulo — Sul</span>
                <h3 style="font-size: 1.6rem; margin-bottom: 10px;">Campus Morumbi (Ala Sul)</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 16px;">
                    Educação Infantil Bilíngue, Ensino Fundamental I e Complexo Poliesportivo.
                </p>
                <div style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;">
                    📍 Av. Giovanni Gronchi, 4500 — Morumbi, São Paulo/SP<br>
                    📞 (11) 3333-2000<br>
                    🕒 Seg a Sex — 07h00 às 18h30
                </div>
                <a href="contato.php" class="btn btn-outline" style="width: 100%;">Agendar Visita Guiada</a>
            </div>

            <!-- UNIDADE 3 -->
            <div class="glass-card">
                <span class="badge badge-primary" style="margin-bottom: 12px;">Grande São Paulo</span>
                <h3 style="font-size: 1.6rem; margin-bottom: 10px;">Campus Alphaville</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 16px;">
                    Campus Eco-tecnológico com fazenda solar e laboratórios de robótica aplicada.
                </p>
                <div style="color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;">
                    📍 Al. Rio Negro, 800 — Alphaville, Barueri/SP<br>
                    📞 (11) 3333-3000<br>
                    🕒 Seg a Sex — 07h00 às 18h30
                </div>
                <a href="contato.php" class="btn btn-outline" style="width: 100%;">Agendar Visita Guiada</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
