# 💾 Backup de Segurança & Recuperação Rápida — Master School ERP (v2.0.0)

> **Resumo:** Esta pasta contém os arquivos de backup estático e estrutural do banco de dados e do instalador para garantia de continuidade operacional e portfólio.

---

## 📦 Conteúdo do Diretório de Backup

1. **`master_school_erp_schema_backup.sql`**:  
   Cópia de segurança integral do script DDL e seed inicial das **10 tabelas relacionais em 3NF** (`usuarios`, `alunos`, `professores`, `turmas`, `disciplinas`, `notas`, `frequencias`, `mensalidades`, `noticias_eventos`, `system_logs`).
   
2. **`install_backup.php`**:  
   Cópia de segurança do instalador automatizado web 1-Clique responsável por criar o banco e preencher 500 alunos, 10 professores e 20 turmas com senhas criptografadas (`Bcrypt`).

---

## 🔄 Como Restaurar o Sistema a Partir Deste Backup

### Opção 1: Via Instalador Web (Recomendado)
Basta acessar no seu navegador local:  
👉 [`http://localhost/Projeto_Escola_ERP/install.php`](http://localhost/Projeto_Escola_ERP/install.php)  
Clique no botão verde **"Executar Instalação / Reset Completo do Banco"** para reconfigurar todo o ecossistema em 2 segundos.

### Opção 2: Importação Direta pelo phpMyAdmin / MySQL CLI
1. Abra o seu servidor MySQL ou **phpMyAdmin** ([`http://localhost/phpmyadmin`](http://localhost/phpmyadmin)).
2. Crie um banco de dados vazio chamado `master_school_erp` com collation `utf8mb4_unicode_ci`.
3. Importe o arquivo `master_school_erp_schema_backup.sql` localizado nesta pasta.
4. Para gerar os usuários e senhas hash dinâmicos, execute o script `install.php` uma vez.

---

## 🏷️ Versão do Backup
* **Release:** `v2.0.0 — Gold Portfolio Release`
* **Data do Snapshot:** Agosto de 2026
* **Repositório GitHub:** [https://github.com/LeoLemosDevs/Projeto_Escola_ERP](https://github.com/LeoLemosDevs/Projeto_Escola_ERP)
