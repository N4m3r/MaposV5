<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - <?php echo $this->config->item('app_name'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="<?php echo base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --dark-color: #1a1f3a;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.3);
            --shadow-color: rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            overflow-x: hidden;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Particulas de fundo */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            animation: float 25s infinite;
            bottom: -100px;
            border-radius: 50%;
        }

        .particle:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; width: 40px; height: 40px; animation-delay: 2s; animation-duration: 12s; }
        .particle:nth-child(3) { left: 35%; width: 60px; height: 60px; animation-delay: 4s; }
        .particle:nth-child(4) { left: 50%; width: 90px; height: 90px; animation-delay: 0s; animation-duration: 18s; }
        .particle:nth-child(5) { left: 65%; width: 30px; height: 30px; animation-delay: 3s; }
        .particle:nth-child(6) { left: 75%; width: 50px; height: 50px; animation-delay: 7s; }
        .particle:nth-child(7) { left: 90%; width: 70px; height: 70px; animation-delay: 11s; }
        .particle:nth-child(8) { left: 5%; width: 35px; height: 35px; animation-delay: 6s; }
        .particle:nth-child(9) { left: 45%; width: 45px; height: 45px; animation-delay: 9s; }
        .particle:nth-child(10) { left: 85%; width: 55px; height: 55px; animation-delay: 5s; }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        /* Container principal */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px var(--shadow-color),
                        0 0 0 1px var(--glass-border);
            padding: 45px 40px;
            position: relative;
            overflow: hidden;
        }

        /* Efeito de brilho no container */
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 70%
            );
            animation: shimmer 6s infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 35px;
            position: relative;
            z-index: 1;
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 24px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); }
            50% { transform: scale(1.02); box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5); }
        }

        .logo-container img {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
        }

        .logo-container i {
            font-size: 45px;
            color: white;
        }

        .login-header h3 {
            color: var(--dark-color);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 15px;
            font-weight: 400;
        }

        /* Info box */
        .info-box {
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .info-box i {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: block;
        }

        .info-box p {
            color: #4c51bf;
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }

        /* Campos de formulario */
        .form-group {
            margin-bottom: 22px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group label i {
            color: var(--primary-color);
            font-size: 18px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        .form-group input:focus + .input-icon {
            color: var(--primary-color);
        }

        /* Animacao de shake para erros */
        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        /* Botao de login */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            margin-right: 8px;
            font-size: 18px;
        }

        .btn-login.loading {
            opacity: 0.8;
            cursor: not-allowed;
        }

        /* Alertas */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            animation: slideInDown 0.5s ease;
            position: relative;
            z-index: 1;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #059669;
            border: 1px solid #6ee7b7;
        }

        .alert i {
            font-size: 20px;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--secondary-color);
            transform: translateX(-3px);
        }

        /* Efeito de loading no botao */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsividade */
        @media (max-width: 480px) {
            .login-container {
                padding: 35px 25px;
            }

            .login-header h3 {
                font-size: 24px;
            }

            .logo-container {
                width: 75px;
                height: 75px;
            }

            .logo-container i {
                font-size: 38px;
            }

            .form-group input {
                padding: 13px 16px 13px 44px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            :root {
                --glass-bg: rgba(30, 30, 40, 0.95);
            }

            body {
                background: linear-gradient(-45deg, #1a1f3a, #2d335b, #4a5568, #2d3748);
            }

            .login-container {
                background: rgba(30, 30, 40, 0.95);
            }

            .login-header h3 {
                color: #f3f4f6;
            }

            .login-header p {
                color: #9ca3af;
            }

            .form-group label {
                color: #d1d5db;
            }

            .form-group input {
                background: #2d3748;
                border-color: #4a5568;
                color: #f3f4f6;
            }

            .form-group input:focus {
                border-color: var(--primary-color);
                background: #374151;
            }

            .input-icon {
                color: #6b7280;
            }

            .info-box {
                background: linear-gradient(135deg, #3730a3, #4338ca);
            }

            .info-box p {
                color: #e0e7ff;
            }

            .login-footer {
                border-top-color: #4a5568;
            }
        }
    </style>
</head>
<body>
    <!-- Particulas de fundo -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo-container">
                    <?php if (isset($emitente) && $emitente->url_logo): ?>
                        <img src="<?php echo $emitente->url_logo; ?>" alt="Logo">
                    <?php else: ?>
                        <i class='bx bx-key'></i>
                    <?php endif; ?>
                </div>
                <h3>Recuperar Senha</h3>
                <p>Informe seu e-mail cadastrado</p>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-error" id="alertError">
                    <i class="bx bx-error-circle"></i>
                    <span><?php echo $this->session->flashdata('error'); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success" id="alertSuccess">
                    <i class="bx bx-check-circle"></i>
                    <span><?php echo $this->session->flashdata('success'); ?></span>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <i class="bx bx-envelope"></i>
                <p>Enviaremos instrucoes para redefinir sua senha no e-mail cadastrado.</p>
            </div>

            <form id="formRecuperar" action="<?php echo current_url(); ?>" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <i class="bx bx-envelope input-icon"></i>
                        <input type="email" name="email" id="email" placeholder="seu@email.com" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnRecuperar">
                    <i class="bx bx-send"></i> Enviar Instrucoes
                </button>
            </form>

            <div class="login-footer">
                <a href="<?php echo site_url('mine'); ?>" class="back-link">
                    <i class="bx bx-arrow-back"></i> Voltar para o Login
                </a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Auto-hide alerts com animacao
            setTimeout(function() {
                $('.alert').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);

            // Animacao nos inputs
            $('.form-group input').on('focus', function() {
                $(this).closest('.form-group').addClass('focused');
            }).on('blur', function() {
                $(this).closest('.form-group').removeClass('focused');
            });

            // Validacao do formulario
            $('#formRecuperar').on('submit', function(e) {
                var email = $('#email').val().trim();

                if (!email) {
                    e.preventDefault();
                    $('.login-container').addClass('shake');
                    setTimeout(function() {
                        $('.login-container').removeClass('shake');
                    }, 500);
                    return false;
                }

                // Adiciona estado de loading
                var $btn = $('#btnRecuperar');
                $btn.addClass('loading').prop('disabled', true).html('<span class="spinner"></span> Enviando...');
            });

            // Efeito de onda nos inputs
            $('.form-group input').on('keypress', function(e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
</body>
</html>
