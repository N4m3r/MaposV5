<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <title>Executar OS #<?php echo $os->idOs; ?></title>
    <link rel="manifest" href="<?php echo base_url('assets/tecnicos/manifest.json'); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f6fa;
            color: #333;
            padding-bottom: 80px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .header-title {
            flex: 1;
        }

        .header-title h1 {
            font-size: 1.1rem;
            margin-bottom: 3px;
        }

        .header-title p {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.em_execucao {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge.pendente {
            background: #fff3e0;
            color: #ef6c00;
        }

        .client-info h2 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .client-info p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            color: white;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .camera-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .camera-preview {
            width: 100%;
            max-width: 250px;
            aspect-ratio: 1;
            border-radius: 12px;
            background: #e0e0e0;
            margin: 0 auto 15px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .camera-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-preview .placeholder {
            font-size: 3rem;
            color: #999;
        }

        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-fill {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }

        .progress-text {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .checklist-item {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
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

        .checklist-item-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .checklist-checkbox {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .checklist-item.conforme .checklist-checkbox {
            background: #4caf50;
            border-color: #4caf50;
            color: white;
        }

        .checklist-item.nao_conforme .checklist-checkbox {
            background: #f44336;
            border-color: #f44336;
            color: white;
        }

        .checklist-text {
            flex: 1;
        }

        .checklist-text h4 {
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .checklist-text p {
            font-size: 0.8rem;
            color: #999;
        }

        .checklist-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }

        .checklist-btn {
            flex: 1;
            padding: 8px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .checklist-btn.active-conforme {
            background: #4caf50;
            border-color: #4caf50;
            color: white;
        }

        .checklist-btn.active-nao {
            background: #f44336;
            border-color: #f44336;
            color: white;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .gallery-item {
            aspect-ratio: 1;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            background: #e0e0e0;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-add {
            border: 2px dashed #667eea;
            background: rgba(102, 126, 234, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #667eea;
        }

        .signature-pad {
            width: 100%;
            height: 200px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background: white;
            margin-bottom: 10px;
        }

        .material-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .material-info h4 {
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .material-info p {
            font-size: 0.8rem;
            color: #999;
        }

        .material-qty {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 8px;
            background: #667eea;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .qty-value {
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .hidden {
            display: none !important;
        }

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

        .btn.loading .text {
            display: none;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="back-btn">←</a>
            <div class="header-title">
                <h1>OS #<?php echo $os->idOs; ?></h1>
                <p><?php echo $servicos ? count($servicos) : 0; ?> serviço(s)</p>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Informações do Cliente -->
        <div class="card">
            <div class="client-info">
                <h2>🙋 <?php echo htmlspecialchars($cliente->nome); ?></h2>
                <p>📍 <?php echo htmlspecialchars($cliente->endereco ?? 'Endereço não informado'); ?></p>
                <?php if ($cliente->telefone): ?>                <p>📞 <?php echo htmlspecialchars($cliente->telefone); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botão de Check-in -->
        <div id="checkinSection" class="card <?php echo $execucao ? 'hidden' : ''; ?>">
            <div class="card-header">
                <div class="card-title">
                    📍 Iniciar Atendimento
                </div>
            </div>

            <div class="camera-section">
                <div class="camera-preview" id="checkinPreview">
                    <span class="placeholder">📷</span>
                </div>
                <button type="button" class="btn btn-primary" onclick="capturarFotoCheckin()" id="btnFotoCheckin">
                    Tirar Foto de Check-in
                </button>
            </div>

            <button type="button" class="btn btn-success" onclick="iniciarExecucao()" id="btnIniciar">
                <span class="spinner"></span>
                <span class="text">🚀 Iniciar Execução</span>
            </button>
        </div>

        <!-- Execução em Andamento -->
        <div id="execucaoSection" class="<?php echo $execucao ? '' : 'hidden'; ?>">
            <!-- Progresso -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        ✅ Progresso
                    </div>
                    <span class="status-badge em_execucao">Em Execução</span>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" id="progressBar" style="width: <?php echo $execucao ? $execucao->progresso_execucao : 0; ?>%"></div>
                </div>

                <div class="progress-text" id="progressText">
                    <?php echo $execucao ? $execucao->progresso_execucao : 0; ?>% concluído
                </div>
            </div>

            <!-- Checklist -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        📋 Checklist de Execução
                    </div>
                </div>

                <div id="checklistContainer">
                    <?php if (!empty($checklist)): ?>
                        <?php foreach ($checklist as $index => $item): ?>
                            <div class="checklist-item" data-item-id="<?php echo $index; ?>" onclick="toggleChecklistItem(<?php echo $index; ?>)">
                                <div class="checklist-item-header">
                                    <div class="checklist-checkbox">
                                        <?php echo $item['status'] == 'conforme' ? '✓' : ($item['status'] == 'nao_conforme' ? '✗' : ''); ?>
                                    </div>
                                    <div class="checklist-text">
                                        <h4><?php echo htmlspecialchars($item['descricao'] ?? $item); ?></h4>
                                        <?php if (isset($item['servico'])): ?>                                        <p><?php echo htmlspecialchars($item['servico']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="checklist-actions" onclick="event.stopPropagation()">
                                    <button type="button" class="checklist-btn <?php echo $item['status'] == 'conforme' ? 'active-conforme' : ''; ?>"
                                            onclick="salvarChecklistItem(<?php echo $index; ?>, 'conforme')">
                                        ✓ OK
                                    </button>
                                    <button type="button" class="checklist-btn <?php echo $item['status'] == 'nao_conforme' ? 'active-nao' : ''; ?>"
                                            onclick="salvarChecklistItem(<?php echo $index; ?>, 'nao_conforme')">
                                        ✗ Não OK
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>003e
                        <p style="text-align: center; color: #999; padding: 20px;">
                            Nenhum item de checklist configurado
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Galeria de Fotos -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        📸 Fotos do Serviço
                    </div>
                </div>

                <div class="gallery-grid" id="galleryGrid">
                    <div class="gallery-item gallery-add" onclick="abrirCamera()">
                        <span style="font-size: 2rem;">+</span>
                        <span style="font-size: 0.8rem;">Adicionar</span>
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

            <!-- Materiais Utilizados -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        🔧 Materiais Utilizados
                    </div>
                </div>

                <div id="materiaisContainer">
                    <p style="text-align: center; color: #999; padding: 20px;">
                        Selecione os materiais utilizados no serviço
                    </p>
                </div>

                <button type="button" class="btn btn-primary" onclick="abrirModalMateriais()" style="margin-top: 10px;">
                    ➕ Adicionar Material
                </button>
            </div>

            <!-- Assinatura -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        ✍️ Assinatura do Cliente
                    </div>
                </div>

                <canvas id="signaturePad" class="signature-pad"></canvas>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn" onclick="limparAssinatura()" style="flex: 1; background: #f5f5f5; color: #666;">
                        🗑️ Limpar
                    </button>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Nome de quem assina</label>
                    <input type="text" id="nomeAssinante" placeholder="Nome completo">
                </div>
            </div>

            <!-- Observações -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        📝 Observações
                    </div>
                </div>

                <div class="form-group">
                    <textarea id="observacoes" rows="4" placeholder="Descreva o que foi realizado, problemas encontrados, recomendações..."></textarea>
                </div>
            </div>

            <!-- Finalização -->
            <div class="card">
                <div class="camera-section">
                    <div class="camera-preview" id="checkoutPreview">
                        <span class="placeholder">📷</span>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="capturarFotoCheckout()" id="btnFotoCheckout">
                        Tirar Foto de Finalização
                    </button>
                </div>

                <button type="button" class="btn btn-success" onclick="finalizarExecucao()" id="btnFinalizar">
                    <span class="spinner"></span>
                    <span class="text">✅ Finalizar OS</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal da Câmera -->
    <div class="modal" id="cameraModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Capturar Foto</h3>
                <button onclick="fecharCamera()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
            </div>
            <div class="modal-body">
                <video id="video" autoplay playsinline style="width: 100%; border-radius: 10px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>

                <div class="form-group">
                    <label>Tipo da foto</label>
                    <select id="tipoFoto" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 10px;">
                        <option value="antes">Antes do serviço</option>
                        <option value="depois">Depois do serviço</option>
                        <option value="problema">Problema encontrado</option>
                        <option value="detalhe">Detalhe técnico</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Descrição (opcional)</label>
                    <input type="text" id="descricaoFoto" placeholder="Descreva a foto">
                </div>

                <button type="button" class="btn btn-primary" onclick="tirarFoto()">
                    📷 Capturar
                </button>
            </div>
        </div>
    </div>

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
            const btn = document.getElementById('btnFotoCheckin');

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
            document.getElementById('cameraModal').classList.add('active');
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                document.getElementById('video').srcObject = stream;
            } catch (err) {
                console.error('Erro ao abrir câmera:', err);
            }
        }

        function fecharCamera() {
            document.getElementById('cameraModal').classList.remove('active');
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

            // Enviar para o servidor
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
                    // Adicionar à galeria
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
                    // Atualizar UI
                    const item = document.querySelector(`[data-item-id="${itemId}"]`);
                    item.classList.remove('conforme', 'nao_conforme');
                    item.classList.add(status === 'conforme' ? 'conforme' : 'nao_conforme');

                    // Atualizar checkbox
                    const checkbox = item.querySelector('.checklist-checkbox');
                    checkbox.textContent = status === 'conforme' ? '✓' : '✗';

                    // Atualizar botões
                    const botoes = item.querySelectorAll('.checklist-btn');
                    botoes[0].classList.toggle('active-conforme', status === 'conforme');
                    botoes[1].classList.toggle('active-nao', status === 'nao_conforme');

                    // Atualizar progresso
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

            // Pegar assinatura
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
    </script>
</body>
</html>
