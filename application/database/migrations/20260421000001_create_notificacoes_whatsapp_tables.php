<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_notificacoes_whatsapp_tables extends CI_Migration
{
    public function up()
    {
        // Tabela de configurações de notificações
        $this->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_config` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `whatsapp_provedor` ENUM('evolution', 'meta_api', 'z_api', 'desativado') DEFAULT 'desativado',
            `whatsapp_ativo` TINYINT(1) DEFAULT 0,
            `evolution_url` VARCHAR(255) DEFAULT NULL,
            `evolution_url_interna` VARCHAR(255) DEFAULT NULL,
            `evolution_apikey` VARCHAR(255) DEFAULT NULL,
            `evolution_instance` VARCHAR(100) DEFAULT 'mapos',
            `evolution_version` ENUM('v1','v2','go') DEFAULT 'v2',
            `evolution_estado` VARCHAR(50) DEFAULT 'desconectado',
            `meta_phone_number_id` VARCHAR(50) DEFAULT NULL,
            `meta_access_token` TEXT DEFAULT NULL,
            `meta_webhook_verify_token` VARCHAR(100) DEFAULT NULL,
            `z_api_url` VARCHAR(255) DEFAULT NULL,
            `z_api_token` VARCHAR(255) DEFAULT NULL,
            `notificacao_os_criada` TINYINT(1) DEFAULT 1,
            `notificacao_os_atualizada` TINYINT(1) DEFAULT 1,
            `notificacao_os_pronta` TINYINT(1) DEFAULT 1,
            `notificacao_os_orcamento` TINYINT(1) DEFAULT 1,
            `notificacao_venda_realizada` TINYINT(1) DEFAULT 1,
            `notificacao_cobranca_gerada` TINYINT(1) DEFAULT 1,
            `notificacao_cobranca_vencimento` TINYINT(1) DEFAULT 1,
            `notificacao_lembrete_aniversario` TINYINT(1) DEFAULT 0,
            `horario_envio_inicio` TIME DEFAULT '08:00:00',
            `horario_envio_fim` TIME DEFAULT '18:00:00',
            `enviar_fim_semana` TINYINT(1) DEFAULT 0,
            `respeitar_horario` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Inserir configuração padrão
        $this->db->query("INSERT INTO `notificacoes_config` (`id`, `whatsapp_provedor`, `whatsapp_ativo`)
            VALUES (1, 'desativado', 0)
            ON DUPLICATE KEY UPDATE id=id");

        // Tabela de templates de mensagens
        $this->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_templates` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `chave` VARCHAR(50) NOT NULL UNIQUE,
            `nome` VARCHAR(100) NOT NULL,
            `descricao` TEXT DEFAULT NULL,
            `categoria` ENUM('os', 'venda', 'cobranca', 'marketing', 'sistema') DEFAULT 'os',
            `canal` ENUM('whatsapp', 'email', 'sms', 'todos') DEFAULT 'whatsapp',
            `assunto` VARCHAR(255) DEFAULT NULL,
            `mensagem` TEXT NOT NULL,
            `variaveis` TEXT DEFAULT NULL,
            `ativo` TINYINT(1) DEFAULT 1,
            `e_marketing` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_chave` (`chave`),
            INDEX `idx_categoria` (`categoria`),
            INDEX `idx_ativo` (`ativo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de log de notificações
        $this->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_log` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cliente_id` INT(11) DEFAULT NULL,
            `telefone` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(255) DEFAULT NULL,
            `template_chave` VARCHAR(50) DEFAULT NULL,
            `assunto` VARCHAR(255) DEFAULT NULL,
            `mensagem` TEXT DEFAULT NULL,
            `mensagem_processada` TEXT DEFAULT NULL,
            `status` ENUM('pendente', 'enviando', 'enviado', 'entregue', 'falha', 'lido', 'cancelado') DEFAULT 'pendente',
            `canal` ENUM('whatsapp', 'email', 'sms') DEFAULT 'whatsapp',
            `provedor` VARCHAR(50) DEFAULT NULL,
            `external_id` VARCHAR(255) DEFAULT NULL,
            `resposta_api` TEXT DEFAULT NULL,
            `erro` TEXT DEFAULT NULL,
            `os_id` INT(11) DEFAULT NULL,
            `venda_id` INT(11) DEFAULT NULL,
            `tentativas` INT(3) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `sent_at` TIMESTAMP NULL,
            `delivered_at` TIMESTAMP NULL,
            `read_at` TIMESTAMP NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_status` (`status`),
            INDEX `idx_cliente` (`cliente_id`),
            INDEX `idx_os` (`os_id`),
            INDEX `idx_venda` (`venda_id`),
            INDEX `idx_created` (`created_at`),
            INDEX `idx_canal_status` (`canal`, `status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de notificações agendadas
        $this->db->query("CREATE TABLE IF NOT EXISTS `notificacoes_agendadas` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `data_hora_envio` DATETIME NOT NULL,
            `data_hora_enviado` DATETIME DEFAULT NULL,
            `cliente_id` INT(11) DEFAULT NULL,
            `telefone` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(255) DEFAULT NULL,
            `template_chave` VARCHAR(50) DEFAULT NULL,
            `variaveis` TEXT DEFAULT NULL,
            `status` ENUM('agendada', 'enviada', 'cancelada', 'falha') DEFAULT 'agendada',
            `os_id` INT(11) DEFAULT NULL,
            `venda_id` INT(11) DEFAULT NULL,
            `origem` VARCHAR(50) DEFAULT 'sistema',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_data_envio` (`data_hora_envio`),
            INDEX `idx_status` (`status`),
            INDEX `idx_cliente` (`cliente_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Tabela de consentimento de clientes (LGPD)
        $this->db->query("CREATE TABLE IF NOT EXISTS `clientes_notificacoes_consent` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cliente_id` INT(11) NOT NULL,
            `whatsapp` TINYINT(1) DEFAULT 1,
            `email` TINYINT(1) DEFAULT 1,
            `sms` TINYINT(1) DEFAULT 0,
            `marketing` TINYINT(1) DEFAULT 0,
            `data_consentimento` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `ip_consentimento` VARCHAR(45) DEFAULT NULL,
            `user_agent` TEXT DEFAULT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_cliente` (`cliente_id`),
            INDEX `idx_whatsapp` (`whatsapp`),
            INDEX `idx_marketing` (`marketing`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Inserir templates padrão
        $this->inserirTemplatesPadrao();

        // Adicionar coluna celular_verificado à tabela clientes se não existir
        if (!$this->db->field_exists('celular_verificado', 'clientes')) {
            $this->db->query("ALTER TABLE `clientes` ADD COLUMN `celular_verificado` TINYINT(1) DEFAULT 0 AFTER `celular`");
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `notificacoes_agendadas`");
        $this->db->query("DROP TABLE IF EXISTS `notificacoes_log`");
        $this->db->query("DROP TABLE IF EXISTS `notificacoes_templates`");
        $this->db->query("DROP TABLE IF EXISTS `notificacoes_config`");
        $this->db->query("DROP TABLE IF EXISTS `clientes_notificacoes_consent`");

        if ($this->db->field_exists('celular_verificado', 'clientes')) {
            $this->db->query("ALTER TABLE `clientes` DROP COLUMN `celular_verificado`");
        }
    }

    private function inserirTemplatesPadrao()
    {
        $templates = [
            [
                'chave' => 'os_criada',
                'nome' => 'OS Criada',
                'descricao' => 'Notificação enviada quando uma nova OS é criada',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 👋\n\nSua Ordem de Serviço #{os_id} foi registrada em nosso sistema.\n\n📋 *Equipamento:* {equipamento}\n📝 *Defeito:* {defeito}\n📅 *Previsão:* {data_previsao}\n\nAcompanhe o status pelo link:\n{link_consulta}\n\nObrigado pela preferência! 🤝",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'equipamento' => 'Descrição do equipamento',
                    'defeito' => 'Defeito informado',
                    'data_previsao' => 'Data prevista para conclusão',
                    'link_consulta' => 'Link para consulta pública da OS',
                    'emitente_nome' => 'Nome da empresa emitente',
                    'emitente_telefone' => 'Telefone da empresa'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'os_atualizada',
                'nome' => 'OS Atualizada',
                'descricao' => 'Notificação enviada quando o status da OS é alterado',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 📋\n\nHouve uma atualização na sua OS #{os_id}.\n\n📊 *Status:* {status_atual}\n📋 {equipamento}\n\nPara mais informações, entre em contato conosco.\n\n{emitente_nome}\n📞 {emitente_telefone}",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'status_atual' => 'Novo status da OS',
                    'status_anterior' => 'Status anterior',
                    'equipamento' => 'Descrição do equipamento',
                    'emitente_nome' => 'Nome da empresa',
                    'emitente_telefone' => 'Telefone da empresa'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'os_pronta',
                'nome' => 'OS Pronta',
                'descricao' => 'Notificação enviada quando a OS é finalizada',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 🎉\n\nSua Ordem de Serviço #{os_id} está *PRONTA*! ✅\n\n📋 {equipamento}\n💰 *Valor:* R$ {valor_total}\n\n📍 *Retirada em:*\n{emitente_endereco}\n⏰ *Funcionamento:* {emitente_horario}\n\nDúvidas? Responda aqui ou ligue {emitente_telefone}",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'equipamento' => 'Descrição do equipamento',
                    'valor_total' => 'Valor total da OS',
                    'emitente_nome' => 'Nome da empresa',
                    'emitente_endereco' => 'Endereço da empresa',
                    'emitente_horario' => 'Horário de funcionamento',
                    'emitente_telefone' => 'Telefone da empresa'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'os_orcamento',
                'nome' => 'Orçamento Disponível',
                'descricao' => 'Notificação enviada quando um orçamento é gerado',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 📋\n\nSeu orçamento para OS #{os_id} está pronto!\n\n*Equipamento:* {equipamento}\n*Valor:* R$ {valor_orcamento}\n*Tempo estimado:* {tempo_estimado}\n\n✅ *Aprovar:* {link_aprovar}\n❌ *Recusar:* {link_recusar}\n\nAguardamos sua resposta! 👍",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'equipamento' => 'Descrição do equipamento',
                    'valor_orcamento' => 'Valor do orçamento',
                    'tempo_estimado' => 'Tempo estimado para execução',
                    'link_aprovar' => 'Link para aprovar orçamento',
                    'link_recusar' => 'Link para recusar orçamento'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'os_aguardando_peca',
                'nome' => 'OS Aguardando Peça',
                'descricao' => 'Notificação enviada quando a OS entra em espera por peça',
                'categoria' => 'os',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! ⏳\n\nSua OS #{os_id} está aguardando peça(s) para prosseguir com o serviço.\n\n📋 {equipamento}\n🔧 *Peça(s):* {pecas_aguardando}\n📅 *Previsão de chegada:* {previsao_peca}\n\nAssim que a peça chegar, iniciaremos o serviço. Obrigado pela paciência! 🙏",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'os_id' => 'Número da OS',
                    'equipamento' => 'Descrição do equipamento',
                    'pecas_aguardando' => 'Lista de peças aguardando',
                    'previsao_peca' => 'Previsão de chegada das peças'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'venda_realizada',
                'nome' => 'Venda Realizada',
                'descricao' => 'Confirmação de venda para o cliente',
                'categoria' => 'venda',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 🛒\n\nSua compra foi registrada com sucesso!\n\n*Venda #{venda_id}*\n💰 *Valor:* R$ {valor_total}\n📅 *Data:* {data_venda}\n\nAgradecemos sua preferência! 💙",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'venda_id' => 'Número da venda',
                    'valor_total' => 'Valor total da venda',
                    'data_venda' => 'Data da venda'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'cobranca_gerada',
                'nome' => 'Cobrança Gerada',
                'descricao' => 'Notificação de cobrança/boleto gerado',
                'categoria' => 'cobranca',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! 💳\n\nSua cobrança foi gerada:\n\n*Referente a:* {referente}\n💰 *Valor:* R$ {valor}\n📅 *Vencimento:* {data_vencimento}\n\n💳 *Pagar agora:* {link_pagamento}\n\nApós o pagamento, envie o comprovante aqui! ✅",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'referente' => 'Referência da cobrança (OS/Venda)',
                    'valor' => 'Valor da cobrança',
                    'data_vencimento' => 'Data de vencimento',
                    'link_pagamento' => 'Link para pagamento'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'cobranca_vencimento',
                'nome' => 'Lembrete de Vencimento',
                'descricao' => 'Lembrete de vencimento próximo',
                'categoria' => 'cobranca',
                'canal' => 'whatsapp',
                'mensagem' => "Olá {cliente_nome}! ⏰\n\nLembrete: sua cobrança vence em {dias} dia(s)!\n\n*Valor:* R$ {valor}\n📅 *Vencimento:* {data_vencimento}\n\n💳 *Pagar:* {link_pagamento}\n\nEvite multas e juros! 🙏",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'dias' => 'Dias até o vencimento',
                    'valor' => 'Valor da cobrança',
                    'data_vencimento' => 'Data de vencimento',
                    'link_pagamento' => 'Link para pagamento'
                ]),
                'ativo' => 1,
                'e_marketing' => 0
            ],
            [
                'chave' => 'aniversario',
                'nome' => 'Aniversário',
                'descricao' => 'Mensagem de aniversário',
                'categoria' => 'marketing',
                'canal' => 'whatsapp',
                'mensagem' => "🎂 *Feliz Aniversário, {cliente_nome}!* 🎉\n\nDesejamos um dia incrível cheio de conquistas!\n\n🎁 *Presente:* {cupom_desconto}\nVálido por 7 dias!\n\nObrigado por fazer parte da nossa história! 💙",
                'variaveis' => json_encode([
                    'cliente_nome' => 'Nome do cliente',
                    'cupom_desconto' => 'Cupom de desconto oferecido'
                ]),
                'ativo' => 0,
                'e_marketing' => 1
            ]
        ];

        foreach ($templates as $template) {
            $this->db->replace('notificacoes_templates', $template);
        }
    }
}
