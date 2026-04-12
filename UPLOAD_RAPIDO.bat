@echo off
chcp 65001 >nul
title UPLOAD SFTP - MAPOS V5
cls

color 0B
echo ==========================================
echo    UPLOAD SFTP - MAPOS V5
echo ==========================================
echo.
echo  Servidor: ftp.jj-ferreiras.com.br
echo  Pasta:    www/mapos3
echo  Usuario:  jj-ferreiras
echo.

REM Verificar se esta na pasta correta
if not exist "index.php" (
    color 0C
    echo ERRO: Execute este arquivo na pasta raiz do MAPOS!
    echo.
    echo Voce deve estar onde esta o arquivo index.php
    pause
    exit /b 1
)

color 0E
echo.
echo Escolha o metodo de upload:
echo.
echo  [1] WinSCP (Recomendado - mais rapido)
echo  [2] FileZilla (Interface grafica)
echo  [3] Abrir instrucoes
echo.
set /p opcao="Digite 1, 2 ou 3: "

if "%opcao%"=="1" goto :winscp
if "%opcao%"=="2" goto :filezilla
if "%opcao%"=="3" goto :instrucoes
goto :fim

:winscp
color 0A
echo.
echo ==========================================
echo  INICIANDO UPLOAD COM WINSCP
echo ==========================================
echo.
echo Verificando WinSCP...

set "WINSCP=C:\Program Files\WinSCP\WinSCP.com"
if not exist "%WINSCP%" set "WINSCP=C:\Program Files (x86)\WinSCP\WinSCP.com"

if not exist "%WINSCP%" (
    color 0C
    echo.
    echo WinSCP nao encontrado!
    echo.
    echo Baixe em: https://winscp.net/eng/download.php
    echo Instale e execute este script novamente.
    echo.
    start https://winscp.net/eng/download.php
    pause
    goto :fim
)

echo Criando script de upload...
set "SCRIPT_TMP=%TEMP%\upload_mapos_%RANDOM%.txt"
(
echo # WinSCP script
echo option batch abort
echo option confirm off
echo open sftp://jj-ferreiras:93982740tT@ftp.jj-ferreiras.com.br:22 -hostkey="*"
echo option transfer binary
echo.
echo # Criar pasta
echo cd /home/jj-ferreiras/www
echo mkdir mapos3
echo cd mapos3
echo.
echo # Upload sincronizado
echo synchronize remote -delete "%CD%" "/home/jj-ferreiras/www/mapos3"
echo.
echo # Permissoes
echo chmod 777 application/logs
echo chmod 777 application/cache
echo chmod 777 assets/uploads
echo chmod 777 updates
echo.
echo close
echo exit
) > "%SCRIPT_TMP%"

echo.
echo Iniciando upload...
echo Isso pode levar alguns minutos...
echo.
"%WINSCP%" /script="%SCRIPT_TMP%"
set RESULTADO=%ERRORLEVEL%

del "%SCRIPT_TMP%" 2>nul

echo.
if %RESULTADO%==0 (
    color 0A
    echo ==========================================
    echo    UPLOAD CONCLUIDO COM SUCESSO!
    echo ==========================================
    echo.
    echo Acesse: https://jj-ferreiras.com.br/mapos3
    echo.
    echo Proximos passos:
    echo  1. Acesse o sistema
    echo  2. Execute VERIFICAR_E_CORRIGIR.php
    echo  3. Faca logout e login
) else (
    color 0C
    echo ==========================================
    echo    ERRO NO UPLOAD
    echo ==========================================
    echo Codigo: %RESULTADO%
)
goto :fim

:filezilla
color 0E
echo.
echo ==========================================
echo  ABRINDO FILEZILLA
echo ==========================================
echo.
echo Configuracoes para FileZilla:
echo.
echo  Protocolo: SFTP
echo  Host: ftp.jj-ferreiras.com.br
echo  Porta: 22
echo  Usuario: jj-ferreiras
echo  Senha: 93982740tT
echo.
echo Pasta remota: /home/jj-ferreiras/www/mapos3
echo.

set "FZ=C:\Program Files\FileZilla FTP Client\filezilla.exe"
if not exist "%FZ%" set "FZ=C:\Program Files (x86)\FileZilla FTP Client\filezilla.exe"

if exist "%FZ%" (
    start "" "%FZ%" sftp://jj-ferreiras:93982740tT@ftp.jj-ferreiras.com.br:22
) else (
    color 0C
    echo FileZilla nao encontrado.
    echo Baixe em: https://filezilla-project.org/
    start https://filezilla-project.org/download.php
)
goto :fim

:instrucoes
color 0F
start INSTRUCOES_UPLOAD_SFTP.md
goto :fim

:fim
color 07
echo.
pause
