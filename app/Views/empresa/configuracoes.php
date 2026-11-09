<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Configurações - iiKliente</title>

  <!-- --- Bloco: Bootstrap 5 via CDN com Fallback Local e CSS Minificado --- -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" onerror="this.onerror=null;this.href='../assets/vendor/bootstrap/css/bootstrap.min.css';">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
  <link rel="stylesheet" href="../assets/css/estilo.min.css">
</head>
<body class="bg-body-tertiary">

<?php if (!empty($flashToast)): ?>
  <div id="flash-toast-servidor" class="d-none" data-mensagem="<?= htmlspecialchars($flashToast['texto'], ENT_QUOTES, 'UTF-8') ?>" data-tipo="<?= htmlspecialchars($flashToast['tipo'], ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<div class="d-flex min-vh-100 bg-white">

  <!-- ================= MENU LATERAL ================= -->
  <aside class="offcanvas-lg offcanvas-start menu-lateral flex-shrink-0" tabindex="-1" id="menu-lateral" aria-labelledby="titulo-menu">

    <div class="offcanvas-header border-bottom">
      <h2 class="offcanvas-title h6" id="titulo-menu">Menu</h2>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#menu-lateral" aria-label="Fechar"></button>
    </div>

    <div class="offcanvas-body flex-grow-1 d-flex flex-column p-3">
      <a class="d-flex align-items-center text-decoration-none text-body mb-4" href="dashboard">
        <i class="bi bi-laptop fs-2 text-primary me-2"></i>
        <span class="lh-1">
          <span class="fw-bold d-block">iiKliente</span>
          <small class="text-secondary">Gestão de Clientes</small>
        </span>
      </a>

      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link" href="dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes"><i class="bi bi-people-fill me-2"></i> Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="projetos"><i class="bi bi-folder-fill me-2"></i> Projetos</a></li>
        <li class="nav-item"><a class="nav-link" href="solicitacoes"><i class="bi bi-file-earmark-text-fill me-2"></i> Solicitações / Orçamentos</a></li>
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="configuracoes"><i class="bi bi-gear-fill me-2"></i> Configurações</a></li>
      </ul>

      <div class="mt-auto pt-4">
        <div class="card mb-3">
          <div class="card-body p-3">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-headset fs-3 text-primary me-2"></i>
              <strong class="small">Precisa de ajuda?</strong>
            </div>
            <p class="small text-secondary">Acesse nossa central de suporte ou entre em contato.</p>
            <button type="button" class="btn btn-outline-primary btn-sm w-100" data-toast="Suporte: vitorh.pedrini@gmail.com">Ver suporte</button>
          </div>
        </div>

        <button type="button" class="btn btn-link text-secondary text-decoration-none d-none d-lg-block p-0" id="btn-recolher-menu">
          <i class="bi bi-chevron-left me-1"></i> Recolher menu
        </button>
      </div>
    </div>
  </aside>


  <!-- ================= AREA PRINCIPAL ================= -->
  <div class="flex-grow-1 d-flex flex-column bg-body-tertiary border-start overflow-hidden">

    <!-- Barra do topo -->
    <header class="bg-white border-bottom px-3 py-2 d-flex align-items-center gap-2">
      <button type="button" class="btn btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#menu-lateral" aria-controls="menu-lateral" aria-label="Abrir menu">
        <i class="bi bi-list"></i>
      </button>
      <button type="button" class="btn btn-outline-secondary d-none" id="btn-expandir-menu" aria-label="Mostrar menu">
        <i class="bi bi-layout-sidebar"></i>
      </button>

      <form class="needs-validation flex-grow-1" role="search" action="busca" method="get" data-backend="true">
        <div class="input-group">
          <button class="btn btn-outline-secondary" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
          <input type="search" class="form-control" name="q" placeholder="Buscar clientes, projetos, orçamentos..." aria-label="Buscar">
        </div>
      </form>

      <select class="form-select w-auto d-none d-xl-block filtro-topo-periodo" aria-label="Filtrar por período">
        <option value="12">Todos os períodos</option>
        <option value="6">Últimos 6 meses</option>
        <option value="3">Últimos 3 meses</option>
      </select>
      <select class="form-select w-auto d-none d-xl-block filtro-topo-projeto" aria-label="Filtrar por projeto">
        <option value="todos">Todos os projetos</option>
        <option value="andamento">Em andamento</option>
        <option value="concluidos">Concluídos</option>
      </select>

      <!-- Item 10: Botão de Modo Escuro / Dark Mode -->
      <button type="button" class="btn btn-light btn-toggle-theme" id="btn-alternar-tema" aria-label="Alternar tema claro/escuro" title="Alternar Modo Escuro">
        <i class="bi bi-moon-stars-fill text-secondary"></i>
      </button>

      <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalComoFunciona">
        <i class="bi bi-question-circle"></i>
        <span class="d-none d-sm-inline">Como funciona</span>
      </button>

      <!-- Item 7: Dropdown de Notificações Persistentes -->
      <div class="dropdown">
        <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificações">
          <i class="bi bi-bell-fill text-primary"></i>
          <?php if (!empty($totalNotifsNaoLidas) && $totalNotifsNaoLidas > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?= (int) $totalNotifsNaoLidas ?>
              <span class="visually-hidden">notificações não lidas</span>
            </span>
          <?php elseif (!empty($notificacoesTopo)): ?>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
              <span class="visually-hidden">Novas notificações</span>
            </span>
          <?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li class="d-flex justify-content-between align-items-center px-3 py-1 border-bottom">
            <h6 class="dropdown-header p-0 mb-0">Notificações</h6>
            <form action="<?= $urlBase ?>/notificacoes-limpar" method="post" class="d-inline m-0">
              <?= $campoCsrf ?? '' ?>
              <button type="submit" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none small">Limpar</button>
            </form>
          </li>
          <?php if (!empty($notificacoesTopo)): ?>
            <?php foreach ($notificacoesTopo as $notif): ?>
              <li>
                <a class="dropdown-item small notificacao-item py-2 <?= empty($notif['lida']) ? 'fw-bold' : 'text-secondary' ?>" href="<?= htmlspecialchars($notif['link'], ENT_QUOTES, 'UTF-8') ?>" data-notif-id="<?= (int)($notif['id'] ?? 0) ?>">
                  <i class="bi <?= htmlspecialchars($notif['icone'], ENT_QUOTES, 'UTF-8') ?> me-2"></i> 
                  <?= htmlspecialchars($notif['texto'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span class="dropdown-item-text small text-secondary">Nenhuma notificação nova</span></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="dropdown">
        <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold me-2">E</span>
          <span class="lh-sm text-start d-none d-md-block">
            <strong class="d-block small"><?= htmlspecialchars($empresa['nome'] ?? 'Empresa Exemplo', ENT_QUOTES, 'UTF-8') ?></strong>
            <small class="text-secondary">Administrador</small>
          </span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item" href="configuracoes"><i class="bi bi-gear me-2"></i> Configurações</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../logout"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
        </ul>
      </div>
    </header>


    <!-- Conteudo da pagina -->
    <main class="flex-grow-1 p-3 p-md-4">

      <div class="mb-4">
        <p class="small text-secondary text-uppercase mb-1">Área da empresa</p>
        <h1 class="fw-bold">Configurações</h1>
        <p class="text-secondary mb-0">Atualize os dados da empresa, as notificações e a sua senha.</p>
      </div>

      <div class="row g-3">

        <!-- ========== DADOS DA EMPRESA ========== -->
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-body">
              <h2 class="h5 fw-bold mb-3"><i class="bi bi-building text-primary me-2"></i> Dados da empresa</h2>

              <form class="needs-validation" action="configuracoes-salvar" method="post" enctype="multipart/form-data" data-backend="true" data-mensagem="Dados da empresa atualizados!" novalidate>
                <?= $campoCsrf ?? '' ?>
                <div class="row g-3">
                  <div class="col-md-7">
                    <label for="conf-nome" class="form-label fw-semibold small">Nome da empresa *</label>
                    <input type="text" class="form-control" id="conf-nome" name="nome" value="<?= htmlspecialchars($empresa['nome'] ?? 'Empresa Exemplo', ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
                    <div class="invalid-feedback">Informe o nome da empresa.</div>
                  </div>
                  <div class="col-md-5">
                    <label for="conf-cnpj" class="form-label fw-semibold small">CNPJ *</label>
                    <input type="text" class="form-control mascara-cnpj" id="conf-cnpj" name="cnpj" value="<?= htmlspecialchars($empresa['cnpj'] ?? '98.765.432/0001-10', ENT_QUOTES, 'UTF-8') ?>" pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}" required>
                    <div class="invalid-feedback">Digite um CNPJ completo.</div>
                  </div>
                  <div class="col-md-7">
                    <label for="conf-email" class="form-label fw-semibold small">E-mail de contato *</label>
                    <input type="email" class="form-control" id="conf-email" name="email" value="<?= htmlspecialchars($empresa['email'] ?? 'contato@empresaexemplo.com', ENT_QUOTES, 'UTF-8') ?>" maxlength="120" required>
                    <div class="invalid-feedback">Digite um e-mail válido.</div>
                  </div>
                  <div class="col-md-5">
                    <label for="conf-telefone" class="form-label fw-semibold small">Telefone</label>
                    <input type="tel" class="form-control mascara-telefone" id="conf-telefone" name="telefone" value="<?= htmlspecialchars($empresa['telefone'] ?? '(11) 3000-1234', ENT_QUOTES, 'UTF-8') ?>" pattern="\(\d{2}\) \d{4,5}-\d{4}">
                    <div class="invalid-feedback">Use o formato (11) 90000-0000.</div>
                  </div>

                  <div class="col-12">
                    <label for="conf-logo" class="form-label fw-semibold small">Logotipo da empresa</label>
                    <input type="file" class="form-control campo-arquivo" id="conf-logo" name="logo" accept="image/png, image/jpeg" data-extensoes="png,jpg,jpeg" data-max-mb="2">
                    <div class="form-text">PNG ou JPG, até 2 MB.</div>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-check-lg me-1"></i> Salvar alterações</button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-5">

          <!-- ========== NOTIFICACOES ========== -->
          <div class="card mb-3">
            <div class="card-body">
              <h2 class="h5 fw-bold mb-3"><i class="bi bi-bell text-primary me-2"></i> Notificações</h2>

              <form class="needs-validation" action="notificacoes-salvar" method="post" data-backend="true" data-mensagem="Preferências salvas!" novalidate>
                <?= $campoCsrf ?? '' ?>
                <div class="form-check form-switch mb-2">
                  <input class="form-check-input" type="checkbox" role="switch" id="notif-solicitacao" name="notif_solicitacao" <?= !isset($empresa['notif_solicitacao']) || $empresa['notif_solicitacao'] ? 'checked' : '' ?>>
                  <label class="form-check-label" for="notif-solicitacao">Avisar quando chegar uma nova solicitação</label>
                </div>
                <div class="form-check form-switch mb-2">
                  <input class="form-check-input" type="checkbox" role="switch" id="notif-aprovacao" name="notif_aprovacao" <?= !isset($empresa['notif_aprovacao']) || $empresa['notif_aprovacao'] ? 'checked' : '' ?>>
                  <label class="form-check-label" for="notif-aprovacao">Avisar quando um orçamento for aprovado</label>
                </div>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" role="switch" id="notif-email" name="notif_email" <?= !empty($empresa['notif_email']) ? 'checked' : '' ?>>
                  <label class="form-check-label" for="notif-email">Receber um resumo semanal por e-mail</label>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm">Salvar preferências</button>
              </form>
            </div>
          </div>

          <!-- ========== ALTERAR SENHA ========== -->
          <div class="card">
            <div class="card-body">
              <h2 class="h5 fw-bold mb-3"><i class="bi bi-shield-lock text-primary me-2"></i> Alterar senha</h2>

              <form class="needs-validation" action="senha-alterar" method="post" data-backend="true" data-mensagem="Senha alterada com sucesso!" novalidate>
                <?= $campoCsrf ?? '' ?>
                <div class="mb-3">
                  <label for="senha-atual" class="form-label fw-semibold small">Senha atual</label>
                  <input type="password" class="form-control" id="senha-atual" name="senha_atual" autocomplete="current-password" required>
                  <div class="invalid-feedback">Digite a senha atual.</div>
                </div>
                <div class="mb-3">
                  <label for="senha-nova" class="form-label fw-semibold small">Nova senha</label>
                  <input type="password" class="form-control" id="senha-nova" name="senha_nova" minlength="8" autocomplete="new-password" required>
                  <div class="invalid-feedback">Mínimo de 8 caracteres.</div>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-sm">Alterar senha</button>
              </form>
            </div>
          </div>

        </div>
      </div>

      <!-- Modal: Como Funciona o Processo -->
      <?php include dirname(__DIR__) . '/comum/modal_como_funciona.php'; ?>

    </main>


    <!-- Rodape -->
    <footer class="bg-white border-top px-3 px-md-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-secondary">
      <span><i class="bi bi-laptop text-primary me-1"></i> <strong>iiKliente</strong> Gestão de Clientes</span>
      <span>
        <a href="#" class="text-secondary text-decoration-none me-3" data-toast="Suporte: vitorh.pedrini@gmail.com">Suporte</a>
        <a href="#" class="text-secondary text-decoration-none me-3" data-toast="Página de exemplo">Termos de Uso</a>
        <a href="#" class="text-secondary text-decoration-none" data-toast="Página de exemplo">Política de Privacidade</a>
      </span>
      <span>© 2026 iiKliente. Desenvolvido por<br><a href="https://capsulum.com.br/vitor-pedrini" target="_blank" rel="noopener noreferrer" class="text-secondary">Vitor Hugo Dos Santos Pedrini</a>.</span>
    </footer>

  </div>
</div>

<!-- --- Bloco: Scripts com Fallback Local e Arquivo Minificado --- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>if (typeof bootstrap === 'undefined') { document.write('<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="../assets/js/main.min.js?v=2"></script>
</body>
</html>
