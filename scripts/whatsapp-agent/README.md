# Agente IA WhatsApp - MapOS

Agente de inteligencia artificial para atendimento via WhatsApp integrado ao MapOS.

## Funcionalidades (MVP)

| Comando | Quem usa | Descricao |
|---------|----------|-----------|
| `status da minha os` | Cliente | Lista ordens de servico do cliente |
| `quanto devo` | Cliente | Valor em aberto |
| `minhas os de hoje` | Tecnico | OS atribuidas ao tecnico |
| `relatorio de os` | Admin | Resumo do dia |
| `os atrasadas` | Admin | Servicos em atraso |
| `vendas pendentes` | Admin | Vendas nao faturadas |
| `cobrancas vencidas` | Admin | Cobrancas atrasadas |
| `total os abertas` | Admin | Quantidade em aberto |
| `ajuda` | Todos | Menu de comandos |

## Instalacao Rapida

```bash
# 1. Copie a pasta para o servidor Linux
cp -r scripts/whatsapp-agent /opt/
cd /opt/whatsapp-agent

# 2. Execute o instalador
chmod +x install.sh
sudo ./install.sh

# 3. Edite o .env
sudo nano /opt/whatsapp-agent/.env

# 4. Inicie
sudo systemctl start whatsapp-agent

# 5. Teste
curl http://localhost:8000/health
```

## Estrutura

```
whatsapp-agent/
|-- main.py              # FastAPI + webhook
|-- config.py            # Variaveis de ambiente
|-- database.py          # Conexao MySQL
|-- requirements.txt    # Dependencias
|-- .env.example        # Modelo de configuracao
|-- services/
|   |-- __init__.py
|   |-- evolution_api.py   # Envia mensagens (Evolution Go)
|   |-- mapos_queries.py     # Consultas SQL no MapOS
|   |-- nlp.py              # Classificacao de comandos
```

## Configuracao do Webhook no Evolution Go

1. Acesse o painel do Evolution Go
2. Va em Configuracoes > Webhooks
3. Adicione webhook:
   - URL: `http://SEU_IP:8000/webhook/evolution`
   - Metodo: `POST`
   - Headers: `apikey: SUA_CHAVE_DO_AGENTE`
4. Ative evento: `messages.upsert`

## Comandos Uteis

```bash
# Ver logs
sudo journalctl -u whatsapp-agent -f

# Reiniciar
sudo systemctl restart whatsapp-agent

# Status
sudo systemctl status whatsapp-agent

# Testar webhook manualmente
curl -X POST http://localhost:8000/webhook/evolution \
  -H "Content-Type: application/json" \
  -H "x-api-key: SUA_CHAVE" \
  -d '{
    "data": {
      "key": {"remoteJid": "559292150107@s.whatsapp.net"},
      "message": {"conversation": "status da minha os"}
    }
  }'
```

## Seguranca

- API Key obrigatoria em todos os endpoints
- Webhook so aceita requests com header correto
- Numero do bot e ignorado (evita loop)
- Numeros nao cadastrados recebem mensagem padrao
- Logs de todas as interacoes no banco

## Custo

**Zero.** O agente usa:
- Regex para classificacao (sem LLM)
- Templates para respostas (sem geracao de texto)
- Consultas SQL diretas no banco (sem API externa)
- Apenas o Evolution Go para enviar mensagens

## Proximas Etapas

1. [ ] Criar OS por WhatsApp
2. [ ] Enviar PDF de OS
3. [ ] Transcricao de audio (Whisper)
4. [ ] Check-in/check-out de tecnicos
5. [ ] Menu interativo com botoes
