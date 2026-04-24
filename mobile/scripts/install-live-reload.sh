#!/bin/bash
# Script de Instalação - Live Reload para MAPOS Mobile
# Linux/Mac Script

echo ""
echo "=========================================="
echo "Instalando Live Reload para MAPOS Mobile"
echo "=========================================="
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Verificar se Node.js está instalado
echo -e "${YELLOW}Verificando Node.js...${NC}"
if ! command -v node &> /dev/null; then
    echo -e "${RED}ERRO: Node.js não está instalado!${NC}"
    echo "Por favor, instale o Node.js em: https://nodejs.org/"
    exit 1
fi

NODE_VERSION=$(node --version)
echo -e "${GREEN}Node.js encontrado: $NODE_VERSION${NC}"
echo ""

# Verificar se npm está instalado
if ! command -v npm &> /dev/null; then
    echo -e "${RED}ERRO: npm não está instalado!${NC}"
    exit 1
fi

# Criar package.json se não existir
if [ ! -f "../package.json" ]; then
    echo -e "${YELLOW}Criando package.json...${NC}"
    cat > ../package.json << 'EOF'
{
  "name": "mapos-live-reload",
  "version": "1.0.0",
  "description": "Live reload para desenvolvimento MAPOS",
  "scripts": {
    "dev": "concurrently \"npm:watch:php\" \"npm:watch:assets\"",
    "watch:php": "browser-sync start --proxy 'http://localhost/MaposV5' --files '../application/**/*.php' --port 3000",
    "watch:assets": "browser-sync start --files '../assets/css/*.css, ../assets/js/*.js' --port 3001"
  },
  "devDependencies": {
    "browser-sync": "^2.29.3",
    "concurrently": "^8.2.2"
  }
}
EOF
    echo -e "${GREEN}package.json criado!${NC}"
fi

echo ""
echo -e "${YELLOW}Instalando dependências...${NC}"
echo "Isso pode levar alguns minutos..."
echo ""

# Instalar dependências
cd ..
npm install

if [ $? -eq 0 ]; then
    echo ""
    echo "=========================================="
    echo -e "${GREEN}Instalação concluída com sucesso!${NC}"
    echo "=========================================="
    echo ""
    echo -e "${CYAN}Comandos disponíveis:${NC}"
    echo "  npm run dev          - Inicia live reload PHP + Assets"
    echo "  npm run watch:php    - Inicia apenas live reload PHP"
    echo "  npm run watch:assets - Inicia apenas live reload CSS/JS"
    echo ""
    echo -e "${YELLOW}Acesse no navegador: http://localhost:3000${NC}"
    echo ""
    echo "Dica: Instale a extensão 'Live Server' para VSCode:"
    echo "  Nome: Live Server (ritwickdey)"
    echo ""
else
    echo ""
    echo -e "${RED}ERRO: Falha na instalação das dependências!${NC}"
    echo -e "${YELLOW}Tente executar manualmente: npm install${NC}"
fi

echo ""
read -p "Pressione ENTER para sair..."
