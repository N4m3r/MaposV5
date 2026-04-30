<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/NfseConfig.php';

/**
 * NFS-e Nacional: DPS XML Builder
 * Gera o XML DPS conforme schema nacional SEFIN NFS-e v1.00/1.01
 */
class DpsXmlBuilder
{
    private $codigoMunicipio;
    private $codigoUf;
    private $versaoDps;

    public function __construct($config = [])
    {
        $this->codigoMunicipio = $config['codigo_municipio'] ?? '1302603';
        $this->codigoUf = $config['codigo_uf'] ?? '13';
        $this->versaoDps = $config['versao_dps'] ?? '1.00';
    }

    /**
     * Gera o XML DPS completo para envio à API NFS-e Nacional
     *
     * @param array $dados Dados da NFS-e:
     *   - prestador: ['cnpj' => '', 'razao_social' => '', 'im' => '', 'endereco' => [...], 'email' => '', 'telefone' => '']
     *   - tomador: ['cpf_cnpj' => '', 'razao_social' => '', 'endereco' => [...], 'email' => '', 'telefone' => '']
     *   - servico: ['descricao' => '', 'codigo_tributacao_nacional' => '', 'codigo_tributacao_municipal' => '', 'valor_servicos' => 0, ...]
     *   - tributacao: ['natureza_operacao' => '1', 'optante_simples' => true, 'regime_especial' => '0', 'incentivador_cultural' => '0']
     *   - competencia: 'YYYY-MM-DD'
     *   - ambiente: 'homologacao'|'producao'
     *   - serie: int
     *   - n_dps: int|string (número da DPS)
     * @return string XML DPS sem assinatura
     */
    public function gerarDps(array $dados)
    {
        $prestador = $dados['prestador'] ?? [];
        $tomador = $dados['tomador'] ?? [];
        $servico = $dados['servico'] ?? [];
        $tributacao = $dados['tributacao'] ?? [];
        $ambiente = ($dados['ambiente'] ?? 'homologacao') === 'producao' ? '1' : '2';
        $competencia = $dados['competencia'] ?? date('Y-m-d');
        $serie = $dados['serie'] ?? 1;
        $nDps = $dados['n_dps'] ?? null;

        $cnpjPrestador = preg_replace('/\D/', '', $prestador['cnpj'] ?? '');
        $idDps = NfseConfig::gerarIdDps($cnpjPrestador, $this->codigoMunicipio, $serie, $nDps);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;

        // Elemento raiz: DPS (namespace oficial)
        $ns = 'http://www.sped.fazenda.gov.br/nfse';
        $dps = $dom->createElementNS($ns, 'DPS');
        $dps->setAttribute('versao', $this->versaoDps);
        $dom->appendChild($dps);

        // infDPS
        $infDps = $dom->createElementNS($ns, 'infDPS');
        $infDps->setAttribute('Id', $idDps);
        $dps->appendChild($infDps);

        // tpAmb
        $infDps->appendChild($dom->createElementNS($ns, 'tpAmb', $ambiente));

        // dhEmi (data/hora emissão no formato ISO 8601)
        $dhEmi = date('Y-m-d\TH:i:sP');
        $infDps->appendChild($dom->createElementNS($ns, 'dhEmi', $dhEmi));

        // verAplic
        $infDps->appendChild($dom->createElementNS($ns, 'verAplic', 'MAPOS-NFSE-1.0'));

        // serie
        $infDps->appendChild($dom->createElementNS($ns, 'serie', str_pad((string)$serie, 1, '0', STR_PAD_LEFT)));

        // nDPS
        $nDpsValor = $nDps ?? substr($idDps, -15);
        $infDps->appendChild($dom->createElementNS($ns, 'nDPS', ltrim($nDpsValor, '0') ?: '1'));

        // dCompet (AAAA-MM-DD)
        $infDps->appendChild($dom->createElementNS($ns, 'dCompet', $competencia));

        // tpEmit (1=Prestador)
        $infDps->appendChild($dom->createElementNS($ns, 'tpEmit', '1'));

        // cLocEmi (código IBGE do município emissor)
        $infDps->appendChild($dom->createElementNS($ns, 'cLocEmi', $this->codigoMunicipio));

        // Prestador
        $prestNode = $this->criarPrestador($dom, $ns, $prestador, $tributacao);
        $infDps->appendChild($prestNode);

        // Tomador (se houver)
        $tomadorNode = $this->criarTomador($dom, $ns, $tomador);
        if ($tomadorNode) {
            $infDps->appendChild($tomadorNode);
        }

        // Serviço
        $servNode = $this->criarServico($dom, $ns, $servico);
        $infDps->appendChild($servNode);

        // Valores
        $valoresNode = $this->criarValores($dom, $ns, $servico, $tributacao);
        $infDps->appendChild($valoresNode);

        // IBSCBS (Reforma Tributária - obrigatório desde NT 007/2026)
        $ibscbsNode = $this->criarIbscbs($dom, $ns, $servico);
        $infDps->appendChild($ibscbsNode);

        return $dom->saveXML();
    }

    private function criarPrestador(DOMDocument $dom, string $ns, array $prestador, array $tributacao)
    {
        $prest = $dom->createElementNS($ns, 'prest');

        $cnpj = preg_replace('/\D/', '', $prestador['cnpj'] ?? '');
        if (strlen($cnpj) === 14) {
            $prest->appendChild($dom->createElementNS($ns, 'CNPJ', $cnpj));
        }

        $im = preg_replace('/\D/', '', $prestador['im'] ?? '');
        if (!empty($im)) {
            $prest->appendChild($dom->createElementNS($ns, 'IM', $im));
        }

        $xNome = !empty($prestador['razao_social']) ? $prestador['razao_social'] : ($prestador['nome_fantasia'] ?? 'PRESTADOR');
        $prest->appendChild($dom->createElementNS($ns, 'xNome', $this->escapeXml($xNome)));

        // Endereço
        if (!empty($prestador['endereco'])) {
            $prest->appendChild($this->criarEndereco($dom, $ns, $prestador['endereco']));
        }

        if (!empty($prestador['telefone'])) {
            $fone = preg_replace('/\D/', '', $prestador['telefone']);
            if (!empty($fone)) {
                $prest->appendChild($dom->createElementNS($ns, 'fone', $fone));
            }
        }

        if (!empty($prestador['email'])) {
            $prest->appendChild($dom->createElementNS($ns, 'email', $this->escapeXml($prestador['email'])));
        }

        // Regime tributário
        $regTrib = $dom->createElementNS($ns, 'regTrib');
        $opSimpNac = ($tributacao['optante_simples'] ?? true) ? '1' : '2';
        $regTrib->appendChild($dom->createElementNS($ns, 'opSimpNac', $opSimpNac));
        $regTrib->appendChild($dom->createElementNS($ns, 'regEspTrib', $tributacao['regime_especial'] ?? '0'));
        $prest->appendChild($regTrib);

        return $prest;
    }

    private function criarTomador(DOMDocument $dom, string $ns, array $tomador)
    {
        $cpfCnpj = preg_replace('/\D/', '', $tomador['cpf_cnpj'] ?? '');
        if (empty($cpfCnpj)) {
            return null;
        }

        $toma = $dom->createElementNS($ns, 'toma');

        if (strlen($cpfCnpj) === 14) {
            $toma->appendChild($dom->createElementNS($ns, 'CNPJ', $cpfCnpj));
        } elseif (strlen($cpfCnpj) === 11) {
            $toma->appendChild($dom->createElementNS($ns, 'CPF', $cpfCnpj));
        }

        $xNome = !empty($tomador['razao_social']) ? $tomador['razao_social'] : 'TOMADOR';
        $toma->appendChild($dom->createElementNS($ns, 'xNome', $this->escapeXml($xNome)));

        if (!empty($tomador['endereco'])) {
            $toma->appendChild($this->criarEndereco($dom, $ns, $tomador['endereco']));
        }

        if (!empty($tomador['telefone'])) {
            $fone = preg_replace('/\D/', '', $tomador['telefone']);
            if (!empty($fone)) {
                $toma->appendChild($dom->createElementNS($ns, 'fone', $fone));
            }
        }

        if (!empty($tomador['email'])) {
            $toma->appendChild($dom->createElementNS($ns, 'email', $this->escapeXml($tomador['email'])));
        }

        return $toma;
    }

    private function criarEndereco(DOMDocument $dom, string $ns, array $endereco)
    {
        $end = $dom->createElementNS($ns, 'end');

        if (!empty($endereco['logradouro'])) {
            $end->appendChild($dom->createElementNS($ns, 'xLgr', $this->escapeXml($endereco['logradouro'])));
        }
        if (!empty($endereco['numero'])) {
            $end->appendChild($dom->createElementNS($ns, 'nro', $endereco['numero']));
        }
        if (!empty($endereco['complemento'])) {
            $end->appendChild($dom->createElementNS($ns, 'xCpl', $this->escapeXml($endereco['complemento'])));
        }
        if (!empty($endereco['bairro'])) {
            $end->appendChild($dom->createElementNS($ns, 'xBairro', $this->escapeXml($endereco['bairro'])));
        }

        $endNac = $dom->createElementNS($ns, 'endNac');
        $codMun = !empty($endereco['codigo_municipio']) ? $endereco['codigo_municipio'] : $this->codigoMunicipio;
        $endNac->appendChild($dom->createElementNS($ns, 'cMun', preg_replace('/\D/', '', $codMun)));
        if (!empty($endereco['cep'])) {
            $endNac->appendChild($dom->createElementNS($ns, 'CEP', preg_replace('/\D/', '', $endereco['cep'])));
        }
        $end->appendChild($endNac);

        return $end;
    }

    private function criarServico(DOMDocument $dom, string $ns, array $servico)
    {
        $serv = $dom->createElementNS($ns, 'serv');

        // Local de prestação
        $locPrest = $dom->createElementNS($ns, 'locPrest');
        $locPrest->appendChild($dom->createElementNS($ns, 'cLocPrestacao', $this->codigoMunicipio));
        $serv->appendChild($locPrest);

        // Código do serviço
        $cServ = $dom->createElementNS($ns, 'cServ');

        $cTribNac = preg_replace('/\D/', '', $servico['codigo_tributacao_nacional'] ?? '010701');
        $cServ->appendChild($dom->createElementNS($ns, 'cTribNac', $cTribNac));

        $cTribMun = $servico['codigo_tributacao_municipal'] ?? '';
        if (!empty($cTribMun)) {
            $cServ->appendChild($dom->createElementNS($ns, 'cTribMun', $cTribMun));
        }

        $descricao = !empty($servico['descricao']) ? $servico['descricao'] : 'Serviços prestados conforme contrato.';
        $cServ->appendChild($dom->createElementNS($ns, 'xDescServ', $this->escapeXml($descricao)));

        // cNBS (opcional, mas recomendado para reforma tributária)
        $cnae = preg_replace('/\D/', '', $servico['cnae'] ?? '');
        if (!empty($cnae)) {
            $cServ->appendChild($dom->createElementNS($ns, 'cNBS', $cnae));
        }

        $serv->appendChild($cServ);

        return $serv;
    }

    private function criarValores(DOMDocument $dom, string $ns, array $servico, array $tributacao)
    {
        $valores = $dom->createElementNS($ns, 'valores');

        $valorServicos = floatval($servico['valor_servicos'] ?? 0);
        $valorDeducoes = floatval($servico['valor_deducoes'] ?? 0);
        $baseCalculo = $valorServicos - $valorDeducoes;
        if ($baseCalculo < 0) $baseCalculo = 0;

        // vServPrest
        $vServPrest = $dom->createElementNS($ns, 'vServPrest');
        $vServPrest->appendChild($dom->createElementNS($ns, 'vServ', number_format($valorServicos, 2, '.', '')));
        $valores->appendChild($vServPrest);

        // Tributação municipal
        $trib = $dom->createElementNS($ns, 'trib');
        $tribMun = $dom->createElementNS($ns, 'tribMun');

        // tribISSQN: 1=Operação tributável, 2=Imunidade, 3=Isenção, etc.
        $tribMun->appendChild($dom->createElementNS($ns, 'tribISSQN', '1'));

        // tpRetISSQN: 1=Não retido, 2=Retido tomador, 3=Retido intermediário
        $tpRetISSQN = !empty($servico['iss_retido']) ? '2' : '1';
        $tribMun->appendChild($dom->createElementNS($ns, 'tpRetISSQN', $tpRetISSQN));

        $aliquotaIss = floatval($servico['aliquota_iss'] ?? $tributacao['aliquota_iss'] ?? 5.00);
        $tribMun->appendChild($dom->createElementNS($ns, 'pAliq', number_format($aliquotaIss, 2, '.', '')));

        $trib->appendChild($tribMun);

        // totTrib
        $totTrib = $dom->createElementNS($ns, 'totTrib');
        // indTotTrib: 0=Não informado, 1=Informado
        $totTrib->appendChild($dom->createElementNS($ns, 'indTotTrib', '0'));
        $trib->appendChild($totTrib);

        $valores->appendChild($trib);

        return $valores;
    }

    private function criarIbscbs(DOMDocument $dom, string $ns, array $servico)
    {
        $valorServicos = floatval($servico['valor_servicos'] ?? 0);
        $valorDeducoes = floatval($servico['valor_deducoes'] ?? 0);
        $baseCalculo = $valorServicos - $valorDeducoes;
        if ($baseCalculo < 0) $baseCalculo = 0;

        $IBSCBS = $dom->createElementNS($ns, 'IBSCBS');

        $IBSCBS->appendChild($dom->createElementNS($ns, 'finNFSe', '0'));
        $IBSCBS->appendChild($dom->createElementNS($ns, 'indFinal', '0'));
        $IBSCBS->appendChild($dom->createElementNS($ns, 'cIndOp', '000000'));

        $valores = $dom->createElementNS($ns, 'valores');
        $valores->appendChild($dom->createElementNS($ns, 'vBC', number_format($baseCalculo, 2, '.', '')));

        $uf = $dom->createElementNS($ns, 'uf');
        $uf->appendChild($dom->createElementNS($ns, 'pIBSUF', '0.00'));
        $uf->appendChild($dom->createElementNS($ns, 'pAliqEfetUF', '0.00'));
        $valores->appendChild($uf);

        $mun = $dom->createElementNS($ns, 'mun');
        $mun->appendChild($dom->createElementNS($ns, 'pIBSMun', '0.00'));
        $mun->appendChild($dom->createElementNS($ns, 'pAliqEfetMun', '0.00'));
        $valores->appendChild($mun);

        $fed = $dom->createElementNS($ns, 'fed');
        $fed->appendChild($dom->createElementNS($ns, 'pCBS', '0.00'));
        $fed->appendChild($dom->createElementNS($ns, 'pAliqEfetCBS', '0.00'));
        $valores->appendChild($fed);

        $IBSCBS->appendChild($valores);

        return $IBSCBS;
    }

    /**
     * Escapa caracteres XML especiais
     */
    private function escapeXml($str)
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
