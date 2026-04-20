<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?> - Login</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url(); ?>assets/img/favicon.png" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="<?= base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <script src="<?= base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0039c6;
            --primary-hover: #002a94;
            --secondary-color: #00cd00;
            --accent-color: #4facfe;
            --dark-color: #1a1f3a;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --shadow-color: rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #f3f4f6;
        }

        /* Lado Esquerdo - Saudacao */
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, var(--dark-color) 0%, #2d335b 50%, var(--primary-color) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        /* Particulas animadas */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            display: block;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
            border-radius: 50%;
        }

        .particle:nth-child(1) { left: 10%; width: 60px; height: 60px; animation-delay: 0s; top: 20%; }
        .particle:nth-child(2) { left: 20%; width: 30px; height: 30px; animation-delay: 2s; top: 60%; }
        .particle:nth-child(3) { left: 35%; width: 45px; height: 45px; animation-delay: 4s; top: 40%; }
        .particle:nth-child(4) { left: 50%; width: 70px; height: 70px; animation-delay: 0s; top: 80%; }
        .particle:nth-child(5) { left: 65%; width: 25px; height: 25px; animation-delay: 3s; top: 30%; }
        .particle:nth-child(6) { left: 75%; width: 40px; height: 40px; animation-delay: 7s; top: 70%; }
        .particle:nth-child(7) { left: 85%; width: 55px; height: 55px; animation-delay: 11s; top: 50%; }
        .particle:nth-child(8) { left: 5%; width: 35px; height: 35px; animation-delay: 6s; top: 90%; }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.6;
            }
            50% {
                transform: translateY(-30px) rotate(180deg);
                opacity: 0.3;
            }
        }

        /* Ondas decorativas */
        .waves {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") repeat-x;
            animation: wave 10s linear infinite;
        }

        @keyframes wave {
            0% { background-position-x: 0; }
            100% { background-position-x: 1440px; }
        }

        .left-content {
            text-align: center;
            color: #fff;
            z-index: 1;
            animation: slideInLeft 1s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .greeting-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .greeting-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .greeting-subtitle {
            font-size: 18px;
            font-weight: 300;
            opacity: 0.9;
        }

        .left-image {
            width: 80%;
            max-width: 400px;
            margin-top: 40px;
            animation: floatImage 6s ease-in-out infinite;
        }

        @keyframes floatImage {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Lado Direito - Formulario */
        .right-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            animation: slideInRight 1s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-card {
            background: var(--glass-bg);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px var(--shadow-color);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        /* Efeito de brilho */
        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(0, 57, 198, 0.03) 50%,
                transparent 70%
            );
            animation: shimmer 6s infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        /* Header do Card */
        .card-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 57, 198, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 8px 20px rgba(0, 57, 198, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 12px 30px rgba(0, 57, 198, 0.4); }
        }

        .logo-icon img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .logo-text img {
            height: 35px;
        }

        .version-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            margin-top: 15px;
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

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        /* Campos de Input */
        .form-group {
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px 14px 50px;
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
            box-shadow: 0 0 0 4px rgba(0, 57, 198, 0.1);
        }

        .form-group input:focus + .input-icon {
            color: var(--primary-color);
        }

        /* Botao de Login */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
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
            box-shadow: 0 4px 15px rgba(0, 57, 198, 0.3);
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
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
            box-shadow: 0 8px 25px rgba(0, 57, 198, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login.loading {
            opacity: 0.8;
            cursor: not-allowed;
        }

        .btn-login i {
            font-size: 18px;
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        .btn-login.loading .spinner {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer */
        .card-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            position: relative;
            z-index: 1;
        }

        .footer-text {
            color: #6b7280;
            font-size: 13px;
        }

        .footer-text a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-text a:hover {
            color: var(--primary-hover);
        }

        /* Responsividade */
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }

            .left-side {
                min-height: 40vh;
                padding: 30px;
            }

            .left-image {
                display: none;
            }

            .greeting-title {
                font-size: 28px;
            }

            .right-side {
                padding: 30px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 25px;
            }

            .greeting-title {
                font-size: 24px;
            }

            .card-title {
                font-size: 20px;
            }
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
    </style>
</head>

<body>
    <!-- Lado Esquerdo - Saudacao -->
    <div class="left-side">
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <div class="waves"></div>

        <div class="left-content">
            <div class="greeting-icon">
                <?php if (isset($emitente) && !empty($emitente->url_logo)): ?>
                    <img src="<?= $emitente->url_logo ?>" alt="Logo" style="max-width: 120px; max-height: 120px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <?php else: ?>
                    <i class="bx bx-shield-alt-2"></i>
                <?php endif; ?>
            </div>
            <h1 class="greeting-title">
                <?php
                function saudacao($nome = '')
                {
                    $hora = date('H');
                    if ($hora >= 00 && $hora < 12) {
                        return 'Bom dia' . (empty($nome) ? '' : ', ' . $nome);
                    } elseif ($hora >= 12 && $hora < 18) {
                        return 'Boa tarde' . (empty($nome) ? '' : ', ' . $nome);
                    } else {
                        return 'Boa noite' . (empty($nome) ? '' : ', ' . $nome);
                    }
                }
                echo saudacao();
                ?>
            </h1>
            <p class="greeting-subtitle">
                <?php if (isset($emitente) && !empty($emitente->nome)): ?>
                    <?= $emitente->nome ?>
                <?php else: ?>
                    Sistema de Gerenciamento de Ordens de Serviço
                <?php endif; ?>
            </p>
            <img src="<?= base_url() ?>assets/img/dashboard-animate.svg" class="left-image" alt="Map-OS">
        </div>
    </div>

    <!-- Lado Direito - Formulario -->
    <div class="right-side">
        <div class="login-container">
            <div class="login-card">
                <!-- Header -->
                <div class="card-header">
                    <div class="logo-area">
                        <div class="logo-icon">
                            <?php if (isset($emitente) && !empty($emitente->url_logo)): ?>
                                <img src="<?= $emitente->url_logo ?>" alt="Logo" style="border-radius: 8px;">
                            <?php else: ?>
                                <img src="<?= base_url() ?>assets/img/logo-two.png" alt="Logo">
                            <?php endif; ?>
                        </div>
                        <div class="logo-text">
                            <?php if (isset($emitente) && !empty($emitente->nome)): ?>
                                <span style="font-size: 20px; font-weight: 700; color: #1a1f3a;"><?= $emitente->nome ?></span>
                            <?php else: ?>
                                <img src="<?= base_url() ?>assets/img/logo-mapos-branco.png" alt="Map-OS">
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="version-badge">Versão: <?= $this->config->item('app_version'); ?></span>
                    <h2 class="card-title">Acesso Administrativo</h2>
                </div>

                <!-- Alertas -->
                <?php if ($this->session->flashdata('error') != null) { ?>
                    <div class="alert alert-danger">
                        <i class="bx bx-error-circle"></i>
                        <span><?= $this->session->flashdata('error'); ?></span>
                    </div>
                <?php } ?>

                <!-- Formulario -->
                <form id="formLogin" method="post" action="<?= site_url('login/verificarLogin') ?>">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="bx bx-envelope input-icon"></i>
                            <input type="email" name="email" id="email" placeholder="E-mail" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="bx bx-lock-alt input-icon"></i>
                            <input type="password" name="senha" id="senha" placeholder="Senha" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <span class="spinner"></span>
                        <span class="btn-text"><i class="bx bx-log-in"></i> Acessar Sistema</span>
                    </button>
                </form>

                <!-- Footer -->
                <div class="card-footer">
                    <p class="footer-text">
                        <?= date('Y'); ?> &copy;
                        <?php if (isset($emitente) && !empty($emitente->nome)): ?>
                            <?= $emitente->nome ?>
                        <?php else: ?>
                            <a href="https://github.com/RamonSilva20/mapos" target="_blank">Map-OS</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Foco no campo email
            $('#email').focus();

            // Validacao e submit do formulario
            $('#formLogin').on('submit', function(e) {
                e.preventDefault();

                var email = $('#email').val().trim();
                var senha = $('#senha').val();

                // Validacao basica
                if (!email || !senha) {
                    $('.login-card').addClass('shake');
                    setTimeout(function() {
                        $('.login-card').removeClass('shake');
                    }, 500);
                    return false;
                }

                // Loading state
                var $btn = $('#btnLogin');
                $btn.addClass('loading').prop('disabled', true);

                // AJAX Login
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('login/verificarLogin?ajax=true'); ?>",
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            // Sucesso - animacao e redirect
                            $btn.find('.btn-text').html('<i class="bx bx-check"></i> Acesso autorizado!');
                            $btn.css('background', 'linear-gradient(135deg, #10b981, #059669)');

                            setTimeout(function() {
                                window.location.href = "<?= site_url('mapos'); ?>";
                            }, 800);
                        } else {
                            // Erro - animacao shake
                            $('.login-card').addClass('shake');
                            setTimeout(function() {
                                $('.login-card').removeClass('shake');
                            }, 500);

                            // Mostra erro com SweetAlert
                            Swal.fire({
                                icon: 'error',
                                title: 'Acesso negado',
                                text: data.message || 'E-mail ou senha incorretos.',
                                confirmButtonColor: '#0039c6',
                                confirmButtonText: 'Tentar novamente'
                            });

                            // Atualiza token CSRF
                            if (data.MAPOS_TOKEN) {
                                $("input[name='<?= $this->security->get_csrf_token_name(); ?>']").val(data.MAPOS_TOKEN);
                            }

                            // Restaura botao
                            $btn.removeClass('loading').prop('disabled', false);
                        }
                    },
                    error: function() {
                        $('.login-card').addClass('shake');
                        setTimeout(function() {
                            $('.login-card').removeClass('shake');
                        }, 500);

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao processar login. Tente novamente.',
                            confirmButtonColor: '#0039c6'
                        });

                        $btn.removeClass('loading').prop('disabled', false);
                    }
                });

                return false;
            });

            // Efeito visual nos inputs
            $('.form-group input').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#formLogin').submit();
                }
            });
        });
    </script>
</body>

</html>