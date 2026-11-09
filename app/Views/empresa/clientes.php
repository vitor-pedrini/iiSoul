<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clientes - iiKliente</title>

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
          <input type="search" class="form-control" name="q" value="<?= htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar clientes, projetos, orçamentos..." aria-label="Buscar">
        </div>
      </form>

      <select class="form-select w-auto d-none d-xl-block filtro-topo-periodo" aria-label="Filtrar por período">
        <option value="12">Todos os períodos</option>
        <option value="6">Últimos 6 meses</option>
        <option value="3">Últimos 3 meses</option>
      </select>
      <select class="form-select w-auto d-none d-xl-block filtro-topo-projeto" aria-label="Filtrar por projeto">
        <option value="todos">Todos os clientes</option>
        <option value="andamento">Clientes ativos</option>
        <option value="concluidos">Clientes inativos</option>
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
          <h1 class="fw-bold">Clientes</h1>
          <p class="text-secondary mb-0">Cadastre, consulte, edite e exclua os clientes da sua empresa.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="exportar-csv?tipo=clientes" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV
          </a>
          <button type="button" class="btn btn-primary" id="btn-novo-cliente" data-bs-toggle="modal" data-bs-target="#modalCliente">
            <i class="bi bi-plus-lg me-1"></i> Novo cliente
          </button>
        </div>
      </div>

      <!-- Filtros -->
      <div class="card mb-3">
        <div class="card-body">
          <div class="row g-2">
            <div class="col-md-6 col-lg-5">
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" class="form-control" id="busca-tabela" value="<?= htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar por nome, e-mail ou CNPJ..." aria-label="Buscar cliente">
              </div>
            </div>
            <div class="col-6 col-lg-3">
              <select class="form-select" id="filtro-status" aria-label="Filtrar por status">
                <option value="todos">Todos os status</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
                <option value="prospecto">Prospecto</option>
              </select>
            </div>
            <div class="col-6 col-lg-3">
              <select class="form-select" id="filtro-tipo" aria-label="Filtrar por segmento">
                <option value="todos">Todos os segmentos</option>
                <option value="comercio">Comércio</option>
                <option value="saude">Saúde</option>
                <option value="educacao">Educação</option>
                <option value="tecnologia">Tecnologia</option>
              </select>
            </div>
            <div class="col-lg-1 d-grid">
              <button type="button" class="btn btn-outline-secondary" id="btn-limpar-filtros" title="Limpar filtros" aria-label="Limpar filtros"><i class="bi bi-x-lg"></i></button>
            </div>
          </div>
        </div>
      </div>

      <?php
        $nomesSegmento = [
          'comercio'   => 'Comércio',
          'saude'      => 'Saúde',
          'educacao'   => 'Educação',
          'tecnologia' => 'Tecnologia',
        ];
        $badgesStatus = [
          'ativo'     => ['bg-success-subtle text-success-emphasis', 'Ativo'],
          'prospecto' => ['bg-warning-subtle text-warning-emphasis', 'Prospecto'],
          'inativo'   => ['bg-secondary-subtle text-secondary-emphasis', 'Inativo'],
        ];
      ?>

      <!-- Tabela de clientes -->
      <div class="card">
        <div class="card-body">
          <p class="fw-bold mb-3"><span id="total-encontrado"><?= count($clientes ?? []) ?></span> clientes encontrados</p>

          <div class="table-responsive">
            <table class="table table-hover align-middle" id="tabela-dados">
              <thead>
                <tr class="small text-secondary">
                  <th>Cliente</th>
                  <th class="d-none d-lg-table-cell">CNPJ</th>
                  <th class="d-none d-md-table-cell">Segmento</th>
                  <th>Projeto / Etapa</th>
                  <th>Status</th>
                  <th class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach (($clientes ?? []) as $c): ?>
                <?php
                  $segTexto = $nomesSegmento[$c['segmento']] ?? ucfirst($c['segmento']);
                  [$classeBadge, $textoBadge] = $badgesStatus[$c['status']] ?? ['bg-success-subtle text-success-emphasis', 'Ativo'];
                  $letra = mb_strtoupper(mb_substr($c['nome'], 0, 1));
                  $progC = !empty($c['projeto_progresso']) ? (int) $c['projeto_progresso'] : 0;
                  $corProgC = ($progC >= 100) ? 'bg-success' : (($progC >= 60) ? 'bg-primary' : 'bg-info');
                ?>
                <tr
                  data-id="<?= (int) $c['id'] ?>"
                  data-nome="<?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"
                  data-cnpj="<?= htmlspecialchars($c['cnpj'], ENT_QUOTES, 'UTF-8') ?>"
                  data-tipo="<?= htmlspecialchars($c['segmento'], ENT_QUOTES, 'UTF-8') ?>"
                  data-contato="<?= htmlspecialchars($c['contato'], ENT_QUOTES, 'UTF-8') ?>"
                  data-email="<?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>"
                  data-telefone="<?= htmlspecialchars($c['telefone'], ENT_QUOTES, 'UTF-8') ?>"
                  data-cep="<?= htmlspecialchars($c['cep'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-endereco="<?= htmlspecialchars($c['endereco'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-cidade="<?= htmlspecialchars($c['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-uf="<?= htmlspecialchars($c['uf'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-status="<?= htmlspecialchars($c['status'], ENT_QUOTES, 'UTF-8') ?>">
                  <td>
                    <div class="d-flex align-items-center">
                      <span class="avatar rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0"><?= htmlspecialchars($letra, ENT_QUOTES, 'UTF-8') ?></span>
                      <div class="lh-sm">
                        <strong class="d-block"><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <small class="text-secondary"><?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?></small>
                      </div>
                    </div>
                  </td>
                  <td class="d-none d-lg-table-cell small"><?= htmlspecialchars($c['cnpj'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="d-none d-md-table-cell small"><?= htmlspecialchars($segTexto, ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php if (!empty($c['projeto_nome'])): ?>
                      <a href="projeto?id=<?= (int) $c['projeto_id'] ?>" class="text-decoration-none">
                        <strong class="small d-block text-primary"><?= htmlspecialchars($c['projeto_nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <div class="d-flex align-items-center gap-2 my-1">
                          <div class="progress flex-grow-1 barra-progresso-fina" role="progressbar" aria-valuenow="<?= $progC ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar <?= $corProgC ?> progresso-<?= $progC ?>"></div>
                          </div>
                          <small class="text-secondary fw-semibold"><?= $progC ?>%</small>
                        </div>
                        <small class="text-secondary d-block"><?= htmlspecialchars($c['projeto_etapa'] ?? 'Em andamento', ENT_QUOTES, 'UTF-8') ?></small>
                      </a>
                    <?php else: ?>
                      <span class="text-secondary small">Sem projeto ativo</span>
                    <?php endif; ?>
                  </td>
                  <td><span class="badge rounded-pill <?= $classeBadge ?>"><?= $textoBadge ?></span></td>
                  <td class="text-end text-nowrap">
                    <?php if (!empty($c['projeto_id'])): ?>
                      <a href="projeto?id=<?= (int) $c['projeto_id'] ?>" class="btn btn-sm btn-outline-primary" title="Ficha do projeto" aria-label="Ficha do projeto de <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-folder2-open"></i></a>
                    <?php endif; ?>
                    <a href="cliente?id=<?= (int) $c['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver ficha" aria-label="Ver ficha de <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-eye"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-editar" data-bs-toggle="modal" data-bs-target="#modalCliente" title="Editar" aria-label="Editar <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-pencil"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-excluir" data-bs-toggle="modal" data-bs-target="#modalExcluir" title="Excluir" aria-label="Excluir <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div id="msg-vazio" class="text-center text-secondary py-4 d-none">
            <i class="bi bi-search fs-2 d-block"></i>
            Nenhum cliente encontrado com esses filtros.
          </div>

          <nav class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3" aria-label="Paginação">
            <small class="text-secondary">Mostrando <?= count($clientes ?? []) ?> de <?= count($clientes ?? []) ?> resultados</small>
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item disabled"><a class="page-link" href="#" aria-label="Anterior"><i class="bi bi-chevron-left"></i></a></li>
              <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
              <li class="page-item disabled"><a class="page-link" href="#" aria-label="Próxima"><i class="bi bi-chevron-right"></i></a></li>
            </ul>
          </nav>
        </div>
      </div>


      <!-- ================= MODAL: NOVO / EDITAR CLIENTE (COM API VIACEP) ================= -->
      <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteTitulo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <form id="form-cliente" class="needs-validation" action="cliente-salvar" method="post" data-backend="true" data-mensagem="Cliente salvo com sucesso!" novalidate>
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="id" id="cliente-id" value="">

              <div class="modal-header">
                <h3 class="modal-title h5" id="modalClienteTitulo">Novo cliente</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>

              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-8">
                    <label for="cliente-nome" class="form-label fw-semibold small">Razão social / Nome *</label>
                    <input type="text" class="form-control" id="cliente-nome" name="nome" maxlength="100" required>
                    <div class="invalid-feedback">Informe o nome do cliente.</div>
                  </div>
                  <div class="col-md-4">
                    <label for="cliente-cnpj" class="form-label fw-semibold small">CNPJ *</label>
                    <input type="text" class="form-control mascara-cnpj" id="cliente-cnpj" name="cnpj" placeholder="00.000.000/0000-00" pattern="\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}" required>
                    <div class="invalid-feedback">Digite um CNPJ completo.</div>
                  </div>

                  <div class="col-md-6">
                    <label for="cliente-segmento" class="form-label fw-semibold small">Segmento *</label>
                    <select class="form-select" id="cliente-segmento" name="segmento" required>
                      <option value="">Selecione</option>
                      <option value="comercio">Comércio</option>
                      <option value="saude">Saúde</option>
                      <option value="educacao">Educação</option>
                      <option value="tecnologia">Tecnologia</option>
                    </select>
                    <div class="invalid-feedback">Escolha um segmento.</div>
                  </div>
                  <div class="col-md-6">
                    <label for="cliente-status" class="form-label fw-semibold small">Status *</label>
                    <select class="form-select" id="cliente-status" name="status" required>
                      <option value="ativo">Ativo</option>
                      <option value="prospecto">Prospecto</option>
                      <option value="inativo">Inativo</option>
                    </select>
                  </div>

                  <div class="col-12"><hr class="my-0"><p class="fw-semibold mt-3 mb-0">Contato principal</p></div>

                  <div class="col-md-4">
                    <label for="cliente-contato" class="form-label fw-semibold small">Nome do contato *</label>
                    <input type="text" class="form-control" id="cliente-contato" name="contato" maxlength="100" required>
                    <div class="invalid-feedback">Informe o contato.</div>
                  </div>
                  <div class="col-md-4">
                    <label for="cliente-email" class="form-label fw-semibold small">E-mail *</label>
                    <input type="email" class="form-control" id="cliente-email" name="email" maxlength="120" required>
                    <div class="invalid-feedback">Digite um e-mail válido.</div>
                  </div>
                  <div class="col-md-4">
                    <label for="cliente-telefone" class="form-label fw-semibold small">Telefone *</label>
                    <input type="tel" class="form-control mascara-telefone" id="cliente-telefone" name="telefone" placeholder="(11) 90000-0000" pattern="\(\d{2}\) \d{4,5}-\d{4}" required>
                    <div class="invalid-feedback">Digite o telefone com DDD.</div>
                  </div>

                  <!-- --- Bloco: Endereco integrado com a API Externa ViaCEP --- -->
                  <div class="col-12"><hr class="my-0"><p class="fw-semibold mt-3 mb-0">Endereço (preenchimento automático via CEP)</p></div>

                  <div class="col-md-3">
                    <label for="cliente-cep" class="form-label fw-semibold small">CEP</label>
                    <input type="text" class="form-control" id="cliente-cep" name="cep" placeholder="01310-100" maxlength="9">
                  </div>
                  <div class="col-md-5">
                    <label for="cliente-endereco" class="form-label fw-semibold small">Logradouro / Bairro</label>
                    <input type="text" class="form-control" id="cliente-endereco" name="endereco" maxlength="200">
                  </div>
                  <div class="col-md-3">
                    <label for="cliente-cidade" class="form-label fw-semibold small">Cidade</label>
                    <input type="text" class="form-control" id="cliente-cidade" name="cidade" maxlength="80">
                  </div>
                  <div class="col-md-1">
                    <label for="cliente-uf" class="form-label fw-semibold small">UF</label>
                    <input type="text" class="form-control" id="cliente-uf" name="uf" maxlength="2">
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ================= MODAL: CONFIRMAR EXCLUSAO ================= -->
      <div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="cliente-excluir" method="post">
              <?= $campoCsrf ?? '' ?>
              <div class="modal-header">
                <h3 class="modal-title h5" id="modalExcluirTitulo">Excluir cliente</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" id="id-excluir" value="">
                <p class="mb-1">Tem certeza que deseja excluir <strong id="nome-excluir"></strong>?</p>
                <p class="small text-danger mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Essa ação não pode ser desfeita.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger" id="btn-confirmar-exclusao">Sim, excluir</button>
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
