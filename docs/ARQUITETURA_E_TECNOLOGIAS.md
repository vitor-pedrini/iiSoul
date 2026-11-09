#  Arquitetura, Tecnologias e Segurança — iiKliente

Este documento apresenta a justificativa técnica das tecnologias utilizadas no projeto **iiKliente — Gestão de Clientes**, demonstrando como cada requisito foi implementado de forma limpa, comentada por blocos e fácil de manter.

---

## 1. Front-End: HTML5, W3C, Schema.org e Bootstrap 5

- **HTML Semântico e Padrão W3C:** Uso correto das tags estruturais (`<nav>`, `<header>`, `<main>`, `<aside>`, `<section>`, `<footer>`), atributos de acessibilidade (`aria-label`, `role`) e zero estilos inline (`style="..."`).
- **Schema.org (`JSON-LD` e Microdata):** As páginas públicas incluem marcação estruturada `SoftwareApplication` e `WebPage` para mecanismos de busca.
- **100% Bootstrap 5.3.3 e Mobile First:** Layout construído inteiramente com o grid responsivo e componentes nativos do Bootstrap (`offcanvas-lg`, `card`, `modal`, `nav-pills`, `accordion`, `toast`).
- **CDN com Fallback Local:** Caso o CDN do Bootstrap ou do Chart.js esteja indisponível (sem internet), as páginas carregam automaticamente os arquivos locais salvos em `assets/vendor/`.
- **Arquivos Minificados:** Os arquivos customizados de estilo e scripts possuem versão de desenvolvimento comentada por blocos (`estilo.css`, `main.js`, `graficos.js`) e versão minificada em produção (`estilo.min.css`, `main.min.js`, `graficos.min.js`).

---

## 2. Back-End em PHP: P.O.O, M.V.C, P.D.O, Rotas, SOLID e Composer

- **P.O.O (Programação Orientada a Objetos) e M.V.C (Model-View-Controller):**
  - **Models (`app/Models/`):** Classes `Usuario`, `Cliente`, `Solicitacao` e `Projeto`, responsáveis exclusivamente pelo acesso ao banco de dados.
  - **Views (`app/Views/`):** Telas separadas em `publico/`, `empresa/` e `cliente/`.
  - **Controllers (`app/Controllers/`):** Classes `HomeController`, `AuthController`, `EmpresaController` e `PortalClienteController`, que recebem as requisições, validam os dados e chamam os Models.
- **Route (Roteamento Front-Controller):** O arquivo `.htaccess` direciona as requisições para `index.php`, onde a classe `App\Core\Router` mapeia cada rota `GET` e `POST` para o método correspondente do Controller.
- **SOLID (Responsabilidade Única):** Cada classe em `app/Core/` tem um único papel claro:
  - `Database`: cria e compartilha a conexão PDO.
  - `Router`: registra e despacha as rotas.
  - `Auth`: gerencia a sessão e mensagens de feedback.
  - `Csrf`: gera e valida tokens contra ataques CSRF.
  - `Mailer`: envia e-mails via PHPMailer.
- **Composer:** Gerencia o autoload PSR-4 (`App\` -> `app/`) e as bibliotecas `vlucas/phpdotenv` e `phpmailer/phpmailer`.

---

## 3. Banco de Dados: PDO, ACID e Migrations

- **PDO com Prepared Statements:** Todas as consultas SQL utilizam `prepare()` e parâmetros nomeados (`:id`, `:email`), impedindo ataques de **SQL Injection**.
- **ACID (Transações):** As tabelas utilizam o motor `ENGINE=InnoDB`. Operações que alteram múltiplas tabelas ao mesmo tempo — como converter uma solicitação em **Cliente + Projeto** (`Solicitacao::converterEmCliente`) ou popular o banco (`migrate.php`) — usam `beginTransaction()`, `commit()` e `rollBack()`.
- **Migrations:** A estrutura do banco fica versionada em `database/migrations/001_criar_tabelas.sql` e é controlada pela tabela `migrations` ao executar `php migrate.php`.

---

## 4. Integrações com APIs e Segurança (`.env`)

- **Arquivo `.env`:** Nenhuma senha ou credencial fica fixa no código PHP. Tudo é lido do arquivo `.env` (que fica listado no `.gitignore`).
- **PHPMailer (`App\Core\Mailer`):** Utilizado no cadastro de conta, recuperação de senha, envio de orçamento ao cliente e confirmação do questionário. Quando usado em laboratório sem SMTP externo configurado, grava automaticamente o registro dos e-mails enviados em `storage/logs/emails.log`.
- **API Externa ViaCEP (`assets/js/main.js`):** No cadastro e edição de clientes (`empresa/clientes.html`), ao digitar os 8 dígitos do CEP, o sistema consulta `https://viacep.com.br/ws/{cep}/json/` e preenche automaticamente Logradouro, Bairro, Cidade e UF.
- **Segurança contra Vulnerabilidades:**
  - Senhas criptografadas com `password_hash()` e conferidas com `password_verify()`.
  - Proteção **CSRF** (`App\Core\Csrf`) em todos os formulários `POST`.
  - Proteção contra **XSS** com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` no PHP e `textContent` no JavaScript.
  - Validação de extensão e tamanho máximo em todos os uploads de arquivos, renomeando os arquivos salvos em `storage/uploads/`.
