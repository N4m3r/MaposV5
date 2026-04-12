<!-- Template: Ordem de Serviço Prestes a Vencer -->
<h2>⏰ Ordem de Serviço em Prazo</h2>

<p>Ol<?= htmlspecialchars($cliente_nome ?? 'Cliente') ?>,</p>

<p>Gostaríamos de lembrá-lo que a ordem de serviço <strong>#<?= $os_id ?? '' ?></strong> est&aacute; prevista para ser finalizada em 2 dias.</p>

<div class="details">
    <p><strong>OS #:</strong> <?= $os_id ?? '' ?></p>
    <p><strong>Data Prevista:</strong> <?= $os_data_final ?? '' ?></p>
    <p><strong>Descri&ccedil;&atilde;o:</strong> <?= htmlspecialchars($os_descricao ?? '') ?></p>
    <p><strong>Status Atual:</strong> <?= $os_status ?? '' ?></p>
</div>

<p>Estamos trabalhando para entregar no prazo combinado. Caso haja necessidade de mais tempo, entraremos em contato.</p>

<p style="text-align: center;">
    <a href="<?= base_url("os/visualizar/" . ($os_id ?? '')) ?>" class="button">
        Ver Detalhes da OS
    </a>
</p>

<p>Agradecemos a confian&ccedil;a!</p>

<p>Atenciosamente,<br><strong><?= $app_name ?? 'MAPOS' ?></strong></p>
