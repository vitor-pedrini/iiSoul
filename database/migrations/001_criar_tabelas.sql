-- =========================================================
-- Migration 001: Criacao das tabelas do sistema SuaMarca
-- Todas as tabelas usam ENGINE=InnoDB para garantir ACID
-- (transacoes seguras e integridade referencial com chaves estrangeiras).
-- =========================================================

-- --- Bloco: Tabela de Usuarios (Login da Empresa e do Cliente) ---
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('empresa', 'cliente') NOT NULL DEFAULT 'cliente',
    telefone VARCHAR(25) DEFAULT NULL,
    empresa_nome VARCHAR(100) DEFAULT NULL,
    cnpj VARCHAR(20) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    notif_solicitacao TINYINT(1) NOT NULL DEFAULT 1,
    notif_aprovacao TINYINT(1) NOT NULL DEFAULT 1,
    notif_email TINYINT(1) NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Bloco: Tabela de Clientes (CRUD principal da area da empresa) ---
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT DEFAULT NULL,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(20) NOT NULL,
    segmento VARCHAR(40) NOT NULL DEFAULT 'comercio',
    contato VARCHAR(100) NOT NULL,
    cargo VARCHAR(80) DEFAULT 'Responsável',
    email VARCHAR(120) NOT NULL,
    telefone VARCHAR(25) NOT NULL,
    cep VARCHAR(12) DEFAULT NULL,
    endereco VARCHAR(200) DEFAULT NULL,
    cidade VARCHAR(80) DEFAULT NULL,
    uf VARCHAR(2) DEFAULT NULL,
    site VARCHAR(150) DEFAULT NULL,
    observacoes TEXT DEFAULT NULL,
    status ENUM('ativo', 'prospecto', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT fk_cliente_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Bloco: Tabela de Solicitacoes e Questionarios ---
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT DEFAULT NULL,
    cliente_id INT DEFAULT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    telefone VARCHAR(25) DEFAULT NULL,
    tipo VARCHAR(40) NOT NULL DEFAULT 'website',
    tipo_texto VARCHAR(80) NOT NULL DEFAULT 'Website Institucional',
    faixa VARCHAR(60) NOT NULL DEFAULT 'R$ 5.000 – 10.000',
    prazo VARCHAR(40) DEFAULT '1 a 3 meses',
    objetivo VARCHAR(100) DEFAULT NULL,
    publico_alvo VARCHAR(150) DEFAULT NULL,
    modulos VARCHAR(255) DEFAULT NULL,
    referencias VARCHAR(255) DEFAULT NULL,
    mensagem TEXT DEFAULT NULL,
    anotacao_interna TEXT DEFAULT NULL,
    valor_orcamento DECIMAL(10,2) DEFAULT NULL,
    validade_orcamento DATE DEFAULT NULL,
    status ENUM('rascunho', 'nova', 'analise', 'orcamento-enviado', 'aceita', 'recusada') NOT NULL DEFAULT 'nova',
    criado_em DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT fk_solicitacao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_solicitacao_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Bloco: Tabela de Projetos ---
CREATE TABLE IF NOT EXISTS projetos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    codigo VARCHAR(30) NOT NULL DEFAULT '#2025-0152',
    nome VARCHAR(120) NOT NULL,
    tipo VARCHAR(60) NOT NULL DEFAULT 'Website Institucional',
    descricao TEXT DEFAULT NULL,
    etapa_atual VARCHAR(60) NOT NULL DEFAULT 'Em desenvolvimento',
    progresso INT NOT NULL DEFAULT 60,
    status ENUM('planejamento', 'desenvolvimento', 'revisao', 'aguardando', 'concluido') NOT NULL DEFAULT 'desenvolvimento',
    etapa_aprovada TINYINT(1) NOT NULL DEFAULT 0,
    ultimo_ajuste TEXT DEFAULT NULL,
    valor_orcamento DECIMAL(10,2) NOT NULL DEFAULT 8750.00,
    data_inicio DATE NOT NULL DEFAULT (CURRENT_DATE),
    prazo_entrega DATE DEFAULT NULL,
    CONSTRAINT fk_projeto_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Bloco: Tabela de Arquivos Enviados no Projeto ---
CREATE TABLE IF NOT EXISTS arquivos_projeto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    usuario_id INT DEFAULT NULL,
    titulo VARCHAR(120) NOT NULL,
    nome_original VARCHAR(150) NOT NULL,
    nome_salvo VARCHAR(150) NOT NULL,
    tipo_arquivo VARCHAR(20) NOT NULL,
    tamanho_kb INT NOT NULL DEFAULT 0,
    criado_em DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT fk_arquivo_projeto FOREIGN KEY (projeto_id) REFERENCES projetos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
