# Correção do Instalador - MAPOS V5

## Problema
**Erro:** `O arquivo ../banco.sql não foi encontrado na pasta de instalação!`

## Causa
O instalador procurava o arquivo SQL na raiz do projeto (`../banco.sql`), mas após a organização dos arquivos, o SQL está em `database/sql/`.

## Solução Aplicada

O arquivo `install/settings.json` foi atualizado:

**Antes:**
```json
"database_file": "../banco.sql"
```

**Depois:**
```json
"database_file": "../database/sql/instalacao_completa_mapos5.sql"
```

## Arquivos SQL Disponíveis

| Arquivo | Descrição | Quando Usar |
|-----------|-----------|-------------|
| `database/sql/instalacao_completa_mapos5.sql` | **Instalação completa V5** (Recomendado) | Nova instalação com todas as funcionalidades |
| `database/sql/banco.sql` | Instalação básica | Instalação mínima |
| `database/sql/atualizacao_completa_v5.sql` | Atualização para V5 | Migrar versão antiga |

## Como Prosseguir

### Opção 1: Usar o Instalador Web
1. Acesse: `http://seudominio.com/install`
2. Preencha os dados do banco
3. O instalador usará automaticamente `instalacao_completa_mapos5.sql`

### Opção 2: Instalação Manual
```bash
# 1. Importar o SQL
mysql -u usuario -p mapos < database/sql/instalacao_completa_mapos5.sql

# 2. Configurar o banco
cp application/config/database.php.example application/config/database.php
# Edite as credenciais

# 3. Configurar a URL
cp application/config/config.php.example application/config/config.php
# Altere a base_url

# 4. Ajustar permissões
chmod 777 application/logs
chmod 777 application/cache
chmod 777 assets/uploads

# 5. Acesse o sistema
# Login: admin@mapos.com.br
# Senha: admin
```

## Notas
- O arquivo `instalacao_completa_mapos5.sql` inclui **todas as tabelas V5** (email, webhooks, certificado, DRE, impostos)
- Se precisar usar outro arquivo SQL, edite `install/settings.json` e altere `"database_file"`
