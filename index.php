<?php
/**
 * index.php (Front Controller)
 * ---------------------------------------------------------
 * Ponto de entrada unico da aplicacao MVC.
 * Carrega o Composer, le o arquivo .env, registra as rotas
 * e chama o Controller responsavel pela pagina.
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\EmpresaController;
use App\Controllers\HomeController;
use App\Controllers\PortalClienteController;
use App\Core\Router;

// --- Bloco 1: Carregar as variaveis de ambiente do arquivo .env ---
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// --- Bloco 2: Descobrir o caminho base do projeto (ex: /projetos/IISoul) ---
$caminhoBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

// --- Bloco 3: Cadastrar as rotas (GET e POST) no Router ---
$router = new Router();

// Rotas Publicas e Autenticacao (Rotas limpas sem extensao)
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'telaLogin']);
$router->post('/login', [AuthController::class, 'entrar']);
$router->post('/cadastro', [AuthController::class, 'cadastrar']);
$router->post('/recuperar-senha', [AuthController::class, 'recuperarSenha']);
$router->get('/redefinir-senha', [AuthController::class, 'telaRedefinirSenha']);
$router->post('/redefinir-senha-salvar', [AuthController::class, 'salvarNovaSenha']);
$router->get('/logout', [AuthController::class, 'sair']);

// Rotas Globais de Notificacoes
$router->post('/notificacao-marcar-lida', [PortalClienteController::class, 'marcarNotificacaoLida']);
$router->post('/notificacoes-limpar', [PortalClienteController::class, 'limparTodasNotificacoes']);

// Rotas da Area da Empresa (Rotas limpas sem extensao)
$router->get('/empresa/dashboard', [EmpresaController::class, 'dashboard']);
$router->get('/empresa/clientes', [EmpresaController::class, 'clientes']);
$router->get('/empresa/busca', [EmpresaController::class, 'clientes']);
$router->post('/empresa/cliente-salvar', [EmpresaController::class, 'salvarCliente']);
$router->post('/empresa/cliente-excluir', [EmpresaController::class, 'excluirCliente']);
$router->get('/empresa/cliente', [EmpresaController::class, 'detalheCliente']);
$router->get('/empresa/projetos', [EmpresaController::class, 'projetos']);
$router->get('/empresa/projeto', [EmpresaController::class, 'detalheProjeto']);
$router->post('/empresa/projeto-etapa', [EmpresaController::class, 'atualizarEtapaProjeto']);
$router->post('/empresa/projeto-prototipo', [EmpresaController::class, 'salvarPrototipo']);
$router->post('/empresa/projeto-links', [EmpresaController::class, 'salvarLinksEtapas']);
$router->post('/empresa/checklist-requisitos', [EmpresaController::class, 'salvarChecklistRequisitos']);
$router->post('/empresa/checklist-homologacao', [EmpresaController::class, 'salvarChecklistHomologacao']);
$router->post('/empresa/projeto-resolver-ajuste', [EmpresaController::class, 'resolverAjuste']);
$router->post('/empresa/projeto-detalhes', [EmpresaController::class, 'atualizarDetalhesProjeto']);
$router->post('/empresa/projeto-parcelas', [EmpresaController::class, 'salvarParcelas']);
$router->post('/empresa/parcela-status', [EmpresaController::class, 'atualizarStatusParcela']);
$router->get('/empresa/exportar-csv', [EmpresaController::class, 'exportarCsv']);
$router->get('/empresa/projeto-termo-pdf', [EmpresaController::class, 'imprimirTermoEntrega']);
$router->post('/empresa/arquivo-enviar', [EmpresaController::class, 'enviarArquivo']);
$router->get('/empresa/arquivo-baixar', [EmpresaController::class, 'baixarArquivo']);
$router->post('/empresa/arquivo-excluir', [EmpresaController::class, 'excluirArquivo']);
$router->get('/empresa/solicitacoes', [EmpresaController::class, 'solicitacoes']);
$router->post('/empresa/solicitacao-salvar', [EmpresaController::class, 'salvarSolicitacao']);
$router->post('/empresa/solicitacao-anotacao', [EmpresaController::class, 'salvarAnotacao']);
$router->post('/empresa/orcamento-enviar', [EmpresaController::class, 'enviarOrcamento']);
$router->post('/empresa/solicitacao-converter', [EmpresaController::class, 'converterSolicitacao']);
$router->post('/empresa/solicitacao-recusar', [EmpresaController::class, 'recusarSolicitacao']);
$router->get('/empresa/configuracoes', [EmpresaController::class, 'configuracoes']);
$router->post('/empresa/configuracoes-salvar', [EmpresaController::class, 'salvarConfiguracoes']);
$router->post('/empresa/notificacoes-salvar', [EmpresaController::class, 'salvarNotificacoes']);
$router->post('/empresa/senha-alterar', [EmpresaController::class, 'alterarSenha']);

// Rotas da Area do Cliente (Rotas limpas sem extensao)
$router->get('/cliente/inicio', [PortalClienteController::class, 'inicio']);
$router->get('/cliente/projeto', [PortalClienteController::class, 'projeto']);
$router->get('/cliente/projeto-selecionar', [PortalClienteController::class, 'selecionarProjetoAtivo']);
$router->post('/cliente/parcela-comprovante', [PortalClienteController::class, 'enviarComprovanteParcela']);
$router->post('/cliente/etapa-aprovar', [PortalClienteController::class, 'aprovarEtapa']);
$router->post('/cliente/orcamento-aprovar', [PortalClienteController::class, 'aprovarOrcamento']);
$router->post('/cliente/ajuste-solicitar', [PortalClienteController::class, 'solicitarAjuste']);
$router->post('/cliente/checklist-requisitos', [PortalClienteController::class, 'salvarChecklistRequisitos']);
$router->post('/cliente/checklist-homologacao', [PortalClienteController::class, 'salvarChecklistHomologacao']);
$router->post('/cliente/projeto-avaliar', [PortalClienteController::class, 'salvarAvaliacao']);
$router->post('/cliente/arquivo-enviar', [PortalClienteController::class, 'enviarArquivo']);
$router->get('/cliente/arquivo-baixar', [PortalClienteController::class, 'baixarArquivo']);
$router->get('/cliente/questionario', [PortalClienteController::class, 'questionario']);
$router->post('/cliente/questionario-salvar', [PortalClienteController::class, 'salvarQuestionario']);
$router->get('/cliente/perfil', [PortalClienteController::class, 'perfil']);
$router->post('/cliente/perfil-salvar', [PortalClienteController::class, 'salvarPerfil']);
$router->post('/cliente/senha-alterar', [PortalClienteController::class, 'alterarSenha']);

// --- Bloco 4: Executar a rota requisitada ---
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$router->executar($metodo, $uri, $caminhoBase);
