<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "        // Para outros controllers, verificar sessao padrao (admin) OU sessao de tecnico\n        else {";
$replace = "        // Para outros controllers, verificar sessao padrao (admin) OU sessao de tecnico\n        else {\n            die(\"Redirecting to login: controller=\" . $controller . \" directory=\" . ($directory ?? ''));";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
