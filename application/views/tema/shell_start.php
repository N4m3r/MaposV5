<?php
/**
 * CoreUI AppShell — layout unificado para views PHP legadas.
 *
 * Uso: substitua o par `$this->load->view('tema/topo', $data)` + `$this->load->view('tema/rodape')`
 * por `$this->load->view('tema/shell_start', $data)` no inicio da action e
 * `$this->load->view('tema/shell_end')` no final. O conteudo da view fica
 * dentro de `.app-content`, ja preparado pelo shell.
 *
 * Vantagens:
 * - Sidebar dark (256px) + topbar CoreUI consistente com a versao React
 * - Tema via body[data-theme] ja propagado
 * - Notificacoes, busca, user dropdown ja no shell
 * - Mobile: sidebar vira offcanvas em <768px
 *
 * O conteudo da view de cada pagina e renderizado normalmente entre
 * shell_start e shell_end, dentro de <main class="app-content">.
 */
?>
<div class="app-shell">
    <?php $this->load->view('tema/_coreui_sidebar', $this->data ?? []); ?>
    <div class="app-main" id="app-main">
        <?php $this->load->view('tema/_coreui_topbar', $this->data ?? []); ?>
        <main class="app-content" id="content" tabindex="-1">
