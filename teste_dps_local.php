<?php
/**
 * Teste local do XML DPS gerado
 */

if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/system/');
}

require_once 'application/libraries/Nfse/NfseConfig.php';
require_once 'application/libraries/Nfse/DpsXmlBuilder.php';

$builder = new DpsXmlBuilder([
    'codigo_municipio' => '1302603',
    'codigo_uf' => '13',
    'versao_dps' => '1.01',
]);

$dados = [
    'prestador' => [
        'cnpj' => '54518217000117',
        'razao_social' => 'JJ TECNOLOGIAS LTDA',
        'im' => '123456',
        'endereco' => [
            'logradouro' => 'R Aldino Azevedo',
            'numero' => '41',
            'bairro' => 'Coroado',
            'codigo_municipio' => '1302603',
            'cep' => '69080360',
        ],
        'telefone' => '9284466044',
        'email' => 'Comercial@jj-ferreiras.com.br',
    ],
    'tomador' => [
        'cpf_cnpj' => '04240370001986',
        'razao_social' => 'Mercantil Nova Era Ltda',
        'endereco' => [
            'logradouro' => 'Avenida Torquato Tapajos',
            'numero' => '2871',
            'complemento' => 'Galpaob',
            'bairro' => 'Da Paz',
            'codigo_municipio' => '1302603',
            'cep' => '69048010',
        ],
        'telefone' => '9230905000',
        'email' => 'nfe.novaera@novaeranet.com.br',
    ],
    'servico' => [
        'descricao' => 'Comércio varejista especializado de equipamentos e suprimentos de informática',
        'codigo_tributacao_nacional' => '010701',
        'codigo_tributacao_municipal' => '100',
        'valor_servicos' => 320.00,
        'valor_deducoes' => 0,
        'aliquota_iss' => 5.00,
    ],
    'tributacao' => [
        'natureza_operacao' => '1',
        'optante_simples' => true,
        'regime_especial' => '0',
        'incentivador_cultural' => '0',
        'aliquota_iss' => 5.00,
    ],
    'competencia' => '2026-04-01',
    'ambiente' => 'homologacao',
    'serie' => 1,
];

$xml = $builder->gerarDps($dados);

echo "=== XML DPS GERADO ===\n";
echo $xml;
echo "\n\n=== VALIDAÇÃO ===\n";

$dom = new DOMDocument('1.0', 'UTF-8');
if (@$dom->loadXML($xml)) {
    echo "XML está bem formado.\n";
    echo "Tamanho: " . strlen($xml) . " bytes\n";

    // Verificar elementos obrigatórios
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('nfse', 'http://www.sped.fazenda.gov.br/nfse');

    $obrigatorios = [
        '//nfse:DPS',
        '//nfse:infDPS',
        '//nfse:tpAmb',
        '//nfse:dhEmi',
        '//nfse:serie',
        '//nfse:nDPS',
        '//nfse:dCompet',
        '//nfse:tpEmit',
        '//nfse:cLocEmi',
        '//nfse:prest',
        '//nfse:prest/nfse:CNPJ',
        '//nfse:prest/nfse:cNaoNIF',
        '//nfse:prest/nfse:xNome',
        '//nfse:prest/nfse:regTrib',
        '//nfse:toma',
        '//nfse:toma/nfse:CNPJ',
        '//nfse:toma/nfse:cNaoNIF',
        '//nfse:toma/nfse:xNome',
        '//nfse:serv',
        '//nfse:serv/nfse:locPrest',
        '//nfse:serv/nfse:locPrest/nfse:cLocPrestacao',
        '//nfse:serv/nfse:cServ',
        '//nfse:serv/nfse:cServ/nfse:cTribNac',
        '//nfse:valores',
        '//nfse:valores/nfse:vServPrest',
        '//nfse:valores/nfse:vServPrest/nfse:vReceb',
        '//nfse:valores/nfse:vServPrest/nfse:vServ',
        '//nfse:valores/nfse:vDescCondIncond',
        '//nfse:valores/nfse:vDescCondIncond/nfse:vDescIncond',
        '//nfse:valores/nfse:vDescCondIncond/nfse:vDescCond',
        '//nfse:valores/nfse:Trib',
        '//nfse:valores/nfse:Trib/nfse:tribMun',
        '//nfse:valores/nfse:Trib/nfse:tribMun/nfse:tribISSQN',
        '//nfse:valores/nfse:Trib/nfse:tribMun/nfse:tpImunidade',
        '//nfse:valores/nfse:Trib/nfse:tribMun/nfse:tpRetISSQN',
        '//nfse:valores/nfse:Trib/nfse:tribMun/nfse:pAliq',
        '//nfse:valores/nfse:Trib/nfse:totTrib',
        '//nfse:valores/nfse:Trib/nfse:totTrib/nfse:indTotTrib',
        '//nfse:valores/nfse:Trib/nfse:tribFed',
        '//nfse:valores/nfse:Trib/nfse:tribFed/nfse:piscofins',
        '//nfse:valores/nfse:Trib/nfse:tribFed/nfse:vRetCP',
        '//nfse:valores/nfse:Trib/nfse:tribFed/nfse:vRetIRRF',
        '//nfse:valores/nfse:Trib/nfse:tribFed/nfse:vRetCSLL',
        '//nfse:IBSCBS',
        '//nfse:IBSCBS/nfse:finNFSe',
        '//nfse:IBSCBS/nfse:indFinal',
        '//nfse:IBSCBS/nfse:cIndOp',
        '//nfse:IBSCBS/nfse:indDest',
        '//nfse:IBSCBS/nfse:indZFMALC',
    ];

    foreach ($obrigatorios as $path) {
        $nodes = $xpath->query($path);
        $status = ($nodes->length > 0) ? 'OK' : 'FALTANDO';
        echo "$status: $path\n";
    }
} else {
    echo "XML MAL FORMADO!\n";
}
