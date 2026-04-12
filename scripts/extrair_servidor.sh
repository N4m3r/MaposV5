#!/bin/bash
# ==========================================
# Script para extrair o MAPOS no servidor
# Execute apos enviar o arquivo .tar.gz
# ==========================================

# Cores
VERDE='\033[0;32m'
AMARELO='\033[1;33m'
VERMELHO='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${VERDE}==========================================${NC}"
echo -e "${VERDE}   EXTRAIR MAPOS V5 NO SERVIDOR${NC}"
echo -e "${VERDE}==========================================${NC}"
echo ""

# Configurações
PASTA_WEB="/home/jj-ferreiras/www"
PASTA_MAPOS="mapos3"
ARQUIVO=$(ls -t ${PASTA_WEB}/mapos3_*.tar.gz 2>/dev/null | head -1)

if [ -z "$ARQUIVO" ]; then
    # Tentar outro nome
    ARQUIVO=$(ls -t ${PASTA_WEB}/mapos3.tar.gz 2>/dev/null | head -1)
fi

if [ -z "$ARQUIVO" ]; then
    echo -e "${VERMELHO}ERRO: Arquivo .tar.gz não encontrado em ${PASTA_WEB}${NC}"
    echo -e "${AMARELO}Verifique se o upload foi concluído.${NC}"
    exit 1
fi

echo -e "Arquivo encontrado: ${AMARELO}$(basename $ARQUIVO)${NC}"
echo ""

# [1/5] Backup
echo -e "${VERDE}[1/5]${NC} Criando backup do sistema atual..."
if [ -d "${PASTA_WEB}/${PASTA_MAPOS}" ]; then
    cd ${PASTA_WEB}
    BACKUP_NOME="mapos3_backup_$(date +%Y%m%d_%H%M%S).tar.gz"
    tar -czf ${BACKUP_NOME} ${PASTA_MAPOS}/ --exclude='*.log' --exclude='application/logs/*' 2>/dev/null
    echo -e "      Backup criado: ${AMARELO}${BACKUP_NOME}${NC}"
fi

# [2/5] Parar Apache (opcional)
echo -e "${VERDE}[2/5]${NC} Verificando serviços..."
# sudo systemctl stop apache2 2>/dev/null || true

# [3/5] Extrair
echo -e "${VERDE}[3/5]${NC} Extraindo arquivos..."
cd ${PASTA_WEB}

# Se existe pasta, remover
if [ -d "${PASTA_MAPOS}" ]; then
    echo -e "      ${AMARELO}Removendo instalação antiga...${NC}"
    rm -rf ${PASTA_MAPOS}
fi

# Extrair
tar -xzf ${ARQUIVO}

if [ $? -ne 0 ]; then
    echo -e "${VERMELHO}ERRO: Falha ao extrair arquivo${NC}"
    exit 1
fi

echo -e "      ${VERDE}Extraído com sucesso!${NC}"

# [4/5] Permissões
echo -e "${VERDE}[4/5]${NC} Configurando permissões..."
cd ${PASTA_WEB}/${PASTA_MAPOS}

# Permissões corretas
chmod 755 .
chmod -R 777 application/logs/ 2>/dev/null
chmod -R 777 application/cache/ 2>/dev/null
chmod -R 777 assets/uploads/ 2>/dev/null
chmod -R 777 updates/ 2>/dev/null

# [5/5] Limpar
echo -e "${VERDE}[5/5]${NC} Limpando..."
rm -f ${ARQUIVO}

# Reiniciar Apache (opcional)
# sudo systemctl start apache2 2>/dev/null || true

echo ""
echo -e "${VERDE}==========================================${NC}"
echo -e "${VERDE}   EXTRAÇÃO CONCLUÍDA!${NC}"
echo -e "${VERDE}==========================================${NC}"
echo ""
echo -e "Acesse: ${AMARELO}https://jj-ferreiras.com.br/mapos3${NC}"
echo ""
echo -e "${AMARELO}Lembretes:${NC}"
echo "  - Verifique se o banco de dados está atualizado"
echo "  - Execute: php application/database/migrations/run_migrations.php"
echo "  - Verifique as permissões das pastas"
echo ""
