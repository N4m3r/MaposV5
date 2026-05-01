<?php
/**
 * Agente_ia_permissoes_model
 * Model para verificar permissoes do agente IA por perfil/numero.
 */

class Agente_ia_permissoes_model extends CI_Model
{
    protected string $permTable  = 'agente_ia_permissoes';
    protected string $vincTable  = 'whatsapp_integracao';

    public function __construct()
    {
        parent::__construct();
    }

    // ========================================================================
    // 1. VERIFICAR PERMISSAO CRUA (tabela permissoes)
    // ========================================================================

    /**
     * Verifica se um perfil tem permissao para uma acao.
     * Retorna array completo da permissao ou null.
     */
    public function verificarPermissao(string $perfil, string $acao): ?array
    {
        $row = $this->db
            ->where('perfil', $perfil)
            ->where('acao', $acao)
            ->where('ativo', 1)
            ->get($this->permTable)
            ->row_array();

        return $row ?: null;
    }

    // ========================================================================
    // 2. VERIFICACAO COMPLETA (numero -> acao)
    // ========================================================================

    /**
     * Verifica se o numero pode executar a acao agora.
     * Retorna array estruturado para API e n8n.
     */
    public function podeExecutar(string $numero, string $acao): array
    {
        // 1. Busca vinculo do numero
        $vinculo = $this->buscarVinculo($numero);

        if (!$vinculo) {
            return [
                'permitido'          => false,
                'motivo'             => 'Numero nao vinculado a nenhum cliente ou usuario.',
                'requer_autorizacao' => false,
                'perfil'             => null,
                'usuarios_id'        => null,
                'clientes_id'        => null,
                'requer_2fa'         => false
            ];
        }

        // 2. Identifica perfil
        $perfil = $this->identificarPerfil($vinculo);

        // 3. Verifica permissao da acao
        $permissao = $this->verificarPermissao($perfil, $acao);

        if (!$permissao) {
            return [
                'permitido'          => false,
                'motivo'             => "Acao '{$acao}' nao permitida para o perfil '{$perfil}'.",
                'requer_autorizacao' => false,
                'perfil'             => $perfil,
                'usuarios_id'        => $vinculo['usuarios_id'] ?? null,
                'clientes_id'        => $vinculo['clientes_id'] ?? null,
                'requer_2fa'         => false
            ];
        }

        // 4. Verifica horario permitido
        $horaAtual = date('H:i:s');
        if ($horaAtual < $permissao['horario_permitido_inicio'] || $horaAtual > $permissao['horario_permitido_fim']) {
            return [
                'permitido'          => false,
                'motivo'             => "Acao permitida apenas entre {$permissao['horario_permitido_inicio']} e {$permissao['horario_permitido_fim']}.",
                'requer_autorizacao' => false,
                'perfil'             => $perfil,
                'usuarios_id'        => $vinculo['usuarios_id'] ?? null,
                'clientes_id'        => $vinculo['clientes_id'] ?? null,
                'requer_2fa'         => false
            ];
        }

        // 5. Verifica nivel criticidade da acao
        $nivelAcao         = $this->getNivelAcao($acao);
        $requerAutorizacao = $nivelAcao > $permissao['nivel_maximo_automatico'];

        return [
            'permitido'          => true,
            'motivo'             => 'OK',
            'requer_autorizacao' => $requerAutorizacao,
            'perfil'             => $perfil,
            'usuarios_id'        => $vinculo['usuarios_id'] ?? null,
            'clientes_id'        => $vinculo['clientes_id'] ?? null,
            'requer_2fa'         => (bool) ($permissao['requer_2fa'] ?? 0),
            'nivel_acao'         => $nivelAcao,
            'nivel_maximo_auto'  => (int) $permissao['nivel_maximo_automatico']
        ];
    }

    // ========================================================================
    // 3. VINCULO DO NUMERO
    // ========================================================================

    /**
     * Busca vinculo na tabela whatsapp_integracao
     */
    public function buscarVinculo(string $numero): ?array
    {
        $row = $this->db
            ->where('numero_telefone', $numero)
            ->where('situacao', 1)
            ->get($this->vincTable)
            ->row_array();

        return $row ?: null;
    }

    // ========================================================================
    // 4. IDENTIFICACAO DE PERFIL
    // ========================================================================

    /**
     * Identifica o perfil com base no vinculo encontrado.
     */
    public function identificarPerfil(array $vinculo): string
    {
        // Se esta vinculado a um usuario interno, busca a permissao
        if (!empty($vinculo['usuarios_id'])) {
            $usuario = $this->db
                ->where('idUsuarios', (int)$vinculo['usuarios_id'])
                ->get('usuarios')
                ->row();

            if ($usuario && !empty($usuario->idPermissao)) {
                return $this->mapearPermissaoParaPerfil((int)$usuario->idPermissao);
            }

            return 'desconhecido';
        }

        // Se esta vinculado a um cliente
        if (!empty($vinculo['clientes_id'])) {
            return 'cliente';
        }

        return 'desconhecido';
    }

    /**
     * Mapeia idPermissao do MapOS para perfil do agente IA.
     */
    public function mapearPermissaoParaPerfil(int $idPermissao): string
    {
        $mapa = [
            1  => 'admin',        // Administrador
            2  => 'tecnico',      // Tecnico
            3  => 'financeiro',   // Financeiro
            4  => 'vendedor',     // Vendedor
            5  => 'cliente',      // Cliente (se existir)
            6  => 'cliente',      // Cliente secundario
        ];
        return $mapa[$idPermissao] ?? 'desconhecido';
    }

    // ========================================================================
    // 5. NIVEL DE CRITICIDADE POR ACAO
    // ========================================================================

    /**
     * Retorna o nivel de criticidade pre-definido para cada acao.
     */
    public function getNivelAcao(string $acao): int
    {
        $niveis = [
            // Nivel 1: Somente Leitura (sem autorizacao)
            'consultar_status_os'    => 1,
            'consultar_divida'       => 1,
            'consultar_cliente'      => 1,
            'consultar_estoque'      => 1,
            'consultar_minhas_os'    => 1,
            'consultar_lancamentos'  => 1,
            'gerar_relatorio'        => 1,

            // Nivel 2: Baixa (com rate limit, sem autorizacao)
            'enviar_lembrete'          => 2,
            'exportar_relatorio_pdf'   => 2,
            'solicitar_orcamento'      => 2,

            // Nivel 3: Media (requer autorizacao automatica se nao admin/tecnico)
            'criar_os'                => 3,
            'atualizar_status_os'     => 3,
            'agendar_atividade'       => 3,
            'registrar_atividade'     => 3,

            // Nivel 4: Alta (sempre requer confirmacao explicita)
            'aprovar_orcamento'       => 4,
            'gerar_cobranca'          => 4,
            'gerar_boleto'            => 4,
            'alterar_valor_os'        => 4,

            // Nivel 5: Critica (notifica admin e aguarda aprovacao)
            'excluir_os'              => 5,
            'alterar_lancamento_fin'  => 5,
            'emitir_nfse'             => 5,
        ];

        return $niveis[$acao] ?? 1;
    }

    // ========================================================================
    // 6. MANIPULACAO DE PERMISSOES (CRUD ADMIN)
    // ========================================================================

    /**
     * Lista todas as permissoes
     */
    public function listar(?string $perfil = null, ?string $acao = null): array
    {
        if ($perfil) {
            $this->db->where('perfil', $perfil);
        }
        if ($acao) {
            $this->db->where('acao', $acao);
        }
        return $this->db
            ->order_by('perfil', 'ASC')
            ->order_by('acao', 'ASC')
            ->get($this->permTable)
            ->result_array();
    }

    /**
     * Cria nova permissao
     */
    public function criarPermissao(array $dados): int
    {
        $this->db->insert($this->permTable, $dados);
        return $this->db->insert_id();
    }

    /**
     * Atualiza permissao
     */
    public function atualizarPermissao(int $id, array $dados): bool
    {
        $this->db->where('id', $id);
        return $this->db->update($this->permTable, $dados);
    }

    /**
     * Ativa/desativa permissao
     */
    public function toggleAtivo(int $id): bool
    {
        $row = $this->db->where('id', $id)->get($this->permTable)->row();
        if (!$row) {
            return false;
        }
        $this->db->where('id', $id);
        return $this->db->update($this->permTable, ['ativo' => $row->ativo ? 0 : 1]);
    }
}
