<div id="content">
<!--start-top-serch-->
  <div id="content-header">
   <div></div>
      <div id="breadcrumb">
        <a href="<?= base_url() ?>" title="Dashboard" class="tip-bottom"> Início</a>
        <?php if ($this->uri->segment(1) != null) { ?>
            <a href="<?= base_url() . 'index.php/' . $this->uri->segment(1) ?>" class="tip-bottom" title="<?= ucfirst($this->uri->segment(1)); ?>">
              <?= ucfirst($this->uri->segment(1)); ?>
            </a>
          <?php if ($this->uri->segment(2) != null) { ?>
            <a href="<?= base_url() . 'index.php/' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>" class="current tip-bottom" title="<?= ucfirst($this->uri->segment(2)); ?>">
              <?= ucfirst($this->uri->segment(2));
          } ?>
            </a>
          <?php } ?>
      </div>
    </div>
    <div class="container-flu">
      <div class="row-fluid">
        <div class="span12">
          <?php if ($var = $this->session->flashdata('success')): ?><script>swal("Sucesso!", "<?php echo str_replace('"', '', $var); ?>", "success");</script><?php endif; ?>
          <?php if ($var = $this->session->flashdata('error')): ?><script>swal("Falha!", "<?php echo str_replace('"', '', $var); ?>", "error");</script><?php endif; ?>
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

              // DEBUG: Mostrar dados disponíveis se for página de atividades
              if (strpos($view, 'atividades') !== false) {
                  echo '<div style="background: #2c3e50; color: #fff; padding: 10px; margin-bottom: 10px; font-size: 12px;">';
                  echo '<strong>DEBUG conteudo.php:</strong> ';
                  echo 'view=' . $view . ' | ';
                  echo 'obra existe: ' . (isset($view_data['obra']) ? 'SIM' : 'NÃO') . ' | ';
                  echo 'atividades existe: ' . (isset($view_data['atividades']) ? 'SIM' : 'NÃO') . ' (' . (isset($view_data['atividades']) ? count($view_data['atividades']) : 0) . ')';
                  echo '</div>';
              }

              echo $this->load->view($view, $view_data, true);
          }
          ?>
        </div>
      </div>
    </div>
  </div>
