<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Meu Projeto - iiKliente</title>

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
  $letraAvatar = mb_strtoupper(mb_substr($nomeUsuario, 0, 1));
  $temProjeto = !empty($projeto);
  $temSolicitacao = !empty($ultimaSolicitacao);

  $progressoAtual = $temProjeto ? (int) ($projeto['progresso'] ?? 20) : ($temSolicitacao ? 20 : 0);
  $classeProgresso = $progressoAtual > 0
      ? 'progresso-' . (in_array($progressoAtual, [20, 40, 60, 80, 100], true) ? $progressoAtual : 20)
      : 'w-0';
  $etapaAprovada = !empty($projeto['etapa_aprovada']);

  $nomeProjView  = $projeto['nome'] ?? ($ultimaSolicitacao['tipo_texto'] ?? 'Nenhum projeto iniciado');
  $codProjView   = $projeto['codigo'] ?? ($temSolicitacao ? '#2026-' . str_pad((string) $ultimaSolicitacao['id'], 4, '0', STR_PAD_LEFT) : 'Aguardando briefing');
  $etapaProjView = $projeto['etapa_atual'] ?? ($temSolicitacao ? 'Em análise' : 'Aguardando questionário');
  $descProjView  = $projeto['descricao'] ?? ($temSolicitacao ? trim((string) $ultimaSolicitacao['mensagem'], '"') : 'Preencha o questionário de orçamento para que possamos iniciar o planejamento do seu projeto.');
  $inicioView    = !empty($projeto['data_inicio']) ? date('d/m/Y', strtotime($projeto['data_inicio'])) : ($temSolicitacao ? date('d/m/Y', strtotime($ultimaSolicitacao['criado_em'])) : 'A definir');
  $prazoView     = !empty($projeto['prazo_entrega']) ? date('d/m/Y', strtotime($projeto['prazo_entrega'])) : ($ultimaSolicitacao['prazo'] ?? 'A definir');
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
        <li class="nav-item"><a class="nav-link" href="inicio"><i class="bi bi-house-door-fill me-2"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="projeto"><i class="bi bi-folder-fill me-2"></i> Meu Projeto</a></li>
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

      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-3">
          <li class="breadcrumb-item"><a href="projeto" class="text-decoration-none">Meus Projetos</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($nomeProjView, ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
      </nav>

      <!-- Titulo -->
      <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
          <h1 class="fw-bold"><?= htmlspecialchars($nomeProjView, ENT_QUOTES, 'UTF-8') ?></h1>
          <p class="text-secondary mb-0">Acompanhe o andamento do seu projeto, visualize atualizações, aprove entregas e converse com nossa equipe.</p>
        </div>
        <div class="text-md-end">
          <span class="badge rounded-pill <?= ($temProjeto || $temSolicitacao) ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?> fs-6"><i class="bi bi-circle-fill small me-1"></i> <?= htmlspecialchars($etapaProjView, ENT_QUOTES, 'UTF-8') ?></span>
          <small class="text-secondary d-block mt-2">Código: <?= htmlspecialchars($codProjView, ENT_QUOTES, 'UTF-8') ?></small>
        </div>
      </div>

      <?php
        // Logica do banner dinamico "O que esta acontecendo e proximo passo"
        $temAjustePendente = !empty($projeto['ultimo_ajuste']);
        if ($temAjustePendente) {
            $bannerIcone    = 'bi-pencil-square text-danger';
            $bannerTitulo   = 'Ajuste Solicitado em Análise';
            $bannerBadge    = 'bg-danger-subtle text-danger-emphasis';
            $bannerEtiqueta = 'Equipe trabalhando no ajuste';
            $bannerTexto    = 'Recebemos o seu pedido de ajuste ("' . htmlspecialchars($projeto['ultimo_ajuste'], ENT_QUOTES, 'UTF-8') . '"). Nossa equipe está aplicando as correções solicitadas. Logo você receberá o novo mockup atualizado.';
        } elseif ($progressoAtual >= 100) {
            $bannerIcone    = 'bi-trophy-fill text-success';
            $bannerTitulo   = 'Projeto 100% Concluído e Entregue!';
            $bannerBadge    = 'bg-success-subtle text-success-emphasis';
            $bannerEtiqueta = 'Finalizado com Sucesso';
            $bannerTexto    = 'Parabéns! Todas as etapas foram aprovadas e concluídas. Todos os arquivos finais do projeto estão disponíveis para download na seção de entregas abaixo.';
        } elseif ($progressoAtual === 80) {
            $bannerIcone    = 'bi-check2-circle text-primary';
            $bannerTitulo   = 'Etapa 4 de 5: Testes e Validação Final';
            $bannerBadge    = 'bg-warning-subtle text-warning-emphasis';
            $bannerEtiqueta = 'Sua ação é necessária';
            $bannerTexto    = 'O sistema está montado em ambiente de testes. Navegue pelas páginas, simule cadastros e, se estiver tudo aprovado, clique em "Aprovar Testes e Finalizar Entrega".';
        } elseif ($progressoAtual === 60) {
            $bannerIcone    = 'bi-code-slash text-primary';
            $bannerTitulo   = 'Etapa 3 de 5: Programação e Desenvolvimento';
            $bannerBadge    = 'bg-primary-subtle text-primary-emphasis';
            $bannerEtiqueta = 'Equipe codificando a solução';
            $bannerTexto    = 'Nossa equipe de desenvolvedores está programando todas as telas e integrações que você aprovou na etapa de wireframes. Acompanhe as novidades pelo feed de atualizações.';
        } elseif ($progressoAtual === 40) {
            $bannerIcone    = 'bi-palette text-primary';
            $bannerTitulo   = 'Etapa 2 de 5: Wireframes e Mockups Visuais';
            $bannerBadge    = 'bg-info-subtle text-info-emphasis';
            $bannerEtiqueta = 'Design das telas em andamento';
            $bannerTexto    = 'Nossa equipe de design está estruturando as telas da sua solução. Assim que postarmos os mockups ao lado em "Arquivos e mockups recentes", baixe os arquivos para avaliar. Se gostar, clique em "Aprovar Wireframes e avançar"; se quiser alterações, clique em "Solicitar ajuste".';
        } else {
            $bannerIcone    = 'bi-clipboard-check text-primary';
            $bannerTitulo   = 'Etapa 1 de 5: Levantamento de Requisitos';
            $bannerBadge    = 'bg-warning-subtle text-warning-emphasis';
            $bannerEtiqueta = 'Sua ação é necessária';
            $bannerTexto    = 'Para iniciarmos o design, nos envie os materiais do projeto (logotipo, textos das páginas, fotos) no formulário "Enviar arquivo" abaixo. Quando terminar, aprove a etapa.';
        }
      ?>

      <!-- Card de Orientacao do Proximo Passo -->
      <div class="card mb-4 border-start border-4 border-primary shadow-sm">
        <div class="card-body p-3">
          <div class="d-flex align-items-start gap-3">
            <span class="icone-caixa rounded bg-primary-subtle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
              <i class="bi <?= $bannerIcone ?> fs-4"></i>
            </span>
            <div class="flex-grow-1">
              <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <strong class="fs-6"><?= $bannerTitulo ?></strong>
                <span class="badge <?= $bannerBadge ?>"><?= $bannerEtiqueta ?></span>
              </div>
              <p class="small text-secondary mb-0"><?= $bannerTexto ?></p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm d-none d-md-flex align-items-center gap-1 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalComoFunciona">
              <i class="bi bi-question-circle"></i> Ver guia do processo
            </button>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">

        <!-- Progresso -->
        <div class="col-xl-8">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h2 class="h5 fw-bold mb-0">Progresso do projeto</h2>
                  <small class="text-secondary">Veja em qual etapa seu projeto está e o que vem a seguir.</small>
                </div>
                <div class="text-center"><strong class="fs-3 text-primary lh-1"><?= $progressoAtual ?>%</strong><small class="d-block text-secondary">Concluído</small></div>
              </div>

              <div class="progress mb-4 barra-progresso-fina" role="progressbar" aria-label="Progresso do projeto" aria-valuenow="<?= $progressoAtual ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar <?= $classeProgresso ?>"></div>
              </div>

              <!-- 5 etapas dinamicas de acordo com o progresso -->
              <div class="row text-center g-2 mb-4">
                <div class="col">
                  <?php if ($progressoAtual > 20): ?>
                    <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"><i class="bi bi-check-lg"></i></span>
                    <strong class="d-block small mt-1">Requisitos</strong><small class="text-secondary">Concluído</small>
                  <?php elseif ($progressoAtual === 20): ?>
                    <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold">1</span>
                    <strong class="d-block small mt-1">Requisitos</strong><small class="text-primary">Em andamento</small>
                  <?php else: ?>
                    <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-inline-flex align-items-center justify-content-center fw-bold">1</span>
                    <strong class="d-block small mt-1">Requisitos</strong><small class="text-secondary">Pendente</small>
                  <?php endif; ?>
                </div>
                <div class="col">
                  <?php if ($progressoAtual > 40): ?>
                    <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"><i class="bi bi-check-lg"></i></span>
                    <strong class="d-block small mt-1">Wireframes</strong><small class="text-secondary">Concluído</small>
                  <?php elseif ($progressoAtual === 40): ?>
                    <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold">2</span>
                    <strong class="d-block small mt-1">Wireframes</strong><small class="text-primary">Em andamento</small>
                  <?php else: ?>
                    <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-inline-flex align-items-center justify-content-center fw-bold">2</span>
                    <strong class="d-block small mt-1">Wireframes</strong><small class="text-secondary">Pendente</small>
                  <?php endif; ?>
                </div>
                <div class="col">
                  <?php if ($progressoAtual > 60): ?>
                    <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"><i class="bi bi-check-lg"></i></span>
                    <strong class="d-block small mt-1">Desenvolvimento</strong><small class="text-secondary">Concluído</small>
                  <?php elseif ($progressoAtual === 60): ?>
                    <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold">3</span>
                    <strong class="d-block small mt-1">Desenvolvimento</strong><small class="text-primary">Em andamento</small>
                  <?php else: ?>
                    <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-inline-flex align-items-center justify-content-center fw-bold">3</span>
                    <strong class="d-block small mt-1">Desenvolvimento</strong><small class="text-secondary">Pendente</small>
                  <?php endif; ?>
                </div>
                <div class="col">
                  <?php if ($progressoAtual > 80): ?>
                    <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"><i class="bi bi-check-lg"></i></span>
                    <strong class="d-block small mt-1">Testes</strong><small class="text-secondary">Concluído</small>
                  <?php elseif ($progressoAtual === 80): ?>
                    <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold">4</span>
                    <strong class="d-block small mt-1">Testes</strong><small class="text-primary">Em andamento</small>
                  <?php else: ?>
                    <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-inline-flex align-items-center justify-content-center fw-bold">4</span>
                    <strong class="d-block small mt-1">Testes</strong><small class="text-secondary">Pendente</small>
                  <?php endif; ?>
                </div>
                <div class="col">
                  <?php if ($progressoAtual >= 100): ?>
                    <span class="avatar rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"><i class="bi bi-check-lg"></i></span>
                    <strong class="d-block small mt-1">Entrega</strong><small class="text-secondary">Concluído</small>
                  <?php else: ?>
                    <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-inline-flex align-items-center justify-content-center fw-bold">5</span>
                    <strong class="d-block small mt-1">Entrega</strong><small class="text-secondary">Pendente</small>
                  <?php endif; ?>
                </div>
              </div>

              <div class="row g-3 border-top pt-3 small">
                <div class="col-md-4 d-flex align-items-center">
                  <i class="bi bi-calendar-plus text-primary fs-3 me-2"></i>
                  <div class="lh-sm"><span class="text-secondary d-block">Prazo previsto</span><strong><?= htmlspecialchars($prazoView, ENT_QUOTES, 'UTF-8') ?></strong><br><span class="text-secondary">Cronograma do projeto</span></div>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                  <i class="bi bi-people text-primary fs-3 me-2"></i>
                  <div class="lh-sm"><span class="text-secondary d-block">Equipe responsável</span><strong>Equipe iiKliente</strong><br><span class="text-secondary">Designer, desenvolvedores e QA</span></div>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                  <i class="bi bi-tag text-primary fs-3 me-2"></i>
                  <div class="lh-sm"><span class="text-secondary d-block">Tipo de projeto</span><strong><?= htmlspecialchars($nomeProjView, ENT_QUOTES, 'UTF-8') ?></strong><br><span class="text-secondary"><?= htmlspecialchars($codProjView, ENT_QUOTES, 'UTF-8') ?></span></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sobre o projeto -->
        <div class="col-xl-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h5 fw-bold mb-0">Sobre o projeto</h2>
                <a href="questionario" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i> <?= ($temProjeto || $temSolicitacao) ? 'Editar' : 'Iniciar' ?></a>
              </div>
              <p class="small text-secondary"><?= htmlspecialchars($descProjView, ENT_QUOTES, 'UTF-8') ?></p>
              <dl class="row small mb-0">
                <dt class="col-5 text-secondary fw-normal"><i class="bi bi-person me-1"></i> Cliente</dt><dd class="col-7"><?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?></dd>
                <dt class="col-5 text-secondary fw-normal"><i class="bi bi-calendar me-1"></i> Data de início</dt><dd class="col-7"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></dd>
                <dt class="col-5 text-secondary fw-normal"><i class="bi bi-calendar-check me-1"></i> Prazo previsto</dt><dd class="col-7"><?= htmlspecialchars($prazoView, ENT_QUOTES, 'UTF-8') ?></dd>
                <dt class="col-5 text-secondary fw-normal"><i class="bi bi-circle me-1"></i> Status</dt><dd class="col-7"><i class="bi bi-circle-fill <?= ($temProjeto || $temSolicitacao) ? 'text-success' : 'text-secondary' ?> small me-1"></i> <?= htmlspecialchars($etapaProjView, ENT_QUOTES, 'UTF-8') ?></dd>
                <dt class="col-5 text-secondary fw-normal"><i class="bi bi-folder me-1"></i> Categoria</dt><dd class="col-7 mb-0"><?= htmlspecialchars($nomeProjView, ENT_QUOTES, 'UTF-8') ?></dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= PAINEL DINÂMICO ESPECÍFICO DA ETAPA ATUAL (CICLO EM 5 ETAPAS) ================= -->
      <?php if ($progressoAtual === 20 || (!$temProjeto && $temSolicitacao)): ?>
        <!-- ETAPA 1: 20% - LEVANTAMENTO DE REQUISITOS E MATERIAIS -->
        <div class="card mb-4 border-primary shadow-sm" id="painel-etapa-20">
          <div class="card-header bg-primary-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-primary-emphasis"><i class="bi bi-clipboard-check text-primary me-2"></i> Etapa 1: Materiais do Projeto e Levantamento de Requisitos</h2>
              <small class="text-secondary">Envie os materiais essenciais para que nossa equipe monte o layout e os wireframes.</small>
            </div>
            <span class="badge bg-primary text-white">Etapa 1 de 5</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <div class="col-lg-7">
                <h3 class="h6 fw-bold mb-2">Checklist de Materiais a Enviar</h3>
                <p class="small text-secondary mb-3">Marque abaixo os materiais que você já separou e clique em salvar. Você pode anexar os arquivos diretamente no formulário abaixo.</p>

                <form action="checklist-requisitos" method="post">
                  <?= $campoCsrf ?? '' ?>
                  <div class="list-group mb-3">
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="logo" <?= in_array('logo', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Logotipo da Empresa</strong>
                        <span class="text-secondary">Arquivo em alta resolução (.PNG com fundo transparente, .SVG, .AI ou .PDF).</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="cores" <?= in_array('cores', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Identidade Visual e Cores</strong>
                        <span class="text-secondary">Manual de marca, paleta de cores preferida ou links de sites referência.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="textos" <?= in_array('textos', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Textos Institucionais</strong>
                        <span class="text-secondary">Quem Somos, Serviços, Produtos, Políticas, Endereço e Contatos.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="fotos" <?= in_array('fotos', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Fotos e Imagens Reais</strong>
                        <span class="text-secondary">Fotos dos seus produtos, instalações ou equipe em boa qualidade.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="dominio" <?= in_array('dominio', $checklistReq, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Definição do Domínio</strong>
                        <span class="text-secondary">Nome do domínio desejado (ex: suaempresa.com.br) ou dados do registro.</span>
                      </span>
                    </label>
                  </div>
                  <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-floppy me-1"></i> Salvar meu checklist</button>
                    <a href="#enviar-arquivo" class="btn btn-primary btn-sm"><i class="bi bi-cloud-arrow-up me-1"></i> Enviar arquivos agora</a>
                  </div>
                </form>
              </div>

              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-info-circle text-primary me-1"></i> Como funciona esta etapa?</h3>
                    <p class="small text-secondary mb-3">Nesta fase inicial, quanto mais materiais e referências você nos enviar, mais rápido e alinhado será o desenho das telas da sua solução.</p>
                    <ul class="small text-secondary ps-3 mb-3">
                      <li>Você pode enviar múltiplos arquivos (.pdf, .png, .jpg, .zip, .docx).</li>
                      <li>Nossa equipe analisa cada arquivo e confirma o recebimento.</li>
                      <li>Após nos enviar o material, aprove esta etapa no card abaixo para avançarmos para os wireframes.</li>
                    </ul>
                  </div>
                  <div class="border-top pt-2">
                    <span class="badge bg-light text-secondary border"><i class="bi bi-paperclip me-1"></i> <?= count($arquivos ?? []) ?> arquivo(s) anexado(s)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progressoAtual === 40): ?>
        <!-- ETAPA 2: 40% - WIREFRAMES E PROTÓTIPO DE DESIGN -->
        <div class="card mb-4 border-info shadow-sm" id="painel-etapa-40">
          <div class="card-header bg-info-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-info-emphasis"><i class="bi bi-palette text-info me-2"></i> Etapa 2: Wireframes e Protótipo de Design</h2>
              <small class="text-secondary">Valide a estrutura visual, distribuição dos elementos e navegação das telas.</small>
            </div>
            <span class="badge bg-info text-white">Etapa 2 de 5</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <div class="col-lg-7">
                <h3 class="h6 fw-bold mb-2">Protótipo Interativo das Telas</h3>
                <?php if (!empty($projeto['link_prototipo'])): ?>
                  <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex align-items-start gap-3">
                      <span class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="bi bi-laptop fs-5"></i>
                      </span>
                      <div class="flex-grow-1">
                        <strong class="d-block mb-1">Navegação Interativa no Figma / Web</strong>
                        <p class="small text-secondary mb-2">Você pode clicar nos botões, alternar telas e testar a usabilidade diretamente no navegador.</p>
                        <a href="<?= htmlspecialchars($projeto['link_prototipo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm px-3">
                          <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Protótipo Interativo
                        </a>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="p-3 bg-light rounded border text-secondary small mb-3">
                    <i class="bi bi-hourglass-split text-info me-1"></i> <strong>Design em produção:</strong> Nossa equipe está finalizando os wireframes no Figma. Assim que o link interativo for disponibilizado, o botão de acesso aparecerá aqui.
                  </div>
                <?php endif; ?>

                <h3 class="h6 fw-bold mb-2">Mockups e Telas Anexadas pela Equipe</h3>
                <?php
                  $mockupsEquipe = array_filter($arquivos ?? [], fn($a) => ($a['usuario_perfil'] ?? '') === 'empresa' || str_contains(strtolower($a['categoria'] ?? ''), 'wireframe'));
                ?>
                <?php if (!empty($mockupsEquipe)): ?>
                  <div class="list-group list-group-flush border rounded mb-3">
                    <?php foreach ($mockupsEquipe as $m): ?>
                      <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div class="lh-sm">
                          <strong class="small d-block"><?= htmlspecialchars($m['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <small class="text-secondary"><?= htmlspecialchars($m['nome_original'], ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                        <a href="arquivo-baixar?id=<?= (int) $m['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i> Baixar</a>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="small text-secondary mb-3">Nenhum arquivo de mockup anexado ainda. Você também pode conferir a lista geral de arquivos ao lado.</p>
                <?php endif; ?>
              </div>

              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100">
                  <h3 class="h6 fw-bold mb-2"><i class="bi bi-lightbulb text-info me-1"></i> Dicas para Avaliar o Protótipo</h3>
                  <ul class="small text-secondary ps-3 mb-3">
                    <li class="mb-2"><strong>Hierarquia Visual:</strong> O conteúdo principal está visível e fácil de localizar?</li>
                    <li class="mb-2"><strong>Fluxo do Usuário:</strong> As ações (comprar, contatar, navegar) estão claras?</li>
                    <li class="mb-2"><strong>Mobile:</strong> Não deixe de conferir como as telas ficam no celular.</li>
                  </ul>
                  <div class="p-2 bg-white rounded border small">
                    <strong class="d-block text-body mb-1">Gostou das telas ou precisa de mudanças?</strong>
                    <span class="text-secondary">Se estiver tudo como planejado, clique em "Aprovar Wireframes" no card abaixo. Se desejar mudanças, clique em "Solicitar ajuste".</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progressoAtual === 60): ?>
        <!-- ETAPA 3: 60% - DESENVOLVIMENTO E PROGRAMAÇÃO -->
        <div class="card mb-4 border-primary shadow-sm" id="painel-etapa-60">
          <div class="card-header bg-primary-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-primary-emphasis"><i class="bi bi-code-slash text-primary me-2"></i> Etapa 3: Programação, Banco de Dados e Integrações</h2>
              <small class="text-secondary">Nossa equipe técnica está codificando a solução aprovada nos wireframes.</small>
            </div>
            <span class="badge bg-primary text-white">Etapa 3 de 5</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <div class="col-lg-7">
                <h3 class="h6 fw-bold mb-3">Status de Construção dos Módulos</h3>
                <div class="d-flex flex-column gap-3 mb-3">
                  <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <strong class="small"><i class="bi bi-window-fullscreen text-primary me-1"></i> Frontend & Telas Responsivas</strong>
                      <span class="badge bg-success-subtle text-success-emphasis">85% Pronto</span>
                    </div>
                    <small class="text-secondary d-block mb-2">Estrutura HTML5, CSS semântico, Bootstrap 5 e adaptação para dispositivos móveis.</small>
                    <div class="progress barra-progresso-fina"><div class="progress-bar bg-success progresso-80"></div></div>
                  </div>

                  <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <strong class="small"><i class="bi bi-server text-primary me-1"></i> Backend, Lógica de Negócio e Rotas</strong>
                      <span class="badge bg-primary-subtle text-primary-emphasis">70% Pronto</span>
                    </div>
                    <small class="text-secondary d-block mb-2">Programação PHP estruturada, rotas limpas, controladores e regras operacionais.</small>
                    <div class="progress barra-progresso-fina"><div class="progress-bar bg-primary progresso-60"></div></div>
                  </div>

                  <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <strong class="small"><i class="bi bi-database text-primary me-1"></i> Banco de Dados e Armazenamento</strong>
                      <span class="badge bg-success-subtle text-success-emphasis">100% Concluído</span>
                    </div>
                    <small class="text-secondary d-block mb-2">Modelagem relacional MySQL, migrations, integridade referencial e sanitização.</small>
                    <div class="progress barra-progresso-fina"><div class="progress-bar bg-success progresso-100"></div></div>
                  </div>

                  <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <strong class="small"><i class="bi bi-plug text-primary me-1"></i> Integrações Externas (Pagamentos, E-mails, APIs)</strong>
                      <span class="badge bg-warning-subtle text-warning-emphasis">60% Pronto</span>
                    </div>
                    <small class="text-secondary d-block mb-2">Conexão com gateways de pagamento, SMTP de e-mail e mensageria.</small>
                    <div class="progress barra-progresso-fina"><div class="progress-bar bg-warning progresso-60"></div></div>
                  </div>
                </div>
              </div>

              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-shield-lock text-primary me-1"></i> Envio Seguro de Credenciais</h3>
                    <p class="small text-secondary mb-3">Se a sua solução depende de credenciais externas (como chave de API de pagamento, credenciais de SMTP para envio de e-mails ou tokens), você pode nos encaminhar de forma segura.</p>
                    <div class="p-2 bg-white rounded border small mb-3">
                      <strong class="d-block text-body mb-1">Como enviar?</strong>
                      <span class="text-secondary">Você pode anexar um documento de texto ou PDF no formulário "Enviar arquivo" abaixo, selecionando a categoria "Credenciais e Acessos Técnicos".</span>
                    </div>
                  </div>
                  <?php if (!empty($projeto['link_homologacao'])): ?>
                    <div class="p-2 bg-success-subtle rounded border border-success small">
                      <strong class="d-block text-success-emphasis mb-1"><i class="bi bi-check-circle me-1"></i> Servidor de teste ativo:</strong>
                      <a href="<?= htmlspecialchars($projeto['link_homologacao'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm w-100"><i class="bi bi-box-arrow-up-right me-1"></i> Visualizar prévia de staging</a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progressoAtual === 80): ?>
        <!-- ETAPA 4: 80% - TESTES E VALIDAÇÃO FINAL (HOMOLOGAÇÃO) -->
        <div class="card mb-4 border-warning shadow-sm" id="painel-etapa-80">
          <div class="card-header bg-warning-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-warning-emphasis"><i class="bi bi-check2-circle text-warning me-2"></i> Etapa 4: Ambiente de Homologação e Testes Finais</h2>
              <small class="text-secondary">Acesse a versão interativa do seu sistema em nosso ambiente de testes e valide cada recurso.</small>
            </div>
            <span class="badge bg-warning text-dark">Etapa 4 de 5</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <div class="col-lg-7">
                <!-- Acesso a Homologacao -->
                <div class="p-3 bg-light rounded border mb-4">
                  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <strong class="text-body"><i class="bi bi-hdd-network text-primary me-1"></i> Servidor de Homologação (Staging)</strong>
                    <?php if (!empty($projeto['link_homologacao'])): ?>
                      <span class="badge bg-success-subtle text-success-emphasis">Online</span>
                    <?php else: ?>
                      <span class="badge bg-secondary-subtle text-secondary-emphasis">Em preparação</span>
                    <?php endif; ?>
                  </div>
                  <?php if (!empty($projeto['link_homologacao'])): ?>
                    <p class="small text-secondary mb-2">Seu sistema está pronto para navegação e simulação no link seguro abaixo:</p>
                    <a href="<?= htmlspecialchars($projeto['link_homologacao'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm mb-3">
                      <i class="bi bi-box-arrow-up-right me-1"></i> Acessar Sistema em Homologação
                    </a>
                  <?php else: ?>
                    <p class="small text-secondary mb-2">Nossa equipe está configurando o banco de dados e os arquivos no servidor de testes. O link de acesso será publicado aqui a qualquer momento.</p>
                  <?php endif; ?>

                  <?php if (!empty($projeto['credenciais_teste'])): ?>
                    <div class="p-2 bg-white rounded border small">
                      <strong class="d-block text-secondary mb-1">Credenciais de teste fornecidas pela equipe:</strong>
                      <span class="font-monospace text-body"><?= htmlspecialchars($projeto['credenciais_teste'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Checklist de Testes do Cliente -->
                <h3 class="h6 fw-bold mb-2">Seu Checklist de Validação</h3>
                <p class="small text-secondary mb-3">Faça os testes práticos abaixo e salve seu progresso. Se encontrar algum erro, use o botão "Solicitar ajuste" para nos avisar.</p>

                <form action="checklist-homologacao" method="post">
                  <?= $campoCsrf ?? '' ?>
                  <div class="list-group mb-3">
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="navegacao" <?= in_array('navegacao', $checklistHomolog, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Navegação em Computador e Celular</strong>
                        <span class="text-secondary">Navegar pelas páginas principais no desktop e no smartphone.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="formularios" <?= in_array('formularios', $checklistHomolog, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Formulários e Cadastros</strong>
                        <span class="text-secondary">Simular envio de contato, cadastro de usuário ou envio de pedidos.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="fluxo_principal" <?= in_array('fluxo_principal', $checklistHomolog, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Fluxo Principal da Solução</strong>
                        <span class="text-secondary">Completar a jornada principal (ex: fechar pedido, agendar ou emitir).</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="textos_contatos" <?= in_array('textos_contatos', $checklistHomolog, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Telefones, E-mails e Redes Sociais</strong>
                        <span class="text-secondary">Conferir se o WhatsApp, telefone e links das redes sociais estão corretos.</span>
                      </span>
                    </label>
                    <label class="list-group-item d-flex align-items-center gap-3">
                      <input class="form-check-input flex-shrink-0" type="checkbox" name="itens[]" value="imagens_layout" <?= in_array('imagens_layout', $checklistHomolog, true) ? 'checked' : '' ?>>
                      <span class="small lh-sm">
                        <strong class="d-block">Fotos e Identidade Visual</strong>
                        <span class="text-secondary">Verificar o alinhamento das imagens, banners e logo da marca.</span>
                      </span>
                    </label>
                  </div>
                  <button type="submit" class="btn btn-outline-warning btn-sm"><i class="bi bi-floppy me-1"></i> Salvar progresso dos testes</button>
                </form>
              </div>

              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="h6 fw-bold mb-2"><i class="bi bi-bug text-danger me-1"></i> Encontrou algum bug ou erro?</h3>
                    <p class="small text-secondary mb-3">Se qualquer tela apresentar comportamento inesperado ou texto incorreto, você pode nos enviar um print e descrever o problema para correção imediata.</p>
                    <div class="p-3 bg-white rounded border mb-3">
                      <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalAjuste">
                        <i class="bi bi-pencil-square me-1"></i> Descrever Correção ou Bug
                      </button>
                      <a href="#enviar-arquivo" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-image me-1"></i> Anexar Prints do Erro
                      </a>
                    </div>
                  </div>
                  <div class="p-2 bg-warning-subtle rounded border border-warning small">
                    <strong class="d-block text-warning-emphasis mb-1">Tudo aprovado nos testes?</strong>
                    <span class="text-secondary">Quando todos os itens estiverem validados, clique no botão "Aprovar Testes e Finalizar Entrega" no card abaixo para publicarmos em produção.</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($progressoAtual >= 100): ?>
        <!-- ETAPA 5: 100% - PROJETO CONCLUÍDO, PRODUÇÃO E AVALIAÇÃO -->
        <div class="card mb-4 border-success shadow-sm" id="painel-etapa-100">
          <div class="card-header bg-success-subtle py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
              <h2 class="h6 fw-bold mb-0 text-success-emphasis"><i class="bi bi-trophy-fill text-success me-2"></i> Etapa 5: Projeto 100% Concluído e Entregue!</h2>
              <small class="text-secondary">Seu projeto foi entregue com total sucesso, testado e publicado em produção.</small>
            </div>
            <span class="badge bg-success text-white">Etapa 5 de 5 · Concluído</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-4">
              <div class="col-lg-7">
                <!-- Botao de Producao -->
                <?php if (!empty($projeto['link_producao'])): ?>
                  <div class="p-3 bg-success-subtle rounded border border-success mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                      <strong class="d-block text-success-emphasis fs-6"><i class="bi bi-globe me-1"></i> Seu Projeto Está no Ar!</strong>
                      <span class="small text-secondary font-monospace"><?= htmlspecialchars($projeto['link_producao'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <a href="<?= htmlspecialchars($projeto['link_producao'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm px-3">
                      <i class="bi bi-box-arrow-up-right me-1"></i> Acessar Projeto Oficial
                    </a>
                  </div>
                <?php endif; ?>

                <!-- Arquivos Finais e Manuais -->
                <h3 class="h6 fw-bold mb-2">Entregas Finais, Códigos e Manuais</h3>
                <?php
                  $entregasFinais = array_filter($arquivos ?? [], fn($a) => str_contains(strtolower($a['categoria'] ?? ''), 'entrega') || str_contains(strtolower($a['categoria'] ?? ''), 'final'));
                ?>
                <?php if (!empty($entregasFinais)): ?>
                  <div class="list-group list-group-flush border rounded mb-3">
                    <?php foreach ($entregasFinais as $ent): ?>
                      <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div class="lh-sm">
                          <strong class="small d-block"><?= htmlspecialchars($ent['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <small class="text-secondary"><?= htmlspecialchars($ent['nome_original'], ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                        <a href="arquivo-baixar?id=<?= (int) $ent['id'] ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i> Baixar</a>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="small text-secondary mb-3">Você pode baixar todos os arquivos trocados durante o projeto na lista geral ao lado.</p>
                <?php endif; ?>

                <!-- Informacoes de Garantia -->
                <div class="p-3 bg-light rounded border">
                  <h4 class="small fw-bold mb-1"><i class="bi bi-shield-check text-success me-1"></i> Garantia e Suporte Pós-Entrega</h4>
                  <p class="small text-secondary mb-0">Seu projeto conta com suporte técnico incluso por 30 dias após a entrega para esclarecer dúvidas de operação ou correções pontuais.</p>
                </div>
              </div>

              <!-- Formulario ou Exibicao de Avaliacao -->
              <div class="col-lg-5">
                <div class="bg-body-tertiary p-3 rounded border h-100">
                  <h3 class="h6 fw-bold mb-2"><i class="bi bi-star-fill text-warning me-1"></i> Avaliação do Cliente</h3>
                  <?php if (!empty($projeto['avaliacao_nota'])): ?>
                    <div class="p-3 bg-white rounded border text-center mb-2">
                      <div class="fs-4 text-warning mb-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                          <i class="bi <?= $i <= (int) $projeto['avaliacao_nota'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                        <?php endfor; ?>
                      </div>
                      <strong class="d-block small text-success mb-2">Avaliação Enviada (Nota <?= (int) $projeto['avaliacao_nota'] ?> de 5)</strong>
                      <?php if (!empty($projeto['avaliacao_comentario'])): ?>
                        <p class="small text-secondary fst-italic mb-0">"<?= htmlspecialchars($projeto['avaliacao_comentario'], ENT_QUOTES, 'UTF-8') ?>"</p>
                      <?php endif; ?>
                    </div>
                    <small class="text-secondary d-block text-center">Muito obrigado pelo seu feedback! Ele nos ajuda a aprimorar sempre nossos serviços.</small>
                  <?php else: ?>
                    <p class="small text-secondary mb-3">Sua opinião é fundamental para nossa equipe! Como foi sua experiência durante as 5 etapas deste projeto?</p>

                    <form action="projeto-avaliar" method="post">
                      <?= $campoCsrf ?? '' ?>
                      <div class="mb-3">
                        <label for="campo-nota" class="form-label small fw-semibold">Sua nota geral *</label>
                        <select name="nota" id="campo-nota" class="form-select form-select-sm" required>
                          <option value="5" selected>⭐⭐⭐⭐⭐ 5 Estrelas — Excelente experiência!</option>
                          <option value="4">⭐⭐⭐⭐ 4 Estrelas — Muito bom</option>
                          <option value="3">⭐⭐⭐ 3 Estrelas — Bom / Regular</option>
                          <option value="2">⭐⭐ 2 Estrelas — Precisa melhorar</option>
                          <option value="1">⭐ 1 Estrela — Abaixo do esperado</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label for="campo-comentario" class="form-label small fw-semibold">Deixe um depoimento ou feedback</label>
                        <textarea name="comentario" id="campo-comentario" rows="3" class="form-control form-control-sm" placeholder="O que você mais gostou no projeto e atendimento?"></textarea>
                      </div>

                      <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-send me-1"></i> Enviar Avaliação
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Aprovacao da etapa -->
      <div class="card mb-3">
        <div class="card-body">
          <div class="row g-3 align-items-center">
            <?php if (!$temProjeto && !$temSolicitacao): ?>
              <div class="col-lg-8 d-flex">
                <span class="icone-caixa rounded bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-clipboard-check fs-4"></i></span>
                <div>
                  <h2 class="h5 fw-bold mb-1">Aprovação da etapa atual <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1 fs-6">Aguardando início</span></h2>
                  <strong class="d-block">Você ainda não enviou o questionário do projeto</strong>
                  <small class="text-secondary">Preencha o questionário na aba "Orçamento / Questionário" para que nossa equipe inicie o seu projeto.</small>
                </div>
              </div>
              <div class="col-lg-4 text-lg-end">
                <a href="questionario" class="btn btn-primary"><i class="bi bi-file-earmark-text me-1"></i> Preencher questionário</a>
              </div>
            <?php elseif ($progressoAtual >= 100): ?>
              <div class="col-lg-8 d-flex">
                <span class="icone-caixa rounded bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-circle-fill fs-4"></i></span>
                <div>
                  <h2 class="h5 fw-bold mb-1 text-success">Projeto 100% Concluído e Entregue! <span class="badge bg-success-subtle text-success-emphasis ms-1 fs-6">Finalizado</span></h2>
                  <strong class="d-block">Todas as 5 etapas foram aprovadas com sucesso.</strong>
                  <small class="text-secondary">Seu projeto foi entregue. Você pode baixar todos os materiais e arquivos finais na seção abaixo.</small>
                </div>
              </div>
              <div class="col-lg-4 text-lg-end">
                <a href="#enviar-arquivo" class="btn btn-outline-success"><i class="bi bi-download me-1"></i> Ver entregas</a>
              </div>
            <?php else: ?>
              <?php
                $textoBtnAprovar = match ($progressoAtual) {
                    20 => 'Aprovar Requisitos e avançar',
                    40 => 'Aprovar Wireframes e avançar',
                    60 => 'Aprovar Desenvolvimento e avançar',
                    80 => 'Aprovar Testes e Finalizar Entrega',
                    default => 'Aprovar etapa',
                };
              ?>
              <div class="col-lg-7 d-flex">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text-fill fs-4"></i></span>
                <div>
                  <h2 class="h5 fw-bold mb-1">Aprovação da etapa atual <span class="badge bg-warning-subtle text-warning-emphasis ms-1 fs-6">Sua validação</span></h2>
                  <strong class="d-block">Fase: <?= htmlspecialchars($etapaProjView, ENT_QUOTES, 'UTF-8') ?></strong>
                  <small class="text-secondary">Revise os arquivos desta etapa. Se estiver tudo certo, aprove para a equipe avançar para a próxima etapa.</small>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                  <form action="etapa-aprovar" method="post" class="d-inline">
                    <?= $campoCsrf ?? '' ?>
                    <button type="submit" class="btn btn-primary" id="btn-aprovar-etapa"><i class="bi bi-check-lg me-1"></i> <?= $textoBtnAprovar ?></button>
                  </form>
                  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalAjuste"><i class="bi bi-pencil-square me-1"></i> Solicitar ajuste</button>
                </div>
                <small class="text-secondary d-block mt-2 text-lg-end"><i class="bi bi-clock me-1"></i> Clique para aprovar ou envie um pedido de ajuste.</small>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">

        <!-- Ultimas atualizacoes -->
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Últimas atualizações</h2>
                <a href="#" class="small text-decoration-none" data-toast="Exibindo todas as atualizações recentes do projeto.">Ver todas <i class="bi bi-arrow-right"></i></a>
              </div>
              <ul class="list-group list-group-flush">
              <?php if (!$temProjeto && !$temSolicitacao): ?>
                <li class="list-group-item px-0 d-flex">
                  <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-flag"></i></span>
                  <div class="lh-sm"><small class="text-secondary">Hoje</small><strong class="small d-block">Painel do cliente liberado</strong><small class="text-secondary">Envie o questionário de orçamento para dar início às etapas do projeto.</small></div>
                </li>
              <?php else: ?>
                <?php if (!empty($projeto['ultimo_ajuste'])): ?>
                  <li class="list-group-item px-0 d-flex">
                    <span class="avatar rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-pencil-square"></i></span>
                    <div class="lh-sm"><small class="text-secondary">Ajuste solicitado</small><strong class="small d-block">Pedido de revisão em análise</strong><small class="text-secondary">"<?= htmlspecialchars($projeto['ultimo_ajuste'], ENT_QUOTES, 'UTF-8') ?>"</small></div>
                  </li>
                <?php endif; ?>

                <?php if ($progressoAtual >= 100): ?>
                  <li class="list-group-item px-0 d-flex">
                    <span class="avatar rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-circle-fill"></i></span>
                    <div class="lh-sm"><small class="text-secondary"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></small><strong class="small d-block text-success">Projeto 100% Concluído e Entregue!</strong><small class="text-secondary">Todas as entregas foram validadas e finalizadas.</small></div>
                  </li>
                <?php endif; ?>

                <?php if ($progressoAtual >= 80): ?>
                  <li class="list-group-item px-0 d-flex">
                    <span class="avatar rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-lg"></i></span>
                    <div class="lh-sm"><small class="text-secondary"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></small><strong class="small d-block">Desenvolvimento aprovado</strong><small class="text-secondary">O código foi aprovado e o sistema avançou para testes.</small></div>
                  </li>
                <?php endif; ?>

                <?php if ($progressoAtual >= 60): ?>
                  <li class="list-group-item px-0 d-flex">
                    <span class="avatar rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-lg"></i></span>
                    <div class="lh-sm"><small class="text-secondary"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></small><strong class="small d-block">Wireframes e telas aprovados</strong><small class="text-secondary">Você validou os mockups das telas do projeto.</small></div>
                  </li>
                <?php endif; ?>

                <?php if ($progressoAtual >= 40): ?>
                  <li class="list-group-item px-0 d-flex">
                    <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-palette"></i></span>
                    <div class="lh-sm"><small class="text-secondary"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></small><strong class="small d-block">Etapa atual: Wireframes e protótipo</strong><small class="text-secondary">Nossa equipe está desenhando as telas e mockups do projeto.</small></div>
                  </li>
                  <li class="list-group-item px-0 d-flex">
                    <span class="avatar rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-check-lg"></i></span>
                    <div class="lh-sm"><small class="text-secondary"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></small><strong class="small d-block">Documento de requisitos aprovado</strong><small class="text-secondary">Você aprovou o levantamento inicial de requisitos.</small></div>
                  </li>
                <?php endif; ?>

                <li class="list-group-item px-0 d-flex">
                  <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-flag"></i></span>
                  <div class="lh-sm"><small class="text-secondary"><?= htmlspecialchars($inicioView, ENT_QUOTES, 'UTF-8') ?></small><strong class="small d-block">Projeto iniciado: <?= htmlspecialchars($nomeProjView, ENT_QUOTES, 'UTF-8') ?></strong><small class="text-secondary">Orçamento aprovado e planejamento em execução.</small></div>
                </li>
              <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Arquivos -->
        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h2 class="h5 fw-bold mb-0">Arquivos e mockups recentes</h2>
                <a href="#enviar-arquivo" class="small text-decoration-none">Enviar novo <i class="bi bi-arrow-right"></i></a>
              </div>
              <small class="text-secondary d-block mb-3">Troca de materiais entre você e a equipe (textos, logos e mockups das telas).</small>

              <ul class="list-group list-group-flush">
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
                    $ehEquipe = ($arq['usuario_perfil'] ?? '') === 'empresa';
                    $badgeOrigem = $ehEquipe
                        ? '<span class="badge bg-primary-subtle text-primary-emphasis"><i class="bi bi-building me-1"></i> Mockup da Equipe</span>'
                        : '<span class="badge bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-person me-1"></i> Enviado por você</span>';
                  ?>
                  <li class="list-group-item px-0 d-flex align-items-center">
                    <i class="bi <?= $iconeArq ?> fs-3 me-3"></i>
                    <div class="flex-grow-1 lh-sm">
                      <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <strong class="small"><?= htmlspecialchars($arq['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <?= $badgeOrigem ?>
                        <?php if (!empty($arq['categoria']) && $arq['categoria'] !== 'Geral'): ?>
                          <span class="badge bg-light text-secondary border"><?= htmlspecialchars($arq['categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                      </div>
                      <small class="text-secondary"><?= strtoupper(htmlspecialchars($ext, ENT_QUOTES, 'UTF-8')) ?> · <?= $tamTexto ?> · <?= date('d/m/Y', strtotime($arq['criado_em'])) ?></small>
                    </div>
                    <a href="arquivo-baixar?id=<?= (int) $arq['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i> Baixar</a>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="list-group-item px-0 text-secondary small py-3">
                  Nenhum arquivo enviado neste projeto ainda. Use o formulário abaixo para enviar materiais ou aguarde os mockups da equipe.
                </li>
              <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

<!-- ================= ITEM 3: MÓDULO FINANCEIRO E PARCELAS DO PROJETO ================= -->
      <div class="card mb-4 border-0 shadow-sm" id="financeiro">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h2 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-cash-coin me-2"></i> Módulo Financeiro e Parcelas</h2>
            <small class="text-secondary">Consulte as parcelas do seu projeto, realize pagamentos via PIX e envie seus comprovantes.</small>
          </div>
          <?php
            $statusFin = $projeto['status_financeiro'] ?? 'pendente';
            $badgeFinClass = ($statusFin === 'pago') ? 'bg-success' : (($statusFin === 'parcial') ? 'bg-info text-dark' : 'bg-warning text-dark');
            $statusFinTexto = ($statusFin === 'pago') ? 'Quitado' : (($statusFin === 'parcial') ? 'Parcialmente Pago' : 'Pendente');
          ?>
          <span class="badge <?= $badgeFinClass ?> px-3 py-2 fs-6">
            <i class="bi bi-wallet2 me-1"></i> <?= $statusFinTexto ?>
          </span>
        </div>
        <div class="card-body p-4">
          <!-- Chave PIX e Instruções -->
          <div class="row g-3 mb-4">
            <div class="col-lg-6">
              <div class="p-3 bg-light rounded border h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <strong class="small text-uppercase"><i class="bi bi-qr-code text-primary me-1"></i> Pagamento via PIX</strong>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Chave <?= htmlspecialchars($projeto['tipo_chave_pix'] ?? 'CNPJ') ?></span>
                </div>
                <?php if (!empty($projeto['chave_pix'])): ?>
                  <div class="input-group mb-2">
                    <input type="text" id="campo-chave-pix" class="form-control form-control-sm font-monospace bg-white" value="<?= htmlspecialchars($projeto['chave_pix']) ?>" readonly>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($projeto['chave_pix'], ENT_QUOTES, 'UTF-8') ?>'); mostrarToast('Chave PIX copiada para a área de transferência!', 'success');">
                      <i class="bi bi-clipboard me-1"></i> Copiar
                    </button>
                  </div>
                  <small class="text-secondary d-block">Após efetuar a transferência via PIX, anexe o comprovante na parcela correspondente abaixo para que nossa equipe valide.</small>
                <?php else: ?>
                  <p class="text-secondary small mb-0">A equipe financeira ainda está configurando a chave PIX para este projeto. Entre em contato com o suporte em caso de dúvidas.</p>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-secondary small">Valor do Projeto Homologado:</span>
                    <strong class="fs-6">R$ <?= number_format((float)($projeto['valor_orcamento'] ?? 0), 2, ',', '.') ?></strong>
                  </div>
                  <?php
                    $totalPago = 0;
                    $totalParcelas = count($parcelas ?? []);
                    if (!empty($parcelas)) {
                        foreach ($parcelas as $p) {
                            if ($p['status'] === 'pago') {
                                $totalPago += (float) $p['valor'];
                            }
                        }
                    }
                    $totalRestante = max(0, (float)($projeto['valor_orcamento'] ?? 0) - $totalPago);
                  ?>
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-success small">Total Já Quitado:</span>
                    <strong class="text-success small">R$ <?= number_format($totalPago, 2, ',', '.') ?></strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small">Saldo Restante:</span>
                    <strong class="text-danger small">R$ <?= number_format($totalRestante, 2, ',', '.') ?></strong>
                  </div>
                </div>
                <div class="mt-2 pt-2 border-top">
                  <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Comprovantes protegidos e auditados pelo sistema.</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabela de Parcelas -->
          <h3 class="h6 fw-bold mb-3"><i class="bi bi-receipt me-1"></i> Cronograma de Parcelas</h3>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr class="small text-uppercase">
                  <th>#</th>
                  <th>Descrição</th>
                  <th>Valor</th>
                  <th>Vencimento</th>
                  <th>Status</th>
                  <th>Comprovante</th>
                  <th class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($parcelas)): ?>
                  <?php foreach ($parcelas as $parc): ?>
                    <tr>
                      <td class="fw-bold"><?= (int)$parc['numero_parcela'] ?></td>
                      <td><?= htmlspecialchars($parc['descricao']) ?></td>
                      <td class="fw-semibold">R$ <?= number_format((float)$parc['valor'], 2, ',', '.') ?></td>
                      <td><?= !empty($parc['data_vencimento']) ? date('d/m/Y', strtotime($parc['data_vencimento'])) : '-' ?></td>
                      <td>
                        <?php if ($parc['status'] === 'pago'): ?>
                          <span class="badge bg-success-subtle text-success border border-success">
                            <i class="bi bi-check-circle-fill me-1"></i> Pago <?= !empty($parc['data_pagamento']) ? '(' . date('d/m/Y', strtotime($parc['data_pagamento'])) . ')' : '' ?>
                          </span>
                        <?php elseif ($parc['status'] === 'em_analise'): ?>
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
                        <?php if (!empty($parc['comprovante'])): ?>
                          <span class="badge bg-light text-secondary border">
                            <i class="bi bi-file-earmark-check text-success me-1"></i> Anexado
                          </span>
                        <?php else: ?>
                          <span class="text-muted small">Não enviado</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <?php if ($parc['status'] !== 'pago'): ?>
                          <button type="button" class="btn btn-outline-primary btn-sm btn-abrir-comprovante" data-bs-toggle="modal" data-bs-target="#modalComprovante" data-parcela-id="<?= (int)$parc['id'] ?>" data-parcela-num="<?= (int)$parc['numero_parcela'] ?>" data-parcela-desc="<?= htmlspecialchars($parc['descricao']) ?>" data-parcela-valor="<?= number_format((float)$parc['valor'], 2, ',', '.') ?>">
                            <i class="bi bi-upload me-1"></i> <?= !empty($parc['comprovante']) ? 'Reenviar Comprovante' : 'Enviar Comprovante' ?>
                          </button>
                        <?php else: ?>
                          <span class="text-success small fw-semibold"><i class="bi bi-check-all"></i> Quitado</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                      Nenhuma parcela configurada no momento. A equipe definirá o cronograma de pagamentos conforme o contrato.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ================= ITEM 1: LINHA DO TEMPO E HISTÓRICO DE AUDITORIA ================= -->
      <div class="card mb-4 border-0 shadow-sm" id="historico">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <div>
            <h2 class="h5 fw-bold mb-0 text-primary"><i class="bi bi-clock-history me-2"></i> Linha do Tempo e Histórico de Aprovações</h2>
            <small class="text-secondary">Registro cronológico detalhado (Audit Trail) de todas as decisões, alterações e validações deste projeto.</small>
          </div>
          <span class="badge bg-secondary-subtle text-secondary border">Total: <?= count($historico ?? []) ?> eventos</span>
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
            <div class="p-3 bg-light rounded text-center text-muted small">
              <i class="bi bi-info-circle me-1"></i> O histórico deste projeto começará a ser registrado a partir das primeiras ações e aprovações.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Modal: Enviar Comprovante de Pagamento da Parcela (Item 3 & Item 6) -->
      <div class="modal fade" id="modalComprovante" tabindex="-1" aria-labelledby="tituloModalComprovante" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="<?= $urlBase ?>/cliente/parcela-comprovante" method="post" enctype="multipart/form-data" class="needs-validation" data-backend="true" novalidate>
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="parcela_id" id="modal-comprovante-parcela-id" value="">

              <div class="modal-header">
                <h3 class="modal-title h5" id="tituloModalComprovante">Enviar Comprovante de Pagamento</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <div class="p-3 bg-light rounded border mb-3 small">
                  <div><strong>Parcela:</strong> <span id="modal-comprovante-parcela-desc">-</span></div>
                  <div><strong>Valor:</strong> R$ <span id="modal-comprovante-parcela-valor">-</span></div>
                </div>

                <div class="mb-3">
                  <label for="comprovante-arquivo" class="form-label fw-semibold small">Arquivo do Comprovante *</label>
                  <input type="file" class="form-control campo-arquivo" id="comprovante-arquivo" name="comprovante" accept=".pdf,.png,.jpg,.jpeg" data-extensoes="pdf,png,jpg,jpeg" data-max-mb="5" required>
                  <div class="form-text small">Formatos aceitos: PDF, PNG ou JPG (até 5 MB). O arquivo é validado por assinatura binária MIME.</div>
                  <div class="invalid-feedback">Selecione o arquivo do comprovante.</div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-upload me-1"></i> Confirmar Envio do Comprovante
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Script de preenchimento do modal de comprovante -->
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          document.querySelectorAll('.btn-abrir-comprovante').forEach(function (btn) {
            btn.addEventListener('click', function () {
              const pid = btn.getAttribute('data-parcela-id');
              const pnum = btn.getAttribute('data-parcela-num');
              const pdesc = btn.getAttribute('data-parcela-desc');
              const pval = btn.getAttribute('data-parcela-valor');

              document.getElementById('modal-comprovante-parcela-id').value = pid;
              document.getElementById('modal-comprovante-parcela-desc').textContent = '#' + pnum + ' - ' + pdesc;
              document.getElementById('modal-comprovante-parcela-valor').textContent = pval;
            });
          });
        });
      </script>

            <!-- Upload de arquivo -->
      <div class="card" id="enviar-arquivo">
        <div class="card-body">
          <h2 class="h5 fw-bold mb-1"><i class="bi bi-cloud-arrow-up text-primary me-2"></i> Enviar arquivo</h2>
          <small class="text-secondary d-block mb-3">Envie textos, imagens ou outros materiais do projeto. Tipos aceitos: PDF, PNG, JPG, DOCX e ZIP (até 5 MB).</small>

          <form class="needs-validation" action="arquivo-enviar" method="post" enctype="multipart/form-data" data-backend="true" data-mensagem="Arquivo enviado com sucesso!" novalidate>
            <?= $campoCsrf ?? '' ?>
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label for="arquivo-upload" class="form-label fw-semibold small">Arquivo *</label>
                <input type="file" class="form-control campo-arquivo" id="arquivo-upload" name="arquivo" accept=".pdf,.png,.jpg,.jpeg,.zip,.docx" data-extensoes="pdf,png,jpg,jpeg,zip,docx" data-max-mb="5" required>
                <div class="invalid-feedback">Escolha um arquivo.</div>
              </div>
              <div class="col-md-3">
                <label for="arquivo-categoria" class="form-label fw-semibold small">Categoria da Entrega *</label>
                <select name="categoria" id="arquivo-categoria" class="form-select form-select-sm" required>
                  <option value="Material de Apoio / Requisitos" <?= ($progressoAtual === 20) ? 'selected' : '' ?>>Material de Apoio / Requisitos</option>
                  <option value="Feedback / Referências de Telas" <?= ($progressoAtual === 40) ? 'selected' : '' ?>>Feedback / Referências de Telas</option>
                  <option value="Credenciais e Acessos Técnicos" <?= ($progressoAtual === 60) ? 'selected' : '' ?>>Credenciais e Acessos Técnicos</option>
                  <option value="Prints de Testes / Relatórios de Bug" <?= ($progressoAtual === 80) ? 'selected' : '' ?>>Prints de Testes / Relatórios de Bug</option>
                  <option value="Documentos Complementares" <?= ($progressoAtual >= 100) ? 'selected' : '' ?>>Documentos Complementares</option>
                  <option value="Geral">Geral</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="arquivo-descricao" class="form-label fw-semibold small">Descrição</label>
                <input type="text" class="form-control form-control-sm" id="arquivo-descricao" name="descricao" maxlength="100" placeholder="Ex: Logo vetorial, prints, etc.">
              </div>
              <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-upload me-1"></i> Enviar</button>
              </div>
            </div>
          </form>
        </div>
      </div>


      <!-- Modal: solicitar ajuste -->
      <div class="modal fade" id="modalAjuste" tabindex="-1" aria-labelledby="tituloAjuste" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form class="needs-validation" action="ajuste-solicitar" method="post" data-backend="true" data-mensagem="Pedido de ajuste enviado para a equipe." novalidate>
              <?= $campoCsrf ?? '' ?>
              <div class="modal-header">
                <h3 class="modal-title h5" id="tituloAjuste">Solicitar ajuste</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <label for="ajuste-texto" class="form-label fw-semibold small">O que precisa ser ajustado? *</label>
                <textarea class="form-control" id="ajuste-texto" name="ajuste" rows="4" minlength="10" maxlength="500" required></textarea>
                <div class="invalid-feedback">Descreva o ajuste (mínimo 10 letras).</div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar pedido</button>
              </div>
            </form>
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
