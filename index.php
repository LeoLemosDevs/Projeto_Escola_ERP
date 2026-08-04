<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = APP_NAME . ' — Portal Institucional & ERP';
$activePage = 'home';

// Busca notícias, eventos e destaques do banco de dados (se o banco já foi instalado)
$noticias = [];
try {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT * FROM noticias_eventos ORDER BY data_publicacao DESC LIMIT 6");
    $noticias = $stmt->fetchAll();
} catch (Exception $e) {
    // Se ainda não rodou o install.php, exibe mensagem ou vazia
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- 1. HERO SECTION -->
<section class="hero">
    <div class="hero-bg-glow"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span>✦</span> MATRÍCULAS ABERTAS PARA 2027 — BOLSAS DE MÉRITO
            </div>
            <h1 class="hero-title">
                Ensino Padrão Internacional, Raízes <span class="text-gradient">Brasileiras</span>.
            </h1>
            <p class="hero-subtitle">
                Na <strong>Master School</strong>, combinamos rigor acadêmico bilíngue, tecnologia educacional e formação humana para preparar seus filhos para o mundo e para as maiores universidades.
            </p>
            <div class="hero-actions">
                <a href="contato.php" class="btn btn-primary">Agendar Visita Guiada</a>
                <a href="#mural" class="btn btn-outline">Explorar Mural & Eventos ↓</a>
            </div>

            <!-- Métricas Rápida -->
            <div style="display: flex; gap: 30px; margin-top: 50px; border-top: 1px solid var(--glass-border); padding-top: 25px;">
                <div>
                    <h3 style="font-size: 1.8rem; color: #60a5fa;">98%</h3>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Aprovação USP/UNICAMP/IB</span>
                </div>
                <div>
                    <h3 style="font-size: 1.8rem; color: #fbbf24;">100%</h3>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Imersão Bilíngue (Inglês)</span>
                </div>
                <div>
                    <h3 style="font-size: 1.8rem; color: #34d399;">+15</h3>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Anos de Excelência no Brasil</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. MURAL DE NOTÍCIAS, EVENTOS E FÉRIAS -->
<section class="section" id="mural">
    <div class="container">
        <h2 class="section-title">Mural Institucional & Eventos</h2>
        <p class="section-subtitle">
            Acompanhe o calendário escolar, informativos de férias, destaques acadêmicos e novidades da Master School.
        </p>

        <!-- Filtros Interativos -->
        <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-bottom: 40px;">
            <button class="btn btn-outline news-filter-btn active" data-filter="todos" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">Todos os Avisos</button>
            <button class="btn btn-outline news-filter-btn" data-filter="evento" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">📅 Eventos</button>
            <button class="btn btn-outline news-filter-btn" data-filter="ferias" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">🏖️ Férias & Recessos</button>
            <button class="btn btn-outline news-filter-btn" data-filter="destaque_aluno" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">🏆 Alunos Destaques</button>
            <button class="btn btn-outline news-filter-btn" data-filter="noticia" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">📰 Notícias</button>
        </div>

        <?php if (empty($noticias)): ?>
            <div class="glass-card" style="text-align: center; padding: 40px;">
                <p>Nenhuma publicação encontrada no banco de dados. Clique em <a href="install.php" style="color: #60a5fa; text-decoration: underline;">Instalar / Resetar Banco</a> para carregar os dados de demonstração.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($noticias as $item): ?>
                    <?php
                        $badgeClass = 'badge-primary';
                        $catName = 'Notícia';
                        if ($item['tipo'] === 'evento') { $badgeClass = 'badge-accent'; $catName = 'Evento'; }
                        if ($item['tipo'] === 'ferias') { $badgeClass = 'badge-success'; $catName = 'Férias'; }
                        if ($item['tipo'] === 'destaque_aluno') { $badgeClass = 'badge-primary'; $catName = 'Destaque Aluno'; }
                    ?>
                    <article class="news-card news-item" data-category="<?= htmlspecialchars($item['tipo']) ?>">
                        <?php if (!empty($item['imagem_url'])): ?>
                            <img src="<?= htmlspecialchars($item['imagem_url']) ?>" alt="Imagem da publicação" class="news-img" loading="lazy">
                        <?php endif; ?>
                        <div class="news-body">
                            <div class="news-meta">
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($catName) ?></span>
                                <span><?= format_date($item['data_publicacao']) ?></span>
                            </div>
                            <h3 class="news-title"><?= htmlspecialchars($item['titulo']) ?></h3>
                            <p class="news-summary"><?= htmlspecialchars($item['resumo']) ?></p>
                            <a href="javascript:void(0)" onclick="showToast('Abrindo artigo completo: <?= htmlspecialchars(addslashes($item['titulo'])) ?>', 'info')" style="color: #60a5fa; font-weight: 600; font-size: 0.9rem; margin-top: auto;">
                                Ler Mais &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- 3. DESTAQUE INSTITUCIONAL: POR QUE A MASTER SCHOOL? -->
<section class="section" style="background: rgba(15, 23, 42, 0.4); border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border);">
    <div class="container">
        <div class="grid-2" style="align-items: center;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 16px;">O Nome em Inglês, O Orgulho Brasileiro</span>
                <h2 style="font-size: 2.3rem; margin-bottom: 20px;">
                    Excelência Internacional Aprovada Pelo MEC & IB.
                </h2>
                <p style="color: var(--text-muted); margin-bottom: 24px; line-height: 1.7;">
                    A <strong>Master School</strong> nasceu para unir a fluência nativa e metodologia de investigação científica americana/europeia ao calor, criatividade e currículo nacional brasileiro.
                </p>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #60a5fa; font-size: 1.2rem;">✔</span>
                        <div>
                            <strong style="color: var(--text-main);">Diploma Duplo (IB Diploma + Ensino Médio Brasileiro)</strong>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Acesso direto a universidades nos EUA, Europa e as grandes federais brasileiras.</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="color: #60a5fa; font-size: 1.2rem;">✔</span>
                        <div>
                            <strong style="color: var(--text-main);">Tecnologia & Gestão 100% Conectadas</strong>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Pais, alunos e professores interagem em nosso ERP educacional de última geração.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card" style="position: relative; overflow: hidden; border-color: rgba(96, 165, 250, 0.3);">
                <div style="text-align: center; padding: 20px 0;">
                    <h3 style="margin-bottom: 8px; color: #60a5fa;">Acesse o Portal Acadêmico</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
                        Ambiente digital seguro para consulta de notas, chamadas, boletos e pagamentos via PagSeguro.
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="login.php" class="btn btn-primary" style="width: 100%;">Entrar no Painel do Aluno</a>
                        <a href="login.php" class="btn btn-outline" style="width: 100%;">Área do Professor & Gestão</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. BANNER DE CHAMADA PARA MATRÍCULA -->
<section class="section">
    <div class="container">
        <div class="glass-card" style="background: linear-gradient(135deg, rgba(30, 58, 138, 0.7) 0%, rgba(15, 23, 42, 0.9) 100%); text-align: center; padding: 60px 40px; border-color: rgba(96, 165, 250, 0.5);">
            <span class="badge badge-accent" style="margin-bottom: 20px;">Matrículas 2027</span>
            <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Faça Parte da Nossa Comunidade</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 30px; font-size: 1.1rem;">
                Agende um teste de nivelamento ou uma visita ao nosso campus em São Paulo e conheça o projeto educacional que transforma futuros.
            </p>
            <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
                <a href="contato.php" class="btn btn-accent">Fale com a Secretaria</a>
                <a href="unidades.php" class="btn btn-outline">Conhecer Nossas Unidades</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
