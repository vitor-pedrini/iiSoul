<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="iiKliente - sistema de gestão de clientes, orçamentos e projetos.">
  <title>iiKliente - Gestão de Clientes</title>

  <!-- --- Bloco: Schema.org (JSON-LD para mecanismos de busca) --- -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "iiKliente - Gestão de Clientes",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "description": "Plataforma para gerenciar clientes, solicitações, orçamentos e projetos.",
    "author": {
      "@type": "Person",
      "name": "Vitor Hugo Dos Santos Pedrini",
      "url": "https://capsulum.com.br/vitor-pedrini"
    },
    "offers": {
      "@type": "Offer",
      "price": "0.00",
      "priceCurrency": "BRL"
    }
  }
  </script>

  <!-- --- Bloco: Bootstrap 5 via CDN com Fallback Local e CSS Minificado --- -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" onerror="this.onerror=null;this.href='assets/vendor/bootstrap/css/bootstrap.min.css';">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/estilo.min.css">
</head>
<body class="bg-body-tertiary" itemscope itemtype="https://schema.org/WebPage">

<?php if (!empty($flashToast)): ?>
  <div id="flash-toast-servidor" class="d-none" data-mensagem="<?= htmlspecialchars($flashToast['texto'], ENT_QUOTES, 'UTF-8') ?>" data-tipo="<?= htmlspecialchars($flashToast['tipo'], ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

  <!-- ================= MENU DO TOPO ================= -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="./">
        <i class="bi bi-laptop fs-2 text-primary me-2"></i>
        <span class="lh-1">
          <span class="fw-bold d-block">iiKliente</span>
          <small class="text-secondary fs-6">Gestão de Clientes</small>
        </span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-topo" aria-controls="menu-topo" aria-expanded="false" aria-label="Abrir menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menu-topo">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="#funcionalidades">Funcionalidades</a></li>
          <li class="nav-item"><a class="nav-link" href="#como-funciona">Como funciona</a></li>
          <li class="nav-item"><a class="nav-link" href="#areas">Áreas do sistema</a></li>
          <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
        </ul>

        <div class="d-flex gap-2 align-items-center">
          <button type="button" class="btn btn-light btn-toggle-theme" id="btn-alternar-tema" aria-label="Alternar tema claro/escuro" title="Alternar Modo Escuro">
            <i class="bi bi-moon-stars-fill text-secondary"></i>
          </button>
          <a href="login" class="btn btn-outline-primary">Entrar</a>
          <a href="login#criar-conta" class="btn btn-primary">Solicitar orçamento</a>
        </div>
      </div>
    </div>
  </nav>


  <main>

    <!-- ================= TOPO DA PAGINA (HERO) ================= -->
    <section class="py-5">
      <div class="container">
        <div class="row align-items-center g-5">

          <div class="col-lg-6">
            <p class="text-secondary text-uppercase small mb-2">Da ideia à entrega</p>
            <h1 class="display-5 fw-bold" itemprop="headline">Gerencie seus clientes do orçamento à entrega do projeto</h1>
            <p class="lead text-secondary" itemprop="description">
              Organize solicitações, crie orçamentos, acompanhe o andamento dos projetos
              e fortaleça o relacionamento com seus clientes. Tudo em um só lugar.
            </p>

            <div class="d-flex flex-wrap gap-2 my-4">
              <a href="login#criar-conta" class="btn btn-primary btn-lg">
                Solicitar orçamento <i class="bi bi-arrow-right ms-1"></i>
              </a>
              <a href="login" class="btn btn-outline-primary btn-lg">Entrar</a>
            </div>

            <div class="row g-3" id="funcionalidades">
              <div class="col-sm-4 d-flex align-items-center">
                <span class="icone-caixa rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0">
                  <i class="bi bi-bar-chart-fill fs-5"></i>
                </span>
                <div class="lh-sm">
                  <strong class="d-block small">Mais organização</strong>
                  <small class="text-secondary">Processos claros e centralizados</small>
                </div>
              </div>
              <div class="col-sm-4 d-flex align-items-center">
                <span class="icone-caixa rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0">
                  <i class="bi bi-clock fs-5"></i>
                </span>
                <div class="lh-sm">
                  <strong class="d-block small">Mais produtividade</strong>
                  <small class="text-secondary">Menos tarefas manuais</small>
                </div>
              </div>
              <div class="col-sm-4 d-flex align-items-center">
                <span class="icone-caixa rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0">
                  <i class="bi bi-people-fill fs-5"></i>
                </span>
                <div class="lh-sm">
                  <strong class="d-block small">Clientes satisfeitos</strong>
                  <small class="text-secondary">Comunicação transparente</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 d-none d-md-block">
            <div class="card shadow">
              <div class="card-header bg-body-secondary">
                <i class="bi bi-circle-fill text-secondary-emphasis opacity-25 me-1"></i>
                <i class="bi bi-circle-fill text-secondary-emphasis opacity-25 me-1"></i>
                <i class="bi bi-circle-fill text-secondary-emphasis opacity-25"></i>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-4 border-end d-none d-xl-block">
                    <p class="fw-bold mb-3"><i class="bi bi-circle-fill text-primary me-1"></i> iiKliente</p>
                    <ul class="nav flex-column small">
                      <li class="nav-item"><span class="nav-link active bg-primary-subtle text-primary rounded"><i class="bi bi-house me-1"></i> Início</span></li>
                      <li class="nav-item"><span class="nav-link text-secondary"><i class="bi bi-people me-1"></i> Clientes</span></li>
                      <li class="nav-item"><span class="nav-link text-secondary"><i class="bi bi-folder me-1"></i> Projetos</span></li>
                      <li class="nav-item"><span class="nav-link text-secondary"><i class="bi bi-file-text me-1"></i> Orçamentos</span></li>
                      <li class="nav-item"><span class="nav-link text-secondary"><i class="bi bi-check2-square me-1"></i> Tarefas</span></li>
                      <li class="nav-item"><span class="nav-link text-secondary"><i class="bi bi-graph-up me-1"></i> Relatórios</span></li>
                    </ul>
                  </div>

                  <div class="col-xl-8">
                    <h2 class="h6 fw-bold mb-0">Olá, Empresa!</h2>
                    <p class="small text-secondary">Aqui está um resumo dos seus projetos.</p>

                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <div class="border rounded p-2 small">
                          <i class="bi bi-people-fill text-primary"></i> Clientes
                          <div class="fw-bold fs-5">24</div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="border rounded p-2 small">
                          <i class="bi bi-folder-fill text-primary"></i> Em andamento
                          <div class="fw-bold fs-5">8</div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="border rounded p-2 small">
                          <i class="bi bi-file-earmark-text-fill text-primary"></i> Orçamentos
                          <div class="fw-bold fs-5">12</div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="border rounded p-2 small">
                          <i class="bi bi-check-circle-fill text-primary"></i> Concluídos
                          <div class="fw-bold fs-5">36</div>
                        </div>
                      </div>
                    </div>

                    <p class="small fw-bold mb-1">Projetos recentes</p>
                    <ul class="list-group list-group-flush small">
                      <li class="list-group-item d-flex justify-content-between px-0">Site institucional <span class="badge bg-primary-subtle text-primary-emphasis">Em andamento</span></li>
                      <li class="list-group-item d-flex justify-content-between px-0">Sistema interno <span class="badge bg-warning-subtle text-warning-emphasis">Orçamento enviado</span></li>
                      <li class="list-group-item d-flex justify-content-between px-0">Aplicativo mobile <span class="badge bg-success-subtle text-success-emphasis">Concluído</span></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>


    <!-- ================= COMO FUNCIONA ================= -->
    <section class="py-5 bg-white border-top border-bottom" id="como-funciona">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-3">
            <h2 class="fw-bold">Como funciona?</h2>
            <p class="text-secondary">Do primeiro contato à entrega final, um processo simples e eficiente.</p>
          </div>

          <div class="col-lg-9">
            <div class="row g-3 text-center text-lg-start">

              <div class="col-6 col-md">
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold mb-2">1</span>
                <h3 class="h6 fw-bold"><i class="bi bi-file-text text-primary me-1"></i> Solicitação</h3>
                <p class="small text-secondary">O cliente envia uma solicitação pela página pública.</p>
              </div>
              <div class="col-6 col-md">
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold mb-2">2</span>
                <h3 class="h6 fw-bold"><i class="bi bi-search text-primary me-1"></i> Análise</h3>
                <p class="small text-secondary">Você analisa os detalhes e conversa com o cliente, se necessário.</p>
              </div>
              <div class="col-6 col-md">
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold mb-2">3</span>
                <h3 class="h6 fw-bold"><i class="bi bi-receipt text-primary me-1"></i> Orçamento</h3>
                <p class="small text-secondary">Crie e envie um orçamento profissional de forma rápida.</p>
              </div>
              <div class="col-6 col-md">
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold mb-2">4</span>
                <h3 class="h6 fw-bold"><i class="bi bi-gear text-primary me-1"></i> Projeto</h3>
                <p class="small text-secondary">Com o orçamento aprovado, o projeto é iniciado e acompanhado no sistema.</p>
              </div>
              <div class="col-12 col-md">
                <span class="avatar rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold mb-2">5</span>
                <h3 class="h6 fw-bold"><i class="bi bi-check2-circle text-primary me-1"></i> Entrega</h3>
                <p class="small text-secondary">Finalize o projeto, entregue ao cliente e mantenha o histórico salvo.</p>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ================= AREAS DA PLATAFORMA + ESTIMATIVA ================= -->
    <section class="py-5" id="areas">
      <div class="container">
        <div class="row g-4">

          <div class="col-lg-7">
            <h2 class="fw-bold">Áreas da plataforma</h2>
            <p class="text-secondary">Um espaço aberto para você conhecer a plataforma e uma área exclusiva para acompanhar tudo.</p>

            <div class="row g-3">
              <div class="col-md-6">
                <div class="card h-100">
                  <div class="card-body">
                    <i class="bi bi-people-fill fs-1 text-primary"></i>
                    <h3 class="h6 fw-bold mt-2">Página pública</h3>
                    <p class="small text-secondary">Conheça a plataforma, simule seu orçamento e solicite o seu projeto.</p>
                    <a href="login#criar-conta" class="small text-decoration-none">Saiba mais <i class="bi bi-arrow-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card h-100">
                  <div class="card-body">
                    <i class="bi bi-person-fill fs-1 text-primary"></i>
                    <h3 class="h6 fw-bold mt-2">Área do cliente</h3>
                    <p class="small text-secondary">Acompanhe orçamentos, projetos e se comunique diretamente com a empresa.</p>
                    <a href="login" class="small text-decoration-none">Saiba mais <i class="bi bi-arrow-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Estimativa de orçamento -->
          <div class="col-lg-5">
            <div class="card shadow-sm">
              <div class="card-body">
                <h2 class="h5 fw-bold">Estimativa de Orçamento</h2>
                <p class="small text-secondary">Tenha uma estimativa inicial do seu projeto em poucos cliques.</p>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="estimativa-tipo" class="form-label small fw-semibold">Tipo de projeto</label>
                    <select id="estimativa-tipo" class="form-select form-select-sm">
                      <option value="">Selecione uma opção</option>
                      <option value="landing-page">Landing page</option>
                      <option value="site-institucional">Website institucional</option>
                      <option value="loja-virtual">Loja virtual</option>
                      <option value="sistema-interno">Sistema interno</option>
                      <option value="app-mobile">Aplicativo mobile</option>
                    </select>

                    <label for="estimativa-paginas" class="form-label small fw-semibold mt-3">Páginas / Módulos</label>
                    <input type="number" id="estimativa-paginas" class="form-control form-control-sm" min="1" max="100" placeholder="Ex: 8">

                    <p class="small fw-semibold mt-3 mb-1">Recursos adicionais</p>
                    <div class="form-check small">
                      <input class="form-check-input estimativa-extra" type="checkbox" id="extra-formulario">
                      <label class="form-check-label" for="extra-formulario">Formulário de contato</label>
                    </div>
                    <div class="form-check small">
                      <input class="form-check-input estimativa-extra" type="checkbox" id="extra-area-cliente">
                      <label class="form-check-label" for="extra-area-cliente">Área do cliente</label>
                    </div>
                    <div class="form-check small">
                      <input class="form-check-input estimativa-extra" type="checkbox" id="extra-integracoes">
                      <label class="form-check-label" for="extra-integracoes">Integrações externas</label>
                    </div>
                    <div class="form-check small">
                      <input class="form-check-input estimativa-extra" type="checkbox" id="extra-painel">
                      <label class="form-check-label" for="extra-painel">Painel administrativo</label>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="bg-body-tertiary rounded p-3 text-center h-100 d-flex flex-column justify-content-center">
                      <i class="bi bi-calculator fs-2 text-primary"></i>
                      <small class="text-secondary">Valor estimado</small>
                      <p class="fs-4 fw-bold mb-1" id="estimativa-valor">R$ ---,--</p>
                      <small class="text-secondary d-block mb-3">Este é apenas uma estimativa. O valor final pode variar conforme os detalhes do projeto.</small>
                      <a href="login#criar-conta" class="btn btn-primary btn-sm">Solicitar orçamento completo</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>


    <!-- ================= POR QUE USAR ================= -->
    <section class="py-5 bg-white border-top border-bottom">
      <div class="container">
        <div class="row g-4 align-items-center">
          <div class="col-lg-3">
            <h2 class="fw-bold h3">Por que usar nossa plataforma?</h2>
            <p class="text-secondary">Mais controle, mais resultados para o seu negócio.</p>
          </div>

          <div class="col-lg-9">
            <div class="row g-3">
              <div class="col-sm-6 col-md-3">
                <i class="bi bi-lightning-charge-fill fs-2 text-primary"></i>
                <h3 class="h6 fw-bold">Centralização</h3>
                <p class="small text-secondary">Todas as informações em um só lugar.</p>
              </div>
              <div class="col-sm-6 col-md-3">
                <i class="bi bi-bar-chart-fill fs-2 text-primary"></i>
                <h3 class="h6 fw-bold">Transparência</h3>
                <p class="small text-secondary">Processos claros para você e seus clientes.</p>
              </div>
              <div class="col-sm-6 col-md-3">
                <i class="bi bi-shield-fill-check fs-2 text-primary"></i>
                <h3 class="h6 fw-bold">Segurança</h3>
                <p class="small text-secondary">Seus dados e dos seus clientes sempre protegidos.</p>
              </div>
              <div class="col-sm-6 col-md-3">
                <i class="bi bi-heart-fill fs-2 text-primary"></i>
                <h3 class="h6 fw-bold">Crescimento</h3>
                <p class="small text-secondary">Mais tempo para focar no que realmente importa.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- ================= PERGUNTAS FREQUENTES ================= -->
    <section class="py-5" id="faq">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-3">
            <h2 class="fw-bold h3">Perguntas frequentes</h2>
            <p class="text-secondary">Tire suas dúvidas sobre a plataforma.</p>
          </div>

          <div class="col-lg-9">
            <div class="row g-3">

              <div class="col-md-6">
                <div class="accordion" id="faq-esquerda">
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">A plataforma é realmente gratuita?</button>
                    </h3>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faq-esquerda">
                      <div class="accordion-body small">Sim! Este é um projeto acadêmico, então o uso é totalmente gratuito.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Posso personalizar os orçamentos?</button>
                    </h3>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faq-esquerda">
                      <div class="accordion-body small">Pode sim. A empresa define o valor, o prazo e as observações de cada orçamento enviado.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">A plataforma serve para qualquer tipo de projeto?</button>
                    </h3>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faq-esquerda">
                      <div class="accordion-body small">Ela foi pensada para projetos digitais (sites, sistemas e aplicativos), mas pode ser adaptada.</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="accordion" id="faq-direita">
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">Meus clientes também precisam se cadastrar?</button>
                    </h3>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faq-direita">
                      <div class="accordion-body small">Sim, para acompanhar o projeto eles criam uma conta pela tela de acesso.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">É possível integrar com outras ferramentas?</button>
                    </h3>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faq-direita">
                      <div class="accordion-body small">Sim! O sistema conta com integração automática de CEP via API ViaCEP e envio de e-mails com PHPMailer.</div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h3 class="accordion-header">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">Como funciona o suporte?</button>
                    </h3>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faq-direita">
                      <div class="accordion-body small">Dentro do sistema há um botão "Ver suporte" para falar com nossa equipe pelo e-mail.</div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main>


  <!-- ================= RODAPE ================= -->
  <footer class="bg-white border-top py-4">
    <div class="container">
      <div class="row align-items-center g-3">
        <div class="col-lg-3 d-flex align-items-center">
          <i class="bi bi-laptop fs-3 text-primary me-2"></i>
          <span class="lh-1">
            <span class="fw-bold d-block">iiKliente</span>
            <small class="text-secondary">Gestão de Clientes</small>
          </span>
        </div>

        <div class="col-lg-5">
          <ul class="nav small justify-content-center">
            <li class="nav-item"><a class="nav-link text-secondary" href="#funcionalidades">Funcionalidades</a></li>
            <li class="nav-item"><a class="nav-link text-secondary" href="#como-funciona">Como funciona</a></li>
            <li class="nav-item"><a class="nav-link text-secondary" href="#areas">Áreas do sistema</a></li>
            <li class="nav-item"><a class="nav-link text-secondary" href="#faq">FAQ</a></li>
          </ul>
        </div>

        <div class="col-lg-4 text-lg-end small text-secondary">
          <a href="#" class="text-secondary fs-5 me-2" aria-label="LinkedIn" data-toast="Link de exemplo (rede social)"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="text-secondary fs-5 me-2" aria-label="Instagram" data-toast="Link de exemplo (rede social)"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-secondary fs-5 me-3" aria-label="YouTube" data-toast="Link de exemplo (rede social)"><i class="bi bi-youtube"></i></a>
          © 2026 iiKliente. Desenvolvido por<br><a href="https://capsulum.com.br/vitor-pedrini" target="_blank" rel="noopener noreferrer" class="text-secondary">Vitor Hugo Dos Santos Pedrini</a>.
        </div>
      </div>
    </div>
  </footer>

  <!-- --- Bloco: Scripts com Fallback Local e Arquivo Minificado --- -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>if (typeof bootstrap === 'undefined') { document.write('<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"><\/script>'); }</script>
  <script src="assets/js/main.min.js?v=2"></script>
</body>
</html>
