<?php

class Permissoes extends Seeder
{
    private $table = 'permissoes';

    public function run()
    {
        echo 'Running Permissoes Seeder';

        // Permissões do Administrador com DRE e outras permissões novas
        $permissoes = [
            'aCliente' => '1', 'eCliente' => '1', 'dCliente' => '1', 'vCliente' => '1',
            'aProduto' => '1', 'eProduto' => '1', 'dProduto' => '1', 'vProduto' => '1',
            'aServico' => '1', 'eServico' => '1', 'dServico' => '1', 'vServico' => '1',
            'aOs' => '1', 'eOs' => '1', 'dOs' => '1', 'vOs' => '1',
            'aVenda' => '1', 'eVenda' => '1', 'dVenda' => '1', 'vVenda' => '1',
            'aGarantia' => '1', 'eGarantia' => '1', 'dGarantia' => '1', 'vGarantia' => '1',
            'aArquivo' => '1', 'eArquivo' => '1', 'dArquivo' => '1', 'vArquivo' => '1',
            'aPagamento' => '1', 'ePagamento' => '1', 'dPagamento' => '1', 'vPagamento' => '1',
            'aLancamento' => '1', 'eLancamento' => '1', 'dLancamento' => '1', 'vLancamento' => '1',
            'cUsuario' => '1', 'cEmitente' => '1', 'cPermissao' => '1', 'cBackup' => '1',
            'cAuditoria' => '1', 'cEmail' => '1', 'cSistema' => '1',
            'rCliente' => '1', 'rProduto' => '1', 'rServico' => '1', 'rOs' => '1',
            'rVenda' => '1', 'rFinanceiro' => '1',
            'aCobranca' => '1', 'eCobranca' => '1', 'dCobranca' => '1', 'vCobranca' => '1',
            // Permissões Dashboard
            'vDashboard' => '1', 'vRelatorioCompleto' => '1', 'vExportarDados' => '1',
            // Permissões DRE
            'vDRE' => '1', 'cDRE' => '1', 'eDRE' => '1', 'dDRE' => '1',
            // Permissões Certificado
            'vCertificado' => '1', 'cCertificado' => '1', 'eCertificado' => '1', 'dCertificado' => '1',
            // Permissões Impostos
            'vImpostos' => '1', 'cImpostos' => '1', 'eImpostos' => '1', 'dImpostos' => '1',
            'cImpostosConfig' => '1', 'vImpostosRelatorio' => '1', 'vImpostosExportar' => '1',
            // Outras permissões
            'vRelatorioAtendimentos' => '1', 'vWebhooks' => '1', 'vRelatorioTecnicos' => '1',
            'vUsuariosCliente' => '1', 'cUsuariosCliente' => '1', 'eUsuariosCliente' => '1', 'dUsuariosCliente' => '1', 'cPermUsuariosCliente' => '1',
            'vBtnAtendimento' => '1', 'vTecnicoOS' => '1', 'eTecnicoCheckin' => '1', 'eTecnicoCheckout' => '1', 'eTecnicoFotos' => '1',
            'cDocOs' => '1',
        ];

        $data = [
            'idPermissao' => 1,
            'nome' => 'Administrador',
            'permissoes' => serialize($permissoes),
            'situacao' => 1,
            'data' => date('Y-m-d'),
        ];
        $this->db->insert($this->table, $data);

        echo PHP_EOL;
    }
}
