#  Guia do Usuário

Bem-vindo ao **iiKliente - Sistema de Gestão de Clientes**. Este documento explica como utilizar todas as telas e recursos do sistema de maneira simples e intuitiva.

---

##  1. Página Inicial (Landing Page) - `index.html`

A página inicial é o cartão de visita público da empresa, projetada para converter visitantes em clientes.

### Recursos disponíveis:
1. **Menu Superior de Navegação:** Permite rolar suavemente até as seções *Funcionalidades*, *Como Funciona*, *Áreas do Sistema* e *FAQ*.
2. **Botões de Acesso Rápido:**
   - **Entrar:** Leva diretamente à tela de login na aba de autenticação.
   - **Solicitar Orçamento:** Leva à tela de cadastro de novos clientes.
3. **Seção "Como Funciona":** Passo a passo visual demonstrando as 5 fases do projeto (Solicitação ➔ Análise ➔ Orçamento ➔ Projeto ➔ Entrega).
4. **Simulador de Orçamento Interativo:**
   - O usuário seleciona o **Tipo de Projeto** (ex: *Website institucional*, *Loja virtual*, *App mobile*).
   - Escolhe a quantidade de **Páginas / Módulos**.
   - Marca **Recursos Adicionais** (ex: *Área do cliente*, *Painel administrativo*, *Integrações externas*).
   - O **Valor Estimado** é recalculado automaticamente em reais (R$) na lateral do card.
5. **Perguntas Frequentes (FAQ):** Acordeão expansível do Bootstrap que responde dúvidas recorrentes.

---

##  2. Autenticação e Cadastro - `login.html`

Tela unificada para login e criação de contas com abas dinâmicas.

### Como entrar na conta:
1. Digite seu **E-mail** e **Senha** cadastrados.
2. Use o botão com ícone de olho (`<i class="bi bi-eye"></i>`) para visualizar ou ocultar os caracteres da senha.
3. Clique em **Entrar na minha conta**. O sistema identifica automaticamente o seu perfil no banco de dados e redireciona para o painel correspondente:
   - Perfil **Cliente** (`maria@email.com` / `12345678`) ➔ `cliente/inicio.html`
   - Perfil **Empresa** (`admin@suamarca.com` / `12345678`) ➔ `empresa/dashboard.html`

### Como criar uma nova conta:
1. Clique na aba superior **Criar conta**.
2. Preencha Nome Completo, E-mail, Senha e Confirmação de Senha.
3. O sistema valida se as duas senhas conferem e se possuem o mínimo de 8 caracteres.
4. Marque a caixa de aceite dos Termos de Uso e clique em **Criar minha conta**. O sistema cria automaticamente seu usuário, sua ficha de cliente e seu projeto inicial em uma transação ACID.

### Esqueci minha senha:
- Clique no link **Esqueci minha senha?** para abrir o modal de recuperação por e-mail.

---

##  3. Painel da Empresa (`empresa/`)

Ambiente restrito para a gestão operacional e estratégica da empresa prestadora de serviços.

### 3.1. Dashboard - `empresa/dashboard.html`
- **Métricas do Negócio:** Cards informativos com total de Clientes Ativos, Novas Solicitações, Orçamentos sem Resposta e Projetos em Andamento (com percentuais de crescimento).
- **Gráficos Interativos (Chart.js):**
  - *Clientes novos por mês:* Gráfico de barras com seletor de período (últimos 12 ou 6 meses).
  - *Projetos por etapa:* Gráfico de rosca exibindo a distribuição entre planejamento, desenvolvimento, revisão e aprovação.
- **Painel "Precisa de Atenção":** Lista de pendências urgentes com badges de prioridade (prazos a vencer, aprovações pendentes).
- **Tabela de Clientes Recentes:** Acesso rápido aos últimos cadastros realizados.

### 3.2. Gestão de Clientes (CRUD) - `empresa/clientes.html`
- **Pesquisa em Tempo Real:** Campo de busca que filtra por razão social, e-mail ou CNPJ enquanto você digita.
- **Filtros Combinados:** Filtragem por *Status* (Ativo, Prospecto, Inativo) e por *Segmento* (Comércio, Saúde, Educação, Tecnologia).
- **Cadastrar Cliente:** Clique em **+ Novo cliente** para abrir o modal completo com validação de campos e máscara automática de CNPJ e Telefone.
- **Editar Cliente:** Clique no ícone de lápis (`<i class="bi bi-pencil"></i>`) em qualquer linha para abrir o modal pré-preenchido com os dados do cliente selecionado.
- **Excluir Cliente:** Clique no ícone de lixeira (`<i class="bi bi-trash"></i>`) para abrir a caixa de confirmação de exclusão segura.
- **Ver Ficha:** O botão de olho (`<i class="bi bi-eye"></i>`) abre a ficha detalhada do cliente (`cliente.html`).

### 3.3. Ficha do Cliente - `empresa/cliente.html`
- Cabeçalho com dados cadastrais principais, links diretos para e-mail e WhatsApp.
- Abas de navegação interna:
  - **Visão Geral:** Indicadores de projetos, orçamentos, dados da empresa, contatos e próximos passos agendados.
  - **Histórico:** Linha do tempo com todas as interações e registros passados.
  - **Projeto:** Status e barra de progresso do projeto em execução.
  - **Orçamentos:** Tabela com todos os orçamentos emitidos para este cliente.

### 3.4. Solicitações e Orçamentos - `empresa/solicitacoes.html`
- **Lista de Solicitações:** Exibe todas as mensagens e pedidos de orçamento recebidos de potenciais clientes.
- **Painel Lateral de Detalhes:** Ao clicar em qualquer linha da tabela, o painel à direita é atualizado imediatamente com todas as informações do solicitante, respostas do questionário e estimativas.
- **Ações Rápidas:**
  - **Enviar Orçamento:** Abre modal para informar valor, data de validade e observações.
  - **Converter em Cliente:** Transforma o prospecto em cliente cadastrado com um clique.
  - **Recusar:** Permite recusar solicitações inviáveis.

### 3.5. Configurações - `empresa/configuracoes.html`
- Formulário para editar dados cadastrais da empresa (Nome, CNPJ, Telefone, E-mail institucional).
- Envio do arquivo de Logotipo com validação de formato e tamanho máximo.
- Chaves de notificação e formulário para alteração da senha de acesso.

---

##  4. Painel do Cliente (`cliente/`)

Ambiente intuitivo e transparente para o cliente acompanhar seu projeto sem fricção.

### 4.1. Início do Cliente - `cliente/inicio.html`
- Resumo do projeto contratado e valor do orçamento oficial aprovado.
- Previsão de entrega e contagem regressiva de dias restantes.
- Bloco **"Próxima ação para você"**: Indica claramente o que a equipe aguarda por parte do cliente (ex: envio de textos e imagens).
- Linha do tempo cronológica das etapas concluídas e em andamento.

### 4.2. Acompanhamento do Projeto - `cliente/projeto.html`
- Indicador visual das 5 etapas do projeto com checkmarks nas fases concluídas.
- **Módulo de Aprovação de Etapa:** Permite ao cliente clicar em **Aprovar** para autorizar o início da próxima fase ou **Solicitar ajuste** detalhando o que deve ser modificado.
- **Arquivos e Mockups:** Lista de links para visualização e download de arquivos (wireframes, especificações, manuais).
- **Upload de Arquivos:** Formulário simples para envio de materiais do cliente (suporta `.pdf`, `.png`, `.jpg`, `.docx`, `.zip` até 5 MB).

### 4.3. Orçamento e Questionário - `cliente/questionario.html`
- Questionário de briefing detalhado contendo tipo de projeto, objetivo principal, público-alvo, módulos necessários (checkboxes), links de referência e prazo desejado.
- Contador de caracteres em tempo real para observações.
- Estimativa de investimento recalculada automaticamente com base no tipo escolhido.

### 4.4. Perfil do Cliente - `cliente/perfil.html`
- Atualização de nome, e-mail, telefone comercial, nome da empresa e foto de perfil.
- Painel para redefinição de senha pessoal.

---

##  5. Responsividade e Uso em Dispositivos Móveis
- Todas as telas contam com menu lateral que se recolhe em formato de gaveta (**Offcanvas**) em telas de smartphones e tablets.
- As tabelas possuem rolagem horizontal suave (`table-responsive`) para evitar quebra de layout.
- Os formulários adaptam suas colunas de 1 a 2 campos por linha automaticamente conforme o tamanho do visor.
