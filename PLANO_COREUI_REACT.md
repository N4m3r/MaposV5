# Plano: Migração para CoreUI React (template Admin) — MaposV5

**Data:** 2026-06-15
**Versão alvo:** 5.2.0 (Frontend Modernization)
**Status:** 📋 Planejamento
**Pré-requisito:** [PLANO_MELHORIAS_UX.md](PLANO_MELHORIAS_UX.md) (Fases 1-2 já concluídas)

---

## 1. Contexto e Objetivo

O MaposV5 hoje é um **CodeIgniter 3 + PHP 8.3 + Bootstrap 5** com 50 controllers, 51 models e 46+ views. As views atuais misturam PHP server-side rendering com jQuery 3.7, DataTables e SweetAlert, e já foram modernizadas no `mapos.css` consolidado (1.1k linhas, 18 blocos, 6 temas via `body[data-theme]`).

O **objetivo deste plano** é **adotar o template admin do CoreUI React** (https://coreui.io/demos/react/latest/default/) como layout unificado para todas as páginas autenticadas, **mantendo compatibilidade com hospedagem compartilhada Kinghost** (sem Node.js, sem build pipeline obrigatório).

### Restrições críticas

| Restrição | Implicação |
|-----------|------------|
| **Kinghost compartilhada** | ❌ Sem `npm install` no servidor, ❌ sem Node.js |
| **PHP 8.3 + CodeIgniter 3** | Backend PHP continua intocado |
| **MySQL existente** | Zero migração de schema |
| **Mobile portal do técnico** | Deve continuar leve (jQuery OK) |
| **Login público** | Deve ser HTML puro (React opcional) |
| **Equipe pequena** | Build local opcional, mas CDN deve ser viável |

### O que NÃO muda

- ✅ CodeIgniter 3 + PHP 8.3 (sem migração pra CI4/Laravel)
- ✅ MySQL schema (29 migrations preservadas)
- ✅ `mapos.css` consolidado (continua sendo o tema)
- ✅ Backend: controllers, models, libraries, helpers
- ✅ API REST existente (`/index.php/api/...`)

### O que muda

- 🎨 **Layout/UI:** views autenticadas migram pro template CoreUI (sidebar dark + topbar + content area)
- ⚛️ **Componentes interativos:** dashboard, kanban, tabelas complexas → React (componentes isolados)
- 📦 **Build opcional:** estrutura `assets/frontend/` com Vite, mas mantém build CDN como alternativa

---

## 2. Decisão de Arquitetura: CDN-first, Build-optional

### Por que CDN-first

| Argumento | Justificativa |
|-----------|---------------|
| **Zero deploy no servidor** | Kinghost não tem Node.js — `npm run build` precisaria rodar no PC local e ser commitado |
| **Hospedagem compartilhada** | `dist/` grande (>5MB) lota cota; CDN externo não conta |
| **Manutenção do time** | Desenvolvedor PHP não precisa aprender Vite/TypeScript pra contribuir |
| **Fallback** | Se o build quebrar, CDN continua funcionando |
| **Tempo de adoção** | Pode ser feito página por página, sem big-bang |

### Por que Build-optional depois

- ✅ Tree-shaking (bundle ~150KB vs ~3MB CDN)
- ✅ TypeScript (tipagem nos models/endpoints)
- ✅ HMR em dev (50ms vs F5 de 2s)
- ✅ Versionamento (filename hash = cache 1 ano)

### Estratégia de rollout

```
Fase 1 (CDN):     Layout base + dashboard           [2 dias]  ← FOCO IMEDIATO
Fase 2 (CDN):     Kanban + Clientes                 [3 dias]
Fase 3 (CDN):     OS + Vendas + Financeiro          [4 dias]
Fase 4 (Build):   Setup Vite + migração TS          [2 dias]
Fase 5 (Build):   Otimização + code-splitting       [1 dia]
```

---

## 3. Stack Final

| Camada | Tecnologia | Versão | Origem |
|--------|-----------|--------|--------|
| **Backend** | CodeIgniter 3 + PHP 8.3 | existente | — |
| **Layout** | CoreUI React | 5.x | CDN unpkg |
| **Componentes** | `@coreui/react` | 5.x | CDN unpkg |
| **Ícones** | `@coreui/icons-react` | 3.x | CDN unpkg |
| **JSX-like** | `htm` (tagged templates) | 3.x | CDN unpkg (~1KB) |
| **React** | `react` + `react-dom` | 18.x | CDN unpkg |
| **HTTP** | `fetch` nativo | — | built-in |
| **CSS theme** | `mapos.css` (consolidado) | existente | local |
| **Font** | Inter (Google Fonts) | — | CDN Google |

**Tamanho CDN total (cached após 1ª visita):**
- React: 130KB (gzip: 42KB)
- ReactDOM: 130KB (gzip: 42KB)
- CoreUI: 250KB (gzip: 80KB)
- CoreUI Icons CSS: 80KB (gzip: 12KB)
- htm: 1KB (gzip: 0.5KB)
- **Total: ~591KB / ~176KB gzipped** ✅ (Kinghost OK)

---

## 4. Estrutura de Arquivos

### 4.1. Frontend (CDN-first)

```
assets/
├── css/
│   └── mapos.css              # já existe (consolidado)
├── js/
│   ├── vendor/
│   │   ├── react.production.min.js       # download único, hospedado local
│   │   ├── react-dom.production.min.js   # download único, hospedado local
│   │   ├── htm.min.js                    # 1KB, JSX-like
│   │   ├── coreui.bundle.min.js          # CoreUI React UMD
│   │   └── coreui-icons.min.js           # ícones (opcional)
│   └── coreui/                          # código dos componentes
│       ├── app.js                       # bootstrap do app
│       ├── router.js                    # roteamento client-side
│       ├── api.js                       # wrapper fetch + CSRF
│       └── components/
│           ├── Layout.js                # AppShell (sidebar + topbar)
│           ├── Sidebar.js               # navegação dark
│           ├── Topbar.js                # header com busca + perfil
│           ├── Dashboard.js             # cards de KPI
│           ├── KanbanBoard.js           # drag & drop
│           ├── DataTable.js             # tabela genérica
│           └── ThemeSwitcher.js         # troca tema (white/puredark/...)
```

**Decisão:** baixar `react`, `react-dom`, `coreui.bundle` para `assets/js/vendor/` via `curl` no PC local, e commitar no repo. Vantagem: **Kinghost não bloqueia CDN externo em algumas contas**, e arquivos locais são cacheados pelo navegador por 1 ano.

### 4.2. Backend (PHP/CodeIgniter — sem mudanças)

```
application/
├── views/
│   ├── tema/
│   │   ├── topo.php                    # MODIFICAR: adicionar CoreUI CSS
│   │   ├── rodape.php                  # MODIFICAR: bootstrap CoreUI JS
│   │   ├── sidebar.php                 # NOVO: navegação dark CoreUI
│   │   └── _ux_layout.php              # NOVO: shell reutilizável
│   ├── dashboard/
│   │   └── index.php                   # MODIFICAR: usar layout CoreUI
│   ├── kanban/
│   │   └── board.php                   # MODIFICAR: virar componente React
│   └── ... (outras views)
├── controllers/
│   ├── Dashboard.php                   # adicionar método `/api/stats`
│   ├── Kanban.php                      # adicionar método `/api/cards`
│   └── Api.php                         # expandir endpoints
└── helpers/
    └── api_helper.php                  # NOVO: json_response, csrf_token
```

---

## 5. Layout Unificado (AppShell)

### 5.1. Estrutura visual (espelhando CoreUI demo)

```
┌─────────────────────────────────────────────────────────────┐
│ ☰  Mapos OS          [Search 🔍]    🔔3   👤 Admin  ▼       │  ← Topbar (h: 64px)
├─────────┬───────────────────────────────────────────────────┤
│         │                                                   │
│ 🏠 Dash │  Dashboard                                        │  ← Page header
│ 📋 OS   │  ──────────────────────────────────────────       │
│ 👥 Cli  │                                                   │
│ 📦 Prod │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐             │
│ 💰 Fin  │  │ 1.2k │ │ 389  │ │ 562  │ │ R$   │             │  ← KPI cards
│ 🔧 Obr  │  │ OS   │ │ Cli  │ │ Vnd  │ │ 12k  │             │
│ 📊 Kan  │  └──────┘ └──────┘ └──────┘ └──────┘             │
│ 📄 NFSe │                                                   │
│         │  ┌─────────────────────┐ ┌────────────────────┐   │
│ ─────── │  │ Vendas por mês      │ │ Status OS          │   │
│ ⚙ Conf  │  │ [line chart]        │ │ [donut chart]      │   │
│ ❓ Ajuda│  └─────────────────────┘ └────────────────────┘   │
│         │                                                   │
│ ─────── │  ┌─────────────────────────────────────────────┐  │
│ v5.0.0  │  │ Atividade recente                          │  │
│         │  │ ─────────────────────────────────────────  │  │
│         │  │ 14:32  OS #1024 atualizada                 │  │
│         │  │ 14:18  Cliente João cadastrado             │  │
│         │  └─────────────────────────────────────────────┘  │
└─────────┴───────────────────────────────────────────────────┘
   ↑
   Sidebar dark (w: 256px, collapsible p/ 64px)
```

### 5.2. CSS necessário no `mapos.css` (Bloco 19 NOVO)

```css
/* =========================================================================
   19. COREUI LAYOUT (AppShell: sidebar + topbar + content)
   ========================================================================= */

body { margin: 0; font-family: 'Inter', sans-serif; }

.app-shell { display: flex; min-height: 100vh; }
.app-sidebar {
    width: 256px; background: #2a3a52; color: #c5cbd3;
    position: fixed; top: 0; left: 0; height: 100vh;
    overflow-y: auto; z-index: 100;
    transition: transform 0.3s ease;
}
.app-sidebar.collapsed { transform: translateX(-192px); width: 64px; }
.app-sidebar-brand {
    padding: 16px 20px; font-size: 18px; font-weight: 700; color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; gap: 10px;
}
.app-sidebar-nav { list-style: none; padding: 12px 0; margin: 0; }
.app-sidebar-nav li a {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 20px; color: #c5cbd3; text-decoration: none;
    border-left: 3px solid transparent; font-size: 14px;
    transition: all 0.15s ease;
}
.app-sidebar-nav li a:hover {
    background: rgba(255,255,255,0.05); color: #fff;
}
.app-sidebar-nav li a.active {
    background: rgba(255,255,255,0.08); color: #fff;
    border-left-color: var(--color-accent);
}
.app-sidebar-nav li a i { width: 20px; font-size: 16px; }

.app-main {
    margin-left: 256px; flex: 1; transition: margin-left 0.3s ease;
}
.app-sidebar.collapsed + .app-main { margin-left: 64px; }

.app-topbar {
    height: 64px; background: var(--bg-card);
    border-bottom: 1px solid var(--color-border);
    padding: 0 24px; display: flex; align-items: center; gap: 16px;
    position: sticky; top: 0; z-index: 50;
}
.app-content { padding: 24px; }

.app-card {
    background: var(--bg-card); border: 1px solid var(--color-border);
    border-radius: var(--radius-lg); padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.app-kpi {
    background: var(--bg-card); border-radius: var(--radius-lg);
    padding: 18px; border-left: 4px solid var(--color-accent);
    display: flex; justify-content: space-between; align-items: center;
}
.app-kpi-value { font-size: 28px; font-weight: 700; color: var(--color-heading); }
.app-kpi-label { font-size: 12px; color: var(--color-text-muted); text-transform: uppercase; }
.app-kpi-icon { font-size: 32px; opacity: 0.5; color: var(--color-accent); }
```

### 5.3. Temas — manter compatibilidade com `data-theme`

O `mapos.css` Bloco 19 deve sobrescrever `--bg-card`, `--color-text`, etc. quando body tem `data-theme="darkviolet"` (sidebar fica escura, content area fica escura também). O sidebar do CoreUI usa cor fixa `#2a3a52` para **preservar contraste da navegação** mesmo em temas dark.

---

## 6. Componentes React (CDN + htm)

### 6.1. Bootstrap do app (`assets/js/coreui/app.js`)

```js
// htm + React via window global
const { useState, useEffect } = React;
const html = htm.bind(React.createElement);

// Carrega CoreUI (assume que está em window.CoreUI)
const {
    CSidebar, CSidebarBrand, CSidebarNav, CNavItem, CNavLink,
    CHeader, CContainer, CCard, CCardBody, CCardHeader,
    CRow, CCol, CBadge, CButton, CIcon
} = CoreUI;

// CSidebar é só CSS, não React component. Vamos usar HTML+CSS.
// Para os componentes interativos, CoreUI React expõe via CDN UMD.
```

**Descoberta importante:** o CoreUI React **NÃO** distribui UMD bundle oficial no npm — só ESM. Para CDN, opções são:

1. **Usar `esm.sh`** (CDN que converte ESM em runtime):
   ```html
   <script type="module">
     import { CSidebar, ... } from 'https://esm.sh/@coreui/react@5';
   </script>
   ```

2. **Usar CoreUI HTML/CSS puro** (sem React) — opção **RECOMENDADA**:
   - CoreUI tem CSS classes como `.sidebar`, `.navbar`, `.card`, `.btn`
   - Funciona com **qualquer** framework
   - jQuery + Bootstrap 5 já presente pode fazer a interatividade

**Recomendação pragmática:** usar **CoreUI HTML/CSS + React apenas onde precisa state complexo** (kanban drag&drop, dashboard com auto-refresh).

### 6.2. Componentes Vanilla (jQuery)

```js
// Sidebar toggle
$('#btn-toggle-sidebar').on('click', () => {
    $('.app-sidebar').toggleClass('collapsed');
});

// Notificações (já existe em rodape.php)
carregarNotificacoes();

// Dropdown do usuário
$('.app-user-dropdown').on('click', e => {
    e.stopPropagation();
    $(e.currentTarget).toggleClass('show');
});
```

### 6.3. Componentes React (apenas onde há state real)

| Componente | React? | Por quê |
|------------|--------|---------|
| **Sidebar** | ❌ HTML/CSS | Estático, só toggle jQuery |
| **Topbar** | ❌ HTML/CSS | Estático |
| **Dashboard cards** | ⚠️ React | Auto-refresh, fetch a cada 30s |
| **Kanban board** | ✅ React | Drag & drop, state complexo |
| **DataTable** | ❌ DataTables.js | Já existe, muito bom |
| **Formulários** | ❌ jQuery | Já funciona |
| **Charts** | ⚠️ Chart.js vanilla | Auto-refresh opcional |

### 6.4. Kanban com drag & drop (React mínimo)

```js
// assets/js/coreui/components/KanbanBoard.js
const { useState, useEffect } = React;
const html = htm.bind(React.createElement);

function KanbanBoard() {
    const [columns, setColumns] = useState({});
    const [draggedCard, setDraggedCard] = useState(null);

    useEffect(() => {
        fetch('/index.php/kanban/api/cards', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => setColumns(data.columns));
    }, []);

    const onDragStart = (card, fromCol) => setDraggedCard({ card, fromCol });
    const onDrop = (toCol) => {
        // POST /kanban/api/move
        fetch('/index.php/kanban/api/move', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                card_id: draggedCard.card.id,
                from: draggedCard.fromCol,
                to: toCol
            })
        });
        // optimistic update
        const newCols = { ...columns };
        newCols[draggedCard.fromCol] = newCols[draggedCard.fromCol]
            .filter(c => c.id !== draggedCard.card.id);
        newCols[toCol] = [...(newCols[toCol] || []), draggedCard.card];
        setColumns(newCols);
    };

    return html`
        <div class="kanban-board">
            ${Object.entries(columns).map(([status, cards]) => html`
                <div class="kanban-column"
                     onDragOver=${e => e.preventDefault()}
                     onDrop=${() => onDrop(status)}>
                    <div class="kanban-column-header">
                        ${status} <span class="badge-count">${cards.length}</span>
                    </div>
                    <div class="kanban-column-body">
                        ${cards.map(card => html`
                            <div class="kanban-card"
                                 draggable=${true}
                                 onDragStart=${() => onDragStart(card, status)}>
                                <div class="kanban-card-title">${card.titulo}</div>
                                <div class="kanban-card-desc">${card.cliente}</div>
                            </div>
                        `)}
                    </div>
                </div>
            `)}
        </div>
    `;
}

// Mount
const root = ReactDOM.createRoot(document.getElementById('kanban-root'));
root.render(html`<${KanbanBoard} />`);
```

---

## 7. Endpoints da API (Backend)

### 7.1. Kanban (novo)

```php
// application/controllers/Kanban.php
public function api_cards() {
    $status = $this->input->get('status') ?: null;
    $cards = $this->kanban_model->getCards($status);
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['columns' => $cards]));
}

public function api_move() {
    $cardId = $this->input->post('card_id');
    $toStatus = $this->input->post('to');
    $result = $this->kanban_model->moveCard($cardId, $toStatus);
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['success' => $result]));
}
```

### 7.2. Dashboard stats

```php
// application/controllers/Dashboard.php
public function api_stats() {
    $stats = [
        'os_total'      => $this->dashboard_model->countOs(),
        'os_pendentes'  => $this->dashboard_model->countOs('pendente'),
        'clientes_total' => $this->dashboard_model->countClientes(),
        'vendas_mes'    => $this->dashboard_model->sumVendasMes(),
        'faturamento_mes' => $this->dashboard_model->sumFaturamentoMes(),
    ];
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($stats));
}
```

### 7.3. Helper CSRF (cross-cutting)

```php
// application/helpers/api_helper.php
function api_fetch($url, $options = []) {
    $csrfName = config_item('csrf_token_name');
    $csrfCookie = config_item('csrf_cookie_name');
    $token = $_COOKIE[$csrfCookie] ?? '';

    $headers = array_merge([
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
        $csrfName => $token,
    ], $options['headers'] ?? []);

    return fetch($url, array_merge($options, ['headers' => $headers]));
}
```

---

## 8. Mapa de Migração por Página

### 8.1. Páginas que viram **layout CoreUI** (shell + conteúdo)

| Página | Controller | View | Esforço |
|--------|-----------|------|---------|
| Dashboard | `Dashboard` | `dashboard/index` | 2h |
| Clientes | `Clientes` | `clientes/clientes` | 1h |
| OS | `Os` | `os/os` | 2h |
| Vendas | `Vendas` | `vendas/vendas` | 2h |
| Financeiro | `Financeiro` | `financeiro/lancamentos` | 1h |
| Obras | `Obras` | `obras/obras_list` | 2h |
| Kanban | `Kanban` | `kanban/board` | 3h |
| NFS-e | `Nfse_os` | `nfse_os/dashboard` | 1h |
| Garantias | `Garantias` | `garantias/*` | 1h |
| Produtos | `Produtos` | `produtos/produtos` | 1h |
| Notificações | `Notificacoes` | inline | 0.5h |
| Configurações | `Mapos` | `mapos/painel` | 1h |

**Total: ~17h de migração visual** (distribuídas em 2-3 sprints)

### 8.2. Páginas que **permanecem como estão**

| Página | Por quê |
|--------|---------|
| Login (`conecte/login.php`) | Página pública, sem sidebar, tema custom |
| Portal do técnico (`conecte/minha_os.php`, etc.) | Mobile-first, leve, jQuery |
| Impressões (`relatorios/imprimir/*`) | `imprimir.css` específico |
| Error pages | HTML simples |

### 8.3. Componentes que viram **React isolado**

| Componente | Onde | Por quê |
|------------|------|---------|
| Kanban board | `/kanban/board` | Drag & drop, state |
| Dashboard auto-refresh | `/dashboard` | Stats a cada 30s |
| Wizard de OS | `/os/adicionar` (futuro) | Multi-step stateful |
| Notificações real-time | topbar (futuro) | WebSocket/Polling |

---

## 9. Plano de Execução

### Fase 1 — Setup base (2 dias) ⭐ FOCO IMEDIATO

**Entregáveis:**
1. ✅ Baixar `react@18`, `react-dom@18`, `htm@3`, `coreui-icons` para `assets/js/vendor/`
2. ✅ Criar `assets/css/mapos.css` Bloco 19 (CoreUI layout classes)
3. ✅ Criar `application/views/tema/sidebar.php` (navegação dark, inclui menu de permissões)
4. ✅ Criar `application/views/tema/_ux_layout.php` (shell reutilizável)
5. ✅ Modificar `application/views/tema/topo.php` (carrega CoreUI CSS + JS vendor)
6. ✅ Modificar `application/views/tema/rodape.php` (bootstrap sidebar toggle)
7. ✅ Migrar `application/views/dashboard/index.php` como página-piloto

**Validação:** dashboard abre com sidebar dark, topbar com busca, KPI cards em grid responsivo.

### Fase 2 — Migração em massa (5 dias)

| Dia | Páginas | Status esperado |
|-----|---------|-----------------|
| 1 | Clientes, Produtos | Layout padrão, DataTable existente |
| 2 | OS (listar), Vendas (listar) | Layout padrão, filtros |
| 3 | Financeiro, Cobranças | Layout padrão, totais |
| 4 | Obras, NFS-e | Layout padrão, status badges |
| 5 | Garantias, Mapos/Config | Layout padrão, formulários |

**Validação:** todas as páginas autenticadas abrem com sidebar+topbar CoreUI, tema via `data-theme` continua funcionando.

### Fase 3 — Componentes React (3 dias)

| Dia | Componente | Complexidade |
|-----|-----------|--------------|
| 1 | Kanban com drag & drop | ⭐⭐⭐ |
| 2 | Dashboard auto-refresh (Chart.js) | ⭐⭐ |
| 3 | Toggle tema no topbar (já existe, polir) | ⭐ |

**Validação:** Kanban move cards com persistência, dashboard atualiza sozinho.

### Fase 4 — Build opcional Vite (2 dias, FUTURO)

| Tarefa | Detalhes |
|--------|----------|
| Setup `package.json` + `vite.config.js` | Apontar `outDir: 'dist'` |
| Migrar `app.js` para TypeScript | Tipos dos endpoints PHP |
| Code-splitting por rota | `dynamic import()` no router |
| Service Worker (PWA) | Cache offline de assets |
| CI no GitHub Actions | Build automático no push |

**Pré-requisito:** comprar domínio + VPS (Kinghost compartilhada não aguenta build).

---

## 10. Compatibilidade com `mapos.css` Consolidado

O `mapos.css` já tem 18 blocos. O **Bloco 19** (CoreUI layout) é **aditivo** — não muda nada que existe. Variáveis como `--bg-card`, `--color-text`, `--color-accent` continuam sendo a fonte da verdade. O tema via `data-theme` no `<body>` funciona igual:

```html
<body data-theme="darkviolet">  <!-- sidebar dark, content dark -->
<body data-theme="white">       <!-- sidebar dark, content light -->
<body data-theme="puredark">    <!-- sidebar dark, content light+preto -->
```

**Sidebar sempre dark** (`#2a3a52`) — independente do tema. Isso preserva contraste da navegação.

---

## 11. Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| **CDN unpkg.com fora do ar** | Baixa | Alto | Baixar vendor pra `assets/js/vendor/` |
| **Bundle muito pesado no celular** | Média | Médio | Code-splitting + lazy load (Fase 4) |
| **CoreUI React sem UMD oficial** | Alta | Baixo | Usar ESM via esm.sh OU CoreUI CSS puro |
| **Conflito com jQuery/Bootstrap 5** | Média | Médio | Namespace `.app-*` em todas as classes CoreUI |
| **Tema darkviolet quebrar contraste** | Baixa | Médio | Testar com `prefers-contrast` media query |
| **Kinghost bloquear CORS** | Baixa | Alto | Vendor local (já planejado) |

---

## 12. Métricas de Sucesso

| KPI | Antes | Meta | Como medir |
|-----|-------|------|------------|
| **Tempo médio pra achar uma página** | 8s (clicar menus) | 2s (busca Cmd+K) | Hotjar / analytics |
| **Tamanho 1ª visita (gzipped)** | ~150KB (mapos.css + BS5 + jQuery) | ~250KB (+ React + CoreUI) | DevTools |
| **Cache hit 2ª visita** | 80% | 95% (vendor cacheado 1 ano) | DevTools |
| **Linhas de jQuery spaghetti** | ~3000 | ~1500 (50% menos) | `git ls-files \| grep '\.js$' \| xargs wc -l` |
| **Linhas React isolado** | 0 | ~600 (kanban + dashboard) | — |

---

## 13. Critério de Pronto

### Fase 1 ✅
- [ ] Sidebar dark renderiza em todas as páginas autenticadas
- [ ] Topbar com busca, notificações, user dropdown funciona
- [ ] Toggle de tema (white/puredark/darkviolet/...) funciona no topbar
- [ ] Dashboard-piloto com KPI cards responsivos
- [ ] Lint PHP 100% (0 erros)
- [ ] Lint JS 100% (0 erros)
- [ ] Validação em todos os 6 temas (manual screenshot)

### Fase 2 ✅
- [ ] 12 páginas migradas pro layout CoreUI
- [ ] DataTable, formulários, modais funcionando idênticos
- [ ] Mobile portal do técnico intocado

### Fase 3 ✅
- [ ] Kanban drag & drop persistindo
- [ ] Dashboard auto-refresh a cada 30s
- [ ] Zero regressão nas páginas antigas

---

## 14. Próximos Passos Imediatos

1. **Aprovar este plano** (você está revisando agora)
2. **Criar branch** `feature/coreui-layout` no Git
3. **Fase 1 dia 1**: baixar vendor, criar Bloco 19 no `mapos.css`, sidebar.php
4. **Fase 1 dia 2**: migrar `dashboard/index.php` como página-piloto
5. **Demo em staging** (se houver) antes de promover pra produção

---

## Anexo A: Comandos para baixar vendor (PC local)

```bash
# Criar pasta vendor
mkdir -p assets/js/vendor

# React 18
curl -L -o assets/js/vendor/react.production.min.js \
  https://unpkg.com/react@18.3.1/umd/react.production.min.js
curl -L -o assets/js/vendor/react-dom.production.min.js \
  https://unpkg.com/react-dom@18.3.1/umd/react-dom.production.min.js

# htm (1KB)
curl -L -o assets/js/vendor/htm.min.js \
  https://unpkg.com/htm@3.1.1/dist/htm.min.js

# CoreUI Icons CSS
curl -L -o assets/css/coreui-icons.css \
  https://unpkg.com/@coreui/icons@3.0.0/css/all.min.css

# Font Inter (já via Google Fonts, mas pode hospedar local)
# (opcional, continuar com Google Fonts CDN)
```

**Tamanho total:** ~600KB (todos os vendors) — cabe na cota Kinghost.

---

## Anexo B: Referências

- **CoreUI React docs:** https://coreui.io/react/docs/getting-started/introduction/
- **CoreUI demo (template alvo):** https://coreui.io/demos/react/latest/default/
- **htm (JSX-like sem build):** https://github.com/developit/htm
- **Plano UX anterior:** [PLANO_MELHORIAS_UX.md](PLANO_MELHORIAS_UX.md)
- **CSS consolidado:** `assets/css/mapos.css` (1.1k linhas, 18 blocos)

---

**Why:** MaposV5 precisa de UI moderna e consistente sem reescrita. CoreUI React via CDN + htm entrega o template admin completo (https://coreui.io/demos/react/latest/default/) com **zero build obrigatório**, mantendo hospedagem compartilhada Kinghost.

**How to apply:** Após aprovação, executar Fase 1 (2 dias) = setup + dashboard-piloto. Avaliar resultado antes de migrar as outras 11 páginas (Fase 2).
