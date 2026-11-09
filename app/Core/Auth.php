<?php

namespace App\Core;

/**
 * Classe Auth
 * Cuida da sessao do usuario logado, mensagens de aviso (flash) e protecao das paginas.
 */
class Auth
{
    // iniciar sessao caso ainda nao esteja ativa
    public static function iniciarSessao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // salvar os dados do usuario na sessao apos o login
    public static function entrar(array $usuario): void
    {
        self::iniciarSessao();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }

        $_SESSION['usuario'] = [
            'id'           => (int) $usuario['id'],
            'nome'         => $usuario['nome'],
            'email'        => $usuario['email'],
            'perfil'       => $usuario['perfil'],
            'empresa_nome' => $usuario['empresa_nome'] ?? '',
            'foto'         => $usuario['foto'] ?? null,
        ];
    }

    // retornar os dados do usuario logado (ou null se nao estiver logado)
    public static function usuario(): ?array
    {
        self::iniciarSessao();
        return $_SESSION['usuario'] ?? null;
    }

    // atualizar nome e email na sessao quando o usuario edita o perfil
    public static function atualizarSessao(string $nome, string $email, string $empresaNome = ''): void
    {
        self::iniciarSessao();
        if (isset($_SESSION['usuario'])) {
            $_SESSION['usuario']['nome']         = $nome;
            $_SESSION['usuario']['email']        = $email;
            $_SESSION['usuario']['empresa_nome'] = $empresaNome;
        }
    }

    // encerrar a sessao (logout)
    public static function sair(): void
    {
        self::iniciarSessao();
        $_SESSION = [];
        session_destroy();
    }

    // proteger paginas que exigem login
    public static function exigirPerfil(string $perfilEsperado, string $urlBase): void
    {
        $usuario = self::usuario();

        if ($usuario === null) {
            self::definirMensagem('Faça login para acessar esta página.', 'warning');
            header('Location: ' . $urlBase . '/login');
            exit;
        }

        if ($usuario['perfil'] !== $perfilEsperado) {
            // Se for cliente tentando entrar na empresa (ou vice-versa), manda para a area certa
            $destino = ($usuario['perfil'] === 'empresa')
                ? $urlBase . '/empresa/dashboard'
                : $urlBase . '/cliente/inicio';
            header('Location: ' . $destino);
            exit;
        }
    }

    // mensagens rapidas (toast) entre um redirecionamento e outro
    public static function definirMensagem(string $texto, string $tipo = 'success'): void
    {
        self::iniciarSessao();
        $_SESSION['flash_toast'] = [
            'texto' => $texto,
            'tipo'  => $tipo,
        ];
    }

    public static function pegarMensagem(): ?array
    {
        self::iniciarSessao();
        if (!isset($_SESSION['flash_toast'])) {
            return null;
        }

        $msg = $_SESSION['flash_toast'];
        unset($_SESSION['flash_toast']);
        return $msg;
    }
}
