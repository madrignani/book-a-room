# Acme

Esse sistema web visa controlar as reservas dos espaços físicos da universidade ACME. Permite que alunos e funcionários realizem o agendamento de salas. Ele faz parte do trabalho final da disciplina **Projeto integrador** do curso **Bacharelado de Sistemas de Informação** do **CEFET/RJ**.

## Autor

- **Giovanni de Oliveira Madrignani** 

## Ambiente de desenvolvimento

Para executar o projeto é necessário ter previamente instalado/configurado no ambiente: 

- [Node.js](https://nodejs.org/) (versão 18 ou superior)
- [PNPM](https://pnpm.io/) (versão 10.11.0)
- [PHP](https://www.php.net/) (versão 8.0 ou superior)
- [Composer](https://getcomposer.org/)
- [MariaDB](https://mariadb.org/) ou MySQL 5.7+

## Dependências

**Front-end (devDependencies listadas no `package.json`)**

No diretório `front`, o projeto utiliza as seguintes dependências:

- `@playwright/test`: ^1.53.2
- `playwright`: ^1.53.2
- `typescript`: ~5.8.3
- `vite`: ^7.0.0
- `vitest`: ^3.2.4
- `@types/node`: ^24.0.15
- `bootstrap`: ^5.3.7
- `dotenv`: ^17.2.0
- `mysql2`: ^3.14.2

**Back-end (require e require-dev do `composer.json`)**

No diretório `back`, a API PHP utiliza as seguintes dependências:

- `php`: ^8.0
- `phputil/router`: dev-main
- `phputil/cors`: dev-main
- `kahlan/kahlan`: ^6.0

## Como executar

1. **Clonar o repositório**

```bash
git clone https://gitlab.com/cefet-nf/pis-2025-1/final/giovanni.git
cd giovanni
```

2. **Configurar o Banco de Dados (MariaDB via phpMyAdmin)**

1. Certifique-se de que o MariaDB e o phpMyAdmin estejam instalados e em execução (por exemplo, via XAMPP, WAMP, Laragon, etc).
2. Acesse o phpMyAdmin em http://localhost/phpmyadmin
3. Crie um novo banco de dados chamado acme no menu à esquerda.
4. Importe os arquivos de estrutura e dados:
    - Clique no banco `acme`.
    - Vá até a aba Importar.
    - Clique em "Escolher arquivo" e selecione o arquivo estrutura.sql localizado na pasta /db na raiz do projeto.
    - Clique em "Executar".
    - Repita o processo para o arquivo dados.sql.
5. Para verificar se você está utilizando o MariaDB, execute o seguinte comando no terminal SQL: 
    > SELECT VERSION();

3. **Instalar Dependências do Back-end**

No diretório `back`:

```bash
cd back
composer install
```

4. **Iniciar a API (Back-end)**

```bash
php -S localhost:8080 
```

5. **Instalar Dependências do Front-end**

No diretório `front`:

```bash
cd front
pnpm install
```

6. **Iniciar o Front-end**

```bash
pnpm dev
```

7. **Acessar o Sistema**

* Front-end: [http://localhost:5173](http://localhost:5173)
* API (back-end): [http://localhost:8080](http://localhost:8080)

## Testes

**Testes e2e com Playwright**
- Certifique-se que a aplicação esteja em execução

```bash
cd front
pnpm run e2e 
```
- Dessa forma executará todos os testes de ponta a ponta

**Testes com Kahlan**
- A aplicação não precisa estar em execução

```bash
cd back
composer test
```
- Assim executará todos os testes de unitários do back-end

## Referências e Recursos Utilizados

- [Composer](https://getcomposer.org/) – Gerenciador de dependências para PHP.
- [Node.js](https://nodejs.org/) – Ambiente de execução JavaScript no back-end.
- [Vite](https://vitejs.dev/) – Ferramenta moderna para bundling e desenvolvimento front-end.
- [Vitest](https://vitest.dev/) – Framework de testes unitários para projetos front-end em TypeScript.
- [Playwright](https://playwright.dev/) – Ferramenta para testes end-to-end automatizados.
- [dotenv](https://github.com/motdotla/dotenv) – Gerenciamento de variáveis de ambiente no front-end.
- [@types/node](https://www.npmjs.com/package/@types/node) – Tipos TypeScript para recursos globais do Node.js.
- [Kahlan](https://kahlan.github.io/) – Framework de testes orientado a BDD para PHP.
- [phputil/router](https://github.com/thiagodp/router) – Biblioteca de roteamento simples para APIs PHP.
- [phputil/cors](https://github.com/phputil/cors) – Middleware de suporte a CORS em aplicações PHP.
- [draw.io (diagrams.net)](https://app.diagrams.net/) – Ferramenta utilizada para criação dos diagramas UML.
- [PHP Manual](https://www.php.net/manual/en/index.php) – Documentação oficial da linguagem PHP


