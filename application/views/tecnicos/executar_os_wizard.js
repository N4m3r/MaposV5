
// ============================================
// WIZARD DE ATENDIMENTO
// ============================================
let wizardStepAtual = 1;
const wizardTotalSteps = 5;
let wizardServicosStatus = {};
let wizardFotos = [];
let wizardSignaturePad = null;

// Inicializar Wizard quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    inicializarWizard();
});

function inicializarWizard() {
    // Inicializar canvas de assinatura do wizard
    const canvas = document.getElementById('wizardSignaturePad');
    if (canvas && typeof SignaturePad !== 'undefined') {
        wizardSignaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Redimensionar canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            if (wizardSignaturePad) wizardSignaturePad.clear();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
    }

    // Inicializar status dos serviços
    const servicosItems = document.querySelectorAll('.wizard-servico-item');
    servicosItems.forEach(item => {
        const servicoId = item.getAttribute('data-servico-id');
        wizardServicosStatus[servicoId] = 'pendente';
    });

    atualizarResumoServicos();
}

// Navegação do Wizard
function wizardProximo() {
    if (wizardStepAtual < wizardTotalSteps) {
        // Validar etapa atual
        if (!validarEtapa(wizardStepAtual)) {
            return;
        }

        wizardStepAtual++;
        atualizarWizardView();
    }
}

function wizardAnterior() {
    if (wizardStepAtual > 1) {
        wizardStepAtual--;
        atualizarWizardView();
    }
}

function irParaEtapa(etapa) {
    if (etapa >= 1 && etapa <= wizardTotalSteps) {
        wizardStepAtual = etapa;
        atualizarWizardView();
    }
}

function validarEtapa(etapa) {
    switch(etapa) {
        case 1:
            // Verificar se pelo menos um checklist está marcado
            const checks = [
                document.getElementById('checkConfirmarLocal'),
                document.getElementById('checkConfirmarCliente'),
                document.getElementById('checkConfirmarEquipamento')
            ];
            const algumMarcado = checks.some(cb => cb && cb.checked);
            if (!algumMarcado) {
                Swal.fire({
                    type: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, confirme pelo menos um item para prosseguir.'
                });
                return false;
            }
            return true;

        case 2:
            // Verificar se todos os serviços foram avaliados
            const pendentes = Object.values(wizardServicosStatus).filter(s => s === 'pendente').length;
            if (pendentes > 0) {
                Swal.fire({
                    type: 'warning',
                    title: 'Serviços Pendentes',
                    text: 'Ainda há ' + pendentes + ' serviço(s) sem status definido. Por favor, marque todos.'
                });
                return false;
            }
            return true;

        case 4:
            // Verificar se observações foram preenchidas
            const obs = document.getElementById('wizardObservacoes')?.value.trim();
            if (!obs) {
                Swal.fire({
                    type: 'warning',
                    title: 'Observações',
                    text: 'Por favor, preencha as observações do atendimento.'
                });
                return false;
            }
            return true;

        default:
            return true;
    }
}

function atualizarWizardView() {
    // Atualizar indicador
    document.getElementById('stepIndicator').textContent = 'Etapa ' + wizardStepAtual + ' de ' + wizardTotalSteps;

    // Atualizar progress bar
    const progressBar = document.getElementById('wizardProgressBar');
    if (progressBar) {
        progressBar.className = 'wizard-progress-bar step-' + wizardStepAtual;
    }

    // Atualizar steps visuais
    document.querySelectorAll('.wizard-step').forEach(step => {
        const stepNum = parseInt(step.getAttribute('data-step'));
        step.classList.remove('active', 'completed');

        if (stepNum === wizardStepAtual) {
            step.classList.add('active');
        } else if (stepNum < wizardStepAtual) {
            step.classList.add('completed');
        }
    });

    // Mostrar conteúdo da etapa atual
    document.querySelectorAll('.wizard-step-content').forEach(content => {
        content.classList.remove('active');
        if (parseInt(content.getAttribute('data-step')) === wizardStepAtual) {
            content.classList.add('active');
        }
    });

    // Se estiver na etapa 5, atualizar resumo
    if (wizardStepAtual === 5) {
        atualizarResumoFinal();
    }
}

// Controle de Serviços no Wizard
function setWizardServicoStatus(servicoId, status) {
    wizardServicosStatus[servicoId] = status;

    const item = document.querySelector('.wizard-servico-item[data-servico-id="' + servicoId + '"]');
    if (item) {
        // Remover classes anteriores
        item.classList.remove('status-conforme', 'status-nao_conforme');

        // Adicionar classe atual
        if (status !== 'pendente') {
            item.classList.add('status-' + status);
        }

        // Atualizar botões
        const botoes = item.querySelectorAll('.btn-status');
        botoes.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-status') === status) {
                btn.classList.add('active');
            }
        });
    }

    atualizarResumoServicos();
}

function atualizarResumoServicos() {
    const total = Object.keys(wizardServicosStatus).length;
    const executados = Object.values(wizardServicosStatus).filter(s => s === 'conforme').length;
    const naoExecutados = Object.values(wizardServicosStatus).filter(s => s === 'nao_conforme').length;
    const pendentes = total - executados - naoExecutados;

    const elPendente = document.getElementById('resumoPendente');
    const elExecutado = document.getElementById('resumoExecutado');

    if (elPendente) {
        elPendente.textContent = pendentes > 0 ? pendentes + ' pendente(s)' : '';
        elPendente.style.display = pendentes > 0 ? 'inline-block' : 'none';
    }

    if (elExecutado) {
        elExecutado.textContent = (executados + naoExecutados) + ' avaliado(s)';
    }
}

// Fotos no Wizard
function abrirCameraWizard() {
    document.getElementById('wizardFotoInput').click();
}

function processarFotoWizard(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const tipo = document.getElementById('tipoFotoWizard').value;

        const reader = new FileReader();
        reader.onload = function(e) {
            adicionarFotoWizard(e.target.result, tipo);
        };
        reader.readAsDataURL(file);
    }
}

function adicionarFotoWizard(imagemBase64, tipo) {
    const foto = {
        id: Date.now(),
        imagem: imagemBase64,
        tipo: tipo,
        data: new Date().toISOString()
    };

    wizardFotos.push(foto);
    renderizarFotosWizard();
}

function traduzirTipoFoto(tipo) {
    const tipos = {
        'antes': 'Antes',
        'durante': 'Durante',
        'depois': 'Depois',
        'detalhe': 'Detalhe'
    };
    return tipos[tipo] || tipo;
}

function renderizarFotosWizard() {
    const container = document.getElementById('wizardFotosPreview');
    if (!container) return;

    let html = '';
    wizardFotos.forEach(foto => {
        html += '<div class="foto-preview-item">' +
            '<img src="' + foto.imagem + '" alt="Foto">' +
            '<div class="foto-tipo-tag">' + traduzirTipoFoto(foto.tipo) + '</div>' +
            '<button type="button" class="btn-remover-foto" onclick="removerFotoWizard(' + foto.id + ')">' +
                '<i class="bx bx-x"></i>' +
            '</button>' +
        '</div>';
    });
    container.innerHTML = html;
}

function removerFotoWizard(fotoId) {
    wizardFotos = wizardFotos.filter(f => f.id !== fotoId);
    renderizarFotosWizard();
}

// Assinatura no Wizard
function limparAssinaturaWizard() {
    if (wizardSignaturePad) {
        wizardSignaturePad.clear();
    }
}

// Resumo Final
function atualizarResumoFinal() {
    const container = document.getElementById('wizardResumoFinal');
    if (!container) return;

    const observacoes = document.getElementById('wizardObservacoes')?.value || '';
    const totalFotos = wizardFotos.length;

    let servicosHtml = '';
    const servicosItems = document.querySelectorAll('.wizard-servico-item');
    servicosItems.forEach(item => {
        const servicoId = item.getAttribute('data-servico-id');
        const nomeEl = item.querySelector('.servico-nome-wizard');
        const nome = nomeEl ? nomeEl.textContent.trim() : 'Serviço';
        const status = wizardServicosStatus[servicoId] || 'pendente';

        let statusIcon = 'bx-circle';
        let statusClass = 'status-pendente';
        let statusText = 'Pendente';

        if (status === 'conforme') {
            statusIcon = 'bx-check';
            statusClass = 'status-ok';
            statusText = 'Executado';
        } else if (status === 'nao_conforme') {
            statusIcon = 'bx-x';
            statusClass = 'status-nok';
            statusText = 'Não Executado';
        }

        servicosHtml += '<div class="resumo-servico-item ' + statusClass + '">' +
            '<i class="bx ' + statusIcon + '"></i>' +
            '<span>' + nome + '</span>' +
            '<small>(' + statusText + ')</small>' +
        '</div>';
    });

    let html = '<div class="resumo-section">' +
        '<h6><i class="bx bx-wrench"></i> Serviços Executados</h6>' +
        '<div class="resumo-servicos-list">' +
            (servicosHtml || '<small>Nenhum serviço</small>') +
        '</div>' +
    '</div>' +
    '<div class="resumo-section">' +
        '<h6><i class="bx bx-camera"></i> Fotos</h6>' +
        '<p>' + totalFotos + ' foto(s) registrada(s)</p>' +
    '</div>';

    if (observacoes) {
        let obsResumo = observacoes.substring(0, 200);
        if (observacoes.length > 200) obsResumo += '...';
        html += '<div class="resumo-section">' +
            '<h6><i class="bx bx-note"></i> Observações</h6>' +
            '<p style="white-space: pre-wrap;">' + obsResumo + '</p>' +
        '</div>';
    }

    container.innerHTML = html;
}

// Finalizar Wizard
function finalizarWizardAtendimento() {
    // Validar assinatura
    if (!wizardSignaturePad || wizardSignaturePad.isEmpty()) {
        Swal.fire({
            type: 'warning',
            title: 'Assinatura Obrigatória',
            text: 'Por favor, colete a assinatura do cliente.'
        });
        return;
    }

    const nomeAssinante = document.getElementById('wizardNomeAssinante')?.value.trim();
    if (!nomeAssinante) {
        Swal.fire({
            type: 'warning',
            title: 'Nome Obrigatório',
            text: 'Por favor, informe o nome de quem está assinando.'
        });
        return;
    }

    const confirmarAssinatura = document.getElementById('checkConfirmarAssinatura');
    if (!confirmarAssinatura?.checked) {
        Swal.fire({
            type: 'warning',
            title: 'Confirmação Necessária',
            text: 'Por favor, confirme que o serviço foi realizado e aceito.'
        });
        return;
    }

    // Coletar dados
    const assinatura = wizardSignaturePad.toDataURL();
    const observacoes = document.getElementById('wizardObservacoes')?.value || '';

    // Preparar dados para envio
    const dados = {
        os_id: typeof osId !== 'undefined' ? osId : null,
        servicos_status: wizardServicosStatus,
        fotos: wizardFotos,
        observacoes: observacoes,
        assinatura: assinatura,
        nome_assinante: nomeAssinante,
        check_confirmacoes: {
            local: document.getElementById('checkConfirmarLocal')?.checked || false,
            cliente: document.getElementById('checkConfirmarCliente')?.checked || false,
            equipamento: document.getElementById('checkConfirmarEquipamento')?.checked || false,
            servico_concluido: document.getElementById('checkServicoConcluido')?.checked || false,
            cliente_orientado: document.getElementById('checkClienteOrientado')?.checked || false,
            local_limpo: document.getElementById('checkLocalLimpo')?.checked || false,
            equipamentos_ok: document.getElementById('checkEquipamentosOk')?.checked || false
        }
    };

    console.log('Dados do wizard:', dados);

    // Aqui você faria o envio para o servidor
    // Por enquanto, mostra mensagem de sucesso
    Swal.fire({
        type: 'success',
        title: 'Atendimento Finalizado!',
        text: 'Todos os dados foram registrados com sucesso.',
        timer: 2000,
        showConfirmButton: false
    }).then(function() {
        // Redirecionar para relatório ou recarregar página
        window.location.reload();
    });
}
