# Script de Instalação - Live Reload para MAPOS Mobile
# PowerShell Script (Windows)

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Instalando Live Reload para MAPOS Mobile" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se Node.js está instalado
Write-Host "Verificando Node.js..." -ForegroundColor Yellow
$nodeVersion = node --version 2>$null
if (-not $nodeVersion) {
    Write-Host "ERRO: Node.js nao esta instalado!" -ForegroundColor Red
    Write-Host "Por favor, instale o Node.js em: https://nodejs.org/"
    exit 1
}
Write-Host "Node.js encontrado: $nodeVersion" -ForegroundColor Green
Write-Host ""

# Criar package.json se nao existir
$packageJsonPath = "..\package.json"
if (-not (Test-Path $packageJsonPath)) {
    Write-Host "Criando package.json..." -ForegroundColor Yellow
    $packageJson = @{
        name = "mapos-mobile-live-reload"
        version = "1.0.0"
        description = "Live reload para desenvolvimento MAPOS Mobile"
        scripts = @{
            "dev" = "concurrently `"npm:watch:php`" `"npm:watch:assets`""
            "watch:php" = "browser-sync start --proxy 'http://localhost/MaposV5' --files '..\application\**\*.php', '..\application\**\*.html', '..\assets\**\*' --port 3000 --no-open"
            "watch:assets" = "browser-sync start --proxy 'http://localhost/MaposV5' --files '..\assets\css\*.css', '..\assets\js\*.js' --port 3001 --no-open"
            "watch:mobile" = "echo 'Iniciar React Native: npx react-native start'"
        }
        devDependencies = @{
            "browser-sync" = "^2.29.3"
            "concurrently" = "^8.2.2"
            "nodemon" = "^3.0.2"
        }
    }

    $packageJson | ConvertTo-Json -Depth 3 | Out-File $packageJsonPath -Encoding UTF8
    Write-Host "package.json criado!" -ForegroundColor Green
}

Write-Host ""
Write-Host "Instalando dependencias..." -ForegroundColor Yellow
Write-Host "Isso pode levar alguns minutos..." -ForegroundColor Gray

# Instalar dependencias
Set-Location ..
npm install

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host "Instalacao concluida com sucesso!" -ForegroundColor Green
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Comandos disponiveis:" -ForegroundColor Cyan
    Write-Host "  npm run dev         - Inicia live reload PHP + Assets" -ForegroundColor White
    Write-Host "  npm run watch:php   - Inicia apenas live reload PHP" -ForegroundColor White
    Write-Host "  npm run watch:assets- Inicia apenas live reload CSS/JS" -ForegroundColor White
    Write-Host ""
    Write-Host "Acesse no navegador: http://localhost:3000" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Dica: Baixe a extensao 'Live Server' para VSCode:" -ForegroundColor Gray
    Write-Host "  Nome: Live Server (ritwickdey)" -ForegroundColor Gray
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "ERRO: Falha na instalacao das dependencias!" -ForegroundColor Red
    Write-Host "Tente executar manualmente: npm install" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Pressione qualquer tecla para sair..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
