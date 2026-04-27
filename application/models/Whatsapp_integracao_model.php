<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model para vinculo de numeros WhatsApp com clientes/usuarios
 */
class Whatsapp_integracao_model extends CI_Model
{
    protected $table = 'whatsapp_integracao';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Busca vinculo por numero de telefone
     */
    public function buscarPorNumero($numero)
    {
        $numero = $this->limparNumero($numero);
        return $this->db->where('numero_telefone', $numero)
            ->where('situacao', 1)
            ->get($this->table)
            ->row();
    }

    /**
     * Vincula numero a um cliente
     */
    public function vincularCliente($cliente_id, $numero)
    {
        $numero = $this->limparNumero($numero);
        if (empty($numero)) {
            return false;
        }

        $existe = $this->buscarPorNumero($numero);

        $dados = [
            'numero_telefone' => $numero,
            'tipo_vinculo' => 'cliente',
            'clientes_id' => $cliente_id,
            'usuarios_id' => null,
            'situacao' => 1,
        ];

        if ($existe) {
            $this->db->where('id', $existe->id);
            return $this->db->update($this->table, $dados);
        }

        return $this->db->insert($this->table, $dados);
    }

    /**
     * Vincula numero a um usuario (tecnico/admin)
     */
    public function vincularUsuario($usuario_id, $numero, $tipo = 'tecnico')
    {
        $numero = $this->limparNumero($numero);
        if (empty($numero)) {
            return false;
        }

        $existe = $this->buscarPorNumero($numero);

        $dados = [
            'numero_telefone' => $numero,
            'tipo_vinculo' => in_array($tipo, ['tecnico', 'admin']) ? $tipo : 'tecnico',
            'clientes_id' => null,
            'usuarios_id' => $usuario_id,
            'situacao' => 1,
        ];

        if ($existe) {
            $this->db->where('id', $existe->id);
            return $this->db->update($this->table, $dados);
        }

        return $this->db->insert($this->table, $dados);
    }

    /**
     * Lista todos os vinculos ativos
     */
    public function listar($limit = null, $offset = 0)
    {
        $this->db->select('w.*, c.nomeCliente as cliente_nome, u.nome as usuario_nome, p.nome as permissao_nome');
        $this->db->from($this->table . ' w');
        $this->db->join('clientes c', 'c.idClientes = w.clientes_id', 'left');
        $this->db->join('usuarios u', 'u.idUsuarios = w.usuarios_id', 'left');
        $this->db->join('permissoes p', 'p.idPermissao = u.permissoes_id', 'left');
        $this->db->where('w.situacao', 1);
        $this->db->order_by('w.updated_at', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Conta total de vinculos ativos
     */
    public function contar()
    {
        return $this->db->where('situacao', 1)->count_all_results($this->table);
    }

    /**
     * Desvincula um numero
     */
    public function desvincular($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['situacao' => 0, 'tipo_vinculo' => 'desconhecido']);
    }

    /**
     * Atualiza ultima interacao
     */
    public function atualizarInteracao($numero)
    {
        $numero = $this->limparNumero($numero);
        $this->db->where('numero_telefone', $numero);
        return $this->db->update($this->table, ['ultima_interacao' => date('Y-m-d H:i:s')]);
    }

    /**
     * Migra vinculos automaticos a partir de clientes.celular e usuarios.celular
     */
    public function migrarVinculosAutomaticos()
    {
        // Migrar clientes
        $clientes = $this->db->select('idClientes, celular')
            ->from('clientes')
            ->where('celular IS NOT NULL')
            ->where('celular !=', '')
            ->get()
            ->result();

        $count = 0;
        foreach ($clientes as $c) {
            if ($this->vincularCliente($c->idClientes, $c->celular)) {
                $count++;
            }
        }

        // Migrar usuarios (tecnicos e admins)
        $usuarios = $this->db->select('u.idUsuarios, u.celular, p.nome as permissao_nome')
            ->from('usuarios u')
            ->join('permissoes p', 'p.idPermissao = u.permissoes_id')
            ->where('u.celular IS NOT NULL')
            ->where('u.celular !=', '')
            ->get()
            ->result();

        foreach ($usuarios as $u) {
            $tipo = (strpos(strtolower($u->permissao_nome), 'admin') !== false) ? 'admin' : 'tecnico';
            if ($this->vincularUsuario($u->idUsuarios, $u->celular, $tipo)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Limpa numero: remove tudo exceto digitos
     */
    private function limparNumero($numero)
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        // Se tiver 11 ou 10 digitos (sem DDI), adiciona 55
        if (strlen($numero) == 11 || strlen($numero) == 10) {
            $numero = '55' . $numero;
        }
        return $numero;
    }
}
