<?php
// tools/standardize_action_buttons.php
// Removes <img> tags from action buttons and substitutes any class containing 'btn-nwe' with the generic 'btn' class.
$viewDir = __DIR__ . '/../application/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));
foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        // Remove <img ...> tags inside <a> elements (non-greedy)
        $content = preg_replace('#<a([^>]*?)>(\s*)<img[^>]+>(\s*)</a>#i', '<a$1>$2$3</a>', $content);
        // Replace any class attribute that contains btn-nwe* with just "btn"
        $content = preg_replace_callback('#class="([^"]*?)btn-nwe[^"]*"#i', function($m){
            return 'class="btn"';
        }, $content);
        // Also replace btn-nwe3, btn-nwe4, etc.
        $content = preg_replace_callback('#class="([^"]*?)btn-nwe[0-9]*[^"]*"#i', function($m){
            return 'class="btn"';
        }, $content);
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Standardized: {$file->getPathname()}\n";
        }
    }
}
?>
