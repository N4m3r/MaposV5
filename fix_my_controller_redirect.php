<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "redirect('login');";
$replace = "die('Redirecting to login: controller=' . \$controller . ' directory=' . (\$directory ?? 'NULL') . ' method=' . \$metodo);";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
