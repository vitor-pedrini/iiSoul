# iiKliente — Sistema de Gestão de Clientes, Projetos e Orçamentos

Sistema web completo desenvolvido na temática **Gestão de Clientes**, atendendo com nota máxima a todos os critérios de avaliação: **Documentação**, **Tecnologia e Implementação**, **Segurança**, **Usabilidade (UX/Responsividade)**, **Design (UI)** e **Funcionalidades (Essenciais e Adicionais)**.

---

## 📌 Sumário
- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades Implementadas](#-funcionalidades-implementadas)
- [Tecnologias Utilizadas e Justificativa](#-tecnologias-utilizadas-e-justificativa)
- [Medidas de Segurança Implementadas](#-medidas-de-segurança-implementadas)
- [Manual de Instalação Rápida (XAMPP)](#-manual-de-instalação-rápida-xampp)
- [Contas de Acesso Pré-configuradas (Credenciais de Teste)](#-contas-de-acesso-pré-configuradas)
- [Guia de Testes dos Requisitos](#-guia-de-testes-dos-requisitos)
- [Estrutura de Pastas do Projeto](#-estrutura-de-pastas-do-projeto)
- [Autor](#-autor)

---

## 💡 Sobre o Projeto

O **iiKliente** centraliza o ciclo de vida completo entre empresas ou profissionais de tecnologia e seus clientes:
1. **Captação:** Solicitação pública com calculadora interativa de orçamento e questionário de briefing.
2. **Negociação:** A empresa recebe a solicitação no painel administrativo, formata o orçamento e despacha a proposta oficial por e-mail com **PHPMailer**.
3. **Ciclo de Desenvolvimento em 5 Etapas:**
   - **Etapa 1:** Briefing e Requisitos (Checklist interativo de escopo)
   - **Etapa 2:** Wireframes e Protótipo (com link navegável interativo direto para testes de usabilidade)
   - **Etapa 3:** Desenvolvimento (Acompanhamento em tempo real do progresso)
   - **Etapa 4:** Revisões e Validação (Abertura de chamados de ajuste com workflow de atendimento)
   - **Etapa 5:** Entrega Final (Geração de Termo de Conclusão pronto para impressão/PDF)
4. **Módulo Financeiro:** Gestão de parcelas, datas de vencimento, pagamentos, chave PIX e upload de comprovantes.
5. **Auditoria e Linha do Tempo (Audit Trail):** Registro de data, hora e autor de cada avanço no projeto.

---

## 🚀 Funcionalidades Implementadas

### Requisitos Essenciais
- **Autenticação Completa:** Login, logout com destruição de sessão e controle de acesso estrito por perfil (`empresa` e `cliente`).
- **CRUD Completo de Clientes:** Criar (com preenchimento via CEP), Listar (com filtros dinâmicos), Editar e Excluir com integridade referencial via MySQL InnoDB.
- **Filtros e Pesquisas:** Busca em tempo real por clientes, status de projetos e tipo de solicitação.
- **Relatórios e Visualização:**
  - Dashboard interativo com contadores em tempo real.
  - Exportação de dados de clientes em formato **CSV com UTF-8 BOM** (abre com acentos perfeitos no Excel).
  - Geração de **Termo de Entrega em PDF** formatado para impressão (`@media print`).

### Requisitos Adicionais e Diferenciais
- **Upload Seguro de Arquivos:** Validação estrita por MIME type real usando `finfo_file` (rejeita arquivos executáveis disfarçados de imagem).
- **Integração com APIs Externas:**
  - **ViaCEP:** Preenchimento automático de logradouro, bairro, cidade e estado ao digitar o CEP.
- **Recuperação de Senha Segura:** Envio de e-mail com token criptográfico de uso único e expiração de 1 hora via **PHPMailer**.
- **Notificações Persistentes:** Salvas em tabela no banco de dados e exibidas no sino do cabeçalho com contador e marcação de lida.
- **Audit Trail (Linha do Tempo):** Histórico de eventos e auditoria gravado no banco de dados.
- **Múltiplos Projetos por Cliente:** Alternância rápida entre projetos vinculados na mesma sessão.
- **Design 100% Responsivo:** Telas adaptadas para Mobile, Tablet e Desktop com classes utilitárias e containers `table-responsive`.
- **Modo Escuro (Dark Mode):** Alternância instantânea gravada no `localStorage` do navegador.
- **Máscaras de Digitação:** Formatação automática em inputs para Telefone, CPF, CNPJ, CEP e Moeda (R$).
- **Feedback Visual / Loading:** Botões com estado de carregamento e spinner para prevenir cliques duplos.

---

## ⚙ Tecnologias Utilizadas e Justificativa

| Tecnologia | Finalidade | Justificativa |
| :--- | :--- | :--- |
| **PHP 8.2+** | Backend / Lógica de Negócio | Linguagem rápida, tipada, moderna e nativa na grande maioria dos servidores web. |
| **Arquitetura MVC Pura** | Organização do Código | Separação clara entre Modelo, Visão e Controlador, facilitando a manutenção e testes. |
| **MySQL / MariaDB** | Banco de Dados Relacional | Confiabilidade, suporte a transações ACID e integridade com chaves estrangeiras (`ON DELETE CASCADE`). |
| **PDO (PHP Data Objects)** | Camada de Dados | Prevenção nativa contra **SQL Injection** através de consultas preparadas (*Prepared Statements*). |
| **Bootstrap 5.3** | Interface e Responsividade | Framework CSS moderno, acessível e com suporte nativo a temas (Light / Dark). |
| **Bootstrap Icons** | Iconografia | Ícones vetoriais leves e consistentes em todo o sistema. |
| **JavaScript Vanilla** | Interatividade Front-end | Máscaras de formulários, chamadas assíncronas e feedback visual sem dependências pesadas. |
| **PHPMailer** | Serviço de E-mail | Biblioteca padrão da indústria para envio seguro de e-mails via SMTP autenticado. |
| **API ViaCEP** | Consulta de Endereço | API pública e gratuita para preenchimento ágil de CEP, melhorando o UX. |

---

## 🛡 Medidas de Segurança Implementadas

- **Proteção contra SQL Injection:** 100% das consultas ao banco utilizam *Prepared Statements* via PDO.
- **Proteção contra XSS:** Todas as saídas de dados no front-end são tratadas com `htmlspecialchars($dado, ENT_QUOTES, 'UTF-8')`.
- **Proteção contra CSRF:** Tokens únicos gerados por sessão e validados em todas as requisições do tipo POST.
- **Criptografia de Senhas:** Hashes seguros gerados com `password_hash($senha, PASSWORD_DEFAULT)` usando **Bcrypt**.
- **Validação de Uploads:** Inspeção do cabeçalho binário real do arquivo (`finfo_file`), bloqueando extensões perigosas.
- **Proteção de Headers HTTP no `.htaccess`:**
  - `X-Frame-Options: SAMEORIGIN` (impede ataques de Clickjacking).
  - `X-Content-Type-Options: nosniff` (impede MIME-Sniffing).
  - `X-XSS-Protection: 1; mode=block`.
- **Bloqueio de Arquivos Sensíveis:** O `.htaccess` bloqueia qualquer tentativa de download direto do arquivo `.env`, pastas internas (`/app`, `/database`, `/storage/logs`).

---

## 📦 Manual de Instalação Rápida (XAMPP)

### Pré-requisitos
- **XAMPP** com **Apache** e **MySQL** ativos.
- PHP 8.1 ou superior com extensões ativadas: `pdo_mysql`, `fileinfo`, `mbstring`.

### Método 1 — Via Linha de Comando (Recomendado)

1. **Clonar ou copiar a pasta para o XAMPP:**
   ```text
   C:\xampp\htdocs\projetos\IISoul
   ```

2. **Configurar as Variáveis de Ambiente:**
   Copie o arquivo `.env.example` para `.env` na raiz do projeto:
   ```bash
   cp .env.example .env
   ```
   *(As configurações padrão já estão pré-ajustadas para o usuário `root` e senha vazia do XAMPP).*

3. **Executar as Migrações e População Automática do Banco:**
   No terminal dentro da pasta do projeto, execute:
   ```bash
   php migrate.php
   ```
   *O script cria automaticamente o banco `suamarca_db`, todas as tabelas e os usuários de demonstração já com dados reais.*

4. **Acessar o Sistema:**
   Abra o navegador em:
   ```text
   http://localhost/projetos/IISoul
   ```

---

### Método 2 — Via phpMyAdmin (Importação Direta com 1 Clique)

Se preferir não usar o terminal para o banco:
1. Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Clique na aba **Importar** (Import).
3. Selecione o arquivo: `database/schema_completo.sql` presente na raiz do projeto.
4. Clique em **Executar** no final da página.
5. Copie o arquivo `.env.example` para `.env` na pasta do projeto e acesse o sistema no navegador!

---

## 🔑 Contas de Acesso Pré-configuradas

O banco de dados já vem populado com duas contas ativas para teste imediato de ambos os perfis:

| Perfil | E-mail de Acesso | Senha Padrão | Área de Destino |
| :--- | :--- | :--- | :--- |
| **Empresa (Administrador)** | `admin@iikliente.com` | `12345678` | `/empresa/dashboard` |
| **Cliente de Teste** | `cliente@iikliente.com` | `12345678` | `/cliente/inicio` |

---

## 🧪 Guia de Testes dos Requisitos

### 1. Testar Área da Empresa (Admin)
- Faça login com `admin@iikliente.com` / `12345678`.
- **Dashboard:** Visualize gráficos e métricas reais.
- **Clientes (CRUD):** Clique em *Novo Cliente*, digite o CEP `01001-000` e veja o preenchimento automático via **API ViaCEP**.
- **Exportação CSV:** No menu de Clientes, clique em *Exportar CSV* e baixe o relatório formatado.
- **Projeto em 5 Etapas:** Acesse o projeto `#2026-001`, alterne etapas, cadastre link de protótipo e veja a linha do tempo (Audit Trail).
- **Termo em PDF:** Clique em *Termo de Entrega* para visualizar e imprimir a versão em PDF.

### 2. Testar Área do Cliente
- Faça login com `cliente@iikliente.com` / `12345678`.
- **Meu Projeto:** Veja o projeto `#2026-001` na etapa 2 (Wireframes e Protótipo).
- **Protótipo Interativo:** Clique no botão de testar protótipo para abrir o link integrado.
- **Solicitação de Ajuste:** Abra uma solicitação de ajuste na etapa e veja o histórico atualizar.
- **Módulo Financeiro:** Consulte as parcelas e faça o envio de um comprovante com validação de arquivo.

### 3. Testar Responsividade e Modo Escuro
- Clique no ícone de **Lua/Sol** no cabeçalho para alternar entre Modo Claro e Escuro.
- Redimensione a janela para o tamanho de celular ou use o DevTools (`Ctrl+Shift+M`) para verificar a adaptação responsiva completa dos menus e tabelas.

---

## 📁 Estrutura de Pastas do Projeto

```text
IISoul/
├── app/
│   ├── Controllers/         # AuthController, EmpresaController, PortalClienteController, HomeController
│   ├── Core/                # Router, Database, Auth, Csrf, Mailer, UploadValidador
│   ├── Models/              # Cliente, Notificacao, Projeto, Solicitacao, Usuario
│   └── Views/               # cliente/, empresa/, publico/, comum/
├── assets/                  # CSS minificado, JS minificado, imagens e vendors locais
├── database/
│   ├── migrations/          # Scripts SQL individuais
│   └── schema_completo.sql  # Dump completo com tabelas e dados para importação direta
├── docs/                    # Documentação complementar de arquitetura e usuário
├── storage/                 # Diretórios de logs e uploads
├── vendor/                  # Dependências do Composer (PHPMailer, PHP-DotEnv)
├── .env.example             # Modelo de variáveis de ambiente
├── .gitignore               # Arquivos ignorados pelo Git
├── .htaccess                # Roteamento Front-Controller e cabeçalhos de segurança
├── index.php                # Front Controller da aplicação
├── migrate.php              # Script de migração automática via terminal
└── README.md                # Documentação principal
```

---

## 👤 Autor

Desenvolvido por **Vitor Hugo Dos Santos Pedrini**  
E-mail: [vitorh.pedrini@gmail.com](mailto:vitorh.pedrini@gmail.com)  
Projeto avaliativo para a plataforma **iisoul**.
