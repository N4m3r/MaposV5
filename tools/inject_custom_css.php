<?php
/**
 * inject_custom_css.php
 * Scans view files and adds a <link> to custom.css if missing.
 */
$baseDir = __DIR__ . '/../application/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        if (strpos($content, 'assets/css/custom.css') === false) {
            // Find closing </head> tag (case-insensitive)
            $pos = stripos($content, '</head>');
            if ($pos !== false) {
                $linkTag = "    <link rel=\"stylesheet\" href=\"<?php echo base_url(); ?>assets/css/custom.css\" />\n";
                $newContent = substr_replace($content, $linkTag, $pos, 0);
                file_put_contents($path, $newContent);
                echo "Injected custom.css into: $path\n";
            } else {
                // If no </head>, prepend at start
                $linkTag = "<link rel=\"stylesheet\" href=\"<?php echo base_url(); ?>assets/css/custom.css\" />\n";
                file_put_contents($path, $linkTag . $content);
                echo "Prepended custom.css into: $path (no </head>)\n";
            }
        }
    }
}
?>
