#  Manual de Instalação — SuaMarca (Gestão de Clientes)

Este manual explica passo a passo como instalar e rodar o sistema **SuaMarca** no seu computador usando o **XAMPP** (Apache + PHP + MySQL/MariaDB) e o **Composer**.

---

## 1. Pré-requisitos

- **XAMPP** instalado (com **Apache** e **MySQL** iniciados no painel do XAMPP).
- **PHP 8.0 ou superior** (já vem incluído no XAMPP).
- **Composer** instalado para gerenciar os pacotes do projeto.

---

## 2. Passo a Passo da Instalação

### Passo 1 — Colocar o projeto na pasta do XAMPP
Verifique se a pasta do projeto está dentro do diretório `htdocs` do XAMPP:

```text
C:\xampp\htdocs\projetos\IISoul
```

### Passo 2 — Configurar o arquivo `.env`
As credenciais do banco de dados e de envio de e-mail ficam separadas do código-fonte no arquivo `.env`.
Se o arquivo `.env` ainda não existir, copie o arquivo `.env.example` para `.env`:

```powershell
copy .env.example .env
```

Valores padrão configurados para o XAMPP:
```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=suamarca_db
DB_USER=root
DB_PASS=
```

### Passo 3 — Instalar as dependências do Composer
Abra o terminal na pasta `C:\xampp\htdocs\projetos\IISoul` e execute:

```powershell
composer install
```
Isso instala o **PHPMailer** (`phpmailer/phpmailer`), o leitor de variáveis de ambiente (`vlucas/phpdotenv`) e gera o autoload PSR-4 da pasta `app/`.

### Passo 4 — Criar o Banco de Dados e rodar as Migrations
Com o MySQL ligado no XAMPP, execute o comando abaixo no terminal:

```powershell
php migrate.php
```
O script `migrate.php` cria automaticamente o banco `suamarca_db`, executa o arquivo SQL `database/migrations/001_criar_tabelas.sql` e insere os dados iniciais usando **transação ACID**.

### Passo 5 — Acessar no Navegador
Abra o navegador e acesse:

- **Página Inicial:** `http://localhost/projetos/IISoul/index.html`
- **Tela de Login:** `http://localhost/projetos/IISoul/login.html`

---

## 3. Credenciais de Teste

| Perfil | E-mail | Senha |
| :--- | :--- | :--- |
| **Empresa (Administrador)** | `admin@suamarca.com` | `12345678` |
| **Cliente** | `maria@email.com` | `12345678` |
