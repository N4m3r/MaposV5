<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <title>Meu Perfil - Portal do Técnico</title>
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
            text-decoration: none;
        }

        .header-title h1 {
            font-size: 1.2rem;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

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
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-edit {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            border: none;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-level {
            font-size: 1rem;
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #667eea;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #999;
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-primary {
            background: #e3f2fd;
            color: #1976d2;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-danger {
            background: #ffebee;
            color: #c62828;
        }

        .btn-danger:hover {
            background: #f44336;
            color: white;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px 15px;
            color: #999;
            text-decoration: none;
            font-size: 0.75rem;
            transition: color 0.3s;
        }

        .nav-item.active {
            color: #667eea;
        }

        .nav-icon {
            font-size: 1.5rem;
            margin-bottom: 3px;
        }

        .specialties {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .specialty-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
        }

        .camera-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.9);
            z-index: 1000;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .camera-modal.active {
            display: flex;
        }

        .camera-preview {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 1;
            border-radius: 20px;
            overflow: hidden;
            background: #333;
        }

        .camera-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-actions {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .camera-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid white;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s;
        }

        .camera-btn.capture {
            background: white;
            color: #333;
        }

        .camera-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="back-btn">←</a>
            <div class="header-title">
                <h1>Meu Perfil</h1>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Perfil Header -->
        <div class="profile-header">
            <div class="profile-avatar" onclick="abrirCamera()">
                <?php if (!empty($tecnico->foto_tecnico) && file_exists(FCPATH . $tecnico->foto_tecnico)): ?>
                    <img src="<?php echo base_url($tecnico->foto_tecnico); ?>?v=<?php echo time(); ?>" alt="Foto">
                <?php else: ?>
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='80'%3E👤%3C/text%3E%3C/svg%3E" alt="Foto">
                <?php endif; ?>
                <button class="avatar-edit">📷 Alterar</button>
            </div>

            <div class="profile-name"><?php echo htmlspecialchars($tecnico->nome ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="profile-level">
                Técnico Nível <?php echo $tecnico->nivel_tecnico; ?>
            </div>
        </div>

        <!-- Informações Pessoais -->
        <div class="card">
            <div class="card-title">👤 Informações Pessoais</div>

            <div class="info-row">
                <span class="info-label">E-mail</span>
                <span class="info-value"><?php echo htmlspecialchars($tecnico->email); ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Telefone</span>
                <span class="info-value"><?php echo htmlspecialchars($tecnico->telefone ?? 'Não informado'); ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">CPF</span>
                <span class="info-value"><?php echo htmlspecialchars($tecnico->cpf ?? 'Não informado'); ?></span>
            </div>
        </div>

        <!-- Informações Profissionais -->
        <div class="card">
            <div class="card-title">🔧 Informações Profissionais</div>

            <div class="info-row">
                <span class="info-label">Nível</span>
                <span class="badge badge-primary">Nível <?php echo $tecnico->nivel_tecnico; ?></span>
            </div>

            <?php if ($tecnico->especialidades): ?>
            <div class="info-row" style="flex-direction: column; align-items: flex-start;">
                <span class="info-label" style="margin-bottom: 10px;">Especialidades</span>
                <div class="specialties">
                    <?php $especialidades = explode(',', $tecnico->especialidades); ?>
                    <?php foreach ($especialidades as $esp): ?>
                        <span class="specialty-tag"><?php echo trim($esp); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($tecnico->veiculo_placa): ?>
            <div class="info-row">
                <span class="info-label">Veículo</span>
                <span class="info-value"><?php echo htmlspecialchars($tecnico->veiculo_tipo . ' - ' . $tecnico->veiculo_placa); ?></span>
            </div>
            <?php endif; ?>

            <div class="info-row">
                <span class="info-label">Plantão 24h</span>
                <span class="badge <?php echo $tecnico->plantao_24h ? 'badge-success' : 'badge-primary'; ?>">
                    <?php echo $tecnico->plantao_24h ? 'Sim' : 'Não'; ?>
                </span>
            </div>
        </div>

        <!-- Configurações -->
        <div class="card">
            <div class="card-title">⚙️ Configurações</div>

            <div class="info-row">
                <span class="info-label">Último acesso</span>
                <span class="info-value">
                    <?php echo $tecnico->ultimo_acesso_app ? date('d/m/Y H:i', strtotime($tecnico->ultimo_acesso_app)) : 'Nunca'; ?>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Versão do App</span>
                <span class="info-value">v1.0.0</span>
            </div>
        </div>

        <!-- Logout -->
        <a href="<?php echo site_url('tecnicos/logout'); ?>" class="btn btn-danger">
            🚪 Sair do Sistema
        </a>
    </div>

    <!-- Modal da Câmera -->
    <div class="camera-modal" id="cameraModal">
        <div class="camera-preview">
            <video id="video" autoplay playsinline></video>
        </div>
        <div class="camera-actions">
            <button class="camera-btn" onclick="fecharCamera()">❌</button>
            <button class="camera-btn capture" onclick="capturarFoto()">📷</button>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="<?php echo site_url('tecnicos/dashboard'); ?>" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span>Início</span>
        </a>
        <a href="<?php echo site_url('tecnicos/minhas_os'); ?>" class="nav-item">
            <span class="nav-icon">📋</span>
            <span>OS</span>
        </a>
        <a href="<?php echo site_url('tecnicos/meu_estoque'); ?>" class="nav-item">
            <span class="nav-icon">📦</span>
            <span>Estoque</span>
        </a>
        <a href="<?php echo site_url('tecnicos/perfil'); ?>" class="nav-item active">
            <span class="nav-icon">👤</span>
            <span>Perfil</span>
        </a>
    </nav>

    <script>
        let stream = null;

        async function abrirCamera() {
            document.getElementById('cameraModal').classList.add('active');
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
            document.getElementById('cameraModal').classList.remove('active');
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

            // Enviar para o servidor
            const formData = new FormData();
            formData.append('foto', foto);

            try {
                const response = await fetch('<?php echo site_url("tecnicos/atualizar_foto"); ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Atualizar preview
                    document.querySelector('.profile-avatar img').src = foto;
                    fecharCamera();
                } else {
                    alert('Erro ao atualizar foto');
                }
            } catch (err) {
                alert('Erro ao enviar foto: ' + err.message);
            }
        }
    </script>
</body>
</html>
