<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NFS-e Nacional: XMLDSIG Signer
 * Assina XML DPS com certificado digital ICP-Brasil (A1)
 * Implementa XMLDSIG Enveloped Signature com SHA-256 e RSA-SHA256
 */
class XmlSigner
{
    /**
     * Assina um XML DPS com XMLDSIG Enveloped Signature
     *
     * @param string $xml XML DPS sem assinatura
     * @param string $certPemPath Caminho para o arquivo PEM do certificado
     * @param string $keyPemPath Caminho para o arquivo PEM da chave privada
     * @return string|false XML assinado ou false em caso de erro
     */
    public function assinarXml($xml, $certPemPath, $keyPemPath)
    {
        if (!file_exists($certPemPath)) {
            log_message('error', 'XmlSigner: Certificado PEM não encontrado: ' . $certPemPath);
            return false;
        }
        if (!file_exists($keyPemPath)) {
            log_message('error', 'XmlSigner: Chave privada PEM não encontrada: ' . $keyPemPath);
            return false;
        }

        // Ler chave privada
        $privateKey = openssl_pkey_get_private(file_get_contents($keyPemPath));
        if (!$privateKey) {
            log_message('error', 'XmlSigner: Erro ao carregar chave privada: ' . openssl_error_string());
            return false;
        }

        // Ler certificado
        $certContent = file_get_contents($certPemPath);
        $certInfo = openssl_x509_parse($certContent);
        if (!$certInfo) {
            log_message('error', 'XmlSigner: Erro ao parsear certificado: ' . openssl_error_string());
            return false;
        }

        // Verificar se o certificado é válido
        if (isset($certInfo['validTo_time_t']) && $certInfo['validTo_time_t'] < time()) {
            log_message('error', 'XmlSigner: Certificado expirado em ' . date('Y-m-d', $certInfo['validTo_time_t']));
            return false;
        }

        // Carregar XML no DOMDocument
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;

        if (!$dom->loadXML($xml)) {
            log_message('error', 'XmlSigner: Erro ao carregar XML para assinatura');
            return false;
        }

        // Encontrar o elemento infDPS (alvo da assinatura conforme schema NFS-e Nacional)
        $infDps = null;
        $infDpsList = $dom->getElementsByTagNameNS('http://www.sped.fazenda.gov.br/nfse', 'infDPS');
        if ($infDpsList->length > 0) {
            $infDps = $infDpsList->item(0);
        } else {
            // Fallback: buscar sem namespace
            $infDpsList = $dom->getElementsByTagName('infDPS');
            if ($infDpsList->length > 0) {
                $infDps = $infDpsList->item(0);
            }
        }

        if (!$infDps) {
            log_message('error', 'XmlSigner: Elemento infDPS não encontrado no XML');
            return false;
        }

        $idDps = $infDps->getAttribute('Id');
        if (empty($idDps)) {
            $idDps = $infDps->getAttribute('id');
        }
        if (empty($idDps)) {
            log_message('error', 'XmlSigner: ID do infDPS não encontrado no XML');
            return false;
        }

        // Canonicalizar o infDPS (C14N não exclusivo) — deve corresponder ao Transform
        $canonicalXml = $infDps->C14N(false, false);

        // Calcular digest (SHA-256)
        $digest = base64_encode(hash('sha256', $canonicalXml, true));
        log_message('error', 'XmlSigner [DEBUG]: Digest infDPS tamanho=' . strlen($canonicalXml) . ' | DigestValue=' . $digest);

        $dsigNs = 'http://www.w3.org/2000/09/xmldsig#';

        // Criar elemento Signature
        $signature = $dom->createElementNS($dsigNs, 'Signature');

        // SignedInfo
        $signedInfo = $dom->createElementNS($dsigNs, 'SignedInfo');

        // CanonicalizationMethod
        $canonMethod = $dom->createElementNS($dsigNs, 'CanonicalizationMethod');
        $canonMethod->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->appendChild($canonMethod);

        // SignatureMethod (RSA-SHA256)
        $sigMethod = $dom->createElementNS($dsigNs, 'SignatureMethod');
        $sigMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
        $signedInfo->appendChild($sigMethod);

        // Reference
        $reference = $dom->createElementNS($dsigNs, 'Reference');
        $reference->setAttribute('URI', '#' . $idDps);

        // Transforms
        $transforms = $dom->createElementNS($dsigNs, 'Transforms');

        // Transform: Enveloped Signature
        $transform1 = $dom->createElementNS($dsigNs, 'Transform');
        $transform1->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform1);

        // Transform: C14N
        $transform2 = $dom->createElementNS($dsigNs, 'Transform');
        $transform2->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $transforms->appendChild($transform2);

        $reference->appendChild($transforms);

        // DigestMethod (SHA-256)
        $digestMethod = $dom->createElementNS($dsigNs, 'DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $reference->appendChild($digestMethod);

        // DigestValue
        $digestValue = $dom->createElementNS($dsigNs, 'DigestValue', $digest);
        $reference->appendChild($digestValue);

        $signedInfo->appendChild($reference);
        $signature->appendChild($signedInfo);

        // Inserir Signature como último filho do elemento raiz (Dps) ANTES de canonicalizar SignedInfo
        $rootElement = $dom->documentElement;
        if (!$rootElement) {
            log_message('error', 'XmlSigner: Elemento raiz não encontrado no XML');
            return false;
        }
        $rootElement->appendChild($signature);

        // Agora que SignedInfo está no DOM, canonicalizar para assinar
        $signedInfoCanonical = $signedInfo->C14N(false, false);
        log_message('error', 'XmlSigner [DEBUG]: SignedInfo canonical tamanho=' . strlen($signedInfoCanonical));

        if (empty($signedInfoCanonical)) {
            log_message('error', 'XmlSigner: SignedInfo canonicalizado está vazio. Tentando fallback com saveXML.');
            $signedInfoXml = $dom->saveXML($signedInfo);
            $tempDom = new DOMDocument('1.0', 'UTF-8');
            $tempDom->loadXML($signedInfoXml);
            $signedInfoCanonical = $tempDom->documentElement->C14N(false, false);
            log_message('error', 'XmlSigner [DEBUG]: Fallback SignedInfo canonical tamanho=' . strlen($signedInfoCanonical));
        }

        // Assinar com RSA-SHA256
        $signatureValue = '';
        if (!openssl_sign($signedInfoCanonical, $signatureValue, $privateKey, OPENSSL_ALGO_SHA256)) {
            log_message('error', 'XmlSigner: Erro ao assinar: ' . openssl_error_string());
            openssl_pkey_free($privateKey);
            return false;
        }
        log_message('error', 'XmlSigner [DEBUG]: SignatureValue tamanho=' . strlen($signatureValue) . ' | base64=' . base64_encode($signatureValue));

        // SignatureValue (DEVE vir antes do KeyInfo no schema XMLDSIG)
        $sigValue = $dom->createElementNS($dsigNs, 'SignatureValue', base64_encode($signatureValue));
        $signature->appendChild($sigValue);

        // KeyInfo (DEVE vir DEPOIS de SignatureValue no schema XMLDSIG)
        $keyInfo = $dom->createElementNS($dsigNs, 'KeyInfo');
        $x509Data = $dom->createElementNS($dsigNs, 'X509Data');
        $x509Cert = '';
        openssl_x509_export($certContent, $x509Cert);
        $x509Cert = str_replace('-----BEGIN CERTIFICATE-----', '', $x509Cert);
        $x509Cert = str_replace('-----END CERTIFICATE-----', '', $x509Cert);
        $x509Cert = preg_replace('/\s+/', '', $x509Cert);
        $x509CertElement = $dom->createElementNS($dsigNs, 'X509Certificate', $x509Cert);
        $x509Data->appendChild($x509CertElement);
        $keyInfo->appendChild($x509Data);
        $signature->appendChild($keyInfo);

        // Liberar recursos
        openssl_pkey_free($privateKey);

        return $dom->saveXML();
    }

    /**
     * Assina XML de evento de cancelamento
     * Mesma lógica de assinatura mas com Reference URI apontando para o ID do evento
     *
     * @param string $xml XML do evento sem assinatura
     * @param string $certPemPath Caminho para o certificado PEM
     * @param string $keyPemPath Caminho para a chave privada PEM
     * @return string|false XML assinado ou false em caso de erro
     */
    public function assinarEventoCancelamento($xml, $certPemPath, $keyPemPath)
    {
        // Mesma lógica de assinatura, apenas com ID diferente
        return $this->assinarXml($xml, $certPemPath, $keyPemPath);
    }
}