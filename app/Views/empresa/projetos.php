<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Projetos - iiKliente</title>

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
          <input type="search" class="form-control" name="q" value="<?= htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar projetos por nome, código ou cliente..." aria-label="Buscar">
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


    <!-- Conteudo da pagina -->
    <main class="flex-grow-1 p-3 p-md-4 overflow-y-auto">

      <!-- Cabecalho da Pagina -->
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 fw-bold mb-0">Gestão de Projetos</h1>
          <small class="text-secondary">Acompanhe as 5 etapas, envie wireframes/mockups e gerencie entregas aos clientes.</small>
        </div>
        <div class="d-flex gap-2">
          <a href="exportar-csv?tipo=projetos" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV
          </a>
          <a href="clientes" class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i> Ver clientes</a>
          <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalComoFunciona"><i class="bi bi-info-circle me-1"></i> Guia do processo</button>
        </div>
      </div>

      <!-- Cards de Resumo de Etapas -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
          <a href="projetos" class="text-decoration-none text-body">
            <div class="card h-100 shadow-sm border <?= empty($etapaFiltro) ? 'border-primary' : '' ?>">
              <div class="card-body p-3">
                <span class="badge bg-secondary-subtle text-secondary-emphasis mb-1">Total</span>
                <strong class="fs-4 d-block"><?= (int) ($contadores['total'] ?? 0) ?></strong>
                <small class="text-secondary">Projetos</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <a href="projetos?etapa=20" class="text-decoration-none text-body">
            <div class="card h-100 shadow-sm border <?= ($etapaFiltro === '20') ? 'border-primary' : '' ?>">
              <div class="card-body p-3">
                <span class="badge bg-warning-subtle text-warning-emphasis mb-1">20% Requisitos</span>
                <strong class="fs-4 d-block"><?= (int) ($contadores['requisitos'] ?? 0) ?></strong>
                <small class="text-secondary">Coleta de briefing</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <a href="projetos?etapa=40" class="text-decoration-none text-body">
            <div class="card h-100 shadow-sm border <?= ($etapaFiltro === '40') ? 'border-primary' : '' ?>">
              <div class="card-body p-3">
                <span class="badge bg-info-subtle text-info-emphasis mb-1">40% Wireframes</span>
                <strong class="fs-4 d-block text-primary"><?= (int) ($contadores['wireframes'] ?? 0) ?></strong>
                <small class="text-secondary">Design de telas</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <a href="projetos?etapa=60" class="text-decoration-none text-body">
            <div class="card h-100 shadow-sm border <?= ($etapaFiltro === '60') ? 'border-primary' : '' ?>">
              <div class="card-body p-3">
                <span class="badge bg-primary-subtle text-primary-emphasis mb-1">60% Dev</span>
                <strong class="fs-4 d-block"><?= (int) ($contadores['desenvolvimento'] ?? 0) ?></strong>
                <small class="text-secondary">Programação</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <a href="projetos?etapa=80" class="text-decoration-none text-body">
            <div class="card h-100 shadow-sm border <?= ($etapaFiltro === '80') ? 'border-primary' : '' ?>">
              <div class="card-body p-3">
                <span class="badge bg-purple-subtle text-purple mb-1">80% Testes</span>
                <strong class="fs-4 d-block"><?= (int) ($contadores['testes'] ?? 0) ?></strong>
                <small class="text-secondary">Validação final</small>
              </div>
            </div>
          </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
          <a href="projetos?etapa=ajuste" class="text-decoration-none text-body">
            <div class="card h-100 shadow-sm border <?= ($etapaFiltro === 'ajuste') ? 'border-danger' : '' ?>">
              <div class="card-body p-3">
                <span class="badge bg-danger-subtle text-danger-emphasis mb-1">Ajustes</span>
                <strong class="fs-4 d-block <?= ($contadores['ajustes'] > 0) ? 'text-danger' : '' ?>"><?= (int) ($contadores['ajustes'] ?? 0) ?></strong>
                <small class="text-secondary">Pedidos do cliente</small>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Filtros por Abas -->
      <div class="card mb-3">
        <div class="card-body p-2 d-flex flex-wrap gap-1 align-items-center justify-content-between">
          <ul class="nav nav-pills gap-1 small">
            <li class="nav-item"><a class="nav-link <?= empty($etapaFiltro) ? 'active' : '' ?>" href="projetos">Todos (<?= $contadores['total'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= ($etapaFiltro === '20') ? 'active' : '' ?>" href="projetos?etapa=20">Requisitos (<?= $contadores['requisitos'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= ($etapaFiltro === '40') ? 'active' : '' ?>" href="projetos?etapa=40">Wireframes (<?= $contadores['wireframes'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= ($etapaFiltro === '60') ? 'active' : '' ?>" href="projetos?etapa=60">Desenvolvimento (<?= $contadores['desenvolvimento'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= ($etapaFiltro === '80') ? 'active' : '' ?>" href="projetos?etapa=80">Testes (<?= $contadores['testes'] ?>)</a></li>
            <li class="nav-item"><a class="nav-link <?= ($etapaFiltro === '100') ? 'active' : '' ?>" href="projetos?etapa=100">Concluídos (<?= $contadores['concluidos'] ?>)</a></li>
            <?php if ($contadores['ajustes'] > 0): ?>
            <li class="nav-item"><a class="nav-link text-danger fw-semibold <?= ($etapaFiltro === 'ajuste') ? 'active bg-danger text-white' : '' ?>" href="projetos?etapa=ajuste"><i class="bi bi-exclamation-circle me-1"></i> Ajustes Pendentes (<?= $contadores['ajustes'] ?>)</a></li>
            <?php endif; ?>
          </ul>
          <?php if (!empty($busca) || !empty($etapaFiltro)): ?>
            <a href="projetos" class="btn btn-sm btn-link text-secondary text-decoration-none"><i class="bi bi-x-circle me-1"></i> Limpar filtros</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Tabela de Projetos -->
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <?php if (!empty($projetos)): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                  <tr>
                    <th class="ps-3">Código</th>
                    <th>Projeto</th>
                    <th>Cliente</th>
                    <th>Etapa Atual</th>
                    <th>Progresso</th>
                    <th>Situação / Ajustes</th>
                    <th>Prazo</th>
                    <th class="text-end pe-3">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($projetos as $proj): ?>
                    <?php
                      $prog = (int) ($proj['progresso'] ?? 20);
                      $corProgresso = match (true) {
                          $prog >= 100 => 'bg-success',
                          $prog >= 60  => 'bg-primary',
                          $prog >= 40  => 'bg-info',
                          default      => 'bg-warning',
                      };
                      $temAjuste = !empty($proj['ultimo_ajuste']);
                    ?>
                    <tr>
                      <td class="ps-3">
                        <span class="badge bg-light text-secondary border font-monospace"><?= htmlspecialchars($proj['codigo'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td>
                        <strong class="d-block"><?= htmlspecialchars($proj['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <small class="text-secondary"><?= htmlspecialchars($proj['tipo'] ?? 'Projeto Web', ENT_QUOTES, 'UTF-8') ?></small>
                        <?php if (!empty($proj['link_prototipo'])): ?>
                          <div class="mt-1">
                            <a href="<?= htmlspecialchars($proj['link_prototipo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="badge bg-primary-subtle text-primary-emphasis text-decoration-none">
                              <i class="bi bi-link-45deg me-1"></i> Protótipo Interativo
                            </a>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="cliente?id=<?= (int) $proj['cliente_id'] ?>" class="text-decoration-none text-body fw-semibold d-block">
                          <?= htmlspecialchars($proj['cliente_nome'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                        <small class="text-secondary d-block"><?= htmlspecialchars($proj['cliente_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                      </td>
                      <td>
                        <span class="badge bg-primary-subtle text-primary-emphasis"><?= htmlspecialchars($proj['etapa_atual'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td class="w-20">
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1 barra-progresso-fina" role="progressbar" aria-valuenow="<?= $prog ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar <?= $corProgresso ?> progresso-<?= $prog ?>"></div>
                          </div>
                          <span class="fw-bold small"><?= $prog ?>%</span>
                        </div>
                      </td>
                      <td>
                        <?php if ($temAjuste): ?>
                          <span class="badge bg-danger-subtle text-danger-emphasis d-inline-flex align-items-center gap-1" title="<?= htmlspecialchars($proj['ultimo_ajuste'], ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-exclamation-triangle-fill"></i> Ajuste Solicitado
                          </span>
                        <?php elseif ($prog >= 100): ?>
                          <span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-check-circle-fill me-1"></i> Concluído</span>
                        <?php else: ?>
                          <span class="badge bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-clock me-1"></i> Em andamento</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <small class="text-secondary">
                          <?= !empty($proj['prazo_entrega']) ? date('d/m/Y', strtotime($proj['prazo_entrega'])) : 'A definir' ?>
                        </small>
                      </td>
                      <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                          <a href="projeto?id=<?= (int) $proj['id'] ?>" class="btn btn-primary" title="Abrir Ficha Completa do Projeto">
                            <i class="bi bi-folder2-open me-1"></i> Ficha do Projeto
                          </a>
                          <a href="cliente?id=<?= (int) $proj['cliente_id'] ?>" class="btn btn-outline-secondary" title="Ver ficha do cliente">
                            <i class="bi bi-person"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="p-5 text-center text-secondary">
              <i class="bi bi-folder-x fs-1 text-secondary mb-2 d-block"></i>
              <strong class="d-block mb-1">Nenhum projeto encontrado.</strong>
              <small>Tente alterar os filtros de busca ou verifique se há propostas aprovadas.</small>
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

<?php require_once dirname(__DIR__) . '/comum/modal_como_funciona.php'; ?>

<!-- --- Bloco: Bootstrap 5 JS Bundle com Fallback Local --- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" onerror="this.onerror=null;this.src='../assets/vendor/bootstrap/js/bootstrap.bundle.min.js';"></script>
<script src="../assets/js/main.min.js"></script>
</body>
</html>
