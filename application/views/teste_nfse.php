<?php
/**
 * View: Diagnóstico Completo do Módulo NFSe
 */

function iconOk() { return '<i class="fas fa-check-circle" style="color:#28a745"></i>'; }
function iconWarn() { return '<i class="fas fa-exclamation-triangle" style="color:#ffc107"></i>'; }
function iconErr() { return '<i class="fas fa-times-circle" style="color:#dc3545"></i>'; }

function row($label, $status, $message = '') {
    $color = $status === 'ok' ? '#28a745' : ($status === 'warn' ? '#ffc107' : '#dc3545');
    $icon = $status === 'ok' ? iconOk() : ($status === 'warn' ? iconWarn() : iconErr());
    echo '<tr><td style="border-color:var(--dark-2,#272835); padding:6px 8px"><strong>' . htmlspecialchars($label) . '</strong></td>';
    echo '<td style="border-color:var(--dark-2,#272835); padding:6px 8px; color:' . $color . '; font-weight:bold">' . $icon . ' ' . strtoupper($status) . '</td>';
    echo '<td style="border-color:var(--dark-2,#272835); padding:6px 8px">' . $message . '</td></tr>';
}

// --- PHP Extensions ---
$exts = [
    'curl' => 'Comunicacao HTTP com API Nacional',
    'openssl' => 'Assinatura digital de XML',
    'dom' => 'Geracao de XML DPS',
    'json' => 'Comunicacao com API REST',
    'mbstring' => 'Manipulacao de strings UTF-8',
    'libxml' => 'Validacao de XML',
    'zlib' => 'Compressao GZip do XML',
];
$extResults = [];
foreach ($exts as $ext => $desc) {
    $extResults[$ext] = extension_loaded($ext);
}

// --- Tabelas ---
$tables = [
    'os_nfse_emitida' => 'Tabela principal de NFS-e',
    'os_boleto_emitido' => 'Tabela de boletos vinculados a OS',
    'impostos_retidos' => 'Retencoes de impostos / DRE',
    'config_sistema_impostos' => 'Configuracoes de impostos',
    'configuracoes_impostos' => 'Aliquotas do Simples Nacional',
    'certificado_digital' => 'Certificado digital',
    'clientes' => 'Clientes (tomador)',
    'emitente' => 'Dados do emitente (prestador)',
    'os' => 'Ordens de servico',
];
$tableResults = [];
foreach ($tables as $table => $desc) {
    $tableResults[$table] = $this->db->table_exists($table);
}

// --- Colunas ---
$columns = [
    ['emitente', 'inscricao_municipal', 'Inscricao Municipal do prestador'],
    ['emitente', 'inscricao_estadual', 'Inscricao Estadual do prestador'],
    ['clientes', 'inscricao_municipal', 'Inscricao Municipal do tomador'],
    ['clientes', 'inscricao_estadual', 'Inscricao Estadual do tomador'],
    ['clientes', 'documento', 'Documento CPF/CNPJ do tomador'],
    ['os_nfse_emitida', 'ambiente', 'Ambiente (homologacao/producao)'],
    ['os_nfse_emitida', 'xml_dps', 'XML DPS armazenado'],
    ['os_nfse_emitida', 'xml_nfse', 'XML NFSe armazenado'],
    ['os_nfse_emitida', 'chave_acesso', 'Chave de acesso API Nacional'],
    ['os_boleto_emitido', 'nfse_id', 'Vinculo boleto->NFSe'],
];
$colResults = [];
foreach ($columns as $col) {
    $colResults[] = [
        'table' => $col[0],
        'column' => $col[1],
        'desc' => $col[2],
        'exists' => $this->db->table_exists($col[0]) && $this->db->field_exists($col[1], $col[0]),
    ];
}

// --- Configuracoes de impostos ---
$impConfig = [];
if (method_exists($this->impostos_model, 'getConfig')) {
    $impConfig = [
        'IMPOSTO_ANEXO_PADRAO' => $this->impostos_model->getConfig('IMPOSTO_ANEXO_PADRAO') ?: 'nao configurado',
        'IMPOSTO_FAIXA_ATUAL' => $this->impostos_model->getConfig('IMPOSTO_FAIXA_ATUAL') ?: 'nao configurado',
        'IMPOSTO_ISS_MUNICIPAL' => $this->impostos_model->getConfig('IMPOSTO_ISS_MUNICIPAL') ?: 'nao configurado',
        'IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_NACIONAL') ?: 'nao configurado',
        'IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL' => $this->impostos_model->getConfig('IMPOSTO_CODIGO_TRIBUTACAO_MUNICIPAL') ?: 'nao configurado',
        'IMPOSTO_DESCRICAO_SERVICO' => $this->impostos_model->getConfig('IMPOSTO_DESCRICAO_SERVICO') ?: 'nao configurado',
    ];
}

// --- Emitente ---
$emitente = $this->mapos_model->getEmitente();
$emitOk = $emitente && !empty($emitente->cnpj);

// --- Certificado ---
$certificado = null;
$certOk = false;
$certMsg = 'Modelo de certificado nao encontrado';
$certArquivoOk = false;
$certArquivoMsg = '';
if ($this->load->model('certificado_model')) {
    $certificado = $this->certificado_model->getCertificadoAtivo();
    if ($certificado) {
        $certOk = true;
        $certMsg = 'Ambiente: ' . ($certificado->ambiente ?? 'nao definido') . ' | Validade: ' . ($certificado->data_validade ?? '---');

        // Verificar arquivo fisico
        $arquivoPath = $certificado->arquivo_caminho ?? '';
        if (empty($arquivoPath)) {
            $certArquivoMsg = 'Caminho do arquivo .pfx nao preenchido no cadastro.';
        } elseif (!file_exists($arquivoPath)) {
            $certArquivoMsg = 'Arquivo .pfx nao encontrado no servidor: ' . $arquivoPath;
        } else {
            $certArquivoOk = true;
            $certArquivoMsg = 'Arquivo .pfx encontrado (' . round(filesize($arquivoPath) / 1024, 1) . ' KB)';
        }
    } else {
        $certMsg = 'Nenhum certificado ativo encontrado.';
    }
}

// --- Teste de calculo de impostos ---
$calcTest = null;
$calcMsg = '';
try {
    $calcTest = $this->impostos_model->calcularImpostos(1000.00);
    if ($calcTest === false) {
        $calcMsg = 'calcularImpostos() retornou false — configuracao nao encontrada.';
    } elseif (!is_array($calcTest)) {
        $calcMsg = 'Retornou tipo inesperado: ' . gettype($calcTest);
    } else {
        $calcMsg = 'Aliquota nominal: ' . ($calcTest['aliquota_nominal'] ?? '---') . '% | ISS: R$ ' . ($calcTest['iss'] ?? 0) . ' | Total: R$ ' . ($calcTest['valor_total_impostos'] ?? 0);
    }
} catch (Exception $e) {
    $calcMsg = 'Erro: ' . $e->getMessage();
}

// --- Teste de DPS XML ---
$dpsTest = null;
$dpsMsg = '';
if (file_exists(APPPATH . 'libraries/Nfse/DpsXmlBuilder.php')) {
    try {
        $this->load->library('Nfse/DpsXmlBuilder');
        $dpsBuilder = new DpsXmlBuilder([
            'codigo_municipio' => '1302603',
            'codigo_uf' => '13',
        ]);
        $testXml = $dpsBuilder->gerarDps([
            'prestador' => [
                'cnpj' => '12345678000195',
                'razao_social' => 'Teste Prestador',
                'im' => '123456',
                'ie' => '123456789',
                'cnae' => '010701',
                'email' => 'teste@teste.com',
                'telefone' => '92999999999',
                'endereco' => ['logradouro' => 'Rua Teste', 'numero' => '123', 'bairro' => 'Centro', 'codigo_municipio' => '1302603', 'uf' => 'AM', 'cep' => '69000000'],
            ],
            'tomador' => [
                'cpf_cnpj' => '12345678901',
                'razao_social' => 'Teste Tomador',
                'im' => '',
                'ie' => '',
                'email' => '',
                'telefone' => '',
                'endereco' => ['logradouro' => 'Rua Teste', 'numero' => '456', 'bairro' => 'Centro', 'codigo_municipio' => '1302603', 'uf' => 'AM', 'cep' => '69000000'],
            ],
            'servico' => [
                'descricao' => 'Servicos de teste',
                'cnae' => '010701',
                'codigo_tributacao_nacional' => '010701',
                'codigo_tributacao_municipal' => '100',
                'valor_servicos' => 1000.00,
                'valor_deducoes' => 0,
                'valor_iss' => 0,
                'aliquota_iss' => 5.00,
                'valor_pis' => 0,
                'valor_cofins' => 0,
                'valor_irrf' => 0,
                'valor_csll' => 0,
                'valor_inss' => 0,
                'valor_liquido' => 1000.00,
                'iss_retido' => false,
                'pis_retido' => false,
                'cofins_retido' => false,
                'irrf_retido' => false,
                'csll_retido' => false,
                'inss_retido' => false,
            ],
            'tributacao' => [
                'natureza_operacao' => '1',
                'optante_simples' => true,
                'regime_especial' => '0',
                'incentivador_cultural' => '0',
                'aliquota_iss' => 5.00,
            ],
            'competencia' => date('Y-m-d'),
        ]);
        $dpsTest = $testXml ? true : false;
        $dpsMsg = $testXml ? 'XML DPS gerado com sucesso (' . strlen($testXml) . ' bytes)' : 'XML DPS retornou vazio';
    } catch (Exception $e) {
        $dpsTest = false;
        $dpsMsg = 'Erro: ' . $e->getMessage();
    }
} else {
    $dpsMsg = 'Arquivo DpsXmlBuilder.php nao encontrado';
}

// --- Teste de normalizacao de valor ---
$normTest = null;
$normMsg = '';
$normFn = function($valor) {
    if (empty($valor) || is_numeric($valor)) { return floatval($valor); }
    $valor = trim($valor);
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
    }
    return floatval($valor);
};
$norm1 = $normFn('1.234,56');
$norm2 = $normFn('1234.56');
$norm3 = $normFn('');
$normTest = ($norm1 == 1234.56 && $norm2 == 1234.56 && $norm3 == 0);
$normMsg = "'1.234,56' => {$norm1} | '1234.56' => {$norm2} | '' => {$norm3}";

// --- Permissoes de diretorio ---
$dirResults = [];
$dirs = [
    FCPATH . 'assets/certs' => 'Certificados digitais',
    FCPATH . 'assets/uploads' => 'Uploads (logos)',
    APPPATH . 'logs' => 'Logs do sistema',
    FCPATH . 'assets/certs/ac-icp-brasil.pem' => 'CA ICP-Brasil (opcional)',
];
foreach ($dirs as $path => $desc) {
    $dirResults[] = [
        'path' => $path,
        'desc' => $desc,
        'exists' => file_exists($path),
        'writable' => is_dir($path) ? is_writable($path) : (file_exists($path) ? is_writable(dirname($path)) : is_writable(dirname($path))),
    ];
}

$ambienteSistema = $certificado->ambiente ?? 'homologacao';

// --- Contadores ---
$okCount = 0; $warnCount = 0; $errCount = 0;
foreach ($extResults as $v) { $v ? $okCount++ : $errCount++; }
foreach ($tableResults as $v) { $v ? $okCount++ : $errCount++; }
foreach ($colResults as $c) { $c['exists'] ? $okCount++ : $errCount++; }
$emitOk ? $okCount++ : $errCount++;
$certOk ? $okCount++ : $warnCount++;
$certArquivoOk ? $okCount++ : ($certOk ? $errCount++ : 0);
($calcTest !== false && is_array($calcTest)) ? $okCount++ : $errCount++;
$dpsTest ? $okCount++ : $errCount++;
$normTest ? $okCount++ : $errCount++;
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)">
            <div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)">
                <span class="icon"><i class="fas fa-stethoscope" style="color:#1086dd"></i></span>
                <h5 style="color:var(--title,#d4d8e0)">Diagnostico NFSe &mdash; Ambiente: <span style="color:#1086dd"><?php echo ucfirst($ambienteSistema); ?></span></h5>
                <div style="float:right; margin:8px 10px 0 0">
                    <a href="<?php echo site_url('nfse_os'); ?>" class="btn btn-mini" style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button onclick="location.reload()" class="btn btn-mini" style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)">
                        <i class="fas fa-sync-alt"></i> Recarregar
                    </button>
                </div>
            </div>
            <div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)">

                <!-- Resumo -->
                <div class="alert" style="background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd; margin-bottom:20px">
                    <i class="fas fa-clipboard-list"></i> <strong>Resumo:</strong>
                    <span style="color:#28a745; font-weight:bold"><?php echo $okCount; ?> OK</span> |
                    <span style="color:#ffc107; font-weight:bold"><?php echo $warnCount; ?> Alertas</span> |
                    <span style="color:#dc3545; font-weight:bold"><?php echo $errCount; ?> Erros</span>
                    <?php if ($errCount > 0): ?>
                        <br><strong style="color:#dc3545"><i class="fas fa-exclamation-triangle"></i> Corrija os erros antes de emitir NFSe.</strong>
                    <?php elseif ($warnCount > 0): ?>
                        <br><strong style="color:#ffc107"><i class="fas fa-info-circle"></i> Ambiente funcional, mas com alertas.</strong>
                    <?php else: ?>
                        <br><strong style="color:#28a745"><i class="fas fa-check-circle"></i> Ambiente validado com sucesso!</strong>
                    <?php endif; ?>
                </div>

                <!-- PHP Extensions -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-microchip" style="color:#1086dd"></i> Extensoes PHP</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Extensao</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Descricao</th>
                    </tr></thead><tbody>
                    <?php foreach ($extResults as $ext => $loaded): ?>
                        <?php row($ext, $loaded ? 'ok' : 'erro', $exts[$ext]); ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Tabelas -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-database" style="color:#1086dd"></i> Tabelas do Banco</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Tabela</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Descricao</th>
                    </tr></thead><tbody>
                    <?php foreach ($tableResults as $table => $exists): ?>
                        <?php row($table, $exists ? 'ok' : 'erro', $tables[$table] . ($exists ? '' : ' &mdash; <strong style="color:#dc3545">Execute as migrations!</strong>')); ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Colunas -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-columns" style="color:#1086dd"></i> Colunas do Banco</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Coluna</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Descricao</th>
                    </tr></thead><tbody>
                    <?php foreach ($colResults as $col): ?>
                        <?php row($col['table'] . '.' . $col['column'], $col['exists'] ? 'ok' : 'erro', $col['desc'] . ($col['exists'] ? '' : ' &mdash; <strong style="color:#dc3545">Execute a migration!</strong>')); ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Configuracoes Impostos -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-sliders-h" style="color:#1086dd"></i> Configuracoes de Impostos</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Configuracao</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Valor</th>
                    </tr></thead><tbody>
                    <?php foreach ($impConfig as $key => $val): ?>
                        <?php
                        $isSet = $val !== 'nao configurado';
                        row($key, $isSet ? 'ok' : 'warn', $val);
                        ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Emitente -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-building" style="color:#1086dd"></i> Dados do Emitente (Prestador)</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Campo</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Valor</th>
                    </tr></thead><tbody>
                    <?php
                    row('Nome/Razao Social', !empty($emitente->nome) ? 'ok' : 'erro', htmlspecialchars($emitente->nome ?? 'Nao configurado'));
                    row('CNPJ', !empty($emitente->cnpj) ? 'ok' : 'erro', htmlspecialchars($emitente->cnpj ?? 'Nao configurado'));
                    row('Inscricao Municipal', !empty($emitente->inscricao_municipal) ? 'ok' : 'warn', htmlspecialchars($emitente->inscricao_municipal ?? 'Nao configurado (opcional para Manaus)'));
                    row('Inscricao Estadual', !empty($emitente->inscricao_estadual) ? 'ok' : 'warn', htmlspecialchars($emitente->inscricao_estadual ?? 'Nao configurado'));
                    row('Endereco', (!empty($emitente->rua) && !empty($emitente->cidade)) ? 'ok' : 'warn',
                        htmlspecialchars(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' — ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '')));
                    row('Email', !empty($emitente->email) ? 'ok' : 'warn', htmlspecialchars($emitente->email ?? 'Nao configurado'));
                    row('Telefone', !empty($emitente->telefone) ? 'ok' : 'warn', htmlspecialchars($emitente->telefone ?? 'Nao configurado'));
                    ?>
                    </tbody>
                </table>

                <!-- Certificado -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-certificate" style="color:#1086dd"></i> Certificado Digital</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Item</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Detalhes</th>
                    </tr></thead><tbody>
                    <?php row('Certificado Ativo', $certOk ? 'ok' : 'warn', $certMsg); ?>
                    <?php if ($certOk): row('Arquivo .pfx', $certArquivoOk ? 'ok' : 'erro', $certArquivoMsg); endif; ?>
                    </tbody>
                </table>

                <!-- Testes Funcionais -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-vial" style="color:#1086dd"></i> Testes Funcionais</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Teste</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Resultado</th>
                    </tr></thead><tbody>
                    <?php
                    row('Calculo de Impostos (R$ 1.000,00)',
                        ($calcTest !== false && is_array($calcTest)) ? 'ok' : 'erro',
                        $calcMsg);
                    row('Geracao XML DPS',
                        $dpsTest ? 'ok' : ($dpsTest === null ? 'warn' : 'erro'),
                        $dpsMsg);
                    row('Normalizacao de Valor',
                        $normTest ? 'ok' : ($normTest === null ? 'warn' : 'erro'),
                        $normMsg);
                    ?>
                    </tbody>
                </table>

                <!-- Diretorios -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-folder-open" style="color:#1086dd"></i> Permissoes de Diretorio</h5>
                <table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px">
                    <thead><tr style="background:var(--dark-1,#14141a)">
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%">Diretorio/Arquivo</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%">Status</th>
                        <th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)">Detalhes</th>
                    </tr></thead><tbody>
                    <?php foreach ($dirResults as $dr): ?>
                        <?php
                        $dStatus = $dr['exists'] ? ($dr['writable'] ? 'ok' : 'warn') : 'warn';
                        $dMsg = ($dr['exists'] ? 'Existe' : 'Nao existe') . ' | ' . ($dr['writable'] ? 'Gravavel' : 'Nao gravavel');
                        row(basename($dr['path']), $dStatus, $dMsg . ' — ' . $dr['desc']);
                        ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Ambiente -->
                <h5 style="color:var(--title,#d4d8e0); margin-top:20px"><i class="fas fa-info-circle" style="color:#1086dd"></i> Informacoes do Ambiente</h5>
                <div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); font-family:monospace; font-size:12px">
                    PHP Version: <?php echo phpversion(); ?><br>
                    CI Version: <?php echo CI_VERSION; ?><br>
                    Ambiente NFSe: <?php echo ucfirst($ambienteSistema); ?><br>
                    Data/Hora Servidor: <?php echo date('d/m/Y H:i:s'); ?><br>
                    Base URL: <?php echo base_url(); ?>
                </div>

            </div>
        </div>
    </div>
</div>
