<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2c3e50">
    <title>Portal do Técnico - Login</title>
    <link rel="manifest" href="<?php echo base_url('assets/tecnicos/manifest.json'); ?>">
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/tecnicos/icon-192x192.png'); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            object-fit: cover;
        }

        .logo h1 {
            color: #333;
            font-size: 1.5rem;
            margin-top: 15px;
        }

        .logo p {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .camera-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .camera-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #e0e0e0;
            margin: 0 auto 15px;
            overflow: hidden;
            position: relative;
            border: 3px solid #667eea;
        }

        .camera-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-preview .placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #999;
            font-size: 3rem;
        }

        .btn-camera {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-camera:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-camera:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .location-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #2e7d32;
            font-size: 0.9rem;
        }

        .location-status.error {
            background: #ffebee;
            color: #c62828;
        }

        .location-status.waiting {
            background: #fff3e0;
            color: #ef6c00;
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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

        .btn-login.loading .spinner {
            display: inline-block;
        }

        .btn-login.loading .text {
            display: none;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 0.85rem;
        }

        #video {
            display: none;
        }

        #canvas {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="<?php echo base_url('assets/tecnicos/icon-192x192.png'); ?>" alt="Logo" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2280%22>🔧</text></svg>'">
            <h1>Portal do Técnico</h1>
            <p>Sistema de Gestão de OS</p>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="error-message show">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <div class="error-message" id="errorMsg"></div>

        <form id="loginForm" action="<?php echo site_url('tecnicos/autenticar'); ?>" method="POST">
            <input type="hidden" name="latitude" id="latitude" required>
            <input type="hidden" name="longitude" id="longitude" required>
            <input type="hidden" name="foto_login" id="foto_login">

            <div class="camera-section">
                <div class="camera-preview" id="cameraPreview">
                    <span class="placeholder">📷</span>
                </div>
                <button type="button" class="btn-camera" id="btnCamera" onclick="capturarFoto()">
                    <span>📷</span>
                    <span>Tirar Foto</span>
                </button>
                <video id="video" autoplay playsinline></video>
                <canvas id="canvas" width="400" height="400"></canvas>
            </div>

            <div class="location-status waiting" id="locationStatus">
                <span>🔄</span>
                <span>Obtendo localização...</span>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" placeholder="seu@email.com" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login" id="btnLogin" disabled>
                <span class="spinner"></span>
                <span class="text">Entrar</span>
            </button>
        </form>

        <div class="footer">
            <p>Mapos OS v5 - Sistema de Gestão</p>
        </div>
    </div>

    <script>
        let fotoCapturada = null;
        let localizacaoObtida = false;

        // Solicitar localização ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        localizacaoObtida = true;
                        atualizarStatusLocalizacao(true);
                        verificarCampos();
                    },
                    function(error) {
                        console.error('Erro ao obter localização:', error);
                        atualizarStatusLocalizacao(false);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                atualizarStatusLocalizacao(false);
            }
        });

        function atualizarStatusLocalizacao(sucesso) {
            const status = document.getElementById('locationStatus');
            if (sucesso) {
                status.className = 'location-status';
                status.innerHTML = '<span>✅</span><span>Localização obtida</span>';
            } else {
                status.className = 'location-status error';
                status.innerHTML = '<span>❌</span><span>Permita o acesso à localização</span>';
            }
        }

        async function capturarFoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const preview = document.getElementById('cameraPreview');
            const btn = document.getElementById('btnCamera');

            try {
                btn.disabled = true;
                btn.innerHTML = '<span>🔄</span><span>Abrindo câmera...</span>';

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' }
                });

                video.srcObject = stream;

                // Aguardar vídeo carregar
                await new Promise(resolve => {
                    video.onloadedmetadata = resolve;
                });

                // Esperar um pouco para a câmera focar
                await new Promise(r => setTimeout(r, 500));

                // Capturar foto
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                fotoCapturada = canvas.toDataURL('image/jpeg', 0.8);
                document.getElementById('foto_login').value = fotoCapturada;

                // Mostrar preview
                preview.innerHTML = `<img src="${fotoCapturada}" alt="Foto capturada">`;

                // Parar câmera
                stream.getTracks().forEach(track => track.stop());

                btn.innerHTML = '<span>🔄</span><span>Tirar Nova</span>';
                btn.disabled = false;

                verificarCampos();

            } catch (error) {
                console.error('Erro ao acessar câmera:', error);
                alert('Não foi possível acessar a câmera. Verifique as permissões.');
                btn.disabled = false;
                btn.innerHTML = '<span>📷</span><span>Tirar Foto</span>';
            }
        }

        function verificarCampos() {
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const btn = document.getElementById('btnLogin');

            if (email && senha && localizacaoObtida) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }

        document.getElementById('email').addEventListener('input', verificarCampos);
        document.getElementById('senha').addEventListener('input', verificarCampos);

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!localizacaoObtida) {
                e.preventDefault();
                document.getElementById('errorMsg').textContent = 'Localização é obrigatória para login';
                document.getElementById('errorMsg').classList.add('show');
                return;
            }

            document.getElementById('btnLogin').classList.add('loading');
        });
    </script>
</body>
</html>
