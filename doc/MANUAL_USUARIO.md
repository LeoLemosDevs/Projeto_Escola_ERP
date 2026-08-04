# 📘 Manual de Uso do Sistema — Master School ERP

Guia prático e passo a passo de operação do portal web e do ERP acadêmico para **Estudantes**, **Professores** e **Gestores (Administradores)**.

---

## 1. Como Entrar no Sistema (Tela de Login Multiperfil)

1. Acesse o endereço principal: `http://localhost/Projeto_Escola_ERP/login.php`.
2. Clique na aba correspondente à sua função:
   - 🎓 **Aluno:** Entrada com e-mail do estudante.
   - 👨‍🏫 **Professor:** Entrada com e-mail institucional docente.
   - ⚙️ **Admin:** Acesso irrestrito ao painel de diretoria e TI.
3. *Modo Demonstração (Para Portfólio/Testes):* Clique diretamente nos botões no rodapé da página **"⚡ Preencher Aluno"**, **"⚡ Preencher Professor"** ou **"⚡ Preencher Admin"** para o preenchimento automático das credenciais sem digitação.

---

## 2. Guia do Estudante (Aluno)

### A. Consultar o Boletim de Notas e Pareceres (`aluno/boletim.php`)
- O boletim exibe as notas de **0 a 10** para os 4 bimestres de todas as matérias matriculadas na sua turma.
- Ao lado da nota, confira o **comentário pedagógico descritivo** deixado pelo professor.

### B. Monitorar Frequência Anual
- Acompanhe o percentual de frequência total e visualize as marcações diárias de presenças, faltas e justificativas registradas no ano letivo.

### C. Pagamento Online de Mensalidades (`aluno/financeiro.php`)
- Clique na aba lateral **"Mensalidades (PagSeguro)"**.
- Identifique os títulos em aberto (marcados com *Pendente* ou *Atrasado*) e clique em **"⚡ Pagar com PagSeguro"**.
- No modal de checkout simulado:
  - Escolha **PIX** para gerar o QR Code interativo e código Copia-e-Cola.
  - Escolha **Boleto Bancário** para obter a linha digitável.
  - Escolha **Cartão de Crédito** para transações parceladas em até 12x.
- Clique em **"Confirmar & Processar Pagamento"** para liquidar o título em tempo real no banco de dados.

---

## 3. Guia do Docente (Professor)

### A. Diário de Chamada em Lote (`professor/chamada.php`)
- Selecione no menu superior a **Disciplina**, a **Turma** e a **Data da Aula**.
- Utilize o botão de atalho **"✔ Marcar Todos Presentes"** para agilizar a chamada ou alterne manualmente os status (`Presente`, `Ausente`, `Justificado`).
- Clique em **"💾 Salvar Diário de Classe"** para gravar as presenças de toda a turma de uma só vez (`UPSERT`).

### B. Lançamento de Notas Bimestrais (`professor/notas.php`)
- Escolha a disciplina e selecione qual bimestre deseja avaliar (**1º, 2º, 3º ou 4º Bimestre**).
- Insira as notas numéricas (0 a 10) e digite observações qualitativas para cada aluno na tabela.
- Salve o lançamento para que o aluno visualize a atualização instantaneamente em seu boletim.

---

## 4. Guia da Administração Geral (Admin)

### A. Visão Analítica Executiva (`admin/index.php`)
- Acompanhe os **4 Cards Executivos**: total de alunos matriculados, total de docentes, turmas ativas e o montante de receita arrecadada vs. a receber.
- Interaja com os gráficos do **Chart.js**:
  - *Desempenho por Disciplina:* Gráfico de barras indicando quais matérias estão com maiores e menores médias de notas.
  - *Arrecadação Financeira:* Gráfico de rosca detalhando liquidez, pendências e inadimplência.

### B. Cadastro de Matrículas e Estudantes (`admin/alunos.php`)
- Clique em **"➕ Matricular Novo Aluno no ERP"** para exibir o formulário.
- Preencha o nome, matrícula, CPF, e-mail e vincule-o à turma correta. O sistema criará o acesso escolar automaticamente com a senha padrão `aluno123`.

### C. Gestão do Corpo Docente e Disciplinas (`admin/professores.php` / `turmas.php`)
- Cadastre novos professores informando sua titulação académica (*Especialista*, *Mestre*, *Doutor*) e especialidade.
- Na aba de **Turmas**, cadastre salas de aula e vincule docentes às respectivas disciplinas de cada ano.

### D. Mural de Notícias da Home (`admin/noticias.php`)
- Publique avisos, feriados ou destaques marcando a opção **"⭐ Destacar no Mural Principal"**.
- As notícias publicadas são renderizadas instantaneamente na página principal pública (`index.php`).

### E. Trilha de Auditoria de TI (`admin/logs.php`)
- Exibe o histórico de todos os eventos do ecossistema, mostrando qual usuário realizou a ação, a hora exata e o endereço IP de conexão (`$_SERVER['REMOTE_ADDR']`).
