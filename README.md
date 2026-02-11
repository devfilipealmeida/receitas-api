# Receitas API

API REST em Laravel para cadastro e gestão de receitas por usuário. Inclui autenticação (cadastro, login, logoff), CRUD de receitas e listagem de categorias.

---

## Requisitos

- **PHP** 8.2+
- **Composer** 2.x
- **MySQL** 8.0 (ou uso do container Docker)
- **Docker** e **Docker Compose** (opcional, recomendado para rodar o sistema completo)

---

## Como rodar o sistema

### Opção 1: Com Docker (recomendado)

Basta ter **Docker** e **Docker Compose** instalados. O projeto sobe Nginx, PHP e MySQL; a API fica em **http://localhost:8000**.

1. **Criar o `.env`** (na pasta do projeto):
   ```bash
   cp .env.example .env
   ```

2. **Gerar a chave da aplicação** (via Docker, para não precisar de PHP instalado):
   ```bash
   docker compose run --rm app php artisan key:generate
   ```

3. **Subir os containers**:
   ```bash
   docker compose up -d
   ```

4. **Criar as tabelas e popular categorias** (aguarde alguns segundos após o passo 3):
   ```bash
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan db:seed --force
   ```

Pronto. A API estará em **http://localhost:8000** e a documentação em **http://localhost:8000/api/docs**.

---

### Opção 2: Sem Docker (PHP e MySQL na máquina)

#### 1. Instalar dependências PHP

```bash
composer install
```

#### 2. Configurar o `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Ajuste o banco para o MySQL local:

- `DB_HOST=127.0.0.1` (ou o host do seu MySQL)
- `DB_DATABASE=teste_receitas_rg_sistemas`
- `DB_USERNAME=` e `DB_PASSWORD=` conforme seu MySQL

Crie o banco no MySQL se ainda não existir:

```sql
CREATE DATABASE teste_receitas_rg_sistemas;
```

#### 3. Migrations e seed

```bash
php artisan migrate
php artisan db:seed
```

#### 4. Servidor de desenvolvimento

```bash
php artisan serve
```

A API ficará em **http://localhost:8000** (ou na porta exibida no terminal). Para produção, use um servidor web (Nginx/Apache) apontando o document root para a pasta `public`.

---

## Comandos úteis (Docker)

| Ação | Comando |
|------|--------|
| Subir os containers | `docker compose up -d` |
| Parar os containers | `docker compose down` |
| Ver logs | `docker compose logs -f` |
| Rodar migrations | `docker compose exec app php artisan migrate` |
| Rodar seeds | `docker compose exec app php artisan db:seed` |
| Limpar cache de config | `docker compose exec app php artisan config:clear` |
| Listar rotas | `docker compose exec app php artisan route:list` |
| Abrir shell no container da app | `docker compose exec app bash` |

---

## Testes

Os testes usam PHPUnit e SQLite em memória (configurado em `phpunit.xml`).

**Com Docker:**

```bash
docker compose exec app php artisan test
```

**Sem Docker:**

```bash
php artisan test
```

Para rodar apenas uma suíte ou arquivo:

```bash
php artisan test tests/Feature/Api
php artisan test tests/Unit/Http/Requests
```

---

## Endpoints da API

| Método | Rota | Descrição | Autenticação |
|--------|------|-----------|--------------|
| GET | `/api/categorias` | Listar categorias | Não |
| POST | `/api/usuarios` | Cadastrar usuário | Não |
| POST | `/api/login` | Login (retorna token) | Não |
| POST | `/api/logoff` | Logoff (invalida token) | Bearer |
| GET | `/api/receitas` | Listar receitas do usuário (filtros: `categoria`, `nome`) | Bearer |
| POST | `/api/receitas` | Cadastrar receita | Bearer |
| GET | `/api/receitas/{id}` | Exibir/impressão de uma receita | Bearer |
| PUT/PATCH | `/api/receitas/{id}` | Editar receita | Bearer |
| DELETE | `/api/receitas/{id}` | Excluir receita | Bearer |

**Documentação interativa (Swagger):** http://localhost:8000/api/docs  

Na Swagger UI é possível testar todos os endpoints e usar **Authorize** com o token retornado pelo login (`Bearer <token>`).

---

## Estrutura do projeto (resumo)

- `app/Http/Controllers/Api/` – Controllers da API (Auth, Categoria, Receita, Swagger)
- `app/Models/` – User, Receita, Categoria
- `routes/api.php` – Rotas da API
- `database/migrations/` – Migrations (usuarios, categorias, receitas, personal_access_tokens)
- `database/seeders/` – Seeders (ex.: CategoriaSeeder)
- `storage/api-docs/openapi.yaml` – Especificação OpenAPI (Swagger)
- `docker/` – Dockerfile (PHP) e configuração Nginx
- `tests/Feature/Api/` – Testes de integração da API
- `tests/Unit/` – Testes unitários (Requests, Models)

---

## Licença

Este projeto utiliza o framework [Laravel](https://laravel.com), open-source sob a [licença MIT](https://opensource.org/licenses/MIT).
