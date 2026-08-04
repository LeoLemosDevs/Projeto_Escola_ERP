<?php
/**
 * Master School ERP - Instalador Automatizado do Banco de Dados
 * Executa o schema.sql e popula o banco de dados com dados de teste prontas para o XAMPP.
 */

require_once __DIR__ . '/config/database.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    try {
        $pdo = get_db_connection();
        
        // Carrega o arquivo SQL
        $sqlFile = __DIR__ . '/database/schema.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("Arquivo schema.sql não encontrado na pasta database/");
        }
        
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        
        // Gera os hashes reais para o ambiente PHP local
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $profPass  = password_hash('prof123', PASSWORD_BCRYPT);
        $alunoPass = password_hash('aluno123', PASSWORD_BCRYPT);
        
        // 1. Usuários Seed
        $stmtUsr = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role, avatar) VALUES 
            ('Administração Geral', 'admin@masterschool.edu.br', ?, 'admin', 'admin-avatar.png'),
            ('Prof. Carlos Silva (Math & Physics)', 'carlos.silva@masterschool.edu.br', ?, 'professor', 'prof-carlos.png'),
            ('Profa. Ana Souza (Literature & English)', 'ana.souza@masterschool.edu.br', ?, 'professor', 'prof-ana.png'),
            ('Lucas Mendes (Aluno)', 'lucas.mendes@aluno.masterschool.edu.br', ?, 'aluno', 'aluno-lucas.png'),
            ('Beatriz Lima (Aluna)', 'beatriz.lima@aluno.masterschool.edu.br', ?, 'aluno', 'aluno-beatriz.png')");
        $stmtUsr->execute([$adminPass, $profPass, $profPass, $alunoPass, $alunoPass]);
        
        // IDs gerados
        $idAdmin = 1;
        $idProfCarlos = 2;
        $idProfAna = 3;
        $idAlunoLucas = 4;
        $idAlunoBeatriz = 5;
        
        // 2. Turmas
        $pdo->exec("INSERT INTO turmas (id, nome, ano_letivo, turno, sala) VALUES 
            (1, '3ª Série Ensino Médio - A', 2026, 'Matutino', 'Sala 301 - Ala Norte'),
            (2, '2ª Série Ensino Médio - B', 2026, 'Vespertino', 'Sala 204 - Ala Sul')");
        
        // 3. Professores (Detalhes)
        $stmtProf = $pdo->prepare("INSERT INTO professores (id, usuario_id, especialidade, bio, telefone, dias_horarios_trabalho) VALUES 
            (1, ?, 'Matemática e Física Avançada', 'Doutor em Matemática Aplicada pela USP, com 12 anos de experiência no ensino bilíngue internacional.', '(11) 98888-1111', 'Seg, Qua, Sex - 07h30 às 12h30'),
            (2, ?, 'Literature & English Studies', 'Mestre em Literatura Inglesa por Cambridge, coordenadora do programa de imersão de intercâmbio.', '(11) 98888-2222', 'Ter, Qui - 08h00 às 16h00')");
        $stmtProf->execute([$idProfCarlos, $idProfAna]);
        
        // 4. Alunos (Detalhes)
        $stmtAlu = $pdo->prepare("INSERT INTO alunos (id, usuario_id, matricula, data_nascimento, cpf, telefone, endereco, turma_id) VALUES 
            (1, ?, 'MS2026001', '2008-04-15', '123.456.789-01', '(11) 97777-0001', 'Av. Paulista, 1500 - Apto 81 - São Paulo, SP', 1),
            (2, ?, 'MS2026002', '2008-09-22', '987.654.321-09', '(11) 97777-0002', 'Rua Augusta, 800 - Apto 32 - São Paulo, SP', 1)");
        $stmtAlu->execute([$idAlunoLucas, $idAlunoBeatriz]);
        
        // 5. Disciplinas
        $pdo->exec("INSERT INTO disciplinas (id, nome, codigo, professor_id, turma_id) VALUES 
            (1, 'Matemática Avançada', 'MAT301', 1, 1),
            (2, 'Física Aplicada', 'FIS301', 1, 1),
            (3, 'Literatura Brasileira', 'LIT301', 2, 1),
            (4, 'English Language & Debate', 'ENG301', 2, 1)");
        
        // 6. Frequências Seed (Exemplos)
        $pdo->exec("INSERT INTO frequencias (aluno_id, disciplina_id, data_aula, presente, observacao) VALUES 
            (1, 1, '2026-03-02', 1, 'Presença participativa'),
            (1, 1, '2026-03-04', 1, ''),
            (1, 1, '2026-03-09', 0, 'Consulta médica informada pela família'),
            (1, 2, '2026-03-03', 1, ''),
            (2, 1, '2026-03-02', 1, ''),
            (2, 1, '2026-03-04', 1, ''),
            (2, 1, '2026-03-09', 1, '')");
        
        // 7. Notas Seed
        $pdo->exec("INSERT INTO notas (aluno_id, disciplina_id, bimestre, nota, observacao_professor) VALUES 
            (1, 1, 1, 9.50, 'Excelente desempenho no primeiro módulo de cálculo.'),
            (1, 2, 1, 8.70, 'Ótimo raciocínio lógico em testes práticos.'),
            (1, 3, 1, 9.00, 'Redação excelente na resenha literária.'),
            (1, 4, 1, 10.00, 'Fluência oral extraordinária nos debates.'),
            (2, 1, 1, 8.80, 'Participação ativa nas resoluções em sala.'),
            (2, 2, 1, 9.20, 'Projeto laboratorial nota máxima.')");
        
        // 8. Mensalidades Seed (Para teste do PagSeguro no Painel do Aluno 1)
        $pdo->exec("INSERT INTO mensalidades (aluno_id, mes_referencia, valor, vencimento, status, metodo_pagamento) VALUES 
            (1, 'Fevereiro/2026', 2450.00, '2026-02-10', 'pago', 'pix'),
            (1, 'Março/2026', 2450.00, '2026-03-10', 'pago', 'boleto'),
            (1, 'Abril/2026', 2450.00, '2026-04-10', 'pendente', NULL),
            (1, 'Maio/2026', 2450.00, '2026-05-10', 'pendente', NULL),
            (2, 'Março/2026', 2450.00, '2026-03-10', 'pago', 'pix'),
            (2, 'Abril/2026', 2450.00, '2026-04-10', 'atrasado', NULL)");
        
        // 9. Notícias, Eventos, Férias e Alunos Destaques
        $pdo->exec("INSERT INTO noticias_eventos (titulo, resumo, conteudo, tipo, imagem_url, destaque, data_publicacao) VALUES 
            ('Feira Internacional de Ciências & Robótica 2026', 'Alunos da Master School apresentarão protótipos em inglês e português na Annual Science Fair.', 'A Feira de Ciências da Master School ocorrerá nos dias 25 e 26 de Maio no Auditório Principal. Todos os familiares e convidados poderão assistir aos debates e exposições dos projetos integrados com inteligência artificial.', 'evento', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&auto=format&fit=crop&q=80', 1, '2026-03-15'),
            ('Recesso Escolar de Inverno e Férias de Julho', 'Confira o calendário oficial para o recesso acadêmico e orientações para estudos complementares.', 'Informamos que o recesso escolar oficial do meio de ano começará no dia 05 de Julho com retorno das atividades letivas em 26 de Julho de 2026.', 'ferias', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&auto=format&fit=crop&q=80', 1, '2026-03-20'),
            ('Lucas Mendes — Destaque na Olimpíada Nacional de Matemática', 'O aluno da 3ª Série do Ensino Médio conquistou medalha de ouro na OBMEP e convite de conferência em Boston.', 'Nesta semana parabenizamos o aluno Lucas Mendes por seu brilhante desempenho. Seu comprometimento inspira toda a comunidade Master School!', 'destaque_aluno', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80', 1, '2026-03-22'),
            ('Abertura das Matrículas para o Ano Letivo 2027', 'Bolsas de mérito acadêmico e condições exclusivas para inscrições antecipadas.', 'Já estão abertas as inscrições para o processo seletivo de novos alunos e renovação da comunidade atual.', 'noticia', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80', 0, '2026-03-25')");
        
        $success = true;
        $message = "Banco de Dados 'master_school_erp' configurado e populado com sucesso!";
    } catch (Exception $e) {
        $message = "Erro ao instalar banco de dados: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador — Master School ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #3b82f6;
            --accent: #f59e0b;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.75);
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .installer-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            max-width: 650px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            margin-bottom: 10px;
            background: linear-gradient(to right, #60a5fa, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p { color: var(--text-muted); margin-bottom: 24px; line-height: 1.6; }
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
        }
        .alert.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid #10b981;
            color: #34d399;
        }
        .alert.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            color: #f87171;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text);
            margin-left: 10px;
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .credentials-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 20px;
            text-align: left;
            margin-top: 25px;
            font-size: 0.9rem;
        }
        .credentials-box h4 {
            color: #60a5fa;
            margin-bottom: 12px;
            font-family: 'Outfit', sans-serif;
        }
        .credentials-box ul {
            list-style: none;
            padding: 0;
        }
        .credentials-box li {
            margin-bottom: 8px;
            color: #cbd5e1;
        }
        .credentials-box code {
            background: #1e293b;
            padding: 2px 6px;
            border-radius: 4px;
            color: #f59e0b;
        }
    </style>
</head>
<body>
    <div class="installer-card">
        <h1>Master School ERP</h1>
        <p>Assistente de Instalação do Banco de Dados para XAMPP/MySQL</p>
        
        <?php if ($message): ?>
            <div class="alert <?= $success ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST">
                <p>Clique no botão abaixo para criar as tabelas e inserir os dados iniciais de demonstração (Alunos, Professores, Admins, Turmas e Eventos).</p>
                <button type="submit" name="install" class="btn">Instalar / Resetar Banco de Dados</button>
            </form>
        <?php else: ?>
            <div class="credentials-box">
                <h4>Credenciais de Teste Geradas:</h4>
                <ul>
                    <li><strong>Administrador:</strong> <code>admin@masterschool.edu.br</code> | Senha: <code>admin123</code></li>
                    <li><strong>Professor (Carlos):</strong> <code>carlos.silva@masterschool.edu.br</code> | Senha: <code>prof123</code></li>
                    <li><strong>Profa (Ana):</strong> <code>ana.souza@masterschool.edu.br</code> | Senha: <code>prof123</code></li>
                    <li><strong>Aluno (Lucas):</strong> <code>lucas.mendes@aluno.masterschool.edu.br</code> | Senha: <code>aluno123</code></li>
                </ul>
            </div>
            <div style="margin-top: 30px;">
                <a href="index.php" class="btn">Acessar Portal da Escola</a>
                <a href="login.php" class="btn btn-outline">Acessar Login ERP</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
