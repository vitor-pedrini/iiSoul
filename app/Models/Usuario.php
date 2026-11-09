<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model Usuario
 * Faz as consultas e gravacoes na tabela `usuarios`.
 */
class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // buscar usuario pelo e-mail (usado no login)
    public function buscarPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $linha = $stmt->fetch();

        return $linha ?: null;
    }

    // buscar usuario pelo id
    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        return $linha ?: null;
    }

    // criar novo usuario (usado no criar conta)
    public function criar(string $nome, string $email, string $senhaPura, string $perfil = 'cliente'): int
    {
        $senhaHash = password_hash($senhaPura, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':senha'  => $senhaHash,
            ':perfil' => $perfil,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // atualizar dados do perfil ou da empresa
    public function atualizarDados(int $id, array $dados): bool
    {
        $sql = "UPDATE usuarios
                SET nome = :nome,
                    email = :email,
                    telefone = :telefone,
                    empresa_nome = :empresa_nome,
                    cnpj = :cnpj,
                    foto = COALESCE(:foto, foto)
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'         => $dados['nome'],
            ':email'        => $dados['email'],
            ':telefone'     => $dados['telefone'] ?? null,
            ':empresa_nome' => $dados['empresa_nome'] ?? null,
            ':cnpj'         => $dados['cnpj'] ?? null,
            ':foto'         => $dados['foto'] ?? null,
            ':id'           => $id,
        ]);
    }

    // atualizar preferencias de notificacao
    public function atualizarNotificacoes(int $id, int $solicitacao, int $aprovacao, int $emailResumo): bool
    {
        $sql = "UPDATE usuarios
                SET notif_solicitacao = :s,
                    notif_aprovacao = :a,
                    notif_email = :e
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':s'  => $solicitacao,
            ':a'  => $aprovacao,
            ':e'  => $emailResumo,
            ':id' => $id,
        ]);
    }

    // alterar senha conferindo a senha atual
    public function alterarSenha(int $id, string $senhaAtual, string $senhaNova): bool
    {
        $usuario = $this->buscarPorId($id);
        if (!$usuario || !password_verify($senhaAtual, $usuario['senha'])) {
            return false;
        }

        $novoHash = password_hash($senhaNova, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        return $stmt->execute([
            ':senha' => $novoHash,
            ':id'    => $id,
        ]);
    }

    // criar token para recuperacao de senha (valido por 1 hora)
    public function criarTokenRecuperacao(int $usuarioId, string $email): string
    {
        $token = bin2hex(random_bytes(32));
        $expiraEm = date('Y-m-d H:i:s', time() + 3600); // 1 hora de validade

        // Invalida tokens anteriores nao usados para o mesmo email
        $this->db->prepare("DELETE FROM recuperacao_senhas WHERE email = :email AND usado_em IS NULL")->execute([':email' => $email]);

        $sql = "INSERT INTO recuperacao_senhas (usuario_id, email, token, expira_em, criado_em)
                VALUES (:uid, :email, :token, :expira, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid'    => $usuarioId,
            ':email'  => $email,
            ':token'  => $token,
            ':expira' => $expiraEm,
        ]);

        return $token;
    }

    // buscar registro de recuperacao por token valido
    public function buscarTokenValido(string $token): ?array
    {
        $sql = "SELECT * FROM recuperacao_senhas
                WHERE token = :token
                  AND usado_em IS NULL
                  AND expira_em >= NOW()
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        $linha = $stmt->fetch();

        return $linha ?: null;
    }

    // redefinir senha usando o token validado
    public function redefinirSenhaComToken(string $token, string $novaSenha): bool
    {
        $reg = $this->buscarTokenValido($token);
        if (!$reg) {
            return false;
        }

        $usuarioId = (int) $reg['usuario_id'];
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET senha = :senha WHERE id = :uid");
            $stmt->execute([':senha' => $hash, ':uid' => $usuarioId]);

            $stmt2 = $this->db->prepare("UPDATE recuperacao_senhas SET usado_em = NOW() WHERE id = :id");
            $stmt2->execute([':id' => (int) $reg['id']]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}
