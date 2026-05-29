<?php
// tools/clean_action_buttons.php
// Remove <img> tags from action buttons and ensure the anchor has the 'btn' class.
$viewDir = __DIR__ . '/../application/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewDir));
foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        // Remove <img ...> tags inside <a> elements (non-greedy)
        $content = preg_replace('#<a([^>]*?)>(\s*)<img[^>]+>(\s*)</a>#i', '<a$1>$2$3</a>', $content);
        // Ensure the anchor has class "btn" (add if missing)
        $content = preg_replace_callback('#<a([^>]*?)class="([^"]*)"([^>]*?)>#i', function($m) {
            $classes = $m[2];
            if (strpos($classes, 'btn') === false) {
                $classes = trim($classes . ' btn');
            }
            return '<a' . $m[1] . 'class="' . $classes . '"' . $m[3] . '>';
        }, $content);
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: {$file->getPathname()}\n";
        }
    }
}
?>
