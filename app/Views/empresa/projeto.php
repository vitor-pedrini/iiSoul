<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($projeto['nome'], ENT_QUOTES, 'UTF-8') ?> - Ficha do Projeto - iiKliente</title>

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
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="projetos"><i class="bi bi-folder-fill me-2"></i> Projetos</a></li>
        <li class="nav-item"><a class="nav-link" href="solicitacoes"><i class="bi bi-file-earmark-text-fill me-2"></i> Solicitações / Orçamentos</a></li>
        <li class="nav-item"><a class="nav-link" href="configuracoes"><i class="bi bi-gear-fill me-2"></i> Configurações</a></li>
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

      <form class="needs-validation flex-grow-1" role="search" action="projetos" method="get">
        <div class="input-group">
          <button class="btn btn-outline-secondary" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
          <input type="search" class="form-control" name="q" placeholder="Buscar projetos por nome, código ou cliente..." aria-label="Buscar">
        </div>
      </form>

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
            <strong class="d-block small"><?= htmlspecialchars($usuarioAtual['nome'] ?? 'Empresa Exemplo', ENT_QUOTES, 'UTF-8') ?></strong>
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


    <!-- Conteudo Principal -->
    <main class="flex-grow-1 p-3 p-md-4 overflow-y-auto">

      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
          <li class="breadcrumb-item"><a href="dashboard" class="text-decoration-none">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="projetos" class="text-decoration-none">Projetos</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($projeto['codigo'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($projeto['nome'], ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
      </nav>

      <?php
        $progAtual = (int) ($projeto['progresso'] ?? 20);
        $corProgresso = match (true) {
            $progAtual >= 100 => 'bg-success',
            $progAtual >= 60  => 'bg-primary',
            $progAtual >= 40  => 'bg-info',
            default           => 'bg-warning',
        };
        $temAjuste = !empty($projeto['ultimo_ajuste']);
      ?>

      <!-- Card Topo do Projeto -->
      <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
          <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-light text-secondary border font-monospace fs-6"><?= htmlspecialchars($projeto['codigo'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="badge bg-primary-subtle text-primary-emphasis fs-6"><?= htmlspecialchars($projeto['tipo'] ?? 'Projeto Web', ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($temAjuste): ?>
                  <span class="badge bg-danger-subtle text-danger-emphasis fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> Ajuste Solicitado</span>
                <?php elseif ($progAtual >= 100): ?>
                  <span class="badge bg-success-subtle text-success-emphasis fs-6"><i class="bi bi-check-circle-fill me-1"></i> Concluído</span>
                <?php endif; ?>
              </div>
              <h1 class="h3 fw-bold mb-1"><?= htmlspecialchars($projeto['nome'], ENT_QUOTES, 'UTF-8') ?></h1>
              <p class="text-secondary small mb-3"><?= htmlspecialchars($projeto['descricao'] ?? 'Sem descrição cadastrada.', ENT_QUOTES, 'UTF-8') ?></p>

              <!-- Dados do Cliente Vinculado -->
              <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small">
                <span><i class="bi bi-person-fill text-primary me-1"></i> Cliente: <a href="cliente?id=<?= (int) $projeto['cliente_id'] ?>" class="fw-semibold text-decoration-none"><?= htmlspecialchars($projeto['cliente_nome'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?></a></span>
                <span><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($projeto['cliente_email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                <span><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($projeto['cliente_telefone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>

            <!-- Dados Comerciais e Acoes -->
            <div class="text-md-end">
              <div class="mb-3">
                <small class="text-secondary d-block">Valor do Orçamento</small>
                <strong class="fs-4 text-success">R$ <?= number_format((float) ($projeto['valor_orcamento'] ?? 0), 2, ',', '.') ?></strong>
              </div>
              <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarProjeto">
                  <i class="bi bi-pencil-square me-1"></i> Editar Informações
                </button>
                <a href="cliente?id=<?= (int) $projeto['cliente_id'] ?>" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-person-lines-fill me-1"></i> Ver Ficha do Cliente
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alerta de Ajuste Solicitado pelo Cliente (se houver) -->
      <?php if ($temAjuste): ?>
        <div class="card border-danger shadow-sm mb-4">
          <div class="card-body p-3 bg-danger-subtle rounded">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
              <div class="d-flex align-items-start gap-3">
                <span class="avatar rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                  <i class="bi bi-exclamation-triangle fs-5"></i>
                </span>
                <div>
                  <h2 class="h6 fw-bold text-danger-emphasis mb-1">O cliente solicitou um ajuste na etapa atual</h2>
                  <p class="small text-danger-emphasis mb-0 fst-italic">"<?= htmlspecialchars($projeto['ultimo_ajuste'], ENT_QUOTES, 'UTF-8') ?>"</p>
                  <small class="text-secondary d-block mt-1">Revise as telas/arquivos com a equipe, envie a nova versão ao cliente abaixo e marque o ajuste como atendido.</small>
                </div>
              </div>
              <form action="projeto-resolver-ajuste" method="post" class="d-inline">
                <?= $campoCsrf ?? '' ?>
                <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm text-nowrap">
                  <i class="bi bi-check2-circle me-1"></i> Marcar ajuste como atendido
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- ================= LINHA DO TEMPO E CONTROLE DE ETAPAS ================= -->
      <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
              <h2 class="h5 fw-bold mb-1"><i class="bi bi-signpost-split text-primary me-2"></i> Estágio do Projeto (Ciclo em 5 Etapas)</h2>
              <small class="text-secondary">Etapa atual do cliente: <strong><?= htmlspecialchars($projeto['etapa_atual'], ENT_QUOTES, 'UTF-8') ?></strong> (<?= $progAtual ?>% concluído)</small>
            </div>
            <span class="badge <?= $corProgresso ?> fs-6 px-3 py-2"><?= $progAtual ?>% Concluído</span>
          </div>

          <!-- Barra de Progresso Geral -->
          <div class="progress mb-4 barra-progresso-fina" role="progressbar" aria-valuenow="<?= $progAtual ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar <?= $corProgresso ?> progresso-<?= $progAtual ?>"></div>
          </div>

          <!-- 5 Etapas Visuais -->
          <div class="row text-center g-2 mb-4">
            <!-- Etapa 1 -->
            <div class="col">
              <?php if ($progAtual >= 20): ?>
                <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-check-lg"></i></span>
                <strong class="d-block small">1. Requisitos</strong>
                <small class="text-secondary">20% · <?= ($progAtual > 20) ? 'Concluído' : 'Em andamento' ?></small>
              <?php else: ?>
                <span class="avatar rounded-circle bg-light text-secondary border d-inline-flex align-items-center justify-content-center mb-1">1</span>
                <strong class="d-block small text-secondary">1. Requisitos</strong>
                <small class="text-secondary">20%</small>
              <?php endif; ?>
            </div>

            <!-- Etapa 2 -->
            <div class="col">
              <?php if ($progAtual > 40): ?>
                <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-check-lg"></i></span>
                <strong class="d-block small">2. Wireframes</strong>
                <small class="text-secondary">40% · Concluído</small>
              <?php elseif ($progAtual === 40): ?>
                <span class="avatar rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-palette"></i></span>
                <strong class="d-block small text-primary">2. Wireframes</strong>
                <small class="text-primary fw-semibold">40% · Em andamento</small>
              <?php else: ?>
                <span class="avatar rounded-circle bg-light text-secondary border d-inline-flex align-items-center justify-content-center mb-1">2</span>
                <strong class="d-block small text-secondary">2. Wireframes</strong>
                <small class="text-secondary">40%</small>
              <?php endif; ?>
            </div>

            <!-- Etapa 3 -->
            <div class="col">
              <?php if ($progAtual > 60): ?>
                <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-check-lg"></i></span>
                <strong class="d-block small">3. Desenvolvimento</strong>
                <small class="text-secondary">60% · Concluído</small>
              <?php elseif ($progAtual === 60): ?>
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-code-slash"></i></span>
                <strong class="d-block small text-primary">3. Desenvolvimento</strong>
                <small class="text-primary fw-semibold">60% · Em andamento</small>
              <?php else: ?>
                <span class="avatar rounded-circle bg-light text-secondary border d-inline-flex align-items-center justify-content-center mb-1">3</span>
                <strong class="d-block small text-secondary">3. Desenvolvimento</strong>
                <small class="text-secondary">60%</small>
              <?php endif; ?>
            </div>

            <!-- Etapa 4 -->
            <div class="col">
              <?php if ($progAtual > 80): ?>
                <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-check-lg"></i></span>
                <strong class="d-block small">4. Testes</strong>
                <small class="text-secondary">80% · Concluído</small>
              <?php elseif ($progAtual === 80): ?>
                <span class="avatar rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-check2-square"></i></span>
                <strong class="d-block small text-warning-emphasis">4. Testes</strong>
                <small class="text-warning-emphasis fw-semibold">80% · Em andamento</small>
              <?php else: ?>
                <span class="avatar rounded-circle bg-light text-secondary border d-inline-flex align-items-center justify-content-center mb-1">4</span>
                <strong class="d-block small text-secondary">4. Testes</strong>
                <small class="text-secondary">80%</small>
              <?php endif; ?>
            </div>

            <!-- Etapa 5 -->
            <div class="col">
              <?php if ($progAtual >= 100): ?>
                <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1"><i class="bi bi-check-circle-fill"></i></span>
                <strong class="d-block small text-success">5. Entrega Final</strong>
                <small class="text-success fw-semibold">100% · Concluído</small>
              <?php else: ?>
                <span class="avatar rounded-circle bg-light text-secondary border d-inline-flex align-items-center justify-content-center mb-1">5</span>
                <strong class="d-block small text-secondary">5. Entrega Final</strong>
                <small class="text-secondary">100%</small>
              <?php endif; ?>
            </div>
          </div>

          <!-- Formulario de Mudanca de Etapa -->
          <div class="bg-body-tertiary p-3 rounded border">
            <form action="projeto-etapa" method="post" class="row g-2 align-items-center">
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
              <input type="hidden" name="cliente_id" value="<?= (int) $projeto['cliente_id'] ?>">
              <input type="hidden" name="origem" value="projeto">

              <div class="col-md-3">
                <label for="select-etapa" class="form-label small fw-semibold mb-0">Alterar etapa manualmente:</label>
              </div>
              <div class="col-md-5">
                <select name="progresso" id="select-etapa" class="form-select form-select-sm">
                  <option value="20" <?= ($progAtual === 20) ? 'selected' : '' ?>>20% — 1. Levantamento de requisitos</option>
                  <option value="40" <?= ($progAtual === 40) ? 'selected' : '' ?>>40% — 2. Wireframes e protótipo</option>
                  <option value="60" <?= ($progAtual === 60) ? 'selected' : '' ?>>60% — 3. Em desenvolvimento</option>
                  <option value="80" <?= ($progAtual === 80) ? 'selected' : '' ?>>80% — 4. Testes e validação</option>
                  <option value="100" <?= ($progAtual >= 100) ? 'selected' : '' ?>>100% — 5. Projeto concluído e entregue</option>
                </select>
              </div>
              <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-arrow-repeat me-1"></i> Atualizar Etapa</button>
                <?php if ($progAtual === 20): ?>
                  <button type="submit" name="progresso" value="40" class="btn btn-outline-info btn-sm text-nowrap"><i class="bi bi-arrow-right me-1"></i> Ir para 40%</button>
                <?php elseif ($progAtual === 40): ?>
                  <button type="submit" name="progresso" value="60" class="btn btn-outline-primary btn-sm text-nowrap"><i class="bi bi-arrow-right me-1"></i> Ir para 60%</button>
                <?php elseif ($progAtual === 60): ?>
                  <button type="submit" name="progresso" value="80" class="btn btn-outline-warning btn-sm text-nowrap"><i class="bi bi-arrow-right me-1"></i> Ir para 80%</button>
                <?php elseif ($progAtual === 80): ?>
                  <button type="submit" name="progresso" value="100" class="btn btn-outline-success btn-sm text-nowrap"><i class="bi bi-check-lg me-1"></i> Finalizar (100%)</button>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ================= FERRAMENTAS ESPECÍFICAS DA ETAPA ATUAL DA EMPRESA (CICLO EM 5 ETAPAS) ================= -->
      <?php if ($progAtual === 20): ?>
        <!-- FERRAMENTAS DA ETAPA 1 (20%): REQUISITOS E MATERIAIS -->
        <div class="card shadow-sm border-primary mb-4" id="empresa-etapa-20">
          <div class="card-header bg-primary-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-primary-emphasis"><i class="bi bi-clipboard-check text-primary me-2"></i> Ferramentas da Etapa 1: Levantamento de Requisitos e Conferência</h2>
              <small class="text-secondary">Valide os materiais recebidos do cliente para liberar a equipe de design para os wireframes.</small>
            </div>
            <span class="badge bg-primary text-white">Etapa 1 de 5 · Requisitos</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <!-- Checklist da Equipe -->
              <div class="col-lg-7">
                <h3 class="h6 fw-bold mb-2">Checklist da Equipe: Materiais e Escopo</h3>
                <p class="small text-secondary mb-3">Marque os materiais já recebidos e validados pela equipe interna antes de avançar para os wireframes.</p>

                <form action="checklist-requisitos" method="post">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <div class="list-group mb-3">
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="logo" <?= in_array('logo', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Logotipo em Alta Resolução / Vetor</strong>
                        <span class="text-secondary">Arquivo em PNG transparente, SVG, AI ou PDF vetorial recebido.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="identidade" <?= in_array('identidade', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Identidade Visual e Referências de Estilo</strong>
                        <span class="text-secondary">Manual de marca, paleta de cores ou sites de inspiração alinhados.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="textos" <?= in_array('textos', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Textos Institucionais e Conteúdos</strong>
                        <span class="text-secondary">Quem somos, catálogo de serviços/produtos e contatos entregues.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="fotos" <?= in_array('fotos', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Fotos e Acervo Visual do Cliente</strong>
                        <span class="text-secondary">Fotos de boa resolução da empresa ou banco de imagens definido.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="escopo" <?= in_array('escopo', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Escopo e Regras de Negócio Mapeados</strong>
                        <span class="text-secondary">Módulos, permissões de usuário e integrações confirmadas.</span>
                      </span>
                    </label>
                  </div>
                  <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-floppy me-1"></i> Salvar Checklist da Equipe</button>
                </form>
              </div>

              <!-- Status e Acoes Rapidas -->
              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-person-check text-primary me-1"></i> Status do Envio pelo Cliente</h3>
                    <p class="small text-secondary mb-3">O cliente possui um checklist correspondente no painel dele para acompanhar o envio de materiais.</p>
                    <div class="p-3 bg-white rounded border mb-3">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-secondary">Arquivos trocados:</span>
                        <strong class="small"><?= count($arquivos ?? []) ?> arquivo(s)</strong>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-secondary">Checklist cliente:</span>
                        <span class="badge bg-primary-subtle text-primary-emphasis"><?= count($checklistReq) ?> / 5 conferidos</span>
                      </div>
                    </div>
                  </div>

                  <form action="projeto-etapa" method="post">
                    <?= $campoCsrf ?? '' ?>
                    <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                    <input type="hidden" name="cliente_id" value="<?= (int) $projeto['cliente_id'] ?>">
                    <input type="hidden" name="origem" value="projeto">
                    <input type="hidden" name="progresso" value="40">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                      <i class="bi bi-arrow-right-circle me-1"></i> Materiais Ok → Avançar para 40% (Wireframes)
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progAtual === 40): ?>
        <!-- FERRAMENTAS DA ETAPA 2 (40%): WIREFRAMES E PROTÓTIPO FIGMA -->
        <div class="card shadow-sm border-info mb-4" id="empresa-etapa-40">
          <div class="card-header bg-info-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-info-emphasis"><i class="bi bi-palette text-info me-2"></i> Ferramentas da Etapa 2: Wireframes e Protótipo (Figma)</h2>
              <small class="text-secondary">Cadastre a URL do protótipo no Figma e envie mockups das telas para aprovação do cliente.</small>
            </div>
            <span class="badge bg-info text-white">Etapa 2 de 5 · Wireframes</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <!-- Gerenciamento do Figma -->
              <div class="col-lg-6">
                <h3 class="h6 fw-bold mb-2">Link do Protótipo Interativo</h3>
                <p class="small text-secondary mb-3">Assim que cadastrado, o cliente verá um botão em destaque na área dele para navegar e validar as telas.</p>

                <?php if (!empty($projeto['link_prototipo'])): ?>
                  <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-3">
                    <div class="lh-sm text-truncate me-2">
                      <strong class="d-block small"><i class="bi bi-check-circle-fill me-1"></i> Link ativo:</strong>
                      <span class="small font-monospace text-truncate d-inline-block text-secondary"><?= htmlspecialchars($projeto['link_prototipo'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <a href="<?= htmlspecialchars($projeto['link_prototipo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm text-nowrap">
                      <i class="bi bi-box-arrow-up-right me-1"></i> Abrir
                    </a>
                  </div>
                <?php endif; ?>

                <form action="projeto-prototipo" method="post" class="mb-3">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <div class="mb-2">
                    <label for="link-figma-etapa" class="form-label small fw-semibold">URL do Figma / Protótipo</label>
                    <input type="url" name="link_prototipo" id="link-figma-etapa" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['link_prototipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://www.figma.com/proto/... ou https://..." required>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm flex-grow-1"><i class="bi bi-save me-1"></i> Salvar Protótipo</button>
                    <?php if (!empty($projeto['link_prototipo'])): ?>
                      <button type="submit" name="link_prototipo" value="" class="btn btn-outline-danger btn-sm" title="Remover link"><i class="bi bi-trash"></i></button>
                    <?php endif; ?>
                  </div>
                </form>
              </div>

              <!-- Envio e Avanco para Desenvolvimento -->
              <div class="col-lg-6">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-shield-check text-info me-1"></i> Aprovação e Ajustes</h3>
                    <p class="small text-secondary mb-3">O cliente pode aprovar os wireframes ou solicitar ajustes pelo painel. Acompanhe abaixo o status:</p>
                    <div class="p-3 bg-white rounded border mb-3">
                      <?php if (!empty($projeto['ultimo_ajuste'])): ?>
                        <div class="text-danger small mb-2">
                          <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Ajuste Solicitado pelo Cliente:</strong>
                          <div class="fst-italic mt-1">"<?= htmlspecialchars($projeto['ultimo_ajuste'], ENT_QUOTES, 'UTF-8') ?>"</div>
                        </div>
                      <?php else: ?>
                        <div class="text-success small mb-1">
                          <i class="bi bi-check-circle me-1"></i> Nenhum ajuste pendente. Aguardando validação do cliente.
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <form action="projeto-etapa" method="post">
                    <?= $campoCsrf ?? '' ?>
                    <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                    <input type="hidden" name="cliente_id" value="<?= (int) $projeto['cliente_id'] ?>">
                    <input type="hidden" name="origem" value="projeto">
                    <input type="hidden" name="progresso" value="60">
                    <button type="submit" class="btn btn-info btn-sm w-100">
                      <i class="bi bi-arrow-right-circle me-1"></i> Wireframes Aprovados → Avançar para 60% (Desenvolvimento)
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progAtual === 60): ?>
        <!-- FERRAMENTAS DA ETAPA 3 (60%): DESENVOLVIMENTO TÉCNICO -->
        <div class="card shadow-sm border-primary mb-4" id="empresa-etapa-60">
          <div class="card-header bg-primary-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-primary-emphasis"><i class="bi bi-code-slash text-primary me-2"></i> Ferramentas da Etapa 3: Desenvolvimento Técnico e Pré-Homologação</h2>
              <small class="text-secondary">Acompanhe a codificação dos módulos e configure o ambiente de testes (Staging).</small>
            </div>
            <span class="badge bg-primary text-white">Etapa 3 de 5 · Desenvolvimento</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <!-- Configuracao do Ambiente de Testes (Staging) -->
              <div class="col-lg-7">
                <h3 class="h6 fw-bold mb-2">Ambiente de Testes / Homologação (Staging)</h3>
                <p class="small text-secondary mb-3">Informe a URL do servidor de testes e as credenciais provisórias para que o cliente possa acompanhar.</p>

                <form action="projeto-links" method="post">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <input type="hidden" name="link_prototipo" value="<?= htmlspecialchars($projeto['link_prototipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="link_producao" value="<?= htmlspecialchars($projeto['link_producao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                  <div class="mb-3">
                    <label for="link-homolog-etapa" class="form-label small fw-semibold">URL de Homologação (Staging)</label>
                    <input type="url" name="link_homologacao" id="link-homolog-etapa" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['link_homologacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://staging.suamarca.com/cliente ou http://localhost/...">
                  </div>

                  <div class="mb-3">
                    <label for="cred-teste-etapa" class="form-label small fw-semibold">Credenciais de Testes (Acesso do Cliente)</label>
                    <input type="text" name="credenciais_teste" id="cred-teste-etapa" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['credenciais_teste'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Usuário: teste@cliente.com / Senha: 123">
                  </div>

                  <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-save me-1"></i> Salvar Acessos de Staging</button>
                </form>
              </div>

              <!-- Módulos e Avanco -->
              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-hdd-stack text-primary me-1"></i> Módulos Programados</h3>
                    <div class="p-2 bg-white rounded border small mb-3">
                      <div class="d-flex justify-content-between mb-1"><span>Frontend & Telas:</span><strong class="text-success">85%</strong></div>
                      <div class="d-flex justify-content-between mb-1"><span>Backend & PHP:</span><strong class="text-primary">70%</strong></div>
                      <div class="d-flex justify-content-between mb-1"><span>Banco de Dados:</span><strong class="text-success">100%</strong></div>
                      <div class="d-flex justify-content-between"><span>Integrações (APIs):</span><strong class="text-warning">60%</strong></div>
                    </div>
                  </div>

                  <form action="projeto-etapa" method="post">
                    <?= $campoCsrf ?? '' ?>
                    <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                    <input type="hidden" name="cliente_id" value="<?= (int) $projeto['cliente_id'] ?>">
                    <input type="hidden" name="origem" value="projeto">
                    <input type="hidden" name="progresso" value="80">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                      <i class="bi bi-arrow-right-circle me-1"></i> Código Concluído → Liberar para Testes (Ir para 80%)
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progAtual === 80): ?>
        <!-- FERRAMENTAS DA ETAPA 4 (80%): HOMOLOGAÇÃO E TESTES FINAIS -->
        <div class="card shadow-sm border-warning mb-4" id="empresa-etapa-80">
          <div class="card-header bg-warning-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-warning-emphasis"><i class="bi bi-check2-circle text-warning me-2"></i> Ferramentas da Etapa 4: Homologação e Testes Finais</h2>
              <small class="text-secondary">Monitore a validação prática do cliente e execute o checklist de pré-lançamento.</small>
            </div>
            <span class="badge bg-warning text-dark">Etapa 4 de 5 · Testes</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <!-- Dados de Homologacao e Testes do Cliente -->
              <div class="col-lg-6">
                <h3 class="h6 fw-bold mb-2">Servidor de Testes & Credenciais</h3>
                <form action="projeto-links" method="post" class="mb-3">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <input type="hidden" name="link_prototipo" value="<?= htmlspecialchars($projeto['link_prototipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="link_producao" value="<?= htmlspecialchars($projeto['link_producao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                  <div class="mb-2">
                    <label for="link-homolog-etapa4" class="form-label small fw-semibold">URL de Homologação *</label>
                    <input type="url" name="link_homologacao" id="link-homolog-etapa4" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['link_homologacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://staging..." required>
                  </div>
                  <div class="mb-2">
                    <label for="cred-teste-etapa4" class="form-label small fw-semibold">Credenciais de Testes</label>
                    <input type="text" name="credenciais_teste" id="cred-teste-etapa4" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['credenciais_teste'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Usuário: teste@cliente.com / Senha: 123">
                  </div>
                  <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-save me-1"></i> Atualizar Dados de Homologação</button>
                </form>

                <div class="p-3 bg-light rounded border">
                  <strong class="small d-block mb-1"><i class="bi bi-clipboard-check text-primary me-1"></i> Acompanhamento do Cliente:</strong>
                  <span class="small text-secondary d-block mb-2">Itens validados pelo cliente no painel dele:</span>
                  <span class="badge bg-warning-subtle text-warning-emphasis fs-6"><?= count($checklistHomolog) ?> de 5 testes concluídos</span>
                </div>
              </div>

              <!-- Checklist Interno de Pré-Lançamento -->
              <div class="col-lg-6">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-patch-check text-warning me-1"></i> Checklist da Equipe: Pré-Lançamento</h3>
                    <p class="small text-secondary mb-3">Conferências técnicas antes de apontar o domínio final para produção.</p>

                    <form action="checklist-homologacao" method="post" class="mb-3">
                      <?= $campoCsrf ?? '' ?>
                      <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                      <div class="list-group mb-3">
                        <label class="list-group-item d-flex align-items-center gap-2 py-1">
                          <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="ssl" <?= in_array('ssl', $checklistHomolog, true) ? 'checked' : '' ?>>
                          <span class="small">Certificado SSL ativo e HTTPS forçado</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2 py-1">
                          <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="responsivo" <?= in_array('responsivo', $checklistHomolog, true) ? 'checked' : '' ?>>
                          <span class="small">Responsividade mobile e cross-browser testada</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2 py-1">
                          <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="emails" <?= in_array('emails', $checklistHomolog, true) ? 'checked' : '' ?>>
                          <span class="small">Disparos de formulários e e-mails validados</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2 py-1">
                          <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="backup" <?= in_array('backup', $checklistHomolog, true) ? 'checked' : '' ?>>
                          <span class="small">Backup inicial e banco de dados otimizado</span>
                        </label>
                        <label class="list-group-item d-flex align-items-center gap-2 py-1">
                          <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="seo" <?= in_array('seo', $checklistHomolog, true) ? 'checked' : '' ?>>
                          <span class="small">Meta tags, favicon e SEO básico configurados</span>
                        </label>
                      </div>
                      <button type="submit" class="btn btn-outline-warning btn-sm"><i class="bi bi-floppy me-1"></i> Salvar Checklist da Equipe</button>
                    </form>
                  </div>

                  <form action="projeto-etapa" method="post">
                    <?= $campoCsrf ?? '' ?>
                    <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                    <input type="hidden" name="cliente_id" value="<?= (int) $projeto['cliente_id'] ?>">
                    <input type="hidden" name="origem" value="projeto">
                    <input type="hidden" name="progresso" value="100">
                    <button type="submit" class="btn btn-success btn-sm w-100">
                      <i class="bi bi-check-circle-fill me-1"></i> Testes Finalizados → Publicar e Concluir Projeto (100%)
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progAtual >= 100): ?>
        <!-- FERRAMENTAS DA ETAPA 5 (100%): PROJETO CONCLUÍDO E AVALIAÇÃO -->
        <div class="card shadow-sm border-success mb-4" id="empresa-etapa-100">
          <div class="card-header bg-success-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-success-emphasis"><i class="bi bi-trophy-fill text-success me-2"></i> Ferramentas da Etapa 5: Entrega Final, Produção e Avaliação</h2>
              <small class="text-secondary">Cadastre o link oficial de produção e acompanhe a satisfação e avaliação do cliente.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <a href="projeto-termo-pdf?id=<?= (int)$projeto['id'] ?>" target="_blank" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> Imprimir / Exportar Termo de Entrega (PDF)
              </a>
              <span class="badge bg-success text-white">Etapa 5 de 5 · Concluído</span>
            </div>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <!-- Link de Producao -->
              <div class="col-lg-6">
                <h3 class="h6 fw-bold mb-2">Link Oficial do Projeto em Produção</h3>
                <p class="small text-secondary mb-3">O cliente verá um botão em destaque para acessar o projeto definitivo no ar.</p>

                <?php if (!empty($projeto['link_producao'])): ?>
                  <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-3">
                    <div class="lh-sm text-truncate me-2">
                      <strong class="d-block small"><i class="bi bi-globe me-1"></i> Projeto no Ar:</strong>
                      <span class="small font-monospace text-truncate d-inline-block text-secondary"><?= htmlspecialchars($projeto['link_producao'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <a href="<?= htmlspecialchars($projeto['link_producao'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm text-nowrap">
                      <i class="bi bi-box-arrow-up-right me-1"></i> Abrir
                    </a>
                  </div>
                <?php endif; ?>

                <form action="projeto-links" method="post" class="mb-3">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <input type="hidden" name="link_prototipo" value="<?= htmlspecialchars($projeto['link_prototipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="link_homologacao" value="<?= htmlspecialchars($projeto['link_homologacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="credenciais_teste" value="<?= htmlspecialchars($projeto['credenciais_teste'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                  <div class="mb-3">
                    <label for="link-prod-etapa5" class="form-label small fw-semibold">URL de Produção (Domínio Oficial) *</label>
                    <input type="url" name="link_producao" id="link-prod-etapa5" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['link_producao'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://www.cliente.com.br" required>
                  </div>
                  <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-save me-1"></i> Salvar Link Oficial</button>
                </form>
              </div>

              <!-- Avaliacao Recebida do Cliente -->
              <div class="col-lg-6">
                <div class="bg-body-tertiary p-3 rounded border h-100">
                  <h3 class="h6 fw-bold mb-2"><i class="bi bi-star-fill text-warning me-1"></i> Avaliação e Depoimento do Cliente</h3>
                  <?php if (!empty($projeto['avaliacao_nota'])): ?>
                    <div class="p-3 bg-white rounded border mb-2">
                      <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fs-4 text-warning">
                          <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi <?= $i <= (int) $projeto['avaliacao_nota'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                          <?php endfor; ?>
                        </span>
                        <span class="badge bg-success-subtle text-success-emphasis">Nota <?= (int) $projeto['avaliacao_nota'] ?> / 5</span>
                      </div>
                      <?php if (!empty($projeto['avaliacao_comentario'])): ?>
                        <p class="small text-secondary fst-italic mb-0">"<?= htmlspecialchars($projeto['avaliacao_comentario'], ENT_QUOTES, 'UTF-8') ?>"</p>
                      <?php endif; ?>
                    </div>
                    <small class="text-secondary d-block"><i class="bi bi-check-circle text-success me-1"></i> Avaliação recebida com sucesso pelo portal do cliente.</small>
                  <?php else: ?>
                    <div class="p-3 bg-white rounded border text-center text-secondary small">
                      <i class="bi bi-clock-history fs-3 d-block mb-1 text-secondary"></i>
                      O cliente ainda não enviou a avaliação de satisfação pelo portal dele.
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- ================= CENTRAL DE ENVIOS PARA O CLIENTE ================= -->
      <div class="row g-3 mb-4">

        <!-- Card 1: Enviar Arquivo / Wireframe / Mockup -->
        <div class="col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
              <h2 class="h6 fw-bold mb-0 text-primary"><i class="bi bi-cloud-arrow-up text-primary me-2"></i> Enviar Mockups e Entregas para o Cliente</h2>
              <small class="text-secondary">Disponibilize telas, wireframes (PDF ou imagens), especificações ou manuais.</small>
            </div>
            <div class="card-body">
              <form action="arquivo-enviar" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <?= $campoCsrf ?? '' ?>
                <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                <input type="hidden" name="cliente_id" value="<?= (int) $projeto['cliente_id'] ?>">
                <input type="hidden" name="origem" value="projeto">

                <div class="mb-3">
                  <label for="arq-upload" class="form-label small fw-semibold">Selecionar Arquivo *</label>
                  <input type="file" name="arquivo" id="arq-upload" class="form-control" accept=".pdf,.png,.jpg,.jpeg,.zip,.docx" required>
                  <div class="form-text small">Aceitos: PDF, PNG, JPG, JPEG, ZIP e DOCX (máx. 10 MB).</div>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-md-6">
                    <label for="arq-categoria" class="form-label small fw-semibold">Categoria da Entrega *</label>
                    <select name="categoria" id="arq-categoria" class="form-select form-select-sm" required>
                      <option value="Wireframes e Telas" <?= ($progAtual === 40) ? 'selected' : '' ?>>Wireframes e Telas (Mockups)</option>
                      <option value="Protótipo Visual">Protótipo Visual</option>
                      <option value="Documentação de Requisitos" <?= ($progAtual === 20) ? 'selected' : '' ?>>Documentação de Requisitos</option>
                      <option value="Entregáveis / Arquivo Final" <?= ($progAtual >= 80) ? 'selected' : '' ?>>Entregáveis / Arquivo Final</option>
                      <option value="Geral">Outros Documentos</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="arq-titulo" class="form-label small fw-semibold">Título / Descrição *</label>
                    <input type="text" name="descricao" id="arq-titulo" class="form-control form-control-sm" placeholder="Ex: Wireframe Home e Checkout v1" required>
                  </div>
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill me-1"></i> Enviar Mockup/Arquivo para o Cliente
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Card 2: Link do Prototipo Interativo (Figma/Canva/URL) -->
        <div class="col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
              <h2 class="h6 fw-bold mb-0 text-primary"><i class="bi bi-link-45deg text-primary me-2"></i> Protótipo Interativo (Figma / Adobe XD / Staging)</h2>
              <small class="text-secondary">Compartilhe um link para o cliente navegar nas telas interativas do projeto.</small>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <?php if (!empty($projeto['link_prototipo'])): ?>
                  <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-3">
                    <div class="lh-sm">
                      <strong class="d-block small mb-1"><i class="bi bi-check-circle-fill me-1"></i> Link de protótipo ativo:</strong>
                      <span class="small font-monospace text-truncate d-inline-block text-secondary"><?= htmlspecialchars($projeto['link_prototipo'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <a href="<?= htmlspecialchars($projeto['link_prototipo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm text-nowrap ms-2">
                      <i class="bi bi-box-arrow-up-right me-1"></i> Abrir
                    </a>
                  </div>
                <?php else: ?>
                  <div class="p-3 bg-light rounded text-secondary small mb-3">
                    <i class="bi bi-info-circle me-1"></i> Nenhum link interativo cadastrado ainda. Se sua equipe construiu o design no Figma, Canva ou ambiente de teste, informe a URL abaixo para que o cliente possa interagir.
                  </div>
                <?php endif; ?>

                <form action="projeto-prototipo" method="post">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <div class="mb-3">
                    <label for="link-proto" class="form-label small fw-semibold">URL do Protótipo ou Telas</label>
                    <input type="url" name="link_prototipo" id="link-proto" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['link_prototipo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://www.figma.com/proto/... ou https://...">
                  </div>
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm flex-grow-1">
                      <i class="bi bi-save me-1"></i> Salvar e Disponibilizar ao Cliente
                    </button>
                    <?php if (!empty($projeto['link_prototipo'])): ?>
                      <button type="submit" name="link_prototipo" value="" class="btn btn-outline-danger btn-sm" title="Remover link">
                        <i class="bi bi-trash"></i>
                      </button>
                    <?php endif; ?>
                  </div>
                </form>
              </div>

              <div class="border-top pt-3 mt-3">
                <small class="text-secondary d-block"><i class="bi bi-eye text-primary me-1"></i> O cliente verá um botão chamativo para abrir o protótipo no painel dele assim que o link for cadastrado.</small>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ================= LISTA COMPLETA DE ARQUIVOS DO PROJETO ================= -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <div>
            <h2 class="h6 fw-bold mb-0"><i class="bi bi-folder2-open text-primary me-2"></i> Materiais e Arquivos Trocados no Projeto (<?= count($arquivos ?? []) ?>)</h2>
            <small class="text-secondary">Visíveis tanto para sua equipe quanto para o cliente no painel dele.</small>
          </div>
          <span class="badge bg-secondary-subtle text-secondary-emphasis"><?= count($arquivos ?? []) ?> arquivos</span>
        </div>
        <div class="card-body p-0">
          <?php if (!empty($arquivos)): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                  <tr>
                    <th class="ps-3">Arquivo / Título</th>
                    <th>Categoria</th>
                    <th>Origem</th>
                    <th>Formato & Tamanho</th>
                    <th>Data</th>
                    <th class="text-end pe-3">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($arquivos as $arq): ?>
                    <?php
                      $ext = strtolower($arq['tipo_arquivo'] ?? 'pdf');
                      $icone = match (true) {
                          in_array($ext, ['png', 'jpg', 'jpeg'], true) => 'bi-file-earmark-image text-primary',
                          $ext === 'pdf' => 'bi-file-earmark-pdf text-danger',
                          $ext === 'zip' => 'bi-file-earmark-zip text-warning',
                          $ext === 'docx' => 'bi-file-earmark-word text-info',
                          default => 'bi-file-earmark-text text-secondary',
                      };
                      $kb = (int) ($arq['tamanho_kb'] ?? 100);
                      $tamFormatado = ($kb >= 1024) ? number_format($kb / 1024, 1, ',', '.') . ' MB' : $kb . ' KB';
                      $ehEmpresa = ($arq['usuario_perfil'] ?? '') === 'empresa';
                    ?>
                    <tr>
                      <td class="ps-3">
                        <div class="d-flex align-items-center">
                          <i class="bi <?= $icone ?> fs-4 me-2"></i>
                          <div>
                            <strong class="d-block"><?= htmlspecialchars($arq['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <small class="text-secondary"><?= htmlspecialchars($arq['nome_original'], ENT_QUOTES, 'UTF-8') ?></small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-light text-secondary border"><?= htmlspecialchars($arq['categoria'] ?? 'Geral', ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td>
                        <?php if ($ehEmpresa): ?>
                          <span class="badge bg-primary-subtle text-primary-emphasis"><i class="bi bi-building me-1"></i> Mockup da Equipe</span>
                        <?php else: ?>
                          <span class="badge bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-person me-1"></i> Material do Cliente</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="text-uppercase fw-semibold"><?= htmlspecialchars($ext, ENT_QUOTES, 'UTF-8') ?></span> · <?= $tamFormatado ?>
                      </td>
                      <td>
                        <small class="text-secondary"><?= date('d/m/Y', strtotime($arq['criado_em'])) ?></small>
                      </td>
                      <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                          <a href="arquivo-baixar?id=<?= (int) $arq['id'] ?>" class="btn btn-outline-secondary" title="Baixar arquivo">
                            <i class="bi bi-download"></i> Baixar
                          </a>
                          <form action="arquivo-excluir" method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?');">
                            <?= $campoCsrf ?? '' ?>
                            <input type="hidden" name="arquivo_id" value="<?= (int) $arq['id'] ?>">
                            <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                            <input type="hidden" name="origem" value="projeto">
                            <button type="submit" class="btn btn-outline-danger" title="Excluir arquivo">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="p-4 text-center text-secondary small">
              <i class="bi bi-folder2 fs-2 d-block mb-1 text-secondary"></i>
              Nenhum arquivo ou mockup anexado neste projeto ainda. Use os campos acima para enviar as primeiras telas ao cliente.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ================= BRIEFING / QUESTIONARIO DO CLIENTE ================= -->
      <?php if (!empty($ultimaSolicitacao)): ?>
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-white py-3">
            <h2 class="h6 fw-bold mb-0 text-primary"><i class="bi bi-journal-check text-primary me-2"></i> Briefing e Respostas do Questionário</h2>
            <small class="text-secondary">Informações enviadas pelo cliente ao solicitar o projeto.</small>
          </div>
          <div class="card-body">
            <div class="row g-3 small">
              <div class="col-md-3">
                <span class="text-secondary d-block">Tipo de Solução</span>
                <strong><?= htmlspecialchars($ultimaSolicitacao['tipo_texto'] ?? $ultimaSolicitacao['tipo'], ENT_QUOTES, 'UTF-8') ?></strong>
              </div>
              <div class="col-md-3">
                <span class="text-secondary d-block">Público-Alvo / Segmento</span>
                <strong><?= htmlspecialchars($ultimaSolicitacao['publico_alvo'] ?? 'Não especificado', ENT_QUOTES, 'UTF-8') ?></strong>
              </div>
              <div class="col-md-3">
                <span class="text-secondary d-block">Faixa de Investimento</span>
                <strong><?= htmlspecialchars($ultimaSolicitacao['faixa'] ?? 'A definir', ENT_QUOTES, 'UTF-8') ?></strong>
              </div>
              <div class="col-md-3">
                <span class="text-secondary d-block">Prazo Estimado</span>
                <strong><?= htmlspecialchars($ultimaSolicitacao['prazo'] ?? 'A combinar', ENT_QUOTES, 'UTF-8') ?></strong>
              </div>
              <div class="col-12">
                <span class="text-secondary d-block mb-1">Módulos Desejados</span>
                <div class="d-flex flex-wrap gap-1">
                  <?php
                    $mods = explode(',', (string) ($ultimaSolicitacao['modulos'] ?? ''));
                    foreach ($mods as $m) {
                        $m = trim($m);
                        if ($m !== '') {
                            echo '<span class="badge bg-light text-secondary border">' . htmlspecialchars(ucfirst(str_replace('-', ' ', $m)), ENT_QUOTES, 'UTF-8') . '</span>';
                        }
                    }
                  ?>
                </div>
              </div>
              <?php if (!empty($ultimaSolicitacao['mensagem'])): ?>
                <div class="col-12">
                  <span class="text-secondary d-block mb-1">Mensagem do Cliente:</span>
                  <div class="p-3 bg-light rounded text-secondary fst-italic">
                    "<?= htmlspecialchars($ultimaSolicitacao['mensagem'], ENT_QUOTES, 'UTF-8') ?>"
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- ================= ITEM 3: GESTÃO FINANCEIRA E PARCELAS (EMPRESA) ================= -->
      <div class="card shadow-sm mb-4" id="financeiro">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h2 class="h6 fw-bold mb-0 text-primary"><i class="bi bi-wallet2 text-primary me-2"></i> Gestão Financeira e Parcelas do Projeto</h2>
            <small class="text-secondary">Defina as parcelas, configure a chave PIX e confirme os recebimentos e comprovantes dos clientes.</small>
          </div>
          <?php
            $stFin = $projeto['status_financeiro'] ?? 'pendente';
            $badgeFinClass = ($stFin === 'pago') ? 'bg-success' : (($stFin === 'parcial') ? 'bg-info text-dark' : 'bg-warning text-dark');
            $stFinTexto = ($stFin === 'pago') ? 'Quitado' : (($stFin === 'parcial') ? 'Parcialmente Pago' : 'Pendente');
          ?>
          <div class="d-flex align-items-center gap-2">
            <span class="badge <?= $badgeFinClass ?> px-3 py-2 fs-6">Status: <?= $stFinTexto ?></span>
            <a href="projeto-termo-pdf?id=<?= (int)$projeto['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-printer me-1"></i> Termo de Entrega (PDF)
            </a>
          </div>
        </div>
        <div class="card-body p-4">
          <!-- Tabela das Parcelas Cadastradas -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 fw-bold mb-0">Parcelas Cadastradas</h3>
            <span class="text-muted small">Valor do Projeto: <strong>R$ <?= number_format((float)($projeto['valor_orcamento'] ?? 0), 2, ',', '.') ?></strong></span>
          </div>

          <div class="table-responsive mb-4">
            <table class="table table-hover align-middle mb-0 small">
              <thead class="table-light">
                <tr class="text-uppercase">
                  <th>#</th>
                  <th>Descrição</th>
                  <th>Valor (R$)</th>
                  <th>Vencimento</th>
                  <th>Data Pagamento</th>
                  <th>Status</th>
                  <th>Comprovante do Cliente</th>
                  <th class="text-end">Ações da Equipe</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($parcelas)): ?>
                  <?php foreach ($parcelas as $p): ?>
                    <tr>
                      <td class="fw-bold"><?= (int)$p['numero_parcela'] ?></td>
                      <td><?= htmlspecialchars($p['descricao']) ?></td>
                      <td class="fw-semibold">R$ <?= number_format((float)$p['valor'], 2, ',', '.') ?></td>
                      <td><?= !empty($p['data_vencimento']) ? date('d/m/Y', strtotime($p['data_vencimento'])) : '-' ?></td>
                      <td><?= !empty($p['data_pagamento']) ? date('d/m/Y', strtotime($p['data_pagamento'])) : '-' ?></td>
                      <td>
                        <?php if ($p['status'] === 'pago'): ?>
                          <span class="badge bg-success-subtle text-success border border-success">
                            <i class="bi bi-check-circle-fill me-1"></i> Quitado
                          </span>
                        <?php elseif ($p['status'] === 'em_analise'): ?>
                          <span class="badge bg-info-subtle text-info border border-info">
                            <i class="bi bi-hourglass-split me-1"></i> Em Análise
                          </span>
                        <?php else: ?>
                          <span class="badge bg-warning-subtle text-warning border border-warning">
                            <i class="bi bi-clock me-1"></i> Pendente
                          </span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (!empty($p['comprovante'])): ?>
                          <a href="<?= $urlBase ?>/storage/uploads/<?= htmlspecialchars($p['comprovante'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i> Ver Comprovante
                          </a>
                        <?php else: ?>
                          <span class="text-muted small">Nenhum</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <form action="parcela-status" method="post" class="d-inline">
                          <?= $campoCsrf ?? '' ?>
                          <input type="hidden" name="parcela_id" value="<?= (int)$p['id'] ?>">
                          <input type="hidden" name="projeto_id" value="<?= (int)$projeto['id'] ?>">
                          <?php if ($p['status'] !== 'pago'): ?>
                            <input type="hidden" name="status" value="pago">
                            <button type="submit" class="btn btn-success btn-sm">
                              <i class="bi bi-check-lg me-1"></i> Confirmar Pagamento
                            </button>
                          <?php else: ?>
                            <input type="hidden" name="status" value="pendente">
                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Reverter para pendente">
                              <i class="bi bi-arrow-counterclockwise"></i> Reverter
                            </button>
                          <?php endif; ?>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                      Nenhuma parcela configurada ainda. Configure o plano financeiro no formulário abaixo.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Formulário de Configuração Financeira e Chave PIX -->
          <div class="p-3 bg-light rounded border">
            <h4 class="h6 fw-bold mb-3"><i class="bi bi-gear me-1"></i> Configurar Chave PIX e Plano de Parcelas</h4>
            <form action="projeto-parcelas" method="post" class="needs-validation" novalidate>
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="projeto_id" value="<?= (int)$projeto['id'] ?>">

              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="pix-tipo" class="form-label small fw-semibold">Tipo da Chave PIX *</label>
                  <select name="tipo_chave_pix" id="pix-tipo" class="form-select form-select-sm" required>
                    <option value="CNPJ" <?= ($projeto['tipo_chave_pix'] ?? '') === 'CNPJ' ? 'selected' : '' ?>>CNPJ</option>
                    <option value="E-mail" <?= ($projeto['tipo_chave_pix'] ?? '') === 'E-mail' ? 'selected' : '' ?>>E-mail</option>
                    <option value="Telefone" <?= ($projeto['tipo_chave_pix'] ?? '') === 'Telefone' ? 'selected' : '' ?>>Telefone</option>
                    <option value="Aleatória" <?= ($projeto['tipo_chave_pix'] ?? '') === 'Aleatória' ? 'selected' : '' ?>>Chave Aleatória (EVP)</option>
                  </select>
                </div>
                <div class="col-md-8">
                  <label for="pix-chave" class="form-label small fw-semibold">Chave PIX da Empresa *</label>
                  <input type="text" name="chave_pix" id="pix-chave" class="form-control form-control-sm font-monospace" value="<?= htmlspecialchars($projeto['chave_pix'] ?? '') ?>" placeholder="Ex: 00.000.000/0001-00 ou pix@suamarca.com" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold d-block">Configurar Parcelas (Até 4 parcelas):</label>
                <div class="row g-2 mb-2">
                  <div class="col-md-4"><label class="small text-muted">Descrição</label></div>
                  <div class="col-md-4"><label class="small text-muted">Valor (R$)</label></div>
                  <div class="col-md-4"><label class="small text-muted">Data Vencimento</label></div>
                </div>

                <?php
                  $pExistentes = !empty($parcelas) ? $parcelas : [
                      ['numero_parcela' => 1, 'descricao' => 'Entrada / Início do Projeto', 'valor' => ((float)($projeto['valor_orcamento'] ?? 0) / 2), 'data_vencimento' => date('Y-m-d', strtotime('+7 days'))],
                      ['numero_parcela' => 2, 'descricao' => 'Entrega Final e Publicação', 'valor' => ((float)($projeto['valor_orcamento'] ?? 0) / 2), 'data_vencimento' => date('Y-m-d', strtotime('+30 days'))],
                  ];
                ?>

                <?php for ($i = 0; $i < 4; $i++): ?>
                  <?php $pItem = $pExistentes[$i] ?? null; ?>
                  <div class="row g-2 mb-2">
                    <div class="col-md-4">
                      <input type="text" name="parcela_descricao[]" class="form-control form-control-sm" placeholder="Ex: Entrada 50%" value="<?= htmlspecialchars($pItem['descricao'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                      <input type="number" step="0.01" name="parcela_valor[]" class="form-control form-control-sm" placeholder="0.00" value="<?= !empty($pItem['valor']) ? htmlspecialchars((string)$pItem['valor']) : '' ?>">
                    </div>
                    <div class="col-md-4">
                      <input type="date" name="parcela_vencimento[]" class="form-control form-control-sm" value="<?= htmlspecialchars($pItem['data_vencimento'] ?? '') ?>">
                    </div>
                  </div>
                <?php endfor; ?>
                <div class="form-text small">Preencha apenas as parcelas necessárias (deixe em branco as linhas extras). Parcelas já quitadas não serão apagadas.</div>
              </div>

              <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-save me-1"></i> Salvar Chave PIX e Gerar Parcelas
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- ================= ITEM 1: HISTÓRICO DE AUDITORIA E LINHA DO TEMPO (EMPRESA) ================= -->
      <div class="card shadow-sm mb-4" id="historico">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <div>
            <h2 class="h6 fw-bold mb-0 text-primary"><i class="bi bi-clock-history text-primary me-2"></i> Histórico de Auditoria e Linha do Tempo (Audit Trail)</h2>
            <small class="text-secondary">Rastreabilidade completa de todas as alterações de etapas, envios, aprovações e transações.</small>
          </div>
          <span class="badge bg-secondary-subtle text-secondary border">Total: <?= count($historico ?? []) ?> registros</span>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($historico)): ?>
            <div class="d-flex flex-column gap-3">
              <?php foreach ($historico as $h): ?>
                <?php
                  $ehEmpresa = ($h['perfil'] ?? '') === 'empresa';
                  $badgePerfil = $ehEmpresa
                      ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-building me-1"></i> Vitor Hugo Pedrini</span>'
                      : '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-person-check me-1"></i> Cliente</span>';
                  $iconeEvento = match ($h['acao'] ?? '') {
                      'avanco_etapa'           => 'bi-arrow-up-right-circle text-primary',
                      'aprovacao_etapa'        => 'bi-check2-circle text-success',
                      'ajuste_solicitado'      => 'bi-pencil-square text-danger',
                      'ajuste_resolvido'       => 'bi-check-all text-success',
                      'arquivo_enviado'        => 'bi-file-earmark-arrow-up text-info',
                      'financeiro_configurado' => 'bi-wallet2 text-primary',
                      'parcela_status'         => 'bi-receipt text-success',
                      'Comprovante Enviado'    => 'bi-receipt-cutoff text-success',
                      default                  => 'bi-dot text-secondary',
                  };
                ?>
                <div class="p-3 bg-light rounded border d-flex flex-wrap align-items-start justify-content-between gap-2">
                  <div class="d-flex align-items-start gap-3">
                    <span class="fs-4 mt-1"><i class="bi <?= $iconeEvento ?>"></i></span>
                    <div>
                      <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <strong><?= htmlspecialchars($h['acao']) ?></strong>
                        <?= $badgePerfil ?>
                        <span class="text-muted small">por <?= htmlspecialchars($h['autor_nome'] ?? 'Sistema') ?></span>
                      </div>
                      <?php if (!empty($h['descricao'])): ?>
                        <p class="text-secondary small mb-1"><?= nl2br(htmlspecialchars($h['descricao'])) ?></p>
                      <?php endif; ?>
                      <?php if (!empty($h['etapa_de']) && !empty($h['etapa_para'])): ?>
                        <small class="badge bg-white text-secondary border">Fase: <?= htmlspecialchars($h['etapa_de']) ?> &rarr; <?= htmlspecialchars($h['etapa_para']) ?></small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="text-end small text-muted">
                    <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y H:i', strtotime($h['criado_em'])) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="p-4 text-center text-secondary small">
              <i class="bi bi-info-circle fs-3 d-block mb-1 text-secondary"></i>
              Nenhum evento registrado no histórico deste projeto ainda. As ações dos usuários aparecerão aqui automaticamente.
            </div>
          <?php endif; ?>
        </div>
      </div>

    </main>

    <!-- Rodape -->
    <footer class="bg-white border-top px-3 py-2 text-center text-secondary small">
      <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
        <span>&copy; <span id="ano-atual">2026</span> iiKliente Gestão de Clientes.</span>
        <span>Desenvolvido com foco em clareza por Vitor Hugo Dos Santos Pedrini</span>
      </div>
    </footer>
  </div>
</div>

<!-- ================= MODAL: EDITAR DETALHES DO PROJETO ================= -->
<div class="modal fade" id="modalEditarProjeto" tabindex="-1" aria-labelledby="tituloModalEditarProjeto" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title h6 fw-bold mb-0" id="tituloModalEditarProjeto"><i class="bi bi-pencil-square text-primary me-2"></i> Editar Informações do Projeto</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <form action="projeto-detalhes" method="post" class="needs-validation" novalidate>
        <?= $campoCsrf ?? '' ?>
        <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">

        <div class="modal-body">
          <div class="mb-3">
            <label for="edit-proj-nome" class="form-label small fw-semibold">Nome do Projeto *</label>
            <input type="text" name="nome" id="edit-proj-nome" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
          <div class="mb-3">
            <label for="edit-proj-tipo" class="form-label small fw-semibold">Tipo de Solução *</label>
            <input type="text" name="tipo" id="edit-proj-tipo" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['tipo'] ?? 'Loja Virtual', ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label for="edit-proj-valor" class="form-label small fw-semibold">Valor do Orçamento (R$)</label>
              <input type="number" step="0.01" name="valor_orcamento" id="edit-proj-valor" class="form-control form-control-sm" value="<?= htmlspecialchars((string) ($projeto['valor_orcamento'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
              <label for="edit-proj-prazo" class="form-label small fw-semibold">Prazo de Entrega</label>
              <input type="date" name="prazo_entrega" id="edit-proj-prazo" class="form-control form-control-sm" value="<?= htmlspecialchars($projeto['prazo_entrega'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="mb-2">
            <label for="edit-proj-desc" class="form-label small fw-semibold">Descrição / Escopo Geral</label>
            <textarea name="descricao" id="edit-proj-desc" rows="3" class="form-control form-control-sm"><?= htmlspecialchars($projeto['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/comum/modal_como_funciona.php'; ?>

<!-- --- Bloco: Bootstrap 5 JS Bundle com Fallback Local --- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" onerror="this.onerror=null;this.src='../assets/vendor/bootstrap/js/bootstrap.bundle.min.js';"></script>
<script src="../assets/js/main.min.js"></script>
</body>
</html>
