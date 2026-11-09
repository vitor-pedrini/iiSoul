<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

/**
 * Model Solicitacao
 * Gerencia os pedidos de orcamento, questionarios e a conversao em cliente (com transacao ACID).
 */
class Solicitacao
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // listar todas as solicitacoes
    public function listarTodas(): array
    {
        $sql = "SELECT * FROM solicitacoes ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // listar solicitacoes de um cliente ou e-mail especifico
    public function listarPorCliente(int $clienteId, string $email = '', ?int $usuarioId = null): array
    {
        $sql = "SELECT * FROM solicitacoes
                WHERE cliente_id = :cid
                   OR (email != '' AND email = :email)
                   OR (:uid IS NOT NULL AND usuario_id = :uid2)
                ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cid'   => $clienteId,
            ':email' => $email,
            ':uid'   => $usuarioId,
            ':uid2'  => $usuarioId,
        ]);
        return $stmt->fetchAll();
    }

    // buscar a solicitacao mais recente de um usuario
    public function buscarUltimaDoUsuario(int $usuarioId, string $email = ''): ?array
    {
        $sql = "SELECT * FROM solicitacoes
                WHERE usuario_id = :uid OR (email != '' AND email = :email)
                ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid'   => $usuarioId,
            ':email' => $email,
        ]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }

    // buscar uma solicitacao pelo id
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM solicitacoes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        return $linha ?: null;
    }

    // contar solicitacoes por status (para os cards do dashboard)
    public function contarPorStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM solicitacoes WHERE status = :status");
        $stmt->execute([':status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    // listar solicitacoes que precisam de atencao no dashboard
    public function listarPendentes(int $limite = 5): array
    {
        $sql = "SELECT * FROM solicitacoes
                WHERE status IN ('nova', 'analise', 'orcamento-enviado')
                ORDER BY id ASC LIMIT " . (int) $limite;
        return $this->db->query($sql)->fetchAll();
    }

    // criar ou atualizar solicitacao / rascunho
    public function criar(array $dados): int
    {
        $nomesTipos = [
            'website' => 'Website Institucional',
            'app'     => 'Aplicativo Mobile',
            'sistema' => 'Sistema Interno',
            'loja'    => 'Loja Virtual',
        ];
        $faixasTipos = [
            'website' => 'R$ 5.000 – 10.000',
            'app'     => 'R$ 20.000 – 40.000',
            'sistema' => 'R$ 15.000 – 30.000',
            'loja'    => 'R$ 10.000 – 20.000',
        ];

        $tipo = $dados['tipo'] ?? 'website';
        $tipoTexto = $dados['tipo_texto'] ?? ($nomesTipos[$tipo] ?? 'Website Institucional');
        $faixa = $dados['faixa'] ?? ($faixasTipos[$tipo] ?? 'R$ 5.000 – 10.000');
        $status = $dados['status'] ?? 'nova';

        // Se o usuario ja tinha um rascunho ou solicitacao em aberto e esta salvando novamente, atualiza o registro existente
        if (!empty($dados['id_existente'])) {
            $sqlUp = "UPDATE solicitacoes
                      SET tipo = :tipo,
                          tipo_texto = :tipo_texto,
                          faixa = :faixa,
                          prazo = :prazo,
                          objetivo = :objetivo,
                          publico_alvo = :publico_alvo,
                          modulos = :modulos,
                          referencias = :referencias,
                          mensagem = :mensagem,
                          status = :status
                      WHERE id = :id";
            $stmtUp = $this->db->prepare($sqlUp);
            $stmtUp->execute([
                ':tipo'         => $tipo,
                ':tipo_texto'   => $tipoTexto,
                ':faixa'        => $faixa,
                ':prazo'        => $dados['prazo'] ?? '1 a 3 meses',
                ':objetivo'     => $dados['objetivo'] ?? null,
                ':publico_alvo' => $dados['publico_alvo'] ?? null,
                ':modulos'      => $dados['modulos'] ?? null,
                ':referencias'  => $dados['referencias'] ?? null,
                ':mensagem'     => $dados['mensagem'] ?? '',
                ':status'       => $status,
                ':id'           => (int) $dados['id_existente'],
            ]);
            return (int) $dados['id_existente'];
        }

        $sql = "INSERT INTO solicitacoes
                (usuario_id, cliente_id, nome, email, telefone, tipo, tipo_texto, faixa, prazo, objetivo, publico_alvo, modulos, referencias, mensagem, status, criado_em)
                VALUES
                (:usuario_id, :cliente_id, :nome, :email, :telefone, :tipo, :tipo_texto, :faixa, :prazo, :objetivo, :publico_alvo, :modulos, :referencias, :mensagem, :status, CURRENT_DATE)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id'   => $dados['usuario_id'] ?? null,
            ':cliente_id'   => $dados['cliente_id'] ?? null,
            ':nome'         => $dados['nome'],
            ':email'        => $dados['email'],
            ':telefone'     => $dados['telefone'] ?? '(11) 90000-0000',
            ':tipo'         => $tipo,
            ':tipo_texto'   => $tipoTexto,
            ':faixa'        => $faixa,
            ':prazo'        => $dados['prazo'] ?? '1 a 3 meses',
            ':objetivo'     => $dados['objetivo'] ?? null,
            ':publico_alvo' => $dados['publico_alvo'] ?? null,
            ':modulos'      => $dados['modulos'] ?? null,
            ':referencias'  => $dados['referencias'] ?? null,
            ':mensagem'     => $dados['mensagem'] ?? '',
            ':status'       => $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // salvar anotacao interna
    public function salvarAnotacao(int $id, string $anotacao): bool
    {
        $stmt = $this->db->prepare("UPDATE solicitacoes SET anotacao_interna = :anotacao WHERE id = :id");
        return $stmt->execute([':anotacao' => $anotacao, ':id' => $id]);
    }

    // registrar envio de orcamento
    public function registrarOrcamento(int $id, float $valor, string $validade, string $observacoes): bool
    {
        $sql = "UPDATE solicitacoes
                SET valor_orcamento = :valor,
                    validade_orcamento = :validade,
                    anotacao_interna = COALESCE(NULLIF(:obs, ''), anotacao_interna),
                    status = 'orcamento-enviado'
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':valor'    => $valor,
            ':validade' => $validade,
            ':obs'      => $observacoes,
            ':id'       => $id,
        ]);
    }

    // recusar solicitacao
    public function recusar(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE solicitacoes SET status = 'recusada' WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // converter solicitacao em cliente + projeto usando transacao acid
    public function converterEmCliente(int $idSolicitacao): ?int
    {
        $solicitacao = $this->buscarPorId($idSolicitacao);
        if (!$solicitacao) {
            return null;
        }

        try {
            // Inicia a transacao ACID: ativa/cria o cliente, cria o projeto e atualiza a solicitacao juntos
            $this->db->beginTransaction();

            // 1. Verifica se o solicitante ja possui ficha na tabela clientes
            $novoClienteId = !empty($solicitacao['cliente_id']) ? (int) $solicitacao['cliente_id'] : 0;

            if ($novoClienteId === 0 && !empty($solicitacao['usuario_id'])) {
                $stmtBuscaCli = $this->db->prepare("SELECT id FROM clientes WHERE usuario_id = :uid LIMIT 1");
                $stmtBuscaCli->execute([':uid' => $solicitacao['usuario_id']]);
                $novoClienteId = (int) ($stmtBuscaCli->fetchColumn() ?: 0);
            }

            if ($novoClienteId > 0) {
                $stmtAtivaCli = $this->db->prepare("UPDATE clientes SET status = 'ativo', telefone = COALESCE(NULLIF(:tel, ''), telefone) WHERE id = :id");
                $stmtAtivaCli->execute([
                    ':tel' => $solicitacao['telefone'] ?? '',
                    ':id'  => $novoClienteId,
                ]);
            } else {
                $stmtCliente = $this->db->prepare("
                    INSERT INTO clientes (usuario_id, nome, cnpj, segmento, contato, email, telefone, status, criado_em)
                    VALUES (:usuario_id, :nome, :cnpj, 'tecnologia', :contato, :email, :telefone, 'ativo', CURRENT_DATE)
                ");
                $stmtCliente->execute([
                    ':usuario_id' => $solicitacao['usuario_id'],
                    ':nome'       => $solicitacao['nome'],
                    ':cnpj'       => '00.000.000/0001-' . str_pad((string) ($idSolicitacao % 99), 2, '0', STR_PAD_LEFT),
                    ':contato'    => $solicitacao['nome'],
                    ':email'      => $solicitacao['email'],
                    ':telefone'   => $solicitacao['telefone'] ?: '(11) 90000-0000',
                ]);
                $novoClienteId = (int) $this->db->lastInsertId();
            }

            // 2. Cria um projeto inicial para esse cliente
            $stmtProjeto = $this->db->prepare("
                INSERT INTO projetos (cliente_id, codigo, nome, tipo, descricao, etapa_atual, progresso, status, valor_orcamento, data_inicio, prazo_entrega)
                VALUES (:cliente_id, :codigo, :nome, :tipo, :descricao, 'Planejamento', 20, 'planejamento', :valor, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY))
            ");
            $stmtProjeto->execute([
                ':cliente_id' => $novoClienteId,
                ':codigo'     => '#2026-' . str_pad((string) $novoClienteId, 4, '0', STR_PAD_LEFT),
                ':nome'       => $solicitacao['tipo_texto'],
                ':tipo'       => $solicitacao['tipo_texto'],
                ':descricao'  => trim((string) $solicitacao['mensagem'], '"'),
                ':valor'      => $solicitacao['valor_orcamento'] ?: 8500.00,
            ]);

            // 3. Marca a solicitacao como aceita e vincula ao cliente criado
            $stmtAtualiza = $this->db->prepare("
                UPDATE solicitacoes SET status = 'aceita', cliente_id = :cliente_id WHERE id = :id
            ");
            $stmtAtualiza->execute([
                ':cliente_id' => $novoClienteId,
                ':id'         => $idSolicitacao,
            ]);

            // Confirma todas as operacoes no banco (Commit ACID)
            $this->db->commit();
            return $novoClienteId;
        } catch (Throwable $erro) {
            // Se qualquer passo falhar, desfaz tudo (Rollback ACID)
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return null;
        }
    }
}
