# MAPOS Mobile

Versão mobile do sistema MAPOS para Android (APK).

## 📋 Índice

- [Sobre](#sobre)
- [Instalação Rápida](#instalação-rápida)
- [Live Reload](#live-reload)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [APIs](#apis)
- [Desenvolvimento](#desenvolvimento)

## 📝 Sobre

O MAPOS Mobile é uma aplicação Android nativa que permite aos técnicos:
- Visualizar e executar atividades
- Registrar fotos e assinaturas
- Acompanhar obras em tempo real
- Funcionar offline com sincronização

## 🚀 Instalação Rápida

### Passo 1: Instalar Live Reload

**Windows (PowerShell como Administrador):**
```powershell
.\scripts\install-live-reload.ps1
```

**Linux/Mac:**
```bash
bash ./scripts/install-live-reload.sh
```

Ou manualmente:
```bash
cd mobile
npm install
```

### Passo 2: Iniciar Live Reload

```bash
npm run dev
```

Acesse: http://localhost:3000

## 🔄 Live Reload

O plugin de live reload monitora alterações nos arquivos PHP, CSS e JS e atualiza o navegador automaticamente.

### Comandos Disponíveis

| Comando | Descrição |
|---------|-----------|
| `npm run dev` | Inicia PHP + Assets simultaneamente |
| `npm run watch:php` | Monitora apenas arquivos PHP |
| `npm run watch:assets` | Monitora apenas CSS/JS |
| `npm run watch:mobile` | Futuro: React Native/Flutter |
| `npm run api:check` | Testa a API localmente |

### Extensões Recomendadas (VSCode)

1. **Live Server** (ritwickdey) - Para preview rápido
2. **PHP Server** (brapifra) - Para debug PHP
3. **ESLint** - Para linting JavaScript
4. **Prettier** - Formatação de código

## 📁 Estrutura do Projeto

```
mobile/
├── docs/                    # Documentação
│   └── PLANEJAMENTO_MOBILE.md
├── scripts/                 # Scripts de automação
│   ├── install-live-reload.ps1
│   ├── install-live-reload.sh
│   └── setup.js
├── src/                     # Código fonte (futuro)
│   ├── android/            # Projeto Android
│   ├── components/         # Componentes React/Flutter
│   ├── screens/            # Telas do app
│   ├── services/           # APIs
│   └── assets/             # Imagens, fontes
├── api-testing/            # Testes de API
├── package.json            # Config Node.js
└── README.md               # Este arquivo
```

## 🔌 APIs

O aplicativo consome APIs REST do CodeIgniter:

### Endpoints Principais

```
POST   /api/auth/login              # Login
GET    /api/obras                  # Listar obras
POST   /api/atividades/iniciar    # Iniciar atividade
POST   /api/atividades/pausar      # Pausar atividade
POST   /api/atividades/finalizar   # Finalizar atividade
```

Veja o documento `docs/PLANEJAMENTO_MOBILE.md` para lista completa.

## 💻 Desenvolvimento

### Requisitos

- Node.js 16+
- NPM ou Yarn
- PHP 7.4+
- MySQL/MariaDB

### Configuração do Backend (CodeIgniter)

Certifique-se de que:
1. O CORS está habilitado para `localhost:3000`
2. As APIs retornam JSON formatado corretamente
3. JWT está configurado

### Configuração do Frontend Mobile (React Native)

```bash
# Instalar React Native CLI
npm install -g react-native-cli

# Criar projeto (futuro)
npx react-native init MAPOSMobile
cd MAPOSMobile

# Rodar no Android
npx react-native run-android
```

## 📱 Build do APK

### Preparar Ambiente

```bash
# Instalar Android Studio
# Configurar variáveis de ambiente:
export ANDROID_HOME=$HOME/Android/Sdk
export PATH=$PATH:$ANDROID_HOME/emulator
export PATH=$PATH:$ANDROID_HOME/tools
export PATH=$PATH:$ANDROID_HOME/tools/bin
export PATH=$PATH:$ANDROID_HOME/platform-tools
```

### Gerar APK Debug

```bash
cd src/android
./gradlew assembleDebug
```

### Gerar APK Release

```bash
cd src/android
./gradlew assembleRelease
```

O APK será gerado em: `src/android/app/build/outputs/apk/release/app-release.apk`

## 🧪 Testes

### Testar API

```bash
cd mobile
npm run api:check
```

### Testes Unitários

```bash
# Futuro: npm test
```

## 📄 Documentação

- [Planejamento Completo](docs/PLANEJAMENTO_MOBILE.md)
- [API Endpoints](docs/PLANEJAMENTO_MOBILE.md#3-apis-necessárias-no-backend-codeigniter)
- [Telas do App](docs/PLANEJAMENTO_MOBILE.md#4-telas-do-aplicativo)

## 🤝 Contribuição

1. Fork o projeto
2. Crie sua branch (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📞 Suporte

Em caso de dúvidas ou problemas:
- Verifique o documento de planejamento
- Execute `npm run dev` para debug
- Consulte os logs em `mobile/logs/`

## 📜 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](../LICENSE) para mais detalhes.

---

**Versão:** 1.0.0  
**Data:** 2026-04-24  
**Autor:** Desenvolvimento MAPOS
