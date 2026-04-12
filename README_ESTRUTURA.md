# 📁 Estrutura Organizada do MAPOS V5

## Estrutura de Pastas

```
mapos-master/
├── 📄 index.php              # Ponto de entrada da aplicação
├── 📄 composer.json          # Dependências PHP
├── 📄 .htaccess             # Configuração Apache
├── 📄 robots.txt            # Configuração SEO
├── 📄 manifest.json         # Configuração PWA
│
├── 📁 application/          # Código da aplicação CodeIgniter
│   ├── config/             # Configurações
│   ├── controllers/        # Controllers
│   ├── models/            # Models
│   ├── views/             # Views
│   ├── libraries/         # Libraries personalizadas
│   │   ├── Email/        # Sistema de email
│   │   ├── Scheduler/   # Agendamento
│   │   └── ...
│   ├── helpers/          # Helpers
│   └── database/         # Migrations
│       └── migrations/   # Scripts de migração
│
├── 📁 assets/              # CSS, JS, Imagens
│   ├── css/
│   ├── js/
│   └── uploads/           # Uploads de arquivos
│
├── 📁 database/            # Scripts de banco
│   └── sql/               # Scripts SQL
│       ├── banco.sql
│       ├── atualizacao_completa_v5.sql
│       └── ...
│
├── 📁 docs/                # Documentação
│   ├── api/               # Documentação da API
│   ├── instalacao/        # Guias de instalação
│   └── *.md              # Documentação geral
│
├── 📁 scripts/             # Scripts utilitários
│   ├── install.sh         # Script de instalação Linux
│   ├── install.bat        # Script de instalação Windows
│   ├── extrair_servidor.sh # Script de deploy
│   └── VERIFICAR_E_CORRIGIR.php # Diagnóstico
│
├── 📁 deploy/              # Scripts de deploy
│   ├── deploy.bat
│   └── deploy.ps1
│
├── 📁 projeto/             # Arquivos de planejamento
│   └── *.md              # Cronogramas, instruções
│
├── 📁 docker/              # Configuração Docker
├── 📁 install/             # Arquivos de instalação
├── 📁 logs/                # Logs do sistema
└── 📁 updates/             # Arquivos de atualização
```

## Arquivos Essenciais na Raiz

| Arquivo | Descrição |
|---------|-----------|
| `index.php` | Ponto de entrada da aplicação CodeIgniter |
| `composer.json` | Configuração de dependências PHP |
| `composer.lock` | Versões bloqueadas das dependências |
| `.htaccess` | Configuração do servidor Apache |
| `robots.txt` | Diretivas para motores de busca |
| `manifest.json` | Configuração para PWA |
| `.gitignore` | Arquivos ignorados pelo Git |
| `.php-cs-fixer.php` | Configuração de formatação de código |

## Onde Encontrar?

### 📖 Documentação
- **API**: `docs/api/`
- **Instalação**: `docs/instalacao/`
- **Changelog**: `docs/CHANGELOG.md`
- **Documentação Geral**: `docs/DOCUMENTAÇÃO-MAPOS.md`

### 💾 Scripts SQL
- **Banco inicial**: `database/sql/banco.sql`
- **Atualização V5**: `database/sql/atualizacao_completa_v5.sql`
- **Outros**: `database/sql/*.sql`

### 🚀 Deploy
- **Windows**: `deploy/deploy.bat`
- **PowerShell**: `deploy/deploy.ps1`
- **Extração no servidor**: `scripts/extrair_servidor.sh`

### 🔧 Ferramentas
- **Verificação**: `scripts/VERIFICAR_E_CORRIGIR.php`
- **Instalação Linux**: `scripts/install.sh`
- **Instalação Windows**: `scripts/install.bat`

## Comandos Úteis

```bash
# Deploy rápido
cd deploy && ./deploy.ps1

# Verificar instalação
php scripts/VERIFICAR_E_CORRIGIR.php

# Instalar banco
cd database/sql && mysql -u usuario -p banco < banco.sql
```

---
**Nota**: Execute `scripts/VERIFICAR_E_CORRIGIR.php` após a instalação para verificar se tudo está configurado corretamente.
