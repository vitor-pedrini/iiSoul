<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Core\UploadValidador;
use App\Models\Cliente;
use App\Models\Notificacao;
use App\Models\Projeto;
use App\Models\Solicitacao;
use App\Models\Usuario;

/**
 * EmpresaController
 * Controla todas as telas e acoes da Area da Empresa:
 * Dashboard, CRUD de Clientes, Ficha do Cliente, Solicitacoes/Orcamentos e Configuracoes.
 */
class EmpresaController extends Controller
{
    public function __construct(string $urlBase = '')
    {
        parent::__construct($urlBase);
        // trava se nao for perfil empresa
        Auth::exigirPerfil('empresa', $this->urlBase);
    }

    // puxa os dados do painel da empresa
    public function dashboard(): void
    {
        $clienteModel     = new Cliente();
        $solicitacaoModel = new Solicitacao();
        $projetoModel     = new Projeto();

        $clientes     = $clienteModel->listarTodos();
        $solicitacoes = $solicitacaoModel->listarTodas();
        $projetos     = $projetoModel->listarTodos();
        $arquivosRec  = $projetoModel->listarArquivosRecentes(3);

        $totalAtivos      = $clienteModel->contarAtivos();
        $novasSolicitacoes= $solicitacaoModel->contarPorStatus('nova') + $solicitacaoModel->contarPorStatus('analise');
        $orcamentosSemResp= $solicitacaoModel->contarPorStatus('orcamento-enviado');
        $projetosAndamento= $projetoModel->contarEmAndamento();
        $itensAtencao     = $solicitacaoModel->listarPendentes(5);
        $etapasGrafico    = $projetoModel->contarPorStatus();
        $clientesPorMes   = $clienteModel->contarPorMes();

        // Montar a lista "Atualizacoes e aprovacoes" 100% com registros reais do banco
        $atualizacoesRecentes = [];

        foreach ($projetos as $proj) {
            $progProjAt = (int) ($proj['progresso'] ?? 20);
            if (!empty($proj['ultimo_ajuste'])) {
                $atualizacoesRecentes[] = [
                    'cor'       => 'text-warning',
                    'titulo'    => 'Ajuste solicitado pelo cliente',
                    'subtitulo' => $proj['nome'] . ' (' . ($proj['cliente_nome'] ?? 'Cliente') . ') · ' . $proj['ultimo_ajuste'],
                    'data'      => 'Aguardando revisão',
                ];
            }

            $cor = ($progProjAt >= 100) ? 'text-success' : 'text-primary';
            $tituloAt = ($progProjAt >= 100)
                ? 'Projeto 100% Concluído'
                : 'Projeto em ' . $proj['etapa_atual'] . ' (' . $progProjAt . '%)';
            $atualizacoesRecentes[] = [
                'cor'       => $cor,
                'titulo'    => $tituloAt,
                'subtitulo' => $proj['nome'] . (!empty($proj['cliente_nome']) ? ' · ' . $proj['cliente_nome'] : ''),
                'data'      => !empty($proj['data_inicio']) ? 'Início: ' . date('d/m/Y', strtotime($proj['data_inicio'])) : 'Ativo',
            ];
        }

        foreach ($solicitacoes as $sol) {
            if (count($atualizacoesRecentes) >= 5) {
                break;
            }
            if ($sol['status'] === 'aceita') {
                $atualizacoesRecentes[] = [
                    'cor'       => 'text-success',
                    'titulo'    => 'Orçamento aprovado',
                    'subtitulo' => $sol['nome'] . ' (' . $sol['tipo_texto'] . ')',
                    'data'      => date('d/m/Y', strtotime($sol['criado_em'])),
                ];
            } elseif ($sol['status'] === 'orcamento-enviado') {
                $atualizacoesRecentes[] = [
                    'cor'       => 'text-primary',
                    'titulo'    => 'Orçamento enviado',
                    'subtitulo' => $sol['nome'] . ' · R$ ' . number_format((float) ($sol['valor_orcamento'] ?? 0), 2, ',', '.'),
                    'data'      => date('d/m/Y', strtotime($sol['criado_em'])),
                ];
            } elseif ($sol['status'] === 'rascunho') {
                $atualizacoesRecentes[] = [
                    'cor'       => 'text-info',
                    'titulo'    => 'Rascunho de questionário salvo',
                    'subtitulo' => $sol['nome'] . ' (' . $sol['tipo_texto'] . ')',
                    'data'      => date('d/m/Y', strtotime($sol['criado_em'])),
                ];
            } else {
                $atualizacoesRecentes[] = [
                    'cor'       => 'text-warning',
                    'titulo'    => 'Solicitação recebida',
                    'subtitulo' => $sol['nome'] . ' (' . $sol['tipo_texto'] . ')',
                    'data'      => date('d/m/Y', strtotime($sol['criado_em'])),
                ];
            }
        }

        foreach ($arquivosRec as $arq) {
            if (count($atualizacoesRecentes) >= 5) {
                break;
            }
            $atualizacoesRecentes[] = [
                'cor'       => 'text-secondary',
                'titulo'    => 'Arquivo anexado: ' . $arq['titulo'],
                'subtitulo' => $arq['projeto_nome'] ?? 'Projeto',
                'data'      => date('d/m/Y', strtotime($arq['criado_em'])),
            ];
        }

        foreach ($clientes as $cli) {
            if (count($atualizacoesRecentes) >= 5) {
                break;
            }
            $detalheCli = (!empty($cli['contato']) && $cli['contato'] !== $cli['nome'])
                ? $cli['nome'] . ' · ' . $cli['contato']
                : $cli['nome'] . ' (' . $cli['email'] . ')';
            $atualizacoesRecentes[] = [
                'cor'       => 'text-success',
                'titulo'    => 'Cliente cadastrado',
                'subtitulo' => $detalheCli,
                'data'      => date('d/m/Y', strtotime($cli['criado_em'])),
            ];
        }

        $this->renderizar('empresa/dashboard', [
            'clientesRecentes'     => array_slice($clientes, 0, 5),
            'projetos'             => $projetos,
            'totalAtivos'          => $totalAtivos,
            'totalClientes'        => count($clientes),
            'novasSolicitacoes'    => $novasSolicitacoes,
            'totalSolicitacoes'    => count($solicitacoes),
            'orcamentosSemResp'    => $orcamentosSemResp,
            'projetosAndamento'    => $projetosAndamento,
            'totalProjetos'        => count($projetos),
            'itensAtencao'         => $itensAtencao,
            'etapasGrafico'        => $etapasGrafico,
            'clientesPorMes'       => $clientesPorMes,
            'atualizacoesRecentes' => array_slice($atualizacoesRecentes, 0, 5),
        ]);
    }

    // lista os clientes e faz a busca por nome/email se tiver filtro
    public function clientes(): void
    {
        $busca = trim($_GET['q'] ?? '');
        $clienteModel = new Cliente();
        $clientes = $clienteModel->listarTodos($busca);

        $this->renderizar('empresa/clientes', [
            'clientes' => $clientes,
            'busca'    => $busca,
        ]);
    }

    // salvar cliente novo ou editado (crud - create / update)
    public function salvarCliente(): void
    {
        $this->validarCsrfOuVoltar('/empresa/clientes');

        $id       = (int) ($_POST['id'] ?? 0);
        $nome     = trim($_POST['nome'] ?? '');
        $cnpj     = trim($_POST['cnpj'] ?? '');
        $segmento = trim($_POST['segmento'] ?? 'comercio');
        $status   = trim($_POST['status'] ?? 'ativo');
        $contato  = trim($_POST['contato'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $cep      = trim($_POST['cep'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $cidade   = trim($_POST['cidade'] ?? '');
        $uf       = trim($_POST['uf'] ?? '');

        if ($nome === '' || $cnpj === '' || $contato === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirecionar('/empresa/clientes', 'Preencha todos os campos obrigatórios do cliente.', 'danger');
        }

        $dados = [
            'nome'     => $nome,
            'cnpj'     => $cnpj,
            'segmento' => $segmento,
            'status'   => $status,
            'contato'  => $contato,
            'email'    => $email,
            'telefone' => $telefone,
            'cep'      => $cep,
            'endereco' => $endereco,
            'cidade'   => $cidade,
            'uf'       => $uf,
        ];

        $clienteModel = new Cliente();

        if ($id > 0) {
            $clienteModel->atualizar($id, $dados);
            $this->redirecionar('/empresa/clientes', 'Cliente "' . $nome . '" atualizado com sucesso!', 'success');
        } else {
            $novoId = $clienteModel->criar($dados);
            // Cria tambem um projeto inicial para que a ficha 360 do novo cliente ja tenha projeto vinculado
            $projetoModel = new Projeto();
            $projetoModel->criarInicialParaCliente($novoId, 'Website Institucional');
            $this->redirecionar('/empresa/clientes', 'Cliente "' . $nome . '" cadastrado com sucesso!', 'success');
        }
    }

    // excluir cliente no banco (crud - delete)
    public function excluirCliente(): void
    {
        $this->validarCsrfOuVoltar('/empresa/clientes');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $clienteModel = new Cliente();
            $cliente = $clienteModel->buscarPorId($id);
            $nome = $cliente['nome'] ?? 'Cliente';
            $clienteModel->excluir($id);
            $this->redirecionar('/empresa/clientes', 'Cliente "' . $nome . '" excluído com sucesso.', 'success');
        }

        $this->redirecionar('/empresa/clientes', 'Não foi possível identificar o cliente para excluir.', 'danger');
    }

    // exibir a ficha 360 do cliente com projetos e orcamentos reais do banco
    public function detalheCliente(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $clienteModel     = new Cliente();
        $projetoModel     = new Projeto();
        $solicitacaoModel = new Solicitacao();

        $cliente = ($id > 0) ? $clienteModel->buscarPorId($id) : $clienteModel->buscarPrimeiro();
        if (!$cliente) {
            $this->redirecionar('/empresa/clientes', 'Cliente não encontrado.', 'warning');
        }

        $projeto = $projetoModel->buscarProjetoDoCliente((int) $cliente['id']);
        $arquivos = $projeto ? $projetoModel->listarArquivos((int) $projeto['id']) : [];
        $orcamentosCliente = $solicitacaoModel->listarPorCliente(
            (int) $cliente['id'],
            (string) ($cliente['email'] ?? ''),
            isset($cliente['usuario_id']) ? (int) $cliente['usuario_id'] : null
        );

        $this->renderizar('empresa/cliente', [
            'cliente'           => $cliente,
            'projeto'           => $projeto,
            'arquivos'          => $arquivos,
            'orcamentosCliente' => $orcamentosCliente,
        ]);
    }

    // listar todos os projetos com filtros e contadores (área de projetos)
    public function projetos(): void
    {
        $busca = trim($_GET['q'] ?? '');
        $etapa = trim($_GET['etapa'] ?? '');

        $projetoModel = new Projeto();
        $projetos = $projetoModel->listarTodos($busca, $etapa);

        // Contadores dos cards de resumo
        $todos = $projetoModel->listarTodos();
        $contadores = [
            'total'           => count($todos),
            'requisitos'      => 0, // 20%
            'wireframes'      => 0, // 40%
            'desenvolvimento' => 0, // 60%
            'testes'          => 0, // 80%
            'concluidos'      => 0, // 100%
            'ajustes'         => 0,
        ];

        foreach ($todos as $proj) {
            $p = (int) ($proj['progresso'] ?? 20);
            if (!empty($proj['ultimo_ajuste'])) {
                $contadores['ajustes']++;
            }
            if ($p === 20) {
                $contadores['requisitos']++;
            } elseif ($p === 40) {
                $contadores['wireframes']++;
            } elseif ($p === 60) {
                $contadores['desenvolvimento']++;
            } elseif ($p === 80) {
                $contadores['testes']++;
            } elseif ($p >= 100) {
                $contadores['concluidos']++;
            }
        }

        $this->renderizar('empresa/projetos', [
            'projetos'     => $projetos,
            'contadores'   => $contadores,
            'busca'        => $busca,
            'etapaFiltro'  => $etapa,
            'paginaAtiva'  => 'projetos',
        ]);
    }

    // ficha completa do projeto com troca de arquivos/mockups com o cliente
    public function detalheProjeto(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $clienteId = (int) ($_GET['cliente_id'] ?? 0);

        $projetoModel = new Projeto();

        if ($id <= 0 && $clienteId > 0) {
            $projCliente = $projetoModel->buscarProjetoDoCliente($clienteId);
            $id = $projCliente ? (int) $projCliente['id'] : 0;
        }

        $projeto = ($id > 0) ? $projetoModel->buscarPorId($id) : null;

        if (!$projeto) {
            $this->redirecionar('/empresa/projetos', 'Projeto não encontrado.', 'warning');
        }

        $arquivos = $projetoModel->listarArquivos((int) $projeto['id']);
        $solicitacaoModel = new Solicitacao();
        $solicitacoes = $solicitacaoModel->listarPorCliente((int) $projeto['cliente_id']);
        $ultimaSolicitacao = !empty($solicitacoes) ? $solicitacoes[0] : null;

        $checklistReq = (!empty($projeto['checklist_requisitos'])) ? json_decode($projeto['checklist_requisitos'], true) : [];
        if (!is_array($checklistReq)) {
            $checklistReq = [];
        }

        $checklistHomolog = (!empty($projeto['checklist_homologacao'])) ? json_decode($projeto['checklist_homologacao'], true) : [];
        if (!is_array($checklistHomolog)) {
            $checklistHomolog = [];
        }

        $parcelas = $projetoModel->listarParcelas((int) $projeto['id']);
        $historico = $projetoModel->listarHistorico((int) $projeto['id'], 35);

        $this->renderizar('empresa/projeto', [
            'projeto'           => $projeto,
            'arquivos'          => $arquivos,
            'parcelas'          => $parcelas,
            'historico'         => $historico,
            'ultimaSolicitacao' => $ultimaSolicitacao,
            'checklistReq'      => $checklistReq,
            'checklistHomolog'  => $checklistHomolog,
            'paginaAtiva'       => 'projetos',
        ]);
    }

    // listar solicitacoes e orcamentos
    public function solicitacoes(): void
    {
        $solicitacaoModel = new Solicitacao();
        $solicitacoes = $solicitacaoModel->listarTodas();

        $this->renderizar('empresa/solicitacoes', [
            'solicitacoes' => $solicitacoes,
        ]);
    }

    // cadastrar nova solicitacao pela area da empresa
    public function salvarSolicitacao(): void
    {
        $this->validarCsrfOuVoltar('/empresa/solicitacoes');

        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $tipo     = trim($_POST['tipo'] ?? 'website');
        $prazo    = trim($_POST['prazo'] ?? '1 a 3 meses');
        $mensagem = trim($_POST['mensagem'] ?? '');

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirecionar('/empresa/solicitacoes', 'Informe o nome e um e-mail válido.', 'danger');
        }

        $solicitacaoModel = new Solicitacao();
        $solicitacaoModel->criar([
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefone,
            'tipo'     => $tipo,
            'prazo'    => $prazo,
            'mensagem' => '"' . $mensagem . '"',
        ]);

        $this->redirecionar('/empresa/solicitacoes', 'Solicitação cadastrada com sucesso!', 'success');
    }

    // salvar anotacao interna da solicitacao
    public function salvarAnotacao(): void
    {
        $this->validarCsrfOuVoltar('/empresa/solicitacoes');

        $id       = (int) ($_POST['id'] ?? 1);
        $anotacao = trim($_POST['anotacao'] ?? '');

        $solicitacaoModel = new Solicitacao();
        $solicitacaoModel->salvarAnotacao($id, $anotacao);

        $this->redirecionar('/empresa/solicitacoes', 'Anotação interna salva no banco!', 'success');
    }

    // enviar orcamento ao cliente (salva no banco + dispara phpmailer)
    public function enviarOrcamento(): void
    {
        $this->validarCsrfOuVoltar('/empresa/solicitacoes');

        $id          = (int) ($_POST['id'] ?? 1);
        $valor       = (float) ($_POST['valor'] ?? 0);
        $validade    = trim($_POST['validade'] ?? date('Y-m-d', strtotime('+15 days')));
        $observacoes = trim($_POST['observacoes'] ?? '');

        if ($valor <= 0) {
            $this->redirecionar('/empresa/solicitacoes', 'Informe um valor maior que zero para o orçamento.', 'danger');
        }

        $solicitacaoModel = new Solicitacao();
        $solicitacao = $solicitacaoModel->buscarPorId($id);
        $solicitacaoModel->registrarOrcamento($id, $valor, $validade, $observacoes);

        if ($solicitacao) {
            $valorFormatado = 'R$ ' . number_format($valor, 2, ',', '.');
            Mailer::enviar(
                $solicitacao['email'],
                $solicitacao['nome'],
                'Seu Orçamento Chegou - iiKliente',
                "<h2>Olá, " . htmlspecialchars($solicitacao['nome']) . "!</h2>" .
                "<p>Preparamos o orçamento para o seu projeto (<strong>" . htmlspecialchars($solicitacao['tipo_texto']) . "</strong>).</p>" .
                "<p><strong>Valor:</strong> {$valorFormatado}<br><strong>Válido até:</strong> " . date('d/m/Y', strtotime($validade)) . "</p>" .
                "<p>" . nl2br(htmlspecialchars($observacoes)) . "</p>"
            );
        }

        $this->redirecionar('/empresa/solicitacoes', 'Orçamento salvo e enviado por e-mail ao cliente!', 'success');
    }

    // converter solicitacao em cliente usando transacao acid
    public function converterSolicitacao(): void
    {
        $this->validarCsrfOuVoltar('/empresa/solicitacoes');

        $id = (int) ($_POST['id'] ?? 1);
        $solicitacaoModel = new Solicitacao();
        $novoClienteId = $solicitacaoModel->converterEmCliente($id);

        if ($novoClienteId !== null) {
            $this->redirecionar('/empresa/clientes', 'Solicitação convertida em cliente e projeto iniciado!', 'success');
        }

        $this->redirecionar('/empresa/solicitacoes', 'Não foi possível converter a solicitação.', 'danger');
    }

    // atualizar etapa e progresso do projeto
    public function atualizarEtapaProjeto(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $origem    = $_POST['origem'] ?? 'cliente';
        $destino   = ($origem === 'projeto') ? '/empresa/projeto?id=' . $projetoId : '/empresa/cliente?id=' . $clienteId;

        $this->validarCsrfOuVoltar($destino);

        $progresso = (int) ($_POST['progresso'] ?? 20);

        if ($projetoId > 0) {
            $projetoModel = new Projeto();
            $projetoAntigo = $projetoModel->buscarPorId($projetoId);
            $etapaAntiga = (int) ($projetoAntigo['etapa_atual'] ?? 1);

            $projetoModel->atualizarEtapa($projetoId, $progresso);

            $etapaMap = [20 => 1, 40 => 2, 60 => 3, 80 => 4, 100 => 5];
            $etapaNova = $etapaMap[$progresso] ?? (int) ceil($progresso / 20);
            $nomesEtapa = [
                1 => 'Briefing e Alinhamento',
                2 => 'Wireframe e Protótipo',
                3 => 'Desenvolvimento',
                4 => 'Homologação e Testes',
                5 => 'Lançamento e Entrega'
            ];
            $nomeNova = $nomesEtapa[$etapaNova] ?? 'Etapa ' . $etapaNova;

            $sessao = Auth::usuario();
            $autorId = $sessao ? (int) $sessao['id'] : null;
            $autorNome = $sessao['nome'] ?? 'Vitor Hugo Pedrini';

            $projetoModel->registrarHistorico(
                $projetoId,
                $autorId,
                'empresa',
                $autorNome,
                'avanco_etapa',
                "Etapa do projeto avançada para {$etapaNova} - {$nomeNova} (Progresso: {$progresso}%).",
                (string) $etapaAntiga,
                (string) $etapaNova
            );

            if ($projetoAntigo && !empty($projetoAntigo['cliente_id'])) {
                $clienteInfo = (new Cliente())->buscarPorId((int) $projetoAntigo['cliente_id']);
                if ($clienteInfo && !empty($clienteInfo['usuario_id'])) {
                    Notificacao::criar(
                        (int) $clienteInfo['usuario_id'],
                        'bi-arrow-up-right-circle text-primary',
                        'Avanço no Projeto!',
                        "Seu projeto avançou para a etapa {$etapaNova} - {$nomeNova} ({$progresso}% concluído).",
                        '/cliente/projeto'
                    );
                }
            }

            $this->redirecionar($destino, 'Etapa do projeto atualizada com sucesso!', 'success');
        }

        $this->redirecionar($destino, 'Projeto não encontrado para atualizar.', 'warning');
    }

    // salvar link externo do protótipo interativo (figma, etc)
    public function salvarPrototipo(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $destino   = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $link = trim($_POST['link_prototipo'] ?? '');
        $projetoModel = new Projeto();
        $projetoModel->atualizarLinkPrototipo($projetoId, $link);

        $sessao = Auth::usuario();
        $autorId = $sessao ? (int) $sessao['id'] : null;
        $autorNome = $sessao['nome'] ?? 'Vitor Hugo Pedrini';

        $projetoModel->registrarHistorico(
            $projetoId,
            $autorId,
            'empresa',
            $autorNome,
            'prototipo_atualizado',
            $link !== '' ? "Link do protótipo interativo atualizado: {$link}" : "Link do protótipo removido."
        );

        $projeto = $projetoModel->buscarPorId($projetoId);
        if ($projeto && !empty($projeto['cliente_id']) && $link !== '') {
            $clienteInfo = (new Cliente())->buscarPorId((int) $projeto['cliente_id']);
            if ($clienteInfo && !empty($clienteInfo['usuario_id'])) {
                Notificacao::criar(
                    (int) $clienteInfo['usuario_id'],
                    'bi-phone text-info',
                    'Protótipo Interativo Disponível',
                    'A Vitor Hugo Pedrini disponibilizou o protótipo interativo navegável para você experimentar.',
                    '/cliente/projeto'
                );
            }
        }

        $msg = ($link !== '')
            ? 'Link do protótipo interativo salvo e disponibilizado para o cliente!'
            : 'Link do protótipo removido.';
        $this->redirecionar($destino, $msg, 'success');
    }

    // salvar todos os links e credenciais das etapas do projeto
    public function salvarLinksEtapas(): void
    {
        $projetoId   = (int) ($_POST['projeto_id'] ?? 0);
        $destino     = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $prototipo   = trim($_POST['link_prototipo'] ?? '');
        $homologacao = trim($_POST['link_homologacao'] ?? '');
        $producao    = trim($_POST['link_producao'] ?? '');
        $credenciais = trim($_POST['credenciais_teste'] ?? '');

        $projetoModel = new Projeto();
        $projetoModel->atualizarLinksEtapas($projetoId, $prototipo, $homologacao, $producao, $credenciais);

        $this->redirecionar($destino, 'Links e credenciais das etapas atualizados com sucesso!', 'success');
    }

    // salvar checklist de requisitos pela equipe
    public function salvarChecklistRequisitos(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $destino   = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $itens = $_POST['itens'] ?? [];
        if (!is_array($itens)) {
            $itens = [];
        }

        $projetoModel = new Projeto();
        $projetoModel->atualizarChecklistRequisitos($projetoId, $itens);

        $this->redirecionar($destino, 'Checklist de requisitos atualizado!', 'success');
    }

    // salvar checklist de homologação pela equipe
    public function salvarChecklistHomologacao(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $destino   = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $itens = $_POST['itens'] ?? [];
        if (!is_array($itens)) {
            $itens = [];
        }

        $projetoModel = new Projeto();
        $projetoModel->atualizarChecklistHomologacao($projetoId, $itens);

        $this->redirecionar($destino, 'Checklist de testes e homologação atualizado!', 'success');
    }

    // resolver pedido de ajuste do cliente
    public function resolverAjuste(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $destino   = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $projetoModel = new Projeto();
        $projeto = $projetoModel->buscarPorId($projetoId);
        $projetoModel->resolverAjuste($projetoId);

        $sessao = Auth::usuario();
        $autorId = $sessao ? (int) $sessao['id'] : null;
        $autorNome = $sessao['nome'] ?? 'Vitor Hugo Pedrini';

        $projetoModel->registrarHistorico(
            $projetoId,
            $autorId,
            'empresa',
            $autorNome,
            'ajuste_resolvido',
            'Solicitação de ajuste marcada como concluída e atendida pela Vitor Hugo Pedrini.'
        );

        if ($projeto && !empty($projeto['cliente_id'])) {
            $clienteInfo = (new Cliente())->buscarPorId((int) $projeto['cliente_id']);
            if ($clienteInfo && !empty($clienteInfo['usuario_id'])) {
                Notificacao::criar(
                    (int) $clienteInfo['usuario_id'],
                    'bi-check-circle text-success',
                    'Ajuste Concluído',
                    'A equipe atendeu o ajuste solicitado no seu projeto. Confira as atualizações!',
                    '/cliente/projeto'
                );
            }
        }

        $this->redirecionar($destino, 'Ajuste marcado como atendido! O projeto segue em andamento.', 'success');
    }

    // atualizar dados e detalhes gerais do projeto
    public function atualizarDetalhesProjeto(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $destino   = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $nome      = trim($_POST['nome'] ?? '');
        $tipo      = trim($_POST['tipo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $prazo     = trim($_POST['prazo_entrega'] ?? '');
        $valor     = (float) ($_POST['valor_orcamento'] ?? 0);

        if ($nome === '') {
            $this->redirecionar($destino, 'Informe o nome do projeto.', 'danger');
        }

        $projetoModel = new Projeto();
        $projetoModel->atualizarDetalhes($projetoId, $nome, $tipo, $descricao, $prazo, $valor);

        $this->redirecionar($destino, 'Informações do projeto atualizadas com sucesso!', 'success');
    }

    // empresa enviar mockup ou arquivo para o projeto do cliente com validacao rigorosa de mime
    public function enviarArquivo(): void
    {
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $origem    = $_POST['origem'] ?? 'cliente';
        $destino   = ($origem === 'projeto') ? '/empresa/projeto?id=' . $projetoId : '/empresa/cliente?id=' . $clienteId . '#aba-projeto';

        $this->validarCsrfOuVoltar($destino);

        if (empty($_FILES['arquivo']['name']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            $this->redirecionar($destino, 'Selecione um arquivo válido para enviar.', 'danger');
        }

        // Validacao real de MIME type com finfo_file (Item 6)
        $validacao = UploadValidador::validar($_FILES['arquivo'], 10.0);
        if (!$validacao['valido']) {
            $this->redirecionar($destino, $validacao['erro'] ?? 'Arquivo inválido.', 'danger');
        }

        $extensao = $validacao['extensao'];
        $nomeOriginal = $_FILES['arquivo']['name'];
        $tamanhoBytes = (int) $_FILES['arquivo']['size'];
        $tamanhoKb = max(1, (int) round($tamanhoBytes / 1024));

        $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($pastaUploads)) {
            mkdir($pastaUploads, 0775, true);
        }

        $nomeSalvo = 'emp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;
        move_uploaded_file($_FILES['arquivo']['tmp_name'], $pastaUploads . '/' . $nomeSalvo);

        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'Wireframes / Mockup');
        $titulo = ($descricao !== '') ? $descricao : pathinfo($nomeOriginal, PATHINFO_FILENAME);

        $sessao = Auth::usuario();
        $autorId = $sessao ? (int) $sessao['id'] : null;
        $autorNome = $sessao['nome'] ?? 'Vitor Hugo Pedrini';

        $projetoModel = new Projeto();
        $projetoModel->salvarArquivo(
            $projetoId,
            $autorId,
            $titulo,
            $nomeOriginal,
            $nomeSalvo,
            $extensao,
            $tamanhoKb,
            $categoria
        );

        $projetoModel->registrarHistorico(
            $projetoId,
            $autorId,
            'empresa',
            $autorNome,
            'arquivo_enviado',
            "Mockup ou arquivo disponibilizado pela equipe: {$titulo} ({$extensao}, {$tamanhoKb} KB)."
        );

        $projeto = $projetoModel->buscarPorId($projetoId);
        if ($projeto && !empty($projeto['cliente_id'])) {
            $clienteInfo = (new Cliente())->buscarPorId((int) $projeto['cliente_id']);
            if ($clienteInfo && !empty($clienteInfo['usuario_id'])) {
                Notificacao::criar(
                    (int) $clienteInfo['usuario_id'],
                    'bi-cloud-arrow-down text-primary',
                    'Novo Arquivo Disponível',
                    "A equipe disponibilizou o arquivo \"{$titulo}\" para o seu projeto.",
                    '/cliente/projeto'
                );
            }
        }

        $this->redirecionar($destino, 'Mockup/Arquivo "' . $titulo . '" disponibilizado para o cliente!', 'success');
    }

    // excluir arquivo/mockup do projeto
    public function excluirArquivo(): void
    {
        $arquivoId = (int) ($_POST['arquivo_id'] ?? 0);
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $origem    = $_POST['origem'] ?? 'projeto';
        $destino   = ($origem === 'cliente') ? '/empresa/cliente?id=' . $clienteId . '#aba-projeto' : '/empresa/projeto?id=' . $projetoId;

        $this->validarCsrfOuVoltar($destino);

        $projetoModel = new Projeto();
        $projetoModel->excluirArquivo($arquivoId, $projetoId);

        $this->redirecionar($destino, 'Arquivo excluído com sucesso.', 'success');
    }

    // salvar plano financeiro e parcelas do projeto (item 3)
    public function salvarParcelas(): void
    {
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $destino   = '/empresa/projeto?id=' . $projetoId;
        $this->validarCsrfOuVoltar($destino);

        $chavePix     = trim($_POST['chave_pix'] ?? '');
        $tipoChavePix = trim($_POST['tipo_chave_pix'] ?? 'CNPJ');

        $descricoes  = $_POST['parcela_descricao'] ?? [];
        $valores     = $_POST['parcela_valor'] ?? [];
        $vencimentos = $_POST['parcela_vencimento'] ?? [];

        $parcelas = [];
        if (is_array($descricoes)) {
            for ($i = 0; $i < count($descricoes); $i++) {
                $desc = trim($descricoes[$i] ?? '');
                $rawVal = $valores[$i] ?? '0';
                $cleanVal = str_replace(['R$', ' ', '.'], '', $rawVal);
                $cleanVal = str_replace(',', '.', $cleanVal);
                $val = (float) $cleanVal;
                $venc = !empty($vencimentos[$i]) ? trim($vencimentos[$i]) : null;

                if ($desc !== '' || $val > 0) {
                    $parcelas[] = [
                        'numero'          => $i + 1,
                        'descricao'       => $desc ?: ('Parcela ' . ($i + 1)),
                        'valor'           => $val,
                        'data_vencimento' => $venc,
                        'status'          => 'pendente',
                    ];
                }
            }
        }

        $projetoModel = new Projeto();
        $projetoModel->atualizarConfigPix($projetoId, $chavePix, $tipoChavePix);
        $projetoModel->salvarParcelas($projetoId, $parcelas);

        $sessao = Auth::usuario();
        $autorId = $sessao ? (int) $sessao['id'] : null;
        $autorNome = $sessao['nome'] ?? 'Vitor Hugo Pedrini';

        $totalParcelas = count($parcelas);
        $projetoModel->registrarHistorico(
            $projetoId,
            $autorId,
            'empresa',
            $autorNome,
            'financeiro_configurado',
            "Plano de pagamento atualizado com {$totalParcelas} parcela(s) e chave PIX cadastrada."
        );

        $projeto = $projetoModel->buscarPorId($projetoId);
        if ($projeto && !empty($projeto['cliente_id'])) {
            $clienteInfo = (new Cliente())->buscarPorId((int) $projeto['cliente_id']);
            if ($clienteInfo && !empty($clienteInfo['usuario_id'])) {
                Notificacao::criar(
                    (int) $clienteInfo['usuario_id'],
                    'bi-credit-card text-success',
                    'Plano de Pagamento Disponível',
                    "O plano financeiro do projeto {$projeto['nome']} foi configurado pela equipe. Acesse para visualizar as parcelas e chave PIX.",
                    '/cliente/projeto#financeiro'
                );
            }
        }

        $this->redirecionar($destino, 'Plano de pagamento e chave PIX atualizados com sucesso!', 'success');
    }

    // atualizar status de uma parcela (pago/pendente) pela equipe (item 3)
    public function atualizarStatusParcela(): void
    {
        $parcelaId = (int) ($_POST['parcela_id'] ?? 0);
        $projetoId = (int) ($_POST['projeto_id'] ?? 0);
        $status    = trim($_POST['status'] ?? 'pago');
        $destino   = '/empresa/projeto?id=' . $projetoId . '#financeiro';

        $this->validarCsrfOuVoltar($destino);

        if (!in_array($status, ['pendente', 'em_analise', 'pago'], true)) {
            $status = 'pago';
        }

        $projetoModel = new Projeto();
        $projetoModel->atualizarStatusParcela($parcelaId, $status);

        // Atualizar status_financeiro geral do projeto
        $todas = $projetoModel->listarParcelas($projetoId);
        $total = count($todas);
        $pagas = 0;
        foreach ($todas as $p) {
            if ($p['status'] === 'pago') {
                $pagas++;
            }
        }

        $statusFinanceiroGeral = 'pendente';
        if ($total > 0 && $pagas === $total) {
            $statusFinanceiroGeral = 'pago';
        } elseif ($pagas > 0) {
            $statusFinanceiroGeral = 'parcial';
        }
        $projetoModel->atualizarStatusFinanceiro($projetoId, $statusFinanceiroGeral);

        $sessao = Auth::usuario();
        $autorId = $sessao ? (int) $sessao['id'] : null;
        $autorNome = $sessao['nome'] ?? 'Vitor Hugo Pedrini';

        $rotuloStatus = ($status === 'pago') ? 'Pago (Confirmado)' : (($status === 'em_analise') ? 'Em Análise' : 'Pendente');
        $projetoModel->registrarHistorico(
            $projetoId,
            $autorId,
            'empresa',
            $autorNome,
            'parcela_status',
            "Status da parcela #{$parcelaId} atualizado para \"{$rotuloStatus}\"."
        );

        $projeto = $projetoModel->buscarPorId($projetoId);
        if ($projeto && !empty($projeto['cliente_id'])) {
            $clienteInfo = (new Cliente())->buscarPorId((int) $projeto['cliente_id']);
            if ($clienteInfo && !empty($clienteInfo['usuario_id'])) {
                Notificacao::criar(
                    (int) $clienteInfo['usuario_id'],
                    'bi-receipt-cutoff text-success',
                    'Atualização de Parcela',
                    "A parcela #{$parcelaId} do projeto {$projeto['nome']} foi marcada como \"{$rotuloStatus}\".",
                    '/cliente/projeto#financeiro'
                );
            }
        }

        $this->redirecionar($destino, 'Status da parcela atualizado com sucesso!', 'success');
    }

    // exportar relatórios para csv com utf-8 bom para excel (item 5)
    public function exportarCsv(): void
    {
        $tipo = $_GET['tipo'] ?? 'projetos';
        $nomeArquivo = 'relatorio_' . $tipo . '_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM para garantir acentuação perfeita no Microsoft Excel
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if ($tipo === 'clientes') {
            $clienteModel = new Cliente();
            $lista = $clienteModel->listarTodos();
            fputcsv($out, ['ID', 'Nome', 'CNPJ', 'Segmento', 'Contato', 'E-mail', 'Telefone', 'Status', 'Data de Cadastro'], ';');
            foreach ($lista as $c) {
                fputcsv($out, [
                    $c['id'],
                    $c['nome'],
                    $c['cnpj'] ?? '',
                    $c['segmento'] ?? '',
                    $c['contato'] ?? '',
                    $c['email'],
                    $c['telefone'] ?? '',
                    $c['status'],
                    $c['criado_em'] ?? '',
                ], ';');
            }
        } elseif ($tipo === 'solicitacoes') {
            $solicitacaoModel = new Solicitacao();
            $lista = $solicitacaoModel->listarTodas();
            fputcsv($out, ['ID', 'Nome', 'E-mail', 'Telefone', 'Segmento', 'Projeto / Ideia', 'Orçamento Estimado (R$)', 'Prazo Desejado', 'Status', 'Data da Solicitação'], ';');
            foreach ($lista as $s) {
                fputcsv($out, [
                    $s['id'],
                    $s['nome'],
                    $s['email'],
                    $s['telefone'] ?? '',
                    $s['segmento'] ?? '',
                    $s['descricao_projeto'] ?? '',
                    number_format((float) ($s['orcamento_estimado'] ?? 0), 2, ',', '.'),
                    $s['prazo_desejado'] ?? '',
                    $s['status'],
                    $s['criado_em'] ?? '',
                ], ';');
            }
        } else {
            // padrao: projetos
            $projetoModel = new Projeto();
            $lista = $projetoModel->listarTodos();
            fputcsv($out, ['ID', 'Projeto', 'Cliente', 'Etapa Atual', 'Progresso (%)', 'Status', 'Status Financeiro', 'Valor Orçamento (R$)', 'Prazo de Entrega', 'Data de Criação'], ';');
            foreach ($lista as $p) {
                fputcsv($out, [
                    $p['id'],
                    $p['nome'],
                    $p['cliente_nome'] ?? '',
                    $p['etapa_atual'],
                    $p['progresso'],
                    $p['status'],
                    $p['status_financeiro'] ?? 'pendente',
                    number_format((float) ($p['valor_orcamento'] ?? 0), 2, ',', '.'),
                    $p['prazo_entrega'] ?? '',
                    $p['criado_em'] ?? '',
                ], ';');
            }
        }

        fclose($out);
        exit;
    }

    // imprimir termo de entrega do projeto em pdf/impressão (item 5)
    public function imprimirTermoEntrega(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $projetoModel = new Projeto();
        $projeto = $projetoModel->buscarPorId($id);

        if (!$projeto) {
            $this->redirecionar('/empresa/projetos', 'Projeto não encontrado para emitir termo.', 'warning');
        }

        $clienteModel = new Cliente();
        $cliente = $clienteModel->buscarPorId((int) $projeto['cliente_id']);
        $parcelas = $projetoModel->listarParcelas($id);
        $historico = $projetoModel->listarHistorico($id, 50);

        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $empresa = $sessao ? $usuarioModel->buscarPorId((int) $sessao['id']) : $usuarioModel->buscarPorEmail('admin@iikliente.com');

        $this->renderizar('empresa/termo_entrega', [
            'projeto'   => $projeto,
            'cliente'   => $cliente,
            'parcelas'  => $parcelas,
            'historico' => $historico,
            'empresa'   => $empresa,
        ]);
    }

    // empresa baixar arquivo do projeto
    public function baixarArquivo(): void
    {
        $idArquivo = (int) ($_GET['id'] ?? 0);
        $projetoModel = new Projeto();
        $arquivo = $projetoModel->buscarArquivoPorId($idArquivo);

        if (!$arquivo) {
            $this->redirecionar('/empresa/clientes', 'Arquivo não encontrado.', 'warning');
        }

        $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
        $caminhoFisico = $pastaUploads . '/' . basename($arquivo['nome_salvo']);

        if (!file_exists($caminhoFisico)) {
            $conteudoDemo = "iiKliente - Gestão de Clientes\n"
                . "Documento: " . $arquivo['titulo'] . "\n"
                . "Arquivo original: " . $arquivo['nome_original'] . "\n";
            file_put_contents($caminhoFisico, $conteudoDemo);
        }

        $nomeDownload = preg_replace('/[^a-zA-Z0-9._-]/', '_', $arquivo['nome_original']);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nomeDownload . '"');
        header('Content-Length: ' . filesize($caminhoFisico));
        readfile($caminhoFisico);
        exit;
    }

    // recusar solicitacao
    public function recusarSolicitacao(): void
    {
        $this->validarCsrfOuVoltar('/empresa/solicitacoes');

        $id = (int) ($_POST['id'] ?? 1);
        $solicitacaoModel = new Solicitacao();
        $solicitacaoModel->recusar($id);

        $this->redirecionar('/empresa/solicitacoes', 'Solicitação marcada como recusada.', 'warning');
    }

    // exibir tela de configuracoes da empresa
    public function configuracoes(): void
    {
        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $dadosEmpresa = $sessao ? $usuarioModel->buscarPorId((int) $sessao['id']) : $usuarioModel->buscarPorEmail('admin@iikliente.com');

        $this->renderizar('empresa/configuracoes', [
            'empresa' => $dadosEmpresa,
        ]);
    }

    // salvar dados da empresa e upload de logotipo
    public function salvarConfiguracoes(): void
    {
        $this->validarCsrfOuVoltar('/empresa/configuracoes');

        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $empresa = $sessao ? $usuarioModel->buscarPorId((int) $sessao['id']) : $usuarioModel->buscarPorEmail('admin@iikliente.com');
        $idUsuario = (int) ($empresa['id'] ?? 1);

        $nome     = trim($_POST['nome'] ?? 'Empresa Exemplo');
        $cnpj     = trim($_POST['cnpj'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $nomeLogo = null;

        // Se enviou um logotipo novo, confere extensao e tamanho (ate 2 MB)
        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $tamanhoMb = $_FILES['logo']['size'] / 1024 / 1024;

            if (!in_array($extensao, ['png', 'jpg', 'jpeg'], true) || $tamanhoMb > 2) {
                $this->redirecionar('/empresa/configuracoes', 'Logotipo inválido. Use PNG ou JPG de até 2 MB.', 'danger');
            }

            $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
            if (!is_dir($pastaUploads)) {
                mkdir($pastaUploads, 0775, true);
            }

            $nomeLogo = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;
            move_uploaded_file($_FILES['logo']['tmp_name'], $pastaUploads . '/' . $nomeLogo);
        }

        $usuarioModel->atualizarDados($idUsuario, [
            'nome'         => $nome,
            'email'        => $email,
            'telefone'     => $telefone,
            'empresa_nome' => $nome,
            'cnpj'         => $cnpj,
            'foto'         => $nomeLogo,
        ]);

        Auth::atualizarSessao($nome, $email, $nome);
        $this->redirecionar('/empresa/configuracoes', 'Dados da empresa atualizados com sucesso!', 'success');
    }

    // salvar preferencias de notificacao
    public function salvarNotificacoes(): void
    {
        $this->validarCsrfOuVoltar('/empresa/configuracoes');

        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $idUsuario = (int) ($sessao['id'] ?? 1);

        $notifSolicitacao = isset($_POST['notif_solicitacao']) ? 1 : 0;
        $notifAprovacao   = isset($_POST['notif_aprovacao']) ? 1 : 0;
        $notifEmail       = isset($_POST['notif_email']) ? 1 : 0;

        $usuarioModel->atualizarNotificacoes($idUsuario, $notifSolicitacao, $notifAprovacao, $notifEmail);
        $this->redirecionar('/empresa/configuracoes', 'Preferências de notificação salvas!', 'success');
    }

    // alterar senha da empresa
    public function alterarSenha(): void
    {
        $this->validarCsrfOuVoltar('/empresa/configuracoes');

        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $idUsuario = (int) ($sessao['id'] ?? 1);

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $senhaNova  = $_POST['senha_nova'] ?? '';

        if (strlen($senhaNova) < 8) {
            $this->redirecionar('/empresa/configuracoes', 'A nova senha precisa ter pelo menos 8 caracteres.', 'danger');
        }

        if ($usuarioModel->alterarSenha($idUsuario, $senhaAtual, $senhaNova)) {
            $this->redirecionar('/empresa/configuracoes', 'Senha alterada com sucesso!', 'success');
        }

        $this->redirecionar('/empresa/configuracoes', 'A senha atual informada está incorreta.', 'danger');
    }
}
