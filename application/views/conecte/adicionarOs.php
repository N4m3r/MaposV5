<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>

<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>

<style>
    .ui-datepicker {
        z-index: 9999 !important;
    }

    .trumbowyg-box {
        margin-top: 0;
        margin-bottom: 0;
    }

    /* Wizard Styles */
    .wizard-container {
        background: #fff;
        border-radius: 8px;
        padding: 0;
    }

    .wizard-steps-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        padding: 20px 40px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        position: relative;
    }

    .wizard-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: default;
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .wizard-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 60%;
        width: 80%;
        height: 3px;
        background: #dee2e6;
        z-index: -1;
    }

    .wizard-step.completed:not(:last-child)::after {
        background: #28a745;
    }

    .wizard-step-number {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        color: #6c757d;
        transition: all 0.3s ease;
        margin-bottom: 8px;
    }

    .wizard-step.active .wizard-step-number {
        background: #0039c6;
        border-color: #0039c6;
        color: #fff;
        box-shadow: 0 4px 15px rgba(0, 57, 198, 0.4);
    }

    .wizard-step.completed .wizard-step-number {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .wizard-step.completed .wizard-step-number::after {
        content: '✓';
        font-size: 20px;
    }

    .wizard-step.completed .wizard-step-number span {
        display: none;
    }

    .wizard-step-label {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        text-align: center;
        transition: all 0.3s ease;
    }

    .wizard-step.active .wizard-step-label {
        color: #0039c6;
    }

    .wizard-step.completed .wizard-step-label {
        color: #28a745;
    }

    .wizard-content {
        min-height: 400px;
        padding: 20px 0;
    }

    .wizard-panel {
        display: none;
        animation: fadeIn 0.4s ease;
    }

    .wizard-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Service Type Cards */
    .service-types-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .service-type-card {
        background: #fff;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .service-type-card:hover {
        border-color: #0039c6;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 57, 198, 0.15);
    }

    .service-type-card.selected {
        border-color: #0039c6;
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eeff 100%);
        box-shadow: 0 8px 25px rgba(0, 57, 198, 0.2);
    }

    .service-type-card.selected::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        width: 25px;
        height: 25px;
        background: #28a745;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .service-type-card i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 15px;
        display: block;
        transition: all 0.3s ease;
    }

    .service-type-card:hover i,
    .service-type-card.selected i {
        color: #0039c6;
    }

    .service-type-card h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .service-type-card p {
        font-size: 13px;
        color: #6c757d;
        margin: 8px 0 0;
    }

    /* Form Styles */
    .wizard-form-group {
        margin-bottom: 25px;
    }

    .wizard-form-group label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .wizard-form-group label .required {
        color: #dc3545;
    }

    .wizard-form-group input[type="text"],
    .wizard-form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .wizard-form-group input[type="text"]:focus,
    .wizard-form-group textarea:focus {
        border-color: #0039c6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 57, 198, 0.1);
    }

    .wizard-form-group .help-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    /* Navigation */
    .wizard-navigation {
        display: flex;
        justify-content: space-between;
        padding: 20px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 20px;
    }

    .wizard-navigation .btn {
        padding: 12px 30px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .wizard-navigation .btn-prev {
        background: #6c757d;
        border: none;
        color: #fff;
    }

    .wizard-navigation .btn-prev:hover {
        background: #5a6268;
    }

    .wizard-navigation .btn-next {
        background: #0039c6;
        border: none;
        color: #fff;
        margin-left: auto;
    }

    .wizard-navigation .btn-next:hover {
        background: #002a94;
    }

    .wizard-navigation .btn-submit {
        background: #28a745;
        border: none;
        color: #fff;
        margin-left: auto;
    }

    .wizard-navigation .btn-submit:hover {
        background: #218838;
    }

    /* Step Title */
    .step-title {
        text-align: center;
        margin-bottom: 30px;
    }

    .step-title h3 {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .step-title p {
        font-size: 14px;
        color: #6c757d;
        margin: 0;
    }

    /* Summary Step */
    .summary-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .summary-section h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item .label {
        font-weight: 600;
        color: #6c757d;
    }

    .summary-item .value {
        color: #333;
        text-align: right;
        max-width: 60%;
    }

    .summary-item .value.empty {
        color: #adb5bd;
        font-style: italic;
    }

    /* Validation Error */
    .wizard-form-group.error input,
    .wizard-form-group.error textarea {
        border-color: #dc3545;
    }

    .wizard-form-group .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }

    .wizard-form-group.error .error-message {
        display: block;
    }

    .service-type-card.error {
        border-color: #dc3545;
        animation: shake 0.5s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* Alert Box */
    .wizard-alert {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #0039c6;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .wizard-alert h5 {
        margin: 0;
        color: #0039c6;
        font-weight: 600;
    }

    .wizard-alert p {
        margin: 5px 0 0;
        color: #555;
        font-size: 13px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .wizard-steps-container {
            padding: 15px;
        }

        .wizard-step:not(:last-child)::after {
            left: 70%;
            width: 60%;
        }

        .wizard-step-label {
            font-size: 11px;
        }

        .service-types-grid {
            grid-template-columns: 1fr;
        }

        .wizard-navigation {
            flex-direction: column-reverse;
            gap: 10px;
        }

        .wizard-navigation .btn {
            width: 100%;
            justify-content: center;
        }

        .wizard-navigation .btn-next,
        .wizard-navigation .btn-submit {
            margin-left: 0;
        }
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-diagnoses"></i>
                </span>
                <h5>Solicitar Nova Ordem de Serviço</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style="margin-left: 0">
                    <div class="wizard-container">
                        <form action="<?php echo current_url(); ?>" method="post" id="formOs">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                            <!-- Hidden fields for combined data -->
                            <input type="hidden" name="descricaoProduto" id="descricaoProdutoHidden">
                            <input type="hidden" name="defeito" id="defeitoHidden">

                            <!-- Wizard Steps Indicator -->
                            <div class="wizard-steps-container">
                                <div class="wizard-step active" data-step="1">
                                    <div class="wizard-step-number"><span>1</span></div>
                                    <div class="wizard-step-label">Tipo de Serviço</div>
                                </div>
                                <div class="wizard-step" data-step="2">
                                    <div class="wizard-step-number"><span>2</span></div>
                                    <div class="wizard-step-label">Equipamento</div>
                                </div>
                                <div class="wizard-step" data-step="3">
                                    <div class="wizard-step-number"><span>3</span></div>
                                    <div class="wizard-step-label">Problema</div>
                                </div>
                                <div class="wizard-step" data-step="4">
                                    <div class="wizard-step-number"><span>4</span></div>
                                    <div class="wizard-step-label">Revisão</div>
                                </div>
                            </div>

                            <!-- Wizard Content -->
                            <div class="wizard-content">

                                <!-- Step 1: Service Type -->
                                <div class="wizard-panel active" id="step1">
                                    <div class="step-title">
                                        <h3>Que tipo de serviço você precisa?</h3>
                                        <p>Selecione a categoria que melhor descreve sua necessidade</p>
                                    </div>

                                    <div class="service-types-grid">
                                        <div class="service-type-card" data-value="Suporte Técnico">
                                            <i class="fas fa-laptop-medical"></i>
                                            <h4>Suporte Técnico</h4>
                                            <p>Computadores, redes, software</p>
                                        </div>
                                        <div class="service-type-card" data-value="Manutenção">
                                            <i class="fas fa-tools"></i>
                                            <h4>Manutenção</h4>
                                            <p>Equipamentos, máquinas, instalações</p>
                                        </div>
                                        <div class="service-type-card" data-value="Consultoria">
                                            <i class="fas fa-comments"></i>
                                            <h4>Consultoria</h4>
                                            <p>Orientações, dúvidas técnicas</p>
                                        </div>
                                        <div class="service-type-card" data-value="Outros">
                                            <i class="fas fa-ellipsis-h"></i>
                                            <h4>Outros Serviços</h4>
                                            <p>Demais necessidades</p>
                                        </div>
                                    </div>

                                    <input type="hidden" name="tipoServico" id="tipoServico" required>
                                    <div class="error-message" id="tipoServicoError" style="text-align: center; margin-top: 15px;">Por favor, selecione um tipo de serviço.</div>
                                </div>

                                <!-- Step 2: Equipment Info -->
                                <div class="wizard-panel" id="step2">
                                    <div class="step-title">
                                        <h3>Informações do Equipamento/Serviço</h3>
                                        <p>Descreva o item que precisa de atendimento</p>
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="equipamento">Equipamento/Item <span class="required">*</span></label>
                                        <input type="text" id="equipamento" name="equipamento" placeholder="Ex: Notebook Dell, Impressora HP, Servidor..." required>
                                        <div class="help-text">Descreva o equipamento ou item que precisa de serviço</div>
                                        <div class="error-message">Campo obrigatório.</div>
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="marcaModelo">Marca/Modelo</label>
                                        <input type="text" id="marcaModelo" name="marcaModelo" placeholder="Ex: Dell Inspiron 15 3000">
                                        <div class="help-text">Opcional, mas ajuda no atendimento</div>
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="numeroSerie">Número de Série</label>
                                        <input type="text" id="numeroSerie" name="numeroSerie" placeholder="Ex: SN123456789">
                                        <div class="help-text">Opcional</div>
                                    </div>
                                </div>

                                <!-- Step 3: Problem Description -->
                                <div class="wizard-panel" id="step3">
                                    <div class="step-title">
                                        <h3>Detalhes do Problema</h3>
                                        <p>Descreva o que está acontecendo</p>
                                    </div>

                                    <div class="wizard-alert">
                                        <h5><i class="fas fa-info-circle"></i> Dica</h5>
                                        <p>Quanto mais detalhes você fornecer, mais rápido nossa equipe poderá ajudar. Inclua mensagens de erro, sintomas e quando o problema começou.</p>
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="descricaoProblema">Descrição do Problema <span class="required">*</span></label>
                                        <textarea class="editor" id="descricaoProblema" name="descricaoProblema" rows="5" required></textarea>
                                        <div class="help-text">Descreva detalhadamente o problema ou solicitação</div>
                                        <div class="error-message">Por favor, descreva o problema.</div>
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="quandoComecou">Quando começou o problema?</label>
                                        <input type="text" id="quandoComecou" name="quandoComecou" placeholder="Ex: Ontem, há 3 dias, após atualização...">
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="tentativasSolucao">Já tentou alguma solução?</label>
                                        <textarea class="editor" id="tentativasSolucao" name="tentativasSolucao" rows="3"></textarea>
                                        <div class="help-text">Descreva se já tentou algo para resolver o problema</div>
                                    </div>
                                </div>

                                <!-- Step 4: Review & Submit -->
                                <div class="wizard-panel" id="step4">
                                    <div class="step-title">
                                        <h3>Revisão da Solicitação</h3>
                                        <p>Confirme se todas as informações estão corretas</p>
                                    </div>

                                    <div class="summary-section">
                                        <h4><i class="fas fa-tag"></i> Tipo de Serviço</h4>
                                        <div class="summary-item">
                                            <span class="label">Categoria:</span>
                                            <span class="value" id="summaryTipoServico">-</span>
                                        </div>
                                    </div>

                                    <div class="summary-section">
                                        <h4><i class="fas fa-desktop"></i> Equipamento</h4>
                                        <div class="summary-item">
                                            <span class="label">Equipamento/Item:</span>
                                            <span class="value" id="summaryEquipamento">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Marca/Modelo:</span>
                                            <span class="value" id="summaryMarcaModelo">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Número de Série:</span>
                                            <span class="value" id="summaryNumeroSerie">-</span>
                                        </div>
                                    </div>

                                    <div class="summary-section">
                                        <h4><i class="fas fa-clipboard-list"></i> Detalhes do Problema</h4>
                                        <div class="summary-item">
                                            <span class="label">Descrição:</span>
                                            <span class="value" id="summaryDescricao">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Quando começou:</span>
                                            <span class="value" id="summaryQuandoComecou">-</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Tentativas de solução:</span>
                                            <span class="value" id="summaryTentativas">-</span>
                                        </div>
                                    </div>

                                    <div class="wizard-form-group">
                                        <label for="observacoes">Observações Adicionais</label>
                                        <textarea class="editor" name="observacoes" id="observacoes" rows="3"></textarea>
                                        <div class="help-text">Algo mais que nossa equipe deva saber?</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wizard Navigation -->
                            <div class="wizard-navigation">
                                <button type="button" class="btn btn-prev" id="btnPrev" style="display: none;">
                                    <i class="bx bx-left-arrow-alt"></i> Voltar
                                </button>
                                <button type="button" class="btn btn-next" id="btnNext">
                                    Próximo <i class="bx bx-right-arrow-alt"></i>
                                </button>
                                <button type="submit" class="btn btn-submit" id="btnSubmit" style="display: none;">
                                    <i class="bx bx-check"></i> Confirmar e Enviar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var currentStep = 1;
        var totalSteps = 4;

        // Initialize Trumbowyg editors
        $('.editor').trumbowyg({
            lang: 'pt_br',
            semantic: { 'strikethrough': 's' },
            btns: [
                ['viewHTML'],
                ['undo', 'redo'],
                ['formatting'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat']
            ],
            autogrow: true
        });

        // Service type card selection
        $('.service-type-card').click(function() {
            $('.service-type-card').removeClass('selected');
            $(this).addClass('selected');
            $('#tipoServico').val($(this).data('value'));
            $(this).closest('.wizard-panel').find('.service-type-card').removeClass('error');
        });

        // Navigation buttons
        $('#btnNext').click(function() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    goToStep(currentStep + 1);
                }
            }
        });

        $('#btnPrev').click(function() {
            if (currentStep > 1) {
                goToStep(currentStep - 1);
            }
        });

        // Form submission
        $('#formOs').submit(function(e) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
                return false;
            }

            // Combine data for submission
            combineFormData();
        });

        function goToStep(step) {
            // Hide current panel
            $('#step' + currentStep).removeClass('active');

            // Show new panel
            $('#step' + step).addClass('active');

            // Update steps indicator
            $('.wizard-step').removeClass('active');
            $('.wizard-step[data-step="' + step + '"]').addClass('active');

            // Mark previous steps as completed
            $('.wizard-step').each(function() {
                var stepNum = parseInt($(this).data('step'));
                if (stepNum < step) {
                    $(this).addClass('completed');
                } else if (stepNum > step) {
                    $(this).removeClass('completed');
                }
            });

            // Update buttons
            if (step === 1) {
                $('#btnPrev').hide();
            } else {
                $('#btnPrev').show();
            }

            if (step === totalSteps) {
                $('#btnNext').hide();
                $('#btnSubmit').show();
                updateSummary();
            } else {
                $('#btnNext').show();
                $('#btnSubmit').hide();
            }

            currentStep = step;

            // Scroll to top of wizard
            $('html, body').animate({
                scrollTop: $('.wizard-steps-container').offset().top - 20
            }, 300);
        }

        function validateStep(step) {
            var isValid = true;

            if (step === 1) {
                if (!$('#tipoServico').val()) {
                    $('.service-type-card').addClass('error');
                    $('#tipoServicoError').show();
                    isValid = false;
                } else {
                    $('.service-type-card').removeClass('error');
                    $('#tipoServicoError').hide();
                }
            }

            if (step === 2) {
                var equipamento = $('#equipamento').val().trim();
                if (!equipamento) {
                    $('#equipamento').closest('.wizard-form-group').addClass('error');
                    isValid = false;
                } else {
                    $('#equipamento').closest('.wizard-form-group').removeClass('error');
                }
            }

            if (step === 3) {
                var descricao = $('#descricaoProblema').val();
                // Strip HTML tags for validation
                var textDescricao = descricao ? descricao.replace(/<[^>]*>/g, '').trim() : '';
                if (!textDescricao) {
                    $('#descricaoProblema').closest('.wizard-form-group').addClass('error');
                    isValid = false;
                } else {
                    $('#descricaoProblema').closest('.wizard-form-group').removeClass('error');
                }
            }

            return isValid;
        }

        function updateSummary() {
            $('#summaryTipoServico').text($('#tipoServico').val() || '-');

            var equipamento = $('#equipamento').val().trim();
            $('#summaryEquipamento').text(equipamento || '-');

            var marcaModelo = $('#marcaModelo').val().trim();
            $('#summaryMarcaModelo').html(marcaModelo ? marcaModelo : '<span class="empty">Não informado</span>');

            var numeroSerie = $('#numeroSerie').val().trim();
            $('#summaryNumeroSerie').html(numeroSerie ? numeroSerie : '<span class="empty">Não informado</span>');

            var descricao = $('#descricaoProblema').val();
            $('#summaryDescricao').html(descricao || '-');

            var quandoComecou = $('#quandoComecou').val().trim();
            $('#summaryQuandoComecou').html(quandoComecou ? quandoComecou : '<span class="empty">Não informado</span>');

            var tentativas = $('#tentativasSolucao').val();
            var textTentativas = tentativas ? tentativas.replace(/<[^>]*>/g, '').trim() : '';
            $('#summaryTentativas').html(textTentativas ? tentativas : '<span class="empty">Não informado</span>');
        }

        function combineFormData() {
            var tipoServico = $('#tipoServico').val();
            var equipamento = $('#equipamento').val().trim();
            var marcaModelo = $('#marcaModelo').val().trim();
            var numeroSerie = $('#numeroSerie').val().trim();
            var descricaoProblema = $('#descricaoProblema').val();
            var quandoComecou = $('#quandoComecou').val().trim();

            // Build combined description
            var descricaoCompleta = '<strong>Tipo de Serviço:</strong> ' + tipoServico + '<br><br>';
            descricaoCompleta += '<strong>Equipamento:</strong> ' + equipamento;

            if (marcaModelo) {
                descricaoCompleta += ' - ' + marcaModelo;
            }
            if (numeroSerie) {
                descricaoCompleta += ' (S/N: ' + numeroSerie + ')';
            }

            descricaoCompleta += '<br><br><strong>Descrição do Problema:</strong><br>' + descricaoProblema;

            if (quandoComecou) {
                descricaoCompleta += '<br><br><strong>Quando começou:</strong> ' + quandoComecou;
            }

            $('#descricaoProdutoHidden').val(descricaoCompleta);

            // Set defeito field
            var tentativas = $('#tentativasSolucao').val();
            if (tentativas) {
                $('#defeitoHidden').val(tentativas);
            }
        }

        // Real-time validation removal on input
        $('#equipamento').on('input', function() {
            if ($(this).val().trim()) {
                $(this).closest('.wizard-form-group').removeClass('error');
            }
        });

        $('#descricaoProblema').on('tbwchange', function() {
            var text = $(this).val().replace(/<[^>]*>/g, '').trim();
            if (text) {
                $(this).closest('.wizard-form-group').removeClass('error');
            }
        });

        // jQuery Validate for final submission (backup)
        $("#formOs").validate({
            rules: {
                descricaoProduto: {
                    required: true
                }
            },
            messages: {
                descricaoProduto: {
                    required: 'O campo descrição da OS é obrigatório.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>
