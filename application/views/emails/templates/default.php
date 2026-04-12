<!-- Template: Padrão para emails genéricos -->
<h2><?= htmlspecialchars($titulo ?? 'Notifica&ccedil;&atilde;o') ?></h2>

<p>Ol<?= htmlspecialchars($destinatario ?? 'Cliente') ?>,</p>

<p><?= nl2br(htmlspecialchars($mensagem ?? '')) ?></p>

<?php if (!empty($link)): ?
<p style="text-align: center;">
    <a href="<?= $link ?>" class="button">
        <?= htmlspecialchars($link_texto ?? 'Acessar') ?
    </a>
</p>
<?php endif; ?>

<p>Atenciosamente,<br><strong><?= $app_name ?? 'MAPOS' ?></strong></p>
