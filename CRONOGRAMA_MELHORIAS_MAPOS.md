# Cronograma de Melhorias — MapOS V5 (Sistema Completo)

> Análise completa do sistema MapOS: aplicação PHP (CodeIgniter), API v2, WhatsApp Agent (Python) e frontend.

---

## Visão Geral por Severidade

| Severidade | Quantidade | Área Principal |
|------------|-----------|----------------|
| CRÍTICO | 22 | Segurança (XSS, SQLi, credenciais, auth) |
| ALTO | 28 | Estabilidade, performance, dados |
| MÉDIO | 24 | Arquitetura, manutenibilidade |
| BAIXO | 12 | Qualidade de código, technical debt |

---

## Fase 1 — Segurança Crítica (Semana 1-2)

> **Risco**: vazamento de dados, invasão, fraude financeira

### 1.1 XSS Sistêmico nas Views (~200+ arquivos)

**Problema**: Quase todas as views usam `<?php echo $r->campo ?>` sem `htmlspecialchars()`. Qualquer campo do banco vira HTML executável.

**Arquivos afetados**: `views/clientes/clientes.php`, `views/os/os.php`, `views/clientes/visualizar.php`, `views/cobrancas/cobrancas.php`, e ~200 outros.

**Ação**:
- Criar helper `e()` (escape) global em `helpers/general_helper.php`
- Buscar e substituir todos `<?php echo $r->` → `<?php echo e($r->` e `<?= $var ?>` → `<?= e($var) ?>`
- Views prioritárias: clientes, OS, cobranças, financeiro, login
- Refletido: `views/agente_ia/logs_conversa.php` linhas 27-35 usam `$_GET` direto

**Esforço**: 3-4 dias

### 1.2 SQL Injection

| Local | Risco | Ação |
|-------|-------|------|
| `Cobrancas_model.php:43,48` | **CRÍTICO** — `$id` interpolado em SQL raw | Migrar para query builder com `$this->db->where()` |
| `Cobrancas_model.php:161,176,191,207` | **CRÍTICO** — dynamic library loading do banco | Validar `$gatewayDePagamento` contra whitelist |
| `Mapos_model.php:283-286` | Alto — raw SQL sem parâmetros | Manter mas documentar que é seguro |
| `Relatorios_model.php:64-81` | Médio — WHERE construído com string | Migrar para query builder |
| `Os_model.php:20` — `$where` dinâmico | Médio — string vira SQL raw | Validar formato de `$where` no controller |
| `Conecte_model.php:56-57,77` | Médio — mesmo padrão `$where` | Validar no controller |

**Esforço**: 2 dias

### 1.3 Credenciais Expostas

| Local | Problema | Ação |
|-------|----------|------|
| `.env` no git | Senha MySQL, API keys reais | Adicionar `.env` ao `.gitignore`, criar `.env.example` com placeholders |
| `env_update.php:22` | API key hardcoded no código | Remover, ler exclusivamente do `.env` |
| `tests/teste_evolution.php:17` | API key Evolution hardcoded | Remover arquivo ou mover para `.env` |
| `tests/test_endpoints_evolution.php:9` | API key + instance token | Remover arquivo ou mover para `.env` |
| `AuthController.php:106` | JWT secret fallback `'mapos-secret-key'` | Remover fallback, falhar se env não definido |
| `Api.php:27` | JWT secret passado para a view | Remover da view, usar apenas no backend |
| `BaseController.php:78` | API key concede permissão `['*']` | Implementar scopes na API key |

**Esforço**: 1 dia

### 1.4 Autenticação e Autorização

| Local | Problema | Ação |
|-------|----------|------|
| `Login.php` | Sem proteção contra brute force | Adicionar rate limiting (5 tentativas, lockout 15min) |
| `Login.php:27-29` | CORS `base_url()` como origem | Restringir a domínios específicos |
| `AuthController.php:176-179` | Fallback MD5 para senhas | Adicionar migração forçada: ao logar com MD5, re-hash com bcrypt |
| `Permission.php:44-46` | `idPermissao == 1` = acesso total | Implementar permissões granulares reais |
| `BaseController.php:72-91` | API key com acesso total | Criar scopes (read, write, admin) |
| Webhook `cora()` em `Webhook.php:40-94` | Sem verificação de assinatura | Implementar HMAC/sha256 signature verification |
| 6 endpoints autocomplete | Sem verificação de permissão | Adicionar check de permissão em cada um |
| `config.php:451-478` | 15+ URIs excluídas do CSRF | Reduzir lista ao mínimo necessário |

**Esforço**: 3 dias

### 1.5 Outros Problemas Críticos

| Local | Problema | Ação |
|-------|----------|------|
| `Os.php:1254-1274` | Upload com `max_size = 0` e tipos `.cdr` | Definir limite (10MB), remover tipos perigosos |
| `Os.php:1006,1066` | `echo json_encode()` bypassa CI output | Usar `$this->output->set_output()` |
| `Mapos.php:163-164` | `print_r($upload_error); exit();` em produção | Trocar por log + mensagem genérica |
| `Clientes.php:70-76` | Validação CPF/CNPJ aceita letras | Corrigir regex para aceitar apenas dígitos |
| `Os.php:47-49` | Parsing de data sem validação | Validar formato `dd/mm/yyyy` antes do `explode` |
| `Os.php:1382-1401` | Array syntax quebrado em desconto | Corrigir `['messages', 'texto']` → `['messages' => 'texto']` |

**Esforço**: 2 dias

---

## Fase 2 — Estabilidade e Integridade de Dados (Semana 3-4)

> **Risco**: perda de dados, inconsistência, crash em produção

### 2.1 Transações Faltantes

| Local | Problema | Ação |
|-------|----------|------|
| `Clientes_model.php:148-166` | `removeClientOs()` — 3 DELETEs por OS sem transação | Envolver em `$this->db->trans_start()` / `trans_complete()` |
| `Clientes_model.php:188-201` | `removeClientVendas()` — mesmo padrão | Envolver em transação |
| `Os.php:825-868` | `excluir()` — deleta de 4 tabelas sem transação | Envolver em transação |
| `Os.php:1426-1528` | `faturar()` — transação não cobre leituras iniciais | Expandir escopo da transação |
| `Os_model.php` — `criar_os` | INSERT em `os` + N INSERTs em `produtos_os`/`servicos_os` sem transação | Envolver em transação |
| `Mapos_model.php:492-503` | `saveConfiguracao()` — N UPDATEs sem transação | Envolver em transação |
| WhatsApp agent `session_store.py:71-83` | INSERT/UPDATE redundante com try/except que mascara erros | Remover try/except, usar apenas `ON DUPLICATE KEY UPDATE` |

**Esforço**: 3 dias

### 2.2 Hard Delete em Tudo

**Problema**: Todos os modelos fazem `DELETE` físico. Dados financeiros, OS e usuários são permanentemente apagados — viola requisitos fiscais brasileiros (NF-e, PIS/COFINS).

**Ação**:
- Adicionar coluna `deleted_at TIMESTAMP NULL` em: `os`, `clientes`, `produtos`, `servicos`, `usuarios`, `cobrancas`, `lancamentos`
- Criar migration para adicionar colunas
- Alterar `delete()` em todos os models para soft delete (`UPDATE SET deleted_at = NOW()`)
- Adicionar scope global `WHERE deleted_at IS NULL` em todas as queries
- Criar `forceDelete()` para limpeza administrativa

**Esforço**: 4 dias

### 2.3 Mass Assignment

**Problema**: `add($table, $data)` e `edit($table, $data, $fieldID, $ID)` em todos os models aceitam qualquer array sem filtro. Controller pode passar `$this->input->post()` direto.

**Ação**:
- Adicionar propriedade `$fillable` em cada model listando campos permitidos
- Filtrar `$data` no `add()` e `edit()` contra `$fillable`
- Remover parâmetro `$table` dos methods públicos — cada model deve operar apenas na sua tabela

**Esforço**: 3 dias

### 2.3 N+1 Queries e Performance

| Local | Problema | Ação |
|-------|----------|------|
| `Clientes_model.php:148-166` | Loop com 3 DELETEs por OS | Usar `WHERE os_id IN (...)` com batch delete |
| `Os_model.php:556-577` | 3 queries para `valorTotalOS()` | Consolidar em 1 query com `SUM()` subqueries |
| `Mapos_model.php:260-280` | JOIN com produtos_os + servicos_os causa multiplicação | Remover JOINs, usar subqueries |
| `Os_model.php:84-92` | Subqueries correlatas em SELECT para cada OS | Pré-calcular via JOIN com GROUP BY |
| `Mapos.php:index()` | 10 queries separadas para dashboard | Consolidar em 2-3 queries otimizadas |
| WhatsApp agent `database.py` | Sem `pool_recycle` | Adicionar `pool_recycle=3600` ao engine |
| WhatsApp agent `evolution_api.py` | Sem retry em chamadas API | Adicionar retry com exponential backoff (3 tentativas) |

**Esforço**: 3 dias

### 2.4 Erros Silenciosos e Resource Leaks

| Local | Problema | Ação |
|-------|----------|------|
| WhatsApp `main.py:386,467` | `except Exception: return ''` | Logar erro e retornar `None` |
| WhatsApp `main.py:798` | Temp PDF file leak | Mover cleanup para `finally` |
| WhatsApp `main.py:49-50` | `_msg_ids` dict sem thread-safety | Usar `threading.Lock` |
| WhatsApp `main.py:995-998` | Race condition read-modify-write em sessões | Adicionar locking ou optimistic concurrency |
| `Os_model.php:184` | `affected_rows() == '1'` (string vs int) | Trocar para `=== 1` |
| `Relatorios_model.php:452,508` | Bug aritmético: `= $servicosTotal` ao invés de `+` | Corrigir `+ $industriaTotal + $servicosTotal` |
| `database.py` (WhatsApp) | Sem error handling em `execute_*` | Adicionar try/except com retry para deadlock |

**Esforço**: 2 dias

---

## Fase 3 — Arquitetura e Refatoração (Semana 5-7)

> **Foco**: reduzir duplicação, melhorar manutenibilidade

### 3.1 Decompor WhatsApp Agent (`main.py` — 2956 linhas)

| Módulo Novo | Funções a Mover |
|-------------|----------------|
| `handlers/webhook.py` | `webhook_evolution`, `extrair_mensagem`, `extrair_numero`, `extrair_audio_info` |
| `commands/` | `processar_comando` → dispatch table com `COMANDOS = {'status_os': cmd_status_os, ...}` |
| `flows/os_creation.py` | `processar_criacao_os`, `_avancar_etapa_os`, `criar_os_completa_via_audio` |
| `flows/status_change.py` | `processar_alterar_status` |
| `services/pdf.py` | `gerar_pdf_relatorio`, `enviar_pdf_whatsapp`, `gerar_e_enviar_pdf` |
| `api/dashboard.py` | Todos os endpoints `/api/*` e `/dashboard` |

**Esforço**: 4 dias

### 3.2 Decompor Controller `Os.php` (1893 linhas)

| Módulo Novo | Funções a Mover |
|-------------|----------------|
| `Os_email_trait.php` | `enviarOsPorEmail`, lógica de notificação |
| `Os_discount_service.php` | `adicionarDesconto`, reset de desconto |
| `Os_upload_service.php` | `anexar`, `excluirAnexo`, validação de upload |

**Esforço**: 2 dias

### 3.3 Eliminar Duplicação de Código

| Padrão Duplicado | Onde | Ação |
|------------------|------|------|
| Perfil de usuário | `main.py:70-86,252-268,310-339` | Criar `get_perfil(usuario)` centralizado |
| PDF generation | `main.py:744-771,962-978,1598-1618` | Criar `gerar_e_enviar_pdf()` |
| LLM call pattern | `llm.py:82-125,128-170,173-221` | Criar `_call_llm(provider, prompt)` |
| Busca de clientes | `main.py:~1050-1270` (4x) | Criar `buscar_e_listar_clientes()` |
| Date parsing `explode('/')` | `Os.php:47,119,269` | Criar helper `parse_date_br()` |
| Discount reset block | `Os.php:957,998,1036,1117,1233` | Criar `reset_os_desconto($id)` |
| Model CRUD genérico | Todos os models | Criar `MY_Model` base com `$fillable`, soft delete |
| `echo json_encode()` em models | `Os_model`, `Financeiro_model`, `Vendas_model` | Mover para controllers |

**Esforço**: 3 dias

### 3.4 Consistent Error Returns

**Problema**: Mistura de `None`, `''`, `{'success': False}`, dicts e `False`.

**Ação**:
- WhatsApp agent: criar dataclass `Result(success: bool, data: Any, error: str)`
- PHP: criar `ApiResponse` helper com `success()`, `error()`, `notFound()` consistentes

**Esforço**: 2 dias

---

## Fase 4 — Performance e Escalabilidade (Semana 8-9)

### 4.1 Database Indexes

Criar migration com indexes nas colunas mais filtradas:

```sql
-- OS
CREATE INDEX idx_os_status ON os(status);
CREATE INDEX idx_os_data_inicial ON os(dataInicial);
CREATE INDEX idx_os_tecnico ON os.tecnico_responsavel;
CREATE INDEX idx_os_cliente ON os.clientes_id;
CREATE INDEX idx_os_status_data ON os(status, dataInicial);

-- Lancamentos
CREATE INDEX idx_lanc_baixado ON lancamentos(baixado);
CREATE INDEX idx_lanc_data ON lancamentos(data_pagamento);
CREATE INDEX idx_lanc_tipo ON lancamentos(tipo);

-- WhatsApp
CREATE INDEX idx_whatsapp_sessoes_numero_tipo ON whatsapp_sessoes(numero_telefone, tipo);
CREATE INDEX idx_whatsapp_log_data ON whatsapp_log_interacoes(created_at);
```

**Esforço**: 1 dia

### 4.2 Query Optimization

| Query | Problema | Solução |
|-------|----------|---------|
| `Mapos_model:getEstatisticasFinanceiro*` | 24 CASE statements hardcoded | Refatorar para GROUP BY com MONTH() |
| `Mapos_model:getProdutosMinimo` | `LIMIT 10` hardcoded | Tornar configurável |
| `Os_model:autoComplete*` | Sem cache | Adicionar cache Redis ou em memória (TTL 5min) |
| `buscar_cliente_por_nome` (WhatsApp) | 4 queries sequenciais (Loja, CNPJ, ID, nome) | Consolidar em 1 query com OR |
| WhatsApp handler bloqueante | `async def` com chamadas sync | Usar `run_in_executor` ou migrar para `httpx` async |

**Esforço**: 3 dias

### 4.3 Frontend Modernization

| Item | Problema | Ação |
|------|----------|------|
| jQuery 1.12.4 (2016) | CVEs conhecidos | Migrar para jQuery 3.7+ |
| jQuery UI 1.9.2 (2012) | Vulnerável | Migrar para jQuery UI 1.13+ |
| Bootstrap 2.x | End-of-life desde 2016 | Planejar migração para Bootstrap 5 |
| wysihtml5 0.3.0 | XSS conhecido | Migrar para TinyMCE ou Quill |
| CDN sem SRI | `tema/topo.php:36-38` | Adicionar `integrity` e `crossorigin` |

**Esforço**: 5 dias (planejamento) — execução é gradual

---

## Fase 5 — Dados e Compliance (Semana 10-11)

### 5.1 Dados Sensíveis

| Local | Problema | Ação |
|-------|----------|------|
| `Mapos_model.php:29-34` | `SELECT usuarios.*` inclui `senha` (hash) | Remover `senha` do SELECT ou excluir coluna |
| `Usuarios_model.php:12-13` | Mesmo problema | Selecionar colunas explícitas |
| `Os_model.php:122,135` | `SELECT os.*, clientes.*` | Selecionar apenas colunas necessárias |
| `Os_model.php:336-348` | Autocomplete retorna CPF/CNPJ, telefone, email | Limitar campos retornados por perfil |
| `agente_ia_configuracoes` | API keys em texto plano | Criptografar com `APP_ENCRYPTION_KEY` |
| WhatsApp `.env` | Senha MySQL, API keys | Garantir que `.env` está no `.gitignore` |
| `views/clientes/clientes.php:82` | Email do cliente na URL (`mine?e=`) | Usar token criptografado em vez de email |

**Esforço**: 3 dias

### 5.2 Auditoria e Rastreabilidade

**Problema**: Sem soft delete, sem log de auditoria, sem rastreabilidade de alterações financeiras.

**Ação**:
- Criar tabela `audit_log` (user_id, action, table, record_id, old_data, new_data, ip, created_at)
- Criar `Audit_model` com trigger automático
- Implementar middleware de auditoria no `MY_Controller`
- Adicionar `deleted_at` em todas as tabelas principais (Fase 2.2)

**Esforço**: 4 dias

### 5.3 LGPD (Lei Geral de Proteção de Dados)

| Requisito | Status Atual | Ação |
|-----------|-------------|------|
| Consentimento | Não implementado | Adicionar flag `consentimento_lgpd` em `clientes` |
| Direito ao esquecimento | Hard delete apenas | Implementar anonimização (manter OS, anonimizar dados pessoais) |
| Portabilidade | Não implementado | Criar endpoint `GET /api/v2/clientes/{id}/export` |
| Notificação de vazamento | Não implementado | Criar tabela e notificação automática |

**Esforço**: 3 dias

---

## Fase 6 — Testes e Qualidade (Semana 12+)

### 6.1 Testes Unitários

| Área | Framework | Prioridade |
|------|-----------|------------|
| WhatsApp Agent (Python) | `pytest` + `pytest-mock` | Alta — testar classificação NLP, fluxo OS, sessões |
| PHP Models | `PHPUnit` + `CI3 mock` | Alta — testar CRUD, validação, permissões |
| PHP Controllers | `PHPUnit` | Média — testar auth, CRUD endpoints |
| API v2 | `PHPUnit` + HTTP client | Alta — testar JWT auth, CRUD, webhooks |

**Esforço**: 5+ dias

### 6.2 Testes de Integração e Segurança

| Tipo | Ferramenta | Escopo |
|------|-----------|--------|
| SQL Injection scan | `sqlmap` | Todos os endpoints CRUD |
| XSS scan | Manual + browser | Todas as views com `echo` |
| Auth bypass | Manual | Login, API auth, permission check |
| CSRF | Manual | Forms sem token explícito |
| Rate limiting | `ab` / `wrk` | Login, webhook, API |

**Esforço**: 3 dias

### 6.3 CI/CD

| Item | Ferramenta | Ação |
|------|-----------|------|
| Lint PHP | `phpcs` / `php-cs-fixer` | Verificar PSR-12 |
| Type check Python | `mypy` | Type hints em todos os módulos |
| Lint Python | `ruff` | Verificar estilo |
| Testes automáticos | GitHub Actions | Rodar pytest + PHPUnit em cada PR |
| Security scan | `composer audit` + `pip audit` | Dependências vulneráveis |

**Esforço**: 2 dias

---

## Resumo: Cronograma Visual

```
Semana 1-2:  ██████ Fase 1 — Segurança Crítica (XSS, SQLi, Credenciais, Auth)
Semana 3-4:  ██████ Fase 2 — Estabilidade (Transações, Soft Delete, Mass Assignment, N+1)
Semana 5-7:  ██████████ Fase 3 — Arquitetura (Decompor main.py, Eliminar Duplicação, Error Returns)
Semana 8-9:  ██████ Fase 4 — Performance (Indexes, Query Opt, Frontend, Async)
Semana 10-11: ██████ Fase 5 — Dados & Compliance (Dados Sensíveis, Auditoria, LGPD)
Semana 12+:   ██████ Fase 6 — Testes & Qualidade (Unit, Integration, CI/CD)
```

## Top 10 Ações Imediatas (Fazer Primeiro)

| # | Ação | Impacto | Esforço |
|---|------|---------|---------|
| 1 | Adicionar `.env` ao `.gitignore` e criar `.env.example` com placeholders | Evita vazamento de credenciais | 1h |
| 2 | Remover API keys hardcoded de `env_update.php`, `tests/` e `Api.php` | Evita acesso não autorizado | 2h |
| 3 | Corrigir SQL Injection em `Cobrancas_model.php:43,48` | Evita invasão via DB | 2h |
| 4 | Adicionar `htmlspecialchars()` nas views prioritárias (clientes, OS, cobranças, login) | Evita XSS em massa | 1 dia |
| 5 | Implementar rate limiting no `Login.php` | Evita brute force | 4h |
| 6 | Remover fallback MD5 em `AuthController.php:176-179` | Evita auth com hash fraco | 4h |
| 7 | Adicionar verificação de assinatura no webhook `cora()` | Evita fraude financeira | 4h |
| 8 | Envolver `removeClientOs()` em transação | Evita dados inconsistentes | 2h |
| 9 | Adicionar `pool_recycle=3600` no `database.py` do WhatsApp agent | Evita "MySQL server has gone away" | 15min |
| 10 | Corrigir bug aritmético em `Relatorios_model.php:452,508` | Relatórios financeiros corretos | 30min |