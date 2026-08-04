-- Master School ERP - Database Schema DDL & Seed Data
-- Banco de Dados MySQL / MariaDB (XAMPP)

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `system_logs`;
DROP TABLE IF EXISTS `noticias_eventos`;
DROP TABLE IF EXISTS `mensalidades`;
DROP TABLE IF EXISTS `notas`;
DROP TABLE IF EXISTS `frequencias`;
DROP TABLE IF EXISTS `disciplinas`;
DROP TABLE IF EXISTS `alunos`;
DROP TABLE IF EXISTS `professores`;
DROP TABLE IF EXISTS `turmas`;
DROP TABLE IF EXISTS `usuarios`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tabelas de Autenticação e Usuários
CREATE TABLE `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `senha_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('aluno', 'professor', 'admin') NOT NULL,
  `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
  `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela de Turmas
CREATE TABLE `turmas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(50) NOT NULL,
  `ano_letivo` YEAR NOT NULL,
  `turno` ENUM('Matutino', 'Vespertino', 'Integral') NOT NULL DEFAULT 'Matutino',
  `sala` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela de Professores
CREATE TABLE `professores` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NOT NULL,
  `especialidade` VARCHAR(100) NOT NULL,
  `bio` TEXT DEFAULT NULL,
  `telefone` VARCHAR(25) DEFAULT NULL,
  `dias_horarios_trabalho` VARCHAR(255) DEFAULT 'Seg a Sex - 07h30 às 13h00',
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabela de Alunos
CREATE TABLE `alunos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NOT NULL,
  `matricula` VARCHAR(20) NOT NULL UNIQUE,
  `data_nascimento` DATE NOT NULL,
  `cpf` VARCHAR(20) DEFAULT NULL,
  `telefone` VARCHAR(25) DEFAULT NULL,
  `endereco` VARCHAR(255) DEFAULT NULL,
  `turma_id` INT UNSIGNED DEFAULT NULL,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`turma_id`) REFERENCES `turmas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabela de Disciplinas
CREATE TABLE `disciplinas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(20) NOT NULL UNIQUE,
  `professor_id` INT UNSIGNED DEFAULT NULL,
  `turma_id` INT UNSIGNED DEFAULT NULL,
  FOREIGN KEY (`professor_id`) REFERENCES `professores`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`turma_id`) REFERENCES `turmas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabela de Frequência (Chamada)
CREATE TABLE `frequencias` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `aluno_id` INT UNSIGNED NOT NULL,
  `disciplina_id` INT UNSIGNED NOT NULL,
  `data_aula` DATE NOT NULL,
  `presente` TINYINT(1) DEFAULT 1,
  `observacao` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`aluno_id`) REFERENCES `alunos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `idx_freq_unique` (`aluno_id`, `disciplina_id`, `data_aula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabela de Notas e Observações Acadêmicas
CREATE TABLE `notas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `aluno_id` INT UNSIGNED NOT NULL,
  `disciplina_id` INT UNSIGNED NOT NULL,
  `bimestre` TINYINT UNSIGNED NOT NULL COMMENT '1, 2, 3 ou 4',
  `nota` DECIMAL(4, 2) DEFAULT NULL,
  `observacao_professor` TEXT DEFAULT NULL,
  FOREIGN KEY (`aluno_id`) REFERENCES `alunos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `idx_nota_unique` (`aluno_id`, `disciplina_id`, `bimestre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabela de Mensalidades / Financeiro (PagSeguro)
CREATE TABLE `mensalidades` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `aluno_id` INT UNSIGNED NOT NULL,
  `mes_referencia` VARCHAR(20) NOT NULL COMMENT 'Ex: Março/2026',
  `valor` DECIMAL(10, 2) NOT NULL,
  `vencimento` DATE NOT NULL,
  `status` ENUM('pendente', 'pago', 'atrasado') NOT NULL DEFAULT 'pendente',
  `metodo_pagamento` VARCHAR(50) DEFAULT NULL COMMENT 'pix, boleto, credito',
  `codigo_pagseguro` VARCHAR(100) DEFAULT NULL,
  `data_pagamento` DATETIME DEFAULT NULL,
  FOREIGN KEY (`aluno_id`) REFERENCES `alunos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabela de Notícias, Férias, Destaques e Mural Institucional
CREATE TABLE `noticias_eventos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(200) NOT NULL,
  `resumo` VARCHAR(300) NOT NULL,
  `conteudo` TEXT NOT NULL,
  `tipo` ENUM('evento', 'noticia', 'ferias', 'destaque_aluno') NOT NULL DEFAULT 'noticia',
  `imagem_url` VARCHAR(255) DEFAULT NULL,
  `destaque` TINYINT(1) DEFAULT 0,
  `data_publicacao` DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabela de Logs e Auditoria
CREATE TABLE `system_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED DEFAULT NULL,
  `acao` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `data_hora` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- DADOS INICIAIS (SEED DATA)
-- Hash BCRYPT para senhas padrão:
-- 'admin123' => $2y$10$5Z8R6L0G/hH1a/2F.0pGveJp1VwMv7zU8fJq5Z6aX.1ZzQ8xY2Y2u
-- 'prof123'  => $2y$10$9GvF2XW9yZ8Q1V0aB.6kYuuU1f7gZ9rZ7rB2J1vM2yQ6wU4vY5xX6
-- 'aluno123' => $2y$10$2Y7V8XW9yZ8Q1V0aB.6kYuuU1f7gZ9rZ7rB2J1vM2yQ6wU4vY5xX6
-- (Observação: o arquivo install.php gerará hashes dinâmicos usando password_hash PHP)
-- =========================================================
