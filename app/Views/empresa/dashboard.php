<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - iiKliente</title>

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
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes"><i class="bi bi-people-fill me-2"></i> Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="projetos"><i class="bi bi-folder-fill me-2"></i> Projetos</a></li>
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

      <form class="needs-validation flex-grow-1" role="search" action="busca" method="get" data-backend="true">
        <div class="input-group">
          <button class="btn btn-outline-secondary" type="submit" aria-label="Buscar"><i class="bi bi-search"></i></button>
          <input type="search" class="form-control" name="q" placeholder="Buscar clientes, projetos, orçamentos..." aria-label="Buscar">
        </div>
      </form>

      <select class="form-select w-auto d-none d-xl-block filtro-topo-periodo" aria-label="Filtrar por período">
        <option value="12">Todos os períodos (12 meses)</option>
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
    <main class="flex-grow-1 p-3 p-md-4">

      <div class="row align-items-end mb-4">
        <div class="col-md-8">
          <p class="small text-secondary text-uppercase mb-1">Área da empresa</p>
          <h1 class="fw-bold">Olá, Equipe!</h1>
          <p class="text-secondary mb-0">Aqui está um panorama completo do seu negócio. Acompanhe seus clientes, solicitações e projetos em um só lugar.</p>
        </div>
        <div class="col-md-4 text-md-end small text-secondary mt-3 mt-md-0">
          <span class="data-hoje d-block"></span>
          <em>"Grandes projetos começam com ótimos relacionamentos."</em>
        </div>
      </div>

      <!-- Cards de numeros (100% vindos do banco de dados, sem valores fixos do front) -->
      <?php
        $numAtivos     = (int) ($totalAtivos ?? 0);
        $numTotalCli   = (int) ($totalClientes ?? 0);
        $numSol        = (int) ($novasSolicitacoes ?? 0);
        $numTotalSol   = (int) ($totalSolicitacoes ?? 0);
        $numOrcSem     = (int) ($orcamentosSemResp ?? 0);
        $numProjetos   = (int) ($projetosAndamento ?? 0);
        $numTotalProj  = (int) ($totalProjetos ?? 0);
        $listaAtencao  = $itensAtencao ?? [];
        $listaAtualiz  = $atualizacoesRecentes ?? [];
        $etapasData    = $etapasGrafico ?? [
            'planejamento'    => 0,
            'desenvolvimento' => 0,
            'revisao'         => 0,
            'aguardando'      => 0,
            'concluido'       => 0,
        ];
        $valoresEtapas = implode(',', array_map('intval', array_values($etapasData)));
        $mesesData     = $clientesPorMes ?? array_fill(0, 12, 0);
        $valoresMeses  = implode(',', array_map('intval', $mesesData));
      ?>
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100">
            <div class="card-body d-flex">
              <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-people-fill fs-4"></i></span>
              <div>
                <span class="small">Clientes Ativos</span>
                <div><span class="fs-2 fw-bold"><?= $numAtivos ?></span> <small class="text-secondary ms-1">de <?= $numTotalCli ?></small></div>
                <small class="text-secondary">Cadastrados no banco</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100">
            <div class="card-body d-flex">
              <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text-fill fs-4"></i></span>
              <div>
                <span class="small">Novas Solicitações</span>
                <div><span class="fs-2 fw-bold"><?= $numSol ?></span> <small class="text-secondary ms-1">de <?= $numTotalSol ?></small></div>
                <small class="text-secondary">Pendentes de análise</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100">
            <div class="card-body d-flex">
              <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-clock-history fs-4"></i></span>
              <div>
                <span class="small">Orçamentos sem Resposta</span>
                <div><span class="fs-2 fw-bold"><?= $numOrcSem ?></span></div>
                <small class="text-secondary">Aguardando retorno</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100">
            <div class="card-body d-flex">
              <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-folder-fill fs-4"></i></span>
              <div>
                <span class="small">Projetos em Andamento</span>
                <div><span class="fs-2 fw-bold"><?= $numProjetos ?></span> <small class="text-secondary ms-1">de <?= $numTotalProj ?></small></div>
                <small class="text-secondary">Em execução</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Graficos -->
      <div class="row g-3 mb-4">
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h2 class="h5 fw-bold mb-0">Clientes novos por mês</h2>
                  <small class="text-secondary">Total de novos clientes cadastrados no sistema.</small>
                </div>
                <select class="form-select form-select-sm w-auto" id="periodo-grafico" aria-label="Período do gráfico">
                  <option value="12">Últimos 12 meses</option>
                  <option value="6">Últimos 6 meses</option>
                </select>
              </div>
              <div class="grafico-caixa">
                <canvas id="grafico-clientes" data-valores="<?= htmlspecialchars($valoresMeses, ENT_QUOTES, 'UTF-8') ?>" aria-label="Gráfico de barras com clientes novos por mês" role="img"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-body">
              <h2 class="h5 fw-bold mb-0">Projetos por etapa</h2>
              <small class="text-secondary d-block mb-3">Distribuição dos projetos em cada fase.</small>
              <div class="grafico-caixa">
                <canvas id="grafico-projetos" data-valores="<?= htmlspecialchars($valoresEtapas, ENT_QUOTES, 'UTF-8') ?>" aria-label="Gráfico de rosca com projetos por etapa" role="img"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Projetos em Andamento (Acompanhamento em Tempo Real) -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h2 class="h5 fw-bold mb-0"><i class="bi bi-kanban text-primary me-2"></i> Projetos em Andamento</h2>
              <small class="text-secondary">Acompanhe a evolução de cada etapa dos clientes em tempo real.</small>
            </div>
            <a href="projetos" class="small text-decoration-none">Ver todos os projetos <i class="bi bi-arrow-right"></i></a>
          </div>

          <?php if (!empty($projetos)): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle small mb-0">
                <thead>
                  <tr class="text-secondary">
                    <th>Projeto</th>
                    <th>Cliente</th>
                    <th>Etapa Atual</th>
                    <th>Progresso</th>
                    <th>Situação</th>
                    <th class="text-end">Ação</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($projetos as $p): ?>
                    <?php
                      $progP = (int) ($p['progresso'] ?? 20);
                      $corProg = ($progP >= 100) ? 'bg-success' : (($progP >= 60) ? 'bg-primary' : 'bg-info');
                      $temAjuste = !empty($p['ultimo_ajuste']);
                    ?>
                    <tr>
                      <td>
                        <strong class="d-block"><?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <small class="text-secondary"><?= htmlspecialchars($p['codigo'], ENT_QUOTES, 'UTF-8') ?></small>
                      </td>
                      <td>
                        <?= htmlspecialchars($p['cliente_nome'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?>
                      </td>
                      <td>
                        <span class="badge bg-primary-subtle text-primary-emphasis"><?= htmlspecialchars($p['etapa_atual'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td class="w-25">
                        <div class="d-flex align-items-center gap-2">
                          <div class="progress flex-grow-1 barra-progresso-fina" role="progressbar" aria-valuenow="<?= $progP ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar <?= $corProg ?> progresso-<?= $progP ?>"></div>
                          </div>
                          <span class="fw-bold"><?= $progP ?>%</span>
                        </div>
                      </td>
                      <td>
                        <?php if ($temAjuste): ?>
                          <span class="badge bg-danger-subtle text-danger-emphasis"><i class="bi bi-exclamation-triangle me-1"></i> Ajuste Solicitado</span>
                        <?php elseif ($progP >= 100): ?>
                          <span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-check-circle me-1"></i> Concluído</span>
                        <?php else: ?>
                          <span class="badge bg-success-subtle text-success-emphasis">Em Execução</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <div class="btn-group btn-group-sm">
                          <a href="projeto?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-folder2-open me-1"></i> Ficha do Projeto
                          </a>
                          <a href="cliente?id=<?= (int)$p['cliente_id'] ?>#aba-projeto" class="btn btn-sm btn-outline-secondary" title="Ficha do cliente">
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
            <p class="text-secondary small mb-0 py-2">Nenhum projeto em andamento no momento. Quando um cliente aprovar um orçamento, o projeto aparecerá aqui.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Listas -->
      <div class="row g-3">

        <div class="col-xl-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 fw-bold mb-0">Precisa de atenção <span class="badge rounded-pill text-bg-danger ms-1"><?= count($listaAtencao) ?></span></h2>
                <a href="solicitacoes" class="small text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
              </div>
              <small class="text-secondary d-block mb-2">Itens que precisam da sua atenção para seguir no fluxo.</small>

              <div class="list-group list-group-flush">
              <?php if (!empty($listaAtencao)): ?>
                <?php foreach ($listaAtencao as $item): ?>
                  <?php
                    $stItem = $item['status'] ?? 'nova';
                    $badgeItem = ($stItem === 'orcamento-enviado')
                        ? '<span class="badge bg-warning-subtle text-warning-emphasis">Sem resposta</span>'
                        : '<span class="badge bg-danger-subtle text-danger-emphasis">Pendente</span>';
                    $iconeCor = ($stItem === 'orcamento-enviado')
                        ? 'bg-warning-subtle text-warning'
                        : 'bg-danger-subtle text-danger';
                    $codSol = '#2026-' . str_pad((string) ($item['id'] ?? 1), 4, '0', STR_PAD_LEFT);
                  ?>
                  <a href="solicitacoes" class="list-group-item list-group-item-action px-0 d-flex align-items-center">
                    <span class="icone-caixa rounded <?= $iconeCor ?> d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-file-earmark-text-fill"></i></span>
                    <span class="flex-grow-1 lh-sm">
                      <strong class="small d-block">Solicitação <?= htmlspecialchars($codSol, ENT_QUOTES, 'UTF-8') ?></strong>
                      <small class="text-secondary">Cliente: <?= htmlspecialchars($item['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($item['tipo_texto'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</small>
                    </span>
                    <?= $badgeItem ?>
                  </a>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="py-3 text-secondary small">Nenhuma pendência no momento!</div>
              <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Clientes recentes (vindos do banco) -->
        <div class="col-xl-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h5 fw-bold mb-0">Clientes recentes</h2>
                <a href="clientes" class="small text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
              </div>

              <div class="table-responsive">
                <table class="table table-sm align-middle small">
                  <thead>
                    <tr class="text-secondary"><th>Cliente</th><th>Cadastro</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                  <?php if (!empty($clientesRecentes)): ?>
                    <?php foreach ($clientesRecentes as $cr): ?>
                    <?php
                      $stCr = $cr['status'] ?? 'prospecto';
                      $corBadgeCr = ($stCr === 'ativo') ? 'bg-success-subtle text-success-emphasis' : (($stCr === 'prospecto') ? 'bg-warning-subtle text-warning-emphasis' : 'bg-secondary-subtle text-secondary-emphasis');
                    ?>
                    <tr>
                      <td>
                        <strong class="d-block"><?= htmlspecialchars($cr['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <?php if (!empty($cr['projeto_nome'])): ?>
                          <span class="badge bg-primary-subtle text-primary-emphasis mt-1"><?= htmlspecialchars($cr['projeto_nome'], ENT_QUOTES, 'UTF-8') ?> · <?= (int)($cr['projeto_progresso'] ?? 20) ?>%</span>
                        <?php elseif (!empty($cr['contato']) && $cr['contato'] !== $cr['nome']): ?>
                          <span class="text-secondary d-block"><?= htmlspecialchars($cr['contato'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php else: ?>
                          <span class="text-secondary"><?= htmlspecialchars($cr['email'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                      </td>
                      <td><?= date('d/m/Y', strtotime($cr['criado_em'])) ?></td>
                      <td><span class="badge <?= $corBadgeCr ?>"><?= ucfirst(htmlspecialchars($stCr, ENT_QUOTES, 'UTF-8')) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="3" class="text-secondary py-3">Nenhum cliente cadastrado ainda.</td></tr>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <a href="clientes" class="btn bg-primary-subtle text-primary mt-auto">Ver todos os clientes</a>
            </div>
          </div>
        </div>

        <!-- Atualizacoes e aprovacoes (100% vindas do banco) -->
        <div class="col-xl-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Atualizações e aprovações</h2>
                <a href="solicitacoes" class="small text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
              </div>

              <?php if (!empty($listaAtualiz)): ?>
                <ul class="list-unstyled mb-0">
                  <?php foreach ($listaAtualiz as $idxAt => $at): ?>
                    <li class="d-flex <?= $idxAt < count($listaAtualiz) - 1 ? 'mb-3' : '' ?>">
                      <i class="bi bi-circle-fill <?= htmlspecialchars($at['cor'], ENT_QUOTES, 'UTF-8') ?> small me-2 mt-1"></i>
                      <div class="lh-sm">
                        <strong class="small d-block"><?= htmlspecialchars($at['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <small class="text-secondary d-block"><?= htmlspecialchars($at['subtitulo'], ENT_QUOTES, 'UTF-8') ?></small>
                        <small class="text-secondary"><?= htmlspecialchars($at['data'], ENT_QUOTES, 'UTF-8') ?></small>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="small text-secondary mb-0 py-2">Nenhuma atualização registrada ainda.</p>
              <?php endif; ?>
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

<!-- --- Bloco: Scripts com Fallback Local e Arquivos Minificados --- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>if (typeof bootstrap === 'undefined') { document.write('<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" integrity="sha384-JUh163oCRItcbPme8pYnROHQMC6fNKTBWtRG3I3I0erJkzNgL7uxKlNwcrcFKeqF" crossorigin="anonymous"></script>
<script>if (typeof Chart === 'undefined') { document.write('<script src="../assets/vendor/chartjs/chart.umd.min.js"><\/script>'); }</script>
<script src="../assets/js/main.min.js?v=2"></script>
<script src="../assets/js/graficos.min.js?v=2"></script>
</body>
</html>
