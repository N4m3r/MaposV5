<div class="row-fluid">
    <div id="footer" class="span12">
        <a class="pecolor" href="https://github.com/RamonSilva20/mapos" target="_blank">
            <?= date('Y') ?> &copy; Ramon Silva - Map-OS - Versão: <?= $this->config->item('app_version') ?>
        </a>
    </div>
</div>
<!--end-Footer-part-->
<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
<script src="<?= base_url() ?>assets/js/matrix.js"></script>
<script type="text/javascript">
// ==============================
// NOTIFICAÇÕES
// ==============================
var notifBaseUrl = '<?= base_url() ?>index.php/notificacoes';

function carregarNotificacoes() {
    $.ajax({
        url: notifBaseUrl + '/listar',
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
            if (!resp.success) return;
            var count = resp.nao_lidas || 0;
            if (count > 0) {
                $('#notif-count').text(count > 99 ? '99+' : count).show();
            } else {
                $('#notif-count').hide();
            }
            renderNotificacoes(resp.notificacoes);
        }
    });
}

function renderNotificacoes(notifs) {
    var container = $('#notif-items');
    if (!notifs || notifs.length === 0) {
        container.html('<div style="padding:15px;text-align:center;color:#888;">Nenhuma notificação</div>');
        return;
    }
    var html = '';
    for (var i = 0; i < notifs.length; i++) {
        var n = notifs[i];
        var classe = n.lida == 1 ? '' : ' nao-lida';
        var icone = n.icone || 'bx-bell';
        var data = formatarDataNotif(n.data_notificacao);
        var url = n.url ? n.url : '#';
        html += '<div class="notif-item' + classe + '" data-id="' + n.id + '" data-url="' + url + '">' +
            '<div class="notif-titulo"><i class="bx ' + icone + ' notif-icone"></i>' + escapeHtml(n.titulo) + '</div>' +
            '<div class="notif-msg">' + escapeHtml(n.mensagem) + '</div>' +
            '<div class="notif-data">' + data + '</div>' +
            '</div>';
    }
    container.html(html);
}

function formatarDataNotif(dataStr) {
    if (!dataStr) return '';
    var d = new Date(dataStr);
    var agora = new Date();
    var diff = agora - d;
    var mins = Math.floor(diff / 60000);
    if (mins < 1) return 'Agora';
    if (mins < 60) return mins + ' min atrás';
    var horas = Math.floor(mins / 60);
    if (horas < 24) return horas + 'h atrás';
    var dias = Math.floor(horas / 24);
    if (dias < 7) return dias + 'd atrás';
    return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Clicar em notificação
$(document).on('click', '.notif-item', function() {
    var id = $(this).data('id');
    var url = $(this).data('url');
    $.post(notifBaseUrl + '/marcar_lida', { id: id });
    $(this).removeClass('nao-lida');
    if (url && url !== '#') {
        window.location.href = url;
    }
});

// Marcar todas como lidas
$('#notif-marcar-todas').on('click', function(e) {
    e.preventDefault();
    $.post(notifBaseUrl + '/marcar_lida', {}, function() {
        carregarNotificacoes();
    });
});

// Polling de notificações a cada 60s
carregarNotificacoes();
setInterval(carregarNotificacoes, 60000);

// ==============================
// TROCAR TEMA
// ==============================
var temaAtual = '<?= isset($configuration["app_theme"]) ? $configuration["app_theme"] : "default" ?>';
var temaCssMap = {
    'default': null,
    'white': 'tema-white.css',
    'puredark': 'tema-pure-dark.css',
    'darkviolet': 'tema-dark-violet.css',
    'darkorange': 'tema-dark-orange.css',
    'whitegreen': 'tema-white-green.css',
    'whiteblack': 'tema-white-black.css'
};
var temaAlternar = { 'default': 'white', 'white': 'default', 'puredark': 'white', 'darkviolet': 'white', 'darkorange': 'white', 'whitegreen': 'default', 'whiteblack': 'default' };
var temaIcone = { 'default': 'bx-sun', 'white': 'bx-moon', 'puredark': 'bx-sun', 'darkviolet': 'bx-sun', 'darkorange': 'bx-sun', 'whitegreen': 'bx-moon', 'whiteblack': 'bx-moon' };

function atualizarIconeTema() {
    var icon = temaIcone[temaAtual] || 'bx-sun';
    $('#theme-icon').removeClass('bx-sun bx-moon').addClass(icon);
}

atualizarIconeTema();

$('#btn-toggle-theme').on('click', function(e) {
    e.preventDefault();
    var novoTema = temaAlternar[temaAtual] || 'white';

    // Remover CSS do tema antigo
    var cssOld = temaCssMap[temaAtual];
    if (cssOld) {
        $('link[href*="' + cssOld + '"]').remove();
    }

    // Adicionar CSS do novo tema
    var cssNew = temaCssMap[novoTema];
    if (cssNew) {
        $('<link rel="stylesheet" href="<?= base_url() ?>assets/css/' + cssNew + '" />').appendTo('head');
    }

    temaAtual = novoTema;
    atualizarIconeTema();

    // Salvar no servidor
    $.post(notifBaseUrl + '/trocar_tema', { tema: novoTema });
});
</script>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var dataTableEnabled = '<?= $configuration['control_datatable'] ?>';
        if(dataTableEnabled == '1') {
            $('#tabela').dataTable( {
                "ordering": false,
                "info": false,
                "language": {
                    "url": "<?= base_url() ?>assets/js/dataTable_pt-br.json",
                },
                "oLanguage": {
                    "sSearch": "Pesquisa rápida na tabela abaixo:"
                }
            } );
        }
    } );
</script>
</html>
