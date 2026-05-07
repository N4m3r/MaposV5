<?php
$file = 'application/controllers/api/v2/BaseController.php';
$content = file_get_contents($file);
$search = "    public function _remap(string \$method, array \$params = []): void\n    {\n        \$httpMethod = strtolower(\$this->input->method());";
$replace = "    public function _remap(string \$method, array \$params = []): void\n    {\n        die('_remap called for method: ' . \$method);\n        \$httpMethod = strtolower(\$this->input->method());";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
