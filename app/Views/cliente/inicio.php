<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Início - iiKliente</title>

  <!-- --- Bloco: Bootstrap 5 via CDN com Fallback Local e CSS Minificado --- -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" onerror="this.onerror=null;this.href='../assets/vendor/bootstrap/css/bootstrap.min.css';">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
  <link rel="stylesheet" href="../assets/css/estilo.min.css">
</head>
<body class="bg-body-tertiary">

<?php if (!empty($flashToast)): ?>
  <div id="flash-toast-servidor" class="d-none" data-mensagem="<?= htmlspecialchars($flashToast['texto'], ENT_QUOTES, 'UTF-8') ?>" data-tipo="<?= htmlspecialchars($flashToast['tipo'], ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<?php
  $nomeUsuario = $usuarioAtual['nome'] ?? 'Cliente';
  $primeiroNome = explode(' ', trim($nomeUsuario))[0] ?: 'Cliente';
  $letraAvatar = mb_strtoupper(mb_substr($nomeUsuario, 0, 1));

  $temProjeto = !empty($projeto);
  $temSolicitacao = !empty($ultimaSolicitacao) && (($ultimaSolicitacao['status'] ?? '') !== 'rascunho');
  $temRascunho = !empty($ultimaSolicitacao) && (($ultimaSolicitacao['status'] ?? '') === 'rascunho');

  $progressoAtual = $temProjeto ? (int) ($projeto['progresso'] ?? 20) : ($temSolicitacao ? 20 : 0);
  $classeProgresso = $progressoAtual > 0
      ? 'progresso-' . (in_array($progressoAtual, [20, 40, 60, 80, 100], true) ? $progressoAtual : 20)
      : 'w-0';

  // Definir status real do orcamento
  $valorOrcamentoNum = (float) ($ultimaSolicitacao['valor_orcamento'] ?? ($projeto['valor_orcamento'] ?? 0));
  $statusSol = $ultimaSolicitacao['status'] ?? ($temProjeto && $valorOrcamentoNum > 0 ? 'aceita' : '');

  if ($statusSol === 'aceita' || ($temProjeto && $valorOrcamentoNum > 0 && $progressoAtual >= 40)) {
      $statusOrcTitulo = 'Aprovado';
      $statusOrcIcone  = 'bi-check-circle-fill text-success';
      $statusOrcSub    = !empty($projeto['data_inicio']) ? 'em ' . date('d/m/Y', strtotime($projeto['data_inicio'])) : 'Projeto ativo';
      $badgeOrcClasse  = 'bg-success-subtle text-success-emphasis';
  } elseif ($statusSol === 'orcamento-enviado' || $valorOrcamentoNum > 0) {
      $statusOrcTitulo = 'Proposta recebida';
      $statusOrcIcone  = 'bi-envelope-check-fill text-primary';
      $statusOrcSub    = 'Confira o valor enviado';
      $badgeOrcClasse  = 'bg-primary-subtle text-primary-emphasis';
  } elseif ($temRascunho) {
      $statusOrcTitulo = 'Rascunho salvo';
      $statusOrcIcone  = 'bi-floppy-fill text-info';
      $statusOrcSub    = 'Finalize o envio';
      $badgeOrcClasse  = 'bg-info-subtle text-info-emphasis';
  } elseif ($temSolicitacao || $temProjeto) {
      $statusOrcTitulo = 'Em análise';
      $statusOrcIcone  = 'bi-hourglass-split text-warning';
      $statusOrcSub    = 'Enviado em ' . date('d/m/Y', strtotime($ultimaSolicitacao['criado_em'] ?? ($projeto['data_inicio'] ?? 'now')));
      $badgeOrcClasse  = 'bg-warning-subtle text-warning-emphasis';
  } else {
      $statusOrcTitulo = 'Pendente';
      $statusOrcIcone  = 'bi-exclamation-circle-fill text-secondary';
      $statusOrcSub    = 'Preencha o questionário';
      $badgeOrcClasse  = 'bg-secondary-subtle text-secondary-emphasis';
  }

  $nomeProjExibir  = $projeto['nome'] ?? ($ultimaSolicitacao['tipo_texto'] ?? 'Nenhum projeto iniciado');
  $codProjExibir   = $projeto['codigo'] ?? ($temSolicitacao ? '#2026-' . str_pad((string) $ultimaSolicitacao['id'], 4, '0', STR_PAD_LEFT) : 'Aguardando briefing');
  $etapaExibir     = $projeto['etapa_atual'] ?? ($temSolicitacao ? 'Análise de requisitos' : 'Questionário inicial');
  $subEtapaExibir  = $temProjeto ? 'Acompanhamento ativo' : ($temSolicitacao ? 'Avaliação da equipe' : 'Envie suas respostas');
  $descProjExibir  = $projeto['descricao'] ?? ($temSolicitacao ? trim((string) $ultimaSolicitacao['mensagem'], '"') : 'Preencha o questionário de orçamento para que nossa equipe conheça o que você precisa e inicie o seu projeto.');
  $dataInicioExib  = !empty($projeto['data_inicio']) ? date('d/m/Y', strtotime($projeto['data_inicio'])) : ($temSolicitacao ? date('d/m/Y', strtotime($ultimaSolicitacao['criado_em'])) : 'A definir');
  $prazoEntregaExib= !empty($projeto['prazo_entrega']) ? date('d/m/Y', strtotime($projeto['prazo_entrega'])) : ($ultimaSolicitacao['prazo'] ?? 'A definir');

  $valorOrcExibir  = $valorOrcamentoNum > 0
      ? 'R$ ' . number_format($valorOrcamentoNum, 2, ',', '.')
      : ($ultimaSolicitacao['faixa'] ?? 'R$ ---,--');
?>

<div class="d-flex min-vh-100 bg-white">

  <!-- ================= MENU LATERAL ================= -->
  <aside class="offcanvas-lg offcanvas-start menu-lateral flex-shrink-0" tabindex="-1" id="menu-lateral" aria-labelledby="titulo-menu">

    <div class="offcanvas-header border-bottom">
      <h2 class="offcanvas-title h6" id="titulo-menu">Menu</h2>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#menu-lateral" aria-label="Fechar"></button>
    </div>

    <div class="offcanvas-body flex-grow-1 d-flex flex-column p-3">
      <a class="d-flex align-items-center text-decoration-none text-body mb-4" href="inicio">
        <i class="bi bi-laptop fs-2 text-primary me-2"></i>
        <span class="lh-1">
          <span class="fw-bold d-block">iiKliente</span>
          <small class="text-secondary">Gestão de Clientes</small>
        </span>
      </a>

      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="inicio"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link" href="projeto"><i class="bi bi-folder-fill me-2"></i> Meu Projeto</a></li>
        <li class="nav-item"><a class="nav-link" href="questionario"><i class="bi bi-file-earmark-text-fill me-2"></i> Orçamento / Questionário</a></li>
        <li class="nav-item"><a class="nav-link" href="perfil"><i class="bi bi-person-fill me-2"></i> Perfil</a></li>
      </ul>

      <div class="mt-auto pt-4">
        <div class="card mb-3">
          <div class="card-body p-3">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-headset fs-3 text-primary me-2"></i>
              <strong class="small">Precisa de ajuda?</strong>
            </div>
            <p class="small text-secondary">Fale com nossa equipe pelo e-mail.</p>
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

      <div class="flex-grow-1"></div>

      <!-- Item 4: Seletor de Projetos Ativos (Multi-projetos por Cliente) -->
      <?php if (!empty($todosProjetos) && count($todosProjetos) > 1): ?>
        <div class="dropdown">
          <button class="btn btn-outline-primary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-folder2-open"></i>
            <span class="d-none d-md-inline">Projeto:</span>
            <strong><?= htmlspecialchars(mb_strimwidth($projeto['nome'] ?? 'Selecionar', 0, 20, '...')) ?></strong>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><h6 class="dropdown-header">Seus Projetos Contratados</h6></li>
            <?php foreach ($todosProjetos as $pItem): ?>
              <li>
                <a class="dropdown-item small d-flex justify-content-between align-items-center <?= ((int)$pItem['id'] === (int)($projeto['id'] ?? 0)) ? 'active' : '' ?>" href="<?= $urlBase ?>/cliente/projeto-selecionar?id=<?= (int)$pItem['id'] ?>">
                  <span><?= htmlspecialchars($pItem['nome']) ?></span>
                  <span class="badge bg-secondary-subtle text-secondary ms-2"><?= (int)$pItem['progresso'] ?>%</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

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
          <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold me-2"><?= htmlspecialchars($letraAvatar, ENT_QUOTES, 'UTF-8') ?></span>
          <span class="lh-sm text-start d-none d-md-block">
            <strong class="d-block small"><?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?></strong>
            <small class="text-secondary">Cliente</small>
          </span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item" href="perfil"><i class="bi bi-person me-2"></i> Meu perfil</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../logout"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
        </ul>
      </div>
    </header>


    <!-- Conteudo da pagina -->
    <main class="flex-grow-1 p-3 p-md-4">

      <div class="row align-items-end mb-4">
        <div class="col-md-8">
          <p class="small text-secondary text-uppercase mb-1">Área do cliente</p>
          <h1 class="fw-bold">Olá, <?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?>!</h1>
          <p class="text-secondary mb-0">Acompanhe o andamento do seu projeto. Aqui você encontra todas as informações em um só lugar.</p>
        </div>
        <div class="col-md-4 text-md-end small text-secondary mt-3 mt-md-0">
          <span class="data-hoje d-block"></span>
          <em>"Grandes ideias começam com boas parcerias."</em>
        </div>
      </div>

      <!-- Cards de resumo -->
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100"><div class="card-body d-flex">
            <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text-fill fs-4"></i></span>
            <div class="lh-sm">
              <small class="d-block">Status do orçamento</small>
              <strong class="fs-5"><i class="bi <?= $statusOrcIcone ?> me-1"></i> <?= htmlspecialchars($statusOrcTitulo, ENT_QUOTES, 'UTF-8') ?></strong>
              <small class="text-secondary d-block"><?= htmlspecialchars($statusOrcSub, ENT_QUOTES, 'UTF-8') ?></small>
            </div>
          </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100"><div class="card-body d-flex">
            <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-folder-fill fs-4"></i></span>
            <div class="lh-sm">
              <small class="d-block">Projeto atual</small>
              <strong class="fs-5"><?= htmlspecialchars($nomeProjExibir, ENT_QUOTES, 'UTF-8') ?></strong>
              <small class="text-secondary d-block"><?= htmlspecialchars($codProjExibir, ENT_QUOTES, 'UTF-8') ?></small>
            </div>
          </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100"><div class="card-body d-flex">
            <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-clock-history fs-4"></i></span>
            <div class="lh-sm">
              <small class="d-block">Etapa atual</small>
              <strong class="fs-6"><?= htmlspecialchars($etapaExibir, ENT_QUOTES, 'UTF-8') ?></strong>
              <small class="text-secondary d-block"><?= htmlspecialchars($subEtapaExibir, ENT_QUOTES, 'UTF-8') ?></small>
            </div>
          </div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card h-100"><div class="card-body d-flex">
            <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-bar-chart-fill fs-4"></i></span>
            <div class="flex-grow-1">
              <small class="d-block">Progresso geral</small>
              <strong class="fs-4"><?= $progressoAtual ?>%</strong>
              <div class="progress mt-1 barra-progresso-fina" role="progressbar" aria-label="Progresso geral" aria-valuenow="<?= $progressoAtual ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar <?= $classeProgresso ?>"></div>
              </div>
            </div>
          </div></div>
        </div>
      </div>

      <div class="row g-3 mb-3">

        <!-- Coluna da esquerda -->
        <div class="col-lg-8">

          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 fw-bold mb-0"><?= ($temProjeto || $temSolicitacao) ? 'Seu projeto em andamento' : 'Comece o seu projeto' ?></h2>
                <a href="<?= ($temProjeto || $temSolicitacao) ? 'projeto' : 'questionario' ?>" class="small text-decoration-none"><?= ($temProjeto || $temSolicitacao) ? 'Ver projeto' : 'Preencher questionário' ?> <i class="bi bi-arrow-right"></i></a>
              </div>
              <small class="text-secondary d-block mb-3"><?= ($temProjeto || $temSolicitacao) ? 'Confira os detalhes e o progresso do seu projeto.' : 'Você ainda não enviou um questionário de projeto.' ?></small>

              <div class="row g-3 align-items-center">
                <div class="col-md-5">
                  <div class="bg-primary-subtle rounded d-flex align-items-center justify-content-center py-5">
                    <i class="bi bi-laptop display-1 text-primary"></i>
                  </div>
                </div>
                <div class="col-md-7">
                  <h3 class="h5 fw-bold mb-0"><?= htmlspecialchars($nomeProjExibir, ENT_QUOTES, 'UTF-8') ?></h3>
                  <small class="text-secondary d-block mb-2"><?= htmlspecialchars($codProjExibir, ENT_QUOTES, 'UTF-8') ?></small>
                  <p class="small text-secondary"><?= htmlspecialchars($descProjExibir, ENT_QUOTES, 'UTF-8') ?></p>
                  <div class="row small">
                    <div class="col-6 d-flex align-items-center">
                      <i class="bi bi-calendar-event text-primary fs-4 me-2"></i>
                      <div class="lh-sm"><span class="text-secondary d-block">Data de início</span><?= htmlspecialchars($dataInicioExib, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                      <i class="bi bi-people text-primary fs-4 me-2"></i>
                      <div class="lh-sm"><span class="text-secondary d-block">Equipe responsável</span>Time iiKliente</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Proxima acao -->
          <div class="card">
            <div class="card-body">
              <div class="row g-3 align-items-center">
                <?php if (!$temProjeto && !$temSolicitacao): ?>
                  <div class="col-md-8 d-flex">
                    <span class="icone-caixa rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-clipboard-check fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block">Preencher o Questionário de Orçamento</strong>
                      <small class="text-secondary">Conte para nossa equipe qual tipo de sistema ou site você precisa para receber sua proposta personalizada.</small>
                    </div>
                  </div>
                  <div class="col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="questionario" class="btn btn-primary btn-sm">Preencher questionário</a>
                  </div>
                <?php elseif (!$temProjeto && $valorOrcamentoNum > 0): ?>
                  <div class="col-md-8 d-flex">
                    <span class="icone-caixa rounded bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-check fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block text-success">Aprovar o Orçamento Enviado</strong>
                      <small class="text-secondary">Nossa equipe já preparou a proposta do seu projeto no valor de <strong>R$ <?= number_format($valorOrcamentoNum, 2, ',', '.') ?></strong>. Clique em aprovar para iniciarmos!</small>
                    </div>
                  </div>
                  <div class="col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                    <form action="orcamento-aprovar" method="post" class="d-inline">
                      <?= $campoCsrf ?? '' ?>
                      <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i> Aprovar orçamento</button>
                    </form>
                    <a href="questionario" class="btn btn-outline-primary btn-sm">Ver proposta</a>
                  </div>
                <?php elseif (!empty($projeto['ultimo_ajuste'])): ?>
                  <div class="col-md-8 d-flex">
                    <span class="icone-caixa rounded bg-danger-subtle text-danger d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-pencil-square fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block text-danger">Ajuste Solicitado em Análise</strong>
                      <small class="text-secondary">Nossa equipe está aplicando as correções solicitadas. Você receberá o novo mockup atualizado em breve.</small>
                    </div>
                  </div>
                  <div class="col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="projeto" class="btn btn-outline-danger btn-sm">Ver andamento</a>
                  </div>
                <?php elseif ($progressoAtual >= 100): ?>
                  <div class="col-md-7 d-flex">
                    <span class="icone-caixa rounded bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-trophy fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block text-success">Projeto 100% Concluído e Entregue! 🎉</strong>
                      <small class="text-secondary">Todas as fases foram finalizadas. Baixe as entregas finais e deixe sua avaliação do atendimento.</small>
                    </div>
                  </div>
                  <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                    <?php if (!empty($projeto['link_producao'])): ?>
                      <a href="<?= htmlspecialchars($projeto['link_producao'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i> Acessar no ar</a>
                    <?php endif; ?>
                    <a href="projeto#painel-etapa-100" class="btn btn-success btn-sm"><i class="bi bi-star me-1"></i> Avaliar projeto</a>
                  </div>
                <?php elseif ($progressoAtual === 80): ?>
                  <div class="col-md-7 d-flex">
                    <span class="icone-caixa rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check2-square fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block">Testar e Homologar o Sistema (80%)</strong>
                      <small class="text-secondary">O sistema está disponível em ambiente de testes. Navegue e valide as funcionalidades para aprovar a entrega final.</small>
                    </div>
                  </div>
                  <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                    <?php if (!empty($projeto['link_homologacao'])): ?>
                      <a href="<?= htmlspecialchars($projeto['link_homologacao'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-warning btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i> Abrir Staging</a>
                    <?php endif; ?>
                    <a href="projeto#painel-etapa-80" class="btn btn-warning btn-sm text-dark">Realizar testes</a>
                  </div>
                <?php elseif ($progressoAtual === 60): ?>
                  <div class="col-md-7 d-flex">
                    <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-code-slash fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block">Acompanhar Desenvolvimento (60%)</strong>
                      <small class="text-secondary">Nossa equipe está codificando os módulos e regras de negócio. Caso tenha credenciais de APIs externas, você pode nos enviar.</small>
                    </div>
                  </div>
                  <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="projeto#painel-etapa-60" class="btn btn-primary btn-sm">Ver status dos módulos</a>
                  </div>
                <?php elseif ($progressoAtual === 40): ?>
                  <div class="col-md-7 d-flex">
                    <span class="icone-caixa rounded bg-info-subtle text-info d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-palette fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block">Avaliar Protótipo e Wireframes (40%)</strong>
                      <small class="text-secondary">Nossa equipe desenhou as telas no Figma. Acesse Meu Projeto para interagir com o protótipo e aprovar ou solicitar ajustes.</small>
                    </div>
                  </div>
                  <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                    <?php if (!empty($projeto['link_prototipo'])): ?>
                      <a href="<?= htmlspecialchars($projeto['link_prototipo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i> Abrir Protótipo</a>
                    <?php endif; ?>
                    <a href="projeto#painel-etapa-40" class="btn btn-info btn-sm text-white">Ver mockups e aprovar</a>
                  </div>
                <?php else: ?>
                  <div class="col-md-7 d-flex">
                    <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-arrow-up fs-3"></i></span>
                    <div>
                      <h2 class="h5 fw-bold mb-0">Próxima ação para você</h2>
                      <strong class="d-block">Checklist e Envio de Materiais (20%)</strong>
                      <small class="text-secondary">Envie seus logos, fotos e textos institucionais para que nossa equipe desenhe as telas da sua solução.</small>
                    </div>
                  </div>
                  <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                    <a href="projeto#painel-etapa-20" class="btn btn-primary btn-sm">Preencher checklist</a>
                    <a href="projeto#enviar-arquivo" class="btn btn-outline-primary btn-sm">Enviar arquivos</a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

        </div>

        <!-- Coluna da direita -->
        <div class="col-lg-4">

          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Orçamento oficial</h2>
                <a href="questionario" class="small text-decoration-none">Ver detalhes <i class="bi bi-arrow-right"></i></a>
              </div>
              <div class="d-flex align-items-center mb-3">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-file-earmark-text-fill fs-4"></i></span>
                <div class="flex-grow-1 lh-sm">
                  <small class="d-block"><?= ($temProjeto || $temSolicitacao) ? 'Orçamento ' . htmlspecialchars($codProjExibir, ENT_QUOTES, 'UTF-8') : 'Ainda não solicitado' ?></small>
                  <strong class="fs-4"><?= htmlspecialchars($valorOrcExibir, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <span class="badge <?= $badgeOrcClasse ?>"><?= htmlspecialchars($statusOrcTitulo, ENT_QUOTES, 'UTF-8') ?></span>
              </div>
              <ul class="list-unstyled small mb-0">
                <li class="d-flex justify-content-between mb-1"><span class="text-secondary"><i class="bi bi-send me-2"></i> Solicitação</span> <?= htmlspecialchars($dataInicioExib, ENT_QUOTES, 'UTF-8') ?></li>
                <li class="d-flex justify-content-between"><span class="text-secondary"><i class="bi bi-check-circle me-2"></i> Situação</span> <?= htmlspecialchars($statusOrcSub, ENT_QUOTES, 'UTF-8') ?></li>
              </ul>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Prazo de entrega</h2>
                <a href="projeto" class="small text-decoration-none">Ver cronograma <i class="bi bi-arrow-right"></i></a>
              </div>
              <div class="d-flex align-items-center mb-3">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0"><i class="bi bi-calendar-check fs-4"></i></span>
                <div class="flex-grow-1 lh-sm">
                  <small class="d-block">Previsão de conclusão</small>
                  <strong class="fs-5"><?= htmlspecialchars($prazoEntregaExib, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <span class="badge <?= ($temProjeto || $temSolicitacao) ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>"><?= ($temProjeto || $temSolicitacao) ? 'Em dia' : 'Pendente' ?></span>
              </div>
              <p class="small mb-0"><span class="text-secondary"><i class="bi bi-hourglass-split me-2"></i> Status do prazo:</span> <strong class="ms-1"><?= ($temProjeto || $temSolicitacao) ? 'Dentro do cronograma' : 'Aguardando início' ?></strong></p>
            </div>
          </div>

        </div>
      </div>

      <div class="row g-3">

        <!-- Ultimas atualizacoes -->
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Últimas atualizações</h2>
                <a href="projeto" class="small text-decoration-none">Ver todas <i class="bi bi-arrow-right"></i></a>
              </div>
              <ul class="list-group list-group-flush">
              <?php if (!$temProjeto && !$temSolicitacao): ?>
                <li class="list-group-item px-0 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-person-check"></i></span>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Conta criada com sucesso</strong><small class="text-secondary">Bem-vindo(a)! Preencha o questionário na aba "Orçamento / Questionário" para iniciar.</small></div>
                  <small class="text-secondary text-end ms-2">Hoje</small>
                </li>
              <?php elseif ($progressoAtual >= 60): ?>
                <li class="list-group-item px-0 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text"></i></span>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Etapa: <?= htmlspecialchars($etapaExibir, ENT_QUOTES, 'UTF-8') ?></strong><small class="text-secondary">Nossa equipe está trabalhando nas entregas do projeto <?= htmlspecialchars($nomeProjExibir, ENT_QUOTES, 'UTF-8') ?>.</small></div>
                  <small class="text-secondary text-end ms-2"><?= htmlspecialchars($dataInicioExib, ENT_QUOTES, 'UTF-8') ?></small>
                </li>
                <li class="list-group-item px-0 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-lg"></i></span>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Orçamento aprovado</strong><small class="text-secondary">O orçamento foi aprovado e o projeto foi iniciado.</small></div>
                  <small class="text-secondary text-end ms-2"><?= htmlspecialchars($dataInicioExib, ENT_QUOTES, 'UTF-8') ?></small>
                </li>
              <?php else: ?>
                <li class="list-group-item px-0 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text"></i></span>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Questionário enviado</strong><small class="text-secondary">Recebemos suas respostas para o projeto <?= htmlspecialchars($nomeProjExibir, ENT_QUOTES, 'UTF-8') ?>.</small></div>
                  <small class="text-secondary text-end ms-2"><?= htmlspecialchars($dataInicioExib, ENT_QUOTES, 'UTF-8') ?></small>
                </li>
              <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Linha do tempo -->
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Linha do tempo</h2>
                <a href="projeto" class="small text-decoration-none">Ver cronograma <i class="bi bi-arrow-right"></i></a>
              </div>
              <ul class="list-unstyled mb-0">
                <li class="d-flex align-items-center mb-3">
                  <i class="bi <?= ($temProjeto || $temSolicitacao) ? 'bi-check-circle-fill text-success' : 'bi-record-circle-fill text-warning' ?> fs-4 me-3"></i>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Envio do questionário</strong><small class="text-secondary"><?= ($temProjeto || $temSolicitacao) ? 'Enviado em ' . htmlspecialchars($dataInicioExib, ENT_QUOTES, 'UTF-8') : 'Aguardando preenchimento' ?></small></div>
                  <span class="badge <?= ($temProjeto || $temSolicitacao) ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' ?>"><?= ($temProjeto || $temSolicitacao) ? 'Concluído' : 'Pendente' ?></span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="bi <?= $progressoAtual >= 40 ? 'bi-check-circle-fill text-success' : (($temProjeto || $temSolicitacao) ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary') ?> fs-4 me-3"></i>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Análise e Orçamento</strong><small class="text-secondary"><?= htmlspecialchars($statusOrcTitulo, ENT_QUOTES, 'UTF-8') ?></small></div>
                  <span class="badge <?= $progressoAtual >= 40 ? 'bg-success-subtle text-success-emphasis' : (($temProjeto || $temSolicitacao) ? 'bg-primary-subtle text-primary-emphasis' : 'bg-secondary-subtle text-secondary-emphasis') ?>"><?= $progressoAtual >= 40 ? 'Concluído' : (($temProjeto || $temSolicitacao) ? 'Em andamento' : 'Pendente') ?></span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="bi <?= $progressoAtual >= 60 ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary' ?> fs-4 me-3"></i>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Desenvolvimento</strong><small class="text-secondary"><?= $progressoAtual >= 60 ? 'Em execução pela equipe' : 'Após aprovação do orçamento' ?></small></div>
                  <span class="badge <?= $progressoAtual >= 60 ? 'bg-primary-subtle text-primary-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>"><?= $progressoAtual >= 60 ? 'Em andamento' : 'Pendente' ?></span>
                </li>
                <li class="d-flex align-items-center mb-3">
                  <i class="bi <?= $progressoAtual >= 80 ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary' ?> fs-4 me-3"></i>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Revisão do cliente</strong><small class="text-secondary">Validação das entregas</small></div>
                  <span class="badge <?= $progressoAtual >= 80 ? 'bg-primary-subtle text-primary-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>"><?= $progressoAtual >= 80 ? 'Em revisão' : 'Pendente' ?></span>
                </li>
                <li class="d-flex align-items-center">
                  <i class="bi <?= $progressoAtual >= 100 ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' ?> fs-4 me-3"></i>
                  <div class="flex-grow-1 lh-sm"><strong class="small d-block">Entrega final</strong><small class="text-secondary">Previsão: <?= htmlspecialchars($prazoEntregaExib, ENT_QUOTES, 'UTF-8') ?></small></div>
                  <span class="badge <?= $progressoAtual >= 100 ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>"><?= $progressoAtual >= 100 ? 'Concluído' : 'Pendente' ?></span>
                </li>
              </ul>
            </div>
          </div>
        </div>

      </div>


      <!-- Modal: instrucoes -->
      <div class="modal fade" id="modalInstrucoes" tabindex="-1" aria-labelledby="tituloInstrucoes" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title h5" id="tituloInstrucoes">Como enviar o conteúdo</h3>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              <ol class="mb-0">
                <li>Reúna os textos de cada página do site (Início, Sobre, Serviços, Contato).</li>
                <li>Separe as imagens e o logotipo da empresa (PNG ou JPG).</li>
                <li>Junte tudo em um arquivo .zip (até 5 MB).</li>
                <li>Na página <strong>Meu Projeto</strong>, use o campo "Enviar arquivo".</li>
              </ol>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
              <a href="projeto#enviar-arquivo" class="btn btn-primary">Ir para o envio</a>
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
