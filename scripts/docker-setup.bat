@echo off
REM
REM

cd /d "%~dp0\.."

echo >>> Copiando .env...
if not exist .env copy .env.example .env

echo >>> Gerando chave da aplicação...
docker compose run --rm app php artisan key:generate

echo >>> Subindo containers...
docker compose up -d

echo >>> Aguardando MySQL...
timeout /t 5 /nobreak > nul

echo >>> Rodando migrations...
docker compose exec app php artisan migrate --force

echo >>> Populando categorias...
docker compose exec app php artisan db:seed --force

echo.
echo >>> Pronto! API disponível em: http://localhost:8000
echo >>> Documentação Swagger: http://localhost:8000/api/docs
echo.
pause
