<!-- Executar OS - Portal do Técnico -->
<style>
.portal-tecnico-content { margin-top: 15px !important; }
@media (max-width: 768px) { .portal-tecnico-content { margin-top: 10px !important; } }
</style>

<div class="row-fluid portal-tecnico-content">
    <div class="span12">

        <!-- Header -->
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-play-circle"></i></span>
                <h5>Executar OS #<?php echo $os->idOs; ?></h5>
                <div class="buttons">
                    <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="btn btn-mini">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="widget-content">

                <!-- Informações do Cliente -->
                <div class="row-fluid">
                    <div class="span12">
                        <div class="client-card">
                            <div class="client-avatar">
                                <i class="bx bx-user"></i>
                            </div>
                            <div class="client-info">
                                <h4><?php echo $cliente ? htmlspecialchars($cliente->nomeCliente, ENT_COMPAT | ENT_HTML5, 'UTF-8') : 'Cliente não encontrado'; ?></h4>
                                <?php if ($cliente): ?>
                                    <p><i class="bx bx-map"></i> <?php echo htmlspecialchars($cliente->endereco ?? 'Endereço não informado', ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></p>
                                    <?php if ($cliente->telefone): ?>
                                        <p><i class="bx bx-phone"></i> <?php echo htmlspecialchars($cliente->telefone, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botão de Check-in -->
                <div id="checkinSection" class="action-card <?php echo $execucao ? 'hidden' : ''; ?>">
                    <h5><i class="bx bx-map-pin"></i> Iniciar Atendimento</h5>

                    <div class="camera-section">
                        <div class="camera-preview" id="checkinPreview">
                            <i class="bx bx-camera"></i>
                            <span>Foto de Check-in</span>
                        </div>
                        <button type="button" class="btn btn-info" onclick="capturarFotoCheckin()" id="btnFotoCheckin">
                            <i class="bx bx-camera"></i> Tirar Foto
                        </button>
                    </div>

                    <button type="button" class="btn btn-success btn-large btn-block" onclick="iniciarExecucao()" id="btnIniciar">
                        <span class="spinner"></span>
                        <i class="bx bx-play-circle"></i>
                        <span class="text">Iniciar Execução</span>
                    </button>
                </div>

                <!-- Execução em Andamento -->
                <div id="execucaoSection" class=" <?php echo $execucao ? '' : 'hidden'; ?>">

                    <!-- Progresso -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-task"></i></span>
                            <h5>Progresso da Execução</h5>
                            <span class="label label-success">Em Execução</span>
                        </div>
                        <div class="widget-content">
                            <div class="progress">
                                <div class="bar bar-success" id="progressBar" style="width: <?php echo $execucao ? $execucao->progresso_execucao : 0; ?>%"></div>
                            </div>
                            <p class="text-center" id="progressText">
                                <?php echo $execucao ? $execucao->progresso_execucao : 0; ?>% concluído
                            </p>
                        </div>
                    </div>

                    <!-- Checklist -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-list-check"></i></span>
                            <h5>Checklist de Execução</h5>
                        </div>
                        <div class="widget-content">
                            <div id="checklistContainer">
                                <?php if (!empty($checklist)): ?>
                                    <?php foreach ($checklist as $index => $item): ?>
                                        <div class="checklist-item <?php echo $item['status'] ?? ''; ?>" data-item-id="<?php echo $index; ?>">
                                            <div class="checklist-header">
                                                <div class="checklist-checkbox">
                                                    <?php if ($item['status'] == 'conforme'): ?>
                                                        <i class="bx bx-check"></i>
                                                    <?php elseif ($item['status'] == 'nao_conforme'): ?>
                                                        <i class="bx bx-x"></i>
                                                    <?php else: ?>
                                                        <i class="bx bx-circle"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="checklist-text">
                                                    <h4><?php echo htmlspecialchars($item['descricao'] ?? $item, ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></h4>
                                                    <?php if (isset($item['servico'])): ?>
                                                        <p><?php echo htmlspecialchars($item['servico'], ENT_COMPAT | ENT_HTML5, 'UTF-8'); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="checklist-actions">
                                                <button type="button" class="btn btn-mini <?php echo ($item['status'] ?? '') == 'conforme' ? 'btn-success' : ''; ?>"
                                                        onclick="salvarChecklistItem(<?php echo $index; ?>, 'conforme')">
                                                    <i class="bx bx-check"></i> OK
                                                </button>
                                                <button type="button" class="btn btn-mini <?php echo ($item['status'] ?? '') == 'nao_conforme' ? 'btn-danger' : ''; ?>"
                                                        onclick="salvarChecklistItem(<?php echo $index; ?>, 'nao_conforme')">
                                                    <i class="bx bx-x"></i> Não OK
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <p>Nenhum item de checklist configurado</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Galeria de Fotos -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-camera"></i></span>
                            <h5>Fotos do Serviço</h5>
                        </div>
                        <div class="widget-content">
                            <div class="gallery-grid" id="galleryGrid">
                                <div class="gallery-add" onclick="abrirCamera()">
                                    <i class="bx bx-plus"></i>
                                    <span>Adicionar</span>
                                </div>

                                <?php if ($execucao && $execucao->fotos_galeria_json): ?>
                                    <?php $fotos = json_decode($execucao->fotos_galeria_json, true); ?>
                                    <?php foreach ($fotos as $foto): ?>
                                        <div class="gallery-item">
                                            <img src="<?php echo base_url($foto['caminho']); ?>" alt="Foto">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Materiais Utilizados -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-package"></i></span>
                            <h5>Materiais Utilizados</h5>
                        </div>
                        <div class="widget-content">
                            <div id="materiaisContainer">
                                <div class="empty-state">
                                    <p>Selecione os materiais utilizados no serviço</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-info btn-block" onclick="abrirModalMateriais()">
                                <i class="bx bx-plus-circle"></i> Adicionar Material
                            </button>
                        </div>
                    </div>

                    <!-- Assinatura -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-pencil"></i></span>
                            <h5>Assinatura do Cliente</h5>
                        </div>
                        <div class="widget-content">
                            <canvas id="signaturePad" class="signature-pad"></canvas>

                            <div class="row-fluid">
                                <div class="span6">
                                    <button type="button" class="btn btn-block" onclick="limparAssinatura()">
                                        <i class="bx bx-trash"></i> Limpar
                                    </button>
                                </div>
                            </div>

                            <div class="control-group" style="margin-top: 15px;">
                                <label class="control-label">Nome de quem assina</label>
                                <div class="controls">
                                    <input type="text" id="nomeAssinante" placeholder="Nome completo" class="span12">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="widget-box">
                        <div class="widget-title">
                            <span class="icon"><i class="bx bx-note"></i></span>
                            <h5>Observações</h5>
                        </div>
                        <div class="widget-content">
                            <textarea id="observacoes" rows="4" class="span12" placeholder="Descreva o que foi realizado, problemas encontrados, recomendações..."></textarea>
                        </div>
                    </div>

                    <!-- Finalização -->
                    <div class="action-card">
                        <h5><i class="bx bx-camera"></i> Foto de Finalização</h5>
                        <div class="camera-section">
                            <div class="camera-preview" id="checkoutPreview">
                                <i class="bx bx-camera"></i>
                                <span>Foto de Check-out</span>
                            </div>
                            <button type="button" class="btn btn-info" onclick="capturarFotoCheckout()" id="btnFotoCheckout">
                                <i class="bx bx-camera"></i> Tirar Foto
                            </button>
                        </div>

                        <button type="button" class="btn btn-success btn-large btn-block" onclick="finalizarExecucao()" id="btnFinalizar">
                            <span class="spinner"></span>
                            <i class="bx bx-check-circle"></i>
                            <span class="text">Finalizar OS</span>
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- Modal da Câmera -->
<div class="modal hide" id="cameraModal">
    <div class="modal-header">
        <button type="button" class="close" onclick="fecharCamera()">×</button>
        <h3>Capturar Foto</h3>
    </div>
    <div class="modal-body">
        <video id="video" autoplay playsinline style="width: 100%; border-radius: 8px;"></video>
        <canvas id="canvas" style="display: none;"></canvas>

        <div class="control-group" style="margin-top: 15px;">
            <label>Tipo da foto</label>
            <select id="tipoFoto" class="span12">
                <option value="antes">Antes do serviço</option>
                <option value="depois">Depois do serviço</option>
                <option value="problema">Problema encontrado</option>
                <option value="detalhe">Detalhe técnico</option>
            </select>
        </div>

        <div class="control-group">
            <label>Descrição (opcional)</label>
            <input type="text" id="descricaoFoto" placeholder="Descreva a foto" class="span12">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" onclick="fecharCamera()">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="tirarFoto()">
            <i class="bx bx-camera"></i> Capturar
        </button>
    </div>
</div>

<style>
/* Cliente Card */
.client-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 20px;
}

.client-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.client-info h4 {
    margin: 0 0 5px 0;
    color: #333;
}

.client-info p {
    margin: 3px 0;
    color: #666;
    font-size: 0.9rem;
}

.client-info i {
    color: #667eea;
    margin-right: 5px;
}

/* Action Card */
.action-card {
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: center;
}

.action-card h5 {
    margin: 0 0 15px 0;
    color: #333;
}

.action-card h5 i {
    color: #667eea;
}

/* Camera Section */
.camera-section {
    margin-bottom: 15px;
}

.camera-preview {
    width: 200px;
    height: 200px;
    border-radius: 12px;
    background: #e0e0e0;
    margin: 0 auto 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
    overflow: hidden;
}

.camera-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.camera-preview i {
    font-size: 48px;
}

/* Checklist */
.checklist-item {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s;
}

.checklist-item:hover {
    border-color: #667eea;
}

.checklist-item.conforme {
    border-color: #4caf50;
    background: #f1f8e9;
}

.checklist-item.nao_conforme {
    border-color: #f44336;
    background: #ffebee;
}

.checklist-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}

.checklist-checkbox {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #999;
}

.checklist-item.conforme .checklist-checkbox {
    background: #4caf50;
    color: white;
}

.checklist-item.nao_conforme .checklist-checkbox {
    background: #f44336;
    color: white;
}

.checklist-text h4 {
    margin: 0 0 3px 0;
    font-size: 0.95rem;
}

.checklist-text p {
    margin: 0;
    font-size: 0.8rem;
    color: #999;
}

.checklist-actions {
    display: flex;
    gap: 10px;
}

.checklist-actions .btn {
    flex: 1;
}

/* Gallery */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
}

.gallery-item {
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    background: #e0e0e0;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-add {
    aspect-ratio: 1;
    border: 2px dashed #667eea;
    border-radius: 8px;
    background: rgba(102, 126, 234, 0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #667eea;
    transition: all 0.3s;
}

.gallery-add:hover {
    background: rgba(102, 126, 234, 0.1);
}

.gallery-add i {
    font-size: 24px;
    margin-bottom: 5px;
}

/* Signature Pad */
.signature-pad {
    width: 100%;
    height: 200px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    margin-bottom: 15px;
    cursor: crosshair;
}

/* Spinner */
.spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.btn.loading .spinner {
    display: inline-block;
}

.btn.loading .text,
.btn.loading i:not(.spinner) {
    display: none;
}

/* Estados vazios */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}

.empty-state p {
    margin: 0;
}

.hidden {
    display: none !important;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .client-card {
        flex-direction: column;
        text-align: center;
    }

    .checklist-header {
        flex-direction: column;
    }

    .gallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 480px) {
    .checklist-actions {
        flex-direction: column;
    }

    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .signature-pad {
        height: 150px;
    }
}
</style>

<script>
let execucaoId = <?php echo $execucao ? $execucao->id : 'null'; ?>;
let osId = <?php echo $os->idOs; ?>;
let latitude, longitude;
let fotoCheckin = null;
let fotoCheckout = null;
let stream = null;

// Obter localização
if ('geolocation' in navigator) {
    navigator.geolocation.watchPosition(
        (pos) => {
            latitude = pos.coords.latitude;
            longitude = pos.coords.longitude;
        },
        (err) => console.error('Erro GPS:', err),
        { enableHighAccuracy: true }
    );
}

// Canvas de assinatura
const canvas = document.getElementById('signaturePad');
const ctx = canvas.getContext('2d');
let isDrawing = false;

function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
}

window.addEventListener('load', resizeCanvas);
window.addEventListener('resize', resizeCanvas);

canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);

canvas.addEventListener('touchstart', (e) => {
    e.preventDefault();
    startDrawing(e.touches[0]);
});
canvas.addEventListener('touchmove', (e) => {
    e.preventDefault();
    draw(e.touches[0]);
});
canvas.addEventListener('touchend', stopDrawing);

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing = false;
}

function limparAssinatura() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Câmera
async function capturarFotoCheckin() {
    const preview = document.getElementById('checkinPreview');

    try {
        const mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.createElement('video');
        video.srcObject = mediaStream;
        video.autoplay = true;

        await new Promise(resolve => video.onloadedmetadata = resolve);
        await new Promise(r => setTimeout(r, 500));

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(video, 0, 0);

        fotoCheckin = tempCanvas.toDataURL('image/jpeg', 0.8);
        preview.innerHTML = `<img src="${fotoCheckin}">`;

        mediaStream.getTracks().forEach(track => track.stop());
    } catch (err) {
        alert('Erro ao acessar câmera: ' + err.message);
    }
}

async function abrirCamera() {
    jQuery('#cameraModal').modal('show');
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        document.getElementById('video').srcObject = stream;
    } catch (err) {
        console.error('Erro ao abrir câmera:', err);
    }
}

function fecharCamera() {
    jQuery('#cameraModal').modal('hide');
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

async function tirarFoto() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    const foto = canvas.toDataURL('image/jpeg', 0.8);
    const tipo = document.getElementById('tipoFoto').value;
    const descricao = document.getElementById('descricaoFoto').value;

    const formData = new FormData();
    formData.append('execucao_id', execucaoId);
    formData.append('foto', foto);
    formData.append('tipo', tipo);
    formData.append('descricao', descricao);
    formData.append('latitude', latitude);
    formData.append('longitude', longitude);

    try {
        const response = await fetch('<?php echo site_url("tecnicos/adicionar_foto"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const grid = document.getElementById('galleryGrid');
            const item = document.createElement('div');
            item.className = 'gallery-item';
            item.innerHTML = `<img src="${foto}" alt="Foto">`;
            grid.insertBefore(item, grid.children[1]);

            fecharCamera();
            document.getElementById('descricaoFoto').value = '';
        } else {
            alert('Erro ao salvar foto: ' + data.message);
        }
    } catch (err) {
        alert('Erro ao enviar foto: ' + err.message);
    }
}

async function capturarFotoCheckout() {
    const preview = document.getElementById('checkoutPreview');

    try {
        const mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        const video = document.createElement('video');
        video.srcObject = mediaStream;
        video.autoplay = true;

        await new Promise(resolve => video.onloadedmetadata = resolve);
        await new Promise(r => setTimeout(r, 500));

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(video, 0, 0);

        fotoCheckout = tempCanvas.toDataURL('image/jpeg', 0.8);
        preview.innerHTML = `<img src="${fotoCheckout}">`;

        mediaStream.getTracks().forEach(track => track.stop());
    } catch (err) {
        alert('Erro ao acessar câmera: ' + err.message);
    }
}

// Execução
async function iniciarExecucao() {
    if (!latitude || !longitude) {
        alert('Aguardando localização...');
        return;
    }

    const btn = document.getElementById('btnIniciar');
    btn.classList.add('loading');
    btn.disabled = true;

    const formData = new FormData();
    formData.append('os_id', osId);
    formData.append('latitude', latitude);
    formData.append('longitude', longitude);
    formData.append('foto_checkin', fotoCheckin || '');
    formData.append('tipo', 'inicio_local');

    try {
        const response = await fetch('<?php echo site_url("tecnicos/iniciar_execucao"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            execucaoId = data.execucao_id;
            document.getElementById('checkinSection').classList.add('hidden');
            document.getElementById('execucaoSection').classList.remove('hidden');
            window.scrollTo(0, 0);
        } else {
            alert('Erro: ' + data.message);
        }
    } catch (err) {
        alert('Erro ao iniciar execução: ' + err.message);
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

// Checklist
async function salvarChecklistItem(itemId, status) {
    if (!execucaoId) return;

    const formData = new FormData();
    formData.append('execucao_id', execucaoId);
    formData.append('item_id', itemId);
    formData.append('status', status);
    formData.append('observacao', '');
    formData.append('valor', '');

    try {
        const response = await fetch('<?php echo site_url("tecnicos/salvar_checklist_item"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const item = document.querySelector(`[data-item-id="${itemId}"]`);
            item.classList.remove('conforme', 'nao_conforme');
            item.classList.add(status === 'conforme' ? 'conforme' : 'nao_conforme');

            const checkbox = item.querySelector('.checklist-checkbox');
            checkbox.innerHTML = status === 'conforme' ? '<i class="bx bx-check"></i>' : '<i class="bx bx-x"></i>';

            const botoes = item.querySelectorAll('.checklist-actions .btn');
            botoes[0].className = 'btn btn-mini ' + (status === 'conforme' ? 'btn-success' : '');
            botoes[1].className = 'btn btn-mini ' + (status === 'nao_conforme' ? 'btn-danger' : '');

            atualizarProgresso();
        }
    } catch (err) {
        console.error('Erro ao salvar item:', err);
    }
}

function atualizarProgresso() {
    const items = document.querySelectorAll('.checklist-item');
    const concluidos = document.querySelectorAll('.checklist-item.conforme, .checklist-item.nao_conforme').length;
    const progresso = items.length > 0 ? Math.round((concluidos / items.length) * 100) : 0;

    document.getElementById('progressBar').style.width = progresso + '%';
    document.getElementById('progressText').textContent = progresso + '% concluído';
}

// Finalização
async function finalizarExecucao() {
    if (!execucaoId) return;

    const nomeAssinante = document.getElementById('nomeAssinante').value;
    const observacoes = document.getElementById('observacoes').value;

    if (!nomeAssinante) {
        alert('Informe o nome de quem está assinando');
        return;
    }

    const btn = document.getElementById('btnFinalizar');
    btn.classList.add('loading');
    btn.disabled = true;

    const assinatura = canvas.toDataURL('image/png');

    const formData = new FormData();
    formData.append('execucao_id', execucaoId);
    formData.append('latitude', latitude);
    formData.append('longitude', longitude);
    formData.append('foto_checkout', fotoCheckout || '');
    formData.append('assinatura_cliente', assinatura);
    formData.append('nome_cliente_assina', nomeAssinante);
    formData.append('observacoes', observacoes);

    try {
        const response = await fetch('<?php echo site_url("tecnicos/finalizar_execucao"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('OS finalizada com sucesso! Tempo total: ' + Math.round(data.tempo_total * 100) / 100 + ' horas');
            window.location.href = '<?php echo site_url("tecnicos/dashboard"); ?>';
        } else {
            alert('Erro: ' + data.message);
        }
    } catch (err) {
        alert('Erro ao finalizar: ' + err.message);
    } finally {
        btn.classList.remove('loading');
        btn.disabled = false;
    }
}

function abrirModalMateriais() {
    // Implementar modal de materiais
    alert('Função de adicionar material em desenvolvimento');
}
</script>
