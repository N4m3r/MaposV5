<?php

/**
 * Migration: Criar tabela whatsapp_log_interacoes e cleanup de PDFs
 * Garante que a tabela de logs do agente WhatsApp exista e
 * adiciona procedimento de limpeza de PDFs temporarios.
 */

class Migration_20260521000001_create_whatsapp_log_and_cleanup extends CI_Migration {

    public function up()
    {
        // ===== Tabela: whatsapp_log_interacoes =====
        // Usada pelo agente Python para registrar todas as interacoes
        if (!$this->db->table_exists('whatsapp_log_interacoes')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ],
                'numero_telefone' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'tipo_mensagem' => [
                    'type' => 'ENUM',
                    'constraint' => "'texto','audio','documento','imagem'",
                    'default' => 'texto',
                ],
                'direcao' => [
                    'type' => 'ENUM',
                    'constraint' => "'entrada','saida'",
                    'null' => FALSE,
                ],
                'conteudo' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'intencao_detectada' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE,
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'recebido',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'default' => 'CURRENT_TIMESTAMP',
                ],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('whatsapp_log_interacoes');

            // Indices
            $this->db->query('ALTER TABLE whatsapp_log_interacoes ADD INDEX idx_numero (numero_telefone)');
            $this->db->query('ALTER TABLE whatsapp_log_interacoes ADD INDEX idx_intencao (intencao_detectada)');
            $this->db->query('ALTER TABLE whatsapp_log_interacoes ADD INDEX idx_created (created_at)');
        }

        // ===== Diretorio: assets/relatorios_temp =====
        $dir = FCPATH . 'assets/relatorios_temp';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, TRUE);
            // Criar .htaccess para proteger o diretorio
            file_put_contents($dir . '/.htaccess', "Options -Indexes\nAllow from all\n");
        }

        // ===== Tabela: agente_ia_notificacoes_agendadas =====
        // Para agendar relatorios e notificacoes
        if (!$this->db->table_exists('agente_ia_notificacoes_agendadas')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ],
                'numero_telefone' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'tipo_notificacao' => [
                    'type' => 'ENUM',
                    'constraint' => "'relatorio_diario','os_vencendo','os_atrasada','pesquisa_satisfacao'",
                    'null' => FALSE,
                ],
                'horario' => [
                    'type' => 'TIME',
                    'null' => TRUE,
                ],
                'dias_semana' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => '1,2,3,4,5',
                    'comment' => 'Dias da semana (1=Seg, 7=Dom)',
                ],
                'situacao' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                ],
                'ultimo_envio' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'default' => 'CURRENT_TIMESTAMP',
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                ],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('agente_ia_notificacoes_agendadas');

            $this->db->query('ALTER TABLE agente_ia_notificacoes_agendadas ADD INDEX idx_numero (numero_telefone)');
            $this->db->query('ALTER TABLE agente_ia_notificacoes_agendadas ADD INDEX idx_tipo (tipo_notificacao)');
        }

        echo "Migration 20260521000001: Tabelas criadas com sucesso.\n";
    }

    public function down()
    {
        $this->dbforge->drop_table('whatsapp_log_interacoes', TRUE);
        $this->dbforge->drop_table('agente_ia_notificacoes_agendadas', TRUE);
        echo "Migration 20260521000001: Tabelas removidas.\n";
    }
}