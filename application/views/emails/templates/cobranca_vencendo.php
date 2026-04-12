<!-- Template: Cobrança Vencendo -->
<h2>📅 Cobran&ccedil;a com Vencimento Pr&oacute;ximo</h2>

<p>Ol<?= htmlspecialchars($cliente_nome ?? 'Cliente') ?>,</p>

<p>Gostaríamos de lembrá-lo que há uma cobrança vencendo <strong>amanhã</strong>.</p>

<div class="details">
    <p><strong>Cobrança #:</strong> <?= $cobranca_id ?? '' ?></p>
    <p><strong>Descrição:</strong> <?= htmlspecialchars($cobranca_descricao ?? '') ?></p>
    <p><strong>Valor:</strong> R$ <?= number_format($cobranca_valor ?? 0, 2, ',', '.') ?></p>
    <p><strong>Vencimento:</strong> <?= $cobranca_vencimento ?? '' ?></p>
</div>

<?php if (!empty($cobranca_link)): ?
<p style="text-align: center;">
    <a href="<?= $cobranca_link ?>" class="button">
        Pagar Agora
    </a>
</p>
<?php endif; ?>

<p>Caso já tenha efetuado o pagamento, por favor desconsidere este email.</p>

<p>Atenciosamente,<br><strong><?= $app_name ?? 'MAPOS' ?></strong></p>
