<?php
/**
 * Componente: Notification/Toast
 * Props: type, message, title, dismissible, autoHide
 */

$type = $type ?? 'info';
$message = $message ?? '';
$title = $title ?? '';
$dismissible = $dismissible ?? true;
$autoHide = $autoHide ?? true;
$delay = $delay ?? 5000;

// Mapeamento de tipos para ícones
$iconMap = [
    'success' => 'fas fa-check-circle',
    'error' => 'fas fa-times-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle'
];

$icon = $iconMap[$type] ?? $iconMap['info'];
$bgClass = "alert-{$type}";

$id = 'toast-' . uniqid();
?>

<div
    id="<?= $id ?>"
    class="alert <?= $bgClass ?> <?= $dismissible ? 'alert-dismissible fade show' : '' ?> shadow-sm"
    role="alert"
    <?= $autoHide ? "data-bs-delay=\"{$delay}\"" : '' ?>
>
    <div class="d-flex align-items-center">
        <i class="<?= $icon ?> me-2"></i>
        <div>
            <?php if ($title): ?>
            <strong><?= htmlspecialchars($title) ?></strong>
            <br>
            <?php endif; ?>
            <?= $message ?>
        </div>
    </div>

    <?php if ($dismissible): ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    <?php endif; ?>
</div>

<?php if ($autoHide): ?>
<script>
    setTimeout(function() {
        var el = document.getElementById('<?= $id ?>');
        if (el) {
            el.classList.remove('show');
            setTimeout(function() {
                el.remove();
            }, 150);
        }
    }, <?= $delay ?>);
</script>
<?php endif; ?>
