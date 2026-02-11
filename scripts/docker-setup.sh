#!/usr/bin/env bash
# Setup inicial para rodar a API com Docker.
# Uso: ./scripts/docker-setup.sh   (ou: bash scripts/docker-setup.sh)

set -e
cd "$(dirname "$0")/.."

echo ">>> Copiando .env..."
test -f .env || cp .env.example .env

echo ">>> Gerando chave da aplicação..."
docker compose run --rm app php artisan key:generate

echo ">>> Subindo containers..."
docker compose up -d

echo ">>> Aguardando MySQL..."
sleep 5

echo ">>> Rodando migrations..."
docker compose exec app php artisan migrate --force

echo ">>> Populando categorias..."
docker compose exec app php artisan db:seed --force

echo ""
echo ">>> Pronto! API disponível em: http://localhost:8000"
echo ">>> Documentação Swagger: http://localhost:8000/api/docs"
echo ""
