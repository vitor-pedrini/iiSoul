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
 * PortalClienteController
 * Controla todas as paginas e formularios da Area do Cliente:
 * Inicio, Meu Projeto (aprovacao, ajuste, upload e download de arquivos, financeiro, historico), Questionario e Perfil.
 */
class PortalClienteController extends Controller
{
    public function __construct(string $urlBase = '')
    {
        parent::__construct($urlBase);
        // garantir que apenas usuarios logados com perfil "cliente" acessem esta area
        Auth::exigirPerfil('cliente', $this->urlBase);
    }

    // descobrir todos os projetos vinculados ao cliente (suporte a multiplos projetos)
    private function obterProjetosDoUsuarioLogado(): array
    {
        $sessao = Auth::usuario();
        if (!$sessao) {
            return ['projeto' => null, 'todosProjetos' => []];
        }

        $clienteModel = new Cliente();
        $projetoModel = new Projeto();

        $cliente = $clienteModel->buscarPorUsuarioOuEmail((int) $sessao['id'], (string) $sessao['email']);
        if (!$cliente) {
            return ['projeto' => null, 'todosProjetos' => []];
        }

        $todosProjetos = $projetoModel->listarTodosDoCliente((int) $cliente['id']);
        if (empty($todosProjetos)) {
            return ['projeto' => null, 'todosProjetos' => []];
        }

        $idDesejado = (int) ($_GET['projeto_id'] ?? ($_SESSION['projeto_ativo_id'] ?? 0));
        $projetoSelecionado = null;

        if ($idDesejado > 0) {
            foreach ($todosProjetos as $p) {
                if ((int) $p['id'] === $idDesejado) {
                    $projetoSelecionado = $p;
                    $_SESSION['projeto_ativo_id'] = $idDesejado;
                    break;
                }
            }
        }

        if (!$projetoSelecionado) {
            $projetoSelecionado = $todosProjetos[0];
            $_SESSION['projeto_ativo_id'] = (int) $projetoSelecionado['id'];
        }

        return [
            'projeto'       => $projetoSelecionado,
            'todosProjetos' => $todosProjetos,
        ];
    }

    // atalho para obter o projeto atualmente selecionado
    private function obterProjetoDoUsuarioLogado(): ?array
    {
        return $this->obterProjetosDoUsuarioLogado()['projeto'];
    }

    // selecionar projeto ativo (suporte a multiplos projetos)
    public function selecionarProjetoAtivo(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $_SESSION['projeto_ativo_id'] = $id;
        }

        $retorno = $_SERVER['HTTP_REFERER'] ?? $this->urlBase . '/cliente/projeto';
        header('Location: ' . $retorno);
        exit;
    }

    // buscar a ultima solicitacao/orcamento do cliente logado
    private function obterUltimaSolicitacaoDoUsuarioLogado(): ?array
    {
        $sessao = Auth::usuario();
        if (!$sessao) {
            return null;
        }

        $solicitacaoModel = new Solicitacao();
        return $solicitacaoModel->buscarUltimaDoUsuario((int) $sessao['id'], (string) $sessao['email']);
    }

    // exibir a tela inicial do cliente
    public function inicio(): void
    {
        $dadosProj = $this->obterProjetosDoUsuarioLogado();
        $projeto = $dadosProj['projeto'];
        $todosProjetos = $dadosProj['todosProjetos'];
        $ultimaSolicitacao = $this->obterUltimaSolicitacaoDoUsuarioLogado();

        $this->renderizar('cliente/inicio', [
            'projeto'           => $projeto,
            'todosProjetos'     => $todosProjetos,
            'ultimaSolicitacao' => $ultimaSolicitacao,
        ]);
    }

    // exibir a tela meu projeto
    public function projeto(): void
    {
        $projetoModel = new Projeto();
        $dadosProj = $this->obterProjetosDoUsuarioLogado();
        $projeto = $dadosProj['projeto'];
        $todosProjetos = $dadosProj['todosProjetos'];
        $ultimaSolicitacao = $this->obterUltimaSolicitacaoDoUsuarioLogado();

        $arquivos  = $projeto ? $projetoModel->listarArquivos((int) $projeto['id']) : [];
        $parcelas  = $projeto ? $projetoModel->listarParcelas((int) $projeto['id']) : [];
        $historico = $projeto ? $projetoModel->listarHistorico((int) $projeto['id'], 30) : [];

        $checklistReq = (!empty($projeto['checklist_requisitos'])) ? json_decode($projeto['checklist_requisitos'], true) : [];
        if (!is_array($checklistReq)) {
            $checklistReq = [];
        }

        $checklistHomolog = (!empty($projeto['checklist_homologacao'])) ? json_decode($projeto['checklist_homologacao'], true) : [];
        if (!is_array($checklistHomolog)) {
            $checklistHomolog = [];
        }

        $this->renderizar('cliente/projeto', [
            'projeto'            => $projeto,
            'todosProjetos'      => $todosProjetos,
            'ultimaSolicitacao'  => $ultimaSolicitacao,
            'arquivos'           => $arquivos,
            'parcelas'           => $parcelas,
            'historico'          => $historico,
            'checklistReq'       => $checklistReq,
            'checklistHomolog'   => $checklistHomolog,
        ]);
    }

    // aprovar o orcamento enviado pela empresa e iniciar o projeto
    public function aprovarOrcamento(): void
    {
        $this->validarCsrfOuVoltar('/cliente/questionario');

        $ultimaSolicitacao = $this->obterUltimaSolicitacaoDoUsuarioLogado();
        if ($ultimaSolicitacao) {
            $solicitacaoModel = new Solicitacao();
            $solicitacaoModel->converterEmCliente((int) $ultimaSolicitacao['id']);
            $this->redirecionar('/cliente/projeto', 'Orçamento aprovado! Seu projeto foi iniciado com sucesso.', 'success');
        }

        $this->redirecionar('/cliente/questionario', 'Nenhuma proposta de orçamento encontrada para aprovar.', 'warning');
    }

    // aprovar a etapa atual do projeto com registro de auditoria
    public function aprovarEtapa(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto');

        $projetoModel = new Projeto();
        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao  = Auth::usuario();

        if (!$projeto) {
            $ultimaSolicitacao = $this->obterUltimaSolicitacaoDoUsuarioLogado();
            if ($ultimaSolicitacao) {
                $solicitacaoModel = new Solicitacao();
                $solicitacaoModel->converterEmCliente((int) $ultimaSolicitacao['id']);
                $projeto = $this->obterProjetoDoUsuarioLogado();
            }
        }

        if ($projeto) {
            $etapaAnterior = $projeto['etapa_atual'];
            $projetoModel->aprovarEtapa((int) $projeto['id']);
            $projetoAtualizado = $projetoModel->buscarPorId((int) $projeto['id']);
            $etapaNova = $projetoAtualizado['etapa_atual'] ?? 'Próxima etapa';

            // Registra no Historico de Auditoria
            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Aprovação de Etapa',
                'O cliente aprovou formalmente a etapa no portal.',
                $etapaAnterior,
                $etapaNova
            );

            // Cria notificacao persistente para os administradores da empresa
            $usuarioModel = new Usuario();
            $admin = $usuarioModel->buscarPorEmail('admin@iikliente.com');
            if ($admin) {
                $notifModel = new Notificacao();
                $notifModel->criar(
                    (int) $admin['id'],
                    'Etapa Aprovada pelo Cliente',
                    ($sessao['nome'] ?? 'Cliente') . ' aprovou a etapa "' . $etapaAnterior . '" do projeto ' . $projeto['nome'] . '.',
                    'projeto?id=' . (int) $projeto['id'],
                    'bi-check-circle-fill text-success'
                );
            }
        }

        $this->redirecionar('/cliente/projeto', 'Etapa aprovada com sucesso! O projeto avançou para a próxima fase.', 'success');
    }

    // solicitar ajuste na etapa do projeto com registro de auditoria
    public function solicitarAjuste(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto');

        $textoAjuste = trim($_POST['ajuste'] ?? '');
        if (mb_strlen($textoAjuste) < 10) {
            $this->redirecionar('/cliente/projeto', 'Descreva o ajuste com pelo menos 10 caracteres.', 'danger');
        }

        $projetoModel = new Projeto();
        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao  = Auth::usuario();

        if ($projeto) {
            $projetoModel->solicitarAjuste((int) $projeto['id'], $textoAjuste);

            // Registra no Historico de Auditoria
            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Ajuste Solicitado',
                $textoAjuste,
                $projeto['etapa_atual'],
                $projeto['etapa_atual']
            );

            // Cria notificacao persistente para a empresa
            $usuarioModel = new Usuario();
            $admin = $usuarioModel->buscarPorEmail('admin@iikliente.com');
            if ($admin) {
                $notifModel = new Notificacao();
                $notifModel->criar(
                    (int) $admin['id'],
                    'Ajuste Solicitado',
                    ($sessao['nome'] ?? 'Cliente') . ' solicitou ajuste em ' . $projeto['nome'] . ': "' . mb_substr($textoAjuste, 0, 50) . '..."',
                    'projeto?id=' . (int) $projeto['id'],
                    'bi-pencil-square text-danger'
                );
            }
        }

        $this->redirecionar('/cliente/projeto', 'Pedido de ajuste enviado para a equipe!', 'success');
    }

    // salvar checklist de envio de materiais (etapa 1 - requisitos)
    public function salvarChecklistRequisitos(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto');

        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao  = Auth::usuario();

        if ($projeto) {
            $itens = $_POST['itens'] ?? [];
            if (!is_array($itens)) {
                $itens = [];
            }
            $projetoModel = new Projeto();
            $projetoModel->atualizarChecklistRequisitos((int) $projeto['id'], $itens);

            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Checklist de Materiais Atualizado',
                'Atualizou ' . count($itens) . ' itens de requisitos.'
            );

            $this->redirecionar('/cliente/projeto', 'Checklist de materiais atualizado com sucesso!', 'success');
        }

        $this->redirecionar('/cliente/projeto', 'Projeto não encontrado.', 'warning');
    }

    // salvar checklist de testes do cliente (etapa 4 - homologação)
    public function salvarChecklistHomologacao(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto');

        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao  = Auth::usuario();

        if ($projeto) {
            $itens = $_POST['itens'] ?? [];
            if (!is_array($itens)) {
                $itens = [];
            }
            $projetoModel = new Projeto();
            $projetoModel->atualizarChecklistHomologacao((int) $projeto['id'], $itens);

            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Checklist de Testes Salvo',
                'Validou ' . count($itens) . ' testes de homologação.'
            );

            $this->redirecionar('/cliente/projeto', 'Checklist de testes e validação atualizado!', 'success');
        }

        $this->redirecionar('/cliente/projeto', 'Projeto não encontrado.', 'warning');
    }

    // salvar avaliação de satisfação do cliente (etapa 5 - concluído)
    public function salvarAvaliacao(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto');

        $nota = (int) ($_POST['nota'] ?? 5);
        $comentario = trim($_POST['comentario'] ?? '');

        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao  = Auth::usuario();

        if ($projeto) {
            $projetoModel = new Projeto();
            $projetoModel->salvarAvaliacao((int) $projeto['id'], $nota, $comentario);

            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Avaliação de Satisfação',
                "Avaliou o projeto com {$nota} estrelas: \"{$comentario}\""
            );

            $usuarioModel = new Usuario();
            $admin = $usuarioModel->buscarPorEmail('admin@iikliente.com');
            if ($admin) {
                $notifModel = new Notificacao();
                $notifModel->criar(
                    (int) $admin['id'],
                    'Nova Avaliação Recebida',
                    ($sessao['nome'] ?? 'Cliente') . " avaliou o projeto {$projeto['nome']} com nota {$nota} de 5!",
                    'projeto?id=' . (int) $projeto['id'],
                    'bi-star-fill text-warning'
                );
            }

            $this->redirecionar('/cliente/projeto', 'Obrigado pela sua avaliação! Sua opinião é fundamental para nossa equipe.', 'success');
        }

        $this->redirecionar('/cliente/projeto', 'Projeto não encontrado.', 'warning');
    }

    // upload seguro de arquivo do projeto com validacao real de mime type
    public function enviarArquivo(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto');

        if (empty($_FILES['arquivo']['name'])) {
            $this->redirecionar('/cliente/projeto#enviar-arquivo', 'Selecione um arquivo para enviar.', 'danger');
        }

        // Validacao com finfo_file (Item 6)
        $val = UploadValidador::validar($_FILES['arquivo'], 5.0, ['pdf', 'png', 'jpg', 'jpeg', 'zip', 'docx']);
        if (!$val['valido']) {
            $this->redirecionar('/cliente/projeto#enviar-arquivo', $val['erro'], 'danger');
        }

        $nomeOriginal = $_FILES['arquivo']['name'];
        $extensao     = $val['extensao'];
        $tamanhoKb    = $val['tamanho_kb'];

        $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($pastaUploads)) {
            mkdir($pastaUploads, 0775, true);
        }

        // Gera nome unico para nunca sobrescrever ou executar script malicioso
        $nomeSalvo = 'proj_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;
        move_uploaded_file($_FILES['arquivo']['tmp_name'], $pastaUploads . '/' . $nomeSalvo);

        $descricao = trim($_POST['descricao'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'Material do Cliente');
        $titulo = ($descricao !== '') ? $descricao : pathinfo($nomeOriginal, PATHINFO_FILENAME);

        $projetoModel = new Projeto();
        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao = Auth::usuario();

        if (!$projeto && $sessao) {
            $clienteModel = new Cliente();
            $ficha = $clienteModel->buscarPorUsuarioOuEmail((int) $sessao['id'], (string) $sessao['email']);
            $idCli = $ficha ? (int) $ficha['id'] : $clienteModel->criar([
                'usuario_id' => (int) $sessao['id'],
                'nome'       => $sessao['nome'],
                'cnpj'       => 'Não informado',
                'segmento'   => 'tecnologia',
                'contato'    => $sessao['nome'],
                'email'      => $sessao['email'],
                'telefone'   => '',
                'status'     => 'prospecto',
            ]);
            $projetoModel->criarInicialParaCliente($idCli, 'Projeto sob Análise', 0.00);
            $projeto = $projetoModel->buscarProjetoDoCliente($idCli);
        }

        if ($projeto) {
            $projetoModel->salvarArquivo(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                $titulo,
                $nomeOriginal,
                $nomeSalvo,
                $extensao,
                $tamanhoKb,
                $categoria
            );

            // Registra no Historico de Auditoria
            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                $sessao ? (int) $sessao['id'] : null,
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Arquivo Enviado',
                "Enviou o arquivo '{$nomeOriginal}' ({$categoria})."
            );

            // Notifica administradores
            $usuarioModel = new Usuario();
            $admin = $usuarioModel->buscarPorEmail('admin@iikliente.com');
            if ($admin) {
                $notifModel = new Notificacao();
                $notifModel->criar(
                    (int) $admin['id'],
                    'Novo Arquivo Enviado',
                    ($sessao['nome'] ?? 'Cliente') . " enviou '{$titulo}' em {$projeto['nome']}.",
                    'projeto?id=' . (int) $projeto['id'] . '#arquivos',
                    'bi-file-earmark-arrow-up text-primary'
                );
            }
        }

        $this->redirecionar('/cliente/projeto#enviar-arquivo', 'Arquivo "' . $titulo . '" enviado com sucesso!', 'success');
    }

    // cliente enviar comprovante de pagamento de parcela
    public function enviarComprovanteParcela(): void
    {
        $this->validarCsrfOuVoltar('/cliente/projeto#financeiro');

        $parcelaId = (int) ($_POST['parcela_id'] ?? 0);
        if ($parcelaId <= 0) {
            $this->redirecionar('/cliente/projeto#financeiro', 'Parcela inválida.', 'danger');
        }

        if (empty($_FILES['comprovante']['name'])) {
            $this->redirecionar('/cliente/projeto#financeiro', 'Selecione o arquivo do comprovante.', 'warning');
        }

        $val = UploadValidador::validar($_FILES['comprovante'], 5.0, ['pdf', 'png', 'jpg', 'jpeg']);
        if (!$val['valido']) {
            $this->redirecionar('/cliente/projeto#financeiro', $val['erro'], 'danger');
        }

        $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($pastaUploads)) {
            mkdir($pastaUploads, 0775, true);
        }

        $nomeSalvo = 'comp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $val['extensao'];
        move_uploaded_file($_FILES['comprovante']['tmp_name'], $pastaUploads . '/' . $nomeSalvo);

        $projetoModel = new Projeto();
        $projetoModel->enviarComprovanteParcela($parcelaId, $nomeSalvo);

        $projeto = $this->obterProjetoDoUsuarioLogado();
        $sessao = Auth::usuario();
        if ($projeto && $sessao) {
            $projetoModel->registrarHistorico(
                (int) $projeto['id'],
                (int) $sessao['id'],
                'cliente',
                $sessao['nome'] ?? 'Cliente',
                'Comprovante Enviado',
                'Enviou comprovante de pagamento da parcela #' . $parcelaId
            );

            $usuarioModel = new Usuario();
            $admin = $usuarioModel->buscarPorEmail('admin@iikliente.com');
            if ($admin) {
                $notif = new Notificacao();
                $notif->criar(
                    (int) $admin['id'],
                    'Comprovante de Pagamento',
                    ($sessao['nome'] ?? 'Cliente') . " anexou comprovante para parcela em {$projeto['nome']}.",
                    'projeto?id=' . (int) $projeto['id'] . '#financeiro',
                    'bi-receipt text-success'
                );
            }
        }

        $this->redirecionar('/cliente/projeto#financeiro', 'Comprovante enviado com sucesso! Nossa equipe financeira irá validar.', 'success');
    }

    // marcar notificacao como lida (ajax ou post)
    public function marcarNotificacaoLida(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $sessao = Auth::usuario();
        if ($id > 0 && $sessao) {
            $notifModel = new Notificacao();
            $notifModel->marcarComoLida($id, (int) $sessao['id']);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => true]);
            exit;
        }

        $retorno = $_SERVER['HTTP_REFERER'] ?? $this->urlBase . '/';
        header('Location: ' . $retorno);
        exit;
    }

    // limpar todas as notificacoes do usuario logado
    public function limparTodasNotificacoes(): void
    {
        $sessao = Auth::usuario();
        if ($sessao) {
            $notifModel = new Notificacao();
            $notifModel->marcarTodasComoLidas((int) $sessao['id']);
        }

        $retorno = $_SERVER['HTTP_REFERER'] ?? $this->urlBase . '/';
        header('Location: ' . $retorno);
        exit;
    }

    // download seguro de arquivo do projeto
    public function baixarArquivo(): void
    {
        $idArquivo = (int) ($_GET['id'] ?? 0);
        $projetoModel = new Projeto();
        $arquivo = $projetoModel->buscarArquivoPorId($idArquivo);

        if (!$arquivo) {
            $this->redirecionar('/cliente/projeto', 'Arquivo não encontrado.', 'warning');
        }

        $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($pastaUploads)) {
            mkdir($pastaUploads, 0775, true);
        }

        $caminhoFisico = $pastaUploads . '/' . basename($arquivo['nome_salvo']);

        // Se for um dos arquivos de demonstracao da migration e ainda nao existir no disco, cria um arquivo de exemplo
        if (!file_exists($caminhoFisico)) {
            $conteudoDemo = "iiKliente - Gestão de Clientes\n"
                . "=========================================\n"
                . "Documento do Projeto: " . $arquivo['titulo'] . "\n"
                . "Arquivo original: " . $arquivo['nome_original'] . "\n"
                . "Data: " . $arquivo['criado_em'] . "\n";
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

    // exibir a tela de orcamento e questionario com dados salvos
    public function questionario(): void
    {
        $sessao = Auth::usuario();
        $solicitacaoModel = new Solicitacao();
        $projeto = $this->obterProjetoDoUsuarioLogado();

        $ultimaSolicitacao = null;
        if ($sessao) {
            $ultimaSolicitacao = $solicitacaoModel->buscarUltimaDoUsuario((int) $sessao['id'], (string) $sessao['email']);
        }

        $this->renderizar('cliente/questionario', [
            'projeto'           => $projeto,
            'ultimaSolicitacao' => $ultimaSolicitacao,
        ]);
    }

    // salvar respostas finais ou rascunho do questionario no banco
    public function salvarQuestionario(): void
    {
        $this->validarCsrfOuVoltar('/cliente/questionario');

        $acao        = trim($_POST['acao'] ?? 'enviar');
        $ehRascunho  = ($acao === 'rascunho');

        $tipoProjeto = trim($_POST['tipo_projeto'] ?? '');
        $objetivo    = trim($_POST['objetivo'] ?? '');
        $publicoAlvo = trim($_POST['publico_alvo'] ?? '');
        $modulosArr  = $_POST['modulos'] ?? [];
        $referencias = trim($_POST['referencias'] ?? '');
        $prazo       = trim($_POST['prazo'] ?? '');
        $observacoes = trim($_POST['observacoes'] ?? '');

        // Se nao for rascunho, exige os campos obrigatorios
        if (!$ehRascunho && ($tipoProjeto === '' || $publicoAlvo === '' || empty($modulosArr))) {
            $this->redirecionar('/cliente/questionario', 'Preencha os campos obrigatórios e marque pelo menos um módulo.', 'danger');
        }

        $mapaTipo = [
            'landing-page'       => 'website',
            'site-institucional' => 'website',
            'loja-virtual'       => 'loja',
            'sistema-interno'    => 'sistema',
            'app-mobile'         => 'app',
        ];
        $faixas = [
            'landing-page'       => 'R$ 2.000 – 5.000',
            'site-institucional' => 'R$ 5.000 – 10.000',
            'loja-virtual'       => 'R$ 10.000 – 20.000',
            'sistema-interno'    => 'R$ 15.000 – 30.000',
            'app-mobile'         => 'R$ 20.000 – 40.000',
        ];

        $sessao = Auth::usuario();
        $nomeCliente  = $sessao['nome'] ?? 'Cliente';
        $emailCliente = $sessao['email'] ?? '';

        $clienteModel = new Cliente();
        $fichaCliente = $sessao ? $clienteModel->buscarPorUsuarioOuEmail((int) $sessao['id'], $emailCliente) : null;

        if (!$fichaCliente && $sessao) {
            $idFicha = $clienteModel->criar([
                'usuario_id' => (int) $sessao['id'],
                'nome'       => $nomeCliente,
                'cnpj'       => 'Não informado',
                'segmento'   => 'tecnologia',
                'contato'    => $nomeCliente,
                'email'      => $emailCliente,
                'telefone'   => '',
                'status'     => 'prospecto',
            ]);
            $fichaCliente = $clienteModel->buscarPorId($idFicha);
        }

        $nomesTipoProjeto = [
            'landing-page'       => 'Landing Page',
            'site-institucional' => 'Website Institucional',
            'loja-virtual'       => 'Loja Virtual',
            'sistema-interno'    => 'Sistema Interno',
            'app-mobile'         => 'Aplicativo Mobile',
        ];
        $nomeProjetoEscolhido = $nomesTipoProjeto[$tipoProjeto] ?? 'Website Institucional';

        $solicitacaoModel = new Solicitacao();
        $ultimaExistente = $sessao ? $solicitacaoModel->buscarUltimaDoUsuario((int) $sessao['id'], $emailCliente) : null;
        $idParaAtualizar = ($ultimaExistente && in_array($ultimaExistente['status'], ['rascunho', 'nova'], true))
            ? (int) $ultimaExistente['id']
            : null;

        $solicitacaoModel->criar([
            'id_existente' => $idParaAtualizar,
            'usuario_id'   => $sessao['id'] ?? null,
            'cliente_id'   => $fichaCliente ? (int) $fichaCliente['id'] : null,
            'nome'         => $nomeCliente,
            'email'        => $emailCliente,
            'telefone'     => !empty($fichaCliente['telefone']) ? $fichaCliente['telefone'] : 'Não informado',
            'tipo'         => $mapaTipo[$tipoProjeto] ?? 'website',
            'tipo_texto'   => $nomeProjetoEscolhido,
            'faixa'        => $faixas[$tipoProjeto] ?? 'R$ 5.000 – 10.000',
            'prazo'        => $prazo !== '' ? $prazo : '1-3-meses',
            'objetivo'     => $objetivo,
            'publico_alvo' => $publicoAlvo,
            'modulos'      => implode(',', (array) $modulosArr),
            'referencias'  => $referencias,
            'mensagem'     => '"' . ($observacoes !== '' ? $observacoes : ($ehRascunho ? 'Rascunho salvo pelo cliente.' : 'Questionário enviado pela área do cliente.')) . '"',
            'status'       => $ehRascunho ? 'rascunho' : 'nova',
        ]);

        if ($ehRascunho) {
            $this->redirecionar('/cliente/questionario', 'Rascunho salvo com sucesso! Você pode continuar preenchendo quando quiser.', 'success');
        }

        // Quando envia em definitivo, cria o projeto inicial em Planejamento (20%) caso o cliente ainda nao tenha um projeto
        if ($fichaCliente) {
            $projetoModel = new Projeto();
            $projetoExistente = $projetoModel->buscarProjetoDoCliente((int) $fichaCliente['id']);
            if (!$projetoExistente) {
                $projetoModel->criarInicialParaCliente(
                    (int) $fichaCliente['id'],
                    $nomeProjetoEscolhido,
                    0.00,
                    $observacoes !== '' ? $observacoes : 'Projeto registrado via questionário do cliente (' . $publicoAlvo . ').'
                );
            }
        }

        // Envia confirmacao por e-mail usando PHPMailer
        Mailer::enviar(
            $emailCliente,
            $nomeCliente,
            'Recebemos o seu Questionário - iiKliente',
            "<p>Olá, <strong>" . htmlspecialchars($nomeCliente) . "</strong>!</p>" .
            "<p>Recebemos as respostas do questionário do seu projeto. Nossa equipe já está analisando para enviar o orçamento oficial.</p>"
        );

        $this->redirecionar('/cliente/questionario', 'Respostas enviadas com sucesso! Nossa equipe vai analisar.', 'success');
    }

    // exibir a tela de perfil do cliente
    public function perfil(): void
    {
        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $dadosCliente = $sessao ? $usuarioModel->buscarPorId((int) $sessao['id']) : $usuarioModel->buscarPorEmail('maria@email.com');

        $this->renderizar('cliente/perfil', [
            'perfilUsuario' => $dadosCliente,
        ]);
    }

    // salvar dados do perfil do cliente
    public function salvarPerfil(): void
    {
        $this->validarCsrfOuVoltar('/cliente/perfil');

        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $cliente = $sessao ? $usuarioModel->buscarPorId((int) $sessao['id']) : $usuarioModel->buscarPorEmail('maria@email.com');
        $idUsuario = (int) ($cliente['id'] ?? 2);

        $nome     = trim($_POST['nome'] ?? 'Maria Silva');
        $email    = trim($_POST['email'] ?? 'maria@email.com');
        $telefone = trim($_POST['telefone'] ?? '');
        $empresa  = trim($_POST['empresa'] ?? '');
        $nomeFoto = null;

        if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $tamanhoMb = $_FILES['foto']['size'] / 1024 / 1024;

            if (!in_array($extensao, ['png', 'jpg', 'jpeg'], true) || $tamanhoMb > 2) {
                $this->redirecionar('/cliente/perfil', 'Foto inválida. Use PNG ou JPG de até 2 MB.', 'danger');
            }

            $pastaUploads = dirname(__DIR__, 2) . '/storage/uploads';
            if (!is_dir($pastaUploads)) {
                mkdir($pastaUploads, 0775, true);
            }

            $nomeFoto = 'avatar_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;
            move_uploaded_file($_FILES['foto']['tmp_name'], $pastaUploads . '/' . $nomeFoto);
        }

        $usuarioModel->atualizarDados($idUsuario, [
            'nome'         => $nome,
            'email'        => $email,
            'telefone'     => $telefone,
            'empresa_nome' => $empresa,
            'cnpj'         => $cliente['cnpj'] ?? null,
            'foto'         => $nomeFoto,
        ]);

        Auth::atualizarSessao($nome, $email, $empresa);
        $this->redirecionar('/cliente/perfil', 'Dados do perfil atualizados com sucesso!', 'success');
    }

    // alterar senha do cliente
    public function alterarSenha(): void
    {
        $this->validarCsrfOuVoltar('/cliente/perfil');

        $usuarioModel = new Usuario();
        $sessao = Auth::usuario();
        $cliente = $sessao ? $usuarioModel->buscarPorId((int) $sessao['id']) : $usuarioModel->buscarPorEmail('maria@email.com');
        $idUsuario = (int) ($cliente['id'] ?? 2);

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $senhaNova  = $_POST['senha_nova'] ?? '';

        if (strlen($senhaNova) < 8) {
            $this->redirecionar('/cliente/perfil', 'A nova senha precisa ter pelo menos 8 caracteres.', 'danger');
        }

        if ($usuarioModel->alterarSenha($idUsuario, $senhaAtual, $senhaNova)) {
            $this->redirecionar('/cliente/perfil', 'Senha alterada com sucesso!', 'success');
        }

        $this->redirecionar('/cliente/perfil', 'A senha atual informada está incorreta.', 'danger');
    }
}
