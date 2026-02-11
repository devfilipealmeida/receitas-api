# Setup inicial para rodar a API com Docker (PowerShell).
# Uso: .\scripts\docker-setup.ps1

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot\..

Write-Host ">>> Copiando .env..." -ForegroundColor Cyan
if (-not (Test-Path .env)) { Copy-Item .env.example .env }

Write-Host ">>> Gerando chave da aplicação..." -ForegroundColor Cyan
docker compose run --rm app php artisan key:generate

Write-Host ">>> Subindo containers..." -ForegroundColor Cyan
docker compose up -d

Write-Host ">>> Aguardando MySQL..." -ForegroundColor Cyan
Start-Sleep -Seconds 5

Write-Host ">>> Rodando migrations..." -ForegroundColor Cyan
docker compose exec app php artisan migrate --force

Write-Host ">>> Populando categorias..." -ForegroundColor Cyan
docker compose exec app php artisan db:seed --force

Write-Host ""
Write-Host ">>> Pronto! API disponível em: http://localhost:8000" -ForegroundColor Green
Write-Host ">>> Documentação Swagger: http://localhost:8000/api/docs" -ForegroundColor Green
Write-Host ""
