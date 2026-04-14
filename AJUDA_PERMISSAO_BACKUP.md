# Como Encontrar o Checkbox de Backup

## 🔍 Problema
Você não consegue achar onde marcar a permissão "Backup" na interface.

## 📍 Localização

O checkbox está em:
**Configurações → Permissões → Editar Grupo → Configurações e Sistema**

### Passo a Passo Visual:

```
┌─────────────────────────────────────────────────┐
│  Configurações → Permissões                     │
│                                                 │
│  ┌─────────────────────────────────────────┐     │
│  │  Grupo: Administrador              [✎ Editar]│
│  └─────────────────────────────────────────┘     │
│                                                 │
│  Clique em "Editar"                             │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  Editar Permissão                               │
│                                                 │
│  ▼ Clientes                                     │
│  ▼ Produtos                                     │
│  ▼ Serviços                                     │
│  ▼ Ordens de Serviço                            │
│  ▼ ...                                          │
│  ▶ Configurações e Sistema  ← CLIQUE AQUI!      │
│                                                 │
│  Depois de clicar, aparece:                     │
│  ☑ Configurar Usuário                          │
│  ☑ Configurar Emitente                         │
│  ☑ Configurar Permissão                        │
│  ☐ Backup  ← MARQUE ESTE!                       │
│  ☑ Auditoria                                    │
│                                                 │
│  [Salvar Alterações]                            │
└─────────────────────────────────────────────────┘
```

## 🚨 Se Não Aparecer

Se você clicou em "Configurações e Sistema" e o checkbox "Backup" não apareceu:

### Opção 1: Via SQL (Rápido)
Execute no banco de dados:

```sql
-- Ver qual é o formato das permissões
SELECT permissoes FROM permissoes WHERE nome = 'Administrador';

-- Adicionar cBackup
UPDATE permissoes 
SET permissoes = CONCAT(
    LEFT(permissoes, LENGTH(permissoes) - 1),
    ';s:7:"cBackup";s:1:"1";}'
)
WHERE nome = 'Administrador'
AND permissoes NOT LIKE '%cBackup%';
```

### Opção 2: Bypass Temporário (Teste)
No arquivo `application/controllers/Backup.php`, **descomente** a linha 40:

```php
// Linha 40 - descomente para testar:
$_SESSION['bypass_permissao_backup'] = true;
```

Isso libera o acesso temporariamente (apenas para teste).

## 🔧 Debug

Se quiser saber o que está acontecendo, verifique os logs em:
`application/logs/log-YEAR-MONTH-DAY.php`

Procure por linhas com "Backup - Sem permissão"

## ✅ Solução Definitiva

1. Execute o SQL acima no phpMyAdmin, OU
2. Descomente a linha de bypass no controller
3. Acesse `/backup` para testar se funciona
4. Depois adicione a permissão corretamente via interface

## 🆘 Ainda não funciona?

Verifique se:
1. Você está logado como usuário do grupo "Administrador"
2. O campo `permissoes` na tabela `permissoes` está em formato serialized
3. Não há cache de sessão ativo (limpe cookies/sessão)

Se precisar de mais ajuda, verifique o log em `application/logs/`.
