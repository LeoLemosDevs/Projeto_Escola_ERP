<footer class="footer" style="background: #0f172a; border-top: 4px solid var(--master-orange);">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div style="margin-bottom: 20px;">
                    <a href="<?= base_url('index.php') ?>" class="master-logo">
                        <div class="master-logo-text">
                            <span class="master-logo-subtitle" style="color: #94a3b8;">COLÉGIO & GRUPO</span>
                            <span class="master-logo-title"><span style="color: #ffffff;">master</span> <span class="text-school">school</span></span>
                        </div>
                        <div class="master-logo-shapes">
                            <div class="shape-square"></div>
                            <div class="shape-arch"></div>
                            <div class="shape-circle"></div>
                            <div class="shape-star"></div>
                        </div>
                    </a>
                </div>
                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6; max-width: 320px;">
                    Educação Bilíngue de Alta Performance e Projeto de Vida. Preparamos jovens pensadores, criadores e líderes para as maiores universidades do Brasil e do exterior.
                </p>
                <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <span style="background: rgba(249,115,22,0.2); color: #fb923c; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; border: 1px solid rgba(249,115,22,0.3);">⚡ Conexão Master</span>
                    <span style="background: rgba(37,99,235,0.2); color: #60a5fa; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; border: 1px solid rgba(37,99,235,0.3);">🌐 IB World School</span>
                </div>
            </div>

            <div class="footer-col">
                <h4>Navegação</h4>
                <ul class="footer-links">
                    <li><a href="quem-somos.php">Quem Somos</a></li>
                    <li><a href="missao-visao-valores.php">Missão, Visão e Valores</a></li>
                    <li><a href="unidades.php">Unidades e Campi</a></li>
                    <li><a href="professores.php">Corpo Docente</a></li>
                    <li><a href="trabalhe-conosco.php">Trabalhe Conosco</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Acesso Rápido</h4>
                <ul class="footer-links">
                    <li><a href="login.php">Portal do Aluno</a></li>
                    <li><a href="login.php">Portal do Professor</a></li>
                    <li><a href="login.php">Painel Administrativo</a></li>
                    <li><a href="contato.php">Ouvidoria & Contato</a></li>
                    <li><a href="install.php">Instalador BD (XAMPP)</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Newsletter Institucional</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 12px;">
                    Receba notícias, calendário de feriados e convites para eventos abertos da escola.
                </p>
                <form onsubmit="event.preventDefault(); showToast('Inscrição confirmada na Newsletter Master School!', 'success');" style="display: flex; gap: 8px;">
                    <input type="email" placeholder="Seu e-mail..." class="form-input" required style="padding: 10px 14px; font-size: 0.85rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">➤</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Master School ERP. Todos os direitos reservados. Sistema Escolar Full Stack com XAMPP & PagSeguro.</p>
        </div>
    </div>
</footer>

</body>
</html>
