<form name="config-form" id="config-form" action="do_install.php" method="post">

  <div class="section clearfix">
    <p>1. Por favor, insira as informações da sua conexão de <strong>banco de dados</strong>.</p>
    <hr />
    <div>
      <div class="form-group clearfix">
        <label for="host" class=" col-md-3">Host</label>
        <div class="col-md-9">
          <input type="text" value="" id="host" name="host" class="form-control" placeholder="Host de Banco de Dados (geralmente localhost)" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label for="dbuser" class=" col-md-3">Usuário</label>
        <div class=" col-md-9">
          <input type="text" value="" id="dbuser" name="dbuser" class="form-control" autocomplete="off" placeholder="Nome de usuário do banco de dados" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label for="dbpassword" class=" col-md-3">Senha</label>
        <div class=" col-md-9">
          <input type="password" value="" id="dbpassword" name="dbpassword" class="form-control" autocomplete="off" placeholder="Senha do usuário do banco de dados" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label for="dbname" class=" col-md-3">Banco de Dados</label>
        <div class=" col-md-9">
          <input type="text" value="" id="dbname" name="dbname" class="form-control" placeholder="Nome do banco de dados" />
        </div>
      </div>
    </div>
  </div>

  <div class="section clearfix">
    <p>2. Por favor, insira as informações para sua conta de <strong>administrador</strong>.</p>
    <hr />
    <div>
      <div class="form-group clearfix">
        <label for="full_name" class=" col-md-3">Nome</label>
        <div class="col-md-9">
          <input type="text" value="" id="full_name" name="full_name" class="form-control" placeholder="Nome completo" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label for="email" class=" col-md-3">Email</label>
        <div class=" col-md-9">
          <input type="text" value="" id="email" name="email" class="form-control" placeholder="Seu email" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label for="password" class=" col-md-3">Senha</label>
        <div class=" col-md-9">
          <input type="password" value="" id="password" name="password" class="form-control" placeholder="Senha de login" />
        </div>
      </div>
    </div>
  </div>

  <div class="section clearfix">
    <p>3. Por favor, insira a URL.</p>
    <hr />
    <div>
      <div class="form-group clearfix">
        <div class="form-group clearfix">
          <label for="base_url" class=" col-md-3">URL</label>
          <div class="col-md-9">
            <input type="text" value="" id="base_url" name="base_url" class="form-control" placeholder="URL do sistema" />
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section clearfix">
    <p>4. REST API (Usada no aplicativo).</p>
    <hr />
    <div>
      <div class="form-group clearfix">
        <label for="enter_api_enabled" class=" col-md-3">Habilitar?</label>
        <div class="col-md-9">
          <select name="enter_api_enabled" id="" autocomplete="off">
            <option value="true">Sim</option>
            <option value="false" selected>Não</option>
          </select>
        </div>
      </div>
      <div class="form-group clearfix">
        <label for="enter_token_expire_time" class=" col-md-3">Expiração Token JWT</label>
        <div class="col-md-9">
          <select name="enter_token_expire_time" id="" autocomplete="off">
            <option value="60">1 minuto</option>
            <option value="3600">1 hora</option>
            <option value="86400" selected>1 dia</option>
            <option value="604800">1 semana</option>
            <option value="2592000">1 mês</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Barra de Progresso da Instalação -->
  <div id="install-progress-container" class="section clearfix hide">
    <hr />
    <div class="form-group">
      <label><strong>Progresso da Instalação</strong></label>
      <div class="progress" style="height: 30px; margin-bottom: 10px;">
        <div id="install-progress-bar" class="progress-bar progress-bar-danger progress-bar-striped active"
             role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
          <span id="install-progress-text">0%</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <small id="install-progress-message" class="text-muted"><i class="fa fa-hourglass-start"></i> Aguardando início...</small>
        </div>
        <div class="col-md-6 text-right">
          <small id="install-progress-step" class="text-info">Etapa: Aguardando...</small>
        </div>
      </div>
    </div>

    <!-- Lista de etapas -->
    <div class="row" style="margin-top: 15px;">
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h5 class="panel-title"><i class="fa fa-list-ol"></i> Etapas da Instalação</h5>
          </div>
          <div class="panel-body" style="padding: 10px;">
            <div class="row text-center">
              <div class="col-xs-3" id="step-1">
                <div class="step-item">
                  <i class="fa fa-check-circle-o" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>1. Validação</small></div>
                </div>
              </div>
              <div class="col-xs-3" id="step-2">
                <div class="step-item">
                  <i class="fa fa-database" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>2. Conexão DB</small></div>
                </div>
              </div>
              <div class="col-xs-3" id="step-3">
                <div class="step-item">
                  <i class="fa fa-table" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>3. Tabelas Base</small></div>
                </div>
              </div>
              <div class="col-xs-3" id="step-4">
                <div class="step-item">
                  <i class="fa fa-plus-square" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>4. Tabelas V5</small></div>
                </div>
              </div>
            </div>
            <div class="row text-center" style="margin-top: 10px;">
              <div class="col-xs-3" id="step-5">
                <div class="step-item">
                  <i class="fa fa-bar-chart" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>5. Dados DRE</small></div>
                </div>
              </div>
              <div class="col-xs-3" id="step-6">
                <div class="step-item">
                  <i class="fa fa-percent" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>6. Impostos</small></div>
                </div>
              </div>
              <div class="col-xs-3" id="step-7">
                <div class="step-item">
                  <i class="fa fa-key" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>7. Permissões</small></div>
                </div>
              </div>
              <div class="col-xs-3" id="step-8">
                <div class="step-item">
                  <i class="fa fa-file-text" style="font-size: 20px; color: #ccc;"></i>
                  <div><small>8. Config .env</small></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .step-item {
      padding: 8px;
      border-radius: 5px;
      transition: all 0.3s ease;
    }
    .step-item.active {
      background-color: #d9edf7;
    }
    .step-item.completed {
      background-color: #dff0d8;
    }
    .step-item.error {
      background-color: #f2dede;
    }
    .step-item.active i {
      color: #31708f !important;
    }
    .step-item.completed i {
      color: #3c763d !important;
    }
    .step-item.error i {
      color: #a94442 !important;
    }
    .progress {
      box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
      border-radius: 4px;
      background-color: #f5f5f5;
    }
    .progress-bar {
      transition: width 0.6s ease;
    }
  </style>


  <div class="panel-footer">
    <button type="submit" class="btn btn-info form-next">
      <span class="loader hide"> Por favor, espere...</span>
      <span class="button-text"><i class='fa fa-chevron-right'></i> Iniciar Instalação</span>
    </button>
  </div>

</form>
