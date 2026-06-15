<?php
/**
 * Definições dos tours guiados do sistema.
 * Adicionado em 2026-06-14 (Fase 2.1.3 do Plano UX).
 *
 * Cada tour é identificado por uma chave (tour_key) e contém:
 *   - titulo:      título mostrado no header do tour
 *   - rota:        URI/rota em que o tour fica disponível (vazio = global)
 *   - auto_start:  true = inicia automaticamente ao carregar a página (se pendente)
 *   - steps:       array de passos, cada um com:
 *       - seletor:    seletor CSS do elemento alvo
 *       - titulo:     título do popover
 *       - descricao:  corpo do popover
 *       - posicao:    'top' | 'bottom' | 'left' | 'right' (padrão: 'bottom')
 *       - ao_avancar: JS callback opcional (string) executado antes de avançar
 *
 * Para criar um novo tour:
 *   1) Adicionar entrada no array $config['ux_tours']
 *   2) Criar a chave única como tour_key
 *   3) Listar os seletores CSS dos elementos-alvo na página
 *
 * O backend (Ux_tour_model) registra a conclusão/pulo por usuário.
 * O frontend (assets/js/ux/tour-runner.js) consome esta config via JSON.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$config['ux_tours'] = [

    // ====================================================================
    // TOUR 1: Dashboard inicial (tela de boas-vindas)
    // ====================================================================
    'dashboard_inicial' => [
        'titulo'     => 'Bem-vindo ao MaposV5!',
        'rota'       => 'dashboard',
        'auto_start' => true,
        'steps'      => [
            [
                'seletor'   => '#tour-kpis',
                'titulo'    => '📊 Seus indicadores',
                'descricao' => 'Aqui você acompanha OS em aberto, faturamento do mês, clientes ativos e muito mais em um piscar de olhos.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => '#tour-menu-lateral',
                'titulo'    => '🗂️ Menu principal',
                'descricao' => 'Todas as áreas do sistema estão aqui: Ordens de Serviço, Clientes, Produtos, Financeiro, Configurações...',
                'posicao'   => 'right',
            ],
            [
                'seletor'   => '#tour-busca-global',
                'titulo'    => '🔍 Busca rápida',
                'descricao' => 'Aperte Ctrl+K (ou Cmd+K no Mac) a qualquer momento para buscar clientes, OS, produtos e serviços sem sair da tela.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => '#tour-atalhos',
                'titulo'    => '⌨️ Atalhos de teclado',
                'descricao' => 'F1 abre Clientes, F2 abre OS, F3 abre Produtos... Use o banner de atalhos para descobrir todos.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => '#tour-primeiros-passos',
                'titulo'    => '✅ Primeiros passos',
                'descricao' => 'Se você está começando agora, siga o checklist "Primeiros Passos" à direita. Em 5 minutos você já vai ter criado um cliente e uma OS.',
                'posicao'   => 'left',
            ],
            [
                'seletor'   => '#tour-nova-os',
                'titulo'    => '➕ Criar nova OS',
                'descricao' => 'Pronto para começar? Clique aqui para abrir uma Ordem de Serviço, ou aperte F2.',
                'posicao'   => 'left',
            ],
        ],
    ],

    // ====================================================================
    // TOUR 2: Como criar uma OS básica
    // ====================================================================
    'os_basico' => [
        'titulo'     => 'Criando uma Ordem de Serviço',
        'rota'       => 'os/adicionar',
        'auto_start' => false,
        'steps'      => [
            [
                'seletor'   => '#clienteSelect',
                'titulo'    => '1. Selecione o cliente',
                'descricao' => 'Comece digitando o nome do cliente. O sistema sugere conforme você digita.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'select[name="idTipo"]',
                'titulo'    => '2. Tipo de serviço',
                'descricao' => 'Escolha o tipo (Instalação, Manutenção, etc). Isso define o modelo de OS usado.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'textarea[name="descricaoProduto"]',
                'titulo'    => '3. Descreva o serviço',
                'descricao' => 'Detalhe o que será feito. Você pode usar modelos prontos no botão ao lado.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => '#tour-produtos',
                'titulo'    => '4. Adicione produtos/serviços',
                'descricao' => 'Busque produtos para incluir na OS. O valor total é calculado automaticamente.',
                'posicao'   => 'top',
            ],
            [
                'seletor'   => 'button[type="submit"]',
                'titulo'    => '5. Salvar',
                'descricao' => 'Tudo certo? Clique aqui para salvar a OS. Depois você pode emitir boleto, NFS-e ou apenas acompanhar o status.',
                'posicao'   => 'top',
            ],
        ],
    ],

    // ====================================================================
    // TOUR 3: Lançamento financeiro
    // ====================================================================
    'financeiro_lancamento' => [
        'titulo'     => 'Lançando contas a pagar/receber',
        'rota'       => 'financeiro/lancamentos',
        'auto_start' => false,
        'steps'      => [
            [
                'seletor'   => 'select[name="tipo"]',
                'titulo'    => '💰 Receita ou despesa?',
                'descricao' => 'Escolha "Receita" para valores que entram (boleto recebido, venda) ou "Despesa" para valores que saem (conta de luz, fornecedor).',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'input[name="valor"]',
                'titulo'    => '💵 Valor',
                'descricao' => 'Use ponto para separar centavos (ex: 150.50). O sistema calcula juros e multas automaticamente se houver atraso.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'input[name="vencimento"]',
                'titulo'    => '📅 Vencimento',
                'descricao' => 'Data em que a conta vence. Após essa data, o sistema marca como "em atraso" e aplica regras de juros.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'select[name="cliente_id"]',
                'titulo'    => '👤 Vincular cliente (opcional)',
                'descricao' => 'Se essa conta está ligada a um cliente, vincule aqui. Assim ela aparece no extrato do cliente também.',
                'posicao'   => 'bottom',
            ],
        ],
    ],

    // ====================================================================
    // TOUR 4: Adicionar cliente
    // ====================================================================
    'cliente_adicionar' => [
        'titulo'     => 'Cadastrando um cliente',
        'rota'       => 'clientes/adicionar',
        'auto_start' => false,
        'steps'      => [
            [
                'seletor'   => 'input[name="nomeCliente"]',
                'titulo'    => '1. Nome / Razão Social',
                'descricao' => 'Preencha com o nome completo ou razão social. Para pessoa jurídica, o CNPJ aparece no campo abaixo.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'input[name="documento"]',
                'titulo'    => '2. CPF ou CNPJ',
                'descricao' => 'O sistema detecta automaticamente se é CPF (11 dígitos) ou CNPJ (14) e aplica a máscara certa.',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'input[name="cep"]',
                'titulo'    => '3. CEP (preenchimento automático)',
                'descricao' => 'Digite o CEP e pressione Tab. O endereço é preenchido automaticamente (integração ViaCEP).',
                'posicao'   => 'bottom',
            ],
            [
                'seletor'   => 'input[name="email"]',
                'titulo'    => '4. E-mail',
                'descricao' => 'Obrigatório se você quiser enviar boletos e NFS-e automaticamente. O sistema valida o formato.',
                'posicao'   => 'bottom',
            ],
        ],
    ],
];
