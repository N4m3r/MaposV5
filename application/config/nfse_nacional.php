<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Configuração: NFS-e Nacional (Sistema Nacional NFS-e)
 * Manaus - Decreto nº 6.743 - Obrigatório desde Janeiro 2026
 */

// Ambiente: 'homologacao' ou 'producao'
$config['nfse_ambiente'] = getenv('NFSE_AMBIENTE') ?: 'homologacao';

// URLs do Sistema Nacional NFS-e
// ATENCAO: A API de emissao e do SEFIN Nacional, nao do ADN
// SEFIN = Sistema de Emissao Fiscal Nacional (emissao propriamente dita)
// ADN   = Ambiente de Dados Nacional (distribuicao/consulta)
$config['nfse_urls'] = [
    'homologacao' => 'https://sefin.producaorestrita.nfse.gov.br/API/SefinNacional/',
    'producao'    => 'https://sefin.nfse.gov.br/SefinNacional/',
];

// Código IBGE do Município (Manaus = 1302603)
$config['nfse_codigo_municipio'] = getenv('NFSE_CODIGO_MUNICIPIO') ?: '1302603';

// Código UF (AM = 13)
$config['nfse_codigo_uf'] = '13';

// Versão do DPS
$config['nfse_versao_dps'] = '1.00';

// Timeout cURL em segundos
$config['nfse_timeout'] = 60;

// Caminho para CA chain ICP-Brasil (deixe vazio para usar o CA do sistema)
$config['nfse_ca_path'] = '';

// Caminho para certificados temporários PEM
$config['nfse_temp_path'] = APPPATH . 'private/temp/';

// Natureza Operação (padrão: 1 = Tributação no município)
// 1 = Tributação no município
// 2 = Tributação fora do município
// 3 = Isenção
// 4 = Imunidade
// 5 = Exigibilidade suspensa por decisão judicial
// 6 = Exigibilidade suspensa por procedimento administrativo
$config['nfse_natureza_operacao'] = '1';

// Simples Nacional (padrão: true para optante)
$config['nfse_optante_simples'] = true;

// Regime Especial Tributação (0 = nenhum)
// 1 = Microempresa Municipal
// 2 = Estimativa
// 3 = Sociedade de Profissionais
// 4 = Cooperativa
$config['nfse_regime_especial'] = '0';

// Incentivador Cultural (0 = não, 1 = sim)
$config['nfse_incentivador_cultural'] = '0';

// Responsável retenção (1 = sim, 2 = não)
$config['nfse_responsavel_retencao'] = '2';