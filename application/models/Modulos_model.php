<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model para gerenciamento de Módulos
 */
class Modulos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna lista de todos os módulos
     */
    public function getModulos()
    {
        return [];
    }

    /**
     * Retorna timeline de desenvolvimento
     */
    public function getTimeline()
    {
        return [];
    }

    /**
     * Retorna estatísticas dos módulos
     */
    public function getStats()
    {
        return [
            'concluidos' => 0,
            'em_progresso' => 0,
            'planejados' => 0,
        ];
    }
}
