#!/usr/bin/env node
/**
 * Script de Setup para MAPOS Mobile
 * Configuração automática do ambiente de desenvolvimento
 */

const fs = require('fs');
const path = require('path');
const { exec } = require('child_process');
const chalk = require('chalk') || { green: (s) => s, yellow: (s) => s, red: (s) => s, cyan: (s) => s };

console.log('');
console.log(chalk.cyan('=========================================='));
console.log(chalk.cyan('   Setup do Ambiente MAPOS Mobile'));
console.log(chalk.cyan('=========================================='));
console.log('');

// Verificar se está no diretório correto
const currentDir = process.cwd();
const mobileDir = path.join(__dirname, '..');

if (currentDir !== mobileDir) {
    console.log(chalk.yellow('Mudando para o diretório mobile...'));
    process.chdir(mobileDir);
}

// Função para verificar dependências
function checkDependencies() {
    return new Promise((resolve) => {
        console.log(chalk.yellow('Verificando dependências...'));
        console.log('');

        const checks = [
            { cmd: 'node --version', name: 'Node.js' },
            { cmd: 'npm --version', name: 'npm' }
        ];

        let completed = 0;
        checks.forEach(check => {
            exec(check.cmd, (error, stdout) => {
                if (error) {
                    console.log(chalk.red(`✗ ${check.name}: NÃO ENCONTRADO`));
                } else {
                    console.log(chalk.green(`✓ ${check.name}: ${stdout.trim()}`));
                }
                completed++;
                if (completed === checks.length) {
                    console.log('');
                    resolve();
                }
            });
        });
    });
}

// Função para instalar dependências
function installDependencies() {
    return new Promise((resolve, reject) => {
        console.log(chalk.yellow('Instalando dependências...'));
        console.log('Isso pode levar alguns minutos...');
        console.log('');

        const install = exec('npm install', { cwd: mobileDir });

        install.stdout.on('data', (data) => {
            process.stdout.write(data);
        });

        install.stderr.on('data', (data) => {
            process.stderr.write(data);
        });

        install.on('close', (code) => {
            if (code === 0) {
                console.log('');
                console.log(chalk.green('✓ Dependências instaladas com sucesso!'));
                resolve();
            } else {
                reject(new Error(`Processo saiu com código ${code}`));
            }
        });
    });
}

// Função para criar arquivos de configuração
function createConfigFiles() {
    return new Promise((resolve) => {
        console.log('');
        console.log(chalk.yellow('Criando arquivos de configuração...'));

        // Criar .env para o mobile se não existir
        const envPath = path.join(mobileDir, '.env');
        if (!fs.existsSync(envPath)) {
            const envContent = `# Configuração MAPOS Mobile
# API Configuration
API_BASE_URL=http://localhost/MaposV5/index.php
API_VERSION=v1

# Debug
DEBUG=true
LOG_LEVEL=debug

# Local Storage
STORAGE_PREFIX=mapos_mobile_

# Sync Configuration
SYNC_INTERVAL=30000
SYNC_RETRY_ATTEMPTS=3
`;
            fs.writeFileSync(envPath, envContent);
            console.log(chalk.green('✓ Arquivo .env criado'));
        } else {
            console.log(chalk.yellow('= Arquivo .env já existe'));
        }

        // Criar diretório de logs
        const logsDir = path.join(mobileDir, 'logs');
        if (!fs.existsSync(logsDir)) {
            fs.mkdirSync(logsDir, { recursive: true });
            console.log(chalk.green('✓ Diretório logs/ criado'));
        }

        console.log('');
        resolve();
    });
}

// Função principal
async function main() {
    try {
        await checkDependencies();
        await installDependencies();
        await createConfigFiles();

        console.log('');
        console.log(chalk.green('=========================================='));
        console.log(chalk.green('    Setup concluído com sucesso!'));
        console.log(chalk.green('=========================================='));
        console.log('');
        console.log(chalk.cyan('Próximos passos:'));
        console.log('');
        console.log('1. Inicie o servidor PHP:');
        console.log('   cd .. && php -S localhost:8080 -t application/');
        console.log('');
        console.log('2. Inicie o Live Reload:');
        console.log('   npm run dev');
        console.log('');
        console.log('3. Acesse no navegador:');
        console.log('   http://localhost:3000');
        console.log('');
        console.log(chalk.yellow('Documentação completa em: docs/PLANEJAMENTO_MOBILE.md'));
        console.log('');

    } catch (error) {
        console.log('');
        console.log(chalk.red('=========================================='));
        console.log(chalk.red('        Erro durante o setup:'));
        console.log(chalk.red('=========================================='));
        console.log(chalk.red(error.message));
        console.log('');
        process.exit(1);
    }
}

// Executar
main();
