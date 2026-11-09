<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Redefinir Senha - iiKliente</title>

  <!-- --- Bloco: Bootstrap 5 via CDN com Fallback Local e CSS Minificado --- -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" onerror="this.onerror=null;this.href='assets/vendor/bootstrap/css/bootstrap.min.css';">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/estilo.min.css">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

<?php if (!empty($flashToast)): ?>
  <div id="flash-toast-servidor" class="d-none" data-mensagem="<?= htmlspecialchars($flashToast['texto'], ENT_QUOTES, 'UTF-8') ?>" data-tipo="<?= htmlspecialchars($flashToast['tipo'], ENT_QUOTES, 'UTF-8') ?>"></div>
<?php endif; ?>

<header class="bg-white border-bottom py-3">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="login" class="d-flex align-items-center text-decoration-none text-body">
      <i class="bi bi-laptop fs-2 text-primary me-2"></i>
      <span class="lh-1">
        <strong class="d-block">iiKliente</strong>
        <small class="text-secondary">Gestão de Clientes</small>
      </span>
    </a>
    <a href="login" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Voltar ao login</a>
  </div>
</header>

<main class="flex-grow-1 d-flex align-items-center py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
              <span class="avatar rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2">
                <i class="bi bi-key-fill fs-4"></i>
              </span>
              <h1 class="h4 fw-bold">Cadastrar Nova Senha</h1>
              <p class="text-secondary small mb-0">Digite sua nova senha para a conta <strong><?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?></strong>.</p>
            </div>

            <form action="redefinir-senha-salvar" method="post" class="needs-validation" novalidate>
              <?= $campoCsrf ?? '' ?>
              <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">

              <div class="mb-3">
                <label for="senha-nova" class="form-label small fw-semibold">Nova Senha *</label>
                <input type="password" name="senha" id="senha-nova" class="form-control" minlength="8" required placeholder="Mínimo 8 caracteres">
                <div class="invalid-feedback">Digite uma senha com no mínimo 8 caracteres.</div>
              </div>

              <div class="mb-4">
                <label for="senha-confirmar" class="form-label small fw-semibold">Confirmar Nova Senha *</label>
                <input type="password" name="confirmar_senha" id="senha-confirmar" class="form-control" minlength="8" required placeholder="Repita a nova senha">
                <div class="invalid-feedback">Confirme a nova senha.</div>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-check2-circle me-1"></i> Salvar Nova Senha
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="bg-white border-top py-3 text-center text-secondary small">
  <div class="container d-flex flex-wrap justify-content-between align-items-center">
    <span>&copy; 2026 iiKliente Gestão de Clientes.</span>
    <span>Desenvolvido por <a href="https://capsulum.com.br/vitor-pedrini" target="_blank" rel="noopener noreferrer" class="text-secondary">Vitor Hugo Dos Santos Pedrini</a></span>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>if (typeof bootstrap === 'undefined') { document.write('<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="assets/js/main.min.js"></script>
</body>
</html>
