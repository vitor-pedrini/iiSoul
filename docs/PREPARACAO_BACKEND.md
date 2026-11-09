#  Preparação para o Back-end e Segurança

Este documento foi elaborado para orientar a integração do Front-End atual com o futuro Back-End (recomendado em **PHP 8 + MySQL** no ambiente XAMPP).

Todos os formulários, botões e telas do projeto já foram estruturados com:
- Atributos semânticos `name`, `action` e `method`.
- Comentários no código HTML indicando a inserção de tokens de segurança e verificações de sessão.
- Marcações no código JavaScript (`main.js`) identificando simulações que deverão ser substituídas por requisições HTTP reais.

---

## 1. Mapeamento de Formulários e Endpoints

| Tela | Formulário / Ação | Método | Action Sugerido | Campos Enviados |
| :--- | :--- | :---: | :--- | :--- |
| `login.html` | Autenticação | `POST` | `login.php` | `perfil`, `email`, `senha`, `lembrar`, `csrf_token` |
| `login.html` | Cadastro | `POST` | `cadastro.php` | `perfil`, `nome`, `email`, `senha`, `confirmar_senha`, `termos`, `csrf_token` |
| `login.html` | Recuperar Senha | `POST` | `recuperar-senha.php` | `email`, `csrf_token` |
| `empresa/clientes.html` | Criar/Editar Cliente | `POST` | `cliente-salvar.php` | `id` (vazio no create), `nome`, `cnpj`, `segmento`, `status`, `contato`, `email`, `telefone`, `csrf_token` |
| `empresa/clientes.html` | Excluir Cliente | `POST` | `cliente-excluir.php` | `id`, `csrf_token` |
| `empresa/solicitacoes.html`| Nova Solicitação | `POST` | `solicitacao-salvar.php` | `nome`, `email`, `telefone`, `tipo`, `prazo`, `mensagem`, `csrf_token` |
| `empresa/solicitacoes.html`| Enviar Orçamento | `POST` | `orcamento-enviar.php` | `solicitacao_id`, `valor`, `validade`, `observacoes`, `csrf_token` |
| `empresa/solicitacoes.html`| Anotação Interna | `POST` | `solicitacao-anotacao.php` | `solicitacao_id`, `anotacao`, `csrf_token` |
| `empresa/configuracoes.html`| Dados da Empresa | `POST` | `configuracoes-salvar.php`| `nome`, `cnpj`, `email`, `telefone`, `logo` (arquivo multipart), `csrf_token` |
| `cliente/questionario.html`| Envio do Questionário| `POST` | `questionario-salvar.php`| `tipo_projeto`, `objetivo`, `publico_alvo`, `modulos[]`, `referencias`, `prazo`, `observacoes`, `csrf_token` |
| `cliente/projeto.html` | Upload de Materiais | `POST` | `arquivo-enviar.php` | `projeto_id`, `arquivo` (multipart), `descricao`, `csrf_token` |
| `cliente/projeto.html` | Solicitar Ajuste | `POST` | `ajuste-solicitar.php` | `etapa_id`, `ajuste`, `csrf_token` |
| `cliente/perfil.html` | Dados Pessoais | `POST` | `perfil-salvar.php` | `nome`, `email`, `telefone`, `empresa`, `foto` (multipart), `csrf_token` |

---

##  2. Sugestão de Modelagem do Banco de Dados (MySQL)

Abaixo está a estrutura relacional sugerida para atender a todos os requisitos do sistema:

```sql
-- 1. Tabela de Usuários (para autenticação)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil ENUM('cliente', 'empresa') NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL, -- HASH com password_hash()
    telefone VARCHAR(20),
    foto VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabela de Clientes da Empresa
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL, -- Vincula a um usuário do sistema quando houver
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(20) NOT NULL,
    segmento ENUM('comercio', 'saude', 'educacao', 'tecnologia') NOT NULL,
    status ENUM('ativo', 'prospecto', 'inativo') DEFAULT 'prospecto',
    contato_nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco VARCHAR(255),
    site VARCHAR(150),
    criado_em DATE NOT NULL
);

-- 3. Tabela de Solicitações de Orçamento
CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    telefone VARCHAR(20),
    tipo_projeto ENUM('website', 'app', 'sistema', 'loja') NOT NULL,
    faixa_estimada VARCHAR(50),
    prazo_desejado VARCHAR(50),
    mensagem TEXT,
    anotacoes_internas TEXT,
    status ENUM('nova', 'analise', 'orcamento-enviado', 'aceita', 'recusada') DEFAULT 'nova',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
);

-- 4. Tabela de Projetos
CREATE TABLE projetos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    titulo VARCHAR(120) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    descricao TEXT,
    progresso INT DEFAULT 0, -- 0 a 100%
    etapa_atual VARCHAR(50) NOT NULL,
    status ENUM('planejamento', 'desenvolvimento', 'revisao', 'aprovacao', 'concluido') DEFAULT 'planejamento',
    data_inicio DATE,
    data_previsao DATE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- 5. Tabela de Arquivos do Projeto (Uploads)
CREATE TABLE arquivos_projeto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projeto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    nome_original VARCHAR(255) NOT NULL,
    nome_servidor VARCHAR(255) NOT NULL, -- Nome com hash único
    extensao VARCHAR(10) NOT NULL,
    tamanho_bytes INT NOT NULL,
    descricao VARCHAR(150),
    enviado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projeto_id) REFERENCES projetos(id) ON DELETE CASCADE
);
```

---

##  3. Recomendações de Segurança para o Desenvolvedor Iniciante

Ao programar o back-end em PHP, siga estas práticas essenciais de segurança:

### 3.1. Proteção contra SQL Injection
- **Nunca concatene variáveis em consultas SQL:**
  ```php
  //  ERRADO (Vulnerável):
  $sql = "SELECT * FROM usuarios WHERE email = '$email'";

  // CORRETO (Seguro com PDO e Prepared Statements):
  $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $usuario = $stmt->fetch();
  ```

### 3.2. Criptografia Segura de Senhas
- Nunca armazene senhas em texto puro ou com algoritmos antigos (como `md5` ou `sha1`):
  ```php
  // Ao cadastrar o usuário:
  $senhaHash = password_hash($senhaDigitada, PASSWORD_DEFAULT);

  // Ao fazer login:
  if (password_verify($senhaDigitada, $usuario['senha'])) {
      // Login autorizado com sucesso
  }
  ```

### 3.3. Proteção contra Cross-Site Request Forgery (CSRF)
- Gere um token aleatório na sessão ao carregar os formulários:
  ```php
  if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  ```
- No HTML do formulário, inclua:
  ```html
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  ```
- Ao receber o `POST`, valide se o token confere antes de gravar os dados.

### 3.4. Segurança no Upload de Arquivos
- **Validação no Servidor:** Nunca confie apenas na extensão que veio do navegador.
  ```php
  $tiposPermitidos = ['image/png', 'image/jpeg', 'application/pdf', 'application/zip'];
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mimeType = finfo_file($finfo, $_FILES['arquivo']['tmp_name']);

  if (!in_array($mimeType, $tiposPermitidos)) {
      die("Tipo de arquivo não permitido.");
  }
  ```
- **Renomeação:** Salve os arquivos com nomes gerados por hash aleatório (ex: `bin2hex(random_bytes(16)) . '.' . $extensao`) para evitar sobreposição ou execução acidental de scripts maliciosos.
- **Armazenamento:** Mantenha os arquivos enviados em pasta fora do diretório público ou com execução de scripts desativada via `.htaccess`.

### 3.5. Controle de Sessão e Autorização
- Em páginas da pasta `/empresa/`, verifique:
  ```php
  session_start();
  if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'empresa') {
      header('Location: ../login.html');
      exit;
  }
  ```
- Em páginas da pasta `/cliente/`, verifique:
  ```php
  session_start();
  if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'cliente') {
      header('Location: ../login.html');
      exit;
  }
  ```

---

## 4. Sugestão de Integração com API Externa
Para atender ao critério de **Integração com APIs Externas**, recomenda-se implementar a consulta automática de endereço via CEP (utilizando a API pública e gratuita do **ViaCEP**):
- O cliente ou atendente digita o CEP no formulário.
- Uma chamada assíncrona (`fetch('https://viacep.com.br/ws/' + cep + '/json/')`) preenche automaticamente os campos de Logradouro, Bairro, Cidade e Estado.
