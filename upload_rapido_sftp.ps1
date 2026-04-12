#!/usr/bin/env pwsh
# ==========================================
# UPLOAD RAPIDO SFTP - MAPOS V5
# ==========================================
# Servidor: ftp.jj-ferreiras.com.br
# Destino: /home/jj-ferreiras/www/mapos3
# ==========================================

param(
    [string]$Servidor = "ftp.jj-ferreiras.com.br",
    [string]$Usuario = "jj-ferreiras",
    [string]$Senha = "93982740tT",
    [string]$PastaRemota = "/home/jj-ferreiras/www/mapos3",
    [string]$PastaLocal = "$PSScriptRoot"
)

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  UPLOAD SFTP - MAPOS V5" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Servidor: $Servidor" -ForegroundColor Gray
Write-Host "Destino: $PastaRemota" -ForegroundColor Gray
Write-Host "Origem: $PastaLocal" -ForegroundColor Gray
Write-Host ""

# Verificar se esta na pasta correta
if (-not (Test-Path "$PastaLocal\index.php")) {
    Write-Host "ERRO: Execute este script na pasta raiz do mapos!" -ForegroundColor Red
    exit 1
}

# Criar lista de exclusoes (arquivos nao necessarios)
$excluir = @(
    ".git",
    ".claude",
    ".github",
    "*.tar.gz",
    "*.log",
    "deploy.bat",
    "deploy.ps1",
    "upload_*.ps1"
)

Write-Host "[1/3] Preparando arquivos..." -ForegroundColor Yellow

# Metodo 1: Usar rsync via WSL (se disponivel)
$hasWSL = Get-Command wsl -ErrorAction SilentlyContinue
if ($hasWSL) {
    Write-Host "      Usando WSL + rsync (mais rapido)..." -ForegroundColor Green

    $excludePattern = ($excluir | ForEach-Object { "--exclude='$_'" }) -join ' '
    $comando = "cd '$PastaLocal' && rsync -avz --progress $excludePattern -e 'sshpass -p '$Senha' ssh -o StrictHostKeyChecking=no' . $Usuario@${Servidor}:${PastaRemota}/"

    wsl bash -c $comando
}
# Metodo 2: Usar sftp nativo do Windows 10/11
else {
    Write-Host "      Usando SFTP nativo do Windows..." -ForegroundColor Yellow

    # Criar script de comandos SFTP
    $sftpCommands = @"
cd /home/jj-ferreiras/www
mkdir mapos3 2>/dev/null || true
cd mapos3
put -r "$PastaLocal/*" .
chmod 777 application/logs 2>/dev/null || true
chmod 777 application/cache 2>/dev/null || true
chmod 777 assets/uploads 2>/dev/null || true
bye
"@

    $sftpScript = "$env:TEMP\sftp_commands.txt"
    $sftpCommands | Out-File -FilePath $sftpScript -Encoding ASCII

    Write-Host ""
    Write-Host "[2/3] Iniciando upload (isso pode levar alguns minutos)..." -ForegroundColor Yellow

    # Usar sftp com arquivo de comandos
    $process = Start-Process -FilePath "sftp" -ArgumentList "-oBatchMode=no -b `"$sftpScript`" ${Usuario}@${Servidor}" -Wait -PassThru -NoNewWindow

    Remove-Item $sftpScript -ErrorAction SilentlyContinue
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Green
Write-Host "  UPLOAD CONCLUIDO!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Acesse: https://jj-ferreiras.com.br/mapos3" -ForegroundColor Cyan
Write-Host ""
Read-Host "Pressione ENTER para sair"
