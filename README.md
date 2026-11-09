# iiKliente — Sistema de Gestão de Clientes, Projetos e Orçamentos

Sistema web completo desenvolvido como projeto prático na temática **Gestão de Clientes**, atendendo aos critérios de avaliação da fase de projeto: **Documentação**, **Tecnologia e Implementação**, **Usabilidade (UX)**, **Design (UI)** e **Funcionalidades (Essenciais e Avançadas)**.

---

## 📌 Sumário
- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades Implementadas](#-funcionalidades-implementadas)
- [Tecnologias Utilizadas e Justificativa](#-tecnologias-utilizadas-e-justificativa)
- [Arquitetura e Boas Práticas](#-arquitetura-e-boas-práticas)
- [Medidas de Segurança](#-medidas-de-segurança)
- [Manual de Instalação e Configuração](#-manual-de-instalação-e-configuração)
- [Guia do Usuário](#-guia-do-usuário)
- [Contas de Acesso Padrão](#-contas-de-acesso-padrão)
- [Estrutura de Pastas do Projeto](#-estrutura-de-pastas-do-projeto)
- [Autor](#-autor)

---

## Sobre o Projeto

O **iiKliente** centraliza o ciclo de vida completo entre prestadores de serviços digitais (freelancers, agências ou empresas) e seus clientes:
1. **Captação:** Solicitação pública de orçamento com calculadora interativa e formulário de briefing.
2. **Negociação:** A empresa recebe a solicitação, analisa e envia a proposta com orçamento detalhado por e-mail.
3. **Execução em 5 Etapas:**
   - 1. Briefing e Requisitos
   - 2. Wireframes e Protótipo (com link navegável interativo)
   - 3. Desenvolvimento Front-end / Back-end
   - 4. Revisões e Ajustes (abertura e acompanhamento de chamados de ajuste)
   - 5. Entrega Final e Termo de Conclusão (com geração de PDF e assinatura digital)
4. **Módulo Financeiro:** Gestão de parcelas, comprovantes de pagamento e chave PIX.
5. **Histórico e Auditoria:** Linha do tempo (Audit Trail) de cada ação realizada no projeto.

---

## Funcionalidades Implementadas

### Requisitos Essenciais
- **Autenticação Completa:** Login, logout com destruição de sessão e proteção de rotas por perfil (`empresa` e `cliente`).
- **CRUD Completo de Clientes:** Criar, visualizar, editar e excluir clientes com persistência em MySQL via PDO.
- **Filtros e Pesquisas:** Busca em tempo real por nome, e-mail, status de projeto e tipo de solicitação.
- **Relatórios e Visualização:**
  - Dashboard interativo com contadores em tempo real.
  - Exportação de dados de clientes em formato **CSV (com suporte a UTF-8 BOM para Excel)**.
  - Geração de **Termo de Entrega em PDF** formatado para impressão.

### Requisitos Adicionais e Diferenciais
- **Upload Seguro de Arquivos:** Validação estrita por MIME type real usando `finfo_file` (impede arquivos renomeados maliciosos).
- **Integração com APIs Externas:**
  - **ViaCEP:** Preenchimento automático de logradouro, bairro, cidade e UF ao digitar o CEP.
- **Recuperação de Senha Segura:** Envio de e-mail com token criptográfico de uso único e expiração de 1 hora via **PHPMailer**.
- **Notificações Persistentes:** Salvas em tabela no banco de dados e exibidas no sino do cabeçalho com marcação de leitura.
- **Audit Trail (Linha do Tempo):** Histórico completo de aprovações, envios e etapas com data, hora e autor.
- **Múltiplos Projetos por Cliente:** O cliente e a empresa podem alternar facilmente entre projetos vinculados.
- **Modo Escuro (Dark Mode):** Alternância instantânea salva no `localStorage` do navegador com base no Bootstrap 5.3.
- **Máscaras de Digitação:** Formatação automática em inputs para Telefone, CPF, CNPJ, CEP e Moeda (R$).
- **Feedback Visual / Loading:** Botões com estado de carregamento e spinner para evitar cliques duplos.

---

## ⚙ Tecnologias Utilizadas e Justificativa

| Tecnologia | Finalidade | Justificativa |
| :--- | :--- | :--- |
| **PHP 8.2+** | Backend / Lógica de Negócio | Linguagem robusta, tipada, rápida e nativa na grande maioria dos servidores web. |
| **Arquitetura MVC pura** | Organização do Código | Separação clara de responsabilidades (Model, View, Controller) sem complexidade excessiva de frameworks pesados. |
| **MySQL / MariaDB** | Banco de Dados Relacional | Confiabilidade, suporte a transações ACID e integridade referencial com chaves estrangeiras (`ON DELETE CASCADE`). |
| **PDO (PHP Data Objects)** | Camada de Dados | Prevenção nativa contra **SQL Injection** através de consultas preparadas (*Prepared Statements*). |
| **Bootstrap 5.3** | Interface e Estilização | Design moderno, responsivo, acessível e suporte nativo a temas (Light / Dark). |
| **Bootstrap Icons** | Iconografia | Ícones vetoriais leves e consistentes em todo o sistema. |
| **JavaScript Vanilla** | Interatividade Front-end | Máscaras de formulários, alternância de tema, chamadas assíncronas e feedback sem dependência de bibliotecas pesadas. |
| **PHPMailer** | Serviço de E-mail | Biblioteca padrão da indústria para envio seguro de e-mails via SMTP autenticado. |
| **API ViaCEP** | Consulta de Endereço | API pública e gratuita para preenchimento ágil de CEP, melhorando a experiência do usuário. |

---

## 🛡 Medidas de Segurança

- **Proteção contra SQL Injection:** 100% das consultas ao banco utilizam *Prepared Statements* via PDO.
- **Proteção contra XSS (Cross-Site Scripting):** Todas as saídas de dados no front-end são tratadas com `htmlspecialchars($dado, ENT_QUOTES, 'UTF-8')`.
- **Proteção contra CSRF (Cross-Site Request Forgery):** Tokens únicos gerados por sessão e validados em todas as requisições do tipo POST.
- **Criptografia de Senhas:** Hashes seguros gerados com `password_hash($senha, PASSWORD_DEFAULT)` usando o algoritmo **Bcrypt**.
- **Validação de Uploads:** Além da extensão do arquivo, é feita a conferência do cabeçalho binário (MIME type real) através da extensão `fileinfo` (`finfo_file`).
- **Variáveis de Ambiente:** Nenhuma credencial de banco ou servidor fica exposta no código-fonte; todas ficam isoladas no arquivo `.env`.

---

## Manual de Instalação e Configuração

### Pré-requisitos
- **XAMPP** (ou WampServer / Docker) com **PHP 8.1 ou superior** e **MySQL/MariaDB**.
- Extensões PHP ativadas no `php.ini`: `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`.

### Passo a Passo

1. **Clonar ou copiar o repositório:**
   Coloque a pasta do projeto dentro do diretório raiz do servidor web:
   ```bash
   # No XAMPP (Windows):
   C:\xampp\htdocs\projetos\IISoul
   ```

2. **Configurar as Variáveis de Ambiente:**
   Na raiz do projeto, copie o arquivo `.env.example` para `.env`:
   ```bash
   cp .env.example .env
   ```
   Abra o arquivo `.env` e confirme os dados de acesso ao banco (por padrão no XAMPP: usuário `root` e senha vazia):
   ```env
   APP_NOME="iiKliente"
   APP_URL="http://localhost/projetos/IISoul"
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=suamarca_db
   DB_USER=root
   DB_PASS=
   ```

3. **Criar o Banco de Dados e Executar as Migrações:**
   Abra o terminal (PowerShell ou CMD) na pasta do projeto e execute:
   ```bash
   php migrate.php
   ```
   *O script criará automaticamente o banco de dados `suamarca_db`, todas as tabelas, índices e os usuários iniciais de demonstração.*

4. **Acessar o Sistema:**
   Inicie o **Apache** e o **MySQL** no painel do XAMPP e acesse no navegador:
   ```text
   http://localhost/projetos/IISoul
   ```

---

## Guia do Usuário

### Acesso da Empresa (Prestador de Serviço)
1. Acesse `http://localhost/projetos/IISoul/login` e informe o login da empresa.
2. **Dashboard:** Visualize métricas de faturamento, projetos em andamento e solicitações pendentes.
3. **Clientes:** Cadastre novos clientes com preenchimento automático pelo CEP, edite dados ou exclua.
4. **Projetos:** 
   - Acompanhe cada projeto nas 5 etapas do ciclo de vida.
   - Atualize a fase do projeto (Briefing → Wireframe → Dev → Revisão → Finalizado).
   - Cadastre o link do protótipo navegável (Figma/Adobe XD).
   - Gerencie as parcelas do projeto e registre os comprovantes de pagamento.
   - Ao finalizar, exporte o **Termo de Entrega em PDF**.
5. **Solicitações:** Analise novos pedidos de orçamento recebidos pela página inicial e envie orçamentos.

### Acesso do Cliente
1. Acesse o sistema e faça login com a conta de cliente.
2. **Painel Inicial:** Visualize o resumo do seu projeto atual, prazo previsto e status geral.
3. **Acompanhamento do Projeto:**
   - Visualize a etapa atual em que o seu projeto se encontra.
   - Teste o protótipo pelo link enviado pela equipe.
   - Abra solicitações de ajuste caso precise de alguma alteração.
   - Consulte suas parcelas financeiras e envie comprovantes de pagamento.
4. **Questionário de Briefing:** Responda ao questionário inicial detalhando as necessidades do seu projeto.

---

## Contas de Acesso Padrão

Criadas automaticamente pelo script de migração:

| Perfil | E-mail | Senha | Área de Acesso |
| :--- | :--- | :--- | :--- |
| **Empresa (Administrador)** | `admin@suamarca.com` | `12345678` | `/empresa/dashboard` |
| **Cliente de Teste** | `maria@email.com` | `12345678` | `/cliente/inicio` |

*(Você também pode cadastrar novos clientes diretamente pela interface ou pelo formulário de cadastro).*

---

## Estrutura de Pastas do Projeto

```text
IISoul/
├── app/
│   ├── Controllers/         # Controladores da aplicação (Lógica de requisições)
│   │   ├── AuthController.php
│   │   ├── EmpresaController.php
│   │   ├── HomeController.php
│   │   └── PortalClienteController.php
│   ├── Core/                # Núcleo MVC (Roteador, Banco, Autenticação, CSRF, Validações)
│   │   ├── Auth.php
│   │   ├── Controller.php
│   │   ├── Csrf.php
│   │   ├── Database.php
│   │   ├── Mailer.php
│   │   ├── Router.php
│   │   └── UploadValidador.php
│   ├── Models/              # Modelos de dados e regras de negócio com PDO
│   │   ├── Cliente.php
│   │   ├── Notificacao.php
│   │   ├── Projeto.php
│   │   ├── Solicitacao.php
│   │   └── Usuario.php
│   └── Views/               # Telas do sistema organizadas por módulo
│       ├── cliente/         # Telas do portal do cliente
│       ├── empresa/         # Telas do painel administrativo
│       ├── publico/         # Telas de login, cadastro, recuperação e landing page
│       └── comum/           # Modais e componentes compartilhados
├── assets/                  # Arquivos estáticos (CSS, JS, Imagens)
│   ├── css/
│   ├── js/
│   └── img/
├── storage/                 # Diretório de uploads e arquivos de log
│   ├── logs/
│   └── uploads/
├── vendor/                  # Dependências instaladas via Composer (PHPMailer)
├── .env.example             # Modelo de configuração de ambiente
├── .gitignore               # Arquivos ignorados pelo controle de versão
├── index.php                # Ponto de entrada único da aplicação (Front Controller)
├── migrate.php              # Script de criação do banco e dados iniciais
└── README.md                # Documentação completa do projeto
```

---

## Autor

Desenvolvido por **Vitor Hugo Dos Santos Pedrini**  
E-mail: [vitorh.pedrini@gmail.com](mailto:vitorh.pedrini@gmail.com)  
Projeto avaliativo para a plataforma **iisoul**.
