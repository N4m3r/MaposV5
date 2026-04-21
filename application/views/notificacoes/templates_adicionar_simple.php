<style>
.wiz{display:flex;min-height:500px;border:1px solid #ddd;border-radius:8px;overflow:hidden}
.wiz-nav{width:200px;background:#f5f5f5;border-right:1px solid #ddd;padding:10px}
.wiz-step{padding:10px;margin-bottom:5px;border-radius:4px;cursor:pointer;font-size:13px}
.wiz-step.active{background:#007bff;color:#fff}
.wiz-step.done{background:#28a745;color:#fff}
.wiz-cont{flex:1;padding:20px;overflow-y:auto}
.wiz-panel{display:none}
.wiz-panel.active{display:block}
.wiz-btns{display:flex;justify-content:space-between;padding:15px 20px;border-top:1px solid #ddd;background:#fafafa}
.btn{padding:8px 20px;border:none;border-radius:4px;cursor:pointer;font-size:14px}
.btn-primary{background:#007bff;color:#fff}
.btn-success{background:#28a745;color:#fff}
.btn-secondary{background:#6c757d;color:#fff}
.btn:disabled{opacity:.5;cursor:not-allowed}
.form-group{margin-bottom:15px}
label{display:block;margin-bottom:5px;font-weight:600;font-size:13px}
input,select,textarea{width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:4px;font-size:13px;box-sizing:border-box}
textarea{min-height:120px;resize:vertical}
.req{color:#dc3545}
.help{font-size:11px;color:#666;margin-top:5px}
.ex-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:15px}
.ex-card{border:1px solid #ddd;border-radius:8px;padding:12px;cursor:pointer;transition:all .2s}
.ex-card:hover{border-color:#007bff;transform:translateY(-2px)}
.vars{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.var-chip{background:#e9ecef;border:1px solid #dee2e6;border-radius:20px;padding:4px 10px;font-size:12px;cursor:pointer}
.var-chip:hover{background:#007bff;color:#fff}
.preview-box{background:#dcf8c6;border-radius:8px;padding:10px;margin-top:10px;font-size:13px;min-height:60px}
.preview-box:empty:before{content:"Digite para ver preview";color:#999;font-style:italic}
.toolbar{display:flex;gap:5px;margin-bottom:5px}
.toolbar button{background:#f8f9fa;border:1px solid #ddd;padding:4px 10px;border-radius:4px;cursor:pointer;font-size:14px}
.toolbar button:hover{background:#e9ecef}
h4{margin:0 0 15px 0;font-size:16px;color:#333}
.pg-title{font-size:18px;font-weight:600;margin-bottom:5px}
.pg-desc{color:#666;font-size:13px;margin-bottom:20px}
.options{display:grid;grid-template-columns:1fr 1fr;gap:15px}
.option{border:2px solid #ddd;border-radius:8px;padding:15px;cursor:pointer;display:flex;gap:10px;align-items:flex-start}
.option:hover{border-color:#007bff}
.option input{width:18px;height:18px;margin-top:2px}
.option h5{margin:0;font-size:14px}
.option p{margin:5px 0 0 0;font-size:12px;color:#666}
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin:-20px 0 0">
                <span class="icon"><i class="bx bx-plus-circle"></i></span>
                <h5>Criar Novo Template</h5>
                <div class="buttons">
                    <a href="<?php echo site_url('notificacoesConfig/templates'); ?>" class="btn btn-mini"><i class="bx bx-arrow-back"></i> Voltar</a>
                </div>
            </div>
            <div class="widget-content nopadding">
                <form action="<?php echo current_url(); ?>" id="formTemplate" method="post">
                    <div class="wiz">
                        <div class="wiz-nav">
                            <div class="wiz-step active" data-step="1" onclick="goStep(1)">1. Informações</div>
                            <div class="wiz-step" data-step="2" onclick="goStep(2)">2. Exemplos</div>
                            <div class="wiz-step" data-step="3" onclick="goStep(3)">3. Mensagem</div>
                            <div class="wiz-step" data-step="4" onclick="goStep(4)">4. Variáveis</div>
                            <div class="wiz-step" data-step="5" onclick="goStep(5)">5. Opções</div>
                        </div>
                        <div class="wiz-cont">
                            <!-- Passo 1 -->
                            <div class="wiz-panel active" id="panel-1">
                                <div class="pg-title"><i class="bx bx-info-circle"></i> Informações Básicas</div>
                                <div class="pg-desc">Defina como identificar este template</div>
                                <div class="form-group">
                                    <label>Identificador (Chave) <span class="req">*</span></label>
                                    <input type="text" name="chave" id="chave" required pattern="[a-z0-9_]+" placeholder="Ex: boas_vindas" style="font-family:monospace">
                                    <div class="help">Apenas letras minúsculas, números e underline</div>
                                </div>
                                <div class="form-group">
                                    <label>Nome do Template <span class="req">*</span></label>
                                    <input type="text" name="nome" id="nome" required placeholder="Ex: Mensagem de Boas-vindas">
                                </div>
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <input type="text" name="descricao" id="descricao" placeholder="Quando usar este template">
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
                                    <div class="form-group">
                                        <label>Categoria <span class="req">*</span></label>
                                        <select name="categoria" id="categoria" required onchange="updVars()">
                                            <option value="">Selecione...</option>
                                            <option value="os">📋 Ordens de Serviço</option>
                                            <option value="venda">🛒 Vendas</option>
                                            <option value="cobranca">💰 Cobranças</option>
                                            <option value="marketing">📢 Marketing</option>
                                            <option value="sistema">⚙️ Sistema</option>
                                            <option value="personalizado">✨ Personalizado</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Canal <span class="req">*</span></label>
                                        <select name="canal" id="canal" required onchange="toggleAssunto()">
                                            <option value="whatsapp">📱 WhatsApp</option>
                                            <option value="email">📧 E-mail</option>
                                            <option value="sms">💬 SMS</option>
                                            <option value="todos">🌐 Todos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="campo-assunto" style="display:none">
                                    <label>Assunto do E-mail</label>
                                    <input type="text" name="assunto" id="assunto" placeholder="Assunto do e-mail">
                                </div>
                            </div>

                            <!-- Passo 2 -->
                            <div class="wiz-panel" id="panel-2">
                                <div class="pg-title"><i class="bx bx-collection"></i> Escolha um Exemplo</div>
                                <div class="pg-desc">Selecione um ponto de partida ou crie do zero</div>
                                <div class="ex-cards">
                                    <div class="ex-card" onclick="useEx('')">
                                        <h5>📝 Do Zero</h5>
                                        <p>Crie sua mensagem personalizada</p>
                                    </div>
                                    <div class="ex-card" onclick="useEx('os')">
                                        <h5>📋 OS Criada</h5>
                                        <p>Notificar cliente sobre nova OS</p>
                                    </div>
                                    <div class="ex-card" onclick="useEx('pronta')">
                                        <h5>✅ OS Pronta</h5>
                                        <p>Avisar que serviço foi concluído</p>
                                    </div>
                                    <div class="ex-card" onclick="useEx('venda')">
                                        <h5>🛒 Confirmação Venda</h5>
                                        <p>Confirmar compra do cliente</p>
                                    </div>
                                    <div class="ex-card" onclick="useEx('cobranca')">
                                        <h5>💳 Cobrança</h5>
                                        <p>Enviar link de pagamento</p>
                                    </div>
                                    <div class="ex-card" onclick="useEx('aniv')">
                                        <h5>🎂 Aniversário</h5>
                                        <p>Parabenizar com cupom</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Passo 3 -->
                            <div class="wiz-panel" id="panel-3">
                                <div class="pg-title"><i class="bx bx-message-square-edit"></i> Mensagem</div>
                                <div class="pg-desc">Escreva o conteúdo da notificação</div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                                    <div>
                                        <div class="toolbar">
                                            <button type="button" onclick="insEmoji('👋')">👋</button>
                                            <button type="button" onclick="insEmoji('✅')">✅</button>
                                            <button type="button" onclick="insEmoji('📋')">📋</button>
                                            <button type="button" onclick="insEmoji('💰')">💰</button>
                                            <button type="button" onclick="insEmoji('🎉')">🎉</button>
                                            <button type="button" onclick="insEmoji('⏰')">⏰</button>
                                        </div>
                                        <textarea name="mensagem" id="mensagem" required oninput="updPreview()" placeholder="Olá {cliente_nome}! 👋

Sua OS #{os_id} foi registrada.

📋 Equipamento: {equipamento}

Obrigado! 🤝"></textarea>
                                        <div class="help">Use {variavel} para dados dinâmicos</div>
                                    </div>
                                    <div>
                                        <label>Preview WhatsApp</label>
                                        <div class="preview-box" id="preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Passo 4 -->
                            <div class="wiz-panel" id="panel-4">
                                <div class="pg-title"><i class="bx bx-variable"></i> Variáveis Disponíveis</div>
                                <div class="pg-desc">Clique para inserir na mensagem</div>
                                <h4>Variáveis Globais</h4>
                                <div class="vars">
                                    <span class="var-chip" onclick="insVar('{cliente_nome}')">{cliente_nome}</span>
                                    <span class="var-chip" onclick="insVar('{cliente_telefone}')">{cliente_telefone}</span>
                                    <span class="var-chip" onclick="insVar('{data_atual}')">{data_atual}</span>
                                    <span class="var-chip" onclick="insVar('{hora_atual}')">{hora_atual}</span>
                                    <span class="var-chip" onclick="insVar('{emitente_nome}')">{emitente_nome}</span>
                                    <span class="var-chip" onclick="insVar('{link_sistema}')">{link_sistema}</span>
                                </div>
                                <h4 style="margin-top:20px">Variáveis da Categoria</h4>
                                <div class="vars" id="vars-cat">
                                    <div class="help">Selecione uma categoria no passo 1</div>
                                </div>
                                <h4 style="margin-top:20px">Criar Variáveis Personalizadas</h4>
                                <div id="custom-vars">
                                    <div style="display:flex;gap:10px;margin-bottom:10px">
                                        <input type="text" name="variavel_nome[]" placeholder="Nome" style="flex:1">
                                        <input type="text" name="variavel_desc[]" placeholder="Descrição" style="flex:2">
                                        <button type="button" class="btn btn-secondary" onclick="delVar(this)"><i class="bx bx-trash"></i></button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary" onclick="addVar()" style="font-size:12px"><i class="bx bx-plus"></i> Adicionar</button>
                            </div>

                            <!-- Passo 5 -->
                            <div class="wiz-panel" id="panel-5">
                                <div class="pg-title"><i class="bx bx-cog"></i> Configurações Finais</div>
                                <div class="pg-desc">Defina o comportamento do template</div>
                                <div class="options">
                                    <label class="option" style="cursor:pointer">
                                        <input type="checkbox" name="ativo" value="1" checked style="margin-top:3px">
                                        <div>
                                            <h5>Template Ativo</h5>
                                            <p>Será usado automaticamente pelo sistema</p>
                                        </div>
                                    </label>
                                    <label class="option" style="cursor:pointer">
                                        <input type="checkbox" name="e_marketing" value="1" style="margin-top:3px">
                                        <div>
                                            <h5>É Marketing</h5>
                                            <p>Requer consentimento do cliente (LGPD)</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wiz-btns">
                        <button type="button" class="btn btn-secondary" id="btn-prev" onclick="prevStep()" disabled><i class="bx bx-left-arrow-alt"></i> Anterior</button>
                        <div>
                            <span id="step-num" style="color:#666;margin-right:15px">Passo 1 de 5</span>
                            <button type="button" class="btn btn-primary" id="btn-next" onclick="nextStep()">Próximo <i class="bx bx-right-arrow-alt"></i></button>
                            <button type="submit" class="btn btn-success" id="btn-save" style="display:none"><i class="bx bx-save"></i> Criar Template</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var step=1,total=5;
var varCats={os:['os_id','equipamento','defeito','valor_total','link_consulta'],venda:['venda_id','valor_total','data_venda'],cobranca:['valor','data_vencimento','link_pagamento'],marketing:['cupom_desconto','validade_oferta'],sistema:[],personalizado:[]};
var exMsgs={os:"Olá {cliente_nome}! 👋\n\nSua OS #{os_id} foi registrada.\n\n📋 Equipamento: {equipamento}\n📝 Defeito: {defeito}\n\nAcompanhe: {link_consulta}\n\nObrigado! 🤝",pronta:"Olá {cliente_nome}! 🎉\n\nSua OS #{os_id} está PRONTA! ✅\n\n💰 Valor: R$ {valor_total}\n\nRetire em nossa loja.\n\nObrigado! 🤝",venda:"Olá {cliente_nome}! 🛒\n\nSua compra foi confirmada!\n\n📋 Venda #{venda_id}\n💰 Valor: R$ {valor_total}\n\nObrigado! 💙",cobranca:"Olá {cliente_nome}! 💳\n\nSua cobrança foi gerada:\n\n💰 Valor: R$ {valor}\n📅 Vencimento: {data_vencimento}\n\n💳 Pagar: {link_pagamento}\n\nObrigado! ✅",aniv:"🎂 Feliz Aniversário, {cliente_nome}! 🎉\n\nDesejamos um dia incrível!\n\n🎁 Cupom: {cupom_desconto}\nVálido por 7 dias!\n\nObrigado! 💙"};
function goStep(s){if(s<1||s>total)return;step=s;document.querySelectorAll('.wiz-step').forEach((el,idx)=>{el.classList.remove('active','done');if(idx+1===s)el.classList.add('active');else if(idx+1<s)el.classList.add('done')});document.querySelectorAll('.wiz-panel').forEach((el,idx)=>{el.classList.remove('active');if(idx+1===s)el.classList.add('active')});document.getElementById('btn-prev').disabled=s===1;document.getElementById('btn-next').style.display=s===total?'none':'inline-block';document.getElementById('btn-save').style.display=s===total?'inline-block':'none';document.getElementById('step-num').textContent='Passo '+s+' de '+total;if(s===3)updPreview();}
function nextStep(){if(step<total)goStep(step+1)}
function prevStep(){if(step>1)goStep(step-1)}
function toggleAssunto(){var c=document.getElementById('canal').value;document.getElementById('campo-assunto').style.display=(c==='email'||c==='todos')?'block':'none'}
function useEx(t){if(!t){goStep(3);return}var msg=exMsgs[t]||'';document.getElementById('mensagem').value=msg;if(t==='os'||t==='pronta')document.getElementById('categoria').value='os';else if(t==='venda')document.getElementById('categoria').value='venda';else if(t==='cobranca')document.getElementById('categoria').value='cobranca';else if(t==='aniv')document.getElementById('categoria').value='marketing';updVars();goStep(3)}
function updVars(){var cat=document.getElementById('categoria').value,container=document.getElementById('vars-cat');if(!cat||!varCats[cat]){container.innerHTML='<div class="help">Selecione uma categoria</div>';return}var vs=varCats[cat];if(vs.length===0){container.innerHTML='<div class="help">Sem variáveis específicas</div>';return}container.innerHTML=vs.map(v=>'<span class="var-chip" onclick="insVar(\'{\'+v+\'}\')">{'+v+'}</span>').join('')}
function insVar(v){var ta=document.getElementById('mensagem'),st=ta.selectionStart,en=ta.selectionEnd,txt=ta.value;ta.value=txt.substring(0,st)+v+txt.substring(en);ta.selectionStart=ta.selectionEnd=st+v.length;ta.focus();updPreview()}
function insEmoji(e){var ta=document.getElementById('mensagem'),st=ta.selectionStart,en=ta.selectionEnd,txt=ta.value;ta.value=txt.substring(0,st)+e+txt.substring(en);ta.selectionStart=ta.selectionEnd=st+e.length;ta.focus();updPreview()}
function updPreview(){var msg=document.getElementById('mensagem').value,prev=document.getElementById('preview');if(!msg.trim()){prev.innerHTML='';return}var p=msg;p=p.replace(/{cliente_nome}/g,'João Silva');p=p.replace(/{os_id}/g,'1234');p=p.replace(/{equipamento}/g,'iPhone 12');p=p.replace(/{defeito}/g,'Tela quebrada');p=p.replace(/{valor_total}/g,'850,00');p=p.replace(/{link_consulta}/g,'https://...');p=p.replace(/{venda_id}/g,'567');p=p.replace(/{valor}/g,'299,90');p=p.replace(/{data_vencimento}/g,'25/04/2026');p=p.replace(/{link_pagamento}/g,'https://...');p=p.replace(/{cupom_desconto}/g,'ANIV2026');p=p.replace(/{validade_oferta}/g,'30/04/2026');p=p.replace(/{cliente_telefone}/g,'(11) 99999-9999');p=p.replace(/{data_atual}/g,'<?php echo date('d/m/Y'); ?>');p=p.replace(/{hora_atual}/g,'<?php echo date('H:i'); ?>');p=p.replace(/{emitente_nome}/g,'<?php echo addslashes($this->session->userdata('nome')); ?>');p=p.replace(/{link_sistema}/g,'<?php echo base_url(); ?>');prev.innerHTML=p.replace(/\n/g,'<br>')}
function addVar(){var c=document.getElementById('custom-vars'),r=document.createElement('div');r.style='display:flex;gap:10px;margin-bottom:10px';r.innerHTML='<input type="text" name="variavel_nome[]" placeholder="Nome" style="flex:1"><input type="text" name="variavel_desc[]" placeholder="Descrição" style="flex:2"><button type="button" class="btn btn-secondary" onclick="delVar(this)"><i class="bx bx-trash"></i></button>';c.appendChild(r)}
function delVar(b){var r=document.querySelectorAll('#custom-vars > div');if(r.length>1)b.parentElement.remove();else{b.parentElement.querySelectorAll('input').forEach(i=>i.value='')}}
document.getElementById('formTemplate').addEventListener('submit',function(e){var ch=document.getElementById('chave').value,msg=document.getElementById('mensagem').value;if(!/^[a-z0-9_]+$/.test(ch)){e.preventDefault();alert('Chave: apenas letras minúsculas, números e underline');goStep(1);return false}if(!msg.trim()){e.preventDefault();alert('Digite uma mensagem');goStep(3);return false}});
$(document).ready(function(){updVars();updPreview();toggleAssunto()});
</script>
