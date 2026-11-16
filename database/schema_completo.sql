-- =========================================================
-- iiKliente - Schema Completo e Dados Iniciais de Teste
-- Compativel com MySQL / MariaDB (XAMPP phpMyAdmin)
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `suamarca_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `suamarca_db`;

-- 1. Tabela de Usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `perfil` ENUM('empresa', 'cliente') NOT NULL DEFAULT 'cliente',
    `telefone` VARCHAR(25) DEFAULT NULL,
    `empresa_nome` VARCHAR(100) DEFAULT NULL,
    `cnpj` VARCHAR(20) DEFAULT NULL,
    `foto` VARCHAR(255) DEFAULT NULL,
    `notif_solicitacao` TINYINT(1) NOT NULL DEFAULT 1,
    `notif_aprovacao` TINYINT(1) NOT NULL DEFAULT 1,
    `notif_email` TINYINT(1) NOT NULL DEFAULT 0,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela de Clientes
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT DEFAULT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `cnpj` VARCHAR(20) NOT NULL,
    `segmento` VARCHAR(40) NOT NULL DEFAULT 'comercio',
    `contato` VARCHAR(100) NOT NULL,
    `cargo` VARCHAR(80) DEFAULT 'Responsavel',
    `email` VARCHAR(120) NOT NULL,
    `telefone` VARCHAR(25) NOT NULL,
    `cep` VARCHAR(12) DEFAULT NULL,
    `endereco` VARCHAR(200) DEFAULT NULL,
    `cidade` VARCHAR(80) DEFAULT NULL,
    `uf` VARCHAR(2) DEFAULT NULL,
    `site` VARCHAR(150) DEFAULT NULL,
    `observacoes` TEXT DEFAULT NULL,
    `status` ENUM('ativo', 'prospecto', 'inativo') NOT NULL DEFAULT 'ativo',
    `criado_em` DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela de Solicitacoes
CREATE TABLE IF NOT EXISTS `solicitacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT DEFAULT NULL,
    `cliente_id` INT DEFAULT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `telefone` VARCHAR(25) DEFAULT NULL,
    `tipo` VARCHAR(40) NOT NULL DEFAULT 'website',
    `tipo_texto` VARCHAR(80) NOT NULL DEFAULT 'Website Institucional',
    `faixa` VARCHAR(60) NOT NULL DEFAULT 'R$ 5.000 - 10.000',
    `prazo` VARCHAR(40) DEFAULT '1 a 3 meses',
    `objetivo` VARCHAR(100) DEFAULT NULL,
    `publico_alvo` VARCHAR(150) DEFAULT NULL,
    `modulos` VARCHAR(255) DEFAULT NULL,
    `referencias` VARCHAR(255) DEFAULT NULL,
    `mensagem` TEXT DEFAULT NULL,
    `anotacao_interna` TEXT DEFAULT NULL,
    `valor_orcamento` DECIMAL(10,2) DEFAULT NULL,
    `validade_orcamento` DATE DEFAULT NULL,
    `status` ENUM('rascunho', 'nova', 'analise', 'orcamento-enviado', 'aceita', 'recusada') NOT NULL DEFAULT 'nova',
    `criado_em` DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT `fk_solicitacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_solicitacao_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabela de Projetos
CREATE TABLE IF NOT EXISTS `projetos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT NOT NULL,
    `codigo` VARCHAR(30) NOT NULL DEFAULT '#2026-001',
    `nome` VARCHAR(120) NOT NULL,
    `tipo` VARCHAR(60) NOT NULL DEFAULT 'Website Institucional',
    `descricao` TEXT DEFAULT NULL,
    `etapa_atual` VARCHAR(60) NOT NULL DEFAULT '2. Wireframes e prototipo',
    `progresso` INT NOT NULL DEFAULT 40,
    `status` ENUM('planejamento', 'desenvolvimento', 'revisao', 'aguardando', 'concluido') NOT NULL DEFAULT 'desenvolvimento',
    `etapa_aprovada` TINYINT(1) NOT NULL DEFAULT 0,
    `ultimo_ajuste` TEXT DEFAULT NULL,
    `link_prototipo` VARCHAR(255) DEFAULT 'https://www.figma.com',
    `link_homologacao` VARCHAR(255) DEFAULT NULL,
    `link_producao` VARCHAR(255) DEFAULT NULL,
    `credenciais_teste` VARCHAR(255) DEFAULT NULL,
    `checklist_requisitos` TEXT DEFAULT NULL,
    `checklist_homologacao` TEXT DEFAULT NULL,
    `avaliacao_nota` TINYINT DEFAULT NULL,
    `avaliacao_comentario` TEXT DEFAULT NULL,
    `chave_pix` VARCHAR(150) DEFAULT '12.345.678/0001-90',
    `tipo_chave_pix` VARCHAR(30) DEFAULT 'CNPJ',
    `status_financeiro` VARCHAR(30) NOT NULL DEFAULT 'pendente',
    `valor_orcamento` DECIMAL(10,2) NOT NULL DEFAULT 8500.00,
    `data_inicio` DATE NOT NULL DEFAULT (CURRENT_DATE),
    `prazo_entrega` DATE DEFAULT NULL,
    CONSTRAINT `fk_projeto_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabela de Arquivos do Projeto
CREATE TABLE IF NOT EXISTS `arquivos_projeto` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `projeto_id` INT NOT NULL,
    `usuario_id` INT DEFAULT NULL,
    `titulo` VARCHAR(120) NOT NULL,
    `nome_original` VARCHAR(150) NOT NULL,
    `nome_salvo` VARCHAR(150) NOT NULL,
    `tipo_arquivo` VARCHAR(20) NOT NULL,
    `categoria` VARCHAR(60) NOT NULL DEFAULT 'Geral',
    `tamanho_kb` INT NOT NULL DEFAULT 0,
    `criado_em` DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT `fk_arquivo_projeto` FOREIGN KEY (`projeto_id`) REFERENCES `projetos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabela de Historico / Auditoria (Audit Trail)
CREATE TABLE IF NOT EXISTS `historico_projetos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `projeto_id` INT NOT NULL,
    `usuario_id` INT NULL,
    `perfil` VARCHAR(20) NOT NULL DEFAULT 'sistema',
    `autor_nome` VARCHAR(100) NOT NULL,
    `acao` VARCHAR(100) NOT NULL,
    `descricao` TEXT NULL,
    `etapa_de` VARCHAR(100) NULL,
    `etapa_para` VARCHAR(100) NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_hist_projeto` (`projeto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabela de Recuperacao de Senhas com Token
CREATE TABLE IF NOT EXISTS `recuperacao_senhas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `expira_em` DATETIME NOT NULL,
    `usado_em` DATETIME NULL DEFAULT NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_recup_token` (`token`),
    INDEX `idx_recup_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabela de Parcelas Financeiras
CREATE TABLE IF NOT EXISTS `projeto_parcelas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `projeto_id` INT NOT NULL,
    `numero_parcela` INT NOT NULL DEFAULT 1,
    `descricao` VARCHAR(100) NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `data_vencimento` DATE NULL,
    `data_pagamento` DATE NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'pendente',
    `comprovante` VARCHAR(255) NULL,
    `observacoes` TEXT NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_parc_projeto` (`projeto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabela de Notificacoes Persistentes
CREATE TABLE IF NOT EXISTS `notificacoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `icone` VARCHAR(50) NOT NULL DEFAULT 'bi-bell',
    `titulo` VARCHAR(150) NOT NULL,
    `mensagem` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) NULL,
    `lida_em` DATETIME NULL DEFAULT NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_usuario` (`usuario_id`, `lida_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Insercao de Dados Iniciais de Teste (Senha padrao: 12345678)
-- Hash Bcrypt de '12345678': $2y$10$wTqS9n4vYmQoX9YwH/b/2.4D6a6F5.6bVb2e6T3e5a5B6c7D8E9F0
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `telefone`, `empresa_nome`, `cnpj`) VALUES
(1, 'Empresa Administradora', 'admin@iikliente.com', '$2y$10$1WbO/ZhyjVqg/0Zvh1uJ5O6v43aPkWt/Bf49bF0Q6G2h7l9dE3g6y', 'empresa', '(11) 3000-1234', 'iiKliente Tecnologia', '98.765.432/0001-10'),
(2, 'Maria Silva', 'cliente@iikliente.com', '$2y$10$1WbO/ZhyjVqg/0Zvh1uJ5O6v43aPkWt/Bf49bF0Q6G2h7l9dE3g6y', 'cliente', '(11) 98765-4321', 'Cafeteria Aroma & Sabor', '12.345.678/0001-90')
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);

INSERT INTO `clientes` (`id`, `usuario_id`, `nome`, `cnpj`, `segmento`, `contato`, `cargo`, `email`, `telefone`, `cep`, `endereco`, `cidade`, `uf`, `status`, `criado_em`) VALUES
(1, 2, 'Cafeteria Aroma & Sabor', '12.345.678/0001-90', 'comercio', 'Maria Silva', 'Proprietaria', 'cliente@iikliente.com', '(11) 98765-4321', '01001-000', 'Praca da Se, 100', 'Sao Paulo', 'SP', 'ativo', CURRENT_DATE)
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);

INSERT INTO `projetos` (`id`, `cliente_id`, `codigo`, `nome`, `tipo`, `descricao`, `etapa_atual`, `progresso`, `status`, `valor_orcamento`, `data_inicio`, `prazo_entrega`, `link_prototipo`, `chave_pix`, `tipo_chave_pix`, `status_financeiro`) VALUES
(1, 1, '#2026-001', 'Portal Web Cafeteria Aroma', 'Website e Catalogo Digital', 'Desenvolvimento de plataforma responsiva completa com cardapio digital e integracao de pedidos.', '2. Wireframes e prototipo', 40, 'desenvolvimento', 8500.00, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), 'https://www.figma.com', '12.345.678/0001-90', 'CNPJ', 'pendente')
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);

INSERT INTO `projeto_parcelas` (`id`, `projeto_id`, `numero_parcela`, `descricao`, `valor`, `data_vencimento`, `status`) VALUES
(1, 1, 1, 'Entrada / Aprovacao do Briefing', 4250.00, CURRENT_DATE, 'pago'),
(2, 1, 2, 'Entrega Final e Homologacao', 4250.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 'pendente')
ON DUPLICATE KEY UPDATE `descricao` = VALUES(`descricao`);

INSERT INTO `historico_projetos` (`id`, `projeto_id`, `usuario_id`, `perfil`, `autor_nome`, `acao`, `descricao`, `etapa_de`, `etapa_para`) VALUES
(1, 1, 1, 'empresa', 'Vitor Hugo Pedrini', 'criacao_projeto', 'Projeto criado e iniciado com sucesso.', NULL, '1. Briefing e Requisitos'),
(2, 1, 1, 'empresa', 'Vitor Hugo Pedrini', 'avanco_etapa', 'Briefing concluido e aprovado. Iniciado wireframe interativo.', '1. Briefing e Requisitos', '2. Wireframes e prototipo')
ON DUPLICATE KEY UPDATE `acao` = VALUES(`acao`);

INSERT INTO `notificacoes` (`id`, `usuario_id`, `icone`, `titulo`, `mensagem`, `link`) VALUES
(1, 2, 'bi-stars', 'Bem-vindo ao iiKliente!', 'Seu projeto #2026-001 esta disponivel para acompanhamento.', '/cliente/projeto')
ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);

SET FOREIGN_KEY_CHECKS = 1;
