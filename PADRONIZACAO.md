# Guia de Padronização Visual e de Usabilidade — MaposV5 (Bootstrap 5.3+)

Este documento estabelece as diretrizes de design, layout e desenvolvimento para a modernização da interface do **MaposV5**. O objetivo é garantir um sistema com visual **clean**, **moderno**, **altamente atrativo** e de **fácil usabilidade**, integrando-se perfeitamente à arquitetura, componentes e lógicas já existentes na aplicação.

---

## 📌 1. Arquitetura e Estrutura de Arquivos Existentes

Toda modificação de layout ou implementação visual deve respeitar e se integrar à estrutura nativa do MaposV5 baseada em **CodeIgniter 3**:

* **Views de Layout Principal:**
  * Topo/Sidebar/Header: [topo.php](file:///c:/Users/thail/Desktop/IVMS-CRUD/MaposV5/application/views/tema/topo.php)
  * Rodapé/Scripts: [rodape.php](file:///c:/Users/thail/Desktop/IVMS-CRUD/MaposV5/application/views/tema/rodape.php)
* **Arquivos de Estilo (Assets):**
  * Estilos de Layout Base: `assets/css/matrix-style.css` e `matrix-media.css`
  * Folha de Estilo Customizada: [custom.css](file:///c:/Users/thail/Desktop/IVMS-CRUD/MaposV5/assets/css/custom.css) (onde novos estilos e correções pontuais devem ser adicionados)
  * Framework Base: `assets/css/bootstrap5.min.css` (Bootstrap 5) e `assets/css/bootstrap5.3.2.min.css`
  * Ícones: **Boxicons** (`unpkg.com/boxicons@2.1.4`) e **FontAwesome** integrados

---

## 🎨 2. Preservação dos Temas e Lógica de Negócio Existente

O MaposV5 possui um sistema de temas dinâmico baseado nas configurações do banco de dados (`$configuration['app_theme']`). Ao criar ou editar telas, os estilos devem se adaptar aos seguintes temas configurados no [topo.php](file:///c:/Users/thail/Desktop/IVMS-CRUD/MaposV5/application/views/tema/topo.php):

1. **Claro Padrão / White:** `tema-white.css`
2. **Pure Dark:** `tema-pure-dark.css`
3. **Dark Violet:** `tema-dark-violet.css`
4. **Dark Orange:** `tema-dark-orange.css`
5. **White Green:** `tema-white-green.css`
6. **White Black:** `tema-white-black.css`

### Integração Bootstrap 5.3 Color Modes
Os novos componentes cleans devem respeitar o tema global definido na tag `<body>` ou `<html>`. Toda folha de estilo customizada para elementos novos deve utilizar variáveis CSS nativas:
```css
/* Exemplo de uso de variáveis compatíveis com múltiplos temas */
.novo-card-clean {
  background-color: var(--funSider, #ffffff); /* Fallback para cor nativa do painel */
  color: var(--cinza0, #a4a6b3);
  border: 1px solid rgba(var(--sidebar-accent-rgb, 4, 103, 252), 0.15);
}
```

---

## 🧱 3. Preservação e Modernização de Componentes Customizados

O MaposV5 possui recursos avançados já desenvolvidos no [custom.css](file:///c:/Users/thail/Desktop/IVMS-CRUD/MaposV5/assets/css/custom.css). Estes componentes **devem ser mantidos intactos** e estendidos para as novas telas:

### 3.1 Menu Badges (Indicadores Dinâmicos no Menu)
Indicador com efeito pulse em gradiente utilizado para alertar técnicos online ou notificações no menu lateral:
* **Online:** `.menu-badge.online` (com animação de pulso verde via `@keyframes pulse-badge`).
* **Padrão:** `.menu-badge` (gradiente roxo/azul para contagens).
* *Ao criar novos menus, mantenha a classe `.menu-badge` dentro da tag `<a>` para preservar o layout.*

### 3.2 Melhorias na Sidebar (Sidebar Enhancements)
A barra lateral de navegação já possui efeitos visuais avançados e otimizações de transição:
* **Efeito Ativo:** A classe `.active` adiciona um gradiente azul suave com borda esquerda acentuada.
* **Tooltips Inteligentes (Estado Compactado):** Ao colapsar o menu, tooltips flutuantes elegantes são acionados (`.title-tooltip` com efeito *blur* e animação via `@keyframes tooltipFadeIn`).
* **Seções e Divisores:** Divisões com texto em caixa alta e espaçados (`.menu-divider` e `.menu-divider-sub`).

### 3.3 Formulários e Selects Globais Customizados
Os formulários do Mapos possuem selects altamente estilizados no [custom.css](file:///c:/Users/thail/Desktop/IVMS-CRUD/MaposV5/assets/css/custom.css):
* **Selects Padronizados:** Altura de `46px`, cantos arredondados (`rounded-3` / `10px`), ícone personalizado em formato de seta e foco suave integrado.
* **Floating Labels:** Ao usar campos com rótulos flutuantes do Bootstrap 5, assegure a harmonia com o estilo de inputs já estilizados do sistema.

### 3.4 Componentes Especiais Integrados
* **Wizard de Emissão NFS-e + Boleto:** Estrutura em etapas flutuantes (`.wizard-steps` e `.wizard-step-panel`) com estados `.active` e `.completed` em verde e azul.
* **Upload Progress Circular:** Usado no check-in de fotos (`.upload-progress-overlay`), animado via SVG e compatível com layouts responsivos de dispositivos móveis.

---

## ⚡ 4. Bibliotecas Atuais e Atalhos Globais de Teclado

O sistema já é muito produtivo e possui atalhos de teclado configurados via `shortcut.js` no arquivo de topo do sistema. Qualquer nova tela deve preservar esses atalhos para não prejudicar a usabilidade rápida do usuário administrativo:

| Atalho | Destino no Sistema |
| :--- | :--- |
| **ESC** | Retornar para a Página Inicial (Dashboard) |
| **F1** | Módulo de Clientes |
| **F2** | Módulo de Produtos |
| **F3** | Módulo de Serviços |
| **F4** | Módulo de Ordens de Serviço (OS) |
| **F6** | Adicionar Nova Venda |
| **F7** | Lançamentos Financeiros |

### 🛠️ Bibliotecas de Suporte Integradas
* **jQuery 3.7.1:** Biblioteca base carregada localmente para manipulação de DOM e requisições AJAX.
* **DataTables:** Utilizado para ordenação, busca e paginação inteligente de tabelas de forma dinâmica.
* **SweetAlert v1 (Legado):** Ainda utilizado no sistema para modais de confirmação e alertas rápidos, com migração gradual para SweetAlert2 recomendada.

---

## 📌 5. Snippets Modernos e Harmonizados com o MaposV5

Para criar novas interfaces que combinam a modernidade do **Bootstrap 5** com o visual clean e a alma do **MaposV5**, utilize as seguintes estruturas:

### Card Moderno para Widgets de Estatísticas
```html
<div class="card border-0 shadow-sm rounded-4 bg-body-tertiary">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <span class="text-secondary small fw-medium uppercase">Ordens de Serviço</span>
        <h3 class="fw-bold mb-0 mt-1">1.284</h3>
      </div>
      <div class="bg-primary-subtle text-primary p-3 rounded-3">
        <i class="bx bx-file fs-3"></i>
      </div>
    </div>
  </div>
</div>
```

### Inputs Modernos com Validação Nativa
```html
<div class="form-floating mb-3">
  <input type="text" class="form-control rounded-3 border-light-subtle" id="campo" placeholder="Ex: Map-OS" required>
  <label for="campo">Nome da OS</label>
  <div class="invalid-feedback">O nome da OS é obrigatório.</div>
</div>
```

### Tabelas Limpas com Hover e DataTables
```html
<div class="table-responsive rounded-3 border border-light-subtle shadow-sm">
  <table class="table table-hover align-middle mb-0 datatable">
    <thead class="table-light">
      <tr>
        <th class="text-center py-3">Cód.</th>
        <th class="py-3">Cliente</th>
        <th class="py-3">Status</th>
        <th class="text-end py-3">Total</th>
        <th class="text-center py-3">Ações</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="text-center fw-medium">#150</td>
        <td><strong>Thailan</strong><br><small class="text-muted">carlos@email.com</small></td>
        <td><span class="badge bg-success-subtle text-success rounded-pill">Finalizado</span></td>
        <td class="text-end fw-semibold">R$ 350,00</td>
        <td class="text-center">
          <button class="btn btn-sm btn-outline-secondary border-0"><i class="bx bx-show"></i></button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```
