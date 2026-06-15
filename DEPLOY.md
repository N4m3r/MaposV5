# Deploy MaposV5

> Guia completo para subir o MaposV5 em produção (Kinghost, VPS, Docker).

---

## Arquitetura

```
┌─────────────────────────────────────────────────────────────┐
│                    Apache + PHP 8.3                         │
│  ┌──────────────────────┐  ┌──────────────────────────┐    │
│  │   Legado (CI3)       │  │  Frontend React (estático) │    │
│  │   /index.php/...     │  │  /app.html + /dist/         │    │
│  │   /views/*.php       │  │  /login.html                │    │
│  └──────────┬───────────┘  └──────────┬─────────────────┘    │
│             │                         │                      │
│             └──────────┬──────────────┘                      │
│                        ▼                                     │
│              ┌──────────────────┐                            │
│              │   MySQL 8.0      │                            │
│              │   (Kinghost)     │                            │
│              └──────────────────┘                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 1. Requisitos do Servidor

| Requisito       | Versão Mínima | Observação                       |
|-----------------|---------------|----------------------------------|
| PHP             | 8.3           | Obrigatório (composer.json)      |
| MySQL           | 5.7 / Maria 10.3 | UTF-8MB4                      |
| Apache + mod_rewrite | 2.4      | `AllowOverride All`              |
| Composer        | 2.x           | Para `composer install` local    |
| Node.js         | 20+           | **Só para build do frontend**    |

> **Atenção**: A Kinghost é shared hosting e **NÃO tem Node.js**. O build do React é commitado em `dist/` e servido estaticamente.

### Extensões PHP obrigatórias

```ini
extension=curl
extension=gd
extension=mysqli
extension=json
extension=mbstring
extension=zip
```

---

## 2. Build Local (Frontend)

```bash
# 1. Clonar
git clone https://github.com/seu-usuario/MaposV5.git
cd MaposV5

# 2. Instalar dependências do frontend
cd assets/frontend
npm install
npm run build       # gera dist/

# 3. Voltar
cd ../..
```

O `dist/` contém:
- `index.html` — shell autenticado
- `login.html` — shell de login
- `assets/main-*.js` — bundle principal (58 kB)
- `assets/NotFound-*.js`, `Dashboard-*.js`, etc. — chunks lazy
- `assets/main-*.css` — CSS único (CoreUI + custom)

---

## 3. Subir para o Servidor

### Opção A: Git (recomendado)

```bash
# No servidor (Kinghost via SSH ou painel)
cd /home/usuario/
git clone https://github.com/seu-usuario/MaposV5.git
cd MaposV5
git pull  # para atualizações
```

**Arquivos versionados** (commitar):
- ✅ `application/`
- ✅ `assets/css/`, `assets/font-awesome/`, `assets/frontend/dist/`
- ✅ `index.php`, `.htaccess`, `composer.json`
- ❌ `node_modules/` (no .gitignore)
- ❌ `.env` (no .gitignore — usar `.env.example`)

### Opção B: FTP

Faça upload de todos os arquivos, **EXCETO**:
- `node_modules/`
- `application/vendor/` (rodar `composer install` no servidor)
- `.env` (criar manualmente)
- `tmp/`, `logs/` (criar vazios com permissão 777)

---

## 4. Instalar Dependências PHP

```bash
cd application
composer install --no-dev --optimize-autoloader
```

> **Sem composer no servidor?** Faça upload da pasta `vendor/` local via FTP. Funciona, mas ocupa ~200MB.

---

## 5. Configurar `.env`

Crie o arquivo `.env` na raiz:

```env
# Banco de dados
DB_HOSTNAME=mysql30-farm10.kinghost.net
DB_USERNAME=jjferreiras05
DB_PASSWORD=***
DB_DATABASE=jjferreiras05
DB_DRIVER=mysqli
DB_PREFIX=

# App
APP_ENV=production
APP_DEBUG=false
APP_URL=https://jj-ferreiras.com.br/MaposV5

# Segurança
APP_ENCRYPTION_KEY=61737d9b63330d6a01a99d72a4fbe8ec1f7602b578a741956e4f129d983ecde9

# Sessão
SESSION_DRIVER=database
SESSION_LIFETIME=7200

# Cache
CACHE_DRIVER=file

# Email (configurar SMTP do provedor)
MAIL_HOST=smtp.kinghost.net
MAIL_PORT=587
MAIL_USERNAME=***
MAIL_PASSWORD=***
MAIL_ENCRYPTION=tls

# API
API_KEY=***
```

> ⚠️ **NUNCA** commitar `.env` no Git. Use `.env.example` para templates.

---

## 6. Permissões de Pasta

```bash
chmod -R 755 /home/usuario/MaposV5
chmod -R 777 /home/usuario/MaposV5/application/logs
chmod -R 777 /home/usuario/MaposV5/application/cache
chmod -R 777 /home/usuario/MaposV5/assets/uploads
chmod -R 777 /home/usuario/MaposV5/tmp
```

No Kinghost, isso pode ser feito via painel ou:

```bash
find /home/usuario/MaposV5 -type d -exec chmod 755 {} \;
find /home/usuario/MaposV5 -type f -exec chmod 644 {} \;
chmod -R 777 /home/usuario/MaposV5/application/logs /home/usuario/MaposV5/application/cache /home/usuario/MaposV5/assets/uploads
```

---

## 7. Migrations

```bash
# Via CLI
php index.php tools migrate

# Ou via web (primeiro acesso como admin):
# https://seu-dominio.com/migrate/latest
```

---

## 8. Configurar Apache (.htaccess)

O `.htaccess` da raiz já vem pronto. Verifique que está sendo lido:

```bash
# Verifique se mod_rewrite está ativo
apache2ctl -M | grep rewrite
# Se não aparecer, ative:
a2enmod rewrite
```

No `httpd.conf` da Kinghost (se acessível), garanta:

```apache
<Directory /home/usuario/MaposV5>
    AllowOverride All
    Require all granted
</Directory>
```

---

## 9. Primeiro Acesso

1. Acesse `https://seu-dominio.com/MaposV5/install/`
2. Siga o wizard de instalação
3. **DELETE** a pasta `install/` após a instalação

```bash
rm -rf /home/usuario/MaposV5/install
```

> A Kinghost tem regras no `.htaccess` que protegem `install/`, mas é boa prática deletar.

---

## 10. CI/CD (Opcional)

### GitHub Actions — Build do Frontend

Crie `.github/workflows/build-frontend.yml`:

```yaml
name: Build Frontend

on:
  push:
    paths:
      - 'assets/frontend/src/**'
      - 'assets/frontend/package.json'

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - uses: actions/setup-node@v4
        with:
          node-version: 20
          cache: 'npm'
          cache-dependency-path: assets/frontend/package-lock.json

      - name: Install
        working-directory: assets/frontend
        run: npm ci

      - name: Build
        working-directory: assets/frontend
        run: npm run build

      - name: Commit dist/
        run: |
          git config user.name "github-actions[bot]"
          git config user.email "github-actions[bot]@users.noreply.github.com"
          git add assets/frontend/dist/
          git commit -m "ci: build frontend" || echo "No changes"
          git push
```

### Deploy via SSH

```yaml
  deploy:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    steps:
      - name: Pull on server
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.SSH_HOST }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /home/usuario/MaposV5
            git pull
            cd application
            composer install --no-dev
            php index.php tools migrate
            # Limpar cache
            rm -rf application/cache/*
            rm -rf tmp/*
```

---

## 11. Rollback

```bash
# Voltar para commit anterior
git log --oneline -10
git checkout <commit-hash> -- .
git commit -m "rollback: voltando para <commit-hash>"

# Ou reverter último commit
git revert HEAD
git push
```

---

## 12. Monitoramento

### Logs

```bash
# Log do PHP/Apache
tail -f /var/log/apache2/error.log

# Log do Mapos
tail -f application/logs/log-$(date +%Y-%m-%d).php
```

### Healthcheck

```bash
curl -I https://seu-dominio.com/MaposV5/
# Esperado: HTTP 200 ou 302 (redirect para login)
```

### Uptime Robot

Configure um monitor em `https://seu-dominio.com/MaposV5/` com alerta via email/SMS.

---

## 13. Performance

### OPcache (PHP)

No `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.revalidate_freq=60
opcache.validate_timestamps=0  # Em produção
```

### Apache KeepAlive

```apache
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5
```

### CDN (Opcional)

Sirva `assets/frontend/dist/assets/*.js` e `*.css` via Cloudflare CDN:

```
https://cdn.seu-dominio.com/assets/...
```

Aponte via `<base href>` ou reescreva no `vite.config.ts`.

---

## 14. Checklist Pós-Deploy

- [ ] `https://seu-dominio.com/` retorna 200
- [ ] Login funciona com admin/admin
- [ ] `/app.html` carrega o React (Network tab: bundle main-*.js)
- [ ] CSRF funciona (POST/PUT/DELETE não retornam 403)
- [ ] Upload de arquivos funciona
- [ ] Permissões bloqueiam usuários sem `vXxx`
- [ ] Migrations rodaram (tabelas no MySQL)
- [ ] `.env` não está commitado (`git status` não mostra)
- [ ] `install/` foi deletado
- [ ] Logs estão escrevendo (`application/logs/log-*.php`)

---

## 15. Problemas Comuns

### "Class 'X' not found" após deploy

`composer install` não foi rodado. Execute:

```bash
cd application
composer install --no-dev
```

### "Permission denied" em `application/logs/`

```bash
chmod -R 777 application/logs
```

### "CSRF token mismatch"

O cache de sessão está corrompido. Limpe:

```bash
rm -rf application/cache/*
rm -rf tmp/cache/*
```

### "500 Internal Server Error" no React

Abra o console do navegador. Provavelmente:
- `baseUrl` errado em `window.APP_CONFIG`
- CSRF não está sendo enviado (verifique `X-CSRF-Token`)

### "Lazy chunk failed to load"

O `dist/` foi rebuildado com hashes novos mas o usuário tem cache antigo. Solução:

```html
<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
```

Ou no `.htaccess`:

```apache
<FilesMatch "\.(js|css)$">
    Header set Cache-Control "no-cache, must-revalidate"
</FilesMatch>
```

---

<p align="center">Deploy feito = sistema no ar 🚀</p>
