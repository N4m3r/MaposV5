<?php
/**
 * AutorizacoesController - API v2
 * Controle de autorizacoes para o agente IA com niveis de poder.
 *
 * Endpoints:
 * - POST   /api/v2/autorizacoes/verificar  -> Verifica se numero pode executar acao
 * - POST   /api/v2/autorizacoes/solicitar   -> Cria token de autorizacao
 * - POST   /api/v2/autorizacoes/validar     -> Valida token e executa acao
 * - GET    /api/v2/autorizacoes/listar      -> Lista autorizacoes pendentes
 * - POST   /api/v2/autorizacoes/responder   -> Admin responde pelo painel
 */

require_once APPPATH . 'controllers/api/v2/BaseController.php';

class AutorizacoesController extends BaseController
{
    protected Agente_ia_permissoes_model $permissoesModel;
    protected Agente_ia_autorizacoes_model $autorizacoesModel;

    public function __construct()
    {
        parent::__construct();

        // O agente/n8n usa token JWT ou apikey; permitir por apikey tambem
        $this->allowPublicAccess = false;

        $this->load->model('agente_ia_permissoes_model', 'permissoesModel');
        $this->load->model('agente_ia_autorizacoes_model', 'autorizacoesModel');
    }

    // ========================================================================
    // 1. VERIFICAR PERMISSAO
    // ========================================================================

    /**
     * POST /api/v2/autorizacoes/verificar
     *
     * Body:
     *   numero_telefone (string, obrigatorio)
     *   acao            (string, obrigatorio)
     *
     * Response:
     *   success: true/false
     *   data:
     *     permitido          -> bool
     *     motivo             -> string
     *     requer_autorizacao -> bool
     *     requer_2fa         -> bool
     *     perfil             -> string
     *     usuarios_id        -> int|null
     *     clientes_id        -> int|null
     */
    public function verificar_post()
    {
        $numero  = $this->input->post('numero_telefone');
        $acao    = $this->input->post('acao');
        $ip      = $this->input->ip_address();

        if (!$numero || !$acao) {
            return $this->error('Campos obrigatorios: numero_telefone, acao', 400);
        }

        // Normaliza numero
        $numero = $this->normalizarNumero($numero);

        $resultado = $this->permissoesModel->podeExecutar($numero, $acao);

        // Registra log de verificacao (assincrono, nao bloqueia)
        $this->autorizacoesModel->logVerificacao([
            'numero_telefone' => $numero,
            'acao'             => $acao,
            'permitido'        => $resultado['permitido'],
            'motivo'           => $resultado['motivo'],
            'ip'               => $ip
        ]);

        return $this->success($resultado);
    }

    // ========================================================================
    // 2. SOLICITAR AUTORIZACAO (CRIAR TOKEN)
    // ========================================================================

    /**
     * POST /api/v2/autorizacoes/solicitar
     *
     * Body:
     *   numero_telefone (string, obrigatorio)
     *   acao            (string, obrigatorio)
     *   dados_json      (json, obrigatorio) - parametros da acao
     *   metodo          (string, opcional) - whatsapp|email|painel default whatsapp
     *   minutos_expira  (int, opcional) default 15
     *
     * Response:
     *   success: true
     *   data:
     *     token         -> string
     *     expires_at    -> datetime
     *     mensagem      -> string
     */
    public function solicitar_post()
    {
        $numero        = $this->input->post('numero_telefone');
        $acao          = $this->input->post('acao');
        $dadosJson     = $this->input->post('dados_json');
        $metodo        = $this->input->post('metodo') ?: 'whatsapp';
        $minutosExpira = (int) ($this->input->post('minutos_expira') ?: 15);
        $ip            = $this->input->ip_address();

        if (!$numero || !$acao || !$dadosJson) {
            return $this->error('Campos obrigatorios: numero_telefone, acao, dados_json', 400);
        }

        $numero = $this->normalizarNumero($numero);

        // Descobre perfil/usuario do numero
        $vinculo = $this->permissoesModel->buscarVinculo($numero);
        if (!$vinculo) {
            return $this->error('Numero nao vinculado a nenhum cliente ou usuario.', 403);
        }

        // Gera token
        $token = $this->gerarToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$minutosExpira} minutes"));

        $autId = $this->autorizacoesModel->criar([
            'token'             => $token,
            'numero_telefone'   => $numero,
            'usuarios_id'       => $vinculo['usuarios_id'] ?? null,
            'clientes_id'       => $vinculo['clientes_id'] ?? null,
            'acao'              => $acao,
            'dados_json'        => is_string($dadosJson) ? $dadosJson : json_encode($dadosJson),
            'nivel_criticidade' => $this->permissoesModel->getNivelAcao($acao),
            'status'            => 'pendente',
            'metodo_autorizacao'=> $metodo,
            'expires_at'        => $expiresAt,
            'ip_autorizacao'    => $ip,
            'executado_por'     => 'agente_ia'
        ]);

        if (!$autId) {
            return $this->error('Erro ao criar token de autorizacao.', 500);
        }

        // Monta mensagem de resposta
        $msg = sprintf(
            "Voce esta solicitando: *%s*.\n\n" .
            "Para confirmar, responda com o codigo: *%s*\n" .
            "O codigo expira em %d minutos.",
            $this->nomeAmigavelAcao($acao),
            $token,
            $minutosExpira
        );

        return $this->success([
            'token'      => $token,
            'expires_at' => $expiresAt,
            'aut_id'     => $autId,
            'mensagem'   => $msg
        ]);
    }

    // ========================================================================
    // 3. VALIDAR AUTORIZACAO (CONFIRMAR TOKEN)
    // ========================================================================

    /**
     * POST /api/v2/autorizacoes/validar
     *
     * Body:
     *   numero_telefone (string, obrigatorio)
     *   token           (string, obrigatorio) ou resposta_usuario
     *   resposta_usuario (string, opcional) texto digitado pelo usuario
     *
     * Response:
     *   success: true/false
     *   data:
     *     status         -> aprovada|rejeitada|expirada|nao_encontrada
     *     autorizacao_id -> int
     *     acao           -> string
     *     dados_json     -> json (parametros prontos para execucao)
     *     executar       -> bool
     */
    public function validar_post()
    {
        $numero   = $this->input->post('numero_telefone');
        $token    = $this->input->post('token');
        $resposta = $this->input->post('resposta_usuario');
        $ip       = $this->input->ip_address();

        if (!$numero || (!$token && !$resposta)) {
            return $this->error('Campos obrigatorios: numero_telefone + (token ou resposta_usuario)', 400);
        }

        $numero = $this->normalizarNumero($numero);

        // Suporta token direto ou extrai da resposta do usuario
        $tokenBusca = $token ?: $this->extrairTokenDaResposta($resposta);

        if (!$tokenBusca) {
            return $this->success([
                'status'     => 'nao_encontrada',
                'motivo'     => 'Token nao encontrado na mensagem. Envie o codigo de confirmacao.',
                'executar'   => false
            ]);
        }

        $autorizacao = $this->autorizacoesModel->buscarPorToken($tokenBusca);

        if (!$autorizacao) {
            return $this->success([
                'status'     => 'nao_encontrada',
                'motivo'     => 'Token invalido.',
                'executar'   => false
            ]);
        }

        // Verifica se token pertence ao numero
        if ($this->normalizarNumero($autorizacao['numero_telefone']) !== $numero) {
            return $this->success([
                'status'     => 'rejeitada',
                'motivo'     => 'Token nao corresponde a este numero.',
                'executar'   => false
            ]);
        }

        // Verifica expiracao
        if (strtotime($autorizacao['expires_at']) < time()) {
            $this->autorizacoesModel->atualizarStatus($autorizacao['id'], 'expirada');
            return $this->success([
                'status'          => 'expirada',
                'motivo'          => 'Token expirado. Solicite nova autorizacao.',
                'executar'        => false,
                'autorizacao_id'  => $autorizacao['id']
            ]);
        }

        // Ja executada?
        if (in_array($autorizacao['status'], ['executada', 'aprovada'])) {
            return $this->success([
                'status'          => $autorizacao['status'],
                'motivo'          => 'Esta autorizacao ja foi utilizada.',
                'executar'        => false,
                'autorizacao_id'  => $autorizacao['id']
            ]);
        }

        // Rejeitada explicitamente pelo usuario?
        if ($this->ehRejeicao($resposta)) {
            $this->autorizacoesModel->atualizarStatus($autorizacao['id'], 'rejeitada', [
                'resposta_usuario' => $resposta,
                'ip_autorizacao'   => $ip
            ]);
            return $this->success([
                'status'          => 'rejeitada',
                'motivo'          => 'Autorizacao negada pelo usuario.',
                'executar'        => false,
                'autorizacao_id'  => $autorizacao['id']
            ]);
        }

        // Aprova
        $this->autorizacoesModel->atualizarStatus($autorizacao['id'], 'aprovada', [
            'resposta_usuario' => $resposta ?: $tokenBusca,
            'ip_autorizacao'   => $ip,
            'executed_at'      => date('Y-m-d H:i:s')
        ]);

        return $this->success([
            'status'           => 'aprovada',
            'autorizacao_id'   => $autorizacao['id'],
            'acao'             => $autorizacao['acao'],
            'dados_json'       => json_decode($autorizacao['dados_json'], true),
            'executar'         => true,
            'motivo'           => 'Autorizacao confirmada. Prossiga com a execucao.'
        ]);
    }

    // ========================================================================
    // 4. LISTAR AUTORIZACOES (PAINEL ADMIN)
    // ========================================================================

    /**
     * GET /api/v2/autorizacoes/listar
     *
     * Query:
     *   status   (string, opcional) pendente|aprovada|rejeitada|expirada|executada
     *   numero   (string, opcional)
     *   page     (int, opcional) default 1
     *   per_page (int, opcional) default 20
     *
     * Response: paginado
     */
    public function listar_get()
    {
        $status   = $this->input->get('status');
        $numero   = $this->input->get('numero');
        $page     = (int) ($this->input->get('page') ?: 1);
        $perPage  = (int) ($this->input->get('per_page') ?: 20);

        $filtros = array_filter([
            'status' => $status,
            'numero' => $numero ? $this->normalizarNumero($numero) : null
        ]);

        $resultado = $this->autorizacoesModel->listar($filtros, $page, $perPage);

        return $this->success([
            'items'        => $resultado['items'],
            'total'        => $resultado['total'],
            'page'         => $page,
            'per_page'     => $perPage,
            'total_pages'  => (int) ceil($resultado['total'] / $perPage)
        ]);
    }

    // ========================================================================
    // 5. RESPONDER PELO PAINEL (ADMIN)
    // ========================================================================

    /**
     * POST /api/v2/autorizacoes/responder
     *
     * Body:
     *   autorizacao_id (int, obrigatorio)
     *   resposta       (string, obrigatorio) aprovar|rejeitar
     *   observacoes    (string, opcional)
     *
     * Response:
     *   success: true/false
     */
    public function responder_post()
    {
        $id       = (int) $this->input->post('autorizacao_id');
        $resposta = strtolower(trim($this->input->post('resposta') ?: ''));
        $obs      = $this->input->post('observacoes');
        $ip       = $this->input->ip_address();

        if (!$id || !in_array($resposta, ['aprovar', 'rejeitar'])) {
            return $this->error('Campos obrigatorios: autorizacao_id, resposta (aprovar|rejeitar)', 400);
        }

        $aut = $this->autorizacoesModel->buscarPorId($id);
        if (!$aut) {
            return $this->error('Autorizacao nao encontrada.', 404);
        }

        $novoStatus = $resposta === 'aprovar' ? 'aprovada' : 'rejeitada';

        $this->autorizacoesModel->atualizarStatus($id, $novoStatus, [
            'resposta_usuario' => $obs ?: 'Respondido pelo painel admin',
            'ip_autorizacao'   => $ip,
            'executed_at'      => date('Y-m-d H:i:s'),
            'executado_por'    => 'usuario',
            'observacoes'      => $obs
        ]);

        return $this->success([
            'autorizacao_id' => $id,
            'status'         => $novoStatus,
            'acao'           => $aut['acao'],
            'mensagem'       => 'Autorizacao ' . ($novoStatus === 'aprovada' ? 'aprovada' : 'rejeitada') . ' com sucesso.'
        ]);
    }

    // ========================================================================
    // UTILITARIOS
    // ========================================================================

    /**
     * Normaliza numero de telefone para comparacao
     */
    private function normalizarNumero(string $numero): string
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        return $numero;
    }

    /**
     * Gera token unico no formato AUTH-XXXXXXX
     */
    private function gerarToken(): string
    {
        return 'AUTH-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Extrai token AUTH-xxxx de uma mensagem do usuario
     */
    private function extrairTokenDaResposta(?string $texto): ?string
    {
        if (!$texto) {
            return null;
        }
        if (preg_match('/AUTH-[A-F0-9]{8}/i', $texto, $m)) {
            return strtoupper($m[0]);
        }
        return null;
    }

    /**
     * Detecta se a mensagem eh uma rejeicao explicita
     */
    private function ehRejeicao(?string $texto): bool
    {
        if (!$texto) {
            return false;
        }
        $negativas = ['nao', 'não', 'cancelar', 'recusar', 'rejeitar', 'nope', 'negativo'];
        $lower = mb_strtolower(trim($texto));
        foreach ($negativas as $neg) {
            if (strpos($lower, $neg) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Nome amigavel para acao na mensagem ao usuario
     */
    private function nomeAmigavelAcao(string $acao): string
    {
        $nomes = [
            'criar_os'          => 'Criar Ordem de Servico',
            'aprovar_orcamento' => 'Aprovar Orcamento',
            'gerar_cobranca'    => 'Gerar Cobranca',
            'gerar_boleto'      => 'Gerar Boleto',
            'emitir_nfse'       => 'Emitir Nota Fiscal de Servico',
            'atualizar_status_os' => 'Atualizar Status da OS',
            'registrar_atividade' => 'Registrar Atividade',
            'excluir_os'        => 'Excluir Ordem de Servico',
            'solicitar_orcamento' => 'Solicitar Orcamento'
        ];
        return $nomes[$acao] ?? ucwords(str_replace('_', ' ', $acao));
    }
}
