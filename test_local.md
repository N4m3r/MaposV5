# Teste Interno - Evolution API Endpoints

## Situacao
- Servidor Evolution externo: `https://evo.jj-ferreiras.com.br`
- Porta interna do Evolution: `8091`
- Porta externa (CloudFlare): `4000`
- Todos os endpoints testados retornam 404 via HTTPS

## Hipotese
O nginx ou CloudFlare pode estar bloqueando/redirecionando os endpoints. Vamos testar **diretamente na porta interna 8091** via localhost, bypassando nginx e CloudFlare.

## Script de Teste Interno

Rode no SSH do servidor onde o Evolution esta instalado:

```bash
#!/bin/bash
APIKEY="7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2"
BASE="http://127.0.0.1:8091"

echo "===== TESTE DIRETO NA PORTA 8091 (bypass nginx/CloudFlare) ====="
echo ""

echo "--- Teste 1: GET /instance/all ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/instance/all"

echo "--- Teste 2: GET /api/instance/all ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/api/instance/all"

echo "--- Teste 3: GET /v1/instance/all ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/v1/instance/all"

echo "--- Teste 4: GET /v2/instance/all ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/v2/instance/all"

echo "--- Teste 5: GET / ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" "$BASE/"

echo "--- Teste 6: GET /swagger/doc.json ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" "$BASE/swagger/doc.json"

echo "--- Teste 7: GET /swagger/index.html ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" "$BASE/swagger/index.html"

echo "--- Teste 8: GET /instance/get/Mapos ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/instance/get/Mapos"

echo "--- Teste 9: GET /instance/status ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/instance/status"

echo "--- Teste 10: POST /instance/connect ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -X POST -H "apikey:$APIKEY" -H "Content-Type: application/json" "$BASE/instance/connect" -d '{"instanceName":"Mapos"}'

echo "--- Teste 11: GET /instance/qr?instanceId=Mapos ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -H "apikey:$APIKEY" "$BASE/instance/qr?instanceId=Mapos"

echo "--- Teste 12: POST /send/text ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -X POST -H "apikey:$APIKEY" -H "Content-Type: application/json" "$BASE/send/text" -d '{"number":"5511999999999","text":"Teste"}'

echo "--- Teste 13: POST /message/sendText/Mapos ---"
curl -s -o /dev/null -w "HTTP %{http_code}\n" -X POST -H "apikey:$APIKEY" -H "Content-Type: application/json" "$BASE/message/sendText/Mapos" -d '{"number":"5511999999999","text":"Teste"}'

echo ""
echo "===== SE NENHUM FUNCIONAR, TESTAR NA RAIZ COM NETCAT ====="
echo "Rode: echo -e 'GET / HTTP/1.1\\r\\nHost: 127.0.0.1:8091\\r\\n\\r\\n' | nc 127.0.0.1 8091"
```

## Como Executar

1. Acesse o servidor via SSH (onde o Evolution esta rodando)
2. Salve o script acima como `test_interno.sh`
3. Execute:
```bash
chmod +x test_interno.sh
./test_interno.sh
```

## O que esperar

- Se algum endpoint retornar **HTTP 200** na porta 8091: o problema e o nginx/CloudFlare. Precisamos ajustar o proxy_pass ou whitelist.
- Se TODOS retornarem 404 na porta 8091: o Evolution GO nao esta expondo a API corretamente. Precisa reiniciar o servico ou verificar a configuracao.
- Se a porta 8091 nao responder: o Evolution nao esta rodando ou esta em outra porta.

## Proximos Passos

Me envie o resultado dos testes acima para continuarmos.
