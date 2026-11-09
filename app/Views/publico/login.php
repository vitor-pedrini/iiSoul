<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Acesse sua conta ou cadastre-se na plataforma iiKliente.">
  <title>Entrar - iiKliente</title>

  <!-- --- Bloco: Schema.org (JSON-LD) --- -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Entrar - iiKliente",
    "description": "Página de autenticação e cadastro de clientes da plataforma iiKliente."
  }
  </script>

  <!-- --- Bloco: Bootstrap 5 via CDN com Fallback Local e CSS Minificado --- -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" onerror="this.onerror=null;this.href='assets/vendor/bootstrap/css/bootstrap.min.css';">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/estilo.min.css">
</head>
<body class="bg-body-tertiary">

<?php if (!empty($flashToast)): ?>
  <div id="flash-toast-servidor" class="d-none" data-mensagem="<?= htmlspecialchars($flashToast['texto'], ENT_QUOTES, 'UTF-8') ?>" data-tipo="<?= htmlspecialchars($flashToast['tipo'], ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

  <!-- ================= MENU DO TOPO ================= -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="./">
        <i class="bi bi-laptop fs-2 text-primary me-2"></i>
        <span class="lh-1">
          <span class="fw-bold d-block">iiKliente</span>
          <small class="text-secondary fs-6">Gestão de Clientes</small>
        </span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-topo" aria-controls="menu-topo" aria-expanded="false" aria-label="Abrir menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menu-topo">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="./#funcionalidades">Funcionalidades</a></li>
          <li class="nav-item"><a class="nav-link" href="./#como-funciona">Como funciona</a></li>
          <li class="nav-item"><a class="nav-link" href="./#areas">Áreas do sistema</a></li>
          <li class="nav-item"><a class="nav-link" href="./#faq">FAQ</a></li>
        </ul>

        <div class="d-flex gap-2 align-items-center">
          <button type="button" class="btn btn-light btn-toggle-theme" id="btn-alternar-tema" aria-label="Alternar tema claro/escuro" title="Alternar Modo Escuro">
            <i class="bi bi-moon-stars-fill text-secondary"></i>
          </button>
          <a href="login" class="btn btn-outline-primary">Entrar</a>
          <a href="login#criar-conta" class="btn btn-primary">Solicitar orçamento</a>
        </div>
      </div>
    </div>
  </nav>


  <main class="container py-5">
    <div class="row align-items-center g-5">

      <!-- ========== LADO ESQUERDO ========== -->
      <div class="col-lg-6 d-none d-lg-block">
        <p class="text-secondary text-uppercase small mb-2">Uma plataforma, mais resultados</p>
        <h1 class="display-5 fw-bold">Acesse sua conta e continue evoluindo seu negócio</h1>
        <p class="lead text-secondary">
          Faça login para gerenciar seus clientes, solicitações, orçamentos e projetos.
          Ainda não tem uma conta? Crie agora e comece a usar em poucos minutos.
        </p>

        <ul class="list-unstyled my-4">
          <li class="d-flex mb-3">
            <span class="icone-caixa rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0">
              <i class="bi bi-bar-chart-fill fs-5"></i>
            </span>
            <div>
              <strong>Tudo em um só lugar</strong>
              <p class="text-secondary mb-0">Acompanhe clientes, orçamentos e projetos com total organização.</p>
            </div>
          </li>
          <li class="d-flex mb-3">
            <span class="icone-caixa rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0">
              <i class="bi bi-people-fill fs-5"></i>
            </span>
            <div>
              <strong>Para você e sua empresa</strong>
              <p class="text-secondary mb-0">Uma área exclusiva para acompanhar tudo do seu projeto.</p>
            </div>
          </li>
          <li class="d-flex">
            <span class="icone-caixa rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0">
              <i class="bi bi-shield-fill-check fs-5"></i>
            </span>
            <div>
              <strong>Seguro e confiável</strong>
              <p class="text-secondary mb-0">Seus dados sempre protegidos com padrões de segurança modernos.</p>
            </div>
          </li>
        </ul>

      </div>


      <!-- ========== LADO DIREITO: formularios ========== -->
      <div class="col-lg-6 col-xl-5 ms-xl-auto">
        <div class="card shadow">
          <div class="card-body p-4">

            <div class="text-center mb-3">
              <i class="bi bi-laptop fs-1 text-primary"></i>
              <h2 class="h4 fw-bold mb-0">iiKliente</h2>
              <p class="text-secondary mb-1">Gestão de Clientes</p>
              <p class="small text-secondary">Acesse sua conta ou crie uma nova para começar.</p>
            </div>

            <!-- Abas: Entrar / Criar conta -->
            <ul class="nav nav-pills nav-fill border rounded mb-4" id="abas-login" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="aba-entrar" data-bs-toggle="pill" data-bs-target="#painel-entrar" type="button" role="tab" aria-controls="painel-entrar" aria-selected="true">Entrar</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="aba-criar-conta" data-bs-toggle="pill" data-bs-target="#painel-criar-conta" type="button" role="tab" aria-controls="painel-criar-conta" aria-selected="false">Criar conta</button>
              </li>
            </ul>

            <div class="tab-content">

              <!-- ---------- ENTRAR ---------- -->
              <div class="tab-pane fade show active" id="painel-entrar" role="tabpanel" aria-labelledby="aba-entrar" tabindex="0">

                <form id="form-login" action="login" method="post" data-backend="true" novalidate>
                  <?= $campoCsrf ?? '' ?>

                  <div class="mb-3">
                    <label for="login-email" class="form-label fw-semibold small">E-mail</label>
                    <div class="input-group has-validation">
                      <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                      <input type="email" class="form-control" id="login-email" name="email" placeholder="seu@email.com" autocomplete="username" required>
                      <div class="invalid-feedback">Digite um e-mail válido.</div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="login-senha" class="form-label fw-semibold small">Senha</label>
                    <div class="input-group has-validation">
                      <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                      <input type="password" class="form-control" id="login-senha" name="senha" placeholder="Sua senha" autocomplete="current-password" minlength="8" required>
                      <button type="button" class="btn btn-outline-secondary btn-ver-senha" data-alvo="login-senha" aria-label="Mostrar ou esconder a senha">
                        <i class="bi bi-eye"></i>
                      </button>
                      <div class="invalid-feedback">A senha precisa ter pelo menos 8 caracteres.</div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="login-lembrar" name="lembrar" checked>
                      <label class="form-check-label small" for="login-lembrar">Lembrar de mim</label>
                    </div>
                    <a href="#" class="small" data-bs-toggle="modal" data-bs-target="#modalEsqueciSenha">Esqueci minha senha?</a>
                  </div>

                  <button type="submit" class="btn btn-primary w-100 py-2">
                    Entrar na minha conta <i class="bi bi-arrow-right ms-1"></i>
                  </button>
                </form>

                <div class="d-flex align-items-center my-3">
                  <hr class="flex-grow-1 m-0">
                  <span class="px-2 small text-secondary">ou</span>
                  <hr class="flex-grow-1 m-0">
                </div>

                <div class="row g-2">
                  <div class="col-sm-6">
                    <button type="button" class="btn btn-outline-secondary w-100" data-toast="Login com Google estará disponível em breve.">
                      <i class="bi bi-google me-1"></i> Google
                    </button>
                  </div>
                  <div class="col-sm-6">
                    <button type="button" class="btn btn-outline-secondary w-100" data-toast="Login com Microsoft estará disponível em breve.">
                      <i class="bi bi-microsoft me-1"></i> Microsoft
                    </button>
                  </div>
                </div>

                <p class="text-center small text-secondary mt-3 mb-0">
                  Ainda não tem uma conta?
                  <a href="#painel-criar-conta" data-bs-toggle="pill" data-bs-target="#painel-criar-conta">Crie agora</a> e comece a usar.
                </p>
              </div>


              <!-- ---------- CRIAR CONTA ---------- -->
              <div class="tab-pane fade" id="painel-criar-conta" role="tabpanel" aria-labelledby="aba-criar-conta" tabindex="0">

                <form id="form-cadastro" action="cadastro" method="post" data-backend="true" novalidate>
                  <?= $campoCsrf ?? '' ?>

                  <div class="mb-3">
                    <label for="cadastro-nome" class="form-label fw-semibold small">Nome completo</label>
                    <input type="text" class="form-control" id="cadastro-nome" name="nome" minlength="3" maxlength="100" autocomplete="name" required>
                    <div class="invalid-feedback">Digite seu nome (mínimo 3 letras).</div>
                  </div>

                  <div class="mb-3">
                    <label for="cadastro-email" class="form-label fw-semibold small">E-mail</label>
                    <input type="email" class="form-control" id="cadastro-email" name="email" placeholder="seu@email.com" autocomplete="email" required>
                    <div class="invalid-feedback">Digite um e-mail válido.</div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                      <label for="cadastro-senha" class="form-label fw-semibold small">Senha</label>
                      <input type="password" class="form-control" id="cadastro-senha" name="senha" minlength="8" autocomplete="new-password" required>
                      <div class="invalid-feedback">Mínimo de 8 caracteres.</div>
                    </div>
                    <div class="col-sm-6">
                      <label for="cadastro-confirmar" class="form-label fw-semibold small">Confirmar senha</label>
                      <input type="password" class="form-control" id="cadastro-confirmar" name="confirmar_senha" autocomplete="new-password" required>
                      <div class="invalid-feedback">As senhas precisam ser iguais.</div>
                    </div>
                  </div>

                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="cadastro-termos" name="termos" required>
                    <label class="form-check-label small" for="cadastro-termos">Li e aceito os termos de uso e a política de privacidade.</label>
                    <div class="invalid-feedback">Você precisa aceitar para continuar.</div>
                  </div>

                  <button type="submit" class="btn btn-primary w-100 py-2">
                    Criar minha conta <i class="bi bi-arrow-right ms-1"></i>
                  </button>
                </form>

                <p class="text-center small text-secondary mt-3 mb-0">
                  Já tem conta?
                  <a href="#painel-entrar" data-bs-toggle="pill" data-bs-target="#painel-entrar">Entrar</a>
                </p>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </main>


  <!-- ================= MODAL: ESQUECI A SENHA ================= -->
  <div class="modal fade" id="modalEsqueciSenha" tabindex="-1" aria-labelledby="tituloEsqueciSenha" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="needs-validation" action="recuperar-senha" method="post" data-backend="true" data-mensagem="Se o e-mail existir, enviaremos um link para redefinir a senha." novalidate>
          <?= $campoCsrf ?? '' ?>
          <div class="modal-header">
            <h3 class="modal-title h5" id="tituloEsqueciSenha">Recuperar senha</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <p class="small text-secondary">Digite o e-mail da sua conta e enviaremos um link para criar uma nova senha.</p>
            <label for="recuperar-email" class="form-label fw-semibold small">E-mail</label>
            <input type="email" class="form-control" id="recuperar-email" name="email" placeholder="seu@email.com" required>
            <div class="invalid-feedback">Digite um e-mail válido.</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Enviar link</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- ================= RODAPE ================= -->
  <footer class="bg-white border-top py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 small text-secondary">
      <span><i class="bi bi-laptop text-primary me-1"></i> <strong>iiKliente</strong> - Gestão de Clientes</span>
      <a href="./" class="text-secondary text-decoration-none">Voltar para a página inicial</a>
      <span>© 2026 iiKliente. Desenvolvido por<br><a href="https://capsulum.com.br/vitor-pedrini" target="_blank" rel="noopener noreferrer" class="text-secondary">Vitor Hugo Dos Santos Pedrini</a>.</span>
    </div>
  </footer>

  <!-- --- Bloco: Scripts com Fallback Local e Arquivo Minificado --- -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>if (typeof bootstrap === 'undefined') { document.write('<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"><\/script>'); }</script>
  <script src="assets/js/main.min.js?v=2"></script>
</body>
</html>
