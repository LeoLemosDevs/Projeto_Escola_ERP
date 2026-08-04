<?php
/**
 * Master School ERP - Instalador & Gerador de Dados Acadêmicos Enterprise
 * Executa o schema.sql e popula o banco de dados com:
 *  - 1 Conta Admin (Direção)
 *  - 10 Contas de Professores (Especialidades diferentes)
 *  - 20 Turmas (Do G1 da Educação Infantil ao Ensino Médio Completo)
 *  - 80 Disciplinas vinculadas às turmas e docentes
 *  - 500 Alunos matriculados nas turmas com dados cadastrais completos
 *  - 2.000 Mensalidades (Fev, Mar, Abr, Mai) cobrindo Pix, Boleto, Cartão e Inadimplência
 *  - Milhares de registros de Notas (Bimestres 1-4) e Frequências em sala de aula
 */

require_once __DIR__ . '/config/database.php';

// Aumenta o tempo limite de execução para processamento do lote de 500 alunos
set_time_limit(120);

$message = '';
$success = false;
$stats   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    try {
        $pdo = get_db_connection();
        
        // 1. Carrega e executa o schema.sql (DDL)
        $sqlFile = __DIR__ . '/database/schema.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("Arquivo schema.sql não encontrado na pasta database/");
        }
        
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        
        // Inicia transação atômica para máxima velocidade de inserção em lote
        $pdo->beginTransaction();
        
        // Hashes BCRYPT pré-calculados para desempenho
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $profPass  = password_hash('prof123', PASSWORD_BCRYPT);
        $alunoPass = password_hash('aluno123', PASSWORD_BCRYPT);
        
        // 2. Criar 1 Conta Admin
        $stmtUsr = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role, avatar) VALUES (?, ?, ?, ?, ?)");
        $stmtUsr->execute([
            'Administração Geral (Direção)',
            'admin@masterschool.edu.br',
            $adminPass,
            'admin',
            'admin-avatar.png'
        ]);
        
        // 3. Criar 10 Contas de Professores com Matérias Diferentes
        $professoresData = [
            ['carlos.silva@masterschool.edu.br', 'Prof. Dr. Carlos Silva', 'Matemática Avançada & Geometria', 'Doutor em Matemática Aplicada pela USP com 12 anos de experiência em olimpíadas científicas.'],
            ['ana.souza@masterschool.edu.br', 'Profa. Me. Ana Souza', 'Literatura Brasileira & Redação', 'Mestre em Estudos Literários com foco em preparação para ENEM e vestibulares de elite.'],
            ['roberto.mendes@masterschool.edu.br', 'Prof. Me. Roberto Mendes', 'Física Teórica & Laboratório', 'Especialista em Física de Partículas e coordenador dos laboratórios STEM da escola.'],
            ['juliana.torres@masterschool.edu.br', 'Profa. Dra. Juliana Torres', 'Química Orgânica & Bioquímica', 'Doutora em Química pela UNICAMP, líder de pesquisas acadêmicas juvenis.'],
            ['marcos.andrade@masterschool.edu.br', 'Prof. Esp. Marcos Andrade', 'Biologia & Genética', 'Biólogo e pesquisador em ecologia, responsável pelas saídas de campo da Master School.'],
            ['beatriz.oliveira@masterschool.edu.br', 'Profa. Me. Beatriz Oliveira', 'História Mundial & Geopolítica', 'Historiadora com especialização em relações internacionais e história contemporânea.'],
            ['fernando.costa@masterschool.edu.br', 'Prof. Me. Fernando Costa', 'Geografia & Meio Ambiente', 'Geógrafo com experiência em geoprocessamento e sustentabilidade ambiental.'],
            ['mariana.lima@masterschool.edu.br', 'Profa. Esp. Mariana Lima', 'English Language & TOEFL Prep', 'Coordenadora do departamento bilíngue internacional com certificação TESOL.'],
            ['rafael.duarte@masterschool.edu.br', 'Prof. Me. Rafael Duarte', 'Filosofia, Sociologia & Ética', 'Mestre em Filosofia Política, orientador de debates sociológicos no Ensino Médio.'],
            ['camila.rocha@masterschool.edu.br', 'Profa. Esp. Camila Rocha', 'Educação Artística & Pedagogia G1-G5', 'Pedagoga e artista plástica especializada no desenvolvimento socioemocional da Educação Infantil.']
        ];
        
        $profIds = [];
        $stmtProf = $pdo->prepare("INSERT INTO professores (usuario_id, especialidade, bio, telefone, dias_horarios_trabalho) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($professoresData as $idx => $p) {
            $stmtUsr->execute([$p[1], $p[0], $profPass, 'professor', 'prof-' . ($idx+1) . '.png']);
            $usrId = $pdo->lastInsertId();
            
            $stmtProf->execute([
                $usrId,
                $p[2],
                $p[3],
                '(11) 98888-10' . str_pad($idx, 2, '0', STR_PAD_LEFT),
                'Seg a Sex - 07h30 às 16h00'
            ]);
            $profIds[] = $pdo->lastInsertId();
        }
        
        // 4. Criar 20 Turmas (Do G1 da Educação Infantil ao Ensino Médio Completo)
        $turmasData = [
            // Educação Infantil (5 turmas - G1 ao G5)
            ['G1 - Educação Infantil (Toddlers)', 2026, 'Matutino', 'Sala 101 - Ala Infantil'],
            ['G2 - Educação Infantil (Nursery)',  2026, 'Matutino', 'Sala 102 - Ala Infantil'],
            ['G3 - Educação Infantil (Pre-K)',    2026, 'Matutino', 'Sala 103 - Ala Infantil'],
            ['G4 - Educação Infantil (Kinder 4)', 2026, 'Matutino', 'Sala 104 - Ala Infantil'],
            ['G5 - Educação Infantil (Kinder 5)', 2026, 'Matutino', 'Sala 105 - Ala Infantil'],
            // Ensino Fundamental I (5 turmas - 1º ao 5º Ano)
            ['1º Ano - Ensino Fundamental I', 2026, 'Matutino', 'Sala 106 - Ala Central'],
            ['2º Ano - Ensino Fundamental I', 2026, 'Matutino', 'Sala 107 - Ala Central'],
            ['3º Ano - Ensino Fundamental I', 2026, 'Matutino', 'Sala 108 - Ala Central'],
            ['4º Ano - Ensino Fundamental I', 2026, 'Matutino', 'Sala 109 - Ala Central'],
            ['5º Ano - Ensino Fundamental I', 2026, 'Matutino', 'Sala 110 - Ala Central'],
            // Ensino Fundamental II (4 turmas - 6º ao 9º Ano)
            ['6º Ano - Ensino Fundamental II', 2026, 'Vespertino', 'Sala 201 - Ala Norte'],
            ['7º Ano - Ensino Fundamental II', 2026, 'Vespertino', 'Sala 202 - Ala Norte'],
            ['8º Ano - Ensino Fundamental II', 2026, 'Vespertino', 'Sala 203 - Ala Norte'],
            ['9º Ano - Ensino Fundamental II', 2026, 'Vespertino', 'Sala 204 - Ala Norte'],
            // Ensino Médio Completo (6 turmas - 1ª, 2ª e 3ª Série - Matutino e Vespertino)
            ['1ª Série - Ensino Médio - A', 2026, 'Matutino',   'Sala 301 - Ala Sul'],
            ['1ª Série - Ensino Médio - B', 2026, 'Vespertino', 'Sala 302 - Ala Sul'],
            ['2ª Série - Ensino Médio - A', 2026, 'Matutino',   'Sala 303 - Ala Sul'],
            ['2ª Série - Ensino Médio - B', 2026, 'Vespertino', 'Sala 304 - Ala Sul'],
            ['3ª Série - Ensino Médio - A', 2026, 'Matutino',   'Sala 305 - Ala Sul'],
            ['3ª Série - Ensino Médio - B', 2026, 'Vespertino', 'Sala 306 - Ala Sul']
        ];
        
        $stmtTurma = $pdo->prepare("INSERT INTO turmas (nome, ano_letivo, turno, sala) VALUES (?, ?, ?, ?)");
        $turmaIds  = [];
        foreach ($turmasData as $t) {
            $stmtTurma->execute($t);
            $turmaIds[] = $pdo->lastInsertId();
        }
        
        // 5. Criar 80 Disciplinas vinculadas às 20 Turmas e aos 10 Professores
        $stmtDisc = $pdo->prepare("INSERT INTO disciplinas (nome, codigo, professor_id, turma_id) VALUES (?, ?, ?, ?)");
        $disciplinasPorTurma = [];
        
        $disciplinasNomes = [
            'Matemática e Lógica',
            'Língua Portuguesa & Redação',
            'Física & Ciências do Ambiente',
            'Química Aplicada',
            'Biologia & Vida Sustentável',
            'História & Humanidades',
            'Geografia & Espaço Global',
            'English Language & Immersion',
            'Filosofia & Sociologia',
            'Arte, Expressão & Música'
        ];
        
        $discCounter = 1;
        foreach ($turmaIds as $idxTurma => $tId) {
            $disciplinasPorTurma[$tId] = [];
            // Cada turma terá 4 disciplinas principais atribuídas a docentes diferentes
            for ($d = 0; $d < 4; $d++) {
                $profIndex = ($idxTurma + $d) % 10;
                $nomeDisc  = $disciplinasNomes[$profIndex];
                $codDisc   = 'DISC-' . str_pad($discCounter++, 4, '0', STR_PAD_LEFT);
                
                $stmtDisc->execute([$nomeDisc, $codDisc, $profIds[$profIndex], $tId]);
                $disciplinasPorTurma[$tId][] = $pdo->lastInsertId();
            }
        }
        
        // 6. Criar 500 Alunos Matriculados (25 em cada uma das 20 Turmas)
        $primeirosNomes = [
            'Gabriel', 'Arthur', 'Guilherme', 'Heitor', 'Theo', 'Davi', 'Lucas', 'Matheus', 'Nicolas', 'Rafael',
            'Daniel', 'Pedro', 'Samuel', 'Enzo', 'Bruno', 'Felipe', 'Rodrigo', 'Leonardo', 'Gustavo', 'Vinicius',
            'Laura', 'Maria Eduarda', 'Alice', 'Sophia', 'Helena', 'Valentina', 'Isabella', 'Manuela', 'Júlia', 'Luiza',
            'Mariana', 'Larissa', 'Gabriela', 'Amanda', 'Bianca', 'Carolina', 'Fernanda', 'Camila', 'Letícia', 'Juliana',
            'Natália', 'Vitória', 'Yasmin', 'Clara', 'Eduarda', 'Bruna', 'Renata', 'Patrícia', 'Vanessa', 'Beatriz'
        ];
        $sobrenomes = [
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira', 'Alves', 'Pereira', 'Lima', 'Gomes',
            'Costa', 'Ribeiro', 'Martins', 'Carvalho', 'Almeida', 'Lopes', 'Soares', 'Fernandes', 'Vieira', 'Barbosa',
            'Rocha', 'Dias', 'Nascimento', 'Andrade', 'Mendes', 'Nunes', 'Moreira', 'Cardoso', 'Teixeira', 'Cavalcanti'
        ];
        
        $stmtAlunoUsr = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, role, avatar) VALUES (?, ?, ?, 'aluno', ?)");
        $stmtAluno    = $pdo->prepare("INSERT INTO alunos (usuario_id, matricula, data_nascimento, cpf, telefone, endereco, turma_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtMensal   = $pdo->prepare("INSERT INTO mensalidades (aluno_id, mes_referencia, valor, vencimento, status, metodo_pagamento, codigo_pagseguro, data_pagamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtNota     = $pdo->prepare("INSERT INTO notas (aluno_id, disciplina_id, bimestre, nota, observacao_professor) VALUES (?, ?, ?, ?, ?)");
        $stmtFreq     = $pdo->prepare("INSERT INTO frequencias (aluno_id, disciplina_id, data_aula, presente, observacao) VALUES (?, ?, ?, ?, ?)");
        
        // Assegurar que Aluno 1 e 2 possuem os e-mails e senhas oficiais de demonstração
        $alunoDemo1 = ['email' => 'lucas.mendes@aluno.masterschool.edu.br', 'nome' => 'Lucas Mendes (Aluno Demo)'];
        $alunoDemo2 = ['email' => 'beatriz.lima@aluno.masterschool.edu.br', 'nome' => 'Beatriz Lima (Aluna Demo)'];
        
        $dataAulasExemplo = ['2026-03-02', '2026-03-09', '2026-03-16', '2026-03-23', '2026-04-06'];
        
        for ($i = 1; $i <= 500; $i++) {
            $turmaIdx = ($i - 1) % 20;
            $tId      = $turmaIds[$turmaIdx];
            
            if ($i === 1) {
                $nomeAluno  = $alunoDemo1['nome'];
                $emailAluno = $alunoDemo1['email'];
            } elseif ($i === 2) {
                $nomeAluno  = $alunoDemo2['nome'];
                $emailAluno = $alunoDemo2['email'];
            } else {
                $pNome      = $primeirosNomes[($i - 1) % count($primeirosNomes)];
                $sNome      = $sobrenomes[($i * 7) % count($sobrenomes)];
                $nomeAluno  = "{$pNome} {$sNome}";
                $emailClean = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', "{$pNome}.{$sNome}.{$i}"));
                $emailAluno = preg_replace('/[^a-z0-9._]/', '', $emailClean) . "@aluno.masterschool.edu.br";
            }
            
            $stmtAlunoUsr->execute([
                $nomeAluno,
                $emailAluno,
                $alunoPass,
                "aluno-{$i}.png"
            ]);
            $usrIdAluno = $pdo->lastInsertId();
            
            // Gerar Matrícula MS2026 + número (001 a 500)
            $matr = 'MS2026' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $anoNasc = 2022 - intval($turmaIdx * 0.6); // Idades compatíveis com G1 até 3ª Série
            $dataNasc = "{$anoNasc}-" . str_pad((($i % 12) + 1), 2, '0', STR_PAD_LEFT) . "-15";
            
            $cpf = str_pad(($i * 123) % 999, 3, '0', STR_PAD_LEFT) . '.' . 
                   str_pad(($i * 456) % 999, 3, '0', STR_PAD_LEFT) . '.' . 
                   str_pad(($i * 789) % 999, 3, '0', STR_PAD_LEFT) . '-01';
            
            $stmtAluno->execute([
                $usrIdAluno,
                $matr,
                $dataNasc,
                $cpf,
                "(11) 97777-" . str_pad($i, 4, '0', STR_PAD_LEFT),
                "Av. Paulista, 1500 - Apto {$i} - São Paulo, SP",
                $tId
            ]);
            $alunoId = $pdo->lastInsertId();
            
            // Mensalidades (Fev, Mar, Abr, Mai) -> 4 mensalidades para cada aluno (2.000 total)
            $valorMensal = $turmaIdx < 5 ? 1850.00 : ($turmaIdx < 14 ? 2150.00 : 2450.00);
            
            // Fev e Mar: Pagos
            $stmtMensal->execute([$alunoId, 'Fevereiro/2026', $valorMensal, '2026-02-10', 'pago', 'pix', 'PAG-FEV-' . $i, '2026-02-08 10:15:00']);
            $stmtMensal->execute([$alunoId, 'Março/2026',     $valorMensal, '2026-03-10', 'pago', 'boleto', 'PAG-MAR-' . $i, '2026-03-09 14:20:00']);
            
            // Abr e Mai: Pendente ou Atrasado
            $statusAbr = ($i % 7 === 0) ? 'atrasado' : 'pendente';
            $stmtMensal->execute([$alunoId, 'Abril/2026', $valorMensal, '2026-04-10', $statusAbr, NULL, NULL, NULL]);
            $stmtMensal->execute([$alunoId, 'Maio/2026',  $valorMensal, '2026-05-10', 'pendente', NULL, NULL, NULL]);
            
            // Notas e Frequências nas Disciplinas da Turma
            foreach ($disciplinasPorTurma[$tId] as $idxDisc => $discId) {
                // Notas no 1º e 2º Bimestre
                $nota1 = 7.0 + (($i + $idxDisc) % 30) / 10;
                $nota2 = 7.5 + (($i * 2 + $idxDisc) % 25) / 10;
                
                $coment1 = $nota1 >= 9.0 ? 'Desempenho de excelência.' : ($nota1 >= 8.0 ? 'Boa participação nas atividades.' : 'Acompanhamento satisfatório.');
                $coment2 = $nota2 >= 9.0 ? 'Nota máxima em teste prático.' : 'Bom trabalho em grupo.';
                
                $stmtNota->execute([$alunoId, $discId, 1, $nota1, $coment1]);
                $stmtNota->execute([$alunoId, $discId, 2, $nota2, $coment2]);
                
                // Frequências (5 datas por disciplina para demonstração)
                foreach ($dataAulasExemplo as $idxData => $dtAula) {
                    $presente = ($i % 13 === 0 && $idxData === 2) ? 0 : 1;
                    $obsFreq  = $presente ? '' : 'Falta justificada com atestado médico';
                    $stmtFreq->execute([$alunoId, $discId, $dtAula, $presente, $obsFreq]);
                }
            }
        }
        
        // 7. Notícias Institucionais para o Mural Principal
        $pdo->exec("INSERT INTO noticias_eventos (titulo, resumo, conteudo, tipo, imagem_url, destaque, data_publicacao) VALUES 
            ('Feira Internacional de Ciências & Robótica 2026', 'Alunos da Master School apresentarão protótipos com IA no Annual Science Fair.', 'A Feira de Ciências da Master School ocorrerá nos dias 25 e 26 de Maio no Auditório Principal com apresentação de projetos interdisciplinares.', 'evento', 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&auto=format&fit=crop&q=80', 1, '2026-03-15'),
            ('Recesso Escolar de Inverno & Férias de Julho', 'Confira o calendário oficial para o recesso acadêmico e orientações para estudos.', 'Informamos que o recesso escolar oficial de meio de ano começará no dia 05 de Julho com retorno em 26 de Julho de 2026.', 'ferias', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&auto=format&fit=crop&q=80', 1, '2026-03-20'),
            ('Lucas Mendes — Destaque na OBMEP 2026', 'O aluno da 3ª Série do Ensino Médio conquistou medalha de ouro na competição de cálculo.', 'Nesta semana parabenizamos o aluno Lucas Mendes por seu brilhante desempenho acadêmico, inspirando toda a nossa comunidade escolar.', 'destaque_aluno', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80', 1, '2026-03-22'),
            ('Abertura de Matrículas para o Ano Letivo 2027', 'Bolsas de mérito acadêmico e condições exclusivas para renovações e novos alunos.', 'Já estão abertas as inscrições para o processo seletivo de novos alunos e renovação da nossa comunidade educacional.', 'noticia', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80', 0, '2026-03-25')");
        
        $pdo->commit();
        
        $success = true;
        $message = "🎉 Sucesso! Banco de Dados 'master_school_erp' criado e populado em alta velocidade!";
        $stats   = [
            'admin'     => 1,
            'docentes'  => 10,
            'turmas'    => 20,
            'disciplinas' => 80,
            'alunos'    => 500,
            'mensalidades' => 2000
        ];
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $message = "Erro ao instalar banco de dados: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador Enterprise — Master School ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --accent: #f59e0b;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.85);
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
            padding: 30px;
        }
        .installer-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 45px;
            max-width: 820px;
            width: 100%;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
            text-align: center;
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            margin-bottom: 12px;
            background: linear-gradient(to right, #60a5fa, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p { color: var(--text-muted); margin-bottom: 25px; line-height: 1.6; }
        .alert {
            padding: 18px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-weight: 600;
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
            padding: 15px 32px;
            border-radius: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px 0 rgba(59, 130, 246, 0.4);
            font-size: 1rem;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.6);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text);
            margin-left: 10px;
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 25px 0;
        }
        .stat-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 18px;
            text-align: center;
        }
        .stat-item .num {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #60a5fa;
            display: block;
        }
        .stat-item .label {
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        .credentials-box {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            text-align: left;
            margin-top: 25px;
            font-size: 0.92rem;
        }
        .credentials-box h4 {
            color: #60a5fa;
            margin-bottom: 15px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
        }
        .credentials-box ul {
            list-style: none;
            padding: 0;
        }
        .credentials-box li {
            margin-bottom: 10px;
            color: #cbd5e1;
        }
        .credentials-box code {
            background: #1e293b;
            padding: 3px 8px;
            border-radius: 6px;
            color: #f59e0b;
            font-weight: 600;
        }
        .teachers-list {
            max-height: 140px;
            overflow-y: auto;
            background: rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            padding: 10px 15px;
            margin-top: 15px;
            font-size: 0.85rem;
        }
        .teachers-list p { margin: 4px 0; color: #cbd5e1; text-align: left; }
    </style>
</head>
<body>
    <div class="installer-card">
        <h1>Master School ERP — Gerador Enterprise</h1>
        <p>Criador de Banco de Dados de Alta Performance com 500 Alunos do G1 ao Ensino Médio, 10 Professores e 2.000 Mensalidades para Teste</p>
        
        <?php if ($message): ?>
            <div class="alert <?= $success ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST">
                <p style="margin-bottom: 30px;">
                    Ao clicar abaixo, o instalador recriará o banco de dados e irá popular automaticamente com <strong>1 Administração Geral</strong>, <strong>10 Docentes (matérias diversas)</strong>, <strong>20 Turmas (G1 da Ed. Infantil até 3ª Série EM)</strong> e <strong>500 Alunos matriculados</strong> com notas, presenças e financeiro realistas.
                </p>
                <button type="submit" name="install" class="btn">🚀 INSTALAR / RESETAR COM 500 ALUNOS E 10 PROFESSORES</button>
            </form>
        <?php else: ?>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="num">500</span>
                    <span class="label">Alunos Matriculados (G1 - EM)</span>
                </div>
                <div class="stat-item">
                    <span class="num">10</span>
                    <span class="label">Professores (Especialistas)</span>
                </div>
                <div class="stat-item">
                    <span class="num">20</span>
                    <span class="label">Turmas Ativas (2026)</span>
                </div>
                <div class="stat-item">
                    <span class="num">80</span>
                    <span class="label">Disciplinas Cadastradas</span>
                </div>
                <div class="stat-item">
                    <span class="num">2.000</span>
                    <span class="label">Mensalidades (Pix/Boleto)</span>
                </div>
                <div class="stat-item">
                    <span class="num">1</span>
                    <span class="label">Administração Escolar</span>
                </div>
            </div>

            <div class="credentials-box">
                <h4>🔑 Credenciais para Demonstração Imediata:</h4>
                <ul>
                    <li>⚙️ <strong>Admin Escolar:</strong> <code>admin@masterschool.edu.br</code> | Senha: <code>admin123</code></li>
                    <li>👨‍🏫 <strong>Professor 1 (Matemática):</strong> <code>carlos.silva@masterschool.edu.br</code> | Senha: <code>prof123</code></li>
                    <li>👩‍🏫 <strong>Professora 2 (Literatura):</strong> <code>ana.souza@masterschool.edu.br</code> | Senha: <code>prof123</code></li>
                    <li>👨‍🏫 <strong>Professor 3 (Física):</strong> <code>roberto.mendes@masterschool.edu.br</code> | Senha: <code>prof123</code></li>
                    <li>👩‍🏫 <strong>Professora 4 (Química):</strong> <code>juliana.torres@masterschool.edu.br</code> | Senha: <code>prof123</code></li>
                    <li>🎓 <strong>Aluno Demo 1 (EM):</strong> <code>lucas.mendes@aluno.masterschool.edu.br</code> | Senha: <code>aluno123</code></li>
                    <li>🎓 <strong>Aluna Demo 2 (EM):</strong> <code>beatriz.lima@aluno.masterschool.edu.br</code> | Senha: <code>aluno123</code></li>
                </ul>
                <div class="teachers-list">
                    <strong style="color: #60a5fa; display: block; margin-bottom: 5px;">Outros 6 Docentes Criados (Todos com senha: <code>prof123</code>):</strong>
                    <p>• Biologia: <code>marcos.andrade@masterschool.edu.br</code></p>
                    <p>• História: <code>beatriz.oliveira@masterschool.edu.br</code></p>
                    <p>• Geografia: <code>fernando.costa@masterschool.edu.br</code></p>
                    <p>• Inglês: <code>mariana.lima@masterschool.edu.br</code></p>
                    <p>• Filosofia/Sociologia: <code>rafael.duarte@masterschool.edu.br</code></p>
                    <p>• Artes & Educação Infantil G1-G5: <code>camila.rocha@masterschool.edu.br</code></p>
                </div>
            </div>
            
            <div style="margin-top: 35px;">
                <a href="index.php" class="btn">Acessar Portal Institucional</a>
                <a href="login.php" class="btn btn-outline">Acessar Tela de Login do ERP</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
