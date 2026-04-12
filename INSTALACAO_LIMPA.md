# 🚀 Instalação Limpa - MAPOS V5

## 📦 Estrutura para Primeira Instalação

```
mapos5/
├── 📄 index.php                    # Entrada principal
├── 📄 .htaccess                     # Config Apache
├── 📄 composer.json                 # Dependências
├── 📄 composer.lock                 # Versões fixadas
├── 📄 robots.txt                    # SEO
├── 📄 manifest.json                 # PWA
│
├── 📁 application/                  # Código CodeIgniter
│   ├── config/
│   │   ├── config.php              # Configurações gerais
│   │   ├── database.php            # Config DB (exemplo)
│   │   └── ...
│   ├── controllers/                # Controllers
│   ├── models/                     # Models
│   ├── views/                      # Views
│   ├── libraries/                  # Libraries
│   ├── helpers/                    # Helpers
│   └── database/migrations/        # Migrations
│
├── 📁 assets/                       # CSS, JS, Imagens
│   ├── css/
│   ├── js/
│   ├── img/
│   └── uploads/                    # (vazio, criar na instalação)
│
├── 📁 system/                       # CodeIgniter Core
│
├── 📁 install/                      # Instalador Web
│   ├── index.php
│   ├── do_install.php
│   ├── view/
│   └── assets/
│
└── 📁 database/
    └── sql/
        └── banco_completo_v5.sql   # Banco inicial
```

---

## 🎯 Arquivos ESSENCIAIS (Obrigatórios)

### 1. Raiz
- `index.php` - Ponto de entrada
- `.htaccess` - Rewrite rules
- `composer.json` + `composer.lock` - Dependências

### 2. Application
- `application/config/` - Todas configs
- `application/controllers/` - Todos controllers
- `application/models/` - Todos models
- `application/views/` - Todas views
- `application/libraries/` - Libraries (Email, Scheduler, etc)
- `application/helpers/` - Helpers

### 3. Assets
- `assets/css/` - Estilos
- `assets/js/` - Scripts
- `assets/img/` - Imagens padrão
- `assets/font-awesome/` - Ícones
- `assets/uploads/` - (criar vazio)

### 4. Database
- `database/sql/banco_completo_v5.sql` - Estrutura inicial

---

## 🗑️ Arquivos para EXCLUIR da Instalação

### Desenvolvimento
- ❌ `.git/` - Git
- ❌ `.claude/` - Arquivos Claude
- ❌ `.github/` - GitHub Actions/Issues
- ❌ `docs/` - Documentação
- ❌ `projeto/` - Arquivos de planejamento
- ❌ `*.md` - Readmes (exceto README.md raiz)

### Scripts e Deploy
- ❌ `deploy/` - Scripts de deploy
- ❌ `upload_*.bat` - Scripts upload
- ❌ `upload_*.ps1` - Scripts upload
- ❌ `INSTRUCOES_*.md` - Instruções avulsas

### Logs e Temp
- ❌ `*.log` - Logs
- ❌ `application/logs/*` - Logs (exceto .htaccess)
- ❌ `application/cache/*` - Cache
- ❌ `*.tar.gz` - Compactados

---

## 📋 Checklist de Instalação

### Passo 1: Requisitos
- [ ] PHP 8.2 ou 8.3
- [ ] MySQL 5.7+ ou MariaDB 10.3+
- [ ] Apache com mod_rewrite
- [ ] Composer (opcional, se já tiver vendor)

### Passo 2: Upload
- [ ] Enviar todos arquivos ESSENCIAIS
- [ ] Configurar permissões:
  ```bash
  chmod 777 application/logs
  chmod 777 application/cache
  chmod 777 assets/uploads
  chmod 777 updates
  ```

### Passo 3: Banco de Dados
- [ ] Criar banco de dados
- [ ] Importar: `database/sql/banco_completo_v5.sql`

### Passo 4: Configuração
- [ ] Renomear `application/config/database.php.example` para `database.php`
- [ ] Editar credenciais do banco
- [ ] Configurar URL base em `config.php`

### Passo 5: Acesso
- [ ] Acessar: `http://seudominio.com/install`
- [ ] Preencher dados de configuração
- [ ] Finalizar instalação

---

## 🔧 Script de Preparação

Execute para preparar pacote limpo:
```bash
# 1. Copiar arquivos essenciais
mkdir mapos5_instalar
cp -r application assets system index.php .htaccess composer.* mapos5_instalar/

# 2. Limpar arquivos desnecessários
rm -rf mapos5_instalar/application/logs/*
rm -rf mapos5_instalar/application/cache/*
touch mapos5_instalar/application/logs/.htaccess
touch mapos5_instalar/application/cache/.htaccess

# 3. Compactar
tar -czf mapos5_instalar.tar.gz mapos5_instalar/
```

---

## ✅ Pós-Instalação

Após instalar, acesse:
- **Admin:** `http://seudominio.com`
- **Login padrão:** admin / admin
- **Altere a senha imediatamente!**

---

**Pacote pronto para instalação!** 🎉
