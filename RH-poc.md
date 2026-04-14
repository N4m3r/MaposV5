# Sistema de RH - Prova de Conceito (PoC)

## Mapos OS - Módulo de Recursos Humanos

---

## 1. Visão Geral

O sistema de RH será integrado ao Mapos OS existente, permitindo gestão completa de colaboradores, folha de pagamento simplificada, controle de ponto e férias.

### Público-alvo
- Micro e pequenas empresas que usam o Mapos OS
- Comércios de materiais de construção (CNAE 4751201)
- Prestadores de serviços técnicos

---

## 2. Funcionalidades Principais

### 2.1 Cadastro de Funcionários
- Dados pessoais e profissionais
- Documentos (RG, CPF, CTPS, PIS/PASEP)
- Dados bancários
- Cargo e departamento
- Salário e benefícios
- Data de admissão/demissão
- Foto do funcionário

### 2.2 Controle de Ponto
- Registro de entrada/saída
- Batida de ponto manual (admin)
- Ajustes de ponto
- Relatório de horas trabalhadas
- Cálculo de horas extras
- Controle de atrasos e faltas

### 2.3 Férias e Folgas
- Cadastro de período aquisitivo
- Solicitação de férias
- Aprovação/reprovação
- Controle de dias disponíveis
- Abono pecuniário
- Relatório de férias programadas

### 2.4 Folha de Pagamento (Simplificada)
- Cálculo de salário mensal
- Lançamento de proventos e descontos
- Adiantamento salarial (vale)
- Vale-transporte
- Vale-refeição
- INSS e IRRF simplificados
- Geração de recibo de pagamento

### 2.5 Documentos e Contratos
- Upload de contratos
- Gerenciamento de exames admissionais/periódicos
- Aviso de férias (PDF)
- Termos de rescisão
- Atestados médicos

### 2.6 Desempenho e Avaliações
- Avaliação de desempenho trimestral/anual
- Registro de advertências e suspensões
- Metas e objetivos
- Histórico de cargos e salários

### 2.7 Dashboard RH
- Total de funcionários ativos
- Aniversariantes do mês
- Férias programadas
- Ponto pendente de fechamento
- Exames vencendo
- Custos com folha de pagamento

---

## 3. Estrutura de Banco de Dados

### 3.1 Tabelas Principais

```sql
-- Tabela: funcionarios
CREATE TABLE funcionarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    matricula VARCHAR(20) UNIQUE,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) UNIQUE,
    rg VARCHAR(20),
    data_nascimento DATE,
    sexo ENUM('M','F'),
    estado_civil ENUM('Solteiro','Casado','Divorciado','Viuvo'),
    
    -- Endereço
    endereco TEXT,
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(9),
    
    -- Contato
    telefone VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(255),
    
    -- Documentos trabalhistas
    pis_pasep VARCHAR(20),
    ctps_numero VARCHAR(20),
    ctps_serie VARCHAR(10),
    ctps_uf VARCHAR(2),
    
    -- Dados bancários
    banco_codigo VARCHAR(3),
    banco_nome VARCHAR(100),
    agencia VARCHAR(10),
    conta VARCHAR(20),
    tipo_conta ENUM('Corrente','Poupanca'),
    
    -- Dados profissionais
    cargo_id INT,
    departamento_id INT,
    tipo_contratacao ENUM('CLT','PJ','Estagio','Temporario'),
    data_admissao DATE,
    data_demissao DATE,
    salario_bruto DECIMAL(15,2),
    carga_horaria_semanal INT DEFAULT 44,
    
    -- Benefícios
    vale_transporte DECIMAL(10,2) DEFAULT 0,
    vale_refeicao DECIMAL(10,2) DEFAULT 0,
    plano_saude DECIMAL(10,2) DEFAULT 0,
    
    -- Status
    status ENUM('Ativo','Ferias','Afastado','Demitido') DEFAULT 'Ativo',
    
    -- Arquivos
    foto VARCHAR(255),
    
    -- Controle
    created_at DATETIME,
    updated_at DATETIME,
    usuario_cadastro_id INT,
    
    FOREIGN KEY (cargo_id) REFERENCES cargos(id),
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
);

-- Tabela: cargos
CREATE TABLE cargos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    departamento_id INT,
    salario_base DECIMAL(15,2),
    carga_horaria INT DEFAULT 44,
    nivel_hierarquico INT DEFAULT 1,
    ativo TINYINT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);

-- Tabela: departamentos
CREATE TABLE departamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    responsavel_id INT,
    centro_custo VARCHAR(50),
    ativo TINYINT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);

-- Tabela: registro_ponto
CREATE TABLE registro_ponto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funcionario_id INT NOT NULL,
    data DATE NOT NULL,
    hora_entrada TIME,
    hora_saida_almoco TIME,
    hora_retorno_almoco TIME,
    hora_saida TIME,
    
    -- Horas calculadas
    horas_normais DECIMAL(5,2) DEFAULT 0,
    horas_extras DECIMAL(5,2) DEFAULT 0,
    horas_faltas DECIMAL(5,2) DEFAULT 0,
    
    -- Justificativas
    justificativa TEXT,
    tipo_registro ENUM('Normal','Falta','FaltaJustificada','Ferias','Feriado','HomeOffice'),
    
    -- Controle
    aprovado TINYINT DEFAULT 0,
    aprovado_por INT,
    data_aprovacao DATETIME,
    observacoes TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id),
    UNIQUE KEY uk_ponto_dia (funcionario_id, data)
);

-- Tabela: ferias
CREATE TABLE ferias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funcionario_id INT NOT NULL,
    
    -- Período aquisitivo
    periodo_inicio DATE NOT NULL,
    periodo_fim DATE NOT NULL,
    
    -- Período de gozo
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    dias_ferias INT NOT NULL,
    dias_abono INT DEFAULT 0,
    
    -- Valores
    valor_ferias DECIMAL(15,2),
    valor_terco DECIMAL(15,2),
    valor_abono DECIMAL(15,2),
    desconto_inss DECIMAL(15,2),
    desconto_irrf DECIMAL(15,2),
    valor_liquido DECIMAL(15,2),
    
    -- Status
    status ENUM('Solicitada','Aprovada','Reprovada','EmGozo','Finalizada') DEFAULT 'Solicitada',
    
    -- Aprovação
    solicitada_em DATETIME,
    aprovada_por INT,
    aprovada_em DATETIME,
    observacoes TEXT,
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Tabela: folha_pagamento
CREATE TABLE folha_pagamento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    competencia VARCHAR(7) NOT NULL, -- formato: YYYY-MM
    funcionario_id INT NOT NULL,
    
    -- Dados do mês
    dias_trabalhados INT DEFAULT 30,
    horas_normais DECIMAL(5,2),
    horas_extras DECIMAL(5,2),
    horas_faltas DECIMAL(5,2),
    
    -- Proventos
    salario_base DECIMAL(15,2),
    horas_extras_valor DECIMAL(15,2),
    adicional_noturno DECIMAL(15,2),
    adicional_insalubridade DECIMAL(15,2),
    adicional_periculosidade DECIMAL(15,2),
    adicional_tempo_servico DECIMAL(15,2),
    bonificacoes DECIMAL(15,2),
    total_proventos DECIMAL(15,2),
    
    -- Descontos
    inss_valor DECIMAL(15,2),
    inss_aliquota DECIMAL(5,2),
    irrf_valor DECIMAL(15,2),
    irrf_aliquota DECIMAL(5,2),
    vale_transporte DECIMAL(15,2),
    vale_refeicao DECIMAL(15,2),
    plano_saude DECIMAL(15,2),
    adiantamento DECIMAL(15,2),
    faltas_desconto DECIMAL(15,2),
    outros_descontos DECIMAL(15,2),
    total_descontos DECIMAL(15,2),
    
    -- Totais
    salario_liquido DECIMAL(15,2),
    
    -- Status
    status ENUM('Aberta','Fechada','Paga') DEFAULT 'Aberta',
    data_pagamento DATE,
    
    -- Controle
    created_at DATETIME,
    updated_at DATETIME,
    fechada_por INT,
    data_fechamento DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id),
    UNIQUE KEY uk_folha_competencia (funcionario_id, competencia)
);

-- Tabela: eventos_folha (proventos/descontos variáveis)
CREATE TABLE eventos_folha (
    id INT PRIMARY KEY AUTO_INCREMENT,
    competencia VARCHAR(7),
    funcionario_id INT,
    tipo ENUM('Provento','Desconto') NOT NULL,
    codigo VARCHAR(20), -- código do evento
    descricao VARCHAR(255),
    valor DECIMAL(15,2),
    referencia DECIMAL(10,2), -- quantidade/horas
    incluir_na_folha TINYINT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Tabela: documentos_funcionario
CREATE TABLE documentos_funcionario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funcionario_id INT NOT NULL,
    tipo_documento ENUM('Contrato','ExameAdmissional','ExameDemissional','ExamePeriodico','Atestado','TermoRescisao','Advertencia','Suspensao','Outro'),
    titulo VARCHAR(255),
    arquivo VARCHAR(255),
    data_documento DATE,
    data_validade DATE,
    observacoes TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Tabela: avaliacoes_desempenho
CREATE TABLE avaliacoes_desempenho (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funcionario_id INT NOT NULL,
    avaliador_id INT,
    periodo_inicio DATE,
    periodo_fim DATE,
    data_avaliacao DATE,
    
    -- Notas (escala 1-5)
    nota_produtividade DECIMAL(3,1),
    nota_qualidade DECIMAL(3,1),
    nota_assiduidade DECIMAL(3,1),
    nota_relacionamento DECIMAL(3,1),
    nota_iniciativa DECIMAL(3,1),
    nota_final DECIMAL(3,1),
    
    -- Feedback
    pontos_fortes TEXT,
    pontos_melhoria TEXT,
    metas_prox_periodo TEXT,
    comentarios TEXT,
    
    -- Plano de desenvolvimento
    necessita_treinamento TINYINT DEFAULT 0,
    descricao_treinamento TEXT,
    
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Tabela: ocorrencias_rh
CREATE TABLE ocorrencias_rh (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funcionario_id INT NOT NULL,
    tipo ENUM('Advertencia','Suspensao','Treinamento','Promocao','MudancaCargo','MudancaSalario','Afastamento','Outro'),
    data_ocorrencia DATE,
    descricao TEXT,
    gravidade ENUM('Baixa','Media','Alta'),
    dias_suspensao INT DEFAULT 0,
    aplicado_por INT,
    arquivo_anexo VARCHAR(255),
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Tabela: parametros_rh (configurações)
CREATE TABLE parametros_rh (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chave VARCHAR(100) UNIQUE,
    valor TEXT,
    descricao VARCHAR(255),
    updated_at DATETIME
);

-- Dados iniciais para tabela parametros_rh
INSERT INTO parametros_rh (chave, valor, descricao) VALUES
('RH_CARGA_HORARIA_PADRAO', '44', 'Carga horária semanal padrão'),
('RH_HORA_EXTRA_MINIMA', '2', 'Mínimo de horas extras para pagamento'),
('RH_INTERVALO_ALMOCO', '60', 'Duração do intervalo de almoço em minutos'),
('RH_FERIAS_MINIMO_DIAS', '6', 'Mínimo de dias para férias'),
('RH_ADIANTAMENTO_PERC', '40', 'Percentual máximo de adiantamento salarial'),
('RH_INSS_TETO', '908.85', 'Teto do INSS (atualizar anualmente)'),
('RH_SALARIO_MINIMO', '1412.00', 'Salário mínimo vigente'),
('RH_FERIAS_PERC_ABONO', '33.33', 'Percentual para cálculo do abono pecuniário');
```

---

## 4. Estrutura de Arquivos (MVC)

```
application/
├── controllers/
│   └── Rh.php                    # Controller principal do RH
├── models/
│   ├── Rh_model.php              # Model principal
│   ├── Funcionarios_model.php    # Gestão de funcionários
│   ├── Ponto_model.php           # Controle de ponto
│   ├── Ferias_model.php          # Gestão de férias
│   ├── Folha_model.php           # Folha de pagamento
│   └── Documentos_rh_model.php   # Documentos
├── views/
│   └── rh/
│       ├── dashboard.php         # Painel principal
│       ├── funcionarios/
│       │   ├── listar.php
│       │   ├── form.php
│       │   └── visualizar.php
│       ├── ponto/
│       │   ├── registro.php
│       │   ├── relatorio.php
│       │   └── ajustes.php
│       ├── ferias/
│       │   ├── listar.php
│       │   ├── form.php
│       │   └── calendario.php
│       ├── folha/
│       │   ├── listar.php
│       │   ├── fechamento.php
│       │   └── recibo.php
│       ├── cargos/
│       │   ├── listar.php
│       │   └── form.php
│       ├── departamentos/
│       │   ├── listar.php
│       │   └── form.php
│       ├── documentos/
│       │   └── gerenciar.php
│       └── avaliacoes/
│           ├── listar.php
│           └── form.php
└── database/
    └── migrations/
        ├── 009_create_rh_tables.php
        └── 010_seed_rh_dados.php
```

---

## 5. Permissões Sugeridas

```php
// Adicionar no sistema de permissões
$permissoes_rh = [
    'vRH' => 'Visualizar módulo RH',
    'aRHFuncionarios' => 'Acessar funcionários',
    'cRHFuncionarios' => 'Cadastrar funcionários',
    'eRHFuncionarios' => 'Editar funcionários',
    'dRHFuncionarios' => 'Excluir funcionários',
    'aRHPonto' => 'Acessar controle de ponto',
    'eRHPonto' => 'Editar registros de ponto',
    'aRHFerias' => 'Acessar férias',
    'cRHFerias' => 'Cadastrar férias',
    'aRHFolha' => 'Acessar folha de pagamento',
    'eRHFolha' => 'Fechar folha de pagamento',
    'vRHFolhaOutros' => 'Ver folha de outros funcionários',
    'aRHCargos' => 'Gerenciar cargos',
    'aRHDepartamentos' => 'Gerenciar departamentos',
    'aRHDocumentos' => 'Acessar documentos',
    'aRHAvaliacoes' => 'Acessar avaliações',
    'aRHRelatorios' => 'Acessar relatórios',
    'cRHOcorrencias' => 'Cadastrar ocorrências',
];
```

---

## 6. API Endpoints (para futuras integrações)

```php
// routes.php
$route['api/rh/funcionarios'] = 'rh/api_funcionarios';
$route['api/rh/ponto/(:num)'] = 'rh/api_ponto/$1';
$route['api/rh/ferias/(:num)'] = 'rh/api_ferias/$1';
$route['api/rh/folha/(:any)/(:num)'] = 'rh/api_folha/$1/$2';
```

---

## 7. Integrações com Módulos Existentes

### 7.1 Integração com DRE
- Lançar salários e encargos no DRE automaticamente
- Vincular departamentos a centros de custo do DRE
- Relatório de custos com pessoal no DRE

### 7.2 Integração com OS
- Vincular funcionário técnico à OS
- Comissão por OS vendida/executada
- Controle de produção por funcionário

### 7.3 Integração com Vendas
- Comissão por venda
- Meta de vendas por vendedor
- Relatório de performance

### 7.4 Integração com Financeiro
- Gerar lançamento de folha de pagamento
- Controle de adiantamentos salariais
- Provisão de férias e 13º

---

## 8. Dashboards e Relatórios

### 8.1 Dashboard Principal
- Total de funcionários ativos
- Turnover (taxa de demissão/admissão)
- Custo médio por funcionário
- Férias a vencer (próximos 3 meses)
- Aniversariantes do mês
- Exames vencendo
- Ponto pendente de fechamento

### 8.2 Relatórios Disponíveis
- Funcionários ativos (PDF/Excel)
- Folha de pagamento consolidada
- Banco de horas
- Férias programadas
- Custos com pessoal
- Afastamentos e licenças
- Histórico salarial

---

## 9. Regras de Negócio

### 9.1 Cálculo de Horas Extras
- Acima de 40h/semana: 50% de acréscimo
- Acima de 2h extras/dia: 100% de acréscimo
- Domingos e feriados: 100% de acréscimo

### 9.2 Férias
- Período aquisitivo: 12 meses
- Direito: 30 dias
- Mínimo para gozo: 6 dias
- Vencimento: 12 meses após período aquisitivo

### 9.3 Descontos
- Vale-transporte: até 6% do salário
- INSS: tabela progressiva (atualizar anualmente)
- IRRF: tabela mensal (atualizar anualmente)

### 9.4 Ponto
- Tolerância de 10 minutos
- Intervalo mínimo de 1h para almoço
- Jornada máxima: 8h/dia ou 44h/semana

---

## 10. Considerações Técnicas

### 10.1 Performance
- Indexar campos de busca frequentes (cpf, nome, matrícula)
- Particionar tabela de ponto por ano/mês
- Cache de relatórios pesados

### 10.2 Segurança
- Criptografar CPF e dados bancários
- Log de acessos a dados sensíveis
- Restrição de acesso aos próprios dados (exceto RH)
- Backup automático de documentos

### 10.3 Compliance
- LGPD: consentimento para dados sensíveis
- Retenção de dados: 5 anos após demissão
- Auditoria de alterações salariais

---

## 11. Cronograma Sugerido

### Fase 1 - Cadastros Básicos (2 semanas)
- [ ] Criar tabelas no banco
- [ ] CRUD de departamentos
- [ ] CRUD de cargos
- [ ] CRUD de funcionários
- [ ] Permissões

### Fase 2 - Ponto e Férias (2 semanas)
- [ ] Registro de ponto
- [ ] Relatório de ponto
- [ ] Solicitação de férias
- [ ] Calendário de férias

### Fase 3 - Folha de Pagamento (2 semanas)
- [ ] Estrutura da folha
- [ ] Cálculo de proventos/descontos
- [ ] Fechamento da folha
- [ ] Emissão de recibos

### Fase 4 - Documentos e Relatórios (1 semana)
- [ ] Upload de documentos
- [ ] Alertas de vencimento
- [ ] Relatórios diversos
- [ ] Dashboard

### Fase 5 - Integrações (1 semana)
- [ ] Integração com DRE
- [ ] Integração com OS
- [ ] Integração com Financeiro

---

## 12. Portal do Técnico/Funcionário (Self-Service)

Aplicativo/mobile para o técnico/funcionário acessar suas informações e realizar operações de RH de forma autônoma.

### 12.1 Login do Técnico
- **Autenticação:** CPF + senha ou Matrícula + senha
- **Autenticação biométrica:** Reconhecimento facial (opcional)
- **Token JWT:** Para manter sessão segura no app
- **Permissões restritas:** Apenas próprios dados

### 12.2 Batida de Ponto Avançada
**É POSSÍVEL SIM!** Recursos implementáveis:

#### 12.2.1 Captura de Foto
- Tirar foto no momento da batida (câmera frontal/obrigatória)
- Armazenar imagem para conferência posterior
- Comparar com foto cadastrada (reconhecimento facial)
- Geolocalização vinculada à foto

#### 12.2.2 Geolocalização (GPS)
```javascript
// Exemplo de implementação
navigator.geolocation.getCurrentPosition(
    (position) => {
        latitude = position.coords.latitude;
        longitude = position.coords.longitude;
        accuracy = position.coords.accuracy;
        
        // Validar se está no raio permitido (ex: 500m da empresa)
        validarLocalizacao(latitude, longitude);
    }
);
```
- Capturar latitude/longitude
- Validar raio de distância da empresa (configurável)
- Registrar endereço aproximado
- Alerta se fora da área permitida

#### 12.2.3 Reconhecimento Facial (Biometria)
**Tecnologias possíveis:**
- **Face-api.js** (JavaScript/browser) - Gratuito
- **Amazon Rekognition** - Pago, mais preciso
- **Google Vision API** - Pago
- **Azure Face API** - Pago

**Implementação básica (Face-api.js):**
```javascript
// Carregar modelos de reconhecimento facial
await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

// Capturar foto e comparar
const detection = await faceapi.detectSingleFace(video)
    .withFaceLandmarks()
    .withFaceDescriptor();

// Comparar com foto cadastrada
const distance = faceapi.euclideanDistance(
    detection.descriptor, 
    funcionarioFaceDescriptor
);

// Match se distância < 0.6
const match = distance < 0.6;
```

**Requisitos:**
- Foto cadastrada no sistema (durante admissão)
- Iluminação adequada
- Câmera de boa qualidade
- Conexão com internet

#### 12.2.4 Fluxo de Batida de Ponto
1. Funcionário abre o app
2. Tira foto do rosto (reconhecimento facial)
3. Sistema valida identidade
4. Captura geolocalização automaticamente
5. Registra entrada/saída com dados completos
6. Exibe confirmação na tela

### 12.3 Funcionalidades do Portal

#### 12.3.1 Meu Ponto
- Visualizar registro do dia
- Histórico de ponto (últimos dias)
- Saldo de horas extras
- Banco de horas
- Justificar atraso/falta (com upload de arquivo)

#### 12.3.2 Minhas Férias
- Consultar períodos aquisitivos disponíveis
- Dias de férias disponíveis para gozo
- Solicitar férias (com calendário de seleção)
- Acompanhar status da solicitação
- Visualizar histórico de férias tiradas

#### 12.3.3 Minha Folha
- Consultar holerite/recibo de pagamento (PDF)
- Download de recibos meses anteriores
- Simulação de salário líquido
- Extrato de vales (transporte/refeição)

#### 12.3.4 Documentos
- **Anexar Atestado Médico:**
  - Upload de foto/PDF
  - Data do atestado
  - Quantidade de dias
  - CID (opcional)
  - Notificação automática para RH
- Visualizar documentos pessoais
- Exames admissionais/periódicos
- Contratos

#### 12.3.5 Solicitações
- Férias
- Afastamento
- Mudança de dados pessoais
- 2ª via de documentos
- Declarações (vínculo empregatício, etc)

#### 12.3.6 Comunicação
- Receber avisos (férias coletivas, paralisações)
- Consultar avisos anteriores
- Chat com RH (opcional)

### 12.4 Estrutura de Arquivos do Portal

```
application/
├── controllers/
│   ├── Portal_rh.php             # Controller do portal do técnico
│   └── Api_rh_mobile.php        # API para app mobile
├── models/
│   └── Portal_rh_model.php      # Model específico do portal
├── views/
│   └── portal_rh/               # Portal do técnico (responsive/mobile)
│       ├── login.php
│       ├── dashboard.php
│       ├── ponto/
│       │   ├── registrar.php    # Página com câmera + geolocalização
│       │   └── historico.php
│       ├── ferias/
│       │   ├── consultar.php
│       │   └── solicitar.php
│       ├── folha/
│       │   └── recibos.php
│       ├── documentos/
│       │   └── atestado.php     # Upload de atestado
│       └── perfil/
│           └── dados.php
└── database/
    └── migrations/
        └── 011_add_portal_rh.php
```

### 12.5 Campos Adicionais no Banco

```sql
-- Tabela: registro_ponto (campos adicionais)
ALTER TABLE registro_ponto ADD COLUMN (
    foto_batida VARCHAR(255),          -- URL da foto tirada
    latitude DECIMAL(10,8),             -- Latitude do GPS
    longitude DECIMAL(11,8),           -- Longitude do GPS
    endereco_localizacao VARCHAR(255), -- Endereço formatado
    distancia_metros INT,              -- Distância da empresa em metros
    reconhecimento_facial TINYINT DEFAULT 0, -- 1=validado, 0=não usado
    face_match_score DECIMAL(5,2),     -- Score de similaridade (0-1)
    dispositivo VARCHAR(100),          -- Modelo do celular/browser
    ip_address VARCHAR(45)              -- IP do dispositivo
);

-- Tabela: funcionarios (campos adicionais para biometria)
ALTER TABLE funcionarios ADD COLUMN (
    senha_portal VARCHAR(255),         -- Senha específica do portal
    foto_biometria VARCHAR(255),      -- Foto de referência para reconhecimento
    face_descriptor TEXT,             -- Descrição facial (vetor de características)
    token_acesso VARCHAR(255),        -- Token para app mobile
    token_expira DATETIME,            -- Expiração do token
    ultimo_acesso DATETIME,           -- Último login no portal
    app_instalado TINYINT DEFAULT 0   -- Se já baixou o app
);

-- Tabela: atestados (nova)
CREATE TABLE atestados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    funcionario_id INT NOT NULL,
    data_atestado DATE NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    quantidade_dias INT NOT NULL,
    cid VARCHAR(10),
    arquivo VARCHAR(255),
    observacao TEXT,
    status ENUM('Pendente','Aprovado','Rejeitado') DEFAULT 'Pendente',
    aprovado_por INT,
    data_aprovacao DATETIME,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- Tabela: configuracoes_portal
CREATE TABLE configuracoes_portal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chave VARCHAR(100) UNIQUE,
    valor TEXT,
    updated_at DATETIME
);

INSERT INTO configuracoes_portal (chave, valor) VALUES
('PORTAL_RAIO_MAXIMO_METROS', '500', 'Raio máximo para batida de ponto'),
('PORTAL_RECONHECIMENTO_FACIAL', '1', 'Exigir reconhecimento facial'),
('PORTAL_FOTO_OBRIGATORIA', '1', 'Exigir foto na batida'),
('PORTAL_INTERVALO_MINIMO_MINUTOS', '60', 'Intervalo mínimo entre batidas'),
('PORTAL_FLEXIBILIDADE_HORARIO', '10', 'Tolerância em minutos');
```

### 12.6 API Mobile Endpoints

```php
// routes.php - API para app mobile
$route['api/portal/login'] = 'api_rh_mobile/login';
$route['api/portal/logout'] = 'api_rh_mobile/logout';
$route['api/portal/validar-token'] = 'api_rh_mobile/validar_token';

$route['api/portal/ponto/registrar'] = 'api_rh_mobile/registrar_ponto';
$route['api/portal/ponto/hoje'] = 'api_rh_mobile/ponto_hoje';
$route['api/portal/ponto/mes/(:any)'] = 'api_rh_mobile/ponto_mes/$1';

$route['api/portal/ferias/saldo'] = 'api_rh_mobile/saldo_ferias';
$route['api/portal/ferias/solicitar'] = 'api_rh_mobile/solicitar_ferias';

$route['api/portal/folha/recibos'] = 'api_rh_mobile/listar_recibos';
$route['api/portal/folha/recibo/(:any)'] = 'api_rh_mobile/baixar_recibo/$1';

$route['api/portal/atestado/upload'] = 'api_rh_mobile/upload_atestado';
$route['api/portal/documentos'] = 'api_rh_mobile/listar_documentos';
```

### 12.7 Tecnologias Recomendadas

#### Para Reconhecimento Facial:
| Tecnologia | Tipo | Preço | Precisão |
|------------|------|-------|----------|
| **Face-api.js** | Client-side | Grátis | Boa |
| **Amazon Rekognition** | Cloud | Pago | Excelente |
| **Azure Face API** | Cloud | Pago | Excelente |
| **Google Vision** | Cloud | Pago | Excelente |

#### Para Geolocalização:
- **HTML5 Geolocation API** - Nativo do browser
- **Google Maps Geocoding API** - Para converter coordenadas em endereço
- **OpenStreetMap Nominatim** - Gratuito para geocoding reverso

#### Para App Mobile:
- **PWA (Progressive Web App)** - Mais barato, funciona no browser
- **Flutter** - App nativo para Android/iOS
- **React Native** - App nativo híbrido
- **Ionic** - App híbrido com tecnologias web

### 12.8 Segurança do Portal

- **HTTPS obrigatório** em todas as requisições
- **Rate limiting** - Limitar tentativas de login
- **Validação de token** em todas as APIs
- **Criptografia** de dados sensíveis (CPF, dados bancários)
- **LGPD compliance** - Consentimento para uso de biometria
- **Backup automático** das fotos de ponto

### 12.9 Fluxo de Implementação

#### Fase 1 - Portal Web (2 semanas)
- [ ] Login do técnico
- [ ] Batida de ponto com foto
- [ ] Geolocalização básica
- [ ] Consulta de recibos

#### Fase 2 - Reconhecimento Facial (1 semana)
- [ ] Integrar face-api.js
- [ ] Cadastrar foto de referência
- [ ] Validação facial no ponto
- [ ] Testes de precisão

#### Fase 3 - Funcionalidades Avançadas (2 semanas)
- [ ] Solicitação de férias
- [ ] Upload de atestado
- [ ] Histórico completo
- [ ] Notificações push

#### Fase 4 - App Mobile (Opcional - 3 semanas)
- [ ] Desenvolver PWA ou app nativo
- [ ] Otimizar para mobile
- [ ] Publicar nas lojas
- [ ] Testes com usuários reais

---

## 13. Observações

- Este é um sistema **simplificado** para PMEs, não substitui sistemas completos de folha
- Cálculos tributários devem ser revisados por contador
- Implementação pode ser feita por fases
- Priorizar usabilidade sobre complexidade
- **Reconhecimento facial é totalmente possível** e pode ser implementado com Face-api.js (gratuito) ou APIs pagas para maior precisão

---

**Data de criação:** 2026-04-13  
**Versão:** 1.1  
**Status:** PoC - Aguardando aprovação
