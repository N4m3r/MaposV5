# Projeto de Redesign - Gestão de Obras MapOS

## 1. Visão Geral

### 1.1 Objetivo
Redesenhar completamente o sistema de gestão de obras do MapOS para criar uma solução integrada que permita:
- Acompanhamento em tempo real das atividades
- Gestão de etapas e tarefas diárias
- Controle de presença e check-in dos técnicos
- Registro fotográfico documentado
- Gestão de impedimentos e bloqueios

### 1.2 Integração com Sistema Existente
O redesign será **100% compatível** com o sistema atual:
- Reutiliza tabelas existentes (`obras`, `obra_etapas`, `obra_diario`, `obra_equipe`)
- Mantém integridade com o sistema de OS (ordens de serviço)
- Integra com o portal do técnico existente
- Preserva permissões e autenticação atual

---

## 2. Arquitetura do Sistema

### 2.1 Entidades Principais

```
┌─────────────────────────────────────────────────────────────┐
│                       OBRA                                  │
├─────────────────────────────────────────────────────────────┤
│  id, codigo, nome, cliente_id, endereco, status            │
│  percentual_concluido, data_inicio, data_fim_prevista        │
└──────────────┬──────────────────────────────────────────────┘
               │
    ┌──────────┼──────────┐
    │          │          │
    ▼          ▼          ▼
┌─────────┐ ┌──────────┐ ┌──────────┐
│ ETAPAS  │ │ TAREFAS  │ │ DIÁRIO   │
└────┬────┘ └────┬─────┘ └────┬─────┘
     │           │            │
     ▼           ▼            ▼
┌─────────┐ ┌──────────┐ ┌──────────┐
│ATIVIDADE│ │ CHECK-IN │ │ IMPEDIM. │
│  DIA    │ │ FOTOS    │ │ REGISTRO │
└─────────┘ └──────────┘ └──────────┘
```

### 2.2 Tabelas do Banco de Dados

#### Tabelas Existentes (Manter)
- `obras` - Cadastro principal
- `obra_etapas` - Etapas da obra
- `obra_diario` - Diário de obra (registro diário)
- `obra_equipe` - Equipe designada

#### Novas Tabelas (Criar via Migration)

**1. obra_atividades** (Atividades Diárias das Etapas)
```sql
CREATE TABLE obra_atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    etapa_id INT NOT NULL,
    tecnico_id INT,
    data_atividade DATE NOT NULL,
    
    -- Descrição
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    tipo ENUM('trabalho', 'impedimento', 'visita', 'manutencao', 'outro') DEFAULT 'trabalho',
    
    -- Status e Progresso
    status ENUM('agendada', 'iniciada', 'pausada', 'concluida', 'cancelada') DEFAULT 'agendada',
    percentual_concluido INT DEFAULT 0,
    
    -- Horários
    hora_inicio TIME,
    hora_fim TIME,
    horas_trabalhadas DECIMAL(5,2) DEFAULT 0,
    
    -- Impedimento (quando não pode realizar)
    impedimento BOOLEAN DEFAULT FALSE,
    motivo_impedimento TEXT,
    tipo_impedimento ENUM('clima', 'falta_material', 'falta_ferramenta', 'acesso_negado', 
                         'problema_tecnico', 'outro') NULL,
    
    -- Localização
    checkin_lat DECIMAL(10,8),
    checkin_lng DECIMAL(11,8),
    checkout_lat DECIMAL(10,8),
    checkout_lng DECIMAL(11,8),
    
    -- Fotos (JSON)
    fotos_checkin JSON,
    fotos_atividade JSON,
    fotos_checkout JSON,
    
    -- Metadados
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX (obra_id),
    INDEX (etapa_id),
    INDEX (tecnico_id),
    INDEX (data_atividade),
    INDEX (status)
);
```

**2. obra_atividades_historico** (Registro de mudanças)
```sql
CREATE TABLE obra_atividades_historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atividade_id INT NOT NULL,
    tecnico_id INT,
    tipo_alteracao ENUM('inicio', 'pausa', 'retorno', 'conclusao', 'impedimento', 'foto', 'observacao'),
    descricao TEXT,
    percentual_anterior INT,
    percentual_novo INT,
    localizacao_lat DECIMAL(10,8),
    localizacao_lng DECIMAL(11,8),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (atividade_id)
);
```

**3. obra_checkins** (Check-ins por atividade)
```sql
CREATE TABLE obra_checkins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atividade_id INT NOT NULL,
    tecnico_id INT NOT NULL,
    tipo ENUM('checkin', 'checkout', 'pausa', 'retorno') NOT NULL,
    
    -- Localização
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    endereco_detectado VARCHAR(255),
    
    -- Dados
    foto_url VARCHAR(255),
    observacao TEXT,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (atividade_id),
    INDEX (tecnico_id)
);
```

---

## 3. Fluxo de Funcionamento

### 3.1 Fluxo do Técnico no Dia-a-Dia

```
┌─────────────┐
│   LOGIN     │
└──────┬──────┘
       │
       ▼
┌─────────────┐     ┌─────────────┐
│   OBRA      │────▶│  ATIVIDADE  │
│  DO DIA     │     │   DO DIA    │
└─────────────┘     └──────┬──────┘
                           │
           ┌───────────────┼───────────────┐
           │               │               │
           ▼               ▼               ▼
     ┌──────────┐    ┌──────────┐   ┌──────────┐
     │ CHECK-IN │    │ CHECK-IN │   │IMPEDIMENTO│
     │  INICIO  │    │  FIM     │   │ REGISTRO │
     └────┬─────┘    └────┬─────┘   └────┬─────┘
          │               │              │
          ▼               ▼              ▼
     ┌──────────┐    ┌──────────┐   ┌──────────┐
     │  FOTOS   │    │  FOTOS   │   │  FOTOS   │
     │  INICIO  │    │  FIM     │   │ EVIDÊNCIA│
     └────┬─────┘    └────┬─────┘   └────┬─────┘
          │               │              │
          ▼               ▼              ▼
     ┌──────────┐    ┌──────────┐   ┌──────────┐
     │ ATIVIDADE│    │ RELATÓRIO│   │ REGISTRO │
     │  EXECUÇÃO│    │  FINAL   │   │ BLOQUEIO │
     └──────────┘    └──────────┘   └──────────┘
```

### 3.2 Estados de uma Atividade

```
                    ┌─────────────┐
                    │  AGENDADA   │
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
              ▼            ▼            ▼
        ┌──────────┐ ┌──────────┐ ┌──────────┐
        │ CANCELADA│ │  INICIADA│ │IMPEDIMENTO│
        └──────────┘ └────┬─────┘ └────┬─────┘
```

---

### 3.3 Fluxo do Cliente (Acompanhamento)

```
┌─────────────┐
│   LOGIN     │
│  (PORTAL)   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  DASHBOARD  │
│   OBRAS     │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────────────────┐
│           VISUALIZAÇÃO DA OBRA              │
├─────────────────────────────────────────────┤
│  • Progresso Geral (Timeline)                │
│  • Etapas Concluídas / Em Andamento         │
│  • Fotos da Obra (Galeria)                  │
│  • Atividades Recentes                      │
│  • Calendário de Execução                   │
│  • Relatório Simplificado                   │
│  • Contato com Gestor                       │
└─────────────────────────────────────────────┘
```

---

## 3.4 Visão do Cliente na Obra

```
┌─────────────────────────────────────────────────────────────────────┐
│                     PORTAL DO CLIENTE                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │  MINHAS OBRAS                                               │   │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐                        │   │
│  │  │ OBRA A  │ │ OBRA B  │ │ OBRA C  │                        │   │
│  │  │ 75%     │ │ 30%     │ │ 100%    │                        │   │
│  │  │ ● Ativa │ ● Ativa  │ ✓ Entregue│                        │   │
│  │  └─────────┘ └─────────┘ └─────────┘                        │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │  OBRA SELECIONADA: "Reforma do Escritório"                  │   │
│  ├─────────────────────────────────────────────────────────────┤   │
│  │                                                             │   │
│  │  📊 PROGRESSO GERAL: 75% concluído                          │   │
│  │  ┌───────────────────────────────────────┐                  │   │
│  │  │████████████████████████████░░░░░░░░░░░│                  │   │
│  │  └───────────────────────────────────────┘                  │   │
│  │  Previsão de entrega: 15/05/2026 (em 15 dias)              │   │
│  │                                                             │   │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │   │
│  │  │ ETAPAS      │ │ FOTOS       │ │ ATIVIDADES  │           │   │
│  │  │             │ │             │ │             │           │   │
│  │  │ □ Demolição │ │ [FOTO]      │ │ • Hoje      │           │   │
│  │  │ ✓ Alvenaria │ │ [FOTO]      │ │ • Ontem     │           │   │
│  │  │ ▶ Elétrica  │ │ [FOTO]      │ │ • Semana    │           │   │
│  │  │ ○ Pintura   │ │ [FOTO]      │ │             │           │   │
│  │  │ ○ Acabamento│ │ Ver mais... │ │             │           │   │
│  │  └─────────────┘ └─────────────┘ └─────────────┘           │   │
│  │                                                             │   │
│  │  📋 ÚLTIMAS ATUALIZAÇÕES:                                   │   │
│  │  ─────────────────────────────────────────────────────     │   │
│  │  18/04 - Elétrica 75% concluída                            │   │
│  │  17/04 - 12 novas fotos adicionadas                        │   │
│  │  15/04 - Alvenaria 100% concluída                          │   │
│  │                                                             │   │
│  │  📷 GALERIA DE FOTOS:                                       │   │
│  │  [FOTO] [FOTO] [FOTO] [FOTO] [FOTO] [+]                     │   │
│  │  Antes/Durante/Depois | Por etapa | Por data                │   │
│  │                                                             │   │
│  │  📄 DOCUMENTOS:                                             │   │
│  │  • Contrato ▾  • ART ▾  • Relatório Mensal ▾               │   │
│  │                                                             │   │
│  │  💬 CONTATO:                                                │   │
│  │  Gestor: João Silva | Tel: (11) 99999-9999                  │   │
│  │  [Enviar Mensagem]                                         │   │
│  │                                                             │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```
                        │            │
              ┌─────────┴──┐         │
              │            │         │
              ▼            ▼         ▼
        ┌──────────┐ ┌──────────┐ ┌────┐
        │  PAUSADA │ │CONCLUÍDA │ │    │
        └────┬─────┘ └──────────┘ └────┘
             │
             ▼
        ┌──────────┐
        │ RETORNO  │
        └────┬─────┘
             │
             ▼
        ┌──────────┐
        │  INICIADA│
        └──────────┘
```

---

## 3.5 Portal do Cliente - Funcionalidades

### Acesso e Dashboard

**Login via Portal do Cliente (Mine):**
- Cliente acessa `mine/obras` para ver suas obras
- Login unificado com o sistema de OS
- Permissão `visualizar_obras` necessária

**Dashboard do Cliente:**
- Cards visuais das obras vinculadas
- Indicador de progresso em tempo real
- Alertas de atualizações importantes
- Timeline de eventos

### Visualização de Progresso

**Timeline de Etapas:**
```
[Data Início] ────●────●────●────●──── [Previsão Entrega]
                   ▲    ▲    ▲    ▲
                   │    │    │    │
              Demolição │    │    └── Acabamento
              Concluída Alvenaria │
                     Concluída  └── Pintura
                                  Em Andamento
```

**Indicadores Visuais:**
- Barra de progresso geral (%)
- Etapas concluídas (verde ✓)
- Etapas em andamento (azul ▶)
- Etapas pendentes (cinza ○)
- Atrasos destacados (vermelho)

### Galeria de Fotos do Cliente

**Organização:**
- **Visualização por Etapa:** Fotos agrupadas por etapa da obra
- **Visualização por Data:** Timeline cronológica
- **Comparativo Antes/Depois:** Slider de comparação
- **Destaques:** Fotos marcadas como importantes pelo gestor

**Recursos:**
- Zoom nas fotos
- Download individual ou em lote
- Compartilhamento (link temporário)
- Legenda e descrição de cada foto

### Atividades e Diário da Obra

**Visualização Simplificada:**
- Data da atividade
- Tipo (trabalho, visita, impedimento)
- Descrição resumida
- Técnico responsável
- Fotos da atividade (se houver)

**Filtros:**
- Por período (últimos 7 dias, 30 dias, todo período)
- Por tipo de atividade
- Por etapa

### Relatórios para Cliente

**Relatório Semanal/Mensal Automático:**
- Resumo do período
- Atividades realizadas
- Fotos em destaque
- Percentual de evolução
- Previsão atualizada de término

**Download em PDF:**
- Relatório formatado para impressão
- Todas as fotos do período
- Gráficos de progresso

### Notificações ao Cliente

**Eventos que geram notificação:**
- Início de nova etapa
- Conclusão de etapa
- Adição de fotos (opcional)
- Impedimento significativo
- Alteração na data prevista
- Mensagem do gestor

**Canais:**
- Notificação no portal (badge)
- E-mail (configurável)
- WhatsApp (se integrado)

### Comunicação Direta

**Chat/Mensagens:**
- Cliente pode enviar mensagem ao gestor
- Histórico de conversas
- Notificação de novas mensagens
- Anexos (documentos, fotos)

**Contatos:**
- Gestor da obra (nome, telefone, email)
- Equipe técnica principal
- Botão "Ligar Agora" (mobile)

---

## 4. Estrutura de Controllers

### 4.1 Obras.php (Admin)

```php
class Obras extends MY_Controller
{
    // CRUD de Obras
    public function index()           // Listar obras
    public function adicionar()       // Nova obra
    public function editar($id)       // Editar obra
    public function visualizar($id)   // Dashboard da obra
    
    // Gestão de Etapas
    public function etapas($obra_id)
    public function adicionarEtapa()
    public function editarEtapa($id)
    public function excluirEtapa($id)
    
    // Gestão de Atividades
    public function atividades($obra_id)
    public function adicionarAtividade()
    public function editarAtividade($id)
    
    // Relatórios
    public function relatorioDiario($obra_id)
    public function relatorioProgresso($obra_id)
    public function exportarExcel($obra_id)
}
```

### 4.2 Obras_tecnico.php (Portal do Técnico)

```php
class Obras_tecnico extends CI_Controller
{
    // Views
    public function minhasObras()      // Obras do técnico
    public function obra($id)          // Dashboard da obra
    public function atividade($id)   // Detalhe da atividade
    
    // Ações
    public function iniciarAtividade()
    public function pausarAtividade()
    public function retomarAtividade()
    public function finalizarAtividade()
    
    // Check-in/Check-out
    public function registrarCheckin()
    public function registrarCheckout()
    
    // Impedimento
    public function registrarImpedimento()
    
    // Fotos
    public function uploadFoto()
    public function listarFotos()
    
    // API Mobile
    public function api_getAtividades()
    public function api_registrarAcao()
}
```

### 4.3 Obras_cliente.php (Portal do Cliente via Mine)

```php
class Obras_cliente extends CI_Controller
{
    // Views Principais
    public function minhasObras()           // Lista de obras do cliente
    public function visualizar($obra_id)    // Dashboard da obra (timeline)
    public function etapas($obra_id)        // Visualização de etapas
    public function atividades($obra_id)  // Atividades realizadas
    
    // Fotos
    public function fotos($obra_id)       // Galeria de fotos
    public function fotoDetalhe($foto_id) // Visualização ampliada
    
    // Relatórios
    public function relatorio($obra_id)   // Relatório simplificado
    public function downloadRelatorio($obra_id, $formato = 'pdf')
    
    // Comunicação
    public function mensagens($obra_id)     // Chat com gestor
    public function enviarMensagem()
    
    // Notificações
    public function notificacoes()
    public function marcarLida($notificacao_id)
    
    // API para App Cliente
    public function api_getObra()
    public function api_getProgresso()
    public function api_getFotos()
    public function api_getAtividades()
}
```

Ou integrado ao controller Mine existente:

```php
// No controller Mine.php - adicionar métodos:

class Mine extends CI_Controller
{
    // ... métodos existentes ...
    
    // Novos métodos para Obras (Cliente)
    public function obras()                    // Lista de obras
    public function visualizarObra($id)        // Dashboard da obra
    public function obraEtapas($obra_id)       // Timeline de etapas
    public function obraFotos($obra_id)        // Galeria de fotos
    public function obraAtividades($obra_id) // Atividades realizadas
    public function obraRelatorio($obra_id)    // Relatório para cliente
    public function obraMensagens($obra_id)    // Chat com gestor
}
```

---

## 5. Estrutura de Views

### 5.1 Área Administrativa

```
views/obras/
├── obras_list.php          # Listagem de obras
├── obra_form.php           # Form add/edit
├── obra_view.php           # Dashboard da obra
├── etapas/
│   ├── etapas_list.php
│   └── etapa_form.php
├── atividades/
│   ├── atividades_list.php
│   └── atividade_form.php
└── relatorios/
    ├── diario_obra.php
    └── progresso.php
```

### 5.2 Portal do Técnico

```
views/obras_tecnico/
├── minhas_obras.php        # Lista de obras do técnico
├── obra_dashboard.php      # Dashboard da obra
├── atividade_execucao.php  # Tela de execução da atividade
├── checkin.php             # Tela de check-in com câmera
├── impedimento.php         # Tela de registro de impedimento
├── fotos_galeria.php       # Galeria de fotos
└── relatorio_diario.php    # Relatório do dia
```

### 5.3 Portal do Cliente (Conecte)

```
views/conecte/
├── obras.php                    # Lista de obras do cliente
├── obra_view.php                # Dashboard da obra (timeline)
├── obra_etapas.php              # Timeline de etapas
├── obra_fotos.php               # Galeria de fotos
│   ├── fotos_galeria.php        # Grid de fotos
│   └── foto_detalhe.php         # Visualização ampliada
├── obra_atividades.php          # Lista de atividades
├── obra_relatorio.php           # Relatório simplificado
└── obra_mensagens.php           # Chat com gestor
```

**Estrutura de Widgets Reutilizáveis:**
```
views/obras/widgets/
├── progresso_bar.php          # Barra de progresso percentual
├── timeline_etapas.php          # Timeline visual de etapas
├── card_foto.php                # Card de foto com hover
├── atividade_item.php           # Item de atividade na lista
├── notificacao_badge.php        # Badge de notificação
└── status_label.php             # Label de status colorido
```

---

## 6. Funcionalidades Detalhadas

### 6.1 Gestão de Etapas

**Estrutura de Etapas:**
- Hierarquia: Obra → Etapas → Atividades
- Status: Não Iniciada | Em Andamento | Concluída | Atrasada | Paralisada
- Cada etapa tem previsão de início e fim
- Percentual de conclusão calculado automaticamente

**Exemplo de Etapas para uma Obra:**
```
Obra: Instalação Elétrica Condomínio
├── Etapa 1: Preparação (5 dias)
│   ├── Atividade 1.1: Remoção de equipamentos antigos
│   └── Atividade 1.2: Limpeza e organização
├── Etapa 2: Instalação Cabeamento (10 dias)
│   ├── Atividade 2.1: Passagem de cabos - Bloco A
│   ├── Atividade 2.2: Passagem de cabos - Bloco B
│   └── Atividade 2.3: Conexões elétricas
├── Etapa 3: Testes e Qualidade (3 dias)
│   ├── Atividade 3.1: Testes de continuidade
│   └── Atividade 3.2: Testes de carga
└── Etapa 4: Entrega (2 dias)
    ├── Atividade 4.1: Treinamento
    └── Atividade 4.2: Documentação
```

### 6.2 Atividades Diárias

**Campos de uma Atividade:**
- **Dados Básicos**: Título, descrição, etapa vinculada
- **Atribuição**: Técnico responsável
- **Agendamento**: Data, hora prevista de início/fim
- **Execução**: Horas trabalhadas, percentual executado
- **Materiais**: Lista de materiais utilizados
- **Observações**: Notas técnicas

**Tipos de Atividade:**
- `trabalho` - Execução normal
- `impedimento` - Não foi possível trabalhar
- `visita` - Visita técnica/inspeção
- `manutencao` - Manutenção corretiva
- `outro` - Outros tipos

### 6.3 Sistema de Check-in/Check-out

**Check-in Inicial:**
1. Técnico acessa atividade do dia
2. Solicita geolocalização (obrigatória)
3. Tira foto de início (obrigatória)
4. Registra hora automática
5. Status muda para "Iniciada"

**Durante Execução:**
- Pode adicionar fotos a qualquer momento
- Pode pausar (almoço, outra prioridade)
- Sistema calcula tempo trabalhado

**Check-out Final:**
1. Técnico marca conclusão
2. Tira foto de finalização
3. Informa percentual executado
4. Adiciona observações
5. Sistema calcula horas totais

**Validações:**
- Geolocalização deve estar próxima da obra (raio configurável)
- Foto obrigatória para cada check-in/out
- Horário mínimo de permanência

### 6.4 Registro de Impedimentos

**Quando Registrar:**
- Chegou na obra mas não pode trabalhar
- Atividade interrompida por motivos externos
- Falta de material, ferramenta ou acesso

**Campos do Impedimento:**
- `tipo_impedimento`: clima, falta_material, falta_ferramenta, acesso_negado, problema_tecnico, outro
- `motivo_impedimento`: Descrição detalhada
- `fotos`: Evidências fotográficas
- `hora_inicio`: Quando identificou
- `hora_fim`: Quando foi resolvido (se aplicável)

**Fluxo:**
1. Técnico identifica impedimento
2. Registra tipo e descrição
3. Tira fotos de evidência
4. Sistema mantém atividade em "Impedimento"
5. Quando resolvido, técnico retoma atividade

### 6.5 Galeria de Fotos

**Organização:**
```
Fotos por Obra/
├── Check-ins/
│   ├── YYYY-MM-DD/
│   │   └── atividade_id_foto_timestamp.jpg
├── Atividades/
│   └── YYYY-MM-DD/
├── Check-outs/
│   └── YYYY-MM-DD/
└── Impedimentos/
    └── YYYY-MM-DD/
```

**Recursos:**
- Visualização em timeline
- Filtro por data, técnico, tipo
- Download em lote
- Comparação antes/depois

---

## 7. Dashboard e Relatórios

### 7.1 Dashboard da Obra (Admin)

**Widgets:**
- Progresso geral (gráfico circular)
- Etapas em andamento (cards)
- Atividades do dia (lista)
- Técnicos ativos (fotos + status)
- Alertas de atraso
- Fotos recentes (miniaturas)

**Gráficos:**
- Evolução do percentual de conclusão
- Horas trabalhadas por dia/semana
- Comparativo planejado vs executado
- Distribuição de atividades por técnico

### 7.2 Dashboard do Técnico

**Cards Principais:**
- "Você tem X atividades hoje"
- Próxima atividade com countdown
- Status atual (Trabalhando/Pausa)
- Resumo da semana

**Lista de Atividades:**
- Agrupadas por data
- Status visual (cor + ícone)
- Botão de ação rápida

### 7.3 Relatórios

**Relatório Diário de Obra (RDO):**
- Data e clima
- Equipe presente
- Atividades executadas
- Fotos do dia
- Problemas e ações corretivas
- Materiais recebidos/consumidos
- Assinatura digital

**Relatório de Progresso:**
- Comparativo planejado vs real
- Curva S (avanço acumulado)
- Análise de desvios
- Previsão de término

---

## 8. API e Mobile

### 8.1 Endpoints da API

```
GET  /api/obras                    # Listar obras do técnico
GET  /api/obras/{id}                # Detalhes da obra
GET  /api/obras/{id}/atividades     # Atividades da obra
POST /api/atividades/{id}/iniciar   # Iniciar atividade
POST /api/atividades/{id}/pausar    # Pausar atividade
POST /api/atividades/{id}/retomar   # Retomar atividade
POST /api/atividades/{id}/finalizar  # Finalizar atividade
POST /api/atividades/{id}/impedir   # Registrar impedimento
POST /api/atividades/{id}/foto      # Upload de foto
GET  /api/atividades/{id}/fotos     # Listar fotos
```

### 8.2 App Mobile (PWA)

**Recursos:**
- Funciona offline (sync quando voltar online)
- Notificações push de novas atividades
- Camera integrada
- Geolocalização em background
- Relatório por voz (speech-to-text)

---

## 9. Permissões e Acessos

### 9.1 Perfis

**Administrador:**
- Todas as operações em obras
- Configuração de etapas
- Relatórios completos
- Gestão de equipe

**Gerente de Obra:**
- CRUD de obras atribuídas
- Gestão de etapas e atividades
- Visualização de relatórios

**Técnico:**
- Visualização de suas obras
- Execução de atividades
- Upload de fotos
- Registro de impedimentos
- Relatório diário próprio

**Cliente (Portal):**
- Visualização de obras vinculadas
- Fotos e progresso (somente leitura)
- Relatórios resumidos

### 9.2 Permissões do Sistema

```php
$permissoes_obras = [
    'vObras'      => 'Visualizar obras',
    'cObras'      => 'Cadastrar obras',
    'eObras'      => 'Editar obras',
    'dObras'      => 'Excluir obras',
    'vTecnicoObra'=> 'Ver obras atribuídas',
    'eTecnicoExec'=> 'Executar atividades',
];
```

---

## 10. Implementação por Fases

### Fase 1: Fundação (Semana 1-2)
1. Criar migrations das novas tabelas
2. Atualizar model Obras_model
3. Criar model Obra_atividades_model
4. Criar controller Obras.php (admin)
5. Views básicas de listagem e cadastro

### Fase 2: Gestão de Etapas (Semana 3)
1. CRUD completo de etapas
2. Vinculação de atividades a etapas
3. Cálculo automático de progresso
4. Dashboard básico da obra

### Fase 3: Portal do Técnico (Semana 4-5)
1. Controller Obras_tecnico.php
2. Telas de execução de atividades
3. Sistema de check-in/out
4. Upload de fotos

### Fase 4: Impedimentos e Relatórios (Semana 6)
1. Sistema de registro de impedimentos
2. Relatório diário de obra (RDO)
3. Relatórios de progresso
4. Exportação para PDF/Excel

### Fase 5: Polimento e API (Semana 7)
1. Ajustes de UX/UI
2. Criação da API REST
3. Otimizações de performance
4. Documentação completa

### Fase 6: Mobile e Integrações (Semana 8)
1. PWA para técnicos
2. Notificações push
3. Integração com OS existentes
4. Testes e ajustes finais

---

## 11. Estrutura de Arquivos

```
application/
├── controllers/
│   ├── Obras.php
│   ├── Obras_tecnico.php
│   └── api/
│       └── Obras_api.php
├── models/
│   ├── Obras_model.php (atualizado)
│   ├── Obra_etapas_model.php
│   ├── Obra_atividades_model.php (novo)
│   └── Obra_checkins_model.php (novo)
└── views/
    ├── obras/
    │   ├── obras_list.php
    │   ├── obra_form.php
    │   ├── obra_view.php
    │   ├── etapas_list.php
    │   ├── atividades_list.php
    │   └── relatorio_diario.php
    └── obras_tecnico/
        ├── minhas_obras.php
        ├── obra_dashboard.php
        ├── atividade_execucao.php
        ├── checkin.php
        ├── impedimento.php
        └── fotos_galeria.php

assets/
├── js/
│   └── obras/
│       ├── obra_dashboard.js
│       ├── atividades.js
│       └── checkin.js
└── css/
    └── obras/
        └── obras.css

database/migrations/
├── 20260420000001_add_obra_atividades.php
└── 20260420000002_add_obra_checkins.php
```

---

## 12. Considerações Técnicas

### 12.1 Backward Compatibility
- Todas as tabelas novas são opt-in
- Sistema funciona sem as novas tabelas
- Migração de dados gradual

### 12.2 Performance
- Índices em todas as foreign keys
- Paginação em todas as listagens
- Cache de fotos (CDN ready)
- Lazy loading de imagens

### 12.3 Segurança
- Validação de geolocalização (raio configurável)
- Rate limiting na API
- Validação de MIME types em uploads
- Sanitização de inputs

### 12.4 Backup e Arquivamento
- Fotos armazenadas em estrutura de diretórios por data
- Rotina de arquivamento de atividades antigas (>2 anos)
- Exportação de relatórios em múltiplos formatos

---

## 13. Configurações do Sistema

**Adicionar em Configurações:**
```php
// Raio máximo para check-in (metros)
$obra_checkin_raio = 500;

// Horário mínimo de permanência (minutos)
$obra_tempo_minimo = 30;

// Tamanho máximo de fotos (MB)
$obra_foto_tamanho_max = 10;

// Qualidade de compressão de fotos (%)
$obra_foto_qualidade = 85;

// Horário padrão de trabalho
$obra_horario_inicio = '08:00';
$obra_horario_fim = '17:00';

// Dias úteis
$obra_dias_uteis = [1, 2, 3, 4, 5]; // Seg a Sex
```

---

## 14. Métricas e KPIs

**Dashboard Gerencial:**
- Taxa de cumprimento de prazo (%)
- Média de horas por atividade
- Número de impedimentos por tipo
- Produtividade por técnico (atividades/dia)
- Adesão ao check-in/out (%)
- Tempo médio de resolução de impedimentos

---

**Documento criado em:** Abril/2026  
**Versão:** 1.0  
**Autor:** Sistema MapOS  
**Status:** Especificação Técnica para Implementação
