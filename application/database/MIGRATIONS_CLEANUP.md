# Limpeza de Migrations - MAP-OS

## Data: 13/04/2026

## Resumo
Foram removidas 11 migrations obsoletas da pasta `migrations/`. As migrations removidas eram aquelas que criavam tabelas já presentes no arquivo `database/sql/banco.sql`.

## Migrations Removidas (11)

| Arquivo | Motivo |
|---------|--------|
| `001_create_email_tables.php` | Tabelas email_queue, email_tracking, scheduled_events já existem em banco.sql |
| `008_create_dre_contabil.php` | Tabelas dre_contas, dre_lancamentos já existem em banco.sql |
| `009_create_impostos_simples.php` | Tabelas impostos_config, impostos_retidos já existem em banco.sql |
| `010_create_certificado_digital.php` | Tabela certificado_config já existe em banco.sql |
| `20121031100537_create_base.php` | Tabelas base (ci_sessions, clientes, usuarios, etc.) já existem em banco.sql |
| `20250413000002_create_nfse_emissao_tables.php` | Tabelas os_nfse_emitida, os_boleto_emitido já existem em banco.sql |
| `20260411000001_create_email_queue.php` | Tabela email_queue já existe em banco.sql |
| `20260411000002_create_email_clicks.php` | Tabela email_clicks já existe em banco.sql |
| `20260411000003_create_scheduled_events.php` | Tabela scheduled_events já existe em banco.sql |
| `20260411000005_create_push_notifications.php` | Tabela push_notifications já existe em banco.sql |
| `20260411000006_create_webhooks.php` | Tabelas webhooks, webhook_logs já existem em banco.sql |

## Migrations Mantidas (56)

### Categorias de Migrations Mantidas

1. **Migrations de Colunas (ALTER)** - Adicionam colunas a tabelas existentes
   - Ex: `20200306012421_add_cep_to_usuarios_table.php`
   - Ex: `20220320173741_add_desconto_lancamentos_os_vendas.php`

2. **Migrations de Correções (FIX)** - Corrigem estruturas existentes
   - Ex: `20210107190526_fix_table_cobrancas.php`
   - Ex: `20210114151943_drop_table_pagamento.php`

3. **Migrations de Features** - Adicionam funcionalidades
   - Ex: `20210110153941_feature_notificawhats.php`
   - Ex: `20210125173741_asaas_payment_gateway.php`

4. **Migrations de Permissões** - Adicionam permissões ao sistema
   - Ex: `20250403000002_add_permissao_atendimentos.php`
   - Ex: `20250411000004_add_permissoes_impostos.php`

5. **Migrations de Tabelas Novas** - Criam tabelas NÃO presentes em banco.sql
   - `007_create_checklist.php` - Tabelas de checklist (não estão em banco.sql)
   - `20250403000001_add_checkin_tables.php` - Tabelas de checkin (estrutura diferente)
   - `20250412000003_create_usuarios_cliente.php` - Tabela usuarios_cliente (não está em banco.sql)
   - `20250412000099_atualizacao_completa_sistema.php` - Migration consolidada segura

6. **Migrations de Performance**
   - `20260411000004_add_performance_indexes.php` - Adiciona índices de performance

## Localização dos Backups

- **Backup completo**: `application/database/migrations_backup/` (67 arquivos)
- **Migrations obsoletas**: `application/database/migrations_obsoletas/` (11 arquivos)

## Tabelas no banco.sql vs Migrations

### Tabelas já existentes em banco.sql (não precisam de migration para criação)

| Tabela | Fonte |
|--------|-------|
| ci_sessions | banco.sql |
| clientes | banco.sql |
| resets_de_senha | banco.sql |
| categorias | banco.sql |
| contas | banco.sql |
| permissoes | banco.sql |
| usuarios | banco.sql |
| lancamentos | banco.sql |
| garantias | banco.sql |
| os | banco.sql |
| produtos | banco.sql |
| produtos_os | banco.sql |
| servicos | banco.sql |
| servicos_os | banco.sql |
| vendas | banco.sql |
| cobrancas | banco.sql |
| itens_de_vendas | banco.sql |
| anexos | banco.sql |
| documentos | banco.sql |
| marcas | banco.sql |
| equipamentos | banco.sql |
| equipamentos_os | banco.sql |
| logs | banco.sql |
| email_queue | banco.sql |
| email_tracking | banco.sql |
| email_clicks | banco.sql |
| scheduled_events | banco.sql |
| webhooks | banco.sql |
| webhook_logs | banco.sql |
| certificado_config | banco.sql |
| nfse_importada | banco.sql |
| dre_contas | banco.sql |
| dre_lancamentos | banco.sql |
| impostos_config | banco.sql |
| impostos_retidos | banco.sql |
| push_notifications | banco.sql |
| checkin | banco.sql |
| os_tecnico_atribuicao | banco.sql |
| fotos_atendimento | banco.sql |
| os_status_history | banco.sql |
| anotacoes_os | banco.sql |
| configuracoes | banco.sql |
| migrations | banco.sql |
| os_nfse_emitida | banco.sql |
| os_boleto_emitido | banco.sql |
| servicos_catalogo | banco.sql |
| os_servicos | banco.sql |
| tec_os_execucao | banco.sql |
| tec_checklist_template | banco.sql |
| tec_estoque_veiculo | banco.sql |
| tec_rotas_tracking | banco.sql |
| obras | banco.sql |
| obra_etapas | banco.sql |
| obra_diario | banco.sql |
| obra_equipe | banco.sql |
| tec_estoque_historico | banco.sql |

### Tabelas criadas por migrations (NÃO estão em banco.sql)

| Tabela | Migration |
|--------|-----------|
| checklist_templates | 007_create_checklist.php |
| checklist_template_items | 007_create_checklist.php |
| os_checklist | 007_create_checklist.php |
| os_timeline | 007_create_checklist.php |
| os_pecas_utilizadas | 007_create_checklist.php |
| os_etapas | 007_create_checklist.php |
| tecnico_competencias | 007_create_checklist.php |
| tecnico_avaliacoes | 007_create_checklist.php |
| usuarios_cliente | 20250412000003_create_usuarios_cliente.php |
| usuarios_cliente_cnpjs | 20250412000003_create_usuarios_cliente.php |
| usuarios_cliente_permissoes | 20250412000003_create_usuarios_cliente.php |
| dre_demonstracoes | 20250412000099_atualizacao_completa_sistema.php |
| calculos_impostos | 20250412000099_atualizacao_completa_sistema.php |
| os_documentos | 20250412000099_atualizacao_completa_sistema.php |

## Recomendações

1. **Para novas instalações**: O arquivo `banco.sql` já contém todas as tabelas base e as tabelas V5. As migrations restantes são para:
   - Adicionar colunas a tabelas existentes
   - Configurar permissões
   - Criar tabelas adicionais não presentes em banco.sql

2. **Para atualizações**: O sistema de migrations atual irá:
   - Verificar se colunas já existem antes de adicioná-las
   - Criar tabelas novas se não existirem
   - Atualizar permissões conforme necessário

3. **Migration consolidada**: A migration `20250412000099_atualizacao_completa_sistema.php` é segura pois verifica se tabelas existem antes de criá-las (`table_exists()`).
