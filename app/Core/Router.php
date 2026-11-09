<?php

namespace App\Core;

/**
 * Classe Router
 * Guarda a lista de rotas (GET e POST) e chama o Controller correspondente.
 */
class Router
{
    private array $rotas = [
        'GET'  => [],
        'POST' => [],
    ];

    // cadastrar rota do tipo get (rota limpa sem extensao, aceitando tambem .html e .php)
    public function get(string $caminho, array $acao): void
    {
        $this->registrarRota('GET', $caminho, $acao);
    }

    // cadastrar rota do tipo post (rota limpa sem extensao, aceitando tambem .html e .php)
    public function post(string $caminho, array $acao): void
    {
        $this->registrarRota('POST', $caminho, $acao);
    }

    // registra a rota limpa e tambem apelidos .html e .php para compatibilidade
    private function registrarRota(string $metodo, string $caminho, array $acao): void
    {
        $limpo = $caminho;
        if (str_ends_with($limpo, '.html')) {
            $limpo = substr($limpo, 0, -5);
        } elseif (str_ends_with($limpo, '.php')) {
            $limpo = substr($limpo, 0, -4);
        }

        if ($limpo === '' || $limpo === '/index') {
            $limpo = '/';
        }

        $this->rotas[$metodo][$limpo] = $acao;

        if ($limpo !== '/') {
            $this->rotas[$metodo][$limpo . '.html'] = $acao;
            $this->rotas[$metodo][$limpo . '.php']  = $acao;
        } else {
            $this->rotas[$metodo]['/index']      = $acao;
            $this->rotas[$metodo]['/index.html'] = $acao;
            $this->rotas[$metodo]['/index.php']  = $acao;
        }
    }

    // descobrir qual url o usuario acessou e chamar o controller
    public function executar(string $metodo, string $uri, string $caminhoBase): void
    {
        // Pega apenas o caminho da URL (sem ?id=1 etc.)
        $caminho = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Remove o caminho da pasta do projeto (ex: /projetos/IISoul)
        if ($caminhoBase !== '' && str_starts_with($caminho, $caminhoBase)) {
            $caminho = substr($caminho, strlen($caminhoBase));
        }

        $caminho = '/' . ltrim(rtrim($caminho, '/'), '/');
        if ($caminho === '/index.php' || $caminho === '/index.html' || $caminho === '/index') {
            $caminho = '/';
        }

        // Procura a rota na lista
        $acao = $this->rotas[$metodo][$caminho] ?? null;

        if ($acao === null) {
            http_response_code(404);
            echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><title>Página não encontrada</title>';
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></head>';
            echo '<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">';
            echo '<div class="text-center p-4"><h1 class="display-4 fw-bold">404</h1><p class="lead">Página não encontrada.</p>';
            echo '<a href="' . htmlspecialchars($caminhoBase ?: '/', ENT_QUOTES, 'UTF-8') . '" class="btn btn-primary">Voltar para o início</a></div></body></html>';
            return;
        }

        [$classeController, $nomeMetodo] = $acao;
        $controller = new $classeController($caminhoBase);
        $controller->$nomeMetodo();
    }
}
