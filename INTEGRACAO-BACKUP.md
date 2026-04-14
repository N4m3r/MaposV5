# Integração do Sistema de Backup no Mapos

Este documento explica como integrar o sistema de backup no menu do Mapos.

## 1. Adicionar Permissão

Adicione a permissão `cBackup` no banco de dados na tabela `permissoes`:

### Via SQL
```sql
-- Verificar permissões atuais do grupo Administrador
SELECT * FROM permissoes WHERE nome = 'Administrador';

-- O campo 'permissoes' deve ser um JSON/Serialized contendo: "cBackup"
-- Exemplo de permissões atualizadas (adicionar cBackup aos existentes):
```

### Via Interface Web
Acesse: **Configurações > Permissões > Administrador**

Adicione as permissões:
| Código | Descrição | Ação |
|--------|-----------|------|
| `cBackup` | Cadastrar Backup | Visualizar e criar backups |
| `vBackup` | Visualizar Backups | Apenas visualizar lista |
| `eBackup` | Editar Backup | Renomear/excluir backups |
| `rBackup` | Restaurar Backup | Executar restauração |

**Nota**: Para acesso total, basta ter a permissão `cBackup`.

### Permissão Mínima
Se quiser apenas visualizar:
- Código: `vBackup`
- Nome: Visualizar Backups
- Descrição: Acesso apenas para visualizar backups existentes

## 2. Adicionar Menu

Edite o arquivo `application/views/tema/xxx/header.php` (substitua xxx pelo tema atual) e adicione o item no menu de Configurações:

```php
<!-- Menu Configurações -->
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="icon-cog"></i>
        <span class="text">Configurações</span>
        <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
        <!-- ... outros itens ... -->
        
        <li class="divider"></li>
        
        <li>
            <a href="<?php echo site_url('backup'); ?>">
                <i class="icon-hdd"></i> Backup e Restauração
            </a>
        </li>
    </ul>
</li>
```

## 3. Criar Diretório de Backups

Crie o diretório para armazenar os backups:

```bash
# Via SSH/Terminal
mkdir -p /var/www/html/mapos/backups
chmod 755 /var/www/html/mapos/backups
chown www-data:www-data /var/www/html/mapos/backups
```

Ou em ambiente Windows/XAMPP:
```batch
mkdir C:\xampp\htdocs\mapos\backups
```

## 4. Configurar Permissões de Escrita

Certifique-se de que o servidor web tem permissão de escrita:

```bash
# Linux/Apache
sudo chown -R www-data:www-data /var/www/html/mapos/backups/
sudo chmod -R 755 /var/www/html/mapos/backups/

# Linux/Nginx
sudo chown -R nginx:nginx /var/www/html/mapos/backups/
sudo chmod -R 755 /var/www/html/mapos/backups/
```

## 5. Configurar PHP (php.ini)

Ajuste as configurações para permitir uploads grandes:

```ini
; Tamanho máximo de upload (ajuste conforme necessário)
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M
```

## 6. Testar Acesso

Após configurar:

1. Faça login como administrador
2. Acesse o menu **Configurações > Backup e Restauração**
3. Clique em **"Realizar Backup Agora"** para testar

## Funcionalidades

### Realizar Backup
- Gera arquivo `.sql.gz` automaticamente
- Nome do arquivo inclui data e hora
- Backup inclui estrutura e dados de todas as tabelas
- Disponível para download imediatamente

### Restaurar Backup
- Aceita arquivos `.sql`, `.sql.gz` ou `.zip`
- Valida integridade do arquivo antes de restaurar
- Cria backup automático antes da restauração
- Mostra progresso em tempo real

### Listar Backups
- Visualização de todos os backups disponíveis
- Informações de tamanho e data
- Verificação de integridade
- Download e exclusão de backups

## Segurança

O sistema inclui as seguintes medidas de segurança:

1. **Autenticação obrigatória** - Apenas usuários logados
2. **Permissão específica** - Apenas admins podem acessar
3. **Validação de arquivo** - Verifica extensão e tamanho
4. **Backup automático** - Criado antes de restauração
5. **Logs de atividade** - Todas as operações são registradas
6. **Sanitização de nome** - Evita path traversal

## Agendamento Automático (Recomendado)

Configure um cron job para backup automático diário:

```bash
# Editar crontab
sudo crontab -e

# Adicionar linha para backup diário às 2h da manhã
0 2 * * * cd /var/www/html/mapos && php index.php backup/realizar_backup > /dev/null 2>&1
```

Ou crie um script PHP:

```php
<?php
// Salvar como /backups/backup_automatico.php
// Acessar via: https://seusite.com/backups/backup_automatico.php?token=SEU_TOKEN_SEGURO

$token = $_GET['token'] ?? '';
if ($token !== 'SEU_TOKEN_SEGURO') {
    die('Acesso negado');
}

// Executar backup
require_once 'index.php';
$CI =& get_instance();
$CI->load->controller('backup');
// ... lógica de backup
```

## Troubleshooting

### Erro: "Não foi possível criar arquivo"
Verifique permissões do diretório `backups/`.

### Erro: "Arquivo muito grande"
Aumente `upload_max_filesize` e `post_max_size` no php.ini.

### Erro: "mysqldump não encontrado"
O sistema tentará fazer backup via PHP automaticamente, mas é mais lento.

### Timeout durante restauração
Aumente `max_execution_time` no php.ini ou restaure via linha de comando.

### Erro de memória
Aumente `memory_limit` no php.ini.

## Suporte

Para problemas adicionais, verifique:
1. Logs em `application/logs/`
2. Console do navegador (para erros JS)
3. Permissões de pastas
4. Configurações do PHP

## Notas Importantes

- **Sempre** faça backup antes de atualizar o sistema
- **Nunca** restaure um backup enquanto usuários estiverem usando o sistema
- **Mantenha** backups antigos (recomendado: últimos 30 dias)
- **Armazene** backups em local externo (cloud, HD externo)
- **Teste** periodicamente a restauração de backups
