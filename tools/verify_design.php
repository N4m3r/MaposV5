<?php
/**
 * verify_design.php
 * Scans all view files for required design elements.
 */
$baseDir = __DIR__ . '/../application/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
$missingCustom = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        // Check for custom.css inclusion
        if (strpos($content, 'assets/css/custom.css') === false) {
            $missingCustom[] = $file->getPathname();
        }
    }
}
if (!empty($missingCustom)) {
    echo "Views missing custom.css:\n";
    foreach ($missingCustom as $path) {
        echo $path . "\n";
    }
    exit(1);
}
echo "All view files include custom.css.\n";
?>
