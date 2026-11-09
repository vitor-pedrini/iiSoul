<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model Cliente
 * Responsavel pelo CRUD completo da tabela `clientes` usando PDO.
 */
class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // sincronizar usuarios de perfil cliente que ainda nao tenham ficha em clientes
    public function sincronizarUsuariosClientes(): void
    {
        $sql = "INSERT INTO clientes (usuario_id, nome, cnpj, segmento, contato, email, telefone, status, criado_em)
                SELECT u.id, u.nome, COALESCE(NULLIF(u.cnpj, ''), 'Não informado'), 'tecnologia', u.nome, u.email, COALESCE(u.telefone, ''), 'prospecto', DATE(u.criado_em)
                FROM usuarios u
                LEFT JOIN clientes c ON (c.usuario_id = u.id OR c.email = u.email)
                WHERE u.perfil = 'cliente' AND c.id IS NULL";
        $this->db->exec($sql);
    }

    // listar todos os clientes com dados do projeto ativo (read)
    public function listarTodos(string $busca = ''): array
    {
        $this->sincronizarUsuariosClientes();

        if ($busca !== '') {
            $sql = "SELECT c.*,
                           p.id AS projeto_id,
                           p.nome AS projeto_nome,
                           p.etapa_atual AS projeto_etapa,
                           p.progresso AS projeto_progresso,
                           p.status AS projeto_status
                    FROM clientes c
                    LEFT JOIN projetos p ON p.cliente_id = c.id
                    WHERE c.nome LIKE :b1 OR c.email LIKE :b2 OR c.cnpj LIKE :b3 OR c.contato LIKE :b4
                    ORDER BY c.id DESC";
            $stmt = $this->db->prepare($sql);
            $termo = '%' . $busca . '%';
            $stmt->execute([':b1' => $termo, ':b2' => $termo, ':b3' => $termo, ':b4' => $termo]);
            return $stmt->fetchAll();
        }

        $sql = "SELECT c.*,
                       p.id AS projeto_id,
                       p.nome AS projeto_nome,
                       p.etapa_atual AS projeto_etapa,
                       p.progresso AS projeto_progresso,
                       p.status AS projeto_status
                FROM clientes c
                LEFT JOIN projetos p ON p.cliente_id = c.id
                ORDER BY c.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // buscar um cliente pelo id
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $cliente = $stmt->fetch();

        return $cliente ?: null;
    }

    // buscar cliente vinculado ao usuario logado
    public function buscarPorUsuarioOuEmail(int $usuarioId, string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE usuario_id = :uid OR email = :email ORDER BY id ASC LIMIT 1");
        $stmt->execute([
            ':uid'   => $usuarioId,
            ':email' => $email,
        ]);
        $cliente = $stmt->fetch();

        return $cliente ?: null;
    }

    // buscar o primeiro cliente para caso nenhum id seja passado
    public function buscarPrimeiro(): ?array
    {
        $stmt = $this->db->query("SELECT * FROM clientes ORDER BY id ASC LIMIT 1");
        $cliente = $stmt->fetch();

        return $cliente ?: null;
    }

    // cadastrar novo cliente (create)
    public function criar(array $dados): int
    {
        $sql = "INSERT INTO clientes (usuario_id, nome, cnpj, segmento, contato, email, telefone, cep, endereco, cidade, uf, status, criado_em)
                VALUES (:usuario_id, :nome, :cnpj, :segmento, :contato, :email, :telefone, :cep, :endereco, :cidade, :uf, :status, CURRENT_DATE)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $dados['usuario_id'] ?? null,
            ':nome'       => $dados['nome'],
            ':cnpj'       => $dados['cnpj'],
            ':segmento'   => $dados['segmento'] ?? 'comercio',
            ':contato'    => $dados['contato'],
            ':email'      => $dados['email'],
            ':telefone'   => $dados['telefone'],
            ':cep'        => $dados['cep'] ?? null,
            ':endereco'   => $dados['endereco'] ?? null,
            ':cidade'     => $dados['cidade'] ?? null,
            ':uf'         => $dados['uf'] ?? null,
            ':status'     => $dados['status'] ?? 'ativo',
        ]);

        return (int) $this->db->lastInsertId();
    }

    // atualizar cliente existente (update)
    public function atualizar(int $id, array $dados): bool
    {
        $sql = "UPDATE clientes
                SET nome = :nome,
                    cnpj = :cnpj,
                    segmento = :segmento,
                    contato = :contato,
                    email = :email,
                    telefone = :telefone,
                    cep = :cep,
                    endereco = :endereco,
                    cidade = :cidade,
                    uf = :uf,
                    status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'     => $dados['nome'],
            ':cnpj'     => $dados['cnpj'],
            ':segmento' => $dados['segmento'],
            ':contato'  => $dados['contato'],
            ':email'    => $dados['email'],
            ':telefone' => $dados['telefone'],
            ':cep'      => $dados['cep'] ?? null,
            ':endereco' => $dados['endereco'] ?? null,
            ':cidade'   => $dados['cidade'] ?? null,
            ':uf'       => $dados['uf'] ?? null,
            ':status'   => $dados['status'] ?? 'ativo',
            ':id'       => $id,
        ]);
    }

    // excluir cliente (delete)
    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // contar clientes ativos (para o dashboard)
    public function contarAtivos(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM clientes WHERE status = 'ativo'")->fetchColumn();
    }

    // contar total de clientes
    public function contarTodos(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    }

    // contar clientes cadastrados em cada mes do ano (jan a dez) para o grafico do dashboard
    public function contarPorMes(): array
    {
        $meses = array_fill(1, 12, 0);
        $linhas = $this->db->query("SELECT MONTH(criado_em) AS mes, COUNT(*) AS total FROM clientes GROUP BY MONTH(criado_em)")->fetchAll();

        foreach ($linhas as $l) {
            $m = (int) $l['mes'];
            if ($m >= 1 && $m <= 12) {
                $meses[$m] = (int) $l['total'];
            }
        }

        return array_values($meses);
    }
}
