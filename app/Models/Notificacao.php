<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model Notificacao
 * Gerencia as notificações persistentes armazenadas no banco de dados.
 */
class Notificacao
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // criar nova notificacao para um usuario
    public function criar(int $usuarioId, string $titulo, string $mensagem, string $link = '', string $icone = 'bi-bell'): int
    {
        $sql = "INSERT INTO notificacoes (usuario_id, icone, titulo, mensagem, link, criado_em)
                VALUES (:uid, :icone, :titulo, :mensagem, :link, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid'      => $usuarioId,
            ':icone'    => $icone,
            ':titulo'   => $titulo,
            ':mensagem' => $mensagem,
            ':link'     => $link,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // listar notificacoes do usuario
    public function listarPorUsuario(int $usuarioId, int $limite = 10, bool $somenteNaoLidas = false): array
    {
        $where = "WHERE usuario_id = :uid";
        if ($somenteNaoLidas) {
            $where .= " AND lida_em IS NULL";
        }

        $sql = "SELECT * FROM notificacoes {$where} ORDER BY id DESC LIMIT " . (int) $limite;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $usuarioId]);

        return $stmt->fetchAll();
    }

    // contar quantas notificacoes nao lidas existem
    public function contarNaoLidas(int $usuarioId): int
    {
        $sql = "SELECT COUNT(*) FROM notificacoes WHERE usuario_id = :uid AND lida_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $usuarioId]);

        return (int) $stmt->fetchColumn();
    }

    // marcar notificacao como lida
    public function marcarComoLida(int $id, int $usuarioId): bool
    {
        $sql = "UPDATE notificacoes SET lida_em = NOW() WHERE id = :id AND usuario_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':uid' => $usuarioId]);
    }

    // marcar todas as notificacoes do usuario como lidas
    public function marcarTodasComoLidas(int $usuarioId): bool
    {
        $sql = "UPDATE notificacoes SET lida_em = NOW() WHERE usuario_id = :uid AND lida_em IS NULL";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $usuarioId]);
    }
}
