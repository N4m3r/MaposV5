<?php
/**
 * LGPD Consentimento - Banner para area do cliente
 * Exibe modal de consentimento se o cliente ainda nao consentiu
 */
$CI =& get_instance();
$clienteId = $CI->session->userdata('cliente_id') ?: $CI->session->userdata('id');
$consentimento = $CI->session->userdata('consentimento_lgpd');
?>

<?php if ($clienteId && (!isset($consentimento) || !$consentimento)): ?>
<style>
.lgpd-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6); z-index: 99998;
    display: flex; align-items: center; justify-content: center;
}
.lgpd-modal {
    background: #fff; border-radius: 12px; padding: 30px;
    max-width: 520px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    z-index: 99999;
}
.lgpd-modal h4 { margin: 0 0 12px; color: #2d3436; font-size: 1.3em; }
.lgpd-modal p { color: #636e72; line-height: 1.6; margin-bottom: 18px; font-size: 0.95em; }
.lgpd-modal .lgpd-actions { display: flex; gap: 10px; justify-content: flex-end; }
.lgpd-modal .btn-accept { background: #27ae60; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; cursor: pointer; font-weight: 600; }
.lgpd-modal .btn-accept:hover { background: #219653; }
.lgpd-modal .btn-reject { background: #dfe6e9; color: #636e72; border: none; padding: 10px 24px; border-radius: 6px; cursor: pointer; }
.lgpd-modal .btn-reject:hover { background: #b2bec3; }
.lgpd-modal .lgpd-detail { font-size: 0.82em; color: #b2bec3; margin-top: 12px; }
</style>

<div class="lgpd-overlay" id="lgpdConsentOverlay">
    <div class="lgpd-modal">
        <h4><i class="fas fa-shield-alt" style="color: #27ae60;"></i> Protecao de Dados (LGPD)</h4>
        <p>
            Solicitamos seu consentimento para processar seus dados pessoais (nome, documento, telefone, e-mail, endereco)
            conforme a Lei Geral de Protecao de Dados (Lei 13.709/2018). Seus dados sao utilizados exclusivamente para
            prestacao dos servicos contratados e comunicacao relacionada.
        </p>
        <p>
            Voce pode solicitar a qualquer momento: acesso aos seus dados, correcao, anonimizacao ou portabilidade.
        </p>
        <div class="lgpd-actions">
            <button class="btn-reject" onclick="lgpdReject()">Recusar</button>
            <button class="btn-accept" onclick="lgpdAccept()">Aceitar</button>
        </div>
        <div class="lgpd-detail">
            Ao aceitar, voce concorda com o processamento de seus dados conforme nossa politica de privacidade.
            Ao recusar, funcionalidades da area do cliente poderao ser limitadas.
        </div>
    </div>
</div>

<script>
function lgpdAccept() {
    fetch('<?php echo base_url("index.php/mine/lgpd_consentimento"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token-name"]').attr('content') ? $('input[name="'+$('meta[name="csrf-token-name"]').attr('content')+'"]').val() : ''
        },
        body: JSON.stringify({ origem_dados: 'portal_consentimento' })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('lgpdConsentOverlay').style.display = 'none';
    })
    .catch(function() {
        document.getElementById('lgpdConsentOverlay').style.display = 'none';
    });
}

function lgpdReject() {
    document.getElementById('lgpdConsentOverlay').style.display = 'none';
}
</script>
<?php endif; ?>