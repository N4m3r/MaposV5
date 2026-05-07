# Deploy Agente IA MapOS V5

## Pacote gerado
`deploy_agente_ia.tar.gz` (33KB) - contém apenas os arquivos necessários para o agente IA.

## Arquivos incluidos
- `application/controllers/api/v2/WhatsappController.php`
- `application/controllers/api/v2/NotificacoesController.php`
- `application/controllers/api/v2/CobrancasController.php`
- `application/config/routes_api.php`
- `application/database/migrations/2026050*.php` (6 migrations)
- `application/views/agente_ia/*.php` (5 telas)
- `database/sql/*.sql`
- `instalar_agente_ia.php`
- `instalar_agente_ia_config.php`
- `n8n/workflows/*.json` (8 workflows)

## Passos para deploy em producao (jj-ferreiras.com.br)

### 1. Upload dos arquivos
Descompacte `deploy_agente_ia.tar.gz` e faca upload dos arquivos para o servidor de producao, mantendo a estrutura de pastas:
- `application/controllers/api/v2/*` -> `application/controllers/api/v2/`
- `application/config/routes_api.php` -> `application/config/routes_api.php`
- `application/database/migrations/*` -> `application/database/migrations/`
- `application/views/agente_ia/*` -> `application/views/agente_ia/`

### 2. Instalar tabelas do banco
Acesse via navegador:
```
https://jj-ferreiras.com.br/MaposV5/instalar_agente_ia.php
```
Aguarde a execucao e DELETE o arquivo `instalar_agente_ia.php` apos.

Alternativamente, rode as migrations pelo painel do MapOS (Configuracoes > Sistema > Migrations).

### 3. Configurar credenciais no n8n (192.168.100.238)
Acesse o n8n em `http://192.168.100.238:5678` e crie:
1. **Credencial `mapos_api`** (HTTP Header Auth):
   - Header Name: `x-api-key`
   - Header Value: `(token fixo da API v2 do MapOS)`
2. **Credencial `evolution_api`** (HTTP Header Auth):
   - Header Name: `apikey`
   - Header Value: `(sua API key da Evolution)`

### 4. Importar workflows no n8n
Importe os 8 arquivos JSON da pasta `n8n/workflows/`:
- `mapos-webhook-dispatcher.json`
- `notificacao-whatsapp-eventos.json`
- `cobranca-automatica.json`
- `satisfacao-pos-atendimento.json`
- `orcamento-por-audio.json`
- `agente-ia-acoes-autorizacao.json`
- `agente-ia-relatorios.json`
- `verificar-permissao.json`

Ative todos os workflows.

### 5. Configurar webhook da Evolution API
No Evolution Manager (http://192.168.100.238:8091/manager):
- Instancia ja deve estar criada e conectada
- Configure o webhook inbound para:
  ```
  http://192.168.100.238:5678/webhook/recebimento-whatsapp
  ```
  (ou o path configurado no workflow 01_recebe_mensagem)

### 6. Ajustar URLs nos workflows (se necessario)
Verifique se as URLs apontam corretamente para o MapOS:
- Base URL: `https://jj-ferreiras.com.br/MaposV5/api/v2/`
- Evolution URL: `http://192.168.100.238:8091`

### 7. Testar fluxo end-to-end
1. Envie uma mensagem de WhatsApp para o numero conectado
2. Verifique se aparece no log da Evolution API
3. Verifique se o n8n recebeu no webhook
4. Verifique se o MapOS recebeu a mensagem (`/agente_ia/logs_conversa`)
5. Verifique se a resposta foi enviada pelo Evolution

## Troubleshooting
- Se a API retornar 404: o codigo ainda nao foi deployado ou as rotas nao estao atualizadas
- Se o n8n retornar "Credential does not exist": crie as credenciais conforme passo 3
- Se o webhook nao chegar: verifique se o container n8n esta acessivel pela rede interna do Docker
