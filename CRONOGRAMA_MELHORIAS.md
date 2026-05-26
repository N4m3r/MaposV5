# MapOS v5 — Agente IA WhatsApp: Cronograma de Melhorias

> Última atualização: 2026-05-21

---

## Visão Geral do Estado Atual

O agente IA WhatsApp está funcional com **18+ comandos**, criação interativa de OS, alteração de status, 7 tipos de relatório com análise GLM, transcrição de áudio e geração de PDF. Este documento lista as melhorias priorizadas por criticidade e impacto.

---im

## Fase 1 — Correções Críticas (1-3 dias)

| # | Item | Arquivo | Descrição |
|---|------|---------|-----------|
| 1.1 | **API Key vazia aceita** | `main.py:87-90` | `verificar_api_key()` permite acesso sem chave. Corrigir para exigir a chave obrigatoriamente. |
| 1.2 | **Sessões nunca expiram** | `main.py:39-41` | `_limpar_sessoes_expiradas()` existe mas nunca é chamada e referencia `_timestamp` que não existe nas sessões. Implementar TTL funcional. |
| 1.3 | **ADMIN_NUMERO hardcoded** | `main.py:31` | Mover número admin para `config.py` / `.env` em vez de constante no código. |
| 1.4 | **Token fixo no BaseController** | `BaseController.php:66` | Token `t4AZOtKk...` com acesso total `['*']` hardcoded. Mover para `.env` e rotacionar. |
| 1.5 | **Fallback MD5 em auth** | `AuthController.php:162` | Senhas legadas com MD5 devem ser migradas para bcrypt na primeira tentativa bem-sucedida. |

### Detalhes Técnicos — Fase 1

**1.1 — API Key vazia:**
```python
# ANTES (inseguro):
if not x_api_key or x_api_key == '' or x_api_key == config.AGENT_API_KEY:
    return

# DEPOIS (seguro):
if not config.AGENT_API_KEY or x_api_key != config.AGENT_API_KEY:
    raise HTTPException(status_code=401, detail="API Key obrigatoria")
```

**1.2 — Sessões com TTL:**
```python
# Adicionar timestamp ao criar sessão
_sessoes_os[numero] = {
    'etapa': 'cliente',
    'dados': {...},
    '_timestamp': time.time()
}

# Chamar limpeza no webhook
_limpar_sessoes_expiradas()
_limpar_sessoes_status_expiradas()  # novo para _sessoes_status
```

**1.3 — Admin número configurável:**
```python
# config.py
ADMIN_NUMERO = os.getenv('ADMIN_NUMERO', '')  # sem default

# main.py
ADMIN_NUMERO = config.ADMIN_NUMERO
```

**1.5 — Migrar MD5 para bcrypt no login:**
```php
// AuthController.php - após validar senha
if (md5($password) === $hash) {
    // Migrar para password_hash automaticamente
    $this->db->where('idUsuarios', $user['idUsuarios'])
              ->update('usuarios', ['senha' => password_hash($password, PASSWORD_BCRYPT)]);
}
```

---

## Fase 2 — Segurança e Robustez (3-7 dias)

| # | Item | Arquivo | Descrição |
|---|------|---------|-----------|
| 2.1 | **CORS restritivo** | `BaseController.php:136` | Trocar `Access-Control-Allow-Origin: *` por whitelist de domínios configurável. |
| 2.2 | **Rate limiter funcional** | `BaseController.php:47-48` | Descomentar e implementar rate limiting real com cache (Redis ou arquivo). |
| 2.3 | **Validação de token JWT refresh** | `AuthController.php` | Verificar campo `type=refresh` antes de emitir novo token. |
| 2.4 | **API key via header apenas** | `BaseController.php:63` | Remover `$_GET['api_key']` para evitar vazamento em logs. |
| 2.5 | **Dual OS creation path** | `main.py` + `AcoesController.php` | Unificar: agente WhatsApp deve chamar a API `/api/v2/acoes/executar` em vez de SQL direto. |
| 2.6 | **Criação de OS com usuário real** | `main.py:728` | Passar `usuarios_id` do usuário identificado em vez de default `1`. |
| 2.7 | **Tabela `whatsapp_log_interacoes`** | Migrations | Criar migration para garantir que a tabela existe no banco. |
| 2.8 | **`cryptography` no requirements** | `requirements.txt` | Adicionar `cryptography>=42.0.0` ao requirements.txt. |
| 2.9 | **Limpeza automática de PDFs** | `RelatoriosController.php` | Criar cron ou hook que remove PDFs de `assets/relatorios_temp/` com mais de 24h. |

### Detalhes Técnicos — Fase 2

**2.5 — Unificar criação de OS via API:**
```python
# main.py — Ao invés de SQL direto:
def criar_os_via_api(cliente_id, descricao, defeito, usuario_id, status='Aberto'):
    url = f"{config.MAPOS_URL}/api/v2/acoes/executar"
    payload = {
        'acao': 'criar_os',
        'dados': json.dumps({
            'clientes_id': cliente_id,
            'descricaoProduto': descricao,
            'defeito': defeito,
            'usuarios_id': usuario_id,
            'status': status,
        }),
    }
    resp = http_requests.post(url, data=payload, headers={'X-API-KEY': config.MAPOS_API_KEY}, timeout=15)
    return resp.json()
```

**2.7 — Migration para tabela de logs:**
```sql
CREATE TABLE IF NOT EXISTS whatsapp_log_interacoes (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_telefone VARCHAR(20) NOT NULL,
    tipo_mensagem ENUM('texto','audio','documento') DEFAULT 'texto',
    direcao ENUM('entrada','saida') NOT NULL,
    conteudo TEXT,
    intencao_detectada VARCHAR(50),
    status VARCHAR(20) DEFAULT 'recebido',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_numero (numero_telefone),
    INDEX idx_intencao (intencao_detectada),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**2.9 — Cleanup de PDFs (adicionar ao controller):**
```php
private function limparPdfsAntigos()
{
    $dir = FCPATH . 'assets/relatorios_temp/';
    if (!is_dir($dir)) return;
    $limite = time() - 86400; // 24h
    foreach (glob($dir . '*.pdf') as $arquivo) {
        if (filemtime($arquivo) < $limite) unlink($arquivo);
    }
}
```

---

## Fase 3 — Funcionalidades Novas (7-14 dias)

| # | Item | Prioridade | Descrição |
|---|------|------------|-----------|
| 3.1 | **Persistência de sessões** | Alta | Mover `_sessoes_os` e `_sessoes_status` para Redis ou banco. Perde sessões ao reiniciar o processo. |
| 3.2 | **Webhook de eventos do MapOS** | Alta | Quando uma OS muda de status no painel, notificar o cliente via WhatsApp. |
| 3.3 | **Check-in/Check-out do técnico** | Média | Técnico envia localização ou confirma início/fim de atendimento. |
| 3.4 | **Agendamento de relatórios** | Média | Usar `agente_ia_relatorios_agendados` para enviar relatórios automáticos (diário, semanal). |
| 3.5 | **Pesquisa de satisfação pós-atendimento** | Média | Após OS finalizada, enviar pesquisa automaticamente. |
| 3.6 | **Integração Anthropic LLM** | Baixa | Implementar `_classificar_anthropic()` no `llm.py`. |
| 3.7 | **Notificação de OS atrasada** | Média | Cron diário que envia notificação ao técnico/admin de OS vencendo. |
| 3.8 | **Dashboard web do agente** | Baixa | Página simples em `/dashboard` com métricas de uso do agente. |

### Detalhes Técnicos — Fase 3

**3.1 — Persistência de sessões (Redis):**
```python
# services/session_store.py
import redis, json, time

class SessionStore:
    def __init__(self, redis_url='redis://localhost:6379'):
        self.r = redis.from_url(redis_url)

    def set(self, key, data, ttl=600):
        self.r.setex(f'sess:{key}', ttl, json.dumps(data))

    def get(self, key):
        val = self.r.get(f'sess:{key}')
        return json.loads(val) if val else None

    def delete(self, key):
        self.r.delete(f'sess:{key}')
```

**3.2 — Webhook de notificação do MapOS:**
```php
// application/controllers/Os.php — após alterar status
if ($this->settings->whatsapp_notificacoes) {
    $this->load->library('whatsapp_notifier');
    $this->whatsapp_notifier->notificarStatusOs($os_id, $novo_status);
}
```

**3.4 — Agendamento de relatórios (cron):**
```python
# jobs/relatorios_agendados.py
from apscheduler.schedulers.background import BackgroundScheduler

scheduler = BackgroundScheduler()

def enviar_relatorio_diario():
    """Envia relatório diário para admin cadastrado."""
    admins = queries.buscar_admins_agendamento('relatorio_diario')
    for admin in admins:
        dados = queries.relatorio_os_dia()
        resposta = nlp.formatar_resposta('relatorio_os', {'resumo': dados}, admin)
        evo.enviar_texto(admin['numero'], resposta)

scheduler.add_job(enviar_relatorio_diario, 'cron', hour=8, minute=0)
scheduler.start()
```

**3.7 — Notificação de OS atrasada (cron diário):**
```python
def notificar_os_vencendo():
    """Busca OS que vencem em 1 dia e notifica o técnico."""
    oss = queries.os_vencendo_amanha()
    for os_item in oss:
        numero = os_item.get('celular_tecnico') or os_item.get('celular')
        if numero:
            msg = (f"⚠️ A OS #{os_item['idOs']} do cliente {os_item['nomeCliente']} "
                   f"vence amanhã ({os_item['dataFinal']}). "
                   f"Status atual: {os_item['status']}")
            evo.enviar_texto(limpar_numero(numero), msg)
```

---

## Fase 4 — Otimização e Escala (14-21 dias)

| # | Item | Descrição |
|---|------|-----------|
| 4.1 | **Métricas e observabilidade** | Adicionar Prometheus metrics (mensagens processadas, tempo de resposta, erros LLM). |
| 4.2 | **Cache de queries** | Implementar cache com TTL (5min) para queries frequentes como `resumo_os_dia`, `total_os_abertas`. |
| 4.3 | **Containerização do agente** | Docker Compose com o agente Python, Redis para sessões e Whisper ASR. |
| 4.4 | **Testes automatizados** | Testes unitários para `nlp.classificar()`, `identificar_usuario()`, `processar_criacao_os()`. |
| 4.5 | **Retry com backoff** | Implementar retry com exponential backoff para chamadas Evolution API e MapOS API. |
| 4.6 | **Pooling de conexão otimizado** | Ajustar `pool_size` e `max_overflow` do SQLAlchemy baseado na carga real. |
| 4.7 | **Logs estruturados** | Trocar `logging` por `structlog` com correlation ID para rastrear conversas. |

### Detalhes Técnicos — Fase 4

**4.1 — Métricas Prometheus:**
```python
from prometheus_client import Counter, Histogram

MESSAGES_PROCESSED = Counter('whatsapp_messages_total', 'Messages processed', ['command', 'status'])
LLM_LATENCY = Histogram('whatsapp_llm_latency_seconds', 'LLM classification latency')

@app.middleware("http")
async def metrics_middleware(request, call_next):
    start = time.time()
    response = await call_next(request)
    duration = time.time() - start
    REQUEST_LATENCY.observe(duration)
    return response
```

**4.2 — Cache de queries:**
```python
from functools import lru_cache
import time

_cache = {}
_CACHE_TTL = 300  # 5 minutos

def cached_query(sql, params=None, ttl=300):
    key = f"{sql}:{sorted(params.items()) if params else ''}"
    now = time.time()
    if key in _cache and now - _cache[key]['ts'] < ttl:
        return _cache[key]['data']
    data = execute_query(sql, params)
    _cache[key] = {'data': data, 'ts': now}
    return data
```

**4.4 — Testes automatizados:**
```python
# tests/test_nlp.py
def test_classificar_status_os():
    cmd, params = nlp.classificar("como está minha os?")
    assert cmd == 'status_os'

def test_classificar_criar_os_com_entidades():
    cmd, params = nlp.classificar("criar os para cliente João, defeito não liga")
    assert cmd == 'criar_os'
    assert params.get('cliente_nome') == 'João'
    assert params.get('defeito') == 'nao liga'

def test_identificar_admin():
    user = identificar_usuario('5592992150107')
    assert user is not None
    assert user['tipo'] == 'admin'
    assert user['permissoes_id'] == 1
```

---

## Cronograma Visual

```
Semana 1        Semana 2        Semana 3        Semana 4
─────────────────────────────────────────────────────────────
Fase 1          Fase 2          Fase 3          Fase 4
Correções      Segurança       Funcionalidades Otimização
Críticas        e Robustez      Novas            e Escala

■■■■■■■■■■    ■■■■■■■■■■      ■■■■■■■■■■      ■■■■■■■■■■
1.1 API Key    2.1 CORS        3.1 Sessões     4.1 Métricas
1.2 Sessões    2.2 Rate Limit  3.2 Webhook     4.2 Cache
1.3 Admin env  2.3 JWT Refresh 3.3 Check-in    4.3 Docker
1.4 Token env  2.4 API Header  3.4 Agendados   4.4 Testes
1.5 MD5→bcrypt 2.5 OS via API  3.5 Pesquisa    4.5 Retry
               2.6 User real   3.6 Anthropic    4.6 Pool
               2.7 Migration   3.7 Notificação
               2.8 Crypto req  3.8 Dashboard
               2.9 PDF cleanup
```

---

## Arquitetura Proposta — Pós-Implementação

```
┌──────────────┐     ┌──────────────────┐     ┌─────────────┐
│  WhatsApp    │────▶│  Evolution Go     │────▶│  FastAPI     │
│  Usuário     │◀────│  (Bridge API)     │◀────│  Agente IA   │
└──────────────┘     └──────────────────┘     │  (Python)    │
                                                │             │
                                                │ ┌─────────┐ │
                                                │ │ NLP/LLM  │ │
                                                │ │ Regex +  │ │
                                                │ │ GLM-5    │ │
                                                │ └─────────┘ │
                                                │             │
                                                │ ┌─────────┐ │
                                                │ │Sessões   │ │
                                                │ │Redis     │ │
                                                │ └─────────┘ │
                                                └──────┬──────┘
                                                       │
                                           ┌───────────┼───────────┐
                                           │           │           │
                                    ┌──────▼──┐ ┌─────▼────┐ ┌───▼────┐
                                    │  MySQL  │ │ MapOS    │ │ Whisper│
                                    │ (King)  │ │ API v2   │ │ ASR    │
                                    └─────────┘ └──────────┘ └────────┘
```

---

## Métricas de Sucesso

| Métrica | Meta | Como Medir |
|---------|------|------------|
| Tempo médio de resposta | < 3s (texto) / < 8s (áudio) | Logs do agente |
| Taxa de classificação correta | > 90% | Comparar `intencao_detectada` com intenção real |
| Sessões ativas perdidas | 0 (após Fase 3.1) | Monitoramento de restart |
| PDFs gerados com sucesso | > 95% | Logs do RelatoriosController |
| Criação de OS via WhatsApp | > 80% sem erro | Taxa de sucesso em `_sessoes_os` |
| Uptime do agente | > 99.5% | Health check endpoint |

---

## Dependências Externas a Verificar

| Item | Status | Ação |
|------|--------|------|
| MySQL KingHost (`mysql30-farm10.kinghost.net`) | Configurado | Testar conectividade e permissões |
| Evolution Go API | Configurado | Verificar se instância `Mapos` está ativa |
| Whisper ASR | Configurado | Verificar se `WHISPER_URL` responde |
| mPDF no MapOS | Instalado (vendor) | Verificar se `Mpdf\Mpdf` está disponível |
| `cryptography` pip | **Faltando** | Adicionar ao `requirements.txt` |
| Redis (para Fase 3.1) | Não instalado | Instalar e configurar quando chegar a Fase 3 |

---

## Notas de Implementação

- Cada fase deve ser implementada e testada **antes** de passar para a próxima
- As correções da Fase 1 são bloqueantes — devem ser deployadas imediatamente
- A Fase 2 deve passar por review de segurança antes de deploy
- As funcionalidades da Fase 3 dependem da Fase 2 estar completa (especialmente 2.5 e 2.7)
- A Fase 4 é incremental e pode ser feita em paralelo com manutenção diária