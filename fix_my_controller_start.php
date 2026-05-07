<?php
$file = 'application/core/MY_Controller.php';
$content = file_get_contents($file);
$search = "    public function __construct()\n    {\n        parent::__construct();";
$replace = "    public function __construct()\n    {\n        error_log('MY_Controller construct started for: ' . ($_SERVER['REQUEST_URI'] ?? ''));\n        parent::__construct();";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done\n";
