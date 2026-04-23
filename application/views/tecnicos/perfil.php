<!-- Perfil - Portal do Técnico -->
<div id="content">

<!-- DEBUG CONSOLE - Remover em produção -->
<div id="debugConsole" style="position:fixed; bottom:10px; right:10px; background:#333; color:#0f0; padding:10px; font-family:monospace; font-size:12px; max-width:400px; max-height:200px; overflow:auto; z-index:99999; border-radius:5px; display:block;">
  <strong>DEBUG JS:</strong> <button onclick="document.getElementById('debugConsole').style.display='none'" style="float:right;color:red;">X</button>
  <div id="debugOutput">Inicializando...</div>
</div>

<script type="text/javascript">
function logDebug(msg) {
    console.log('[DEBUG]', msg);
    var out = document.getElementById('debugOutput');
    if (out) {
        var time = new Date().toLocaleTimeString();
        out.innerHTML += time + ': ' + msg + '<br>';
    }
}
window.onerror = function(msg, url, line) {
    logDebug('ERRO JS: ' + msg + ' (linha ' + line + ')');
};
logDebug('Página de perfil carregada');
</script>

<style>
.portal-tecnico-content { margin-top: 15px !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 10px !important; } }
</style>

<div class="row-fluid portal-tecnico-content">
    <div class="span12">

        <!-- Header com Avatar -->
        <div class="profile-header">
            <div class="profile-avatar" onclick="abrirCamera()">
                <?php if (!empty($tecnico->foto_tecnico) && file_exists(FCPATH . $tecnico->foto_tecnico)): ?>
                    <img src="<?php echo base_url($tecnico->foto_tecnico); ?>?v=<?php echo time(); ?>" alt="Foto">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <i class="icon icon-user"></i>
                    </div>
                <?php endif; ?>
                <div class="avatar-overlay">
                    <i class="icon icon-camera"></i>
                    <span>Alterar</span>
                </div>
            </div>

            <h3 class="profile-name"><?php echo htmlspecialchars($tecnico->nome ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h3>
            <p class="profile-level">
                <span class="label label-info">
                    <i class="icon icon-star"></i> Técnico Nível <?php echo $tecnico->nivel_tecnico; ?>
                </span>
            </p>
        </div>

        <!-- Informações Pessoais -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon icon-user"></i></span>
                <h5>Informações Pessoais</h5>
            </div>
            <div class="widget-content">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-envelope"></i> E-mail
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($tecnico->email ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-phone"></i> Telefone
                        </span>
                        <span class="info-value">
                            <?php echo htmlspecialchars($tecnico->telefone ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-credit-card"></i> CPF
                        </span>
                        <span class="info-value">
                            <?php echo htmlspecialchars($tecnico->cpf ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações Profissionais -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon icon-wrench"></i></span>
                <h5>Informações Profissionais</h5>
            </div>
            <div class="widget-content">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-star"></i> Nível
                        </span>
                        <span class="label label-primary">Nível <?php echo $tecnico->nivel_tecnico; ?></span>
                    </div>

                    <?php if ($tecnico->especialidades): ?>
                    <div class="info-item info-item-block">
                        <span class="info-label">
                            <i class="icon icon-lightbulb"></i> Especialidades
                        </span>
                        <div class="specialties-list">
                            <?php $especialidades = explode(',', $tecnico->especialidades); ?>
                            <?php foreach ($especialidades as $esp): ?>
                                <span class="specialty-tag"><?php echo trim(htmlspecialchars($esp, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($tecnico->veiculo_placa): ?>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-truck"></i> Veículo
                        </span>
                        <span class="info-value">
                            <?php echo htmlspecialchars(($tecnico->veiculo_tipo ?? '') . ' - ' . ($tecnico->veiculo_placa ?? ''), ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-time"></i> Plantão 24h
                        </span>
                        <span class="label <?php echo $tecnico->plantao_24h ? 'label-success' : 'label-default'; ?>">
                            <?php echo $tecnico->plantao_24h ? 'Sim' : 'Não'; ?>
                        </span>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-value">0</div>
                        <div class="stat-label">OS Hoje</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">0</div>
                        <div class="stat-label">OS Semana</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">0</div>
                        <div class="stat-label">OS Mês</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="icon icon-cog"></i></span>
                <h5>Configurações</h5>
            </div>
            <div class="widget-content">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-time"></i> Último acesso
                        </span>
                        <span class="info-value">
                            <?php echo $tecnico->ultimo_acesso_app ? date('d/m/Y H:i', strtotime($tecnico->ultimo_acesso_app)) : 'Nunca'; ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="icon icon-mobile-phone"></i> Versão do App
                        </span>
                        <span class="info-value">v1.0.0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div class="logout-section">
            <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn btn-danger btn-large btn-block">
                <i class="icon icon-signout"></i> Sair do Sistema
            </a>
        </div>

    </div>
</div>

<!-- Modal da Foto -->
<div class="modal hide" id="cameraModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharCamera()">×</button>
        <h3><i class="icon icon-camera"></i> Atualizar Foto</h3>
    </div>
    <div class="modal-body">
        <!-- Abas -->
        <ul class="nav nav-tabs" id="fotoTab">
            <li class="active"><a href="#tab-camera" data-toggle="tab"><i class="icon icon-camera"></i> Câmera</a></li>
            <li><a href="#tab-upload" data-toggle="tab"><i class="icon icon-upload"></i> Galeria</a></li>
        </ul>

        <div class="tab-content">
            <!-- Aba Câmera -->
            <div class="tab-pane active" id="tab-camera">
                <div class="text-center" style="padding: 20px;">
                    <video id="video" autoplay playsinline style="width: 100%; max-width: 400px; border-radius: 8px; display: none;"></video>
                    <div id="camera-off" style="padding: 40px; color: #888;">
                        <i class="icon icon-camera" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                        <p>Clique em "Iniciar Câmera" para tirar uma foto</p>
                        <button type="button" class="btn btn-primary" onclick="iniciarCamera()">
                            <i class="icon icon-camera"></i> Iniciar Câmera
                        </button>
                    </div>
                </div>
            </div>

            <!-- Aba Upload -->
            <div class="tab-pane" id="tab-upload">
                <div class="text-center" style="padding: 20px;">
                    <div id="upload-preview" style="margin-bottom: 15px; display: none;">
                        <img id="preview-img" src="" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                    </div>
                    <div id="upload-placeholder" style="padding: 40px; border: 2px dashed #ddd; border-radius: 8px; color: #888;">
                        <i class="icon icon-picture" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                        <p>Selecione uma foto da galeria</p>
                    </div>

                    <div class="file-input-wrapper" style="margin-top: 15px;">
                        <input type="file" id="input-foto" accept="image/*" style="display: none;" onchange="previewUpload(this)">
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('input-foto').click()">
                            <i class="icon icon-upload"></i> Selecionar Arquivo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" onclick="fecharCamera()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-salvar-foto" onclick="salvarFoto()" disabled>
            <i class="icon icon-ok"></i> Salvar Foto
        </button>
    </div>
</div>

<style>
/* Profile Header */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -20px -20px 20px -20px;
    padding: 40px 20px;
    text-align: center;
    color: white;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.3);
    margin: 0 auto 20px;
    overflow: hidden;
    background: white;
    position: relative;
    cursor: pointer;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
}

.avatar-placeholder i {
    font-size: 48px;
}

.avatar-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 8px;
    font-size: 0.8rem;
    opacity: 0;
    transition: opacity 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.profile-avatar:hover .avatar-overlay {
    opacity: 1;
}

.profile-name {
    margin: 0 0 10px 0;
    color: white;
}

.profile-level {
    margin: 0;
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item-block {
    flex-direction: column;
    align-items: flex-start;
}

.info-item-block .info-label {
    margin-bottom: 10px;
}

.info-label {
    color: #666;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-label i {
    color: #667eea;
    font-size: 1.1rem;
}

.info-value {
    font-weight: 600;
    color: #333;
}

/* Specialties */
.specialties-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.specialty-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

.stat-box {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
}

.stat-label {
    font-size: 0.75rem;
    color: #888;
    margin-top: 5px;
}

/* Logout Section */
.logout-section {
    margin-top: 20px;
    padding-bottom: 20px;
}

.logout-section .btn {
    padding: 15px;
}

/* Estilos do Modal de Foto */
#cameraModal .modal-body {
    padding: 0;
}

#cameraModal .nav-tabs {
    margin: 0;
    border-bottom: 1px solid #ddd;
}

#cameraModal .nav-tabs > li > a {
    padding: 12px 20px;
    color: #666;
    font-weight: 500;
}

#cameraModal .nav-tabs > li.active > a {
    color: #667eea;
    font-weight: 600;
    background: #f8f9ff;
}

#cameraModal .tab-content {
    padding: 0;
}

#cameraModal #upload-placeholder:hover,
#cameraModal #camera-off:hover {
    border-color: #667eea;
    background: #f8f9ff;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .profile-header {
        padding: 30px 15px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
    }

    .stats-grid {
        gap: 10px;
    }

    .stat-box {
        padding: 12px;
    }

    .stat-value {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>

<?php
// Preparar dados no PHP
$csrf_token_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$url_atualizar_foto = site_url("tecnicos/atualizar_foto");
?>

<script type="text/javascript">
var stream = null;
var URL_ATUALIZAR_FOTO = '<?php echo $url_atualizar_foto; ?>';
var CSRF_TOKEN_NAME = '<?php echo $csrf_token_name; ?>';
var CSRF_HASH = '<?php echo $csrf_hash; ?>';

// Variáveis para controle
var fotoCapturada = null;
var stream = null;

// Abrir modal de foto
function abrirCamera() {
    logDebug('Abrindo modal de foto...');
    fotoCapturada = null;
    document.getElementById('btn-salvar-foto').disabled = true;

    // Resetar abas
    jQuery('#fotoTab a:first').tab('show');

    // Resetar preview
    document.getElementById('video').style.display = 'none';
    document.getElementById('camera-off').style.display = 'block';

    // Resetar upload
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('input-foto').value = '';

    jQuery('#cameraModal').modal('show');
}

// Iniciar câmera
function iniciarCamera() {
    logDebug('Iniciando câmera...');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        logDebug('ERRO: getUserMedia não suportado');
        alert('Seu navegador não suporta acesso à câmera');
        return;
    }

    navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user' }
    }).then(function(mediaStream) {
        stream = mediaStream;
        var video = document.getElementById('video');
        if (video) {
            video.srcObject = stream;
            video.style.display = 'block';
            document.getElementById('camera-off').style.display = 'none';
        }
        logDebug('Câmera aberta com sucesso');
        habilitarSalvar();
    }).catch(function(err) {
        logDebug('Erro ao abrir câmera: ' + err.message);
        console.error('Erro ao abrir câmera:', err);
        alert('Não foi possível acessar a câmera');
    });
}

// Preview de upload
function previewUpload(input) {
    logDebug('Arquivo selecionado...');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            fotoCapturada = e.target.result;
            document.getElementById('preview-img').src = fotoCapturada;
            document.getElementById('upload-preview').style.display = 'block';
            document.getElementById('upload-placeholder').style.display = 'none';
            habilitarSalvar();
            logDebug('Preview carregado');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Habilitar botão salvar
function habilitarSalvar() {
    document.getElementById('btn-salvar-foto').disabled = false;
}

// Capturar foto da câmera
function capturarDaCamera() {
    var video = document.getElementById('video');
    if (!video || video.style.display === 'none') {
        logDebug('Câmera não está ativa');
        alert('Inicie a câmera primeiro');
        return null;
    }

    var canvas = document.createElement('canvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    var ctx = canvas.getContext('2d');
    if (ctx) {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    }

    return canvas.toDataURL('image/jpeg', 0.8);
}

// Salvar foto (tanto da câmera quanto do upload)
function salvarFoto() {
    logDebug('Salvando foto...');

    // Verificar qual aba está ativa
    var abaAtiva = jQuery('#fotoTab .active a').attr('href');
    var foto = null;

    if (abaAtiva === '#tab-camera') {
        foto = capturarDaCamera();
        if (!foto) {
            alert('Inicie a câmera e aguarde o vídeo carregar');
            return;
        }
    } else {
        foto = fotoCapturada;
        if (!foto) {
            alert('Selecione uma imagem primeiro');
            return;
        }
    }

    logDebug('Enviando foto...');

    var formData = new FormData();
    formData.append('foto', foto);
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

    fetch(URL_ATUALIZAR_FOTO, {
        method: 'POST',
        body: formData
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        logDebug('Resposta recebida: ' + JSON.stringify(data));
        if (data.success) {
            // Atualizar preview
            var avatar = document.querySelector('.profile-avatar');
            if (avatar) {
                avatar.innerHTML = '<img src="' + foto + '" alt="Foto"><div class="avatar-overlay"><i class="icon icon-camera"></i><span>Alterar</span></div>';
            }
            fecharCamera();
            logDebug('Foto atualizada com sucesso');
            alert('Foto atualizada com sucesso!');
        } else {
            logDebug('ERRO ao atualizar foto: ' + (data.message || 'Erro desconhecido'));
            alert('Erro ao atualizar foto: ' + (data.message || 'Erro desconhecido'));
        }
    }).catch(function(err) {
        logDebug('ERRO ao enviar foto: ' + err.message);
        alert('Erro ao enviar foto: ' + err.message);
    });
}

// Fechar modal
function fecharCamera() {
    logDebug('Fechando modal...');
    jQuery('#cameraModal').modal('hide');
    if (stream) {
        var tracks = stream.getTracks();
        for (var i = 0; i < tracks.length; i++) {
            tracks[i].stop();
        }
        stream = null;
    }
}

logDebug('JavaScript do perfil carregado');
</script>
</div>
