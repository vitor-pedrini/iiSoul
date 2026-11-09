<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Mailer;
use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\Usuario;
use Throwable;

/**
 * AuthController
 * Cuida do login, cadastro de nova conta (com transacao ACID), recuperacao de senha (PHPMailer) e logout.
 */
class AuthController extends Controller
{
    // abre a tela de login e cadastro
    public function telaLogin(): void
    {
        $this->renderizar('publico/login');
    }

    // processa o login e valida as credenciais
    public function entrar(): void
    {
        $this->validarCsrfOuVoltar('/login');

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $this->redirecionar('/login', 'Preencha o e-mail e a senha.', 'danger');
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        // Verifica se achou o usuario no banco e se a senha confere
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            Auth::entrar($usuario);

            // Redireciona conforme o perfil gravado no banco (empresa ou cliente)
            if ($usuario['perfil'] === 'empresa') {
                $this->redirecionar('/empresa/dashboard', 'Bem-vindo de volta, ' . $usuario['nome'] . '!', 'success');
            } else {
                $this->redirecionar('/cliente/inicio', 'Olá, ' . $usuario['nome'] . '!', 'success');
            }
        }

        $this->redirecionar('/login', 'E-mail ou senha incorretos. Confira os dados e tente novamente.', 'danger');
    }

    // cadastra o usuario e ja cria o registro de cliente junto
    public function cadastrar(): void
    {
        $this->validarCsrfOuVoltar('/login#criar-conta');

        $nome      = trim($_POST['nome'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $senha     = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        if (mb_strlen($nome) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirecionar('/login#criar-conta', 'Informe um nome e e-mail válidos.', 'danger');
        }

        if (strlen($senha) < 8 || $senha !== $confirmar) {
            $this->redirecionar('/login#criar-conta', 'As senhas precisam ser iguais e ter pelo menos 8 caracteres.', 'danger');
        }

        $usuarioModel = new Usuario();
        if ($usuarioModel->buscarPorEmail($email) !== null) {
            $this->redirecionar('/login', 'Este e-mail já está cadastrado. Faça login.', 'warning');
        }

        $pdo = Database::conectar();

        try {
            // Inicia transacao ACID para criar Usuario + Ficha de Cliente inicial juntos
            $pdo->beginTransaction();

            // 1. Cria o usuario como cliente
            $novoId = $usuarioModel->criar($nome, $email, $senha, 'cliente');

            // 2. Cria ou vincula a ficha de cliente correspondente (como prospecto ate aprovar orcamento)
            $clienteModel = new Cliente();
            $clienteExistente = $clienteModel->buscarPorUsuarioOuEmail($novoId, $email);

            if (!$clienteExistente) {
                $clienteModel->criar([
                    'usuario_id' => $novoId,
                    'nome'       => $nome,
                    'cnpj'       => 'Não informado',
                    'segmento'   => 'tecnologia',
                    'contato'    => $nome,
                    'email'      => $email,
                    'telefone'   => '',
                    'status'     => 'prospecto',
                ]);
            }

            $pdo->commit();
        } catch (Throwable $erro) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->redirecionar('/login#criar-conta', 'Erro ao criar conta. Tente novamente.', 'danger');
        }

        // Envia e-mail de boas-vindas usando o PHPMailer
        Mailer::enviar(
            $email,
            $nome,
            'Bem-vindo à iiKliente!',
            "<h2>Olá, " . htmlspecialchars($nome) . "!</h2><p>Sua conta foi criada com sucesso na plataforma iiKliente.</p>"
        );

        // Ja autentica o novo usuario e leva para a area do cliente
        $novoUsuario = $usuarioModel->buscarPorId($novoId);
        if ($novoUsuario) {
            Auth::entrar($novoUsuario);
            $this->redirecionar('/cliente/inicio', 'Conta criada com sucesso! Bem-vindo(a)!', 'success');
        }

        $this->redirecionar('/login', 'Conta criada! Agora é só entrar.', 'success');
    }

    // gera o token e manda o link pro email do usuario
    public function recuperarSenha(): void
    {
        $this->validarCsrfOuVoltar('/login');

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirecionar('/login', 'Digite um e-mail válido.', 'danger');
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if ($usuario) {
            $token = $usuarioModel->criarTokenRecuperacao((int) $usuario['id'], $usuario['email']);

            $esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $linkCompleto = $esquema . '://' . $host . $this->urlBase . '/redefinir-senha?token=' . $token;

            $mensagemHtml = "
                <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                  <h2 style='color: #0F766E;'>Recuperação de Senha - iiKliente</h2>
                  <p>Olá, <strong>" . htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') . "</strong>.</p>
                  <p>Recebemos uma solicitação para redefinir a senha da sua conta na plataforma iiKliente.</p>
                  <p>Para cadastrar uma nova senha com segurança, clique no botão abaixo. Este link expira em 1 hora:</p>
                  <div style='margin: 25px 0;'>
                    <a href='" . htmlspecialchars($linkCompleto, ENT_QUOTES, 'UTF-8') . "' style='background-color: #0F766E; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Redefinir Minha Senha</a>
                  </div>
                  <p style='color: #64748b; font-size: 13px;'>Se o botão não funcionar, copie e cole o link no seu navegador:<br>" . htmlspecialchars($linkCompleto, ENT_QUOTES, 'UTF-8') . "</p>
                  <p style='color: #94a3b8; font-size: 12px; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px;'>Se você não solicitou a redefinição de senha, nenhuma ação é necessária.</p>
                </div>
            ";

            Mailer::enviar(
                $usuario['email'],
                $usuario['nome'],
                'Recuperação de Senha - iiKliente',
                $mensagemHtml
            );
        }

        $this->redirecionar('/login', 'Se o e-mail estiver cadastrado, enviamos um link seguro para redefinir sua senha.', 'success');
    }

    // valida o token pra liberar a troca de senha
    public function telaRedefinirSenha(): void
    {
        $token = trim($_GET['token'] ?? '');
        if ($token === '') {
            $this->redirecionar('/login', 'Token de recuperação não informado.', 'danger');
        }

        $usuarioModel = new Usuario();
        $registro = $usuarioModel->buscarTokenValido($token);
        if (!$registro) {
            $this->redirecionar('/login', 'O link de recuperação é inválido ou já expirou. Solicite um novo link.', 'warning');
        }

        $this->renderizar('publico/redefinir_senha', [
            'token' => $token,
            'email' => $registro['email'],
        ]);
    }

    // salva a nova senha e invalida o token
    public function salvarNovaSenha(): void
    {
        $token = trim($_POST['token'] ?? '');
        $this->validarCsrfOuVoltar('/redefinir-senha?token=' . urlencode($token));

        $senhaNova  = $_POST['senha'] ?? '';
        $confirmar  = $_POST['confirmar_senha'] ?? '';

        if (strlen($senhaNova) < 8 || $senhaNova !== $confirmar) {
            $this->redirecionar('/redefinir-senha?token=' . urlencode($token), 'As senhas devem ser iguais e ter pelo menos 8 caracteres.', 'danger');
        }

        $usuarioModel = new Usuario();
        if ($usuarioModel->redefinirSenhaComToken($token, $senhaNova)) {
            $this->redirecionar('/login', 'Senha redefinida com sucesso! Você já pode entrar com sua nova senha.', 'success');
        }

        $this->redirecionar('/login', 'Não foi possível redefinir a senha. O link pode ter expirado.', 'danger');
    }

    // faz logout e limpa a sessao
    public function sair(): void
    {
        Auth::sair();
        $this->redirecionar('/login', 'Você saiu da sua conta com segurança.', 'primary');
    }
}
