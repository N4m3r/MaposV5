<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Templates de Notificação para Obras
 * Insere templates padrão na tabela notificacoes_templates para o módulo de obras.
 */
class Migration_Notificacoes_obras_templates extends CI_Migration
{
    public function up()
    {
        // Garantir que a tabela de templates existe
        if (!$this->db->table_exists('notificacoes_templates')) {
            log_message('error', 'Tabela notificacoes_templates nao existe. Execute a migration de notificacoes primeiro.');
            return;
        }

        $templates = [
            [
                'chave' => 'obra_atividade_finalizada',
                'nome' => 'Obra - Atividade Finalizada',
                'descricao' => 'Notificacao enviada quando uma atividade de obra e finalizada pelo tecnico',
                'categoria' => 'obra',
                'canal' => 'whatsapp',
                'mensagem' => "Ola {cliente_nome}! 👷‍♂️\n\nUma atividade da sua obra *{obra_nome}* foi concluida.\n\n📋 *Atividade:* {atividade_titulo}\n🏗️ *Etapa:* {etapa_nome}\n👤 *Tecnico:* {tecnico_nome}\n📅 *Data:* {data_hora}\n📊 *Progresso:* {percentual}%\n\nAcompanhe sua obra pelo link:\n{link_obra}\n\nObrigado pela preferencia! 🤝",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'obra_nome' => 'Nome da obra',
                    'etapa_nome' => 'Nome da etapa',
                    'atividade_titulo' => 'Titulo da atividade',
                    'tecnico_nome' => 'Nome do tecnico',
                    'data_hora' => 'Data e hora da finalizacao',
                    'percentual' => 'Percentual de conclusao da atividade',
                    'link_obra' => 'Link para acompanhar a obra'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'obra_etapa_concluida',
                'nome' => 'Obra - Etapa Concluida',
                'descricao' => 'Notificacao enviada quando uma etapa da obra atinge 100% de conclusao',
                'categoria' => 'obra',
                'canal' => 'whatsapp',
                'mensagem' => "Ola {cliente_nome}! 🎉\n\nUma etapa da sua obra *{obra_nome}* foi concluida com sucesso!\n\n🏗️ *Etapa:* {etapa_nome}\n🔢 *Numero:* {etapa_numero}\n📅 *Data:* {data_hora}\n\nAcompanhe o andamento completo pelo link:\n{link_obra}\n\nObrigado pela preferencia! 🤝",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'obra_nome' => 'Nome da obra',
                    'etapa_nome' => 'Nome da etapa',
                    'etapa_numero' => 'Numero da etapa',
                    'data_hora' => 'Data e hora da conclusao',
                    'link_obra' => 'Link para acompanhar a obra'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'obra_concluida',
                'nome' => 'Obra - Concluida',
                'descricao' => 'Notificacao enviada quando toda a obra e concluida',
                'categoria' => 'obra',
                'canal' => 'whatsapp',
                'mensagem' => "Ola {cliente_nome}! 🎊\n\nSua obra *{obra_nome}* foi concluida com sucesso!\n\n📅 *Data de conclusao:* {data_conclusao}\n⏱️ *Total de horas:* {total_horas}h\n\nAgradecemos a confianca em nosso trabalho!\n\nPara mais informacoes, acesse:\n{link_obra}\n\nObrigado pela preferencia! 🤝",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'obra_nome' => 'Nome da obra',
                    'data_conclusao' => 'Data de conclusao da obra',
                    'total_horas' => 'Total de horas trabalhadas na obra',
                    'link_obra' => 'Link para acompanhar a obra'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'obra_impedimento',
                'nome' => 'Obra - Impedimento Registrado',
                'descricao' => 'Notificacao enviada quando um tecnico registra um impedimento em uma atividade',
                'categoria' => 'obra',
                'canal' => 'whatsapp',
                'mensagem' => "Ola {cliente_nome}! ⚠️\n\nFoi registrado um impedimento na obra *{obra_nome}*.\n\n📋 *Atividade:* {atividade_titulo}\n🏗️ *Etapa:* {etapa_nome}\n👤 *Tecnico:* {tecnico_nome}\n📅 *Data:* {data_hora}\n\n*Tipo:* {tipo_impedimento}\n*Descricao:* {descricao_impedimento}\n\nNossa equipe ja esta trabalhando para resolver. Acompanhe pelo link:\n{link_obra}\n\nObrigado pela compreensao! 🤝",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'obra_nome' => 'Nome da obra',
                    'etapa_nome' => 'Nome da etapa',
                    'atividade_titulo' => 'Titulo da atividade',
                    'tecnico_nome' => 'Nome do tecnico',
                    'tipo_impedimento' => 'Tipo de impedimento',
                    'descricao_impedimento' => 'Descricao do impedimento',
                    'data_hora' => 'Data e hora do registro',
                    'link_obra' => 'Link para acompanhar a obra'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
        ];

        foreach ($templates as $t) {
            // Verificar se ja existe
            $existe = $this->db->where('chave', $t['chave'])->get('notificacoes_templates')->num_rows();
            if ($existe == 0) {
                $this->db->insert('notificacoes_templates', $t);
                log_message('info', 'Template de notificacao obra inserido: ' . $t['chave']);
            } else {
                // Atualizar se existir (garantir que esteja ativo e com mensagem atualizada)
                $this->db->where('chave', $t['chave']);
                $this->db->update('notificacoes_templates', [
                    'mensagem' => $t['mensagem'],
                    'variaveis' => $t['variaveis'],
                    'ativo' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                log_message('info', 'Template de notificacao obra atualizado: ' . $t['chave']);
            }
        }

        log_message('info', 'Migration notificacoes_obras_templates executada com sucesso.');
    }

    public function down()
    {
        // Desativar os templates em vez de remover (para nao perder logs antigos)
        $chaves = ['obra_atividade_finalizada', 'obra_etapa_concluida', 'obra_concluida', 'obra_impedimento'];
        $this->db->where_in('chave', $chaves);
        $this->db->update('notificacoes_templates', ['ativo' => 0]);
    }
}
