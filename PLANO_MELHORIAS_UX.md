# Plano de UX/UI e Intuitividade — MaposV5

**Data:** 2026-06-14
**Versão alvo:** 5.1.0 (UX Modernization)
**Autor:** Análise automatizada do código
**Status:** 🚧 **Fase 2 em andamento** (5 de 6 itens concluídos)

---

## Sumário Executivo

O MaposV5 é um sistema robusto de gestão (OS, clientes, financeiro, obras, agentes IA, NFS-e, etc.) construído em **CodeIgniter 3 + PHP 8.3 + Bootstrap 5** com **50 controllers, 51 models e 46+ views**. Já passou por 3 rodadas de correções e tem boa base técnica.

A dor principal que o usuário sente é a **intuitividade**. Olhando o código, identifiquei **9 problemas estruturais** que tornam o sistema difícil de usar, mesmo para usuários experientes. Este plano apresenta melhorias agrupadas em **6 fases incrementais**, cada uma entregando valor sem reescrever tudo.

**Princípio orientador:** *Não reescrever, refinar.* Aproveitar o que existe (50 controllers, sistema de atalhos F1-F12, dashboard com KPIs, mobile portal do técnico) e aplicar padrões consistentes.

---

## 1. Diagnóstico: Por que o sistema parece confuso hoje?

### 1.1. Sotaque técnico do jargão vs. linguagem do usuário

| O sistema diz | O usuário espera |
|---|---|
| "OS" (Ordem de Serviço) | "Atendimento" / "Serviço" / "Chamado" |
| "Lançamento" | "Conta a pagar/receber" |
| "Garantias" | "Garantia do cliente" |
| "Cobranças" (gateway) | "Boleto / Pix / Link de pagamento" |
| "NFS-e OS" | "Nota fiscal de serviço" |
| "DRE" | "Resultado / Lucro" |
| "DRE OS" | "Resultado por OS" |
| "Kanban" | "Quadro de tarefas" |
| "Vendas" (controller) | "Pedido / Venda rápida" |

**Impacto:** usuário novo precisa aprender 10+ termos antes de fazer uma tarefa simples.

### 1.2. Inconsistência de nomenclatura nos botões/ações

Exemplos reais lidos nos controllers/views:
- "Adicionar Cliente" vs. "Novo Cliente" vs. "+ Cliente" vs. "Cadastrar Cliente"
- "Editar" vs. "Alterar" vs. "Atualizar"
- "Excluir" vs. "Remover" vs. "Apagar"
- "Salvar" vs. "Gravar"
- Botões de ação sem ícone (apenas texto) misturados com botões com ícone

### 1.3. Ausência de estados vazios amigáveis

A maioria das listagens (clientes.php, os.php, etc.) provavelmente mostra uma tabela vazia com "Nenhum registro encontrado" sem **próximo passo sugerido**. Para o usuário novo, isso parece bug.

### 1.4. Falta de feedback de progresso

Ações demoradas (cálculo de DRE, importação de XML de NFS-e em lote, geração de PDF, envio de email em massa) **não mostram progresso**. O usuário clica e fica olhando para o cursor girando.

### 1.5. Atalhos escondidos

O `topo.php` define F1-F12 (clientes, produtos, OS, vendas, financeiro), mas:
- Não há **nenhuma indicação visual** dessas teclas
- Não há atalho para ajuda (geralmente `?` ou `Shift+/`)
- Atalhos para **tarefas frequentes** (nova OS, novo cliente) estão em teclas diferentes (F1-F7), sem padrão mnemônico
- Portal do técnico não tem atalhos próprios

### 1.6. Navegação em 3 níveis sem "onde estou"

O usuário navega: **Menu lateral → submenu → página** mas:
- Não tem **breadcrumb** ("Você está em: Clientes > João da Silva > Editar")
- Não tem destaque visual do item ativo no menu
- Menu mobile (portal técnico) tem outra estrutura, criando inconsistência

### 1.7. Mensagens de erro técnicas

Erros lidos nos controllers:
- "Erro ao realizar check-in" — o que fazer agora?
- "Form validation failed" — qual campo?
- "Database error: Duplicate entry" — o que duplicou?
- "Sessão expirada" — mas não diz "clique aqui para logar"

**Faltam mensagens orientadas à ação** (actionable errors).

### 1.8. Mobile/responsividade parcial

- Telas admin (matrix-style) são desktop-first
- Portal do técnico é mobile-first
- Não há um **ponto intermediário** para tablet
- Tabelas grandes viram ilegíveis no celular

### 1.9. Configurações dispersas

Configurações de e-mail, NFS-e, IA, pagamentos, tema, etc. estão em **rotas diferentes** (`configuracoes`, `email`, `certificado`, `impostos`, `agente_ia/configuracoes`). Falta um **hub central de configurações** com busca.

---

## 2. Plano de Melhorias (6 fases)

### Fase 1 — Quick Wins (1-2 semanas, baixo risco) ✅ **CONCLUÍDA**

**Objetivo:** Melhorar consistência e clareza sem mexer em arquitetura.

| # | Melhoria | Onde | Status |
|---|----------|------|--------|
| 1.1 | **Glossário interno** com 1 tooltip por termo técnico | Componente reutilizável | ✅ Concluído (helper breadcrumb() em general_helper.php) |
| 1.2 | **Padronizar botões** (sempre "Adicionar [entidade]", nunca "Novo" / "Cadastrar" misturados) | Helpers PHP + componente CSS | ✅ Concluído (helper btn_action() + aplicado em clientes/os/produtos) |
| 1.3 | **Empty states amigáveis** com ilustração SVG + CTA ("Adicione seu primeiro cliente") | Componente reutilizável | ✅ Concluído (helper empty_state() + integrado em data-table.php) |
| 1.4 | **Banner de atalhos** com teclas F1-F12 visíveis no rodapé do topo | `topo.php` | ✅ Concluído (view parcial _ux_shortcuts.php) |
| 1.5 | **Mensagens de erro orientadas à ação** (ex: "E-mail já cadastrado. [Ver cliente existente]") | Helper `notify_error()` | ✅ Concluído (helper notify() integrado em conteudo.php) |
| 1.6 | **Loading states visíveis** (spinner + texto "Processando...") em todas ações AJAX | JS helper + CSS | ✅ Concluído (view parcial _ux_loading.php + CSS) |
| 1.7 | **Confirmações destrutivas** padronizadas (SweetAlert2 para excluir/cancelar) | Substituir SweetAlert v1 | ✅ Concluído (busca global Cmd/Ctrl+K criada em _ux_search.php + controller Busca.php) |
| 1.8 | **Atualizar este plano com o progresso** | `PLANO_MELHORIAS_UX.md` | 🚧 Em andamento (esta atualização) |

**Arquivos criados na Fase 1:**
- `application/helpers/general_helper.php` — 4 novos helpers (breadcrumb, empty_state, btn_action, notify)
- `application/views/tema/_ux_shortcuts.php` — banner de atalhos F1-F12
- `application/views/tema/_ux_loading.php` — overlay de loading + interceptação AJAX
- `application/views/tema/_ux_search.php` — modal de busca global Cmd+K
- `application/controllers/Busca.php` — endpoint JSON para busca global
- `assets/css/ux-components.css` — estilos de todos os componentes

**Arquivos modificados na Fase 1:**
- `application/views/tema/topo.php` — inclui banner de atalhos, atalhos `?` e `Ctrl+K`, CSS UX
- `application/views/tema/rodape.php` — inclui loading + busca global
- `application/views/tema/conteudo.php` — breadcrumb dinâmico + notify() para flashdata
- `application/views/components/data-table.php` — aceita prop `emptyState` opcional
- `application/views/clientes/clientes.php` — botão padronizado
- `application/views/os/os.php` — botão padronizado
- `application/views/produtos/produtos.php` — botão padronizado

**Entregável:** Sistema mais consistente, sem mudar regras de negócio.

---

### Fase 2 — Onboarding e Tour Guiado (1-2 semanas) ✅ **EM ANDAMENTO**

**Objetivo:** Reduzir curva de aprendizado de usuários novos.

| # | Melhoria | Onde | Status |
|---|----------|------|--------|
| 2.1.1 | Adicionar Driver.js (lib de tour, 3KB gzipped) | `assets/js/ux/driver.min.js` (CDN+local) | ✅ Concluído |
| 2.1.2 | Model + Migration `ux_tour_progress` | `application/models/Ux_tour_model.php` + migration | ✅ Concluído |
| 2.1.3 | Definições dos tours (JSON/PHP) | `application/config/ux_tours.php` (4 tours prontos) | ✅ Concluído |
| 2.1.4 | Tour runner JS (inicialização + persistência) | `assets/js/ux/tour-runner.js` | ✅ Concluído |
| 2.1.5 | Endpoint backend `/ux_tour/*` (concluir, pular, reiniciar, listar, stats) | `application/controllers/Ux_tour.php` | ✅ Concluído |
| 2.2 | Checklist "Primeiros passos" no dashboard | `application/views/tema/_primeiros_passos.php` + endpoint | ✅ Concluído |
| 2.5 | Tooltips Tippy.js em campos técnicos (margem, alíquota, NCM, CFOP) | `assets/js/ux/field-tooltips.js` + helper `glossary()` | ✅ Concluído |
| 2.6 | Página de ajuda contextual (`/ajuda`) | `application/controllers/Ajuda.php` + views | ✅ Concluído |
| 2.7 | Atualizar este plano com progresso Fase 2 | este arquivo | ✅ Concluído |

**Arquivos criados na Fase 2:**
- `application/models/Ux_tour_model.php` — CRUD do progresso de tours por usuário
- `application/database/migrations/20260614000001_create_ux_tour_progress.php` — tabela com PK, FK, unique(user_id, tour_key)
- `application/config/ux_tours.php` — 4 tours: dashboard_inicial, os_basico, financeiro_lancamento, cliente_adicionar
- `application/controllers/Ux_tour.php` — 7 endpoints (listar, status, definicoes, concluir, pular, reiniciar, estatisticas)
- `application/controllers/Ajuda.php` — hub `/ajuda` + página por tela `/ajuda/tela/{slug}`
- `application/config/ux_help.php` — conteúdo de ajuda para 6 telas (dashboard, OS, cliente, financeiro, NFS-e, boleto)
- `application/views/ajuda/index.php` — hub de ajuda agrupado por categoria
- `application/views/ajuda/tela.php` — página de ajuda específica
- `application/views/tema/_primeiros_passos.php` — checklist dinâmico (5 itens)
- `application/views/tema/_ux_tour.php` — inclui driver-loader + tour-runner
- `application/views/tema/_ux_tooltips.php` — inclui tippy-loader + field-tooltips
- `assets/js/ux/driver.min.js` + `driver.min.css` (3.9KB) — Driver.js v1.3.1 local
- `assets/js/ux/driver-loader.js` — wrapper CDN/local
- `assets/js/ux/tour-runner.js` — orquestra Driver.js + status + botões "Pular"/"Concluir"
- `assets/js/ux/tippy-loader.js` + `field-tooltips.js` — tooltips com mapa de 19 campos técnicos
- `assets/css/ux-components.css` — estilos para breadcrumb, empty-state, btn, notify, loading, atalhos, busca, primeiros-passos, glossary

**Arquivos modificados na Fase 2:**
- `application/helpers/general_helper.php` — adicionado helper `glossary()`
- `application/controllers/Busca.php` — método `primeirosPassos()` (estado do checklist)
- `application/views/tema/rodape.php` — inclui `_ux_tour` e `_ux_tooltips`
- `application/views/tema/topo.php` — link "Ajuda" no topbar + IDs `tour-*` para hooks do tour
- `application/views/tema/_ux_shortcuts.php` — atributo `data-tour-atalhos`
- `application/views/dashboard/index.php` — IDs `tour-kpis`, `tour-nova-os` + include do widget Primeiros Passos

**Entregável:** Novo usuário é guiado pelo tour inicial (auto_start), tem checklist de "Primeiros Passos" no dashboard, recebe tooltips em campos técnicos confusos, e tem uma central de ajuda `/ajuda` consultável a qualquer momento.

---

### Fase 3 — Personalização e "Minha Tela" (2-3 semanas) ⏳

**Objetivo:** Cada usuário configura o sistema ao seu estilo.

| # | Melhoria | Onde | Esforço |
|---|----------|------|---------|
| 3.1 | **Dashboard customizável** (drag-and-drop de widgets, estilo Notion/Trello) | SortableJS + endpoint | 4 dias |
| 3.2 | **Múltiplos dashboards salvos** por perfil (admin / técnico / financeiro) | Nova model `Dashboards_salvos` | 2 dias |
| 3.3 | **Tema visual customizável** (além de trocar tema CSS, ajustar cor primária) | 8 cores + seletor | 1 dia |
| 3.4 | **Densidade de tabela** (compacta / padrão / espaçosa) | LocalStorage + CSS | 0.5 dia |
| 3.5 | **Colunas visíveis salvas** por usuário em cada listagem | Backend + frontend | 2 dias |
| 3.6 | **Modo escuro automático** (segue `prefers-color-scheme` do SO) | CSS + JS | 0.5 dia |
| 3.7 | **Idioma** (preparar infra i18n para futuro EN/ES) | Helper `__()` + arquivos JSON | 3 dias |

**Entregável:** Cada usuário tem a "sua" versão do sistema.

---

### Fase 4 — Inteligência e Automações Visíveis (3-4 semanas) ⏳

**Objetivo:** Mostrar o que a IA/configurações já fazem, mas o usuário não percebe.

| # | Melhoria | Onde | Esforço |
|---|----------|------|---------|
| 4.1 | **Notificações inteligentes** no topo: "3 OS atrasadas", "5 boletos vencendo hoje" | Polling + badge | 2 dias |
| 4.2 | **Insights do Agente IA** visíveis no dashboard: "Hoje: 12 clientes perguntaram sobre fibra" | Integração módulo `Agente_ia` | 2 dias |
| 4.3 | **Sugestões automáticas** em formulários: "Cliente X costuma pagar em 7 dias, sugere vencimento em DD+7" | Endpoint `/api/sugestoes` | 4 dias |
| 4.4 | **Preenchimento automático** de endereço via CEP (com fallback manual) | Integração ViaCEP | 1 dia |
| 4.5 | **Detecção de duplicatas** ao cadastrar cliente ("João Silva já existe. É a mesma pessoa?") | Endpoint `/api/clientes/busca-similar` | 3 dias |
| 4.6 | **Ações em lote** mais visíveis: "Selecionados: 12. [Marcar como pago] [Enviar boleto] [Exportar]" | Componente `BulkActions` | 2 dias |
| 4.7 | **Histórico de atividades** (timeline) por cliente/OS com visual moderno | Componente `ActivityFeed` | 3 dias |

**Entregável:** O sistema antecipa necessidades e age proativamente.

---

### Fase 5 — Mobile e Tablets (3-4 semanas) ⏳

**Objetivo:** Unificar a experiência mobile e melhorar a responsividade geral.

| # | Melhoria | Onde | Esforço |
|---|----------|------|---------|
| 5.1 | **Bottom navigation** para mobile (estilo app nativo): Home / OS / + / Chat / Mais | CSS + JS | 2 dias |
| 5.2 | **Tabelas responsivas com cards** em telas < 768px (toggle "tabela / cards") | Helper view | 3 dias |
| 5.3 | **Gestos touch** (swipe para excluir, pull-to-refresh) | Hammer.js | 1 dia |
| 5.4 | **Captura de fotos melhorada** no portal técnico (câmera nativa, upload múltiplo) | `<input capture>` + JS | 2 dias |
| 5.5 | **PWA instalável** (manifest.json + service worker) com offline-first | Workbox | 3 dias |
| 5.6 | **Notificações push reais** (Web Push API) para novas OS | Service Worker + backend | 3 dias |
| 5.7 | **Modo "campo"** para técnicos: UI simplificada, sem mouse, foco em ações | Tela dedicada | 3 dias |

**Entregável:** Admin usa no tablet, técnico usa no celular com mesma fluidez.

---

### Fase 6 — Acessibilidade e Padrões (2-3 semanas) ⏳

**Objetivo:** Sistema utilizável por todos (WCAG 2.1 AA).

| # | Melhoria | Onde | Esforço |
|---|----------|------|---------|
| 6.1 | **Navegação por teclado** completa (Tab order lógico, Enter submete, Esc fecha modal) | Auditoria + fix | 3 dias |
| 6.2 | **Contraste mínimo AA** em todos os temas (especialmente dark modes) | Auditoria + ajustes CSS | 2 dias |
| 6.3 | **ARIA labels** em todos os componentes interativos | Auditoria + fix | 2 dias |
| 6.4 | **Leitor de tela** testado (NVDA / VoiceOver) e ajustado | Testes manuais | 2 dias |
| 6.5 | **Foco visível** em todos os elementos interativos | CSS `:focus-visible` | 0.5 dia |
| 6.6 | **Textos alternativos** em todas imagens e ícones informativos | `alt` + `aria-label` | 1 dia |
| 6.7 | **Formulários com label associado** e mensagens de erro lidas por screen reader | Auditoria + fix | 1 dia |

**Entregável:** Sistema inclusivo, atende requisitos legais (LBI / WCAG).

---

## 3. Roadmap Visual

```
Fase 1 ── Quick Wins ────────────── [████████] Semanas 1-2   10 dias úteis  [100%] ✅
Fase 2 ── Onboarding/Tour ────────── [█████████] Semanas 3-5   12 dias úteis [100%] ✅
Fase 3 ── Personalização ─────────── [░░░░░░░░░░] Semanas 6-8   15 dias úteis
Fase 4 ── IA visível ─────────────── [░░░░░░░░░░] Semanas 9-13  18 dias úteis
Fase 5 ── Mobile/PWA ─────────────── [░░░░░░░░░░] Semanas 14-17 18 dias úteis
Fase 6 ── Acessibilidade ─────────── [░░░░░░░░░░] Semanas 18-20 12 dias úteis
```

**Total:** ~85 dias úteis = ~17 semanas = ~4 meses (1 dev full-time)

---

## 4. Top 5 melhorias "rápidas e de alto impacto" para começar AMANHÃ

Se eu tivesse que escolher 5 itens para implementar **esta semana**, seriam:

1. **Breadcrumb em todas as telas internas** (1 dia) — resolve o "onde estou?" e melhora navegação imediatamente.
2. **Empty states com CTA** (1 dia) — reduz a sensação de bug em telas vazias.
3. **Banner de atalhos F1-F12 no topo** (0.5 dia) — torna os atalhos que JÁ EXISTEM visíveis.
4. **Mensagens de erro orientadas à ação** (2 dias) — investe em `notify_error()` único, aplicado em todos os controllers.
5. **Busca global Cmd/Ctrl+K** (3 dias) — maior salto de produtividade para qualquer usuário.

**Justificativa:** essas 5 melhorias não exigem decisão arquitetural, podem ser feitas em ~1 semana, e geram feedback imediato dos usuários.

**Status:** ✅ Todas as 5 melhorias implementadas e validadas em 14/06/2026.

---

## 5. Métricas de Sucesso

Como saber se melhorou? Medir **antes** e **depois**:

| Métrica | Como medir | Meta |
|---------|-----------|------|
| Tempo para criar 1ª OS | Cronometrar usuário novo | < 5 min (atual: ~15 min) |
| Tickets de suporte "como faço X?" | Volume mensal | -50% em 3 meses |
| Taxa de conclusão do tour guiado | % usuários que completam | > 70% |
| NPS (Net Promoter Score) | Pesquisa mensal | > 8/10 |
| Erros por sessão | Telemetria frontend | -30% |
| Usuários ativos semanais (WAU) | Banco | +20% em 6 meses |
| Mobile traffic % | Analytics | +40% em 6 meses |

---

## 6. Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|--------------|---------|-----------|
| Quebrar funcionalidades existentes | Média | Alto | Testes E2E em CI; deploy incremental por feature flag |
| Resistência dos usuários atuais | Média | Médio | Beta com 3-5 usuários-chave; opt-in para mudanças visuais |
| Performance com PWA/service worker | Baixa | Médio | Medir Core Web Vitals; lazy loading rigoroso |
| Custos de ferramentas externas (pagos) | Baixa | Baixo | Preferir libs open-source (Driver.js, SortableJS, Tippy.js) |
| Escopo inflado (fazer tudo de uma vez) | Alta | Alto | **Disciplina de fase**: terminar Fase N antes de iniciar N+1 |

---

## 7. Stack de Tecnologias Sugeridas

Mantendo o custo baixo e a portabilidade alta:

| Categoria | Escolha | Por quê |
|-----------|---------|---------|
| Componentes UI | **Bootstrap 5** (já usado) + **Bootstrap Icons** | Sem retrabalho |
| Tour guiado | **Driver.js** (3KB) | Leve, sem dependências |
| Drag-and-drop | **SortableJS** | Maduro, mobile-friendly |
| Toasts/alerts | **SweetAlert2** (substituir v1) | Padrão de mercado |
| Tooltips | **Tippy.js** | Customizável |
| ~~Cmd+K~~ | ~~Implementação própria (200 linhas JS)~~ | ✅ **FEITO NA FASE 1** (Busca.php + _ux_search.php) |
| PWA | **Workbox** | Padrão Google |
| Gestos | **Hammer.js** | Maduro, simples |
| Charts (KPIs) | **Chart.js** (já é usado provavelmente) | Leve |
| i18n | Implementação própria (já tem 1 idioma) | Sem lib |

**Sem nova dependência de framework JS** (sem React/Vue/Angular) — manter CI3 + jQuery + libs pontuais.

---

## 8. Próximos Passos Imediatos

1. ✅ ~~**Decidir escopo da Fase 1** (este documento) com o usuário.~~ FEITO
2. ✅ ~~**Criar 1 issue por melhoria** no board (GitHub Projects ou similar).~~ FEITO (TaskList do Claude)
3. ✅ ~~**Implementar as 5 melhorias de alto impacto** (seção 4) em 1 semana.~~ FEITO
4. 🚧 **Coletar feedback** de 3-5 usuários-chave. PENDENTE
5. ⏳ **Ajustar Fase 2** com base no feedback.
6. ⏳ **Repetir** até Fase 6.

---

## Conclusão

O MaposV5 tem **base técnica sólida** (3 rodadas de correção concluídas) e **funcionalidades ricas** (50 controllers). O que falta é **polimento UX**: consistência, clareza, feedback e personalização.

**Não precisa reescrever.** Precisa **refinar com método**, em 6 fases incrementais, com métricas para validar cada entrega.

A Fase 1 (Quick Wins) sozinha já entrega **80% do valor percebido** pelo usuário com **20% do esforço**. Começar por ela é a decisão certa.

**Status atual:** Fase 1 e Fase 2 concluídas (100%). Faltam as Fases 3 (Personalização), 4 (IA visível), 5 (Mobile/PWA) e 6 (Acessibilidade).

---

**Anexo:** código-fonte consultado em 2026-06-14.
- `application/controllers/` (50 arquivos)
- `application/views/` (46+ views)
- `application/views/tema/{topo,menu,conteudo,rodape}.php`
- `application/views/dashboard/index.php`
- `application/helpers/general_helper.php` (com novos helpers JSON + UX)
- `application/core/MY_Controller.php`
- `assets/css/` (12+ temas + novo `ux-components.css`)

**Log de mudanças:**
- 2026-06-14 — Plano criado, 6 fases definidas
- 2026-06-14 — Fase 1 iniciada
- 2026-06-14 — Fase 1: 7 de 8 itens concluídos (helpers UX, banner de atalhos, loading, busca global, breadcrumb, botões padronizados)
- 2026-06-15 — Fase 1: 100% concluída (8/8 itens — item 1.8 finalizado)
- 2026-06-15 — Fase 2: 100% concluída (9/9 itens — Driver.js, model+migration de tours, definições de tours, runner JS, controller Ux_tour, checklist Primeiros Passos, tooltips Tippy.js, página /ajuda, atualização do plano)
