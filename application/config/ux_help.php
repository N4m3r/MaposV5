<?php
/**
 * Conteudo da ajuda contextual (Fase 2.6 do Plano UX).
 *
 * Cada entrada em $config['ux_help']['telas'] e uma pagina de ajuda.
 * Campos:
 *   - titulo:    titulo da pagina
 *   - resumo:    resumo curto (1-2 frases) mostrado no hub
 *   - icone:     classe CSS do boxicon (ex: bx-file)
 *   - categoria: 'financeiro' | 'cadastros' | 'os' | 'configuracoes' | 'geral'
 *   - url:       URL da tela no sistema (para botao "Ir para a tela")
 *   - secoes:    array de secoes {titulo, conteudo (HTML)}
 *
 * Adicionar/editar livremente - nao requer deploy.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$config['ux_help'] = [
    'telas' => [

        'dashboard' => [
            'titulo'    => 'Dashboard',
            'resumo'    => 'Visao geral com KPIs, atalhos e graficos do seu negocio.',
            'icone'     => 'bxs-dashboard',
            'categoria' => 'geral',
            'url'       => 'dashboard',
            'secoes'    => [
                [
                    'titulo'   => 'O que sao os KPIs?',
                    'conteudo' => '<p>Os <strong>KPIs</strong> (Key Performance Indicators) sao indicadores que resumem a saude do seu negocio em tempo real:</p>
<ul>
  <li><strong>OS em Aberto:</strong> Quantas ordens de servico estao aguardando conclusao.</li>
  <li><strong>Faturamento do Mes:</strong> Soma de todas as OS finalizadas/pagas no mes corrente.</li>
  <li><strong>Clientes Ativos:</strong> Quantidade de clientes que tiveram movimento nos ultimos 90 dias.</li>
  <li><strong>Atrasadas:</strong> OS com data de entrega prevista ja ultrapassada.</li>
</ul>',
                ],
                [
                    'titulo'   => 'Como filtrar o periodo?',
                    'conteudo' => '<p>Use o seletor <strong>"Periodo"</strong> no topo do dashboard para escolher Hoje, Esta Semana, Este Mes ou Este Ano. Para periodos personalizados, escolha "Customizado" e preencha as datas.</p>',
                ],
            ],
        ],

        'os_adicionar' => [
            'titulo'    => 'Criando uma Ordem de Servico',
            'resumo'    => 'Passo a passo para lancar uma OS completa (cliente, servico, produtos, valores).',
            'icone'     => 'bx-file',
            'categoria' => 'os',
            'url'       => 'os/adicionar',
            'secoes'    => [
                [
                    'titulo'   => '1. Selecione o cliente',
                    'conteudo' => '<p>Comece digitando o nome do cliente no campo "Cliente". O sistema sugere automaticamente os cadastros existentes. Se o cliente nao existir, clique em <strong>"Cadastrar novo cliente"</strong>.</p>',
                ],
                [
                    'titulo'   => '2. Defina o tipo de servico',
                    'conteudo' => '<p>O <strong>tipo de servico</strong> (Instalacao, Manutencao, Garantia, etc.) define o modelo de OS usado e pode pre-preencher valores padrao.</p>',
                ],
                [
                    'titulo'   => '3. Adicione produtos ou servicos',
                    'conteudo' => '<p>Use o campo "Produto/Servico" para buscar. A quantidade multiplica pelo preco unitario. O valor total da OS e calculado automaticamente.</p>
<p class="alert alert-warning"><i class="bx bx-error"></i> <strong>Atencao:</strong> o estoque so e baixado quando a OS e marcada como "Concluida".</p>',
                ],
                [
                    'titulo'   => '4. Salvar vs Salvar e Emitir',
                    'conteudo' => '<p><strong>Salvar:</strong> Apenas grava a OS, que pode ser editada depois.</p>
<p><strong>Salvar e Emitir:</strong> Grava e ja gera o boleto/NFS-e (se configurado).</p>',
                ],
            ],
        ],

        'cliente_adicionar' => [
            'titulo'    => 'Cadastrando um cliente',
            'resumo'    => 'Como adicionar pessoa fisica ou juridica com preenchimento automatico de endereco.',
            'icone'     => 'bx-user',
            'categoria' => 'cadastros',
            'url'       => 'clientes/adicionar',
            'secoes'    => [
                [
                    'titulo'   => 'Pessoa fisica ou juridica?',
                    'conteudo' => '<p>Use o seletor no topo do formulario. Isso altera os campos exibidos (CPF/CNPJ, IE, etc.) e as validacoes aplicadas.</p>',
                ],
                [
                    'titulo'   => 'Preenchimento automatico do endereco',
                    'conteudo' => '<p>Digite o CEP e pressione <kbd>Tab</kbd>. O sistema consulta o ViaCEP e preenche logradouro, bairro, cidade e UF. Voce pode ajustar manualmente se necessario.</p>',
                ],
                [
                    'titulo'   => 'Campos obrigatorios',
                    'conteudo' => '<p>Para salvar um cliente, preencha pelo menos:</p>
<ul>
  <li>Nome / Razao Social</li>
  <li>CPF ou CNPJ (valido)</li>
  <li>Telefone</li>
</ul>
<p>O e-mail e obrigatorio apenas se voce quiser enviar boletos e NFS-e.</p>',
                ],
            ],
        ],

        'financeiro_lancamento' => [
            'titulo'    => 'Lancamento financeiro',
            'resumo'    => 'Como lancar contas a pagar e a receber.',
            'icone'     => 'bx-wallet',
            'categoria' => 'financeiro',
            'url'       => 'financeiro/lancamentos/adicionar',
            'secoes'    => [
                [
                    'titulo'   => 'Receita x Despesa',
                    'conteudo' => '<p><strong>Receita:</strong> valores que entram (boleto recebido, venda a vista).</p>
<p><strong>Despesa:</strong> valores que saem (conta de luz, pagamento de fornecedor).</p>
<p>A classificacao correta e essencial para o calculo do DRE e indicadores financeiros.</p>',
                ],
                [
                    'titulo'   => 'Status do lancamento',
                    'conteudo' => '<ul>
  <li><strong>Pendente:</strong> cadastrado mas ainda nao pago/recebido.</li>
  <li><strong>Pago:</strong> confirmado. Data do pagamento preenchida.</li>
  <li><strong>Atrasado:</strong> passou da data de vencimento.</li>
  <li><strong>Cancelado:</strong> lancamento desfeito (estorno, erro).</li>
</ul>',
                ],
                [
                    'titulo'   => 'Juros e multa',
                    'conteudo' => '<p>O sistema calcula automaticamente juros e multa para lancamentos atrasados, com base nas configuracoes financeiras (geralmente 2% de multa + 1% de juros ao mes).</p>',
                ],
            ],
        ],

        'nfe_emissao' => [
            'titulo'    => 'Emitindo uma NFS-e',
            'resumo'    => 'Como emitir nota fiscal de servico eletronica pelo sistema.',
            'icone'     => 'bx-receipt',
            'categoria' => 'financeiro',
            'url'       => 'nfe',
            'secoes'    => [
                [
                    'titulo'   => 'Pre-requisitos',
                    'conteudo' => '<ul>
  <li>Certificado digital A1 configurado (menu Configuracoes > Certificado)</li>
  <li>Dados do emitente preenchidos (CNPJ, razao social, endereco)</li>
  <li>Prefeitura da sua cidade homologada no sistema</li>
</ul>',
                ],
                [
                    'titulo'   => 'Passo a passo',
                    'conteudo' => '<ol>
  <li>Acesse o menu <strong>Fiscal > NFS-e</strong>.</li>
  <li>Selecione a OS ou lancamento que deseja emitir.</li>
  <li>Preencha os dados do servico (codigo do servico municipal, aliquota ISS).</li>
  <li>Clique em "Emitir NFS-e".</li>
  <li>Aguarde a resposta da prefeitura (pode levar ate 30s).</li>
  <li>O PDF/XML ficam disponiveis para download na aba "Documentos".</li>
</ol>',
                ],
            ],
        ],

        'cobranca_boleto' => [
            'titulo'    => 'Gerando boleto bancario',
            'resumo'    => 'Como gerar boletos pelo sistema integrado com seu banco.',
            'icone'     => 'bx-barcode',
            'categoria' => 'financeiro',
            'url'       => 'cobrancas',
            'secoes'    => [
                [
                    'titulo'   => 'Configuracao inicial',
                    'conteudo' => '<p>Antes de gerar o primeiro boleto, configure a conta bancaria que sera usada:</p>
<ol>
  <li>Acesse <strong>Configuracoes > Conta Bancaria</strong>.</li>
  <li>Preencha banco, agencia, conta e variacao.</li>
  <li>Cadastre o convenio com o banco (cedente).</li>
  <li>Faca um teste com valor minimo (R$ 1,00) para validar.</li>
</ol>',
                ],
                [
                    'titulo'   => 'Gerando o boleto',
                    'conteudo' => '<p>Na OS ou no lancamento financeiro, clique no botao <strong>"Gerar boleto"</strong>. O PDF aparecera em ate 5 segundos. O boleto e enviado automaticamente por e-mail se o cliente tiver e-mail cadastrado.</p>',
                ],
            ],
        ],
    ],
];
