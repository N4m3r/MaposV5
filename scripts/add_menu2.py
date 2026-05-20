# -*- coding: utf-8 -*-
import json

with open('/tmp/wf01_final.json', 'r', encoding='utf-8') as f:
    wf = json.load(f)[0]

nodes = wf['nodes']

menu_text = 'Ola! Bem-vindo ao atendimento automatico da JJ Ferreiras.\\n\\nEscolha uma opcao:\\n1- Consultar status da minha OS\\n2- Verificar dividas/cobrancas\\n3- Criar nova ordem de servico\\n4- Aprovar orcamento\\n\\nDigite o numero ou descreva o que voce precisa.'

for n in nodes:
    if n['name'] == 'Analisar Nivel':
        new = 'const resp = $input.first().json;\nconst texto = ($("Extrair Dados").item.json.texto || "").toLowerCase().trim();\nconst saudacoes = ["oi", "ola", "bom dia", "boa tarde", "boa noite", "hey", "hi", "hello"];\nconst isSaudacao = saudacoes.some(s => texto.startsWith(s));\nconst nivel = resp.data?.nivel_acesso || 1;\nconst precisa = !isSaudacao && nivel >= 4 && !resp.data?.token;\nreturn [{json: {...resp, precisa_solicitar: precisa, is_saudacao: isSaudacao, telefone: $("Extrair Dados").item.json.telefone, texto: $("Extrair Dados").item.json.texto}}];'
        n['parameters']['functionCode'] = new
        print('Fixed Analisar Nivel')

    if n['name'] == 'Formatar Resposta':
        new = 'const r = $input.first().json;\nlet msg = "";\nif (r.is_saudacao || $("Analisar Nivel").item.json.is_saudacao) {\n  msg = "' + menu_text + '";\n} else {\n  msg = r.message || r.data?.mensagem || "Acao concluida!";\n  if (r.data?.pdf_url) msg += " PDF: " + r.data.pdf_url;\n  if (r.data?.os_id) msg += " OS #:" + r.data.os_id;\n}\nreturn [{json: {code: 200, body: msg, headers: {}}}];'
        n['parameters']['functionCode'] = new
        print('Fixed Formatar Resposta')

    if n['name'] == 'Solicitar Token':
        new = 'const isSaud = $("Analisar Nivel").item.json.is_saudacao;\nif (isSaud) {\n  return [{json: {code: 200, body: "' + menu_text + '", headers: {}}}];\n}\nreturn [{json: {code: 200, body: "Esta acao requer autorizacao do administrador. Responda com CONFIRMAR se deseja prosseguir.", headers: {}}}];'
        n['parameters']['functionCode'] = new
        print('Fixed Solicitar Token')

with open('/tmp/wf01_menu2.json', 'w', encoding='utf-8') as f:
    json.dump([wf], f, ensure_ascii=False)

print('Done')
