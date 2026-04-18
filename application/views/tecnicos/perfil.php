<!-- Perfil - Portal do Técnico -->
<div class="row-fluid">
    <div class="span12">

        <!-- Header com Avatar -->
        <div class="profile-header">
            <div class="profile-avatar" onclick="abrirCamera()">
                <?php if (!empty($tecnico->foto_tecnico) && file_exists(FCPATH . $tecnico->foto_tecnico)): ?>
                    <img src="<?php echo base_url($tecnico->foto_tecnico); ?>?v=<?php echo time(); ?>" alt="Foto">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <i class="bx bx-user"></i>
                    </div>
                <?php endif; ?>
                <div class="avatar-overlay">
                    <i class="bx bx-camera"></i>
                    <span>Alterar</span>
                </div>
            </div>

            <h3 class="profile-name"><?php echo htmlspecialchars($tecnico->nome ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h3>
            <p class="profile-level">
                <span class="label label-info">
                    <i class="bx bx-star"></i> Técnico Nível <?php echo $tecnico->nivel_tecnico; ?>
                </span>
            </p>
        </div>

        <!-- Informações Pessoais -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-user"></i></span>
                <h5>Informações Pessoais</h5>
            </div>
            <div class="widget-content">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-envelope"></i> E-mail
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($tecnico->email ?? '', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-phone"></i> Telefone
                        </span>
                        <span class="info-value">
                            <?php echo htmlspecialchars($tecnico->telefone ?? 'Não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-id-card"></i> CPF
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
                <span class="icon"><i class="bx bx-wrench"></i></span>
                <h5>Informações Profissionais</h5>
            </div>
            <div class="widget-content">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-star"></i> Nível
                        </span>
                        <span class="label label-primary">Nível <?php echo $tecnico->nivel_tecnico; ?></span>
                    </div>

                    <?php if ($tecnico->especialidades): ?>
                    <div class="info-item info-item-block">
                        <span class="info-label">
                            <i class="bx bx-bulb"></i> Especialidades
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
                            <i class="bx bx-car"></i> Veículo
                        </span>
                        <span class="info-value">
                            <?php echo htmlspecialchars(($tecnico->veiculo_tipo ?? '') . ' - ' . ($tecnico->veiculo_placa ?? ''), ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-time"></i> Plantão 24h
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
                <span class="icon"><i class="bx bx-cog"></i></span>
                <h5>Configurações</h5>
            </div>
            <div class="widget-content">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-time-five"></i> Último acesso
                        </span>
                        <span class="info-value">
                            <?php echo $tecnico->ultimo_acesso_app ? date('d/m/Y H:i', strtotime($tecnico->ultimo_acesso_app)) : 'Nunca'; ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            <i class="bx bx-mobile"></i> Versão do App
                        </span>
                        <span class="info-value">v1.0.0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div class="logout-section">
            <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn btn-danger btn-large btn-block">
                <i class="bx bx-log-out"></i> Sair do Sistema
            </a>
        </div>

    </div>
</div>

<!-- Modal da Câmera -->
<div class="modal hide" id="cameraModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharCamera()">×</button>
        <h3>Atualizar Foto</h3>
    </div>
    <div class="modal-body text-center">
        <video id="video" autoplay playsinline style="width: 100%; max-width: 400px; border-radius: 8px;"></video>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" onclick="fecharCamera()">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="capturarFoto()">
            <i class="bx bx-camera"></i> Capturar
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

<script>
let stream = null;

async function abrirCamera() {
    jQuery('#cameraModal').modal('show');
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user' }
        });
        document.getElementById('video').srcObject = stream;
    } catch (err) {
        console.error('Erro ao abrir câmera:', err);
        alert('Não foi possível acessar a câmera');
    }
}

function fecharCamera() {
    jQuery('#cameraModal').modal('hide');
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

async function capturarFoto() {
    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const foto = canvas.toDataURL('image/jpeg', 0.8);

    const formData = new FormData();
    formData.append('foto', foto);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

    try {
        const response = await fetch('<?php echo site_url("tecnicos/atualizar_foto"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Atualizar preview
            document.querySelector('.profile-avatar').innerHTML = `<img src="${foto}"><div class="avatar-overlay"><i class="bx bx-camera"></i><span>Alterar</span></div>`;
            fecharCamera();
        } else {
            alert('Erro ao atualizar foto');
        }
    } catch (err) {
        alert('Erro ao enviar foto: ' + err.message);
    }
}
</script>
