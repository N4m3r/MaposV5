#!/bin/bash
APIKEY="7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2"
BASE="https://evo.jj-ferreiras.com.br"

echo "===== VARIACOES DE PATH BASE ====="
for path in /instance/all /api/instance/all /v1/instance/all /v2/instance/all /evo/instance/all /whatsapp/instance/all /bot/instance/all /api/v1/instance/all /api/v2/instance/all /swagger/doc.json /swagger/v1/swagger.json /api/swagger.json /doc.json /docs; do
  code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "apikey:$APIKEY" "$BASE$path")
  echo "$path -> HTTP $code"
done

echo ""
echo "===== VARIACOES DE AUTH (usando /api/instance/all) ====="
for header in \
  "apikey:$APIKEY" \
  "Apikey:$APIKEY" \
  "APIKEY:$APIKEY" \
  "x-api-key:$APIKEY" \
  "X-Api-Key:$APIKEY" \
  "Authorization:Bearer $APIKEY" \
  "Authorization:Apikey $APIKEY" \
  "token:$APIKEY"; do
  code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "$header" "$BASE/api/instance/all")
  echo "Header: $header -> HTTP $code"
done

echo ""
echo "===== TESTES ESPECIFICOS EVOLUTION GO ====="
code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "apikey:$APIKEY" "$BASE/instance/get/Mapos")
echo "GET /instance/get/Mapos -> HTTP $code"

code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "apikey:$APIKEY" "$BASE/instance/status")
echo "GET /instance/status -> HTTP $code"

code=$(curl -k -s -o /dev/null -w "%{http_code}" -X POST -H "apikey:$APIKEY" -H "Content-Type: application/json" -d '{"instanceName":"Mapos"}' "$BASE/instance/connect")
echo "POST /instance/connect -> HTTP $code"

code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "apikey:$APIKEY" "$BASE/instance/qr?instanceId=Mapos")
echo "GET /instance/qr?instanceId=Mapos -> HTTP $code"

code=$(curl -k -s -o /dev/null -w "%{http_code}" -X POST -H "apikey:$APIKEY" -H "Content-Type: application/json" -d '{"number":"5511999999999","text":"Teste"}' "$BASE/send/text")
echo "POST /send/text -> HTTP $code"

code=$(curl -k -s -o /dev/null -w "%{http_code}" -X POST -H "apikey:$APIKEY" -H "Content-Type: application/json" -d '{"number":"5511999999999","text":"Teste"}' "$BASE/message/sendText/Mapos")
echo "POST /message/sendText/Mapos -> HTTP $code"

echo ""
echo "===== TESTES COM INSTANCE TOKEN ====="
TOKEN="9e907a2a-1b06-4812-badb-02e5205df9f7"
code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "apikey:$TOKEN" "$BASE/instance/connectionState/Mapos")
echo "GET /instance/connectionState/Mapos (instance token) -> HTTP $code"

code=$(curl -k -s -o /dev/null -w "%{http_code}" -H "apikey:$TOKEN" "$BASE/instance/get/Mapos")
echo "GET /instance/get/Mapos (instance token) -> HTTP $code"

echo ""
echo "===== VERIFICAR REDIRECTS ====="
curl -k -s -I -H "apikey:$APIKEY" "$BASE/instance/all" | grep -i "location\|HTTP/"
curl -k -s -I -H "apikey:$APIKEY" "$BASE/api/instance/all" | grep -i "location\|HTTP/"
