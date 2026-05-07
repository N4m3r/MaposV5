<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "            // Nao redirecionar - controllers da API tem autenticacao propria\n        }\n        // Se for controller Tecnicos e metodo publico, nao redirecionar";
$replace = "            // Nao redirecionar - controllers da API tem autenticacao propria\n            error_log('MY_Controller API check passed for: ' . ($controller ?? '') . ' dir: ' . ($directory ?? ''));\n        }\n        // Se for controller Tecnicos e metodo publico, nao redirecionar";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
