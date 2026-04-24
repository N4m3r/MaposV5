# Projeto MAPOS Mobile - Versão Android APK

## Visão Geral

Este documento descreve o desenvolvimento da versão mobile do sistema MAPOS, uma aplicação Android (APK) que permitirá aos técnicos acessarem e executarem suas atividades de forma nativa no smartphone.

---

## 1. Arquitetura do Projeto

### 1.1 Estrutura
```
mobile/
├── docs/                    # Documentação
├── src/                     # Código fonte do app
│   ├── android/            # Projeto Android Nativo
│   ├── components/         # Componentes reutilizáveis
│   ├── screens/            # Telas do app
│   ├── services/           # Serviços de API
│   ├── utils/              # Utilitários
│   └── assets/             # Imagens, fontes, etc
├── api-testing/            # Testes da API
└── assets/                 # Recursos compartilhados
```

### 1.2 Tecnologias Recomendadas

| Componente | Tecnologia | Motivo |
|------------|------------|--------|
| Framework | **Flutter** ou **React Native** | Desenvolvimento híbrido, código único para Android/iOS |
| API Backend | REST/CodeIgniter | Aproveitar o backend existente |
| Autenticação | JWT (JSON Web Token) | Segurança e sessões |
| Offline | SQLite/SharedPreferences | Funcionamento offline com sync |
| Push Notifications | Firebase Cloud Messaging | Notificações em tempo real |

---

## 2. Funcionalidades do MVP (Mínimo Produto Viável)

### 2.1 Módulo de Autenticação
- [ ] Login com técnico (email/senha)
- [ ] Lembrar credenciais (biometria)
- [ ] Logout seguro
- [ ] Recuperação de senha

### 2.2 Módulo de Obras
- [ ] Listar minhas obras
- [ ] Visualizar detalhes da obra
- [ ] Ver etapas e progresso
- [ ] Mapa com localização da obra (GPS)

### 2.3 Módulo de Atividades
- [ ] Listar atividades do dia
- [ ] Iniciar atividade (com timer)
- [ ] Pausar/Continuar atividade
- [ ] Finalizar atividade
- [ ] Registrar fotos (câmera nativa)
- [ ] Assinatura digital do cliente
- [ ] Adicionar materiais utilizados
- [ ] Check-in/Check-out com geolocalização

### 2.4 Módulo de Offline
- [ ] Salvar dados localmente quando sem internet
- [ ] Sincronização automática quando online
- [ ] Indicador de status de conexão

---

## 3. APIs Necessárias no Backend (CodeIgniter)

### 3.1 Endpoints de Autenticação
```
POST   /api/auth/login              # Login do técnico
POST   /api/auth/logout             # Logout
POST   /api/auth/refresh            # Renovar token
POST   /api/auth/recover            # Recuperar senha
```

### 3.2 Endpoints de Obras
```
GET    /api/obras                   # Listar obras do técnico
GET    /api/obras/{id}              # Detalhes da obra
GET    /api/obras/{id}/etapas       # Etapas da obra
GET    /api/obras/{id}/atividades  # Atividades da obra
```

### 3.3 Endpoints de Atividades
```
POST   /api/atividades              # Criar nova atividade
GET    /api/atividades/{id}         # Detalhes da atividade
PUT    /api/atividades/{id}         # Atualizar atividade
POST   /api/atividades/{id}/iniciar    # Iniciar execução
POST   /api/atividades/{id}/pausar     # Pausar execução
POST   /api/atividades/{id}/retomar    # Retomar execução
POST   /api/atividades/{id}/finalizar  # Finalizar execução
POST   /api/atividades/{id}/foto        # Upload de foto
POST   /api/atividades/{id}/assinatura  # Salvar assinatura
POST   /api/atividades/{id}/materiais  # Registrar materiais
```

### 3.4 Endpoints de Sincronização
```
GET    /api/sync/pending          # Dados pendentes do servidor
POST   /api/sync/push             # Enviar dados do mobile
GET    /api/sync/status           # Status da sincronização
```

---

## 4. Telas do Aplicativo

### 4.1 Fluxo de Navegação
```
Login → Dashboard → Lista de Obras → Detalhe da Obra → 
→ Lista de Atividades → Execução da Atividade → Finalização
```

### 4.2 Descrição das Telas

#### Tela 1: Login
- Logo do sistema
- Campos: Email, Senha
- Botão: Entrar
- Link: Esqueci minha senha
- Switch: Lembrar-me

#### Tela 2: Dashboard
- Resumo do dia
- Obras em andamento
- Atividades pendentes
- Botão de sincronização
- Menu inferior (Home, Obras, Perfil)

#### Tela 3: Lista de Obras
- Cards com: Nome da obra, Cliente, Progresso%
- Filtro: Em andamento, Concluídas, Todas
- Busca por nome
- Pull-to-refresh

#### Tela 4: Detalhes da Obra
- Informações da obra
- Lista de etapas
- Mapa com localização
- Botão: Ver Atividades

#### Tela 5: Execução de Atividade
- Timer/cronômetro em tempo real
- Botões: Pausar, Finalizar
- Acesso à câmera para fotos
- Checklist de tarefas
- Botão de assinatura

---

## 5. Requisitos Técnicos

### 5.1 Requisitos do Android
- **Versão mínima:** Android 7.0 (API 24)
- **Versão alvo:** Android 13+ (API 33)
- **Permissões necessárias:**
  - Internet
  - Câmera
  - Localização (GPS)
  - Armazenamento
  - Biometria (opcional)

### 5.2 Requisitos do Backend
- HTTPS obrigatório (SSL)
- CORS configurado para mobile
- Rate limiting nas APIs
- Validação de token JWT
- Upload de arquivos (fotos)

---

## 6. Instalação e Configuração

### 6.1 Pré-requisitos
```bash
# Node.js (para React Native)
npm install -g react-native-cli

# OU Flutter
# Download em: https://flutter.dev

# Android Studio
# SDK Android
```

### 6.2 Dependências do Projeto
```bash
# React Native
npm install @react-navigation/native
npm install @react-navigation/stack
npm install axios
npm install @react-native-async-storage/async-storage
npm install react-native-camera
npm install react-native-geolocation-service
npm install react-native-vector-icons
npm install react-native-signature-canvas

# Flutter (pubspec.yaml)
# dependencies:
#   http: ^1.1.0
#   shared_preferences: ^2.2.2
#   camera: ^0.10.5+4
#   geolocator: ^10.1.0
#   sqflite: ^2.3.0
```

---

## 7. Configuração do Ambiente de Desenvolvimento

### 7.1 Hot Reload / Live Reload

#### Para React Native:
```bash
# Iniciar Metro Bundler com hot reload
npx react-native start

# Instalar React Native Debugger (opcional)
npm install -g react-native-debugger

# Extensão VSCode: React Native Tools
```

#### Para Flutter:
```bash
# Hot reload automático ao salvar
flutter run

# Ou extensão Flutter no VSCode
```

### 7.2 Plugin de Live Reload para CodeIgniter (Backend)

#### Instalação do Live Server para PHP:
```bash
# Usando Composer
composer require --dev php-live-reload/live-reload

# OU usar extensão VSCode: PHP Server
# Nome: "PHP Server" por brapifra
```

#### Configuração do BrowserSync (alternativa):
```bash
# Instalar Node.js e BrowserSync
npm install -g browser-sync

# Comando para rodar
cd application/views
browser-sync start --proxy "http://localhost/MaposV5" --files "**/*"
```

---

## 8. Cronograma de Desenvolvimento

### Fase 1: Setup e API (Semana 1-2)
- [ ] Configurar ambiente mobile
- [ ] Criar estrutura de APIs no CodeIgniter
- [ ] Implementar autenticação JWT
- [ ] Testar endpoints com Postman/Insomnia

### Fase 2: Interface Básica (Semana 3-4)
- [ ] Tela de Login
- [ ] Tela de Dashboard
- [ ] Navegação entre telas
- [ ] Integração com API de autenticação

### Fase 3: Módulo de Obras (Semana 5-6)
- [ ] Lista de obras
- [ ] Detalhes da obra
- [ ] Mapa com localização
- [ ] Cache offline

### Fase 4: Módulo de Atividades (Semana 7-9)
- [ ] Execução de atividades
- [ ] Timer/cronômetro
- [ ] Câmera e fotos
- [ ] Assinatura digital

### Fase 5: Offline e Sync (Semana 10-11)
- [ ] Persistência local
- [ ] Sincronização de dados
- [ ] Testes offline/online

### Fase 6: Build e Publicação (Semana 12)
- [ ] Gerar APK
- [ ] Assinar APK
- [ ] Publicar na Play Store (opcional)

---

## 9. Configuração do Plugin Live Reload (Instalação Automática)

Um script foi criado para facilitar a instalação dos plugins de desenvolvimento.

### 9.1 Instalação Rápida
```bash
# Windows (PowerShell)
.\mobile\scripts\install-live-reload.ps1

# Linux/Mac
bash ./mobile/scripts/install-live-reload.sh
```

### 9.2 Plugins Instalados
- **PHP Live Server:** Recarrega o navegador ao salvar arquivos PHP
- **BrowserSync:** Sincroniza navegadores em tempo real
- **Nodemon:** Monitora alterações no backend Node.js (se houver)

### 9.3 Uso
```bash
# Iniciar live reload
cd mobile
npm run dev

# OU individualmente
npm run watch:php    # Apenas PHP/CodeIgniter
npm run watch:mobile # Apenas React Native/Flutter
```

---

## 10. Considerações de Segurança

- [ ] Usar HTTPS em produção
- [ ] Validar todos os inputs no backend
- [ ] Implementar refresh token
- [ ] Criptografar dados sensíveis no storage local
- [ ] Validar assinatura do APK
- [ ] Proteção contra screenshot (opcional)
- [ ] Timeout de sessão

---

## 11. Recursos Úteis

### Documentação
- [React Native](https://reactnative.dev/)
- [Flutter](https://flutter.dev/)
- [CodeIgniter REST](https://github.com/chriskacerguis/codeigniter-restserver)
- [JWT PHP](https://github.com/firebase/php-jwt)

### Ferramentas
- Postman (teste de API)
- Android Studio (emulador)
- VSCode (desenvolvimento)

---

**Data de criação:** 2026-04-24
**Versão:** 1.0
**Responsável:** Desenvolvimento MAPOS
