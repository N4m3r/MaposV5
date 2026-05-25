<?php

class Usuarios_model extends CI_Model
{
    protected $fillable = [
        'nome', 'rg', 'cpf', 'cep', 'rua', 'numero', 'bairro',
        'cidade', 'estado', 'email', 'telefone', 'celular', 'senha',
        'situacao', 'permissoes_id', 'dataExpiracao', 'url_image_user',
        'is_tecnico', 'nivel_tecnico', 'app_tecnico_instalado',
        'coordenadas_base_lat', 'coordenadas_base_lng', 'raio_atuacao_km',
        'foto_tecnico'
    ];

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

    public function getById($id)
    {
        $this->db->where('idUsuarios', $id);
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getByEmail($email)
    {
        $this->db->where('email', $email);
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getAll()
    {
        return $this->db->get('usuarios')->result();
    }

    public function add($table, $data)
    {
        if ($table === 'usuarios') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() >= 1) {
            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        if ($table === 'usuarios') {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() >= 1) {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}
