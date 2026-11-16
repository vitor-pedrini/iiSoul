<?php
/**
 * migrate.php
 * ---------------------------------------------------------
 * Script para criar o banco de dados, rodar as Migrations
 * e popular os dados iniciais usando transacoes ACID (PDO).
 *
 * Como usar no terminal:
 *   php migrate.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carregar variaveis do arquivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
$porta   = $_ENV['DB_PORT'] ?? '3306';
$banco   = $_ENV['DB_NAME'] ?? 'suamarca_db';
$usuario = $_ENV['DB_USER'] ?? 'root';
$senha   = $_ENV['DB_PASS'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

echo "=========================================\n";
echo " Iniciando Migrations - iiKliente\n";
echo "=========================================\n";

try {
    // Conectar ao MySQL e criar o banco se nao existir
    $pdoSemBanco = new PDO("mysql:host={$host};port={$porta};charset={$charset}", $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdoSemBanco->exec("CREATE DATABASE IF NOT EXISTS `{$banco}` CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci;");
    echo "[OK] Banco de dados '{$banco}' verificado.\n";

    // Conectar ao banco criado e criar tabela de controle de migrations
    $pdo = new PDO("mysql:host={$host};port={$porta};dbname={$banco};charset={$charset}", $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            arquivo VARCHAR(150) NOT NULL UNIQUE,
            executado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Ler arquivos .sql da pasta database/migrations
    $arquivosSql = glob(__DIR__ . '/database/migrations/*.sql');
    sort($arquivosSql);

    foreach ($arquivosSql as $caminhoArquivo) {
        $nomeArquivo = basename($caminhoArquivo);

        $verifica = $pdo->prepare("SELECT id FROM migrations WHERE arquivo = :arquivo");
        $verifica->execute([':arquivo' => $nomeArquivo]);

        if ($verifica->fetch()) {
            echo "[PULAR] {$nomeArquivo} ja foi executada.\n";
            continue;
        }

        $sql = file_get_contents($caminhoArquivo);
        $pdo->exec($sql);

        $registra = $pdo->prepare("INSERT INTO migrations (arquivo) VALUES (:arquivo)");
        $registra->execute([':arquivo' => $nomeArquivo]);

        echo "[OK] Migration executada: {$nomeArquivo}\n";
    }

    // Garantir colunas adicionais essenciais
    $pdo->exec("ALTER TABLE solicitacoes MODIFY COLUMN status ENUM('rascunho', 'nova', 'analise', 'orcamento-enviado', 'aceita', 'recusada') NOT NULL DEFAULT 'nova'");

    $colunasProjetos = $pdo->query("SHOW COLUMNS FROM projetos LIKE 'link_prototipo'")->fetchAll();
    if (empty($colunasProjetos)) {
        $pdo->exec("ALTER TABLE projetos ADD COLUMN link_prototipo VARCHAR(255) NULL DEFAULT NULL AFTER ultimo_ajuste");
        echo "[OK] Coluna link_prototipo adicionada em projetos.\n";
    }

    $novasColunasEtapas = [
        'link_homologacao'      => "VARCHAR(255) NULL DEFAULT NULL AFTER link_prototipo",
        'link_producao'         => "VARCHAR(255) NULL DEFAULT NULL AFTER link_homologacao",
        'credenciais_teste'     => "VARCHAR(255) NULL DEFAULT NULL AFTER link_producao",
        'checklist_requisitos'  => "TEXT NULL DEFAULT NULL AFTER credenciais_teste",
        'checklist_homologacao' => "TEXT NULL DEFAULT NULL AFTER checklist_requisitos",
        'avaliacao_nota'        => "TINYINT NULL DEFAULT NULL AFTER checklist_homologacao",
        'avaliacao_comentario'  => "TEXT NULL DEFAULT NULL AFTER avaliacao_nota",
        'chave_pix'             => "VARCHAR(150) NULL DEFAULT NULL AFTER avaliacao_comentario",
        'tipo_chave_pix'        => "VARCHAR(30) NULL DEFAULT 'CNPJ' AFTER chave_pix",
        'status_financeiro'     => "VARCHAR(30) NOT NULL DEFAULT 'pendente' AFTER tipo_chave_pix",
    ];
    foreach ($novasColunasEtapas as $col => $def) {
        $checkCol = $pdo->query("SHOW COLUMNS FROM projetos LIKE '{$col}'")->fetchAll();
        if (empty($checkCol)) {
            $pdo->exec("ALTER TABLE projetos ADD COLUMN {$col} {$def}");
            echo "[OK] Coluna {$col} adicionada em projetos.\n";
        }
    }

    $colunasArquivos = $pdo->query("SHOW COLUMNS FROM arquivos_projeto LIKE 'categoria'")->fetchAll();
    if (empty($colunasArquivos)) {
        $pdo->exec("ALTER TABLE arquivos_projeto ADD COLUMN categoria VARCHAR(60) NOT NULL DEFAULT 'Geral' AFTER tipo_arquivo");
        echo "[OK] Coluna categoria adicionada em arquivos_projeto.\n";
    }

    // Tabelas complementares com InnoDB
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS historico_projetos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            projeto_id INT NOT NULL,
            usuario_id INT NULL,
            perfil VARCHAR(20) NOT NULL DEFAULT 'sistema',
            autor_nome VARCHAR(100) NOT NULL,
            acao VARCHAR(100) NOT NULL,
            descricao TEXT NULL,
            etapa_de VARCHAR(100) NULL,
            etapa_para VARCHAR(100) NULL,
            criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_hist_projeto (projeto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS recuperacao_senhas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            email VARCHAR(150) NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            expira_em DATETIME NOT NULL,
            usado_em DATETIME NULL DEFAULT NULL,
            criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_recup_token (token),
            INDEX idx_recup_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projeto_parcelas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            projeto_id INT NOT NULL,
            numero_parcela INT NOT NULL DEFAULT 1,
            descricao VARCHAR(100) NOT NULL,
            valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            data_vencimento DATE NULL,
            data_pagamento DATE NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pendente',
            comprovante VARCHAR(255) NULL,
            observacoes TEXT NULL,
            criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_parc_projeto (projeto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notificacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            icone VARCHAR(50) NOT NULL DEFAULT 'bi-bell',
            titulo VARCHAR(150) NOT NULL,
            mensagem VARCHAR(255) NOT NULL,
            link VARCHAR(255) NULL,
            lida_em DATETIME NULL DEFAULT NULL,
            criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_notif_usuario (usuario_id, lida_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Seed de Usuarios e Dados de Teste Garantidos
    $senhaHash = password_hash('12345678', PASSWORD_DEFAULT);

    // 1. Garante conta da Empresa (admin@iikliente.com)
    $stmtAdmin = $pdo->prepare("SELECT id FROM usuarios WHERE email = 'admin@iikliente.com'");
    $stmtAdmin->execute();
    $adminId = $stmtAdmin->fetchColumn();

    if (!$adminId) {
        $stmtInsAdmin = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, perfil, telefone, empresa_nome, cnpj)
            VALUES (:nome, :email, :senha, 'empresa', :telefone, :empresa_nome, :cnpj)
        ");
        $stmtInsAdmin->execute([
            ':nome'         => 'Empresa Administradora',
            ':email'        => 'admin@iikliente.com',
            ':senha'        => $senhaHash,
            ':telefone'     => '(11) 3000-1234',
            ':empresa_nome' => 'iiKliente Tecnologia',
            ':cnpj'         => '98.765.432/0001-10'
        ]);
        $adminId = $pdo->lastInsertId();
        echo "[OK] Usuario Administrador criado: admin@iikliente.com\n";
    }

    // 2. Garante conta do Cliente (cliente@iikliente.com)
    $stmtCli = $pdo->prepare("SELECT id FROM usuarios WHERE email = 'cliente@iikliente.com'");
    $stmtCli->execute();
    $clienteUsuarioId = $stmtCli->fetchColumn();

    if (!$clienteUsuarioId) {
        $stmtInsCli = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, perfil, telefone, empresa_nome, cnpj)
            VALUES (:nome, :email, :senha, 'cliente', :telefone, :empresa_nome, :cnpj)
        ");
        $stmtInsCli->execute([
            ':nome'         => 'Maria Silva',
            ':email'        => 'cliente@iikliente.com',
            ':senha'        => $senhaHash,
            ':telefone'     => '(11) 98765-4321',
            ':empresa_nome' => 'Cafeteria Aroma & Sabor',
            ':cnpj'         => '12.345.678/0001-90'
        ]);
        $clienteUsuarioId = $pdo->lastInsertId();
        echo "[OK] Usuario Cliente criado: cliente@iikliente.com\n";
    }

    // 3. Garante registro de Cliente na tabela clientes
    $stmtRegCli = $pdo->prepare("SELECT id FROM clientes WHERE usuario_id = :uid OR email = 'cliente@iikliente.com'");
    $stmtRegCli->execute([':uid' => $clienteUsuarioId]);
    $clienteId = $stmtRegCli->fetchColumn();

    if (!$clienteId) {
        $stmtInsRegCli = $pdo->prepare("
            INSERT INTO clientes (usuario_id, nome, cnpj, segmento, contato, cargo, email, telefone, cep, endereco, cidade, uf, status, criado_em)
            VALUES (:usuario_id, :nome, :cnpj, 'comercio', :contato, 'Proprietaria', :email, :telefone, '01001-000', 'Praca da Se, 100', 'Sao Paulo', 'SP', 'ativo', CURRENT_DATE)
        ");
        $stmtInsRegCli->execute([
            ':usuario_id' => $clienteUsuarioId,
            ':nome'       => 'Cafeteria Aroma & Sabor',
            ':cnpj'       => '12.345.678/0001-90',
            ':contato'    => 'Maria Silva',
            ':email'      => 'cliente@iikliente.com',
            ':telefone'   => '(11) 98765-4321',
        ]);
        $clienteId = $pdo->lastInsertId();
        echo "[OK] Registro de cliente criado na tabela clientes.\n";
    }

    // 4. Garante Projeto Ativo com 5 Etapas e Dados para Teste
    $stmtProj = $pdo->prepare("SELECT id FROM projetos WHERE cliente_id = :cid");
    $stmtProj->execute([':cid' => $clienteId]);
    $projetoId = $stmtProj->fetchColumn();

    if (!$projetoId) {
        $stmtInsProj = $pdo->prepare("
            INSERT INTO projetos (cliente_id, codigo, nome, tipo, descricao, etapa_atual, progresso, status, valor_orcamento, data_inicio, prazo_entrega, link_prototipo, chave_pix, tipo_chave_pix, status_financeiro)
            VALUES (:cliente_id, '#2026-001', :nome, 'Website e Catalogo Digital', :descricao, '2. Wireframes e prototipo', 40, 'desenvolvimento', 8500.00, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), 'https://www.figma.com', '12.345.678/0001-90', 'CNPJ', 'pendente')
        ");
        $stmtInsProj->execute([
            ':cliente_id' => $clienteId,
            ':nome'       => 'Portal Web Cafeteria Aroma',
            ':descricao'  => 'Desenvolvimento de plataforma responsiva completa com cardapio digital e integracao de pedidos.',
        ]);
        $projetoId = $pdo->lastInsertId();
        echo "[OK] Projeto de demonstracao criado: #2026-001\n";

        // Cria parcelas financeiras
        $pdo->exec("
            INSERT INTO projeto_parcelas (projeto_id, numero_parcela, descricao, valor, data_vencimento, status)
            VALUES 
            ({$projetoId}, 1, 'Entrada / Aprovacao do Briefing', 4250.00, CURRENT_DATE, 'pago'),
            ({$projetoId}, 2, 'Entrega Final e Homologacao', 4250.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), 'pendente')
        ");

        // Cria historico inicial de auditoria
        $pdo->exec("
            INSERT INTO historico_projetos (projeto_id, usuario_id, perfil, autor_nome, acao, descricao, etapa_de, etapa_para)
            VALUES 
            ({$projetoId}, {$adminId}, 'empresa', 'Vitor Hugo Pedrini', 'criacao_projeto', 'Projeto criado e iniciado com sucesso.', NULL, '1. Briefing e Requisitos'),
            ({$projetoId}, {$adminId}, 'empresa', 'Vitor Hugo Pedrini', 'avanco_etapa', 'Briefing concluido e aprovado. Iniciado wireframe interativo.', '1. Briefing e Requisitos', '2. Wireframes e prototipo')
        ");

        // Cria notificacao de boas-vindas
        $pdo->exec("
            INSERT INTO notificacoes (usuario_id, icone, titulo, mensagem, link)
            VALUES 
            ({$clienteUsuarioId}, 'bi-stars', 'Bem-vindo ao iiKliente!', 'Seu projeto #2026-001 esta disponivel para acompanhamento.', '/cliente/projeto')
        ");
        echo "[OK] Parcelas financeiras, historico e notificacao inicial criados.\n";
    }

    echo "=========================================\n";
    echo " Migrations Concluidas com Sucesso!\n";
    echo "-----------------------------------------\n";
    echo " Admin / Empresa : admin@iikliente.com   (Senha: 12345678)\n";
    echo " Cliente Teste   : cliente@iikliente.com (Senha: 12345678)\n";
    echo "=========================================\n";

} catch (Throwable $erro) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "[ERRO] Transacao desfeita.\n";
    }
    echo "[ERRO] Falha na migration: " . $erro->getMessage() . "\n";
    exit(1);
}
