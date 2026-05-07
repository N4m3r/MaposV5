<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "        // Carregar library de permissoes\n        \$this->load->library('permission');\n    }";
$replace = "        // Carregar library de permissoes\n        \$this->load->library('permission');\n        die('MY_Controller finished');\n    }";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
