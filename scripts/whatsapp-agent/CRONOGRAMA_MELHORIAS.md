# Cronograma de Melhorias — WhatsApp Agent MapOS

## Resumo Executivo

Análise completa do projeto identificou **8 problemas criticos**, **12 problemas altos**, **14 medios** e **10 baixos**. O cronograma esta organizado em 6 fases, priorizando seguranca, estabilidade e manutenibilidade.

---

## Fase 1 — Seguranca e Correcoes Criticas (Semana 1)

> **Prioridade: CRITICA** — Risco de vazamento de credenciais, SQL injection e dados corrompidos

| # | Item | Arquivo | Descricao |
|---|------|---------|-----------|
| 1.1 | Credenciais no `.env` | `.env` | Senha MySQL, API keys e host de producao commitados no git. Mover para `.env.production` e adicionar ao `.gitignore` |
| 1.2 | API key hardcoded | `env_update.py:22` | `LLM_CLOUD_API_KEY` real no codigo-fonte. Remover e ler exclusivamente do `.env` |
| 1.3 | `.env.example` com dados reais | `.env.example` | Contem host e keys de producao. Substituir por valores placeholder |
| 1.4 | Webhook sem auth obrigatoria | `main.py:2236-2237` | Se `x_api_key` nao for enviado, o request passa sem autenticacao. Alterar para rejeitar quando `AGENT_API_KEY` esta configurado |
| 1.5 | MAC verification bypassada | `whatsapp_media.py:70` | Falha na verificacao MAC loga warning mas continua descriptografia com dados potencialmente adulterados. Deve rejeitar o audio |
| 1.6 | SQL f-string em `resumo_os_dia` | `mapos_queries.py:217-225` | `CURDATE()` injetado via f-string. Refatorar para usar SQL direto sem interpolacao |
| 1.7 | Senha MySQL nao URL-encoded | `config.py:59` | Se a senha contem `@`, `:`, `/` ou `#`, a URL quebra silenciosamente. Usar `urllib.parse.quote_plus()` |
| 1.8 | Dashboard API key em query string | `main.py:2788` | Key aparece em logs e historico do navegador. Migrar para header ou cookie |

---

## Fase 2 — Estabilidade e Tratamento de Erros (Semana 2)

> **Prioridade: ALTA** — Erros silenciosos, race conditions e falta de retry causam perda de dados e mensagens

| # | Item | Arquivo | Descricao |
|---|------|---------|-----------|
| 2.1 | `database.py` sem error handling | `database.py:19-47` | Nenhuma funcao (`execute_query`, `execute_insert`, etc.) tem tratamento de erro. DB fora = crash sem log util. Adicionar try/except com retry para deadlock (MySQL 1213) e connection timeout |
| 2.2 | `except Exception: return ''` silencioso | `main.py:386,467` | `extrair_numero` e `extrair_mensagem` engolem erros e retornam vazio. Logar o erro e retornar None explicitamente |
| 2.3 | `except Exception: pass` em cleanup | `main.py:798,2293,2299` | Falha de cleanup de temp file e ignorada. Usar `finally` blocks para garantir cleanup |
| 2.4 | Race condition em sessoes | `main.py:995-998` | Read-modify-write sem lock em `processar_criacao_os`. Duas mensagens simultaneas sobrescrevem a sessao. Implementar optimistic locking com versao ou SELECT FOR UPDATE |
| 2.5 | `_msg_ids` nao thread-safe | `main.py:49-50` | Dict mutavel compartilhado entre threads sem lock. Migrar para `threading.Lock` ou `collections.OrderedDict` com lock |
| 2.6 | Sem retry em Evolution API | `evolution_api.py` | Nenhuma chamada de API tem retry. Falha transitoria = mensagem perdida. Adicionar retry com exponential backoff (3 tentativas) |
| 2.7 | `criar_os` sem transacao atomica | `mapos_queries.py:619-668` | OS criada em 1 INSERT + itens em N INSERTs separados. Se falhar no meio, OS fica incompleta. Envolver em transacao explicita |
| 2.8 | `set_os_session` try/except redundante | `session_store.py:71-83` | Ja usa `ON DUPLICATE KEY UPDATE` mas faz fallback com try/except que mascara erros reais. Remover o try/except |
| 2.9 | `criar_os_via_api` fallback pode duplicar | `main.py:2617-2641` | Se API retorna 201 em vez de 200, fallback SQL cria OS duplicada. Verificar status code range e adicionar log antes de fallback |
| 2.10 | PDF temp file leak | `main.py:789-800` | Se `enviar_documento` falha, `os.unlink` nao e executado. Mover para `finally` |
| 2.11 | Evolution API erros engolidos | `evolution_api.py` | Todos os metodos usam `except Exception as e` e retornam dict. Callers raramente verificam `success`. Adicionar logging de traceback e propagar erros criticos |
| 2.12 | Falta `pool_recycle` no DB | `database.py:6-13` | Sem `pool_recycle`, conexoes ficam stale e geram "MySQL server has gone away". Adicionar `pool_recycle=3600` |

---

## Fase 3 — Arquitetura e Organizacao (Semanas 3-4)

> **Prioridade: MEDIA** — Codigo monolitico dificulta manutencao, teste e evolucao

| # | Item | Arquivo | Descricao |
|---|------|---------|-----------|
| 3.1 | Decompor `main.py` (2956 linhas) | `main.py` | Separar em modulos: `handlers/webhook.py`, `commands/`, `flows/os_creation.py`, `flows/status_change.py`, `services/pdf.py`, `api/dashboard.py` |
| 3.2 | Duplicacao de logica de perfil | `main.py:70-86,252-268,310-339` | Logica `permissoes_id → perfil` repetida 3x. Criar `get_perfil(usuario)` centralizada |
| 3.3 | Duplicacao de geracao de PDF | `main.py:744-771,962-978,1598-1618` | Tres blocos quase identicos. Extrair para `services/pdf.py` com `gerar_e_enviar_pdf(tipo, params)` |
| 3.4 | Duplicacao de LLM call patterns | `llm.py:82-125,128-170,173-221` | `_classificar_ollama`, `_classificar_openai`, `_classificar_anthropic` seguem o mesmo pattern. Refatorar para `_call_llm(provider, prompt)` com handler por provider |
| 3.5 | Duplicacao de busca de clientes | `main.py:~1050-1270` | Pattern "buscar cliente + construir lista" repetido 4x. Extrair para `buscar_e_listar_clientes(numero, nome, usuario)` |
| 3.6 | `processar_comando` gigante | `main.py:1928-2148` | 217 linhas de if/elif. Refatorar para dispatch table: `COMANDOS = {'status_os': cmd_status_os, ...}` |
| 3.7 | Inconsistent error return types | Multiplos arquivos | Alguns retornam `None`, outros `{'success': False}`, outros `''`. Padronizar com dataclass `Result(success, data, error)` |
| 3.8 | Imports dentro de funcoes | `main.py:787,1018,2041,2293` | `import tempfile`, `import re`, `import os` dentro de funcoes. Mover para topo do arquivo |
| 3.9 | Scheduler no modulo | `main.py:2936-2950` | `BackgroundScheduler` iniciado no import. Mover para startup event do FastAPI |
| 3.10 | `PERMISSOES_MAP` hardcoded | `main.py:53-60` | Mapeamento de permissoes acoplado a IDs do banco. Carregar do banco na inicializacao ou configurar via `.env` |

---

## Fase 4 — Performance e Confiabilidade (Semana 5)

> **Prioridade: MEDIA** — Problemas de performance sob carga e gaps de validacao

| # | Item | Arquivo | Descricao |
|---|------|---------|-----------|
| 4.1 | Handler webhook bloqueante | `main.py:2231` | FastAPI `async def` com chamadas sincronas (DB, HTTP, LLM). Usar `run_in_executor` ou migrar para `httpx` async |
| 4.2 | Sem rate limiting | Todos os endpoints | Sem limitacao de requisicoes no webhook e API. Adicionar `slowapi` ou middleware customizado |
| 4.3 | `buscar_cliente_por_nome` N+1 queries | `mapos_queries.py:89-136` | Executa ate 4 queries sequenciais (Loja, CNPJ, ID, nome). Consolidar em query unica com OR |
| 4.4 | Sessoes em DB a cada passo | `session_store.py` | Cada interacao gera 1-4 round-trips ao DB para sessao. Implementar cache em memoria com write-behind |
| 4.5 | Audio baixado inteiro na memoria | `whatsapp_media.py:110-112` | `resp.content` carrega arquivo inteiro na RAM. Para audios longos, usar streaming para disco |
| 4.6 | Validacao de `itens` em `criar_os` | `mapos_queries.py:619-668` | Sem validacao de `id`, `quantidade` ou `preco`. Aceita valores None ou negativos. Adicionar validacao antes do INSERT |
| 4.7 | Validacao de numero de telefone | `main.py:527-531` | `limpar_numero` aceita qualquer input. Validar comprimento minimo (ex: 12-15 digitos) |
| 4.8 | `limpar_pdfs_temp` limpa dir inexistente | `main.py:2915` | Funcao limpa `assets/relatorios_temp` que nunca e usado (usa `tempfile`). Remover dead code |

---

## Fase 5 — Funcionalidades Faltantes (Semana 6)

> **Prioridade: BAIXA** — Melhorias de usabilidade e completude

| # | Item | Arquivo | Descricao |
|---|------|---------|-----------|
| 5.1 | Comandos ignorados dentro de sessao | `main.py:1938-1942` | Se usuario esta em sessao de OS e digita "ajuda" ou "status os", a sessao intercepta. Adicionar deteccao de comandos de escape (`#sair`, `cancelar`, `ajuda`) |
| 5.2 | Ambiguidade em "cancelar" | `main.py:1679,1796` | No fluxo de status, "cancelar" pode significar "sair do fluxo" ou "status Cancelado". Desambiguar com contexto |
| 5.3 | Mensagem para midia nao suportada | `main.py:423-468` | Imagens, videos e documentos sao ignorados sem feedback. Responder "Nao consigo processar imagens/videos. Envie texto ou audio." |
| 5.4 | CORS para dashboard | `main.py` | Sem CORS middleware, dashboard nao funciona de origens diferentes. Adicionar `CORSMiddleware` |
| 5.5 | Graceful shutdown | `main.py:2936-2950` | Sem signal handler para scheduler e DB connections. Adicionar `atexit` e `signal.signal` |
| 5.6 | Log estruturado | Todos os arquivos | Usar `structlog` ou JSON formatter para facilitar busca e monitoramento |
| 5.7 | Health check detalhado | `main.py` | Endpoint `/health` que verifica DB, Evolution API e Whisper connectivity, nao apenas "running" |

---

## Fase 6 — Testes e Qualidade (Semana 7+)

> **Prioridade: BAIXA** — Fundacao para evolucao sustentavel

| # | Item | Descricao |
|---|------|-----------|
| 6.1 | Testes unitarios | Adicionar `pytest` com fixtures para DB mock, Evolution API mock e LLM mock. Cobrir: NLP classification, session management, OS creation flow, status change flow |
| 6.2 | Testes de integracao | Testar endpoints FastAPI com `TestClient`. Validar webhook, dashboard API, e fluxos completos |
| 6.3 | CI/CD pipeline | GitHub Actions com lint (`ruff`), type check (`mypy`), e testes automaticos |
| 6.4 | Type hints completos | Adicionar type hints em todas as funcoes publicas. Padronizar retornos com `Result[T]` ou `TypedDict` |
| 6.5 | Documentacao de API | Gerar OpenAPI schema automaticamente (FastAPI ja gera). Adicionar descricao e exemplos nos endpoints |
| 6.6 | Migration framework | Adicionar `alembic` para schema migrations. Criar migration inicial para todas as tabelas |

---

## Metricas de Impacto

| Fase | Esforco Estimado | Risco Reduzido | Ganho Principal |
|------|------------------|----------------|-----------------|
| Fase 1 | 2-3 dias | Credenciais expostas, SQL injection, auth bypass | Seguranca |
| Fase 2 | 4-5 dias | Perda de dados, mensagens silenciosas, crash em producao | Estabilidade |
| Fase 3 | 5-7 dias | Codigo impossivel de manter, regressao em mudancas | Manutenibilidade |
| Fase 4 | 3-4 dias | Lentidao sob carga, dados invalidos no DB | Performance |
| Fase 5 | 2-3 dias | UX inconsistente, gaps de funcionalidade | Usabilidade |
| Fase 6 | 5+ dias | Regressao invisivel em mudancas futuras | Qualidade |

---

## Top 5 Acoes Imediatas (Fazer Agora)

1. **Mover `.env` para `.gitignore`** e criar `.env.example` com placeholders
2. **Remover API key de `env_update.py`** e ler exclusivamente do `.env`
3. **Tornar auth do webhook obrigatoria** quando `AGENT_API_KEY` esta configurado
4. **Adicionar `pool_recycle=3600`** ao SQLAlchemy engine
5. **Corrigir MAC verification** em `whatsapp_media.py` para rejeitar audios adulterados