<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="logo" style="margin-bottom: 15px;">
                    <div class="logo-badge">MS</div>
                    <span>Master School</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; max-width: 300px;">
                    Excellence in Brazilian & International Education. Preparamos jovens pensadores, criadores e líderes para os maiores desafios do século XXI.
                </p>
                <div style="margin-top: 20px; display: flex; gap: 12px;">
                    <span class="badge badge-primary">Bilingue</span>
                    <span class="badge badge-accent">IB World School</span>
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
