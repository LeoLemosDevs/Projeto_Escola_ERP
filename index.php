<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = APP_NAME . ' — Colégio & Grupo Educacional (Bilingue e Alta Performance)';
$activePage = 'home';

// Busca notícias, eventos e destaques do banco de dados (se o banco já foi instalado)
$noticias = [];
try {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT * FROM noticias_eventos ORDER BY data_publicacao DESC LIMIT 6");
    $noticias = $stmt->fetchAll();
} catch (Exception $e) {
    // Se ainda não rodou o install.php, mantém array vazio
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<!-- BANNER DE DESTAQUE SUPERIOR (ESTILO COLÉGIO MASTER: "Agende uma visita. CLIQUE AQUI") -->
<section style="background: #ffffff; padding: 20px 0 0;">
    <div class="container">
        <div class="hero-promo-banner" style="background: linear-gradient(90deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%); margin-bottom: 20px;">
            <div class="promo-banner-text">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                    <span style="background: #f59e0b; color: #0f172a; font-size: 0.75rem; font-weight: 900; padding: 3px 10px; border-radius: 20px;">MATRÍCULAS 2027</span>
                    <span style="color: #60a5fa; font-weight: 700; font-size: 0.9rem;">Bolsas de Mérito Acadêmico & Concursos</span>
                </div>
                <h2>Agende uma visita guiada à Master School.</h2>
                <p>Conheça nossa infraestrutura laboratorial, proposta pedagógica bilingue e converse com nossa equipe diretiva.</p>
            </div>
            <div>
                <a href="contato.php" class="btn-promo-orange pulse-btn">
                    <span>CLIQUE AQUI E AGENDE</span>
                    <span style="font-size: 1.3rem;">→</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 1. HERO SECTION (TEMA CLARO COM FOTOS DE ALUNOS EM ALTA RESOLUÇÃO) -->
<section class="hero-light">
    <div class="container">
        <div class="hero-main-grid">
            <!-- Coluna da Esquerda: Textos de Venda & Impacto -->
            <div>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 0.82rem; margin-bottom: 18px;">
                    <span>🏆 1º LUGAR EM APROVAÇÕES E IMERSÃO BILÍNGUE</span>
                </div>
                <h1 class="hero-main-title">
                    Ensino de Padrão <span class="highlight-navy">Internacional</span>, Raízes <span class="highlight-orange">Brasileiras</span>.
                </h1>
                <p class="hero-main-subtitle">
                    O <strong>Colégio Master School</strong> une acolhimento socioemocional, robótica educacional e o mais alto rigor acadêmico para formar jovens autônomos, criativos e preparados para as maiores universidades do Brasil e do exterior.
                </p>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <a href="contato.php" class="btn-promo-orange" style="font-size: 1rem; padding: 12px 28px;">
                        Agendar Visita Guiada
                    </a>
                    <a href="#niveis" class="btn" style="background: #ffffff; color: var(--master-navy); border: 2px solid var(--master-navy); font-weight: 800; padding: 12px 26px; border-radius: 50px; text-decoration: none;">
                        Conhecer os Níveis ↓
                    </a>
                </div>

                <!-- Métricas & Badges Rápidos -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 40px; padding-top: 25px; border-top: 1px solid #cbd5e1;">
                    <div>
                        <h3 style="font-size: 1.8rem; font-weight: 900; color: var(--master-navy); margin: 0;">98%</h3>
                        <span style="font-size: 0.82rem; color: var(--text-muted-light); font-weight: 600;">Aprovação USP, UNICAMP & ITA</span>
                    </div>
                    <div>
                        <h3 style="font-size: 1.8rem; font-weight: 900; color: var(--master-orange); margin: 0;">100%</h3>
                        <span style="font-size: 0.82rem; color: var(--text-muted-light); font-weight: 600;">Imersão Bilíngue (G1 ao Médio)</span>
                    </div>
                    <div>
                        <h3 style="font-size: 1.8rem; font-weight: 900; color: var(--master-green); margin: 0;">+15</h3>
                        <span style="font-size: 0.82rem; color: var(--text-muted-light); font-weight: 600;">Ouros em Olimpíadas Científicas</span>
                    </div>
                </div>
            </div>

            <!-- Coluna da Direita: Grade Interativa com Fotos Reais de Alunos -->
            <div class="hero-photo-collage">
                <!-- Foto 1: Alunos de Ensino Médio / Vestibular -->
                <div class="hero-photo-card">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80" alt="Alunos Master School">
                    <div class="hero-photo-label">
                        <span>🎓 Terceirão & Vestibular</span>
                        <span style="color: #f59e0b;">★ Top 1%</span>
                    </div>
                </div>
                <!-- Foto 2: Alunos em Laboratório / Robótica -->
                <div class="hero-photo-card" style="margin-top: 25px;">
                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=600&q=80" alt="Robótica e Ciências">
                    <div class="hero-photo-label">
                        <span>🔬 Ciências & Robótica</span>
                        <span style="color: #60a5fa;">STEM Lab</span>
                    </div>
                </div>
                <!-- Foto 3: Educação Infantil & Fundamental -->
                <div class="hero-photo-card" style="margin-top: -25px;">
                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=600&q=80" alt="Ensino Fundamental e Infantil">
                    <div class="hero-photo-label">
                        <span>📖 Letramento Bilíngue</span>
                        <span style="color: #34d399;">G1 ao 5º Ano</span>
                    </div>
                </div>
                <!-- Foto 4: Comunidade e Esportes -->
                <div class="hero-photo-card">
                    <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=600&q=80" alt="Professores e Alunos">
                    <div class="hero-photo-label">
                        <span>👨‍🏫 Corpo Docente Mestre/Dr.</span>
                        <span style="color: #fbbf24;">Acolhimento</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. NÍVEIS DE ENSINO (SEÇÃO ESCOLAR COMPLETA INSPIRADA NO COLÉGIO MASTER) -->
<section id="niveis" class="section-light">
    <div class="container">
        <h2 class="section-title-light">Níveis de Ensino — Uma Jornada Pedagógica Completa</h2>
        <p class="section-subtitle-light">
            Da Educação Infantil ao Ensino Médio, oferecemos projetos didáticos inovadores, laboratórios práticos e acompanhamento socioemocional diário.
        </p>

        <div class="niveis-grid">
            <!-- Nível 1: Educação Infantil -->
            <div class="nivel-card">
                <div class="nivel-card-img">
                    <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=600&q=80" alt="Educação Infantil Master School">
                    <span class="nivel-tag">G1 ao G5 (1 a 5 anos)</span>
                </div>
                <div class="nivel-card-content">
                    <div>
                        <h3>🧸 Educação Infantil</h3>
                        <p>
                            Acolhimento amoroso, psicomotricidade, musicalização e imersão em língua inglesa com professores nativos e fluentes desde os primeiros anos.
                        </p>
                    </div>
                    <a href="contato.php" class="nivel-card-btn">
                        <span>Conhecer o Projeto Lúdico</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Nível 2: Ensino Fundamental I -->
            <div class="nivel-card">
                <div class="nivel-card-img">
                    <img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=600&q=80" alt="Ensino Fundamental I Master School">
                    <span class="nivel-tag">1º ao 5º Ano</span>
                </div>
                <div class="nivel-card-content">
                    <div>
                        <h3>📖 Ensino Fundamental I</h3>
                        <p>
                            Formação de hábitos de estudo, raciocínio lógico-matemático, iniciação científica e desenvolvimento ético com materiais exclusivos.
                        </p>
                    </div>
                    <a href="contato.php" class="nivel-card-btn">
                        <span>Ver Matriz Curricular</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Nível 3: Ensino Fundamental II -->
            <div class="nivel-card">
                <div class="nivel-card-img">
                    <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=600&q=80" alt="Ensino Fundamental II Master School">
                    <span class="nivel-tag">6º ao 9º Ano</span>
                </div>
                <div class="nivel-card-content">
                    <div>
                        <h3>🔬 Ensino Fundamental II</h3>
                        <p>
                            Autonomia investigativa, feiras de ciências, laboratório Maker, robótica e orientação socioemocional intensiva nas transformações da adolescência.
                        </p>
                    </div>
                    <a href="contato.php" class="nivel-card-btn">
                        <span>Conhecer Laboratórios</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Nível 4: Ensino Médio & Pré-Vestibular -->
            <div class="nivel-card">
                <div class="nivel-card-img">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80" alt="Ensino Médio e Terceirão Master">
                    <span class="nivel-tag" style="background: var(--master-navy);">1ª à 3ª Série — Terceirão</span>
                </div>
                <div class="nivel-card-content">
                    <div>
                        <h3>🎓 Ensino Médio & Terceirão</h3>
                        <p>
                            Foco total na alta performance: simulados padrão ENEM/FUVEST, turmas olímpicas, orientação vocacional e suporte de redação nota 1000.
                        </p>
                    </div>
                    <a href="contato.php" class="nivel-card-btn">
                        <span>Ver Nossos Aprovados</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. VIDA NA MASTER SCHOOL & ÚLTIMOS EVENTOS (GALERIA INTERATIVA DE ALUNOS & EVENTOS) -->
<section id="eventos" class="section-surface">
    <div class="container">
        <h2 class="section-title-light">Vida na Master School & Acontece Aqui</h2>
        <p class="section-subtitle-light">
            Muito além da sala de aula: vivencie olimpíadas científicas, eventos da comunidade escolar, campeonatos esportivos e apresentações artísticas.
        </p>

        <div class="vida-gallery-grid">
            <!-- Evento 1 -->
            <div class="vida-photo-card">
                <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=600&q=80" alt="Olimpíada de Robótica">
                <div class="vida-photo-overlay">
                    <span class="vida-badge" style="background: var(--master-orange);">STEM & Inovação</span>
                    <h4>Feira Nacional de Robótica & IA</h4>
                    <p>Alunos do Ensino Médio conquistam Ouro em competição nacional de engenharia.</p>
                </div>
            </div>

            <!-- Evento 2 -->
            <div class="vida-photo-card">
                <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=600&q=80" alt="Conexão Master">
                <div class="vida-photo-overlay">
                    <span class="vida-badge" style="background: var(--master-navy);">Comunidade Escolar</span>
                    <h4>Conexão Master — Encontro das Famílias</h4>
                    <p>Mais de 1.200 pais, alunos e docentes unidos em um sábado de workshops e integração.</p>
                </div>
            </div>

            <!-- Evento 3 -->
            <div class="vida-photo-card">
                <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=600&q=80" alt="Aprovações Master">
                <div class="vida-photo-overlay">
                    <span class="vida-badge" style="background: var(--master-green);">Mérito & Aprovados</span>
                    <h4>Cerimônia do Listão 2026</h4>
                    <p>Celebração das 140 aprovações nos cursos de Medicina, Engenharia e Direito na USP e Unicamp.</p>
                </div>
            </div>

            <!-- Evento 4 -->
            <div class="vida-photo-card">
                <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=600&q=80" alt="Campeonato Intercolegial Esportes">
                <div class="vida-photo-overlay">
                    <span class="vida-badge" style="background: var(--master-cyan);">Esporte & Liderança</span>
                    <h4>Intercolegial Master de Futsal & Basquete</h4>
                    <p>Desenvolvendo trabalho em equipe, respeito às regras e superação através do esporte.</p>
                </div>
            </div>

            <!-- Evento 5 -->
            <div class="vida-photo-card">
                <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=600&q=80" alt="Festival Cultural Master School">
                <div class="vida-photo-overlay">
                    <span class="vida-badge" style="background: #a855f7;">Arte & Expressão</span>
                    <h4>Festival Anual de Música, Teatro & Poesia</h4>
                    <p>Nossos alunos no palco do auditório apresentando clássicos da literatura brasileira.</p>
                </div>
            </div>

            <!-- Evento 6 -->
            <div class="vida-photo-card">
                <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=600&q=80" alt="Laboratório de Pesquisa">
                <div class="vida-photo-overlay">
                    <span class="vida-badge" style="background: #ec4899;">Ciência & Biologia</span>
                    <h4>Iniciação Científica — Laboratório de Química</h4>
                    <p>Práticas semanais laboratoriais para todas as turmas do Ensino Fundamental II e Médio.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. MURAL DE NOTÍCIAS & COMUNICADOS EM TEMPO REAL (ERP BD INTEGRADO) -->
<section id="mural" class="section-light">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
            <div>
                <span class="badge" style="background: #eff6ff; color: var(--master-navy); border: 1px solid #bfdbfe; font-weight: 800; margin-bottom: 8px;">INTEGRADO AO ERP</span>
                <h2 style="font-size: 2.3rem; font-weight: 900; color: var(--text-dark); margin: 0;">Mural Institucional & Comunicados</h2>
            </div>
            <div>
                <a href="<?= base_url('login.php') ?>" class="btn" style="background: var(--master-navy); color: white; border-radius: 50px; font-weight: 700;">
                    Acessar Portal para Todos os Eventos →
                </a>
            </div>
        </div>

        <?php if (empty($noticias)): ?>
            <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 20px; padding: 40px; text-align: center;">
                <h4 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 8px;">Nenhum comunicado publicado no momento</h4>
                <p style="color: var(--text-muted-light);">Acesse o painel administrativo para publicar novas notícias no mural da escola.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                <?php foreach ($noticias as $noticia): ?>
                    <?php
                        $tipoNoticia = !empty($noticia['categoria']) ? strtolower($noticia['categoria']) : (!empty($noticia['tipo']) ? strtolower($noticia['tipo']) : 'geral');
                        $catColor    = ($tipoNoticia === 'urgente' || $tipoNoticia === 'alerta') ? '#ef4444' : (($tipoNoticia === 'evento' || $tipoNoticia === 'academico') ? '#3b82f6' : '#10b981');
                        $publico     = !empty($noticia['publico_alvo']) ? $noticia['publico_alvo'] : ($tipoNoticia === 'evento' ? 'Comunidade & Pais' : 'Alunos & Docentes');
                    ?>
                    <div style="background: #ffffff; border-radius: 20px; padding: 26px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <span style="background: <?= $catColor ?>15; color: <?= $catColor ?>; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                    <?= htmlspecialchars($tipoNoticia) ?>
                                </span>
                                <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">
                                    <?= !empty($noticia['data_publicacao']) ? date('d/m/Y', strtotime($noticia['data_publicacao'])) : date('d/m/Y') ?>
                                </span>
                            </div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; line-height: 1.35;">
                                <?= htmlspecialchars($noticia['titulo'] ?? 'Comunicado Institucional') ?>
                            </h3>
                            <p style="color: #475569; font-size: 0.93rem; line-height: 1.6; margin-bottom: 20px;">
                                <?= nl2br(htmlspecialchars(substr($noticia['conteudo'] ?? ($noticia['resumo'] ?? ''), 0, 160))) ?>...
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #1e3a8a;">
                                🎯 Público: <?= htmlspecialchars($publico) ?>
                            </span>
                            <a href="<?= base_url('login.php') ?>" style="color: var(--master-orange); font-weight: 800; text-decoration: none; font-size: 0.85rem;">
                                Ler mais →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- 5. CALL TO ACTION DE VISITA GUIADA & AGENDAMENTO (EMPOLGANTE E PRONTO PARA VENDA) -->
<section style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); padding: 80px 0; color: #ffffff; text-align: center; position: relative; overflow: hidden; border-top: 4px solid var(--master-orange);">
    <div class="container" style="position: relative; z-index: 2; max-width: 800px;">
        <span style="background: rgba(249, 115, 22, 0.2); color: #fb923c; padding: 6px 16px; border-radius: 50px; font-weight: 800; font-size: 0.85rem; border: 1px solid rgba(249, 115, 22, 0.4); display: inline-block; margin-bottom: 20px;">
            ✨ O FUTURO DO SEU FILHO COMEÇA HOJE
        </span>
        <h2 style="font-size: 2.8rem; font-weight: 900; margin-bottom: 16px; color: #ffffff;">
            Agende uma visita e encante-se com a Master School.
        </h2>
        <p style="font-size: 1.15rem; color: #cbd5e1; margin-bottom: 35px; line-height: 1.7;">
            Venha conversar com nossa coordenação pedagógica, conhecer os laboratórios STEM e descobrir por que lideramos os índices de aprovação nas melhores universidades.
        </p>
        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <a href="contato.php" class="btn-promo-orange pulse-btn" style="font-size: 1.15rem; padding: 16px 38px;">
                <span>AGENDAR VISITA AGORA</span>
                <span>→</span>
            </a>
            <a href="<?= base_url('login.php') ?>" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; border: 2px solid rgba(255, 255, 255, 0.3); font-weight: 800; font-size: 1.05rem; padding: 14px 32px; border-radius: 50px; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                Acessar Portal EDU 🔒
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
