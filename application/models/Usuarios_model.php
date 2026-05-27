<?php

class Usuarios_model extends MY_Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idUsuarios';
    protected $fillable = [
        'nome', 'rg', 'cpf', 'cep', 'rua', 'numero', 'bairro',
        'cidade', 'estado', 'email', 'telefone', 'celular', 'senha',
        'situacao', 'permissoes_id', 'dataExpiracao', 'url_image_user',
        'is_tecnico', 'nivel_tecnico', 'app_tecnico_instalado',
        'coordenadas_base_lat', 'coordenadas_base_lng', 'raio_atuacao_km',
        'foto_tecnico'
    ];
    protected $softDelete = true;

    private $mainTable = 'usuarios';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($perpage = 0, $start = 0, $one = false)
    {
        $this->db->from('usuarios');
        $this->db->select('usuarios.idUsuarios, usuarios.nome, usuarios.rg, usuarios.cpf, usuarios.cep, usuarios.rua, usuarios.numero, usuarios.bairro, usuarios.cidade, usuarios.estado, usuarios.email, usuarios.telefone, usuarios.celular, usuarios.situacao, usuarios.dataCadastro, usuarios.permissoes_id, usuarios.dataExpiracao, usuarios.url_image_user, usuarios.is_tecnico, permissoes.nome as permissao');
        $this->db->limit($perpage, $start);
        $this->db->join('permissoes', 'usuarios.permissoes_id = permissoes.idPermissao', 'left');

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getAllTipos()
    {
        $this->db->where('situacao', 1);

        return $this->db->get('tiposUsuario')->result();
    }

    private $safeColumns = 'usuarios.idUsuarios, usuarios.nome, usuarios.rg, usuarios.cpf, usuarios.cep, usuarios.rua, usuarios.numero, usuarios.bairro, usuarios.cidade, usuarios.estado, usuarios.email, usuarios.telefone, usuarios.celular, usuarios.situacao, usuarios.dataCadastro, usuarios.permissoes_id, usuarios.dataExpiracao, usuarios.url_image_user, usuarios.is_tecnico';

    public function getById($id)
    {
        $this->db->select($this->safeColumns);
        $this->db->where('idUsuarios', $id);
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getByIdWithSenha($id)
    {
        $this->db->where('idUsuarios', $id);
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getByEmail($email)
    {
        $this->db->select($this->safeColumns . ', permissoes.nome as permissao');
        $this->db->from('usuarios');
        $this->db->join('permissoes', 'usuarios.permissoes_id = permissoes.idPermissao', 'left');
        $this->db->where('usuarios.email', $email);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByEmailWithSenha($email)
    {
        $this->db->where('email', $email);
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getAll()
    {
        $this->db->select($this->safeColumns . ', permissoes.nome as permissao');
        $this->db->from('usuarios');
        $this->db->join('permissoes', 'usuarios.permissoes_id = permissoes.idPermissao', 'left');

        return $this->db->get()->result();
    }

    public function add($table, $data)
    {
        if ($table === 'usuarios') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() >= 1) {
            if ($table === $this->mainTable) {
                log_audit('INSERT', 'usuarios', $this->db->insert_id(), null, $data);
            }
            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        if ($table === 'usuarios') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        if ($table === $this->mainTable) {
            $oldData = (array) $this->db->where($fieldID, $ID)->get($this->mainTable)->row();
        }
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            if ($table === $this->mainTable) {
                log_audit('UPDATE', 'usuarios', $ID, $oldData ?? null, $data);
            }
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        if ($table === $this->mainTable && $this->db->field_exists('deleted_at', $this->mainTable)) {
            // Soft delete for usuarios
            $this->db->where($fieldID, $ID);
            $this->db->update($this->mainTable, ['deleted_at' => date('Y-m-d H:i:s')]);
            if ($this->db->affected_rows() >= 0) {
                log_audit('DELETE', 'usuarios', $ID);
                return true;
            }
            return false;
        }

        // Hard delete for other tables
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() >= 1) {
            return true;
        }

        return false;
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        $this->db->where($this->primaryKey, $id);
        $this->db->update($this->mainTable, ['deleted_at' => null]);
        if ($this->db->affected_rows() >= 0) {
            log_audit('RESTORE', 'usuarios', $id);
            return true;
        }
        return false;
    }

    /**
     * Permanently delete a user (administrative only)
     */
    public function forceDelete($id)
    {
        $this->db->where($this->primaryKey, $id);
        $this->db->delete($this->mainTable);
        return $this->db->affected_rows() >= 1;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}
