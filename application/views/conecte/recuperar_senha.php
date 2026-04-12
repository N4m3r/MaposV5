<?php
/**
 * View: Recuperar Senha do Usuário do Portal
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Portal do Cliente</title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/matrix-login.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: #2d335b;
            background: linear-gradient(135deg, #2d335b 0%, #1a1f3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .login-header h3 {
            color: #2d335b;
            margin: 0;
            font-weight: 600;
        }
        .login-header p {
            color: #666;
            margin-top: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #2d335b;
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #2d335b;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #1a1f3a;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .login-footer a {
            color: #2d335b;
            text-decoration: none;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <?php if (isset($emitente) && $emitente->url_logo): ?>
                <img src="<?php echo $emitente->url_logo; ?>" alt="Logo">
            <?php else: ?>
                <i class='bx bx-user-circle' style="font-size: 60px; color: #2d335b;"></i>
            <?php endif; ?>
            <h3>Recuperar Senha</h3>
            <p>Digite seu e-mail para receber instruções</p>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-error">
                <i class="bx bx-error-circle"></i> <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <i class="bx bx-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo current_url(); ?>" method="post">
            <div class="form-group">
                <label for="email"><i class="bx bx-envelope"></i> E-mail</label>
                <input type="email" name="email" id="email" placeholder="seu@email.com" required autofocus>
            </div>

            <button type="submit" class="btn-login">
                <i class="bx bx-send"></i> Enhar Instruções
            </button>
        </form>

        <div class="login-footer">
            <p><a href="<?php echo site_url('mine/login_usuario'); ?>">← Voltar para o Login</a></p>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <script>
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>
</body>
</html>
