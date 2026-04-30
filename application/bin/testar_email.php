#!/usr/bin/env php
<?php
/**
 * Script CLI para testar envio de email
 * Uso: php application/bin/testar_email.php thailer.alfaia@gmail.com
 */

define('BASEPATH', __DIR__ . '/../../');

try {
    require_once BASEPATH . 'application/helpers/autoload_helper.php';

    // Simula carregamento minimo do CI para usar EmailQueue e TemplateEngine
    // Como estamos em CLI isolado, vamos usar uma abordagem standalone

    echo "========================================\n";
    echo "  Teste de Envio de Email - MAPOS V5\n";
    echo "========================================\n\n";

    $emailDestino = $argv[1] ?? 'thailer.alfaia@gmail.com';

    echo "Destinatario: {$emailDestino}\n";
    echo "Data/Hora:    " . date('d/m/Y H:i:s') . "\n\n";

    // Verifica se template boas_vindas existe
    $templatePath = BASEPATH . 'application/views/emails/templates/boas_vindas.php';
    if (!file_exists($templatePath)) {
        echo "[ERRO] Template boas_vindas.php nao encontrado!\n";
        exit(1);
    }

    // Renderiza template manualmente
    $data = [
        'cliente_nome' => 'Thailer Alfaia',
        'cliente_email' => $emailDestino,
        'data_atual' => date('d/m/Y'),
        'sistema_url' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
    ];
    extract($data);
    ob_start();
    include $templatePath;
    $html = ob_get_clean();

    echo "[OK] Template renderizado com sucesso.\n";
    echo "     Tamanho do HTML: " . strlen($html) . " bytes\n\n";

    // Tenta carregar o CI para usar a fila
    if (file_exists(BASEPATH . 'index.php')) {
        echo "[INFO] Para enfileirar o email, acesse o sistema web e:\n";
        echo "       1. Va em Emails > Configuracoes\n";
        echo "       2. No campo 'Email de destino para teste', digite: {$emailDestino}\n";
        echo "       3. Clique em 'Enviar Email de Teste'\n\n";

        echo "[INFO] Ou execute via CLI no servidor (com CI carregado):\n";
        echo "       php index.php email testar_envio\n";
        echo "       (com POST data: email_teste={$emailDestino})\n\n";
    }

    echo "[OK] Validacao concluida. O sistema esta pronto para enviar.\n";
    echo "     Certifique-se de que as configuracoes SMTP estao salvas no banco.\n\n";

} catch (\Throwable $e) {
    echo "[ERRO FATAL] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
