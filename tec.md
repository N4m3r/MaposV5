# Sistema de Técnicos - Segurança Eletrônica & TI

## Módulo Especializado para Instalação e Manutenção

---

## 1. Visão Geral

Sistema completo para gestão de técnicos especializados em:
- **CFTV** (Câmeras de segurança)
- **Controle de Acesso** (catracas, portões, biometria)
- **Alarmes** (intrusão, incêndio, monitoramento)
- **Redes** (cabeamento estruturado, switches, wifi)
- **Automatização** (automação residencial/comercial)
- **Interfonia** (video porteiro, interfones)
- **Cerca Elétrica & Concertina**

---

## 2. Tipos de Serviço

### 2.1 Instalação (INS)
- Instalação de novos equipamentos
- Configuração inicial
- Treinamento básico ao cliente
- Testes de funcionamento
- Entrega de documentação

### 2.2 Manutenção Preventiva (MP)
- Limpeza de equipamentos
- Verificação de cabos e conexões
- Atualização de firmware
- Testes de funcionamento
- Relatório técnico

### 2.3 Manutenção Corretiva (MC)
- Diagnóstico de falhas
- Substituição de peças
- Reparo de cabeamento
- Configuração/correção de software
- Garantia do serviço

### 2.4 Consultoria Técnica (CT)
- Levantamento de necessidades
- Elaboração de projetos
- Orçamento técnico
- Visita técnica comercial

### 2.5 Treinamento (TR)
- Treinamento operacional
- Treinamento administrativo
- Treinamento avançado
- Entrega de manual

### 2.6 Migração/Upgrade (UP)
- Substituição de equipamentos antigos
- Migração de dados/configurações
- Upgrade de firmware
- Ampliação de sistema

### 2.7 Emergência/Urgente (URG)
- Falha crítica de segurança
- Sistema fora do ar
- Vandalismo/roubo
- Atendimento 24h (plantão)

---

## 3. Especialidades do Técnico

### 3.1 Níveis de Certificação
```
NÍVEL I (Aprendiz)
├── Assistir instalações simples
├── Suporte básico remoto
└── Manutenção preventiva simples

NÍVEL II (Técnico)
├── Instalação completa CFTV
├── Instalação alarmes
├── Configuração redes básicas
├── Manutenção corretiva
└── Suporte técnico nível 2

NÍVEL III (Especialista)
├── Projetos complexos
├── Configuração avançada redes
├── Integração de sistemas
├── Programação de automatização
├── Treinamento de outros técnicos
└── Suporte técnico nível 3

NÍVEL IV (Coordenador/Líder)
├── Gestão de equipe
├── Projetos e consultorias
├── Validação técnica de orçamentos
├── Interface com clientes VIP
└── Aprovação de soluções
```

### 3.2 Especialidades Técnicas
- **CFTV Analógico** (HDCVI, AHD, TVI)
- **CFTV IP** (ONVIF, cameras inteligentes)
- **Gravadores** (DVR, NVR, HVR)
- **Controle de Acesso** (cartão, biometria, facial)
- **Alarmes** (sensores, sirenes, monitoradas)
- **Redes** (cabeamento, wifi corporativo)
- **Fibra Óptica** (fusão, medição OTDR)
- **Automatização** (domótica, IoT)

---

## 4. Sistema de Geolocalização & Navegação

### 4.1 Funcionalidades

#### 4.1.1 Mapa de Clientes
```
[MAPA INTERATIVO - GOOGLE MAPS / LEAFLET]
├── Marcadores de clientes
├── Cores por status:
│   ├── Verde = Instalação OK
│   ├── Amarelo = Manutenção agendada
│   ├── Vermelho = Emergência/Fora do ar
│   └── Cinza = Orçamento/Proposta
├── Filtros por:
│   ├── Técnico responsável
│   ├── Tipo de serviço
│   ├── Data agendada
│   └── Região/bairro
└── Clustering de clientes próximos
```

#### 4.1.2 Roteirização Inteligente (Route Optimization)
```
[OTIMIZADOR DE ROTA - GOOGLE DIRECTIONS API]
Input:
├── Ponto de partida (matriz/última OS)
├── Lista de OS do dia
└── Horários agendados

Output:
├── Sequência otimizada de visitas
├── Tempo estimado em cada cliente
├── Tempo total de deslocamento
├── Combustível economizado
└── Alerta se impossível cumprir
```

**APIs recomendadas:**
- **Google Maps Directions API** - Roteirização completa
- **Google Maps Distance Matrix API** - Matriz de distâncias
- **OSRM (Open Source Routing Machine)** - Gratuito
- **GraphHopper** - Open source com opção paga

#### 4.1.3 Integração com Apps de Navegação

**Opção 1: Abrir App Externo (simples)**
```javascript
// Waze
deepLink = "waze://?q=CLIENTE+ENDEREÇO&navigate=yes";

// Google Maps
window.open("https://www.google.com/maps/dir/?api=1&destination=LAT,LNG");

// Apple Maps (iOS)
window.open("http://maps.apple.com/?daddr=LAT,LNG");
```

**Opção 2: Mapa Embutido com Navegação (avançado)**
```
[MAPA NA APLICAÇÃO]
├── Posição em tempo real do técnico
├── Rota desenhada no mapa
├── Indicação de virar (turn-by-turn)
├── Estimativa de chegada (ETA)
├── Alerta de trânsito/desvio
├── Botão "Cheguei" para iniciar OS
└── Botão "Finalizar" ao concluir
```

**Tecnologias:**
- **Google Maps JavaScript API** (pago por uso)
- **Mapbox GL JS** (opção gratuita generosa)
- **Leaflet.js** (open source) + OpenStreetMap

#### 4.1.4 Check-in/Check-out Geolocalizado
```
[BATIDA DE PONTO GEOLOCALIZADA]
├── Técnico chega no cliente
├── App captura GPS automaticamente
├── Valida se está no raio permitido (100m)
├── Tira selfie com cliente (opcional)
├── Inicia contador de tempo de atendimento
├── Técnico finaliza serviço
├── Captura GPS novamente
├── Marca OS como concluída
└── Calcula tempo gasto no local
```

### 4.2 Campos no Banco de Dados

```sql
-- Tabela: tecnicos (adicionais)
ALTER TABLE tecnicos ADD COLUMN (
    nivel_tecnico ENUM('I','II','III','IV') DEFAULT 'II',
    especialidades JSON, -- ['CFTV', 'Alarmes', 'Redes', 'Automação']
    certificacoes TEXT, -- JSON com certificações
    veiculo_placa VARCHAR(10),
    veiculo_tipo ENUM('Moto','Carro','Nenhum'),
    raio_atuacao_km INT DEFAULT 50,
    plantao_24h TINYINT DEFAULT 0,
    lat_base DECIMAL(10,8), -- Localização matriz
    lng_base DECIMAL(11,8),
    app_rh_instalado TINYINT DEFAULT 0,
    ultima_localizacao_lat DECIMAL(10,8),
    ultima_localizacao_lng DECIMAL(11,8),
    ultima_localizacao_atualizado DATETIME,
    INDEX idx_localizacao (ultima_localizacao_lat, ultima_localizacao_lng)
);

-- Tabela: os_tecnicos (controle de execução)
CREATE TABLE os_tecnicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    os_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    
    -- Check-in
    checkin_lat DECIMAL(10,8),
    checkin_lng DECIMAL(11,8),
    checkin_endereco VARCHAR(255),
    checkin_distancia_metros INT,
    checkin_foto VARCHAR(255),
    checkin_datahora DATETIME,
    
    -- Check-out
    checkout_lat DECIMAL(10,8),
    checkout_lng DECIMAL(11,8),
    checkout_endereco VARCHAR(255),
    checkout_distancia_metros INT,
    checkout_foto VARCHAR(255),
    checkout_datahora DATETIME,
    
    -- Tempo
    tempo_total_minutos INT,
    tempo_deslocamento_minutos INT,
    
    -- Serviço executado
    tipo_servico ENUM('INS','MP','MC','CT','TR','UP','URG'),
    checklist_executado JSON,
    equipamentos_utilizados JSON,
    fotos_antes JSON, -- Array de URLs
    fotos_depois JSON,
    assinatura_cliente VARCHAR(255), -- Imagem base64
    observacoes_tecnico TEXT,
    material_utilizado TEXT,
    
    -- Status
    status_execucao ENUM('Agendada','EmDeslocamento','EmAtendimento','Concluida','Pausada','Cancelada'),
    
    -- Avaliação
    cliente_avaliacao INT, -- 1-5 estrelas
    cliente_comentario TEXT,
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (os_id) REFERENCES os(idOs),
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id)
);

-- Tabela: tecnicos_rotas (histórico de rotas)
CREATE TABLE tecnicos_rotas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tecnico_id INT,
    data DATE,
    
    -- Pontos da rota (JSON com múltiplos pontos)
    pontos_rota JSON, -- [{lat: x, lng: y, timestamp: z, velocidade: v}]
    
    -- Resumo
    km_percorridos DECIMAL(10,2),
    tempo_total TIME,
    os_atendidas INT,
    combustivel_estimado DECIMAL(10,2),
    
    created_at DATETIME,
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id)
);

-- Tabela: os_checklist (checklist por tipo de serviço)
CREATE TABLE os_checklist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_os VARCHAR(20), -- 'CFTV', 'Alarme', 'Rede', etc
    tipo_servico VARCHAR(10), -- 'INS', 'MP', 'MC'
    
    -- Checklist em JSON ou relacionamento
    itens JSON, -- [{"ordem": 1, "descricao": "Verificar cabos", "obrigatorio": true}]
    
    ativo TINYINT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);

-- Tabela: estoque_tecnico (equipamentos no veículo)
CREATE TABLE estoque_tecnico (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tecnico_id INT,
    produto_id INT,
    quantidade INT DEFAULT 0,
    reservado INT DEFAULT 0, -- Para OS agendadas
    localizacao ENUM('Veiculo','Deposito','EmUso'),
    
    last_update DATETIME,
    
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(idProdutos)
);
```

---

## 5. Portal/App do Técnico (Mobile-First)

### 5.1 Fluxo do Dia do Técnico

```
07:00 - [APP] Técnico abre app, visualiza rota do dia
07:15 - [APP] Sai de casa, app inicia tracking de rota
07:30 - [APP] "Iniciar Deslocamento" para 1ª OS
07:45 - [APP] Chega no cliente, "Cheguei" + Check-in GPS + Foto
07:50 - [APP] Acessa OS, vê checklist de CFTV instalação
08:00 - [INÍCIO] Inicia trabalho (câmeras, cabeamento, configuração)
10:30 - [APP] Pausa para café (opcional)
10:45 - [APP] Retorna ao trabalho
12:00 - [APP] "Finalizar" OS + Check-out GPS + Fotos depois
        [APP] Assinatura digital do cliente
        [APP] Preenche material utilizado
        [APP] Indica próximo cliente (rota otimizada)
12:15 - [APP] "Iniciar Deslocamento" para 2ª OS
...
18:00 - [APP] Finaliza última OS
18:05 - [APP] Relatório do dia:
        ├── 5 OS atendidas
        ├── 127 km percorridos
        ├── 8h de trabalho
        ├── 2h de deslocamento
        └── Combustível estimado: R$ 45,00
```

### 5.2 Telas do App

#### 5.2.1 Dashboard do Técnico
```
┌─────────────────────────────────┐
│  🌤️ Bom dia, João!              │
│  Hoje: 5 OS | 3 Concluídas      │
├─────────────────────────────────┤
│                                 │
│  📍 PRÓXIMA OS                  │
│  ─────────────────────────────  │
│  Cliente: Empório São José      │
│  Serviço: Instalação CFTV       │
│  Horário: 14:00 (em 30 min)     │
│  Endereço: Av. Brasil, 1230     │
│                                 │
│  [🗺️ ABRIR NO WAZE]            │
│  [✓ INICIAR DESLOCAMENTO]      │
│                                 │
├─────────────────────────────────┤
│  📋 MINHAS OS DE HOJE           │
│  ─────────────────────────────  │
│  ☑️ 08:00 - Posto Shell (Concluída)
│  ☑️ 10:30 - Mercado Extra (Concluída)
│  ⏳ 14:00 - Empório São José    │
│  ⏳ 16:00 - Residência Ana      │
│  ⏳ 18:00 - Loja TecnoMega      │
│                                 │
├─────────────────────────────────┤
│  [📦 MEU ESTOQUE]              │
│  [📊 MEU DESEMPENHO]            │
│  [⚙️ CONFIGURAÇÕES]            │
└─────────────────────────────────┘
```

#### 5.2.2 Execução da OS
```
┌─────────────────────────────────┐
│  OS #2024-0456                  │
│  CFTV - Instalação              │
├─────────────────────────────────┤
│  CLIENTE                        │
│  Empório São José               │
│  Av. Brasil, 1230 - Centro      │
│  Contato: (11) 98765-4321      │
│                                 │
│  EQUIPAMENTOS PREVISTOS         │
│  ─────────────────────────────  │
│  • 4x Câmera IP 2MP             │
│  • 1x NVR 4 canais              │
│  • 100m Cabo de rede            │
│  • 1x Fonte 12V 10A             │
│                                 │
│  [VER DETALHES COMPLETOS]      │
│                                 │
├─────────────────────────────────┤
│  ⏱️ Tempo de atendimento: 2h15m │
│                                 │
│  [📸 TIRAR FOTO DO CLIENTE]    │
│  [📋 CHECKLIST DE INSTALAÇÃO]  │
│                                 │
│  STATUS: EM ATENDIMENTO         │
│                                 │
│  [✓ CONCLUIR OS]               │
│  [⏸️ PAUSAR (almoço)]          │
│  [⚠️ REPORTAR PROBLEMA]         │
└─────────────────────────────────┘
```

#### 5.2.3 Checklist de Instalação CFTV
```
┌─────────────────────────────────┐
│  CHECKLIST - CFTV INSTALAÇÃO    │
│  OS #2024-0456                  │
├─────────────────────────────────┤
│                                 │
│  ☑️ 1. Verificar equipamentos   │
│     (quantidade e integridade)  │
│     [📷 Foto]                   │
│                                 │
│  ☑️ 2. Definir posição câmeras  │
│     com cliente                 │
│     [📷 Foto local antes]       │
│                                 │
│  ⬜ 3. Instalar suportes        │
│     [📷 Foto]                   │
│                                 │
│  ⬜ 4. Passar cabeamento        │
│     [📷 Foto]                   │
│                                 │
│  ⬜ 5. Conectar equipamentos    │
│                                 │
│  ⬜ 6. Configurar NVR           │
│                                 │
│  ⬜ 7. Configurar app cliente   │
│                                 │
│  ⬜ 8. Testar gravação          │
│                                 │
│  ⬜ 9. Orientar cliente         │
│                                 │
│  ⬜ 10. Limpar área de trabalho │
│      [📷 Foto final]            │
│                                 │
│  Progresso: 2/10 (20%)          │
│                                 │
│  [💾 SALVAR]  [✓ FINALIZAR]    │
└─────────────────────────────────┘
```

#### 5.2.4 Conclusão da OS
```
┌─────────────────────────────────┐
│  CONCLUIR OS #2024-0456           │
├─────────────────────────────────┤
│                                 │
│  📸 FOTOS OBRIGATÓRIAS          │
│  ─────────────────────────────  │
│  [📷] Antes (obrigatório)       │
│  [📷] Durante (opcional)        │
│  [📷] Depois (obrigatório)      │
│                                 │
│  ✍️ ASSINATURA DO CLIENTE       │
│  ─────────────────────────────  │
│  [ÁREA PARA ASSINAR COM DEDO]   │
│                                 │
│  João da Silva                  │
│  11/04/2024 12:30               │
│                                 │
│  📦 MATERIAL UTILIZADO            │
│  ─────────────────────────────  │
│  Cabo rede: 85m (previsto: 100m)│
│  Conectores: 8 (previsto: 8) ✓  │
│                                 │
│  📝 OBSERVAÇÕES                 │
│  Cliente solicitou orçamento    │
│  para mais 2 câmeras no futuro  │
│                                 │
│  ⭐ AVALIAÇÃO DO SERVIÇO         │
│  Cliente satisfeito? [SIM] [NÃO]
│                                 │
│  [✓ CONFIRMAR CONCLUSÃO]         │
└─────────────────────────────────┘
```

---

## 6. Checklists por Tipo de Serviço

### 6.1 Instalação CFTV
```json
{
  "tipo": "CFTV",
  "servico": "INS",
  "itens": [
    {"ordem": 1, "descricao": "Verificar integridade dos equipamentos", "obrigatorio": true},
    {"ordem": 2, "descricao": "Definir posições das câmeras com cliente", "obrigatorio": true},
    {"ordem": 3, "descricao": "Tirar foto do local antes da instalação", "obrigatorio": true},
    {"ordem": 4, "descricao": "Instalar suportes das câmeras", "obrigatorio": true},
    {"ordem": 5, "descricao": "Passar cabeamento estruturado", "obrigatorio": true},
    {"ordem": 6, "descricao": "Crimpagem/conectorização dos cabos", "obrigatorio": true},
    {"ordem": 7, "descricao": "Instalar câmeras e ajustar ângulo", "obrigatorio": true},
    {"ordem": 8, "descricao": "Instalar e conectar NVR/DVR", "obrigatorio": true},
    {"ordem": 9, "descricao": "Configurar rede do gravador", "obrigatorio": true},
    {"ordem": 10, "descricao": "Configurar resolução e gravação", "obrigatorio": true},
    {"ordem": 11, "descricao": "Configurar app no celular do cliente", "obrigatorio": true},
    {"ordem": 12, "descricao": "Testar visualização remota", "obrigatorio": true},
    {"ordem": 13, "descricao": "Testar playback de gravação", "obrigatorio": true},
    {"ordem": 14, "descricao": "Orientar cliente sobre uso", "obrigatorio": true},
    {"ordem": 15, "descricao": "Entregar manual e senhas", "obrigatorio": true},
    {"ordem": 16, "descricao": "Tirar foto final da instalação", "obrigatorio": true},
    {"ordem": 17, "descricao": "Limpar área de trabalho", "obrigatorio": false},
    {"ordem": 18, "descricao": "Coletar assinatura do cliente", "obrigatorio": true}
  ]
}
```

### 6.2 Manutenção CFTV
```json
{
  "tipo": "CFTV",
  "servico": "MP",
  "itens": [
    {"ordem": 1, "descricao": "Verificar funcionamento de todas as câmeras", "obrigatorio": true},
    {"ordem": 2, "descricao": "Limpar lentes das câmeras", "obrigatorio": true},
    {"ordem": 3, "descricao": "Verificar ângulos e ajustar se necessário", "obrigatorio": false},
    {"ordem": 4, "descricao": "Verificar cabos e conexões", "obrigatorio": true},
    {"ordem": 5, "descricao": "Verificar espaço em disco do gravador", "obrigatorio": true},
    {"ordem": 6, "descricao": "Verificar data/hora do sistema", "obrigatorio": true},
    {"ordem": 7, "descricao": "Testar gravação em todas as câmeras", "obrigatorio": true},
    {"ordem": 8, "descricao": "Testar acesso remoto (app)", "obrigatorio": true},
    {"ordem": 9, "descricao": "Verificar atualizações de firmware", "obrigatorio": false},
    {"ordem": 10, "descricao": "Relatar necessidades ao cliente", "obrigatorio": true},
    {"ordem": 11, "descricao": "Coletar assinatura do cliente", "obrigatorio": true}
  ]
}
```

### 6.3 Instalação Controle de Acesso
```json
{
  "tipo": "CONTROLE_ACESSO",
  "servico": "INS",
  "itens": [
    {"ordem": 1, "descricao": "Verificar equipamentos", "obrigatorio": true},
    {"ordem": 2, "descricao": "Avaliar melhor posição para leitoras", "obrigatorio": true},
    {"ordem": 3, "descricao": "Instalar/controlar furação de suportes", "obrigatorio": true},
    {"ordem": 4, "descricao": "Passar cabeamento elétrico e de dados", "obrigatorio": true},
    {"ordem": 5, "descricao": "Instalar/controlar instalação de eletroímã/fechadura", "obrigatorio": true},
    {"ordem": 6, "descricao": "Instalar botoeira de saída", "obrigatorio": true},
    {"ordem": 7, "descricao": "Instalar leitora biométrica/cartão", "obrigatorio": true},
    {"ordem": 8, "descricao": "Instalar/controlar fonte e nobreak", "obrigatorio": true},
    {"ordem": 9, "descricao": "Configurar controladora", "obrigatorio": true},
    {"ordem": 10, "descricao": "Cadastrar usuários/administradores", "obrigatorio": true},
    {"ordem": 11, "descricao": "Testar todos os sentidos de passagem", "obrigatorio": true},
    {"ordem": 12, "descricao": "Testar botoeira de emergência", "obrigatorio": true},
    {"ordem": 13, "descricao": "Treinar administrador", "obrigatorio": true},
    {"ordem": 14, "descricao": "Coletar assinatura do cliente", "obrigatorio": true}
  ]
}
```

### 6.4 Instalação Rede/Cabeamento
```json
{
  "tipo": "REDE",
  "servico": "INS",
  "itens": [
    {"ordem": 1, "descricao": "Verificar equipamentos e cabos", "obrigatorio": true},
    {"ordem": 2, "descricao": "Mapear pontos de rede necessários", "obrigatorio": true},
    {"ordem": 3, "descricao": "Identificar roteamento de cabos", "obrigatorio": true},
    {"ordem": 4, "descricao": "Passar cabos/rack/eletrodutos", "obrigatorio": true},
    {"ordem": 5, "descricao": "Instalar tomadas RJ45 (testar crimpagem)", "obrigatorio": true},
    {"ordem": 6, "descricao": "Instalar patch panel", "obrigatorio": false},
    {"ordem": 7, "descricao": "Instalar switches", "obrigatorio": true},
    {"ordem": 8, "descricao": "Configurar VLANs se necessário", "obrigatorio": false},
    {"ordem": 9, "descricao": "Testar todos os pontos (tester de rede)", "obrigatorio": true},
    {"ordem": 10, "descricao": "Medir velocidade em pontos críticos", "obrigatorio": true},
    {"ordem": 11, "descricao": "Documentar IPs e senhas", "obrigatorio": true},
    {"ordem": 12, "descricao": "Entregar mapa da rede ao cliente", "obrigatorio": true},
    {"ordem": 13, "descricao": "Coletar assinatura do cliente", "obrigatorio": true}
  ]
}
```

---

## 7. Estoque do Técnico (Veículo)

### 7.1 Controle de Material
```
[ESTOQUE DO TÉCNICO]
├── Baixa no estoque matriz
├── Carregamento no veículo
├── Utilização nas OS
├── Devolução (estragado/não usado)
├── Solicitação de compra/reposição
└── Inventário periódico
```

### 7.2 Equipamentos Padrão no Veículo
```
FERRAMENTAS BÁSICAS:
├── Kit de ferramentas (chaves, alicates, etc)
├── Furadeira/Parafusadeira
├── Escada pequena
├── Testador de cabo de rede
├── Multímetro
├── Lanterna
├── Nível e treina
├── Alicate crimpador
├── Kit de fusão (se trabalha com fibra)
└── Caixa de sobressalentes (conectores, etc)

MATERIAL DE CONSUMO:
├── Cabo de rede (caixa/bobina)
├── Conectores RJ45
├── Conectores de compressão
├── Fita isolante
├── Abraçadeiras
├── Buchas e parafusos
├── Fontes 12V (sobressalente)
├── Extensões e T's
└── Limpa contato/lentes
```

### 7.3 Telas de Controle
```
┌─────────────────────────────────┐
│  📦 MEU ESTOQUE                 │
│  Técnico: João                  │
├─────────────────────────────────┤
│                                 │
│  CATEGORIA: CFTV                │
│  ─────────────────────────────  │
│  Câmera IP 2MP        [03]      │
│  Câmera IP 4MP        [02]      │
│  NVR 4 canais         [01]      │
│  Cabo UTP Cat5e 305m  [01]      │
│                                 │
│  CATEGORIA: CONECTORES          │
│  ─────────────────────────────  │
│  RJ45 Cat5e           [45]      │
│  P4 Macho             [12]      │
│  BNC                  [20]      │
│                                 │
│  [🔄 SOLICITAR REPOSIÇÃO]       │
│  [📋 INVENTÁRIO]                │
└─────────────────────────────────┘
```

---

## 8. Integrações

### 8.1 Com OS (Ordem de Serviço)
- Técnico visualiza OS atribuída
- Atualiza status em tempo real
- Anexa fotos à OS
- Preenche relatório técnico
- Gera material utilizado (para faturamento)

### 8.2 Com Estoque
- Reserva equipamentos para OS agendada
- Baixa automática ao concluir OS
- Alerta de estoque baixo
- Sugestão de compra baseada em histórico

### 8.3 Com Vendas/Orçamento
- Técnico indica oportunidade (upsell)
- Gera orçamento para ampliação
- Comissão sobre venda técnica

### 8.4 Com Financeiro
- Comissão por OS concluída
- Reembolso de combustível (por km)
- Diárias/despesas de viagem
- Adiantamento de material

### 8.5 Com RH
- Ponto integrado às OS
- Horas extras automáticas
- Produtividade por técnico

---

## 9. Relatórios e Indicadores

### 9.1 Dashboard Gerencial
```
PRODUTIVIDADE:
├── OS concluídas no dia/semana/mês
├── Tempo médio por tipo de serviço
├── Taxa de resolução no primeiro atendimento
├── Revisitas (chamados repetidos)
└── Satisfação do cliente (NPS)

EFICIÊNCIA:
├── KM percorridos por técnico
├── Custo de deslocamento
├── Tempo de deslocamento vs atendimento
├── OS por km percorrido
└── Otimização de rotas

FINANCEIRO:
├── Mão de obra faturada
├── Custo de material usado
├── Lucratividade por técnico
├── Comissões pagas
└── Reembolsos

ESTOQUE:
├── Material consumido
├── Perdas/quebras
├── Giro de estoque técnico
└── Itens críticos (baixo)
```

### 9.2 Relatórios Disponíveis
1. **Relatório de OS por Técnico** (PDF/Excel)
2. **Relatório de Material Utilizado**
3. **Relatório de Rotas e KM**
4. **Relatório de Checklists** (conformidade)
5. **Relatório de Satisfação do Cliente**
6. **Relatório de Produtividade**
7. **Relatório de Revisitas**
8. **Relatório de Estoque Técnico**

---

## 10. Estrutura de Arquivos

```
application/
├── controllers/
│   ├── Tecnicos.php              # Controller principal
│   ├── Tec_os.php               # OS específicas técnicos
│   ├── Tec_app.php              # API para app mobile
│   └── Tec_rotas.php            # Rotas e geolocalização
├── models/
│   ├── Tecnicos_model.php
│   ├── Tec_os_model.php
│   ├── Tec_estoque_model.php
│   ├── Tec_checklist_model.php
│   └── Tec_rotas_model.php
├── views/
│   └── tecnicos/
│       ├── dashboard.php
│       ├── os/
│       │   ├── listar.php
│       │   ├── executar.php      # Tela de execução
│       │   └── checklist.php
│       ├── mapa/
│       │   └── clientes_mapa.php
│       ├── estoque/
│       │   ├── meu_estoque.php
│       │   └── solicitar.php
│       └── relatorios/
│           └── produtividade.php
└── database/
    └── migrations/
        ├── 012_create_tecnicos_tables.php
        └── 013_seed_checklists.php
```

---

## 11. APIs Externas

### 11.1 Google Maps Platform
```
APIs Necessárias:
├── Maps JavaScript API          # Mapa no browser
├── Directions API               # Rotas otimizadas
├── Distance Matrix API          # Matriz de distâncias
├── Geocoding API                # Endereço → Coordenadas
├── Places API                   # Sugestão de endereços
└── Street View Static API       # Visualização do local

Custo estimado:
├── Crédito mensal: $200 grátis
├── Uso típico: $50-150/mês
└── Opção gratuita: OpenStreetMap + OSRM
```

### 11.2 Alternativa Open Source
```
MAPA: Leaflet.js + OpenStreetMap
ROTEAMENTO: OSRM (Open Source Routing Machine)
GEOCODING: Nominatim (OpenStreetMap)
CUSTO: Gratuito (com limitações de uso)
```

### 11.3 Waze Deep Linking
```
URL Scheme:
waze://?q=<endereco>&navigate=yes

Exemplo:
waze://?q=Rua+Exemplo+123+Sao+Paulo&navigate=yes

Limitações:
├── Só funciona se app Waze instalado
├── Abre app externo (não embutido)
└── Não retorna dados para sistema
```

---

## 12. Cronograma de Implementação

### Fase 1 - Estrutura Base (2 semanas)
- [ ] Tabelas no banco
- [ ] Cadastro de técnicos com especialidades
- [ ] Checklists configuráveis
- [ ] Vinculação técnico ↔ OS

### Fase 2 - App Básico (2 semanas)
- [ ] Login do técnico
- [ ] Visualizar OS do dia
- [ ] Check-in/check-out básico
- [ ] Checklist digital

### Fase 3 - Geolocalização (2 semanas)
- [ ] Mapa de clientes
- [ ] Roteirização simples
- [ ] Integração Waze/Google Maps
- [ ] Tracking de rota

### Fase 4 - Execução Completa (2 semanas)
- [ ] Fotos antes/depois
- [ ] Assinatura digital
- [ ] Estoque no veículo
- [ ] Material utilizado

### Fase 5 - Otimizações (2 semanas)
- [ ] Roteirização inteligente (TSP)
- [ ] Previsão de tempo
- [ ] Análise de produtividade
- [ ] Relatórios gerenciais

---

## 13. Custos Estimados

### Desenvolvimento
| Item | Tempo | Custo Estimado |
|------|-------|----------------|
| Backend/API | 80h | R$ 8.000 |
| App Mobile/Web | 120h | R$ 12.000 |
| Mapas/Geolocalização | 40h | R$ 4.000 |
| Testes/Ajustes | 40h | R$ 4.000 |
| **Total** | **280h** | **R$ 28.000** |

### APIs e Serviços (mensal)
| Serviço | Custo |
|---------|-------|
| Google Maps API | US$ 50-150 (R$ 250-750) |
| ou OpenStreetMap | Grátis |
| Servidor/Hospedagem | R$ 100-300 |
| Push Notifications | R$ 0-50 |
| **Total mensal** | **R$ 350-1.100** |

---

## 14. Observações

- **Priorizar mobile-first**: Técnicos usam celulares/tablets
- **Offline-first**: Funcionar sem internet no local (sincroniza depois)
- **Performance**: App rápido, sem travamentos
- **UX simples**: Poucos cliques para executar tarefas
- **Fotos comprimidas**: Não lotar servidor/storage
- **Segurança**: Dados de clientes protegidos (LGPD)

---

## 15. Integração com Mapos OS Existente

O sistema de técnicos **integra com as tabelas e estruturas já existentes** no Mapos, aproveitando o cadastro de usuários, clientes e OS.

### 15.1 Mapeamento de Tabelas Existentes

```sql
-- TABELAS EXISTENTES DO MAPOS (REUTILIZAR)

-- os → Usar como base para execução técnica
-- Colunas existentes relevantes:
--   - idOs (PK)
--   - dataInicial, dataFinal
--   - garantia
--   - status (para acompanhamento)
--   - cliente_id → clientes.idClientes
--   - usuarios_id → técnicos vinculados

-- usuarios → Técnicos cadastrados no sistema
-- Colunas a adicionar via migration:
ALTER TABLE usuarios ADD COLUMN (
    is_tecnico TINYINT DEFAULT 0,          -- Indica se é técnico de campo
    nivel_tecnico ENUM('I','II','III','IV') DEFAULT 'II',
    especialidades JSON,                   -- ['CFTV','Alarmes','Redes']
    veiculo_placa VARCHAR(10),
    raio_atuacao_km INT DEFAULT 50,
    plantao_24h TINYINT DEFAULT 0,
    coordenadas_base_lat DECIMAL(10,8),
    coordenadas_base_lng DECIMAL(11,8),
    app_tecnico_instalado TINYINT DEFAULT 0
);

-- produtos → Estoque já existe
-- Usar para baixa de material nas OS

-- clientes → Já tem endereço completo
-- Usar para geolocalização e navegação

-- os_produtos → Relaciona produtos à OS
-- Usar para materiais utilizados na execução

-- vendas → Para integração com vendas técnicas
-- lancamentos → Financeiro (comissões, reembolsos)
```

### 15.2 Novas Tabelas (Extensão do Mapos)

```sql
-- Nova tabela: os_execucao_tecnica
-- Vinculada à OS existente (1:N - uma OS pode ter várias visitas)
CREATE TABLE os_execucao_tecnica (
    id INT PRIMARY KEY AUTO_INCREMENT,
    os_id INT NOT NULL,
    tecnico_id INT NOT NULL,                  -- FK para usuarios.idUsuarios
    
    -- Tipo de serviço técnico
    tipo_servico ENUM('INS','MP','MC','CT','TR','UP','URG') DEFAULT 'MC',
    especialidade VARCHAR(50),              -- CFTV, Alarme, Rede, etc
    
    -- Check-in/Check-out
    checkin_lat DECIMAL(10,8),
    checkin_lng DECIMAL(11,8),
    checkin_endereco VARCHAR(255),
    checkin_foto VARCHAR(255),
    checkin_datahora DATETIME,
    
    checkout_lat DECIMAL(10,8),
    checkout_lng DECIMAL(11,8),
    checkout_endereco VARCHAR(255),
    checkout_foto VARCHAR(255),
    checkout_datahora DATETIME,
    
    -- Tempo e deslocamento
    tempo_atendimento_minutos INT,
    tempo_deslocamento_minutos INT,
    km_deslocamento DECIMAL(10,2),
    
    -- Checklist digital
    checklist JSON,                           -- [{"item": 1, "desc": "...", "ok": true}]
    checklist_completude INT DEFAULT 0,       -- % de conclusão
    
    -- Fotos e evidências
    fotos_antes JSON,                         -- URLs das fotos
    fotos_depois JSON,
    fotos_durante JSON,
    
    -- Material utilizado (integrado com os_produtos)
    material_json JSON,                     -- Detalhamento técnico
    
    -- Execução
    status_execucao ENUM('Agendado','EmDeslocamento','Chegada','EmExecucao','Pausado','Concluido','Cancelado') DEFAULT 'Agendado',
    
    -- Cliente
    assinatura_cliente TEXT,                  -- Base64 da imagem
    nome_responsavel VARCHAR(255),
    avaliacao_cliente INT,                    -- 1-5 estrelas
    observacoes_cliente TEXT,
    
    -- Técnico
    observacoes_tecnico TEXT,
    problema_encontrado TEXT,
    solucao_aplicada TEXT,
    recomendacoes TEXT,
    
    -- Oportunidade de venda
    oportunidade_venda TINYINT DEFAULT 0,
    descricao_oportunidade TEXT,
    orcamento_gerado_id INT,                -- FK vendas.idVendas
    
    -- Controle
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME,
    
    FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(idUsuarios),
    INDEX idx_os_tecnico (os_id, tecnico_id),
    INDEX idx_data_execucao (checkin_datahora)
);

-- Nova tabela: os_checklist_template
-- Templates de checklist por tipo de serviço
CREATE TABLE os_checklist_template (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_os VARCHAR(50) NOT NULL,             -- CFTV, Alarme, Rede, ControleAcesso
    tipo_servico ENUM('INS','MP','MC','CT','TR','UP') DEFAULT 'MC',
    nome_template VARCHAR(100),
    
    -- Itens do checklist em JSON
    itens JSON,                               -- [{"ordem":1, "desc":"...", "obrigatorio":true}]
    
    ativo TINYINT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    
    UNIQUE KEY uk_template (tipo_os, tipo_servico)
);

-- Nova tabela: tecnicos_estoque_veiculo
-- Controle de estoque no veículo do técnico
CREATE TABLE tecnicos_estoque_veiculo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tecnico_id INT NOT NULL,
    produto_id INT NOT NULL,                  -- FK produtos.idProdutos
    quantidade_disponivel INT DEFAULT 0,
    quantidade_reservada INT DEFAULT 0,       -- Para OS agendadas
    localizacao ENUM('Veiculo','EmUso','Retirado') DEFAULT 'Veiculo',
    ultima_movimentacao DATETIME,
    
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(idUsuarios),
    FOREIGN KEY (produto_id) REFERENCES produtos(idProdutos),
    UNIQUE KEY uk_tecnico_produto (tecnico_id, produto_id)
);

-- Nova tabela: tecnicos_rotas_tracking
-- Histórico de rotas percorridas (para auditoria e otimização)
CREATE TABLE tecnicos_rotas_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tecnico_id INT NOT NULL,
    data DATE NOT NULL,
    
    -- Pontos da rota em JSON (compacto)
    pontos_rota JSON,                         -- [{"lat":x, "lng":y, "time":"HH:MM", "vel":0}]
    
    -- Resumo
    km_total DECIMAL(10,2),
    os_atendidas INT,
    tempo_total_horas DECIMAL(5,2),
    combustivel_estimado DECIMAL(10,2),
    
    created_at DATETIME,
    
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(idUsuarios),
    INDEX idx_data_tecnico (tecnico_id, data)
);
```

### 15.3 Hooks e Eventos (Integração)

```php
// Arquivo: application/hooks/Tec_integracao_hook.php

// Evento: OS criada
// Ação: Notificar técnico disponível
// Evento: OS atualizada
// Ação: Atualizar status no app do técnico
// Evento: OS finalizada pelo técnico
// Ação: Atualizar OS no Mapos, enviar notificação

// Exemplo de integração na OS existente
class Tec_integracao {
    
    // Quando técnico conclui execução
    public function on_execucao_concluida($execucao_id) {
        $execucao = $this->get_execucao($execucao_id);
        
        // 1. Atualizar status da OS no Mapos
        $this->db->where('idOs', $execucao->os_id);
        $this->db->update('os', [
            'status' => 'Finalizado',
            'dataFinal' => date('Y-m-d H:i:s')
        ]);
        
        // 2. Baixar estoque usado
        $this->baixar_estoque_os($execucao);
        
        // 3. Notificar cliente (WhatsApp/email)
        $this->notificar_cliente_conclusao($execucao);
        
        // 4. Gerar comissão técnico (se configurado)
        $this->gerar_comissao($execucao);
        
        // 5. Se tem oportunidade de venda, criar alerta comercial
        if ($execucao->oportunidade_venda) {
            $this->criar_oportunidade_venda($execucao);
        }
    }
}
```

### 15.4 Alterações nas Views Existentes

```php
// Adicionar aba "Execução Técnica" na visualização da OS
// Arquivo: application/views/os/visualizar.php (novo trecho)

?>
<div class="tab-pane" id="tabExecucao">
    <h4>Execução Técnica</h4>
    
    <?php if ($execucoes): ?>
    <table class="table">
        <tr>
            <th>Data</th>
            <th>Técnico</th>
            <th>Tipo</th>
            <th>Tempo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($execucoes as $exec): ?>
        <tr>
            <td><?= date('d/m/Y H:i', strtotime($exec->checkin_datahora)) ?></td>
            <td><?= $exec->tecnico_nome ?></td>
            <td><?= $this->tecnicos_model->tipo_servico_label($exec->tipo_servico) ?></td>
            <td><?= $exec->tempo_atendimento_minutos ?> min</td>
            <td><?= $exec->status_execucao ?></td>
            <td>
                <a href="/tec/execucao/<?= $exec->id ?>" class="btn btn-info btn-mini">
                    <i class="fas fa-eye"></i> Detalhes
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <!-- Fotos -->
    <h5>Fotos da Execução</h5>
    <div class="fotos-execucao">
        <?php foreach ($fotos as $foto): ?>
        <img src="<?= base_url($foto) ?>" class="img-thumbnail" style="max-width:200px;">
        <?php endforeach; ?>
    </div>
    
    <!-- Assinatura -->
    <h5>Assinatura do Cliente</h5>
    <img src="<?= $execucao->assinatura_cliente ?>" class="img-polaroid">
    
    <!-- Checklist -->
    <h5>Checklist</h5>
    <div class="progress">
        <div class="bar" style="width: <?= $execucao->checklist_completude ?>%">
            <?= $execucao->checklist_completude ?>%
        </div>
    </div>
    <?php else: ?>
    <p>Nenhuma execução registrada.</p>
    <?php endif; ?>
    
    <a href="/tec/execucao/novo/<?= $os->idOs ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Nova Execução
    </a>
</div>
```

### 15.5 Permissões Integradas ao Mapos

```php
// Adicionar ao sistema de permissões existente
$permissoes_tecnicos = [
    'vTec' => 'Visualizar módulo técnicos',
    'aTecOS' => 'Acessar execuções de OS',
    'eTecOS' => 'Editar execuções',
    'cTecExecucao' => 'Criar execução técnica',
    'aTecMapa' => 'Acessar mapa de técnicos',
    'aTecEstoque' => 'Gerenciar estoque de técnicos',
    'vTecOutros' => 'Ver execuções de outros técnicos',
    'aTecChecklist' => 'Configurar checklists',
    'eTecParametros' => 'Editar parâmetros do sistema',
];
```

---

## 16. Modalidade: Acompanhamento em Obra

Sistema completo para gestão de **obras de grande porte** (instalações prediais, condomínios, empresas) com acompanhamento de equipe técnica, cronograma físico-financeiro e controle de qualidade.

### 16.1 Conceito

Diferente de uma OS simples, uma **Obra** é um conjunto de serviços executados em múltiplas etapas, envolvendo:
- Equipe técnica (múltiplos técnicos simultâneos)
- Cronograma de execução
- Controle de materiais (entrada/saída)
- Medições parciais
- Entregas parciais
- Documentação técnica (ART, memoriais)

### 16.2 Tipos de Obra

```
TIPO A - Condomínio Residencial
├── Instalação CFTV completo
├── Controle de acesso (portarias)
├── Interfonia coletiva
├── Cerca elétrica
├── Automação de portões
└── Central de alarme monitorada

TIPO B - Comércio/Indústria
├── CFTV IP com analytics
├── Controle de acesso (catracas)
├── Sistema de alarme perimetral
├── Detecção de incêndio
├── Infraestrutura de rede
└── Automação comercial

TIPO C - Residência de Alto Padrão
├── CFTV com reconhecimento facial
├── Automação residencial completa
├── Home theater
├── Sistema de som ambiente
├── Fechaduras eletrônicas
└── Portão automatizado

TIPO D - Obra Pública/Corporativa
├── Projeto executivo
├── Licitação/execução
├── Gestão de equipe grande
├── Controle orçamentário
└── Documentação técnica
```

### 16.3 Estrutura de Dados - Obras

```sql
-- Nova tabela: obras (nível acima da OS)
CREATE TABLE obras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identificação
    codigo VARCHAR(50) UNIQUE,               -- OB-2024-001
    nome VARCHAR(255) NOT NULL,            -- Condomínio Solar das Palmeiras
    cliente_id INT NOT NULL,
    
    -- Tipo e escopo
    tipo_obra ENUM('Condominio','Comercio','Residencia','Industrial','Publica') DEFAULT 'Condominio',
    especialidade_principal VARCHAR(50),     -- CFTV, Integrado, etc
    
    -- Endereço completo
    endereco TEXT,
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(9),
    coordenadas_lat DECIMAL(10,8),
    coordenadas_lng DECIMAL(11,8),
    
    -- Contrato
    valor_contrato DECIMAL(15,2),
    valor_material_estimado DECIMAL(15,2),
    prazo_dias INT,
    data_inicio_contrato DATE,
    data_fim_prevista DATE,
    data_fim_real DATE,
    
    -- Cronograma resumido (JSON)
    cronograma_etapas JSON,                  -- [{"etapa":1, "nome":"...", "dias":5, "ordem":1}]
    
    -- Responsáveis
    gestor_obra_id INT,                      -- FK usuarios (nível IV)
    responsavel_tecnico_id INT,              -- Engenheiro/Técnico responsável
    responsavel_comercial_id INT,            -- Quem vendeu
    
    -- Status
    status ENUM('Prospeccao','Orcamentacao','Contratada','EmExecucao','Paralisada','Finalizada','Entregue','Garantia') DEFAULT 'Prospeccao',
    percentual_concluido INT DEFAULT 0,
    
    -- Documentos
    contrato_arquivo VARCHAR(255),
    projeto_arquivo VARCHAR(255),
    art_arquivo VARCHAR(255),                  -- Anotação de Responsabilidade Técnica
    memorial_descritivo TEXT,
    
    -- Gestão
    observacoes TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(idClientes),
    FOREIGN KEY (gestor_obra_id) REFERENCES usuarios(idUsuarios),
    FOREIGN KEY (responsavel_tecnico_id) REFERENCES usuarios(idUsuarios),
    FOREIGN KEY (responsavel_comercial_id) REFERENCES usuarios(idUsuarios)
);

-- Nova tabela: obra_etapas (cronograma detalhado)
CREATE TABLE obra_etapas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    obra_id INT NOT NULL,
    
    -- Etapa
    numero_etapa INT,
    nome VARCHAR(100),                       -- "Passagem de cabos Bloco A"
    descricao TEXT,
    especialidade VARCHAR(50),               -- CFTV, Rede, etc
    
    -- Cronograma
    data_inicio_prevista DATE,
    data_fim_prevista DATE,
    data_inicio_real DATE,
    data_fim_real DATE,
    
    -- Progresso
    percentual_concluido INT DEFAULT 0,
    status ENUM('NaoIniciada','EmAndamento','Concluida','Atrasada','Paralisada') DEFAULT 'NaoIniciada',
    
    -- Orçamento
    mao_de_obra_prevista DECIMAL(10,2),
    material_previsto DECIMAL(10,2),
    total_previsto DECIMAL(10,2),
    
    -- Execução
    mao_de_obra_real DECIMAL(10,2),
    material_real DECIMAL(10,2),
    total_real DECIMAL(10,2),
    
    -- Equipe
    tecnicos_designados JSON,                -- [1, 5, 8] IDs dos técnicos
    
    -- Vínculo com OS existentes
    os_ids JSON,                             -- [101, 102, 103] IDs das OS geradas
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE
);

-- Nova tabela: obra_equipe (escala de técnicos)
CREATE TABLE obra_equipe (
    id INT PRIMARY KEY AUTO_INCREMENT,
    obra_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    
    -- Função na obra
    funcao ENUM('Gestor','Encarregado','Executor','Auxiliar','Estagiario') DEFAULT 'Executor',
    especialidade_obra VARCHAR(50),          -- Pode ser diferente da especialidade padrão
    
    -- Escala
    dia_semana JSON,                         -- [1,2,3,4,5] 1=domingo
    horario_inicio TIME,
    horario_fim TIME,
    data_inicio DATE,
    data_fim DATE,
    
    -- Status
    ativo TINYINT DEFAULT 1,
    
    -- Custos
    valor_diaria DECIMAL(10,2),              -- Para cálculo de custo
    valor_hora_extra DECIMAL(10,2),
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(idUsuarios),
    UNIQUE KEY uk_tecnico_obra (obra_id, tecnico_id)
);

-- Nova tabela: obra_diario (diário de obra)
CREATE TABLE obra_diario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    obra_id INT NOT NULL,
    data DATE NOT NULL,
    
    -- Resumo do dia
    clima_manha ENUM('Sol','Nublado','Chuva','Garoa'),
    clima_tarde ENUM('Sol','Nublado','Chuva','Garoa'),
    
    -- Equipe presente (JSON)
    equipe_presente JSON,                    -- [{"tecnico_id":1, "entrada":"07:00", "saida":"17:00"}]
    
    -- Atividades executadas
    atividades_executadas TEXT,              -- Descrição textual
    etapas_avancadas JSON,                   -- [{"etapa_id":5, "percentual_antes":30, "percentual_depois":60}]
    
    -- Fotos
    fotos JSON,                              -- URLs das fotos do dia
    
    -- Problemas/impedimentos
    problemas TEXT,
    acoes_corretivas TEXT,
    
    -- Materiais recebidos/consumidos
    material_recebido TEXT,
    material_consumido TEXT,
    
    -- Visitas
    visitas_cliente TINYINT DEFAULT 0,
    visitas_fiscalizacao TINYINT DEFAULT 0,
    
    -- Responsável pelo preenchimento
    preenchido_por INT,
    preenchido_em DATETIME,
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (preenchido_por) REFERENCES usuarios(idUsuarios),
    UNIQUE KEY uk_obra_data (obra_id, data)
);

-- Nova tabela: obra_medicoes (medições parciais)
CREATE TABLE obra_medicoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    obra_id INT NOT NULL,
    
    -- Identificação
    numero_medicao INT,
    data_medicao DATE,
    
    -- Etapas medidas
    etapas_medidas JSON,                     -- [{"etapa_id":5, "percentual_medido":100, "valor":5000}]
    
    -- Valores
    valor_medido DECIMAL(15,2),
    descontos_retencoes DECIMAL(15,2),
    valor_liquido DECIMAL(15,2),
    
    -- Status
    status ENUM('EmElaboracao','Enviada','Aprovada','Paga') DEFAULT 'EmElaboracao',
    data_envio DATE,
    data_aprovacao DATE,
    data_pagamento DATE,
    
    -- Documentos
    arquivo_medicao VARCHAR(255),
    nota_fiscal VARCHAR(50),
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE
);

-- Nova tabela: obra_materiais
CREATE TABLE obra_materiais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    obra_id INT NOT NULL,
    produto_id INT NOT NULL,
    
    -- Quantidades
    quantidade_prevista INT,
    quantidade_comprada INT DEFAULT 0,
    quantidade_consumida INT DEFAULT 0,
    quantidade_estoque INT DEFAULT 0,       -- No depósito da obra
    
    -- Valores
    valor_unitario_previsto DECIMAL(10,2),
    valor_unitario_real DECIMAL(10,2),
    
    -- Status
    status_compra ENUM('NaoComprado','EmCompra','Comprado','Entregue') DEFAULT 'NaoComprado',
    
    -- Fornecedor
    fornecedor_id INT,
    pedido_compra_id INT,                    -- Integração com compras
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(idProdutos),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(idFornecedores)
);
```

### 16.4 Funcionalidades do Acompanhamento em Obra

#### 16.4.1 Dashboard da Obra
```
┌─────────────────────────────────────────────────────────────┐
│  OBRA: CONDOMÍNIO SOLAR DAS PALMEIRAS                       │
│  Código: OB-2024-015 | Status: EM EXECUÇÃO                │
│  Progresso Geral: 65% [████████████░░░░░░░░░░]            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📅 CRONOGRAMA                                              │
│  ─────────────────────────────────────────────────────    │
│  Início Previsto: 01/03/2024 | Real: 05/03/2024 (+4d)      │
│  Término Previsto: 30/05/2024 | Estimado: 15/06/2024       │
│  Dias restantes: 45 | Atraso acumulado: 15 dias            │
│                                                             │
│  ETAPAS CRÍTICAS:                                           │
│  🔴 Passagem cabos Bloco B - Atrasada 5 dias               │
│  🟡 Instalação CFTV Bloco A - No prazo                     │
│  🟢 Configuração central - Não iniciada                    │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  👥 EQUIPE HOJE (09/04/2024)                                │
│  ─────────────────────────────────────────────────────    │
│  ✓ João (CFTV) - 07:15 ✓ Marcos (CFTV) - 07:05             │
│  ✓ Pedro (Redes) - 07:20 ✓ André (Alarmes) - 07:18         │
│  ⏸️ Carlos (CFTV) - Falta justificada                      │
│                                                             │
│  [📋 VER ESCALA COMPLETA]  [➕ ALOCAR TÉCNICO]             │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  💰 FINANCEIRO                                              │
│  ─────────────────────────────────────────────────────    │
│  Valor Contrato: R$ 180.000,00                             │
│  1ª Medição (Aprovada): R$ 45.000,00                       │
│  2ª Medição (Em análise): R$ 32.000,00                     │
│  Material Consumido: R$ 28.450,00                          │
│  Mão de Obra: R$ 15.200,00                                 │
│  Lucro Estimado: 34%                                       │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📸 DIÁRIO DE OBRA (Últimos 3 dias)                         │
│  ─────────────────────────────────────────────────────    │
│  [📷] [📷] [📷] [📷] [📷] 08/04                            │
│  [📷] [📷] [📷] [📷] 07/04                                  │
│  [📷] [📷] [📷] [📷] [📷] [📷] [📷] 06/04                   │
│                                                             │
│  [📝 PREENCHER DIÁRIO DE HOJE]                            │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

#### 16.4.2 Mapa da Obra
```
[MAPA INTERATIVO - PLANTA BAIXA OU GOOGLE MAPS]
├── Marcadores por etapa:
│   ├── 🔴 Etapas atrasadas
│   ├── 🟡 Etapas em andamento
│   ├── 🟢 Etapas concluídas
│   └── ⚪ Etapas futuras
├── Localização em tempo real dos técnicos
├── Rota de deslocamento dentro da obra
└── Fotos georreferenciadas
```

#### 16.4.3 Diário de Obra Digital
```
┌─────────────────────────────────────────────────────────────┐
│  DIÁRIO DE OBRA - 09/04/2024                               │
│  Obra: OB-2024-015 - Condomínio Solar                      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  🌤️ CLIMA                                                   │
│  Manhã: [●] Sol  [ ] Nublado  [ ] Chuva                    │
│  Tarde: [ ] Sol  [●] Nublado  [ ] Chuva                    │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  👥 EQUIPE PRESENTE                                         │
│  ─────────────────────────────────────────────────────    │
│  ☑️ João Silva - Entrada: 07:15 | Saída: 17:00             │
│     Função: Instalador CFTV | Check-in: [VER MAPA]         │
│  ☑️ Marcos Oliveira - Entrada: 07:05 | Saída: -           │
│  ⬜ Carlos Souza - Falta - Justificativa: [ANEXAR]          │
│                                                             │
│  [➕ Adicionar Técnico]                                    │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  🏗️ ATIVIDADES EXECUTADAS                                  │
│  ─────────────────────────────────────────────────────    │
│  • Concluída passagem de eletrodutos no Bloco B (3º andar)│
│  • Iniciada instalação de cameras no Bloco A              │
│  • Testes de conectividade - 100% OK                       │
│                                                             │
│  [+] Adicionar atividade                                   │
│                                                             │
│  📸 FOTOS DO DIA:                                          │
│  [📷 Adicionar] [📷] [📷] [📷] [📷] [📷]                   │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📊 AVANÇO DAS ETAPAS                                       │
│  ─────────────────────────────────────────────────────    │
│  Etapa 3 - Cabos Bloco B:  30% → 60%  [Atualizar]        │
│  Etapa 5 - CFTV Bloco A:   0% → 20%   [Atualizar]         │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ⚠️ PROBLEMAS / IMPEDIMENTOS                               │
│  ─────────────────────────────────────────────────────    │
│  • Elevador do Bloco B parado - subir por escada           │
│  • Material retardante não entregue - previsão amanhã      │
│                                                             │
│  ✅ AÇÕES CORRETIVAS:                                      │
│  • Solicitado material emergencial ao Depósito Central     │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📦 MATERIAL                                                 │
│  ─────────────────────────────────────────────────────    │
│  Recebido: 100m Cabo CAT6 (Extra) - Nota Fiscal #4521      │
│  Consumido: 200m Cabo CAT6 | 45 Conectores                 │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📝 OBSERVAÇÕES GERAIS                                      │
│  [Área de texto livre para observações]                    │
│                                                             │
│                                                             │
│                    [💾 SALVAR DIÁRIO]                      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

#### 16.4.4 Controle de Materiais na Obra
```
┌─────────────────────────────────────────────────────────────┐
│  ESTOQUE DA OBRA - OB-2024-015                              │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📦 MATERIAL NO DEPÓSITO DA OBRA                            │
│  ─────────────────────────────────────────────────────    │
│  Câmera IP 4MP Dome        | Estoque: 12 | Reservado: 8   │
│  NVR 32 Canais             | Estoque: 2  | Reservado: 1    │
│  Cabo UTP CAT6 (caixa 305m)| Estoque: 3  | Reservado: 2   │
│  Conectores RJ45 (pac 100) | Estoque: 5  | Reservado: 3   │
│                                                             │
│  [📥 LANÇAR ENTRADA]  [📤 LANÇAR SAÍDA]  [🔄 TRANSFERIR]  │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📋 MATERIAL PREVISTO VS REAL                               │
│  ─────────────────────────────────────────────────────    │
│  Item              | Previsto | Comprado | Consumido | %   │
│  ─────────────────────────────────────────────────────    │
│  Câmera IP 4MP     |    45   |    40    |    15    | 33% │
│  Cabo CAT6 (m)     | 2.500   |  2.000   |   850    | 34% │
│  NVR 32 canais     |     2   |     2    |     0    |  0% │
│                                                             │
│  Alertas:                                                  │
│  🔴 Câmera IP - Estoque baixo (comprar +20 unidades)       │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 16.5 Integração OS ↔ Obra

```
FLUXO DE INTEGRAÇÃO:

Obra criada no sistema
    ↓
Etapa definida (ex: "CFTV Bloco A")
    ↓
Gerador automaticamente cria OS vinculadas:
    ├── OS #2001 - Infra/Cabeamento Bloco A
    ├── OS #2002 - Instalação Câmeras Bloco A
    └── OS #2003 - Configuração/Entrega Bloco A
    ↓
Técnicos designados na etapa
    ↓
Execução via App Técnico (padrão)
    ↓
Conclusão das OS
    ↓
Atualização automática do % da etapa
    ↓
Atualização do % geral da obra
```

### 16.6 App do Gestor de Obra

Funcionalidades adicionais no app do técnico para gestores de obra:

```
┌─────────────────────────────────┐
│  🏗️ MODO OBRA - GESTOR          │
├─────────────────────────────────┤
│                                 │
│  OBRAS SOB MINHA GESTÃO        │
│  ─────────────────────────     │
│  🔵 OB-2024-015 - 65%           │
│     Solar das Palmeiras          │
│  🟢 OB-2024-008 - 90%           │
│     Loja Magazine Sul           │
│  🟡 OB-2024-021 - 30%           │
│     Condomínio Bela Vista       │
│                                 │
├─────────────────────────────────┤
│                                 │
│  QUADRO DE PRESENÇA HOJE       │
│  ─────────────────────────     │
│  OB-015: 8/9 presentes         │
│  OB-008: 2/2 presentes ✓       │
│  OB-021: 3/4 presentes         │
│                                 │
├─────────────────────────────────┤
│                                 │
│  ⚠️ ALERTAS                     │
│  ─────────────────────────     │
│  • OB-015: Etapa 3 atrasada    │
│  • OB-021: Material pendente   │
│  • OB-015: 2 faltas não justif.│
│                                 │
├─────────────────────────────────┤
│  [📝 DIÁRIO] [📸 FOTOS]        │
│  [👥 EQUIPE] [📦 MATERIAL]      │
│  [📊 MEDIÇÃO] [🗺️ MAPA]        │
└─────────────────────────────────┘
```

---

## 17. Serviços na OS (Mão de Obra / Atividades)

Além de produtos (materiais), a OS pode conter **serviços** que serão executados pelo técnico. Cada serviço funciona como um item de check de execução.

### 17.1 Conceito

```
OS #2045 - Posto Shell
├── PRODUTOS (Materiais usados)
│   ├── Câmera IP 4MP (Qtd: 2)
│   ├── Cabo CAT6 50m (Qtd: 1)
│   └── Conectores RJ45 (Qtd: 10)
│
└── SERVIÇOS (Mão de obra / Atividades)
    ├── ☐ Instalação de câmeras
    ├── ☐ Passagem de cabos estruturados
    ├── ☐ Configuração do sistema
    ├── ☐ Testes de funcionamento
    └── ☐ Treinamento ao cliente
```

### 17.2 Estrutura de Dados

```sql
-- Tabela: servicos (catálogo de serviços)
CREATE TABLE servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    
    -- Categoria e especialidade
    categoria VARCHAR(100),              -- CFTV, Alarme, Rede, etc
    especialidade VARCHAR(50),           -- Para qual técnico é indicado
    
    -- Tempo estimado (para planejamento)
    tempo_estimado_minutos INT DEFAULT 60,
    
    -- Checklist padrão (JSON com itens a verificar)
    checklist_padrao JSON,               -- [{"ordem":1, "desc":"Verificar angulo"}]
    
    -- Campos opcionais (podem ficar nulos)
    valor_sugerido DECIMAL(10,2),        -- Sugestão de valor (opcional)
    
    -- Status
    ativo TINYINT DEFAULT 1,
    
    created_at DATETIME,
    updated_at DATETIME
);

-- Tabela: os_servicos (serviços vinculados à OS)
CREATE TABLE os_servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    os_id INT NOT NULL,                  -- FK para os.idOs
    servico_id INT NOT NULL,             -- FK para servicos.id
    
    -- Quantidade e observações
    quantidade INT DEFAULT 1,
    observacao TEXT,                     -- Detalhes específicos desta OS
    
    -- Execução (preenchido pelo técnico)
    status ENUM('Pendente','EmExecucao','Concluido','Cancelado') DEFAULT 'Pendente',
    
    -- Checklist de execução (pode ser diferente do padrão)
    checklist_execucao JSON,             -- Itens marcados pelo técnico
    
    -- Técnico e datas
    tecnico_id INT,                      -- Quem executou
    data_inicio DATETIME,                -- Quando começou
    data_conclusao DATETIME,             -- Quando terminou
    
    -- Anexos
    fotos JSON,                          -- URLs das fotos deste serviço
    assinatura_cliente TEXT,             -- Base64 da assinatura
    laudo_tecnico TEXT,                  -- Descrição do que foi feito
    
    -- Ordem de execução (para sequência)
    ordem_execucao INT DEFAULT 0,
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (os_id) REFERENCES os(idOs) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id),
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(idUsuarios)
);

-- Dados iniciais (exemplos)
INSERT INTO servicos (codigo, nome, descricao, categoria, especialidade, tempo_estimado_minutos, checklist_padrao) VALUES
('SRV-CFTV-001', 'Instalação de Câmeras', 'Instalação completa de câmeras de segurança', 'CFTV', 'CFTV', 90, '[{"ordem":1,"desc":"Verificar posição com cliente"},{"ordem":2,"desc":"Instalar suporte"},{"ordem":3,"desc":"Conectar cabos"},{"ordem":4,"desc":"Ajustar angulo"}]'),
('SRV-CFTV-002', 'Configuração de Sistema CFTV', 'Configuração de gravadores e acesso remoto', 'CFTV', 'CFTV', 60, '[{"ordem":1,"desc":"Configurar rede"},{"ordem":2,"desc":"Configurar gravação"},{"ordem":3,"desc":"Configurar app cliente"},{"ordem":4,"desc":"Testar acesso remoto"}]'),
('SRV-CFTV-003', 'Manutenção Preventiva CFTV', 'Limpeza e verificação de sistema CFTV', 'CFTV', 'CFTV', 45, '[{"ordem":1,"desc":"Limpar lentes"},{"ordem":2,"desc":"Verificar conexões"},{"ordem":3,"desc":"Testar gravação"},{"ordem":4,"desc":"Verificar espaço em disco"}]'),
('SRV-ALM-001', 'Instalação de Alarme', 'Instalação de sensores e central de alarme', 'Alarmes', 'Alarmes', 120, '[{"ordem":1,"desc":"Instalar sensores"},{"ordem":2,"desc":"Instalar sirene"},{"ordem":3,"desc":"Programar zonas"},{"ordem":4,"desc":"Testar disparo"}]'),
('SRV-RED-001', 'Passagem de Cabos de Rede', 'Passagem e organização de cabos estruturados', 'Redes', 'Redes', 90, '[{"ordem":1,"desc":"Identificar pontos"},{"ordem":2,"desc":"Passar cabos"},{"ordem":3,"desc":"Crimpagem"},{"ordem":4,"desc":"Testar conectividade"}]'),
('SRV-ACE-001', 'Instalação de Controle de Acesso', 'Instalação de catracas/leitores/fechaduras', 'Controle de Acesso', 'Controle de Acesso', 180, '[{"ordem":1,"desc":"Instalar equipamentos"},{"ordem":2,"desc":"Ligar elétrica"},{"ordem":3,"desc":"Configurar usuários"},{"ordem":4,"desc":"Testar liberações"}]');
```

### 17.3 Fluxo na Prática

#### Criação da OS (Admin/Atendente)
```
Nova OS #2056
├── Cliente: Supermercado Sul
├── Técnico: João
├── Descrição: Instalação CFTV Completa
│
├── PRODUTOS ADICIONADOS:
│   ├── Câmera IP 4MP - 8 unidades
│   ├── NVR 16 canais - 1 unidade
│   └── Cabo CAT6 305m - 1 caixa
│
└── SERVIÇOS ADICIONADOS:
    ├── ☐ Instalação de Câmeras (Qtd: 1)
    │   └─ Observação: Instalar nas 8 posições marcadas
    │
    ├── ☐ Passagem de Cabos de Rede (Qtd: 1)
    │   └─ Observação: Incluir organização no rack
    │
    ├── ☐ Configuração de Sistema CFTV (Qtd: 1)
    │   └─ Observação: Configurar acesso remoto no cel do Sr. Carlos
    │
    └── ☐ Treinamento ao Cliente (Qtd: 1)
        └─ Observação: Explicar app e recuperação de vídeos
```

#### Execução pelo Técnico (App)
```
OS #2056 - Supermercado Sul

SERVIÇOS PENDENTES: 4

[ ] Instalação de Câmeras
    Tempo estimado: 1h 30min
    [▶️ INICIAR]

[ ] Passagem de Cabos de Rede
    Tempo estimado: 1h 30min
    [▶️ INICIAR]

[ ] Configuração de Sistema CFTV
    Tempo estimado: 1h
    [▶️ INICIAR]

[ ] Treinamento ao Cliente
    Tempo estimado: 30min
    [▶️ INICIAR]

---

Técnico clica em "Instalação de Câmeras"

Abre checklist específico:
☐ Verificar posição com cliente
☐ Instalar suporte
☐ Conectar cabos
☐ Ajustar angulo
☐ Testar imagem

[📸 Adicionar Fotos]
[📝 Observações]
[✓ CONCLUIR SERVIÇO]

---

Após concluir:

✅ Instalação de Câmeras - CONCLUÍDO
   Início: 08:15 | Término: 09:45
   Tempo gasto: 1h 30min
   [📷 4 fotos] [✍️ Assinatura]

SERVIÇOS PENDENTES: 3
```

### 17.4 Tela de Execução de Serviços (App)

```
┌─────────────────────────────────────────────────────────────┐
│  OS #2056 - Supermercado Sul                                │
│  ⏱️ Em atendimento há 45 minutos                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📋 SERVIÇOS A EXECUTAR                                     │
│  ─────────────────────────────────────────────────────    │
│                                                             │
│  ✅ Instalação de Câmeras                                   │
│     Concluído às 09:45                                      │
│     [Ver detalhes]                                          │
│                                                             │
│  🔄 Passagem de Cabos de Rede                               │
│     [INICIADO] - Em andamento                               │
│     ⏱️ 00:23:15 decorrido                                   │
│                                                             │
│     Checklist:                                              │
│     ☑️ Identificar pontos                                   │
│     ☑️ Passar cabos                                         │
│     ⬜ Crimpagem                                            │
│     ⬜ Testar conectividade                                │
│                                                             │
│     [📸 Adicionar Foto]                                     │
│     [✓ Concluir Serviço]                                    │
│                                                             │
│  ⏳ Configuração de Sistema CFTV                             │
│     Aguardando                                              │
│     [▶️ Iniciar]                                            │
│                                                             │
│  ⏳ Treinamento ao Cliente                                  │
│     Aguardando                                              │
│     [▶️ Iniciar]                                            │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [✓ CONCLUIR OS GERAL]                                     │
│  (disponível quando todos serviços estiverem prontos)      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 17.5 Visualização no Admin (Mapos)

#### Aba "Serviços" na OS
```php
// Nova aba na visualização da OS

<div id="tabServicos" class="tab-pane">
    <h4>Serviços a Executar</h4>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="30">#</th>
                <th>Serviço</th>
                <th>Observação</th>
                <th>Técnico</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicos as $i => $srv): ?>
            <tr class="<?= $srv->status == 'Concluido' ? 'success' : '' ?>">
                <td><?= $i + 1 ?></td>
                <td>
                    <strong><?= $srv->servico_nome ?></strong>
                    <?php if ($srv->checklist_execucao): ?>
                    <br><small class="text-muted">
                        <?= count(json_decode($srv->checklist_execucao)) ?> itens verificados
                    </small>
                    <?php endif; ?>
                </td>
                <td><?= $srv->observacao ?></td>
                <td>
                    <?php if ($srv->tecnico_nome): ?>
                        <i class="fas fa-user"></i> <?= $srv->tecnico_nome ?>
                    <?php else: ?>
                        <span class="label">Não iniciado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    $badge_class = [
                        'Pendente' => 'default',
                        'EmExecucao' => 'warning',
                        'Concluido' => 'success',
                        'Cancelado' => 'danger'
                    ][$srv->status];
                    ?>
                    <span class="label label-<?= $badge_class ?>">
                        <?= $srv->status ?>
                    </span>
                    <?php if ($srv->data_conclusao): ?>
                    <br><small>
                        <?= date('d/m H:i', strtotime($srv->data_conclusao)) ?>
                    </small>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-info btn-small" 
                            onclick="verDetalhesServico(<?= $srv->id ?>)">
                        <i class="fas fa-eye"></i> Detalhes
                    </button>
                    <?php if ($srv->fotos): ?>
                    <button class="btn btn-default btn-small">
                        <i class="fas fa-images"></i> Fotos
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Progresso -->
    <div class="progress">
        <?php 
        $total = count($servicos);
        $concluidos = count(array_filter($servicos, function($s) { 
            return $s->status == 'Concluido'; 
        }));
        $percentual = $total > 0 ? ($concluidos / $total) * 100 : 0;
        ?>
        <div class="bar bar-success" style="width: <?= $percentual ?>%">
            <?= $concluidos ?>/<?= $total ?> serviços (<?= round($percentual) ?>%)
        </div>
    </div>
    
    <a href="#modalAddServico" data-toggle="modal" class="btn btn-success">
        <i class="fas fa-plus"></i> Adicionar Serviço
    </a>
</div>
```

### 17.6 Relatórios de Serviços

```
RELATÓRIO DE SERVIÇOS EXECUTADOS
Período: 01/04/2024 a 30/04/2024

┌─────────────────┬──────────┬───────────┬────────────┬──────────┐
│ Serviço         │ Total    │ Concluídos│ Em Andamento│ Pendentes │
├─────────────────┼──────────┼───────────┼────────────┼──────────┤
│ Instalação CFTV │    25    │    23     │     1      │    1     │
│ Manutenção CFTV │    18    │    18     │     0      │    0     │
│ Instalação Alarm│    12    │    10     │     2      │    0     │
│ Passagem Cabos  │    30    │    28     │     2      │    0     │
│ Configuração    │    25    │    24     │     1      │    0     │
└─────────────────┴──────────┴───────────┴────────────┴──────────┘

TEMPO MÉDIO POR SERVIÇO:
• Instalação CFTV: 1h 45min (estimado: 1h 30min)
• Manutenção CFTV: 42min (estimado: 45min)
• Instalação Alarme: 2h 10min (estimado: 2h)

TÉCNICOS - SERVIÇOS CONCLUÍDOS:
• João Silva: 28 serviços
• Marcos Oliveira: 31 serviços
• Pedro Santos: 19 serviços
```

### 17.7 Vantagens desta Abordagem

1. **Padronização**: Cada serviço tem checklist específico
2. **Rastreabilidade**: Sabe exatamente o que foi feito
3. **Controle de qualidade**: Checklist garante nada esquecido
4. **Histórico**: Consultar serviços anteriores por cliente
5. **Planejamento**: Tempo estimado ajuda a organizar agenda
6. **Documentação**: Fotos e laudos por serviço
7. **Sem valores obrigatórios**: Foco na execução, não no financeiro

---

**Data de criação:** 2026-04-13  
**Versão:** 1.2  
**Status:** PoC - Com Serviços na OS
