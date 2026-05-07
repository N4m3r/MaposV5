<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);

// API block
$content = str_replace(
    "            // Não redirecionar - controllers da API têm autenticação própria",
    "            // Não redirecionar - controllers da API têm autenticação própria\n            die('API block reached: ' . \$controller . ' dir:' . (\$directory ?? 'NULL'));",
    $content
);

// Tecnicos public block
$content = str_replace(
    "            // Não redirecionar - o Tecnicos controller tem sua própria autenticação",
    "            // Não redirecionar - o Tecnicos controller tem sua própria autenticação\n            die('Tecnicos public block reached');",
    $content
);

// Tecnicos protected block
$content = str_replace(
    "            // Verifica sessão de técnico: nova (tec_logado) ou legada (logged_in + tec_id)",
    "            // Verifica sessão de técnico: nova (tec_logado) ou legada (logged_in + tec_id)\n            die('Tecnicos protected block reached');",
    $content
);

file_put_contents($file, $content);
echo "Done\n";
