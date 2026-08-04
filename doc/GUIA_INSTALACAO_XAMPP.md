# ⚙️ Guia de Instalação e Ambiente — Master School ERP

Instruções para implantação rápida e configuração do **Master School ERP** em ambientes Windows (XAMPP, WAMP, Laragon) ou Linux/Apache/PHP.

---

## 1. Requisitos de Sistema
- **PHP:** Versão 8.0 ou superior com extensões ativadas (`pdo_mysql`, `mbstring`, `json`, `session`).
- **MySQL / MariaDB:** Versão 5.7+ ou MariaDB 10.4+.
- **Servidor Web:** Apache 2.4+ com `mod_rewrite` ou Nginx configurado para roteamento PHP.

---

## 2. Instalação Padrão via XAMPP (Windows)

1. Baixe e instale o pacote oficial do [XAMPP for Windows](https://www.apachefriends.org/).
2. Abra o diretório público de documentos web:
   ```cmd
   C:\xampp\htdocs\
   ```
3. Copie ou clone a pasta do projeto:
   ```cmd
   C:\xampp\htdocs\Projeto_Escola_ERP
   ```
   *(Ou utilize uma junção NTFS com o comando: `mklink /J C:\xampp\htdocs\Projeto_Escola_ERP D:\caminho_do_projeto`)*
4. Abra o **XAMPP Control Panel** e inicie os módulos **Apache** e **MySQL**.

---

## 3. Criação do Banco de Dados via Instalador Web (1-Clique)

O projeto possui um assistente automatizado que dispensa a importação manual de arquivos `.sql`:

1. Abra seu navegador em:
   ```url
   http://localhost/Projeto_Escola_ERP/install.php
   ```
2. Clique no botão azul **"🚀 INSTALAR / RESETAR BANCO DE DADOS DA MASTER SCHOOL"**.
3. O script `install.php` se conectará ao MySQL local sem senha (`root`), criará o banco `master_school_erp`, processará o DDL de `database/schema.sql` e inserirá 100% dos dados de demonstração.

---

## 4. Configuração Manual ou Avançada (Opcional)

Caso seu servidor MySQL utilize senha ou host customizado, edite as constantes no arquivo `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'master_school_erp');
```

---

## 5. Resolução de Problemas Comuns (Troubleshooting)

- **Erro `Access denied for user 'root'@'localhost'`:** Seu MySQL local requer senha. Edite a senha em `config/database.php` ou execute `install.php` com o usuário autenticado.
- **Página não encontrada (404) ao clicar em links:** Verifique se o nome da pasta no seu XAMPP corresponde a `Projeto_Escola_ERP` (ou `master-school` caso esteja utilizando o link simbólico).
- **Estilos CSS ou JS não carregam:** O projeto utiliza caminhos relativos resolvidos pela constante `BASE_URL` em `config/config.php`. Caso o subdiretório mude, o helper se ajusta automaticamente.
