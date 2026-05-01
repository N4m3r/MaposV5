<?php
/**
 * AcoesController - API v2
 * Executa acoes no MapOS apos validacao do agente IA.
 *
 * Endpoint:
 *   POST /api/v2/acoes/executar
 *
 * Body:
 *   acao            (string) obrigatorio: criar_os | aprovar_orcamento | atualizar_status_os |
 *                                 gerar_cobranca | gerar_boleto | excluir_os |
 *                                 registrar_atividade | emitir_nfse
 *   dados           (json)   parametros da acao
 *   token_autorizacao (string) token previamente aprovado (opcional mas exigido para nivel 4+)
 *
 * Retorno:
 *   success: true
 *   data: { resultado da acao }
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class AcoesController extends BaseController
{
    protected Agente_ia_autorizacoes_model $autModel;
    protected Agente_ia_permissoes_model   $permModel;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('agente_ia_autorizacoes_model', 'autModel');
        $this->load->model('agente_ia_permissoes_model',   'permModel');
    }

    // ========================================================================
    // EXECUTAR ACAO
    // ========================================================================

    /**
     * POST /api/v2/acoes/executar
     */
    public function executar_post()
    {
        $acao     = $this->input->post('acao');
        $dados    = $this->input->post('dados');
        $tokenAut = $this->input->post('token_autorizacao');
        $numero   = $this->input->post('numero_telefone');
        $ip       = $this->input->ip_address();

        if (!$acao || !$dados) {
            return $this->error('Campos obrigatorios: acao, dados', 400);
        }

        // Normaliza dados
        $dados    = is_string($dados) ? json_decode($dados, true) : $dados;
        if (!is_array($dados)) { return $this->error('Dados deve ser JSON valido.', 400); }

        $numero = $this->normalizarNumero($numero ?: ($dados['numero_telefone'] ?? ''));

        // Verifica se acao existe
        $nivel = $this->permModel->getNivelAcao($acao);
        $metodosValidos = [
            'criar_os','aprovar_orcamento','atualizar_status_os',
            'registrar_atividade','gerar_cobranca','gerar_boleto',
            'excluir_os','emitir_nfse'
        ];
        if (!in_array($acao, $metodosValidos)) {
            return $this->error('Acao invalida.', 400, ['acoes_validas' => $metodosValidos]);
        }

        // Se nivel critico, exige token de autorizacao valido
        if ($nivel >= 4) {
            if (!$tokenAut) {
                return $this->error('Esta acao requer token de autorizacao.', 403);
            }
            $aut = $this->autModel->buscarPorToken($tokenAut);
            if (!$aut || $aut['status'] !== 'aprovada') {
                return $this->error('Token nao aprovado ou invalido.', 403);
            }
            if ($aut['acao'] !== $acao) {
                return $this->error('Token nao corresponde a esta acao.', 403);
            }
            // Verifica expiracao
            if (strtotime($aut['expires_at']) < time()) {
                $this->autModel->atualizarStatus($aut['id'], 'expirada');
                return $this->error('Token expirado.', 403);
            }
        }

        // Executa
        $resultado = null;
        $msg       = '';

        try {
            switch ($acao) {
                case 'criar_os':
                    $resultado = $this->executarCriarOS($dados);
                    break;
                case 'aprovar_orcamento':
                    $resultado = $this->executarAprovarOrcamento($dados);
                    break;
                case 'atualizar_status_os':
                    $resultado = $this->executarAtualizarStatusOS($dados);
                    break;
                case 'registrar_atividade':
                    $resultado = $this->executarRegistrarAtividade($dados);
                    break;
                case 'gerar_cobranca':
                    $resultado = $this->executarGerarCobranca($dados);
                    break;
                case 'gerar_boleto':
                    $resultado = $this->executarGerarBoleto($dados);
                    break;
                case 'excluir_os':
                    $resultado = $this->executarExcluirOS($dados);
                    break;
                case 'emitir_nfse':
                    $resultado = $this->executarEmitirNFSe($dados);
                    break;
            }
        } catch (\Throwable $e) {
            log_message('error', '[AcoesController] Erro executando acao ' . $acao . ': ' . $e->getMessage());
            return $this->error('Erro ao executar acao: ' . $e->getMessage(), 500);
        }

        // Marca token como executado se houver
        if (!empty($aut)) {
            $this->autModel->marcarExecutada($aut['id'], [
                'resultado'   => $resultado,
                'executado_em'=> date('Y-m-d H:i:s'),
                'ip_executado' => $ip
            ]);
        }

        return $this->success([
            'acao_executada' => $acao,
            'nivel'          => $nivel,
            'resultado'      => $resultado
        ]);
    }

    // ========================================================================
    // EXECUTORES INDIVIDUAIS
    // ========================================================================

    private function executarCriarOS(array $dados): array
    {
        $clienteId = (int) ($dados['clientes_id'] ?? 0);
        if (!$clienteId) {
            throw new \RuntimeException('clientes_id obrigatorio.');
        }

        $osData = [
            'dataInicial'      => date('Y-m-d'),
            'dataFinal'        => $dados['dataFinal'] ?? date('Y-m-d', strtotime('+3 days')),
            'clientes_id'      => $clienteId,
            'usuarios_id'      => $dados['usuarios_id'] ?? 1, // default admin
            'descricaoProduto' => $dados['descricaoProduto'] ?? ($dados['equipamento'] ?? 'Nao especificado'),
            'defeito'          => $dados['defeito'] ?? ($dados['servico'] ?? ''),
            'status'           => $dados['status'] ?? 'Aberto',
            'observacoes'      => $dados['observacoes'] ?? '',
            'laudoTecnico'     => $dados['laudoTecnico'] ?? '',
            'garantia'         => $dados['garantia'] ?? '',
            'faturado'         => 0,
        ];

        $this->load->model('Os_model');
        $id = $this->Os_model->add('os', $osData, true);

        if (!is_numeric($id)) {
            throw new \RuntimeException('Falha ao criar OS.');
        }

        return [
            'os_id'    => (int)$id,
            'status'   => 'criada',
            'mensagem' => 'OS #' . $id . ' criada com sucesso.'
        ];
    }

    private function executarAprovarOrcamento(array $dados): array
    {
        $osId = (int) ($dados['os_id'] ?? 0);
        $valor = (float) ($dados['valor'] ?? 0);
        if (!$osId) throw new \RuntimeException('os_id obrigatorio.');

        $this->load->model('Os_model');
        $this->Os_model->edit('os', [
            'status'     => 'Aprovado',
            'valorTotal' => $valor > 0 ? $valor : null,
        ], 'idOs', $osId);

        return [
            'os_id'    => $osId,
            'status'   => 'aprovado',
            'mensagem' => 'Orcamento da OS #' . $osId . ' aprovado.'
        ];
    }

    private function executarAtualizarStatusOS(array $dados): array
    {
        $osId  = (int) ($dados['os_id'] ?? 0);
        $novo  = $dados['novo_status'] ?? '';
        $novo  = $dados['status'] ?? $novo;
        if (!$osId || !$novo) throw new \RuntimeException('os_id e status obrigatorios.');

        $statusValidos = ['Aberto','Orçamento','Aprovado','Faturado','Finalizado','Cancelado','Aguardando Peças'];
        if (!in_array($novo, $statusValidos)) {
            throw new \RuntimeException('Status invalido: ' . $novo);
        }

        $this->load->model('Os_model');
        $this->Os_model->edit('os', ['status' => $novo], 'idOs', $osId);

        return [
            'os_id'    => $osId,
            'status'   => $novo,
            'mensagem' => 'Status da OS #' . $osId . ' alterado para: ' . $novo
        ];
    }

    private function executarRegistrarAtividade(array $dados): array
    {
        $osId  = (int) ($dados['os_id'] ?? 0);
        $desc  = $dados['descricao'] ?? ($dados['atividade'] ?? '');
        $tecnico = (int) ($dados['usuarios_id'] ?? ($dados['tecnico_id'] ?? 1));
        if (!$osId || !$desc) throw new \RuntimeException('os_id e descricao obrigatorios.');

        $this->db->insert('atividades', [
            'os_id'        => $osId,
            'usuarios_id'  => $tecnico,
            'descricao'    => $desc,
            'data_inicio'  => date('Y-m-d H:i:s'),
            'data_criacao' => date('Y-m-d H:i:s'),
            'status'       => 'Concluida',
        ]);
        $id = $this->db->insert_id();

        return [
            'atividade_id' => (int)$id,
            'os_id'        => $osId,
            'mensagem'     => 'Atividade registrada na OS #' . $osId
        ];
    }

    private function executarGerarCobranca(array $dados): array
    {
        $clienteId = (int) ($dados['clientes_id'] ?? 0);
        $valor     = (float) ($dados['valor'] ?? 0);
        $descricao = $dados['descricao'] ?? 'Cobranca gerada via agente IA';
        $tipo      = $dados['tipo'] ?? 'receita';
        if (!$clienteId || $valor <= 0) throw new \RuntimeException('clientes_id e valor obrigatorios.');

        $vencimento = $dados['data_vencimento'] ?? date('Y-m-d', strtotime('+3 days'));

        $this->db->insert('lancamentos', [
            'descricao'        => $descricao,
            'valor'            => $valor,
            'data_vencimento'  => $vencimento,
            'data_pagamento'   => null,
            'baixado'          => 0,
            'clientes_id'      => $clienteId,
            'forma_pgto'       => $dados['forma_pgto'] ?? '',
            'tipo'             => $tipo,
            'observacoes'      => $dados['observacoes'] ?? '',
            'usuarios_id'      => $dados['usuarios_id'] ?? 1,
            'created_at'       => date('Y-m-d H:i:s'),
        ]);
        $id = $this->db->insert_id();

        return [
            'lancamento_id' => (int)$id,
            'cliente_id'    => $clienteId,
            'valor'         => $valor,
            'mensagem'      => 'Cobranca #' . $id . ' gerada: R$ ' . number_format($valor, 2, ',', '.')
        ];
    }

    private function executarGerarBoleto(array $dados): array
    {
        // Reutiliza gerar_cobranca + marca como boleto
        $result = $this->executarGerarCobranca($dados);
        $this->db->where('idLancamentos', $result['lancamento_id']);
        $this->db->update('lancamentos', ['forma_pgto' => 'Boleto']);
        return array_merge($result, ['mensagem' => 'Boleto #' . $result['lancamento_id'] . ' gerado.']);
    }

    private function executarExcluirOS(array $dados): array
    {
        $osId = (int) ($dados['os_id'] ?? 0);
        if (!$osId) throw new \RuntimeException('os_id obrigatorio.');

        $this->load->model('Os_model');
        // Soft delete ou hard delete conforme implementacao do MapOS
        $this->Os_model->delete('os', 'idOs', $osId);

        return [
            'os_id'    => $osId,
            'status'   => 'excluida',
            'mensagem' => 'OS #' . $osId . ' excluida.'
        ];
    }

    private function executarEmitirNFSe(array $dados): array
    {
        $osId = (int) ($dados['os_id'] ?? 0);
        if (!$osId) throw new \RuntimeException('os_id obrigatorio para NFSe.');

        $this->load->model('Nfse_emitida_model');
        // Delega para o controller existente de NFSe ou chama o model
        // Simplificacao: retorna instrucao
        return [
            'os_id'    => $osId,
            'status'   => 'delegado',
            'mensagem' => 'NFSe delegada ao modulo de emissao. Acesse /nfse_os/emitir/' . $osId
        ];
    }

    // ========================================================================
    // UTIL
    // ========================================================================

    private function normalizarNumero(string $numero): string
    {
        return preg_replace('/[^0-9]/', '', $numero);
    }
}
