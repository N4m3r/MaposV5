<?php
$file = 'application/controllers/api/v2/BaseController.php';
$content = file_get_contents($file);
$search = "    public function __construct()\n    {\n        parent::__construct();";
$replace = "    public function __construct()\n    {\n        die(\"BaseController construct reached\");\n        parent::__construct();";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
