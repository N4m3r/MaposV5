@echo off
chcp 65001 >nul
title Upload SFTP - MAPOS V5
cls

echo ==========================================
echo   UPLOAD SFTP - MAPOS V5
echo ==========================================
echo.
echo Servidor: ftp.jj-ferreiras.com.br
echo Destino: /home/jj-ferreiras/www/mapos3
echo.

REM Verificar se WinSCP esta instalado
set "WINSCP=C:\Program Files\WinSCP\WinSCP.com"
if not exist "%WINSCP%" (
    echo WinSCP nao encontrado!
    echo.
    echo Por favor, baixe e instale o WinSCP:
    echo https://winscp.net/eng/download.php
    echo.
    pause
    exit /b 1
)

echo [1/3] Verificando conexao...

REM Criar script temporario do WinSCP
set "SCRIPT_TEMP=%TEMP%\winscp_upload_%RANDOM%.txt"
(
echo option batch abort
echo option confirm off
echo open sftp://jj-ferreiras:93982740tT@ftp.jj-ferreiras.com.br:22 -hostkey="*"
echo option transfer binary
echo.
echo # Criar pasta se nao existir
echo cd /home/jj-ferreiras/www
echo mkdir mapos3
echo.
echo # Sincronizar arquivos
echo synchronize remote -delete "%~dp0" "/home/jj-ferreiras/www/mapos3"
echo.
echo # Configurar permissoes
echo cd /home/jj-ferreiras/www/mapos3
echo chmod 777 application/logs
echo chmod 777 application/cache
echo chmod 777 assets/uploads
echo chmod 777 updates
echo.
echo # Fechar
echo close
echo exit
) > "%SCRIPT_TEMP%"

echo [2/3] Iniciando upload...
echo      Isso pode levar alguns minutos...
echo.

"%WINSCP%" /script="%SCRIPT_TEMP%"

if %ERRORLEVEL% neq 0 (
    echo.
    echo ==========================================
    echo   ERRO NO UPLOAD!
    echo ==========================================
    echo Codigo: %ERRORLEVEL%
) else (
    echo.
    echo ==========================================
    echo   UPLOAD CONCLUIDO!
    echo ==========================================
    echo.
    echo Acesse: https://jj-ferreiras.com.br/mapos3
    echo.
    echo Proximos passos:
    echo   1. Acesse o sistema no navegador
    echo   2. Execute o diagnostico
    echo   3. Faca logout e login
)

REM Limpar script temporario
del "%SCRIPT_TEMP%" 2>nul

echo.
pause
