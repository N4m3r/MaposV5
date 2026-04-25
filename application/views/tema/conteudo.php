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
          <?php if ($var = $this->session->flashdata('success')): ?><script>swal("Sucesso!", <?php echo json_encode(strip_tags($var)); ?>, "success");</script><?php endif; ?>
          <?php if ($var = $this->session->flashdata('error')): ?><script>swal("Falha!", <?php echo json_encode(strip_tags($var)); ?>, "error");</script><?php endif; ?>
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
