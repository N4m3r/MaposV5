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
        $this->versaoDps = $config['versao_dps'] ?? '1.01';
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

        // serie (5 posições com zeros, deve bater com o Id do infDPS)
        $serieXml = str_pad((string)$serie, 5, '0', STR_PAD_LEFT);
        $infDps->appendChild($dom->createElementNS($ns, 'serie', $serieXml));

        // nDPS (15 posições, deve bater com Id e começar com 1-9 para satisfazer TSNumDPS)
        $nDpsValor = $nDps ?? '1';
        $nDpsXml = str_pad((string)$nDpsValor, 15, '0', STR_PAD_LEFT);
        if ($nDpsXml[0] === '0') {
            $nDpsXml = '1' . substr($nDpsXml, 1);
        }
        $infDps->appendChild($dom->createElementNS($ns, 'nDPS', $nDpsXml));

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


        return $dom->saveXML();
    }

    private function criarPrestador(DOMDocument $dom, string $ns, array $prestador, array $tributacao)
    {
        $prest = $dom->createElementNS($ns, 'prest');

        $cnpj = preg_replace('/\D/', '', $prestador['cnpj'] ?? '');
        $cpf = preg_replace('/\D/', '', $prestador['cpf'] ?? '');
        $nif = preg_replace('/\D/', '', $prestador['nif'] ?? '');
        if (strlen($cnpj) === 14) {
            $prest->appendChild($dom->createElementNS($ns, 'CNPJ', $cnpj));
        } elseif (strlen($cpf) === 11) {
            $prest->appendChild($dom->createElementNS($ns, 'CPF', $cpf));
        } elseif (!empty($nif)) {
            $prest->appendChild($dom->createElementNS($ns, 'NIF', $nif));
        }

        // cNaoNIF é parte do choice com CNPJ/CPF/NIF — só enviar se NÃO houver CNPJ/CPF/NIF
        if (empty($cnpj) && empty($cpf) && empty($nif)) {
            $prest->appendChild($dom->createElementNS($ns, 'cNaoNIF', '1'));
        }

        $im = preg_replace('/\D/', '', $prestador['im'] ?? '');
        if (!empty($im)) {
            // Formatar IM com espaços à esquerda para 15 posições (conforme portal do contribuinte)
            $imFormatado = str_pad($im, 15, ' ', STR_PAD_LEFT);
            $prest->appendChild($dom->createElementNS($ns, 'IM', $imFormatado));
        }

        // xNome e endereco do prestador NAO sao incluidos no DPS pelo portal oficial
        // Fica apenas na NFSe raiz, nao no DPS envelopado

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
        // opSimpNac: código conforme cadastro do prestador no CNC NFS-e
        // 1=SN sublimite, 2=SN excesso, 3=MEI/EPP SN, 4=Fator r, 5=Fixo, 6=Anexo VI
        // O portal do contribuinte de Manaus emitiu com valor 3 para esta empresa
        $opSimpNac = $tributacao['op_simp_nac'] ?? '3';
        $regTrib->appendChild($dom->createElementNS($ns, 'opSimpNac', $opSimpNac));
        // regApTribSN: obrigatório para optantes pelo Simples Nacional
        // 1=Excesso sublimite, 2=Enquadramento Anexos, 3=Fixo, 4=Anexo VI
        $regApTribSN = $tributacao['reg_ap_trib_sn'] ?? '2';
        $regTrib->appendChild($dom->createElementNS($ns, 'regApTribSN', $regApTribSN));
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

        // cNaoNIF é parte do choice com CNPJ/CPF/NIF — só enviar se NÃO houver CNPJ/CPF/NIF
        if (empty($cpfCnpj)) {
            $toma->appendChild($dom->createElementNS($ns, 'cNaoNIF', '1'));
        }

        $xNome = !empty($tomador['razao_social']) ? $tomador['razao_social'] : 'TOMADOR';
        $toma->appendChild($dom->createElementNS($ns, 'xNome', $this->escapeXml($xNome)));

        if (!empty($tomador['endereco'])) {
            $toma->appendChild($this->criarEndereco($dom, $ns, $tomador['endereco']));
        }


        return $toma;
    }

    private function criarEndereco(DOMDocument $dom, string $ns, array $endereco)
    {
        $end = $dom->createElementNS($ns, 'end');

        $endNac = $dom->createElementNS($ns, 'endNac');
        $codMun = !empty($endereco['codigo_municipio']) ? $endereco['codigo_municipio'] : $this->codigoMunicipio;
        $endNac->appendChild($dom->createElementNS($ns, 'cMun', preg_replace('/\D/', '', $codMun)));
        if (!empty($endereco['cep'])) {
            $endNac->appendChild($dom->createElementNS($ns, 'CEP', preg_replace('/\D/', '', $endereco['cep'])));
        }
        $end->appendChild($endNac);

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

        return $end;
    }

    private function criarServico(DOMDocument $dom, string $ns, array $servico)
    {
        $serv = $dom->createElementNS($ns, 'serv');

        // Local de prestação (choice: cLocPrestacao para Brasil, cPaisPrestacao para exterior)
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


        $serv->appendChild($cServ);

        return $serv;
    }

    private function criarValores(DOMDocument $dom, string $ns, array $servico, array $tributacao)
    {
        $valores = $dom->createElementNS($ns, 'valores');

        $valorServicos = floatval($servico['valor_servicos'] ?? 0);

        // vServPrest (apenas vServ, conforme portal SEFIN)
        $vServPrest = $dom->createElementNS($ns, 'vServPrest');
        $vServPrest->appendChild($dom->createElementNS($ns, 'vServ', number_format($valorServicos, 2, '.', '')));
        $valores->appendChild($vServPrest);

        // Tributação municipal - tag trib (minúsculo conforme schema)
        $trib = $dom->createElementNS($ns, 'trib');
        $tribMun = $dom->createElementNS($ns, 'tribMun');

        // tribISSQN: 1=Operação tributável, 2=Imunidade, 3=Exportação de serviço, 4=Não incidência
        $tribISSQN = '1';
        $tribMun->appendChild($dom->createElementNS($ns, 'tribISSQN', $tribISSQN));

        // tpRetISSQN: 1=Não retido, 2=Retido tomador, 3=Retido intermediário
        $tpRetISSQN = !empty($servico['iss_retido']) ? '2' : '1';
        $tribMun->appendChild($dom->createElementNS($ns, 'tpRetISSQN', $tpRetISSQN));

        $trib->appendChild($tribMun);

        // Tributação federal (tribFed) - opcional, mas deve vir antes de totTrib
        $tribFed = $dom->createElementNS($ns, 'tribFed');

        // PIS/COFINS (conforme portal: apenas CST)
        $piscofins = $dom->createElementNS($ns, 'piscofins');
        $piscofins->appendChild($dom->createElementNS($ns, 'CST', '00'));
        $tribFed->appendChild($piscofins);

        $trib->appendChild($tribFed);

        // Totais aproximados (obrigatório)
        $totTrib = $dom->createElementNS($ns, 'totTrib');
        // Para optantes pelo Simples Nacional, usar pTotTribSN (percentual total tributos SN)
        // em vez de indTotTrib, conforme layout oficial SEFIN
        $optanteSimples = ($tributacao['optante_simples'] ?? true);
        if ($optanteSimples && !empty($tributacao['aliquota_nominal'])) {
            $totTrib->appendChild($dom->createElementNS($ns, 'pTotTribSN', number_format(floatval($tributacao['aliquota_nominal']), 2, '.', '')));
        } else {
            $totTrib->appendChild($dom->createElementNS($ns, 'indTotTrib', '0'));
        }
        $trib->appendChild($totTrib);

        $valores->appendChild($trib);

        return $valores;
    }


    /**
     * Escapa caracteres XML especiais
     */
    private function escapeXml($str)
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
