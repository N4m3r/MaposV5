<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NFS-e Nacional: Configuração
 * Constantes e utilitários para o Sistema Nacional NFS-e
 */
class NfseConfig
{
    // Versão do DPS
    const VERSAO_DPS = '1.00';

    // Natureza de Operação
    const NATUREZA_TRIBUTACAO_MUNICIPIO = '1';
    const NATUREZA_TRIBUTACAO_FORA = '2';
    const NATUREZA_ISENCAO = '3';
    const NATUREZA_IMUNIDADE = '4';
    const NATUREZA_SUSPENSAO_JUDICIAL = '5';
    const NATUREZA_SUSPENSAO_ADMIN = '6';

    // Simples Nacional
    const OPTANTE_SIM = 1;
    const OPTANTE_NAO = 2;

    // Regime Especial Tributação
    const REGIME_NENHUM = '0';
    const REGIME_MICROEMPRESA = '1';
    const REGIME_ESTIMATIVA = '2';
    const REGIME_SOCIEDADE_PROFISSIONAL = '3';
    const REGIME_COOPERATIVA = '4';

    // Incentivador Cultural
    const INCENTIVADOR_SIM = '1';
    const INCENTIVADOR_NAO = '0';

    // Situação NFS-e Nacional
    const SITUACAO_NORMAL = '1';
    const SITUACAO_CANCELADA = '2';
    const SITUACAO_SUBSTITUIDA = '3';

    // Código Município Manaus (IBGE)
    const CODIGO_MUNICIPIO_MANAUS = '1302603';
    const CODIGO_UF_AM = '13';

    /**
     * Retorna URL base da API conforme ambiente
     */
    public static function getBaseUrl($ambiente = 'homologacao')
    {
        // SEFIN Nacional = endpoint de emissao
        // ADN = endpoint de distribuicao/consulta
        $urls = [
            'homologacao' => 'https://sefin.producaorestrita.nfse.gov.br/SefinNacional/',
            'producao'    => 'https://sefin.nfse.gov.br/SefinNacional/',
        ];
        return $urls[$ambiente] ?? $urls['homologacao'];
    }

    /**
     * Gera ID único para DPS (infDPS)
     * Formato oficial NFS-e Nacional: DPS + CodMun(7) + TpInsc(1) + InscFed(14) + Serie(5) + nDPS(15) = 45 posições
     *
     * @param string $cnpj CNPJ do prestador (somente números ou formatado)
     * @param string $codMun Código IBGE do município (7 dígitos)
     * @param int|string $serie Série da DPS (padrão 1)
     * @param int|string $nDPS Número da DPS (deve ser único por prestador+serie)
     * @return string Id no formato oficial
     */
    public static function gerarIdDps($cnpj, $codMun = '1302603', $serie = 1, $nDPS = null)
    {
        $inscFed = preg_replace('/\D/', '', $cnpj);
        $inscFed = str_pad($inscFed, 14, '0', STR_PAD_LEFT);
        $codMun = str_pad(preg_replace('/\D/', '', $codMun), 7, '0', STR_PAD_LEFT);
        $serie = (string)$serie;
        if ($nDPS === null) {
            // Gerar nDPS único de 15 dígitos sem notação científica
            // time() = 10 dígitos + random de 5 dígitos = 15 dígitos
            $nDPS = time() . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        }
        // nDPS e serie no Id devem ter tamanho fixo: serie=5, nDPS=15
        // para satisfazer o pattern TSIdDPS de 45 posições.
        $serieId = str_pad($serie, 5, '0', STR_PAD_LEFT);
        $nDPSId  = str_pad((string)$nDPS, 15, '0', STR_PAD_LEFT);
        $nDPSId  = substr($nDPSId, -15); // Garante exatamente 15 dígitos, trunca se necessário
        // Tipo de Inscrição Federal: 1=CPF, 2=CNPJ
        $tpInsc = (strlen(preg_replace('/\D/', '', $cnpj)) === 11) ? '1' : '2';
        return 'DPS' . $codMun . $tpInsc . $inscFed . $serieId . $nDPSId;
    }

    /**
     * Gera número sequencial para NFS-e
     */
    public static function gerarNumeroNfse($ultimoNumero = 0)
    {
        return $ultimoNumero + 1;
    }

    /**
     * Formata CNPJ para o padrão nacional (somente números, 14 dígitos)
     */
    public static function formatarCnpj($cnpj)
    {
        return str_pad(preg_replace('/\D/', '', $cnpj), 14, '0', STR_PAD_LEFT);
    }

    /**
     * Formata CPF para o padrão nacional (somente números, 11 dígitos)
     */
    public static function formatarCpf($cpf)
    {
        return str_pad(preg_replace('/\D/', '', $cpf), 11, '0', STR_PAD_LEFT);
    }
}