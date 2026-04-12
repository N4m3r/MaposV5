<?php
/**
 * Componente: Form Input
 * Props: name, label, type, value, placeholder, required, help, errors
 */

$name = $name ?? '';
$label = $label ?? '';
$type = $type ?? 'text';
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$help = $help ?? '';
$errors = $errors ?? [];
$attributes = $attributes ?? [];

$hasError = isset($errors[$name]) && !empty($errors[$name]);
$inputClass = 'form-control' . ($hasError ? ' is-invalid' : '');

$attrs = '';
foreach ($attributes as $key => $val) {
    $attrs .= " {$key}=\"" . htmlspecialchars($val) . "\"";
}
?>

<div class="mb-3">
    <?php if ($label): ?>
    <label for="<?= $name ?>" class="form-label">
        <?= htmlspecialchars($label) ?>
        <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
    </label>
    <?php endif; ?>

    <input
        type="<?= $type ?>"
        class="<?= $inputClass ?>"
        id="<?= $name ?>"
        name="<?= $name ?>"
        value="<?= htmlspecialchars($value) ?>"
        placeholder="<?= htmlspecialchars($placeholder) ?>"
        <?= $required ? 'required' : '' ?>
        <?= $attrs ?>
    >

    <?php if ($help): ?>
    <div class="form-text"><?= htmlspecialchars($help) ?></div>
    <?php endif; ?>

    <?php if ($hasError): ?>
    <div class="invalid-feedback"><?= is_array($errors[$name]) ? implode(', ', $errors[$name]) : $errors[$name] ?></div>
    <?php endif; ?>
</div>
