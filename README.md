# 🎓 Master School — Projeto ERP Educacional & Portal Web

**Master School** é uma plataforma educacional de excelência para ensino médio, bilíngue, projetada no Brasil. Este repositório contém o **Portal Institucional Público** e o **Sistema ERP Completo** com 3 perfis de acesso: **Estudante (Aluno)**, **Professor(a)** e **Administração Geral**.

---

## ✨ Destaques de Design & Arquitetura

- 🎨 **Design System Premium (HSL & Glassmorphism):** Interface contemporânea em tons marinhos profundo, azul vibrante e acentos dourados, com cards com efeito vidro fosco (`backdrop-filter: blur`), tipografia **Outfit / Inter** e animações dinâmicas.
- 📱 **100% Responsivo & Mobile-Ready:** Menu de navegação animado com gaveta lateral em dispositivos móveis, tabelas auto-ajustáveis e modais de checkout responsivos.
- 🛡️ **Segurança em Camadas (PDO & RBAC):** Consultas preparadas do PDO contra Injeção SQL, sanitização de saídas (`htmlspecialchars`), senhas protegidas com `bcrypt` e controle de permissões baseado em perfil (`require_role`).
- 💳 **Módulo Financeiro Integrado (Simulador PagSeguro):** Checkout modal interativo permitindo quitação imediata via **PIX (QR Code e Copia-e-Cola)**, **Boleto Bancário (Linha Digitável)** ou **Cartão de Crédito**.
- 📈 **Executive Analytics (Chart.js):** Painel do administrador com gráficos de barras (desempenho de notas por disciplina) e rosca (receita liquidada, pendente e em atraso).
- 🛡️ **Trilha de Auditoria e Logs:** Histórico de eventos gravado na tabela `system_logs` contendo IP, usuário, ação e carimbo de tempo.

---

## 🛠️ Stack Tecnológica

- **Backend:** PHP 8 (Orientado a objetos, Modular, com sessões limpas)
- **Banco de Dados:** MySQL / MariaDB (Ambiente padrão **XAMPP**)
- **Estilização:** Vanilla CSS Moderno (`assets/css/design-system.css`, `assets/css/style.css`, `assets/css/dashboard.css`)
- **Interatividade Frontend:** Vanilla JavaScript ES6+ & Chart.js

---

## 🚀 Como Instalar e Executar no XAMPP (1-Clique)

1. **Instale o XAMPP** (ou similar como WAMP/Laragon) com Apache e MySQL.
2. Copie ou clone a pasta deste projeto para o diretório de publicação web:
   ```bash
   C:\xampp\htdocs\Projeto_Escola_ERP
   ```
3. Abra o **Painel de Controle do XAMPP** e inicie os serviços **Apache** e **MySQL**.
4. Acesse o **Instalador Web 1-Clique** pelo navegador:
   ```url
   http://localhost/Projeto_Escola_ERP/install.php
   ```
5. Clique no botão azul **"🚀 INSTALAR / RESETAR BANCO DE DADOS DA MASTER SCHOOL"**.
   - O script criará o banco `master_school_erp`, construirá todas as 10 tabelas relacionais com chaves estrangeiras e preencherá a escola com professores, alunos, turmas, notas, chamada e notícias de demonstração.

---

## 🔑 Credenciais de Acesso ao ERP (Modo Demo)

Você pode testar qualquer um dos 3 perfis pela tela de login (`http://localhost/Projeto_Escola_ERP/login.php`) utilizando os **botões de preenchimento rápido 1-clique** ou com as seguintes credenciais:

| Perfil | E-mail de Acesso | Senha Padrão | Painel Inicial |
| :--- | :--- | :--- | :--- |
| 🎓 **Aluno** | `lucas.mendes@aluno.masterschool.edu.br` | `aluno123` | `/aluno/index.php` |
| 👨‍🏫 **Professor** | `carlos.silva@masterschool.edu.br` | `prof123` | `/professor/index.php` |
| ⚙️ **Admin** | `admin@masterschool.edu.br` | `admin123` | `/admin/index.php` |

---

## 📂 Estrutura Modular de Diretórios

```
Projeto_Escola_ERP/
├── config/
│   ├── config.php            # Configurações globais e helpers (APP_NAME, rotas, logs)
│   └── database.php          # Conexão PDO segura ao MySQL
├── database/
│   └── schema.sql            # Script SQL DDL + DML completo (10 tabelas)
├── assets/
│   ├── css/
│   │   ├── design-system.css # Tokens CSS HSL, Glassmorphism, modo escuro/claro
│   │   ├── style.css         # Layout do portal público institucional
│   │   └── dashboard.css     # Estilos dos 3 painéis (aluno, prof, admin)
│   └── js/
│       ├── main.js           # Menu animado, modais e feedback toast
│       └── payment.js        # Checkout interativo do PagSeguro (PIX, Boleto, Cartão)
├── includes/
│   ├── header.php            # Cabeçalho global público
│   ├── footer.php            # Rodapé global público
│   ├── auth_check.php        # Middleware de proteção RBAC (require_role)
│   ├── dashboard_header.php  # Sidebar e Topbar do ERP
│   └── dashboard_footer.php  # Fechamento do layout do ERP
├── admin/                    # Módulo Administrativo (Chart.js, CRUD Alunos, Profs, Turmas, Notícias, Logs)
├── professor/                # Módulo Docente (Chamada em lote, Notas bimestrais 1-4, Grade)
├── aluno/                    # Módulo Estudante (Boletim de notas e frequência, Perfil, PagSeguro)
├── index.php                 # Página Principal (Hero animado, mural de notícias, carrossel de alunos)
├── quem-somos.php            # História da instituição
├── missao-visao-valores.php  # Cards animados institucionais
├── unidades.php              # Campi e laboratórios
├── trabalhe-conosco.php      # Portal de vagas
├── professores.php           # Vitrine do corpo docente
├── contato.php               # Ouvidoria e canais diretos
├── login.php                 # Login multiperfil com abas e atalhos demo
├── esqueci-senha.php         # Recuperação / simulação de reset de senha
├── logout.php                # Encerramento seguro de sessão
└── install.php               # Instalador Web 1-Clique do banco de dados
```

---

## 📜 Licença & Autoria

Desenvolvido exclusivamente para a **Master School** por **Leo Lemos / Master School ERP Dev Team**.
Todos os direitos reservados &copy; 2026.
