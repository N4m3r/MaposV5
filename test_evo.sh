#!/bin/bash
echo "=== Teste 1: GET /instance/all ==="
curl -k -s -o /dev/null -w "%{http_code}\n" -H "apikey:7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2" https://evo.jj-ferreiras.com.br/instance/all

echo "=== Teste 2: GET /api/instance/all ==="
curl -k -s -o /dev/null -w "%{http_code}\n" -H "apikey:7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2" https://evo.jj-ferreiras.com.br/api/instance/all

echo "=== Teste 3: GET /v1/instance/all ==="
curl -k -s -o /dev/null -w "%{http_code}\n" -H "apikey:7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2" https://evo.jj-ferreiras.com.br/v1/instance/all

echo "=== Teste 4: GET /instance/get/Mapos ==="
curl -k -s -o /dev/null -w "%{http_code}\n" -H "apikey:7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2" https://evo.jj-ferreiras.com.br/instance/get/Mapos

echo "=== Teste 5: GET /instance/status ==="
curl -k -s -o /dev/null -w "%{http_code}\n" -H "apikey:7bd8a76492e92f7e0e4bad14d42eeb0e889e2cfdcd7c8f0ce9b4e1e6607935e2" https://evo.jj-ferreiras.com.br/instance/status

echo "=== Teste 6: GET /manager ==="
curl -k -s -o /dev/null -w "%{http_code}\n" https://evo.jj-ferreiras.com.br/manager

echo "=== Teste 7: GET / ==="
curl -k -s -o /dev/null -w "%{http_code}\n" https://evo.jj-ferreiras.com.br/

echo "=== Teste 8: GET /swagger/doc.json ==="
curl -k -s -o /dev/null -w "%{http_code}\n" https://evo.jj-ferreiras.com.br/swagger/doc.json
