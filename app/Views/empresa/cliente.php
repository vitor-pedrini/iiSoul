<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ficha do cliente - iiKliente</title>

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
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="clientes"><i class="bi bi-people-fill me-2"></i> Clientes</a></li>
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

    <?php
      $mapaSegmento = [
          'comercio'   => 'Comércio',
          'saude'      => 'Saúde',
          'educacao'   => 'Educação',
          'tecnologia' => 'Tecnologia',
      ];
      $nomeCli    = $cliente['nome'] ?? 'Cliente';
      $cnpjCli    = !empty($cliente['cnpj']) ? $cliente['cnpj'] : 'Não informado';
      $segCli     = $mapaSegmento[$cliente['segmento'] ?? ''] ?? ucfirst($cliente['segmento'] ?? 'Tecnologia');
      $statusCli  = $cliente['status'] ?? 'prospecto';
      $contatoCli = !empty($cliente['contato']) ? $cliente['contato'] : $nomeCli;
      $cargoCli   = !empty($cliente['cargo']) ? $cliente['cargo'] : 'Responsável';
      $emailCli   = $cliente['email'] ?? '';
      $telCli     = !empty($cliente['telefone']) ? $cliente['telefone'] : 'Não informado';
      $endCli     = !empty($cliente['endereco']) ? $cliente['endereco'] : 'Endereço não informado';
      $cidCli     = !empty($cliente['cidade']) ? $cliente['cidade'] : '';
      $ufCli      = !empty($cliente['uf']) ? $cliente['uf'] : '';
      $cepCli     = !empty($cliente['cep']) ? $cliente['cep'] : '';
      $dataCadCli = !empty($cliente['criado_em']) ? date('d/m/Y', strtotime($cliente['criado_em'])) : date('d/m/Y');
      $letraCli   = mb_strtoupper(mb_substr($nomeCli, 0, 1));

      $temProjCli = !empty($projeto);
      $nomeProj   = $projeto['nome'] ?? 'Nenhum projeto iniciado';
      $codProj    = $projeto['codigo'] ?? 'Aguardando briefing';
      $descProj   = $projeto['descricao'] ?? 'Este cliente ainda não possui um projeto em execução.';
      $etapaProj  = $projeto['etapa_atual'] ?? 'Sem projeto';
      $progProj   = $temProjCli ? (int) ($projeto['progresso'] ?? 20) : 0;
      $classeProgProj = $progProj > 0 ? 'progresso-' . (in_array($progProj, [20, 40, 60, 80, 100], true) ? $progProj : 20) : 'w-0';

      $listaOrc  = $orcamentosCliente ?? [];
      $totalOrc  = count($listaOrc);
      $ultimoOrc = $listaOrc[0] ?? null;
    ?>

    <!-- Conteudo da pagina -->
    <main class="flex-grow-1 p-3 p-md-4">

      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-3">
          <li class="breadcrumb-item"><a href="clientes" class="text-decoration-none">Clientes</a></li>
          <li class="breadcrumb-item active" aria-current="page">Detalhes do cliente</li>
        </ol>
      </nav>

      <!-- Cabecalho do cliente -->
      <div class="card mb-3">
        <div class="card-body">
          <div class="row g-3 align-items-center">
            <div class="col-lg-5 d-flex align-items-center">
              <span class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-1 me-3 flex-shrink-0 avatar-grande"><?= htmlspecialchars($letraCli, ENT_QUOTES, 'UTF-8') ?></span>
              <div>
                <h1 class="fw-bold mb-1"><?= htmlspecialchars($nomeCli, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="mb-1">CNPJ <?= htmlspecialchars($cnpjCli, ENT_QUOTES, 'UTF-8') ?> <span class="badge rounded-pill <?= $statusCli === 'ativo' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' ?> ms-1"><i class="bi bi-check-circle me-1"></i><?= ucfirst(htmlspecialchars($statusCli, ENT_QUOTES, 'UTF-8')) ?></span></p>
                <small class="text-secondary"><?= htmlspecialchars($segCli, ENT_QUOTES, 'UTF-8') ?> · Cadastrado em <?= htmlspecialchars($dataCadCli, ENT_QUOTES, 'UTF-8') ?></small>
              </div>
            </div>

            <div class="col-lg-7">
              <div class="row g-2 mb-3 small">
                <div class="col-md-4 d-flex align-items-center">
                  <i class="bi bi-person-fill fs-4 text-primary me-2"></i>
                  <div class="lh-sm"><span class="text-secondary d-block">Contato principal</span><strong><?= htmlspecialchars($contatoCli, ENT_QUOTES, 'UTF-8') ?></strong><br><span class="text-secondary"><?= htmlspecialchars($cargoCli, ENT_QUOTES, 'UTF-8') ?></span></div>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                  <i class="bi bi-envelope-fill fs-4 text-primary me-2"></i>
                  <div class="lh-sm text-break"><span class="text-secondary d-block">E-mail</span><?= htmlspecialchars($emailCli, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                  <i class="bi bi-telephone-fill fs-4 text-primary me-2"></i>
                  <div class="lh-sm"><span class="text-secondary d-block">Telefone</span><?= htmlspecialchars($telCli, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
              </div>

              <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary" data-toast="Mensagem enviada para o e-mail do cliente!">
                  <i class="bi bi-envelope me-1"></i> Enviar mensagem
                </button>
                <a href="solicitacoes" class="btn btn-outline-primary"><i class="bi bi-file-earmark-plus me-1"></i> Nova solicitação</a>
                <div class="dropdown">
                  <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Mais ações</button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="clientes"><i class="bi bi-pencil me-2"></i> Editar cliente</a></li>
                    <li><a class="dropdown-item" href="#" data-toast="Nota adicionada na ficha." data-toast-tipo="success"><i class="bi bi-journal-plus me-2"></i> Adicionar nota</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="clientes"><i class="bi bi-trash me-2"></i> Excluir (na lista)</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Abas -->
      <ul class="nav nav-underline mb-3 border-bottom" id="abas-cliente" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aba-geral" type="button" role="tab" aria-selected="true">Visão geral</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-historico" type="button" role="tab" aria-selected="false">Histórico</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-projeto" type="button" role="tab" aria-selected="false">Projeto</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aba-orcamentos" type="button" role="tab" aria-selected="false">Orçamentos</button></li>
      </ul>

      <div class="tab-content">

        <!-- ========== ABA: VISAO GERAL ========== -->
        <div class="tab-pane fade show active" id="aba-geral" role="tabpanel" tabindex="0">

          <div class="row g-3 mb-3">
            <div class="col-sm-6 col-xl-3">
              <div class="card h-100"><div class="card-body d-flex">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-folder-fill fs-4"></i></span>
                <div><span class="small">Projetos</span><div class="fs-2 fw-bold lh-1"><?= $temProjCli ? 1 : 0 ?></div><small class="text-secondary"><?= htmlspecialchars($etapaProj, ENT_QUOTES, 'UTF-8') ?></small></div>
              </div></div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="card h-100"><div class="card-body d-flex">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text-fill fs-4"></i></span>
                <div><span class="small">Orçamentos</span><div class="fs-2 fw-bold lh-1"><?= $totalOrc ?></div><small class="text-secondary">Registrados</small></div>
              </div></div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="card h-100"><div class="card-body d-flex">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-circle-fill fs-4"></i></span>
                <div><span class="small">Solicitações</span><div class="fs-2 fw-bold lh-1"><?= $totalOrc ?></div><small class="text-secondary">Vinculadas ao cliente</small></div>
              </div></div>
            </div>
            <div class="col-sm-6 col-xl-3">
              <div class="card h-100"><div class="card-body d-flex">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-calendar-event-fill fs-4"></i></span>
                <div><span class="small">Cliente desde</span><div class="fs-4 fw-bold lh-1 mt-1"><?= htmlspecialchars($dataCadCli, ENT_QUOTES, 'UTF-8') ?></div><small class="text-secondary"><?= ucfirst(htmlspecialchars($statusCli, ENT_QUOTES, 'UTF-8')) ?></small></div>
              </div></div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <!-- Informacoes -->
            <div class="col-lg-6 col-xl-4">
              <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <h2 class="h5 fw-bold mb-0">Informações do cliente</h2>
                  <a href="clientes" class="small text-decoration-none">Editar</a>
                </div>
                <dl class="row small mb-0">
                  <dt class="col-4 text-secondary fw-normal">Razão social</dt><dd class="col-8"><?= htmlspecialchars($nomeCli, ENT_QUOTES, 'UTF-8') ?></dd>
                  <dt class="col-4 text-secondary fw-normal">CNPJ</dt><dd class="col-8"><?= htmlspecialchars($cnpjCli, ENT_QUOTES, 'UTF-8') ?></dd>
                  <dt class="col-4 text-secondary fw-normal">Segmento</dt><dd class="col-8"><?= htmlspecialchars($segCli, ENT_QUOTES, 'UTF-8') ?></dd>
                  <dt class="col-4 text-secondary fw-normal">Status</dt><dd class="col-8"><?= ucfirst(htmlspecialchars($statusCli, ENT_QUOTES, 'UTF-8')) ?></dd>
                  <dt class="col-4 text-secondary fw-normal">Endereço</dt><dd class="col-8 mb-0"><?= htmlspecialchars($endCli, ENT_QUOTES, 'UTF-8') ?><?= $cidCli !== '' ? '<br>' . htmlspecialchars($cidCli . ($ufCli !== '' ? ' - ' . $ufCli : '') . ($cepCli !== '' ? ', ' . $cepCli : ''), ENT_QUOTES, 'UTF-8') : '' ?></dd>
                </dl>
              </div></div>
            </div>

            <!-- Contato principal -->
            <div class="col-lg-6 col-xl-4">
              <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <h2 class="h5 fw-bold mb-0">Contato principal</h2>
                  <a href="clientes" class="small text-decoration-none">Editar</a>
                </div>
                <dl class="row small mb-0">
                  <dt class="col-4 text-secondary fw-normal">Nome</dt><dd class="col-8"><?= htmlspecialchars($contatoCli, ENT_QUOTES, 'UTF-8') ?></dd>
                  <dt class="col-4 text-secondary fw-normal">Cargo</dt><dd class="col-8"><?= htmlspecialchars($cargoCli, ENT_QUOTES, 'UTF-8') ?></dd>
                  <dt class="col-4 text-secondary fw-normal">E-mail</dt><dd class="col-8 text-break"><a href="mailto:<?= htmlspecialchars($emailCli, ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none"><?= htmlspecialchars($emailCli, ENT_QUOTES, 'UTF-8') ?></a></dd>
                  <dt class="col-4 text-secondary fw-normal">Telefone</dt><dd class="col-8 mb-0"><?= htmlspecialchars($telCli, ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
              </div></div>
            </div>

            <!-- Projeto atual -->
            <div class="col-xl-4">
              <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <h2 class="h5 fw-bold mb-0">Projeto atual</h2>
                  <a href="#aba-projeto" class="small text-decoration-none" data-bs-toggle="tab" data-bs-target="#aba-projeto">Ver projeto <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-file-earmark-text-fill"></i></span>
                  <div class="flex-grow-1 lh-sm"><strong class="d-block"><?= htmlspecialchars($nomeProj, ENT_QUOTES, 'UTF-8') ?></strong><small class="text-secondary"><?= htmlspecialchars($codProj, ENT_QUOTES, 'UTF-8') ?></small></div>
                  <span class="badge bg-primary-subtle text-primary-emphasis"><?= htmlspecialchars($etapaProj, ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <div class="d-flex justify-content-between small mb-1"><strong>Progresso geral</strong><span><?= $progProj ?>%</span></div>
                <div class="progress mb-3 barra-progresso-fina" role="progressbar" aria-label="Progresso do projeto" aria-valuenow="<?= $progProj ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar <?= $classeProgProj ?>"></div>
                </div>

                <ul class="list-unstyled small mb-0">
                  <li class="mb-1"><i class="bi <?= $progProj >= 20 ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' ?> me-2"></i> Planejamento <span class="text-secondary ms-1"><?= $progProj >= 20 ? 'Concluído' : 'Pendente' ?></span></li>
                  <li class="mb-1"><i class="bi <?= $progProj >= 40 ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' ?> me-2"></i> Wireframe <span class="text-secondary ms-1"><?= $progProj >= 40 ? 'Concluído' : 'Pendente' ?></span></li>
                  <li class="mb-1"><i class="bi <?= $progProj >= 60 ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary' ?> me-2"></i> Desenvolvimento <span class="text-secondary ms-1"><?= $progProj >= 60 ? 'Em andamento' : 'Pendente' ?></span></li>
                  <li class="mb-1"><i class="bi <?= $progProj >= 80 ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary' ?> me-2"></i> Revisão <span class="text-secondary ms-1"><?= $progProj >= 80 ? 'Em revisão' : 'Pendente' ?></span></li>
                  <li><i class="bi <?= $progProj >= 100 ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' ?> me-2"></i> Publicação <span class="text-secondary ms-1"><?= $progProj >= 100 ? 'Concluído' : 'Pendente' ?></span></li>
                </ul>
              </div></div>
            </div>
          </div>

          <div class="row g-3">
            <!-- Ultimo orcamento -->
            <div class="col-lg-6 col-xl-4">
              <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <h2 class="h5 fw-bold mb-0">Último orçamento</h2>
                  <a href="#aba-orcamentos" class="small text-decoration-none" data-bs-toggle="tab" data-bs-target="#aba-orcamentos">Ver todos <i class="bi bi-arrow-right"></i></a>
                </div>
                <?php if ($ultimoOrc): ?>
                  <?php
                    $codUltOrc  = '#2026-' . str_pad((string) $ultimoOrc['id'], 4, '0', STR_PAD_LEFT);
                    $tipoUltOrc = $ultimoOrc['tipo_texto'] ?? $nomeProj;
                    $valUltOrc  = !empty($ultimoOrc['valor_orcamento'])
                        ? 'R$ ' . number_format((float) $ultimoOrc['valor_orcamento'], 2, ',', '.')
                        : ($ultimoOrc['faixa'] ?? 'Sob análise');
                    $dataUltOrc = !empty($ultimoOrc['criado_em']) ? date('d/m/Y', strtotime($ultimoOrc['criado_em'])) : $dataCadCli;
                    $stUltOrc   = $ultimoOrc['status'] ?? 'nova';
                    $txtStOrc   = ($stUltOrc === 'aceita') ? 'Aprovado' : (($stUltOrc === 'orcamento-enviado') ? 'Enviado' : (($stUltOrc === 'rascunho') ? 'Rascunho' : 'Em análise'));
                  ?>
                  <div class="d-flex align-items-center mb-3">
                    <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-file-earmark-text-fill"></i></span>
                    <div class="flex-grow-1 lh-sm"><strong class="d-block">Orçamento <?= htmlspecialchars($codUltOrc, ENT_QUOTES, 'UTF-8') ?></strong><small class="text-secondary">Projeto: <?= htmlspecialchars($tipoUltOrc, ENT_QUOTES, 'UTF-8') ?></small></div>
                    <span class="badge bg-success-subtle text-success-emphasis"><?= htmlspecialchars($txtStOrc, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                  <div class="row small mb-3">
                    <div class="col-4"><span class="text-secondary d-block">Valor</span><strong><?= htmlspecialchars($valUltOrc, ENT_QUOTES, 'UTF-8') ?></strong></div>
                    <div class="col-4"><span class="text-secondary d-block">Data</span><?= htmlspecialchars($dataUltOrc, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="col-4"><span class="text-secondary d-block">Status</span><?= htmlspecialchars($txtStOrc, ENT_QUOTES, 'UTF-8') ?></div>
                  </div>
                <?php else: ?>
                  <p class="small text-secondary mb-0">Nenhuma solicitação ou orçamento registrado para este cliente ainda.</p>
                <?php endif; ?>
              </div></div>
            </div>

            <!-- Proximos passos -->
            <div class="col-lg-6 col-xl-4">
              <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <h2 class="h5 fw-bold mb-0">Próximos passos</h2>
                  <a href="#aba-historico" class="small text-decoration-none" data-bs-toggle="tab" data-bs-target="#aba-historico">Ver histórico <i class="bi bi-arrow-right"></i></a>
                </div>
                <ul class="list-unstyled small mb-0">
                  <li class="d-flex align-items-center mb-3">
                    <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-clipboard-check"></i></span>
                    <div class="lh-sm"><strong class="d-block">Análise de requisitos</strong><span class="text-secondary"><?= $totalOrc > 0 ? 'Questionário recebido' : 'Aguardando envio do cliente' ?></span></div>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                    <span class="avatar rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-cash-coin"></i></span>
                    <div class="lh-sm"><strong class="d-block">Proposta comercial</strong><span class="text-secondary"><?= $temProjCli ? 'Projeto iniciado' : 'Enviar orçamento pela aba Solicitações' ?></span></div>
                  </li>
                  <li class="d-flex align-items-center">
                    <span class="avatar rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-code-slash"></i></span>
                    <div class="lh-sm"><strong class="d-block">Entrega prevista</strong><span class="text-secondary"><?= !empty($projeto['prazo_entrega']) ? date('d/m/Y', strtotime($projeto['prazo_entrega'])) : 'A definir após aprovação' ?></span></div>
                  </li>
                </ul>
              </div></div>
            </div>

            <!-- Acesso rapido -->
            <div class="col-xl-4">
              <div class="card h-100"><div class="card-body">
                <h2 class="h5 fw-bold mb-3">Acesso rápido</h2>
                <div class="list-group">
                  <a href="#aba-projeto" class="list-group-item list-group-item-action d-flex justify-content-between small" data-bs-toggle="tab" data-bs-target="#aba-projeto"><span><i class="bi bi-folder me-2"></i> Ver todos os projetos</span><i class="bi bi-chevron-right"></i></a>
                  <a href="solicitacoes" class="list-group-item list-group-item-action d-flex justify-content-between small"><span><i class="bi bi-file-text me-2"></i> Ver todas as solicitações</span><i class="bi bi-chevron-right"></i></a>
                  <a href="#aba-orcamentos" class="list-group-item list-group-item-action d-flex justify-content-between small" data-bs-toggle="tab" data-bs-target="#aba-orcamentos"><span><i class="bi bi-receipt me-2"></i> Ver todos os orçamentos</span><i class="bi bi-chevron-right"></i></a>
                  <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between small" data-toast="Mensagem enviada por e-mail ao cliente!"><span><i class="bi bi-envelope me-2"></i> Enviar mensagem</span><i class="bi bi-chevron-right"></i></a>
                  <a href="clientes" class="list-group-item list-group-item-action d-flex justify-content-between small"><span><i class="bi bi-person me-2"></i> Editar cliente</span><i class="bi bi-chevron-right"></i></a>
                </div>
              </div></div>
            </div>
          </div>
        </div>


        <!-- ========== ABA: HISTORICO ========== -->
        <div class="tab-pane fade" id="aba-historico" role="tabpanel" tabindex="0">
          <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold mb-3">Histórico de atividades</h2>
            <ul class="list-group list-group-flush">
              <?php if ($temProjCli): ?>
                <li class="list-group-item d-flex gap-3"><small class="text-secondary text-nowrap"><?= !empty($projeto['data_inicio']) ? date('d/m/Y', strtotime($projeto['data_inicio'])) : $dataCadCli ?></small><div class="lh-sm"><strong class="small d-block">Projeto <?= htmlspecialchars($nomeProj, ENT_QUOTES, 'UTF-8') ?> em andamento</strong><small class="text-secondary">Etapa atual: <?= htmlspecialchars($etapaProj, ENT_QUOTES, 'UTF-8') ?> (<?= $progProj ?>% concluído).</small></div></li>
              <?php endif; ?>
              <?php foreach ($listaOrc as $histOrc): ?>
                <li class="list-group-item d-flex gap-3"><small class="text-secondary text-nowrap"><?= date('d/m/Y', strtotime($histOrc['criado_em'])) ?></small><div class="lh-sm"><strong class="small d-block">Solicitação registrada: <?= htmlspecialchars($histOrc['tipo_texto'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><small class="text-secondary">Status: <?= htmlspecialchars($histOrc['status'] ?? 'nova', ENT_QUOTES, 'UTF-8') ?></small></div></li>
              <?php endforeach; ?>
              <li class="list-group-item d-flex gap-3"><small class="text-secondary text-nowrap"><?= htmlspecialchars($dataCadCli, ENT_QUOTES, 'UTF-8') ?></small><div class="lh-sm"><strong class="small d-block">Cliente cadastrado no sistema</strong><small class="text-secondary">Ficha registrada para <?= htmlspecialchars($nomeCli, ENT_QUOTES, 'UTF-8') ?>.</small></div></li>
            </ul>
          </div></div>
        </div>

        <!-- ========== ABA: PROJETO ========== -->
        <div class="tab-pane fade" id="aba-projeto" role="tabpanel" tabindex="0">
          <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
              <div>
                <h2 class="h5 fw-bold mb-0"><?= htmlspecialchars($nomeProj, ENT_QUOTES, 'UTF-8') ?></h2>
                <small class="text-secondary"><?= htmlspecialchars($codProj, ENT_QUOTES, 'UTF-8') ?> · Cliente: <?= htmlspecialchars($nomeCli, ENT_QUOTES, 'UTF-8') ?></small>
              </div>
              <div class="d-flex align-items-center gap-2">
                <?php if (!empty($projeto['id'])): ?>
                  <a href="projeto?id=<?= (int) $projeto['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-folder2-open me-1"></i> Abrir Ficha Completa do Projeto
                  </a>
                <?php endif; ?>
                <span class="badge bg-primary-subtle text-primary-emphasis"><?= htmlspecialchars($etapaProj, ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>
            <div class="progress mb-2 barra-progresso-media" role="progressbar" aria-label="Progresso do projeto" aria-valuenow="<?= $progProj ?>" aria-valuemin="0" aria-valuemax="100">
              <div class="progress-bar <?= $classeProgProj ?>"></div>
            </div>
            <small class="text-secondary"><?= $progProj ?>% concluído</small>
            <p class="mt-3 mb-3 small"><?= htmlspecialchars($descProj, ENT_QUOTES, 'UTF-8') ?></p>

            <?php if (!empty($projeto['id'])): ?>
              <?php if (!empty($projeto['ultimo_ajuste'])): ?>
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                  <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                  <div>
                    <strong>Pedido de ajuste enviado pelo cliente:</strong>
                    <div class="small mt-1 text-dark">"<?= htmlspecialchars($projeto['ultimo_ajuste'], ENT_QUOTES, 'UTF-8') ?>"</div>
                  </div>
                </div>
              <?php endif; ?>

              <div class="border-top pt-3 mt-3">
                <h3 class="h6 fw-bold mb-2"><i class="bi bi-sliders me-1 text-primary"></i> Gerenciar etapa do projeto</h3>
                <form action="projeto-etapa" method="post" class="row g-2 align-items-center">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                  <input type="hidden" name="cliente_id" value="<?= (int) $cliente['id'] ?>">
                  <div class="col-sm-8 col-md-6 col-lg-5">
                    <select name="progresso" class="form-select form-select-sm">
                      <option value="20" <?= (int)$progProj === 20 ? 'selected' : '' ?>>20% - 1. Levantamento de requisitos</option>
                      <option value="40" <?= (int)$progProj === 40 ? 'selected' : '' ?>>40% - 2. Wireframes e protótipo</option>
                      <option value="60" <?= (int)$progProj === 60 ? 'selected' : '' ?>>60% - 3. Em desenvolvimento</option>
                      <option value="80" <?= (int)$progProj === 80 ? 'selected' : '' ?>>80% - 4. Testes e validação</option>
                      <option value="100" <?= (int)$progProj === 100 ? 'selected' : '' ?>>100% - 5. Projeto concluído e entregue</option>
                    </select>
                  </div>
                  <div class="col-sm-4 col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Atualizar etapa</button>
                  </div>
                </form>
              </div>

              <!-- Lista de Arquivos do Projeto (Troca entre Empresa e Cliente) -->
              <div class="border-top pt-4 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h3 class="h6 fw-bold mb-0"><i class="bi bi-folder2-open text-primary me-1"></i> Arquivos e mockups deste projeto (<?= count($arquivos ?? []) ?>)</h3>
                  <small class="text-secondary">Visíveis também para o cliente</small>
                </div>

                <ul class="list-group list-group-flush mb-4">
                <?php if (!empty($arquivos)): ?>
                  <?php foreach ($arquivos as $arq): ?>
                    <?php
                      $ext = strtolower($arq['tipo_arquivo'] ?? 'pdf');
                      $iconeArq = 'bi-file-earmark-text text-primary';
                      if (in_array($ext, ['png', 'jpg', 'jpeg'], true)) {
                          $iconeArq = 'bi-file-earmark-image text-primary';
                      } elseif ($ext === 'pdf') {
                          $iconeArq = 'bi-file-earmark-pdf text-danger';
                      } elseif ($ext === 'zip') {
                          $iconeArq = 'bi-file-earmark-zip text-warning';
                      }
                      $kb = (int) ($arq['tamanho_kb'] ?? 500);
                      $tamTexto = ($kb >= 1024) ? number_format($kb / 1024, 1, ',', '.') . ' MB' : $kb . ' KB';
                    ?>
                    <li class="list-group-item px-0 d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <i class="bi <?= $iconeArq ?> fs-3 me-3"></i>
                        <div class="lh-sm">
                          <strong class="small d-block"><?= htmlspecialchars($arq['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <small class="text-secondary"><?= strtoupper(htmlspecialchars($ext, ENT_QUOTES, 'UTF-8')) ?> · <?= $tamTexto ?> · <?= date('d/m/Y', strtotime($arq['criado_em'])) ?></small>
                        </div>
                      </div>
                      <a href="arquivo-baixar?id=<?= (int) $arq['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i> Baixar</a>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="list-group-item px-0 text-secondary small py-2">
                    Nenhum arquivo ou mockup anexado neste projeto ainda. Use o campo abaixo para disponibilizar telas ao cliente.
                  </li>
                <?php endif; ?>
                </ul>

                <!-- Enviar Mockup ou Arquivo para o Cliente -->
                <div class="card bg-body-tertiary border">
                  <div class="card-body p-3">
                    <h4 class="h6 fw-bold mb-1"><i class="bi bi-cloud-arrow-up text-primary me-1"></i> Enviar mockup ou arquivo para o cliente</h4>
                    <small class="text-secondary d-block mb-3">Envie os wireframes (PDF, imagens), protótipos ou contratos. O cliente poderá ver e baixar na tela dele.</small>

                    <form action="arquivo-enviar" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                      <?= $campoCsrf ?? '' ?>
                      <input type="hidden" name="projeto_id" value="<?= (int) $projeto['id'] ?>">
                      <input type="hidden" name="cliente_id" value="<?= (int) $cliente['id'] ?>">
                      <input type="hidden" name="origem" value="cliente">
                      <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                          <label for="emp-arq" class="form-label small fw-semibold">Arquivo *</label>
                          <input type="file" name="arquivo" id="emp-arq" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip,.docx" required>
                        </div>
                        <div class="col-md-5">
                          <label for="emp-desc" class="form-label small fw-semibold">Título do mockup/arquivo</label>
                          <input type="text" name="descricao" id="emp-desc" class="form-control form-control-sm" placeholder="Ex: Wireframe das telas - Etapa 2" maxlength="100">
                        </div>
                        <div class="col-md-2">
                          <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-upload me-1"></i> Enviar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div></div>
        </div>

        <!-- ========== ABA: ORCAMENTOS ========== -->
        <div class="tab-pane fade" id="aba-orcamentos" role="tabpanel" tabindex="0">
          <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold mb-3">Orçamentos do cliente</h2>
            <div class="table-responsive">
              <table class="table align-middle small mb-0">
                <thead><tr class="text-secondary"><th>Número</th><th>Projeto</th><th>Valor</th><th>Enviado em</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (!empty($listaOrc)): ?>
                  <?php foreach ($listaOrc as $orc): ?>
                    <?php
                      $codOrcLinha = '#2026-' . str_pad((string) ($orc['id'] ?? 1), 4, '0', STR_PAD_LEFT);
                      $valOrc = !empty($orc['valor_orcamento'])
                          ? 'R$ ' . number_format((float) $orc['valor_orcamento'], 2, ',', '.')
                          : ($orc['faixa'] ?? 'Sob análise');
                      $stO = $orc['status'] ?? 'nova';
                      if ($stO === 'aceita') {
                          $badgeO = '<span class="badge bg-success-subtle text-success-emphasis">Aprovado</span>';
                      } elseif ($stO === 'recusada') {
                          $badgeO = '<span class="badge bg-danger-subtle text-danger-emphasis">Recusado</span>';
                      } elseif ($stO === 'orcamento-enviado') {
                          $badgeO = '<span class="badge bg-warning-subtle text-warning-emphasis">Aguardando resposta</span>';
                      } elseif ($stO === 'rascunho') {
                          $badgeO = '<span class="badge bg-info-subtle text-info-emphasis">Rascunho</span>';
                      } else {
                          $badgeO = '<span class="badge bg-primary-subtle text-primary-emphasis">Em análise</span>';
                      }
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($codOrcLinha, ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($orc['tipo_texto'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($valOrc, ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= date('d/m/Y', strtotime($orc['criado_em'])) ?></td>
                      <td><?= $badgeO ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-secondary">Nenhum orçamento emitido para este cliente ainda.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div></div>
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
