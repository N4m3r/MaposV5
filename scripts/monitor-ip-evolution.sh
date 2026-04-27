#!/bin/bash
# monitor-ip-evolution.sh
# Script para monitorar mudanças de IP publico do servidor local
# e atualizar automaticamente a URL da Evolution API no MapOS.
#
# Instalacao:
#   1. Copie para /usr/local/bin/monitor-ip-evolution.sh
#   2. chmod +x /usr/local/bin/monitor-ip-evolution.sh
#   3. crontab -e -> */5 * * * * /usr/local/bin/monitor-ip-evolution.sh >> /var/log/monitor-ip-evolution.log 2>&1
#
# Configuracao:
#   Edite as variaveis abaixo conforme sua infraestrutura.

# -------------------------------
# CONFIGURACOES OBRIGATORIAS
# -------------------------------

# URL base do MapOS (com ou sem SSL)
# Exemplo: "https://mapos.exemplo.com.br" ou "https://mapos.exemplo.com.br/MaposV5"
MAPOSS_URL="https://SEU_MAPOS.com.br"

# Token de seguranca configurado no MapOS (tabela configuracoes ou .env)
IP_TOKEN="seu-token-seguro-aqui-aleatorio"

# Porta onde a Evolution API esta rodando no servidor local
EVOLUTION_PORT="8080"

# Caminho do arquivo que armazena o IP anterior (para comparar)
IP_FILE="/var/tmp/evolution_ip_atual"

# Servicos para consultar IP publico (tentara em ordem)
IP_SERVICES=(
    "https://api.ipify.org"
    "https://ifconfig.me/ip"
    "https://ipecho.net/plain"
    "https://icanhazip.com"
    "https://checkip.amazonaws.com"
)

# Timeout para requisicoes (segundos)
CURL_TIMEOUT=15

# -------------------------------
# FUNCOES
# -------------------------------

log_msg() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

obter_ip_publico() {
    for service in "${IP_SERVICES[@]}"; do
        local ip
        ip=$(curl -s --max-time "$CURL_TIMEOUT" "$service" 2>/dev/null)
        # Valida formato basico de IPv4
        if [[ "$ip" =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
            echo "$ip"
            return 0
        fi
    done
    return 1
}

enviar_para_mapos() {
    local ip="$1"
    local url="${MAPOSS_URL}/index.php/api/v2/evolution/atualizar-ip"
    local nova_url="http://${ip}:${EVOLUTION_PORT}"

    local response
    response=$(curl -s --max-time 30 -X POST \
        -H "Content-Type: application/json" \
        -H "X-IP-Token: ${IP_TOKEN}" \
        -d "{\"evolution_url\":\"${nova_url}\",\"ip\":\"${ip}\"}" \
        "$url" 2>/dev/null)

    echo "$response"
}

# -------------------------------
# EXECUCAO PRINCIPAL
# -------------------------------

# Verifica dependencias
if ! command -v curl &> /dev/null; then
    log_msg "ERRO: curl nao esta instalado. Instale com: apt install curl"
    exit 1
fi

# Obtem IP publico atual
IP_ATUAL=$(obter_ip_publico)

if [ -z "$IP_ATUAL" ]; then
    log_msg "ERRO: Nao foi possivel obter o IP publico. Verifique a conexao com a internet."
    exit 1
fi

# Verifica IP anterior
if [ -f "$IP_FILE" ]; then
    IP_ANTERIOR=$(cat "$IP_FILE" 2>/dev/null)
else
    IP_ANTERIOR=""
fi

# Se nao mudou, apenas loga e sai
if [ "$IP_ATUAL" == "$IP_ANTERIOR" ]; then
    log_msg "INFO: IP sem alteracoes (${IP_ATUAL})"
    exit 0
fi

# IP mudou - envia para o MapOS
log_msg "AVISO: IP mudou! ${IP_ANTERIOR:-N/A} -> ${IP_ATUAL}"

RESPONSE=$(enviar_para_mapos "$IP_ATUAL")

# Verifica resposta
if echo "$RESPONSE" | grep -q '"success".*true'; then
    echo "$IP_ATUAL" > "$IP_FILE"
    log_msg "SUCESSO: MapOS atualizado com a nova URL: http://${IP_ATUAL}:${EVOLUTION_PORT}"
    log_msg "RESPOSTA: $RESPONSE"
    exit 0
else
    log_msg "ERRO: Falha ao atualizar MapOS."
    log_msg "RESPOSTA: $RESPONSE"
    exit 1
fi
