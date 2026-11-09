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

// --- Bloco: Carregar variaveis do arquivo .env ---
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
$porta   = $_ENV['DB_PORT'] ?? '3306';
$banco   = $_ENV['DB_NAME'] ?? 'suamarca_db';
$usuario = $_ENV['DB_USER'] ?? 'root';
$senha   = $_ENV['DB_PASS'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

echo "=========================================\n";
echo " Iniciando Migrations - SuaMarca\n";
echo "=========================================\n";

try {
    // --- Bloco: Conectar ao MySQL e criar o banco se nao existir ---
    $pdoSemBanco = new PDO("mysql:host={$host};port={$porta};charset={$charset}", $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdoSemBanco->exec("CREATE DATABASE IF NOT EXISTS `{$banco}` CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci;");
    echo "[OK] Banco de dados '{$banco}' verificado.\n";

    // --- Bloco: Conectar ao banco criado e criar tabela de controle de migrations ---
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

    // --- Bloco: Ler arquivos .sql da pasta database/migrations ---
    $arquivosSql = glob(__DIR__ . '/database/migrations/*.sql');
    sort($arquivosSql);

    foreach ($arquivosSql as $caminhoArquivo) {
        $nomeArquivo = basename($caminhoArquivo);

        // Verifica se essa migration ja rodou antes
        $verifica = $pdo->prepare("SELECT id FROM migrations WHERE arquivo = :arquivo");
        $verifica->execute([':arquivo' => $nomeArquivo]);

        if ($verifica->fetch()) {
            echo "[PULAR] {$nomeArquivo} ja foi executada.\n";
            continue;
        }

        // Le o conteudo do SQL e executa
        $sql = file_get_contents($caminhoArquivo);
        $pdo->exec($sql);

        // Registra a migration executada
        $registra = $pdo->prepare("INSERT INTO migrations (arquivo) VALUES (:arquivo)");
        $registra->execute([':arquivo' => $nomeArquivo]);

        echo "[OK] Migration executada: {$nomeArquivo}\n";
    }

    // --- Bloco: Inserir conta administrativa inicial usando transacao ACID ---
    $totalUsuarios = (int) $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

    if ($totalUsuarios === 0) {
        echo "Inserindo conta administrativa inicial com transacao ACID...\n";

        $pdo->beginTransaction();

        $senhaHash = password_hash('12345678', PASSWORD_DEFAULT);

        $stmtUsuario = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, perfil, telefone, empresa_nome, cnpj)
            VALUES (:nome, :email, :senha, :perfil, :telefone, :empresa_nome, :cnpj)
        ");

        // Conta da Empresa
        $stmtUsuario->execute([
            ':nome'         => 'Empresa Exemplo',
            ':email'        => 'admin@suamarca.com',
            ':senha'        => $senhaHash,
            ':perfil'       => 'empresa',
            ':telefone'     => '(11) 3000-1234',
            ':empresa_nome' => 'Empresa Exemplo',
            ':cnpj'         => '98.765.432/0001-10'
        ]);

        $pdo->commit();
        echo "[OK] Conta administrativa criada com sucesso!\n";
        echo "-----------------------------------------\n";
        echo " Login Empresa: admin@suamarca.com  (Senha: 12345678)\n";
        echo "-----------------------------------------\n";
    } else {
        echo "[PULAR] Banco ja possui usuarios cadastrados.\n";
    }

    // --- Bloco: Garantir coluna rascunho e limpar dados ficticios do front-end ---
    $pdo->exec("ALTER TABLE solicitacoes MODIFY COLUMN status ENUM('rascunho', 'nova', 'analise', 'orcamento-enviado', 'aceita', 'recusada') NOT NULL DEFAULT 'nova'");
    $pdo->exec("DELETE FROM solicitacoes WHERE mensagem LIKE '%rascunho de teste%'");

    // Adiciona coluna link_prototipo em projetos se ainda nao existir
    $colunasProjetos = $pdo->query("SHOW COLUMNS FROM projetos LIKE 'link_prototipo'")->fetchAll();
    if (empty($colunasProjetos)) {
        $pdo->exec("ALTER TABLE projetos ADD COLUMN link_prototipo VARCHAR(255) NULL DEFAULT NULL AFTER ultimo_ajuste");
        echo "[OK] Coluna link_prototipo adicionada em projetos.\n";
    }

    // Adiciona colunas para necessidades dinamicas de cada uma das 5 etapas
    $novasColunasEtapas = [
        'link_homologacao'      => "VARCHAR(255) NULL DEFAULT NULL AFTER link_prototipo",
        'link_producao'         => "VARCHAR(255) NULL DEFAULT NULL AFTER link_homologacao",
        'credenciais_teste'     => "VARCHAR(255) NULL DEFAULT NULL AFTER link_producao",
        'checklist_requisitos'  => "TEXT NULL DEFAULT NULL AFTER credenciais_teste",
        'checklist_homologacao' => "TEXT NULL DEFAULT NULL AFTER checklist_requisitos",
        'avaliacao_nota'        => "TINYINT NULL DEFAULT NULL AFTER checklist_homologacao",
        'avaliacao_comentario'  => "TEXT NULL DEFAULT NULL AFTER avaliacao_nota",
    ];
    foreach ($novasColunasEtapas as $col => $def) {
        $checkCol = $pdo->query("SHOW COLUMNS FROM projetos LIKE '{$col}'")->fetchAll();
        if (empty($checkCol)) {
            $pdo->exec("ALTER TABLE projetos ADD COLUMN {$col} {$def}");
            echo "[OK] Coluna {$col} adicionada em projetos.\n";
        }
    }

    // Adiciona coluna categoria em arquivos_projeto se ainda nao existir
    $colunasArquivos = $pdo->query("SHOW COLUMNS FROM arquivos_projeto LIKE 'categoria'")->fetchAll();
    if (empty($colunasArquivos)) {
        $pdo->exec("ALTER TABLE arquivos_projeto ADD COLUMN categoria VARCHAR(60) NOT NULL DEFAULT 'Geral' AFTER tipo_arquivo");
        echo "[OK] Coluna categoria adicionada em arquivos_projeto.\n";
    }

    // --- Bloco: Adicionar colunas financeiras em projetos ---
    $colunasFin = [
        'chave_pix'          => "VARCHAR(150) NULL DEFAULT NULL AFTER avaliacao_comentario",
        'tipo_chave_pix'     => "VARCHAR(30) NULL DEFAULT 'CNPJ' AFTER chave_pix",
        'status_financeiro'  => "VARCHAR(30) NOT NULL DEFAULT 'pendente' AFTER tipo_chave_pix",
    ];
    foreach ($colunasFin as $c => $d) {
        $checkFin = $pdo->query("SHOW COLUMNS FROM projetos LIKE '{$c}'")->fetchAll();
        if (empty($checkFin)) {
            $pdo->exec("ALTER TABLE projetos ADD COLUMN {$c} {$d}");
            echo "[OK] Coluna {$c} adicionada em projetos.\n";
        }
    }

    // --- Bloco: Criar tabela de historico e auditoria do projeto ---
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
    echo "[OK] Tabela historico_projetos verificada.\n";

    // --- Bloco: Criar tabela de recuperacao de senhas com token ---
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
    echo "[OK] Tabela recuperacao_senhas verificada.\n";

    // --- Bloco: Criar tabela de parcelas e pagamentos do projeto ---
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
    echo "[OK] Tabela projeto_parcelas verificada.\n";

    // --- Bloco: Criar tabela de notificacoes persistentes ---
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
    echo "[OK] Tabela notificacoes verificada.\n";

    // Corrige caracteres em etapa_atual de projetos para UTF-8 limpo
    $pdo->exec("UPDATE projetos SET etapa_atual = '2. Wireframes e protótipo' WHERE etapa_atual LIKE '%Wireframe%'");
    $pdo->exec("UPDATE projetos SET etapa_atual = '4. Testes e validação' WHERE etapa_atual LIKE '%valida%'");
    $pdo->exec("UPDATE projetos SET etapa_atual = '5. Projeto concluído' WHERE etapa_atual LIKE '%conclu%'");

    // Remove registros ficticios de 2025 que vieram do template estatico
    $pdo->exec("DELETE FROM arquivos_projeto WHERE criado_em < '2026-01-01'");
    $pdo->exec("DELETE FROM projetos WHERE codigo = '#2025-0152' OR data_inicio < '2026-01-01'");
    $pdo->exec("DELETE FROM solicitacoes WHERE criado_em < '2026-01-01'");
    $pdo->exec("DELETE FROM clientes WHERE criado_em < '2026-01-01'");
    $pdo->exec("DELETE FROM usuarios WHERE email = 'maria@email.com'");

    // Sincroniza usuarios reais com perfil 'cliente' para a tabela de clientes
    $stmtClientesReais = $pdo->query("
        SELECT u.id, u.nome, u.email, u.telefone, u.empresa_nome, u.cnpj, DATE(u.criado_em) AS data_cadastro
        FROM usuarios u
        LEFT JOIN clientes c ON c.usuario_id = u.id
        WHERE u.perfil = 'cliente' AND c.id IS NULL
    ");
    $novosClientes = $stmtClientesReais ? $stmtClientesReais->fetchAll() : [];
    $stmtInsereCliente = $pdo->prepare("
        INSERT INTO clientes (usuario_id, nome, cnpj, segmento, contato, cargo, email, telefone, status, criado_em)
        VALUES (:usuario_id, :nome, :cnpj, 'servicos', :contato, 'Cliente', :email, :telefone, 'ativo', :criado_em)
    ");
    foreach ($novosClientes as $nc) {
        $nomeEmp = trim((string) ($nc['empresa_nome'] ?? '')) !== '' ? $nc['empresa_nome'] : $nc['nome'];
        $stmtInsereCliente->execute([
            ':usuario_id' => (int) $nc['id'],
            ':nome'       => $nomeEmp,
            ':cnpj'       => $nc['cnpj'] ?: '',
            ':contato'    => $nc['nome'],
            ':email'      => $nc['email'],
            ':telefone'   => $nc['telefone'] ?: 'Não informado',
            ':criado_em'  => $nc['data_cadastro'] ?: date('Y-m-d'),
        ]);
    }

    // Atualiza os arquivos JS minificados (main.min.js e graficos.min.js)
    foreach (['main', 'graficos'] as $arqJs) {
        $origem  = __DIR__ . "/assets/js/{$arqJs}.js";
        $destino = __DIR__ . "/assets/js/{$arqJs}.min.js";
        if (file_exists($origem)) {
            $codigo = file_get_contents($origem);
            $codigo = preg_replace('!/\*.*?\*/!s', '', $codigo);
            $linhas = explode("\n", $codigo);
            $limpas = [];
            foreach ($linhas as $linha) {
                $linha = trim($linha);
                if ($linha !== '' && !str_starts_with($linha, '//')) {
                    $limpas[] = $linha;
                }
            }
            file_put_contents($destino, implode("\n", $limpas));
            echo "[OK] Arquivo minificado atualizado: assets/js/{$arqJs}.min.js\n";
        }
    }

    echo "Migrations finalizadas com sucesso!\n";

} catch (Throwable $erro) {
    // Se der qualquer erro durante a transacao, desfaz tudo (Rollback ACID)
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "[ERRO] Transacao desfeita (Rollback executado).\n";
    }
    echo "[ERRO] Falha na migration: " . $erro->getMessage() . "\n";
    exit(1);
}
