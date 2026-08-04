        </main>

        <footer style="padding: 20px 30px; border-top: 1px solid var(--glass-border); color: #64748b; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; background: rgba(15, 23, 42, 0.4);">
            <div>&copy; <?= date('Y') ?> Master School ERP — Módulo <?= ucfirst(get_user_role() ?? '') ?></div>
            <div>Desenvolvido com PHP 8, MySQL (XAMPP) & PagSeguro API</div>
        </footer>
    </div>
</div>

</body>
</html>
