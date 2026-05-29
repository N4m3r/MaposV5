<?php
// tools/remove_boxicons.php
// Remove <i class='bx ...'></i> icons from all view PHP files
$viewDir = __DIR__ . '/../application/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));
foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
        $content = file_get_contents($file->getPathname());
        // Remove <i class='bx ...'></i> tags (including possible double quotes)
        $newContent = preg_replace('/<i\s+class=["\']bx[^"\']*["\']\s*><\/i>/i', '', $content);
        // Also remove any leftover empty spaces between tags
        $newContent = preg_replace('/\s{2,}/', ' ', $newContent);
        if ($newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Processed: {$file->getPathname()}\n";
        }
    }
}
?>
