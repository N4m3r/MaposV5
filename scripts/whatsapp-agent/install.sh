#!/bin/bash
# Script de instalacao do Agente IA WhatsApp para MapOS
# Executar no servidor Linux

set -e

AGENT_DIR="/opt/whatsapp-agent"
SERVICE_NAME="whatsapp-agent"

echo "========================================"
echo " Instalador Agente IA WhatsApp"
echo "========================================"

# 1. Verificar dependencias
echo "[1/7] Verificando dependencias..."
if ! command -v python3 &> /dev/null; then
    echo "Python3 nao encontrado. Instalando..."
    sudo apt update && sudo apt install -y python3 python3-pip python3-venv
fi

if ! command -v pip3 &> /dev/null; then
    echo "pip3 nao encontrado. Instalando..."
    sudo apt install -y python3-pip
fi

# 2. Criar diretorio
echo "[2/7] Criando diretorio do agente..."
sudo mkdir -p "$AGENT_DIR"
sudo cp -r . "$AGENT_DIR/"

# 3. Criar ambiente virtual
echo "[3/7] Criando ambiente virtual..."
cd "$AGENT_DIR"
python3 -m venv venv
source venv/bin/activate

# 4. Instalar dependencias
echo "[4/7] Instalando dependencias Python..."
pip install --upgrade pip
pip install -r requirements.txt

# 5. Configurar .env
echo "[5/7] Configurando .env..."
if [ ! -f "$AGENT_DIR/.env" ]; then
    cp "$AGENT_DIR/.env.example" "$AGENT_DIR/.env"
    echo "Arquivo .env criado a partir do exemplo."
    echo "IMPORTANTE: Edite $AGENT_DIR/.env e preencha suas credenciais!"
fi

# 6. Criar servico systemd
echo "[6/7] Criando servico systemd..."
sudo tee /etc/systemd/system/${SERVICE_NAME}.service > /dev/null <<EOF
[Unit]
Description=Agente IA WhatsApp - MapOS
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=$AGENT_DIR
Environment=PYTHONPATH=$AGENT_DIR
ExecStart=$AGENT_DIR/venv/bin/uvicorn main:app --host 0.0.0.0 --port 8000
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable $SERVICE_NAME

# 7. Firewall
echo "[7/7] Liberando porta 8000 no firewall..."
if command -v ufw &> /dev/null; then
    sudo ufw allow 8000/tcp || true
fi
if command -v firewall-cmd &> /dev/null; then
    sudo firewall-cmd --permanent --add-port=8000/tcp || true
    sudo firewall-cmd --reload || true
fi

echo ""
echo "========================================"
echo " Instalacao concluida!"
echo "========================================"
echo ""
echo "Proximos passos:"
echo "1. Edite o arquivo .env:"
echo "   nano $AGENT_DIR/.env"
echo ""
echo "2. Inicie o servico:"
echo "   sudo systemctl start $SERVICE_NAME"
echo ""
echo "3. Verifique o status:"
echo "   sudo systemctl status $SERVICE_NAME"
echo ""
echo "4. Teste o health check:"
echo "   curl http://localhost:8000/health"
echo ""
echo "5. Configure o webhook no Evolution Go:"
echo "   URL: http://SEU_IP:8000/webhook/evolution"
echo "   API Key: (a mesma do .env)"
echo ""
