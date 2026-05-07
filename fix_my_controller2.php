<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "        // Nao redirecionar - controllers da API tem autenticacao propria\n        }";
$replace = "        // Nao redirecionar - controllers da API tem autenticacao propria\n            die(\"API access allowed: controller=\" . \$controller . \" directory=\" . (\$directory ?? ''));\n        }";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
