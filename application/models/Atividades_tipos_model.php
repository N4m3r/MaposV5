<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model para gerenciamento de Tipos de Atividades
 */
class Atividades_tipos_model extends CI_Model
{
    protected $table = 'atividades_tipos';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lista todos os tipos
     */
    public function listar($filtros = [], $apenasAtivos = true)
    {
        if ($apenasAtivos) {
            $this->db->where('ativo', 1);
        }

        if (isset($filtros['categoria'])) {
            $this->db->where('categoria', $filtros['categoria']);
        }

        if (isset($filtros['requer_material'])) {
            $this->db->where('requer_material', $filtros['requer_material']);
        }

        if (isset($filtros['requer_foto'])) {
            $this->db->where('requer_foto', $filtros['requer_foto']);
        }

        $this->db->order_by('ordem', 'ASC');
        $this->db->order_by('nome', 'ASC');

        return $this->db->get($this->table)->result();
    }

    /**
     * Obtém tipo por ID
     */
    public function getById($id)
    {
        $this->db->where('idTipo', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Salva um tipo (insert ou update)
     */
    public function salvar($dados)
    {
        if (isset($dados['idTipo'])) {
            $this->db->where('idTipo', $dados['idTipo']);
            unset($dados['idTipo']);
            return $this->db->update($this->table, $dados);
        }

        // Define a ordem para o novo item
        if (!isset($dados['ordem'])) {
            $this->db->select_max('ordem', 'max_ordem');
            $result = $this->db->get($this->table)->row();
            $dados['ordem'] = ($result->max_ordem ?? 0) + 1;
        }

        return $this->db->insert($this->table, $dados);
    }

    /**
     * Ativa/desativa um tipo
     */
    public function toggleAtivo($id)
    {
        $tipo = $this->getById($id);
        if (!$tipo) {
            return false;
        }

        $this->db->where('idTipo', $id);
        return $this->db->update($this->table, [
            'ativo' => $tipo->ativo ? 0 : 1
        ]);
    }

    /**
     * Exclui um tipo (apenas se não for padrão)
     */
    public function excluir($id)
    {
        $tipo = $this->getById($id);

        // Não permite excluir tipos padrão do sistema
        if ($tipo && $tipo->padrao == 1) {
            return false;
        }

        $this->db->where('idTipo', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Reordena os tipos
     */
    public function reordenar($ordens)
    {
        foreach ($ordens as $id => $ordem) {
            $this->db->where('idTipo', $id);
            $this->db->update($this->table, ['ordem' => $ordem]);
        }
        return true;
    }

    /**
     * Lista categorias disponíveis
     */
    public function getCategorias()
    {
        return [
            'rede' => [
                'nome' => 'Rede Estruturada',
                'icone' => 'bx-network-chart',
                'cor' => '#007bff',
            ],
            'cftv' => [
                'nome' => 'CFTV IP',
                'icone' => 'bx-camera',
                'cor' => '#dc3545',
            ],
            'seguranca' => [
                'nome' => 'Sistemas de Segurança',
                'icone' => 'bx-shield-alt',
                'cor' => '#6f42c1',
            ],
            'infra' => [
                'nome' => 'Infraestrutura',
                'icone' => 'bx-hdd',
                'cor' => '#fd7e14',
            ],
            'internet' => [
                'nome' => 'Internet/Redes',
                'icone' => 'bx-wifi',
                'cor' => '#17a2b8',
            ],
            'geral' => [
                'nome' => 'Serviços Gerais',
                'icone' => 'bx-wrench',
                'cor' => '#6c757d',
            ],
        ];
    }

    /**
     * Lista tipos por categoria (agrupados)
     */
    public function listarPorCategoria($apenasAtivos = true)
    {
        $tipos = $this->listar([], $apenasAtivos);
        $categorias = $this->getCategorias();

        $resultado = [];
        foreach ($tipos as $tipo) {
            $cat = $tipo->categoria;
            if (!isset($resultado[$cat])) {
                $resultado[$cat] = [
                    'info' => $categorias[$cat] ?? [
                        'nome' => ucfirst($cat),
                        'icone' => 'bx-wrench',
                        'cor' => '#6c757d',
                    ],
                    'tipos' => [],
                ];
            }
            $resultado[$cat]['tipos'][] = $tipo;
        }

        return $resultado;
    }

    /**
     * Busca tipos por nome (para autocomplete)
     */
    public function buscar($termo, $limite = 10)
    {
        $this->db->like('nome', $termo, 'both');
        $this->db->where('ativo', 1);
        $this->db->limit($limite);
        return $this->db->get($this->table)->result();
    }

    /**
     * Obtém tipos recomendados para um tipo de OS
     */
    public function getRecomendadosPorOS($os_descricao)
    {
        // Mapeamento simples baseado em palavras-chave
        $keywords = [
            'cftv' => 'cftv',
            'camera' => 'cftv',
            'dvr' => 'cftv',
            'nvr' => 'cftv',
            'rede' => 'rede',
            'cabo' => 'rede',
            'switch' => 'rede',
            'internet' => 'internet',
            'wifi' => 'internet',
            'roteador' => 'internet',
            'alarme' => 'seguranca',
            'sensor' => 'seguranca',
            'fechadura' => 'seguranca',
        ];

        $categoria = 'geral';
        $descricao_lower = strtolower($os_descricao);

        foreach ($keywords as $keyword => $cat) {
            if (strpos($descricao_lower, $keyword) !== false) {
                $categoria = $cat;
                break;
            }
        }

        // Retorna os 5 primeiros tipos dessa categoria
        $this->db->where('categoria', $categoria);
        $this->db->where('ativo', 1);
        $this->db->order_by('ordem', 'ASC');
        $this->db->limit(5);

        return $this->db->get($this->table)->result();
    }
}
