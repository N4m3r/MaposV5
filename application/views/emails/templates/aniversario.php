<!-- Template: Aniversário do Cliente -->
<div style="text-align: center; padding: 20px;">
    <h1 style="font-size: 48px; margin: 10px 0;">🎂🎉</h1>
    <h2>Feliz Anivers&aacute;rio!</h2>
</div>

<p>Ol<?= htmlspecialchars($cliente_nome ?? 'Cliente') ?>,</p>

<p>Hoje &eacute; um dia especial! Queremos desejar um feliz anivers&aacute;rio cheio de alegria e conquistas.</p>

<div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 8px; padding: 30px; margin: 20px 0; text-align: center;">
    <h3 style="margin-top: 0; color: #764ba2;">Nossa Equipe Deseja:</h3>
    <ul style="list-style: none; padding: 0; text-align: left; max-width: 400px; margin: 0 auto;">
        <li>🎁 Muita sa&uacute;de e felicidade</li>
        <li>🌟 Sucesso em todos os seus projetos</li>
        <li>💪 Realiza&ccedil;&otilde;es pessoais e profissionais</li>
        <li>🤝 Muitos anos de parceria conosco</li>
    </ul>
</div>

<?php if (!empty($cupom_desconto)): ?
<div style="background-color: #d4edda; border: 2px dashed #28a745; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
    <h3 style="margin-top: 0; color: #155724;">🎁 Presente de Anivers&aacute;rio!</h3>
    <p>Use o cupom abaixo e ganhe <strong><?= $desconto ?? '10%' ?> de desconto</strong> em sua pr&oacute;xima compra:</p>
    <div style="background: white; display: inline-block; padding: 15px 30px; border-radius: 6px; font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #28a745; margin: 10px 0;">
        <?= $cupom_desconto ?>
    </div>
    <p style="font-size: 12px; color: #155724; margin: 10px 0 0 0;">V&aacute;lido at&eacute; <?= $cupom_validade ?? date('d/m/Y', strtotime('+30 days')) ?></p>
</div>
<?php endif; ?>

<p style="text-align: center; margin-top: 30px;">
    <strong>Parab&eacute;ns e tenha um &oacute;timo dia! 🎈</strong>
</p>

<p>Com carinho,<br><strong>Equipe <?= $app_name ?? 'MAPOS' ?></strong></p>
