# Desafio Perfect-Pay

Implementação do desafio proposto pela empresa Perfect-pay

## Descrição

Este projeto em forma de desafio consiste em um aplicativo para gerenciamento de vendas. Ele permite ao usuário cadastrar informações de Clientes, Produtos e Vendas feitas em seu negócio.

## Funcionalidades

- `CRUD Produtos`: Operações básicas para a tabela produto.
- `CRUD Clientes`: Operações básicas para a tabela clientes.
- `CRUD Vendas`: Operações básicas para a tabela vendas. Em breve será colocada algumas validações para garantir a lógica de negócio das vendas.
- `Autenticação e histórico de opeações`: Em breve irá ser criado uma funcionalidade que permita que vários usuários tenham acesso ao aplicativo e que suas atividades sejam mapeadas e salvas para futuras consultas.

### Pré-requisitos
- Para rodar esse projeto em sua máquina, você precisar ter instalado:

```
PHP versão > 8.2
Composer
Docker
```

###  Instalação
- Rodar os comandos

```
docker compose build
docker compose up
```

e o comando
```
docker compose exec php bash

```

para ter acesso ao container php criado.

- Configurar o .env.example e docker-compose.yml mudando para a conexão com db de sua escolha.

- Dentro do container, rode os comandos:

```
cp .env.example .env
```

```
php artisan key:generate
```

- Crie as tabelas com migrations:

```
 php artisan migrate
```

- Criar alguns dados falsos para testes utilizando o comando

```
php artisan db:seed
```

- Por fim, basta rodar o comando ir até localhost:8088 e usar a aplicação.

##



## ⚙️ Executando os testes

- Em desenvolvimento...

###  Analise os testes de ponta a ponta

- Como já foi dito, execulte o comando

```
php artisan db:seed
```
para criar alguns dados falsos a fim de testar a aplicação localmente com alguns dados criados pelo Faker PHP.

##  Construído com

Ferramentas utilizadas no projeto

* [PHP](https://www.php.net/) - Linguagem de programção
* [MySql](https://www.mysql.com/) - Banco de Dados
* [Laravel](https://laravel.com/) - O framework web usado
* [Docker](https://www.docker.com/) - Configuração do ambiente de desenvolvimento
* [Composer](https://getcomposer.or) - Gerente de Dependências
* [Faker PHP](https://fakerphp.org/) - Gerador de dados falsos
