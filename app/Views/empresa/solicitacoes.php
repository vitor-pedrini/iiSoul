<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Solicitações e Orçamentos - iiKliente</title>

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
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="solicitacoes"><i class="bi bi-file-earmark-text-fill me-2"></i> Solicitações / Orçamentos</a></li>
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
        <option value="todos">Todas as solicitações</option>
        <option value="andamento">Em análise</option>
        <option value="concluidos">Aceitas</option>
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

      <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
          <p class="small text-secondary text-uppercase mb-1">Área da empresa</p>
          <h1 class="fw-bold">Solicitações e Orçamentos</h1>
          <p class="text-secondary mb-0">Gerencie as solicitações recebidas, acompanhe o andamento, envie orçamentos e converta em clientes.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="exportar-csv?tipo=solicitacoes" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV
          </a>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaSolicitacao">
            <i class="bi bi-plus-lg me-1"></i> Nova solicitação
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="card mb-3">
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-5">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" class="form-control" id="busca-tabela" placeholder="Buscar por nome, e-mail ou projeto..." aria-label="Buscar solicitação">
              </div>
            </div>
            <div class="col-6 col-md-3">
              <select class="form-select" id="filtro-status" aria-label="Filtrar por status">
                <option value="todos">Todos os status</option>
                <option value="nova">Nova</option>
                <option value="analise">Em análise</option>
                <option value="orcamento-enviado">Orçamento enviado</option>
                <option value="aceita">Aceita</option>
                <option value="recusada">Recusada</option>
              </select>
            </div>
            <div class="col-6 col-md-3">
              <select class="form-select" id="filtro-tipo" aria-label="Filtrar por tipo de projeto">
                <option value="todos">Todos os tipos de projeto</option>
                <option value="website">Website Institucional</option>
                <option value="app">Aplicativo Mobile</option>
                <option value="sistema">Sistema Interno</option>
                <option value="loja">Loja Virtual</option>
              </select>
            </div>
            <div class="col-md-1 d-grid">
              <button type="button" class="btn btn-outline-secondary" id="btn-limpar-filtros" title="Limpar filtros" aria-label="Limpar filtros"><i class="bi bi-x-lg"></i></button>
            </div>
          </div>
        </div>
      </div>

      <?php
        $badgesSol = [
          'rascunho'          => ['bg-secondary-subtle text-secondary-emphasis', 'Rascunho'],
          'nova'              => ['bg-info-subtle text-info-emphasis', 'Nova'],
          'analise'           => ['bg-warning-subtle text-warning-emphasis', 'Em análise'],
          'orcamento-enviado' => ['bg-primary-subtle text-primary-emphasis', 'Orçamento enviado'],
          'aceita'            => ['bg-success-subtle text-success-emphasis', 'Aceita'],
          'recusada'          => ['bg-danger-subtle text-danger-emphasis', 'Recusada'],
        ];
      ?>

      <div class="row g-3">

        <!-- ========== LISTA ========== -->
        <div class="col-xl-7">
          <div class="card">
            <div class="card-body">
              <p class="fw-bold mb-3"><span id="total-encontrado"><?= count($solicitacoes ?? []) ?></span> solicitações encontradas</p>

              <div class="table-responsive">
                <table class="table table-hover align-middle small" id="tabela-dados">
                  <thead>
                    <tr class="text-secondary">
                      <th><input class="form-check-input" type="checkbox" id="marcar-todos" aria-label="Marcar todas"></th>
                      <th>Nome</th>
                      <th class="d-none d-md-table-cell">Tipo de projeto</th>
                      <th class="d-none d-lg-table-cell">Faixa estimada</th>
                      <th>Status</th>
                      <th class="d-none d-md-table-cell">Data</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach (($solicitacoes ?? []) as $indice => $s): ?>
                    <?php
                      [$classeBadge, $textoBadge] = $badgesSol[$s['status']] ?? ['bg-info-subtle text-info-emphasis', 'Nova'];
                      $dataFormatada = date('d/m/Y', strtotime($s['criado_em']));
                      $ativa = ($indice === 0) ? ' table-active' : '';
                      $valOrcLinha = !empty($s['valor_orcamento']) ? 'R$ ' . number_format((float) $s['valor_orcamento'], 2, ',', '.') : 'Ainda não enviado';
                    ?>
                    <tr class="tabela-linha linha-solicitacao<?= $ativa ?>"
                        data-id="<?= (int) $s['id'] ?>"
                        data-nome="<?= htmlspecialchars($s['nome'], ENT_QUOTES, 'UTF-8') ?>"
                        data-email="<?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?>"
                        data-telefone="<?= htmlspecialchars($s['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        data-tipo="<?= htmlspecialchars($s['tipo'], ENT_QUOTES, 'UTF-8') ?>"
                        data-tipo-texto="<?= htmlspecialchars($s['tipo_texto'], ENT_QUOTES, 'UTF-8') ?>"
                        data-faixa="<?= htmlspecialchars($s['faixa'], ENT_QUOTES, 'UTF-8') ?>"
                        data-prazo="<?= htmlspecialchars($s['prazo'] ?? '1 a 3 meses', ENT_QUOTES, 'UTF-8') ?>"
                        data-objetivo="<?= htmlspecialchars($s['objetivo'] ?? 'Não informado', ENT_QUOTES, 'UTF-8') ?>"
                        data-publico="<?= htmlspecialchars($s['publico_alvo'] ?? 'Não informado', ENT_QUOTES, 'UTF-8') ?>"
                        data-modulos="<?= htmlspecialchars($s['modulos'] ?? 'Não informado', ENT_QUOTES, 'UTF-8') ?>"
                        data-referencias="<?= htmlspecialchars($s['referencias'] ?: 'Sem referências informadas', ENT_QUOTES, 'UTF-8') ?>"
                        data-anotacao="<?= htmlspecialchars($s['anotacao_interna'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        data-orcamento="<?= htmlspecialchars($valOrcLinha, ENT_QUOTES, 'UTF-8') ?>"
                        data-status="<?= htmlspecialchars($s['status'], ENT_QUOTES, 'UTF-8') ?>"
                        data-data="<?= $dataFormatada ?>"
                        data-mensagem="<?= htmlspecialchars($s['mensagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      <td><input class="form-check-input marcar-linha" type="checkbox" aria-label="Marcar <?= htmlspecialchars($s['nome'], ENT_QUOTES, 'UTF-8') ?>"></td>
                      <td class="lh-sm"><strong class="d-block"><?= htmlspecialchars($s['nome'], ENT_QUOTES, 'UTF-8') ?></strong><span class="text-secondary"><?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?></span></td>
                      <td class="d-none d-md-table-cell"><?= htmlspecialchars($s['tipo_texto'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="d-none d-lg-table-cell"><?= htmlspecialchars($s['faixa'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><span class="badge rounded-pill <?= $classeBadge ?>"><?= $textoBadge ?></span></td>
                      <td class="d-none d-md-table-cell"><?= $dataFormatada ?></td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div id="msg-vazio" class="text-center text-secondary py-4 d-none">
                <i class="bi bi-search fs-2 d-block"></i>
                Nenhuma solicitação encontrada com esses filtros.
              </div>

              <nav class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3" aria-label="Paginação">
                <small class="text-secondary">Mostrando 1 a <?= count($solicitacoes ?? []) ?> de <?= count($solicitacoes ?? []) ?> resultados</small>
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item disabled"><a class="page-link" href="#" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a></li>
                  <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
                  <li class="page-item disabled"><a class="page-link" href="#" aria-label="Próxima"><i class="bi bi-chevron-right"></i></a></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>

        <!-- ========== DETALHES DA SOLICITACAO SELECIONADA ========== -->
        <?php
          $primeiraSol = ($solicitacoes ?? [])[0] ?? [];
          $idPrim      = (int) ($primeiraSol['id'] ?? 1);
          $nomePrim    = $primeiraSol['nome'] ?? 'Solicitante';
          $letraPrim   = mb_strtoupper(mb_substr($nomePrim, 0, 1));
          $emailPrim   = $primeiraSol['email'] ?? '';
          $telPrim     = $primeiraSol['telefone'] ?? '';
          $dataPrim    = !empty($primeiraSol['criado_em']) ? date('d/m/Y', strtotime($primeiraSol['criado_em'])) : date('d/m/Y');
          $tipoPrim    = $primeiraSol['tipo_texto'] ?? 'Website Institucional';
          $faixaPrim   = $primeiraSol['faixa'] ?? 'R$ 5.000 – 10.000';
          $prazoPrim   = $primeiraSol['prazo'] ?? '1 a 3 meses';
          $msgPrim     = $primeiraSol['mensagem'] ?? '';
          $anotPrim    = $primeiraSol['anotacao_interna'] ?? '';
          $orcPrim     = !empty($primeiraSol['valor_orcamento']) ? 'R$ ' . number_format((float) $primeiraSol['valor_orcamento'], 2, ',', '.') : 'Ainda não enviado';
          $objPrim     = $primeiraSol['objetivo'] ?? 'Não informado';
          $pubPrim     = $primeiraSol['publico_alvo'] ?? 'Não informado';
          $modPrim     = $primeiraSol['modulos'] ?? 'Não informado';
          $refPrim     = !empty($primeiraSol['referencias']) ? $primeiraSol['referencias'] : 'Sem referências informadas';
          [$clsBadgePrim, $txtBadgePrim] = $badgesSol[$primeiraSol['status'] ?? 'nova'] ?? ['bg-info-subtle text-info-emphasis', 'Nova'];
        ?>
        <div class="col-xl-5">
          <div class="card">
            <div class="card-body">

              <div class="d-flex align-items-center mb-3">
                <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-4 me-3 flex-shrink-0" id="det-letra"><?= htmlspecialchars($letraPrim, ENT_QUOTES, 'UTF-8') ?></span>
                <div class="lh-sm">
                  <h2 class="h5 fw-bold mb-1"><span id="det-nome"><?= htmlspecialchars($nomePrim, ENT_QUOTES, 'UTF-8') ?></span> <span id="det-status"><span class="badge rounded-pill <?= $clsBadgePrim ?>"><?= $txtBadgePrim ?></span></span></h2>
                  <small class="text-secondary d-block text-break"><span id="det-email"><?= htmlspecialchars($emailPrim, ENT_QUOTES, 'UTF-8') ?></span> · <i class="bi bi-telephone"></i> <span id="det-telefone"><?= htmlspecialchars($telPrim, ENT_QUOTES, 'UTF-8') ?></span></small>
                  <small class="text-secondary">Solicitação recebida em <span id="det-recebida"><?= $dataPrim ?></span></small>
                </div>
              </div>

              <ul class="nav nav-underline mb-3 border-bottom" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#det-aba-resumo" type="button" role="tab" aria-selected="true">Resumo</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#det-aba-questionario" type="button" role="tab" aria-selected="false">Questionário</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#det-aba-historico" type="button" role="tab" aria-selected="false">Histórico</button></li>
              </ul>

              <div class="tab-content">

                <!-- Resumo -->
                <div class="tab-pane fade show active" id="det-aba-resumo" role="tabpanel" tabindex="0">
                  <h3 class="h6 fw-bold"><i class="bi bi-file-text text-primary me-1"></i> Informações do projeto</h3>
                  <dl class="row small mb-3">
                    <dt class="col-5 text-secondary fw-normal">Tipo de projeto</dt><dd class="col-7" id="det-tipo"><?= htmlspecialchars($tipoPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-5 text-secondary fw-normal">Faixa de investimento</dt><dd class="col-7" id="det-faixa"><?= htmlspecialchars($faixaPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-5 text-secondary fw-normal">Prazo desejado</dt><dd class="col-7" id="det-prazo"><?= htmlspecialchars($prazoPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-5 text-secondary fw-normal">Mensagem</dt><dd class="col-7 mb-0" id="det-mensagem"><?= htmlspecialchars($msgPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                  </dl>

                  <h3 class="h6 fw-bold"><i class="bi bi-cash-coin text-primary me-1"></i> Estimativa da empresa</h3>
                  <div class="bg-primary-subtle rounded p-3 mb-3">
                    <strong class="fs-5" id="det-faixa-empresa"><?= htmlspecialchars($faixaPrim, ENT_QUOTES, 'UTF-8') ?></strong><br>
                    <small class="text-secondary">Com base nas informações fornecidas</small>
                  </div>

                  <form class="needs-validation mb-3" action="solicitacao-anotacao" method="post" data-backend="true" data-mensagem="Anotação salva." novalidate>
                    <?= $campoCsrf ?? '' ?>
                    <input type="hidden" name="id" class="input-solicitacao-id" value="<?= $idPrim ?>">
                    <label for="det-anotacao" class="form-label h6 fw-bold"><i class="bi bi-journal-text text-primary me-1"></i> Anotações internas</label>
                    <textarea class="form-control" id="det-anotacao" name="anotacao" rows="3" maxlength="500" placeholder="Adicione observações internas da equipe sobre esta solicitação..." required><?= htmlspecialchars($anotPrim, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                      <small class="text-secondary">Visível apenas para a equipe da empresa.</small>
                      <button type="submit" class="btn btn-sm btn-outline-primary">Salvar anotação</button>
                    </div>
                  </form>

                  <h3 class="h6 fw-bold"><i class="bi bi-receipt text-primary me-1"></i> Orçamento</h3>
                  <div class="bg-body-tertiary rounded p-3 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div><small class="text-secondary d-block">Valor do orçamento</small><strong class="fs-4" id="det-orcamento-valor"><?= htmlspecialchars($orcPrim, ENT_QUOTES, 'UTF-8') ?></strong></div>
                    <div><small class="text-secondary d-block">Registrado em</small><span id="det-orcamento-data"><?= $dataPrim ?></span></div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toast="Visualizando resumo do orçamento."><i class="bi bi-file-earmark-pdf me-1"></i> Visualizar PDF</button>
                  </div>
                </div>

                <!-- Questionario -->
                <div class="tab-pane fade" id="det-aba-questionario" role="tabpanel" tabindex="0">
                  <dl class="row small mb-0">
                    <dt class="col-5 text-secondary fw-normal">Objetivo do sistema</dt><dd class="col-7" id="det-objetivo"><?= htmlspecialchars($objPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-5 text-secondary fw-normal">Público-alvo</dt><dd class="col-7" id="det-publico"><?= htmlspecialchars($pubPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-5 text-secondary fw-normal">Módulos desejados</dt><dd class="col-7" id="det-modulos"><?= htmlspecialchars($modPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-5 text-secondary fw-normal">Referências</dt><dd class="col-7 mb-0" id="det-referencias"><?= htmlspecialchars($refPrim, ENT_QUOTES, 'UTF-8') ?></dd>
                  </dl>
                </div>

                <!-- Historico -->
                <div class="tab-pane fade" id="det-aba-historico" role="tabpanel" tabindex="0">
                  <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0"><strong><?= $dataPrim ?></strong> - Status atual: <?= htmlspecialchars($txtBadgePrim, ENT_QUOTES, 'UTF-8') ?></li>
                    <li class="list-group-item px-0"><strong><?= $dataPrim ?></strong> - Solicitação registrada no sistema</li>
                  </ul>
                </div>
              </div>

              <!-- Botoes de acao -->
              <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEnviarOrcamento">Enviar orçamento</button>
                <form action="solicitacao-converter" method="post" class="d-inline">
                  <?= $campoCsrf ?? '' ?>
                  <input type="hidden" name="id" class="input-solicitacao-id" value="<?= $idPrim ?>">
                  <button type="submit" class="btn btn-primary" id="btn-converter"><i class="bi bi-person-plus me-1"></i> Converter em cliente</button>
                </form>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalRecusar"><i class="bi bi-trash me-1"></i> Recusar</button>
              </div>

            </div>
          </div>
        </div>

      </div>


      <!-- ================= MODAL: NOVA SOLICITACAO ================= -->
      <div class="modal fade" id="modalNovaSolicitacao" tabindex="-1" aria-labelledby="tituloNovaSolicitacao" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <form class="needs-validation" action="solicitacao-salvar" method="post" data-backend="true" data-mensagem="Solicitação cadastrada com sucesso!" novalidate>
              <?= $campoCsrf ?? '' ?>
              <div class="modal-header">
                <h3 class="modal-title h5" id="tituloNovaSolicitacao">Nova solicitação</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="nova-nome" class="form-label fw-semibold small">Nome *</label>
                    <input type="text" class="form-control" id="nova-nome" name="nome" maxlength="100" required>
                    <div class="invalid-feedback">Informe o nome.</div>
                  </div>
                  <div class="col-md-6">
                    <label for="nova-email" class="form-label fw-semibold small">E-mail *</label>
                    <input type="email" class="form-control" id="nova-email" name="email" maxlength="120" required>
                    <div class="invalid-feedback">Digite um e-mail válido.</div>
                  </div>
                  <div class="col-md-4">
                    <label for="nova-telefone" class="form-label fw-semibold small">Telefone</label>
                    <input type="tel" class="form-control mascara-telefone" id="nova-telefone" name="telefone" placeholder="(11) 90000-0000">
                  </div>
                  <div class="col-md-4">
                    <label for="nova-tipo" class="form-label fw-semibold small">Tipo de projeto *</label>
                    <select class="form-select" id="nova-tipo" name="tipo" required>
                      <option value="">Selecione</option>
                      <option value="website">Website Institucional</option>
                      <option value="app">Aplicativo Mobile</option>
                      <option value="sistema">Sistema Interno</option>
                      <option value="loja">Loja Virtual</option>
                    </select>
                    <div class="invalid-feedback">Escolha o tipo.</div>
                  </div>
                  <div class="col-md-4">
                    <label for="nova-prazo" class="form-label fw-semibold small">Prazo desejado</label>
                    <select class="form-select" id="nova-prazo" name="prazo">
                      <option value="">Selecione</option>
                      <option value="1-3">1 a 3 meses</option>
                      <option value="3-6">3 a 6 meses</option>
                      <option value="6+">Mais de 6 meses</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label for="nova-mensagem" class="form-label fw-semibold small">Mensagem</label>
                    <textarea class="form-control" id="nova-mensagem" name="mensagem" rows="3" maxlength="1000"></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar solicitação</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ================= MODAL: ENVIAR ORCAMENTO ================= -->
      <div class="modal fade" id="modalEnviarOrcamento" tabindex="-1" aria-labelledby="tituloEnviarOrcamento" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form class="needs-validation" action="orcamento-enviar" method="post" data-backend="true" data-mensagem="Orçamento enviado ao cliente!" novalidate>
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="id" class="input-solicitacao-id" value="<?= $idPrim ?>">
              <div class="modal-header">
                <h3 class="modal-title h5" id="tituloEnviarOrcamento">Enviar orçamento</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="orc-valor" class="form-label fw-semibold small">Valor (R$) *</label>
                  <input type="number" class="form-control" id="orc-valor" name="valor" min="1" max="9999999" step="0.01" required>
                  <div class="invalid-feedback">Informe um valor maior que zero.</div>
                </div>
                <div class="mb-3">
                  <label for="orc-validade" class="form-label fw-semibold small">Válido até *</label>
                  <input type="date" class="form-control" id="orc-validade" name="validade" required>
                  <div class="invalid-feedback">Informe a data de validade.</div>
                </div>
                <div>
                  <label for="orc-obs" class="form-label fw-semibold small">Observações</label>
                  <textarea class="form-control" id="orc-obs" name="observacoes" rows="3" maxlength="500"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Enviar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ================= MODAL: RECUSAR ================= -->
      <div class="modal fade" id="modalRecusar" tabindex="-1" aria-labelledby="tituloRecusar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="solicitacao-recusar" method="post">
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="id" class="input-solicitacao-id" value="<?= $idPrim ?>">
              <div class="modal-header">
                <h3 class="modal-title h5" id="tituloRecusar">Recusar solicitação</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <p class="mb-0">Tem certeza que deseja recusar essa solicitação? O cliente será avisado.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger" id="btn-confirmar-recusa">Sim, recusar</button>
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
