<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#667eea">
    <title>Portal do Técnico - Login</title>
    <link rel="manifest" href="<?php echo base_url('assets/tecnicos/manifest.json'); ?>">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
            overflow: hidden;
        }

        /* Animated background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .bg-animation .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite;
        }

        .circle:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 150px;
            height: 150px;
            right: 10%;
            top: 20%;
            animation-delay: 2s;
        }

        .circle:nth-child(3) {
            width: 60px;
            height: 60px;
            left: 20%;
            bottom: 20%;
            animation-delay: 4s;
        }

        .circle:nth-child(4) {
            width: 120px;
            height: 120px;
            right: 20%;
            bottom: 10%;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.5;
            }
            50% {
                transform: translateY(-100px) rotate(180deg);
                opacity: 0.8;
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 20px 45px rgba(102, 126, 234, 0.5);
            }
        }

        .logo-icon i {
            font-size: 45px;
            color: white;
        }

        .logo h1 {
            color: #333;
            font-size: 1.6rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .logo p {
            color: #888;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #555;
            font-size: 0.85rem;
            margin-bottom: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-group label i {
            color: #667eea;
            font-size: 1.1rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid #e8e8e8;
            border-radius: 14px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.3rem;
            transition: color 0.3s;
        }

        .form-group input:focus + .input-icon {
            color: #667eea;
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-login i {
            font-size: 1.3rem;
        }

        .spinner {
            display: none;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-login.loading .spinner {
            display: block;
        }

        .btn-login.loading .text,
        .btn-login.loading i {
            display: none;
        }

        .error-message {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            color: white;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
            box-shadow: 0 4px 15px rgba(238, 90, 90, 0.3);
        }

        .error-message.show {
            display: flex;
        }

        .error-message i {
            font-size: 1.2rem;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #aaa;
            font-size: 0.85rem;
        }

        .footer span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Features badges */
        .features {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .feature-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            color: #888;
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .feature-badge i {
            color: #667eea;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }

            .logo-icon {
                width: 70px;
                height: 70px;
            }

            .logo-icon i {
                font-size: 35px;
            }

            .logo h1 {
                font-size: 1.4rem;
            }
        }

        /* Success animation */
        .success-check {
            display: none;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.3s ease;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-check i {
            color: #667eea;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">
                <i class='bx bx-hard-hat'></i>
            </div>
            <h1>Portal do Técnico</h1>
            <p>Acesse suas ordens de serviço</p>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="error-message show">
            <i class='bx bx-error-circle'></i>
            <span><?php echo $this->session->flashdata('error'); ?></span>
        </div>
        <?php endif; ?>

        <div class="error-message" id="errorMsg">
            <i class='bx bx-error-circle'></i>
            <span id="errorText"></span>
        </div>

        <form id="loginForm" action="<?php echo site_url('tecnicos/autenticar'); ?>" method="POST">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

            <div class="form-group">
                <label for="email">
                    <i class='bx bx-envelope'></i> E-mail
                </label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" placeholder="seu@email.com" required autocomplete="email">
                    <i class='bx bx-user input-icon'></i>
                </div>
            </div>

            <div class="form-group">
                <label for="senha">
                    <i class='bx bx-lock-alt'></i> Senha
                </label>
                <div class="input-wrapper">
                    <input type="password" name="senha" id="senha" placeholder="••••••••" required autocomplete="current-password">
                    <i class='bx bx-lock input-icon'></i>
                </div>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <div class="spinner"></div>
                <div class="success-check">
                    <i class='bx bx-check'></i>
                </div>
                <i class='bx bx-log-in-circle'></i>
                <span class="text">Entrar no Sistema</span>
            </button>
        </form>

        <div class="features">
            <div class="feature-badge">
                <i class='bx bx-map'></i> GPS
            </div>
            <div class="feature-badge">
                <i class='bx bx-camera'></i> Fotos
            </div>
            <div class="feature-badge">
                <i class='bx bx-signal-4'></i> Offline
            </div>
        </div>

        <div class="footer">
            <span><i class='bx bx-cube-alt'></i> Mapos OS v5</span>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnLogin');
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;

            // Validação básica
            if (!email || !senha) {
                e.preventDefault();
                showError('Preencha todos os campos');
                return;
            }

            btn.classList.add('loading');
            btn.disabled = true;
        });

        function showError(msg) {
            const errorDiv = document.getElementById('errorMsg');
            const errorText = document.getElementById('errorText');
            errorText.textContent = msg;
            errorDiv.classList.add('show');

            setTimeout(() => {
                errorDiv.classList.remove('show');
            }, 5000);
        }

        // Animate inputs on focus
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
