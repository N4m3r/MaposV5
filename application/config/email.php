<?php

$config['protocol'] = $_ENV['EMAIL_PROTOCOL'] ?? 'smtp';
$config['smtp_host'] = $_ENV['EMAIL_SMTP_HOST'] ?? 'smtp.gmail.com';
$config['smtp_crypto'] = $_ENV['EMAIL_SMTP_CRYPTO'] ?? 'tls'; // tls or ssl
$config['smtp_port'] = $_ENV['EMAIL_SMTP_PORT'] ?? 587;
$config['smtp_user'] = $_ENV['EMAIL_SMTP_USER'] ?? 'seuemail@gmail.com';
$config['smtp_pass'] = $_ENV['EMAIL_SMTP_PASS'] ?? 'senhadoemail';
$config['validate'] = isset($_ENV['EMAIL_VALIDATE']) ? filter_var($_ENV['EMAIL_VALIDATE'], FILTER_VALIDATE_BOOLEAN) : true; // validar email
$config['mailtype'] = $_ENV['EMAIL_MAILTYPE'] ?? 'html'; // text ou html
$config['charset'] = $_ENV['EMAIL_CHARSET'] ?? 'utf-8';
$config['newline'] = $_ENV['EMAIL_NEWLINE'] ?? "\r\n";
$config['bcc_batch_mode'] = isset($_ENV['EMAIL_BCC_BATCH_MODE']) ? filter_var($_ENV['EMAIL_BCC_BATCH_MODE'], FILTER_VALIDATE_BOOLEAN) : false;
$config['wordwrap'] = isset($_ENV['EMAIL_WORDWRAP']) ? filter_var($_ENV['EMAIL_WORDWRAP'], FILTER_VALIDATE_BOOLEAN) : false;
$config['priority'] = $_ENV['EMAIL_PRIORITY'] ?? 3; // 1, 2, 3, 4, 5 | Email Priority. 1 = highest. 5 = lowest. 3 = normal.

// Configurações do Sistema de Fila
$config['email_queue_enabled'] = $_ENV['EMAIL_QUEUE_ENABLED'] ?? true;
$config['email_batch_size'] = $_ENV['EMAIL_BATCH_SIZE'] ?? 50;
$config['email_retry_attempts'] = $_ENV['EMAIL_RETRY_ATTEMPTS'] ?? 3;
$config['email_from'] = $_ENV['EMAIL_FROM'] ?? 'noreply@mapos.com';
$config['email_from_name'] = $_ENV['EMAIL_FROM_NAME'] ?? 'MAPOS';

// Configurações Redis (opcional)
$config['redis_enabled'] = $_ENV['REDIS_ENABLED'] ?? false;
$config['redis_host'] = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
$config['redis_port'] = $_ENV['REDIS_PORT'] ?? 6379;
$config['redis_password'] = $_ENV['REDIS_PASSWORD'] ?? null;

// Webhook Secret
$config['webhook_secret'] = $_ENV['WEBHOOK_SECRET'] ?? bin2hex(random_bytes(32));
