<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model Projeto
 * Consulta e atualiza os dados do projeto e os arquivos enviados pelo cliente.
 */
class Projeto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // buscar o projeto exclusivo de um cliente especifico
    public function buscarProjetoDoCliente(?int $clienteId = null): ?array
    {
        if ($clienteId === null || $clienteId <= 0) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM projetos WHERE cliente_id = :cid ORDER BY id DESC LIMIT 1");
        $stmt->execute([':cid' => $clienteId]);
        $proj = $stmt->fetch();

        return $proj ?: null;
    }

    // criar um projeto inicial para um cliente (comecando do inicio: planejamento / 20%)
    public function criarInicialParaCliente(int $clienteId, string $nomeProjeto = 'Website Institucional', float $valorOrcamento = 0.00, string $descricao = ''): int
    {
        $codigo = '#2026-' . str_pad((string) $clienteId, 4, '0', STR_PAD_LEFT);
        $descFinal = ($descricao !== '')
            ? $descricao
            : 'Projeto iniciado no sistema. Aguardando definição das primeiras entregas.';

        $sql = "INSERT INTO projetos
                (cliente_id, codigo, nome, tipo, descricao, etapa_atual, progresso, status, valor_orcamento, data_inicio, prazo_entrega)
                VALUES
                (:cid, :codigo, :nome, :tipo, :descricao, 'Planejamento', 20, 'planejamento', :valor, CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 35 DAY))";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cid'       => $clienteId,
            ':codigo'    => $codigo,
            ':nome'      => $nomeProjeto,
            ':tipo'      => $nomeProjeto,
            ':descricao' => $descFinal,
            ':valor'     => $valorOrcamento,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // contar projetos em andamento
    public function contarEmAndamento(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM projetos WHERE status != 'concluido'")->fetchColumn();
    }

    // listar todos os projetos com busca e filtros
    public function listarTodos(string $busca = '', string $filtroEtapa = ''): array
    {
        $sql = "SELECT p.*,
                       c.id AS cliente_id,
                       c.nome AS cliente_nome,
                       c.email AS cliente_email,
                       c.telefone AS cliente_telefone
                FROM projetos p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                WHERE 1=1";
        $params = [];

        if ($busca !== '') {
            $sql .= " AND (p.nome LIKE :b1 OR p.codigo LIKE :b2 OR c.nome LIKE :b3 OR c.email LIKE :b4)";
            $params[':b1'] = '%' . $busca . '%';
            $params[':b2'] = '%' . $busca . '%';
            $params[':b3'] = '%' . $busca . '%';
            $params[':b4'] = '%' . $busca . '%';
        }

        if ($filtroEtapa !== '' && $filtroEtapa !== 'todas') {
            if ($filtroEtapa === 'ajuste') {
                $sql .= " AND (p.ultimo_ajuste IS NOT NULL AND p.ultimo_ajuste != '')";
            } elseif (is_numeric($filtroEtapa)) {
                $sql .= " AND p.progresso = :etapaProg";
                $params[':etapaProg'] = (int) $filtroEtapa;
            } else {
                $sql .= " AND p.status = :st";
                $params[':st'] = $filtroEtapa;
            }
        }

        $sql .= " ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // buscar ficha completa de um projeto pelo id
    public function buscarPorId(int $projetoId): ?array
    {
        $sql = "SELECT p.*,
                       c.id AS cliente_id,
                       c.nome AS cliente_nome,
                       c.email AS cliente_email,
                       c.telefone AS cliente_telefone,
                       c.cnpj AS cliente_cnpj,
                       c.contato AS cliente_contato,
                       c.segmento AS cliente_segmento,
                       c.usuario_id AS cliente_usuario_id
                FROM projetos p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                WHERE p.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $projetoId]);
        $proj = $stmt->fetch();

        return $proj ?: null;
    }

    // listar os arquivos mais recentes enviados nos projetos (para o dashboard)
    public function listarArquivosRecentes(int $limite = 3): array
    {
        $sql = "SELECT a.*, p.nome AS projeto_nome
                FROM arquivos_projeto a
                LEFT JOIN projetos p ON p.id = a.projeto_id
                ORDER BY a.id DESC LIMIT " . (int) $limite;
        return $this->db->query($sql)->fetchAll();
    }

    // contar projetos por etapa (para o grafico de rosca do dashboard)
    public function contarPorStatus(): array
    {
        $contagem = [
            'planejamento'    => 0,
            'desenvolvimento' => 0,
            'revisao'         => 0,
            'aguardando'      => 0,
            'concluido'       => 0,
        ];

        $linhas = $this->db->query("SELECT status, COUNT(*) AS total FROM projetos GROUP BY status")->fetchAll();
        foreach ($linhas as $l) {
            if (isset($contagem[$l['status']])) {
                $contagem[$l['status']] = (int) $l['total'];
            }
        }

        return $contagem;
    }

    // definir os dados da etapa conforme a porcentagem (20%, 40%, 60%, 80%, 100%)
    public function atualizarEtapa(int $projetoId, int $novoProgresso): bool
    {
        $mapaEtapas = [
            20  => ['etapa' => '1. Levantamento de requisitos', 'status' => 'planejamento',    'aprovada' => 0],
            40  => ['etapa' => '2. Wireframes e protótipo',     'status' => 'planejamento',    'aprovada' => 0],
            60  => ['etapa' => '3. Em desenvolvimento',         'status' => 'desenvolvimento', 'aprovada' => 0],
            80  => ['etapa' => '4. Testes e validação',         'status' => 'revisao',         'aprovada' => 0],
            100 => ['etapa' => '5. Projeto concluído',          'status' => 'concluido',       'aprovada' => 1],
        ];

        $dadosEtapa = $mapaEtapas[$novoProgresso] ?? $mapaEtapas[20];

        $sql = "UPDATE projetos
                SET progresso = :progresso,
                    etapa_atual = :etapa,
                    status = :status,
                    etapa_aprovada = :aprovada
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':progresso' => $novoProgresso,
            ':etapa'     => $dadosEtapa['etapa'],
            ':status'    => $dadosEtapa['status'],
            ':aprovada'  => $dadosEtapa['aprovada'],
            ':id'        => $projetoId,
        ]);
    }

    // aprovar etapa atual do projeto e avancar para a proxima fase
    public function aprovarEtapa(int $projetoId): bool
    {
        $stmtBusca = $this->db->prepare("SELECT progresso FROM projetos WHERE id = :id LIMIT 1");
        $stmtBusca->execute([':id' => $projetoId]);
        $atual = (int) ($stmtBusca->fetchColumn() ?: 20);
        $proximo = min(100, $atual + 20);

        return $this->atualizarEtapa($projetoId, $proximo);
    }

    // registrar pedido de ajuste do cliente
    public function solicitarAjuste(int $projetoId, string $textoAjuste): bool
    {
        $sql = "UPDATE projetos
                SET ultimo_ajuste = :ajuste,
                    etapa_atual = 'Ajuste solicitado pelo cliente',
                    etapa_aprovada = 0,
                    status = 'revisao'
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ajuste' => $textoAjuste,
            ':id'     => $projetoId,
        ]);
    }

    // resolver ajuste do cliente e retornar projeto para desenvolvimento
    public function resolverAjuste(int $projetoId): bool
    {
        $stmtBusca = $this->db->prepare("SELECT progresso FROM projetos WHERE id = :id LIMIT 1");
        $stmtBusca->execute([':id' => $projetoId]);
        $prog = (int) ($stmtBusca->fetchColumn() ?: 20);

        return $this->atualizarEtapa($projetoId, $prog);
    }

    // atualizar link externo do prototipo (figma/invision/url)
    public function atualizarLinkPrototipo(int $projetoId, ?string $link): bool
    {
        $stmt = $this->db->prepare("UPDATE projetos SET link_prototipo = :link WHERE id = :id");
        return $stmt->execute([
            ':link' => !empty($link) ? trim($link) : null,
            ':id'   => $projetoId,
        ]);
    }

    // atualizar links das etapas (protótipo/figma, homologação/testes, produção/oficial e credenciais)
    public function atualizarLinksEtapas(int $projetoId, ?string $prototipo, ?string $homologacao, ?string $producao, ?string $credenciais = null): bool
    {
        $stmt = $this->db->prepare("
            UPDATE projetos
            SET link_prototipo = :proto,
                link_homologacao = :homolog,
                link_producao = :prod,
                credenciais_teste = :cred
            WHERE id = :id
        ");
        return $stmt->execute([
            ':proto'   => !empty($prototipo) ? trim($prototipo) : null,
            ':homolog' => !empty($homologacao) ? trim($homologacao) : null,
            ':prod'    => !empty($producao) ? trim($producao) : null,
            ':cred'    => !empty($credenciais) ? trim($credenciais) : null,
            ':id'      => $projetoId,
        ]);
    }

    // atualizar checklist da etapa de requisitos
    public function atualizarChecklistRequisitos(int $projetoId, array $itens): bool
    {
        $json = json_encode($itens, JSON_UNESCAPED_UNICODE);
        $stmt = $this->db->prepare("UPDATE projetos SET checklist_requisitos = :c WHERE id = :id");
        return $stmt->execute([':c' => $json, ':id' => $projetoId]);
    }

    // atualizar checklist da etapa de homologação / testes
    public function atualizarChecklistHomologacao(int $projetoId, array $itens): bool
    {
        $json = json_encode($itens, JSON_UNESCAPED_UNICODE);
        $stmt = $this->db->prepare("UPDATE projetos SET checklist_homologacao = :c WHERE id = :id");
        return $stmt->execute([':c' => $json, ':id' => $projetoId]);
    }

    // salvar avaliação de satisfação do cliente (etapa 5 - concluído)
    public function salvarAvaliacao(int $projetoId, int $nota, string $comentario): bool
    {
        $stmt = $this->db->prepare("
            UPDATE projetos
            SET avaliacao_nota = :nota,
                avaliacao_comentario = :comentario
            WHERE id = :id
        ");
        return $stmt->execute([
            ':nota'       => max(1, min(5, $nota)),
            ':comentario' => trim($comentario),
            ':id'         => $projetoId,
        ]);
    }

    // atualizar detalhes do projeto (nome, tipo, descricao, prazo, valor)
    public function atualizarDetalhes(int $projetoId, string $nome, string $tipo, ?string $descricao, ?string $prazoEntrega, float $valorOrcamento): bool
    {
        $stmt = $this->db->prepare("
            UPDATE projetos
            SET nome = :nome,
                tipo = :tipo,
                descricao = :descricao,
                prazo_entrega = :prazo,
                valor_orcamento = :valor
            WHERE id = :id
        ");
        return $stmt->execute([
            ':nome'      => $nome,
            ':tipo'      => $tipo,
            ':descricao' => $descricao,
            ':prazo'     => !empty($prazoEntrega) ? $prazoEntrega : null,
            ':valor'     => $valorOrcamento,
            ':id'        => $projetoId,
        ]);
    }

    // listar arquivos enviados no projeto
    public function listarArquivos(int $projetoId): array
    {
        $sql = "SELECT a.*, u.nome AS usuario_nome, u.perfil AS usuario_perfil
                FROM arquivos_projeto a
                LEFT JOIN usuarios u ON u.id = a.usuario_id
                WHERE a.projeto_id = :pid
                ORDER BY a.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $projetoId]);
        return $stmt->fetchAll();
    }

    // buscar um arquivo especifico pelo id
    public function buscarArquivoPorId(int $arquivoId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM arquivos_projeto WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $arquivoId]);
        $arq = $stmt->fetch();

        return $arq ?: null;
    }

    // salvar registro de novo arquivo enviado com categoria
    public function salvarArquivo(int $projetoId, ?int $usuarioId, string $titulo, string $nomeOriginal, string $nomeSalvo, string $extensao, int $tamanhoKb, string $categoria = 'Geral'): int
    {
        $sql = "INSERT INTO arquivos_projeto (projeto_id, usuario_id, titulo, nome_original, nome_salvo, tipo_arquivo, categoria, tamanho_kb, criado_em)
                VALUES (:pid, :uid, :titulo, :original, :salvo, :tipo, :cat, :kb, CURRENT_DATE)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid'      => $projetoId,
            ':uid'      => $usuarioId,
            ':titulo'   => $titulo,
            ':original' => $nomeOriginal,
            ':salvo'    => $nomeSalvo,
            ':tipo'     => $extensao,
            ':cat'      => $categoria,
            ':kb'       => $tamanhoKb,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // excluir arquivo do projeto
    public function excluirArquivo(int $arquivoId, int $projetoId): bool
    {
        $stmt = $this->db->prepare("SELECT nome_salvo FROM arquivos_projeto WHERE id = :id AND projeto_id = :pid LIMIT 1");
        $stmt->execute([':id' => $arquivoId, ':pid' => $projetoId]);
        $nomeSalvo = $stmt->fetchColumn();

        if ($nomeSalvo) {
            $caminho = dirname(__DIR__, 2) . '/storage/uploads/' . $nomeSalvo;
            if (file_exists($caminho)) {
                @unlink($caminho);
            }
        }

        $stmtDel = $this->db->prepare("DELETE FROM arquivos_projeto WHERE id = :id AND projeto_id = :pid");
        return $stmtDel->execute([':id' => $arquivoId, ':pid' => $projetoId]);
    }

    // listar todos os projetos de um cliente (suporte a multiplos projetos)
    public function listarTodosDoCliente(int $clienteId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM projetos WHERE cliente_id = :cid ORDER BY id DESC");
        $stmt->execute([':cid' => $clienteId]);
        return $stmt->fetchAll();
    }

    // registrar evento na linha do tempo / historico de auditoria
    public function registrarHistorico(
        int $projetoId,
        ?int $usuarioId,
        string $perfil,
        string $autorNome,
        string $acao,
        ?string $descricao = null,
        ?string $etapaDe = null,
        ?string $etapaPara = null
    ): int {
        $sql = "INSERT INTO historico_projetos
                (projeto_id, usuario_id, perfil, autor_nome, acao, descricao, etapa_de, etapa_para, criado_em)
                VALUES
                (:pid, :uid, :perfil, :autor, :acao, :desc, :ede, :epara, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pid'    => $projetoId,
            ':uid'    => $usuarioId,
            ':perfil' => $perfil,
            ':autor'  => $autorNome,
            ':acao'   => $acao,
            ':desc'   => $descricao,
            ':ede'    => $etapaDe,
            ':epara'  => $etapaPara,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // listar historico de eventos do projeto (ordem cronologica)
    public function listarHistorico(int $projetoId, int $limite = 40): array
    {
        $sql = "SELECT * FROM historico_projetos
                WHERE projeto_id = :pid
                ORDER BY id DESC
                LIMIT " . (int) $limite;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $projetoId]);
        return $stmt->fetchAll();
    }

    // listar parcelas financeiras do projeto
    public function listarParcelas(int $projetoId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM projeto_parcelas WHERE projeto_id = :pid ORDER BY numero_parcela ASC, id ASC");
        $stmt->execute([':pid' => $projetoId]);
        return $stmt->fetchAll();
    }

    // salvar/gerar parcelas do projeto
    public function salvarParcelas(int $projetoId, array $parcelas): bool
    {
        $this->db->beginTransaction();
        try {
            // Remove parcelas existentes nao pagas
            $this->db->prepare("DELETE FROM projeto_parcelas WHERE projeto_id = :pid AND status = 'pendente'")->execute([':pid' => $projetoId]);

            $sql = "INSERT INTO projeto_parcelas (projeto_id, numero_parcela, descricao, valor, data_vencimento, status)
                    VALUES (:pid, :num, :desc, :val, :venc, :status)";
            $stmt = $this->db->prepare($sql);

            foreach ($parcelas as $p) {
                $stmt->execute([
                    ':pid'    => $projetoId,
                    ':num'    => (int) ($p['numero'] ?? 1),
                    ':desc'   => trim($p['descricao'] ?? 'Parcela'),
                    ':val'    => (float) ($p['valor'] ?? 0),
                    ':venc'   => !empty($p['data_vencimento']) ? $p['data_vencimento'] : null,
                    ':status' => $p['status'] ?? 'pendente',
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    // atualizar status de uma parcela
    public function atualizarStatusParcela(int $parcelaId, string $status, ?string $dataPagamento = null): bool
    {
        $sql = "UPDATE projeto_parcelas
                SET status = :st,
                    data_pagamento = :dt
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':st' => $status,
            ':dt' => ($status === 'pago') ? ($dataPagamento ?: date('Y-m-d')) : null,
            ':id' => $parcelaId,
        ]);
    }

    // salvar comprovante de pagamento da parcela enviado pelo cliente
    public function enviarComprovanteParcela(int $parcelaId, string $nomeArquivo): bool
    {
        $sql = "UPDATE projeto_parcelas
                SET comprovante = :comp,
                    status = 'em_analise'
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':comp' => $nomeArquivo,
            ':id'   => $parcelaId,
        ]);
    }

    // atualizar chave pix e tipo do projeto
    public function atualizarConfigPix(int $projetoId, ?string $chavePix, string $tipoChave = 'CNPJ'): bool
    {
        $sql = "UPDATE projetos SET chave_pix = :pix, tipo_chave_pix = :tipo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pix'  => $chavePix,
            ':tipo' => $tipoChave,
            ':id'   => $projetoId,
        ]);
    }

    // atualizar status financeiro geral do projeto
    public function atualizarStatusFinanceiro(int $projetoId, string $status): bool
    {
        $sql = "UPDATE projetos SET status_financeiro = :st WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':st' => $status,
            ':id' => $projetoId,
        ]);
    }
}
