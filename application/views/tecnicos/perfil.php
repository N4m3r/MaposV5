<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Perfil do Técnico - Design Moderno -->
<style>
/* Container Principal */
.perfil-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

/* Header do Perfil */
.perfil-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.perfil-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

/* Avatar */
.perfil-avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.perfil-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.3);
    overflow: hidden;
    background: white;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
}

.perfil-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 40px rgba(0,0,0,0.3);
}

.perfil-avatar img {
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
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
    color: #667eea;
}

.avatar-placeholder i {
    font-size: 60px;
}

.avatar-edit-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 40px;
    height: 40px;
    background: #27ae60;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    border: 3px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    z-index: 10;
}

.avatar-edit-badge:hover {
    background: #229954;
    transform: scale(1.1);
}

/* Nome e Nível */
.perfil-nome {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    position: relative;
    z-index: 1;
}

.perfil-nivel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.perfil-nivel i {
    color: #ffd700;
}

/* Cards de Seção */
.perfil-section {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.perfil-section:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

/* Grid de Info */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
    transition: all 0.3s ease;
}

.info-card:hover {
    background: #e8f4f8;
    transform: translateY(-3px);
}

.info-card-icon {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-size: 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    flex-shrink: 0;
}

.info-card-content {
    flex: 1;
    min-width: 0;
}

.info-card-label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.info-card-value {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    word-break: break-word;
}

/* Especialidades */
.especialidades-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.especialidade-tag {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.especialidade-tag i {
    font-size: 12px;
}

/* Stats Cards */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e8e8e8 100%);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-card.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.stat-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
.stat-card.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }

.stat-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    opacity: 0.9;
}

/* Plantão Badge */
.plantao-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
}

.plantao-badge.ativo {
    background: #d4edda;
    color: #155724;
}

.plantao-badge.inativo {
    background: #f8f9fa;
    color: #666;
}

/* Botão Sair */
.btn-logout {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(238, 90, 90, 0.3);
}

.btn-logout:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(238, 90, 90, 0.4);
    text-decoration: none;
    color: white;
}

/* Modal de Foto */
.foto-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px 6px 0 0;
}

.foto-modal .modal-header h3 {
    margin: 0;
    font-weight: 600;
}

.foto-modal .nav-tabs {
    border-bottom: 2px solid #eee;
    margin-bottom: 20px;
}

.foto-modal .nav-tabs > li > a {
    padding: 15px 25px;
    font-weight: 600;
    color: #666;
    border: none;
    background: transparent;
}

.foto-modal .nav-tabs > li.active > a {
    color: #667eea;
    border-bottom: 3px solid #667eea;
    background: #f8f9ff;
}

.foto-preview-area {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    margin-bottom: 20px;
    border: 2px dashed #ddd;
    transition: all 0.3s ease;
}

.foto-preview-area:hover {
    border-color: #667eea;
    background: #f0f4ff;
}

.foto-preview-area i {
    font-size: 48px;
    color: #667eea;
    margin-bottom: 15px;
    display: block;
}

.foto-preview-area p {
    color: #888;
    margin: 0;
}

.video-container {
    position: relative;
    display: inline-block;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

#video {
    max-width: 100%;
    max-height: 300px;
    display: block;
}

.btn-capturar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 15px;
}

.btn-capturar:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

#preview-img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

/* Responsividade */
@media (max-width: 768px) {
    .perfil-container {
        padding: 15px;
    }

    .perfil-header {
        padding: 30px 20px;
        border-radius: 16px;
    }

    .perfil-avatar {
        width: 120px;
        height: 120px;
    }

    .perfil-nome {
        font-size: 22px;
    }

    .stats-row {
        grid-template-columns: 1fr;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .perfil-avatar {
        width: 100px;
        height: 100px;
    }

    .section-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="perfil-container">
    <!-- Header com Avatar -->
    <div class="perfil-header">
        <div class="perfil-avatar-wrapper">
            <div class="perfil-avatar" onclick="abrirCamera()">
                <?php if (!empty($tecnico->foto_tecnico) && file_exists(FCPATH . $tecnico->foto_tecnico)): ?>
                    <img src="<?php echo base_url($tecnico->foto_tecnico); ?>?v=<?php echo time(); ?>" alt="Foto">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <i class="icon icon-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="avatar-edit-badge" onclick="abrirCamera()" title="Alterar Foto">
                <i class="icon icon-camera"></i>
            </div>
        </div>

        <h3 class="perfil-nome"><?php echo htmlspecialchars($tecnico->nome ?? 'Técnico', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h3>

        <div class="perfil-nivel">
            <i class="icon icon-star"></i>
            <span>Técnico Nível <?php echo $tecnico->nivel_tecnico ?? 1; ?></span>
        </div>
    </div>

    <!-- Informações Pessoais -->
    <div class="perfil-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="icon icon-user"></i>
            </div>
            <h4 class="section-title">Informações Pessoais</h4>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-envelope"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">E-mail</div>
                    <div class="info-card-value"><?php echo htmlspecialchars($tecnico->email ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-phone"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Telefone</div>
                    <div class="info-card-value"><?php echo htmlspecialchars($tecnico->telefone ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-credit-card"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">CPF</div>
                    <div class="info-card-value"><?php echo htmlspecialchars($tecnico->cpf ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações Profissionais -->
    <div class="perfil-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="icon icon-wrench"></i>
            </div>
            <h4 class="section-title">Informações Profissionais</h4>
        </div>

        <div class="info-grid">
            <?php if (!empty($tecnico->especialidades)): ?>
            <div class="info-card" style="grid-column: 1 / -1;">
                <div class="info-card-icon">
                    <i class="icon icon-lightbulb"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Especialidades</div>
                    <div class="especialidades-list">
                        <?php foreach (explode(',', $tecnico->especialidades) as $esp): ?>
                            <span class="especialidade-tag">
                                <i class="icon icon-ok"></i>
                                <?php echo trim(htmlspecialchars($esp, ENT_COMPAT | ENT_HTML5, 'UTF-8')); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($tecnico->veiculo_placa): ?>
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-truck"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Veículo</div>
                    <div class="info-card-value">
                        <?php echo htmlspecialchars(($tecnico->veiculo_tipo ?? '') . ' - ' . ($tecnico->veiculo_placa ?? ''), ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-time"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Plantão 24h</div>
                    <div class="info-card-value">
                        <span class="plantao-badge <?php echo ($tecnico->plantao_24h ?? 0) ? 'ativo' : 'inativo'; ?>">
                            <i class="icon <?php echo ($tecnico->plantao_24h ?? 0) ? 'icon-ok' : 'icon-remove'; ?>"></i>
                            <?php echo ($tecnico->plantao_24h ?? 0) ? 'Disponível' : 'Indisponível'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-row">
            <div class="stat-card blue">
                <div class="stat-value">0</div>
                <div class="stat-label">OS Hoje</div>
            </div>
            <div class="stat-card green">
                <div class="stat-value">0</div>
                <div class="stat-label">OS Semana</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-value">0</div>
                <div class="stat-label">OS Mês</div>
            </div>
        </div>
    </div>

    <!-- Configurações -->
    <div class="perfil-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="icon icon-cog"></i>
            </div>
            <h4 class="section-title">Configurações</h4>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-time"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Último Acesso</div>
                    <div class="info-card-value">
                        <?php echo ($tecnico->ultimo_acesso_app ?? false) ? date('d/m/Y H:i', strtotime($tecnico->ultimo_acesso_app)) : 'Nunca acessou'; ?>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-icon">
                    <i class="icon icon-mobile-phone"></i>
                </div>
                <div class="info-card-content">
                    <div class="info-card-label">Versão do App</div>
                    <div class="info-card-value">v1.0.0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão Sair -->
    <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn-logout">
        <i class="icon icon-signout"></i>
        <span>Sair do Sistema</span>
    </a>
</div>

<!-- Modal de Foto -->
<div class="modal hide foto-modal" id="cameraModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharCamera()" style="color: white; opacity: 0.8;">&times;</button>
        <h3><i class="icon icon-camera"></i> Atualizar Foto de Perfil</h3>
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
                <div class="foto-preview-area" id="camera-off">
                    <i class="icon icon-camera"></i>
                    <p>Clique no botão abaixo para iniciar a câmera</p>
                    <button type="button" class="btn-capturar" onclick="iniciarCamera()">
                        <i class="icon icon-camera"></i> Iniciar Câmera
                    </button>
                </div>
                <div class="text-center" id="camera-on" style="display: none;">
                    <div class="video-container">
                        <video id="video" autoplay playsinline></video>
                    </div>
                    <button type="button" class="btn-capturar" onclick="capturarDaCamera()">
                        <i class="icon icon-camera"></i> Tirar Foto
                    </button>
                </div>
            </div>

            <!-- Aba Upload -->
            <div class="tab-pane" id="tab-upload">
                <div class="foto-preview-area" id="upload-placeholder">
                    <i class="icon icon-picture"></i>
                    <p>Selecione uma foto da galeria</p>
                    <input type="file" id="input-foto" accept="image/*" style="display: none;" onchange="previewUpload(this)">
                    <button type="button" class="btn-capturar" onclick="document.getElementById('input-foto').click()">
                        <i class="icon icon-folder-open"></i> Selecionar Arquivo
                    </button>
                </div>
                <div class="text-center" id="upload-preview" style="display: none;">
                    <img id="preview-img" src="" alt="Preview">
                    <br><br>
                    <button type="button" class="btn" onclick="resetUpload()">
                        <i class="icon icon-remove"></i> Escolher Outra
                    </button>
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

<?php
// Dados para JavaScript
$csrf_token_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$url_atualizar_foto = site_url("tecnicos/atualizar_foto");
?>

<script type="text/javascript">
// Variáveis globais
var stream = null;
var fotoCapturada = null;
var URL_ATUALIZAR_FOTO = '<?php echo $url_atualizar_foto; ?>';
var CSRF_TOKEN_NAME = '<?php echo $csrf_token_name; ?>';
var CSRF_HASH = '<?php echo $csrf_hash; ?>';

// Abrir modal de foto
function abrirCamera() {
    fotoCapturada = null;
    document.getElementById('btn-salvar-foto').disabled = true;

    // Resetar abas
    jQuery('#fotoTab a:first').tab('show');

    // Resetar câmera
    document.getElementById('camera-off').style.display = 'block';
    document.getElementById('camera-on').style.display = 'none';

    // Resetar upload
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('input-foto').value = '';

    jQuery('#cameraModal').modal('show');
}

// Iniciar câmera
function iniciarCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
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
            document.getElementById('camera-off').style.display = 'none';
            document.getElementById('camera-on').style.display = 'block';
            habilitarSalvar();
        }
    }).catch(function(err) {
        console.error('Erro ao abrir câmera:', err);
        alert('Não foi possível acessar a câmera');
    });
}

// Preview de upload
function previewUpload(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            fotoCapturada = e.target.result;
            document.getElementById('preview-img').src = fotoCapturada;
            document.getElementById('upload-placeholder').style.display = 'none';
            document.getElementById('upload-preview').style.display = 'block';
            habilitarSalvar();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Resetar upload
function resetUpload() {
    fotoCapturada = null;
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('input-foto').value = '';
    document.getElementById('btn-salvar-foto').disabled = true;
}

// Habilitar botão salvar
function habilitarSalvar() {
    document.getElementById('btn-salvar-foto').disabled = false;
}

// Capturar foto da câmera
function capturarDaCamera() {
    var video = document.getElementById('video');
    if (!video) return;

    var canvas = document.createElement('canvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    var ctx = canvas.getContext('2d');
    if (ctx) {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    }

    fotoCapturada = canvas.toDataURL('image/jpeg', 0.8);

    // Mostrar preview
    document.getElementById('preview-img').src = fotoCapturada;
    document.getElementById('upload-placeholder').style.display = 'none';
    document.getElementById('upload-preview').style.display = 'block';

    // Parar câmera e mudar para aba upload
    if (stream) {
        var tracks = stream.getTracks();
        for (var i = 0; i < tracks.length; i++) {
            tracks[i].stop();
        }
        stream = null;
    }

    // Mudar para aba de preview
    jQuery('#fotoTab a[href="#tab-upload"]').tab('show');
}

// Salvar foto
function salvarFoto() {
    if (!fotoCapturada) {
        alert('Selecione ou capture uma foto primeiro');
        return;
    }

    var formData = new FormData();
    formData.append('foto', fotoCapturada);
    formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

    fetch(URL_ATUALIZAR_FOTO, {
        method: 'POST',
        body: formData
    }).then(function(response) {
        return response.json();
    }).then(function(data) {
        if (data.success) {
            // Atualizar preview do avatar
            var avatar = document.querySelector('.perfil-avatar');
            if (avatar) {
                avatar.innerHTML = '<img src="' + fotoCapturada + '" alt="Foto">';
            }
            fecharCamera();
            alert('Foto atualizada com sucesso!');
        } else {
            alert('Erro ao atualizar foto: ' + (data.message || 'Erro desconhecido'));
        }
    }).catch(function(err) {
        alert('Erro ao enviar foto: ' + err.message);
    });
}

// Fechar modal
function fecharCamera() {
    jQuery('#cameraModal').modal('hide');
    if (stream) {
        var tracks = stream.getTracks();
        for (var i = 0; i < tracks.length; i++) {
            tracks[i].stop();
        }
        stream = null;
    }
}
</script>
