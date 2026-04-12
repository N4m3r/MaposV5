<?php
/**
 * Componente: Modal
 * Props: id, title, content, footer, size, backdrop, keyboard
 */

$id = $id ?? 'modal-' . uniqid();
$title = $title ?? '';
$content = $content ?? '';
$footer = $footer ?? '';
$size = $size ?? ''; // sm, lg, xl
$backdrop = $backdrop ?? 'static';
$keyboard = $keyboard ?? false;

$sizeClass = $size ? "modal-{$size}" : '';
?>

<div class="modal fade" id="<?= $id ?>" tabindex="-1" data-bs-backdrop="<?= $backdrop ?>" data-bs-keyboard="<?= $keyboard ? 'true' : 'false' ?>">
    <div class="modal-dialog <?= $sizeClass ?>">
        <div class="modal-content">
            <?php if ($title): ?>
            <div class="modal-header">
                <h5 class="modal-title"><?= htmlspecialchars($title) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <?php endif; ?>

            <div class="modal-body">
                <?= $content ?>
            </div>

            <?php if ($footer): ?>
            <div class="modal-footer">
                <?= $footer ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
