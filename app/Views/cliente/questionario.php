<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Orçamento e Questionário - iiKliente</title>

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
  $temSol      = !empty($ultimaSolicitacao);
  $temProj     = !empty($projeto);

  $nomeProjetoQ = $projeto['nome'] ?? ($ultimaSolicitacao['tipo_texto'] ?? 'Novo Projeto');
  $faixaAtual   = $ultimaSolicitacao['faixa'] ?? 'Escolha o tipo de projeto';
  $statusSol    = $ultimaSolicitacao['status'] ?? '';
  $valorOrcNum  = (float) ($ultimaSolicitacao['valor_orcamento'] ?? ($projeto['valor_orcamento'] ?? 0));

  $orcOficial   = $valorOrcNum > 0
      ? 'R$ ' . number_format($valorOrcNum, 2, ',', '.')
      : ($statusSol === 'rascunho' ? 'Rascunho em edição' : ($temSol ? 'Em análise pela equipe' : 'Aguardando envio'));
  if ($valorOrcNum > 0) {
      $badgeStatusQ = 'Orçamento enviado';
      $corBadgeQ    = 'bg-success-subtle text-success-emphasis';
  } elseif ($statusSol === 'rascunho') {
      $badgeStatusQ = 'Rascunho salvo';
      $corBadgeQ    = 'bg-info-subtle text-info-emphasis';
  } elseif ($temSol) {
      $badgeStatusQ = 'Respostas enviadas';
      $corBadgeQ    = 'bg-success-subtle text-success-emphasis';
  } else {
      $badgeStatusQ = 'Aguardando envio';
      $corBadgeQ    = 'bg-warning-subtle text-warning-emphasis';
  }

  // Dados ja preenchidos pelo usuario (se houver)
  $mapaTipoInverso = [
      'Website Institucional' => 'site-institucional',
      'Landing Page'          => 'landing-page',
      'Loja Virtual'          => 'loja-virtual',
      'Sistema Interno'       => 'sistema-interno',
      'Aplicativo Mobile'     => 'app-mobile',
  ];
  $tipoSalvo = $temSol ? ($mapaTipoInverso[$ultimaSolicitacao['tipo_texto'] ?? ''] ?? 'site-institucional') : '';
  $objSalvo  = $ultimaSolicitacao['objetivo'] ?? '';
  $pubSalvo  = $ultimaSolicitacao['publico_alvo'] ?? '';
  $modsSalvos= !empty($ultimaSolicitacao['modulos']) ? explode(',', (string) $ultimaSolicitacao['modulos']) : [];
  $refSalvo  = $ultimaSolicitacao['referencias'] ?? '';
  $prazoSalvo= $ultimaSolicitacao['prazo'] ?? '';
  $obsSalvo  = $temSol ? trim((string) ($ultimaSolicitacao['mensagem'] ?? ''), '"') : '';
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
        <li class="nav-item"><a class="nav-link" href="projeto"><i class="bi bi-folder-fill me-2"></i> Meu Projeto</a></li>
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="questionario"><i class="bi bi-file-earmark-text-fill me-2"></i> Orçamento / Questionário</a></li>
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
          <li class="breadcrumb-item"><a href="projeto" class="text-decoration-none"><?= htmlspecialchars($nomeProjetoQ, ENT_QUOTES, 'UTF-8') ?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Orçamento e Questionário</li>
        </ol>
      </nav>

      <!-- Titulo -->
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <h1 class="fw-bold mb-0">Orçamento e Questionário</h1>
            <span class="badge rounded-pill <?= $corBadgeQ ?> fs-6"><i class="bi bi-circle-fill small me-1"></i> <?= htmlspecialchars($badgeStatusQ, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <p class="text-secondary mb-0">Preencha o questionário abaixo para que possamos entender melhor o seu projeto. Com base nas suas respostas, nossa equipe fará uma análise e enviará o orçamento oficial.</p>
        </div>
        <div class="col-lg-4">
          <div class="card"><div class="card-body py-2">
            <small class="text-secondary">Projeto</small>
            <strong class="d-block fs-5"><?= htmlspecialchars($nomeProjetoQ, ENT_QUOTES, 'UTF-8') ?></strong>
            <small class="text-secondary">Cliente: <?= htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') ?></small>
          </div></div>
        </div>
      </div>

      <div class="row g-3">

        <!-- ========== COLUNA PRINCIPAL ========== -->
        <div class="col-xl-9">

          <!-- Faixa e orcamento oficial -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="card h-100"><div class="card-body d-flex">
                <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-calculator fs-4"></i></span>
                <div class="lh-sm">
                  <small class="d-block">Faixa estimada de investimento</small>
                  <strong class="fs-4" id="q-faixa"><?= htmlspecialchars($faixaAtual, ENT_QUOTES, 'UTF-8') ?></strong>
                  <small class="text-secondary d-block mt-1">Esta é uma estimativa inicial com base em projetos similares. O valor final será definido após a análise das suas respostas.</small>
                </div>
              </div></div>
            </div>
            <div class="col-md-6">
              <div class="card h-100"><div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex">
                  <span class="icone-caixa rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0"><i class="bi bi-file-earmark-text-fill fs-4"></i></span>
                  <div class="lh-sm">
                    <small class="d-block">Orçamento oficial</small>
                    <strong class="fs-4"><?= htmlspecialchars($orcOficial, ENT_QUOTES, 'UTF-8') ?></strong>
                    <small class="text-secondary d-block mt-1">
                      <?= ($valorOrcNum > 0 && !$temProj) ? 'A proposta foi enviada pela equipe para sua validação.' : 'Em breve enviaremos o orçamento detalhado.' ?>
                    </small>
                  </div>
                </div>
                <?php if ($valorOrcNum > 0 && !$temProj): ?>
                  <div class="mt-3 pt-2 border-top">
                    <form action="orcamento-aprovar" method="post" class="d-inline">
                      <?= $campoCsrf ?? '' ?>
                      <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-check-circle me-1"></i> Aprovar orçamento e iniciar projeto
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
              </div></div>
            </div>
          </div>

          <!-- Etapas -->
          <div class="card mb-3">
            <div class="card-body py-3">
              <div class="row g-2">
                <div class="col-6 col-md-3 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0">1</span>
                  <div class="lh-sm"><strong class="small d-block">Questionário</strong><small class="text-secondary">Preencha suas respostas</small></div>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0">2</span>
                  <div class="lh-sm"><strong class="small d-block">Análise da equipe</strong><small class="text-secondary">Nossa equipe analisa o projeto</small></div>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0">3</span>
                  <div class="lh-sm"><strong class="small d-block">Envio do orçamento</strong><small class="text-secondary">Você recebe a proposta</small></div>
                </div>
                <div class="col-6 col-md-3 d-flex align-items-center">
                  <span class="avatar rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0">4</span>
                  <div class="lh-sm"><strong class="small d-block">Aprovação</strong><small class="text-secondary">Você aprova e iniciamos</small></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Questionario -->
          <div class="card">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start mb-1">
                <h2 class="h5 fw-bold mb-0">Questionário do projeto</h2>
                <small class="text-secondary"><i class="bi bi-clock me-1"></i> Tempo estimado: 5 a 10 minutos</small>
              </div>
              <small class="text-secondary d-block mb-3">Quanto mais detalhes você fornecer, mais preciso será o nosso orçamento.</small>

              <form id="form-questionario" class="needs-validation" action="questionario-salvar" method="post" data-backend="true" data-mensagem="Respostas enviadas! Nossa equipe vai analisar." novalidate>
                <?= $campoCsrf ?? '' ?>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="q-tipo" class="form-label fw-semibold small">Tipo de projeto <span class="text-danger">*</span></label>
                    <select class="form-select" id="q-tipo" name="tipo_projeto" required>
                      <option value="" <?= $tipoSalvo === '' ? 'selected' : '' ?>>Selecione uma opção</option>
                      <option value="landing-page" <?= $tipoSalvo === 'landing-page' ? 'selected' : '' ?>>Landing page</option>
                      <option value="site-institucional" <?= $tipoSalvo === 'site-institucional' ? 'selected' : '' ?>>Website institucional</option>
                      <option value="loja-virtual" <?= $tipoSalvo === 'loja-virtual' ? 'selected' : '' ?>>Loja virtual</option>
                      <option value="sistema-interno" <?= $tipoSalvo === 'sistema-interno' ? 'selected' : '' ?>>Sistema interno</option>
                      <option value="app-mobile" <?= $tipoSalvo === 'app-mobile' ? 'selected' : '' ?>>Aplicativo mobile</option>
                    </select>
                    <div class="invalid-feedback">Escolha o tipo de projeto.</div>
                  </div>

                  <div class="col-md-6">
                    <label for="q-objetivo" class="form-label fw-semibold small">Objetivo do sistema <span class="text-danger">*</span></label>
                    <select class="form-select" id="q-objetivo" name="objetivo" required>
                      <option value="" <?= $objSalvo === '' ? 'selected' : '' ?>>Selecione uma opção</option>
                      <option value="apresentar" <?= $objSalvo === 'apresentar' ? 'selected' : '' ?>>Apresentar a empresa</option>
                      <option value="vender" <?= $objSalvo === 'vender' ? 'selected' : '' ?>>Vender produtos ou serviços</option>
                      <option value="captar" <?= $objSalvo === 'captar' ? 'selected' : '' ?>>Captar novos clientes</option>
                      <option value="organizar" <?= $objSalvo === 'organizar' ? 'selected' : '' ?>>Organizar processos internos</option>
                    </select>
                    <div class="invalid-feedback">Escolha o objetivo.</div>
                  </div>

                  <div class="col-12">
                    <label for="q-publico" class="form-label fw-semibold small">Público-alvo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="q-publico" name="publico_alvo" value="<?= htmlspecialchars($pubSalvo, ENT_QUOTES, 'UTF-8') ?>" maxlength="150" placeholder="Ex: clientes finais, empresas, estudantes..." required>
                    <div class="invalid-feedback">Informe o público-alvo.</div>
                  </div>

                  <div class="col-12">
                    <p class="form-label fw-semibold small mb-1">Páginas / módulos desejados <span class="text-danger">*</span></p>
                    <div class="border rounded p-3">
                      <div class="row g-2">
                        <div class="col-sm-6 col-lg-4">
                          <div class="form-check">
                            <input class="form-check-input q-modulo" type="checkbox" id="mod1" name="modulos[]" value="pagina-inicial" <?= in_array('pagina-inicial', $modsSalvos, true) ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="mod1">Página inicial</label>
                            <div class="invalid-feedback">Marque pelo menos um módulo.</div>
                          </div>
                        </div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod2" name="modulos[]" value="area-cliente" <?= in_array('area-cliente', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod2">Área do cliente</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod3" name="modulos[]" value="painel-admin" <?= in_array('painel-admin', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod3">Painel administrativo</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod4" name="modulos[]" value="sobre" <?= in_array('sobre', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod4">Sobre a empresa</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod5" name="modulos[]" value="ecommerce" <?= in_array('ecommerce', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod5">E-commerce (loja virtual)</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod6" name="modulos[]" value="area-membros" <?= in_array('area-membros', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod6">Área de membros</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod7" name="modulos[]" value="servicos" <?= in_array('servicos', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod7">Serviços / Produtos</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod8" name="modulos[]" value="contato" <?= in_array('contato', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod8">Formulários de contato</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod9" name="modulos[]" value="multilingue" <?= in_array('multilingue', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod9">Multilíngue</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod10" name="modulos[]" value="blog" <?= in_array('blog', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod10">Blog / Notícias</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod11" name="modulos[]" value="integracao" <?= in_array('integracao', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod11">Integração com sistemas</label></div></div>
                        <div class="col-sm-6 col-lg-4"><div class="form-check"><input class="form-check-input q-modulo" type="checkbox" id="mod12" name="modulos[]" value="outros" <?= in_array('outros', $modsSalvos, true) ? 'checked' : '' ?>><label class="form-check-label small" for="mod12">Outros (especificar)</label></div></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="q-referencias" class="form-label fw-semibold small">Referências visuais</label>
                    <div class="input-group">
                      <span class="input-group-text bg-white"><i class="bi bi-link-45deg"></i></span>
                      <input type="url" class="form-control" id="q-referencias" name="referencias" value="<?= htmlspecialchars($refSalvo, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cole links de sites que você gosta (opcional)">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="q-prazo" class="form-label fw-semibold small">Prazo desejado</label>
                    <select class="form-select" id="q-prazo" name="prazo">
                      <option value="" <?= $prazoSalvo === '' ? 'selected' : '' ?>>Selecione uma opção</option>
                      <option value="1-mes" <?= $prazoSalvo === '1-mes' ? 'selected' : '' ?>>Até 1 mês</option>
                      <option value="1-3-meses" <?= $prazoSalvo === '1-3-meses' ? 'selected' : '' ?>>1 a 3 meses</option>
                      <option value="3-6-meses" <?= $prazoSalvo === '3-6-meses' ? 'selected' : '' ?>>3 a 6 meses</option>
                      <option value="6-mais" <?= $prazoSalvo === '6-mais' ? 'selected' : '' ?>>Mais de 6 meses</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label for="q-observacoes" class="form-label fw-semibold small">Observações adicionais</label>
                    <textarea class="form-control" id="q-observacoes" name="observacoes" rows="4" maxlength="1000" placeholder="Conte mais sobre o seu projeto, necessidades específicas ou outras informações importantes..."><?= htmlspecialchars($obsSalvo, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div class="text-end"><small class="text-secondary"><span id="q-contador"><?= mb_strlen($obsSalvo) ?></span>/1000</small></div>
                  </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
                  <button type="submit" name="acao" value="rascunho" formnovalidate class="btn btn-outline-primary"><i class="bi bi-floppy me-1"></i> Salvar rascunho</button>
                  <button type="submit" name="acao" value="enviar" class="btn btn-primary">Enviar respostas <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- ========== COLUNA LATERAL ========== -->
        <div class="col-xl-3">

          <div class="card mb-3">
            <div class="card-body">
              <h2 class="h5 fw-bold mb-3">Status do projeto</h2>
              <?php
                $dataEnvioQ = !empty($ultimaSolicitacao['criado_em'])
                    ? date('d/m', strtotime($ultimaSolicitacao['criado_em']))
                    : '';
                $enviadoReal = $temSol && ($statusSol !== 'rascunho');
              ?>
              <ul class="list-unstyled small mb-0">
                <li class="d-flex mb-3">
                  <i class="bi <?= $enviadoReal ? 'bi-check-circle-fill text-success' : 'bi-record-circle-fill text-warning' ?> me-2"></i>
                  <div class="lh-sm flex-grow-1"><strong class="d-block"><?= $enviadoReal ? 'Questionário enviado' : ($statusSol === 'rascunho' ? 'Rascunho salvo' : 'Aguardando envio') ?></strong><span class="text-secondary"><?= $enviadoReal ? 'Respostas recebidas pela equipe.' : 'Preencha o questionário para iniciarmos a análise.' ?></span></div>
                  <?php if ($dataEnvioQ !== ''): ?><span class="text-secondary"><?= htmlspecialchars($dataEnvioQ, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                </li>
                <li class="d-flex mb-3">
                  <i class="bi <?= $valorOrcNum > 0 ? 'bi-check-circle-fill text-success' : ($enviadoReal ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary') ?> me-2"></i>
                  <div class="lh-sm"><strong class="d-block">Em análise</strong><span class="text-secondary">Nossa equipe avalia suas respostas.</span></div>
                </li>
                <li class="d-flex mb-3">
                  <i class="bi <?= $valorOrcNum > 0 ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' ?> me-2"></i>
                  <div class="lh-sm"><strong class="d-block">Orçamento enviado</strong><span class="text-secondary"><?= $valorOrcNum > 0 ? 'Proposta de R$ ' . number_format($valorOrcNum, 2, ',', '.') : 'Você receberá a proposta detalhada.' ?></span></div>
                </li>
                <li class="d-flex mb-3">
                  <i class="bi <?= ($statusSol === 'aceita' || $temProj) ? 'bi-check-circle-fill text-success' : ($valorOrcNum > 0 ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary') ?> me-2"></i>
                  <div class="lh-sm"><strong class="d-block">Aprovação</strong><span class="text-secondary">Analise o orçamento e aprove para iniciarmos o projeto.</span></div>
                </li>
                <li class="d-flex">
                  <i class="bi <?= $temProj ? 'bi-record-circle-fill text-primary' : 'bi-circle text-secondary' ?> me-2"></i>
                  <div class="lh-sm"><strong class="d-block">Projeto em andamento</strong><span class="text-secondary">Após a aprovação, iniciaremos o desenvolvimento.</span></div>
                </li>
              </ul>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-body">
              <h2 class="h5 fw-bold">Dúvidas sobre o questionário?</h2>
              <p class="small text-secondary">Fique tranquilo! Nossa equipe está à disposição para ajudar você a preencher as informações.</p>
              <button type="button" class="btn btn-outline-primary w-100" data-toast="Suporte por e-mail: vitorh.pedrini@gmail.com"><i class="bi bi-envelope me-1"></i> Falar com a equipe</button>
            </div>
          </div>

          <div class="alert alert-info mb-0 small" role="note">
            <strong><i class="bi bi-lightbulb me-1"></i> Dica</strong><br>
            Se possível, compartilhe referências de sites que você gosta. Isso nos ajuda a entender melhor o estilo visual que você procura.
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
