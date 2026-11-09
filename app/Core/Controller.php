<?php

namespace App\Core;

use App\Models\Cliente;
use App\Models\Notificacao;
use App\Models\Projeto;
use App\Models\Solicitacao;
use Throwable;

/**
 * Classe base Controller
 * Contem funcoes auxiliares para carregar as Views (telas) e redirecionar.
 */
abstract class Controller
{
    protected string $urlBase;

    public function __construct(string $urlBase = '')
    {
        $this->urlBase = $urlBase;
        Auth::iniciarSessao();
    }

    // montar notificacoes persistentes e dinamicas do topo
    protected function montarNotificacoesTopo(?array $usuarioAtual, array $dadosView = []): array
    {
        if (!$usuarioAtual) {
            return [];
        }

        $notificacoes = [];
        $uid = (int) ($usuarioAtual['id'] ?? 0);

        try {
            // 1. Prioriza notificacoes persistentes gravadas no banco de dados
            if ($uid > 0) {
                $notifModel = new Notificacao();
                $persistentes = $notifModel->listarPorUsuario($uid, 8);
                if (!empty($persistentes)) {
                    foreach ($persistentes as $np) {
                        $notificacoes[] = [
                            'id'        => (int) $np['id'],
                            'icone'     => $np['icone'] ?? 'bi-bell text-primary',
                            'texto'     => $np['titulo'] . ' · ' . $np['mensagem'],
                            'link'      => !empty($np['link']) ? $np['link'] : '#',
                            'lida'      => !empty($np['lida_em']),
                            'criado_em' => $np['criado_em'],
                        ];
                    }
                }
            }

            // 2. Se nao houver notificacoes persistentes suficientes, complementa com analise dinamica
            if (count($notificacoes) < 3) {
                if (($usuarioAtual['perfil'] ?? '') === 'empresa') {
                    $solModel  = new Solicitacao();
                    $cliModel  = new Cliente();
                    $projModel = new Projeto();

                    $projetos = $projModel->listarTodos();
                    foreach ($projetos as $proj) {
                        if (!empty($proj['ultimo_ajuste'])) {
                            $notificacoes[] = [
                                'icone' => 'bi-pencil-square text-danger',
                                'texto' => 'Ajuste em ' . $proj['nome'] . ' (' . ($proj['cliente_nome'] ?? 'Cliente') . ')',
                                'link'  => 'projeto?id=' . (int) $proj['id'],
                            ];
                        } elseif ((int)$proj['progresso'] > 20) {
                            $notificacoes[] = [
                                'icone' => 'bi-kanban text-primary',
                                'texto' => $proj['nome'] . ' está em ' . $proj['etapa_atual'] . ' (' . (int)$proj['progresso'] . '%)',
                                'link'  => 'projeto?id=' . (int) $proj['id'],
                            ];
                        }
                    }

                    $pendentes = $solModel->listarPendentes(2);
                    foreach ($pendentes as $p) {
                        $icone = ($p['status'] === 'orcamento-enviado') ? 'bi-clock text-warning' : 'bi-file-text text-primary';
                        $notificacoes[] = [
                            'icone' => $icone,
                            'texto' => 'Solicitação de ' . $p['nome'] . ' (' . $p['tipo_texto'] . ')',
                            'link'  => 'solicitacoes',
                        ];
                    }
                } else {
                    $email = (string) ($usuarioAtual['email'] ?? '');
                    $projeto = $dadosView['projeto'] ?? null;
                    $ultimaSolicitacao = $dadosView['ultimaSolicitacao'] ?? null;

                    if ($projeto === null && $ultimaSolicitacao === null && $uid > 0) {
                        $cliModel = new Cliente();
                        $projModel = new Projeto();
                        $solModel = new Solicitacao();

                        $ficha = $cliModel->buscarPorUsuarioOuEmail($uid, $email);
                        if ($ficha) {
                            $projeto = $projModel->buscarProjetoDoCliente((int) $ficha['id']);
                        }
                        $ultimaSolicitacao = $solModel->buscarUltimaDoUsuario($uid, $email);
                    }

                    if (!empty($projeto)) {
                        $notificacoes[] = [
                            'icone' => 'bi-folder-check text-primary',
                            'texto' => 'Projeto ' . $projeto['nome'] . ': ' . $projeto['etapa_atual'] . ' (' . (int) $projeto['progresso'] . '%)',
                            'link'  => 'projeto',
                        ];
                    }
                }
            }
        } catch (Throwable $e) {
            // Ignora falhas para nao interromper a renderizacao da tela
        }

        return $notificacoes;
    }

    // carregar um arquivo de view da pasta app/views
    protected function renderizar(string $view, array $dados = []): void
    {
        $dados['urlBase']          = $this->urlBase;
        $dados['usuarioAtual']     = Auth::usuario();
        $dados['flashToast']       = Auth::pegarMensagem();
        $dados['campoCsrf']        = Csrf::campoHidden();
        $dados['notificacoesTopo'] = $this->montarNotificacoesTopo($dados['usuarioAtual'], $dados);

        $uid = (int) ($dados['usuarioAtual']['id'] ?? 0);
        $notifModel = new Notificacao();
        $dados['totalNotifsNaoLidas'] = ($uid > 0) ? $notifModel->contarNaoLidas($uid) : 0;

        // Transforma as chaves do array em variaveis para usar no HTML
        extract($dados);

        $caminhoView = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (file_exists($caminhoView)) {
            require $caminhoView;
        } else {
            echo "Erro: View {$view} nao encontrada.";
        }
    }

    // redirecionar para outra rota com mensagem toast opcional
    protected function redirecionar(string $caminho, ?string $mensagem = null, string $tipo = 'success'): void
    {
        if ($mensagem !== null) {
            Auth::definirMensagem($mensagem, $tipo);
        }

        header('Location: ' . $this->urlBase . $caminho);
        exit;
    }

    // validar o token csrf em requisicoes post
    protected function validarCsrfOuVoltar(string $caminhoRetorno): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!Csrf::validar($token)) {
            $this->redirecionar($caminhoRetorno, 'Sessão expirada. Tente enviar o formulário novamente.', 'danger');
        }
    }
}
