<?php
/**
 * View: Diagnóstico Completo do Módulo NFSe
 * Verifica tabelas, colunas, configurações, certificado, bibliotecas e faz testes
 */

// ============================================
// HELPERS
// ============================================
function iconOk() { return '<i class="fas fa-check-circle" style="color:#28a745"></i>'; }
function iconWarn() { return '<i class="fas fa-exclamation-triangle" style="color:#ffc107"></i>'; }
function iconErr() { return '<i class="fas fa-times-circle" style="color:#dc3545"></i>'; }
function iconInfo() { return '<i class="fas fa-info-circle" style="color:#1086dd"></i>'; }

function row($label, $status, $message = '') {
    $color = $status === 'ok' ? '#28a745' : ($status === 'warn' ? '#ffc107' : '#dc3545');
    $icon = $status === 'ok' ? iconOk() : ($status === 'warn' ? iconWarn() : iconErr());
    echo '<tr><td style="border-color:var(--dark-2,#272835); padding:6px 8px"><strong>' . htmlspecialchars($label) . '</strong></td>';
    echo '<td style="border-color:var(--dark-2,#272835); padding:6px 8px; color:' . $color . '; font-weight:bold">' . $icon . ' ' . strtoupper($status) . '</td>';
    echo '<td style="border-color:var(--dark-2,#272835); padding:6px 8px">' . $message . '</td></tr>';
}

// ============================================
// COLETAR DADOS
// ============================================
$tests = [];

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
    'boletos_os' => 'Tabela de boletos vinculados a OS',
    'impostos_retidos' => 'Retencoes de impostos / DRE',
    'config_sistema_impostos' => 'Configuracoes de impostos',
    'configuracoes_impostos' => 'Aliquotas do Simples Nacional',
    'certificados' => 'Certificado digital',
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
    ['boletos_os', 'nfse_id', 'Vinculo boleto->NFSe'],
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
$emitente = $this->maposs_model->getEmitente();
$emitOk = $emitente && !empty($emitente->cnpj);

// --- Certificado ---
$certificado = null;
$certOk = false;
$certMsg = 'Modelo de certificado nao encontrado';
if ($this->load->model('certificado_model')) {
    $certificado = $this->certificado_model->getCertificadoAtivo();
    if ($certificado) {
        $certOk = true;
        $certMsg = 'Ambiente: ' . ($certificado->ambiente ?? 'nao definido') . ' | Validade: ' . ($certificado->data_validade ?? '---');
    } else {
        $certMsg = 'Nenhum certificado ativo encontrado. Cadastre um certificado em Configuracoes > Certificado Digital.';
    }
}

// --- Teste de calculo de impostos ---
$calcTest = null;
$calcMsg = '';
try {
    $calcTest = $this->impostos_model->calcularImpostos(1000.00);
    if ($calcTest === false) {
        $calcMsg = 'calcularImpostos() retornou false — configuracao tributaria nao encontrada.';
    } elseif (!is_array($calcTest)) {
        $calcMsg = 'calcularImpostos() retornou tipo inesperado: ' . gettype($calcTest);
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
if (method_exists($this, 'normalizarValorMonetario')) {
    $norm1 = $this->normalizarValorMonetario('1.234,56');
    $norm2 = $this->normalizarValorMonetario('1234.56');
    $norm3 = $this->normalizarValorMonetario('');
    $normTest = ($norm1 == 1234.56 && $norm2 == 1234.56 && $norm3 == 0);
    $normMsg = "'1.234,56' => {$norm1} | '1234.56' => {$norm2} | '' => {$norm3}";
} else {
    $normMsg = 'Metodo normalizarValorMonetario() nao encontrado no controller. Execute a atualizacao do Nfse_os.php.';
}

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

// --- Ambiente ---
$ambienteSistema = $certificado->ambiente ?? 'homologacao';

?&gt;

&lt;div class="row-fluid"&gt;
    &lt;div class="span12"&gt;
        &lt;div class="widget-box" style="background:var(--wid-dark,#1c1d26); border-color:var(--dark-2,#272835)"&gt;
            &lt;div class="widget-title" style="background:var(--dark-0,#191a22); border-bottom:1px solid var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;
                &lt;span class="icon"&gt;&lt;i class="fas fa-stethoscope" style="color:#1086dd"&gt;&lt;/i&gt;&lt;/span&gt;
                &lt;h5 style="color:var(--title,#d4d8e0)"&gt;Diagnostico NFSe — Ambiente: &lt;span style="color:&#35;1086dd"&gt;&lt;?php echo ucfirst($ambienteSistema); ?&gt;&lt;/span&gt;&lt;/h5&gt;
                &lt;div style="float:right; margin:8px 10px 0 0"&gt;
                    &lt;a href="&lt;?php echo site_url('nfse_os'); ?&gt;" class="btn btn-mini" style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)"&gt;
                        &lt;i class="fas fa-arrow-left"&gt;&lt;/i&gt; Voltar
                    &lt;/a&gt;
                    &lt;button onclick="location.reload()" class="btn btn-mini" style="background:var(--dark-1,#14141a); border-color:var(--dark-2,#272835); color:var(--branco,#caced8)"&gt;
                        &lt;i class="fas fa-sync-alt"&gt;&lt;/i&gt; Recarregar
                    &lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="widget-content" style="background:var(--wid-dark,#1c1d26); color:var(--branco,#caced8)"&gt;

                &lt;!-- Resumo --&gt;
                &lt;?php
                $okCount = 0; $warnCount = 0; $errCount = 0;
                foreach ($extResults as $v) { $v ? $okCount++ : $errCount++; }
                foreach ($tableResults as $v) { $v ? $okCount++ : $errCount++; }
                foreach ($colResults as $c) { $c['exists'] ? $okCount++ : $errCount++; }
                $emitOk ? $okCount++ : $errCount++;
                $certOk ? $okCount++ : $warnCount++;
                ($calcTest !== false && is_array($calcTest)) ? $okCount++ : $errCount++;
                $dpsTest ? $okCount++ : $errCount++;
                $normTest ? $okCount++ : $errCount++;
                ?&gt;
                &lt;div class="alert" style="background:rgba(16,134,221,0.15); border-color:rgba(16,134,221,0.3); color:#1086dd; margin-bottom:20px"&gt;
                    &lt;i class="fas fa-clipboard-list"&gt;&lt;/i&gt; &lt;strong&gt;Resumo:&lt;/strong&gt;
                    &lt;span style="color:#28a745; font-weight:bold"&gt;&lt;?php echo $okCount; ?&gt; OK&lt;/span&gt; |
                    &lt;span style="color:#ffc107; font-weight:bold"&gt;&lt;?php echo $warnCount; ?&gt; Alertas&lt;/span&gt; |
                    &lt;span style="color:#dc3545; font-weight:bold"&gt;&lt;?php echo $errCount; ?&gt; Erros&lt;/span&gt;
                    &lt;?php if ($errCount > 0): ?&gt;
                        &lt;br&gt;&lt;strong style="color:#dc3545"&gt;&lt;i class="fas fa-exclamation-triangle"&gt;&lt;/i&gt; Corrija os erros antes de emitir NFSe.&lt;/strong&gt;
                    &lt;?php elseif ($warnCount > 0): ?&gt;
                        &lt;br&gt;&lt;strong style="color:#ffc107"&gt;&lt;i class="fas fa-info-circle"&gt;&lt;/i&gt; Ambiente funcional, mas com alertas.&lt;/strong&gt;
                    &lt;?php else: ?&gt;
                        &lt;br&gt;&lt;strong style="color:#28a745"&gt;&lt;i class="fas fa-check-circle"&gt;&lt;/i&gt; Ambiente validado com sucesso!&lt;/strong&gt;
                    &lt;?php endif; ?&gt;
                &lt;/div&gt;

                &lt;!-- PHP Extensions --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-microchip" style="color:#1086dd"&gt;&lt;/i&gt; Extensoes PHP&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Extensao&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Descricao&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                    &lt;?php foreach ($extResults as $ext => $loaded): ?&gt;
                        &lt;?php row($ext, $loaded ? 'ok' : 'erro', $exts[$ext]); ?&gt;
                    &lt;?php endforeach; ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Tabelas --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-database" style="color:#1086dd"&gt;&lt;/i&gt; Tabelas do Banco&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Tabela&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Descricao&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                    &lt;?php foreach ($tableResults as $table => $exists): ?&gt;
                        &lt;?php row($table, $exists ? 'ok' : 'erro', $tables[$table] . ($exists ? '' : ' — &lt;strong style="color:#dc3545"&gt;Execute as migrations!&lt;/strong&gt;')); ?&gt;
                    &lt;?php endforeach; ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Colunas --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-columns" style="color:#1086dd"&gt;&lt;/i&gt; Colunas do Banco&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Coluna&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Descricao&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;/tbody&gt;
                    &lt;?php foreach ($colResults as $col): ?&gt;
                        &lt;?php row($col['table'] . '.' . $col['column'], $col['exists'] ? 'ok' : 'erro', $col['desc'] . ($col['exists'] ? '' : ' — &lt;strong style="color:#dc3545"&gt;Execute a migration!&lt;/strong&gt;')); ?&gt;
                    &lt;?php endforeach; ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Configuracoes Impostos --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-sliders-h" style="color:#1086dd"&gt;&lt;/i&gt; Configuracoes de Impostos&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Configuracao&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Valor&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                    &lt;?php foreach ($impConfig as $key => $val): ?&gt;
                        &lt;?php
                        $isSet = $val !== 'nao configurado';
                        row($key, $isSet ? 'ok' : 'warn', $val);
                        ?&gt;
                    &lt;?php endforeach; ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Emitente --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-building" style="color:#1086dd"&gt;&lt;/i&gt; Dados do Emitente (Prestador)&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Campo&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Valor&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                        &lt;?php
                        row('Nome/Razao Social', !empty($emitente->nome) ? 'ok' : 'erro', htmlspecialchars($emitente->nome ?? 'Nao configurado'));
                        row('CNPJ', !empty($emitente->cnpj) ? 'ok' : 'erro', htmlspecialchars($emitente->cnpj ?? 'Nao configurado'));
                        row('Inscricao Municipal', !empty($emitente->inscricao_municipal) ? 'ok' : 'warn', htmlspecialchars($emitente->inscricao_municipal ?? 'Nao configurado (opcional para Manaus)'));
                        row('Inscricao Estadual', !empty($emitente->inscricao_estadual) ? 'ok' : 'warn', htmlspecialchars($emitente->inscricao_estadual ?? 'Nao configurado'));
                        row('Endereco', (!empty($emitente->rua) && !empty($emitente->cidade)) ? 'ok' : 'warn',
                            htmlspecialchars(($emitente->rua ?? '') . ', ' . ($emitente->numero ?? '') . ' — ' . ($emitente->cidade ?? '') . '/' . ($emitente->uf ?? '')));
                        row('Email', !empty($emitente->email) ? 'ok' : 'warn', htmlspecialchars($emitente->email ?? 'Nao configurado'));
                        row('Telefone', !empty($emitente->telefone) ? 'ok' : 'warn', htmlspecialchars($emitente->telefone ?? 'Nao configurado'));
                        ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Certificado --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-certificate" style="color:#1086dd"&gt;&lt;/i&gt; Certificado Digital&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Item&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Detalhes&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                        &lt;?php row('Certificado Ativo', $certOk ? 'ok' : 'warn', $certMsg); ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Testes Funcionais --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-vial" style="color:#1086dd"&gt;&lt;/i&gt; Testes Funcionais&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Teste&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Resultado&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                        &lt;?php
                        row('Calculo de Impostos (R$ 1.000,00)',
                            ($calcTest !== false && is_array($calcTest)) ? 'ok' : 'erro',
                            $calcMsg);
                        row('Geracao XML DPS',
                            $dpsTest ? 'ok' : ($dpsTest === null ? 'warn' : 'erro'),
                            $dpsMsg);
                        row('Normalizacao de Valor',
                            $normTest ? 'ok' : ($normTest === null ? 'warn' : 'erro'),
                            $normMsg);
                        ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Diretorios --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-folder-open" style="color:#1086dd"&gt;&lt;/i&gt; Permissoes de Diretorio&lt;/h5&gt;
                &lt;table class="table table-condensed" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); margin-bottom:15px"&gt;
                    &lt;thead&gt;&lt;tr style="background:var(--dark-1,#14141a)"&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:25%"&gt;Diretorio/Arquivo&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0); width:15%"&gt;Status&lt;/th&gt;
                        &lt;th style="border-color:var(--dark-2,#272835); color:var(--title,#d4d8e0)"&gt;Detalhes&lt;/th&gt;
                    &lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;
                        &lt;?php foreach ($dirResults as $dr): ?&gt;
                            &lt;?php
                            $dStatus = $dr['exists'] ? ($dr['writable'] ? 'ok' : 'warn') : 'warn';
                            $dMsg = ($dr['exists'] ? 'Existe' : 'Nao existe') . ' | ' . ($dr['writable'] ? 'Gravavel' : 'Nao gravavel');
                            row(basename($dr['path']), $dStatus, $dMsg . ' — ' . $dr['desc']);
                            ?&gt;
                        &lt;?php endforeach; ?&gt;
                    &lt;/tbody&gt;
                &lt;/table&gt;

                &lt;!-- Ambiente --&gt;
                &lt;h5 style="color:var(--title,#d4d8e0); margin-top:20px"&gt;&lt;i class="fas fa-info-circle" style="color:#1086dd"&gt;&lt;/i&gt; Informacoes do Ambiente&lt;/h5&gt;
                &lt;div class="well well-small" style="background:var(--dark-0,#191a22); border-color:var(--dark-2,#272835); color:var(--branco,#caced8); font-family:monospace; font-size:12px"&gt;
                    PHP Version: &lt;?php echo phpversion(); ?&gt;&lt;br&gt;
                    CI Version: &lt;?php echo CI_VERSION; ?&gt;&lt;br&gt;
                    Ambiente NFSe: &lt;?php echo ucfirst($ambienteSistema); ?&gt;&lt;br&gt;
                    Data/Hora Servidor: &lt;?php echo date('d/m/Y H:i:s'); ?&gt;&lt;br&gt;
                    Base URL: &lt;?php echo base_url(); ?&gt;
                &lt;/div&gt;

            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
