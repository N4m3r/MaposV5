<div id="content">
<!--start-top-serch-->
  <div id="content-header">
   <div></div>
      <?php
      // Fase 1.1 - Breadcrumb padronizado
      $bc_items = [];
      $seg1 = $this->uri->segment(1);
      $seg2 = $this->uri->segment(2);
      $seg3 = $this->uri->segment(3);
      if ($seg1) {
          $bc_items[] = ['label' => ucfirst(str_replace('_', ' ', $seg1)), 'url' => base_url() . 'index.php/' . $seg1, 'icon' => 'bx-folder'];
      }
      if ($seg2) {
          $bc_items[] = ['label' => ucfirst(str_replace('_', ' ', $seg2)), 'url' => base_url() . 'index.php/' . $seg1 . '/' . $seg2, 'icon' => 'bx-folder-open'];
      }
      if ($seg3) {
          $bc_items[] = ['label' => ucfirst(str_replace('_', ' ', $seg3))];
      }
      echo breadcrumb($bc_items);
      ?>
    </div>
    <div class="container-flu">
      <div class="row">
        <div class="col-12">
          <?php
          // Fase 1.4 - Notificacoes orientadas a acao
          if ($var = $this->session->flashdata('success')) {
              echo notify(strip_tags($var), 'success');
          }
          if ($var = $this->session->flashdata('error')) {
              echo notify(strip_tags($var), 'error');
          }
          if ($var = $this->session->flashdata('warning')) {
              echo notify(strip_tags($var), 'warning');
          }
          if ($var = $this->session->flashdata('info')) {
              echo notify(strip_tags($var), 'info');
          }
          // Fallback para SweetAlert caso JS esteja disponivel (mantem compat)
          if ($this->session->flashdata('success') || $this->session->flashdata('error')) {
              $s = $this->session->flashdata('success');
              $e = $this->session->flashdata('error');
              if ($s) echo '<script>swal("Sucesso!", ' . json_encode(strip_tags($s)) . ', "success");</script>';
              if ($e) echo '<script>swal("Falha!", ' . json_encode(strip_tags($e)) . ', "error");</script>';
          }
          ?>
          <?php
          if (isset($view)) {
              // Preparar dados para a view - incluir todas as variáveis disponíveis
              $view_data = [];

              // Capturar todas as variáveis definidas nesta view
              $vars = get_defined_vars();
              foreach ($vars as $key => $value) {
                  if ($key !== 'view' && $key !== 'var') {
                      $view_data[$key] = $value;
                  }
              }

              echo $this->load->view($view, $view_data, true);
          }
          ?>
        </div>
      </div>
    </div>
  </div>
