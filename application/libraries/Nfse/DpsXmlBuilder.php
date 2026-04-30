<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/NfseConfig.php';

/**
 * NFS-e Nacional: DPS XML Builder
 * Gera o XML DPS (Declaração Prévia de Serviços) conforme schema nacional
 * para envio à API NFS-e Nacional (Sistema Nacional NFS-e)
 */
class DpsXmlBuilder
{
    private $codigoMunicipio;
    private $codigoUf;
    private $versaoDps;

    public function __construct($config = [])
    {
        $this->codigoMunicipio = $config['codigo_municipio'] ?? '1302603'; // Manaus
        $this->codigoUf = $config['codigo_uf'] ?? '13'; // AM
        $this->versaoDps = $config['versao_dps'] ?? '1.00';
    }

    /**
     * Gera o XML DPS completo para envio à API NFS-e Nacional
     *
     * @param array $dados Dados da NFS-e:
     *   - prestador: ['cnpj' => '', 'razao_social' => '', 'im' => '', 'cnae' => '']
     *   - tomador: ['cpf_cnpj' => '', 'razao_social' => '', 'im' => '', 'email' => '', 'endereco' => [...]]
     *   - servico: ['descricao' => '', 'codigo_tributacao_nacional' => '', 'codigo_tributacao_municipal' => '', 'valor_servicos' => 0, ...]
     *   - tributacao: ['natureza_operacao' => '1', 'optante_simples' => true, 'regime_especial' => '0', 'incentivador_cultural' => '0']
     *   - competencia: 'YYYY-MM-DD'
     * @return string XML DPS sem assinatura
     */
    public function gerarDps(array $dados)
    {
        $idDps = $dados['id_dps'] ?? NfseConfig::gerarIdDps($dados['prestador']['cnpj'] ?? '');

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;

        // Elemento raiz: dps
        $dps = $dom->createElementNS('http://www.sped.fazenda.gov.br/nfse', 'dps');
        $dps->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dps->setAttribute('versao', $this->versaoDps);
        $dps->setAttribute('Id', $idDps);
        $dom->appendChild($dps);

        // infDps
        $infDps = $dom->createElement('infDps');
        $infDps->setAttribute('Id', $idDps);
        $dps->appendChild($infDps);

        // Competência
        $competencia = $dados['competencia'] ?? date('Y-m-d');
        $competNode = $dom->createElement('Competência', $this->formatarCompetencia($competencia));
        $infDps->appendChild($competNode);

        // Natureza Operação
        $natOp = $dados['tributacao']['natureza_operacao'] ?? '1';
        $infDps->appendChild($dom->createElement('NaturezaOperação', $natOp));

        // Optante Simples Nacional
        $optanteSimples = ($dados['tributacao']['optante_simples'] ?? true) ? '1' : '2';
        $infDps->appendChild($dom->createElement('OptanteSimplesNacional', $optanteSimples));

        // Regime Especial Tributação
        $regimeEspecial = $dados['tributacao']['regime_especial'] ?? '0';
        $infDps->appendChild($dom->createElement('RegimeEspecialTributação', $regimeEspecial));

        // Incentivador Cultural
        $incentivador = $dados['tributacao']['incentivador_cultural'] ?? '0';
        $infDps->appendChild($dom->createElement('IncentivadorCultural', $incentivador));

        // Prestador
        $prestador = $this->criarPrestador($dom, $dados['prestador'] ?? []);
        $infDps->appendChild($prestador);

        // Tomador
        $tomador = $this->criarTomador($dom, $dados['tomador'] ?? []);
        $infDps->appendChild($tomador);

        // Serviço
        $servico = $this->criarServico($dom, $dados['servico'] ?? [], $dados['tributacao'] ?? []);
        $infDps->appendChild($servico);

        return $dom->saveXML();
    }

    /**
     * Cria elemento Prestador
     */
    private function criarPrestador(DOMDocument $dom, array $prestador)
    {
        $prestNode = $dom->createElement('Prestador');

        // CPF/CNPJ
        $identificacao = $dom->createElement('IdentificacaoPrestador');
        $cnpj = preg_replace('/\D/', '', $prestador['cnpj'] ?? '');
        if (strlen($cnpj) === 14) {
            $identificacao->appendChild($dom->createElement('CpfCnpj'))
                ->appendChild($dom->createElement('Cnpj', $cnpj));
        } elseif (strlen($cnpj) > 0) {
            $identificacao->appendChild($dom->createElement('CpfCnpj'))
                ->appendChild($dom->createElement('Cpf', str_pad($cnpj, 11, '0', STR_PAD_LEFT)));
        }

        // Inscrição Municipal
        if (!empty($prestador['im'])) {
            $identificacao->appendChild($dom->createElement('InscricaoMunicipal', $prestador['im']));
        }

        // Inscrição Estadual
        if (!empty($prestador['ie'])) {
            $identificacao->appendChild($dom->createElement('InscricaoEstadual', $prestador['ie']));
        }

        $prestNode->appendChild($identificacao);

        // Razão Social (obrigatória — usa fallback se vazia)
        $razaoSocial = !empty($prestador['razao_social']) ? $prestador['razao_social'] : ($prestador['nome_fantasia'] ?? 'PRESTADOR');
        $prestNode->appendChild($dom->createElement('RazaoSocial', $this->escapeXml($razaoSocial)));

        // Endereço do Prestador
        if (!empty($prestador['endereco'])) {
            $prestNode->appendChild($this->criarEndereco($dom, $prestador['endereco'], 'Endereco'));
        }

        // Contato
        if (!empty($prestador['email']) || !empty($prestador['telefone'])) {
            $contato = $dom->createElement('Contato');
            if (!empty($prestador['email'])) {
                $contato->appendChild($dom->createElement('Email', $this->escapeXml($prestador['email'])));
            }
            if (!empty($prestador['telefone'])) {
                $contato->appendChild($dom->createElement('Telefone', $prestador['telefone']));
            }
            $prestNode->appendChild($contato);
        }

        return $prestNode;
    }

    /**
     * Cria elemento Tomador
     */
    private function criarTomador(DOMDocument $dom, array $tomador)
    {
        $tomNode = $dom->createElement('Tomador');

        // CPF/CNPJ
        $cpfCnpj = preg_replace('/\D/', '', $tomador['cpf_cnpj'] ?? '');
        if (empty($cpfCnpj)) {
            return $tomNode;
        }

        $identificacao = $dom->createElement('IdentificacaoTomador');
        $cpfcnpjNode = $dom->createElement('CpfCnpj');
        if (strlen($cpfCnpj) === 14) {
            $cpfcnpjNode->appendChild($dom->createElement('Cnpj', $cpfCnpj));
        } else {
            $cpfcnpjNode->appendChild($dom->createElement('Cpf', str_pad($cpfCnpj, 11, '0', STR_PAD_LEFT)));
        }
        $identificacao->appendChild($cpfcnpjNode);

        if (!empty($tomador['im'])) {
            $identificacao->appendChild($dom->createElement('InscricaoMunicipal', $tomador['im']));
        }

        if (!empty($tomador['ie'])) {
            $identificacao->appendChild($dom->createElement('InscricaoEstadual', $tomador['ie']));
        }

        $tomNode->appendChild($identificacao);

        // Razão Social / Nome (obrigatória — usa fallback se vazia)
        $razaoSocial = !empty($tomador['razao_social']) ? $tomador['razao_social'] : 'TOMADOR';
        $tomNode->appendChild($dom->createElement('RazaoSocial', $this->escapeXml($razaoSocial)));

        // Endereço do Tomador
        if (!empty($tomador['endereco'])) {
            $tomNode->appendChild($this->criarEndereco($dom, $tomador['endereco'], 'Endereco'));
        }

        // Contato
        if (!empty($tomador['email']) || !empty($tomador['telefone'])) {
            $contato = $dom->createElement('Contato');
            if (!empty($tomador['email'])) {
                $contato->appendChild($dom->createElement('Email', $this->escapeXml($tomador['email'])));
            }
            if (!empty($tomador['telefone'])) {
                $contato->appendChild($dom->createElement('Telefone', $tomador['telefone']));
            }
            $tomNode->appendChild($contato);
        }

        return $tomNode;
    }

    /**
     * Cria elemento de Serviço (Detalhe)
     */
    private function criarServico(DOMDocument $dom, array $servico, array $tributacao)
    {
        $servNode = $dom->createElement('DetalhamentoServico');

        // Descrição dos Serviços (obrigatória — usa fallback se vazia)
        $descricao = !empty($servico['descricao']) ? $servico['descricao'] : 'Serviços prestados conforme contrato.';
        $servNode->appendChild($dom->createElement('Descricao', $this->escapeXml($descricao)));

        // CNAE
        if (!empty($servico['cnae'])) {
            $servNode->appendChild($dom->createElement('Cnae', preg_replace('/\D/', '', $servico['cnae'])));
        }

        // Código Tributação Municipal
        if (!empty($servico['codigo_tributacao_municipal'])) {
            $servNode->appendChild($dom->createElement('CodigoTributacaoMunicipal', $servico['codigo_tributacao_municipal']));
        }

        // Código Tributação Nacional (LC 116/2003)
        if (!empty($servico['codigo_tributacao_nacional'])) {
            $servNode->appendChild($dom->createElement('CodigoTributacaoNacional', $servico['codigo_tributacao_nacional']));
        }

        // Valores
        $valores = $dom->createElement('Valores');

        $valorServicos = number_format(floatval($servico['valor_servicos'] ?? 0), 2, '.', '');
        $valores->appendChild($dom->createElement('ValorServicos', $valorServicos));

        // Deduções
        $valorDeducoes = number_format(floatval($servico['valor_deducoes'] ?? 0), 2, '.', '');
        if ($valorDeducoes > 0) {
            $valores->appendChild($dom->createElement('ValorDeducoes', $valorDeducoes));
        }

        // ISS
        $issNode = $dom->createElement('Iss');
        $issNode->appendChild($dom->createElement('ValorIss', number_format(floatval($servico['valor_iss'] ?? 0), 2, '.', '')));

        $aliquotaIss = number_format(floatval($servico['aliquota_iss'] ?? $tributacao['aliquota_iss'] ?? 5.00), 4, '.', '');
        $issNode->appendChild($dom->createElement('Aliquota', $aliquotaIss));

        // ISS Retido
        if (!empty($servico['iss_retido'])) {
            $issNode->appendChild($dom->createElement('IssRetido', '1'));
            $issNode->appendChild($dom->createElement('ValorIssRetido', number_format(floatval($servico['valor_iss_retido'] ?? 0), 2, '.', '')));
        } else {
            $issNode->appendChild($dom->createElement('IssRetido', '2'));
        }

        $valores->appendChild($issNode);

        // Base de Cálculo
        $baseCalculo = number_format(floatval($servico['valor_servicos'] ?? 0) - floatval($servico['valor_deducoes'] ?? 0), 2, '.', '');
        $valores->appendChild($dom->createElement('BaseCalculo', $baseCalculo));

        // Valor Líquido
        $valorLiquido = number_format(floatval($servico['valor_liquido'] ?? 0), 2, '.', '');
        $valores->appendChild($dom->createElement('ValorLiquido', $valorLiquido));

        $servNode->appendChild($valores);

        // Tributações federais (se houver retenções)
        $tribFed = $dom->createElement('TributacoesFederais');

        // PIS
        if (!empty($servico['pis_retido']) || !empty($servico['valor_pis'])) {
            $tribFed->appendChild($dom->createElement('ValorPis', number_format(floatval($servico['valor_pis'] ?? 0), 2, '.', '')));
            $tribFed->appendChild($dom->createElement('PisRetido', !empty($servico['pis_retido']) ? '1' : '2'));
        }

        // COFINS
        if (!empty($servico['cofins_retido']) || !empty($servico['valor_cofins'])) {
            $tribFed->appendChild($dom->createElement('ValorCofins', number_format(floatval($servico['valor_cofins'] ?? 0), 2, '.', '')));
            $tribFed->appendChild($dom->createElement('CofinsRetido', !empty($servico['cofins_retido']) ? '1' : '2'));
        }

        // IRPJ
        if (!empty($servico['irrf_retido']) || !empty($servico['valor_irrf'])) {
            $tribFed->appendChild($dom->createElement('ValorIrrf', number_format(floatval($servico['valor_irrf'] ?? 0), 2, '.', '')));
            $tribFed->appendChild($dom->createElement('IrrfRetido', !empty($servico['irrf_retido']) ? '1' : '2'));
        }

        // CSLL
        if (!empty($servico['csll_retido']) || !empty($servico['valor_csll'])) {
            $tribFed->appendChild($dom->createElement('ValorCsll', number_format(floatval($servico['valor_csll'] ?? 0), 2, '.', '')));
            $tribFed->appendChild($dom->createElement('CsllRetido', !empty($servico['csll_retido']) ? '1' : '2'));
        }

        // INSS
        if (!empty($servico['inss_retido']) || !empty($servico['valor_inss'])) {
            $tribFed->appendChild($dom->createElement('ValorInss', number_format(floatval($servico['valor_inss'] ?? 0), 2, '.', '')));
            $tribFed->appendChild($dom->createElement('InssRetido', !empty($servico['inss_retido']) ? '1' : '2'));
        }

        // Só anexar TributacoesFederais se houver conteúdo
        if ($tribFed->hasChildNodes()) {
            $servNode->appendChild($tribFed);
        }

        // Município de Prestação (obrigatório)
        $municipioPrestacao = $dom->createElement('MunicipioPrestacaoServico');
        $municipioPrestacao->appendChild($dom->createElement('CodigoMunicipioIbge', $this->codigoMunicipio));
        $servNode->appendChild($municipioPrestacao);

        return $servNode;
    }

    /**
     * Cria elemento de Endereço
     */
    private function criarEndereco(DOMDocument $dom, array $endereco, $tagName = 'Endereco')
    {
        $endNode = $dom->createElement($tagName);

        if (!empty($endereco['logradouro'])) {
            $endNode->appendChild($dom->createElement('Logradouro', $this->escapeXml($endereco['logradouro'])));
        }
        if (!empty($endereco['numero'])) {
            $endNode->appendChild($dom->createElement('Numero', $endereco['numero']));
        }
        if (!empty($endereco['complemento'])) {
            $endNode->appendChild($dom->createElement('Complemento', $this->escapeXml($endereco['complemento'])));
        }
        if (!empty($endereco['bairro'])) {
            $endNode->appendChild($dom->createElement('Bairro', $this->escapeXml($endereco['bairro'])));
        }
        if (!empty($endereco['codigo_municipio'])) {
            $endNode->appendChild($dom->createElement('CodigoMunicipioIbge', $endereco['codigo_municipio']));
        }
        if (!empty($endereco['uf'])) {
            $endNode->appendChild($dom->createElement('Uf', $endereco['uf']));
        }
        if (!empty($endereco['cep'])) {
            $endNode->appendChild($dom->createElement('Cep', preg_replace('/\D/', '', $endereco['cep'])));
        }

        return $endNode;
    }

    /**
     * Formata competência: YYYY-MM-DD -> YYYY-MM
     */
    private function formatarCompetencia($data)
    {
        return substr($data, 0, 7); // YYYY-MM
    }

    /**
     * Escapa caracteres XML especiais
     */
    private function escapeXml($str)
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}