<?php
/**
 * Componente: Card
 * Props: title, content, footer, class, headerAction
 */

$title = $title ?? '';
$content = $content ?? '';
$footer = $footer ?? '';
$class = $class ?? '';
$headerAction = $headerAction ?? '';
?>

<div class="card <?= $class ?>">
    <?php if ($title): ?>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><?= htmlspecialchars($title) ?></h5>
        <?php if ($headerAction): ?>
        <div class="card-actions"><?= $headerAction ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="card-body">
        <?= $content ?>
    </div>

    <?php if ($footer): ?>
    <div class="card-footer">
        <?= $footer ?>
    </div>
    <?php endif; ?>
</div>
