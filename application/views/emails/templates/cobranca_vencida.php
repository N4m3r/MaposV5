<!-- Template: Cobrança Vencida -->
<h2 style="color: #dc3545;">⚠️ Cobran&ccedil;a em Atraso</h2>

<p>Ol<?= htmlspecialchars($cliente_nome ?? 'Cliente') ?>,</p>

<p>Identificamos que a cobrança <strong>#<?= $cobranca_id ?? '' ?></strong> encontra-se em atraso.</p>

<div class="details" style="border-left: 4px solid #dc3545;">
    <p><strong>Cobrança #:</strong> <?= $cobranca_id ?? '' ?></p>
    <p><strong>Descrição:</strong> <?= htmlspecialchars($cobranca_descricao ?? '') ?></p>
    <p><strong>Valor:</strong> R$ <?= number_format((float)($cobranca_valor ?? 0), 2, ',', '.') ?></p>
    <p><strong>Vencimento:</strong> <?= $cobranca_vencimento ?? '' ?></p>
    <p><strong>Dias em Atraso:</strong> <?= $dias_atraso ?? '1' ?></p>
</div>

<p>Para regularizar sua situação, efetue o pagamento o mais breve possível.</p>

<?php if (!empty($cobranca_link)): ?>
<p style="text-align: center;">
    <a href="<?= $cobranca_link ?>" class="button" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
        Pagar Agora
    </a>
</p>
<?php endif; ?>

<p>Caso já tenha efetuado o pagamento, por favor desconsidere este email.</p>

<p>Atenciosamente,<br><strong><?= $app_name ?? 'MAPOS' ?></strong></p>
