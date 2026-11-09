<?php

namespace App\Core;

/**
 * Classe Csrf
 * Gera e valida o token CSRF nos formularios POST para evitar ataques externos.
 */
class Csrf
{
    // gerar ou retornar o token atual da sessao
    public static function gerarToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    // criar o campo <input type="hidden"> pronto para o html
    public static function campoHidden(): string
    {
        $token = self::gerarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    // conferir se o token enviado no post bate com o da sessao
    public static function validar(?string $tokenEnviado): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($tokenEnviado)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $tokenEnviado);
    }
}
