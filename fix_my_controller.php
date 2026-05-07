<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "        \$this->load->library('permission');\n    }\n\n    private function load_configuration()";
$replace = "        \$this->load->library('permission');\n        error_log('MY_Controller construct finished for: ' . (\$_SERVER['REQUEST_URI'] ?? ''));\n    }\n\n    private function load_configuration()";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
