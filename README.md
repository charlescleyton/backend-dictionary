# App Dictionary
## Referência
Esse projeto tem como objetivo exibir termos em inglês e gerenciar as palavras visualizadas, trata-se de um projeto para teste tecnico para a vaga na empresa For People de PHP/Laravel Developer Pleno.

Um projeto bem interessante, que tem como alguns de desafios: autenticação de usuário com uso do JWT (Json Web Token), consumo de APi para listar palavras em inglês, guardar e acessar histórico de palavras pesquisadas, guardar e apagar palavras como favoritas, baixar a lista de palavras de um repósitório externo e importar estas palavras para o banco de dados, salvar em cache o resultado das requisições a API, para agilizar a resposta em caso de buscas com parâmetros repetidos entre outros.

# App Dictionary
Esse projeto tem como objetivo exibir termos em inglês e gerenciar as palavras visualizadas, trata-se de um projeto para teste tecnico para a vaga na empresa For People de PHP/Laravel Developer Pleno.


## Tecnologias Usadas

- **PHP 8.1.10**
- **Laravel 10.48.26**
- **MySQL**
- **Redis**
- **Git**
- **HTTP Client (Laravel HTTP Client / Guzzle)**

## Como Instalar e Usar o Projeto

### Requisitos

Antes de rodar o projeto, certifique-se de ter o seguinte instalado:

- **PHP 8.1.10 ou superior**
- **Composer**
- **MySQL**
- **Git**
- **Laragon ou outro servidor de desenvolvimento PHP** (se estiver usando um ambiente local)

### Passo 1: Clonar o Repositório

Clone o repositório do projeto para a sua máquina local:
```
git clone https://github.com/charlescleyton/backend-dictionary.git 

```

### Passo 2: Instalar Dependências
Navegue até o diretório do projeto e instale as dependências com o Composer:
```
cd backend-dictionary
composer install
```

### Passo 3: Configurar o Ambiente
Copie o arquivo .env.example para .env e configure suas credenciais de banco de dados:
```
cp .env.example .env
```
Edite o arquivo .env para configurar as variáveis do banco de dados:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco_de_dados
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### Passo 4: Gerar a Chave de Aplicação e a chave JWT 
Gere a chave de aplicação do Laravel:
```
php artisan key:generate
php artesão jwt :secret
```

### Passo 5: Rodar as Migrações
Crie as tabelas no banco de dados:
```
php artisan migrate
```

### Passo 6: Rodar o Servidor
Inicie o servidor de desenvolvimento:
```
php artisan serve
```

### Passo 7: Rodar os testes unitários
Iniciar os teste unitários:
```
php artisan test
```

### Passo 8: Acessar os endpoints 
Acesse o link abaixo para consultar os endpoints disponíveis na API, incluindo a criação de usuário, autenticação e outras funcionalidades:

[Documentação Dictionary](https://app.swaggerhub.com/apis/CharlesPereira/Dictionary/1.0.0#/)

### Passo 9:  Verificar dados no Banco de Dados
Você pode verificar as palavras inseridas no banco de dados acessando a tabela criada no seu MySQL.

Contato para mais informações [Charles Pereira](https://github.com/charlescleyton)

>  This is a challenge by [Coodesh](https://coodesh.com/)
