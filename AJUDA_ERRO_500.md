# Erro 500 no do_install.php - Solução

## 🔍 Diagnóstico Rápido

Acesse este arquivo no navegador:
```
http://seusite.com/install/test_php.php
```

Isso vai mostrar exatamente qual é o problema.

---

## 🎯 Causas Comuns do Erro 500

### 1. **settings.json não existe ou está corrompido**
**Verifique se existe:** `install/settings.json`

Se não existir, crie com:
```json
{
  "database_file": "../database/sql/banco.sql"
}
```

### 2. **banco.sql não está no local esperado**
O instalador procura em: `database/sql/banco.sql`

Verifique se o arquivo existe:
```
MaposV5/
├── database/
│   └── sql/
│       └── banco.sql  <-- DEVE EXISTIR
```

### 3. **.env.example não existe**
Verifique se existe: `application/.env.example`

Se não existir, pode usar um template básico.

### 4. **Erro de sintaxe no PHP**
Algumas versões do PHP podem ter problemas com sintaxe.

---

## 🛠️ Soluções

### Solução 1: Verificar Tudo via Testador

Acesse no navegador:
```
http://localhost/MaposV5/install/test_php.php
```

O teste vai mostrar:
- ✓ Se o PHP está OK
- ✓ Se settings.json existe
- ✓ Se banco.sql existe
- ✓ Se há permissões de escrita
- ✗ Qual erro específico está ocorrendo

### Solução 2: Habilitar Debug no Installer

Edite o arquivo `install/do_install.php` e mude as linhas iniciais:

```php
// DE:
ini_set('display_errors', 0);

// PARA:
ini_set('display_errors', 1);
```

Isso vai mostrar o erro exato na tela.

### Solução 3: Instalação Manual

Se o instalador continuar falhando, faça manualmente:

1. **Crie o banco de dados** via phpMyAdmin ou MySQL:
```sql
CREATE DATABASE mapos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Importe o banco.sql:**
```bash
mysql -u root -p mapos < database/sql/banco.sql
```

3. **Crie o arquivo .env:**
Copie `application/.env.example` para `application/.env`

Edite e substitua:
- `enter_db_hostname` → localhost
- `enter_db_username` → root (ou seu usuário)
- `enter_db_password` → sua_senha
- `enter_db_name` → mapos
- `enter_baseurl` → http://seusite.com/
- `enter_encryption_key` → qualquer_chave_aleatoria

4. **Adicione o usuário admin:**
```sql
INSERT INTO usuarios (nome, email, senha, situacao, permissoes_id) 
VALUES ('Administrador', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);
```

Senha padrão: `password`

---

## 🔥 Erros Específicos

### "Arquivo de configuração não encontrado!"
→ Crie o arquivo `install/settings.json`

### "O arquivo ../banco.sql não foi encontrado"
→ O banco.sql deve estar em `database/sql/banco.sql`

### "Arquivo .env.example não encontrado"
→ Crie o arquivo `application/.env.example`

### "Diretório application/ não é gravável"
→ Dê permissão de escrita: `chmod 755 application/`

---

## 📋 Checklist Pré-Instalação

Antes de instalar, verifique:

- [ ] PHP 7.0 ou superior
- [ ] Extensões: mysqli, json, mbstring
- [ ] MySQL/MariaDB instalado
- [ ] Permissão de escrita em `application/`
- [ ] Arquivo `database/sql/banco.sql` existe
- [ ] Arquivo `application/.env.example` existe

---

## 🆘 Ainda não funciona?

Verifique os logs de erro do servidor:

**XAMPP/WAMP:**
- Arquivo: `C:\xampp\apache\logs\error.log`

**Linux (Apache):**
```bash
sudo tail -f /var/log/apache2/error.log
```

**cPanel/Hostinger:**
- Acesse: File Manager > error_logs

Procure por linhas com "do_install.php" para ver o erro exato.
