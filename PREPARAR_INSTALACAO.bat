@echo off
chcp 65001 >nul
title PREPARAR PACOTE DE INSTALACAO - MAPOS V5
cls

color 0B
echo ==========================================
echo   PREPARAR PACOTE DE INSTALACAO
echo   MAPOS V5 - Versao 4.52.0
echo ==========================================
echo.
echo Este script ira criar um pacote limpo
echo para instalacao do MAPOS V5
echo.

pause

REM Verificar se esta na pasta correta
if not exist "index.php" (
    color 0C
    echo ERRO: Execute este arquivo na pasta raiz do MAPOS!
    pause
    exit /b 1
)

set "PASTA_ORIGEM=%CD%"
set "PASTA_TEMP=%TEMP%\mapos5_instalacao_%RANDOM%"
set "PASTA_DESTINO=%PASTA_ORIGEM%\PACOTE_INSTALACAO"

echo.
echo [1/6] Criando estrutura temporaria...
if exist "%PASTA_DESTINO%" (
    rmdir /s /q "%PASTA_DESTINO%" 2>nul
)
mkdir "%PASTA_DESTINO%"
mkdir "%PASTA_DESTINO%\application"
mkdir "%PASTA_DESTINO%\assets"
mkdir "%PASTA_DESTINO%\system"
mkdir "%PASTA_DESTINO%\install"
mkdir "%PASTA_DESTINO%\database\sql"

color 0E
echo.
echo [2/6] Copiando arquivos essenciais...
echo.

REM Copiar arquivos raiz
copy "%PASTA_ORIGEM%\index.php" "%PASTA_DESTINO%\" >nul
copy "%PASTA_ORIGEM%\.htaccess" "%PASTA_DESTINO%\" >nul
copy "%PASTA_ORIGEM%\composer.json" "%PASTA_DESTINO%\" >nul
copy "%PASTA_ORIGEM%\composer.lock" "%PASTA_DESTINO%\" >nul
copy "%PASTA_ORIGEM%\robots.txt" "%PASTA_DESTINO%\" >nul
copy "%PASTA_ORIGEM%\manifest.json" "%PASTA_DESTINO%\" >nul
copy "%PASTA_ORIGEM%\README.md" "%PASTA_DESTINO%\" >nul
echo   ✓ Arquivos raiz copiados

REM Copiar Application
xcopy "%PASTA_ORIGEM%\application\*" "%PASTA_DESTINO%\application\" /E /H /C /I /Q 2>nul
echo   ✓ Application copiado

REM Copiar Assets
xcopy "%PASTA_ORIGEM%\assets\*" "%PASTA_DESTINO%\assets\" /E /H /C /I /Q 2>nul
echo   ✓ Assets copiado

REM Copiar System
xcopy "%PASTA_ORIGEM%\system\*" "%PASTA_DESTINO%\system\" /E /H /C /I /Q 2>nul
echo   ✓ System copiado

REM Copiar Install
xcopy "%PASTA_ORIGEM%\install\*" "%PASTA_DESTINO%\install\" /E /H /C /I /Q 2>nul
echo   ✓ Install copiado

echo.
echo [3/6] Copiando banco de dados...
copy "%PASTA_ORIGEM%\database\sql\instalacao_completa_mapos5.sql" "%PASTA_DESTINO%\database\sql\" >nul
echo   ✓ SQL de instalacao copiado

echo.
echo [4/6] Limpando arquivos desnecessarios...
echo.

REM Remover pastas desnecessarias
rmdir /s /q "%PASTA_DESTINO%\application\logs" 2>nul
rmdir /s /q "%PASTA_DESTINO%\application\cache" 2>nul
rmdir /s /q "%PASTA_DESTINO%\docs" 2>nul
rmdir /s /q "%PASTA_DESTINO%\projeto" 2>nul
rmdir /s /q "%PASTA_DESTINO%\deploy" 2>nul
rmdir /s /q "%PASTA_DESTINO%\database\sql" 2>nul

REM Recriar pastas necessarias vazias
mkdir "%PASTA_DESTINO%\application\logs" 2>nul
mkdir "%PASTA_DESTINO%\application\cache" 2>nul
mkdir "%PASTA_DESTINO%\database\sql" 2>nul
mkdir "%PASTA_DESTINO%\assets\uploads" 2>nul

REM Criar .htaccess nas pastas
(echo order allow,deny) > "%PASTA_DESTINO%\application\logs\.htaccess"
(echo deny from all) >> "%PASTA_DESTINO%\application\logs\.htaccess"
copy "%PASTA_DESTINO%\application\logs\.htaccess" "%PASTA_DESTINO%\application\cache\" >nul

echo   ✓ Pastas limpas

REM Remover arquivos de exemplo
if exist "%PASTA_DESTINO%\application\config\database.php" (
    del "%PASTA_DESTINO%\application\config\database.php" 2>nul
)
if exist "%PASTA_DESTINO%\application\config\config.php" (
    del "%PASTA_DESTINO%\application\config\config.php" 2>nul
)

echo.
echo [5/6] Criando arquivo de instrucoes...
(
echo ==========================================
echo MAPOS V5 - INSTRUCOES DE INSTALACAO
echo ==========================================
echo.
echo 1. BANCO DE DADOS
echo    - Crie um banco MySQL chamado 'mapos'
echo    - Importe: database/sql/instalacao_completa_mapos5.sql
echo.
echo 2. CONFIGURACAO
echo    - Renomeie application/config/database.php.example para database.php
echo    - Edite as credenciais do banco
echo    - Renomeie application/config/config.php.example para config.php
echo    - Altere a URL base
echo.
echo 3. PERMISSOES
echo    - application/logs/ = 777
echo    - application/cache/ = 777
echo    - assets/uploads/ = 777
echo.
echo 4. ACESSO
echo    - Acesse: http://seudominio.com/install
echo    - Finalize a instalacao
echo    - Login: admin@mapos.com.br / admin
echo    - ALTERE A SENHA!
echo.
echo ==========================================
) > "%PASTA_DESTINO%\INSTRUCOES_INSTALACAO.txt"

echo   ✓ Instrucoes criadas

echo.
echo [6/6] Compactando pacote...
echo.

REM Criar pacote zip
if exist "C:\Program Files\7-Zip\7z.exe" (
    "C:\Program Files\7-Zip\7z.exe" a -tzip "PACOTE_INSTALACAO_MAPOS5.zip" "PACOTE_INSTALACAO\*" -r
) else (
    powershell -Command "Compress-Archive -Path PACOTE_INSTALACAO\* -DestinationPath PACOTE_INSTALACAO_MAPOS5.zip -Force"
)

if exist "%PASTA_ORIGEM%\PACOTE_INSTALACAO_MAPOS5.zip" (
    for %%I in ("%PASTA_ORIGEM%\PACOTE_INSTALACAO_MAPOS5.zip") do (
        set "TAMANHO=%%~zI"
    )

    color 0A
    echo ==========================================
    echo    PACOTE CRIADO COM SUCESSO!
    echo ==========================================
    echo.
    echo Arquivo: PACOTE_INSTALACAO_MAPOS5.zip
    echo Local: %PASTA_ORIGEM%
    echo.
    echo Tamanho: aproximadamente %TAMANHO% bytes
    echo.
    echo Conteudo:
    echo   - Codigo fonte completo
    echo   - Banco de dados inicial
    echo   - Instalador web
    echo   - Configuracoes de exemplo
    echo.
    echo Proximos passos:
    echo   1. Envie o arquivo ZIP para o servidor
    echo   2. Extraia na pasta web
    echo   3. Execute o instalador
echo.
    echo Siga as instrucoes em INSTRUCOES_INSTALACAO.txt
    echo ==========================================
) else (
    color 0C
    echo ERRO: Falha ao criar pacote ZIP
)

REM Limpar pasta temporaria
rmdir /s /q "%PASTA_DESTINO%" 2>nul

echo.
pause
