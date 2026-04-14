<!-- Template: Ordem de Serviço Criada -->
<h2>Ordem de Serviço Criada</h2>

<p>Ol<?= htmlspecialchars($cliente_nome ?? 'Cliente') ?>,</p>

<p>Sua ordem de serviço <strong>#<?= $os_id ?? '' ?></strong> foi criada com sucesso.</p>

<div class="details">
    <p><strong>Data de Abertura:</strong> <?= $os_data ?? date('d/m/Y') ?></p>
    <p><strong>Descrição:</strong> <?= htmlspecialchars($os_descricao ?? '') ?></p>
    <p><strong>Status:</strong> <?= $os_status ?? 'Em Aberto' ?></p>
    <?php if (!empty($os_valor)): ?>
    <p><strong>Valor Estimado:</strong> R$ <?= number_format($os_valor, 2, ',', '.') ?></p>
    <?php endif; ?>
</div>

<p style="text-align: center;">
    <a href="<?= base_url("os/visualizar/" . ($os_id ?? '')) ?>" class="button">
        Acompanhar OS
    </a>
</p>

<p>Se tiver alguma dúvida, entre em contato conosco.</p>

<p>Atenciosamente,<br><strong><?= $app_name ?? 'MAPOS' ?></strong></p>
