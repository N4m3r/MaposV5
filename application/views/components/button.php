<?php
/**
 * Componente: Button
 * Props: text, url, type, size, icon, outline, attributes
 */

$type = $type ?? 'primary';
$size = $size ?? '';
$outline = $outline ?? false;
$icon = $icon ?? '';
$url = $url ?? '';
$text = $text ?? 'Button';
$attributes = $attributes ?? [];

$class = "btn btn-" . ($outline ? "outline-" : "") . $type;
if ($size) {
    $class .= " btn-{$size}";
}

$attrs = '';
foreach ($attributes as $key => $value) {
    $attrs .= " {$key}=\"" . htmlspecialchars($value) . "\"";
}

$content = '';
if ($icon) {
    $content .= "<i class='{$icon}'></i> ";
}
$content .= htmlspecialchars($text);

if ($url):
?>
<a href="<?= htmlspecialchars($url) ?>" class="<?= $class ?>"<?= $attrs ?>><?= $content ?></a>
<?php else: ?>
<button type="button" class="<?= $class ?>"<?= $attrs ?>><?= $content ?></button>
<?php endif; ?>
